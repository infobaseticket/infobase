<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("../general_info/general_info_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['siteID']){
	$siteID=$_POST['siteID'];
	$query="SELECT
			wor.wor_id,
			sit.sit_udk,
			wor.wor_udk,
			substr(sit.SIT_UDK,2,2) AS REGION,
		  	wor.WOR_NET_CODE AS BTS_TYPE,
		  	wor.WOR_NOTES AS COMMENTS , 
			wot.wot_tos_tas_code,	 
			wot.wot_planned as PLANNED,  
			wot.wot_complete as COMPLETE,	  
			sit.SIT_NAME AS SITE_ADDRESS,
		  	woe.WOE_DOM_WES_CODE AS PREF_STATE,
		  	wor.WOR_DOM_WOS_CODE AS STATUS,		  	
		  	sittype.LDE_DESC AS SITE_TYPE,
		  	wod.DRE_V20_1 AS BAND,
		  	wod.DRE_V2_1 AS DRE_V2_1,
		  	wod.DRE_V20_1 AS DRE_V20_1,
		  	wor.WOR_NAME AS WOR_NAME,
		  	wor.WOR_LKP_WCO_CODE as WOR_LKP_WCO_CODE,
		  	wor.WOR_HSDPA_CLUSTER as WOR_HSDPA_CLUSTER         
		FROM   
		   works_order_tasks@NET1PROD wot,                         
			 works_order_elements@NET1PROD woe,     
			 works_orders@NET1PROD wor,                                  
			 sites@NET1PROD sit,
		 	 DYNAMIC_RECORDS@net1prod wod,
		 	 LOOKUP_DETAILS@net1prod  sittype,
		 	 PARTIES@net1prod  parties,
		  	 TRANSACTION_PARTIES@net1prod  trans
		WHERE  wot.wot_wor_id = wor.wor_id
			 AND woe.woe_wor_id = wor.wor_id 
			 AND woe.woe_sit_id = sit.sit_id 
			 AND   sit.sit_id = WOT_SIT_ID
			 AND wor.WOR_DRE_ID= wod.DRE_ID
		   	AND sit.SIT_LKP_STY_CODE=sittype.LDE_CODE
		    AND  ( wor.WOR_ID=trans.TXP_PRIMARY_KEY  )
  			AND  ( trans.TXP_PTY_ID=parties.PTY_ID  )
			AND sit.sit_udk  LIKE '%".$siteID."%'
			AND wot_tos_tas_code!='START'
			AND wot_tos_tas_code!='END'
		ORDER BY WOR_UDK";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_NET1=count($res1['SIT_UDK']);
	}

	for ($i=0;$i<$amount_of_NET1;$i++){
		if (substr($res1['WOR_UDK'][$i],0,2)=='99'){
			$type='UPG';
		}else{
			$type='NEW';
		}

		$net1[$type][$res1['WOR_UDK'][$i]]['site']=$res1['SIT_UDK'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['BTS_TYPE']=$res1['BTS_TYPE'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['SITE_ADDRESS']=$res1['SITE_ADDRESS'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['PREF_STATE']=$res1['PREF_STATE'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['SITE_TYPE']=$res1['SITE_TYPE'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['BAND']=$res1['BAND'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['DRE_V2_1']=$res1['DRE_V2_1'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['DRE_V20_1']=$res1['DRE_V20_1'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['STATUS']=$res1['STATUS'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['TECHNOS']=analyseTechno($res1['WOR_NAME'][$i]);
		$net1[$type][$res1['WOR_UDK'][$i]]['WOR_LKP_WCO_CODE']=$res1['WOR_LKP_WCO_CODE'][$i];
		$net1[$type][$res1['WOR_UDK'][$i]]['WOR_HSDPA_CLUSTER']=$res1['WOR_HSDPA_CLUSTER'][$i];

		$code=$res1['WOT_TOS_TAS_CODE'][$i];
		if ($res1['COMPLETE'][$i]!=''){	
			$net1[$type][$res1['WOR_UDK'][$i]]['codes'][$code]['CPL']=substr($res1['COMPLETE'][$i],0,10);//date('d-M-Y',strtotime(substr($res1['COMPLETE'][$i],0,10)))
		}
		if ($res1['PLANNED'][$i]!=''){	
			$net1[$type][$res1['WOR_UDK'][$i]]['codes'][$code]['PLA']=substr($res1['PLANNED'][$i],0,10);
		}

		//echo $res1['WOR_ID'][$i]."!=$lastWorid<br>";
		
		if ($res1['WOR_UDK'][$i]!=$lastWorid){
			$query="select PTY_UDK,TXP_LKP_PTP_CODE from transaction_parties@net1prod trans, parties@net1prod parties  WHERE
			  trans.TXP_PTY_ID=parties.PTY_ID 
			  AND  ( trans.TXP_ATB_TABLE='WORKS_ORDERS' OR trans.TXP_ATB_TABLE IS NULL  )
			AND TXP_LKP_PTP_CODE IN ('SAC','CON','ALSAC','ALCON')  AND txp_primary_key='".$res1['WOR_ID'][$i]."'";
			echo $query;
			/*$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
			if (!$stmt2) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt2);
				$amount_of_SAC=count($res2['PTY_UDK']);
				for ($i=0;$i<$amount_of_SAC;$i++){
					$net1[$type][$res1['WOR_UDK'][$i]][$res2['TXP_LKP_PTP_CODE'][$i]]=$res2['PTY_UDK'][$i];
				}
			}*/
		}
		$lastWorid=$res1['WOR_UDK'][$i];
	}

	foreach ($net1['UPG'] as $key => $value) {
		//echo "<pre>$key".print_r($value,true)."</pre>";
		if ($value['STATUS']=='IS'){
			$upgrade.="
			<tr class='info'>
				<td><a href='#' rel='tooltip' title='".$value['TECHNOS']."' data-toggle='modal' data-target='#NEW_".$res1['SIT_UDK'][$i]."'>".$key."<br>".$value['site']."</a></td>
				<td><a href='#' rel='tooltip' title='".$value['TECHNOS']."'>".$value['TECHNO'].$value['STATUS']."</a></td>
				<td><a href='#' rel='tooltip' title='RFINFO: ".$value['WOR_HSDPA_CLUSTER']."'>".$value['WOR_LKP_WCO_CODE']."</a></td>
				<td><a href='#' rel='tooltip' title='PO ACQ: ".$value['U501']." * ALSAC: ".$value['ALSAC']."'>".$value['SAC']."</a></td>";

			if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'SH')){
				$upgrade.="<td><a href='#' rel='tooltip' title='U100'>".$value['codes']['U100']['CPL']."</a></td>";
				$upgrade.="<td><a href='#' rel='tooltip' title='U104'>".$value['codes']['U104']['CPL']."</a></td>";
			}else{
				$upgrade.="<td><a href='#' rel='tooltip' title='U709'>".$value['codes']['U709']['CPL']."</a></td>";
				$upgrade.="<td><a href='#' rel='tooltip' title='U405'>".$value['codes']['U405']['CPL']."</a></td>";
			}
			$upgrade.="
			<td><a href='#' rel='tooltip' title='PO CON: ".$value['codes']['U503']['CPL']." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."</a></td>
			<td><a href='#' rel='popover' data-content='".$res1['U353_NOTES'][$i]."' data-original-title='Budget info'>".$value['codes']['U353']['CPL']."</a></td>
			<td>".$value['codes']['U459']['CPL']."</td>
			<td><a href='#' rel='popover' data-content='".$value['codes']['U571']['PLA']."' data-original-title='Integration forecast'>".$value['codes']['U571']['CPL']."</a></td>
			<td>".$value['codes']['U363']['CPL']."</td>
			<td>".$value['codes']['U391']['CPL']."</td>
			<td>".$value['codes']['U380']['CPL']."<br>".$value['codes']['U825']['CPL']."</td>
			<td><a href='#' rel='popover' data-placement='bottom' class='".$pakcheck."' data-content='".
			"A503: ". $res1['U503'][$i]."<br>U725: ".$res1['U725'][$i]."<br>U134: ".$res1['U134'][$i]."<br>U141: ".$res1['U141'][$i] 
			.'<br>U102: '.$res1['U102'][$i]."<br>U345: ".$res1['U345'][$i]."<br>U349: ".$res1['U349'][$i]."<br>U459: ".$res1['U459'][$i]."<br>U363: ".$res1['U363'][$i]
			.'<br>U110: '.$res1['U110'][$i]."<br>U364: ".$res1['U364'][$i]."<br>U365: ".$res1['U365'][$i]."<br>U388: ".$res1['U388'][$i]
			.'<br>U571: '.$res1['U571'][$i]."<br>U391: ".$res1['U391'][$i]."<br>U383: ".$res1['U383'][$i]."<br>U309: ".$res1['U392'][$i]
			.'<br>U392: '.$res1['U392'][$i]."<br>U380: ".$res1['U380'][$i]."<br>U712: ".$res1['U712'][$i]
			."' data-original-title='PAC CHECK'><span class='label ".$pakcheck."'>".$value['codes']['U418']['CPL']."</span></a></td>
			<td>".$value['codes']['U381']['CPL']."</td>
			</tr> ";
		}else{

		}
		
	}
	$upgrade.="<tr class='info'><td>-----</td></tr>";
	/*
	//echo "<pre>".print_r($net1,true)."</pre>";
	$query="SELECT * FROM  VW_NET1_ALL_NEWBUILDS  WHERE SIT_UDK LIKE '%".$siteID."%'  ORDER BY SIT_UDK ASC";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_NEW=count($res1['SIT_UDK']);
	}
/*
	for ($i=0;$i<$amount_of_NEW;$i++){
		if ($res1['A105'][$i]!="" && $res1['A28'][$i]!=""){
			$virtual='OK';
		}else{
			$virtual='NOT OK';
		}
		?>
		<div id='NEW_<?=$res1['SIT_UDK'][$i]?>' class='modal hide fade modalwide' role='dialog' aria-labelleby='myModelLabel' aria-hidden='true' style='display:none;'>
		 <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3 id="myModalLabel"><?=$res1['SIT_UDK'][$i]?></h3>
		  </div>
		  <div class="modal-body">
		    <div class="pull-left"  style="width:49%; border-right:1px solid;">
		    	<table class="table table-striped table-hover table-condensed">
		    	<caption>Acquisition</caption>
		    	<tbody>
					<tr>
					<td>A04</td><td class='parameter'>NB Issued</td><td><?=$res1['A04'][$i]?></td>
					</tr>
					<tr>
					<td>A501</td><td class='parameter'>PO Acquisition Sent</td><td><?=$res1['A501'][$i]?></td>
					</tr>
					<tr>
					<td>A15</td><td class='parameter'>M/S 1.2 - Best Candidate Selected</td><td><?=$res1['A15'][$i]?></td>
					</tr>
					<tr>
					<td>A26</td><td class='parameter'>	M/S 2.1 - Techincal Review Held</td><td><?=$res1['A26'][$i]?></td>
					</tr>
					<tr>
					<td>A28</td><td class='parameter'>Lease Sketch Approved</td><td><?=$res1['A28'][$i]?></td>
					</tr>
					<tr>
					<td>A34</td><td class='parameter'>M/S 3.1 - BP Application Submitted </td><td><?=$res1['A34'][$i]?></td>
					</tr>
					<tr>
					<td>A35</td><td class='parameter'>Acknowledgement from Region</td><td><?=$res1['A35'][$i]?></td>
					</tr>
					<tr>
					<td>A37</td><td class='parameter'>Authority Level 2: Commune</td><td><?=$res1['A37'][$i]?></td>
					</tr>
					<tr>
					<td>A100</td><td class='parameter'>Public Inquiry Start</td><td><?=$res1['A100'][$i]?></td>
					</tr>
					<tr>
					<td>A101</td><td class='parameter'>Public Inquiry End</td><td><?=$res1['A101'][$i]?></td>
					</tr>
					<tr>
					<td>A40</td><td class='parameter'>Authority Level 3: Region</td><td><?=$res1['A40'][$i]?></td>
					</tr>
					<tr>
					<td>A102</td><td class='parameter'>BP Refused</td><td><?=$res1['A102'][$i]?></td>
					</tr>
					<tr>
					<td>A103</td><td class='parameter'>Special BP</td><td><?=$res1['A103'][$i]?></td>
					</tr>
					<tr>
					<td>A104</td><td class='parameter'>Decree</td><td><?=$res1['A104'][$i]?></td>
					</tr>
					<tr>
					<td>A41</td><td class='parameter'>M/S 3.2 - BP Received</td><td><?=$res1['A41'][$i]?></td>
					</tr>
					<tr>
					<td>A105</td><td class='parameter'>BP OK To Build</td><td><?=$res1['A105'][$i]?></td>
					</tr>
					<tr>
					<td>Virtual</td><td class='parameter'>Virtual Lease & BP OK</td><td><=?$virtual?></td>
					</tr>
					<tr>
					<td>A352</td><td class='parameter'>M/S 5.0 - Acquisition Completed</td><td><?=$res1['A352'][$i]?></td>
					</tr>
					<tr>
					<td>A407</td><td class='parameter'>BP Expiry Date</td><td><?=$res1['A407'][$i]?></td>
					</tr>
					<tr>
					<td>A408</td><td class='parameter'>BP Extension Request Sent</td><td><?=$res1['A408'][$i]?></td>
					</tr>
					<tr>
					<td>A409</td><td class='parameter'>BP Extension Request Received</td><td><?=$res1['A409'][$i]?></td>
					</tr>
					<tr>
					<td>A710</td><td class='parameter'>Lease option expired</td><td><?=$res1['A710'][$i]?></td>
					</tr>
		    	</tbody>
		   	 	</table>
		    </div>
		    <div class="pull-right"  style="width:49%; border-left:1px solid;">
				<table class="table table-striped table-hover table-condensed">
		    	<caption>Construction</caption>
		    	<tbody>
					<tr>
						<td>A353</td><td class='parameter'>M/S 5.1 - Site Funded</td><td><?=$res1['A353'][$i]?></td>
					</tr>
					<tr>
						<td>A503</td><td class='parameter'>PO For Construction Sent</td><td><?=$res1['A503'][$i]?></td>
					</tr>
					<tr>
						<td>A134</td><td class='parameter'>Environmental Permit Submitted</td><td><?=$res1['A134'][$i]?></td>
					</tr>
					<tr>
						<td>A141</td><td class='parameter'>Environmental Permit Received</td><td><?=$res1['A141'][$i]?></td>
					</tr>
					<tr>
						<td>A305</td><td class='parameter'>BSDS Funded</td><td><?=$res1['A305'][$i]?></td>
					</tr>
					<tr>
						<td>A306</td><td class='parameter'>Material Ordering Process Initiated</td><td><?=$res1['A306'][$i]?></td>
					</tr>
					<tr>
						<td>A307</td><td class='parameter'>Materials Available</td><td><?=$res1['A307'][$i]?></td>
					</tr>
					<tr>
						<td>A308</td><td class='parameter'>Materials Received</td><td><?=$res1['A308'][$i]?></td>
					</tr>
					<tr>
						<td>A309</td><td class='parameter'>Materials Returned</td><td><?=$res1['A309'][$i]?></td>
					</tr>
					<tr>
						<td>A45</td><td class='parameter'>Construction kick-off meeting</td><td><?=$res1['A45'][$i]?></td>
					</tr>
					<tr>
						<td>A50</td><td class='parameter'>Detailed design / Construction drawings approved</td><td><?=$res1['A50'][$i]?></td>
					</tr>
					<tr>
						<td>A54</td><td class='parameter'>( NB Only )	Lease Activated</td><td><?=$res1['A54'][$i]?></td>
					</tr>
					<tr>
						<td>A59</td><td class='parameter'>M/S 5.2 Physical Start On Site</td><td><?=$res1['A59'][$i]?></td>
					</tr>
					<tr>
						<td>A680</td><td class='parameter'>Letter Start of Works Sent</td><td><?=$res1['A680'][$i]?></td>
					</tr>
					<tr>
						<td>A285</td><td class='parameter'>HSF Submitted</td><td><?=$res1['A285'][$i]?></td>
					</tr>
					<tr>
						<td>A288</td><td class='parameter'>HSF Completed</td><td><?=$res1['A288'][$i]?></td>
					</tr>
					<tr>
						<td>A361</td><td class='parameter'>CAB installed on site</td><td><?=$res1['A361'][$i]?></td>
					</tr>
					<tr>
						<td>A44</td><td class='parameter'>TX Build Pack Completed</td><td><?=$res1['A44'][$i]?></td>
					</tr>
					<tr>
						<td>A46</td><td class='parameter'>Transmission Capacity Implemented</td><td><?=$res1['A46'][$i]?></td>
					</tr>
					<tr>
						<td>A63</td><td class='parameter'>Civil Inspection Performed</td><td><?=$res1['A63'][$i]?></td>
					</tr>
					<tr>
						<td>A110</td><td class='parameter'>Only C punches CW</td><td><?=$res1['A110'][$i]?></td>
					</tr>
					<tr>
						<td>A64</td><td class='parameter'>Civil Inspection - All punches cleared</td><td><?=$res1['A64'][$i]?></td>
					</tr>
					<tr>
						<td>A65</td><td class='parameter'>( NB Only )	M/S 5.4 - Permanent Power Installation Completed</td><td><?=$res1['A65'][$i]?></td>
					</tr>
					<tr>
						<td>A71</td><td class='parameter'>M/S 6.5 - Site/UPG Integrated</td><td><?=$res1['A71'][$i]?></td>
					</tr>
					<tr>
						<td>A91</td><td class='parameter'>M/S 6.0 - Implementation Inspection Performed</td><td><?=$res1['A91'][$i]?></td>
					</tr>
					<tr>
						<td>A83</td><td class='parameter'>M/S 7.0 - Joint Inspection C Punches only</td><td><?=$res1['A83'][$i]?></td>
					</tr>
					<tr>
						<td>A92</td><td class='parameter'>M/S 6.1 - Implementation Inspection Completed</td><td><?=$res1['A92'][$i]?></td>
					</tr>
					<tr>
						<td>A712</td><td class='parameter'>Raf Pac Completed</td><td><?=$res1['A712'][$i]?></td>
					</tr>
					<tr>
						<td>A80</td><td class='parameter'>M/S 7.2 - SITE/UPG Debarred</td><td><?=$res1['A80'][$i]?></td>
					</tr>
					<tr>
						<td>A72</td><td class='parameter'>M/S 7.1 - PAC  Site Provisionally Accepted</td><td><?=$res1['A72'][$i]?></td>
					</tr>
					<tr>
						<td>A81</td><td class='parameter'>M/S 7.3 - FAC Final Acceptance</td><td><?=$res1['A81'][$i]?></td>
					</tr>
					<tr>
						<td>A200</td><td class='parameter'>M/S 8.0 - Cutover completed</td><td><?=$res1['A200'][$i]?></td>
					</tr>
					<tr>
						<td>A250</td><td class='parameter'>M/S 8.5 - Reconfiguration Completed</td><td><?=$res1['A250'][$i]?></td>
					</tr>
					<tr>
						<td>A270</td><td class='parameter'>Dismantling Performed</td><td><?=$res1['A270'][$i]?></td>
					</tr>
					<tr>
						<td>A275</td><td class='parameter'>M/S 8.6 - Dismantling Completed</td><td><?=$res1['A275'][$i]?></td>
					</tr>
		    	</tbody>
		   	 	</table>
		    </div>
		  </div>
		  <div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		  </div>
		</div>
		<?php
		
		if (trim($res1['WOE_RANK'][$i])==1 && trim($res1['WOR_DOM_WOS_CODE'][$i])=='IS'){
			if ($res1['A72'][$i]){
				$pac=$res1['A72'][$i];
			}else{
				$pac='X';
			}
			if($res1['A503'][$i]."<br>".$res1['A725'][$i]."<br>".$res1['A134'][$i]!='' && $res1['A141'][$i]!='' 
			&& $res1['A54'][$i]!='' && $res1['A45'][$i]!='' && $res1['A50'][$i]!='' && $res1['A59'][$i]!='' && $res1['A63'][$i]!=''
			&& $res1['A110'][$i]!='' && $res1['A64'][$i]!='' && $res1['A65'][$i]!='' && $res1['A285'][$i]!='' && $res1['A288'][$i]!=''
			&& $res1['A71'][$i]!='' && $res1['A91'][$i]!='' && $res1['A83'][$i]!='' && $res1['A309'][$i]!=''
			&& $res1['A92'][$i]!='' && $res1['A80'][$i]!='' && $res1['A712'][$i]!=''){
				$pakcheck="label-success";
			}else{
				$pakcheck="label-important";
			}
			$newbuild.="
			<tr class='info'>
				<td><a href='#' rel='tooltip' title='".$res1['DRE_V20_1'][$i]."' data-toggle='modal' data-target='#NEW_".$res1['SIT_UDK'][$i]."'>".$res1['WOR_UDK'][$i]."<br>".$res1['SIT_UDK'][$i]."</a></td>
				<td><a href='#' rel='tooltip' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_DOM_WOS_CODE'][$i]."</a></td>
				<td>".$res1['DRE_V2_1_6'][$i]."<br><a href='#' rel='popover' data-content='".$res1['WOR_HSDPA_CLUSTER'][$i]."' data-original-title='RF INFO'>".$res1['SIT_LKP_STY_CODE'][$i]."</a></td>
				<td><a href='#' rel='tooltip' title='PO ACQ: ".$res1['A501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."</a></td>
				<td>".$res1['A709'][$i]."</td>
				<td>".$res1['A105'][$i]."</td>
				<td><a href='#' rel='tooltip' title='PO CON: ".$res1['A503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."</a></td>
				<td><a href='#' rel='popover' data-content='".$res1['A353_NOTES'][$i]."' data-original-title='Budget Info'>".$res1['A353'][$i]."</a></td>
				<td><a href='#' rel='popover' data-content='".$res1['A54'][$i]."' data-original-title='Lease activation'>".$res1['A59'][$i]."</a></td>
				<td><a href='#' rel='popover' data-content='".$res1['A71_ESTIM'][$i]."' data-original-title='RF INFO'>".$res1['A71'][$i]."</a></td>
				<td>".$res1['A63'][$i]."</td>
				<td>".$res1['A91'][$i]."</td>
				<td>".$res1['A80'][$i]."</td>
				<td><a href='#' rel='popover' data-placement='bottom'  data-content='".
				"A503: ". $res1['A503'][$i]."<br>A725: ".$res1['A725'][$i]."<br>A134: ".$res1['A134'][$i]."<br>A141: ".$res1['A141'][$i] 
				.'<br>A54: '.$res1['A54'][$i]."<br>A45: ".$res1['A45'][$i]."<br>A50: ".$res1['A50'][$i]."<br>A59: ".$res1['A59'][$i]."<br>A63: ".$res1['A63'][$i]
				.'<br>A110: '.$res1['A110'][$i]."<br>A64: ".$res1['A64'][$i]."<br>A65: ".$res1['A65'][$i]."<br>A285: ".$res1['A285'][$i]."<br>A288: ".$res1['A288'][$i]
				.'<br>A71: '.$res1['A71'][$i]."<br>A91: ".$res1['A91'][$i]."<br>A83: ".$res1['A83'][$i]."<br>A309: ".$res1['A309'][$i]
				.'<br>A92: '.$res1['A92'][$i]."<br>A80: ".$res1['A80'][$i]."<br>A712: ".$res1['A712'][$i]
				."' data-original-title='PAC CHECK'><span class='label ".$pakcheck."'>".$pac."</span></a></td>
				<td>".$res1['A81'][$i]."</td>
			</tr>";
		}else{  // HISTORY
			$newbuild_history.="
			<tr class='NB_hist_data' style='display:none;'>
				<td><a href='#' rel='tooltip' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_UDK'][$i]."<br>".$res1['SIT_UDK'][$i]."</a></td>
				<td><a href='#' rel='tooltip' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_DOM_WOS_CODE'][$i]."</a></td>
				<td>".$res1['DRE_V2_1_6'][$i]."<br><a href='#' rel='popover' data-content='".$res1['WOR_HSDPA_CLUSTER'][$i]."' data-original-title='RF INFO'>".$res1['SIT_LKP_STY_CODE'][$i]."</a></td>
				<td><a href='#' rel='tooltip' title='PO ACQ: ".$res1['A501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."</a></td>
				<td>".$res1['A709'][$i]."</td>
				<td>".$res1['A105'][$i]."</td>
				<td><a href='#' rel='tooltip' title='PO CON: ".$res1['A503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."</a></td>
				<td><a href='#' rel='popover' data-content='".$res1['A353_NOTES'][$i]."' data-original-title='Budget Info'>".$res1['A353'][$i]."</a></td>
				<td><a href='#' rel='popover' data-content='".$res1['A54'][$i]."' data-original-title='Lease activation'>".$res1['A59'][$i]."</a></td>
				<td><a href='#' rel='popover' data-content='".$res1['A71_ESTIM'][$i]."' data-original-title='RF INFO'>".$res1['A71'][$i]."</a></td>
				<td>".$res1['A63'][$i]."</td>
				<td>".$res1['A91'][$i]."</td>
				<td>".$res1['A80'][$i]."</td>
				<td>".$res1['A72'][$i]."</td>
				<td>".$res1['A81'][$i]."</td>
				</tr>";
		}
	}

	$query="SELECT * FROM  VW_NET1_ALL_UPGRADES  WHERE SIT_UDK LIKE '%".$siteID."%' ORDER BY SIT_UDK, WOR_DOM_WOS_CODE ASC";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_UPG=count($res1['SIT_UDK']);
	}

	for ($i=0;$i<$amount_of_UPG;$i++){
		if ($res1['U405'][$i]!="" && $res1['U709'][$i]!=""){
				$virtual='OK';
			}else{
				$virtual='NOT OK';
		}
		?>
		<div id='UPG_<?=$res1['WOR_UDK'][$i]?>' class='modal hide fade modalwide' role='dialog' aria-labelleby='myModelLabel' aria-hidden='true' style='display:none;'>
		 <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3 id="myModalLabel"><?=$res1['SIT_UDK'][$i]?> - <?=$res1['WOR_UDK'][$i]?></h3>
		  </div>
		  <div class="modal-body">
		    <div class="pull-left"  style="width:49%; border-right:1px solid;">
		    	<table class="table table-striped table-hover table-condensed">
		    	<caption>Acquisition</caption>
		    	<tbody>
				<tr>
				<td>U001</td><td class='parameter'>Issued</td><td><?=$res1['U001'][$i]?></td>
				</tr>
				<tr>
				<td>U501</td><td class='parameter'>PO Acquisition Sent</td><td><?=$res1['U501'][$i]?></td>
				</tr>
				<tr>
				<td>U003</td><td class='parameter'> PSR In</td><td><?=$res1['U003'][$i]?></td>
				</tr>
				<tr>
				<td>U004</td><td class='parameter'>PSR Out</td><td><?=$res1['U004'][$i]?></td>
				</tr>
				<tr>
				<td>U007</td><td class='parameter'>Site Visit - TR for Newcomer</td><td><?=$res1['U007'][$i]?></td>
				</tr>
				<tr>
				<td>U014</td><td class='parameter'>LS Newcomer Approved</td><td><?=$res1['U014'][$i]?></td>
				</tr>
				<tr>
				<td>U100</td><td class='parameter'>(SUB)Lease Signed</td><td><?=$res1['U100'][$i]?></td>
				</tr>
				<tr>
				<td>U162</td><td class='parameter'>Kick off meeting</td><td><?=$res1['U162'][$i]?></td>
				</tr>
				<tr>
				<td>U104</td><td class='parameter'>BP Newcomer Received</td><td><?=$res1['U104'][$i]?></td>
				</tr>
				<tr>
				<td>U326</td><td class='parameter'>Techincal Review Held</td><td><?=$res1['U326'][$i]?></td>
				</tr>
				<tr>
				<td>U328</td><td class='parameter'>Lease Sketch Approved</td><td><?=$res1['U328'][$i]?></td>
				</tr>
				<tr>
				<td>U709</td><td class='parameter'>Lease Ok To Build</td><td><?=$res1['U709'][$i]?></td>
				</tr>
				<tr>
				<td>U334</td><td class='parameter'>BP Application Submitted </td><td><?=$res1['U334'][$i]?></td>
				</tr>
				<tr>
				<td>U335</td><td class='parameter'>Acknowledgement from Region</td><td><?=$res1['U335'][$i]?></td>
				</tr>
				<tr>
				<td>U337</td><td class='parameter'>Authority Level 2: Commune</td><td><?=$res1['U337'][$i]?></td>
				</tr>
				<tr>
				<td>U400</td><td class='parameter'>Public Inquiry Start</td><td><?=$res1['U400'][$i]?></td>
				</tr>
				<tr>
				<td>U401</td><td class='parameter'>Public Inquiry End</td><td><?=$res1['U401'][$i]?></td>
				</tr>
				<tr>
				<td>U340</td><td class='parameter'>Authority Level 3: Region</td><td><?=$res1['U340'][$i]?></td>
				</tr>
				<tr>
				<td>U402</td><td class='parameter'>BP Refused</td><td><?=$res1['U402'][$i]?></td>
				</tr>
				<tr>
				<td>U403</td><td class='parameter'>Special BP</td><td><?=$res1['U403'][$i]?></td>
				</tr>
				<tr>
				<td>U404</td><td class='parameter'>Decree</td><td><?=$res1['U404'][$i]?></td>
				</tr>
				<tr>
				<td>U341</td><td class='parameter'>BP Received</td><td><?=$res1['U341'][$i]?></td>
				</tr>
				<tr>
				<td>U405</td><td class='parameter'>BP OK To Build</td><td><?=$res1['U405'][$i]?></td>
				</tr>
				<tr>
				<td>Virtual</td><td class='parameter'>Virtual Lease & BP OK</td><td><?=$virtual?></td>
				</tr>
				<tr>
				<td>A407</td><td class='parameter'>BP Expiry Date</td><td><?=$res1['U407'][$i]?></td>
				</tr>
				<tr>
				<td>U408</td><td class='parameter'>BP Extension Request Sent</td><td><?=$res1['U408'][$i]?></td>
				</tr>
				<tr>
				<td>U409</td><td class='parameter'>BP Extension Request Received</td><td><?=$res1['U409'][$i]?></td>
				</tr>
				<tr>
				<td>U710</td><td class='parameter'>Lease option expired</td><td><?=$res1['U710'][$i]?></td>
				</tr>
				<tr>
				<td>U711</td><td class='parameter'>ALU Acquired</td><td><?=$res1['U711'][$i]?></td>
				</tr>
				<tr>
				<td style='background-color:lightgreen;'>U352</td><td class='parameter'>Acquisition Completed</td><td><?=$res1['U352'][$i]?></td>
				</tr>
			</tbody>
	   	 	</table>
	    </div>
	    <div class="pull-right"  style="width:49%; border-left:1px solid;">
			<table class="table table-striped table-hover table-condensed">
	    	<caption>Construction</caption>
	    	<tbody>
			<tr>
			<td>U353</td><td class='parameter'>M/S 5.1 - Site Funded</td><td><?=$res1['U353'][$i]?></td>
			</tr>
			<tr>
				<td>U503</td><td class='parameter'>PO For Construction Sent</td><td><?=$res1['U503'][$i]?></td>
			</tr>
			<tr>
				<td>U134</td><td class='parameter'>Environmental Permit Submitted</td><td><?=$res1['U134'][$i]?></td>
			</tr>
			<tr>
				<td>U141</td><td class='parameter'>Environmental Permit Received</td><td><?=$res1['U141'][$i]?></td>
			</tr>
			<tr>
				<td>U305</td><td class='parameter'>BSDS Funded</td><td><?=$res1['U305'][$i]?></td>
			</tr>
			<tr>
				<td>U306</td><td class='parameter'>Material Ordering Process Initiated</td><td><?=$res1['U306'][$i]?></td>
			</tr>
			<tr>
				<td>U307</td><td class='parameter'>Materials Available</td><td><?=$res1['U307'][$i]?></td>
			</tr>
			<tr>
				<td>U308</td><td class='parameter'>Materials Received</td><td><?=$res1['U308'][$i]?></td>
			</tr>
			<tr>
				<td>U309</td><td class='parameter'>Materials Returned</td><td><?=$res1['U309'][$i]?></td>
			</tr>
			<tr>
				<td>U345</td><td class='parameter'>Construction Kick-off meeting</td><td><?=$res1['U345'][$i]?></td>
			</tr>
			<tr>
				<td>U349</td><td class='parameter'>Detailed design / Construction drawing approved</td><td><?=$res1['U349'][$i]?></td>
			</tr>
			<tr>
				<td>U459</td><td class='parameter'>M/S 5.2 Physical Start On Site</td><td><?=$res1['U459'][$i]?></td>
			</tr>
			<tr>
				<td>U680</td><td class='parameter'>Letter Start of Works Sent</td><td><?=$res1['U680'][$i]?></td>
			</tr>
			<tr>
				<td>U365</td><td class='parameter'>HSF Submitted</td><td><?=$res1['U365'][$i]?></td>
			</tr>
			<tr>
				<td>U388</td><td class='parameter'>HSF Completed</td><td><?=$res1['U388'][$i]?></td>
			</tr>
			<tr>
				<td>U361</td><td class='parameter'>Cabinet installed on site</td><td><?=$res1['U361'][$i]?></td>
			</tr>
			<tr>
				<td>U440</td><td class='parameter'>Transmission Planned</td><td><?=$res1['U440'][$i]?></td>
			</tr>
			<tr>
				<td>U446</td><td class='parameter'>Transmission Capacity Implemented</td><td><?=$res1['U446'][$i]?></td>
			</tr>
			<tr>
				<td>U363</td><td class='parameter'>Civil Inspection Performed</td><td><?=$res1['U363'][$i]?></td>
			</tr>
			<tr>
				<td>U310</td><td class='parameter'>Only C punches CW</td><td><?=$res1['U310'][$i]?></td>
			</tr>
			<tr>
				<td>U364</td><td class='parameter'>Civil Inspection - All punches cleared</td><td><?=$res1['U364'][$i]?></td>
			</tr>
			<tr>
				<td>U571</td><td class='parameter'>M/S 6.5 - Site/UPG Integrated</td><td><?=$res1['U571'][$i]?></td>
			</tr>
			<tr>
				<td>U391</td><td class='parameter'>M/S 6.0 - Implementation Inspection Performed</td><td><?=$res1['U391'][$i]?></td>
			</tr>
			<tr>
				<td>U383</td><td class='parameter'>M/S 7.0 - Joint Inspection C Punches only</td><td><?=$res1['U383'][$i]?></td>
			</tr>
			<tr>
				<td>U392</td><td class='parameter'>M/S 6.1 - Implementation Inspection Completed</td><td><?=$res1['U392'][$i]?></td>
			</tr>

			<tr>
				<td>U380</td><td class='parameter'>M/S 7.2 - SITE/UPG Debarred</td><td><?=$res1['U380'][$i]?></td>
			</tr>
			<tr>
				<td>U712</td><td class='parameter'>RF Pac Completed</td><td><?=$res1['U712'][$i]?></td>
			</tr>
			<tr>
				<td>U418</td><td class='parameter'>M/S 7.1 - PAC  Site Provisionally Accepted</td><td><?=$res1['U418'][$i]?></td>
			</tr>
			<tr>
				<td>U381</td><td class='parameter'>M/S 7.3 - FAC Final Acceptance</td><td><?=$res1['U381'][$i]?></td>
			</tr>
			<tr>
				<td>U825</td><td class='parameter'>Civil Works Completed</td><td><?=$res1['U825'][$i]?></td>
			</tr>
			<tr>
				<td>U999</td><td class='parameter'>Upgrade Complete</td><td><?=$res1['U999'][$i]?></td>
			</tr>
			<tr>
				<td>U220</td><td class='parameter'>FAC (NEwcomer/BASE)</td><td><?=$res1['U220'][$i]?></td>
			</tr>
			</tbody>
	   	 	</table>
	    </div>
	  </div>
	  <div class="modal-footer">
	    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	  </div>
	</div>
	<?php

	if (trim($res1['WOR_DOM_WOS_CODE'][$i])=='IS'){
		if ($res1['U418'][$i]){
			$pac=$res1['U418'][$i];
		}else{
			$pac='X';
		}
		if($res1['U503'][$i]."<br>".$res1['U725'][$i]."<br>".$res1['U134'][$i]!='' && $res1['U141'][$i]!='' 
		&& $res1['U102'][$i]!='' && $res1['U345'][$i]!='' && $res1['U349'][$i]!='' && $res1['U459'][$i]!='' && $res1['U363'][$i]!=''
		&& $res1['U310'][$i]!='' && $res1['U464'][$i]!='' && $res1['U365'][$i]!='' && $res1['U388'][$i]!=''
		&& $res1['U571'][$i]!='' && $res1['U391'][$i]!='' && $res1['U383'][$i]!='' && $res1['U309'][$i]!=''  && $res1['U392'][$i]!=''
		&& $res1['U380'][$i]!='' && $res1['U712'][$i]!=''){
			$pakcheck="label-success";
		}else{
			$pakcheck="label-important";
		}
		$upgrade.="
		<tr class='info'>
			<td class='header_site'><a href='#' rel='tooltip' title='".$res1['WOR_NAME'][$i]."' data-toggle='modal' data-target='#UPG_".$res1['WOR_UDK'][$i]."'>".$res1['SIT_UDK'][$i]."<br>".$res1['WOR_UDK'][$i]."</a></td>
			<td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
			<td><a href='#' rel='tooltip' title='RFINFO: ".$res1['WOR_HSDPA_CLUSTER'][$i]."'>".$res1['WOR_LKP_WCO_CODE'][$i]."</a></td>
			<td><a href='#' rel='tooltip' title='PO ACQ: ".$res1['U501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."</a></td>";

			if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'SH')){
				$upgrade.="<td><a href='#' rel='tooltip' title='U100'>".$res1['U100'][$i]."</a></td>";
				$upgrade.="<td><a href='#' rel='tooltip' title='U104'>".$res1['U104'][$i]."</a></td>";
			}else{
				$upgrade.="<td><a href='#' rel='tooltip' title='U709'>".$res1['U709'][$i]."</a></td>";
				$upgrade.="<td><a href='#' rel='tooltip' title='U405'>".$res1['U405'][$i]."</a></td>";
			}
		$upgrade.="
			<td><a href='#' rel='tooltip' title='PO CON: ".$res1['U503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."</a></td>
			<td><a href='#' rel='popover' data-content='".$res1['U353_NOTES'][$i]."' data-original-title='Budget info'>".$res1['U353'][$i]."</a></td>
			<td>".$res1['U459'][$i]."</td>
			<td><a href='#' rel='popover' data-content='".$res1['U571_ESTIM'][$i]."' data-original-title='Integration forecast'>".$res1['U571'][$i]."</a></td>
			<td>".$res1['U363'][$i]."</td>
			<td>".$res1['U391'][$i]."</td>
			<td>".$res1['U380'][$i]."<br>".$res1['U825'][$i]."</td>
			<td><a href='#' rel='popover' data-placement='bottom' class='".$pakcheck."' data-content='".
			"A503: ". $res1['U503'][$i]."<br>U725: ".$res1['U725'][$i]."<br>U134: ".$res1['U134'][$i]."<br>U141: ".$res1['U141'][$i] 
			.'<br>U102: '.$res1['U102'][$i]."<br>U345: ".$res1['U345'][$i]."<br>U349: ".$res1['U349'][$i]."<br>U459: ".$res1['U459'][$i]."<br>U363: ".$res1['U363'][$i]
			.'<br>U110: '.$res1['U110'][$i]."<br>U364: ".$res1['U364'][$i]."<br>U365: ".$res1['U365'][$i]."<br>U388: ".$res1['U388'][$i]
			.'<br>U571: '.$res1['U571'][$i]."<br>U391: ".$res1['U391'][$i]."<br>U383: ".$res1['U383'][$i]."<br>U309: ".$res1['U392'][$i]
			.'<br>U392: '.$res1['U392'][$i]."<br>U380: ".$res1['U380'][$i]."<br>U712: ".$res1['U712'][$i]
			."' data-original-title='PAC CHECK'><span class='label ".$pakcheck."'>".$pac."</span></a></td>
			<td>".$res1['U381'][$i]."</td>
			</tr> ";
	}else{
		$upgrade_history.="
		<tr class='UPG_hist_data' style='display:none;'>
			<td class='header_site'><a href='#' rel='tooltip' title='".$res1['WOR_NAME'][$i]."'>".$res1['SIT_UDK'][$i]."<br>".$res1['WOR_UDK'][$i]."</a></td>
			<td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
			<td><a href='#' rel='popover' data-content='".$res1['WOR_HSDPA_CLUSTER'][$i]."' data-original-title='RFINFO'>".$res1['WOR_LKP_WCO_CODE'][$i]."</a></td>
			<td><a href='#' rel='tooltip' title='PO ACQ: ".$res1['U501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."</a></td>";

		if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'SH')){
			$upgrade_rank2.="<td><a href='#' title='U100'>".$res1['U100'][$i]."</a></td>";
			$upgrade_rank2.="<td><a href='#' title='U104'>".$res1['U104'][$i]."</a></td>";
		}else{
			$upgrade_rank2.="<td><a href='#' title='U709'>".$res1['U709'][$i]."</a></td>";
			$upgrade_rank2.="<td><a href='#' title='U405'>".$res1['U405'][$i]."</a></td>";
		}

		$upgrade_history.="
			<td><a href='#' rel='tooltip' title='PO CON: ".$res1['U503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."</a></td>
			<td><a href='#' rel='popover' data-content='".$res1['U353_NOTES'][$i]."' data-original-title='Budget info'>".$res1['U353'][$i]."</a></td>
			<td>".$res1['U459'][$i]."</td>
			<td><a href='#' rel='popover' data-content='".$res1['U571_ESTIM'][$i]."' data-original-title='Integration forecast'>".$res1['U571'][$i]."</a></td>
			<td>".$res1['U363'][$i]."</td>
			<td>".$res1['U391'][$i]."</td>";

		if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'CWK')){
			$upgrade_history.="<td><a href='#' title='U825'>".$res1['U825'][$i]."</a></td>";
		}else if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'SH')){
			$upgrade_history.="<td><a href='#' title='U999'>".$res1['U999'][$i]."</a></td>";
		}else{
			$upgrade_history.="<td><a href='#' title='U385 & U825'>".$res1['U380'][$i]."<br>".$res1['U825'][$i]."</a></td>";
		}

		$upgrade_history.="
			<td>".$res1['U418'][$i]."</td>
			<td>".$res1['U381'][$i]."</td>
			</tr>";	
	}
	
}
*/
?>
<table class="table table-striped table-hover table-condensed">
	<caption>NEWBUILDS &nbsp;<div class="btn-group"><a class="btn btn-mini history" href="#" id="NB_hist">
	<i class="icon-circle-arrow-down"></i> HISTORY</a>
	</caption>
  	<thead>
    <tr>
		<th>SITE</th>
		<th>ST</th>
		<th>TYPE</th>
		<th>SAC</th>
		<th>LEASE OK</th>
		<th>BP OK </th>
		<th>CON</th>
		<th>FUNDED</th>
		<th>PS</th>
		<th>INT</th>
		<th>CWI</th>
		<th>JI</th>
		<th>DEBARRED</th>
		<th>PAC</th>
		<th>FAC</th>
    </tr>
  	</thead>
 	 <tbody>
 	<?php echo $newbuild; ?>
 	<?php echo $newbuild_history; ?>
  	</tbody>
</table>

<table class="table table-striped table-hover table-condensed">
	<caption>
		UPGRADES &nbsp; <div class="btn-group"><a class="btn btn-mini history" href="#" id="UPG_hist">
		<i class="icon-circle-arrow-down"></i> HISTORY</a>
	</caption>
  	<thead>
    <tr>
		<th>SITE</th>
		<th>ST</th>
		<th>TYPE</th>
		<th>SAC</th>
		<th>LEASE OK</th>
		<th>BP OK </th>
		<th>CON</th>
		<th>FUNDED</th>
		<th>PS</th>
		<th>INT</th>
		<th>CWI</th>
		<th>JI</th>
		<th>DEBARRED</th>
		<th>PAC</th>
		<th>FAC</th>
    </tr>
  	</thead>
 	 <tbody>
 	<?php echo $upgrade; ?>
 	<?php echo $upgrade_history; ?>
  	</tbody>
</table>
<?php
}
?>
<script type="text/javascript">
	if ($("[rel=tooltip]").length) {
	        $("[rel=tooltip]").tooltip();
	 }
	 if ($("[rel=popover]").length) {
	        $("[rel=popover]").popover({trigger:'hover'});
	 }
 </script>
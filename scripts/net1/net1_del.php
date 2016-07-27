<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("../general_info/general_info_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['siteID']){
	$BSDSrefresh=get_BSDSrefresh();

	$siteID=$_POST['siteID'];

	$query="select * from VW_NET1_TASKNAMES";
	//echo $query;
	$stmtN = parse_exec_fetch($conn_Infobase, $query, $error_str, $resN);
	if (!$stmtN) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmtN);
	}

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

	for ($i=0;$i<$amount_of_NEW;$i++){
		/*if ($res1['A105'][$i]!="" && $res1['A709'][$i]!=""){
			$virtual='OK';
		}else{
			$virtual='NOT OK';
		}*/
		if ($res1['WIPA'][$i]=='TECHM'){
			$WIPA='<span class="label label-warning">WIP TECHM</span>';
		}else{
			$WIPA='';
		}
		if ($res1['WIPC'][$i]=='TECHM'){
			$WIPC='<span class="label label-warning">WIP TECHM</span>';
		}else{
			$WIPC='';
		}
		?>
<div class='modal fade' id='NEW_<?=$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]?>' tabindex="-1" role="dialog" aria-labelledby="NEW_<?=$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]?>Label" aria-hidden="true">
	<div class="modal-dialog modalwide"> 
		<div class="modal-content"> 	
		 	<div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    	<h3 id="NEW_<?=$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]?>Label"><?=$res1['SIT_UDK'][$i]?></h3>
		  	</div>
		  	<div class="modal-body">
		    	<div class="row">
		    		<div class="col-md-6">
				    	<table class="table table-striped table-hover table-condensed">
				    	<caption><h3><span class="label label-primary">Acquisition</span></h3></caption>
				    	<tbody>
							<tr>
							<td>A04</td><td class='parameter'><?=$resN['A04U001'][0]?></td><td><?=$res1['A04'][$i]?></td>
							</tr>
							<tr>
							<td>A501</td><td class='parameter'><?=$resN['AU501'][0]?></td><td><?=$res1['A501'][$i]?></td>
							</tr>
							<tr>
							<td>A15</td><td class='parameter'><?=$resN['A15'][0]?></td><td><?=$res1['A15'][$i]?></td>
							</tr>
							<tr>
							<td>A26</td><td class='parameter'><?=$resN['A26U326'][0]?></td><td><?=$res1['A26'][$i]?></td>
							</tr>
							<tr>
							<td>A28</td><td class='parameter'><?=$resN['A28U328'][0]?></td><td><?=$res1['A28'][$i]?></td>
							</tr>
							<tr>
							<td>A901</td><td class='parameter'><?=$resN['AU901'][0]?></td><td><?=$res1['A901'][$i]?></td>
							</tr>
							<tr>
							<td>A902</td><td class='parameter'><?=$resN['AU902'][0]?></td><td><?=$res1['A902'][$i]?></td>
							</tr>
							<tr>
							<td>A34</td><td class='parameter'><?=$resN['A34U334'][0]?></td><td><?=$res1['A34'][$i]?></td>
							</tr>
							<tr>
							<td>A35</td><td class='parameter'><?=$resN['A35U335'][0]?></td><td><?=$res1['A35'][$i]?></td>
							</tr>
							<tr>
							<td>A37</td><td class='parameter'><?=$resN['A37U337'][0]?></td><td><?=$res1['A37'][$i]?></td>
							</tr>
							<tr>
							<td>A100</td><td class='parameter'><?=$resN['A100U400'][0]?></td><td><?=$res1['A100'][$i]?></td>
							</tr>
							<tr>
							<td>A101</td><td class='parameter'><?=$resN['A101U401'][0]?></td><td><?=$res1['A101'][$i]?></td>
							</tr>
							<tr>
							<td>A40</td><td class='parameter'><?=$resN['A40U340'][0]?></td><td><?=$res1['A40'][$i]?></td>
							</tr>
							<tr>
							<td>A102</td><td class='parameter'><?=$resN['A102U402'][0]?></td><td><?=$res1['A102'][$i]?></td>
							</tr>
							<tr>
							<td>A103</td><td class='parameter'><?=$resN['A103U403'][0]?></td><td><?=$res1['A103'][$i]?></td>
							</tr>
							<tr>
							<td>A104</td><td class='parameter'><?=$resN['A104U404'][0]?></td><td><?=$res1['A104'][$i]?></td>
							</tr>
							<tr>
							<td>A41</td><td class='parameter'><?=$resN['A41U341'][0]?></td><td><?=$res1['A41'][$i]?></td>
							</tr>
							<tr>
							<td>A105</td><td class='parameter'><?=$resN['A105U405'][0]?></td><td><?=$res1['A105'][$i]?></td>
							</tr>
							<tr>
							<td>Virtual</td><td class='parameter'><?=$resN['A105U405'][0]?> + <?=$resN['AU709'][0]?></td><td><?=$virtual?></td>
							</tr>
							<tr>
							<td>A711</td><td class='parameter'><?=$resN['AU711'][0]?></td><td><?=$res1['A711'][$i]?></td>
							</tr>
							<tr>
							<td>A352</td><td class='parameter'><?=$resN['AU352'][0]?></td><td><?=$res1['A352'][$i]?></td>
							</tr>
							<tr>
							<td>A407</td><td class='parameter'><?=$resN['AU407'][0]?></td><td><?=$res1['A407'][$i]?></td>
							</tr>
							<tr>
							<td>A408</td><td class='parameter'><?=$resN['AU408'][0]?></td><td><?=$res1['A408'][$i]?></td>
							</tr>
							<tr>
							<td>A409</td><td class='parameter'><?=$resN['AU409'][0]?></td><td><?=$res1['A409'][$i]?></td>
							</tr>
							<tr>
							<td>A710</td><td class='parameter'><?=$resN['AU710'][0]?></td><td><?=$res1['A710'][$i]?></td>
							</tr>
							<tr>
							<td>A711</td><td class='parameter'><?=$resN['AU711'][0]?></td><td><?=$res1['A711'][$i]?></td>
							</tr>
				    	</tbody>
				   	 	</table>
			   	 	</div>
		    		<div class="col-md-6">
						<table class="table table-striped table-hover table-condensed">
				    	<caption><h3><span class="label label-primary">Construction</span></h3></caption>
				    	<tbody>
							<tr>
								<td>A353</td><td class='parameter'><?=$resN['AU353'][0]?></td><td><?=$res1['A353'][$i]?></td>
							</tr>
							<tr>
								<td>A503</td><td class='parameter'><?=$resN['AU503'][0]?></td><td><?=$res1['A503'][$i]?></td>
							</tr>
							<tr>
								<td>A904</td><td class='parameter'><?=$resN['AU904'][0]?></td><td><?=$res1['A904'][$i]?></td>
							</tr>
							<tr>
								<td>A905</td><td class='parameter'><?=$resN['AU905'][0]?></td><td><?=$res1['A905'][$i]?></td>
							</tr>
							<tr>
								<td>A134</td><td class='parameter'><?=$resN['AU134'][0]?></td><td><?=$res1['A134'][$i]?></td>
							</tr>
							<tr>
								<td>A141</td><td class='parameter'><?=$resN['AU141'][0]?></td><td><?=$res1['A141'][$i]?></td>
							</tr>
							<tr>
								<td>A305</td><td class='parameter'><?=$resN['AU305'][0]?></td><td><?=$res1['A305'][$i]?></td>
							</tr>
							<tr>
								<td>A306</td><td class='parameter'><?=$resN['AU306'][0]?></td><td><?=$res1['A306'][$i]?></td>
							</tr>
							<tr>
								<td>A307</td><td class='parameter'><?=$resN['AU307'][0]?></td><td><?=$res1['A307'][$i]?></td>
							</tr>
							<tr>
								<td>A308</td><td class='parameter'><?=$resN['AU308'][0]?></td><td><?=$res1['A308'][$i]?></td>
							</tr>
							<tr>
								<td>A309</td><td class='parameter'><?=$resN['AU309'][0]?></td><td><?=$res1['A309'][$i]?></td>
							</tr>
							<tr>
								<td>A45</td><td class='parameter'><?=$resN['A45U345'][0]?></td><td><?=$res1['A45'][$i]?></td>
							</tr>
							<tr>
								<td>A50</td><td class='parameter'><?=$resN['A50U349'][0]?></td><td><?=$res1['A50'][$i]?></td>
							</tr>
							<tr>
								<td>A54</td><td class='parameter'><?=$resN['A54U102'][0]?></td><td><?=$res1['A54'][$i]?></td>
							</tr>
							<tr>
								<td>A59</td><td class='parameter'><?=$resN['A59U459'][0]?></td><td><?=$res1['A59'][$i]?></td>
							</tr>
							<tr>
								<td>A680</td><td class='parameter'><?=$resN['AU680'][0]?></td><td><?=$res1['A680'][$i]?></td>
							</tr>
							<tr>
								<td>A285</td><td class='parameter'><?=$resN['A285U365'][0]?></td><td><?=$res1['A285'][$i]?></td>
							</tr>
							<tr>
								<td>A288</td><td class='parameter'><?=$resN['A288U388'][0]?></td><td><?=$res1['A288'][$i]?></td>
							</tr>
							<tr>
								<td>A361</td><td class='parameter'><?=$resN['AU361'][0]?></td><td><?=$res1['A361'][$i]?></td>
							</tr>
							<tr>
								<td>A44</td><td class='parameter'><?=$resN['A44U440'][0]?></td><td><?=$res1['A44'][$i]?></td>
							</tr>
							<tr>
								<td>A46</td><td class='parameter'><?=$resN['A46U446'][0]?></td><td><?=$res1['A46'][$i]?></td>
							</tr>
							<tr>
								<td>A63</td><td class='parameter'><?=$resN['A63U363'][0]?></td><td><?=$res1['A63'][$i]?></td>
							</tr>
							<tr>
								<td>A110</td><td class='parameter'><?=$resN['A110U310'][0]?></td><td><?=$res1['A110'][$i]?></td>
							</tr>
							<tr>
								<td>A64</td><td class='parameter'><?=$resN['A64U364'][0]?></td><td><?=$res1['A64'][$i]?></td>
							</tr>
							<tr>
								<td>A65</td><td class='parameter'><?=$resN['A65'][0]?></td><td><?=$res1['A65'][$i]?></td>
							</tr>
							<tr>
								<td>A71</td><td class='parameter'><?=$resN['A71U571'][0]?></td><td><?=$res1['A71'][$i]?></td>
							</tr>
							<tr>
								<td>A91</td><td class='parameter'><?=$resN['A91U391'][0]?></td><td><?=$res1['A91'][$i]?></td>
							</tr>
							<tr>
								<td>A83</td><td class='parameter'><?=$resN['A83U383'][0]?></td><td><?=$res1['A83'][$i]?></td>
							</tr>
							<tr>
								<td>A92</td><td class='parameter'><?=$resN['A92U392'][0]?></td><td><?=$res1['A92'][$i]?></td>
							</tr>
							<tr>
								<td>A712</td><td class='parameter'><?=$resN['AU712'][0]?></td><td><?=$res1['A712'][$i]?></td>
							</tr>
							<tr>
								<td>A80</td><td class='parameter'><?=$resN['A80U380'][0]?></td><td><?=$res1['A80'][$i]?></td>
							</tr>
							<tr>
								<td>A72</td><td class='parameter'><?=$resN['A72U418'][0]?></td><td><?=$res1['A72'][$i]?></td>
							</tr>
							<tr>
								<td>A81</td><td class='parameter'><?=$resN['A81U381'][0]?></td><td><?=$res1['A81'][$i]?></td>
							</tr>
							<tr>
								<td>A200</td><td class='parameter'><?=$resN['A200'][0]?></td><td><?=$res1['A200'][$i]?></td>
							</tr>
							<tr>
								<td>A250</td><td class='parameter'><?=$resN['A250'][0]?></td><td><?=$res1['A250'][$i]?></td>
							</tr>
							<tr>
								<td>A270</td><td class='parameter'><?=$resN['A270'][0]?></td><td><?=$res1['A270'][$i]?></td>
							</tr>
							<tr>
								<td>A270 EST</td><td class='parameter'>ESTIMATE of <?=$resN['A270'][0]?></td><td><?=$res1['A270_ESTIM'][$i]?></td>
							</tr>
							<tr>
								<td>A275</td><td class='parameter'><?=$resN['A275'][0]?></td><td><?=$res1['A275'][$i]?></td>
							</tr>
				    	</tbody>
				   	 	</table>
			   	 </div>
		    	</div>
		  	</div><!-- /.modal-body-->
		  	<div class="modal-footer">
			    <button tyep="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content-->
	</div><!-- /.modal-dialog-->
</div><!-- /.modal-->
		<?php
		$query="SELECT SITEID,MONUMENT from MONLAND WHERE SITEID LIKE '%".$res1['SIT_UDK'][$i]."%'";
		
		$stmtB = parse_exec_fetch($conn_Infobase, $query, $error_str, $resB);
		if (!$stmtB){
			die_silently($conn_Infobase, $error_str);
			exit;
		}else{
			OCIFreeStatement($stmtB);
			$amountB=count($resB['SITEID']);
			if ($amountB>0){
				$monu="<div id='prio' class='badge pull-right' style='background-color:#66FF33;' rel='tooltip' title='Monument & Landschappen site: ".$resB['MONUMENT'][0]."'>ML</div>";
			}else{
				$monu='';
			}
		}

		//echo $res1['WOR_UDK'][$i]."---".$res1['WOE_RANK'][$i]."---".$res1['WOR_DOM_WOS_CODE'][$i]."<br>";
		if (trim($res1['WOE_RANK'][$i])==1 && (trim($res1['WOR_DOM_WOS_CODE'][$i])=='IS' OR trim($res1['WOR_DOM_WOS_CODE'][$i])=='OH' OR trim($res1['WOR_DOM_WOS_CODE'][$i])=='AD')){
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
				$pakcheck="label-inverse";
			}
			if (substr($res1['WOR_UDK'][$i],0,1)=='_'){
				$worudk=substr($res1['WOR_UDK'][$i],1);
			}else{ //T sites
				$worudk=$res1['WOR_UDK'][$i];
			}
			if($res1['CON'][$i]=='BENCHMARK' or ($res1['CON'][$i]=='' && $res1['SAC'][$i]=='BENCHMARK')){
				$ran='BENCHMARK_RAN';
			}else{
				$ran='RAN-ALU';
			}
			$ranurl=$config['sitepath_url'].'/bsds/scripts/liveranbrowser/liveranbrowser.php?dir='.substr($res1['WOR_UDK'][$i],1,2)."/".$worudk."/".$res1['SIT_UDK'][$i]."&ran=".$ran;
			//echo $ranurl;
			$newbuild.="
			<tr class='info'>
				<td>
			        <div class='btn-toolbar' role='toolbar'>
			          <div class='btn-group'>
			            <button class='btn btn-default btn-xs' title='View' data-action='view'  data-toggle='modal' data-target='#NEW_".$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]."'><span class='glyphicon glyphicon-eye-open'></span></button>
						<button class='btn btn-default btn-xs validation' title='validation' data-siteupgnr='".$res1['SIT_UDK'][$i]."' data-nbup='NB'><span class='glyphicon glyphicon-check'></span></button>			          
			          	<button class='btn btn-default btn-xs liveran' title='View files LIVE on the RAN' data-ranurl='".$ranurl."'><span class='glyphicon glyphicon-folder-open'></span></button>	
			          </div>
			        </div>";
		        if (substr_count($guard_groups, 'Admin')==1){
		          	$newbuild.="<div class='btn-toolbar role='toolbar'>
		          	<div class='btn-group'>
		          	<button class='btn btn-default btn-xs refreshN1' title='Live refresh from NET1' data-siteupgnr='".$res1['SIT_UDK'][$i]."' data-site='".$res1['WOR_UDK'][$i]."' data-nbup='NB'><span class='glyphicon glyphicon-refresh'></span></button>
		          	</div>
		          	</div>";
		         }
		       $newbuild.="
		        </td>
				<td><a href='#' class='tippy' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_UDK'][$i]."<br>".$res1['SIT_UDK'][$i]."</a></td>
				<td><a href='#' class='tippy' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_DOM_WOS_CODE'][$i]."</a><br>".$monu."</td>
				<td>".$res1['DRE_V2_1_6'][$i]."<br><a href='#' rel='popover' data-content='".$res1['WOR_HSDPA_CLUSTER'][$i]."' data-original-title='RF INFO'>".$res1['SIT_LKP_STY_CODE'][$i]."</a></td>
				<td><a href='#' class='tippy' title='PO ACQ: ".$res1['A501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."<br>".$WIPA."</a></td>
				<td><a href='#' class='tippy' title='A709'>".$res1['A709'][$i]."</a></td>
				<td><a href='#' class='tippy' title='A105'>".$res1['A105'][$i]."</a></td>
				<td><a href='#' class='tippy' title='PO CON: ".$res1['A503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."<br>".$WIPC."</a></td>
				<td><a href='#' rel='popover' class='tippy' title='A353' data-content='".$res1['A353_NOTES'][$i]."' data-original-title='Budget Info'>".$res1['A353'][$i]."</a></td>
				<td><a href='#' rel='popover' class='tippy' title='A59' data-content='".$res1['A54'][$i]."' data-original-title='Lease activation'>".$res1['A59'][$i]."</a></td>
				<td><a href='#' rel='popover' class='tippy' title='A71' data-content='".$res1['A71_ESTIM'][$i]."' data-original-title='RF INFO'>".$res1['A71'][$i]."</a></td>
				<td><a href='#' class='tippy' title='A63'>".$res1['A63'][$i]."</a></td>
				<td><a href='#' class='tippy' title='A91'>".$res1['A91'][$i]."</a></td>
				<td><a href='#' class='tippy' title='A80'>".$res1['A80'][$i]."</a></td>
				<td><a href='#' title='A72 PAC CHECK' rel='popover' data-placement='left' data-content='".
				"<br>A503: ". $res1['A503'][$i]."<br>A725: ".$res1['A725'][$i]."<br>A134: ".$res1['A134'][$i]."<br>A141: ".$res1['A141'][$i].'<br>A54: '.$res1['A54'][$i]."<br>A45: ".$res1['A45'][$i]."<br>A50: ".$res1['A50'][$i]."<br>A59: ".$res1['A59'][$i]."<br>A63: ".$res1['A63'][$i]
				.'<br>A110: '.$res1['A110'][$i]."<br>A64: ".$res1['A64'][$i]."<br>A65: ".$res1['A65'][$i]."<br>A285: ".$res1['A285'][$i]."<br>A288: ".$res1['A288'][$i]
				.'<br>A71: '.$res1['A71'][$i]."<br>A91: ".$res1['A91'][$i]."<br>A83: ".$res1['A83'][$i]."<br>A309: ".$res1['A309'][$i]
				.'<br>A92: '.$res1['A92'][$i]."<br>A80: ".$res1['A80'][$i]."<br>A712: ".$res1['A712'][$i]
				."'>".$pac."</a></td>
				<td>".$res1['A81'][$i]."</td>
			</tr>";
		}else{  // HISTORY
			$newbuild_history.="
			<tr class='NB_hist_data warning' style='display:none;'>
				<td style='font-size:18px'>
			        <div class='btn-toolbar role='toolbar'>
			          <div class='btn-group'>
			            <button class='btn btn-default btn-xs' title='View' data-action='view'  href='#' data-toggle='modal' data-target='#NEW_".$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]."'><span class='glyphicon glyphicon-eye-open'></span></button>
			          </div>
			        </div>
		        </td>
				<td><a href='#' class='tippy' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_UDK'][$i]."<br>".$res1['SIT_UDK'][$i]."</a></td>
				<td><a href='#' class='tippy' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_DOM_WOS_CODE'][$i]."</a><br>".$monu."</td>
				<td>".$res1['DRE_V2_1_6'][$i]."<br><a href='#' rel='popover' data-content='".$res1['WOR_HSDPA_CLUSTER'][$i]."' data-original-title='RF INFO'>".$res1['SIT_LKP_STY_CODE'][$i]."</a></td>
				<td><a href='#' class='tippy' title='PO ACQ: ".$res1['A501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."<br>".$WIPA."</a></td>
				<td>".$res1['A709'][$i]."</td>
				<td>".$res1['A105'][$i]."</td>
				<td><a href='#' class='tippy' title='PO CON: ".$res1['A503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."<br>".$WIPC."</a></td>
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

	$query="SELECT * FROM  VW_NET1_ALL_UPGRADES  WHERE SIT_UDK LIKE '%".$siteID."%' AND WOR_LKP_WCO_CODE!='COP' ORDER BY SIT_UDK, WOR_UDK,WOR_DOM_WOS_CODE ASC";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_UPG=count($res1['SIT_UDK']);
	}
	//echo $amount_of_UPG;
	for ($i=0;$i<$amount_of_UPG;$i++){
		if ($res1['U405'][$i]!="" && $res1['U709'][$i]!=""){
				$virtual='OK';
			}else{
				$virtual='NOT OK';
		}
		if ($res1['WIPA'][$i]=='TECHM'){
			$WIPA='<span class="label label-warning">WIP TECHM</span>';
		}else{
			$WIPA='';
		}
		if ($res1['WIPC'][$i]=='TECHM'){
			$WIPC='<span class="label label-warning">WIP TECHM</span>';
		}else{
			$WIPC='';
		}
		?>
<div class='modal fade' id='UPG_<?=$res1['WOR_UDK'][$i]?>' role='dialog' aria-labelleby='UPG_<?=$res1['WOR_UDK'][$i]?>Label' aria-hidden='true'>
	<div class="modal-dialog modalwide">
		<div class="modal-content">
		 	<div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    	<h4 id="UPG_<?=$res1['WOR_UDK'][$i]?>Label"><?=$res1['SIT_UDK'][$i]?> - <?=$res1['WOR_UDK'][$i]?></h4>
		  	</div>
		  	<div class="modal-body">
		    	<div class="row">
		    		<div class="col-md-6">
				    	<table class="table table-striped table-hover table-condensed">
				    	<caption><h3><span class="label label-primary">Acquisition</span></h3></caption>
				    	<tbody>
						<tr>
						<td>U001</td><td class='parameter'><?=$resN['A04U001'][0]?></td><td><?=$res1['U001'][$i]?></td>
						</tr>
						<tr>
						<td>U501</td><td class='parameter'><?=$resN['AU501'][0]?></td><td><?=$res1['U501'][$i]?></td>
						</tr>
						<tr>
						<td>U003</td><td class='parameter'><?=$resN['U003'][0]?></td><td><?=$res1['U003'][$i]?></td>
						</tr>
						<tr>
						<td>U004</td><td class='parameter'><?=$resN['U004'][0]?></td><td><?=$res1['U004'][$i]?></td>
						</tr>
						<tr>
						<td>U007</td><td class='parameter'><?=$resN['U007'][0]?></td><td><?=$res1['U007'][$i]?></td>
						</tr>
						<tr>
						<td>U014</td><td class='parameter'><?=$resN['U014'][0]?></td><td><?=$res1['U014'][$i]?></td>
						</tr>
						<tr>
						<td>U100</td><td class='parameter'><?=$resN['U100'][0]?></td><td><?=$res1['U100'][$i]?></td>
						</tr>
						<tr>
						<td>U162</td><td class='parameter'><?=$resN['U162'][0]?></td><td><?=$res1['U162'][$i]?></td>
						</tr>
						<tr>
						<td>U104</td><td class='parameter'><?=$resN['U104'][0]?></td><td><?=$res1['U104'][$i]?></td>
						</tr>
						<tr>
						<td>U326</td><td class='parameter'><?=$resN['A26U326'][0]?></td><td><?=$res1['U326'][$i]?></td>
						</tr>
						<tr>
						<td>U328</td><td class='parameter'><?=$resN['A28U328'][0]?></td><td><?=$res1['U328'][$i]?></td>
						</tr>
						<tr>
							<td>U901</td><td class='parameter'><?=$resN['AU901'][0]?></td><td><?=$res1['U901'][$i]?></td>
						</tr>
						<tr>
							<td>U902</td><td class='parameter'><?=$resN['AU902'][0]?></td><td><?=$res1['U902'][$i]?></td>
						</tr>
						<tr>
						<td>U709</td><td class='parameter'><?=$resN['AU709'][0]?></td><td><?=$res1['U709'][$i]?></td>
						</tr>
						<tr>
						<td>U334</td><td class='parameter'><?=$resN['A34U334'][0]?></td><td><?=$res1['U334'][$i]?></td>
						</tr>
						<tr>
						<td>U335</td><td class='parameter'><?=$resN['A35U335'][0]?></td><td><?=$res1['U335'][$i]?></td>
						</tr>
						<tr>
						<td>U337</td><td class='parameter'><?=$resN['A37U337'][0]?></td><td><?=$res1['U337'][$i]?></td>
						</tr>
						<tr>
						<td>U400</td><td class='parameter'><?=$resN['A100U400'][0]?></td><td><?=$res1['U400'][$i]?></td>
						</tr>
						<tr>
						<td>U401</td><td class='parameter'><?=$resN['U104'][0]?></td><td><?=$res1['U401'][$i]?></td>
						</tr>
						<tr>
						<td>U340</td><td class='parameter'><?=$resN['A40U340'][0]?></td><td><?=$res1['U340'][$i]?></td>
						</tr>
						<tr>
						<td>U402</td><td class='parameter'><?=$resN['A102U402'][0]?></td><td><?=$res1['U402'][$i]?></td>
						</tr>
						<tr>
						<td>U403</td><td class='parameter'><?=$resN['A103U403'][0]?></td><td><?=$res1['U403'][$i]?></td>
						</tr>
						<tr>
						<td>U404</td><td class='parameter'><?=$resN['A104U404'][0]?></td><td><?=$res1['U404'][$i]?></td>
						</tr>
						<tr>
						<td>U341</td><td class='parameter'><?=$resN['A41U341'][0]?></td><td><?=$res1['U341'][$i]?></td>
						</tr>
						<tr>
						<td>U405</td><td class='parameter'><?=$resN['A105U405'][0]?></td><td><?=$res1['U405'][$i]?></td>
						</tr>
						<tr>
						<td>Virtual</td><td class='parameter'>Virtual Lease & BP OK</td><td><?=$virtual?></td>
						</tr>
						<tr>
						<td>A407</td><td class='parameter'><?=$resN['AU407'][0]?></td><td><?=$res1['U407'][$i]?></td>
						</tr>
						<tr>
						<td>U408</td><td class='parameter'><?=$resN['AU408'][0]?></td><td><?=$res1['U408'][$i]?></td>
						</tr>
						<tr>
						<td>U409</td><td class='parameter'><?=$resN['AU409'][0]?></td><td><?=$res1['U409'][$i]?></td>
						</tr>
						<tr>
						<td>U710</td><td class='parameter'><?=$resN['AU710'][0]?></td><td><?=$res1['U710'][$i]?></td>
						</tr>
						<tr>
						<td>U711</td><td class='parameter'><?=$resN['AU711'][0]?></td><td><?=$res1['U711'][$i]?></td>
						</tr>
						<tr>
						<td>U352</td><td class='parameter'><?=$resN['AU352'][0]?></td><td><?=$res1['U352'][$i]?></td>
						</tr>
						</tbody>
			   	 		</table>
	    			</div>
		    		<div class="col-md-6">
						<table class="table table-striped table-hover table-condensed">
				    	<caption><h3><span class="label label-primary">Construction</span></h3></caption>
				    	<tbody>
						<tr>
						<td>U353</td><td class='parameter'><?=$resN['AU353'][0]?></td><td><?=$res1['U353'][$i]?></td>
						</tr>
						<tr>
							<td>U503</td><td class='parameter'><?=$resN['AU503'][0]?></td><td><?=$res1['U503'][$i]?></td>
						</tr>
						<tr>
							<td>U904</td><td class='parameter'><?=$resN['AU904'][0]?></td><td><?=$res1['U904'][$i]?></td>
						</tr>
						<tr>
							<td>U905</td><td class='parameter'><?=$resN['AU905'][0]?></td><td><?=$res1['U905'][$i]?></td>
						</tr>
						<tr>
							<td>U134</td><td class='parameter'><?=$resN['AU134'][0]?></td><td><?=$res1['U134'][$i]?></td>
						</tr>
						<tr>
							<td>U141</td><td class='parameter'><?=$resN['AU141'][0]?></td><td><?=$res1['U141'][$i]?></td>
						</tr>
						<tr>
							<td>U305</td><td class='parameter'><?=$resN['AU305'][0]?></td><td><?=$res1['U305'][$i]?></td>
						</tr>
						<tr>
							<td>U306</td><td class='parameter'><?=$resN['AU306'][0]?></td><td><?=$res1['U306'][$i]?></td>
						</tr>
						<tr>
							<td>U307</td><td class='parameter'><?=$resN['AU307'][0]?></td><td><?=$res1['U307'][$i]?></td>
						</tr>
						<tr>
							<td>U308</td><td class='parameter'><?=$resN['AU308'][0]?></td><td><?=$res1['U308'][$i]?></td>
						</tr>
						<tr>
							<td>U309</td><td class='parameter'><?=$resN['AU309'][0]?></td><td><?=$res1['U309'][$i]?></td>
						</tr>
						<tr>
							<td>U345</td><td class='parameter'><?=$resN['A45U345'][0]?></td><td><?=$res1['U345'][$i]?></td>
						</tr>
						<tr>
							<td>U349</td><td class='parameter'><?=$resN['A50U349'][0]?></td><td><?=$res1['U349'][$i]?></td>
						</tr>
						<tr>
							<td>U459</td><td class='parameter'><?=$resN['A59U459'][0]?></td><td><?=$res1['U459'][$i]?></td>
						</tr>
						<tr>
							<td>U680</td><td class='parameter'><?=$resN['AU680'][0]?></td><td><?=$res1['U680'][$i]?></td>
						</tr>
						<tr>
							<td>U365</td><td class='parameter'><?=$resN['A285U365'][0]?></td><td><?=$res1['U365'][$i]?></td>
						</tr>
						<tr>
							<td>U388</td><td class='parameter'><?=$resN['A288U388'][0]?></td><td><?=$res1['U388'][$i]?></td>
						</tr>
						<tr>
							<td>U361</td><td class='parameter'><?=$resN['AU361'][0]?></td><td><?=$res1['U361'][$i]?></td>
						</tr>
						<tr>
							<td>U440</td><td class='parameter'><?=$resN['A44U440'][0]?></td><td><?=$res1['U440'][$i]?></td>
						</tr>
						<tr>
							<td>U446</td><td class='parameter'><?=$resN['A46U446'][0]?></td><td><?=$res1['U446'][$i]?></td>
						</tr>
						<tr>
							<td>U363</td><td class='parameter'><?=$resN['A63U363'][0]?></td><td><?=$res1['U363'][$i]?></td>
						</tr>
						<tr>
							<td>U310</td><td class='parameter'><?=$resN['A110U310'][0]?></td><td><?=$res1['U310'][$i]?></td>
						</tr>
						<tr>
							<td>U364</td><td class='parameter'><?=$resN['A64U364'][0]?></td><td><?=$res1['U364'][$i]?></td>
						</tr>
						<tr>
							<td>U571</td><td class='parameter'><?=$resN['A71U571'][0]?></td><td><?=$res1['U571'][$i]?></td>
						</tr>
						<tr>
							<td>U391</td><td class='parameter'><?=$resN['A91U391'][0]?></td><td><?=$res1['U391'][$i]?></td>
						</tr>
						<tr>
							<td>U383</td><td class='parameter'><?=$resN['A83U383'][0]?></td><td><?=$res1['U383'][$i]?></td>
						</tr>
						<tr>
							<td>U392</td><td class='parameter'><?=$resN['A92U392'][0]?></td><td><?=$res1['U392'][$i]?></td>
						</tr>

						<tr>
							<td>U380</td><td class='parameter'><?=$resN['A80U380'][0]?></td><td><?=$res1['U380'][$i]?></td>
						</tr>
						<tr>
							<td>U712</td><td class='parameter'><?=$resN['AU712'][0]?></td><td><?=$res1['U712'][$i]?></td>
						</tr>
						<tr>
							<td>U418</td><td class='parameter'><?=$resN['A72U418'][0]?></td><td><?=$res1['U418'][$i]?></td>
						</tr>
						<tr>
							<td>U381</td><td class='parameter'><?=$resN['A81U381'][0]?></td><td><?=$res1['U381'][$i]?></td>
						</tr>
						<tr>
							<td>U825</td><td class='parameter'><?=$resN['U825'][0]?></td><td><?=$res1['U825'][$i]?></td>
						</tr>
						<tr>
							<td>U999</td><td class='parameter'><?=$resN['U999'][0]?></td><td><?=$res1['U999'][$i]?></td>
						</tr>
						<tr>
							<td>U220</td><td class='parameter'><?=$resN['U220'][0]?></td><td><?=$res1['U220'][$i]?></td>
						</tr>﻿
						<tr>
							<td>U329</td><td class='parameter'><?=$resN['U329'][0]?></td><td><?=$res1['U329'][$i]?></td>
						</tr>﻿
						</tbody>
				   	 	</table>
	    			</div>
	  			</div>
	  		</div><!-- /.modal-body-->
			<div class="modal-footer">
			    <button tyep="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content-->
	</div><!-- /.modal-dialog-->
</div><!-- /.modal-->
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
			$pakcheck="label-inverse";
		}
		if ($_POST['upgnr']==$res1['WOR_UDK'][$i]){
			$gclass="style='background-color:yellow;'";
		}else{
			$gclass="";
		}

		if($res1['CON'][$i]=='BENCHMARK' or ($res1['CON'][$i]=='' && $res1['SAC'][$i]=='BENCHMARK')){
			$ran='BENCHMARK_RAN';
		}else{
			$ran='RAN-ALU';
		}
		$ranurl=$config['sitepath_url'].'/bsds/scripts/liveranbrowser/liveranbrowser.php?dir='.substr($res1['SIT_UDK'][$i],1,2)."/".substr($res1['SIT_UDK'][$i],1,6)."/".$res1['SIT_UDK'][$i]."/".$res1['WOR_UDK'][$i]."&ran=".$ran;

		$upgrade.="
		<tr class='info'>
			 <td style='font-size:18px'>
		        <div class='btn-toolbar role='toolbar'>
		          <div class='btn-group'>
		            <button class='btn btn-default btn-xs' title='View' data-action='view'  href='#' data-toggle='modal' data-target='#UPG_".$res1['WOR_UDK'][$i]."'><span class='glyphicon glyphicon-eye-open'></span></button>
		            <button class='btn btn-default btn-xs validation' title='validation' data-siteupgnr='".$res1['WOR_UDK'][$i]."' data-nbup='UPG'><span class='glyphicon glyphicon-check'></span></button>
		          	<button class='btn btn-default btn-xs liveran' title='View files LIVE on the RAN' data-ranurl='".$ranurl."'><span class='glyphicon glyphicon-folder-open'></span></button>		
		          </div>
		        </div>";
		        if (substr_count($guard_groups, 'Admin')==1){
		          	$upgrade.="<div class='btn-toolbar role='toolbar'>
		          	<div class='btn-group'>
		          	<button class='btn btn-default btn-xs refreshN1' title='Live refresh from NET1' data-siteupgnr='".$res1['WOR_UDK'][$i]."' data-site='".$res1['SIT_UDK'][$i]."' data-nbup='UPG'><span class='glyphicon glyphicon-refresh'></span></button>
		          	</div>
		          	</div>";
		         }
		       $upgrade.="
	        </td>
			<td class='header_site ' ".$gclass."><a href='#' class='tippy' title='".$res1['WOR_NAME'][$i]."'>".$res1['SIT_UDK'][$i]."<br>".$res1['WOR_UDK'][$i]."</a></td>
			<td ".$gclass.">".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
			<td ".$gclass."><a href='#' class='tippy' title='RFINFO: ".$res1['WOR_HSDPA_CLUSTER'][$i]."'>".$res1['WOR_LKP_WCO_CODE'][$i]."</a></td>
			<td><a href='#' class='tippy' title='PO ACQ: ".$res1['U501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."<br>".$WIPA."</a></td>";

			if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'SH')){
				$upgrade.="<td><a href='#' class='tippy' title='Sublease signed'>".$res1['U100'][$i]."<br>U100</a><br></td>";
				$upgrade.="<td><a href='#' class='tippy' title='BP newcomer received'>".$res1['U104'][$i]."<br>U104</a><br></td>";
			}else{
				$upgrade.="<td><a href='#' class='tippy' title='U709'>".$res1['U709'][$i]."</a></td>";
				$upgrade.="<td><a href='#' class='tippy' title='U405'>".$res1['U405'][$i]."</a></td>";
			}
		$upgrade.="
			<td><a href='#' class='tippy' title='PO CON: ".$res1['U503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."<br>".$WIPC."</a></td>
			<td><a href='#' class='tippy' title='U353' rel='popover' data-content='".$res1['U353_NOTES'][$i]."' data-original-title='Budget info'>".$res1['U353'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U459'>".$res1['U459'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U571' rel='popover' data-content='".$res1['U571_ESTIM'][$i]."' data-original-title='Integration forecast'>".$res1['U571'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U363'>".$res1['U363'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U391'>".$res1['U391'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U380'>".$res1['U380'][$i]."</a><br><a href='#' class='tippy' title='U825'>".$res1['U825'][$i]."</a></td>
			<td><a href='#' rel='popover' data-placement='left' data-content='".
			"A503: ". $res1['U503'][$i]."<br>U725: ".$res1['U725'][$i]."<br>U134: ".$res1['U134'][$i]."<br>U141: ".$res1['U141'][$i] 
			.'<br>U102: '.$res1['U102'][$i]."<br>U345: ".$res1['U345'][$i]."<br>U349: ".$res1['U349'][$i]."<br>U459: ".$res1['U459'][$i]."<br>U363: ".$res1['U363'][$i]
			.'<br>U110: '.$res1['U110'][$i]."<br>U364: ".$res1['U364'][$i]."<br>U365: ".$res1['U365'][$i]."<br>U388: ".$res1['U388'][$i]
			.'<br>U571: '.$res1['U571'][$i]."<br>U391: ".$res1['U391'][$i]."<br>U383: ".$res1['U383'][$i]."<br>U309: ".$res1['U392'][$i]
			.'<br>U392: '.$res1['U392'][$i]."<br>U380: ".$res1['U380'][$i]."<br>U712: ".$res1['U712'][$i]
			."' title='U418 PAC CHECK'>".$pac."</a></td>
			<td><a href='#' class='tippy' title='U381'>".$res1['U381'][$i]."</a></td>
			</tr> ";
	}else{
		$upgrade_history.="
		<tr class='UPG_hist_data warning' style='display:none;'>
			<td style='font-size:18px'>
		        <div class='btn-toolbar' role='toolbar'>
		          <div class='btn-group'>
		            <button class='btn btn-default btn-xs' title='View' data-action='view'  href='#' data-toggle='modal' data-target='#UPG_".$res1['WOR_UDK'][$i]."'><span class='glyphicon glyphicon-eye-open'></span></button>
		          </div>
		        </div>
	        </td>
			<td class='header_site'><a href='#' class='tippy' title='".$res1['WOR_NAME'][$i]."'>".$res1['SIT_UDK'][$i]."<br>".$res1['WOR_UDK'][$i]."</a></td>
			<td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
			<td><a href='#' rel='popover' data-content='".$res1['WOR_HSDPA_CLUSTER'][$i]."' data-original-title='RFINFO'>".$res1['WOR_LKP_WCO_CODE'][$i]."</a></td>
			<td><a href='#' class='tippy' title='PO ACQ: ".$res1['U501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."</a></td>";

		if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'SH')){
			$upgrade_history.="<td><a href='#' class='tippy' title='Sublease signed'>".$res1['U100'][$i]."<br>U100</a><br></td>";
			$upgrade_history.="<td><a href='#' class='tippy' title='BP newcomer received'>".$res1['U104'][$i]."<br>U104</a><br></td>";
		}else{
			$upgrade_history.="<td><a href='#' title='U709'>".$res1['U709'][$i]."</a></td>";
			$upgrade_history.="<td><a href='#' title='U405'>".$res1['U405'][$i]."</a></td>";
		}

		$upgrade_history.="
			<td><a href='#' class='tippy' title='PO CON: ".$res1['U503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."</a></td>
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
?>

<div class="pull-left"><h3><span class="label label-success">NEWBUILDS</span></h3></div>
<div class="pull-right"><h3>
	<button type="button" class="btn btn-success btn-xs history" id="NB_hist" data-clone='clone_NET1UPG<?=$_POST['siteID']?>'>
	<span class="glyphicon glyphicon-eye-open"></span>
	</button>
</h3>
</div>
<div class="clearfix"></div>
	<div class="table-responsive table-responsive-force">
	<table class="table table-striped table-hover table-condensed" id="NET1NB<?=$_POST['siteID']?>" style="table-layout: fixed;">
		<colgroup>
			<col style="width: 80px">
		    <col style="width: 80px">
		    <col style="width: 30px">
		    <col style="width: 110px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
    	</colgroup>
	  	<thead>
	    <tr>
	    	<th>&nbsp;</th>
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
	 	<tr class='NB_hist_data warning' style='display:none;'>
	 		<td colspan="15" style="text-align:center;"><span class="label label-default">HISTORY</span></td>
	 	</tr>
	 	<?php echo $newbuild_history; ?>
	  	</tbody>
	</table>
	</div>

<table>
<tr>
    <td><i>Last:</i></td><td>
    <?php if($BSDSrefresh['ACTION_ALL_NEW_NET1']=="Downloading"){ ?>
    <span class="label label-danger">Downloading data</span>
    <?php }else if($BSDSrefresh['ACTION_ALL_NEW_NET1']=="Importing"){ ?>
    <span class="label label-danger">Updating live data</span>
    <?php }else{ ?>
    <span class="label label-default" rel="tooltip" data-placement="bottom" title="Runs from 6:05 till 20:35 every 30 min."><?=substr($BSDSrefresh['DATE_ALL_UPG'],11,8)?></span>
    <?php } ?>
    </td>
    <td><i>Next:</i></td>
    <td><span class="label label-default"><?=$BSDSrefresh['NEXTRUN_ALL_NEW']?></span></td>
</tr>
</table>

<div class="pull-left"><h3><span class="label label-info">UPGRADES</span></h3></div>
<div class="pull-right"><h3>
	<button type="button" class="btn btn-info btn-xs history" id="UPG_hist">
	<span class="glyphicon glyphicon-eye-open"></span>
	</button>
</h3>
</div>

<div class="clearfix"></div>

	<div class="table-responsive table-responsive-force">
	<table class="table table-striped table-hover table-condensed" id="NET1UPG<?=$_POST['siteID']?>" style="table-layout: fixed;">
	  	<colgroup>
	  	 	<col style="width: 80px">
		    <col style="width: 80px">
		    <col style="width: 30px">
		    <col style="width: 110px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
    	</colgroup>
	  	<thead>
	    <tr>
	    	<th>&nbsp;</th>
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
	 	<tr class='UPG_hist_data warning' style='display:none;'>
	 		<td colspan="15" style="text-align:center;"><span class="label label-default">HISTORY</span></td>
	 	</tr>
	 	<?php echo $upgrade_history; ?>
	  	</tbody>
	</table>
	</div>

<table>
<tr>
    <td><i>Last:</i></td><td>
	    <?php if($BSDSrefresh['ACTION_ALL_UPG_NET1']=="Downloading"){ ?>
	    <span class="label label-danger">Downloading data</span>
	    <?php }else if($BSDSrefresh['ACTION_ALL_UPG_NET1']=="Importing"){ ?>
	    <span class="label label-danger">Updating live data</span>
	    <?php }else{ ?>
	    <span class="label label-default" rel="tooltip" data-placement="bottom" title="Runs from 6:00 till 20:35 every 30 min."><?=substr($BSDSrefresh['DATE_ALL_UPG'],11,8)?></span>
	    <?php } ?>
    </td>
    <td><i>Next:</i></td>
    <td><span class="label label-default"><?=$BSDSrefresh['NEXTRUN_ALL_UPG']?></span></td>
</tr>
</table>
<?php
}
?>
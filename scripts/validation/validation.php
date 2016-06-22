<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("../general_info/general_info_procedures.php");
require_once("validation_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$commandline='no';
$insertindb='no';

if ($_POST['siteupgnr']){
	$BSDSrefresh=get_BSDSrefresh();

	$siteupgnr=$_POST['siteupgnr'];

	$query="select * FROM VALIDATION_ADMIN ORDER BY GROUPNAME,CHECKORDER ASC";
	//echo $query."<br>";
	$stmtVALA = parse_exec_fetch($conn_Infobase, $query, $error_str, $resVALA);
	if (!$stmtVALA){
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmtVALA);
		$amount_VAL=count($resVALA['CHECKTYPE']);
	}

	//Here we we get the naming of the milestones
	
	for ($i = 0; $i <$amount_VAL; $i++) { 
 		if ($resVALA['TOCHECK'][$i]=='MS'){
 			$MS=$resVALA['CHECKTYPE'][$i];
			$milestones.=$MS.",";
		}
	}

	$query="select N1_CANDIDATE, N1_UPGNR, N1_SITEID, RA.RAFID, RADIO_FUND,BUFFER, PARTNER_DESIGN, BP_NEEDED, MA.BSDSKEY AS MA_BSDSKEY, SH.BSDSKEY as SN_BSDSKEY, 
	SH.BSDSBOBREFRESH AS SN_BSDSBOBREFRESH, TYPE, N1_CANDIDATE,N1_NBUP,N1_SAC,N1_SAC,N1_CON,N1_PRO,A72U418,AU407,AU353,IB_SAC, IB_CON
	".substr($milestones, 0,-1).",AU680,AU353 from BSDS_RAFV2 RA 
	LEFT JOIN MASTER_REPORT MA on RA.RAFID=MA.IB_RAFID 
	LEFT JOIN SN_SHIPPINGLIST SH on RA.RAFID=SH.RAFID
	WHERE NET1_LINK ='".$siteupgnr."'";
	//echo $query."<br>";
	$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
	if (!$stmt3){
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt3);
		$amount_RAF=count($res3['RAFID']);
	}

	if (($res3['AU352'][0]=="" && $res3['BUFFER'][0]!="1")){ //buffer sites skip acquisition
		$acq_active="in active";
	}else if($res3['PARTNER_DESIGN'][0]=='NOT OK'){
		$design_active="in active";
	}else{
		$con_active="in active";
	}
}


/* HERE WE DO THE FILE VALIDATIONS */

$ymd1 = DateTime::createFromFormat('d/m/Y', $res3['A72U418'][0]);
$ymd2 = DateTime::createFromFormat('d/m/Y', '01/01/2015');

if (($res3['N1_CON'][0]=='BENCHMARK' && $ymd1>=$ymd2) or ($res3['A72U418'][0]=='' && $res3['N1_CON'][0]=='BENCHMARK')){
	$ranloc=$config['ranfolderBENCH'];
	$ran='BENCHMARK_RAN';
}else if ($res3['IB_SAC'][0]=='M4C' or $res3['IB_CON'][0]=='M4C'){
	$ranloc=$config['ranfolderM4C'];
	$ran='M4C_RAN';
}elseif ($res3['N1_CON'][0]=='TECHM' or $res3['N1_WIPC'][0]=='ALU' or $ymd1<$ymd2){
	$ranloc=$config['ranfolder'];
	$ran='RAN-ALU';

}else if (($res3['IB_SAC'][0]=='BENCHMARK' && $ymd1>=$ymd2) or ($res3['A72U418'][0]=='' && $res3['IB_SAC'][0]=='BENCHMARK')){
	$ranloc=$config['ranfolderBENCH'];
	$ran='BENCHMARK_RAN';
}elseif ($res3['IB_SAC'][0]=='TECHM' or $res3['N1_WIPA'][0]=='ALU' or $ymd1<$ymd2){
	$ranloc=$config['ranfolder'];
	$ran='RAN-ALU';
}
//echo $ran;
$N1_PRO=$res3['N1_PRO'][0];

$query="select N1_SITEID from MASTER_REPORT WHERE N1_CANDIDATE = '".$res3['N1_CANDIDATE'][0]."' AND N1_STATUS='IS' AND (N1_NBUP='NB' OR N1_NBUP='NB REPL')";
//echo $query."<br>";
$stmtMA = parse_exec_fetch($conn_Infobase, $query, $error_str, $resMA);
if (!$stmtMA) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmtMA);
	//if (substr($resMA['N1_SITEID'][0],0,1)=="_"){
		$folderSITEID=substr($resMA['N1_SITEID'][0],1,6);
	//}else{
	//	$folderSITEID=$resMA['N1_SITEID'][0];
	//}
}

if ($ran=='M4C_RAN' && $res3['N1_NBUP'][0]=='UPG'){
	$ranSubfolder=substr($res3['N1_SITEID'][0],1,2)."/".substr($res3['N1_SITEID'][0],1,6)."/".$res3['N1_SITEID'][0]."/".$res3['N1_CANDIDATE'][0]."/".$res3['N1_UPGNR'][0];
	$directory = $ranloc.$ranSubfolder;
		
}else{
	if ($res3['N1_NBUP'][0]=='NB' or $res3['N1_NBUP'][0]=='NB REPL'){ 
		$ranSubfolder=substr($res3['N1_CANDIDATE'][0],1,2)."/".$folderSITEID."/".$res3['N1_CANDIDATE'][0]."/NB".$res3['N1_CANDIDATE'][0];
		$directory = $ranloc.$ranSubfolder;
	}else if ($res3['N1_NBUP'][0]=='UPG'){ 
		$ranSubfolder=substr($res3['N1_CANDIDATE'][0],1,2)."/".$folderSITEID."/".$res3['N1_CANDIDATE'][0]."/".$siteupgnr;
		$directory = $ranloc.$ranSubfolder;
	}
}

	//We make sure that the filechecks are set to 0
	for ($i = 0; $i <$amount_VAL; $i++){ 
		if ($resVALA['TOCHECK'][$i]=='FILE'){
			$received=$resVALA['CHECKTYPE'][$i]."_received";
			$$received=0;
		}
	}

	require("filevalidations.php");

	/*
	if ($res3['N1_NBUP'][0]=='UPG'){ //for UPG we always check in general folder for some files
		$ran='RAN-ALU';
		$directory = $config['ranfolder'].substr($res3['N1_CANDIDATE'][0],1,2)."/".$folderSITEID."/".$res3['N1_CANDIDATE'][0]."/01. H&S";
		require("filevalidations.php");
	}*/

	//OVERRULING
	$query="SELECT * FROM VALIDATION_OVERRULE WHERE SITEUPGNR='".$siteupgnr."'";

	$stmtval = parse_exec_fetch($conn_Infobase, $query, $error_str, $resval);
	if (!$stmtval){
	    die_silently($conn_Infobase, $error_str);
	    exit;
	} else {
	    OCIFreeStatement($stmtval);
	    $amount_overrule=count($resval['SITEUPGNR']);
	}
	for ($i = 0; $i <$amount_overrule; $i++) {
		$checktype=$resval['CHECKTYPE'][$i];
		
		$valtype2=$checktype."_reason";
		$check=explode("_", $checktype);
		$valtype=$check[0]."_received";
		$$valtype='7';
		$$valtype2=$resval['REASON'][$i];
		//echo $valtype2."=".$resval['REASON'][$i];
		//echo $valtype."=".$$valtype;
	}  

	//Here we get the naming/description of the milestones
	$query="select N1_CANDIDATE,N1_NBUP,N1_SAC,".substr($milestones, 0,-1).",AU680,AU353 from VW_MASTER_REPORT WHERE N1_SITEID ='N1_SITEID'";
	//echo $query."<br>";
	$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
	if (!$stmt2) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt2);
	}

	for ($i = 0; $i <$amount_VAL; $i++) { 
		$checktype=$resVALA['CHECKTYPE'][$i]."_".$resVALA['GROUPNAME'][$i];
		$received_reason=$resVALA['CHECKTYPE'][$i]."_".$resVALA['GROUPNAME'][$i]."_reason";

		//echo $resVALA['CHECKTYPE'][$i];
		if ($resVALA['TOCHECK'][$i]=='FILE'){
			$GROUP=$resVALA['GROUPNAME'][$i]."_out"; //= ACQ_out
			$received=$resVALA['CHECKTYPE'][$i]."_received";
			$received_fullpath=$resVALA['CHECKTYPE'][$i]."_received_fullpath";
			$filedate=$resVALA['CHECKTYPE'][$i]."_filedate";
			$received_filename=$resVALA['CHECKTYPE'][$i]."_received_filename";
			
			//echo $received_reason."=".$$received_reason."<br>";
			$received_ran=$resVALA['CHECKTYPE'][$i]."_received_ran";
			
			$$GROUP.=generatebutton($siteupgnr,$checktype,$resVALA['FULLFILENAME'][$i],$$received,$$filedate,$$received_fullpath,$$received_filename,$$received_ran,$$received_reason);

		}else if ($resVALA['TOCHECK'][$i]=='MS'){

			$MS=$resVALA['CHECKTYPE'][$i];
			//$milestones.=$MS.",";
			$GROUP=$resVALA['GROUPNAME'][$i]."_MSout";

			if ($resVALA['RULE'][$i]=='!UPG'){
				if ($res3['N1_NBUP'][0]!='UPG'){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} //else do nothing
			}else if ($resVALA['RULE'][$i]=='UPG'){
				if ($res3['N1_NBUP'][0]=='UPG'){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} //else do nothing
			}else if ($resVALA['RULE'][$i]=='G9'){
				if (strpos($res3['RADIO_FUND'][0], 'G9')!==false){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} //else do nothing
			}else if ($resVALA['RULE'][$i]=='G18'){
				if (strpos($res3['RADIO_FUND'][0], 'G18')!==false){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} //else do nothing
			}else if ($resVALA['RULE'][$i]=='U9'){
				if (strpos($res3['RADIO_FUND'][0], 'U9')!==false){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} //else do nothing
			}else if ($resVALA['RULE'][$i]=='U21'){
				if (strpos($res3['RADIO_FUND'][0], 'U21')!==false){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} //else do nothing
			}else if ($resVALA['RULE'][$i]=='L8'){
				if (strpos($res3['RADIO_FUND'][0], 'L8')!==false){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} //else do nothing
			}else if ($resVALA['RULE'][$i]=='L18'){
				if (strpos($res3['RADIO_FUND'][0], 'L18')!==false){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} //else do nothing
			}else if ($resVALA['RULE'][$i]=='L26'){
				if (strpos($res3['RADIO_FUND'][0], 'L26')!==false){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} //else do nothing
			}else if ($resVALA['RULE'][$i]=="ACQ_PARTNER!='BASE' && ACQ_PARTNER!='KPNGB'"){
				if ($res3['IB_SAC'][0]!='BASE' && $res3['IB_SAC'][0]!='KPNGB'){
					$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
				} 
			/*}else
				if (($res3['A34U334'][0]=$res3['A41U341'][0] AND $res3['A34U334'][0]!='') or $res3['N1_SAC'][0]=='KPNGB' or $res3['N1_SAC'][0]=='BASE' or $res3['AU680'][0]!=''){ 
					$checkCON++;
					$AU680=1;
				}else{ 
					$AU680=0;
				}*/
			}else{
				$$GROUP.=generateMilestone2($siteupgnr,$checktype,$MS,$res2[$MS][0],$res3[$MS][0],$$received_reason);
			}	
		}
	}

?>
<div class="well">
	<div class="row">
	  <div class="col-md-3">
	<?php if ($amount_RAF>1){
		echo '<span class="label label-danger">To many RAF\'s for same activity!</span><br>';
	}else if ($amount_RAF==0){
		echo '<span class="label label-danger">No RAF available for this activituy!</span><br>';
	}else if ($amount_RAF==1){
		echo '<span class="label label-success">RAFID: '.$res3['RAFID'][0].'</span><br>';
	}
	?>
	  </div>
	  <div class="col-md-3">
	<?php
	echo '<span class="label label-default">SITE FUNDED on: </b>'.$res3['AU353'][0].'</span>';
	?>
	  </div>
	  <div class="col-md-3">
	  	<span class="label label-default">FUNDED TECHNOS: <?=$res3['RADIO_FUND'][0]?></span>
	  </div>
	  <div class="col-md-3 pull-right">
	 		<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
	          <button type="button" class="btn btn-info btn-xs">
				  <span class="glyphicon glyphicon-eye-open"></span> Files info
			   </button>
	        </a>
	  </div>
	</div>
</div>

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
      	<?=$folder_out?>
        <?=$files?>
      </div>
    </div>
</div>

 <!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="<?=$acq_active?>"><a href="#acquisition" aria-controls="acquisition" role="tab" data-toggle="tab">ACQUISITION</a></li>
    <li role="presentation" class="<?=$design_active?>"><a href="#design" aria-controls="design" role="tab" data-toggle="tab">DESIGN</a></li>
    <li role="presentation" class="<?=$con_active?>"><a href="#construction" aria-controls="construction" role="tab" data-toggle="tab">CONSTRUCTION</a></li>
</ul>
  <!-- Tab panes -->
<div class="tab-content">
	<div role="tabpanel" class="tab-pane fade <?=$acq_active?>" id="acquisition">
	  <?php if ($res3['BUFFER'][0]!="1"){ ?>
	  <div class="row">
	  	<div class="col-md-5">
	  		<table class="table table-condensed">
			<tbody>
				<tr><td colspan="2" align="center"><h3>Files Check</h3></td></tr>
				<?php echo $ACQ_out; ?>
			</tbody>
			</table>
	  	</div>
	  	<div class="col-md-7">
	  		<table class="table table-condensed">
			<tbody>
				<tr><td colspan="3" align="center"><h3>MS Check</h3></td></tr>
				<?php echo $ACQ_MSout; ?>
			</tbody>
			</table>
	  	</div>
	  </div>
	  <?php }else{ ?>
	  <br><p class="text-center"><h3><span class="label label-warning">Acquisition has been skipped!</span></h3></p><br>
	  <?php } ?>
	</div>
	<div role="tabpanel" class="tab-pane fade <?=$design_active?>" id="design">
	  <?php if ($res3['PARTNER_DESIGN'][0]!="NA"){ ?>
	  <div class="row">
	  	<div class="col-md-5">
	  		<table class="table table-condensed">
			<tbody>
				<tr><td colspan="2" align="center"><h3>Files Check</h3></td></tr>
				<?php echo $DESIGN_out; ?>
			</tbody>
			</table>
	  	</div>
	  	<div class="col-md-7">
	  		<table class="table table-condensed">
			<tbody>
				<tr><td colspan="3" align="center"><h3>MS Check</h3></td></tr>
				<?php echo $DESIGN_MSout; ?>
			</tbody>
			</table>
	  	</div>
	  </div>
	  <div class="row">
	  	<div class="col-md-6">
	  		<table class="table table-condensed">
			<tbody>
			<tr><td colspan="2" align="center"><h3>BSDS Check</h3></td></tr>
			<td>BSDS ID</td>
			<?php if($res3['BSDSKEY'][0]!=''){ ?>
			<td class="success"><?=$res3['MA_BSDSKEY'][0]?></td>
			<?php }else{ ?>
			<td class="danger">NOT OK</td>
			<?php } ?>
			</tbody>
			</table>
		</div>
		<div class="col-md-6">
	  		<table class="table table-condensed">
			<tbody>
			<tr><td colspan="2" align="center"><h3>SN Check</h3></td></tr>
			<td>SN ID</td>
			<?php if($res3['BSDSKEY'][0]!=''){ ?>
			<td class="success"><?=$res3['SN_BSDSKEY'][0]?> <?=$res3['SN_BSDSBOBREFRESH'][0]?></td>
			<?php }else{ ?>
			<td class="danger">NOT OK</td>
			<?php } ?>
			</tbody>
			</table>
		</div>
	  </div>
	  <?php }else{ ?>
	  <br><p class="text-center"><h3><span class="label label-warning">NO DESIGN PHASE FOR <?=$res3['TYPE'][0]?>!</span></h3></p><br>
	  <?php } ?>
	</div>
    <div role="tabpanel" class="tab-pane fade <?=$con_active?>" id="construction">
	  <div class="row">
	  	<div class="col-md-5">
			<table class="table table-condensed">
			<tbody>
				<tr><td colspan="2" align="center"><h3>PAC File Check</h3></td></tr>
				<?php echo $CON_PAC_out; ?>
				<tr><td colspan="2" align="center"><h3>FAC File Check</h3></td></tr>
				<?php echo $CON_FAC_out; ?>
			</tbody>
			</table>
	  	</div>
	    <div class="col-md-7">
			<table class="table table-condensed">
			<tr><td colspan="3" align="center"><h3>PAC MS Check</h3></td></tr>
			<tbody>
				<?php echo $CON_PAC_MSout; ?>
				<?php
				if (($res2['A34U334'][0]=$res2['A41U341'][0] AND $res2['A34U334'][0]!='') or $res2['N1_SAC'][0]=='KPNGB' or $res2['N1_SAC'][0]=='BASE'){ 
					?>
					<tr>
						<td>AU680</td>
						<td><?=$res2['AU680'][0]?></td>
						<td class="success">SAC=KPNG or A34U334=A41U341</td>
					</tr>
					<?php
				}else{
					echo generateMilestone2($siteupgnr,'AU680',$res2['AU680'][0],$res3['AU680'][0],$AU680_received_reason);	
				}	
				?>
				<tr><td colspan="3" align="center"><h3>FAC MS Check</h3></td></tr>
				<?php echo $CON_FAC_MSout; ?>
			</tbody>
			</table>
		  </div>
		</div>
	</div>
  </div>
</div>

<script type="text/javascript">
$(document).ready( function(){
	$("#savemodal").data("module","signoff");
});
</script>

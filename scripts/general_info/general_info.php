<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$query="select idname,name, LOGNODEPK, LOGNODETYPEFK, ADDRESSFK from ".$config['table_asset_lognode']."
    	where name like '%".$_POST['candidate']."%' AND  LOGNODETYPEFK IN('5105','5108','11008','1222','1214','11002')
    	order by  IDNAME ASC, LOGNODEPK DESC";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt){
	die_silently($conn_Infobase, $error_str);
	exit;
}else{
	OCIFreeStatement($stmt);
	$amount_sites1=count($res1['LOGNODEPK']);
}
$j=0;

if ($amount_sites1!=0){

	for ($i=0;$i<$amount_sites1;$i++){
		//echo "===>".$res1["IDNAME"][$i]."<br>";
		$firstletter=substr($res1["IDNAME"][$i],0,1);
		$firsttwoletter=substr($res1["IDNAME"][$i],0,2);

		if($res1["NAME"][$i]=="NOVAL"){
			echo "<div class='alert alert-danger'><b>ASSET ERROR!</b><br>Please correct the candidate letter (first name) for site ".$res1["IDNAME"][$i]." => ".$res1["NAME"][$i]." or create site in Asset.</div>";
			die;
		}

		if(($firstletter!="M" && $firstletter!="S" && $firstletter!="T") or $firsttwoletter=="MT"){
			$newsiteID= substr($res1["IDNAME"][$i],0,7);
			//echo "-----".$newsiteID." ".substr($newsiteID,-1,1)."<br>";
			if (strlen($newsiteID)==7 && substr($newsiteID,-1,1)!='0'){
				$newsiteID= substr($res1["IDNAME"][$i],0,7);
			}else{
				$newsiteID= substr($res1["IDNAME"][$i],0,6);
			}
			$candidate=$res1["NAME"][$i];
			//echo $candidate."-".substr($candidate,-1,1)."-".strlen($candidate)."<br>";
			if (substr($candidate,-1,1)!='0' && strlen($candidate)==7){ //BW4550A
				$candidate=substr($res1["NAME"][$i],0,7);
			}else if (substr($candidate,-1,1)!='0' && strlen($candidate)==8){ //MBX3817C
				$candidate=substr($res1["NAME"][$i],0,8);
			}else if (substr($candidate,-1,1)!='0' && strlen($candidate)==10){  //MBX3817C01
				$candidate=substr($res1["NAME"][$i],0,8);
			}else if (substr($candidate,-1,1)!='0' && strlen($candidate)==9){ //BW4550A01
				$candidate=substr($res1["NAME"][$i],0,7);
			}else{
				echo "<div class='alert alert-danger'>ERROR IN ASSET!<br>(2) Please correct the candidate letter (first name) for site ".$res1["IDNAME"][$i]." => ".$res1["NAME"][$i]."</div>";
				die;
			}

		}else if($firstletter=="M" or $firstletter=="S" or $firstletter=="T"){

			$newsiteID= substr($res1["IDNAME"][$i],0,8);
			if (strlen($newsiteID)==8){
				$newsiteID= substr($newsiteID,0,8);
			}else{
				$newsiteID= substr($newsiteID,0,-1);
			}
			$candidate=$res1["NAME"][$i];
			//echo $candidate."-".substr($candidate,-1,1);
			if (substr($candidate,-1,1)!='0' && strlen($candidate)==8){ //MBX3817C
				$candidate=substr($res1["NAME"][$i],0,8);
			}else if (substr($candidate,-1,1)!='0' && strlen($candidate)==10){ //MBX3817C01
				$candidate=substr($res1["NAME"][$i],0,8);
			}else{
				echo "<div class='alert alert-danger'>ERROR IN ASSET!<br>(3) Please correct the candidate letter (first name) for site ".$res1["IDNAME"][$i]." => ".$res1["NAME"][$i]."</div>";
				die;
			}
		}

		//we check the last letter of the candidate
		 if (!preg_match("/^[a-zA-Z]$/", substr($candidate,-1,1))) {
		    echo "<div class='alert alert-danger'>ERROR IN ASSET! => missing candidate letter!<br>(4) Please correct the candidate letter (first name) for site ".$res1["IDNAME"][$i]." => ".$res1["NAME"][$i]."</div>";
			die;
		 }
		//echo $newsiteID." ".$candidate."<br>";

		if ($res1["LOGNODETYPEFK"][$i]=='1214'){ //U9 U21    				
			if (substr($res1["IDNAME"][$i],-2,2)=='02'){
				$lognode['U9']=$res1["LOGNODEPK"][$i];
				$techno.='U9+';
			}
			if (substr($res1["IDNAME"][$i],-2,2)=='01'){
				$lognode['U21']=$res1["LOGNODEPK"][$i];
				$techno.='U21+';
			}
			$siteID=substr($res1["IDNAME"][$i],0,-2);
			$candidate=substr($res1["NAME"][$i],0,-2);
		}else if ($res1["LOGNODETYPEFK"][$i]=='5105'){
			$query = "select * from BSDSINFO2 WHERE SITEKEY='".$res1["LOGNODEPK"][$i]."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%900%'";
			$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
			if (!$stmt3){
				die_silently($conn_Infobase, $error_str);
				exit;
			}else{
				OCIFreeStatement($stmt3);
				$amount_GSM900=count($res3['ANTENNATYPE']);
			}
			$query = "select * from BSDSINFO2 WHERE SITEKEY='".$res1["LOGNODEPK"][$i]."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%1800%'";
			$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
			if (!$stmt3){
				die_silently($conn_Infobase, $error_str);
				exit;
			}else{
				OCIFreeStatement($stmt3);
				$amount_GSM1800=count($res3['ANTENNATYPE']);
			}
			if($amount_GSM900!=0 && $amount_GSM1800!=0){
				$techno.='G9+G18+';
			}else if($amount_GSM900!=0){
				$techno.='G9+';
			}else if($amount_GSM1800!=0){
				$techno.='G18+';
			}

			$lognode['G18']=$res1["LOGNODEPK"][$i];
			$lognode['G9']=$res1["LOGNODEPK"][$i];
			$siteID=$res1["IDNAME"][$i];
			$candidate=$res1["NAME"][$i];
		}else if ($res1["LOGNODETYPEFK"][$i]=='5108'){ //Repeater GSM G9/G18
			$query = "select DONOR from ".$config['table_asset_repeaters']."
					 where REPEATER LIKE '%".$_POST['searchk']."%'";
			//echo $query;
			$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
			if (!$stmt3){
				die_silently($conn_Infobase, $error_str);
				exit;
			}else{
				OCIFreeStatement($stmt3);
				$amount_DONOR=count($res3['DONOR']);
				if($amount_DONOR!=0){
					$donor=$res3["DONOR"][0];							
				}
			}
			$query = "select * from BSDSINFO2 WHERE SECTORID='".$donor."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%900%'";
			//echo $query;
			$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
			if (!$stmt3){
				die_silently($conn_Infobase, $error_str);
				exit;
			}else{
				OCIFreeStatement($stmt3);
				$amount_GSM900=count($res3['ANTENNATYPE']);
			}
			$query = "select * from BSDSINFO2 WHERE SECTORID='".$donor."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%1800%'";
			//echo $query;
			$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
			if (!$stmt3){
				die_silently($conn_Infobase, $error_str);
				exit;
			}else{
				OCIFreeStatement($stmt3);
				$amount_GSM1800=count($res3['ANTENNATYPE']);
			}
			if($amount_GSM900!=0 && $amount_GSM1800!=0){
					$techno.='G9 REP+G18 REP+';
			}else if($amount_GSM900!=0){
					$techno.='G9 REP+';
			}else if($amount_GSM1800!=0){
					$techno.='G18 REP+';
			}
			$lognode['G9']=$res1["LOGNODEPK"][$i];
			$lognode['G18']=$res1["LOGNODEPK"][$i];
			$siteID=$res1["IDNAME"][$i];
			$candidate=$res1["NAME"][$i];

		}else if ($res1["LOGNODETYPEFK"][$i]=='1222'){ //Repeater UMTS U9/U21
			if (substr($res1["IDNAME"][$i],-2,2)=='01'){
				$techno.='U21 REP+';
				$lognodeID_UMTS1800=$res1["LOGNODEPK"][$i];
			}
			if (substr($res1["IDNAME"][$i],-2,2)=='02'){
				$techno.='U9 REP+';
				$lognode['U9']=$res1["LOGNODEPK"][$i];
			}
			$siteID=substr($res1["IDNAME"][$i],0,-2);
			$candidate=substr($res1["NAME"][$i],0,-2);
		}else if ($res1["LOGNODETYPEFK"][$i]=='11008'){
			if (substr($res1["IDNAME"][$i],-2,2)=='05'){
				$techno.='L18 REP+';
				$lognode['L18']=$res1["LOGNODEPK"][$i];
			}
			if (substr($res1["IDNAME"][$i],-2,2)=='06'){
				$techno.='L26 REP+';
				$lognode['L26']=$res1["LOGNODEPK"][$i];
			}
			if (substr($res1["IDNAME"][$i],-2,2)=='07'){
				$techno.='L8 REP+';
				$lognode['L8']=$res1["LOGNODEPK"][$i];
			}
			$siteID=substr($res1["IDNAME"][$i],0,-2);
			$candidate=substr($res1["NAME"][$i],0,-2);
		}else if ($res1["LOGNODETYPEFK"][$i]=='11002'){
			if (substr($res1["IDNAME"][$i],-2,2)=='05'){
				$techno.='L18+';
				$lognode['L18']=$res1["LOGNODEPK"][$i];
			}
			if (substr($res1["IDNAME"][$i],-2,2)=='06'){
				$techno.='L26+';
				$lognode['L26']=$res1["LOGNODEPK"][$i];
			}
			if (substr($res1["IDNAME"][$i],-2,2)=='07'){
				$techno.='L8+';
				$lognode['L8']=$res1["LOGNODEPK"][$i];
			}
			$siteID=substr($res1["IDNAME"][$i],0,-2);
			$candidate=substr($res1["NAME"][$i],0,-2);
		}

	}
}else{
	echo "<div class='alert alert-danger'><b>CANDIDATE IS NOT DEFINED IN ASSET</b></div>";
}

include("general_info_procedures.php");
include("general_info_data.php");
?>

<div class="well well-small" style="border-color:#428bca;">
	<div class="row">
 	 	<div class="col-md-4">
			<table border='0'>
			<tbody>
			<tr>
				<td><b>Site Identity:</b></td>
				<td><span class="label label-info" style="font-size:16px;"><?=$_POST['siteid']?></span></td>
			</tr>
			<tr>
				<td><b>Candidate (Firstname Asset):</b></td>
				<td><span class="label label-default" style="font-size:16px;"><?=$_POST['candidate']?></span></td>
			</tr>
			<?php if($_POST['upgnr']!=''){ ?>
			<tr>
				<td><b>N1 UPGNR:</b></td>
				<td><span class="label label-default" style="font-size:16px;"><?=$_POST['upgnr']?></span></td>
			</tr>
			<?php }
			if ($_POST['donor']){ ?>
			<tr>
				<td><b>Donor site:</b></td>
				<td><font color="red"><?=$_POST['donor']?></font></td>
			</tr>
			<?php 
			} ?>
			<tr>
				<td><b>Techno's defined in Asset:</b></td>
				<td><span class="label label-warning" style="font-size:14px;"><?=substr($techno,0,-1)?></span></td>
			</tr>
			</tbody>
			</table>
		</div>
		<div class="col-md-6">
			<table border='0'>
			<tbody>
			<tr>
				<td valign="top"><b>Address:</b></td>
				<td><?=$address?></td>
			</tr>
			<tr>
				<td><b>Class code:</b></td>
				<td><?=$Classcode?></td>
			</tr>
			<tr>
				<td><b>X - Y coordinates:</b></td>
				<td><?=$coor['longitude']?> - <?=$coor['latitude']?></td>
			</tr>
			</tbody>
			</table>
		</div>
		<div class="col-md-2">
			<button id="newbsds" type="button" class='btn btn-sm btn-warning BSDS_new' data-upgnr='<?=$_POST['upgnr']?>' data-nbup='<?=$_POST['nbup']?>' data-rafid='<?=$_POST['rafid']?>' data-siteid='<?=$_POST['siteid']?>' data-candidate='<?=$_POST['candidate']?>'><span class="glyphicon glyphicon-plus-sign"></span> Add a new BSDS</a>
		</div>
	</div>
</div>
<?php
echo $pop_data; 
echo $output_form;
?>


	<table class="table table-hover">
	<thead>
	<tr>
		<th>BSDSID</th>
		<th><span class='glyphicon glyphicon-road' rel='tooltip' title='Radio Access Form'></span> RAFID</th>
		<th>RAF TECHNOS CON</th>
		<th>SAVE DATE</th>
		<th>UPDATE DATE</th>
		<th>BSDS TYPE</th>
		<th style="width:50px;">
			<button type="button" class="btn btn-info btn-xs history" href="#" id="historyPerdate">
			<span class="glyphicon glyphicon-circle-arrow-down"></span> HISTORY</button>
		</th>
	</tr>
	</thead>
	<tbody>
	<? echo $output_latestwithRAFID; ?>
	<? echo $output_withRAFID; ?>
	<? echo $output_withoutRAFID; ?>
	

	<? echo $output_funded; ?>
	<? echo $output_asbuild; ?>
	</tbody>
	</table>

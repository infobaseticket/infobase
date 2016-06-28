<?php
$technosInAsset=$_POST['technos'];

//GET THE DATE OF LATEST TIME THE BOB REPORT HAS BEEN REFRESHED
$REFRESH_DATE=get_BSDSrefresh();

$DB_BOB_refresh=$REFRESH_DATE['DATE_ALL_UPG'];
$DB_BOB_refresh_amount=$REFRESH_DATE['AMOUNT'];
if ($DB_BOB_refresh=="" || $DB_BOB_refresh_amount==0){
	?>
	<script language="JavaScript">
	Messenger().post({
	  message: 'Due to problems with the link between Aircom and Base, the BSDS-module is unavailable',
	  type: 'error',
	  showCloseButton: false
	});
	</script>
	<?
	die;
}

$lognode['G18']=$_POST['lognodeID_GSM'];
$lognode['G9']=$_POST['lognodeID_GSM'];
$lognode['U21']=$_POST['lognodeID_UMTS2100'];
$lognode['U9']=$_POST['lognodeID_UMTS900'];
$lognode['L18']=$_POST['lognodeID_LTE1800'];
$lognode['L26']=$_POST['lognodeID_LTE2600'];
$lognode['L8']=$_POST['lognodeID_LTE800'];

/*
* Get address and classcode info out of NET1
*/
$fname_voor=substr($_POST['siteID'],0,1);
if (($fname_voor=="M" || $fname_voor=="S")&& substr($_POST['siteID'],0,2)!='MT'){
	$fname_pre=substr($_POST['site'],1);
}else{
	$fname_pre=$_POST['siteID'];
}

$coor=get_coordinates($fname_pre);

$siteinfo=get_siteinfo($_POST['candidate']);

$Sitename=$siteinfo['SIT_UDK'][0];
$Classcode=$siteinfo['SIT_LKP_STY_CODE'][0];
$adress=$siteinfo['SIT_ADDRESS'][0]."<br>";


$query = "Select * FROM ORQ_BSDS WHERE CANDIDATE LIKE '%".$_POST['candidate']."%'";
$stmtORQ = parse_exec_fetch($conn_Infobase, $query, $error_str, $resORQ);
if (!$stmtORQ){
	die_silently($conn_Infobase, $error_str);
 	exit;
}else{
	OCIFreeStatement($stmtORQ);
	if (count($resORQ['SITE'])>0){
		$output_pre.="<tr class='warning'><td colspan='8'><b>ORQ ongoing:</b><br>";
		foreach ($resORQ['SITE'] as $key=>$attrib_id){
			$output_pre.="<b>".$resORQ['TICKETID'][$key]."</b>: ".$resORQ['DESCRIPTION'][$key];
			$output_pre.="<br>";
		}
		$output_pre.="</td></td>";
	}
}

//First make sure there are BDS(s) existing => count
$query = "Select * FROM BSDS_GENERALINFO2 WHERE SITEKEY = '".$_POST['ADDRESSFK']."' AND DELETEDSTATUS!='yes' ORDER BY CHANGE_DATE DESC";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_BSDSs=count($res1['BSDSKEY'][0]);
	//echo $amount_of_BSDSs;
	if ($amount_of_BSDSs>0){
		include("general_info_GENERALINFO2.php");
	}else{
		$output_pre.="<tr class='danger'><td colspan='8'>NO BSDS DATA FOUND! <br>You probably deleted the SITE in Asset and recretaed it. Because of this some keys in the database changed to (".$_POST['ADDRESSFK']."). Please contact Infobase admin!</td></tr>";
	}
}

if($amount_BSDS_withRAF==0){
	$output_pre.="<tr class='danger'><td colspan='8'><b>No BSDS found which is attached to a RAF or BSDS is not yet funded!</b></td></tr>";
}

ocilogoff($conn_Infobase);
?>


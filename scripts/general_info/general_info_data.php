<?php

/*
* Get address and classcode info out of NET1
*/
$fname_voor=substr($_POST['siteid'],0,1);
if (($fname_voor=="M" || $fname_voor=="S")&& substr($_POST['siteid'],0,2)!='MT'){
	$fname_pre=substr($_POST['site'],1);
}else{
	$fname_pre=$_POST['siteid'];
}

$coor=get_coordinates($fname_pre);

$siteinfo=get_siteinfo($_POST['candidate']);

$Sitename=$siteinfo['SIT_UDK'][0];
$Classcode=$siteinfo['SIT_LKP_STY_CODE'][0];
$address=$siteinfo['SIT_ADDRESS'][0]."<br>";


$query = "Select * FROM ORQ_BSDS WHERE CANDIDATE LIKE '%".$_POST['candidate']."%'";
//echo $query;
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
$query = "Select * FROM BSDS_OVERVIEW OV LEFT JOIN BSDS_RAFV2 RA on OV.RAFID=RA.RAFID WHERE OV.CANDIDATE = '".$_POST['candidate']."'";
if ($_POST['nbup']=='UPG'){
	$query .= " AND OV.UPGNR='".$_POST['upgnr']."'";
}else{
	$query .= " AND (OV.UPGNR NOT LIKE '99%' or OV.UPGNR IS NULL)";
}
$query .= " ORDER BY OV.DATE_UPDATE DESC, OV.FROZEN DESC";
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
		$output_latestwithRAFID.="<tr class='warning'><td colspan='8'><b>NO BSDS DATA FOUND!</b></td></tr>";
	}
}
ocilogoff($conn_Infobase);
?>


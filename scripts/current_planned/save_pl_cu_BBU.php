<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
include("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


if ($_POST['bsdskey']!=""){
	//ONLY STATUS O CAN BE UPDATED
	$query = "UPDATE BSDS_OVERVIEW SET
		DATE_UPDATE = SYSDATE,
		UNIRAN='".$_POST['uniran']."',
		CABTYPE='".$_POST['cabtype']."',
		RECTIFIER='".$_POST['rectifier']."',
		POWERSUP='".$_POST['powersup']."'
		WHERE BSDSKEY= '".$_POST['bsdskey']."' AND FROZEN='0'  AND CREATED_DATE='".$_POST['createddate']."'";
	//echo $query;
	
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message="OVERVIEW DATA '".$action."'!<br>";
	}

	/*************************
	// CURRENT SAVE OR UPDATE
	*************************/
	//when frozen, current cannot be saved
	$query = "SELECT BSDSKEY FROM BSDS_CU_BBU WHERE SITEKEY= '".$_POST['candidate']."' AND STATUS='".$_POST['frozen']."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$Count=count($res1['BSDSKEY']);
	}
	if ($Count=="0"){
		//ONly a current PRE can be inserted or updated. (FOR POST => BSDS funded can not be updated)
		$query = "INSERT INTO BSDS_CU_BBU
			VALUES ('".$_POST['bsdskey']."','".$_POST['createddate']."',SYSDATE,
			'".$_POST['cur_C1_SLOT1']."','".$_POST['cur_C1_SLOT2']."','".$_POST['cur_C1_SLOT3']."','".$_POST['cur_C1_SLOT4']."','".$_POST['cur_C1_SLOT5']."','".$_POST['cur_C1_SLOT6']."','".$_POST['cur_C1_SLOT7']."','".$_POST['cur_C1_SLOT8']."',
			'".$_POST['cur_C1_SLOT13']."','".$_POST['cur_C1_SLOT14']."','".$_POST['cur_C1_SLOT15']."','".$_POST['cur_C1_TECHNOS']."',
			'".$_POST['cur_C2_SLOT1']."','".$_POST['cur_C2_SLOT2']."','".$_POST['cur_C2_SLOT3']."','".$_POST['cur_C2_SLOT4']."','".$_POST['cur_C2_SLOT5']."','".$_POST['cur_C2_SLOT6']."','".$_POST['cur_C2_SLOT7']."','".$_POST['cur_C2_SLOT8']."',
			'".$_POST['cur_C2_SLOT13']."','".$_POST['cur_C2_SLOT14']."','".$_POST['cur_C2_SLOT15']."','".$_POST['cur_C2_TECHNOS']."',
			'0','".$_POST['candidate']."')";
			$action="saved";
	}else{
		$query = "UPDATE BSDS_CU_BBU SET
		CHANGE_DATE = SYSDATE,
		C1_SLOT1='".$_POST['cur_C1_SLOT1']."',
		C1_SLOT2='".$_POST['cur_C1_SLOT2']."',
		C1_SLOT3='".$_POST['cur_C1_SLOT3']."',
		C1_SLOT4='".$_POST['cur_C1_SLOT4']."',
		C1_SLOT5='".$_POST['cur_C1_SLOT5']."',
		C1_SLOT6='".$_POST['cur_C1_SLOT6']."',
		C1_SLOT7='".$_POST['cur_C1_SLOT7']."',
		C1_SLOT8='".$_POST['cur_C1_SLOT8']."',
		C1_SLOT13='".$_POST['cur_C1_SLOT13']."',
		C1_SLOT14='".$_POST['cur_C1_SLOT14']."',
		C1_SLOT15='".$_POST['cur_C1_SLOT15']."',
		C1_TECHNOS='".$_POST['cur_C1_TECHNOS']."',
		C2_SLOT1='".$_POST['cur_C2_SLOT1']."',
		C2_SLOT2='".$_POST['cur_C2_SLOT2']."',
		C2_SLOT3='".$_POST['cur_C2_SLOT3']."',
		C2_SLOT4='".$_POST['cur_C2_SLOT4']."',
		C2_SLOT5='".$_POST['cur_C2_SLOT5']."',
		C2_SLOT6='".$_POST['cur_C2_SLOT6']."',
		C2_SLOT7='".$_POST['cur_C2_SLOT7']."',
		C2_SLOT8='".$_POST['cur_C2_SLOT8']."',
		C2_SLOT13='".$_POST['cur_C2_SLOT13']."',
		C2_SLOT14='".$_POST['cur_C2_SLOT14']."',
		C2_SLOT15='".$_POST['cur_C2_SLOT15']."',
		C2_TECHNOS='".$_POST['cur_C2_TECHNOS']."'
		WHERE SITEKEY= '".$_POST['candidate']."' AND STATUS='0'";
		 $action="updated";
	}
	//echo $query."<br>";

	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message.="CURRENT BBU DATA '".$action."'!<br>";
	}

	/*************************
	// PLANNED SAVE OR UPDATE
	*************************/
	$query = "SELECT BSDSKEY FROM BSDS_PL_BBU WHERE BSDSKEY= '".$_POST['bsdskey']."' AND STATUS='".$_POST['frozen']."'
	 AND BSDS_BOB_REFRESH=to_date('".$_POST['createddate']."')";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$Count=count($res1['BSDSKEY']);
	}

	if ($_POST['pl_C1_G9']=='G9'){
		$technos1.='G9,';
	}
	if ($_POST['pl_C1_G18']=='G18'){
		$technos1.='G18,';
	}
	if ($_POST['pl_C1_U9']=='U9'){
		$technos1.='U9,';
	}
	if ($_POST['pl_C1_U21']=='U21'){
		$technos1.='U21,';
	}
	if ($_POST['pl_C1_L8']=='L8'){
		$technos1.='L8,';
	}
	if ($_POST['pl_C1_L18']=='L18'){
		$technos1.='L18,';
	}
	if ($_POST['pl_C1_L26']=='L26'){
		$technos1.='L26,';
	}
	if ($_POST['pl_C2_G9']=='G9'){
		$technos2.='G9,';
	}
	if ($_POST['pl_C2_G18']=='G18'){
		$technos2.='G18,';
	}
	if ($_POST['pl_C2_U9']=='U9'){
		$technos2.='U9,';
	}
	if ($_POST['pl_C2_U21']=='U21'){
		$technos2.='U21,';
	}
	if ($_POST['pl_C2_L8']=='L8'){
		$technos2.='L8,';
	}
	if ($_POST['pl_C2_L18']=='L18'){
		$technos2.='L18,';
	}
	if ($_POST['pl_C2_L26']=='L26'){
		$technos2.='L26,';
	}
	if ($technos1!=''){
		$technos1=substr($technos1,0,-1);
	}
	if ($technos2!=''){
		$technos2=substr($technos2,0,-1);
	}
	
		
	if ($Count=="0"){
		//ONly a current PRE can be inserted or updated. (FOR POST => BSDS funded can not be updated)
		$query = "INSERT INTO BSDS_PL_BBU
			VALUES ('".$_POST['bsdskey']."','".$_POST['createddate']."',SYSDATE,
			'".$_POST['pl_C1_SLOT1']."','".$_POST['pl_C1_SLOT2']."','".$_POST['pl_C1_SLOT3']."','".$_POST['pl_C1_SLOT4']."','".$_POST['pl_C1_SLOT5']."','".$_POST['pl_C1_SLOT6']."','".$_POST['pl_C1_SLOT7']."','".$_POST['pl_C1_SLOT8']."',
			'".$_POST['pl_C1_SLOT13']."','".$_POST['pl_C1_SLOT14']."','".$_POST['pl_C1_SLOT15']."','".$technos1."',
			'".$_POST['pl_C2_SLOT1']."','".$_POST['pl_C2_SLOT2']."','".$_POST['pl_C2_SLOT3']."','".$_POST['pl_C2_SLOT4']."','".$_POST['pl_C2_SLOT5']."','".$_POST['pl_C2_SLOT6']."','".$_POST['pl_C2_SLOT7']."','".$_POST['pl_C2_SLOT8']."',
			'".$_POST['pl_C2_SLOT13']."','".$_POST['pl_C2_SLOT14']."','".$_POST['pl_C2_SLOT15']."','".$technos2."',
			'0')";
			$action="saved";
	}else{
		$query = "UPDATE BSDS_PL_BBU SET
		CHANGE_DATE = SYSDATE,
		C1_SLOT1='".$_POST['pl_C1_SLOT1']."',
		C1_SLOT2='".$_POST['pl_C1_SLOT2']."',
		C1_SLOT3='".$_POST['pl_C1_SLOT3']."',
		C1_SLOT4='".$_POST['pl_C1_SLOT4']."',
		C1_SLOT5='".$_POST['pl_C1_SLOT5']."',
		C1_SLOT6='".$_POST['pl_C1_SLOT6']."',
		C1_SLOT7='".$_POST['pl_C1_SLOT7']."',
		C1_SLOT8='".$_POST['pl_C1_SLOT8']."',
		C1_SLOT13='".$_POST['pl_C1_SLOT13']."',
		C1_SLOT14='".$_POST['pl_C1_SLOT14']."',
		C1_SLOT15='".$_POST['pl_C1_SLOT15']."',
		C1_TECHNOS='".$technos1."',
		C2_SLOT1='".$_POST['pl_C2_SLOT1']."',
		C2_SLOT2='".$_POST['pl_C2_SLOT2']."',
		C2_SLOT3='".$_POST['pl_C2_SLOT3']."',
		C2_SLOT4='".$_POST['pl_C2_SLOT4']."',
		C2_SLOT5='".$_POST['pl_C2_SLOT5']."',
		C2_SLOT6='".$_POST['pl_C2_SLOT6']."',
		C2_SLOT7='".$_POST['pl_C2_SLOT7']."',
		C2_SLOT8='".$_POST['pl_C2_SLOT8']."',
		C2_SLOT13='".$_POST['pl_C2_SLOT13']."',
		C2_SLOT14='".$_POST['pl_C2_SLOT14']."',
		C2_SLOT15='".$_POST['pl_C2_SLOT15']."',
		C2_TECHNOS='".$technos2."'
		 WHERE BSDSKEY= '".$_POST['bsdskey']."' AND STATUS='0'  AND BSDS_BOB_REFRESH='".$_POST['createddate']."'";
		 $action="updated";
	}
	//echo $query."<br>";

	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message.="PLANNED BBU DATA '".$action."'!<br>";
	}

}else{
	die("You are doing strange things!!");
}

if ($message){
	$res["responsedata"] = $message;
	$res["responsetype"]="info";
}
if ($warning){
	$res["responsedata"] = $warning;
	$res["responsetype"]="warning";
}
if ($alert){
	$res["responsedata"] = $alert;
	$res["responsetype"]="error";
}
echo json_encode($res);

?>

<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_other","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST["action"]=='insert_new_audit'){
	if ($_POST['auditdate']==""){
		$guard_username_audit="";
		$audit_sysdate="''";
	}else{
		$guard_username_audit=$guard_username;
		$audit_sysdate="SYSDATE";
	}

	if (($_POST['PUNCHA']>0 || $_POST['PUNCHB']>0 || $_POST['PUNCHC']>2) && $_POST['auditid']!="planning"){
		$result='FAILED';
		$planning="No";
	}else if( $_POST['auditid']!="planning"){
		$result='PASSED';
		$planning="No";
	}else{
		$planning="yes";
	}
	$query = "INSERT INTO INFOBASE.BSDS_AUDITS (
	   ID, TYPE, SITE,AUDITS,
	   AUDITS_DATE, AUDITS_BY, STATUS, COMMENTS, REASON_COMMENTS,REASON, TYPE2,CREATION_DATE,CREATION_BY,
	   INSPECTIONPARTNER,SERVICEPARTNER1,SERVICEPARTNER2,PLANNING,HSCOORD,SITEENG,PUNCHA,PUNCHB,PUNCHC)
	VALUES ( '', '".$_POST['audittype1']."', '".$_POST['siteaudit']."', '".$_POST['auditdate']."',
	    ".$audit_sysdate.", '".$guard_username_audit."','".$result."','".escape_sq($_POST['comments'])."','".escape_sq($_POST['reason_comments'])."','".escape_sq($_POST['reason'])."','".$_POST['audittype2']."',SYSDATE,'".$guard_username."',
	    '".$_POST['inspectionpartner']."','".$_POST['servicepartner1']."','".$_POST['servicepartner2']."','".$planning."','".$_POST['HSCOORD']."','".$_POST['SITEENG']."',
	    '".$_POST['PUNCHA']."','".$_POST['PUNCHB']."','".$_POST['PUNCHC']."')";

	//echo "$query";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}

	$query = "SELECT MAX(ID) AS ID FROM INFOBASE.BSDS_AUDITS";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
	}
	$res["responsedata"] = "NEW AUDIT SUCCESSFULLY CREATED!";
	$res["responsetype"]="info";
	$res["auditid"]=$res1['ID'][0];
	$res["siteaudit"]=$_POST['siteaudit'];
	echo json_encode($res);
}

if ($_POST['action']=="delete_audit_file"){
	unlink($config['audit_folder_abs'].$_POST['auditid']."/".$_POST["auditfile"]);
	$ext=explode(".",$_POST["auditfile"]);

	if ($ext[1]=="gif" || $ext[1]=="bmp"  || $ext[1]=="jpg"){
		$exp=explode("_",$_POST["auditfile"],2);
		$origin=$config['audit_folder_abs'].$_POST['auditid']."/ori_".$exp[1];
		//echo $origin;
		unlink($origin);
	}
}

if ($_POST['action']=="delete_audit"){
	$query = "DELETE FROM BSDS_AUDITS WHERE ID='".$_POST['id']."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "AUDIT ".$_POST['id']." has succesfully been deleted!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}


if ($_POST['action']=="provideFailureReason"){
	if ($_POST['field']=="STATUSKPNGB"){
	$query = "UPDATE BSDS_AUDITS SET REASONKPNGB1 ='".$_POST['reasonKPNGB']."',REASONKPNGB2 ='".$_POST['failureReason']."'  WHERE ID='".$_POST['auditid']."'";
	}else if ($_POST['field']=="STATUS"){
		if ($_POST['PUNCHA']>0 || $_POST['PUNCHB']>0 || $_POST['PUNCHC']>2){
			$result='FAILED';
		}else{
			$result='PASSED';
		}
		$query = "UPDATE BSDS_AUDITS SET COMMENTS ='".$_POST['failureReason']."', STATUS='".$result."',
		PUNCHA='".$_POST['PUNCHA']."', PUNCHB='".$_POST['PUNCHB']."', PUNCHC='".$_POST['PUNCHC']."' WHERE ID='".$_POST['auditid']."'";
	}
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$res["responsedata"] = "Audit result has been updated.";
		$res["responsetype"]="info";
		$res["status"]=$result;
		$res["auditid"]=$_POST['auditid'];
		$res["field"]=$_POST['field'];
		echo json_encode($res);
	}


}

if ($_POST['action']=="update_planning_date"){

	$query = "UPDATE BSDS_AUDITS SET ";
	if ( $_POST['field']!="AUDITS"){
	$query .=   " PLANNING='No',";
	}
	$query .= $_POST['field']." ='".$_POST['value']."',".$_POST['field']."_BY='".$guard_username."', ". $_POST['field']."_DATE=SYSDATE  WHERE ID='".$_POST['id']."'";

	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
	echo $_POST['value'];
}


?>
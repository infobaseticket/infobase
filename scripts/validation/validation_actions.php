<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","BASE_delivery,Administrators","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['action']=="overruleValidation"){
	if (trim($_POST['reason'])!=''){
		$query = "INSERT INTO VALIDATION_OVERRULE VALUES ('".$_POST['rafid']."','".$_POST['checktype']."',
		'".escape_sq($_POST['reason'])."',SYSDATE,'".$guard_username."','".$_POST['type']."')";
		//echo "$query";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
			$res["msg"] = "Override has been saved!";
			$res["rtype"]="success";
			echo json_encode($res);
		}	
	}else{
		$res["msg"] = "You can override without any reason!";
			$res["rtype"]="error";
			echo json_encode($res);
	}
}
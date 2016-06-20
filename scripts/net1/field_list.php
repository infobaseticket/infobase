<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_GET["field"]=="tasklist"){
	$query = "select * from  TASKS_ADMIN WHERE (upper(DESCRIPTION) LIKE '%".strtoupper($_GET["q"])."%' OR upper(TASK) LIKE '%".strtoupper($_GET["q"])."%' 
		AND TYPE LIKE '".strtoupper($_GET["type"])."%') AND UPDATABLE='1' ORDER BY TASK";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	foreach ($res1['TASK'] as $key=>$attrib_id) {
	    $arr[$key]['text']=$res1['TASK'][$key]."-".$res1['DESCRIPTION'][$key];
	    $arr[$key]['id']=$res1['TASK'][$key];
	}
	echo json_encode($arr);
}
?>
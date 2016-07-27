<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");

if (!$_GET["q"]) return;

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$field=$_GET['field'];
$query = "SELECT DISTINCT(".$_GET['field'].") FROM BSDS_CURRENT_REP_".$_GET['type']." WHERE UPPER(".$field.") LIKE '".strtoupper($_GET["q"])."%'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
    OCIFreeStatement($stmt);
    $count=count($res1[$field]);
}
//echo  $count;
for ($i=0;$i<$count;$i++){
	echo $res1[$field][0]."\n";
}
				
?>
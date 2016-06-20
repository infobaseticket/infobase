<?
require($_SERVER['DOCUMENT_ROOT']."/include/config.php");
require_once($config['phpguarddog_path']."/guard.php");
include($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
//error_reporting(E_ALL);

if (function_exists('imap_open')) {
    echo "IMAP functions are available.<br />\n";
} else {
    echo "IMAP functions are not available.<br />\n";
}

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$query = "SELECT a.type as TYPE, a.update_date AS UPDATE_DATE, a.amount_assigned AS AMOUNT_ASSIGNED, a.STILL_AVAILABLE
AS STILL_AVAILABLE, a.update_by FROM INFOBASE.MATERIAL_AVAILABILITY a,(SELECT type, MIN(UPDATE_DATE) as min_date
FROM INFOBASE.MATERIAL_AVAILABILITY GROUP BY type) b
WHERE a.type = b.type AND a.UPDATE_DATE = b.min_date";

//echo "$query<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
exit;
}else{
	OCIFreeStatement($stmt);
}
$count=count($res1['STILL_AVAILABLE']);

for ($i=0;$i<=$count;$i++){
	if ($res1['TYPE'][$i]=="TRU"){
		$TRU_AMOUNT_ASSIGNED=$res1['TRU_AMOUNT_ASSIGNED'][$i];
		$TRU_STILL_AVAILABLE=$res1['STILL_AVAILABLE'][$i];
	}
}


?>
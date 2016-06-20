<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
//protect("","Radioplanners,BASE_MP","");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
//error_reporting(E_ALL);

include $config['sitepath_abs']."/include/PHP/open-flash-chart-2/php-ofc-library/open-flash-chart.php";

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$query1 = "Select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_NEWBUILDS WHERE ";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$ACQ_NEW=$res1['AMOUNT'][0];
}

SELECT    *
FROM       VW_NET1_ALL_NEWBUILDS  a
WHERE      NOT EXISTS (SELECT * FROM BSDS_RAF  b WHERE trim(a.SIT_UDK) = b.NET1_LINK)


SELECT    *
FROM       VW_NET1_ALL_UPGRADES  a
WHERE      NOT EXISTS (SELECT * FROM BSDS_RAF  b WHERE trim(a.SIT_UDK) = b.NET1_LINK)

?>
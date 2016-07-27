<?php
include_once('/var/www/html/bsds/config.php');
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");
include("BSDSanalyzer_copydata.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$query = " SELECT * FROM BSDS_STATUS_CHANGES WHERE EXECUTED=0 ";//AND IB_BSDSKEY='43384'WHERE NET1_LINK IS NOT NULL AND SITEID LIKE '%LG2065%'
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}

echo "\r\n*******************************************************\r\n";
echo "               TOTAL BSDS CHANGED:".count($res1['IB_RAFID'])."\r\n";
echo "*******************************************************\r\n";
for ($i = 0; $i <count($res1['IB_RAFID']); $i++) {

	echo $i.' => RAFID='.$res1['IB_RAFID'][$i]." (".$res1['ACTION'][$i].")\r\n";
	echo 'IB_BSDSKEY: '.$res1['IB_BSDSKEY'][$i]."\r\n";
	echo 'CURRENT STATUS: '.$res1['CURRENTSTATUS'][$i]."\r\n";
	echo 'CURRENT NET1 DATE: '.$res1['CURRENTNET1DATE'][$i]."\r\n";
	echo 'N1_UPGNR: '.$res1['N1_UPGNR'][$i]."\r\n";
	echo 'N1_SITEID: '.$res1['N1_SITEID'][$i]."\r\n";
	echo 'N1_CANDIDATE: '.$res1['N1_CANDIDATE'][$i]."\r\n";
	echo 'REPORTDATE: '.$res1['REPORTDATE'][$i]."\r\n";
}
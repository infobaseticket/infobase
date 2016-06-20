<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query = "SELECT DISTINCT(N1_SITETYPE) FROM MASTER_REPORT ORDER BY N1_SITETYPE";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}
for ($i = 0; $i < count($res1['N1_SITETYPE']); $i++){
	$labels[$i]['label']=$res1['N1_SITETYPE'][$i];
	$label=$res1['N1_SITETYPE'][$i];
	$dataACQ_IS[$label]['value']="0";
	$dataACQ_OH[$label]['value']="0";
	$dataACQ_AD[$label]['value']="0";
	$dataBUF_IS[$label]['value']="0";
	$dataBUF_OH[$label]['value']="0";
	$dataBUF_AD[$label]['value']="0";
	$dataCON_IS[$label]['value']="0";
	$dataCON_OH[$label]['value']="0";
	$dataCON_AD[$label]['value']="0";
	$dataAIR_IS[$label]['value']="0";
	$dataAIR_OH[$label]['value']="0";
	$dataAIR_AD[$label]['value']="0";
}
?>
L.Control.Search.callJsonp([{"place_id":"71319920","licence":"Data © OpenStreetMap contributors, ODbL 1.0. http:\/\/www.openstreetmap.org\/copyright","osm_type":"way","osm_id":"69745487","boundingbox":["51.2063375","51.2067525","2.9332759","2.933687"],"lat":"51.20661375","lon":"2.93346075441176","display_name":"Reigersplein, Stene, Oostende, West-Vlaanderen, Vlaanderen, 8400, België","class":"highway","type":"residential","importance":0.21}])
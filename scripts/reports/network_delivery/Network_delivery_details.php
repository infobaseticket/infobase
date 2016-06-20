<?php
include("/var/www/html/include/config.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
require_once("Network_delivery_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


echo "slice ".$_GET['slice']."<br>";
echo "report ".$_GET['report']."<br>";
echo "year ".$_GET['year']."<br>";
echo "phase ".$_GET['phases']."<br>";
echo "split ".$_GET['split']."<br>";

if ($_GET['slice']==0){
	$func = 'query_'.$_GET['report'].'_new';
	$query = $func("DETAILS","MACROCELLS",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="MACROCELLS";
}else if ($_GET['slice']==1){
	$func = 'query_'.$_GET['report'].'_new';
	$query = $func("DETAILS","MICROCELLS",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="MICROCELLS";
}else if ($_GET['slice']==2){
	$func = 'query_'.$_GET['report'].'_new';
	$query = $func("DETAILS","INDOOR",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="INDOOR";
}else if ($_GET['slice']==3){
	$func = 'query_'.$_GET['report'].'_new';
	$query = $func("DETAILS","REPLACEMENTS",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="REPLACEMENTS";
}else if ($_GET['slice']==4){
	$func = 'query_'.$_GET['report'].'_new';
	$query = $func("DETAILS","EMPTY",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="EMPTY";
}else if ($_GET['slice']==5){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'ASC'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="ASC";
}else if ($_GET['slice']==6){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'DCS'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="DCS";
}else if ($_GET['slice']==7){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'CAB'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="CAB";
}else if ($_GET['slice']==8){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'CTX'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="CTX";
}else if ($_GET['slice']==9){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'UMTS'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="UMTS";
}else if ($_GET['slice']==10){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'UMT6'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="UMT6";
}else if ($_GET['slice']==11){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'HSDPA'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="HSDPA";
}else if ($_GET['slice']==12){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'HSPX'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="HSPX";
}else if ($_GET['slice']==13){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'CWK'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="CWK";
}else if ($_GET['slice']==14){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'ANT'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="ANT";
}else if ($_GET['slice']==15){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'EG6'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="EG6";
}else if ($_GET['slice']==16){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'EGS'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="EGS";
}else if ($_GET['slice']==17){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'SHA', 'SHB', 'SHC', 'SHM', 'SHP', 'SHR'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="'SHA', 'SHB', 'SHC', 'SHM', 'SHP', 'SHR'";
}else if ($_GET['slice']==18){
	$func = 'query_'.$_GET['report'].'_upg';
	$query = $func("DETAILS","'RPT','LLA','IND'",$_GET['split'],$_GET['year'],$_GET['phases']);
	$typefile="'RPT','LLA','IND'";
}

echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_inreport=count($res1['SIT_UDK']);	
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$_GET['report']."_".$typefile."_".$_GET['split']."_".$_GET['year'].".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	
if ($_GET['slice']<=4){
	echo "<table><thead><th>SITE</th><th>WOR_DOM_WOS_CODE</th><th>DRE_V20_1</th><th>WOE_RANK</th><th>WOR_HSDPA_CLUSTER</th><th>DRE_V2_1_6</th><th>SIT_LKP_STY_CODE</th>";
	echo "<th>SAC</th><th>CON</th><th>WIP</th><th>A105</th><th>A709</th><th>A04</th><th>A353</th><th>A80</th><th>A81</th><th>A105</th></thead>";
	if ($amount_inreport>=1){
		for ($i = 0; $i <$amount_inreport; $i++) {	
		echo "<tr><td>".$res1['SIT_UDK'][$i]."</td><td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td><td>".$res1['DRE_V20_1'][$i]."</td><td>".$res1['WOE_RANK'][$i]."</td>";
		echo "<td>".$res1['WOR_HSDPA_CLUSTER'][$i]."</td><td>".$res1['DRE_V2_1_6'][$i]."</td><td>".$res1['SIT_LKP_STY_CODE'][$i]."</td><td>".$res1['SAC'][$i]."</td>";
		echo "<td>".$res1['CON'][$i]."</td><td>".$res1['WIP'][$i]."</td><td>".$res1['A105'][$i]."</td><td>".$res1['A709'][$i]."</td>";	
		echo "<td>".$res1['A04'][$i]."</td><td>".$res1['A353'][$i]."</td><td>".$res1['A80'][$i]."</td><td>".$res1['A81'][$i]."</td><td>".$res1['A105'][$i]."</td></tr>";	
		}
	}
	echo "</table>";
}else{
	echo "<table><thead><th>SITE</th><th>WOR_UDK</th><th>WOR_NAME</th><th>WOR_DOM_WOS_CODE</th><th>WOR_HSDPA_CLUSTER</th>";
	echo "<th>WOR_LKP_WCO_CODE<th>SAC</th><th>CON</th><th>WIP</th><th>U405</th><th>U709</th><th>U001</th><th>U353</th><th>U380</th><th>U381</th></thead>";
	if ($amount_inreport>=1){
		for ($i = 0; $i <$amount_inreport; $i++) {	
		echo "<tr><td>".$res1['SIT_UDK'][$i]."</td><td>".$res1['WOR_UDK'][$i]."</td><td>".$res1['WOR_NAME'][$i]."</td><td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td>";
		echo "<td>".$res1['WOR_HSDPA_CLUSTER'][$i]."</td><td>".$res1['WOR_LKP_WCO_CODE'][$i]."</td>";
		echo "<td>".$res1['SAC'][$i]."</td><td>".$res1['CON'][$i]."</td><td>".$res1['WIP'][$i]."</td><td>".$res1['U405'][$i]."</td><td>".$res1['U709'][$i]."</td>";	
		echo "<td>".$res1['U001'][$i]."</td><td>".$res1['U353'][$i]."</td><td>".$res1['U380'][$i]."</td><td>".$res1['U381'][$i]."</td></tr>";	
		}
	}
	echo "</table>";
}
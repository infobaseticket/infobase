<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_other","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("audit_procedures.php");



$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_GET["list"]=="LIST1"){
	if (!$_GET["q"]) return;
	//trim(SIT_UDK)||' '||trim(DRE_V20_1)
	$query1 = "select 'NB: '||trim(SIT_UDK)||': '||trim(DRE_V20_1) AS SITE from VW_NET1_ALL_NEWBUILDS WHERE upper(SIT_UDK) LIKE '%".strtoupper($_GET["q"])."%'
	AND WOE_RANK=1  AND WOR_DOM_WOS_CODE ='IS'
    AND trim(SIT_UDK)||': '||trim(DRE_V20_1) NOT IN (select site from BSDS_AUDITS WHERE type='".$_GET["audittype1"]."' AND type='".$_GET["audittype2"]."' and STATUS ='OK') ORDER BY SIT_UDK";
	//echo $query."<br>";
	$stmt1 = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
	if (!$stmt1) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt1);
	  $amount1=count($res1['SITE']);
	  foreach ($res1['SITE'] as $key=>$attrib_id) {
	    echo $res1['SITE'][$key]."\n";
	  }
	}

	$query2 = "select  'UPG: '||trim(SIT_UDK)||': '||trim(WOR_LKP_WCO_CODE)||' ['||trim(WOR_UDK)||']' AS SITE from VW_NET1_ALL_UPGRADES WHERE upper(SIT_UDK) LIKE '%".strtoupper($_GET["q"])."%'
	AND WOR_LKP_WCO_CODE NOT LIKE 'SH%' AND WOR_LKP_WCO_CODE NOT LIKE 'TRX%' AND WOR_LKP_WCO_CODE NOT LIKE 'FLX%' AND WOR_LKP_WCO_CODE NOT LIKE 'EDGE%'
	AND trim(SIT_UDK)||': '||trim(WOR_LKP_WCO_CODE)||' ['||trim(WOR_UDK)||']'  NOT IN (select site from BSDS_AUDITS WHERE type='".$_GET["audittype1"]."' AND type='".$_GET["audittype2"]."' and STATUS ='OK') ORDER BY SIT_UDK";
	//echo $query2."<br>";
	$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
	if (!$stmt2) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt2);
	}
	//echo $_GET["audit"];
	$amount2=count($res2['SITE']);
	foreach ($res2['SITE'] as $key=>$attrib_id) {
	    echo $res2['SITE'][$key]."\n";
	}

	//echo "<b>".$amount1."+".$amount2." record(s) found</b>\n";

}else if ($_GET["list"]=="LIST2"){
	if (!$_GET["q"]) return;
	$query=query_audit($_GET['audit'],$_GET['q']);
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_audits=count($res1['SITE']);
	}
	if ($amount_of_audits>=1){
		for ($i = 0; $i <$amount_of_audits; $i++) {
			echo $res1['SITE'][$i]."\n";
		}
	}
}

if($_POST["action"]=="net1_max_date"){

	echo get_net1_maxdate($_POST["sitetype"]);

}
?>
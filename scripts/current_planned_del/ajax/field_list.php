<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

if (!$_GET["q"]) return;

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_GET["field"]=="antenna"){
	$query = "select DISTINCT IDNAME from ".$config['table_asset_antennatype']." 
	WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%' AND upper(IDNAME) LIKE '%".strtoupper($_GET["type"])."%'";
	if ($_GET["type"]=='800'){
		$query.=" AND upper(IDNAME) NOT LIKE '%1800%'";
	}
	$query.=" ORDER BY IDNAME";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	$extra=count($res['IDNAME']);
	foreach ($res['IDNAME'] as $key=>$attrib_id) {
	    $arr[$key]['id']=$res['IDNAME'][$key];
	    $arr[$key]['text']=$res['IDNAME'][$key];
	}

	$arr[$extra]['id']='';
	$arr[$extra]['text']='NONE';
	echo json_encode($arr);
}else if ($_GET["field"]=="cabtype2"){

	$arr[0]['id']='BS8800:0';
	$arr[0]['text']='BS8800:0';
	$arr[1]['id']='BS8800:6';
	$arr[1]['text']='BS8800:6';
	$arr[2]['id']='BS8800:12';
	$arr[2]['text']='BS8800:12';
	$arr[3]['id']='BS8900:0';
	$arr[3]['text']='BS8900:0';
	$arr[4]['id']='BS8900:3';
	$arr[4]['text']='BS8900:3';
	$arr[5]['id']='BS8900A:6';
	$arr[5]['text']='BS8900A:6';
	$arr[6]['id']='BS8900A:12';
	$arr[6]['text']='BS8900A:12';
	$arr[7]['id']='BS8900A:9';
	$arr[7]['text']='BS8900A:9';
	$arr[8]['id']='BS8800:9';
	$arr[8]['text']='BS8800:9';
	$arr[9]['id']='';
	$arr[9]['text']='NONE';
	echo json_encode($arr);
}else if ($_GET["field"]=="config"){
	$query = "select DISTINCT IDNAME from ".$config['table_asset_cellequipment']." WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%'";
	//echo "<br><br>$query";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
   	$extra=count($res['IDNAME']);
	foreach ($res['IDNAME'] as $key=>$attrib_id) {
	    $arr[$key]['id']=$res['IDNAME'][$key];
	    $arr[$key]['text']=$res['IDNAME'][$key];
	}
	$arr[$extra]['id']='';
	$arr[$extra]['text']='NONE';
	echo json_encode($arr);
}else if ($_GET["field"]=="feedertype"){

	if ($_GET["type"]=="UMTS"){
		$query = "select IDNAME from ".$config['table_asset_feeder']." WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%' AND (IDNAME LIKE '%UMTS%' OR IDNAME LIKE '%2100%') ORDER BY IDNAME ASC";
	}else if ($_GET["type"]=="U9"){
		$query = "select IDNAME from ".$config['table_asset_feeder']." WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%' AND IDNAME LIKE '%UMTS%' OR  IDNAME LIKE '%900%')  ORDER BY IDNAME ASC";
	}else if ($_GET["type"]=="L26" or $_GET["type"]=="L18"){
		$query = "select IDNAME from ".$config['table_asset_feeder']." WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%' AND IDNAME LIKE '%LTE%' OR  IDNAME LIKE '%".strtoupper($_GET["type"])."%')  ORDER BY IDNAME ASC";
	}else if ($_GET["type"]=="L8"){
		$query = "select IDNAME from ".$config['table_asset_feeder']." WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%' AND IDNAME LIKE '%LTE%' OR  (IDNAME LIKE '%".strtoupper($_GET["type"])."%' AND IDNAME NOT LIKE '%1800%')  ORDER BY IDNAME ASC";
	}else{
		$query = "select IDNAME from ".$config['table_asset_feeder']." WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%' AND IDNAME LIKE '%".strtoupper($_GET["type"])."%' ORDER BY IDNAME ASC";
	}
	//echo "<br><br>$query";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
   	$extra=count($res['IDNAME']);
	foreach ($res['IDNAME'] as $key=>$attrib_id) {
	    $arr[$key]['id']=$res['IDNAME'][$key];
	    $arr[$key]['text']=$res['IDNAME'][$key];
	}
	$extra=count($res['IDNAME']);
	$arr[$extra]['id']='';
	$arr[$extra]['text']='NONE';
	echo json_encode($arr);
}else if ($_GET["field"]=="cabtype"){
	$query = "select DISTINCT IDNAME from ".$config['table_asset_bts']." WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%' ORDER BY IDNAME ASC";
	//echo "<br><br>$query";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
   	$arr=array();
   	$i=0;
   	$extra=count($res['IDNAME']);
	foreach ($res['IDNAME'] as $key=>$attrib_id) {
	    $arr[$key]['id']=$res['IDNAME'][$key];
	    $arr[$key]['text']=$res['IDNAME'][$key];
	}
	$arr[$extra]['id']='';
	$arr[$extra]['text']='NONE';
	echo json_encode($arr);
}
?>
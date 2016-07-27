<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_GET["field"]=="sites"){

	$query = "select DISTINCT SIT_UDK from VW_NET1_ALL_NEWBUILDS WHERE upper(SIT_UDK) LIKE '%".strtoupper($_GET["q"])."%' AND WOE_RANK=1 ORDER BY SIT_UDK";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	if (count($res1['SIT_UDK'])!=0){
		foreach ($res1['SIT_UDK'] as $key=>$attrib_id) {
		    $arr[$key]['id']=$res1['SIT_UDK'][$key];
		    $arr[$key]['text']=$res1['SIT_UDK'][$key];
		}
	}else{
		$arr[0]['id']=0;
		$arr[0]['text']="No results found...";
	}
	echo json_encode($arr);
}else if ($_GET["field"]=="upgnrs"){

	$query = "select DISTINCT WOR_UDK, WOR_LKP_WCO_CODE, WOR_NAME from  VW_NET1_ALL_UPGRADES 
	WHERE upper(WOR_UDK) LIKE '%".strtoupper($_GET["q"])."%'";
	if ($_GET["siteid"]!=""){
	$query .= " AND SIT_UDK='".$_GET["siteid"]."'";
	}
	if ($_GET["status"]!=""){
	$query .= " AND WOR_DOM_WOS_CODE='".$_GET["status"]."'";
	}
	$query .= " ORDER BY WOR_UDK ASC";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	foreach ($res1['WOR_UDK'] as $key=>$attrib_id) {
	    $arr[$key]['id']=$res1['WOR_UDK'][$key];
	    $arr[$key]['text']=$res1['WOR_UDK'][$key];
	    $arr[$key]['description']=$res1['WOR_UDK'][$key]. " (".$res1['WOR_LKP_WCO_CODE'][$key].": ".$res1['WOR_NAME'][$key].")";
	}
	echo json_encode($arr);

}else if ($_GET["field"]=="tasklist"){
	$query = "select DISTINCT(TAS_CODE),TAS_DESC from  TASKS@NET1PRD WHERE (upper(TAS_DESC) LIKE '%".strtoupper($_GET["q"])."%' OR upper(TAS_CODE) LIKE '%".strtoupper($_GET["q"])."%') ";
	
	if($_GET["upgnr"]==""){
		$query .= " AND upper(TAS_CODE) LIKE 'A%'";
	}else if ($_GET["upgnr"]!=""){
		$query .= " AND upper(TAS_CODE) LIKE 'U%'";
	}
	$query .= " ORDER BY TAS_CODE";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	foreach ($res1['TAS_DESC'] as $key=>$attrib_id) {
	    $arr[$key]['id']=$res1['TAS_CODE'][$key];
	    $arr[$key]['upgnr']=$res1['TAS_CODE'][$key];
	    $arr[$key]['description']=$res1['TAS_CODE'][$key]."-".$res1['TAS_DESC'][$key];
	}
	echo json_encode($arr);
}else if ($_GET["field"]=="config"){

	$query = "select DISTINCT IDNAME from ".$config['table_asset_cellequipment']." WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%'";
	//echo "<br><br>$query";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	foreach ($res1['IDNAME'] as $key=>$attrib_id) {
	    $arr[$key]['id']=$key;
	    $arr[$key]['text']=$res1['IDNAME'][$key];
	}
	echo json_encode($arr);
}else if ($_GET["field"]=="feedertype"){
	$query = "select IDNAME from ".$config['table_asset_feeder']." WHERE upper(IDNAME) LIKE '%".strtoupper($_GET["q"])."%' AND IDNAME LIKE '%".strtoupper($_GET["type"])."%'";
	//echo "<br><br>$query";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	$amount=count($res1['IDNAME']);
	 $arr['options'][]=$amount." record(s) found";
	foreach ($res1['IDNAME'] as $key=>$attrib_id) {
	    $arr[$key]['id']=$key;
	    $arr[$key]['text']=$res1['IDNAME'][$key];
	}
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
	$amount=count($res1['IDNAME']);
	 $arr['options'][]=$amount." record(s) found";
	foreach ($res1['IDNAME'] as $key=>$attrib_id) {
	   $arr[$key]['id']=$key;
	    $arr[$key]['text']=$res1['IDNAME'][$key];
	}
}else if ($_GET["field"]=="conAcqPartner"){
	if($_GET["lostype"]=="MOV"){
		$query = "select SAC ,CON from VW_NET1_ALL_UPGRADES WHERE upper(SIT_UDK) LIKE '%".strtoupper($_GET["q"])."%' AND WOR_DOM_WOS_CODE='IS' AND WOR_LKP_WCO_CODE='MOV' ORDER BY SIT_UDK";
	}else{
		$query = "select SAC ,CON from VW_NET1_ALL_NEWBUILDS WHERE upper(SIT_UDK) LIKE '%".strtoupper($_GET["q"])."%' AND WOE_RANK=1 ORDER BY SIT_UDK";	
	}
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}

	if ($res1['CON'][0]=="BENCHMARK" or $res1['CON'][0]=="ALU" or $res1['CON'][0]=="ZTE" OR $res1['CON'][0]=="TECHM"){
		$out.="<option>".$res1['CON'][0]." (CON)</option>";
	}
	if ($res1['SAC'][0]=="BENCHMARK" or $res1['SAC'][0]=="ALU" or $res1['SAC'][0]=="ZTE" or  $res1['SAC'][0]=="TECHM"){
		$out.="<option>".$res1['SAC'][0]." (SAC)</option>";
	}
	if ($out==""){
		$out= "error";
	}
	echo $res1['CON'][0].$out;

}
?>
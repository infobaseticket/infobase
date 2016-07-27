<?PHP
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

/*********************************************************************************************************************/
function get_siteinfo($sitename){
	global $conn_Infobase;
	$query="select * from VW_NET1_ALL_NEWBUILDS
				where
					SIT_UDK like '%".$sitename."%'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	return $res1;
}

/*********************************************************************************************************************/
function get_BSDS_info($lognode){
	global $conn_Infobase, $config;

	$query = "select * from ".$config['table_asset_geninfo']." WHERE SITEKEY=".$lognode;
	//echo "$query<br>";
	//die;
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	return $res1;
}
/********************************************************************************************************************/
function get_BSDS_info2($Sitekey){
	global $conn_Infobase,$config;
	$query = "select * from ".$config['table_asset_geninfo']." WHERE SITEKEY=".$Sitekey;
	//echo "$query<br>";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	return $res1;
}
/*********************************************************************************************************************/
function get_coordinates($fname_pre){

	global $conn_Infobase;

	$query1 = "select * from ASSET_COORD WHERE SITE LIKE '%".$fname_pre."%'";
	//echo "$query1 <br>";
   	$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	$ret['longitude']=$res1['XLAMBERT'][0];
	$ret['latitude']=$res1['YLAMBERT'][0];
	$ret['x']=$res1['X'][0];
	$ret['y']=$res1['Y'][0];
	/*
	$last_line =shell_exec("coord_conv $longitude $latitude");
	$longlat=explode(" ",$last_line);
	//echo "<pre>".print_r($longlat,true);
	$ret['longitude']=$longlat[0];
	$ret['latitude']=$longlat[1];
	*/
	return $ret;
}
/*****************************************************************/
?>
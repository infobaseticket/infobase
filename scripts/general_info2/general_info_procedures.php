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
/***************************************************************************************************************/
/*
function get_BSDS_site_funded($BOB_refresh,$candidate,$siteID){
	global $conn_Infobase;
	$query = "SELECT * FROM INFOBASE.BSDS_SITE_FUNDED2 WHERE SITEID='".$candidate."' AND RAFID IS NOT NULL";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);

	}
	
}
/*********************************************************************************************************************/
/*
function get_latestdate_BSDS($siteID){
	global $conn_Infobase;
	//GET THE LATEST BSDSKEY
	$query_latest = "SELECT MAX(BSDSKEY) as BSDSKEY FROM BSDS_GENERALINFO WHERE SITEID
	= '".$siteID."' AND
	CHANGE_DATE=(Select MAX(CHANGE_DATE)
	FROM BSDS_GENERALINFO WHERE SITEID = '".$siteID."')";
	//echo $query;
	$stmt_latest = parse_exec_fetch($conn_Infobase, $query_latest, $error_str, $res_latest);
	if (!$stmt_latest) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt_latest);
		//echo count($res_latest['BSDSKEY']);
		if (count($res_latest['BSDSKEY'])>1){
		?>
		<font color='red'>Something was wrong with Infobase for this site!<br></font>
		<font size=1>Too many data found in BSDS_GENERALINFO for site <?=$siteID?></font><br>
		Please try to reload this site again.
		<? die;
		}else{
			return $res_latest['BSDSKEY'][0];
		}
	}
}
/*********************************************************************************************************************/
/*
function insert_site_funded_BSDS($latest_BSDS,$site){
	global $conn_Infobase;
	$query="DELETE FROM INFOBASE.BSDS_SITE_FUNDED WHERE SITEID='".$site."' OR BSDSKEY='".$latest_BSDS."'";
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}
	OCICommit($conn_Infobase);
	$query="INSERT INTO INFOBASE.BSDS_SITE_FUNDED (BSDSKEY, SITEID, UPDATE_DATE, COPIED) VALUES ('$latest_BSDS','".$site."',SYSDATE,'NO')";
	echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}
	OCICommit($conn_Infobase);
	return $IDNR;
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
function analyseTechno($technos){
	if(strpos($technos, "GSM900")!==false or strpos($technos, "G9")!==false or strpos($technos, "GSM9")!==false or strpos($technos, "EGS")!==false){
		$G9="G9+";
	}else{ //FOR NB 900&
		$start = 0;
		while(($pos = strpos($technos, "900", $start)) !== false){
		   	if((substr($technos,$pos-4,4)!="UMTS" && substr($technos,$pos-1,1)!="U" 
		   		&& substr($technos,$pos-3,3)!="LTE" && substr($technos,$pos-1,1)!="L")
		   		 or $pos==0){
				$G9="G9+";
		   	}
			$start = $pos+1;
		}
	}
	if(strpos($technos, "GSM1800")!==false or strpos($technos, "G18")!==false or strpos($technos, "GSM18")!==false or strpos($technos, "DCS")!==false){
		$G18="G18+";
	}else{ //FOR NB 1800&
		$start = 0;
		while(($pos = strpos($technos, "1800", $start)) !== false){
		   	if((substr($technos,$pos-4,4)!="UMTS" && substr($technos,$pos-1,1)!="U" 
		   		&& substr($technos,$pos-3,3)!="LTE" && substr($technos,$pos-1,1)!="L")
		   		 or $pos==0){
				$G18="G18+";
		   	}
			$start = $pos+1;
		}
	}
	if(strpos($technos, "UMTS900")!==false or strpos($technos, "UMT900")!==false or strpos($technos, "U9")!==false or strpos($technos, "UMTS9")!==false){
		$U9="U9+";
	}
	if(strpos($technos, "UMTS2100")!==false or strpos($technos, "UMT2100")!==false or strpos($technos, "U21")!==false 
		or strpos($technos, "UMTS21")!==false or strpos($technos, "HSPX")!==false or strpos($technos, "HSDPA")!==false){
		$U21="U21+";
	}else{//FOR NB UMTS&
		$posUMTS=strpos($technos, "UMTS");
		if(substr($technos,$posUMTS+4,1)!="9" && $posUMTS!==false){
			$U21="U21+";
		}
	}
	if(strpos($technos, "LTE800")!==false or strpos($technos, "L8")!==false or strpos($technos, "LTE8")!==false){
		$L8="L8+";
	}
	if(strpos($technos, "LTE1800")!==false or strpos($technos, "L18")!==false or strpos($technos, "LTEX")!==false or strpos($technos, "LTE18")!==false){
		$L18="L18+";
	}
	if(strpos($technos, "LTE2600")!==false or strpos($technos, "L26")!==false or strpos($technos, "LTE26")!==false){
		$L26="L26+";
	}
	$technosNET1=substr($G9.$G18.$U9.$U21.$L8.$L18.$L26,0,-1);
	return $technosNET1;
}
?>
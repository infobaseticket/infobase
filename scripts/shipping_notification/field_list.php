<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_GET["field"]=="products"){

	$query = "select KPNGB_PROD_REF,DESCRIPTION from SN_MATERIAL_LIST WHERE upper(KPNGB_PROD_REF) LIKE '%".strtoupper($_GET["q"])."%' OR  upper(DESCRIPTION) LIKE '%".strtoupper($_GET["q"])."%' ORDER BY KPNGB_PROD_REF";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	if (count($res1['KPNGB_PROD_REF'])!=0){
		foreach ($res1['KPNGB_PROD_REF'] as $key=>$attrib_id) {
		    $arr[$key]['id']=$res1['KPNGB_PROD_REF'][$key];
		    $arr[$key]['text']='('.$res1['KPNGB_PROD_REF'][$key].') '.$res1['DESCRIPTION'][$key];
		}
	}else{
		$arr[0]['id']=0;
		$arr[0]['text']="No results found...";
	}
	echo json_encode($arr);
}
?>
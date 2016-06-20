<?PHP
require_once ($_SERVER['DOCUMENT_ROOT'].'/include/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BSDS_view,BASE_MP","");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");

/*********************************************************************************************************************/
function get_gain($type,$ant){

	global $conn_Infobase;

	if ($ant!=""){
		$query = "SELECT ANTENNA, GAIN, HOR, VER, BAND FROM INFOBASE.MBANTREF1 WHERE (ANTENNA like substr('".$ant."',0,7) OR ANTENNA like substr('".$ant."',0,5)) AND band='".$type."'";
		//echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
		    return $res1;
		}
	}
}
?>
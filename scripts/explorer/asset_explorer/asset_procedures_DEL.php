<?PHP
require_once('/srv/www/htdocs/include/config.php');
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
$type=$_GET['type'];

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

function get_data($type){
	global $conn_Infobase;
	switch($type){
		case "2GSITE":
			$query = "select * from VW2GSITE WHERE SITEKEY='".$_SESSION['Sitekey']."'";
		break;

		case "CELL2G":

		break;
	}


	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, &$res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		$ncols = oci_num_fields($stmt);
	    OCIFreeStatement($stmt);
	}
	$data[data]=$res1;
	$data[numcols]=$ncols;
	return $data;

}
?>

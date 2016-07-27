<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$siteID=$_POST['siteID'];
$query="SELECT * FROM  EVENTCAL WHERE SITEID LIKE '%".$_POST['siteID']."%'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}
echo "Event:".$res1['EVENT'][0]."<br>";
echo "Sart of event:".$res1['STARTDATE'][0]."<br>";
echo "End of event:".$res1['ENDDATE'][0]."<br>";
echo "X:".$res1['X'][0]."<br>";
echo "Y:".$res1['Y'][0]."<br>";
echo "INFO:".$res1['EXTRAINFO'][0]."<br>";

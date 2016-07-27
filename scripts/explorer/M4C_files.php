<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Base_RF,Base_TXMN,Base_delivery,Base_other,Base_risk,Partner,Administrators","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query="SELECT FILENAME, CANDIDATE,UPGNR, EXTENSION FROM RAN_SCAN_TODAY WHERE FILETYPE='".$_POST['filetype']."' AND PARTNER='M4C_RAN' ORDER BY CANDIDATE";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $Amounttypes=count($resT['FILENAME']);
}
for ($i = 0; $i <$Amounttypes; $i++) { 
     echo "<b>".$resT['CANDIDATE'][$i]. " [". $resT['UPGNR'][$i]. "]:</b>  ".$resT['FILENAME'][$i].".".$resT['EXTENSION'][$i]."<br>";
}

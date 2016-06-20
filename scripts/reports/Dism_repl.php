<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query3 = "select * from IMPORT_STATUS WHERE type='DISM_REPL'";
//echo $query3."<br>";					
$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
if (!$stmt3) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt3);
}
?>
<br>Latest import <?=$res3['RUN'][0]?> 

<? if ($res3['STATUS'][0]){
	echo "<br><font color=red><b>".$res3['STATUS'][0]."</b></font>";
}
?>

<br><br>
<p>Click <a href="<?=$config['dism_repl_report_url']?>" target="_new">here</a> to download file in CSV format.</p><br>
<p><i>The file is updated daily at 8h15.</i></p>
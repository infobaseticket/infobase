<?php
include_once('/var/www/html/bsds/config.php');
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query = "Select * FROM BSDS_RAF_COF t1 LEFT JOIN BSDS_RAFV2 t2 ON t1.RAFID=t2.RAFID LEFT JOIN COF_MASTERFILE t3 
on t1.MATERIAL_CODE=t3.MATERIAL AND t1.ACQCON=t3.ACQ_CON LEFT JOIN MASTER_REPORT ON NET1_LINK=N1_CANDIDATE OR NET1_LINK=N1_UPGNR
LEFT JOIN COST_CENTERS ON N1_SITETYPE = SITETYPE
WHERE EXPORTED=0 AND N1_STATUS='IS' AND (
	(COF_ACQ = 'BASE OK' AND t1.ACQCON='ACQ')
	OR (COF_CON = 'BASE OK' AND t1.ACQCON='CON')
) AND ENG IS NOT NULL AND N1_SITETYPE IS NOT NULL
ORDER BY t1.RAFID, t3.ORDERCOL";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}

if(count($res1['RAFID'])>0){
?>

<button value='Reject' class='btn btn-success' id="genrateCOF">GENRATE COF</button><br><br>
<div id="outputCOFgenerator"></div>
<?php
}else{
	echo "NO COF to generate";
}
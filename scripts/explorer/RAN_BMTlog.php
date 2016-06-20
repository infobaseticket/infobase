<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
?>


<table class="table table-striped table-condensed">
<thead>
	<tr>
		<th>ANALYSEDATE</th>
		<th>SUBPATH</th>
		<th>FILENAME</th>
		<th>EXTENSION</th>
	</tr>
</thead>
<tbody>
<?php

//
//echo $query."<br>";
$query=$query = "SELECT SUBPATH,ANALYSEDATE , FILENAME,EXTENSION FROM RAN_SCAN_LOG WHERE ANALYSEDATE > SYSDATE-1  AND  PARTNER='BENCHMARK_SUBMIT' AND (ACTION='ADDED' OR ACTION='CHANGED') ORDER BY ANALYSEDATE";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}
foreach ($res1['ANALYSEDATE'] as $key => $value) {
	echo "<tr><td>".$res1['ANALYSEDATE'][$key]."</td><td>".$res1['SUBPATH'][$key]."</td><td>".$res1['FILENAME'][$key]."</td><td>".$res1['EXTENSION'][$key]."</td></tr>";
}
?>
</tbody>
</table>

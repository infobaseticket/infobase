<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","BASE_delivery,Administrators","");
include($config['sitepath_abs'].'/include/config_db.php');
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");

$query="SELECT COUNT(SITE) AS TOTAL from SWITCH_2G_RXMOP_TG a RIGHT JOIN SWITCH_3G_RBS b ON substr(RSITE,0,6) =  substr(SITE,0,6) 
WHERE RSITE IS NULL";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$TOT_amount_of_3G=$res['TOTAL'][0];	
}

$query="SELECT COUNT(RSITE) AS TOTAL from SWITCH_2G_RXMOP_TG a LEFT JOIN SWITCH_3G_RBS b ON substr(RSITE,0,6) =  substr(SITE,0,6) WHERE SITE IS NULL";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$TOT_amount_of_2G=$res['TOTAL'][0];	
}

$query="SELECT COUNT(DISTINCT(SITE)) AS TOTAL from SWITCH_2G_RXMOP_TG a INNER JOIN SWITCH_3G_RBS b ON substr(RSITE,0,6) =  substr(SITE,0,6)";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$TOT_amount_of_2G3G=$res['TOTAL'][0];	
}
?>
<html>
<head>
<link rel="stylesheet" href="scripts/reports/reports.css" type="text/css">
</head>
<body>
	<h1>Site counts</h1>
<table width="300px">
	<thead>
		<th>Technology</th>
		<th>Amount of sites</th>
	</thead>
	<tr>
		<td>3G only</td>
		<td><?=$TOT_amount_of_3G?></td>
	</tr>
	<tr>
		<td>2G only</td>
		<td><?=$TOT_amount_of_2G?></td>
	</tr>
	<tr>
		<td>2G+3G</td>
		<td><?=$TOT_amount_of_2G3G?></td>
	</tr>
</table>
</body>
</html>
<?
oci_close($conn_Infobase);

?>
<?
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/01/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_JAN_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/02/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_FEB_tot=$res1['AMOUNT'][0];
}

$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/03/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_MAR_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/04/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_APR_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/05/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_MAY_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/06/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_JUN_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/07/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_JUL_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/08/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_AUG_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/09/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_SEP_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/10/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_OCT_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/11/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_NOV_tot=$res1['AMOUNT'][0];
}
$query1 = "Select SUM(TOTALVALUE) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/12/%' AND NAME1 LIKE '%ERICSSON%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_DEC_tot=$res1['AMOUNT'][0];
}

?>
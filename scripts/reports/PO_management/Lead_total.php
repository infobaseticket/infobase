<?
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/01/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_JAN_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/02/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_FEB_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/03/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_MAR_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/04/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_APR_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/05/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_MAY_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/06/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_JUN_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/07/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_JUL_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/08/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_AUG_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/09/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_SEP_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/10/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_OCT_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/11/%'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_NOV_tot=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/12/%'";
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
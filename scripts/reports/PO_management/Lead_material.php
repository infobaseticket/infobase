<?
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/01/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_JAN_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/02/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_FEB_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/03/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_MAR_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/04/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_APR_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/05/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_MAY_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/06/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_JUN_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/07/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_JUL_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/08/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_AUG_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/09/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_SEP_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/10/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_OCT_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/11/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_NOV_mat=$res1['AMOUNT'][0];
}
$query1 = "Select AVG(LEADTIME) AS AMOUNT FROM BSDS_PO_MANAGEMENT WHERE PODAT LIKE '%/12/%' AND MATERIAL1 IS NOT NULL AND REQUISNR !='S. Hanrez'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$PO_DEC_mat=$res1['AMOUNT'][0];
}

?>
<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
//protect("","Radioplanners,BASE_MP","");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
//error_reporting(E_ALL);

include $config['sitepath_abs']."/include/PHP/open-flash-chart-2/php-ofc-library/open-flash-chart.php";

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL AND trim(WOR_LKP_WCO_CODE)='ASC'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_ASC=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL AND trim(WOR_LKP_WCO_CODE)='DCS'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_DCS=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL AND trim(WOR_LKP_WCO_CODE)='CAB'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_CAB=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL AND trim(WOR_LKP_WCO_CODE)='CTX'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_CTX=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL AND trim(WOR_LKP_WCO_CODE)='UMTS'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_UMTS=$res1['AMOUNT'][0];
}
$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL AND trim(WOR_LKP_WCO_CODE)='DCS'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_DCS=$res1['AMOUNT'][0];
}
$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL AND trim(WOR_LKP_WCO_CODE)='HSDPA'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_HSDPA=$res1['AMOUNT'][0];
}
$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL AND trim(WOR_LKP_WCO_CODE)='CWK'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_CWK=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL AND trim(WOR_LKP_WCO_CODE)='ANT'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_ANT=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NOT NULL AND trim(WOR_LKP_WCO_CODE)='UMT6'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_UMT6=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NOT NULL AND trim(WOR_LKP_WCO_CODE)='EG6'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_EG6=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NOT NULL AND trim(WOR_LKP_WCO_CODE)='EGS'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_EGS=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NOT NULL AND trim(WOR_LKP_WCO_CODE)='HSPX'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_HSPX=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE WOR_DOM_WOS_CODE IN ('IS') 
AND trim(U353) IS  NULL AND trim(U709) IS NOT NULL AND trim(U905) IS NOT NULL
AND trim(WOR_LKP_WCO_CODE) NOT LIKE 'ASC' AND trim(WOR_LKP_WCO_CODE) NOT LIKE 'DCS' AND trim(WOR_LKP_WCO_CODE) NOT LIKE 'CAB'
AND trim(WOR_LKP_WCO_CODE) NOT LIKE 'UMTS' AND trim(WOR_LKP_WCO_CODE) NOT LIKE 'HSDPA' AND trim(WOR_LKP_WCO_CODE) NOT LIKE 'CWK'
AND trim(WOR_LKP_WCO_CODE) NOT LIKE 'ANT' AND trim(WOR_LKP_WCO_CODE) NOT LIKE 'CTX'";
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG_OTHER=$res1['AMOUNT'][0];
}

$title = new title( 'Buffer Upgrades' );
$title->set_style( "{font-size: 12px; font-family: Times New Roman; font-weight: bold; color: #FF9900; text-align: center;}" );

$pie = new pie();
$pie->set_alpha(0.6);
$pie->set_start_angle(30);
$pie->add_animation( new pie_fade() );

$pie->set_tooltip( '#val# of #total#<br>#percent# of 100%' );
$pie->set_colours( array('#3399FF','#3366CC','#FFCC00','#FF9900','#FF00FF','#990099','#99CC00','#669966','#00FFFF') ); 
	
$pie->set_values( array(
	new pie_value(intval($BUFFER_UPG_ASC), "ASC ($BUFFER_UPG_ASC)"),
	new pie_value(intval($BUFFER_UPG_DCS), "DCS ($BUFFER_UPG_DCS)"),
	new pie_value(intval($BUFFER_UPG_CAB), "CAB ($BUFFER_UPG_CAB)"),
	new pie_value(intval($BUFFER_UPG_CTX), "CTX ($BUFFER_UPG_CTX)"),
	new pie_value(intval($BUFFER_UPG_UMTS), "UMTS ($BUFFER_UPG_UMTS)"),
	new pie_value(intval($BUFFER_UPG_HSDPA), "HSDPA ($BUFFER_UPG_HSDPA)"),
	new pie_value(intval($BUFFER_UPG_CWK), "CWK ($BUFFER_UPG_CWK)"),
	new pie_value(intval($BUFFER_UPG_ANT), "ANT ($BUFFER_UPG_ANT)"),
	new pie_value(intval($BUFFER_UPG_EG6), "EG6 ($BUFFER_UPG_EG6)"),
	new pie_value(intval($BUFFER_UPG_UMT6), "UMT6 ($BUFFER_UPG_UMT6)"),
	new pie_value(intval($BUFFER_UPG_EGS), "EGS ($BUFFER_UPG_EGS)"),
	new pie_value(intval($BUFFER_UPG_HSPX), "UMT6 ($BUFFER_UPG_HSPX)"),
	new pie_value(intval($BUFFER_UPG_OTHER), "OTHER ($BUFFER_UPG_OTHER)")
	));
//$pie->set_values($val);


$chart = new open_flash_chart();
$chart->set_bg_colour( '#FFFFFF' );
$chart->set_title( $title );
$chart->add_element( $pie );

$chart->x_axis = null;

echo $chart->toPrettyString();
?>
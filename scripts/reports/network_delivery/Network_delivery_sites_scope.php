<?PHP
require_once("/var/www/html/include/config.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
//error_reporting(E_ALL);

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query1 = "Select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_NEWBUILDS WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
AND trim(A04) IS NOT NULL
AND trim(A353) IS NULL
AND trim(A80) IS NULL
AND trim(A81) IS NULL
AND trim(WOE_RANK) = '1' 
AND ((trim(A105) IS NULL and trim(A709) is not null) or (trim(A105) IS  NULL and trim(A709) is  null) OR (trim(A105) IS not NULL and trim(A709) is null))";
if ($_GET['phases']=="Phase 1"){
$query1 .= "AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= "AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1 .= "AND trim(A04) LIKE '%".$_GET['year']."%')";
if ($_GET['split']=="WIP"){
	$query1 .= " AND trim(SAC) IS NOT NULL AND trim(SAC)!='ALU' and  trim(WIP)='ALU'"; 
}else if ($_GET['split']=="ALU"){
	$query1 .= " AND trim(SAC)='ALU'"; 
}else if ($_GET['split']=="KPNGB"){
	$query1 .= " AND trim(SAC)!='ALU' AND AND trim(WIP)!='ALU' "; 
}
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$ACQ_NEW=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE ((WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
AND trim(U001) IS NOT NULL
AND trim(U353) IS NULL 
AND trim(U380) IS NULL 
AND trim(U381) IS NULL 
AND ((trim(U405) IS NULL and trim(U709) is not null) or (trim(U405) IS  NULL and trim(U709) is  null) OR (trim(U405) IS not NULL and trim(U709) is null))
AND trim(WOR_LKP_WCO_CODE) IN('ANT','ASC','CAB','CTX','CWK','DCS','EGS','UMTS','HSDPA','HSPX','RPT','LLA','IND','EG6','UMT6')";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1 .= " AND trim(U001) LIKE '%".$_GET['year']."%' 
AND ((trim(U405) IS NULL and trim(U709) LIKE '%".$_GET['year']."%') OR (trim(U405) LIKE '%".$_GET['year']."%' and trim(U709) is null))
) OR (
WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
AND trim(U001) IS NOT NULL
AND trim(U353) IS NULL 
AND trim(U162) IS NULL 
AND trim(U220) IS NULL 
AND ((trim(U104) IS NULL and trim(U100) is not null) or (trim(U104) IS  NULL and trim(U100) is  null) OR (trim(U104) IS not NULL and trim(U100) is null))
AND trim(WOR_LKP_WCO_CODE) IN('SHA', 'SHB', 'SHC', 'SHM', 'SHP', 'SHR')";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1 .= " AND trim(U001) LIKE '%".$_GET['year']."%' 
AND ((trim(U104) IS NULL and trim(U100) LIKE '%".$_GET['year']."%') OR (trim(U104) LIKE '%".$_GET['year']."%' and trim(U100) is null))
))";
if ($_GET['split']=="WIP"){
	$query1 .= " AND trim(SAC) IS NOT NULL AND trim(SAC)!='ALU' and  trim(WIP)='ALU'"; 
}else if ($_GET['split']=="ALU"){
	$query1 .= " AND trim(SAC)='ALU'"; 
}else if ($_GET['split']=="KPNGB"){
	$query1 .= " AND trim(SAC)!='ALU' AND AND trim(WIP)!='ALU' "; 
}
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$ACQ_UPG=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_NEWBUILDS WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH')
AND trim(A709) IS NOT NULL 
AND trim(A105) IS NOT NULL 
AND trim(A353) IS NULL  
AND trim(A80) IS NULL  
AND trim(A81) IS NULL 
AND trim(WOE_RANK) ='1'";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1.=")";
if ($_GET['split']=="WIP"){
	$query1 .= " AND trim(SAC) IS NOT NULL AND trim(SAC)!='ALU' and  trim(WIP)='ALU'"; 
}else if ($_GET['split']=="ALU"){
	$query1 .= " AND trim(SAC)='ALU'"; 
}else if ($_GET['split']=="KPNGB"){
	$query1 .= " AND trim(SAC)!='ALU' AND AND trim(WIP)!='ALU' "; 
}
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_NEW=$res1['AMOUNT'][0];
}	

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE ((WOR_DOM_WOS_CODE IN ('IS','SL','OH')
AND trim(U353) IS  NULL 
AND UPPER(trim(U001)) not like '%IN //%'
AND trim(U709) IS NOT NULL 
AND trim(U405) IS NOT NULL 
AND trim(U353) IS NULL 
AND trim(U380) IS NULL  
AND trim(U381) IS NULL 
AND trim(WOR_LKP_WCO_CODE) IN('ANT','ASC','CAB','CTX','CWK','DCS','EGS','UMTS','HSDPA','HSPX','RPT','LLA','IND','EG6','UMT6')";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
} 
$query1.=" AND trim(U380) IS NULL
) OR (
WOR_DOM_WOS_CODE IN ('IS','SL','OH' ) 
AND trim(U353) IS  NULL 
AND UPPER(trim(U001)) not like '%IN //%'
AND trim(U100) IS NOT NULL 
AND trim(U104) IS NOT NULL 
AND trim(U353) IS NULL 
AND trim(U162) IS NULL  
AND trim(U220) IS NULL 
AND trim(WOR_LKP_WCO_CODE) IN('SHA', 'SHB', 'SHC', 'SHM', 'SHP', 'SHR')";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1 .= " AND trim(U162) IS NULL
))";
if ($_GET['split']=="WIP"){
	$query1 .= " AND trim(SAC) IS NOT NULL AND trim(SAC)!='ALU' and  trim(WIP)='ALU'"; 
}else if ($_GET['split']=="ALU"){
	$query1 .= " AND trim(SAC)='ALU'"; 
}else if ($_GET['split']=="KPNGB"){
	$query1 .= " AND trim(SAC)!='ALU' AND AND trim(WIP)!='ALU' "; 
}
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$BUFFER_UPG=$res1['AMOUNT'][0];
}	

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_NEWBUILDS WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH')
AND trim(A353) IS  NOT NULL  
AND trim(A80) IS NULL  
AND trim(A81) IS NULL 
AND trim(WOE_RANK) = '1'";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1.=")";
if ($_GET['split']=="WIP"){
	$query1 .= " AND trim(CON) IS NOT NULL AND trim(CON)!='ALU' and  trim(WIP)='ALU'"; 
}else if ($_GET['split']=="ALU"){
	$query1 .= " AND trim(CON)='ALU'"; 
}else if ($_GET['split']=="KPNGB"){
	$query1 .= " AND trim(CON)!='ALU' AND AND trim(WIP)!='ALU' "; 
}
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_NEW=$res1['AMOUNT'][0];
}	

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE ((WOR_DOM_WOS_CODE IN ('IS','SL','OH')
AND trim(U353) IS  NOT NULL 
AND trim(U380) IS NULL 
AND trim(U381) IS NULL
AND trim(WOR_LKP_WCO_CODE) IN('ANT','ASC','CAB','CTX','CWK','DCS','EGS','UMTS','HSDPA','HSPX','RPT','LLA','IND','EG6','UMT6')";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1.=") OR (
WOR_DOM_WOS_CODE IN ('IS','SL','OH' ) 
AND trim(U353) IS  NOT NULL 
AND trim(U162) IS NULL 
AND trim(U220) IS NULL
AND trim(WOR_LKP_WCO_CODE) IN('SHA', 'SHB', 'SHC', 'SHM', 'SHP', 'SHR' )";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1 .= "))";
if ($_GET['split']=="WIP"){
	$query1 .= " AND trim(CON) IS NOT NULL AND trim(CON)!='ALU' and  trim(WIP)='ALU'"; 
}else if ($_GET['split']=="ALU"){
	$query1 .= " AND trim(CON)='ALU'"; 
}else if ($_GET['split']=="KPNGB"){
	$query1 .= " AND trim(CON)!='ALU' AND AND trim(WIP)!='ALU' "; 
}
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_NEWBUILDS WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH')
AND trim(A81) LIKE '%".$_GET['year']."' 
AND trim(A81) IS NOT NULL 
AND trim(WOE_RANK) ='1' ";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1.=")";
if ($_GET['split']=="WIP"){
	$query1 .= " AND trim(CON) IS NOT NULL AND trim(CON)!='ALU' and  trim(WIP)='ALU'"; 
}else if ($_GET['split']=="ALU"){
	$query1 .= " AND trim(CON)='ALU'"; 
}else if ($_GET['split']=="KPNGB"){
	$query1 .= " AND trim(CON)!='ALU' AND AND trim(WIP)!='ALU' "; 
}
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$FAC_NEW=$res1['AMOUNT'][0];
}

$query1 = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES WHERE ((WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
AND trim(U381) LIKE '%".$_GET['year']."' 
AND trim(U381) IS NOT NULL
AND trim(WOR_LKP_WCO_CODE) IN('ANT','ASC','CAB','CTX','CWK','DCS','EGS','UMTS','HSDPA','HSPX','RPT','LLA','IND','EG6','UMT6')";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1.=")OR(
WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
AND trim(U381) LIKE '%".$_GET['year']."' 
AND trim(U381) IS NOT NULL
AND trim(WOR_LKP_WCO_CODE) IN('SHA', 'SHB', 'SHC', 'SHM', 'SHP', 'SHR')";
if ($_GET['phases']=="Phase 1"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}else if ($_GET['phases']=="Phase 2"){
$query1 .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$_GET['phases']."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$_GET['phases']."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$_GET['phases']." +%')"; 
}
$query1.="))";
if ($_GET['split']=="WIP"){
	$query1 .= " AND trim(CON) IS NOT NULL AND trim(CON)!='ALU' and  trim(WIP)='ALU'"; 
}else if ($_GET['split']=="ALU"){
	$query1 .= " AND trim(CON)='ALU'"; 
}else if ($_GET['split']=="KPNGB"){
	$query1 .= " AND trim(CON)!='ALU' AND AND trim(WIP)!='ALU' "; 
}
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$FAC_UPG=$res1['AMOUNT'][0];
}	

$title = new title( 'Sites in scope '.$_GET['year'].' '.$_GET['phases'].' '.$_GET['split'] );
$title->set_style( "{font-size: 12px; font-family: Times New Roman; font-weight: bold; color: #A2ACBA; text-align: center;}" );

$pie = new pie();
$pie->set_alpha(0.6);
$pie->set_start_angle(30);
$pie->add_animation( new pie_fade() );

$pie->set_tooltip( '#val# of #total#<br>#percent# of 100%' );
$pie->set_colours( array('#3399FF','#FFCC00','#3366CC','#FF9900','#FF00FF','#990099','#99CC00','#669966') ); 
	
//echo "$ACQ_NEW|$BUFFER_NEW|$ACQ_UPG|$BUFFER_UPG|$CON_NEW|$CON_UPG|$FAC_NEW|$FAC_UPG";
$pie->set_values( array(
	new pie_value(intval($ACQ_NEW), "ACQ NEW ($ACQ_NEW)"),	
	new pie_value(intval($BUFFER_NEW), "BUFFER NEW ($BUFFER_NEW)"),
	new pie_value(intval($ACQ_UPG), "ACQ UPG ($ACQ_UPG)"),
	new pie_value(intval($BUFFER_UPG), "BUFFER UPG ($BUFFER_UPG)"),
	new pie_value(intval($CON_NEW), "CON NEW ($CON_NEW)"),
	new pie_value(intval($CON_UPG), "CON UPG ($CON_UPG)"),
	new pie_value(intval($FAC_NEW), "FAC NEW ($FAC_NEW)"),
	new pie_value(intval($FAC_UPG), "FAC UPG ($FAC_UPG)")
	));
//$pie->set_values($val);

$pie->on_click('pie_slice_clicked');

$chart = new open_flash_chart();
$chart->set_bg_colour( '#FFFFFF' );
$chart->set_title( $title );
$chart->add_element( $pie );

$chart->x_axis = null;

$data_scope= $chart->toPrettyString();
?>
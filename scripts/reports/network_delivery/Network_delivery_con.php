<?PHP
require_once("/var/www/html/include/config.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
//error_reporting(E_ALL);

//include $config['sitepath_abs']."/include/PHP/open-flash-chart-2/php-ofc-library/open-flash-chart.php";

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

?>
<script language="javascript">

function barClicked_con( index )
{
	//alert( 'Bar Slice '+ index +' clicked');
	window.open( "scripts/reports/Network_delivery_details.php?slice="+index+"&report=con&year=<?=$_GET['year']?>&split=<?=$_GET['split']?>&phases=<?=$_GET['phases']?>");
}
</script>
<?
$query1=query_buffer_new("TOTAL","MACROCELLS",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_NEW_MACRO=$res1['AMOUNT'][0];
}

$query1=query_con_new("TOTAL","MICROCELLS",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_NEW_MICRO=$res1['AMOUNT'][0];
}

$query1=query_con_new("TOTAL","INDOOR",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_NEW_INDOOR=$res1['AMOUNT'][0];
}


$query1=query_con_new("TOTAL","REPLACEMENTS",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_NEW_REPL=$res1['AMOUNT'][0];
}
	


$query1=query_con_new("TOTAL","EMPTY",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_NEW_EMPTY=$res1['AMOUNT'][0];
}	

$query1 = query_con_upg("TOTAL","'ASC'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_ASC=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'DCS'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_DCS=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'CAB'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_CAB=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'CTX'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_CTX=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'UMTS'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_UMTS=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'HSDPA'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_HSDPA=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'CWK'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_CWK=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'ANT'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_ANT=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'UMT6'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_UMT6=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'EG6'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_EG6=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'EGS'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_EGS=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'HSPX'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_HSPX=$res1['AMOUNT'][0];
}


$query1 = query_con_upg("TOTAL","'SHA', 'SHB', 'SHC', 'SHM', 'SHP', 'SHR'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_SH=$res1['AMOUNT'][0];
}

$query1 = query_con_upg("TOTAL","'RPT','LLA','IND'",$_GET['split'],$_GET['year'],$_GET['phases']);
//echo "<br><br>".$query1;
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$CON_UPG_OTHER=$res1['AMOUNT'][0];
}


$title = new title( 'Construction New Builds + Upgrades '.$_GET['year'].' '.$_GET['phases'].' '.$_GET['split'] );
$title->set_style( "{font-size: 12px; font-family: Times New Roman; font-weight: bold; color: #000000; text-align: center;}" );

$animation_1= 'pop';
$delay_1    = 0.5;
$cascade_1    = 1;

$bar = new bar_3d();
$data = array();
$tmp = new bar_filled_value(intval($CON_NEW_MACRO));
$tmp->on_click("barClicked_buffer"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_NEW_MICRO));
$tmp->on_click("barClicked_buffer"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_NEW_INDOOR));
$tmp->on_click("barClicked_buffer"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_NEW_REPL));
$tmp->on_click("barClicked_buffer"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_NEW_EMPTY));
$tmp->on_click("barClicked_buffer"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_ASC));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_DCS));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_CAB));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_CTX));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_UMTS));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_UMT6));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_HSDPA));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_HSPX));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_CWK));
$tmp->set_colour( '#990099' );
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_ANT));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_EG6));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_EGS));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_SH));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$tmp = new bar_filled_value(intval($CON_UPG_OTHER));
$tmp->set_colour( '#990099' );
$tmp->on_click("barClicked_con"); 
$data[] = $tmp;
$bar->set_colour('#990099');
$tmp->on_click("barClicked_con"); 
$bar->set_values( $data );
$bar->set_colour('#FF00FF');
$bar->set_on_show(new bar_on_show($animation_1, $cascade_1, $delay_1));

$max= max($CON_NEW_MACRO,$CON_NEW_MICRO,$CON_NEW_INDOOR,$CON_NEW_REPL,$CON_NEW_EMPTY,$CON_UPG_ASC,$CON_UPG_DCS,$CON_UPG_CAB,$CON_UPG_CTX,$CON_UPG_UMTS,$CON_UPG_UMT6,$CON_UPG_HSDPA,$CON_UPG_HSPX,$CON_UPG_CWK,$CON_UPG_ANT,$CON_UPG_EG6,$CON_UPG_EGS,$CON_UPG_SH,$CON_UPG_OTHER); 
if($max>100){
	$step='100';
}else if($max<=10){
	$step='1';
}else{
	$step='10';
}

$y = new y_axis(); 
$y->set_range( 0, $max, $step );

$x = new x_axis();
$x->set_labels_from_array( array('MACRO','MICRO','INDOOR','REPL','EMPTY','ASC','DCS','CAB','CTX','UMTS','UMT6','HSDPA','HSPX','CWK','ANT','EG6','EGS','SH','OTHER') );


$chart = new open_flash_chart();
$chart->set_bg_colour( '#FFFFFF' );
$chart->set_title( $title );
$chart->add_element( $bar );

$chart->set_y_axis( $y );
$chart->set_x_axis( $x );

$data_con=$chart->toPrettyString();

?>
<?php
include("/var/www/html/include/config.php");

include $config['sitepath_abs']."/include/PHP/open-flash-chart-2/php-ofc-library/open-flash-chart.php";

include('PO_management_number.php');
include('PO_management_leadtime.php');
include('PO_management_euro.php');
include('PO_management_euro_ericsson.php');
include('PO_management_euro_alu.php');
include('PO_management_services.php');
?>
<html>
<head>
<link rel="stylesheet" href="scripts/reports/reports.css" type="text/css">
<script type="text/javascript">

swfobject.embedSWF(
  "<?=$config['sitepath_url']?>/include/PHP/open-flash-chart-2/open-flash-chart.swf", "po_chart",
  "950", "350", "9.0.0", "expressInstall.swf",
  {"get-data":"get_data_1","id":"chart1"} );
  
swfobject.embedSWF(
  "<?=$config['sitepath_url']?>/include/PHP/open-flash-chart-2/open-flash-chart.swf", "leadtime_chart",
  "950", "350", "9.0.0", "expressInstall.swf",
  {"get-data":"get_data_2","id":"chart2"} );

swfobject.embedSWF(
  "<?=$config['sitepath_url']?>/include/PHP/open-flash-chart-2/open-flash-chart.swf", "euro_chart",
  "950", "350", "9.0.0", "expressInstall.swf",
  {"get-data":"get_data_3","id":"chart3"} );
  
swfobject.embedSWF(
  "<?=$config['sitepath_url']?>/include/PHP/open-flash-chart-2/open-flash-chart.swf", "euro_ericsson_chart",
  "950", "350", "9.0.0", "expressInstall.swf",
  {"get-data":"get_data_4","id":"chart4"} );
  
swfobject.embedSWF(
  "<?=$config['sitepath_url']?>/include/PHP/open-flash-chart-2/open-flash-chart.swf", "euro_alu_chart",
  "950", "350", "9.0.0", "expressInstall.swf",
  {"get-data":"get_data_5","id":"chart5"} );
swfobject.embedSWF(
  "<?=$config['sitepath_url']?>/include/PHP/open-flash-chart-2/open-flash-chart.swf", "euro_services",
  "950", "350", "9.0.0", "expressInstall.swf",
  {"get-data":"get_data_6","id":"chart6"} );
   
   
function ofc_ready()
{
    //alert('ofc_ready');
}

function open_flash_chart_data()
{
    //alert( 'reading data' );
    return JSON.stringify(data);
}

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window[movieName];
  } else {
    return document[movieName];
  }
}

function get_data_1()
{
	// alert( 'reading data 1' );
	return JSON.stringify(data_scope);
}

function get_data_2()
{
	return JSON.stringify(data_leadtime);
}

function get_data_3()
{
	return JSON.stringify(data_euro);
}

function get_data_4()
{
	return JSON.stringify(data_euro_ericsson);
}
 
function get_data_5()
{
	return JSON.stringify(data_euro_alu);
}

function get_data_6()
{
	return JSON.stringify(data_euro_services);
}
 
var data_scope = <? echo $data_scope; ?>;
var data_leadtime = <? echo $data_leadtime; ?>;
var data_euro = <? echo $data_euro; ?>;
var data_euro_ericsson = <? echo $data_euro_ericsson; ?>;
var data_euro_alu = <? echo $data_euro_alu; ?>;
var data_euro_services = <? echo $data_euro_services; ?>;
//
// to help debug:
//
 //alert(JSON.stringify(data_scope));

</script>
</head>
<body>

<div id="po_chart"></div>
<div id="leadtime_chart"></div>
<div id="euro_chart"></div>
<div id="euro_ericsson_chart"></div>
<div id="euro_alu_chart"></div>
<div id="euro_services"></div>
</body>
</html>
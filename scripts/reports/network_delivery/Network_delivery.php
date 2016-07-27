<?php
include("/var/www/html/include/config.php");
include $config['sitepath_abs']."/include/PHP/open-flash-chart-2/php-ofc-library/open-flash-chart.php";

require_once("Network_delivery_procedures.php");
include('Network_delivery_sites_scope.php');
include('Network_delivery_buffer.php');
include('Network_delivery_con.php');
include('Network_delivery_fac.php');
include('Network_delivery_acquired.php');
//
// This is the VIEW section:
//
?>
<html>
<head>

<link rel="stylesheet" href="scripts/reports/reports.css" type="text/css">

<script type="text/javascript">

$(document).ready(function() {
		
	var options = { 
    target:  $.session("targetscreen"),   
    success:    function() { 
		$("#loading").hide('fast');
		$("#reportOutput").show('fast');}  
	};	
	// attach handler to form's submit event 
	$('#filterForm').submit(function() { 
		$("#loading").show('fast');
		$("#reportOutput").hide('fast');			
	    $(this).ajaxSubmit(options); 
	    return false; 	    
	});	
	
	$('#help').click(function() { 
		$("#helpinfo").toggle("slow");

	});	
	
});	

swfobject.embedSWF(
  "<?=$config['sitepath_url']?>/include/PHP/open-flash-chart-2/open-flash-chart.swf", "my_chart",
  "850", "350", "9.0.0", "expressInstall.swf",
  {"get-data":"get_data_1","id":"piechart"} );
swfobject.embedSWF(
  "<?=$config['sitepath_url']?>/include/PHP/open-flash-chart-2/open-flash-chart.swf", "my_chart2",
  "850", "300", "9.0.0", "expressInstall.swf",
  {"get-data":"get_data_2","id":"barchart"} );


function pie_slice_clicked( index )
{
	//alert( 'Pie Slice '+ index +' clicked');
	tmp = findSWF("my_chart2");
	if (index==1 || index==3){
		x = tmp.load( JSON.stringify(data_buffer) );
	}else if (index==0 || index==2){
		x = tmp.load( JSON.stringify(data_acquired) );
	}else if (index==4 || index==5){
		x = tmp.load( JSON.stringify(data_con) );
	}else if (index==6 || index==7){
		x = tmp.load( JSON.stringify(data_fac) );
	}  		
}

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
	// alert( 'reading data 2' );
	// alert(JSON.stringify(data_2));
	return JSON.stringify(data_acquired);
}

var data_scope = <? echo $data_scope; ?>;
//
// to help debug:
//
// alert(JSON.stringify(data_1));
 
var data_buffer = <? echo $data_buffer; ?>;
var data_con = <? echo $data_con; ?>;
var data_acquired = <? echo $data_acquired; ?>;
var data_fac = <? echo $data_fac; ?>;

</script>
</head>
<body>
<?

if ($_GET['year']=="" || $_GET['year']=="All"){
	$_GET['year']="All";
	$year="";
}else{
	$year=$_GET['year'];
}
if ($_GET['phases']=="" || $_GET['phases']=="All"){
	$_GET['phases']="All";
	$phases="";
}else{
	$phases=$_GET['phases'];
}
if ($_GET['split']=="" || $_GET['split']=="All"){
	$_GET['split']="All";
	$split="";
}else{
	$split=$_GET['split'];
}
?>
<div id="reportfilters">
	<form id="filterForm" action="scripts/reports/Network_delivery.php">
	Year: <select name="year"><option value="<?=$year?>"><?=$_GET['year']?></option><option value="">All</option><option>2009</option><option>2010</option></select>
	Project: <select name="phases"><option value="<?=$phases?>"><?=$_GET['phases']?></option><option value="">All</option><option value="Phase 1">HSDPA Phase 1</option><option value="Phase 2">HSDPA Phase 2</option></select>
	Split: <select name="split"><option value="<?=$split?>"><?=$_GET['split']?></option><option value="">All</option><option value="ALU">ALU</option><option value="WIP">WIP</option><option value="KPNGB">KPNGB </option></select>
	<input type="submit" value="OK"> (Bug in Internet Explorer! Please click twice on OK.
	<img src="../images/icons/help.png" id='help' class="pointer">
	</form>
	<div id="helpinfo" style="display:none">
	<img src="../images/bsds/process.jpg">
	</div>
</div>
<div id="my_chart"></div>
<div id="my_chart2"></div>   
</body>
</html>
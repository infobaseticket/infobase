<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

//Chart1
$query="select count(SITEID) AS AMOUNT2G from VW_2G3G4G_TECHNOS where G2='yes'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(SITEID) AS AMOUNT3G from VW_2G3G4G_TECHNOS where G3='yes'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(SITEID) AS AMOUNT4G from VW_2G3G4G_TECHNOS where G4='yes'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}


//CHART 2
$query="select count(SITEID) AS AMOUNT2GG9 from VW_2G3G4G_TECHNOS WHERE TECHNOS LIKE '%G9%'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res4);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(SITEID) AS AMOUNT2GG18 from VW_2G3G4G_TECHNOS WHERE TECHNOS LIKE '%G18%'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res5);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(SITEID) AS AMOUNT3GU9 from VW_2G3G4G_TECHNOS WHERE TECHNOS LIKE '%U9%'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(SITEID) AS AMOUNT3GU21 from VW_2G3G4G_TECHNOS WHERE TECHNOS LIKE '%U21%'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res7);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(SITEID) AS AMOUNT4GL8 from VW_2G3G4G_TECHNOS WHERE TECHNOS LIKE '%L8%'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res8);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(SITEID) AS AMOUNT4GL18 from VW_2G3G4G_TECHNOS WHERE TECHNOS LIKE '%L18%'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res9);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(SITEID) AS AMOUNT4GL26 from VW_2G3G4G_TECHNOS WHERE TECHNOS LIKE '%L26%'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res10);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
?>
<!DOCTYPE HTML>
<html>

<head>  
	<script type="text/javascript" src="javascripts/canvasjs/canvasjs.min.js"></script>
	<script type="text/javascript">
	$(document).ready( function(){
		var chart2 = new CanvasJS.Chart("chartContainer2",
		{
			title:{
				text: "SITE COUNT PER TECHNOLOGY from OSS live network"
			},          
			theme: "theme2",
		     data: [
		      {        
		        type: "column",  
		        dataPoints: [      
		        {y: <?=$res4['AMOUNT2GG9'][0]?>, label: "G9"},
		        {y: <?=$res5['AMOUNT2GG18'][0]?>,  label: "G18" },
		        {y: <?=$res6['AMOUNT3GU9'][0]?>,  label: "U9"},
		        {y: <?=$res7['AMOUNT3GU21'][0]?>,  label: "U21"},
		        {y: <?=$res8['AMOUNT4GL8'][0]?>,  label: "L8"},
		        {y: <?=$res9['AMOUNT4GL18'][0]?>, label: "L18"},
		        {y: <?=$res10['AMOUNT4GL26'][0]?>,  label: "L26"},               
		        ]
		      }   
		      ]
		});
		chart2.render();
		var chart = new CanvasJS.Chart("chartContainer",
		{
			title:{
				text: "SITE COUNT PER BAND from ACTIVE OSS live network"
			}, 
			theme: "theme2",
			data: [
			{        
				type: "doughnut",
				startAngle: 30,                          
				toolTipContent: "{y} sites", 					
				click: function(e){
		        alert(  "dataSeries Event => Type: "+ e.dataSeries.type+ ", dataPoint { x:" + e.dataPoint.x + ", y: "+ e.dataPoint.y + " }" );
		        },
				showInLegend: true,
				dataPoints: [
				{  y: <?=$res1['AMOUNT2G'][0]?>, label: "2G <?=$res1['AMOUNT2G'][0]?>", legendText: "2G" },
				{  y: <?=$res2['AMOUNT3G'][0]?>, label: "3G <?=$res2['AMOUNT3G'][0]?>", legendText: "3G" },
				{  y: <?=$res3['AMOUNT4G'][0]?>, label: "4G <?=$res3['AMOUNT4G'][0]?>", legendText: "4G" }			

				]
			}
			]
		});
		chart.render();
	});
	</script>
	
	<body>
		<div id="chartContainer" style="height: 300px; width: 50%; float:left;">
		</div>
		<div id="chartContainer2" style="height: 500px; width: 50%; float:left;">
		</div>
	</body>
</html>

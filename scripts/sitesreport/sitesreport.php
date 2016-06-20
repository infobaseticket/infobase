<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

//Chart1
$query="select count(N1_SITEID) AS CON from MASTER_REPORT WHERE N1_PROCESS='CONSTRUCTION' AND N1_STATUS='IS'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(N1_SITEID) AS ACQ from MASTER_REPORT WHERE N1_PROCESS='ACQUISITION' AND N1_STATUS='IS'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(N1_SITEID) AS BUFNORAF from MASTER_REPORT WHERE N1_PROCESS='BUFFER LBPOK - NO RAF' AND N1_STATUS='IS'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(N1_SITEID) AS ONAIR from MASTER_REPORT WHERE N1_PROCESS='ON-AIR' AND N1_STATUS='IS'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res5);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
$query="select count(N1_SITEID) AS BUFRAF from MASTER_REPORT WHERE N1_PROCESS='BUFFER LBPOK TXACQ OK - RAF' AND N1_STATUS='IS'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res4);
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
		
		var chart = new CanvasJS.Chart("chartContainer",
		{
			title:{
				text: "SITES in progress as per NET1"
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
				{  y: <?=$res3['BUFNORAF'][0]?>, label: "BUFFER LBPOK - NO RAF <?=$res3['BUFNORAF'][0]?>", legendText: "BUF NO RAF" },
				{  y: <?=$res4['BUFRAF'][0]?>, label: "BUFFER LBPOK TXACQ OK - RAF <?=$res4['BUFRAF'][0]?>", legendText: "BUF RAF" },
				{  y: <?=$res2['ACQ'][0]?>, label: "ACQUISITION <?=$res2['ACQ'][0]?>", legendText: "ACQ" },
				{  y: <?=$res1['CON'][0]?>, label: "CONSTRUCTION <?=$res1['CON'][0]?>", legendText: "CON" },
				{  y: <?=$res5['ONAIR'][0]?>, label: "ON-AIR <?=$res5['ACQ'][ONAIR]?>", legendText: "ONAIR" }		

				]
			}
			]
		});
		chart.render();
	});
	</script>
	
	<body>
		<div id="chartContainer" style="height: 400px; width: 100%; float:left;">
		</div>
	</body>
</html>

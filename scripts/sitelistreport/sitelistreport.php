<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$current_week=date('W');

$query="select * FROM VW_DEBARRED_4G";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
for ($i = 0; $i < count($res1['SITES']); $i++) {
		
	if ($res1['WEEK'][$i]<=$current_week){
		$sites=$res1['SITES'][$i]+$sites;
		$G4_data.="{ label: ".$res1['WEEK'][$i].", y: ".$res1['SITES'][$i]." },";
		$G4tot_data.="{ label: ".$res1['WEEK'][$i].", y: ".$sites." },";
	}
}
$query="select * FROM VW_DEBARRED_3G";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
for ($i = 0; $i < count($res1['SITES']); $i++) {
	if ($res1['WEEK'][$i]<=$current_week){
		$sites=$res1['SITES'][$i]+$sites;
		$G3_data.="{ label: ".$res1['WEEK'][$i].", y: ".$res1['SITES'][$i]." },";
		$G3tot_data.="{ label: ".$res1['WEEK'][$i].", y: ".$sites." },";
	}
}
$query="select * FROM VW_DEBARRED_2G";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
for ($i = 0; $i < count($res1['SITES']); $i++) {
	if ($res1['WEEK'][$i]<=$current_week){
		$sites=$res1['SITES'][$i]+$sites;
		$G2_data.="{ label: ".$res1['WEEK'][$i].", y: ".$res1['SITES'][$i]." },";
		$G2tot_data.="{ label: ".$res1['WEEK'][$i].", y: ".$sites." },";
	}
}

$query="select count(N1_SITEID) AS SITES, to_char(to_date(A250, 'DD/MM/YYYY'),'iw') AS WEEK from MASTER_REPORT 
WHERE A250 IS NOT NULL 
GROUP BY to_char(to_date(A250, 'DD/MM/YYYY'),'iw') 
ORDER BY to_char(to_date(A250, 'DD/MM/YYYY'),'iw') ";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
for ($i = 0; $i < count($res1['SITES']); $i++) {
	if ($res1['WEEK'][$i]<=$current_week){
		$dism_data.="{ label: ".$res1['WEEK'][$i].", y: ".$res1['SITES'][$i]." },";
	}
}
?>

<!DOCTYPE HTML>
<html>

<head>  
	<script type="text/javascript" src="javascripts/canvasjs/canvasjs.min.js"></script>
	<script type="text/javascript">
	$(document).ready( function(){
		

		var chart1 = new CanvasJS.Chart("chartContainer1",
		{
			title:{
				text: "Total debarred sites in network as per NET1"
			}, 
			theme: "theme2",    
			axisY:{ 
				title: "Sites totals"                 
			},
			axisY2:{ 
				title: "Sites per week"                 
			},
			axisX: {
				title: "Weeks",
				interval: 1
			},

			toolTip: {
				shared: true,
				content: function(e){
					var body = new String;
					var head ;
					for (var i = 0; i < e.entries.length; i++){
						var  str = "<span style= 'color:"+e.entries[i].dataSeries.color + "'> " + e.entries[i].dataSeries.name + "</span>: <strong>"+  e.entries[i].dataPoint.y + "</strong>'' <br/>" ; 
						body = body.concat(str);
					}
					head = "<span style = 'color:DodgerBlue; border-bottom:1px solid;'>WEEK: <strong>"+ (e.entries[0].dataPoint.label) + "</strong></span><br/>";

					return (head.concat(body));
				}
			},
			legend: {
				horizontalAlign :"center"
			},
			data: [
			{        
				type: "line",
				lineThickness:3,
				showInLegend: true,           
				name: "2G debarred total",
				legendText: "2G ", 
				dataPoints: [
				<?php echo $G2tot_data; ?>
				]
			},
			{        
				type: "line",
				lineThickness:3,
				showInLegend: true,           
				name: "3G debarred total", 
				legendText: "3G", 
				dataPoints: [
				<?php echo $G3tot_data; ?>
				]
			},
			{        
				type: "line",
				lineThickness:3,
				showInLegend: true,           
				name: "4G debarred total", 
				legendText: "4G",
				dataPoints: [
				<?php echo $G4tot_data; ?>
				]
			},
			{        
				type: "line",
				lineThickness:3,
				showInLegend: true,           
				name: "Dismantling", 
				legendText: "Dismantling",
				dataPoints: [
				<?php echo $dism_data; ?>
				]
			},
			{        
				name: "2G debarred this week",
				showInLegend: true,
				type: "column", 
				legendText: "2G", 
				axisYType: "secondary",
					dataPoints: [
				<?php echo $G2_data; ?>
				]
			},
			{        
				name: "3G debarred this week",
				legendText: "3G",
				showInLegend: true,
				type: "column", 
				axisYType: "secondary",
				dataPoints: [
				<?php echo $G3_data; ?>
				]
			},
			{        
				name: "4G debarred this week",
				legendText: "4G",
				showInLegend: true,
				type: "column", 
				axisYType: "secondary",
				dataPoints: [
				<?php echo $G4_data; ?>
				]
			},
			],
          legend :{
            cursor:"pointer",
            itemclick : function(e) {
              if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
				e.dataSeries.visible = false;
              }
              else{
				e.dataSeries.visible = true;
              }
              chart1.render();
            }
          }
          
		});
		chart1.render();
	});
	</script>
	
	<body>
		<div id="chartContainer1" style="height: 600px; width: 100%;"></div>
		
	</body>
</html>

<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other,Base_RF","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$months = array("Jan", "Feb", "Mar", "Apr","May","Jun","Jul","Sep","Oct","Nov", "Dec");

//echo $categories;

$query = "SELECT
	N1_NBUP,
	N1_PROCESS2,
	COUNT (N1_CANDIDATE) AS TOTAL
FROM
	MASTER_REPORT
WHERE
	N1_PROCESS = 'CONSTRUCTION'";
if ($_POST['sitetype']!=''){
	$query.=" AND N1_SITETYPE='".$_POST['sitetype']."'";
}
if ($_POST['partner']=='BENCHMARK'){
	$query.=" AND (N1_SAC='BENCHMARK' or N1_CON='BENCHMARK')";
}else if ($_POST['partner']=='TECHM'){
	$query.=" AND N1_SAC!='BENCHMARK' AND N1_CON!='BENCHMARK'";
}
$query.="
GROUP BY
	N1_NBUP,
	N1_PROCESS2
ORDER BY N1_PROCESS2";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}

for ($i = 0; $i < count($res1['TOTAL']); $i++){
    $data[$res1['N1_NBUP'][$i]][$res1['N1_PROCESS2'][$i]]=$res1['TOTAL'][$i];
    if ($prev_pro!=$res1['N1_PROCESS2'][$i]){
    	$categories.='{ "label" : "'. $res1['N1_PROCESS2'][$i].'" },';	
    	$cats[]= $res1['N1_PROCESS2'][$i];
    }
    $prev_pro=$res1['N1_PROCESS2'][$i];
}
//echo "<pre>".print_r($data,true)."</pre>";
//echo $categories;


foreach ($data as $key => $series) {

	//echo "<pre>".print_r($series,true)."</pre>";
	$series_out.=  '{
            "seriesname": "'.$key.'",
            "data": [';
            	foreach ($series as $key => $val){
            		$series_out.=  '{ "value" : "'.$val.'"},';
            	}
    $series_out.= '   ]

		   },';
}

//echo $series_out;
?>
<div id="chartContainer">Loading....</div>


<script src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/fusioncharts.js"></script>
<script src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>

<script type="text/javascript">
FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "stackedcolumn2d",
        "renderAt": "chartContainer",
        "width": "100% ",
        "height": "500",
        "dataFormat": "json",
        "dataSource": {
		    "chart": {
		        "caption": "Sites in construction",
		        "showvalues": "0",
		        "plotgradientcolor": "",
		        "formatnumberscale": "0",
		        "showplotborder": "0",
		        "palettecolors": "#EED17F,#97CBE7,#074868,#B0D67A,#2C560A,#DD9D82",
		        "canvaspadding": "0",
		        "bgcolor": "FFFFFF",
		        "showalternatehgridcolor": "0",
		        "divlinecolor": "CCCCCC",
		        "showcanvasborder": "0",
		        "legendborderalpha": "0",
		        "legendshadow": "0",
		        "interactivelegend": "0",
		        "showpercentvalues": "1",
		        "showsum": "1",
		        "canvasborderalpha": "0",
		        "showborder": "0",
		        "showHoverEffect": "1",
		        "scrollToEnd": "1",
		        "plotHighlightEffect": "fadeout",
		    },
		    "categories": [
		        {
		            "category": [
		                <?=$categories?>
		            ]
		        }
		    ],
		    "dataset": [
		        <?=$series_out?>
		    ]
		}
    });

    revenueChart.render();
})


</script>

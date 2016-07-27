<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other,Base_RF","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$months = array("Jan", "Feb", "Mar", "Apr","May","Jun","Jul","Sep","Oct","Nov", "Dec");

if ($_POST['year']==''){
	function incrementDate($startDate, $monthIncrement = 0) {

	    $startingTimeStamp = $startDate->getTimestamp();
	    // Get the month value of the given date:
	    $monthString = date('Y-m', $startingTimeStamp);
	    // Create a date string corresponding to the 1st of the give month,
	    // making it safe for monthly calculations:
	    $safeDateString = "first day of $monthString";
	    // Increment date by given month increments:
	    $incrementedDateString = "$safeDateString $monthIncrement month";
	    $newTimeStamp = strtotime($incrementedDateString);
	    $newDate = DateTime::createFromFormat('U', $newTimeStamp);
	    return $newDate;
	}

	$currentDate = new DateTime();
	$oneMonthAgo = incrementDate($currentDate);
	$twoMonthsAgo = incrementDate($currentDate, -1);
	$threeMonthsAgo = incrementDate($currentDate, -2);
	$twoMonths = incrementDate($currentDate, 2);
	$threeMonths = incrementDate($currentDate, 3);
	$fourMonths = incrementDate($currentDate, 4);
	$fiveMonths = incrementDate($currentDate, 5);
	$sixMonths = incrementDate($currentDate, 6);

	/*
	echo "<br><br>3 AGO: ".$threeMonthsAgo->format('F Y d') . "<br>";
	echo "2 AGO: ".$twoMonthsAgo->format('F Y d') . "<br>";
	echo "1 AGO: ".$oneMonthAgo->format('F Y d') . "<br>";
	echo "THIS: ".$currentDate->format('F Y d') . "<br>";
	echo "2: ".$twoMonths->format('F Y d') . "<br>";
	echo "3: ".$threeMonths->format('F Y d') . "<br>";
	echo "4: ".$fourMonths->format('F Y d') . "<br>";
	echo "5: ".$fiveMonths->format('F Y d') . "<br>";
	echo "6: ".$sixMonths->format('F Y d') . "<br>";

	*/
	$months=array($threeMonthsAgo->format('M'),$twoMonthsAgo->format('M'),$oneMonthAgo->format('M'),$currentDate->format('M'),$twoMonths->format('M'),$threeMonths->format('M'),$fourMonths->format('M'),$fiveMonths->format('M'),$sixMonths->format('M'));
	$months_labels=array($threeMonthsAgo->format('M-Y'),$twoMonthsAgo->format('M-Y'),$oneMonthAgo->format('M-Y'),$currentDate->format('M-Y'),$twoMonths->format('M-Y'),$threeMonths->format('M-Y'),$fourMonths->format('M-Y'),$fiveMonths->format('M-Y'),$sixMonths->format('M-Y'));
	$years_array=array($threeMonthsAgo->format('Y'),$twoMonthsAgo->format('Y'),$oneMonthAgo->format('Y'),$currentDate->format('Y'),$twoMonths->format('Y'),$threeMonths->format('Y'),$fourMonths->format('Y'),$fiveMonths->format('Y'),$sixMonths->format('Y'));
	$select_year=date('Y');
	$start=$threeMonthsAgo->format('m')."-".$threeMonthsAgo->format('Y');
	$end=$sixMonths->format('m')."-".$sixMonths->format('Y');
	$title="CURRENT ONGOING DISMANTLINGS"; 

}else{
	$select_year=$_POST['year'];
	$months = array("Jan", "Feb", "Mar", "Apr","May","Jun","Jul","Sep","Oct","Nov", "Dec");
	$months_labels=array("Jan-".$select_year, "Feb-".$select_year, "Mar-".$select_year, "Apr-".$select_year,"May-".$select_year,"Jun-".$select_year,"Jul-".$select_year,"Sep-".$select_year,"Oct-".$select_year,"Nov-".$select_year, "Dec-".$select_year);
	$years_array= array($select_year, $select_year, $select_year, $select_year,$select_year,$select_year,$select_year,$select_year,$select_year,$select_year, $select_year);
	$start='01-'.$select_year;
	$endyear=$select_year+1;
	$end='01-'.$endyear;
	$title="DISMANTLINGS FOR ".$select_year; 
}

$query = "SELECT
    EXTRACT(year FROM TO_DATE(T_A250)) as YEAR,
EXTRACT(month FROM TO_DATE(T_A250)) AS MONTH,
COUNT(T_N1_SITEID) AS TOTAL,
T_N1_SITETYPE
FROM
    VW_DISM_REPL
WHERE T_A250 IS NOT NULL
AND EXTRACT(year FROM TO_DATE(T_A250))!='1990'
AND TO_DATE(T_A250)>= '01-".$start."'
AND TO_DATE(T_A250)< '01-".$end."'
GROUP BY 
EXTRACT(year FROM TO_DATE(T_A250)),
EXTRACT(month FROM TO_DATE(T_A250)),
T_N1_SITETYPE
ORDER BY 
EXTRACT(year FROM TO_DATE(T_A250)),
EXTRACT(month FROM TO_DATE(T_A250))";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}


for ($i = 0; $i < count($res1['TOTAL']); $i++){
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "MICRO");
    if ($pos !== false){
        $dataA250_MICRO[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "MACRO");
    if ($pos !== false){
        $dataA250_MACRO[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "IND");
    if ($pos !== false){
        $dataA250_IND[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }   
}
//echo "<pre>".print_r($dataA250_MICRO,true)."</pre>";
$query = "SELECT
    EXTRACT(year FROM TO_DATE(T_A250)) as YEAR,
EXTRACT(month FROM TO_DATE(T_A250)) AS MONTH,
COUNT(T_N1_SITEID) AS TOTAL,
T_N1_SITETYPE
FROM
    VW_DISM_REPL
WHERE T_A250 IS NOT NULL
AND EXTRACT(year FROM TO_DATE(T_A250))!='1990'
AND TO_DATE(T_A250)>=TO_DATE(A80U380) AND TO_DATE(T_A250)<TO_DATE(A80U380)+2
GROUP BY 
EXTRACT(year FROM TO_DATE(T_A250)),
EXTRACT(month FROM TO_DATE(T_A250)),
T_N1_SITETYPE
ORDER BY 
EXTRACT(year FROM TO_DATE(T_A250)),
EXTRACT(month FROM TO_DATE(T_A250))";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}

for ($i = 0; $i < count($res1['TOTAL']); $i++){
   
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "MICRO");
    if ($pos !== false){
        $dataA250_MICRO2[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "MACRO");
    if ($pos !== false){
        $dataA250_MACRO2[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "IND");
    if ($pos !== false){
        $dataA250_IND2[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }
}


/***************************************
        We get A80 per year and month
***************************************/
$query = "SELECT
    EXTRACT(year FROM TO_DATE(A80U380)) as YEAR,
EXTRACT(month FROM TO_DATE(A80U380)) AS MONTH,
COUNT(T_N1_SITEID) AS TOTAL,
T_N1_SITETYPE
FROM
    VW_DISM_REPL
WHERE A80U380 IS NOT NULL
AND EXTRACT(year FROM TO_DATE(A80U380))!='1990'
GROUP BY 
EXTRACT(year FROM TO_DATE(A80U380)),
EXTRACT(month FROM TO_DATE(A80U380)),
T_N1_SITETYPE
ORDER BY 
EXTRACT(year FROM TO_DATE(A80U380)),
EXTRACT(month FROM TO_DATE(A80U380))";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}

for ($i = 0; $i < count($res1['TOTAL']); $i++){
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "MICRO");
    if ($pos !== false){
        $dataA80_MICRO[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "MACRO");
    if ($pos !== false){
        $dataA80_MACRO[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }    
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "IND");
    if ($pos !== false){
        $dataA80_IND[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }    
}

$query = "SELECT
    EXTRACT(year FROM TO_DATE(A80U380)) as YEAR,
EXTRACT(month FROM TO_DATE(A80U380)) AS MONTH,
COUNT(T_N1_SITEID) AS TOTAL,
T_N1_SITETYPE
FROM
    VW_DISM_REPL
WHERE A80U380 IS NOT NULL
AND EXTRACT(year FROM TO_DATE(A80U380))!='1990'
AND TO_DATE(T_A250)>=TO_DATE(A80U380) AND TO_DATE(T_A250)<TO_DATE(A80U380)+2
GROUP BY 
EXTRACT(year FROM TO_DATE(A80U380)),
EXTRACT(month FROM TO_DATE(A80U380)),
T_N1_SITETYPE
ORDER BY 
EXTRACT(year FROM TO_DATE(A80U380)),
EXTRACT(month FROM TO_DATE(A80U380))";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}

for ($i = 0; $i < count($res1['TOTAL']); $i++){
    //echo $res1['YEAR'][$i];
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "MICRO");
    if ($pos !== false){
        $dataA80_MICRO2[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "MACRO");
    if ($pos !== false){
        $dataA80_MACRO2[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }    
    $pos = strpos($res1['T_N1_SITETYPE'][$i], "IND");
    if ($pos !== false){
        $dataA80_IND2[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    }    
}

/***************************************
        We get A270_ESTIM per year and month
***************************************/
$query = "SELECT
    EXTRACT(year FROM TO_DATE(T_AU270_ESTIM)) as YEAR,
EXTRACT(month FROM TO_DATE(T_AU270_ESTIM)) AS MONTH,
COUNT(T_AU270_ESTIM) AS TOTAL
FROM
    VW_DISM_REPL
WHERE T_AU270_ESTIM IS NOT NULL
AND EXTRACT(year FROM TO_DATE(T_AU270_ESTIM))!='2099'
GROUP BY 
EXTRACT(year FROM TO_DATE(T_AU270_ESTIM)),
EXTRACT(month FROM TO_DATE(T_AU270_ESTIM))
ORDER BY 
EXTRACT(year FROM TO_DATE(T_AU270_ESTIM)),
EXTRACT(month FROM TO_DATE(T_AU270_ESTIM))";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}

for ($i = 0; $i < count($res1['TOTAL']); $i++){
    //echo $res1['YEAR'][$i];
    $dataESTIM[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
}

//echo "<pre>".print_r($months,true)."</pre>";
foreach ($months as $month) {
	
	$nmonth = date("m", strtotime($month));
	$nmonth=ltrim($nmonth, '0');
	//echo $nmonth."<br>";
	$year=$years_array[$nmonth];
    $lables.= '{"label": "'.$month.'"},';
    $A250_IND.='{"value": "'.$dataA250_IND[$nmonth][$year].'"},';
    $A250_MACRO.='{"value": "'.$dataA250_MACRO[$nmonth][$year].'"},';
    $A250_MICRO.='{"value": "'.$dataA250_MICRO[$nmonth][$year].'"},';
    $A250_IND2.='{"value": "'.$dataA250_IND2[$nmonth][$year].'"},';
    $A250_MACRO2.='{"value": "'.$dataA250_MACRO2[$nmonth][$year].'"},';
    $A250_MICRO2.='{"value": "'.$dataA250_MICRO2[$nmonth][$year].'"},';
    $A80_IND.='{"value": "'.$dataA80_IND[$nmonth][$year].'"},';
    $A80_MACRO.='{"value": "'.$dataA80_MACRO[$nmonth][$year].'"},';
    $A80_MICRO.='{"value": "'.$dataA80_MICRO[$nmonth][$year].'"},';
    $A80_IND2.='{"value": "'.$dataA80_IND2[$nmonth][$year].'"},';
    $A80_MACRO2.='{"value": "'.$dataA80_MACRO2[$nmonth][$year].'"},';
    $A80_MICRO2.='{"value": "'.$dataA80_MICRO2[$nmonth][$year].'"},';
    $AU270_ESTIM.='{"value": "'.$dataESTIM[$nmonth][$year].'"},';
}
//echo $A250_MICRO;

?>
<div id="chartContainer">Loading....</div>


<script src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/fusioncharts.js"></script>
<script src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>

<script type="text/javascript">
FusionCharts.ready(function(){
    var revenueChart = new FusionCharts({
        "type": "msstackedcolumn2d",
        "renderAt": "chartContainer",
        "width": "100% ",
        "height": "500",
        "dataFormat": "json",
        "dataSource": {
		    "chart": {
		        "caption": "<?=$title?>",
		        "subCaption": "",
		        "captionFontSize": "14",
		        "subcaptionFontSize": "14",
		        "subcaptionFontBold": "0",
		        "xaxisname": "Month",
		        "yaxisname": "Amount",
		        "showvalues": "0",
		        "numberprefix": "",
		        "legendBgAlpha": "0",
		        "legendBorderAlpha": "0",
		        "legendShadow": "0",
		        "showborder": "0",
		        "bgcolor": "#ffffff",
		        "showalternatehgridcolor": "0",
		        "showplotborder": "0",
		        "showcanvasborder": "1",
		        "legendshadow": "0",
		        "plotgradientcolor": "",
		        "showCanvasBorder": "0",
		        "showAxisLines": "0",
		        "showAlternateHGridColor": "0",
		        "divlineAlpha": "100",
		        "divlineThickness": "1",
		        "divLineDashed": "1",
		        "divLineDashLen": "1",
		        "divLineGapLen": "1",
		        "lineThickness": "3",
		        "flatScrollBars": "1",
		        "scrollheight": "10",
		        "numVisiblePlot": "12",
		        "showHoverEffect": "1",
		        "scrollToEnd": "1",
		        "plotHighlightEffect": "fadeout",
		        "legendPosition":"right"
		    },
		    "categories": [
		        {
		            "category": [
		               <?=$lables?>
		            ]
		        }
		    ],
		    "dataset": [
			    {
				    "dataset": [
				        {
				            "seriesname": "A250 IND",
				            "data": [
				                <?=$A250_IND?>
				            ]
				        },
				        {
				            "seriesname": "A250 IND 2",
				            "data": [
				                <?=$A250_IND2?>
				            ]
				        }	  
				    ]
				},
				{
				    "dataset": [
				        {
				            "seriesname": "A250 MACRO",
				            "data": [
				                <?=$A250_MACRO?>
				            ]
				        },
				        {
				            "seriesname": "A250 MACRO 2",
				            "data": [
				                <?=$A250_MACRO2?>
				            ]
				        }
				    ]
				},
				{
				    "dataset": [
				        {
				            "seriesname": "A250 MICRO",
				            "data": [
				                <?=$A250_MICRO?>
				            ]
				        },
				        {
				            "seriesname": "A250 MICRO 2",
				            "data": [
				                <?=$A250_MICRO2?>
				            ]
				        }
				    ]
				},
				{
				    "dataset": [
				        {
				            "seriesname": "A80 REPL IND",
				            "data": [
				                <?=$A80_IND?>
				            ]
				        },
				        {
				            "seriesname": "A80 REPL IND 2",
				            "data": [
				                <?=$A80_IND2?>
				            ]
				        }
				    ]
				},
				{
				    "dataset": [
				        {
				            "seriesname": "A80 REPL MACRO",
				            "data": [
				                <?=$A80_MACRO?>
				            ]
				        },
				        {
				            "seriesname": "A80 REPL MACRO 2",
				            "data": [
				                <?=$A80_MACRO2?>
				            ]
				        }
				    ]
				},
				{
				    "dataset": [
				        {
				            "seriesname": "A80 REPL MICRO",
				            "data": [
				                <?=$A80_MICRO?>
				            ]
				        },
				        {
				            "seriesname": "A80 REPL MICRO2",
				            "data": [
				                <?=$A80_MICRO2?>
				            ]
				        }
				    ]
				},
				{
				    "dataset": [
				        {
				            "seriesname": "AU270 ESTIM",
				            "data": [
				                <?=$AU270_ESTIM?>
				            ]
				        }
				    ]
				}
			]
		}
    });

    revenueChart.render();
})


</script>

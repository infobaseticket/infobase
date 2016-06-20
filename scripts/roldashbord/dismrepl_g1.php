<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other,Base_RF","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$k=0;
$z=0;
/***************************************
        We get A250 per year and month
***************************************/
$query = "SELECT
    EXTRACT(year FROM TO_DATE(T_A250)) as YEAR,
EXTRACT(month FROM TO_DATE(T_A250)) AS MONTH,
COUNT(T_N1_SITEID) AS TOTAL,
T_N1_SITETYPE
FROM
    VW_DISM_REPL
WHERE T_A250 IS NOT NULL
AND EXTRACT(year FROM TO_DATE(T_A250))!='1990'
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
    //echo $res1['YEAR'][$i];
    if ($res1['YEAR'][$i]!=$prev_year){
        //echo $res1['YEAR'][$i];
        $allYears[$k++]=$res1['YEAR'][$i];              
    }
    $prev_year=$res1['YEAR'][$i];

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
    //echo $res1['YEAR'][$i];
    if ($res1['YEAR'][$i]!=$prev_year){
        //echo $res1['YEAR'][$i];
        $allYears[$k++]=$res1['YEAR'][$i];       
    }
    $prev_year=$res1['YEAR'][$i];
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
    if ($res1['YEAR'][$i]!=$prev_year){
        //echo $res1['YEAR'][$i];
        $allYears[$k++]=$res1['YEAR'][$i];             
    }
    $prev_year=$res1['YEAR'][$i];

    $dataESTIM[$res1['MONTH'][$i]][$res1['YEAR'][$i]]=$res1['TOTAL'][$i];
    
}
//echo "<pre>".print_r($dataESTIM,true);

/******************* we make data 0 if it is not available: */

$maxYear= max($allYears);
$minYear= min($allYears);
//echo $maxYear;

$outA250_MICRO="";
$outA250_MICRO2="";
$outA80_MICRO="";
$outA250_MACRO="";
$outA250_MACRO2="";
$outA80_MACRO="";
$outA250_IND="";
$outA250_IND2="";
$outA80_IND="";
$outESTIM="";

for ($i=$minYear;$i<=$maxYear;$i++){
    //echo $i."<br>";
    $years.="'".$i."',";
    if ($i!=$minYear){
        $dataSeries2="{
            title : {'text':'".$i." DISMANTLED SITES per month'},
            series : [
                {'data': dataMap.dataA250_MICRO['".$i."']},
                {'data': dataMap.dataA250_MICRO2['".$i."']},
                {'data': dataMap.dataA80_MICRO['".$i."']},
                {'data': dataMap.dataA250_MACRO['".$i."']},
                {'data': dataMap.dataA250_MACRO2['".$i."']},
                {'data': dataMap.dataA80_MACRO['".$i."']},
                {'data': dataMap.dataA250_IND['".$i."']},
                {'data': dataMap.dataA250_IND2['".$i."']},
                {'data': dataMap.dataA80_IND['".$i."']},
                {'data': dataMap.dataA270['".$i."']},
                
            ]
        },"; 
    }
    $dataSeries.=$dataSeries2;
    //echo $i;
    $outA250_MICRO.=$i.":[";
    $outA250_MICRO2.=$i.":[";
    $outA80_MICRO.=$i.":[";
    $outA250_MACRO.=$i.":[";
    $outA250_MACRO2.=$i.":[";
    $outA80_MACRO.=$i.":[";
    $outA250_IND.=$i.":[";
    $outA250_IND2.=$i.":[";
    $outA80_IND.=$i.":[";
    $outESTIM.=$i.":[";
    //echo $year.": -";
    for ($j=1;$j<=12;$j++){
        //echo $$i.":";
        if ($dataA250_MICRO[$j][$i]==''){
            $outA250_MICRO.="0,";
        }else{
            $outA250_MICRO.=$dataA250_MICRO[$j][$i].",";
        }

        if ($dataA250_MICRO2[$j][$i]==''){
            $outA250_MICRO2.="0,";
        }else{
            $outA250_MICRO2.=$dataA250_MICRO2[$j][$i].",";
        }


        if ($dataA80_MICRO[$j][$i]==''){
            $outA80_MICRO.="0,";
        }else{
            $outA80_MICRO.=$dataA80_MICRO[$j][$i].",";
        }

        if ($dataA250_MACRO[$j][$i]==''){
            $outA250_MACRO.="0,";
        }else{
            $outA250_MACRO.=$dataA250_MACRO[$j][$i].",";
        }

        if ($dataA250_MACRO2[$j][$i]==''){
            $outA250_MACRO2.="0,";
        }else{
            $outA250_MACRO2.=$dataA250_MACRO2[$j][$i].",";
        }

        if ($dataA80_MACRO[$j][$i]==''){
            $outA80_MACRO.="0,";
        }else{
            $outA80_MACRO.=$dataA80_MACRO[$j][$i].",";
        }

        if ($dataA250_IND[$j][$i]==''){
            $outA250_IND.="0,";
        }else{
            $outA250_IND.=$dataA250_IND[$j][$i].",";
        }

        if ($dataA250_IND2[$j][$i]==''){
            $outA250_IND2.="0,";
        }else{
            $outA250_IND2.=$dataA250_IND2[$j][$i].",";
        }

        if ($dataA80_IND[$j][$i]==''){
            $outA80_IND.="0,";
        }else{
            $outA80_IND.=$dataA80_IND[$j][$i].",";
        }

        if ($dataESTIM[$j][$i]==''){
            $outESTIM.="0,";
        }else{
            $outESTIM.=$dataESTIM[$j][$i].",";
        }
        //echo $outA250_MICRO."<br>";
    }
    $outA250_MICRO=substr($outA250_MICRO, 0,-1);
    $outA250_MICRO.="],";
    $outA250_MICRO2=substr($outA250_MICRO2, 0,-1);
    $outA250_MICRO2.="],";
    $outA80_MICRO=substr($outA80_MICRO, 0,-1);
    $outA80_MICRO.="],";
    $outA250_MACRO=substr($outA250_MACRO, 0,-1);
    $outA250_MACRO.="],";
    $outA250_MACRO2=substr($outA250_MACRO2, 0,-1);
    $outA250_MACRO2.="],";
    $outA80_MACRO=substr($outA80_MACRO, 0,-1);
    $outA80_MACRO.="],";
    $outA250_IND=substr($outA250_IND, 0,-1);
    $outA250_IND.="],";
    $outA250_IND2=substr($outA250_IND2, 0,-1);
    $outA250_IND2.="],";
    $outA80_IND=substr($outA80_IND, 0,-1);
    $outA80_IND.="],";
    $outESTIM=substr($outESTIM, 0,-1);
    $outESTIM.="],";
}
$outA250_MICRO=substr($outA250_MICRO, 0,-1);
$outA80_MICRO=substr($outA80_MICRO, 0,-1);
$outA250_MICRO2=substr($outA250_MICRO2, 0,-1);
$outA80_MICRO2=substr($outA80_MICRO2, 0,-1);
$outA250_MACRO=substr($outA250_MACRO, 0,-1);
$outA80_MACRO=substr($outA80_MACRO, 0,-1);
$outA250_MACRO2=substr($outA250_MACRO2, 0,-1);
$outA80_MACRO2=substr($outA80_MACRO2, 0,-1);
$outA250_IND=substr($outA250_IND, 0,-1);
$outA250_IND2=substr($outA250_IND2, 0,-1);
$outA80_IND=substr($outA80_IND, 0,-1);
$outESTIM=substr($outESTIM, 0,-1);
$years=substr($years, 0,-1);
//$dataSeries=substr($dataSeries, 0,-1);
//echo $outA250_MICRO2."<hr>";

?>

<div id="main" style="height:600px"></div>


<script src="<?=$config['explorer_url']?>javascripts/echarts-2.2.7/build/dist/echarts.js"></script>
<script type="text/javascript">

var dataMap = {};

function dataFormatter(obj) {
    var pList = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var temp;
    var max = 0;
    <?php for ($year=$minYear;$year<=$maxYear;$year++){ ?>
        var year='<?=$year?>';
        temp = obj[year];
        for (var i = 0, l = temp.length; i < l; i++) {
            max = Math.max(max, temp[i]);
            obj[year][i] = {
                name : pList[i],
                value : temp[i]
            }
        }
        obj[year+'max'] = Math.floor(max/100) * 100;
    <?php } ?>
    return obj;
}

dataMap.dataA250_MICRO = dataFormatter({
    <?=$outA250_MICRO?>
});
dataMap.dataA250_MICRO2 = dataFormatter({
    <?=$outA250_MICRO2?>
});

dataMap.dataA80_MICRO = dataFormatter({
    <?=$outA80_MICRO?>
});

dataMap.dataA250_MACRO = dataFormatter({
    <?=$outA250_MACRO?>
});

dataMap.dataA250_MACRO2 = dataFormatter({
    <?=$outA250_MACRO2?>
});

dataMap.dataA80_MACRO = dataFormatter({
    <?=$outA80_MACRO?>
});

dataMap.dataA250_IND = dataFormatter({
    <?=$outA250_IND?>
});

dataMap.dataA250_IND2 = dataFormatter({
    <?=$outA250_IND2?>
});

dataMap.dataA80_IND = dataFormatter({
    <?=$outA80_IND?>
});

dataMap.dataA270 = dataFormatter({
    <?=$outESTIM?>
});

// configure for module loader
require.config({
    paths: {
        echarts: '<?=$config['explorer_url']?>javascripts/echarts-2.2.7/build/dist'
    }
});

// use
require(
    [
        'echarts',
        'echarts/chart/bar', // require the specific chart type
        'echarts/chart/line'
    ],
    function (ec) {
        // Initialize after dom ready
        var myChart = ec.init(document.getElementById('main')); 
        
        var option = {
            timeline:{
                data:[
                    <?=$years?>
                ],

                autoPlay : true,
                playInterval : 4000
            },
            options:[
                {
                    title : {
                        'text':'<?=$minYear?> DISMANTLINGS PER MONTH',
                        'subtext':'as per NetOne'
                    },
                    tooltip : {'trigger':'axis'},
                    legend : {
                        x:'right',
                        'data':['A250 MICRO','A250 MICRO2','A80 MICRO','A250 MACRO','A250 MACRO2','A80 MACRO','A250 IND','A250 IND2','A80 IND','A270_ESTIM'],
                        'selected':{
                            'A250 MICRO':true,
                            'A250 MICRO2':true,
                            'A80 MICRO':true,
                            'A250 MACRO':true,
                            'A250 MACRO2':true,
                            'A80 MACRO':true,
                            'A250 IND':true,
                            'A250 IND2':true,
                            'A80 IND':true,
                            'A270_ESTIM':true
                        }
                    },
                    toolbox : {
                        'show':true, 
                        orient : 'vertical',
                        x: 'right', 
                        y: 'center',
                        'feature':{
                            'mark':{'show':false},
                            'dataView':{'show':true,'readOnly':true},
                            'magicType':{
                                'show':true,
                                'type':['line','bar'],
                                'title' : {
                                    'line' : 'convert to line',
                                    'bar' : 'convert to bar',
                                    'save' : 'save to image'
                                },
                            },
                            'restore':{'show':false},
                            'saveAsImage':{'show':true}
                        }
                    },
                    calculable : true,
                    grid : {'y':80,'y2':100},
                    xAxis : [{
                        'type':'category',
                        'axisLabel':{'interval':0},
                        'data':[
                            'Jan','\nFeb','Mar','\nApr','May','\nJun','Jul','\nAug',
                            'Sep','\nOct','Nov','\nDec'
                        ]
                    }],
                    yAxis : [
                        {
                            'type':'value',
                            'name':'#sites',
                            'max':20
                        }
                    ],
                    series : [
                        {
                            'name':'A250 MICRO',
                            'type':'bar',
                            'stack':'A250_MICRO_S',
                            'data': dataMap.dataA250_MICRO['<?=$minYear?>']
                        },
                        {
                            'name':'A250 MICRO2',
                            'type':'bar',
                            'stack':'A250_MICRO_S',
                            'data': dataMap.dataA250_MICRO2['<?=$minYear?>']
                        },
                        {
                            'name':'A80 MICRO',
                            'type':'bar',
                            'data': dataMap.dataA80_MICRO['<?=$minYear?>']
                        },
                        {
                            'name':'A250 MACRO',
                            'type':'bar',
                            'stack':'A250_MACRO_S',
                            'data': dataMap.dataA250_MACRO['<?=$minYear?>']
                        },
                        {
                            'name':'A250 MACRO2',
                            'type':'bar',
                            'stack':'A250_MACRO_S',
                            'data': dataMap.dataA250_MACRO2['<?=$minYear?>']
                        },
                        {
                            'name':'A80 MACRO',
                            'type':'bar',
                            'data': dataMap.dataA80_MACRO['<?=$minYear?>']
                        },
                        {
                            'name':'A250 IND',
                            'type':'bar',
                            'stack':'A250_IND_S',
                            'data': dataMap.dataA250_IND['<?=$minYear?>']
                        },
                         {
                            'name':'A250 IND2',
                            'type':'bar',
                            'stack':'A250_IND_S',
                            'data': dataMap.dataA250_IND2['<?=$minYear?>']
                        },
                         {
                            'name':'A80 IND',
                            'type':'bar',
                            'data': dataMap.dataA80_IND['<?=$minYear?>']
                        },
                        {
                            'name':'A270_ESTIM',
                            'type':'bar',
                            'data': dataMap.dataA270['<?=$minYear?>']
                        }
                    ]
                },
                <?=$dataSeries?>
            ]
        };

        // Load data into the ECharts instance 
        myChart.setOption(option); 
    }
);
</script>

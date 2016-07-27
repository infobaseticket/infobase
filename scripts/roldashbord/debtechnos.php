<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_RF","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query="select * from BTS_LIVE";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $amount=count($resT['MONTHYEAR']);
}

for ($i=0;$i<$amount;$i++){
    $split=explode("-", $resT['MONTHYEAR'][$i]);
    $year=$split[1];
    $month=$split[0];
    $sumG9=$sumG9+$resT['G9'][$i];
    $sumG18=$sumG18+$resT['G18'][$i];
    $sumU9=$sumU9+$resT['U9'][$i];
    $sumU21=$sumU21+$resT['U21'][$i];
    $sumL8=$sumL8+$resT['L8'][$i];
    $sumL18=$sumG18+$resT['L18'][$i];
    $data[$year][$month]['G9']=$resT['G9'][$i];
    $data2[$year][$month]['G9']=$sumG9;
    $data[$year][$month]['G18']=$resT['G18'][$i];
    $data2[$year][$month]['G18']=$sumG18;
    $data[$year][$month]['U9']=$resT['U9'][$i];
     $data2[$year][$month]['U9']=$sumU9;
    $data[$year][$month]['U21']=$resT['U21'][$i];
     $data2[$year][$month]['U21']=$sumU21;
    $data[$year][$month]['L8']=$resT['L8'][$i];
     $data2[$year][$month]['L8']=$sumL8;
    $data[$year][$month]['L18']=$resT['L18'][$i];
     $data2[$year][$month]['L18']=$sumL18;
}
//var_dump($data);
for ($i=1999;$i<=2016;$i++){
    $outG9.=$i.":[";
    $outG9_2.=$i.":[";
    $outG18.=$i.":[";
    $outG18_2.=$i.":[";
    $outU9.=$i.":[";
    $outU9_2.=$i.":[";
    $outU21.=$i.":[";
    $outU21_2.=$i.":[";
    $outL8.=$i.":[";
    $outL8_2.=$i.":[";
    $outL18.=$i.":[";
    $outL18_2.=$i.":[";
    $years.="'".$i."',";
    if ($i!=1999){
        $dataArrays.="{
            title : {'text':'".$i." DEBARRED BTS SITES per month'},
            series : [
                {'data': dataMap.dataG9['".$i."']},
                {'data': dataMap.dataG18['".$i."']},
                {'data': dataMap.dataU9['".$i."']},
                {'data': dataMap.dataU21['".$i."']},
                {'data': dataMap.dataL8['".$i."']},
                {'data': dataMap.dataL18['".$i."']}
            ]
        },";
    }
    foreach ($data[$i] as $key => $month) {
        $outG9.=$month['G9'].",";
        $outG9_2.=$data2[$i][$key]['G9'].",";
        $outG18.=$month['G18'].",";
        $outG18_2.=$data2[$i][$key]['G18'].",";
        $outU9.=$month['U9'].",";
         $outU9_2.=$data2[$i][$key]['U9'].",";
        $outU21.=$month['U21'].",";
         $outU21_2.=$data2[$i][$key]['U21'].",";
        $outL8.=$month['L8'].",";
         $outL8_2.=$data2[$i][$key]['L8'].",";
        $outL18.=$month['L18'].",";
         $outL18_2.=$data2[$i][$key]['L18'].",";
    }
    $outG9=substr($outG9, 0,-1);
    $outG9.="],";
    $outG9_2=substr($outG9_2, 0,-1);
    $outG9_2.="],";
    $outG18=substr($outG18, 0,-1);
    $outG18.="],";
    $outG18_2=substr($outG18_2, 0,-1);
    $outG18_2.="],";
    $outU9=substr($outU9, 0,-1);
    $outU9.="],";
    $outU9_2=substr($outU9_2, 0,-1);
    $outU9_2.="],";
    $outU21=substr($outU21, 0,-1);
    $outU21.="],";
    $outU21_2=substr($outU21_2, 0,-1);
    $outU21_2.="],";
    $outL8=substr($outL8, 0,-1);
    $outL8.="],";
    $outL8_2=substr($outL8_2, 0,-1);
    $outL8_2.="],";
    $outL18=substr($outL18, 0,-1);
    $outL18.="],";
    $outL18_2=substr($outL18_2, 0,-1);
    $outL18_2.="],";
}
$outG9=substr($outG9, 0,-1);
$outG9_2=substr($outG9_2, 0,-1);
$outG18=substr($outG18, 0,-1);
$outG18_2=substr($outG18_2, 0,-1);
$outU9=substr($outU9, 0,-1);
$outU9_2=substr($outU9_2, 0,-1);
$outU21=substr($outU21, 0,-1);
$outU21_2=substr($outU21_2, 0,-1);
$outL8=substr($outL8, 0,-1);
$outL8_2=substr($outL8_2, 0,-1);
$outL18=substr($outL18, 0,-1);
$outL18_2=substr($outL18_2, 0,-1);
$years=substr($years, 0,-1);
$dataArrays=substr($dataArrays, 0,-1);
/*
echo $outG9;
echo "<hr>";
echo $outG18;
echo "<hr>";
echo $outU9;
echo "<hr>";
echo $outU21;*/
//echo $outG9_2;

?>
  <div id="main" style="height:450px"></div>
    <!-- ECharts import -->
    <script src="<?=$config['explorer_url']?>javascripts/echarts-2.2.7/build/dist/echarts.js"></script>
    <script type="text/javascript">

    var dataMap = {};
function dataFormatter(obj) {
    var pList = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var temp;
    var max = 0;
    for (var year = 1999; year <= 2016; year++) {
        temp = obj[year];
        for (var i = 0, l = temp.length; i < l; i++) {
            max = Math.max(max, temp[i]);
            obj[year][i] = {
                name : pList[i],
                value : temp[i]
            }
        }
        obj[year+'max'] = Math.floor(max/100) * 100;
    }
    return obj;
}

function dataMix(list) {
    var mixData = {};
    for (var i = 0, l = list.length; i < l; i++) {
        for (var key in list[i]) {
            if (list[i][key] instanceof Array) {
                mixData[key] = mixData[key] || [];
                for (var j = 0, k = list[i][key].length; j < k; j++) {
                    mixData[key][j] = mixData[key][j] 
                                      || {name : list[i][key][j].name, value : []};
                    mixData[key][j].value.push(list[i][key][j].value);
                }
            }
        }
    }
    return mixData;
}

dataMap.dataG9 = dataFormatter({
    <?=$outG9_2?>
});

dataMap.dataG18 = dataFormatter({
   <?=$outG18_2?>
});


dataMap.dataU9 = dataFormatter({
    <?=$outU9_2?>
});


dataMap.dataU21 = dataFormatter({
    <?=$outU21_2?>
});


dataMap.dataL8 = dataFormatter({
    <?=$outL8_2?>
});
;

dataMap.dataL18 = dataFormatter({
    <?=$outL18_2?>
});


//dataMap.dataAll = dataMix([dataMap.dataG9, dataMap.dataG18,dataMap.dataU9, dataMap.dataU21,dataMap.dataL8, dataMap.dataL18]);
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
                 label : {
                    formatter : function(s) {
                        return s.slice(0, 4);
                    }
                },
                autoPlay : true,
                playInterval : 4000
            },
            options:[
                {
                    title : {
                        'text':'1999 DEBARRED BTS SITES',
                        'subtext':'as per NetOne'
                    },
                    tooltip : {'trigger':'axis'},
                    legend : {
                        x:'right',
                        'data':['G9','G18','U9','U21','L8','L18'],
                        'selected':{
                            'G9':true,
                            'G8':true,
                            'U9':true,
                            'U21':true,
                            'L8':true,
                            'L18':true,
                        }
                    },
                    toolbox : {
                        'show':true, 
                        orient : 'vertical',
                        x: 'right', 
                        y: 'center',
                        'feature':{
                            'mark':{'show':false},
                            'dataView':{'show':false,'readOnly':true},
                            'magicType':{
                                'show':true,
                                'type':['line','bar','stack','tiled'],
                                'title' : {
                                    'line' : 'convert to line',
                                    'bar' : 'convert to bar',
                                    'stack' : 'convert to stack',
                                    'tiled' : 'convert to tiled'
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
                            'max':3000
                        },
                        {
                            'type':'value',
                            'name':'#sites',
                            'max':3000
                        }
                    ],
                    series : [
                        {
                            'name':'G9',
                            'type':'line',
                            /*
                            'markLine':{
                                symbol : ['arrow','none'],
                                symbolSize : [4, 2],
                                itemStyle : {
                                    normal: {
                                        lineStyle: {color:'orange'},
                                        barBorderColor:'orange',
                                        label:{
                                            position:'left',
                                            formatter:function(params){
                                                return Math.round(params.value);
                                            },
                                            textStyle:{color:'orange'}
                                        }
                                    }
                                },
                                'data':[{'type':'average','name':'averagae G9'}]
                            },*/
                            'data': dataMap.dataG9['1999']
                        },
                        {
                            'name':'G18','yAxisIndex':1,'type':'line',
                            'data': dataMap.dataG18['1999']
                        },
                        {
                            'name':'U9','yAxisIndex':1,'type':'line',
                            'data': dataMap.dataU9['1999']
                        },
                        {
                            'name':'U21','yAxisIndex':1,'type':'line',
                            'data': dataMap.dataU21['1999']
                        },
                        {
                            'name':'L8','yAxisIndex':1,'type':'line',
                            'data': dataMap.dataL8['1999']
                        },
                        {
                            'name':'L18','yAxisIndex':1,'type':'line',
                            'data': dataMap.dataL18['1999']
                        }
                    ]
                },
                <?=$dataArrays?>
            ]
        };

        // Load data into the ECharts instance 
        myChart.setOption(option); 
    }
);
    </script>

<?php

$query = "SELECT * FROM  MVW_REP_TECHNOS_BTS";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}
$a=0;
$b=0;
$c=0;
for ($i = 0; $i < count($res1['TECHNO']); $i++){
	//echo $res1['TECHNO'][$i]." ".$res1['AMOUNT'][$i]."<br>";
	//echo substr($res1['TECHNO'][$i],-6,6)."<br>";
	if ($res1['AMOUNT'][$i]!=0 AND substr($res1['TECHNO'][$i],-6,6)!="EXISTS" AND substr($res1['TECHNO'][$i],0,2)!="2G" AND substr($res1['TECHNO'][$i],0,2)!="3G" AND substr($res1['TECHNO'][$i],0,2)!="4G"){
		$dataBTS[$a]['label']=$res1['TECHNO'][$i];
		$dataBTS[$a]['value']=$res1['AMOUNT'][$i];
		$a++;
	}else if($res1['TECHNO'][$i]=="2G" or $res1['TECHNO'][$i]=="3G" or $res1['TECHNO'][$i]=="4G"){
		$dataBTSbands[$b]['label']=$res1['TECHNO'][$i];
		$dataBTSbands[$b]['value']=$res1['AMOUNT'][$i];
		if ($res1['TECHNO'][$i]=="4G"){
			$dataBTSbands[$b]['issliced']="1";
		}
		$b++;
	}else if(substr($res1['TECHNO'][$i],-6,6)=="EXISTS"){
		$dataBTSexists[$c]['label']=$res1['TECHNO'][$i];
		$dataBTSexists[$c]['value']=$res1['AMOUNT'][$i];
		$c++;
	}
}
$BTS=json_encode($dataBTS);
$BTSbands=json_encode($dataBTSbands);
$BTSexists=json_encode($dataBTSexists);

$query = "SELECT
	TRIM(UPPER(N1_PHASE)) AS N1_PHASE,
	COUNT(N1_SITETYPE) AS AMOUNTS
	FROM
		MASTER_REPORT
	WHERE
			N1_NBUP LIKE '%NB%'
			AND N1_STATUS = 'IS'
			AND N1_CLASSCODE IN ('RPT')
			OR N1_SITEID = 'N1_SITEID'
	GROUP BY 
	TRIM(UPPER(N1_PHASE))";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}
$a=0;
$b=0;
for ($i = 0; $i < count($res1['N1_PHASE']); $i++){
	if ($res1['N1_PHASE'][$i]=="MINI RPT COILER" or $res1['N1_PHASE'][$i]=="MINI RPT ANDREW"){
		$dataRPT[$a]['label']=$res1['N1_PHASE'][$i];
		$dataRPT[$a]['value']=$res1['AMOUNTS'][$i];
		$a++;
	}else{
		$b=$b+$res1['AMOUNTS'][$i];
	}
}
$dataRPT[$a]['label']="REPEATERS";
$dataRPT[$a]['value']=$b;
$RPT=json_encode($dataRPT);
//echo $BTSexists;
?>
<html>
<head>
<title>Chart</title>
<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/themes/fusioncharts.theme.zune.js"></script>
<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/fusioncharts.maps.js"></script>
<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/maps/fusioncharts.belgium.js"></script>


 
<script type="text/javascript">
  FusionCharts.ready(function(){
    
  	var BTSchart = new FusionCharts({
  		"type": "pie2D",
        "renderAt": "chartBTStechnos",
        "dataFormat": "json",
        "width": "100%",
        "height": "400",
        "dataSource":  {
          "chart": {
            "caption": "BANDS",
            "animate":"1",
	        "exportEnabled":"1",
	        "showHoverEffect": "1",
	        "theme": "zune",
         },
         "data": <?=$BTS?>
      	}
  	});
  	var BTSbandschart = new FusionCharts({
  		"type": "pie2D",
        "renderAt": "chartBTSbands",
        "dataFormat": "json",
        "width": "100%",
        "height": "400",
        "dataSource":  {
          "chart": {
            "caption": "TECHNOS",
            "animate":"1",
	        "exportEnabled":"1",
	        "showHoverEffect": "1",
	        "theme": "zune"
         },
         "data": <?=$BTSbands?>
      	}
  	});
  	var BTSexistschart = new FusionCharts({
  		"type": "pie3D",
        "renderAt": "chartBTSexists",
        "dataFormat": "json",
        "width": "100%",
        "height": "400",
        "dataSource":  {
          "chart": {
            "caption": "EXISTING BANDS",
	        "exportEnabled":"1",
	        "showHoverEffect": "1",
	        "theme": "zune"
         },
         "data": <?=$BTSexists?>
      	}
  	});
  	var RPTchart = new FusionCharts({
  		"type": "pie3D",
        "renderAt": "chartRPT",
        "dataFormat": "json",
        "width": "100%",
        "height": "400",
        "dataSource":  {
          "chart": {
            "caption": "REPEATERS",
	        "exportEnabled":"1",
	        "showHoverEffect": "1",
	        "theme": "zune"
         },
         "data": <?=$RPT?>
      	}
  	});
/*
  	var populationMap = new FusionCharts({
        type: 'belgium',
        renderAt: 'belg',
        width: '100%',
        height: '600',
        dataFormat: 'json',
        dataSource: {
    "map": {
        "showshadow": "0",
        "showlabels": "0",
        "showmarkerlabels": "1",
        "fillcolor": "F1f1f1",
        "bordercolor": "CCCCCC",
        "basefont": "Verdana",
        "basefontsize": "10",
        "markerbordercolor": "000000",
        "markerbgcolor": "FF5904",
        "markerradius": "6",
        "usehovercolor": "0",
        "hoveronempty": "0",
        "showmarkertooltip": "1",
        "canvasBorderColor": "375277",
        "canvasBorderAlpha": "0"
    },
    "markers": {
        "shapes": [
            {
                "id": "myCustomShape",
                "type": "circle",
                "fillcolor": "FFFFFF,333333",
                "fillpattern": "radial",
                "showborder": "0",
                "radius": "4"
            },
            {
                "id": "newCustomShape",
                "type": "circle",
                "fillcolor": "FFFFFF,000099",
                "fillpattern": "radial",
                "showborder": "0",
                "radius": "3"
            }
        ],
        "items": [
            {
                "id": "BR",
                "shapeid": "myCustomShape",
                "x": "178.52",
                "y": "109.41",
                "label": "Brussels",
                "labelpos": "right"
            },
            {
                "id": "01",
                "shapeid": "newCustomShape",
                "x": "58.78",
                "y": "38.19",
                "label": "Zeebrugge",
                "labelpos": "right"
            },
            {
                "id": "02",
                "shapeid": "newCustomShape",
                "x": "67.04",
                "y": "59.87",
                "label": "Brugge",
                "labelpos": "right"
            },
            {
                "id": "03",
                "shapeid": "newCustomShape",
                "x": "26.78",
                "y": "56.77",
                "label": "Oostende"
            },
            {
                "id": "04",
                "shapeid": "newCustomShape",
                "x": "56.72",
                "y": "97.03",
                "label": "Kortrijk"
            },
            {
                "id": "05",
                "shapeid": "newCustomShape",
                "x": "103.17",
                "y": "72.25",
                "label": "Gent",
                "labelpos": "right"
            },
            {
                "id": "06",
                "shapeid": "newCustomShape",
                "x": "134.14",
                "y": "89.8",
                "label": "Aalst",
                "labelpos": "right"
            },
            {
                "id": "07",
                "shapeid": "newCustomShape",
                "x": "177.49",
                "y": "37.16",
                "label": "Antwerp",
                "labelpos": "right"
            },
            {
                "id": "08",
                "shapeid": "newCustomShape",
                "x": "182.65",
                "y": "60.9",
                "label": "Mechelen",
                "labelpos": "right"
            },
            {
                "id": "09",
                "shapeid": "newCustomShape",
                "x": "202.27",
                "y": "74.32",
                "label": "Leuven",
                "labelpos": "right"
            },
            {
                "id": "11",
                "shapeid": "newCustomShape",
                "x": "313.75",
                "y": "239.48",
                "label": "Bastogne"
            },
            {
                "id": "10",
                "shapeid": "newCustomShape",
                "x": "313.75",
                "y": "137.29",
                "label": "Liege",
                "labelpos": "right"
            },
            {
                "id": "12",
                "shapeid": "newCustomShape",
                "x": "239.43",
                "y": "181.67",
                "label": "Namur"
            },
            {
                "id": "13",
                "shapeid": "newCustomShape",
                "x": "183.69",
                "y": "186.83",
                "label": "Charleroi"
            },
            {
                "id": "14",
                "shapeid": "newCustomShape",
                "x": "131.04",
                "y": "173.41",
                "label": "Mons"
            }
        ]
    }
}

            
    }).render();    
*/
	BTSchart.render();
	BTSbandschart.render();
	BTSexistschart.render();
	RPTchart.render();
})
</script>
</head>
<body>
<div class="col-md-12" id="belg"></div>
	<div class="row">
		<div class="col-md-6" id="chartBTSbands"></div>
	  	<div class="col-md-6" id="chartBTStechnos"></div>  
	</div>
	<div class="row">
		<div class="col-md-6" id="chartBTSexists"></div>
	  	<div class="col-md-6" id="chartRPT"></div>  
	</div>
	Details can be viewed at: Y:\Infobase_Reports\reports\TECHNOLOGIES.xlsx
</body>
</html>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query = "SELECT DISTINCT(N1_SITETYPE) FROM MASTER_REPORT ORDER BY N1_SITETYPE";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}
for ($i = 0; $i < count($res1['N1_SITETYPE']); $i++){
	$labels[$i]['label']=$res1['N1_SITETYPE'][$i];
	$label=$res1['N1_SITETYPE'][$i];
	$dataACQ_IS[$label]['value']="0";
	$dataACQ_OH[$label]['value']="0";
	$dataACQ_AD[$label]['value']="0";
	$dataBUF_IS[$label]['value']="0";
	$dataBUF_OH[$label]['value']="0";
	$dataBUF_AD[$label]['value']="0";
	$dataCON_IS[$label]['value']="0";
	$dataCON_OH[$label]['value']="0";
	$dataCON_AD[$label]['value']="0";
	$dataAIR_IS[$label]['value']="0";
	$dataAIR_OH[$label]['value']="0";
	$dataAIR_AD[$label]['value']="0";
}
//echo "<pre>".print_r($dataACQ_IS,true)."</pre>";
$query = "SELECT
N1_PROCESS,
 N1_SITETYPE,
 N1_STATUS,
	COUNT(N1_SITEID) AS AMOUNT
FROM
	MASTER_REPORT
	WHERE IB_RAFID IS NOT NULL
	AND (N1_STATUS='OH' OR N1_STATUS='IS' OR N1_STATUS='AD')
GROUP BY N1_PROCESS,N1_SITETYPE,N1_PROCESS,N1_STATUS
ORDER BY N1_PROCESS,N1_SITETYPE,N1_STATUS";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}

$k=0;
$l=0;
$m=0;
$n=0;

for ($i = 0; $i < count($res1['N1_SITETYPE']); $i++){

	$label=$res1['N1_SITETYPE'][$i];
	if ($res1['N1_PROCESS'][$i]=='ACQUISITION'){
		if ($res1['N1_STATUS'][$i]=="IS"){
			$dataACQ_IS[$label]['value']=$res1['AMOUNT'][$i];
		}else if ($res1['N1_STATUS'][$i]=="OH"){
			$dataACQ_OH[$label]['value']=$res1['AMOUNT'][$i];
		}else if ($res1['N1_STATUS'][$i]=="AD"){
			$dataACQ_AD[$label]['value']=$res1['AMOUNT'][$i];
		}
	}else if ($res1['N1_PROCESS'][$i]=='BUFFER'){
		if ($res1['N1_STATUS'][$i]=="IS"){
			$dataBUF_IS[$label]['value']=$res1['AMOUNT'][$i];
		}else if ($res1['N1_STATUS'][$i]=="OH"){
			$dataBUF_OH[$label]['value']=$res1['AMOUNT'][$i];
		}else if ($res1['N1_STATUS'][$i]=="AD"){
			$dataBUF_AD[$label]['value']=$res1['AMOUNT'][$i];
		}
	}else if ($res1['N1_PROCESS'][$i]=='CONSTRUCTION'){
		if ($res1['N1_STATUS'][$i]=="IS"){
			$dataCON_IS[$label]['value']=$res1['AMOUNT'][$i];
		}else if ($res1['N1_STATUS'][$i]=="OH"){
			$dataCON_OH[$label]['value']=$res1['AMOUNT'][$i];
		}else if ($res1['N1_STATUS'][$i]=="AD"){
			$dataCON_AD[$label]['value']=$res1['AMOUNT'][$i];
		}
	}else if ($res1['N1_PROCESS'][$i]=='ON-AIR'){
		if ($res1['N1_STATUS'][$i]=="IS"){
			$dataAIR_IS[$label]['value']=$res1['AMOUNT'][$i];
		}else if ($res1['N1_STATUS'][$i]=="OH"){
			$dataAIR_OH[$label]['value']=$res1['AMOUNT'][$i];
		}else if ($res1['N1_STATUS'][$i]=="AD"){
			$dataAIR_AD[$label]['value']=$res1['AMOUNT'][$i];
		}
	}
}

$i=0;
$j=0;
$k=0;
$l=0;
foreach ($labels as $key => $label) {
	$labelname= $label['label'];
	//echo $labelname.":".$dataACQ_IS[$labelname]['value']."<br>";
	if ($dataACQ_IS[$labelname]['value']=="0" && $dataACQ_OH[$labelname]['value']=="0" && $dataACQ_AD[$labelname]['value']=="0"){
		unset($dataACQ_IS[$labelname]);
		unset($dataACQ_OH[$labelname]);
		unset($dataACQ_AD[$labelname]);
	}else{
		$catsACQ[$i]['label']= $label['label'];
		$i++;
	}

	if ($dataBUF_IS[$labelname]['value']=="0" && $dataBUF_OH[$labelname]['value']=="0" && $dataBUF_AD[$labelname]['value']=="0"){
		unset($dataBUF_IS[$labelname]);
		unset($dataBUF_OH[$labelname]);
		unset($dataBUF_AD[$labelname]);
	}else{
		$catsBUF[$j]['label']= $label['label'];
		$j++;
	}	

	if ($dataCON_IS[$labelname]['value']=="0" && $dataCON_OH[$labelname]['value']=="0" && $dataCON_AD[$labelname]['value']=="0"){
		unset($dataCON_IS[$labelname]);
		unset($dataCON_OH[$labelname]);
		unset($dataCON_AD[$labelname]);
	}else{
		$catsCON[$k]['label']= $label['label'];
		$k++;
	}	

	if ($dataAIR_IS[$labelname]['value']=="0" && $dataAIR_OH[$labelname]['value']=="0" && $dataAIR_AD[$labelname]['value']=="0"){
		unset($dataAIR_IS[$labelname]);
		unset($dataAIR_OH[$labelname]);
		unset($dataAIR_AD[$labelname]);
	}else{
		$catsAIR[$l]['label']= $label['label'];
		$l++;
	}	
}

//echo "$key<pre>".print_r($cats,true)."</pre>";

$labelsACQ=json_encode($catsACQ);
$labelsBUF=json_encode($catsBUF);
$labelsAIR=json_encode($catsAIR);
$labelsCON=json_encode($catsCON);
$ACQ_IS=json_encode(array_values($dataACQ_IS));
$ACQ_OH=json_encode(array_values($dataACQ_OH));
$ACQ_AD=json_encode(array_values($dataACQ_AD));
$BUF_IS=json_encode(array_values($dataBUF_IS));
$BUF_OH=json_encode(array_values($dataBUF_OH));
$BUF_AD=json_encode(array_values($dataBUF_AD));
$CON_IS=json_encode(array_values($dataCON_IS));
$CON_OH=json_encode(array_values($dataCON_OH));
$CON_AD=json_encode(array_values($dataCON_AD));
$AIR_IS=json_encode(array_values($dataAIR_IS));
$AIR_OH=json_encode(array_values($dataAIR_OH));
$AIR_AD=json_encode(array_values($dataAIR_AD));

$query = "SELECT * FROM  MVW_REP_TECHNOS_BTS";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}

?>

<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/fusioncharts.js"></script>
<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/themes/fusioncharts.theme.zune.js"></script>
<script type="text/javascript">
  FusionCharts.ready(function(){
    var ACQchart = new FusionCharts({
        "type": "stackedColumn3DLine",
        "renderAt": "chartACQ",
        "dataFormat": "json",
        "width": "100%",
        "height": "400",        
        "dataSource":  {
          "chart": {
            "caption": "Sites in ACQUISITION",
            "subCaption": "with a RAF (IS/OH)",
            "showvalues": "1",
            "placeValuesInside": "1",
            "showSum": "1",
	        "palette": "2",
	        "useroundedges": "1",
	        "labelDisplay":"Rotate",
            "slantLabels":"1",
            "logoURL": "http://infobase/bsds/images/logoInfobase.png",
	        "logoAlpha": "40",
	        "logoScale": "50",
	        "logoPosition": "TL",
	        "exportEnabled":"1",
	        "showHoverEffect": "1",
	        "theme": "zune"
          },
          "categories":[
	        {
	            "category":<?=$labelsACQ?>
	        }
	      ],  
	      "dataset":[
	      	{
	      		"seriesname":"IS",
	      		"initiallyHidden": "0",
	      		"data": <?=$ACQ_IS?>
	      	},
	      	{
	      		"seriesname":"OH",
	      		"initiallyHidden": "1",
	      		"data": <?=$ACQ_OH?>
	      	},
	      	{
	      		"seriesname":"AD",
	      		"initiallyHidden": "1",
	      		"data": <?=$ACQ_AD?>
	      	},
	       ]         	
      	}
  	}).render();

  	var BUFFERchart = new FusionCharts({
        "type": "stackedColumn3DLine",
        "renderAt": "chartBUF",
        "dataFormat": "json",
        "width": "100%",
        "height": "400",        
        "dataSource":  {
          "chart": {
            "caption": "Sites in BUFFER",
            "subCaption": "with a RAF (IS/OH)",
            "showvalues": "1",
            "placeValuesInside": "1",
            "showSum": "1",
	        "palette": "2",
	        "useroundedges": "1",
	        "labelDisplay":"Rotate",
            "slantLabels":"1",
            "yAxisMaxValue":"300",
            "logoURL": "http://infobase/bsds/images/logoInfobase.png",
	        "logoAlpha": "40",
	        "logoScale": "50",
	        "logoPosition": "TL",
	        "exportEnabled":"1",
	        "showHoverEffect": "1",
	        "theme": "zune"
          },
          "categories":[
	        {
	            "category":<?=$labelsBUF?>
	        }
	      ],  
	      "dataset":[
	      	{
	      		"seriesname":"IS",
	      		"initiallyHidden": "0",
	      		"data": <?=$BUF_IS?>
	      	},
	      	{
	      		"seriesname":"OH",
	      		"initiallyHidden": "1",
	      		"data": <?=$BUF_OH?>
	      	},
	      	{
	      		"seriesname":"AD",
	      		"initiallyHidden": "1",
	      		"data": <?=$BUF_AD?>
	      	},
	       ]         	
      	}
  	}).render();

	var CONchart = new FusionCharts({
        "type": "stackedColumn3DLine",
        "renderAt": "chartCON",
        "dataFormat": "json",
        "width": "100%",
        "height": "400",        
        "dataSource":  {
          "chart": {
            "caption": "Sites in CONSTRUCTION",
            "subCaption": "with a RAF (IS/OH)",
            "showvalues": "1",
            "placeValuesInside": "1",
            "showSum": "1",
	        "palette": "2",
	        "useroundedges": "1",
	        "labelDisplay":"Rotate",
            "slantLabels":"1",
            "yAxisMaxValue":"300",
            "logoURL": "http://infobase/bsds/images/logoInfobase.png",
	        "logoAlpha": "40",
	        "logoScale": "50",
	        "logoPosition": "TL",
	        "exportEnabled":"1",
	        "showHoverEffect": "1",
	        "theme": "zune"
          },
          "categories":[
	        {
	            "category":<?=$labelsCON?>
	        }
	      ],  
	      "dataset":[
	      	{
	      		"seriesname":"IS",
	      		"initiallyHidden": "0",
	      		"data": <?=$CON_IS?>
	      	},
	      	{
	      		"seriesname":"OH",
	      		"initiallyHidden": "1",
	      		"data": <?=$CON_OH?>
	      	},
	      	{
	      		"seriesname":"AD",
	      		"initiallyHidden": "1",
	      		"data": <?=$CON_AD?>
	      	},
	       ]         	
      	}
  	}).render();

	var AIRchart = new FusionCharts({
        "type": "stackedColumn3DLine",
        "renderAt": "chartAIR",
        "dataFormat": "json",
        "width": "100%",
        "height": "400",        
        "dataSource":  {
          "chart": {
            "caption": "Sites ON-AIR",
            "subCaption": "with a RAF (IS/OH)",
            "showvalues": "1",
            "placeValuesInside": "1",
            "showSum": "0",
	        "palette": "2",
	        "useroundedges": "1",
	        "labelDisplay":"Rotate",
            "slantLabels":"1",
            "yAxisMaxValue":"300",
	        "exportEnabled":"1",
	        "showHoverEffect": "1",
	        "theme": "zune"
          },
          "categories":[
	        {
	            "category":<?=$labelsAIR?>
	        }
	      ],  
	      "dataset":[
	      	{
	      		"seriesname":"IS",
	      		"initiallyHidden": "0",
	      		"data": <?=$AIR_IS?>
	      	},
	      	{
	      		"seriesname":"OH",
	      		"initiallyHidden": "1",
	      		"data": <?=$AIR_OH?>
	      	},
	      	{
	      		"seriesname":"AD",
	      		"initiallyHidden": "1",
	      		"data": <?=$AIR_AD?>
	      	},
	       ]         	
      	}
  	}).render();
})
</script>

  	<div class="row">
	  	 <div class="col-md-6" id="chartACQ"></div>
	 	 <div class="col-md-6" id="chartBUF"></div>
	</div>
	<div class="row">
	  	<div class="col-md-6" id="chartCON"></div>
	  	<div class="col-md-6" id="chartAIR"></div>
	</div>
	
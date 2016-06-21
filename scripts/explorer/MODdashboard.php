<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
if (!$_GET['module']){
  require($config['phpguarddog_path']."/guard.php");
  protect("","Base_RF,Base_TXMN,Base_delivery,Base_other,Base_risk,Partner,Administrators","");
}
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


if ($config['cache']!==false){
  $cachefile = '../cache/'.basename(__FILE__, '.php'); 
  $cachetime = 120 * 60; // 2 hours
  // Serve from the cache if it is younger than $cachetime
  if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {
  include($cachefile);
  echo "<!-- Cached ".date('jS F Y H:i', filemtime($cachefile))." -->";
  exit;
  }

  ob_start();
}



$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query="SELECT DISTINCT(FILETYPE), COUNT(FILENAME) AS AMOUNT FROM RAN_SCAN_TODAY WHERE partner='M4C_RAN'
AND FILETYPE IS NOT NULL
GROUP BY FILETYPE";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $Amounttypes=count($resT['FILETYPE']);
}
for ($i = 0; $i <$Amounttypes; $i++) { 
     $data.='{
          "label": "'.$resT['FILETYPE'][$i].'",
          "value": "'.$resT['AMOUNT'][$i].'"
      },';
}

$query="SELECT DISTINCT(FILETYPE), COUNT(FILENAME) AS AMOUNT FROM RAN_SCAN_TODAY WHERE partner='M4C_RAN'
AND FILETYPE IS NOT NULL AND VALIDATION_RESULT=1
GROUP BY FILETYPE";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $Amounttypes=count($resT['FILETYPE']);
}
for ($i = 0; $i <$Amounttypes; $i++) { 
     $dataVAL.='{
          "label": "'.$resT['FILETYPE'][$i].'",
          "value": "'.$resT['AMOUNT'][$i].'"
      },';
}

$query="SELECT DISTINCT(CLUSTERN), COUNT (RA.RAFID) AS AMOUNT FROM BSDS_RAFV2 RA LEFT JOIN BSDS_RAF_RADIO RF on RA.RAFID=RF.RAFID
WHERE RA.TYPE ='MOD Upgrade' GROUP BY CLUSTERN ORDER BY CLUSTERN ";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $Amounttypes=count($resT['CLUSTERN']);
}

 $series_outCluster1.=  '{
              "seriesname": "DEFINED SITES PER CLUSTER",
              "data": [';

for ($i = 0; $i <$Amounttypes; $i++) { 

      $categoriesCluster.='{ "label" : "'. $resT['CLUSTERN'][$i].'" },';
      $series_outCluster1.=  '{ "value" : "'.$resT['AMOUNT'][$i].'"},';
}
 $series_outCluster1.= '   ]
     },';


$query="SELECT DISTINCT(CLUSTERN), COUNT (RA.RAFID) AS AMOUNT FROM BSDS_RAFV2 RA LEFT JOIN BSDS_RAF_RADIO RF on RA.RAFID=RF.RAFID
WHERE RA.TYPE ='MOD Upgrade' AND RADIO_FUND='NOT OK'  AND CLUSTERN IS NOT NULL GROUP BY CLUSTERN ORDER BY CLUSTERN ";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $Amounttypes=count($resT['CLUSTERN']);
}

 $series_outCluster2.=  '{
              "seriesname": "IN ACQUISITION",
              "data": [';

for ($i = 0; $i <$Amounttypes; $i++) { 
      $series_outCluster2.=  '{ "value" : "'.$resT['AMOUNT'][$i].'"},';
}
 $series_outCluster2.= '   ]
     },';

$query="SELECT DISTINCT(CLUSTERN), COUNT (RA.RAFID) AS AMOUNT FROM BSDS_RAFV2 RA LEFT JOIN BSDS_RAF_RADIO RF on RA.RAFID=RF.RAFID
WHERE RA.TYPE ='MOD Upgrade' AND RADIO_FUND!='NOT OK' AND READY_BUILD='NOT OK' AND CLUSTERN IS NOT NULL GROUP BY CLUSTERN ORDER BY CLUSTERN";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $Amounttypes=count($resT['CLUSTERN']);
}
if ($Amounttypes!=0){
  $series_outCluster2.=  '{
                "seriesname": "IN DESIGN",
                "data": [';
  for ($i = 0; $i <$Amounttypes; $i++) { 
        $series_outCluster2.=  '{ "value" : "'.$resT['AMOUNT'][$i].'"},';
  }
  $series_outCluster2.= '   ]
       },';
}

$query="SELECT DISTINCT(CLUSTERN), COUNT (RA.RAFID) AS AMOUNT FROM BSDS_RAFV2 RA LEFT JOIN BSDS_RAF_RADIO RF on RA.RAFID=RF.RAFID
WHERE RA.TYPE ='MOD Upgrade' AND RADIO_FUND!='NOT OK' AND READY_BUILD!='NOT OK' AND NET1_PAC='NOT OK'  AND CLUSTERN IS NOT NULL GROUP BY CLUSTERN ORDER BY CLUSTERN ";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $Amounttypes=count($resT['CLUSTERN']);
}
if ($Amounttypes!=0){
  $series_outCluster2.=  '{
                "seriesname": "IN CONSTRUCTION",
                "data": [';
  for ($i = 0; $i <$Amounttypes; $i++) { 
        $series_outCluster2.=  '{ "value" : "'.$resT['AMOUNT'][$i].'"},';
  }
  $series_outCluster2.= '   ]
       },';
}

$query="SELECT DISTINCT(CLUSTERN), COUNT (RA.RAFID) AS AMOUNT FROM BSDS_RAFV2 RA LEFT JOIN BSDS_RAF_RADIO RF on RA.RAFID=RF.RAFID
WHERE RA.TYPE ='MOD Upgrade' AND RADIO_FUND!='NOT OK' AND READY_BUILD!='NOT OK' AND NET1_PAC!='NOT OK' AND CLUSTERN IS NOT NULL GROUP BY CLUSTERN ORDER BY CLUSTERN ";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $Amounttypes=count($resT['CLUSTERN']);
}
if ($Amounttypes!=0){
  $series_outCluster2.=  '{
                "seriesname": "ON-AIR",
                "data": [';

  for ($i = 0; $i <$Amounttypes; $i++) { 
        $series_outCluster2.=  '{ "value" : "'.$resT['AMOUNT'][$i].'"},';
  }
  $series_outCluster2.= '   ]
       },';
}



$query="SELECT DISTINCT(ACTION), COUNT(RAFID) AS AMOUNT FROM VW_RAF_ACTIONS_BY_DUPL
WHERE RAFTYPE='MOD Upgrade' 
GROUP BY ACTION";
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtT);
    $Amountactions=count($resT['ACTION']);
}
for ($i = 0; $i <$Amountactions; $i++) { 
     $data_actions.='{
          "label": "'.$resT['ACTION'][$i].'",
          "value": "'.$resT['AMOUNT'][$i].'"
      },';
}

?>
<br>
<div class="pull-left" style="margin: 0 10px;min-width:300px;">
  <div class="panel panel-default">
    <div class="panel-body" id="FiletypeM4Cdashbord">
      <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading graph 'FILES ON M4C RAN'...
    </div>
    <i>&nbsp;&nbsp;Click on the graph to display the files</i>
    <div class="panel-body" id="FiletypeM4CVALdashbord">
      <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading graph 'VALIDATED FILES'...
    </div>
  </div>
</div>

<div class="pull-left" style="margin: 0 10px;min-width:300px;">
  <div class="panel panel-default">
    <div class="panel-body" id="Actionsdashbord">
      <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading graph 'RAF MOD ACTIONS'...
    </div>
  </div>
</div>

<div class="pull-left" style="margin: 0 10px;min-width:300px;">
  <div class="panel panel-default">
    <div class="panel-body" id="Clusterdashbord">
      <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading graph 'SITES PER CLUSTERS'...
    </div>
  </div>
</div>



<script type="text/javascript">
FusionCharts.ready(function () {
    var FiletypeM4CChart = new FusionCharts({
       type: 'pie2d',
        renderAt: 'FiletypeM4Cdashbord',
        width: '350',
        height: '300',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": "FILETYPES ON RAN M4C",
                "showPercentValues": "0",
                "showPercentInTooltip": "1",
                "exportenabled": "1",
                "exportatclient": "0",
                "exporthandler": "http://export.api3.fusioncharts.com",
                "html5exporthandler": "http://export.api3.fusioncharts.com",
                "logoURL": "http://infobase/bsds/images/logoInfobase.png",
                "logoAlpha": "40",
                "logoScale": "50",
                "logoPosition": "BR",
                //"enableSmartLabels": "1",
                "decimals": "1",
                "skipOverlapLabels": "1",
                //Theme
                "theme": "fint"
            },
            "data": [
                <?=$data?>
            ] 
        },
        /*"events":{
          "chartclick" : function(evtObj, argObj){
              //var json = evtObj.sender.getJSONData();
              alert(argObj.categoryLabel+' '+argObj.categoryLabel);             
          }
        }*/
    }).render();
    function handler(eventObj, dataObj) {
      $('#spinner').spin();
      
      jQuery.ajax({
          type: 'POST',
          url: 'scripts/explorer/M4C_files.php',
          data: { filetype: dataObj.categoryLabel },
          success: function(data) {
              bootbox.dialog({
                  message: data,
                  title: dataObj.categoryLabel+' files found on RAN M4C:</h4>',
              });
              $('#spinner').spin(false);
          }
      });
        //var json = eventObj.sender.getJSONData();
        //alert(dataObj.categoryLabel + " | Value: " + dataObj.dataValue);
    }
    FiletypeM4CChart.addEventListener("dataplotClick", handler);
});



FusionCharts.ready(function () {
    var ageGroupChart = new FusionCharts({
       type: 'pie2d',
        renderAt: 'FiletypeM4CVALdashbord',
        width: '350',
        height: '300',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": "VALIDATED FILETYPES ON RAN M4C",
                "showPercentValues": "0",
                "showPercentInTooltip": "1",
                "exportenabled": "1",
                "exportatclient": "0",
                "exporthandler": "http://export.api3.fusioncharts.com",
                "html5exporthandler": "http://export.api3.fusioncharts.com",
                "logoURL": "http://infobase/bsds/images/logoInfobase.png",
                "logoAlpha": "40",
                "logoScale": "50",
                "logoPosition": "BR",
                //"enableSmartLabels": "1",
                "decimals": "1",
                "skipOverlapLabels": "1",
                //Theme
                "theme": "fint"
            },
            "data": [
                <?=$dataVAL?>
            ]
        }
    }).render();
});


FusionCharts.ready(function () {
  var clusterChart = new FusionCharts({
    "type": "scrollstackedcolumn2d",
    "renderAt": "Clusterdashbord",
    "width": "600",
    "height": "300",
    "dataFormat": "json",
    "dataSource": {
      "chart": {
          "caption": "RAF's PER CLUSTER IN PROGRESS",
          "showvalues": "1",
          "plotgradientcolor": "",
          "exportenabled": "1",
          "exportatclient": "0",
          "exporthandler": "http://export.api3.fusioncharts.com",
          "html5exporthandler": "http://export.api3.fusioncharts.com",
          "logoURL": "http://infobase/bsds/images/logoInfobase.png",
          "logoAlpha": "40",
          "logoScale": "50",
          "logoPosition": "BR",
          "formatnumberscale": "0",
          "showplotborder": "0",
          "theme": "fint",
          "canvaspadding": "0",
          "bgcolor": "FFFFFF",
          "showalternatehgridcolor": "0",
          "divlinecolor": "CCCCCC",
          "showcanvasborder": "0",
          "legendborderalpha": "0",
          "legendshadow": "0",
          "interactivelegend": "1",
          "showpercentvalues": "0",
          "showSum": "1",
          "canvasborderalpha": "0",
          "showborder": "0",
          "showHoverEffect": "1",
          "scrollToEnd": "1",
          "plotHighlightEffect": "fadeout",
          "flatScrollBars": "1",
          "scrollheight": "10",
      },
      "categories": [
          {
              "category": [
                  <?=$categoriesCluster?>
              ]
          }
      ],

          "dataset": [
              <?=$series_outCluster2?>
          ]
    }
  }).render();
});

FusionCharts.ready(function () {
    var actionsChart = new FusionCharts({
       type: 'pie2d',
        renderAt: 'Actionsdashbord',
        
        width: '400',
        height: '300',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": "OUTSTANDING ACTIONS MOD Upgrades",
                "showPercentValues": "0",
                "showPercentInTooltip": "1",
                "exportenabled": "1",
                "exportatclient": "0",
                "exporthandler": "http://export.api3.fusioncharts.com",
                "html5exporthandler": "http://export.api3.fusioncharts.com",
                "logoURL": "http://infobase/bsds/images/logoInfobase.png",
                "logoAlpha": "40",
                "logoScale": "50",
                "logoPosition": "BR",
                //"enableSmartLabels": "1",
                "decimals": "1",
                "skipOverlapLabels": "1",
                //Theme
                "theme": "fint"
            },
            "data": [
                <?=$data_actions?>
            ]
        }
    }).render();
});
</script>

<?php 
if ($config['cache']!==false){
  $fp = fopen($cachefile, 'w'); // open the cache file for writing
  fwrite($fp, ob_get_contents()); // save the contents of output buffer to the file
  fclose($fp); // close the file
  ob_end_flush(); // Send the output to the browser
}
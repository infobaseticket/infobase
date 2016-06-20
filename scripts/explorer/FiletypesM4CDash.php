<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Base_RF,Base_TXMN,Base_delivery,Base_other,Base_risk,Partner,Administrators","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$cachefile = '../cache/'.basename(__FILE__, '.php'); 
$cachetime = 120 * 60; // 2 hours
// Serve from the cache if it is younger than $cachetime
if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {
include($cachefile);
echo "<!-- Cached ".date('jS F Y H:i', filemtime($cachefile))." -->";
exit;
}

ob_start();

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
?>
<div id="Filteypes-container"></div>

<script type="text/javascript">
FusionCharts.ready(function () {
    var ageGroupChart = new FusionCharts({
       type: 'pie2d',
        renderAt: 'Filteypes-container',
        
        width: '350',
        height: '300',
        dataFormat: 'json',
        dataSource: {
            "chart": {
                "caption": "FILETYPES ON RAN M4C",
                "showPercentValues": "0",
                "showPercentInTooltip": "1",
                //"enableSmartLabels": "1",
                "decimals": "1",
                "skipOverlapLabels": "1",
                //Theme
                "theme": "fint"
            },
            "data": [
                <?=$data?>
            ]
        }
    }).render();
});
</script>

<?php 
$fp = fopen($cachefile, 'w'); // open the cache file for writing
fwrite($fp, ob_get_contents()); // save the contents of output buffer to the file
fclose($fp); // close the file
ob_end_flush(); // Send the output to the browser

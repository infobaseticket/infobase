<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");
?>
	<link rel="stylesheet" href="http://openlayers.org/en/v3.5.0/css/ol.css" type="text/css">
    <style>
      .map {
        height: 400px;
        width: 100%;
      }
    </style>
    <script src="http://openlayers.org/en/v3.5.0/build/ol.js" type="text/javascript"></script>
<?php
$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

//echo "<pre>".print_r($dataACQ_IS,true)."</pre>";
$query = "SELECT
N1_PROCESS2,
	COUNT(N1_SITEID) AS AMOUNT
FROM
	MASTER_REPORT
	WHERE IB_RAFID IS NOT NULL
AND N1_PROCESS='ACQUISITION'
	AND (N1_STATUS='OH' OR N1_STATUS='IS' OR N1_STATUS='AD')
GROUP BY N1_PROCESS2
ORDER BY N1_PROCESS2";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}


?>
<table class="table">

<?php
for ($i = 0; $i < count($res1['N1_PROCESS2']); $i++){
	echo "<tr><td>".$res1['N1_PROCESS2'][$i]."</td><td>".$res1['AMOUNT'][$i]."</td></tr>";
}

?>
</table>

<div id="map" class="map"></div>
    <script type="text/javascript">
      var map = new ol.Map({
        target: 'map',
        layers: [
          new ol.layer.Tile({
            source: new ol.source.MapQuest({layer: 'sat'})
          })
        ],
        view: new ol.View({
          center: ol.proj.transform([37.41, 8.82], 'EPSG:4326', 'EPSG:3857'),
          zoom: 4
        })
      });
    </script>
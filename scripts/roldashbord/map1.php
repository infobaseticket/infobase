<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_RF","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query = "SELECT
*
FROM
	MASTER_REPORT
	WHERE IB_RAFID IS NOT NULL
	AND (N1_STATUS='OH' OR N1_STATUS='IS' OR N1_STATUS='AD')
	AND N1_PROCESS='ACQUISITION'";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}

for ($i = 0; $i < count($res1['N1_CANDIDATE']); $i++){
	
	$xcoord=$res1['ASSET_X'][$i];
	$ycoord=$res1['ASSET_Y'][$i];
	if ($xcoord!='' && $ycoord!=''){
		$pointsACQ.="L.marker([".$ycoord.", ".$xcoord."]).bindPopup('".$res1['N1_CANDIDATE'][$i]."<br>".$res1['N1_SITETYPE'][$i]."').addTo(cities),";
	}
}
$pointsACQ=substr($pointsACQ, 0,-1).";";

$query = "SELECT
*
FROM
	MASTER_REPORT
	WHERE IB_RAFID IS NOT NULL
	AND (N1_STATUS='OH' OR N1_STATUS='IS' OR N1_STATUS='AD')
	AND N1_PROCESS='CONSTRUCTION'";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}

for ($i = 0; $i < count($res1['N1_CANDIDATE']); $i++){
	
	$xcoord=$res1['ASSET_X'][$i];
	$ycoord=$res1['ASSET_Y'][$i];
	if ($xcoord!='' && $ycoord!=''){
		$pointsCON.="L.marker([".$ycoord.", ".$xcoord."]).bindPopup('".$res1['N1_CANDIDATE'][$i]."<br>".$res1['N1_SITETYPE'][$i]."').addTo(construction),";
	}
}
$pointsCON=substr($pointsCON, 0,-1).";";

//echo $points;
?>
<!DOCTYPE html>
<html>
<head>
<title>Attributions example</title>
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
<link rel="stylesheet" href="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-search/src/leaflet-search.css">

<link href='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.2/leaflet.fullscreen.css' rel='stylesheet' />
<style>
#map { height: 180px; }


.menu-ui {
  background:#fff;
  position:absolute;
  top:10px;right:10px;
  z-index:1;
  border-radius:3px;
  width:120px;
  border:1px solid rgba(0,0,0,0.4);
  }
  .menu-ui a {
    font-size:13px;
    color:#404040;
    display:block;
    margin:0;padding:0;
    padding:10px;
    text-decoration:none;
    border-bottom:1px solid rgba(0,0,0,0.25);
    text-align:center;
    }
    .menu-ui a:first-child {
      border-radius:3px 3px 0 0;
      }
    .menu-ui a:last-child {
      border:none;
      border-radius:0 0 3px 3px;
      }
    .menu-ui a:hover {
      background:#f8f8f8;
      color:#404040;
      }
    .menu-ui a.active {
      background:#3887BE;
      color:#FFF;
      }
      .menu-ui a.active:hover {
        background:#3074a4;
        }
</style>

</head>
<body>
	<nav id='menu-ui' class='menu-ui'></nav>
	<div id="map" style="width: 100%; height: 600px"></div>

	<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-providers.js"></script>
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-search/src/leaflet-search.js"></script>
	<script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.2/Leaflet.fullscreen.min.js'></script>




	<script>
		var cities = new L.LayerGroup();

		<?php echo $pointsACQ; ?>

		var construction = new L.LayerGroup();

		<?php echo $pointsCON; ?>


	    var mbAttr = 'Map data &copy; <a href="http://openstreetmap.org">Powered by Flow</a>',
			mbUrl = 'https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png';

	    var grayscale   = L.tileLayer(mbUrl, {id: 'examples.map-20v6611k', attribution: mbAttr}),
		    streets  = L.tileLayer(mbUrl, {id: 'examples.map-i875mjb7',   attribution: mbAttr}),
		    test = L.tileLayer.provider('MapQuestOpen.Aerial');

var layers = document.getElementById('menu-ui');



		var southWest = L.latLng(50.500, 4.735),
		    northEast = L.latLng(40.518, 4.905),
		    bounds = L.latLngBounds(southWest, northEast);

		var map = L.map('map', {
			center: [50.508, 4.785],
			zoom: 8,
			fullscreenControl: true,
			/*maxBounds: bounds,*/
			layers: [grayscale, cities, construction]
		});

addLayer(grayscale, 'Base Map', 1);
addLayer(streets, 'Bike Lanes', 2);
addLayer(test, 'Bike Stations', 3);

function addLayer(layer, name, zIndex) {
    layer
        .setZIndex(zIndex)
        .addTo(map);

    // Create a simple layer switcher that
    // toggles layers on and off.
    var link = document.createElement('a');
        link.href = '#';
        link.className = 'active';
        link.innerHTML = name;

    link.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (map.hasLayer(layer)) {
            map.removeLayer(layer);
            this.className = '';
        } else {
            map.addLayer(layer);
            this.className = 'active';
        }
    };

    layers.appendChild(link);
}

		map.addControl( new L.Control.Search({
		    url: 'http://nominatim.openstreetmap.org/search?format=json&q={s}',
		    jsonpParam: 'json_callback',
		    propertyName: 'display_name',
		    propertyLoc: ['lat','lon']
		}) );

		map.addControl( new L.Control.Search({url: 'search.php?q={s}',
	 		jsonpParam: 'json_callback',
		    propertyName: 'display_name',
		    propertyLoc: ['lat','lon']}));


		var baseLayers = {
			"Grayscale": grayscale,
			"Streets": streets,
			"Test": test
		};

		var overlays = {
			"Sites in Acquisition": cities,
			"Sites in Construction": construction
		}

		L.control.layers(baseLayers, overlays).addTo(map);
	</script>
</body>
</html>

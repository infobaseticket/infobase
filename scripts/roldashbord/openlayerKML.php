<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_RF","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
/*
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
		$pointsACQ.="L.circleMarker([".$ycoord.", ".$xcoord."]).bindLabel('Hello World', { direction: 'left',color: '#fff', }).bindPopup('".$res1['N1_CANDIDATE'][$i]."($xcoord $ycoord)<br>".$res1['N1_SITETYPE'][$i]."').addTo(acquisition),";
		
	}
}
$pointsACQ=substr($pointsACQ, 0,-1).";";
*/
//echo $points;
?>
<!DOCTYPE html>
<html>
<head>
<title>Attributions example</title>
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
<link rel="stylesheet" href="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-search/src/leaflet-search.css">
<link href='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.2/leaflet.fullscreen.css' rel='stylesheet' />
<link rel="stylesheet" href="<?=$config['explorer_url']?>javascripts/leaflet/sidebar-v2/css/leaflet-sidebar.min.css">
<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

<link class="cssdeck" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.1/css/bootstrap-responsive.min.css" class="cssdeck">
<style>
#map { height: 180px; }
/*   Label */
.leaflet-label {
	background: rgb(235, 235, 235);
	background: rgba(235, 235, 235, 0.81);
	background-clip: padding-box;
	border-color: #777;
	border-color: rgba(0,0,0,0.25);
	border-radius: 4px;
	border-style: solid;
	border-width: 4px;
	color: #111;
	display: block;
	font: 12px/20px "Helvetica Neue", Arial, Helvetica, sans-serif;
	font-weight: bold;
	padding: 1px 6px;
	position: absolute;
	-webkit-user-select: none;
	   -moz-user-select: none;
	    -ms-user-select: none;
	        user-select: none;
	pointer-events: none;
	white-space: nowrap;
	z-index: 6;
}

.leaflet-label.leaflet-clickable {
	cursor: pointer;
	pointer-events: auto;
}

.leaflet-label:before,
.leaflet-label:after {
	border-top: 6px solid transparent;
	border-bottom: 6px solid transparent;
	content: none;
	position: absolute;
	top: 5px;
}

.leaflet-label:before {
	border-right: 6px solid black;
	border-right-color: inherit;
	left: -10px;
}

.leaflet-label:after {
	border-left: 6px solid black;
	border-left-color: inherit;
	right: -10px;
}

.leaflet-label-right:before,
.leaflet-label-left:after {
	content: "";
}
/* tree css */

.circle {
	border-radius: 50%;
  	display: inline-block;
  	margin-right: 20px;
  	 width: 15px;
  height: 15px;
	/* width and height can be anything, as long as they're equal */
}

.buffer_circle {
 
  background:#ff78ff;
}
.buffer{
	color:#ff78ff;
}
.acquisition{
	color:#ff7800;
}
ul.nav-list .checkbox{
	min-height: 10px;
}
ul.nav-list .checkbox{
	line-height: 10px;
}
ul.nav-list label{
	margin-bottom:0px;
	font-size: 11px;
}
	


</style>
</head>
<body>

	<div id="sidebar" class="sidebar collapsed">
        <!-- Nav tabs -->
        <ul class="sidebar-tabs" role="tablist">
            <li><a href="#home" role="tab"><i class="fa fa-bars"></i></a></li>
            <!--
            <li><a href="#profile" role="tab"><i class="fa fa-user"></i></a></li>
            <li><a href="#messages" role="tab"><i class="fa fa-envelope"></i></a></li>
            <li><a href="#settings" role="tab"><i class="fa fa-gear"></i></a></li>
            -->
        </ul>

        <!-- Tab panes -->
        <div class="sidebar-content active">
            <div class="sidebar-pane" id="home">
<!--
<input type="checkbox" name="rep1" id="buf" class="mapnav" value="1"> <span class="buffer tree-toggler nav-header">Sites in Buffer
-->

            <h1>SELECT LAYERS</h1>
            <ul class="nav nav-list" style="display:block;">
	            <li><label class="tree-toggler nav-header">As per NET1 with RAF</label>
	                <ul class="nav nav-list tree">
	                	<li><label class="tree-toggler nav-header buffer">Buffer sites</label>
            				<ul class="nav nav-list tree">
            					<li>
            						<div class="checkbox">
									    <label class="buffer">
									      <input type="checkbox" id="bufall" class="allcheck mapnav" data-type="buf" data-techno="all"> ALL
									    </label>
									</div>
								</li>
            					<li>
            						<div class="checkbox">
									    <label class="buffer">
									      <input type="checkbox" id="bufG9" class="mapnav buf" data-type="buf" data-techno="G9"> G9 <div class="circle buffer_circle"></div>
									    </label>
									</div>
								</li>
	                            <li>
            						<div class="checkbox">
									    <label class="buffer">
									      <input type="checkbox" id="bufG18" class="mapnav buf" data-type="buf" data-techno="G18"> G18
									    </label>
									</div>
								</li>
								<li>
            						<div class="checkbox">
									    <label class="buffer">
									      <input type="checkbox" id="bufU9" class="mapnav buf" data-type="buf" data-techno="U9"> U9
									    </label>
									</div>
								</li>
								<li>
            						<div class="checkbox">
									    <label class="buffer">
									      <input type="checkbox" id="bufU21" class="mapnav buf" data-type="buf" data-techno="U21"> U21
									    </label>
									</div>
								</li>
								<li>
            						<div class="checkbox">
									    <label class="buffer">
									      <input type="checkbox" id="bufL8" class="mapnav buf" data-type="buf" data-techno="L8"> L8
									    </label>
									</div>
								</li>
								<li>
            						<div class="checkbox">
									    <label class="buffer">
									      <input type="checkbox" id="bufL18" class="mapnav buf" data-type="buf" data-techno="L18"> L18
									    </label>
									</div>
								</li>
            				</ul>
            			</li>
            			<li><label class="tree-toggler nav-header acquisition">Sites in acquisition</label>
            			<ul class="nav nav-list tree">
            					<li>
            						<div class="checkbox">
									    <label class="acquisition">
									      <input type="checkbox" id="acqall" class="allcheck mapnav" data-type="acq" data-techno="all"> ALL
									    </label>
									</div>
								</li>
            					<li>
            						<div class="checkbox">
									    <label class="acquisition">
									      <input type="checkbox" id="acqG9" class="mapnav acq" data-type="acq" data-techno="G9"> G9 <div class
									    </label>
									</div>
								</li>
	                            <li>
            						<div class="checkbox">
									    <label class="acquisition">
									      <input type="checkbox" id="acqG18" class="mapnav acq" data-type="acq" data-techno="G18"> G18
									    </label>
									</div>
								</li>
								<li>
            						<div class="checkbox">
									    <label class="acquisition">
									      <input type="checkbox" id="acqU9" class="mapnav acq" data-type="acq" data-techno="U9"> U9
									    </label>
									</div>
								</li>
								<li>
            						<div class="checkbox">
									    <label class="acquisition">
									      <input type="checkbox" id="acqU21" class="mapnav acq" data-type="acq" data-techno="U21"> U21
									    </label>
									</div>
								</li>
								<li>
            						<div class="checkbox">
									    <label class="acquisition">
									      <input type="checkbox" id="acqL8" class="mapnav acq" data-type="acq" data-techno="L8"> L8
									    </label>
									</div>
								</li>
								<li>
            						<div class="checkbox">
									    <label class="acquisition">
									      <input type="checkbox" id="acqL18" class="mapnav acq" data-type="acq" data-techno="L18"> L18
									    </label>
									</div>
								</li>
            				</ul>
            			</li>
           				<li><input type="checkbox" name="rep1" id="con" class="mapnav" value="1"> <span style='color:#0000ff'>Sites in Construction</span></li>
           				<li><input type="checkbox" name="rep1" id="air" class="mapnav" value="1"> <span style='color:#00ff00'>Sites on-air</span></li>
	                    <li><label class="tree-toggler nav-header">Header 1.1</label>
	                        <ul class="nav nav-list tree">
	                            <li><a href="#">Link</a></li>
	                            <li><a href="#">Link</a></li>
	                            <li><label class="tree-toggler nav-header">Header 1.1.1</label>
	                                <ul class="nav nav-list tree">
	                                    <li><a href="#">Link</a></li>
	                                    <li><a href="#">Link</a></li>
	                                </ul>
	                            </li>
	                        </ul>
	                    </li>
	                </ul>
	            </li>
	            <li class="divider"></li>
	            <li><label class="tree-toggler nav-header">Header 2</label>
	                <ul class="nav nav-list tree">
	                    <li><a href="#">Link</a></li>
	                    <li><a href="#">Link</a></li>
	                    <li><label class="tree-toggler nav-header">Header 2.1</label>
	                        <ul class="nav nav-list tree">
	                            <li><a href="#">Link</a></li>
	                            <li><a href="#">Link</a></li>
	                            <li><label class="tree-toggler nav-header">Header 2.1.1</label>
	                                <ul class="nav nav-list tree">
	                                    <li><a href="#">Link</a></li>
	                                    <li><a href="#">Link</a></li>
	                                </ul>
	                            </li>
	                        </ul>
	                    </li>
	                    <li><label class="tree-toggler nav-header">Header 2.2</label>
	                        <ul class="nav nav-list tree">
	                            <li><a href="#">Link</a></li>
	                            <li><a href="#">Link</a></li>
	                            <li><label class="tree-toggler nav-header">Header 2.2.1</label>
	                                <ul class="nav nav-list tree">
	                                    <li><a href="#">Link</a></li>
	                                    <li><a href="#">Link</a></li>
	                                </ul>
	                            </li>
	                        </ul>
	                    </li>
	                </ul>
	            </li>
	        </ul>
            	


            </div>
            <div class="sidebar-pane" id="profile">

            <h1>SELECT BLABL</h1>
           
           	
            </div>
            <div class="sidebar-pane" id="messages"><h1>Messages</h1></div>
            <div class="sidebar-pane" id="settings"><h1>Settings</h1></div>
        </div>
    </div>

	<div id="map" class="sidebar-map" style="width: 100%; height: 600px"></div>

	<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
	<!-- Script for easy tiles providing -->
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-providers.js"></script>

	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-search/src/leaflet-search.js"></script>

	<!--label code -->
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-label/src/Label.js"></script>
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-label/src/BaseMarkerMethods.js"></script>
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-label/src/Marker.Label.js"></script>
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-label/src/CircleMarker.Label.js"></script>
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-label/src/Path.Label.js"></script>
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-label/src/Map.Label.js"></script>
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-label/src/FeatureGroup.Label.js"></script>
	
	<!--<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/leaflet-label/src/ajax.js"></script>-->
	<script src='//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v0.0.2/Leaflet.fullscreen.min.js'></script>
	<script type="text/javascript" src="<?=$config['explorer_url']?>javascripts/leaflet/sidebar-v2/js/leaflet-sidebar.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>

	<script src='https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-omnivore/v0.2.0/leaflet-omnivore.min.js'></script>
	<script language="javascript">

	$(document).ready(function () {
		$('.tree-toggler').click(function () {
			$(this).parent().children('ul.tree').toggle(300);
		});
	});

	function onEachFeature(feature, layer) {
	    // does this feature have a property named popupContent?
	    if (feature.properties && feature.properties.popupContent) {
	        layer.bindPopup(feature.properties.popupContent);
	    }
	}


/*
		var acquisition = new L.LayerGroup();
		<?php echo $pointsACQ; ?>
		var construction = new L.LayerGroup();
		<?php echo $pointsCON; ?>
*/

    var mbAttr = 'Map data &copy; <a href="http://openstreetmap.org">Powered by Flow</a>',
		mbUrl = 'https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png';

    var grayscale   = L.tileLayer(mbUrl, {id: 'examples.map-20v6611k', attribution: mbAttr}),
	    streets  = L.tileLayer(mbUrl, {id: 'examples.map-i875mjb7',   attribution: mbAttr}),
	    mapquest = L.tileLayer.provider('MapQuestOpen.Aerial'),
	    Esri_WorldImagery = L.tileLayer('http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
			attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'}),
	    OpenWeatherMap_RainClassic = L.tileLayer('http://{s}.tile.openweathermap.org/map/rain_cls/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: 'Map data &copy; <a href="http://openweathermap.org">OpenWeatherMap</a>',
			opacity: 0.5
		}),
		OpenWeatherMap_CloudsClassic = L.tileLayer('http://{s}.tile.openweathermap.org/map/clouds_cls/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: 'Map data &copy; <a href="http://openweathermap.org">OpenWeatherMap</a>',
			opacity: 0.5
		});

	var map = L.map('map', {
		center: [50.508, 4.785],
		zoom: 8,
		fullscreenControl: true,
		layers: [grayscale, /*acquisition, construction*/]
	});
	


	var layernames = {};

	$('.mapnav').change(function() {

		var type=$(this).data('type');
		var techno=$(this).data('techno');
		var id=$(this).attr('id');

		if ($(this).is(':checked')){

			//alert(techno);
			
			if(type=='acq'){
				var bulletcolor='#ff7800';
				var strokecolor='#ff7800';
			}else if(type=='con'){
				var bulletcolor='#0000ff';
				var strokecolor='#0000ff';
			}else if(type=='air'){
				var bulletcolor='#00ff00';
				var strokecolor='#00ff00';
			}else if(type=='buf'){
				var bulletcolor='#ff78ff';
				var strokecolor='#ff78ff';
			}

			if(techno=='G9'){
				var strokecolor='#f0f';
			}else if(techno=='G18'){
				var strokecolor='#ff0';
			}else if(techno=='U9'){
				var strokecolor='#0ff';
			}else if(techno=='U21'){
				var strokecolor='#aca';
			}else if(techno=='L8'){
				var strokecolor='#3c3';
			}else if(techno=='L18'){
				var strokecolor='#c3c';
			}
			 
			if(id=='bufall' || id=='acqall'){
				if (map.hasLayer(layernames[type+'G9'])){
					map.removeLayer(layernames[type+'G9']);
				}
				if (map.hasLayer(layernames[type+'G18'])){
					map.removeLayer(layernames[type+'G18']);
				}
				if (map.hasLayer(layernames[type+'U9'])){
					map.removeLayer(layernames[type+'U9']);
				}
				if (map.hasLayer(layernames[type+'U21'])){
					map.removeLayer(layernames[type+'U21']);
				}
				if (map.hasLayer(layernames[type+'L8'])){
					map.removeLayer(layernames[type+'L8']);
				}
				if (map.hasLayer(layernames[type+'L18'])){
					map.removeLayer(layernames[type+'L18']);
				}
				$('.'+type).prop('checked', true);
			}
			//alert(id+','+type+','+techno+','+bulletcolor+','+strokecolor)
			addlayer(id,type,techno,bulletcolor,strokecolor);
			
		}else{

			if(id=='bufall' || id=='acqall'){
				if (map.hasLayer(layernames[type+'G9'])){
					map.removeLayer(layernames[type+'G9']);
				}
				if (map.hasLayer(layernames[type+'G18'])){
					map.removeLayer(layernames[type+'G18']);
				}
				if (map.hasLayer(layernames[type+'U9'])){
					map.removeLayer(layernames[type+'U9']);
				}
				if (map.hasLayer(layernames[type+'U21'])){
					map.removeLayer(layernames[type+'U21']);
				}
				if (map.hasLayer(layernames[type+'L8'])){
					map.removeLayer(layernames[type+'L8']);
				}
				if (map.hasLayer(layernames[type+'L18'])){
					map.removeLayer(layernames[type+'L18']);
				}
				if (map.hasLayer(layernames[type+'all'])){
					map.removeLayer(layernames[type+'all']);
				}
				$('.'+type).prop('checked', false);
			}else{
				map.removeLayer(layernames[id]);
			}
			
		}
	});

	function addlayer(id,type,techno,bulletcolor,strokecolor){
		$.ajax({
		    type: "POST",
		    url: "dataprovider.php",
		    data: {type:type,techno:techno},
		    dataType: 'json',
		    success: function (response) {

		        layernames[id] = L.geoJson(response, {
		           	onEachFeature: onEachFeature,
		           	pointToLayer: function (feature, latlng) {
						return L.circleMarker(latlng, {
							radius: 6,
							fillColor: bulletcolor,
							color: strokecolor,
							weight: 3,
							opacity: 1,
							fillOpacity: 0.8
						}).bindLabel(feature.properties.title, { direction: 'auto',noHide:false})/*.on('click', function () {
							alert('---');
						})*/;
					}
		        }).addTo(map);
		    }
		});
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

		var sidebar = L.control.sidebar('sidebar').addTo(map);

		var baseLayers = {
			"Grayscale": grayscale,
			"Streets": streets,
			"Mapquest": mapquest,
			"ESRI": Esri_WorldImagery
		};

		var overlays = {
			"Open weather RAIN": OpenWeatherMap_RainClassic,
			"Open weather CLOUDS": OpenWeatherMap_CloudsClassic
			/*"Sites in Acquisition": acquisition,
			"Sites in Construction": construction*/
		}

		L.control.layers(baseLayers, overlays).addTo(map);


$.ajax({
dataType: "json",
url: "fred.geojson",
success: function(data) {
    $(data.features).each(function(key, data) {
        district_boundary.addData(data);
    });
}
}).error(function() {});
		//map.showLabel();
/*
	$('.mapnav').change(function() {
		var id=$(this).attr('id');
		if(id=='acq'){
			name='acquisition';
		}
		alert(name);
		if ($('#'+id).is(':checked')){
			map.addLayer(name);
		}else if (map.hasLayer(name)){
			map.removeLayer(name);
		}
	});
*/

	</script>
</body>
</html>



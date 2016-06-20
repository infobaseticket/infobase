<!DOCTYPE html>
<html>
<head>
<title>GeoJSON example</title>
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.5.0/ol.css" type="text/css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/3.5.0/ol.js"></script>

<!-- LAMBERT: ESPG:31370 
var lambert72Punt = [151600,211900];
var mercatorPunt = ol.proj.transform(lambert72Punt, 'EPSG:31370', 'EPSG:3857');
Openlayers heeft out-of-the-box 2 coördinaatsystemen ingebouwd aanwezig: het geprojecteerde systeem webmercator 
met code ESPG:3857 en het ongeprojecteerde systeem WGS84 met code ESPG:4326. Dit zijn de meeste gebruikte 
coördinaatsysteem bij webtoepassingen ondermeer OpenStreetMap, Bing en Google gebruiken dit
-->
<script src="http://cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.3/proj4.js" type="text/javascript"></script>
<script src="http://epsg.io/31370.js" type="text/javascript"></script>

</head>
<body>
<div class="container-fluid">

<div class="row-fluid">
  <div class="span12">
    <div id="map" class="map"></div>
  </div>
</div>

</div>
<script>

var lambert72Punt = [151600,211900];
var mercatorPunt = ol.proj.transform(lambert72Punt, 'EPSG:31370', 'EPSG:3857');

var image = new ol.style.Circle({
  radius: 5,
  fill: null,
  stroke: new ol.style.Stroke({color: 'yellow', width: 5}),
   fill: new ol.style.Fill({
      color: 'rgba(255,0,0,0.2)'
    })
});

var styles = {
  'Point': [new ol.style.Style({
    image: image
  })],
  'LineString': [new ol.style.Style({
    stroke: new ol.style.Stroke({
      color: 'green',
      width: 1
    })
  })],
  'MultiLineString': [new ol.style.Style({
    stroke: new ol.style.Stroke({
      color: 'green',
      width: 1
    })
  })],
  'MultiPoint': [new ol.style.Style({
    image: image
  })],
  'MultiPolygon': [new ol.style.Style({
    stroke: new ol.style.Stroke({
      color: 'yellow',
      width: 1
    }),
    fill: new ol.style.Fill({
      color: 'rgba(255, 255, 0, 0.1)'
    })
  })],
  'Polygon': [new ol.style.Style({
    stroke: new ol.style.Stroke({
      color: 'blue',
      lineDash: [4],
      width: 3
    }),
    fill: new ol.style.Fill({
      color: 'rgba(0, 0, 255, 0.1)'
    })
  })],
  'GeometryCollection': [new ol.style.Style({
    stroke: new ol.style.Stroke({
      color: 'magenta',
      width: 2
    }),
    fill: new ol.style.Fill({
      color: 'magenta'
    }),
    image: new ol.style.Circle({
      radius: 10,
      fill: null,
      stroke: new ol.style.Stroke({
        color: 'magenta'
      })
    })
  })],
  'Circle': [new ol.style.Style({
    stroke: new ol.style.Stroke({
      color: 'green',
      width: 2
    }),
    fill: new ol.style.Fill({
      color: 'rgba(255,0,0,0.2)'
    })
  })]
};

var styleFunction = function(feature, resolution) {
  return styles[feature.getGeometry().getType()];
};


iconStyle = [
    new ol.style.Circle({
	  radius: 5,
	  fill: null,
	  stroke: new ol.style.Stroke({color: 'red', width: 5}),
	   fill: new ol.style.Fill({
	      color: 'rgba(255,0,0,0.2)'
	    })
	}),
    new ol.style.Style({
        text: new ol.style.Text({
            text: "Wow such label",
            offsetY: -25,
            fill: new ol.style.Fill({
                color: '#fff'
            })
        })
    })
];

var geojsonObject = {
  'type': 'FeatureCollection',
  'crs': {
    'type': 'name',
    'properties': {
      'name': 'EPSG:3857'
    }
  },
  'features': [
    {
      'type': 'Feature',
      "properties":{
                "name":"TRON-02",
            },
      'geometry': {
        'type': 'Point',
        'style': iconStyle,
        'coordinates': mercatorPunt
      }
    },
    /*
    {
      'type': 'Feature',
      'geometry': {
        'type': 'LineString',
        'coordinates': [[4e6, -2e6], [8e6, 2e6]]
      }
    },
    {
      'type': 'Feature',
      'geometry': {
        'type': 'LineString',
        'coordinates': [[4e6, 2e6], [8e6, -2e6]]
      }
    },
    {
      'type': 'Feature',
      'geometry': {
        'type': 'Polygon',
        'coordinates': [[[-5e6, -1e6], [-4e6, 1e6], [-3e6, -1e6]]]
      }
    },
    {
      'type': 'Feature',
      'geometry': {
        'type': 'MultiLineString',
        'coordinates': [
          [[-1e6, -7.5e5], [-1e6, 7.5e5]],
          [[1e6, -7.5e5], [1e6, 7.5e5]],
          [[-7.5e5, -1e6], [7.5e5, -1e6]],
          [[-7.5e5, 1e6], [7.5e5, 1e6]]
        ]
      }
    },
    {
      'type': 'Feature',
      'geometry': {
        'type': 'MultiPolygon',
        'coordinates': [
          [[[-5e6, 6e6], [-5e6, 8e6], [-3e6, 8e6], [-3e6, 6e6]]],
          [[[-2e6, 6e6], [-2e6, 8e6], [0, 8e6], [0, 6e6]]],
          [[[1e6, 6e6], [1e6, 8e6], [3e6, 8e6], [3e6, 6e6]]]
        ]
      }
    },
    {
      'type': 'Feature',
      'geometry': {
        'type': 'GeometryCollection',
        'geometries': [
          {
            'type': 'LineString',
            'coordinates': [[-5e6, -5e6], [0, -5e6]]
          },
          {
            'type': 'Point',
            'coordinates': [4e6, -5e6]
          },
          {
            'type': 'Polygon',
            'coordinates': [[[1e6, -6e6], [2e6, -4e6], [3e6, -6e6]]]
          }
        ]
      }
    }*/
  ]
};


var vectorSource = new ol.source.Vector({
	
  features: (new ol.format.GeoJSON()).readFeatures(geojsonObject)
});



//vectorSource.addFeature(new ol.Feature(new ol.geom.Circle([5e6, 7e6], 1e6)));

var vectorLayer = new ol.layer.Vector({
  source: vectorSource,
  style: styleFunction
});

var map = new ol.Map({
  layers: [
    new ol.layer.Tile({
      source: new ol.source.OSM()
    }),
    vectorLayer
  ],
  target: 'map',
  controls: ol.control.defaults({
    attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
      collapsible: false
    })
  }),
  view: new ol.View({
    center: mercatorPunt,
    zoom: 8
  })
});





</script>
</body>
</html>
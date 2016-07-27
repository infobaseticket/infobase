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
		$pointsACQ.="L.marker([".$ycoord.", ".$xcoord."]).bindPopup('".$res1['N1_CANDIDATE'][$i]."($xcoord $ycoord)<br>".$res1['N1_SITETYPE'][$i]."').addTo(acquisition),";
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

</head>
<body>

	

	<div id="data" id="data"></div>

	<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
	<script language="javascript">

	$(document).ready(function () {
		
		$.ajax({
			  url: "http://www.zendmasten.be/toonxml.php?xzw=2.867115396362351&yzw=51.149253001854994&xno=2.986420053344773&yno=51.20306266066504&g=0&p=0&r=0&b=0&w=0&o=0&q=0&z=0",
			  
			})
			  .done(function( data ) {
			    if ( console && console.log ) {
			      console.log( "Sample of data:", data.slice( 0, 100 ) );
			    }
			  });
	});

		
			 
			
		
	</script>
</body>
</html>

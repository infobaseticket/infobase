<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if($_POST['type']=='acq' or $_POST['type']=='con' or $_POST['type']=='air' or $_POST['type']=='buf'){
	if($_POST['type']=='acq'){
		$N1_PROCESS='ACQUISITION';
	}else if($_POST['type']=='con'){
		$N1_PROCESS='CONSTRUCTION';
	}else if($_POST['type']=='air'){
		$N1_PROCESS='ON-AIR';
	}else if($_POST['type']=='buf'){
		$N1_PROCESS='BUFFER';
	}
	$query = "SELECT
	*
	FROM
		MASTER_REPORT
		WHERE IB_RAFID IS NOT NULL
		AND (N1_STATUS='OH' OR N1_STATUS='IS' OR N1_STATUS='AD')
		AND N1_NBUP='NB'
		AND N1_PROCESS='".$N1_PROCESS."'";
	if($_POST['techno']!="all"){
		$query.=" AND IB_TECHNOS_ACQ LIKE '%".$_POST['techno']."%'";
	}
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
	}

	$geojson = array( 'type' => 'FeatureCollection', 'features' => array());

	for ($i = 0; $i < count($res1['N1_CANDIDATE']); $i++){
		
		$xcoord=$res1['ASSET_X'][$i];
		$ycoord=$res1['ASSET_Y'][$i];

		if ($xcoord!='' && $ycoord!=''){	
			$marker = array(
	                'type' => 'Feature',
	                'features' => array(
	                    'type' => 'Feature',
	                    'properties' => array(
	                        'title' => $res1['N1_SITEID'][$i],
	                        'marker-color' => '#f00',
	                        'marker-size' => 'small',
	                        'popupContent'=> '<b>'.$res1['N1_CANDIDATE'][$i].'</b><br>TECHNOS ACQ: '. $res1['IB_TECHNOS_ACQ'][$i],
	                        //'url' => 
	                        ),
	                    "geometry" => array(
	                        'type' => 'Point',
	                        'coordinates' => array(                            
	                                        $xcoord,
	                                        $ycoord
	                        )
	                    )
	                )
	    );
		array_push($geojson['features'], $marker['features']);
		}
	}

	echo json_encode($geojson);
}

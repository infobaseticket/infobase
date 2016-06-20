<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once('raf_procedures.php');

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

header('Content-type: text/csv');
header('Content-disposition: attachment; filename="RAF_export.csv"');
header("Pragma: no-cache");
header("Expires: 0");


function outputCSV($data) {
    $outstream = fopen("php://output", "w");
    function __outputCSV(&$vals, $key, $filehandler) {
        fputcsv($filehandler, $vals); // add parameters if you want
    }
    array_walk($data, "__outputCSV", $outstream);
    fclose($outstream);
}

ini_set('display_errors',1);


$array[] = array( "RAF REPORT (".$_GET['actionby'].") GENERATED ON ".date('d-m-Y H:i:s'));

$array[] = array("RAF ID","REGION","SITEID","TYPE","OTHER INPUT","RF INPUT","TX INPUT","NET1 LINK","PARTNER INPUT","BCS STATUS","LS&BP OK",
"PARTNER ACQUIRED","TX ACQUIRED","RAF AQUIRED","FUND SITE","PO for Construction","FUNDING DATE","PARTNER PAC","RF PAC","PAC DATE","ACTION BY"
,"BAND","RFINFO","SAC","CON","STATUS","PO for Acquisition","Physical start","Site Integrated","Vendor GSM900","Vendor GSM1800","Vendor 3G",
"BAND GSM900","BAND GSM1800", "BAND UMTS","BAND UMTS900","COMMERCIAL PHASE");

;

$query=create_query($_GET['site_key'],$_GET['region'],$_GET['type'],$_GET['actionby'],$_GET['orderby'],$_GET['order'],$_GET['start'],$_GET['end'],$_GET['phase'],$_GET['commercial'],$_GET['allocated']);
//echo $query."<br>";
//die;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_RAFS=count($res1['SITEID']);
}
//echo $amount_of_RAFS;



$output_raf="";
if ($amount_of_RAFS>=1){
	$row=4;
	for ($i = 0; $i <$amount_of_RAFS; $i++) {

		$TXMN_INP_SEL="";
		$NET1_CREATED_SEL="";
		$PARTNER_INP_SEL="";
		$RADIO_ACC_SEL="";
		$TXMN_ACC_SEL="";
		$status="";


		if ($res1['TYPE'][$i]=="New Indoor" || $res1['TYPE'][$i]=="Indoor Upgrade"  || $res1['TYPE'][$i]=="IND Upgrade"){
			$raf_type="indoor";
		}else{
			$raf_type="outdoor";
		}

		include('raf_color_analysis.php');

		$band="";
		$query2 = "select * FROM BSDS_RAF_RADIO WHERE RAFID='".$res1['RAFID'][$i]."'";
		//echo "<br><br>".$query1;
		$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
		if (!$stmt2) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt2);
			if($res2['BAND_900'][0]==1){
				$band.="GSM900 ";
				$vendor_2G_GSM900=$res2['VENDOR2G_GSM900'][0];
			}
			if($res2['BAND_1800'][0]==1){
				$band.="GSM1800 ";
				$vendor_2G_GSM1800=$res2['VENDOR2G_GSM1800'][0];
			}
			if($res2['BAND_UMTS'][0]==1){
				$band.="UMTS ";
				$vendor_3G=$res2['VENDOR3G'][0];
			}
		}


			$array[] = array($res1['RAFID'][$i],
			  	substr($res1['SITEID'][$i],0,2),
				substr($res1['SITEID'][$i],2),
				$res1['TYPE'][$i]." ".$res1['CANDIDATE'][$i],
				$res1['OTHER_INP'][$i],
				$res1['RADIO_INP'][$i],
				$res1['TXMN_INP'][$i],
				$res1['NET1_LINK'][$i],
				$res1['ALU_INP'][$i],
				$res1['BCS_NET1'][$i],
				$res1['NET1_LBP'][$i],
				$rs1['ALU_ACQUIRED'][$i],
				$res1['TXMN_ACQUIRED'][$i],
				$res1['NET1_AQUIRED'][$i],
				$res1['STATUS_FUND'][$i],
				$res1['UA503'][$i],
				$res1['NET1_FUND'][$i],
				$res1['PAC_STATUS'][$i],
				$res1['RF_PAC'][$i],
				$res1['NET1_PAC'][$i],
				$status.' '.$status_special,
				$band,
				$res1['RFINFO'][$i],
				$res1['SAC'][$i],
				$res1['CON'][$i],
				$res1['STATUS'][$i],
				$res1['UA501'][$i],
				$res1['U459A59'][$i],
				$res1['U571A71'][$i],
				$vendor_2G_GSM900,
				$vendor_2G_GSM1800,
				$vendor_3G,
				$res2['BAND_900'][0],
				$res2['BAND_1800'][0],
				$res2['BAND_UMTS'][0],
				$res2['BAND_UMTS900'][0],
				$res2['COMMERICAL'][0]);
		$row++;
		$letter="";
		$status_special="";
		$vendor_2G_GSM900="";
		$vendor_2G_GSM1800="";
		$vendor_3G="";
		$band_900="";
		$band_1800="";
		$band_UMTS="";

	}
}else{
	echo "NO RAF data found!";
}

outputCSV($array);
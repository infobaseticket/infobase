<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Alcatel,Alcatel_sub","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
include('raf_procedures.php');
require_once($config['sitepath_abs'].'/bsds/PHPlibs/PHPExcel/Classes/PHPExcel.php');
require_once($config['sitepath_abs'].'/bsds/PHPlibs/PHPExcel/Classes/PHPExcel/IOFactory.php');

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
/** Error reporting */
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Infobase")
							 ->setLastModifiedBy("Infobase")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Report exported from Infobase")
							 ->setKeywords("office 2007 openxml php rafreport Infobase")
							 ->setCategory("Report file");

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);

$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('PHPExcel logo');
$objDrawing->setDescription('KPNGroup logo');
$objDrawing->setPath($config['sitepath_abs']."/infobase/files/siteImages/basecompany.png");
$objDrawing->setHeight(70);
$objDrawing->setCoordinates('A1');
$objDrawing->setOffsetX(10);
$objDrawing->setOffsetY(10);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('PHPExcel logo');
$objDrawing->setDescription('KPNGroup logo');
$objDrawing->setPath($config['sitepath_abs']."/infobase/files/siteImages/logo.png");
$objDrawing->setCoordinates('Z1');
$objDrawing->setOffsetY(10);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


$objPHPExcel->getActiveSheet()->mergeCells('E1:Y1');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'RAF REPORT ('.$_POST['actionby'].') GENERATED ON '.date('d-m-Y H:i:s'));
			$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setSize(20);
			-
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A3', 'RAF ID')
	->setCellValue('B3', 'REGION')
	->setCellValue('C3', 'SITEID')
	->setCellValue('D3', 'TYPE')
	->setCellValue('E3', 'OTHER INPUT')
	->setCellValue('F3', 'RF INPUT')
	->setCellValue('G3', 'TX INPUT')
	->setCellValue('H3', 'NET1 LINK')
	->setCellValue('I3', 'PARTNER INPUT')
	->setCellValue('J3', 'BCS STATUS')
	->setCellValue('K3', 'LS&BP OK')
	->setCellValue('L3', 'PARTNER ACQUIRED')
	->setCellValue('M3', 'TX ACQUIRED')
	->setCellValue('N3', 'RAF AQUIRED')
	->setCellValue('O3', 'FUND SITE')
	->setCellValue('P3', 'PO for Construction')
	->setCellValue('Q3', 'FUNDING DATE')
	->setCellValue('R3', 'PARTNER PAC')
	->setCellValue('S3', 'RF PAC')
	->setCellValue('T3', 'PAC DATE')
	->setCellValue('U3', 'ACTION BY')
	->setCellValue('V3', 'BAND')
	->setCellValue('W3', 'RFINFO')
	->setCellValue('X3', 'SAC')
	->setCellValue('Y3', 'CON')
	->setCellValue('Z3', 'STATUS')
	->setCellValue('AA3', 'PO for Acquisition')
	->setCellValue('AB3', 'Physical start')
	->setCellValue('AC3', 'Site Integrated')
	->setCellValue('AD3', 'Vendor GSM900')
	->setCellValue('AE3', 'Vendor GSM1800')
	->setCellValue('AF3', 'Vendor 3G')
	->setCellValue('AG3', 'BAND GSM900')
	->setCellValue('AH3', 'BAND GSM1800')
	->setCellValue('AI3', 'BAND UMTS')
	->setCellValue('AJ3', 'BAND UMTS900')
	->setCellValue('AK3', 'COMMERCIAL PHASE');;

$styleArray = array(
	'font' => array(
		'bold' => true,
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
	),
	'borders' => array(
		'bottom' => array(
			'style' => PHPExcel_Style_Border::BORDER_THICK,
		),
	),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
		'rotation' => 90,
		'startcolor' => array(
			'argb' => 'FFA0A0A0',
		),
		'endcolor' => array(
			'argb' => 'FFFFFFFF',
		),
	),
	);

$objPHPExcel->getActiveSheet()->getStyle('A3:AK3')->applyFromArray($styleArray);



$query=create_query($_POST['site_key'],$_POST['region'],$_POST['type'],$_POST['actionby'],$_POST['orderby'],$_POST['order'],$_POST['start'],$_POST['end'],$_POST['phase'],$_POST['commercial'],$_POST['allocated']);
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

$styleArray_orange = array(
'alignment' => array(
	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
),
'fill' => array(
	'type' => PHPExcel_Style_Fill::FILL_SOLID,
	'color' => array(
		'argb' => 'FFFFFF00',
	)
),
);

$styleArray_green = array(
'alignment' => array(
	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
),
'fill' => array(
	'type' => PHPExcel_Style_Fill::FILL_SOLID,
	'color' => array(
		'argb' => 'FF00FF00',
	)
),
);

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


		if ($val1=="selected_RAF"){
			$letter="E";
		}else if ($val2=="selected_RAF"){
			$letter="F";
		}else if ($val3=="selected_RAF"){
			$letter="G";
		}else if ($val4=="selected_RAF"){
			$letter="H";
		}else if ($val5=="selected_RAF"){
			$letter="I";
		}else if ($val6=="selected_RAF"){
			$letter="J";
		}else if ($val7=="selected_RAF"){
			$letter="K";
		}else if ($val14=="selected_RAF"){
			$letter="L";
		}else if ($val15=="selected_RAF"){
			$letter="M";
		}else if ($val8=="selected_RAF"){
			$letter="N";
		}else if ($val9=="selected_RAF"){
			$letter="O";
		}else if ($val10=="selected_RAF"){
			$letter="Q";
		}else if ($val11=="selected_RAF"){
			$letter="R";
		}else if ($val12=="selected_RAF"){
			$letter="S";
		}else if ($val13=="selected_RAF"){
			$letter="T";
		}

		if ($status=="RAF ASBUILD"){
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':AC'.$row)->applyFromArray($styleArray_green);
		}else{
			$objPHPExcel->getActiveSheet()->getStyle($letter.$row)->applyFromArray($styleArray_orange);
		}



		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue("A".$row, $res1['RAFID'][$i])
					->setCellValue("B".$row, substr($res1['SITEID'][$i],0,2))
					->setCellValue("C".$row, substr($res1['SITEID'][$i],2))
					->setCellValue("D".$row, $res1['TYPE'][$i]." ".$res1['CANDIDATE'][$i])
					->setCellValue("E".$row, $res1['OTHER_INP'][$i])
					->setCellValue("F".$row, $res1['RADIO_INP'][$i])
					->setCellValue("G".$row, $res1['TXMN_INP'][$i])
					->setCellValue("H".$row, $res1['NET1_LINK'][$i])
					->setCellValue("I".$row, $res1['ALU_INP'][$i])
					->setCellValue("J".$row, $res1['BCS_NET1'][$i])
					->setCellValue("K".$row, $res1['NET1_LBP'][$i])
					->setCellValue("L".$row, $res1['ALU_ACQUIRED'][$i])
					->setCellValue("M".$row, $res1['TXMN_ACQUIRED'][$i])
					->setCellValue("N".$row, $res1['NET1_AQUIRED'][$i])
					->setCellValue("O".$row, $res1['STATUS_FUND'][$i])
					->setCellValue("P".$row, $res1['UA503'][$i])
					->setCellValue("Q".$row, $res1['NET1_FUND'][$i])
					->setCellValue("R".$row, $res1['PAC_STATUS'][$i])
					->setCellValue("S".$row, $res1['RF_PAC'][$i])
					->setCellValue("T".$row, $res1['NET1_PAC'][$i])
					->setCellValue("U".$row, $status." ".$status_special)
					->setCellValue("V".$row, $band)
					->setCellValue("W".$row, $res1['RFINFO'][$i])
					->setCellValue("X".$row, $res1['SAC'][$i])
					->setCellValue("Y".$row, $res1['CON'][$i])
					->setCellValue("Z".$row, $res1['STATUS'][$i])
					->setCellValue("AA".$row, $res1['UA501'][$i])
					->setCellValue("AB".$row, $res1['U459A59'][$i])
					->setCellValue("AC".$row, $res1['U571A71'][$i])
					->setCellValue("AD".$row, $vendor_2G_GSM900)
					->setCellValue("AE".$row, $vendor_2G_GSM1800)
					->setCellValue("AF".$row, $vendor_3G)
					->setCellValue("AG".$row, $res2['BAND_900'][0])
					->setCellValue("AH".$row, $res2['BAND_1800'][0])
					->setCellValue("AI".$row, $res2['BAND_UMTS'][0])
					->setCellValue("AJ".$row, $res2['BAND_UMTS900'][0])
					->setCellValue("AK".$row, $res2['COMMERICAL'][0]);


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

$objPHPExcel->getActiveSheet()->getStyle('U4:U'.$row)->applyFromArray($styleArray_orange);
//$objPHPExcel->getActiveSheet()->getStyle('T4:T1500')->applyFromArray($style_BorderVerticallines);

$objPHPExcel->getActiveSheet()->setAutoFilter('A3:AK3');


$style_BorderVerticallines = array(
			'borders' => array(
				'vertical' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => '00000000'),
				),
			),
		);
$objPHPExcel->getActiveSheet()->getStyle('A3:AK'.$row)->applyFromArray($style_BorderVerticallines);

$objPHPExcel->getActiveSheet()->setTitle('RAF report');
$objPHPExcel->setActiveSheetIndex(0);


if ($filtetype=="html"){
	echo __FILE__;

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
	$objWriter->setSheetIndex(0);
	$objWriter->setImagesRoot('/var/www/html/infobase/files/infobase/images/');
	$objWriter->save('/var/www/html/bash_scripts/Temp/raf_report_'.$_POST['actionby'].'.html');
}else{
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="raf_report_'.$_POST['actionby'].'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;
}
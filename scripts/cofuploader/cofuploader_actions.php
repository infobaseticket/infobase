<?PHP
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/PHPExcel/Classes/PHPExcel/IOFactory.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['action']=="importCofs"){
	
	$error=0;
	//  Read your Excel workbook
	try {
		$inputFileName=$_POST["folder"].$_POST["newfilename"];
	    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
	    $objPHPExcel = $objReader->load($inputFileName);
	} catch(Exception $e) {
	    $out['msg']="Error loading file ".pathinfo($inputFileName,PATHINFO_BASENAME).": ".$e->getMessage();
	    $out['msgtype']="error";
	    $error=1;
	}
	if ($error!=1){

		$query="DELETE FROM BSDS_RAF_COF_TMP";
		//echo $query;
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	    if (!$stmt) {
	      die_silently($conn_Infobase, $error_str);
	    }else{
	      OCICommit($conn_Infobase);
	    }

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();

		$error=0;
		//  Loop through each row of the worksheet in turn
		$outResult.="<div class='row'><div class='col-md-12'>";
		$outResult.="<table class='table'><tr><th>ROWNUM</th><th>RAFID</th><th>SITEID</th><th>MATERIAL_CODE</th><th>ACQCON</th><th>PRICE</th><th>RTN</th></tr>";
		$i=0;
		$hasError="no";


		for ($row = 1; $row <= $highestRow; $row++){ 
		  //  Read a row of data into an array
			$rowData = $sheet->rangeToArray('A' . $row . ':' .'E' . $row,
		                                  NULL,
		                                  TRUE,
		                                  FALSE);

			if ($row==1){
				if ($rowData[0][0]!='RAFID' or $rowData[0][1]!='MATERIAL_CODE' or $rowData[0][2]!='ACQCON' or $rowData[0][3]!='PRICE' or $rowData[0][4]!='RTN'){
					$outResult.="<tr class='danger'><td colspan='6'>HEADERS NOT CORRECT, please use provided template</td></tr>";
					$hasError="yes";
					break;
				}else{
					continue;	
				} 
						    
		 	}else{

		 		if (is_numeric($rowData[0][0])){

		 			$query="SELECT SITEID FROM BSDS_RAFV2 WHERE RAFID='".$rowData[0][0]."'";
					//echo $query;
					$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
					if (!$stmt){
					    die_silently($conn_Infobase, $error_str);
					    exit;
					} else {
					    OCIFreeStatement($stmt);
						$SITEID=$res1['SITEID'][0];	
					}

		 			if ($SITEID!=''){

					    $query= "INSERT INTO BSDS_RAF_COF_TMP
					    VALUES ('".$rowData[0][0]."','".$rowData[0][1]."',SYSDATE,'".$guard_username."','".$rowData[0][2]."','".$rowData[0][3]."','".$rowData[0][4]."')";
					    //echo $query."<br>";
					    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
					    if (!$stmt) {
					      die_silently($conn_Infobase, $error_str);
					    }else{
					      OCICommit($conn_Infobase);
					    }
					    $outResult.="<tr class='success'><td>".$row."</td><td>".$rowData[0][0]."</td><td>".$SITEID."</td><td>".$rowData[0][1]."</td><td>".$rowData[0][2]."</td><td>".$rowData[0][3]."</td><td>".$rowData[0][4]."</td></tr>";
					    $i++;

					}else{
						$outResult.="<tr class='danger'><td>".$row."</td><td>".$rowData[0][0]."</td><td colspan='5'>RAF not found</td></tr>";
						$hasError="no";
					}
			    }
			    //echo "<pre>".print_r($rowData,true)."</pre>";
			}
		}
		$outResult.="</table></div></div>";

		$query="DELETE FROM BSDS_RAF_COF_TMP WHERE RAFID IS NULL";
		//echo $query;
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	    if (!$stmt) {
	      die_silently($conn_Infobase, $error_str);
	    }else{
	      OCICommit($conn_Infobase);
	    }

		if ($hasError!='yes'){
			$outResult.=" <button class='btn btn-success ConfirmCOF' title='Upload COF into the RAF'>Upload COF to the RAF</button>";
		}

		$out['output']=$outResult; 

	}
}elseif ($_POST['action']=="confirmCof"){
  

    $query="SELECT DISTINCT(RAFID), ACQCON, SN FROM BSDS_RAF_COF_TMP GROUP BY RAFID,ACQCON, SN";
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
	    die_silently($conn_Infobase, $error_str);
	    exit;
	} else {
	    OCIFreeStatement($stmt);

	    $amount_of_RAFS=count($res1['RAFID']);
	}

   	for ($i = 0; $i <$amount_of_RAFS; $i++){ 

   		$query="DELETE FROM  BSDS_RAF_COF WHERE RAFID='".$res1['RAFID'][$i]."' AND ACQCON='".$res1['ACQCON'][$i]."'";
   		$stmt2 = parse_exec_free($conn_Infobase, $query, $error_str);
	    if (!$stmt2) {
	      die_silently($conn_Infobase, $error_str);
	    }else{
	      OCICommit($conn_Infobase);
	    }

	    if ($res1['ACQCON'][$i]=='CON'){
	    	$query="UPDATE BSDS_RAFV2 SET BUDGET_CON='".$res1['SN'][$i]."' WHERE RAFID='".$res1['RAFID'][$i]."'";
	    }else{
	    	$query="UPDATE BSDS_RAFV2 SET BUDGET_ACQ='".$res1['SN'][$i]."' WHERE RAFID='".$res1['RAFID'][$i]."'";
	    }
   		//echo $res1['RAFID'][$i];
   		
   		$stmt2 = parse_exec_free($conn_Infobase, $query, $error_str);
	    if (!$stmt2) {
	      die_silently($conn_Infobase, $error_str);
	    }else{
	      OCICommit($conn_Infobase);
	    }

   	}


	$query= "INSERT INTO BSDS_RAF_COF SELECT RAFID, MATERIAL_CODE, INSERT_DATE, INSERT_BY, ACQCON, SPRICE, '0' FROM BSDS_RAF_COF_TMP";
    //echo $query."<br>";
    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
    if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
    }else{
      OCICommit($conn_Infobase);
    }
 

    $outResult.="RAF COF info has been added and RTN info updated";

    $out['output']=$outResult;

}

echo json_encode($out);
<?PHP
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/PHPExcel/Classes/PHPExcel/IOFactory.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['action']=="importComments"){
	
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

		$query="DELETE FROM RAF_COMMENTS_TMP";
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
		$outResult.="<table class='table'><tr><th>ROWNUM</th><th>RAFID</th><th>SITEID</th><th>COMMENTS</th></tr>";
		$i=0;
		$hasError="no";

		for ($row = 1; $row <= $highestRow; $row++){ 
		  //  Read a row of data into an array
		  	$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
		                                  NULL,
		                                  TRUE,
		                                  FALSE);
		  
			if ($row==1){
				//echo "---".$highestColumn;
				$col=0;
				foreach ( range('A', $highestColumn) as $column_key) {
					//echo $rowData[0][$col]."<br>";
					if (trim(strtoupper($rowData[0][$col]))=="RAFID" or trim(strtoupper($rowData[0][$col]))=="IB_RAFID"){
						$RAFIDcol=$col;
						//echo $col."=".$RAFIDcol;
					}else if (trim(strtoupper($rowData[0][$col]))=="NEW_COMMENT" or trim(strtoupper($rowData[0][$col]))=="NEW_COMMENTS"){
						$COMMENTcol=$col;
						//echo $col."=".$COMMENTcol;
					}
			
				 	$col++;
				}
 
				//die;
				continue;			    
		 	}else{

		 		if (!isset($RAFIDcol) or !isset($COMMENTcol)){
		 			$outResult.="<tr class='danger'><td>".$row."</td><td>".$rowData[0][$RAFIDcol]."</td><td>HEADERS NOT CORRECT, should be RAFID and NEW_COMMENT</td><td>".$rowData[0][$COMMENTcol]."</td></tr>";
					$hasError="yes";
					break;
				}else if($rowData[0][$COMMENTcol]==''){
					$outResult.="<tr class='danger'><td>".$row."</td><td>".$rowData[0][$RAFIDcol]."</td><td>No COMMENTS</td><td>".$rowData[0][$COMMENTcol]."</td></tr>";
					$hasError="no";
		 		}else if (is_numeric($rowData[0][0])){

		 			$query="SELECT SITEID FROM BSDS_RAFV2 WHERE RAFID='".$rowData[0][$RAFIDcol]."'";
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

		 				$query="SELECT ID FROM RAF_COMMENTS WHERE RAFCOMMENT='".escape_sq($rowData[0][$COMMENTcol])."' AND RAFID=".$rowData[0][$RAFIDcol];
						//echo $query;
						$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
						if (!$stmt){
						    die_silently($conn_Infobase, $error_str);
						    exit;
						} else {
						    OCIFreeStatement($stmt);
							$ID=$res1['ID'][0];	
						}
						if ($ID==''){
						    $query= "INSERT INTO RAF_COMMENTS_TMP
						    VALUES ('".$guard_username."',SYSDATE,".$rowData[0][$RAFIDcol].", '".escape_sq($rowData[0][$COMMENTcol])."',0,'','".$SITEID."')";
						    //echo $query."<br>";
						    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
						    if (!$stmt) {
						      die_silently($conn_Infobase, $error_str);
						    }else{
						      OCICommit($conn_Infobase);
						    }
						    $outResult.="<tr class='success'><td>".$row."</td><td>".$rowData[0][$RAFIDcol]."</td><td>".$SITEID."</td><td>".$rowData[0][$COMMENTcol]."</td></tr>";
						    $data[$i]['RAFID']=$rowData[0][$RAFIDcol];
						    $data[$i]['COMMENTS']=$rowData[0][$COMMENTcol];
						    $i++;
						}else{
							$outResult.="<tr class='danger'><td>".$row."</td><td>".$rowData[0][$RAFIDcol]."</td><td>RAF comment duplication</td><td>".$rowData[0][$COMMENTcol]."</td></tr>";
							$hasError="yes";
						}
					}else{
						$outResult.="<tr class='danger'><td>".$row."</td><td>".$rowData[0][$RAFIDcol]."</td><td>RAF not found</td><td>".$rowData[0][$COMMENTcol]."</td></tr>";
						$hasError="no";
					}
			    }
			    //echo "<pre>".print_r($rowData,true)."</pre>";
			}
		}
		$outResult.="</table></div></div>";
		if ($hasError!='yes'){
			$outResult.=" <button class='btn btn-success ConfirmRAFcomments' title='Upload comments into the RAF'>Attach comments to the RAF</button>";
		}
		if (strlen(serialize($data))<2000){
			$query= "INSERT INTO RAF_COMMENTS_LOG  VALUES (SYSDATE,'".$guard_username."','".$i."', '".serialize($data)."','".$_POST['newfilename']."','".$hasError."')";
		    //echo $query."<br>";
		    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		    if (!$stmt) {
		      die_silently($conn_Infobase, $error_str);
		    }else{
		      OCICommit($conn_Infobase);
		    }
		}

		$out['output']=$outResult; 

	}
}elseif ($_POST['action']=="confirmComments"){
	 $query= "INSERT INTO RAF_COMMENTS SELECT INSERT_BY, INSERT_DATE, RAFID, RAFCOMMENT, HISTORY, HISTORY_BY, SITEID,'' FROM RAF_COMMENTS_TMP";
    //echo $query."<br>";
    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
    if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
    }else{
      OCICommit($conn_Infobase);
    }
    $outResult.="RAF comments have been added";

    $out['output']=$outResult;

}

echo json_encode($out);
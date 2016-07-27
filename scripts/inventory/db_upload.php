<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


require_once($config['sitepath_abs']."/bsds/PHPlibs/PHPExcel/Classes/PHPExcel/IOFactory.php");

if ($_POST['filetype']=='inventory'){
  $inputFileName = '/var/www/html/Uploads/INVENTORY/Inventory_today/inventory.xls';
}else if ($_POST['filetype']=='movement'){
  $inputFileName = '/var/www/html/Uploads/INVENTORY/Movement_today/movement.xls';
}

$error=0;
//  Read your Excel workbook
try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName);
} catch(Exception $e) {
    $out['msg']="Error loading file ".pathinfo($inputFileName,PATHINFO_BASENAME).": ".$e->getMessage();
    $out['msgtype']="error";
    $error=1;
}
if ($_POST['filetype']=='inventory' && $error!=1){

  $query= "DELETE FROM INVENTORY_TODAY";
  $stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
  if (!$stmt4) {
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
  for ($row = 1; $row <= $highestRow; $row++){ 
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                      NULL,
                                      TRUE,
                                      FALSE);
      if ($row==1){
        if ($rowData[0][0]!="WAREHOUSE" or $rowData[0][1]!="MOVEMENT ID" or $rowData[0][3]!="PRODUCT DESCRIPTION" or $rowData[0][4]!="KPNGBE PRODUCT REFERENCE"){
          $out['msgtype']="error";
          $out['msg']="File has not the correct format. Headers should be avilable!";
          $error=1;
          break;
        }
      }else{
        $query= "INSERT INTO INVENTORY_TODAY VALUES (SYSDATE,'".strtoupper($rowData[0][0])."', '".strtoupper($rowData[0][1])."', '".strtoupper($rowData[0][2])."', '".strtoupper($rowData[0][3])."', '".strtoupper($rowData[0][4])."', '".strtoupper($rowData[0][5])."','".strtoupper($rowData[0][6])."', '".strtoupper($rowData[0][7])."', '".strtoupper($rowData[0][8])."', '".strtoupper($rowData[0][9])."', '".strtoupper($rowData[0][10])."', '".strtoupper($rowData[0][11])."', '".strtoupper($rowData[0][12])."', '".strtoupper($rowData[0][13])."', '".strtoupper($rowData[0][14])."')";
        //echo $query."<br>";
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
          die_silently($conn_Infobase, $error_str);
        }else{
          OCICommit($conn_Infobase);
        }
      }
      //echo "<pre>".print_r($rowData,true)."</pre>";
  }

  
  $query= "UPDATE INVENTORY_TODAY SET LOT_SERIAL_NUMBER=REPLACE(LOT_SERIAL_NUMBER,'REF','')";
  $stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
  if (!$stmt4) {
    die_silently($conn_Infobase, $error_str);
  }else{
    OCICommit($conn_Infobase);
  }

  $query= "DELETE FROM INVENTORY_TODAY WHERE KPNGBE_PRODUCTREFERENCE IS NULL";
  $stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
  if (!$stmt4) {
    die_silently($conn_Infobase, $error_str);
  }else{
    OCICommit($conn_Infobase);
  }

  if ($error!=1){
    $out['msgtype']="info";
    $out['msg']=$row." records have been imported into the database table INVENTORY_TODAY";
  }
  
  
}else if ($_POST['filetype']=='movement' && $error!=1){

  $query= "DELETE FROM MOVEMENT_TODAY";
  $stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
  if (!$stmt4) {
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
  for ($row = 1; $row <= $highestRow; $row++){ 
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
      if ($row==1){
        if (strtoupper($rowData[0][3])!="MOVEMENT ID" or strtoupper($rowData[0][6])!="PRODUCT DESCRIPTION" or strtoupper($rowData[0][7])!="BASE ITEM" or strtoupper($rowData[0][8])!="QTY IN" or strtoupper($rowData[0][9])!="QTY OUT"){
          $out['msgtype']="error";
          $out['msg']="File has not the correct format. Headers should be avilable!";
          $error=1;
          break;
        }
      }else{
       $query= "INSERT INTO MOVEMENT_TODAY VALUES (SYSDATE,'".strtoupper($rowData[0][0])."', '".strtoupper($rowData[0][1])."', '".strtoupper($rowData[0][2])."', '".strtoupper($rowData[0][3])."', '".strtoupper($rowData[0][4])."', '".strtoupper($rowData[0][5])."','".strtoupper($rowData[0][6])."', '".strtoupper($rowData[0][7])."', '".strtoupper($rowData[0][8])."', '".strtoupper($rowData[0][9])."', '".strtoupper($rowData[0][10])."', '".strtoupper($rowData[0][11])."', '".strtoupper($rowData[0][12])."', '".strtoupper($rowData[0][13])."', '".strtoupper($rowData[0][14])."', '".strtoupper($rowData[0][15])."', '".strtoupper($rowData[0][16])."')";
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
          die_silently($conn_Infobase, $error_str);
        }else{
          OCICommit($conn_Infobase);
        }
      }
     
      //echo "<pre>".print_r($rowData,true)."</pre>";
  }

  $query= "UPDATE MOVEMENT_TODAY SET LOST_SERIAL_NO=REPLACE(LOST_SERIAL_NO,'REF','')";
  $stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
  if (!$stmt4) {
    die_silently($conn_Infobase, $error_str);
  }else{
    OCICommit($conn_Infobase);
  }

  $query= "DELETE FROM MOVEMENT_TODAY WHERE BASE_ITEM IS NULL";
  $stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
  if (!$stmt4) {
    die_silently($conn_Infobase, $error_str);
  }else{
    OCICommit($conn_Infobase);
  }

  if ($error!=1){
    $out['msgtype']="info";
    $out['msg']=$row." records have been imported into the database table INVENTORY_MOVEMENT";
  } 
}

echo json_encode($out);

?>

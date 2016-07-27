<?PHP
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/PHPExcel/Classes/PHPExcel/IOFactory.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

function reloadTable($SN_ID){
	global $conn_Infobase;
	$query="SELECT * FROM SN_SHIPPINGLIST WHERE SN_ID='".$SN_ID."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
	    die_silently($conn_Infobase, $error_str);
	    exit;
	} else {
	    OCIFreeStatement($stmt);
		$PARTNER=$res1['PARTNER'][0];	
		$PARTNER_BY=$res1['PARTNER_BY'][0];
		$PARTNER_DATE=$res1['PARTNER_DATE'][0];  
		$PARTNERLOG=$res1['PARTNERLOG'][0];	
		$PARTNERLOG_BY=$res1['PARTNERLOG_BY'][0];
		$PARTNERLOG_DATE=$res1['PARTNERLOG_DATE'][0]; 
		$RAFID=$res1['RAFID'][0]; 
		$REJECT_BY=$res1['REJECT_BY'][0];
		$REJECT_DATE=$res1['REJECT_DATE'][0];
		$REJECT_REASON=$res1['REJECT_REASON'][0];
	}

	$query="SELECT SH.KPNGB_PROD_REF AS KPNGB_PROD_REF,DESCRIPTION, SH.OLD_REF AS OLD_REF, 
	SH.INSERT_BY as INSERT_BY,SH.INSERTDATE as INSERTDATE, SUM(SH.AMOUNT) AS TOTAL,  SM.KPNGB_PROD_REF as CHECK_REF, DELETED, DELETED_BY, DELETED_DATE
	FROM SN_SHIPPINGS SH LEFT JOIN SN_MATERIAL_LIST SM ON SH.KPNGB_PROD_REF=SM.KPNGB_PROD_REF 
	WHERE SN_ID='".$SN_ID."' 
	GROUP BY SH.KPNGB_PROD_REF,DESCRIPTION, SH.OLD_REF, SH.INSERT_BY,SH.INSERTDATE, SM.KPNGB_PROD_REF, DELETED, DELETED_BY, DELETED_DATE
	ORDER BY SM.KPNGB_PROD_REF";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
	    die_silently($conn_Infobase, $error_str);
	    exit;
	} else {
	    OCIFreeStatement($stmt);
	    $amount_of_SN=count($res1['KPNGB_PROD_REF']);
	}


	if ($amount_of_SN>=1){
		$outTable="<table class='table table-condensed'><thead><tr>";
		$outTable.="<th>KPNGB PROD REF</th>";
		$outTable.="<th>OLD REF</th>";
		$outTable.="<th>DESCRIPTION</th>";
		$outTable.="<th>AMOUNT</th>";
		$outTable.="<th>CHECK</th>";
		$outTable.="<th>BY</th>";

		$outTable.="</tr>";
	 	for ($i = 0; $i <$amount_of_SN; $i++) { 
	 		
	 		if ($res1['DELETED'][$i]==1){
	 			$deleted=" style='text-decoration: line-through;'";
	 			$class="danger";
	 			$by=$res1['DELETED_BY'][$i];
	 		}else if ($res1['INSERT_BY'][$i]!=$PARTNER_BY){
	 			$class="warning";
	 			$deleted="";
	 			$by=$res1['INSERT_BY'][$i];
	 		}else{
	 			$deleted="";
	 			$class="";
	 			$by=$res1['INSERT_BY'][$i];
	 		}
			$outTable.="<tr class='".$class."'><td".$deleted.">".$res1['KPNGB_PROD_REF'][$i]."</td>";
			$outTable.="<td".$deleted.">".$res1['OLD_REF'][$i]."</td>";
			$outTable.="<td".$deleted.">".$res1['DESCRIPTION'][$i]."</td>";
			$outTable.="<td>".$res1['TOTAL'][$i]."</td>";
			if($res1['CHECK_REF'][$i]!=''){
				$outTable.="<td class='success'>OK</td>";
			}else{
				$outTable.="<td class='danger'>UKNOWN PRODUCT</td>";
				$hasError='yes';
			}
			$outTable.="<td>".$by." @ ".$res1['INSERTDATE'][$i]."</td>";
			$outTable.="<td><div class='btn-toolbar' role='toolbar'>";
		    $outTable.="<div class='btn-group'>";
		    if ($res1['DELETED'][$i]!=1 && ($PARTNERLOG!='OK' OR $PARTNER!='OK')){
		    $outTable.="<button class='btn btn-default btn-xs deleteSN' title='Delete SN line' data-sn_id='".$SN_ID."' data-prodref='".$res1['KPNGB_PROD_REF'][$i]."'>";
		    $outTable.="<span class='glyphicon glyphicon-trash'></span></button>";
			}
		    $outTable.="</div>";
		    $outTable.="</div></td></tr>";
		}
		$outTable.="</table>";

		
			$outTable.="
			<form action='scripts/shipping_notification/sn_actions.php' id='SNForm".$SN_ID."' method='POST'>
			<input type='hidden' name='SN_ID' value='".$SN_ID."'>
			<input type='hidden' name='RAFID' value='".$RAFID."'>
			<div class='form-group'>";
				if ($hasError!='yes' && $PARTNER!='OK'){

				    $outTable.="<label for='dpYears' class='col-sm-2 control-label'>SHIPPING DATE</label>
				    <div class='col-sm-3'>
				    	<input name='shipdate' id='shipdate' class='form-control' data-provide='datepicker' data-date-format='dd-mm-yyyy' placeholder='SELECT SHIPPING DATE'>
				    </div>
				    <div class='col-sm-3'>";
			   	}
			    if ($hasError!='yes' && $PARTNER!='OK'){
			    	$outTable.="<input type='submit' value='Mark SN finished' class='btn btn-success partnerConfirmSN' data-action='partnerOK' data-sn_id='".$SN_ID."'>";
			    }
			    if ($hasError!='yes' && $PARTNER=='OK' && $PARTNERLOG=='NOT OK'){
			    	$outTable.="<input type='submit' value='Accept SN' class='btn btn-success partnerConfirmSN' data-sn_id='".$SN_ID."' data-rafid='".$RAFID."' data-action='logisticsOK'>&nbsp;";
			    	$outTable.="<input type='submit' value='Reject' class='btn btn-danger partnerConfirmSN' data-sn_id='".$SN_ID."' data-rafid='".$RAFID."'  data-action='logisticsREJECT'>";
			    }
			    $outTable.="
			    </div>
			</div>
			</form><br><br><div class='well'>";

		

		if ($PARTNER=='OK'){
			$outTable.="SN created by ".$PARTNER_BY." on ".$PARTNER_DATE."<br>";
		}
		if ($PARTNER=='REJECTED'){
			$outTable.="<b>SN rejected by ".$REJECT_BY." on ".$REJECT_DATE." because:</b><br>".$REJECT_REASON."<br>";
		}
		if ($PARTNERLOG=='OK' && $PARTNER=='OK'){
			$outTable.="SN ".$PARTNERLOG." by ".$PARTNERLOG_BY." on ".$PARTNERLOG_DATE."<br>";
		}
	}
	$outTable.="</div>";
	return $outTable;
}


if ($_POST['action']=="addSN"){
	if ($_POST['template']==""){
		$query = "select * from SN_MATERIAL_LIST WHERE upper(KPNGB_PROD_REF) = '".escape_sq($_POST["KPNGB_PROD_REF"])."'";
		//echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
		  die_silently($conn_Infobase, $error_str);
		  exit;
		} else {
		  OCIFreeStatement($stmt);
		}
		if (count($res1['KPNGB_PROD_REF'])==1){
			if ($_POST["amount"]=='' or $_POST["amount"]=='0'){
				$out['msg']="You have to put an amount";
				$out['msgtype']="error";
			}else{

				$query= "INSERT INTO SN_SHIPPINGS
					    VALUES (SYSDATE,'".$guard_username."','".$guard_groups."','".$res1['KPNGB_PROD_REF'][0]."', 
					    	'".$_POST["amount"]."','".$res1['OLD_REFERENCE'][0]."','".$_POST['SN_ID']."',0,'','')";
			    //echo $query."<br>";
			    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			    if (!$stmt) {
			      die_silently($conn_Infobase, $error_str);
			    }else{
			      OCICommit($conn_Infobase);
			    }

				$out['msg']="Product has been added to SN";
				$out['msgtype']="info";
			}

		}else{
			$out['msg']="You have to select a product from the list";
			$out['msgtype']="error";
		}
	}else{
		$query= "INSERT INTO SN_SHIPPINGS 
				    SELECT SYSDATE,'".$guard_username."','".$guard_groups."',MATERIAL, 
				    	AMOUNT,'','".$_POST['SN_ID']."',0,'','' FROM SN_TEMPLATES WHERE TEMPLATE='".$_POST['template']."'";
		//echo $query;
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	    if (!$stmt) {
	      die_silently($conn_Infobase, $error_str);
	    }else{
	      OCICommit($conn_Infobase);
	    }
	    $out['msg']="Products from template have been added to SN";
		$out['msgtype']="info";

	}
}elseif ($_POST['action']=="importSN"){


	$queryIN="UPDATE SN_SHIPPINGLIST SET PARTNER='NOT OK', PARTNER_DATE='', PARTNER_BY='', PARTNERLOG='NOT OK', PARTNERLOG_DATE='".escape_sq($_POST['shipdate'])."', PARTNERLOG_BY='' WHERE SN_ID='".$_POST['SN_ID']."'";
	//echo $queryIN;
	$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
	if (!$stmtIN) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}

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

		if ($_POST["SN_ID"]!=""){
			$query="DELETE FROM SN_SHIPPINGS WHERE SN_ID='".$_POST["SN_ID"]."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		    if (!$stmt) {
		      die_silently($conn_Infobase, $error_str);
		    }else{
		      OCICommit($conn_Infobase);
		    }
		}

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(1); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		$error=0;
		//  Loop through each row of the worksheet in turn
		for ($row = 14; $row <= $highestRow; $row++){ 
		  //  Read a row of data into an array
		  $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
		                                  NULL,
		                                  TRUE,
		                                  FALSE);
			if ($row==14){

			    if ($rowData[0][0]!="PRODUCT REFERENCE" or $rowData[0][2]!="TYPE"){
			      $out['msgtype']="error";
			      $out['msg']="File has not the correct format. Headers should be available!";
			      $error=1;
			      break;
			    }
		 	}else{
		 		if (is_numeric($rowData[0][4]) && $rowData[0][4]>0){
		 			$amount=$rowData[0][4];

				  	$bobdate=str_replace(' ', '_',str_replace('/', '',str_replace(':', '', $_POST['bsdsbobrefresh'])));
				    $query= "INSERT INTO SN_SHIPPINGS (INSERTDATE,INSERT_BY,INSERT_GROUP,KPNGB_PROD_REF,AMOUNT,OLD_REF,SN_ID) 
				    VALUES (SYSDATE,'".$guard_username."','".$guard_groups."','".strtoupper($rowData[0][0])."', 
				    	'".$amount."','".strtoupper($rowData[0][1])."','".$_POST['SN_ID']."')";
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
		}

		return $outResult;
	}
}elseif ($_POST['action']=="reloadTable"){
	$outResult=reloadTable($_POST["SN_ID"]);
	$out['msgtype']="info";
	$out['output']=$outResult;

}elseif ($_POST['action']=="deleteSNline"){

	$out['msgtype']="info";

	$query= "UPDATE SN_SHIPPINGS SET DELETED='1', DELETED_BY='".$guard_username."', DELETED_DATE=SYSDATE WHERE SN_ID='".$_POST['SN_ID']."' AND KPNGB_PROD_REF='".$_POST['prodref']."'";
    echo $query."<br>";
    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
    if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
    }else{
      OCICommit($conn_Infobase);
    }
    
}elseif ($_POST['action']=="partnerOK"){

	$date = DateTime::createFromFormat('d-m-Y',escape_sq($_POST['shipdate']));
	$date->modify('+4 weeks');
	$cabEtsim= strtoupper($date->format('d-M-Y'));
	

    $queryIN="UPDATE SN_SHIPPINGLIST SET PARTNER='OK', PARTNER_DATE=SYSDATE, PARTNER_BY='".$guard_username."', REQUESTED_SHIPPING_DATE='".escape_sq($_POST['shipdate'])."', CAB_ON_SITE_ESTIM='".$cabEtsim."' WHERE SN_ID='".$_POST['SN_ID']."'";
	//echo $queryIN;
	$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
	if (!$stmtIN) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
	

	$query = "SELECT N1_SITEID, N1_NBUP, N1_CANDIDATE, N1_UPGNR FROM MASTER_REPORT WHERE IB_RAFID='".$_POST["RAFID"]."'";
	//echo $query.EOL;
	$stmt1 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt1) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt1);
	}
	if ($res1['N1_NBUP'][0]=='UPG'){
		$SITEID=$res1['N1_SITEID'][0];
		$UPGNR=$res1['N1_UPGNR'][0];
		$MS='U363';
	}else if ($res1['N1_NBUP'][0]=='NB'){
		$SITEID=$res1['N1_SITEID'][0];
		$UPGNR=$res1['N1_CANDIDATE'][0];
		$MS='A363';
	}

	//we toggle Cabinat installed on site estimate:
	if ($res1['N1_NBUP'][0]!=""){
		$queryUP = "INSERT INTO INFOBASE.NET1UPDATER_CSV VALUES ('".$SITEID."','".$UPGNR."','".$MS."','".date('d-m-Y')."','SN',SYSDATE,'1','','SN','".$res1['N1_CANDIDATE'][0]."')";
		//echo $queryUP;
		$stmtUP = parse_exec_free($conn_Infobase, $queryUP, $error_str);
		if (!$stmtUP) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}
	}
	$queryIN="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['RAFID']."',SYSDATE,'OK','".$guard_username."','SN ".$_POST['SN_ID']." CREATED')";
	//echo $queryIN;
	$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
	if (!$stmtIN) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}

	$out['msgtype']="info";
	$out['output']=reloadTable($_POST["SN_ID"]);

}elseif ($_POST['action']=="logisticsOK"){

    $queryIN="UPDATE SN_SHIPPINGLIST SET PARTNERLOG='OK', PARTNERLOG_DATE=SYSDATE, PARTNERLOG_BY='".$guard_username."' WHERE SN_ID='".$_POST['SN_ID']."'";
	//echo $queryIN;
	$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
	if (!$stmtIN) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}

	$queryIN="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['RAFID']."',SYSDATE,'OK','".$guard_username."','SN ".$_POST['SN_ID']." LOGISTICS')";
	//echo $queryIN;
	$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
	if (!$stmtIN) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
	
	$out['msgtype']="info";
	$out['output']=reloadTable($_POST["SN_ID"]);

}elseif ($_POST['action']=="logisticsREJECT"){

    $queryIN="UPDATE SN_SHIPPINGLIST SET PARTNER='REJECTED',PARTNERLOG='NOT OK',PARTNERLOG_BY='',PARTNER_DATE='', PARTNERLOG_DATE='',REJECT_REASON='".escape_sq($_POST['SNRejectReason'])."', REJECT_BY='".$guard_username."', REJECT_DATE=SYSDATE WHERE SN_ID='".$_POST['SN_ID']."'";
	//echo $queryIN;
	$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
	if (!$stmtIN) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}

	$queryIN="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['RAFID']."',SYSDATE,'REJECT: ".escape_sq($_POST['SNRejectReason'])."','".$guard_username."','SN ".$_POST['SN_ID']." LOGISTICS')";
	//echo $queryIN;
	$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
	if (!$stmtIN) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
	
	$out['msgtype']="info";
	$out['output']=reloadTable($_POST["SN_ID"]);
}

echo json_encode($out);
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['filetype']=='inventory'){
	$query = "select KPNGBE_PRODUCTREFERENCE from INVENTORY_TODAY
	LEFT JOIN SN_MATERIAL_LIST ON KPNGBE_PRODUCTREFERENCE=KPNGB_PROD_REF
	WHERE KPNGB_PROD_REF IS NULL AND KPNGBE_PRODUCTREFERENCE IS NOT NULL
	GROUP BY KPNGBE_PRODUCTREFERENCE";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
	 	exit;
	}else{
		OCIFreeStatement($stmt);
	}

	if (count($res['KPNGBE_PRODUCTREFERENCE'])!=0){
		$output.="<table class='table table-condensed'>
		<caption>Material(s) not found in the MASTERE REFRENCE DATABASE:</caption>
		<thead><th>KPNGBE PRODUCTREFERENCE</th></thead><tbody>";
		foreach ($res['KPNGBE_PRODUCTREFERENCE'] as $key=>$attrib_id){
			$output.="<tr class='warning'><td>".$res['KPNGBE_PRODUCTREFERENCE'][$key]."<td></tr>";
		}
			$output.="</tbody></table>";

		$out['msg']="Errors found in the material list!";
		$out['output']=$output;
		$out['msgtype']="error";
	}else{
		$out['msg']="Material file is OK";
		$out['msgtype']="info";
	}
	echo json_encode($out);
}elseif ($_POST['filetype']=='movement'){
	$error=0;
	$output="";

	//Check if products are in master ref db
	$query = "select KPNGB_PROD_REF, BASE_ITEM from MOVEMENT_TODAY
	LEFT JOIN SN_MATERIAL_LIST ON KPNGB_PROD_REF=BASE_ITEM
	WHERE KPNGB_PROD_REF IS NULL AND BASE_ITEM IS NOT NULL
	GROUP BY KPNGB_PROD_REF,BASE_ITEM";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
	 	exit;
	}else{
		OCIFreeStatement($stmt);
	}

	if (count($res['KPNGB_PROD_REF'])!=0){
		$output.="<table class='table table-condensed'>
		<caption>Material(s) not found in the MASTERE REFRENCE DATABASE:</caption>
		<thead><th>KPNGBE PRODUCTREFERENCE</th></thead><tbody>";
		foreach ($res['BASE_ITEM'] as $key=>$attrib_id){
			$output.="<tr class='warning'><td>".$res['BASE_ITEM'][$key]."</td></tr>";
		}
		$output.="</tbody></table>";
		$error=1;
		$query= "INSERT INTO MOVEMENT_LOG VALUES (SYSDATE,'NOT OK','".escape_sq($output)."','".$guard_username."')";
		//echo $query;
		$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt4) {
		  die_silently($conn_Infobase, $error_str);
		}else{
		  OCICommit($conn_Infobase);
		}
	}

	//Check if upgrades are existing
	$query = "select UPGNR, N1_UPGNR, N1_STATUS, IB_RAFID from MOVEMENT_TODAY LEFT JOIN MASTER_REPORT ON UPGNR=N1_UPGNR
	WHERE UPGNR LIKE '99%' AND (N1_UPGNR IS NULL OR N1_STATUS IS NULL OR IB_RAFID IS NULL)";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
	 	exit;
	}else{
		OCIFreeStatement($stmt);
	}

	if (count($res['UPGNR'])!=0){
		$output.="<table class='table table-condensed'>
		<caption>Upgrades do not exist in MASTER REPORT:</caption>
		<thead><tr><th>UPGNR</th><th>STATUS N1</th><th>RAF ID</th></thead><tbody>";
		foreach ($res['UPGNR'] as $key=>$attrib_id){
			$output.="<tr class='warning'><td>".$res['UPGNR'][$key]."<td><td>".$res['N1_STATUS'][$key]."<td><td>".$res['IB_RAFID'][$key]."</td></tr>";
		}
		$output.="</tbody></table>";

		$error=1;
		$query= "INSERT INTO MOVEMENT_LOG VALUES (SYSDATE,'NOT OK','".escape_sq($output)."','".$guard_username."')";
		//echo $query;
		$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt4) {
		  die_silently($conn_Infobase, $error_str);
		}else{
		  OCICommit($conn_Infobase);
		}
	}
	//WHE CHECK FOR NB IF MOVEMENTID(=site ID) is existing
	$query = "select SUBSTR(N1_SITEID,2,6), MOVEMENT_ID, UPGNR from MOVEMENT_TODAY 
			LEFT JOIN MASTER_REPORT ON MOVEMENT_ID=SUBSTR(N1_SITEID,2,6)
				WHERE (N1_NBUP ='NB' OR N1_NBUP ='NB REPL')
			AND SUBSTR(N1_SITEID,2,6)!=MOVEMENT_ID";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
	 	exit;
	}else{
		OCIFreeStatement($stmt);
	}

	if (count($res['MOVEMENT_ID'])!=0){
		$output.="<table class='table table-condensed'>
		<caption>Site ID's do not exist in MASTER REPORT:</caption>
		<thead><tr><th>MOVEMENT ID</th></thead><tbody>";
		foreach ($res['MOVEMENT_ID'] as $key=>$attrib_id){
			$output.="<tr class='warning'><td>".$res['MOVEMENT_ID'][$key]."<td></tr>";
		}
		$output.="</tbody></table>";

		$error=1;
		$query= "INSERT INTO MOVEMENT_LOG VALUES (SYSDATE,'NOT OK','".escape_sq($output)."','".$guard_username."')";
		//echo $query;
		$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt4) {
		  die_silently($conn_Infobase, $error_str);
		}else{
		  OCICommit($conn_Infobase);
		}
	}

	//CHECK FOR UPG if the site ID is corresponding to the siteID in the master report
	$query = "select SUBSTR(N1_SITEID,2,6), MOVEMENT_ID, UPGNR,N1_UPGNR from MOVEMENT_TODAY 
	LEFT JOIN MASTER_REPORT ON UPGNR=N1_UPGNR
		WHERE N1_NBUP ='UPG'  AND UPGNR LIKE '99%'
	AND SUBSTR(N1_SITEID,2,6)!=MOVEMENT_ID AND MOVEMENT_ID!='RETOUR'";
		//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
	 	exit;
	}else{
		OCIFreeStatement($stmt);
	}

	if (count($res['MOVEMENT_ID'])!=0){
		$output.="<table class='table table-condensed'>
		<caption>Site ID's are not correspong to the UPG in MASTER REPORT:</caption>
		<thead><tr><th>MOVEMENT ID</th><th>UPGNR</th></thead><tbody>";
		foreach ($res['MOVEMENT_ID'] as $key=>$attrib_id){
			$output.="<tr class='warning'><td>".$res['MOVEMENT_ID'][$key]."</td><td>".$res['UPGNR'][$key]."</td></tr>";
		}
		$output.="</tbody></table>";

		$error=1;
		$query= "INSERT INTO MOVEMENT_LOG VALUES (SYSDATE,'NOT OK','".escape_sq($output)."','".$guard_username."')";
		//echo $query;
		$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt4) {
		  die_silently($conn_Infobase, $error_str);
		}else{
		  OCICommit($conn_Infobase);
		}
	}


	//For the UPG's we check if the upgrade type machtes with master report
	$query = "select SUBSTR(N1_SITEID,2,6), MOVEMENT_ID, UPGNR,N1_UPGNR,N1_SITETYPE,SITETYPE from MOVEMENT_TODAY 
	LEFT JOIN MASTER_REPORT ON UPGNR=N1_UPGNR
		WHERE N1_NBUP ='UPG'
	AND N1_SITETYPE!=SITETYPE AND SUBSTR(N1_SITETYPE,1,3)!=SUBSTR(SITETYPE,1,3)"; // SUBSTR(N1_SITETYPE,1,3) is for LTE and LTEX 
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
	 	exit;
	}else{
		OCIFreeStatement($stmt);
	}

	if (count($res['MOVEMENT_ID'])!=0){
		$output.="<table class='table table-condensed'>
		<caption>UPG type does not match with UPG type in MASTER REPORT:</caption>
		<thead><tr><th>MOVEMENT ID</th><thUPGNR</th><th>SITE TYPE N1</th><th>SITE TYPE MOVEMENT</th></thead></tr>

		<tbody>";
		foreach ($res['MOVEMENT_ID'] as $key=>$attrib_id){
			$output.="<tr class='warning'><td>".$res['MOVEMENT_ID'][$key]."</td><td>".$res['UPGNR'][$key]."</td><td>".$res['N1_SITETYPE'][$key]."</td><td>".$res['SITETYPE'][$key]."</td></tr>";
		}
		$output.="</tbody></table>";

		$error=1;

		$query= "INSERT INTO MOVEMENT_LOG VALUES (SYSDATE,'NOT OK','".escape_sq($output)."','".$guard_username."')";
		//echo $query;
		$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt4) {
		  die_silently($conn_Infobase, $error_str);
		}else{
		  OCICommit($conn_Infobase);
		}
	}

	if ($error==1){
		$out['output']=$output;
		$out['msg']="The are errors in the movement file. Please correct and re-upload!";
		$out['msgtype']="error";
	}else{
		$out['output']="";
		$out['msg']="Movement file is OK";
		$out['msgtype']="info";
	}
	echo json_encode($out);

}elseif ($_POST['filetype']=='invmov'){
	$error=0;
	$output="";
	$query = "select * FROM VW_INVENTORY_TODAY_MINUSONE WHERE TODAY_MINUSONE != - QTY_OUT AND TODAY_MINUSONE !=QTY_IN";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
	 	exit;
	}else{
		OCIFreeStatement($stmt);
	}

	if (count($res['LOT_SERIAL_NUMBER'])!=0){
		$output.="<table class='table table-condensed'>
		<caption>Following movements are not OK:</caption>
		<thead><tr><td>LOT SERIAL NUMBER</td><td>KPNGBE PRODUCTREFERENCE</td><td>AMOUNT INVENTORY TODAY</td><td>AMOUNT INVENTORY -1</td><td>MOVEMENT</td><td>QTY IN</td><td>QTY OUT</td></tr></thead><tbody>";
		foreach ($res['LOT_SERIAL_NUMBER'] as $key=>$attrib_id){
			$output.="<tr class='warning'>
			<td>".$res['LOT_SERIAL_NUMBER'][$key]."</td>
			<td>".$res['KPNGBE_PRODUCTREFERENCE'][$key]."</td>
			<td>".$res['TOTAL_TODAY'][$key]."</td>
			<td>".$res['TOTAL_MINUSONE'][$key]."</td>
			<td>".$res['TODAY_MINUSONE'][$key]."</td>
			<td>".$res['QTY_IN'][$key]."</td>
			<td>".$res['QTY_OUT'][$key]."</td></tr>";
		}
		$output.="</tbody></table>";

		$query= "INSERT INTO MOVEMENT_LOG VALUES (SYSDATE,'NOT OK','".escape_sq($output)."','".$guard_username."')";
		//echo $query;
		$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt4) {
		  die_silently($conn_Infobase, $error_str);
		}else{
		  OCICommit($conn_Infobase);
		}


		$out['output']=$output;
		$out['msg']="The are errors in the movement of today. Please correct and re-upload!";
		$out['msgtype']="error";
		echo json_encode($out);

	}else{



		$query= "INSERT INTO MOVEMENT_ARCHIVE SELECT * FROM MOVEMENT_TODAY";
		//echo $query;
		$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt4) {
		  die_silently($conn_Infobase, $error_str);
		}else{
		  OCICommit($conn_Infobase);
		}

		$query= "INSERT INTO MOVEMENT_LOG VALUES (SYSDATE,'OK','','".$guard_username."')";
		//echo $query;
		$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt4) {
		  die_silently($conn_Infobase, $error_str);
		}else{
		  OCICommit($conn_Infobase);
		}

		rename('/var/www/html/Uploads/INVENTORY/Inventory_today/inventory.xls', '/var/www/html/Uploads/INVENTORY/Inventory_archive/inventory'.date("dmY_his").'.xls');
		rename('/var/www/html/Uploads/INVENTORY/Movement_today/movement.xls', '/var/www/html/Uploads/INVENTORY/Movement_archive/movement'.date("dmY_his").'.xls');

		$out['output']="";
		$out['msg']="Movement of today has passed";
		$out['msgtype']="info";
		echo json_encode($out);
	}
}elseif ($_POST['filetype']=='chipotage'){
	$query= "DELETE FROM INVENTORY_MINUSONE";
	//echo $query;
	$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt4) {
	  die_silently($conn_Infobase, $error_str);
	}else{
	  OCICommit($conn_Infobase);
	}

	$query= "INSERT INTO INVENTORY_MINUSONE SELECT * FROM INVENTORY_TODAY";
	//echo $query;
	$stmt4 = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt4) {
	  die_silently($conn_Infobase, $error_str);
	}else{
	  OCICommit($conn_Infobase);
	}

	$out['output']=$output;
	$out['msg']="Chipotage done!";
	$out['msgtype']="error";
	echo json_encode($out);
}


?>
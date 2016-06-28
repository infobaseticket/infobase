<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/phpmailer/class.phpmailer.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['action']=="insert_new_bsds"){
	$COMMENTS=str_replace("'","''",$_POST['COMMENTS']);

	$query = "INSERT INTO BSDS_GENERALINFO2 VALUES ('','".$_POST['ADDRESSFK']."' ,'".$_POST['candidate']."','no',
	'Pending','Pending','no','". $guard_username."',SYSDATE, SYSDATE,'".$_POST['BSDS_TYPE']."','".$COMMENTS."','','".$_POST['BSDS_TR']."','','". $guard_username."','','','no','','','')";
	echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);  
		$res["msg"] = "New BSDS has succesfully been created for ".$_POST['candidate']."!";
		$res["type"]="info";
		$res["site"]=$_POST['siteID'];
		echo json_encode($res);
	}	
}

if ($_POST['action']=="change_bsds_funding_id"){

	if ($_POST['IDNR']!=""){
		$query = "SELECT * FROM BSDS_SITE_FUNDED WHERE IDNR='".$_POST['IDNR']."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
			$total_records=count($res1['IDNR']);
			//echo $total_records;
			if ($total_records>=2){
				$query = "DELETE FROM BSDS_SITE_FUNDED WHERE IDNR='".$_POST['IDNR']."' AND SITEID!='".$_POST['candidate']."'";
				$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
				$query = "INSERT INTO BSDS_SITE_FUNDED VALUES (
				'','".$_POST['BSDSKEY']."', '".$_POST['candidate']."','','no','')";
				//echo $query;
				$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
			}
		}

		$query = "DELETE FROM BSDS_SITE_FUNDED WHERE IDNR!='".$_POST['IDNR']."' AND BSDSKEY='".$_POST['BSDSKEY']."'";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}

		$query = "UPDATE BSDS_SITE_FUNDED SET BSDSKEY='".$_POST['BSDSKEY']."', COPIED='no' WHERE IDNR='".$_POST['IDNR']."'";
		//echo $query;
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
			echo "BSDS ID for funding has been has been changed to ".$_POST['BSDSKEY']."!";
		}
	}else{
		if ($_POST['candidate']!=''){
			$query = "DELETE FROM BSDS_SITE_FUNDED WHERE SITEID='".$_POST['candidate']."'";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
		}
		$query = "INSERT INTO BSDS_SITE_FUNDED VALUES (
				'','".$_POST['BSDSKEY']."', '".$_POST['candidate']."','','no','')";
				echo $query;
				$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
		}
	}

}

if ($_POST['action']=="change_teamlacc"){
	if ($_POST['pk']!="" && $_POST['value']!=""){
		$query = "UPDATE BSDS_GENERALINFO set SITE_CONFLICT='', TEAML_APPROVED2='',
		TEAML_APPROVED='". $_POST['value'] ."',CHANGE_DATE= SYSDATE, TEAMLEADER='".$guard_username."',BSDS_TR='',
		DESIGNER_UPDATE='".$guard_username."'  WHERE BSDSKEY LIKE '%".$_POST['pk']."%'";
		$stmt1 = OCIParse($conn_Infobase, $query);
		OCIExecute($stmt1);
		echo  $_POST['value'];
	}
}

if ($_POST['action']=="delete_bsds"){
	
	if ($_POST['bsdsid']!=""){

		$query="UPDATE INFOBASE.BSDS_GENERALINFO2 SET DELETEDSTATUS='yes', DELETED_BY='".$guard_username."',DELETED_DATE=SYSDATE
		WHERE BSDSKEY = '". $_POST['bsdsid'] ."'";
		//echo $query."<br>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);		
		echo "BSDS ".$_POST['bsdsid']." has been successfully deleted!";
	}
}

if ($_POST['action']=="add_bsds_funded"){
	
	if ($_POST['bsdsid']!=""){

		$query="SELECT N1_CANDIDATE, N1_NBUP, N1_UPGNR,N1_SITEID, IB_TECHNOS_CON, AU353, AU305 FROM MASTER_REPORT WHERE IB_RAFID = '".$_POST['rafid']."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
	   	if (!$stmt) {
	      die_silently($conn_Infobase, $error_str);
	      exit;
	   	} else {
	      OCIFreeStatement($stmt);
	   	}

	   	if ($_POST['todo']=="add"){
	   		$au305=date('d-m-Y');
	   		$au305_before='';
	   		$actionstatus="ADD BSDS FUNDED";
	   		$currentstatus='BSDS FUNDED';
	   		$currentms=date('d-m-Y');
	   	}else if($_POST['todo']=="remove"){
	   		$au305='';
	   		$au305_before=$res['AU305'][0];
	   		$actionstatus="REMOVE BSDS FUNDED";
	   		$currentstatus='SITE FUNDED';
	   		$currentms=$res['AU353'][0];
	   	}

	   	if ($res['N1_CANDIDATE'][0]!=''){

	   		if ($res['N1_NBUP'][0]=='UPG'){
	   			$queryUP = "INSERT INTO INFOBASE.NET1UPDATER_CSV VALUES ('".substr($res['N1_CANDIDATE'][0],0,7)."','".$res['N1_UPGNR'][0]."','U305','".$au305."','BSDS ".$guard_username."',SYSDATE,'0','','','".$res['N1_CANDIDATE'][0]."')";
				//echo $queryUP;
				$stmtUP = parse_exec_free($conn_Infobase, $queryUP, $error_str);
				if (!$stmtUP) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
	   		}elseif ($res['N1_NBUP'][0]=='NB'){
	   			$queryNB = "INSERT INTO INFOBASE.NET1UPDATER_CSV VALUES ('".$res['N1_SITEID'][0]."','','A305','".$au305."','BSDS ".$guard_username."',SYSDATE,'0','','','".$res['N1_CANDIDATE'][0]."')";
				//echo $queryNB."<br>";
				$stmtUP = parse_exec_free($conn_Infobase, $queryNB, $error_str);
				if (!$stmtUP) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
	   		}
		
			$query = "INSERT INTO BSDS_STATUS_CHANGES VALUES ('',SYSDATE,'".$res['N1_SITEID'][0]."','".$res['N1_CANDIDATE'][0]."','".$res['N1_NBUP'][0]."',
				'".$res['N1_UPGNR'][0]."','".$res['AU353'][0]."','".$res['AU353'][0]."','".str_replace("-", "/", $au305)."','".str_replace("-", "/", $au305_before)."','','',
				'".$_POST['rafid']."','".$_POST['bsdsid']."','".$actionstatus."','".$currentstatus."','".str_replace("-", "/", $currentms)."','0', '".$res['IB_TECHNOS_CON'][0]."')";
			//echo $query;				
			$stmtCH = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmtCH) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
	
			echo "BSDS ".$_POST['bsdsid']." FUNDING has been changed and will synced to N1 in about 30 minutes!";
	   	}

		
	}
}

if ($_POST['action']=="remove_funding"){
	$BSDS_BOB_REFRESH=$_POST['bsdsbobrefresh'];
	$to=$_POST['status'];
	$BSDSKEY=$_POST['bsdskey'];
	$candidate=$_POST['candidate'];
	$upgnr=$_POST['upgnr'];
	$net1date=$_POST['net1date'];

	if(substr($upgnr,0,2)=="99" && strlen($upgnr)==8){
		if ($to=="FUND"){
			$code="U305";
		}else if ($to=="BUILD"){
			$code="U380";
		}else if ($to=="POST"){
			$code="U353";
		}
		$query="UPDATE NET1_UPGRADES_2 SET $code=''
		WHERE WOR_UDK = '". $upgnr ."'";
		echo $query."<br>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
	}else{
		if ($to=="FUND"){
			$code="A305";
		}else if ($to=="BUILD"){
			$code="A80";
		}else if ($to=="POST"){
			$code="A353";
		}
		$query="UPDATE NET1_NEWBUILDS_2 SET $code=''
		WHERE WOR_UDK = '". $upgnr ."'";
		//echo $query."<br>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
	}
}

if ($_POST['action']=="overrideMultiFund"){

	$query = "INSERT INTO BSDS_FUNDED_MULTI VALUES ('".$_POST['BSDSKEY']."','".$_POST['BSDS_BOB_REFRESH']."')";
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "BSDS HAS BEEN OVERRIDDEN!";
		$res["data"] = $message;
		$res["type"]="info";
		echo json_encode($res);
	}
}

?>
<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/phpmailer/class.phpmailer.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


if ($_POST['action']=="insert_new_bsds_raf"){

	if ($_POST['bsdskey']==''){
		$COMMENTS=str_replace("'","''",escape_sq($_POST['COMMENTS']));
		$query = "INSERT INTO BSDS_OVERVIEW VALUES ('','".$_POST['candidate']."','". $guard_username."',SYSDATE,'".$_POST['BSDS_TYPE']."','".$COMMENTS."',
		'','','','no','','','".$_POST['rafid']."','".$_POST['upgnr']."',0,'','','','')";
		//echo $query;
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);  
			$res["msg"] = "New BSDS has succesfully been created for ".$_POST['candidate']." ".$_POST['upgnr']."!";
			$res["type"]="info";
			$res["site"]=$_POST['siteID'];
			echo json_encode($res);
		}	
	}else{
		$query = "UPDATE BSDS_OVERVIEW SET RAFID='".$_POST['rafid']."' WHERE BSDSKEY='".$_POST['bsdskey']."'";
		//echo $query;
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);  
			$res["msg"] = "BSDS ".$_POST['bsdskey']." is now attached to ".$_POST['bsdskey']."!";
			$res["type"]="info";
			$res["site"]=$_POST['siteID'];	
		}	
	}
	echo json_encode($res);
}else if ($_POST['action']=="delete_bsds"){
	
	if ($_POST['key']!=""){

		$query="UPDATE INFOBASE.BSDS_OVERVIEW SET DEL_STATUS='yes', DEL_BY='".$guard_username."',DEL_DATE=SYSDATE
		WHERE BSDSKEY||REPLACE(REPLACE(REPLACE(CREATED_DATE,':',''),'/',''),' ','')||FROZEN = '". $_POST['key'] ."'";
		//echo $query."<br>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);		
		echo "BSDS ".$_POST['bsdsid']." has been successfully deleted!";
	}
}else if ($_POST['action']=="freeze_bsds"){


	$query = "SELECT PARTNER_DESIGN FROM BSDS_RAFV2 WHERE RAFID='".$_POST['rafid']."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
	$total_records=count($res1['PARTNER_DESIGN']);
	if($total_records==1){

		if ($res1['PARTNER_DESIGN'][0]!='BASE RF OK'){

			$date_today= date('d-m-Y H:m:s');

			$query="INSERT INTO BSDS_OVERVIEW (select 
			BSDSKEY,CANDIDATE,'".$guard_username."','".$date_today."',BSDS_TYPE,COMMENTS,DESIGNER_UPDATE,DATE_UPDATE,
			BY_UPDATE,DEL_STATUS,DEL_BY,DEL_DATE,RAFID,UPGNR,'1',CABTYPE,UNIRAN,RECTIFIER,POWERSUP
			from BSDS_OVERVIEW WHERE BSDSKEY||REPLACE(REPLACE(REPLACE(CREATED_DATE,':',''),'/',''),' ','')||FROZEN='".$_POST['key']."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$query="INSERT INTO BSDS_CU_BBU (select 
			BSDSKEY,'".$date_today."',CHANGE_DATE,C1_SLOT1,C1_SLOT2,C1_SLOT3,C1_SLOT4,C1_SLOT5,C1_SLOT6,C1_SLOT7,
			C1_SLOT8,C1_SLOT13,C1_SLOT14,C1_SLOT15,C1_TECHNOS,C2_SLOT1,C2_SLOT2,C2_SLOT3,C2_SLOT4,C2_SLOT5,C2_SLOT6,
			C2_SLOT7,C2_SLOT8,C2_SLOT13,C2_SLOT14,C2_SLOT15,C2_TECHNOS,'1',SITEKEY
			from BSDS_CU_BBU WHERE SITEKEY||STATUS='".$_POST['key2']."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$query="INSERT INTO BSDS_CU2 (select 
			BSDSKEY,'".$date_today."',GSM900_1,GSM1800_1,UMTS900_1,UMTS2100_1,LTE800_1,LTE1800_1,LTE2600_1,GSM900_2,GSM1800_2,
			UMTS900_2,UMTS2100_2,LTE800_2,LTE1800_2,LTE2600_2,GSM900_3,LTE800_3,GSM1800_3,UMTS900_3,UMTS2100_3,LTE1800_3,LTE2600_3,
			GSM900_4,GSM1800_4,UMTS900_4,UMTS2100_4,LTE800_4,LTE1800_4,LTE2600_4,GSM900_5,GSM1800_5,UMTS900_5,UMTS2100_5,LTE800_5,
			LTE1800_5,LTE2600_5,GSM900_6,GSM1800_6,UMTS900_6,UMTS2100_6,LTE800_6,LTE1800_6,LTE2600_6,'1',SITEKEY 
			from BSDS_CU2 WHERE SITEKEY||STATUS='".$_POST['key2']."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$query="INSERT INTO BSDS_CU_SEC (select 
			BSDSKEY,SITEKEY,'1',CHANGEDATE,'".$date_today."',TECHNO,SECT,CONFIG,TMA,FREQ_ACTIVE1,FREQ_ACTIVE2,FREQ_ACTIVE3,
			TRU_INST1_1,TRU_INST1_2,TRU_INST2_1,TRU_INST2_2,TRU_INST3_1,TRU_INST3_2,TRU_TYPE1_1,TRU_TYPE1_2,TRU_TYPE2_1,
			TRU_TYPE2_2,TRU_TYPE3_1,TRU_TYPE3_2,ANTTYPE1,ANTTYPE2,ELECTILT1,ELECTILT2,MECHTILT1,MECHTILT2,MECHTILT_DIR1,
			MECHTILT_DIR2,ANTHEIGHT1,ANTHEIGHT2,AZI1,AZI2,FEEDER,FEEDERLEN,DCBLOCK,COMB,HR_ACTIVE,MCPAMODE,MCPATYPE,ACS,RET
			from BSDS_CU_SEC WHERE SITEKEY||STATUS='".$_POST['key2']."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
			

			$query="INSERT INTO BSDS_CU_GEN (select 
			BSDSKEY,SITEKEY,'1',CHANGEDATE,'".$date_today."' ,TECHNO,CDUTYPE,BBS,CABTYPE,
			NR_OF_CAB,DXUTYPE1,DXUTYPE2,DXUTYPE3,PLAYSTATION,POWERSUP,IPB,PSU,TXBHW,TXBSW,RAXBHW,RAXBSW,
			MBPS,RAXEHW,RAXESW,HSTXHW,HSTXSW,SERVICE,BPC,BPK,CC,BPN2,PM0,FS5,RECT,BPL
			from BSDS_CU_GEN WHERE SITEKEY||STATUS='".$_POST['key2']."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
			

			$query="INSERT INTO BSDS_PL_GEN (select 
			BSDSKEY,CABTYPE,NR_OF_CAB,CDUTYPE,COMMENTS,BBS,DXUTYPE1,DXUTYPE2,DXUTYPE3,PLAYSTATION,
			TECHNO,'1','".$date_today."',TECHNO_CHANGEDATE,POWERSUP,IPB,PSU,TXBHW,TXBSW,MBPS,RAXEHW,RAXESW,
			HSTXHW,HSTXSW,SERVICE,BPC,BPK,CC,BPN2,PM0,FS5,RECT,BPL,RAXBHW,RAXBSW
			from BSDS_PL_GEN WHERE BSDSKEY||REPLACE(REPLACE(REPLACE(BSDS_BOB_REFRESH,':',''),'/',''),' ','')||STATUS='".$_POST['key']."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$query="INSERT INTO BSDS_PL_SEC (select 
			BSDSKEY,FREQ_ACTIVE1,TRU_INST1_1,TRU_TYPE1_1,TRU_INST1_2,TRU_TYPE1_2,CONFIG,STATE,TMA,AZI1,
			ANTTYPE1,ELECTILT1,MECHTILT1,MECHTILT_DIR1,ANTHEIGHT1,ANTTYPE2,ELECTILT2,MECHTILT2,MECHTILT_DIR2,
			ANTHEIGHT2,FEEDERLEN,FEEDER,COMB,DCBLOCK,TRU_INST2_1,TRU_TYPE2_1,TRU_INST2_2,TRU_TYPE2_2,TRU_INST3_1,
			TRU_TYPE3_1,TRU_INST3_2,TRU_TYPE3_2,FREQ_ACTIVE2,FREQ_ACTIVE3,HR_ACTIVE,AZI2,TECHNO,SECT,'1',
			'".$date_today."',MCPAMODE,MCPATYPE,ACS,RET
			from BSDS_PL_SEC WHERE BSDSKEY||REPLACE(REPLACE(REPLACE(BSDS_BOB_REFRESH,':',''),'/',''),' ','')||STATUS='".$_POST['key']."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$query="INSERT INTO BSDS_PL2 (select 
			BSDSKEY,'".$date_today."',GSM900_1,GSM1800_1,UMTS900_1,UMTS2100_1,LTE800_1,LTE1800_1,LTE2600_1,GSM900_2,
			GSM1800_2,UMTS900_2,UMTS2100_2,LTE800_2,LTE1800_2,LTE2600_2,GSM900_3,LTE800_3,GSM1800_3,UMTS900_3,UMTS2100_3,
			LTE1800_3,LTE2600_3,GSM900_4,GSM1800_4,UMTS900_4,UMTS2100_4,LTE800_4,LTE1800_4,LTE2600_4,GSM900_5,GSM1800_5,
			UMTS900_5,UMTS2100_5,LTE800_5,LTE1800_5,LTE2600_5,GSM900_6,GSM1800_6,UMTS900_6,UMTS2100_6,LTE800_6,LTE1800_6,
			LTE2600_6,'1'
			from BSDS_PL2 WHERE BSDSKEY||REPLACE(REPLACE(REPLACE(BSDS_BOB_REFRESH,':',''),'/',''),' ','')||STATUS='".$_POST['key']."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$query="INSERT INTO BSDS_PL_BBU (select 
			BSDSKEY,'".$date_today."',CHANGE_DATE,C1_SLOT1,C1_SLOT2,C1_SLOT3,C1_SLOT4,C1_SLOT5,C1_SLOT6,C1_SLOT7,
			C1_SLOT8,C1_SLOT13,C1_SLOT14,C1_SLOT15,C1_TECHNOS,C2_SLOT1,C2_SLOT2,C2_SLOT3,C2_SLOT4,C2_SLOT5,C2_SLOT6,
			C2_SLOT7,C2_SLOT8,C2_SLOT13,C2_SLOT14,C2_SLOT15,C2_TECHNOS,'1'
			from BSDS_PL_BBU WHERE BSDSKEY||REPLACE(REPLACE(REPLACE(BSDS_BOB_REFRESH,':',''),'/',''),' ','')||STATUS='".$_POST['key']."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$query="UPDATE BSDS_RAFV2 SET PARTNER_DESIGN='PARTNER RF OK' WHERE RAFID='".$_POST['rafid']."'";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$queryIN="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['rafid']."',SYSDATE,'FREEZE BSDS ".$_POST['key']."+PARTNER DESIGN','".$guard_username."','PARTNER DESIGN')";
			//echo $queryIN;
			$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
			if (!$stmtIN) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}



			$query = "SELECT N1_SITEID, N1_CANDIDATE,N1_UPGNR FROM MASTER_REPORT WHERE IB_RAFID='".$_POST['rafid']."'";
			//echo $query;
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
			$total_records=count($res1['N1_SITEID']);

			if ($total_records!=0){
				if ($_POST['upgnr']=='NB'){
					$MS="A305";
					$net1link=$res1['N1_SITEID'][0];
				}else{
					$MS="U305";
					$net1link=$res1['N1_UPGNR'][0];
				}

				$queryUP = "INSERT INTO INFOBASE.NET1UPDATER_CSV VALUES ('".$res1['N1_SITEID'][0]."','".$net1link."','".$MS."','".date('d-m-Y')."','RAF',SYSDATE,'0','','','".$res1['N1_CANDIDATE'][0]."')";
				//echo $queryUP.EOL;
				$stmtUP = parse_exec_free($conn_Infobase, $queryUP, $error_str);
				if (!$stmtUP) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
			}
			$type="info";
			$message="BSDS HAS BEEN FROZEN!";
		}else{
			$type="danger";
			$message="BSDS CANNOT BEEN FROZEN as DESIGN PHASE has been finished!";
		}
	}else{
		$type="danger";
		$message="RAFID not found!";
	}
	/*
	$query = "INSERT INTO BSDS_FUNDED_MULTI VALUES ('".$_POST['BSDSKEY']."','".$_POST['BSDS_BOB_REFRESH']."')";
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "BSDS HAS BEEN FROZEN!";
		$res["data"] = $message;
		$res["type"]="info";
		echo json_encode($res);
	}
	*/

	
	$res["data"] = $message;
	$res["type"]=$type;
	echo json_encode($res);
}
?>
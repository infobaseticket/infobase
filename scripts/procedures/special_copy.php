<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/include/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

error_reporting(E_ALL);
copy_site_data_oldinfobase($_GET['diplay_technologie'],$_GET['BSDS_BOB_REFRESH'],$_GET['viewtype']);

function copy_site_data_oldinfobase($technologie,$BSDS_BOB_REFRESH,$type){
	global $conn_Infobase;

	echo "<font color=red>".$_SESSION['BSDSKEY']." => $BSDS_BOB_REFRESH ($type) copy ($technologie)</font><br>"; // --$BSDS_BOB_REFRESH == $BOB_EREFRESH_DATE_previous_status

	if ($type=="_SITE"){
		$from="_POST";
		$to="_POST";
	}elseif ($type=="_POST"){
		$from="_POST";
		$to="_FUND";
	}elseif ($type=="_BUILD"){
		$from="_POST";
		$to="_FUND";
	}


	if ($technologie=="GSM900"){

		$query="SELECT count(BSDSKEY) as AMOUNT FROM BSDS_PLANNED_GEN_GSM900".$from."@INFOBASEV1
		WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $_SESSION['BSDSKEY'] ."'";
		echo $query;
/*
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_totable=$res1['AMOUNT'][0];
		}
*/
		//echo "amount_in_totable".$amount_in_totable;
		if ($amount_in_totable==0){
			$query="INSERT INTO INFOBASE.BSDS_PLANNED_GEN_GSM900".$to." (BSDSKEY, CABTYPE,
			NR_OF_CAB, CDUTYPE, COMMENTS, BBS, DXUTYPE1, DXUTYPE2, DXUTYPE3,BSDS_BOB_REFRESH)
			SELECT
				BSDSKEY, CABTYPE, NR_OF_CAB,  CDUTYPE, COMMENTS, BBS, DXUTYPE1,DXUTYPE2,
				DXUTYPE3,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh
			FROM INFOBASE.BSDS_PLANNED_GEN_GSM900".$from."@INFOBASEV1
			WHERE BSDSKEY = '". $_SESSION['BSDSKEY'] ."'";

			echo $query."<hr>";
/*
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
*/
			for ($n = 1; $n <= 4; $n++) {
				$query="INSERT INTO INFOBASE.BSDS_PLANNED_GSM900_".$n.$to." (BSDSKEY, FREQ_ACTIVE1,
				TRU_INST1_1, TRU_TYPE1_1, TRU_INST1_2, TRU_TYPE1_2, CONFIG, STATE, TMA, AZI, ANTTYPE1,
				ELECTILT1,MECHTILT1, MECHTILT_DIR1, ANTHEIGHT1,ANTTYPE2, ELECTILT2, MECHTILT2,
				MECHTILT_DIR2, ANTHEIGHT2, FEEDERLEN, FEEDER, COMB, DCBLOCK,BSDS_BOB_REFRESH,
				TRU_INST2_1, TRU_TYPE2_1, TRU_INST2_2, TRU_TYPE2_2,TRU_INST3_1, TRU_TYPE3_1, TRU_INST3_2, TRU_TYPE3_2,
				FREQ_ACTIVE2, FREQ_ACTIVE3, HR_ACTIVE)
				SELECT
					BSDSKEY, FREQ_ACTIVE1, TRU_INST1_1, TRU_TYPE1_1, TRU_INST1_2, TRU_TYPE1_2,
					CONFIG, STATE, TMA, AZI,ANTTYPE1, ELECTILT1, MECHTILT1, MECHTILT_DIR1,
					ANTHEIGHT1, ANTTYPE2, ELECTILT2, MECHTILT2, MECHTILT_DIR2, ANTHEIGHT2,
					FEEDERLEN, FEEDER, COMB, DCBLOCK,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh,
					TRU_INST2_1, TRU_TYPE2_1, TRU_INST2_2, TRU_TYPE2_2,TRU_INST3_1, TRU_TYPE3_1, TRU_INST3_2,
					TRU_TYPE3_2, FREQ_ACTIVE2, FREQ_ACTIVE3, HR_ACTIVE
				FROM INFOBASE.BSDS_PLANNED_GSM900_".$n.$from."@INFOBASEV1
				WHERE BSDSKEY = '". $_SESSION['BSDSKEY'] ."'";
				if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
					$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_EREFRESH_DATE_previous_status."')";
				}
				echo $query."<hr>";
/*				
				$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}
				OCICommit($conn_Infobase);
*/
			}
			echo "<font size=1>$type HISTORY CREATED for GSM900!</font><br>";
		}
	}else if ($technologie=="GSM1800"){

		$query="SELECT count(BSDSKEY) as AMOUNT FROM BSDS_PLANNED_GEN_GSM1800".$from."@INFOBASEV1
		WHERE BSDSKEY = '". $_SESSION['BSDSKEY'] ."'";
		echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_from_table=$res1['AMOUNT'][0];
		}
		
		$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_PLANNED_GEN_GSM1800".$to."
		WHERE BSDSKEY = '". $_SESSION['BSDSKEY'] ."' AND BSDS_BOB_REFRESH='".$BSDS_BOB_REFRESH."'";
		echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_to_table=$res1['AMOUNT'][0];
		}
		
		
		echo "amount_in_from_table $amount_in_from_table // amount_in_to_table $amount_in_to_table<hr>";
		
		if ($amount_in_from_table==1 && $amount_in_to_table==0){

			$query="INSERT INTO INFOBASE.BSDS_PLANNED_GEN_GSM1800".$to." (BSDSKEY, CABTYPE,
			NR_OF_CAB, CDUTYPE, COMMENTS, BBS, DXUTYPE1, DXUTYPE2, DXUTYPE3,BSDS_BOB_REFRESH)
			SELECT
				BSDSKEY, CABTYPE, NR_OF_CAB,  CDUTYPE, COMMENTS, BBS, DXUTYPE1,DXUTYPE2,
				DXUTYPE3,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh
			FROM INFOBASE.BSDS_PLANNED_GEN_GSM1800".$from."@INFOBASEV1
			WHERE BSDSKEY = '". $_SESSION['BSDSKEY'] ."'";
			echo $query."<hr>";
/*
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
*/
			for ($n = 1; $n <= 4; $n++) {
				$query="INSERT INTO INFOBASE.BSDS_PLANNED_GSM1800_".$n.$to." (BSDSKEY,
				FREQ_ACTIVE1, TRU_INST1_1, TRU_TYPE1_1, TRU_INST1_2, TRU_TYPE1_2, CONFIG, STATE, TMA,
				AZI, ANTTYPE1, ELECTILT1,MECHTILT1, MECHTILT_DIR1, ANTHEIGHT1,ANTTYPE2,
				ELECTILT2, MECHTILT2, MECHTILT_DIR2, ANTHEIGHT2, FEEDERLEN, FEEDER, COMB,
				DCBLOCK, BSDS_BOB_REFRESH,TRU_INST2_1, TRU_TYPE2_1, TRU_INST2_2, TRU_TYPE2_2,
				TRU_INST3_1, TRU_TYPE3_1, TRU_INST3_2, TRU_TYPE3_2, FREQ_ACTIVE2, FREQ_ACTIVE3, HR_ACTIVE)
				SELECT
					BSDSKEY, FREQ_ACTIVE1, TRU_INST1_1, TRU_TYPE1_1, TRU_INST1_2, TRU_TYPE1_2, CONFIG,
					STATE, TMA, AZI,ANTTYPE1, ELECTILT1, MECHTILT1, MECHTILT_DIR1, ANTHEIGHT1,
					ANTTYPE2, ELECTILT2, MECHTILT2, MECHTILT_DIR2, ANTHEIGHT2, FEEDERLEN,
					FEEDER, COMB, DCBLOCK,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh,
					TRU_INST2_1, TRU_TYPE2_1, TRU_INST2_2, TRU_TYPE2_2,TRU_INST3_1, TRU_TYPE3_1, TRU_INST3_2,
					TRU_TYPE3_2, FREQ_ACTIVE2, FREQ_ACTIVE3, HR_ACTIVE
				FROM INFOBASE.BSDS_PLANNED_GSM1800_".$n.$from."@INFOBASEV1
				WHERE BSDSKEY = '". $_SESSION['BSDSKEY'] ."'";
				echo $query."<hr>";
/*
				$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}
				OCICommit($conn_Infobase);
*/
			}
		echo "<font size=1>$type DATA COPIED for GSM1800!</font><br>";
		}
	}
/*
	if ($dtechnologie=="UMTS"){

		$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_PLANNED_UMTS_GEN_01".$to."
		WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $_SESSION['BSDSKEY'] ."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_totable=$res1['AMOUNT'][0];;
		}

		if ($amount_in_totable==0){

			$query="INSERT INTO INFOBASE.BSDS_PLANNED_UMTS_GEN_01".$to." (BSDSKEY, CHANGE_DATE, POWERSUP,
				CABTYPE, IPB,PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS, COMMENTS, BSDS_BOB_REFRESH)
			SELECT
				BSDSKEY, CHANGE_DATE, POWERSUP, CABTYPE, IPB, PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS,
				COMMENTS,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh
			FROM INFOBASE.BSDS_PLANNED_UMTS_GEN_01".$from."
			WHERE BSDSKEY LIKE '". $_SESSION['BSDSKEY'] ."'";
			if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
				$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_EREFRESH_DATE_previous_status."')";
			}
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
			OCICommit($conn_Infobase);

			for ($n = 1; $n <= 3; $n++) {
				$query="INSERT INTO INFOBASE.BSDS_PLANNED_UMTS_01_".$n.$to." (BSDSKEY, UMTSCELLID,
					UMTSCELLPK, TRU_INST1, TRU_INST2, FREQ_ACTIVE, MCPAMODE, MCPATYPE, ACS, RET, ANTHEIGHT1,
					AZI, ANTTYPE1,ELECTILT1,MECHTILT1, MECHTILT_DIR1, FEEDER, FEEDERLEN, ANTTYPE2, ELECTILT2,
					MECHTILT2,MECHTILT_DIR2,ANTHEIGHT2, STATE, BSDS_BOB_REFRESH)
				SELECT
					BSDSKEY, UMTSCELLID, UMTSCELLPK, TRU_INST1, TRU_INST2, FREQ_ACTIVE, MCPAMODE, MCPATYPE,
					ACS, RET, ANTHEIGHT1, AZI, ANTTYPE1, ELECTILT1, MECHTILT1, MECHTILT_DIR1, FEEDER,
					FEEDERLEN, ANTTYPE2, ELECTILT2, MECHTILT2, MECHTILT_DIR2, ANTHEIGHT2, STATE,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh
				FROM INFOBASE.BSDS_PLANNED_UMTS_01_".$n.$from."
				WHERE BSDSKEY LIKE '".$_SESSION['BSDSKEY'] ."'";
				if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
					$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_EREFRESH_DATE_previous_status."')";
				}
				//echo $query."<hr>";
				$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}
				OCICommit($conn_Infobase);
			}
			echo "<font size=1>$type HISTORY CREATED for UMTS!</font><br>";
		}
	}

	if ($type=="SITE FUNDED"){

		$query = "UPDATE BSDS_SITE_FUNDED SET COPIED='yes', COPIED_DATE=SYSDATE WHERE BSDSKEY='".$_SESSION['BSDSKEY']."'";
		//echo "$query <hr>";
		$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}
	}*/
}

?>
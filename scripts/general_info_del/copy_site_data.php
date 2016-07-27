<?
function copy_site_data($type,$BSDSKEY,$technos,$BSDS_BOB_REFRESH,$copied,$BOB_REFRESH_DATE_previous_status,$donor,$lognode){

	global $conn_Infobase, $firephp;
	//echo $technos;
	if ($type=="SITE FUNDED"){
		$from="PRE";
		$to="POST";
	}elseif ($type=="BSDS FUNDED"){
		$from="POST";
		$to="FUND";
	}else if ($type=="BSDS AS BUILD"){
		$from="FUND";
		$to="BUILD";
	}else if ($type=="BSDS FUNDED DEFUNDED"){
		$from="FUND";
		$to="POST";
	}else if ($type=="BSDS AS BUILD DEFUNDED"){
		$from="BUILD";
		$to="FUND";
	}else if ($type==""){
		echo "TYPE ERROR: PLEASE CONTACT INFOBASE ADMIN!!";
		die;
	}

	if($donor==""){
		//FEEDERDATA
		$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_PL
		WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$to."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_totable1=$res1['AMOUNT'][0];
		}
		//echo "amount_in_totable".$amount_in_totable1;
		if ($amount_in_totable1==0){
			$query="INSERT INTO INFOBASE.BSDS_PL (BSDSKEY, BSDS_BOB_REFRESH,
			GSM900_1,GSM1800_1,UMTS900_1,UMTS2100_1,LTE800_1,LTE1800_1,LTE2600_1,
			GSM900_2,GSM1800_2,UMTS900_2,UMTS2100_2,LTE800_2,LTE1800_2,LTE2600_2,
			GSM900_3,GSM1800_3,UMTS900_3,UMTS2100_3,LTE800_3,LTE1800_3,LTE2600_3,
			GSM900_4,GSM1800_4,UMTS900_4,UMTS2100_4,LTE800_4,LTE1800_4,LTE2600_4,STATUS)
			SELECT
				BSDSKEY, to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,
				GSM900_1,GSM1800_1,UMTS900_1,UMTS2100_1,LTE800_1,LTE1800_1,LTE2600_1,
				GSM900_2,GSM1800_2,UMTS900_2,UMTS2100_2,LTE800_2,LTE1800_2,LTE2600_2,
				GSM900_3,GSM1800_3,UMTS900_3,UMTS2100_3,LTE800_3,LTE1800_3,LTE2600_3,
			GSM900_4,GSM1800_4,UMTS900_4,UMTS2100_4,LTE800_4,LTE1800_4,LTE2600_4,'".$to."'
			FROM INFOBASE.BSDS_PL
			WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$from."'";
			if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
				$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
			}
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
			OCICommit($conn_Infobase);
		}

		if ($type=="BSDS FUNDED"){

			//GENERAL CURRENT DATA
			$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_CU
			WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' AND STATUS='FUND'";
	//echo $query;
			//echo $query;
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$amount_in_totable=$res1['AMOUNT'][0];
			}
			if ($amount_in_totable==0){
				$query="INSERT INTO INFOBASE.BSDS_CU (BSDSKEY, BSDS_BOB_REFRESH,
				GSM900_1,GSM1800_1,UMTS900_1,UMTS2100_1,LTE800_1,LTE1800_1,LTE2600_1,
				GSM900_2,GSM1800_2,UMTS900_2,UMTS2100_2,LTE800_2,LTE1800_2,LTE2600_2,
				GSM900_3,GSM1800_3,UMTS900_3,UMTS2100_3,LTE800_3,LTE1800_3,LTE2600_3,
				GSM900_4,GSM1800_4,UMTS900_4,UMTS2100_4,LTE800_4,LTE1800_4,LTE2600_4,STATUS)
				SELECT
					BSDSKEY,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,
					GSM900_1,GSM1800_1,UMTS900_1,UMTS2100_1,LTE800_1,LTE1800_1,LTE2600_1,
					GSM900_2,GSM1800_2,UMTS900_2,UMTS2100_2,LTE800_2,LTE1800_2,LTE2600_2,
					GSM900_3,GSM1800_3,UMTS900_3,UMTS2100_3,LTE800_3,LTE1800_3,LTE2600_3,
					GSM900_4,GSM1800_4,UMTS900_4,UMTS2100_4,LTE800_4,LTE1800_4,LTE2600_4,'FUND'
				FROM INFOBASE.BSDS_CU
				WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='PRE'";
				$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}
				OCICommit($conn_Infobase);
			}
		}

		if (strpos($technos, "G9")!==false) {
			$amount_in_totable_G9=copyGSM('G9',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['G9'],$type,$from,$to);	
		}
		if (strpos($technos, "G18")!==false) {
			$amount_in_totable_G18=copyGSM('G18',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['G18'],$type,$from,$to);	
		}
		if (strpos($technos, "U9")!==false) {			
			$amount_in_totable_U9=copyUMTS('U9',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['U9'],$type,$from,$to);
		}
		if (strpos($technos, "U21")!==false) {
			$amount_in_totable_U21=copyUMTS('U21',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['U21'],$type,$from,$to);
		}
		if (strpos($technos, "L8")!==false) {
			$amount_in_totable_L8=copyLTE('L8',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['L8'],$type,$from,$to);
		}
		if (strpos($technos, "L18")!==false) {
			$amount_in_totable_L18=copyLTE('L18',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['L18'],$type,$from,$to);
		}
		if (strpos($technos, "L26")!==false) {
			$amount_in_totable_L26=copyLTE('L26',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['L26'],$type,$from,$to);
		}

		if ($type=="SITE FUNDED" && ($amount_in_totable_G9==0 || $amount_in_totable_G18==0 
			|| $amount_in_totable_U9==0 || $amount_in_totable_U21==0 
			|| $amount_in_totable_L18==0 || $amount_in_totable_L8==0  || $amount_in_totable_L26==0)){
			$query = "UPDATE BSDS_SITE_FUNDED SET COPIED='yes', COPIED_DATE=SYSDATE WHERE BSDSKEY='".$BSDSKEY."'";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
			echo "STATUS/TECHNOLOGIES FROM BSDS HAS BEEN CHANGED: COPY FROM ".$from." to ".$to;
		}else{
			$query = "UPDATE BSDS_SITE_FUNDED SET COPIED='yes' WHERE BSDSKEY='".$BSDSKEY."'";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
		}

	}else{ //SITE IS REPEATER
		if (strpos($technos, "G9")!==false) {
			$amount_in_totable_G9=copyGSMrep('G9',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['G9'],$type,$from,$to);	
		}
		if (strpos($technos, "G18")!==false) {
			$amount_in_totable_G18=copyGSMrep('G18',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['G18'],$type,$from,$to);	
		}
		if (strpos($technos, "U21")!==false) {
			$amount_in_totable_U21=copyUMTSrep('U21',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['U21'],$type,$from,$to);	
		}
		if (strpos($technos, "U9")!==false) {
			$amount_in_totable_U9=copyUMTSrep('U9',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['U9'],$type,$from,$to);	
		}
		if (strpos($technos, "L8")!==false) {
			$amount_in_totable_L8=copyLTErep('L8',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['L8'],$type,$from,$to);	
		}
		if (strpos($technos, "L18")!==false) {
			$amount_in_totable_L18=copyLTErep('L18',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['L18'],$type,$from,$to);	
		}
		if (strpos($technos, "L26")!==false) {
			$amount_in_totable_L26=copyLTErep('L26',$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode['L26'],$type,$from,$to);		
		}
	}
}


function copyGSM($techno,$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode_GSM,$type,$from,$to){
	global $conn_Infobase;
	$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_PL_GSM
	WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$to."' AND TECHNO='".$techno."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$amount_in_totable_GSM=$res1['AMOUNT'][0];
	}

	//echo "amount_in_totable".$amount_in_totable_GSM;
	if ($amount_in_totable_GSM==0){
		$query="INSERT INTO INFOBASE.BSDS_PL_GSM (BSDSKEY, CABTYPE,
		NR_OF_CAB, CDUTYPE, COMMENTS, BBS, DXUTYPE1, DXUTYPE2, DXUTYPE3,PLAYSTATION,BSDS_BOB_REFRESH,STATUS,TECHNO)
		SELECT
			BSDSKEY, CABTYPE, NR_OF_CAB,  CDUTYPE, COMMENTS, BBS, DXUTYPE1,DXUTYPE2,
			DXUTYPE3,PLAYSTATION,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh,'".$to."','".$techno."'
		FROM INFOBASE.BSDS_PL_GSM
		WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$from."' AND TECHNO='".$techno."'";
		if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
			$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
		}
		//echo $query."<hr>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);

		$query="DELETE FROM INFOBASE.BSDS_PL_GSM_SEC
		WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$to."' AND TECHNO='".$techno."' AND SECT IN('1','2','3','4')
		AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);

		$query="INSERT INTO INFOBASE.BSDS_PL_GSM_SEC (BSDSKEY, FREQ_ACTIVE1,
		TRU_INST1_1, TRU_TYPE1_1, TRU_INST1_2, TRU_TYPE1_2, CONFIG, STATE, TMA, AZI1, ANTTYPE1,
		ELECTILT1,MECHTILT1, MECHTILT_DIR1, ANTHEIGHT1,ANTTYPE2, ELECTILT2, MECHTILT2,
		MECHTILT_DIR2, ANTHEIGHT2, FEEDERLEN, FEEDER, COMB, DCBLOCK,BSDS_BOB_REFRESH,
		TRU_INST2_1, TRU_TYPE2_1, TRU_INST2_2, TRU_TYPE2_2,TRU_INST3_1, TRU_TYPE3_1, TRU_INST3_2, TRU_TYPE3_2,
		FREQ_ACTIVE2, FREQ_ACTIVE3, HR_ACTIVE,AZI2,STATUS,SECT,TECHNO)
		SELECT
			BSDSKEY, FREQ_ACTIVE1, TRU_INST1_1, TRU_TYPE1_1, TRU_INST1_2, TRU_TYPE1_2,
			CONFIG, STATE, TMA, AZI1,ANTTYPE1, ELECTILT1, MECHTILT1, MECHTILT_DIR1,
			ANTHEIGHT1, ANTTYPE2, ELECTILT2, MECHTILT2, MECHTILT_DIR2, ANTHEIGHT2,
			FEEDERLEN, FEEDER, COMB, DCBLOCK,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh,
			TRU_INST2_1, TRU_TYPE2_1, TRU_INST2_2, TRU_TYPE2_2,TRU_INST3_1, TRU_TYPE3_1, TRU_INST3_2,
			TRU_TYPE3_2, FREQ_ACTIVE2, FREQ_ACTIVE3, HR_ACTIVE,AZI2,'".$to."',SECT,'".$techno."'
		FROM INFOBASE.BSDS_PL_GSM_SEC
		WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$from."' AND TECHNO='".$techno."' AND SECT IN('1','2','3','4')";
		if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
			$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
		}
		//echo $query."<hr>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
	}

	if ($type=="BSDS FUNDED"){
		$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_CU_GSM
		WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' AND STATUS='FUND'
		AND TECHNO='".$techno."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_totable6=$res1['AMOUNT'][0];
		}
		if ($amount_in_totable6==0){
			$query="INSERT INTO INFOBASE.BSDS_CU_GSM (BSDSKEY, BSDS_BOB_REFRESH, SITEKEY, CHANGEDATE, FREQ_ACTIVE_1,
			   FREQ_ACTIVE_2, FREQ_ACTIVE_3, TRU_INST1_1, TRU_INST1_2, TRU_INST1_3, TRU_TYPE1_1,
			   TRU_TYPE1_2, TRU_TYPE1_3, TRU_INST2_1,TRU_INST2_2, TRU_INST2_3, TRU_TYPE2_1,
			   TRU_TYPE2_2, TRU_TYPE2_3, CABTYPE, NR_OF_CAB, CDUTYPE, TMA_1,
			   TMA_2, TMA_3, COMB_1, COMB_2, COMB_3, DCBLOCK_1, DCBLOCK_2, DCBLOCK_3,
			   BBS, DXUTYPE1, DXUTYPE2, DXUTYPE3,
			   FREQ_ACTIVE_4, TRU_INST1_4, TRU_TYPE1_4, TRU_INST2_4, TRU_TYPE2_4, TMA_4,COMB_4, DCBLOCK_4,
			   STATUS,TECHNO)
			SELECT
			   ".$BSDSKEY.",to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,SITEKEY, CHANGEDATE, FREQ_ACTIVE_1,
			   FREQ_ACTIVE_2, FREQ_ACTIVE_3, TRU_INST1_1,
			   TRU_INST1_2, TRU_INST1_3, TRU_TYPE1_1,   TRU_TYPE1_2, TRU_TYPE1_3, TRU_INST2_1,
			   TRU_INST2_2, TRU_INST2_3, TRU_TYPE2_1,  TRU_TYPE2_2, TRU_TYPE2_3, CABTYPE,
			   NR_OF_CAB, CDUTYPE, TMA_1, TMA_2, TMA_3, COMB_1, COMB_2, COMB_3, DCBLOCK_1, DCBLOCK_2, DCBLOCK_3,
			   BBS, DXUTYPE1, DXUTYPE2, DXUTYPE3, FREQ_ACTIVE_4, TRU_INST1_4, TRU_TYPE1_4,
			   TRU_INST2_4, TRU_TYPE2_4, TMA_4,COMB_4, DCBLOCK_4,'FUND','".$techno."'
			FROM INFOBASE.BSDS_CU_GSM
			WHERE SITEKEY = '". $lognode_GSM ."' AND STATUS='PRE' AND TECHNO='".$techno."'";
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
		}
	}
	//$message.= $type." HISTORY CREATED for ".$techno."!<br>";
	return $amount_in_totable_GSM;
}
function copyUMTS($techno,$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode_UMTS,$type,$from,$to){
	global $conn_Infobase;
	$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_PL_UMTS
	WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$to."' AND TECHNO='".$techno."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$amount_in_totable_UMTS=$res1['AMOUNT'][0];;
	}

	if ($amount_in_totable_UMTS==0){
		$query="INSERT INTO INFOBASE.BSDS_PL_UMTS (BSDSKEY, TECHNO_CHANGEDATE, POWERSUP,
			CABTYPE, IPB,PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS, COMMENTS, BSDS_BOB_REFRESH,RAXEHW,RAXESW,HSTXHW,
			HSTXSW,PLAYSTATION,SERVICE,STATUS,TECHNO,BPC,BPK,CC)
		SELECT
			BSDSKEY, TECHNO_CHANGEDATE, POWERSUP, CABTYPE, IPB, PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS,
			COMMENTS,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh,RAXEHW,RAXESW,HSTXHW,HSTXSW,PLAYSTATION,SERVICE,
			'".$to."','".$techno."',BPC,BPK,CC
		FROM INFOBASE.BSDS_PL_UMTS
		WHERE BSDSKEY LIKE '". $BSDSKEY ."' AND STATUS='".$from."' AND TECHNO='".$techno."'";
		if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
			$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
		}
		//echo $query."<hr>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);

		$query="DELETE FROM INFOBASE.BSDS_PL_UMTS_SEC
		WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$to."' AND TECHNO='".$techno."' AND SECT IN('1','2','3','4')
		AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);

		$query="INSERT INTO INFOBASE.BSDS_PL_UMTS_SEC (BSDSKEY, UMTSCELLID,
			UMTSCELLPK, TRU_INST1, TRU_INST2, FREQ_ACTIVE, MCPAMODE, MCPATYPE, ACS, RET, ANTHEIGHT1,
			AZI1, ANTTYPE1,ELECTILT1,MECHTILT1, MECHTILT_DIR1, FEEDER, FEEDERLEN, ANTTYPE2, ELECTILT2,
			MECHTILT2,MECHTILT_DIR2,ANTHEIGHT2, STATE, BSDS_BOB_REFRESH,AZI2,STATUS,SECT,TECHNO)
		SELECT
			BSDSKEY, UMTSCELLID, UMTSCELLPK, TRU_INST1, TRU_INST2, FREQ_ACTIVE, MCPAMODE, MCPATYPE,
			ACS, RET, ANTHEIGHT1, AZI1, ANTTYPE1, ELECTILT1, MECHTILT1, MECHTILT_DIR1, FEEDER,
			FEEDERLEN, ANTTYPE2, ELECTILT2, MECHTILT2, MECHTILT_DIR2, ANTHEIGHT2, STATE,
			to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh, AZI2,'".$to."',SECT,'".$techno."'
		FROM INFOBASE.BSDS_PL_UMTS_SEC
		WHERE BSDSKEY LIKE '".$BSDSKEY ."' AND STATUS='".$from."' AND TECHNO='".$techno."' AND SECT IN('1','2','3','4')";
		if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
			$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
		}
		//echo $query."<hr>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
		
	}
	if ($type=="BSDS FUNDED"){
		$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_CU_UMTS
		WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' AND STATUS='FUND'
		AND TECHNO='".$techno."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_totable=$res1['AMOUNT'][0];;
		}
		if ($amount_in_totable==0){
			$query="INSERT INTO INFOBASE.BSDS_CU_UMTS (BSDSKEY, BSDS_BOB_REFRESH,
			LOGNODEPK, LOGNODEID, CHANGE_DATE, POWERSUP, CABTYPE, IPB, PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS,
			RAXEHW, RAXESW, HSTXHW, HSTXSW,STATUS,TECHNO)
			SELECT
				".$BSDSKEY.",to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,
				LOGNODEPK, LOGNODEID, CHANGE_DATE, POWERSUP, CABTYPE, IPB, PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS,
				RAXEHW, RAXESW, HSTXHW, HSTXSW,'FUND','".$techno."'
			FROM INFOBASE.BSDS_CU_UMTS
			WHERE LOGNODEPK LIKE '". $lognode_UMTS ."'";
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
			OCICommit($conn_Infobase);

			$query="INSERT INTO INFOBASE.BSDS_CU_UMTS_SEC (BSDSKEY, BSDS_BOB_REFRESH,
				LOGNODEPK, UMTSCELLID, UMTSCELLPK,  TRU_INST1, TRU_INST2, FREQ_ACTIVE,
				MCPAMODE, MCPATYPE, ACS, RET,STATUS,SECT,TECHNO)
			SELECT
				".$BSDSKEY.",to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,
				LOGNODEPK, UMTSCELLID, UMTSCELLPK,  TRU_INST1, TRU_INST2, FREQ_ACTIVE,
				MCPAMODE, MCPATYPE, ACS, RET,'FUND',SECT,'".$techno."'
			FROM INFOBASE.BSDS_CU_UMTS_SEC
			WHERE LOGNODEPK LIKE '". $Sitekey ."' AND STATUS='PRE' AND TECHNO='".$techno."' AND SECT IN('1','2','3','4')";
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
			OCICommit($conn_Infobase);
		}
	}
	//$message.=$type." HISTORY CREATED for ".$techno."!<br>";
	return $amount_in_totable_UMTS;
}
function copyLTE($techno,$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode_LTE,$type,$from,$to){
	global $conn_Infobase;
	$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_PL_LTE
	WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') 
	AND BSDSKEY = '". $BSDSKEY ."'  AND STATUS='".$to."' AND TECHNO='".$techno."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$amount_in_totable_LTE=$res1['AMOUNT'][0];;
	}
	if ($amount_in_totable_LTE==0){

		$query="INSERT INTO INFOBASE.BSDS_PL_LTE (BSDSKEY, TECHNO_CHANGEDATE, POWERSUP,
			CABTYPE, IPB,PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS, 
			COMMENTS, BSDS_BOB_REFRESH,RAXEHW,RAXESW,HSTXHW,HSTXSW,
			PLAYSTATION,SERVICE,STATUS,TECHNO,BPL)
		SELECT
			BSDSKEY, TECHNO_CHANGEDATE, POWERSUP, CABTYPE, IPB, PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS,
			COMMENTS,to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh,RAXEHW,RAXESW,HSTXHW,HSTXSW,
			PLAYSTATION,SERVICE,'".$to."','".$techno."',BPL
		FROM INFOBASE.BSDS_PL_LTE
		WHERE BSDSKEY LIKE '". $BSDSKEY ."' AND STATUS='".$from."' AND TECHNO='".$techno."'";
		if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
			$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
		}
		//echo $query."<hr>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
		
		$query="DELETE FROM INFOBASE.BSDS_PL_LTE_SEC
		WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$to."' AND TECHNO='".$techno."' AND SECT IN('1','2','3','4')
		AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
		//echo $query;
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);

		$query="INSERT INTO INFOBASE.BSDS_PL_LTE_SEC (
			BSDSKEY, UMTSCELLID, UMTSCELLPK, TRU_INST1, TRU_INST2, FREQ_ACTIVE, MCPAMODE, MCPATYPE, ACS, 
			RET, ANTHEIGHT1,AZI1, ANTTYPE1,ELECTILT1,MECHTILT1, MECHTILT_DIR1, FEEDER, FEEDERLEN, ANTTYPE2, 
			ELECTILT2, MECHTILT2,MECHTILT_DIR2,ANTHEIGHT2, STATE, AZI2,BSDS_BOB_REFRESH,STATUS,SECT,TECHNO)
		SELECT
			BSDSKEY, UMTSCELLID, UMTSCELLPK, TRU_INST1, TRU_INST2, FREQ_ACTIVE, MCPAMODE, MCPATYPE,	ACS,
			 RET, ANTHEIGHT1, AZI1, ANTTYPE1, ELECTILT1, MECHTILT1, MECHTILT_DIR1, FEEDER,FEEDERLEN, ANTTYPE2,
			 ELECTILT2, MECHTILT2, MECHTILT_DIR2, ANTHEIGHT2, STATE,AZI2,to_date('".$BSDS_BOB_REFRESH."'),
			 '".$to."',SECT,'".$techno."' 
			 AS BSDS_BOB_refresh
		FROM INFOBASE.BSDS_PL_LTE_SEC
		WHERE BSDSKEY LIKE '".$BSDSKEY."' AND STATUS='".$from."' AND TECHNO='".$techno."' AND SECT IN('1','2','3','4')";
		if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
			$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
		}
		//echo $query."<hr>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
	
	}

	if ($type=="BSDS FUNDED"){
		$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_CU_UMTS
		WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' AND STATUS='FUND'
		AND TECHNO='".$techno."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_totable=$res1['AMOUNT'][0];;
		}
		if ($amount_in_totable==0){
			$query="INSERT INTO INFOBASE.BSDS_CU_LTE (BSDSKEY, BSDS_BOB_REFRESH,
			LOGNODEPK, LOGNODEID, CHANGE_DATE, POWERSUP, CABTYPE, IPB, PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS,
			RAXEHW, RAXESW, HSTXHW, HSTXSW,STATUS,TECHNO)
			SELECT
				".$BSDSKEY.",to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,
				LOGNODEPK, LOGNODEID, CHANGE_DATE, POWERSUP, CABTYPE, IPB, PSU, TXBHW, TXBSW, RAXBHW, RAXBSW, MBPS,
				RAXEHW, RAXESW, HSTXHW, HSTXSW,'FUND','".$techno."'
			FROM INFOBASE.BSDS_CU_LTE
			WHERE LOGNODEPK LIKE '". $lognode_LTE ."'";
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
			OCICommit($conn_Infobase);

			$query="INSERT INTO INFOBASE.BSDS_CU_LTE_SEC (BSDSKEY, BSDS_BOB_REFRESH,
				LOGNODEPK, UMTSCELLID, UMTSCELLPK,  TRU_INST1, TRU_INST2, FREQ_ACTIVE,
				MCPAMODE, MCPATYPE, ACS, RET,STATUS,SECT,TECHNO)
			SELECT
				".$BSDSKEY.",to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,
				LOGNODEPK, UMTSCELLID, UMTSCELLPK,  TRU_INST1, TRU_INST2, FREQ_ACTIVE,
				MCPAMODE, MCPATYPE, ACS, RET,'FUND',SECT,'".$techno."'
			FROM INFOBASE.BSDS_CU_LTE_SEC
			WHERE LOGNODEPK LIKE '". $lognode_LTE ."' AND STATUS='PRE' AND TECHNO='".$techno."' AND SECT IN('1','2','3','4')";
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
			OCICommit($conn_Infobase);
		}
	}
	//$message.=$type." HISTORY CREATED for ".$techno."!<br>";
	return $amount_in_totable_GSM;
}
/*****************************************************************************************************************************/
function copyGSMrep($techno,$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode_GSM,$type,$from,$to){
	global $conn_Infobase;
	$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_PL_REP_GSM
	WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$to."' AND TECHNO='".$techno."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$amount_in_totable_GSM=$res1['AMOUNT'][0];
	}

	if ($amount_in_totable_GSM==0){
		$query="INSERT INTO INFOBASE.BSDS_PL_REP_GSM (BSDSKEY,OWNER,BRAND,TYPE,
		TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,BSDS_BOB_REFRESH,STATUS,TECHNO)
		SELECT
			BSDSKEY,OWNER,BRAND,TYPE,TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,
			to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh,'".$to."','".$techno."'
		FROM INFOBASE.BSDS_PL_REP_GSM
		WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$from."' AND TECHNO='".$techno."'";
		if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
			$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
		}
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
	}
	if ($type=="BSDS FUNDED"){
		$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_CU_REP_GSM
		WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' 
		AND STATUS='FUND' AND TECHNO='".$techno."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_totable6=$res1['AMOUNT'][0];
		}
		//echo "amount_in_totable".$amount_in_totable;
		if ($amount_in_totable6==0){

			$query="INSERT INTO INFOBASE.BSDS_CU_REP_GSM (BSDSKEY,BSDS_BOB_REFRESH,SITEKEY,
			CHANGE_DATE,OWNER,BRAND,TYPE,TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,STATUS,TECHNO)
			SELECT
			   ".$BSDSKEY.",to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,SITEKEY, CHANGE_DATE,
			   OWNER,BRAND,TYPE,TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,'FUND','".$techno."'
			FROM INFOBASE.BSDS_CU_REP_GSM
			WHERE SITEKEY = '". $lognode_GSM ."' AND STATUS='PRE' AND TECHNO='".$techno."'";
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
			OCICommit($conn_Infobase);
		}
	}
	return $amount_in_totable_GSM;
}
function copyUMTSrep($techno,$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode_UMTS,$type,$from,$to){
	global $conn_Infobase;
	$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_PL_REP_UMTS
	WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$amount_in_totable_UMTS=$res1['AMOUNT'][0];
	}

	if ($amount_in_totable_UMTS==0){
		$query="INSERT INTO INFOBASE.BSDS_PL_REP_UMTS (BSDSKEY,OWNER,BRAND,TYPE,
		TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,BSDS_BOB_REFRESH,STATUS,TECHNO)
		SELECT
			BSDSKEY,OWNER,BRAND,TYPE,TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,
			to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh,'".$to."','".$techno."'
		FROM INFOBASE.BSDS_PL_REP_UMTS
		WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$from."' AND TECHNO='".$techno."'";
		if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
			$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
		}
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
	}
	if ($type=="BSDS FUNDED"){
		$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_CU_REP_UMTS
		WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' 
		AND STATUS='FUND' AND TECHNO='".$techno."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_totable6=$res1['AMOUNT'][0];
		}
		//echo "amount_in_totable".$amount_in_totable;
		if ($amount_in_totable6==0){

			$query="INSERT INTO INFOBASE.BSDS_CU_REP_UMTS (BSDSKEY,BSDS_BOB_REFRESH,SITEKEY,
			CHANGE_DATE,OWNER,BRAND,TYPE,TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,STATUS,TECHNO)
			SELECT
			   ".$BSDSKEY.",to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,SITEKEY, CHANGE_DATE,
			   OWNER,BRAND,TYPE,TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,'FUND','".$techno."'
			FROM INFOBASE.BSDS_CU_REP_UMTS
			WHERE SITEKEY = '". $lognode_UMTS ."' AND STATUS='PRE' AND TECHNO='".$techno."'";
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
			OCICommit($conn_Infobase);
		}
	}
	return $amount_in_totable_UMTS;
}
function copyLTErep($techno,$BSDSKEY,$BSDS_BOB_REFRESH,$BOB_REFRESH_DATE_previous_status,$lognode_LTE,$type,$from,$to){
	global $conn_Infobase;
	$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_PL_REP_LTE
	WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$amount_in_totable_LTE=$res1['AMOUNT'][0];
	}

	if ($amount_in_totable_LTE==0){
		$query="INSERT INTO INFOBASE.BSDS_PL_REP_LTE (BSDSKEY,OWNER,BRAND,TYPE,
		TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,BSDS_BOB_REFRESH,STATUS,TECHNO)
		SELECT
			BSDSKEY,OWNER,BRAND,TYPE,TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,
			to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_refresh,'".$to."','".$techno."'
		FROM INFOBASE.BSDS_PL_REP_LTE
		WHERE BSDSKEY = '". $BSDSKEY ."' AND STATUS='".$from."' AND TECHNO='".$techno."'";
		if ($type!="SITE FUNDED" && $type!="BSDS AS BUILD SPECIAL"  && $type!="BSDS FUNDED SPECIAL"){
			$query.=" AND BSDS_BOB_REFRESH=to_date('".$BOB_REFRESH_DATE_previous_status."')";
		}
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
	}
	if ($type=="BSDS FUNDED"){
		$query="SELECT count(BSDSKEY) as AMOUNT FROM INFOBASE.BSDS_CU_REP_LTE
		WHERE BSDS_BOB_refresh =to_date('".$BSDS_BOB_REFRESH."') AND BSDSKEY = '". $BSDSKEY ."' 
		AND STATUS='FUND' AND TECHNO='".$techno."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$amount_in_totable6=$res1['AMOUNT'][0];
		}
		//echo "amount_in_totable".$amount_in_totable;
		if ($amount_in_totable6==0){

			$query="INSERT INTO INFOBASE.BSDS_CU_REP_LTE (BSDSKEY,BSDS_BOB_REFRESH,SITEKEY,
			CHANGE_DATE,OWNER,BRAND,TYPE,TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,STATUS,TECHNO)
			SELECT
			   ".$BSDSKEY.",to_date('".$BSDS_BOB_REFRESH."') AS BSDS_BOB_REFRESH,SITEKEY, CHANGE_DATE,
			   OWNER,BRAND,TYPE,TECHNOLOGY,CHANNEL,PICKUP,DISTRIB,COSP,COMMENTS,'FUND','".$techno."'
			FROM INFOBASE.BSDS_CU_REP_LTE
			WHERE SITEKEY = '". $lognode_LTE ."' AND STATUS='PRE' AND TECHNO='".$techno."'";
			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}
			OCICommit($conn_Infobase);
		}
	}
	return $amount_in_totable_LTE;
}

<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
include("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


if ($_POST['NR_OF_CAB']=="Unknown"){
	$warning.="PLANNED data could not be saved because of errors!<br>";
	$warning.="Nr of CABS mut be different than 'Unknown'!<br>";
}else if ($_POST['bsdskey']!=""){

/*************************
// CURRENT SAVE OR UPDATE
*************************/
	if ($_POST['onlyfeeder']!="yes"){ //We don't save current when only feeder data is updated/inserted
		$check_current_exists=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['createddate'],'',$_POST['donor'],$_POST['frozen'],$_POST['candidate']);
		
		if ($check_current_exists=="0"){
			//ONly a current PRE(=not frozen) can be inserted or updated. (FOR POST => BSDS funded/FROZEN can not be updated)
			//we also update BSDS_NOB_REFRESH to created_date so we know which BSDS has last updated the current info
			$query = "INSERT INTO BSDS_CU_GEN
				VALUES ('".$_POST['bsdskey']."','".$_POST['candidate']."','0',SYSDATE,'".$_POST['createddate']."','".$_POST['band']."',
				'".$_POST['CDUTYPE']."','".$_POST['BBS']."','".$_POST['CABTYPE']."','".$_POST['NR_OF_CAB']."',
				'".$_POST['DXUTYPE1']."','".$_POST['DXUTYPE2']."','".$_POST['DXUTYPE3']."','".$_POST['PLAYSTATION']."','".$_POST['POWERSUP']."',
				'".$_POST['IPB']."','".$_POST['PSU']."','".$_POST['TXBHW']."', '".$_POST['TXBSW']."', '".$_POST['RAXBHW']."','".$_POST['RAXBSW']."',
				'".$_POST['MBPS']."', '".$_POST['RAXEHW']."', '".$_POST['RAXESW']."', '".$_POST['HSTXHW']."','".$_POST['HSTXSW']."','".$_POST['SERVICE']."',
				'".$_POST['BPC']."','".$_POST['BPK']."','".$_POST['CC']."','".$_POST['BPN2']."','".$_POST['PM0']."','".$_POST['FS5']."','".$_POST['RECT']."','".$_POST['BPL']."')";
				$action="saved";
		}else{
			$query = "UPDATE BSDS_CU_GEN SET
			CHANGEDATE = SYSDATE,
			BSDSKEY='".$_POST['bsdskey']."',
			CABTYPE='".$_POST['CABTYPE']."',
			NR_OF_CAB='".$_POST['NR_OF_CAB']."',
			CDUTYPE='".$_POST['CDUTYPE']."',
			BBS= '".$_POST['BBS']."',
			DXUTYPE1='".$_POST['DXUTYPE1']."',
			DXUTYPE2='".$_POST['DXUTYPE2']."',
			DXUTYPE3='".$_POST['DXUTYPE3']."',
			PLAYSTATION='".$_POST['PLAYSTATION']."',
			IPB='".$_POST['IPB']."',
			PSU='".$_POST['PSU']."',
			TXBHW='".$_POST['TXBHW']."',
			TXBSW='".$_POST['TXBSW']."',
			RAXBHW='".$_POST['RAXBHW']."',
			RAXBSW='".$_POST['RAXBSW']."',
			MBPS='".$_POST['MBPS']."',
			RAXEHW='".$_POST['RAXEHW']."',
			RAXESW='".$_POST['RAXESW']."',
			HSTXHW='".$_POST['HSTXHW']."',
			HSTXSW='".$_POST['HSTXSW']."',
			SERVICE='".$_POST['SERVICE']."',
			BPC='".$_POST['BPC']."',
			BPK='".$_POST['BPK']."',
			CC='".$_POST['CC']."',
			BPN2='".$_POST['BPN2']."',
			PM0='".$_POST['PM0']."',
			FS5='".$_POST['FS5']."',
			RECT='".$_POST['RECT']."',
			BPL='".$_POST['BPL']."'
			WHERE SITEKEY= '".$_POST['candidate']."' AND STATUS='0' AND TECHNO='".$_POST['band']."'";
			
			$action="updated";
		}
		//echo $query."<br>";

		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
			$message="CURRENT DATA '".$action."' for ".$_POST['band']."!<br>";
		}


		for ($n = 1; $n <= 6; $n++) {
			require("height_conversion_concat.php");
			$check_current_exists_SECT=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['createddate'],$n,$_POST['donor'],$_POST['frozen'],$_POST['candidate']);

			if ($check_current_exists_SECT=="0"){
				$query2 = "INSERT INTO BSDS_CU_SEC
				VALUES ('".$_POST['bsdskey']."','".$_POST['candidate']."','0',SYSDATE,'".$_POST['createddate']."','".$_POST['band']."','".$n."',
				'".$_POST['CONFIG_'.$n]."','".$_POST['TMA_'.$n]."','".$_POST['FREQ_ACTIVE1_'.$n]."','".$_POST['FREQ_ACTIVE2_'.$n]."','".$_POST['FREQ_ACTIVE3_'.$n]."',
				'".$_POST['TRU_INST1_1_'.$n]."','".$_POST['TRU_INST1_2_'.$n]."','".$_POST['TRU_INST2_1_'.$n]."','".$_POST['TRU_INST2_2_'.$n]."',
				'".$_POST['TRU_INST3_1_'.$n]."','".$_POST['TRU_INST3_2_'.$n]."','".$_POST['TRU_TYPE1_1_'.$n]."','".$_POST['TRU_TYPE1_2_'.$n]."',
				'".$_POST['TRU_TYPE2_1_'.$n]."','".$_POST['TRU_TYPE2_2_'.$n]."','".$_POST['TRU_TYPE3_1_'.$n]."','".$_POST['TRU_TYPE3_2_'.$n]."',
				'".$_POST['ANTTYPE1_'.$n]."','".$_POST['ANTTYPE2_'.$n]."','".$_POST['ELECTILT1_'.$n]."','".$_POST['ELECTILT2_'.$n]."',
				'".$_POST['MECHTILT1_'.$n]."','".$_POST['MECHTILT2_'.$n]."','".$_POST['MECHTILT_DIR1_'.$n]."','".$_POST['MECHTILT_DIR2_'.$n]."',
				'".$_POST['ANTHEIGHT1_'.$n]."','".$_POST['ANTHEIGHT2_'.$n]."',
				'".$_POST['AZI1_'.$n]."','".$_POST['AZI2_'.$n]."','".$_POST['FEEDER_'.$n]."','".$_POST['FEEDERLEN_'.$n]."','".$_POST['DCBLOCK_'.$n]."',
				'".$_POST['COMB_'.$n]."','".$_POST['HR_ACTIVE_'.$n]."',
				'".$_POST['MCPAMODE_'.$n]."','".$_POST['MCPATYPE_'.$n]."','".$_POST['ACS_'.$n]."', '".$_POST['RET_'.$n]."')";
				$action="inserted";
			}else{
				$query2 = "UPDATE BSDS_CU_SEC SET
				 CHANGEDATE = SYSDATE,
				 BSDSKEY='".$_POST['bsdskey']."',
				 BSDS_BOB_REFRESH='".$_POST['createddate']."',
				 CONFIG='".$_POST['CONFIG_'.$n]."',
				 TMA='".$_POST['TMA_'.$n]."',
				 FREQ_ACTIVE1='".$_POST['FREQ_ACTIVE1_'.$n]."',
				 FREQ_ACTIVE2='".$_POST['FREQ_ACTIVE2_'.$n]."',
				 FREQ_ACTIVE3='".$_POST['FREQ_ACTIVE3_'.$n]."',
				 TRU_INST1_1='".$_POST['TRU_INST1_1_'.$n]."',
				 TRU_INST1_2='".$_POST['TRU_INST1_2_'.$n]."',
				 TRU_INST2_1='".$_POST['TRU_INST2_1_'.$n]."',
				 TRU_INST2_2='".$_POST['TRU_INST2_2_'.$n]."',
				 TRU_INST3_1='".$_POST['TRU_INST3_1_'.$n]."',
				 TRU_INST3_2='".$_POST['TRU_INST3_2_'.$n]."',
				 TRU_TYPE1_1='".$_POST['TRU_TYPE1_1_'.$n]."',
				 TRU_TYPE1_2='".$_POST['TRU_TYPE1_2_'.$n]."',
				 TRU_TYPE2_1='".$_POST['TRU_TYPE2_1_'.$n]."',
				 TRU_TYPE2_2='".$_POST['TRU_TYPE2_2_'.$n]."',
				 TRU_TYPE3_1='".$_POST['TRU_TYPE3_1_'.$n]."',
				 TRU_TYPE3_2='".$_POST['TRU_TYPE3_2_'.$n]."',
				 ANTTYPE1='".$_POST['ANTTYPE1_'.$n]."',
				 ANTTYPE2='".$_POST['ANTTYPE2_'.$n]."',
				 ELECTILT1='".$_POST['ELECTILT1_'.$n]."',
				 ELECTILT2='".$_POST['ELECTILT2_'.$n]."',
				 MECHTILT1='".$_POST['MECHTILT1_'.$n]."',
				 MECHTILT2='".$_POST['MECHTILT2_'.$n]."',
				 MECHTILT_DIR1='".$_POST['MECHTILT_DIR1_'.$n]."',
				 MECHTILT_DIR2='".$_POST['MECHTILT_DIR2_'.$n]."',
				 ANTHEIGHT1='".$_POST['ANTHEIGHT1_'.$n]."',
				 ANTHEIGHT2='".$_POST['ANTHEIGHT2_'.$n]."',
				 AZI1='".$_POST['AZI1_'.$n]."',
				 AZI2='".$_POST['AZI2_'.$n]."',
				 FEEDER='".$_POST['FEEDER_'.$n]."',
				 FEEDERLEN='".$_POST['FEEDERLEN_'.$n]."',
				 DCBLOCK='".$_POST['DCBLOCK_'.$n]."',
				 COMB='".$_POST['COMB_'.$n]."',
				 HR_ACTIVE='".$_POST['HR_ACTIVE_'.$n]."',
				 MCPAMODE='".$_POST['MCPAMODE_'.$n]."',
				 MCPATYPE='".$_POST['MCPATYPE_'.$n]."',
				 ACS='".$_POST['ACS_'.$n]."',
				 RET='".$_POST['RET_'.$n]."'
				 WHERE SITEKEY= '".$_POST['candidate']."' AND STATUS='PRE' AND TECHNO='".$_POST['band']."' AND SECT='".$n."'";
				 $action="updated";
			}
			//echo $query2;
			//die;
			$stmt2 = parse_exec_free($conn_Infobase, $query2, $error_str);
			if (!$stmt2) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
				$sectors.=$n."&nbsp;";
			}
		}
		//echo "----";
		$message.="CURRENT DATA '".$action."' for ".$_POST['band']." sectors ".$sectors."!<br>";
		//UPDATE CURRENT FEEDERSHARE DATA
		$feedershare_Count=check_feedershare_exists("CURRENT",$_POST['frozen'],$_POST['candidate'],$_POST['createddate'],$_POST['candidate']);

		for ($i = 1; $i <= 6; $i++) { //foreach sector

			$FEEDERSHARE_temp="FEEDERSHARE_".$i;

			if ($_POST['band']=="G9"){
				$query4_update .= "GSM900_$i='".$_POST[$FEEDERSHARE_temp]."',";
				$FEEDERSHARE_GSM900=$_POST[$FEEDERSHARE_temp];
			}else{
				$FEEDERSHARE_GSM900="";
			}
			if ($_POST['band']=="G18"){
				$query4_update .= "GSM1800_$i='".$_POST[$FEEDERSHARE_temp]."',";
				$FEEDERSHARE_GSM1800=$_POST[$FEEDERSHARE_temp];
			}else{
				$FEEDERSHARE_GSM1800="";
			}
			if ($_POST['band']=="U9"){
				$query4_update .= "UMTS900_$i='".$_POST[$FEEDERSHARE_temp]."',";
				$FEEDERSHARE_UMTS900=$_POST[$FEEDERSHARE_temp];
			}else{
				$FEEDERSHARE_UMTS900="";
			}
			if ($_POST['band']=="U21"){
				$query4_update .= "UMTS2100_$i='".$_POST[$FEEDERSHARE_temp]."',";
				$FEEDERSHARE_UMTS2100=$_POST[$FEEDERSHARE_temp];
			}else{
				$FEEDERSHARE_UMTS2100="";
			}
			if ($_POST['band']=="L8"){
				$query4_update .= "LTE800_$i='".$_POST[$FEEDERSHARE_temp]."',";
				$FEEDERSHARE_LTE800=$_POST[$FEEDERSHARE_temp];
			}else{
				$FEEDERSHARE_LTE800="";
			}
			if ($_POST['band']=="L18"){
				$query4_update .= "LTE1800_$i='".$_POST[$FEEDERSHARE_temp]."',";
				$FEEDERSHARE_LTE1800=$_POST[$FEEDERSHARE_temp];
			}else{
				$FEEDERSHARE_LTE1800="";
			}
			if ($_POST['band']=="L26"){
				$query4_update .= "LTE2600_$i='".$_POST[$FEEDERSHARE_temp]."',";
				$FEEDERSHARE_LTE2600=$_POST[$FEEDERSHARE_temp];
			}else{
				$FEEDERSHARE_LTE2600="";
			}
			$query4_new .= "'".$FEEDERSHARE_GSM900."','".$FEEDERSHARE_GSM1800."',
			'".$FEEDERSHARE_UMTS900."','".$UMTS2100_data."','".$FEEDERSHARE_LTE800."',
			'".$FEEDERSHARE_LTE1800."','".$FEEDERSHARE_LTE2600."',";
		}

		if ($feedershare_Count=="0"){
			$query4 = "INSERT INTO BSDS_CU2 VALUES ('".$_POST['bsdskey']."','".$_POST['createddate']."',";
			$query4 .= $query4_new;
			$query4 .= "'".$_POST['frozen']."','".$_POST['candidate']."')";
			//echo $query4;
			$action="inserted";
		}else if ($feedershare_Count=="1"){
			$query4 = "UPDATE BSDS_CU2 SET ";
			$query4 .= substr($query4_update,0,-1);
			$query4 .=" WHERE SITEKEY='".$_POST['candidate']."' AND STATUS='".$_POST['frozen']."'";
			
			$action="updated";
		}
		if ($query4_update!="" or $query4_new!=""){
			//echo $query4;
			$stmt4 = parse_exec_free($conn_Infobase, $query4, $error_str);
			if (!$stmt4) {
				die_silently($conn_Infobase, $error_str);
			}else{
				//echo $feedershare_Count."=> $query4 <=";
				$message.="CUR FEEDER SHARE '".$action."' for ".$_POST['band']."!<br>";
			}
			OCICommit($conn_Infobase);
		}

	}
/*************************
// PLANNED SAVE OR UPDATE
*************************/
	
	if ($pl_is_BSDS_accepted!="Accepted"){
		$checktype="pl_";
		require("../checks/checks_proc.php");

		if(empty($ERROR_MESSAGE)){

			if ($_POST['onlyfeeder']!="yes"){
				for ($n = 1; $n <= 6; $n++) {  // VARAIBELE VARIABLES !!!
								
					require("height_conversion_concat.php");

					$pl_Count="";
					$pl_Count=check_planned_exists($_POST['bsdskey'],$_POST['createddate'],$_POST['band'],$n,$_POST['frozen'],$_POST['donor']);
					//echo "$n pl_Count $pl_Count<br>";

					// INSERT OR UPDATE THE BSDSDATA
					if ($pl_Count=="error"){
						$query3= "DELETE FROM BSDS_PL_SEC
						WHERE BSDSKEY= '".$_POST['bsdskey']."' AND STATUS='".$_POST['frozen']."' AND SECT='".$n."' AND TECHNO='".$_POST['band']."'";
						if ($_POST['frozen']=="POST" || $_POST['frozen']=="FUND"  || $_POST['frozen']=="BUILD"){ //build only for admin
							$query3.=" AND BSDS_BOB_REFRESH=to_date('".$_POST['createddate']."')";
						}
						//echo $query3;
						$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
						if (!$stmt3) {
							die_silently($conn_Infobase, $error_str);
						}
						OCICommit($conn_Infobase);
					}
					if ($pl_Count=="error" or $pl_Count==0){
						$query2 = "INSERT INTO BSDS_PL_SEC VALUES ('".$_POST['bsdskey']."',
						'".$_POST["pl_FREQ_ACTIVE1_".$n]."','".$_POST["pl_TRU_INST1_1_".$n]."','".$_POST["pl_TRU_TYPE1_1_".$n]."',	
						'".$_POST["pl_TRU_INST1_2_".$n]."',	'".$_POST["pl_TRU_TYPE1_2_".$n]."', '".$_POST["pl_CONFIG_".$n]."',
						'".$_POST["pl_STATE_".$n]."','".$_POST["pl_TMA_".$n]."', '".$_POST["pl_AZI1_".$n]."', 
						'".$_POST["pl_ANTTYPE1_".$n]."', '".$_POST["pl_ELECTILT1_".$n]."',	'".$_POST["pl_MECHTILT1_".$n]."',
						'".$_POST["pl_MECHTILT_DIR1_".$n]."', '".$_POST["pl_ANTHEIGHT1_".$n]."','".$_POST["pl_ANTTYPE2_".$n]."', 
						'".$_POST["pl_ELECTILT2_".$n]."', '".$_POST["pl_MECHTILT2_".$n]."', '".$_POST["pl_MECHTILT_DIR2_".$n]."',
						'".$_POST["pl_ANTHEIGHT2_".$n]."', '".$_POST["pl_FEEDERLEN_".$n]."','".$_POST["pl_FEEDER_".$n]."', 
						'".$_POST["pl_COMB_".$n]."','".$_POST["pl_DCBLOCK_".$n]."','".$_POST["pl_TRU_INST2_1_".$n]."', 
						'".$_POST["pl_TRU_TYPE2_1_".$n]."', '".$_POST["pl_TRU_INST2_2_".$n]."','".$_POST["pl_TRU_TYPE2_2_".$n]."',
						'".$_POST["l_TRU_INST3_1_".$n]."', '". $_POST["pl_TRU_TYPE3_1_".$n]."','". $_POST["pl_TRU_INST3_2_".$n]."',
						'". $_POST["pl_TRU_TYPE3_2_".$n]."','".$_POST["pl_FREQ_ACTIVE2_".$n]."','". $_POST["pl_FREQ_ACTIVE3_".$n]."', 
						'". $_POST["pl_HR_ACTIVE_".$n]."', '".$_POST["pl_AZI2_".$n]."',
						'".$_POST['band']."','".$n."','".$_POST['frozen']."','".$_POST['createddate']."',
						'".$_POST['MCPAMODE_'.$n]."','".$_POST['MCPATYPE_'.$n]."','".$_POST['ACS_'.$n]."', '".$_POST['RET_'.$n]."')";
						$action="saved";

					}else if ($pl_Count=="1"){
						$query2 = "UPDATE BSDS_PL_SEC
						SET
						FREQ_ACTIVE1='".$_POST["pl_FREQ_ACTIVE1_".$n]."',
						FREQ_ACTIVE2='".$_POST["pl_FREQ_ACTIVE2_".$n]."',
						FREQ_ACTIVE3='".$_POST["pl_FREQ_ACTIVE3_".$n]."',
						TRU_INST1_1='".$_POST["pl_TRU_INST1_1_".$n]."',
						TRU_TYPE1_1='".$_POST["pl_TRU_TYPE1_1_".$n]."',
						TRU_INST1_2='".$_POST["pl_TRU_INST1_2_".$n]."',
						TRU_TYPE1_2='".$_POST["pl_TRU_TYPE1_2_".$n]."',
						TRU_INST2_1='".$_POST["pl_TRU_INST2_1_".$n]."',
						TRU_TYPE2_1='".$_POST["pl_TRU_TYPE2_1_".$n]."',
						TRU_INST2_2='".$_POST["pl_TRU_INST2_2_".$n]."',
						TRU_TYPE2_2='".$_POST["pl_TRU_TYPE2_2_".$n]."',
						TRU_INST3_1='".$_POST["pl_TRU_INST3_1_".$n]."',
						TRU_TYPE3_1='".$_POST["pl_TRU_TYPE3_1_".$n]."',
						TRU_INST3_2='".$_POST["pl_TRU_INST3_2_".$n]."',
						TRU_TYPE3_2='".$_POST["pl_TRU_TYPE3_2_".$n]."',
						CONFIG='".$_POST["pl_CONFIG_".$n]."',
						STATE='".$_POST["pl_STATE_".$n]."',
						TMA='".$_POST["pl_TMA_".$n]."',
						AZI1='".$_POST["pl_AZI1_".$n]."',
						ANTTYPE1='".$_POST["pl_ANTTYPE1_".$n]."',
						ELECTILT1='".$_POST["pl_ELECTILT1_".$n]."',
						MECHTILT1='".$_POST["pl_MECHTILT1_".$n]."',
						MECHTILT_DIR1='".$_POST["pl_MECHTILT_DIR1_".$n]."',
						ANTHEIGHT1='".$_POST["pl_ANTHEIGHT1_".$n]."',
						ANTTYPE2='".$_POST["pl_ANTTYPE2_".$n]."',
						ELECTILT2='".$_POST["pl_ELECTILT2_".$n]."',
						MECHTILT2='".$_POST["pl_MECHTILT2_".$n]."',
						MECHTILT_DIR2='".$_POST["pl_MECHTILT_DIR2_".$n]."',
						ANTHEIGHT2='".$_POST["pl_ANTHEIGHT2_".$n]."',
						FEEDERLEN='".$_POST["pl_FEEDERLEN_".$n]."',
						FEEDER='".$_POST["pl_FEEDER_".$n]."',
						COMB='".$_POST["pl_COMB_".$n]."',
						DCBLOCK='".$_POST["pl_DCBLOCK_".$n]."',
						HR_ACTIVE='".$_POST["pl_HR_ACTIVE_".$n]."',
						AZI2='".$_POST["pl_AZI2_".$n]."',
						MCPAMODE='".$_POST['pl_MCPAMODE_'.$n]."',
						MCPATYPE='".$_POST['pl_MCPATYPE_'.$n]."',
						ACS='".$_POST['pl_ACS_'.$n]."',
						RET='".$_POST['pl_RET_'.$n]."'
						WHERE BSDSKEY= '".$_POST['bsdskey']."'
						AND SECT='".$n."' AND TECHNO='".$_POST['band']."' AND STATUS='".$_POST['frozen']."'
						AND BSDS_BOB_REFRESH='".$_POST['createddate']."'";
						
						$action="updated";
					}

					//echo $query2."<br>";
					$stmt2 = parse_exec_free($conn_Infobase, $query2, $error_str);
					if (!$stmt2) {
						die_silently($conn_Infobase, $error_str);
					}else{
						$sectors.=$n."&nbsp;";
					}
					OCICommit($conn_Infobase);

				}// END for ($n = 1; $n <= 4; $n++) {  // VARAIBELE VARIABLES !!! END for 3 sectors
				$message.="PLANNED DATA '".$action."' for ".$_POST['band']." sectors ".$sectors."!<br>";

				//UPDATE THE GENERAL INFO DATA
				$pl_Count=check_planned_exists($_POST['bsdskey'],$_POST['createddate'],$_POST['band'],'allsec',$_POST['frozen'],$_POST['donor']);
				$pl_COMMENTS=str_replace("'","''",$_POST['pl_COMMENTS']);

				if ($pl_Count=="0" || $pl_Count==""){
					$query3 = "INSERT INTO BSDS_PL_GEN VALUES ('".$_POST['bsdskey']."','".$_POST['pl_CABTYPE']."','".$_POST['pl_NR_OF_CAB']."',
					'".$_POST['pl_CDUTYPE']."','".$pl_COMMENTS."','".$_POST['pl_BBS']."',
					'".$_POST['pl_DXUTYPE1']."','".$_POST['pl_DXUTYPE2']."','".$_POST['pl_DXUTYPE3']."',
					'".$_POST['PLAYSTATION']."','".$_POST['band']."','".$_POST['frozen']."',
					'".$_POST['createddate']."',SYSDATE,'".$_POST['pl_POWERSUP']."',
					'".$_POST['pl_IPB']."','".$_POST['pl_PSU']."', '".$_POST['pl_TXBHW']."', '".$_POST['pl_TXBSW']."','".$_POST['pl_MBPS']."',
					'".$_POST['RAXEHW']."', '".$_POST['RAXESW']."', '".$_POST['HSTXHW']."', '".$_POST['HSTXSW']."',
				 	'".$_POST['SERVICE']."','".$_POST['pl_BPC']."','".$_POST['pl_BPK']."','".$_POST['pl_CC']."','".$_POST['pl_BPN2']."',
				 	'".$_POST['pl_PM0']."','".$_POST['pl_FS5']."','".$_POST['pl_RECT']."','".$_POST['pl_BPL']."',
				 	'".$_POST['pl_RAXBHW']."', '".$_POST['pl_RAXBSW']."')";
	
					$action="inserted";
				}else if ($pl_Count=="1"){
					$query3 = "UPDATE BSDS_PL_GEN set
					COMMENTS='".$pl_COMMENTS."',
					NR_OF_CAB='".$_POST['pl_NR_OF_CAB']."',
					CDUTYPE='".$_POST['pl_CDUTYPE']."',
					CABTYPE='".$_POST['pl_CABTYPE']."',
					BBS='".$_POST['pl_BBS']."' ,
					DXUTYPE1='".$_POST['pl_DXUTYPE1']."',
					DXUTYPE2='".$_POST['pl_DXUTYPE2']."',
					DXUTYPE3='".$_POST['pl_DXUTYPE3']."',
					PLAYSTATION='".$_POST['pl_PLAYSTATION']."',
					IPB='".$_POST['pl_IPB']."',
					PSU='".$_POST['pl_PSU']."',
					TXBHW='".$_POST['pl_TXBHW']."',
					TXBSW='".$_POST['pl_TXBSW']."',
					RAXBHW='".$_POST['pl_RAXBHW']."',
					RAXBSW='".$_POST['pl_RAXBSW']."',
					MBPS='".$_POST['pl_MBPS']."',
					RAXEHW='".$_POST['pl_RAXEHW']."',
					RAXESW='".$_POST['pl_RAXESW']."',
					HSTXHW='".$_POST['pl_HSTXHW']."',
					HSTXSW='".$_POST['pl_HSTXSW']."',
					SERVICE='".$_POST['pl_SERVICE']."',
					TECHNO_CHANGEDATE=SYSDATE
					WHERE BSDSKEY='".$_POST['bsdskey']."' AND TECHNO='".$_POST['band']."' AND STATUS='".$_POST['frozen']."' 
					AND BSDS_BOB_REFRESH=to_date('".$_POST['createddate']."')";
					$action="updated";
				}
				//echo "$query3 <br>";

				$stmt2 = parse_exec_free($conn_Infobase, $query3, $error_str);
				if (!$stmt2) {
					die_silently($conn_Infobase, $error_str);
				}else{
					$message.="PLANNED DATA ".$action." for ".$_POST['band']."!<br>";
				}
				OCICommit($conn_Infobase);

			}else if ($_POST['onlyfeeder']=="yes"){
				for ($n = 1; $n <= 6; $n++) {
					$pl_temp19="pl2_FEEDERLEN_$n";
					$pl2_FEEDERLEN=$$pl_temp19;
					$pl_temp20="pl_FEEDER_$n";
					$pl_FEEDER=$_POST[$pl_temp20];
					$pl_temp="pl_ELECTILT1_$n";
					$pl_ELECTILT1=$_POST[$pl_temp];
					$pl_temp="pl_ELECTIL2_$n";
					$pl_ELECTILT2=$_POST[$pl_temp];

					$query2 = "UPDATE BSDS_PL_GSM_SEC SET
							FEEDERLEN='".$pl2_FEEDERLEN."',
							FEEDER='".$pl_FEEDER."',
							ELECTILT1='".$pl_ELECTILT1."',
							ELECTILT2='".$pl_ELECTILT2."'
							WHERE BSDSKEY= '".$_POST['bsdskey']."' AND TECHNO='".$_POST['band']."'
							AND STATUS='".$_POST['frozen']."' AND SECT='".$n."' AND BSDS_BOB_REFRESH='".$_POST['createddate']."'";
					$action="updated";
					$stmt2 = parse_exec_free($conn_Infobase, $query2, $error_str);
					if (!$stmt2) {
						die_silently($conn_Infobase, $error_str);
					}else{
						$sectors.=$n."&nbsp;";
					}
					OCICommit($conn_Infobase);
				}
				$message.="PL FEEDER DATA & ELECT TILT ".$action." for ".$_POST['band']." sectors ".$sectors."!<br>";
			}

			//UPDATE FEEDER SHARE DATA
			$feedershare_Count=check_feedershare_exists("PLANNED",$_POST['frozen'],$_POST['bsdskey'],$_POST['createddate']);

			for ($i = 1; $i <= 6; $i++) { //foreach sector

				$FEEDERSHARE_temp="pl_FEEDERSHARE_".$i;

				if ($_POST['band']=="G9"){
					$query5_update .= "GSM900_$i='".$_POST[$FEEDERSHARE_temp]."',";
					$FEEDERSHARE_GSM900=$_POST[$FEEDERSHARE_temp];
				}else{
					$FEEDERSHARE_GSM900="";
				}
				if ($_POST['band']=="G18"){
					$query5_update .= "GSM1800_$i='".$_POST[$FEEDERSHARE_temp]."',";
					$FEEDERSHARE_GSM1800=$_POST[$FEEDERSHARE_temp];
				}else{
					$FEEDERSHARE_GSM1800="";
				}
				if ($_POST['band']=="U9"){
					$query5_update .= "UMTS900_$i='".$_POST[$FEEDERSHARE_temp]."',";
					$FEEDERSHARE_UMTS900=$_POST[$FEEDERSHARE_temp];
				}else{
					$FEEDERSHARE_UMTS900="";
				}
				if ($_POST['band']=="U21"){
					$query5_update .= "UMTS2100_$i='".$_POST[$FEEDERSHARE_temp]."',";
					$FEEDERSHARE_UMTS2100=$_POST[$FEEDERSHARE_temp];
				}else{
					$FEEDERSHARE_UMTS2100="";
				}
				if ($_POST['band']=="L8"){
					$query5_update .= "LTE800_$i='".$_POST[$FEEDERSHARE_temp]."',";
					$FEEDERSHARE_LTE800=$_POST[$FEEDERSHARE_temp];
				}else{
					$FEEDERSHARE_LTE800="";
				}
				if ($_POST['band']=="L18"){
					$query5_update .= "LTE1800_$i='".$_POST[$FEEDERSHARE_temp]."',";
					$FEEDERSHARE_LTE1800=$_POST[$FEEDERSHARE_temp];
				}else{
					$FEEDERSHARE_LTE1800="";
				}
				if ($_POST['band']=="L26"){
					$query5_update .= "LTE2600_$i='".$_POST[$FEEDERSHARE_temp]."',";
					$FEEDERSHARE_LTE2600=$_POST[$FEEDERSHARE_temp];
				}else{
					$FEEDERSHARE_LTE2600="";
				}
				$query5_new .= "'".$FEEDERSHARE_GSM900."','".$FEEDERSHARE_GSM1800."',
				'".$FEEDERSHARE_UMTS900."','".$UMTS2100_data."','".$FEEDERSHARE_LTE800."',
				'".$FEEDERSHARE_LTE1800."','".$FEEDERSHARE_LTE2600."',";
			}

			if ($feedershare_Count>1){
				$query6="DELETE FROM BSDS_PL2 WHERE BSDSKEY='".$_POST['bsdskey']."' AND STATUS='".$_POST['frozen']."'";

				$query6.=" AND BSDS_BOB_REFRESH=to_date('".$_POST['createddate']."')";
		
				//echo $query6;
				$stmt6 = parse_exec_free($conn_Infobase, $query6, $error_str);
				if (!$stmt6) {
					die_silently($conn_Infobase, $error_str);
				}else{
					$message.="PL FEEDER DATA DELETED for ".$_POST['band']."!<br>";
				}
				OCICommit($conn_Infobase);
				$feedershare_Count="";
			}
			if ($feedershare_Count==0 || $feedershare_Count==""){
				$query5 = "INSERT INTO BSDS_PL2 
				VALUES ('".$_POST['bsdskey']."','".$_POST['createddate']."',";
				$query5 .= substr($query5_new,0,-1). ",'".$_POST['frozen']."')";

				$action="inserted";
			}else if ($feedershare_Count==1){
				$query5 = "UPDATE BSDS_PL2 set ";
				$query5 .= substr($query5_update,0,-1);
				$query5 .= "WHERE BSDSKEY='".$_POST['bsdskey']."' AND STATUS='".$_POST['frozen']."'";
				$query5.=" AND BSDS_BOB_REFRESH=to_date('".$_POST['createddate']."')";
		
				$action="updated";
			}
			if ($query5_new!="" or $query5_update!=""){
				//echo $query5;
				$stmt5 = parse_exec_free($conn_Infobase, $query5, $error_str);
				if (!$stmt5) {
					die_silently($conn_Infobase, $error_str);
				}else{
					$message.="PL FEEDER SHARE '".$action."' for ".$_POST['band']."!<br>";
				}
				OCICommit($conn_Infobase);
			}

			
			// UPDATE THE CHANGEDATE
			if ($_POST['bsdskey']!=''){
				$query4 = "UPDATE BSDS_OVERVIEW set DATE_UPDATE=SYSDATE, DESIGNER_UPDATE='".$guard_username."' WHERE BSDSKEY='".$_POST['bsdskey']."' AND CREATED_DATE='".$_POST['createddate']."'";
				//echo "$query4 <br>";
				$stmt2 = parse_exec_free($conn_Infobase, $query4, $error_str);
				if (!$stmt2) {
					die_silently($conn_Infobase, $error_str);
				}else{
					//$message.="BSDS CHANGEDATE has succesfully been updated!<br>";
				}
				OCICommit($conn_Infobase);

				$_POST['band']=$_POST['band'];
			}else{
				echo 'Infobase lost the BSDSKEY. Please contact Infobase admin asap!';
				die;
			}
			
		}else{//END ERROR
			$warning.="PLANNED data could not be saved because of errors!<br>";
			$warning.=$ERROR_MESSAGE;
			$message="";
		}
	}

}else{
	die("You are doing strange things!!");
}

if ($message){
	$res["responsedata"] = $message;
	$res["responsetype"]="info";
}
if ($warning){
	$res["responsedata"] = $warning;
	$res["responsetype"]="warning";
}
if ($alert){
	$res["responsedata"] = $alert;
	$res["responsetype"]="error";
}
echo json_encode($res);

?>

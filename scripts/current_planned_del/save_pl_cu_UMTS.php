<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
include("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

//set_error_handler("myErrorHandler");

if ($_POST['lognode']!="" && $_POST['bsdskey']!=""){
	
	$check_current_exists_UMTS=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['bsdsbobrefresh'],'allsec',$_POST['donor'],$_POST['lognode'],$_POST['viewtype']);
	if ($_POST['band']=="L18" or $_POST['band']=="L26" or $_POST['band']=="L8"){
		$tabletype="LTE";
	}else if ($_POST['band']=="U21" or $_POST['band']=="U9"){
		$tabletype="UMTS";
	}else{
		die;
	}
	if ($check_current_exists_UMTS=="0"){
		//FUND, BUILD and POST can not be updated!
		$query1 = "INSERT INTO BSDS_CU_".$tabletype." VALUES ('','".$_POST['lognode']."','','',
		SYSDATE,'".$_POST['POWERSUP']."', '".$_POST['CABTYPE']."', '".$_POST['IPB']."','".$_POST['PSU']."',
		'".$_POST['TXBHW']."', '".$_POST['TXBSW']."', '".$_POST['RAXBHW']."','".$_POST['RAXBSW']."',
		'".$_POST['MBPS']."', '".$_POST['RAXEHW']."', '".$_POST['RAXESW']."', '".$_POST['HSTXHW']."',
		'".$_POST['HSTXSW']."', '".$_POST['PLAYSTATION']."', '".$_POST['SERVICE']."',
		'".$_POST['band']."','PRE',";
		if ($_POST['band']=="L18" or $_POST['band']=="L26" or $_POST['band']=="L8"){
			$query1 .= "'".$_POST['BPL']."')";
		}else if ($_POST['band']=="U21" or $_POST['band']=="U9"){
			$query1 .= "'".$_POST['BPC']."','".$_POST['BPK']."','".$_POST['CC']."')";
		}
		$action="saved";
	}else{
		$query1 = "UPDATE BSDS_CU_".$tabletype." SET
			LOGNODEID='".$_POST['LOGNODEID']."',
			POWERSUP='".$_POST['POWERSUP']."',
			CABTYPE='".$_POST['CABTYPE']."',
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
			PLAYSTATION='".$_POST['PLAYSTATION']."',
			SERVICE='".$_POST['SERVICE']."',";
			if ($_POST['band']=="L18" or $_POST['band']=="L26" or $_POST['band']=="L8"){
				$query1 .= "BPL='".$_POST['BPL']."',";
			}else if ($_POST['band']=="U21" or $_POST['band']=="U9"){
				$query1 .= "BPC='".$_POST['BPC']."',BPK='".$_POST['BPK']."',CC='".$_POST['CC']."',";
			}
			$query1 .= "
			CHANGE_DATE = SYSDATE
			WHERE LOGNODEPK = '".$_POST['lognode']."' AND STATUS='PRE' AND TECHNO='".$_POST['band']."'";

		$action="updated";
	}
	//echo $query1;
	$stmt = parse_exec_free($conn_Infobase, $query1, $error_str);
	if (!$stmt) {
		choke_and_die($conn_Infobase, $error_str);
	}else{
		$message.="CUR DATA has been ".$action."<br>";
	}
	OCICommit($conn_Infobase);

	for ($n = 1; $n <= 4; $n++) {  // VARAIBELE VARIABLES !!!
		$check_current_exists_UMTS_sec=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['bsdsbobrefresh'],$n,$_POST['donor'],$_POST['lognode'],$_POST['viewtype']);

		if($check_current_exists_UMTS_sec=="0"){
			$query2 = "INSERT INTO BSDS_CU_".$tabletype."_SEC VALUES ('','".$_POST['lognode']."',
					'".$_POST['UMTSCELLID_'.$n]."','".$_POST['UMTSCELLPK_'.$n]."','".$_POST['TRUS_INST1_'.$n]."', 
					'".$_POST['TRUS_INST2_'.$n]."', '".$_POST['FREQ_ACTIVE_'.$n]."','".$_POST['MCPAMODE_'.$n]."', 
					'".$_POST['MCPATYPE_'.$n]."', '".$_POST['ACS_'.$n]."', '".$_POST['RET_'.$n]."',
					'".$_POST['band']."','".$n."','PRE','')";
			$action="saved";
		}else if($check_current_exists_UMTS_sec=="1"){
			$query2 = "UPDATE BSDS_CU_".$tabletype."_SEC SET
			UMTSCELLID='".$_POST['UMTSCELLID_'.$n]."',
			UMTSCELLPK='".$_POST['UMTSCELLPK_'.$n]."',
			TRU_INST1='".$_POST['TRUS_INST1_'.$n]."',
			TRU_INST2='".$_POST['TRUS_INST2_'.$n]."',
			FREQ_ACTIVE='".$_POST['FREQ_ACTIVE_'.$n]."',
			MCPAMODE='".$_POST['MCPAMODE_'.$n]."',
			MCPATYPE='".$_POST['MCPATYPE_'.$n]."',
			ACS='".$_POST['ACS_'.$n]."',
			RET='".$_POST['RET_'.$n]."'
			WHERE LOGNODEPK = '".$_POST['lognode']."' AND SECT='".$n."' AND STATUS='PRE' AND TECHNO='".$_POST['band']."'";
			$action="updated";
		}
		//echo $query2;
		$stmt = parse_exec_free($conn_Infobase, $query2, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$message.="CURRENT sector $n<br>";
		}
		OCICommit($conn_Infobase);
	}

	//UPDATE CURRENT FEEDERSHARE DATA
	$feedershare_Count=check_feedershare_exists("CURRENT",$_POST['viewtype'],$_POST['bsdskey'],$_POST['bsdsbobrefresh']);
	for ($i = 1; $i <= 4; $i++) { //foreach sector

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

		if ($feedershare_Count=="0" || $feedershare_Count==""){
			$query4 = "INSERT INTO BSDS_CU VALUES ('".$_POST['bsdskey']."',";
			if ($_POST['viewtype']=="FUND"){
				$query4 .="to_date('".$_POST['bsdsbobrefresh']."'),";
			}else{
				$query4 .="'',";
			}
			$query4 .= $query4_new;
			$query4 .= "'".$_POST['viewtype']."')";
			//echo $query4;
			$action="inserted";
		}else if ($feedershare_Count=="1" && $query4_update!=""){
			$query4 = "UPDATE BSDS_CU SET ";
			$query4 .= substr($query4_update,0,-1);
			$query4 .=" WHERE BSDSKEY='".$_POST['bsdskey']."' AND STATUS='".$_POST['viewtype']."'";
			if ($_POST['viewtype']=="FUND"){
				$query4 .=" AND BSDS_BOB_REFRESH=to_date('".$_POST['bsdsbobrefresh']."')";
			}
			$action="updated";
		}
		if ($query4_new!="" or $query4_update!=""){
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

/*************************
// PLANNED SAVE OR UPDATE
*************************/

	require("../checks/checks_proc.php");

	$check_planned_exists_UMTS=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],'',$_POST['viewtype'],$_POST['donor']);
	if(empty($ERROR_MESSAGE)){

		if ($_POST['onlyfeeder']!="yes"){

			$pl_COMMENTS=str_replace("'","''",$_POST['pl_COMMENTS']);
			if ($check_planned_exists_UMTS==0){
				$query = "INSERT INTO BSDS_PL_".$tabletype." VALUES ('".$_POST['bsdskey']."',SYSDATE,'".$_POST['pl_POWERSUP']."','".$_POST['pl_CABTYPE']."',
				'".$_POST['pl_IPB']."','".$_POST['pl_PSU']."', '".$_POST['pl_TXBHW']."', '".$_POST['pl_TXBSW']."',
				'".$_POST['pl_RAXBHW']."', '".$_POST['pl_RAXBSW']."','".$_POST['pl_MBPS']."',
				'".$pl_COMMENTS."','".$_POST['RAXEHW']."', '".$_POST['RAXESW']."', '".$_POST['HSTXHW']."', '".$_POST['HSTXSW']."',
				'".$_POST['PLAYSTATION']."', '".$_POST['SERVICE']."','".$_POST['band']."','".$_POST['viewtype']."',";
				if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
					$query.="'".$_POST['bsdsbobrefresh']."',";
				}else if ($_POST['viewtype']=="PRE"){
					$query.="'',";
				}
				if ($_POST['band']=="L18" or $_POST['band']=="L26" or $_POST['band']=="L8"){
					$query .= "'".$_POST['BPL']."')";
				}else if ($_POST['band']=="U21" or $_POST['band']=="U9"){
					$query .= "'".$_POST['pl_BPC']."','".$_POST['pl_BPK']."','".$_POST['pl_CC']."')";
				}
				$action="saved";
			}else{
				$query = "UPDATE BSDS_PL_".$tabletype." SET
				POWERSUP='".$_POST['pl_POWERSUP']."',
				CABTYPE='".$_POST['pl_CABTYPE']."',
				IPB='".$_POST['pl_IPB']."',
				PSU='".$_POST['pl_PSU']."',
				TXBHW='".$_POST['pl_TXBHW']."',
				TXBSW='".$_POST['pl_TXBSW']."',
				RAXBHW='".$_POST['pl_RAXBHW']."',
				RAXBSW='".$_POST['pl_RAXBSW']."',
				MBPS='".$_POST['pl_MBPS']."',
				COMMENTS='".$pl_COMMENTS."',
				RAXEHW='".$_POST['pl_RAXEHW']."',
				RAXESW='".$_POST['pl_RAXESW']."',
				HSTXHW='".$_POST['pl_HSTXHW']."',
				HSTXSW='".$_POST['pl_HSTXSW']."',
				PLAYSTATION='".$_POST['pl_PLAYSTATION']."',
				SERVICE='".$_POST['pl_SERVICE']."',
				TECHNO_CHANGEDATE=SYSDATE,";
				if ($_POST['band']=="L18" or $_POST['band']=="L26" or $_POST['band']=="L8"){
					$query .= "BPL='".$_POST['pl_BPL']."'";
				}else if ($_POST['band']=="U21" or $_POST['band']=="U9"){
					$query .= "BPC='".$_POST['pl_BPC']."',BPK='".$_POST['pl_BPK']."',CC='".$_POST['pl_CC']."'";
				}
				$query .=" WHERE BSDSKEY= '".$_POST['bsdskey']."' AND TECHNO='".$_POST['band']."' AND STATUS='".$_POST['viewtype']."'";
				if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
						$query.=" AND BSDS_BOB_REFRESH='".$_POST['bsdsbobrefresh']."'";
				}
				$action="updated";
			}
			//echo $query."<br>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message.="PL DATA has been $action.<br>";
			}
			OCICommit($conn_Infobase);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			for ($n = 1; $n <= 4; $n++){ 

				require("height_conversion_concat.php");

				$check_planned_exists_UMTS_sec=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],$n,$_POST['viewtype'],$_POST['donor']);

				if ($check_planned_exists_UMTS_sec=="0"){
					$query = "INSERT INTO BSDS_PL_".$tabletype."_SEC VALUES (
					'".$_POST['bsdskey']."','".$_POST['pl_UMTSCELLID_'.$n]."','".$_POST['pl_UMTSCELLPK_'.$n]."', 
					'".$_POST['pl_TRUS_INST1_'.$n]."','".$_POST['pl_TRUS_INST2_'.$n]."','".$_POST['pl_FREQ_ACTIVE_'.$n]."', 
					'".$_POST['pl_MCPAMODE_'.$n]."', '".$_POST['pl_MCPATYPE_'.$n]."', '".$_POST['pl_ACS_'.$n]."',
					'".$_POST['pl_RET_'.$n]."',	'".$_POST['pl_ANTHEIGHT1_'.$n]."', '".$_POST['pl_AZI1_'.$n]."',
					'".$_POST['pl_ANTTYPE1_'.$n]."', '".$_POST['pl_ELECTILT1_'.$n]."', 
					'".$_POST['pl_MECHTILT1_'.$n]."','".$_POST['pl_MECHTILT_DIR1_'.$n]."','".$_POST['pl_FEEDER_'.$n]."', 
					'".$_POST['pl_FEEDERLEN_'.$n]."', '".$_POST['pl_ANTTYPE2_'.$n]."', '".$_POST['pl_ELECTILT2_'.$n]."',
					'".$_POST['pl_MECHTILT2_'.$n]."', '".$_POST['pl_MECHTILT_DIR2_'.$n]."','".$_POST['pl_ANTHEIGHT2_'.$n]."',
					'".$_POST['pl_STATE_'.$n]."','".$_POST['pl_AZI2_'.$n]."',
					'".$_POST['band']."','".$n."','". $_POST['viewtype']."'";
					if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
						$query.=" ,'".$_POST['bsdsbobrefresh']."')";
					}else if ($_POST['viewtype']=="PRE"){
						$query.=",'')";	
					}					
					$action="saved";
				}else{
					$query = "UPDATE BSDS_PL_".$tabletype."_SEC SET
					FREQ_ACTIVE='".$_POST['pl_FREQ_ACTIVE_'.$n]."',
					TRU_INST1='".$_POST['pl_TRUS_INST1_'.$n]."',
					TRU_INST2='".$_POST['pl_TRUS_INST2_'.$n]."',
			 		MCPAMODE='".$_POST['pl_MCPAMODE_'.$n]."',
					MCPATYPE='".$_POST['pl_MCPATYPE_'.$n]."',
					ACS='".$_POST['pl_ACS_'.$n]."',
					RET='".$_POST['pl_RET_'.$n]."',
					ANTHEIGHT1='".$_POST['pl_ANTHEIGHT1_'.$n]."',
					ANTHEIGHT2='".$_POST['pl_ANTHEIGHT2_'.$n]."',
					AZI1='".$_POST['pl_AZI1_'.$n]."',
					ANTTYPE1='".$_POST['pl_ANTTYPE1_'.$n]."',
					ELECTILT1='".$_POST['pl_ELECTILT1_'.$n]."',
					MECHTILT1='".$_POST['pl_MECHTILT1_'.$n]."',
					MECHTILT_DIR1='".$_POST['pl_MECHTILT_DIR1_'.$n]."',
					FEEDER='".$_POST['pl_FEEDER_'.$n]."',
					FEEDERLEN='".$_POST['pl_FEEDERLEN_'.$n]."',
					ANTTYPE2='".$_POST['pl_ANTTYPE2_'.$n]."',
					ELECTILT2='".$_POST['pl_ELECTILT2_'.$n]."',
					MECHTILT2='".$_POST['pl_MECHTILT2_'.$n]."',
					MECHTILT_DIR2='".$_POST['pl_MECHTILT_DIR2_'.$n]."',
					STATE='".$_POST['pl_STATE_'.$n]."',
					AZI2='".$_POST['pl_AZI2_'.$n]."'
					WHERE BSDSKEY= '".$_POST['bsdskey']."' AND SECT='".$n."' AND STATUS='".$_POST['viewtype']."'  AND TECHNO='".$_POST['band']."'";
					if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
						$query.=" AND BSDS_BOB_REFRESH='".$_POST['bsdsbobrefresh']."'";
					}
					$action="updated";
				}

				$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}else{
					$message.="PL DATA has been ".$action." for sector ".$n."!<br>";
				}
				OCICommit($conn_Infobase);
			}
		}else if ($_POST['onlyfeeder']=="yes"){

			for ($n = 1; $n <= 4; $n++) {  // VARAIBELE VARIABLES !!!

				$temp20="pl_FEEDER_$n";
				$pl_FEEDER=$_POST[$temp20];
				$temp19="pl_FEEDERLEN_$n";
				$pl_FEEDERLEN=$_POST[$temp19];
				$temp28="pl_FEEDERLEN_".$n."_t";
				$pl_FEEDERLEN_t=$_POST[$temp28];
				$pl_temp="pl_ELECTILT1_".$n;
				$pl_ELECTILT1=$_POST[$pl_temp];
				$pl_temp="pl_ELECTIL2_$n";
				$pl_ELECTILT2=$_POST[$pl_temp];

				if ($pl_FEEDERLEN!="" && $pl_FEEDERLEN!="-"){
					if ($pl_FEEDERLEN_t==""){
						$pl_FEEDERLEN_t="0";
					}
					$pl_FEEDERLEN=$pl_FEEDERLEN.".".$pl_FEEDERLEN_t;
				}

				$check_planned_exists_UMTS_sec=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],$n,$_POST['viewtype'],$_POST['donor']);
			 	//echo $check_planned_exists_UMTS_sec;
				if ($check_planned_exists_UMTS_sec!="0"){

					$query2 = "UPDATE BSDS_PL_".$tabletype."_SEC SET
								FEEDER='".$pl_FEEDER."',
								FEEDERLEN='".$pl_FEEDERLEN."',
								ELECTILT1='".$pl_ELECTILT1."',
								ELECTILT2='".$pl_ELECTILT2."'
								WHERE BSDSKEY= '".$_POST['bsdskey']."' AND TECHNO='".$_POST['band']."'
								AND STATUS='".$_POST['viewtype']."' AND SECT='".$n."'";
								if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
									$query.=" AND BSDS_BOB_REFRESH='".$_POST['bsdsbobrefresh']."'";
								}
					$action="updated";
					//echo "<br>$query2<br><br>";
					$stmt2 = parse_exec_free($conn_Infobase, $query2, $error_str);
				   	if (!$stmt2) {
				    	die_silently($conn_Infobase, $error_str);
				   	}else{
						$message.="PL FEEDER DATA has been UPDATED for sector $n!<br>";
				   	}
				   	OCICommit($conn_Infobase);
			   	}
			}

		}


		//UPDATE FEEDER SHARE DATA
		$feedershare_Count=check_feedershare_exists("PLANNED",$_POST['viewtype'],$_POST['bsdskey'],$_POST['bsdsbobrefresh']);

			for ($i = 1; $i <= 4; $i++) { //foreach sector

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
			$query6="DELETE FROM BSDS_PL WHERE BSDSKEY='".$_POST['bsdskey']."' AND STATUS='".$_POST['viewtype']."'";
			if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND"){
					$query6.=" AND BSDS_BOB_REFRESH=to_date('".$_POST['bsdsbobrefresh']."')";
			}
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
			$query5 = "INSERT INTO BSDS_PL 
			VALUES ('".$_POST['bsdskey']."',";
			if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
					$query5.="'".$_POST['bsdsbobrefresh']."',";
			}else if ($_POST['viewtype']=="PRE"){
				$query5.="'',";
			}
			$query5 .= substr($query5_new,0,-1). ",'".$_POST['viewtype']."')";

			$action="inserted";
		}else if ($feedershare_Count==1){
			$query5 = "UPDATE BSDS_PL set ";
			$query5 .= substr($query5_update,0,-1);
			$query5 .= "WHERE BSDSKEY='".$_POST['bsdskey']."' AND STATUS='".$_POST['viewtype']."'";
			if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND"){
					$query5.=" AND BSDS_BOB_REFRESH=to_date('".$_POST['bsdsbobrefresh']."')";
			}
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
		$query = "UPDATE BSDS_GENERALINFO set CHANGE_DATE= SYSDATE, DESIGNER_UPDATE='".$guard_username."' WHERE BSDSKEY='".$_POST['bsdskey']."'";
		//$query_out.= $query."<br><br>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			$message.="CHANGEDATE has been ".$action." for ".$_POST['band']."!<br>";
		}
		OCICommit($conn_Infobase);
	}else{//END ERROR
		$warning.="PLANNED data could not be saved because of errors!<br>";
		$warning.="$ERROR_MESSAGE";
		$message="";
	}
}else{ //if no lognode
	die("You are doing strange things!!");
}

if ($message){
	$res["responsedata"] = $message;
	$res["responsetype"]="info";
	echo json_encode($res);
}

if ($warning){
	$res["responsedata"] = $warning;
	$res["responsetype"]="warning";
	echo json_encode($res);
}
if ($alert){
	$res["responsedata"] = $alert;
	$res["responsetype"]="error";
	echo json_encode($res);
}
?>
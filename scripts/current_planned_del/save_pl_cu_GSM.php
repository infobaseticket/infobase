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
}else if ($_POST['lognode']!="" && $_POST['bsdskey']!=""){

/*************************
// CURRENT SAVE OR UPDATE
*************************/
	$check_current_exists=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['bsdsbobrefresh'],'allsec',$_POST['donor'],$_POST['lognode'],$_POST['viewtype']);
	if ($check_current_exists=="0"){
		//ONly a current PRE can be inserted or updated. (FOR POST => BSDS funded can not be updated)
		$query = "INSERT INTO BSDS_CU_GSM
		VALUES ('','".$_POST['lognode']."', SYSDATE,
		'".$_POST['FREQ_ACTIVE_1']."','".$_POST['FREQ_ACTIVE_2']."','".$_POST['FREQ_ACTIVE_3']."','".$_POST['TRU_INST1_1']."',
		'".$_POST['TRU_INST1_2']."','".$_POST['TRU_INST1_3']."','".$_POST['TRU_TYPE1_1']."','".$_POST['TRU_TYPE1_2']."',
		'".$_POST['TRU_TYPE1_3']."','".$_POST['TRU_INST2_1']."','".$_POST['TRU_INST2_2']."','".$_POST['TRU_INST2_3']."',
		'".$_POST['TRU_TYPE2_1']."','".$_POST['TRU_TYPE2_2']."','".$_POST['TRU_TYPE2_3']."','".$_POST['CABTYPE']."',
		'".$_POST['NR_OF_CAB']."','".$_POST['CDUTYPE']."','".$_POST['TMA_1']."','".$_POST['TMA_2']."','".$_POST['TMA_3']."',
		'".$_POST['COMB_1']."','".$_POST['COMB_2']."','".$_POST['COMB_3']."','".$_POST['DCBLOCK_1']."','".$_POST['DCBLOCK_2']."',
		'".$_POST['DCBLOCK_3']."','".$_POST['BBS']."','".$_POST['DXUTYPE1']."','".$_POST['DXUTYPE2']."','".$_POST['DXUTYPE3']."',
		'".$_POST['FREQ_ACTIVE_1']."','".$_POST['TRU_INST1_4']."','".$_POST['TRU_TYPE1_4']."','".$_POST['TRU_INST2_4']."',
		'".$_POST['TRU_TYPE2_4']."','".$_POST['TMA_4']."','".$_POST['COMB_4']."','".$_POST['DCBLOCK_4']."','".$_POST['PLAYSTATION']."',
		'".$_POST['band']."','PRE','')";
		$action="saved";
	}else{
		$query = "UPDATE BSDS_CU_GSM SET
		 CHANGEDATE = SYSDATE,
		 FREQ_ACTIVE_1='".$_POST['FREQ_ACTIVE_1']."',
		 FREQ_ACTIVE_2='".$_POST['FREQ_ACTIVE_2']."',
		 FREQ_ACTIVE_3='".$_POST['FREQ_ACTIVE_3']."',
		 FREQ_ACTIVE_4='".$_POST['FREQ_ACTIVE_4']."',
		 TRU_INST1_1='".$_POST['TRU_INST1_1']."',
		 TRU_INST1_2='".$_POST['TRU_INST1_2']."',
		 TRU_INST1_3='".$_POST['TRU_INST1_3']."',
		 TRU_INST1_4='".$_POST['TRU_INST1_4']."',
		 TRU_TYPE1_1='".$_POST['TRU_TYPE1_1']."',
		 TRU_TYPE1_2='".$_POST['TRU_TYPE1_2']."',
		 TRU_TYPE1_3='".$_POST['TRU_TYPE1_3']."',
		 TRU_TYPE1_4='".$_POST['TRU_TYPE1_4']."',
		 TRU_INST2_1='".$_POST['TRU_INST2_1']."',
		 TRU_INST2_2='".$_POST['TRU_INST2_2']."',
		 TRU_INST2_3='".$_POST['TRU_INST2_3']."',
		 TRU_INST2_4='".$_POST['TRU_INST2_4']."',
		 TRU_TYPE2_1='".$_POST['TRU_TYPE2_1']."',
		 TRU_TYPE2_2='".$_POST['TRU_TYPE2_2']."',
		 TRU_TYPE2_3='".$_POST['TRU_TYPE2_3']."',
		 TRU_TYPE2_4='".$_POST['TRU_TYPE2_4']."',
		 CABTYPE='".$_POST['CABTYPE']."',
		 NR_OF_CAB='".$_POST['NR_OF_CAB']."',
		 CDUTYPE='".$_POST['CDUTYPE']."',
		 TMA_1='".$_POST['TMA_1']."',
		 TMA_2='".$_POST['TMA_2']."',
		 TMA_3='".$_POST['TMA_3']."',
		 TMA_4='".$_POST['TMA_4']."',
		 COMB_1='".$_POST['COMB_1']."',
		 COMB_2='".$_POST['COMB_2']."',
		 COMB_3='".$_POST['COMB_3']."',
		 COMB_4='".$_POST['COMB_4']."',
		 DCBLOCK_1='".$_POST['DCBLOCK_1']."',
		 DCBLOCK_2='".$_POST['DCBLOCK_2']."',
		 DCBLOCK_3='".$_POST['DCBLOCK_3']."',
		 DCBLOCK_4='".$_POST['DCBLOCK_4']."',
		 BBS= '".$_POST['BBS']."',
		 DXUTYPE1='".$_POST['DXUTYPE1']."',
		 DXUTYPE2='".$_POST['DXUTYPE2']."',
		 DXUTYPE3='".$_POST['DXUTYPE3']."',
		 PLAYSTATION='".$_POST['PLAYSTATION']."'
		 WHERE SITEKEY= '".$_POST['lognode']."' AND STATUS='PRE' AND TECHNO='".$_POST['band']."'";

		 $action="updated";
	}
	//echo $query."<br>";
	$stmt2 = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt2) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$message="CURRENT DATA '".$action."' for ".$_POST['band']."!<br>";
	}
	OCICommit($conn_Infobase);


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
	
	if ($pl_is_BSDS_accepted!="Accepted"){
		$checktype="pl_";
		require("../checks/checks_proc.php");

		if(empty($ERROR_MESSAGE)){

			if ($_POST['onlyfeeder']!="yes"){
				for ($n = 1; $n <= 4; $n++) {  // VARAIBELE VARIABLES !!!
								
					require("height_conversion_concat.php");

					$pl_Count="";
					$pl_Count=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],$n,$_POST['viewtype'],$_POST['donor']);
					//echo "$n pl_Count $pl_Count<br>";
					// INSERT OR UPDATE THE BSDSDATA
					if ($pl_Count=="error"){
						$query3= "DELETE FROM BSDS_PL_GSM_SEC
						WHERE BSDSKEY= '".$_POST['bsdskey']."' AND STATUS='".$_POST['viewtype']."' AND SECT='".$n."' AND TECHNO='".$_POST['band']."'";
						if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND"  || $_POST['viewtype']=="BUILD"){ //build only for admin
							$query3.=" AND BSDS_BOB_REFRESH=to_date('".$_POST['bsdsbobrefresh']."')";
						}
						//echo $query3;
						$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
						if (!$stmt3) {
							die_silently($conn_Infobase, $error_str);
						}
						OCICommit($conn_Infobase);
					}
					if ($pl_Count=="error" or $pl_Count==0){
						$query2 = "INSERT INTO BSDS_PL_GSM_SEC VALUES ('".$_POST['bsdskey']."',
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
						'".$_POST['band']."','".$n."','".$_POST['viewtype']."',";
						if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
							$query2.="'".$_POST['bsdsbobrefresh']."')";
						}else{
							$query2.="'')";
						}
						$action="saved";

					}else if ($pl_Count=="1"){
						$query2 = "UPDATE BSDS_PL_GSM_SEC
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
						AZI2='".$_POST["pl_AZI2_".$n]."'
						WHERE BSDSKEY= '".$_POST['bsdskey']."'
						AND SECT='".$n."' AND TECHNO='".$_POST['band']."' AND STATUS='".$_POST['viewtype']."'";
						if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND"  || $_POST['viewtype']=="BUILD"){ //build only for admin
							$query2.=" AND BSDS_BOB_REFRESH='".$_POST['bsdsbobrefresh']."'";
						}
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
				$pl_Count=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],'allsec',$_POST['viewtype'],$_POST['donor']);
				$pl_COMMENTS=str_replace("'","''",$_POST['pl_COMMENTS']);

				if ($pl_Count=="0" || $pl_Count==""){
					$query3 = "INSERT INTO BSDS_PL_GSM VALUES ('".$_POST['bsdskey']."','".$_POST['pl_CABTYPE']."','".$_POST['pl_NR_OF_CAB']."','".$_POST['pl_CDUTYPE']."','".$pl_COMMENTS."','".$_POST['pl_BBS']."',
					'".$_POST['pl_DXUTYPE1']."','".$_POST['pl_DXUTYPE2']."','".$_POST['pl_DXUTYPE3']."',
					'".$_POST['PLAYSTATION']."','".$_POST['band']."','".$_POST['viewtype']."',";
					if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
						$query3.="'".$_POST['bsdsbobrefresh']."',SYSDATE)";
					}else if ($_POST['viewtype']=="PRE"){
						$query3.="'',SYSDATE)";
					}				
					
					$action="inserted";
				}else if ($pl_Count=="1"){
					$query3 = "UPDATE BSDS_PL_GSM set
					COMMENTS='".$pl_COMMENTS."',
					NR_OF_CAB='".$_POST['pl_NR_OF_CAB']."',
					CDUTYPE='".$_POST['pl_CDUTYPE']."',
					CABTYPE='".$_POST['pl_CABTYPE']."',
					BBS='".$_POST['pl_BBS']."' ,
					DXUTYPE1='".$_POST['pl_DXUTYPE1']."',
					DXUTYPE2='".$_POST['pl_DXUTYPE2']."',
					DXUTYPE3='".$_POST['pl_DXUTYPE3']."',
					PLAYSTATION='".$_POST['pl_PLAYSTATION']."',
					TECHNO_CHANGEDATE=SYSDATE
					WHERE BSDSKEY='".$_POST['bsdskey']."' AND TECHNO='".$_POST['band']."' AND STATUS='".$_POST['viewtype']."'";
					if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
						$query3.=" AND BSDS_BOB_REFRESH=to_date('".$_POST['bsdsbobrefresh']."')";
					}
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
				for ($n = 1; $n <= 4; $n++) {
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
							AND STATUS='".$_POST['viewtype']."' AND SECT='".$n."'";
							if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
								$query.=" AND BSDS_BOB_REFRESH='".$_POST['bsdsbobrefresh']."'";
							}
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

			if ($_POST['viewtype']!="POST"){
				// UPDATE THE CHANGEDATE
				if ($_POST['bsdskey']!=''){
					$query4 = "UPDATE BSDS_GENERALINFO set CHANGE_DATE=SYSDATE, DESIGNER_UPDATE='".$guard_username."' WHERE BSDSKEY='".$_POST['bsdskey']."'";
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
			}else{
				// UPDATE THE CHANGEDATE
				if ($_POST['bsdskey']!=''){
					$query4 = "UPDATE BSDS_GENERALINFO set UPDATE_AFTER_COPY=SYSDATE, UPDATE_BY_AFTER_COPY='$guard_username' WHERE BSDSKEY='".$_POST['bsdskey']."'";
					//echo "$query4 <br>";
					$stmt2 = parse_exec_free($conn_Infobase, $query4, $error_str);
					if (!$stmt2) {
						die_silently($conn_Infobase, $error_str);
					}else{
						$message.="BSDS UPDATE DATE adapted!<br>";
					}
					OCICommit($conn_Infobase);

					$_POST['band']=$_POST['band'];
				}else{
					echo 'Infobase lost the BSDSKEY. Please contact Infobase admin asap!';
					die;
				}
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

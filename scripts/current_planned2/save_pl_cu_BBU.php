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

	$tabletype="BBU";

/*************************
// PLANNED SAVE OR UPDATE
*************************/
	$check_planned_exists_BBU=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],'',$_POST['viewtype'],$_POST['donor']);
	echo 'check_planned_exists_BBU:'.$check_planned_exists_BBU;
	die;

	if(empty($ERROR_MESSAGE)){

		

			$pl_COMMENTS=str_replace("'","''",$_POST['pl_COMMENTS']);
			if ($check_planned_exists_UMTS==0){
				$query1 = "INSERT INTO BSDS_PL_".$tabletype." VALUES ('".$_POST['bsdskey']."',SYSDATE,'".$_POST['pl_POWERSUP']."','".$_POST['pl_CABTYPE']."',
				'".$_POST['pl_IPB']."','".$_POST['pl_PSU']."', '".$_POST['pl_TXBHW']."', '".$_POST['pl_TXBSW']."',
				'".$_POST['pl_RAXBHW']."', '".$_POST['pl_RAXBSW']."','".$_POST['pl_MBPS']."',
				'".$pl_COMMENTS."','".$_POST['RAXEHW']."', '".$_POST['RAXESW']."', '".$_POST['HSTXHW']."', '".$_POST['HSTXSW']."',
				'".$_POST['PLAYSTATION']."', '".$_POST['SERVICE']."','".$_POST['band']."','".$_POST['viewtype']."',";
				if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
					$query1.="'".$_POST['bsdsbobrefresh']."',";
				}else if ($_POST['viewtype']=="PRE"){
					$query1.="'',";
				}
				if ($_POST['band']=="L18" or $_POST['band']=="L26" or $_POST['band']=="L8"){
					$query1 .= "'".$_POST['pl_BPL']."','".$_POST['pl_BPC']."','".$_POST['pl_BPK']."','".$_POST['pl_CC']."','".$_POST['pl_BPN2']."','".$_POST['pl_PM0']."','".$_POST['pl_FS5']."','".$_POST['pl_RECT']."')";
				}else if ($_POST['band']=="U21" or $_POST['band']=="U9"){
					$query1 .= "'".$_POST['pl_BPC']."','".$_POST['pl_BPK']."','".$_POST['pl_CC']."','".$_POST['pl_BPN2']."','".$_POST['pl_PM0']."','".$_POST['pl_FS5']."','".$_POST['pl_RECT']."','".$_POST['pl_BPL']."')";
				}
				$action="saved";
			}else{
				$query1= "UPDATE BSDS_PL_".$tabletype." SET
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
					$query1 .= "BPL='".$_POST['pl_BPL']."',BPC='".$_POST['pl_BPC']."',BPK='".$_POST['pl_BPK']."',CC='".$_POST['pl_CC']."',BPN2='".$_POST['pl_BPN2']."',PM0='".$_POST['pl_PM0']."',FS5='".$_POST['pl_FS5']."',RECT='".$_POST['pl_RECT']."'";
				}else if ($_POST['band']=="U21" or $_POST['band']=="U9"){
					$query1 .= "BPC='".$_POST['pl_BPC']."',BPK='".$_POST['pl_BPK']."',CC='".$_POST['pl_CC']."',BPN2='".$_POST['pl_BPN2']."',PM0='".$_POST['pl_PM0']."',FS5='".$_POST['pl_FS5']."',RECT='".$_POST['pl_RECT']."'";
				}
				$query1 .=" WHERE BSDSKEY= '".$_POST['bsdskey']."' AND TECHNO='".$_POST['band']."' AND STATUS='".$_POST['viewtype']."'";
				if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
						$query1.=" AND BSDS_BOB_REFRESH='".$_POST['bsdsbobrefresh']."'";
				}
				$action="updated";
			}
			//echo $query."<br>";
			$stmt = parse_exec_free($conn_Infobase, $query1, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message.="PL DATA has been $action.<br>";
				OCICommit($conn_Infobase);
			}


			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			for ($n = 1; $n <= 6; $n++){ 

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
					OCICommit($conn_Infobase);
					$sectors.=$n."&nbsp;";
				}
			}
			$message.="PLANNED DATA '".$action."' for ".$_POST['band']." sectors ".$sectors."!<br>";
		
		// UPDATE THE CHANGEDATE
		$query = "UPDATE BSDS_GENERALINFO2 set CHANGE_DATE= SYSDATE, DESIGNER_UPDATE='".$guard_username."' WHERE BSDSKEY='".$_POST['bsdskey']."'";
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

if ($warning or $alert){
	$res["responsedata"] = $warning;
	$res["responsetype"]="error";
	echo json_encode($res);
}

?>
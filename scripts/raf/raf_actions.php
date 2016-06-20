<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
//require_once("../procedures/cur_plan_procedures.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/phpmailer/class.phpmailer.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['action']=="insert_new_raf"){

	if (!$_POST['justification']){
		$message.= "You need to provide a JUSTIFICATION for your RAF!<br>";
	}

	if (substr($_POST['justification'],0,7)=="Minimum"){
		$message.= "You need to provide a JUSTIFICATION for your RAF. Remove current txt!<br>";
	}
	if (!$_POST['sitenum'] || strlen(!$_POST['sitenum'])>4){
		$message.= "You need to provide the SITE NUMBER for your RAF. (Max 4 characters)!<br>";
	}

	if ($_POST['buffer']==1){
		if ($_POST['RADIO_FUND_G9']=="G9"){
		$funding="G9,".$funding;
		}
		if ($_POST['RADIO_FUND_G18']=="G18"){
			$funding="G18,".$funding;
		}
		if ($_POST['RADIO_FUND_U9']=="U9"){
			$funding="U9,".$funding;
		}
		if ($_POST['RADIO_FUND_U21']=="U21"){
			$funding="U21,".$funding;
		}
		if ($_POST['RADIO_FUND_L8']=="L8"){
			$funding="L8,".$funding;
		}
		if ($_POST['RADIO_FUND_L18']=="L18"){
			$funding="L18,".$funding;
		}
		if ($_POST['RADIO_FUND_L26']=="L26"){
			$funding="L26,".$funding;
		}
		if ($_POST['RADIO_FUND_ANT']=="ANT"){
			$funding="ANT,".$funding;
		}
		if ($_POST['RADIO_FUND_CTX']=="CTX"){
			$funding="CTX,".$funding;
		}
		if ($_POST['RADIO_FUND_CTX']=="CAB"){
			$funding="CTX,".$funding;
		}
		if ($_POST['RADIO_FUND_EXISTING']=="EXISTING"){
			$funding="EXISTING,".$funding;
		}
		if ($_POST['RADIO_FUND_CWK']=="CWK"){
			$funding="CWK,".$funding;
		}
		if ($_POST['RADIO_FUND_DISM']=="DISM"){
			$funding="DISM,".$funding;
		}
		$funding=substr($funding,0,-1);
		$buffer=1;
	}else{
		$buffer=0;
	}

	if ($_POST['region']=="CT"){
		$funding="G18, U21, L18";
	}
	if ($_POST['buffer']=="1" && $_POST['bufferchangeallowed']=="yes" && $_POST['RADIO_FUND_G9']=="" && $_POST['RADIO_FUND_G18']==""
		&& $_POST['RADIO_FUND_U9']=="" && $_POST['RADIO_FUND_U21']=="" && $_POST['RADIO_FUND_L8']==""
		&& $_POST['RADIO_FUND_L18']=="" && $_POST['RADIO_FUND_L26']=="" && $_POST['RADIO_FUND_ANT']==""
		&& $_POST['RADIO_FUND_CTX']=="" && $_POST['RADIO_FUND_CWK']=="" && $_POST['RADIO_FUND_DISM']==""
		&& $_POST['RADIO_FUND_EXISTING']==""){
		$message.= "You need to provide the funded technologies when buffer site!<br>";
	}

	if ($message){
		$res["responsedata"] = $message;
		$res["responsetype"]="error";
		if ($_POST['rafid']){
			$res["typeupdate"]="update";
		}else{
			$res["typeupdate"]="insert";
		}
		echo json_encode($res);
	}else{
		$SITEID=$_POST['region'].$_POST['sitenum'];

		if ($_POST['rafid']){
			$query = "UPDATE BSDS_RAFV2 SET
	       	SITEID = '".$SITEID."',";
	       	if ($_POST['buffer']==1){
	       		if ($_POST['type']=="CTX Upgrade" or $_POST['type']=="MSH Upgrade"){
					$query.="OTHER_INP='NA',
					RADIO_INP='NA',ACQ_PARTNER='BASE',CON_PARTNER='NOT OK',";
				}else if ($_POST['type']!="New Indoor" && $_POST['type']!="IND Upgrade"){
					$query.="RADIO_INP='OK',
					RADIO_INP_DATE=SYSDATE,
					RADIO_INP_BY='".$guard_username."',";
					$query.="ACQ_PARTNER='NA',";
				}else{
					$query.="ACQ_PARTNER='NA',";
				}
				if ($_POST['type']=="CWK Upgrade" or $_POST['type']=="MSH Upgrade" or $_POST['type']=="CTX Upgrade"){
					$query.="RF_PAC='NA',";
				}

				if ($_POST['type']=="MSH Upgrade"){
					$funding="MSH";
					$query.="TXMN_INP='NA',
			   		PARTNER_INP='NA',
			   		TXMN_ACQUIRED='NA,
			   		PARTNER_ACQUIRED='NA',
			   		NET1_FUND='NA',
			   		NET1_ACQUIRED='NA',";
			    }else if ($_POST['type']=="New Indoor" or $_POST['type']=="IND Upgrade"){
					$query.="
					RADIO_INP='NOT OK',
			   		RADIO_INP_BY='',
			   		RADIO_INP_DATE='',
					TXMN_INP='NOT OK',
			   		TXMN_INP_DATE='', 
			   		TXMN_INP_BY='',
			   		PARTNER_INP='OK',
			   		PARTNER_ACQUIRED='OK',
			   		TXMN_ACQUIRED='OK',
			   		TXMN_ACQUIRED_DATE=SYSDATE,";
			   	 }else if ($_POST['type']=="DISM Upgrade"){
					$query.="
					RADIO_INP='NA',
			   		RADIO_INP_BY='',
			   		RADIO_INP_DATE='',
					TXMN_INP='NA',
			   		TXMN_INP_DATE='', 
			   		TXMN_INP_BY='',
			   		RADIO_INP='NA',
			   		RADIO_INP_BY='',
			   		RADIO_INP_DATE='',
			   		PARTNER_INP='NA',
			   		PARTNER_INP_DATE='', 
			   		PARTNER_INP_BY='',
			   		BCS_NET1='NA',
			   		NET1_LBP='NA',
			   		PARTNER_ACQUIRED='NA',
				   	PARTNER_ACQUIRED_DATE='',
				   	PARTNER_ACQUIRED_BY='',
			   		TXMN_ACQUIRED=='NA',
			   		TXMN_ACQUIRED_DATE='',
			   		TXMN_ACQUIRED_BY='',
			   		NET1_ACQUIRED='NA',
			   		PARTNER_RFPAC='NOT OK',
			   		PARTNER_RFPAC2='NOT OK',
			   		RF_PAC='NA',
			   		RADIO_FUND='DISM',
			   		NET1_FUND='NA',";
				}else{
					$query.="TXMN_INP='OK',
			   		PARTNER_INP='OK',
			   		PARTNER_ACQUIRED='OK',
			   		TXMN_ACQUIRED='OK',";
				}

				$query.="NET1_A304='NA',";
				$query.="BCS_NET1='NA',";
				$query.="COF_ACQ='NA',";
			}

			if ($_POST['bufferchangeallowed']=="yes"){
				$query.="RADIO_FUND='".$funding."',
				RADIO_FUND_DATE=SYSDATE, 
				RADIO_FUND_BY='".$guard_username."',";
			}

	       	$query.="UPDATE_DATE = SYSDATE,
	       	UPDATE_BY     = '".$guard_username."',
	       	TYPE          = '".$_POST['type']."',
	       	JUSTIFICATION = '".escape_sq($_POST['justification'])."',
	       	BUDGET_ACQ = '".$_POST['budget_acq']."',
	       	BUDGET_CON = '".$_POST['budget_con']."',
	       	RFINFO = '".$_POST['rfinfo']."',
			COMMERCIAL = '".$_POST['commercial']."',
			BUFFER = '".$buffer."',
			EVENT='".$_POST['EVENT']."' 
			WHERE RAFID='".$_POST['rafid']."'";

			//echo $query;
		}else{

			//We first set the default values
			if (substr_count($guard_groups, 'Base_other')==1){
				$other_input="OK";
				$other_input_by=$guard_username;
				$other_input_date="SYSDATE";
			}else{
				$other_input="NA";
				$other_input_by="";
				$other_input_date="''";
			}
			$radio_input="NOT OK";
			$radio_input_by="";
			$radio_input_date="''";
			$txmn_input="NOT OK";				
			$txmn_input_by="";
			$txmn_input_date="''";
			$cof_acq="NOT OK"; 
			$cof_acq_by="";
			$cof_acq_date="''";
			$acq_partner="NOT OK"; 
			$acq_partner_by="";
			$acq_partner_date="''";
			$net1_link="NOT OK"; 
			$net1_link_by="";
			$net1_link_date="''";
			$partner_input="NOT OK";
			$partner_input_by='';
			$partner_input_date="''";
			$bcs_net1="NOT OK";
			$bcs_net1_by="";
			$bcs_net1_date="''";
			$bcs_tx_inp="NOT OK";
			$bcs_rf_inp="NOT OK";
			$net1_lbp="NOT OK";
			$partner_acquired="NOT OK";
			$partner_acquired_by="";
			$partner_acquired_date="''";
			$txmn_acquired="NOT OK";
			$txmn_acquired_by="";
			$txmn_acquired_date="''";
			$net1_acquired="NOT OK";
			$radio_fund="NOT OK";
			$radio_fund_by="";
			$radio_fund_date="''";
			$cof_con="NOT OK"; 
			$cof_con_by="";
			$cof_con_date="''";
			$con_partner="NOT OK"; 
			$con_partner_by="";
			$con_partner_date="''";
			$net1_fund="NOT OK";
			$PARTNER_RFPAC="NOT OK";
			$PARTNER_RFPAC_BY="";
			$PARTNER_RFPAC_date="''";
			$rf_pac="NOT OK";
			$rf_pac_by="";
			$rf_pac_date="''";
			$net1_pac="NOT OK";
			$net1_fac="NOT OK";
			$NET1_A304="NOT OK";
			$BP_NEEDED="NA";
			$BP_NEEDED_by="";
			$BP_NEEDED_date="''";
			$partner_design='NA';
			$partner_design_date="''";

			if ($_POST['type']=="MOV Upgrade" or $_POST['type']=="TECHNO Upgrade" or $_POST['type']=="MOD Upgrade"  or $_POST['type']=="New Macro (v2)" or substr_count($_POST['type'], 'New')=="1" ){
				$partner_design='NOT OK';

		    }
		    if (substr_count($_POST['type'], 'Upgrade')=="1"){ //Means for all upgrades
		    	$NET1_A304='NA'; //We only have A304 for NB
		    }

			if ($_POST['buffer']==1 or $_POST['type']=="DISM Upgrade"){
				$buffer=1;
				$other_input="NA";
				$other_input_date="SYSDATE";
				$radio_input="NOT OK";
				$radio_input_by='';
				$radio_input_date="''";
				if ($_POST['type']=="New Indoor" or $_POST['type']=="IND Upgrade"){
					$txmn_input="NOT OK";				
					$txmn_input_by="";;
					$txmn_input_date="''";
					$radio_input="NOT OK";
					$radio_input_by='';
					$radio_input_date="''";
				}else{
					$txmn_input="NA";				
					$txmn_input_date="''";
				}
				$cof_acq="NA"; 
				$cof_acq_by="";
				$cof_acq_date="''";
				$acq_partner="NA";
				$acq_partner_by="";
				$acq_partner_date="''";
				$partner_input="NA";
				$partner_input_by='';
				$partner_input_date="''";
				$bcs_net1="NA";
				$bcs_net1_by='';
				$bcs_net1_date="''";
				if ($_POST['type']=="DISM Upgrade"){
					$net1_lbp="NA";
					$radio_fund="DISM";
					$radio_fund_by='Infobase';
					$radio_fund_date="SYSDATE";
				}else{
					$net1_lbp="NOT OK";
					$radio_fund=$funding;
					$radio_fund_by=$guard_username;
					$radio_fund_date="SYSDATE";
				}
				$partner_acquired="NA";
				$partner_acquired_by='';
				$partner_acquired_date="''";
				$txmn_acquired="NA";
				$txmn_acquired_by="";
				$txmn_acquired_date="''";
				$net1_acquired="NA";
				$radio_fund=$funding;
				
				if ($_POST['buffer']==1 AND $_POST['type']=="DISM Upgrade"){
					$net1_fund="NA";
				}else{
					$net1_fund="NOT OK";
				}
				if ($_POST['type']=="DISM Upgrade"){
					$net1_lbp="NA";
					$rf_pac="NA";
					$rf_pac_by="";
					$rf_pac_date="''";
					$PARTNER_RFPAC="NA";
					$PARTNER_RFPAC_BY="";
					$PARTNER_RFPAC_date="''";
				}else{
					$net1_lbp="NOT OK";
					$rf_pac="NOT OK";
					$rf_pac_by="";
					$rf_pac_date="''";
					$PARTNER_RFPAC="NOT OK";
					$PARTNER_RFPAC_BY="";
					$PARTNER_RFPAC_date="''";
				}
				
				$net1_pac="NOT OK";
				$net1_fac="NOT OK";
				$NET1_A304="NA";

				$buffer_message=" Don't forget to add the TRX requirements!";
			}else{
				$buffer=0;
			}
			if($_POST['rfinfo']=="Mini RPT Coiler" or $_POST['rfinfo']=="Mini RPT Andrew"){
				$txmn_acquired="NA";
				$txmn_input="NA";
			}
			if ($_POST['type']=="Dismantling"){
		   
		        $txmn_acquired="NA";
		    }
			if (substr_count($_POST['type'], 'Upgrade')=="1"){ //BCS not applicable for Upgrades
				$BCS_NET1="NA";
			}
			if ($_POST['type']=="CTX Upgrade"){
				$other_input="NA";
				$radio_input="NA";
				$acq_partner="NOT OK";
				$rf_pac="NA";

			}
			if ($_POST['type']=="CWK Upgrade"){
				$rf_pac="NA";
			}

			if ($_POST['type']=="MSH Upgrade"){
				$other_input="NA";
				$radio_input="NA";
				$txmn_input="NA";
				$NET1_LINK="NOT OK";
				$partner_input="NA";
				$bcs_net1="NA";
				
				$partner_acquired="NA";
				$txmn_acquired="NA";
				$net1_acquired="NA";
				$radio_fund="MSH";
				$net1_fund="NA";
				$rf_pac="NA";
			}

			if ($_POST['type']=="MOV Upgrade"){
				$bcs_rf_inp="NA";
				$bcs_tx_inp="NA";
				$bcs_net1="NA";
			}
			/*
			removed for ALL areas
			if ($_POST['region']=="MT"){
				$bcs_net1="NA";
				$bcs_tx_inp="NA";
				$bcs_rf_inp="NA";
			}
				*/
			if ($_POST['type']=="New Indoor"){
				$cof_acq="NA"; 
			}

			if ($_POST['type']=="New All Areas"){
				$other_input="NA";
				$NET1_ACQUIRED="NA";
				if ($_POST['region']=="CT"){
					$acq_partner='BENCHMARK';
				}elseif ($_POST['region']=="MT"){
					$acq_partner='TECHM';
				}
				$cof_acq="NA";
			}

			$query = "INSERT INTO INFOBASE.BSDS_RAFV2 (
			   RAFID, SITEID, CREATION_DATE,CREATED_BY, UPDATE_DATE, UPDATE_BY,
			   CANDIDATE, BUFFER,
			   TYPE, RFINFO, JUSTIFICATION, COMMERCIAL,
			   OTHER_INP, OTHER_INP_DATE, OTHER_INP_BY, 
			   RADIO_INP, RADIO_INP_DATE, RADIO_INP_BY, 
			   TXMN_INP, TXMN_INP_DATE, TXMN_INP_BY, 
			   NET1_LINK, NET1_LINK_DATE, NET1_LINK_BY, 
			   ACQ_PARTNER, ACQ_PARTNER_DATE, ACQ_PARTNER_BY, 
			   PARTNER_INP, PARTNER_INP_DATE, PARTNER_INP_BY, 
			   BCS_NET1, BCS_NET1_DATE, BCS_NET1_BY, 
			   BCS_RF_INP,BCS_TX_INP,
			   NET1_LBP,
			   PARTNER_ACQUIRED, PARTNER_ACQUIRED_DATE, PARTNER_ACQUIRED_BY, 
			   TXMN_ACQUIRED, TXMN_ACQUIRED_DATE, TXMN_ACQUIRED_BY,
			   NET1_ACQUIRED,
			   RADIO_FUND, RADIO_FUND_DATE, RADIO_FUND_BY,
			   CON_PARTNER, CON_PARTNER_DATE, CON_PARTNER_BY, 
			   NET1_FUND,			   
			   PARTNER_RFPAC, PARTNER_RFPAC_DATE, PARTNER_RFPAC_BY,
			   RF_PAC, RF_PAC_DATE, RF_PAC_BY,
			   NET1_PAC,NET1_FAC, 
			   BUDGET_ACQ, BUDGET_CON, DELETED, LOCKEDD,
			   COF_ACQ,COF_ACQ_BY,COF_ACQ_DATE,
			   COF_CON,COF_CON_BY,COF_CON_DATE,
			   NET1_A304, BP_NEEDED, BP_NEEDED_DATE, BP_NEEDED_BY,
			   PARTNER_DESIGN,PARTNER_DESIGN_DATE,PARTNER_DESIGN_BY,
			   EVENT
			   )
			VALUES ('' ,'".$SITEID."' , SYSDATE, '".$guard_username."', '', '',
			    '".$_POST['candidate']."','".$buffer."',
			    '".$_POST['type']."','".escape_sq($_POST['rfinfo'])."','".escape_sq($_POST['justification'])."','".escape_sq($_POST['commercial'])."', 
			    '".$other_input."', ".$other_input_date.",'".$other_input_by."',
			    '".$radio_input."',".$radio_input_date.",'".$radio_input_by."',
			    '".$txmn_input."',".$txmn_input_date.",'".$txmn_input_by."',
			    '".$net1_link."',".$net1_link_date.",'".$net1_link_by."',
			    '".$acq_partner."',".$acq_partner_date.",'".$acq_partner_by."',
			    '".$partner_input."',".$partner_input_date.",'".$partner_input_by."',
			    '".$bcs_net1."',".$bcs_net1_date.",'".$bcs_net1_by."',
			    '".$bcs_rf_inp."','".$bcs_tx_inp."',
			    '".$net1_lbp."',
			    '".$partner_acquired."',".$partner_acquired_date.",'".$partner_acquired_by."',
			    '".$txmn_acquired."',".$txmn_acquired_date.",'".$txmn_acquired_by."',
			    '".$net1_acquired."',
			    '".$radio_fund."',".$radio_fund_date.",'".$radio_fund_by."',
			    '".$con_partner."',".$con_partner_date.",'".$con_partner_by."',
			    '".$net1_fund."',
			    '".$PARTNER_RFPAC."',".$PARTNER_RFPAC_date.",'".$PARTNER_RFPAC_BY."',
				'".$rf_pac."',".$rf_pac_date.",'".$rf_pac_by."',
				'".$net1_pac."','".$net1_fac."',
				'".$_POST['budget_acq']."','".$_POST['budget_con']."','No','No',
				'".$cof_acq."','".$cof_acq_by."',".$cof_acq_date.",
				'".$cof_con."','".$cof_con_by."',".$cof_con_date.",
				'".$NET1_A304."','".$BP_NEEDED."',".$BP_NEEDED_date.",'".$BP_NEEDED_by."',
				'".$partner_design."',".$partner_design_date.",'".$partner_design_by."',
				'".$_POST['EVENT']."')";
		}
		//echo $query;
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
			if($_POST['rafid']==''){
				$message= "New RAF has succesfully been created!".$buffer_message;
				$res["responsetype"]="info";
				$res["typeupdate"]="insert";
			}else{
				$message= "RAF has succesfully been updated!".$buffer_message;
				$res["responsetype"]="info";
				$res["typeupdate"]="update";
			}
			$res["responsedata"] = $message;

			$query = "Select MAX(RAFID) AS RAFID from BSDS_RAFV2 WHERE SITEID = '".$SITEID."'";
			$stmt6 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
			if (!$stmt6) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt6);
			}

			if ($_POST['buffer']==1){
				
				if ($_POST['rafid']==""){
					$query = "INSERT INTO BSDS_RAF_PARTNER (RAFID,FINAL_RF,FINAL_MICROWAVE,FINAL_CAB,FINAL_BTS,FINAL_OTHER) VALUES ('".$res6['RAFID'][0]."','Yes','Yes','Yes','Yes','Yes')";
					$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}
				}
			}

			if ($_POST['region']=="CT" && $_POST['rafid']==""){
				
					$query = "INSERT INTO BSDS_RAF_TXMN (RAFID,SPECIFIC_TXMN) VALUES ('".$res6['RAFID'][0]."','TXMN requirements as confirmed during site visit')";
					$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}
			}

			if (($_POST['region']=="MT" && $_POST['rafid']=="") or $_POST['type']=='New All Areas'){ //=> also for all areas
				$query = "INSERT INTO BSDS_RAF_PO (RAFID,POPR,ACQCON,INSERTDATE) VALUES ('".$res6['RAFID'][0]."','NA','ACQ',SYSDATE)";
				$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
				$query = "INSERT INTO BSDS_RAF_PO (RAFID,POPR,ACQCON,INSERTDATE) VALUES ('".$res6['RAFID'][0]."','NA','CON',SYSDATE)";
				$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
			}
			if ($_POST['type']=="New All Areas" && $_POST['rafid']==""){ //MT & CT sites
				$query = "INSERT INTO BSDS_RAF_PO (RAFID,POPR,CONFIRMED,CONFIRMED_PARTNER,ACQCON,INSERTDATE) VALUES ('".$res6['RAFID'][0]."','NA','CONFIRMED','CONFIRMED','ACQ',SYSDATE)";
				$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
			}
			if ($_POST['type']=="MOV Upgrade" && $_POST['rafid']==""){
	            $text="LOS of existing link(s) to be checked/confirmed for new dish position(s). If no LOS on the new dish position is possible, please inform BASE SDM and BASE TX. The MOV UPG is put ON-HOLD until the issue is resolved (confirmed by BASE)";
	            $query = "INSERT INTO INFOBASE.BSDS_RAF_TXMN (RAFID,UPG_DATE,UPG_BY,SPECIFIC_TXMN)
	              VALUES ( '".$res6['RAFID'][0]."', SYSDATE, '".$guard_username."','".escape_sq($text)."')";
	            //echo $query;
	            $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	            if (!$stmt) {
	              die_silently($conn_Infobase, $error_str);
	            }else{
	              OCICommit($conn_Infobase);
	            }
	        }

			$res["siteID"]=$SITEID;
			echo json_encode($res);
		}
	}
}


if ($_POST['action']=="attach_BSDSKEY"){
	//We remove current links to this RAFID
	$query = "UPDATE BSDS_GENERALINFO2 SET RAFID ='' WHERE RAFID='".escape_sq($_POST['id'])."'";
	//echo "$query";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
	$query = "UPDATE BSDS_GENERALINFO2 SET RAFID ='".escape_sq($_POST['id'])."' WHERE BSDSKEY='".$_POST['value']."'";
	//echo "$query";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
	$query = "UPDATE MASTER_REPORT SET BSDSKEY ='".$_POST['value']."' WHERE IB_RAFID='".escape_sq($_POST['id'])."'";
	//echo "$query";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
}else if ($_POST['action']=="change_net1link"){

	if ($_POST['field']!='NET1_PAC_REJECT' && $_POST['field']!='NET1_FAC_REJECT'){
		if ($_POST['field']=="RADIO_FUND"){
			foreach ($_POST['value'] as $value) {
				$val=$value.",".$val;
			};
			$val=substr($val,0,-1);
		}else{
			$val=$_POST['value'];
		}

		if($_POST['field']=="RF_PAC" && $_POST['value']!="REJECTED"){
			$query = "Select RAFID FROM BSDS_RAF_RADIO WHERE RAFID = '".$_POST['id']."' AND COVERAGE_SOCIAL IS NOT NULL AND AREA_SOCIAL IS NOT NULL AND PACCOMMENTS IS NOT NULL";
			//echo $query;
			$stmt= parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			}else{
				OCIFreeStatement($stmt);
			}

			if (count($res['RAFID'])==0){
				$res["msg"] = "You can not set to OK as no SOCIAL info has been entered (all 3 fields must be provided)!";
				$res["rtype"]="error";
				$res["success"]=false;
				echo json_encode($res);
				return;
			}
		}

		if(($_POST['field']=="COF_ACQ" or $_POST['field']=="COF_CON") && trim(strtoupper($_POST['value']))!="REJECTED" && trim(strtoupper($_POST['value']))!="REJECT" && trim(strtoupper($_POST['value']))!="NOT OK") {
			if ($_POST['field']=="COF_CON"){
				$acqcon='CON';
			}else{
				$acqcon='ACQ';
			}

			if (substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'Admin')==1){ //The check doesn't need to happen for the partner
				$query = "Select RAFID FROM BSDS_RAFV2 WHERE RAFID = '".$_POST['id']."' AND BUDGET_".$acqcon." IS NOT NULL";
				//echo $query;
				$stmtCOF= parse_exec_fetch($conn_Infobase, $query, $error_str, $resCOF);
				if (!$stmtCOF) {
					die_silently($conn_Infobase, $error_str);
				 	exit;
				}else{
					OCIFreeStatement($stmtCOF);
				}

				if (count($resCOF['RAFID'])==0 && substr_count($guard_groups, 'Admin')!=1){
					$res["msg"] = "You can not set to OK as NO BUDGET info has stored";
					$res["rtype"]="error";
					$res["success"]=false;
					echo json_encode($res);
					return;
				}
			}
			//echo $_POST['field'];
			$query = "Select RAFID FROM BSDS_RAF_COF WHERE RAFID = '".$_POST['id']."' AND ACQCON='".$acqcon."'";
			//echo $query;
			$stmtCOF= parse_exec_fetch($conn_Infobase, $query, $error_str, $resCOF);
			if (!$stmtCOF) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			}else{
				OCIFreeStatement($stmtCOF);
			}

			if (count($resCOF['RAFID'])==0 && substr_count($guard_groups, 'Admin')!=1){
				$res["msg"] = "You can not set to OK as NO COF info has been stored";
				$res["rtype"]="error";
				$res["success"]=false;
				echo json_encode($res);
				return;
			}
		}


		if ($_POST['field']=="BP_NEEDED"){ 
			
			$query = "SELECT BP_NEEDED_REASON FROM BSDS_RAF_PARTNER WHERE RAFID='".$_POST['id']."'";
			//echo $query;
			$stmt= parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			}else{
				OCIFreeStatement($stmt);
			}

			if ($res['BP_NEEDED_REASON'][0]!=''){
				$may_update='yes';
				//echo "----1";
				if ($_POST['value']=='BASE BP NO'){ //Is only for AZdmin user

					$query = "SELECT BAND_900, BAND_1800, BAND_UMTS, BAND_UMTS900, BAND_LTE800, BAND_LTE1800, BAND_LTE2600  FROM BSDS_RAF_RADIO WHERE RAFID='".$_POST['id']."'";
					//echo $query;
					$stmt= parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					}else{
						OCIFreeStatement($stmt);
					}
					if($res['BAND_LTE2600'][0]==1){
						$band.='L26,';
					}
					if($res['BAND_LTE1800'][0]==1){
						$band.='L18,';
					}
					if($res['BAND_LTE800'][0]==1){
						$band.='L8,';
					}
					if($res['BAND_UMTS900'][0]==1){
						$band.='U9,';
					}
					if($res['BAND_UMTS'][0]==1){
						$band.='U21,';
					}
					if($res['BAND_1800'][0]==1){
						$band.='G18,';
					}
					if($res['BAND_900'][0]==1){
						$band.='G9,';
					}


					$query = "SELECT TASK_NAME FROM VW_RAF_PROCESSTAKS WHERE RAFTYPE='".$_POST['raftype']."' AND PHASE='skip'";
					//echo $query;
					$stmt= parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					}else{
						OCIFreeStatement($stmt);
						$amount_of_skip=count($res['TASK_NAME']);
					}
					for ($i = 0; $i <$amount_of_skip; $i++) { 
						$tasks.=$res['TASK_NAME'][$i]."='NA',";
					}

					$query="UPDATE BSDS_RAFV2 SET
					UPDATE_DATE = SYSDATE,
			       	UPDATE_BY     = '".$guard_username."',
			       	".$tasks."
			       	RADIO_FUND='".substr($band,0,-1)."'
					WHERE RAFID='".$_POST['id']."'";
					//echo $query;
					$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}

					//SAVE IN HISTORY LOG
					$query3="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['id']."',SYSDATE,'".str_replace("'", "", $tasks).substr($band,0,-1)."','".$guard_username."','BP_NEEDED: BASE BP NO')";
					//echo $query3;
					$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
					if (!$stmt3) {
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}
				}else if ($_POST['value']=='BASE BP YES'){

					//echo "----2";
					$query="UPDATE BSDS_RAFV2 SET UPDATE_DATE = SYSDATE, UPDATE_BY = '".$guard_username."', RADIO_INP='NOT OK',
				       	TXMN_INP='NOT OK', RADIO_FUND='NOT OK' WHERE RAFID='".$_POST['id']."'";
					//echo $query;
					$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}

					$query3="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['id']."',SYSDATE,'RADIO_INP=NA,TXMN_INP=NOT OK,RADIO_FUND=NOT OK,NET1_ACQUIRED=NOT OK','".$guard_username."','BP_NEEDED: BASE BP YES')";
					echo $query3;
					$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
					if (!$stmt3) {
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}

				}
			}else if ($_POST['value']!='NA' AND $_POST['value']!='NOT OK' AND $_POST['value']!='REJECTED' && substr_count($guard_groups, 'Admin')!=1){
				$res["msg"] = "The reason why ACQ IS REQUIRED or NOT REQUIRED has to be inserted into RAF 0!";
				$res["rtype"]="error";
				$may_update='no';
				echo json_encode($res);
			}				
		}	

		//Here WE HANDLE THE REJECTIONS
		if ((trim(strtoupper($_POST['value']))=="REJECTED" || trim(strtoupper($_POST['value']))=="REJECT") && $_POST['field']!="COF_ACQ" && $_POST['field']!="COF_CON"){
			if ($_POST['field']=="RF_PAC"){
				$extra="PARTNER_RFPAC='REJECTED'";
			}else if ($_POST['field']=="BCS_RF_INP" or $_POST['field']=="BCS_TX_INP"){
				$extra="PARTNER_INP='REJECTED'";

				$query3="UPDATE BSDS_RAF_PARTNER SET BC_PROPOSAL1='No',BC_PROPOSAL2='No',BC_PROPOSAL3='No',BC_PROPOSAL4='No' WHERE RAFID='".$_POST['id']."'";
				//echo $query3;
				$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
				if (!$stmt3) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}

			}else if ($_POST['field']=="NET1_LINK"){
				if ($_POST['type']=="CTX Upgrade"){
					$extra="TXMN_INP='REJECTED', NET1_LINK='NOT OK'";
				}else{
					$extra="RADIO_INP='REJECTED', NET1_LINK='NOT OK'";
				}
			}else if ($_POST['field']=="NET1_FUND"){
					$extra="RADIO_FUND='REJECTED'";
			}else if ($_POST['field']=="PARTNER_ACQUIRED"){
					$extra="PARTNER_INP='REJECTED'";
			}else if ($_POST['field']=="TXMN_ACQUIRED"){
					$extra="PARTNER_ACQUIRED='REJECTED'";
			}else if ($_POST['field']=="NET1_AQUIRED"){
					$extra="PARTNER_ACQUIRED='REJECTED'";
			}else if ($_POST['field']=="NET1_PAC"){
					$extra="PARTNER_VALREQ='REJECTED'";
			}else if ($_POST['field']=="NET1_FAC"){
					$extra="PARTNER_VALREQ='REJECTED'";
			}
			$query = "UPDATE BSDS_RAFV2 SET ".$_POST['field']." ='NOT OK',".$extra;
		}else if (trim(substr($_POST['field'],-7))=="_REJECT"){
			$query = "UPDATE BSDS_RAFV2 SET ".$_POST['field']." ='".escape_sq($val)."<br>' ||".$_POST['field']; //APPEND reason for rejection
		}else{
			$query = "UPDATE BSDS_RAFV2 SET ".$_POST['field']." ='".escape_sq($val)."'";
		}

		if (trim($_POST['field'])=="CON_PARTNER" && $_POST['value']!="NOT OK"){
			$query.=",NET1_FUND='AUTOMATIC'";
		}	

		if ((trim(strtoupper($_POST['value']))=="REJECTED" || trim(strtoupper($_POST['value']))=="REJECT") && $_POST['field']=="NET1_LINK"){
			$query .="";
		}else if ($_POST['field']!="NET1_FUND" && $_POST['field']!="NET1_ACQUIRED"){
		$query .= " ,".$_POST['field']."_DATE=SYSDATE,".$_POST['field']."_BY='".$guard_username."'";
		}
		$query .= " WHERE RAFID='".$_POST['id']."'";
		//echo "$query";

		if (trim(substr($_POST['field'],-7))!="_REJECT" && $may_update!='no'){
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			//SAVE IN HISTORY LOG
			$query3="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['id']."',SYSDATE,'".escape_sq($val)."','".$guard_username."','".$_POST['field']."')";
			//echo $query3;
			$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
			if (!$stmt3) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
			echo $val;
		}
		if ($_POST['field']=="NET1_FAC" or $_POST['field']=="PARTNER_VALREQ"){ //NET1_PAC IS NOT here but in an other php function: update_pacfacready
			$query3="INSERT INTO INFOBASE.BSDS_RAF_PACFACHIST (RAFID, ACTION_DATE, STATUS,ACTION_BY,FIELD) VALUES ('".$_POST['id']."',SYSDATE,'".$_POST['value']."','".$guard_username."','".$_POST['field']."')";
			//echo $query3;
			$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
			if (!$stmt3) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
		}	
	}
}else if ($_POST['action']=="save_rejection_reason"){
	//SAVE IN HISTORY LOG
	$query3="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['rafid']."',SYSDATE,'".escape_sq($_POST['value'])."','".$guard_username."','".$_POST['field']."')";
	//echo $query3;
	$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
	if (!$stmt3) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$res["msg"] = "Reason has been inserted.";
		$res["rtype"]="info";
		$res["siteID"]=$_POST['siteID'];
		$res["rafid"]=$_POST['rafid'];
		$res["success"]=true;
		echo json_encode($res);
	}

}else if ($_POST['action']=="delete_material"){
	$query = "DELETE FROM BSDS_RAF_COF WHERE RAFID='".$_POST['rafid']."' AND MATERIAL_CODE='".$_POST['material']."' AND ACQCON='".$_POST['acqcon']."'";

	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$res["msg"] = "MATERIAL ".$_POST['material']." has successfully been deleted";
		$res["type"]="info";
		$res["materialID"]=$_POST['rafid'].$_POST['material'].$_POST['acqcon'];

		$queryIN="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['rafid']."',SYSDATE,'".$_POST['MATERIAL_CODE']."','".$guard_username."','DEL COF MATERIAL CODE ".$_POST['acqcon']."')";
		//echo $queryIN;
		$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
		if (!$stmtIN) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}

		echo json_encode($res);
	}
}

if ($_POST['action']=="delete_raf"){

	$query = "UPDATE INFOBASE.BSDS_RAFV2 SET
	       	DELETED        = 'yes',
	       	DELETE_DATE   = SYSDATE,
	       	DELETE_BY     = '".$guard_username."',
			DELETE_REASON     = '".escape_sq($_POST['delreason'])."'
			WHERE RAFID='".$_POST['rafid']."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$out.="RAF has been succesfully deleted in IB<br>";
	}

	$query3="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['rafid']."',SYSDATE,'yes','".$guard_username."','RAF DELETED')";
		//echo $query3;
		$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
		if (!$stmt3) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}

	if (strlen($_POST['net1link'])==8 or substr($_POST['net1link'],0,2)=='99'){
		$queryUP = "INSERT INTO INFOBASE.NET1UPDATER_STATUS VALUES ('".$_POST['net1link']."','DL',SYSDATE,0,'".$_POST['rafid']."')";
		//echo $queryUP."\r\n";
		$stmtUP = parse_exec_free($conn_Infobase, $queryUP, $error_str);
		if (!$stmtUP) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
			$out.="SITE has been put to DL in NET1<br>";
		}

		$query = "SELECT N1_SITEID,N1_CANDIDATE,N1_UPGNR,N1_NBUP FROM MASTER_REPORT WHERE IB_RAFID='".$_POST['rafid']."'";
		//echo $query;
		$stmt= parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		}else{
			OCIFreeStatement($stmt);
		}
		if (count($res['N1_SITEID'])==1){

			if ($res['N1_NBUP'][0]=='NB' or $res['N1_NBUP'][0]=='NB REPL'){
				$query="INSERT INTO INFOBASE.NET1UPDATER_CSV VALUES ('".$res['N1_SITEID'][0]."','','A998','".date('d-m-Y')."','RAF_DEL',SYSDATE,'0','".substr(escape_sq($_POST['delreason']), 0,1300)."','','".$res['N1_CANDIDATE'][0]."')";
			}else if ($res['N1_NBUP'][0]=='UPG'){
				$query="INSERT INTO INFOBASE.NET1UPDATER_CSV VALUES ('".$res['N1_SITEID'][0]."','".$res['N1_UPGNR'][0]."','U998','".date('d-m-Y')."','RAF_DEL',SYSDATE,'0','".substr(escape_sq($_POST['delreason']), 0,1300)."','','".$res['N1_CANDIDATE'][0]."')";
			}else{
				echo "error when deleting";
				die;
			}
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$out.="U998 has been put to todays date and comments have been added!";
		}else{
			$out.="There is an issue with NET1 for this site, p^lease contact Frederick Eyland!";
		}	
	}
	$res["msg"] = $out;
	$res["type"]="info";
	$res["siteID"]=$_POST['siteID'];
	echo json_encode($res);
}
/*
if ($_POST['action']=="override_raf"){
	$query = "UPDATE INFOBASE.BSDS_RAFV2 SET
	       	OVERRIDE        = 'yes',
	       	OVERRIDE_DATE   = SYSDATE,
	       	OVERRIDE_BY     = '".$guard_username."'
			WHERE RAFID='".$_POST['rafid']."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$res["msg"] = "RAF can now be created";
		$res["type"]="info";
		$res["siteID"]=$_POST['siteID'];
		echo json_encode($res);
	}
}*/
if ($_POST['action']=="undelete_raf"){
	$query = "UPDATE INFOBASE.BSDS_RAFV2 SET
	       	DELETED        = 'no',
	       	DELETE_DATE   = '',
	       	DELETE_BY     = '',
			DELETE_REASON     = '',
			STATUS_CHANGE=''
			WHERE RAFID='".$_POST['rafid']."'";

	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$out.="RAF has been succesfully UN-deleted in IB<br>";
	}

	$query3="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['rafid']."',SYSDATE,'no','".$guard_username."','RAF UNDELETED')";
		//echo $query3;
		$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
		if (!$stmt3) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}

	if (strlen($_POST['net1link'])==8 or substr($_POST['net1link'],0,2)=='99'){
		$queryUP = "INSERT INTO INFOBASE.NET1UPDATER_STATUS VALUES ('".$_POST['net1link']."','IS',SYSDATE,0,'".$_POST['rafid']."')";
		//echo $queryUP."\r\n";
		$stmtUP = parse_exec_free($conn_Infobase, $queryUP, $error_str);
		if (!$stmtUP) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
			$out.="SITE has been put to IS in NET1<br>";
		}

		$query = "SELECT N1_SITEID,N1_CANDIDATE,N1_UPGNR,N1_NBUP FROM MASTER_REPORT WHERE IB_RAFID='".$_POST['rafid']."'";
		//echo $query;
		$stmt= parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		}else{
			OCIFreeStatement($stmt);
		}
		if (count($res['N1_SITEID'])==1){

			if ($res['N1_NBUP'][0]=='NB' or $res['N1_NBUP'][0]=='NB REPL'){
				$query="INSERT INTO INFOBASE.NET1UPDATER_CSV VALUES ('".$res['N1_SITEID'][0]."','','A998','','RAF_DEL',SYSDATE,'0','','','".$res['N1_CANDIDATE'][0]."')";
			}else if ($res['N1_NBUP'][0]=='UPG'){
				$query="INSERT INTO INFOBASE.NET1UPDATER_CSV VALUES ('".$res['N1_SITEID'][0]."','','U998','".date('d-m-Y')."','RAF_DEL',SYSDATE,'0','','','".$res['N1_CANDIDATE'][0]."')";
			}else{
				echo "error when deleting";
				die;
			}
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}

			$out.="Date for U998 and comments have been removed!";
		}else{
			$out.="There is an issue with NET1 for this site, please contact Frederick Eyland!";
		}	
	}
	$res["msg"] = $out;
	$res["type"]="info";
	$res["siteID"]=$_POST['siteID'];
	echo json_encode($res);
}
if ($_POST['action']=="lock_raf"){
	$query = "UPDATE INFOBASE.BSDS_RAFV2 SET
	       	LOCKEDD        = 'yes',
	       	LOCKEDD_DATE   = SYSDATE,
	       	LOCKEDD_BY     = '".$guard_username."',
			LOCKEDD_REASON     = '".$_POST['lockreason']."'
			WHERE RAFID='".$_POST['rafid']."'";
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$res["msg"] = "RAF has successfully been locked";
		$res["type"]="info";
		$res["siteID"]=$_POST['siteID'];
		echo json_encode($res);
	}

	$query3="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['rafid']."',SYSDATE,'yes','".$guard_username."','RAF LOCKED')";
	//echo $query3;
	$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
	if (!$stmt3) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
}
if ($_POST['action']=="unlock_raf"){
	$query = "UPDATE INFOBASE.BSDS_RAFV2 SET
	       	LOCKEDD        = 'no',
	       	LOCKEDD_DATE   = '',
	       	LOCKEDD_BY     = '',
			LOCKEDD_REASON     = '',
			STATUS_CHANGE=''
			WHERE RAFID='".$_POST['rafid']."'";
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$res["msg"] = "RAF has successfully been un-locked";
		$res["type"]="info";
		$res["siteID"]=$_POST['siteID'];
		echo json_encode($res);
	}

	$query3="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['rafid']."',SYSDATE,'no','".$guard_username."','RAF UNLOCKED')";
	//echo $query3;
	$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
	if (!$stmt3) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}
}


if ($_POST['action']=="delete_raffile"){
	unlink($_POST["raffile"]);

/*
}else if ($_POST['action']=="update_poinfo"){

	$query="UPDATE BSDS_RAF_PO SET CONFIRMED_BY='".$guard_username."', CONFIRMED_DATE=SYSDATE, CONFIRMED_ITEMCOST='".$_POST['ITEMCOST']."',
	CONFIRMED='".$_POST['actiontype']."',CONFIRMED_COMMENTS='".escape_sq($_POST['CONFIRMED_COMMENTS'])."' WHERE ID='".$_POST['POID']."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}

	if ($_POST['actiontype']=='REJECTED'){
		$msg="PO info REJECTED, a mail willl be send to Laurence to inform here to delete this PO!";
		$act="REJECTED";
	}else{
		$msg="PO info updated successfully";
		$act="OK";
	}
	$query3 = "Select * FROM BSDS_RAF_PO WHERE RAFID = '".$_POST['RAFID']."' AND ACQCON='CON' AND CONFIRMED!='DELETED'";
	//echo $query3;
	$stmtPO = parse_exec_fetch($conn_Infobase, $query3, $error_str, $resPO);
	if (!$stmtPO) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmtPO);
		$allconfirmedDELIVERY='yes';

		if (count($resPO['RAFID'])>0){
			for ($po = 0; $po < count($resPO['RAFID']); $po++) {
				if(($resPO['CONFIRMED'][$po]=='NOT OK' or $resPO['CONFIRMED'][$po]=='' or ($resPO['CONFIRMED_PARTNER'][$po]=='MISSING' && $resPO['CONFIRMED'][$po]=='NOT OK')) && $allconfirmedDELIVERY!='no' && $allconfirmedDELIVERY!='rejected'){
					$allconfirmedDELIVERY='no';
				}
				if($resPO['CONFIRMED'][$po]=='REJECTED'){
					$allconfirmedDELIVERY='rejected';
				}
			}	
		}else{
			$allconfirmedDELIVERY='no';
		}

		if (count($resPO['RAFID'])>0 && $allconfirmedDELIVERY=='yes'){
			$query4 = "UPDATE BSDS_RAFV2 SET POCON_DELIVERY='OK CONFIRMED',POCON_DELIVERY_BY='".$guard_username."',POCON_DELIVERY_DATE=SYSDATE";
		}else{
			$query4 = "UPDATE BSDS_RAFV2 SET POCON_DELIVERY='NOT OK',POCON_DELIVERY_BY='".$guard_username."',POCON_DELIVERY_DATE=SYSDATE";
		}
		if ($_POST['actiontype']=="CREATED"){
			$query4.= ",POCON_PARTNER='NOT OK'";
		}
		$query4.=" WHERE RAFID='".$_POST['RAFID']."'";
		$stmt = parse_exec_free($conn_Infobase, $query4, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}
	}
	
	$res["msg"] = $msg;
	$res["act"] = $act;
	$res["rtype"]="info";
	echo json_encode($res);

	if ($_POST['actiontype']=='REJECTED'){
		$mail             = new PHPMailer();
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
		                                           // 1 = errors and messages
		                                           // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
		$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
		$mail->Username   = "infobaseticket@gmail.com";  // GMAIL username
		$mail->Password   = "Genie-456";            // GMAIL password
		$mail->AddEmbeddedImage('../../images/basecompany.png', 'logo_2u');

		$userdetails_Sender=getuserdata($guard_username);
		$fullname_sender=$userdetails_Sender['fullname'];
		$email_sender=$userdetails_Sender['email'];

		$mail->SetFrom($email_sender, 'Infobase');
		//$mail->AddReplyTo($email_sender,$fullname_sender);
		$mail->Subject    = "PO/PR to delete: ".$_POST['POPR'];
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$text_body = "Hi Laurence,<br><br>";
	   	$text_body .= "Can u please delete PO <font color='orange'>".$_POST['POPR']."</font> (".$_POST['SHORTTEXT']." =>  ".$_POST['ITEMCOST'].") on request of <a href='mailto:'".$email_sender."'>".$fullname_sender."</a>!<br><br>";
	   	$text_body .= "<u>Reason:</u><br>".escape_sq($_POST['CONFIRMED_COMMENTS'])."<br><br>";
	   	$text_body .= "Click <a href='http://infobase/bsds/index.php?module=RAF&rafid=".escape_sq($_POST['RAFID'])."'>here</a> to view RAF in Infobase<br><br>";
	   	$text_body .= "(You can reply to this message, it will be sent to the requestor)<br><br>";
	   	$text_body .= "Rgds,<br>From Frederick Eyland on behalf of Infobase<br><br>";
	   	$text_body .= "<img src='cid:logo_2u' width='100px' height='52px'>";
		$mail->Body = $text_body;
		$mail->MsgHTML($text_body);
		
		//$mail->AddAddress($email, $fullname);
		$mail->AddAddress('frederick.eyland@basecompany.be','Frederick Eyland');
		$mail->AddReplyTo($email_sender, $fullname_sender);
		$mail->AddCC($email_sender, $fullname_sender);
		//$mail->AddAddress('Laurence.Vanden.Broeck@basecompany.be','Laurence.Vanden.Broeck');

		if(!$mail->Send()) {
		  echo "Mailer Error: " . $mail->ErrorInfo;
		}
	}
*/
}else if ($_POST['action']=="update_budget"){

	if ($_POST['ACQCON']=="ACQ"){
		$query2="UPDATE BSDS_RAFV2 SET BUDGET_ACQ='".$_POST['budget_acq']."' WHERE RAFID='".$_POST['rafid']."'";
		$val=$_POST['budget_acq'];
	}else if ($_POST['ACQCON']=="CON"){
		$query2="UPDATE BSDS_RAFV2 SET BUDGET_CON='".$_POST['budget_con']."' WHERE RAFID='".$_POST['rafid']."'";
		$val=$_POST['budget_con'];
	}
	//echo $query2;
	$stmt2 = parse_exec_free($conn_Infobase, $query2, $error_str);
	if (!$stmt2) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$resp["msg"] = "BUDGET has been updated";
		$resp["rtype"]="info";

		$queryIN="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['rafid']."',SYSDATE,'".$val."','".$guard_username."','BUDGET ".$_POST['acqcon']."')";
		//echo $queryIN;
		$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
		if (!$stmtIN) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}
	}

	echo json_encode($resp);

}else if ($_POST['action']=="update_cof"){

	$query = "Select RAFID FROM BSDS_RAF_COF WHERE RAFID = '".$_POST['rafid']."' 
	AND ACQCON='".$_POST['ACQCON']."' AND MATERIAL_CODE='".$_POST['MATERIAL_CODE']."'";
	//echo $query;
	$stmtCOF= parse_exec_fetch($conn_Infobase, $query, $error_str, $resCOF);
	if (!$stmtCOF) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	}else{
		OCIFreeStatement($stmtCOF);
	}
	$BOQ_file_found=1;
	if (count($resCOF['RAFID'])!=1 or $_POST['MATERIAL_CODE']=="BM_CON_MULTIBAND" or $_POST['MATERIAL_CODE']=="TM_CON_MULTIBAND"){

		$pos = strpos($_POST['MATERIAL_CODE'], "BOQ");
		if ($pos !== false){
			$price=str_replace(",", ".", $_POST['BOQ_AMOUNT']);
			if ($price!="" && is_numeric($price)){
				
				//If BOQ, a file needs to be uploaded first
				$rafdir=$config['raf_folder_abs'].$_POST['rafid']."/";
				if (file_exists($rafdir)){
					$files=scandir($rafdir);
					$BOQ_file_found=0;
					foreach ($files as $key => $file){
						$pos = strpos($file, "BOQ");
						if ($pos !== false){
							$BOQ_file_found=1;
							break;
						}
					}			
				}else{
					$BOQ_file_found=0;
				}
			}else{
				$price="";
			}
		}else{
			$query = "Select PRICE FROM COF_MASTERFILE WHERE MATERIAL = '".$_POST['MATERIAL_CODE']."'";
			//echo $query;
			$stmtCOFprice= parse_exec_fetch($conn_Infobase, $query, $error_str, $resCOFprice);
			if (!$stmtCOFprice) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			}else{
				OCIFreeStatement($stmtCOFprice);
				$price=$resCOFprice['PRICE'][0];
			}
		}

		if ($price==""){
			$resp["msg"] = "No price available (or wrongly formatted) for ".$_POST['MATERIAL_CODE'].", please contact Base Delivery!";
			$resp["rtype"]="error";
		}else if($BOQ_file_found==0){
			$resp["msg"] = "BOQ file missing, please first upload BOQ in FILES tab!";
			$resp["rtype"]="error";
		}else{
			$query3="INSERT INTO BSDS_RAF_COF (RAFID, MATERIAL_CODE, INSERT_DATE,INSERT_BY,ACQCON,SPRICE) VALUES ('".$_POST['rafid']."','".$_POST['MATERIAL_CODE']."',SYSDATE,'".$guard_username."','".$_POST['ACQCON']."','".$price."')";
			//echo $query3;
			$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
			if (!$stmt3) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
				$resp["msg"] = "COF info added";
				$resp["rtype"]="info";
				$resp["table"]=$_POST['rafid']."_COF".$_POST['ACQCON']."table";
				$resp["row"]="<tr><td>".$_POST['MATERIAL_CODE']."</td><td>&nbsp;</td><td>".$price."</td></tr>";
				
				$queryIN="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$_POST['rafid']."',SYSDATE,'".$_POST['MATERIAL_CODE']."','".$guard_username."','INS COF MATERIAL CODE ".$_POST['acqcon']."')";
				//echo $queryIN;
				$stmtIN = parse_exec_free($conn_Infobase, $queryIN, $error_str);
				if (!$stmtIN) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
			}
		}

		
	}else{
		$resp["msg"] = "You can not add the same material code ".$_POST['MATERIAL_CODE']." twice!";
		$resp["rtype"]="error";
	}
	echo json_encode($resp);

}else if ($_POST['action']=="update_pacfacready"){
//pre_PARTNER_VALREQ
	if ($_POST['actiontype']=='notready'){
		$status="NOT OK";
	}else{
		if ($_POST['prev_PARTNER_VALREQ']=="READY FOR PAC"){
			$status="PAC CONFIRMED";
		}else if ($_POST['prev_PARTNER_VALREQ']=="READY FOR PAC&FAC"){
			$status="PAC&FAC CONFIRMED";
		}else if ($_POST['prev_PARTNER_VALREQ']=="READY FOR FAC"){
			$status="FAC CONFIRMED";
		}else{
			$status="NOT OK";
		}
	}

	$query2="UPDATE BSDS_RAFV2 SET PARTNER_VALREQ='".$status."', PARTNER_VALREQ_BY='".$guard_username."', PARTNER_VALREQ_DATE=SYSDATE WHERE RAFID='".$_POST['RAFID']."'";
	//echo $query2;
	$stmt2 = parse_exec_free($conn_Infobase, $query2, $error_str);
	if (!$stmt2) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$resp["msg"] = "PAC/FAC confimration changed to ".$status;
		$resp["rtype"]="info";
	}

	$query3="INSERT INTO INFOBASE.BSDS_RAF_PACFACHIST (RAFID, ACTION_DATE, STATUS,ACTION_BY,FIELD) VALUES ('".$_POST['RAFID']."',SYSDATE,'".$status."','".$guard_username."','PARTNER_VALREQ')";
	//echo $query3;
	$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
	if (!$stmt3) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}

	echo json_encode($resp);
	
/*
}else if ($_POST['action']=="update_poinfo_partner"){

	if ($_POST['actiontype']!='MISSING'){
		$query="UPDATE BSDS_RAF_PO SET CONFIRMED_PARTNER='".$_POST['actiontype']."', CONFIRMED_PARTNER_BY='".$guard_username."', CONFIRMED_PARTNER_DATE=SYSDATE, 
		CONFIRMED_COMMENTS_PARTNER='".escape_sq($_POST['CONFIRMED_COMMENTS_PARTNER'])."',CONFIRMED='NOT OK' WHERE ID='".$_POST['POID']."'";
		$res["msg"] = "PO info updated successfully";
		$res["rtype"]="info";
		$res["actiontype"]=$_POST['actiontype'];
	}else if($_POST['actiontype']=='MISSING'){
		$query="INSERT INTO BSDS_RAF_PO (RAFID,ACQCON,CONFIRMED_PARTNER,CONFIRMED_PARTNER_BY,CONFIRMED_PARTNER_DATE,CONFIRMED_COMMENTS_PARTNER,CONFIRMED) 
		VALUES ('".$_POST['RAFID']."','CON','MISSING','".$guard_username."', SYSDATE, '".escape_sq($_POST['CONFIRMED_COMMENTS_PARTNER'])."','NOT OK')";
		$res["msg"] = "Missing PO info has been saved and will be reviewed by BASE DELIVERY";
		$res["rtype"]="info";
		$res["actiontype"]=$_POST['actiontype'];
	}	
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);

		$query3 = "Select * FROM BSDS_RAF_PO WHERE RAFID = '".$_POST['RAFID']."' AND ACQCON='CON'";
		//echo $query3;
		$stmtPO = parse_exec_fetch($conn_Infobase, $query3, $error_str, $resPO);
		if (!$stmtPO) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmtPO);
			$allconfirmedPARTNER='yes';

			if (count($resPO['RAFID'])>0){
				for ($po = 0; $po < count($resPO['RAFID']); $po++) {

					if(($resPO['CONFIRMED_PARTNER'][$po]=='NOT OK' or $resPO['CONFIRMED_PARTNER'][$po]=='') && $allconfirmedPARTNER!='no'){
						$allconfirmedPARTNER='no';
					}
					if(($resPO['CONFIRMED_PARTNER'][$po]=='NOT AGREED' or $resPO['CONFIRMED_PARTNER'][$po]=='MISSING') && $allconfirmedPARTNER!='no'){ //as soon a NOT OK is found it should stay as NOT OK
						$allconfirmedPARTNER='notagreed';
					}
				}	
			}else{
				$allconfirmedPARTNER='no';
			}

			if (count($resPO['RAFID'])>0 && $allconfirmedPARTNER=='yes'){
				$query4 = "UPDATE BSDS_RAFV2 SET POCON_PARTNER='OK CONFIRMED',POCON_PARTNER_BY='INFOBASE',POCON_PARTNER_DATE=SYSDATE WHERE RAFID='".$_POST['RAFID']."'";
				
			}else if (count($resPO['RAFID'])>0 && $allconfirmedPARTNER=='notagreed'){
				$query4 = "UPDATE BSDS_RAFV2 SET POCON_PARTNER='NOT AGREED',POCON_PARTNER_BY='INFOBASE',POCON_PARTNER_DATE=SYSDATE WHERE RAFID='".$_POST['RAFID']."'";
			}
			$stmt = parse_exec_free($conn_Infobase, $query4, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
		}	
	echo json_encode($res);
	}
*/	
}else if ($_POST['action']=="update_delivery_2"){
	$query = "Select RAFID FROM BSDS_RAF_DELIVERY WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	if ($_POST['STAB']==1){
		$STAB=1;
	}else{
		$STAB=0;
	}
	if ($_POST['PIF']==1){
		$PIF=1;
	}else{
		$PIF=0;
	}
	if ($_POST['OTHER']==1){
		$OTHER=1;
	}else{
		$OTHER=0;
	}

	if ($amount_of_RAFS==1){
		$query="UPDATE BSDS_RAF_DELIVERY SET 
		STAB='".$STAB."',STAB_REASON='".escape_sq($_POST['STAB_REASON'])."',
		PIF='".$PIF."',
		PIF_REASON='".escape_sq($_POST['PIF_REASON'])."',
		OTHER='".$OTHER."',
		OTHER_REASON='".escape_sq($_POST['OTHER_REASON'])."'
		WHERE RAFID='".$_POST['rafid']."'";
		$message=  "RAF DELIVERY has succesfully been UPDATED for RAFID '".$_POST['rafid']."'!";
	}else{
		$query="INSERT INTO BSDS_RAF_DELIVERY VALUES 
		('".$_POST['rafid']."' , SYSDATE, '".$guard_username."','".$_POST['STAB']."','".escape_sq($_POST['STAB_REASON'])."',
		'".$_POST['PIF']."','".escape_sq($_POST['PIF_REASON'])."','".$_POST['OTHER']."','".escape_sq($_POST['OTHER_REASON'])."')";
		$message=  "RAF DELIVERY has succesfully been INSERTED for RAFID '".$_POST['rafid']."'!";
	}
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$res["responsedata"] = $message;
		$res["responsetype"]="info";	
	}

	if ($_POST['PIF']==1 && $_POST['STAB']==1 && $_POST['OTHER']==1){
		$query4 = "UPDATE BSDS_RAFV2 SET NET1_PAC='OK',NET1_PAC_BY='".$guard_username."',NET1_PAC_DATE=SYSDATE WHERE RAFID='".$_POST['rafid']."'";	
		//echo $query4;
		$stmt = parse_exec_free($conn_Infobase, $query4, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}
	}else{
		$query4 = "UPDATE BSDS_RAFV2 SET NET1_PAC='NOT OK',NET1_FAC='NOT OK',PARTNER_RFPAC='NOT OK' WHERE RAFID='".$_POST['rafid']."'";	
		//echo $query4;
		$stmt = parse_exec_free($conn_Infobase, $query4, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}
	}

	echo json_encode($res);

}else if ($_POST['action']=="update_radio_raf_1_7"){
  	$query = "Select RAFID FROM BSDS_RAF_RADIO WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	$COVERAGE_DESCR=escape_sq($_POST['COVERAGE_DESCR']);
	$COMMENTS=escape_sq($_POST['COMMENTS']);
	$ADDRESS=escape_sq($_POST['ADDRESS']);
	$COVERAGE_OBJECTIVE=escape_sq($_POST['COVERAGE_OBJECTIVE']);
	$EXPTRAFFIC="";
	if ($_POST['EXP_G9']=="G9") $EXPTRAFFIC.=$_POST['EXP_G9'].",";
	if ($_POST['EXP_G18']=="G18") $EXPTRAFFIC.=$_POST['EXP_G18'].",";
	if ($_POST['EXP_U9']=="U9") $EXPTRAFFIC.=$_POST['EXP_U9'].",";
	if ($_POST['EXP_U21']=="U21") $EXPTRAFFIC.=$_POST['EXP_U21'].",";
	if ($_POST['EXP_L8']=="L8") $EXPTRAFFIC.=$_POST['EXP_L8'].",";
	if ($_POST['EXP_L18']=="L18") $EXPTRAFFIC.=$_POST['EXP_L18'].",";
	if ($_POST['EXP_L26']=="L26") $EXPTRAFFIC.=$_POST['EXP_L26'].",";
	$EXPTRAFFIC=substr($EXPTRAFFIC,0,-1);

	if ($amount_of_RAFS==1){
	//echo $_POST['AREA_900'];

	  $query = "UPDATE INFOBASE.BSDS_RAF_RADIO
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
		XCOORD='".$_POST['XCOORD']."',
		YCOORD='".$_POST['YCOORD']."',
		ADDRESS='".$ADDRESS."',
	    RFPLAN='".$_POST['RFPLAN']."',
		CONTACT='".$_POST['CONTACT']."',
		PHONE='".$_POST['PHONE']."',
	    SITETYPE='".$_POST['SITETYPE']."',
		SITESHARING='".$_POST['SITESHARING']."',
		BAND_900='".$_POST['BAND_900']."',
		BAND_1800='".$_POST['BAND_1800']."',
		BAND_UMTS='".$_POST['BAND_UMTS']."',
		BAND_UMTS900='".$_POST['BAND_UMTS900']."',
		BAND_LTE800='".$_POST['BAND_LTE800']."',
		BAND_LTE1800='".$_POST['BAND_LTE1800']."',
		BAND_LTE2600='".$_POST['BAND_LTE2600']."',
	    EXPTRAFFIC='".$EXPTRAFFIC."',
		FEATURE='".$_POST['FEATURE']."',
		PREFERREDINST='".$_POST['PREFERREDINST']."',
	    CABTYPE='".$_POST['CABTYPE']."',
		CHTRX='".$_POST['CHTRX']."',
		SECTORS='".$_POST['SECTORS']."',
	    REPEATER='".$_POST['REPEATER']."',
		SECTOR='".$_POST['SECTOR']."',
		COVERAGE_OBJECTIVE='".$COVERAGE_OBJECTIVE."',
	    COVERAGE_DESCR='".$COVERAGE_DESCR."',
		FLOORS='".$_POST['FLOORS']."',
		AREAS='".$_POST['AREAS']."',
	    SITETYPE2='".$_POST['SITETYPE2']."',
		AREA1_900='".$_POST['AREA1_900']."',
		AREA1_1800='".$_POST['AREA1_1800']."',
	    AREA1_UMTS='".$_POST['AREA1_UMTS']."',
	    AREA1_UMTS900='".$_POST['AREA1_UMTS900']."',
	    AREA1_LTE800='".$_POST['AREA1_LTE800']."',
	    AREA1_LTE1800='".$_POST['AREA1_LTE1800']."',
	    AREA1_LTE2600='".$_POST['AREA1_LTE2600']."',
		AREA2_900='".$_POST['AREA2_900']."',
		AREA2_1800='".$_POST['AREA2_1800']."',
	    AREA2_UMTS='".$_POST['AREA2_UMTS']."',
	    AREA2_UMTS900='".$_POST['AREA2_UMTS900']."',
	    AREA2_LTE800='".$_POST['AREA2_LTE800']."',
	    AREA2_LTE1800='".$_POST['AREA2_LTE1800']."',
	    AREA2_LTE2600='".$_POST['AREA2_LTE2600']."',
	    AREA3_900='".$_POST['AREA3_900']."',
		AREA3_1800='".$_POST['AREA3_1800']."',
		AREA3_UMTS='".$_POST['AREA3_UMTS']."',
	    AREA3_UMTS900='".$_POST['AREA3_UMTS900']."',
	    AREA3_LTE800='".$_POST['AREA3_LTE800']."',
	    AREA3_LTE1800='".$_POST['AREA3_LTE1800']."',
	    AREA3_LTE2600='".$_POST['AREA3_LTE2600']."',
	    AREA4_900='".$_POST['AREA4_900']."',
		AREA4_1800='".$_POST['AREA4_1800']."',
		AREA4_UMTS='".$_POST['AREA4_UMTS']."',
	    AREA4_UMTS900='".$_POST['AREA4_UMTS900']."',
	    AREA4_LTE1800='".$_POST['AREA4_LTE1800']."',
	    AREA4_LTE2600='".$_POST['AREA4_LTE2600']."',
		COVERAGE_TUNNEL='".$_POST['COVERAGE_TUNNEL']."',
		PLANS='".$_POST['PLANS']."',
	    SHARING='".$_POST['SHARING']."',
		GUIDELINES='".$_POST['GUIDELINES']."',
		COMMENTS='".$COMMENTS."',
		INTER_900='".$_POST['INTER_900']."',
		INTER_1800='".$_POST['INTER_1800']."',
	    INTER_UMTS='".$_POST['INTER_UMTS']."',
	    INTER_UMTS900='".$_POST['INTER_UMTS900']."',
	     INTER_LTE800='".$_POST['INTER_LTE800']."',
	    INTER_LTE1800='".$_POST['INTER_LTE1800']."',
	    INTER_LTE2600='".$_POST['INTER_LTE2600']."',
		THRESHOLD_900='".$_POST['THRESHOLD_900']."',
		THRESHOLD_1800='".$_POST['THRESHOLD_1800']."',
	    THRESHOLD_UMTS='".$_POST['THRESHOLD_UMTS']."',
	    THRESHOLD_UMTS900='".$_POST['THRESHOLD_UMTS900']."',
	     THRESHOLD_LTE800='".$_POST['THRESHOLD_LTE800']."',
	    THRESHOLD_LTE1800='".$_POST['THRESHOLD_LTE1800']."',
	    THRESHOLD_LTE2600='".$_POST['THRESHOLD_LTE2600']."',
		COVERAGE_900='".$_POST['COVERAGE_900']."',
		COVERAGE_1800='".$_POST['COVERAGE_1800']."',
	    COVERAGE_UMTS='".$_POST['COVERAGE_UMTS']."',
	    COVERAGE_UMTS900='".$_POST['COVERAGE_UMTS900']."',
	    COVERAGE_LTE800='".$_POST['COVERAGE_LTE800']."',
	    COVERAGE_LTE1800='".$_POST['COVERAGE_LTE1800']."',
	    COVERAGE_LTE2600='".$_POST['COVERAGE_LTE2600']."',
		TOTCOVERAGE_900='".$_POST['TOTCOVERAGE_900']."',
		TOTCOVERAGE_1800='".$_POST['TOTCOVERAGE_1800']."',
	    TOTCOVERAGE_UMTS='".$_POST['TOTCOVERAGE_UMTS']."',
	    TOTCOVERAGE_UMTS900='".$_POST['TOTCOVERAGE_UMTS900']."',
	     TOTCOVERAGE_LTE800='".$_POST['TOTCOVERAGE_LTE800']."',
	    TOTCOVERAGE_LTE1800='".$_POST['TOTCOVERAGE_LTE1800']."',
	    TOTCOVERAGE_LTE2600='".$_POST['TOTCOVERAGE_LTE2600']."',
		POLYMAP='".$_POST['POLYMAP']."',
		NRSECTORS='".$_POST['NRSECTORS']."',
	    NRSECTORS_900='".$_POST['NRSECTORS_900']."',
		NRSECTORS_1800='".$_POST['NRSECTORS_1800']."',
		NRSECTORS_UMTS='".$_POST['NRSECTORS_UMTS']."',
		NRSECTORS_UMTS900='".$_POST['NRSECTORS_UMTS900']."',
		NRSECTORS_LTE800='".$_POST['NRSECTORS_LTE800']."',
		NRSECTORS_LTE1800='".$_POST['NRSECTORS_LTE1800']."',
		NRSECTORS_LTE2600='".$_POST['NRSECTORS_LTE2600']."',
	    HMINMAX='".$_POST['HMINMAX']."',
		HMINMAXRF='".$_POST['HMINMAXRF']."',
		ANTBLOCKING='".$_POST['ANTBLOCKING']."',
	    ANGLE='".$_POST['ANGLE']."',
		RFGUIDES='".$_POST['RFGUIDES']."',
		CONGUIDES='".$_POST['CONGUIDES']."',
	    TXGUIDES='".$_POST['TXGUIDES']."',
		LOC_NAME1='".$_POST['LOC_NAME1']."',
		LOC_ADDRESS1='".$_POST['LOC_ADDRESS1']."',
	    LOC_STRUCTURE1='".$_POST['LOC_STRUCTURE1']."',
		LOC_PREFER1='".$_POST['LOC_PREFER1']."',
		LOC_NOTPREFER1='".$_POST['LOC_NOTPREFER1']."',
	    LOC_NAME2='".$_POST['LOC_NAME2']."',
		LOC_ADDRESS2='".$_POST['LOC_ADDRESS2']."',
		LOC_STRUCTURE2='".$_POST['LOC_STRUCTURE2']."',
	    LOC_PREFER2='".$_POST['LOC_PREFER2']."',
		LOC_NOTPREFER2='".$_POST['LOC_NOTPREFER2']."',
		VENDOR2G_GSM900='".$_POST['VENDOR2G_GSM900']."',
		VENDOR2G_GSM1800='".$_POST['VENDOR2G_GSM1800']."',
		VENDOR3G_UMTS='".$_POST['VENDOR3G_UMTS']."',
		VENDOR3G_UMTS900='".$_POST['VENDOR3G_UMTS900']."',
		VENDOR4G_LTE800='".$_POST['VENDOR4G_LTE800']."',
		VENDOR4G_LTE1800='".$_POST['VENDOR4G_LTE1800']."',
		VENDOR4G_LTE2600='".$_POST['VENDOR4G_LTE2600']."',
		CONFIG='".$_POST['CONFIG']."',
		CLUSTERN='".$_POST['CLUSTER']."',
		CLUSTERNUM='".$_POST['CLUSTERNUM']."',
		CLUSTER_TARGET_DATE='".$_POST['CLUSTER_TARGET_DATE']."'
	   WHERE RAFID='".$_POST['rafid']."'";
		//echo "$query <br>";
	}else{
	   $query = "INSERT INTO INFOBASE.BSDS_RAF_RADIO (
	   RAFID, UPG_DATE, UPG_BY,
	   XCOORD, YCOORD, ADDRESS,
	   RFPLAN, CONTACT, PHONE,
	   SITETYPE, SITESHARING,
	   BAND_900, BAND_1800, BAND_UMTS, BAND_UMTS900,BAND_LTE1800,BAND_LTE2600,
	   EXPTRAFFIC, FEATURE, PREFERREDINST,
	   CABTYPE, CHTRX, SECTORS,
	   REPEATER, SECTOR, COVERAGE_OBJECTIVE,
	   COVERAGE_DESCR, FLOORS, AREAS,
	   SITETYPE2, AREA1_900, AREA1_1800,
	   AREA1_UMTS,AREA2_900, AREA2_1800,
	   AREA2_UMTS,AREA3_900, AREA3_1800,
	   AREA3_UMTS,AREA4_900, AREA4_1800,
	   AREA4_UMTS, COVERAGE_TUNNEL, PLANS,
	   SHARING, GUIDELINES, COMMENTS,
	   INTER_900, INTER_1800,
	   INTER_UMTS, THRESHOLD_900, THRESHOLD_1800,
	   THRESHOLD_UMTS, COVERAGE_900, COVERAGE_1800,
	   COVERAGE_UMTS, TOTCOVERAGE_900, TOTCOVERAGE_1800,
	   TOTCOVERAGE_UMTS, POLYMAP, NRSECTORS,
	   NRSECTORS_900, NRSECTORS_1800, NRSECTORS_UMTS,
	   HMINMAX, HMINMAXRF, ANTBLOCKING,
	   ANGLE, RFGUIDES, CONGUIDES,
	   TXGUIDES, LOC_NAME1, LOC_ADDRESS1,
	   LOC_STRUCTURE1, LOC_PREFER1, LOC_NOTPREFER1,
	   LOC_NAME2, LOC_ADDRESS2, LOC_STRUCTURE2,
	   LOC_PREFER2, LOC_NOTPREFER2,
	   VENDOR2G_GSM1800, VENDOR2G_GSM900, VENDOR3G_UMTS, VENDOR4G_LTE1800,  VENDOR4G_LTE2600, VENDOR3G_UMTS900,
	   AREA1_UMTS900, AREA2_UMTS900,  AREA3_UMTS900, AREA4_UMTS900, COVERAGE_UMTS900,
	   INTER_UMTS900, THRESHOLD_UMTS900, TOTCOVERAGE_UMTS900,NRSECTORS_UMTS900,
	   AREA1_LTE1800, AREA2_LTE1800, AREA3_LTE1800, AREA4_LTE1800, COVERAGE_LTE1800,
	   INTER_LTE1800, THRESHOLD_LTE1800, TOTCOVERAGE_LTE1800,NRSECTORS_LTE1800,
	   AREA1_LTE2600, AREA2_LTE2600, AREA3_LTE2600, AREA4_LTE2600, COVERAGE_LTE2600,
	   INTER_LTE2600, THRESHOLD_LTE2600, TOTCOVERAGE_LTE2600, NRSECTORS_LTE2600,
	   BAND_LTE800, AREA1_LTE800, AREA2_LTE800, AREA3_LTE800, AREA4_LTE800, COVERAGE_LTE800,
	   INTER_LTE800, THRESHOLD_LTE800, TOTCOVERAGE_LTE800,NRSECTORS_LTE800,VENDOR4G_LTE800,
	   CONFIG,CLUSTERN, CLUSTERNUM,CLUSTER_TARGET_DATE)
	  VALUES ('".$_POST['rafid']."' , SYSDATE, '".$guard_username."',
		'".$_POST['XCOORD']."', '".$_POST['YCOORD']."', '".$_POST['ADDRESS']."',
	    '".$_POST['RFPLAN']."',  '".$_POST['CONTACT']."',  '".$_POST['PHONE']."',
	    '".$_POST['SITETYPE']."',  '".$_POST['SITESHARING']."',
		'".$_POST['BAND_900']."','".$_POST['BAND_1800']."','".$_POST['BAND_UMTS']."','".$_POST['BAND_UMTS900']."','".$_POST['BAND_LTE1800']."','".$_POST['BAND_LTE2600']."',
		'".$EXPTRAFFIC."',  '".$_POST['FEATURE']."',  '".$_POST['PREFERREDINST']."',
	    '".$_POST['CABTYPE']."',  '".$_POST['CHTRX']."',  '".$_POST['SECTORS']."',
	    '".$_POST['REPEATER']."', '".$_POST['SECTOR']."',  '".$_POST['COVERAGE_OBJECTIVE']."',
	    '".$COVERAGE_DESCR."',  '".$_POST['FLOORS']."',  '".$_POST['AREAS']."',
	    '".$_POST['SITETYPE2']."',  '".$_POST['AREA1_900']."',  '".$_POST['AREA1_1800']."',
	    '".$_POST['AREA1_UMTS']."',  '".$_POST['AREA2_900']."',  '".$_POST['AREA2_1800']."',
	    '".$_POST['AREA2_UMTS']."',  '".$_POST['AREA3_900']."',  '".$_POST['AREA3_1800']."',
	    '".$_POST['AREA3_UMTS']."',  '".$_POST['AREA4_900']."',  '".$_POST['AREA4_1800']."',
	    '".$_POST['AREA4_UMTS']."',  '".$_POST['COVERAGE_TUNNEL']."',  '".$_POST['PLANS']."',
	    '".$_POST['SHARING']."',  '".$_POST['GUIDELINES']."',  '".$COMMENTS."',
	    '".$_POST['INTER_900']."',  '".$_POST['INTER_1800']."',
	    '".$_POST['INTER_UMTS']."',  '".$_POST['THRESHOLD_900']."',  '".$_POST['THRESHOLD_1800']."',
	    '".$_POST['THRESHOLD_UMTS']."',  '".$_POST['COVERAGE_900']."',  '".$_POST['COVERAGE_1800']."',
	    '".$_POST['COVERAGE_UMTS']."',  '".$_POST['TOTCOVERAGE_900']."',  '".$_POST['TOTCOVERAGE_1800']."',
	    '".$_POST['TOTCOVERAGE_UMTS']."',  '".$_POST['POLYMAP']."',  '".$_POST['NRSECTORS']."',
	    '".$_POST['NRSECTORS_900']."',  '".$_POST['NRSECTORS_1800']."',  '".$_POST['NRSECTORS_UMTS']."',
	    '".$_POST['HMINMAX']."',  '".$_POST['HMINMAXRF']."',  '".$_POST['ANTBLOCKING']."',
	    '".$_POST['ANGLE']."',  '".$_POST['RFGUIDES']."',  '".$_POST['CONGUIDES']."',
	    '".$_POST['TXGUIDES']."',  '".$_POST['LOC_NAME1']."',  '".$_POST['LOC_ADDRESS1']."',
	    '".$_POST['LOC_STRUCTURE1']."',  '".$_POST['LOC_PREFER1']."',  '".$_POST['LOC_NOTPREFER1']."',
	    '".$_POST['LOC_NAME2']."',  '".$_POST['LOC_ADDRESS2']."',  '".$_POST['LOC_STRUCTURE2']."',
	    '".$_POST['LOC_PREFER2']."',  '".$_POST['LOC_NOTPREFER2']."',
		'".$_POST['VENDOR2G_GSM1800']."',  '".$_POST['VENDOR2G_GSM900']."', '".$_POST['VENDOR3G_UMTS']."','".$_POST['VENDOR4G_LTE1800']."','".$_POST['VENDOR4G_LTE2600']."',
	    '".$_POST['VENDOR3G_UMTS900']."', '".$_POST['AREA1_UMTS900']."', '".$_POST['AREA2_UMTS900']."',
	    '".$_POST['AREA3_UMTS900']."', '".$_POST['AREA4_UMTS900']."', '".$_POST['COVERAGE_UMTS900']."',
	    '".$_POST['INTER_UMTS900']."', '".$_POST['THRESHOLD_UMTS900']."', '".$_POST['TOTCOVERAGE_UMTS900']."',
	    '".$_POST['NRSECTORS_UMTS900']."', '".$_POST['AREA1_LTE1800']."', '".$_POST['AREA2_LTE1800']."',
	    '".$_POST['AREA3_LTE1800']."', '".$_POST['AREA4_LTE1800']."', '".$_POST['COVERAGE_LTE1800']."',
	    '".$_POST['INTER_LTE1800']."', '".$_POST['THRESHOLD_LTE1800']."', '".$_POST['TOTCOVERAGE_LTE1800']."',
	    '".$_POST['NRSECTORS_LTE1800']."' , '".$_POST['AREA1_LTE2600']."', '".$_POST['AREA2_LTE2600']."',
	    '".$_POST['AREA3_LTE2600']."', '".$_POST['AREA4_LTE2600']."', '".$_POST['COVERAGE_LTE2600']."',
	    '".$_POST['INTER_LTE2600']."', '".$_POST['THRESHOLD_LTE2600']."', '".$_POST['TOTCOVERAGE_LTE2600']."',
	    '".$_POST['NRSECTORS_LTE2600']."',
	    '".$_POST['BAND_LTE800']."', '".$_POST['AREA1_LTE800']."', '".$_POST['AREA2_LTE800']."', '".$_POST['AREA3_LTE800']."', 
	    '".$_POST['AREA4_LTE800']."', '".$_POST['COVERAGE_LTE800']."',
	   	'".$_POST['INTER_LTE800']."', '".$_POST['THRESHOLD_LTE800']."', '".$_POST['TOTCOVERAGE_LTE800']."','".$_POST['NRSECTORS_LTE800']."',
	   	'".$_POST['VENDOR4G_LTE800']."','".$_POST['CONFIG']."','".$_POST['CLUSTER']."','".$_POST['CLUSTERNUM']."','".$_POST['CLUSTER_TARGET_DATE']."')";
	}
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "RAF RADIO has succesfully been updated for RAFID '".$_POST['rafid']."'!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}

if ($_POST['action']=="update_radio_raf_8_9"){
  	$query = "Select RAFID FROM BSDS_RAF_RADIO WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	$JUSTIFICATION=escape_sq($_POST['JUSTIFICATION']);
	$AZCAPTILT=escape_sq($_POST['AZCAPTILT']);
	$CONDITIONAL=escape_sq($_POST['CONDITIONAL']);

	if ($amount_of_RAFS==1){
	  $query = "UPDATE INFOBASE.BSDS_RAF_RADIO
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
		AZCAPTILT='".$AZCAPTILT."',
	    JUSTIFICATION='".$JUSTIFICATION."',
		CONDITIONAL='".$CONDITIONAL."'
	   WHERE RAFID='".$_POST['rafid']."'";
	}else{
	   $query = "INSERT INTO INFOBASE.BSDS_RAF_RADIO (
	   RAFID, UPG_DATE, UPG_BY,
	   AZCAPTILT, JUSTIFICATION,C ONDITIONAL)
	  VALUES ('".$_POST['rafid']."' , SYSDATE, '".$guard_username."',
		'".$AZCAPTILT."', '".$JUSTIFICATION."', '".$CONDITIONAL."')";
	}
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "RAF RADIO has succesfully been updated!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}

if ($_POST['action']=="update_radio_raf_10_11"){
  	$query = "Select RAFID FROM BSDS_RAF_RADIO WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	$BUDGET=escape_sq($_POST['BUDGET']);

	if ($amount_of_RAFS==1){
	  $query = "UPDATE INFOBASE.BSDS_RAF_RADIO
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
		BUDGET='".$BUDGET."'
	   WHERE RAFID='".$_POST['rafid']."'";
	}else{
	   $query = "INSERT INTO INFOBASE.BSDS_RAF_RADIO (
	   RAFID, UPG_DATE, UPG_BY, BUDGET)
	  VALUES ('".$_POST['rafid']."' , SYSDATE, '".$guard_username."','".$BUDGET."')";
	}
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "RAF RADIO has succesfully been updated!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}

if ($_POST['action']=="update_radio_raf_12"){
  	$query = "Select RAFID FROM BSDS_RAF_RADIO WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	if ($amount_of_RAFS==1){
	  $query = "UPDATE INFOBASE.BSDS_RAF_RADIO
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
		PACCOMMENTS='".escape_sq($_POST['PACCOMMENTS'])."',AREA_SOCIAL='".escape_sq($_POST['AREA_SOCIAL'])."',COVERAGE_SOCIAL='".escape_sq($_POST['COVERAGE_SOCIAL'])."'
	   WHERE RAFID='".$_POST['rafid']."'";
	}else{
	   $query = "INSERT INTO INFOBASE.BSDS_RAF_RADIO (
	   RAFID, UPG_DATE, UPG_BY, PACCOMMENTS,AREA_SOCIAL,COVERAGE_SOCIAL)
	  VALUES ('".$_POST['rafid']."' , SYSDATE, '".$guard_username."','".escape_sq($_POST['PACCOMMENTS'])."','".escape_sq($_POST['AREA_SOCIAL'])."','".escape_sq($_POST['COVERAGE_SOCIAL'])."')";
	}
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "RAF RADIO has succesfully been updated!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}


if ($_POST['action']=="update_txmn_raf_1_6"){
  	$query = "Select RAFID FROM BSDS_RAF_TXMN WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	$BUDGET=escape_sq($_POST['BUDGET']);
	$SPECIFIC_TXMN=escape_sq($_POST['SPECIFIC_TXMN']);

	if ($amount_of_RAFS==1){
	//echo $_POST['AREA_900'];
	  $query = "UPDATE INFOBASE.BSDS_RAF_TXMN
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
	   GRANTED_BEARING1     ='".$_POST['GRANTED_BEARING1']."',
	   GRANTED_DIAMETER1    ='".$_POST['GRANTED_DIAMETER1']."',
	   GRANTED_BEARING2     ='".$_POST['GRANTED_BEARING2']."',
	   GRANTED_DIAMETER2    ='".$_POST['GRANTED_DIAMETER2']."',
	   GRANTED_BEARING3     ='".$_POST['GRANTED_BEARING3']."',
	   GRANTED_DIAMETER3    ='".$_POST['GRANTED_DIAMETER3']."',
	   GRANTED_BEARING4     ='".$_POST['GRANTED_BEARING4']."',
	   GRANTED_DIAMETER4    ='".$_POST['GRANTED_DIAMETER4']."',
	   GRANTED_BEARING5     ='".$_POST['GRANTED_BEARING5']."',
	   GRANTED_DIAMETER5    ='".$_POST['GRANTED_DIAMETER5']."',
	   GRANTED_BEARING6     ='".$_POST['GRANTED_BEARING6']."',
	   GRANTED_DIAMETER6    ='".$_POST['GRANTED_DIAMETER6']."',
	   GRANTED_BEARING7     ='".$_POST['GRANTED_BEARING7']."',
	   GRANTED_DIAMETER7    ='".$_POST['GRANTED_DIAMETER7']."',
	   GRANTED_BEARING8     ='".$_POST['GRANTED_BEARING8']."',
	   GRANTED_DIAMETER8    ='".$_POST['GRANTED_DIAMETER8']."',
	   GRANTED_BEARING9     ='".$_POST['GRANTED_BEARING9']."',
	   GRANTED_DIAMETER9    ='".$_POST['GRANTED_DIAMETER9']."',
	   GRANTED_BEARING10     ='".$_POST['GRANTED_BEARING10']."',
	   GRANTED_DIAMETER10    ='".$_POST['GRANTED_DIAMETER10']."',
	   GRANTED_BEARING11     ='".$_POST['GRANTED_BEARING11']."',
	   GRANTED_DIAMETER11    ='".$_POST['GRANTED_DIAMETER11']."',
	   GRANTED_BEARING12     ='".$_POST['GRANTED_BEARING12']."',
	   GRANTED_DIAMETER12    ='".$_POST['GRANTED_DIAMETER12']."',
	   GRANTED_HEIGHT1  ='".$_POST['GRANTED_HEIGHT1']."',
	   GRANTED_HEIGHT2  ='".$_POST['GRANTED_HEIGHT2']."',
	   GRANTED_HEIGHT3  ='".$_POST['GRANTED_HEIGHT3']."',
	   GRANTED_HEIGHT4  ='".$_POST['GRANTED_HEIGHT4']."',
	   GRANTED_HEIGHT5  ='".$_POST['GRANTED_HEIGHT5']."',
	   GRANTED_HEIGHT6  ='".$_POST['GRANTED_HEIGHT6']."',
	   GRANTED_HEIGHT7  ='".$_POST['GRANTED_HEIGHT7']."',
	   GRANTED_HEIGHT8  ='".$_POST['GRANTED_HEIGHT8']."',
	   GRANTED_HEIGHT9  ='".$_POST['GRANTED_HEIGHT9']."',
	   GRANTED_HEIGHT10  ='".$_POST['GRANTED_HEIGHT10']."',
	   GRANTED_HEIGHT11  ='".$_POST['GRANTED_HEIGHT11']."',
	   GRANTED_HEIGHT12  ='".$_POST['GRANTED_HEIGHT12']."',
	   ADDITIONAL_BEARING1  ='".$_POST['ADDITIONAL_BEARING1']."',
	   ADDITIONAL_DIAMETER1 ='".$_POST['ADDITIONAL_DIAMETER1']."',
	   ADDITIONAL_BEARING2  ='".$_POST['ADDITIONAL_BEARING2']."',
	   ADDITIONAL_DIAMETER2 ='".$_POST['ADDITIONAL_DIAMETER2']."',
	   ADDITIONAL_BEARING3  ='".$_POST['ADDITIONAL_BEARING3']."',
	   ADDITIONAL_DIAMETER3 ='".$_POST['ADDITIONAL_DIAMETER3']."',
	   ADDITIONAL_BEARING4  ='".$_POST['ADDITIONAL_BEARING4']."',
	   ADDITIONAL_DIAMETER4 ='".$_POST['ADDITIONAL_DIAMETER4']."',
	   ADDITIONAL_BEARING5  ='".$_POST['ADDITIONAL_BEARING5']."',
	   ADDITIONAL_DIAMETER5 ='".$_POST['ADDITIONAL_DIAMETER5']."',
	   ADDITIONAL_BEARING6  ='".$_POST['ADDITIONAL_BEARING6']."',
	   ADDITIONAL_DIAMETER6 ='".$_POST['ADDITIONAL_DIAMETER6']."',
	   ADDITIONAL_BEARING7  ='".$_POST['ADDITIONAL_BEARING7']."',
	   ADDITIONAL_DIAMETER7 ='".$_POST['ADDITIONAL_DIAMETER7']."',
	   ADDITIONAL_BEARING8  ='".$_POST['ADDITIONAL_BEARING8']."',
	   ADDITIONAL_DIAMETER8 ='".$_POST['ADDITIONAL_DIAMETER8']."',
	   ADDITIONAL_BEARING9  ='".$_POST['ADDITIONAL_BEARING9']."',
	   ADDITIONAL_DIAMETER9 ='".$_POST['ADDITIONAL_DIAMETER9']."',
	   ADDITIONAL_BEARING10  ='".$_POST['ADDITIONAL_BEARING10']."',
	   ADDITIONAL_DIAMETER10 ='".$_POST['ADDITIONAL_DIAMETER10']."',
	   ADDITIONAL_BEARING11  ='".$_POST['ADDITIONAL_BEARING11']."',
	   ADDITIONAL_DIAMETER11 ='".$_POST['ADDITIONAL_DIAMETER11']."',
	   ADDITIONAL_BEARING12  ='".$_POST['ADDITIONAL_BEARING12']."',
	   ADDITIONAL_DIAMETER12 ='".$_POST['ADDITIONAL_DIAMETER12']."',
	   ADDITIONAL_HEIGHT1  ='".$_POST['ADDITIONAL_HEIGHT1']."',
	   ADDITIONAL_HEIGHT2  ='".$_POST['ADDITIONAL_HEIGHT2']."',
	   ADDITIONAL_HEIGHT3  ='".$_POST['ADDITIONAL_HEIGHT3']."',
	   ADDITIONAL_HEIGHT4  ='".$_POST['ADDITIONAL_HEIGHT4']."',
	   ADDITIONAL_HEIGHT5  ='".$_POST['ADDITIONAL_HEIGHT5']."',
	   ADDITIONAL_HEIGHT6  ='".$_POST['ADDITIONAL_HEIGHT6']."',
	   ADDITIONAL_HEIGHT7  ='".$_POST['ADDITIONAL_HEIGHT7']."',
	   ADDITIONAL_HEIGHT8  ='".$_POST['ADDITIONAL_HEIGHT8']."',
	   ADDITIONAL_HEIGHT9  ='".$_POST['ADDITIONAL_HEIGHT9']."',
	   ADDITIONAL_HEIGHT10  ='".$_POST['ADDITIONAL_HEIGHT10']."',
	   ADDITIONAL_HEIGHT11  ='".$_POST['ADDITIONAL_HEIGHT11']."',
	   ADDITIONAL_HEIGHT12  ='".$_POST['ADDITIONAL_HEIGHT12']."',
	   EXISTING_CAB1        ='".$_POST['EXISTING_CAB1']."',
	   EXISTING_AMOUNT1     ='".$_POST['EXISTING_AMOUNT1']."',
	   EXISTING_CAB2        ='".$_POST['EXISTING_CAB2']."',
	   EXISTING_AMOUNT2     ='".$_POST['EXISTING_AMOUNT2']."',
	   ADDITIONAL_CAB1      ='".$_POST['ADDITIONAL_CAB1']."',
	   ADDITIONAL_AMOUNT1   ='".$_POST['ADDITIONAL_AMOUNT1']."',
	   ADDITIONAL_CAB2      ='".$_POST['ADDITIONAL_CAB2']."',
	   ADDITIONAL_AMOUNT2   ='".$_POST['ADDITIONAL_AMOUNT2']."',
	   SPECIFIC_TXMN        ='".$SPECIFIC_TXMN."',
	   HMIN                 ='".$_POST['HMIN']."',
	   HMINDISH             ='".$_POST['HMINDISH']."',
	   TXMNGUIDES           ='".$_POST['TXMNGUIDES']."',
	   BUDGET               ='".$BUDGET."'
	   WHERE RAFID='".$_POST['rafid']."'";
		//echo "$query <br>";
	}else{
	   $query = "INSERT INTO INFOBASE.BSDS_RAF_TXMN (
	   RAFID, UPG_DATE, UPG_BY,
	   GRANTED_BEARING1, GRANTED_DIAMETER1, GRANTED_BEARING2,
	   GRANTED_DIAMETER2, GRANTED_BEARING3, GRANTED_DIAMETER3,
	   GRANTED_BEARING4, GRANTED_DIAMETER4, GRANTED_BEARING5,
	   GRANTED_DIAMETER5, GRANTED_BEARING6, GRANTED_DIAMETER6,
	   GRANTED_BEARING7, GRANTED_DIAMETER7, GRANTED_BEARING8,  GRANTED_DIAMETER8, GRANTED_BEARING9, GRANTED_DIAMETER9,
	   GRANTED_BEARING10, GRANTED_DIAMETER10, GRANTED_BEARING11, GRANTED_DIAMETER11,
	   GRANTED_BEARING12, GRANTED_DIAMETER12, GRANTED_HEIGHT1, GRANTED_HEIGHT2,
	   GRANTED_HEIGHT3, GRANTED_HEIGHT4, GRANTED_HEIGHT5, GRANTED_HEIGHT6,
	   GRANTED_HEIGHT7, GRANTED_HEIGHT8, GRANTED_HEIGHT9, GRANTED_HEIGHT10,
	   GRANTED_HEIGHT11, GRANTED_HEIGHT12,
	   ADDITIONAL_BEARING1, ADDITIONAL_DIAMETER1,
	   ADDITIONAL_BEARING2, ADDITIONAL_DIAMETER2, ADDITIONAL_BEARING3,
	   ADDITIONAL_DIAMETER3, ADDITIONAL_BEARING4, ADDITIONAL_DIAMETER4,
	   ADDITIONAL_BEARING5, ADDITIONAL_DIAMETER5, ADDITIONAL_BEARING6,
	   ADDITIONAL_DIAMETER6, ADDITIONAL_BEARING7, ADDITIONAL_DIAMETER7,
	   ADDITIONAL_BEARING8, ADDITIONAL_DIAMETER8, ADDITIONAL_BEARING9, ADDITIONAL_DIAMETER9,
	   ADDITIONAL_BEARING10, ADDITIONAL_DIAMETER10, ADDITIONAL_BEARING11, ADDITIONAL_DIAMETER11,
	   ADDITIONAL_BEARING12, ADDITIONAL_DIAMETER12, ADDITIONAL_HEIGHT1, ADDITIONAL_HEIGHT2,
	   ADDITIONAL_HEIGHT3, ADDITIONAL_HEIGHT4, ADDITIONAL_HEIGHT5, ADDITIONAL_HEIGHT6,
	   ADDITIONAL_HEIGHT7, ADDITIONAL_HEIGHT8, ADDITIONAL_HEIGHT9, ADDITIONAL_HEIGHT10,
	   ADDITIONAL_HEIGHT11, ADDITIONAL_HEIGHT12, EXISTING_CAB1,
	   EXISTING_AMOUNT1, EXISTING_CAB2, EXISTING_AMOUNT2,
	   ADDITIONAL_CAB1, ADDITIONAL_AMOUNT1, ADDITIONAL_CAB2,
	   ADDITIONAL_AMOUNT2, SPECIFIC_TXMN, HMIN,
	   HMINDISH, TXMNGUIDES, BUDGET)
		VALUES ( '".$_POST['rafid']."' , SYSDATE, '".$guard_username."',
	   '".$_POST['GRANTED_BEARING1']."', '".$_POST['GRANTED_DIAMETER1']."', '".$_POST['GRANTED_BEARING2']."',
	   '".$_POST['GRANTED_DIAMETER2']."', '".$_POST['GRANTED_BEARING3']."', '".$_POST['GRANTED_DIAMETER3']."',
	   '".$_POST['GRANTED_BEARING4']."', '".$_POST['GRANTED_DIAMETER4']."', '".$_POST['GRANTED_BEARING5']."',
	   '".$_POST['GRANTED_DIAMETER5']."', '".$_POST['GRANTED_BEARING6']."', '".$_POST['GRANTED_DIAMETER6']."',
	   '".$_POST['GRANTED_BEARING7']."', '".$_POST['GRANTED_DIAMETER7']."', '".$_POST['GRANTED_BEARING8']."',
	   '".$_POST['GRANTED_DIAMETER8']."', '".$_POST['GRANTED_BEARING9']."', '".$_POST['GRANTED_DIAMETER9']."',
	   '".$_POST['GRANTED_BEARING10']."', '".$_POST['GRANTED_DIAMETER10']."', '".$_POST['GRANTED_BEARING11']."', '".$_POST['GRANTED_DIAMETER11']."',
	   '".$_POST['GRANTED_BEARING12']."', '".$_POST['GRANTED_DIAMETER12']."', '".$_POST['GRANTED_HEIGHT1']."', '".$_POST['GRANTED_HEIGHT2']."',
	   '".$_POST['GRANTED_HEIGHT3']."', '".$_POST['GRANTED_HEIGHT4']."', '".$_POST['GRANTED_HEIGHT5']."', '".$_POST['GRANTED_HEIGHT6']."',
	   '".$_POST['GRANTED_HEIGHT7']."', '".$_POST['GRANTED_HEIGHT8']."', '".$_POST['GRANTED_HEIGHT9']."', '".$_POST['GRANTED_HEIGHT10']."',
	   '".$_POST['GRANTED_HEIGHT11']."', '".$_POST['GRANTED_HEIGHT12']."',
	   '".$_POST['ADDITIONAL_BEARING1']."', '".$_POST['ADDITIONAL_DIAMETER1']."',
	   '".$_POST['ADDITIONAL_BEARING2']."', '".$_POST['ADDITIONAL_DIAMETER2']."', '".$_POST['ADDITIONAL_BEARING3']."',
	   '".$_POST['ADDITIONAL_DIAMETER3']."', '".$_POST['ADDITIONAL_BEARING4']."', '".$_POST['ADDITIONAL_DIAMETER4']."',
	   '".$_POST['ADDITIONAL_BEARING5']."', '".$_POST['ADDITIONAL_DIAMETER5']."', '".$_POST['ADDITIONAL_BEARING6']."',
	   '".$_POST['ADDITIONAL_DIAMETER6']."', '".$_POST['ADDITIONAL_BEARING7']."', '".$_POST['ADDITIONAL_DIAMETER7']."',
	   '".$_POST['ADDITIONAL_BEARING8']."', '".$_POST['ADDITIONAL_DIAMETER8']."', '".$_POST['ADDITIONAL_BEARING9']."', '".$_POST['ADDITIONAL_DIAMETER9']."',
	   '".$_POST['ADDITIONAL_BEARING10']."', '".$_POST['ADDITIONAL_DIAMETER10']."', '".$_POST['ADDITIONAL_BEARING11']."', '".$_POST['ADDITIONAL_DIAMETER11']."',
	   '".$_POST['ADDITIONAL_BEARING12']."', '".$_POST['ADDITIONAL_DIAMETER12']."', '".$_POST['ADDITIONAL_HEIGHT1']."', '".$_POST['ADDITIONAL_HEIGHT2']."',
	   '".$_POST['ADDITIONAL_HEIGHT3']."', '".$_POST['ADDITIONAL_HEIGHT4']."', '".$_POST['ADDITIONAL_HEIGHT5']."', '".$_POST['ADDITIONAL_HEIGHT6']."',
	   '".$_POST['ADDITIONAL_HEIGHT7']."', '".$_POST['ADDITIONAL_HEIGHT8']."', '".$_POST['ADDITIONAL_HEIGHT9']."', '".$_POST['ADDITIONAL_HEIGHT10']."',
	   '".$_POST['ADDITIONAL_HEIGHT11']."', '".$_POST['ADDITIONAL_HEIGHT12']."', '".$_POST['EXISTING_CAB1']."',
	   '".$_POST['EXISTING_AMOUNT1']."', '".$_POST['EXISTING_CAB2']."', '".$_POST['EXISTING_AMOUNT2']."',
	   '".$_POST['ADDITIONAL_CAB1']."', '".$_POST['ADDITIONAL_AMOUNT1']."', '".$_POST['ADDITIONAL_CAB2']."',
	   '".$_POST['ADDITIONAL_AMOUNT2']."', '".$SPECIFIC_TXMN."', '".$_POST['HMIN']."',
	   '".$_POST['HMINDISH']."', '".$_POST['TXMNGUIDES']."', '".$BUDGET."')";
	}
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase); 
		$res["responsedata"] =  "RAF TXMN has succesfully been updated!";
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}

if ($_POST['action']=="update_txmn_raf_7"){
  	$query = "Select RAFID FROM BSDS_RAF_TXMN WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	$BUDGET=escape_sq($_POST['BUDGET']);
	$SPECIFIC_TXMN=escape_sq($_POST['SPECIFIC_TXMN']);

	if ($amount_of_RAFS==1){
	//echo $_POST['AREA_900'];
	  $query = "UPDATE INFOBASE.BSDS_RAF_TXMN
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
	   BUDGET               ='".$BUDGET."'
	   WHERE RAFID='".$_POST['rafid']."'";
		//echo "$query <br>";
	}else{
	   $query = "INSERT INTO INFOBASE.BSDS_RAF_TXMN (
	   RAFID, UPG_DATE, UPG_BY,  BUDGET)
		VALUES ( '".$_POST['rafid']."' , SYSDATE, '".$guard_username."', '".$BUDGET."')";
	}

	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "RAF TXMN has succesfully been updated!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}

if ($_POST['action']=="update_partner_raf_0"){
	$query = "Select RAFID FROM BSDS_RAF_PARTNER WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	if ($amount_of_RAFS==1){
	//echo $_POST['AREA_900'];
	  $query = "UPDATE BSDS_RAF_PARTNER
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
	   BP_NEEDED_REASON='".escape_sq($_POST['BP_NEEDED_REASON'])."'
	   WHERE RAFID='".$_POST['rafid']."'";
		//echo "$query <br>";
	}else{

	   $query = "INSERT INTO INFOBASE.BSDS_RAF_PARTNER (
		   RAFID, UPG_DATE, UPG_BY,BP_NEEDED_REASON)
	   VALUES ( '".$_POST['rafid']."', SYSDATE, '".$guard_username."','".escape_sq($_POST['FIRST_REASON'])."')";
	}
	//echo "$query <br>";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}

	$message=  "RAF PARTNER ACQ REASON has succesfully been updated!";
	$res["responsedata"] = $message;
	$res["responsetype"]="info";
	echo json_encode($res);

}else if ($_POST['action']=="update_partner_raf_1_4"){
  	$query = "Select RAFID FROM BSDS_RAF_PARTNER WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	$BCS_RESULT=escape_sq($_POST['BCS_RESULT']);

	if ($amount_of_RAFS==1){
	//echo $_POST['AREA_900'];
	  $query = "UPDATE INFOBASE.BSDS_RAF_PARTNER
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
	   BC_DATE1         ='".$_POST['BC_DATE1']."',
	   BC_CANDIDATE1    ='".$_POST['BC_CANDIDATE1']."',
	   BC_ENGINEER1     ='".$_POST['BC_ENGINEER1']."',
	   BC_PROPOSAL1    ='".$_POST['BC_PROPOSAL1']."',
	   BC_DATE2          ='".$_POST['BC_DATE2']."',
	   BC_CANDIDATE2    ='".$_POST['BC_CANDIDATE2']."',
	   BC_ENGINEER2     ='".$_POST['BC_ENGINEER2']."',
	   BC_PROPOSAL2    ='".$_POST['BC_PROPOSAL2']."',
	   BC_DATE3         ='".$_POST['BC_DATE3']."',
	   BC_CANDIDATE3    ='".$_POST['BC_CANDIDATE3']."',
	   BC_ENGINEER3     ='".$_POST['BC_ENGINEER3']."',
	   BC_PROPOSAL3    ='".$_POST['BC_PROPOSAL3']."',
	   BC_DATE4         ='".$_POST['BC_DATE4']."',
	   BC_CANDIDATE4    ='".$_POST['BC_CANDIDATE4']."',
	   BC_ENGINEER4     ='".$_POST['BC_ENGINEER4']."',
	   BC_PROPOSAL4    ='".$_POST['BC_PROPOSAL4']."',
	   BCS_RESULT       ='".$BCS_RESULT."',
	   FIRST_RF    		='".$_POST['FIRST_RF']."',
	   FIRST_MICROWAVE  ='".$_POST['FIRST_MICROWAVE']."',
	   FIRST_CAB    	='".$_POST['FIRST_CAB']."',
	   FIRST_BTS    	='".$_POST['FIRST_BTS']."',
	   FIRST_OTHER    	='".$_POST['FIRST_OTHER']."'
	   WHERE RAFID='".$_POST['rafid']."'";
		//echo "$query <br>";
	}else{

	   $query = "INSERT INTO INFOBASE.BSDS_RAF_PARTNER (
		   RAFID, UPG_DATE, UPG_BY,
		   BC_DATE1, BC_CANDIDATE1, BC_ENGINEER1,
		   BC_PROPOSAL1, BC_DATE2, BC_CANDIDATE2,
		   BC_ENGINEER2, BC_PROPOSAL2, BC_DATE3,
		   BC_CANDIDATE3, BC_ENGINEER3, BC_PROPOSAL3,
		   BC_DATE4, BC_CANDIDATE4, BC_ENGINEER4,
		   BC_PROPOSAL4, BCS_RESULT, FIRST_RF,
		   FIRST_MICROWAVE, FIRST_CAB, FIRST_BTS,
		   FIRST_OTHER)
	   VALUES ( '".$_POST['rafid']."', SYSDATE, '".$guard_username."',
		    '".$_POST['BC_DATE1']."', '".$_POST['BC_CANDIDATE1']."', '".$_POST['BC_ENGINEER1']."',
			'".$_POST['BC_PROPOSAL1']."', '".$_POST['BC_DATE2']."', '".$_POST['BC_CANDIDATE2']."',
			'".$_POST['BC_ENGINEER2']."', '".$_POST['BC_PROPOSAL2']."', '".$_POST['BC_DATE3']."',
			'".$_POST['BC_CANDIDATE3']."', '".$_POST['BC_ENGINEER3']."', '".$_POST['BC_PROPOSAL3']."',
			'".$_POST['BC_DATE4']."', '".$_POST['BC_CANDIDATE4']."', '".$_POST['BC_ENGINEER4']."',
			'".$_POST['BC_PROPOSAL4']."', '".$BCS_RESULT."', '".$_POST['FIRST_RF']."',
			'".$_POST['FIRST_MICROWAVE']."', '".$_POST['FIRST_CAB']."', '".$_POST['FIRST_BTS']."',
			'".$_POST['FIRST_OTHER']."')";
	}
	//echo "$query <br>";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);

		if ($_POST['FIRST_MICROWAVE']=="No" || $_POST['FIRST_CAB']=="No" || $_POST['FIRST_BTS']=="No" || $_POST['FIRST_OTHER']=="No" || $_POST['FIRST_REASON']=="No"){
			if ($config['mail']==true){
				$mail             = new PHPMailer();
				$mail->IsSMTP(); // telling the class to use SMTP
				$mail->Host       = "Infobase"; // SMTP server
				$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
				                                           // 1 = errors and messages
				                                           // 2 = messages only
				$mail->SMTPAuth   = true;                  // enable SMTP authentication
				$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
				$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
				$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
				$mail->Username   = "infobaseticket@gmail.com";  // GMAIL username
				$mail->Password   = "Genie-456";            // GMAIL password
				$mail->AddEmbeddedImage('images/basecompany.png', 'logo_2u');

				$userdetails_Sender=getuserdata($guard_username);
				$fullname_sender=$userdetails_Sender['fullname'];
				$email_sender=$userdetails_Sender['email'];

				$mail->SetFrom($email_sender, 'Infobase');
				//$mail->AddReplyTo($email_sender,$fullname_sender);
				$mail->Subject = $_POST['siteid'].": RAF ".$_POST['id']." is not compiliant!";
				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
				$text_body = "Hi ".$fullname.",<br>";
			   	$text_body .= "The RAF with ID ".$_POST['id']." is not compliant!<br><br>";
			   	$text_body .= "This has been done by <a href='mailto:'".$email_sender."'>".$fullname_sender."</a><br>";
			   	$text_body .= "<u>Reason:</u><br>".$_POST['FIRST_REASON']."<br>";
			   	$text_body .= "http://infobase/bsds/index.php<br><br>";
			   	$text_body .= "<br><br>For Citrix users: please start Infobase via citrix and copy this link in the Infobase window.<br><br>";
			   	$text_body .= "Rgds,<br>From Frederick Eyland for Infobase<br>";
			   	$text_body .= "<img src='cid:logo_2u' width='100px' height='52px'>";
				$mail->Body = $text_body;
				$mail->MsgHTML($text_body);
				
				$mail->AddAddress("Transmission.Design@kpngroup.be", "Transmission.Design@kpngroup.be");

				if(!$mail->Send()) {
				  echo "Mailer Error: " . $mail->ErrorInfo;
				}


				$mail->AddAddress("Transmission.Design@basecompay.be", "Transmission Design");
				//$mail->AddAddress("frederick.eyland@kpngroup.be", "frederick.eyland");
				if(!$mail->Send())
					echo "There has been a mail error sending to Transmission.Design@basecompay.be<br>\r\n";
			}
		}
		$message=  "RAF PARTNER has succesfully been updated!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}

if ($_POST['action']=="update_partner_raf_5_6"){
  	$query = "Select RAFID FROM BSDS_RAF_PARTNER WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	$BCS_RESULT=escape_sq($_POST['BCS_RESULT']);

	if ($amount_of_RAFS==1){
	//echo $_POST['AREA_900'];
	  $query = "UPDATE INFOBASE.BSDS_RAF_PARTNER
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
	   FINAL_RF         ='".$_POST['FINAL_RF']."',
	   FINAL_MICROWAVE  ='".$_POST['FINAL_MICROWAVE']."',
	   FINAL_CAB        ='".$_POST['FINAL_CAB']."',
	   FINAL_BTS        ='".$_POST['FINAL_BTS']."',
	   FINAL_OTHER      ='".$_POST['FINAL_OTHER']."'
	   WHERE RAFID='".$_POST['rafid']."'";
		//echo "$query <br>";
	}else{

	   $query = "INSERT INTO INFOBASE.BSDS_RAF_PARTNER (
		   RAFID, UPG_DATE, UPG_BY,
		    FINAL_RF, FINAL_MICROWAVE,
		   FINAL_CAB, FINAL_BTS, FINAL_OTHER)
	   VALUES ( '".$_POST['rafid']."', SYSDATE, '".$guard_username."',
		    '".$_POST['FINAL_RF']."', '".$_POST['FINAL_MICROWAVE']."',
			'".$_POST['FINAL_CAB']."', '".$_POST['FINAL_BTS']."', '".$_POST['FINAL_OTHER']."')";
	}
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "RAF PARTNER has succesfully been updated!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}

if ($_POST['action']=="update_partner_raf_7"){
  	$query = "Select RAFID FROM BSDS_RAF_PARTNER WHERE RAFID ='".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}

	$BCS_RESULT=escape_sq($_POST['BCS_RESULT']);

	if ($amount_of_RAFS==1){
	//echo $_POST['AREA_900'];
	  $query = "UPDATE INFOBASE.BSDS_RAF_PARTNER
	  SET
	   UPG_DATE=SYSDATE,
	   UPG_BY='".$guard_username."',
	   CNT_TCH          ='".$_POST['CNT_TCH']."',
	   CNT_BLOCKING     ='".$_POST['CNT_BLOCKING']."',
	   CNT_DROPPED      ='".$_POST['CNT_DROPPED']."',
	   CNT_SDCCH        ='".$_POST['CNT_SDCCH']."',
	   CNT_UPQUAL       ='".$_POST['CNT_UPQUAL']."',
	   CNT_DQUAL        ='".$_POST['CNT_DQUAL']."',
	   CNT_SLEEPING     ='".$_POST['CNT_SLEEPING']."',
	   CNT_AVAILABILITY ='".$_POST['CNT_AVAILABILITY']."',
	   CNT_SDCCHDROP    ='".$_POST['CNT_SDCCHDROP']."',
	   TX_TOTALMW       ='".$_POST['TX_TOTALMW']."',
	   TX_BEARINGS      ='".$_POST['TX_BEARINGS']."',
	   TX_AMOUNT        ='".$_POST['TX_AMOUNT']."',
	   TX_LOCATION      ='".$_POST['TX_LOCATION']."',
	   TX_TYPE          ='".$_POST['TX_TYPE']."',
	   TX_TRAY          ='".$_POST['TX_TRAY']."',
	   REPORT_UPLOAD 	='".$_POST['REPORT_UPLOAD']."',
	   COMPLIANT          ='".$_POST['COMPLIANT']."'
	   WHERE RAFID='".$_POST['rafid']."'";
		//echo "$query <br>";
	}else{

	   $query = "INSERT INTO INFOBASE.BSDS_RAF_PARTNER (
		   RAFID, UPG_DATE, UPG_BY,
		   CNT_TCH, CNT_BLOCKING, CNT_DROPPED,
		   CNT_SDCCH, CNT_UPQUAL, CNT_DQUAL,
		   CNT_SLEEPING, CNT_AVAILABILITY, CNT_SDCCHDROP,
		   TX_TOTALMW, TX_BEARINGS, TX_AMOUNT,
		   TX_LOCATION, TX_TYPE, TX_TRAY,REPORT_UPLOAD,COMPLIANT)
	   VALUES ( '".$_POST['rafid']."', SYSDATE, '".$guard_username."',
			'".$_POST['CNT_TCH']."', '".$_POST['CNT_BLOCKING']."', '".$_POST['CNT_DROPPED']."',
			'".$_POST['CNT_SDCCH']."', '".$_POST['CNT_UPQUAL']."', '".$_POST['CNT_DQUAL']."',
			'".$_POST['CNT_SLEEPING']."', '".$_POST['CNT_AVAILABILITY']."', '".$_POST['CNT_SDCCHDROP']."',
			'".$_POST['TX_TOTALMW']."', '".$_POST['TX_BEARINGS']."', '".$_POST['TX_AMOUNT']."',
			'".$_POST['TX_LOCATION']."', '".$_POST['TX_TYPE']."', '".$_POST['TX_TRAY']."',
			'".$_POST['REPORT_UPLOAD']."', '".$_POST['COMPLIANT']."')";
	}

	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "RAF PARTNER has succesfully been updated!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}


if ($_POST['action']=="update_trx_raf"){

   	$query = "INSERT INTO INFOBASE.BSDS_RAF_TRX (
	   RAFID, DATE_OF_SAVE, UPDATE_BY, REQUIREMENTS)
   	VALUES ( '".$_POST['rafid']."', SYSDATE, '".$guard_username."','".escape_sq($_POST['REQUIREMENTS'])."')";
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "RAF TRX REQUIREMENTS have succesfully been inserted!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}


if ($_POST['action']=="update_bcsm"){

//echo "---".print_r($_POST,true);
	$query = "Select MAX(VERSION) AS VERSION from BCS_CANDIDATES WHERE RAFID = '".$_POST['rafid']."'";
	//echo $query;
	$stmt6 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
	if (!$stmt6) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt6);
	}
	$version=$res6['VERSION'][0]+1;

	foreach ($_POST['question'] as $groupID => $questionsIDs) {
		foreach ($questionsIDs as $questionID => $answer) {
			
			$answersplit=explode('_', $answer);
			//echo $groupID."-".$questionID."-".$answersplit[0]."/";
			if ($answersplit[0]=='32' && $_POST['georegion']=='south'){
				$telenetspl=explode('_', $_POST['question'][2][1]);
				//echo "*****".$telenetspl[1]."******";
				if ($telenetspl[1]<=2){
					$score=0;
				}else{
					$score=1;
				}
  			}else{
  				$score=$answersplit[1];
  			}
			$answersplit=explode('_', $answer);
        	$query="INSERT INTO BCS_CANDIDATES VALUES('".$_POST['rafid']."','".$_POST['candidate']."','".$questionID."','".$answersplit[0]."','".$groupID."','".$version."','".$score."')";
        	//echo $query;
        	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
		}
    }
    $message=  "SAVED";
	$res["siteID"] = $_POST['siteID'];
	$res["rafid"] = $_POST['rafid'];
	$res["responsetype"]="info";
	echo json_encode($res);
  
}
/*
if ($_POST['action']=="backtoacq"){
	$query = "UPDATE BSDS_RAFV2 SET CON_PARTNER='NOT OK',CON_PARTNER_BY='', CON_PARTNER_DATE='',	 
	RADIO_INP='NOT OK', RADIO_INP_BY='', RADIO_INP_DATE='',
	TXMN_INP='NOT OK', TXMN_INP_BY='', TXMN_INP_DATE='',
	PARTNER_INP='NOT OK', PARTNER_INP_BY='', PARTNER_INP_DATE='',
	COF_ACQ='NOT OK', COF_ACQ_BY='', COF_ACQ_DATE='',

	if (upgrade)
		BCS_RF_INP='NOT OK', BCS_RF_INP_BY='', BCS_RF_INP_DATE='',
		BCS_TX_INP='NOT OK', BCS_TX_INP_BY='', BCS_TX_INP_DATE='',
	else if nbs
		BCS_RF_INP='NOT OK', BCS_RF_INP_BY='', BCS_RF_INP_DATE='',
		BCS_TX_INP='NOT OK', BCS_TX_INP_BY='', BCS_TX_INP_DATE='',

	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "RAF is now back to ACQ!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}

}*/


/*
/*
					//toggle lease OK in NET1
					$query = "SELECT N1_SITEID,N1_CANDIDATE,N1_UPGNR,N1_NBUP FROM MASTER_REPORT WHERE IB_RAFID='".$_POST['id']."'";
					//echo $query;
					$stmt= parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					}else{
						OCIFreeStatement($stmt);
					}
					if (count($res['N1_SITEID'])==1){

						if ($res['N1_NBUP'][0]=='UPG'){
							$query="INSERT INTO INFOBASE.NET1UPDATER_CSV VALUES ('".$res['N1_SITEID'][0]."','','U709','".date('d-m-Y')."','RAF_BP_NEEDED_NO',SYSDATE,'0','','','".$res['N1_CANDIDATE'][0]."')";
						}else{
							echo "error when deleting";
							die;
						}
						//echo $query;
						$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
						if (!$stmt) {
							die_silently($conn_Infobase, $error_str);
						}else{
							OCICommit($conn_Infobase);
						}
					}*/
?>
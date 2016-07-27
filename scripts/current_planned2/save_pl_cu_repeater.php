<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_viewtype","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
include("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


if ($_POST['lognode']!="" && $_POST['bsdskey']!=""){
	if ($_POST['band']=="L18" or $_POST['band']=="L26" or $_POST['band']=="L8"){
		$tabletype="LTE";
	}else if ($_POST['band']=="U21" or $_POST['band']=="U9"){
		$tabletype="UMTS";
	}else if ($_POST['band']=="G9" or $_POST['band']=="G18"){
		$tabletype="GSM";
	}else{
		die;
	}
//**************** CURRENT SAVE OR UPDATE **********************************************/

	$check_current_exists=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['bsdsbobrefresh'],'allsec',$_POST['donor'],$_POST['lognode'],$_POST['viewtype']);
	//FUND, BUILD and POST can not be updated!
	if ($check_current_exists=="0"){
		$query = "INSERT INTO BSDS_CU_REP_".$tabletype." 
		VALUES ('".$_POST['bsdskey']."','".$_POST['lognode']."', SYSDATE,";
		if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
			$query.="'".$_POST['bsdsbobrefresh']."',";
		}else if ($_POST['viewtype']=="PRE"){
			$query.="'',";
		}	
		$query.="'".$_POST['OWNER']."','".$_POST['BRAND']."','".$_POST['RTYPE']."','".$_POST['TECHNOLOGY']."',
		'".$_POST['CHANNEL']."','".$_POST['PICKUP']."','".$_POST['DISTRIB']."','".$_POST['COSP']."',
		'".$_POST['COMMENTS']."','".$_POST['band']."','PRE')"; //We only save into PRE, for fund => we take a copy of pre
		$action="saved";
	}else{
		$query = "UPDATE BSDS_CU_REP_".$tabletype."  SET
		 CHANGE_DATE = SYSDATE,
		 OWNER='".$_POST['OWNER']."',
		 BRAND='".$_POST['BRAND']."',
		 TYPE='".$_POST['RTYPE']."',
		 TECHNOLOGY='".$_POST['TECHNOLOGY']."',
		 CHANNEL='".$_POST['CHANNEL']."',
		 PICKUP='".$_POST['PICKUP']."',
		 DISTRIB='".$_POST['DISTRIB']."',
		 COSP='".$_POST['COSP']."',
		 COMMENTS='".$_POST['COMMENTS']."'
		 WHERE SITEKEY= '".$_POST['lognode']."' AND TECHNO='".$_POST['band']."' AND STATUS='PRE'";
		 $action="updated";
	}
	//echo $query;
	$stmt2 = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt2) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$message="CURRENT DATA has been ".$action." for ".$_POST['band']."!<br>";
	}
	OCICommit($conn_Infobase);


//**************** PLANNED SAVE OR UPDATE **********************************************/

	
		if(empty($ERROR_MESSAGE)){

			$pl_Count=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],$n,$_POST['viewtype'],$_POST['donor']);
			//echo "pl_Count $pl_Count<br>";

			// INSERT OR UPDATE THE BSDSDATA
			if ($pl_Count=="0" || $pl_Count==""){

				$query2 = "INSERT INTO BSDS_PL_REP_".$tabletype."  VALUES 
				('".$_POST['bsdskey']."',";
				if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND" || $_POST['viewtype']=="BUILD"){
					$query2.="'".$_POST['bsdsbobrefresh']."',";
				}else{
					$query2.="'',";
				}
				$query2.="'".$_POST['pl_OWNER']."','".$_POST['pl_BRAND']."','".$_POST['pl_RTYPE']."','".$_POST['pl_TECHNOLOGY']."',
				'".$_POST['pl_CHANNEL']."','".$_POST['pl_PICKUP']."','".$_POST['pl_DISTRIB']."','".$_POST['pl_COSP']."',
				'".$_POST['pl_COMMENTS']."','".$_POST['band']."','".$_POST['viewtype']."')";
				$action="saved";

			}else if ($pl_Count=="1"){
				$query2 = "UPDATE BSDS_PL_REP_".$tabletype."  SET
				 OWNER='".$_POST['pl_OWNER']."',
				 BRAND='".$_POST['pl_BRAND']."',
				 TYPE='".$_POST['pl_RTYPE']."',
				 TECHNOLOGY='".$_POST['pl_TECHNOLOGY']."',
				 CHANNEL='".$_POST['pl_CHANNEL']."',
				 PICKUP='".$_POST['pl_PICKUP']."',
				 DISTRIB='".$_POST['pl_DISTRIB']."',
				 COSP='".$_POST['pl_COSP']."',
				 COMMENTS='".$_POST['pl_COMMENTS']."'
				 WHERE BSDSKEY= '".$_POST['bsdskey']."' AND TECHNO='".$_POST['band']."' AND STATUS='".$_POST['viewtype']."'";
				 if ($_POST['viewtype']=="POST" || $_POST['viewtype']=="FUND"  || $_POST['viewtype']=="BUILD"){ //build only for admin
					$query2.=" AND BSDS_BOB_REFRESH=to_date('".$_POST['bsdsbobrefresh']."')";
				}
				 $action="updated";
			}
			//echo "$query2";
			$stmt2 = parse_exec_free($conn_Infobase, $query2, $error_str);
		   	if (!$stmt2) {
		    	die_silently($conn_Infobase, $error_str);
		   	}else{
				$message.="PLANNED DATA has been ".$action." for ".$_POST['band']."!<br>";
		   	}
		   	OCICommit($conn_Infobase);

			if ($_POST['viewtype']!="POST"){
				// UPDATE THE CHANGEDATE
				if ($_POST['bsdskey']!=''){
					$query4 = "UPDATE BSDS_GENERALINFO set CHANGE_DATE=SYSDATE, DESIGNER_UPDATE='$guard_username' WHERE BSDSKEY='".$_POST['bsdskey']."'";
					//echo "$query4 <br>";
					$stmt2 = parse_exec_free($conn_Infobase, $query4, $error_str);
					if (!$stmt2) {
						die_silently($conn_Infobase, $error_str);
					}else{
						//$message.="BSDS CHANGEDATE has succesfully been updated!<br>";
					}
					OCICommit($conn_Infobase);

					$type=$_POST['band'];
				}else{
					echo 'Infobase lost the BSDSKEY. Please contact Infobase admin asap!';
					die;
				}
			}else{
				// UPDATE THE CHANGEDATE
				if ($_POST['bsdskey']!=''){
					$query4 = "UPDATE BSDS_GENERALINFO set UPDATE_AFTER_COPY=SYSDATE, UPDATE_BY_AFTER_COPY='".$guard_username."' WHERE BSDSKEY='".$_POST['bsdskey']."'";
					//echo "$query4 <br>";
					$stmt2 = parse_exec_free($conn_Infobase, $query4, $error_str);
					if (!$stmt2) {
						die_silently($conn_Infobase, $error_str);
					}else{
						//$message.="BSDS UPDATE DATE has succesfully been adapted!<br>";
					}
					OCICommit($conn_Infobase);

					$type=$_POST['band'];
				}else{
					echo 'Infobase lost the BSDSKEY. Please contact Infobase admin asap!';
					die;
				}
			}
		}else{//END ERROR
			$warning.="PLANNED data could not be saved because of errors!<br>";
			$warning.="$ERROR_MESSAGE";
			$message="";
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
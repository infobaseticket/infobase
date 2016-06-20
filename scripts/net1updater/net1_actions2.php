<?php
require_once("/var/www/html/bsds/config.php");
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

/*
if ($guard_username==""){
	$user['netoneuser']='Vand_p';
	$user['netonepass']='rome';
}else{
	$user=getuserdata($guard_username);
}
$conn_Netone = oci_connect($user['netoneuser'],$user['netonepass'], $sid_Netone);
$stmt = OCIParse($conn_Netone,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
*/
if ($_POST['netoneuser']){
	$conn_Netone = oci_connect($_POST['netoneuser'],$_POST['netonepass'], $sid_Netone);
	$stmt = OCIParse($conn_Netone,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
	OCIExecute($stmt,OCI_DEFAULT);
}

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$keymilestones=array('A04','A15','A22','A26','A32','A34','A41','A352','A353','A55','A59','A65','A91','A92','A75','A77','A76','A71','A83','A80','A72','A81','A200','A250','A275');
//$keymilestones=array();

function get_net1_date($siteid,$code,$upgnr,$type,$conn_Netone){
	global $config;

	if ($state==""){
		$state="PREF";
	}

	if($type=="UPG"){
		$query="select WOR_ID AS WORID,
		wot_complete as COMPLETE, 
		WOT.wot_estimate as ESTIMATE, 
		WOT.wot_notes as NOTES,    
		WOT.wot_sit_id AS SITID,
		SIT_UDK,
		WOE_DOM_WES_CODE 
		from WORKS_ORDERS@".$config['net1db']." WO INNER JOIN WORKS_ORDER_TASKS@".$config['net1db']." WOT 
		ON WOT.WOT_WOR_ID=WO.WOR_ID
		INNER JOIN WORKS_ORDER_ELEMENTS@".$config['net1db']." WOE 
		ON WOE.WOE_WOR_ID=WO.WOR_ID
		INNER JOIN SITES@".$config['net1db']." SIT
	    ON SIT.SIT_ID=WOE.WOE_SIT_ID
	    WHERE
		WOT.WOT_TOS_TAS_CODE='".$code."'
		AND WO.WOR_UDK='".$upgnr."'";
	}else if($type=="NEW"){
		$query="select WOR_ID AS WORID,
		wot_complete as COMPLETE, 
		WOT.wot_estimate as ESTIMATE, 
		WOT.wot_notes as NOTES,    
		WOT.wot_sit_id AS SITID,
		SIT_UDK,
		WOE_DOM_WES_CODE
		from WORKS_ORDERS@".$config['net1db']." WO INNER JOIN WORKS_ORDER_TASKS@".$config['net1db']." WOT 
		ON WOT.WOT_WOR_ID=WO.WOR_ID
		INNER JOIN WORKS_ORDER_ELEMENTS@".$config['net1db']." WOE 
		ON WOE.WOE_WOR_ID=WO.WOR_ID
		INNER JOIN SITES@".$config['net1db']." SIT
	    ON SIT.SIT_ID=WOE.WOE_SIT_ID
	    WHERE
		(
		 	WOT.WOT_TOS_TAS_CODE='".$code."'
			AND SIT.SIT_UDK='".$siteid."'
			AND WOT_SIT_ID IS NOT NULL
			AND WOT.WOT_SIT_ID=SIT.SIT_ID
			AND WOT_SIT_UDK='".$siteid."'
		)or (
			WOT.WOT_TOS_TAS_CODE='".$code."'
			AND SIT.SIT_UDK='".$siteid."'
			AND WOT_SIT_ID IS NULL
		)";
	}
	//echo $siteid."<br>".$query."<hr>";
	$stmt = parse_exec_fetch($conn_Netone, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Netone, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount=count($res1['WORID']);

		if ($amount==1){
	
			//if (($res1["WOE_DOM_WES_CODE"][0]=='PREF' && $type=="NEW") or $type=="UPG"){
				if ($res1["COMPLETE"][0]!=''){
					$out["complete"]=substr($res1['COMPLETE'][0],0,10);
				}else{
					$out["complete"]='NOT SET';
				}
				if ($res1["ESTIMATE"][0]!=''){
					$out["estimate"]=substr($res1['ESTIMATE'][0],0,10);
				}else{
					$out["estimate"]='NOT SET';
				}
				if ($res1["NOTES"][0]!=''){
					$out["notes"]=$res1['NOTES'][0];
				}else{
					$out["notes"]='NOT SET';
				}

				$out["SITID"]=$res1['SITID'][0];
				$out["WORID"]=$res1['WORID'][0];
				$out["SIT_UDK"]=$res1['SIT_UDK'][0];
				//echo "--<pre>".print_r($out,true)."</pre>--";

				return $out;
			//}elseif ($res1["WOE_DOM_WES_CODE"][0]=='CAND'){
			//	return 'ERRORCAND';
			//}
		}else{
			$query="select
				WOE_DOM_WES_CODE
				from WORKS_ORDERS@".$config['net1db']." WO INNER JOIN WORKS_ORDER_TASKS@".$config['net1db']." WOT 
				ON WOT.WOT_WOR_ID=WO.WOR_ID
				INNER JOIN WORKS_ORDER_ELEMENTS@".$config['net1db']." WOE 
				ON WOE.WOE_WOR_ID=WO.WOR_ID
				INNER JOIN SITES@".$config['net1db']." SIT
			    ON SIT.SIT_ID=WOE.WOE_SIT_ID
			    WHERE
				(
				 	WOT.WOT_TOS_TAS_CODE='".$code."'
					AND SIT.SIT_UDK='".$siteid."'
					AND WOT_SIT_ID IS NOT NULL
					AND WOT_SIT_UDK='".$siteid."'
				 	AND WO.WOR_DOM_WOS_CODE !='DM'
				 	AND WO.WOR_DOM_WOS_CODE !='DL'
				 	AND WOE_DOM_WES_CODE!='PREF'
				)or (
					WOT.WOT_TOS_TAS_CODE='".$code."'
					AND SIT.SIT_UDK='".$siteid."'
					AND WOT_SIT_ID IS NULL
					AND WO.WOR_DOM_WOS_CODE !='DM'
					AND WO.WOR_DOM_WOS_CODE !='DL'
					AND WOE_DOM_WES_CODE!='PREF'
				)";
	
				//echo $siteid."<br>".$query."<hr>";
				$stmt = parse_exec_fetch($conn_Netone, $query, $error_str, $res1);
				if (!$stmt) {
					die_silently($conn_Netone, $error_str);
				 	exit;
				} else {
					OCIFreeStatement($stmt);
				}
				
				if (count($res1['WOE_DOM_WES_CODE'])>=1){
					return 'ERRORCAND';
				}else{
					return 'ERROR';
				}
		}
	}
}

if(!function_exists('str_getcsv')) {
    function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
        $fp = fopen("php://memory", 'r+');
        fputs($fp, $input);
        rewind($fp);
        $data = fgetcsv($fp, null, $delimiter, $enclosure); // $escape only got added in 5.3.0
        fclose($fp);
        return $data;
    }
}

function updatePartie($site_upgnr,$code,$partnerCode){

	global $conn_Infobase;
	global $conn_Netone;
	global $guard_username;
	global $keymilestones;
	global $config;
	global $user;

	if (substr($site_upgnr,0,2)=='99' || $code=='WIP-A' || $code=='SAC'){ //UPG
		$query = "select WOR_ID from WORKS_ORDERS@".$config['net1db']." WO LEFT JOIN TRANSACTION_PARTIES@".$config['net1db']." TP 
	ON WO.WOR_ID=TP.TXP_PRIMARY_KEY 
	 WHERE WOR_UDK='".strtoupper(trim($site_upgnr))."' AND TXP_LKP_PTP_CODE='".strtoupper(trim($code))."' AND TXP_ATB_TABLE='WORKS_ORDERS'";
	$txp_table='WORKS_ORDERS';
	}else if  ($code=='WIP-C' || $code=='CON'){ //NB
		$query = "select SIT_ID AS WOR_ID from SITES@".$config['net1db']." SIT
		LEFT JOIN TRANSACTION_PARTIES@".$config['net1db']." TXP ON TXP.TXP_PRIMARY_KEY=SIT.SIT_ID
		WHERE SIT_UDK='".strtoupper(trim($site_upgnr))."'
		AND TXP.TXP_LKP_PTP_CODE='".strtoupper(trim($code))."' AND TXP_ATB_TABLE='SITES'";
		$txp_table='SITES';
	}
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Netone, $query, $error_str, $res1);
	if (!$stmt) {
		 die_silently($conn_Netone, $error_str);
		 exit;
	} else {
		 OCIFreeStatement($stmt);
	}
	$amount=count($res1['WOR_ID']);

	if ($amount==0){
		if (substr($site_upgnr,0,2)=='99' || $code=='WIP-A' || $code=='SAC'){
			$query = "select WOR_ID from WORKS_ORDERS@".$config['net1db']." WHERE WOR_UDK='".strtoupper(trim($site_upgnr))."'";
			
		}else if ($code=='WIP-C' || $code=='CON'){
			$query = "select SIT_ID AS WOR_ID from SITES@".$config['net1db']." WHERE SIT_UDK='".strtoupper(trim($site_upgnr))."'";
			
		}
		//echo $query."<br>";

		$stmt = parse_exec_fetch($conn_Netone, $query, $error_str, $res1);
		if (!$stmt) {
			 die_silently($conn_Netone, $error_str);
			 exit;
		} else {
			 OCIFreeStatement($stmt);
		}
		
		$query="INSERT INTO TRANSACTION_PARTIES@".$config['net1db']." VALUES ('".$txp_table."','".$res1['WOR_ID'][0]."',
		'".strtoupper(trim($partnerCode))."','".strtoupper(trim($code))."' ,'".$user['netoneuser']."',SYSDATE)";
		//echo $query."<hr>";
		$stmt = parse_exec_free($conn_Netone, $query, $error_str);
		if (!$stmt){
			die_silently($conn_Netone, $error_str);
		}else{
			OCICommit($conn_Netone);
			$message="INSERTED";
			$type="info";
		}

	}elseif ($amount==1){
		
		$query="UPDATE TRANSACTION_PARTIES@".$config['net1db']." SET TXP_PTY_ID='".strtoupper(trim($partnerCode))."', TXP_LAST_USER= '".$user['netoneuser']."',
		TXP_TIMESTAMP=SYSDATE WHERE TXP_LKP_PTP_CODE='".strtoupper(trim($code))."' AND TXP_ATB_TABLE='".$txp_table."' AND TXP_PRIMARY_KEY='".$res1['WOR_ID'][0]."'";
		//echo $query."<hr>";
		$stmt = parse_exec_free($conn_Netone, $query, $error_str);
		if (!$stmt){
			die_silently($conn_Netone, $error_str);
		}else{
			OCICommit($conn_Netone);
			$message="UPDATED";
			$type="info";
		}		
		
	}else{
		$message= "SYSTEM ERROR, contact Fred!";
		$type="error";
	}

	$out["message"]=$message;
	$out["type"]=$type;
	return $out;

}


function updateStatus($site_upgnr,$status){

	global $conn_Infobase;
	global $conn_Netone;
	global $guard_username;
	global $config;

	$query="UPDATE WORKS_ORDERS@".$config['net1db']." SET WOR_DOM_WOS_CODE='".strtoupper(trim($status))."' WHERE WOR_UDK='".strtoupper(trim($site_upgnr))."'";
	//echo $query."<hr>";
	$stmt = parse_exec_free($conn_Netone, $query, $error_str);
	if (!$stmt){
		die_silently($conn_Netone, $error_str);
	}else{
		OCICommit($conn_Netone);
		$message="UPDATED";
		$type="info";
	}

	$out["message"]=$message;
	$out["type"]=$type;
	return $out;
}

function updatecode($site,$upgnr,$code,$insertdate,$estimate,$notes,$override,$conn_Netone){
	global $conn_Infobase;
	global $guard_username;
	global $keymilestones;
	global $config;
	$start=substr(strtoupper($code),0,1);
	if ($start=="A"){
		$type="NEW";
	}elseif ($start=="U"){
		$type="UPG";
	}
	//We first check if there is not already a date set
	$net1_date=get_net1_date($site,$code,$upgnr,$type,$conn_Netone);
	//echo "<pre>-----".print_r($net1_date,true)."</pre>";
	if ($net1_date!="ERROR" and $net1_date!="ERRORCAND"){
		if ($estimate=='1'){
			$net1Already=$net1_date["estimate"];
		}else{
			$net1Already=$net1_date["complete"];
		}
		$WORID=$net1_date["WORID"];
		$SITID=$net1_date["SITID"];

		if ($net1Already=='NOT SET' or $override=="yes"){
			if (($start=="A" && $upgnr=='') or ($start=="U"  && $upgnr!='')){
				if ($insertdate!=''){
					//ST FIRST TO EXE
					$query="UPDATE works_order_tasks@".$config['net1db']." SET WOT_LKP_TAS_CODE='EXE'  WHERE 
						 wot_tos_tas_code = NVL('".$code."',wot_tos_tas_code) 
						 AND wot_wor_id='".$WORID."'";
						 if ($SITID!=""){
						 	$query.=" AND WOT_SIT_ID='".$SITID."'";
						 }
						// echo $query."<hr>";
					$stmt = parse_exec_free($conn_Netone, $query, $error_str);
					if (!$stmt){
						die_silently($conn_Netone, $error_str);
					}else{
						OCICommit($conn_Netone);
					}
					$insertdate=date("d-M-Y",strtotime($insertdate));
					$status='COM';	
				}else{
					$status='PEN';
					$date='';
				}

				$query="UPDATE works_order_tasks@".$config['net1db']." SET ";
				if ($estimate=='1'){
					$query.="wot_estimate='".$insertdate."',";
				}else{
					$query.="wot_complete='".$insertdate."',WOT_NOTES='".$notes."',";
				}
				$query.=" WOT_LKP_TAS_CODE='".$status."'  WHERE 
				 wot_tos_tas_code = NVL('".$code."',wot_tos_tas_code) 
				 AND wot_wor_id='".$WORID."'";
				 //echo $query."<hr>";
				if ($SITID!=""){
				 	$query.=" AND WOT_SIT_ID='".$SITID."'";
				}
				//echo "$query<hr>";
				$stmt = parse_exec_free($conn_Netone, $query, $error_str);
				if (!$stmt){
					die_silently($conn_Netone, $error_str);
				}else{
					OCICommit($conn_Netone);

					if (in_array(strtoupper($code),$keymilestones)){					
						//UPDATE LATEST MILESTONE IN NET1 DETAILS SCREEN
						$query="BEGIN dbp_works_orders.set_wo_current_milestone (".$WORID.",NULL); END;";
						//echo $query;
						$stmt = parse_exec_free($conn_Netone, $query, $error_str);
						if (!$stmt){
							die_silently($conn_Netone, $error_str);
						}else{
							OCICommit($conn_Netone);
						}
						if ($SITID==""){
							$SITID="NULL";
						}
						//UPDATE LATEST MILESTONE IN NET1 TASKS SCREEN
						$query2="BEGIN dbp_works_orders.set_wo_current_site_milestone (".$WORID.",".$SITID."); END;";
						//echo $query;
						$stmt2 = parse_exec_free($conn_Netone, $query2, $error_str);
						if (!$stmt2){
							die_silently($conn_Netone, $error_str);
						}else{
							OCICommit($conn_Netone);
						}
					}
					if ($estimate=='1'){
						$message= "ESTIMATE date for ".$code ." has been updated to '".$insertdate."'";
					}else{
						$message= "COMPLETE date for ".$code ." has been updated to '".$insertdate."'";
					}

					$type="info";
					$query2="INSERT INTO NET1UPDATER_LOG VALUES (SYSDATE,'".$site."','".$upgnr."','".$code."', '".$guard_username."','".$insertdate."','".$estimate."','".$notes."')";
					$stmt2 = parse_exec_free($conn_Infobase, $query2, $error_str);
					if (!$stmt2) {						
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}
				}	
			}else{
				$message= "Code needs to start with A or U and UPGNR has to be empty for NEWBUILDS!";
				$type="error";
			}
		}else if ($net1Already==''){
			$message= "Site not found in NET1 database!";
			$type="error";
			$overide="no";
		}else{
			if ($estimate=='1'){
				$message= "There is already a ESTIMATE date in NET1 for '".$code.": ".$net1Already."<br>Click confirm if you are 100% sure!'";
			}else{
				$message= "There is already a COMPLETE date in NET1 for '".$code.": ".$net1Already."<br>Click confirm if you are 100% sure!'";	
			} 
			$type="error";
			$overide="yes";
		}
	}else if ($net1_date=="ERROR"){
		$message= "The code ".$code." does not exist for that site in NET1!";
		$type="error";
		$overide="no";
	}else if ($net1_date=="ERRORCAND"){
		$message= "The candidate is not  PREF!";
		$type="error";
		$overide="no";
	}
	$out["message"]=$message;
	$out["site"]=$site;
	$out["type"]=$type;
	$out["overide"]=$overide;
	return $out;
}

if ($_POST['action']=="updatecode"){

	if ($_POST['element']!="" && strlen( $_POST['element'])==8){
		$code=explode("-",$_POST['code']);
		$code=$code[0];
		$notes=escape_sq($_POST['notes']);
		$out=updatecode($_POST['element'],$_POST['upgnr'],$code,$_POST['insertdate'],$_POST['estimate'],$notes,$_POST['override'],$conn_Netone);
		if ($out=="error"){
			$out["message"]= $message= "The code ".$code." does not exist for that site in NET1!";
			$out["type"]="error";
		}
	}else{
		$out["message"]= "Please provide SITE ID (must be 7 characters) or UPG number";
		$out["type"]="error";
	}
	
	$res["responsedata"] = $out["message"];
	$res["responsetype"]=$out["type"];
	$res["overide"]=$out["overide"];
	echo json_encode($res);

}else if ($_POST['action']=="getcode"){
		$code=$_POST['code'];
		$date=date("d-M-Y",strtotime($_POST['datum']));
		$start=substr(strtoupper($_POST['code']),0,1);
		if ($start=="A" && $_POST['upgnr']==''){
			if ($_POST['element']!="" && strlen( $_POST['element'])!=7){
				$net1_date=get_net1_date($_POST['element'],$code,'','NEW',$conn_Netone);

				if ($net1_date!="ERROR" && $net1_date!="ERRORCAND"){
					$net1Out="<b>".$net1_date['SIT_UDK'].": ".$code."</b><br>COMPLETE: ".$net1_date['complete']."<br>ESTIMATE: ".$net1_date['estimate']."<br>NOTES: ".$net1_date['notes'];
					$type="info";
				}else if ($net1_date=="ERROR"){
					$net1Out="NEW: CANDIDATE NOT FOUND IN NET1";
					$type="error";
				}else if ($net1_date=="ERRORCAND"){
					$net1Out="NEW: CANDIDATE IS NOT PREFERRED";
					$type="error";
				}
			}else{
				$net1Out= "Please provide SITE ID or UPG number <br>(must be 7 characters)";
				$type="error";
			}	
		}else if ($start=="U"  && $_POST['upgnr']!=''){			
			$net1_date=get_net1_date($_POST['element'],$code,$_POST['upgnr'],'UPG',$conn_Netone);
			if ($net1_date!="ERROR" && $net1_date!="ERRORCAND"){
				$net1Out="<b>".$net1_date['SIT_UDK'].": ".$code."</b><br>COMPLETE: ".$net1_date['complete']."<br>ESTIMATE: ".$net1_date['estimate']."<br>NOTES: ".$net1_date['notes'];
				$type="info";
			}else if ($net1_date=="ERROR"){
				$net1Out="UPG: CANDIDATE NOT FOUND IN NET1";
				$type="error";
			}else if ($net1_date=="ERRORCAND"){
				$net1Out="UPG: CANDIDATE IS NOT PREFEERD";
				$type="error";
			}
		}else{
			$net1Out= "Code needs to start with A or U and UPGNR has to be empty for NEWBUILDS!";
			$type="error";
		}

	$res["responsedata"] = $net1Out;
	$res["responsetype"]=$type;
	$res["overide"]=$overide;
	echo json_encode($res);

}else if ($_POST['action']=="multianalyse"){
	//$data=str_getcsv($_POST['csvdata']);
	//echo "<pre>".print_r($data)."<pre>";

	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdata']));
	if (trim($data[0][0])!="SITE" or trim($data[0][1])!="UPGNR" or trim($data[0][2])!="CODE" or trim($data[0][3])!="DATE" or trim($data[0][4])!="ESTIMATE"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect';
		echo json_encode($res);
		exit;
	}
	//We now analyse the input
	$i=0;
	
	foreach ($data as $key => $result) {
		$message="";
		$status="OK";
		if ($i!=0){
			$startCode=substr(strtoupper(trim($result['2'])),0,1);
			if ($startCode!="U" && $startCode!="A"){
				$message.="CODE has to start with A or U<br>";
				$status='NOTOK';
				$general_status='NOTOK';
			}else if ($startCode=="U" && $result['1']==""){
				$message.="For an UPG we need a UPGNR<br>";
				$status='NOTOK';
				$general_status='NOTOK';
			}else if (($result['4']!=0 && $result['4']!=1) or trim($result['4'])==""){
				$message.="ESTIMATE info to be provided<br>";
				$status='NOTOK';
				$general_status='NOTOK';
			}else{
				$query = "select DISTINCT(TAS_CODE) AS TAS_CODE from TASKS@".$config['net1db']." WHERE upper(TAS_CODE)='".strtoupper(trim($result['2']))."'";
				//echo $query."<br>";
				$stmt = parse_exec_fetch($conn_Netone, $query, $error_str, $res1);
				if (!$stmt) {
				  	die_silently($conn_Netone, $error_str);
				  	exit;
				} else {
				  OCIFreeStatement($stmt);
				}
				$amount=count($res1['TAS_CODE']);
				if ($amount!=1){
					$status='NOTOK';
					$general_status='NOTOK';
					$message.="Task code not found in NET1<br>";
				}
			}	

			if ($result['0']=="" or strlen(trim($result['0']))!=8){
				$status='NOTOK';
				$general_status='NOTOK';
				$message.="SiteID must be 7 characters long<br>";
			}
			if ($result['3']!=''){
				$newdate=str_replace('/', '-', trim($result['3']));
				$time = strtotime($newdate);
				$is_valid = date('d-m-Y', $time) == $newdate;
				if(!$is_valid){
					$status='NOTOK';
					$general_status='NOTOK';
					$message.="Date wrongly formatted: dd-mm-yyyy<br>";
				}
			}

			if ($status=="OK"){
				$class="success";
			}else{
				$class="danger";
			}
			$out.='<tr class="'.$class.'"><td>'.$i.'</td><td>'.trim($result['0']).'</td>';
			$out.="<td>".trim($result['1'])."</td>";
			$out.="<td>".trim($result['2'])."</td>";
			$out.="<td>".trim($newdate)."</td>";
			$out.="<td>".trim($result['4'])."</td>";
			$out.="<td>".trim($message)."</td></tr>";
			
		}
		$i++;
	}
	$out="<table class='table' style='width:700px;'><thead><th>ID</th><th>SITE</th><th>UPGNR</th><th>CODE</th><th>DATE</th><th>ESTIMATE</th></thead>".$out."</table>";
	if ($general_status!='NOTOK'){
		$retval["type"]='info';
		$retval["table"]=$out;
	}else{
		$retval["type"]='error';
		$retval["message"]="You have errors in your csv data!";
		$retval["table"]=$out;
	}	
	
	echo json_encode($retval);

}else if ($_POST['action']=="multiimport"){
	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdata']));
	if ($data[0][0]!="SITE" && $data[0][1]!="UPGNR" && $data[0][2]!="CODE" && $data[0][3]!="DATE"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect';
		echo json_encode($res);
		exit;
	}
	//We now analyse the input
	$i=0;
	//echo "<pre>".print_r($results,true)."</pre>";
	$general="info";
	foreach ($data as $key => $result) {
		if ($i!=0){
			if (trim($result['3'])!=''){
				$newdate=str_replace('/', '-', trim($result['3']));
			}

			$out=updatecode(trim($result['0']),trim($result['1']),trim($result['2']),$newdate,trim($result['4']),trim($result['5']),'yes',$conn_Netone);
			//echo "<pre>".print_r($out,true)."</pre><hr>";
			$table.='<tr class="success"><td>'.$i.'</td><td>'.$result['0'].'</td>';
			$table.="<td>".$result['1']."</td>";
			$table.="<td>".$result['2']."</td>";
			$table.="<td>".$newdate."</td>";
			$table.="<td>".$result['4']."</td>";
			$table.="<td>".$out['message']."</td>";
			$table.="<td>".$out['type']."</td></tr>";
			if ($out['type']=='error' && $general!='error'){
				$general='error';
				$data["message"]="Issues with importing!";
			}
		}
		$i++;
	}
	$table="<table class='table' style='width:900px'><thead><th>ID</th><th>SITE</th><th>UPGNR</th><th>CODE</th><th>DATE</th><th>ESTIMATE</th><th>STATUS</th></thead>".$table."</table>";
	$data["type"]=$general;
	$data["table"]=$table;	
	echo json_encode($data);

}else if ($_POST['action']=="multianalyseStatus"){
	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdataStatus']));

	if ($data[0][0]!="SITE_UPGNR" && $data[0][1]!="STATUS"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect';
		echo json_encode($res);
		exit;
	}
	//We now analyse the input
	$i=0;
	foreach ($data as $key => $result){
		$message="";
		$status="OK";
		if ($i!=0 && $result['0']!=''){
		
			if ($result['1']!='IS' && $result['1']!='CL' && $result['1']!='DL' && $result['1']!='OH'){
				$status='NOTOK';
				$general_status='NOTOK';
				$message.="STATUS has to be IS, DL, CL or OH<br>";
			}


			$query = "select WOR_UDK AS RES from WORKS_ORDERS@".$config['net1db']." WHERE WOR_UDK='".strtoupper(trim($result['0']))."'";
			//echo $query."<br>";
			$stmt = parse_exec_fetch($conn_Netone, $query, $error_str, $res1);
			if (!$stmt) {
				 die_silently($conn_Netone, $error_str);
				 exit;
			} else {
				 OCIFreeStatement($stmt);
			}
		
			if (count($res1['RES'])!=1){
				$status='NOTOK';
				$general_status='NOTOK';
				$message.="SITE/UPGR not found in NET1!";
			}

			if ($status=="OK"){
				$class="success";
			}else{
				$class="danger";
			}
			$out.='<tr class="'.$class.'"><td>'.$i.'</td><td>'.trim($result['0']).'</td>';
			$out.="<td>".trim($result['1'])."</td>";
			$out.="<td>".trim($message)."</td></tr>";
		}
		$i++;
	}

	if ($i==1){
		$res["type"]='error';
		$res["message"]='No data provided';
		echo json_encode($res);
		exit;
	}

	$out="<table class='table' style='width:700px;'><thead><th>ID</th><th>UPGNR/SITE</th><th>STATUS</thead>".$out."</table>";
	if ($general_status!='NOTOK'){
		$retval["type"]='info';
		$retval["table"]=$out;
	}else{
		$retval["type"]='error';
		$retval["message"]="You have errors in your csv data!";
		$retval["table"]=$out;
	}	
	echo json_encode($retval);
}else if ($_POST['action']=="multiimportStatus"){
	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdataStatus']));

	if ($data[0][0]!="SITE_UPGNR" or $data[0][1]!="STATUS"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect!';
		echo json_encode($res);
		exit;
	}

	$i=0;	
	$general="info";
	foreach ($data as $key => $result) {
		if ($i!=0 && $result['0']!=''){
			
			$out=updateStatus(trim($result['0']),trim($result['1']));
			
			$table.='<tr class="success"><td>'.$i.'</td><td>'.$result['0'].'</td>';
			$table.="<td>".$result['1']."</td>";
			$table.="<td>".$out['message']."</td>";
			$table.="<td>".$out['type']."</td></tr>";
			if ($out['type']=='error' && $general!='error'){
				$general='error';
				$data["message"]="Issues with importing!";
			}
		}
		$i++;
	}
	$table="<table class='table'><thead><th>ID</th><th>SITE/UPGNR</th><th>STATUS</th><th>STATE</th></thead>".$table."</table>";
	$data["type"]=$general;
	$data["table"]=$table;	
	echo json_encode($data);
}else if ($_POST['action']=="multianalyseParties"){
	//$data=str_getcsv($_POST['csvdata']);

	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdataParties']));

	if ($data[0][0]!="SITE_UPGNR" or $data[0][1]!="CODE" or $data[0][2]!="PARTNER"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect';
		echo json_encode($res);
		exit;
	}
	//We now analyse the input
	$i=0;
	
	foreach ($data as $key => $result) {

		$message="";
		$status="OK";
		if ($i!=0 && $result['0']!=''){
			
			if ($result['1']!='WIP-A' && $result['1']!='WIP-C' && $result['1']!='CON' && $result['1']!='SAC'){
				$status='NOTOK';
				$general_status='NOTOK';
				$message.="CODE has to be WIP-A or WIP-C or CON or SAC<br>";
			}

			if (substr(trim($result['0']),0,2)=='99' || $result['1']=='WIP-A' || $result['1']=='SAC'){
				$query = "select WOR_UDK AS RES from WORKS_ORDERS@".$config['net1db']." WHERE WOR_UDK='".strtoupper(trim($result['0']))."'";
			}else if ($result['1']=='WIP-C' || $result['1']=='CON'){
				$query = "select SIT_UDK AS RES from SITES@".$config['net1db']." WHERE SIT_UDK='".strtoupper(trim($result['0']))."'";
			}else{
				$status='NOTOK';
			}
			if ($status!='NOTOK'){
				//echo $query."<br>";
				$stmt = parse_exec_fetch($conn_Netone, $query, $error_str, $res1);
				if (!$stmt) {
					 die_silently($conn_Netone, $error_str);
					 exit;
				} else {
					 OCIFreeStatement($stmt);
				}
				$amount=count($res1['RES']);
			}
			if ($amount!=1){
				$status='NOTOK';
				$general_status='NOTOK';
				$message.="SITE/UPGR not found in NET1!";
				if ($result['1']=='WIP-A' || $result['1']=='SAC'){
				$message.="For SAC and WIP-A: please provide nominal and not candidate!<br>";
				}
			}

			if (substr($result['0'],0,1)!=9 && strlen(trim($result['0']))!=8 && strlen(trim($result['0']))!=7){
				$status='NOTOK';
				$general_status='NOTOK';
				$message.="SiteID not correctbr>";
			}
			
			if ($result['2']=='' or ($result['2']!='TECHM' && $result['2']!='KPNGB' && $result['2']!='BENCHMARK' && $result['2']!='ZTE')){
				$status='NOTOK';
				$general_status='NOTOK';
				$message.="Partner has to be TECHM, KPNGB, BENCHMARK or ZTE<br>";
			
			}

			if ($status=="OK"){
				$class="success";
			}else{
				$class="danger";
			}
			$out.='<tr class="'.$class.'"><td>'.$i.'</td><td>'.trim($result['0']).'</td>';
			$out.="<td>".trim($result['1'])."</td>";
			$out.="<td>".trim($result['2'])."</td>";
			$out.="<td>".trim($newdate)."</td>";
			$out.="<td>".trim($result['4'])."</td>";
			$out.="<td>".trim($message)."</td></tr>";
		}
		$i++;
	}//END FOR

	if ($i==1){
		$res["type"]='error';
		$res["message"]='No data provided';
		echo json_encode($res);
		exit;
	}

	
	$out="<table class='table' style='width:700px;'><thead><th>ID</th><th>UPGNR/SITE</th><th>CODE</th><th>PARTNER</th></thead>".$out."</table>";
	if ($general_status!='NOTOK'){
		$retval["type"]='info';
		$retval["table"]=$out;
	}else{
		$retval["type"]='error';
		$retval["message"]="You have errors in your csv data!";
		$retval["table"]=$out;
	}	
	
	echo json_encode($retval);
}else if ($_POST['action']=="multiimportParties"){
	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdataParties']));

	if ($data[0][0]!="SITE_UPGNR" or $data[0][1]!="CODE" or $data[0][2]!="PARTNER"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect!';
		echo json_encode($res);
		exit;
	}
	//We now analyse the input
	$i=0;
	//echo "<pre>".print_r($results,true)."</pre>";
	$general="info";
	foreach ($data as $key => $result) {
		if ($i!=0 && $result['0']!=''){
			if (trim($result['2'])=="TECHM"){
				$code='33370';
			}else if (trim($result['2'])=="BENCHMARK"){
				$code='31481';
			}else if (trim($result['2'])=="ZTE"){
				$code='32633';
			}else if (trim($result['2'])=="KPNGB"){
				$code='32474';
			}
			$out=updatePartie(trim($result['0']),trim($result['1']),$code);
			//echo "<pre>".print_r($out,true)."</pre><hr>";
			$table.='<tr class="success"><td>'.$i.'</td><td>'.$result['0'].'</td>';
			$table.="<td>".$result['1']."</td>";
			$table.="<td>".$result['2']."</td>";
			$table.="<td>".$out['message']."</td>";
			$table.="<td>".$out['type']."</td></tr>";
			if ($out['type']=='error' && $general!='error'){
				$general='error';
				$data["message"]="Issues with importing!";
			}
		}
		$i++;
	}
	$table="<table class='table'><thead><th>ID</th><th>SITE/UPGNR</th><th>CODE</th><th>PARTNER</th><th>STATUS</th></thead>".$table."</table>";
	$data["type"]=$general;
	$data["table"]=$table;	
	echo json_encode($data);
}

ocilogoff($conn_Infobase);

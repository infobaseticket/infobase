<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$id=explode("-",$_GET['name']);

if ($_POST['field']!="READY_BUILD" && $_POST['field']!="BCS_NET1" && $_POST['field']!="PARTNER_VALREQ" && $_POST['field']!="BP_NEEDED" && $_POST['field']!="COF_ACQ"  && $_POST['field']!="COF_CON" && $_POST['field']!="PARTNER_ACQUIRED" && $_POST['field']!="RADIO_FUND" && $_POST['field']!="SITELISTNOTPREF" && $_POST['field']!="UPGNRS" && $_POST['field']!="BSDS" && $_POST['field']!="NET1_LINK" && $_POST['field']!="ACQ_PARTNER" && $_POST['field']!="CON_PARTNER"){
	$array['OK']='OK';
	if ($_POST['field']!="TXMN_ACQUIRED"){
	$array['NOT OK']='NOT OK';
	}
}

if ($_POST['field']=="COF_ACQ"){
	$array['NOT OK']='NOT OK';
	$array['PARTNER OK']='PARTNER OK';
	
	if ((($_POST['oldval']=="PARTNER OK" or $_POST['oldval']=="NOT OK" or $_POST['oldval']=="OK") && (substr_count($guard_groups, 'Base')==1) or substr_count($guard_groups, 'Admin')==1)){
		$array['REJECTED']='REJECT';
		$array['BASE OK']='BASE OK';
	}	
}

if ($_POST['field']=="PARTNER_DESIGN"){
	if (substr_count($guard_groups, 'Admin')==1){
		$array['NOT OK']='NOT OK';
		$array['PARTNER RF OK']='PARTNER RF OK';
		$array['REJECTED']='REJECT';
		$array['REJECTED']='DESIGN MS&DOCS OK';
	}
	if (($_POST['oldval']=='PARTNER RF OK' && substr_count($guard_groups, 'Base_RF')==1) or substr_count($guard_groups, 'Admin')==1){
		$array['BASE RF OK']='BASE RF OK';
	}
	if (($_POST['oldval']=='BASE RF OK' && substr_count($guard_groups, 'Base_delivery')==1) or substr_count($guard_groups, 'Admin')==1){
		$array['BASE TS OK']='BASE TS OK';
		
	}

	if ((($_POST['oldval']=='PARTNER RF OK' && substr_count($guard_groups, 'Base_RF')==1) OR ($_POST['oldval']=='BASE RF OK' && substr_count($guard_groups, 'Base_delivery')==1))  or substr_count($guard_groups, 'Admin')==1){
		$array['REJECTED']='REJECT';
	}
}

if ($_POST['field']=="BP_NEEDED"){
	if ((($_POST['oldval']=='NOT OK' or $_POST['oldval']=='REJECTED') && substr_count($guard_groups, 'Partner')==1) or substr_count($guard_groups, 'Admin')==1){
		$array['PARTNER BP YES']='PARTNER BP YES';
		$array['PARTNER BP NO']='PARTNER BP NO';
	}
	if (($_POST['oldval']=='PARTNER BP YES' && substr_count($guard_groups, 'Base')==1) or substr_count($guard_groups, 'Admin')==1){
		$array['BASE BP YES']='BASE BP YES';
		$array['REJECTED']='REJECT';
	}
	if (($_POST['oldval']=='PARTNER BP NO' && substr_count($guard_groups, 'Base')==1) or substr_count($guard_groups, 'Admin')==1){
		$array['BASE BP NO']='BASE BP NO';
		$array['REJECTED']='REJECT';
	}
	if (substr_count($guard_groups, 'Admin')==1){
		$array['NOT OK']='NOT OK';
	}
}

if ($_POST['field']=="COF_CON"){
	$array['NOT OK']='NOT OK';
	$array['REJECTED']='REJECT';
	$array['PARTNER OK']='PARTNER OK';

	$query = "select RAFID FROM BSDS_RAF_COF WHERE RAFID='".$_POST["rafid"]."' AND MATERIAL_CODE LIKE '%BOQ%'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	//echo count($res1['RAFID']);
	if (count($res1['RAFID'])!=0 && $_POST['oldval']=="PARTNER OK"){
		$array['BASE TS OK']='BASE TS OK';
	}else if(substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'Admin')==1){
		$array['BASE OK']='BASE PM OK';
	}
}

if ($_POST['field']=="READY_BUILD"){
	
	$query = "select BL.SITEID AS SITEID FROM BSDS_RAFV2 RA LEFT JOIN BLACKLIST BL ON RA.SITEID=SUBSTR(BL.SITEID,0,6) WHERE RAFID  ='".$_POST["rafid"]."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	if (count($res1['SITEID'])==0){
		$array['BASE PM OK']='BASE PM OK';
	}else{
		$array['BLACKLISTED']='BLACKLISTED';
	}

	if (substr_count($guard_groups, 'Admin')==1){
		$array['NOT OK']='NOT OK';
	}

}

$posDISM= strrpos($_POST["raftype"], "DISM Upgrade");

if ($_POST['field']=="PARTNER_ACQUIRED" or (($_POST['field']=="PARTNER_RFPAC" or $_POST['field']=="PARTNER_RFPAC2") && $posDISM!==false)){
	$array['AWAITING NET1']='OK, TOGGLE NET1';
}

if ($_POST['field']=="PARTNER_VALREQ" && $_POST['oldval']=="READY FOR FAC"){
	$array['FAC CONFIRMED']='FAC CONFIRMED';
}

if ($_POST['field']=="PARTNER_VALREQ" && $_POST['oldval']=="REJECTED"){
	$array['OK']='OK';
}


if ($_POST['field']=="BCS_TX_INP" || $_POST['field']=="BCS_RF_INP" || $_POST['field']=="NET1_LINK" || $_POST['field']=="RF_PAC" || $_POST['field']=="OTHER_INP" || $_POST['field']=="ALU_ACQUIRED"|| $_POST['field']=="TXMN_ACQUIRED" || $_POST['field']=="NET1_ACQUIRED" || $_POST['field']=="NET1_PAC" || $_POST['field']=="NET1_FAC"){
$array['REJECTED']='REJECT';
}

if ($_POST['field']=="RADIO_FUND"){
	if ($_POST['raftype']!='CTX Upgrade' && $_POST['raftype']!='CWK Upgrade'){
	$array['G9']='G9';
	$array['G18']='G18';
	$array['U9']='U9';
	$array['U21']='U21';
	$array['L8']='L8';
	$array['L18']='L18';
	$array['L26']='L26';
	$array['ON HOLD']='ON HOLD';
	//$array['NOT OK']='NOT OK';
	}
	if ($_POST['raftype']=='ANT Upgrade'){
	$array['EXISTING']='EXISTING TECHNOS';
	}
	if ($_POST['raftype']=='CTX Upgrade'){
	$array['CTX']='CTX';
	}
	if ($_POST['raftype']=='CWK Upgrade'){
	$array['CWK']='CWK';
	}
	if ($_POST['raftype']=='ASC Upgrade'){
	$array['ASC']='ASC';
	}
	if ($_POST['raftype']=='DISM Upgrade'){
	$array['DISM']='DISM';
	}
	if ($_POST['raftype']=='CAB Upgrade'){
	$array['CAB']='CAB';
	}
	if (substr_count($guard_groups, 'Admin')==1){
		$array['NOT OK']='NOT OK';
	}

	if (substr_count($guard_groups, 'Admin')==1 && $_POST['field']!="NET1_LINK"){
		$array['END']='END';
	}
}
if ($_POST['field']=="TXMN_ACQUIRED"){
	$array['COND OK']='COND OK';
}
if (substr_count($guard_groups, 'Admin')==1 &&  $_POST['field']!="READY_BUILD" && $_POST['field']!="RADIO_FUND"  && $_POST['field']!="PARTNER_VALREQ" && $_POST['field']!="COF_CON" && $_POST['field']!="COF_ACQ" && $_POST['field']!="PARTNER_RFPAC" && $_POST['field']!="NET1_LINK" && $_POST['field']!="UPGNRS" && $_POST['field']!="BSDS" && $_POST['field']!="SITELISTNOTPREF" && $_POST['field']!="SITELISTNOTPREF"){
	$array['NA']='NA';
}
if ($_POST['field']=="ACQ_PARTNER" || $_POST['field']=="CON_PARTNER"){
		$array['TECHM']='TECHM';
		$array['ZTE']='ZTE';
		$array['ALU']='ALU';
		$array['BENCHMARK']='BENCHMARK';
		$array['BASE']='BASE';
		$array['M4C']='M4C';
		$array['OTHER']='OTHER';
		$array['NOT OK']='NOT OK';
}

if ($_POST['field']=="BSDS"){
	$query="select BSDSKEY,BSDS_TYPE FROM BSDS_GENERALINFO2 WHERE SITEID LIKE '%".$_POST["siteid"]."%' AND DELETEDSTATUS!='yes'";
	if (substr_count($guard_groups, 'Admin')!=1){
		$query.=" AND RAFID IS NULL";
	}
	$query.="  ORDER BY BSDSKEY DESC";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	foreach ($res1['BSDSKEY'] as $key=>$attrib_id) {
	    $array[$res1['BSDSKEY'][$key]]=$res1['BSDSKEY'][$key]." (".$res1['BSDS_TYPE'][$key].")";
	}

}


if ($_POST['field']=="SITELISTNOTPREF"){

	$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
	$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
	OCIExecute($stmt,OCI_DEFAULT);

	$query = "select DISTINCT WOR_UDK,SIT_UDK,WOE_RANK,WOR_DOM_WOS_CODE from VW_NET1_ALL_NEWBUILDS WHERE upper(WOR_UDK) LIKE '%".strtoupper($_POST["siteid"])."%' ORDER BY WOE_RANK,WOR_UDK";
	
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}

	if (count($res1['WOR_UDK'])!=0){
		foreach ($res1['WOR_UDK'] as $key=>$attrib_id) {
		    $array[$key]['id']=$res1['WOR_UDK'][$key].":".$res1['SIT_UDK'][$key];
		    $array[$key]['text']=$res1['WOR_UDK'][$key]." : ".$res1['SIT_UDK'][$key]." (".$res1['WOE_RANK'][$key]." ".$res1['WOR_DOM_WOS_CODE'][$key].")";
		}
	}else{
		$array[0]['id']=0;
		$array[0]['text']="No results found...";
	}
}else if ($_POST["field"]=="UPGNRS"){

	$query = "select DISTINCT WOR_UDK, WOR_LKP_WCO_CODE, WOR_NAME, WOR_DOM_WOS_CODE from  VW_NET1_ALL_UPGRADES 
	WHERE upper(WOR_UDK) LIKE '%".strtoupper($_POST["upgnr"])."%'";
	if ($_POST["siteidcand"]!=""){
		$epl=explode(":",$_POST['siteidcand']);
		$candidate=trim($epl[1]);
	$query .= " AND SIT_UDK='".$candidate."'";
	}
	if ($_POST["status"]!=""){
	$query .= " AND WOR_DOM_WOS_CODE='".$_POST["status"]."'";
	}
	$query .= " ORDER BY WOR_UDK ASC";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	if (count($res1['WOR_UDK'])!=0){
		foreach ($res1['WOR_UDK'] as $key=>$attrib_id) {
		    $array[$key]['id']=$res1['WOR_UDK'][$key];
		    $array[$key]['text']=$res1['WOR_UDK'][$key]. " : ".$res1['WOR_LKP_WCO_CODE'][$key]." (".$res1['WOR_DOM_WOS_CODE'][$key].")";
		    $array[$key]['description']=$res1['WOR_UDK'][$key]. " (".$res1['WOR_LKP_WCO_CODE'][$key].": ".$res1['WOR_NAME'][$key].")";
		}
	}else{
		$array[0]['id']=0;
		$array[0]['text']="No results found...";
	}
}
if ($_POST['field']=="NET1_LINK"){

	$array['END']='END';

	$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
	$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
	OCIExecute($stmt,OCI_DEFAULT);

	$pos = strrpos(strtoupper($_POST["raftype"]), "UPGRADE");

	if ($_POST["raftype"]=="New Temp Replacement"){
		$query="select WOR_UDK from WORKS_ORDERS@net1prd WHERE WOR_UDK LIKE '%".$_POST["siteid"]."%' AND WOR_LKP_WAR_CODE='ROL'AND WOR_DOM_WOS_CODE='IS'";
		//echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
		  die_silently($conn_Infobase, $error_str);
		  exit;
		} else {
		  OCIFreeStatement($stmt);
		}
		foreach ($res1['WOR_UDK'] as $key=>$attrib_id) {
		    $array[$res1['WOR_UDK'][$key]]=$res1['WOR_UDK'][$key];
		}

	}else if ($pos === false) { //NEWBUILD
		/*
		$query="select N1_SITEID, N1_CANDIDATE from MASTER_REPORT 
		 WHERE N1_SITEID LIKE '%".strtoupper($_POST["siteid"])."%' AND (N1_NBUP='NB' OR N1_NBUP='NB REPL') AND N1_STATUS='IS'";
		//echo $query."<br>"; */
		
		$query="select * from SITES@".$config['net1db']." a LEFT JOIN WORKS_ORDER_ELEMENTS@".$config['net1db']." b ON a.SIT_ID=b.WOE_SIT_ID 
		where sit_udk like  '%".strtoupper($_POST["siteid"])."%' and WOE_RANK=1";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
		  die_silently($conn_Infobase, $error_str);
		  exit;
		} else {
		  OCIFreeStatement($stmt);
		}
		foreach ($res1['SIT_UDK'] as $key=>$attrib_id) {
			$query3="SELECT * FROM BSDS_RAFV2 WHERE NET1_LINK='".$res1['SIT_UDK'][$key]."'";
			//echo $query3;
			$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
		   	if (!$stmt3) {
		      die_silently($conn_Infobase, $error_str);
		      exit;
		   	} else {
		      OCIFreeStatement($stmt3);
		   	}
		   	if (count($res3['NET1_LINK'])==0){
			    $array[$res1['SIT_UDK'][$key]]=$res1['SIT_UDK'][$key];
			}
		}
		
	}else{ //UPGRADES
		//We take the data immeditaely from NET1 so we don't need to wait for refresh
		$query = "SELECT
		  WORKS_ORDERS.WOR_UDK as WOR_UDK,
		  SITES.SIT_UDK as SITE_ID,
		  WORKS_ORDERS.WOR_LKP_WCO_CODE as BAND,
		  WORKS_ORDERS.WOR_NAME AS WOR_NAME
		FROM
		  SITES@".$config['net1db'].",
		  LOOKUP_DETAILS@".$config['net1db']."  SITE_TYPE,
		  WORKS_ORDER_ELEMENTS@".$config['net1db'].",
		  WORKS_ORDERS@".$config['net1db'].",
		  PARTIES@".$config['net1db']."  UPG_PARTIES,
		  TRANSACTION_PARTIES@".$config['net1db']."  UPG_TRANSACTION_PARTIES
		WHERE
		  ( WORKS_ORDER_ELEMENTS.WOE_WOR_ID=WORKS_ORDERS.WOR_ID  )
		  AND  ( SITES.SIT_ID=WORKS_ORDER_ELEMENTS.WOE_SIT_ID  )
		  AND  ( WORKS_ORDERS.WOR_LKP_WAR_CODE='NEM'  )
		  AND  ( SITE_TYPE.LDE_DOM_LOH_CODE='STY' OR SITE_TYPE.LDE_DOM_LOH_CODE IS NULL  )
		  AND  ( SITES.SIT_LKP_STY_CODE=SITE_TYPE.LDE_CODE  )
		  AND  ( WORKS_ORDERS.WOR_ID=UPG_TRANSACTION_PARTIES.TXP_PRIMARY_KEY  )
		  AND  ( UPG_TRANSACTION_PARTIES.TXP_PTY_ID=UPG_PARTIES.PTY_ID  )
		  AND  ( UPG_TRANSACTION_PARTIES.TXP_ATB_TABLE='WORKS_ORDERS' OR UPG_TRANSACTION_PARTIES.TXP_ATB_TABLE IS NULL  )
		  AND  (
		  WORKS_ORDERS.WOR_LKP_WCO_CODE  IN  ('ANT', 'ASC', 'CAB', 'DCS', 'EG6', 'EGS', 'UMTS', 'UMT6', 'HSDPA','MOD', 'HSPX','SWAP','IND','ANT','MSH','LTE','LTEX','CTX','RPT','CWK','MOV','DISM')
		  AND  WORKS_ORDERS.WOR_DOM_WOS_CODE  IN  ('IS')
		  )
		AND SITES.SIT_UDK LIKE '%".strtoupper($_POST["siteid"])."%'
		GROUP BY
		  WORKS_ORDERS.WOR_UDK ,
		  SITES.SIT_UDK ,
		  WORKS_ORDERS.WOR_LKP_WCO_CODE ,
		  WORKS_ORDERS.WOR_NAME";
		/*  
		$query = "select N1_SITEID, N1_CANDIDATE,N1_UPGNR,N1_SITETYPE from MASTER_REPORT 
		 WHERE N1_UPGNR NOT IN( SELECT NET1_LINK FROM BSDS_RAFV2  WHERE NET1_LINK LIKE '99%' AND SITEID LIKE '%".strtoupper($_POST["siteid"])."%')
			AND N1_SITEID LIKE '%".strtoupper($_POST["siteid"])."%' AND N1_NBUP='UPG' AND N1_STATUS='IS'";
		*/
		//echo "<br><br>$query";
	   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
	   	if (!$stmt) {
	      die_silently($conn_Infobase, $error_str);
	      exit;
	   	} else {
	      OCIFreeStatement($stmt);
	   	}
		foreach ($res2['WOR_UDK'] as $key=>$attrib_id) {
			$query3="SELECT * FROM BSDS_RAFV2 WHERE NET1_LINK='".$res2['WOR_UDK'][$key]."'";
			//echo $query3;
			$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
		   	if (!$stmt3) {
		      die_silently($conn_Infobase, $error_str);
		      exit;
		   	} else {
		      OCIFreeStatement($stmt3);
		   	}
		   	if (count($res3['NET1_LINK'])==0){
			    $array[$res2['WOR_UDK'][$key]]=$res2['WOR_UDK'][$key]." (".$res2['BAND'][$key].")";
			}
		}	
	}
}
print json_encode($array);
?>
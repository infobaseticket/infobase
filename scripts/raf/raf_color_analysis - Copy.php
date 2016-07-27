<?php

$OTHER_INP_class="";
$RADIO_INP_class="";
$TXMN_INP_class="";
$ACQ_PARTNER_class="";
$NET1_LINK_class="";
$PO_ACQ_class="";
$PARTNER_INP_class="";
$BCS_TXRF_class="";
$BCS_NET1_class="";
$TXMN_LOS_class="";
$NET1_LBP_class="";
$PARTNER_ACQUIRED_class="";
$TXMN_ACQUIRED_class="";
$NET1_ACQUIRED_class="";
$RADIO_FUND_class="";
$CON_PARTNER_class="";
$NET1_FUND_class="";
$PO_CON_class="";
$PARTNER_PAC_class="";
$RF_PAC_class="";
$POCON_DELIVERY_class="";
$POCON_PARTNER_class="";
$NET1_PAC_class="";
$NET1_FAC_class="";
$status_special3="";


if ($res1['OTHER_INP'][$i]=="NOT OK" && $res1['TYPE'][$i]=="New Replacement"){
	$status="BASE Others or RF (RAF)";
	$OTHER_INP_class="selected_RAF";
}elseif ($res1['OTHER_INP'][$i]=="NOT OK" && ($res1['TYPE'][$i]=="New Move" || $res1['TYPE'][$i]=="CWK Upgrade" || $res1['TYPE'][$i]=="Dismantling")){
	$status="BASE Others (RAF)";
	$OTHER_INP_class="selected_RAF";
}elseif ($res1['OTHER_INP'][$i]=="REJECTED"){
	$status="BASE Others (RAF)";
	$OTHER_INP_class="selected_RAF";
}elseif ($res1['RADIO_INP'][$i]=="NOT OK" || $res1['RADIO_INP'][$i]=="REJECTED"){
	if ($raf_type=="indoor"){
		$RADIO_INP_class="selected_RAF";
		$status="BASE RF (RAF 1->4)";
	}else if ($raf_type=="outdoor"){
		$status="BASE RF (RAF 1->7)";
		$RADIO_INP_class="selected_RAF";
	}
}else if ($res1['TXMN_INP'][$i]=="NOT OK"){
	if ($raf_type=="indoor"){
		$status="TXMN (RAF 6)";
		$TXMN_INP_class="selected_RAF";
	}else if ($raf_type=="outdoor"){
		$status="TXMN (RAF 1->6)";
		$TXMN_INP_class="selected_RAF";
	}
}else if ($res1['ACQ_PARTNER'][$i]=="NOT OK"){
        $status="DELIVERY";
        $ACQ_PARTNER_class="selected_RAF";
}else if ($res1['NET1_LINK'][$i]=="OK"){
	$status="(INFORMATIONAL)";
	$NET1_LINK_class="selected_RAF";
}else if (trim($res1['NET1_LINK'][$i])=="NOT OK" || trim($res1['NET1_LINK'][$i])==""  || trim($res1['NET1_LINK'][$i])=="BCS CHANGE"
|| ($res1['NET1_LINK'][$i]=="REJECTED" && $res1['RADIO_INP'][$i]=="OK" && $res1['TXMN_INP'][$i]=="OK")
|| ($res1['NET1_LINK'][$i]=="REJECTED" && $res1['RADIO_INP'][$i]=="NA" && $res1['TXMN_INP'][$i]=="OK")){
	$status="BASE Delivery (RAF+NET1)";
	$NET1_LINK_class="selected_RAF";
}else if ($res1['NET1_LINK'][$i]=="END"){
    $status="END OF PROCESS";
    $NET1_LINK_class="selected_RAF";
}else if ($res1['POPR_ACQ'][$i]=='' && $res1['SAC'][$i]!='BASE' && $res1['SAC'][$i]!='KPNGB' && $res1['TYPE'][$i]!='DISM Upgrade' && $res1['NET1_PAC'][$i]=="NOT OK"){
	$status="Base Delivery<br>PO Acquisition";
	$PO_ACQ_class="selected_RAF";
}else if ($res1['PARTNER_INP'][$i]=="NOT OK" || $res1['PARTNER_INP'][$i]=="REJECTED"){;
	$status="PARTNER (RAF 1->4)";
	$PARTNER_INP_class="selected_RAF";
}else if ($res1['BCS_NET1'][$i]=="NOT OK" && $res1['BCS_RF_INP'][$i]=='NOT OK'  && $res1['PARTNER_INP'][$i]=="OK" && $res1['POPR_ACQ'][$i]!=''){
    if ($raf_type=="indoor"){
        $status="BASE RF (RAF 9)";
    }else if ($raf_type=="outdoor"){
        $status="BASE RF (RAF 8->9)";
    }
    $BCS_TXRF_class="selected_RAF";
}else if ($res1['BCS_NET1'][$i]=="NOT OK" &&  $res1['BCS_TX_INP'][$i]=='NOT OK' && $res1['PARTNER_INP'][$i]=="OK" && $res1['POPR_ACQ'][$i]!=''){
    $status="BASE TX (RAF)";
    $BCS_TXRF_class="selected_RAF";
}else if (($res1['BCS_NET1'][$i]=="NOT OK" or $res1['BCS_NET1'][$i]=="AWAIT SYNC" or $res1['BCS_NET1'][$i]=="NET1LINK" or $res1['BCS_NET1'][$i]=="A304") && $res1['NET1_PAC'][$i]=="NOT OK"){
	$status="BASE RF (NET1)";
	$BCS_NET1_class="selected_RAF";
}else if ($res1['BCS_NET1'][$i]=="STOPPED"){
	$status="STOPPED";
	$BCS_NET1_class="selected_RAF";
}else if ($res1['NET1_LBP'][$i]=="NOT OK"){
	$status="PARTNER (NET1)";
	$NET1_LBP_class="selected_RAF";
}else if ($res1['PARTNER_ACQUIRED'][$i]=="NOT OK" || $res1['PARTNER_ACQUIRED'][$i]=="REJECTED"){ //&& ($res1['SAC'][$i]=="ALU" or $res1['SAC'][$i]=="BENCHMARK" )
	$query = "Select FINAL_RF,FINAL_MICROWAVE,FINAL_CAB,FINAL_BTS,FINAL_OTHER FROM BSDS_RAF_PARTNER WHERE RAFID = '".$res1['RAFID'][$i]."'";
	//echo $query."<br>";
	$stmt6 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
	if (!$stmt6) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt6);
	}

	if ($res6['FINAL_RF'][0]=='Yes' && $res6['FINAL_MICROWAVE'][0]=='Yes' && $res6['FINAL_CAB'][0]=='Yes' && $res6['FINAL_BTS'][0]=='Yes' && $res6['FINAL_OTHER'][0]=='Yes'){
			$status_special="";
	}else{
		$status_special="Final compliancy needed!";
	}
    if (substr_count($guard_groups, 'Base_RF')==1 && $res1['RADIO_FUND'][$i]=="NOT OK" && $res1['TXMN_ACQUIRED'][$i]!="NOT OK"){
        $status="BASE RF (RAF 10->11)";
        $RADIO_FUND_class="selected_RAF";
    }else{
        $status="PARTNER (RAF 5->6)";
        $PARTNER_ACQUIRED_class="selected_RAF";
    }
	
	
}else if ($res1['PARTNER_ACQUIRED'][$i]!="NOT OK" && $res1['PARTNER_ACQUIRED'][$i]!="REJECTED" && $res1['TXMN_ACQUIRED'][$i]=="NOT OK"){
	$status="TXMN";
	$TXMN_ACQUIRED_class="selected_RAF";
}else if ($res1['RADIO_FUND'][$i]=="END"){
	$status="RAF AS BUILD";
}else if ($res1['RADIO_FUND'][$i]=="NOT OK" || $res1['RADIO_FUND'][$i]=="" || $res1['RADIO_FUND'][$i]=="REJECTED" || $res1['RADIO_FUND'][$i]=="NOT FUNDED" || $res1['RADIO_FUND'][$i]=="NO BUDGET" || $res1['RADIO_FUND'][$i]=="ON HOLD"){
	$status="BASE RF (RAF 10->11)";
	$RADIO_FUND_class="selected_RAF";
}else if ($res1['CON_PARTNER'][$i]=="NOT OK"){
        $status="DELIVERY";
        $CON_PARTNER_class="selected_RAF";
}else if ($res1['NET1_FUND'][$i]=="NOT OK" || $res1['NET1_FUND'][$i]=="REJECTED"){
	$status="BASE Delivery (NET1)";
	$NET1_FUND_class="selected_RAF";
}else if ($res1['POPR_CON'][$i]=='' && $res1['TYPE'][$i]!='Dismantling' && $res1['NET1_PAC'][$i]=="NOT OK"){
    $status="Base Delivery<br>PO Construction";
    $PO_CON_class="selected_RAF";
}else if (($res1['PARTNER_PAC'][$i]=="NOT OK" || $res1['PARTNER_PAC'][$i]=="REJECTED") && $res1['NET1_PAC'][$i]=="NOT OK"){
    $query = "Select COMPLIANT,CNT_TCH,CNT_BLOCKING,CNT_DROPPED,CNT_SDCCH,CNT_UPQUAL,CNT_DQUAL,CNT_SLEEPING,CNT_AVAILABILITY,CNT_SDCCHDROP FROM BSDS_RAF_PARTNER WHERE RAFID = '".$res1['RAFID'][$i]."'";
    //echo $query."<br>";
    $stmt6 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
    if (!$stmt6) {
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmt6);
    }

    if ($res6['COMPLIANT'][0]=='Yes' or ($res6['CNT_TCH'][0]=='Yes' && $res6['CNT_BLOCKING'][0]=='Yes' && $res6['CNT_DROPPED'][0]=='Yes' && $res6['CNT_SDCCH'][0]=='Yes' && $res6['CNT_UPQUAL'][0]=='Yes' && $res6['CNT_DQUAL'][0]=='Yes' && $res6['CNT_SLEEPING'][0]=='Yes' && $res6['CNT_AVAILABILITY'][0]=='Yes' && $res6['CNT_SDCCHDROP'][0]=='Yes')){
            $status_special2="";
    }else{
        $status_special2="Site acceptance needed!";
    }
	$status="PARTNER (RAF 7)";
	$PARTNER_PAC_class="selected_RAF";
}else if (($res1['RF_PAC'][$i]=="NOT OK" || $res1['RF_PAC'][$i]=="OK" || $res1['RF_PAC'][$i]=="AWAITING SYNC" || $res1['RF_PAC'][$i]=="REJECTED" || $res1['RF_PAC'][$i]=="09-SEP-1990" || $res1['RF_PAC'][$i]=="REJECTED") && $res1['NET1_PAC'][$i]=="NOT OK"){
	$status="BASE RF (RAF 12+NET1)";
	$RF_PAC_class="selected_RAF";

}else if (($res1['POCON_PARTNER'][$i]=="NOT OK") && $res1['NET1_PAC'][$i]=="NOT OK"){
    $status="PARTNER (RAF 8)";
    $POCON_PARTNER_class="selected_RAF";
}else if (($res1['POCON_DELIVERY'][$i]=="NOT OK" || $res1['POCON_DELIVERY'][$i]=="REJECTED"  or $res1['POCON_PARTNER'][$i]=="NOT AGREED") && $res1['NET1_PAC'][$i]=="NOT OK"){
    
    if ($res1['POCON_DELIVERY'][$i]=="REJECTED"){
        $status="BASE DELIVERY (SAP)";
    }else{
        $status="BASE DELIVERY (RAF 1)";
    }
    
    $POCON_DELIVERY_class="selected_RAF";
}else if ($res1['NET1_PAC'][$i]=="READY FOR PAC"){
    $status="BASE DELIVERY (RAF 2)";
    $NET1_PAC_class="selected_RAF";
}else if ($res1['NET1_PAC'][$i]=="MISSING DOCUMENTS"){
    $status="PARTNER (ADD DOCS)";
}else if ($res1['NET1_PAC'][$i]=="OK" or $res1['NET1_PAC'][$i]=="AWAITING SYNC"){
    $status="BASE DELIVERY (NET1)";
    $NET1_PAC_class="selected_RAF";
}else if ($res1['NET1_PAC'][$i]=="NOT OK"){
    $status="BASE DELIVERY (NET1)";
    $NET1_PAC_class="selected_RAF";
}else if ($res1['NET1_FAC'][$i]=="NOT OK" or $res1['NET1_FAC'][$i]=="AWAITING SYNC"){
    $status="BASE DELIVERY (NET1)";
    $NET1_FAC_class="selected_RAF";
}else{
	$status="RAF ASBUILD";
}


//Editables
if (((substr_count($guard_groups, 'Base_other')==1  || substr_count($guard_groups, 'Base_delivery')==1) && $res1['LOCKEDD'][$i]!="yes" && $res1['RADIO_INP'][$i]!="OK") || substr_count($guard_groups, 'Administrators')==1){
    $OTHER_INP_select="editableSelectItem";
}else{
    $OTHER_INP_select="";
}
if (((substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Base_delivery')==1) && $res1['LOCKEDD'][$i]!="yes") || substr_count($guard_groups, 'Administrators')==1){ //&& $res1['TXMN_INP'][$i]!="OK"
    $RADIO_INP_select="editableSelectItem";
}else{
    $RADIO_INP_select="";
}
if (((substr_count($guard_groups, 'Base_TXMN')==1 || substr_count($guard_groups, 'Base_delivery')==1) && $res1['LOCKEDD'][$i]!="yes" && ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA")) || substr_count($guard_groups, 'Administrators')==1){
    $TXMN_INP_select="editableSelectItem";
}else{
    $TXMN_INP_select="";
}

if (substr_count($guard_groups, 'Base_delivery')==1  || substr_count($guard_groups, 'Administrators')==1){
    $ACQ_PARTNER_select="editableSelectItem";
    $CON_PARTNER_select="editableSelectItem";
    $NET1_LINK_select="editableSelectItem";
}else{
    $NET1_LINK_select="";
    $ACQ_PARTNER_select="";
    $CON_PARTNER_select="";
}

if ((substr_count($guard_groups, 'Partner')==1 && ($res1['RADIO_INP'][$i]=="OK" ||  $res1['RADIO_INP'][$i]=="NA")  && ($res1['TXMN_INP'][$i]=="OK" or $res1['TXMN_INP'][$i]=="NA")  && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="") && ($res1['PARTNER_INP'][$i]=="NOT OK" OR $res1['PARTNER_INP'][$i]=="REJECTED")) || substr_count($guard_groups, 'Administrators')==1){
    $PARTNER_INP_select="editableSelectItem";
}else{
    $PARTNER_INP_select="";
}
if ((substr_count($guard_groups, 'Base_RF')==1 && $res1['RADIO_INP'][$i]=="OK" && ($res1['TXMN_INP'][$i]=="OK" or $res1['TXMN_INP'][$i]=="NA") && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['PARTNER_INPUT'][$i]!="NOT OK"  && ($res1['BCS_NET1'][$i]=="NOT OK" || $res1['BCS_NET1'][$i]=="A304")) || substr_count($guard_groups, 'Administrators')==1){
    $BCS_RF_select="editableSelectItem";
}else{
    $BCS_RF_select="";
}

if ((substr_count($guard_groups, 'Base_TXMN')==1 && $res1['RADIO_INP'][$i]=="OK"  && ($res1['TXMN_INP'][$i]=="OK" or $res1['TXMN_INP'][$i]=="NA")   && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['PARTNER_INPUT'][$i]!="NOT OK"  && ($res1['BCS_NET1'][$i]=="NOT OK" || $res1['BCS_NET1'][$i]=="A304")) || substr_count($guard_groups, 'Administrators')==1){
    $BCS_TX_select="editableSelectItem";
}else{
    $BCS_TX_select="";
}

/*
if (((substr_count($guard_groups, 'Base_RF')==1 && $res1['RADIO_INP'][$i]=="OK"  && ($res1['TXMN_INP'][$i]=="OK" or $res1['TXMN_INP'][$i]=="NA")   && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['PARTNER_INPUT'][$i]!="NOT OK" && $res1['BCS_RF_INP'][$i]=='OK') || (substr_count($guard_groups, 'Administrators')==1 && $res1['BCS_RF_INP'][$i]=='OK'))){
    $BCS_select="editableSelectItem";
}else{
    $BCS_select="";
}
*/
if (((substr_count($guard_groups, 'Partner')==1 && ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA")  && ($res1['TXMN_INP'][$i]=="OK" or $res1['TXMN_INP'][$i]=="NA")  && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['PARTNER_INPUT'][$i]!="NOT OK" && $res1['NET1_LBP'][$i]!="NOT OK" && $status_special=="" && ($res1['PARTNER_ACQUIRED'][$i]=="NOT OK" or $res1['PARTNER_ACQUIRED'][$i]=="REJECTED")) || substr_count($guard_groups, 'Administrators')==1)&& $res1['LOCKEDD'][$i]!="yes"){
    $partneracq_select="editableSelectItem";
}else{
    $partneracq_select="";
}

if (((substr_count($guard_groups, 'Base_TXMN')==1 && ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA")  && ($res1['TXMN_INP'][$i]=="OK" or $res1['TXMN_INP'][$i]=="NA")   && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['PARTNER_INPUT'][$i]!="NOT OK" && $res1['NET1_LBP'][$i]!="NOT OK" && $res1['PARTNER_ACQUIRED'][$i]!='NOT OK') || substr_count($guard_groups, 'Administrators')==1)&& $res1['LOCKEDD'][$i]!="yes"){
    $txmnacq_select="editableSelectItem";
}else{
    $txmnacq_select="";
}

if (((substr_count($guard_groups, 'Partner')==1 && ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA")  && ($res1['TXMN_INP'][$i]=="OK" or $res1['TXMN_INP'][$i]=="NA")  && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['PARTNER_INPUT'][$i]!="NOT OK" && $res1['NET1_LBP'][$i]!="NOT OK" && $res1['NET1_ACQUIRED'][$i]!="NOT OK" && $res1['NET1_ACQUIRED'][$i]!="REJECTED") || substr_count($guard_groups, 'Administrators')==1)&& $res1['LOCKEDD'][$i]!="yes"){
    $net1acq_select="editableSelectItem";
}else{
    $net1acq_select="";
}


if (((($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA") 
&& ($res1['TXMN_INP'][$i]=="OK" || $res1['TXMN_INP'][$i]=="NA")
&& ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")
&& $res1['NET1_LBP'][$i]!="NOT OK" && (($res1['PARTNER_ACQUIRED'][$i]!="NOT OK" && $res1['SAC'][$i]=="ALU")
|| ($res1['SAC'][$i]!="ALU")) && $res1['CON_PARTNER'][$i]=="NOT OK") || $res1['RADIO_FUND'][$i]=="NOT OK" || $res1['RADIO_FUND'][$i]==""
|| substr_count($guard_groups, 'Administrators')==1)&& $res1['LOCKEDD'][$i]!="yes"){

    if (substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1
    || substr_count($guard_groups, 'Base_delivery')==1 ||
    (substr_count($guard_groups, 'Base_TXMN')==1 && $res1['TYPE'][$i]=="CTX Upgrade")){
        $FUNDSTATUS_select="editableSelectItem";
    }else{
        $FUNDSTATUS_select="";
    }
}else{
    $FUNDSTATUS_select="";
}
if (((($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA" ) 
&& ($res1['TXMN_INP'][$i]=="OK" || $res1['TXMN_INP'][$i]=="NA")
&& ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")
&& $res1['NET1_LBP'][$i]!="NOT OK" && $res1['RADIO_FUND'][$i]!="NOT OK" & $res1['NET1_FUND'][$i]=="NOT OK"
&&  substr_count($guard_groups, 'Base_delivery')==1) || substr_count($guard_groups, 'Administrators')==1)&& $res1['LOCKEDD'][$i]!="yes"){
    $NET1_FUND_select="editableSelectItem";
}else{
    $NET1_FUND_select="";
}

if (((($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA" || $res1['RADIO_INP'][$i]=="N/A")
&& ($res1['TXMN_INP'][$i]=="OK" || $res1['TXMN_INP'][$i]=="NA" || $res1['TXMN_INP'][$i]=="N/A")
&& ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")
&& $res1['NET1_LBP'][$i]!="NOT OK"
&& $res1['RADIO_FUND'][$i]!="NOT OK"
&& substr_count($guard_groups, 'Partner')==1 && $status_special2=="")
|| substr_count($guard_groups, 'Administrators')==1) && $res1['LOCKEDD'][$i]!="yes"){
    $PAC_select="editableSelectItem";
}else{
    $PAC_select="";
}
if (((($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA" ) && ($res1['TXMN_INP'][$i]=="OK" || $res1['TXMN_INP'][$i]=="NA")
&& ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")
&& $res1['NET1_LBP'][$i]!="NOT OK"
&& $res1['RADIO_FUND'][$i]!="NOT OK" && $res1['NET1_FUND'][$i]!="NOT OK"
&& $res1['PARTNER_PAC'][$i]!="NOT OK"
&& substr_count($guard_groups, 'Base_RF')==1) || substr_count($guard_groups, 'Administrators')==1)&& $res1['LOCKEDD'][$i]!="yes"){
    $RFPAC_select="editableSelectItem";
}else{
    $RFPAC_select="";
}

if (substr_count($guard_groups, 'Administrators')==1){
    $NET1_PAC_select="editableSelectItem";
    $NET1_FAC_select="editableSelectItem";
}else{
    $NET1_PAC_select="";
    $NET1_FAC_select="";
}


if ($res1['BUFFER'][$i]==1 && $res1['DELETED'][$i]!="yes" && $status!="RAF ASBUILD"){
    $status_special3='Acquisition skipped';
    $OTHER_INP_class="buffer";
    $RADIO_INP_class="buffer";
    $TXMN_INP_class="buffer";
    $ACQ_PARTNER_class="buffer";
    $NET1_LINK_class="buffer buffer2";
    $PARTNER_INP_class="buffer";
    $BCS_TXRF_class="buffer";
    $BCS_NET1_class="buffer";
    $TXMN_LOS_class="buffer";
    $PO_ACQ_class="buffer";
    $NET1_LBP_class="buffer buffer2";
    $PARTNER_ACQUIRED_class="buffer";
    $TXMN_ACQUIRED_class="buffer";
    $NET1_ACQUIRED_class="buffer";
}


if(($res1['STATUS_CHANGE'][$i]=='OHIS' or $res1['STATUS_CHANGE'][$i]=='DLIS') && ($res1['DELETED'][$i]=='yes' or $res1['LOCKEDD'][$i]=='yes')){
    $status_special='<a rel="tooltip" title="" class="tip" data-original-title="NET1 status change ('.$res1['STATUS_CHANGE'][$i].') => contact SDM">NET1 STATUS CHANGE!</a>';
}

?>
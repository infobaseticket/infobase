<?php
require_once("/var/www/html/include/config.php");

function query_acqui_upg($reporttype,$techno,$split,$year,$phases){
	
	if ($reporttype=="TOTAL"){
		$query = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES ";
	}else if ($reporttype=="DETAILS"){
		$query = "select * from VW_NET1_ALL_UPGRADES ";
	}
	
	$query .= "WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
	AND trim(U001) IS NOT NULL
	AND trim(U353) IS NULL 
	AND trim(U380) IS NULL 
	AND trim(U381) IS NULL 
	AND ((trim(U405) IS NULL and trim(U709) is not null) or (trim(U405) IS  NULL and trim(U709) is  null) OR (trim(U405) IS not NULL and trim(U709) is null))";
	if ($phases=="Phase 1"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}else if ($phases=="Phase 2"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$phases."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}	
	$query.=" AND trim(U001) LIKE '%".$year."%' 
	AND ((trim(U405) IS NULL and trim(U709) LIKE '%".$year."%') OR (trim(U405) LIKE '%".$year."%' and trim(U709) is null))
	AND trim(WOR_LKP_WCO_CODE) IN (".$techno."))";
	
	if ($split=="WIP"){
		$query .= " AND trim(SAC) IS NOT NULL AND trim(SAC)!='ALU' and  trim(WIP)='ALU'"; 
	}else if ($split=="ALU"){
		$query .= " AND trim(SAC)='ALU'"; 
	}else if ($split=="KPNGB"){
		$query .= " AND trim(SAC)!='ALU' AND AND trim(WIP)!='ALU' "; 
	}
	//echo $query;
	return $query;
}


function query_acqui_new($reporttype,$techno,$split,$year,$phases){
	if ($reporttype=="TOTAL"){
		$query = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_NEWBUILDS ";
	}else if ($reporttype=="DETAILS"){
		$query = "select * from VW_NET1_ALL_NEWBUILDS ";
	}
	
	$query .= "WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
	AND trim(A04) IS NOT NULL
	AND trim(A353) IS NULL
	AND trim(A80) IS NULL
	AND trim(A81) IS NULL
	AND trim(WOE_RANK) = '1' 
	AND ((trim(A105) IS NULL and trim(A709) is not null) or (trim(A105) IS  NULL and trim(A709) is  null) OR (trim(A105) IS not NULL and trim(A709) is null))";
	if ($phases=="Phase 1"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}else if ($phases=="Phase 2"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$phases."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}	
	$query.=" AND trim(A04) LIKE '%".$year."%'";	
	if ($techno=="EMPTY"){
		$query.=" AND (trim(SIT_LKP_STY_CODE) IS NULL or trim(SIT_LKP_STY_CODE) LIKE '%UNDEF%')";
		$query.=" AND trim(DRE_V2_1_6) NOT lIKE '%Repl%')";
	}else if ($techno=="REPLACEMENTS"){
		$query.=" AND trim(DRE_V2_1_6) lIKE '%Repl%')";
	}else{
		$query.=" AND trim(SIT_LKP_STY_CODE)='".$techno."'";
		$query.=" AND trim(DRE_V2_1_6) NOT lIKE '%Repl%')";
	}		
	
	if ($split=="WIP"){
		$query .= " AND trim(SAC) IS NOT NULL AND trim(SAC)!='ALU' and  trim(WIP)='ALU'"; 
	}else if ($split=="ALU"){
		$query .= " AND trim(SAC)='ALU'"; 
	}else if ($split=="KPNGB"){
		$query .= " AND trim(SAC)!='ALU' AND AND trim(WIP)!='ALU' "; 
	}
	//echo $query;
	return $query;
}

function query_buffer_upg($reporttype,$techno,$split,$year,$phases){
	
	if ($reporttype=="TOTAL"){
		$query = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES ";
	}else if ($reporttype=="DETAILS"){
		$query = "select * from VW_NET1_ALL_UPGRADES ";
	}
	
	$query .= "WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH')
	AND trim(U353) IS  NULL 
	AND trim(U709) IS NOT NULL 
	AND trim(U405) IS NOT NULL 
	AND trim(U353) IS NULL 
	AND trim(U380) IS NULL  
	AND trim(U381) IS NULL ";
	if ($phases=="Phase 1"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}else if ($phases=="Phase 2"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$phases."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}	
	$query.=" AND trim(WOR_LKP_WCO_CODE) IN (".$techno."))";	
	if ($split=="WIP"){
		$query .= " AND trim(SAC) IS NOT NULL AND trim(SAC)!='ALU' and  trim(WIP)='ALU'"; 
	}else if ($split=="ALU"){
		$query .= " AND trim(SAC)='ALU'"; 
	}else if ($split=="KPNGB"){
		$query .= " AND trim(SAC)!='ALU' AND AND trim(WIP)!='ALU' "; 
	}
	//echo $query;
	return $query;
}

function query_buffer_new($reporttype,$techno,$split,$year,$phases){
	if ($reporttype=="TOTAL"){
		$query = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_NEWBUILDS ";
	}else if ($reporttype=="DETAILS"){
		$query = "select * from VW_NET1_ALL_NEWBUILDS ";
	}
	
	$query .= "WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
	AND trim(A709) IS NOT NULL 
	AND trim(A105) IS NOT NULL 
	AND trim(A353) IS NULL  
	AND trim(A80) IS NULL  
	AND trim(A81) IS NULL";
	if ($phases=="Phase 1"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}else if ($phases=="Phase 2"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$phases."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}	
	$query.=" AND trim(WOE_RANK) ='1'";	
	if ($techno=="EMPTY"){
		$query.=" AND (trim(SIT_LKP_STY_CODE) IS NULL or trim(SIT_LKP_STY_CODE) LIKE '%UNDEF%')";
		$query.=" AND trim(DRE_V2_1_6) NOT lIKE '%Repl%')";
	}else if ($techno=="REPLACEMENTS"){
		$query.=" AND trim(DRE_V2_1_6) lIKE '%Repl%')";
	}else{
		$query.=" AND trim(SIT_LKP_STY_CODE)='".$techno."'";
		$query.=" AND trim(DRE_V2_1_6) NOT lIKE '%Repl%')";
	}		
	
	if ($split=="WIP"){
		$query .= " AND trim(SAC) IS NOT NULL AND trim(SAC)!='ALU' and  trim(WIP)='ALU'"; 
	}else if ($split=="ALU"){
		$query .= " AND trim(SAC)='ALU'"; 
	}else if ($split=="KPNGB"){
		$query .= " AND trim(SAC)!='ALU' AND AND trim(WIP)!='ALU' "; 
	}
	//echo $query;
	return $query;
}


function query_con_upg($reporttype,$techno,$split,$year,$phases){
	if ($reporttype=="TOTAL"){
		$query = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES ";
	}else if ($reporttype=="DETAILS"){
		$query = "select * from VW_NET1_ALL_UPGRADES ";
	}
	
	$query .= "WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH')
	AND trim(U353) IS  NOT NULL 
	AND trim(U380) IS NULL 
	AND trim(U381) IS NULL";
	if ($phases=="Phase 1"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}else if ($phases=="Phase 2"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$phases."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}	
	$query.=" AND trim(WOR_LKP_WCO_CODE) IN (".$techno."))";	
	if ($split=="WIP"){
		$query .= " AND trim(CON) IS NOT NULL AND trim(CON)!='ALU' and  trim(WIP)='ALU'"; 
	}else if ($split=="ALU"){
		$query .= " AND trim(CON)='ALU'"; 
	}else if ($split=="KPNGB"){
		$query .= " AND trim(CON)!='ALU' AND AND trim(WIP)!='ALU' "; 
	}
	//echo $query;
	return $query;	
}

function query_con_new($reporttype,$techno,$split,$year,$phases){
	if ($reporttype=="TOTAL"){
		$query = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_NEWBUILDS ";
	}else if ($reporttype=="DETAILS"){
		$query = "select * from VW_NET1_ALL_NEWBUILDS ";
	}
	
	$query .= "WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
	AND trim(A353) IS  NOT NULL  
	AND trim(A80) IS NULL  
	AND trim(A81) IS NULL";
	if ($phases=="Phase 1"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}else if ($phases=="Phase 2"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$phases."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}	
	$query.=" AND trim(WOE_RANK) = '1'";	
	if ($techno=="EMPTY"){
		$query.=" AND (trim(SIT_LKP_STY_CODE) IS NULL or trim(SIT_LKP_STY_CODE) LIKE '%UNDEF%')";
		$query.=" AND trim(DRE_V2_1_6) NOT lIKE '%Repl%')";
	}else if ($techno=="REPLACEMENTS"){
		$query.=" AND trim(DRE_V2_1_6) lIKE '%Repl%')";
	}else{
		$query.=" AND trim(SIT_LKP_STY_CODE)='".$techno."'";
		$query.=" AND trim(DRE_V2_1_6) NOT lIKE '%Repl%')";
	}	
	if ($split=="WIP"){
		$query .= " AND trim(CON) IS NOT NULL AND trim(CON)!='ALU' and  trim(WIP)='ALU'"; 
	}else if ($split=="ALU"){
		$query .= " AND trim(CON)='ALU'"; 
	}else if ($split=="KPNGB"){
		$query .= " AND trim(CON)!='ALU' AND AND trim(WIP)!='ALU' "; 
	}
	//echo $query;
	return $query;
}

function query_onair_upg($reporttype,$techno,$split,$year,$phases){
	if ($reporttype=="TOTAL"){
		$query = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_UPGRADES ";
	}else if ($reporttype=="DETAILS"){
		$query = "select * from VW_NET1_ALL_UPGRADES ";
	}	
	$query .= "WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH')
	AND trim(U381) IS NOT NULL
	AND trim(U381) LIKE '%".$year."'";
	if ($phases=="Phase 1"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}else if ($phases=="Phase 2"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$phases."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}	
	$query.=" AND trim(WOR_LKP_WCO_CODE) IN (".$techno."))";
	if ($split=="WIP"){
		$query .= " AND trim(CON) IS NOT NULL AND trim(CON)!='ALU' and  trim(WIP)='ALU'"; 
	}else if ($split=="ALU"){
		$query .= " AND trim(CON)='ALU'"; 
	}else if ($split=="KPNGB"){
		$query .= " AND trim(CON)!='ALU' AND AND trim(WIP)!='ALU' "; 
	}
	//echo $query;
//	$firephp->log($query,'INSERT_QUERIES');
	return $query;	
}

function query_onair_new($reporttype,$techno,$split,$year,$phases){
	if ($reporttype=="TOTAL"){
		$query = "select count(SIT_UDK) AS AMOUNT from VW_NET1_ALL_NEWBUILDS ";
	}else if ($reporttype=="DETAILS"){
		$query = "select * from VW_NET1_ALL_NEWBUILDS ";
	}
	
	$query .= "WHERE (WOR_DOM_WOS_CODE IN ('IS','SL','OH') 
	AND trim(A81) LIKE '%".$year."' 
	AND trim(A81) IS NOT NULL";
	if ($phases=="Phase 1"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases."+%' AND WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}else if ($phases=="Phase 2"){
	$query .= " AND (WOR_HSDPA_CLUSTER LIKE '%".$phases."%' OR WOR_HSDPA_CLUSTER  LIKE '%".$phases."+%' OR WOR_HSDPA_CLUSTER NOT LIKE '%".$phases." +%')"; 
	}	
	$query.=" AND trim(WOE_RANK) ='1'";	
	if ($techno=="EMPTY"){
		$query.=" AND (trim(SIT_LKP_STY_CODE) IS NULL or trim(SIT_LKP_STY_CODE) LIKE '%UNDEF%')";
		$query.=" AND trim(DRE_V2_1_6) NOT lIKE '%Repl%')";
	}else if ($techno=="REPLACEMENTS"){
		$query.=" AND trim(DRE_V2_1_6) lIKE '%Repl%')";
	}else{
		$query.=" AND trim(SIT_LKP_STY_CODE)='".$techno."'";
		$query.=" AND trim(DRE_V2_1_6) NOT lIKE '%Repl%')";
	}		
	
	if ($split=="WIP"){
		$query .= " AND trim(CON) IS NOT NULL AND trim(CON)!='ALU' and  trim(WIP)='ALU'"; 
	}else if ($split=="ALU"){
		$query .= " AND trim(CON)='ALU'"; 
	}else if ($split=="KPNGB"){
		$query .= " AND trim(CON)!='ALU' AND AND trim(WIP)!='ALU' "; 
	}
	//echo $query;
	return $query;
}

?>
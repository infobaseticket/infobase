<?php
//$query=create_query("",$value,"NA","Base RF - INPUT","SITEID","ASC",0,"","NA","NA",$allocated,"no");

function create_query2($siteID,$rafid,$region,$type,$actionby,$orderby,$order,$start,$end,$rfinfo,$commercial,$allocated,$deleted,$event,$cluster){
	global $guard_groups;

		if ($siteID && $rafid==''){
		$where .= " t.SITEID LIKE '%".$siteID."%'";
	}else if($rafid){
		$where .= " t.RAFID = '".$rafid."'";
	}else{
		if ($region=="NA"){
			$region='';
		}
		$where .= " t.SITEID LIKE '%".$region."%'";
		
		if ($type!="NA"){
			if ($type=="Ind"){
				$where .= " AND (t.TYPE LIKE '%".$type."%' OR t.TYPE LIKE '%RPT%')";
			}else{
				$where .= "AND t.TYPE LIKE '%".$type."%'";
			}
		}
		if ($type!=""){
			$where.=" AND t.DELETED !='yes'";
		}

		$where .= " AND t.LOCKEDD !='yes'";

		if ($rfinfo!="NA" && $rfinfo!=""){
			if ($rfinfo=="Phase 1"){
				$where .= " AND (t.RFINFO LIKE '%Phase 1%' AND t.RFINFO NOT LIKE '%Phase 1 +%' AND t.RFINFO NOT LIKE '%Phase 1+%')";
			}else if ($rfinfo=="Phase 2"){
				$where .= " AND (t.RFINFO LIKE '%Phase 2%' OR t.RFINFO LIKE '%Phase 1 +%' OR t.RFINFO LIKE '%Phase 1+%')";
			}else{
				$where .= " AND UPPER(RFINFO) = '".strtoupper($rfinfo)."'";
			}
		}
		if ($commercial!="NA" and $commercial!=""){
				$where .= " AND (UPPER(t.COMMERCIAL) LIKE '%".strtoupper($commercial)."%')";
		}	
	}
	if ($actionby!=''){
		$where .= " AND (ACTION LIKE '%". $actionby."%' OR ACTION_BY LIKE '%". $actionby."%')";
	}

	if ($deleted!=1 && !$siteID && !$rafid){ //only for reporting //&&  $actionby!="Base Delivery - FAC DATE" && $actionby!="Partner - ACQUIRED"
		$where .= " AND LOWER(t.DELETED)!='yes' AND LOWER(t.LOCKEDD)!='yes'";
	}


	if ($allocated=='BENCHMARK' OR ((substr_count($guard_groups, 'Benchmark')==1) && substr_count($guard_groups, 'Administrators')!=1))
	{
		$where .= " AND (t.CON_PARTNER ='BENCHMARK' OR t.ACQ_PARTNER='BENCHMARK')";
	}
	if ($allocated=='TECHM' OR  ((substr_count($guard_groups, 'TechM')==1) && substr_count($guard_groups, 'Administrators')!=1))
	{
		$where .= " AND (t.CON_PARTNER ='TECHM' OR t.CON_PARTNER ='ALU' OR t.ACQ_PARTNER='TECHM' OR t.ACQ_PARTNER='ALU')";
	}
	if ($allocated=='M4C' OR ((substr_count($guard_groups, 'ZTE')==1) && substr_count($guard_groups, 'Administrators')!=1))
	{
		$where .= " AND (t.CON_PARTNER ='M4C' OR t.ACQ_PARTNER='M4C')";
	}

	if ($event!=''){
		$where .= " AND (t.EVENT= '".$event."')";
	}
	if ($cluster!=''){
		$where .= " AND (t.CLUSTERN ||t.CLUSTERNUM= '".$cluster."')";
	}

	if (!$orderby){
		$orderby="t.RAFID";
	}else{
		$orderby="t.".$orderby;
	}

	$query="SELECT * FROM";
	$query.="(";
    $query.="SELECT t.*,
    ACQ.POPR AS PO_ACQ,
    ACQ.INSERTDATE AS INSERTDATE_ACQ,
     CON.INSERTDATE AS INSERTDATE_CON, 
     CON.POPR AS PO_CON, 
     ACQ.SHORTTEXT AS ACQ_SHORRTEXT, 
    CON.SHORTTEXT AS CON_SHORTTEXT,
    ACT.ACTION,
    ACT.ACTION2,
    ACT.ACTION_DO,
    ACT.ACTION_BY,
    ACT.INOUTDOOR, 
    Row_Number() OVER (ORDER BY ".$orderby."  ".$order.") MyRow ,
    G.BSDSKEY,
    T2.RAFID AS MASTERRAF,
    T2.DELETED AS MASTERDEL
    FROM  BSDS_RAFV2 t  
    LEFT JOIN VW_POPR_ACQ ACQ  ON T.RAFID=ACQ.RAFID 
    LEFT JOIN VW_POPR_CON CON ON T.RAFID=CON.RAFID 
    LEFT JOIN BSDS_GENERALINFO2 G ON T.RAFID= G.RAFID  
    LEFT JOIN RAF_HAS_BOQ COF on COF.BOQ_RAFID=t.RAFID
    LEFT JOIN VW_RAF_ACTIONS_BY_DUPL2 ACT on ACT.RAFID=t.RAFID
    LEFT JOIN BSDS_RAF_RADIO RA on RA.RAFID=t.RAFID
    LEFT JOIN BSDS_RAFV2 T2 on t.RAFID=T2.MASTER_RAFID
    WHERE ".$where;
	$query.=")";
	if ($end!=""){
	$query.=" WHERE MyRow BETWEEN ".$start." AND ".$end;
	}
	//echo $query;
	$query.=" ORDER BY SITEID,RAFID";
	if (substr_count($guard_groups, 'Admin')==1){
		//echo "<br><br> ".$query."\r\n";
	}
	return $query;
}
?>
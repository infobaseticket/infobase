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
				$where .= " AND (TYPE LIKE '%".$type."%' OR TYPE LIKE '%RPT%')";
			}else{
				$where .= "AND TYPE LIKE '%".$type."%'";
			}
		}
		if ($type!=""){
			$where.=" AND DELETED !='yes'";
		}

		$where .= " AND LOCKEDD !='yes'";

		if ($rfinfo!="NA" && $rfinfo!=""){
			if ($rfinfo=="Phase 1"){
				$where .= " AND (RFINFO LIKE '%Phase 1%' AND RFINFO NOT LIKE '%Phase 1 +%' AND RFINFO NOT LIKE '%Phase 1+%')";
			}else if ($rfinfo=="Phase 2"){
				$where .= " AND (RFINFO LIKE '%Phase 2%' OR RFINFO LIKE '%Phase 1 +%' OR RFINFO LIKE '%Phase 1+%')";
			}else{
				$where .= " AND UPPER(RFINFO) = '".strtoupper($rfinfo)."'";
			}
		}
		if ($commercial!="NA" and $commercial!=""){
				$where .= " AND (UPPER(COMMERCIAL) LIKE '%".strtoupper($commercial)."%')";
		}	
	}
	if ($actionby!=''){
		$where .= " AND (ACTION LIKE '%". $actionby."%' OR ACTION_BY LIKE '%". $actionby."%')";
	}

	if ($deleted!=1 && !$siteID && !$rafid){ //only for reporting //&&  $actionby!="Base Delivery - FAC DATE" && $actionby!="Partner - ACQUIRED"
		$where .= " AND LOWER(DELETED)!='yes' AND LOWER(LOCKEDD)!='yes'";
	}


	if ($allocated=='BENCHMARK' OR ((substr_count($guard_groups, 'Benchmark')==1) && substr_count($guard_groups, 'Administrators')!=1))
	{
		$where .= " AND (CON_PARTNER ='BENCHMARK' OR ACQ_PARTNER='BENCHMARK')";
	}
	if ($allocated=='TECHM' OR  ((substr_count($guard_groups, 'TechM')==1) && substr_count($guard_groups, 'Administrators')!=1))
	{
		$where .= " AND (CON_PARTNER ='TECHM' OR CON_PARTNER ='ALU' OR ACQ_PARTNER='TECHM' OR ACQ_PARTNER='ALU')";
	}
	if ($allocated=='M4C' OR ((substr_count($guard_groups, 'ZTE')==1) && substr_count($guard_groups, 'Administrators')!=1))
	{
		$where .= " AND (CON_PARTNER ='M4C' OR ACQ_PARTNER='M4C')";
	}

	if ($event!=''){
		$where .= " AND (EVENT= '".$event."')";
	}
	if ($cluster!=''){
		$where .= " AND (CLUSTERN ||CLUSTERNUM= '".$cluster."')";
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
    G.BSDSKEY
    FROM  BSDS_RAFV2 t  
    LEFT JOIN VW_POPR_ACQ ACQ  ON T.RAFID=ACQ.RAFID 
    LEFT JOIN VW_POPR_CON CON ON T.RAFID=CON.RAFID 
    LEFT JOIN BSDS_GENERALINFO2 G ON T.RAFID= G.RAFID  
    LEFT JOIN RAF_HAS_BOQ COF on COF.BOQ_RAFID=t.RAFID
    LEFT JOIN VW_RAF_ACTIONS_BY_DUPL ACT on ACT.RAFID=t.RAFID
    LEFT JOIN BSDS_RAF_RADIO RA on RA.RAFID=t.RAFID
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
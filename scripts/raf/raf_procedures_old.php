<?php
//$query=create_query("",$value,"NA","Base RF - INPUT","SITEID","ASC",0,"","NA","NA",$allocated,"no");

function create_query($siteID,$rafid,$region,$type,$actionby,$orderby,$order,$start,$end,$rfinfo,$commercial,$allocated,$build,$deleted){
	global $guard_groups;

	if ($siteID && $rafid==''){
		$where .= " (t.SITEID LIKE '%".$siteID."%'";
	}else if($rafid){
		$where .= " (t.RAFID = '".$rafid."'";
	}else{
		if ($region!="NA"){
			$where .= " t.SITEID LIKE '%".$region."%' AND ";
		}
		if ($type!="NA"){
			if ($type=="Ind"){
				$where .= " (TYPE LIKE '%".$type."%' OR TYPE LIKE '%RPT%') AND";
			}else{
				$where .= " TYPE LIKE '%".$type."%' AND";
			}
		}
		if ($type!=""){
			$where.=" DELETED !='yes' AND";
		}


		if ($actionby!="Base Delivery - Locked RAF"){
			$where .= " LOCKEDD !='yes' AND";
		}

		$where .= " (  ";
	}

	if ($actionby=="NA"){
		$where .= " RADIO_INP IS NOT NULL AND NET1_LINK!='END'";
	}

	if ($actionby=="Base Other"){
		$where .= " OTHER_INP='NOT_OK' OR OTHER_INP='REJECTED'";
	}

	if ($actionby=="Base RF" || $actionby=="Base RF - INPUT"){
		$where .= " (OTHER_INP!='REJECTED' AND RADIO_INP='NOT OK')";
	}
	if ($actionby=="Base RF - INPUT REJECTED"){
		$where .= " (OTHER_INP!='REJECTED' AND RADIO_INP='REJECTED')";
	}
	if ($actionby=="Base RF" || $actionby=="Base RF - BCS"){
		if ($actionby=="Base RF"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK!='END' AND PARTNER_INP='OK' AND (BCS_NET1='NOT OK' OR  BCS_NET1='NET1LINK' OR  BCS_NET1='AWAIT SYNC') AND  BCS_RF_INP='NOT OK' AND ACQ.POPR IS NOT NULL AND COF_ACQ='BASE OK' AND BUFFER!=1)";

		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}
	if ($actionby=="Base RF" || $actionby=="Base RF - BCS NET1"){ //Base RF - BCS NET1 (AUTO FIELD)
		if ($actionby=="Base RF"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK!='END' AND PARTNER_INP='OK' AND (BCS_NET1='NOT OK' OR  BCS_NET1='NET1LINK' OR  BCS_NET1='AWAIT SYNC') AND BCS_RF_INP!='NOT OK' AND BCS_TX_INP!='NOT OK' AND ACQ.POPR IS NOT NULL AND BUFFER!=1)";

		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	if ($actionby=="Base RF" || $actionby=="Base RF - FUNDING"){
		if ($actionby=="Base RF"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK'  AND NET1_LINK!='END' AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND  BCS_NET1!='AWAIT SYNC' AND BCS_RF_INP!='NOT OK' AND BCS_TX_INP!='NOT OK' AND NET1_LBP !='NOT OK' AND PARTNER_ACQUIRED!='REJECTED' AND PARTNER_ACQUIRED!='NOT OK' AND TXMN_ACQUIRED!='NOT OK' AND (RADIO_FUND='NOT OK' or RADIO_FUND IS NULL) )"; //AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	if ($actionby=="Base RF" || $actionby=="Base RF - FUNDING BLOCKED"){
		if ($actionby=="Base RF"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK'  AND NET1_LINK!='END' AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND  BCS_NET1!='AWAIT SYNC' AND BCS_RF_INP!='NOT OK' AND BCS_TX_INP!='NOT OK' AND NET1_LBP !='NOT OK' AND PARTNER_ACQUIRED!='REJECTED' AND PARTNER_ACQUIRED!='NOT OK' AND TXMN_ACQUIRED!='NOT OK' AND (RADIO_FUND='NOT FUNDED' OR  RADIO_FUND='ON HOLD' OR  RADIO_FUND='NO BUDGET' ) )"; //AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}
	
	if ($actionby=="Base RF - FUNDING REJECTED"){
		if ($actionby=="Base RF"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK'  AND NET1_LINK!='END' AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK' AND PARTNER_ACQUIRED!='REJECTED' AND  TXMN_ACQUIRED!='NOT OK' AND RADIO_FUND='REJECTED')"; //AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}
	if ($actionby=="Base RF - FUNDING WITHOUT"){
			$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK'  AND NET1_LINK!='END' AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK' AND PARTNER_ACQUIRED!='REJECTED' AND TXMN_ACQUIRED!='NOT OK' AND (RADIO_FUND='NO BUDGET' or RADIO_FUND='AWAIT LTE BUDGET' or RADIO_FUND='NO BUDGET' or RADIO_FUND='ON HOLD'))"; //AND NET1_ACQUIRED!='NOT OK'
			if ($allocated!=""){
				$where .= " AND SAC ='".$allocated."'";
			}

	}

	if ($actionby=="Base RF" || $actionby=="Base RF - PAC"){
		if ($actionby=="Base RF"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND PARTNER_ACQUIRED!='NOT OK' 
		AND (ACQ.POPR IS NOT NULL OR BUFFER=1) AND CON.POPR IS NOT NULL
		AND (TXMN_INP='OK' OR TXMN_INP='NA') AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND NET1_LBP !='NOT OK'   AND RADIO_FUND!='NOT OK' 
		AND PARTNER_RFPACK!='NOT OK' AND PARTNER_RFPACK!='REJECTED' AND COF_CON='BASE OK' AND (RF_PAC='NOT OK' OR RF_PAC='REJECTED' OR RF_PAC='09-SEP-1990' OR RF_PAC='AWAITING SYNC' OR RF_PAC='OK') AND TXMN_ACQUIRED!='NOT OK' AND TXMN_ACQUIRED IS NOT NULL)";//AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND CON ='".$allocated."'";
		}
	}

	if ($actionby=="Base TXMN" || $actionby=="Base TXMN - INPUT"){
		$where .= " TXMN_INP='NOT OK' AND (RADIO_INP='OK' OR RADIO_INP='NA')";
	}
	if ($actionby=="Base TXMN" || $actionby=="Base TXMN - ACQUISITION APPROVAL"){
		if ($actionby=="Base TXMN"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')  AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND NET1_LINK!='END' AND (TXMN_INP='OK' or TXMN_INP='NA') AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1 !='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK' AND PARTNER_ACQUIRED!='NOT OK' AND PARTNER_ACQUIRED!='REJECTED' AND TXMN_ACQUIRED='NOT OK' AND ACQ.POPR IS NOT NULL AND BUFFER!=1) ";
	}

	if ($actionby=="Base TXMN" || $actionby=="Base TXMN - ACQUISITION APPROVAL CONDITIONAL"){
		if ($actionby=="Base TXMN"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')  AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND NET1_LINK!='END' AND NET1_LINK!='END' AND (TXMN_INP='OK' or TXMN_INP='NA') AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1 !='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK' AND PARTNER_ACQUIRED!='NOT OK' AND PARTNER_ACQUIRED!='REJECTED' AND TXMN_ACQUIRED='COND OK') ";
	}

	if ($actionby=="Base TXMN" || $actionby=="Base TXMN - BCS"){
		if ($actionby=="Base TXMN"){
			$where .= " OR ";
		}
		$where .= " OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK!='END' AND PARTNER_INP='OK' AND (BCS_NET1='NOT OK' OR BCS_NET1='NET1LINK') AND  BCS_TX_INP='NOT OK' AND ACQ.POPR IS NOT NULL AND COF_ACQ!='NOT OK' AND BUFFER!=1";

		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	if ($actionby=="Base Delivery - Locked RAF"){
		$where .= " (LOCKEDD='yes')";
	}

	if ($actionby=="Base Delivery" || $actionby=="Base Delivery - NET1 LINK"){
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND ACQ_PARTNER!='NOT OK'
		AND (TXMN_INP='OK' OR  TXMN_INP='NA') AND (NET1_LINK='NOT OK' OR NET1_LINK IS NULL OR NET1_LINK='BCS CHANGE' ))";
	}
	if ($actionby=="Base Delivery - ACQ PARTNER"){
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')
		AND (TXMN_INP='OK' OR  TXMN_INP='NA') AND (NET1_LINK='NOT OK' OR NET1_LINK IS NULL ) AND ACQ_PARTNER='NOT OK' AND BUFFER=0)";
	}
	if ($actionby=="Base Delivery - CON PARTNER"){
		$where .= " (OTHER_INP!='REJECTED' AND(RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK!='END' AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK' AND PARTNER_ACQUIRED!='REJECTED' AND PARTNER_ACQUIRED!='NOT OK' 
			AND  TXMN_ACQUIRED!='NOT OK' AND RADIO_FUND!='NOT OK' AND RADIO_FUND!='NOT FUNDED' AND RADIO_FUND!='NO BUDGET' AND RADIO_FUND!='EXISTING TECHNOS' AND RADIO_FUND!='REJECTED' AND RADIO_FUND!='ON HOLD' AND (ACQ.POPR IS NOT NULL OR BUFFER=1) AND CON_PARTNER='NOT OK')"; //AND NET1_ACQUIRED!='NOT OK'
	}

	if ($actionby=="Base Delivery" || $actionby=="Base Delivery - MISSING PO ACQ"){
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')
		AND (TXMN_INP='OK' OR  TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND UPPER(NET1_LINK)!='OK' AND NET1_LINK!='END' AND ACQ.POPR IS NULL AND TYPE!='New Indoor' AND BUFFER=0 AND TYPE!='Dismantling' AND (COF_ACQ = 'BASE OK' or COF_ACQ = 'NA'))";
	}

	if ($actionby=="Base Delivery" || $actionby=="Base Delivery - RAF ACQUIRED"){
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}

		$where .= " (OTHER_INP!='REJECTED'
		AND (RADIO_INP='OK' OR RADIO_INP='NA')
		AND (TXMN_INP='OK' or TXMN_INP='NA')
		AND NET1_LINK!='NOT OK'
		AND NET1_LINK !='OK'
		AND (PARTNER_INP='OK' OR PARTNER_INP='NA')
		AND BCS_NET1!='NOT OK'  AND BCS_NET1!='NET1LINK'
		AND NET1_LBP !='NOT OK' )";
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	if ($actionby=="Base Delivery" || $actionby=="Base Delivery - FUND DATE"){
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK !='OK' AND NET1_LINK!='END'  AND ACQ.POPR IS NOT NULL AND BUFFER!=1 AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK'  AND CON_PARTNER!='NOT OK' AND RADIO_FUND!='NOT OK' AND RADIO_FUND!='REJECTED' AND RADIO_FUND!='NOT FUNDED' AND RADIO_FUND!='NO BUDGET' AND RADIO_FUND!='ON HOLD' AND RADIO_FUND!='END' AND NET1_FUND='NOT OK')";//AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	if ($actionby=="Base Delivery" || $actionby=="Base Delivery - MISSING PO CON"){
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK'  AND NET1_LINK!='END' AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK'  AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK' AND PARTNER_ACQUIRED!='REJECTED' AND  TXMN_ACQUIRED!='NOT OK' AND PARTNER_ACQUIRED!='NOT OK' AND RADIO_FUND!='NOT OK' AND RADIO_FUND!='NOT FUNDED' AND RADIO_FUND!='END' AND RADIO_FUND!='ON HOLD' AND NET1_FUND!='NOT OK' AND CON.POPR IS NULL AND COF_CON!='NOT OK' AND COF_CON!='PARTNER OK' AND COF_CON!='BASE TS OK' AND TYPE!='Dismantling')"; //AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}


	if ($actionby=="Base Delivery" || $actionby=="Base Delivery - PO CHECK"){
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' OR TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK !='OK' AND NET1_LINK!='END' AND  (TXMN_INP='OK' OR TXMN_INP='NA') AND BCS_NET1!='NOT OK'  AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK'  AND PARTNER_ACQUIRED!='NOT OK' AND RADIO_FUND!='NOT OK' AND NET1_FUND!='NOT OK' AND TXMN_ACQUIRED!='NOT OK' AND NET1_PAC='NOT OK'  AND PARTNER_RFPACK!='NOT OK' AND POCON_PARTNER='OK CONFIRMED' AND (POCON_DELIVERY='NOT OK' OR POCON_DELIVERY='OK NOT CONFIRMED' OR POCON_DELIVERY='REJECTED'))"; 
	}

	if ($actionby=="Base Delivery" || $actionby=="Base Delivery - PAC DATE"){
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' OR TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK !='OK' AND NET1_LINK!='END' AND  (TXMN_INP='OK' OR TXMN_INP='NA') AND BCS_NET1!='NOT OK'  AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK'  AND PARTNER_ACQUIRED!='NOT OK' AND RADIO_FUND!='NOT OK' AND NET1_FUND!='NOT OK' AND RF_PAC!='NOT OK' AND RF_PAC!='REJECTED'  AND RF_PAC!='09-SEP-1990' AND TXMN_ACQUIRED!='NOT OK' AND (NET1_PAC='NOT OK' or  NET1_PAC='OK' or  NET1_PAC='AWAITING SYNC')  AND PARTNER_RFPACK!='NOT OK' and (PARTNER_VALREQ='PAC&FAC CONFIRMED' or PARTNER_VALREQ='PAC CONFIRMED'))"; //AND NET1_ACQUIRED!='NOT OK'

		if ($allocated!=""){
			$where .= " AND CON ='".$allocated."'";
		}
	}

	if ($actionby=="Base Delivery" || $actionby=="Base Delivery - FAC DATE"){
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' OR TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK !='OK' AND NET1_LINK!='END' AND  (TXMN_INP='OK' OR TXMN_INP='NA') AND BCS_NET1!='NOT OK'  AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK'  AND PARTNER_ACQUIRED!='NOT OK' AND RADIO_FUND!='NOT OK' AND NET1_FUND!='NOT OK' AND RF_PAC!='NOT OK' AND RF_PAC!='REJECTED'  AND RF_PAC!='09-SEP-1990' AND TXMN_ACQUIRED!='NOT OK' AND (NET1_FAC='NOT OK' or  NET1_FAC='OK' or  NET1_PAC='AWAITING SYNC')  AND PARTNER_RFPACK!='NOT OK' and (PARTNER_VALREQ='PAC&FAC CONFIRMED' or PARTNER_VALREQ='FAC CONFIRMED'))"; //AND NET1_ACQUIRED!='NOT OK'

		if ($allocated!=""){
			$where .= " AND CON ='".$allocated."'";
		}
	}

	if ($actionby=="Base Delivery - COF ACQ"){
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK'  AND NET1_LINK!='END' AND SAC!='NOT OK' AND (COF_ACQ='PARTNER OK' OR (COF_ACQ='NOT OK' AND TYPE!='MOV Upgrade' AND TYPE!='IND Upgrade' AND TYPE!='RPT Upgrade' AND TYPE!='CWK Upgrade' AND TYPE!='New Indoor')))"; 
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	if ($actionby=="Base Delivery - COF CON PM"){ //HAS NO BOQ
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK !='OK' AND TXMN_ACQUIRED!='NOT OK' AND NET1_LINK!='END'  AND (ACQ.POPR IS NOT NULL OR BUFFER=1) AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK'  AND CON_PARTNER!='NOT OK' AND RADIO_FUND!='NOT OK' AND RADIO_FUND!='REJECTED' AND PARTNER_ACQUIRED!='NOT OK' AND RADIO_FUND!='NOT FUNDED' AND RADIO_FUND!='NO BUDGET' AND NET1_FUND!='NOT OK' AND ((COF_CON='PARTNER OK' AND BOQ_RAFID IS NULL) OR COF_CON='BASE TS OK') )";//AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND CON ='".$allocated."'";
		}
	}
	if ($actionby=="Base Delivery - COF CON TS"){
		if ($actionby=="Base Delivery"){
			$where .= " OR ";
		}
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK !='OK' AND TXMN_ACQUIRED!='NOT OK' AND NET1_LINK!='END'  AND (ACQ.POPR IS NOT NULL OR BUFFER=1) AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK'  AND CON_PARTNER!='NOT OK' AND RADIO_FUND!='NOT OK' AND RADIO_FUND!='REJECTED' AND PARTNER_ACQUIRED!='NOT OK' AND RADIO_FUND!='NOT FUNDED' AND RADIO_FUND!='NO BUDGET' AND NET1_FUND!='NOT OK' AND COF_CON='PARTNER OK' AND BOQ_RAFID IS NOT NULL) ";//AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND CON ='".$allocated."'";
		}
	}

	if ($actionby=="Partner - INPUT"){

		$where .= " OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')  AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND NET1_LINK!='END' AND (TXMN_INP='OK' OR TXMN_INP='NOT OK') AND PARTNER_INP='NOT OK' AND ACQ.POPR IS NOT NULL AND BUFFER!=1";
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	if ($actionby=="Partner - A304"){

		$where .= " OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')  AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND NET1_LINK!='END' AND (TXMN_INP='OK' OR TXMN_INP='NOT OK') AND PARTNER_INP='OK' AND ACQ.POPR IS NOT NULL AND BUFFER!=1 AND NET1_A304='NOT OK'";
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	

	if ($actionby=="Partner - INPUT REJECTED"){

		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')  AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND NET1_LINK!='END' AND (TXMN_INP='OK' OR TXMN_INP='NOT OK') AND PARTNER_INP='REJECTED')";
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}
	
	if ($actionby=="Partner - LBP"){
		
		$where .= " OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')  AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND NET1_LINK!='END' AND (TXMN_INP='OK' OR TXMN_INP='NA') AND PARTNER_INP!='NOT OK' AND BCS_NET1 !='NOT OK'  AND BCS_NET1!='NET1LINK' AND (BCS_RF_INP='OK' OR BCS_RF_INP='NA') AND (BCS_TX_INP='OK' OR BCS_TX_INP='NA') AND NET1_LBP ='NOT OK' AND ACQ.POPR IS NOT NULL AND BUFFER!=1";
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}
	
	if ($actionby=="Partner - ACQUIRED REJECTED"){
		
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')  AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND NET1_LINK!='END' AND (TXMN_INP='OK' or TXMN_INP='NA') AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1 !='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK' AND PARTNER_ACQUIRED='REJECTED') ";
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}
	if ($actionby=="Partner" ||$actionby=="Partner - ACQUIRED"){
		
		$where .= " OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')  AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND NET1_LINK!='END' AND (TXMN_INP='OK' or TXMN_INP='NA') AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1 !='NOT OK' AND BCS_NET1!='NET1LINK'  AND NET1_LBP !='NOT OK' AND (PARTNER_ACQUIRED='NOT OK' OR PARTNER_ACQUIRED='REJECTED' OR PARTNER_ACQUIRED='AWAITING NET1') AND ACQ.POPR IS NOT NULL AND BUFFER!=1";
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	if ($actionby=="Partner" ||$actionby=="Partner - SUBMIT RF PACK"){
		
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')  AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND NET1_LINK!='END' AND (TXMN_INP='OK' OR TXMN_INP='NA') AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1 !='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK' AND RADIO_FUND!='NOT OK' AND NET1_FUND!='NOT OK' AND PARTNER_ACQUIRED!='NOT OK' AND TXMN_ACQUIRED!='NOT OK' AND  CON_PARTNER!='NOT OK' AND CON.POPR !='NOT OK' AND (ACQ.POPR IS NOT NULL OR BUFFER=1) AND COF_CON!='NOT OK' AND COF_CON!='PARTNER OK' AND (PARTNER_RFPACK='NOT OK' OR PARTNER_RFPACK='REJECTED' OR PARTNER_RFPACK='AWAITING NET1'))"; //AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND CON ='".$allocated."'";
		}
	}

	if ($actionby=="Partner - COF ACQ"){
		
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK'  AND NET1_LINK!='END' AND COF_ACQ='NOT OK' AND (TYPE='MOV Upgrade' or TYPE='IND Upgrade' or TYPE='RPT Upgrade' or TYPE='CWK Upgrade' or TYPE='New Indoor'))"; //AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND SAC ='".$allocated."'";
		}
	}

	if ($actionby=="Partner - COF CON"){
		
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA') AND (TXMN_INP='OK' or TXMN_INP='NA') AND NET1_LINK!='NOT OK' AND NET1_LINK !='OK' AND NET1_LINK!='END' AND TXMN_ACQUIRED!='NOT OK' AND (ACQ.POPR IS NOT NULL OR BUFFER=1) AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1!='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK'  AND CON_PARTNER!='NOT OK' AND RADIO_FUND!='NOT OK' AND PARTNER_ACQUIRED!='NOT OK' AND RADIO_FUND!='REJECTED' AND RADIO_FUND!='NOT FUNDED' AND RADIO_FUND!='NO BUDGET' AND NET1_FUND!='NOT OK' AND COF_CON='NOT OK') ";//AND NET1_ACQUIRED!='NOT OK'
		if ($allocated!=""){
			$where .= " AND CON ='".$allocated."'";
		}
	}

	if ($actionby=="Partner - PACFAC"){
		$where .= " (OTHER_INP!='REJECTED' AND (RADIO_INP='OK' OR RADIO_INP='NA')
		AND NET1_LINK!='NOT OK' AND NET1_LINK!='OK' AND (TXMN_INP='OK' OR TXMN_INP='NA')
		AND (PARTNER_INP='OK' OR PARTNER_INP='NA') AND BCS_NET1 !='NOT OK' AND BCS_NET1!='NET1LINK' AND NET1_LBP !='NOT OK' AND RADIO_FUND!='NOT OK' AND NET1_FUND!='NOT OK' AND TXMN_ACQUIRED!='NOT OK' AND (PARTNER_RFPACK!='NOT OK'  OR PARTNER_RFPACK!='REJECTED')) AND (PARTNER_VALREQ='READY FOR PAC' or PARTNER_VALREQ='READY FOR FAC' or PARTNER_VALREQ='READY FOR PAC&FAC')  AND (NET1_PAC!='NOT OK' OR NET1_FAC!='NOT OK')";
		if ($allocated!=""){
			$where .= " AND CON ='".$allocated."'";
		}
	}

	$where .= ")";
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
	
	if (substr_count($guard_groups, 'Partner')==1){
			$where .= " AND (TXMN_INP='OK' or TXMN_INP='NA')";
	}
/*
	if($remove_empty == "yes") {
		$where .= " AND (PARTNER_RFPACK_DATE IS NOT NULL OR RF_PAC_DATE IS NOT NULL)";
	}*/

	
	if ($build!=1 && !$siteID && !$rafid && $actionby!="Base RF - BCS NET1"){ //only for reporting //&&  $actionby!="Base Delivery - FAC DATE" && $actionby!="Partner - ACQUIRED"
		$where .= " AND (NET1_PAC='NOT OK' OR NET1_PAC='NA') AND RADIO_FUND!='END'";
	}
	if ($deleted!=1 && !$siteID && !$rafid){ //only for reporting //&&  $actionby!="Base Delivery - FAC DATE" && $actionby!="Partner - ACQUIRED"
		$where .= " AND LOWER(DELETED)!='yes' AND LOWER(LOCKEDD)!='yes'";
	}

	if (!$orderby){
		$orderby="t.RAFID";
	}else{
		$orderby="t.".$orderby;
	}
// 
		$query="SELECT * FROM";
		$query.="(";
	    $query.="SELECT t.*,ACQ.POPR AS POPR_ACQ,ACQ.INSERTDATE AS INSERTDATE_ACQ, CON.INSERTDATE AS INSERTDATE_CON, CON.POPR AS POPR_CON, ACQ.SHORTTEXT AS ACQ_SHORRTEXT, 
	    CON.SHORTTEXT AS CON_SHORTTEXT, Row_Number() OVER (ORDER BY ".$orderby."  ".$order.") MyRow ,
	    G.BSDSKEY
	    FROM  BSDS_RAFV2 t  
	    LEFT JOIN VW_POPR_ACQ ACQ  ON T.RAFID=ACQ.RAFID 
	    LEFT JOIN VW_POPR_CON CON ON T.RAFID=CON.RAFID 
	    LEFT JOIN BSDS_GENERALINFO2 G ON T.RAFID= G.RAFID  
	    LEFT JOIN RAF_HAS_BOQ COF on COF.BOQ_RAFID=t.RAFID
	    WHERE ". $where;
		$query.=")";
		if ($end!=""){
		$query.=" WHERE MyRow BETWEEN ".$start." AND ".$end;
		}
		$query.=" ORDER BY SITEID,RAFID";
	if (substr_count($guard_groups, 'Admin')==1){
		//echo "<br><br>(".$actionby.") ".$query."\r\n";
	}
	return $query;
}

function create_query2($siteID,$rafid,$region,$type,$actionby,$orderby,$order,$start,$end,$rfinfo,$commercial,$allocated,$deleted){
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
				$where .= "AND TYPE LIKE '%".$type."%' AND";
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

	if ($allocated!=""){
		$where .= " AND (CON_PARTNER ='".$allocated."' OR ACQ_PARTNER='".$allocated."')";
	}

	if (!$orderby){
		$orderby="t.RAFID";
	}else{
		$orderby="t.".$orderby;
	}

	$query="SELECT * FROM";
	$query.="(";
    $query.="SELECT t.*,ACQ.POPR AS POPR_ACQ,ACQ.INSERTDATE AS INSERTDATE_ACQ, CON.INSERTDATE AS INSERTDATE_CON, CON.POPR AS POPR_CON, ACQ.SHORTTEXT AS ACQ_SHORRTEXT, 
    CON.SHORTTEXT AS CON_SHORTTEXT,ACT.ACTION2, Row_Number() OVER (ORDER BY ".$orderby."  ".$order.") MyRow ,
    G.BSDSKEY
    FROM  BSDS_RAFV2 t  
    LEFT JOIN VW_POPR_ACQ ACQ  ON T.RAFID=ACQ.RAFID 
    LEFT JOIN VW_POPR_CON CON ON T.RAFID=CON.RAFID 
    LEFT JOIN BSDS_GENERALINFO2 G ON T.RAFID= G.RAFID  
    LEFT JOIN RAF_HAS_BOQ COF on COF.BOQ_RAFID=t.RAFID
    LEFT JOIN VW_RAF_ACTIONS_BY_DUPL ACT on ACT.RAFID=t.RAFID
    WHERE ".$where;
	$query.=")";
	if ($end!=""){
	$query.=" WHERE MyRow BETWEEN ".$start." AND ".$end;
	}
	$query.=" ORDER BY SITEID,RAFID";
	if (substr_count($guard_groups, 'Admin')==1){
		//echo "<br><br> ".$query."\r\n";
	}
	return $query;
}
?>
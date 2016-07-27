<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_other","");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


function query_audit($site,$auditid,$audittype1,$audittype2,$region,$datefilter,$daterange,$orderby,$order,$planning){

	$query = "select * from BSDS_AUDITS WHERE id is not null";
	if ($audittype1!=""){
		$query .= " AND type = '".$audittype1."'";
	}
	if ($audittype2!=""){
		$query .= " AND type2 = '".$audittype2."'";
	}
	if ($datefilter!=""){
		$date_split=explode(' - ',$daterange);
		$query .= " AND (TO_DATE('".$date_split[0]."','DD/MM/YYYY') <= ".$datefilter." AND TO_DATE('".$date_split[1]."','DD/MM/YYYY')+1 >= ".$datefilter.")";
	}

	if ($site!=""){
		$query .= " AND SITE LIKE '%".strtoupper($site)."%'";
	}
	if ($region!="" AND $region!="NA"){
		$query .= " AND (SITE LIKE '%_".strtoupper($region)."%'
		OR SITE LIKE '%S".strtoupper($region)."%'
		OR SITE LIKE '%M".strtoupper($region)."%'
		OR SITE LIKE '%T".strtoupper($region)."%')";
	}

	if (substr_count($guard_groups, 'Cofely')==1){
		$query .= " AND INSPECTIONPARTNER = 'COFELY SERVICES'";
	}else if (substr_count($guard_groups, 'DLConsulting')==1 ){
		$query .= " AND INSPECTIONPARTNER = 'COFELY SERVICES'";
	}

	if (substr_count($guard_groups, 'Alcatel')==1){
			$query .= " AND (SERVICEPARTNER1 = 'ALUROL' OR SERVICEPARTNER1 = 'ALUROP')";
		}else if (substr_count($guard_groups, 'Benchmark')==1 ){
			$query .= " AND SERVICEPARTNER1 = 'BENCHMARK'";
	}

	if ($auditid!=""){
		$query .= " AND ID = '".$auditid."'";
	}

	if ($planning=="yes"){
			$query .= " AND (PLANNING = 'yes')";
	}else{
			$query .= " AND (PLANNING != 'yes' OR PLANNING IS NULL)";
	}


	if($order_by==''){
		$query .= " ORDER BY SITE,TYPE,TYPE2,CREATION_DATE DESC";
	}else{
		$query .= " ORDER BY ".$order_by." ".$order;
	}

	//echo $query;
	return $query;
}

function query_audit_comments($auditnr){

	$query = "select COMMENTS, REASON, REASON_COMMENTS, REASONKPNGB1, REASONKPNGB2 from BSDS_AUDITS WHERE id = '".$auditnr."'";
	return $query;
}

function get_reason($REASON){
	if ($REASON=="R1"){
		$REASON="Info not on shared drive";
	}else if ($REASON=="R2"){
		$REASON="Info incomplete";
	}else if ($REASON=="R3"){
		$REASON="Content incorrect";
	}else if ($REASON=="R4"){
		$REASON="Document not signed (validated) by ALU";
	}else if ($REASON=="R5"){
		$REASON="NET1 not ok";
	}
	return $REASON;
}


function get_net1_maxdate($NET1_selected){
	global $conn_Infobase;
	$temp=explode(':',$NET1_selected);
	$type=$temp[0];
	$site=$temp[1];

	$temp2=explode('[',$NET1_selected);
	$upgnr=substr($temp2[1],0,-1);

	if ($type=="NB"){
		$query1 = "select * from VW_NET1_ALL_NEWBUILDS WHERE upper(SIT_UDK) LIKE '%".trim($site)."%' AND WOE_RANK=1 AND WOR_DOM_WOS_CODE ='IS' ORDER BY SIT_UDK";
				//echo $query1."<br>";
				$stmt1 = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
				if (!$stmt1) {
				  die_silently($conn_Infobase, $error_str);
				  exit;
				} else {
					  OCIFreeStatement($stmt1);
					  $amount1=count($res1['SIT_UDK']);
					  if ($amount1=='1'){
						if ($res1['A81'][0]!=""){
							return  "FAC: ".$res1['A81'][0];
						}else if ($res1['A72'][0]!=""){
							return "PAC: ".$res1['A72'][0];
						}else if ($res1['A80'][0]!=""){
							return "DEBARRED: ".$res1['A80'][0];
						}else if ($res1['A91'][0]!=""){
							return "JI: ".$res1['A91'][0];
						}else if ($res1['A63'][0]!=""){
							return "CWI: ".$res1['A63'][0];
						}else if ($res1['A71'][0]!=""){
							return "INT: ".$res1['A71'][0];
						}else if ($res1['A59'][0]!=""){
							return "PS: ".$res1['A59'][0];
						}else if ($res1['A353'][0]!=""){
							return "FUNDED: ".$res1['A353'][0];
						}else if ($res1['A105'][0]!=""){
							return "LEASE OK: ".$res1['A105'][0];
						}else if ($res1['A709'][0]!=""){
							return "BP OK: ".$res1['A709'][0];
						}else{
							return '=> ONLY A1 Inspection allowed (NB)';
						}
					  }else{
						return 'ERROR IN NET1 NB';
					  }

		}

	}else if ($type=="UPG"){
		$query1 = "select * from VW_NET1_ALL_UPGRADES WHERE upper(SIT_UDK) LIKE '%".trim($site)."%' AND WOR_LKP_WCO_CODE NOT LIKE 'SH%' AND WOR_UDK ='".$upgnr."'";
		//echo $query1."<br>";
		$stmt1 = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
		if (!$stmt1) {
		  die_silently($conn_Infobase, $error_str);
		  exit;
		} else {
			  OCIFreeStatement($stmt1);
			  $amount1=count($res1['WOR_UDK']);
			  if ($amount1=='1'){
				if ($res1['U381'][0]!=""){
					return  "FAC: ".$res1['U381'][0];
				}else if ($res1['U418'][0]!=""){
					return "PAC: ".$res1['U418'][0];
				}else if ($res1['U380'][0]!=""){
					return "DEBARRED: ".$res1['U380'][0];
				}else if ($res1['U391'][0]!=""){
					return "JI: ".$res1['U391'][0];
				}else if ($res1['U363'][0]!=""){
					return "CWI: ".$res1['U363'][0];
				}else if ($res1['U571'][0]!=""){
					return "INT: ".$res1['U571'][0];
				}else if ($res1['A59'][0]!=""){
					return "PS: ".$res1['U459'][0];
				}else if ($res1['U353'][0]!=""){
					return "FUNDED: ".$res1['U353'][0];
				}else if ($res1['A105'][0]!=""){
					return "LEASE OK: ".$res1['A105'][0];
				}else if ($res1['A709'][0]!=""){
					return "BP OK: ".$res1['A709'][0];
				}else{
					return '=> ONLY A1 Inspection allowed (UPG)';
				}
			  }else{
				return 'ERROR IN NET1 UPG';
			  }

		}
	}
}
?>
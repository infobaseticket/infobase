<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


function get_GSM_data($siteID){
	global $conn_Infobase,$config,$guard_groups;
	global $conn_Infobase;
	if (strlen($siteID)<4){
		echo "<font color='red' size='2'><B>You need to enter at least 4 characters!</B></FONT>";
		die;
	}

	if (strlen($siteID)>6){
		if (substr($siteID,0,1)=="M"){
			$sitesearch=substr($siteID,1,-1);
		}else{
			$sitesearch=substr($siteID,0,-1);
		}
	}else{
		$sitesearch=$siteID;
	}


	$query1 = "SELECT BSC, MO, RSITE FROM SWITCH_2G_RXMOP_TG  WHERE rsite like '%".$sitesearch."%' ORDER by rsite";
	//echo $query1;
	$stmt1 = parse_exec_fetch($conn_Infobase, $query1, $error_str,$res1);
	if (!$stmt1){
		die_silently($conn_Infobase, $errr_str);
		exit;
	}else{
		OCIFreeStatement($stmt1);
		$amount=count($res1['RSITE']);
		if ($amount>=1){
			for ($i=0;$i<$amount;$i++) {
				$tg=explode("-",$res1['MO'][$i]);
				$data[$i]['BSC']=strtoupper($res1['BSC'][$i]);
				$data[$i]['TG']=$tg[1];
				$data[$i]['BSC_display']=strtoupper($res1['BSC'][$i]);
				$data[$i]['TG_display']=$tg[1];
				$data[$i]['rsite']=$res1['RSITE'][$i];

				$query6 = "select CABTYPE
				from SWITCH_2G_RXMFP_TRX a LEFT JOIN
				SWITCH_SHELVES b ON
				(trim(substr(RUPOSITION,instr(RUPOSITION, 'SH:')+3,3))=b.SH
				and trim(substr(RUPOSITION,instr(RUPOSITION, 'SH:')+3,3)) !='--')
				OR
				(
				(trim(substr(substr(RUREVISION,1,instr(RUREVISION, '/',1,1)-1),instr(substr(RUREVISION,1,instr(RUREVISION, '/',1,1)-1), 'KRC')+8,4)
				)=b.KRC) and trim(substr(RUPOSITION,instr(RUPOSITION, 'SH:')+3,3)) ='--')
				WHERE BSC='".$res1['BSC'][$i]."' AND MO LIKE'RXOTRX-".$tg[1] ."-%'";
				//echo $query6;
				$stmt6 = parse_exec_fetch($conn_Infobase, $query6, $error_str, $res6);
				if (!$stmt6) {
					die_silently($conn_Infobase, $error_str);
					exit;
				} else {
					OCIFreeStatement($stmt6);
					$data[$i]['CABTYPE']=$res6['CABTYPE'][0];

				}
			}
		}
	}
   	return $data;
}


function get_cell_data($data,$num){
	global $db;

	global $conn_Infobase;

	$query1="select DISTINCT(cell) from SWITCH_2G_RXMOP_TX WHERE BSC='".$data[$num]['BSC']."'
	AND MO LIKE 'RXOTX-".$data[$num]['TG']."-%' order by cell ASC";
	//echo $query1;
	$j=0;
	$stmt1 = parse_exec_fetch($conn_Infobase, $query1, $error_str,$res1);
	if (!$stmt1){
		die_silently($conn_Infobase, $errr_str);
		exit;
	}else{
		OCIFreeStatement($stmt1);
		$data[$num]['TECHNO']=array();
		for ($i=0;$i< count($res1['CELL']);$i++) {
			$cell=$res1['CELL'][$i];
			$data[$num]['celddata'][$j]['cell']=$cell;

			$query2="select BCCHNO, CGI from SWITCH_2G_RLDEP WHERE cell='".$cell."' ";
			$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str,$res2);
			if (!$stmt2){
				die_silently($conn_Infobase, $errr_str);
				exit;
			}else{
				OCIFreeStatement($stmt2);
				if ($res2['BCCHNO'][0]>=975){
					if (!in_array("G9",$data[$num]['TECHNO'])){
						$data[$num]['TECHNO'][]="G9";
					}
				}else{
					if (!in_array("G18",$data[$num]['TECHNO'])){
						$data[$num]['TECHNO'][]="G18";
					}
				}
				$data[$num]['celddata'][$j]['BCCHNO']=$res2['BCCHNO'][0];
				$data[$num]['celddata'][$j]['CGI']=$res2['CGI'][0];

			}
			$l=0;
			$query3="select DCHNO, HSN, SDCCH, CHGR, HOP from SWITCH_2G_RLCFP WHERE cell='".$cell."' order by DCHNO";
			//echo $query3;
			$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str,$res3);
			if (!$stmt3){
				die_silently($conn_Infobase, $errr_str);
				exit;
			}else{
				OCIFreeStatement($stmt3);
				for ($z=0;$z<count($res3['DCHNO']);$z++) {
					$data[$num]['celddata'][$j]['FREQS'][$l]['DCHNO']=$res3['DCHNO'][$z];
					$data[$num]['celddata'][$j]['FREQS'][$l]['HSN']=$res3['HSN'][$z];
					$data[$num]['celddata'][$j]['FREQS'][$l]['SDCCH']=$res3['SDCCH'][$z];
					$data[$num]['celddata'][$j]['FREQS'][$l]['CHGR']=$res3['CHGR'][$z];
					$data[$num]['celddata'][$j]['FREQS'][$l]['HOP']=$res3['HOP'][$z];
					$l++;
				}
			}

			$query4="select CB from SWITCH_2G_RLSBP WHERE BSC='".$data[$num]['BSC']."' AND cell='".$cell."'";
			$stmt4 = parse_exec_fetch($conn_Infobase, $query4, $error_str,$res4);
			if (!$stmt4){
				die_silently($conn_Infobase, $errr_str);
				exit;
			}else{
				OCIFreeStatement($stmt4);
				$data[$num]['celddata'][$j]['CB']=$res4['CB'][0];
			}

			$query4="select STATE from SWITCH_2G_RLSTP_15MIN WHERE cell='".$cell."'";
			//echo $query4.'<br>';
			$stmt4 = parse_exec_fetch($conn_Infobase, $query4, $error_str,$res4);
			if (!$stmt4){
				die_silently($conn_Infobase, $errr_str);
				exit;
			}else{
				OCIFreeStatement($stmt4);
				if ($res4['STATE'][0]==''){
					$data[$num]['celddata'][$j]['STATE']='ACTIVE';
				}else{
					$data[$num]['celddata'][$j]['STATE']=$res4['STATE'][0];
				}
			}

			$query4="select * from SWITCH_HALTEDCELLS WHERE CELLID='".$cell."'";
			$stmt4 = parse_exec_fetch($conn_Infobase, $query4, $error_str,$res4);
			if (!$stmt4){
				die_silently($conn_Infobase, $errr_str);
				exit;
			}else{
				OCIFreeStatement($stmt4);
				if (count($res4['HALTSTART'])!=0){
					$data[$num]['celddata'][$j]['STATEINFO']="From ".$res4['HALTSTART'][0]." till ".$res4['HALTEND'][0]. " with ORQ ".$res4['HALTINGORQ'][0];
				}else{
					$data[$num]['celddata'][$j]['STATEINFO']="";
				}
			}

			$query5="select BAND, MPWR, MO from SWITCH_2G_RXMOP_TX  WHERE BSC='".$data[$num]['BSC']."' AND MO LIKE 'RXOTX-".$data[$num]['TG']."-%' AND cell='".$cell."' ORDER BY substr(MO,-2,2)";
			//echo $query5."<br>";
			$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str,$res5);
			if (!$stmt5){
				die_silently($conn_Infobase, $errr_str);
				exit;
			}else{
				OCIFreeStatement($stmt5);
				$k=0;
				for ($z=0;$z<count($res5['BAND']);$z++) {
					$data[$num]['celddata'][$j]['TRX'][$k]['BAND']=$res5['BAND'][$z];
					$TRX_num=explode("-",$res5['MO'][$z]);
					$TRX_num=$TRX_num[2];

					$query6="select RXD, BAND from SWITCH_2G_RXMOP_RX  WHERE BSC='".strtolower( $data[$num]['BSC'])."' AND MO LIKE 'RXORX-".$data[$num]['TG']."-".$TRX_num."'";
					//echo $query6."<br>";
					$stmt6 = parse_exec_fetch($conn_Infobase, $query6, $error_str,$res6);
					if (!$stmt6){
						die_silently($conn_Infobase, $errr_str);
						exit;
					}else{
					OCIFreeStatement($stmt6);
						$RX=$res6['RXD'][0];
						$BAND=$res6['BAND'][0];

					}


					$query6="select RESULT from SWITCH_2G_RXCDP_TX  WHERE BSC='".$data[$num]['BSC']."' AND MO LIKE 'RXOTX-".$data[$num]['TG']."-".$TRX_num."'";
					//echo $query6."<br>";
					$stmt6 = parse_exec_fetch($conn_Infobase, $query6, $error_str,$res6);
					if (!$stmt6){
						die_silently($conn_Infobase, $errr_str);
						exit;
					}else{
					OCIFreeStatement($stmt6);
						$SIGNAL=$res6['RESULT'][0];
					}

					$query6="select RULOGICALID from SWITCH_2G_RXMFP_TRX  WHERE BSC='".$data[$num]['BSC']."' AND MO LIKE 'RXOTRX-".$data[$num]['TG']."-".$TRX_num."'";
					//echo $query6."<br>";
					$stmt6 = parse_exec_fetch($conn_Infobase, $query6, $error_str,$res6);
					if (!$stmt6){
						die_silently($conn_Infobase, $errr_str);
						exit;
					}else{
					OCIFreeStatement($stmt6);
						$TRX_type=preg_split('/\s+/',trim($res6['RULOGICALID'][0]));
						$TRX_type=$TRX_type[1];
					}

					$data[$num]['celddata'][$j]['TRX'][$k]['SIGNAL']=$SIGNAL;
					$data[$num]['celddata'][$j]['TRX'][$k]['TRXNUM']=$TRX_num;
					$data[$num]['celddata'][$j]['TRX'][$k]['BAND']=$BAND;
					$data[$num]['celddata'][$j]['TRX'][$k]['RX']=$RX;
					$data[$num]['celddata'][$j]['TRX'][$k]['TRXTYPE']=$TRX_type;

					$k++;
				}
			}

			$data[$num]['celddata'][$j]['TRXAMOUNT']=$k;
			$j++;

			$DCHNO="";
		}
	}
	return $data;
}

function get_site_data($data,$num){
	global $db,$conn_Infobase;

	$query6="select TFMODE from SWITCH_2G_RXMOP_TF  WHERE BSC='".$data[$num]['BSC']."' AND MO LIKE 'RXOTF-".$data[$num]['TG']."'";
	//echo $query6."<br>";
	$stmt6 = parse_exec_fetch($conn_Infobase, $query6, $error_str,$res6);
	if (!$stmt6){
		die_silently($conn_Infobase, $errr_str);
		exit;
	}else{
	OCIFreeStatement($stmt6);
		$data[$num]['TFMODE']=$res6['TFMODE'][0];
	}

	$query7="select RULOGICALID  from SWITCH_2G_RXMFP_CF  WHERE BSC='".$data[$num]['BSC']."' AND MO LIKE 'RXOCF-".$data[$num]['TG']."'
	AND RULOGICALID LIKE '%CDU%'";
	//echo $query6."<br>";
	$stmt7 = parse_exec_fetch($conn_Infobase, $query7, $error_str,$res7);
	if (!$stmt7){
		die_silently($conn_Infobase, $errr_str);
		exit;
	}else{
	OCIFreeStatement($stmt7);
		$CDU=preg_split('/\s+/',trim($res7['RULOGICALID'][0]));
		$data[$num]['CDU']=$CDU[1];
	}


	$query7="select RULOGICALID  from SWITCH_2G_RXMFP_CF  WHERE BSC='".$data[$num]['BSC']."' AND MO LIKE 'RXOCF-".$data[$num]['TG']."'
	AND RULOGICALID LIKE '%DXU%'";
	//echo $query6."<br>";
	$stmt7 = parse_exec_fetch($conn_Infobase, $query7, $error_str,$res7);
	if (!$stmt7){
		die_silently($conn_Infobase, $errr_str);
		exit;
	}else{
		OCIFreeStatement($stmt7);
		$DXU=preg_split('/\s+/',trim($res7['RULOGICALID'][0]));
		$data[$num]['DXU']=$DXU[1];
	}

	$query7="select TEI from SWITCH_2G_RXMOP_TEI  WHERE BSC='".$data[$num]['BSC']."' AND MO LIKE 'RXOCF-".$data[$num]['TG']."'";
	//echo $query7."<br>";
	$stmt7 = parse_exec_fetch($conn_Infobase, $query7, $error_str,$res7);
	if (!$stmt7){
		die_silently($conn_Infobase, $errr_str);
		exit;
	}else{
		OCIFreeStatement($stmt7);
		$data[$num]['TEI']=$res7['TEI'][0];
	}


	$query8="select * from SWITCH_2G_RXAPP_TG  WHERE BSC='".$data[$num]['BSC']."' AND MO LIKE 'RXOTG-".$data[$num]['TG']."' ORDER BY DEV,MO";
	//echo $query8."<br>";
	$stmt8 = parse_exec_fetch($conn_Infobase, $query8, $error_str,$res8);
	if (!$stmt8){
		die_silently($conn_Infobase, $errr_str);
		exit;
	}else{
		OCIFreeStatement($stmt8);
		$j=0;

		for ($z=0;$z<count($res8['TEI']);$z++){

			$ETRBLT_t=explode("-",$res8['DEV'][$z]);
			$ETRBLT=floor($ETRBLT_t[1]/32);

			$data[$num]['ABIS'][$j]['TEI']=$res8['TEI'][0];
			$data[$num]['ABIS'][$j]['ETRBLT']=$ETRBLT;
			$data[$num]['ABIS'][$j]['RBLT']=$ETRBLT_t[1];
			$data[$num]['ABIS'][$j]['DCP']=$res8['DCP'][$z];
			$data[$num]['ABIS'][$j]['TEI']=$res8['TEI'][$z];
			$data[$num]['ABIS'][$j]['SIGNALLING']=$res8['APUSAGE'][$z];

			$j++;
		}
	}

	return $data;
}

function get_repeater_data($data,$num){
	global $conn_Infobase;
/*
	$sql =  " select c.idname as SITEID,
             a.idname as CELLID,
             b.idname as REPEATER_ID
		 from gsmcell@BASEPRO7 a, repeater@BASEPRO7 b, cellsites@BASEPRO7 c
		 where
		 b.cellkey = a.cellkey
		 and a.SITEKEY = c.sitekey
		 and c.idname = '".$data[$num]['site']."'
		 order by a.idname asc ";
	//echo $sql;
	$stmt = oci_parse($conn_Infobase, $sql);
	oci_execute($stmt);
	$i=0;
	while (ocifetch($stmt)){
		$data[$num]['REPEATER'][$i]= ociresult($stmt, "CELLID"). ": ".ociresult($stmt, "REPEATER_ID");
		$i++;
	}
*/
	return $data;
}

//Ericsson
function get_3G_data($siteID){
	global $conn_Infobase;

	$query =  " select * from SWITCH_3G_RBS WHERE SITE LIKE '%".$siteID."%' ORDER BY SITE ASC";
	echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$amount_RBS=count($res1['SITE']);

		if ($amount_RBS>=1){
			for ($i=0;$i<$amount_RBS;$i++) {
				$data[$i]['RBS']=$res1['SITE'][$i];
				$query2 =  " select RBSTYPE from SWITCH_3G_RBSTYPE WHERE SITE LIKE '%".$res1['SITE'][$i]."%' ORDER BY SITE ASC";
				//echo $query;
				$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
				if (!$stmt2){
					die_silently($conn_Infobase, $error_str);
					exit;
				}else{
					OCIFreeStatement($stmt2);
					$amount_RBSTYPE=count($res2['RBSTYPE']);
					if ($amount_RBS>=1){
						for ($j=0;$j<$amount_RBS;$j++) {
							$data[$i]['RBSTYPE'][$j]=$res2['RBSTYPE'][$j];
						}
					}
				}
			}

			for ($i=0;$i<$amount_RBS;$i++) {
				$query3 =  " select RNC from SWITCH_3G_RNCMODULE WHERE SITE LIKE '%".$res1['SITE'][$i]."%' ORDER BY SITE ASC";
				//echo $query3;
				$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
				if (!$stmt3){
					die_silently($conn_Infobase, $error_str);
					exit;
				}else{
					OCIFreeStatement($stmt3);
					$amount_RNC=count($res3['RNC']);
					if ($amount_RNC>=1){
							$data[$i]['RNC']=$res3['RNC'][0];
					}
				}
			}


			for ($i=0;$i<$amount_RBS;$i++) {

				$cellpart=ltrim(substr($res1['SITE'][$i],2,-2),'0');
				$query5 =  " select CELL from SWITCH_3G_CELLPERRNC WHERE CELL = '".$cellpart."7' OR CELL = '".$cellpart."8' OR CELL = '".$cellpart."9' ORDER BY CELL ASC";
				//echo $query5;
				$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str,$res5);
				if (!$stmt5){
					die_silently($conn_Infobase, $errr_str);
					exit;
				}else{
					OCIFreeStatement($stmt5);
					$amount_CELLS=count($res5['CELL']);
					if ($amount_CELLS>=1){
						for ($j=0;$j<$amount_CELLS;$j++) {
							$data[$i]['CELLS'][$j]=$res5['CELL'][$j];
							$query4 =  " select SCRAMBLINGCODE from SWITCH_3G_CELLSCRAMBLING WHERE CELL = '".$res5['CELL'][$j]."'";
							//echo $query4."<br>";
							$stmt4 = parse_exec_fetch($conn_Infobase, $query4, $error_str,$res4);
							if (!$stmt4){
								die_silently($conn_Infobase, $errr_str);
								exit;
							}else{
								OCIFreeStatement($stmt4);
								$amount_CODE=count($res4['SCRAMBLINGCODE']);
								if ($amount_CODE>=1){
									$data[$i][$res5['CELL'][$j]]['CODE']=$res4['SCRAMBLINGCODE'][0];
								}
							}

							$query6 =  " select STATE2 from SWITCH_3G_HSPX WHERE CELL = '".$res5['CELL'][$j]."'";
							//echo $query6;
							$stmt6 = parse_exec_fetch($conn_Infobase, $query6, $error_str, $res6);
							if (!$stmt6){
								die_silently($conn_Infobase, $error_str);
								exit;
							}else{
								OCIFreeStatement($stmt6);
								$amount_STATE2=count($res6['STATE2']);
								if ($amount_STATE2>=1){
									$data[$i][$res5['CELL'][$j]]['HSPX']='yes';
								}else{
									$data[$i][$res5['CELL'][$j]]['HSPX']='no';
								}
							}

						}
					}
				}
			}
		}//END if ($amount_RBS>=1){
	}
return $data;
}

function get_3GZTE_data($siteID){
	global $conn_Infobase;
	$data=array();
	$query =  "select USERLABEL, URRNCFUNCTION as OMCRNCID,TECHNO, SUBSTR(UUTRANCELLFDD,0,4) AS NODEBNO  from SWITCH_3GZTE_UUTRANCELLFDD WHERE USERLABEL LIKE '%".$siteID."%' ORDER BY USERLABEL";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);

		for ($i=0;$i< count($res1['USERLABEL']);$i++) {
			$techno=$res1['TECHNO'][$i];
			$data[$techno]['RBS'][$i]=$res1['USERLABEL'][$i];
			$NODEBNO=$res1['NODEBNO'][$i];
			$data[$techno]['RNC']=$res1['OMCRNCID'][$i];

			$query =  "select MAX(ENDTIME),CELLNAME, SERVICETIMEOFCELL, STATUS from SWITCH_3GZTE_CELLSERVICE_15MIN WHERE CELLNAME LIKE '".$res1['USERLABEL'][$i]."%' GROUP BY CELLNAME,SERVICETIMEOFCELL,STATUS";
			//echo $query;
			$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
			if (!$stmt2){
				die_silently($conn_Infobase, $error_str);
				exit;
			}else{
				OCIFreeStatement($stmt2);
				$data[$techno]['STATE'][$i]=$res2['STATUS'][0];
			}

			$query4="select * from SWITCH_HALTEDCELLS WHERE CELLID='".$res1['USERLABEL'][$i]."'";
			//echo $query4."<br>";
			$stmt4 = parse_exec_fetch($conn_Infobase, $query4, $error_str,$res4);
			if (!$stmt4){
				die_silently($conn_Infobase, $errr_str);
				exit;
			}else{
				OCIFreeStatement($stmt4);
				if (count($res4['HALTSTART'])!=0){
					$data[$techno]['STATEINFO'][$i]="From ".$res4['HALTSTART'][0]." till ".$res4['HALTEND'][0]. " with ORQ ".$res4['HALTINGORQ'][0];
				}else{
					$data[$techno]['STATEINFO'][$i]="";
				}
			}
		}
	}

	$query =  "select SLOTNO, USERLABEL from SWITCH_3GZTE_PLUGINUNIT WHERE RACKNO = '".$NODEBNO."' ORDER BY SLOTNO ASC";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
 	if(!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$j=0;
		for ($i=0;$i< count($res1['SLOTNO']);$i++) {
			$data[$techno]['SLOTS'][$j]['PRODNAME']=$res1['USERLABEL'][$i];
			$data[$techno]['SLOTS'][$j]['SLOTNO']=$res1['SLOTNO'][$i];
			$j++;
		}
	}

	return $data;
}

function get_4GZTE_data($siteID){
	global $conn_Infobase;
	$data=array();
	$query =  "select ULABEL,TECHNO,MEID,EUTRANCELLFDD from SWITCH_4GZTE_UTRANCELL WHERE ULABEL LIKE '%".$siteID."%' ORDER BY ULABEL";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);

		for ($i=0;$i<count($res1['TECHNO']);$i++) {

			$techno=$res1['TECHNO'][$i];
	
			$data['TECHNOS'][$techno]['RBS'][$i]=$res1['ULABEL'][$i];

			$query =  "select MAX(ENDTIME),CELLNAME, SERVICETIMEOFCELL, STATUS from SWITCH_4GZTE_CELLSERVICE_15MIN WHERE CELLNAME LIKE '%".$res1['ULABEL'][$i]."%' GROUP BY CELLNAME,SERVICETIMEOFCELL, STATUS";
			//echo $query;
			$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
			if (!$stmt2){
				die_silently($conn_Infobase, $error_str);
				exit;
			}else{
				OCIFreeStatement($stmt2);
				$data['TECHNOS'][$techno]['STATE'][$i]=$res2['STATUS'][0];
			}

			if ($i==0){				
				$query =  "select DESCRIPTION from SWITCH_4GZTE_BPDEVICESET WHERE MEID='".$res1['MEID'][0]."'";		
				//echo $query;							
				$stmtBP = parse_exec_fetch($conn_Infobase, $query, $error_str, $resBP);
				if (!$stmtBP){
					die_silently($conn_Infobase, $error_str);
					exit;
				}else{
					OCIFreeStatement($stmtBP);
					for ($j=0;$j< count($resBP['DESCRIPTION']);$j++) {
						$data['BPLPORTS'][$j]=$resBP['DESCRIPTION'][$j];
					}
				}
			}
			

			$query =  "select MIMO,RU from SWITCH_4GZTE_ECELLEQUIPFUNC WHERE ECELLEQUIPFUNC='".$res1['EUTRANCELLFDD'][$i]."' AND MEID='".$res1['MEID'][$i]."'";
			//echo $query."<br>";			
			$stmtEQ = parse_exec_fetch($conn_Infobase, $query, $error_str, $resEQ);
			if (!$stmtEQ){
				die_silently($conn_Infobase, $error_str);
				exit;
			}else{
				OCIFreeStatement($stmtEQ);

				$data['TECHNOS'][$techno]['CELLEQ'][$res1['CELLLOCALID'][$i]]['MIMO']=$resEQ['MIMO'][0];
				$data['TECHNOS'][$techno]['CELLEQ'][$res1['CELLLOCALID'][$i]]['RU']=$resEQ['RU'][0];				
			}

			$prev_techno=$res1['TECHNO'][$i];			
		}
		
	}
		
	return $data;
}
?>
<?PHP
include_once('/var/www/html/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


function getStatusInfo($viewtype){
	if ($viewtype=="FUNDHIST" or $viewtype=="FUND"){ //HISTORY VIEW
		$ret['viewtype']="FUND";
		$ret['color']="BSDS_funded";
		if ($viewtype=="FUNDHIST"){
			$ret['viewhistory']="yes";
		}else{
			$ret['viewhistory']="no";
		}
		$ret['status']="BSDS FUNDED";
	}else if ($viewtype=="POSTHIST" or $viewtype=="POST"){//HISTORY VIEW
		$ret['viewtype']="POST";
		if ($viewtype=="POSTHIST"){
			$ret['viewhistory']="yes";
		}else{
			$ret['viewhistory']="no";
		}
		$ret['color']="SITE_funded";
		$ret['status']="SITE FUNDED";
	}else if ($viewtype=="BUILDHIST" or $viewtype=="BUILD"){//HISTORY VIEW
		$ret['viewtype']="BUILD";
		if ($viewtype=="BUILDHIST"){
			$ret['viewhistory']="yes";
		}else{
			$ret['viewhistory']="no";
		}
		$ret['color']="BSDS_asbuild";
		$ret['status']="BSDS AS BUILD";
	}else if ($viewtype=="PRE" or $viewtype=="PREHIST" or $viewtype=="PRE READY TO BUILD"){ //PRE VIEW
		$ret['viewtype']="PRE";
		if ($viewtype=="PREHIST"){
			$ret['viewhistory']="yes";
		}else{
			$ret['viewhistory']="no";
		}
		$ret['color']="BSDS_preready";
		$ret['status']="PRE READY TO BUILD";
	}else if ($viewtype=="PRE READY TO BUILD"){ 
		$ret['viewtype']="PRE";
		$ret['viewhistory']="no";
		$ret['status']="PRE READY TO BUILD";
		$ret['color']="BSDS_preready";
	}
	return $ret;
}
function check_current_exists($band,$BSDSKEY,$BSDS_BOB_REFRESH,$sector,$donor,$lognode,$view){
	global $conn_Infobase;

	if ($band=='G9' or $band=='G18'){
		$tabletype="GSM";
		if ($view=="FUND"){
			$key="BSDSKEY";
			$val=$BSDSKEY;
		}else{
			$key="sitekey";
			$val=$lognode;
		}
	}else if ($band=='U21' or $band=='U9'){ 
		$tabletype="UMTS";
		if ($view=="FUND"){
			$key="BSDSKEY";
			$val=$BSDSKEY;
		}else{
			$key="lognodepk";
			$val=$lognode;
		}
	}else if ($band=='L18' or $band=='L26' or $band=='L8'){ 
		$tabletype="LTE";
		if ($view=="FUND"){
			$key="BSDSKEY";
			$val=$BSDSKEY;
		}else{
			$key="lognodepk";
			$val=$lognode;
		}
	}	
	if ($view!="FUND"){ //Only for FUNDED a record is existing, else we look at the PRE
		$view="PRE";
	}
	if ($donor==""){		
		if($sector=="allsec" or $sector==""){
			$query = "SELECT count(".$key.") AS AMOUNT FROM BSDS_CU_".$tabletype."2 WHERE ".$key."='".$val."' AND STATUS='".$view."' AND TECHNO='".$band."'";
		}else if($sector!='' or $sector=='all'){
			$query = "SELECT count(".$key.") AS AMOUNT FROM BSDS_CU_".$tabletype."_SEC2 WHERE ".$key."='".$val."' AND STATUS='".$view."' AND TECHNO='".$band."'";
			if ($sector!='all'){
				$query .= " AND SECT='".$sector."'";
			}
		}		
	}else{ //REPEATER BSDS
		$query = "SELECT count(SITEKEY) AS AMOUNT FROM BSDS_CU_REP_".$tabletype." WHERE SITEKEY='".$lognode."' AND STATUS='".$view."' AND TECHNO='".$band."'";
	}
	if ($view=="FUND"){
		$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
	}
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$total_records=$res1['AMOUNT'][0];
		return $total_records;
	}
}
/*********************************************************************************************************************/
function check_planned_exists($BSDSKEY,$BSDS_BOB_date,$band,$sec,$view,$donor)
{
	global $conn_Infobase;
	if ($band=='G9' or $band=='G18'){
		$tabletype="GSM";
	}else if ($band=='U21' or $band=='U9'){ 
		$tabletype="UMTS";
	}else if ($band=='L18' or $band=='L26'  or $band=='L8'){ 
		$tabletype="LTE";
	}	
	if ($donor==''){	
		if($sec=="allsec" or $sec==""){
			$query = "SELECT BSDSKEY FROM BSDS_PL_".$tabletype." WHERE BSDSKEY='".$BSDSKEY."' AND STATUS='".$view."' AND TECHNO='".$band."'";
			if ($view!="PRE"){
				$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_date."')";
			}
		}else if($sec!=''){
			$query = "SELECT BSDSKEY  FROM BSDS_PL_".$tabletype."_SEC WHERE BSDSKEY='".$BSDSKEY."' AND STATUS='".$view."' AND TECHNO='".$band."' AND SECT='".$sec."'";
			if ($view!="PRE"){
				$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_date."')";
			}
		}			
	}else{ //for repeaters	
		$query = "SELECT BSDSKEY FROM BSDS_PL_REP_".$tabletype." WHERE BSDSKEY= '".$BSDSKEY."' AND STATUS='".$view."' AND TECHNO='".$band."'";
		if ($view!="PRE"){
			$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_date."')";
		}
	}
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$Count=count($res1['BSDSKEY']);
	}
	//echo $Count;
	if ($Count>1){		
		return 'error';
	}else if ($Count==1){	
		return '1';
	}else{
		return '0';
	}
}
/*********************************************************************************************************************/
function get_BSDS_GENERALINFO($BSDSKEY){
	global $conn_Infobase;
	$query = "SELECT TEAML_APPROVED, CHANGE_DATE, SITEKEY, SITEID, UPDATE_AFTER_COPY, 
	UPDATE_BY_AFTER_COPY FROM BSDS_GENERALINFO2 WHERE BSDSKEY= '".$BSDSKEY."'";
	//echo "$query<br>";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	return $res1;
}
/*********************************************************************************************************************/
function get_data($type,$sect,$what,$viewtype,$BSDSKEY,$BSDS_BOB_REFRESH,$donor,$lognode){
	global $conn_Infobase, $config;


	if ($type=="G9" || $type=="G18"){
		$tabletype="GSM";
		if ($viewtype=="FUND"){
			$key="BSDSKEY";
			$val=$BSDSKEY;
		}else{
			$key="sitekey";
			$val=$lognode;
		}
	}else if ($type=="U21" || $type=="U9"){
		$tabletype="UMTS";
		$key="lognodepk";
		if ($viewtype=="FUND"){
			$key="BSDSKEY";
			$val=$BSDSKEY;
		}else{
			$key="lognodepk";
			$val=$lognode;
		}
	}else if ($type=="L18" || $type=="L26" || $type=="L8"){
		$tabletype="LTE";
		if ($viewtype=="FUND"){
			$key="BSDSKEY";
			$val=$BSDSKEY;
		}else{
			$key="lognodepk";
			$val=$lognode;
		}
	}
	if ($what=="FEEDERSHARE_PLANNED"){
		$query = "SELECT * FROM BSDS_PL WHERE BSDSKEY='".$BSDSKEY."' AND STATUS='".$viewtype."'";
		if ($viewtype!="PRE"){
			$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
		}			
	}else if ($what=="FEEDERSHARE_CURRENT"){
		$query = "SELECT * FROM BSDS_CU WHERE BSDSKEY='".$BSDSKEY."' AND STATUS='".$viewtype."'";
		if ($viewtype=="FUND"){
			$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
		}
	}else if ($what=="CURRENT_ASSET"){
		if ($type=="G9"){
			$query = "SELECT * FROM ".$config['table_asset_BSDSinfo']." WHERE SITEKEY='".$lognode."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%900%' ORDER BY SECTORID,AZIMUTH";
		}else if ($type=="G18"){
			$query = "SELECT * FROM ".$config['table_asset_BSDSinfo']." WHERE SITEKEY='".$lognode."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%1800%' ORDER BY SECTORID,AZIMUTH";
		}else if ($type=="U21" || $type=="U9"){
			$query = "select DISTINCT * from ".$config['table_asset_UMTSinfo']." WHERE LOGNODEFK='".$lognode."' AND UMTSCELLID LIKE '%1' ORDER BY UMTSCELLID";
		}else if ($type=="L18" || $type=="L26" || $type=="L8"){
			$query = "SELECT DISTINCT * from LTE1 WHERE LOGNODEFK='".$lognode."' ORDER BY UMTSCELLID ASC, INDEXNO DESC";
		}
		//echo $type."mmmmmmmmmmmm".$query."<br>";
	}else if ($what=="CURRENT_EXISTING"){		
		if ($donor==""){
			$table="BSDS_CU_".$tabletype."2";
		}else{
			$key="sitekey";
			$table="BSDS_CU_REP_".$tabletype."";
		}
		if ($viewtype=="BUILD"){
			$query="SELECT BSDS_BOB_REFRESH FROM VW_STATUS_MAXBOB_FORASBUILD WHERE BSDSKEY='".$BSDSKEY."'";
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt){
				die_silently($conn_Infobase, $error_str);
				exit;
			}else{
				OCIFreeStatement($stmt);
				$total_records=count($res1['BSDS_BOB_REFRESH']);
				if ($total_records==1){
					$BSDS_BOB_REFRESH=$res1['BSDS_BOB_REFRESH'][0];
					/* IMPORTANT !!!!!!!! */
					$viewtype="FUND";
					//echo $BSDS_BOB_REFRESH;
				}else{
					echo "BSDS AS BUILD ERROR";
					die;
				}
			}
		}
		if ($viewtype!="FUND"){ //Only for FUNDED a record is existing, else we look at the PRE
			$viewtype="PRE";
		}

		if ($sect==""){
			$query = "SELECT * FROM ".$table." WHERE ".$key."='".$val."' AND STATUS='".$viewtype."' AND TECHNO='".$type."'";		
		}else{
			//for donor we don't have sector data
			$query = "SELECT * FROM BSDS_CU_".$tabletype."_SEC2 WHERE ".$key."='".$val."' AND STATUS='".$viewtype."' AND TECHNO='".$type."'";
			if ($sect!="all"){
				$query .= "AND SECT='".$sect."'";
			}
		}		
		if ($viewtype=="FUND"){
			$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
		}
		if ($sect!=""){
			$query .=" ORDER BY SECT";
		}
		//echo $query."<br>";
	}else if ($what=="RNCNODEB_ASSET"){
		$query = "SELECT * FROM VW_ASSET_RNC_NODEB WHERE LOGNODEPK='".$lognode."'";
	}else if ($what=="PLANNED"){
		if ($donor==""){
			if ($sect==""){
				$query = "SELECT * FROM BSDS_PL_".$tabletype." WHERE BSDSKEY='".$BSDSKEY."' AND STATUS='".$viewtype."' AND TECHNO='".$type."'";
				if ($viewtype!="PRE"){
					$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
				}
			}else if($sect=="all"){
				$query = "SELECT * FROM BSDS_PL_".$tabletype."_SEC WHERE BSDSKEY='".$BSDSKEY."' AND STATUS='".$viewtype."'  AND TECHNO='".$type."'";
				if ($viewtype!="PRE"){
					$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
				}
				$query .=" ORDER BY SECT";
			
			}else{
				$query = "SELECT * FROM BSDS_PL_".$tabletype."_SEC WHERE BSDSKEY='".$BSDSKEY."' AND STATUS='".$viewtype."'  AND TECHNO='".$type."'AND SECT='".$sect."'";
				if ($viewtype!="PRE"){
					$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
				}
			}
			//echo $query;
		}else{
			$query = "SELECT * FROM BSDS_PL_REP_".$tabletype." WHERE BSDSKEY= '".$BSDSKEY."' AND TECHNO='".$type."' AND STATUS='".$viewtype."'";
			if ($viewtype!="PRE"){
				$query .=" AND BSDS_BOB_REFRESH=to_date('".$BSDS_BOB_REFRESH."')";
			}
		}
	}
	//echo "<font color=red>$what=:$query</font><br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
	    OCIFreeStatement($stmt);
	}
	return $res1;
}
/***********************************/
function get_cols($table){
	global $conn_Infobase;
	$query="SELECT column_name
	FROM USER_TAB_COLUMNS
	WHERE table_name = '".$table."' ORDER BY column_name";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
	    OCIFreeStatement($stmt);
	}
	return $res1;
}
/*********************************************************************************************************************/
function get_config($stat,$type){
	global $conn_Infobase;

	if ($type=="U21" or $type=="U9"){
		$query = "select * from umtsbsds6 WHERE CELL_ID='".$stat."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
		  die_silently($conn_Infobase, $error_str);
		  exit;
		} else {
		  OCIFreeStatement($stmt);
		}

		if (strtoupper($res1['FLAGID'][0])=="FREQPLANNING_WORKPROGRESS"){
			return "FREQ. WP.";
		}else if(strtoupper($res1['FLAGID'][0])=="PARAMETERS_RELEASED"){
			return "PAR. REL.";
		}else{
			return $res1['FLAGID'][0];
		}
	}else if ($type=="L18" or $type=="L26" || $type=="L8"){
			$query = "select * from LTESTATUS WHERE IDNAME='".$stat."'";
			//echo $query;
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
			  die_silently($conn_Infobase, $error_str);
			  exit;
			} else {
			  OCIFreeStatement($stmt);
			}

			if (strtoupper($res1['FLAGID'][0])=="FREQPLANNING_WORKPROGRESS"){
				return "FREQ. WP.";
			}else if(strtoupper($res1['FLAGID'][0])=="PARAMETERS_RELEASED"){
				return "PAR. REL.";
			}else{
				return $res1['FLAGID'][0];
		}

	}else if ($type=="G9" or $type=="G18"){
		
		if ($stat=='179736311'){   		$CONFIG='-'; }
			else if ($stat=='179736312'){  	$CONFIG='ACTIVE'; }
			else if ($stat=='1567764043'){   	$CONFIG='FREQPL_WORKPR'; }
			else if ($stat=='1568107250'){   	$CONFIG='HALTED'; }
			else if ($stat=='179736313'){   	$CONFIG='PAR_REL'; }
			else if ($stat=='415009229'){   	$CONFIG='PLANNED'; }
			else if ($stat=='415009230'){   	$CONFIG='STUDY'; }
			else if ($stat==''){   			$CONFIG='UNKNOWN'; }

		return $CONFIG;
	}
}
/*********************************************************************************************************************/
function get_freq($cell){

	global $conn_Infobase;

	$query1 = "Select CELL,count(MO) as amount,SUBSTR(
		MO,
		INSTR(MO, '-', 1, 1)+ 1,
		INSTR(MO, '-', 1, 2)- INSTR(MO, '-', 1, 1)- 1
	) AS TG from switch_2G_rxmop_tx WHERE cell LIKE '".$cell."%' 
	GROUP BY CELL,
	SUBSTR(
		MO,
		INSTR(MO, '-', 1, 1)+ 1,
		INSTR(MO, '-', 1, 2)- INSTR(MO, '-', 1, 1)- 1
	) ORDER BY TG,CELL";
	//echo $query1."<hr>";
	$stmt1 = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
	if (!$stmt1) {
		die_silently($conn_Infobase, $error_str);
		exit;
	} else {
		OCIFreeStatement($stmt1);
		$start=1;
		$k=1;
		for ($i = 0; $i < count($res1['TG']); $i++) {
			$TG=$res1['TG'][$i];
			if ($TG!=$previousTG && $start!=1){
				$k++;
			}else{
				$start=0;
			}
			$cell=$res1['CELL'][$i];
			$freq[$k][$cell]=$res1['AMOUNT'][$i];
			$previousTG=$TG;
		}
	}
	//echo "<pre>".print_r($freq,true)."</pre>";
	return $freq;
}
/*******************************************************************************************************/
function get_cabinettype($type,$site){

	global $conn_Infobase;

	if ($type=="G9"){
		$query3 = "select REPLACE(MO,'RXOTG-','') AS MO, BSC from SWITCH_2G_RXMOP_TG WHERE rsite like '%".$site."E%'
		OR rsite like '%".$site."HE%'";
	}else if ($type=="G18"){
		$query3 = "select REPLACE(MO,'RXOTG-','') AS MO, BSC from SWITCH_2G_RXMOP_TG WHERE rsite not like '%".$site."E%'
		and rsite not like '%".$site."HE%' and rsite like '%".$site."%'";
	}
	//echo $query3."<hr>";;
	$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
	if (!$stmt3) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt3);
		$cab['number']=count($res3['MO']);
		for ($i = 0; $i < count($res3['MO']); $i++) {
			$TG=$res3['MO'][$i];
			$BSC=$res3['BSC'][$i];

			if ($BSC!="" && $TG!=""){
				$query4 = "select CABTYPE
				from SWITCH_2G_RXMFP_TRX a LEFT JOIN
				SWITCH_SHELVES b ON
				(trim(substr(RUPOSITION,instr(RUPOSITION, 'SH:')+3,3))=b.SH
				and trim(substr(RUPOSITION,instr(RUPOSITION, 'SH:')+3,3)) !='--')
				OR
				(
				(trim(substr(substr(RUREVISION,1,instr(RUREVISION, '/',1,1)-1),instr(substr(RUREVISION,1,instr(RUREVISION, '/',1,1)-1), 'KRC')+8,4)
				)=b.KRC) and trim(substr(RUPOSITION,instr(RUPOSITION, 'SH:')+3,3)) ='--')
				WHERE BSC='".$BSC."' AND MO LIKE'RXOTRX-".$TG ."-%'";
				//echo $query4;
				$stmt4 = parse_exec_fetch($conn_Infobase, $query4, $error_str, $res4);
				if (!$stmt4) {
					die_silently($conn_Infobase, $error_str);
					exit;
				} else {
					OCIFreeStatement($stmt4);
					$cab['type']=$res4['CABTYPE'][0];
				}

				$query7="select RULOGICALID  from SWITCH_2G_RXMFP_CF  WHERE BSC='".$BSC."' AND MO LIKE 'RXOCF-".$TG."'
				AND RULOGICALID LIKE '%CDU%'";
				//echo $query6."<br>";
				$stmt7 = parse_exec_fetch($conn_Infobase, $query7, $error_str,$res7);
				if (!$stmt7){
					die_silently($conn_Infobase, $errr_str);
					exit;
				}else{
					OCIFreeStatement($stmt7);
					$CDU=preg_split('/\s+/',trim($res7['RULOGICALID'][0]));
					$CDU1=$CDU[1];
					if ($CDU1=='CDU_A1'){
						$cab['CDU']='CDU_A';
					}else if ($CDU1=='CDUC+1'){
						$cab['CDU']='CDU_C+';
					}else if ($CDU1=='CDU_G'){
						$cab['CDU']='CDU_G';
					}else if ($CDU1=='CDU_G9'){
						$cab['CDU']='CDU_G';
					}else if ($CDU1=='CDU_G18'){
						$cab['CDU']='CDU_G';
					}else {
						$cab['CDU']=$CDU1;
	 				}
				}
			}else{
				$cab['type']="Unknown";
				$cab['number']="Unknown";
			}
		}
	}
	//echo "<pre>".print_r($cab,true)."</pre>";
	return $cab;
}
/*********************************************************************************************************************/
function get_TRU_data($cell){

	global $conn_Infobase;
	
	$query1="SELECT
		CELL,
		TRIM(SUBSTR(RUREVISION, 0, 14)) AS KRC,
		COUNT(A .MO)AS MOCOUNT,
		TRUTYPE,
		SUBSTR(
			A .MO,
			INSTR(A .MO, '-', 1, 1)+ 1,
			INSTR(A .MO, '-', 1, 2)- INSTR(A .MO, '-', 1, 1)- 1
		)AS TG
	FROM
		SWITCH_2G_RXMFP_TRX A
	LEFT JOIN SWITCH_SHELVES_TRU b ON TRIM(SUBSTR(RUREVISION, 0, 14))= b.KRC
	LEFT JOIN switch_2g_rxmop_tx c ON SUBSTR(A .MO, INSTR(A .MO, '-', 1, 1) + 1)= SUBSTR(c.MO, INSTR(c.MO, '-', 1, 1) + 1)
	AND A .BSC = c.BSC
	WHERE
		CELL LIKE '".$cell."'
	GROUP BY
		CELL,
		TRUTYPE,
		SUBSTR(
			A .MO,
			INSTR(A .MO, '-', 1, 1)+ 1,
			INSTR(A .MO, '-', 1, 2)- INSTR(A .MO, '-', 1, 1)- 1
		),TRIM(SUBSTR(RUREVISION, 0, 14))
	ORDER BY
		TG,
		CELL,
		SUBSTR(
			A .MO,
			INSTR(A .MO, '-', 1, 1)+ 1,
			INSTR(A .MO, '-', 1, 2)- INSTR(A .MO, '-', 1, 1)- 1
		)";
//echo $query1;

$stmt1 = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt1) {
	die_silently($conn_Infobase, $error_str);
	exit;
} else {
	$j=0;
	$k=1;
	$start=1;
	for ($i=0;$i< count($res1['CELL']);$i++) {

		$cell=$res1['CELL'][$i];
		$TG=$res1['TG'][$i];

		if ($TG!=$previousTG && $start!=1){
			$k++;
		}

		if ($cell!=$previousCell){
			$j=0;
			$start=0;
		}

		if ($res1['TRUTYPE'][$i]=="EDTRU" || $res1['TRUTYPE'][$i]=="DTRU"){
			$MO=ceil($res1['MOCOUNT'][$i]/2);
		}else{
			$MO=$res1['MOCOUNT'][$i];
		}
		$TRU_data[$k][$cell]['TRUTYPE'][$j]=$res1['TRUTYPE'][$i];
		$TRU_data[$k][$cell]['MO'][$j]=$MO;

		$previousCell=$cell;
		$previousTG=$TG;
		$j++;
	}
}
//echo "<pre>".print_r($TRU_data,true)."</pre>"; 
return $TRU_data;
}
/*********************************************************************************************************************/
function get_select_BBS($BBS){
	if ($BBS!=""){
		$option.= "<option selected>".$BBS."</option>";
	}
	$option.= "<option>NONE</option>";
	if ($BBS!="BBS 2202"){
		$option.= "<option>BBS 2202</option>";
	}
	if ($BBS!="Existing"){
		$option.= "<option>Existing</option>";
	}
	if ($BBS!="BBS 2000"){
		$option.= "<option>BBS 2000</option>";
	}
	if ($BBS!="PBC 02"){
		$option.= "<option>PBC 02</option>";
	}
	if ($BBS!="PBC 2302"){
		$option.= "<option>PBC 2302</option>";
	}
	if ($BBS!="BBS 4500"){
		$option.= "<option>BBS 4500</option>";
	}

	//if ($BBS!="NONE"){
		$option.= "<option>NONE</option>";
	//}
	return $option;
}
/*********************************************************************************************************************/
function get_select_DXU($DXU){
	$option.= "<option selected>$DXU</option>";
	if ($DXU!="NONE" && $DXU!=""){
		$option.= "<option value='NONE'>NONE</option>";
	}
	if ($DXU!="DXU 01"){
		$option.= "<option>DXU 01</option>";
	}
	if ($DXU!="DXU 03"){
		$option.= "<option>DXU 03</option>";
	}
	if ($DXU!="DXU 11"){
		$option.= "<option>DXU 11</option>";
	}
	if ($DXU!="DXU 21A"){
		$option.= "<option>DXU 21A</option>";
	}
	if ($DXU!="DXU 2302"){
		$option.= "<option>DXU 2302</option>";
	}
	if ($DXU!="DXU 2109"){
		$option.= "<option>DXU 2109</option>";
	}
	if ($DXU!="DXU 23"){
		$option.= "<option>DXU 23</option>";
	}
	if ($DXU!="DXU 2308"){
		$option.= "<option>DXU 2308</option>";
	}
	return $option;
}
/*********************************************************************************************************************/
function get_select_numbers($selected,$min,$max,$steps,$trail){
	echo "<option selected>$selected";
	echo "<option value=''></option>";
	for ($i = $min; $i <= $max; $i=$i+$steps) {
		if ($selected!="$i"){
			if (strlen($i)<2 && $trail=="yes"){
				echo "<option>0".$i."</option>";
			}else{
				echo "<option>$i</option>";
			}
		}
	}
	if ($trail=="FS5"){
		echo "<option>FS5</option>";
	}
	echo "</select>";
}
/*********************************************************************************************************************/
function get_select_CDU($CDU){
	echo "<option selected>$CDU</option>";
	/*
	if ($CDU!="CDU_C"){
		echo "<option>CDU_C</option>";
	}*/
	if ($CDU!="CDU_C+"){
		echo "<option>CDU_C+</option>";
	}
	if ($CDU!="CDU_A"){
		echo "<option>CDU_A</option>";
	}
	/*
	if ($CDU!="CDU_C+ E"){
		echo "<option>CDU_C+ E</option>";
	}*/
	if ($CDU!="CDU_M"){
		echo "<option>CDU_M</option>";
	}
	if ($CDU!="CDU_G"){
		echo "<option>CDU_G</option>";
	}
	if ($CDU!="CDU_T"){
		echo "<option>CDU_T</option>";
	}
	if ($CDU!="CDU_F"){
		echo "<option>CDU_F</option>";
	}
	if ($CDU!="CDU_N"){
		echo "<option>CDU_N</option>";
	}
	/*
	if ($CDU!="CDU_D"){
		echo "<option>CDU_D</option>";
	}*/
	if ($CDU!="NONE" && $CDU!=""){
		echo "<option value=''>NONE</option>";
	}
}
/*********************************************************************************************************************/
function get_select_TMA($TMA){

		$option.= "<option selected>$TMA</option>";

	if ($TMA!="NONE" && $TMA!=""){
		$option.= "<option value='NONE'>NONE</option>";
	}
	if ($TMA!="TMA"){
		$option.= "<option>TMA</option>";
	}
	if ($TMA!="DTMA"){
		$option.= "<option>DTMA</option>";
	}
	if ($TMA!="DDTMA"){
		$option.= "<option>DDTMA</option>";
	}
	if ($TMA!="DB DDTMA"){
		$option.= "<option>DB DDTMA</option>";
	}
	if ($TMA!="Double DDTMA"){
		$option.= "<option>Double DDTMA</option>";
	}
	return $option;
}
/*********************************************************************************************************************/
function get_select_TRU($TRU){

	$option.= "<option selected>$TRU</option>";

	if ($TRU!="None" && $TRU!="NONE" &&  $TRU!=""){
		$option.= "<option value=''>NONE</option>";
	}
	if ($TRU!="TRU"){
		$option.= "<option>TRU</option>";
	}
	if ($TRU!="CTRU"){
		$option.= "<option>CTRU</option>";
	}
	if ($TRU!="DTRU"){
		$option.= "<option>DTRU</option>";
	}
	if ($TRU!="DRU"){
		$option.= "<option>DRU</option>";
	}
	if ($TRU!="EDTRU"){
		$option.= "<option>EDTRU</option>";
	}
	if ($TRU!="STRU"){
		$option.= "<option>STRU</option>";
	}
	if ($TRU!="MTRU"){
		$option.= "<option>MTRU</option>";
	}
	return $option;
}
/*********************************************************************************************************************/
function get_select_azi($selected){
	echo "<option selected>$selected";
	if ($selected!="NA"){
	echo "<option value=''>NA</option>";
	}
	for ($i = 0; $i <= 359; $i++) {
		if ($selected!="$i"){
		echo "<option>$i</option>";
		}
	}
	echo "</select>";
}
/*********************************************************************************************************************/
function get_select_YESNO($selected){
	if ($selected!=""){
	echo "<option selected>$selected";
	}
	if ($selected!="NA" && $selected!=""){
	echo "<option value=''>NA</option>";
	}
	if ($selected!="NO"){
	echo "<option>NO</option>";
	}
	if ($selected!="YES"){
	echo "<option>YES</option>";
	}
}
/*********************************************************************************************************************/
function check_feedershare_exists($statut,$viewtype,$bsdskey,$bsdsbobrefresh){
	global $conn_Infobase;
	if ($statut=="PLANNED"){
		$query = "SELECT COUNT(BSDSKEY) AS AMOUNT FROM BSDS_PL WHERE 
		BSDSKEY='".$bsdskey."' AND STATUS='".$viewtype."'";
		//echo "$query<br>";
		if ($viewtype!="PRE"){
			$query .=" AND BSDS_BOB_REFRESH=to_date('".$bsdsbobrefresh."')";
		}
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt){
			die_silently($conn_Infobase, $error_str);
			exit;
		}else{
			OCIFreeStatement($stmt);
			return $res1['AMOUNT'][0];
		}
	}else if ($statut=="CURRENT"){
		$query = "SELECT COUNT(BSDSKEY) AS AMOUNT FROM BSDS_CU
		WHERE BSDSKEY='".$bsdskey."' AND STATUS='".$viewtype."'";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt){
			die_silently($conn_Infobase, $error_str);
			exit;
		}else{
			OCIFreeStatement($stmt);
			return $res1['AMOUNT'][0];
		}
	}
}
/*********************************************************************************************************************/
function get_select_ASC($ASC){
	if ($ASC){
		$option.= "<option selected>$ASC</option>";
	}
	if ($ASC!="NONE"){
		$option.= "<option>NONE</option>";
	}
	if ($ASC!="ASC"){
		$option.= "<option>ASC</option>";
	}
	if ($ASC!="AISG 12dB"){
		$option.= "<option>AISG 12dB</option>";
	}
	if ($ASC!="RRU AC"){
		$option.= "<option>RRU AC</option>";
	}
	if ($ASC!="RRU DC"){
		$option.= "<option>RRU DC</option>";
	}
	echo $option;
}
/*********************************************************************************************************************/
function get_select_RRU($ASC){
	if ($ASC){
		$option.= "<option selected>$ASC</option>";
	}
	if ($ASC!="NONE"){
		$option.= "<option>NONE</option>";
	}
	if ($ASC!="ASC"){
		$option.= "<option>ASC</option>";
	}
	if ($ASC!="AISG 12dB"){
		$option.= "<option>AISG 12dB</option>";
	}
	if ($ASC!="RRU AC"){
		$option.= "<option>RRU AC</option>";
	}
	if ($ASC!="RRU DC"){
		$option.= "<option>RRU DC</option>";
	}
	if ($ASC!="KAT78210430"){
		$option.= "<option>KAT78210430</option>";
	}
	if ($ASC!="KAT78210440"){
		$option.= "<option>KAT78210440</option>";
	}
	if ($ASC!="KAT78210510"){
		$option.= "<option>KAT78210510</option>";
	}
	if ($ASC!="KAT78210517"){
		$option.= "<option>KAT78210517</option>";
	}
	if ($ASC!="KAT78210583"){
		$option.= "<option>KAT78210583</option>";
	}
	if ($ASC!="KAT78210612"){
		$option.= "<option>KAT78210612</option>";
	}
	if ($ASC!="KAT78210990"){
		$option.= "<option>KAT78210990</option>";
	}
	if ($ASC!="KAT78211103"){
		$option.= "<option>KAT78211103</option>";
	}
	

	echo $option;
}
function get_select_RRU2($ASC){
	if ($ASC){
		$option.= "<option selected>$ASC</option>";
	}
	if ($ASC!="NA"){
		$option.= "<option>NA</option>";
	}
	if ($ASC!="RU"){
		$option.= "<option>RU</option>";
	}
	if ($ASC!="RRU"){
		$option.= "<option>RRU</option>";
	}
	if ($ASC!="NONE"){
		$option.= "<option>NONE</option>";
	}
	if ($ASC!="RU 1"){
		$option.= "<option>RU 1</option>";
	}
	if ($ASC!="RU 2"){
		$option.= "<option>RU 2</option>";
	}
	if ($ASC!="RU 3"){
		$option.= "<option>RU 3</option>";
	}
	if ($ASC!="RU 4"){
		$option.= "<option>RU 4</option>";
	}
	if ($ASC!="RU 5"){
		$option.= "<option>RU 5</option>";
	}
	if ($ASC!="RU 6"){
		$option.= "<option>RU 6</option>";
	}
	if ($ASC!="RRU 1"){
		$option.= "<option>RRU 1</option>";
	}
	if ($ASC!="RRU 2"){
		$option.= "<option>RRU 2</option>";
	}
	if ($ASC!="RRU 3"){
		$option.= "<option>RRU 3</option>";
	}
	if ($ASC!="RRU 4"){
		$option.= "<option>RRU 4</option>";
	}
	if ($ASC!="RRU 5"){
		$option.= "<option>RRU 5</option>";
	}
	if ($ASC!="RRU 6"){
		$option.= "<option>RRU 6</option>";
	}
	if ($ASC!="R8862 S8000"){
		$option.= "<option>R8862 S8000</option>";
	}
	if ($ASC!="R8882 S8000"){
		$option.= "<option>R8882 S8000</option>";
	}
	if ($ASC!="R8892N M8090"){
		$option.= "<option>R8892N M8090</option>";
	}
	if ($ASC!="RSU82 S8000"){
		$option.= "<option>RSU82 S8000</option>";
	}
	if ($ASC!="RSU82 S9000"){
		$option.= "<option>RSU82 S9000</option>";
	}
	if ($ASC!="R8862 S9000"){
		$option.= "<option>R8862 S9000</option>";
	}
	if ($ASC!="R8882 S1800"){
		$option.= "<option>R8882 S1800</option>";
	}
	if ($ASC!="R8882 GUL1812"){
		$option.= "<option>R8882 GUL1812</option>";
	}
	if ($ASC!="R8862 S1800"){
		$option.= "<option>R8862 S1800</option>";
	}
	if ($ASC!="R8854 S1800"){
		$option.= "<option>R8854 S1800</option>";
	}
	if ($ASC!="RSU82 S1800"){
		$option.= "<option>RSU82 S1800</option>";
	}
	if ($ASC!="RSU82 GUL1816"){
		$option.= "<option>RSU82 GUL1816</option>";
	}
	if ($ASC!="R8862 S2100"){
		$option.= "<option>R8862 S2100</option>";
	}
	if ($ASC!="RSU40"){
		$option.= "<option>RSU40</option>";
	}
	if ($ASC!="RSU60E"){
		$option.= "<option>RSU60E</option>";
	}
	if ($ASC!="RSU82 S2100"){
		$option.= "<option>RSU82 S2100</option>";
	}

	echo $option;
}




//ALLES HIERBOVEN WORDT ZEKER GEBRUIKT

/*********************************************************************************************************************/
function get_mechtilt_dir($MECHTILT1_1){
	$MECHTILT_DIR1_1 = substr($MECHTILT1_1,0,1);
	if ($MECHTILT1_1==0){
		$MECHTILT_DIR1_1='NA';
	}else if ($MECHTILT_DIR1_1=='-'){
		$MECHTILT_DIR1_1='UPTILT';
	}else {
	  	$MECHTILT_DIR1_1='DOWNTILT';
	}
	return $MECHTILT_DIR1_1;
}

/****************************************************************************************************************/
function get_select_LOGNODES($LOGNODE_selected){
	global $conn_Infobase;
	$query = "select IDNAME from VWLOGNODE ORDER BY IDNAME ASC";
	//echo "<br><br>$query";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	echo "<option selected>$LOGNODE_selected";
	foreach ($res1['IDNAME'] as $key=>$attrib_id) {
		$Lognode = $res1['IDNAME'][$key];
	    echo "<option>$Lognode</option>";
	}
	 echo "<option>NONE</option>";
}
/*********************************************************************************************************************/
function get_select_LOWHIGH($selected){
		echo "<option selected>$selected";
		if ($selected!="NA"){
        echo "<option>NA</option>";
		}
		if ($selected!="NORMAL"){
        echo "<option>NORMAL</option>";
		}
		if ($selected!="HIGH"){
		echo "<option>HIGH</option>";
		}
		echo "</select>";
}
/*********************************************************************************************************************/
function get_select_celleq($CONFIG){
	global $conn_Infobase;
	$query = "select DISTINCT IDNAME from VWCELLEQUIPMENT";
	//echo "<br><br>$query";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	echo "<option selected>$CONFIG";
	foreach ($res1['IDNAME'] as $key=>$attrib_id) {
		$celleq = $res1['IDNAME'][$key];
	 	if ($CONFIG!=$celleq){
	        echo "<option>$celleq</option>";
		}
	}
}




/*********************************************************************************************************************/
function get_select_HRACTIVE($HR){
	if ($HR){
	echo "<option selected>$HR</option>";
	}
	if ($HR!="NA"){
		echo "<option>NA</option>";
	}
	if ($HR!="HR"){
		echo "<option>HR</option>";
	}
	if ($HR!="AMRHR"){
		echo "<option>AMRHR</option>";
	}
	if ($HR!="AMRFR"){
		echo "<option>AMRFR</option>";
	}
}
/*********************************************************************************************************************/
function get_select_anttype($ANTTYPE){
	global $conn_Infobase;
	$query = "select DISTINCT IDNAME from antennatype";
	//echo "$query<br>";
	//die;
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	$option.= "<option selected>$ANTTYPE</option>";
	foreach ($res1['IDNAME'] as $key=>$attrib_id) {
		$anttype = $res1['IDNAME'][$key];
	 	//if ($ANTTYPE!="$anttype"){
	        $option.= "<option>$anttype</option>";
		//}
	}
	return $option;
}

/*********************************************************************************************************************/
function get_select_TXBHW($selected){
		echo "<option selected>$selected</option>";
		echo "<option></option>";
		echo "<option>32</option>";
		echo "<option>64</option>";
		echo "<option>192</option>";
		echo "<option>228</option>";
		echo "<option>256</option>";
		echo "<option>384</option>";
		echo "<option>512</option>";
		echo "<option>768</option>";
		echo "</select>";
}


/*********************************************************************************************************************/

function convert_antenna_bipt_UMTS($ANTTYPE){

	if (substr($ANTTYPE, 0, 14)=="K742212_S_UMTS"){
			$ANTTYPE="K742212_S_UMTS_0_8";
	}else if (substr($ANTTYPE, 0, 14)=="K742234_D_UMTS"){
		if (substr($ANTTYPE, -1)=="L"){
			$ANTTYPE="K742234_D_UMTS_0_8_L";
		}
		if (substr($ANTTYPE, -1)=="R"){
		$ANTTYPE="K742234_D_UMTS_0_8_R";
		}
	}else if (substr($ANTTYPE, 0, 14)=="K742211_S_UMTS"){
		$ANTTYPE="K742211_S_UMTS_0_10";
	}else if (substr($ANTTYPE, 0, 14)=="K742215_S_UMTS"){
		$ANTTYPE="K742215_S_UMTS_0_10";
	}else if (substr($ANTTYPE, 0, 17)=="K742241_T_UMTS_LS"){
		$ANTTYPE="K742241_T_UMTS_LS_0_8";
	}else if (substr($ANTTYPE, 0, 17)=="K742241_T_UMTS_US"){
		$ANTTYPE="K742241_T_UMTS_US_0_8";
	}else if (substr($ANTTYPE, 0, 14)=="K742265_D_UMTS"){
		$ANTTYPE="K742265_D_UMTS_0_6";
	}else if (substr($ANTTYPE, 0, 14)=="K742266_D_UMTS"){
		$ANTTYPE="K742266_D_UMTS_0_6";
	}else{
		$ANTTYPE=$ANTTYPE;
	}
	return $ANTTYPE;

}
/*********************************************************************************************************************/
/*
function get_lognodes($idname){
	global $conn_Infobase;
	$query="select a.sitekey, b.lognodepk, a.idname
				from cellsites@ent4prod a left outer join lognode@ent4prod b on a.sitename like substr(b.name,0,7) or a.sitename like substr(b.name,0,8)
				where
					a.idname like '%".$idname."%' and
					a.projectno = '1'
					order by  a.sitename";
	//echo $query;
	//die;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	return $res1;
}*/

/*********************************************************************************************************************/
function get_lognode($idname){
	global $conn_Infobase;
	$query="select lognodepk from lognode@ent4prod WHERE NAME LIKE '%".$idname."%'";
	//echo $query;
	//die;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	return $res1;
}
/*********************************************************************************************************************/
function delete_BSDS($BSDSKEY,$POST){
	global $conn_Infobase;

	$query = "DELETE FROM BSDS_PLANNED_GEN_GSM900".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM  BSDS_PLANNED_GEN_GSM1800".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_GSM1800_1".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_GSM1800_1".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_GSM1800_2".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_GSM1800_3".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_GSM900_1".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_GSM900_2".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_GSM900_3".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_UMTS_01_1".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_UMTS_01_2".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_UMTS_01_3".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);
	$query = "DELETE FROM BSDS_PLANNED_UMTS_GEN_01".$POST." WHERE BSDSKEY='".$BSDSKEY."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
   	}
   	OCICommit($conn_Infobase);

   	//echo $query."<hr>";
   	if ($POST!="_POST"){
		$query = "DELETE FROM BSDS_GENERALINFO2 WHERE BSDSKEY='".$BSDSKEY."'";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	   	if (!$stmt) {
	      die_silently($conn_Infobase, $error_str);
	   	}
	   	OCICommit($conn_Infobase);
	    //echo $query."<hr>";

	   	$query = "DELETE FROM INFOBASE.BSDS_SITE_FUNDED WHERE BSDSKEY='".$BSDSKEY."'";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	   	if (!$stmt) {
	      die_silently($conn_Infobase, $error_str);
	   	}
	   	OCICommit($conn_Infobase);
	   	//echo $query."<hr>";

	   	$query = "DELETE FROM INFOBASE.BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY."'";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}
		OCICommit($conn_Infobase);
	   	//echo $query."<hr>";
   	}
}
/*********************************************************************************************************************/
function check_if_same($current1,$planned1,$current2,$planned2,$current3,$planned3,$current4,$planned4,$override,$general_override){

	if ($general_override=="NOT_SAVED"){
		$color[1][1]="NOT_SAVED";
		$color[1][2]="NOT_SAVED_SELECT";
		$color[1][3]="NOT_SAVED_INPUT";
		$color[1][4]="NOT_SAVED_TEXTAREA";
		$color[2][1]="NOT_SAVED";
		$color[2][2]="NOT_SAVED_SELECT";
		$color[2][3]="NOT_SAVED_INPUT";
		$color[3][1]="NOT_SAVED";
		$color[3][2]="NOT_SAVED_SELECT";
		$color[3][3]="NOT_SAVED_INPUT";

		$color[4][1]="NOT_SAVED";
		$color[4][2]="NOT_SAVED_SELECT";
		$color[4][3]="NOT_SAVED_INPUT";
		$color[4][4]="NOT_SAVED_TEXTAREA";
		$color[5][1]="NOT_SAVED";
		$color[5][2]="NOT_SAVED_SELECT";
		$color[5][3]="NOT_SAVED_INPUT";
		$color[6][1]="NOT_SAVED";
		$color[6][2]="NOT_SAVED_SELECT";
		$color[6][3]="NOT_SAVED_INPUT";

	}else {
		$current1=strtoupper($current1);
		$current2=strtoupper($current2);
		$current3=strtoupper($current3);
		$current4=strtoupper($current4);
		$planned1=strtoupper($planned1);
		$planned2=strtoupper($planned2);
		$planned3=strtoupper($planned3);
		$planned4=strtoupper($planned4);
		//echo "($current1,$planned1,$current2,$planned2,$current3,$planned3,$override)<br>";
		if ($current1=="NONE" || $current1=="-" || $current1=="NA" || $current1=="N/A" || ($current1=="" && $planned1=="0") || ($current1=="0" && $planned1=="")){
			$current1="";
		}
		if ($current2=="NONE" || $current2=="-" || $current2=="NA" || $current2=="N/A" || ($current2=="" && $planned2=="0") || ($current2=="0" && $planned2=="")){
			$current2="";
		}
		if ($current3=="NONE" || $current3=="-" || $current3=="NA" || $current3=="N/A" || ($current3=="" && $planned3=="0") || ($current3=="0" && $planned3=="")){
			$current3="";
		}
		if ($current4=="NONE" || $current4=="-" || $current4=="NA" || $current4=="N/A" || ($current4=="" && $planned4=="0") || ($current4=="0" && $planned4=="")){
			$current4="";
		}
		if ($planned1=="NONE" || $planned1=="-" || $planned1=="NA" || $planned1=="N/A" || ($current1=="" && $planned1=="0") || ($current1=="0" && $planned1=="")){
			$planned1="";
		}
		if ($planned2=="NONE" || $planned2=="-" || $planned2=="NA" || $planned2=="N/A" || ($current2=="" && $planned2=="0") || ($current2=="0" && $planned2=="")){
			$planned2="";
		}
		if ($planned3=="NONE" || $planned3=="-" || $planned3=="NA" || $planned3=="N/A" || ($current3=="" && $planned3=="0") || ($current3=="0" && $planned3=="")){
			$planned3="";
		}
		if ($planned4=="NONE" || $planned4=="-" || $planned4=="NA" || $planned4=="N/A" || ($current4=="" && $planned4=="0") || ($current4=="0" && $planned4=="")){
			$planned4="";
		}
		//echo "($current1,$planned1,$current2,$planned2,$current3,$planned3,$override)<br>";
		if ($current1 != $planned1 && $override!="1"){
			$color[1][1]="CURRENT_NOTSAME";
			$color[1][2]="SELECT_CUR_NOTSAME";
			$color[1][3]="INPUT_CUR_NOTSAME";
			$color[1][4]="TEXTAREA_CUR_NOTSAME";
			$color[4][1]="PLANNED_NOTSAME";
			$color[4][2]="SELECT_PL_NOTSAME";
			$color[4][3]="INPUT_PL_NOTSAME";
		}elseif ($current1 == $planned1 && $override!="1"){
			$color[1][1]="CURRENT_SAME";
			$color[1][2]="SELECT_CUR_SAME";
			$color[1][3]="INPUT_CUR_SAME";
			$color[1][4]="TEXTAREA_CUR_SAME";
			$color[4][1]="PLANNED_SAME";
			$color[4][2]="SELECT_PL_SAME";
			$color[4][3]="INPUT_PL_SAME";
		}else{
			$color[1][1]="ERROR";
			$color[1][2]="ERROR";
			$color[1][3]="ERROR";
			$color[1][4]="ERROR";
			$color[4][1]="ERROR";
			$color[4][2]="ERROR";
			$color[4][3]="ERROR";
		}
		if ($current2 != $planned2 && $override!="1"){
			$color[2][1]="CURRENT_NOTSAME";
			$color[2][2]="SELECT_CUR_NOTSAME";
			$color[2][3]="INPUT_CUR_NOTSAME";
			$color[5][1]="PLANNED_NOTSAME";
			$color[5][2]="SELECT_PL_NOTSAME";
			$color[5][3]="INPUT_PL_NOTSAME";
		}elseif ($current2 == $planned2 && $override!="1"){
			$color[2][1]="CURRENT_SAME";
			$color[2][2]="SELECT_CUR_SAME";
			$color[2][3]="INPUT_CUR_SAME";
			$color[5][1]="PLANNED_SAME";
			$color[5][2]="SELECT_PL_SAME";
			$color[5][3]="INPUT_PL_SAME";
		}else{
			$color[2][1]="ERROR";
			$color[2][2]="ERROR";
			$color[2][3]="ERROR";
			$color[5][1]="ERROR";
			$color[5][2]="ERROR";
			$color[5][3]="ERROR";
		}
		if  ($current3 != $planned3 && $override!="1"){
			$color[3][1]="CURRENT_NOTSAME";
			$color[3][2]="SELECT_CUR_NOTSAME";
			$color[3][3]="INPUT_CUR_NOTSAME";
			$color[6][1]="PLANNED_NOTSAME";
			$color[6][2]="SELECT_PL_NOTSAME";
			$color[6][3]="INPUT_PL_NOTSAME";
		}elseif ($current3 == $planned3 && $override!="1"){
			$color[3][1]="CURRENT_SAME";
			$color[3][2]="SELECT_CUR_SAME";
			$color[3][3]="INPUT_CUR_SAME";
			$color[6][1]="PLANNED_SAME";
			$color[6][2]="SELECT_PL_SAME";
			$color[6][3]="INPUT_PL_SAME";
		}else{
			$color[3][1]="ERROR";
			$color[3][2]="ERROR";
			$color[3][3]="ERROR";
			$color[6][1]="ERROR";
			$color[6][2]="ERROR";
			$color[6][3]="ERROR";
		}
		if  ($current4 != $planned4 && $override!="1"){
			$color[7][1]="CURRENT_NOTSAME";
			$color[7][2]="SELECT_CUR_NOTSAME";
			$color[7][3]="INPUT_CUR_NOTSAME";
			$color[8][1]="PLANNED_NOTSAME";
			$color[8][2]="SELECT_PL_NOTSAME";
			$color[8][3]="INPUT_CUR_NOTSAME";
		}elseif ($current4 == $planned4 && $override!="1"){
			$color[7][1]="CURRENT_SAME";
			$color[7][2]="SELECT_CUR_SAME";
			$color[7][3]="INPUT_CUR_SAME";
			$color[8][1]="PLANNED_SAME";
			$color[8][2]="SELECT_PL_SAME";
			$color[8][3]="INPUT_PL_SAME";
		}else{
			$color[7][1]="ERROR";
			$color[7][2]="ERROR";
			$color[7][3]="ERROR";
			$color[8][1]="ERROR";
			$color[8][2]="ERROR";
			$color[8][3]="ERROR";
		}
	}
	//echo "---".print_r($color,true);
	return $color;
}

/****************************************************************************************************************/
function get_bobrep_refreshdate(){

	global $conn_Infobase;

	$today_date=date("d/m/y");
	$today_hour=date("H");

	$query = "SELECT RUN_DATE FROM N1_POSTDTB_FUNDED_ALLUPG_V2";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
	    $REFRESH_DATE['UPG']=$res1['RUN_DATE'][0];
	}
	$query = "SELECT TODAY_DATE FROM N1_POSTDTB_TECHFUNDED_ALLNB_V2";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
	    $REFRESH_DATE['NB']=$res1['TODAY_DATE'][0];
	}

	if (substr($REFRESH_DATE['UPG'],0,8)== $today_date && abs(substr($REFRESH_DATE['UPG'],9,2)- $today_hour) < 7 && substr($REFRESH_DATE['UPG'],0,8)!="") {
		$REFRESH_DATE['COLOR1']="green";
	}else{
		$REFRESH_DATE['COLOR1']="red";
		$REFRESH_DATE['ERROR']="";
		if (substr($REFRESH_DATE['UPG'],0,8)==""){
			$REFRESH_DATE['UPG']="BOB report ERROR!";
		}
	}
	if (substr($REFRESH_DATE['NB'],0,8)== $today_date && abs(substr($REFRESH_DATE['NB'],9,2)- $today_hour) < 7 && substr($REFRESH_DATE['NB'],0,8)!=""){
		$REFRESH_DATE['COLOR2']="green";
	}else{
		$REFRESH_DATE['COLOR2']="red";
		$REFRESH_DATE['ERROR']="";
		if (substr($REFRESH_DATE['NB'],0,8)==""){
			$REFRESH_DATE['NB']="BOB report ERROR!";
		}
	}

	return $REFRESH_DATE;
}
/*******************************************************************************************************************/
function check_config($GSM1800_status,$GSM900_status){

	global $conn_Infobase;

	if ($GSM1800_status!="PRE READY TO BUILD"){
		for ($i=1;$i<=3;$i++){

			$query = "SELECT FREQ_ACTIVE FROM BSDS_PLANNED_GSM1800_".$i."_POST WHERE BSDSKEY= '".$_SESSION['BSDSKEY']."'";
			//echo $query."<br>";
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt);
			    $freq[$i]=$res1['FREQ_ACTIVE'][0];
			}
			//echo "freq=".$freq[$i];
		}
	}

	if ($GSM900_status!="PRE READY TO BUILD"){
		$j=4;
		for ($i=1;$i<=3;$i++){

			$query = "SELECT FREQ_ACTIVE FROM BSDS_PLANNED_GSM900_".$i."_POST WHERE BSDSKEY= '".$_SESSION['BSDSKEY']."'";
			//echo $query."<br>";
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt);
			    $freq[$j]=$res1['FREQ_ACTIVE'][0];
			}
			//echo "freq=".$freq[$j];
			$j++;
		}
	}
	return $freq;
}
/*******************************************************************************************************************/
function check_numbercabs($GSM1800_status,$GSM900_status){

	global $conn_Infobase;

	if ($GSM1800_status!="PRE READY TO BUILD"){
		$query = "SELECT NR_OF_CAB, CABTYPE FROM BSDS_PLANNED_GEN_GSM1800_POST WHERE BSDSKEY= '".$_SESSION['BSDSKEY']."'";
		//echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
		    $NR_OF_CAB[DCS][CABNR]=$res1['NR_OF_CAB'][0];
		    $NR_OF_CAB[DCS][CABTYPE]=$res1['CABTYPE'][0];
		}
	}else if ($GSM1800_status=="PRE READY TO BUILD"){
		$query = "SELECT NR_OF_CAB FROM BSDS_CURRENT_GSM1800 WHERE sitekey= '".$_SESSION['Sitekey']."'";
		//echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
		    $NR_OF_CAB[DCS][CABNR]=$res1['NR_OF_CAB'][0];
			}
	}


	if ($GSM900_status!="PRE READY TO BUILD"){
		$query = "SELECT NR_OF_CAB, CABTYPE FROM BSDS_PLANNED_GEN_GSM900_POST WHERE BSDSKEY= '".$_SESSION['BSDSKEY']."'";
		//echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
		    $NR_OF_CAB[EGS][CABNR]=$res1['NR_OF_CAB'][0];
		    $NR_OF_CAB[EGS][CABTYPE]=$res1['CABTYPE'][0];
		}
	}else if ($GSM900_status=="PRE READY TO BUILD"){
		$query = "SELECT NR_OF_CAB FROM BSDS_CURRENT_GSM900 WHERE sitekey= '".$_SESSION['Sitekey']."'";
		//echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
		    $NR_OF_CAB[EGS][CABNR]=$res1['NR_OF_CAB'][0];
		}
	}

	return $NR_OF_CAB;
}
/*********************************************************************************************************************/
function get_LAC($siteID)
{
	global $conn_Infobase;

	$query = "SELECT CGI FROM SWITCH_2G_RLDEP WHERE CELL LIKE '%".$siteID."%'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$LAC=explode("-",$res1['CGI'][0]);
	    return $LAC[2];
	}
}

/*********************************************************************************************************************/
function check_if_dup_records($BSDS_funded)
{
	for ($i=0;$i<=$BSDS_funded['amount'];$i++){
		if($BSDS_funded[$i]['TECHNOLOGY_BOTH']!=""){
		$array[]=$BSDS_funded[$i]['TECHNOLOGY_BOTH'];
		//echo $BSDS_funded[$i]['TECHNOLOGY_BOTH']."<br>";
		}
	}

    $dup_array = $array;
    $dup_array = array_unique($dup_array);
    if(count($dup_array) != count($array))
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}
/********************************************************************************************************************/
function analyse_changes_Asset($name, $w1, $w2, $w3,$w4,$w5,$w6,$x1,$x2,$x3,$x4,$x5,$x6)
{
	
	//echo $name."------->".$w1." ".$x1." / ".$w2." ".$x2." / ".$w3." ".$x3." / ".$w4." ".$x4."<br>";

 	if ($w1!=$x1 or $w2!=$x2 or $w3!=$x3 or $w4!=$x4 or $w5!=$x5 or $w6!=$x6){ 

 		echo '<tr>
			 <td class="tableheader danger">'.$name.'</td>';
			 if ($w1!=$x1 and $w1!='' and $x1!=''){ 
				echo '<td class="danger">'.$w1.'</td>';
			 }else{
			 	echo '<td>&nbsp;</td>';
			 }
			 if ($w2!=$x2 and $w2!='' and $x2!=''){ 
				echo '<td class="danger">'.$w2.'</td>';
			 }else{
			 	echo '<td>&nbsp;</td>';
			 }
			 if ($w3!=$x3 and $w3!='' and $x3!=''){ 
				echo '<td class="danger">'.$w3.'</td>';
			 }else{
			 	echo '<td>&nbsp;</td>';
			 }
			 if ($w4!=$x4 and $w4!='' and $x4!=''){ 
				echo '<td class="danger">'.$w4.'</td>';
			 }else if ($w4!='' and $x4!=''){
			 	echo '<td>&nbsp;</td>';
			 }
			 if ($w5!=$x5 and $w5!='' and $x5!=''){ 
				echo '<td class="danger">'.$w5.'</td>';
			 }else if ($w5!='' and $x5!=''){
			 	echo '<td>&nbsp;</td>';
			 }
			 if ($w4!=$x6 and $w6!='' and $x6!=''){ 
				echo '<td class="danger">'.$w6.'</td>';
			 }else if ($w6!='' and $x6!=''){
			 	echo '<td>&nbsp;</td>';
			 }
			 echo '<td class="borderleft danger" colspan="3"><b>CHANGE IN ASSET!!</b></td>';
			 if (($w4!='' and $x4!='')){
			 echo '<td>&nbsp;</td>';
			 } 
			 if (($w5!='' and $x5!='')){
			 echo '<td>&nbsp;</td>';
			 } 
			 if (($w6!='' and $x6!='')){
			 echo '<td>&nbsp;</td>';
			 } 
		echo '</tr>';
	} 
	
}
?>
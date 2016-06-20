<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
//include("campon_proc.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$conn_mysql = mysql_connect($mysql_host, $mysql_user, $mysql_password);

?>
<link rel="stylesheet" href="band.css" type="text/css"></link>
</head>
<body>
<?php
/* ANTHEIGHT1,AZI,ANTTYPE1,MECHTILT_DIR1,MECHTILT1,ELECTILT1*/
$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$_SESSION['BSDSKEY']."'
AND SITEKEY='".$_POST['lognodeID_GSM']."'
AND (TECHNOLOGY LIKE '%DCS%' OR TECHNOLOGY LIKE '%1800%')
AND BSDS_BOB_REFRESH=(
		SELECT MAX(BSDS_BOB_REFRESH) FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$_POST['BSDSKEY']."'
		AND SITEKEY='".$_SESSION['Sitekey']."')";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
    $total_records=count($res1['BSDSKEY']); // CAN ONLY RETURN 1 or 0 (NOT MORE THAN 1!!!!)
	$STATUS_DATE_NET1=$res1['NET1_DATE'][0];
	$STATUS_BSDS=$res1['STATUS'][0];
	$_SESSION['BSDS_BOB_REFRESH']=$res1['BSDS_BOB_REFRESH'][0];
}
//echo "GSM1800: ". $total_records."<br>";
if ($total_records!=0){
	//echo $_SESSION['BSDS_BOB_REFRESH']."=BSDS_BOB_REFRESH<br>";

	if ($_SESSION['STATUS_GSM1800']=="SITE FUNDED"){
		$_SESSION['table_view']="_POST";
	}else if ($_SESSION['STATUS_GSM1800']=="BSDS FUNDED"){
		$_SESSION['table_view']="_FUND";
	}else if ($_SESSION['STATUS_GSM1800']=="BSDS AS BUILD"){
		$_SESSION['table_view']="_BUILD";
	}
}else{
		$_SESSION['table_view']=""; //USE PRE DATA
		$_SESSION['BSDS_BOB_REFRESH']="";
}
//echo "$total_records BSDS_BOB_REFRESH=". $_SESSION['BSDS_BOB_REFRESH']." - STATUS=".$_SESSION['STATUS_GSM1800']."<br>";

$check_current_exists=check_current_exists("GSM1800",'',''); //Check if there is current data in Infobase DB
//echo "check_current_exists900 $check_current_exists<br>";
if ($check_current_exists!=0){
	$check_planned_exists=check_planned_exists("GSM1800",'','allsec');
	//echo "check_planned_exists $check_planned_exists<br>";

	if ($check_planned_exists!=0){
		$planneddata=get_data("GSM1800","1","PLANNED",'');
		$GSM1800_antheight1=$planneddata['ANTHEIGHT1'][0];
		$GSM1800_azi1=$planneddata['AZI'][0];
		$GSM1800_ant1=$planneddata['ANTTYPE1'][0];
		$GSM1800_mechtilt_dir1=$planneddata['MECHTILT_DIR1'][0];
		$GSM1800_elektilt1=$planneddata['ELECTILT1'][0];
		if ($GSM1800_mechtilt_dir1=="UPTILT"){
			$GSM1800_tilt1=$planneddata['MECHTILT1'][0]-$GSM1800_elektilt1; //= MECH TILT + ELEK TILT
		}else{
			$GSM1800_tilt1=-$planneddata['MECHTILT1'][0]-$GSM1800_elektilt1; //= MECH TILT + ELEK TILT
		}
		//echo $planneddata['MECHTILT1'][0]."**".$GSM1800_elektilt1;

		$currentdata=get_data("GSM1800","1","CURRENT_ASSET",'');
		$GSM1800_state1=get_config($currentdata['CELLSTATUS'][0],'GSM1800');

		$planneddata=get_data("GSM1800","2","PLANNED",'');
		$GSM1800_antheight2=$planneddata['ANTHEIGHT1'][0];
		$GSM1800_azi2=$planneddata['AZI'][0];
		$GSM1800_ant2=$planneddata['ANTTYPE1'][0];
		$GSM1800_mechtilt_dir2=$planneddata['MECHTILT_DIR1'][0];
		$GSM1800_elektilt2=$planneddata['ELECTILT1'][0];
		if ($GSM1800_mechtilt_dir2=="UPTILT"){
			$GSM1800_tilt2=$planneddata['MECHTILT1'][0]-$GSM1800_elektilt2; //= MECH TILT + ELEK TILT
		}else{
			$GSM1800_tilt2=-$planneddata['MECHTILT1'][0]-$GSM1800_elektilt2; //= MECH TILT + ELEK TILT
		}

		$currentdata=get_data("GSM1800","2","CURRENT_ASSET",'');
		$GSM1800_state2=get_config($currentdata['CELLSTATUS'][0],'GSM1800');

		$planneddata=get_data("GSM1800","3","PLANNED",'');
		$GSM1800_antheight3=$planneddata['ANTHEIGHT1'][0];
		$GSM1800_azi3=$planneddata['AZI'][0];
		$GSM1800_ant3=$planneddata['ANTTYPE1'][0];
		$GSM1800_mechtilt_dir3=$planneddata['MECHTILT_DIR1'][0];
		$GSM1800_elektilt3=$planneddata['ELECTILT1'][0];
		if ($GSM1800_mechtilt_dir3=="UPTILT"){
			$GSM1800_tilt3=$planneddata['MECHTILT1'][0]-$GSM1800_elektilt3; //= MECH TILT + ELEK TILT
		}else{
			$GSM1800_tilt3=-$planneddata['MECHTILT1'][0]-$GSM1800_elektilt3; //= MECH TILT + ELEK TILT
		}

		$currentdata=get_data("GSM1800","3","CURRENT_ASSET",'');
		$GSM1800_state3=get_config($currentdata['CELLSTATUS'][0],'GSM1800');

	}else{
		$currentdata=get_data("GSM1800","$sec1","CURRENT_ASSET",$cab); //Get data from Asset
		$AMOUNT_ASSET_INFO=count($currentdata['SITEKEY']);
		//echo "$AMOUNT_ASSET_INFO"; //Amount of sectors

		$j=1;
		$start="yes";
		for ($i=0;$i<$AMOUNT_ASSET_INFO;$i++){
			$SECTORID=$currentdata['SECTORID'][$i];
			//echo $SECTORID;
			$ID=substr($SECTORID,-1);
			$last_sect=substr($SECTORID,-1);

			if ($last_sect!=$vorige){
				$k=1;

			}else{
				$k=2;
			}

			if ($ID==6){
				$j=3;
			}else if ($ID==5){
				$j=2;
			}else if ($ID==4){
				$j=1;
			}else if ($ID==3){
				$j=3;
			}else if ($ID==2){
				$j=2;
			}else if ($ID==1){
				$j=1;
			}else if ($ID==0){
				$j=4;
			}

			$STATE="GSM1800_state$j";
			$$STATE=get_config($currentdata['CELLSTATUS'][$i],'GSM1800');

			$AZI="GSM1800_azi$j";
			$$AZI=$currentdata['AZIMUTH'][$i];


			$ANTTYPE="GSM1800_ant$j";
			$$ANTTYPE=$currentdata['ANTENNATYPE'][$i];

			if (substr($currentdata['ANTENNATYPE'][$i], -2,1)=="_"){
				$GSM1800_elektilt1=substr($currentdata['ANTENNATYPE'][$i], -1);
			}else{
				$GSM1800_elektilt1=substr($currentdata['ANTENNATYPE'][$i], -2,2);
			}

			if (!is_numeric($GSM1800_elektilt1)){
				$GSM1800_elektilt1=substr($currentdata['ANTENNATYPE'][$i], -3,1);
			}

			$ANTHEIGHT="GSM1800_antheight$j";
			$$ANTHEIGHT=round($currentdata['ANTENNAHEIGHT'][$i],2);

			$GSM1800_mechtilt_dir=get_mechtilt_dir($currentdata['DOWNTILT'][$i]);

			$TILT="GSM1800_tilt$j";
			if (GSM1800_mechtilt_dir=="UPTILT" OR $currentdata['DOWNTILT'][$i]==0){
				$$TILT=$currentdata['DOWNTILT'][$i]-$GSM1800_elektilt1;
			}else{
				$$TILT=-$currentdata['DOWNTILT'][$i]-$GSM1800_elektilt1;
			}
		}

	}
}

/*******************************************   GSM900  **********************************************/
$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$_SESSION['BSDSKEY']."'
AND SITEKEY='".$_SESSION['Sitekey']."'
AND (TECHNOLOGY LIKE '%EGS%' OR TECHNOLOGY LIKE '%900%')
AND BSDS_BOB_REFRESH=(
		SELECT MAX(BSDS_BOB_REFRESH) FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$_SESSION['BSDSKEY']."'
		AND SITEKEY='".$_SESSION['Sitekey']."')";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
    $total_records=count($res1['BSDSKEY']); // CAN ONLY RETURN 1 or 0 (NOT MORE THAN 1!!!!)
	$STATUS_DATE_NET1=$res1['NET1_DATE'][0];
	$STATUS_BSDS=$res1['STATUS'][0];
	$_SESSION['BSDS_BOB_REFRESH']=$res1['BSDS_BOB_REFRESH'][0];
	OCIFreeStatement($stmt);
}
//echo "GSM900: ". $total_records."<br>";
if ($total_records!=0){

	//echo "BSDS_BOB_REFRESH=". $_SESSION['BSDS_BOB_REFRESH']." - STATUS=".$_SESSION['STATUS_GSM900']."<br>";

	if ($_SESSION['STATUS_GSM900']=="SITE FUNDED"){
		$_SESSION['table_view']="_POST";
	}else if ($_SESSION['STATUS_GSM900']=="BSDS FUNDED"){
		$_SESSION['table_view']="_FUND";
	}else if ($_SESSION['STATUS_GSM900']=="BSDS AS BUILD"){
		$_SESSION['table_view']="_BUILD";
	}
}else{
		$_SESSION['table_view']=""; //USE PRE DATA
}

//echo "GSM900: ".$_SESSION['table_view']."<br>";

$check_current_exists=check_current_exists("GSM900",'',''); //Check if there is current data in Infobase DB
//echo "check_current_exists $check_current_exists <br>";
if ($check_current_exists!=0){
	$check_planned_exists=check_planned_exists("GSM900",'','allsec');
	//echo "check_planned_exists $check_planned_exists <br>";
	if ($check_planned_exists!=0){

		$planneddata=get_data("GSM900","1","PLANNED",'');
		$GSM900_antheight1=$planneddata['ANTHEIGHT1'][0];
		$GSM900_azi1=$planneddata['AZI'][0];
		$GSM900_ant1=$planneddata['ANTTYPE1'][0];
		$GSM900_mechtilt_dir1=$planneddata['MECHTILT_DIR1'][0];
		$GSM900_elektilt1=$planneddata['ELECTILT1'][0];
		if ($GSM900_mechtilt_dir1=="UPTILT"){
			$GSM900_tilt1=$planneddata['MECHTILT1'][0]-$GSM900_elektilt1;
		}else{
			$GSM900_tilt1=-$planneddata['MECHTILT1'][0]-$GSM900_elektilt1;
		}

		//echo "<br>*** $GSM900_tilt1 --> ".$planneddata['ELECTILT1'][0]."<--->".$planneddata['MECHTILT1'][0]."<br>";

		$currentdata=get_data("GSM900","4","CURRENT_ASSET",'');
		$GSM900_state1=get_config($currentdata['CELLSTATUS'][0],'GSM900');

		$planneddata=get_data("GSM900","2","PLANNED",'');
		$GSM900_antheight2=$planneddata['ANTHEIGHT1'][0];
		$GSM900_azi2=$planneddata['AZI'][0];
		//echo "----".$GSM900_azi2;
		$GSM900_ant2=$planneddata['ANTTYPE1'][0];
		$GSM900_mechtilt_dir2=$planneddata['MECHTILT_DIR1'][0];
		$GSM900_elektilt2=$planneddata['ELECTILT1'][0];


		if ($GSM900_mechtilt_dir2=="UPTILT"){
			$GSM900_tilt2=$planneddata['MECHTILT1'][0]-$GSM900_elektilt2;
		}else{
			$GSM900_tilt2=-$planneddata['MECHTILT1'][0]-$GSM900_elektilt2;
		}

		$currentdata=get_data("GSM900","5","CURRENT_ASSET",'');
		$GSM900_state2=get_config($currentdata['CELLSTATUS'][0],'GSM900');

		$planneddata=get_data("GSM900","3","PLANNED",'');
		$GSM900_antheight3=$planneddata['ANTHEIGHT1'][0];
		$GSM900_azi3=$planneddata['AZI'][0];
		$GSM900_ant3=$planneddata['ANTTYPE1'][0];
		$GSM900_mechtilt_dir3=$planneddata['MECHTILT_DIR1'][0];
		$GSM900_elektilt3=$planneddata['ELECTILT1'][0];

		if ($GSM900_mechtilt_dir3=="UPTILT"){
			$GSM900_tilt3=$planneddata['MECHTILT1'][0]-$GSM900_elektilt3;
		}else{
			$GSM900_tilt3=-$planneddata['MECHTILT1'][0]-$GSM900_elektilt3;
		}

		$currentdata=get_data("GSM900","6","CURRENT_ASSET",'');
		$GSM900_state3=get_config($currentdata['CELLSTATUS'][0],'GSM900');
	}else{

		$currentdata=get_data("GSM900","$sec1","CURRENT_ASSET",$cab); //Get data from Asset
		$AMOUNT_ASSET_INFO=count($currentdata['SITEKEY']);
		//echo "$AMOUNT_ASSET_INFO"; //Amount of sectors

		$j=1;
		$start="yes";
		for ($i=0;$i<$AMOUNT_ASSET_INFO;$i++){
			$SECTORID=$currentdata['SECTORID'][$i];
			//echo $SECTORID;
			$ID=substr($SECTORID,-1);
			$last_sect=substr($SECTORID,-1);

			if ($last_sect!=$vorige){
				$k=1;

			}else{
				$k=2;
			}

			if ($ID==6){
				$j=3;
			}else if ($ID==5){
				$j=2;
			}else if ($ID==4){
				$j=1;
			}else if ($ID==3){
				$j=3;
			}else if ($ID==2){
				$j=2;
			}else if ($ID==1){
				$j=1;
			}else if ($ID==0){
				$j=4;
			}

			$STATE="GSM900_state$j";
			$$STATE=get_config($currentdata['CELLSTATUS'][$i],'GSM900');

			$AZI="GSM900_azi$j";
			$$AZI=$currentdata['AZIMUTH'][$i];


			$ANTTYPE="GSM900_ant$j";
			$$ANTTYPE=$currentdata['ANTENNATYPE'][$i];




			if (substr($currentdata['ANTENNATYPE'][$i], -2,1)=="_"){
			$GSM900_elektilt1=substr($currentdata['ANTENNATYPE'][$i], -1);
			}else{
			$GSM900_elektilt1=substr($currentdata['ANTENNATYPE'][$i], -2,2);
			}
			if (!is_numeric($GSM900_elektilt1)){
				$GSM900_elektilt1=substr($currentdata['ANTENNATYPE'][$i], -3,1);
			}

			$ANTHEIGHT="GSM900_antheight$j";
			$$ANTHEIGHT=round($currentdata['ANTENNAHEIGHT'][$i],2);

			$GSM900_mechtilt_dir=get_mechtilt_dir($currentdata['DOWNTILT'][$i]);

			$TILT="GSM900_tilt$j";
			if (GSM900_mechtilt_dir=="UPTILT" OR $currentdata['DOWNTILT'][$i]==0){
				$$TILT=$currentdata['DOWNTILT'][$i]-$GSM900_elektilt1;
			}else{
				$$TILT=-$currentdata['DOWNTILT'][$i]-$GSM900_elektilt1;
			}


		}

	}
}

$gain_data=get_gain("GSM900",$GSM900_ant1);
$GSM900_gain1=$gain_data['GAIN'][0];
$GSM900_ver1=$gain_data['VER'][0];
$GSM900_hor1=$gain_data['HOR'][0];
$gain_data=get_gain("GSM900",$GSM900_ant2);
$GSM900_gain2=$gain_data['GAIN'][0];
$GSM900_ver2=$gain_data['VER'][0];
$GSM900_hor2=$gain_data['HOR'][0];
$gain_data=get_gain("GSM900",$GSM900_ant3);
$GSM900_gain3=$gain_data['GAIN'][0];
$GSM900_ver3=$gain_data['VER'][0];
$GSM900_hor3=$gain_data['HOR'][0];

$gain_data=get_gain("GSM1800",$GSM1800_ant1);
$GSM1800_gain1=$gain_data['GAIN'][0];
$GSM1800_ver1=$gain_data['VER'][0];
$GSM1800_hor1=$gain_data['HOR'][0];
$gain_data=get_gain("GSM1800",$GSM1800_ant2);
$GSM1800_gain2=$gain_data['GAIN'][0];
$GSM1800_ver2=$gain_data['VER'][0];
$GSM1800_hor2=$gain_data['HOR'][0];
$gain_data=get_gain("GSM1800",$GSM1800_ant3);
$GSM1800_gain3=$gain_data['GAIN'][0];
$GSM1800_ver3=$gain_data['VER'][0];
$GSM1800_hor3=$gain_data['HOR'][0];

$query = "SELECT DONOR FROM VW_REPEATERS WHERE DONOR='".$_SESSION['SiteID']."1'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
    if (count($res1['DONOR'])!=0){
		$donor1="yes";
	}else{
		$donor1="no";
	}
}
$query = "SELECT DONOR FROM VW_REPEATERS WHERE DONOR='".$_SESSION['SiteID']."2'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
    if (count($res1['DONOR'])!=0){
		$donor2="yes";
	}else{
		$donor2="no";
	}
}
$query = "SELECT DONOR FROM VW_REPEATERS WHERE DONOR='".$_SESSION['SiteID']."3'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	if (count($res1['DONOR'])!=0){
		$donor3="yes";
	}else{
		$donor3="no";
	}
}
$query = "SELECT DONOR FROM VW_REPEATERS WHERE DONOR='".$_SESSION['SiteID']."4'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	if (count($res1['DONOR'])!=0){
		$donor4="yes";
	}else{
		$donor4="no";
	}
}
$query = "SELECT DONOR FROM VW_REPEATERS WHERE DONOR='".$_SESSION['SiteID']."5'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	if (count($res1['DONOR'])!=0){
		$donor5="yes";
	}else{
		$donor5="no";
	}
}
$query = "SELECT DONOR FROM VW_REPEATERS WHERE DONOR='".$_SESSION['SiteID']."6'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	if (count($res1['DONOR'])!=0){
		$donor6="yes";
	}else{
		$donor6="no";
	}
}

//THE CRITERIA

if ($GSM1800_azi1!=""){
	if (abs($GSM1800_azi1-$GSM900_azi1)<=10 OR abs($GSM1800_azi1-$GSM900_azi1)>=350){
		$crit_azi1="OK";
		$crit1="Sector 1-4";
		$sec4="taken";
		if ($GSM1800_antheight1-$GSM900_antheight1<=10)	{$crit_antheight1="OK";}else{$crit_antheight1="NOT_OK";}
		if ($GSM1800_hor1-$GSM900_hor1<=10)	{$crit_hor1="OK";}else{$crit_hor1="NOT_OK";}
		if ($GSM1800_ver1-$GSM900_ver1<=3){$crit_ver1="OK";}else{$crit_ver1="NOT_OK";}
		if (abs($GSM900_tilt1-$GSM1800_tilt1)<=3){$crit_tilt1="OK";}else{$crit_tilt1="NOT_OK";}
		if ($GSM1800_gain1=="" && $GSM1800_ant1!="" || $GSM900_gain1=="" && $GSM900_ant1!=""){
			$crit_gain1="ANTENNA ERROR";
		}else if ($GSM1800_gain1-$GSM900_gain1<=2){	$crit_gain1="OK";}else{	$crit_gain1="NOT_OK";}
		if ($donor1=="no" && $donor4=="no")	{$crit_donor1="OK";}else{$crit_donor1="NOT_OK";}

		$antenna_data1="<td>$GSM1800_antheight1</td><td>$GSM900_antheight1</td>";
		$azimuth_data1="<td>$GSM1800_azi1</td><td>$GSM900_azi1</td>";
		$hor_data1="<td>$GSM1800_hor1</td><td>$GSM900_hor1</td>";
		$ver_data1="<td>$GSM1800_ver1</td><td>$GSM900_ver1</td>";
		$tilt_data1="<td>$GSM1800_tilt1</td><td>$GSM900_tilt1</td>";
		$gain_data1="<td>$GSM1800_gain1</td><td>$GSM900_gain1</td>";
		$donor_data1="<td>$donor1</td><td>$donor4</td>";


	}else if (abs($GSM1800_azi1-$GSM900_azi2)<=10 OR abs($GSM1800_azi1-$GSM900_azi2)>=350){
		$crit_azi1="OK";
		$crit1="Sector 1-5";
		$sec5="taken";
		if ($GSM1800_antheight1-$GSM900_antheight2<=10)	{$crit_antheight1="OK";}else{$crit_antheight1="NOT_OK";}
		if ($GSM1800_hor1-$GSM900_hor2<=10)	{$crit_hor1="OK";}else{$crit_hor1="NOT_OK";}
		if ($GSM1800_ver1-$GSM900_ver2<=3){$crit_ver1="OK";}else{$crit_ver1="NOT_OK";}
		if (abs($GSM900_tilt1-$GSM1800_tilt2)<=3){$crit_tilt1="OK";}else{$crit_tilt1="NOT_OK";}
		if ($GSM1800_gain1=="" && $GSM1800_ant2!="" || $GSM900_gain2=="" && $GSM900_ant2!=""){
			$crit_gain1="ANTENNA ERROR";
		}else if ($GSM1800_gain1-$GSM900_gain2<=2){	$crit_gain1="OK";}else{	$crit_gain1="NOT_OK";}
		if ($donor1=="no" && $donor5=="no")	{$crit_donor1="OK";}else{$crit_donor1="NOT_OK";}

		$antenna_data1="<td>$GSM1800_antheight1</td><td>$GSM900_antheight2</td>";
		$azimuth_data1="<td>$GSM1800_azi1</td><td>$GSM900_azi2</td>";
		$hor_data1="<td>$GSM1800_hor1</td><td>$GSM900_hor2</td>";
		$ver_data1="<td>$GSM1800_ver1</td><td>$GSM900_ver2</td>";
		$tilt_data1="<td>$GSM1800_tilt1</td><td>$GSM900_tilt2</td>";
		$gain_data1="<td>$GSM1800_gain1</td><td>$GSM900_gain2</td>";
		$donor_data1="<td>$donor1</td><td>$donor5</td>";

	}else if (abs($GSM1800_azi1-$GSM900_azi3)<=10 OR abs($GSM1800_azi1-$GSM900_azi3)>=350){
		$crit_azi1="OK";
		$crit1="Sector 1-6";
		$sec6="taken";
		if ($GSM1800_antheight1-$GSM900_antheight3<=10)	{$crit_antheight1="OK";}else{$crit_antheight1="NOT_OK";}
		if ($GSM1800_hor1-$GSM900_hor3<=10)	{$crit_hor1="OK";}else{$crit_hor1="NOT_OK";}
		if ($GSM1800_ver1-$GSM900_ver3<=3){$crit_ver1="OK";}else{$crit_ver1="NOT_OK";}
		if (abs($GSM900_tilt1-$GSM1800_tilt3)<=3){$crit_tilt1="OK";}else{$crit_tilt1="NOT_OK";}
		if ($GSM1800_gain1=="" && $GSM1800_ant3!="" || $GSM900_gain3=="" && $GSM900_ant3!=""){
			$crit_gain1="ANTENNA ERROR";
		}else if ($GSM1800_gain1-$GSM900_gain3<=2){	$crit_gain1="OK";}else{	$crit_gain1="NOT_OK";}
		if ($donor1=="no" && $donor6=="no")	{$crit_donor1="OK";}else{$crit_donor1="NOT_OK";}

		$antenna_data1="<td>$GSM1800_antheight1</td><td>$GSM900_antheight3</td>";
		$azimuth_data1="<td>$GSM1800_azi1</td><td>$GSM900_azi3</td>";
		$hor_data1="<td>$GSM1800_hor1</td><td>$GSM900_hor3</td>";
		$ver_data1="<td>$GSM1800_ver1</td><td>$GSM900_ver3</td>";
		$tilt_data1="<td>$GSM1800_tilt1</td><td>$GSM900_tilt3</td>";
		$gain_data1="<td>$GSM1800_gain1</td><td>$GSM900_gain3</td>";
		$donor_data1="<td>$donor1</td><td>$donor6</td>";

	}else{
		$crit1="Sector 1";
		$crit_azi1="NOT_OK";
		$crit_antheight1="NOT_OK";
		$crit_tilt1="NOT_OK";
		$crit_ver1="NOT_OK";
		$crit_hor1="NOT_OK";
		$crit_donor1="NOT_OK";
		$crit_gain1="NOT_OK";
		$antenna_data1="<td>$GSM1800_antheight1</td><td>&nbsp;</td>";
		$azimuth_data1="<td>$GSM1800_azi1</td><td>&nbsp;</td>";
		$hor_data1="<td>$GSM1800_hor1</td><td>&nbsp;</td>";
		$ver_data1="<td>$GSM1800_ver1</td><td>&nbsp;</td>";
		$tilt_data1="<td>$GSM1800_tilt1</td><td>&nbsp;</td>";
		$gain_data1="<td>$GSM1800_gain1</td><td>&nbsp;</td>";
		$donor_data1="<td>$donor1</td><td>&nbsp;</td>";
	}
}else{
		if ($sec4!="taken"){
			$crit1="Sector 4";
			$crit_azi1="NA";
			$crit_antheight1="NA";
			$crit_tilt1="NA";
			$crit_ver1="NA";
			$crit_hor1="NA";
			$crit_donor1="NA";
			$crit_gain1="NA";
			$antenna_data1="<td>$GSM1800_antheight2</td><td>$GSM900_antheight1</td>";
			$azimuth_data1="<td>$GSM1800_azi2</td><td>$GSM900_azi1</td>";
			$hor_data1="<td>$GSM1800_hor2</td><td>$GSM900_hor1</td>";
			$ver_data1="<td>$GSM1800_ver2</td><td>$GSM900_ver1</td>";
			$tilt_data1="<td>$GSM1800_tilt2</td><td>$GSM900_tilt1</td>";
			$gain_data1="<td>$GSM1800_gain2</td><td>$GSM900_gain1</td>";
			$donor_data1="<td>$donor2</td><td>$donor4</td>";

		}elseif ($sec5!="taken"){
			$crit1="Sector 5";
			$crit_azi1="NA";
			$crit_antheight1="NA";
			$crit_tilt1="NA";
			$crit_ver1="NA";
			$crit_hor1="NA";
			$crit_donor1="NA";
			$crit_gain1="NA";
			$antenna_data1="<td>$GSM1800_antheight3</td><td>$GSM900_antheight2</td>";
			$azimuth_data1="<td>$GSM1800_azi3</td><td>$GSM900_azi2</td>";
			$hor_data1="<td>$GSM1800_hor3</td><td>$GSM900_hor2</td>";
			$ver_data1="<td>$GSM1800_ver3</td><td>$GSM900_ver2</td>";
			$tilt_data1="<td>$GSM1800_tilt3</td><td>$GSM900_tilt2</td>";
			$gain_data1="<td>$GSM1800_gain3</td><td>$GSM900_gain2</td>";
			$donor_data1="<td>$donor3</td><td>$donor5</td>";
		}elseif ($sec6!="taken"){
			$crit1="Sector 6";
			$crit_azi1="NA";
			$crit_antheight1="NA";
			$crit_tilt1="NA";
			$crit_ver1="NA";
			$crit_hor1="NA";
			$crit_donor1="NA";
			$crit_gain1="NA";
			$antenna_data1="<td>$GSM1800_antheight3</td><td>$GSM900_antheight3</td>";
			$azimuth_data1="<td>$GSM1800_azi3</td><td>$GSM900_azi3</td>";
			$hor_data1="<td>$GSM1800_hor3</td><td>$GSM900_hor3</td>";
			$ver_data1="<td>$GSM1800_ver3</td><td>$GSM900_ver3</td>";
			$tilt_data1="<td>$GSM1800_tilt3</td><td>$GSM900_tilt3</td>";
			$gain_data1="<td>$GSM1800_gain3</td><td>$GSM900_gain3</td>";
			$donor_data1="<td>$donor3</td><td>$donor6</td>";
		}
}

if ($GSM1800_azi2!=""){
	if (abs($GSM1800_azi2-$GSM900_azi2)<=10 OR abs($GSM1800_azi2-$GSM900_azi2)>=350){
		$crit_azi2="OK";
		$crit2="Sector 2-5";
		$sec5="taken";
		if ($GSM1800_antheight2-$GSM900_antheight2<=10)	{$crit_antheight2="OK";}else{$crit_antheight2="NOT_OK";}
		if ($GSM1800_hor2-$GSM900_hor2<=10)	{$crit_hor2="OK";}else{$crit_hor2="NOT_OK";}
		if ($GSM1800_ver2-$GSM900_ver2<=3){$crit_ver2="OK";}else{$crit_ver2="NOT_OK";}
		if (abs($GSM900_tilt2-$GSM1800_tilt2)<=3){$crit_tilt2="OK";}else{$crit_tilt2="NOT_OK";}
		if ($GSM1800_gain2=="" && $GSM1800_ant2!="" || $GSM900_gain2=="" && $GSM900_ant2!=""){
			$crit_gain2="ANTENNA ERROR";
		}else if ($GSM1800_gain2-$GSM900_gain2<=2){	$crit_gain2="OK";}else{	$crit_gain2="NOT_OK";}
		if ($donor2=="no" && $donor5=="no")	{$crit_donor2="OK";}else{$crit_donor2="NOT_OK";}

		$antenna_data2="<td>$GSM1800_antheight2</td><td>$GSM900_antheight2</td>";
		$azimuth_data2="<td>$GSM1800_azi2</td><td>$GSM900_azi2</td>";
		$hor_data2="<td>$GSM1800_hor2</td><td>$GSM900_hor2</td>";
		$ver_data2="<td>$GSM1800_ver2</td><td>$GSM900_ver2</td>";
		$tilt_data2="<td>$GSM1800_tilt2</td><td>$GSM900_tilt2</td>";
		$gain_data2="<td>$GSM1800_gain2</td><td>$GSM900_gain2</td>";
		$donor_data2="<td>$donor2</td><td>$donor5</td>";

	}else if (abs($GSM1800_azi2-$GSM900_azi3)<=10 OR abs($GSM1800_azi2-$GSM900_azi3)>=350){
		$crit_azi2="OK";
		$crit2="Sector 2-6";
		$sec6="taken";
		if ($GSM1800_antheight2-$GSM900_antheight3<=10)	{$crit_antheight2="OK";}else{$crit_antheight2="NOT_OK";}
		if ($GSM1800_hor2-$GSM900_hor3<=10)	{$crit_hor2="OK";}else{$crit_hor2="NOT_OK";}
		if ($GSM1800_ver2-$GSM900_ver3<=3){$crit_ver2="OK";}else{$crit_ver2="NOT_OK";}
		if (abs($GSM900_tilt2-$GSM1800_tilt3)<=3){$crit_tilt2="OK";}else{$crit_tilt2="NOT_OK";}
		if ($GSM1800_gain2=="" && $GSM1800_ant3!="" || $GSM900_gain3=="" && $GSM900_ant3!=""){
			$crit_gain2="ANTENNA ERROR";
		}else if ($GSM1800_gain2-$GSM900_gain3<=2){	$crit_gain2="OK";}else{	$crit_gain2="NOT_OK";}
		if ($donor2=="no" && $donor6=="no")	{$crit_donor2="OK";}else{$crit_donor2="NOT_OK";}

		$antenna_data2="<td>$GSM1800_antheight2</td><td>$GSM900_antheight3</td>";
		$azimuth_data2="<td>$GSM1800_azi2</td><td>$GSM900_azi3</td>";
		$hor_data2="<td>$GSM1800_hor2</td><td>$GSM900_hor3</td>";
		$ver_data2="<td>$GSM1800_ver2</td><td>$GSM900_ver3</td>";
		$tilt_data2="<td>$GSM1800_tilt2</td><td>$GSM900_tilt3</td>";
		$gain_data2="<td>$GSM1800_gain2</td><td>$GSM900_gain3</td>";
		$donor_data2="<td>$donor2</td><td>$donor6</td>";

	}else if (abs($GSM1800_azi2-$GSM900_azi1)<=10 OR abs($GSM1800_azi2-$GSM900_azi1)>=350){
		$crit_azi2="OK";
		$crit2="Sector 2-4";
		$sec4="taken";
		if ($GSM1800_antheight2-$GSM900_antheight1<=10)	{$crit_antheight2="OK";}else{$crit_antheight2="NOT_OK";}
		if ($GSM1800_hor2-$GSM900_hor1<=10)	{$crit_hor2="OK";}else{$crit_hor2="NOT_OK";}
		if ($GSM1800_ver2-$GSM900_ver1<=3){$crit_ver2="OK";}else{$crit_ver2="NOT_OK";}
		if (abs($GSM900_tilt2-$GSM1800_tilt1)<=3){$crit_tilt2="OK";}else{$crit_tilt2="NOT_OK";}
		if ($GSM1800_gain2=="" && $GSM1800_ant1!="" || $GSM900_gain1=="" && $GSM900_ant1!=""){
			$crit_gain2="ANTENNA ERROR";
		}else if ($GSM1800_gain2-$GSM900_gain1<=2){	$crit_gain2="OK";}else{	$crit_gain2="NOT_OK";}
		if ($donor2=="no" && $donor4=="no")	{$crit_donor2="OK";}else{$crit_donor2="NOT_OK";}

		$antenna_data2="<td>$GSM1800_antheight2</td><td>$GSM900_antheight1</td>";
		$azimuth_data2="<td>$GSM1800_azi2</td><td>$GSM900_azi1</td>";
		$hor_data2="<td>$GSM1800_hor2</td><td>$GSM900_hor1</td>";
		$ver_data2="<td>$GSM1800_ver2</td><td>$GSM900_ver1</td>";
		$tilt_data2="<td>$GSM1800_tilt2</td><td>$GSM900_tilt1</td>";
		$gain_data2="<td>$GSM1800_gain2</td><td>$GSM900_gain1</td>";
		$donor_data2="<td>$donor2</td><td>$donor4</td>";

	}else{
		$crit2="Sector 2";
		$crit_azi2="NOT_OK";
		$crit_antheight2="NOT_OK";
		$crit_tilt2="NOT_OK";
		$crit_ver2="NOT_OK";
		$crit_hor2="NOT_OK";
		$crit_donor2="NOT_OK";
		$crit_gain2="NOT_OK";
		$antenna_data2="<td>$GSM1800_antheight2</td><td>&nbsp;</td>";
		$azimuth_data2="<td>$GSM1800_azi2</td><td>&nbsp;</td>";
		$hor_data2="<td>$GSM1800_hor2</td><td>&nbsp;</td>";
		$ver_data2="<td>$GSM1800_ver2</td><td>&nbsp;</td>";
		$tilt_data2="<td>$GSM1800_tilt2</td><td>&nbsp;</td>";
		$gain_data2="<td>$GSM1800_gain2</td><td>&nbsp;</td>";
		$donor_data2="<td>$donor2</td><td>&nbsp;</td>";
	}
}else{
		if ($sec4!="taken"){
			$crit2="Sector 4";
			$crit_azi2="NA";
			$crit_antheight2="NA";
			$crit_tilt2="NA";
			$crit_ver2="NA";
			$crit_hor2="NA";
			$crit_donor2="NA";
			$crit_gain2="NA";
			$antenna_data2="<td>$GSM1800_antheight2</td><td>$GSM900_antheight1</td>";
			$azimuth_data2="<td>$GSM1800_azi2</td><td>$GSM900_azi1</td>";
			$hor_data2="<td>$GSM1800_hor2</td><td>$GSM900_hor1</td>";
			$ver_data2="<td>$GSM1800_ver2</td><td>$GSM900_ver1</td>";
			$tilt_data2="<td>$GSM1800_tilt2</td><td>$GSM900_tilt1</td>";
			$gain_data2="<td>$GSM1800_gain2</td><td>$GSM900_gain1</td>";
			$donor_data2="<td>$donor2</td><td>$donor4</td>";

		}elseif ($sec5!="taken"){
			$crit2="Sector 5";
			$crit_azi2="NA";
			$crit_antheight2="NA";
			$crit_tilt2="NA";
			$crit_ver2="NA";
			$crit_hor2="NA";
			$crit_donor2="NA";
			$crit_gain2="NA";
			$antenna_data2="<td>$GSM1800_antheight3</td><td>$GSM900_antheight2</td>";
			$azimuth_data2="<td>$GSM1800_azi3</td><td>$GSM900_azi2</td>";
			$hor_data2="<td>$GSM1800_hor3</td><td>$GSM900_hor2</td>";
			$ver_data2="<td>$GSM1800_ver3</td><td>$GSM900_ver2</td>";
			$tilt_data2="<td>$GSM1800_tilt3</td><td>$GSM900_tilt2</td>";
			$gain_data2="<td>$GSM1800_gain3</td><td>$GSM900_gain2</td>";
			$donor_data2="<td>$donor3</td><td>$donor5</td>";
		}elseif ($sec6!="taken"){
			$crit2="Sector 6";
			$crit_azi2="NA";
			$crit_antheight2="NA";
			$crit_tilt2="NA";
			$crit_ver2="NA";
			$crit_hor2="NA";
			$crit_donor2="NA";
			$crit_gain2="NA";
			$antenna_data2="<td>$GSM1800_antheight3</td><td>$GSM900_antheight3</td>";
			$azimuth_data2="<td>$GSM1800_azi3</td><td>$GSM900_azi3</td>";
			$hor_data2="<td>$GSM1800_hor3</td><td>$GSM900_hor3</td>";
			$ver_data2="<td>$GSM1800_ver3</td><td>$GSM900_ver3</td>";
			$tilt_data2="<td>$GSM1800_tilt3</td><td>$GSM900_tilt3</td>";
			$gain_data2="<td>$GSM1800_gain3</td><td>$GSM900_gain3</td>";
			$donor_data2="<td>$donor3</td><td>$donor6</td>";
		}
}

if ($GSM1800_azi3!=""){
	if (abs($GSM1800_azi3-$GSM900_azi3)<=10 OR abs($GSM1800_azi3-$GSM900_azi3)>=350){
		$crit_azi3="OK";
		$crit3="Sector 3-6";
		$sec6="taken";
		if ($GSM1800_antheight3-$GSM900_antheight3<=10)	{$crit_antheight3="OK";}else{$crit_antheight3="NOT_OK";}
		if ($GSM1800_hor3-$GSM900_hor3<=10)	{$crit_hor3="OK";}else{$crit_hor3="NOT_OK";}
		if ($GSM1800_ver3-$GSM900_ver3<=3){$crit_ver3="OK";}else{$crit_ver3="NOT_OK";}
		if (abs($GSM900_tilt3-$GSM1800_tilt3)<=3){$crit_tilt3="OK";}else{$crit_tilt3="NOT_OK";}
		if ($GSM1800_gain3=="" && $GSM1800_ant3!="" || $GSM900_gain3=="" && $GSM900_ant3!=""){
			$crit_gain3="ANTENNA ERROR";
		}else if ($GSM1800_gain3-$GSM900_gain3<=2){	$crit_gain3="OK";}else{	$crit_gain3="NOT_OK";}
		if ($donor3=="no" && $donor6=="no")	{$crit_donor3="OK";}else{$crit_donor3="NOT_OK";}

		$antenna_data3="<td>$GSM1800_antheight3</td><td>$GSM900_antheight3</td>";
		$azimuth_data3="<td>$GSM1800_azi3</td><td>$GSM900_azi3</td>";
		$hor_data3="<td>$GSM1800_hor3</td><td>$GSM900_hor3</td>";
		$ver_data3="<td>$GSM1800_ver3</td><td>$GSM900_ver3</td>";
		$tilt_data3="<td>$GSM1800_tilt3</td><td>$GSM900_tilt3</td>";
		$gain_data3="<td>$GSM1800_gain3</td><td>$GSM900_gain3</td>";
		$donor_data3="<td>$donor3</td><td>$donor6</td>";

	}else if (abs($GSM1800_azi3-$GSM900_azi1)<=10 OR abs($GSM1800_azi3-$GSM900_azi1)>=350){
		$crit_azi3="OK";
		$crit3="Sector 3-4";
		$sec4="taken";
		if ($GSM1800_antheight3-$GSM900_antheight1<=10)	{$crit_antheight3="OK";}else{$crit_antheight3="NOT_OK";}
		if ($GSM1800_hor3-$GSM900_hor1<=10)	{$crit_hor3="OK";}else{$crit_hor3="NOT_OK";}
		if ($GSM1800_ver3-$GSM900_ver1<=3){$crit_ver3="OK";}else{$crit_ver3="NOT_OK";}
		if (abs($GSM900_tilt3-$GSM1800_tilt1)<=3){$crit_tilt3="OK";}else{$crit_tilt3="NOT_OK";}
		if ($GSM1800_gain3=="" && $GSM1800_ant1!="" || $GSM900_gain3=="" && $GSM900_ant3!=""){
			$crit_gain3="ANTENNA ERROR";
		}else if ($GSM1800_gain3-$GSM900_gain1<=2){	$crit_gain3="OK";}else{	$crit_gain3="NOT_OK";}
		if ($donor3=="no" && $donor4=="no")	{$crit_donor3="OK";}else{$crit_donor3="NOT_OK";}

		$antenna_data3="<td>$GSM1800_antheight3</td><td>$GSM900_antheight1</td>";
		$azimuth_data3="<td>$GSM1800_azi3</td><td>$GSM900_azi1</td>";
		$hor_data3="<td>$GSM1800_hor3</td><td>$GSM900_hor1</td>";
		$ver_data3="<td>$GSM1800_ver3</td><td>$GSM900_ver1</td>";
		$tilt_data3="<td>$GSM1800_tilt3</td><td>$GSM900_tilt1</td>";
		$gain_data3="<td>$GSM1800_gain3</td><td>$GSM900_gain1</td>";
		$donor_data3="<td>$donor3</td><td>$donor4</td>";

	}else if (abs($GSM1800_azi3-$GSM900_azi2)<=10 OR abs($GSM1800_azi3-$GSM900_azi2)>=350){
		$crit_azi3="OK";
		$crit3="Sector 3-5";
		$sec5="taken";
		if ($GSM1800_antheight3-$GSM900_antheight2<=10)	{$crit_antheight3="OK";}else{$crit_antheight3="NOT_OK";}
		if ($GSM1800_hor3-$GSM900_hor2<=10)	{$crit_hor3="OK";}else{$crit_hor3="NOT_OK";}
		if ($GSM1800_ver3-$GSM900_ver2<=3){$crit_ver3="OK";}else{$crit_ver3="NOT_OK";}
		if (abs($GSM900_tilt3-$GSM1800_tilt2)<=3){$crit_tilt3="OK";}else{$crit_tilt3="NOT_OK";}
		if ($GSM1800_gain3=="" && $GSM1800_ant2!="" || $GSM900_gain3=="" && $GSM900_ant3!=""){
			$crit_gain3="ANTENNA ERROR";
		}else if ($GSM1800_gain3-$GSM900_gain2<=2){	$crit_gain3="OK";}else{	$crit_gain3="NOT_OK";}
		if ($donor3=="no" && $donor5=="no")	{$crit_donor3="OK";}else{$crit_donor3="NOT_OK";}

		$antenna_data3="<td>$GSM1800_antheight3</td><td>$GSM900_antheight2</td>";
		$azimuth_data3="<td>$GSM1800_azi3</td><td>$GSM900_azi2</td>";
		$hor_data3="<td>$GSM1800_hor3</td><td>$GSM900_hor2</td>";
		$ver_data3="<td>$GSM1800_ver3</td><td>$GSM900_ver2</td>";
		$tilt_data3="<td>$GSM1800_tilt3</td><td>$GSM900_tilt2</td>";
		$gain_data3="<td>$GSM1800_gain3</td><td>$GSM900_gain2</td>";
		$donor_data3="<td>$donor3</td><td>$donor5</td>";

	}else{
		$crit3="Sector 3";
		$crit_azi3="NOT_OK";
		$crit_antheight3="NOT_OK";
		$crit_tilt3="NOT_OK";
		$crit_ver3="NOT_OK";
		$crit_hor3="NOT_OK";
		$crit_donor3="NOT_OK";
		$crit_gain3="NOT_OK";
		$antenna_data3="<td>$GSM1800_antheight3</td><td>&nbsp;</td>";
		$azimuth_data3="<td>$GSM1800_azi3</td><td>&nbsp;</td>";
		$hor_data3="<td>$GSM1800_hor3</td><td>&nbsp;</td>";
		$ver_data3="<td>$GSM1800_ver3</td><td>&nbsp;</td>";
		$tilt_data3="<td>$GSM1800_tilt3</td><td>&nbsp;</td>";
		$gain_data3="<td>$GSM1800_gain3</td><td>&nbsp;</td>";
		$donor_data3="<td>$donor3</td><td>&nbsp;</td>";
	}
}else{
		if ($sec4!="taken"){
			$crit3="Sector 4";
			$crit_azi3="NA";
			$crit_antheight3="NA";
			$crit_tilt3="NA";
			$crit_ver3="NA";
			$crit_hor3="NA";
			$crit_donor3="NA";
			$crit_gain3="NA";
			$antenna_data3="<td>$GSM1800_antheight3</td><td>$GSM900_antheight1</td>";
			$azimuth_data3="<td>$GSM1800_azi3</td><td>$GSM900_azi1</td>";
			$hor_data3="<td>$GSM1800_hor3</td><td>$GSM900_hor1</td>";
			$ver_data3="<td>$GSM1800_ver3</td><td>$GSM900_ver1</td>";
			$tilt_data3="<td>$GSM1800_tilt3</td><td>$GSM900_tilt1</td>";
			$gain_data3="<td>$GSM1800_gain3</td><td>$GSM900_gain1</td>";
			$donor_data3="<td>$donor3</td><td>$donor4</td>";

		}elseif ($sec5!="taken"){
			$crit3="Sector 5";
			$crit_azi3="NA";
			$crit_antheight3="NA";
			$crit_tilt3="NA";
			$crit_ver3="NA";
			$crit_hor3="NA";
			$crit_donor3="NA";
			$crit_gain3="NA";
			$antenna_data3="<td>$GSM1800_antheight3</td><td>$GSM900_antheight2</td>";
			$azimuth_data3="<td>$GSM1800_azi3</td><td>$GSM900_azi2</td>";
			$hor_data3="<td>$GSM1800_hor3</td><td>$GSM900_hor2</td>";
			$ver_data3="<td>$GSM1800_ver3</td><td>$GSM900_ver2</td>";
			$tilt_data3="<td>$GSM1800_tilt3</td><td>$GSM900_tilt2</td>";
			$gain_data3="<td>$GSM1800_gain3</td><td>$GSM900_gain2</td>";
			$donor_data3="<td>$donor3</td><td>$donor5</td>";
		}elseif ($sec6!="taken"){
			$crit3="Sector 6";
			$crit_azi3="NA";
			$crit_antheight3="NA";
			$crit_tilt3="NA";
			$crit_ver3="NA";
			$crit_hor3="NA";
			$crit_donor3="NA";
			$crit_gain3="NA";
			$antenna_data3="<td>$GSM1800_antheight3</td><td>$GSM900_antheight3</td>";
			$azimuth_data3="<td>$GSM1800_azi3</td><td>$GSM900_azi3</td>";
			$hor_data3="<td>$GSM1800_hor3</td><td>$GSM900_hor3</td>";
			$ver_data3="<td>$GSM1800_ver3</td><td>$GSM900_ver3</td>";
			$tilt_data3="<td>$GSM1800_tilt3</td><td>$GSM900_tilt3</td>";
			$gain_data3="<td>$GSM1800_gain3</td><td>$GSM900_gain3</td>";
			$donor_data3="<td>$donor3</td><td>$donor6</td>";
		}
	}
//end THE CRITERIA

//... Same for other sectors

if ($GSM1800_azi1!="" && $crit1!="Sector 1"){
	if ($crit_antheight1=="OK" && $crit_azi1=="OK" && $crit_gain1=="OK" && $crit_hor1=="OK" && $crit_ver1=="OK" && $crit_tilt1=="OK" && $crit_donor1=="OK"){
		$bandtype_sec1="Camp-on-EGSM";
	}else{
		$bandtype_sec1="Non-Camp-on-EGSM";
	}
}

if ($GSM1800_azi2!="" && $crit2!="Sector 2"){
	if ($crit_antheight2=="OK" && $crit_azi2=="OK" && $crit_gain2=="OK" && $crit_hor2=="OK" && $crit_ver2=="OK" && $crit_tilt2=="OK" && $crit_donor2=="OK"){
		$bandtype_sec2="Camp-on-EGSM";
	}else{
		$bandtype_sec2="Non-Camp-on-EGSM";

	}
}

if ($GSM1800_azi3!="" && $crit3!="Sector 3"){
	if ($crit_antheight3=="OK" && $crit_azi3=="OK" && $crit_gain3=="OK" && $crit_hor3=="OK" && $crit_ver3=="OK" && $crit_tilt3=="OK" && $crit_donor3=="OK"){
		$bandtype_sec3="Camp-on-EGSM";
	}else{
		$bandtype_sec3="Non-Camp-on-EGSM";
	}
}
?>
<br><br><br>
<table border="0" align="center">
<tr>
	<td>&nbsp;</td>
	<td colspan="2" class="sector"><?=$crit1?></td>
	<td>&nbsp;</td>
	<td colspan="2" class="sector"><?=$crit2?></td>
	<td>&nbsp;</td>
	<td colspan="2" class="sector"><?=$crit3?></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td class="band"><b>GSM1800</b></td>
	<td class="band"><b>GSM900</b></td>
	<td>&nbsp;</td>
	<td class="band"><b>GSM1800</b></td>
	<td class="band"><b>GSM900</b></td>
	<td>&nbsp;</td>
	<td class="band"><b>GSM1800</b></td>
	<td class="band"><b>GSM900</b></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<th class="parameter"><b>AZIMUTH</b><br><font size="1">abs(AZIMUTH GSM1800 - AZIMUTH GSM900)<=10 OR <br>abs(AZIMUTH GSM1800 - AZIMUTH GSM900)>=360</font></a></th>
	<?=$azimuth_data1?>
	<td class="<?=$crit_azi1?>"><?=$crit_azi1?></td>
	<?=$azimuth_data2?>
	<td class="<?=$crit_azi2?>"><?=$crit_azi2?></td>
	<?=$azimuth_data3?>
	<td class="<?=$crit_azi3?>"><?=$crit_azi3?></td>
</tr>
<tr>
	<th class="parameter"><b>ANTENNA HEIGHT</b><br><font size="1">HEIGHT GSM1800 - HEIGHT GSM900<=10</font></a></th>
	<?=$antenna_data1?>
	<td class="<?=$crit_antheight1?>"><?=$crit_antheight1?></td>
	<?=$antenna_data2?>
	<td class="<?=$crit_antheight2?>"><?=$crit_antheight2?></td>
	<?=$antenna_data3?>
	<td class="<?=$crit_antheight3?>"><?=$crit_antheight3?></td>
</tr>
<tr>
	<th class="parameter"><b>TILT</b><br><font size="1">abs(TILT GSM900 - TILT GSM1800)<=3</font></a></th>
	<?=$tilt_data1?>
	<td class="<?=$crit_tilt1?>"><?=$crit_tilt1?></td>
	<?=$tilt_data2?>
	<td class="<?=$crit_tilt2?>"><?=$crit_tilt2?></td>
	<?=$tilt_data3?>
	<td class="<?=$crit_tilt3?>"><?=$crit_tilt3?></td>
</tr>
<tr>
	<th class="parameter"><b>GAIN</b><br><font size="1">(GAIN GSM1800 - GAIN GSM900)<=2</font></a></th>
	<?=$gain_data1?>
	<td class="<?=$crit_gain1?>"><?=$crit_gain1?></td>
	<?=$gain_data2?>
	<td class="<?=$crit_gain2?>"><?=$crit_gain2?></td>
	<?=$gain_data3?>
	<td class="<?=$crit_gain3?>"><?=$crit_gain3?></td>
</tr>
<tr>
	<th class="parameter"><b>VERTICAL</b><br><font size="1">VERTICAL GSM1800 - VERTICAL GSM900<=3</font></a></th>
	<?=$ver_data1?>
	<td class="<?=$crit_ver1?>"><?=$crit_ver1?></td>
	<?=$ver_data2?>
	<td class="<?=$crit_ver2?>"><?=$crit_ver2?></td>
	<?=$ver_data3?>
	<td class="<?=$crit_ver3?>"><?=$crit_ver3?></td>
</tr>
<tr>
	<th class="parameter"><b>HORIZONTAL</b><br><font size="1">HORIZONTAL GSM1800 - HORIZONTAL GSM900<=10</font></a></th>
	<?=$hor_data1?>
	<td class="<?=$crit_hor1?>"><?=$crit_hor1?></td>
	<?=$hor_data2?>
	<td class="<?=$crit_hor2?>"><?=$crit_hor2?></td>
	<?=$hor_data3?>
	<td class="<?=$crit_hor3?>"><?=$crit_hor3?></td>
</tr>
<tr>
	<th class="parameter"><b>DONOR CELL</b><br>&nbsp;</th>
	<?=$donor_data1?>
	<td class="<?=$crit_donor1?>"><?=$crit_donor1?></td>
	<?=$donor_data2?>
	<td class="<?=$crit_donor2?>"><?=$crit_donor2?></td>
	<?=$donor_data3?>
	<td class="<?=$crit_donor3?>"><?=$crit_donor3?></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="2" class="bandtype"><?=$bandtype_sec1?></td>
	<td>&nbsp;</td>
	<td colspan="2" class="bandtype"><?=$bandtype_sec2?></td>
	<td>&nbsp;</td>
	<td colspan="2" class="bandtype"><?=$bandtype_sec3?></td>
	<td>&nbsp;</td>
</tr>
</table>
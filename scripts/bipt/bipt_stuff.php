<?
require_once($_SERVER['DOCUMENT_ROOT']."/include/config.php");
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners","");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$conn_mysql = mysql_connect("$mysql_host", "$mysql_user", "$mysql_password");
mysql_select_db("MSCdata",$conn_mysql);


if ($cab==''){
	$cab="01";
}


if ($_SESSION['PREVIEW']!="yes"){
	if ($_SESSION['STATUS_GSM900']=="BSDS AS BUILD"){
		$_SESSION['table_view']="_BUILD";
	}else if ($_SESSION['STATUS_GSM900']=="BSDS FUNDED"){
		$_SESSION['table_view']="_FUND";
	}elseif ($_SESSION['STATUS_GSM900']=="SITE FUNDED"){
		$_SESSION['table_view']="_POST";
	}else if ($_SESSION['PREVIEW']=="yes"){
		$_SESSION['table_view']="";
	}
}else if ($_SESSION['PREVIEW']=="yes"){
	$_SESSION['table_view']="";
}

$check_planned_exists_GSM900=check_planned_exists("GSM900",'allsec');

if ($check_planned_exists_GSM900=="1"){
	for ($i = 1; $i <= 4; $i++) {
		$planneddata=get_data("GSM900",$i,"PLANNED",'');

		$GSM900_TRU_INST1_1="GSM900_TRU_INST1_1_$i";
		$$GSM900_TRU_INST1_1=$planneddata['TRU_INST1_1'][0];
		$GSM900_TRU_TYPE1_1="GSM900_TRU_TYPE1_1_$i";
		$$GSM900_TRU_TYPE1_1=$planneddata['TRU_TYPE1_1'][0];

		$GSM900_TRU_INST1_2="GSM900_TRU_INST1_2_$i";
		$$GSM900_TRU_INST1_2=$planneddata['TRU_INST1_2'][0];
		$GSM900_TRU_TYPE1_2="GSM900_TRU_TYPE1_2_$i";
		$$GSM900_TRU_TYPE1_2=$planneddata['TRU_TYPE1_2'][0];

		$GSM900_TRU_INST1_3="GSM900_TRU_INST1_3_$i";
		$$GSM900_TRU_INST1_3=$planneddata['TRU_INST1_3'][0];
		$GSM900_TRU_TYPE1_3="GSM900_TRU_TYPE1_3_$i";
		$$GSM900_TRU_TYPE1_3=$planneddata['TRU_TYPE1_3'][0];

		$GSM900_CONFIG="GSM900_CONFIG_$i";
		$$GSM900_CONFIG=$planneddata['CONFIG'][0];

		$GSM900_FREQ_ACTIVE1="GSM900_FREQ_ACTIVE1_$i";
		$$GSM900_FREQ_ACTIVE1=$planneddata['FREQ_ACTIVE1'][0];
		$GSM900_FREQ_ACTIVE2="GSM900_FREQ_ACTIVE2_$i";
		$$GSM900_FREQ_ACTIVE2=$planneddata['FREQ_ACTIVE2'][0];
		$GSM900_FREQ_ACTIVE3="GSM900_FREQ_ACTIVE3_$i";
		$$GSM900_FREQ_ACTIVE3=$planneddata['FREQ_ACTIVE3'][0];

		$GSM900_STATE="GSM900_STATE_$i";
		$GSM900_STATE=$planneddata['STATE'][0];
		$GSM900_TMA="GSM900_TMA_$i";
		$$GSM900_TMA=$planneddata['TMA'][0];
		$GSM900_AZI="GSM900_AZI_$i";
		$$GSM900_AZI=$planneddata['AZI'][0];
		$GSM900_ANTTYPE1="GSM900_ANTTYPE1_$i";
		$$GSM900_ANTTYPE1=$planneddata['ANTTYPE1'][0];
		$GSM900_ELECTILT1="GSM900_ELECTILT1_$i";
		$$GSM900_ELECTILT1=$planneddata['ELECTILT1'][0];
		$GSM900_MECHTILT1="GSM900_MECHTILT1_$i";
		$$GSM900_MECHTILT1=$planneddata['MECHTILT1'][0];
		$GSM900_MECHTILT_DIR1="GSM900_MECHTILT_DIR1_$i";
		$$GSM900_MECHTILT_DIR1=$planneddata['MECHTILT_DIR1'][0];
		$GSM900_ANTHEIGHT1="GSM900_ANTHEIGHT1_$i";
		$$GSM900_ANTHEIGHT1=$planneddata['ANTHEIGHT1'][0];
		$GSM900_ANTTYPE2="GSM900_ANTTYPE2_$i";
		$$GSM900_ANTTYPE2=$planneddata['ANTTYPE2'][0];
		$GSM900_ELECTILT2="GSM900_ELECTILT2_$i";
		$$GSM900_ELECTILT2=$planneddata['ELECTILT2'][0];
		$GSM900_MECHTILT2="GSM900_MECHTILT2_$i";
		$$GSM900_MECHTILT2=$planneddata['MECHTILT2'][0];
		$GSM900_MECHTILT_DIR2="GSM900_MECHTILT_DIR2_$i";
		$$GSM900_MECHTILT_DIR2=$planneddata['MECHTILT_DIR2'][0];
		$GSM900_ANTHEIGHT2="GSM900_ANTHEIGHT2_$i";
		$$GSM900_ANTHEIGHT2=$planneddata['ANTHEIGHT2'][0];
		$GSM900_FEEDERLEN="GSM900_FEEDERLEN_$i";
		$$GSM900_FEEDERLEN=$planneddata['FEEDERLEN'][0];
		$GSM900_FEEDER="GSM900_FEEDER_$i";
		$$GSM900_FEEDER=$planneddata['FEEDER'][0];
		$GSM900_COMB="GSM900_COMB_$i";
		$$GSM900_COMB=$planneddata['COMB'][0];
		$GSM900_DCBLOCK="GSM900_DCBLOCK_$i";
		$$GSM900_DCBLOCK=$planneddata['DCBLOCK'][0];
	}

	$planneddata_additional=get_data("GSM900","","ADDITIONAL_PLANNED",'');
	$GSM900_COMMENTS=$planneddata_additional['COMMENTS'][0];
	$GSM900_CDUTYPE=$planneddata_additional['CDUTYPE'][0];
	$GSM900_CABTYPE=$planneddata_additional['CABTYPE'][0];
	$GSM900_NR_OF_CAB=$planneddata_additional['NR_OF_CAB'][0];
	$GSM900_BBS=$planneddata_additional['BBS'][0];
	$GSM900_DXUTYPE=$planneddata_additional['DXUTYPE'][0];

}else{

	$currentdata=get_data("GSM900","","CURRENT_ASSET",""); //Get data from Asset
	$AMOUNT_ASSET_INFO=count($currentdata['SITEKEY']);
	//echo "$AMOUNT_ASSET_INFO"; //Amount of sectors

	$j=1;
	$start="yes";
	for ($i=0;$i<$AMOUNT_ASSET_INFO;$i++){

		$SECTORID=$currentdata['SECTORID'][$i];
		$ID=substr($SECTORID,-1);
		$last_sect=substr($SECTORID,-1);

		if ($last_sect!=$vorige){
			$k=1;
			//echo "1 $j<br>";
			if ($start!="yes"){
				$j++;
			}else{
				$start="no";
			}

		}else{
			$k=2;
			//echo "2 $j<br>";
		}

		$CONFIG="CONFIG_$j";
		$$CONFIG=$currentdata['CELLEQUIPMENT'][$i];

		$STATE="STATE_$j";
		$$STATE=get_config($currentdata['CELLSTATUS'][$i]);

		$AZI="AZI_$j";
		$$AZI=$currentdata['AZIMUTH'][$i];

		$FEEDER="FEEDER_$j";
		$$FEEDER=$currentdata['FEEDERKEY'][$i];

		$FEEDERLEN="FEEDERLEN_$j";
		$$FEEDERLEN=$currentdata['FEEDERLENGTH'][$i];


		$ANTTYPE="ANTTYPE".$k."_".$j;
		$$ANTTYPE=$currentdata['ANTENNATYPE'][$i];

		$ANTHEIGHT="ANTHEIGHT".$k."_".$j;
		$$ANTHEIGHT=round($currentdata['ANTENNAHEIGHT'][$i],2);

		$MECHTILT="MECHTILT".$k."_".$j;
		$MECHTILT_DIR="MECHTILT_DIR1_".$j;
		$$MECHTILT=trim(abs($currentdata['DOWNTILT'][$i]));
		$$MECHTILT_DIR=get_mechtilt_dir($currentdata['DOWNTILT'][$i]);

		$ELECTILT="ELECTILT".$k."_".$j;
		$$ELECTILT=$currentdata['ANTENNATYPE'][$i];
		$$ELECTILT=substr($$ANTTYPE, -2);
		if (!is_numeric($$ELECTILT)){
			$$ELECTILT=substr($$ANTTYPE, -1);
			if (!is_numeric($$ELECTILT)){
				$$ELECTILT=substr($$ANTTYPE, -3,1);
			}
		}

		$vorige=$last_sect;
	}

}

	$freq=get_freq("4");
	$GSM900_FREQ_ACTIVE_1=$freq[1];
	$GSM900_CDUTYPE=$freq[0];
	$freq=get_freq("5");
	$GSM900_FREQ_ACTIVE_2=$freq[1];
	$freq=get_freq("6");
	$GSM900_FREQ_ACTIVE_3=$freq[1];

$j=4;
for ($i = 1; $i <= 3; $i++) {
	$GSM900_CONFIG="GSM900_CONFIG_$i";
	$GSM900_FREQ_ACTIVE="GSM900_FREQ_ACTIVE_$i";
	$GSM900_MECHTILT_DIR1="GSM900_MECHTILT_DIR1_$i";
	$GSM900_MECHTILT1="GSM900_MECHTILT1_$i";
	$GSM900_MECHTILT_DIR2="GSM900_MECHTILT_DIR2_$i";
	$GSM900_MECHTILT2="GSM900_MECHTILT2_$i";
	$j++;

	//echo "$AntennaEntryPowerBm =". $$AntennaEntryPowerBm ."<br>";

	if ($$GSM900_FREQ_ACTIVE < 2){	$$GSM900_FREQ_ACTIVE=2;	}
	if ($$GSM900_MECHTILT_DIR1=="DOWNTILT" || $$GSM900_MECHTILT_DIR1=="Downtilt"){ $$GSM900_MECHTILT_DIR1="-"; }
	if ($$GSM900_MECHTILT_DIR2=="DOWNTILT" || $$GSM900_MECHTILT_DIR2=="Downtilt"){ $$GSM900_MECHTILT_DIR2="-"; }
	if ($$GSM900_MECHTILT1=="-"){ $$GSM900_MECHTILT1="0"; }
	if ($$GSM900_MECHTILT2=="-"){ $$GSM900_MECHTILT2="0"; }
	if ($$GSM900_MECHTILT_DIR1=="-" && $$GSM900_MECHTILT1=="0"){	$$GSM900_MECHTILT_DIR1=""; }
	if ($$GSM900_MECHTILT_DIR2=="-" && $$GSM900_MECHTILT2=="0"){	$$GSM900_MECHTILT_DIR2=""; }
	if ($$GSM900_MECHTILT_DIR1=="UPTILT" || $$GSM900_MECHTILT_DIR1=="Uptilt"){ $$GSM900_MECHTILT_DIR1=""; }
	if ($$GSM900_MECHTILT_DIR2=="UPTILT" || $$GSM900_MECHTILT_DIR2=="Uptilt"){ $$GSM900_MECHTILT_DIR2=""; }

}

/******************************************************* PLANNED 1800 **********************************************************/
if ($_SESSION['PREVIEW']!="yes"){
	if ($_SESSION['STATUS_GSM1800']=="BSDS AS BUILD"){
		$_SESSION['table_view']="_BUILD";
	}else if ($_SESSION['STATUS_GSM1800']=="BSDS FUNDED"){
		$_SESSION['table_view']="_FUND";
	}elseif ($_SESSION['STATUS_GSM1800']=="SITE FUNDED"){
		$_SESSION['table_view']="_POST";
	}else{
		$_SESSION['table_view']="";
	}
}else if ($_SESSION['PREVIEW']=="yes"){
	$_SESSION['table_view']="";
}

$check_planned_exists_GSM1800=check_planned_exists("GSM1800",'allsec');
//echo "check_planned_exists_GSM1800 $check_planned_exists_GSM1800";

if ($check_planned_exists_GSM1800=="1"){
	for ($i = 1; $i <= 3; $i++) {
		$planneddata=get_data("GSM1800",$i,"PLANNED",'');

		$GSM1800_TRU_INST1_1="GSM1800_TRU_INST1_1_$i";
		$$GSM1800_TRU_INST1_1=$planneddata['TRU_INST1_1'][0];
		$GSM1800_TRU_TYPE1_1="GSM1800_TRU_TYPE1_1_$i";
		$$GSM1800_TRU_TYPE1_1=$planneddata['TRU_TYPE1_1'][0];

		$GSM1800_TRU_INST1_2="GSM1800_TRU_INST1_2_$i";
		$$GSM1800_TRU_INST1_2=$planneddata['TRU_INST1_2'][0];
		$GSM1800_TRU_TYPE1_2="GSM1800_TRU_TYPE1_2_$i";
		$$GSM1800_TRU_TYPE1_2=$planneddata['TRU_TYPE1_2'][0];

		$GSM1800_TRU_INST1_3="GSM1800_TRU_INST1_3_$i";
		$$GSM1800_TRU_INST1_3=$planneddata['TRU_INST1_3'][0];
		$GSM1800_TRU_TYPE1_3="GSM1800_TRU_TYPE1_3_$i";
		$$GSM1800_TRU_TYPE1_3=$planneddata['TRU_TYPE1_3'][0];


		$GSM1800_CONFIG="GSM1800_CONFIG_$i";
		$$GSM1800_CONFIG=$planneddata['CONFIG'][0];
		$GSM1800_FREQ_ACTIVE="GSM1800_FREQ_ACTIVE_$i";
		$$GSM1800_FREQ_ACTIVE=$planneddata['FREQ_ACTIVE'][0];
		$GSM1800_STATE="GSM1800_STATE_$i";
		$$GSM1800_STATE=$planneddata['STATE'][0];
		$GSM1800_TMA="GSM1800_TMA_$i";
		$$GSM1800_TMA=$planneddata['TMA'][0];
		$GSM1800_AZI="GSM1800_AZI_$i";
		$$GSM1800_AZI=$planneddata['AZI'][0];
		$GSM1800_ANTTYPE1="GSM1800_ANTTYPE1_$i";
		$$GSM1800_ANTTYPE1=$planneddata['ANTTYPE1'][0];
		$GSM1800_ELECTILT1="GSM1800_ELECTILT1_$i";
		$$GSM1800_ELECTILT1=$planneddata['ELECTILT1'][0];
		$GSM1800_MECHTILT1="GSM1800_MECHTILT1_$i";
		$$GSM1800_MECHTILT1=$planneddata['MECHTILT1'][0];
		$GSM1800_MECHTILT_DIR1="GSM1800_MECHTILT_DIR1_$i";
		$$GSM1800_MECHTILT_DIR1=$planneddata['MECHTILT_DIR1'][0];
		$GSM1800_ANTHEIGHT1="GSM1800_ANTHEIGHT1_$i";
		$$GSM1800_ANTHEIGHT1=$planneddata['ANTHEIGHT1'][0];
		$GSM1800_ANTTYPE2="GSM1800_ANTTYPE2_$i";
		$$GSM1800_ANTTYPE2=$planneddata['ANTTYPE2'][0];
		$GSM1800_ELECTILT2="GSM1800_ELECTILT2_$i";
		$$GSM1800_ELECTILT2=$planneddata['ELECTILT2'][0];
		$GSM1800_MECHTILT2="GSM1800_MECHTILT2_$i";
		$$GSM1800_MECHTILT2=$planneddata['MECHTILT2'][0];
		$GSM1800_MECHTILT_DIR2="GSM1800_MECHTILT_DIR2_$i";
		$$GSM1800_MECHTILT_DIR2=$planneddata['MECHTILT_DIR2'][0];
		$GSM1800_ANTHEIGHT2="GSM1800_ANTHEIGHT2_$i";
		$$GSM1800_ANTHEIGHT2=$planneddata['ANTHEIGHT2'][0];
		$GSM1800_FEEDERLEN="GSM1800_FEEDERLEN_$i";
		$$GSM1800_FEEDERLEN=$planneddata['FEEDERLEN'][0];
		$GSM1800_FEEDER="GSM1800_FEEDER_$i";
		$$GSM1800_FEEDER=$planneddata['FEEDER'][0];
		$GSM1800_COMB="GSM1800_COMB_$i";
		$$GSM1800_COMB=$planneddata['COMB'][0];
		$GSM1800_DCBLOCK="GSM1800_DCBLOCK_$i";
		$$GSM1800_DCBLOCK=$planneddata['DCBLOCK'][0];
	}
	//echo "****".$GSM1800_MECHTILT_DIR1_1;

	$planneddata=get_data("GSM1800","","ADDITIONAL_PLANNED",'');
	$GSM1800_COMMENTS=$planneddata_additional['COMMENTS'][0];
	$GSM1800_CDUTYPE=$planneddata_additional['CDUTYPE'][0];
	$GSM1800_CABTYPE=$planneddata_additional['CABTYPE'][0];
	$GSM1800_NR_OF_CAB=$planneddata_additional['NR_OF_CAB'][0];
	$GSM1800_BBS=$planneddata_additional['BBS'][0];
	$GSM1800_DXUTYPE=$planneddata_additional['DXUTYPE'][0];
}else{
	// get current data
	$currentdata=get_data("GSM1800","","CURRENT_ASSET",""); //Get data from Asset
	$AMOUNT_ASSET_INFO=count($currentdata['SITEKEY']);
	//echo "$AMOUNT_ASSET_INFO"; //Amount of sectors

	$j=1;
	$start="yes";
	for ($i=0;$i<$AMOUNT_ASSET_INFO;$i++){

		$SECTORID=$currentdata['SECTORID'][$i];
		$ID=substr($SECTORID,-1);
		$last_sect=substr($SECTORID,-1);

		if ($last_sect!=$vorige){
			$k=1;
			//echo "1 $j<br>";
			if ($start!="yes"){
				$j++;
			}else{
				$start="no";
			}

		}else{
			$k=2;
			//echo "2 $j<br>";
		}

		$CONFIG="CONFIG_$j";
		$$CONFIG=$currentdata['CELLEQUIPMENT'][$i];

		$STATE="STATE_$j";
		$$STATE=get_config($currentdata['CELLSTATUS'][$i]);

		$AZI="AZI_$j";
		$$AZI=$currentdata['AZIMUTH'][$i];

		$FEEDER="FEEDER_$j";
		$$FEEDER=$currentdata['FEEDERKEY'][$i];

		$FEEDERLEN="FEEDERLEN_$j";
		$$FEEDERLEN=$currentdata['FEEDERLENGTH'][$i];


		$ANTTYPE="ANTTYPE".$k."_".$j;
		$$ANTTYPE=$currentdata['ANTENNATYPE'][$i];

		$ANTHEIGHT="ANTHEIGHT".$k."_".$j;
		$$ANTHEIGHT=round($currentdata['ANTENNAHEIGHT'][$i],2);

		$MECHTILT="MECHTILT".$k."_".$j;
		$MECHTILT_DIR="MECHTILT_DIR1_".$j;
		$$MECHTILT=trim(abs($currentdata['DOWNTILT'][$i]));
		$$MECHTILT_DIR=get_mechtilt_dir($currentdata['DOWNTILT'][$i]);

		$ELECTILT="ELECTILT".$k."_".$j;
		$$ELECTILT=$currentdata['ANTENNATYPE'][$i];
		$$ELECTILT=substr($$ANTTYPE, -2);
		if (!is_numeric($$ELECTILT)){
			$$ELECTILT=substr($$ANTTYPE, -1);
			if (!is_numeric($$ELECTILT)){
				$$ELECTILT=substr($$ANTTYPE, -3,1);
			}
		}

		$vorige=$last_sect;
	}

} //END if ($check_assetdata_exists_GSM1800!="0"){

	$freq=get_freq("1");
	$GSM1800_FREQ_ACTIVE_1=$freq[1];
	$GSM1800_CDUTYPE=$freq[0];
	$freq=get_freq("2");
	$GSM1800_FREQ_ACTIVE_2=$freq[1];
	$freq=get_freq("3");
	$GSM1800_FREQ_ACTIVE_3=$freq[1];
/*******************************************************************************************************/
for ($i = 1; $i <= 3; $i++) {

	$GSM1800_CONFIG="GSM1800_CONFIG_$i";
	$GSM1800_FREQ_ACTIVE="GSM1800_FREQ_ACTIVE_$i";
	$GSM1800_MECHTILT_DIR1="GSM1800_MECHTILT_DIR1_$i";
	$GSM1800_MECHTILT1="GSM1800_MECHTILT1_$i";
	$GSM1800_MECHTILT_DIR2="GSM1800_MECHTILT_DIR2_$i";
	$GSM1800_MECHTILT2="GSM1800_MECHTILT2_$i";

	//echo "$GSM1800_CONFIG = ".$$GSM1800_CONFIG."<br>";
	//echo $$GSM1800_MECHTILT_DIR2."--".$$GSM1800_MECHTILT_DIR1;
	if ($$GSM1800_FREQ_ACTIVE < 2){	$$GSM1800_FREQ_ACTIVE=2;	}
	if ($$GSM1800_MECHTILT_DIR1=="DOWNTILT" || $$GSM1800_MECHTILT_DIR1=="Downtilt"){ $$GSM1800_MECHTILT_DIR1="-"; }
	if ($$GSM1800_MECHTILT_DIR2=="DOWNTILT" || $$GSM1800_MECHTILT_DIR2=="Downtilt"){ $$GSM1800_MECHTILT_DIR2="-"; }
	if ($$GSM1800_MECHTILT1=="-"){ $$GSM1800_MECHTILT1="0"; }
	if ($$GSM1800_MECHTILT2=="-"){ $$GSM1800_MECHTILT2="0"; }
	if ($$GSM1800_MECHTILT_DIR1=="-" && ($$GSM1800_MECHTILT1=="0")){	$$GSM1800_MECHTILT_DIR1=""; }
	if ($$GSM1800_MECHTILT_DIR2=="-" && $$GSM1800_MECHTILT2=="0"){	$$GSM1800_MECHTILT_DIR2=""; }
	if ($$GSM1800_MECHTILT_DIR1=="UPTILT" || $$GSM1800_MECHTILT_DIR1=="Uptilt"){ $$GSM1800_MECHTILT_DIR1=""; }
	if ($$GSM1800_MECHTILT_DIR2=="UPTILT" || $$GSM1800_MECHTILT_DIR2=="Uptilt"){ $$GSM1800_MECHTILT_DIR2=""; }

}

/******************************************************* PLANNED UMTS **********************************************************/
if ($_SESSION['PREVIEW']!="yes"){
	if ($_SESSION['STATUS_UMTS']=="BSDS AS BUILD"){
		$_SESSION['table_view']="_BUILD";
	}else if ($_SESSION['STATUS_UMTS']=="BSDS FUNDED"){
		$_SESSION['table_view']="_FUND";
	}else if ($_SESSION['STATUS_UMTS']=="SITE FUNDED"){
		$_SESSION['table_view']="_POST";
	}else{
		$_SESSION['table_view']="";
	}
}else if ($_SESSION['PREVIEW']=="yes"){
	$_SESSION['table_view']="";
}
$check_planned_exists_UMTS=check_planned_exists("UMTS",'allsec');

if ($check_planned_exists_UMTS=="1"){
	for ($i = 1; $i <= 3; $i++) {
		$currentdata=get_data("UMTS",$i,"PLANNED",$cab);

		$UMTSCELLID="UMTS_UMTSCELLID_$i";
		$UMTSCELLPK="UMTS_UMTSCELLPK_$i";
		$TRU_INST1="UMTS_TRU_INST1_$i";
		$TRU_INST2="UMTS_TRU_INST2_$i";
		$FREQ_ACTIVE="UMTS_FREQ_ACTIVE_$i";
		$MCPAMODE="UMTS_MCPAMODE_$i";
		$MCPATYPE="UMTS_MCPATYPE_$i";
		$ACS="UMTS_ACS_$i";
		$RET="UMTS_RET_$i";
		$ANTHEIGHT1="UMTS_ANTHEIGHT1_$i";
		$AZI="UMTS_AZI_$i";
		$ANTTYPE1="UMTS_ANTTYPE1_$i";
		$ELECTILT1="UMTS_ELECTILT1_$i";
		$MECHTILT1="UMTS_MECHTILT1_$i";
		$MECHTILT_DIR1="UMTS_MECHTILT_DIR1_$i";
		$FEEDER="UMTS_FEEDER_$i";
		$FEEDERLEN="FEEDERLEN_$i";
		$ANTTYPE2="UMTS_ANTTYPE2_$i";
		$ELECTILT2="UMTS_ELECTILT2_$i";
		$MECHTILT_DIR2="UMTS_MECHTILT_DIR2_$i";
		$ANTHEIGHT2="UMTS_ANTHEIGHT2_$i";
		$MECHTILT2="UMTS_MECHTILT2_$i";
		$STATE="UMTS_STATE_$i";

		$$UMTSCELLID=$currentdata['UMTSCELLID'][0];
		$$UMTSCELLPK=$currentdata['UMTSCELLPK'][0];
		$$TRU_INST1=$currentdata['TRU_INST1'][0];
		$$TRU_INST2=$currentdata['TRU_INST2'][0];
		$$FREQ_ACTIVE=$currentdata['FREQ_ACTIVE'][0];
		$$MCPAMODE=$currentdata['MCPAMODE'][0];
		$$MCPATYPE=$currentdata['MCPATYPE'][0];
		$$ACS=$currentdata['ACS'][0];
		$$RET=$currentdata['RET'][0];
		$$ANTHEIGHT1=$currentdata['ANTHEIGHT1'][0];
		$pieces = explode(".", $$ANTHEIGHT1);
		$$ANTHEIGHT1=$pieces[0].".".$pieces[1];
		$$AZI=$currentdata['AZI'][0];
		$$ELECTILT1=$currentdata['ELECTILT1'][0];
		$$MECHTILT1=$currentdata['MECHTILT1'][0];

		$$MECHTILT_DIR1=$currentdata['MECHTILT_DIR1'][0];
		$$FEEDER=$currentdata['FEEDER'][0];
		$$FEEDERLEN=$currentdata['FEEDERLEN'][0];

		$$ANTTYPE1=$currentdata['ANTTYPE1'][0];
		$$ANTTYPE2=$currentdata['ANTTYPE2'][0];

		$$ELECTILT2=$currentdata['ELECTILT2'][0];
		$$MECHTILT2=$currentdata['MECHTILT2'][0];
		$$MECHTILT_DIR2=$currentdata['MECHTILT_DIR2'][0];
		$$ANTHEIGHT2=$currentdata['ANTHEIGHT2'][0];
		$$STATE=$currentdata['STATE'][0];

		if ($$MECHTILT_DIR1=="DOWNTILT"  || $$MECHTILT_DIR1=="Downtilt"){ $$MECHTILT_DIR1="-"; }
		if ($$MECHTILT_DIR2=="DOWNTILT"  || $$MECHTILT_DIR2=="Downtilt"){ $$MECHTILT_DIR2="-"; }
		if ($$MECHTILT_DIR1=="UPTILT"  || $$MECHTILT_DIR1=="Uptilt"){ $$MECHTILT_DIR1=""; }
		if ($$MECHTILT_DIR2=="UPTILT"  || $$MECHTILT_DIR2=="Uptilt"){ $$MECHTILT_DIR2=""; }

		if ($$MECHTILT_DIR1=="-" && $$MECHTILT1=="0"){
			$$MECHTILT_DIR1="";
		}
		if ($$MECHTILT_DIR2=="-" && $$MECHTILT2=="0"){
			$$MECHTILT_DIR2="";
		}

		//CONVERT ANTENNATYPES TO BIPT FORMAT FOR UMTS
		$$ANTTYPE1=convert_antenna_bipt_UMTS($$ANTTYPE1);
		$$ANTTYPE2=convert_antenna_bipt_UMTS($$ANTTYPE2);

	}//END for loop
}else{
	//Current UMTS
	//$check_current_exists_UMTS=check_current_exists("UMTS","1");
}


/******************************************************* END PLANNED UMTS *****************************************************/

//GET LONGITUDE AND LTITUDE
$coor=get_coordinates($fname);
//echo "<pre>".print_r($coor,true)."</pre>";

//GET BSDS CREATOR DETAILS
	$gen_info=get_BSDS_generalinfo();
	$userdetails=getuserdata($DESIGNER_CREATE);
	$email=$userdetails['email'];
	$fullname=$userdetails['fullname'];
	$mobile=$userdetails['mobile'];

?>
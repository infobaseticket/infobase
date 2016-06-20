<?php

if($band=="G9" || $band=="G18"){

	$LAC=get_LAC($siteID);

	//MAKE 100% sure that vars are empty
	for ($i=1;$i<=6;$i++){
		foreach ($cols_pl_sec['COLUMN_NAME'] as $key => $column) {
			$cur_parname=$column."_".$i;					
			$$cur_parname="";
		}			
	}
	for ($i=1;$i<=6;$i++){
		foreach ($cols_pl['COLUMN_NAME'] as $key => $column) {
			$cur_parname=$column;					
			$$cur_parname="";
		}			
	}
	//WE FIRST RETRIEVE CURRENT SAVED DATA FROM THE DATABASE
	//For not BSDS FUNDED BSDS we overwrite with OSS data

	if ($check_current_exists!="0"){ //we only retrieve data, if there was already data stored
		//WE GET DATA FROM BSDS_CU_GSM2. BSDS_CU_GSM2 contains info which is not available in Asset
		$currentdata=get_data($band,'',"CURRENT_EXISTING",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM);
		$CHANGEDATE = $currentdata['CHANGEDATE'][0];
		$BBS=$currentdata['BBS'][0];
		$CABTYPE=$currentdata['CABTYPE'][0];
		$NR_OF_CAB=$currentdata['NR_OF_CAB'][0];
		$CDUTYPE=$currentdata['CDUTYPE'][0];
		$DXUTYPE1=$currentdata['DXUTYPE1'][0];
		$DXUTYPE2=$currentdata['DXUTYPE2'][0];
		$DXUTYPE3=$currentdata['DXUTYPE3'][0];			
	}

	//retrieve sector info:
	if ($check_current_exists_SECTOR!=0){
		//We get saved sector (all 4) info from BSDS_CU_GSM_SEC2
		$currentdata=get_data($band,'all',"CURRENT_EXISTING",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM);
		foreach ($currentdata as $key => $current) {
			$i=1;
			foreach ($current as $keyid => $value) {
				$parname=$key."_".$i;
				$$parname=$value;
				//echo $parname.":". $value."<br>";
				$i++;
			}
		}
	}


	$feedershare=get_data($band,'','FEEDERSHARE_CURRENT',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM);
	if ($band=="G9"){
		$FEEDERSHARE_1=$feedershare['GSM900_1'][0];
		$FEEDERSHARE_2=$feedershare['GSM900_2'][0];
		$FEEDERSHARE_3=$feedershare['GSM900_3'][0];
		$FEEDERSHARE_4=$feedershare['GSM900_4'][0];
		$FEEDERSHARE_5=$feedershare['GSM900_5'][0];
		$FEEDERSHARE_6=$feedershare['GSM900_6'][0];
	}
	if ($band=="G18"){
		$FEEDERSHARE_1=$feedershare['GSM1800_1'][0];
		$FEEDERSHARE_2=$feedershare['GSM1800_2'][0];
		$FEEDERSHARE_3=$feedershare['GSM1800_3'][0];
		$FEEDERSHARE_4=$feedershare['GSM1800_4'][0];
		$FEEDERSHARE_5=$feedershare['GSM1800_5'][0];
		$FEEDERSHARE_6=$feedershare['GSM1800_6'][0];
	}
	
	// GET ASSET INFO /OSS INFO AND OVERWRITE PREVIOUS RETRIEVED DATA
	//We do not overwrite for FUNDED BSDS, if there is data stored in the database (check_current_exists_SECTOR!=0)
	
	$currentdata=get_data($band,$sec1,"CURRENT_ASSET",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM);
	$AMOUNT_ASSET_INFO=count($currentdata['SITEKEY']);
	$j=1;
	$start="yes";

	for ($i=0;$i<$AMOUNT_ASSET_INFO;$i++){

		$SECTORID=$currentdata['SECTORID'][$i];
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

		$STATE="STATE_$j";
		$$STATE=get_config($currentdata['CELLSTATUS'][$i],$band);
		//echo $$STATE."--";
		
		if ($viewtype=="FUND"){ 
			
			//WE store them in ASEET variables to be able to compare with saved data
			if ($$STATE=="ACTIVE"){
				
				$AZI1="ASSET_AZI1_".$j;
				$$AZI1=$currentdata['AZIMUTH'][$i];
				$AZI2="ASSET_AZI2_".$j;
				$$AZI2=$currentdata['AZIMUTH2'][$i];
				$ANTTYPE="ASSET_ANTTYPE".$k."_".$j;
				$$ANTTYPE=$currentdata['ANTENNATYPE'][$i];
				$MECHTILT="ASSET_MECHTILT".$k."_".$j;
				$MECHTILT_t="ASSET_MECHTILT_DIR".$k."_".$j; 
				$$MECHTILT=trim(abs($currentdata['DOWNTILT'][$i]));
				$$MECHTILT_t=get_mechtilt_dir($currentdata['DOWNTILT'][$i]);
				$ELECTILT="ASSET_ELECTILT".$k."_".$j;
				$$ELECTILT=$currentdata['ANTENNATYPE'][$i];
				$$ELECTILT=substr($$ANTTYPE, -2);
				if ($currentdata['ANTENNATYPE'][$i]=="K80010292_T_900_95"){
					$$ELECTILT="9,5";
				}else{
					if (!is_numeric($$ELECTILT)){
						$$ELECTILT=substr($$ANTTYPE, -1);
						if (!is_numeric($$ELECTILT)){
							$$ELECTILT=substr($$ANTTYPE, -3,1);
						}
					}
				}
				$SECTORID="SECTORID_".$j;
				$$SECTORID=$currentdata['SECTORID'][$i];

				$TRU_data=get_TRU_data($SECTORID_1);
				$ASSET_TRU_INST1_1_1=$TRU_data[1][$SECTORID_1]['MO'][0];
				$ASSET_TRU_INST1_2_1=$TRU_data[1][$SECTORID_1]['MO'][1];
				$ASSET_TRU_INST2_1_1=$TRU_data[2][$SECTORID_1]['MO'][0];
				$ASSET_TRU_INST2_2_1=$TRU_data[2][$SECTORID_1]['MO'][1];
				$ASSET_TRU_TYPE1_1_1=$TRU_data[1][$SECTORID_1]['TRUTYPE'][0];
				$ASSET_TRU_TYPE1_2_1=$TRU_data[1][$SECTORID_1]['TRUTYPE'][1];
				$ASSET_TRU_TYPE2_1_1=$TRU_data[2][$SECTORID_1]['TRUTYPE'][0];
				$ASSET_TRU_TYPE2_2_1=$TRU_data[2][$SECTORID_1]['TRUTYPE'][1];

				$TRU_data=get_TRU_data($SECTORID_2);
				$ASSET_TRU_INST1_1_2=$TRU_data[1][$SECTORID_2]['MO'][0];
				$ASSET_TRU_INST1_2_2=$TRU_data[1][$SECTORID_2]['MO'][1];
				$ASSET_TRU_INST2_1_2=$TRU_data[2][$SECTORID_2]['MO'][0];
				$ASSET_TRU_INST2_2_2=$TRU_data[2][$SECTORID_2]['MO'][1];
				$ASSET_TRU_TYPE1_1_2=$TRU_data[1][$SECTORID_2]['TRUTYPE'][0];
				$ASSET_TRU_TYPE1_2_2=$TRU_data[1][$SECTORID_2]['TRUTYPE'][1];
				$ASSET_TRU_TYPE2_1_2=$TRU_data[2][$SECTORID_2]['TRUTYPE'][0];
				$ASSET_TRU_TYPE2_2_2=$TRU_data[2][$SECTORID_2]['TRUTYPE'][1];

				$TRU_data=get_TRU_data($SECTORID_3);
				$ASSET_TRU_INST1_1_3=$TRU_data[1][$SECTORID_3]['MO'][0];
				$ASSET_TRU_INST1_2_3=$TRU_data[1][$SECTORID_3]['MO'][1];
				$ASSET_TRU_INST2_1_3=$TRU_data[2][$SECTORID_3]['MO'][0];
				$ASSET_TRU_INST2_2_3=$TRU_data[2][$SECTORID_3]['MO'][1];
				$ASSET_TRU_TYPE1_1_3=$TRU_data[1][$SECTORID_3]['TRUTYPE'][0];
				$ASSET_TRU_TYPE1_2_3=$TRU_data[1][$SECTORID_3]['TRUTYPE'][1];
				$ASSET_TRU_TYPE2_1_3=$TRU_data[2][$SECTORID_3]['TRUTYPE'][0];
				$ASSET_TRU_TYPE2_2_3=$TRU_data[2][$SECTORID_3]['TRUTYPE'][1];

				if($SECTORID_4){
				$TRU_data=get_TRU_data($SECTORID_4);
				$ASSET_TRU_INST1_1_4=$TRU_data[1][$SECTORID_4]['MO'][0];
				$ASSET_TRU_INST1_2_4=$TRU_data[1][$SECTORID_4]['MO'][1];
				$ASSET_TRU_INST2_1_4=$TRU_data[2][$SECTORID_4]['MO'][0];
				$ASSET_TRU_INST2_2_4=$TRU_data[2][$SECTORID_4]['MO'][1];
				$ASSET_TRU_INST1_1_4=$TRU_data[1][$SECTORID_4]['MO'][0];
				$ASSET_TRU_INST1_2_4=$TRU_data[1][$SECTORID_4]['MO'][1];
				$ASSET_TRU_INST2_1_4=$TRU_data[2][$SECTORID_4]['MO'][0];
				$ASSET_TRU_INST2_2_4=$TRU_data[2][$SECTORID_4]['MO'][1];
				}

				if($SECTORID_5){
				$TRU_data=get_TRU_data($SECTORID_5);
				$ASSET_TRU_INST1_1_5=$TRU_data[1][$SECTORID_5]['MO'][0];
				$ASSET_TRU_INST1_2_5=$TRU_data[1][$SECTORID_5]['MO'][1];
				$ASSET_TRU_INST2_1_5=$TRU_data[2][$SECTORID_5]['MO'][0];
				$ASSET_TRU_INST2_2_5=$TRU_data[2][$SECTORID_5]['MO'][1];
				$ASSET_TRU_INST1_1_5=$TRU_data[1][$SECTORID_5]['MO'][0];
				$ASSET_TRU_INST1_2_5=$TRU_data[1][$SECTORID_5]['MO'][1];
				$ASSET_TRU_INST2_1_5=$TRU_data[2][$SECTORID_5]['MO'][0];
				$ASSET_TRU_INST2_2_5=$TRU_data[2][$SECTORID_5]['MO'][1];
				}

				if($SECTORID_6){
				$TRU_data=get_TRU_data($SECTORID_6);
				$ASSET_TRU_INST1_1_6=$TRU_data[1][$SECTORID_6]['MO'][0];
				$ASSET_TRU_INST1_2_6=$TRU_data[1][$SECTORID_6]['MO'][1];
				$ASSET_TRU_INST2_1_6=$TRU_data[2][$SECTORID_6]['MO'][0];
				$ASSET_TRU_INST2_2_6=$TRU_data[2][$SECTORID_6]['MO'][1];
				$ASSET_TRU_INST1_1_6=$TRU_data[1][$SECTORID_6]['MO'][0];
				$ASSET_TRU_INST1_2_6=$TRU_data[1][$SECTORID_6]['MO'][1];
				$ASSET_TRU_INST2_1_6=$TRU_data[2][$SECTORID_6]['MO'][0];
				$ASSET_TRU_INST2_2_6=$TRU_data[2][$SECTORID_6]['MO'][1];
				}
			}//END if ($$STATE=="ACTIVE"){

			
		}//END if ($viewtype=="FUND"){ 

		//Here we overwrite if not data has been storead aat time of BSDS funding
		if (($viewtype=="FUND" && $check_current_exists_SECTOR==0) or $viewtype!="FUND"){ 
			//echo "get asset data";
			$$STATE=get_config($currentdata['CELLSTATUS'][$i],$band);
			if ($$STATE=="ACTIVE"){
				$CONFIG="CONFIG_".$j;
				$$CONFIG=$currentdata['CELLEQUIPMENT'][$i];
				$SECTORID="SECTORID_".$j;
				$$SECTORID=$currentdata['SECTORID'][$i];
				$AZI1="AZI1_".$j;
				$$AZI1=$currentdata['AZIMUTH'][$i];
				$AZI2="AZI2_".$j;
				$$AZI2=$currentdata['AZIMUTH2'][$i];
				$FEEDER="FEEDER_$j";
				$$FEEDER=$currentdata['FEEDERKEY'][$i];
				$FEEDERLEN="FEEDERLEN_$j";
				$$FEEDERLEN=number_format(round($currentdata['FEEDERLENGTH'][$i],2),2);
				$ANTTYPE="ANTTYPE".$k."_".$j;
				$$ANTTYPE=$currentdata['ANTENNATYPE'][$i];
				$ANTHEIGHT="ANTHEIGHT".$k."_".$j;
				$$ANTHEIGHT=number_format(round($currentdata['ANTENNAHEIGHT'][$i],2),2);
				$MECHTILT="MECHTILT".$k."_".$j;
				$MECHTILT_t="MECHTILT_DIR".$k."_".$j; 
				$$MECHTILT=trim(abs($currentdata['DOWNTILT'][$i]));

				$$MECHTILT_t=get_mechtilt_dir($currentdata['DOWNTILT'][$i]);
				$ELECTILT="ELECTILT".$k."_".$j;
				$$ELECTILT=$currentdata['ANTENNATYPE'][$i];
				$$ELECTILT=substr($$ANTTYPE, -2);
				$$ELECTILT."<br>";
				if ($currentdata['ANTENNATYPE'][$i]=="K80010292_T_900_95"){
					$$ELECTILT="9,5";
				}else{
					if (!is_numeric($$ELECTILT)){
						$$ELECTILT=substr($$ANTTYPE, -1);
						if (!is_numeric($$ELECTILT)){
							$$ELECTILT=substr($$ANTTYPE, -3,1);
						}
					}
				}
			}else{
				$CONFIG="tmp_CONFIG_$j";
				$$CONFIG=$currentdata['CELLEQUIPMENT'][$i];
				$AZI1="tmp_AZI1_$j";
				$$AZI1=$currentdata['AZIMUTH'][$i];
				$AZI2="tmp_AZI2_$j";
				$$AZI2=$currentdata['AZIMUTH2'][$i];
				$FEEDER="tmp_FEEDER_$j";
				$$FEEDER=$currentdata['FEEDERKEY'][$i];
				$FEEDERLEN="tmp_FEEDERLEN_$j";
				$$FEEDERLEN=number_format(round($currentdata['FEEDERLENGTH'][$i],2),2);
				$ANTTYPE="tmp_ANTTYPE".$k."_".$j;
				$$ANTTYPE=$currentdata['ANTENNATYPE'][$i];
				$ANTHEIGHT="tmp_ANTHEIGHT".$k."_".$j;
				$$ANTHEIGHT=number_format(round($currentdata['ANTENNAHEIGHT'][$i],2),2);
				$MECHTILT="tmp_MECHTILT".$k."_".$j;
				$MECHTILT_t="tmp_MECHTILT1_".$j."_t";
				$$MECHTILT=trim(abs($currentdata['DOWNTILT'][$i]));
				$$MECHTILT_t=get_mechtilt_dir($currentdata['DOWNTILT'][$i]);
				$ELECTILT="tmp_ELECTILT".$k."_".$j;
				$$ELECTILT=$currentdata['ANTENNATYPE'][$i];
				$$ELECTILT=substr($$ANTTYPE, -2);
				$$ELECTILT."<br>";
				if (!is_numeric($$ELECTILT)){
					$$ELECTILT=substr($$ANTTYPE, -1);
					if (!is_numeric($$ELECTILT)){
						$$ELECTILT=substr($$ANTTYPE, -3,1);
					}
				}
			}

			//We get OSS data:
			$freq=get_freq($SECTORID_1);
			$FREQ_ACTIVE1_1=$freq[1][$SECTORID_1];
			$FREQ_ACTIVE2_1=$freq[2][$SECTORID_1];
			$FREQ_ACTIVE3_1=$freq[3][$SECTORID_1];

			$freq=get_freq($SECTORID_2);
			$FREQ_ACTIVE1_2=$freq[1][$SECTORID_2];
			$FREQ_ACTIVE2_2=$freq[2][$SECTORID_2];
			$FREQ_ACTIVE3_2=$freq[3][$SECTORID_2];

			$freq=get_freq($SECTORID_3);
			$FREQ_ACTIVE1_3=$freq[1][$SECTORID_3];
			$FREQ_ACTIVE2_3=$freq[2][$SECTORID_3];
			$FREQ_ACTIVE3_3=$freq[2][$SECTORID_3];	

			if($SECTORID_4){
			$freq=get_freq($SECTORID_4);
			$FREQ_ACTIVE1_4=$freq[1][$SECTORID_4];
			$FREQ_ACTIVE2_4=$freq[2][$SECTORID_4];
			$FREQ_ACTIVE3_4=$freq[3][$SECTORID_4];
			}

			if($SECTORID_5){
			$freq=get_freq($SECTORID_5);
			$FREQ_ACTIVE1_5=$freq[1][$SECTORID_5];
			$FREQ_ACTIVE2_5=$freq[2][$SECTORID_5];
			$FREQ_ACTIVE3_5=$freq[3][$SECTORID_5];
			}

			if($SECTORID_6){
			$freq=get_freq($SECTORID_6);
			$FREQ_ACTIVE1_6=$freq[1][$SECTORID_6];
			$FREQ_ACTIVE2_6=$freq[2][$SECTORID_6];
			$FREQ_ACTIVE3_6=$freq[3][$SECTORID_6];
			}

			$TRU_data=get_TRU_data($SECTORID_1);
			$TRU_INST1_1_1=$TRU_data[1][$SECTORID_1]['MO'][0];
			$TRU_INST1_2_1=$TRU_data[1][$SECTORID_1]['MO'][1];
			$TRU_INST2_1_1=$TRU_data[2][$SECTORID_1]['MO'][0];
			$TRU_INST2_2_1=$TRU_data[2][$SECTORID_1]['MO'][1];
			$TRU_TYPE1_1_1=$TRU_data[1][$SECTORID_1]['TRUTYPE'][0];
			$TRU_TYPE1_2_1=$TRU_data[1][$SECTORID_1]['TRUTYPE'][1];
			$TRU_TYPE2_1_1=$TRU_data[2][$SECTORID_1]['TRUTYPE'][0];
			$TRU_TYPE2_2_1=$TRU_data[2][$SECTORID_1]['TRUTYPE'][1];

			$TRU_data=get_TRU_data($SECTORID_2);
			$TRU_INST1_1_2=$TRU_data[1][$SECTORID_2]['MO'][0];
			$TRU_INST1_2_2=$TRU_data[1][$SECTORID_2]['MO'][1];
			$TRU_INST2_1_2=$TRU_data[2][$SECTORID_2]['MO'][0];
			$TRU_INST2_2_2=$TRU_data[2][$SECTORID_2]['MO'][1];
			$TRU_TYPE1_1_2=$TRU_data[1][$SECTORID_2]['TRUTYPE'][0];
			$TRU_TYPE1_2_2=$TRU_data[1][$SECTORID_2]['TRUTYPE'][1];
			$TRU_TYPE2_1_2=$TRU_data[2][$SECTORID_2]['TRUTYPE'][0];
			$TRU_TYPE2_2_2=$TRU_data[2][$SECTORID_2]['TRUTYPE'][1];

			$TRU_data=get_TRU_data($SECTORID_3);
			$TRU_INST1_1_3=$TRU_data[1][$SECTORID_3]['MO'][0];
			$TRU_INST1_2_3=$TRU_data[1][$SECTORID_3]['MO'][1];
			$TRU_INST2_1_3=$TRU_data[2][$SECTORID_3]['MO'][0];
			$TRU_INST2_2_3=$TRU_data[2][$SECTORID_3]['MO'][1];
			$TRU_TYPE1_1_3=$TRU_data[1][$SECTORID_3]['TRUTYPE'][0];
			$TRU_TYPE1_2_3=$TRU_data[1][$SECTORID_3]['TRUTYPE'][1];
			$TRU_TYPE2_1_3=$TRU_data[2][$SECTORID_3]['TRUTYPE'][0];
			$TRU_TYPE2_2_3=$TRU_data[2][$SECTORID_3]['TRUTYPE'][1];

			if($SECTORID_4){
			$TRU_data=get_TRU_data($SECTORID_4);
			$TRU_INST1_1_4=$TRU_data[1][$SECTORID_4]['MO'][0];
			$TRU_INST1_2_4=$TRU_data[1][$SECTORID_4]['MO'][1];
			$TRU_INST2_1_4=$TRU_data[2][$SECTORID_4]['MO'][0];
			$TRU_INST2_2_4=$TRU_data[2][$SECTORID_4]['MO'][1];
			$TRU_INST1_1_4=$TRU_data[1][$SECTORID_4]['MO'][0];
			$TRU_INST1_2_4=$TRU_data[1][$SECTORID_4]['MO'][1];
			$TRU_INST2_1_4=$TRU_data[2][$SECTORID_4]['MO'][0];
			$TRU_INST2_2_4=$TRU_data[2][$SECTORID_4]['MO'][1];
			}

			if($SECTORID_5){
			$TRU_data=get_TRU_data($SECTORID_5);
			$TRU_INST1_1_5=$TRU_data[1][$SECTORID_5]['MO'][0];
			$TRU_INST1_2_5=$TRU_data[1][$SECTORID_5]['MO'][1];
			$TRU_INST2_1_5=$TRU_data[2][$SECTORID_5]['MO'][0];
			$TRU_INST2_2_5=$TRU_data[2][$SECTORID_5]['MO'][1];
			$TRU_INST1_1_5=$TRU_data[1][$SECTORID_5]['MO'][0];
			$TRU_INST1_2_5=$TRU_data[1][$SECTORID_5]['MO'][1];
			$TRU_INST2_1_5=$TRU_data[2][$SECTORID_5]['MO'][0];
			$TRU_INST2_2_5=$TRU_data[2][$SECTORID_5]['MO'][1];
			}

			if($SECTORID_6){
			$TRU_data=get_TRU_data($SECTORID_6);
			$TRU_INST1_1_6=$TRU_data[1][$SECTORID_6]['MO'][0];
			$TRU_INST1_2_6=$TRU_data[1][$SECTORID_6]['MO'][1];
			$TRU_INST2_1_6=$TRU_data[2][$SECTORID_6]['MO'][0];
			$TRU_INST2_2_6=$TRU_data[2][$SECTORID_6]['MO'][1];
			$TRU_INST1_1_6=$TRU_data[1][$SECTORID_6]['MO'][0];
			$TRU_INST1_2_6=$TRU_data[1][$SECTORID_6]['MO'][1];
			$TRU_INST2_1_6=$TRU_data[2][$SECTORID_6]['MO'][0];
			$TRU_INST2_2_6=$TRU_data[2][$SECTORID_6]['MO'][1];
			}

			$cabdata=get_cabinettype($band,$siteID);
			$CABTYPE=$cabdata['type'];
			$NR_OF_CAB=$cabdata['number'];
			$CDUTYPE=$cabdata['CDU'];
		}
		$vorige=$last_sect;	
	}//END for


}else if($band=="U21" || $band=="U9" || $band=="L18" || $band=="L26" || $band=="L8"){
	//MAKE 100% sure that vars are empty
	for ($i=1;$i<=6;$i++){
		foreach ($cols_pl_sec['COLUMN_NAME'] as $key => $column) {
			//echo $column."<br>";
			$cur_parname=$column."_".$i;					
			$$cur_parname="";
		}			
	}
	for ($i=1;$i<=6;$i++){
		foreach ($cols_pl['COLUMN_NAME'] as $key => $column) {
			$cur_parname=$column;
			//echo $column."<br>";					
			$$cur_parname="";
		}			
	}

	//echo "***".$check_current_exists_UMTS;
	if ($check_current_exists_UMTS!=0){
		$currentdata=get_data($band,"","CURRENT_EXISTING",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognode);
		$LOGNODEID=$currentdata['LOGNODE'][0];
		$CHANGEDATE=$currentdata['CHANGEDATE'][0];
		$POWERSUP=$currentdata['POWERSUP'][0];
		$CABTYPE=$currentdata['CABTYPE'][0];
		$IPB=$currentdata['IPB'][0];
		$PSU=$currentdata['PSU'][0];
		$TXBHW=$currentdata['TXBHW'][0];
		$TXBSW=$currentdata['TXBSW'][0];
		$RAXBHW=$currentdata['RAXBHW'][0];
		$RAXBSW=$currentdata['RAXBSW'][0];
		$RAXEHW=$currentdata['RAXEHW'][0];
		$RAXESW=$currentdata['RAXESW'][0];
		$HSTXHW=$currentdata['HSTXHW'][0];
		$HSTXSW=$currentdata['HSTXSW'][0];
		$MBPS=$currentdata['MBPS'][0];
		$PLAYSTATION=$currentdata['PLAYSTATION'][0];
		$SERVICE=$currentdata['SERVICE'][0];
		if ($band=="L18" or $band=="L26" or $band=="L8"){
			$BPL=$currentdata['BPL'][0];
		}else if ($band=="U21" or $band=="U9"){
			$BPC=$currentdata['BPC'][0];
			$BPK=$currentdata['BPK'][0];
			$CC=$currentdata['CC'][0];
		}
	}

	if ($check_current_exists_UMTS_SECTOR!=0){
		//SAVED CURRENT PER SECTOR
		
		for ($i = 1; $i <= 6; $i++){
			//echo $viewtype;
			//Diffrence between previous get_data: sector $i
			$currentdata=get_data($band,$i,"CURRENT_EXISTING",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognode);
			$STATE="STATE_$i";

			//if ($$STATE=="ACTIVE"){
				$UMTSCELLPK="UMTSCELLPK_$i";
				$$UMTSCELLPK=$currentdata['UMTSCELLPK'][0];
				$TRU_INST1="TRU_INST1_$i";
				$$TRU_INST1=$currentdata['TRU_INST1'][0];
				$TRU_INST2="TRU_INST2_$i";
				$$TRU_INST2=$currentdata['TRU_INST2'][0];
				$FREQ_ACTIVE="FREQ_ACTIVE_$i";
				$$FREQ_ACTIVE=$currentdata['FREQ_ACTIVE'][0];
				$MCPAMODE="MCPAMODE_$i";
				$$MCPAMODE=$currentdata['MCPAMODE'][0];
				$MCPATYPE="MCPATYPE_$i";
				$$MCPATYPE=$currentdata['MCPATYPE'][0];
				$ACS="ACS_$i";
				$$ACS=$currentdata['ACS'][0];
				$RET="RET_$i";
				$$RET=$currentdata['RET'][0];
			/*}else{*/
				$UMTSCELLID="tmp_UMTSCELLID_$i";
				$$UMTSCELLID=$currentdata['UMTSCELLID'][0];
				$UMTSCELLPK="tmp_UMTSCELLPK_$i";
				$$UMTSCELLPK=$currentdata['UMTSCELLPK'][0];
				$TRU_INST1="tmp_TRU_INST1_$i";
				$$TRU_INST1=$currentdata['TRU_INST1'][0];
				$TRU_INST2="tmp_TRU_INST2_$i";
				$$TRU_INST2=$currentdata['TRU_INST2'][0];
				$FREQ_ACTIVE="tmp_FREQ_ACTIVE_$i";
				$$FREQ_ACTIVE=$currentdata['FREQ_ACTIVE'][0];
				$MCPAMODE="tmp_MCPAMODE_$i";
				$$MCPAMODE=$currentdata['MCPAMODE'][0];
				$MCPATYPE="tmp_MCPATYPE_$i";
				$$MCPATYPE=$currentdata['MCPATYPE'][0];
				$ACS="tmp_ACS_$i";
				$$ACS=$currentdata['ACS'][0];
				$RET="tmp_RET_$i";
				$$RET=$currentdata['RET'][0];
			/*}*/
		}
	}

	$feedershare=get_data($band,'','FEEDERSHARE_CURRENT',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognode);
	if ($band=="U9"){
		$FEEDERSHARE_1=$feedershare['UMTS900_1'][0];
		$FEEDERSHARE_2=$feedershare['UMTS900_2'][0];
		$FEEDERSHARE_3=$feedershare['UMTS900_3'][0];
		$FEEDERSHARE_4=$feedershare['UMTS900_4'][0];
		$FEEDERSHARE_5=$feedershare['UMTS900_5'][0];
		$FEEDERSHARE_6=$feedershare['UMTS900_6'][0];
	}
	if ($band=="U21"){
		$FEEDERSHARE_1=$feedershare['UMTS2100_1'][0];
		$FEEDERSHARE_2=$feedershare['UMTS2100_2'][0];
		$FEEDERSHARE_3=$feedershare['UMTS2100_3'][0];
		$FEEDERSHARE_4=$feedershare['UMTS2100_4'][0];
		$FEEDERSHARE_5=$feedershare['UMTS2100_5'][0];
		$FEEDERSHARE_6=$feedershare['UMTS2100_6'][0];

	}
	if ($band=="L18"){
		$FEEDERSHARE_1=$feedershare['LTE1800_1'][0];
		$FEEDERSHARE_2=$feedershare['LTE1800_2'][0];
		$FEEDERSHARE_3=$feedershare['LTE1800_3'][0];
		$FEEDERSHARE_4=$feedershare['LTE1800_4'][0];
		$FEEDERSHARE_5=$feedershare['LTE1800_5'][0];
		$FEEDERSHARE_6=$feedershare['LTE1800_6'][0];
	}
	if ($band=="L26"){
		$FEEDERSHARE_1=$feedershare['LTE2600_1'][0];
		$FEEDERSHARE_2=$feedershare['LTE2600_2'][0];
		$FEEDERSHARE_3=$feedershare['LTE2600_3'][0];
		$FEEDERSHARE_4=$feedershare['LTE2600_4'][0];
		$FEEDERSHARE_5=$feedershare['LTE2600_5'][0];
		$FEEDERSHARE_6=$feedershare['LTE2600_6'][0];
	}
	if ($band=="L8"){
		$FEEDERSHARE_1=$feedershare['LTE800_1'][0];
		$FEEDERSHARE_2=$feedershare['LTE800_2'][0];
		$FEEDERSHARE_3=$feedershare['LTE800_3'][0];
		$FEEDERSHARE_4=$feedershare['LTE800_4'][0];
		$FEEDERSHARE_5=$feedershare['LTE800_5'][0];
		$FEEDERSHARE_6=$feedershare['LTE800_6'][0];
	}

	// GET ASSET INFO/OSS INFO AND OVERWRITE PREVIOUS RETRIEVED DATA
	//We do not overwrite for FUNDED BSDS, if there is data stored in the database (check_current_exists_SECTOR!=0)
	
	//GET ASSET INFO
	$currentdata=get_data($band, "","CURRENT_ASSET",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognode);
	$AMOUNT_ASSET_INFO=count($currentdata['UMTSCELLID']);

	//echo "AMOUNT_ASSET_INFO $AMOUNT_ASSET_INFO<br>";; //Amount of sectors
	$j=1;
	$start="yes";

	for ($i=0;$i<=$AMOUNT_ASSET_INFO;$i++){

		$SECTORID=$currentdata['UMTSCELLID'][$i];
		$last_sect=substr($SECTORID,-2,1);

		if ($last_sect!=$vorige){
			$k=1;
			if ($start!="yes"){
				$j++;
			}else{
				$start="no";
			}

		}else{
			$k=2;
		}

		$STATE="STATE_$j";
		$$STATE=strtoupper(get_config($currentdata['UMTSCELLID'][$i],$band));

		//We store ASSET VALUESin variables to be able to check if something has changed (by ORQ) when BSDS is BSDS FUNDED
		if ($viewtype=="FUND"){ 
			if ($$STATE=="ACTIVE"){
				if ($k=="1"){
					$AZI1="ASSET_AZI1_$j";
					$$AZI1=$currentdata['AZIMUTH'][$i];
					$ANTTYPE1="ASSET_ANTTYPE1_$j";
					$$ANTTYPE1=$currentdata['ANTTYPE'][$i];
					//echo "$j--$k SECTORID $SECTORID / $ANTTYPE1".$currentdata['ANTTYPE'][$i]."<br>";
					$ANTHEIGHT1="ASSET_ANTHEIGHT1_$j";
					$$ANTHEIGHT1=number_format(round($currentdata['HEIGHT'][$i],2),2);
					$ELECTILT1="ASSET_ELECTILT1_$j";
					$temp=explode("_",$$ANTTYPE1);
					$amount1=count($temp)-1;
					$amount2=count($temp)-2;
					//echo $amount;
					if(is_numeric($temp[$amount1])){
						$$ELECTILT1=$temp[$amount1];
					}else{
						$$ELECTILT1=$temp[$amount2];
					}
					$MECHTILT1="ASSET_MECHTILT1_$j";
					$$MECHTILT1=trim(abs($currentdata['MECH_TILT'][$i]));
					$MECHTILT1_t="ASSET_MECHTILT1_".$j."_t";
					$$MECHTILT1_t=get_mechtilt_dir($currentdata['MECH_TILT'][$i]);
				}else if ($k=="2"){
					$AZI2="ASSET_AZI2_$j";
					$$AZI2=$currentdata['AZIMUTH2'][$i];
					$ANTTYPE2="ASSET_ANTTYPE2_".$j;
					$$ANTTYPE2=$currentdata['ANTTYPE'][$i];
					$ANTHEIGHT2="ASSET_ANTHEIGHT2_$j";
					$$ANTHEIGHT2=number_format(round($currentdata['HEIGHT'][$i],2),2);
					$MECHTILT2="ASSET_MECHTILT2_$j";
					$$MECHTILT2=$currentdata['MECH_TILT'][$i];
					$ELECTILT2="ASSET_ELECTILT2_$j";
					$$ELECTILT2=$currentdata['ELEC_TILT'][$i];
					$MECHTILT2_t="ASSET_MECHTILT2_".$j."_t";
					$$MECHTILT2_t=get_mechtilt_dir($currentdata['MECH_TILT'][$i]);
				}
			}
		}

		if (($viewtype=="FUND" && $check_current_exists_SECTOR==0) or $viewtype!="FUND"){ 
			//echo "$k SECTORID $SECTORID / ".$currentdata['ANTTYPE'][$i]."<br>";
			$STATE="STATE_$j";

			//echo $currentdata['UMTSCELLID'][$i];
			$$STATE=strtoupper(get_config($currentdata['UMTSCELLID'][$i],$band));
			$UMTSCELLID="UMTSCELLID_$j";
			$$UMTSCELLID=$currentdata['UMTSCELLID'][$i];

			if ($$STATE=="ACTIVE"){
			
				$FEEDER="FEEDER_$j";
				$$FEEDER=$currentdata['FEEDERTYPE'][$i];
				$FEEDERLEN="FEEDERLEN_$j";
				$$FEEDERLEN=number_format(round($currentdata['FEEDERLENGTH'][$i],2),2);
				$UMTSCELLID="UMTSCELLID_".$j;
				$$UMTSCELLID=$currentdata['UMTSCELLID'][$i];

				if ($LOGNODEID==""){
					$LOGNODEID=$currentdata['LOGNODE'][$i];//ALL SAME FOR THREE SECTORS!
				}

				$UMTSCELLPK="UMTSCELLPK_$j";
				$$UMTSCELLPK=$currentdata['UMTSCELLPK'][$i];

				if ($k=="1"){
					$AZI1="AZI1_$j";
					$$AZI1=$currentdata['AZIMUTH'][$i];
					
					$ANTTYPE1="ANTTYPE1_$j";
					$$ANTTYPE1=$currentdata['ANTTYPE'][$i];
					//echo $currentdata['ANTTYPE'][$i];
					//echo "$j -- $k SECTORID $SECTORID / $ANTTYPE1".$currentdata['ANTTYPE'][$i]."<br>";
					$ANTHEIGHT1="ANTHEIGHT1_$j";
					$$ANTHEIGHT1=number_format(round($currentdata['HEIGHT'][$i],2),2);
					$ELECTILT1="ELECTILT1_$j";
					$temp=explode("_",$$ANTTYPE1);
					$amount1=count($temp)-1;
					$amount2=count($temp)-2;
					//echo $amount;
					if(is_numeric($temp[$amount1])){
						$$ELECTILT1=$temp[$amount1];
					}else{
						$$ELECTILT1=$temp[$amount2];
					}
					$MECHTILT1="MECHTILT1_$j";
					$$MECHTILT1=trim(abs($currentdata['MECH_TILT'][$i]));
					$MECHTILT1_t="MECHTILT1_".$j."_t";
					$$MECHTILT1_t=get_mechtilt_dir($currentdata['MECH_TILT'][$i]);
				}else if ($k=="2"){
					$AZI2="AZI2_$j";
					$$AZI2=$currentdata['AZIMUTH2'][$i];
					$ANTTYPE2="ANTTYPE2_".$j;
					$$ANTTYPE2=$currentdata['ANTTYPE'][$i];
					$ANTHEIGHT2="ANTHEIGHT2_$j";
					$$ANTHEIGHT2=number_format(round($currentdata['HEIGHT'][$i],2),2);
					$MECHTILT2="MECHTILT2_$j";
					$$MECHTILT2=$currentdata['MECH_TILT'][$i];
					$ELECTILT2="ELECTILT2_$j";
					$$ELECTILT2=$currentdata['ELEC_TILT'][$i];
					$MECHTILT2_t="MECHTILT2_".$j."_t";
					$$MECHTILT2_t=get_mechtilt_dir($currentdata['MECH_TILT'][$i]);
				}
			}else{
				
				$FEEDER="tmp_FEEDER_$j";
				$$FEEDER=$currentdata['FEEDERTYPE'][$i];
				$FEEDERLEN="tmp_FEEDERLEN_$j";		
				$$FEEDERLEN=number_format(round($currentdata['FEEDERLENGTH'][$i],2),2);
				$UMTSCELLID="tmp_UMTSCELLID_$j";
				$$UMTSCELLID=$currentdata['UMTSCELLID'][$i];

				if ($LOGNODEID==""){
					$LOGNODEID=$currentdata['LOGNODE'][$i];//ALL SAME FOR THREE SECTORS!
				}
				$UMTSCELLPK="tmp_UMTSCELLPK_$j";
				$$UMTSCELLPK=$currentdata['UMTSCELLPK'][$i];

				if ($k=="1"){
					$AZI1="tmp_AZI1_$j";
					$$AZI1=$currentdata['AZIMUTH'][$i];
					$ANTTYPE1="tmp_ANTTYPE1_$j";
					$$ANTTYPE1=$currentdata['ANTTYPE'][$i];
					$ELECTILT1="tmp_ELECTILT1_$j";
					$temp=explode("_",$$ANTTYPE1);
					$amount1=count($temp)-1;
					$amount2=count($temp)-2;
					//echo $amount;
					if(is_numeric($temp[$amount1])){
						$$ELECTILT1=$temp[$amount1];
					}else{
						$$ELECTILT1=$temp[$amount2];
					}
					$ANTHEIGHT1="tmp_ANTHEIGHT1_$j";
					$$ANTHEIGHT1=number_format(round($currentdata['HEIGHT'][$i],2),2);
					$MECHTILT1="tmp_MECHTILT1_$j";
					$$MECHTILT1=$currentdata['MECH_TILT'][$i];
					$MECHTILT1_t="tmp_MECHTILT1_".$j."_t";
					$$MECHTILT1_t=get_mechtilt_dir($currentdata['MECHTILT_DIR1'][$i]);

				}else if ($k=="2"){
					$AZI2="tmp_AZI2_$j";
					$$AZI2=$currentdata['AZIMUTH2'][$i];
					$ANTTYPE2="tmp_ANTTYPE2_$j";
					$$ANTTYPE2=$currentdata['ANTTYPE'][$i];
					$ANTHEIGHT2="tmp_ANTHEIGHT2_$j";
					$$ANTHEIGHT2=number_format(round($currentdata['HEIGHT'][$i],2),2);
					$MECHTILT2="tmp_MECHTILT2_$j";
					$$MECHTILT2=$currentdata['MECH_TILT'][$i];
					$ELECTILT2="tmp_ELECTILT2_$j";
					$$ELECTILT2=$currentdata['ELEC_TILT'][$i];
					$MECHTILT2_t="tmp_MECHTILT2_".$j."_t";
					$$MECHTILT2_t=get_mechtilt_dir($currentdata['MECHTILT_DIR2'][$i]);
				}
			}
		}
		$vorige=$last_sect;
		$k++;
	}	
}


?>
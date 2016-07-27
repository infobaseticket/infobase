<?php
if ($band=="G9" or $band=="G18"){
	$LAC=get_LAC($_POST['siteid']);
}
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

//WE FIRST RETRIEVE CURRENT SAVED DATA FROM THE DATABASE
//For not BSDS FUNDED BSDS we overwrite with OSS data
//echo "***".$check_current_exists;
if ($check_current_exists!=0){//we only retrieve data, if there was already data stored
	//WE GET DATA FROM BSDS_CU_GSM2. BSDS_CU_GSM2 contains info which is not available in Asset

	$currentdata=get_data($band,"","CURRENT_EXISTING",$_POST['frozen'],$_POST['bsdskey'],$_POST['createddate'],$_POST['donor'],$_POST['candidate']);
	foreach ($currentdata as $key => $current) {
		$i=1;
		foreach ($current as $keyid => $value) {
			$parname=$key;
			$$parname=$value;
			//echo $parname.":". $value."<br>";
			$i++;
		}
	}			
}

if ($check_current_exists_SECTOR!=0){
	//SAVED CURRENT PER SECTOR	
	//Diffrence between previous get_data: sector $i
	$currentdata=get_data($band,'all',"CURRENT_EXISTING",$_POST['frozen'],$_POST['bsdskey'],$_POST['createddate'],$_POST['donor'],$_POST['candidate']);
	$STATE="STATE_$i";

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

$feedershare=get_data($band,'','FEEDERSHARE_CURRENT',$_POST['frozen'],$_POST['bsdskey'],$_POST['createddate'],$_POST['donor'],$_POST['candidate']);
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
$currentdata=get_data($band,"","CURRENT_ASSET",$_POST['frozen'],$_POST['bsdskey'],$_POST['createddate'],$_POST['donor'],$lognode);

//echo "<pre>".print_r($currentdata,true)."</pre>";
if ($band=='G9' or $band=='G18'){
	$AMOUNT_ASSET_INFO=count($currentdata['STATUS']);
}else{
	$AMOUNT_ASSET_INFO=count($currentdata['UMTSCELLID']);
}

$j=1;
$start="yes";

for ($i=0;$i<$AMOUNT_ASSET_INFO;$i++){

	if ($band=='G9' or $band=='G18'){
		$SECTORID=$currentdata['SECTORID'][$i];
		$last_sect=substr($SECTORID,-1);

		if ($last_sect!=$vorige){
			$k=1;
		}else{
			$k=2;
		}
		if ($last_sect==6){
			$j=3;
		}else if ($last_sect==5){
			$j=2;
		}else if ($last_sect==4){
			$j=1;
		}else if ($last_sect==3){
			$j=3;
		}else if ($last_sect==2){
			$j=2;
		}else if ($last_sect==1){
			$j=1;
		}else if ($last_sect==0){
			$j=4;
		}

		$STATE="STATE_".$j;
		$$STATE=get_config($currentdata['CELLSTATUS'][$i],$band);

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

		$ANTTYPE='ANTENNATYPE';
		$ANTHEIGHT='ANTENNAHEIGHT';
		$ELECTITLT='ANTENNATYPE';
		$MECHTILT='DOWNTILT';
		$FEEDERTYPE='FEEDERKEY';
		$FEERLENGTH='FEERLENGTH';
		$AZIMUTH='AZIMUTH';
		
	}else{ //END FOR GSM
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

		$SECTORID="SECTORID_".$j;
		$$SECTORID=$currentdata['UMTSCELLID'][$i];

		$STATE="STATE_".$j;
		if ($currentdata['UMTSCELLID'][$i]!=''){
			$$STATE=strtoupper(get_config($currentdata['UMTSCELLID'][$i],$band));
		}

		$ANTTYPE='ANTTYPE';
		$ANTHEIGHT='HEIGHT';
		$ELECTILT='ELEC_TILT';
		$MECHTILT='MECH_TILT';
		$FEEDERTYPE='FEEDERTYPE';
		$FEERLENGTH='FEERLENGTH';
		$AZIMUTH='AZIMUTH';
	
	}	

		//We store ASSET VALUESin variables to be able to check if something has changed (by ORQ) when BSDS is BSDS FUNDED/Frozen
	if ($_POST['frozen']==1){ 
		if ($$STATE=="ACTIVE"){
			if ($k=="1"){
				$AZI1="ASSET_AZI1_".$j;
				$$AZI1=$currentdata['AZIMUTH'][$i];
				$ANTTYPE1="ASSET_ANTTYPE1_".$j;
				$$ANTTYPE1=$currentdata['ANTTYPE'][$i];
				//echo "$j--$k SECTORID $SECTORID / $ANTTYPE1".$currentdata['ANTTYPE'][$i]."<br>";
				$ANTHEIGHT1="ASSET_ANTHEIGHT1_".$j;
				$$ANTHEIGHT1=number_format(round($currentdata['HEIGHT'][$i],2),2);
				$ELECTILT1="ASSET_ELECTILT1_".$j;
				$$ELECTILT1=$currentdata[$ELECTILT][$i];
				if ($band=='G9' or $band=='G18'){
					if ($currentdata['ANTENNATYPE'][$i]=="K80010292_T_900_95"){
						$$ELECTILT1="9,5";
					}else{
						if (!is_numeric($$ELECTILT1)){
							$$ELECTILT1=substr($$ANTTYPE1, -1);
							if (!is_numeric($$ELECTILT1)){
								$$ELECTILT1=substr($$ANTTYPE1, -3,1);
							}
						}
					}

					$CONFIG="ASSET_CONFIG_".$j;
					$$CONFIG=$currentdata['CELLEQUIPMENT'][$i];
				}
				$MECHTILT="ASSET_MECHTILT1_".$j;
				$MECHTILT_t="ASSET_MECHTILT_DIR1_".$j; 
				$$MECHTILT=trim(abs($currentdata[$MECHTILT][$i]));
				$$MECHTILT_t=get_mechtilt_dir($currentdata[$MECHTILT][$i]);

				if ($band=='G9' or $band=='G18'){
					$CONFIG="ASSET_CONFIG_".$j;
					$$CONFIG=$currentdata['CELLEQUIPMENT'][$i];
				}

			}else if ($k=="2"){
				$AZI2="ASSET_AZI2_".$j;
				$$AZI2=$currentdata['AZIMUTH2'][$i];
				$ANTTYPE2="ASSET_ANTTYPE2_".$j;
				$$ANTTYPE2=$currentdata['ANTTYPE'][$i];
				$ANTHEIGHT2="ASSET_ANTHEIGHT2_".$j;
				$$ANTHEIGHT2=number_format(round($currentdata['HEIGHT'][$i],2),2);
				
				$ELECTILT2="ASSET_ELECTILT2_".$j;
				$$ELECTILT2=$currentdata[$ELECTILT][$i];
				if ($band=='G9' or $band=='G18'){
					if ($currentdata['ANTENNATYPE'][$i]=="K80010292_T_900_95"){
						$$ELECTILT2="9,5";
					}else{
						if (!is_numeric($$ELECTILT2)){
							$$ELECTILT2=substr($$ANTTYPE2, -1);
							if (!is_numeric($$ELECTILT2)){
								$$ELECTILT2=substr($$ANTTYPE2, -3,1);
							}
						}
					}
				}
				$MECHTILT="ASSET_MECHTILT1_".$j;
				$MECHTILT_t="ASSET_MECHTILT_DIR1_".$j; 
				$$MECHTILT=trim(abs($currentdata[$MECHTILT][$i]));
				$$MECHTILT_t=get_mechtilt_dir($currentdata[$MECHTILT][$i]);
			}
		}
	}
	

	if (($_POST['frozen']==1 && $check_current_exists_SECTOR==0) or $_POST['frozen']!=1){ 
		

		if ($$STATE=="ACTIVE"){
	
			$FEEDER="FEEDER_".$j;
			$$FEEDER=$currentdata[$FEEDERTYPE][$i];
			$FEEDERLEN="FEEDERLEN_".$j;
			$$FEEDERLEN=number_format(round($currentdata[$FEERLENGTH][$i],2),2);

			$UMTSCELLPK="UMTSCELLPK_".$j;
			$$UMTSCELLPK=$currentdata['UMTSCELLPK'][$i];

			if ($k=="1"){
				$AZI1="AZI1_".$j;
				$$AZI1=$currentdata[$AZIMUTH][$i];
				
				$ANTTYPE1="ANTTYPE1_".$j;
				$$ANTTYPE1=$currentdata[$ANTTYPE][$i];
				//echo $ANTTYPE1."/".$$ANTTYPE1."<br>";
				$ANTHEIGHT1="ANTHEIGHT1_".$j;
				$$ANTHEIGHT1=number_format(round($currentdata[$ANTHEIGHT][$i],2),2);

				$MECHTILT="MECHTILT1_".$j;
				$MECHTILT_t="MECHTILT_DIR1_".$j; 
				$$MECHTILT=trim(abs($currentdata[$MECHTILT][$i]));
				$$MECHTILT_t=get_mechtilt_dir($currentdata[$MECHTILT][$i]);

				$ELECTILT1="ELECTILT1_".$j;
				$$ELECTILT1=$currentdata[$ELECTILT][$i];
				if ($band=='G9' or $band=='G18'){
					if ($currentdata['ANTENNATYPE'][$i]=="K80010292_T_900_95"){
						$$ELECTILT1="9,5";
					}else{
						if (!is_numeric($$ELECTILT1)){
							$$ELECTILT1=substr($$ANTTYPE1, -1);
							if (!is_numeric($$ELECTILT1)){
								$$ELECTILT1=substr($$ANTTYPE1, -3,1);
							}
						}
					}

					$CONFIG="CONFIG_".$j;
					$$CONFIG=$currentdata['CELLEQUIPMENT'][$i];
				}

			}else if ($k=="2"){
				$AZI2="AZI2_".$j;
				$$AZI2=$currentdata[$AZIMUTH][$i];

				$ANTTYPE2="ANTTYPE2_".$j;
				$$ANTTYPE2=$currentdata[$ANTTYPE][$i];

				$ANTHEIGHT2="ANTHEIGHT2_".$j;
				$$ANTHEIGHT2=number_format(round($currentdata[$ANTHEIGHT][$i],2),2);

				$MECHTILT2="MECHTILT2_".$j;
				$MECHTILT2_t="MECHTILT_DIR2_".$j; 
				$$MECHTILT2=trim(abs($currentdata[$MECHTILT][$i]));
				$$MECHTILT2_t=get_mechtilt_dir($currentdata[$MECHTILT][$i]);

				$ELECTILT2="ELECTILT2_".$j;
				$$ELECTILT2=$currentdata[$ELECTILT][$i];

				if ($band=='G9' or $band=='G18'){
					if ($currentdata['ANTENNATYPE'][$i]=="K80010292_T_900_95"){
						$$ELECTILT2="9,5";
					}else{
						if (!is_numeric($$ELECTILT2)){
							$$ELECTILT2=substr($$ANTTYPE2, -1);
							if (!is_numeric($$ELECTILT2)){
								$$ELECTILT2=substr($$ANTTYPE2, -3,1);
							}
						}
					}
				}
				
			}
		}else{
			
			$FEEDER="tmp_FEEDER_".$j;
			$$FEEDER=$currentdata['FEEDERTYPE'][$i];
			$FEEDERLEN="tmp_FEEDERLEN_".$j;		
			$$FEEDERLEN=number_format(round($currentdata['FEEDERLENGTH'][$i],2),2);
			$UMTSCELLID="tmp_UMTSCELLID_".$j;
			$$UMTSCELLID=$currentdata['UMTSCELLID'][$i];

			if ($LOGNODEID==""){
				$LOGNODEID=$currentdata['LOGNODE'][$i];//ALL SAME FOR THREE SECTORS!
			}
			$UMTSCELLPK="tmp_UMTSCELLPK_".$j;
			$$UMTSCELLPK=$currentdata['UMTSCELLPK'][$i];

			if ($k=="1"){
				$AZI1="tmp_AZI1_".$j;
				$$AZI1=$currentdata[$AZIMUTH][$i];

				$ANTTYPE1="tmp_ANTTYPE1_".$j;
				$$ANTTYPE1=$currentdata[$ANTTYPE][$i];

				$ELECTILT1="ELECTILT1_".$j;
				$$ELECTILT1=$currentdata[$ELECTILT][$i];
				if ($band=='G9' or $band=='G18'){
					if ($currentdata['ANTENNATYPE'][$i]=="K80010292_T_900_95"){
						$$ELECTILT1="9,5";
					}else{
						if (!is_numeric($$ELECTILT1)){
							$$ELECTILT1=substr($$ANTTYPE1, -1);
							if (!is_numeric($$ELECTILT1)){
								$$ELECTILT1=substr($$ANTTYPE1, -3,1);
							}
						}
					}
					
					$CONFIG="tmp_CONFIG_".$j;
					$$CONFIG=$currentdata['CELLEQUIPMENT'][$i];
				}

				$ANTHEIGHT1="tmp_ANTHEIGHT1_".$j;
				$$ANTHEIGHT1=number_format(round($currentdata[$ANTHEIGHT][$i],2),2);

				$MECHTILT="tmp_MECHTILT1_".$j;
				$MECHTILT_t="tmp_MECHTILT_DIR1_".$j; 
				$$MECHTILT=trim(abs($currentdata[$MECHTILT][$i]));
				$$MECHTILT_t=get_mechtilt_dir($currentdata[$MECHTILT][$i]);

				if ($band=='G9' or $band=='G18'){
					$CONFIG="tmp_CONFIG_".$j;
					$$CONFIG=$currentdata['CELLEQUIPMENT'][$i];
				}

			}else if ($k=="2"){
				$AZI2="tmp_AZI2_".$j;
				$$AZI2=$currentdata[$AZIMUTH][$i];
				$ANTTYPE2="tmp_ANTTYPE2_".$j;
				$$ANTTYPE2=$currentdata[$ANTTYPE][$i];
				$ANTHEIGHT2="tmp_ANTHEIGHT2_".$j;
				$$ANTHEIGHT2=number_format(round($currentdata[$ANTHEIGHT][$i],2),2);
				$MECHTILT2="MECHTILT2_".$j;
				$MECHTILT2_t="MECHTILT_DIR2_".$j; 
				$$MECHTILT2=trim(abs($currentdata[$MECHTILT][$i]));
				$$MECHTILT2_t=get_mechtilt_dir($currentdata[$MECHTILT][$i]);
				
				$ELECTILT2="ELECTILT2_".$j;
				$$ELECTILT2=$currentdata[$ELECTILT][$i];
				if ($band=='G9' or $band=='G18'){
					if ($currentdata['ANTENNATYPE'][$i]=="K80010292_T_900_95"){
						$$ELECTILT2="9,5";
					}else{
						if (!is_numeric($$ELECTILT2)){
							$$ELECTILT2=substr($$ANTTYPE2, -1);
							if (!is_numeric($$ELECTILT2)){
								$$ELECTILT2=substr($$ANTTYPE2, -3,1);
							}
						}
					}
				}
			}
		}
	}


	$vorige=$last_sect;
	$k++;
}	

if ($_POST['frozen']!=1 && ($band=='G9' or $band=='G18')){
	//We get OSS data:
	if ($SECTORID_1!=''){
	$freq=get_freq($SECTORID_1);
	$FREQ_ACTIVE1_1=$freq[1][$SECTORID_1];
	$FREQ_ACTIVE2_1=$freq[2][$SECTORID_1];
	$FREQ_ACTIVE3_1=$freq[3][$SECTORID_1];
	}
	if ($SECTORID_2!=''){
	$freq=get_freq($SECTORID_2);
	$FREQ_ACTIVE1_2=$freq[1][$SECTORID_2];
	$FREQ_ACTIVE2_2=$freq[2][$SECTORID_2];
	$FREQ_ACTIVE3_2=$freq[3][$SECTORID_2];
	}	
	if ($SECTORID_3!=''){
	$freq=get_freq($SECTORID_3);
	$FREQ_ACTIVE1_3=$freq[1][$SECTORID_3];
	$FREQ_ACTIVE2_3=$freq[2][$SECTORID_3];
	$FREQ_ACTIVE3_3=$freq[2][$SECTORID_3];	
	}
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

	$cabdata=get_cabinettype($band,$_POST['siteid']);
	$CABTYPE=$cabdata['type'];
	$NR_OF_CAB=$cabdata['number'];
	$CDUTYPE=$cabdata['CDU'];
}
?>
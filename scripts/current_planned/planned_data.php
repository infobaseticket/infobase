<?PHP

if($band=="G9" || $band=="G18"){

	if ($check_planned_exists!="0"){  			//IF PLANNED DATA HAS ALREADY BEEN SAVED

		$planneddata=get_data($band,"all","PLANNED",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM);
		foreach ($planneddata as $key => $planned) {
			$i=1;
			foreach ($planned as $keyid => $value) {
				$parname="pl_".$key."_".$i;
				$$parname=$value;
				//echo $parname.":". $value."<br>";
				$i++;
			}
		}

		$planneddata_additional=get_data($band,"","PLANNED",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM);
		foreach ($planneddata_additional as $key => $planned) {
			$i=1;
			foreach ($planned as $keyid => $value) {
				$parname="pl_".$key;
				$$parname=$value;
				$i++;
			}
		}

		$planneddata_feedershare=get_data($band,'','FEEDERSHARE_PLANNED',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM);
		if ($band=="G9"){
			$pl_FEEDERSHARE_1=$planneddata_feedershare['GSM900_1'][0];
			$pl_FEEDERSHARE_2=$planneddata_feedershare['GSM900_2'][0];
			$pl_FEEDERSHARE_3=$planneddata_feedershare['GSM900_3'][0];
			$pl_FEEDERSHARE_4=$planneddata_feedershare['GSM900_4'][0];
		}
		if ($band=="G18"){
			$pl_FEEDERSHARE_1=$planneddata_feedershare['GSM1800_1'][0];
			$pl_FEEDERSHARE_2=$planneddata_feedershare['GSM1800_2'][0];
			$pl_FEEDERSHARE_3=$planneddata_feedershare['GSM1800_3'][0];
			$pl_FEEDERSHARE_4=$planneddata_feedershare['GSM1800_4'][0];
		}
	} // END if ($check_planned_exists!="0"){
	else   //IF NO PLANNED DATA HAS BEEN SAVED YET, COPY CURRENT TO PLANNED
	{
		for ($i=1;$i<=4;$i++){
			$STATE="STATE_".$i;	

			if ($$STATE=="ACTIVE"){
				foreach ($cols_pl_sec['COLUMN_NAME'] as $key => $column) {
					if ($column!='STATE'){
						$cur_parname=$column."_".$i;
						$pl_parname="pl_".$column."_".$i;					
						$$pl_parname=$$cur_parname;
					}
				}
			}else{
				foreach ($cols_pl_sec['COLUMN_NAME'] as $key => $column) {
					if ($column!='STATE'){
						$pl_parname="pl_".$column."_".$i;
						$parname="tmp_".$column."_".$i;
						$cur_parname=$column."_".$i;
						$$pl_parname=$$parname;
						$$cur_parname="";
					}
				}
			}		
		}
		foreach ($cols_pl['COLUMN_NAME'] as $key => $column) {
			$pl_parname="pl_".$column;
			$cur_parname=$column;
			$$pl_parname=$$cur_parname;
			if ($STATE_1!="ACTIVE" &&  $STATE_2!="ACTIVE" && $STATE_3!="ACTIVE"){
				$$cur_parname=="";
			}
		}
	}
}else if($band=="U21" OR $band=="U9" OR $band=="L18" OR $band=="L26" OR $band=="L8"){

	if ($check_planned_exists_UMTS!="0"){
		//IF PLANNED DATA HAS ALREADY BEEN SAVED
		
		$planneddata_additional = get_data($band,"","PLANNED",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognode);
		foreach ($planneddata_additional as $key => $planned) {
			$i=1;
			foreach ($planned as $keyid => $value) {
				$parname="pl_".$key;
				$$parname=$value;
				$i++;
			}
		}
	
		$planneddata_feedershare=get_data($band,'','FEEDERSHARE_PLANNED',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognode);
		
		if ($band=="U9"){
			$pl_FEEDERSHARE_1=$planneddata_feedershare['UMTS900_1'][0];
			$pl_FEEDERSHARE_2=$planneddata_feedershare['UMTS900_2'][0];
			$pl_FEEDERSHARE_3=$planneddata_feedershare['UMTS900_3'][0];
			$pl_FEEDERSHARE_4=$planneddata_feedershare['UMTS900_4'][0];
		}
		if ($band=="U21"){
			$pl_FEEDERSHARE_1=$planneddata_feedershare['UMTS2100_1'][0];
			$pl_FEEDERSHARE_2=$planneddata_feedershare['UMTS2100_2'][0];
			$pl_FEEDERSHARE_3=$planneddata_feedershare['UMTS2100_3'][0];
			$pl_FEEDERSHARE_4=$planneddata_feedershare['UMTS2100_4'][0];
		}
		if ($band=="L18"){
			$pl_FEEDERSHARE_1=$planneddata_feedershare['LTE1800_1'][0];
			$pl_FEEDERSHARE_2=$planneddata_feedershare['LTE1800_2'][0];
			$pl_FEEDERSHARE_3=$planneddata_feedershare['LTE1800_3'][0];
			$pl_FEEDERSHARE_4=$planneddata_feedershare['LTE1800_4'][0];
		}
		if ($band=="L26"){
			$pl_FEEDERSHARE_1=$planneddata_feedershare['LTE2600_1'][0];
			$pl_FEEDERSHARE_2=$planneddata_feedershare['LTE2600_2'][0];
			$pl_FEEDERSHARE_3=$planneddata_feedershare['LTE2600_3'][0];
			$pl_FEEDERSHARE_4=$planneddata_feedershare['LTE2600_4'][0];
		}
		if ($band=="L8"){
			$pl_FEEDERSHARE_1=$planneddata_feedershare['LTE800_1'][0];
			$pl_FEEDERSHARE_2=$planneddata_feedershare['LTE800_2'][0];
			$pl_FEEDERSHARE_3=$planneddata_feedershare['LTE800_3'][0];
			$pl_FEEDERSHARE_4=$planneddata_feedershare['LTE800_4'][0];
		}

		$planneddata=get_data($band,"all","PLANNED",$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognode);
		foreach ($planneddata as $key => $planned) {
			$i=1;
			foreach ($planned as $keyid => $value) {
				$parname="pl_".$key."_".$i;
				//echo $parname."<br>";
				$$parname=$value;
				$i++;
			}
		}
		
	} // END if ($check_planned_exists_UMTS!="0"){
	else
	{
		for ($i=1;$i<=4;$i++){
			$STATE="STATE_".$i;	

			if ($$STATE=="ACTIVE"){
				foreach ($cols_pl_sec['COLUMN_NAME'] as $key => $column) {
					if ($column!='STATE'){
						$cur_parname=$column."_".$i;
						$pl_parname="pl_".$column."_".$i;					
						$$pl_parname=$$cur_parname;
					}
				}
			}else{
				foreach ($cols_pl_sec['COLUMN_NAME'] as $key => $column) {
					//echo $column."<br>";
					if ($column!='STATE'){
						$pl_parname="pl_".$column."_".$i;
						$parname="tmp_".$column."_".$i;
						
						
						$cur_parname=$column."_".$i;
						$$pl_parname=$$parname;
						$$cur_parname="";
					}
				}
			}		
		}
	
		foreach ($cols_pl['COLUMN_NAME'] as $key => $column) {
			$pl_parname="pl_".$column;
			$cur_parname=$column;
			$$pl_parname=$$cur_parname;
			if ($STATE_1!="ACTIVE" &&  $STATE_2!="ACTIVE" && $STATE_3!="ACTIVE"){
				$$cur_parname=="";
			}
		}
	}


	//MAke CURRENT EMPTY IF NOT ACTIVE
	if ($STATE_1!="ACTIVE"){

		$AZI1_1="";
		$AZI2_1="";
		$ANTTYPE1_1="";
		$FEEDER_1="";
		$ANTHEIGHT1_1="";
		$FEEDERLEN_1="";
		$MECHTILT1_1="";
		$CONFIG_1="";
		$ANTTYPE2_1="";
		$MECHTILT2_1="";
		$ANTHEIGHT2_1="";
		$ELECTILT1_1="";
		$ELECTILT2_1="";
		$MECHTILT_DIR1_1="";
		$MECHTILT_DIR2_1="";
		$FREQ_ACTIVE_1="";
		$TRU_INST1_1="";
		$TRU_INST2_1="";
		$RET_1="";
		$ACS_1="";
		$MCPAMODE_1="";
		$MCPATYPE_1="";
	}
	if ($STATE_2!="ACTIVE"){
		$AZI1_2="";
		$AZI2_2="";
		$ANTTYPE1_2="";
		$FEEDER_2="";
		$ANTHEIGHT1_2="";
		$FEEDERLEN_2="";
		$MECHTILT1_2="";
		$CONFIG_2="";
		$ANTTYPE2_2="";
		$MECHTILT2_2="";
		$ANTHEIGHT2_2="";
		$ELECTILT1_2="";
		$ELECTILT2_2="";
		$MECHTILT_DIR1_2="";
		$MECHTILT_DIR2_2="";
		$FREQ_ACTIVE_2="";
		$TRU_INST1_2="";
		$TRU_INST2_2="";
		$RET_2="";
		$ACS_2="";
		$MCPAMODE_2="";
		$MCPATYPE_2="";
	}
	if ($STATE_3!="ACTIVE"){
		$AZI1_3="";
		$AZI2_3="";
		$ANTTYPE1_3="";
		$FEEDER_3="";
		$ANTHEIGHT1_3="";
		$FEEDERLEN_3="";
		$MECHTILT1_3="";
		$CONFIG_3="";
		$ANTTYPE2_3="";
		$MECHTILT2_3="";
		$ANTHEIGHT2_3="";
		$ELECTILT1_3="";
		$ELECTILT2_3="";
		$MECHTILT_DIR1_3="";
		$MECHTILT_DIR2_3="";
		$FREQ_ACTIVE_3="";
		$TRU_INST1_3="";
		$TRU_INST2_3="";
		$RET_3="";
		$ACS_3="";
		$MCPAMODE_3="";
		$MCPATYPE_3="";
	}
	if ($STATE_4!="ACTIVE"){
		$AZI1_4="";
		$AZI2_4="";
		$ANTTYPE1_4="";
		$FEEDER_4="";
		$ANTHEIGHT1_4="";
		$FEEDERLEN_4="";
		$MECHTILT1_4="";
		$CONFIG_4="";
		$ANTTYPE2_4="";
		$MECHTILT2_4="";
		$ANTHEIGHT2_4="";
		$ELECTILT1_4="";
		$ELECTILT2_4="";
		$MECHTILT_DIR1_4="";
		$MECHTILT_DIR2_4="";
		$FREQ_ACTIVE_4="";
		$TRU_INST1_4="";
		$TRU_INST2_4="";
		$RET_4="";
		$ACS_4="";
		$MCPAMODE_4="";
		$MCPATYPE_4="";
	}
	if ($STATE_1!="ACTIVE" &&  $STATE_2!="ACTIVE" && $STATE_3!="ACTIVE" && $STATE_4!="ACTIVE"){
		$POWERSUP="";
		$CABTYPE="";
		$IPB="";
		$PSU="";
		$TXBHW="";
		$TXBSW="";
		$RAXBHW="";
		$RAXBSW="";
		$RAXEHW="";
		$RAXESW="";
		$HSTXHW="";
		$HSTXSW="";
		$MBPS="";
	}

	$planneddata_nodebrnc=get_data($band,'','RNCNODEB_ASSET',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognode);
		$pl_NODEB=$planneddata_nodebrnc['NODEB'][0];
		$pl_RNC=$planneddata_nodebrnc['RNC'][0];
}
?>
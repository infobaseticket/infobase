<?php
function check_NET1_BSDS_funded($fname,$filter){
	global $conn_Infobase,$guard_groups;

	$special_fname= substr($fname,2,1);
	if (is_numeric($special_fname)){
		$fname="%/_$fname%' ESCAPE '/";
	} else{
		$fname="%".$fname."%";
	}

	$UPGNR=0;
	if ($fname){
		$where .= "SITE_ID LIKE '".$fname."'";
	}

	if ($filter!=""){ 
		if ($filter[2] && $filter[3]){
			$filter_quer.="(TO_DATE('".$filter[2]."','DD-MON-YYYY') <= trim(U305)
			AND TO_DATE('".$filter[3]."','DD-MON-YYYY') >= trim(U305))";
		}
		if ($filter[2] && $filter[3] && $filter[0] && $filter[1]){
			$filter_quer.=" AND ";
		}
		if ($filter[0] && $filter[1]){ //SITE FUNDED
			$filter_quer.="(TO_DATE('$filter[0]','DD-MON-YYYY') <= trim(U353)
			AND TO_DATE('".$filter[1]."','DD-MON-YYYY') >= trim(U353))";
		}
		if ( (($filter[2] && $filter[3]) && ($filter[4] && $filter[5]))
		|| (($filter[0] && $filter[1]) && ($filter[4] && $filter[5])))
		{
			$filter_quer.=" AND ";
		}
		if ($filter[4] && $filter[5]){
			$filter_quer.=" (TO_DATE('".$filter[4]."','DD-MON-YYYY') <= trim(U571)
			AND TO_DATE('".$filter[5]."','DD-MON-YYYY') >= trim(U571))";
		}

		$where .= $filter_quer;
	}
	$where .= " AND NOT (LTRIM(RTRIM(U571)) IS NULL and LTRIM(RTRIM(U305)) IS NULL and LTRIM(RTRIM(U353)) IS NULL)
	AND BAND NOT LIKE '%MSH%'";
	$order= " ORDER BY SITE_ID,U571";
	$filter_quer="";
	$qu="SELECT * FROM VW_NET1_UPGRADES WHERE " .$where.$order;
	//echo $qu;
	$stmt = parse_exec_fetch($conn_Infobase, $qu, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	}else{
		OCIFreeStatement($stmt);
		$Count_upgrades=count($res1['SITE_ID']);

		for ($i=0;$i<$Count_upgrades;$i++){

			$j=0;
			//echo $res1['U305'][$i]."|".$res1['U353'][$i]."|".$res1['U571'][$i]."<br>";
			if ((trim($res1['U305'][$i])!="" || trim($res1['U353'][$i])!="" || trim($res1['U571'][$i])!="") && trim($res1['BAND'][$i])!="CWK"){
				$UPGNR=trim($res1['WOR_UDK'][$i]);

				$BSDS_funded[$UPGNR]['SITEID']=substr(trim($res1['SITE_ID'][$i]),1);
				$BSDS_funded[$UPGNR]['NET1TYPE']=trim($res1['BAND'][$i]);
				//echo $res1['WOR_NAME'][$i];
				$technos=analyseTechno($res1['WOR_NAME'][$i]);
				$BSDS_funded[$UPGNR]['TECHNOLOGY']=$technos;
				$BSDS_funded[$UPGNR]['UPGNR']=$UPGNR;
				$check_double_upgnrs[$UPGNR]=$UPGNR;	
				$BSDS_funded[$UPGNR]['COMBINED']=$CombinedUpgnrs;
				$BSDS_funded[$UPGNR]['ESTIM']=trim($res1['U571_ESTIM'][$i]);
				$BSDS_funded[$UPGNR]['SAC']=$res1['SAC'][$i];
				$BSDS_funded[$UPGNR]['CON']=$res1['CON'][$i];

				if ($technos==''){
					$BSDS_funded['TECHNOLOGY']="BASE NET1 BOB REPORT ERROR => No technology specified in comments for an UPGRADE!! (".$commentsFieldTechnos.")";
					$BSDS_funded['STATUS']="ERROR";

				}else{
					if (trim($res1['U571'][$i])!=""){
						$BSDS_funded[$UPGNR]['ASBUILD']=trim($res1['U571'][$i]);
						$BSDS_funded[$UPGNR]['DATE']=trim($res1['U571'][$i]);
						$BSDS_funded[$UPGNR]['COLOR']="BSDS_asbuild";
						$BSDS_funded[$UPGNR]['STATUS']="BSDS AS BUILD";
						$BSDS_funded[$UPGNR]['NET1_DATE_SITEFUNDED']=trim($res1['U353'][$i]);
						$ASBUILD_amount++;
					}else if (trim($res1['U305'][$i])!=""){
						$BSDS_funded[$UPGNR]['BSDSFUNDED']=trim($res1['U305'][$i]);
						$BSDS_funded[$UPGNR]['DATE']=trim($res1['U305'][$i]);
						$BSDS_funded[$UPGNR]['COLOR']="BSDS_funded";
						$BSDS_funded[$UPGNR]['STATUS']="BSDS FUNDED";
						$BSDS_funded[$UPGNR]['NET1_DATE_SITEFUNDED']=trim($res1['U353'][$i]);
						$BSDSfunded_amount++;
					}else if (trim($res1['U353'][$i])!=""){
						$BSDS_funded[$UPGNR]['SITEFUNDED']=trim($res1['U353'][$i]);
						$BSDS_funded[$UPGNR]['DATE']=trim($res1['U353'][$i]);
						$BSDS_funded[$UPGNR]['COLOR']="SITE_funded";
						$BSDS_funded[$UPGNR]['STATUS']="SITE FUNDED";
						$BSDS_funded[$UPGNR]['NET1_DATE_SITEFUNDED']=trim($res1['U353'][$i]);
						$SITEfunded_amount++;
					}					

					$pos = inStr( "G9",$technos);
					if ($pos === true) { // note: three equal signs
					    $BSDS_funded[$UPGNR]['G9STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
					    $BSDS_funded[$UPGNR]['G9COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
					}else{
						$BSDS_funded[$UPGNR]['G9STATUS']="PRE READY TO BUILD";
						$BSDS_funded[$UPGNR]['G9COLOR']="BSDS_preready";
					}
					$pos = inStr( "G18",$technos);
					if ($pos === true) { // note: three equal signs
					    $BSDS_funded[$UPGNR]['G18STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
					    $BSDS_funded[$UPGNR]['G18COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
					}else{
						$BSDS_funded[$UPGNR]['G18STATUS']="PRE READY TO BUILD";
						$BSDS_funded[$UPGNR]['G18COLOR']="BSDS_preready";
					}

					$pos1 = inStr("U21",$technos);					
					if ($pos1 === true ) {
					    $BSDS_funded[$UPGNR]['U21STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
					    $BSDS_funded[$UPGNR]['U21COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
					}else{
						$BSDS_funded[$UPGNR]['U21STATUS']="PRE READY TO BUILD";
						$BSDS_funded[$UPGNR]['U21COLOR']="BSDS_preready";
					}

					$pos = inStr("U9",$technos);
					if ($pos === true) { // note: three equal signs
						$BSDS_funded[$UPGNR]['U9STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
						$BSDS_funded[$UPGNR]['U9COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
					}else{
						$BSDS_funded[$UPGNR]['U9STATUS']="PRE READY TO BUILD";
						$BSDS_funded[$UPGNR]['U9COLOR']="BSDS_preready";
					}

					$pos = inStr("L8",$technos);
					if ($pos === true) { // note: three equal signs
						$BSDS_funded[$UPGNR]['L8STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
						$BSDS_funded[$UPGNR]['L8COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
					}else{
						$BSDS_funded[$UPGNR]['L8STATUS']="PRE READY TO BUILD";
						$BSDS_funded[$UPGNR]['L8COLOR']="BSDS_preready";
					}

					$pos = inStr("L18",$technos);
					if ($pos === true) { // note: three equal signs
						$BSDS_funded[$UPGNR]['L18STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
						$BSDS_funded[$UPGNR]['L18COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
					}else{
						$BSDS_funded[$UPGNR]['L18STATUS']="PRE READY TO BUILD";
						$BSDS_funded[$UPGNR]['L18COLOR']="BSDS_preready";
					}

					$pos = inStr("L26",$technos);
					if ($pos === true) { // note: three equal signs
						$BSDS_funded[$UPGNR]['L26']=$BSDS_funded[$UPGNR]['STATUS'];
						$BSDS_funded[$UPGNR]['L26']=$BSDS_funded[$UPGNR]['COLOR'];
					}else{
						$BSDS_funded[$UPGNR]['L26STATUS']="PRE READY TO BUILD";
						$BSDS_funded[$UPGNR]['L26COLOR']="BSDS_preready";
					}
					$UPGNR++;
				}
			}
		}//END FOR LOOP
		/*

		print_r($check_double_upgnrs);
		print_r(array_unique($check_double_upgnrs));
		*/
	}
/**************************************************************************************************************************************************/
	$query = "SELECT * FROM VW_NET1_NEWBUILDS WHERE "; //N1_POSTDTB_TECHFUNDED_ALLNB_V2
	if ($fname){
		$query .= "SITENAME LIKE '".$fname."'";
	}
	if ($filter!=""){
		if ($filter[2] && $filter[3]){
			$filter_quer.="(TO_DATE('".$filter[2]."','DD-MON-YYYY') <= trim(A305)
			AND TO_DATE('".$filter[3]."','DD-MON-YYYY') >= trim(A305))";
		}

		if ($filter[2] && $filter[3] && $filter[0] && $filter[1]){
			$filter_quer.=" AND ";
		}
		if ($filter[0] && $filter[1]){
			$filter_quer.="(TO_DATE('$filter[0]','DD-MON-YYYY') <= trim(A353)
			AND TO_DATE('".$filter[1]."','DD-MON-YYYY') >= trim(A353))";
		}
		if ( (($filter[2] && $filter[3]) && ($filter[4] && $filter[5]))
		|| (($filter[0] && $filter[1]) && ($filter[4] && $filter[5])))
		{
			$filter_quer.=" AND ";
		}
		if ($filter[4] && $filter[5]){
			$filter_quer.="
			(TO_DATE('".$filter[4]."','DD-MON-YYYY') <= trim(A71)
			AND TO_DATE('".$filter[5]."','DD-MON-YYYY') >= trim(A71))";
		}
		$query .= $filter_quer." ORDER BY SITENAME";
	}
	//echo "<font size=1>$query</font><br>";

	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
	}else{
		OCIFreeStatement($stmt);
	    $Count_newbuilds=count($res1['SITENAME']);
	    //echo "$Count2 <br>";
	    $j=0;

		for ($i=0;$i<$Count_newbuilds;$i++){
			$UPGNR=trim($res1['SIT_ID'][$i]);
			$BSDS_funded[$UPGNR]['UPGNR']=$UPGNR;
			$techs_memory.=trim($res1['BAND'][$i])."*";

			$BSDS_funded[$UPGNR]['NET1TYPE']=trim($res1['SITE_TYPE'][$i]);
			$BSDS_funded[$UPGNR]['SAC']=$res1['SAC'][$i];
			$BSDS_funded[$UPGNR]['CON']=$res1['CON'][$i];

			if ($BSDS_funded[$UPGNR]['NET1TYPE']!="COMB"  &&  $BSDS_funded[$UPGNR]['NET1TYPE']!="DUAL"){
				$technos=analyseTechno($res1['BAND'][$i]);
				if ($res1['BAND'][$i]!=""){
					$BSDS_funded[$UPGNR]['TECHNOLOGY']=$technos;

					if (trim($res1['A305'][$i])!="" || trim($res1['A353'][$i])!="" || trim($res1['A71'][$i])!=""){

						$BSDS_funded[$UPGNR]['SITEID']=substr(trim($res1['SITENAME'][$i]),1);
						$BSDS_funded[$UPGNR]['ESTIM']=trim($res1['A71_ESTIM'][$i]);

						$start = 0;


						if (trim($res1['A71'][$i])!=""){
							$BSDS_funded[$UPGNR]['ASBUILD']=trim($res1['A71'][$i]);
							$BSDS_funded[$UPGNR]['DATE']=trim($res1['A71'][$i]);
							$BSDS_funded[$UPGNR]['COLOR']="BSDS_asbuild";
							$BSDS_funded[$UPGNR]['STATUS']="BSDS AS BUILD";
							$BSDS_funded[$UPGNR]['NET1_DATE_SITEFUNDED']=trim($res1['A353'][$i]);
							$ASBUILD_amount++;
						}elseif (trim($res1['A305'][$i])!=""){
							$BSDS_funded[$UPGNR]['BSDSFUNDED']=trim($res1['A305'][$i]);
							$BSDS_funded[$UPGNR]['DATE']=trim($res1['A305'][$i]);
							$BSDS_funded[$UPGNR]['COLOR']="BSDS_funded";
							$BSDS_funded[$UPGNR]['STATUS']="BSDS FUNDED";
							$BSDS_funded[$UPGNR]['NET1_DATE_SITEFUNDED']=trim($res1['A353'][$i]);
							$BSDSfunded_amount++;
						}elseif (trim($res1['A353'][$i])!=""){
							$BSDS_funded[$UPGNR]['SITEFUNDED']=trim($res1['A353'][$i]);
							$BSDS_funded[$UPGNR]['DATE']=trim($res1['A353'][$i]);
							$BSDS_funded[$UPGNR]['COLOR']="SITE_funded";
							$BSDS_funded[$UPGNR]['STATUS']="SITE FUNDED";
							$BSDS_funded[$UPGNR]['NET1_DATE_SITEFUNDED']=trim($res1['A353'][$i]);
							$SITEfunded_amount++;
						}

						$pos = inStr( "G9",$technos);
						if ($pos === true) { // note: three equal signs
						    $BSDS_funded[$UPGNR]['G9STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
						    $BSDS_funded[$UPGNR]['G9COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
						}else{
							$BSDS_funded[$UPGNR]['G9STATUS']="PRE READY TO BUILD";
							$BSDS_funded[$UPGNR]['G9COLOR']="BSDS_preready";
						}
						$pos = inStr( "G18",$technos);
						if ($pos === true) { // note: three equal signs
						    $BSDS_funded[$UPGNR]['G18STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
						    $BSDS_funded[$UPGNR]['G18COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
						}else{
							$BSDS_funded[$UPGNR]['G18STATUS']="PRE READY TO BUILD";
							$BSDS_funded[$UPGNR]['G18COLOR']="BSDS_preready";
						}
						$pos1 = inStr("U21",$technos);
						if ($pos1 === true) {
						    $BSDS_funded[$UPGNR]['U21STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
						    $BSDS_funded[$UPGNR]['U21COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
						}else{
							$BSDS_funded[$UPGNR]['U21STATUS']="PRE READY TO BUILD";
							$BSDS_funded[$UPGNR]['U21COLOR']="BSDS_preready";
						}
						$pos = inStr("U9",$technos);
						if ($pos === true) { // note: three equal signs
							$BSDS_funded[$UPGNR]['U9STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
							$BSDS_funded[$UPGNR]['U9COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
						}else{
							$BSDS_funded[$UPGNR]['U9STATUS']="PRE READY TO BUILD";
							$BSDS_funded[$UPGNR]['U9COLOR']="BSDS_preready";
						}
						$pos = inStr("L8",$technos);
						if ($pos === true) { // note: three equal signs
							$BSDS_funded[$UPGNR]['L8STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
							$BSDS_funded[$UPGNR]['L8COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
						}else{
							$BSDS_funded[$UPGNR]['L8STATUS']="PRE READY TO BUILD";
							$BSDS_funded[$UPGNR]['L8COLOR']="BSDS_preready";
						}
						$pos = inStr("L18",$technos);
						if ($pos === true) { // note: three equal signs
							$BSDS_funded[$UPGNR]['L18STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
							$BSDS_funded[$UPGNR]['L18COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
						}else{
							$BSDS_funded[$UPGNR]['L18STATUS']="PRE READY TO BUILD";
							$BSDS_funded[$UPGNR]['L18COLOR']="BSDS_preready";
						}
						$pos = inStr("L26",$technos);
						if ($pos === true) { // note: three equal signs
							$BSDS_funded[$UPGNR]['L26STATUS']=$BSDS_funded[$UPGNR]['STATUS'];
							$BSDS_funded[$UPGNR]['L26COLOR']=$BSDS_funded[$UPGNR]['COLOR'];
						}else{
							$BSDS_funded[$UPGNR]['L26STATUS']="PRE READY TO BUILD";
							$BSDS_funded[$UPGNR]['L26COLOR']="BSDS_preready";
						}

					}
				}else{
					return 'technoerror';
				}
			}else{
				$BSDS_funded[$UPGNR]['TECHNOLOGY']="BASE BOB/NET1 REPORT INCONSISTENCY!! (".$BSDS_funded[$UPGNR]['TECHNOLOGY'].")<br>Please contact <a href='mailto:julie.vynckx@kpngroup.be'>Network delivery</a>";
				$BSDS_funded[$UPGNR]['ASBUILD']="";
				if (trim($res1['U571'][$i])!=""){
					$date=trim($res1['U571'][$i]);
				}else if (trim($res1['U305'][$i])!=""){
					$date=trim($res1['U305'][$i]);
				}else if (trim($res1['U353'][$i])!=""){
					$date=trim($res1['U353'][$i]);
				}
				$BSDS_funded[$UPGNR]['DATE']=$date;
				$BSDS_funded[$UPGNR]['COLOR']="BSDS_preready";
				$BSDS_funded[$UPGNR]['STATUS']="ERROR";
			}
		}
	}
	//echo "<pre>".print_r($BSDS_funded,true)."</pre>";
	return  $BSDS_funded;
}
?>
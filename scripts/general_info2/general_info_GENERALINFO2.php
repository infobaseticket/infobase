<?php

function special_STATUSCHANGE_insert($RAFID,$BSDSKEY){
	
	global $conn_Infobase;
	
	$queryMA="SELECT N1_STATUS,
		IB_RAFID, N1_SITEID,N1_CANDIDATE,N1_UPGNR,N1_NBUP,IB_BSDSSTATUS,AU353,AU305,A80U380,N1_STATUS,IB_RAFTYPE
	FROM
		MASTER_REPORT WHERE IB_RAFID = '".$RAFID."' AND (AU353 IS NOT NULL or AU305 IS NOT NULL or A80U380 IS NOT NULL)";
	//echo $queryMA;
	$stmtMASTER = parse_exec_fetch($conn_Infobase, $queryMA, $error_str, $resMAS);
   	if (!$stmtMASTER) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmtMASTER);
   	}
   	$amount_in_MASTER=count($resMAS['IB_RAFID']);

	if($amount_in_MASTER=="0"){
		$queryRAF="SELECT DELETED, DELETE_REASON FROM BSDS_RAFV2 WHERE RAFID = '".$RAFID."'";
		//echo $queryRAF;
		$stmtRAF = parse_exec_fetch($conn_Infobase, $queryRAF, $error_str, $resRAF);
	   	if (!$stmtRAF) {
	      die_silently($conn_Infobase, $error_str);
	      exit;
	   	} else {
	      OCIFreeStatement($stmtRAF);
	   	}
	   	if ($resRAF['DELETED'][0]=="yes"){
	   		$msg= "The BSDS ".$BSDSKEY." attached to RAFID ".$RAFID." is not funded in NET1. The RAF has been deleted with following reaseon:<br>".$resRAF['DELETE_REASON'][0]."<br>";
	   	}else{
	   		$msg= "The BSDS ".$BSDSKEY." attached to RAFID ".$RAFID." is not funded in NET1!<br>As soon as the BSDS is SITE funded (=UA353 in NET1 toggled), it will appear on this location in green.<br>You can adapt the PRE BSDS by clicking the history button on the right.<br>";
	   	}
   		
   	}else if ($resMAS['N1_STATUS'][0]!='IS'){
   		$msg= "The site/UPG (".$resMAS['N1_CANDIDATE'][0]."/".$resMAS['N1_UPGNR'][0].") for RAF ".$resMAS['IB_RAFTYPE'][0]." is not ISSUED (IS) in NET1 but has been put to ".$resMAS['N1_STATUS'][0]."!<br>";
   	}else if($amount_in_MASTER=="1"){

   		$query = "INSERT INTO BSDS_STATUS_CHANGES
		select '',SYSDATE,N1_SITEID,N1_CANDIDATE,N1_NBUP,N1_UPGNR,AU353,'INITIAL',AU305,'INITIAL',
		A80U380, '',IB_RAFID,BSDSKEY,
		CASE 
		WHEN A80U380 IS NOT NULL THEN
			'ADD BSDS AS BUILD'
		WHEN AU305 IS NOT NULL THEN
			'ADD BSDS FUNDED'
		ELSE 'ADD SITE FUNDED'
		END AS ACTION,
		CASE WHEN A80U380 IS NOT NULL THEN
			'BSDS AS BUILD'
		WHEN AU305 IS NOT NULL THEN
			'BSDS FUNDED'
		ELSE 'SITE FUNDED'
		END AS CURRENTSTATUS,
		CASE WHEN AU305 IS NOT NULL THEN
			AU305
		ELSE AU353
		END AS CURRENTNET1DATE,
		'0', IB_TECHNOS_CON
		from MASTER_REPORT WHERE N1_STATUS='IS' 
		AND NOT (AU353 IS NULL AND AU305 IS NULL)
		AND BSDSKEY ='".$BSDSKEY."'";
		//echo $query;
		
		$stmtCH = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmtCH) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}

		$msg= "The BSDS ".$BSDSKEY." with RAFID ".$RAFID." (".$resMAS['N1_NBUP'][0].": ".$resMAS['N1_UPGNR'][0].") has a status <u>".$resMAS['IB_BSDSSTATUS'][0]."</u> in NET1, however Infobase cannot find the status change in it's BSDS_STATUS_CHANGES table!<br>Record created -> Please reload BSDS by clicking on BSDS icon<br>";
				   		
   	}
   	return $msg;
}


$amount_BSDS_withRAF=0;
foreach ($res1['BSDSKEY'] as $key=>$attrib_id){
	$amount_fund=0;
	$BSDSKEY=$res1['BSDSKEY'][$key];
	$SITEKEY=$res1['SITEKEY'][$key];
	$SITEID=$res1['SITEID'][$key];
	//$TEAML_APPROVED=$res1['TEAML_APPROVED'][$key];
	$DESIGNER_CREATE=$res1['DESIGNER_CREATE'][$key];
		$userdetails=getuserdata($DESIGNER_CREATE);
		$email=$userdetails['email'];
		$fullname=$userdetails['fullname'];
		$mobile=$userdetails['mobile'];
	$CHANGE_DATE=$res1['CHANGE_DATE'][$key];
	$COMMENTS=$res1['COMMENTS'][$key];
	$ORIGIN_DATE=$res1['ORIGIN_DATE'][$key];
	$BSDS_TYPE=$res1['BSDS_TYPE'][$key];
	$DESIGNER_UPDATE=$res1['DESIGNER_UPDATE'][$key];
	$UPDATE_AFTER_COPY=$res1['UPDATE_AFTER_COPY'][$key];
	$UPDATE_AFTER_COPY=$res1['UPDATE_AFTER_COPY'][$key];
	$RAFID=$res1['RAFID'][$key];
	$RAFTYPE=$res1['RAFTYPE'][$key];

	$userdetails=getuserdata($DESIGNER_CREATE);
	$email=trim($userdetails['email']);
	$fullname=trim($userdetails['fullname']);
	$mobile=trim($userdetails['mobile']);

	$disable_button3='';
	$error='';
	//If the RAFID is NULL, this means that the data will be displayed in the history
	if ($RAFID==''){
		include("general_info_popup.php");
		$output_form.="
			<form id='bsdsform_".$popup."'>
			<input type='hidden' name='bsdskey' value='".$BSDSKEY."'>
			<input type='hidden' name='bsdsbobrefresh' value='PRE'>
			<input type='hidden' name='technosAsset' value='".$technosInAsset."'>
			<input type='hidden' name='donor' value='".$_POST['donor']."'>
			<input type='hidden' name='siteid' value='".$_POST['siteID']."'>
			<input type='hidden' name='candidate' value='".$_POST['candidate']."'>
			<input type='hidden' name='status' value='PREHIST'>
			<input type='hidden' name='rafid' value=''>
			</form>";
		if ($_POST['bsdskey']==$res1['BSDSKEY'][$key]){
			$style='style="background-color:yellow;"';
		}else{
			$style='';
		}
		//<td>".$TEAML_APPROVED."</td>
		$output_pre_history .= "
		<tr id='bsdsrow_".$BSDSKEY."' class='history_data historyPerdate_data' style='display:none;'>
			<td ".$style."'>
			<a href='#' data-toggle='modal' data-target='#".$BSDSKEY.$popup."".$res1['SIT_UDK'][$i]."'>".$BSDSKEY."</a></td>
			<td>NA</td>
			<td>NA</td>
			<td>".$CHANGE_DATE."</td>
			<td class='BSDS_preready'>PRE BSDS WITH NO ATTACHED RAF</td>
			<td>".$BSDS_TYPE."</td>
			
			<td style='width:100px;'>";
			if ((substr_count($guard_groups, 'BSDS_view')=="1"  && ($READY_FOR_APP=="yes" || $READY_FOR_APP=="Yes")) || substr_count($guard_groups, 'Radioplanners')=="1"){//&& $TEAML_APPROVED=="Accepted"
				
				$output_pre_history.='<div class="btn-group dropup"> 
				  <button class="btn btn-xs" data-id="'.$popup.'" data-techno="ALL">Action</button>
				  <button class="btn btn-xs dropdown-toggle" data-toggle="dropdown">
				    <span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu pull-right">';
				  	if(strpos($technosInAsset, 'G9')!==false){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="G9" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM900</a></li>';
	                }  
	                if(strpos($technosInAsset, 'G18')!==false){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="G18" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM1800</a></li>';
	                } 
	                if(strpos($technosInAsset, 'U9')!==false){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="U9" data-bsdskey="'.$BSDSKEY.'" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS900</a></li>';
	                }
	                if(strpos($technosInAsset, 'U21')!==false){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="U21" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS2100</a></li>';
	                }
	                if(strpos($technosInAsset, 'L8')!==false){ 
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="L8" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE800</a></li>';
	                }
	                if(strpos($technosInAsset, 'L18')!==false){ 
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="L18" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE1800</a></li>';
	                }
	                if(strpos($technosInAsset, 'L26')!==false){  
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="L26" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE2600</a></li>';	       
	                }  
	                $output_pre_history.='<li class="bsdsdetails2 '.$statuscolor.'" data-id="'.$popup.'" data-techno="BBU" data-bsdskey="'.$BSDS_BSDSKEY.'"> <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> BBU</a></li>';
       		
	                  $output_pre_history.='<li class="divider"></li>
	                  <li data-id="'.$popup.'"  data-techno="ALL" data-technos="'.$technosInAsset.'" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
		              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> ALL</a></li>
	                  <li data-techno="PRINT" class="bsdsdetails2" data-technos="'.$technosInAsset.'" data-bsdskey="'.$BSDSKEY.'" data-id="'.$popup.'">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-print"></span> PRINT</li>';
				 	if (substr_count($guard_groups, 'Administrators')=="1" || substr_count($guard_groups, 'Radioplanners')=="1"){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="DELETEBSDS" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	                  &nbsp; <span class="glyphicon glyphicon-trash"></span> DELETE</li>';
				 	}
				 	$output_pre_history.='
				  </ul>
				</div>';
 					
			}
		$output_pre_history .="</td></tr>";

		$statuscolor="";
	}else{ //THE PURPLE ONE (which has RAFID)
		$amount_BSDS_withRAF++;
		//We GET NET1 DATA:
		$queryN1="SELECT
					*
				FROM
					BSDS_STATUS_CHANGES t1
				INNER JOIN (
					SELECT
						MAX (REPORTDATE) AS REPORTDATE
					FROM
						BSDS_STATUS_CHANGES
					WHERE
						IB_RAFID = '".$RAFID."'
				) t2 ON t1. REPORTDATE = t2. REPORTDATE AND IB_RAFID = '".$RAFID."'";
		//echo $queryN1;
		$stmtN1 = parse_exec_fetch($conn_Infobase, $queryN1, $error_str, $resN1);
	   	if (!$stmtN1) {
	      die_silently($conn_Infobase, $error_str);
	      exit;
	   	} else {
	      OCIFreeStatement($stmtN1);
	   	}

	   	$amountNET1data=count($resN1['N1_SITEID']);
	   	$N1UPGNR=$resN1['N1_UPGNR'][0];
	   	$N1_NBUP=$resN1['N1_NBUP'][0];
	   	
	   	if ($amountNET1data==1){
	   		$STATUS_AS_PER_NET1=$resN1['CURRENTSTATUS'][0];
	   		$DATE_IN_N1=$resN1['CURRENTNET1DATE'][0];
		   	if (trim($STATUS_AS_PER_NET1)=="BSDS AS BUILD"){
		   		$wiewtype_NET1='BUILD';
		   		$statuscolor2="BSDS_asbuild";
		   	}else if (trim($STATUS_AS_PER_NET1)=="BSDS FUNDED"){
		   		$wiewtype_NET1='FUND';
		   		$statuscolor2="BSDS_funded";
		   	}else if (trim($STATUS_AS_PER_NET1)=="SITE FUNDED"){
		   		$wiewtype_NET1='POST';
		   		$statuscolor2="SITE_funded";
		   	}else{
		   		echo "ERROR";
		   		die;
		   	}
		   	
		}elseif ($amountNET1data==0){
			$STATUS_AS_PER_NET1="PRE BSDS";
			$DATE_IN_N1='NOT FUNDED';
		 	$wiewtype_NET1="PRE";
		}elseif ($amountNET1data>1){
			$msg= "2 STATUS CHANGES WITH THE SAME DATE AND TIME! Please contact Frederick Eyland<br>";
			$output_pre.="<tr class='danger'><td colspan='8'><b>".$msg."</b></td></tr>";
			$disable_button='yes';
			$error='yes';
		}

		//echo $RAFID."----".$STATUS_AS_PER_NET1."---".$resN1['ACTION'][0]."<br>";

	   	$AU353= strtotime(substr($resN1['AU353'][0],-4,4)."-".substr($resN1['AU353'][0],3,2)."-".substr($resN1['AU353'][0],0,2));
	   	$AU305= strtotime(substr($resN1['AU305'][0],-4,4)."-".substr($resN1['AU305'][0],3,2)."-".substr($resN1['AU305'][0],0,2));
	   	$A80U380= strtotime(substr($resN1['A80U380'][0],-4,4)."-".substr($resN1['A80U380'][0],3,2)."-".substr($resN1['A80U380'][0],0,2));
	
		$disable_button="no";

		$queryTECH="SELECT IB_TECHNOS_CON FROM MASTER_REPORT WHERE IB_RAFID = '".$RAFID."'";
		//echo $queryTECH;
		$stmtTECH = parse_exec_fetch($conn_Infobase, $queryTECH, $error_str, $resTECH);
	   	if (!$stmtTECH) {
	      die_silently($conn_Infobase, $error_str);
	      exit;
	   	} else {
	      OCIFreeStatement($stmtTECH);
	   	}
	   

	   	if ($resN1['IB_TECHNOS_CON'][0]!=$resTECH['IB_TECHNOS_CON'][0] && $resN1['IB_TECHNOS_CON'][0]!=""){
	   		$msg= "FOR BSDS ".$resN1['IB_BSDSKEY'][0]." with RAFID ".$RAFID." the technologies funded in the RAF have changed after the BSDS funding from ".$resN1['IB_TECHNOS_CON'][0]." to ".$resTECH['IB_TECHNOS_CON'][0]."!<br>";
			$msg.="If you think this is not correct, please contact Base";
			$output_pre.="<tr class='warning'><td colspan='8'><b>".$msg."</b></td></tr>";
			$disable_button3='no';
			$error='no';
	   	}
		//We hand all errors and display a meesage if necassry
		if($resN1['ACTION'][0]=="ERROR BSDS FUNDED"){
			$msg= "FOR BSDS ".$resN1['IB_BSDSKEY'][0]." with RAFID ".$RAFID." the BSDS FUNDED date ".$resN1['AU305'][0]." (AU305) has to be bigger than  SITE FUNDED date ".$resN1['AU353'][0]." (AU353)!<br>";
			$msg.="Please put a newer date for AU305";
			$output_pre.="<tr class='danger'><td colspan='8'><b>".$msg."</b></td></tr>";
			$disable_button3='yes';
			$error='yes';
		}
		if($resN1['ACTION'][0]=="ERROR BSDS AS BUILD"){
			$msg= "FOR BSDS ".$resN1['IB_BSDSKEY'][0]." with RAFID ".$RAFID." the AS BUILD date ".$resN1['A80U380'][0]." (A80U380) has to be bigger than BSDS FUNDED date ".$resN1['AU305'][0]." AU305)!<br>";
			$msg.="Please put a newer date for A80U380";
			$output_pre.="<tr class='danger'><td colspan='8'><b>".$msg."</b></td></tr>";
			$disable_button3='yes';
			$error='yes';
		}

		if($resN1['ACTION'][0]=="MISSING DATES BSDS FUNDED"){
			$msg= "FOR BSDS ".$resN1['IB_BSDSKEY'][0]." with RAFID ".$RAFID.": BEFORE YOU CAN TOGGLE BSDS AS BUILD (A80U380) you need to make sure that BSDS FUNDED (AU305) AND SITE FUNDED (AU353) IS TOGGLED IN NET1!<br>";
			if ($resN1['AU353'][0]==''){
			$msg.="Please put a date for SITE FUNDED (AU353) in NET1.";
			}else if ($resN1['AU305'][0]==''){
			$msg.="Please put a date for BSDS FUNDED (AU305) in NET1.";
			}
			$output_pre.="<tr class='danger'><td colspan='8'><b>".$msg."</b></td></tr>";
			$disable_button='yes';
			$error='yes';
		}

		if($resN1['ACTION'][0]=="MISSING DATES BSDS AS BUILD"){
			$msg= "FOR BSDS ".$resN1['IB_BSDSKEY'][0]." with RAFID ".$RAFID.": BEFORE YOU CAN TOGGLE BSDS FUNDED (AU305) you need to make sure that SITE FUNDED (AU353) IS TOGGLED IN NET1!<br>";
			$msg.="Please put a date for SITE FUNDED (AU353) in NET1.";
			$output_pre.="<tr class='danger'><td colspan='8'><b>".$msg."</b></td></tr>";
			$disable_button='yes';
			$error='yes';
		}

		if($resN1['ACTION'][0]=="ERROR SAME DATES"){
			$msg= "FOR BSDS ".$resN1['IB_BSDSKEY'][0]." with RAFID ".$RAFID.": YOU'VE SET THE SAME DATES FOR 'BSDS FUNDED BSDS' AND 'SITE FUNDED BSDS'<br>OR FOR 'AS BUILD BSDS' AND 'BSDS FUNDED BSDS'!<br>";
			$msg.="Please put different dates in NET1.";
			$output_pre.="<tr class='danger'><td colspan='8'><b>".$msg."</b></td></tr>";
			$disable_button='yes';
			$error='yes';
		}

		if($resN1['ACTION'][0]=="REMOVE AS BUILD BSDS"){
			$msg= "FOR BSDS ".$resN1['IB_BSDSKEY'][0]." with RAFID ".$RAFID.": IT IS NOT ALLOWED TO REMOVE A80U380 IN NET1 FOR ".$resN1['N1_CANDIDATE'][0]." ".$resN1['N1_UPGNR'][0]."!<br>";
			$msg.="Please restore A80U380 to ".$resN1['A80U380_BEFORE'][0].".";
			$output_pre.="<tr class='danger'><td colspan='8'><b>".$msg."</b></td></tr>";
			$disable_button='yes';
			$error='yes';
		}


		if($resN1['EXECUTED'][0]==0 && $resN1['EXECUTED'][0]!=''){
			$msg= "FOR BSDS ".$resN1['IB_BSDSKEY'][0]." with RAFID ".$RAFID.": The BSDS has not yet been processed by Infobase!<br>";
			$msg.="Please wait for the next refresh in +- 5min.";
			$output_pre.="<tr class='danger'><td colspan='8'><b>".$msg."</b></td></tr>";
			$disable_button='yes';
			$disable_button3='yes';
			$error='yes';
		}

	   	$pos = strpos($STATUS_DATE_NET1, '/');
		if ($pos === true){
			echo "PROBLEM WITH NET1 DATE FORMAT! PLEASE CONTACT INFOBASE ADMIN ASAP";
			die;
		}
			// GET ALL FUNDED & AS BUILD BSDSs
	   	$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC22 WHERE BSDSKEY='".$BSDSKEY."' ORDER BY BSDSKEY,BSDS_BOB_REFRESH DESC";
	   	//echo $query;
		$stmtTEAML = parse_exec_fetch($conn_Infobase, $query, $error_str, $resTEAML);
		if (!$stmtTEAML) {
			die_silently($conn_Infobase, $error_str);
			exit;
		} else {
			OCIFreeStatement($stmtTEAML);
			$amount_in_teamlacc=count($resTEAML['BSDSKEY']);
		}	

		//echo $RAFID."----".$STATUS_AS_PER_NET1."---".$resN1['ACTION'][0]."<br>";	
		//echo "amount_in_teamlacc=".$amount_in_teamlacc."\r\n";
		if ($amount_in_teamlacc!=0){

			$query = "SELECT AU353, AU305, A80U380, BSDSKEY FROM MASTER_REPORT WHERE BSDSKEY='".$BSDSKEY."' AND IB_RAFID='".$RAFID."'";
		   	//echo $query;
			$stmtMA = parse_exec_fetch($conn_Infobase, $query, $error_str, $resMA);
			if (!$stmtTEAML) {
				die_silently($conn_Infobase, $error_str);
				exit;
			} else {
				OCIFreeStatement($stmtMA);
				$amount_in_MA=count($resMA['BSDSKEY']);
			}
	
			if  ($amount_in_MA!=0){
				//echo $resMA['AU305'][0];
				if($resMA['A80U380'][0]!='' && $resN1['CURRENTSTATUS'][0]!="BSDS AS BUILD"){
					echo "BSDS SHOULD BE AS BUILD";
				}else if($resMA['AU305'][0]!='' && $resN1['CURRENTSTATUS'][0]!="BSDS FUNDED" && $resN1['CURRENTSTATUS'][0]!="BSDS AS BUILD" && $resN1['ACTION'][0]!="REMOVE BSDS FUNDED"){
					echo "BSDS SHOULD BE BSDS FUNDED";
					$msg=special_STATUSCHANGE_insert($RAFID,$BSDSKEY);
					$output_pre .="<tr class='warning'><td colspan='8'><b>".$msg."</b></td></tr>";

					$disable_button2="yes";
					$disable_button="yes";
				}
			}

			$amount_fund_or_build=0;
			foreach ($resTEAML['BSDSKEY'] as $keyT=>$attrib_idT){
				
				include("general_info_fund_data.php");
			}
			//echo $amount_fund_or_build;
			if($resN1['ACTION'][0]=="REMOVE BSDS FUNDED"){
				$msg= "THE DATE FOR BSDS FUNDED (AU305) HAS BEEN REMOVED FOR ".$resN1['N1_CANDIDATE'][0]." ".$resN1['N1_UPGNR'][0]." IN NET1!<br>";
				$msg.="This will allow you to modify following (SITE FUNDED) red BSDS.<br>";
				$msg.="You can refund by pressing the FUND button under action<br>";
				$output_pre.="<tr class='warning'><td colspan='8'><b>".$msg."</b></td></tr>";
				$disable_button='no';
				$error='no';
				$match_found='no';
			}

			if  ($amountNET1data!=0 && $amount_fund_or_build==0 && $error!='yes'){
				if ($resN1['EXECUTED'][0]==0){
				$msg= "The BSDS ".$BSDSKEY." with RAFID ".$RAFID." (".$N1_NBUP.": ".$N1UPGNR.") has <u>changed status to ".$STATUS_AS_PER_NET1."</u> in NET1 but Infobase did not yet processed the BSDS.<br>Please wait for next refresh cycle in Infobase!<br>";
				}else{
					$msg="--The BSDS ".$BSDSKEY." with RAFID ".$RAFID." (".$N1_NBUP.": ".$N1UPGNR.") has satus <u>".$STATUS_AS_PER_NET1."</u> in NET1, however Infobase was unable to process the BSDS due to inconsistancy in the database:<br>&nbsp;&nbsp;&nbsp;Please contact Infobase admin and ask to rexecute manually.";
					if($amount_fund_or_build==''){
						$msg.="<br><i>The BSDS has a status of ".$STATUS_AS_PER_NET1." and there are no ".$STATUS_AS_PER_NET1." records in BSDS_FUNDED_TEAMLACC22 with corresponding funding date</i>";
					}
				}
				if ($match_found=='yes'){
					$msg.="<b><font color='red'>PLEASE MAKE SURE THAT YOU REFUND WITH A NEWER DATE THAN ".$problem."! (now set to ".$DATE_IN_N1.")</font></b>";
				}
				$output_pre .="<tr class='danger'><td colspan='8'><b>".$msg."</b></td></tr>";
				$disable_button2="yes";
				$disable_button="yes";
			}else if($amountNET1data==0 && $amount_fund_or_build==0 && $error!='yes'){
				echo "SPECIAL1";
				$msg=special_STATUSCHANGE_insert($RAFID,$BSDSKEY);

				$output_pre .="<tr class='warning'><td colspan='8'><b>".$msg."</b></td></tr>";

				$disable_button2="yes";
				$disable_button="yes";
			}


		}else{

			if ($STATUS_AS_PER_NET1=='PRE BSDS' or $STATUS_AS_PER_NET1=='SITE FUNDED' && $resN1['EXECUTED'][0]!=0){
				$msg=special_STATUSCHANGE_insert($RAFID,$BSDSKEY);
				echo "SPECIAL2";
				if($STATUS_AS_PER_NET1=='PRE BSDS'){
					include("general_info_popup.php");
					$output_form.="
						<form id='bsdsform_".$popup."'>
						<input type='hidden' name='bsdskey' value='".$BSDSKEY."'>
						<input type='hidden' name='bsdsbobrefresh' value='PRE'>
						<input type='hidden' name='technosAsset' value='".$technosInAsset."'>
						<input type='hidden' name='donor' value='".$_POST['donor']."'>
						<input type='hidden' name='siteid' value='".$_POST['siteID']."'>
						<input type='hidden' name='candidate' value='".$_POST['candidate']."'>
						<input type='hidden' name='status' value='PRE'>
						<input type='hidden' name='rafid' value='".$RAFID."'>
						</form>";
					if ($_POST['bsdskey']==$res1['BSDSKEY'][$key]){
						$style='style="background-color:yellow;"';
					}else{
						$style='';
					}
					//<td>".$TEAML_APPROVED."</td>
					$output_pre_history .= "
					<tr id='bsdsrow_".$BSDSKEY."' class='history_data historyPerdate_data' style='display:none;'>
						<td ".$style."'>
						<a href='#' data-toggle='modal' data-target='#".$BSDSKEY.$popup."".$res1['SIT_UDK'][$i]."'>".$BSDSKEY."</a></td>
						<td>NA</td>
						<td>NA</td>
						<td>".$CHANGE_DATE."</td>
						<td class='BSDS_preready'>PRE BSDS ATTACHED TO RAF ".$RAFID."<br>The BSDS is not yet funded in N1</td>
						<td>".$BSDS_TYPE."</td>
						
						<td style='width:100px;'>";
						if ((substr_count($guard_groups, 'BSDS_view')=="1"  && ($READY_FOR_APP=="yes" || $READY_FOR_APP=="Yes")) || substr_count($guard_groups, 'Radioplanners')=="1"){//&& $TEAML_APPROVED=="Accepted"
							
							$output_pre_history.='<div class="btn-group dropup"> 
							  <button class="btn btn-xs" data-id="'.$popup.'" data-techno="ALL">Action</button>
							  <button class="btn btn-xs dropdown-toggle" data-toggle="dropdown">
							    <span class="caret"></span>
							  </button>
							  <ul class="dropdown-menu pull-right">';
							  	if(strpos($technosInAsset, 'G9')!==false){
				                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="G9" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
				                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM900</a></li>';
				                }  
				                if(strpos($technosInAsset, 'G18')!==false){
				                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="G18" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
				                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM1800</a></li>';
				                } 
				                if(strpos($technosInAsset, 'U9')!==false){
				                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="U9" data-bsdskey="'.$BSDSKEY.'" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
				                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS900</a></li>';
				                }
				                if(strpos($technosInAsset, 'U21')!==false){
				                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="U21" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
				                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS2100</a></li>';
				                }
				                 if(strpos($technosInAsset, 'L8')!==false){ 
				                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="L8" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
				                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE800</a></li>';
				                }
				                if(strpos($technosInAsset, 'L18')!==false){ 
				                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="L18" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
				                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE1800</a></li>';
				                }
				                if(strpos($technosInAsset, 'L26')!==false){  
				                  $output_pre_history.='<li  data-id="'.$popup.'" data-techno="L26" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
				                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE2600</a></li>';	       
				                }  
				                  $output_pre_history.='<li class="divider"></li>
				                  <li data-id="'.$popup.'"  data-techno="ALL" data-technos="'.$technosInAsset.'" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
					              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> ALL</a></li>
				                  <li data-techno="PRINT" class="bsdsdetails2" data-technos="'.$technosInAsset.'" data-bsdskey="'.$BSDSKEY.'" data-id="'.$popup.'">
				                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-print"></span> PRINT</li>';
							 	if (substr_count($guard_groups, 'Administrators')=="1" || substr_count($guard_groups, 'Radioplanners')=="1"){
				                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="DELETEBSDS" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
				                  &nbsp; <span class="glyphicon glyphicon-trash"></span> DELETE</li>';
							 	}
							 	$output_pre_history.='
							  </ul>
							</div>';
			 					
						}
					$output_pre_history .="</td></tr>";
				}

			}else{
				$disable_button2="yes";
				$disable_button="yes";
				if ($resN1['EXECUTED'][0]!=0){
				
					$msg="The BSDS ".$BSDSKEY." with RAFID ".$RAFID." (".$N1_NBUP.": ".$N1UPGNR.") has satus <u>".$STATUS_AS_PER_NET1."</u> in NET1. However Infobase was unable to process the BSDS due to inconsistancy in the database (ececuted!=0). Please contact Infobase admin.";
				}else{

					$msg= "The BSDS (".$BSDSKEY.") with RAFID ".$RAFID." has  <u>a status of ".$STATUS_AS_PER_NET1."</u> in NET1!<br>";
					
					if ($STATUS_AS_PER_NET1=="BSDS AS BUILD" or $STATUS_AS_PER_NET1=="BSDS FUNDED"){
						if ($STATUS_AS_PER_NET1=="BSDS AS BUILD"){
							$extr="BSDS FUNDED and/or SITE FUNDED";
						}else if ($STATUS_AS_PER_NET1=="BSDS FUNDED"){
							$extr="SITE FUNDED";
						}
						$msg.="<b><font color='orange'>As the previous BSDS (".$extr.") is missing, Infobase cannot copy the BSDS.<br>Please defund if you want to make changes.</font></b>";
					}else{
						$msg.="Infobase did not yet processed the BSDS. Please wait for next refresh of InfoBase.";
					}
				}
			}
			

			$output_pre .="<tr class='warning'><td colspan='8'><b>".$msg."</b></td></tr>";
			
		}

/*
/* for the records which are in MASTER table but not in MASTER-1: *

*/
	}
}
?>
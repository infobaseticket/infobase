<?php
$output_fundata="";
$disable_button2='no';

$BSDS_BSDSKEY=$resTEAML['BSDSKEY'][$keyT];
$BSDS_BOB_REFRESH=$resTEAML['BSDS_BOB_REFRESH'][$keyT];
$BSDS_STATUS_DATE_NET1=$resTEAML['NET1_DATE'][$keyT];
$TEAML_FUNDED=$resTEAML['TEAM_STATUS'][$keyT];
$STATUS_BSDS=$resTEAML['STATUS'][$keyT];
$UPGNR=$resTEAML['UPGNR'][$keyT];
$COMMENTS_QA_DECLINED=$resTEAML['COMMENTS'][$keyT];

if ($BSDSKEY==$BSDS_BSDSKEY){

	if($STATUS_BSDS=="BSDS AS BUILD"){
		$statuscolor="BSDS_asbuild";
		$viewtype_BSDS='BUILD';
	}elseif($STATUS_BSDS=="BSDS FUNDED"){
		$statuscolor="BSDS_funded";
		$viewtype_BSDS='FUND';
	}elseif($STATUS_BSDS=="SITE FUNDED"){
		$statuscolor="SITE_funded";
		$viewtype_BSDS='POST';
	}else if($STATUS_BSDS=="REMOVE BSDS FUNDED"){
		$statuscolor="refunded";
		$viewtype_BSDS='POST';
	}else if($STATUS_BSDS=="REMOVE BSDS AS BUILD"){ // DIT MAGE NIET!!!!
		$statuscolor="refunded";
	}else{
		$statuscolor="other";
		$viewtype_BSDS='PRE';
	}

	include("general_info_popup.php");

	$bsds_info="bsds_info_".$popup."_".$_POST['tabid'];
	$bsds_info_toggle="bsds_info_toggle_".$popup."_".$_POST['tabid'];
	$bsds_info_B="#bsds_info_".$popup."_".$_POST['tabid'];
	$bsds_info_toggle_B="#bsds_info_toggle_".$popup."_".$_POST['tabid'];
	$TEAML_FUNDED_SELECT="TEAML_FUNDED_".$popup."_".$_POST['tabid'];
	$TEAML_FUNDED_FORM="TEAML_FORM_".$popup."_".$_POST['tabid'];
	$warning="";


	$output_fundata.= "
	<td><a href='#' data-toggle='modal' data-target='#".$BSDS_BSDSKEY.$popup."'>".$BSDS_BSDSKEY."</a></td>";

	if ($BSDSKEY==$BSDS_BSDSKEY){
		$output_fundata.= "<td><b>".$RAFID."</b></td>";
	}else{
		$output_fundata.= "<td>NA</td>";
	}
	//<td>".$TEAML_APPROVED."</td>
	$output_fundata.= "
	<td>".$resN1['IB_TECHNOS_CON'][0]."</td>
	<td>".$BSDS_BOB_REFRESH."</td>
	<td class='".$statuscolor."'>".$STATUS_BSDS." (".$BSDS_STATUS_DATE_NET1.") [".$N1_NBUP." ".$N1UPGNR."]</td>
	<td>".$BSDS_TYPE."</td>
	
	<td style='width:100px;'>";

	if (($wiewtype_NET1=='FUND' && $viewtype_BSDS=="FUND" && ($resN1['ACTION'][0]=="NO CHANGE" or $resN1['ACTION'][0]=="ADD BSDS FUNDED" ) && $DATE_IN_N1==$BSDS_STATUS_DATE_NET1) or 
			($wiewtype_NET1=='POST' && $viewtype_BSDS=="FUND"  && ($resN1['ACTION'][0]=="NO CHANGE" or $resN1['ACTION'][0]=="ADD SITE FUNDED") && $DATE_IN_N1==$BSDS_STATUS_DATE_NET1) or
			($wiewtype_NET1=='POST' && $viewtype_BSDS=="POST"  && ($resN1['ACTION'][0]=="NO CHANGE" or $resN1['ACTION'][0]=="ADD SITE FUNDED") && $DATE_IN_N1==$BSDS_STATUS_DATE_NET1) or
			($STATUS_BSDS=="REMOVE BSDS FUNDED" && $resN1['ACTION'][0]=="REMOVE BSDS FUNDED" AND $STATUS_BSDS==$resN1['ACTION'][0]) or
			($wiewtype_NET1=='BUILD' && $viewtype_BSDS=="BUILD" && $BSDSKEY==$BSDS_BSDSKEY && $amount_build==0)){ 
		$disable_save='no';
	}else{
		$disable_save='yes';
	}
	if ($disable_button2!='yes' && $disable_button3!='yes'){
		$output_form.="
		<form id='bsdsform_".$popup."'>
		<input type='hidden' name='bsdskey' value='".$BSDS_BSDSKEY."'>
		<input type='hidden' name='copydate' value='".$COPY_DATE."'>
		<input type='hidden' name='bsdsbobrefresh' value='".$BSDS_BOB_REFRESH."'>
		<input type='hidden' name='technosAsset' value='".$technosInAsset."'>
		<input type='hidden' name='donor' value='".$_POST['donor']."'>
		<input type='hidden' name='siteid' value='".$_POST['siteID']."'>
		<input type='hidden' name='candidate' value='".$_POST['candidate']."'>
		<input type='hidden' name='status' value='".$viewtype_BSDS."'>
		<input type='hidden' name='rafid' value='".$RAFID."'>
		<input type='hidden' name='disable_save' value='".$disable_save."'>
		</form>";

		$output_fundata.='<div class="btn-group dropup">
			  <button type="button" class="btn btn-sm" data-id="'.$popup.'" data-techno="ALL">Action</button>
			  <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown">
			    <span class="caret"></span>
			    <span class="sr-only">Toggle Dropdown</span>
			  </button>
			  <ul class="dropdown-menu" role="menu">';
	/******************************************************
	******** $technosInAsset contains ASSET technos *****
	*******************************************************/
		  	$output_fundata.='<li>
       		<ul class="technoNav">';
       		if(strpos($technosInAsset, 'G9')!==false){
       			$output_fundata.='<li class="bsdsdetails2 '.$statuscolor.'" data-id="'.$popup.'" data-techno="G9" data-bsdskey="'.$BSDS_BSDSKEY.'">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> G9</a></li>';
       		}
       		if(strpos($technosInAsset, 'G18')!==false){
       			$output_fundata.='<li class="bsdsdetails2 '.$statuscolor.'" data-id="'.$popup.'" data-techno="G18" data-bsdskey="'.$BSDS_BSDSKEY.'">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> G18</a></li>';
       		}
          	$output_fundata.='</ul></li>';  
    
          	$output_fundata.='<li>
       		<ul class="technoNav">';
       		if(strpos($technosInAsset, 'U9')!==false){
       			$output_fundata.='<li class="bsdsdetails2 '.$statuscolor.'" data-id="'.$popup.'" data-techno="U9" data-bsdskey="'.$BSDS_BSDSKEY.'">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> U9</a></li>';
       		}
       		if(strpos($technosInAsset, 'U21')!==false){
       			$output_fundata.='<li class="bsdsdetails2 '.$statuscolor.'" data-id="'.$popup.'" data-techno="U21" data-bsdskey="'.$BSDS_BSDSKEY.'">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> U21</a></li>';
       		}
          	$output_fundata.='</ul></li>'; 

       		$output_fundata.='<li>
       		<ul class="technoNav">';
       		if(strpos($technosInAsset, 'L8')!==false){
       			$output_fundata.='<li class="bsdsdetails2 '.$statuscolor.'" data-id="'.$popup.'" data-techno="L8" data-bsdskey="'.$BSDS_BSDSKEY.'">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> L8</a></li>';
       		}
       		if(strpos($technosInAsset, 'L18')!==false){
       			$output_fundata.='<li class="bsdsdetails2 '.$statuscolor.'" data-id="'.$popup.'" data-techno="L18" data-bsdskey="'.$BSDS_BSDSKEY.'">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> L18</a></li>';
       		}
       		$output_fundata.='</ul></li>';

       		$output_fundata.='<li>
       		<ul class="technoNav">
       			<li data-id="'.$popup.'"  data-techno="ALL" data-technos="'.$technosInAsset.'" data-bsdskey="'.$BSDS_BSDSKEY.'" class="bsdsdetails2">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> ALL</li>
       			<li data-id="'.$popup.'"  data-techno="PRINT" data-technos="'.$technosInAsset.'" data-bsdskey="'.$BSDS_BSDSKEY.'" class="bsdsdetails2">&nbsp; <span class="glyphicon glyphicon-print"></span> PRINT</li>
       		</ul></li>';
             /* 
            if(strpos($technosInAsset, 'L26')!==false){
				$output_fundata.='<li data-id="'.$popup.'" data-techno="L26" data-bsdskey="'.$BSDS_BSDSKEY.'" class="bsdsdetails2'.$statuscolor.'">
				<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> L26</a></li>';
            } 
             */
            $output_fundata.=' 	       
              <li class="divider"></li>
              <li data-id="'.$popup.'"  data-techno="BIPT" data-technos="'.$technosInAsset.'" data-bsdskey="'.$BSDS_BSDSKEY.'" class="bsdsdetails2">
              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-certificate"></span> BIPT </a></li>';
              
            if($STATUS_BSDS=="BSDS FUNDED"){
	               $output_fundata.='<li data-id="'.$popup.'"  data-techno="SN" data-technos="'.$technosInAsset.'" data-bsdskey="'.$BSDS_BSDSKEY.'" class="bsdsdetails2">
	              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-send"></span> SHIPPING NOTIFICATION </a></li>';
		  	}
		  	if (substr_count($guard_groups, 'Administrators')=="1"){ 	      
               $output_fundata.='<li class="divider"></li>
              <li data-id="'.$popup.'"  data-techno="DELETE" data-bsdskey="'.$BSDS_BSDSKEY.'" class="bsdsdetails2">
              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-trash"></span> DELETE</a></li>	       
              <li data-id="'.$popup.'" data-upgnr="'.$UPGNR.'" data-net1date="'.$BSDS_STATUS_DATE_NET1.'"  data-techno="REMOVEFUNDING" data-bsdskey="'.$BSDS_BSDSKEY.'" class="bsdsdetails2">
              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-trash"></span> REMOVE '.$BSDS_STATUS_DATE_NET1.'</a></li>';
			}
			if ($STATUS_BSDS=="SITE FUNDED" or $STATUS_BSDS=="REMOVE BSDS FUNDED"){
			 $output_fundata.=' 	       
              <li data-id="'.$popup.'" data-siteid="'.$_POST['siteID'].'" data-upgnr="'.$UPGNR.'" data-techno="BSDSFUNDING" data-todo="add" data-bsdskey="'.$BSDS_BSDSKEY.'" data-rafid="'.$RAFID.'" class="bsdsdetails2">
              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-arrow-down"></span> FUND BSDS</a></li>';
            }else if ($STATUS_BSDS=="BSDS FUNDED"){
			 $output_fundata.=' 	       
              <li data-id="'.$popup.'" data-siteid="'.$_POST['siteID'].'" data-upgnr="'.$UPGNR.'" data-techno="BSDSFUNDING" data-todo="remove" data-bsdskey="'.$BSDS_BSDSKEY.'" data-rafid="'.$RAFID.'" class="bsdsdetails2">
              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-arrow-up"></span> REMOVE BSDS FUNDING</a></li>';
            }
				$output_fundata.='</ul>
		  </div>';
	}
	$output_fundata.= "
	</td>
	</tr>";

	//echo "$BSDS_BOB_date----".$STATUS_BSDS."/$STATUS_AS_PER_NET1 ($BSDS_STATUS_DATE_NET1) $DATE_IN_N1==$BSDS_STATUS_DATE_NET1<br>";
	//echo $BSDSKEY."--".$STATUS_BSDS."-$viewtype_BSDS-".$wiewtype_NET1."/".$resN1['ACTION'][0]."$DATE_IN_N1==$BSDS_STATUS_DATE_NET1<br>";	

	$DATE_IN_N1_2= strtotime(substr($DATE_IN_N1,-4,4)."-".substr($DATE_IN_N1,3,2)."-".substr($DATE_IN_N1,0,2));
	$BSDS_STATUS_DATE_NET1_2= strtotime(substr($BSDS_STATUS_DATE_NET1,-4,4)."-".substr($BSDS_STATUS_DATE_NET1,3,2)."-".substr($BSDS_STATUS_DATE_NET1,0,2));
	if($STATUS_BSDS==$STATUS_AS_PER_NET1 && $DATE_IN_N1_2<=$BSDS_STATUS_DATE_NET1_2){
		$match_found='yes';
		$problem=$BSDS_STATUS_DATE_NET1;
		//We do this for the REFUNDING of BSDS so we are sure a newer date is saved in the database and we do not refund with same date or older date
	}

	//echo $amount_fund.")))))".$resN1['ACTION'][0]."==". $STATUS_BSDS."? //wiewtype_NET1 $wiewtype_NET1 // viewtype_BSDS $viewtype_BSDS  ---- $DATE_IN_N1==$BSDS_STATUS_DATE_NET1 // $DATE_IN_N1==$BSDS_STATUS_DATE_NET1<br>";
	if ($STATUS_BSDS!="BSDS AS BUILD"){
		if (($wiewtype_NET1=='FUND' && $viewtype_BSDS=="FUND" && ($resN1['ACTION'][0]=="NO CHANGE" or $resN1['ACTION'][0]=="ADD BSDS FUNDED" ) && $DATE_IN_N1==$BSDS_STATUS_DATE_NET1) or 
			($wiewtype_NET1=='POST' && $viewtype_BSDS=="FUND"  && ($resN1['ACTION'][0]=="NO CHANGE" or $resN1['ACTION'][0]=="ADD SITE FUNDED") && $DATE_IN_N1==$BSDS_STATUS_DATE_NET1) or
			($wiewtype_NET1=='POST' && $viewtype_BSDS=="POST"  && ($resN1['ACTION'][0]=="NO CHANGE" or $resN1['ACTION'][0]=="ADD SITE FUNDED") && $DATE_IN_N1==$BSDS_STATUS_DATE_NET1) or
			($STATUS_BSDS=="REMOVE BSDS FUNDED" && $resN1['ACTION'][0]=="REMOVE BSDS FUNDED" AND $STATUS_BSDS==$resN1['ACTION'][0])
			&& $amount_fund==0){ 
			$amount_fund_or_build++;
			$amount_fund=1;
			$output_funded.=$warning."<tr class='".$statuscolor."'>".$output_fundata;
		}else{
			$output_fund_history.="<tr class='".$statuscolor." history_data' style='display:none;'>".$output_fundata;
			$output_HistoryPerDate.="<tr class='".$statuscolor." historyPerdate_data' style='display:none;'>".$output_fundata;
		}
		
	}else if ($STATUS_BSDS=="BSDS AS BUILD" || $STATUS_BSDS=="REMOVE BSDS AS BUILD"){
		if ($wiewtype_NET1=='BUILD' && $viewtype_BSDS=="BUILD" && $BSDSKEY==$BSDS_BSDSKEY && $amount_build==0){ 
			$output_asbuild.=$warning."<tr class='".$statuscolor."'>".$output_fundata;
			$amount_fund_or_build++;
			$amount_build=1;
		}else{
			$output_asbuild_history.="<tr class='".$statuscolor." history_data' style='display:none;'>".$output_fundata;
			$output_HistoryPerDate.="<tr class='".$statuscolor." historyPerdate_data' style='display:none;'>".$output_fundata;
		}
	}
	$previousSTATUS_BSDS=$STATUS_BSDS;
}

?>
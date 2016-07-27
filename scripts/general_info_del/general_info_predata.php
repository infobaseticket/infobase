<?
$w=0;
foreach ($res1['BSDSKEY'] as $key=>$attrib_id){
	$BSDSKEY=$res1['BSDSKEY'][$key];
	$SITEKEY=$res1['SITEKEY'][$key];
	$SITEID=$res1['SITEID'][$key];
	$READY_FOR_APP=$res1['READY_FOR_APP'][$key];
	$TEAML_APPROVED=$res1['TEAML_APPROVED'][$key];
	$SITE_CONFLICT=$res1['SITE_CONFLICT'][$key];
	$TEAML_APPROVED2=$res1['TEAML_APPROVED2'][$key];
	$DESIGNER_CREATE=$res1['DESIGNER_CREATE'][$key];
	$CHANGE_DATE=$res1['CHANGE_DATE'][$key];
	$COMMENTS=$res1['COMMENTS'][$key];
	$ORIGIN_DATE=$res1['ORIGIN_DATE'][$key];
	$BSDS_TYPE=$res1['BSDS_TYPE'][$key];
	$DESIGNER_UPDATE=$res1['DESIGNER_UPDATE'][$key];
	$UPDATE_AFTER_COPY=$res1['UPDATE_AFTER_COPY'][$key];
	$UPDATE_BY_AFTER_COPY=$res1['UPDATE_BY_AFTER_COPY'][$key];

	$userdetails=getuserdata($DESIGNER_CREATE);
	$email=trim($userdetails['email']);
	$fullname=trim($userdetails['fullname']);
	$mobile=trim($userdetails['mobile']);

	if ($BSDSKEY==$BSDSKEY_FUNDEDBSDS){
		$status="USED FOR POST";
		$statuscolor="BSDS_preready_selected";
	}else{
		$status="PRE READY TO BUILD";
		$statuscolor="BSDS_preready";
	}

	if ($BSDSKEY!=$BSDSKEY_FUNDEDBSDS){
		include("general_info_popup.php");
		$output_form.="
			<form id='bsdsdetails_".$popup."'>
			<input type='hidden' name='bsdskey' value='".$res1['BSDSKEY'][$key]."'>
			<input type='hidden' name='bsdsbobrefresh' value='PRE'>
			<input type='hidden' name='technos' value='".$technos."'>
			<input type='hidden' name='technosAsset' value='".$_POST['technos']."'>
			<input type='hidden' name='bsdsdata' value='".json_encode($BSDS_funded[$UPGNR])."'>
			<input type='hidden' name='donor' value='".$_POST['donor']."'>
			<input type='hidden' name='siteid' value='".$_POST['siteID']."'>
			<input type='hidden' name='candidate' value='".$_POST['candidate']."'>
			<input type='hidden' name='status' value='PREHIST'>
			</form>";
		if ($_POST['bsdskey']==$res1['BSDSKEY'][$key]){
			$style='style="background-color:yellow;"';
		}else{
			$style='';
		}
	
		$output_pre_history .= "
		<tr id='bsdsrow_".$BSDSKEY."' class='prehistory_data' style='display:none;'>
			<td class='".$statuscolor." ".$style."'>
			<a href='#' data-toggle='modal' data-target='#".$BSDSKEY.$popup."".$res1['SIT_UDK'][$i]."'>".$BSDSKEY."</a></td>
			<td class='".$statuscolor."'>".$BSDS_TYPE."</td>
		    <td>".$CHANGE_DATE."</td>
			<td>".$TEAML_APPROVED."</td>
			<td style='width:100px;'>";
			if ((substr_count($guard_groups, 'BSDS_view')=="1" && $TEAML_APPROVED=="Accepted" && ($READY_FOR_APP=="yes" || $READY_FOR_APP=="Yes")) || substr_count($guard_groups, 'Radioplanners')=="1"){
				
				$output_pre_history.='<div class="btn-group dropup"> 
				  <button class="btn btn-xs" data-id="'.$popup.'" data-techno="ALL">Action</button>
				  <button class="btn btn-xs dropdown-toggle" data-toggle="dropdown">
				    <span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu pull-right">';
				  	if(strpos($_POST['technos'], 'G9')!==false){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="G9" class="bsdsdetails">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM900</a></li>';
	                }  
	                if(strpos($_POST['technos'], 'G18')!==false){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="G18" class="bsdsdetails">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM1800</a></li>';
	                } 
	                if(strpos($_POST['technos'], 'U9')!==false){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="U9" class="bsdsdetails">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS900</a></li>';
	                }
	                if(strpos($_POST['technos'], 'U21')!==false){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="U21" class="bsdsdetails">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS2100</a></li>';
	                }
	                 if(strpos($_POST['technos'], 'L8')!==false){ 
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="L8" class="bsdsdetails">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE800</a></li>';
	                }
	                if(strpos($_POST['technos'], 'L18')!==false){ 
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="L18" class="bsdsdetails">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE1800</a></li>';
	                }
	                if(strpos($_POST['technos'], 'L26')!==false){  
	                  $output_pre_history.='<li  data-id="'.$popup.'" data-techno="L26" class="bsdsdetails">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE2600</a></li>';	       
	                }  
	                  $output_pre_history.='<li class="divider"></li>
	                  <li data-id="'.$popup.'"  data-techno="ALL" data-technos="'.$_POST['technos'].'" class="bsdsdetails">
		              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> ALL</a></li>
	                  <li data-techno="PRINT" class="bsdsdetails" data-technos="'.$_POST['technos'].'" data-id="'.$popup.'">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-print"></span> PRINT</li>';
	                if ((substr_count($guard_groups, 'Administrators')=="1" || substr_count($guard_groups, 'Radioplanners')=="1") && $BSDS_TYPE!="TRX upgrade" && $BSDS_TYPE!="Antenna change"){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="CHANGEID" data-idnr="'.$IDNR.'" class="bsdsdetails">
	                  &nbsp; <span class="glyphicon glyphicon-share-alt"></span> SELECT TO FUND</li>';
				 	}
				 	if (substr_count($guard_groups, 'Administrators')=="1" || substr_count($guard_groups, 'Radioplanners')=="1"){
	                  $output_pre_history.='<li data-id="'.$popup.'" data-techno="DELETEBSDS" data-bsdsid="'.$BSDSKEY.'" class="bsdsdetails">
	                  &nbsp; <span class="glyphicon glyphicon-trash"></span> DELETE</li>';
				 	}
				 	$output_pre_history.='
				  </ul>
				</div>';
 					
			}
		$output_pre_history .="</td></tr>";

		$statuscolor="";
	}else{ //THE PURPLE ONE
		include("general_info_popup.php");
		$READY_FOR_APP_FUND_CHECK=$READY_FOR_APP;
		$TEAML_APPROVED_FUND_CHECK=$TEAML_APPROVED;
	
		if ($_POST['bsdskey']==$res1['BSDSKEY'][$key]){
			$style='style="background-color:yellow;"';
		}else{
			$style='';
		}

		$output_pre ="
		<tr class='BSDS_preready'>
			<td ".$style."><a href='#' data-toggle='modal' data-target='#".$BSDSKEY_FUNDEDBSDS.$popup."".$res1['SIT_UDK'][$i]."'>".$BSDSKEY_FUNDEDBSDS."</a></td>
			<td>".$BSDS_TYPE."</td>
			<td>".$CHANGE_DATE."</td>";

			// TEAML_APPROVED
			if (((substr_count($guard_groups, 'Radioplanners')=="1"  || substr_count($guard_groups, 'Partner')=="1") && $TEAML_APPROVED!="Accepted") || substr_count($guard_groups, 'Administrators')=="1"){
			$output_pre.= "
			<td><div class='teamleader_select' data-siteid='".$SITEID."' data-type='select' data-pk='".$BSDSKEY_FUNDEDBSDS."'>".$TEAML_APPROVED."</div></td>";
			}else{
				if ($TEAML_APPROVED=="Pending"){
					$output_pre.= "<td><font color='orange'><b>".$TEAML_APPROVED."</b></font></td>";
				}else if ($TEAML_APPROVED=="Declined"){
					$output_pre.= "<td><font color='red'><b>".$TEAML_APPROVED."</b></font></td>";
				}else{
					$output_pre.= "<td><font color='green'>".$TEAML_APPROVED."</font></td>";
				}
			}
			$output_form.="
			<form id='bsdsdetails_".$popup."'>
			<input type='hidden' name='bsdskey' value='".$res1['BSDSKEY'][$key]."'>
			<input type='hidden' name='bsdsbobrefresh' value='PRE'>
			<input type='hidden' name='technos' value='".$technos."'>
			<input type='hidden' name='technosAsset' value='".$_POST['technos']."'>
			<input type='hidden' name='bsdsdata' value='".json_encode($BSDS_funded[$UPGNR])."'>
			<input type='hidden' name='donor' value='".$_POST['donor']."'>
			<input type='hidden' name='siteid' value='".$_POST['siteID']."'>
			<input type='hidden' name='candidate' value='".$_POST['candidate']."'>
			<input type='hidden' name='status' value='PRE'>
			</form>";

			$output_pre.="<td style='width:100px;'>";			
			if ((substr_count($guard_groups, 'BSDS_view')=="1" && $TEAML_APPROVED=="Accepted" && ($READY_FOR_APP=="yes" || $READY_FOR_APP=="Yes")) || substr_count($guard_groups, 'Radioplanners')=="1"){
				$output_pre.='
				<div class="btn-group dropup">
				  <button class="btn btn-default btn-sm" data-techno="ALL" data-id="'.$popup.'">Action</button>
				  <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
				    <span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu pull-right">';
				  	if(strpos($_POST['technos'], 'G9')!==false){
	                  $output_pre.='<li data-id="'.$popup.'" data-techno="G9"  class="bsdsdetails BSDS_preready">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM900</a></li>';
	                }  
	                if(strpos($_POST['technos'], 'G18')!==false){
	                  $output_pre.='<li  data-id="'.$popup.'" data-techno="G18"  class="bsdsdetails BSDS_preready">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM1800</a></li>';
	                } 
	                if(strpos($_POST['technos'], 'U9')!==false){
	                  $output_pre.='<li data-id="'.$popup.'" data-techno="U9"  class="bsdsdetails BSDS_preready">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS900</a></li>';
	                }
	                if(strpos($_POST['technos'], 'U21')!==false){
	                  $output_pre.='<li data-id="'.$popup.'" data-techno="U21"  class="bsdsdetails BSDS_preready">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS2100</a></li>';
	                }
	                if(strpos($_POST['technos'], 'L8')!==false){ 
	                  $output_pre.='<li data-id="'.$popup.'" data-techno="L8"  class="bsdsdetails BSDS_preready">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE800</a></li>';
	                }
	                if(strpos($_POST['technos'], 'L18')!==false){ 
	                  $output_pre.='<li data-id="'.$popup.'" data-techno="L18"  class="bsdsdetails BSDS_preready">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE1800</a></li>';
	                }
	                if(strpos($_POST['technos'], 'L26')!==false){  
	                  $output_pre.='<li data-id="'.$popup.'" data-techno="L26"  class="bsdsdetails BSDS_preready">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE2600</a></li>';	       
	                } 
	                  $output_pre.='<li class="divider"></li>
	                  <li data-id="'.$popup.'"  data-techno="ALL" data-technos="'.$_POST['technos'].'" class="bsdsdetails">
		              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> ALL</li>
	                  <li  data-id="'.$popup.'" data-techno="PRINT"  data-technos="'.$_POST['technos'].'" class="bsdsdetails ">
	                  <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-print"></span> PRINT</li>
				  </ul>
				</div>';
			}
		$output_pre.="</td></tr>";
	}
}
?>
<?php

$amountBSDS=0;
//echo "---".count($res1['BSDSKEY']);
foreach ($res1['BSDSKEY'] as $key=>$attrib_id){

	$BSDSKEY=$res1['BSDSKEY'][$key];
	$DEL_STATUS=$res1['DEL_STATUS'][$key];
	$CANDIDATE=$res1['CANDIDATE'][$key];
	$FROZEN=$res1['FROZEN'][$key];
	$RECTIFIER=$res1['RECTIFIER'][$key];
	$PARTNER_DESIGN=$res1['PARTNER_DESIGN'][$key];
	$POWERSUP=$res1['POWERSUP'][$key];
	$CABTYPE=$res1['CABTYPE'][$key];
	$UNIRAN=$res1['UNIRAN'][$key];

	$DESIGNER_CREATE=$res1['BY_CREATED'][$key];
	$RADIO_FUND=$res1['RADIO_FUND'][$key];
	$userdetails=getuserdata($DESIGNER_CREATE);
	$email=$userdetails['email'];
	$fullname=$userdetails['fullname'];
	$mobile=$userdetails['mobile'];

	$CHANGE_DATE=$res1['DATE_UPDATE'][$key];
	$CREATED_DATE=$res1['CREATED_DATE'][$key];
	$BSDS_TYPE=$res1['BSDS_TYPE'][$key];
	$DESIGNER_UPDATE=$res1['BY_UPDATE'][$key];
	$RAFID=$res1['RAFID'][$key];
	$RAFTYPE=$res1['TYPE'][$key];

	$userdetails=getuserdata($DESIGNER_UPDATE);
	$emailUP=trim($userdetails['email']);
	$fullnameUP=trim($userdetails['fullname']);
	$mobileUP=trim($userdetails['mobile']);

	$amountBSDS++;

	$disable_button3='';
	$error='';
	//If the RAFID is NULL, this means that the data will be displayed in the history

	$CREATED_DATE2=str_replace(' ', '',str_replace('/', '',str_replace(':', '', $CREATED_DATE)));
	$key_zband=$_POST['bsdskey'].$_POST['candidate'].$upgnr.$CREATED_DATE2;

	if ($DEL_STATUS!='yes' && $RAFID!="" && $has_first!='yes'){
		$out="output_latestwithRAFID";
		$histclass="";
		$histstyle="";
		$has_first='yes';
		$RAFTYPE2=$RAFTYPE;
	}else{
		if ($RAFID==""){
			$RAFTYPE2="<span class='bsdsdetails2 pointer btn btn-warning' data-bsdskey='".$BSDSKEY."' data-techno='ATTACHRAF' data-id='".$key_zband."'>NO ATTACHED RAF</span>";
			$RAFID="#";
			$out="output_withoutRAFID";
		}else{
			$out="output_withRAFID";
			$RAFTYPE2=$RAFTYPE;
		}
		
		$histclass="history_data historyPerdate_data";
		$histstyle="style='display:none;'";
	}

	if ($_POST['bsdskey']==$res1['BSDSKEY'][$key]){
		$style='style="background-color:yellow;"';
	}else{
		$style='';
	}
	if ($_POST['upgnr']==''){
		$upgnr='';
	}else{
		$upgnr=$_POST['upgnr'];
	}
	
	include("general_info_popup.php");
	$output_form.="
		<form id='bsdsform_".$key_zband."'>
		<input type='hidden' name='datakey' value='".$key_zband."'>
		<input type='hidden' name='formid' value='".$popup."'>
		<input type='hidden' name='bsdskey' value='".$BSDSKEY."'>
		<input type='hidden' name='nbup' value='".$_POST['nbup']."'>
		<input type='hidden' name='createddate' value='".$CREATED_DATE."'>
		<input type='hidden' name='donor' value='".$_POST['donor']."'>
		<input type='hidden' name='siteid' value='".$_POST['siteid']."'>
		<input type='hidden' name='candidate' value='".$_POST['candidate']."'>
		<input type='hidden' name='upgnr' value='".$_POST['upgnr']."'>
		<input type='hidden' name='partner_design' value='".$PARTNER_DESIGN."'>
		<input type='hidden' name='frozen' value='".$FROZEN."'>
		<input type='hidden' name='cabtype' value='".$CABTYPE."'>
		<input type='hidden' name='uniran' value='".$UNIRAN."'>
		<input type='hidden' name='rectifier' value='".$RECTIFIER."'>
		<input type='hidden' name='powersup' value='".$POWERSUP."'>
		<input type='hidden' name='technos' value='".$techno."'>
		<input type='hidden' name='rafid' value='".$RAFID."'>
		<input type='hidden' name='band' value='".$vand."'>
		<input type='hidden' name='lognodeG9' value='".$lognode['G9']."'>
		<input type='hidden' name='lognodeG18' value='".$lognode['G18']."'>
		<input type='hidden' name='lognodeU9' value='".$lognode['U9']."'>
		<input type='hidden' name='lognodeU21' value='".$lognode['U21']."'>
		<input type='hidden' name='lognodeL8' value='".$lognode['L8']."'>
		<input type='hidden' name='lognodeL18' value='".$lognode['L18']."'>
		<input type='hidden' name='lognodeL26' value='".$lognode['L26']."'>
		<input type='hidden' name='xycoord' value='".$coor['longitude']."-".$coor['latitude']."'>
		<input type='hidden' name='address' value='".$address."'>
		<input type='hidden' name='technosCon' value='".$RADIO_FUND."'>
		<input type='hidden' name='raftype' value='".$RAFTYPE."'>
		</form>";

	if ($DEL_STATUS=='yes'){
		$class="danger";
	}else if ($FROZEN=='1'){
		$class="BSDS_funded";
	}else if ($FROZEN=='2'){
		$class="BSDS_asbuild";
	}else{
		$class="BSDS_preready";
	}

	$$out .= "
	<tr id='bsdsrow_".$BSDSKEY."' class='".$histclass."' ".$histstyle.">
		<td ".$style."'>".$BSDSKEY."</td>
		<td class='".$class." colorchange pointer".$key_zband."'>".$RAFID.": ".$RAFTYPE2."</td>
		<td class='".$class." colorchange".$key_zband."'>".$RADIO_FUND."</td>
		<td><a href='#' title='BY ".$fullname."' class='tippy'>".$CREATED_DATE."</a></td>
		<td><a href='#' title='BY ".$fullnameUP."' class='tippy'>".$CHANGE_DATE."</a></td>
		<td>".$BSDS_TYPE."</td>
		
		<td style='width:100px;'>";

		if ((substr_count($guard_groups, 'BSDS_view')=="1" || substr_count($guard_groups, 'Radioplanners')=="1") && $RAFID!="#"){
			/*
			<li data-id="'.$key_zband.'"  data-techno="PRINTBSDS2" data-technos="'.$techno.'" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	       		<a href="#" tabindex="-1">  <span class="glyphicon glyphicon-print"></span> PRINT </a>
	       		</li>
	       		*/
			$$out.='<div class="btn-group dropup"> 
			  <button class="btn btn-xs bsdsdetails2 '.$class.'" data-id="'.$key_zband.'" data-techno="ALLIN" data-bsdskey="'.$BSDSKEY.'"><span class="glyphicon glyphicon-eye-open"></span> VIEW</button>
			  <button class="btn btn-xs dropdown-toggle" data-toggle="dropdown">
			    <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu" role="menu" style="min-width:180px;">

	       		<li data-id="'.$key_zband.'"  data-techno="ALLIN" data-technos="'.$techno.'" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
	       		<a href="#" tabindex="-1">  <span class="glyphicon glyphicon-eye-open"></span> VIEW </a>
	       		</li>';	              
	            if($FROZEN=="1" && $amountBSDS==1 && $DEL_STATUS!='yes'){
		              $$out.='<li data-id="'.$key_zband.'"  data-techno="SN" data-technos="'.$techno.'" data-bsdskey="'.$BSDSKEY.'" class="bsdsdetails2">
		              <a href="#" tabindex="-1"> <span class="glyphicon glyphicon-send"></span> SHIPPING NOTIFICATION </a></li>';
			  	}
			  	if ($FROZEN!=1 && $RAFID!="#" && $DEL_STATUS!='yes' && $RAFID!="" && $PARTNER_DESIGN!='BASE RF OK'){
				$$out.='<li data-id="'.$key_zband.'" data-key="'.$BSDSKEY.$CREATED_DATE2.$FROZEN.'" data-key2="'.$_POST['candidate'].$FROZEN.'" data-rafid="'.$RAFID.'" data-techno="FREEZEBSDS" class="bsdsdetails2">
	            <a href="#" tabindex="-1"> <span class="glyphicon glyphicon-arrow-down"></span> FREEZE</a></li>';
	        	}
	        	if ($DEL_STATUS!='yes'){
	        	$$out.='<li data-id="'.$key_zband.'" data-key="'.$BSDSKEY.$CREATED_DATE2.$FROZEN.'" data-techno="DELETEBSDS" class="bsdsdetails2">
	            <a href="#" tabindex="-1"> <span class="glyphicon glyphicon-trash"></span> DELETE</a></li>';
	        	}
	        	$$out.='
			  </ul>
			</div>';
					
		}else{
			$$out.='RAFID!';
		}
	$$out .="</td></tr>";			
	
}
?>
<?php
$output_genralinfo="";

if ($status=="SITE FUNDED"){
	$NET1_date=$BSDS_funded[$key]['SITEFUNDED'];
	$viewtype='POST';
	$NET1_date=$NET1_DATE_SITEFUNDED;
}else if ($status=="BSDS FUNDED"){
	$NET1_date=$BSDS_funded[$key]['BSDSFUNDED'];
	$viewtype='FUND';
}else if ($status=="BSDS AS BUILD"){
	$viewtype='BUILD';
	$NET1_date=$BSDS_funded[$key]['ASBUILD'];
}

$CON=$BSDS_funded[$UPGNR]['CON'];
$SAC=$BSDS_funded[$UPGNR]['SAC'];

if($SAC=="BENCHMARK" && $CON=="ALU"){
	$partner="ALU";
}else if($SAC=="ALU" && $CON=="ALU"){
	$partner="ALU";
}else if($SAC=="ALU" && $CON=="BENCHMARK"){
	$partner="BENCHMARK";
}else if($SAC="BENCHMARK" && $CON=="BENCHMARK"){
	$partner="BENCHMARK";
}else if($SAC="KPNGB" && $CON=="ALU"){
	$partner="ALU";
}

$data=accept_teaml_fundtech($_POST['candidate'],$status,$BSDSKEY_FUNDEDBSDS,$NET1_date,$BSDS_BOB_date,$DB_BOB_refresh,$copied,$technos,$UPGNR,$_POST['donor'],$lognode,$partner);
//echo "<pre>".print_r($data,true)."</pre>";

$db_data=$data[0];
$message.=$data[1];
$message_type=$data[2];


if ($message){
?>
 <div class="alert alert-danger"><?=$message?></div>
<?
}
if ($message_type=="error"){
	die;
}

$BSDS_BSDSKEY=$db_data['BSDSKEY'][0];
$BSDS_BOB_date=$db_data['BSDS_BOB_REFRESH'][0];
$BSDS_copy_date=$db_data['COPY_DATE'][0];
$STATUS_DATE_NET1=$db_data['NET1_DATE'][0];
$TEAML_FUNDED=$db_data['TEAM_STATUS'][0];
$QA_STATUS=$db_data['QA_STATUS'][0];
$STATUS_BSDS=$db_data['STATUS'][0];
$UPGNR=$db_data['UPGNR'][0];
$PARTNER_VIEW=$db_data['PARTNER_VIEW'][0];

if ($STATUS_BSDS=="BSDS AS BUILD => DEFUNDED TO OLD DATE" || $STATUS_BSDS=="BSDS FUNDED => DEFUNDED TO OLD DATE"){
	$statuscolor="refunded";
}

$COMMENTS_QA_DECLINED=$db_data['COMMENTS'][0];


if ((substr_count($guard_groups, 'BSDS_view')=="1") || substr_count($guard_groups, 'Radioplanners')=="1" || substr_count($guard_groups, 'BASE_MP')=="1")
{
	include("general_info_popup.php");

	$bsds_info="bsds_info_".$popup."_".$_POST['tabid'];
	$bsds_info_toggle="bsds_info_toggle_".$popup."_".$_POST['tabid'];
	$bsds_info_B="#bsds_info_".$popup."_".$_POST['tabid'];
	$bsds_info_toggle_B="#bsds_info_toggle_".$popup."_".$_POST['tabid'];
	$TEAML_FUNDED_SELECT="TEAML_FUNDED_".$popup."_".$_POST['tabid'];
	$TEAML_FUNDED_FORM="TEAML_FORM_".$popup."_".$_POST['tabid'];

	if ($status!="BSDS AS BUILD"){
		if ($TEAML_APPROVED_FUND_CHECK=="Pending" || $TEAML_APPROVED_FUND_CHECK=="Declined" || ($TEAML_APPROVED_FUND_CHECK=="Pending" && $status=="BSDS FUNDED")){
				if ($STATUS_DATE_NET1!=""){
					$action_status="Rollout partner";
					if (substr_count($guard_groups, 'Radioplanners')=="1" || substr_count($guard_groups, 'Administrators')=="1"){
						$hideOnClick="true";
					}else{
						$hideOnClick="false";
					}
					$warning="In Net1, the date for status ".$status." (U305/A305) has been added before the teamleader has accepted.<br>";
					$warning.="There are 2 options:<br><ol><li>BSDS is correct, teamleader accept the BSDS.</li>";
					$warning.="<li>BSDS is not correct, BSDS has to be defunded!</li></ol>";
					?>
					<script language="JavaScript">
						$('.top-right').notify({
							message: { html: "<?=$warning?>"},
							type: 'warning',
							fadeOut: { enabled: false }
						}).show();
					</script>
					<?
					}else{
						$action_status="Partner";
					}
		}else if ($TEAML_APPROVED=="Accepted"){
			if ($TEAML_FUNDED=="Declined" || $TEAML_FUNDED=="Pending"){
					$action_status="Partner";
			}else{
				$action_status="Rollout partner";
			}
		}
	}

	if ($BSDSKEY_FUNDEDBSDS!=""){
		$output_genralinfo.= "
		<tr class='".$statuscolor."'>
			<td>
			<a href='#' data-toggle='modal' data-target='#".$BSDSKEY_FUNDEDBSDS.$popup."'>".$BSDSKEY_FUNDEDBSDS." [".$BSDS_BOB_date."]</a></td>";
			if ($COMBINED!=''){
				$output_genralinfo.= "<td class='".$statuscolor."'>".$COMBINED."</td>";
			}else{
				$output_genralinfo.= "<td class='".$statuscolor."'>".$UPGNR."</td>";
			}
			
			$output_genralinfo.= "<td><b>".htmlentities($technos)."</b></td>";

			if ($QA_STATUS==""){
				$QA_STATUS="Pending";
			}

			if ($status!="BSDS AS BUILD"){
				$output_genralinfo.= "<td>$action_status</td>";
			}

			
			$output_genralinfo.= "<td>".$STATUS_DATE_NET1."</td>";
			
			$output_genralinfo.= "<td>".$partner."</td>";
			$output_genralinfo.= "<td>".$status."</td>";
			$output_genralinfo.='<td style="width:100px;">';
			if ($message_type!="error2"){
				if (($QA_STATUS=="Accepted" || substr_count($guard_groups, 'BASE_MP')=="1"
				|| substr_count($guard_groups, 'Radioplanners')=="1"
				|| substr_count($guard_groups, 'Administrators')=="1"
				|| substr_count($guard_groups, 'BSDS_view')=="1" )
				&& $status!="BSDS DEFUNDED")
				{

				$output_form.="
				<form id='bsdsdetails_".$popup."'>
				<input type='hidden' name='bsdskey' value='".$BSDSKEY_FUNDEDBSDS."'>
				<input type='hidden' name='bsdsbobrefresh' value='".$BSDS_BOB_date."'>
				<input type='hidden' name='technos' value='".$technos."'>
				<input type='hidden' name='technosAsset' value='".$_POST['technos']."'>
				<input type='hidden' name='bsdsdata' value='".json_encode($BSDS_funded[$UPGNR])."'>
				<input type='hidden' name='donor' value='".$_POST['donor']."'>
				<input type='hidden' name='siteid' value='".$_POST['siteID']."'>
				<input type='hidden' name='candidate' value='".$_POST['candidate']."'>
				<input type='hidden' name='status' value='".$viewtype."'>
				</form>";

					if ((substr_count($guard_groups, 'Radioplanners')=="1" or  substr_count($guard_groups, 'BSDS_view')=="1") && $message_type!="error"){
						$output_genralinfo.='<div class="btn-group dropup">
						  <button type="button" class="btn btn-sm" data-id="'.$popup.'" data-techno="ALL">Action</button>
						  <button type="button" class="btn btn-sm dropdown-toggle" data-toggle="dropdown">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu" role="menu">';

					  	if(strpos($_POST['technos'], 'G9')!==false){
			  				if(strpos($technos, 'G9')!==false){
			  					$pre_GSM900=$statuscolor;
				  			}else{
				  				$pre_GSM900='BSDS_preready';
				  			}
							$output_genralinfo.='<li data-id="'.$popup.'" data-techno="G9" class="bsdsdetails '.$pre_GSM900.'">
							<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM900</a></li>';
		                }  
		        		if(strpos($_POST['technos'], 'G18')!==false){
		        			if(strpos($technos, 'G18')!==false){
			  					$pre_GSM1800=$statuscolor;
				  			}else{
				  				$pre_GSM1800='BSDS_preready';
				  			}
							$output_genralinfo.='<li data-id="'.$popup.'" data-techno="G18" class="bsdsdetails '.$pre_GSM1800.'">
							<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM1800</a></li>';
		                } 
			            if(strpos($_POST['technos'], 'U9')!==false){
		                 	if(strpos($technos, 'U9')!==false){
			  					$pre_UMTS900=$statuscolor;
				  			}else{
				  				$pre_UMTS900='BSDS_preready';
				  			}
							$output_genralinfo.='<li data-id="'.$popup.'" data-techno="U9" class="bsdsdetails '.$pre_UMTS900.'">
							<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS900</a></li>';
		                } 
			            if(strpos($_POST['technos'], 'U21')!==false){
		                  	if(strpos($technos, 'U21')!==false){
			  					$pre_UMTS2100=$statuscolor;
				  			}else{
				  				$pre_UMTS2100='BSDS_preready';
				  			}
							$output_genralinfo.='<li data-id="'.$popup.'" data-techno="U21" class="bsdsdetails '.$pre_UMTS2100.'">
							<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS2100</a></li>';
		                }  
		                if(strpos($_POST['technos'], 'L8')!==false){
		                 	if(strpos($technos, 'L8')!==false){
			  					$pre_LTE800=$statuscolor;
				  			}else{
				  				$pre_LTE800='BSDS_preready';
				  			}
							$output_genralinfo.='<li data-id="'.$popup.'" data-techno="L8" class="bsdsdetails '.$pre_LTE800.'">
							<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE800</a></li>';
		                }  
			            if(strpos($_POST['technos'], 'L18')!==false){
		                 	if(strpos($technos, 'L18')!==false){
			  					$pre_LTE1800=$statuscolor;
				  			}else{
				  				$pre_LTE1800='BSDS_preready';
				  			}
							$output_genralinfo.='<li data-id="'.$popup.'" data-techno="L18" class="bsdsdetails '.$pre_LTE1800.'">
							<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE1800</a></li>';
		                }   
			            if(strpos($_POST['technos'], 'L26')!==false){
			            	if(strpos($technos, 'L26')!==false){
			  					$pre_LTE2600=$statuscolor;
				  			}else{
				  				$pre_LTE2600='BSDS_preready';
				  			}
							$output_genralinfo.='<li data-id="'.$popup.'" data-techno="L26" class="bsdsdetails'.$pre_LTE2600.'">
							<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE2600</a></li>';
		                } 
			             
			            $output_genralinfo.=' 	       
			              <li class="divider"></li>
			              <li data-id="'.$popup.'"  data-techno="ALL" data-technos="'.$technos.'" class="bsdsdetails">
			              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> ALL</a></li>
			              <li data-id="'.$popup.'"  data-techno="PRINT" data-technos="'.$_POST['technos'].'" class="bsdsdetails">
			              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-print"></span> PRINT </a></li>
			              <li data-id="'.$popup.'"  data-techno="BIPT" data-technos="'.$_POST['technos'].'" class="bsdsdetails">
			              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-certificate"></span> BIPT </a></li>';
					  	if (substr_count($guard_groups, 'Administrators')=="1"){
					  	 $output_genralinfo.=' 	      
			              <li class="divider"></li>
			              <li data-id="'.$popup.'"  data-techno="DELETE" class="bsdsdetails">
			              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-trash"></span> DELETE</a></li>';

			              $output_genralinfo.=' 	       
			              <li data-id="'.$popup.'" data-upgnr="'.$UPGNR.'" data-net1date="'.$STATUS_DATE_NET1.'"  data-techno="REMOVEFUNDING" class="bsdsdetails">
			              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-trash"></span> REMOVE '.$STATUS_DATE_NET1.'</a></li>';
						}
	 					$output_genralinfo.='</ul>
					  </div>';
					}
				}
			}else{
				$output_genralinfo.= "<span class='label label-important'>ERROR</span>";
			}
		$output_genralinfo.= "
		</td>
		</tr>";
	}

}else{
	if ($status=="SITE FUNDED"){
		$output_genralinfo.= "<tr><td class='".$statuscolor."' colspan='7'>The BSDS has not been funded by BASE!</td></tr>";
	}else{
		$output_genralinfo.= "<tr><td class='".$statuscolor."' colspan='7'>There is a FUNDED BSDS for $statustechnologie but it has not been accepted by teamleader or the BSDS has not been accepted by rollout partner!</td></tr>";
	}
	$warning="There is a FUNDED BSDS for ".$statustechnologie." but it has not been accepted by teamleader or the BSDS has not been accepted by rollout partner!";
	?>
	<script language="JavaScript">
	createAlert(<?=$warning?>,'WARNING!',true,'red');
	</script>
	<?
}

if ($status!="BSDS AS BUILD"){
	$output_funded.=$output_genralinfo;
}else if ($status=="BSDS AS BUILD" || $status=="BSDS AS BUILD => DEFUNDED TO OLD DATE"){
	$output_asbuild.=$output_genralinfo;
}
?>
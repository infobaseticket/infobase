<?

$output="";

$query = "Select * FROM BSDS_FUNDED_TEAML_ACC2 WHERE SITEID LIKE '%".$_POST['candidate']."%'";
if ($BSDS_BOB_date){
	$query .= "AND BSDS_BOB_REFRESH!=TO_DATE('".$BSDS_BOB_date."','dd/mm/yyyy HH24:MI:SS') ";
}
$query .= " ORDER BY UPGNR,BSDS_BOB_REFRESH DESC";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);

	if (count($res1['BSDSKEY'])!=0){

		$i=0;
		foreach ($res1['BSDSKEY'] as $key=>$attrib_id){
			//echo $res1['BSDSKEY'][$key]."-".$res1['STATUS'][$key]."<br>";
			$techno_changed=strstr($res1['STATUS'][$key], 'TECHNOLOGY CHANGED FROM');
			$status="";
			$BSDStype="";
			if($techno_changed){
				$status="<span title='".substr($res1['STATUS'][$key],24)."' class='tip'>TECHNO CHANGE!</span>";
			}else{
				$status=$res1['STATUS'][$key];
			}
			$style="style='display:none;'";

			if ($res1['STATUS'][$key]=="BSDS AS BUILD"){
				$statuscolor="BSDS_asbuild";
				if (($total_debarredNEW==1 || $total_debarredUPG==1)
					&& $latestBSDS_amount==1 && $latestBSDS_status=='BSDS AS BUILD'
					&& $BOB_REFRESH_DATE_BSDSFUNDED==$res1['BSDS_BOB_REFRESH'][$key] 
					&& $latestBSDSKEY==$res1['BSDSKEY'][$key]){
					$history="";
					$style="";
				}else{
					$history="build_hist_data";
				}
				$BSDStype="BUILDHIST";
			}else if ($res1['STATUS'][$key]=="SITE FUNDED"){
				$statuscolor="SITE_funded";
				$history="fund_hist_data";
				$BSDStype="POSTHIST";
			}else if ($res1['STATUS'][$key]=="BSDS FUNDED"){
				$statuscolor="BSDS_funded";
				$history="fund_hist_data";
				$BSDStype="FUNDHIST";
			}else if ($res1['STATUS'][$key]=="SITE FUNDED => DEFUNDED" || $res1['STATUS'][$key]=="BSDS FUNDED => DEFUNDED"
				|| $res1['STATUS'][$key]=="BSDS AS BUILD => DEFUNDED" || $res1['STATUS'][$key]=="BSDS DEFUNDED"
				|| $res1['STATUS'][$key]=="BSDS FUNDED => DEFUNDED TO OLD DATE"
				|| $res1['STATUS'][$key]=="BSDS AS BUILD => DEFUNDED TO OLD DATE"
			){
				$statuscolor="refunded";
				$history="fund_hist_data";
			}else if($techno_changed){
				$history="fund_hist_data";
				$statuscolor="refunded";
			}

			include("general_info_popup.php");

			if($res1['UPGNR'][$key]!=""){
				$UPGNR=$res1['UPGNR'][$key];
			}else{
				$UPGNR="NA";
			}
			$output_form.="
			<form id='bsdsdetails_".$popup."'>
			<input type='hidden' name='bsdskey' value='".$BSDSKEY_FUNDEDBSDS."'>
			<input type='hidden' name='bsdsbobrefresh' value='".$res1['BSDS_BOB_REFRESH'][$key]."'>
			<input type='hidden' name='technos' value='".$res1['TECHNOLOGY'][$key]."'>
			<input type='hidden' name='technosAsset' value='".$_POST['technos']."'>
			<input type='hidden' name='bsdsdata' value='".json_encode($BSDS_funded[$UPGNR])."'>
			<input type='hidden' name='donor' value='".$_POST['donor']."'>
			<input type='hidden' name='siteid' value='".$_POST['siteID']."'>
			<input type='hidden' name='candidate' value='".$_POST['candidate']."'>
			<input type='hidden' name='status' value='".$BSDStype."'>
			</form>";

			$output= "<tr class='".$history." ".$statuscolor."' ".$style.">
			<td><div id='".$BSDSKEY.$popup."' class='tip bsdskey'><b>[".$res1['BSDSKEY'][$key]."] ".$res1['BSDS_BOB_REFRESH'][$key]."</b></div></td>
			<td>".$UPGNR."</td>
			<td>".$res1['TECHNOLOGY'][$key]."</td>
			<td>".$res1['NET1_DATE'][$key]."</td>
			<td>".$res1['COPY_DATE'][$key]."</td>
			<td>".$status."</td>
			<td style='width:100px;'>";
			if ($res1['STATUS'][$key]!="BSDS DEFUNDED" && $res1['STATUS'][$key]!="BSDS FUNDED => DEFUNDED TO OLD DATE" &&
			 $res1['STATUS'][$key]!="BSDS AS BUILD => DEFUNDED TO OLD DATE" && $techno_changed=== false && $message_type!="error"){
				$output.='<div class="btn-group dropup">
				<button class="btn btn-default btn-sm" data-id="'.$popup.'" data-techno="ALL">Action</button>
				<button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
				<span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right">';
		  	if(strpos($_POST['technos'], 'G9')!==false){
  				if(strpos($res1['TECHNOLOGY'][$key], 'G9')!==false){
  					$pre_GSM900=$statuscolor;
	  			}else{
	  				$pre_GSM900='BSDS_preready';
	  			}

				$output.='<li data-id="'.$popup.'" 	data-techno="G9" class="bsdsdetails '.$pre_GSM900.'">
				<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM900</a></li>';
            }  
    		if(strpos($_POST['technos'], 'G18')!==false){
    			if(strpos($res1['TECHNOLOGY'][$key], 'G18')!==false){
  					$pre_GSM1800=$statuscolor;
	  			}else{
	  				$pre_GSM1800='BSDS_preready';
	  			}
				$output.='<li data-id="'.$popup.'" data-techno="G18" class="bsdsdetails '.$pre_GSM1800.'">
				<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> GSM1800</a></li>';
            } 
            if(strpos($_POST['technos'], 'U9')!==false){
             	if(strpos($res1['TECHNOLOGY'][$key], 'U9')!==false){
  					$pre_UMTS900=$statuscolor;
	  			}else{
	  				$pre_UMTS900='BSDS_preready';
	  			}
				$output.='<li data-id="'.$popup.'" data-techno="U9" class="bsdsdetails '.$pre_UMTS900.'">
				<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS900</a></li>';
            } 

            if(strpos($_POST['technos'], 'U21')!==false){
              	if(strpos($res1['TECHNOLOGY'][$key], 'U21')!==false){
  					$pre_UMTS2100=$statuscolor;
	  			}else{
	  				$pre_UMTS2100='BSDS_preready';
	  			}
				$output.='<li data-id="'.$popup.'" data-techno="U21" class="bsdsdetails '.$pre_UMTS2100.'">
				<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> UMTS2100</a></li>';
            }  
            if(strpos($_POST['technos'], 'L8')!==false){
             	if(strpos($res1['TECHNOLOGY'][$key], 'L8')!==false){
  					$pre_LTE800=$statuscolor;
	  			}else{
	  				$pre_LTE800='BSDS_preready';
	  			}
				$output.='<li data-id="'.$popup.'" data-techno="L8" class="bsdsdetails '.$pre_LTE800.'">
				<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE800</a></li>';
            } 
            if(strpos($_POST['technos'], 'L18')!==false){
             	if(strpos($res1['TECHNOLOGY'][$key], 'L18')!==false){
  					$pre_LTE1800=$statuscolor;
	  			}else{
	  				$pre_LTE1800='BSDS_preready';
	  			}
				$output.='<li data-id="'.$popup.'" data-techno="L18" class="bsdsdetails '.$pre_LTE1800.'">
				<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE1800</a></li>';
            }   
            if(strpos($_POST['technos'], 'L26')!==false){
            	if(strpos($res1['TECHNOLOGY'][$key], 'L26')!==false){
  					$pre_LTE2600=$statuscolor;
	  			}else{
	  				$pre_LTE2600='BSDS_preready';
	  			}
				$output.='<li data-id="'.$popup.'" data-techno="L26" class="bsdsdetails'.$pre_LTE2600.'">
				<a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-eye-open"></span> LTE2600</a></li>';
            } 
             
            $output.=' 	       
              <li class="divider"></li>
              <li data-bsdskey="'.$res1['BSDSKEY'][$key].'" data-id="'.$popup.'" data-techno="PRINT" data-technos="'.$_POST['technos'].'" class="bsdsdetails">
              <a href="#" tabindex="-1">&nbsp; <span class="glyphicon glyphicon-print"></span> PRINT</a></li>
		  	</ul>
		  	</div>';
			}
			$output .="</td>
			</tr>";

			if ($output_newblock!="" && $i!=0){
					$output_fund_history_t.="<tr class='fund_hist_data'><td colspan='7'>&nbsp;</td></tr>";
					$output_asbuild_history_t.=$output_newblock;
			}

			if (($total_debarredNEW==1 || $total_debarredUPG==1)
			&& $latestBSDS_amount==1 && $latestBSDS_status=='BSDS AS BUILD'
			&& $BOB_REFRESH_DATE_BSDSFUNDED==$res1['BSDS_BOB_REFRESH'][$key] 
			&& $latestBSDSKEY==$res1['BSDSKEY'][$key]){
						$output_asbuild.=$output;
			}else{
				if ($res1['STATUS'][$key]=="SITE FUNDED" OR $res1['STATUS'][$key]=="BSDS FUNDED"
					OR $res1['STATUS'][$key]=="BSDS FUNDED => DEFUNDED"  OR $res1['STATUS'][$key]=="SITE FUNDED => DEFUNDED"
					|| $res1['STATUS'][$key]=="BSDS DEFUNDED" || $res1['STATUS'][$key]=="BSDS FUNDED => DEFUNDED TO OLD DATE"
					 || $res1['STATUS'][$key]=="SITE FUNDED => DEFUNDED TO OLD DATE" || $techno_changed){

						if ($previous_UPGNR_FUND!=$UPGNR && $i!=0 && $previous_UPGNR_FUND!=""){
							$output_fund_history_t.="<tr class='fund_hist_data' style='display:none;'><td colspan='7'>&nbsp;</td></tr>";
						}
						$output_fund_history_t.=$output;
						$previous_UPGNR_FUND=$res1['UPGNR'][$key];

				}else if ($res1['STATUS'][$key]=="BSDS AS BUILD"  OR $res1['STATUS'][$key]=="BSDS AS BUILD => DEFUNDED TO OLD DATE"
				|| $techno_changed){

						if ($previous_UPGNR_BUILD!=$UPGNR && $i!=0 && $previous_UPGNR_BUILD!=""){
							$output_asbuild_history_t.="<tr class='fund_hist_data'><td colspan='7'>&nbsp;</td></tr>";
						}
						$output_asbuild_history_t.=$output;
						$previous_UPGNR_BUILD=$res1['UPGNR'][$key];

				}
			}
			$popup=$popup+1;
			$i++;
		}

		if ($output_fund_history_t){
			$output_fund_history.="<thead>
									<tr class='fund_hist_data' style='display:none;'>
										<th>BSDS ID</th>
										<th>UPGNR</th>
										<th>Technology</th>
										<th>NET1 date</th>
										<th>Copied date</th>
										<th>Status</th>
										<th style='width:100px;'>&nbsp;</th>
									</tr>
									</thead>
									<tbody>";
			$output_fund_history.=$output_fund_history_t;
			$output_fund_history.="</tbody>";
		}
		if ($output_asbuild_history_t){
			$output_asbuild_history.="<thead>
									<tr class='build_hist_data' style='display:none;'>
										<th>BSDS ID</th>
										<th>UPGNR</th>
										<th>Technology</th>
										<th>NET1 date</th>
										<th>Copied date</th>
										<th>Status</th>
										<th style='width:100px;'>&nbsp;</th>
									</tr>
									</thead>
									<tbody>";
			$output_asbuild_history.=$output_asbuild_history_t;
			$output_asbuild_history.="</tbody>";
		}

	}

}
?>
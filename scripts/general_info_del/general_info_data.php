<?php
//GET THE DATE OF LATEST TIME THE BOB REPORT HAS BEEN REFRESHED
$REFRESH_DATE=get_BSDSrefresh();
$DB_BOB_refresh=$REFRESH_DATE['DATE_UPG'];
$DB_BOB_refresh_amount=$REFRESH_DATE['AMOUNT'];
if ($DB_BOB_refresh=="" || $DB_BOB_refresh_amount==0){
	?>
	<script language="JavaScript">
	Messenger().post({
	  message: 'Due to problems with the link between Aircom and Base, the BSDS-module is unavailable',
	  type: 'error',
	  showCloseButton: false
	});
	</script>
	<?
	die;
}

$lognode['G18']=$_POST['lognodeID_GSM'];
$lognode['G9']=$_POST['lognodeID_GSM'];
$lognode['U21']=$_POST['lognodeID_UMTS2100'];
$lognode['U9']=$_POST['lognodeID_UMTS900'];
$lognode['L18']=$_POST['lognodeID_LTE1800'];
$lognode['L26']=$_POST['lognodeID_LTE2600'];
$lognode['L8']=$_POST['lognodeID_LTE800'];

/*
* Get address and classcode info out of NET1
*/
$fname_voor=substr($_POST['siteID'],0,1);
if ($fname_voor=="M" || $fname_voor=="S"){
	$fname_pre=substr($_POST['site'],1);
}else{
	$fname_pre=$_POST['siteID'];
}
$coor=get_coordinates($fname_pre);
$siteinfo=get_siteinfo($_POST['candidate']);
$Sitename=$siteinfo['SIT_UDK'][0];
$Classcode=$siteinfo['SIT_LKP_STY_CODE'][0];
$adress=$siteinfo['SIT_ADDRESS'][0]."<br>";

//First make sure there are BDS(s) existing => count
$query = "Select * FROM BSDS_GENERALINFO WHERE SITEKEY = '".$_POST['ADDRESSFK']."' AND DELETEDSTATUS!='yes' ORDER BY CHANGE_DATE";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_BSDSs=count($res1['BSDSKEY'][0]);
	if ($amount_of_BSDSs>0){
		$funded=get_BSDS_site_funded($DB_BOB_refresh,$_POST['candidate'],$_POST['siteID']);
		$BSDSKEY_FUNDEDBSDS=$funded['BSDSKEY'][0];
		$IDNR=$funded['IDNR'][0];
		$copied=$funded['COPIED'][0];
		$copied_date=$funded['COPIED_DATE'][0];

		require_once("NET1_bsds_data.php");
		$BSDS_funded=check_NET1_BSDS_funded($_POST['candidate'],''); //see file NET1_bsds_data.php
		//echo "<pre>".print_r($BSDS_funded,true)."</pre>";

		if ($BSDS_funded['STATUS']=="ERROR"){
			?>
			<script language="JavaScript">
			Messenger().post({
			  message: 'NET1 error: No technology has been defined in NET1.',
			  type: 'error',
			  showCloseButton: false
			}); 
			</script>
			<?
			die;
		}
		//echo count($BSDS_funded);

		if (count($BSDS_funded)>1){ //MULTIPLE UPGRADES/ACTIVITIES AT THE SAME TIME
			foreach ($BSDS_funded as $key=>$y){
				$UPGNR=$UPGNR."&".$BSDS_funded[$key]['UPGNR'];
			}
			$UPGNR= substr($UPGNR,1);
			$query_multi = "Select * FROM BSDS_FUNDED_MULTI WHERE BSDSKEY='".$BSDSKEY_FUNDEDBSDS."' AND BSDS_BOB_REFRESH='".$UPGNR."'";
			$stmt = parse_exec_fetch($conn_Infobase, $query_multi, $error_str, $res_m);
			$amount_of_MULTI=count($res_m['BSDSKEY'][0]);
			if (count($res_m['BSDSKEY'][0])!=1){ ?>
				<script language="JavaScript">
					Messenger().post({
					  message: "<h3>NET1 inconsistency!</h3>You can not have 2 activities for the same site at the same time in NET1 (<?=$UPGNR?>)!<br>Please contact Network delivery @ KPNGB to override this decision.",
					  type: 'error',
					  showCloseButton: false,
					   hideAfter: 5,
  					   hideOnNavigate: true
					});
				</script>
				<? if (substr_count($guard_groups, 'Base_delivery')==1 or substr_count($guard_groups, 'Administrators')==1){ ?>
				<form action= "scripts/general_info/general_info_actions.php" id="multioverride" method="POST">
				<input type="hidden" name="BSDS_BOB_REFRESH" value="<?=$UPGNR?>">
				<input type="hidden" name="BSDSKEY" value="<?=$BSDSKEY_FUNDEDBSDS?>">
				<input type="hidden" name="action" value="overrideMultiFund">
				<input type="submit" class="btn btn-primary" value="OVERRIDE MULTI UPGARDE" id="multioverrideButton">
				</form>
				<? }
				die;
			}
		}
		include("general_info_predata.php");
	}

	if ($BSDSKEY_FUNDEDBSDS!=""){

		$userdetails=getuserdata($DESIGNER_CREATE);
		$email=$userdetails['email'];
		$fullname=$userdetails['fullname'];
		$mobile=$userdetails['mobile'];

		$z=0;
		//SELECT BOB REPORT data and check if asbuild or funded
		//echo count($BSDS_funded);
		//echo "<pre>".print_r($BSDS_funded,true)."</pre>";

		if (count($BSDS_funded)>0){
			
				if ($TEAML_APPROVED_FUND_CHECK=="Accepted"){

					foreach ($BSDS_funded as $key=>$y){ //$y is an array!!!!
						$status="";
						$status=$BSDS_funded[$key]['STATUS'];
						$statuscolor=$BSDS_funded[$key]['COLOR'];
						$statusdate=$BSDS_funded[$key]['DATE'];
						$technos=$BSDS_funded[$key]['TECHNOLOGY'];
						$UPGNR=$BSDS_funded[$key]['UPGNR'];
						$ESTIM=$BSDS_funded[$key]['ESTIM'];
						$COMBINED=$BSDS_funded[$key]['COMBINED'];
						$NET1_DATE_SITEFUNDED=$BSDS_funded[$key]['NET1_DATE_SITEFUNDED'];
						$date=$BSDS_funded[$key]['DATE'];

						if ($status!="" && $status!="ERROR" && $statustechnologie!="CTX"){
							include("general_info_fund_data.php");
						}elseif ($status=="ERROR"){
							$output_funded.="<tr><td class='$statuscolor' colspan='6'>$statustechnologie</td></tr>";
							$output_asbuild.="<tr><td class='$statuscolor' colspan='6'>$statustechnologie</td></tr>";
						}
						$z++;
					}//END FOREACH
				}else{
					?>
					<script language="JavaScript">
						Messenger().post({
							message:"<h2>Please put 'ready for funding' to yes!</h2>The milestone in NetOne is set to funded!<br> Before the BSDS can be viewed, the BSDS in PRE must be accepted!",
							showCloseButton:false,
							type:'error'});
					</script>
					<?
				}			
		}else{ //end if ($BSDS_funded['amount']>0){ MEANS NO NET1 DATA
	//FOR UPG
			//We check if the latest BSDS ID is an BSDS FUNDED bsds.
			//This is to resolve the problem that upgrades are getting U380 and U571 (=debaring + ASBUILD) at the same time

			$query = "SELECT * FROM infobase.BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY_FUNDEDBSDS."'
			AND STATUS NOT LIKE 'TECHNOLOGY CHANGED FROM%'
			AND BSDS_BOB_REFRESH=(
					SELECT MAX(BSDS_BOB_REFRESH) FROM infobase.BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY_FUNDEDBSDS."'
					AND STATUS NOT LIKE 'TECHNOLOGY CHANGED FROM%')";
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
				exit;
			} else {
				OCIFreeStatement($stmt);
				$latestBSDS_amount=count($res1['UPGNR']);
			}
			$latestBSDS_status=$res1['STATUS'][0];
			$latestBSDSKEY=$res1['BSDSKEY'][0];

			if ($latestBSDS_amount==1){
				$UPGNR=$res1['UPGNR'][0];
				$TECHNOLOGY=$res1['TECHNOLOGY'][0];
				$TEAM_STATUS=$res1['TEAM_STATUS'][0];
				$TEAM_DATE=$res1['TEAM_DATE'][0];
				$TECHNOLOGY=$res1['TECHNOLOGY'][0];
				$TEAM_BY=$res1['TEAM_BY'][0];
				$QA_STATUS=$res1['QA_STATUS'][0];
				$STATUS=$res1['STATUS'][0];
				$COMMENTS=$res1['COMMENTS'][0];
				$BSDSKEY=$res1['BSDSKEY'][0];
				$BOB_REFRESH_DATE_BSDSFUNDED=$res1['BSDS_BOB_REFRESH'][0];

				//if the latest BSDS was an 'BSDS FUNDED' bsds, we check if it has a record in the NET1-ALL table RECORDS (= sites which are also debarred)
				$query2 = "SELECT WOR_NAME, U571, U353, SIT_UDK FROM infobase.VW_NET1_ALL_UPGRADES
				WHERE WOR_UDK='".$UPGNR."' AND U380 IS NOT NULL AND WOR_DOM_WOS_CODE='IS'";

				//echo "==>".$query2."<br>";
				$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
					exit;
				} else {
					OCIFreeStatement($stmt2);
					$total_debarredUPG=count($res2['WOR_NAME']);

					$NET1_DATE_U571=$res2['U571'][0];
					//If there is a record, we create an AS BUILD BSDS
					if ($total_debarredUPG==1 && $latestBSDS_status=='BSDS FUNDED'){
						$query = "INSERT INTO BSDS_FUNDED_TEAML_ACC2 VALUES
						('".$BSDSKEY."','".$_POST['candidate']."','".$TECHNOLOGY."','".$TEAM_DATE."','".$TEAM_STATUS."',
						'','".$COMMENTS."','".$QA_STATUS."','".$TEAM_BY."','".$NET1_DATE_U571."',SYSDATE,'".$DB_BOB_refresh."','BSDS AS BUILD',".$UPGNR.",'".$partner."')";

						$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
						if (!$stmt) {
							die_silently($conn_Infobase, $error_str);
						}else{
							OCICommit($conn_Infobase);
						}
						require_once("copy_site_data.php");
						echo "missing AS BUILD => copy procedure<br>";
						copy_site_data("BSDS AS BUILD",$BSDSKEY,$TECHNOLOGY,$DB_BOB_refresh,$copied,$BOB_REFRESH_DATE_BSDSFUNDED,$donor,$lognode);
					}
				}
		//We check if the latest BSDS ID is a NEWBUILD and if it has an as build BSDS.
		//This is to resolve the problem that newbuilds are getting A80 to fast
				//ASBUILD FOR NEW
				$query2 = "SELECT SIT_ID, A71, A353, SIT_UDK FROM infobase.VW_NET1_ALL_NEWBUILDS
				WHERE SIT_ID='".$UPGNR."' AND A80 IS NOT NULL AND WOR_DOM_WOS_CODE='IS'";
				//echo "==>".$query2."<br>";
				$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
				if (!$stmt2) {
					die_silently($conn_Infobase, $error_str);
					exit;
				} else {
					OCIFreeStatement($stmt2);
					$total_debarredNEW=count($res2['SIT_ID']);

					$NET1_DATE_A71=$res2['A71'][0];

					if ($total_debarredNEW==1 && $latestBSDS_status=='BSDS FUNDED'){
						$query = "INSERT INTO BSDS_FUNDED_TEAML_ACC2 VALUES
						('".$BSDSKEY."','".$_POST['candidate']."','".$TECHNOLOGY."','".$TEAM_DATE."','".$TEAM_STATUS."',
						'".$_SESSION['Sitekey']."','".$COMMENTS."','".$QA_STATUS."','".$TEAM_BY."','".$NET1_DATE_A71."',SYSDATE,'".$DB_BOB_refresh."','BSDS AS BUILD',".$UPGNR.",'".$partner."')";

						$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
						if (!$stmt) {
							die_silently($conn_Infobase, $error_str);
						}else{
							OCICommit($conn_Infobase);
						}
						require_once("../procedures/copy_site_data.php");
						echo "missing AS BUILD => copy procedure<br>";
						copy_site_data("BSDS AS BUILD",$BSDSKEY,$TECHNOLOGY,$DB_BOB_refresh,$copied,$BOB_REFRESH_DATE_BSDSFUNDED,$donor,$lognode);
					}
				}				
			}
		}
		include("general_info_fund_history.php");
	}else{//END foreach
		$output_pre.="<td>NO BSDS DATA FOUND!</td>";
	}
}
ocilogoff($conn_Infobase);
?>


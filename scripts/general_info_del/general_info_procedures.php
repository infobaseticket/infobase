<?PHP
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

/*********************************************************************************************************************/
function get_siteinfo($sitename){
	global $conn_Infobase;
	$query="select * from VW_NET1_ALL_NEWBUILDS
				where
					SIT_UDK like '%".$sitename."%'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	return $res1;
}
/***************************************************************************************************************/
function get_BSDS_site_funded($BOB_refresh,$candidate,$siteID){
	global $conn_Infobase;
	$query = "SELECT BSDSKEY,IDNR,COPIED, COPIED_DATE FROM INFOBASE.BSDS_SITE_FUNDED WHERE SITEID='".$candidate."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);

	}
	
	if (count($res1['BSDSKEY'])!=1){
		$BSDSKEY_TOBEFUNDED=get_latestdate_BSDS($siteID);  //MAX(CHANGE_DATE) will be used as BSDS (to get the purple 1)

		if ($BSDSKEY_TOBEFUNDED){ //CHECK IF THERE ARE BSDSs existing in PRE

			insert_site_funded_BSDS($BSDSKEY_TOBEFUNDED,$candidate); //INSERT INTO BSDS_SITE_FUNDED TABLE

			$query = "SELECT BSDSKEY,IDNR,COPIED FROM INFOBASE.BSDS_SITE_FUNDED WHERE SITEID='".$candidate."'";
			//echo $query."<br>";
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt);
				return $res1;
			}
		}
	}else{
		return $res1;
	}
}
/*********************************************************************************************************************/
function get_latestdate_BSDS($siteID){
	global $conn_Infobase;
	//GET THE LATEST BSDSKEY
	$query_latest = "SELECT MAX(BSDSKEY) as BSDSKEY FROM BSDS_GENERALINFO WHERE SITEID
	= '".$siteID."' AND
	CHANGE_DATE=(Select MAX(CHANGE_DATE)
	FROM BSDS_GENERALINFO WHERE SITEID = '".$siteID."')";
	//echo $query;
	$stmt_latest = parse_exec_fetch($conn_Infobase, $query_latest, $error_str, $res_latest);
	if (!$stmt_latest) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt_latest);
		//echo count($res_latest['BSDSKEY']);
		if (count($res_latest['BSDSKEY'])>1){
		?>
		<font color='red'>Something was wrong with Infobase for this site!<br></font>
		<font size=1>Too many data found in BSDS_GENERALINFO for site <?=$siteID?></font><br>
		Please try to reload this site again.
		<? die;
		}else{
			return $res_latest['BSDSKEY'][0];
		}
	}
}
/*********************************************************************************************************************/
function insert_site_funded_BSDS($latest_BSDS,$site){
	global $conn_Infobase;
	$query="DELETE FROM INFOBASE.BSDS_SITE_FUNDED WHERE SITEID='".$site."' OR BSDSKEY='".$latest_BSDS."'";
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}
	OCICommit($conn_Infobase);
	$query="INSERT INTO INFOBASE.BSDS_SITE_FUNDED (BSDSKEY, SITEID, UPDATE_DATE, COPIED) VALUES ('$latest_BSDS','".$site."',SYSDATE,'NO')";
	echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}
	OCICommit($conn_Infobase);
	return $IDNR;
}
/*********************************************************************************************************************/
function get_BSDS_info($lognode){
	global $conn_Infobase, $config;

	$query = "select * from ".$config['table_asset_geninfo']." WHERE SITEKEY=".$lognode;
	//echo "$query<br>";
	//die;
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	return $res1;
}
/********************************************************************************************************************/
function get_BSDS_info2($Sitekey){
	global $conn_Infobase,$config;
	$query = "select * from ".$config['table_asset_geninfo']." WHERE SITEKEY=".$Sitekey;
	//echo "$query<br>";
   	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	return $res1;
}
/*********************************************************************************************************************/
function get_coordinates($fname_pre){

	global $conn_Infobase;

	$query1 = "select * from ASSET_COORD WHERE SITE LIKE '%".$fname_pre."%'";
	//echo "$query1 <br>";
   	$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
   	if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
   	} else {
      OCIFreeStatement($stmt);
   	}
	$ret['longitude']=$res1['XLAMBERT'][0];
	$ret['latitude']=$res1['YLAMBERT'][0];
	$ret['x']=$res1['X'][0];
	$ret['y']=$res1['Y'][0];
	/*
	$last_line =shell_exec("coord_conv $longitude $latitude");
	$longlat=explode(" ",$last_line);
	//echo "<pre>".print_r($longlat,true);
	$ret['longitude']=$longlat[0];
	$ret['latitude']=$longlat[1];
	*/
	return $ret;
}
/********************************************************************************************************************/
function accept_teaml_fundtech($candidate,$status,$BSDSKEY,$NET1_date_from_BOB,$BSDSFUNDED_BOB_REFRESH,
	$DB_BOB_refresh,$copied,$technos,$UPGNR,$donor,$lognode,$partner)
{

	global $conn_Infobase, $guard_username, $firephp;

	require_once("copy_site_data.php");

	if ($BSDSKEY!="" && $candidate!=""){
		if ($QA_STATUS==""){
			$QA_STATUS='Pending';
			$QA_STATUS_BY="";
		}
		if ($NET1_date_from_BOB!="" && $QA_STATUS_BY==""){
			$QA_STATUS_BY="BASE";
		}

		//WE FIRST CHECK IF THE RECORD ISNT ALREADY EXISTING BASED ON THE CURRENT SELECTED BSDS AND BSDS_BOB_REFRESH date

		$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY."'
		AND SITEID='".$candidate."' AND UPGNR='".$UPGNR."'
		AND TECHNOLOGY='".$technos."' AND STATUS NOT LIKE 'TECHNOLOGY CHANGED FROM%'
		AND BSDS_BOB_REFRESH=(
				SELECT MAX(BSDS_BOB_REFRESH) FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY."'
				AND SITEID='".$candidate."'  AND UPGNR='".$UPGNR."' AND TECHNOLOGY='".$technos."' AND STATUS NOT LIKE 'TECHNOLOGY CHANGED FROM%')";
		//echo "==>".$query."<br>";

		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
		    $total_records=count($res1['BSDSKEY']); // CAN ONLY RETURN 1 or 0 (NOT MORE THAN 1!!!!)
	    	$STATUS_DATE_NET1=$res1['NET1_DATE'][0];
			$STATUS_BSDS=$res1['STATUS'][0];
			$TEAM_STATUS=$res1['TEAM_STATUS'][0];
			$TEAM_DATE=$res1['TEAM_DATE'][0];
			//HERE WE CHANGE TECHNOLOGY CONVENTION TO INFOBASE V4:
			$TECHNOLOGY=$res1['TECHNOLOGY'][0];
			$BOB_EREFRESH_DATE_previous_status=$res1['BSDS_BOB_REFRESH'][0];
			$PARTNER_VIEW=$res1['PARTNER_VIEW'][0];
			$previous_STATUS_BSDS=$res1['STATUS'][0];
		}

		//echo "$STATUS_DATE_NET1 total_records ".$total_records."-- $status -- $technos ---> STATUS_BSDS: $STATUS_BSDS $BOB_EREFRESH_DATE_previous_status<br>";

		$findme   = '/';
		$pos = strpos($STATUS_DATE_NET1, $findme);

		// Note our use of ===.  Simply == would not work as expected
		// because the position of 'a' was the 0th (first) character.
		$pos = strpos ($mystring, "b");
		if ($pos === true){
			$message="PROBLEM WITH NET1 DATE FORMAT! PLEASE CONTACT INFOBASE ADMIN ASAP";
			$data[1]=$message;
			$data[0]="";
			$data[2]="error";
			return $data;
		}

		//==> A bsds is existing
		//echo $BSDSKEY."/".$BSDS_BOB_REFRESH."***".$total_records;
		//echo "$STATUS_DATE_NET1!=$NET1_date_from_BOB ($DB_BOB_refresh // $BOB_EREFRESH_DATE_previous_status)<br>";
		if ($total_records=="1"){
				//echo "($status==$STATUS_BSDS // DB_BOB_refresh $DB_BOB_refresh // BOB_EREFRESH_DATE_previous_status $BOB_EREFRESH_DATE_previous_status// && $STATUS_DATE_NET1 (TASK DATE FROM BSDS)!=$NET1_date_from_BOB (DATE FROM NET1))";
				if ($status==$STATUS_BSDS && $STATUS_DATE_NET1!=$NET1_date_from_BOB){
					if ($status=="SITE FUNDED"){
						$date="U/A353";
						$message = "<h3>For UPG/NB ".$UPGNR.":</h3>You are trying to 'SITE fund' a BSDS with a date of ".$NET1_date_from_BOB."<br>";
						$message .= "however there is already a '".$status."' BSDS existing with date of ".$STATUS_DATE_NET1."!.<br>";
						$message .= "Please restore ".$date." in NET1 to ".$STATUS_DATE_NET1." or remove date if you want to make modifications to the BSDS!";

					}else if ($status=="BSDS FUNDED"){
						$date="U/A305";
						$message = "<h3>For UPG/NB ".$UPGNR.":</h3>You are trying to fund a BSDS with a date of ".$NET1_date_from_BOB."<br>";
						$message .= "however there is already a '".$status."' BSDS existing with date of ".$STATUS_DATE_NET1."!.<br>";
						$message .= "Please restore ".$date." in NET1 to ".$STATUS_DATE_NET1." or remove date if you want to make modifications to the BSDS!";
					}else if ($status=="BSDS AS BUILD"){
						$message = "<h3>For UPG/NB ".$UPGNR.":</h3>You are trying to put an inconsistent Integration date (U571/A71) in NET1:  ".$NET1_date_from_BOB."<br>";
						$message .= "however there is already a '".$status."' BSDS existing with date of ".$STATUS_DATE_NET1."!.<br>";
						$message .= "Please be aware that you cannot change the FUNDED BSDS if there is an AS-BUILT BSDS existing for the same BSDS ID. Changes needed are to be performed in Asset (after site is Debarred or after approval from KPNGB RF) and will be reflected in the “Current” view of the PRE- and AS-BUILT BSDS (the “Current” view of the FUNDED BSDS is a copy of the “Current” view at the time the BSDS was set to BSDS FUNDED)";
					}
					$res1="ERROR";
					$message_type="error";

			/**************************************/
			/* WE CHECK IF THE DATES HAVE CHANGED */
			/**************************************/
			//==>NET_BOB!=NET1_BSDS + refresh has happend.
			//====>If yes, a new copy will be made:

				}else if ($status!=$STATUS_BSDS && strpos($STATUS_BSDS, "DEFUNDED")===false && $STATUS_DATE_NET1==$NET1_date_from_BOB){
					$message = "<h3>For UPG/NB ".$UPGNR.":</h3>You are putting the same date for SITE FUNDING AND BSDS FUNDING or for BSDS FUNDING AND BSDS AS BUILD:  ".$NET1_date_from_BOB."<br>";
					$message .= "however there is already a '".$status."' BSDS existing with date of ".$STATUS_DATE_NET1."!.<br>";
						
					$res1="ERROR";
					$message_type="error";

				}
				else if (($STATUS_DATE_NET1!=$NET1_date_from_BOB  && $STATUS_BSDS!="" && $DB_BOB_refresh!=$BOB_EREFRESH_DATE_previous_status) || trim($STATUS_DATE_NET1)=="")
				{

					//echo $status."!=".$STATUS_BSDS."------".$NET1_date_from_BOB."-".$STATUS_DATE_NET1;
					$NET1_date_from_BOB_2 = strtotime($NET1_date_from_BOB);
					$STATUS_DATE_NET1_2 = strtotime($STATUS_DATE_NET1);
					//echo "$STATUS_DATE_NET1!=$NET1_date_from_BOB ($DB_BOB_refresh // $BOB_EREFRESH_DATE_previous_status)<br>";
					//echo "$NET1_date_from_BOB_2 ($NET1_date_from_BOB) >$STATUS_DATE_NET1_2 ($STATUS_DATE_NET1)";

			//======>The NET1 data hase to be bigger than the one in the db.
			//======>If yes => Make a new copy: BSDS status has changed to $status

					if ($NET1_date_from_BOB_2>$STATUS_DATE_NET1_2 ||$NET1_date_from_BOB=="01-JAN-1990" ||$NET1_date_from_BOB=="01-JAN-1900"){
							//echo "--- $UPGNR --";
							$query = "INSERT INTO BSDS_FUNDED_TEAML_ACC2 VALUES
							('".$BSDSKEY."','".$candidate."','".$technos."','".$TEAM_DATE."','".$TEAM_STATUS."',
							'','','','',
							'".$NET1_date_from_BOB."',SYSDATE,'".$DB_BOB_refresh."','".$status."','".$UPGNR."','".$partner."')";
							$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
							if (!$stmt) {
								die_silently($conn_Infobase, $error_str);
							}else{
								OCICommit($conn_Infobase);
							}

							copy_site_data($status,$BSDSKEY,$technos,$DB_BOB_refresh,$copied,$BOB_EREFRESH_DATE_previous_status,$donor,$lognode);

							$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY."'
							AND SITEID='".$candidate."'
							AND TECHNOLOGY='".$technos."'
							 AND UPGNR='".$UPGNR."'
							AND BSDS_BOB_REFRESH=(
							SELECT MAX(BSDS_BOB_REFRESH) FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY."'
							AND SITEID='".$candidate."' AND TECHNOLOGY='".$technos."' AND UPGNR='".$UPGNR."')";
							//echo $query;
							$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
							if (!$stmt) {
								die_silently($conn_Infobase, $error_str);
							 	exit;
							} else {
								OCIFreeStatement($stmt);
							}

			//====> If no (NET1date in BOB is smaller than or equal to NET1date latest BSDSID),
			//This means the BSDS has previously been defunded and now refunded to an older date or the same date.
					}else if ($NET1_date_from_BOB_2<$STATUS_DATE_NET1_2 && (($status=="BSDS FUNDED" && $STATUS_BSDS=="SITE FUNDED" ) or ($status=="BSDS AS BUILD" && $STATUS_BSDS=="BSDS FUNDED"))){
						$message = "<h3>For ".$UPGNR." following error occured:</h3>The '".$STATUS_BSDS."' has a date $STATUS_DATE_NET1<br>In NET1 you put $NET1_date_from_BOB".$net1_date." in NET1 for $status!!<br><b>The $status date has to be bigger than $STATUS_BSDS date !!</b>";
						$message_type="error";
					}else{

						if ($STATUS_BSDS == "BSDS FUNDED" or $STATUS_BSDS == "BSDS FUNDED => DEFUNDED TO OLD DATE"){
							$net1_date="UA305";
						}elseif ($STATUS_BSDS == "SITE FUNDED" or $STATUS_BSDS == "SITE FUNDED => DEFUNDED TO OLD DATE"){
							$net1_date="UA353";							
						}
						$message = "<h3>BSDS has been defunded for ".$UPGNR."</h3>The '".$STATUS_BSDS."'  for <b>".$technos."</b> has been defunded by removing ".$net1_date." in NET1!!<br>";
						if ($status!="BSDS FUNDED"){
							$message.= "This will allow you to update the ".$status." BSDS in red.";
						}else if($status=="BSDS FUNDED"){
							$message.= "You are not allowed to defund and 'AS BUILD' bsds!";
						}
						$message_type="warning";

						if ($STATUS_BSDS != "BSDS FUNDED => DEFUNDED TO OLD DATE" && $STATUS_BSDS != "BSDS AS BUILD => DEFUNDED TO OLD DATE"
						&& $STATUS_BSDS != "SITE FUNDED => DEFUNDED TO OLD DATE")
						{
							$query = "INSERT INTO BSDS_FUNDED_TEAML_ACC2 VALUES
								('".$BSDSKEY."','".$candidate."','".$technos."','','Pending',
								'','','','',
								'".$STATUS_DATE_NET1."',SYSDATE,'".$DB_BOB_refresh."','".$STATUS_BSDS." => DEFUNDED TO OLD DATE','".$UPGNR."','".$partner."')";
							$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
							if (!$stmt) {
								die_silently($conn_Infobase, $error_str);
							}else{
								OCICommit($conn_Infobase);

								if ($STATUS_BSDS=="BSDS FUNDED"){
									$type="BSDS FUNDED DEFUNDED";
									copy_site_data($type,$BSDSKEY,$technos,$DB_BOB_refresh,$copied,$BOB_EREFRESH_DATE_previous_status,$donor,$lognode);
								}else if ($STATUS_BSDS=="BSDS AS BUILD"){
									$type="BSDS AS BUILD DEFUNDED";
									copy_site_data($type,$BSDSKEY,$technos,$DB_BOB_refresh,$copied,$BOB_EREFRESH_DATE_previous_status,$donor,$lognode);
								}

								$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY."'
								AND SITEID='".$candidate."'
								AND TECHNOLOGY='".$technos."' AND NET1_DATE='".$STATUS_DATE_NET1."'
								AND status='".$STATUS_BSDS." => DEFUNDED TO OLD DATE'
								AND UPGNR='".$UPGNR."'";
								//echo $query;
								$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
								if (!$stmt) {
									die_silently($conn_Infobase, $error_str);
								 	exit;
								} else {
									OCIFreeStatement($stmt);
								}

							}
						}
					}

		  //==>NET_BOB==NET1_BSDS + refresh has happend.
		  //A BDS is existing (count!=0)
				}else if($STATUS_DATE_NET1==$NET1_date_from_BOB && $STATUS_BSDS!="BSDS FUNDED => DEFUNDED TO OLD DATE"
				 && $STATUS_BSDS!="BSDS AS BUILD => DEFUNDED TO OLD DATE")
				{
					//WE check that the MILSTONE FOR FUNDING hasn't change to an older date
					//check if there is a history with same BSDSKEY. If yes, see if not already existing with same NET1DATE.
					//If yes, insert a record with STATUS= BSDS DEFUNDED
					$query = "Select * FROM BSDS_FUNDED_TEAML_ACC2 WHERE SITEID LIKE '%".$candidate."%' AND
					BSDSKEY='".$BSDSKEY."' AND NET1_DATE='".$NET1_date_from_BOB."' AND BSDS_BOB_refresh!='".$BOB_EREFRESH_DATE_previous_status."'
					AND UPGNR='".$UPGNR."'
					 AND STATUS NOT LIKE 'TECHNOLOGY CHANGED FROM%' AND TECHNOLOGY='".$technos."'";
					//echo "<font color=orange>".$query."</font><br>";
					$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					}
					else
					{
						OCIFreeStatement($stmt);

						//echo "$STATUS_BSDS != $status ".count($res2['BSDSKEY']);
						if (count($res2['BSDSKEY'])!=0){
							
							if ($STATUS_BSDS=="SITE FUNDED" or $STATUS_BSDS=="SITE FUNDED => DEFUNDED TO OLD DATE"){
								$act_by="Network Delivery (Please conatct them!)";
							}else{
								$act_by="The partner";
							}
							$message = "THE BSDS $BSDSKEY (".$STATUS_BSDS.") HAS BEEN REFUNDED WITH THE SAME DATE for ".$technos."! <br>=> ".$act_by." has to put a date newer than ".$STATUS_DATE_NET1." (for ".$UPGNR.")<br>";
							$message_type="error";
							if (count($res2['BSDSKEY'])!=0 && $STATUS_BSDS != $status && $STATUS_BSDS!="SITE FUNDED => DEFUNDED"
								&& $STATUS_BSDS!="BSDS FUNDED => DEFUNDED" && $STATUS_BSDS!="BSDS ASBUILD => DEFUNDED"
								&& $STATUS_BSDS!="SITE FUNDED => DEFUNDED TO OLD DATE"
								&& $STATUS_BSDS!="BSDS FUNDED => DEFUNDED TO OLD DATE"
								&& $STATUS_BSDS!="BSDS ASBUILD => DEFUNDED TO OLD DATE"
								&& $STATUS_BSDS!="BSDS DEFUNDED")
							{
								$query = "INSERT INTO BSDS_FUNDED_TEAML_ACC2 VALUES
								('".$BSDSKEY."','".$candidate."','".$technos."','','Pending',
								'','','','',
								'".$NET1_date_from_BOB."',SYSDATE,'".$DB_BOB_refresh."','".$status." => DEFUNDED','".$UPGNR."','".$partner."')";
								$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
								if (!$stmt) {
									die_silently($conn_Infobase, $error_str);
								}else{
									OCICommit($conn_Infobase);
								}
							}
							$data[1]=$message;
							$data[2]=$message_type;
							return $data;
						}else{
							//echo "THE BSDS DID NOT CHANGE!!<br>";
						}
					}
				}
		//==>Nothing changed: BSDS still defunded
				else if ($STATUS_BSDS=="BSDS FUNDED => DEFUNDED TO OLD DATE" || $STATUS_BSDS=="BSDS AS BUILD => DEFUNDED TO OLD DATE" || $STATUS_BSDS=="SITE FUNDED => DEFUNDED TO OLD DATE")
				{
						$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY."'
						AND SITEID='".$candidate."'  AND UPGNR='".$UPGNR."'
						AND TECHNOLOGY='".$technos."' AND NET1_DATE='".$STATUS_DATE_NET1."' AND status='".$STATUS_BSDS."'  AND UPGNR='".$UPGNR."'";
						//echo $query;
						$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
						if (!$stmt) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt);
						}
						if ($STATUS_BSDS == "BSDS FUNDED => DEFUNDED TO OLD DATE"){
							$net1_date="UA305";
						}elseif ($STATUS_BSDS == "BSDS AS BUILD => DEFUNDED TO OLD DATE"){
							$net1_date="U571/A71";							
						}elseif ($STATUS_BSDS == "SITE FUNDED => DEFUNDED TO OLD DATE"){
							$net1_date="UA353";							
						}
						$message = "<h3>BSDS for ".$UPGNR." has been defunded</h3>";
						$message .= "The BSDS for technology ".$technos." has been defunded and put back to status ".$status." by removing ".$net1_date." in NET1<br>";
						$message .= "Please make sure if you RE-fund that ".$net1_date." is newer than previous funding date (> $STATUS_DATE_NET1).<br>";
						$message_type="warning";
				}
				
		//END if ($total_records=="1"){
		}else if ($total_records=="0"){

			// WE FIRST CHECK IF TECHNOLOGY HASN'T CHANGED
			$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY."'
			AND SITEID='".$candidate."'  AND BSDS_BOB_REFRESH=(
			SELECT MAX(BSDS_BOB_REFRESH) FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY='".$BSDSKEY."'
			AND SITEID='".$candidate."'   AND UPGNR='".$UPGNR."'  AND STATUS NOT LIKE 'TECHNOLOGY CHANGED FROM%')";
			//echo $query;
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt);
			    $total_records=count($res1['BSDSKEY']); // CAN ONLY RETURN 1 or 0 (NOT MORE THAN 1!!!!)
		    	$STATUS_DATE_NET1=$res1['NET1_DATE'][0];
				$STATUS_BSDS=$res1['STATUS'][0];
				$TEAM_STATUS=$res1['TEAM_STATUS'][0];
				$TEAM_DATE=$res1['TEAM_DATE'][0];
				$TECHNOLOGY=$res1['TECHNOLOGY'][0];
				$BOB_EREFRESH_DATE_previous_status=$res1['BSDS_BOB_REFRESH'][0];
			}
			//echo $total_records;
			
			//echo $BOB_EREFRESH_DATE_previous_status."-".$TECHNOLOGY."!=".$technos;
			if ( $TECHNOLOGY!=$technos && $STATUS_DATE_NET1 == $NET1_date_from_BOB){

				if ($status=="SITE FUNDED"){
					//THIS MEANS THAT THE TECHNOLOGIES HAVE CHANGED

					$query = "UPDATE BSDS_FUNDED_TEAML_ACC2 SET TECHNOLOGY='".$technos."'
					WHERE BSDS_BOB_REFRESH='".$BOB_EREFRESH_DATE_previous_status."' AND BSDSKEY='".$BSDSKEY."' AND SITEID='".$candidate."'";
					//echo $query;
					$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}

					$query = "INSERT INTO BSDS_FUNDED_TEAML_ACC2 VALUES
					('".$BSDSKEY."','".$candidate."','".$technos."','','Pending',
					'','','','',
					'".$NET1_date_from_BOB."',SYSDATE,'".$DB_BOB_refresh."','TECHNOLOGY CHANGED FROM ".$TECHNOLOGY." to ".$technos."','".$UPGNR."','".$partner."')";
					//echo $query;
					$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}
					//We recopy from PRE to POST (should only copy changed technos)
					
					copy_site_data($status,$BSDSKEY,$technos,$BOB_EREFRESH_DATE_previous_status,$copied,$BOB_EREFRESH_DATE_previous_status,$donor,$lognode);

					$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDS_BOB_REFRESH='".$BOB_EREFRESH_DATE_previous_status."' AND TECHNOLOGY='".$technos."'  AND UPGNR='".$UPGNR."'";
					//echo $query;
					$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					} else {
						OCIFreeStatement($stmt);
					}
				}else if ($status!="SITE FUNDED"){
					//From Infobase V3->V4:
					$TECHNOLOGY=analyseTechno($TECHNOLOGY);
					if ($TECHNOLOGY!=$technos){
					$message = "<h3>TECHNO CHANGE WITHOUT DEFUNDING!</h3>Technology has changed without defunding the BSDS! (".htmlspecialchars($TECHNOLOGY)." -> ".$technos." for ".$UPGNR.")<br>";
					$message .= "The technologies of a BSDS can only be changed for 'SITE FUNDED' bsds.<br>";
					$message .= "For 'BSDS FUNDED' status (orange),<br> the BSDS needs to be defunded (remove MS U/A305 in NET1)!";
					$res1="ERROR";
					$message_type="error";
					}
				}
			}else{
				//NEW BSDS FOR FUNDING
				if ($total_records==1){
					$teaml_acc="Accepted";
				}else{
					$teaml_acc="Pending";
				}
				if ($status=="SITE FUNDED"){

					$query = "INSERT INTO BSDS_FUNDED_TEAML_ACC2 VALUES
					('".$BSDSKEY."','".$candidate."','".$technos."','','".$teaml_acc."',
					'','','','',
					'".$NET1_date_from_BOB."',SYSDATE,'".$DB_BOB_refresh."','".$status."','".$UPGNR."','".$partner."')";
					//echo $query;
					$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					}else{
						OCICommit($conn_Infobase);
					}

					copy_site_data($status,$BSDSKEY,$technos,$DB_BOB_refresh,$copied,$BOB_EREFRESH_DATE_previous_status,$donor,$lognode);

					$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC2 WHERE BSDS_BOB_REFRESH='".$DB_BOB_refresh."' AND TECHNOLOGY='".$technos."' AND UPGNR='".$UPGNR."'";
					//echo $query;
					$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
						exit;
					} else {
						OCIFreeStatement($stmt);
					}
				}else{
					$message = "<h3>MISSING SITE FUNDED BSDS</h3>The BSDS has been set to 'BSDS FUNDED' without an existing 'SITE FUNDED' bsds (key: ".$BSDSKEY.")!<br>";
					$message .= "Please remove BSDS funding date (".$NET1_date_from_BOB.") in NET1 for UPG/NB ".$UPGNR."!<br>";
					if ($STATUS_DATE_NET1 != $NET1_date_from_BOB){
					$message .= "Please aslo make sure that the SITE FUNDING date (U/A353) in NET1 is still the same as in the SITE FUNDED history!";
					}
					//$res1="ERROR";
					$message_type="error2";
				}
			}
		}else{
				$message = "Infobase error due to old Infobase import. Please contact <a href='mailto:frederick.eyland@basecompany.be'>Infobase admin</a> with following message: ERROR in BOB report date for site x and BSDS x should be made later.";
				$res1="ERROR";
				$message_type="error";
		}

		$data[0]=$res1;
		$data[1]=$message;
		$data[2]=$message_type;
		return $data;
	}
}

function analyseTechno($technos){
	if(strpos($technos, "GSM900")!==false or strpos($technos, "G9")!==false or strpos($technos, "GSM9")!==false or strpos($technos, "EGS")!==false){
		$G9="G9+";
	}else{ //FOR NB 900&
		$start = 0;
		while(($pos = strpos($technos, "900", $start)) !== false){
		   	if((substr($technos,$pos-4,4)!="UMTS" && substr($technos,$pos-1,1)!="U" 
		   		&& substr($technos,$pos-3,3)!="LTE" && substr($technos,$pos-1,1)!="L")
		   		 or $pos==0){
				$G9="G9+";
		   	}
			$start = $pos+1;
		}
	}
	if(strpos($technos, "GSM1800")!==false or strpos($technos, "G18")!==false or strpos($technos, "GSM18")!==false or strpos($technos, "DCS")!==false){
		$G18="G18+";
	}else{ //FOR NB 1800&
		$start = 0;
		while(($pos = strpos($technos, "1800", $start)) !== false){
		   	if((substr($technos,$pos-4,4)!="UMTS" && substr($technos,$pos-1,1)!="U" 
		   		&& substr($technos,$pos-3,3)!="LTE" && substr($technos,$pos-1,1)!="L")
		   		 or $pos==0){
				$G18="G18+";
		   	}
			$start = $pos+1;
		}
	}
	if(strpos($technos, "UMTS900")!==false or strpos($technos, "UMT900")!==false or strpos($technos, "U9")!==false or strpos($technos, "UMTS9")!==false){
		$U9="U9+";
	}
	if(strpos($technos, "UMTS2100")!==false or strpos($technos, "UMT2100")!==false or strpos($technos, "U21")!==false 
		or strpos($technos, "UMTS21")!==false or strpos($technos, "HSPX")!==false or strpos($technos, "HSDPA")!==false){
		$U21="U21+";
	}else{//FOR NB UMTS&
		$posUMTS=strpos($technos, "UMTS");
		if(substr($technos,$posUMTS+4,1)!="9" && $posUMTS!==false){
			$U21="U21+";
		}
	}
	if(strpos($technos, "LTE800")!==false or strpos($technos, "L8")!==false or strpos($technos, "LTE8")!==false){
		$L8="L8+";
	}
	if(strpos($technos, "LTE1800")!==false or strpos($technos, "L18")!==false or strpos($technos, "LTEX")!==false or strpos($technos, "LTE18")!==false){
		$L18="L18+";
	}
	if(strpos($technos, "LTE2600")!==false or strpos($technos, "L26")!==false or strpos($technos, "LTE26")!==false){
		$L26="L26+";
	}
	$technosNET1=substr($G9.$G18.$U9.$U21.$L8.$L18.$L26,0,-1);
	return $technosNET1;
}
?>
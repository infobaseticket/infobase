<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/phpmailer/class.phpmailer.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


if ($_POST['action']=="ins_upd_los"){

	if (!$_POST['SITEA'] || strlen(!$_POST['SITEA'])>8){
		$message.=  "You need to provide SITE A!<br>";

	}
	if (!$_POST['SITEB'] || strlen(!$_POST['SITEB'])>8){
		$message.=  "You need to provide SITE B!<br>";
	}

	if ($message){
		$res["responsedata"] = $message;
		$res["responsetype"]="error";
		$res["responseaction"]="update";
		echo json_encode($res);
	}else{
		$SITEID=$_POST['SITEA'];

		if ($_POST['losid']){
			$query = "UPDATE INFOBASE.BSDS_LINKINFO SET
			UPDATE_BY='".$guard_username."',
			UPDATE_DATE=SYSDATE,
			SITEA='".escape_sq($_POST['SITEA'])."',
			SITEB='".escape_sq($_POST['SITEB'])."',
	       	HEIGHTA='".escape_sq($_POST['HEIGHTA'])."',
			HEIGHTB='".escape_sq($_POST['HEIGHTB'])."',
			COMMENTSA='".escape_sq($_POST['COMMENTSA'])."',
			COMMENTSB='".escape_sq($_POST['COMMENTSB'])."',
			PRIORITY='".escape_sq($_POST['PRIORITY'])."',
			TYPE='".escape_sq($_POST['TYPE'])."',
			PARTNERVIEW='".$_POST['PARTNERVIEW']."'
			WHERE ID='".$_POST['losid']."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);

				$res["responsedata"] = "LOS ".$_POST['losid']." has succesfully been updated!";
				$res["responsetype"]="info";
				$res["responseaction"]="update";
				$res["siteID"]=$_POST['siteID'];
				echo json_encode($res);
			}
		}else{
			$query2="SELECT COUNT(ID) AS TOTAL FROM BSDS_LINKINFO WHERE upper(SITEA)='".strtoupper($_POST['SITEA'])."' AND upper(SITEB)='".strtoupper($_POST['SITEB'])."'";
			//echo $query2;
			$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
			if (!$stmt2) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt2);
				$TOT_amount_of_LOS=$res2['TOTAL'][0];
			}
			//echo $TOT_amount_of_LOS;
			if ($TOT_amount_of_LOS==0){
				//echo substr_count($_POST['TYPE'], 'Upgrade');
				$query = "INSERT INTO INFOBASE.BSDS_LINKINFO (
				   ID, CREATION_BY, CREATION_DATE,
				   SITEA, SITEB, COMMENTSA,
				   COMMENTSB,HEIGHTA,HEIGHTB, PRIORITY, PRIORITY_BY, PRIORITY_DATE, DONE, REPORT, RESULT, TYPE,PARTNERVIEW)
				VALUES ('' , '".$guard_username."', SYSDATE, '".$_POST['SITEA']."', '".$_POST['SITEB']."',
				'".escape_sq($_POST['COMMENTSA'])."', '".escape_sq($_POST['COMMENTSB'])."','".$_POST['HEIGHTA']."', '".$_POST['HEIGHTB']."','".$_POST['PRIORITY']."','".$guard_username."',
				SYSDATE,'NOT OK','NOT OK','NOT OK','".$_POST['TYPE']."','".$_POST['PARTNERVIEW']."')";
				//echo $query;
				$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);

					$query = "Select MAX(ID) as AMOUNT FROM INFOBASE.BSDS_LINKINFO";
					//echo $query."<br>";
					$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
					if (!$stmt) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					} else {
						OCIFreeStatement($stmt);
						$ID=$res1['AMOUNT'][0];
					}

					$res["responsedata"] = "New LOS has succesfully been created with id ".$ID."!";
					$res["responsetype"]="info";
					$res["responseaction"]="new";
					$res["siteID"]=$_POST['siteID'];
					echo json_encode($res);
				}
			}else{
				$res["responsedata"] = "LOS already existing!";
				$res["responsetype"]="warning";
				$res["responseaction"]="new";
				$res["siteID"]=$_POST['siteID'];
				echo json_encode($res);
			}
		}
		//echo $query;

	}
}

if ($_POST['action']=="reject_los"){

	$query = "UPDATE BSDS_LINKINFO SET RESULT ='NOT OK',RESULT_BY='".$guard_username."',RESULT_DATE=SYSDATE,
	REPORT='REJECTED',DONE='REJECTED', REJECT_REASON=REJECT_REASON || '   ' || '".escape_sq($_POST['reason'])."'
	WHERE ID='".trim($_POST['losid'])."'";
	//echo "$query";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		
				$mail             = new PHPMailer();
				$mail->IsSMTP(); // telling the class to use SMTP
				$mail->Host       = "Infobase"; // SMTP server
				$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
				                                           // 1 = errors and messages
				                                           // 2 = messages only
				$mail->SMTPAuth   = true;                  // enable SMTP authentication
				$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
				$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
				$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
				$mail->Username   = "infobaseticket@gmail.com";  // GMAIL username
				$mail->Password   = "Genie-456";            // GMAIL password
				$mail->AddEmbeddedImage('../../images/basecompany.png', 'logo_2u');

				$userdetails_Sender=getuserdata($guard_username);
				$fullname_sender=$userdetails_Sender['fullname'];
				$email_sender=$userdetails_Sender['email'];

				$mail->SetFrom($email_sender, 'Infobase');
				//$mail->AddReplyTo($email_sender,$fullname_sender);
				$mail->Subject    = " LOS ".$_POST['losid']." has been rejected";
				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
				$text_body = "Hi ".$_POST['partner'].",<br>";
			   	$text_body .= "The LOS with ID ".$_POST['losid']." has been REJECTED!<br><br>";
			   	$text_body .= "This has been done by <a href='mailto:'".$email_sender."'>".$fullname_sender."</a><br>";
			   	$text_body .= "<u>Reason:</u><br>".escape_sq($_POST['reason'])."<br><br>";
			   	$text_body .= "http://infobase/bsds/index.php<br><br>";
			   	$text_body .= "<br><br>For Citrix users: please start Infobase via citrix and copy this link in the Infobase window.<br><br>";
			   	$text_body .= "Rgds,<br>From Frederick Eyland for Infobase<br><br>";
			   	$text_body .= "<img src='cid:logo_2u' width='100px' height='52px'>";
				$mail->Body = $text_body;
				$mail->MsgHTML($text_body);
				
				if ($_POST['partner']=="ALU"){
					$mail->AddAddress("olivier.vincke@alcatel-lucent.com","olivier.vincke@alcatel-lucent.com");
					$mail->AddAddress("dirk.wouters@alcatel-lucent.com","dirk.wouters@alcatel-lucent.com");
					$mail->AddAddress("jeroen.verhaeren@alcatel-lucent.com","jeroen.verhaeren@alcatel-lucent.com");
				}else if ($_POST['partner']=="BENCHMARK"){
					$mail->AddAddress("steven.nagels@benchmark-telecom.eu");
					$mail->AddAddress("ariane.traweels@benchmark-telecom.eu");
				}else{
					$mail->AddAddress($email_sender,$fullname_sender);
				}	

		if(!$mail->Send()){
			$res["responsedata"] = "There has been a mail error! Please contact Infobase admin.". $mail->ErrorInfo;
			$res["responsetype"]="error";
		}else{
			$res["responsedata"] = "Reason has been updated for LOS with id ".$_POST['losid']."!";
			$res["responsetype"]="info";
		}
	}	
	$res["rmessage"] = "Reason has been updated for LOS with id ".$_POST['losid']."!";
	$res["rtype"]="info";
	$res["losid"]= $_POST['losid'];
	echo json_encode($res);
}
if ($_POST['action']=="change_status"){
	$query = "UPDATE BSDS_LINKINFO SET ".$_POST['field']." ='".$_POST['value']."',
	".$_POST['field']."_DATE=SYSDATE,".$_POST['field']."_BY='".$guard_username."' WHERE ID='".$_POST['pk']."'";
	//echo "$query";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$res["rmessage"] = "The field '".$_POST['field']."' with LOS ID ".$_POST['pk']." has been succesfully updated!";
		$res["rtype"]="info";
		$res["id"]=$_POST['pk'];
		echo json_encode($res);
	}
	
}

if ($_POST['action']=="reopen_los"){
	$query="INSERT INTO BSDS_LINKINFO (CREATION_BY,CREATION_DATE,SITEA,SITEB,PRIORITY,
	PRIORITY_BY,PRIORITY_DATE,COMMENTSA,COMMENTSB,HEIGHTA,HEIGHTB,TYPE,PARTNERVIEW,
	DONE, REPORT, RESULT 
	) SELECT '".$guard_username."' AS CREATION_BY, SYSDATE AS CREATION_DATE,
	SITEA,SITEB,PRIORITY,'".$guard_username."' AS PRIORITY_BY, SYSDATE AS PRIORITY_DATE,
	COMMENTSA,COMMENTSB,HEIGHTA,HEIGHTB,TYPE, 'NOT ASSIGNED' as PARTNERVIEW, 
	'NOT OK' AS DONE, 'NOT OK' AS REPORT, 'NOT OK' AS RESULT FROM BSDS_LINKINFO WHERE ID='".$_POST['losid']."'";
	//echo "$query";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
	}

	$query="select SEQ_LINKINFO_COUNT.currval from dual";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$CURVAL=$res1['CURRVAL'][0];
	}
	if ($CURVAL!=""){

		$query="INSERT INTO BSDS_LINKINFO_DETAILS_A (LOSID,CREATION_BY,CREATION_DATE,MINHEIGHT,
			OBSTR_TYPE,OBSTR_DISTANCE,SURVEYOR_COMMENTS,SKETCH,PHOTO,SITESHARE,
		LOCATION ,PANORAMIC,COORDINATES,SURVEYOR_NAME,SURVEY_DATE
		) SELECT '".$CURVAL."' AS LOSID,'".$guard_username."' AS CREATION_BY, SYSDATE AS CREATION_DATE,
		MINHEIGHT,
			OBSTR_TYPE,OBSTR_DISTANCE,SURVEYOR_COMMENTS,SKETCH,PHOTO,SITESHARE,
		LOCATION ,PANORAMIC,COORDINATES,SURVEYOR_NAME,SURVEY_DATE FROM BSDS_LINKINFO_DETAILS_A WHERE LOSID='".$CURVAL."'";
		//echo "$query";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}

		$query="INSERT INTO BSDS_LINKINFO_DETAILS_B (LOSID,CREATION_BY,CREATION_DATE,MINHEIGHT,
			OBSTR_TYPE,OBSTR_DISTANCE,SURVEYOR_COMMENTS,SKETCH,PHOTO,SITESHARE,
		LOCATION ,PANORAMIC,COORDINATES,SURVEYOR_NAME,SURVEY_DATE
		) SELECT '".$CURVAL."' AS LOSID,'".$guard_username."' AS CREATION_BY, SYSDATE AS CREATION_DATE,
		MINHEIGHT,
			OBSTR_TYPE,OBSTR_DISTANCE,SURVEYOR_COMMENTS,SKETCH,PHOTO,SITESHARE,
		LOCATION ,PANORAMIC,COORDINATES,SURVEYOR_NAME,SURVEY_DATE FROM BSDS_LINKINFO_DETAILS_B WHERE LOSID='".$CURVAL."'";
		//echo "$query";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);	
		}
 	}

	$message=  "LOS ".$_POST['id']." has succesfully been reopened to ID ".$CURVAL."!";
	echo $message;

}

if ($_POST['action']=="delete_los"){

	$query = "UPDATE BSDS_LINKINFO SET DELETED='yes',DELETED_BY='".$guard_username."',DELETED_DATE=SYSDATE WHERE ID='".$_POST['losid']."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "LOS ".$_POST['losid']." has succesfully been deleted!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}
if ($_POST['action']=="undelete_los"){

	$query = "UPDATE BSDS_LINKINFO SET DELETED='no',DELETED_BY='',DELETED_DATE='' WHERE ID='".$_POST['losid']."'";
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "LOS ".$_POST['losid']." has succesfully been UNdeleted!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}

//echo $_POST['action'];
if ($_POST['action']=="update_los_details"){
  	$query = "Select LOSID FROM INFOBASE.BSDS_LINKINFO_DETAILS_".$_POST['end']." WHERE LOSID ='".$_POST['losid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_LOS=count($res1['LOSID']);
	}

	if ($amount_of_LOS==1){
		$SURVEYOR_COMMENTS=escape_sq($_POST['SURVEYOR_COMMENTS']);
	//echo $_POST['AREA_900'];
		$query = "
		UPDATE INFOBASE.BSDS_LINKINFO_DETAILS_".$_POST['end']."
		SET    UPDATE_BY         = '".$guard_username."',
		       UPDATE_DATE       = SYSDATE,
		       MINHEIGHT         = '".$_POST['MINHEIGHT']."',
		       OBSTR_TYPE        = '".$_POST['OBSTR_TYPE']."',
		       OBSTR_DISTANCE    = '".$_POST['OBSTR_DISTANCE']."',
		       SURVEYOR_COMMENTS = '".$_POST['SURVEYOR_COMMENTS']."',
		       SKETCH            = '".$_POST['SKETCH']."',
		       PHOTO             = '".$_POST['PHOTO']."',
		       SITESHARE         = '".$_POST['SITESHARE']."',
		       LOCATION          = '".$_POST['LOCATION']."',
		       PANORAMIC         = '".$_POST['PANORAMIC']."',
		       COORDINATES       = '".$_POST['COORDINATES']."',
		       SURVEYOR_NAME     = '".$_POST['SURVEYOR_NAME']."',
		       SURVEY_DATE       = '".$_POST['SURVEY_DATE']."'
		WHERE LOSID='".$_POST['losid']."'";
	}else{
	   $query = "INSERT INTO INFOBASE.BSDS_LINKINFO_DETAILS_".$_POST['end']." (
	   LOSID, CREATION_BY, CREATION_DATE,
	   MINHEIGHT,  OBSTR_TYPE, OBSTR_DISTANCE, SURVEYOR_COMMENTS,
	   SKETCH, PHOTO, SITESHARE,  LOCATION, PANORAMIC, COORDINATES,
	   SURVEYOR_NAME, SURVEY_DATE)
	  VALUES ('".$_POST['losid']."', '".$guard_username."', SYSDATE,
		'".$_POST['MINHEIGHT']."', '".$_POST['OBSTR_TYPE']."', '".$_POST['OBSTR_DISTANCE']."',
	    '".$_POST['SURVEYOR_COMMENTS']."',  '".$_POST['SKETCH']."',  '".$_POST['PHOTO']."',
	    '".$_POST['SITESHARE']."',  '".$_POST['LOCATION']."', 	'".$_POST['PANORAMIC']."',
		'".$_POST['COORDINATES']."','".$_POST['SURVEYOR_NAME']."', '".$_POST['SURVEY_DATE']."')";
	}
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		OCICommit($conn_Infobase);
		$message=  "LOS report ".$_POST['end']."-end has succesfully been updated!";
		$res["responsedata"] = $message;
		$res["responsetype"]="info";
		echo json_encode($res);
	}
}

if ($_POST['action']=="delete_losfile"){
	unlink($_POST["losfile"]);
}


?>
<?
$_SESSION['table_view']=$_POST['viewtype'];
$_SESSION['BSDS_BOB_REFRESH']=$_POST['BSDS_BOB_date'];
$_SESSION['BSDSKEY']=$_POST['BSDSKEY'];
$type=$_POST['type'];

$query = "select * from BSDS_MATERIAL WHERE BSDSKEY ='".$_SESSION['BSDSKEY']."' AND BSDS_BOB_REFRESH='".$_SESSION['BSDS_BOB_REFRESH']."'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
	exit;
} else {
	OCIFreeStatement($stmt);
}
$count=count($res1['BSDSKEY']);
if ($count==0){	

	include($config['sitepath_abs']."/bsds/scripts/procedures/planned_stuf.php");
	
	/*
	echo 'pl_BBS= '.$pl_BBS."<br>";
	echo 'pl_CABTYPE= '.$pl_CABTYPE."<br>";
	echo 'pl_CABTYPE= '.$pl_CABTYPE."<br>";
	echo 'pl_ANTTYPE1_1= '.$pl_ANTTYPE1_1."<br>";
	echo 'pl_ANTTYPE1_2= '.$pl_ANTTYPE1_2."<br>";
	echo 'pl_ANTTYPE1_3= '.$pl_ANTTYPE1_3."<br>";
	echo 'pl_ANTTYPE1_3= '.$pl_ANTTYPE1_4."<br>";
	
	echo 'pl_ANTTYPE2_1= '.$pl_ANTTYPE2_1."<br>";
	echo 'pl_ANTTYPE2_2= '.$pl_ANTTYPE2_2."<br>";
	echo 'pl_ANTTYPE2_3= '.$pl_ANTTYPE2_3."<br>";
	echo 'pl_ANTTYPE2_4= '.$pl_ANTTYPE2_4."<br>";
	*/
	
	if ($pl_BBS!="NONE" && $pl_BBS!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='BBS' AND DESCRIPTION='$pl_BBS' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='BBS' AND DESCRIPTION='$pl_BBS' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		
		$count=count($res1['DESCRIPTION']);
	
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=1){				
				$BBS_ID=$res1['ID'][0];				
				$BBS_check="OK";
			}else{
				$BBS_check="NOK";		
			}
		}else{
			$BBS_check="NOK";	
		}	
	}else{
		$BBS_check="NA";
	}
	
	if ($pl_CABTYPE!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='CAB' AND DESCRIPTION='$pl_CABTYPE' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='CAB' AND DESCRIPTION='$pl_CABTYPE' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		$count=count($res1['DESCRIPTION']);
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=1){
				$CAB_ID=$res1['ID'][0];
				$CAB_check="OK";
			}else{
				$CAB_check="NOK";		
			}
		}else{
			$CAB_check="NOK";	
		}	
	}else{
		$CAB_check="NA";
	}
	
	if ($pl_ANTTYPE1_1!="Unknown" && $pl_ANTTYPE1_1!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE1_1' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE1_1' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		$count=count($res1['DESCRIPTION']);
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=3){
				$ant1_1_ID=$res1['ID'][0];
				$ant1_1_check="OK";
			}else{
				$ant1_1_check="NOK";		
			}
		}else{
			$ant1_1_check="NOK";	
		}
	}else{
		$ant1_1_check="NA";
	}
	
	if ($pl_ANTTYPE1_2!="Unknown" && $pl_ANTTYPE1_2!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE1_2' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE1_2' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		$count=count($res1['DESCRIPTION']);
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=3){
				$ant1_2_ID=$res1['ID'][0];
				$ant1_2_check="OK";
			}else{
				$ant1_2_check="NOK";		
			}
		}else{
			$ant1_2_check="NOK";	
		}
	}else{
		$ant1_2_check="NA";
	}
	
	if ($pl_ANTTYPE1_3!="Unknown" && $pl_ANTTYPE1_3!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE1_3' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE1_3' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		$count=count($res1['DESCRIPTION']);
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=3){
				$ant1_3_ID=$res1['ID'][0];
				$ant1_3_check="OK";
			}else{
				$ant1_3_check="NOK";		
			}
		}else{
			$ant1_3_check="NOK";	
		}
	}else{
		$ant1_3_check="NA";
	}
	
	if ($pl_ANTTYPE1_4!="Unknown" && $pl_ANTTYPE1_4!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE1_4' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE1_4' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		$count=count($res1['DESCRIPTION']);
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=3){
				$ant1_4_ID=$res1['ID'][0];
				$ant1_4_check="OK";
			}else{
				$ant1_4_check="NOK";		
			}
		}else{
			$ant1_4_check="NOK";	
		}
	}else{
		$ant1_4_check="NA";
	}
	
	if ($pl_ANTTYPE2_1!="Unknown" && $pl_ANTTYPE2_1!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE2_1' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE2_1' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		$count=count($res1['DESCRIPTION']);
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=3){
				$ant2_1_ID=$res1['ID'][0];
				$ant2_1_check="OK";
			}else{
				$ant2_1_check="NOK";		
			}
		}else{
			$ant2_1_check="NOK";	
		}
	}else{
		$ant2_1_check="NA";
	}
	
	if ($pl_ANTTYPE2_2!="Unknown" && $pl_ANTTYPE2_2!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE2_2' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE2_2' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		$count=count($res1['DESCRIPTION']);
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=3){
				$ant2_2_ID=$res1['ID'][0];
				$ant2_2_check="OK";
			}else{
				$ant2_2_check="NOK";		
			}
		}else{
			$ant2_2_check="NOK";	
		}
	}else{
		$ant2_2_check="NA";
	}
	
	if ($pl_ANTTYPE2_3!="Unknown" && $pl_ANTTYPE2_3!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE2_3' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='CAB' AND DESCRIPTION='$pl_ANTTYPE2_3' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		$count=count($res1['DESCRIPTION']);
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=3){
				$ant2_3_ID=$res1['ID'][0];
				$ant2_3_check="OK";
			}else{
				$ant2_3_check="NOK";		
			}
		}else{
			$ant2_3_check="NOK";	
		}
	}else{
		$ant2_3_check="NA";
	}
	
	if ($pl_ANTTYPE2_4!="Unknown" && $pl_ANTTYPE2_4!=""){		
		$query = "select * from BSDS_MATERIAL_AVAILABILITY WHERE type ='antenna' AND DESCRIPTION='$pl_ANTTYPE2_4' AND UPDATE_DATE=(SELECT MIN(update_date) from BSDS_MATERIAL_AVAILABILITY WHERE type ='CAB' AND DESCRIPTION='$pl_ANTTYPE2_4' AND STILL_AVAILABLE>=1) ORDER BY DESCRIPTION";
		//echo "$query<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, &$error_str, &$res1);
		if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
		} else {
		OCIFreeStatement($stmt);
		}
		$count=count($res1['DESCRIPTION']);
		if ($count==1){
			if ($res1['STILL_AVAILABLE'][0]>=3){
				$ant2_4_ID=$res1['ID'][0];
				$ant2_4_check="OK";
			}else{
				$ant2_4_check="NOK";		
			}
		}else{
			$ant2_4_check="NOK";	
		}
	}else{
		$ant2_4_check="NA";
	}
	
	//echo "<br>$BBS_check -- $CAB_check | $ant1_1_check -- $ant1_2_check -- $ant1_3_check -- $ant1_4_check | $ant2_1_check -- $ant2_2_check -- $ant2_3_check -- $ant2_4_check<br>";
	
	if(($BBS_check=="OK" || $BBS_check=="NA")
	&& ($CAB_check=="OK" || $CAB_check=="NA")
	&& ($ant1_1_check=="OK" || $ant1_1_check=="NA")
	&& ($ant1_2_check=="OK" || $ant1_2_check=="NA") 
	&& ($ant1_3_check=="OK" || $ant1_3_check=="NA") 
	&& ($ant1_4_check=="OK" || $ant1_4_check=="NA") 
	&& ($ant2_1_check=="OK" || $ant2_1_check=="NA") 
	&& ($ant2_2_check=="OK" || $ant2_2_check=="NA") 
	&& ($ant2_3_check=="OK" || $ant2_3_check=="NA") 
	&& ($ant2_4_check=="OK" || $ant2_4_check=="NA")){
		
		if($BBS_check=="OK"){
			
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$BBS_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}	
			
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$BBS_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message="Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
		
		if($CAB_check=="OK"){
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$CAB_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}	
				
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$CAB_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message="Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
		
		if($ant1_1_check=="OK"){
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$ant1_1_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}	
				
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$ant1_1_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message="Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
		if($ant1_2_check=="OK"){
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$ant1_2_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
				
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$ant1_2_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message="Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
		if($ant1_3_check=="OK"){
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$ant1_3_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
				
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$ant1_3_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message="Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
		if($ant1_4_check=="OK"){
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$ant1_4_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
				
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$ant1_4_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message="Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
		if($ant2_1_check=="OK"){
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$ant2_1_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
			
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$ant2_1_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message="Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
		if($ant2_2_check=="OK"){
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$ant2_2_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
				
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$ant2_2_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message="Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
		if($ant2_3_check=="OK"){
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$ant2_3_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
				
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$ant2_3_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				$message="Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
		if($ant2_4_check=="OK"){
			$query="UPDATE BSDS_MATERIAL_AVAILABILITY SET STILL_AVAILABLE=STILL_AVAILABLE-1 WHERE ID='".$ant2_4_ID."'";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
			}
				
			$query="INSERT INTO BSDS_MATERIAL VALUES ('','".$_SESSION['BSDSKEY']."','".$_SESSION['BSDS_BOB_REFRESH']."','".$ant2_4_ID."')";
			//echo $query;
			$stmt = parse_exec_free($conn_Infobase, $query, &$error_str);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			}else{
				echo "Material has been reserved!!!<br>";
			}
			OCICommit($conn_Infobase);
		}
			  ?>
			  <script>
			  $(document).ready(function(){
			       $("#information")
		          .fadeIn('slow')
		          .animate({opacity: 1.0}, 8000)
				  });
			</script>
			<?
		$acceptance="OK";
	}else{
		$message_BSDSstatus = "BSDS can not be accepted because not enough material available!!<br>Please contact Q&A.";
		$acceptance="NOK";
		 ?>
		  <script>
		  $(document).ready(function(){
		       $("#warning")
	          .fadeIn('slow')
	          .animate({opacity: 1.0}, 8000)
	          .empty()
	          .append('<?=$message_BSDSstatus?>')
	          .fadeOut('slow');
	          
	          $("#TEAML_FUNDED").selectOptions("Pending"); //or $("#TEAML_FUNDED").val("Pending");

			  });
		  </script>
		<?
	}	
}else{
	$message="Material has already been reserved for this BSDS ID!<br>";
	?>
		  <script>
		  $(document).ready(function(){
		       $("#warning")
	          .fadeIn('slow')
	          .animate({opacity: 1.0}, 8000)
	          .empty()
	          .append('<?=$message?>')
	          .fadeOut('slow');
			  });
		  </script>
	<?
	$acceptance="OK";	
}

echo $message;
?>
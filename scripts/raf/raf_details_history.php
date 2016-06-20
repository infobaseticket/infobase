<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
?>

<?php
if ($_POST['rafid']){
	?>
	<table class="table table-striped table-condensed">
	<thead>
		<tr>
			<th width="130px">ACTION BY</th>
			<th width="180px">ACTION DATE</th>
			<th width="140px">FIELD</th>
			<th>CHANGE TO</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$query = "Select * FROM BSDS_RAF_HISTORY WHERE RAFID = '".$_POST['rafid']."' order BY ACTION_DATE DESC";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}
	for ($i = 0; $i <$amount_of_RAFS; $i++) {  
		if($res1['ACTION_BY'][$i]=="IB"){
			$userfull="Infobase";
		}else{
			$user=getuserdata($res1['ACTION_BY'][$i]);
			$userfull=$user['fullname'];
		}
		
		echo '<tr><td>'.$userfull.'</td><td>'.$res1['ACTION_DATE'][$i]."</td><td>".$res1['FIELD'][$i]."</td><td>".$res1['STATUS'][$i]."</td></tr>";
	}
	?>
	</tbody>
	</table>
	<?php
	/*
	$query = "Select * FROM BSDS_RAF_LOG WHERE RAFID = '".$_POST['rafid']."' order BY QUERYDATE";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_RAFS=count($res1['RAFID']);
	}
	for ($i = 0; $i <$amount_of_RAFS; $i++) {  
		$user=getuserdata($res1['USERNAME'][$i]);
		echo '<span class="label label-default">'.$user['fullname'].' '.$res1['QUERYDATE'][$i]."</span><br>
		".$res1['QUERY'][$i]."<hr>";
	}
*/
}else{
	echo '<span class="label label-danger">You did not provide an RAFID!</span>';
}
?>
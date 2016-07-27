<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
?>


<table class="table table-striped table-condensed">
<thead>
	<tr>
		<th>ACTION BY</th>
		<th>ELEMENT</th>
		<th>UPGNR</th>		
		<th>CODE</th>
		<th>ACTION DATE</th>
		<th>CHANGE TO DATE</th>
		<th>PLANNED</th>
		<th>FILENAME PARTNER</th>
	</tr>
</thead>
<tbody>
<?php

if ($_POST['nbup']=="NB"){
	$query = "Select * FROM NET1UPDATER_LOG WHERE ELEMENT = '".$_POST['siteupgnr']."' AND UPGNR IS NULL order BY DATUM DESC";
}else{
	$query = "Select * FROM NET1UPDATER_LOG WHERE UPGNR='".$_POST['siteupgnr']."' order BY DATUM DESC";
}

$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_LOGS=count($res1['ELEMENT']);
}
for ($i = 0; $i <$amount_of_LOGS; $i++) {  
	if($res1['UPDATE_BY'][$i]==""){
		$userfull="Import from Matrix";
	}else{
		$user=getuserdata($res1['UPDATE_BY'][$i]);
		$userfull=$user['fullname'];
	}

	echo "<tr><td>".$userfull."</td><td>".$res1['ELEMENT'][$i]."</td><td>".$res1['UPGNR'][$i]."</td><td>".$res1['CODE'][$i]."</td><td>".$res1['DATUM'][$i]."</td><td>".$res1['TODATE'][$i]."</td><td>".$res1['PLANNED'][$i]."</td><td>".$res1['FILENAME'][$i]."</td></tr>";
}
?>
</tbody>
</table>

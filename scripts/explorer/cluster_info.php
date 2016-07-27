<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
?>
<table class='table'>
<th>SITEID</th>
<th>RAFID</th>
<?php
if ($_POST['cluster']!=""){
	$query = "Select SITEID, t1.RAFID as RAFID FROM BSDS_RAF_RADIO t1 LEFT JOIN BSDS_RAFV2 t2 on t1.RAFID=t2.RAFID WHERE CLUSTERN = '".$_POST['cluster']."'";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of=count($res1['SITEID']);
	}
	for ($i = 0; $i <$amount_of; $i++) {  
		
		echo "<tr><td>".$res1['SITEID'][$i]."</td><td>".$res1['RAFID'][$i]."</td></tr>";
	}
}
?>

</table>

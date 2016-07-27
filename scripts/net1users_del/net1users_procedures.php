<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$query="select * from USERS_ROLES@".$config['net1db']." a LEFT JOIN USERS@".$config['net1db']." b 
on b.USE_ID=a.URO_USE_ID WHERE USE_ENABLED='Y'";
if ($_POST['role']!='All'){
	$query.=" AND URO_LKP_ROL_CODE='".$_POST['role']."'";
}
$query.="ORDER BY USE_NAME ASC";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_NET1=count($res1['USE_NAME']);
}

?>
<br><br>
<span class="label label-default"><?=$amount_of_NET1?> users found</span>
<table class="table table-striped table-condensed">
	<thead>
		<th>ROLE</th>
		<th>NAME</th>
		<th>LOGIN</th>
		<th>ACTIVE</th>
	</thead>
	<tbody>
<?
for ($i=0;$i<$amount_of_NET1;$i++){
?>
	<tr>
		<td><?=$res1['URO_LKP_ROL_CODE'][$i]?></td>
		<td><?=$res1['USE_NAME'][$i]?></td>
		<td><?=$res1['USE_LOGIN'][$i]?></td>
		<td><?=$res1['USE_ENABLED'][$i]?></td>
	</tr>
<?php
}
?>
	</tbody>
</table>
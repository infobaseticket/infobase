<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$query="select * from USERS_ROLES@".$config['net1db']." a LEFT JOIN USERS@".$config['net1db']." b 
on b.USE_ID=a.URO_USE_ID WHERE USE_ENABLED='Y'
ORDER BY USE_NAME ASC";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_NET1=count($res1['USE_NAME']);
}
$query2="select DISTINCT(URO_LKP_ROL_CODE) as ROLE from USERS_ROLES@".$config['net1db']." 
ORDER BY URO_LKP_ROL_CODE";
$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
if (!$stmt2) {
	die_silently($conn_Infobase, $error_str);
	exit;
} else {
	OCIFreeStatement($stmt2);
	$amount_of_NET1ROLES=count($res2['ROLE']);
}
?>
<div class="panel panel-primary" id="net1userlist">
    <div class="panel-heading">
        <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#userlistfilter">
            SET FILTER <span class="glyphicon glyphicon-eye-open"></span>
        </a>
        </h4>
    </div>
    <div id="userlistfilter" class="panel-collapse collapse in">
        <div class="panel-body">
	        <form action="scripts/net1users/net1users_procedures.php" name="fm" method="post" id="Net1UsersForm" role="form" class="form-horizontal" role="form">
				<div class="form-group">
					<label for="type" class="col-sm-5 control-label">Select role</label>
					<div class="col-sm-7">
						<select name="role" id="type" class="form-control col-sm-10">
							<option>All</option>
							<?
							for ($k=0;$k<$amount_of_NET1ROLES;$k++){
								echo "<option>".$res2['ROLE'][$k]."</option>";
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-7 col-md-offset-5">		
					<input type="submit" value="DISPLAY" id="Net1USerListform" class="btn btn-primary">
					<br><br>
				</div>
			</form>
		</div>
	</div>
</div>
<br>

<div id="reportoutput">
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
</div>
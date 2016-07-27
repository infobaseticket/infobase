<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<script language="JavaScript">
$(document).ready(function() {
	function after_RAFdetails_save(response)  {  
		$('#modalspinner').spin(false);
		Messenger().post({
			message: response.responsedata,
			type: response.responsetype,
			showCloseButton: true
		});
	}	
	var options = {
		success: after_RAFdetails_save,
		dataType:  'json'
	};
	$('#form_trx').submit(function() { 
		$('#modalspinner').spin('medium');
	    $(this).ajaxSubmit(options); 
	    return false; 
	});
});
</script>
<?php	
$query = "Select NET1_FUND FROM BSDS_RAFV2 WHERE RAFID = '".$_POST['rafid']."'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$STATUS_FUND=$res1['STATUS_FUND'][0];
}

$query = "Select * FROM BSDS_RAF_TRX WHERE RAFID ='".$_POST['rafid']."' ORDER BY DATE_OF_SAVE DESC";
$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
if (!$stmt2) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt2);
	$amount_trx_data=count($res2['RAFID']);
}
?>
<table class="table">
<?php
for ($i = 0; $i <$amount_trx_data; $i++) {
	$DATE_OF_SAVE=$res2['DATE_OF_SAVE'][$i];
	$REQUIREMENTS=$res2['REQUIREMENTS'][$i];
	$UPDATE_BY=$res2['UPDATE_BY'][$i];
	?>
	<tr><td><?=$UPDATE_BY?></td><td> <?=$DATE_OF_SAVE?></td><td><?=$REQUIREMENTS?></td></tr>
	<?
}
?>
</table>
<br>
<form action="scripts/raf/raf_actions.php" method="post" id="form_trx" role="form">
<input type="hidden" name="action" value="update_trx_raf">
<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
<div class="form-group">
	<label for="RFPLAN" class="control-label">CAPACITY REQUIREMENTS</label>
	<textarea class="form-control input-sm" rows="5" name="REQUIREMENTS" id="REQUIREMENTS"></textarea>
</div>
<?
if ((substr_count($guard_groups, 'Base_RF')==1  && ($NET1_FUND=='NOT OK' or $NET1_FUND=='' or $NET1_FUND=='NA')) or substr_count($guard_groups, 'Admin')){ ?>
<br><input type='submit' class="btn btn-default" value='Save requirements'>
<? } ?>
</form>
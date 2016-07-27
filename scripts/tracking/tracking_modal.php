<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

	
$query = "select RAFID,NET1_LINK, TYPE FROM BSDS_RAFV2 WHERE SITEID = '".strtoupper($_POST['siteid'])."' ORDER BY RAFID";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmt);
}
$amount=count($res1['RAFID']);
foreach ($res1['RAFID'] as $key=>$attrib_id) {
    $options_raf.="<option value='".$res1['RAFID'][$key]."'>RAF ".$res1['RAFID'][$key]." (".$res1['NET1_LINK'][$key]." ".$res1['TYPE'][$key].")</option>";
}
?>

<h3>Add comment for RAF <?=$_POST['siteid']?></h3>
	
<form role="form" action="scripts/tracking/tracking_actions.php" method="post" id="addTrackingForm<?=$_POST['siteid']?>">
<input type="hidden" name="siteid" value="<?=$_POST['siteid']?>">
<input type="hidden" name="action" value="insertComment">
<div class="form-group">
    <label for="rafidInput">RAF ID</label>
    <select name="rafid" id="rafidInput" class="form-control">
	<option value=''>Select RAFID</option>
	<?php echo $options_raf; ?>
	</select>
</div>
<div class="form-group">
    <label for="commInput">Comments</label>
    <textarea name="comments" class="form-control" id="commInput" rows="3"></textarea>
</div>
</form>

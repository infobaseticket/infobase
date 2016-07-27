<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery","");

if ($_POST['rafid']!=""){
	if ($_POST['action']=="delete_raf"){
		$status="deletion";
	}else if ($_POST['action']=="lock_raf"){
		$status="locking";
	}
?>
<form action="scripts/raf/raf_actions.php" method="post" id="del_raf_form" role="form">
<input type="hidden" name="action" value="<?=$_POST['action']?>">
<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
<input type="hidden" name="siteID" value="<?=$_POST['siteID']?>">
<input type="hidden" name="net1link" value="<?=$_POST['net1link']?>">
<div class="form-group">
    <label for="delreason">Reason for <?=$status?>:</label>
    <textarea name="delreason" class="form-control" id="delreason" rows="9" placeholder="Enter reason"></textarea>
  </div>
</form>
<?php } ?>
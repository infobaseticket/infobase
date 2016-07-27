<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
?>
Pleasee provide a reason why you reject the Shipping Notification <?=$_POST['SN_ID']?>:<br>
<form action='scripts/shipping_notification/sn_actions.php' method='post' role="form" id='SN_Reject_form<?=$_POST['SN_ID']?>'>
<input type='hidden' name='action' value='logisticsREJECT'>
<input type='hidden' name='SN_ID' value='<?=$_POST['SN_ID']?>'>
<input type='hidden' name='RAFID' value='<?=$_POST['RAFID']?>'>
<textarea name='SNRejectReason' class="form-control" rows="10" id="SNRejectReason"></textarea>
</form>
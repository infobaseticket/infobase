<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");

$user=getuserdata($_GET['previous_user']);

if($_GET['previous_user']){
?>
A rejection mail will be sent to <font color="orange">'<?=$user['firstname']?> <?=$user['lastname']?>'</font><br>
<?php
}
?>
Please be aware that this could take some time, so be patient untill you receive a success message!<br>

<form action='scripts/raf/raf_actions.php' method='post' role="form" id='Raf_Reject_form'>
<input type='hidden' name='action' value='change_net1link'>
<input type='hidden' name='id' value='<?=$_GET['rafid']?>'>
<input type='hidden' name='field' value='<?=$_GET['field']?>'>
<input type='hidden' name='previous_user' value='<?=$_GET['previous_user']?>'>
<input type='hidden' name='siteid' value='<?=$_GET['siteID']?>'>
<textarea name='value' class="form-control" rows="10" id="reason"></textarea>
</form>
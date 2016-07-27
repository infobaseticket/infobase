<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");

?>
A rejection mail will be sent to '<?=$_POST['partner']?>'<br>
Please be aware that this could take some time, so be patient untill you receive a success message!<br>
<form action='scripts/los/los_actions.php' method='post' id='Reject_form<?=$_POST['losid']?>'>
<input type='hidden' name='action' value='reject_los'>
<input type='hidden' name='losid' value=" <?=$_POST['losid']?>">
<input type='hidden' name='partner' value=" <?=$_POST['partner']?>">

Please provide reason and date of REJECTION for LOS <?=$_POST['losid']?>:<br>
<textarea name="reason" cols="55" rows="10"><? echo date('d-m-Y');?>:</textarea>
</form>
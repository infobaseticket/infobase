<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Alcatel,Alcatel_sub","");
?>

<script language="JavaScript">
$(document).ready(function() {
	function after_ReasonForm_save(response)  {
		if (response.responsetype === "info"){
				$(document.body).qtip('destroy');
				if (response.field === "STATUS"){
					$("#"+response.auditid+"_STATUS").html(response.status);
				}
				createGrowl(response.responsedata,false);
				$("#loadingbar"+$.session("tabid")).hide();
		}
	}

	var options = {
		success:  after_ReasonForm_save,
		dataType:  'json'
	};

	$('#Reject_form'+$.session("tabid")).submit(function() {
		$("#loadingbar"+$.session("tabid")).show('fast');
	    $(this).ajaxSubmit(options);
	    return false;
	});

});
</script>


<form action='scripts/audits/audit_actions.php' method='post' id='Reject_form<?=$_GET['tabid']?>'>
<input type='hidden' name='action' value='provideFailureReason'>
<input type='hidden' name='auditid' value='<?=$_GET['auditid']?>'>
<input type='hidden' name='field' value='<?=$_GET['field']?>'>

<? if($_GET['field']=="STATUSKPNGB"){ ?>
Please select reason:&nbsp;
<select name="reasonKPNGB">
<option>Remove PAC</option>
<option>Remove FAC</option>
<option>Remove PAC and FAC</option>
<option>Remove PAC + Request CN</option>
<option>Other</option>
</select><br>
<? }else{ ?>
Punches A: <select name="PUNCHA">
<? for($i=0;$i<=60;$i++){ ?>
<option><?=$i?></option>
<? } ?>
</select><br>
Punches B: <select name="PUNCHB">
<? for($i=0;$i<=60;$i++){ ?>
<option><?=$i?></option>
<? } ?>
</select><br>
Punches C: <select name="PUNCHC">
<? for($i=0;$i<=60;$i++){ ?>
<option><?=$i?></option>
<? } ?>
</select><br>
<? } ?>
Comments:<br>
<textarea name='failureReason' cols=30 rows=10></textarea>
<br><input type='submit' value='Save result'>
</form>
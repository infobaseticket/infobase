<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Alcatel,Alcatel_sub","");

?>
<link type="text/css" rel="stylesheet" href="<?=$config['sitepath_url']?>/include/javascripts/jquery/jwysiwyg/jwysiwyg/jquery.wysiwyg.css" />
<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jwysiwyg/jwysiwyg/jquery.wysiwyg.js"></script>

<script language="JavaScript">
$(document).ready(function() {
	function after_ReasonForm_save(response)  {
			$('#mainContent').unblock();
			$("#loading").hide();
	}
	function validate_ReasonForm(formData, jqForm, options){
		var form = jqForm[0];
	    if (form.failureReason.value==="") {
	        alert('You need to provide a reason for failure!');
	        return false;
	    } else{
			$("#loading").show('fast');
		}
	}
	var options = {
    success:  after_ReasonForm_save,
	dataType:  'json',
	beforeSubmit: validate_ReasonForm
	};

	$('#Reject_form').submit(function() {
	    $(this).ajaxSubmit(options);
	    return false;
	});

});

(function($)
{
  $('.wysiwyg').wysiwyg({
    controls: {
 	  insertImage: { visible : false },
	  separator05 : { separator : false },
	  createLink: { visible : false }
    }
  });
})(jQuery);

</script>




<form action='scripts/audits/audit_actions.php' method='post' id='Reject_form'>
<input type='hidden' name='action' value='provideFailureReason'>
<input type='hidden' name='id' value='<?=$_GET['auditID']?>'>
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
Comments:<br>
<? }else{ ?>
Please provide reason of failure <?=$_GET['auditID']?>:<br>
<? } ?>
<textarea name='failureReason' class="wysiwyg" cols=60 rows=10></textarea>
<br><input type='submit' value='Save reason'>

</form>
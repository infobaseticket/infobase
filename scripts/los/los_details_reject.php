<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
	<script language="JavaScript">
	$(document).ready( function(){

		function after_LOSdetails_save(response)  {
			$('#modalspinner').spin(false);
			var rmessage = response.rmessage;
			Messenger().post({
					  message: rmessage,
					  showCloseButton: true
					});
		}

		var options = {
		    success:  after_LOSdetails_save,
			dataType:  'json'
		};

		$('#los_rejection_form').submit(function(){
			$('#modalspinner').spin('small');
		    $(this).ajaxSubmit(options);
		    return false;
		});
	});
	</script>
<?
$query="SELECT REJECT_REASON,RESULT_BY,RESULT_DATE from BSDS_LINKINFO WHERE ID='".$_POST['losid']."'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$REJECT_REASON=$res1['REJECT_REASON'][0];
	$REJECT_BY=$res1['RESULT_BY'][0];
	$REJECT_DATE=$res1['RESULT_DATE'][0];

}
?>
<form role="form" action='scripts/los/los_actions.php' id="los_rejection_form" method="POST">
<input type="hidden" name="action" value="reject_los">
<input type="hidden" name="losid" value="<?=$_POST['losid']?>">
<textarea rows="10" cols="85" name="reason" class="form-control"><?=$REJECT_REASON?></textarea><br>
<button class="btn btn-primary" type="submit">Save changes</button>
</form>
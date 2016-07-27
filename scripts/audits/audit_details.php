<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_other","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("audit_procedures.php");

?>
<script language="javascript">
	$("#loadingbar"+$.session("tabid")).show('fast');
	$('#audit_files').load("scripts/audits/audit_files.php", {
		auditid:'<?=$_POST['auditid']?>'
	},function(){
		$("#loadingbar"+$.session("tabid")).hide();
	});

	$(".audit_uploadbutton").click(
	function(){
		id_all=$(this).attr("id");
		id = id_all.split('*');

   		$('#AuditUploadForm').ajaxSubmit({
	        beforeSubmit: function(a,f,o) {
	            o.dataType ="HTML";
	            $('#Audit_uploadOutput').html('<font color="blue"><b>Uploading...</b></font>');
	        },
	        success: function(data) {
	            var $out = $('#Audit_uploadOutput');
	            //$out.html('Form success handler received: <strong>' + typeof data + '</strong>');
	            if (typeof data == 'object' && data.nodeType)
	                data = elementToString(data.documentElement, true);
	            else if (typeof data == 'object')
	                data = objToString(data);
	            $out.html('<div><pre>'+ data +'</pre></div>');
	            $('#audit_files').load("scripts/audits/audit_files.php", {auditid:id[1]},
				function(){
					//$("#loading").hide();
				});
	        }
	    })
	});


</script>
<?
$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query=query_audit_comments($_POST['auditid']);
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_audits=count($res1['COMMENTS']);
}

if ($amount_of_audits=1){
	$COMMENTS= $res1['COMMENTS'][0];
	$REASON= get_reason($res1['REASON'][0]);
	$REASON_COMMENTS= $res1['REASON_COMMENTS'][0];
	$REASONKPNGB1= $res1['REASONKPNGB1'][0];
	$REASONKPNGB2= $res1['REASONKPNGB2'][0];
}
?>

<b>Selected AUDIT: <?=$_POST['auditid']?></b><br>
<u>Reason for inspection by KPNGB:</u><br><?=$REASON?><br><?=$REASON_COMMENTS?><br><br>
<u>Inspection Partner Comments:</u><br><?=$COMMENTS?><br><br>
<u>Audit status change reason by KPNGB:</u><br><?=$REASONKPNGB1?><br><?=$REASONKPNGB2?><br><br>

<a class="audit_attach" href="#"><span><b>Upload a file or image</b></span></a>
<br><br>
<div class="clear"></div>
<div id="audit_uploadform" style="display:none">
	<form id="AuditUploadForm" action="scripts/audits/file_upload.php" method="POST" enctype="multipart/form-data">
	    <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	    <input type="hidden" name="auditid" value="<?=$_POST['auditid']?>">
	    File: <input type="file" name="uploadedfile"><input type="button" value="UPLOAD" class="audit_uploadbutton" id="audtituplbut*<?=$_POST['auditid']?>">
	</form><br>
	<label>Output:</label>
	<div id="Audit_uploadOutput"></div>
	<br>
</div>

<div class="clear"></div>
<div id="audit_files"></div>

<div id="delete_auditfile" style="display:none; cursor: default">
<p><u>&dArr; DELETE FILE:</u></p>
<br>
<table width="100%" cellpadding="4px" cellspacing="4px">
<tr>
	<td>
	<table>
	<tr>
		<td colspan="2">Are you sure you wnat to delete this file from audit <?=$_POST['auditid']?>?</td>
	</tr>
	<tr>
		<td><input type="button" id="yes_delete_auditfile" value="Delete" />
        <input type="button" id="no_delete_auditfile" value="Cancel" /> </td>
	</tr>
	</table>
	</td>
</tr>
</table>
</div>
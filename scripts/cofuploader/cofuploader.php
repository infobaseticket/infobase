<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
?>
<h3><span class="label label-default">RAF COF UPLOADER</span></h3>

<link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>javascripts/fine-uploader/fine-uploader.min.css">


<h4>Add COF info by uploading from Excel file</h4>
<p class="well">Please make sure you use the template provided by Steven!<br>Once finished copy the list to a new exel file as Infobase cannot handle the VLOOKUPS.<br>

You can also upload the file by dropping it here from your desktop to your webbrowser (NOT VIA CITRIX!)<br>
<b>Be aware that this uploader is first deleting all ALL COF info per RAFID</b></p>
<div id="fine-uploader"></div>
<hr>
<div id="Commentsdata"></div>


<link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>javascripts/fine-uploader/fine-uploader.min.css">

<div id="fine-uploader"></div>

<div id="Cofsdata"></div>

	<script type="text/template" id="qq-template">
<div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
    <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
    </div>
    <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
        <span class="qq-upload-drop-area-text-selector"></span>
    </div>
    <div class="qq-upload-button-selector btn btn-primary">
        <div>Upload COF Exel</div>
    </div>
    <span class="qq-drop-processing-selector qq-drop-processing">
        <span>Processing dropped files...</span>
        <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
    </span>
    <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
        <li>
            <div class="qq-progress-bar-container-selector">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
            </div>
            <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
            <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
            <span class="qq-upload-file-selector qq-upload-file"></span>
            <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
            <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
            <span class="qq-upload-size-selector qq-upload-size"></span>
            <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
            <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
            <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
            <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
        </li>
    </ul>

    <dialog class="qq-alert-dialog-selector">
        <div class="qq-dialog-message-selector"></div>
        <div class="qq-dialog-buttons">
            <button type="button" class="qq-cancel-button-selector">Close</button>
        </div>
    </dialog>

    <dialog class="qq-confirm-dialog-selector">
        <div class="qq-dialog-message-selector"></div>
        <div class="qq-dialog-buttons">
            <button type="button" class="qq-cancel-button-selector">No</button>
            <button type="button" class="qq-ok-button-selector">Yes</button>
        </div>
    </dialog>

    <dialog class="qq-prompt-dialog-selector">
        <div class="qq-dialog-message-selector"></div>
        <input type="text">
        <div class="qq-dialog-buttons">
            <button type="button" class="qq-cancel-button-selector">Cancel</button>
            <button type="button" class="qq-ok-button-selector">Ok</button>
        </div>
    </dialog>
</div>
</script>

<script src="<?=$config['explorer_url']?>javascripts/fine-uploader/fine-uploader.min.js" type="text/javascript"></script>

<script language="javascript">
$(document).ready(function() {

	var folder="<?=$_SERVER['DOCUMENT_ROOT']?>/Uploads/COF/";
	var newfilename= "<?=$guard_username?>_<?=date('dmY_His')?>.xlsx";

	var uploader = new qq.FineUploader({
	    debug: false,
	    multiple: false,
	    element: document.getElementById("fine-uploader"),
	    request: {
	        endpoint: 'scripts/FineUploaderServer/endpoint.php',
	        params: {
			    folder: folder,
			    allowedextensions: 'xlsx,xls',
			    newfilename: newfilename
			}
		},
	    retry: {
	       enableAuto: false
	    },
	    callbacks: {
	        onComplete: function(id, name, response) {
	            if (response.success) {
	                $.ajax({
						type: "POST",
						url: 'scripts/cofuploader/cofuploader_actions.php',
						data: { 
							action:'importCofs',
							folder:folder,
							newfilename:newfilename
						},
						success : function(data){
							var response = $.parseJSON(data);
						    $('#spinner').spin(false);
						    $("#Cofsdata").html(response.output);
						},
						beforeSend: function ( xhr ) {
							$('#spinner').spin();
						}
					});
	            }
	        }
	    }
	});

 });
</script>
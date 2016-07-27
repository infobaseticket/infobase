<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$bobdate=str_replace(' ', '_',str_replace('/', '',str_replace(':', '', $_POST['bsdsbobrefresh'])));

$query = "SELECT N1_ALCON, SERVICE_PROVIDER, AU308 FROM MASTER_REPORT LEFT JOIN SN_SERVICEPROVIDERS ON N1_ALCON=SERVICE_PROVIDER WHERE IB_RAFID='".$_POST["rafid"]."'";
//echo $query.EOL;
$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
if (!$stmt3) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt3);
}

if ($res3['SERVICE_PROVIDER'][0]==""){
	echo '<div class="alert alert-danger" role="alert">You can not generate SN without the ALSAC (Partner SUBCO) being set in NET1</div>';
	die;
	/*
}else if ($res3['AU308'][0]!=""){
	echo '<div class="alert alert-danger" role="alert">Materials have already been received!</div>';
	die;*/
}else{

	$query="SELECT SN_ID, PARTNER FROM SN_SHIPPINGLIST WHERE BSDSKEY='".$_POST["bsdskey"]."' AND BSDSBOBREFRESH='".$_POST["bsdsbobrefresh"]."' AND RAFID='".$_POST["rafid"]."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
	    die_silently($conn_Infobase, $error_str);
	    exit;
	} else {
	    OCIFreeStatement($stmt);
	    $amount_of_SN=count($res1['SN_ID']);
	}
	if ($amount_of_SN==0){
		$query= "INSERT INTO SN_SHIPPINGLIST (BSDSBOBREFRESH, RAFID, BSDSKEY,PARTNER,SN_GENERATED,SITEID) 
		VALUES ('".$_POST['bsdsbobrefresh']."','".$_POST['rafid']."','".$_POST['bsdskey']."','NOT OK',0,'".$_POST['siteid']."')";
		//echo $query."<br>";
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
		  die_silently($conn_Infobase, $error_str);
		}else{
		  OCICommit($conn_Infobase);
		}

		$query="SELECT SN_ID, PARTNER FROM SN_SHIPPINGLIST WHERE BSDSKEY='".$_POST["bsdskey"]."' AND BSDSBOBREFRESH='".$_POST["bsdsbobrefresh"]."' AND RAFID='".$_POST["rafid"]."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt){
		    die_silently($conn_Infobase, $error_str);
		    exit;
		} else {
		    OCIFreeStatement($stmt);
		}
	}
		
	$SN_ID=$res1['SN_ID'][0];
	$PARTNER=$res1['PARTNER'][0];

	$query="SELECT DISTINCT(TEMPLATE) as TEMPLATE FROM SN_TEMPLATES";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
	    die_silently($conn_Infobase, $error_str);
	    exit;
	} else {
	    OCIFreeStatement($stmt);
	    $amount_of_tmplates=count($res1['TEMPLATE']);
	}

	?>
	<h3><span class="label label-default">SHIPPING NOTIFICATION <?=$SN_ID?></span></h3>

	<link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>javascripts/fine-uploader/fine-uploader.min.css">

	<?php if ($PARTNER!='OK'){ ?>
	
	<h4>Add products by uploading from template</h4>
	<div id="fine-uploader<?=$SN_ID?>"></div>
	<?php } ?>
	<h4>Add products to the shipping notification one by one</h4>
	<form class="form-inline" id="addSNForm<?=$SN_ID?>" action="scripts/shipping_notification/sn_actions.php">
	<input type="hidden" name="action" value="addSN">
	<input type="hidden" name="SN_ID" value="<?=$SN_ID?>">
	  <div class="form-group">
	    <label class="sr-only" for="products<?=$SN_ID?>">Product</label>
	    <input name="KPNGB_PROD_REF" type="text" class="form-control" style="width:500px;" id="products<?=$SN_ID?>" placeholder="Search a product">
	  </div>
	  <div class="form-group">
	    <label class="sr-only" for="amount<?=$SN_ID?>">Amount</label>
	    <input type="text" name="amount" class="form-control" id="amount<?=$SN_ID?>">
	  </div>
	  OR:
	   <div class="form-group">
	    <label class="sr-only" for="amount<?=$SN_ID?>">template</label>
	    <select name='template' class="form-control" id="template<?=$SN_ID?>">
	    	<option value=''>Select template</option>
	    	<?php for ($i = 0; $i < $amount_of_tmplates ; $i++){ ?>
	    	<option><?=$res1['TEMPLATE'][$i]?></option>
	    	<? } ?>
	    </select>
	  </div>
	  <button type="submit" class="btn btn-primary" id="addSNBtn<?=$SN_ID?>">Add to SN</button>
	</form>

	<hr>
	

	<div id="SNdata<?=$SN_ID?>">Loading....</div>

	<?php if ($PARTNER!='OK'){ ?>

	 	<script type="text/template" id="qq-template">
	        <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
	            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
	                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
	            </div>
	            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
	                <span class="qq-upload-drop-area-text-selector"></span>
	            </div>
	            <div class="qq-upload-button-selector btn btn-primary">
	                <div>Upload SN to server</div>
	            </div>
	            <span class="qq-drop-processing-selector qq-drop-processing">
	                <span>Processing dropped SN files...</span>
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
	<?php } ?>

	<script language="javascript">
	$(document).ready(function() {

		var folder="<?=$_SERVER['DOCUMENT_ROOT']?>/Uploads/SN/<?php echo substr($_POST['siteid'],0,2); ?>/";
		var newfilename= "<?=$_SN_ID?>_<?=$_POST['siteid']?>.xlsx";

		reloadTable();

	<?php if ($PARTNER!='OK'){ ?>
		var uploader = new qq.FineUploader({
		    debug: false,
		    multiple: false,
		    element: document.getElementById("fine-uploader<?=$SN_ID?>"),
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
							url: 'scripts/shipping_notification/sn_actions.php',
							data: { 
								action:'importSN',
								folder:folder,
								newfilename:newfilename,
								SN_ID:"<?=$SN_ID?>"
							},
							success : function(data){							 
							    reloadTable();
							},
							beforeSend: function ( xhr ) {
								$("#SNdata<?=$SN_ID?>").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
							}
						});
		            }
		        }
		    }
		});
	<?php } ?>

		function reloadTable(){

			$.ajax({
				type: "POST",
				url: 'scripts/shipping_notification/sn_actions.php',
				data: { action:'reloadTable',
					SN_ID:"<?=$SN_ID?>"
				},
				success : function(data){
					var response = $.parseJSON(data);
				    $('#spinner').spin(false);
				    $("#SNdata<?=$SN_ID?>").html(response.output);
				},
				beforeSend: function ( xhr ) {
					$('#spinner').spin();
				}
			});
		}


		$("#products<?=$SN_ID?>").select2({
			initSelection: function(element, callback) {
				callback({id: '<?=$SITEA?>', text: '<?=$SITEA?>' });
			},
		    minimumInputLength: 3,
		    ajax: {
		      url: "scripts/shipping_notification/field_list.php",
		      dataType: 'json',
		      data: function (term, page) {
		        return {
		          q: term,
		          field: 'products'
		        };
		      },
		      results: function (data, page) {
		        return { results: data };
		      }
		    }
	    });//
	    
		$("#addSNForm<?=$SN_ID?>").submit(function(e)
		{
		    var postData = $(this).serializeArray();
		    var formURL = $(this).attr("action");
		    $.ajax(
		    {
		        url : formURL,
		        type: "POST",
		        data : postData,
		        dataType: "json",
		        success:function(data) 
		        {

		        	Messenger().post({
						message: data.msg,
						 type: data.msgtype,
						 showCloseButton: true
					});
		            reloadTable();
		        },
		        fail: function(data) 
		        {
		            alert('fail');    
		        },
		        beforeSend:function(data)
		        {
		        alert($("#template<?=$SN_ID?>").val());
		        	if ($("#template<?=$SN_ID?>").val()!=''){
		        		return true;
		        	}else if ($("#amount<?=$SN_ID?>").val()==0 || $("#amount<?=$SN_ID?>").val()=='' || $.isNumeric($("#amount<?=$SN_ID?>").val())==false){
		        		Messenger().post({
							message: 'Amount must be numeric and greater than 0',
						  	type: 'error',
						  	showCloseButton: true
						});
		        		return false;
		        	}else{
		        		return true;
		        	}
		        }
		    });
		    e.preventDefault(); //STOP default action
		});


	 });
	</script>
<?php 
}
?>
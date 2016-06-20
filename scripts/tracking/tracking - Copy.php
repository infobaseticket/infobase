<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
/*
$query="SELECT * from DELIVERYMASTER WHERE SITEID='".$_POST['siteID']."'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
     OCIFreeStatement($stmt);
}
$amount=count($res1['SITEID']);
if ($amount!=0){
	$tags=$res1['TAGS'][0];
	$user=getuserdata($res1['UPDATE_BY'][0]);
	$update_by=$user['firstname']." ".$user['lastname'];
	$update_on=$res1['UPDATE_ON'][0];
}

$query="SELECT TAGNAME from DELIVERYTAGS WHERE TAGNAME IS NOT NULL";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
     OCIFreeStatement($stmt);
    foreach ($res1['TAGNAME'] as $key=>$tagname) {
	    $taglist.="{id: ".$key.", title: '".$tagname."'},";
	    $taglist2.='"'.$tagname.'",';
	}
	$taglist=substr($taglist,0,-1);
}
*/
?>

<script type="text/javascript">
$(document).ready( function() {

	/*
	$("#tokens").select2({tags:[<?=$taglist2?>]});
	$("#tokens").on("change", function(e) { 
		$('#tagsForm').ajaxSubmit({data: {type:'add', addval: e.added}});
	}).on("select2-removed", function(e) { 
		$('#tagsForm').ajaxSubmit({data: {type:'remove', addval: e.choice}});
	});
	*/
	$('#dpYears').datepicker();

	$(document).on('change','#siteidInput', function(){
		var tracksiteid=($(this).val());

		$.ajax({
	        url: 'scripts/los/ajax/field_list.php?siteid='+tracksiteid,
	        type: 'get',
	        data: {q:'99',field:'upgnrs',status:'IS'},
	        dataType: 'json',
	        success: function(json) {
				$('#upgnrInput').empty();	
	        	if (json!=null){
	        		$('#upgnrInput').removeAttr('disabled');
	        		$('#upgnrInput').append('<option value="">Not an upgrade</option>');
		        	$.each(json, function(i,item) {
	                	$('#upgnrInput').append('<option>'+item.text+'</option>');
	            	});
	            }else{
            		$('#upgnrInput').attr('disabled', 'disabled');
            	}
	        }
	    }); 
	});


		function after_tracking_save(response){ 
			if (response.responsetype === "info") {
			 	$('.top-right').notify({
					message: { text: response.responsedata},
					type: 'info'
				}).show();
				Messenger().post({
				  message: response.responsedata,
				  showCloseButton: true
				});

				$('#addComment<?=$_POST['siteID']?>').modal('hide');
			}
			$('#trackicon').click();		
		}
		var options = {   
	    	success:  after_tracking_save,
			dataType:  'json',
		};	
	
		$("#addInfo").click(function( e ){
		   	$('#newTracking_form').ajaxSubmit(options);
		    return false;
		});	

		$('.popovers').popover();

});
</script>
<?php

	$query = "select SIT_UDK, WOE_RANK, WOR_DOM_WOS_CODE from VW_NET1_ALL_NEWBUILDS WHERE upper(SIT_UDK) LIKE '%".strtoupper($_POST['siteID'])."%' AND WOE_RANK=1 ORDER BY SIT_UDK";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	$amount=count($res1['SIT_UDK']);
	foreach ($res1['SIT_UDK'] as $key=>$attrib_id) {
		if ($res1['WOE_RANK'][$key]=='1' && $res1['WOR_DOM_WOS_CODE'][$key]=='IS'){
			$sel="selected";
			$pref=$res1['SIT_UDK'][$key];
		}else{
			$sel="";
		}
	    $options_sites.="<option ".$sel.">".$res1['SIT_UDK'][$key]."</option>";
	}
	if ($pref!=""){
		$query = "select DISTINCT WOR_UDK from VW_NET1_ALL_UPGRADES 
		WHERE SIT_UDK='".$pref."' AND WOR_DOM_WOS_CODE='IS' ORDER BY WOR_UDK ASC";
		//echo $query."<br>";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
		  die_silently($conn_Infobase, $error_str);
		  exit;
		} else {
		  OCIFreeStatement($stmt);
		}
		foreach ($res1['WOR_UDK'] as $key=>$attrib_id) {
		    $options_upg.="<option>".$res1['WOR_UDK'][$key]."</option>";
		}
	}
	if($_POST['rafid']){
		$query = "select * from DELIVERYTRACK WHERE RAFID = '".strtoupper($_POST['rafid'])."'  ORDER BY UPDATEDATE DESC";
	}else{
		$query = "select * from DELIVERYTRACK WHERE SITEID LIKE '%".strtoupper($_POST['siteID'])."%' ORDER BY UPDATEDATE DESC";
	}
	echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	$amount=count($res1['SITEID']);
	$hist=0;
	foreach ($res1['SITEID'] as $key=>$attrib_id) {
		$user_UPDATE=getuserdata($res1['UPDATEBY'][$key]);
		if ($res1['DELETED'][$key]==1){
			$class="class='TRACK_hist_data deleted'";
			$btnDis="disabled";
			$editing="";
			$style="style='display:none;'";
			$hist++;
		}else if ($res1['DELETED'][$key]==2){
			$class="class='TRACK_hist_data locked'";
			$btnDis="disabled";
			$editing="";
			$style="style='display:none;'";
			$hist++;
		}else{
			$class="";
			$btnDis="";
			$editing="editableTracking";
			$style="";
		}
	    $comments.="<tr id='trackLine".$res1['ID'][$key]."' ".$class." ".$style.">
	    	<td style='width:65px;'>
			 <div class='btn-toolbar' role='toolbar' style='display:block;'>
	          <div class='btn-group'>
	            <button class='btn btn-default btn-xs tracknav ".$btnDis."' id='delete' data-trackid='".$res1['ID'][$key]."' href='#'  title='Delete comment'><span class='glyphicon glyphicon-trash'></span></button>
	            <button class='btn btn-default btn-xs tracknav ".$btnDis."' id='history' data-trackid='".$res1['ID'][$key]."' href='#'  title='Make history'><span class='glyphicon glyphicon-time'></span></button>
	          </div>
	         </div>
	    	</td>
            <td>
            <a class='popovers' data-trigger='hover' data-container='body' data-html='true' data-toggle='popover' data-placement='right' 
            data-content='Created on: ".$res1['CREATIONDATE'][$key]."<br>Update on: ".$res1['UPDATEDATE'][$key]."' data-original-title='' title=''>
        ".substr($res1['DATUM'][$key],0,10)."</a></td>
            <td>".$res1['SITEID'][$key]."</td>
            <td>".$user_UPDATE['firstname']." ".$user_UPDATE['lastname']."</td>
            <td><a href='#' class='".$editing."' data-type='textarea' data-pk='".$res1['ID'][$key]."' data-id='".$res1['ID'][$key]."'>".$res1['COMMENTS'][$key]."</span></td>
        </tr>";
	}

?>
<div id="addComment<?=$_POST['siteID']?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">	 
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3>Add comment to <?=$_POST['siteID']?></h3>
		  </div>
		  <div class="modal-body">
		  	<form role="form" action="scripts/tracking/tracking_actions.php" method="post" id="newTracking_form">
			<input type="hidden" name="siteid" value="<?=$_POST['siteID']?>">
			<input type="hidden" name="action" value="insertComment">
		  	<div class="form-group">
			    <label for="dateInput">Date</label>
			    <div class="input-group">
				  <input type="text" name="datum" value="<?php echo date("d/m/Y"); ?>" id="dpYears" data-date="" data-date-format="dd/mm/yyyy" data-date-viewmode="years" class="form-control" id="dateInput" placeholder="Enter date">
				  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				</div>
			</div>
		    <div class="form-group">
			    <label for="siteidInput">Site ID</label>
			    <select name="tracksiteid" id="siteidInput" class="form-control">
				<option>Select SITEID</option>
				<?php echo $options_sites; ?>
				</select>
			</div>
			<div class="form-group">
			    <label for="upgnrInput">Upgrade Number</label>
			    <select name="upgnr" id="upgnrInput" class="form-control">
			    	<option value="">Not an upgrade</option>
			    	<?php echo $options_upg; ?>
				</select>
			</div>
			<div class="form-group">
			    <label for="commInput">Comments</label>
			    <textarea name="comments" class="form-control" id="commInput" rows="3"></textarea>
			</div>
			</form>
		  </div>
		  <div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		    <button class="btn btn-primary" id="addInfo">Add info</button>
		  </div>
		</div>
	</div>
</div>

<form action="scripts/tracking/tracking_actions.php" method="post" id="tagsForm">
<input type="hidden" name="siteID" value="<?=$_POST['siteID']?>">
<input type="hidden" name="action" value="updateTags">
 <input type="text" id="tokens" name="SiteTags" value="<?=$tags?>" class="select2-offscreen form-control">
</form>
<hr>
<?php if ($update_by){ ?>
Last update by <?=$update_by?> on <?=$update_on?>
<?php } ?>

<div class="pull-right"><h3>
<?php if ($hist!=0){ ?>
	<button type="button" class="btn btn-success btn-xs history" id="TRACK_hist">
	<span class="glyphicon glyphicon-eye-open"></span>
	</button>
<?php } ?>
</h3>
</div>
<table class="table table-bordered" id="tracking<?=$_POST['siteID']?>">
    <thead>
    	<tr>
    		<th><button class="btn btn-xs tracknav" title='Add comments to site' id='newTrack' data-siteid="<?=$_POST['siteID']?>" data-toggle='modal' data-target='#addComment<?=$_POST['siteID']?>' data-toggle='modal'><span class="glyphicon glyphicon-plus-sign"></span>Add</button></th>
    		<th>Date</th>
    		<th>SITEID</th>
            <th>Updated by</th>
            <th>Comments</th>
        </tr>
    </thead>
    <tbody>
        <?=$comments?>
    </tbody>    
</table>

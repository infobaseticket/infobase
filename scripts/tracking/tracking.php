<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

?>

<script type="text/javascript">
$(document).ready( function() {


	function after_tracking_save(response){ 
		Messenger().post({
			  message: response.responsedata,
			  type: response.responsetype,
			  showCloseButton: true
		});

		if (response.responsetype === "info"){
			$('#addCommenModalt<?=$_POST['siteID']?>').modal('hide');
		}
		$("#trackicon<?=$_POST['siteID']?>").click();		
	}
	var options = {   
    	success:  after_tracking_save,
		dataType:  'json',
	};	

	$("#addInfo").click(function( e ){
	   	$('#newTracking_form').ajaxSubmit(options);
	    return false;
	});	

});
</script>
<?php
	
	$query = "select RAFID,NET1_LINK FROM BSDS_RAFV2 WHERE SITEID = '".strtoupper($_POST['siteID'])."' ORDER BY RAFID";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	$amount=count($res1['RAFID']);
	foreach ($res1['RAFID'] as $key=>$attrib_id) {
	    $options_raf.="<option value='".$res1['RAFID'][$key]."'>RAF ".$res1['RAFID'][$key]." (".$res1['NET1_LINK'][$key].")</option>";
	}

	$query = "select * from RAF_COMMENTS WHERE SITEID LIKE '%".strtoupper($_POST['siteID'])."%' ";

	if ($_POST['rafid']){
	 	$query.= " AND RAFID='".$_POST['rafid']."'";
	 }
	 $query.= " ORDER BY RAFID, INSERT_DATE DESC";

	//echo $query."<br>";
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
		
		$user_UPDATE=getuserdata($res1['INSERT_BY'][$key]);

		if ($res1['HISTORY'][$key]==1){
			$class="class='TRACK_hist_data'";
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
	    $comments.="<tr id='trackLine".$res1['ID'][$key]."' ".$style." ".$class.">
	    	<td style='width:65px;'>
			 <div class='btn-toolbar' role='toolbar' style='display:block;'>
	          <div class='btn-group'>
	            <button class='btn btn-default btn-xs tracknav ".$btnDis."' data-trackid='".$res1['ID'][$key]."' href='#'  title='Make history'><span class='glyphicon glyphicon-time'></span></button>
	          </div>
	         </div>
	    	</td>
	    	<td>".$res1['ID'][$key]."</td>
	    	<td>".$res1['RAFID'][$key]."</td>
	    	<td>".$res1['SITEID'][$key]."</td>
	    	<td><a class='popovers' data-trigger='hover' data-container='body' data-html='true' data-toggle='popover' data-placement='right' 
            data-content='Created by: ".$res1['INSERT_BY'][$key]."'>".$res1['INSERT_DATE'][$key]."</a></td>
            <td>".$res1['RAFCOMMENT'][$key]."</td>
        </tr>";
	}

	$query = "select t1.RAFID AS RAFID, TASK, t1.COMMENTS, t1.UPDATE_BY, t1.UPDATE_DATE from TASK_COMMENTS t1 LEFT JOIN BSDS_RAFV2 t2 ON t1.RAFID=t2.RAFID
	 WHERE SITEID LIKE '%".strtoupper($_POST['siteID'])."%' ";
	if ($_POST['rafid']){
	 	$query.= " AND t1.RAFID='".$_POST['rafid']."'";
	 }
	$query.=" ORDER BY t1.RAFID, t1.UPDATE_DATE DESC";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	  die_silently($conn_Infobase, $error_str);
	  exit;
	} else {
	  OCIFreeStatement($stmt);
	}
	$amountTaskComments=count($res1['RAFID']);
	foreach ($res1['RAFID'] as $key=>$attrib_id) {
		
		$user_UPDATE=getuserdata($res1['UPDATE_BY'][$key]);
	    $task_comments.="<tr>
	    	<td>".$res1['RAFID'][$key]."</td>
	    	<td>".$res1['TASK'][$key]."</td>
	    	<td><a class='popovers' data-trigger='hover' data-container='body' data-html='true' data-toggle='popover' data-placement='right' 
            data-content='Created by: ".$res1['UPDATE_BY'][$key]."'>".$res1['UPDATE_DATE'][$key]."</a></td>
            <td>".$res1['COMMENTS'][$key]."</td>
        </tr>";
	}

?>
<div id="addCommenModalt<?=$_POST['siteID']?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">	 
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3>Add comment for RAF <?=$_POST['siteID']?></h3>
		  </div>
		  <div class="modal-body">
		  	<form role="form" action="scripts/tracking/tracking_actions.php" method="post" id="newTracking_form">
			<input type="hidden" name="siteid" value="<?=$_POST['siteID']?>">
			<input type="hidden" name="action" value="insertComment">
		    <div class="form-group">
			    <label for="rafidInput">RAF ID</label>
			    <select name="rafid" id="rafidInput" class="form-control">
				<option value=''>Select RAFID</option>
				<?php echo $options_raf; ?>
				</select>
			</div>
			<div class="form-group">
			    <label for="commInput">Comments</label>
			    <textarea name="comments" class="form-control" id="commInput" rows="3"></textarea>
			</div>
			</form>
		  </div>
		  <div class="modal-footer">
		    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    <button class="btn btn-primary" id="addInfo">Add info</button>
		  </div>
		</div>
	</div>
</div>

<div class="pull-right">
<?php if ($hist!=0){ ?>
	<button type="button" class="btn btn-success btn-xs history" id="TRACK_hist">
	<span class="glyphicon glyphicon-eye-open"> HISTORY (<?=$hist?>)</span>
	</button>
<?php } ?>
</div>

<h3>TASK COMMENTS</h3>
<table class="table table-bordered">
	<thead>
    	<tr>
    		<th>RAFID</th>
    		<th>TASK</th>
           
            <th>INSERT DATE</th>
             <th>COMMENTS</th>
        </tr>
    </thead>
    <tbody>
        <?=$task_comments?>
    </tbody> 
</table>

<h3>RAFID COMMENTS</h3>
<table class="table table-bordered" id="tracking<?=$_POST['siteID']?>">
    <thead>
    	<tr>
    		<th><button class="btn btn-xs" title='Add comments to site' id='newTrack' data-siteid="<?=$_POST['siteID']?>" data-toggle='modal' data-target='#addCommenModalt<?=$_POST['siteID']?>' data-toggle='modal'><span class="glyphicon glyphicon-plus-sign"></span>Add</button></th>
    		<th>ID</th>
    		<th>RAFID</th>
    		<th>SITEID</th>
            <th>INSERT DATE</th>
            <th>COMMENTS</th>
        </tr>
    </thead>
    <tbody>
        <?=$comments?>
    </tbody>    
</table>

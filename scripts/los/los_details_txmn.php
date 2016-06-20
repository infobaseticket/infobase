<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['losid']!=""){
	$disable_check="readonly";
	$query = "Select * FROM BSDS_LINKINFO WHERE ID = '".$_POST['losid']."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_LOS=count($res1['SITEA'][0]);	
		$SITEA=$res1['SITEA'][0];
		$SITEB=$res1['SITEB'][0];
		$COMMENTSA=$res1['COMMENTSA'][0];
		$COMMENTSB=$res1['COMMENTSB'][0];
		$HEIGHTA=$res1['HEIGHTA'][0];
		$HEIGHTB=$res1['HEIGHTB'][0];
		$PRIORITY=$res1['PRIORITY'][0];
		$PARTNERVIEW=$res1['PARTNERVIEW'][0];
		$TYPE=$res1['TYPE'][0];
	}

}

if ($amount_of_LOS>=1){
	$action="Update";
}else{
	$action="Create";
}

if ($PRIORITY==""){
	$PRIORITY_select="Please select";
}else{
	$PRIORITY_select=$PRIORITY;
}

if ($TYPE==""){
	$TYPE_select="Please select";
}else{
	$TYPE_select=$TYPE;
}	
if ($PARTNERVIEW==""){
	$PARTNERVIEW_select="Please select";
}else{
	$PARTNERVIEW_select=$PARTNERVIEW;
}

?>
<script language="JavaScript">
$(document).ready(function() {
	$('#inputSiteA').select2({
		initSelection: function(element, callback) {
			callback({id: '<?=$SITEA?>', text: '<?=$SITEA?>' });
		},
	    minimumInputLength: 3,
	    ajax: {
	      url: "scripts/los/ajax/field_list.php",
	      dataType: 'json',
	      data: function (term, page) {
	        return {
	          q: term,
	          field: 'sites'
	        };
	      },
	      results: function (data, page) {
	        return { results: data };
	      }
	    }
    });
    $('#inputSiteB').select2({
    	initSelection: function(element, callback) {
			callback({id: '<?=$SITEB?>', text: '<?=$SITEB?>' });
		},
	    minimumInputLength: 3,
	    ajax: {
	      url: "scripts/los/ajax/field_list.php",
	      dataType: 'json',
	      data: function (term, page) {
	        return {
	          q: term,
	          field: 'sites'
	        };
	      },
	      results: function (data, page) {
	        return { results: data };
	      }
	    }
    });
    $("#inputSiteB").on("change", function(e) { 
    
		if ($('#inputLosType').val()=="NB"){
			$.get( "scripts/los/ajax/field_list.php", { field: "conAcqPartner",q:e.val,lostype: $('#inputLosType').val()} ,
			function( data ) {
				if (data!='null'){
				 if (data==='error'){
				 		$('#savemodal').attr('disabled','disabled');
				 		$('#inputPartnerview').attr('disabled','disabled');
				 		Messenger().post({
						  message: 'Please update the SAC or CON for that site in NET1 to TECHM, ALU, BENCHMARK or ZTE!',
						  type: 'error',
						  id:'onlyone',
						  showCloseButton: true
						});
						$('#inputPartnerview').empty();
				 }else{
				 		$('#savemodal').removeAttr('disabled');
				 		$('#inputPartnerview').empty().removeAttr('disabled').append(data);
				 }
				}
			})
	    }
	});

	$('#savemodal').removeAttr('disabled');

	if (($('#inputLosType').val()=="NB" || $('#inputLosType').val()=="MOV") && $('#inputSiteB').val()!=''){
		$.get( "scripts/los/ajax/field_list.php", { 
				field: "conAcqPartner",
				q:$('#inputSiteB').val(),
				lostype: $('#inputLosType').val()} ,
			 function( data ) {
			 	if (data==='error'){
			 		$('#savemodal').attr('disabled','disabled');
			 		$('#inputPartnerview').attr('disabled','disabled');
			 		Messenger().post({
					  message: 'Please update the SAC or CON for that site in NET1 to TECHM, ALU, BENCHMARK or ZTE!',
					  type: 'error',
					  id:'onlyone',
					  showCloseButton: true
					});
			 	}else{
			 		$('#savemodal').removeAttr('disabled');
			 		$('#inputPartnerview').empty().removeAttr('disabled').append(data);
			 	}
			})
	}
	if (($('#inputLosType').val() === "ST" || $('#inputLosType').val() === "RSL")){
			$('#inputPartnerview').removeAttr('disabled');
			$('#savemodal').removeAttr('disabled');
			$('#inputPartnerview').val('Please select');
			$('#inputPartnerview').append("<option>ZTE</option><option selected>TECHM</option><option>BENCHMARK</option><option>BASE</option>");
	}
	$('#inputLosType').change(function(){
		if (this.value === "NB" || this.value=="MOV") {
			$('#inputPartnerview').attr('disabled','disabled');
			$('#savemodal').attr('disabled','disabled');

			if ($('#inputSiteB').val()!=''){
				$.get( "scripts/los/ajax/field_list.php", { field: "conAcqPartner",
					q:$('#inputSiteB').val(),
					lostype: $('#inputLosType').val()
				} ,
					 function( data ) {
				
					 	if (data==='error'){
					 		$('#savemodal').attr('disabled','disabled');
					 		$('#inputPartnerview').attr('disabled','disabled');
					 		Messenger().post({
							  message: 'Please update the SAC or CON for that site in NET1 to TECHM, ALU, BENCHMARK or ZTE!',
							  type: 'error',
							  id:'onlyone',
							  showCloseButton: true
							});
					 	}else{
					 		$('#savemodal').removeAttr('disabled');
					 		$('#inputPartnerview').empty().removeAttr('disabled').append(data);
					 	}
					}
					)
			}
		}else if (this.value === "ST" || this.value === "RSL"){
			$('#inputPartnerview').removeAttr('disabled');
			$('#savemodal').removeAttr('disabled');
			$('#inputPartnerview').val('Please select');
			$('#inputPartnerview').empty().append("<option selected>TECHM</option><option>BASE</option>");
		}
	});
	$('#inputSiteB').focusout(function(){

		if ($('#inputLosType').val()=="NB"){
			var siteB=$(this).val();
			$.get( "scripts/los/ajax/field_list.php", { field: "conAcqPartner",q:siteB} ,
			 function( data ) {
			 	if (data==='error'){
			 		$('#savemodal').attr('disabled','disabled');
			 		$('#inputPartnerview').attr('disabled','disabled');
			 		Messenger().post({
					  message: 'Please update the SAC or CON for that site in NET1 to TECHM, ALU, BENCHMARK or ZTE!',
					  type: 'error',
					  id:'onlyone',
					  showCloseButton: true
					});
			 	}else{
			 		$('#savemodal').removeAttr('disabled');
			 		$('#inputPartnerview').empty().removeAttr('disabled').append(data);
			 	}
			})
		}
	});
});
</script>

<form action="scripts/los/los_actions.php" method="post" id="new_los_form" role="form">
<input type="hidden" name="action" value="ins_upd_los">
<input type="hidden" name="losid" value="<?=$_POST['losid']?>">
<input type="hidden" name="siteID" value="<?=$_POST['siteID']?>">

<form role="form">
  <div class="form-group">
    <label for="inputPrio" class="control-label">Priority</label>
	<select name="PRIORITY" class="form-control input-sm" id="inputPrio"><option selected><?=$PRIORITY_select?></option>
		<option>0</option><option>1</option><option>2</option><option>Canceled</option></select>
  </div>
  <div class="form-group">
    <label for="inputLosType" class="control-label">Type</label>
	<select name="TYPE" class="form-control input-sm" id="inputLosType"><option selected><?=$TYPE_select?></option>
	<option value="NB">New build</option><option value="ST">Standard</option><option value="RSL">RSL Project</option><option value="MOV">MOV Project</option></select>
  </div>
  <div class="form-group">
    <label for="inputPartnerview" class="control-label">Execution partner</label>
	<select name="PARTNERVIEW" id="inputPartnerview" class="form-control input-sm">
	<option><?=$PARTNERVIEW_select?></option></select>
  </div>
  </div>
  <div class="row">
  	<div class="col-md-6">
		<div class="form-group">
		    <label for="inputSiteA" class="control-label">SITE A</label>
		    <input type="text" name='SITEA' value="<?=$SITEA?>" class="form-control" id="inputSiteA" />
		</div>
		<div class="form-group">
		    <label for="inputHEIGHTA" class="control-label">HEIGHT A</label>
		   	<input type="text" value="<?=$HEIGHTA?>" id="inputHEIGHTA" name="HEIGHTA" class="form-control input-sm">
		</div>
		<div class="form-group">
		    <label for="inputCOMMENTSA" class="control-label">COMMENTS A</label>
		   	<textarea name="COMMENTSA" id="COMMENTSA" class="form-control" rows="3"><?=$COMMENTSA?></textarea>
		</div>
	</div>
  	<div class="col-md-6">
		<div class="form-group">
		    <label for="inputSiteB" class="control-label">SITE B</label>
		    <input type="text" name='SITEB' value="<?=$SITEB?>" class="form-control" id="inputSiteB" />
		</div>
		<div class="form-group">
		    <label for="inputHEIGHTB" class="control-label">HEIGHT B</label>
		   	<input type="text" value="<?=$HEIGHTB?>" id="inputHEIGHTB" name="HEIGHTB" class="form-control input-sm">
		</div>
		<div class="form-group">
		    <label for="inputCOMMENTSB" class="control-label">COMMENTS B</label>
		   	<textarea name="COMMENTSB" id="inputCOMMENTSB" class="form-control" rows="3"><?=$COMMENTSB?></textarea>
		</div>
	</div>
</div>
<?php
if ($_POST['losid']!=""){
	echo "<button class='btn btn-primary' id='savemodal' data-module='losnew'>SAVE CHANGES</button>";
}
?>
</form>
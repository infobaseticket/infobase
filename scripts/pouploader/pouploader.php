<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Benchmark","");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

?>
<script type="text/javascript">
$(document).ready( function() {
	var multiAnalyseOptions = {
		success: 	after_analyse,
		data: 		{action:'multianalyse'},
		dataType:  'json'
	};	
	function after_analyse(response){  
		
		if(response.type!='info'){
	         Messenger().post({
				  message: response.message,
				  type: response.type,
				  showCloseButton: true
				});
		}else{
			$("#uploadStart").show();
			$("#uploadBegin").hide();
			$("#csvdata").attr('readonly','readonly');
		}
		$("#csvresult").html(response.table);
	}	
	$('#uploadBegin').click(function(){
		$('#csvresult').html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
	    $('#pouploderForm').ajaxSubmit(multiAnalyseOptions);
	    return false;
	});

	var multiImportOptions = {
		success: after_import,
		data: {action:'multiimport'},
		dataType:  'json'
	};	
	function after_import(response){  
		if(response.type!='info'){
			$('.top-right').notify({
	            message: { text: response.message},
	            type:  response.type
	        }).show();
		}else{
			$("#uploadStart").hide();
			$("#uploadBegin").show();
			$("#csvdata").removeAttr('readonly');
		}
		$("#csvresult").html(response.table);
	}
	$('#uploadStart').click(function(){
		$('#csvresult').html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
	    $('#pouploderForm').ajaxSubmit(multiImportOptions);
	    return false;
	});

	var multiDeleteOptions = {
		success: 	after_delanalyse,
		data: 		{action:'multianalyseDelete'},
		dataType:  'json'
	};	
	function after_delanalyse(response){  
		if(response.type!='info'){
	         Messenger().post({
				  message: response.message,
				  type: response.type,
				  showCloseButton: true
				});
		}else{
			$("#deleteStart").show();
			$("#deleteBegin").hide();
			$("#csvdataDelete").attr('readonly','readonly');
		}
		$("#csvresult").html(response.table);
	}	
	$('#deleteBegin').click(function(){
		$('#csvresult').html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
	    $('#podeletionForm').ajaxSubmit(multiDeleteOptions);
	    return false;
	});

	var multiDeleteDOOptions = {
		success: after_del,
		data: {action:'multidel'},
		dataType:  'json'
	};	
	function after_del(response){  
		if(response.type!='info'){
			$('.top-right').notify({
	            message: { text: response.message},
	            type:  response.type
	        }).show();
		}else{
			$("#deleteStart").hide();
			$("#deleteBegin").show();
			$("#csvdata").removeAttr('readonly');
		}
		$("#csvresult").html(response.table);
	}
	$('#deleteStart').click(function(){
		$('#csvresult').html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
	    $('#podeletionForm').ajaxSubmit(multiDeleteDOOptions);
	    return false;
	});
});
</script>


<br>
<div class="row-fluid">    
    <div class="span6 well"> 
    	<h2>RAF PO upload</h2>
    	<i>For ACQCON: ACQ for PO acquisition and CON for PO construction<br>NOTE: 2700.00 and NOT 2700,00</i>
    	<div class="csvimporter" id="csvimporter">  
    		<form action="scripts/pouploader/pouploader_actions.php" method="post" id="pouploderForm">  
    		<input type="hidden" name="csvdataAnalysed" value="">
    		<textarea name="csvdata" class="form-control" rows="10" style="width:500px;" id="csvdata">RAFID,POPR,ACQCON,POTEXT,ITEMCOST,PODATE&#13;9360, OK, ACQ,MANUALLY IMPORTED,"27700.00",20/06/2015</textarea><br>    		
    		<input type="submit" value="Analyse csv data" class="btn" id="uploadBegin"> <input type="submit" value="Start update" id="uploadStart" class="btn btn-warning" style="display:none;">
    		</form>
    	</div>    	
    </div> 
    <div class="span6 well"> 
    	<h2>RAF PO deletion</h2>
    	<i>Please provide list of RAFID's</i>
    	<div class="csvimporter" id="csvimporter">  
    		<form action="scripts/pouploader/pouploader_actions.php" method="post" id="podeletionForm">  
    		<input type="hidden" name="csvdataAnalysed" value="">
    		<textarea name="csvdata" class="form-control" rows="10" style="width:500px;" id="csvdataDelete">RAFID,ACQCON&#13;99999,CON</textarea><br>    		
    		<input type="submit" value="Analyse csv data" class="btn" id="deleteBegin"> <input type="submit" value="Start update" id="deleteStart" class="btn btn-warning" style="display:none;">
    		</form>
    	</div>    	
    </div> 
</div>
<div id="csvresult"></div>
<?php
ocilogoff($conn_Infobase);
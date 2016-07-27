<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Benchmark","");

$user=getuserdata($guard_username);
/*
if ($user['netoneuser']!="feyland"){
echo "There is an issue with NET1 for the net1 updater. Access denied untill a solution has been found.";
die;
}*/
if ($user['netoneuser']=="" or $user['netonepass']==""){
	?>
	<script type="text/javascript">
	Messenger().post({
				  message: "Please provide your username and pass for NetOne to Frederick Eyland to have access to this part of Infobase!",
				  type: 'info',
				  showCloseButton: true
				});
	</script>
	<?
	die;
}
echo "<span class='badge pull-right'>Your net1 username: ".$user['netoneuser']."</span>";


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

?>
<script type="text/javascript">
$(document).ready( function() {

	$('.datepicker').datepicker({
	    format: 'mm/dd/yyyy',
	    startDate: '-3d'
	});

	$("#estimate").change(function(){
		if ($(this).is(':checked')){
			$(".notes").hide();
		}else{
			$(".notes").show();
		}
	});

	function after_net1_update(response){  
		$('#spinner').spin(false);
		Messenger().post({
				  message: response.responsedata,
				  type: response.responsetype,
				  showCloseButton: true
				});
		if (response.overide=='yes'){
			$('#updatecode2').toggle();
		}
	}	

	$('#updatecode2').click(function(){
		var options1 = {
			success: after_net1_update,
			dataType:  'json',
			data: {override:'yes'}
		};
		$('#updatecode2').toggle();
	  	$('#form_updater').ajaxSubmit(options1);
	  	return false;
	});
	
	$('#updatecode1').click(function(){
		var options2 = {
			success: after_net1_update,
			dataType:  'json'
		};

		$('#spinner').spin('medium');
	    $('#form_updater').ajaxSubmit(options2);
	    return false;
	});
	function after_net1_get(response){  
		$('#spinner').spin(false);
		Messenger().post({
				  message: response.responsedata,
				  type: response.responsetype,
				  showCloseButton: true,
				  id:'net1updater'
				});
	}	
	var options2 = {
		success: after_net1_get,
		dataType:  'json'
	};
	$('#form_getter').submit(function(){
		$("#updatebox").html('');
		$('#spinner').spin('medium');
	    $(this).ajaxSubmit(options2);
	    return false;
	});
	var multiAnalyseOptions = {
		success: 	after_analyse,
		data: 		{action:'multianalyse'},
		dataType:  'json'
	};	
	function after_analyse(response){  
		$('#spinner').spin(false);
		if(response.type!='info'){
	         Messenger().post({
				  message: response.message,
				  type: response.type,
				  showCloseButton: true
				});
		}else{
			$("#uploadstart").show();
			$("#uploadbegin").hide();
			$("#csvdata").attr('readonly','readonly');
		}
		$("#csvresult").html(response.table);
	}	
	$('#uploadbegin').click(function(){
		$("#updatebox").html('');
		$('#spinner').spin('medium');
	    $('#mutliuploder').ajaxSubmit(multiAnalyseOptions);
	    return false;
	});

	var multiImportOptions = {
		success: after_import,
		data: {action:'multiimport'},
		dataType:  'json'
	};	
	function after_import(response){  
		$('#spinner').spin(false);
		if(response.type!='info'){
			$('.top-right').notify({
	            message: { text: response.message},
	            type:  response.type
	        }).show();
		}else{
			$("#uploadstart").hide();
			$("#uploadbegin").show();
			$("#csvdata").removeAttr('readonly');
		}
		$("#csvresult").html(response.table);
	}
	$('#uploadstart').click(function(){
		$("#updatebox").html('');
		$('#spinner').spin('medium');
	    $('#mutliuploder').ajaxSubmit(multiImportOptions);
	    return false;
	});

	var multiAnalyseOptionsParties = {
		success: 	after_analyseParties,
		data: 		{action:'multianalyseParties'},
		dataType:  'json'
	};	
	function after_analyseParties(response){  
		$('#spinner').spin(false);
		if(response.type!='info'){
	         Messenger().post({
				  message: response.message,
				  type: response.type,
				  showCloseButton: true
				});
		}else{
			$("#uploadstartParties").show();
			$("#uploadbeginParties").hide();
			$("#csvdataParties").attr('readonly','readonly');
		}
		$("#csvresultParties").html(response.table);
	}	
	$('#uploadbeginParties').click(function(){
		$("#updatebox").html('');
		$('#spinner').spin('medium');
	    $('#mutliuploderParties').ajaxSubmit(multiAnalyseOptionsParties);
	    return false;
	});
//Multi status update
	var multiAnalyseOptionsStatus = {
		success: 	after_analyseStatus,
		data: 		{action:'multianalyseStatus'},
		dataType:  'json'
	};	
	function after_analyseStatus(response){  
		$('#spinner').spin(false);
		if(response.type!='info'){
	         Messenger().post({
				  message: response.message,
				  type: response.type,
				  showCloseButton: true
				});
		}else{
			$("#uploadstartStatus").show();
			$("#uploadbeginStatus").hide();
			$("#csvdataStatus").attr('readonly','readonly');
		}
		$("#csvresultStatus").html(response.table);
	}	
	$('#uploadbeginStatus').click(function(){
		$("#updatebox").html('');
		$('#spinner').spin('medium');
	    $('#mutliuploderStatus').ajaxSubmit(multiAnalyseOptionsStatus);
	    return false;
	});

	var multiImportOptionsStatus = {
		success: after_importStatus,
		data: {action:'multiimportStatus'},
		dataType:  'json'
	};	
	function after_importStatus(response){  
		$('#spinner').spin(false);
		if(response.type!='info'){
			$('.top-right').notify({
	            message: { text: response.message},
	            type:  response.type
	        }).show();
		}else{
			$("#uploadstartStatus").hide();
			$("#uploadbeginStatus").show();
			$("#csvdataStatus").removeAttr('readonly');
		}
		$("#csvresultStatus").html(response.table);
	}
	$('#uploadstartStatus').click(function(){
		$("#updatebox").html('');
		$('#spinner').spin('medium');
	    $('#mutliuploderStatus').ajaxSubmit(multiImportOptionsStatus);
	    return false;
	});

//Multi Parties upload in NET1
	var multiImportOptionsParties = {
		success: after_importParties,
		data: {action:'multiimportParties'},
		dataType:  'json'
	};	
	function after_importParties(response){  
		$('#spinner').spin(false);
		if(response.type!='info'){
			$('.top-right').notify({
	            message: { text: response.message},
	            type:  response.type
	        }).show();
		}else{
			$("#uploadstartParties").hide();
			$("#uploadbeginParties").show();
			$("#csvdataParties").removeAttr('readonly');
		}
		$("#csvresultParties").html(response.table);
	}
	$('#uploadstartParties').click(function(){
		$("#updatebox").html('');
		$('#spinner').spin('medium');
	    $('#mutliuploderParties').ajaxSubmit(multiImportOptionsParties);
	    return false;
	});

	$('.sitelist').select2({
		initSelection: function(element, callback) {
					callback({id: element.val(), text: element.val() });
		},
		minimumInputLength: 1,
		ajax: {
			url: "scripts/raf/raf_select_options.php",
			dataType: 'json',
			type:'POST',
			data: function (term, page) {
			  	return {
			          siteid: term,
			          field: 'SITELISTNOTPREF'
			    };
			},
			results: function (data, page) {
				return { results: data };
			}
		}
	});

	$('#upgnr2').select2({
		initSelection: function(element, callback) {
					callback({id: element.val(), text: element.val() });
		},
		minimumInputLength: 1,
		ajax: {
			url: "scripts/raf/raf_select_options.php",
			dataType: 'json',
			type:'POST',
			data: function (term, page) {
			  	return {
			          	upgnr: term,
			           	field: 'UPGNRS',
	                	siteidcand: $('#siteid2').val(),
			    };
			},
			results: function (data, page) {
				return { results: data };
			}
		}
	});

	
	$('#upgnr1').select2({
		initSelection: function(element, callback) {
					callback({id: element.val(), text: element.val() });
		},
		minimumInputLength: 1,
		ajax: {
			url: "scripts/raf/raf_select_options.php",
			dataType: 'json',
			type:'POST',
			data: function (term, page) {
			  	return {
			          	upgnr: term,
			           	field: 'UPGNRS',
	                	siteidcand: $('#siteid1').val(),
			    };
			},
			results: function (data, page) {
				return { results: data };
			}
		}
	});
	function FormatResult(tasklist){
		 return tasklist.description;
	}
	function FormatSelection(tasklist){
		return tasklist.upgnr	
	}
	$('#tasklist1').select2({
		initSelection: function(element, callback) {
					callback({id: element.val(), text: element.val() });
		},
		minimumInputLength: 1,
		ajax: {
			url: "scripts/los/ajax/field_list.php",
			dataType: 'json',
			data: function (term, page) {
			  	return {
			          	q: term,
			           	field: 'tasklist',
	                	upgnr: $('#upgnr1').val()
			    };
			},
			results: function (data, page) {
				return { results: data };
			},
		},
		formatResult: FormatResult, 
    	formatSelection: FormatSelection
	})

	$('#tasklist2').select2({
		initSelection: function(element, callback) {
					callback({id: element.val(), text: element.val() });
		},
		minimumInputLength: 1,
		ajax: {
			url: "scripts/los/ajax/field_list.php",
			dataType: 'json',
			data: function (term, page) {
			  	return {
			          	q: term,
			           	field: 'tasklist',
	                	upgnr: $('#upgnr2').val()
			    };
			},
			results: function (data, page) {
				return { results: data };
			}
		},
		formatResult: FormatResult, 
    	formatSelection: FormatSelection 
	});
//Multi RF info updater
	var multiAnalyseOptionsRFinfo = {
		success: 	after_analyseRFinfo,
		data: 		{action:'multianalyseRFinfo'},
		dataType:  'json'
	};	
	function after_analyseRFinfo(response){  
		$('#spinner').spin(false);
		if(response.type!='info'){
	         Messenger().post({
				  message: response.message,
				  type: response.type,
				  showCloseButton: true
				});
		}else{
			$("#uploadstartRFinfo").show();
			$("#uploadbeginRFinfo").hide();
			$("#csvdataRFinfo").attr('readonly','readonly');
		}
		$("#csvresultRFinfo").html(response.table);
	}	
	$('#uploadbeginRFinfo').click(function(){
		$("#updatebox").html('');
		$('#spinner').spin('medium');
	    $('#mutliuploderRFinfo').ajaxSubmit(multiAnalyseOptionsRFinfo);
	    return false;
	});

});
</script>

<div class="container">     
    <div class="span12">      
         <div id="updatebox"></div>
    </div> 
</div>
<div class="row-fluid">
  	<div class="span6 well">
  		<h2>Update NET1</h2>
	  	<form action="scripts/net1updater/net1_actions.php" class="form-horizontal" role="form" method="post" id="form_updater">
		<input type="hidden" name="action" value="updatecode">
		<input type="hidden" name="netoneuser" value="<?=$user['netoneuser']?>">
		<input type="hidden" name="netonepass" value="<?=$user['netonepass']?>">
		<div class="form-group">
		    <label for="siteid1" class="col-sm-3 control-label">SITE</label>
		    <div class="col-sm-9">
		      <input type="text" name="element" class="sitelist form-control" id="siteid1" placeholder="SITEID">
		    </div>
		</div>
		<div class="form-group">
		    <label for="upgnr1" class="col-sm-3 control-label">UPG NR</label>
		    <div class="col-sm-9">
		      <input type="text" name="upgnr" class="form-control upglist" id="upgnr1" placeholder="UPGRADE NUMBER">
		    </div>
		</div>
		<div class="form-group">
		    <label for="tasklist1" class="col-sm-3 control-label">CODE</label>
		    <div class="col-sm-9">
		      <input type="text" name="code" class="form-control tasklist" id="tasklist1" placeholder="TASK">
		    </div>
		</div>
		<div class="form-group">
		    <label for="dpYears" class="col-sm-3 control-label">DATE</label>
		    <div class="col-sm-9">
		    	<input name="insertdate" class="form-control" data-provide="datepicker" data-date-format="dd-mm-yyyy" placeholder="SELECT DATE">
		    </div>
		</div>
		<div class="form-group">
		    <div class="col-sm-offset-3 col-sm-9">
		      <div class="checkbox">
		        <label>
		          <input name="estimate" id="estimate" type="checkbox" value="1"> Estimate date
		        </label>
		      </div>
		    </div>
		</div>
		<div class="form-group">
		    <label for="dpYears" class="col-sm-3 control-label notes">NOTES</label>
		    <div class="col-sm-9">
		    	<textarea name="notes"  class="form-control notes" rows="3"></textarea>
		    </div>
		</div>
		<div class="form-group">
		    <div class="col-sm-offset-3 col-sm-9">
		    	<input type="submit" id="updatecode1" value="UPDATE CODE" class="btn btn-primary">
		    	<input type="submit" id="updatecode2" value="CONFIRM" class="btn btn-warning" style="display:none;">
		    </div>
		</div>
		</form>
	</div>
	<div class="span6 well">
		<h2>Get dates from NET1</h2>
	  	<form action="scripts/net1updater/net1_actions.php" class="form-horizontal" role="form" method="post" id="form_getter">
		<input type="hidden" name="action" value="getcode">
		<input type="hidden" name="netoneuser" value="<?=$user['netoneuser']?>">
		<input type="hidden" name="netonepass" value="<?=$user['netonepass']?>">
		<div class="form-group">
		    <label for="siteid2" class="col-sm-2 control-label">SITE</label>
		    <div class="col-sm-10">
		      <input type="text" name="element" class="sitelist form-control" id="siteid2" placeholder="SITEID">
		    </div>
		</div>
		<div class="form-group">
		    <label for="upgnr2" class="col-sm-2 control-label">UPG NR</label>
		    <div class="col-sm-10">
		      <input type="text" name="upgnr" class="form-control upglist" id="upgnr2" placeholder="UPGRADE NUMBER">
		    </div>
		</div>
		<div class="form-group">
		    <label for="tasklist2" class="col-sm-2 control-label">CODE</label>
		    <div class="col-sm-10">
		      <input type="text" name="code" class="form-control tasklist" id="tasklist2" placeholder="TASK">
		    </div>
		</div>
		<div class="form-group">
		    <div class="col-sm-offset-2 col-sm-10">
		    	<input type="submit" value="GET DATE" class="btn btn-primary">
		    </div>
		</div>
		</form>
  	</div>

</div>
<?php if (substr_count($guard_groups, 'Benchmark')!=1){ ?>

<div class="row-fluid">    
    <div class="span6 well"> 
    	<h2>Multi milestone update</h2>
    	<i>For estimate dates: 1 else 0</i>
    	<div class="csvimporter" id="csvimporter">  
    		<form action="scripts/net1updater/net1_actions.php" method="post" id="mutliuploder">  
    		<input type="hidden" name="csvdataAnalysed" value="">
    		<input type="hidden" name="netoneuser" value="<?=$user['netoneuser']?>">
			<input type="hidden" name="netonepass" value="<?=$user['netonepass']?>">
    		<textarea name="csvdata" class="form-control" rows="10" style="width:500px;" id="csvdata">SITE,UPGNR/CANDIDATE,CODE, DATE, ESTIMATE, NOTES&#13; _LI2581, 99142127, U329, 10-04-2013, 0, "some text"&#13;_AN0804,_AN0804A,A06,05/06/2003,0,""</textarea><br>    		
    		
    		<input type="submit" value="Analyse csv data" class="btn" id="uploadbegin"> <input type="submit" value="Start update" id="uploadstart" class="btn  btn-warning" style="display:none;">
    		</form>
    		<div id="csvresult"></div>
    	</div>    	
    </div> 
    <div class="span6 well"> 
    	<h2>Multi parties update</h2>
    	<i>Partner can be: KPNGB, ZTE, BENCHMARK, TECHM, M4C</i>
    	<div class="csvimporter" id="csvimporterParties">  
    		<form action="scripts/net1updater/net1_actions.php" method="post" id="mutliuploderParties">  
    		<input type="hidden" name="csvdataAnalysed" value="">
    		<input type="hidden" name="netoneuser" value="<?=$user['netoneuser']?>">
			<input type="hidden" name="netonepass" value="<?=$user['netonepass']?>">
    		<textarea name="csvdataParties" class="form-control" rows="10" style="width:500px;" id="csvdataParties">SITE_UPGNR,CODE,PARTNER&#13;</textarea><br>    		
    		
    		<input type="submit" value="Analyse csv data" class="btn" id="uploadbeginParties"> <input type="submit" value="Start update" id="uploadstartParties" class="btn  btn-warning" style="display:none;">
    		</form>
    		<div id="csvresultParties"></div>
    	</div>    	
    </div> 
</div>

<div class="row-fluid">    
    <div class="span6 well">
    	<h2>Multi status update</h2>
    	<i>Status can be: IS, DL, CL, OH, ST,DM<br>Site (without candidate) or upgrade number</i>
    	<div class="csvimporter" id="csvimporterStatus">  
    		<form action="scripts/net1updater/net1_actions.php" method="post" id="mutliuploderStatus">  
    		<input type="hidden" name="csvdataAnalysed" value="">
    		<input type="hidden" name="netoneuser" value="<?=$user['netoneuser']?>">
			<input type="hidden" name="netonepass" value="<?=$user['netonepass']?>">
    		<textarea name="csvdataStatus" class="form-control" rows="10" style="width:500px;" id="csvdataStatus">SITE_UPGNR,STATUS&#13;_BW4550,IS</textarea><br>    		
    		
    		<input type="submit" value="Analyse csv data" class="btn" id="uploadbeginStatus"> <input type="submit" value="Upload in NET1" id="uploadstartStatus" class="btn  btn-warning" style="display:none;">
    		</form>
    		<div id="csvresultStatus"></div>
    	</div>   
    </div> 

    <div class="span6 well">
    	<h2>Multi RF iNFO update</h2>
    	<i>Candidate or upgrade number, RF INFO</i>
    	<div class="csvimporter" id="csvimporterRFinfo">  
    		<form action="scripts/net1updater/net1_actions.php" method="post" id="mutliuploderRFinfo">  
    		<input type="hidden" name="RFinfoAnalyse" value="">
    		<input type="hidden" name="netoneuser" value="<?=$user['netoneuser']?>">
			<input type="hidden" name="netonepass" value="<?=$user['netonepass']?>">
    		<textarea name="csvdataStatus" class="form-control" rows="10" style="width:500px;" id="csvdataStatus">SITE_UPGNR,RFINFO</textarea><br>    		
    		
    		<input type="submit" value="Analyse csv data" class="btn" id="uploadbeginRFinfo"> <input type="submit" value="Upload in NET1" id="uploadstartRFinfo" class="btn  btn-warning" style="display:none;">
    		</form>
    		<div id="csvresultRFinfo"></div>
    	</div>   
    </div> 
</div>    	
<?php
}
ocilogoff($conn_Infobase);

/*
$query = "SELECT * FROM BSDS_FUNDED_TEAML_ACC WHERE BSDSKEY='".$_SESSION['BSDSKEY']."'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);

}

/*SELECT wot.wot_tos_tas_code
,      wot.wot_planned
,      wot.wot_complete,
	   wor.wor_udk
FROM   works_order_tasks wot
,      works_order_elements woe
,      works_orders wor
,      sites sit
WHERE  wot.wot_wor_id = wor.wor_id
AND    woe.woe_wor_id = wor.wor_id
AND    woe.woe_sit_id = sit.sit_id
AND   wor.wor_udk='99138845'
AND    sit.sit_udk = '_BW4550A'
AND    wot.wot_tos_tas_code = NVL('U001',wot.wot_tos_tas_code)
 
 UPDATE works_order_tasks SET wot_complete='', WOT_LKP_TAS_CODE=''  WHERE 
 wot_tos_tas_code = NVL('A501',wot_tos_tas_code) AND wot_wor_id=(
 SELECT 
	   wor.wor_id
FROM   works_order_tasks wot,      
	   works_order_elements woe,      
	   works_orders wor,      
	   sites sit
WHERE  wot.wot_wor_id = wor.wor_id
AND    woe.woe_wor_id = wor.wor_id
AND    woe.woe_sit_id = sit.sit_id
AND    sit.sit_udk = '_BW4550A'
AND    wot.wot_tos_tas_code = NVL('A501',wot.wot_tos_tas_code)
 )*/
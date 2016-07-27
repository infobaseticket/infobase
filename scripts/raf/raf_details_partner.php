<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<script language="JavaScript">
$(document).ready(function(){
	function after_RAFdetails_save(response){  
		$('#modalspinner').spin(false);
		Messenger().post({
			message: response.responsedata,
			type: response.responsetype,
			showCloseButton: true
		});
	}	
	var options = {
		success: after_RAFdetails_save,
		dataType:  'json'
	};
	$('#form_partner').submit(function(){
	    $(this).ajaxSubmit(options);
	    return false;
	});


	$("#infodetails").hide();
	$('#infobox').click(function(){
		$("#infodetails").slideToggle("fast");
	});


	$('.proposal').change(function(){
		val1=$('#bc_proposal1').val();
		val2=$('#bc_proposal2').val();
		val3=$('#bc_proposal3').val();
		val4=$('#bc_proposal4').val();

		if (this.name==="BC_PROPOSAL1" && val1==="Yes" && (val2==="Yes" || val3==="Yes"  || val4==="Yes")){
			alert('You need to set candidate 2,3 and 4 to NO first!');
			$(this).val('No');
		}
		if (this.name==="BC_PROPOSAL2" && val2==="Yes" && (val1==="Yes" || val3==="Yes"  || val4==="Yes")){
			alert('You need to set candidate 1,3 and 4 to NO first!');
			$(this).val('No');
		}
		if (this.name==="BC_PROPOSAL3" && val3==="Yes" && (val1==="Yes" || val2==="Yes"  || val4==="Yes")){
			alert('You need to set candidate 1,2 and 4 to NO first!');
			$(this).val('No');
		}
		if (this.name==="BC_PROPOSAL4" && val4==="Yes" && (val1==="Yes" || val2==="Yes"  || val3==="Yes")){
			alert('You need to set candidate 1,2 and 3 to NO first!');
			$(this).val('No');
		}
	});

	$('#first_reason').hide();
	$('#firstcomp_all').change(function() {
		if ($(this).is(':checked')){
			$('.firstcomp').each(function() {
				$(this).val('Yes');
			});
			$('#first_reason').hide();
		}
	});
	$('#finalcomp_all').change(function() {
		if ($(this).is(':checked')){
			$('.finalcomp').each(function() {
				$(this).val('Yes');
			});
		}
	});
	$('#stiteacc_all').change(function() {
		if ($(this).is(':checked')){
			$('.stiteacc').each(function() {
				$(this).val('Yes');
			});
		}
	});

	$('.firstcomp').change(function() {
		$('.firstcomp').each(function() {
			if ($(this).val() === "No") {
				$('#first_reason').show();
			}
		});
	
	});
	$("#counter_results1").hide();
	$("#counterreport").change(function(){
		if ($(this).is(':checked')){		
			$("#counter_results1").show("fast");
			$("#counter_results2").hide();
		}else{
			$("#counter_results1").hide();
			$("#counter_results2").show("fast");
		}		
	});

	$('.MISSINGPO').hide();
	$('#DECLARE').change(function() {
		if ($(this).is(':checked')){
			$('.MISSINGPO').hide();
		}else{
			$('.MISSINGPO').show("fast");
		}
	});

	$('#ALLPO').change(function() {
		if ($(this).is(':checked')){
			var actiontype='readyforpac';
		}else{
			var actiontype='notready';
		}
		function after_POcheck(response){  
			$('#modalspinner').spin(false);			
			Messenger().post({
				message: response.msg,
				type: response.rtype,
				showCloseButton: true
			});
			
		}
		var options = {
			success: after_POcheck,
			dataType:  'json',
			data: {
				actiontype: actiontype
			}
		};
		$('#modalspinner').spin('medium');
		$('#form_pacfac').ajaxSubmit(options); 
	   	return false; 
	});
});
</script>
<?php

$query = "Select * FROM BSDS_RAF_PARTNER WHERE RAFID = '".$_POST['rafid']."'";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
   OCIFreeStatement($stmt);
   $amount_of_RAFS=count($res1['RAFID']);
   $BCS_RESULT=unescape_quotes($res1['BCS_RESULT'][0]);
}

if ($REPORT_UPLOAD=="1"){
	$REPORT_UPLOAD_CHECK="checked";
}

if($res1['POCON_CONFIRM'][0]==''){
	$POCON_CONFIRM="Please confirm";
}else{
	$POCON_CONFIRM=$res1['POCON_CONFIRM'][0];
}

if ($_POST['raftype']!="indoor" && $BCS_RESULT==""){
	$BCS_RESULT="BCS excel form to be uploaded in 'files-TAB.";
}
if (substr_count($_POST['actiondo'], 'BASE Delivery (RAF+NET1)')==1  || substr_count($_POST['actiondo'], 'Base Delivery-PO Construction')==1
	 ||substr_count($_POST['actiondo'], 'Base Delivery-PO Acquisition')==1){
	echo "<p style='color:red;font-weight:bold'>No partner data available => NET1 link to be provided or no PO</p>";
}
?>
<div class="panel-group" id="accordion">
<?php

if ($_POST['type']=="MOD Upgrade" or  substr_count($_POST['type'], 'V2')==1) {

	if (substr_count($_POST['actiondo'], 'PARTNER (RAF 0)')==1){
	$changeable_0="changeable";
	$collapse='collapsed';
?> 
	<form action="scripts/raf/raf_actions.php" method="post" id="form_partner">
	<input type="hidden" name="action" value="update_partner_raf_0">
	<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
<?php
	}
?>
	<div class="panel panel-default">
	    <div class="panel-heading <?=$changeable_0?>">
	        <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#acqneed">
	              0. BP NEEDED Y/N
	            </a>
	        </h4>
	    </div>
	    <div id="acqneed" class="panel-collapse <?=$collapse?>">
	    	<div class="form-group">
		        <label for="ACQ_NEEDED_REASON" class="col-sm-12 control-label">Please specify reason why BP IS NEEDED or WHY NOT:</label>
		        <div class="col-sm-12">
		            <textarea class="form-control input-sm" rows="5" name="BP_NEEDED_REASON" id="BP_NEEDED_REASON"><?php echo unescape_quotes($res1['BP_NEEDED_REASON'][0]); ?></textarea>
		        </div>
		    </div>
	    </div>
	</div>
<?php
	if (substr_count($_POST['actiondo'], 'PARTNER (RAF 0)')==1){ ?>
	<br><input type="submit" class="btn btn-default <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?> value="SAVE PARTNER CHANGES 0">
	</form>
	<?
	}
}
	if (substr_count($_POST['actiondo'], 'PARTNER (RAF 1->4)')==1 || substr_count($_POST['actiondo'], 'BASE RF (RAF 8->9+NET1)')==1 ||  substr_count($_POST['actiondo'], 'PARTNER ADD MISSING MS/DOCS')==1){
	$changeable_1_4="changeable";
?> 
	<form action="scripts/raf/raf_actions.php" method="post" id="form_partner">
	<input type="hidden" name="action" value="update_partner_raf_1_4">
	<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
<?	
	}
?>

	<div class="panel panel-default">
	    <div class="panel-heading <?=$changeable_1_4?>">
	        <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#budget">
	              1. BEST CANDIDATE SELECTION
	            </a>
	        </h4>
	    </div>
	    <div id="budget" class="panel-collapse collapse">
	        <div class="panel-body">	
				<table class="table">
				<thead>
				<tr>
					<th>NR</th>
					<th>Date</th>
					<th>Candidate</th>
					<th>Engineer</th>
					<th>BC proposal</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>1.</td>
					<td><input type="text" name="BC_DATE1" VALUE="<?=$res1['BC_DATE1'][0]?>" class="form-control input-sm" size="10"></td>
					<td><input type="text" name="BC_CANDIDATE1" VALUE="<?=$res1['BC_CANDIDATE1'][0]?>" class="form-control input-sm" size="10"></td>
					<td><input type="text" name="BC_ENGINEER1" VALUE="<?=$res1['BC_ENGINEER1'][0]?>" class="form-control input-sm"></td>
					<td><select name="BC_PROPOSAL1" id="bc_proposal1" class="proposal form-control input-sm"><option selected><?=$res1['BC_PROPOSAL1'][0]?></option><option>No</option><option>Yes</option></select></td>
				</tr>
				<tr>
					<td>2.</td>
					<td><input type="text" name="BC_DATE2" VALUE="<?=$res1['BC_DATE2'][0]?>" class="form-control input-sm" size="10"></td>
					<td><input type="text" name="BC_CANDIDATE2" VALUE="<?=$res1['BC_CANDIDATE2'][0]?>"  class="form-control input-sm" size="10"></td>
					<td><input type="text" name="BC_ENGINEER2" VALUE="<?=$res1['BC_ENGINEER2'][0]?>"  class="form-control input-sm"></td>
					<td><select name="BC_PROPOSAL2" id="bc_proposal2" class="proposal form-control input-sm"><option selected><?=$res1['BC_PROPOSAL2'][0]?></option><option>No</option><option>Yes</option></select></td>
				</tr>
				<tr>
					<td>3.</td>
					<td><input type="text" name="BC_DATE3" VALUE="<?=$res1['BC_DATE3'][0]?>" class="form-control input-sm" size="10"></td>
					<td><input type="text" name="BC_CANDIDATE3" VALUE="<?=$res1['BC_CANDIDATE3'][0]?>"  class="form-control input-sm" size="10"></td>
					<td><input type="text" name="BC_ENGINEER3" VALUE="<?=$res1['BC_ENGINEER3'][0]?>"  class="form-control input-sm"></td>
					<td><select name="BC_PROPOSAL3" id="bc_proposal3" class="proposal form-control input-sm"><option selected><?=$res1['BC_PROPOSAL3'][0]?></option><option>No</option><option>Yes</option></select></td>
				</tr>
				<tr>
					<td>4.</td>
					<td><input type="text" name="BC_DATE4" VALUE="<?=$res1['BC_DATE4'][0]?>" class="form-control input-sm" size="10"></td>
					<td><input type="text" name="BC_CANDIDATE4" VALUE="<?=$res1['BC_CANDIDATE4'][0]?>"  class="form-control input-sm" size="10"></td>
					<td><input type="text" name="BC_ENGINEER4" VALUE="<?=$res1['BC_ENGINEER4'][0]?>"  class="form-control input-sm"></td>
					<td><select name="BC_PROPOSAL4" id="bc_proposal4" class="proposal form-control input-sm"><option selected><?=$res1['BC_PROPOSAL4'][0]?></option><option>No</option><option>Yes</option></select></td>
				</tr>
				</tr>
				</tbody>
				</table>
				<div class="form-group">
		            <label for="RFPLAN" class="col-sm-3 control-label">BCS result</label>
		            <div class="col-sm-9">
		                <textarea class="form-control input-sm" rows="5" name="BCS_RESULT" id="BCS_RESULT"><?php echo unescape_quotes($res1['BCS_RESULT'][0]); ?></textarea>
		            </div>
		        </div>
				<? if ($_POST['raftype']!="indoor"){ ?>
				<br>
				<button id="infobox" class="btn btn-default">REJECTION CRITERIA</button><br>
				<div id="infodetails">
					<b>Rejection criteria for a candidate :</b>
					<ul>
			          <li>Antenna height less than 3m above clutter</li>
			          <li>20% criteria not respected</li>
			          <li>When blocking</li>
			          <li>Coverage KPI smaller than requested value on Release and Acceptance form – 5%</li>
			          <li>Number of cabinets for acquisition is less than 2</li>
			          <li>Poor or no chance to get a BP </li>
			          <li>Unprobable or no chance to get a lease</li>
			          <li>Possible sharing at less than 200m</li>
			          <li>Access window to site NOT guaranteed for 24hrs 7*7 for Transmission sites</li>
			          <li>Amount of transmission dishes less than requested – 2 for BTS sites</li>
			          <li>Space requested transmission cabinet not available for BTS sites</li>
			          <li>No space transmission cabinet for Transmission sites</li>
			          <li>Amount of transmission dishes less than requested – 4 for Transmission sites</li>
					 </ul>
					<p>If the quote of the power connection is >= to 30K Euro (cfr SSR), then the candidate can be
					selected as BC. After debate with Base, a go or no go will be given to continue with this candidate </p>
			  	</div>
				<? } ?>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
	    <div class="panel-heading <?=$changeable_1_4?>">
	        <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#siteServey">
	             2. SITE SURVEY REPORT
	            </a>
	        </h4>
	    </div>
	    <div id="siteServey" class="panel-collapse collapse">
	        <div class="panel-body">
		  	NOTE: Report also to be placed on shared drive.<br><br>Files can be uploaded in 'files TAB'<br> 		
		  	</div>
		</div>
	</div>

	<div class="panel panel-default">
	    <div class="panel-heading <?=$changeable_1_4?>">
	        <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#firstComplRep">
	            3. FIRST COMPLIANCY REPORT
	            </a>
	        </h4>
	    </div>
	    <div id="firstComplRep" class="panel-collapse collapse">
	        <div class="panel-body">
		  	<u>Coverage Report including the KPI coverage results for indoor and outdoor sites and polygon coverage
			plot for outdoor sites..</u><br><br>
			Files can be uploaded in 'files TAB'		
		  	</div>
		</div>
	</div>

	<div class="panel panel-default">
	    <div class="panel-heading <?=$changeable_1_4?>">
	        <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#firstComplSt">
	            4. FIRST COMPLIANCY STATEMENT
	            </a>
	        </h4>
	    </div>
	    <div id="firstComplSt" class="panel-collapse collapse">
	        <div class="panel-body">
	        	<div class="form-group">
	                <div class="checkbox">
	                    <label>
	                        <input type="checkbox" name="firstcomp_all" id="firstcomp_all" value="1"> Set all to yes<br>
	                    </label>
	                </div>
	            </div>
	        	<div class="form-group">
	                <label for="FIRST_RF" class="col-sm-7 control-label">Best Candidate/ Upgrade fulfils the RF requirements:</label>
	                <div class="col-sm-5">
	                 <select name="FIRST_RF" id="FIRST_RF" class="form-control input-sm firstcomp"><option><?=$res1['FIRST_RF'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
	        	<div class="form-group">
	                <label for="FIRST_MICROWAVE" class="col-sm-7 control-label">Best Candidate/ Upgrade compliant with additional microwave dishes:</label>
	                <div class="col-sm-5">
	                 <select name="FIRST_MICROWAVE" id="FIRST_MICROWAVE" class="form-control input-sm firstcomp"><option><?=$res1['FIRST_MICROWAVE'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
	        	<div class="form-group">
	                <label for="FIRST_CAB" class="col-sm-7 control-label">Best Candidate/ Upgrade compliant with additional Transmission cabinets:</label>
	                <div class="col-sm-5">
	                 <select name="FIRST_CAB" id="FIRST_CAB" class="form-control input-sm firstcomp"><option><?=$res1['FIRST_CAB'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
	        	<div class="form-group">
	                <label for="FIRST_BTS" class="col-sm-7 control-label">Best Candidate/ Upgrade is compliant with Transmission requirements for BTS sites:</label>
	                <div class="col-sm-5">
	                 <select name="FIRST_BTS" id="FIRST_BTS" class="form-control input-sm firstcomp"><option><?=$res1['FIRST_BTS'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
	        	<div class="form-group">
	                <label for="FIRST_OTHER" class="col-sm-7 control-label">Best Candidate/ Upgrade is compliant with Transmission requirements for BTS sites:</label>
	                <div class="col-sm-5">
	                 <select name="FIRST_OTHER" id="FIRST_OTHER" class="form-control input-sm firstcomp"><option><?=$res1['FIRST_OTHER'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
	            <div class="form-group" id="first_reason">
			        <label for="FIRST_REASON" class="col-sm-4 control-label">Please specify reason why no:</label>
			        <div class="col-sm-8">
			            <textarea class="form-control input-sm" rows="5" name="FIRST_REASON" id="FIRST_REASON"><?php echo unescape_quotes($res1['FIRST_REASON'][0]); ?></textarea>
			        </div>
			    </div>
		    </div>
		</div>
	</div>
<?

	if ((substr_count($_POST['actiondo'], 'PARTNER (RAF 1->4)')==1 || substr_count($_POST['actiondo'], 'BASE RF (RAF 8->9+NET1)')==1) &&
	(substr_count($guard_groups, 'Partner')==1 || substr_count($guard_groups, 'PARTNER_sub')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	<br><input type="submit" class="btn btn-default <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?> value="SAVE PARTNER CHANGES 1->4">
	</form>
	<?
	}


if (substr_count($_POST['actiondo'], 'PARTNER COF (RAF 0)')!=1){
	
	if (substr_count($_POST['actiondo'], 'PARTNER (RAF 5->6)')==1 && $_POST['type']!="MOD Upgrade" &&  substr_count($_POST['type'], 'v2')!=1){
	$changeable_5_6="changeable";
		if ((substr_count($_POST['actiondo'], 'PARTNER (RAF 5->6)')==1) &&
		(substr_count($guard_groups, 'Partner')==1 || substr_count($guard_groups, 'Administrators')==1)  && $_POST['type']!="MOD Upgrade" && substr_count($_POST['type'], 'v2')!=1){
		?>
		<form action="scripts/raf/raf_actions.php" method="post" id="form_partner">
		<input type="hidden" name="action" value="update_partner_raf_5_6">
		<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
		<?php 
		}
	}

	if ($_POST['type']!="MOD Upgrade" && substr_count($_POST['type'], 'v2')!=1){

?>
	<div class="panel panel-default">
	    <div class="panel-heading <?=$changeable_5_6?>">
	        <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#finalCompliancyReport">
	            5. FINAL COMPLIANCY REPORT AFTER LEASE AND BP OK
	            </a>
	        </h4>
	    </div>
	    <div id="finalCompliancyReport" class="panel-collapse collapse">
	        <div class="panel-body">
			  	<ul>
					<li><u>Coverage Report including the KPI coverage results for indoor and outdoor sites</u></li>
					<li><u>Polygon coverage plot for outdoor sites</u></li>
				</ul>
				<br>
				Files can be uploaded in 'files' TAB		
		  	</div>
		</div>
	</div>
<?php
	} ?>

	<div class="panel panel-default">
	    <div class="panel-heading <?=$changeable_5_6?>">
	        <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#finalCompliancyAfter">
	            6.FINAL COMPLIANCY STATEMENT AFTER L&BP OK
	            </a>
	        </h4>
	    </div>
	    <div id="finalCompliancyAfter" class="panel-collapse collapse">
	        <div class="panel-body">
	        	<div class="form-group">
	                <div class="checkbox">
	                    <label>
	                        <input type="checkbox" name="firstcomp_all" id="finalcomp_all" value="1"> Set all to yes<br>
	                    </label>
	                </div>
	            </div>
	        	<div class="form-group">
	                <label for="FINAL_RF" class="col-sm-7 control-label">Best Candidate / Upgrade fullfils the RF requirements:</label>
	                <div class="col-sm-5">
	                 <select name="FINAL_RF" id="FINAL_RF" class="form-control input-sm finalcomp"><option><?=$res1['FINAL_RF'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
	            <div class="form-group">
	                <label for="FINAL_MICROWAVE" class="col-sm-7 control-label">Best Candidate / Upgrade compliant with microwave dishes:</label>
	                <div class="col-sm-5">
	                 <select name="FINAL_MICROWAVE" id="FINAL_MICROWAVE" class="form-control input-sm finalcomp"><option><?=$res1['FINAL_MICROWAVE'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
	            <div class="form-group">
	                <label for="FINAL_CAB" class="col-sm-7 control-label">Best Candidate / Upgrade compliant with Transmission cabinets:</label>
	                <div class="col-sm-5">
	                 <select name="FINAL_CAB" id="FINAL_CAB" class="form-control input-sm finalcomp"><option><?=$res1['FINAL_CAB'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
	            <div class="form-group">
	                <label for="FINAL_BTS" class="col-sm-7 control-label">Best Candidate / Upgrade is compliant with Transmission requirements for BTS sites:</label>
	                <div class="col-sm-5">
	                 	<select name="FINAL_BTS" id="FINAL_BTS" class="form-control input-sm finalcomp"><option><?=$res1['FINAL_BTS'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
	          	<div class="form-group">
	                <label for="FINAL_OTHER" class="col-sm-7 control-label">Best Candidate / Upgrade is compliant with other requirements:</label>
	                <div class="col-sm-5">
	                 <select name="FINAL_OTHER" id="FINAL_OTHER" class="form-control input-sm finalcomp"><option><?=$res1['FINAL_OTHER'][0]?></option><option>Yes</option><option>No</option></select>
	            	</div>
	            </div>
		  	</div>
		</div>
	</div>
<?
	if ((substr_count($_POST['actiondo'], 'PARTNER (RAF 5->6)')==1) &&
	(substr_count($guard_groups, 'Partner')==1 || substr_count($guard_groups, 'PARTNER_sub')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	<input type="submit" class="btn btn-default <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?> value="SAVE PARTNER CHANGES 5->6">
	</form>
	<?
	}
}


	if (substr_count($_POST['actiondo'], 'PARTNER (RAF 7)')==1){ 
		$changeable_7="changeable";
	?>
	<form action="scripts/raf/raf_actions.php" method="post" id="form_partner">
	<input type="hidden" name="action" value="update_partner_raf_7">
	<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
<?
	}
?>
	<div class="panel panel-default">
	    <div class="panel-heading <?=$changeable_7?>">
	        <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#siteAcceptance">
	            7. RF PACK SUBMITTAL
	            </a>
	        </h4>
	    </div>
	    <div id="siteAcceptance" class="panel-collapse collapse">
	        <div class="panel-body">
	        	
	        	<b><u>Radio Site integration drivetest / walktest (for indoor)</u></b><br>
	        	<div class="form-group">
	                <div class="checkbox">
	                    <label>
	                        <input type="checkbox" name="REPORT_UPLOAD" id="counterreport" value="1" <?=$REPORT_UPLOAD_CHECK?>>FSIDT/SIC report has been uploaded to Infobase
	                    </label>
	                </div>
	            </div>
	            <div id="counter_results1">
		        	<div class="form-group">
		                <label for="COMPLIANT" class="col-sm-7 control-label">Is the report compliant?</label>
		                <div class="col-sm-5">
		                 <select name="COMPLIANT" id="COMPLIANT" class="form-control input-sm"><option><?=$res1['COMPLIANT'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		        </div>
		        <div id="counter_results2">
		        	<div class="form-group">
		                <div class="checkbox">
		                    <label>
		                        <input type="checkbox" name="stiteacc_all" id="stiteacc_all" value="1"> Set all to yes<br>
		                    </label>
		                </div>
		            </div>
		            <div class="form-group">
		                <label for="CNT_TCH" class="col-sm-7 control-label">TCH Assignment Failure Rate (<10%, averages measured during the 3 day period):</label>
		                <div class="col-sm-5">
		                 <select name="CNT_TCH" id="CNT_TCH" class="form-control input-sm stiteacc"><option><?=$res1['CNT_TCH'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="CNT_BLOCKING" class="col-sm-7 control-label">Customer Perceived Blocking Rate (<10%, averages measured during the 3 day Busy Hour):</label>
		                <div class="col-sm-5">
		                 <select name="CNT_BLOCKING" id="CNT_BLOCKING" class="form-control input-sm stiteacc"><option><?=$res1['CNT_BLOCKING'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="CNT_DROPPED" class="col-sm-7 control-label">TCH Dropped Call Rate (<5%, averages measured during the 3 day period):</label>
		                <div class="col-sm-5">
		                 <select name="CNT_DROPPED" id="CNT_DROPPED" class="form-control input-sm stiteacc"><option><?=$res1['CNT_BLOCKING'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="CNT_SDCCH" class="col-sm-7 control-label">SDCCH Establishment Failure Rate (<10%, averages measured during the 3 day period):</label>
		                <div class="col-sm-5">
		                 <select name="CNT_SDCCH" id="CNT_SDCCH" class="form-control input-sm stiteacc"><option><?=$res1['CNT_SDCCH'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="CNT_UPQUAL" class="col-sm-7 control-label">Upqual H/O (<25%, averages measured during the 3 day period):</label>
		                <div class="col-sm-5">
		                 <select name="CNT_UPQUAL" id="CNT_UPQUAL" class="form-control input-sm stiteacc"><option><?=$res1['CNT_UPQUAL'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="CNT_DQUAL" class="col-sm-7 control-label">Dqual H/O (<25%, averages measured during the 3 day period):</label>
		                <div class="col-sm-5">
		                 <select name="CNT_DQUAL" id="CNT_DQUAL" class="form-control input-sm stiteacc"><option><?=$res1['CNT_DQUAL'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="CNT_SLEEPING" class="col-sm-7 control-label">Sleeping Cell (NO):</label>
		                <div class="col-sm-5">
		                 <select name="CNT_SLEEPING" id="CNT_SLEEPING" class="form-control input-sm stiteacc"><option><?=$res1['CNT_SLEEPING'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="CNT_AVAILABILITY" class="col-sm-7 control-label">Cell Availability (>98%, averages measured during the 3 day period):</label>
		                <div class="col-sm-5">
		                 <select name="CNT_AVAILABILITY" id="CNT_AVAILABILITY" class="form-control input-sm stiteacc"><option><?=$res1['CNT_AVAILABILITY'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="CNT_SDCCHDROP" class="col-sm-7 control-label">SDCCH Drop Call Rate (<15%, averages measured during the 3 day period):</label>
		                <div class="col-sm-5">
		                 <select name="CNT_SDCCHDROP" id="CNT_SDCCHDROP" class="form-control input-sm stiteacc"><option><?=$res1['CNT_SDCCHDROP'][0]?></option><option>Yes</option><option>No</option></select>
		            	</div>
		            </div>
		            <br><br>
					<b><u></u>Transmission</u></b><br>
					<i>Key points checklist for Transmission. Compliancy of:</i>K<br>
					<div class="form-group">
		                <label for="TX_TOTALMW" class="col-sm-7 control-label">Total amount of MW antenna’s:</label>
		                <div class="col-sm-5">
		                 <input type="text" name="TX_TOTALMW" id="TX_TOTALMW" value="<?=$res1['TX_TOTALMW'][0]?>" class="form-control input-sm">
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="TX_BEARINGS" class="col-sm-7 control-label">MW antenna’s bearings:</label>
		                <div class="col-sm-5">
		                 <input type="text" name="TX_BEARINGS" id="TX_BEARINGS" value="<?=$res1['TX_BEARINGS'][0]?>" class="form-control input-sm">
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="TX_AMOUNT" class="col-sm-7 control-label">Amount of Transmission cabinets:</label>
		                <div class="col-sm-5">
		                 <input type="text" name="TX_AMOUNT" id="TX_AMOUNT" value="<?=$res1['TX_AMOUNT'][0]?>" class="form-control input-sm">
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="TX_LOCATION" class="col-sm-7 control-label">Location of Transmission cabinets:</label>
		                <div class="col-sm-5">
		                 <input type="text" name="TX_LOCATION" id="TX_LOCATION" value="<?=$res1['TX_LOCATION'][0]?>" class="form-control input-sm">
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="TX_TYPE" class="col-sm-7 control-label">Type of Transmission cabinets:</label>
		                <div class="col-sm-5">
		                 <input type="text" name="TX_TYPE" id="TX_TYPE" value="<?=$res1['TX_TYPE'][0]?>" class="form-control input-sm">
		            	</div>
		            </div>
		            <div class="form-group">
		                <label for="TX_TRAY" class="col-sm-7 control-label">Cable tray (if applicable between new and existing cabinets):</label>
		                <div class="col-sm-5">
		                 <input type="text" name="TX_TRAY" id="TX_TRAY" value="<?=$res1['TX_TRAY'][0]?>" class="form-control input-sm">
		            	</div>
		            </div>
		        </div>
			</div>
		</div>
	</div>	
<?

	if ((substr_count($_POST['actiondo'], 'PARTNER (RAF 7)')==1) &&
	(substr_count($guard_groups, 'Partner')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	<input type="submit" class="btn btn-default <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?> value="SAVE PARTNER CHANGES 7">
	</form>
	<?
	}
?>
</div>	
<?php


	if  (substr_count($_POST['actiondo'], 'PARTNER (RAF 8)')==1){
		$changeable_8="changeable";	
		$collapse8='in';
	}

	$queryPO = "Select PARTNER_VALREQ FROM BSDS_RAFV2 WHERE RAFID = '".$_POST['rafid']."'";

	$stmtPO = parse_exec_fetch($conn_Infobase, $queryPO, $error_str, $resPO);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmtPO);
		$amount=count($resPO['PARTNER_VALREQ']);
	}
	if ($amount==0){
		$check_PO='';
	}else if ($resPO['PARTNER_VALREQ'][0]=='PAC CONFIRMED' or $resPO['PARTNER_VALREQ'][0]=='PAC&FAC CONFIRMED' or $resPO['PARTNER_VALREQ'][0]=='FAC CONFIRMED'){
		$check_PO='CHECKED';
	}
?>
	<div class="panel panel-default">
	    <div class="panel-heading <?=$changeable_8?>">
	        <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#poprcheck">
	            8. PAC request for validation
	            </a>
	        </h4>
	    </div>
	    <div id="poprcheck" class="panel-collapse collapse <?=$collapse8?>">
	        <div class="panel-body" style="margin: 0 10px">
	         <?php
	    if ($resPO['PARTNER_VALREQ'][0]=='READY FOR PAC' or $resPO['PARTNER_VALREQ'][0]=='PAC CONFIRMED'
	    	or $resPO['PARTNER_VALREQ'][0]=='READY FOR PAC&FAC' or $resPO['PARTNER_VALREQ'][0]=='PAC&FAC CONFIRMED'
	    	or $resPO['PARTNER_VALREQ'][0]=='READY FOR FAC' or $resPO['PARTNER_VALREQ'][0]=='FAC CONFIRMED'){

	    	if ($resPO['PARTNER_VALREQ'][0]=='READY FOR PAC' or $resPO['PARTNER_VALREQ'][0]=='READY FOR PAC&FAC'){
	    		$id='ALLPO';
	    	}else{
	    		$id='ALLPOconfirmed';
	    	}

	        if ((substr_count($_POST['actiondo'], 'PARTNER (RAF 8)')==1) && (substr_count($guard_groups, 'Partner')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	        <form action="scripts/raf/raf_actions.php" method="post" class="form_pocheck" id="form_pacfac">
			<input type="hidden" name="action" value="update_pacfacready">
			<input type="hidden" name="RAFID" value="<?=$_POST['rafid']?>">
			<input type="hidden" name="prev_PARTNER_VALREQ" value="<?=$resPO['PARTNER_VALREQ'][0]?>">
	      	<?php
			} ?>
	        <div class="checkbox">
			    <label>
			      <input type="checkbox" name="ALLPO" id="<?=$id?>" <?=$check_PO?>> All PO's have been received.<br>All deliverables have been checked up-front.
			    </label>
			  </div>       	
	        </div>
	        <?php
	        if ((substr_count($_POST['actiondo'], 'PARTNER (RAF 8)')==1) && (substr_count($guard_groups, 'Partner')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
			</form>
			<?php
			} 
		}else if ($resPO['PARTNER_VALREQ'][0]=='REJECTED'){
			echo "<font color='red'>Please first confirm the rejection by setting 'PARTNER VALIDATION REQUEST' to <b>OK</b></font>";
		}
		?>
	    </div>
	</div>

	
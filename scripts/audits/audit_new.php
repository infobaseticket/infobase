<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_other","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("audit_procedures.php");


if ($_POST['auditid']=="planning"){
	$goto="scripts/audits/auditplanning.php";
	$planning="yes";
}else{
	$goto="scripts/audits/audit.php";
}

?>
<link rel="stylesheet" href="scripts/audits/audit.css" type="text/css">

<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-ui/datepicker/ui.datepicker.css" type="text/css"></link>
<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-ui/datepicker/ui.datepicker.js"></script>


<script language="javascript">
$(document).ready(function() {
	audittype1=$("#audittype1").val();
	audittype2=$("#audittype2").val();

	$(".sitelist_audit").autocomplete("scripts/audits/auto_fieldlists.php?audittype1="+audittype1+"&audittype2="+audittype2+"&list=LIST1", {
		width: 260,
		scroll: true,
		minChars:2,
		extraParams: {
	       audittype1: function() { return $("#audittype1").val(); },
		   audittype2: function() { return $("#audittype2").val(); }
	   }

	});

	$(".sitelist_audit").result(function(event, data, formatted) {
		if(data){
				$.ajax({
				  url: 'scripts/audits/auto_fieldlists.php',
				  type: 'POST',
				  data: "action=net1_max_date&sitetype="+data,
				  success: function(reponse) {
				   	$('#latestnet1').html(reponse);
				    //alert('Load was performed.p'+reponse);
				  }
				});
		}
	 //$("#result").html( !data ? "No match!" : "Selected: " + formatted);
	});

	$('.dateselecter').datepicker({rangeSelect: false,dateFormat: 'dd/mm/yy'});

	$('#Savebutton').click(function() {
		var options = {
			success:  after_AUDIT_save,
			beforeSubmit:validateNewAUDIT,
			dataType:  'json'
		};
		$('#new_audit_form'+$.session("tabid")).ajaxSubmit(options);
		return false;
    });

    $('.Cancelbutton').click(function() {

        $("#auditnew"+ $.session("tabid")).slideToggle("slow");
        return false;
    });

	function after_AUDIT_save(response)  {
	 	if (response.responsetype === "info"){
			$("#auditresult"+$.session("tabid")).load("<?=$goto?>", { site:'<?=$_SESSION['sitesearch'];?>'
			}, function(){
				createGrowl(response.responsedata,false);
				$("#loadingbar"+$.session("tabid")).hide();
				return false;
			});
		}
	}
	function validateNewAUDIT(formData, jqForm, options){

		var form = jqForm[0];

		if (form.audittype1.value === "" ) {
			alert('You need to provide an audit type!');
		    return false;
		}

		if (form.siteaudit.value === "") {
			alert('You need to provide a site!');
			return false;
		}


		$("#loadingbar"+$.session("tabid")).show('fast');
		return true;

	}

	$("#status_audit").change(function() {
		var partner=$("#inspectionpartner").val();
		if($("#status_audit").val()=='FAILED' && (partner==='KPNGB NSO' ||partner==='KPNGB RF' || partner==='KPNGB TX' || partner==='KPNGB IMPLEMENTATION')){
			$("#reasonblock").show('fast');
		}else{
			$("#reasonblock").hide();
		}
	});

	$("#audittype2").hide();
	$(".partnerNotKPNGB").hide();

	$("#inspectionpartner").change(function() {
		var partner=$("#inspectionpartner").val();
		if(partner==='KPNGB NSO' || partner==='KPNGB RF' || partner==='KPNGB TX' || partner==='KPNGB IMPLEMENTATION'){
			$(".partnerKPNGB").hide();
			$(".partnerNotKPNGB").show('fast');
			$("#audittype2").show('fast');
			if ($("#status_audit").val()=='FAILED'){
				$("#reasonblock").show('fast');
			}
		}else{
			$(".partnerKPNGB").show('fast');
			$(".partnerNotKPNGB").hide();
			$("#audittype2").hide();
			$("#reasonblock").hide();
		}
	});

	$("#servicepartner1").change(function() {
		var spartner=$("#servicepartner1").val();
		if(spartner==='ALUROL' || spartner==='ALUOP'){
			$('#servicepartner2').children().remove().end().append('<option value=VW>VW</option>').append('<option value=BENCHMARK>BENCHMARK</option>').append('<option value=SPEEDWORKS>SPEEDWORKS</option>');
		}else if(spartner==='BENCHMARK'){
			$('#servicepartner2').children().remove().end().append('<option value=VW>VW</option>').append('<option value=BENCHMARK>BENCHMARK</option>').append('<option value=SPEEDWORKS>SPEEDWORKS</option>').append('<option value=NA>NA</option>');
		}
	});


});
</script>


<?php


if ($_POST['auditid']!="" && $_POST['auditid']!="planning"){

	$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
	$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
	OCIExecute($stmt,OCI_DEFAULT);
	$query= query_audit('',$_POST['auditid'],'','','','','','','','');
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
	}
}
?>
<div id="new_audit_container1">
	<form action="scripts/audits/audit_actions.php" method="post" id="new_audit_form<?=$_POST['tabid']?>">
	<input type="hidden" name="action" value="insert_new_audit">
	<input type="hidden" name="auditid" value="<?=$_POST['auditid'];?>">
	<table>
	<tr>
			<td class="title">Site + Type:</td>
			<? if ($_POST['auditid']=="planning"){ ?>
	 		<td><input type="text" name="siteaudit" class="sitelist_audit" size="40"value="<? echo $res1['SITE'][0]; ?>"></td>
	 		<? }else{ ?>
	 		<td><? echo $res1['SITE'][0]; ?></td>
	 		<input type="hidden" name="siteaudit" value="<? echo $res1['SITE'][0]; ?>">
	 		<? } ?>
	</tr>
	<tr>
			<td class="title">Latest MS NET1:</td>
			<td id="latestnet1"></td>
	</tr>
	<tr>
			<td class="title">Inspection type: </td>
			<? if ($_POST['auditid']=="planning"){ ?>
	 		<td><select name="audittype1" id="audittype1">
	 		<option selected value="<? echo $res1['TYPE'][0]; ?>"><? echo $res1['TYPE'][0]; ?></option>
	 		<?
		 	if (substr_count($guard_groups, 'Cofely')!=1 && substr_count($guard_groups, 'DLConsulting')!=1 ){?>
	 		<option value="NA">NA</option>
			<option value="Admin" class="partnerNotKPNGB">Admin</option>
			<option value="Physical" class="partnerNotKPNGB">Physical</option>
			<? } ?>
			<option value="A1 Inspection During ACQ" class="partnerKPNGB">A1 Inspection During ACQ</option>
			<option value="A2 Inspection at end of ACQ" class="partnerKPNGB">A2 Inspection at end of ACQ</option>
			<option value="C1 Inspection During CON" class="partnerKPNGB">C1 Inspection During CON</option>
			<option value="C2 Inspection at end of CON" class="partnerKPNGB">C2 Inspection at end of CON</option>
			<option value="O1 Operations Civil Inspection" class="partnerKPNGB">O1 Operations Civil Inspection</option>
			<option value="O2 Operations Technical Inspection" class="partnerKPNGB">O2 Operations Technical Inspection</option>
			</select>
			<select name="audittype2" id="audittype2">
			<option value="<? echo $res1['TYPE2'][0]; ?>" selected><? echo $res1['TYPE2'][0]; ?>
			<option value="NA">NA</option>
			<option value="Building Permit">Building Permit</option>
	 		<option value="Environmental">Environmental Permit</option>
			<option value="Start of works letter">Start of works letter</option>
			<option value="Lease">Lease</option>
			<option value="Health and safety">Health and safety</option>
			<option value="Operations">Operations</option>
			<option value="RF">RF</option>
			<option value="TX">TX</option>
			<option value="Radiation">Radiation Hazard</option>
			</select></td>

			<? }else{ ?>
			<td><? echo $res1['TYPE'][0]." ".$res1['TYPE2'][0]; ?></td>
			<input type="hidden" name="audittype1" value="<? echo $res1['TYPE'][0]; ?>">
			<input type="hidden" name="audittype2" value="<? echo $res1['TYPE2'][0]; ?>">
	 		<? } ?>
	</tr>
	<tr>
			<td class="title">Assigned inspection Partner: </td>
			<? if ($_POST['auditid']=="planning"){ ?>
	 		<td><select name="inspectionpartner" id="inspectionpartner">
	 		<option value="ETS">ETS</option>
	 		<option value="TERUSUS">TERUSUS</option>
	 		<option value="<? echo $res1['INSPECTIONPARTNER'][0];?>" selected><? echo $res1['INSPECTIONPARTNER'][0];
	 		if (substr_count($guard_groups, 'DLConsulting')!=1 ){ ?>
	 		<option value="COFELY SERVICES">COFELY SERVICES</option>
	 		<? }
	 		if (substr_count($guard_groups, 'Cofely')!=1  ){ ?>
	 		<option value="DL-CONSULTING">DL-CONSULTING</option>
			<? }
	 		if (substr_count($guard_groups, 'Cofely')!=1 && substr_count($guard_groups, 'DLConsulting')!=1 ){?>
			<option value="KPNGB IMPLEMENTATION">KPNGB IMPLEMENTATION</option>
			<option value="KPNGB NSO">KPNGB NSO</option>
			<option value="KPNGB TX">KPNGB TX</option>
			<option value="KPNGB RF">KPNGB RF</option>
			<? } ?>
			</select>
			</td>
			<? }else{ ?>
				<td><? echo $res1['INSPECTIONPARTNER'][0]; ?></td>
				<input type="hidden" name="inspectionpartner" value="<? echo $res1['INSPECTIONPARTNER'][0]; ?>">
	 		<? } ?>
	</tr>

	<? if ($_POST['auditid']=="planning"){ ?>
	<tr>
		<td class="title">Responsible Partner: </td>
		<td><select name="servicepartner1" id="servicepartner1">
		<option selected><? echo $res1['SERVICEPARTNER1'][0]; ?>
		<option value="ALUROL">ALU ROLL-OUT</option>
		<option value="BENCHMARK">BENCHMARK</option>
		<option value="ALUOP">ALU OPERATIONS</option>
		</select>
		<select name="servicepartner2" id="servicepartner2">
		<option selected><? echo $res1['SERVICEPARTNER2'][0]; ?>
		</select>
		</td>

	<? }else{?>
		<tr>
		<td class="title">Responsible Partner / Subco: </td>
		<td><? echo $res1['SERVICEPARTNER1'][0]; ?> / <? echo $res1['SERVICEPARTNER2'][0]; ?> </td>
		<input type="hidden" name="servicepartner1" value="<? echo $res1['SERVICEPARTNER1'][0]; ?>">
		<input type="hidden" name="servicepartner2" value="<? echo $res1['SERVICEPARTNER2'][0]; ?>">
	<? } ?>
	</tr>
	<? if ($_POST['auditid']!="planning"){ ?>
		<tr>
		<td class="title">Site Engineer: </td>
		<td><? echo $res1['SITEENG'][0]; ?> </td>
		</tr>
		<tr>
		<td class="title">H&S Coordinator: </td>
		<td><? echo $res1['HSCOORD'][0]; ?> </td>
		</tr>
		<input type="hidden" name="HSCOORD" value="<? echo $res1['HSCOORD'][0]; ?>">
		<input type="hidden" name="SITEENG" value="<? echo $res1['SITEENG'][0]; ?>">
	<? } ?>
	<? if ($_POST['auditid']!="planning"){ ?>
	<tr>
		<td class="title">Audit date:</td>
 		<td><input type="text" name="auditdate" class="dateselecter" size="10"></td>
	</tr>
	<? }
	if ($_POST['auditid']!="planning"){ ?>
	<tr>
	<td>Punches: </td>
	<td>
	Punches A: <select name="PUNCHA">
	<? for($i=0;$i<=60;$i++){ ?>
	<option><?=$i?></option>
	<? } ?>
	</select><br>
	Punches B: <select name="PUNCHB">
	<? for($i=0;$i<=60;$i++){ ?>
	<option><?=$i?></option>
	<? } ?>
	</select><br>
	Punches C: <select name="PUNCHC">
	<? for($i=0;$i<=60;$i++){ ?>
	<option><?=$i?></option>
	<? } ?>
	</td>
	</tr>
	<? } ?>
	<? if ($_POST['auditid']!="planning"){ ?>
	<tr>
			<td colspan="2"><br><span class="title">Comments:<br><textarea name="comments" cols="45" rows="4"></textarea></td>
	</tr>
	<? }
	if ($_POST['auditid']=="planning"){ ?>
	<tr>
		<td colspan="2"><br><span class="title">Reason of Inspection:<br><textarea name="reason_comments" cols="45" rows="4"></textarea></td>
	</tr>

	<tr>
		<td colspan=2>
			<table id="reasonblock" style="display:none;">
			<tr>
				<td class="title">Reason:</td>
				<td>
				<select name="reason" id="reason"><option value="">Please select</option>
				<option value="R1">Info not on shared drive</option>
				<option value="R2">Info not complete</option>
				<option value="R3">Content incorrect</option>
				<option value="R4">Document not signed (validated) by ALU</option>
				<option value="R5">NET1 not ok</option>
				</select></td>
			</tr>
			</table>
		</td>
	</tr>

	<? }else{ ?>
	<input type="hidden" name="reason_comments" value="<? echo $res1['REASON_COMMENTS'][0]; ?>">
	<? } ?>

	<tr>
	<td colspan=2>
		<input type="button" value="SAVE" id="Savebutton">
		<input type="button" value="Cancel" class="Cancelbutton">
	</td>
	</tr>
	</table>
	</form><br>
</div>
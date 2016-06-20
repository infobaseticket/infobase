<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-ui/datepicker/ui.datepicker.css" type="text/css"></link>
<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-ui/datepicker/ui.datepicker.js"></script>

<link rel="stylesheet" href="scripts/audits/audit.css" type="text/css">

<script language="JavaScript">
$(document).ready( function() {

	$("#content").css({'overflow-x' : 'scroll', 'overflow-y' : 'hidden'});

	var options = {
    target:  '#reportOutput',
    success:    function() {
		$("#reportOutput").show('fast');}
	};
	// attach handler to form's submit event
	$('#Report1Form').submit(function() {
		$("#reportOutput").hide('fast');
	    $(this).ajaxSubmit(options);
	    return false;
	});

	$('#daterange').datepicker({rangeSelect: true,dateFormat: 'dd/mm/yy'});
});
</script>

<?
$regions=array(
    0=>"NA",
	1=>"AN",
	2=>"BW",
	3=>"BX",
	4=>"HT",
	5=>"LG",
	6=>"LI",
	7=>"LX",
	8=>"NR",
	9=>"OV",
	10=>"VB",
	11=>"WV",
	12=>"MT",
);
?>
<form action="scripts/reports/auditreport_type.php" name="fm" method="post" id="Report1Form">
Filter <b>on region = </b>
<select name="region">
<?
foreach ($regions as $key => $value){
?>
<option><?=$value?></option>
<?}?>
</select>
<br>
AND <b>Inspection type = </b></td>
<select name="audittype1" id="audittype1">
<?
	if (substr_count($guard_groups, 'Cofely')!=1 && substr_count($guard_groups, 'DLConsulting')!=1 ){?>
	<option value="">NA</option>
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
<option value="">NA</option>
	<option value="Building Permit">Building Permit</option>
	<option value="Environmental">Environmental Permit</option>
	<option value="Start of works letter">Start of works letter</option>
	<option value="Lease">Lease</option>
	<option value="Health and safety">Health and safety</option>
	<option value="Operations">Operations</option>
	<option value="RF">RF</option>
	<option value="TX">TX</option>
	<option value="Radiation">Radiation Hazard</option>
</select>
<br>
AND
<select name="datefilter">
<option value="">NA</option>
<option value="CREATION_DATE">Creation Date</option>
<option value="AUDIT_DATE">Audit Date</option>
</select>
IS BETWEEN DATES
<input type="text" name="daterange" id="daterange">
<br>
<b>Order by:</b>
<select name="orderby">
<option value="ID">AUDIT ID</option>
<option value="SITE">SITE ID</option>
<option value="CREATION_DATE">Creation Date</option>
<option value="AUDIT_DATE">Audit Date</option>
</select>
<select name="order">
<option value="ASC">Ascending</option>
<option value="DESC">Descending</option>
</select>
<input type="checkbox" name="xlsprint" value="yes">
Export as xls?<input type="hidden" name="view" value="RAF">
<input type="reset" value="Reset!">
<input type="submit" value="DISPLAY">
</form>
<hr>

<div id="reportOutput"></div>

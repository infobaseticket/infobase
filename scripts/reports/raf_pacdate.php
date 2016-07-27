<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-ui/datepicker/ui.datepicker.css" type="text/css">
<link rel="stylesheet" href="scripts/reports/reports.css" type="text/css">
<link rel="stylesheet" href="scripts/raf/bsds_raf.css" type="text/css">

<script language="JavaScript">
$(document).ready( function() {

	$("#content").css({'overflow-x' : 'scroll', 'overflow-y' : 'hidden'});

	var options = {
    target:  '#reportOutput',
    success:    function() {
		//$("#loading").hide('fast');
		$("#reportOutput").show('fast');}
	};
	// attach handler to form's submit event
	$('#Report1Form').submit(function() {
		//$("#loading").show('fast');
		$("#reportOutput").hide('fast');
	    // submit the form
	    $(this).ajaxSubmit(options);
	    // return false to prevent normal br
	    return false;
	});
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

<br>
<form action="scripts/reports/rafreport_type.php" name="fm" method="post" id="Report1Form">
<input type="hidden" name="leadtimes" value="<? echo $_POST['leadtimes'];?>">
<input type="hidden" name="actionby" value="All">
<input type="hidden" name="pacdate" value="yes">
<b>Filter</b> on region:
<select name="region">
<?
foreach ($regions as $key => $value){
?>
<option><?=$value?></option>
<?}?>
</select>
AND Type:
<select name="type" id="type">
<option>NA</option><
<option>New Macro</option><!-- radio -->
<option>New Micro</option><!-- radio -->
<option>New Indoor</option><!-- radio -->
<option>New CTX site</option><!-- radio -->
<option>CAB Upgrade</option><!-- radio -->
<option>TECHNO Upgrade</option><!-- radio -->
<option>ANT Upgrade</option><!-- radio -->
<option>IND Upgrade</option>
<option>ASC Upgrade</option><!-- radio -->
<option>RPT Upgrade</option><!-- radio -->
<option>New Replacement</option><!-- others + radio -->
<option>New Mobile Truck</option><!-- radio -->
<option>Replacement Request</option><!-- others + radio -->
<option>Move Request</option><!-- others -->
<option>CWK Upgrade</option><!-- others -->
<option>Dismantling</option><!-- others -->
<option>CTX Upgrade</option><!-- TXMN -->
<option>SWAP Upgrade</option><!-- TXMN -->
<option>MSH Upgrade</option><!-- Delivery -->
</select>
AND phase:
<select name="phase" id="phase">
<option>All</option>
<option>Phase 1</option>
<option>Phase 2</option>
<option>Phase 3</option>
<option>Phase 4</option>
<option>Mini RPT</option>
<option>Network Driven</option>
<option>Commercial Driven</option>
<option>Adding UMTS900 to UMTS2100</option>
</select>
<? if (substr_count($guard_groups, 'Base_TXMN')!=1){ ?>
	AND allocated to:
	<select name="allocated" id="allocated">
	<? if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
	<option>ALL</option>
	<? } ?>
	<? if (substr_count($guard_groups, 'Alcatel')==1 || substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
		<option>ALU</option>
	<? } ?>

	<? if (substr_count($guard_groups, 'Benchmark')==1 || substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
			<option>BENCHMARK</option>
	<? } ?>

	<? if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
	<option>KPNGB</option>
	<? } ?>
	</select>
<? } ?>

<b>Order</b> by:
<select name="orderby">
<option value="SITEID">SITE ID</option>
<option value="RAFID">RAF ID</option>
<option value="CREATION_DATE">Creation Date</option>
<option value="NET1_LINK">NET1 LIK</option>
</select>
<select name="order">
<option value="ASC">Ascending</option>
<option value="DESC">Descending</option>
</select><br/>
<? if ($_POST['leadtimes']!="yes" && substr_count($guard_groups, 'Base_TXMN')!=1){?>
<input type="checkbox" name="xlsprint" value="yes">XLS? (In xls NET1 dates are added)
<? } ?>
<input type="hidden" name="view" value="RAF">
<input type="submit" value="DISPLAY">
</form>
<hr>

<div id="reportOutput"></div>

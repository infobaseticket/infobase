<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<script language="JavaScript">
$(document).ready( function() {
	$("body").on("click","#displayLosform",function( e ){
		$('#spinner').spin('medium');
		$("#LOS").hide('fast');
		var options = {
	    	target:  '#LOS',
	    	success:    function() {
				$("#LOS").show('fast');
				$('#spinner').spin(false);
			},
			url:'scripts/los/los.php'
		};

		$("#LOS").hide('fast');
		$("#LosReportForm").ajaxSubmit(options);		
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
<div class="span3" id="leftcontent">
	<div class="well sidebar-nav" id="sidebar-nav"> 
		<form action="scripts/los/los.php" name="fm" method="post" id="LosReportForm">
			<input type="hidden" name="viewtype" value="report">
			<b>Action required by:</b><br>
			<select name="actionby">
			<option value='Partner Processing'>Partner Processing</option>
			<option value='Partner Reporting'>Partner Reporting</option>
			<option>TXMN Resulting</option>
			<option>Canceled</option>
			</select><br>
			<b>Filter:</b> Type:<br>
			<select name="type">
			<option value="">All</option>
			<option value="NB">New build</option>
			<option value="ST">Standard</option>
			<option value="RSL">Received Signal Level</option>
			</select><br>
			Region:<br>
			<select name="region">
			<?
			foreach ($regions as $key => $value){
			?>
			<option><?=$value?></option>
			<?}?>
			</select><br>
			<b>Order</b> by:<br>
			<select name="orderby">
			<option value="SITEA">SITE ID A</option>
			<option value="SITEB">SITE ID B</option>
			<option value="ID">LOS ID</option>
			<option value="CREATION_DATE">Creation Date</option>
			</select>
			<select name="order">
			<option value="ASC">Ascending</option>
			<option value="DESC">Descending</option>
			</select><br>
			allocated to:<br>
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
			</select><br>
			<input type="hidden" name="view" value="LOS">
			<input type="submit" value="DISPLAY" id="displayLosform" class="btn btn-primary">
			</form>
        <div id="search_output"></div>
    </div><!--/.well -->
</div><!--/span-->
<div class="span9" id="rightcontent">
    <div id="maincontent" class="maincontent">
 		<div id="LOS"></div>
    </div><!--/maincontent-->
</div><!--/span-->


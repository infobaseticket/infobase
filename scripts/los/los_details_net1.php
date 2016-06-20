<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
include('los_functions.php');

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query="SELECT SIT_UDK,SIT_ADDRESS, SIT_X_COORDINATE, SIT_Y_COORDINATE, SITE_TYPE_LDE_DESC, POSTCODE, LATESTMILESTONE from VW_NET1_ALL_NEWBUILDS WHERE trim(SIT_UDK) LIKE '%".strtoupper($_POST['siteIDA'])."'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $resA);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_NEWA=count($resA['SIT_UDK']);
	$SIT_ADDRESSA=str_replace(",","<br>",$resA['SIT_ADDRESS'][0]);
}

$query="SELECT SIT_UDK,SIT_ADDRESS, SIT_X_COORDINATE, SIT_Y_COORDINATE, SITE_TYPE_LDE_DESC, POSTCODE, LATESTMILESTONE from VW_NET1_ALL_NEWBUILDS WHERE trim(SIT_UDK)='".strtoupper($_POST['siteIDB'])."'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $resB);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_NEWB=count($resB['SIT_UDK']);
	$SIT_ADDRESSB=str_replace(",","<br>",$resB['SIT_ADDRESS'][0]);
}
//echo $amount_of_NEWB;


if ($resA['SIT_X_COORDINATE'][0]!='' && $resB['SIT_X_COORDINATE'][0]!='' && $resA['SIT_Y_COORDINATE'][0]!='' && $resA['SIT_Y_COORDINATE'][0]!=''){
$DiffEast=($resA['SIT_X_COORDINATE'][0]-$resB['SIT_X_COORDINATE'][0]);
$DiffNorth=($resA['SIT_Y_COORDINATE'][0]-$resB['SIT_Y_COORDINATE'][0]);
$Distance=sqrt($DiffEast*$DiffEast+$DiffNorth*$DiffNorth);
$Distance/=1000;
$Distance=number_format($Distance,2)." km";
}else{
	$Distance="ERROR";
}

list($AngleAB,$AngleBA)=CalcAngle($resA['SIT_X_COORDINATE'][0],$resA['SIT_Y_COORDINATE'][0],$resB['SIT_X_COORDINATE'][0],$resB['SIT_Y_COORDINATE'][0]);

?>
<table class="table">
	<thead>
	<tr>
		<th>A-end</th>
		<th>&nbsp;</th>
		<th>B-end</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td style="width:40%">
		<table>
		<tr>
			<td class="param_title" width="80px">SITE A</td>
			<td><?=$_POST['siteIDA']?></td>
			<td class="param_title">Easting</td>
			<td><?=$resA['SIT_X_COORDINATE'][0]?></td>
		</tr>
		<tr>
			<td class="param_title" width="80px">BEARING AB</td>
			<td><?=$AngleAB?></td>
			<td class="param_title">Northing</td>
			<td><?=$resA['SIT_Y_COORDINATE'][0]?></td>
		</tr>
		<tr>
			<td class="param_title">Site type</td>
			<td><?=$resA['SITE_TYPE_LDE_DESC'][0]?></td>
			<td class="param_title">Milestone</td>
			<td><?=$resA['LATESTMILESTONE'][0]?></td>
		</tr>
		<tr>
			<td class="param_title">Address</td>
			<td><?=$SIT_ADDRESSA?></td>
			<td class="param_title">Postal code</td>
			<td><?=$resA['POSTCODE'][0]?></td>
		</tr>
		</table>
	</td>
	<td style="border-left: 1px solid #ccc; border-right: 1px solid #ccc;vertical-align:top; text-align:center">
	<span class="well"><-- <b>Distance:</b> <?=$Distance?> --></span>
	</td>
	<td style="width:40%">
		<table>
		<tr>
			<td class="param_title">SITE B</td>
			<td><?=$_POST['siteIDB']?></td>
			<td class="param_title">Easting</td>
			<td><?=$resB['SIT_X_COORDINATE'][0]?></td>
		</tr>
		<tr>
			<td class="param_title" width="80px">BEARING BA</td>
			<td><?=$AngleBA?></td>
			<td class="param_title">Northing</td>
			<td><?=$resB['SIT_Y_COORDINATE'][0]?></td>
		</tr>
		<tr>
			<td class="param_title">Site type</td>
			<td><?=$resB['SITE_TYPE_LDE_DESC'][0]?></td>
			<td class="param_title">Milestone</td>
			<td><?=$resB['LATESTMILESTONE'][0]?></td>
		</tr>
		<tr>
			<td class="param_title">Address</td>
			<td><?=$SIT_ADDRESSB?></td>
			<td class="param_title">Postal code</td>
			<td><?=$resB['POSTCODE'][0]?></td>
		</tr>
		</table>
	</td>
	</tr>
	</tbody>
</table>
<hr>
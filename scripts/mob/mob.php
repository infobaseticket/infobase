<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Base_TXMN,Base_delivery,Administrators","");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
?>
<link rel="stylesheet" href="scripts/mob/mob.css" type="text/css">
<?
$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$siteID=$_POST['siteID'];
$viewtype=$_POST['viewtype'];


$query="select * from BSDS_HOST where KPN LIKE '%".$_POST['siteID']."%'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt){
	die_silently($conn_Infobase, $error_str);
	exit;
}else{
	OCIFreeStatement($stmt);
	$amount_links=count($res1['KPN']);
}

if ($amount_links==0){
	echo "NO DATA FOUND!";
}else{
?>
	<br>
	<table width=100% class="mobdata">
	<thead>
	<tr>
		<th class="generaltable_header">KPN</th>
		<th class="generaltable_header">MOB 2G</th>
		<th class="generaltable_header">MOB 3G</th>
		<th class="generaltable_header">HOST</th>

		<th class="generaltable_header">DISH</th>
		<th class="generaltable_header">OTHER</th>
		<th class="generaltable_header">E1</th>
		<th class="generaltable_header">ETHERNET</th>
		<th class="generaltable_header" width="100">STATUS</th>
		<th class="generaltable_header">RESULT</th>
	<th class="generaltable_header">BUILD STATUS</th>
	</tr>
	<tr>
		<td class="data"><? echo $res1['KPN'][0]; ?></td>
		<td class="data"><? echo $res1['MOB2G'][0]; ?></td>
		<td class="data"><? echo $res1['MOB3G'][0]; ?></td>
		<td class="data"><? echo $res1['HOST'][0]; ?></td>
		<td colspan="4" class="generaltable_header">&nbsp;</td>
		<td class="data" rowspan=5><? echo $res1['STATUS'][0]; ?></td>
		<td class="data" rowspan=5><? echo $res1['RESULT'][0]; ?></td>
		<td class="data" rowspan=5><? echo $res1['BUILDSTATUS'][0]; ?></td>
	</tr>
	<tr>
		<td class="generaltable_header" colspan="4">Comments KPN</td>
		<td class="generaltable_superheader" colspan="4">KPN</td>
		<td colspan="3">&nbsp;</td>

	</tr>
	<tr>
		<td colspan="4" class="data"><? echo $res1['KPN_COMMENTS'][0]; ?></td>
		<td class="data"><? echo $res1['KPN_DISH'][0]; ?></td>
		<td class="data"><? echo $res1['KPN_OTHER'][0]; ?></td>
		<td class="data"><? echo $res1['KPN_E1'][0]; ?></td>
		<td class="data"><? echo $res1['KPN_ETHERNET'][0]; ?></td>
		<td colspan="3">&nbsp;</td>

	</tr>
	<tr>
		<td class="generaltable_header" colspan="4">Comments KPN</td>
		<td class="generaltable_superheader" colspan="4">MOBISTAR</td>
		<td colspan="3">&nbsp;</td>

	</tr>
	<tr>
		<td colspan="4" class="data"><? echo $res1['MOB_COMMENTS'][0]; ?></td>
		<td class="data"><? echo $res1['MOB_DISH'][0]; ?></td>
		<td class="data"><? echo $res1['MOB_OTHER'][0]; ?></td>
		<td class="data"><? echo $res1['MOB_E1'][0]; ?></td>
		<td class="data"><? echo $res1['MOB_ETHERNET'][0]; ?></td>
		<td colspan="3">&nbsp;</td>
	</tr>
	</table>

<?
}
?>
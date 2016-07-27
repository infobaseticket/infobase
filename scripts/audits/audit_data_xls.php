<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
protect("","Administrators,Base_delivery,Base_other","");
require_once("audit_procedures.php");

if ($_GET['xlsprint']=="yes"){

	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=REPORT_AUDIT.xls");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
$query=query_audit($_POST['site'],$_POST['auditid'],$_POST['audittype1'],$_POST['audittype2'],$_POST['region'],$_POST['datefilter'],$_POST['daterange'],$_POST['orderby'],$_POST['order']);
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_audits=count($res1['SITE']);
}

if ($amount_of_audits>=1){
	for ($i = 0; $i <$amount_of_audits; $i++) {
		$user=getuserdata($res1['AUDIT_BY'][$i]);
		$REASON= get_reason($res1['REASON'][$i]);
		$output.="<td class='auditdata'>".$res1['ID'][$i]."</td>
		<td >".$res1['SITE'][$i]."</td>
		<td >".$res1['TYPE'][$i]."</td>
		<td >".$res1['TYPE2'][$i]."</td>
		<td >".$res1['AUDIT_DATE'][$i]."</td>
		<td >".$user['firstname']." ".$user['lastname']."</td>
		<td >".$res1['STATUS'][$i]."</td>
		<td>".$REASON."</td>
		<td >".$res1['COMMENTS'][$i]."</td>
		</tr>";
	}
}else{
	$output.="<tr id='row_".$res1['ID'][$i]."'><td colspan='8'>No audits found for this site!-</tr>";
}
?>
<table cellspacing='0px' border='1' cellpadding='0' cellspacing='0' width=100%>
<tr>
	<td align="left" class="audit_superheader" colspan="9" style='font-weight: bold;color: white;background-color: blue;font-size:8px;text-align:left;'><b>.: AUDIT OVERVIEW <?=$_POST['audit']?> :.</b></td>
</tr>
<tr>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>ID</th>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>SITE</th>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>TYPE</th>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>&nbsp;</th>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>DATE</td>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>DONE BY</td>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>STATUS</td>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>REASON</td>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>COMMENTS</td>
</tr>
<?=$output;?>
<tr>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>ID</th>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>SITE</th>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>TYPE</th>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>&nbsp;</th>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>DATE</td>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>DONE BY</td>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>STATUS</td>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>REASON</td>
	<td  style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>COMMENTS</td>
</tr>
</table>
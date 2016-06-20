<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_other","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("audit_procedures.php");
?>
<link rel="stylesheet" href="scripts/audits/audit.css" type="text/css">
<script type="text/javascript" src="scripts/audits/audit.js"></script><?

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query=query_audit($_POST['site'],$_POST['auditid'],$_POST['audittype1'],$_POST['audittype2'],$_POST['region'],$_POST['datefilter'],$_POST['daterange'],$_POST['orderby'],$_POST['order'],"yes");
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
$j=0;

	for ($i = 0; $i <$amount_of_audits; $i++) {
		$user=getuserdata($res1['CREATION_BY'][$i]);
		$user_kpngb=getuserdata($res1['STATUS_KPNGB_BY'][$i]);

		if ($prv_TYPE!=$res1['TYPE'][$i]) {
			$j++;
		}

		if($j%2) {
			 $auditcolor="auditdata1";
		}else{
			$auditcolor="auditdata2";
		}

		$output.="<tr id='row_".$res1['ID'][$i]."' class='".$auditcolor."'>
		<td><ul class='icon_nav'>";
		if (substr_count($guard_groups, 'Base_delivery')=="1" || $guard_username==$res1['AUDITS_BY'][$i] || substr_count($guard_groups, 'Administrators')=="1" ){
		$output.="<li class='audit_delete pointer' id='del_".$res1['ID'][$i]."'><img src='".$config['sitepath_url']."/images/bsds/del.gif'></li>";
		}
		$output.="</ul></td>
		<td>".$res1['SITE'][$i]."</td>
		<td>".$res1['INSPECTIONPARTNER'][$i]."</td>
		<td class='type'>".$res1['TYPE'][$i]." ".$res1['TYPE2'][$i]."</td>
		<td>".$user['firstname']." ".$user['lastname']."<br>".$res1['CREATION_DATE'][$i]."</td>";

		$output.="<td>".$res1['REASON_COMMENTS'][$i]."</td>";
		$output.="<td><span class='editable' id='".$res1['ID'][$i]."_PLANNED'>NOT PLANNED</td>";
		$output.="</td>
		</tr>";

		$prv_TYPE=$res1['TYPE'][$i];
	}
}else{
	$output.="<tr id='row_".$res1['ID'][$i]."'><td colspan='8'>No audits found in planning!-</tr>";
}
?>

<div id="auditnew<?=$_POST['tabid']?>" style="display:none;"></div>
<div id="auditresult<?=$_POST['tabid']?>" class="audittabledata">
	<div id="auditdata_planner">
		<table class="auditdata">
		<tr>
			<td align="left" class="audit_superheader" colspan="11"><b>.: SITE AUDIT PLANNING :.</b></td>
		</tr>
		<tr>
			<td class="audit_header">
			<ul id='audit_nav'><li>
			<?php if (substr_count($guard_groups, 'Base_delivery')=="1" || substr_count($guard_groups, 'Base_other')=="1" || substr_count($guard_groups, 'Administrators')=="1" ){ ?>
			<img src='images/plus.png' class='new_AUDIT pointer' id='planning' />
			<? } ?>
			</li></ul></td>
			<td class="audit_header">SITE</th>
			<td class="audit_header">ASSIGNED INSPECTION PARTNER</th>
			<td class="audit_header">TYPE</th>
			<td class="audit_header">CREATED</td>
			<td class="audit_header">INSPECTION REASON</td>
			<td class="audit_header">INSPECTION ORDERED</td>
		</tr>
		<?=$output;?>
		</table>
	</div>
</div>
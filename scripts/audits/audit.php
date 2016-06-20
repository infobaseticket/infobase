<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_other","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("audit_procedures.php");
?>
<link rel="stylesheet" href="scripts/audits/audit.css" type="text/css">
<script type="text/javascript" src="scripts/audits/audit.js"></script>
<?
$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query=query_audit($_POST['site'],$_POST['auditid'],$_POST['audittype1'],$_POST['audittype2'],$_POST['region'],$_POST['datefilter'],$_POST['daterange'],$_POST['orderby'],$_POST['order'],"no");
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
		$user_pl=getuserdata($res1['PLANNED_BY'][$i]);
		$user_audit=getuserdata($res1['AUDITS_BY'][$i]);

		$pos = strpos($res1['INSPECTIONPARTNER'][$i], 'KPNGB');
		if ($pos === false) {
			$output="output1";

		}else{
			$output="output2";
			$change="yes";
		}

		if ($prv_TYPE!=$res1['TYPE'][$i] && ($res1['TYPE'][$i]=="C2 Inspection at end of CON" || $res1['TYPE'][$i]=="A2 Inspection at end of ACQ") && ($res1['STATUS'][$i]=="FAILED" || $res1['STATUS'][$i]=="") && (substr_count($guard_groups, 'DLConsulting')==1 || substr_count($guard_groups, 'Cofely')==1 || substr_count($guard_groups, 'Administrators')==1) && $res1['PLANNING'][$i]!="yes"){
			$icon="<li><img src='".$config['sitepath_url']."/bsds/images/plus.png' class='new_AUDIT pointer' id='".$res1['ID'][$i]."'></li>";
		}else{
			$icon="";
		}

		if ($prv_TYPE!=$res1['TYPE'][$i] && $res1['STATUS_KPNGB'][$i]=="FAILED"
		&& (substr_count($guard_groups, 'Base_other')==1 || substr_count($guard_groups, 'Base_delivery')==1
		|| substr_count($guard_groups, 'Administrators')==1) && $res1['PLANNING'][$i]!="yes"){
			$editable="editable_select";
		}else{
			$editable="not-editable";
		}

		if($res1['SERVICEPARTNER1'][$i]==""){
			$editable_service1="editable_select_servicep1";
			$servicepartner1="Please complete";
		}elseif($res1['SERVICEPARTNER1'][$i]!=""){
			$servicepartner1=$res1['SERVICEPARTNER1'][$i];
		}

		if($res1['SERVICEPARTNER2'][$i]==""){
			$editable_service2="editable_select_servicep2";
			$servicepartner2="Please complete";
		}elseif($res1['SERVICEPARTNER2'][$i]!=""){
			$servicepartner2=$res1['SERVICEPARTNER2'][$i];
		}

		if($res1['SITEENG'][$i]==""){
			$editable_siteeng="editable_siteeng";
		}elseif($res1['SITEENG'][$i]!=""){
			$siteeng=$res1['SITEENG'][$i];
		}

		if($res1['HSCOORD'][$i]==""){
			$editable_hscoord="editable_select_hscoord";
			$hscoord="Please complete";
		}elseif($res1['HSCOORD'][$i]!=""){
			$hscoord=$res1['HSCOORD'][$i];
		}

		if ($prv_TYPE!=$res1['TYPE'][$i]) {
			$j++;
		}

		if($j%2) {
			$auditcolor="auditdata1";
		}else{
			$auditcolor="auditdata2";
		}

		if ($res1['AUDITS'][$i]!=''){
			$auditdate=substr($res1['AUDITS'][$i],0,-9);
			$editable_time="";
		}else{
			$auditdate="No date planned";
			$editable_time="editable";
		}

		if($res1['STATUSKPNGB'][$i]==""){
			$status_kpngb="Please complete";
			$editable_kpngb="editable_select";
		}else{
			$status_kpngb=$res1['STATUSKPNGB'][$i];
		}

		if($res1['STATUS'][$i]==""){
			$status_partner="Please complete";
			$editable_partner="editable_partner";
		}else{
			$status_partner=$res1['STATUS'][$i];
		}

		$$output.="<tr id='row_".$res1['ID'][$i]."' class='".$auditcolor."'>
		<td><ul class='icon_nav'>";
		if($res1['PLANNING'][$i]!="yes"){
			$$output.="<li class='audit_details pointer' id='audit_".$res1['ID'][$i]."'><img src='".$config['sitepath_url']."/bsds/images/icons/audit.png'></li>";
		}
		if (substr_count($guard_groups, 'Base_delivery')=="1" || $guard_username==$res1['AUDITS_BY'][$i] || substr_count($guard_groups, 'Administrators')=="1" ){
		$$output.="<li class='audit_delete pointer' id='del_".$res1['ID'][$i]."'><img src='".$config['sitepath_url']."/bsds/images/minus.png'></li>";
		}
		$$output.=$icon;
		$$output.="</ul></td>
		<td>".$res1['SITE'][$i]."</td>
		<td>".$res1['INSPECTIONPARTNER'][$i]."</td>
		<td class='type'>".$res1['TYPE'][$i]." ".$res1['TYPE2'][$i]."</td>
		<td>".$user['firstname']." ".$user['lastname']."<br>".$res1['CREATION_DATE'][$i]."</td>
		<td>".$user_pl['firstname']." ".$user_pl['lastname']."<br>".substr($res1['PLANNED'][$i],0,-9)."</td>";

		if ($res1['PLANNING'][$i]!="yes"){
		$$output.="<td id='".$res1['ID'][$i]."_AUDITS' class='".$editable_time."'>";
		if ($user_audit['firstname']){
			$$output.=$user_audit['firstname']." ".$user_audit['lastname']."<br>";
		}
		$$output.= $auditdate."</td>
		<td>Rol. Part.: <span  id='".$res1['ID'][$i]."_SERVICEPARTNER1' class='".$editable_service1."'>".$servicepartner1."</span><br>
		Subco: <span  id='".$res1['ID'][$i]."_SERVICEPARTNER2' class='".$editable_service2."'>".$servicepartner2."</span><br>
		Site Eng.: <span  id='".$res1['ID'][$i]."_SITEENG' class='".$editable_siteeng."'>".$siteeng."</span><br>
		H&S Coord.: <span  id='".$res1['ID'][$i]."_HSCOORD' class='".$editable_hscoord."'>".$hscoord."</span>";
		}

		$$output.="<td><span class='".$editable_partner."' id='".$res1['ID'][$i]."_STATUS'>".$status_partner."</span></td>
		<td class='tabledata'><span class='".$editable_kpngb."' id='".$res1['ID'][$i]."_STATUSKPNGB'>".$status_kpngb."</span>";
		if ($res1['STATUS_KPNGB_DATE'][$i]!=""){
		$$output.="<br>".$user_kpngb['firstname']." ".$user_kpngb['lastname']." on ".$res1['STATUS_KPNGB_DATE'][$i];
		}
		$$output.="</td>
		</tr>";

		$prv_TYPE=$res1['TYPE'][$i];

	}
}else{
	$output.="<tr id='row_".$res1['ID'][$i]."'><td colspan='8'>No audits found for this site!-</tr>";
}


?>

<div id="auditdata_external">
	<table class="auditdata">
	<tr>
		<td align="left" class="audit_superheader" colspan="11"><b>.: EXTERNAL SITE AUDITS :.</b></td>
	</tr>
	<tr>
		<td class="audit_header">&nbsp;</td>

		<td class="audit_header">SITE</th>
		<td class="audit_header">INSP. PARTNER</th>
		<td class="audit_header">TYPE</th>

		<td class="audit_header">CREATED</td>
		<td class="audit_header">ORDERED</td>
		<td class="audit_header">AUDIT DATE</td>
		<td class="audit_header">RESPONSIBLE ROLOUT PARTNER</th>
		<td class="audit_header">AUDIT STATUS<br> INSP. PARTNER</td>
		<td class="audit_header">AUDIT STATUS<br>CHANGE KPNGB</td>
	</tr>
	<?=$output1;?>
	</table>
</div>
<br>
<div id="auditnew<?=$_POST['tabid']?>" style="display:none;border:1px solid;padding:20px; width:600px;"></div>
<br>
<div id="auditdata_internal">
	<table class="auditdata">
	<tr>
		<td align="left" class="audit_superheader" colspan="11"><b>.: INTERNAL SITE AUDITS :.</b></td>
	</tr>
	<tr>
		<td class="audit_header">
		<ul id='audit_nav'>
		<? if (substr_count($guard_groups, 'Administrators')==1 || substr_count($guard_groups, 'Base_delivery')==1 ){ ?>
		<!--<li><img src='images/plus.png' class='new_AUDIT pointer' id="*<?=$_POST['site']?>" /></li>-->
		<? } ?>
		</ul></td>

		<td class="audit_header">SITE</th>
		<td class="audit_header">INSP. PARTNER</th>
		<td class="audit_header">TYPE</th>
		<td class="audit_header">CREATED</td>
		<td class="audit_header">ORDERED</td>
		<td class="audit_header">AUDIT DATE</td>
		<td class="audit_header">RESPONSIBLE ROLOUT PARTNER</th>
		<td class="audit_header">AUDIT STATUS<br>INSP. PARTNER</td>
		<td class="audit_header">AUDIT STATUS<br>CHANGE KPNGB</td>
	</tr>
	<?=$output2;?>
	</table>
</div>

<br><br>

<hr><div id="auditright<?=$_POST['tabid']?>"></div>


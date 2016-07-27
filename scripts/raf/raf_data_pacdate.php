<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Alcatel,Alcatel_sub","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/phpmailer/class.phpmailer.php");
include('raf_procedures.php');
?>
<link rel="stylesheet" href="scripts/raf/bsds_raf.css" type="text/css">
<script type="text/javascript" src="scripts/raf/raf.js"></script>
<?
$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['site_key']){
	register_sitekey($_POST['site_key']);
}

$query=create_query($_POST['site_key'],$_POST['region'],$_POST['type'],$_POST['actionby'],$_POST['orderby'],$_POST['order'],$_POST['start'],$_POST['end'],$_POST['phase'],$_POST['allocated'],"yes");
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_RAFS=count($res1['SITEID']);
}

if ($amount_of_RAFS>=1){
	$k=0;
	for ($i = 0; $i <$amount_of_RAFS; $i++) {
		if ($_POST['net1']=="yes" ){
			$query2 = "	select * from VW_NET1_ALL_NEWBUILDS where SIT_UDK like '%".$res1['SITEID'][$i]."%'";
			//echo "<br><br>".$query1;
			$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
			if (!$stmt2) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt2);
			}
		}

		$query3 = "select CONDITIONAL from BSDS_RAF_RADIO where RAFID = '".$res1['RAFID'][$i]."'";
		//echo "<br><br>".$query1;
		$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
		if (!$stmt3) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt3);
			if ($res3['CONDITIONAL'][0]!=""){
				$CONDITIONAL="<DIV class='conditional' id='".$res3['CONDITIONAL'][0]."' >CONDITIONAL!</div>";
			}
		}


		$TXMN_INP_SEL="";
		$NET1_CREATED_SEL="";
		$PARTNER_INP_SEL="";
		$RADIO_ACC_SEL="";
		$TXMN_ACC_SEL="";
		$status="";
		$status_special="";


		if ($res1['TYPE'][$i]=="New Indoor" || $res1['TYPE'][$i]=="Indoor Upgrade" || $res1['TYPE'][$i]=="IND Upgrade" || $res1['TYPE'][$i]=="RPT Upgrade"){
			$raf_type="indoor";
		}else{
			$raf_type="outdoor";
		}

		if (substr_count($guard_groups, 'Base_other')==1 || substr_count($guard_groups, 'Administrators')==1 || substr_count($guard_groups, 'Base_delivery')==1){
			$other_select="editable_select";
		}else{
			$other_select="";
		}
		if (substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1  || substr_count($guard_groups, 'Base_delivery')==1){
			$RF_select="editable_select";
		}else{
			$RF_select="";
		}		if ((substr_count($guard_groups, 'Base_TXMN')==1 && ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA")) || substr_count($guard_groups, 'Administrators')==1 || substr_count($guard_groups, 'Base_delivery')==1){
			$TXMN_select="editable_select";
		}else{
			$TXMN_select="";
		}
		if (substr_count($guard_groups, 'Base_delivery')==1  || substr_count($guard_groups, 'Administrators')==1){
			$delivery_select="editable";
		}else{
			$delivery_select="";
		}
		if ((substr_count($guard_groups, 'Alcatel')==1 && ($res1['RADIO_INP'][$i]=="OK" ||  $res1['RADIO_INP'][$i]=="NA")  && $res1['TXMN_INP'][$i]=="OK" && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")) || substr_count($guard_groups, 'Administrators')==1){
			$alcatel_select="editable_select";
		}else{
			$alcatel_select="";
		}
		if (((substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Base_delivery')==1) && $res1['RADIO_INP'][$i]=="OK"  && $res1['TXMN_INP'][$i]=="OK"  && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['ALU_INPUT'][$i]!="NOT OK") || substr_count($guard_groups, 'Administrators')==1){
			$BCS_select="editable_select";
		}else{
			$BCS_select="";
		}
		if ((substr_count($guard_groups, 'Alcatel')==1 && ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA")  && $res1['TXMN_INP'][$i]=="OK"  && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['ALU_INPUT'][$i]!="NOT OK" && $res1['NET1_LBP'][$i]!="NOT OK") || substr_count($guard_groups, 'Administrators')==1){
			$aluacq_select="editable_select";
		}else{
			$aluacq_select="";
		}

		if ((substr_count($guard_groups, 'Base_TXMN')==1 && ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA")  && $res1['TXMN_INP'][$i]=="OK"  && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['ALU_INPUT'][$i]!="NOT OK" && $res1['NET1_LBP'][$i]!="NOT OK" && $res1['ALU_ACQUIRED'][$i]!='NOT OK') || substr_count($guard_groups, 'Administrators')==1){
			$txmnacq_select="editable_select";
		}else{
			$txmnacq_select="";
		}

		if ((substr_count($guard_groups, 'Alcatel')==1 && ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA")  && $res1['TXMN_INP'][$i]=="OK"  && ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")  && $res1['ALU_INPUT'][$i]!="NOT OK" && $res1['NET1_LBP'][$i]!="NOT OK" && $res1['NET1_AQUIRED'][$i]!="NOT OK" && $res1['NET1_AQUIRED'][$i]!="REJECTED") || substr_count($guard_groups, 'Administrators')==1){
			$net1acq_select="editable_select";
		}else{
			$net1acq_select="";
		}

		if (( ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA") && $res1['TXMN_INP'][$i]=="OK"
		&& ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")
		&& $res1['NET1_LBP'][$i]!="NOT OK" && (($res1['ALU_ACQUIRED'][$i]!="NOT OK" && $res1['SAC'][$i]=="ALU")
		|| ($res1['SAC'][$i]!="ALU")))
		|| substr_count($guard_groups, 'Administrators')==1){

			if (substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1
			|| substr_count($guard_groups, 'Base_delivery')==1 ||
			(substr_count($guard_groups, 'Base_TXMN')==1 && $res1['TYPE'][$i]=="CTX Upgrade")){
				$FUNDSTATUS_select="editable_select";
			}else{
				$FUNDSTATUS_select="";
			}
		}else{
			$FUNDSTATUS_select="";
		}
		//echo $res1['NET1_LBP'][$i]."!=NOT OK // ".$res1['STATUS_FUND'][$i]."==OK // ". $res1['RADIO_INP'][$i]."==OK //". $res1['TXMN_INP'][$i]."<br>";
		if (( ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA" ) && $res1['TXMN_INP'][$i]=="OK"
		&& ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")
		&& $res1['NET1_LBP'][$i]!="NOT OK" && $res1['STATUS_FUND'][$i]!="NOT OK" & $res1['NET1_FUND'][$i]=="NOT OK"
		&&  substr_count($guard_groups, 'Base_delivery')==1) || substr_count($guard_groups, 'Administrators')==1){
			$NET1_FUND_select="editable_select";
		}else{
			$NET1_FUND_select="";
		}

		if (( ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA")  && ($res1['TXMN_INP'][$i]=="OK" || $res1['TXMN_INP'][$i]=="NA")
		&& ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")
		&& $res1['NET1_LBP'][$i]!="NOT OK"
		&& $res1['STATUS_FUND'][$i]!="NOT OK"
		&& substr_count($guard_groups, 'Alcatel')==1)
		|| substr_count($guard_groups, 'Administrators')==1){
			$PAC_select="editable_select";
		}else{
			$PAC_select="";
		}
		if (( ($res1['RADIO_INP'][$i]=="OK" || $res1['RADIO_INP'][$i]=="NA" ) && ($res1['TXMN_INP'][$i]=="OK" || $res1['TXMN_INP'][$i]=="NA")
		&& ($res1['NET1_LINK'][$i]!="NOT OK" || $res1['NET1_LINK'][$i]=="")
		&& $res1['NET1_LBP'][$i]!="NOT OK"
		&& $res1['STATUS_FUND'][$i]!="NOT OK" && $res1['NET1_FUND'][$i]!="NOT OK"
		&& $res1['PAC_STATUS'][$i]!="NOT OK"
		&& substr_count($guard_groups, 'Base_RF')==1) || substr_count($guard_groups, 'Administrators')==1){
			$RFPAC_select="editable_select";
		}else{
			$RFPAC_select="";
		}


		include('raf_color_analysis.php');


		$k++;

		$user_CREATION=getuserdata($res1['CREATED_BY'][$i]);
		$user_OTHER_INP_BY=getuserdata($res1['OTHER_INP_BY'][$i]);
		$user_RADIO_INP_BY=getuserdata($res1['RADIO_INP_BY'][$i]);
		$user_TXMN_INP_BY=getuserdata($res1['TXMN_INP_BY'][$i]);
		$user_NET1_LINK_BY=getuserdata($res1['NET1_LINK_BY'][$i]);
		$user_TXMN_INP_BY=getuserdata($res1['TXMN_INP_BY'][$i]);
		$user_ALU_INP_BY=getuserdata($res1['ALU_INP_BY'][$i]);
		$user_STATUS_FUND_BY=getuserdata($res1['STATUS_FUND_BY'][$i]);
		$user_BCS_NET1_BY=getuserdata($res1['BCS_NET1_BY'][$i]);
		$user_PAC_STATUS_BY=getuserdata($res1['PAC_STATUS_BY'][$i]);
		$user_RF_PAC_BY=getuserdata($res1['RF_PAC_BY'][$i]);
		$user_ALU_ACQ_BY=getuserdata($res1['ALU_ACQUIRED_BY'][$i]);
		$user_UPDATE_BY=getuserdata($res1['UPDATE_BY'][$i]);
		$user_TXMN_ACQ_BY=getuserdata($res1['TXMN_ACQUIRED_BY'][$i]);


		$output_popup.="
			<table id='RAFBOX_".$res1['RAFID'][$i]."' style='display:none;' class='raf_hoverbox'>
			<tr>
				<td colspan='2' style='text-align:cneter;font-weight:bold;'>RAF".$res1['RAFID'][$i]."</td>
			</tr>
			<tr>
				<td>CREATION</td>
				<td>".$user_CREATION['firstname']." ".$user_CREATION['lastname']." on ".$res1['CREATION_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>UPDATE</td>
				<td>".$user_UPDATE_BY['firstname']." ".$user_UPDATE_BY['lastname']." on ".$res1['UPDATE_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>OTHER INPUT</td>
				<td>".$user_OTHER_INP_BY['firstname']." ".$user_OTHER_INP_BY['lastname']." on ".$res1['OTHER_INP_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>RF INPUT</td>
				<td>".$user_RADIO_INP_BY['firstname']." ".$user_RADIO_INP_BY['lastname']." on ".$res1['RADIO_INP_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>TX INPUT</td>
				<td>".$user_TXMN_INP_BY['firstname']." ".$user_TXMN_INP_BY['lastname']." on ".$res1['TXMN_INP_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>NET1 LINK</td>
				<td>".$user_NET1_LINK_BY['firstname']." ".$user_NET1_LINK_BY['lastname']." on ".$res1['NET1_LINK_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>PARTNER INPUT</td>
				<td>".$user_ALU_INP_BY['firstname']." ".$user_ALU_INP_BY['lastname']." on ".$res1['ALU_INP_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>BCS STATUS</td>
				<td>".$user_BCS_NET1_BY['firstname']." ".$user_BCS_NET1_BY['lastname']." on ".$res1['BCS_NET1_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>PARTNER ACQUIRED</td>
				<td>".$user_ALU_ACQ_BY['firstname']." ".$user_ALU_ACQ_BY['lastname']." on ".$res1['ALU_ACQUIRED_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>TXMN ACQUIRED</td>
				<td>".$user_TXMN_ACQ_BY['firstname']." ".$user_TXMN_ACQ_BY['lastname']." on ".$res1['TXMN_ACQUIRED_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>FUND SITE</td>
				<td>".$user_STATUS_FUND_BY['firstname']." ".$user_STATUS_FUND_BY['lastname']." on ".$res1['STATUS_FUND_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>PAC STATUS</td>
				<td>".$user_PAC_STATUS_BY['firstname']." ".$user_PAC_STATUS_BY['lastname']." on ".$res1['PAC_STATUS_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>RF PAC</td>
				<td>".$user_RF_PAC_BY['firstname']." ".$user_RF_PAC_BY['lastname']." on ".$res1['RF_PAC_DATE'][$i]."</td>
			</tr>
			<tr>
				<td colspan='2'><hr></td>
			</tr>
			<tr>
				<td>DELETED</td>
				<td>".$res1['DELETE_DATE'][$i]."</td>
			</tr>
			<tr>
				<td>REASON</td>
				<td>".$res1['DELETE_REASON'][$i]."</td>
			</tr>
			</table>";

		if ($status=="RAF ASBUILD"){
			$select_action="asbuild_RAF";
			$val1="asbuild_RAF";
			$val2="asbuild_RAF";
			$val3="asbuild_RAF";
			$val4="asbuild_RAF";
			$val5="asbuild_RAF";
			$val6="asbuild_RAF";
			$val7="asbuild_RAF";
			$val8="asbuild_RAF";
			$val9="asbuild_RAF";
			$val10="asbuild_RAF";
			$val11="asbuild_RAF";
			$val12="asbuild_RAF";
			$val13="asbuild_RAF";
			$val14="asbuild_RAF";
			$val15="asbuild_RAF";
		}else{
			$select_action="selected_RAF";
		}
		if ($res1['DELETED'][$i]=="yes"){
			$deleted_color="deleted";
			$user_DELETED=getuserdata($res1['DELETE_BY'][$i]);
			$status="DELETED BY ".$user_DELETED['firstname']." ".$user_DELETED['lastname'];
		}else{
			$deleted_color="";
		}

		if (substr($status,0,7)=="ALCATEL"){
			$status_screen="ROLL-OUT PARTNER ".substr($status,7);
		}else{
			$status_screen=$status;
		}

		if ($res1['TYPE'][$i]=="Adding UMTS900 to UMTS2100 (= Not TECHNO Upgrade)"){
			$type="Add U900 to U2100";
		}else{
			$type=$res1['TYPE'][$i];
		}

		$output_raf_spl1.="<input type='hidden' name='createdby' id='createdby-".$res1['RAFID'][$i]."' value='".$res1['CREATED_BY'][$i]."'>
		<input type='hidden' name='user_TXMN_INP_BY' id='user_TXMN_INP_BY-".$res1['RAFID'][$i]."' value='".$res1['TXMN_INP_BY'][$i]."'>
		<input type='hidden' name='user_RADIO_INP_BY' id='user_RADIO_INP_BY-".$res1['RAFID'][$i]."' value='".$res1['RADIO_INP_BY'][$i]."'>
		<input type='hidden' name='user_STATUS_FUND_BY' id='user_STATUS_FUND_BY-".$res1['RAFID'][$i]."' value='".$res1['STATUS_FUND_BY'][$i]."'>
		<input type='hidden' name='user_PAC_STATUS_BY' id='user_PAC_STATUS_BY-".$res1['RAFID'][$i]."' value='".$res1['PAC_STATUS_BY'][$i]."'>
		<input type='hidden' name='user_ALU_INP_BY' id='user_ALU_INP_BY-".$res1['RAFID'][$i]."' value='".$res1['ALU_INP_BY'][$i]."'>
		<input type='hidden' name='user_ALU_ACQUIRED_BY' id='user_ALU_ACQUIRED_BY-".$res1['RAFID'][$i]."' value='".$res1['ALU_ACQUIRED_BY'][$i]."'>
		<input type='hidden' name='user_TXMN_ACQUIRED_BY' id='user_TXMN_ACQUIRED_BY-".$res1['RAFID'][$i]."' value='".$res1['TXMN_ACQUIRED_BY'][$i]."'>
		<input type='hidden' name='raftype' id='raftype-".$res1['RAFID'][$i]."' value='".$raf_type."'>
		<input type='hidden' name='siteid' id='sitename-".$res1['RAFID'][$i]."' value='".$res1['SITEID'][$i]."'>";

		$output_raf_spl1.="<tr id='row_".$res1['RAFID'][$i]."' class='tablerow ".$deleted_color."'>";
		$output_raf_spl1.="<td style='text-align:center' width='80px'><ul id='raf_icons'>";
		if ($res1['DELETED'][$i]!="yes" || substr_count($guard_groups, 'Administrators')==1){
		$output_raf_spl1.="<li><img src='".$config['sitepath_url']."/images/bsds/raf.png' class='RAF_details pointer RAF_".$res1['RAFID'][$i]."' title='Check RAF data' border='0' id='".$res1['RAFID'][$i]."*".$raf_type."*".$status."'></li>";
		if (substr_count($guard_groups, 'Base_delivery')==1 || substr_count($guard_groups, 'Administrators')==1){
		$output_raf_spl1.="<li><img src='".$config['sitepath_url']."/images/bsds/del.gif' title='Delete RAF' class='RAF_delete pointer' border='0' id='delete_".$res1['RAFID'][$i]."'></a></li>";
		}
		$output_raf_spl1.="<li><a href='scripts/raf/raf_print.php?rafid=".$res1['RAFID'][$i]."&raftype=".$res1['TYPE'][$i]."' target='_new'><img src='".$config['sitepath_url']."/images/icons/printer.png' class='RAF_print pointer RAF_".$res1['RAFID'][$i]."' title='Print RAF data' border='0'  id='".$res1['RAFID'][$i]."-".$raf_type."-".$status."'></a></li>";
		}
		$output_raf_spl1.="</ul></td>";
		$output_raf_spl1.="<td class='rafidnr fixed ".$deleted_color."' id='".$res1['RAFID'][$i]."'>".$res1['RAFID'][$i]."<br>&nbsp;</td>";
		$output_raf_spl1.="<td class='fixed ".$deleted_color."'>".substr($res1['SITEID'][$i],0,2)."</td>";
		$output_raf_spl1.="<td class='fixed ".$deleted_color."'>".substr($res1['SITEID'][$i],2)."</td>";
		$output_raf_spl1.="<td class='fixed ".$deleted_color."' width='140px'><div id='type-".$res1['RAFID'][$i]."'>".$type."</div>".$res1['CANDIDATE'][$i]."</td>";
		$output_raf_spl2.="</td><tr>";
		$output_raf_spl2.="<td class='".$val10."'><div id='NET1_FUND-".$res1['RAFID'][$i]."' class='tabledata ".$NET1_FUND_select."'>".$res1['NET1_FUND'][$i]."</div></td>";
		$output_raf_spl2.="<td class='".$val11."'><div id='PAC_STATUS-".$res1['RAFID'][$i]."' class='tabledata ".$PAC_select."'>".$res1['PAC_STATUS_DATE'][$i]."</div></td>";
		$output_raf_spl2.="<td class='".$val12."'><div id='RF_PAC-".$res1['RAFID'][$i]."' class='tabledata ".$RFPAC_select."'>".$res1['RF_PAC'][$i]."</div></td>";
		$output_raf_spl2.="<td style='text-align:center' width='80px'><ul id='raf_icons'>";
		if ($res1['DELETED'][$i]!="yes" || substr_count($guard_groups, 'Administrators')==1){
		$output_raf_spl2.="<li><img src='".$config['sitepath_url']."/images/bsds/raf.png' class='RAF_details pointer RAF_".$res1['RAFID'][$i]."' title='Check RAF data' border='0' id='".$res1['RAFID'][$i]."*".$raf_type."*".$status."'></li>";
		if (substr_count($guard_groups, 'Base_delivery')==1 || substr_count($guard_groups, 'Administrators')==1){
		$output_raf_spl2.="<li><img src='".$config['sitepath_url']."/images/bsds/del.gif' title='Delete RAF' class='RAF_delete pointer' border='0' id='delete_".$res1['RAFID'][$i]."'></a></li>";
		}
		$output_raf_spl2.="<li><a href='scripts/raf/raf_print.php?rafid=".$res1['RAFID'][$i]."&raftype=".$res1['TYPE'][$i]."' target='_new'><img src='".$config['sitepath_url']."/images/icons/printer.png' class='RAF_print pointer RAF_".$res1['RAFID'][$i]."' title='Print RAF data' border='0' id='".$res1['RAFID'][$i]."-".$raf_type."-".$status."'></a></li>";
		}
		$output_raf_spl2.="</tr>";

		$CONDITIONAL="";
	}
}else{
	$output_raf_spl1="<tr>";
	$output_raf_spl1.="<td colspan='6'>NO RAF found!</td>";
	$output_raf_spl1.="</tr>";
	$output_raf_spl2="<tr>";
	$output_raf_spl2.="<td colspan='16'>NO RAF found!</td>";
	$output_raf_spl2.="</tr>";
}


echo $output_popup;
?>

<div id="raftable">
  <table id="raftable1" cellspacing="0" cellpadding="0" style="width: 360px;">
  	<tr>
  		<th><img src='../../images/icons/add.png' class='new_RAF pointer' id="new" /></th>
		<th style="width:50px;">RAF ID</th>
		<th>REGION<br>&nbsp;</th>
		<th>SITEID</th>
		<th>TYPE</th>
	</tr>
    <?=$output_raf_spl1?>
    <tr>
      <td class="bottom" colspan="6"><font size='4'><i>Delete a RAF: contact Base Delivery </i></font></td>
    </tr>
  </table>
  <div id="wrap_raftable_pac">
    <table id="raftable_pac" cellspacing="0" cellpadding="0">
      <tr>
		<th><a title='SITE FUNDED'>FUNDING DATE<br>UA353</a></th>
		<th>PARTNER PAC DATE</th>
		<th><a title='RAF PAC Completed'>RF PAC DATE<br>UA712</a></th>
		<th><img src='../../images/icons/add.png' class='new_RAF pointer' id="new" /></th>
	  </tr>
      <?=$output_raf_spl2?>
      <tr>
        <td class="bottom" colspan="18" style="padding: 0px;"><font size='4'><i>NET1 naming: hover over the column title</i></font></td>
      </tr>
    </table>
  </div>
</div>

<div style="clear:both"></div>
<div id="RAFcontentmenu<?=$_POST['tabid']?>"></div>
<div id="RAFcontent<?=$_POST['tabid']?>">
	<div id="RAFwrapper">
		<div id="rafleft">
			<div id="RAFcontent_left<?=$_POST['tabid']?>"></div>
		</div>
		<div style="clear:both"></div>
	</div>
</div>
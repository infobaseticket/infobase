<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

if ($_GET['xlsprint']=="yes"){
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=REPORT_LOS.xls");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$where="";

if ($_GET['actionby']=="Alcatel Processing"){
	$where .= " WHERE DONE = 'NOT OK'";
}else if ($_GET['actionby']=="Alactel Reporting"){
	$where .= " WHERE REPORT = 'NOT OK' AND DONE='OK'";
}else if ($_GET['actionby']=="TXMN Resulting"){
	$where .= " WHERE RESULT = 'NOT OK' AND DONE='OK' AND REPORT='OK'";
}else if ($_GET['actionby']=="Canceled"){
	$where .= " WHERE PRIORITY = 'Canceled'";
}else{
		$where .= " WHERE SITEA IS NOT NULL";
	}
if ($_GET['type']!=""){
	$where .= " AND TYPE ='".$_GET['type']."'";
}
if ($_GET['region']!="" && $_GET['region']!="ALL"){
	$where .= " AND (SITEA LIKE '%".$_GET['region']."%' OR SITEB LIKE '%".$_GET['region']."%')";
}

$query="";
$query="SELECT *  FROM  BSDS_LINKINFO ". $where ." ORDER BY ID ASC";
//echo $query;
//die;

$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_LOS=count($res1['SITEA']);
}
//echo $amount_of_LOS;

if ($amount_of_LOS>=1){
	$k=0;
	for ($i = 0; $i <$amount_of_LOS; $i++) {
		if (substr_count($guard_groups, 'Base_TXMN')==1 || substr_count($guard_groups, 'Administrators')==1){
			$PRIORITY_select="editable_select_PRIORITY";
		}else{
			$PRIORITY_select="";
		}
		if ((substr_count($guard_groups, 'Alcatel')==1 && $res1['DONE'][$i]=="NOT OK")|| substr_count($guard_groups, 'Administrators')==1){
			$DONE_select="editable_select";
		}else{
			$DONE_select="";
		}

		if ((substr_count($guard_groups, 'Alcatel')==1 && $res1['DONE'][$i]=="OK" && $res1['REPORT'][$i]=="NOT OK") || substr_count($guard_groups, 'Administrators')==1){
			$REPORT_select="editable_select";
		}else{
			$REPORT_select="";
		}

		if (substr_count($guard_groups, 'Base_TXMN')==1  || substr_count($guard_groups, 'Administrators')==1){
			$RESULT_select="editable_select_RESULT";
		}else{
			$RESULT_select="";
		}


		if ($res1['PRIORITY'][$i]=="Canceled"){
			$status="LOS canceled";
			$PRIORITY_status="selected_LOS";
		}else{
			if ($res1['DONE'][$i]=="NOT OK"){
				$status="ALU in process";
				$DONE_status="selected_LOS";
			}else if ($res1['DONE'][$i]=="OK" && $res1['REPORT'][$i]=="NOT OK"){
				$status="ALU to create report";
				$REPORT_status="selected_LOS";
			}else if ($res1['RESULT'][$i]=="NOT OK" && $res1['REPORT'][$i]=="OK" && $res1['DONE'][$i]=="OK"){
				$status="TXMN to evaluate report";
				$RESULT_status="selected_LOS";
			}else{
				$status="LOS confirmed";
				$DONE_status="";
				$REPORT_status="";
				$RESULT_status="";
			}
		}

		$query = "Select CON FROM VW_NET1_ALL_NEWBUILDS WHERE";
		$query .= " trim(SIT_UDK) = '".$res1['SITEB'][$i]."'";
		//echo $query;
		$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
		if (!$stmt2) {
			die_silently($conn_Infobase, $error_str);
			exit;
		} else {
			OCIFreeStatement($stmt2);
			$CON=$res2['CON'][0];
		}


		$output_los.="<tr id='row_".$res1['ID'][$i]."' class='tablerow'>";
		$output_los.="<td style='font-size:10px;' id='".$res1['ID'][$i]."'>".$res1['ID'][$i]."</td>";
		$output_los.="<td style='font-size:10px;'>".$res1['SITEA'][$i]."</td>";
		$output_los.="<td  style='font-size:10px;'>".$res1['SITEB'][$i]."</td>";
		$output_los.="<td  style='font-size:10px;' ".$PRIORITY_status."'><div id='PRIORITY-".$res1['ID'][$i]."' class='tabledata ".$PRIORITY_select."'>".$res1['PRIORITY'][$i]."</div></td>";
		$output_los.="<td  style='font-size:10px;' asbuild_LOS'><b><div id='status_".$res1['ID'][$i]."'>".$status."</div></b></td>";
		$output_los.="<td  style='font-size:10px;'>".$res1['CREATION_BY'][$i]."</td>";
		$output_los.="<td  style='font-size:10px;' ".$DONE_status."'><div id='DONE-".$res1['ID'][$i]."' class='tabledata ".$DONE_select."'>".$res1['DONE'][$i]."</div></td>";
		$output_los.="<td  style='font-size:10px;' ".$REPORT_status."'><div id='REPORT-".$res1['ID'][$i]."' class='tabledata ".$REPORT_select."'>".$res1['REPORT'][$i]."</div></td>";
		$output_los.="<td  style='font-size:10px;' ".$RESULT_status."'><div id='RESULT-".$res1['ID'][$i]."' class='tabledata ".$RESULT_select."'>".$res1['RESULT'][$i]."</div></td>";
		$output_los.="<td  style='font-size:10px;'><div id='TYPE-".$res1['ID'][$i]."' class='tabledata'>".$res1['TYPE'][$i]."</div></td>";
		$output_los.="<td  style='font-size:10px;'><div id='TYPE-".$res1['ID'][$i]."' class='tabledata'>".$CON."</div></td>";
		$output_los.="</tr>";
		}
}else{
	$output_los.="<tr>";
	$output_los.="<td colspan='11'>NO links found!</td>";
	$output_los.="</tr>";
}

?>
<table cellpadding="0" cellspacing="0" border="0"  id="content_RAFverview" width="100%" align="center">
<tr>
	<td>
		<table cellspacing="0px" border="1" cellpadding="0" cellspacing="0" width=100% id="reporttable">
		<tr>
			<td align="left" class="generaltable_superheader" colspan="10">&nbsp;<b>.: LOS OVERVIEW :.</b></td>
		</tr>
		<tr>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>LINK ID</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>SiteA</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>SiteB</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>Priority</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>Action by</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>Link Designer</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>ALU processed</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>ALU report created</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>TXMN Result</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>Type</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>CON Partner</td>
		</tr>
		<? echo $output_los; ?>
		<tr>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>LINK ID</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>SiteA</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>SiteB</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>Priority</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>Action by</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>Link Designer</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>ALU processed</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>ALU report created</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>TXMN Result</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>Type</td>
			<td style='font-weight: bold;color: #FFFFFF;background-color: #434751;font-size:10px;text-align:center;'>CON Partner</td>
		</tr>
		</table>
	</td>
</tr>
</table>



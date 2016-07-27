<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['siteID']){
	$BSDSrefresh=get_BSDSrefresh();

	$siteID=$_POST['siteID'];

	$query="SELECT
		t1.*, ta.ASSET_X as A_X, ta.ASSET_Y as A_Y, tb.ASSET_X as B_X, tb.ASSET_Y as B_Y, ta.ASSET_XY as A_XY, tb.ASSET_XY as B_XY
	FROM
		MVW_TX_MW t1 left JOIN MASTER_REPORT ta on t1.LNK_SIT_UDK_A=ta.N1_CANDIDATE AND ta.N1_STATUS='IS' AND (ta.N1_NBUP='NB' OR ta.N1_NBUP='NB REPL') AND ta.N1_CLASSCODE='BTS'
	left JOIN MASTER_REPORT tb on t1.LNK_SIT_UDK_A=tb.N1_CANDIDATE AND tb.N1_STATUS='IS' AND (tb.N1_NBUP='NB' OR tb.N1_NBUP='NB REPL') AND tb.N1_CLASSCODE='BTS'
	WHERE
 	LNK_SIT_UDK_A LIKE '%".$siteID."%' or LNK_SIT_UDK_B LIKE '%".$siteID."%' ORDER BY LNK_SIT_UDK_A, LNK_UDK";
	//echo $query;
	$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
	if (!$stmtT) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmtT);
		$amount_of_MW=count($resT['LNK_SIT_UDK_A']);
	}

	for ($i=0;$i<$amount_of_MW;$i++){
		
		if ($resT['A_XY'][$i]!='' && $resT['B_XY'][$i]!=''){
			$AXY=explode('-', $resT['A_XY'][$i]);
			$BXY=explode('-', $resT['B_XY'][$i]);
			$DiffEast=($AXY[0]-$AXY[1]);
			$DiffNorth=($BXY[0]-$BXY[1]);
			$Distance=sqrt($DiffEast*$DiffEast+$DiffNorth*$DiffNorth);
			$Distance/=1000;
			$Distance=number_format($Distance,2)." km";
		}else{
			$Distance="ERROR";
		}

		$mw.="<tr><td>".$resT['LNK_UDK'][$i]."</td>
		<td>".$resT['LNK_LKP_ELS_CODE'][$i]."</td>
		<td>".$resT['LNK_SIT_UDK_A'][$i]."</td>
		<td>". $resT['LNK_SIT_UDK_B'][$i]."</td>
		<td>".$Distance."</td>
		</tr>";
	
	}
} ?>

<?php
if ($_POST['siteID']){
	$BSDSrefresh=get_BSDSrefresh();

	$siteID=$_POST['siteID'];

	$query="select * from MVW_TX_LL tb left JOIN MASTER_REPORT ta on t1.LNK_SIT_UDK_A=ta.N1_CANDIDATE AND ta.N1_STATUS='IS' AND (ta.N1_NBUP='NB' OR ta.N1_NBUP='NB REPL') AND ta.N1_CLASSCODE='BTS'
	left JOIN MASTER_REPORT tb on t1.LNK_SIT_UDK_A=tb.N1_CANDIDATE AND tb.N1_STATUS='IS' AND (tb.N1_NBUP='NB' OR tb.N1_NBUP='NB REPL') AND tb.N1_CLASSCODE='BTS'
	WHERE
 	LNK_SIT_UDK_A LIKE '%".$siteID."%' or LNK_SIT_UDK_B LIKE '%".$siteID."%' ORDER BY LNK_SIT_UDK_A, LNK_UDK";
	//echo $query;
	$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
	if (!$stmtT) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmtT);
		$amount_of_LL=count($resT['LNK_SIT_UDK_A']);
	}

	for ($i=0;$i<$amount_of_LL;$i++){

		if ($resT['A_XY'][$i]!='' && $resT['B_XY'][$i]!=''){
			$AXY=explode('-', $resT['A_XY'][$i]);
			$BXY=explode('-', $resT['B_XY'][$i]);
			$DiffEast=($AXY[0]-$AXY[1]);
			$DiffNorth=($BXY[0]-$BXY[1]);
			$Distance=sqrt($DiffEast*$DiffEast+$DiffNorth*$DiffNorth);
			$Distance/=1000;
			$Distance=number_format($Distance,2)." km";
		}else{
			$Distance="ERROR";
		}

		$ll.="<tr><td>".$resT['LNK_UDK'][$i]."</td>
		<td>".$resT['LNK_LKP_ELS_CODE'][$i]."</td>
		<td>".$resT['LNK_SIT_UDK_A'][$i]."</td>
		<td>". $resT['LNK_SIT_UDK_B'][$i]."</td>
		<td>".$Distance."</td>
		</tr>";
	}
}
?>

<h2>Microwave dishes:</h2>
<table class="table table-hover">
<thead>
	<tr>
		<td>LK Link ID</td>
		<td>LK Link ID</td>
	</tr>
</thead>
<?=$mw?>
</table>

<h2>Leased lines:</h2>
<table class="table table-hover">
<?=$ll?>
</table>

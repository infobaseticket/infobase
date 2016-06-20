<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP","");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
//error_reporting(E_ALL);

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_GET['report']==""){
?>
<b><u>Download for:</u></b><br><br>
<a href="scripts/reports/Raf_missing.php?report=upg_ph1" target="_new">UMTS UPG HSDPA PH1</a><br><br>
<a href="scripts/reports/Raf_missing.php?report=upg_ph2" target="_new">UMTS UPG HSDPA PH2</a><br><br>
<a href="scripts/reports/Raf_missing.php?report=new_ph1" target="_new">UMTS NEW HSDPA PH1</a><br><br>
<a href="scripts/reports/Raf_missing.php?report=new_ph2" target="_new">UMTS NEW HSDPA PH2</a><br><br>
<?
}else{
	header("Content-Type: application/vnd.ms-excel");
	$file="UMTS_Upgrades_".$_GET['report']."_MISSING_RAF.xls";
	header("Content-Disposition: attachment; filename=".$file);
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	?>
	<HTML>
	<HEAD>
	<TITLE><? echo "RAF report - ".$_GET['report']; ?></TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=">
	</HEAD>
	
	<BODY>
	<table>
		<thead>
			<tr>
				<th>SITE ID</th>
				<th>HSDPA PHASE</th>
				<th>STATUS</th>
				<? if($_GET['report']=="new_ph2" || $_GET['report']=="new_ph1"){ 
				?>
				<th>TYPE</th>	
				<? } ?>
				<th>TECHNOLOGY</th>
				<th>RF INFO</th>
				<th>SAC</th>
				<th>ALSAC</th>
				<th>PO ACQ</th>
				<th>CON</th>
				<th>ALCON</th>
				<th>PO CON</th>
				<th>SITE FUNDED</th>
			</tr>
		</thead>
	<?	
	if ($_GET['report']=="new_ph1"){
		$query1 = "
			select * from VW_NET1_ALL_NEWBUILDS where trim(WOR_HSDPA_CLUSTER) LIKE '%Phase 1%' 
					AND trim(WOR_HSDPA_CLUSTER) NOT LIKE '%Phase 1+%' 
					AND trim(WOR_HSDPA_CLUSTER) NOT LIKE '%Phase 1 +%'
					AND trim(WOR_DOM_WOS_CODE) IN ('IS')
					AND trim(WOE_RANK)=1    AND SUBSTR(SIT_UDK,2,6) IN(
				select SUBSTR(SIT_UDK,2,6) AS SITE from VW_NET1_ALL_NEWBUILDS where 
				trim(WOR_HSDPA_CLUSTER) LIKE '%Phase 1%' 
				AND trim(WOR_HSDPA_CLUSTER) NOT LIKE '%Phase 1+%' AND trim(WOE_RANK)=1
				AND trim(WOR_HSDPA_CLUSTER) NOT LIKE '%Phase 1 +%' AND trim(WOE_RANK)=1
				AND trim(WOR_DOM_WOS_CODE) IN ('IS')
             minus
             select SITEID from BSDS_RAF)  ORDER BY SIT_UDK";
		//echo "<br><br>".$query1;
		$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			//echo count($res1['SIT_UDK']);
			for ($i=0;$i<count($res1['SIT_UDK']);$i++){
				echo "<tr><td>".$res1['SIT_UDK'][$i]."</td>
				<td>".$res1['WOR_HSDPA_CLUSTER'][$i]."</td>
				<td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
				<td>".$res1['DRE_V2_1_6'][$i]."</td>
				<td>".$res1['DRE_V20_1'][$i]."</td>
				<td>".$res1['WOR_NAME'][$i]."</td>
				<td>".$res1['SAC'][$i]."</td>
				<td>".$res1['ALSAC'][$i]."</td>
				<td>".$res1['A501'][$i]."</td>
				<td>".$res1['CON'][$i]."</td>
				<td>".$res1['ALCON'][$i]."</td>
				<td>".$res1['A503'][$i]."</td>
				<td>".$res1['A353'][$i]."</td>
				</tr>";
			}
			
		}
	}else if ($_GET['report']=="new_ph2"){
		$query1 = "select * from VW_NET1_ALL_NEWBUILDS where (trim(WOR_HSDPA_CLUSTER) LIKE 'HSDPA Phase 2%' OR trim(WOR_HSDPA_CLUSTER) LIKE '%Phase 1+%'
				OR trim(WOR_HSDPA_CLUSTER) LIKE '%Phase 1 +%')
				AND trim(WOE_RANK)=1 
				AND trim(WOR_DOM_WOS_CODE) IN ('IS')   
				AND SUBSTR(SIT_UDK,2,6) IN(
			select SUBSTR(SIT_UDK,2,6) AS SITE from VW_NET1_ALL_NEWBUILDS where trim(WOR_HSDPA_CLUSTER) LIKE 'HSDPA Phase 2%' AND trim(WOE_RANK)=1
			AND trim(WOR_DOM_WOS_CODE) IN ('IS')
             minus
             select SITEID from BSDS_RAF) ORDER BY SIT_UDK";
				//echo "<br><br>".$query1;
		$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			//echo count($res1['SIT_UDK']);
			for ($i=0;$i<count($res1['SIT_UDK']);$i++){
					echo "<tr><td>".$res1['SIT_UDK'][$i]."</td>
				<td>".$res1['WOR_HSDPA_CLUSTER'][$i]."</td>
				<td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
				<td>".$res1['DRE_V2_1_6'][$i]."</td>
				<td>".$res1['DRE_V20_1'][$i]."</td>
				<td>".$res1['WOR_NAME'][$i]."</td>
				<td>".$res1['SAC'][$i]."</td>
				<td>".$res1['ALSAC'][$i]."</td>
				<td>".$res1['A501'][$i]."</td>
				<td>".$res1['CON'][$i]."</td>
				<td>".$res1['ALCON'][$i]."</td>
				<td>".$res1['A503'][$i]."</td>
				<td>".$res1['A353'][$i]."</td>
				</tr>";
			}
			
		}
	}else if ($_GET['report']=="upg_ph1"){
		$query1 = "select * from VW_NET1_ALL_UPGRADES WHERE trim(WOR_HSDPA_CLUSTER) LIKE '%Phase 1%' 
					AND trim(WOR_HSDPA_CLUSTER) NOT LIKE '%Phase 1+%' 
					AND trim(WOR_HSDPA_CLUSTER) NOT LIKE '%Phase 1 +%'
					AND trim(WOR_DOM_WOS_CODE) IN ('IS')
					AND trim(WOR_LKP_WCO_CODE) NOT LIKE '%HSPX%' 
					AND SUBSTR(SIT_UDK,2,6) IN (
					select SUBSTR(SIT_UDK,2,6) AS SITE from VW_NET1_ALL_UPGRADES 
					where trim(WOR_HSDPA_CLUSTER) LIKE 'HSDPA Phase 1%' 
					AND trim(WOR_HSDPA_CLUSTER) NOT LIKE 'HSDPA Phase 1 +%' 
					AND trim(WOR_HSDPA_CLUSTER) NOT LIKE 'HSDPA Phase 1+%' 
					AND trim(WOR_LKP_WCO_CODE) NOT LIKE '%HSPX%'
					AND trim(WOR_DOM_WOS_CODE) IN ('IS')
		 		minus
				 select SITEID from BSDS_RAF WHERE TYPE NOT LIKE '%TRX%' 
				 AND TYPE NOT LIKE '%CTX%' AND TYPE NOT LIKE '%CAB%' 
				 AND TYPE NOT LIKE '%RPT%' AND TYPE NOT LIKE '%CWK%'
				 AND TYPE NOT LIKE '%ANT%' AND TYPE NOT LIKE '%ASC%')  ORDER BY SIT_UDK";
				//echo "<br><br>".$query1;
		$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			//echo count($res1['SIT_UDK']);
			for ($i=0;$i<count($res1['SIT_UDK']);$i++){
				echo "<tr><td>".$res1['SIT_UDK'][$i]."</td>
				<td>".$res1['WOR_HSDPA_CLUSTER'][$i]."</td>
				<td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
				<td>".$res1['WOR_LKP_WCO_CODE'][$i]."</td>
				<td>".$res1['WOR_NAME'][$i]."</td>
				<td>".$res1['SAC'][$i]."</td>
				<td>".$res1['ALSAC'][$i]."</td>
				<td>".$res1['U501'][$i]."</td>
				<td>".$res1['CON'][$i]."</td>
				<td>".$res1['ALCON'][$i]."</td>
				<td>".$res1['U503'][$i]."</td>
				<td>".$res1['U353'][$i]."</td></tr>";
			}
			
		}
	}else if ($_GET['report']=="upg_ph2"){
		/*
		$query1 = "SELECT    *
		FROM       VW_NET1_ALL_UPGRADES  a
		WHERE      NOT EXISTS (SELECT * FROM BSDS_RAF  b WHERE trim(a.SIT_UDK) = b.NET1_LINK) 
		AND trim(WOR_HSDPA_CLUSTER) = 'HSDPA Phase 2' AND trim(WOR_LKP_WCO_CODE) NOT LIKE '%HSPX%'";*/
		
		$query1 = "select * from VW_NET1_ALL_UPGRADES WHERE (trim(WOR_HSDPA_CLUSTER) LIKE 'HSDPA Phase 2%' OR trim(WOR_HSDPA_CLUSTER) LIKE '%Phase 1+%'
		OR trim(WOR_HSDPA_CLUSTER) LIKE '%Phase 1 +%') 
		AND trim(WOR_LKP_WCO_CODE) NOT LIKE '%HSPX%' AND SUBSTR(SIT_UDK,2,6) AND trim(WOR_DOM_WOS_CODE) IN ('IS')
		IN (
		select SUBSTR(SIT_UDK,2,6) AS SITE from VW_NET1_ALL_UPGRADES 
		where (trim(WOR_HSDPA_CLUSTER) LIKE 'HSDPA Phase 2%' OR trim(WOR_HSDPA_CLUSTER) LIKE '%Phase 1+%'
		OR trim(WOR_HSDPA_CLUSTER) LIKE '%Phase 1 +%') 
		AND trim(WOR_LKP_WCO_CODE) NOT LIKE '%HSPX%'
		AND trim(WOR_DOM_WOS_CODE) IN ('IS')
		 minus
		 select SITEID from BSDS_RAF WHERE TYPE NOT LIKE '%TRX%' AND TYPE NOT LIKE '%CTX%' AND TYPE NOT LIKE '%CAB%' AND TYPE NOT LIKE '%RPT%' AND TYPE NOT LIKE '%CWK%'
		 AND TYPE NOT LIKE '%ANT%' AND TYPE NOT LIKE '%ASC%')  ORDER BY SIT_UDK";
				
		//echo "<br><br>".$query1;
		$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			//echo count($res1['SIT_UDK']);
			for ($i=0;$i<count($res1['SIT_UDK']);$i++){
				echo "<tr><td>".$res1['SIT_UDK'][$i]."</td>
				<td>".$res1['WOR_HSDPA_CLUSTER'][$i]."</td>
				<td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
				<td>".$res1['WOR_LKP_WCO_CODE'][$i]."</td>
				<td>".$res1['WOR_NAME'][$i]."</td>
				<td>".$res1['SAC'][$i]."</td>
				<td>".$res1['ALSAC'][$i]."</td>
				<td>".$res1['U501'][$i]."</td>
				<td>".$res1['CON'][$i]."</td>
				<td>".$res1['ALCON'][$i]."</td>
				<td>".$res1['U503'][$i]."</td>
				<td>".$res1['U353'][$i]."</td></tr>";
			}
					
		}
	}
	
	?>
	</table>
	</BODY>
	</HTML>
	<?
}
?>
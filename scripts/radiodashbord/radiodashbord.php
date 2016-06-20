<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_RF","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query = "SELECT t1.N1_SITEID,
t1.N1_CANDIDATE,
t1.N1_CLASSCODE,
t1.A80U380,
t1.G18_A97,
t1.G18_ASSET,
t1.G18_CHECK,
t1.G9_A99,
t1.G9_ASSET,
t1.G9_CHECK,
t1.U9_A508,
t1.U9_ASSET,
t1.U9_CHECK,
t1.U21_A197,
t1.U21_ASSET,
t1.U21_CHECK,
t1.L8_A1973,
t1.L8_ASSET,
t1.L8_CHECK,
t1.L18_A1975,
t1.L18_ASSET,
t1.L18_CHECK,
t1.N1_TX_CATEGORY,
t1.ASSET_TECHNOS_ACTIVE,
t1.OS_TECHNOS
FROM VW_POPSTATS t1 LEFT JOIN SWITCH_HALTEDCELLS  t2
ON  substr(N1_CANDIDATE,2,6) =substr(CELLID,1,6)
WHERE ((G18_A97 IS NOT NULL AND G18_ASSET='NOT ACTIVE') OR  (G18_A97 IS NULL AND G18_ASSET='ACTIVE')
OR  (G9_A99 IS NOT NULL AND G9_ASSET='NOT ACTIVE') OR  (G9_A99 IS NULL AND G9_ASSET='ACTIVE')
OR (U9_A508 IS NOT NULL AND U9_ASSET='NOT ACTIVE') OR  (U9_A508 IS NULL AND U9_ASSET='ACTIVE')
 OR (U21_A197 IS NOT NULL AND U21_ASSET='NOT ACTIVE') OR  (U21_A197 IS NULL AND U21_ASSET='ACTIVE') 
 OR (L8_A1973 IS NOT NULL AND L8_ASSET='NOT ACTIVE') OR  (L8_A1973 IS NULL AND L8_ASSET='ACTIVE') 
 OR (L18_A1975 IS NOT NULL AND L18_ASSET='NOT ACTIVE') OR  (L18_A1975 IS NULL AND L18_ASSET='ACTIVE'))
AND CELLID IS NULL
GROUP BY
t1.N1_SITEID,
t1.N1_CANDIDATE,
t1.N1_CLASSCODE,
t1.A80U380,
t1.G18_A97,
t1.G18_ASSET,
t1.G18_CHECK,
t1.G9_A99,
t1.G9_ASSET,
t1.G9_CHECK,
t1.U9_A508,
t1.U9_ASSET,
t1.U9_CHECK,
t1.U21_A197,
t1.U21_ASSET,
t1.U21_CHECK,
t1.L8_A1973,
t1.L8_ASSET,
t1.L8_CHECK,
t1.L18_A1975,
t1.L18_ASSET,
t1.L18_CHECK,
t1.N1_TX_CATEGORY,
t1.ASSET_TECHNOS_ACTIVE,
t1.OS_TECHNOS
ORDER BY t1.N1_CANDIDATE ASC";
//BY SITE_BLOCKED DESC,
//substr(CELLID,1,6) AS SITE_BLOCKED 
//substr(CELLID,1,6)
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount= count($res1['N1_CANDIDATE']);
}
for ($i = 0; $i <$amount; $i++){
	$wrongly_deb.="<tr><td>".$i."</td><td>".$res1['N1_SITEID'][$i]."</td>";
	$wrongly_deb.="<td>".$res1['N1_CANDIDATE'][$i]."</td><td>".$res1['A80U380'][$i]."</td>";
	if ($res1['G18_CHECK'][$i]=="NOT OK"){
		$wrongly_deb.="<td class='danger'>".$res1['G18_A97'][$i]."</td>";
	}else{
		$wrongly_deb.="<td>".$res1['G18_A97'][$i]."</td>";
	}
	if ($res1['G9_CHECK'][$i]=="NOT OK"){
		$wrongly_deb.="<td class='danger'>".$res1['G9_A99'][$i]."</td>";
	}else{
		$wrongly_deb.="<td>".$res1['G9_A99'][$i]."</td>";
	}
	if ($res1['U9_CHECK'][$i]=="NOT OK"){
		$wrongly_deb.="<td class='danger'>".$res1['U9_A508'][$i]."</td>";
	}else{
		$wrongly_deb.="<td>".$res1['U9_A508'][$i]."</td>";
	}
	if ($res1['U21_CHECK'][$i]=="NOT OK"){
		$wrongly_deb.="<td class='danger'>".$res1['U21_A197'][$i]."</td>";
	}else{
		$wrongly_deb.="<td>".$res1['U21_A197'][$i]."</td>";
	}
	if ($res1['L8_CHECK'][$i]=="NOT OK"){
		$wrongly_deb.="<td class='danger'>".$res1['L8_A1973'][$i]."</td>";
	}else{
		$wrongly_deb.="<td>".$res1['L8_A1973'][$i]."</td>";
	}
	if ($res1['L18_CHECK'][$i]=="NOT OK"){
		$wrongly_deb.="<td class='danger'>".$res1['L18_A1975'][$i]."</td>";
	}else{
		$wrongly_deb.="<td>".$res1['L18_A1975'][$i]."</td>";
	}
	if ($res1['ASSET_TECHNOS_ACTIVE'][$i]=="NOT IN ASSET"){
		$wrongly_deb.="<td class='danger'>&nbsp;</td>";
	}else{
		$wrongly_deb.="<td>".$res1['ASSET_TECHNOS_ACTIVE'][$i]."</td>";
	}
	$wrongly_deb.="<td>".$res1['OS_TECHNOS'][$i]."</td>";
	if ($res1['SITE_BLOCKED'][$i]!=""){
		$wrongly_deb.="<td class='danger'>".$res1['SITE_BLOCKED'][$i]."</td></tr>";
	}else{
		$wrongly_deb.="<td>&nbsp;</td></tr>";
	}
}

$query="SELECT SITEID FROM VW_ASSET_TECHNOS_ACTIVE GROUP BY SITEID HAVING COUNT(SITEID)>1";
$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt2);
	$amount_wrongCand= count($res2['SITEID']);
}

//echo "<pre>".print_r($dataACQ_IS,true)."</pre>";
?>
<div class="row">
	<div class="col-md-10">
	    <div class="panel panel-default" id="ImportStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">Wrongly debarred sites or wrongly active in Asset (<?=$amount?>)</h3>
	      	</div>
	      	<div class="panel-body" style="height:400px;overflow:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<tr>
		      			<th>#</th>
		      			<th>SITEID</th>
		      			<th>CANDIDATE</th>
		      			<th>A80</th>
		      			<th>G18 (A97)</th>
		      			<th>G9 (A99)</th>
		      			<th>U9 (A508)</th>
		      			<th>U21 (A197)</th>
		      			<th>L8 (A1973)</th>
		      			<th>L18 (A1975)</th>
		      			<th>ACTIVE ASSET</th>
		      			<th>OS TECHNOS</th>
		      			<th>MAN HALTED</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php echo $wrongly_deb; ?>
		      		</tbody>
		      		<tfoot>
		      			<tr>
		      				<td class="danger">&nbsp;</td>
		      				<td colspan="11">= techno date not debarred in NET1 or should not be active in n1</td>
		      		</tfoot>
	      		</table>
	      	<?php }else{ ?>
	      		Everything is OK
	      	<?php } ?>
	      	
	     	</div>
	    </div>
	</div>
	<div class="col-md-2">
	    <div class="panel panel-default" id="ImportStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">Wrong canidates in Asset (<?=$amount_wrongCand?>)</h3>
	      	</div>
	      	<div class="panel-body" style="height:400px;overflow-y:auto">
	      		<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<th>#</th>
		      			<th>SITEID</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php 
		      			for ($i = 0; $i <$amount_wrongCand; $i++){ 
		      				echo "<tr><td>".$i."</td>";
		      				echo "<td class='danger'>".$res2['SITEID'][$i]."</td></tr>";
		      			} ?>
		      		</tbody>
		      		<tfoot>
		      			<tr>
		      				<td class="danger">&nbsp;</td>
		      				<td colspan="10">= sites with a different candidate name active for different techno's</td>
		      		</tfoot>
	      		</table>
	      		<?php }else{ ?>
	      		Everything is OK
	      		<?php } ?>
	      	</div>
	    </div>
	</div>
</div>

<div class="row">
	 <div class="col-md-12">
	    <div class="panel panel-default" id="ImportStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">Sites debarred last month</h3>
	      	</div>
	      	<div class="panel-body" style="height:400px;overflow-y:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      	</table>
	      	<?php } ?>
	     	</div>
	    </div>
	</div>
</div>

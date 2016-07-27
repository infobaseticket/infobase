<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query = "select N1_SITEID,N1_CANDIDATE,N1_UPGNR, N1_CON,CON_PARTNER as IB_CON from MASTER_REPORT MA LEFT JOIN BSDS_RAFV2 RA on ma.IB_RAFID=RA.RAFID
WHERE N1_STATUS='IS' AND (UPPER(MA.N1_CON) != UPPER(RA.CON_PARTNER) OR (MA.N1_CON IS NULL AND RA.CON_PARTNER!='NOT OK')) 
AND CON_PARTNER!='NOT OK' AND CON_PARTNER!='OK' AND NET1_PAC='NOT OK'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount= count($res1['N1_CANDIDATE']);
}
for ($i = 0; $i <$amount; $i++){
	$wrong_con.="<tr><td>".$i."</td>
	<td>".$res1['N1_SITEID'][$i]."</td>
	<td>".$res1['N1_CANDIDATE'][$i]."</td>
	<td>".$res1['N1_UPGNR'][$i]."</td>
	<td>".$res1['N1_CON'][$i]."</td>
	<td>".$res1['IB_CON'][$i]."</td></tr>";
}
//echo "<pre>".print_r($dataACQ_IS,true)."</pre>";
?>
<div class="row">
	 <div class="col-md-6">
	    <div class="panel panel-default" id="ImportStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">CON PARTNER N1 != CON PARTNER RAF (<?=$amount?>)</h3>
	      	</div>
	      	<div class="panel-body" style="height:300px;overflow-y:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<th>#</th>
		      			<th>SITEID</th>
		      			<th>CANDIDATE</th>
		      			<th>UPGNR</th>
		      			<th>N1 CON</th>
		      			<th>IB CON</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php echo $wrong_con; ?>
		      		</tbody>
	      		</table>
	      	<?php }else{ ?>
	      		Everything is OK
	      	<?php } ?>
	      	
	     	</div>
	    </div>
	</div>


<?php
$query = "select N1_SITEID,N1_CANDIDATE,N1_UPGNR, N1_SAC,ACQ_PARTNER as IB_SAC from MASTER_REPORT MA LEFT JOIN BSDS_RAFV2 RA on ma.IB_RAFID=RA.RAFID
WHERE N1_STATUS='IS' AND (UPPER(MA.N1_SAC) != UPPER(RA.ACQ_PARTNER) OR (MA.N1_SAC IS NULL AND RA.ACQ_PARTNER!='NOT OK')) 
AND ACQ_PARTNER!='NOT OK' AND ACQ_PARTNER!='NA' AND BUFFER!=1 AND ACQ_PARTNER!='OK' AND NET1_PAC='NOT OK'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount= count($res1['N1_CANDIDATE']);
}
for ($i = 0; $i <$amount; $i++){
	$wrong_sac.="<tr><td>".$i."</td>
	<td>".$res1['N1_SITEID'][$i]."</td>
	<td>".$res1['N1_CANDIDATE'][$i]."</td>
	<td>".$res1['N1_UPGNR'][$i]."</td>
	<td>".$res1['N1_SAC'][$i]."</td>
	<td>".$res1['IB_SAC'][$i]."</td></tr>";
}
//echo "<pre>".print_r($dataACQ_IS,true)."</pre>";
?>

	 <div class="col-md-6">
	    <div class="panel panel-default" id="ImportStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">SAC PARTNER N1 != SAC PARTNER RAF (<?=$amount?>)</h3>
	      	</div>
	      	<div class="panel-body" style="height:300px;overflow-y:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<th>#</th>
		      			<th>SITEID</th>
		      			<th>CANDIDATE</th>
		      			<th>UPGNR</th>
		      			<th>N1 SAC</th>
		      			<th>IB SAC</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php echo $wrong_sac; ?>
		      		</tbody>
	      		</table>
	      	<?php }else{ ?>
	      		Everything is OK
	      	<?php } ?>
	      	
	     	</div>
	    </div>
	</div>
</div>

<?php

$query = "SELECT RA1.RAFID as RAFID1 ,RA1.SITEID as SITEID1,RA1.NET1_LINK as NET1_LINK1,RA2.RAFID as RAFID2,RA2.SITEID,RA2.NET1_LINK,
RA1.NET1_PAC, RA2.NET1_PAC
 FROM BSDS_RAFV2 RA1 INNER JOIN BSDS_RAFV2 RA2
ON RA1.NET1_LINK=RA2.NET1_LINK WHERE RA1.RAFID!=RA2.RAFID AND RA1.SITEID=RA2.SITEID
AND RA1.DELETED!='yes' AND RA2.DELETED!='yes' AND RA1.NET1_PAC='NOT OK' AND RA2.NET1_PAC='NOT OK'
AND RA1.SITEID NOT LIKE 'CT%'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount= count($res1['RAFID1']);
}
for ($i = 0; $i <$amount; $i++){
	$wrong_raf.="<tr><td>".$i."</td>
	<td>".$res1['RAFID1'][$i]."</td>
	<td>".$res1['RAFID2'][$i]."</td>
	<td>".$res1['SITEID1'][$i]."</td>
	<td>".$res1['NET1_LINK1'][$i]."</td></tr>";
}
//echo "<pre>".print_r($dataACQ_IS,true)."</pre>";
?>
<div class="row">
	 <div class="col-md-4">
	    <div class="panel panel-default" id="ImportStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">DUPLICATE RAF's FOR SAME ACTIVITY (<?=$amount?>)</h3>
	      	</div>
	      	<div class="panel-body" style="height:300px;overflow-y:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<th>#</th>
		      			<th>RAFID 1</th>
		      			<th>RAFID 2</th>
		      			<th>SITEID</th>
		      			<th>NET1 LINK</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php echo $wrong_raf; ?>
		      		</tbody>
	      		</table>
	      	<?php }else{ ?>
	      		Everything is OK
	      	<?php } ?>
	      	
	     	</div>
	    </div>
	</div>

<?php
$query = "select t1.FILENAME||'.'||t1.EXTENSION as FILE_ON_RANBMT,t1.SUBPATH from RAN_SCAN_TODAY t1 
LEFT OUTER jOIN RAN_SCAN_TODAY t2 ON t1.FILENAME=t2.FILENAME
AND t1.PARTNER='BENCHMARK_RAN' AND t2.PARTNER='RAN-ALU'
WHERE t2.FILENAME IS NULL AND
t1.PARTNER='BENCHMARK_RAN' AND t1.FILENAME IS NOT NULL AND t1.SUBPATHNAME NOT LIKE '%.FUSE_HIDDEN%'
ORDER BY t1.FILENAME";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount= count($res1['FILE_ON_RANBMT']);
}
for ($i = 0; $i <$amount; $i++){
	$wrong_ranbmt.="<tr><td>".$i."</td>
	<td>".$res1['FILE_ON_RANBMT'][$i]."</td>
	<td>".$res1['SUBPATH'][$i]."</td></tr>";
}
//echo "<pre>".print_r($dataACQ_IS,true)."</pre>";
?>

	<div class="col-md-4">
	    <div class="panel panel-default" id="ImportStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">MISSING FILES ON RAN ALU (WHICH ARE AVAILABLE ON RAN BMT) (<?=$amount?>)</h3>
	      	</div>
	      	<div class="panel-body" style="height:300px;overflow-y:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<th>#</th>
		      			<th>FILE_ON_RANBMT</th>
		      			<th>SUBPATH</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php echo $wrong_ranbmt; ?>
		      		</tbody>
	      		</table>
	      	<?php }else{ ?>
	      		Everything is OK
	      	<?php } ?>
	      	
	     	</div>
	    </div>
	</div>

	
<?php
$query = "SELECT MA.N1_SITEID as N1_SITEID,MA.N1_CANDIDATE as N1_CANDIDATE, MA2.N1_CANDIDATE , MA2.N1_UPGNR as N1_UPGNR , 
MA.N1_STATUS AS STATUS1,MA2.N1_STATUS AS STATUS2
FROM MASTER_REPORT MA LEFT JOIN
MASTER_REPORT MA2  ON MA.N1_CANDIDATE=MA2.N1_CANDIDATE AND MA.N1_SITEID=MA2.N1_SITEID
WHERE MA.N1_STATUS!='IS' AND MA2.N1_STATUS='IS' AND MA.N1_NBUP='NB' AND MA2.N1_NBUP='UPG'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount= count($res1['N1_SITEID']);
}
for ($i = 0; $i <$amount; $i++){
	$wrong_is.="<tr><td>".$i."</td>
	<td>".$res1['N1_SITEID'][$i]."</td>
	<td>".$res1['N1_CANDIDATE'][$i]."</td>
	<td>".$res1['N1_UPGNR'][$i]."</td>
	<td>".$res1['STATUS1'][$i]."</td>
	<td>".$res1['STATUS2'][$i]."</td></tr>";
}
//echo "<pre>".print_r($dataACQ_IS,true)."</pre>";
?>

	 <div class="col-md-4">
	    <div class="panel panel-default" id="ImportStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">NB SITES NOT <b>IS</b> BUT HAVING UPGRADES WHICH ARE <b>IS</b> (<?=$amount?>)</h3>
	      	</div>
	      	<div class="panel-body" style="height:300px;overflow-y:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<th>#</th>
		      			<th>SITEID</th>
		      			<th>CANDIDATE</th>
		      			<th>UPGNR</th>
		      			<th>NB</th>
		      			<th>UPG</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php echo $wrong_is; ?>
		      		</tbody>
	      		</table>
	      	<?php }else{ ?>
	      		Everything is OK
	      	<?php } ?>
	      	
	     	</div>
	    </div>
	</div>
</div>

<?php
/*
select *
from MASTER_REPORT MA3
WHERE MA3.N1_UPGNR NOT IN(
select MA2.N1_UPGNR from MASTER_REPORT MA 
LEFT JOIN MASTER_REPORT MA2
ON MA.N1_CANDIDATE=MA2.N1_CANDIDATE
WHERE MA.N1_STATUS='IS' AND MA.N1_SITEID LIKE 'T%'
AND MA.N1_NBUP='NB' AND MA2.N1_NBUP='UPG' AND MA2.N1_SITETYPE='DISM')
AND MA3.N1_STATUS='IS' AND MA3.N1_SITEID LIKE 'T%' AND  MA3.N1_NBUP='NB'¨*/

$query = "select MA3.N1_SITEID as N1_SITEID, MA3.N1_CANDIDATE as N1_CANDIDATE, MA3.N1_SITETYPE as N1_SITETYPE
from MASTER_REPORT MA3
WHERE MA3.N1_UPGNR NOT IN(
select MA2.N1_UPGNR from MASTER_REPORT MA 
LEFT JOIN MASTER_REPORT MA2
ON MA.N1_CANDIDATE=MA2.N1_CANDIDATE
WHERE MA.N1_STATUS='IS' AND MA.N1_SITEID LIKE 'T%'
AND MA.N1_NBUP='NB' AND MA2.N1_NBUP='UPG' AND MA2.N1_SITETYPE='DISM')
AND MA3.N1_STATUS='IS' AND MA3.N1_SITEID LIKE 'T%' AND  MA3.N1_NBUP='NB'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount= count($res1['N1_SITEID']);
}
for ($i = 0; $i <$amount; $i++){
	$wrong_dism.="<tr><td>".$i."</td>
	<td>".$res1['N1_SITEID'][$i]."</td>
	<td>".$res1['N1_CANDIDATE'][$i]."</td>
	<td>".$res1['N1_SITETYPE'][$i]."</td></tr>";
}

$query = "SELECT N1_SITEID,N1_CANDIDATE, POPR, ACQCON,N1_UPGNR, A59U459, IB_RAFID FROM MASTER_REPORT t1 LEFT JOIN BSDS_RAF_PO t2 ON t1.IB_RAFID=t2.RAFID AND  ACQCON='CON'
WHERE A59U459 IS NOT NULL AND POPR IS NULL AND N1_STATUS='IS' AND IB_RAFID IS NOT NULL";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount= count($res1['N1_SITEID']);
}
for ($i = 0; $i <$amount; $i++){
	$wrong_po1.="<tr><td>".$i."</td>
	<td>".$res1['N1_SITEID'][$i]."</td>
	<td>".$res1['N1_CANDIDATE'][$i]."</td>
	<td>".$res1['N1_UPGNR'][$i]."</td>
	<td>".$res1['IB_RAFID'][$i]."</td>
	<td>".$res1['A59U459'][$i]."</td></tr>";
}

$query = "SELECT N1_SITEID,N1_CANDIDATE, POPR, ACQCON,N1_UPGNR, A26U326, IB_RAFID FROM MASTER_REPORT t1 LEFT JOIN BSDS_RAF_PO t2 ON t1.IB_RAFID=t2.RAFID AND  ACQCON='ACQ'
WHERE A26U326 IS NOT NULL AND POPR IS NULL AND N1_STATUS='IS' AND IB_RAFID IS NOT NULL";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount= count($res1['N1_SITEID']);
}
for ($i = 0; $i <$amount; $i++){
	$wrong_po2.="<tr><td>".$i."</td>
	<td>".$res1['N1_SITEID'][$i]."</td>
	<td>".$res1['N1_CANDIDATE'][$i]."</td>
	<td>".$res1['N1_UPGNR'][$i]."</td>
	<td>".$res1['IB_RAFID'][$i]."</td>
	<td>".$res1['A59U459'][$i]."</td></tr>";
}

//echo "<pre>".print_r($dataACQ_IS,true)."</pre>";
?>
<div class="row">
	<div class="col-md-4">
	    <div class="panel panel-default" id="ImportStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">T sites where no DISM UPG has been issued (<?=$amount?>)</h3>
	      	</div>
	      	<div class="panel-body" style="height:300px;overflow-y:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<th>#</th>
		      			<th>SITEID</th>
		      			<th>CANDIDATE</th>
		      			<th>SITETYPE</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php echo $wrong_dism; ?>
		      		</tbody>
	      		</table>
	      	<?php }else{ ?>
	      		Everything is OK
	      	<?php } ?>
	      	
	     	</div>
	    </div>
	</div>
	<div class="col-md-4">
	    <div class="panel panel-default" id="POStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">CON SITE PHYSICAL START WITHOUT PO</h3>
	      	</div>
	      	<div class="panel-body" style="height:300px;overflow-y:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<th>#</th>
		      			<th>SITEID</th>
		      			<th>CANDIDATE</th>
		      			<th>N1 UPGNR</th>
		      			<th>RAFID</th>
		      			<th>A59U459</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php echo $wrong_po1; ?>
		      		</tbody>
	      		</table>
	      	<?php }else{ ?>
	      		Everything is OK
	      	<?php } ?>
	      	
	     	</div>
	    </div>
	</div>
	<div class="col-md-4">
	    <div class="panel panel-default" id="POStatus">
	      	<div class="panel-heading">
	        	<h3 class="panel-title">CON SITE PHYSICAL START WITHOUT PO</h3>
	      	</div>
	      	<div class="panel-body" style="height:300px;overflow-y:auto">
	      	<?php if ($amount!=0){ ?>
	      		<table class="table table-condensed">
		      		<thead>
		      			<th>#</th>
		      			<th>SITEID</th>
		      			<th>CANDIDATE</th>
		      			<th>N1 UPGNR</th>
		      			<th>RAFID</th>
		      			<th>A26U326</th>
		      			</tr>
		      		</thead>
		      		<tbody>
		      			<?php echo $wrong_po2; ?>
		      		</tbody>
	      		</table>
	      	<?php }else{ ?>
	      		Everything is OK
	      	<?php } ?>
	      	
	     	</div>
	    </div>
	</div>
</div>
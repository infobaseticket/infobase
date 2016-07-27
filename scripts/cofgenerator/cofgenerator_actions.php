
<?php
include_once('/var/www/html/bsds/config.php');
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");
require("/var/www/html/bsds/PHPlibs/phpmailer/class.phpmailer.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

function udate($format, $utimestamp = null) {
  if (is_null($utimestamp))
    $utimestamp = microtime(true);

  $timestamp = floor($utimestamp);
  $milliseconds = round(($utimestamp - $timestamp) * 1000000);

  return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}




//We first check which RAF have issues in the COF info:
$query = "Select t2.RAFID, t2.NET1_LINK, t2.SITEID, ENG, N1_SITETYPE, N1_CANDIDATE, COF_ACQ, COF_CON FROM BSDS_RAF_COF t1 LEFT JOIN BSDS_RAFV2 t2 ON t1.RAFID=t2.RAFID LEFT JOIN COF_MASTERFILE t3 
on t1.MATERIAL_CODE=t3.MATERIAL AND t1.ACQCON=t3.ACQ_CON LEFT JOIN MASTER_REPORT ON NET1_LINK=N1_CANDIDATE OR NET1_LINK=N1_UPGNR
LEFT JOIN COST_CENTERS ON N1_SITETYPE = SITETYPE
WHERE EXPORTED=0 AND (N1_SITETYPE IS NULL or ENG IS NULL) AND (COF_ACQ='BASE OK' OR COF_CON='BASE_OK')
ORDER BY t3.ORDERCOL";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}

for ($i = 0; $i < count($res1['RAFID']); $i++) {
	$outnotok.="<tr><td>".$res1['SITEID'][$i]."</td>
	<td>".$res1['RAFID'][$i]."</td>
	<td>".$res1['NET1_LINK'][$i]."</td>
	<td>".$res1['N1_CANDIDATE'][$i]."</td>
	<td>".$res1['ENG'][$i]."</td>
	<td>".$res1['N1_SITETYPE'][$i]."</td>
	<td>".$res1['COF_ACQ'][$i]."</td>
	<td>".$res1['COF_CON'][$i]."</td></tr>";
}
?>
<div class="panel panel-warning">
  <div class="panel-heading">RAF for which COF could not be generated because of errors:</div>
  <div class="panel-body">
  	<table class="table">
  	<thead>
  		<tr>
  		<th>SITEID</th>
  		<th>RAFID</th>
  		<th>NET1 LINK</th>
  		<th>CANDIDATE</th>
  		<th>COST CENTER</th>
  		<th>SITE TYPE</th>
  		<th>COF ACQ</th>
  		<th>COF CON</th>
  		</tr>
  	</thead>
  	<tbody>
  		<?=$outnotok?>
  	</tbody>
  	</table>
  </div>
</div>

<?php

$query = "Select * FROM BSDS_RAF_COF t1 LEFT JOIN BSDS_RAFV2 t2 ON t1.RAFID=t2.RAFID LEFT JOIN COF_MASTERFILE t3 
on t1.MATERIAL_CODE=t3.MATERIAL AND t1.ACQCON=t3.ACQ_CON LEFT JOIN MASTER_REPORT ON NET1_LINK=N1_CANDIDATE OR NET1_LINK=N1_UPGNR
LEFT JOIN COST_CENTERS ON N1_SITETYPE = SITETYPE
WHERE EXPORTED=0 AND N1_STATUS='IS' AND (
	(COF_ACQ = 'BASE OK' AND t1.ACQCON='ACQ')
	OR (COF_CON = 'BASE OK' AND t1.ACQCON='CON')
) AND ENG IS NOT NULL AND N1_SITETYPE IS NOT NULL
ORDER BY t1.RAFID, t3.ORDERCOL";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}

if(count($res1['RAFID'])>0){
	//Location where file will be stored:
	$filename="COF_".date('dmY_His').".txt";
	$handle = fopen("/var/www/html/RAN/RAN_INFOBASE/Infobase_Imports/COF/SAP/".$filename, "w+");
}
//echo count($res1['RAFID']);
for ($i = 0; $i < count($res1['RAFID']); $i++) {

	//echo '<hr>RAFID='.$res1['RAFID'][$i]." (".$res1['NET1_LINK'][$i].") ".$res1['SITEID'][$i]." | " .$res1['TYPE'][$i]." | " .$res1['MATERIAL'][$i]."\r\n";

	$outok.="<tr><td>".$res1['SITEID'][$i]."</td><td>".$res1['RAFID'][$i]."</td><td>".$res1['MATERIAL'][$i]."</td></tr>";
	
	if ($res1['ACQCON'][$i]=="CON"){
		$partner=$res1['CON_PARTNER'][$i];
		//echo $res1['COF_CON_BY'][$i];
		$user_CREATION=getuserdata($res1['COF_CON_BY'][$i]);
		if ($user_CREATION['firstname']!=''){
			$user_created=substr($user_CREATION['firstname'],0,1).".".$user_CREATION['lastname'];
		}else{
			$user_created="D.Van.Craenenbroeck";
		}
		
		$budget=$res1['BUDGET_CON'][$i];
		$dateplus = date('d.m.Y',mktime(0, 0, 0, date("m"),   date("d"),   date("Y")+1));
		if($res1['N1_NBUP'][$i]!="UPG"){
			$SAPCODE="CN".$res1['SITEID'][$i]."*".$res1['RAFID'][$i];
		}else{
			$SAPCODE="CU".$res1['N1_UPGNR'][$i]."*".$res1['RAFID'][$i];
		}
		$ASSETCLASS="5010";
		
	}elseif ($res1['ACQCON'][$i]=="ACQ"){
		$partner=$res1['ACQ_PARTNER'][$i];
		$budget=$res1['BUDGET_ACQ'][$i];

		$user_CREATION=getuserdata($res1['COF_ACQ_BY'][$i]);
		if ($user_CREATION['firstname']!=''){
			$user_created=substr($user_CREATION['firstname'],0,1).".".$user_CREATION['lastname'];
		}else{
			$user_created="D.Van.Craenenbroeck";
		}
		$dateplus = date('d.m.Y',mktime(0, 0, 0, date("m")+6,   date("d"),   date("Y")));

		if($res1['N1_NBUP'][$i]!="UPG"){
			$SAPCODE="AN".$res1['SITEID'][$i]."*".$res1['RAFID'][$i];
		}else{
			$SAPCODE="AU".$res1['N1_UPGNR'][$i]."*".$res1['RAFID'][$i];
		}
		$ASSETCLASS="5200";
	}else{
		echo "<font color='red'><b>MISSING ACQ CON for !!!!</b></font>";
	}

	if ($res1['N1_SITETYPE'][$i]==""){
		echo "<font color='red'><b>N1_SITETYPE NOT FOUND BECAUSE OF ISSUE WITH NET1_LINK!!!!</b></font>";
	}

	if ($res1['RAFID'][$i]!=$prevRAFID){
		$unique_date=udate('YmdHisu');
	}

	if ($res1['N1_NBUP'][$i]=="NB" or $res1['N1_NBUP'][$i]=="NB REPL"){
		$N1_UPGNR="";
		$N1_UPGTYPE="";
	}else{
		$N1_UPGNR=$res1['N1_UPGNR'][$i];
		$N1_UPGTYPE=$res1['N1_SITETYPE'][$i];
	}

	if ($res1['ENG'][$i]!=''){
		//echo  $unique_date."\t".$res1['TYPE'][$i]."\t".$partner."\t\tA\t".$res1['MATERIAL_CODE'][$i]."\t".$res1['MATERIAL_DESCRIPTION'][$i];
		//echo "\t1\tEA\t".$dateplus."\t".$res1['MATERIAL_GROUP'][$i]."\t0020\tSERV\t".$res1['PURCH_GROUP'][$i]."\t".substr($user_CREATION['firstname'],0,1).".".$user_CREATION['lastname']."\t";
		//echo $budget."\t".$res1['VENDORNR'][$i]."\t".$price."\t".$res1['SITEID'][$i]."\t".$N1_UPGTYPE."\t".$N1_UPGNR."\t".$SAPCODE."\t\t".$res1['ENG'][$i]."\t".$ASSETCLASS."\t\t\t".$res1['DOCTYPE'][$i]."\t\t".$res1['VENDORNR'][$i];

		fwrite($handle, $unique_date."\t".$res1['TYPE'][$i]."\t".$partner."\t\tA\t".$res1['MATERIAL_CODE'][$i]."\t".$res1['MATERIAL_DESCRIPTION'][$i]);

		fwrite($handle, "\t1\tEA\t".$dateplus."\t".$res1['MATERIAL_GROUP'][$i]."\t0020\tSERV\t".$res1['PURCH_GROUP'][$i]."\t".$user_created."\t");
		
		fwrite($handle, $budget."\t".$res1['VENDORNR'][$i]."\t".$res1['SPRICE'][$i]."\t".$res1['SITEID'][$i]."\t".$N1_UPGTYPE."\t".$N1_UPGNR."\t".$SAPCODE."\t\t".$res1['ENG'][$i]."\t".$ASSETCLASS."\t\t\t".$res1['DOCTYPE'][$i]."\t\t".$res1['VENDORNR'][$i]);

		fwrite($handle, "\r\n");

		if (trim($prevRAFID)!=trim($res1['RAFID'][$i])){

			//echo $prevRAFID."!=".$res1['RAFID'][$i]."<br>";

			$rafdir=$config['raf_folder_abs'].$res1['RAFID'][$i]."/";

			if (file_exists($rafdir)){
				$files=scandir($rafdir);
				$BOQ_file_found=0;
				foreach ($files as $key => $file){

					$pos = strpos($file, "BOQ");
					if ($pos !== false){
						$BOQ_file_found=1;
						$BOQ_file=$rafdir.$file;
						$BOQ_filename=$file;
						break;
					}
				}			
			}else{
				$BOQ_file_found=0;
			}

			if ($BOQ_file_found==1){

				//echo $BOQ_file." :<br>";
				if (file_exists($config['ranfolderIB'].'Infobase_Imports/COF/BOQ/'.$BOQ_filename)){
					unlink($config['ranfolderIB'].'Infobase_Imports/COF/BOQ/'.$BOQ_filename);
				}
				copy($BOQ_file,$config['ranfolderIB'].'Infobase_Imports/COF/BOQ/'.$BOQ_filename);
				//echo "<hr>";
			}
		}

		$query3="UPDATE BSDS_RAF_COF SET EXPORTED='1' WHERE RAFID='".$res1['RAFID'][$i]."' AND MATERIAL_CODE='".$res1['MATERIAL_CODE'][$i]."'";
		//echo $query3;
		$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
		if (!$stmt3) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}

		$query3="INSERT INTO INFOBASE.BSDS_RAF_HISTORY (RAFID, ACTION_DATE, STATUS, ACTION_BY, FIELD) VALUES ('".$res1['RAFID'][$i]."',SYSDATE,'COF info exported to ".$filename."','".$guard_username."','COF EXPORT')";
		//echo $query3;
		$stmt3 = parse_exec_free($conn_Infobase, $query3, $error_str);
		if (!$stmt3) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
		}

	}else{
		echo "<font color='red'><b>ENG missing in COST_CENTERS TABLE!!!!</b></font>";
		die;
	}
	$prevRAFID=$res1['RAFID'][$i];
}

fclose($handle);

?>
<div class="panel panel-success">
  <div class="panel-heading">COF's successfully generated to <?=$filename?>:</div>
  <div class="panel-body">
  	<table class="table">
  	<thead>
  		<tr>
  		<th>SITEID</th>
  		<th>RAFID</th>
  		<th>POINFO</th>
  		</tr>
  	</thead>
  	<tbody>
  		<?=$outok?>
  	</tbody>
  	</table>
  </div>
</div>
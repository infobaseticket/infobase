<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

echo "<h3>SITES DEBARRED IN DIFFERENT MONTH THAN DEBARRING DATE</h3>";

echo "<table class='table' id='BOQVALIDATIONStable'>";
echo "<thead><tr><th>SITEID</th><th>CANDIDATE</th><th>SITETYPE</th><th>CON PARTNER</th><th>ACTION PARTNER OK</th><th>COF STATUS</th><th>AU305</th><th>A45U345</th><th>PRICE</th><th>Analysis date COF</th><th>REJECTIONS</th><th>REJECTION REASON</th><th>BOQ file</th></tr>";

$query = "select N1_SITEID, N1_SITETYPE, IB_CON, N1_CANDIDATE, AU305 , 
A45U345, MATERIAL_CODE, SPRICE, COF_CON_DATE, RA.RAFID AS RAFID,
HIS.ACTION_BY || ' @' || HIS.ACTION_DATE AS REJECTIONS,
HIS.STATUS AS REJECTION_REASON, 
IB_COFCON,
HIS2.ACTION_BY || ' @' || HIS2.ACTION_DATE AS PARTNER_OK
FROM MASTER_REPORT MA LEFT JOIN BSDS_RAFV2 RA ON RA.RAFID=MA.IB_RAFID
LEFT JOIN BSDS_RAF_COF COF ON RA.RAFID=COF.RAFID   
LEFT JOIN BSDS_RAF_HISTORY HIS ON RA.RAFID=HIS.RAFID AND HIS.FIELD ='COF_CON_REJECT'
LEFT JOIN BSDS_RAF_HISTORY HIS2 ON RA.RAFID=HIS2.RAFID AND HIS2.FIELD ='COF_CON' AND HIS2.STATUS='PARTNER OK'
WHERE 
IB_COFCON!='NOT OK' 
AND COF.ACQCON='CON'
AND COF.MATERIAL_CODE LIKE '%BOQ%'
ORDER BY IB_COFCON DESC, N1_CANDIDATE ASC";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}

if (count($res1['N1_SITEID'])>0){
	for ($i = 0; $i < count($res1['N1_SITEID']); $i++){

		$rafdir=$config['raf_folder_abs'].$res1['RAFID'][$i]."/";
		$BOQ_file_found='';
		if (file_exists($rafdir)){
			$files=scandir($rafdir);
			foreach ($files as $key => $file){
				$pos = strpos($file, "BOQ");
				if ($pos !== false){
					$BOQ_file_found.=$file;
					break;
				}
			}			
		}else{
			$BOQ_file_found='';
		}

		$rafDownLoadLoc=$config['raf_folder_abs'].$res1['RAFID'][$i]."/".urlencode($BOQ_file_found);

		if ($res1['IB_COFCON'][$i]=="REJECTED" or $res1['IB_COFCON'][$i]=="PARTNER OK"){
			$class="danger";
		}else{
			$class="success";
		}

	    echo "<tr>
	    <td>".$res1['N1_SITEID'][$i]."</td>
	    <td>".$res1['N1_CANDIDATE'][$i]."</td>
	    <td>".$res1['N1_SITETYPE'][$i]."</td>
	    <td>".$res1['IB_CON'][$i]."</td>
	    <td>".$res1['PARTNER_OK'][$i]."</td>
	    <td class='".$class."'>".$res1['IB_COFCON'][$i]."</td>
	    <td>".$res1['AU305'][$i]."</td>
	    <td>".$res1['A45U345'][$i]."</td>
	    <td>".$res1['SPRICE'][$i]."</td>
	    <td>".$res1['COF_CON_DATE'][$i]."</td>
	    <td>".$res1['REJECTIONS'][$i]."</td>
	    <td>".$res1['REJECTION_REASON'][$i]."</td>
	    <td>";
	    if ($BOQ_file_found!=''){
	    echo "<a class='btn btn-default btn-xs filedownload' target='_ne' title='Download file' href='scripts/filebrowser/filedownload.php?file=".$rafDownLoadLoc."&name=".str_replace(" ", "_", $BOQ_file_found)."'><span class='glyphicon glyphicon-download'></span></a> ".$BOQ_file_found;
	   	}
	    echo "</td></tr>";
	}
}else{
	echo "<tr><td colspan='8'>No BOQ to validate</td></tr>";
}
echo "</table>";
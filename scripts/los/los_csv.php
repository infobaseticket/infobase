<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$amountperpage=8;

if ($_POST['page']==""){
    $page=1;
}else{
    $page=$_POST['page'];
    $start= ($page-1)*$amountperpage +1;
    $end = ($page)*$amountperpage ;
}
 
$siteID=$_POST['siteID'];
$viewtype=$_POST['viewtype'];
if ($_POST['orderby']==""){
	$orderby="ID";
	$order="ASC";
}else{
	$orderby=$_POST['orderby'];
	$order=$_POST['order'];
}

if ($viewtype=="link"){
	$where=" WHERE ID='".$siteID."'";
}else if ($viewtype=="list"){
	$where=" WHERE (SITEA like '%".$siteID."%' OR SITEB like '%".$siteID."%') ";
}else if ($viewtype=="after_insert"){
	$where=" WHERE SITEA like '%".$siteID."%'";
}else if ($viewtype=="report"){
	$where="";

	if ($_POST['actionby']=="Partner Processing"){
		$where .= " WHERE (DONE = 'NOT OK' OR DONE='REJECTED')";
	}else if ($_POST['actionby']=="Partner Reporting"){
		$where .= " WHERE (REPORT = 'NOT OK' OR REPORT='REJECTED') AND DONE='OK'";
	}else if ($_POST['actionby']=="TXMN Resulting"){
		$where .= " WHERE RESULT = 'NOT OK' AND DONE='OK' AND REPORT='OK'";
	}else if ($_POST['actionby']=="Canceled"){
		$where .= " WHERE PRIORITY = 'Canceled'";
	}else{
		$where .= " WHERE SITEA IS NOT NULL";
	}
	if ($_POST['region']!="NA" && $_POST['region']!="ALL"){
		$where .= " AND (SITEA LIKE '%".$_POST['region']."%' OR SITEB LIKE '%".$_POST['region']."%')";
	}
	if ($_POST['type']!="" && $_POST['type']!="ALL"){
		$where .= " AND TYPE ='".$_POST['type']."'";
	}
}

if ($_POST['allocated']){
		$where .= " AND PARTNERVIEW LIKE '%".$_POST['allocated']."%'";
}
if (substr_count($guard_groups, 'ZTE')==1){
	$where .= " AND PARTNERVIEW LIKE '%ZTE%'";
}
if (substr_count($guard_groups, 'Bechmark')==1){
	$where .= " AND PARTNERVIEW LIKE '%BENCHMARK%'";
}
//echo $guard_groups;
if (substr_count($guard_groups, 'TechM')==1){
	$where .= " AND (PARTNERVIEW LIKE '%TECHM%' or PARTNERVIEW LIKE '%ALU%')";
}
if (substr_count($guard_groups, 'Alcatel')==1){
	$where .= " AND PARTNERVIEW LIKE '%ALU%'";
}


$query="SELECT *  FROM  BSDS_LINKINFO ".$where ." ORDER BY ".$orderby."  ".$order;

//echo $query;
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
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=file.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
		
	$out.= '"LOSID","SITEA","SITEB","PRIRITY","ACTION BY","LINK DESIGNER","PARTNER VIEWABLE","PARTNER PROCESSED","PARTNER REPORT CREATED","TXMN RESULT","TYPE"\r\n';			

	$k=0;
	for ($i = 0; $i <$amount_of_LOS; $i++) {

		
			if ($res1['PRIORITY'][$i]=="Canceled"){
				$status="LOS canceled";
				$PRIORITY_status="selected_LOS";
			}else{
				if ($res1['DONE'][$i]=="NOT OK" || $res1['DONE'][$i]=="REJECTED"){
					$status="Partner in process";
					$DONE_status="selected_LOS";
				}else if ($res1['DONE'][$i]=="OK" && ($res1['REPORT'][$i]=="NOT OK" || $res1['REPORT'][$i]=="REJECTED")){
					$status="Partner to create report";
					$REPORT_status="selected_LOS";
				}else if (($res1['RESULT'][$i]=="NOT OK" || $res1['RESULT'][$i]=="REJECTED") && $res1['REPORT'][$i]=="OK" && $res1['DONE'][$i]=="OK"){
					$status="TXMN to evaluate report";
					$RESULT_status="selected_LOS";
				}else{
					$status="LOS confirmed";
					$DONE_status="";
					$REPORT_status="";
					$RESULT_status="";
				}
			}

			$user_CREATION=getuserdata($res1['CREATION_BY'][$i]);


			if ($res1['TYPE'][$i]=="ST"){
				$CON='TECHM TXMN';
			}else{
				$CON=$res1['CON'][$i];
			}
			$out.=  '"'.$res1['ID'][$i].'","'.$res1['SITEA'][$i].'","'.$res1['SITEB'][$i].'","'.$res1['PRIORITY'][$i].'","'.$status.'","'.$user_CREATION['firstname']." ".$user_CREATION['lastname'].'","'.$res1['PARTNERVIEW'][$i].'","'.$res1['DONE'][$i].'","'.$res1['REPORT'][$i].'","'.$res1['RESULT'][$i].'","'.$res1['TYPE'][$i].'"\r\n';	

	}//End FOR

}else{
	
	$out.=  "NO links found!";
}
echo $out;
?>
<br><br>
<form class="form-horizontal" role="form" action="scripts/los/los_csv2.php" method="post">
	<input type="hidden" name="data" value='<?=$out?>'>
	<div class="form-group">
      <button type="submit" class="btn btn-default">Save as csv</button>
  </div>
</form>
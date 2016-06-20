<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
if (!$_GET['module']){
  require($config['phpguarddog_path']."/guard.php");
  protect("","Base_RF,Base_TXMN,Base_delivery,Base_other,Base_risk,Partner,Administrators","");
}
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

if ($guard_username=="bruno" or $guard_username=="nasifkha"){
  $cachefile = '../cache/'.basename(__FILE__, '_IND.php'); 
}else{
  $cachefile = '../cache/'.basename(__FILE__, '.php'); 
}

$cachetime = 120 * 60; // 2 hours
// Serve from the cache if it is younger than $cachetime
if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {
include($cachefile);
echo "<!-- Cached ".date('jS F Y H:i', filemtime($cachefile))." -->";
$caching='no';
exit;
}

$caching='yes';

ob_start();

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['partner']=='' or $_POST['partner']=='all') {
	$partner="ALL";
}else if ($_POST['partner']=='benchmark') {
	$partner="BENCHMARK";
}else if ($_POST['partner']=='techm') {
	$partner="TECHM";
}else if ($_POST['partner']=='m4c') {
  $partner="M4C";
}else{
  die;
}
?>


<form action="scripts/raf/rafActionsTable.php" method="post" id="RafActionsForm" role="form" class="form-inline">
 <div class="form-group">
  <select class="form-control" id="partner" name="partner">
  <?php 
  if (substr_count($guard_groups, 'Administrators')==1 or substr_count($guard_groups, 'Base')==1){ ?>
  <option value="ALL"> ALL</option>
  <?php }
  if (substr_count($guard_groups, 'Administrators')==1 or substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'Benchmark')==1){ ?>
  <option value="benchmark">BENCHMARK</option>
  <?php }
  if (substr_count($guard_groups, 'Administrators')==1 or substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'TechM')==1){ ?>
  <option value="techm">TECHM</option> 
  <?php } 
  if (substr_count($guard_groups, 'Administrators')==1 or substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'ZTE')==1){ ?>
  <option value="m4c">M4C/ZTE</option> 
  <?php } ?>
  </select>
 </div>
 <div class="form-group">
  <select class="form-control" id="raftype" name="raftype" multiple>
  <?php
    $query="SELECT RAFTYPE FROM RAF_PROCESS_STEPS WHERE RAFTYPE NOT LIKE '%v2%' order by RAFTYPE";
    //echo $query;
    $stmtPR= parse_exec_fetch($conn_Infobase, $query, $error_str, $resPR);
    if (!$stmtPR){
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmtPR);
        $amount_of_TYPES=count($resPR['RAFTYPE']);
    }
    for ($k = 0; $k <$amount_of_TYPES; $k++){ 
      if (($guard_username=="bruno" or $guard_username=="nasifkha") && ($resPR['RAFTYPE'][$k]=='IND Upgrade' or $resPR['RAFTYPE'][$k]=='New Indoor' or $resPR['RAFTYPE'][$k]=='New Indoor Site')){
        $selected="selected";
      }else if ($resPR['RAFTYPE'][$k]!='IND Upgrade' && $resPR['RAFTYPE'][$k]!='New Indoor' && $resPR['RAFTYPE'][$k]!='New Indoor Site' &&  substr_count($guard_groups, 'Base_RF')==1){
        $selected="selected";
      }else{
        $selected="";
      }
      echo "<option ".$selected." value='".$resPR['RAFTYPE'][$k]."''>".$resPR['RAFTYPE'][$k]."</option>";   
    }
    ?>
  </select>
 </div>

 <button type="button" class="btn btn-default btn-xs rightArrowRAF" data-scrollid="rafActionsTableoutput">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</button>
&nbsp;&nbsp;
<button type="button" class="btn btn-default btn-xs leftArrowRAF" data-scrollid="rafActionsTableoutput" style="margin:0 5px 0 5px;">
  <span class="glyphicon glyphicon-backward" aria-hidden="true" ></span>
</button>

 <div class="form-group">
  <select class="form-control" id="cluster" name="cluster">
  <option value=''>Cluster</option>
  <?php
  $query="SELECT DISTINCT(CLUSTERN || CLUSTERNUM) AS CLUST FROM BSDS_RAF_RADIO WHERE CLUSTERN IS NOT NULL ORDER BY CLUSTERN || CLUSTERNUM";
  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
  if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
  } else {
      OCIFreeStatement($stmt);
      $amount_CLUSTER=count($res1['CLUST']);
  }

  for ($i = 0; $i <$amount_CLUSTER; $i++){ 
    echo "<option>".$res1['CLUST'][$i]."</option>";
  }
  ?>
  </select>
 </div>
 <button type="submit" id="rafActionsSubmit" class="btn btn-default">FILTER</button>
</form>

<table class="pull-right">
  <tr>
    <td><div class='Hradio'>BASE RADIO</div></td>
    <td><div class='Htxmn'>BASE TXMN</div></td>
    <td><div class='Hpartner'>PARTNER</div></td>
    <td><div class='Hdelivery'>BASE DELIVERY</div></td>
    <td><div class='Hdelivery2'>BASE ROL</div></td>
  </tr>
</table>
<br><br>
<?php 
include('rafActionsTable_data.php');

$fp = fopen($cachefile, 'w'); // open the cache file for writing
fwrite($fp, ob_get_contents()); // save the contents of output buffer to the file
fclose($fp); // close the file
ob_end_flush(); // Send the output to the browser
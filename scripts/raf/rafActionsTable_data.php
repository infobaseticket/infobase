<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
if (!$_GET['module']){
  require($config['phpguarddog_path']."/guard.php");
  protect("","Base_RF,Base_TXMN,Base_delivery,Base_other,Base_risk,Partner,Administrators","");
}
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

function getHighlightColor($col,$action_by){

  if ($col=="REGION"){
    $highlight='region';
  }else if ($action_by=='Base_RF'){
    $highlight='radio';
  }else if ($action_by=='Base_delivery'){
    $highlight='delivery';
  }else if ($action_by=='Base_TXMN'){
     $highlight='txmn';
  }else if ($action_by=='Partner'){
     $highlight='partner';
  }else{
    $highlight='';
  }
  return $highlight;
}

if ($_POST['partner']=='' or $_POST['partner']=='all') {
	$partner="ALL";
}else if ($_POST['partner']=='benchmark') {
	$partner="BENCHMARK";
}else if ($_POST['partner']=='techm') {
	$partner="TECHM";
}else if ($_POST['partner']=='m4c') {
  $partner="M4C";
}

if (substr_count($guard_groups, 'Benchmark')==1){
  $partner="BENCHMARK";
}elseif (substr_count($guard_groups, 'TechM')==1){
  $partner="TECHM";
}elseif (substr_count($guard_groups, 'm4c')==1){
  $partner="M4C";
}


if ($_POST['raftype']!=''){
  foreach ($_POST['raftype'] as $key => $raftype) {
    $filter.=" RAFTYPE LIKE '%".$raftype."%' OR";
  }
   $query_filter=" AND (".substr($filter, 0,-2).")";
}elseif ($guard_username=="bruno" or $guard_username=="nasifkha" && $_POST['raftype']==''){
   $query_filter=" AND (RAFTYPE LIKE '%IND Upgrade%' OR RAFTYPE LIKE '%New Indoor%')";
}elseif ($_POST['raftype']=='' &&  substr_count($guard_groups, 'Base_RF')==1){
  $query_filter=" AND (RAFTYPE NOT LIKE '%IND Upgrade%' AND RAFTYPE NOT LIKE '%New Indoor%')";
}


if ($_POST['cluster']!=''){
  $cluster=$_POST['cluster'];
  $query_filter=" AND CLUST LIKE '%".$cluster."%'";
}

//Here we get the detailed sitelist per action
$query="SELECT * FROM VW_RAF_ACTIONS_BY2 WHERE RAFTYPE IS NOT NULL";
  if ($partner=='BENCHMARK') {
    $query.=" AND SAC='BENCHMARK' or CON='BENCHMARK'";
  }else if ($partner=='M4C') {
    $query.=" AND SAC='M4C' or CON='M4C'";
  }else  if ($partner=='TECHM') {
    $query.=" AND SAC='TECHM' OR CON='TECHM' OR SAC='ALU' OR CON='ALU'";
  }

  if ($query_filter!=''){
     $query.=$query_filter;
  }
  $query.=" ORDER BY SITEID";
 //echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
    $amount=count($res1['ACTION']);
}
$sum=0;
for ($i = 0; $i <$amount; $i++){ 
  $data2[$res1['ACTION'][$i]][substr($res1['SITEID'][$i],0,2)][]="(".$res1['RAFID'][$i]."): ".$res1['SITEID'][$i];
}
//echo "<pre>".print_r($data2,true)."</pre>";

//Here we get the totals per region and per action
$query="SELECT
  SUBSTR (SITEID, 0, 2) AS REGION,
  ACTION,
  COUNT (SITEID) AS AMOUNT
FROM
  VW_RAF_ACTIONS_BY2 WHERE RAFTYPE IS NOT NULL";
  if ($partner=='BENCHMARK') {
    $query.=" AND SAC='BENCHMARK' or CON='BENCHMARK'";
  }else if ($partner=='M4C') {
    $query.=" AND SAC='M4C' or CON='M4C'";
  }else  if ($partner=='TECHM') {
    $query.=" AND SAC='TECHM' OR CON='TECHM' OR SAC='ALU' OR CON='ALU'";
  }

  if ($query_filter!=''){
     $query.=$query_filter;
  }
  $query.=" 
GROUP BY
  SUBSTR (SITEID, 0, 2) , ACTION
ORDER BY
  SUBSTR(SITEID, 0, 2)";
 //echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
    $amount=count($res1['ACTION']);
}

$sum=0;
for ($i = 0; $i <$amount; $i++){ 
  $data[$res1['REGION'][$i]][$res1['ACTION'][$i]]=$res1['AMOUNT'][$i];
}
//echo "<pre>".print_r($data,true)."</pre>";

//Here we get the different actions for the headers
$query="SELECT DISTINCT(ACTION),MAX(UNID),ACTION_BY from VW_RAF_ACTIONS_BY2 WHERE RAFTYPE IS NOT NULL";
 if ($partner=='BENCHMARK') {
    $query.=" AND SAC='BENCHMARK' or CON='BENCHMARK'";
  }else if ($partner=='M4C') {
    $query.=" AND SAC='M4C' or CON='M4C'";
  }else  if ($partner=='TECHM') {
    $query.=" AND SAC='TECHM' OR CON='TECHM' OR SAC='ALU' OR CON='ALU'";
  }

   if ($query_filter!=''){
     $query.=$query_filter;
  }
  $query.="  GROUP BY ACTION,ACTION_BY ORDER BY MAX(UNID)";
 // echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
    $TotalAmountTasks=count($res1['ACTION']);
}


//Here we get the regions
$query2="SELECT DISTINCT(SUBSTR (SITEID, 0, 2)) AS REGION from VW_RAF_ACTIONS_BY2 WHERE RAFTYPE IS NOT NULL";
 if ($partner=='BENCHMARK') {
    $query2.=" AND SAC='BENCHMARK' or CON='BENCHMARK'";
  }else if ($partner=='M4C') {
    $query2.=" AND SAC='M4C' or CON='M4C'";
  }else  if ($partner=='TECHM') {
    $query2.=" AND SAC='TECHM' OR CON='TECHM' OR SAC='ALU' OR CON='ALU'";
  }

  if ($query_filter!=''){
     $query2.=$query_filter;
  }
  $query2.=" ORDER BY SUBSTR (SITEID, 0, 2)";
  //echo $query2;
$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
if (!$stmt2) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt2);
    $TotalAmountRegions=count($res2['REGION']);
}

?>

<div id="rafActionsTableoutput" style="overflow-x:auto;">
<?php
if ($caching=='yes'){
  echo "<div class='pull-right'><i>Note: File cached @ ".date("H:i:s")." (refresh every 2 hours)</i></div>";
}
?><br>
  <table class='table table-hover table-condensed table-header-rotated'>
   <thead>
    <tr>
    <th></th>
     <?php
      for ($i = 0; $i <$TotalAmountTasks; $i++){ 
        $highlight='';
        $highlight=getHighlightColor($res1['ACTION'][$i],$res1['ACTION_BY'][$i]);
        echo "<th class='rotate-45' class='".$highlight."'><div class='H".$highlight."'><span>".$res1['ACTION'][$i]."</span></div></th>";
      }
     ?>
    </tr>
   </thead>
   <tbody>
    
     <?php
      for ($i = 0; $i <$TotalAmountRegions; $i++){ //foreach region found
        
        echo "<tr><td><b>".$res2['REGION'][$i]."</b></td>";
         // echo $res2['REGION'][$i].": $TotalAmountTasks<br>";
        for ($j = 0; $j <$TotalAmountTasks; $j++){ //foreach tasks found

          if (is_array($data2[$res1['ACTION'][$j]][$res2['REGION'][$i]])){
            $out='';
            $z=1;

            //Here we output all the siteID's per action
            foreach ($data2[$res1['ACTION'][$j]][$res2['REGION'][$i]] as $key => $value) { 
              $out.=$value." - ";
              if ($z==3){
                $out.="<br>";
                $z=0;
              }
              $z++;              
            }
          }

          //$report= getReport($res1['ACTION'][$j]);
          /* if ($res1['ACTION'][$j]!=''){
            $link=$config['sitepath_url'].'/bsds/index.php?module=rafreport&report='.$res1['ACTION'][$j].'&region='.$res2['REGION'][$i].'&partner='.$partner;
            $link2=$config['sitepath_url'].'/bsds/index.php?module=rafreport&report='.$res1['ACTION'][$j].'&region=NA&partner='.$partner;
          }else{
            $link='#';
            $link2='#';
          }*/
          $amountTaks=intval($data[$res2['REGION'][$i]][$res1['ACTION'][$j]]);
          //$alltasks='alltasks_'.$res1['ACTION'][$j];
          $alltasks[$res1['ACTION'][$j]]=$amountTaks+$alltasks[$res1['ACTION'][$j]];
          
          //$alltasks_link[$res1['ACTION'][$j]]=$link2;
          echo "<td><a href='#' class='badge rafreportLink' data-action='".$res1['ACTION'][$j]."' data-cluster='".$cluster."' data-region='".$res2['REGION'][$i]."' data-partner='".$partner."' data-raftype='".$raftypes."' data-html='true' rel='tooltip' data-placement='right' title='".$out."'>".$data[$res2['REGION'][$i]][$res1['ACTION'][$j]]."</a></td>";
         //echo "<td><a href='".$link."' class='badge rafreportLink' data-action='".$res1['ACTION'][$j]."' data-region='".$res1['REGION'][$j]."' data-partner='".$partner."' data-raftype='".$_POST['raftype']."' data-html='true' rel='tooltip' data-placement='right' title='".$out."'>".$data[$res2['REGION'][$i]][$res1['ACTION'][$j]]."</a></td>";
        }
        echo "</tr>";
        $totals[]=$$alltasks;

      }
      echo "<tr><td>SUM</td>";
      if (count($alltasks)!=0){
        foreach ($alltasks as $key => $value) {
          //echo "<td><a href='".$alltasks_link[$key]."' class='badge'>".$value."</a></td>";
          echo "<td><a href='#' class='badge rafreportLink' data-action='".$alltasks['ACTION'][$key]."' data-region='NA' data-partner='".$partner."' data-raftype='".$raftypes."'>".$value."</a></td>";
        }
      }else{
        echo "<tr><td>NO RAF FOUND BASED ON YOUR FILTER</td></tr>";
      }
      echo "</tr>";
     ?>
    
   </tbody>
  </table>
</div>
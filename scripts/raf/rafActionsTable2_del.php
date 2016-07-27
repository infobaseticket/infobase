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


function getHighlightColor($col){
  if ($col=="RADIO_INP" or $col=="BCS_RF_INP" or $col=="BCS_A15" or $col=="RADIO_FUND" or $col=="RADIO_BLOCKED_FUND" or $col=="RADIO_RFPAC"){
    $highlight='radio';
  }else if ($col=="TXMN_INP" or $col=="BCS_TX_INP" or $col=="TXMN_ACQUIRED"){
    $highlight='txmn';
  }else if ($col=="COF_ACQ_PARTNER" or $col=="PARTNER_INP" or $col=="NET1_LBP" or $col=="NET1_ACQUIRED" or $col=="PARTNER_ACQUIRED" or $col=="COF_CON_PARTNER" or $col=="PARTNER_RFPACK" or $col=="PARTNER_A304"){
    $highlight='partner';
  }else if ($col=="NET1_LINK" or $col=="NET1_FUND" or $col=="PO_CON" or $col=="PO_ACQ"){
    $highlight='delivery';
   }else if ($col=="COF_ACQ_BASE" or $col=="ACQ_PARTNER" or $col=="CON_PARTNER" or $col=="COF_CON_BASE_TS" or $col=="COF_CON_BASE_PM" or $col=="NET1_FAC" or $col=="NET1_PAC"){
    $highlight='delivery2';
  }else if ($col=="REGION"){
    $highlight='region';
  }else{
    $highlight='';
  }
   
  return $highlight;
}

function getReport($col){
  if ($col=="TXMN_INP"){
  $report='TXINP';
  }elseif ($col=="BCS_TX_INP"){
  $report='BCSTX';
  }elseif ($col=="RADIO_INP"){
  $report='RFINP';
  }else if ($col=="ACQ_PARTNER"){
  $report='DELACQ';
  }else if ($col=="NET1_LINK"){
  $report='DELNET1';
  }else if ($col=="COF_ACQ_PARTNER"){
  $report='PARTNERCOFACQ';
  }else if ($col=="COF_ACQ_BASE"){
  $report='DELCOFACQ';
  }else if ($col=="PO_ACQ"){
  $report='DELPOA';
  }else if ($col=="PARTNER_INP"){
  $report='PARTNERINP';
}else if ($col=="PARTNER_A304"){
  $report='PARTNERA304';
  }else if ($col=="BCS_RF_INP"){
  $report='RFBCS';

  }else if ($col=="NET1_LBP"){
  $report='PARTNERLBP';
  }else if ($col=="TXMN_ACQUIRED"){
  $report='TXACQ';
  }else if ($col=="PARTNER_ACQUIRED"){
  $report='PARTNERACQ';
  }else if ($col=="CON_PARTNER"){
  $report='DELCON';
  }else if ($col=="RADIO_FUND"){
  $report='RFFUND';
  }else if ($col=="RADIO_BLOCKED_FUND"){
  $report='RFFUNDBLOCKED';
  }else if ($col=="NET1_FUND"){
  $report='DELFUND';
  }else if ($col=="COF_CON_PARTNER"){
  $report='PARTNERCOFCON';
  }else if ($col=="COF_CON_BASE_TS"){
  $report='DELCOFCONTS';
   }else if ($col=="COF_CON_BASE_PM"){
  $report='DELCOFCONPM';
  }else if ($col=="PO_CON"){
  $report='DELPOC';
  }else if ($col=="PARTNER_RFPACK"){
  $report='PARTNERRFPACK';
  }else if ($col=="BCS_A15"){
  $report='RFBCSA15';

  }else if ($col=="RADIO_RFPAC"){
  $report='RFPAC';
  }else if ($col=="RADIO_RFPAC"){
  $report='RFPAC';
  }else{
  $report='';
  }
  return $report;
}

if ($_POST['partner']=='' or $_POST['partner']=='all') {
	$partner="ALL";
}else if ($_POST['partner']=='benchmark') {
	$partner="BENCHMARK";
}else if ($_POST['partner']=='techm') {
	$partner="TECHM";
}

<table id="rafactionsTable" class='table table-hover table-condensed table-header-rotated'>
<thead>
<tr>

<?php
require('rafActionsTableQuery.php');

//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
    $amount=count($res1['REGION']);
}

$i=1;
foreach ($res1 as $key=>$attrib_id){
  $cols[$i]=$key;
  $highlight=getHighlightColor($key);
  echo "<th class='rotate-45' class='".$highlight."'><div class='H".$highlight."'><span>".$key."</span></div></th>";
  $i++;
}
?>
</tr>
</thead>
<tbody>
<?php
//echo var_dump($cols);

//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
    $amount=count($res1['REGION']);
}

foreach ($res1['REGION'] as $key=>$attrib_id){
  echo "<tr>";
  foreach ($cols as $key2=>$col){
    //echo $region."-".$col."<br>";
    $highlight='';
    if ($res1[$col][$key]==''){
      echo "<td>--</td>";
    }else{
      $region=$res1['REGION'][$key];
      if ($region=="TOTALS"){
        $region='';
      }
      $report= getReport($col);

      $highlight=getHighlightColor($col);

      if ($report!=''){
        $link=$config['sitepath_url'].'/bsds/index.php?module=rafreport&report='.$report.'&region='.$region.'&partner='.$partner;
        echo "<td class='".$highlight."'><a href='".$link."'>".$res1[$col][$key]."</a></td>";
      }else{
        echo "<td class='".$highlight."'>".$res1[$col][$key]."</td>";
      }
      
    }
  }
  echo "</tr>";
}

?> 
</tbody>     
</table>
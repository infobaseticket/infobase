<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

function format_bytes($bytes) {
   if ($bytes < 1024) return $bytes.' B';
   elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KB';
   elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MB';
   elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GB';
   else return round($bytes / 1099511627776, 2).' TB';
}
?>


<table class="table table-bordered table-condensed table-hover" style="background-color: white;" id='filebrowserdata'>
  <thead>
    <th>Actions </th>
    <th>Filename <span class="glyphicon glyphicon-sort pointer"></th>
    <th>Ext. <span class="glyphicon glyphicon-sort pointer"></th>
    <th>Size <span class="glyphicon glyphicon-sort pointer"></th>
    <th>Location <span class="glyphicon glyphicon-sort pointer"></th>
    <th>Last modif. time <span class="glyphicon glyphicon-sort pointer"></th>
    <th>Last acces time <span class="glyphicon glyphicon-sort pointer"></th>
    <th>Date in filename <span class="glyphicon glyphicon-sort pointer"></th>
    <th>RAN scan date <span class="glyphicon glyphicon-sort pointer"></th>
    <th>Filetype <span class="glyphicon glyphicon-sort pointer"></th>
    <th>SiteID <span class="glyphicon glyphicon-sort pointer"></th>
    <th>Candidate <span class="glyphicon glyphicon-sort pointer"></th>
    <th>UPGNR <span class="glyphicon glyphicon-sort pointer"></th>
  </thead>
  <tbody>
<?php
if ($_POST['typeRANALU']=='ALU' or $_POST['typeRANOLD']=='OLD' or $_POST['typeRANBENCH']=='BENCH' or $_POST['typeRANLEASE']=='LEASE'){
  $query="SELECT * FROM  RAN_SCAN_TODAY  WHERE SUBPATHNAME IS NOT NULL";
  if (trim($_POST['searchfor'])!=''){
    $query.=" AND SUBPATHNAME LIKE '%".escape_sq($_POST['searchfor'])."%'";
  }
  $query.=" AND ( KEY LIKE '%XXXX%'";
  if ($_POST['typeRANALU']=='ALU'){
    $query.=" OR KEY LIKE '%ALURAN%'";
  }
  if ($_POST['typeRANOLD']=='OLD'){
    $query.=" OR KEY LIKE '%RAN_ARCHIVE%'";
  }
  if ($_POST['typeRANBASE']=='BASE'){
    $query.=" OR KEY LIKE '%BASE-RAN%'";
  }
  if ($_POST['typeRANM4C']=='M4C'){
    $query.=" OR KEY LIKE '%M4C_RAN%'";
  }
  if ($_POST['typeRANBENCH']=='BENCH'){
    $query.=" OR KEY LIKE '%BENCHMARK_RAN%' OR KEY LIKE '%RAN_BENCH%'";
  }
  if ($_POST['typeRANLEASE']=='LEASE'){
    $query.=" OR KEY LIKE '%RAN-BASELeaseBP%'";
  }
  $query.=" )";

  if ($_POST['extension']!='All'){
    if ($_POST['extension']!='img'){
      $query.=" AND EXTENSION LIKE '%".escape_sq($_POST['extension'])."%'";
    }elseif ($_POST['extension']=='img'){
      $query.=" AND (EXTENSION LIKE '%jp%' or EXTENSION LIKE '%gif%' or EXTENSION LIKE '%bm%' or EXTENSION LIKE '%tif')";
    }
  }
  if ($_POST['rangetype']=='File' && $_POST['start']!='' && $_POST['end']!=''){
    $query.=" AND FILENAMEDATE >= '".$_POST['start']."' AND  FILENAMEDATE <= '".$_POST['end']."' ";
  }
  if ($_POST['rangetype']=='Modif' && $_POST['start']!='' && $_POST['end']!=''){
    $query.=" AND MODIFTIME >= '".$_POST['start']."' AND  MODIFTIME <= '".$_POST['end']."' ";
  }
  if ($_POST['rangetype']=='Acces' && $_POST['start']!='' && $_POST['end']!=''){
    $query.=" AND LASTACCESTIME >= '".$_POST['start']."' AND  LASTACCESTIME <= '".$_POST['end']."' ";
  }
  if (trim($_POST['filesize'])!='' && $_POST['start']!='' && $_POST['end']!=''){
    $query.=" AND FILESIZE  > ".escape_sq($_POST['filesize'])*1048576;
  }
  if (isset($_POST['filetype'])){
     $queryt=" AND (";
    foreach ($_POST['filetype'] as $key => $filetype) {
      $queryt.=" FILETYPE = '".$filetype."' OR";
      $filetypes.=$filetype." ";
    }
     $query.=substr($queryt,0,-2).")";
  }
  $query.=" ORDER BY SUBPATHNAME ASC";
  //echo ")))".$query."<br>";

  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
  if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
  } else {
    OCIFreeStatement($stmt);
    $amount_of_FILES=count($res1['SUBPATHNAME']);
  }

  for ($i=0;$i<$amount_of_FILES;$i++){
    $fileloc=urlencode($res1['KEY'][$i]);
    $dirloc=substr($res1['SUBPATH'][$i],0,2).'/'.rawurlencode($res1['SUBPATH'][$i]);
    $ranurl=$config['sitepath_url'].'/bsds/scripts/liveranbrowser/liveranbrowser.php?dir='.$dirloc.'&ran='.$res1['PARTNER'][$i];

    echo '<tr><td><a class="btn btn-default btn-xs filedownload" target="_new" title="Download file" href="scripts/filebrowser/filedownload.php?file='.$fileloc.'&name='.$res1['FILENAME'][$i].'.'.$res1['EXTENSION'][$i].'"><span class="glyphicon glyphicon-download"></span></a>';
    echo '<a class="btn btn-default btn-xs liveran" title="Open containing folder" target="_blank" style="target-new: tab;" id="filefolder" data-ranurl="'.$ranurl.'"><span class="glyphicon glyphicon-circle-arrow-right"></span></a></td>';
    echo "<td>".$res1['FILENAME'][$i]."</td>";
    echo "<td>".$res1['EXTENSION'][$i]."</td>";
    echo "<td>".format_bytes($res1['FILESIZE'][$i])."</td>";
    if ($res1['PARTNER'][$i]=='RAN_ARCHIVE'){
    echo "<td><a class='tippy' data-toggle='tooltip' data-placement='top' href='#' title='".substr($res1['KEY'][$i],46)."'>RAN-ARCHIVE</a></td>";
    }else if ($res1['PARTNER'][$i]=='RAN-ALU'){
    echo "<td><a class='tippy' data-toggle='tooltip' data-placement='top' href='#' title='".substr($res1['KEY'][$i],29)."'>RAN-ALU</a></td>";
    }else if ($res1['PARTNER'][$i]=='BENCHMARK_RAN'){
    echo "<td><a class='tippy' data-toggle='tooltip' data-placement='top' href='#' title='".substr($res1['KEY'][$i],35)."'>RAN-BENCH</a></td>";
    }else if ($res1['PARTNER'][$i]=='RAN_LEASE'){
    echo "<td><a class='tippy' data-toggle='tooltip' data-placement='top' href='#' title='".substr($res1['KEY'][$i],47)."'>RAN-LEASE</a></td>";
    }else{
    echo "<td><a class='tippy' data-toggle='tooltip' data-placement='top' href='#' title='".$res1['KEY'][$i]."'>NA</a></td>";
    }

    echo "<td>".$res1['MODIFTIME'][$i]."</td>";
    echo "<td>".$res1['LASTACCESTIME'][$i]."</td>";
    echo "<td>".substr($res1['FILENAMEDATE'][$i],0,-8)."</td>";
    echo "<td>".substr($res1['INSERTDATE'][$i],0,-8)."</td>";
    echo "<td>".$res1['FILETYPE'][$i]."</td>";
    echo "<td>".$res1['SITEID'][$i]."</td>";
    echo "<td>".$res1['CANDIDATE'][$i]."</td>";
    echo "<td>".$res1['UPGNR'][$i]."</td>";

    echo "</tr>";
  }
}
if ($_POST['typeRANHIST']=='HIST'){

  $query=" select * from RAN_SCAN_LOG WHERE SUBPATHNAME IS NOT NULL";
  if (trim($_POST['searchfor'])!=''){
    $query.=" AND SUBPATHNAME LIKE '%".escape_sq($_POST['searchfor'])."%'";
  }
  if ($_POST['extension']!='All'){
    if ($_POST['extension']!='img'){
      $query.=" AND EXTENSION LIKE '%".escape_sq($_POST['extension'])."%'";
    }elseif ($_POST['extension']=='img'){
      $query.=" AND (EXTENSION LIKE '%jp%' or EXTENSION LIKE '%gif%' or EXTENSION LIKE '%bm%' or EXTENSION LIKE '%tif')";
    }
  }
  if ($_POST['rangetype']=='File' && $_POST['start']!='' && $_POST['end']!=''){
    $query.=" AND FILENAMEDATE >= '".$_POST['start']."' AND  FILENAMEDATE <= '".$_POST['end']."' ";
  }
  if ($_POST['rangetype']=='Modif' && $_POST['start']!='' && $_POST['end']!=''){
    $query.=" AND MODIFTIME >= '".$_POST['start']."' AND  MODIFTIME <= '".$_POST['end']."' ";
  }
  if ($_POST['rangetype']=='Acces' && $_POST['start']!='' && $_POST['end']!=''){
    $query.=" AND LASTACCESTIME >= '".$_POST['start']."' AND  LASTACCESTIME <= '".$_POST['end']."' ";
  }
  if ($_POST['rangetype']=='Hist' && $_POST['start']!='' && $_POST['end']!=''){
    $query.=" AND ANALYSEDATE >= '".$_POST['start']."' AND  ANALYSEDATE <= '".$_POST['end']."' ";
  }
  if (trim($_POST['filesize'])!=''){
    $query.=" AND FILESIZE  > ".escape_sq($_POST['filesize'])*1048576;
  }
  if (isset($_POST['filetype'])){
     $queryt=" AND (";
    foreach ($_POST['filetype'] as $key => $filetype) {
      $queryt.=" FILETYPE = '".$filetype."' OR";
      $filetypes.=$filetype." ";
    }
    $query.=substr($queryt,0,-2).")";
  }
  $query.=" ORDER BY ANALYSEDATE DESC";

  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
  if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
  } else {
    OCIFreeStatement($stmt);
    $amount_of_FILES=count($res1['SUBPATHNAME']);
  }
  
  for ($i=0;$i<$amount_of_FILES;$i++){
    echo "<tr class='warning'><td>".$res1['ACTION'][$i]."</td><td>".$res1['FILENAME'][$i]."</td>";
    echo "<td>".$res1['EXTENSION'][$i]."</td>";
    echo "<td>".format_bytes($res1['FILESIZE'][$i])."</td>";
    if (substr_count($res1['KEY'][$i], 'RAN_ARCHIVE')==1){
    echo "<td><a class='tippy' data-toggle='tooltip' data-placement='left' href='#' title='".substr($res1['KEY'][$i],29)."'>RAN-ARCHIVE</a></td>";
    }else{
    echo "<td><a class='tippy' data-toggle='tooltip' data-placement='left' href='#' title='".substr($res1['KEY'][$i],29)."'>RAN-ALU</a></td>";
    }
    echo "<td>".$res1['MODIFTIME'][$i]."</td>";
    echo "<td>".$res1['LASTACCESTIME'][$i]."</td>";
    echo "<td>".substr($res1['FILENAMEDATE'][$i],0,-8)."</td>";
    echo "<td>".substr($res1['ANALYSEDATE'][$i],0,-8)."</td>";
    echo "<td>".$res1['FILETYPE'][$i]."</td>";
    echo "<td>".$res1['SITEID'][$i]."</td>";
    echo "<td>".$res1['CANDIDATE'][$i]."</td>";
    echo "<td>".$res1['UPGNR'][$i]."</td>";
    echo "</tr>";
  }
}
?>
  </tbody>
</table>
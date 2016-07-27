<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
if (!$_GET['module']){
  require($config['phpguarddog_path']."/guard.php");
  protect("","Base_RF,Base_TXMN,Base_delivery,Base_other,Base_risk,Partner,Administrators","");
}
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
include('../raf/raf_procedures.php');

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$BSDSrefresh=get_BSDSrefresh();


if ($_GET['rafid']){
  $searchk=$_GET['rafid'];
}else if ($_GET['losid']){
  $searchk=$_GET['losid'];
}else if ($_GET['site']){
  $searchk=$_GET['site'];
}else if ($_GET['bsdsid']){
  $searchk=$_GET['bsdsid'];
}else if ($_SESSION['searchk']){
  $searchk=$_SESSION['searchk'];
}else{
  $searchk="";
}

if (strlen($searchk)==8 && $_GET['module']){
  $searchk=substr($searchk, 1,-1);
}

$query="select COUNT(FILENAME) AS AMOUNTFILES, PARTNER from RAN_SCAN_TODAY GROUP BY PARTNER";
$stmtR = parse_exec_fetch($conn_Infobase, $query, $error_str, $resR);
if (!$stmtR) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtR);
}
?>

    
<div class="row row-offcanvas">
    <div class="col-md-10">
      <div class="scroller scroller-left pull-left" id="leftsubTabs"><i class="glyphicon glyphicon-chevron-left"></i></div>
      <div class="scroller scroller-right pull-right" id="rightsubTabs"><i class="glyphicon glyphicon-chevron-right"></i></div>
      <div class="wrapper" id="subTabs">
        <ul class="nav nav-tabs list pull-left" id="siteTabs" role="tablist"></ul>
      </div>
      

      <div class="tab-content" id="contentTabs">

       <?php if(!$_GET['module']){ ?>
       
          <div class="pull-left" style="margin: 0 10px;">
            <div class="panel panel-default">
              <div class="panel-body" id="RAFTYPESdashbord">
                <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading 'RAFs ONGING per type'...
              </div>
            </div>
          </div>
          <div class="pull-left" style="margin: 0 10px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Import status</h3>
              </div>
              <div class="panel-body">
                <table class="table table-condensed" style="font-size:11px;">
                <tr>
                  <th>Task</th>
                  <th>Run</th>
                </tr>
                <tr>
                  <td>BSDS UPGRADES:</td><td>
                  <?php if($BSDSrefresh['ACTION_BSDS_UPG']=="Downloading"){ ?>
                    <span class="label label-danger">Downloading data</span>
                  <?php }else if($BSDSrefresh['ACTION_BSDS_UPG']=="Importing"){ ?>
                    <span class="label label-danger">Updating live data</span>
                  <?php }else{ ?>
                    <span class="label label-success" rel="tooltip" data-placement="bottom" title="Runs from 6:10 till 01:00 every 30 min. NEXT RUN: <?=$BSDSrefresh['NEXTRUN_UPG']?>"><?=$BSDSrefresh['DATE_UPG']?></span>
                  <?php } ?>
                  </td>
                  </tr>

                <tr>
                  <td>BSDS CAB UPGRADES:</td><td>
                  <?php if($BSDSrefresh['ACTION_BSDS_CAB']=="Downloading"){ ?>
                    <span class="label label-danger">Downloading data</span>
                  <?php }else if($BSDSrefresh['ACTION_BSDS_CAB']=="Importing"){ ?>
                    <span class="label label-danger">Updating live data</span>
                  <?php }else{ ?>
                    <span class="label label-success" rel="tooltip" data-placement="bottom" title="Runs from 6:15 till 01:00 every 30 min. NEXT RUN: <?=$BSDSrefresh['NEXTRUN_CABUPG']?>"><?=$BSDSrefresh['DATE_CABUPG']?></span>
                  <?php } ?>
                  </td>
                </tr>

                <tr>
                  <td>BSDS NEW SITES:</td><td>
                  <?php if($BSDSrefresh['ACTION_BSDS_NEW']=="Downloading"){ ?>
                    <span class="label label-danger">Downloading data</span>
                  <?php }else if($BSDSrefresh['ACTION_BSDS_NEW']=="Importing"){ ?>
                    <span class="label label-danger">Updating live data</span>
                  <?php }else{ ?>
                    <span class="label label-success" rel="tooltip" data-placement="bottom" title="Runs from 6:20 till 01:00 every 30 min. NEXT RUN: <?=$BSDSrefresh['NEXTRUN_NEW']?>"><?=$BSDSrefresh['DATE_NEW']?></span>
                  <?php } ?>
                  </td>
                </tr>

                <tr>
                  <td>NET1 EXPLORER + RAF UPGRADES</td>
                  <td>
                  <?php if($BSDSrefresh['ACTION_ALL_UPG_NET1']=="Downloading"){ ?>
                    <span class="label label-danger">Downloading data</span>
                  <?php }else if($BSDSrefresh['ACTION_ALL_UPG_NET1']=="Importing"){ ?>
                    <span class="label label-danger">Updating live data</span>
                  <?php }else{ ?>
                    <span class="label label-success" rel="tooltip" data-placement="bottom" title="Runs from 6:00 till 20:35 every 30 min. NEXT RUN: <?=$BSDSrefresh['NEXTRUN_ALL_UPG']?>"><?=$BSDSrefresh['DATE_ALL_UPG']?></span>
                  <?php } ?>
                  </td>
                </tr>
                <tr>
                  <td>NET1 EXPLORER + RAF NEW SITES</td>
                  <td><span class="label label-success" rel="tooltip" data-placement="bottom" title="Runs from 6:05 till 20:35 every 30 min. NEXT RUN:<?=$BSDSrefresh['NEXTRUN_ALL_NEW']?>"><?=$BSDSrefresh['DATE_ALL_NEW']?></span>
                  </td>
                </tr>
                <tr>
                  <td>MASTER MATERIAL LIST</td>
                  <td>
                  <?php if($BSDSrefresh['ACTION_MASTER_MATERIAL']=="Importing"){ ?>
                    <span class="label label-danger">Updating data</span>
                  <?php }else{ ?>
                    <span class="label label-success" rel="tooltip" data-placement="bottom" title="Runs from 6:05 till 20:35 every 2 hours. NEXT RUN: <?=$BSDSrefresh['NEXTRUN_MASTER_MATERIAL']?>"><?=$BSDSrefresh['DATE_MASTER_MATERIAL']?></span>
                  <?php } ?>
                  </td>
                </tr>
                <tr>
                  <td>RAF REFRESH/ANALYSE</td>
                  <td>
                  <?php if($BSDSrefresh['RAF_PROCESS']=="STARTED"){ ?>
                    <span class="label label-warning">Looping through RAF's</span>
                  <?php }else{ ?>
                    <span class="label label-success" rel="tooltip" data-placement="bottom" title="Runs every 5min and 35min after the hour from 06:05 till 23:35. NEXT RUN:<?=$BSDSrefresh['NEXTRUN_RAF_PROCESS']?>"><?=$BSDSrefresh['DATE_RAF_PROCESS']?></span>
                  <?php } ?>
                  </td>
                </tr>
                <tr>
                  <td><b>RAN STATUS</b><br>
                  <table style="font-size:10px">
                  <thead><th>RAN</th><th># files</th></thead>
                  <tbody>
                  <?php
                    foreach ($resR['PARTNER'] as $key => $value) {
                      echo "<tr><td>".$resR['PARTNER'][$key]."</td><td>".$resR['AMOUNTFILES'][$key].'</td></tr>';
                    }
                    ?>
                    </tbody>
                    </table>
                  </td>
                  <td>
                  <?php if($BSDSrefresh['RAN_SCAN_BENCHMARK_SUBMIT']!=""){ ?>
                    <span class="label label-danger">ISSUE WITH RAN BMT FTP SYNC</span><br>
                  <?php }else{ ?>
                   <span class="label label-success">BENCHMARK FILE SYNC OK</span> <button type="button" id="RAN_BMTlog" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-eye-open"></span></button><br>
                  <?php } 
                  if (file_exists($config['ranfolderBENCHSUBMIT']."AN")) {
                   echo '<span class="label label-success">BENCHSUBMIT RAN</span><br>';
                  }else{
                    echo '<span class="label label-danger">BENCHSUBMIT RAN</span><br>';
                  }
                  if (file_exists($config['ranfolderBENCH']."AN")) {
                   echo '<span class="label label-success">BENCHMARK RAN</span><br>';
                  }else{
                    echo '<span class="label label-danger">BENCHMARK RAN</span><br>';
                  }
                  if (file_exists($config['ranfolderARCHIVE']."AN")) {
                    echo '<span class="label label-success">ARCHIVE RAN</span><br>';
                  }else{
                    echo '<span class="label label-danger">ARCHIVE RAN</span><br>';
                  }
                  if (file_exists($config['ranfolder']."AN")) {
                    echo '<span class="label label-success">ALU RAN</span><br>';
                  }else{
                    echo '<span class="label label-danger">ALU RAN</span><br>';
                  }
                  if (file_exists($config['ranfolderLEASE']."AN")) {
                    echo '<span class="label label-success">LEASE&BP RAN</span>';
                  }else{
                    echo '<span class="label label-danger">LEASE&BP RAN</span>';
                  }
                  ?>

                  </td>
                </tr>
                
                 <tr>
                    <td>RAF NET1 SYNC<br></td>
                    <td>
                    <?php
                    if ($rafSync==0){
                    echo '<span class="label label-success">SYNC OK</span>';
                    }else{
                      echo '<span class="label label-danger">ISSUE WITH SYNC</span>';
                    }
                  ?>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>    
       <?php } ?>
      </div>
    </div>
    <div class="col-md-2 sidebar-offcanvas">
    <br>
      <div class="panel panel-primary">
        <div class="panel-heading">
          <form action="search_data.php" method="post" id="searchForm" class="form-search">
            <input type="hidden" name="submitted" id="submitted" value="true">
            <?php if ($_GET['module']){ ?>
            <input type="hidden" name="bypass" value="yes">
            <?php } ?>
            <div class="input-group">
            <input type="text" name="searchk" class="form-control input-md search-query" id="searchk" placeholder="BSDS|SITE|RAF|LOS" value="<?=$searchk?>">
              <span class="input-group-btn">
                <button class="btn btn-default" id="searchbutton" type="submit"><span class="glyphicon glyphicon-search"></span></button>
              </span>
            </div>
          </form>
        </div>
        <div class="panel-body">
          <div id="search_output"><i>You can search by SITE ID, RAF ID, LOS ID, BSDS ID</div>
        </div>
        <div class="panel-footer" id="sitesearch"><p class="text-center" style="font-size:8px;">&copy Basecompany</p>
        </div>
      </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready( function(){  
  $.ajax({
          type: "POST",
          url: "scripts/explorer/RAFtypesDash.php",
          success : function(data){
              $('#RAFTYPESdashbord').html(data); 
          }
  });
});



$('.scroller-right').click(function() {
  
  $('.scroller-left').fadeIn('slow');
  $('.scroller-right').fadeOut('slow');
  
  $('.list').animate({left:"+="+widthOfHidden()+"px"},'slow',function(){

  });
});

$('.scroller-left').click(function() {
  
  $('.scroller-right').fadeIn('slow');
  $('.scroller-left').fadeOut('slow');
  
    $('.list').animate({left:"-="+getLeftPosi()+"px"},'slow',function(){
    
    });
});

  $(window).on('resize',function(e){  
      reAdjust('subTabs');
  });
</script>
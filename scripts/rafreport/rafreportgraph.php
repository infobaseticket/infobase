<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Base_RF,Base_delivery,Partner,Base_TXMN,Base_other,Administrators","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
include('../raf/raf_procedures.php');

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$regions=array(
  1=>"AN",
  2=>"BW",
  3=>"BX",
  4=>"HT",
  5=>"LG",
  6=>"LI",
  7=>"LX",
  8=>"NR",
  9=>"OV",
  10=>"VB",
  11=>"WV",
  12=>"MT",
  13 =>"CT"
);

if (substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'Admin')==1){

    $query=create_query("","NA","NA","Base RF - INPUT","SITEID","ASC",0,"","NA","NA",$allocated,"no");
    //echo "<br><br>".$query;
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmt);
        $amount=count($res1['RAFID']);
        $points4.='{  y: '.$amount.', label: "RADIO INPUT '.$amount.'", legendText: "RADIO INPUT" },';
    }
    $query=create_query("","NA","NA","Base RF - BCS NET1","SITEID","ASC",0,"","NA","NA",$allocated,"no");
    //echo "<br><br>".$query;
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmt);
        $amount=count($res1['RAFID']);
        $points4.='{  y: '.$amount.', label: "BCS '.$amount.'", legendText: "BEST CANDIDATE SELECTION" },';
    }
    $query=create_query("","NA","NA","Base RF - FUNDING","SITEID","ASC",0,"","NA","NA",$allocated,"no");
    //echo "<br><br>".$query;
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmt);
        $amount=count($res1['RAFID']);
        $points4.='{  y: '.$amount.', label: "FUNDING '.$amount.'", legendText: "FUNDING" },';
    }

  foreach ($regions as $key => $value){
    $query=create_query("",$value,"NA","Base RF - INPUT","SITEID","ASC",0,"","NA","NA",$allocated,"no");
    //echo "<br><br>".$query;
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmt);
        $amount=count($res1['RAFID']);
        $points1.='{  y: '.$amount.', label: "'.$value.' '.$amount.'", legendText: "'.$value.'" },';
    }
  }
  foreach ($regions as $key => $value){
    $query=create_query("",$value,"NA","Base RF - BCS NET1","SITEID","ASC",0,"","NA","NA",$allocated,"no");
    //echo "<br><br>".$query;
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmt);
        $amount=count($res1['RAFID']);
        $points3.='{  y: '.$amount.', label: "'.$value.' '.$amount.'", legendText: "'.$value.'" },';
    }
  }

  foreach ($regions as $key => $value){
    $query=create_query("",$value,"NA","Base RF - FUNDING","SITEID","ASC",0,"","NA","NA",$allocated,"no");
    //echo "<br><br>".$query;
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmt);
        $amount=count($res1['RAFID']);
        $points5.='{  y: '.$amount.', label: "'.$value.' '.$amount.'", legendText: "'.$value.'" },';
    }
  }
}

if (substr_count($guard_groups, 'Partner')==1 or substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'Admin')==1){
  $query=create_query("","NA","NA","Partner - INPUT","SITEID","ASC",0,"","NA","NA",$allocated,"no");
  //echo "<br><br>".$query;
  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
  if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
  } else {
      OCIFreeStatement($stmt);
      $amount=count($res1['RAFID']);
      $points2.='{  y: '.$amount.', label: "PARTNER INPUT '.$amount.'", legendText: "PARTNER INPUT" },';
  }
  $region="NA";
  $query=create_query("","NA","NA","Partner - ACQUIRED","SITEID","ASC",0,"","NA","NA",$allocated,"no");
  //echo "<br><br>".$query;
  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
  if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
  } else {
      OCIFreeStatement($stmt);
      $amount=count($res1['RAFID']);
      $points2.='{  y: '.$amount.', label: "PARTNER ACQUIRED '.$amount.'", legendText: "PARTNER ACQUIRED" },';
  }
  $query=create_query("","NA","NA","Partner - SUBMIT RF PACK","SITEID","ASC",0,"","NA","NA",$allocated,"no");
  //echo "<br><br>".$query;
  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
  if (!$stmt) {
      die_silently($conn_Infobase, $error_str);
      exit;
  } else {
      OCIFreeStatement($stmt);
      $amount=count($res1['RAFID']);
      $points2.='{  y: '.$amount.', label: "PARTNER SUBMIT RF PACK '.$amount.'", legendText: "PARTNER SUBMIT RF PACK" },';
  }
}
?>
<!DOCTYPE HTML>
<html>

<head>  
  <script type="text/javascript" src="javascripts/canvasjs/canvasjs.min.js"></script>
  <script type="text/javascript">
  $(document).ready( function(){
    
    var chart1 = new CanvasJS.Chart("chartContainer1",
    {
      title:{
        text: "BASE RF INPUT action per region",
        fontSize: 20
      }, 
      theme: "theme1",
      data: [
      {        
        type: "doughnut",
        startAngle: 30,                          
        toolTipContent: "{y} sites",          
        showInLegend: true,
        dataPoints: [
        <?=$points1?>
        ]
      }
      ]
    });
    chart1.render();
    
    var chart2 = new CanvasJS.Chart("chartContainer2",
    {
      title:{
        text: "PARTNER",
        fontSize: 20
      }, 
      theme: "theme1",
      data: [
      {        
        type: "doughnut",
        startAngle: 30,                          
        toolTipContent: "{y} sites",          
        showInLegend: true,
        dataPoints: [
        <?=$points2?>
        ]
      }
      ]
    });
    chart2.render();
  
    var chart3 = new CanvasJS.Chart("chartContainer3",
    {
      title:{
        text: "BASE RF BEST CANDIDATE SELECTION action per region",
        fontSize: 20
      }, 
      theme: "theme1",
      data: [
      {        
        type: "doughnut",
        startAngle: 30,                          
        toolTipContent: "{y} sites",          
        showInLegend: true,
        dataPoints: [
        <?=$points3?>
        ]
      }
      ]
    });
    chart3.render();

    var chart4 = new CanvasJS.Chart("chartContainer4",
    {
      title:{
        text: "BASE RF actions per task",
        fontSize: 20
      }, 
      theme: "theme1",
      data: [
      {        
        type: "doughnut",
        startAngle: 30,                          
        toolTipContent: "{y} sites",          
        showInLegend: true,
        dataPoints: [
        <?=$points4?>
        ]
      }
      ]
    });
    chart4.render();

    var chart5 = new CanvasJS.Chart("chartContainer5",
    {
      title:{
        text: "BASE RF FUNDING action per region",
        fontSize: 20
      }, 
      theme: "theme1",
      data: [
      {        
        type: "doughnut",
        startAngle: 30,                          
        toolTipContent: "{y} sites",          
        showInLegend: true,
        dataPoints: [
        <?=$points5?>
        ]
      }
      ]
    });
    chart5.render();
  });
  </script>
  
  <body>
    

            <div class="row">
                <div class="col-md-6" id="chartContainer4" style="height: 300px;"></div>
                <div class="col-md-6" id="chartContainer5" style="height: 300px;"></div>
            </div>
            <div class="row">
                <div class="col-md-6" id="chartContainer1" style="height: 300px;"></div>
                <div class="col-md-6" id="chartContainer3" style="height: 300px;"></div>
            </div>
            <div class="row">
                <div class="col-md-6" id="chartContainer2" style="height: 300px;"></div>
                <div class="col-md-6" id="chartContainer6" style="height: 300px;"></div>
            </div>

  </body>
</html>

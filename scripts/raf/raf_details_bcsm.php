<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

if ($_POST['rafid']==''){
  echo "error RAFID is null";
}

$query = "Select TYPE from BSDS_RAFV2 WHERE RAFID = '".$_POST['rafid']."'";
$stmt6 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
if (!$stmt6) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmt6);
}
$type=$res6['TYPE'][0];
$pos = strpos($type, 'Upg');
if ($pos!==false){
  echo '<div class="alert alert-danger" role="alert"><b>No Best Candidate selection as RAF is for an upgrade!</b></div>';
  die;
}

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<script language="JavaScript">
$(document).ready(function() {
 
  function after_BCSM_save(response)  {  
    $('#RAFcontent').load('scripts/raf/raf_details_bcsm.php', {
        rafid:response.rafid,
        siteID:response.siteID 
    });
  } 

  function before_BCSM(data) {
    var haserror='no'; 
    $(".formSelects").each(function(){
      var plval=$('#'+this.id).val();
      if (plval==''){
        haserror='yes';
      }
    });

    if (haserror=='yes'){
      Messenger().post({
        message: 'You did not fill in all form fields',
        showCloseButton: true
      });
      return false;
    }else{
      $("#RAFcontent").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
      return true;
    }
  }
  var options = {
    success: after_BCSM_save,
    beforeSubmit: before_BCSM,
    dataType:  'json'
  };
  $('#form_bcsm').submit(function() { 
      $(this).ajaxSubmit(options); 
      return false; 
  });

  $('#version').on("change",function( e ){
    if ($('#Compare').is(':checked')){
      var compare=$("#Compare").val();
    }else{
      var compare='';
    }
    $("#RAFcontent").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
    $('#RAFcontent').load('scripts/raf/raf_details_bcsm.php', {
        compare:compare,
        rafid:$(this).data('rafid'),
        siteID:$(this).data('siteid'),
        version:$(this).val(),
        candidate:$("#candidate").val()
        
    });
  });
  $('#candidate').on("change",function( e ){
    $("#RAFcontent").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
    $('#RAFcontent').load('scripts/raf/raf_details_bcsm.php', {
        rafid:$(this).data('rafid'),
        siteID:$(this).data('siteid'),
        candidate:$(this).val(),
        version:$("#version").val()
    });
  });
});
</script>
<?php
$region=substr($_POST['siteID'],0,2);
if ($region=='BW' or $region=='HT' or $region=='LG' or $region=='LX' or $region=='NR'){
  $georegion='south';
}else if ($region=='BX' or $region=='AN' or $region=='LI' or $region=='OV' or $region=='WV' or $region=='VB'){
  $georegion='north';
}

$query = "Select * FROM BCS_MODEL ORDER BY GROUPORDER, QUESTIONORDER, ANSWERID";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmt);
  $amount_of_Q=count($res1['QUESTION']);
} 

for ($i = 0; $i <$amount_of_Q; $i++) { 
  $group=$res1['GROUP'][$i];
  $groupID=$res1['GROUPORDER'][$i];
  $question=$res1['QUESTION'][$i];
  $answer=$res1['ANSWER'][$i];
  $special=$res1['SPECIAL'][$i];
  $comments=$res1['COMMENTS'][$i];
  $answerid=$res1['ANSWERID'][$i];
  $questionorder=$res1['QUESTIONORDER'][$i];
  $scoring=$res1['SCORING'][$i];

  if ($prv_group!=$group){
    $groups[$groupID]=$group; 
  }

  if ($prev_question!=$question){
    //echo $group."($groupID):".$question."<br>";
    $groups_questions[$groupID][$questionorder]=$question;
    $groups_special[$groupID][$questionorder]=$special;
    $groups_comments[$groupID][$questionorder]=$comments;
  }

  $question_anwsers[$question][$answerid]=$answer;
  $question_scoring[$question][$answerid]=$scoring;

  $prv_group=$group;
  $prev_question=$question;
}

if ($candidate=="" && $_POST['candidate']==""){
  $query = "Select MAX(CANDIDATE) AS CANDIDATE from BCS_CANDIDATES WHERE RAFID = '".$_POST['rafid']."'";
  //echo $query;
  $stmt6 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
  if (!$stmt6) {
    die_silently($conn_Infobase, $error_str);
    exit;
  } else {
    OCIFreeStatement($stmt6);
  }
  $candidate=$res6['CANDIDATE'][0];

  if ($candidate==""){
    $candidate="A";
  }
}else{
  $candidate=$_POST['candidate'];
}

$query = "Select DISTINCT(CANDIDATE) from BCS_CANDIDATES WHERE RAFID = '".$_POST['rafid']."'";
//echo $query;
$stmt6 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
if (!$stmt6) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmt6);
}
$amount_of_Cand=count($res6['CANDIDATE']);
for ($i = 0; $i<$amount_of_Cand; $i++) {
  $candidatesList[]=$res6['CANDIDATE'][$i];
}

$query = "Select MAX(VERSION) AS VERSION from BCS_CANDIDATES WHERE RAFID = '".$_POST['rafid']."' AND CANDIDATE='".$candidate."'";
//echo $query;
$stmt6 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
if (!$stmt6) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmt6);
}
$version=$res6['VERSION'][0];

if ($version!=''){
  if ($_POST['version']==""){
    $ver=$version;
  }else{
    $ver=$_POST['version'];
  }
  $query = "SELECT * FROM BCS_CANDIDATES WHERE VERSION=".$ver." AND CANDIDATE='".$candidate."' AND RAFID='".$_POST['rafid']."' ORDER BY GROUPID, QUESTIONNUMBER";
  //echo $query;
  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
  if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
  } else {
    OCIFreeStatement($stmt);
    $amount_of_A=count($res1['ANSWERNUMBER']);
  } 
  for ($i = 0; $i<$amount_of_A; $i++) {
    
    $answers[$res1['GROUPID'][$i]][$res1['QUESTIONNUMBER'][$i]]=$res1['ANSWERNUMBER'][$i];
    if ($res1['ANSWERNUMBER'][$i]=="32"){
      $score_32=$res1['SCORING'][$i];
    }
    if ($res1['SCORING'][$i]==0){
      $scores0[$res1['GROUPID'][$i]]="yes";
      $hasOneWithZero="yes";
    }
    $scores[$res1['GROUPID'][$i]]=$scores[$res1['GROUPID'][$i]]+$res1['SCORING'][$i];
    $scoresMAX[$res1['GROUPID'][$i]]=$scoresMAX[$res1['GROUPID'][$i]]+5;
    $total=$total+$res1['SCORING'][$i];
    $totalMAX=$totalMAX+5;
  }
  if ($totalMAX!='' && $hasOneWithZero!='yes'){
    $totalpercentage=round(($total/$totalMAX)*100);
  }else{
    $totalpercentage=0;
  }


  if ($_POST['compare']!=""){
    $query = "SELECT * FROM BCS_CANDIDATES WHERE VERSION=".$_POST['compare']." AND CANDIDATE='".$candidate."' AND RAFID='".$_POST['rafid']."' ORDER BY GROUPID, QUESTIONNUMBER";
    //echo $query;
    $stmtC = parse_exec_fetch($conn_Infobase, $query, $error_str, $resC);
    if (!$stmtC) {
      die_silently($conn_Infobase, $error_str);
      exit;
    } else {
      OCIFreeStatement($stmtC);
      $amount_of_C=count($resC['ANSWERNUMBER']);
    }
    for ($i = 0; $i<$amount_of_C; $i++) {
    
      $answersC[$res1['GROUPID'][$i]][$resC['QUESTIONNUMBER'][$i]]=$resC['ANSWERNUMBER'][$i];
      if ($resC['ANSWERNUMBER'][$i]=="32"){
        $score_32C=$resC['SCORING'][$i];
      }
      if ($resC['SCORING'][$i]==0){
        $scores0C[$resC['GROUPID'][$i]]="yes";
        $hasOneWithZeroC="yes";
      }
      $scoresC[$res1['GROUPID'][$i]]=$scoresC[$resC['GROUPID'][$i]]+$resC['SCORING'][$i];
      $scoresMAXC[$resC['GROUPID'][$i]]=$scoresMAXC[$resC['GROUPID'][$i]]+5;
      $totalC=$totalC+$resC['SCORING'][$i];
      $totalMAXC=$totalMAXC+5;
    }
  }
}


$query = "SELECT * FROM RAN_SCAN_TODAY WHERE SITEID='".$_POST['siteID']."' AND (FILENAME LIKE '%SSR%' OR FILENAME LIKE '%DACL%')";
//echo $query;
$stmtF = parse_exec_fetch($conn_Infobase, $query, $error_str, $resF);
if (!$stmtF) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmtF);
  $amount_of_F=count($resF['FILENAME']);
}

?>

<form action="scripts/raf/raf_actions.php" class="form-horizontal" method="post" id="form_bcsm" role="form">
<input type="hidden" name="action" value="update_bcsm">
<input type="hidden" name="georegion" value="<?=$georegion?>">
<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
<input type="hidden" name="siteID" value="<?=$_POST['siteID']?>">

<?php
$letters = range('A', 'Z');
?>
<div class="form-group">
  <label for="q<?=$key?>" class="col-sm-4 control-label">BCS model for:</label>
  <div class="col-sm-2">
    <select class="form-control" id='candidate' name='candidate' data-siteID="<?=$_POST['siteID']?>" data-rafid="<?=$_POST['rafid']?>">
    <?php
    foreach ($candidatesList as $cand) {
      echo "<option>".$cand."</option>";
    }
    echo "<option>---------</option>";
    foreach ($letters as $letter) {
      if(!in_array($letter, $candidatesList)){
        if ($_POST['candidate']==$letter){
          echo "<option SELECTED>".$letter."</option>";
        }else{
          echo "<option>".$letter."</option>";
        }
      }
    }
    ?>
    </select>
  </div>
</div>
<?php if ($version!=''){ ?>
<div class="form-group">
  <label for="q<?=$key?>" class="col-sm-4 control-label">Version</label>
  <div class="col-sm-2">
    <select class="form-control" name='version' id='version' data-siteID="<?=$_POST['siteID']?>" data-rafid="<?=$_POST['rafid']?>">
    
    <?php
    if ($_POST['version']==""){
      echo "<option SELECTED value='".$version."'>VERSION ".$version."</option>";
    }
    for ($i=1; $i <= $version; $i++) { 
      if ($i==$_POST['version'] or ($_POST['version']=='' AND $i==$version)){
        $sel="SELECTED";
      }else{
        $sel="";
      }
      echo "<option ".$sel." value='".$i."'>VERSION ".$i."</option>";
    }
    ?>
    </select>
  </div>
  <?php
  if ($version!=""){
  ?>
  <input type='checkbox' name='Compare' id="Compare" value="<?=$ver?>"> compare
  <?php } ?>
</div>

<?php if ($totalpercentage>=70){
  $color="info";
}else{
  $color="danger";
} ?>
<div class="progress pull-right" style="width:100%">
  <div class="progress-bar progress-bar-<?=$color?>" role="progressbar" aria-valuenow="<?=$totalpercentage?>" aria-valuemin="0" aria-valuemax="100" style="min-width:3em;width: <?=$totalpercentage?>%;">
    <?=$totalpercentage?>%
  </div>
</div>
<br>
<?php } ?>

  <a class="btn btn-default pull-right" role="button" data-toggle="collapse" href="#collapseDocs" aria-expanded="false" aria-controls="collapseExample">
   Display documents
  </a>
  <br><br>
  <div class="collapse" id="collapseDocs">
    <div class="well">
<?php
  
  for($i=0;$i<$amount_of_F;$i++){
    $fileloc=urlencode($resF['KEY'][$i]);
    $dirloc=substr($resF['SUBPATH'][$i],0,2).'/'.rawurlencode($resF['SUBPATH'][$i]);
    $ranurl=$config['sitepath_url'].'/bsds/scripts/liveranbrowser/liveranbrowser.php?dir='.$dirloc.'&ran='.$res1['PARTNER'][$i];
    echo '<a class="btn btn-default btn-xs filedownload" target="_new" title="Download file" href="scripts/filebrowser/filedownload.php?file='.$fileloc.'&name='.$resF['FILENAME'][$i].'.'.$resF['EXTENSION'][$i].'"><span class="glyphicon glyphicon-download"></span></a>';
    echo '<a class="btn btn-default btn-xs liveran" title="Open containing folder" target="_blank" style="target-new: tab;" id="filefolder" data-ranurl="'.$ranurl.'"><span class="glyphicon glyphicon-circle-arrow-right"></span></a> '.$resF['FILENAME'][$i].".".$resF['EXTENSION'][$i]."<br>";
  }

// $question_anwsers[$question][$answerid]
//echo "<pre>".print_r($candidatesList,true)."</pre>";
//echo "<pre>".print_r($scoresMAX,true)."</pre>";
$imgSrcPathFinal  = $config['raf_folder_abs'].$_POST['rafid']."/".$_POST['siteID'].".jpg"; // absolute path of source image or relative path to source directory with image name.
  
if (file_exists($imgSrcPathFinal)){

  $imgSrcPathFinal2  = $config['raf_folder_abs'].$_POST['rafid']."/".$_POST['siteID']."_cropped.jpg"; // absolute path of source image or relative path to source directory with image name.
  $imgDestPath      = $config['raf_folder_abs'].$_POST['rafid']."/";  // relative path to destination directory.
  $imgName          = $_POST['siteID'].".jpg"; // image name
  $imgName2          = $_POST['siteID']."_cropped.jpg";
  $types        = array(1 => 'gif', 2 => 'jpg', 3 => 'png', 4 => 'swf', 5 => 'psd', 6 => 'bmp', 7 => 'tiff', 8 => 'tiff', 9 => 'jpc', 10 => 'jp2', 11 => 'jpx', 12 => 'jb2', 13 => 'swc', 14 => 'iff', 15 => 'wbmp', 16 => 'xbm');
  if (!file_exists($imgSrcPathFinal2)){
    list($width, $height, $type, $attr) = getimagesize($imgSrcPathFinal); 
    $fileExtension = $types[$type];
    //$fileExtension='jpg';   
    if($fileExtension == "jpg"){;
      $img    = imagecreatefromjpeg($imgSrcPathFinal) or die("Error Opening JPG"); 
      $quality  = 95;
      $valid_ext  = 1;
    }
    if($valid_ext){
      $img_top    = 0;
      $img_bottom     = 0;
      $img_left     = 0;
      $img_right    = 0;

      //top
      for($img_top = 0; $img_top < imagesy($img); ++$img_top) {
        for($x = 0; $x < imagesx($img); ++$x) {
        if(imagecolorat($img, $x, $img_top) != 0xFFFFFF) {
           break 2; //out of the 'top' loop
        }
        }
      }

      //bottom
      for($img_bottom = 0; $img_bottom < imagesy($img); ++$img_bottom) {
        for($x = 0; $x < imagesx($img); ++$x) {
        if(imagecolorat($img, $x, imagesy($img) - $img_bottom-1) != 0xFFFFFF) {
           break 2;// out of the 'bottom' loop
        }
        }
      }

      //left
      for($img_left = 0; $img_left < imagesx($img); ++$img_left) {
        for($y = 0; $y < imagesy($img); ++$y) {
        if(imagecolorat($img, $img_left, $y) != 0xFFFFFF) {
           break 2; //out of the 'left' loop
        }
        }
      }

      //right
      for($img_right = 0; $img_right < imagesx($img); ++$img_right) {
        for($y = 0; $y < imagesy($img); ++$y) {
        if(imagecolorat($img, imagesx($img) - $img_right-1, $y) != 0xFFFFFF) {
           break 2; //out of the 'right' loop
        }
        }
      }
      
      $newimg_width = $width;
      if(($img_left + $img_right) < $width){
        $newimg_width = $width-($img_left+$img_right);
      }
      $newimg_height = $height;
      if(($img_top+$img_bottom) < $height){
        $newimg_height = $height-($img_top+$img_bottom);
      }   
      $newimg = imagecreatetruecolor($newimg_width, $newimg_height);    
      imagecopy($newimg, $img, 0, 0, $img_left, $img_top, $newimg_width, $newimg_height); 
      imagedestroy($img);
      unset($img);
      if($fileExtension == "jpg"){
       
        imagejpeg($newimg, $imgDestPath.$imgName2, $quality)  or die("Cant save JPG");
      } 
    }
  }
  echo "<img src='".$config['raf_folder'].$_POST['rafid']."/".$imgName2."'>";
}else{
  echo "No area map available.";
}
?>
    </div>
</div>
<hr>
<?php

$groupnum=1;
foreach ($groups as $groupID => $group) {

  if ($scores0[$groupID]=='yes'){
    $percentage=0;
  }else if ($scoresMAX!=''){
    $percentage=round(($scores[$groupID]/$scoresMAX[$groupID])*100);
  }
  ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#gr<?=$groupID?>">
         <?=$groupnum++?>. <?=$group?>
        </a>
        <?php if ($percentage>=70){
                $color="success";
              }else{
                $color="danger";
              } ?>
        <div class="progress pull-right" style="width:400px">
          <div class="progress-bar progress-bar-<?=$color?>" role="progressbar" aria-valuenow="<?=$percentage?>" aria-valuemin="0" aria-valuemax="100" style="min-width:3em;width: <?=$percentage?>%;">
            <?=$percentage?>%
          </div>
        </div>
      </h4>
    </div>
    <div id="gr<?=$groupID?>" class="panel-collapse collapse">
      <div class="panel-body">       
      <?php 
      foreach ($groups_questions[$groupID] as $key2 => $question) { 

        if ($groups_special[$groupID][$key2]=='1'){
            echo '<span class="label label-danger pull-right">If this requirement not met, this group has score 0. This means BAD candidate proposed</span>';
        }
        ?>
        <div class="form-group">
          <label for="q<?=$key?>" class="col-sm-4 control-label"><?=$question?></label>
          <div class="col-sm-8">
            <select class="form-control formSelects" id='<?=str_replace("&", "",str_replace(" ", "_", $group))?>_<?=$key2?>' name="question[<?=$groupID?>][<?=$key2?>]">
            <option value=''>Please select</option>
            <?php 
            foreach ($question_anwsers[$question] as $key3 => $answer) { 
              //echo $groupID."/".$key3."--".$answers[$groupID][$key2[$i]]."<br>";
              if ($answers[$groupID][$key2]==$key3){
                $sel="SELECTED";
              }else{
                $sel="";
              }
              //SPECIAL FOR TRANSMISSION
              if ($key3==32 && $score_32!=''){
                $score=$score_32;
              }else{
                $score=$question_scoring[$question][$key3];
              }
              if($answersC[$groupID][$key2]==$key3 && $answersC[$groupID][$key2]!=$answers[$groupID][$key2]){
                $prev_answerC="VER. ".$_POST['compare'].":".$answer;
              }else{
                 $prev_answerC="";
              }
            ?>
            <option <?=$sel?> value='<?=$key3?>_<?=$score?>'><?=$answer?> (<?=$score?>)</option>
            <?php
            } 
            ?>
            </select>
            <?php
            echo "<font color='red'><b>".$prev_answerC."</b></font>";
            ?>
          </div>
        </div>
        <?php
          if ($groups_comments[$groupID][$key2]!=''){
            echo '<div class="well well-sm"><u>'.$question.'</u><br>'.str_replace("\n", "<br>", $groups_comments[$groupID][$key2]).'</div>';
          }
      }
      ?>
      </div>
    </div>
  </div>
  <?
}
?>
<input type='submit' value='Submit BCM' class='btn btn-success submitBCM' data-candidate='<?=$candletter?>' data-rafid='<?=$_POST['RAFID']?>'>
</form>
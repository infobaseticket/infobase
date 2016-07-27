<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/scripts/liveranbrowser/fileTypes.php");


function format_bytes($bytes) {
   if ($bytes < 1024) return $bytes.' B';
   elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KB';
   elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MB';
   elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GB';
   else return round($bytes / 1099511627776, 2).' TB';
}

if (file_exists($config['ranfolderBENCH'].urldecode($_REQUEST['dir']))) {
  $ranBENCH=true;
}
if (file_exists($config['ranfolderARCHIVE'].urldecode($_REQUEST['dir']))) {
  $ranARCHIVE=true;
}
if (file_exists($config['ranfolder'].urldecode($_REQUEST['dir']))) {
  $ranTECHM=true;
}
if (file_exists($config['ranfolderM4C'].urldecode($_REQUEST['dir']))) {
  $ranM4C=true;
}
if (file_exists($config['ranfolderLEASE'].urldecode($_REQUEST['dir']))) {
  $ranLEASE=true;
}


if ($_REQUEST['ran']=='BENCHMARK_RAN'){
  $ran=$config['ranfolderBENCH'];
  $ranurl=$config['ranfolderBENCH_url'];
  $ranloc='BENCHMARK RAN';
  $ranexists=$ranBENCH;
}else if ($_REQUEST['ran']=='RAN_ARCHIVE'){
  $ran=$config['ranfolderARCHIVE'];
  $ranurl=$config['ranfolderARCHIVE_url'];
  $ranloc='ARCHIVE RAN';
  $ranexists=$ranARCHIVE;
}else if ($_REQUEST['ran']=='M4C_RAN'){
  $ran=$config['ranfolderM4C'];
  $ranurl=$config['ranfolderM4C_url'];
  $ranloc='M4C RAN';
  $ranexists=$ranM4C;
}else if ($_REQUEST['ran']=='RAN_LEASE'){
  $ran=$config['ranfolderLEASE'];
  $ranurl=$config['ranfolderLEASE_url'];
  $ranloc='LEASE&BP RAN';
  $ranexists=$ranLEASE;
}else{
  $ran=$config['ranfolder'];
  $ranurl=$config['ranfolder_url'];
  $ranloc='TECHM RAN'; 
  $ranexists=$ranTECHM;
}

$dir_folder=$ran.urldecode($_REQUEST['dir']);

if(substr($dir_folder, -1)=="/"){
  $dir_folder=substr($dir_folder,0, -1);
}
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <title>Infobase</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Infobase developed for BASECOMPANY">
    <meta name="author" content="Frederick Eyland">

    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>bootstrap/css/bootstrap.min.css" media="all">
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>css/dirlister.css" media="all">
  </head>
  <body>
  
<?php

if ($ranexists==true){
    // Generate breadcrumbs
    $dirArray = explode('/', $_REQUEST['dir']);
    $i=0;
    $breadcrumbHome="<span class='label label-warning'><a href='".$_SERVER['PHP_SELF'].'?dir=&ran='.$_REQUEST['ran']."'>".$ranloc."</a></span> / ";
    foreach ($dirArray as $key => $directory) {
      
      if ($directory!=''){
        $url3.=rawurlencode($directory)."/";
        $url=$_SERVER['PHP_SELF'].'?dir='.substr($url3,0,-1).'&ran='.$_REQUEST['ran'];
        if(count($dirArray)-1!=$i){
          $breadcrumb.="<a href='".$url."'>".$directory."</a> / ";
        }else{
          $breadcrumb.=$directory;
        }
      }
      $i++;
    }
    $dirLink=$_REQUEST['dir'];
}else{
  $dirLink=substr($_REQUEST['dir'],0,10);

  if (file_exists($config['ranfolderBENCH'].urldecode($dirLink))) {
    $ranBENCH=true;
  }
  if (file_exists($config['ranfolderARCHIVE'].urldecode($dirLink))) {
    $ranARCHIVE=true;
  }
  if (file_exists($config['ranfolder'].urldecode($dirLink))) {
    $ranTECHM=true;
  }
  if (file_exists($config['ranfolderM4C'].urldecode($dirLink))) {
    $ranM4C=true;
  }

  if (file_exists($config['ranfolderLEASE'].urldecode($dirLink))) {
    $ranLEASE=true;
  }
}

  ?>
  <div id="page-navbar" class="navbar navbar-default navbar-fixed-top">

    <div class="container">
      
      <div class="pull-left">
       <p class="navbar-text"><?=$breadcrumbHome?><?=$breadcrumb?></p>
      </div>
      <div class="pull-right"><p class="navbar-text">
      <?php if ($_REQUEST['ran']!='' && $_REQUEST['ran']!='RAN-ALU' && $ranTECHM==true){ ?> 
       <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span> <a href='<?=$_SERVER['PHP_SELF']?>?ran=RAN-ALU&dir=<?=$dirLink?>'>RAN-TECHM</a> |
      <?php } 
      if ($_REQUEST['ran']!='BENCHMARK_RAN' && $ranBENCH==true){ ?> 
        <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span> <a href='<?=$_SERVER['PHP_SELF']?>?ran=BENCHMARK_RAN&dir=<?=$dirLink?>'>RAN-BENCHMARK</a> |
      <?php } 
      if ($_REQUEST['ran']!='RAN_ARCHIVE' && $ranARCHIVE==true){ ?>  
        <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span> <a href='<?=$_SERVER['PHP_SELF']?>?ran=RAN_ARCHIVE&dir=<?=$dirLink?>'>RAN-ARCHIVE</a> |
      <?php }
      if ($_REQUEST['ran']!='RAN_M4C' && $ranM4C==true){ ?>  
        <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span> <a href='<?=$_SERVER['PHP_SELF']?>?ran=RAN_M4C&dir=<?=$dirLink?>'>RAN-M4C</a> |
      <?php }
      if ($_REQUEST['ran']!='RAN_LEASE' && $ranLEASE==true){ ?>  
        <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span> <a href='<?=$_SERVER['PHP_SELF']?>?ran=RAN_LEASE&dir=<?=$dirLink?>'>RAN-LEASE&BP</a></p>
      <?php } ?>
      </div>
    </div>
  </div>
  <?php 

  if ($ranexists==true){
    ?>
  <div id="page-content" class="container" style="padding-top: 56px;">
    <div id="directory-list-header">
      <div class="row">
          <div class="col-md-5 col-sm-4 col-xs-8">File</div>
          <div class="col-md-2 col-sm-2 col-xs-2 text-right">Ext</div>
          <div class="col-md-2 col-sm-2 col-xs-2 text-right">Size</div>
          <div class="col-md-3 col-sm-4 hidden-xs text-right">Last Modified</div>
      </div>
    </div>
    <ul id="directory-listing" class="nav nav-pills nav-stacked">
    <?php
    $dir = new DirectoryIterator($dir_folder);
    while($dir->valid()) {

      //if(!$dir->isDot()) { //&& !$dir->isDir()
      if(!$dir->isDot()){
        $file_extension = strtolower(pathinfo($dir->getFilename(), PATHINFO_EXTENSION));

        if ($dir->isDir()) { //is a dir
          $url2 = implode('/', array_map('rawurlencode', explode('/', $_REQUEST['dir']."/".$dir->getFilename())));
          $url=$_SERVER['PHP_SELF'].'?dir='.$url2.'&ran='.$_REQUEST['ran'];
            $icon = 'folder-close';
            $fileExt="-";
            $isdir='_self';
        } else {  //is a file

           $isdir='LIVERAN';
   
          $fileExt = strtolower(pathinfo($_REQUEST['dir']."/".$dir->getFilename(), PATHINFO_EXTENSION));
          $url=urlencode($ran.$_REQUEST['dir']);
          $url="../filebrowser/filedownload.php?file=".$url."/".$dir->getFilename()."&name=".$dir->getFilename();
        
          if (isset($extensions[$file_extension])) {
              $icon=$extensions[$file_extension];
          } else {
              $icon=$extensions['blank'];
              $fileExt = '-';
          }
        }
        $showup="yes";
      }else{ //is a dir
          $pathArray = explode('/',  $_REQUEST['dir']);
          if(count($pathArray)>1){
              unset($pathArray[count($pathArray)-1]);
              $url2 = implode('/', $pathArray);

              if (!empty($url2)) {
                $url=$_SERVER['PHP_SELF'].'?dir='.$url2.'&ran='.$_REQUEST['ran'];
              }else{
                $url=$_SERVER['PHP_SELF'];
                
              }
              $showup="yes";
          }else{
            $showup="no";
          }
          $icon="level-up";
          $fileExt="-";
      }

      if (substr($dir->current(),0,5)!='thumb' && $showup=="yes" && (substr($dir->getFilename(),0,1)!='.' or (substr($dir->getFilename(),0,2)=='..' && $showup=="yes" )) ){
     ?>
       <li data-name="<?php echo $name; ?>" data-href="<?php echo $url; ?>">
          <a href="<?=$url?>" class="clearfix" data-name="<?php echo $name; ?>" target="<?=$isdir?>">
           <div class="row">
              <span class="file-name col-md-5 col-sm-4 col-xs-8">
                  <span class="glyphicon glyphicon-<?=$icon?>"></span>
                  <?=$dir->getFilename()?> 
              </span>

              <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                  <?=$fileExt?>
              </span>

              <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                  <?php
                  if (!$dir->isDir()){
                    echo format_bytes($dir->getSize());
                  }else{
                    echo "-";
                  }
                  ?>
              </span>

              <span class="file-modified col-md-3 col-sm-4 hidden-xs text-right">
                  <?=date("d-m-Y H:i:s",$dir->getMTime())?>
              </span>
          </div>
        </a>
      </li>
        <?php
        }
      //}
      $dir->next();
    }
    ?>
    </ul>
    <br><br>
  </div>
 <?php
}else{
  echo '<div id="page-content" class="container" style="padding-top: 56px;"><br><br><div class="alert alert-danger">folder '.$dir_folder.' does not exist.<br>
  Please us one of the icons on the top to go to an other RAN (if folder exists on other RAN)</div></div>';
}
?>
</body>
</html>
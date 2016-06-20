<a class="btn btn-primary pull-right" role="button" data-toggle="collapse" href="#collapseDocs" aria-expanded="false" aria-controls="collapseExample">
 Display documents
</a>
<div class="collapse" id="collapseDocs">
  <div class="well">
<?php
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
  echo "No area map avilable.";
}
?>
  </div>
</div>
<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/dirlister/filefunctions.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/dirlister/class_thumbnails.php");
error_reporting(0);
?>
<script type="text/javascript">
$(document).ready(function() {

	$(".audit_uploadbutton").click(function(){
   		$('#uploadForm'+ $.session("tabid")).ajaxSubmit({
	        beforeSubmit: function(a,f,o) {
	            o.dataType ="HTML";
	            $('#uploadOutput'+ $.session("tabid")).html('<font color="blue"><b>Uploading...</b></font>');
	            $("#loadingbar"+$.session("tabid")).show();
	        },
	        success: function(data) {
	            var $out = $('#uploadOutput'+ $.session("tabid"));
	            //$out.html('Form success handler received: <strong>' + typeof data + '</strong>');
	            if (typeof data == 'object' && data.nodeType)
	                data = elementToString(data.documentElement, true);
	            else if (typeof data == 'object')
	                data = objToString(data);
	            $out.html('<div><pre>'+ data +'</pre></div>');
	            $('#radiofiles').load("scripts/audit/audit_details_files.php", {},
				function(){
					$("#loadingbar"+$.session("tabid")).hide();
				});
	        }
	    })
	});

});

</script>
<div id="radiofiles<?=$_POST['tabid']?>">
<?php

$filelist=getFileList($config['audit_folder_abs'].$_POST['auditid']);

for( $i=0; $i < count($filelist) ; $i++ ) {
  $exp=explode("_",$filelist[$i]['name'],2);

   if (($filelist[$i]['type']=="jpg" || $filelist[$i]['type']=="gif" || $filelist[$i]['type']=="png") && substr_count($filelist[$i]['name'], 'thumb')==1){
  	echo "<div id='auditfile_".$i."' class='auditfile'>
	<img src='".$config['sitepath_url']."/bsds/images/minus.png' id='$i*".$filelist[$i]['name']."*".$_POST['auditid']."' class='auditfile_delete pointer'>
  	<a href='".$config['audit_folder'].$_POST['auditid']."/ori_".$exp[1]."'  target='_new'>
    <img class='full' src='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."' longdesc='".$config['audit_folder'].$_POST['auditid']."/ori_".$exp[1]."' title='".$exp[1]."'></a><br>
	".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod'].
  	"</div>";

  }else if ($filelist[$i]['type']=="docx" || $filelist[$i]['type']=="doc"){
	echo "<div id='auditfile_".$i."' class='auditfile'>
	<img src='".$config['sitepath_url']."/bsds/images/minus.png' id='$i*".$filelist[$i]['name']."*".$_POST['auditid']."' class='auditfile_delete pointer'>
  	<a href='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."'  target='_new'>
    <img src='images/fileicons/word.png' width='80px' height='80px'></a><br>
	<br>".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod'].
  	"</div>";
  }else if ($filelist[$i]['type']=="xlsx" || $filelist[$i]['type']=="xls"){

	echo "<div id='auditfile_".$i."'  class='auditfile'>
	<img src='".$config['sitepath_url']."/bsds/images/minus.png' id='$i*".$filelist[$i]['name']."*".$_POST['auditid']."' class='auditfile_delete pointer'>
  	<a href='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."'  target='_new'>
    <img src='images/fileicons/excel.png' width='80px' height='80px'></a><br>
	".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod'].
   	"</div>";
  }else if ($filelist[$i]['type']=="pdf"){
  	echo "<div id='auditfile_".$i."' class='auditfile'>
	<img src='".$config['sitepath_url']."/bsds/images/minus.png' id='$i*".$filelist[$i]['name']."*".$_POST['auditid']."' class='auditfile_delete pointer'>
  	<a href='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."'  target='_new'>
    <img src='images/fileicons/acrobat.png' width='80px' height='80px'></a><br>
	".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod'].
   	"</div>";
  }else {
  	if ($filelist[$i]['type']=="jpg"  && substr_count($filelist[$i]['name'], 'ori')!=1){

  		$target_path = $_SERVER['DOCUMENT_ROOT'] ."/infobase/files/audit/".$POST['auditid']."/";
  		$target_pathfile = $target_path . basename( $filelist[$i]['name']);
  		$File_without_ext=explode(".",$filelist[$i]['name']);
  		$File_without_ext=$File_without_ext[0];

  		  $image = new Image();
          $image->setFile($target_pathfile);
          $image->setUploadDir($target_path);
          $image->resize(840);
          $image->createFile("ori_".$File_without_ext);
          $image->resize(150);
          $image->createFile("thumb_".$File_without_ext);
          $image->flush();

          unlink($target_pathfile);

          ?>
          <script language="javascript">
          $('#radiofiles').load("scripts/audit/audit_details_files.php", {},
  		function(){
  			//$("#loading").hide();
  		});
  		</script>

          <?
  	}else if( substr_count($filelist[$i]['name'], 'ori')!=1){
  		echo "<div id='auditfile_".$i."' class='auditfile'>
			<img src='".$config['sitepath_url']."/bsds/images/minus.png' id='$i*".$filelist[$i]['name']."*".$_POST['auditid']."' class='auditfile_delete pointer'>
		  	<a href='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."'  target='_new'>
		    <img src='images/fileicons/media.png' width='80px' height='80px'></a><br>
			".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod'].
   	"</div>";
  	}
  }
}
?>
<div style='clear:both'></div>
<a class="audit_attach" href="#"><span><b>Upload a file or image</b></span></a>
<br><br>
<div class="clear"></div>
<div id="audit_upload<?=$_POST['tabid']?>" style="display:none">
	All files can be uploaded!<br>
	For bigger files, please use FTP program:<br>
	Host: infobase<br>
	User: audit<br>
	Pass: freedom<br><br>
	Only allowed extensions: xlsx, xls,xlsm, docx, doc, png, jpg, gif, txt, zip, msg, vsd
	<form id="uploadForm<?=$_POST['tabid']?>" action="scripts/audit/file_upload.php" method="POST" enctype="multipart/form-data">
	    <input type="hidden" name="MAX_FILE_SIZE" value="35000000">
	    <input type="hidden" name="auditid" value="<?=$_POST['auditid']?>">
	    File: <input type="file" name="uploadedfile"><input type="button" value="UPLOAD" class="audit_uploadbutton">
	</form><br><br><br>
	<label>Output:</label>
	<div id="uploadOutput<?=$_POST['tabid']?>"></div>
</div>
</div>
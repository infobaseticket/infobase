<?
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs'].'/include/PHP/dirlist/filefunctions.php');
require($config['sitepath_abs']."/include/PHP/dirlist/class_thumbnails.php");
?>
<link type="text/css" href="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-fullsize/fullsize.css" rel="stylesheet"></script>
<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-fullsize/jquery.fullsize.pack.js"></script>

<script type="text/javascript">
$(document).ready(function() {	
	$('.full').fullsize();
});
</script>

<table  border=1 width=400px>
 <?
 	$filelist=getFileList($config['audit_folder_abs'].$_POST['auditid']);
	//echo "<pre>".print_r($filelist,true)."</pre>";
	
	echo "<tr style='bgcolor:yellow'>";
	$j=0;
	for( $i=0; $i < count($filelist) ; $i++ ) {		
	 	
		if ($j==2){
		echo "</tr><tr>";
		$j=0;
		}
		$exp=explode("_",$filelist[$i]['name'],2);
		if (($filelist[$i]['type']=="jpg" || $filelist[$i]['type']=="gif" || $filelist[$i]['type']=="png") && substr_count($filelist[$i]['name'], 'thumb')==1){			
			echo "<td id='auditfile_".$i."'><div><a href='".$config['audit_folder'].$_POST['auditid']."/ori_".$exp[1]."'  target='_new'>
			<img class='full' src='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."' longdesc='".$config['audit_folder'].$_POST['auditid']."/ori_".$exp[1]."' title='".$exp[1]."'></a><br>";
			echo "<table><tr><td><br></td><td>&nbsp;&nbsp;</td>";			
			echo "<td><img src='".$config['sitepath_url']."/images/icons/del2.png' id='".$_POST['auditid']."*".$filelist[$i]['name']."*".$i."' class='auditfile_delete pointer'> ".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod']."</td>";
			echo "</tr></table></div><br></td>";
			$j++;
		}else if ($filelist[$i]['type']=="docx" || $filelist[$i]['type']=="doc"){
			echo "<td id='auditfile_".$i."'><a href='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."' target='_new'>
			<img src='../images/icons/Microsoft Office Word.png' width='80px' height='80px'></a><br>";
			echo "<table><tr><td><br></td><td>&nbsp;&nbsp;</td>";
			echo "<td><img src='".$config['sitepath_url']."/images/icons/del2.png' id='".$_POST['auditid']."*".$filelist[$i]['name']."*".$i."' class='auditfile_delete pointer'>".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod']."</td>";
			echo "</tr></table><br></td>";
			$j++;
		}else if ($filelist[$i]['type']=="xlsx" || $filelist[$i]['type']=="xls"){
			echo "<td id='auditfile_".$i."'><a href='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."' target='_new'>
			<img src='../images/icons/Microsoft Office Excel.png' width='80px' height='80px'></a><br>";
			echo "<table><tr><td><br></td><td>&nbsp;&nbsp;</td>";
			echo "<td><img src='".$config['sitepath_url']."/images/icons/del2.png' id='".$_POST['auditid']."*".$filelist[$i]['name']."*".$i."' class='auditfile_delete pointer'>".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod']."</td>";
			echo "</tr></table><br></td>";
			$j++;
		}else if ($filelist[$i]['type']=="pdf"){
			echo "<td id='auditfile_".$i."'><a href='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."' target='_new'>
			<img src='../images/icons/Adobe Acrobat Reader.png' width='80px' height='80px'></a><br>";
			echo "<table><tr><td><br></td><td>&nbsp;&nbsp;</td>";
			echo "<td><img src='".$config['sitepath_url']."/images/icons/del2.png' id='".$_POST['auditid']."*".$filelist[$i]['name']."*".$i."' class='auditfile_delete pointer'>".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod']."</td>";
			echo "</tr></table><br></td>";
			$j++;
		}else {
			if ($filelist[$i]['type']=="jpg"  && substr_count($filelist[$i]['name'], 'ori')!=1){
				
				$target_path = $_SERVER['DOCUMENT_ROOT'] ."/infobase/files/audit/".$_POST['auditid']."/";
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
		        $('#radiofiles').load("scripts/audit/audit_filesoverview.php", {}, 
				function(){
					//$("#loading").hide();
				});
				</script>
		        
		        <?
			}else if( substr_count($filelist[$i]['name'], 'ori')!=1){
				echo "<td id='auditfile_".$i."'><a href='".$config['audit_folder'].$_POST['auditid']."/".$filelist[$i]['name']."' target='_new'>
				<img src='../images/icons/Microsoft Media Center.png' width='80px' height='80px'></a><br>";
				echo "<table><tr><td><br></td><td>&nbsp;&nbsp;</td>";
				echo "<td><img src='".$config['sitepath_url']."/images/icons/del2.png' id='".$_POST['auditid']."*".$filelist[$i]['name']."*".$i."' class='auditfile_delete pointer'>".$filelist[$i]['name'] ."<br>".$filelist[$i]['size']."kb<br>".$filelist[$i]['lastmod']."</td>";
				echo "</tr></table><br></td>";
				$j++;
			}		
			
		}
		
	}
	echo "</tr><tr><td></td></tr>"
?>
</table>
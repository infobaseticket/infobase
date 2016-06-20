<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
 
require($config['sitepath_abs']."/include/PHP/dirlist/class_thumbnails.php");

$target_path = $_SERVER['DOCUMENT_ROOT'] ."/infobase/files/audits/".$_POST['auditid']."/";

/* Add the original filename to our target path.  
Result is "uploads/filename.extension" */

if (!is_dir($target_path))
{
mkdir($target_path, 0777);
} 

$target_pathfile = $target_path . basename( $_FILES['uploadedfile']['name']); 

$File_without_ext=explode(".",$_FILES['uploadedfile']['name']);
$File_without_ext=$File_without_ext[0];
//error_reporting(E_ALL);
//echo $target_pathfile;
if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_pathfile)) {
    echo "The file ".  basename( $_FILES['uploadedfile']['name']). " has been uploaded";
    $ext = strtolower(pathinfo($_FILES['uploadedfile']['name'], PATHINFO_EXTENSION));    

	if ($ext=="jpg" || $ext=="gif" || $ext=="png"){
/*	echo $target_pathfile."<br>";
	echo $target_path."<br>";
	echo $File_without_ext."<br>";*/
	
		$image = new Image();
        $image->setFile($target_pathfile);
        $image->setUploadDir($target_path);
        $image->resize(840);
        $image->createFile("ori_".$File_without_ext);
        $image->resize(150);
        $image->createFile("thumb_".$File_without_ext);
        $image->flush();
        
        unlink($target_pathfile);
	}
} else{
    echo "There was an error uploading the file, please try again!".print_r($_FILES);
}

?> 
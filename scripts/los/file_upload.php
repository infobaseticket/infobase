<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");

if ($_POST['losid']!='' && is_array($_FILES['myfile']['name'])){
    $target_path = $_SERVER['DOCUMENT_ROOT'] ."/infobase/files/los/".$_POST['losid']."/";
    if (!is_dir($target_path))
    {
    mkdir($target_path, 0777);
    }


    //echo "<pre>".print_r($_FILES['myfile'],true)."</pre>";
    $i=0;
    foreach ($_FILES['myfile']['name'] as $filename){
        $target_pathfile = $target_path . basename($filename);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext=="jpg" || $ext=="pdf" || $ext=="gif" || $ext=="png" || $ext=="doc" || $ext=="docx" || $ext=="xls"|| $ext=="xlsm" || $ext=="xlsx" || $ext=="txt" || $ext=="zip"|| $ext=="msg"|| $ext=="vsd"){
            if(move_uploaded_file($_FILES['myfile']['tmp_name'][$i], $target_pathfile)) {
                echo "The file ".$filename. " has been uploaded.";
            } else{
                echo "<span class='label label-important'>There was an error uploading ".$filename. ", please try again!</span><br>";
            }
        }else{
             echo "<span class='label label-important'>There was an error uploading ".$filename. ": wrong file extension (".$ext.")!</span><br>";
        }
        $i++;

    }
}else{
    echo "<span class='label label-important'>Something is wrong with Infobase or you did not choose a file. PLease contact Frederick Eyland!</span><br>";
}
?>
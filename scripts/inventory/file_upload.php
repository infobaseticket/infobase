<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");

if (is_array($_FILES['myfile']['name'])){
	if (strpos(strtolower($_FILES['myfile']['name'][0]),'inventory')!==false && $_POST['filetype']=='inventory'){
		$target_pathfile = $_SERVER['DOCUMENT_ROOT'] ."/Uploads/INVENTORY/Inventory_today/inventory.xls";
	}else if (strpos(strtolower($_FILES['myfile']['name'][0]), 'movement')!==false && $_POST['filetype']=='movement'){
		$target_pathfile = $_SERVER['DOCUMENT_ROOT'] ."/Uploads/INVENTORY/Movement_today/movement.xls";
	}else{
		$out['msg']="Filename is not correct!";
		$out['msgtype']="error";
		$error=1;
	}
	if ($error!=1){
		$ext = strtolower(pathinfo($_FILES['myfile']['name'][0], PATHINFO_EXTENSION));
		if ($ext=="xls" ){
			if(move_uploaded_file($_FILES['myfile']['tmp_name'][0], $target_pathfile)) {
			    $out['msg']="The file has been uploaded.";
			    $out['msgtype']="info";
			} else{
			   $out['msg']="There was an error uploading the file, please try again!";
			   $out['msgtype']="error";
			}
		}else{
			 $out['msg']="There was an error uploading the file: wrong file extension (".$ext.")!";
			 $out['msgtype']="error";
		}
	}
}else{
	$out['msg']="Something is wrong with Infobase or you did not choose a file. PLease contact Frederick Eyland!";
	$out['msgtype']="error";
}
echo json_encode($out);
?>
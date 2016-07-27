<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=LOSexport.csv");
header("Pragma: no-cache");
header("Expires: 0");

echo $_POST['data'];

exit();
	
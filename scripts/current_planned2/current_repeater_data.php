<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_viewtype","");

$currentdata=get_data($_POST['band'],"","CURRENT_EXISTING",$viewtype,$_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['donor'],$lognode);

$OWNER=$currentdata['OWNER'][0];
$BRAND=$currentdata['BRAND'][0];
$RTYPE=$currentdata['TYPE'][0];
$TECHNOLOGY=$currentdata['TECHNOLOGY'][0];
$CHANNEL=$currentdata['CHANNEL'][0];
$PICKUP=$currentdata['PICKUP'][0];
$DISTRIB=$currentdata['DISTRIB'][0];
$COSP=$currentdata['COSP'][0];
$COMMENTS=$currentdata['COMMENTS'][0];

?>
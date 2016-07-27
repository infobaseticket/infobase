<?php
<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("../procedures/cur_plan_procedures.php");

include("../current_planned_register.php");
//error_reporting(E_ALL);

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$conn_mysql = mysql_connect("$mysql_host", "$mysql_user", "$mysql_password");


if ($_GET['filetype']=="xls"){
	$filename="BSDS_".$_SESSION['BSDSKEY']."_".$_SESSION['BSDS_BOB_REFRESH'].".xls";
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$filename");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}
?>
<html>
<head>
<title>BSDS <?=$_SESSION['BSDSKEY']?>
<?
if ($_SESSION['BSDS_BOB_REFRESH']!=""){
	echo "-".$_SESSION['BSDS_BOB_REFRESH'];
}
?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/CSS/bsds_print.css" type="text/css"></link>

<script language="JavaScript">

  function printpage() {
  window.print();
  }

</script>
</head>
<body onload="printpage()">
<?
/******************************************************************** GSM1800 */
$type="GSM1800";
$sec1="1";
$sec2="2";
$sec3="3";
$sec4="0";

$BSDS_status=$_SESSION['BSDS_UPGdata'][$_SESSION['UPGNR']][$type]['STATUS'];
$BSDS_color= $_SESSION['BSDS_UPGdata'][$_SESSION['UPGNR']][$type]['COLOR'];

$check_current_exists=check_current_exists($type,'allsec'); //Check if there is current data in Infobase DB
if ($check_current_exists!=0 || $BSDS_status=="BSDS FUNDED"){
	$check_planned_exists=check_planned_exists($type,'allsec');
}

$gen_info=get_BSDS_generalinfo();

$pl_is_BSDS_accepted=$gen_info['TEAML_APPROVED'][0];
$pl_CHANGEDATE=$gen_info['UPDATE_AFTER_COPY'][0];

if ($check_planned_exists!="0"){
	include("../planned_data.php");
}

include("../current_data.php");

if ($check_planned_exists=="0"){
	include("../planned_data.php");
}

include("../height_conversion.php");
include("current_planned_output.php");
$pl_COMMENTS="";
/******************************************************************** GSM900 */
$type="GSM900";
$sec1="4";
$sec2="5";
$sec3="6";
$sec4="0";

$BSDS_status=$_SESSION['BSDS_UPGdata'][$_SESSION['UPGNR']][$type]['STATUS'];
$BSDS_color= $_SESSION['BSDS_UPGdata'][$_SESSION['UPGNR']][$type]['COLOR'];

$check_current_exists=check_current_exists($type,'allsec'); //Check if there is current data in Infobase DB
if ($check_current_exists!=0 || $BSDS_status=="BSDS FUNDED"){
	$check_planned_exists=check_planned_exists($type,'allsec');
}

if ($check_planned_exists!="0"){
	include("../planned_data.php");
}

include("../current_data.php");

if ($check_planned_exists=="0"){
	include("../planned_data.php");
}
include("../height_conversion.php");
include("current_planned_output.php");
$pl_COMMENTS="";
/******************************************************************** UMTS */
$type="UMTS";

$BSDS_status=$_SESSION['BSDS_UPGdata'][$_SESSION['UPGNR']][$type]['STATUS'];
$BSDS_color= $_SESSION['BSDS_UPGdata'][$_SESSION['UPGNR']][$type]['COLOR'];

$_SESSION['cab']="01";

if ($_SESSION['lognodepk']){

	//GET CURRENT AND PLANNED STATUS
	$check_current_exists_UMTS=check_current_exists($type,'allsec');
	$check_current_exists_UMTS_sec1=check_current_exists($type,'1');
	$check_current_exists_UMTS_sec2=check_current_exists($type,'2');
	$check_current_exists_UMTS_sec3=check_current_exists($type,'3');
	if ($check_current_exists_UMTS!=0 || $BSDS_status=="BSDS FUNDED"){
		$check_planned_exists_UMTS=check_planned_exists("UMTS",'allsec');
	}

	if ($check_planned_exists_UMTS!="0"){
		include("../planned_data.php");
	}

	include("../current_data.php");

	if ($check_planned_exists_UMTS=="0"){
		include("../planned_data.php");
	}

	include("../height_conversion.php");
	include("current_planned_output_umts.php");


}else{
	echo "<p align=center><font color=red><b>No UMTS data available in Asset!</b></font></p>";
}


/******************************************************************** UMTS900 */
$type="UMTS900";

$BSDS_status=$_SESSION['BSDS_UPGdata'][$_SESSION['UPGNR']][$type]['STATUS'];
$BSDS_color= $_SESSION['BSDS_UPGdata'][$_SESSION['UPGNR']][$type]['COLOR'];

$_SESSION['cab']="01";

if ($_SESSION['lognodepk_900']){

	//GET CURRENT AND PLANNED STATUS
	$check_current_exists_UMTS900=check_current_exists($type,'allsec');
	$check_current_exists_UMTS_sec1=check_current_exists($type,'1');
	$check_current_exists_UMTS_sec2=check_current_exists($type,'2');
	$check_current_exists_UMTS_sec3=check_current_exists($type,'3');
	if ($check_current_exists_UMTS900!=0 || $BSDS_status=="BSDS FUNDED"){
		$check_planned_exists_UMTS900=check_planned_exists("UMTS",'allsec');
	}

	if ($check_planned_exists_UMTS900!="0"){
		include("../planned_data.php");
	}

	include("../current_data.php");

	if ($check_planned_exists_UMTS=="0"){
		include("../planned_data.php");
	}

	include("../height_conversion.php");
	include("current_planned_output_umts900.php");


}else{
	echo "<p align=center><font color=red><b>No UMTS900 data available in Asset!</b></font></p>";
}


?>
<div id="page">
<font color="blue" size=3><u><b>General info:</b></u></font><br>
UPGNR: <?=$_GET['UPGNR']?><br>
STATUS BSDS: <?=$_SESSION['BSDS_status']?><br>
FUNDED TECHNOLOGIES: <?=$_GET['technos']?>
<br><br>
<font color="blue" size=3><u><b>BSDS comments:</b></u></font><br>
<u><b>GSM900:</b></u><br> <? echo $pl_COMMENTS_GSM900 ?><br>
<u><b>GSM1800:</b></u><br> <? echo $pl_COMMENTS_GSM1800 ?><br>
<u><b>UMTS:</b></u><br> <? echo $pl_COMMENTS_UMTS ?><br>
</div>
<?
//echo "<pre>".print_r($_GET['BSDS_funded'],true)."</pre>";
OCILogoff($conn_Infobase);
?>
</body>
</html>

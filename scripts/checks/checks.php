<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators","");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
include("../procedures/cur_plan_procedures.php");
//error_reporting(E_ALL);

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);

if ($_GET['IDNR']!=""){ $IDNR=$_GET['IDNR']; }
if ($_POST['IDNR']!=""){ $IDNR=$_POST['IDNR']; }

if ($_POST['save']=="SAVE FILTER" || $_POST['save']=="SAVE AS NEW FILTER"){
	$error=escape_sq($_POST['ERROR']);
	$rule=escape_sq($_POST['RULE']);
	$query="
	INSERT INTO INFOBASE.BSDS_VALIDATION (
	   RULE, ERROR, ACTIVE,TECHNOLOGY)
	VALUES ('".$rule."', '".$error."','yes','".$_POST['TECHNOLOGY']."')";

	//echo $query;

	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$message.="FIELD has been ADDED!<br>";
		OCICommit($conn_Infobase);
	}
	$error=unescape_quotes($_POST['error']);

}

if ($_GET['action']=='delete'){
	$query="DELETE FROM INFOBASE.BSDS_VALIDATION WHERE IDNR='".$IDNR."'";
	//echo $query;
	$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	}else{
		$message.="FILTER HAS BEEN DELETED!<br>";
		OCICommit($conn_Infobase);
	}
	$IDNR="";
}

?>
<html>
<head>
<title>BSDS - Administartion of checks for planners</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/CSS/checks.css" type="text/css"></link>


<script language="JavaScript" type="text/JavaScript">
$(document).ready(function(){
	$('.PAR').click(function () {
	  $the_parameter=$(this).attr("title");
	  $box_value=$("#box").attr("value");
	  if($box_value != undefined){
		 $result=$box_value + $the_parameter;
	  }else{
	  	$result=$the_parameter;
	  }
	  $('#box').val($result);
    });

    $('.OPER').click(function () {
	  $the_parameter=$(this).attr("title");
	  $box_value=$("#box").attr("value");
	  if($box_value != undefined){
		 $result=$box_value + $the_parameter;
	  }else{
	  	$result=$the_parameter;
	  }
	  $('#box').val($result);
    });
	
});

</script>

</head>
<body>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<table border="0" cellpadding="1" cellspacing="0" align="center">
<tr>
	<td class="header">PARAMETER</td>
	<td class="header" colspan="3">Options</td>
<tr>
	<td class="PARNAME">BAND (UMTS / GSM900 / GSM1800)</td>
	<td><DIV class="PAR" title="($_POST['type'] == 'GSM900')">GSM900</div></td>
	<td><DIV class="PAR" title="($_POST['type'] == 'GSM1800')">GSM1800</div></td>
	<td><DIV class="PAR" title="($_POST['type'] == 'UMTS')">UMTS</div></td>
</tr>
<tr>
	<td class="PARNAME">Battery backup</td>
	<td colspan="3"><DIV class="PAR" title="($_POST['pl_BBS == '')">BBS</div></td>
</tr>
<tr>
	<td class="PARNAME">Cabinet type</td>
	<td colspan="3"><DIV class="PAR" title="($_POST['pl_CABTYPE == '')">CABTYPE</div></td>
</table>
<br>

<table border="0" cellpadding="1" cellspacing="0" align="center">
<tr>
	<td class="header">PARAMETER</td>
	<td class="header" colspan="5">CAB1</td>
	<td class="header" colspan="5">CAB2</td>
	<td class="header" colspan="5">CAB3</td>
</tr>
<tr>
	<td class="PARNAME">Configuration</td>
	<td><DIV class="PAR" title="$_POST['pl_CONFIG_1'] ==''">SEC 1</div></td>
	<td><DIV class="PAR" title="$_POST['pl_CONFIG_2'] ==''">SEC 2</div></td>
	<td><DIV class="PAR" title="$_POST['pl_CONFIG_3'] ==''">SEC 3</div></td>
	<td><DIV class="PAR" title="$_POST['pl_CONFIG_4'] ==''">SEC 4</div></td>
	<td><DIV class="PAR" title="($_POST['pl_CONFIG_1'] == '' || $_POST['pl_CONFIG_2'] == '' || $_POST['pl_CONFIG_3'] == '' || $_POST['pl_CONFIG_4'] == '')">ALL 4 SEC</div></td>
</tr>
<tr>
	<td class="PARNAME">Antenna Type 1</td>
	<td><DIV class="PAR" title="$_POST['pl_ANTTYPE_1'] ==''">SEC 1</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ANTTYPE_2'] ==''">SEC 2</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ANTTYPE_3'] ==''">SEC 3</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ANTTYPE_4'] ==''">SEC 4</div></td>
	<td><DIV class="PAR" title="($_POST['pl_ANTTYPE_1'] == '' || $_POST['pl_ANTTYPE_2'] == '' || $_POST['pl_ANTTYPE_3'] == '' || $_POST['pl_ANTTYPE_4'] == '')">ALL 4 SEC</div></td>
</tr>
<tr>
	<td class="PARNAME">Antenna Type 2</td>
	<td><DIV class="PAR" title="$_POST['pl_ANTTYPE2_1'] == ''">SEC 1</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ANTTYPE2_2'] == ''">SEC 2</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ANTTYPE2_3'] == ''">SEC 3</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ANTTYPE2_4'] == ''">SEC 4</div></td>
	<td><DIV class="PAR" title="($_POST['pl_ANTTYPE2_1'] == '' || $_POST['pl_ANTTYPE2_2'] == '' || $_POST['pl_ANTTYPE2_3'] == '' || $_POST['pl_ANTTYPE2_4'] == '')">ALL 4 SEC</div></td>
</tr>
<tr>
	<td class="PARNAME">Electrical tilt 1</td>
	<td><DIV class="PAR" title="$_POST['pl_ELECTILT1_1 =''">SEC 1</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ELECTILT1_2 =''">SEC 2</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ELECTILT1_3 =''">SEC 3</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ELECTILT1_4 =''">SEC 4</div></td>
	<td><DIV class="PAR" title="($_POST['pl_ELECTILT1_1'] == '' || $_POST['pl_ELECTILT1_2'] == '' || $_POST['pl_ELECTILT1_3'] == '' || $_POST['pl_ELECTILT1_4'] == '')">ALL 4 SEC</div></td>
</tr>
<tr>
	<td class="PARNAME">Electrical tilt 2</td>
	<td><DIV class="PAR" title="$_POST['pl_ELECTILT2_1'] == ''">SEC 1</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ELECTILT2_2'] == ''">SEC 2</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ELECTILT2_3'] == ''">SEC 3</div></td>
	<td><DIV class="PAR" title="$_POST['pl_ELECTILT2_4'] == ''">SEC 4</div></td>
	<td><DIV class="PAR" title="($_POST['pl_ELECTILT2_1'] == '' || $_POST['pl_ELECTILT2_2'] == '' || $_POST['pl_ELECTILT2_3'] == '' || $_POST['pl_ELECTILT2_4'] == '')">ALL 4 SEC</div></td>
</tr>
<tr>
	<td class="PARNAME">TMA</td>
	<td><DIV class="PAR" title="$_POST['pl_TMA_1 =''">SEC 1</div></td>
	<td><DIV class="PAR" title="$_POST['pl_TMA_2 =''">SEC 2</div></td>
	<td><DIV class="PAR" title="$_POST['pl_TMA_3 =''">SEC 3</div></td>
	<td><DIV class="PAR" title="$_POST['pl_TMA_4 =''">SEC 4</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TMA_1'] == '' || $_POST['pl_TMA_2'] == '' || $_POST['pl_TMA_3'] == '' || $_POST['pl_TMA_4'] == '')">ALL 4 SEC</div></td>
</tr>
<tr>
	<td class="PARNAME">TRU TYPE & TRU INSTALLED</td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST1_1_1'] == '' && $_POST['pl_TRU_TYPE1_1_1'] == '')">SEC 1</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST1_1_2'] == '' && $_POST['pl_TRU_TYPE1_1_2'] == '')">SEC 2</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST1_1_3'] == '' && $_POST['pl_TRU_TYPE1_1_3'] == '')">SEC 3</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST1_1_4'] == '' && $_POST['pl_TRU_TYPE1_1_4'] == '')">SEC 4</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST1_1_1'] == '' && $_POST['pl_TRU_TYPE1_1_1'] == '') || ($_POST['pl_TRU_INST1_1_2'] == '' && $_POST['pl_TRU_TYPE1_1_2'] == '') || ($_POST['pl_TRU_INST1_1_3'] == '' && $_POST['pl_TRU_TYPE1_1_3'] == '') || ($_POST['pl_TRU_INST1_1_4'] == '' && $_POST['pl_TRU_TYPE1_1_4'] == '')">ALL 4 SEC</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST2_1_1'] == '' && $_POST['pl_TRU_TYPE2_1_1'] == '')">SEC 1</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST2_1_2'] == '' && $_POST['pl_TRU_TYPE2_1_2'] == '')">SEC 2</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST2_1_3'] == '' && $_POST['pl_TRU_TYPE2_1_3'] == '')">SEC 3</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST2_1_4'] == '' && $_POST['pl_TRU_TYPE2_1_4'] == '')">SEC 4</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST2_1_1'] == '' && $_POST['pl_TRU_TYPE2_1_1'] == '') || ($_POST['pl_TRU_INST2_1_2'] == '' && $_POST['pl_TRU_TYPE2_1_2'] == '') || ($_POST['pl_TRU_INST2_1_3'] == '' && $_POST['pl_TRU_TYPE2_1_3'] == '') || ($_POST['pl_TRU_INST2_1_4'] == '' && $_POST['pl_TRU_TYPE2_1_4'] == '')">ALL 4 SEC</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST3_1_1'] == '' && $_POST['pl_TRU_TYPE3_1_1'] == '')">SEC 1</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST3_1_2'] == '' && $_POST['pl_TRU_TYPE3_1_2'] == '')">SEC 2</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST3_1_3'] == '' && $_POST['pl_TRU_TYPE3_1_3'] == '')">SEC 3</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST3_1_4'] == '' && $_POST['pl_TRU_TYPE3_1_4'] == '')">SEC 4</div></td>
	<td><DIV class="PAR" title="($_POST['pl_TRU_INST3_1_1'] == '' && $_POST['pl_TRU_TYPE3_1_1'] == '') || ($_POST['pl_TRU_INST3_1_2'] == '' && $_POST['pl_TRU_TYPE3_1_2'] == '') || ($_POST['pl_TRU_INST3_1_3'] == '' && $_POST['pl_TRU_TYPE3_1_3'] == '') || ($_POST['pl_TRU_INST3_1_4'] == '' && $_POST['pl_TRU_TYPE3_1_4'] == '')">ALL 4 SEC</div></td>
</tr>
<tr>
	<td class="PARNAME">DXU type</td>
	<td colspan="5"><DIV class="PAR" title="$_POST['pl_DXUTYPE1'] =''">CAB 1</div></td>
	<td colspan="5"><DIV class="PAR" title="$_POST['pl_DXUTYPE2'] =''">CAB 2</div></td>
	<td colspan="5"><DIV class="PAR" title="$_POST['pl_DXUTYPE3'] =''">CAB 3</div></td>
</tr>
</table>
<br>
<table border="0" cellpadding="1" cellspacing="0" align="center">
<tr>
	<td><div class="OPER" title="==">EQUAL</div></td>
	<td><div class="OPER" title="!=">NOT EQUAL</div></td>
	<td><div class="OPER" title="<">SMALLER THAN</div></td>
	<td><div class="OPER" title=">">GREATER THAN</div></td>
	<td><div class="OPER" title="AND">AND</div></td>
	<td><div class="OPER" title="OR">OR</div></td>

</tr>
</table>
<br>


<center>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<input type="hidden" name="IDNR" VALue="<?=$IDNR?>">
<textarea cols="70" rows="7" id="box" name="RULE"></textarea><br>
<b>ERROR MESSAGE:</b>
<input type="text" name="ERROR" size="60"><?=$ERROR?></textarea><br>
<select name="TECHNOLOGY"><option>GSM900</option><option>GSM1800</option><option>GSM900,GSM1800</option><option>UMTS</option><option>GSM900,GSM1899,UMTS</option></select><br>
<? 	if ($IDNR!=""){
		$VAL_button="UPDATE";
		?><input type="submit" name="save" VALue="UPDATE FILTER"><?
		?><input type="submit" name="save" VALue="SAVE AS NEW FILTER"><?
	}else{
		?><input type="submit" name="save" VALue="SAVE FILTER"><?
	}
?>
</form>
</center>

<?
/*
$str='($_POST["pl_CABTYPE"] == "RBS3106") or ($_POST["pl_CABTYPE"] == "RBS3301") or ($_POST["pl_CABTYPE"] == "testnodeB") or ($_POST["pl_CABTYPE"] == "Node HS")';
$str= '($_POST["type"] == "GSM900") AND ((($_POST["pl_TRU_INST1_1_1"] + $_POST["pl_TRU_INST2_1_1"] + $_POST["pl_TRU_INST3_1_1"]) >4) OR (($_POST["pl_TRU_INST1_1_2"] + $_POST["pl_TRU_INST2_1_2"] + $_POST["pl_TRU_INST3_1_2"] ) >4) OR (($_POST["pl_TRU_INST1_1_3"] + $_POST["pl_TRU_INST2_1_3"] + $_POST["pl_TRU_INST3_1_3"]) >4))';

$str=str_replace(") or (","<br>or<br>",$str);
$str=str_replace(") OR (","<br>or<br>",$str);
$str=str_replace(") and (","<br>and<br>",$str);
$str=str_replace(") AND (","<br>AND<br>",$str);
$str=str_replace("(","&nbsp;&nbsp;&nbsp;&nbsp;",$str);

echo $str;
*/
?>
<br><br>
<table cellpadding="1" border="0" cellspacing="1" width="90%" align="center">
<?
	$query="SELECT * FROM INFOBASE.BSDS_VALIDATION ORDER BY IDNR";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
	    OCIFreeStatement($stmt);
	}
	//echo "$what => $query<br>";
	foreach ($res1['IDNR'] as $key=>$attrib_id) {
		echo "
			<tr>
				<td class='PARNAME'><b>".$res1['IDNR'][$key].":</b></td>
				<td class='table_rule'>".$res1['RULE'][$key]."</td>
				<td class='error_message'>".$res1['ERROR'][$key]."</td>
				<td class='table_active'>".$res1['ACTIVE'][$key]."</td>
				<td class='table_techno'>".$res1['TECHNOLOGY'][$key]."</td>
				<td>&nbsp;&nbsp;<a href='".$_SERVER['PHP_SELF']."?action=delete&IDNR=".$res1['IDNR'][$key]."'><img src='".$config['sitepath_url']."/images/icons/del.png' border=0></a></td>
		    </tr>";
	}
?>
</table>
</body>
</html>

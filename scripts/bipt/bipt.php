<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
error_reporting(E_ALL ^E_NOTICE);
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
require_once("../procedures/cur_plan_procedures.php");
?>
<html>
<head>
<title>BSDS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/CSS/bsds_bipt.css" type="text/css"></link>
<? require_once("../js_css_includes.php"); ?>
</head>
<body>
<?

//error_reporting(E_ALL);
include("bipt_stuff.php");

/************ START OUTPUT *********************/
?>
<div id="biptcontent">
<table width="100%">
<tr>
<td align="center">

	<form action="bipt_save.php" name="test" method="post">
	<input name='datetime' type='hidden' value="<? echo "$datetime"; ?>">
	<input name='sitekey' type='hidden' value="<? echo "$sitekey"; ?>">

	<table  cellpadding='0' cellspacing='0' border=1 bordercolor="black">
	<tr>
		<td bgcolor='lightblue'>Lambert coordinate</td>
		<td bgcolor='lightblue'>
			<table width="100%" border=0>
			<tr>
			<td bgcolor='lightblue'>XLambert: <INPUT TYPE='text' NAME='XCoordinateSite' VALUE="<? echo $coor[longitude]; ?>"></td>
			<td bgcolor='lightblue'>YLambert: <INPUT TYPE='text' NAME='YCoordinateSite' VALUE="<? echo $coor[latitude]; ?>"></td>
			</tr>
			</table>
		<td bgcolor='lightblue'>LS04<br>LS05 </td>
	</tr>
	</table>

	<br>

	<table cellpadding='0' cellspacing='0' border=1 bordercolor="black">

	<?


	$i=0;
	/******************************************************  GSM1800 SEC1  ****************************************************************/
	if ($GSM1800_ANTTYPE1_1!='' && $GSM1800_ANTTYPE1_1!='' && $GSM1800_ANTTYPE1_1!='None' && $GSM1800_ANTTYPE1_1!='Unknown'){
	$i++;
	?>
	<INPUT TYPE='hidden' NAME='AntennaNumberOnPlan1' value="1">

	<tr>
		<td><INPUT TYPE='hidden' NAME='AntennaNumber1' value='1'><?=$i?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaDescription1A' value='1A'>1A</td>
		<td><INPUT TYPE='hidden' NAME='AntennaType1' VALUE="<?=$GSM1800_ANTTYPE1_1?>"><?=$GSM1800_ANTTYPE1_1?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight1' VALUE="<?=$GSM1800_ANTHEIGHT1_1?>"><?=$GSM1800_ANTHEIGHT1_1?></td>
		<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt1' VALUE="-<?=$GSM1800_ELECTILT1_1?>">-<?=$GSM1800_ELECTILT1_1?></td>-->
		<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt1' VALUE="<?=$GSM1800_MECHTILT_DIR1_1?><?=$GSM1800_MECHTILT1_1?>"><?=$GSM1800_MECHTILT_DIR1_1?><?=$GSM1800_MECHTILT1_1?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaAzimut1' VALUE="<?=$GSM1800_AZI_1?>"><?=$GSM1800_AZI_1?></td>
		<td><INPUT TYPE='hidden' NAME='Frequency1' VALUE="1800">1800</td>
		<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters1' VALUE="<?=$GSM1800_FREQ_ACTIVE_1?>" size="5"></td>
	</tr>
	<?

		if ($GSM1800_ANTTYPE2_1!='' && $GSM1800_ANTTYPE2_1!='-' && $GSM1800_ANTTYPE2_1!='None' && $GSM1800_ANTTYPE2_1!='Unknown'){
		$i++;
	?>
		<tr>
			<td><INPUT TYPE='hidden' NAME='AntennaNumber2' value='2'><?=$i?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaDescription1B' value='1B'>1B</td>
			<td><INPUT TYPE='hidden' NAME='AntennaType1_2' VALUE="<?=$GSM1800_ANTTYPE2_1?>"><?=$GSM1800_ANTTYPE2_1?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight1_2' VALUE="<?=$GSM1800_ANTHEIGHT2_1?>"><?=$GSM1800_ANTHEIGHT2_1?></td>
			<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt1_2' VALUE="-<?=$GSM1800_ELECTILT2_1?>">-<?=$GSM1800_ELECTILT2_1?></td>-->
			<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt1_2' VALUE="<?=$GSM1800_MECHTILT_DIR2_1?><?=$GSM1800_MECHTILT2_1?>"><?=$GSM1800_MECHTILT_DIR2_1?><?=$GSM1800_MECHTILT2_1?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaAzimut1_2' VALUE="<?=$GSM1800_AZI_1?>"><?=$GSM1800_AZI_1?></td>
			<td><INPUT TYPE='hidden' NAME='Frequency1_2' VALUE="1800">1800</td>
			<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters1_2' VALUE='0'  size="5"></td>
		</tr>
	<?
		}
	}

	/******************************************************  GSM1800 SEC2  ****************************************************************/
	if ($GSM1800_ANTTYPE1_2!='-' && $GSM1800_ANTTYPE1_2!='' && $GSM1800_ANTTYPE1_2!='None' && $GSM1800_ANTTYPE1_2!='Unknown'){
	$i++;
	?>
	<INPUT TYPE='hidden' NAME='AntennaNumberOnPlan2' value="2">

	<tr>
		<td><INPUT TYPE='hidden' NAME='AntennaNumber3' value='3'><?=$i?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaDescription2A' value='2A'>2A</td>
		<td><INPUT TYPE='hidden' NAME='AntennaType2' VALUE="<?=$GSM1800_ANTTYPE1_2?>"><?=$GSM1800_ANTTYPE1_2?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight2' VALUE="<?=$GSM1800_ANTHEIGHT1_2?>"><?=$GSM1800_ANTHEIGHT1_2?></td>
		<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt2' VALUE="-<?=$GSM1800_ELECTILT1_2?>">-<?=$GSM1800_ELECTILT1_2?></td>-->
		<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt2' VALUE="<?=$GSM1800_MECHTILT_DIR1_2?><?=$GSM1800_MECHTILT1_2?>"><?=$GSM1800_MECHTILT_DIR1_2?><?=$GSM1800_MECHTILT1_2?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaAzimut2' VALUE="<?=$GSM1800_AZI_2?>"><?=$GSM1800_AZI_2?></td>
		<td><INPUT TYPE='hidden' NAME='Frequency2' VALUE="1800">1800</td>
		<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters2' VALUE="<?=$GSM1800_FREQ_ACTIVE_2?>" size="5"></td>
	</tr>
	<?

		if ($GSM1800_ANTTYPE2_2!='-' && $GSM1800_ANTTYPE2_2!='' && $GSM1800_ANTTYPE2_2!='None' && $GSM1800_ANTTYPE2_2!='Unknown'){
		$i++;
	?>
		<tr>
			<td><INPUT TYPE='hidden' NAME='AntennaNumber4' value='4'><?=$i?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaDescription2B' value='2B'>2B</td>
			<td><INPUT TYPE='hidden' NAME='AntennaType2_2' VALUE="<?=$GSM1800_ANTTYPE2_2?>"><?=$GSM1800_ANTTYPE2_2?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight2_2' VALUE="<?=$GSM1800_ANTHEIGHT2_2?>"><?=$GSM1800_ANTHEIGHT2_2?></td>
			<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt2_2' VALUE="-<?=$GSM1800_ELECTILT2_2?>">-<?=$GSM1800_ELECTILT2_2?></td>-->
			<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt2_2' VALUE="<?=$GSM1800_MECHTILT_DIR2_2?><?=$GSM1800_MECHTILT2_2?>"><?=$GSM1800_MECHTILT_DIR2_2?><?=$GSM1800_MECHTILT2_2?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaAzimut2_2' VALUE="<?=$GSM1800_AZI_2?>"><?=$GSM1800_AZI_2?></td>
			<td><INPUT TYPE='hidden' NAME='Frequency2_2' VALUE="1800">1800</td>
			<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters2_2' VALUE='0'  size="5"></td>
		</tr>
	<?
		}
	}

	/******************************************************  GSM1800 SEC3  ****************************************************************/
	if ($GSM1800_ANTTYPE1_3!='' && $GSM1800_ANTTYPE1_3!='-' && $GSM1800_ANTTYPE1_3!='None' && $GSM1800_ANTTYPE1_3!='Unknown'){
	$i++;
	?>
	<INPUT TYPE='hidden' NAME='AntennaNumberOnPlan3' value="3">

	<tr>
		<td><INPUT TYPE='hidden' NAME='AntennaNumber5' value='5'><?=$i?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaDescription3A' value='3A'>3A</td>
		<td><INPUT TYPE='hidden' NAME='AntennaType3' VALUE="<?=$GSM1800_ANTTYPE1_3?>"><?=$GSM1800_ANTTYPE1_3?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight3' VALUE="<?=$GSM1800_ANTHEIGHT1_3?>"><?=$GSM1800_ANTHEIGHT1_3?></td>
		<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt3' VALUE="-<?=$GSM1800_ELECTILT1_3?>">-<?=$GSM1800_ELECTILT1_3?></td>-->
		<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt3' VALUE="<?=$GSM1800_MECHTILT_DIR1_3?><?=$GSM1800_MECHTILT1_3?>"><?=$GSM1800_MECHTILT_DIR1_3?><?=$GSM1800_MECHTILT1_3?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaAzimut3' VALUE="<?=$GSM1800_AZI_3?>"><?=$GSM1800_AZI_3?></td>
		<td><INPUT TYPE='hidden' NAME='Frequency3' VALUE="1800">1800</td>
		<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters3' VALUE="<?=$GSM1800_FREQ_ACTIVE_3?>" size="5"></td>
	</tr>
	<?

		if ($GSM1800_ANTTYPE2_3!='-' && $GSM1800_ANTTYPE2_3!='' && $GSM1800_ANTTYPE2_3!='None' && $GSM1800_ANTTYPE2_3!='Unknown'){
		$i++;
	?>
		<tr>
			<td><INPUT TYPE='hidden' NAME='AntennaNumber6' value='6'><?=$i?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaDescription3B' value='3B'>3B</td>
			<td><INPUT TYPE='hidden' NAME='AntennaType3_2' VALUE="<?=$GSM1800_ANTTYPE2_3?>"><?=$GSM1800_ANTTYPE2_3?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight3_2' VALUE="<?=$GSM1800_ANTHEIGHT2_3?>"><?=$GSM1800_ANTHEIGHT2_3?></td>
			<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt3_2' VALUE="-<?=$GSM1800_ELECTILT2_3?>">-<?=$GSM1800_ELECTILT2_3?></td>-->
			<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt3_2' VALUE="<?=$GSM1800_MECHTILT_DIR2_3?><?=$GSM1800_MECHTILT2_3?>"><?=$GSM1800_MECHTILT_DIR2_3?><?=$GSM1800_MECHTILT2_3?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaAzimut3_2' VALUE="<?=$GSM1800_AZI_3?>"><?=$GSM1800_AZI_3?></td>
			<td><INPUT TYPE='hidden' NAME='Frequency3_2' VALUE="1800">1800</td>
			<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters3_2' VALUE='0'  size="5"></td>
		</tr>
	<?
		}
	}

	/*************************************************************************************************************************************/
	/*************************************************************************************************************************************/
	/*************************************************************************************************************************************/
	?>
	<tr>
		<td colspan="9"></td>
	</tr>
	<?
	/******************************************************  GSM900 SEC1  ****************************************************************/
	 if ($GSM900_ANTTYPE1_1!='' && $GSM900_ANTTYPE1_1!='None' && $GSM900_ANTTYPE1_1!='-' && $GSM900_ANTTYPE1_1!='Unknown'){
	$i++;
	?>
	<INPUT TYPE='hidden' NAME='AntennaNumberOnPlan4' value="4">

	<tr>
		<td><INPUT TYPE='hidden' NAME='AntennaNumber7' value='7'><?=$i?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaDescription4A' value='4A'>4A</td>
		<td><INPUT TYPE='hidden' NAME='AntennaType4' VALUE="<?=$GSM900_ANTTYPE1_1?>"><?=$GSM900_ANTTYPE1_1?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight4' VALUE="<?=$GSM900_ANTHEIGHT1_1?>"><?=$GSM900_ANTHEIGHT1_1?></td>
		<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt4' VALUE="-<?=$GSM900_ELECTILT1_1?>">-<?=$GSM900_ELECTILT1_1?></td>-->
		<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt4' VALUE="<?=$GSM900_MECHTILT_DIR1_1?><?=$GSM900_MECHTILT1_1?>"><?=$GSM900_MECHTILT_DIR1_1?><?=$GSM900_MECHTILT1_1?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaAzimut4' VALUE="<?=$GSM900_AZI_1?>"><?=$GSM900_AZI_1?></td>
		<td><INPUT TYPE='hidden' NAME='Frequency4' VALUE="900">900</td>
		<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters4' VALUE="<?=$GSM900_FREQ_ACTIVE_1?>" size="5"></td>
	</tr>
	<?

		if ($GSM900_ANTTYPE2_1!='-' && $GSM900_ANTTYPE2_1!='' && $GSM900_ANTTYPE2_1!='None' && $GSM900_ANTTYPE2_1!='Unknown'){
		$i++;
	?>
		<tr>
			<td><INPUT TYPE='hidden' NAME='AntennaNumber8' value='8'><?=$i?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaDescription4B' value='4B'>4B</td>
			<td><INPUT TYPE='hidden' NAME='AntennaType4_2' VALUE="<?=$GSM900_ANTTYPE2_1?>"><?=$GSM900_ANTTYPE2_1?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight4_2' VALUE="<?=$GSM900_ANTHEIGHT2_1?>"><?=$GSM900_ANTHEIGHT2_1?></td>
			<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt1_2' VALUE="-<?=$GSM900_ELECTILT2_1?>">-<?=$GSM900_ELECTILT2_1?></td>-->
			<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt4_2' VALUE="<?=$GSM900_MECHTILT_DIR2_1?><?=$GSM900_MECHTILT2_1?>"><?=$GSM900_MECHTILT_DIR2_1?><?=$GSM900_MECHTILT2_1?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaAzimut4_2' VALUE="<?=$GSM900_AZI_1?>"><?=$GSM900_AZI_1?></td>
			<td><INPUT TYPE='hidden' NAME='Frequency4_2' VALUE="900">900</td>
			<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters4_2' VALUE='0'  size="5"></td>
		</tr>
	<?
		}
	}

	/******************************************************  GSM900 SEC2  ****************************************************************/
	if ($GSM900_ANTTYPE1_2!='' && $GSM900_ANTTYPE1_2!='-' && $GSM900_ANTTYPE1_2!='None' && $GSM900_ANTTYPE1_2!='Unknown'){
	$i++;
	?>
	<INPUT TYPE='hidden' NAME='AntennaNumberOnPlan5' value="5">

	<tr>
		<td><INPUT TYPE='hidden' NAME='AntennaNumber9' value='9'><?=$i?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaDescription5A' value='5A'>5A</td>
		<td><INPUT TYPE='hidden' NAME='AntennaType5' VALUE="<?=$GSM900_ANTTYPE1_2?>"><?=$GSM900_ANTTYPE1_2?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight5' VALUE="<?=$GSM900_ANTHEIGHT1_2?>"><?=$GSM900_ANTHEIGHT1_2?></td>
		<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt5' VALUE="-<?=$GSM900_ELECTILT1_2?>">-<?=$GSM900_ELECTILT1_2?></td>-->
		<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt5' VALUE="<?=$GSM900_MECHTILT_DIR1_2?><?=$GSM900_MECHTILT1_2?>"><?=$GSM900_MECHTILT_DIR1_2?><?=$GSM900_MECHTILT1_2?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaAzimut5' VALUE="<?=$GSM900_AZI_2?>"><?=$GSM900_AZI_2?></td>
		<td><INPUT TYPE='hidden' NAME='Frequency5' VALUE="900">900</td>
		<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters5' VALUE="<?=$GSM900_FREQ_ACTIVE_2?>" size="5"></td>
	</tr>
	<?

		if ($GSM900_ANTTYPE2_2!='-' && $GSM900_ANTTYPE2_2!='' && $GSM900_ANTTYPE2_2!='None' && $GSM900_ANTTYPE2_2!='Unknown'){
		$i++;
	?>
		<tr>
			<td><INPUT TYPE='hidden' NAME='AntennaNumber10' value='10'><?=$i?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaDescription5B' value='5B'>5B</td>
			<td><INPUT TYPE='hidden' NAME='AntennaType5_2' VALUE="<?=$GSM900_ANTTYPE2_2?>"><?=$GSM900_ANTTYPE2_2?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight5_2' VALUE="<?=$GSM900_ANTHEIGHT2_2?>"><?=$GSM900_ANTHEIGHT2_2?></td>
			<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt5_2' VALUE="-<?=$GSM900_ELECTILT2_2?>">-<?=$GSM900_ELECTILT2_2?></td>-->
			<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt5_2' VALUE="<?=$GSM900_MECHTILT_DIR2_2?><?=$GSM900_MECHTILT2_2?>"><?=$GSM900_MECHTILT_DIR2_2?><?=$GSM900_MECHTILT2_2?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaAzimut5_2' VALUE="<?=$GSM900_AZI_2?>"><?=$GSM900_AZI_2?></td>
			<td><INPUT TYPE='hidden' NAME='Frequency5_2' VALUE="900">900</td>
			<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters5_2' VALUE='0'  size="5"></td>
		</tr>
	<?
		}
	}

	/******************************************************  GSM900 SEC3  ****************************************************************/
	if ($GSM900_ANTTYPE1_3!='' && $GSM900_ANTTYPE1_3!='-' && $GSM900_ANTTYPE1_3!='None' && $GSM900_ANTTYPE1_3!='Unknown'){
	$i++;
	?>
	<INPUT TYPE='hidden' NAME='AntennaNumberOnPlan6' value="6">

	<tr>
		<td><INPUT TYPE='hidden' NAME='AntennaNumber11' value='11'><?=$i?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaDescription6A' value='6A'>6A</td>
		<td><INPUT TYPE='hidden' NAME='AntennaType6' VALUE="<?=$GSM900_ANTTYPE1_3?>"><?=$GSM900_ANTTYPE1_3?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight6' VALUE="<?=$GSM900_ANTHEIGHT1_3?>"><?=$GSM900_ANTHEIGHT1_3?></td>
		<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt6' VALUE="-<?=$GSM900_ELECTILT1_3?>">-<?=$GSM900_ELECTILT1_3?></td>-->
		<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt6' VALUE="<?=$GSM900_MECHTILT_DIR1_3?><?=$GSM900_MECHTILT1_3?>"><?=$GSM900_MECHTILT_DIR1_3?><?=$GSM900_MECHTILT1_3?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaAzimut6' VALUE="<?=$GSM900_AZI_3?>"><?=$GSM900_AZI_3?></td>
		<td><INPUT TYPE='hidden' NAME='Frequency6' VALUE="900">900</td>
		<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters6' VALUE="<?=$GSM900_FREQ_ACTIVE_3?>" size="5"></td>
	</tr>
	<?

		if ($GSM900_ANTTYPE2_3!='-' && $GSM900_ANTTYPE2_3!='' && $GSM900_ANTTYPE2_3!='None' && $GSM900_ANTTYPE2_3!='Unknown'){
		$i++;
	?>
		<tr>
			<td><INPUT TYPE='hidden' NAME='AntennaNumber12' value='12'><?=$i?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaDescription6B' value='6B'>6B</td>
			<td><INPUT TYPE='hidden' NAME='AntennaType6_2' VALUE="<?=$GSM900_ANTTYPE2_3?>"><?=$GSM900_ANTTYPE2_3?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight6_2' VALUE="<?=$GSM900_ANTHEIGHT2_3?>"><?=$GSM900_ANTHEIGHT2_3?></td>
			<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt6_2' VALUE="-<?=$GSM900_ELECTILT2_3?>">-<?=$GSM900_ELECTILT2_3?></td>-->
			<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt1_2' VALUE="<?=$GSM900_MECHTILT_DIR2_3?><?=$GSM900_MECHTILT2_3?>"><?=$GSM900_MECHTILT_DIR2_3?><?=$GSM900_MECHTILT2_3?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaAzimut6_2' VALUE="<?=$GSM900_AZI_3?>"><?=$GSM900_AZI_3?></td>
			<td><INPUT TYPE='hidden' NAME='Frequency6_2' VALUE="900">900</td>
			<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters6_2' VALUE='0'  size="5"></td>
		</tr>
	<?
		}
	}

	/*************************************************************************************************************************************/
	/*************************************************************************************************************************************/
	/*************************************************************************************************************************************/
	?>
	<tr>
		<td colspan="9"></td>
	</tr>
	<?
	/******************************************************  UMTS SEC1  ****************************************************************/
	//echo "$UMTS_ANTTYPE1_1!='' && $UMTS_ANTTYPE1_1!='-' && $UMTS_ANTTYPE1_1!='None' && $UMTS_ANTTYPE1_1!='Unknown'";
	if ($UMTS_ANTTYPE1_1!='' && $UMTS_ANTTYPE1_1!='-' && $UMTS_ANTTYPE1_1!='None' && $UMTS_ANTTYPE1_1!='Unknown'){
	$i++;
	?>
	<INPUT TYPE='hidden' NAME='AntennaNumberOnPlan7' value="7">

	<tr>
		<td><INPUT TYPE='hidden' NAME='AntennaNumber13' value='13'><?=$i?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaDescription7A' value='7A'>7A</td>
		<td><INPUT TYPE='hidden' NAME='AntennaType7' VALUE="<?=$UMTS_ANTTYPE1_1?>"><?=$UMTS_ANTTYPE1_1?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight7' VALUE="<?=$UMTS_ANTHEIGHT1_1?>"><?=$UMTS_ANTHEIGHT1_1?></td>
		<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt7' VALUE="-<?=$UMTS_ELECTILT1_1?>">-<?=$UMTS_ELECTILT1_1?></td>-->
		<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt7' VALUE="<?=$UMTS_MECHTILT_DIR1_1?><?=$UMTS_MECHTILT1_1?>"><?=$UMTS_MECHTILT_DIR1_1?><?=$UMTS_MECHTILT1_1?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaAzimut7' VALUE="<?=$UMTS_AZI_1?>"><?=$UMTS_AZI_1?></td>
		<td><INPUT TYPE='hidden' NAME='Frequency7' VALUE="2100">2100</td>
		<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters7' VALUE="<?=$UMTS_FREQ_ACTIVE_1?>" size="5"></td>
	</tr>
	<?

		if ($UMTS_ANTTYPE2_1!='-' && $UMTS_ANTTYPE2_1!='None' && $UMTS_ANTTYPE2_1!='' && $UMTS_ANTTYPE2_1!='Unknown'){
		$i++;
	?>
		<tr>
			<td><INPUT TYPE='hidden' NAME='AntennaNumber14' value='14'><?=$i?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaDescription7B' value='7B'>7B</td>
			<td><INPUT TYPE='hidden' NAME='AntennaType7_2' VALUE="<?=$UMTS_ANTTYPE2_1?>"><?=$UMTS_ANTTYPE2_1?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight7_2' VALUE="<?=$UMTS_ANTHEIGHT2_1?>"><?=$UMTS_ANTHEIGHT2_1?></td>
			<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt7_2' VALUE="-<?=$UMTS_ELECTILT2_1?>">-<?=$UMTS_ELECTILT2_1?></td>-->
			<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt7_2' VALUE="<?=$UMTS_MECHTILT_DIR2_1?><?=$UMTS_MECHTILT2_1?>"><?=$UMTS_MECHTILT_DIR2_1?><?=$UMTS_MECHTILT2_1?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaAzimut7_2' VALUE="<?=$UMTS_AZI_1?>"><?=$UMTS_AZI_1?></td>
			<td><INPUT TYPE='hidden' NAME='Frequency7_2' VALUE="2100">2100</td>
			<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters7_2' VALUE='0'  size="5"></td>
		</tr>
	<?
		}
	}

	/******************************************************  UMTS SEC2  ****************************************************************/
	if ($UMTS_ANTTYPE1_2!='' && $UMTS_ANTTYPE1_2!='None' && $UMTS_ANTTYPE1_2!='-' && $UMTS_ANTTYPE1_2!='Unknown'){
	$i++;
	?>
	<INPUT TYPE='hidden' NAME='AntennaNumberOnPlan8' value="8">

	<tr>
		<td><INPUT TYPE='hidden' NAME='AntennaNumber15' value='15'><?=$i?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaDescription8A' value='8A'>8A</td>
		<td><INPUT TYPE='hidden' NAME='AntennaType8' VALUE="<?=$UMTS_ANTTYPE1_2?>"><?=$UMTS_ANTTYPE1_2?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight8' VALUE="<?=$UMTS_ANTHEIGHT1_2?>"><?=$UMTS_ANTHEIGHT1_2?></td>
		<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt8' VALUE="-<?=$UMTS_ELECTILT1_2?>">-<?=$UMTS_ELECTILT1_2?></td>-->
		<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt8' VALUE="<?=$UMTS_MECHTILT_DIR1_2?><?=$UMTS_MECHTILT1_2?>"><?=$UMTS_MECHTILT_DIR1_2?><?=$UMTS_MECHTILT1_2?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaAzimut8' VALUE="<?=$UMTS_AZI_2?>"><?=$UMTS_AZI_2?></td>
		<td><INPUT TYPE='hidden' NAME='Frequency8' VALUE="2100">2100</td>
		<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters8' VALUE="<?=$UMTS_FREQ_ACTIVE_2?>" size="5"></td>
	</tr>
	<?

		if ($UMTS_ANTTYPE2_2!='' && $UMTS_ANTTYPE2_2!='None' && $UMTS_ANTTYPE2_2!='-' && $UMTS_ANTTYPE2_2!='Unknown'){
		$i++;
	?>
		<tr>
			<td><INPUT TYPE='hidden' NAME='AntennaNumber16' value='16'><?=$i?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaDescription8B' value='8B'>8B</td>
			<td><INPUT TYPE='hidden' NAME='AntennaType8_2' VALUE="<?=$UMTS_ANTTYPE2_2?>"><?=$UMTS_ANTTYPE2_2?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight8_2' VALUE="<?=$UMTS_ANTHEIGHT2_2?>"><?=$UMTS_ANTHEIGHT2_2?></td>
			<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt8_2' VALUE="-<?=$UMTS_ELECTILT2_2?>">-<?=$UMTS_ELECTILT2_2?></td>-->
			<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt8_2' VALUE="<?=$UMTS_MECHTILT_DIR2_2?><?=$UMTS_MECHTILT2_2?>"><?=$UMTS_MECHTILT_DIR2_2?><?=$UMTS_MECHTILT2_2?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaAzimut8_2' VALUE="<?=$UMTS_AZI_2?>"><?=$UMTS_AZI_2?></td>
			<td><INPUT TYPE='hidden' NAME='Frequency8_2' VALUE="2100">2100</td>
			<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters8_2' VALUE='0'  size="5"></td>
		</tr>
	<?
		}
	}

	/******************************************************  UMTS SEC3  ****************************************************************/
	if ($UMTS_ANTTYPE1_3!='' && $UMTS_ANTTYPE1_3!='None' && $UMTS_ANTTYPE1_3!='-' && $UMTS_ANTTYPE1_3!='Unknown'){
	$i++;
	?>
	<INPUT TYPE='hidden' NAME='AntennaNumberOnPlan9' value="9">

	<tr>
		<td><INPUT TYPE='hidden' NAME='AntennaNumber17' value='17'><?=$i?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaDescription9A' value='9A'>9A</td>
		<td><INPUT TYPE='hidden' NAME='AntennaType9' VALUE="<?=$UMTS_ANTTYPE1_3?>"><?=$UMTS_ANTTYPE1_3?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight9' VALUE="<?=$UMTS_ANTHEIGHT1_3?>"><?=$UMTS_ANTHEIGHT1_3?></td>
		<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt9' VALUE="-<?=$UMTS_ELECTILT1_3?>">-<?=$UMTS_ELECTILT1_3?></td>-->
		<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt9' VALUE="<?=$UMTS_MECHTILT_DIR1_3?><?=$UMTS_MECHTILT1_3?>"><?=$UMTS_MECHTILT_DIR1_3?><?=$UMTS_MECHTILT1_3?></td>
		<td><INPUT TYPE='hidden' NAME='AntennaAzimut9' VALUE="<?=$UMTS_AZI_3?>"><?=$UMTS_AZI_3?></td>
		<td><INPUT TYPE='hidden' NAME='Frequency9' VALUE="2100">2100</td>
		<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters9' VALUE="<?=$UMTS_FREQ_ACTIVE_3?>" size="5"></td>
	</tr>
	<?
		if ($UMTS_ANTTYPE2_3!='' && $UMTS_ANTTYPE2_3!='None'  && $UMTS_ANTTYPE2_3!='-' && $UMTS_ANTTYPE2_3!='Unknown'){
		$i++;
	?>
		<tr>
			<td><INPUT TYPE='hidden' NAME='AntennaNumber18' value='18'><?=$i?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaDescription9B' value='9B'>9B</td>
			<td><INPUT TYPE='hidden' NAME='AntennaType9_2' VALUE="<?=$UMTS_ANTTYPE2_3?>"><?=$UMTS_ANTTYPE2_3?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaPositionHeight9_2' VALUE="<?=$UMTS_ANTHEIGHT2_3?>"><?=$UMTS_ANTHEIGHT2_3?></td>
			<!--<td><INPUT TYPE='hidden' NAME='AntennaElectricalTilt9_2' VALUE="-<?=$UMTS_ELECTILT2_3?>">-<?=$UMTS_ELECTILT2_3?></td>-->
			<td><INPUT TYPE='hidden' NAME='AntennaMechanicalTilt9_2' VALUE="<?=$UMTS_MECHTILT_DIR2_3?><?=$UMTS_MECHTILT2_3?>"><?=$UMTS_MECHTILT_DIR2_3?><?=$UMTS_MECHTILT2_3?></td>
			<td><INPUT TYPE='hidden' NAME='AntennaAzimut9_2' VALUE="<?=$UMTS_AZI_3?>"><?=$UMTS_AZI_3?></td>
			<td><INPUT TYPE='hidden' NAME='Frequency9_2' VALUE="2100">2100</td>
			<td><INPUT TYPE='text' NAME='AntennaNumberOfTransmitters9_2' VALUE='0'  size="5"></td>
		</tr>
	<?
		}
	}

	?>
	</table>
	<br>
	<input type="hidden" name="amount_send" value="<?=$i?>">
	<input type="submit" value="Save BIPT-file as CSV">
	</form>

</td>
</tr>
</table>
</div>
</body>
</html>
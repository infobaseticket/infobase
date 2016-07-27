<?PHP
require_once("../../../include/config.php");
require_once($config['phpguarddog_path']."/guard.php");
protect("","BSDS_admin","");
require_once($config['sitepath_url']."/include/PHP/oci8_funcs.php");
include("../procedures/cur_plan_procedures.php");

$_SESSION['VIEW']="CHECKS_UMTS";

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);

?>
<html>
<head>
<title>BSDS - Administartion of checks for planners</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/CSS/checks.css" type="text/css"></link>
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/CSS/bsds.css" type="text/css"></link>
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/CSS/tabbed_menu.css" type="text/css"></link>
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/CSS/bsds_displaymessages.css" type="text/css"></link>

<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery.js"></script>
</head>
<body>
<div id="navigation">
<?
include("../navigation/BSDS_nav.php");
?>
</div>

<?

if ($_POST['action']=="save"){
	$testing="yes";
	$type=$_POST['type'];
	require_once("checks_proc.php");
	
	if ($ERROR_MESSAGE2){
	?>
	<script>
	  $(document).ready(function(){
	       $("#warning")
	      .fadeIn('slow')
	      .animate({opacity: 1.0}, 3000)
		  });
	</script>
	<div id="warning"><?=$ERROR_MESSAGE2?></div>
	<? } 
}
?>
<br>
<table cellpadding="0" cellspacing="0"  border="0" width="100%">
<tr valign="top" align="center">
  <td align='center'>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<input type="hidden" name="action" value="save">
	<input type="hidden" name="pl_type" value="UMTS"> 
	<input type="hidden" name="cab" value="01">
	 <?
		$colspan2=3;
		$colspan=8;
	 ?>
	 <table border="0" bordercolor="lighblue" cellpadding="0" cellspacing="1" align="center" width="98%">
	 <tr>
	 	<td colspan="<?=$colspan?>" bgcolor="Black" height="2px">&nbsp;</td>
	 </tr>
	 <tr>
		<td class="table_head">&nbsp;</td>
		<td colspan="<?=$colspan2?>">
				<table width="100%" height="100%" border="0" bordercolor="lighblue" cellpadding="1" align="center" cellspacing="1">
				<tr>
					<td class="parameter_name">Cabinet</td>
					<td class=""<?=$color3[4][1]?>"></td>
					<td class="parameter_name">IP-B</td>
					<td class="<?=$color9[4][1]?>"><input type="text" name="pl_IPB" value="<?=$pl_IPB?>" class="<?=$color9[4][3]?>" size="10"></td>
				</tr>
				<tr>
					<td class="parameter_name">2 MBPS</td>
					<TD class="<?=$color1[4][1]?>"><SELECT NAME="pl_MBPS" class="<?=$color1[4][2]?>"><? get_select_numbers($pl_MBPS,"1","8","1",'no'); ?></td>
					<td class="parameter_name">Cabinettype</td>
					<TD class="<?=$color2[4][1]?>"><SELECT NAME="pl_CABTYPE" class="<?=$color2[4][2]?>"><? get_select_LOGNODES($pl_CABTYPE); ?></td>
				</tr>
				<tr>
					<td class="parameter_name">Power supply</td>
					<td class="<?=$color3[4][1]?>"><SELECT NAME="pl_POWERSUP" class="<?=$color3[4][2]?>"><option SELECTED><?=$pl_POWERSUP?><option>ACTURA CS</option><option>NONE</option></td>
					<td class="parameter_name">PSU amount</td>
					<TD class="<?=$color4[4][1]?>"><SELECT NAME="pl_PSU" class="<?=$color4[4][2]?>"><? get_select_numbers($pl_PSU,0,6,1,'no');?></td>
				</tr>
				<tr>
					<td class="parameter_name">TX-B HW installed</td>
					<td class="<?=$color5[4][1]?>"><SELECT NAME="pl_TXBHW" class="<?=$color5[4][2]?>"><? get_select_TXBHW($pl_TXBHW); ?></td>
					<td class="parameter_name">TX-B SW installed</td>
					<TD class="<?=$color6[4][1]?>"><SELECT NAME="pl_TXBSW" class="<?=$color6[4][2]?>"><? get_select_numbers($pl_TXBSW,16,768,16,'no'); ?></td>
				</tr>
				<tr>
					<td class="parameter_name">RAX-B HW installed</td>
					<td class="<?=$color7[4][1]?>"><SELECT NAME="pl_RAXBHW" class="<?=$color7[4][2]?>"><option>32</option><option>64</option><? get_select_numbers($pl_RAXBHW,"768","96","96",'no'); ?></td>
					<td class="parameter_name">RAX-B SW installed</td>
					<td class="<?=$color8[4][1]?>"><SELECT NAME="pl_RAXBSW" class="<?=$color8[4][2]?>"><? get_select_numbers($pl_RAXBSW,"16","384","16",'no'); ?></td>
				</tr>
				<tr>
					<td class="parameter_name">HS-TX HW installed</td>
					<td class="<?=$color7[1][1]?>"><SELECT NAME="pl_HSTXHW" class="<?=$color7[1][2]?>"><? get_select_numbers($pl_HSTXHW,"0","60","15",'no'); ?></td>
					<td class="parameter_name">HS-TX SW installed</td>
					<td class="<?=$color8[1][1]?>"><SELECT NAME="pl_HSTXSW" class="<?=$color8[1][2]?>"><? get_select_numbers($pl_HSTXSW,"0","20","5",'no'); ?></td>
				</tr>
				<tr>
					<td class="parameter_name">RAX-E HW installed</td>
					<td class="<?=$color7[1][1]?>"><SELECT NAME="pl_RAXEHW" class="<?=$color7[1][2]?>"><? get_select_numbers($pl_RAXEHW,"0","4","2",'no'); ?></td>
					<td class="parameter_name">RAX-E SW installed</td>
					<td class="<?=$color8[1][1]?>"><SELECT NAME="pl_RAXESW" class="<?=$color8[1][2]?>"><? get_select_numbers($pl_HRAXESW,"0","4","2",'no'); ?></td>
				</tr>
				</table>
		</td>
	    </tr>
		<tr class="TR2">
			 <td bgcolor="Black">&nbsp;</td>
			 <td bgcolor="Black" align="center" width="160"><font color="White" size="2"><b> Sector 1 </td>
			 <td bgcolor="Black" align="center" width="160"><font color="White" size="2"><b> Sector 2 </td>
			 <td bgcolor="Black" align="center" width="160"><font color="White" size="2"><b> Sector 3 </td>

		</tr>
		<tr>
			 <td class="parameter_name">State</td>
	 		 <td class="<?=$color[4][1]?>"><?=$STATE_1?></td>
			 <td class="<?=$color[5][1]?>"><?=$STATE_2?></td>
			 <td class="<?=$color[6][1]?>"><?=$STATE_3?></td>

		</tr>
		<tr>
			 <td class="parameter_name"><font color="blue">MCPA type</font></td>
			 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_MCPATYPE_1" class="<?=$color[4][2]?>"><? get_select_LOWHIGH($pl_MCPATYPE_1);?></td>
			 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_MCPATYPE_2" class="<?=$color[5][2]?>"><? get_select_LOWHIGH($pl_MCPATYPE_2);?></td>
			 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_MCPATYPE_3" class="<?=$color[6][2]?>"><? get_select_LOWHIGH($pl_MCPATYPE_3);?></td>

		</tr>
		<tr>
			 <td class="parameter_name"><font color="blue">MCPA mode</font></td>
			 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_MCPAMODE_1" class="<?=$color[4][2]?>"><? get_select_LOWHIGH($pl_MCPAMODE_1);?></td>
			 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_MCPAMODE_2" class="<?=$color[5][2]?>"><? get_select_LOWHIGH($pl_MCPAMODE_2);?></td>
			 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_MCPAMODE_3" class="<?=$color[6][2]?>"><? get_select_LOWHIGH($pl_MCPAMODE_3);?></td>

		 </tr>
	 	 <tr>
			 <td class="parameter_name"><font color="blue">Carriers active network</font></td>
	 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_FREQ_ACTIVE_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE_1,0,4,1,'no');?></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_FREQ_ACTIVE_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE_2,0,4,1,'no');?></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_FREQ_ACTIVE_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_FREQ_ACTIVE_3,0,4,1,'no');?></td>


		</tr>
		<tr>
			 <td class="parameter_name"><font color="blue">Carriers installed 1</font></td>
			 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_TRU_INST1_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_TRU_INST1_1,0,4,1,'no');?></td>
			 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_TRU_INST1_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_TRU_INST1_2,0,4,1,'no');?></td>
			 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_TRU_INST1_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_TRU_INST1_3,0,4,1,'no');?></td>
		</tr>
		<tr>
			 <td class="parameter_name"><font color="blue">ASC</font></td>
			 <td class="<?=$color[4][1]?>"><select name="pl_ASC_1" class="<?=$color[4][2]?>"><? get_select_yesno("$pl_ASC_1"); ?></td>
			 <td class="<?=$color[5][1]?>"><select name="pl_ASC_2" class="<?=$color[5][2]?>"><? get_select_yesno("$pl_ASC_2"); ?></td>
			 <td class="<?=$color[6][1]?>"><select name="pl_ASC_3" class="<?=$color[6][2]?>"><? get_select_yesno("$pl_ASC_3"); ?></td>

		</tr>
		<tr>
			 <td class="parameter_name"><font color="blue">RET</font></td>
			 <td class="<?=$color[4][1]?>"><select name="pl_RET_1" class="<?=$color[4][2]?>"><? get_select_yesno("$pl_RET_1"); ?></td>
			 <td class="<?=$color[5][1]?>"><select name="pl_RET_2" class="<?=$color[5][2]?>"><? get_select_yesno("$pl_RET_2"); ?></td>
			 <td class="<?=$color[6][1]?>"><select name="pl_RET_3" class="<?=$color[6][2]?>"><? get_select_yesno("$pl_RET_3"); ?></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Antenna Type 1</td>
			 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ANTTYPE1_1" class="<?=$color[4][2]?>"><? echo get_select_anttype($pl_ANTTYPE1_1);?></select></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ANTTYPE1_2" class="<?=$color[5][2]?>"><? echo get_select_anttype($pl_ANTTYPE1_2);?></select></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ANTTYPE1_3" class="<?=$color[6][2]?>"><? echo get_select_anttype($pl_ANTTYPE1_3);?></select></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Elektrical downtilt 1</td>
		 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ELECTILT1_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ELECTILT1_1,0,15,1,'no');?></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ELECTILT1_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ELECTILT1_2,0,15,1,'no');?></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ELECTILT1_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ELECTILT1_3,0,15,1,'no');?></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Mechanical tilt 1</td>
		 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_MECHTILT1_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_MECHTILT1_1,0,15,1,'no');?>&nbsp;
		 	 <SELECT NAME='pl_MECHTILT_DIR1_1' CLASS='<?=$color2[4][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR1_1?><option>DOWNTILT<option>UPTILT</SELECT></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_MECHTILT1_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_MECHTILT1_2,0,15,1,'no');?>&nbsp;
		 	 <SELECT NAME='pl_MECHTILT_DIR1_2' CLASS='<?=$color2[5][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR1_2?><option>DOWNTILT<option>UPTILT</SELECT></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_MECHTILT1_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_MECHTILT1_3,0,15,1,'no');?>&nbsp;
		 	 <SELECT NAME='pl_MECHTILT_DIR1_3' CLASS='<?=$color2[6][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR1_3?><option>DOWNTILT<option>UPTILT</SELECT></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Antenna Height 1 </td>
		 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ANTHEIGHT1_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_1,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT1_1_t" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_1_t,0,99,1,'yes');?></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ANTHEIGHT1_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_2,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT1_2_t" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_2_t,0,99,1,'yes');?></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ANTHEIGHT1_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_3,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT1_3_t" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ANTHEIGHT1_3_t,0,99,1,'yes');?></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Antenna Type 2</td>
	 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ANTTYPE2_1" class="<?=$color[4][2]?>"><? echo get_select_anttype($ANTTYPE2_1);?></select></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ANTTYPE2_2" class="<?=$color[5][2]?>"><? echo get_select_anttype($ANTTYPE2_2);?></select></td>
			 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ANTTYPE2_3" class="<?=$color[6][2]?>"><? echo get_select_anttype($ANTTYPE2_3);?></select></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Elektrical downtilt 2</td>
		 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ELECTILT2_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ELECTILT2_1,0,15,1,'no');?></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ELECTILT2_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ELECTILT2_2,0,15,1,'no');?></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ELECTILT2_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ELECTILT2_3,0,15,1,'no');?></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Mechanical tilt 2</td>
	 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_MECHTILT2_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_MECHTILT2_1,0,15,1,'no');?>&nbsp;
		 	 <SELECT NAME='pl_MECHTILT_DIR2_1' CLASS='<?=$color2[4][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR2_1?><option>DOWNTILT<option>UPTILT</SELECT></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_MECHTILT2_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_MECHTILT2_2,0,15,1,'no');?>&nbsp;
		 	 <SELECT NAME='pl_MECHTILT_DIR2_2' CLASS='<?=$color2[5][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR2_2?><option>DOWNTILT<option>UPTILT</SELECT></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_MECHTILT2_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_MECHTILT2_3,0,15,1,'no');?>&nbsp;
		 	 <SELECT NAME='pl_MECHTILT_DIR2_3' CLASS='<?=$color2[6][2]?>'><option SELECTED><?=$pl_MECHTILT_DIR2_3?><option>DOWNTILT<option>UPTILT</SELECT></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Antenna Height 2</td>
		     <td class="<?=$color[4][1]?>"><SELECT NAME="pl_ANTHEIGHT2_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_1,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT2_1_t" class="<?=$color2[4][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_1_t,0,99,1,'yes');?></td>
		  	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_ANTHEIGHT2_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_2,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT2_2_t" class="<?=$color2[5][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_2_t,0,99,1,'yes');?></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_ANTHEIGHT2_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_3,-5,200,1,'no');?>m<SELECT NAME="pl_ANTHEIGHT2_3_t" class="<?=$color2[6][2]?>"><? get_select_numbers($pl_ANTHEIGHT2_3_t,0,99,1,'yes');?></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Azimuth</td>
	 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_AZI_1" class="<?=$color[4][2]?>"><? get_select_azi($pl_AZI_1);?></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_AZI_2" class="<?=$color[5][2]?>"><? get_select_azi($pl_AZI_2);?></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_AZI_3" class="<?=$color[6][2]?>"><? get_select_azi($pl_AZI_3);?></td>

		</tr>
	 	<tr>
			 <td class="parameter_name">Feeder type <?=$updatable?></td>
	 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_FEEDER_1" class="<?=$color[4][2]?>"><? get_select_feeder($pl_FEEDER_1);?></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_FEEDER_2" class="<?=$color[5][2]?>"><? get_select_feeder($pl_FEEDER_2);?></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_FEEDER_3" class="<?=$color[6][2]?>"><? get_select_feeder($pl_FEEDER_3);?></td>

		</tr>
		<tr>
			 <td class="parameter_name"><font color="blue">Feeder sharing</font></td>
		 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_FEEDERSHARE_1" class="<?=$color[4][2]?>"><? get_select_feedershare($pl_FEEDERSHARE_1);?></SELECT></td>
			 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_FEEDERSHARE_2" class="<?=$color[5][2]?>"><? get_select_feedershare($pl_FEEDERSHARE_2);?></SELECT></td>
			 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_FEEDERSHARE_3" class="<?=$color[6][2]?>"><? get_select_feedershare($pl_FEEDERSHARE_3);?></SELECT></td>
	    </tr>
	 	<tr>
			 <td class="parameter_name">Feeder length <?=$updatable?></td>
	 	 	 <td class="<?=$color[4][1]?>"><SELECT NAME="pl_FEEDERLEN_1" class="<?=$color[4][2]?>"><? get_select_numbers($pl_FEEDERLEN_1,0,200,1,'no');?>m<SELECT NAME="pl_FEEDERLEN_1_t" class="<?=$color2[4][2]?>"><? get_select_numbers($pl_FEEDERLEN_1_t,0,99,5,'yes');?></td>
		 	 <td class="<?=$color[5][1]?>"><SELECT NAME="pl_FEEDERLEN_2" class="<?=$color[5][2]?>"><? get_select_numbers($pl_FEEDERLEN_2,0,200,1,'no');?>m<SELECT NAME="pl_FEEDERLEN_2_t" class="<?=$color2[5][2]?>"><? get_select_numbers($pl_FEEDERLEN_2_t,0,99,5,'yes');?></td>
		 	 <td class="<?=$color[6][1]?>"><SELECT NAME="pl_FEEDERLEN_3" class="<?=$color[6][2]?>"><? get_select_numbers($pl_FEEDERLEN_3,0,200,1,'no');?>m<SELECT NAME="pl_FEEDERLEN_3_t" class="<?=$color2[6][2]?>"><? get_select_numbers($pl_FEEDERLEN_3_t,0,99,5,'yes');?></td>

		</tr>
	    <tr>
	 		<td colspan="<?=$colspan?>" class="<?=$BSDS_color?>">&nbsp;</td>
	 	</tr>
	 	<tr>
	 		<td colspan="<?=$colspan?>" bgcolor="Black" height="2px"></td>
	 	</tr>
	 	</table>
</td>
</tr>
<tr>
<td>
	<center><input type="submit" value="DO THE TEST"></center>
</td>
</tr>
</table>
</body>
</html>

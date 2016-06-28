<div id="page">
<div id="bsds_content">

<table cellpadding="0" cellspacing="0"  border="0" width="100%" height="100%">
<tr valign="top">
<?
if (!empty($type)){
?>

<td align='center'>

	 <p align="center" class="<?=$_SESSION['BSDS_color']?>"><font size=3><b>STATUS: <?=$_SESSION['BSDS_status']?></b>
	 <? if  ($_SESSION['BSDS_BOB_REFRESH']!=""){ ?>
	 	(BOB REFRESH:<?=$_SESSION['BSDS_BOB_REFRESH']?>)
	 	<? } ?>
	 	</font></p>
	<?
	if ($_SESSION['table_view']==""){
 	?>
  	<p align="center" class="BSDS_preready"><font size=3><b>!!! PRE READY VIEW !!!!</b></p>
 	<?
	}
	?>
	<?  if ($check_current_exists==0 && $_SESSION['table_view']=="_FUND" && $_SESSION['BSDS_status']!="PRE READY TO BUILD"){ ?>
 		<p align="center"><font size=3><b>NO LIVE DATA AVAILABLE AT TIME OF FUNDING!</p>
 	<? } ?>

	<table border="1" bordercolor="lighblue" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td class="table_BSDSinfo">&nbsp;</td>
	 	<td colspan="3" class="table_head"><b>
	 	<? if ($check_current_exists_UMTS=="0"){ ?>
	 			<font color=red>NOT SAVED</font>
	 		<? } ?>
	 			CURRENT <?=$type?>
	 			<?
	 			if ($BSDS_status!="BSDS FUNDED"){ ?>
	 				<font color="yellow">LIVE SITUATION</font> from OSS/ASSET
	 			<? }else{?>
					SITUATION FROM <?=$CHANGEDATE;?>
				<? } ?>
	 			</B>
			</td>

		 	<?
		 	$pl="";
		 	if (($check_current_exists_UMTS=="0" && $BSDS_status!="BSDS FUNDED") || ($check_current_exists_UMTS=="0" && $check_planned_exists_UMTS=="1" && $BSDS_status=="BSDS FUNDED")){
		 		$pl.="SAVE FIRST current BSDS situation!";
		 		$clas="table_head2";
		 	}else{
		 		$clas="table_head";
		 		if ($check_planned_exists_UMTS==0){
	 				$pl.="<font color=red>NOT SAVED</font> ";
	 				$general_override="NOT_SAVED";
		 		}
		 		$pl.="PLANNED $type SITUATION";
		 	}
			if ($CHANGEDATE && $check_planned_exists_UMTS==1){
				$pl.= "($pl_CHANGEDATE)";
			}

	 	?>
	 	<td colspan="3" class="<?=$clas?>"><b><?=$pl?></b></td>

	</tr>
	<tr>
		<td class="table_BSDSinfo">
			<table cellpadding="0" cellspacing="0" height="100%" width="100%">
	 		<tr>
	 			<td align="center" class="table_BSDSinfo"><font size="3px"><b>SITEID: <?=$_SESSION[SiteID]?><br>BSDSID: <?=$_SESSION['BSDSKEY']?><br>BOB REFRESH:<br><?=$_SESSION['BSDS_BOB_REFRESH']?></b></font></td>
	 		</tr>
	 		</table>
		</td>
		<? 	$color1=check_if_same($MBPS,$pl_MBPS,'','','','','','',$MBPS_override,$general_override);
			$color2=check_if_same($CABTYPE,$pl_CABTYPE,'','','','','','',$CABTYPE_override,$general_override);
			$color3=check_if_same($POWERSUP,$pl_POWERSUP,'','','','','','',$POWERSUP_override,$general_override);
			$color4=check_if_same($PSU,$pl_PSU,'','','','','','',$PSU_override,$general_override);
			$color5=check_if_same($TXBHW,$pl_TXBHW,'','','','','','',$TXBHW_override,$general_override);
			$color6=check_if_same($TXBSW,$pl_TXBSW,'','','','','','',$TXBSW_override,$general_override);
			$color7=check_if_same($RAXBHW,$pl_RAXBHW,'','','','','','',$RAXBHW_override,$general_override);
			$color8=check_if_same($RAXBSW,$pl_RAXBSW,'','','','','','',$RAXBSW_override,$general_override);
			$color9=check_if_same($IPB,$pl_IPB,'','','','','','',$IPB_override,$general_override); ?>
		<td colspan="3">
			<table width="100%" height="100%" border="0" bordercolor="lighblue" cellpadding="1" align="center" cellspacing="1">
			<tr>
				<td class="parameter_name">Cabinet</td>
				<td class="CURRENT_SAME"><?=$cab?> (<?=$LOGNODEID?>)</td>
				<td class="parameter_name"><b>Cabinettype</b></td>
				<TD class="<?=$color2[1][1]?>"><?=$CABTYPE?></td>
			</tr>
			<tr>
				<td class="parameter_name">Power supply</td>
				<td class="<?=$color3[1][1]?>"><?=$POWERSUP?></td>
				<td class="parameter_name"><b>Data Service</b></td>
				<TD class="<?=$color4[1][1]?>"><?=$SERVICE?></td>
			</tr>
			<tr>
				<td class="parameter_name">Playstation</td>
				<td class="<?=$color5[1][1]?>"><?=$PLAYSTATION?></td>
				<td class="parameter_name">&nbsp;</td>
				<TD class="<?=$color6[1][1]?>">&nbsp</td>
			</tr>
			</table>
		</td>


		<td colspan="3">
			<table width="100%" height="100%" border="0" bordercolor="lighblue" cellpadding="1" align="center" cellspacing="1">
			<tr>
				<td class="parameter_name">Cabinet</td>
				<td class="PLANNED_SAME"><?=$cab?> (<?=$LOGNODEID?>)</td>
				<td class="parameter_name">Cabinettype</td>
				<TD class="<?=$color2[4][1]?>"><?=$pl_CABTYPE?></td>
			</tr>
			<tr>
				<td class="parameter_name">Power supply</td>
				<td class="<?=$color3[4][1]?>"><?=$pl_POWERSUP?></td>
				<td class="parameter_name">Data Service</td>
				<TD class="<?=$color4[4][1]?>"><?=$pl_SERVICE?></td>
			</tr>
			<tr>
				<td class="parameter_name">Playstation</td>
				<td class="<?=$color5[4][1]?>"><?=$pl_PLAYSTATION?></td>
				<td class="parameter_name">NODEB - RNC</td>
				<TD class="SAME"><?=$pl_NODEB?> - <?=$pl_RNC?></td>
			</tr>
			</table>
		</td>

	</tr>
	<tr class="TR2">
		 <td bgcolor="Black">&nbsp;</td>
		 <td bgcolor="Black" align="center" width="160"><font color="White" size="2"><b> Sector 1 (<? echo substr($UMTSCELLID_1,0,-1); ?>)</td>
		 <td bgcolor="Black" align="center" width="160"><font color="White" size="2"><b> Sector 2 (<? echo substr($UMTSCELLID_2,0,-1); ?>)</td>
		 <td bgcolor="Black" align="center" width="160"><font color="White" size="2"><b> Sector 3 (<? echo substr($UMTSCELLID_3,0,-1); ?>)</td>


		 <td bgcolor="Black" align="center" width="160"><font color="White" size="2"><b> Sector 1 (<? echo substr($UMTSCELLID_1,0,-1); ?>)</td>
		 <td bgcolor="Black" align="center" width="160"><font color="White" size="2"><b> Sector 2 (<? echo substr($UMTSCELLID_2,0,-1); ?>)</td>
		 <td bgcolor="Black" align="center" width="160"><font color="White" size="2"><b> Sector 3 (<? echo substr($UMTSCELLID_3,0,-1); ?>)</td>

	</tr>
    <? $color=check_if_same($STATE_1,$pl_STATE_1,$STATE_2,$pl_STATE_2,$STATE_3,$pl_STATE_3,'','','','',$pl_STATE_override,$general_override); ?>
	<tr>
		 <td class="parameter_name">State</td>
		 <td class="<?=$color[1][1]?>"><?=$STATE_1?></td>
	 	 <td class="<?=$color[2][1]?>"><?=$STATE_2?></td>
	 	 <td class="<?=$color[3][1]?>"><?=$STATE_3?></td>


 		 <td class="<?=$color[4][1]?>"><?=$STATE_1?></td>
		 <td class="<?=$color[5][1]?>"><?=$STATE_2?></td>
		 <td class="<?=$color[6][1]?>"><?=$STATE_3?></td>

	</tr>
 	 <? $color=check_if_same($FREQ_ACTIVE_1,$pl_FREQ_ACTIVE_1,$FREQ_ACTIVE_2,$pl_FREQ_ACTIVE_2,$FREQ_ACTIVE_3,$pl_FREQ_ACTIVE_3,'','','','',$FREQ_ACTIVE_override,$general_override); ?>
 	 <tr>
		 <td class="parameter_name">Carriers active network</td>
		 <td class="<?=$color[1][1]?>"><?=$FREQ_ACTIVE_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$FREQ_ACTIVE_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$FREQ_ACTIVE_3?></td>


 	 	 <td class="<?=$color[4][1]?>"><?=$pl_FREQ_ACTIVE_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_FREQ_ACTIVE_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_FREQ_ACTIVE_3?></td>


	</tr>
	<? $color=check_if_same($ASC_1,$pl_ASC_1,$ASC_2,$pl_ASC_2,$ASC_3,$pl_ASC_3,'','',$ASC_override,$general_override); ?>
	<tr>
		 <td class="parameter_name">ASC</td>
		 <td class="<?=$color[1][1]?>"><?=$ASC_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ASC_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ASC_3?></td>


		 <td class="<?=$color[4][1]?>"><?=$pl_ASC_1?></td>
		 <td class="<?=$color[5][1]?>"><?=$pl_ASC_2?></td>
		 <td class="<?=$color[6][1]?>"><?=$pl_ASC_3?></td>

	</tr>
 	<? $color=check_if_same($RET_1,$pl_RET_1,$RET_2,$pl_RET_2,$RET_3,$pl_RET_3,'','',$RET_override,$general_override); ?>
	<tr>
		 <td class="parameter_name">RET</td>
		 <td class="<?=$color[1][1]?>"><?=$RET_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$RET_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$RET_3?></td>


		 <td class="<?=$color[4][1]?>"><?=$pl_RET_1?></td>
		 <td class="<?=$color[5][1]?>"><?=$pl_RET_2?></td>
		 <td class="<?=$color[6][1]?>"><?=$pl_RET_3?></td>

	</tr>
 	<? $color=check_if_same($ANTTYPE1_1,$pl_ANTTYPE1_1,$ANTTYPE1_2,$pl_ANTTYPE1_2,$ANTTYPE1_3,$pl_ANTTYPE1_3,'','',$ANTTYPE1_override,$general_override); ?>
 	<tr>
		 <td class="parameter_name">Antenna Type 1</td>
		 <td class="<?=$color[1][1]?>"><?=$ANTTYPE1_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ANTTYPE1_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ANTTYPE1_3?></td>


		 <td class="<?=$color[4][1]?>"><?=$pl_ANTTYPE1_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_ANTTYPE1_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_ANTTYPE1_3?></td>

	</tr>
 	<? $color=check_if_same($ELECTILT1_1,$pl_ELECTILT1_1,$ELECTILT1_2,$pl_ELECTILT1_2,$ELECTILT1_3,$pl_ELECTILT1_3,'','',$ELECTILT1_override,$general_override); ?>
 	<tr>
		 <td class="parameter_name">Elektrical downtilt 1</td>
		 <td class="<?=$color[1][1]?>"><?=$ELECTILT1_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ELECTILT1_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ELECTILT1_3?></td>


	 	 <td class="<?=$color[4][1]?>"><?=$pl_ELECTILT1_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_ELECTILT1_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_ELECTILT1_3?></td>

	</tr>
 	<? $color=check_if_same($MECHTILT1_1,$pl_MECHTILT1_1,$MECHTILT1_2,$pl_MECHTILT1_2,$MECHTILT1_3,$pl_MECHTILT1_3,'','',$MECHTILT1_override,$general_override);
		$color2=check_if_same($MECHTILT1_1_t,$pl_MECHTILT1_1_t,$MECHTILT1_2_t,$pl_MECHTILT1_2_t,$MECHTILT1_3_t,$pl_MECHTILT1_3_t,$MECHTILT1_4_t,$pl_MECHTILT1_4_t,$pl_MECHTILT1_t_override,$general_override); ?>
	  <tr>
		 <td class="parameter_name">Mechanical tilt 1</td>
		 <td class="<?=$color[1][1]?>"><?=$MECHTILT1_1?>&nbsp;<?=$MECHTILT1_1_t?></td>
		 <td class="<?=$color[2][1]?>"><?=$MECHTILT1_2?>&nbsp;<?=$MECHTILT1_2_t?></td>
		 <td class="<?=$color[3][1]?>"><?=$MECHTILT1_3?>&nbsp;<?=$MECHTILT1_3_t?></td>


		 <td class="<?=$color[4][1]?>"><?=$pl_MECHTILT1_1?>&nbsp;<?=$pl_MECHTILT1_1_t?></td>
		 <td class="<?=$color[5][1]?>"><?=$pl_MECHTILT1_2?>&nbsp;<?=$pl_MECHTILT1_2_t?></td>
		 <td class="<?=$color[6][1]?>"><?=$pl_MECHTILT1_3?>&nbsp;<?=$pl_MECHTILT1_3_t?></td>

	  </tr>
 	<? $color=check_if_same($ANTHEIGHT1_1,$pl_ANTHEIGHT1_1,$ANTHEIGHT1_2,$pl_ANTHEIGHT1_2,$ANTHEIGHT1_3,$pl_ANTHEIGHT1_3,'','',$ANTHEIGHT1_override,$general_override);
 		 $color2=check_if_same($ANTHEIGHT1_1_t,$pl_ANTHEIGHT1_1_t,$ANTHEIGHT1_2_t,$pl_ANTHEIGHT1_2_t,$ANTHEIGHT1_3_t,$pl_ANTHEIGHT1_3_t,'','',$ANTHEIGHT1_t_override,$general_override); ?>
 	<tr>
		 <td class="parameter_name">Antenna Height 1 </td>
		 <td class="<?=$color[1][1]?>"><?=$ANTHEIGHT1_1?>m<?=$ANTHEIGHT1_1_t?></td>
		 <td class="<?=$color[2][1]?>"><?=$ANTHEIGHT1_2?>m<?=$ANTHEIGHT1_2_t?></td>
		 <td class="<?=$color[3][1]?>"><?=$ANTHEIGHT1_3?>m<?=$ANTHEIGHT1_3_t?></td>


	 	 <td class="<?=$color[4][1]?>"><?=$pl_ANTHEIGHT1_1?>m<?=$pl_ANTHEIGHT1_1_t?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_ANTHEIGHT1_2?>m<?=$pl_ANTHEIGHT1_2_t?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_ANTHEIGHT1_3?>m<?=$pl_ANTHEIGHT1_3_t?></td>

	</tr>
 	<? $color=check_if_same($ANTTYPE2_1,$pl_ANTTYPE2_1,$ANTTYPE2_2,$pl_ANTTYPE2_2,$ANTTYPE2_3,$pl_ANTTYPE2_3,'','',$ANTTYPE2_override,$general_override); ?>
 	<tr>
		 <td class="parameter_name">Antenna Type 2</td>
		 <td class="<?=$color[1][1]?>"><?=$ANTTYPE2_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ANTTYPE2_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ANTTYPE2_3?></td>


 	 	 <td class="<?=$color[4][1]?>"><?=$ANTTYPE2_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$ANTTYPE2_2?></td>
		 <td class="<?=$color[6][1]?>"><?=$ANTTYPE2_3?></td>

	</tr>
 	<? $color=check_if_same($ELECTILT2_1,$pl_ELECTILT2_1,$ELECTILT2_2,$pl_ELECTILT2_2,$ELECTILT2_3,$pl_ELECTILT2_3,'','',$ELECTILT2_override,$general_override); ?>
 	<tr>
		 <td class="parameter_name">Elektrical downtilt 2</td>
		 <td class="<?=$color[1][1]?>"><?=$ELECTILT2_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ELECTILT2_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ELECTILT2_3?></td>


	 	 <td class="<?=$color[4][1]?>"><?=$pl_ELECTILT2_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_ELECTILT2_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_ELECTILT2_3?></td>

	</tr>
 	<? $color=check_if_same($MECHTILT2_1,$pl_MECHTILT2_1,$MECHTILT2_2,$pl_MECHTILT2_2,$MECHTILT2_3,$pl_MECHTILT2_3,'','',$MECHTILT2_override,$general_override);
 	 $color2=check_if_same($MECHTILT2_1_t,$pl_MECHTILT2_1_t,$MECHTILT2_2_t,$pl_MECHTILT2_2_t,$MECHTILT2_3_t,$pl_MECHTILT2_3_t,$MECHTILT2_4_t,$pl_MECHTILT2_4_t,$pl_MECHTILT2_t_override,$general_override); ?>
	 	  <tr>
			 <td class="parameter_name">Mechanical tilt 2</td>
			 <td class="<?=$color[1][1]?>"><?=$MECHTILT2_1?>&nbsp;<?=$MECHTILT2_1_t?></td>
			 <td class="<?=$color[2][1]?>"><?=$MECHTILT2_2?>&nbsp;<?=$MECHTILT2_2_t?></td>
			 <td class="<?=$color[3][1]?>"><?=$MECHTILT2_3?>&nbsp;<?=$MECHTILT2_3_t?></td>

	 	 	 <td class="<?=$color[4][1]?>"><?=$pl_MECHTILT2_1?>&nbsp;<?=$pl_MECHTILT2_1_t?></td>
	 	 	 <td class="<?=$color[5][1]?>"><?=$pl_MECHTILT2_2?>&nbsp;<?=$pl_MECHTILT2_2_t?></td>
	 	 	 <td class="<?=$color[6][1]?>"><?=$pl_MECHTILT2_3?>&nbsp;<?=$pl_MECHTILT2_3_t?></td>
	  </tr>
 	<? $color=check_if_same($ANTHEIGHT2_1,$pl_ANTHEIGHT2_1,$ANTHEIGHT2_2,$pl_ANTHEIGHT2_2,$ANTHEIGHT2_3,$pl_ANTHEIGHT2_3,'','',$ANTHEIGHT2_override,$general_override);
 	 $color2=check_if_same($ANTHEIGHT2_1_t,$pl_ANTHEIGHT2_1_t,$ANTHEIGHT2_2_t,$pl_ANTHEIGHT2_2_t,$ANTHEIGHT2_3_t,$pl_ANTHEIGHT2_3_t,'','',$ANTHEIGHT2_t_override,$general_override); ?>
 	<tr>
		 <td class="parameter_name">Antenna Height 2</td>
		 <td class="<?=$color[1][1]?>"><?=$ANTHEIGHT2_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ANTHEIGHT2_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ANTHEIGHT2_3?></td>


	     <td class="<?=$color[4][1]?>"><?=$pl_ANTHEIGHT2_1?>m<?=$pl_ANTHEIGHT2_1_t?></td>
	  	 <td class="<?=$color[5][1]?>"><?=$pl_ANTHEIGHT2_2?>m<?=$pl_ANTHEIGHT2_2_t?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_ANTHEIGHT2_3?>m<?=$pl_ANTHEIGHT2_3_t?></td>

	</tr>
 	<? $color=check_if_same($AZI_1,$pl_AZI_1,$AZI_2,$pl_AZI_2,$AZI_3,$pl_AZI_3,'','',$AZI_override,$general_override); ?>
 	<tr>
		 <td class="parameter_name">Azimuth</td>
		 <td class="<?=$color[1][1]?>"><?=$AZI_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$AZI_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$AZI_3?></td>


 	 	 <td class="<?=$color[4][1]?>"><?=$pl_AZI_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_AZI_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_AZI_3?></td>

	</tr>
 	<? $color=check_if_same($FEEDER_1,$pl_FEEDER_1,$FEEDER_2,$pl_FEEDER_2,$FEEDER_3,$pl_FEEDER_3,'','',$FEEDER_override,$general_override); ?>
 	<tr>
		 <td class="parameter_name">Feeder type <?=$updatable?></td>
		 <td class="<?=$color[1][1]?>"><?=$FEEDER_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$FEEDER_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$FEEDER_3?></td>


 	 	 <td class="<?=$color[4][1]?>"><?=$pl_FEEDER_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_FEEDER_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_FEEDER_3?></td>

	</tr>
 	<? $color=check_if_same($FEEDERLEN_1,$pl_FEEDERLEN_1,$FEEDERLEN_2,$pl_FEEDERLEN_2,$FEEDERLEN_3,$pl_FEEDERLEN_3,'','',$FEEDERLEN_override,$general_override);
 	 $color2=check_if_same($FEEDERLEN_1_t,$pl_FEEDERLEN_1_t,$FEEDERLEN_2_t,$pl_FEEDERLEN_2_t,$FEEDERLEN_3_t,$pl_FEEDERLEN_3_t,'','',$FEEDERLEN_t_override,$general_override); ?>
 	<tr>
		 <td class="parameter_name">Feeder length <?=$updatable?></td>
		 <td class="<?=$color[1][1]?>"><?=$FEEDERLEN_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$FEEDERLEN_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$FEEDERLEN_3?></td>


 	 	 <td class="<?=$color[4][1]?>"><?=$pl_FEEDERLEN_1?>m<?=$pl_FEEDERLEN_1_t?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_FEEDERLEN_2?>m<?=$pl_FEEDERLEN_2_t?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_FEEDERLEN_3?>m<?=$pl_FEEDERLEN_3_t?></td>

	</tr>
 	  <? $color=check_if_same($FEEDERSHARE1,$pl_FEEDERSHARE1,$FEEDERSHARE2,$pl_FEEDERSHARE2,$FEEDERSHARE3,$pl_FEEDERSHARE3,$FEEDERSHARE4,$pl_FEEDERSHARE4,$FEEDERSHARE_override,$general_override);?>
 	<tr>
 		 <td class="parameter_name">Feeder sharing</td>
		 <td class="<?=$color[1][1]?>"><? print_feedershare($FEEDERSHARE1)?></td>
		 <td class="<?=$color[2][1]?>"><? print_feedershare($FEEDERSHARE2)?></td>
		 <td class="<?=$color[3][1]?>"><? print_feedershare($FEEDERSHARE3)?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=print_feedershare($FEEDERSHARE4)?></td>
		 <? } ?>

 	 	 <td class="<?=$color[4][1]?>"><? print_feedershare($pl_FEEDERSHARE1);?></td>
	 	 <td class="<?=$color[5][1]?>"><? print_feedershare($pl_FEEDERSHARE2)?></td>
	 	 <td class="<?=$color[6][1]?>"><? print_feedershare($pl_FEEDERSHARE3)?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><? print_feedershare($pl_FEEDERSHARE4)?></td>
		 <? } ?>
	</tr>
	<tr>
 	<td colspan="8" bgcolor="Black" height="2px"></td>
 	</tr>
	</table>
  	<?
	 $varname="pl_COMMENTS_UMTS";
	 $$varname=$pl_COMMENTS; ?>
<?
}
?>
</td>
</tr>
</table>

</div>
</div>
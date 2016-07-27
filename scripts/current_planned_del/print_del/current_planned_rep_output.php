<div id="page">
<div id="bsds_content">

<br>
<table cellpadding="0" cellspacing="0"  border="0" width="100%">
<tr valign="top" align="center">
  <td align='center'>
	 <table border="0" cellpadding="0" cellspacing="1" align="center" width="98%">
	 <tr>
	 	<td colspan="6" bgcolor="Black" height="2px"></td>
	 </tr>
	<?
	 if ($_SESSION['HISTVIEW']!=""){
	 	?>
	  	<tr>
	 		<td colspan="6>" class="BSDS_preready"><font size=3><b>!!! HISTORY VIEW !!!!</b></td>
	 	</tr>
	 <?
	 }
	 if ($_SESSION['table_view']=="" && $_SESSION['HISTVIEW']==""){
	?>
	  	<tr>
	 		<td colspan="6" class="BSDS_preready"><font size=3><b>!!! PRE READY VIEW !!!!</b></td>
	 	</tr>
	 <?
	 }
	 ?>
 	<tr>
	 	<td colspan="6" class="<?=$BSDS_color?>"><font size=3><b>CURRENT STATUS for <?=$type?>: <?=$BSDS_status?>
	 	<? if  ($_SESSION['BSDS_BOB_REFRESH']!=""){ ?>
	 	(BOB REFRESH:<?=$_SESSION['BSDS_BOB_REFRESH']?>)</b>
	 	<? } ?>
	 	</td>
 	</tr>
	 <tr>
	 	<td align="center" bgcolor="black">&nbsp;</td>
	 	<td class="table_head"><b>
	 		<? if ($check_current_exists=="0"){ ?>
	 			<font color=red>NOT SAVED</font>
	 		<? } ?>
	 			CURRENT <?=$type?> SITUATION
	 			<?
	 			if ($CHANGEDATE)
	 				echo "($CHANGEDATE)";
	 			?>
	 			</B></td>

		 	<td width="1px" bgcolor="Black"></td>
		 	<?
		 	if ($check_current_exists=="0"){
		 		$pl="SAVE FIRST current BSDS situation!";
		 		$clas="table_head2";
		 		$general_override="NOT_SAVED";
		 	}else{
		 		$clas="table_head";
		 		if ($check_current_exists=="0" || ($check_planned_exists ==0 && $check_current_exists==1) && $_POST['action']!="save"){
	 				$pl="<font color=red>NOT SAVED</font> ";
	 				$general_override="NOT_SAVED";
		 		}
		 		$pl.="PLANNED $type SITUATION ";
		 	}
			if ($pl_CHANGEDATE && $check_planned_exists==1){
				$pl.= "($pl_CHANGEDATE)";
			}
	 	?>
	 	<td class="<?=$clas?>"><b><?=$pl?></b></td>
	 </tr>
	 <tr>
	 	<td align="center" valign="top" bgcolor="black">
		<font size=2 color="white"><b><br>BSDS <?=$_SESSION['BSDSKEY']?><br><?=$pl_is_BSDS_accepted?></b></font><br>
		</td></td>
		<td>
			<table width="100%" height="100%" border="0" bordercolor="lighblue" cellpadding="0" align="center" cellspacing="1">
			<tr>
				<? $color_OWNER=check_if_same($OWNER,$pl_OWNER,'','','','','','',$OWNER_override,$general_override); ?>
				<td class="parameter_name"><b>OWNER</b></td>
				<td class="<?=$color_OWNER[1][1]?>"><?=$OWNER?></td>
			</tr>
			<tr>
				<? $color_BRAND=check_if_same($BRAND,$pl_BRAND,'','','','','','',$BRAND_override,$general_override); ?>
				<td class="parameter_name"><b>BRAND</b></td>
				<td class="<?=$color_BRAND[1][1]?>"><?=$BRAND?></td>
			</tr>
			<? $color_RTYPE=check_if_same($RTYPE,$pl_RTYPE,'','','','','','',$RTYPE_override,$general_override); ?>
			<tr id="pl_DXU1">
				<td class="parameter_name"><b>REPEATER TYPE</b></td>
				<td class="<?=$color_RTYPE[1][1]?>"><?=$RTYPE?></td>
			</tr>
			<? $color_TECHNOLOGY=check_if_same($TECHNOLOGY,$pl_TECHNOLOGY,'','','','','','',$TECHNOLOGY_override,$general_override); ?>
			<tr id="pl_DXU2">
				<td class="parameter_name"><b>TECHNOLOGY</b></a></td>
				<TD class="<?=$color_TECHNOLOGY[1][1]?>"><?=$TECHNOLOGY?></SELECT>
				</td>
			</tr>
			<? $color_CHANNEL=check_if_same($CHANNEL,$pl_CHANNEL,'','','','','','',$CHANNEL_override,$general_override); ?>
			<tr>
				<td class="parameter_name"><b>CHANNELIZED</b></td>
				<td class="<?=$color_CHANNEL[1][1]?>"><?=$CHANNEL?></td>
			</tr>
			<? $color_PICKUP=check_if_same($PICKUP,$pl_PICKUP,'','','','','','',$PICKUP_override,$general_override); ?>
			<tr>
				<td class="parameter_name">PICK-UP ANTENNA</td>
				<td class="<?=$color_PICKUP[1][1]?>"><?=$PICKUP?></td>
			</tr>
			<? $color_DISTRIB=check_if_same($DISTRIB,$pl_DISTRIB,'','','','','','',$DISTRIB_override,$general_override); ?>
			<tr>
				<td class="parameter_name">DISTRIBUTION ANTENNA</td>
				<td class="<?=$color_DISTRIB[1][4]?>"><?=$DISTRIB?></td>
			</tr>
			<? $color_COSP=check_if_same($COSP,$pl_COSP,'','','','','','',$COSP_override,$general_override); ?>
			<tr>
				<td class="parameter_name">COUPLERS AND SPLITTERS</td>
				<td class="<?=$color_COSP[1][4]?>"><?=$COSP?></td>
			</tr>
			<? $color_DISTRIB=check_if_same($COMMENTS,$pl_COMMENTS,'','','','','','',$COMMENTS_override,$general_override); ?>
			<tr>
				<td class="parameter_name">COMMENTS</td>
				<td class="<?=$color_COMMENTS[1][4]?>"><?=$COMMENTS?></td>
			</tr>
			</table>
		</td>

		<td width="1px" bgcolor="Black"></td>
		<td colspan="<?=$colspan2?>">
	 	    <table width="100%"  border="0"  cellpadding="0" align="center" cellspacing="1">
			<tr>
				<td class="parameter_name"><b>OWNER</b></td>
				<td class="<?=$color_OWNER[4][1]?>"><?=$pl_OWNER?></td>
			</tr>
			<tr>
				<td class="parameter_name"><b>BRAND</b></td>
				<td class="<?=$color_BRAND[4][1]?>"><?=$pl_BRAND?></td>
			</tr>
			<tr>
				<td class="parameter_name"><b>REPEATER TYPE</b></td>
				<TD class="<?=$color_RTYPE[4][1]?>"><?=$pl_RTYPE?></td>
			</tr>
			<tr>
				<td class="parameter_name"><b>TECHNOLOGY</b></a></td>
				<TD class="<?=$color_TECHNOLOGY[4][1]?>"><?=$pl_TECHNOLOGY?></td>
			</tr>
			<tr>
				<td class="parameter_name"><b>CHANNELIZED</b></td>
				<td class="<?=$color_CHANNEL[4][1]?>"><?=$pl_CHANNEL?></td>
			</tr>
			<tr>
				<td class="parameter_name">PICK-UP ANTENNA</td>
				<td class="<?=$color_PICKUP[4][1]?>"><?=$pl_PICKUP?></td>
			</tr>
			<tr>
				<td class="parameter_name">DISTRIBUTION ANTENNA</td>
				<td class="<?=$color_DISTRIB[4][4]?>"><?=$pl_DISTRIB?></td>
			</tr>
			<tr>
				<td class="parameter_name">COUPLERS AND SPLITTERS</td>
				<td class="<?=$color_COSP[4][4]?>"><?=$pl_COSP?></td>
			</tr>
			<tr>
				<td class="parameter_name">COMMENTS</td>
				<td class="<?=$color_COMMENTS[4][4]?>"><?=$pl_COMMENTS?></td>
			</tr>
			</table>

		</td>

	 </tr>
	 <tr>
	 	<td colspan="6" bgcolor="Black" height="2px"></td>
	 </tr>
	  <tr>
	 	<td colspan="6" class="<?=$BSDS_color?>">&nbsp;</td>
	 </tr>
	 <tr>
	 	<td colspan="6" bgcolor="Black" height="2px"></td>
	 </tr>
	 </table>
</td>
</tr>
</table>

</div>
</div>
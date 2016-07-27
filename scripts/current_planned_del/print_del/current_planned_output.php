
<div id="page">
<div id="bsds_content">
	 <?
	 if ($CONFIG_4){
	 	$colspan2=4;
	 	$colspan=10;
	 }else{
		$colspan2=3;
		$colspan=8;
	 }
	 ?>
	 <p align="center" class="<?=$BSDS_color?>"><font size=3><b>STATUS: <?=$BSDS_status?></b>
	 <? if  ($_SESSION['BSDS_BOB_REFRESH']!=""){ ?>
	 	(BOB REFRESH:<?=$_SESSION['BSDS_BOB_REFRESH']?>)
	 	<? } ?>
	 	</font></p>
	 <?
	 if ($_SESSION['table_view']=="" && $_SESSION['HISTVIEW']==""){
	 ?>
	  	 <p align="center" class="BSDS_preready"><font size=3><b>!!! PRE READY VIEW !!!!</b></font></p>
	 <?
	 }
	 if ($_SESSION['HISTVIEW']!=""){
	 ?>
	  	 <p align="center" class="BSDS_preready"><font size=3><b>!!! HISTORY VIEW !!!!</b></font></p>
	 <?
	 }
	 ?>

	<?  if ($check_current_exists==0 && $_SESSION['table_view']=="_FUND" && $BSDS_status!="PRE READY TO BUILD"){ ?>
 		<p align="center"><font size=3><b>NO LIVE DATA AVAILABLE AT TIME OF FUNDING!</p>
 	<? } ?>
 	---------------------<?=$NR_OF_CAB?>
	 <table border="1" cellpadding="0" cellspacing="0" align="center" width="80%">
	 <tr>
	 	<td>&nbsp;</td>
	 	<td colspan="<?=$colspan2?>" class="table_head" width="40%"><b>
	 	   <? if ($check_current_exists=="0"){ ?>
	 			<font color=red>NOT SAVED</font>
	 		<? } ?>
	 			CURRENT <?=$type?>
	 			<?
	 			   if ($BSDS_status!="BSDS FUNDED"){ ?>
	 				<br><font color="yellow">LIVE SITUATION</font> from OSS/ASSET
	 			<? }else{?>
					SITUATION SAVED ON  <?=$CHANGEDATE;?>
				<? } ?>
	 			</b>
	 	</td>
		 	<?
		 	$pl="";
		 	if ($check_current_exists=="0" && $BSDS_status!="BSDS FUNDED"){
		 		$pl="SAVE FIRST current BSDS situation!";
		 		$clas="table_head2";
		 	}else{
		 		$clas="table_head";
		 		if ($check_planned_exists=="0"){
	 				$pl.="<font color=red>NOT SAVED</font> ";
	 				$general_override="NOT_SAVED";
		 		}
		 		$pl.="PLANNED ".$type." SITUATION ";
		 	}
			if ($pl_CHANGEDATE && $check_planned_exists==1){
				$pl.= "<br>(Last update: $pl_CHANGEDATE)";
			}
	 	?>
	 	<td colspan="<?=$colspan2?>" class="<?=$clas?>" width="40%"><b><?=$pl?></b></td>

	 </tr>
	 <tr>
	 	<td><font size="3px"><b>SITEID: <?=$_SESSION[SiteID]?><br>Candidate: <?=$_SESSION[fname]?><br>BSDSID: <?=$_SESSION['BSDSKEY']?><br>BOB REFRESH:<br><?=$_SESSION['BSDS_BOB_REFRESH']?></b></font></td>
		<td colspan="<?=$colspan2?>">
			<table  border="0" cellpadding="0" align="center" cellspacing="0" width="100%" height="100%">
			<tr>
				<? $color=check_if_same($CABTYPE,$pl_CABTYPE,'','','','','','',$CABTYPE_override,$general_override); ?>
				<td class="parameter_name">Cabinettype</td>
				<td class="<?=$color[1][1]?>"><?=$CABTYPE?></td>
				<td width="5px;">&nbsp;</td>
				<? $color=check_if_same($NR_OF_CAB,$pl_NR_OF_CAB,'','','','','','',$NR_OF_CAB_override,$general_override); ?>
				<td class="parameter_name"><font size="1"><b># cabinet <?=$type?></font></td>
				<td class="<?=$color[1][1]?>"><?=$NR_OF_CAB?></td>
			</tr>
			<tr>
				<? $color=check_if_same($CDUTYPE,$pl_CDUTYPE,'','','','','','',$CDUTYPE_override,$general_override); ?>
				<td class="parameter_name">CDU Type</td>
				<TD class="<?=$color[1][1]?>"><?=$CDUTYPE?></td>
				<td width="5px;">&nbsp;</td>
				<? $color=check_if_same($BBS,$pl_BBS,'','','','','','',$BBS_override,$general_override); ?>
				<td class="parameter_name">Battery Backup Sys</td>
				<TD class="<?=$color[1][1]?>"><?=$BBS?></td>
			</tr>
			<? $color=check_if_same($DXUTYPE1,$pl_DXUTYPE1,'','','','','','',$DXUTYPE_override1,$general_override); ?>
			<tr>
				<td class="parameter_name">DXU CAB 1</td>
				<TD class="<?=$color[1][1]?>"><?=$DXUTYPE1?></td>
				<td width="5px;">&nbsp;</td>
				<? $color=check_if_same($DXUTYPE2,$pl_DXUTYPE2,'','','','','','',$DXUTYPE_override2,$general_override); ?>
				<td class="parameter_name">DXU CAB 2</td>
				<TD class="<?=$color[1][1]?>"><?=$DXUTYPE2?></td>
			</tr>
			<tr>
			<? $color=check_if_same($DXUTYPE3,$pl_DXUTYPE3,'','','','','','',$DXUTYPE_override3,$general_override); ?>
				<td class="parameter_name">DXU CAB 3</td>
				<TD class="<?=$color[1][1]?>"><?=$DXUTYPE3?></td>
				<td width="5px;">&nbsp;</td>
				<td class="parameter_name"></td>
				<TD class="PLANNED_SAME"></td>
			</tr>
			</table>
		</td>
		<td colspan="<?=$colspan2?>">
	 	    <table  border="0" cellpadding="0" align="center" cellspacing="0" width="100%" height="100%">
			<? $color=check_if_same($CABTYPE,$pl_CABTYPE,'','','','','','',$CABTYPE_override,$general_override); ?>
		 	<tr>
				<td class="parameter_name">Cabinettype</td>
				<td class="<?=$color[4][1]?>"><?=$pl_CABTYPE?></td>
				<td width="5px;">&nbsp;</td>
				<? $color=check_if_same($NR_OF_CAB,$pl_NR_OF_CAB,'','','','','','',$DXUTYPE_override,$general_override); ?>
				<td class="parameter_name"># cabinet <?=$type?></td>
				<td class="<?=$color[4][1]?>"><?=$pl_NR_OF_CAB?></td>
			 </tr>
			 <? $color=check_if_same($CDUTYPE,$pl_CDUTYPE,'','','','','','',$CDUTYPE_override,$general_override); ?>
			 <tr>
				<td class="parameter_name">CDU Type</td>
				<TD class="<?=$color[4][1]?>"><?=$pl_CDUTYPE?></td>
				<td width="5px;">&nbsp;</td>
				<? $color=check_if_same($BBS,$pl_BBS,'','','','','','',$BBS_override,$general_override); ?>
				<td class="parameter_name">Battery Backup Sys</td>
				<TD class="<?=$color[4][1]?>"><?=$pl_BBS?></td>
			</tr>
			<? $color=check_if_same($DXUTYPE1,$pl_DXUTYPE1,'','','','','','',$DXUTYPE_override1,$general_override); ?>
			<tr>
				<td class="parameter_name">DXU CAB 1</td>
				<TD class="<?=$color[4][1]?>"><?=$pl_DXUTYPE1?></td>
				<td width="5px;">&nbsp;</td>
				<? $color=check_if_same($DXUTYPE2,$pl_DXUTYPE2,'','','','','','',$DXUTYPE_override2,$general_override); ?>
				<td class="parameter_name">DXU CAB 2</td>
				<TD class="<?=$color[4][1]?>"><?=$pl_DXUTYPE2?></td>
			</tr>
			<tr>
				<? $color=check_if_same($DXUTYPE3,$pl_DXUTYPE3,'','','','','','',$DXUTYPE_override3,$general_override); ?>
				<td class="parameter_name">DXU CAB 3</td>
				<TD class="<?=$color[4][1]?>"><?=$pl_DXUTYPE3?></td>
				<td width="5px;">&nbsp;</td>
				<td class="parameter_name"></td>
				<TD class="PLANNED_SAME"></td>
			</tr>
			</table>

		</td>

	 </tr>
	 <tr>
	 	<td colspan="<?=$colspan?>"></td>
	 </tr>
	 <tr class="TR2">
		 <td width="1px">&nbsp;</td>
		 <td class="table_head">Sector <?=$sec1?></td>
		 <td class="table_head">Sector <?=$sec2?></td>
		 <td class="table_head">Sector <?=$sec3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="table_head">Sector <?=$sec4?></td>
		 <? } ?>

		 <td class="table_head">Sector <?=$sec1?></td>
		 <td class="table_head">Sector <?=$sec2?></td>
		 <td class="table_head">Sector <?=$sec3?></td>
		<? if ($CONFIG_4){ ?>
		 <td class="table_head">Sector <?=$sec4?></td>
		 <? } ?>
	  </tr>
	  <tr>
		 <td class="parameter_name" width="120px">State</td>
		 <td class="CURRENT_SAME"><?=$STATE_1?></td>
	 	 <td class="CURRENT_SAME"><?=$STATE_2?></td>
	 	 <td class="CURRENT_SAME"><?=$STATE_3?></td>
	 	 <? if ($CONFIG_4){ ?>
		 <td class="CURRENT_SAME"><?=$STATE_4?></td>
		 <? } ?>


 		 <td class="PLANNED_SAME"><?=$STATE_1?></td>
		 <td class="PLANNED_SAME"><?=$STATE_2?></td>
		 <td class="PLANNED_SAME"><?=$STATE_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="PLANNED_SAME"><?=$STATE_4?></td>
		 <? } ?>

	  </tr>
 	  <? $color=check_if_same($CONFIG_1,$pl_CONFIG_1,$CONFIG_2,$pl_CONFIG_2,$CONFIG_3,$pl_CONFIG_3,$CONFIG_4,$pl_CONFIG_4,$CONFIG_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Config</td>
		 <td class="<?=$color[1][1]?>"><?=$CONFIG_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$CONFIG_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$CONFIG_3?></td>
		 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[7][1]?>"><?=$CONFIG_4?></td>
		 <? } ?>


 		 <td class="<?=$color[4][1]?>"><?=$pl_CONFIG_1?></td>
 		 <td class="<?=$color[5][1]?>"><?=$pl_CONFIG_2?></td>
 		 <td class="<?=$color[6][1]?>"><?=$pl_CONFIG_3?></td>


		 <? if ($CONFIG_4){ ?>
          <td class="<?=$color[8][1]?>"><?=$pl_CONFIG_4?></td>
         </td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($TMA_1,$pl_TMA_1,$TMA_1,$pl_TMA_2,$TMA_1,$pl_TMA_3,$TMA_4,$pl_TMA_4,$TMA_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">TMA</td>
		 <td class="<?=$color[1][1]?>"><?=$TMA_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$TMA_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$TMA_3?></td>
		 <? if ($CONFIG_4){ ?>
		<td class="<?=$color[7][1]?>"><?=$TMA_4?></td>
		 <? } ?>

     	 <td class="<?=$color[4][1]?>"><?=$pl_TMA_1?></td>
     	 <td class="<?=$color[5][1]?>"><?=$pl_TMA_2?></td>
     	 <td class="<?=$color[6][1]?>"><?=$pl_TMA_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_TMA_4?></td>
		 <? } ?>
	  </tr>
 	  <?
 	  $color=check_if_same($FREQ_ACTIVE1_1,$pl_FREQ_ACTIVE1_1,$FREQ_ACTIVE1_2,$pl_FREQ_ACTIVE1_2,$FREQ_ACTIVE1_3,$pl_FREQ_ACTIVE1_3,$FREQ_ACTIVE1_4,$pl_FREQ_ACTIVE1_4,$FREQ_ACTIVE_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name"><b>FREQ active network CAB1</td>
		 <td class="<?=$color[1][1]?>"><?=$FREQ_ACTIVE1_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$FREQ_ACTIVE1_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$FREQ_ACTIVE1_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$FREQ_ACTIVE1_4?></td>
		 <? } ?>

 	 	 <td class="<?=$color[4][1]?>"><?=$pl_FREQ_ACTIVE1_1?> </td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_FREQ_ACTIVE1_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_FREQ_ACTIVE1_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_FREQ_ACTIVE1_4?></td>
		 <? } ?>
	  </tr>
	  <?
 	  if ($pl_NR_OF_CAB>=2){
 	  $color=check_if_same($FREQ_ACTIVE2_1,$pl_FREQ_ACTIVE2_1,$FREQ_ACTIVE2_2,$pl_FREQ_ACTIVE2_2,$FREQ_ACTIVE2_3,$pl_FREQ_ACTIVE2_3,$FREQ_ACTIVE2_4,$pl_FREQ_ACTIVE2_4,$FREQ_ACTIVE_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name"><b>FREQ active network CAB2</td>
		 <td class="<?=$color[1][1]?>"><?=$FREQ_ACTIVE2_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$FREQ_ACTIVE2_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$FREQ_ACTIVE2_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$FREQ_ACTIVE2_4?></td>
		 <? } ?>

 	 	 <td class="<?=$color[4][1]?>"><?=$pl_FREQ_ACTIVE2_1?> </td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_FREQ_ACTIVE2_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_FREQ_ACTIVE2_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_FREQ_ACTIVE2_4?></td>
		 <? } ?>
	  </tr>
	  <?
 	  }
 	  if ($pl_NR_OF_CAB>=3){
 	  $color=check_if_same($FREQ_ACTIVE3_1,$pl_FREQ_ACTIVE3_1,$FREQ_ACTIVE3_2,$pl_FREQ_ACTIVE3_2,$FREQ_ACTIVE3_3,$pl_FREQ_ACTIVE3_3,$FREQ_ACTIVE3_4,$pl_FREQ_ACTIVE3_4,$FREQ_ACTIVE_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name"><b>FREQ active network CAB2</td>
		 <td class="<?=$color[1][1]?>"><?=$FREQ_ACTIVE3_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$FREQ_ACTIVE3_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$FREQ_ACTIVE3_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$FREQ_ACTIVE3_4?></td>
		 <? } ?>

 	 	 <td class="<?=$color[4][1]?>"><?=$pl_FREQ_ACTIVE3_1?> </td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_FREQ_ACTIVE3_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_FREQ_ACTIVE3_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_FREQ_ACTIVE3_4?></td>
		 <? } ?>
	  </tr>
	  <?
 	  }
	   $FREQ_ACTIVE_1=trim($FREQ_ACTIVE_1);
	   $FREQ_ACTIVE_2=trim($FREQ_ACTIVE_2);
	   $FREQ_ACTIVE_3=trim($FREQ_ACTIVE_3);
	   $FREQ_ACTIVE_4=trim($FREQ_ACTIVE_4);

	  if (
	  (($FREQ_ACTIVE_1!= $FREQ_ACTIVE_SWITCH_1) && $FREQ_ACTIVE_SWITCH_1!= "") ||
	  (($FREQ_ACTIVE_2!= $FREQ_ACTIVE_SWITCH_2) && $FREQ_ACTIVE_SWITCH_1!= "") ||
	  (($FREQ_ACTIVE_3!= $FREQ_ACTIVE_SWITCH_3) && $FREQ_ACTIVE_SWITCH_1!= "") ||
	  (($FREQ_ACTIVE_4!= $FREQ_ACTIVE_SWITCH_4) && $FREQ_ACTIVE_SWITCH_1!= "")
	  ){
		  //if (($FREQ_ACTIVE_SWITCH_1!= "" && $FREQ_ACTIVE_1!=0) || ($FREQ_ACTIVE_SWITCH_2!= "" && $FREQ_ACTIVE_2!=0) || ($FREQ_ACTIVE_SWITCH_3!= "" && $FREQ_ACTIVE_3!=0)){
		?>
		<tr bgcolor='red'>
		 <td class='parameter_name'>Number active FREQ in SWITCH</td>
		 <td class='PLANNED_SAME'><?=$FREQ_ACTIVE_SWITCH_1?></td>
		 <td class='PLANNED_SAME'><?=$FREQ_ACTIVE_SWITCH_2?></td>
		 <td class='PLANNED_SAME'><?=$FREQ_ACTIVE_SWITCH_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class='PLANNED_SAME'><?=$FREQ_ACTIVE_SWITCH_4?></td>
		 <? } ?>
		 <td width='2px' bgcolor='Black'>&nbsp;</td>
		 <td class='PLANNED_SAME'><?=$FREQ_ACTIVE_SWITCH_1?></td>
		 <td class='PLANNED_SAME'><?=$FREQ_ACTIVE_SWITCH_2?></td>
		 <td class='PLANNED_SAME'><?=$FREQ_ACTIVE_SWITCH_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class='PLANNED_SAME'><?=$FREQ_ACTIVE_SWITCH_4?></td>
		 <? } ?>
	  </tr><?
	  	 // }
	   }
 	  $color=check_if_same($TRU_INST1_1_1,$pl_TRU_INST1_1_1,$TRU_INST1_1_2,$pl_TRU_INST1_1_2,$TRU_INST1_1_3,$pl_TRU_INST1_1_3,$TRU_INST1_1_4,$pl_TRU_INST1_1_4,$TRU_INST1_override,$general_override);
 	  $color2=check_if_same($TRU_TYPE1_1_1,$pl_TRU_TYPE1_1_1,$TRU_TYPE1_1_2,$pl_TRU_TYPE1_1_2,$TRU_TYPE1_1_3,$pl_TRU_TYPE1_1_3,$TRU_TYPE1_1_4,$pl_TRU_TYPE1_1_4,$TRU_TYPE1_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name"><b>TRU installed CAB1</b></td>
		 <td class="<?=$color[1][1]?>"><?=$TRU_INST1_1_1?> <?=$TRU_TYPE1_1_1?> &nbsp;&nbsp; <?=$TRU_INST1_2_1?> <?=$TRU_TYPE1_2_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$TRU_INST1_1_2?> <?=$TRU_TYPE1_1_2?> &nbsp;&nbsp; <?=$TRU_INST1_2_2?> <?=$TRU_TYPE1_2_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$TRU_INST1_1_3?> <?=$TRU_TYPE1_1_3?> &nbsp;&nbsp; <?=$TRU_INST1_2_3?> <?=$TRU_TYPE1_2_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$TRU_INST1_1_4?> <?=$TRU_TYPE1_1_4?> &nbsp;&nbsp; <?=$TRU_INST1_2_4?> <?=$TRU_TYPE1_2_4?></td>
		 <? } ?>

		 <td class="<?=$color[4][1]?>"><?=$pl_TRU_INST1_1_1?> <?=$pl_TRU_TYPE1_1_1?> &nbsp;&nbsp; <?=$pl_TRU_INST1_2_1?> <?=$pl_TRU_TYPE1_2_1?></td>
		 <td class="<?=$color[5][1]?>"><?=$pl_TRU_INST1_1_2?> <?=$pl_TRU_TYPE1_1_2?> &nbsp;&nbsp; <?=$pl_TRU_INST1_2_2?> <?=$pl_TRU_TYPE1_2_2?></td>
		 <td class="<?=$color[6][1]?>"><?=$pl_TRU_INST1_1_3?> <?=$pl_TRU_TYPE1_1_3?> &nbsp;&nbsp; <?=$pl_TRU_INST1_2_3?> <?=$pl_TRU_TYPE1_2_3?></td>
		 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[8][1]?>"><?=$pl_TRU_INST1_1_4?> <?=$pl_TRU_TYPE1_1_4?> &nbsp;&nbsp; <?=$pl_TRU_INST1_2_4?> <?=$pl_TRU_TYPE1_2_4?></td>
		 <? } ?>
	  </tr>
 	  <?
 	  if ($pl_NR_OF_CAB>=2){
 	  	$color=check_if_same($TRU_INST2_1_1,$pl_TRU_INST2_1_1,$TRU_INST2_1_2,$pl_TRU_INST2_1_2,$TRU_INST2_1_3,$pl_TRU_INST2_1_3,$TRU_INST2_1_4,$pl_TRU_INST2_1_4,$TRU_INST2_override,$general_override);
 	  	$color2=check_if_same($TRU_TYPE2_1_1,$pl_TRU_TYPE2_1_1,$TRU_TYPE2_1_2,$pl_TRU_TYPE2_1_2,$TRU_TYPE2_1_3,$pl_TRU_TYPE2_1_3,$TRU_TYPE2_1_4,$pl_TRU_TYPE2_1_4,$TRU_TYPE2_override,$general_override);
 	  ?>
 	  <tr>
		 <td class="parameter_name"><b>TRU installed CAB2</b></td>
		 <td class="<?=$color[1][1]?>"><?=$TRU_INST2_1_1?> <?=$TRU_TYPE2_1_1?> &nbsp;&nbsp; <?=$TRU_INST2_2_1?> <?=$TRU_TYPE2_2_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$TRU_INST2_1_2?> <?=$TRU_TYPE2_1_2?> &nbsp;&nbsp; <?=$TRU_INST2_2_2?> <?=$TRU_TYPE2_2_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$TRU_INST2_1_3?> <?=$TRU_TYPE2_1_3?> &nbsp;&nbsp; <?=$TRU_INST2_2_3?> <?=$TRU_TYPE2_2_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$TRU_INST2_1_4?> <?=$TRU_TYPE2_1_4?> &nbsp;&nbsp; <?=$TRU_INST2_2_4?> <?=$TRU_TYPE2_2_4?></td>
		 <? } ?>

		 <td class="<?=$color[4][1]?>"><?=$pl_TRU_INST2_1_1?> <?=$pl_TRU_TYPE2_1_1?> &nbsp;&nbsp; <?=$pl_TRU_INST2_2_1?> <?=$pl_TRU_TYPE2_2_1?></td>
		 <td class="<?=$color[5][1]?>"><?=$pl_TRU_INST2_1_2?> <?=$pl_TRU_TYPE2_1_2?> &nbsp;&nbsp; <?=$pl_TRU_INST2_2_2?> <?=$pl_TRU_TYPE2_2_2?></td>
		 <td class="<?=$color[6][1]?>"><?=$pl_TRU_INST2_1_3?> <?=$pl_TRU_TYPE2_1_3?> &nbsp;&nbsp; <?=$pl_TRU_INST2_2_3?> <?=$pl_TRU_TYPE2_2_3?></td>
		 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[8][1]?>"><?=$pl_TRU_INST2_1_4?> <?=$pl_TRU_TYPE2_1_4?> &nbsp;&nbsp; <?=$pl_TRU_INST2_2_4?> <?=$pl_TRU_TYPE2_2_4?></td>
		 <? } ?>
	  </tr>
 	  <?
 	  }
 	  if ($pl_NR_OF_CAB>=3){
  	  	$color=check_if_same($TRU_INST3_1_1,$pl_TRU_INST3_1_1,$TRU_INST3_1_2,$pl_TRU_INST3_1_2,$TRU_INST3_1_3,$pl_TRU_INST3_1_3,$TRU_INST3_1_4,$pl_TRU_INST3_1_4,$TRU_INST3_override,$general_override);
 	  	$color2=check_if_same($TRU_TYPE3_1_1,$pl_TRU_TYPE3_1_1,$TRU_TYPE3_1_2,$pl_TRU_TYPE3_1_2,$TRU_TYPE3_1_3,$pl_TRU_TYPE3_1_3,$TRU_TYPE3_1_4,$pl_TRU_TYPE3_1_4,$TRU_TYPE3_override,$general_override); ?>
	  <tr>
		 <td class="parameter_name"><b>TRU installed CAB3</b></td>
		 <td class="<?=$color[1][1]?>"><?=$TRU_INST3_1_1?> <?=$TRU_TYPE3_1_1?> &nbsp;&nbsp; <?=$TRU_INST3_2_1?> <?=$TRU_TYPE3_2_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$TRU_INST3_1_2?> <?=$TRU_TYPE3_1_2?> &nbsp;&nbsp; <?=$TRU_INST3_2_2?> <?=$TRU_TYPE3_2_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$TRU_INST3_1_3?> <?=$TRU_TYPE3_1_3?> &nbsp;&nbsp; <?=$TRU_INST3_2_3?> <?=$TRU_TYPE3_2_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$TRU_INST3_1_4?> <?=$TRU_TYPE3_1_4?> &nbsp;&nbsp; <?=$TRU_INST3_2_4?> <?=$TRU_TYPE3_2_4?></td>
		 <? } ?>

		 <td class="<?=$color[4][1]?>"><?=$pl_TRU_INST3_1_1?> <?=$pl_TRU_TYPE3_1_1?> &nbsp;&nbsp; <?=$pl_TRU_INST3_2_1?> <?=$pl_TRU_TYPE3_2_1?></td>
		 <td class="<?=$color[5][1]?>"><?=$pl_TRU_INST3_1_2?> <?=$pl_TRU_TYPE3_1_2?> &nbsp;&nbsp; <?=$pl_TRU_INST3_2_2?> <?=$pl_TRU_TYPE3_2_2?></td>
		 <td class="<?=$color[6][1]?>"><?=$pl_TRU_INST3_1_3?> <?=$pl_TRU_TYPE3_1_3?> &nbsp;&nbsp; <?=$pl_TRU_INST3_2_3?> <?=$pl_TRU_TYPE3_2_3?></td>
		 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[8][1]?>"><?=$pl_TRU_INST3_1_4?> <?=$pl_TRU_TYPE3_1_4?> &nbsp;&nbsp; <?=$pl_TRU_INST3_2_4?> <?=$pl_TRU_TYPE3_2_4?></td>
		 <? } ?>
	  </tr>
 	  <?
 	  }
 	  $color=check_if_same($ANTTYPE1_1,$pl_ANTTYPE1_1,$ANTTYPE1_2,$pl_ANTTYPE1_2,$ANTTYPE1_3,$pl_ANTTYPE1_3,$ANTTYPE1_4,$pl_ANTTYPE1_4,$ANTTYPE1_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Antenna Type 1</td>
		 <td class="<?=$color[1][1]?>"><?=$ANTTYPE1_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ANTTYPE1_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ANTTYPE1_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[4][1]?>"><?=$ANTTYPE1_4?></td>
		 <? } ?>

		 <td class="<?=$color[4][1]?>"><?=$pl_ANTTYPE1_1?></td>
		 <td class="<?=$color[4][1]?>"><?=$pl_ANTTYPE1_2?></td>
		 <td class="<?=$color[4][1]?>"><?=$pl_ANTTYPE1_3?></td>
   		 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[8][1]?>"><?=$pl_ANTTYPE1_4?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($ELECTILT1_1,$pl_ELECTILT1_1,$ELECTILT1_2,$pl_ELECTILT1_2,$ELECTILT1_3,$pl_ELECTILT1_3,$ELECTILT1_4,$pl_ELECTILT1_4,$ELECTILT1_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Elektrical downtilt 1</td>
		 <td class="<?=$color[1][1]?>"><?=$ELECTILT1_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ELECTILT1_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ELECTILT1_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$ELECTILT1_4?></td>
		 <? } ?>

	 	 <td class="<?=$color[4][1]?>"><?=$pl_ELECTILT1_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_ELECTILT1_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_ELECTILT1_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_ELECTILT1_4?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($MECHTILT1_1,$pl_MECHTILT1_1,$MECHTILT1_2,$pl_MECHTILT1_2,$MECHTILT1_3,$pl_MECHTILT1_3,$MECHTILT1_4,$pl_MECHTILT1_4,$ANTTYPE2_override,$general_override);
 	  	 $color2=check_if_same($MECHTILT1_1_t,$pl_MECHTILT1_1_t,$MECHTILT1_2_t,$pl_MECHTILT1_2_t,$MECHTILT1_3_t,$pl_MECHTILT1_3_t,$MECHTILT1_4_t,$pl_MECHTILT1_4_t,$pl_MECHTILT1_t_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Mechanical tilt 1</td>
		 <td class="<?=$color[1][1]?>"><?=$MECHTILT1_1?>&nbsp;<?=$MECHTILT1_1_t?></td>
		 <td class="<?=$color[2][1]?>"><?=$MECHTILT1_2?>&nbsp;<?=$MECHTILT1_2_t?></td>
		 <td class="<?=$color[3][1]?>"><?=$MECHTILT1_3?>&nbsp;<?=$MECHTILT1_3_t?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$MECHTILT1_4?>&nbsp;<?=$MECHTILT1_4_t?></td>
		 <? } ?>

	 	 <td class="<?=$color[4][1]?>"><?=$pl_MECHTILT1_1?>&nbsp;<?=$pl_MECHTILT1_1_t?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_MECHTILT1_2?>&nbsp;<?=$pl_MECHTILT1_2_t?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_MECHTILT1_3?>&nbsp;<?=$pl_MECHTILT1_3_t?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_MECHTILT1_4?>&nbsp;<?=$pl_MECHTILT1_4_t?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($ANTHEIGHT1_1,$pl_ANTHEIGHT1_1,$ANTHEIGHT1_2,$pl_ANTHEIGHT1_2,$ANTHEIGHT1_3,$pl_ANTHEIGHT1_3,$ANTHEIGHT1_4,$pl_ANTHEIGHT1_4,$ANTHEIGHT1_override,$general_override);
 	  	 $color2=check_if_same($ANTHEIGHT1_1_t,$pl_ANTHEIGHT1_1_t,$ANTHEIGHT1_2_t,$pl_ANTHEIGHT1_2_t,$ANTHEIGHT1_3_t,$pl_ANTHEIGHT1_3_t,$ANTHEIGHT1_4_t,$pl_ANTHEIGHT1_4_t,$ANTHEIGHT1_t_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Antenna Height 1</td>
		 <td class="<?=$color[1][1]?>"><?=$ANTHEIGHT1_1?>m<?=$ANTHEIGHT1_1_t?></td>
		 <td class="<?=$color[2][1]?>"><?=$ANTHEIGHT1_2?>m<?=$ANTHEIGHT1_2_t?></td>
		 <td class="<?=$color[3][1]?>"><?=$ANTHEIGHT1_3?>m<?=$ANTHEIGHT1_3_t?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$ANTHEIGHT1_4?>m<?=$ANTHEIGHT1_4_t?></td>
		 <? } ?>

	 	 <td class="<?=$color[4][1]?>"><?=$pl_ANTHEIGHT1_1?>m<?=$pl_ANTHEIGHT1_1_t?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_ANTHEIGHT1_2?>m<?=$pl_ANTHEIGHT1_2_t?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_ANTHEIGHT1_3?>m<?=$pl_ANTHEIGHT1_3_t?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_ANTHEIGHT1_4?>m<?=$pl_ANTHEIGHT1_4_t?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($ANTTYPE2_1,$pl_ANTTYPE2_1,$ANTTYPE2_2,$pl_ANTTYPE2_2,$ANTTYPE2_3,$pl_ANTTYPE2_3,$ANTTYPE2_4,$pl_ANTTYPE2_4,$ANTTYPE2_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Antenna Type 2</td>
		 <td class="<?=$color[1][1]?>"><?=$ANTTYPE2_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ANTTYPE2_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ANTTYPE2_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$ANTTYPE2_4?></td>
		 <? } ?>

 	 	 <td class="<?=$color[4][1]?>"><?=$pl_ANTTYPE2_1?></td>
 	 	 <td class="<?=$color[5][1]?>"><?=$pl_ANTTYPE2_2?></td>
 	 	 <td class="<?=$color[6][1]?>"><?=$pl_ANTTYPE2_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_ANTTYPE2_4?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($ELECTILT2_1,$pl_ELECTILT2_1,$ELECTILT2_2,$pl_ELECTILT2_2,$ELECTILT2_3,$pl_ELECTILT2_3,$ELECTILT2_4,$pl_ELECTILT2_4,$ELECTILT2_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Elektrical downtilt 2</td>
		 <td class="<?=$color[1][1]?>"><?=$ELECTILT2_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$ELECTILT2_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$ELECTILT2_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$ELECTILT2_4?></td>
		 <? } ?>

	 	 <td class="<?=$color[4][1]?>"><?=$pl_ELECTILT2_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_ELECTILT2_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_ELECTILT2_3?></td>
		 <? if ($CONFIG_4){ ?>
		<td class="<?=$color[8][1]?>"><?=$pl_ELECTILT2_4?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($MECHTILT2_1,$pl_MECHTILT2_1,$MECHTILT2_2,$pl_MECHTILT2_2,$MECHTILT2_3,$pl_MECHTILT2_3,$MECHTILT2_4,$pl_MECHTILT2_4,$MECHTILT2_override,$general_override);
 	  	 $color2=check_if_same($MECHTILT2_1_t,$pl_MECHTILT2_1_t,$MECHTILT2_2_t,$pl_MECHTILT2_2_t,$MECHTILT2_3_t,$pl_MECHTILT2_3_t,$MECHTILT2_4_t,$pl_MECHTILT2_4_t,$pl_MECHTILT2_t_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Mechanical tilt 2</td>
		 <td class="<?=$color[1][1]?>"><?=$MECHTILT2_1?>&nbsp;<?=$MECHTILT2_1_t?></td>
		 <td class="<?=$color[2][1]?>"><?=$MECHTILT2_2?>&nbsp;<?=$MECHTILT2_2_t?></td>
		 <td class="<?=$color[3][1]?>"><?=$MECHTILT2_3?>&nbsp;<?=$MECHTILT2_3_t?></td>
		 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[7][1]?>"><?=$MECHTILT2_4?>&nbsp;<?=$MECHTILT2_4_t?></td>
		 <? } ?>

 	 	 <td class="<?=$color[4][1]?>"><?=$pl_MECHTILT2_1?>&nbsp;<?=$pl_MECHTILT2_1_t?></td>
 	 	 <td class="<?=$color[5][1]?>"><?=$pl_MECHTILT2_2?>&nbsp;<?=$pl_MECHTILT2_2_t?></td>
 	 	 <td class="<?=$color[6][1]?>"><?=$pl_MECHTILT2_3?>&nbsp;<?=$pl_MECHTILT2_3_t?></td>
	 	 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[8][1]?>"><?=$pl_MECHTILT2_4?>&nbsp;<?=$pl_MECHTILT2_4_t?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($ANTHEIGHT2_1,$pl_ANTHEIGHT2_1,$ANTHEIGHT2_2,$pl_ANTHEIGHT2_2,$ANTHEIGHT2_3,$pl_ANTHEIGHT2_3,$ANTHEIGHT2_4,$pl_ANTHEIGHT2_4,$ANTHEIGHT2_override,$general_override);
 	 	 $color2=check_if_same($ANTHEIGHT2_1_t,$pl_ANTHEIGHT2_1_t,$ANTHEIGHT2_2_t,$pl_ANTHEIGHT2_2_t,$ANTHEIGHT2_3_t,$pl_ANTHEIGHT2_3_t,$ANTHEIGHT2_4_t,$pl_ANTHEIGHT2_4_t,$ANTHEIGHT2_t_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Antenna Height 2</td>
		 <td class="<?=$color[1][1]?>"><?=$ANTHEIGHT2_1?>m<?=$ANTHEIGHT2_1_t?></td>
		 <td class="<?=$color[2][1]?>"><?=$ANTHEIGHT2_2?>m<?=$ANTHEIGHT2_2_t?></td>
		 <td class="<?=$color[3][1]?>"><?=$ANTHEIGHT2_3?>m<?=$ANTHEIGHT2_3_t?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$ANTHEIGHT2_4?>m<?=$ANTHEIGHT2_4_t?></td>
		 <? } ?>

	     <td class="<?=$color[4][1]?>"><?=$pl_ANTHEIGHT2_1?>m<?=$pl_ANTHEIGHT2_1_t?></td>
	  	 <td class="<?=$color[5][1]?>"><?=$pl_ANTHEIGHT2_2?>m<?=$pl_ANTHEIGHT2_2_t?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_ANTHEIGHT2_3?>m<?=$pl_ANTHEIGHT2_3_t?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_ANTHEIGHT2_4?>m<?=$pl_ANTHEIGHT2_4_t?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($AZI_1,$pl_AZI_1,$AZI_2,$pl_AZI_2,$AZI_3,$pl_AZI_3,$AZI_4,$pl_AZI_4,$AZI_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Azimuth</td>
		 <td class="<?=$color[1][1]?>"><?=$AZI_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$AZI_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$AZI_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$AZI_4?></td>
		 <? } ?>

 	 	 <td class="<?=$color[4][1]?>"><?=$pl_AZI_1?></td>
 	 	 <td class="<?=$color[5][1]?>"><?=$pl_AZI_2?></td>
 	 	 <td class="<?=$color[6][1]?>"><?=$pl_AZI_3?></td>
		 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[8][1]?>"><?=$pl_AZI_4?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($FEEDER_1,$pl_FEEDER_1,$FEEDER_2,$pl_FEEDER_2,$FEEDER_3,$pl_FEEDER_3,$FEEDER_4,$pl_FEEDER_4,$FEEDER_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Feeder type</td>
		 <td class="<?=$color[1][1]?>"><?=$FEEDER_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$FEEDER_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$FEEDER_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$FEEDER_4?></td>
		 <? } ?>

 	 	 <td class="<?=$color[4][1]?>"><?=$pl_FEEDER_1?></td>
 	 	 <td class="<?=$color[5][1]?>"><?=$pl_FEEDER_2?></td>
 	 	 <td class="<?=$color[6][1]?>"><?=$pl_FEEDER_3?></td>
	     </td>
		 <? if ($CONFIG_4){ ?>
		  <td class="<?=$color[8][1]?>"><?=$pl_FEEDER_4?></td>
		 <? } ?>
	  </tr>
 	  <? $color=check_if_same($FEEDERLEN_1,$pl_FEEDERLEN_1,$FEEDERLEN_2,$pl_FEEDERLEN_2,$FEEDERLEN_3,$pl_FEEDERLEN_3,$FEEDERLEN_4,$pl_FEEDERLEN_4,$FEEDERLEN_override,$general_override);
 	  	 $color2=check_if_same($FEEDERLEN_1_t,$pl_FEEDERLEN_1_t,$FEEDERLEN_2_t,$pl_FEEDERLEN_2_t,$FEEDERLEN_3_t,$pl_FEEDERLEN_3_t,$FEEDERLEN_4_t,$pl_FEEDERLEN_4_t,$FEEDERLEN_t_override,$general_override); ?>
 	  <tr>
		 <td class="parameter_name">Feeder length</td>
		 <td class="<?=$color[1][1]?>"><?=$FEEDERLEN_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$FEEDERLEN_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$FEEDERLEN_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$FEEDERLEN_4?></td>
		 <? } ?>

 	 	 <td class="<?=$color[4][1]?>"><?=$pl_FEEDERLEN_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_FEEDERLEN_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_FEEDERLEN_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_FEEDERLEN_4?></td>
		 <? } ?>
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
 	<? $color=check_if_same($COMB_1,$pl_COMB_1,$COMB_2,$pl_COMB_2,$COMB_3,$pl_COMB_3,$COMB_4,$pl_COMB_4,$COMB_override,$general_override); ?>
	<tr>
		 <td class="parameter_name">Feedercombining</td>
		 <td class="<?=$color[1][1]?>"><?=$COMB_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$COMB_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$COMB_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$COMB_4?></td>
		 <? } ?>

	 	 <td class="<?=$color[4][1]?>"><?=$pl_COMB_1?></td>
		 <td class="<?=$color[5][1]?>"><?=$pl_COMB_2?></td>
		 <td class="<?=$color[6][1]?>"><?=$pl_COMB_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_COMB_4?></td>
		 <? } ?>
	</tr>
 	<? $color=check_if_same($DCBLOCK_1,$pl_DCBLOCK_1,$DCBLOCK_2,$pl_DCBLOCK_2,$DCBLOCK_3,$pl_DCBLOCK_3,$DCBLOCK_4,$pl_DCBLOCK_4,$DCBLOCK_override,$general_override); ?>
	<tr>
		 <td class="parameter_name">DC block</td>
		 <td class="<?=$color[1][1]?>"><?=$DCBLOCK_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$DCBLOCK_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$DCBLOCK_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$DCBLOCK_4?></td>
		 <? } ?>

	 	 <td class="<?=$color[4][1]?>"><?=$pl_DCBLOCK_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_DCBLOCK_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_DCBLOCK_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_DCBLOCK_4?></td>
		 <? } ?>
	 </tr>
	<? $color=check_if_same($HRACTIVE_1,$pl_HRACTIVE_1,$HRACTIVE_2,$pl_HRACTIVE_2,$HRACTIVE_3,$pl_HRACTIVE_3,$HRACTIVE_4,$pl_HRACTIVE_4,$HRACTIVE_override,$general_override); ?>
	<tr>
		 <td class="parameter_name">HR active upon integration</td>
		 <td class="<?=$color[1][1]?>"><?=$HRACTIVE_1?></td>
		 <td class="<?=$color[2][1]?>"><?=$HRACTIVE_2?></td>
		 <td class="<?=$color[3][1]?>"><?=$HRACTIVE_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[7][1]?>"><?=$HRACTIVE_4?></td>
		 <? } ?>

	 	 <td class="<?=$color[4][1]?>"><?=$pl_HRACTIVE_1?></td>
	 	 <td class="<?=$color[5][1]?>"><?=$pl_HRACTIVE_2?></td>
	 	 <td class="<?=$color[6][1]?>"><?=$pl_HRACTIVE_3?></td>
		 <? if ($CONFIG_4){ ?>
		 <td class="<?=$color[8][1]?>"><?=$pl_HRACTIVE_4?></td>
		 <? } ?>
	 </tr>
	 </table>
	 <?
	 $varname="pl_COMMENTS_".$type;
	 $$varname=$pl_COMMENTS;
	 ?>
</div>
</div>
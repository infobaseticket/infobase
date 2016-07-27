<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['band']){ 
	$band=$_POST['band'];
}
if ($band=="G9"){
	$lognode=$_POST['lognodeG9'];
}else if ($band=="G18"){
	$lognode=$_POST['lognode18'];
}else if ($band=="U9"){
	$lognode=$_POST['lognodeU9'];
}else if ($band=="U21"){
	$lognode=$_POST['lognodeU21'];
}else if ($band=="L8"){
	$lognode=$_POST['lognodeL8'];
}else if ($band=="L18"){
	$lognode=$_POST['lognodeL18'];
}else if ($band=="L26"){
	$lognode=$_POST['lognodeL26'];
}


$key_band=$band.$_POST['datakey'];

$cols_pl_sec=get_cols("BSDS_PL_SEC");
$cols_pl=get_cols("BSDS_PL_GEN");

$cols_cur_sec=get_cols("BSDS_CU_SEC");
$cols_cur=get_cols("BSDS_CU_GEN");

if ($_POST['print']!="yes"){
 	?>
	<script type="text/javascript">
	$(document).ready( function(){
		
		var band="<?=$band?>";
		if (band=='G9'){
			var filter='900';
		}else if (band=='G18'){
			var filter='1800';
		}else if (band=='L18'){
			var filter='1800';
		}else if (band=='U9'){
			var filter='900';
		}else if (band=='U21'){
			var filter='UMTS';
		}else if (band=='L26'){
			var filter='2600';
		}else if (band=='L8'){
			var filter='800';
		}else{
			var filter='';
		}
		
		get_select2(filter,'antenna','antenna_list'+band);
		get_select2('','cabtype','cabtype_list'+band);
		get_select2(filter,'feedertype','feeder_list'+band);
		get_select2('','config','config_list'+band);

		$('.feedershare_list'+band).select2({tags:["G9", "G18", "U9","U21","L8","L18","L26"]});

		if($('#pl_NR_OF_CAB'+band).val() == 1){
			$('.cab2'+band).hide();
			$('.cab3'+band).hide();
		}else if($('#pl_NR_OF_CAB'+band).val() == 2){
			$('.cab2'+band).show();
			$('.cab3'+band).hide();
		}
		$('#pl_NR_OF_CAB'+band).change(function(){

			if($(this).val() == 1){
				$('.cab2'+band).hide();
				$('.cab3'+band).hide();
			}
			if($(this).val() == 2){
				$('.cab2'+band).show();
				$('.cab3'+band).hide();
			}
			if($(this).val() == 3){
				$('.cab2'+band).show();
				$('.cab3'+band).show();
			}
		});

		if ($('#uniran<?=$_POST['datakey']?>').is(':checked')){
			$('.hideunirandata<?=$_POST['datakey']?>').hide();
			$('.hideunirandataG9<?=$_POST['datakey']?>').hide();
			$('.hideunirandataG18<?=$_POST['datakey']?>').hide();
		}else{
			$('.hideunirandata<?=$_POST['datakey']?>').show();
			$('.hideunirandataG9<?=$_POST['datakey']?>').show();
			$('.hideunirandataG18<?=$_POST['datakey']?>').show();
		}
	});
	</script>
	<?php
	}



if (empty($band)){
	echo "ERROR, no band specified!";
	die;
}

$check_current_exists=check_current_exists($band,$_POST['bsdskey'],$_POST['createddate'],'',$_POST['donor'],$_POST['frozen'],$_POST['candidate']);
$check_current_exists_SECTOR=check_current_exists($band,$_POST['bsdskey'],$_POST['createddate'],'all',$_POST['donor'],$_POST['frozen'],$_POST['candidate']);


if ($check_current_exists!=0 || $_POST['frozen']==1){
	$check_planned_exists=check_planned_exists($_POST['bsdskey'],$_POST['createddate'],$band,'allsec',$_POST['frozen'],$_POST['donor']);
	
	if ($check_planned_exists==="error"){
		echo "<h1>Sytem error</h1>There are too many records in the database for <?=$band?> BSDS! Please contact Frederick Eyland",
		die;
	}
}else{
	$check_planned_exists=0;
}

//echo $check_current_exists. "-".$check_planned_exists;
if($check_planned_exists==0 && $_POST['frozen']=="1"){
	echo "<div class='alert alert-warning'><h3>No planned data available for ".$band." when frozen!</h3>Please freeze again the PRE BSDS.</div>";
	if ($_POST['print']=="yes" && $check_planned_exists==0){
		die;
	}
}

if ($_POST['action']!="save" && $_POST['reloadAsset']!='yes'){
	if($check_planned_exists!="0"){
		include("planned_data.php");
	}
}

if ($_POST['frozen']==1){
	$updatable="<font color=red>*</font>";
}else{
	$updatable="";
}


include("current_data.php");

if ($check_planned_exists=="0" or $_POST['reloadAsset']=='yes'){
// If there is NO planned data, we first need to get current data out of the datbase
// because we need the state of secoters (we may not make PLANNED fields empty, but need to copy current stuff to planned)
	include("planned_data.php");
}
include("height_conversion.php");

if ($pl_NR_OF_CAB==1){
	$cab2="hidden";
	$cab3="hidden";
}else if ($pl_NR_OF_CAB==2){
	$cab3="hidden";
}

if ($_POST['frozen']==1){
	$color='BSDS_funded';
	$status='BSDS FUNDED (FROZEN)';
}else if ($_POST['rafid']!=''){
	$color='SITE_funded';
	$status='PRE BSDS';
}else{
	$color='BSDS_preready';
	$status='PRE BSDS';
}


/*******************************************************************************************************************/
/***********************************     OUTPUT DATA TO SCREEN  ****************************************************/
/*******************************************************************************************************************/

?>
<a name='vscroll<?=$key_band?>'></a>

<div id="banddataBSDS<?=$band?><?=$_POST['datakey']?>" class="<?=$printclass?>">
<form action="scripts/current_planned/save_pl_cu.php" method="post" id="current_planned_form<?=$key_band?>" role="form">
<input type="hidden" name="band" value="<?=$band?>">
<input type="hidden" name="pl_band" value="<?=$band?>">
<input type="hidden" name="action" value="save">
<input type="hidden" name="bsdskey" value="<?=$_POST['bsdskey']?>">
<input type="hidden" name="createddate" value="<?=$_POST['createddate']?>">
<input type="hidden" name="frozen" value="<?=$_POST['frozen']?>">
<input type="hidden" name="candidate" value="<?=$_POST['candidate']?>">

<input type="hidden" name="FEEDERSHARE_1" value="<?=$FEEDERSHARE_1?>" id="cur_FEEDERSHARE_1<?=$band?>">
<input type="hidden" name="FEEDERSHARE_2" value="<?=$FEEDERSHARE_2?>" id="cur_FEEDERSHARE_2<?=$band?>">
<input type="hidden" name="FEEDERSHARE_3" value="<?=$FEEDERSHARE_3?>" id="cur_FEEDERSHARE_3<?=$band?>">
<input type="hidden" name="FEEDERSHARE_4" value="<?=$FEEDERSHARE_4?>" id="cur_FEEDERSHARE_4<?=$band?>">
<input type="hidden" name="FEEDERSHARE_5" value="<?=$FEEDERSHARE_5?>" id="cur_FEEDERSHARE_5<?=$band?>">
<input type="hidden" name="FEEDERSHARE_6" value="<?=$FEEDERSHARE_6?>" id="cur_FEEDERSHARE_6<?=$band?>">

<?php	
for ($i=1;$i<=6;$i++){
	foreach ($cols_cur_sec['COLUMN_NAME'] as $key => $column) {
		$cur_parname=$column."_".$i;
		if ($column=="ANTHEIGHT1" || $column=="ANTHEIGHT2" || $column=="MECHTILT1" || $column=="MECHTILT2" || $column=="MECHTILT1" || $column=="FEEDERLEN"){
			$cur_parname2=$column."_".$i."_t";
			echo '<input type="hidden" name="'.$cur_parname2.'" value="'.$$cur_parname2.'" id="cur_'.$cur_parname2.$band.'">';
		}	
		echo '<input type="hidden" name="'.$cur_parname.'" value="'.$$cur_parname.'" id="cur_'.$cur_parname.$band.'">';
	}			
}	
foreach ($cols_cur['COLUMN_NAME'] as $key => $column) {
	$cur_parname=$column;	
	echo '<input type="hidden" name="'.$cur_parname.'" value="'.$$cur_parname.'" id="cur_'.$cur_parname.$band.'">';
}


if ($STATE_6){
	$colspan2=6;
	$colspan=12;
}else if ($STATE_5){
	$colspan2=5;
	$colspan=11;
}else if ($STATE_4){
	$colspan2=4;
	$colspan=10;
}else{
	$colspan2=3;
	$colspan=8;
}

$query = "Select * FROM ORQ_BSDS WHERE CANDIDATE LIKE '%".$_POST['candidate']."%'";
//echo $query;
$stmtORQ = parse_exec_fetch($conn_Infobase, $query, $error_str, $resORQ);
if (!$stmtORQ){
	die_silently($conn_Infobase, $error_str);
 	exit;
}else{
	OCIFreeStatement($stmtORQ);
	if (count($resORQ['SITE'])>0){
		echo "<div class='well well-sm' style='background-color:orange;'><b>ORQ ongoing:</b><br>";
		foreach ($resORQ['SITE'] as $key=>$attrib_id){
			echo "<b>".$resORQ['TICKETID'][$key]."</b>: ".$resORQ['DESCRIPTION'][$key];
			echo "<br>";
		}
		echo "</div>";
	}
}


if ($_POST['print']!='yes'){ 
	?>
	<div id="scrolls_<?=$key_band?>" class="scrolls bsdsScroll" style="display:none;">
		<button type="button" class="btn btn-default btn-xs leftArrow Arrows" data-scrollid="scroll<?=$key_band?>">
		  <span class="glyphicon glyphicon glyphicon-triangle-left" aria-hidden="true"></span>
		</button>
		<button type="button" class="btn btn-default btn-xs rightArrow Arrows" data-scrollid="scroll<?=$key_band?>">
		  <span class="glyphicon glyphicon glyphicon-triangle-right" aria-hidden="true"></span>
		</button>
	</div>

	<?php 

	$overflow="overflow-x: auto;";
}else{
	$overflow="";
}
?>


<div class="" style="width:100%;<?=$overflow?>" id="scroll<?=$key_band?>">
	<table class="table table-bordered table-condensed table-responsive-force" id="bsdsTable<?=$key_band?>">

	<?php
	$pl="";
	if ($check_current_exists==0 && $_POST['frozen']==1){
	?>
	<caption class="error">NO LIVE DATA AVAILABLE AT TIME OF FUNDING!</caption>
	<? } ?>
	<thead>
		<tr>
			<th style="min-width:150px;" class="tableheader <?=$color?>"><?=$_POST['candidate']?>   <?=$band?></th>
			<th colspan="<?=$colspan2?>" class="tableheader <?=$color?>">
				<?php if ($check_current_exists=="0"){ ?>
					<span class="savemessage"><font color='red'>NOT SAVED</font></span>
				<?php } ?>
				<?php if ($_POST['frozen']!=1){ ?>
				LIVE SITUATION from OSS/ASSET (<?php echo date('d-m-Y H:i:s'); ?>)
				<?php }else{ ?>
				SAVED SITUATION @ FUNDING/FREEZING (<?=$CHANGEDATE?>)
				<? } ?>
			</th>		
			<?	
			if ($_POST['frozen']!=2){
				$clas="tableheader";
				if ($check_planned_exists=="0"){
					$pl.="<span class='savemessage'><font color='red'>NOT SAVED</font></span>";
					$general_override="NOT_SAVED";
				}
				$pl.="PLANNED SITUATION ";
				if ($check_planned_exists==1){
					$pl.= "(Last update: ".$pl_TECHNO_CHANGEDATE.")";
				}
			?>
			<th colspan="<?=$colspan2?>" class="tableheader <?=$color?> borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>">
			<?=$pl?>
			<?php if ($_POST['print']!="yes"){ ?>
			<button type="button" class="btn btn-danger btn-xs clear pull-right" name="clear" data-table="bsdsTable<?=$key_band?>">Clear</button>
			<?php } ?>
			<?php if ($_POST['print']!="yes" && $check_planned_exists_UMTS=="0"){ 
				$hidebtn="hidden";
			} ?>
			</th>
			<?php } ?>
	 	</tr>
	 	</thead>
	 	<tbody>
		<tr>
			<td></td>
			<td colspan="<?=$colspan2?>">
				<table width="100%">
				<tr>
					<td class="tableheader hideunirandata<?=$_POST['datakey']?>">Cabinettype</td>
					<td class="hideunirandata<?=$_POST['datakey']?>"><?=$CABTYPE?></td>
					<td class="tableheader hideunirandata<?=$_POST['datakey']?>"><font color="blue">Solution</td>
					<td class="hideunirandata<?=$_POST['datakey']?>"><SELECT NAME="PLAYSTATION" id="PLAYSTATION<?=$band?>" class="form-control"><option selected><?=$PLAYSTATION?><option>NONE</option><option>Normal</option><option>Playstation</option></select></td>	
				</tr>
				<?php if ($band=='G9' or $band=='G18'){ ?>
				<tr>
					<td class="tableheader">CDU Type</td>
					<td><?=$CDUTYPE?></td>
					<td class="tableheader"><font color="blue">BBS</font></td>
					<td><SELECT NAME="BBS" id="BBS<?=$band?>" class="form-control"><? echo get_select_BBS($BBS);?></select></td>
				</tr>
				<tr>
					<td class="tableheader"># cabinet <?=$band?></td>
					<td><?=$NR_OF_CAB?></td>	
					<td class="tableheader"><font color="blue">DXU CAB 1</font></td>
					<td><SELECT NAME="DXUTYPE1" id="DXUTYPE1<?=$band?>" class="form-control"><? echo get_select_DXU($DXUTYPE1); ?> </SELECT></td>	
				</tr>
				<tr>
					<td class="tableheader"><span class="cab2<?=$band?> <?=$cab2?>"><font color="blue">DXU CAB 2</font></span></td>
					<td><span class="cab2<?=$band?> <?=$cab2?>"><SELECT NAME="DXUTYPE2" id="DXUTYPE2<?=$band?>" class="form-control"><? echo get_select_DXU($DXUTYPE2); ?></SELECT></span></td>
					<td class="tableheader"><span class="cab3<?=$band?> <?=$cab3?>"><font color="blue">DXU CAB 3</font></span></td>
					<td><span class="cab3<?=$band?> <?=$cab3?>"><SELECT NAME="DXUTYPE3" id="DXUTYPE3<?=$band?>" class="form-control"><? echo get_select_DXU($DXUTYPE3); ?> </SELECT></span></td>
				</tr>
				<?php }else{ ?>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name"><font color="blue">Power supply</font></td>
					<td><select NAME="POWERSUP" id="POWERSUP<?=$band?>" class="form-control"><option selected><?=$POWERSUP?><option>ACTURA CS</option><option>B900</option><option>DC/DC</option><option>PC8910A</option><option>B121</option><option>B201</option><option>NONE</option></select></td>
					<td class="parameter_name"><font color="blue">Data Service</font></td>
					<td><select NAME="PSU" id="PSU<?=$band?>" class="form-control"><option selected><?=$SERVICE?></option><option>R99</option><option>HSDPA</option></select></td>
				</tr>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name">BPC</td>
					<td><select NAME="BPC" id="BPC<?=$band?>" class="form-control"><? get_select_numbers($BPC,0,5,1,'no');?></td>
					<td class="parameter_name">BPK</td>
					<td><select NAME="BPK" id="BPK<?=$band?>" class="form-control"><? get_select_numbers($BPK,0,4,1,'no');?></td>
				</tr>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name">CC</td>
					<td><select NAME="CC" id="CC<?=$band?>" class="form-control"><option selected><?=$CC?></option><option value="CC">CC</option><option value="CC16">CC16</option><option value="CC16B">CC16B</option><option value="NONE">NONE</option><option value=""></option></select></td>
					<td class="parameter_name">BPL</td>
					<td><select NAME="BPL" id="BPL<?=$band?>" class="form-control"><? get_select_numbers($BPL,0,4,1,'FS5');?></td>
				</tr>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name">BPN2</td>
					<td><select NAME="BPN2" id="BPN2<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($BPN2,0,4,1,'no');?></td>
					<td class="parameter_name">PM0</td>
					<td><select NAME="PM0" id="PM0<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($PM0,0,4,1,'no');?></td>
				</tr>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name">FS5</td>
					<td><select NAME="FS5" id="FS5<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($FS5,1,2,1,'no');?></td>
					<td class="parameter_name">RECTIFIERS</td>
					<td><select NAME="RECT" id="RECT<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($RECT,1,4,1,'no');?></td>
				</tr>
				<?php } ?>
				</table>
			</td>
			<?php if ($_POST['frozen']!=2){ ?>
			<td colspan="<?=$colspan2?>" class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>">
				<table width="100%">
				<tr>
					<td class="tableheader hideunirandata<?=$_POST['datakey']?>">Cabinettype</td>
					<td class="hideunirandata<?=$_POST['datakey']?>"><input type="text" value="<?=$pl_CABTYPE?>" name="pl_CABTYPE" id="pl_CABTYPE<?=$band?>" placeholder="Select cabinet..." tabindex="-1" class="dynamic cabtype_list<?=$band?> form-control"></td>
					<td class="tableheader hideunirandata<?=$_POST['datakey']?>"><font color="blue">Solution</font></td>
					<td class="hideunirandata<?=$_POST['datakey']?>"><SELECT NAME="pl_PLAYSTATION" id="pl_PLAYSTATION<?=$band?>" class="tabledata cleardata form-control"><option selected><?=$pl_PLAYSTATION?><option>Normal</option><option>Playstation</option></select></td>
				 </tr>
				 <?php if ($band=='G9' or $band=='G18'){ ?>
				 <tr>
					<td class="tableheader">CDU Type</td>
					<td><SELECT NAME="pl_CDUTYPE" class="tabledata cleardata form-control cabtype_changer" id="pl_CDUTYPE<?=$band?>">
					<? echo get_select_CDU($pl_CDUTYPE); ?></SELECT>
					</td>
					<td class="tableheader"><font color="blue">BBS</font></td>
					<td><SELECT NAME="pl_BBS" class="tabledata cleardata form-control" id="pl_BBS<?=$band?>"><? echo get_select_BBS($pl_BBS);?></SELECT></td>
				</tr>
				<tr>
					<td class="tableheader"># cabinet <?=$band?></td>
					<td><SELECT NAME="pl_NR_OF_CAB" class="tabledata cleardata form-control" id="pl_NR_OF_CAB<?=$band?>">
					<? get_select_numbers($pl_NR_OF_CAB,0,3,1,'no');?></td>
					<td class="tableheader"><font color="blue">DXU CAB 1</font></td>
					<td><SELECT NAME="pl_DXUTYPE1" class="tabledata cleardata form-control cabtype_changer" id="pl_DXUTYPE1<?=$band?>">	<? echo get_select_DXU($pl_DXUTYPE1); ?></SELECT></span></td>	
				</tr>
				<tr>
					<td class="tableheader"><span class="cab2<?=$band?> <?=$cab2?>"><font color="blue">DXU CAB 2</font></span></td>
					<td><span class="cab2<?=$band?> <?=$cab2?>"><SELECT NAME="pl_DXUTYPE2" class="tabledata cleardata form-control" id="pl_DXUTYPE2<?=$band?>"><? echo get_select_DXU($pl_DXUTYPE2); ?></SELECT></span></td>
					<td class="tableheader"><span class="cab3<?=$band?> <?=$cab3?>"><font color="blue">DXU CAB 3</font></span></td>
					<td><span class="cab3<?=$band?> <?=$cab3?>"><SELECT NAME="pl_DXUTYPE3" class="tabledata cleardata form-control" id="pl_DXUTYPE3<?=$band?>"><? echo get_select_DXU($pl_DXUTYPE3); ?></SELECT></span></td>
				</tr>
				<?php }else{ ?>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name"><font color="blue">Power supply</font></td>
					<td><select NAME="pl_POWERSUP" id="pl_POWERSUP<?=$band?>" class="form-control"><option selected><?=$POWERSUP?><option>ACTURA CS</option><option>B900</option><option>DC/DC</option><option>PC8910A</option><option>B121</option><option>B201</option><option>NONE</option></select></td>
					<td class="parameter_name"><font color="blue">Data Service</font></td>
					<td><select NAME="pl_PSU" id="pl_PSU<?=$band?>" class="form-control"><option selected><?=$SERVICE?></option><option>R99</option><option>HSDPA</option></select></td>
				</tr>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name">BPC</td>
					<td><select NAME="pl_BPC" id="pl_BPC<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_BPC,0,5,1,'no');?></td>
					<td class="parameter_name">BPK</td>
					<td><select NAME="pl_BPK" id="pl_BPK<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_BPK,0,4,1,'no');?></td>
				</tr>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name">CC</td>
					<td><select NAME="pl_CC" id="pl_CC<?=$band?>" class="tabledata cleardata form-control"><option selected><?=$pl_CC?></option><option value="CC">CC</option><option value="CC16">CC16</option><option value="CC16B">CC16B</option><option value="NONE">NONE</option><option value=""></option></select></td>
					<td class="parameter_name">BPL</td>
					<td><select NAME="pl_BPL" id="pl_BPL<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_BPL,0,4,1,'FS5');?></td>
				</tr>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name">BPN2</td>
					<td><select NAME="pl_BPN2" id="pl_BPN2<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_BPN2,0,4,1,'no');?></td>
					<td class="parameter_name">PM0</td>
					<td><select NAME="pl_PM0" id="pl_PM0<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_PM0,0,4,1,'no');?></td>
				</tr>
				<tr class="hideunirandata<?=$_POST['datakey']?>">
					<td class="parameter_name">FS5</td>
					<td><select NAME="pl_FS5" id="pl_FS5<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FS5,1,2,1,'no');?></td>
					<td class="parameter_name">RECTIFIERS</td>
					<td><select NAME="pl_RECT" id="pl_RECT<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_RECT,1,4,1,'no');?></td>
				</tr>
				<?php } ?>
				</table>
			</td>
			<?php } ?>
		</tr>
		<tr>
			 <td>&nbsp;</td>
			 <td class="tableheader" style="min-width:150px;"><?=$SECTORID_1?></td>
			 <td class="tableheader" style="min-width:150px;"><?=$SECTORID_2?></td>
			 <td class="tableheader" style="min-width:150px;"><?=$SECTORID_3?></td>
			 <? if ($STATE_4){ ?>
			 <td class="tableheader" style="min-width:150px;"><?=$SECTORID_4?></td>
			<? }
			if ($STATE_5){ ?>
			 <td class="tableheader" style="min-width:150px;"><?=$SECTORID_5?></td>
			 <? } 
			 if ($STATE_6){ ?>
			 <td class="tableheader" style="min-width:150px;"><?=$SECTORID_6?></td>
			 <? } 
			 if ($_POST['frozen']!=2){ ?>
			 <td class="tableheader borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$SECTORID_1?></td>
			 <td class="tableheader hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$SECTORID_2?></td>
			 <td class="tableheader hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$SECTORID_3?></td>
			 <? if ($STATE_4){ ?>
			 <td class="tableheader hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$SECTORID_4?></td>
			 <? } 
			 if ($STATE_5){ ?>
			 <td class="tableheader hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$SECTORID_5?></td>
			 <? } 
			 if ($STATE_6){ ?>
			 <td class="tableheader hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$SECTORID_6?></td>
			 <? } 
			}?>
		</tr>
		<tr>
			 <td class="tableheader" width="120px">State</td>
			 <td><?=$STATE_1?></td>
			 <td><?=$STATE_2?></td>
			 <td><?=$STATE_3?></td>
			 <? if ($STATE_4){ ?>
			 <td><?=$STATE_4?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td><?=$STATE_5?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td><?=$STATE_6?></td>
			 <? } 
			 if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$STATE_1?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$STATE_2?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$STATE_3?></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"<?=$STATE_4?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$STATE_5?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><?=$STATE_6?></td>
			 <? } 
			}?>
		</tr>
		<?php 
		if ($band=='G9' or $band=='G18'){ ?>
			  <tr>
				 <td class="tableheader">Config</td>
				 <td><?=$CONFIG_1?></td>
				 <td><?=$CONFIG_2?></td>
				 <td><?=$CONFIG_3?></td>
				 <? if ($STATE_4){ ?>
				  <td><?=$CONFIG_4?></td>
				 <? }
				 if ($STATE_5){ ?>
				  <td><?=$CONFIG_5?></td>
				 <? }
				 if ($STATE_6){ ?>
				  <td><?=$CONFIG_6?></td>
				 <? } 
				 if ($_POST['frozen']!=2){ ?>
				 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>">
				 <input type="text" name='pl_CONFIG_1' value="<?=$pl_CONFIG_1?>" class="dynamic form-control config_list<?=$band?>" id="pl_CONFIG_1<?=$band?>"  placeholder="Select config..." /></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_CONFIG_2' value="<?=$pl_CONFIG_2?>" class="dynamic form-control config_list<?=$band?>" id="pl_CONFIG_2<?=$band?>"  placeholder="Select config..." /></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_CONFIG_3' value="<?=$pl_CONFIG_3?>" class="dynamic form-control config_list<?=$band?>" id="pl_CONFIG_3<?=$band?>"  placeholder="Select config..." /></td>
				 <? if ($STATE_4){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_CONFIG_4' value="<?=$pl_CONFIG_4?>" class="dynamic form-control config_list<?=$band?>" id="pl_CONFIG_4<?=$band?>"  placeholder="Select config..." /></td>
				 <? }
				 if ($STATE_5){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_CONFIG_5' value="<?=$pl_CONFIG_5?>" class="dynamic form-control config_list<?=$band?>" id="pl_CONFIG_5<?=$band?>"  placeholder="Select config..." /></td>
				 <? }
				 if ($STATE_6){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_CONFIG_6' value="<?=$pl_CONFIG_6?>" class="dynamic form-control config_list<?=$band?>" id="pl_CONFIG_6<?=$band?>"  placeholder="Select config..." /></td>
				 <? } 
				}?>
			</tr>

			<tr>
				 <td class="tableheader"><font color="blue">TMA</td>
				 <td><SELECT NAME="TMA_1" id="TMA_1<?=$band?>" class="form-control"><? echo get_select_TMA($TMA_1);?></SELECT></td>
				 <td><SELECT NAME="TMA_2" id="TMA_2<?=$band?>" class="form-control"><? echo get_select_TMA($TMA_2);?></SELECT></td>
				 <td><SELECT NAME="TMA_3" id="TMA_3<?=$band?>" class="form-control"><? echo get_select_TMA($TMA_3);?></SELECT></td>
				 <? if ($STATE_4){ ?>
				 <td><SELECT NAME="TMA_4" id="TMA_4<?=$band?>" class="form-control"><? echo get_select_TMA($TMA_4);?></SELECT></td>
				 <? }
				 if ($STATE_5){ ?>
				 <td><SELECT NAME="TMA_5" id="TMA_5<?=$band?>" class="form-control"><? echo get_select_TMA($TMA_5);?></SELECT></td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td><SELECT NAME="TMA_6" id="TMA_6<?=$band?>" class="form-control"><? echo get_select_TMA($TMA_6);?></SELECT></td>
				 <? }  
				 if ($_POST['frozen']!=2){ ?>
				 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_TMA_1" class="tabledata cleardata form-control" id="pl_TMA_1<?=$band?>"><? echo get_select_TMA($pl_TMA_1);?></SELECT></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_TMA_2" class="tabledata cleardata form-control" id="pl_TMA_2<?=$band?>"><? echo get_select_TMA($pl_TMA_2);?></SELECT></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_TMA_3" class="tabledata cleardata form-control" id="pl_TMA_3<?=$band?>"><? echo get_select_TMA($pl_TMA_3);?></SELECT></td>
				 <? if ($STATE_4){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_TMA_4" class="tabledata cleardata form-control" id="pl_TMA_4<?=$band?>"><? echo get_select_TMA($pl_TMA_4);?></SELECT></td>
				 <? } 
				 if ($STATE_5){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_TMA_5" class="tabledata cleardata form-control" id="pl_TMA_5<?=$band?>"><? echo get_select_TMA($pl_TMA_5);?></SELECT></td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_TMA_6" class="tabledata cleardata form-control" id="pl_TMA_6<?=$band?>"><? echo get_select_TMA($pl_TMA_6);?></SELECT></td>
				 <? } 
				}?>
			  </tr>
			  <tr>
				 <td class="tableheader">FREQ active network CAB1</td>
				 <td><?=$FREQ_ACTIVE1_1?></td>
				 <td><?=$FREQ_ACTIVE1_2?></td>
				 <td><?=$FREQ_ACTIVE1_3?></td>
				 <? if ($STATE_4){ ?>
				 <td><?=$FREQ_ACTIVE1_4?></td>
				 <? }
				 if ($STATE_5){ ?>
				 <td><?=$FREQ_ACTIVE1_5?></td>
				 <? }
				 if ($STATE_6){ ?>
				 <td><?=$FREQ_ACTIVE1_6?></td>
				 <? } 
				 if ($_POST['frozen']!=2){ ?>
				 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE1_1" id="pl_FREQ_ACTIVE1_1<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_1,0,12,1,'no');?></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE1_2" id="pl_FREQ_ACTIVE1_2<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_2,0,12,1,'no');?></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE1_3" id="pl_FREQ_ACTIVE1_3<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_3,0,12,1,'no');?></td>
				 <? if ($STATE_4){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE1_4" id="pl_FREQ_ACTIVE1_4<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_4,0,12,1,'no');?></td>
				 <? }
				 if ($STATE_5){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE1_5" id="pl_FREQ_ACTIVE1_5<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_5,0,12,1,'no');?></td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE1_6" id="pl_FREQ_ACTIVE1_6<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_6,0,12,1,'no');?></td>
				 <? }  
				}?>
			</tr>
			<tr class="cab2<?=$band?> <?=$cab2?>">
				 <td class="tableheader">FREQ active network CAB2</td>
				 <td><?=$FREQ_ACTIVE2_1?></td>
				 <td><?=$FREQ_ACTIVE2_2?></td>
				 <td><?=$FREQ_ACTIVE2_3?></td>
				 <? if ($STATE_4){ ?>
				 <td><?=$FREQ_ACTIVE2_4?></td>
				 <? } 
				 if ($STATE_5){ ?>
				 <td><?=$FREQ_ACTIVE2_5?></td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td><?=$FREQ_ACTIVE2_6?></td>
				 <? } 
				 if ($_POST['frozen']!=2){ ?>
				 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE2_1" id="pl_FREQ_ACTIVE2_1<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_1,0,12,1,'no');?> </td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE2_2" id="pl_FREQ_ACTIVE2_2<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_2,0,12,1,'no');?></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE2_3" id="pl_FREQ_ACTIVE2_3<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_3,0,12,1,'no');?></td>
				 <? if ($STATE_4){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE2_4" id="pl_FREQ_ACTIVE2_4<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_4,0,12,1,'no');?></td>
				 <? } 
				 if ($STATE_5){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE2_5" id="pl_FREQ_ACTIVE2_5<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_5,0,12,1,'no');?></td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE2_6" id="pl_FREQ_ACTIVE2_6<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_6,0,12,1,'no');?></td>
				 <? } 
				}?>
			</tr>
			<tr class="cab3<?=$band?> <?=$cab3?>">
				 <td class="tableheader">FREQ active network CAB2</td>
				 <td><?=$FREQ_ACTIVE3_1?></td>
				 <td><?=$FREQ_ACTIVE3_2?></td>
				 <td><?=$FREQ_ACTIVE3_3?></td>
				 <? if ($STATE_4){ ?>
				 <td><?=$FREQ_ACTIVE3_4?></td>
				 <? }
				 if ($STATE_5){ ?>
				 <td><?=$FREQ_ACTIVE3_5?></td>
				 <? }
				 if ($STATE_6){ ?>
				 <td><?=$FREQ_ACTIVE3_6?></td>
				 <? } 
				 if ($_POST['frozen']!=2){ ?>
				 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE3_1" id="pl_FREQ_ACTIVE3_1<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_1,0,12,1,'no');?> </td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE3_2" id="pl_FREQ_ACTIVE3_2<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_2,0,12,1,'no');?></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE3_3" id="pl_FREQ_ACTIVE3_3<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_3,0,12,1,'no');?></td>
				 <? if ($STATE_4){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE3_4" id="pl_FREQ_ACTIVE3_4<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_4,0,12,1,'no');?></td>
				 <? }
				 if ($STATE_5){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE3_5" id="pl_FREQ_ACTIVE3_5<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_5,0,12,1,'no');?></td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FREQ_ACTIVE3_6" id="pl_FREQ_ACTIVE3_6<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_6,0,12,1,'no');?></td>
				 <? } 

				}?>
			</tr>		  
			<tr>
				 <td class="tableheader">TRU installed CAB1</td>
				 <td><?=$TRU_INST1_1_1?> <?=$TRU_TYPE1_1_1?><br><?=$TRU_INST1_2_1?> <?=$TRU_TYPE1_2_1?></td>
				 <td><?=$TRU_INST1_1_2?> <?=$TRU_TYPE1_1_2?><br><?=$TRU_INST1_2_2?> <?=$TRU_TYPE1_2_2?></td>
				 <td><?=$TRU_INST1_1_3?> <?=$TRU_TYPE1_1_3?><br><?=$TRU_INST1_2_3?> <?=$TRU_TYPE1_2_3?></td>
				 <? if ($STATE_4){ ?>
				 <td><?=$TRU_INST1_1_4?> <?=$TRU_TYPE1_1_4?> &nbsp; <?=$TRU_INST1_2_4?> <?=$TRU_TYPE1_2_4?></td>
				 <? } 
				 if ($STATE_5){ ?>
				 <td><?=$TRU_INST1_1_5?> <?=$TRU_TYPE1_1_5?> &nbsp; <?=$TRU_INST1_2_5?> <?=$TRU_TYPE1_2_5?></td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td><?=$TRU_INST1_1_6?> <?=$TRU_TYPE1_1_6?> &nbsp; <?=$TRU_INST1_2_6?> <?=$TRU_TYPE1_2_6?></td>
				 <? } 
				if ($_POST['frozen']!=2){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST1_1_1" id="pl_TRU_INST1_1_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_1_1,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_1_1" id="pl_TRU_TYPE1_1_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_1);?></select><br>
				 <SELECT NAME="pl_TRU_INST1_2_1" id="pl_TRU_INST1_2_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_2_1,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_2_1" id="pl_TRU_TYPE1_2_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><?echo get_select_TRU($pl_TRU_TYPE1_2_1);?></select>
				 </td>
				 <td>
				 <SELECT NAME="pl_TRU_INST1_1_2" id="pl_TRU_INST1_1_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_1_2,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_1_2" id="pl_TRU_TYPE1_1_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_2);?></select><br>
				 <SELECT NAME="pl_TRU_INST1_2_2" id="pl_TRU_INST1_2_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_2_2,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_2_2" id="pl_TRU_TYPE1_2_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_2_2);?></select>
				 </td>
				 <td>
				 <SELECT NAME="pl_TRU_INST1_1_3" id="pl_TRU_INST1_1_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_1_3,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_1_3" id="pl_TRU_TYPE1_1_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_3);?></select><br>
				 <SELECT NAME="pl_TRU_INST1_2_3" id="pl_TRU_INST1_2_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_2_3,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_2_3" id="pl_TRU_TYPE1_2_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_2_3);?></select>
				 </td>
				 <? if ($STATE_4){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST1_1_4" id="pl_TRU_INST1_1_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_1_4,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_1_4" id="pl_TRU_TYPE1_1_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_4);?></select><br>
				 <SELECT NAME="pl_TRU_INST1_2_4" id="pl_TRU_INST1_2_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_2_4,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_2_4" id="pl_TRU_TYPE1_2_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_2_4);?></select>
				 </td>
				 <? } 
				 if ($STATE_5){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST1_1_5" id="pl_TRU_INST1_1_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_1_5,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_1_5" id="pl_TRU_TYPE1_1_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_5);?></select><br>
				 <SELECT NAME="pl_TRU_INST1_2_5" id="pl_TRU_INST1_2_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_2_5,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_2_5" id="pl_TRU_TYPE1_2_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_2_5);?></select>
				 </td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST1_1_6" id="pl_TRU_INST1_1_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_1_6,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_1_6" id="pl_TRU_TYPE1_1_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_6);?></select><br>
				 <SELECT NAME="pl_TRU_INST1_2_6" id="pl_TRU_INST1_2_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST1_2_6,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE1_2_6" id="pl_TRU_TYPE1_2_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_2_6);?></select>
				 </td>
				 <? } 
				}?>
			  </tr>
			   <?php 	  
			  	if ($_POST['frozen']=="FUND"){
			  		analyse_changes_Asset('TRU_INST1_1',$ASSET_TRU_INST1_1_1,$ASSET_TRU_INST1_1_2,$ASSET_TRU_INST1_1_3,$ASSET_TRU_INST1_1_4,$ASSET_TRU_INST1_1_5,$ASSET_TRU_INST1_1_6,$TRU_INST1_1_1,$TRU_INST1_1_2,$TRU_INST1_1_3,$TRU_INST1_1_4,$TRU_INST1_1_5,$TRU_INST1_1_6);
			  		analyse_changes_Asset('TRU_TYPE1_1',$ASSET_TRU_TYPE1_1_1,$ASSET_TRU_TYPE1_1_2,$ASSET_TRU_TYPE1_1_3,$ASSET_TRU_TYPE1_1_4,$ASSET_TRU_TYPE1_1_5,$ASSET_TRU_TYPE1_1_6,$TRU_TYPE1_1_1,$TRU_TYPE1_1_2,$TRU_TYPE1_1_3,$TRU_TYPE1_1_4,$TRU_TYPE1_1_5,$TRU_TYPE1_1_6);
			  		analyse_changes_Asset('TRU_INST1_2',$ASSET_TRU_INST1_2_1,$ASSET_TRU_INST1_2_2,$ASSET_TRU_INST1_2_3,$ASSET_TRU_INST1_2_4,$ASSET_TRU_INST1_2_5,$ASSET_TRU_INST1_2_6,$TRU_INST1_2_1,$TRU_INST1_2_2,$TRU_INST1_2_3,$TRU_INST1_2_4,$TRU_INST1_2_5,$TRU_INST1_2_6);
			  		analyse_changes_Asset('TRU_TYPE1_2',$ASSET_TRU_TYPE1_2_1,$ASSET_TRU_TYPE1_2_2,$ASSET_TRU_TYPE1_2_3,$ASSET_TRU_TYPE1_2_4,$ASSET_TRU_TYPE1_2_5,$ASSET_TRU_TYPE1_2_6,$TRU_TYPE1_2_1,$TRU_TYPE1_2_2,$TRU_TYPE1_2_3,$TRU_TYPE1_2_4,$TRU_TYPE1_2_5,$TRU_TYPE1_2_6);
			  	}
			   ?>
			  <tr class="cab2<?=$band?> <?=$cab2?>">
				 <td class="tableheader" width="120px">TRU installed CAB2</td>
				 <td><?=$TRU_INST2_1_1?> <?=$TRU_TYPE2_1_1?><br><?=$TRU_INST2_2_2?> <?=$TRU_TYPE2_2_1?></td>
				 <td><?=$TRU_INST2_1_2?> <?=$TRU_TYPE2_1_2?><br><?=$TRU_INST2_2_3?> <?=$TRU_TYPE2_2_2?></td>
				 <td><?=$TRU_INST2_1_3?> <?=$TRU_TYPE2_1_3?><br><?=$TRU_INST2_2_3?> <?=$TRU_TYPE2_2_3?></td>
				 <? if ($STATE_4){ ?>
				  <td><?=$TRU_INST2_1_4?> <?=$TRU_TYPE2_1_4?> &nbsp; <?=$TRU_INST2_2_4?> <?=$TRU_TYPE2_2_4?></td>
				 <? } 
				  if ($STATE_5){ ?>
				  <td><?=$TRU_INST2_1_5?> <?=$TRU_TYPE2_1_5?> &nbsp; <?=$TRU_INST2_2_5?> <?=$TRU_TYPE2_2_5?></td>
				 <? } 
				  if ($STATE_6){ ?>
				  <td><?=$TRU_INST2_1_6?> <?=$TRU_TYPE2_1_6?> &nbsp; <?=$TRU_INST2_2_4?> <?=$TRU_TYPE2_2_6?></td>
				 <? } 
				if ($_POST['frozen']!=2){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST2_1_1" id="pl_TRU_INST2_1_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_1_1,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_1_1" id="pl_TRU_TYPE2_1_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_1);?></select><br>
				 <SELECT NAME="pl_TRU_INST2_2_1" id="pl_TRU_INST2_2_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_2_1,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_2_1" id="pl_TRU_TYPE2_2_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_1);?></select>
				 </td>
				 <td>
				 <SELECT NAME="pl_TRU_INST2_1_2" id="pl_TRU_INST2_1_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_1_2,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_1_2" id="pl_TRU_TYPE2_1_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_2);?></select><br>
				 <SELECT NAME="pl_TRU_INST2_2_2" id="pl_TRU_INST2_2_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_2_2,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_2_2" id="pl_TRU_TYPE2_2_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_2);?></select>
				 </td>
				 <td>
				 <SELECT NAME="pl_TRU_INST2_1_3" id="pl_TRU_INST2_1_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_1_3,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_1_3" id="pl_TRU_TYPE2_1_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_3);?></select><br>
				 <SELECT NAME="pl_TRU_INST2_2_3" id="pl_TRU_INST2_2_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_2_3,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_2_3" id="pl_TRU_TYPE2_2_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_3);?></select>
				 </td>
				 <? if ($STATE_4){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST2_1_4" id="pl_TRU_INST2_1_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_1_4,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_1_4" id="pl_TRU_TYPE2_1_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_4);?></select><br>
				 <SELECT NAME="pl_TRU_INST2_2_4" id="pl_TRU_INST2_2_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_2_4,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_2_4" id="pl_TRU_TYPE2_2_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_4);?></select>
				 </td>
				 <? } 
				 if ($STATE_5){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST2_1_5" id="pl_TRU_INST2_1_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_1_5,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_1_5" id="pl_TRU_TYPE2_1_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_5);?></select><br>
				 <SELECT NAME="pl_TRU_INST2_2_5" id="pl_TRU_INST2_2_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_2_5,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_2_5" id="pl_TRU_TYPE2_2_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_5);?></select>
				 </td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST2_1_6" id="pl_TRU_INST2_1_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_1_6,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_1_6" id="pl_TRU_TYPE2_1_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_6);?></select><br>
				 <SELECT NAME="pl_TRU_INST2_2_6" id="pl_TRU_INST2_2_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST2_2_6,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE2_2_6" id="pl_TRU_TYPE2_2_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_6);?></select>
				 </td>
				 <? } 
				}?>
			  </tr>
			  <?php 
			  	if ($_POST['frozen']=="FUND"){
			  		analyse_changes_Asset('TRU_INST2_1',$ASSET_TRU_INST2_1_1,$ASSET_TRU_INST2_1_2,$ASSET_TRU_INST2_1_3,$ASSET_TRU_INST2_1_4,$ASSET_TRU_INST2_1_5,$ASSET_TRU_INST2_1_6,$TRU_INST2_1_1,$TRU_INST2_1_2,$TRU_INST2_1_3,$TRU_INST2_1_4,$TRU_INST2_1_5,$TRU_INST2_1_6);
			  		analyse_changes_Asset('TRU_TYPE2_1',$ASSET_TRU_TYPE2_1_1,$ASSET_TRU_TYPE2_1_2,$ASSET_TRU_TYPE2_1_3,$ASSET_TRU_TYPE2_1_4,$ASSET_TRU_TYPE2_1_5,$ASSET_TRU_TYPE2_1_6,$TRU_TYPE2_1_1,$TRU_TYPE2_1_2,$TRU_TYPE2_1_3,$TRU_TYPE2_1_4,$TRU_TYPE2_1_5,$TRU_TYPE2_1_6);
			  		analyse_changes_Asset('TRU_INST2_2',$ASSET_TRU_INST2_2_1,$ASSET_TRU_INST2_2_2,$ASSET_TRU_INST2_2_3,$ASSET_TRU_INST2_2_4,$ASSET_TRU_INST2_2_5,$ASSET_TRU_INST2_2_6,$TRU_INST2_1_1,$TRU_INST2_2_2,$TRU_INST2_2_3,$TRU_INST2_2_4,$TRU_INST2_2_5,$TRU_INST2_2_6);
			  		analyse_changes_Asset('TRU_TYPE2_2',$ASSET_TRU_TYPE2_2_1,$ASSET_TRU_TYPE2_2_2,$ASSET_TRU_TYPE2_2_3,$ASSET_TRU_TYPE2_2_4,$ASSET_TRU_TYPE2_2_5,$ASSET_TRU_TYPE2_2_6,$TRU_TYPE2_1_1,$TRU_TYPE2_2_2,$TRU_TYPE2_2_3,$TRU_TYPE2_2_4,$TRU_TYPE2_2_5,$TRU_TYPE2_2_6);
			  	}
			   ?>
			  <tr class="cab3<?=$band?> <?=$cab3?>">
				 <td class="tableheader">TRU installed CAB3</td>
				 <td><?=$TRU_INST3_1_1?> <?=$TRU_TYPE3_1_1?><br><?=$TRU_INST3_2_2?> <?=$TRU_TYPE3_2_1?></td>
				 <td><?=$TRU_INST3_1_2?> <?=$TRU_TYPE3_1_2?><br><?=$TRU_INST3_2_3?> <?=$TRU_TYPE3_2_2?></td>
				 <td><?=$TRU_INST3_1_3?> <?=$TRU_TYPE3_1_3?><br><?=$TRU_INST3_2_3?> <?=$TRU_TYPE3_2_3?></td>
				 <? if ($STATE_4){ ?>
				  <td><?=$TRU_INST3_1_4?> <?=$TRU_TYPE3_1_4?> &nbsp; <?=$TRU_INST3_2_4?> <?=$TRU_TYPE3_2_4?></td>
				 <? }
				 if ($STATE_5){ ?>
				  <td><?=$TRU_INST3_1_5?> <?=$TRU_TYPE3_1_5?> &nbsp; <?=$TRU_INST3_2_5?> <?=$TRU_TYPE3_2_5?></td>
				 <? } 
				 if ($STATE_6){ ?>
				  <td><?=$TRU_INST3_1_6?> <?=$TRU_TYPE3_1_6?> &nbsp; <?=$TRU_INST3_2_6?> <?=$TRU_TYPE3_2_6?></td>
				 <? }  
				if ($_POST['frozen']!=2){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST3_1_1" id="pl_TRU_INST3_1_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_1_1,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_1_1" id="pl_TRU_TYPE3_1_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_1);?></select><br>
				 <SELECT NAME="pl_TRU_INST3_2_1" id="pl_TRU_INST3_2_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_2_1,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_2_1" id="pl_TRU_TYPE3_2_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_1);?></select>
				 </td>
				 <td>
				 <SELECT NAME="pl_TRU_INST3_1_2" id="pl_TRU_INST3_1_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_1_2,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_1_2" id="pl_TRU_TYPE3_1_2<?=$band?>" style="width:50%;float:left;" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_2);?></select><br>
				 <SELECT NAME="pl_TRU_INST3_2_2" id="pl_TRU_INST3_2_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_2_2,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_2_2" id="pl_TRU_TYPE3_2_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_2);?></select>
				 </td>
				 <td>
				 <SELECT NAME="pl_TRU_INST3_1_3" id="pl_TRU_INST3_1_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_1_3,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_1_3" id="pl_TRU_TYPE3_1_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_3);?></select><br>
				 <SELECT NAME="pl_TRU_INST3_2_3" id="pl_TRU_INST3_2_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_2_3,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_2_3" id="pl_TRU_TYPE3_2_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_3);?></select>
				 </td>
				 <? if ($STATE_4){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST3_1_4" id="pl_TRU_INST3_1_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_1_4,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_1_4" id="pl_TRU_TYPE3_1_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_4);?></select><br>
				 <SELECT NAME="pl_TRU_INST3_2_4" id="pl_TRU_INST3_2_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_2_4,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_2_4" id="pl_TRU_TYPE3_2_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_4);?></select>
				 </td>
				 <? }
				 if ($STATE_5){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST3_1_5" id="pl_TRU_INST3_1_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_1_5,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_1_5" id="pl_TRU_TYPE3_1_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_5);?></select><br>
				 <SELECT NAME="pl_TRU_INST3_2_5" id="pl_TRU_INST3_2_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_2_5,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_2_5" id="pl_TRU_TYPE3_2_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_5);?></select>
				 </td>
				 <? }
				 if ($STATE_6){ ?>
				 <td>
				 <SELECT NAME="pl_TRU_INST3_1_6" id="pl_TRU_INST3_1_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_1_6,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_1_6" id="pl_TRU_TYPE3_1_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_6);?></select><br>
				 <SELECT NAME="pl_TRU_INST3_2_6" id="pl_TRU_INST3_2_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_TRU_INST3_2_6,0,12,1,'no');?>
				 <SELECT NAME="pl_TRU_TYPE3_2_6" id="pl_TRU_TYPE3_2_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_6);?></select>
				 </td>
				 <? } 
				}?>
			  </tr>
			  <?php 
			  	if ($_POST['frozen']=="FUND"){
			  		analyse_changes_Asset('TRU_INST3_1',$ASSET_TRU_INST3_1_1,$ASSET_TRU_INST3_1_2,$ASSET_TRU_INST3_1_3,$ASSET_TRU_INST3_1_4,$ASSET_TRU_INST3_1_5,$ASSET_TRU_INST3_1_6,$TRU_INST3_1_1,$TRU_INST3_1_2,$TRU_INST3_1_3,$TRU_INST3_1_4,$TRU_INST3_1_5,$TRU_INST3_1_6);
			  		analyse_changes_Asset('TRU_TYPE3_1',$ASSET_TRU_TYPE3_1_1,$ASSET_TRU_TYPE3_1_2,$ASSET_TRU_TYPE3_1_3,$ASSET_TRU_TYPE3_1_4,$ASSET_TRU_TYPE3_1_5,$ASSET_TRU_TYPE3_1_6,$TRU_TYPE3_1_1,$TRU_TYPE3_1_2,$TRU_TYPE3_1_3,$TRU_TYPE3_1_4,$TRU_TYPE3_1_5,$TRU_TYPE3_1_6);
			  		analyse_changes_Asset('TRU_INST3_2',$ASSET_TRU_INST3_2_1,$ASSET_TRU_INST3_2_2,$ASSET_TRU_INST3_2_3,$ASSET_TRU_INST3_2_4,$ASSET_TRU_INST3_2_5,$ASSET_TRU_INST3_2_6,$TRU_INST3_1_1,$TRU_INST3_2_2,$TRU_INST3_2_3,$TRU_INST3_2_4,$TRU_INST3_2_5,$TRU_INST3_2_6);
			  		analyse_changes_Asset('TRU_TYPE3_2',$ASSET_TRU_TYPE3_2_1,$ASSET_TRU_TYPE3_2_2,$ASSET_TRU_TYPE3_2_3,$ASSET_TRU_TYPE3_2_4,$ASSET_TRU_TYPE3_2_5,$ASSET_TRU_TYPE3_2_6,$TRU_TYPE3_1_1,$TRU_TYPE3_2_2,$TRU_TYPE3_2_3,$TRU_TYPE3_2_4,$TRU_TYPE3_2_5,$TRU_TYPE3_2_6);
			  	}
		    }

		    if ($band!='G9' && $band!='G18'){ ?>
			   	<tr>
					 <td class="parameter_name"><font color="blue">Radio unit type</font></td>
					 <td><select NAME="FREQ_ACTIVE_1" id="FREQ_ACTIVE_1<?=$band?>" class="form-control"><?php
					 get_select_RRU2($FREQ_ACTIVE_1); ?></select></td>
					 <td><select NAME="FREQ_ACTIVE_2" id="FREQ_ACTIVE_2<?=$band?>" class="form-control"><?php
					 	get_select_RRU2($FREQ_ACTIVE_2); ?></select></td>
					 <td><select NAME="FREQ_ACTIVE_3" id="FREQ_ACTIVE_3<?=$band?>" class="form-control"><?php
					 get_select_RRU2("$FREQ_ACTIVE_3"); ?></select></td>
					 <? if ($STATE_4){ ?>
					 <td><select NAME="FREQ_ACTIVE_4" id="FREQ_ACTIVE_4<?=$band?>" class="form-control"><?
					 get_select_RRU2("$FREQ_ACTIVE_4"); ?></select></td>
					 <? }
					 if ($STATE_5){ ?>
					 <td><select NAME="FREQ_ACTIVE_5" id="FREQ_ACTIVE_5<?=$band?>" class="form-control"><?
					 get_select_RRU2("$FREQ_ACTIVE_5"); ?></select></td>
					 <? }
					 if ($STATE_6){ ?>
					 <td><select NAME="FREQ_ACTIVE_6" id="FREQ_ACTIVE_6<?=$band?>" class="form-control"><?
					 get_select_RRU2("$FREQ_ACTIVE_6"); ?></select></td>
					 <? } 
					 if ($viewtype!="BUILD"){ ?>
				 	 	 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><select NAME="pl_FREQ_ACTIVE1_1" id="pl_FREQ_ACTIVE1_1<?=$band?>" class="tabledata cleardata form-control"><?
						 get_select_RRU2("$pl_FREQ_ACTIVE1_1"); ?>></select></td>
					 	 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select NAME="pl_FREQ_ACTIVE1_2" id="pl_FREQ_ACTIVE1_2<?=$band?>" class="tabledata cleardata form-control"><?
						 get_select_RRU2("$pl_FREQ_ACTIVE1_2"); ?>></select></td>
					 	 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select NAME="pl_FREQ_ACTIVE1_3" id="pl_FREQ_ACTIVE1_3<?=$band?>" class="tabledata cleardata form-control"><?
						 get_select_RRU2("$pl_FREQ_ACTIVE1_3"); ?>></select></td>
					 	 <? if ($STATE_4){ ?>
						 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select NAME="pl_FREQ_ACTIVE1_4" id="pl_FREQ_ACTIVE1_4<?=$band?>" class="tabledata cleardata form-control"><?
						 get_select_RRU2("$pl_FREQ_ACTIVE1_4"); ?>></select></td>
						 <? }
						 if ($STATE_5){ ?>
						 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select NAME="pl_FREQ_ACTIVE1_5" id="pl_FREQ_ACTIVE1_5<?=$band?>" class="tabledata cleardata form-control"><?
						 get_select_RRU2("$pl_FREQ_ACTIVE1_5"); ?>></select></td>
						 <? }
						 if ($STATE_6){ ?>
						 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select NAME="pl_FREQ_ACTIVE1_6" id="pl_FREQ_ACTIVE1_6<?=$band?>" class="tabledata cleardata form-control"><?
						 get_select_RRU2("$pl_FREQ_ACTIVE1_6"); ?>></select></td>
						 <? } 
					 }?>
				</tr>
				<tr>
					 <td class="parameter_name"><font color="blue">TMA</font></td>
					 <td><select name="ACS_1" id="ACS_1<?=$band?>" class="form-control"><? get_select_RRU("$ACS_1"); ?></select></td>
					 <td><select name="ACS_2" id="ACS_2<?=$band?>" class="form-control"><? get_select_RRU("$ACS_2"); ?></select></td>
					 <td><select name="ACS_3" id="ACS_3<?=$band?>" class="form-control"><? get_select_RRU("$ACS_3"); ?></select></td>
			         <? if ($STATE_4){ ?>
					 <td><select name="ACS_4" id="ACS_4<?=$band?>" class="form-control"><? get_select_RRU("$ACS_4"); ?></select></td>
					 <? }
					 if ($STATE_5){ ?>
					 <td><select name="ACS_5" id="ACS_5<?=$band?>" class="form-control"><? get_select_RRU("$ACS_5"); ?></select></td>
					 <? }
					 if ($STATE_6){ ?>
					 <td><select name="ACS_6" id="ACS_6<?=$band?>" class="form-control"><? get_select_RRU("$ACS_6"); ?></select></td>
					 <? } 
					 if ($viewtype!="BUILD"){ ?>
					 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_ACS_1" id="pl_ACS_1<?=$band?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_1"); ?></td>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_ACS_2" id="pl_ACS_2<?=$band?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_2"); ?></select></td>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_ACS_3" id="pl_ACS_3<?=$band?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_3"); ?></select></td>
					 <? if ($STATE_4){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_ACS_4" id="pl_ACS_4<?=$band?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_4"); ?></select></td>
					 <? }
					 if ($STATE_5){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_ACS_5" id="pl_ACS_5<?=$band?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_5"); ?></select></td>
					 <? } 
					 if ($STATE_6){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_ACS_6" id="pl_ACS_6<?=$band?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_6"); ?></select></td>
					 <? }  
					 } ?>
				</tr>
			 	<tr>
					 <td class="parameter_name"><font color="blue">RET</font></td>
					 <td><select name="RET_1" id="RET_1<?=$band?>" class="form-control"><? get_select_yesno("$RET_1"); ?></select></td>
					 <td><select name="RET_2" id="RET_2<?=$band?>" class="form-control"><? get_select_yesno("$RET_2"); ?></select></td>
					 <td><select name="RET_3" id="RET_3<?=$band?>" class="form-control"><? get_select_yesno("$RET_3"); ?></select></td>
			 		 <? if ($STATE_4){ ?>
					 <td><select name="RET_4" id="RET_4<?=$band?>" class="form-control"><? get_select_yesno("$RET_4"); ?></select></td>
					 <? }
					 if ($STATE_5){ ?>
					 <td><select name="RET_5" id="RET_5<?=$band?>" class="form-control"><? get_select_yesno("$RET_5"); ?></select></td>
					 <? }
					 if ($STATE_6){ ?>
					 <td><select name="RET_6" id="RET_6<?=$band?>" class="form-control"><? get_select_yesno("$RET_6"); ?></select></td>
					 <? } 
					 if ($viewtype!="BUILD"){ ?>
					 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_RET_1" id="pl_RET_1<?=$band?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_1"); ?></td>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_RET_2" id="pl_RET_2<?=$band?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_2"); ?></select></td>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_RET_3" id="pl_RET_3<?=$band?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_3"); ?></select></td>
			 		 <? if ($STATE_4){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_RET_4" id="pl_RET_4<?=$band?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_4"); ?></select></td>
					 <? } 
					 if ($STATE_5){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_RET_5" id="pl_RET_5<?=$band?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_5"); ?></select></td>
					 <? }
					 if ($STATE_6){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><select name="pl_RET_6" id="pl_RET_6<?=$band?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_6"); ?></select></td>
					 <? }
					 } ?>
				</tr>
		  <?php } ?>
		  <tr>
			 <td class="tableheader">Antenna Type 1</td>
			 <td><?=$ANTTYPE1_1?></td>
			 <td><?=$ANTTYPE1_2?></td>
			 <td><?=$ANTTYPE1_3?></td>
			 <? if ($STATE_4){ ?>
			 <td><?=$ANTTYPE1_4?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td><?=$ANTTYPE1_5?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td><?=$ANTTYPE1_6?></td>
			 <? }

			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>">
			 <input type="text" name='pl_ANTTYPE1_1' value="<?=$pl_ANTTYPE1_1?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE1_1<?=$band?>"  placeholder="Select antenna..." /></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE1_2' value="<?=$pl_ANTTYPE1_2?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE1_2<?=$band?>"  placeholder="Select antenna..." /></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE1_3' value="<?=$pl_ANTTYPE1_3?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE1_3<?=$band?>"  placeholder="Select antenna..." /></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE1_4' value="<?=$pl_ANTTYPE1_4?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE1_4<?=$band?>"  placeholder="Select antenna..." /></td>
			 <? } 
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE1_5' value="<?=$pl_ANTTYPE1_5?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE1_5<?=$band?>"  placeholder="Select antenna..." /></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE1_6' value="<?=$pl_ANTTYPE1_6?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE1_6<?=$band?>"  placeholder="Select antenna..." /></td>
			 <? }
			} ?>
		  </tr>
		 	<?php 
		  	if ($_POST['frozen']=="FUND"){
		  		analyse_changes_Asset('ANTTYPE1',$ASSET_ANTTYPE1_1,$ASSET_ANTTYPE1_2,$ASSET_ANTTYPE1_3,$ASSET_ANTTYPE1_4,$ASSET_ANTTYPE1_5,$ASSET_ANTTYPE1_6,$ANTTYPE1_1,$ANTTYPE1_2,$ANTTYPE1_3,$ANTTYPE1_4,$ANTTYPE1_5,$ANTTYPE1_6);
		  	}
		   ?>
		  <tr>
			 <td class="tableheader">Elektrical downtilt 1 <?=$updatable?></td>
			 <td><?=$ELECTILT1_1?></td>
			 <td><?=$ELECTILT1_2?></td>
			 <td><?=$ELECTILT1_3?></td>
			 <? if ($STATE_4){ ?>
			 <td><?=$ELECTILT1_4?></td>
			 <? }
			  if ($STATE_5){ ?>
			 <td><?=$ELECTILT1_5?></td>
			 <? }
			  if ($STATE_6){ ?>
			 <td><?=$ELECTILT1_6?></td>
			 <? } 
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT1_1" id="pl_ELECTILT1_1<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_1,0,15,1,'no');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT1_2" id="pl_ELECTILT1_2<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_2,0,15,1,'no');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT1_3" id="pl_ELECTILT1_3<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_3,0,15,1,'no');?></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT1_4" id="pl_ELECTILT1_4<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_4,0,15,1,'no');?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT1_5" id="pl_ELECTILT1_5<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_5,0,15,1,'no');?></td>
			 <? } 
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT1_6" id="pl_ELECTILT1_6<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_6,0,15,1,'no');?></td>
			 <? }  
			}?>
		  </tr>
		   <?php 
		  	if ($_POST['frozen']=="FUND"){
		  		analyse_changes_Asset('ELECTILT1',$ASSET_ELECTILT1_1,$ASSET_ELECTILT1_2,$ASSET_ELECTILT1_3,$ASSET_ELECTILT1_4,$ASSET_ELECTILT1_5,$ASSET_ELECTILT1_6,$ELECTILT1_1,$ELECTILT1_2,$ELECTILT1_3,$ELECTILT1_4,$ELECTILT1_5,$ELECTILT1_6);
		  	}
		   ?>
		  <tr>
			 <td class="tableheader">Mechanical tilt 1</td>
			 <td><?=$MECHTILT1_1?>&nbsp;<?=$MECHTILT_DIR1_1?></td>
			 <td><?=$MECHTILT1_2?>&nbsp;<?=$MECHTILT_DIR1_2?></td>
			 <td><?=$MECHTILT1_3?>&nbsp;<?=$MECHTILT_DIR1_3?></td>
			 <? if ($STATE_4){ ?>
			 <td id="cur_MECHTILT1_4"><?=$MECHTILT1_4?>&nbsp;<?=$pl_MECHTILT_DIR1_4?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td id="cur_MECHTILT1_4"><?=$MECHTILT1_5?>&nbsp;<?=$pl_MECHTILT_DIR1_5?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td id="cur_MECHTILT1_6"><?=$MECHTILT1_6?>&nbsp;<?=$pl_MECHTILT_DIR1_6?></td>
			 <? } 
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_MECHTILT1_1" id="pl_MECHTILT1_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_1,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR1_1' id='pl_MECHTILT_DIR1_1<?=$band?>' style="width:50%;float:left;" class='tabledata cleardata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_1?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_MECHTILT1_2" id="pl_MECHTILT1_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_2,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR1_2' id='pl_MECHTILT_DIR1_2<?=$band?>' style="width:50%;float:left;" class='tabledata cleardata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_2?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_MECHTILT1_3" id="pl_MECHTILT1_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_3,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR1_3' id='pl_MECHTILT_DIR1_3<?=$band?>' style="width:50%;float:left;" class='tabledata cleardata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_3?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_MECHTILT1_4" id="pl_MECHTILT1_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_4,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR1_4' id='pl_MECHTILT_DIR1_4<?=$band?>' style="width:50%;float:left;" class='tabledata cleardata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_4?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <? } 
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_MECHTILT1_5" id="pl_MECHTILT1_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_5,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR1_5' id='pl_MECHTILT_DIR1_5<?=$band?>' style="width:50%;float:left;" class='tabledata cleardata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_5?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <? } 
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_MECHTILT1_6" id="pl_MECHTILT1_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_6,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR1_6' id='pl_MECHTILT_DIR1_6<?=$band?>' style="width:50%;float:left;" class='tabledata cleardata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_6?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <? } 
			}?>
		  </tr>
		   <?php 
		  	if ($_POST['frozen']=="FUND"){
		  		analyse_changes_Asset('MECHTILT1',$ASSET_MECHTILT1_1,$ASSET_MECHTILT1_2,$ASSET_MECHTILT1_3,$ASSET_MECHTILT1_5,$ASSET_MECHTILT1_6,$ASSET_MECHTILT1_4,$MECHTILT1_1,$MECHTILT1_2,$MECHTILT1_3,$MECHTILT1_4,$MECHTILT1_5,$MECHTILT1_6);
		  	}
		   ?>
		  <tr>
			 <td class="tableheader">Antenna Height 1</td>
			 <td><?=$ANTHEIGHT1_1?>m<?=$ANTHEIGHT1_1_t?></td>
			 <td><?=$ANTHEIGHT1_2?>m<?=$ANTHEIGHT1_2_t?></td>
			 <td><?=$ANTHEIGHT1_3?>m<?=$ANTHEIGHT1_3_t?></td>
			 <? if ($STATE_4){ ?>
			 <td><?=$ANTHEIGHT1_4?>m<?=$ANTHEIGHT1_4_t?></td>
			 <? } 
			 if ($STATE_5){ ?>
			 <td><?=$ANTHEIGHT1_5?>m<?=$ANTHEIGHT1_5_t?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td><?=$ANTHEIGHT1_6?>m<?=$ANTHEIGHT1_6_t?></td>
			 <? }
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>">
			 <SELECT NAME="pl_ANTHEIGHT1_1" id="pl_ANTHEIGHT1_1<?=$band?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_1,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT1_1_t" id="pl_ANTHEIGHT1_1_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_1_t,0,99,1,'yes');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT1_2" id="pl_ANTHEIGHT1_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_2,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT1_2_t" id="pl_ANTHEIGHT1_2_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_2_t,0,99,1,'yes');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT1_3" id="pl_ANTHEIGHT1_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_3,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT1_3_t" id="pl_ANTHEIGHT1_3_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_3_t,0,99,1,'yes');?></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT1_4" id="pl_ANTHEIGHT1_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_4,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT1_4_t" id="pl_ANTHEIGHT1_4_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_4_t,0,99,1,'yes');?></td>
			 <? } 
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT1_5" id="pl_ANTHEIGHT1_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_5,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT1_5_t" id="pl_ANTHEIGHT1_5_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_5_t,0,99,1,'yes');?></td>
			 <? } 
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT1_6" id="pl_ANTHEIGHT1_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_6,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT1_6_t" id="pl_ANTHEIGHT1_6_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_6_t,0,99,1,'yes');?></td>
			 <? } 
			} ?>
		  </tr>
		  <tr>
			 <td class="tableheader">Azimuth 1</td>
			 <td><?=$AZI1_1?></td>
			 <td><?=$AZI1_2?></td>
			 <td><?=$AZI1_3?></td>
			 <? if ($STATE_4){ ?>
			 <td id="cur_AZI_4"><?=$AZI_4?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td id="cur_AZI_5"><?=$AZI_5?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td id="cur_AZI_6"><?=$AZI_6?></td>
			 <? } 
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI1_1" id="pl_AZI1_1<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_1);?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI1_2" id="pl_AZI1_2<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_2);?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI1_3" id="pl_AZI1_3<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_3);?></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI1_4" id="pl_AZI1_4<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_4);?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI1_5" id="pl_AZI1_5<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_5);?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI1_6" id="pl_AZI1_6<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_6);?></td>
			 <? }
			} ?>
		  </tr>
		  <?php 
		  	if ($_POST['frozen']=="FUND"){
		  		analyse_changes_Asset('AZI1',$ASSET_AZI1_1,$ASSET_AZI1_2,$ASSET_AZI1_3,$ASSET_AZI1_4,$ASSET_AZI1_5,$ASSET_AZI1_6,$AZI1_1,$AZI1_2,$AZI1_3,$AZI1_4,$AZI1_5,$AZI1_6);
		  	}
		   ?>
		  <tr>
			 <td class="tableheader">Antenna Type 2</td>
			 <td><?=$ANTTYPE2_1?></td>
			 <td><?=$ANTTYPE2_2?></td>
			 <td><?=$ANTTYPE2_3?></td>
			 <? if ($STATE_4){ ?>
			 <td><?=$ANTTYPE2_4?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td><?=$ANTTYPE2_5?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td><?=$ANTTYPE2_6?></td>
			 <? } 
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>">
			 <input type="text" name='pl_ANTTYPE2_1' value="<?=$pl_ANTTYPE2_1?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE2_1<?=$band?>"  placeholder="Select antenna..." /></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE2_2' value="<?=$pl_ANTTYPE2_2?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE2_2<?=$band?>"  placeholder="Select antenna..." /></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE2_3' value="<?=$pl_ANTTYPE2_3?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE2_3<?=$band?>"  placeholder="Select antenna..." /></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE2_4' value="<?=$pl_ANTTYPE2_4?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE2_4<?=$band?>"  placeholder="Select antenna..." /></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE2_5' value="<?=$pl_ANTTYPE2_5?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE2_5<?=$band?>"  placeholder="Select antenna..." /></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_ANTTYPE2_6' value="<?=$pl_ANTTYPE2_6?>" class="dynamic form-control antenna_list<?=$band?> antenna_listbig" id="pl_ANTTYPE2_6<?=$band?>"  placeholder="Select antenna..." /></td>
			 <? } 
			}?>
		  </tr>
		   <?php 
		  	if ($_POST['frozen']=="FUND"){
		  		analyse_changes_Asset('ANTTYPE2',$ASSET_ANTTYPE2_1,$ASSET_ANTTYPE2_2,$ASSET_ANTTYPE2_3,$ASSET_ANTTYPE2_4,$ASSET_ANTTYPE2_5,$ASSET_ANTTYPE2_6,$ANTTYPE2_1,$ANTTYPE2_2,$ANTTYPE2_3,$ANTTYPE2_4,$ANTTYPE2_5,$ANTTYPE2_6);
		  	}
		   ?>
		  <tr>
			 <td class="tableheader">Elektrical downtilt 2 <?=$updatable?></td>
			 <td><?=$ELECTILT2_1?></td>
			 <td><?=$ELECTILT2_2?></td>
			 <td><?=$ELECTILT2_3?></td>
			 <? if ($STATE_4){ ?>
			 <td><?=$ELECTILT2_4?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td><?=$ELECTILT2_5?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td><?=$ELECTILT2_6?></td>
			 <? }

			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT2_1" id="pl_ELECTILT2_1<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_1,0,15,1,'no');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT2_2" id="pl_ELECTILT2_2<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_2,0,15,1,'no');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT2_3" id="pl_ELECTILT2_3<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_3,0,15,1,'no');?></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT2_4" id="pl_ELECTILT2_4<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_4,0,15,1,'no');?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT2_5" id="pl_ELECTILT2_5<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_5,0,15,1,'no');?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ELECTILT2_6" id="pl_ELECTILT2_6<?=$band?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_6,0,15,1,'no');?></td>
			 <? } 
			}?>
		  </tr>
		  <?php 
		  	if ($_POST['frozen']=="FUND"){
		  		analyse_changes_Asset('ELECTILT2',$ASSET_ELECTILT2_1,$ASSET_ELECTILT2_2,$ASSET_ELECTILT2_3,$ASSET_ELECTILT2_4,$ASSET_ELECTILT2_5,$ASSET_ELECTILT2_6,$ELECTILT2_1,$ELECTILT2_2,$ELECTILT2_3,$ELECTILT2_4,$ELECTILT2_5,$ELECTILT2_6);
		  	}
		   ?>
		  <tr>
			 <td class="tableheader">Mechanical tilt 2</td>
			 <td><?=$MECHTILT2_1?>&nbsp;<?=$MECHTILT_DIR2_1?></td>
			 <td><?=$MECHTILT2_2?>&nbsp;<?=$MECHTILT_DIR2_2?></td>
			 <td><?=$MECHTILT2_3?>&nbsp;<?=$MECHTILT_DIR2_3?></td>
			 <? if ($STATE_4){ ?>
			  <td><?=$MECHTILT2_4?>&nbsp;<?=$MECHTILT_DIR2_4?></td>
			 <? }
			  if ($STATE_5){ ?>
			  <td><?=$MECHTILT2_5?>&nbsp;<?=$MECHTILT_DIR2_5?></td>
			 <? }
			  if ($STATE_6){ ?>
			  <td><?=$MECHTILT2_6?>&nbsp;<?=$MECHTILT_DIR2_6?></td>
			 <? } 
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>">
			 <SELECT NAME="pl_MECHTILT2_1" id="pl_MECHTILT2_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_1,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR2_1' id="pl_MECHTILT_DIR2_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_1?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>">
			 <SELECT NAME="pl_MECHTILT2_2" id="pl_MECHTILT2_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_2,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR2_2' id="pl_MECHTILT_DIR2_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_2?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_MECHTILT2_3" id="pl_MECHTILT2_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_3,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR2_3' id="pl_MECHTILT_DIR2_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_3?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>">
			 <SELECT NAME="pl_MECHTILT2_4" id="pl_MECHTILT2_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_4,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR2_4' id="pl_MECHTILT_DIR2_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_4?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <? } 
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>">
			 <SELECT NAME="pl_MECHTILT2_5" id="pl_MECHTILT2_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_5,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR2_5' id="pl_MECHTILT_DIR2_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_5?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <? } 
			 if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>">
			 <SELECT NAME="pl_MECHTILT2_6" id="pl_MECHTILT2_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_6,0,15,1,'no');?>
			 <SELECT NAME='pl_MECHTILT_DIR2_6' id="pl_MECHTILT_DIR2_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_6?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
			 <? } 
			}?>
		  </tr>
		  <?php 
		  	if ($_POST['frozen']=="FUND"){
		  		analyse_changes_Asset('MECHTILT2',$ASSET_MECHTILT2_1,$ASSET_MECHTILT2_2,$ASSET_MECHTILT2_3,$ASSET_MECHTILT2_4,$ASSET_MECHTILT2_5,$ASSET_MECHTILT2_6,$MECHTILT2_1,$MECHTILT2_2,$MECHTILT2_3,$MECHTILT2_4,$MECHTILT2_5,$MECHTILT2_6);
		  	}
		   ?>
		  <tr>
			 <td class="tableheader">Antenna Height 2</td>
			 <td><?=$ANTHEIGHT2_1?>m<?=$ANTHEIGHT2_1_t?></td>
			 <td><?=$ANTHEIGHT2_2?>m<?=$ANTHEIGHT2_2_t?></td>
			 <td><?=$ANTHEIGHT2_3?>m<?=$ANTHEIGHT2_3_t?></td>
			 <? if ($STATE_4){ ?>
			 <td><?=$ANTHEIGHT2_4?>m<?=$ANTHEIGHT2_4_t?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td><?=$ANTHEIGHT2_5?>m<?=$ANTHEIGHT2_5_t?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td><?=$ANTHEIGHT2_6?>m<?=$ANTHEIGHT2_6_t?></td>
			 <? } 
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT2_1" id="pl_ANTHEIGHT2_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_1,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT2_1_t" id="pl_ANTHEIGHT2_1_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_1_t,0,99,1,'yes');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT2_2" id="pl_ANTHEIGHT2_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_2,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT2_2_t" id="pl_ANTHEIGHT2_2_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_2_t,0,99,1,'yes');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT2_3" id="pl_ANTHEIGHT2_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_3,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT2_3_t" id="pl_ANTHEIGHT2_3_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_3_t,0,99,1,'yes');?></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT2_4" id="pl_ANTHEIGHT2_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_4,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT2_4_t" id="pl_ANTHEIGHT2_4_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_4_t,0,99,1,'yes');?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT2_5" id="pl_ANTHEIGHT2_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_5,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT2_5_t" id="pl_ANTHEIGHT2_5_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_5_t,0,99,1,'yes');?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_ANTHEIGHT2_6" id="pl_ANTHEIGHT2_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_6,-5,200,1,'no');?>
			 <SELECT NAME="pl_ANTHEIGHT2_6_t" id="pl_ANTHEIGHT2_6_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_6_t,0,99,1,'yes');?></td>
			 <? }
			}?>
		  </tr>
		  <tr>
			 <td class="tableheader">Azimuth 2</td>
			 <td><?=$AZI2_1?></td>
			 <td><?=$AZI2_2?></td>
			 <td><?=$AZI2_3?></td>
			 <? if ($STATE_4){ ?>
			 <td id="cur_AZI_4"><?=$AZI2_4?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td id="cur_AZI_5"><?=$AZI2_5?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td id="cur_AZI_6"><?=$AZI2_6?></td>
			 <? } 
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI2_1" id="pl_AZI2_1<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_1);?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI2_2" id="pl_AZI2_2<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_2);?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI2_3" id="pl_AZI2_3<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_3);?></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI2_4" id="pl_AZI2_4<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_4);?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI2_5" id="pl_AZI2_5<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_5);?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_AZI2_6" id="pl_AZI2_6<?=$band?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_6);?></td>
			 <? } 
			} ?>
		  </tr>
		  <?php 
		  	if ($_POST['frozen']=="FUND"){
		  		analyse_changes_Asset('AZI2',$ASSET_AZI2_1,$ASSET_AZI2_2,$ASSET_AZI2_3,$ASSET_AZI2_4,$ASSET_AZI2_5,$ASSET_AZI2_6,$AZI2_1,$AZI2_2,$ASSET_AZI2_3,$ASSET_AZI2_4,$ASSET_AZI2_5,$ASSET_AZI2_6);
		  	}
		   ?>
		  <tr>
			 <td class="tableheader">Feeder type <?=$updatable?></td>
			 <td><?=$FEEDER_1?></td>
			 <td><?=$FEEDER_2?></td>
			 <td><?=$FEEDER_3?></td>
			 <? if ($STATE_4){ ?>
			 <td id="cur_FEEDER_4"><?=$FEEDER_4?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td id="cur_FEEDER_5"><?=$FEEDER_5?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td id="cur_FEEDER_6"><?=$FEEDER_6?></td>
			 <? } 
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>">
			 <input type="text" name='pl_FEEDER_1' value="<?=$pl_FEEDER_1?>" class="dynamic form-control feeder_list<?=$band?>" id="pl_FEEDER_1<?=$band?>" placeholder="Select feeder..." /></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_FEEDER_2' value="<?=$pl_FEEDER_2?>" class="dynamic form-control feeder_list<?=$band?>" id="pl_FEEDER_2<?=$band?>" placeholder="Select feeder..." /></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_FEEDER_3' value="<?=$pl_FEEDER_3?>" class="dynamic form-control feeder_list<?=$band?>" id="pl_FEEDER_3<?=$band?>" placeholder="Select feeder..." /></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_FEEDER_4' value="<?=$pl_FEEDER_4?>" class="dynamic form-control feeder_list<?=$band?>" id="pl_FEEDER_4<?=$band?>" placeholder="Select feeder..." /></td>
			 <? } 
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_FEEDER_5' value="<?=$pl_FEEDER_5?>" class="dynamic form-control feeder_list<?=$band?>" id="pl_FEEDER_5<?=$band?>" placeholder="Select feeder..." /></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name='pl_FEEDER_6' value="<?=$pl_FEEDER_6?>" class="dynamic form-control feeder_list<?=$band?>" id="pl_FEEDER_6<?=$band?>" placeholder="Select feeder..." /></td>
			 <? }
			} ?>
		</tr>
		<tr>
			 <td class="tableheader">Feeder length <?=$updatable?></td>
			 <td><?=$FEEDERLEN_1?>m<?=$FEEDERLEN_1_t?></td>
			 <td><?=$FEEDERLEN_2?>m<?=$FEEDERLEN_2_t?></td>
			 <td><?=$FEEDERLEN_3?>m<?=$FEEDERLEN_3_t?></td>
			 <? if ($STATE_4){ ?>
			 <td id="cur_FEEDERLEN_4"><?=$FEEDERLEN_4?>m<?=$FEEDERLEN_4_t?></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td id="cur_FEEDERLEN_5"><?=$FEEDERLEN_5?>m<?=$FEEDERLEN_5_t?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td id="cur_FEEDERLEN_6"><?=$FEEDERLEN_6?>m<?=$FEEDERLEN_6_t?></td>
			 <? } 
			if ($_POST['frozen']!=2){ ?>
			 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FEEDERLEN_1" id="pl_FEEDERLEN_1<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_1,0,200,1,'no');?>
			 <SELECT NAME="pl_FEEDERLEN_1_t" id="pl_FEEDERLEN_1_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_1_t,0,99,5,'yes');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FEEDERLEN_2" id="pl_FEEDERLEN_2<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_2,0,200,1,'no');?>
			 <SELECT NAME="pl_FEEDERLEN_2_t" id="pl_FEEDERLEN_2_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_2_t,0,99,5,'yes');?></td>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FEEDERLEN_3" id="pl_FEEDERLEN_3<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_3,0,200,1,'no');?>
			 <SELECT NAME="pl_FEEDERLEN_3_t" id="pl_FEEDERLEN_3_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_3_t,0,99,5,'yes');?></td>
			 <? if ($STATE_4){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FEEDERLEN_4" id="pl_FEEDERLEN_4<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_4,0,200,1,'no');?>
			 <SELECT NAME="pl_FEEDERLEN_4_t" id="pl_FEEDERLEN_4_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_4_t,0,99,5,'yes');?></td>
			 <? } 
			 if ($STATE_5){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FEEDERLEN_(" id="pl_FEEDERLEN_5<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_5,0,200,1,'no');?>
			 <SELECT NAME="pl_FEEDERLEN_5_t" id="pl_FEEDERLEN_5_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_5_t,0,99,5,'yes');?></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_FEEDERLEN_6" id="pl_FEEDERLEN_6<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_6,0,200,1,'no');?>
			 <SELECT NAME="pl_FEEDERLEN_6_t" id="pl_FEEDERLEN_6_t<?=$band?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_6_t,0,99,5,'yes');?></td>
			 <? }
			} ?>
		</tr>
		<?php
		if ($band=='G9' or $band=='G18'){ ?> 
			<tr>
				 <td class="tableheader"><font color="blue">DC block</td>
				 <td><SELECT NAME="DCBLOCK_1" id="DCBLOCK_1<?=$band?>" class="form-control"><?=get_select_YESNO($DCBLOCK_1);?></select></td>
				 <td><SELECT NAME="DCBLOCK_2" id="DCBLOCK_2<?=$band?>" class="form-control"><?=get_select_YESNO($DCBLOCK_2);?></select></td>
				 <td><SELECT NAME="DCBLOCK_3" id="DCBLOCK_3<?=$band?>" class="form-control"><?=get_select_YESNO($DCBLOCK_3);?></select></td>
				 <? if ($STATE_4){ ?>
				 <td><SELECT NAME="DCBLOCK_4" id="DCBLOCK_4<?=$band?>" class="form-control"><?=get_select_YESNO($DCBLOCK_4);?></select></td>
				 <? }
				 if ($STATE_5){ ?>
				 <td><SELECT NAME="DCBLOCK_5" id="DCBLOCK_5<?=$band?>" class="form-control"><?=get_select_YESNO($DCBLOCK_5);?></select></td>
				 <? }
				 if ($STATE_6){ ?>
				 <td><SELECT NAME="DCBLOCK_6" id="DCBLOCK_6<?=$band?>" class="form-control"><?=get_select_YESNO($DCBLOCK_6);?></select></td>
				 <? } 

				 if ($_POST['frozen']!=2){ ?>
					 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_DCBLOCK_1" id="pl_DCBLOCK_1<?=$band?>" class="tabledata cleardata form-control"><?=get_select_YESNO($pl_DCBLOCK_1);?></select></td>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_DCBLOCK_2" id="pl_DCBLOCK_2<?=$band?>" class="tabledata cleardata form-control"><?=get_select_YESNO($pl_DCBLOCK_2);?></select></td>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_DCBLOCK_3" id="pl_DCBLOCK_3<?=$band?>" class="tabledata cleardata form-control"><?=get_select_YESNO($pl_DCBLOCK_3);?></select></td>
					 <? if ($STATE_4){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_DCBLOCK_4" id="pl_DCBLOCK_4<?=$band?>" class="tabledata cleardata form-control"><?=get_select_YESNO($pl_DCBLOCK_4);?></select></td>
					 <? }
					 if ($STATE_5){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_DCBLOCK_5" id="pl_DCBLOCK_5<?=$band?>" class="tabledata cleardata form-control"><?=get_select_YESNO($pl_DCBLOCK_5);?></select></td>
					 <? }
					 if ($STATE_6){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><SELECT NAME="pl_DCBLOCK_6" id="pl_DCBLOCK_6<?=$band?>" class="tabledata cleardata form-control"><?=get_select_YESNO($pl_DCBLOCK_6);?></select></td>
					 <? } 
				 } ?>
			</tr>
			<tr>
				 <td class="tableheader">MPWR value</td>
				 <td><?=$HRACTIVE_1?></td>
				 <td><?=$HRACTIVE_2?></td>
				 <td><?=$HRACTIVE_3?></td>
				 <? if ($STATE_4){ ?>
				 <td><?=$HRACTIVE_4?></td>
				 <? }
				 if ($STATE_5){ ?>
				 <td><?=$HRACTIVE_5?></td>
				 <? }
				 if ($STATE_6){ ?>
				 <td><?=$HRACTIVE_6?></td>
				 <? } 
				 if ($_POST['frozen']!=2){ ?>
					 <td class="borderleft hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_HR_ACTIVE_1" id="pl_HR_ACTIVE_1<?=$band?>" class="tabledata cleardata form-control input-medium" value="<?=$pl_HRACTIVE_1?>"></td>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_HR_ACTIVE_2" id="pl_HR_ACTIVE_2<?=$band?>" class="tabledata cleardata form-control input-medium" value="<?=$pl_HRACTIVE_2?>"></td>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_HR_ACTIVE_3" id="pl_HR_ACTIVE_3<?=$band?>" class="tabledata cleardata form-control input-medium" value="<?=$pl_HRACTIVE_3?>"></td>
					 <? if ($STATE_4){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_HR_ACTIVE_4" id="pl_HR_ACTIVE_4<?=$band?>" class="tabledata cleardata form-control input-medium" value="<?=$pl_HRACTIVE_4?>"></td>
					 <? }
					 if ($STATE_5){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_HR_ACTIVE_5" id="pl_HR_ACTIVE_5<?=$band?>" class="tabledata cleardata form-control input-medium" value="<?=$pl_HRACTIVE_5?>"></td>
					 <? }
					 if ($STATE_6){ ?>
					 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_HR_ACTIVE_6" id="pl_HR_ACTIVE_6<?=$band?>" class="tabledata cleardata form-control input-medium" value="<?=$pl_HRACTIVE_6?>"></td>
					 <? } 
				 } ?>
			</tr>
		  <?php
		} ?>
		  <tr>
			<td>&nbsp;</td>
			<td colspan="<?=$colspan-1?>" class="tableheader" style="text-align:center;border-top:1px solid black;">Settings the same for all technologies</td>
		</tr>
		 <tr>
	     	<td class="tableheader"><font color="blue">Feeder sharing <?=$updatable?></td>
			<td><input type="text" name="FEEDERSHARE_1" id="FEEDERSHARE_1<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$FEEDERSHARE_1?>"></td>
			<td><input type="text" name="FEEDERSHARE_2" id="FEEDERSHARE_2<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$FEEDERSHARE_2?>"></td>
			<td><input type="text" name="FEEDERSHARE_3" id="FEEDERSHARE_3<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$FEEDERSHARE_3?>"></td>
			<? if ($STATE_4){ ?>
			<td><input type="text" name="FEEDERSHARE_4" id="FEEDERSHARE_4<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$FEEDERSHARE_4?>"></td>
			<? } 
			if ($STATE_5){ ?>
			<td><input type="text" name="FEEDERSHARE_5" id="FEEDERSHARE_5<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$FEEDERSHARE_5?>"></td>
			<? } 
			if ($STATE_6){ ?>
			<td><input type="text" name="FEEDERSHARE_6" id="FEEDERSHARE_6<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$FEEDERSHARE_6?>"></td>
			<? } 
			if ($_POST['frozen']!=2){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_FEEDERSHARE_1" id="pl_FEEDERSHARE_1<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$pl_FEEDERSHARE_1?>"></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_FEEDERSHARE_2" id="pl_FEEDERSHARE_2<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$pl_FEEDERSHARE_2?>"></td>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_FEEDERSHARE_3" id="pl_FEEDERSHARE_3<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$pl_FEEDERSHARE_3?>"></td>
				 <? if ($STATE_4){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_FEEDERSHARE_4" id="pl_FEEDERSHARE_4<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$pl_FEEDERSHARE_4?>"></td>
				 <? } 
				 if ($STATE_5){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_FEEDERSHARE_5" id="pl_FEEDERSHARE_5<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$pl_FEEDERSHARE_5?>"></td>
				 <? } 
				 if ($STATE_6){ ?>
				 <td class="hideunirandata<?=$band?><?=$_POST['datakey']?>"><input type="text" name="pl_FEEDERSHARE_6" id="pl_FEEDERSHARE_6<?=$band?>" class="form-control feedershare_list<?=$band?>" value="<?=$pl_FEEDERSHARE_6?>"></td>
				 <? } 
			} ?>
		 </tr>
	</tbody>
	</table>
</div>


<?php
if (substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['frozen']!="FUND" && $_POST['print']!="yes"){ ?>
	<p align="center">
	<font color="blue">BSDS comments <?=$band?></font><br>
	<textarea name="pl_COMMENTS" cols="100" rows="5"><?=$pl_COMMENTS?></textarea>
	</p>
	<p align="center">
	<font color="blue" size="1"><u>Remark:</u> Text in blue MUST be filled in by the radioplanner before a new BSDS can be created!<br>
	Those values could NOT be downloaded from Asset or the network</font></p>
	<?
}else{
	?><div style="width:400px;margin: 0px auto;"><?=$pl_COMMENTS?></div><?
}

	if ($_POST['frozen']==1){ 
		$value_save="Save current + Feeder + Tilt data \n(Fields with a * can be updated)";
  	}else{
  		$value_save="Save current & planned info!";
  	}

	if ($_POST['frozen']==1 && substr_count($guard_groups, 'Radioplanners')=="1"){
		?><input type="hidden" name="onlyfeeder" value="yes"><?
	}

	if (((substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['frozen']!=1 && $_POST['print']!="yes" && $_POST['print']!="yes")
	or ($pl_is_BSDS_accepted=="Accepted" && $_POST['frozen']==1  && substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['print']!="yes")
	or  ($_POST['frozen']=="POST"  && substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['print']!="yes"))
	&& $_POST['disable_save']!='yes'){ //CURRENT IS ALWAYS UPDATEBLE ** Only group 'Radioplanners' can update BSDSs
	 ?>
	 <p align='center'><input type="submit" class="btn btn-primary saveSubCurPl" value="<?=$value_save?>" data-key="<?=$key_band?>"></p>
	<?
	}

?>
</form>
<hr style='padding: 0;border: none;border-top: medium double #333;color:#333;text-align:center;page-break-after: always'></hr>
</div>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['print']!="yes"){
 	?>
	<script type="text/javascript">
	$(document).ready( function(){
		$(".clear").click(function(){
		var table=$(this).data('table');
		msg = Messenger().post({
			  message: 'Are u sure you want to clear? Everything will be removed/emptied when you hit save button!',
			  type: 'error',
			  actions: {
			    ok: {
			      label: "I'm sure",
			      action: function() {
			      	msg.cancel();			 
					$("#"+table+" .cleardata").each(
						function(intIndex){
							pl_attribute_type=$(this).attr('type');
							pl_attribute_name=$(this).attr('name');
							if (pl_attribute_type==='select-one'){
								$(this).find('option').remove().append("<option value='' selected='selected'></option>");
							}else{
								$(this).val('');
							}
						}
					);
				
					$(".dynamic").select2('val',null);
			      }
			    },
			    cancel: {
			      label: 'cancel',
			      action: function() {
					return msg.cancel(); 
			      }
			    }
			  }
			});
		});

		function displayCurrentValue(selectedObject, currentSearchTerm) {
		  return selectedObject.text;
		}

		function get_select2(filter,field,classfield){		
			$('.'+classfield).select2({
		    	initSelection: function(element, callback) {
					callback({id: element.val(), text: element.val() });
				},
				nextSearchTerm: displayCurrentValue,
			    minimumInputLength: 2,
			    ajax: {
			      url: "scripts/current_planned2/ajax/field_list.php",
			      dataType: 'json',
			      data: function (term, page) {
			      	
			        return {
			          q: term,
			          field: field,
		              type: filter
			        };
			      },
			      results: function (data, page) {
			        return { results: data };
			      }
			    }
		    });
		}

		var band="<?=$_POST['band']?>";
		if (band=='L18'){
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
		$('.feedershare_list'+band).select2({tags:["G9", "G18", "U9","U21","L8","L18","L26"]});
	});
	</script>
<?php
}else if ($_POST['print']=="yes"){
	$printclass="print";
	?>
	<link rel="stylesheet" href="<?=$config['sitepath_url']?>/bsds/scripts/current_planned/currentplanned_print.css" type="text/css" media="screen,print" />
	<?
}
if (empty($_POST['band'])){
	echo "ERROR, no band specified!";
	die;
}
if ($_POST['bsdsbobrefresh']=="PRE"){
	$bsdsdata="PRE READY TO BUILD";
	$_POST['bsdsbobrefresh']="";
}
//echo "BSDSDATA: <pre>".print_r($bsdsdata,true)."</pre>";
//echo "---".$_POST['status'];
$statusinfo=getStatusInfo($_POST['status']);
$viewtype=$statusinfo['viewtype'];
$viewhistory=$statusinfo['viewhistory'];
$color=$statusinfo['color'];
$status=$statusinfo['status'];

if ($_POST['band']=="U21"){
	$lognode=$_POST['lognodeID_UMTS2100'];
	$tabletype="UMTS";
}elseif ($_POST['band']=="U9"){
	$lognode=$_POST['lognodeID_UMTS900'];
	$tabletype="UMTS";
}elseif ($_POST['band']=="L18"){
	$lognode=$_POST['lognodeID_LTE1800'];
	$tabletype="LTE";
}elseif ($_POST['band']=="L26"){
	$lognode=$_POST['lognodeID_LTE2600'];
	$tabletype="LTE";
}elseif ($_POST['band']=="L8"){
	$lognode=$_POST['lognodeID_LTE800'];
	$tabletype="LTE";
}else{
	echo "ERROR: lost lognode key";
	die;
}

$cols_pl_sec=get_cols("BSDS_PL_".$tabletype."_SEC");
$cols_pl=get_cols("BSDS_PL_".$tabletype);

$cols_cur_sec=get_cols("BSDS_CU_".$tabletype."_SEC2");
$cols_cur=get_cols("BSDS_CU_".$tabletype."2");

$check_current_exists_UMTS=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['bsdsbobrefresh'],'',$_POST['donor'],$lognode,$viewtype);
$check_current_exists_UMTS_SECTOR=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['bsdsbobrefresh'],'all',$_POST['donor'],$lognode,$viewtype);

if ($check_current_exists_UMTS!=0 || $viewtype=="FUND"){
	$check_planned_exists_UMTS=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],'allsec',$viewtype,$_POST['donor']);
	if ($check_planned_exists_UMTS==="error"){
		?>
		<script language="JavaScript">
		Messenger().post({
		  message: "<h1>Sytem error</h1>There are too many records in the database for <?=$_POST['band']?> BSDS! Please contact Frederick Eyland",
		  type: 'error',
		  showCloseButton: false
		});
		</script>
		<?
		die;
	}
}else{
	$check_planned_exists_UMTS=0;
}

if ($_POST['print']=="yes" && $check_planned_exists_UMTS==0){
	echo "<blockquote style='page-break-after: always'><p>NO PLANNED DATA AVAILABLE FOR ".$_POST['band']."</p></blockquote>";
	die;
}

//echo $check_current_exists_UMTS. "-".$check_planned_exists_UMTS;
if($check_planned_exists_UMTS==0 && $status=="BSDS FUNDED"){
	echo "<div class='alert alert-warning'><h3>No planned data available for ".$_POST['band']." at time of funding!</h3>If needed, pease defund BSDS by removing U305 in NET1 and save data for ".$_POST['band']."<br>Then you will be able to refund with a newer date than previous UA305.</div>";
	if ($_POST['print']=="yes" && $check_planned_exists_UMTS==0){
		die;
	}
}

$gen_info=get_BSDS_generalinfo($_POST['bsdskey']);
$pl_is_BSDS_accepted=$gen_info['TEAML_APPROVED'][0];
$pl_CHANGEDATE=$gen_info['UPDATE_AFTER_COPY'][0];

$band=$_POST['band'];
$bsdskey=$_POST['bsdskey'];
$bsdsbobrefresh=$_POST['bsdsbobrefresh'];
$donor=$_POST['donor'];


if ($_POST['action']!="save" && $_POST['reloadAsset']!='yes'){
	if($check_planned_exists_UMTS!="0"){
		include("planned_data.php");
	}
}

if ($status!="PRE READY TO BUILD"){
	if ($pl_is_BSDS_accepted=="Accepted" && ($status=="BSDS FUNDED")){
		$updatable="<font color=red><b>*</b></font>";
	}else{
		$updatable="";
	}
}
include("current_data.php");

if ($check_planned_exists_UMTS=="0" or $_POST['reloadAsset']=='yes'){
// If there is NO planned data, we first need to get current data out of the datbase
	include("planned_data.php");
}
include("height_conversion.php");

//putting default values
if (($band=="L8" or $band=="L18") && $check_planned_exists_UMTS==0){
	$pl_FREQ_ACTIVE_1="RU";
	$pl_FREQ_ACTIVE_2="RU";
	$pl_FREQ_ACTIVE_5="RU";
	$pl_FREQ_ACTIVE_4="RU";
	$pl_FREQ_ACTIVE_5="RU";
	$pl_FREQ_ACTIVE_6="RU";
	$pl_ACS_1="ASG 12dB";
	$pl_ACS_2="ASG 12dB";
	$pl_ACS_3="ASG 12dB";
	$pl_ACS_4="ASG 12dB";
	$pl_ACS_5="ASG 12dB";
	$pl_ACS_6="ASG 12dB";
	$pl_RET_1="YES";
	$pl_RET_2="YES";
	$pl_RET_3="YES";
	$pl_RET_4="YES";
	$pl_RET_5="YES";
	$pl_RET_6="YES";
}


/*******************************************************************************************************************/
/***********************************     OUTPUT DATA TO SCREEN  ****************************************************/
/*******************************************************************************************************************/
if ($STATE_6){
	$colspan2=6;
	if ($viewtype=="BUILD"){
		$colspan=7;
	}else{
		$colspan=12;
	}
}else if ($STATE_5){
	$colspan2=5;
	if ($viewtype=="BUILD"){
		$colspan=6;
	}else{
		$colspan=11;
	}
}else if ($STATE_4){
	$colspan2=4;
	if ($viewtype=="BUILD"){
		$colspan=5;
	}else{
		$colspan=10;
	}
}else{
	$colspan2=3;
	if ($viewtype=="BUILD"){
		$colspan=4;
	}else{
		$colspan=8;
	}
}

$query = "Select * FROM ORQ_BSDS WHERE CANDIDATE LIKE '%".$_POST['siteID']."%'";
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

?>

<div id="<?=$_POST['print']?>curpl_<?=$_POST['bsdskey']?><?=$_POST['band']?><?=$_POST['status']?><?php echo str_replace(':', '', str_replace('/', '', str_replace(' ', '', $_POST['bsdsbobrefresh']))); ?>"  style='page-break-after: always'>
<?php
if ($_POST['print']!="yes"){
?>
<form action="scripts/current_planned2/save_pl_cu_UMTS.php" method="post" id="current_planned_form<?=$_POST['band']?><?=$viewtype?>">
<input type="hidden" name="band" value="<?=$_POST['band']?>">
<input type="hidden" name="pl_band" value="<?=$_POST['band']?>">
<input type="hidden" name="action" value="save">
<input type="hidden" name="lognode" value="<?=$lognode?>">
<input type="hidden" name="bsdskey" value="<?=$_POST['bsdskey']?>">
<input type="hidden" name="bsdsbobrefresh" value="<?=$_POST['bsdsbobrefresh']?>">
<input type="hidden" name="viewtype" value="<?=$viewtype?>">

<input type="hidden" name="FEEDERSHARE_1" value="<?=$FEEDERSHARE_1?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_1<?=$_POST['band']?>">
<input type="hidden" name="FEEDERSHARE_2" value="<?=$FEEDERSHARE_2?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_2<?=$_POST['band']?>">
<input type="hidden" name="FEEDERSHARE_3" value="<?=$FEEDERSHARE_3?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_3<?=$_POST['band']?>">
<input type="hidden" name="FEEDERSHARE_4" value="<?=$FEEDERSHARE_4?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_4<?=$_POST['band']?>">
<input type="hidden" name="FEEDERSHARE_5" value="<?=$FEEDERSHARE_5?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_5<?=$_POST['band']?>">
<input type="hidden" name="FEEDERSHARE_6" value="<?=$FEEDERSHARE_6?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_6<?=$_POST['band']?>">

<?php

	for ($i=1;$i<=6;$i++){
		foreach ($cols_cur_sec['COLUMN_NAME'] as $key => $column) {
			$cur_parname=$column."_".$i;
			if ($column=="ANTHEIGHT1" || $column=="ANTHEIGHT2" || $column=="MECHTILT1" || $column=="MECHTILT2" || $column=="FEEDERLEN"){
				$cur_parname2=$column."_".$i."_t";
				echo '<input type="hidden" name="'.$cur_parname2.'" value="'.$$cur_parname2.'" id="'.$_POST['print'].'cur_'.$cur_parname2.$_POST['band'].'">';
			}						
			echo '<input type="hidden" name="'.$cur_parname.'" value="'.$$cur_parname.'" id="'.$_POST['print'].'cur_'.$cur_parname.$_POST['band'].'">';
		}			
	}	
	foreach ($cols_pl['COLUMN_NAME'] as $key => $column) {
		$cur_parname=$column;					
		echo '<input type="hidden" name="'.$cur_parname.'" value="'.$$cur_parname.'" id="'.$_POST['print'].'cur_'.$cur_parname.$_POST['band'].'">';
	}	
	if ($viewtype!="BUILD"){ ?>
	<button type="button" data-srollband="scroll<?=$_POST['band']?><?=$_POST['status']?>" class="btn btn-success btn-xs bsdsScrollRight rightArrow">
	  <span class="glyphicon glyphicon-forward"></span> Right &nbsp;</button>
	<button type="button" data-srollband="scroll<?=$_POST['band']?><?=$_POST['status']?>" class="btn btn-success btn-xs bsdsScrollLeft leftArrow">
	  <span class="glyphicon glyphicon-backward"></span> Left</button>
	<?php 
	}
} ?>

<div class="table-responsive table-responsive-force" id="scroll<?=$_POST['band']?><?=$_POST['status']?>">
<table class="table table-bordered table-hover table-condensed <?=$printclass?>" id="bsds<?=$_POST['candidate']?><?=$_POST['status']?><?=$_POST['band']?>">
<?php
if ($viewhistory=="yes" &&  $viewtype!="PRE"){?>
<caption class="<?=$color?>"><?=$status?></caption>
<?php
}else{ ?>
	<caption class="<?=$color?>"><?=$band?> STATUS: <?=$status?>
 	</caption>
<?
}
if ($check_current_exists_UMTS==0 && $_POST['status']=="FUND" && $status!="PRE READY TO BUILD"){
$pl=""
?>
<caption class="error">NO LIVE DATA AVAILABLE AT TIME OF FUNDING!</caption>
<?php } ?>
<tbody>
<tr>
	<td style="min-width:160px;" class="tableheader <?=$color?>"><?=$_POST['siteID']?>   <?=$_POST['band']?></td>
	<td colspan="<?=$colspan2?>" class="table_head"><b>
				<?php if ($check_current_exists_UMTS=="0"){ ?>
					<span class="savemessage"><font color='red'>NOT SAVED</font></span>
				<?php } ?>
				<?php if ($viewtype!="FUND" or ($viewtype=="FUND" && $check_current_exists_UMTS_SECTOR==0)){ ?>
				LIVE SITUATION from OSS/ASSET (<?php echo date('d-m-Y H:i:s'); ?>)
				<?php }else if ($viewtype=="FUND" && $check_current_exists_UMTS_SECTOR!=0){ ?>
				<b>SAVED</b> SITUATION @ FUNDING (<?=$CHANGEDATE?>)
				<?php }else{ ?>
				<b>SAVED</b> SITUATION
				<? } ?>
		</td>
		<?
	if ($viewtype!="BUILD"){

		$clas="table_head";
		if ($check_planned_exists_UMTS=="0"){
			$pl.="<span class='savemessage'><font color='red'>NOT SAVED</font></span> ";
			$general_override="NOT_SAVED";
		}
		$pl.="PLANNED SITUATION ";
		if ($pl_TECHNO_CHANGEDATE && $check_planned_exists_UMTS==1){
			$pl.= "(Last update: ".$pl_TECHNO_CHANGEDATE.")";
		}

	?>
	<td colspan="<?=$colspan2?>" class="table_head borderleft">
	<b><?=$pl?></b>
	<button type="button" class="btn btn-danger btn-xs clear pull-right" name="clear"  data-table="bsds<?=$_POST['candidate']?><?=$_POST['status']?><?=$_POST['band']?>">Clear</button>
	<?php if ($_POST['print']!="yes" && $check_planned_exists_UMTS=="0"){ 
		$hidebtn="hidden";
	} ?>
	<button type="button" data-reloadAsset='yes' data-id="<?=$_POST['bsdsformid']?>" data-techno="<?=$_POST['band']?>" data-bsdskey="<?=$_POST['bsdskey']?>" class="btn btn-info btn-xs bsdsdetails2 pull-right hidebtn <?=$hidebtn?>" name="reloadAsset"  data-table="bsds<?=$_POST['candidate']?><?=$_POST['status']?><?=$_POST['band']?>">Reload from Asset</button>
	</td>
	<?php } ?>
 </tr>
	<tr>
		<td class="bsdsinfoband <?=$color?>">RAFID: <?=$_POST['rafid']?><br>BSDSKEY: <?=$_POST['bsdskey']?><br>STATUS DATE:<br><?=$_POST['bsdsbobrefresh']?></td>
		<td colspan="<?=$colspan2?>">
			<table width="100%" height="100%" border="0" bordercolor="lighblue" cellpadding="1" align="center" cellspacing="1">
			<tr>
				<td class="parameter_name"><font color="blue"><b>Cabinettype</b></font></td>
				<td><input type="text" value="<?=$CABTYPE?>" name="CABTYPE" id="CABTYPE<?=$_POST['band']?>" placeholder="Select cabinet..." tabindex="-1" class="cabtype_list<?=$_POST['band']?> form-control"></td>
				<td class="parameter_name"><font color="blue">Solution</font></td>
				<td><select NAME="PLAYSTATION" id="PLAYSTATION<?=$_POST['band']?>" class="form-control">
					<option selected><?=$PLAYSTATION?><option>Normal</option><option>Playstation</option><option>Stealth</option><option>UniRAN</option></select></td>
			</tr>
			<tr>
				<td class="parameter_name"><font color="blue">Power supply</font></td>
				<td><select NAME="POWERSUP" id="POWERSUP<?=$_POST['band']?>" class="form-control"><option selected><?=$POWERSUP?><option>ACTURA CS</option><option>B900</option><option>DC/DC</option><option>PC8910A</option><option>B121</option><option>B201</option><option>NONE</option></select></td>
				<td class="parameter_name"><font color="blue">Data Service</font></td>
				<td><select NAME="PSU" id="PSU<?=$_POST['band']?>" class="form-control"><option selected><?=$SERVICE?></option><option>R99</option><option>HSDPA</option></select></td>
			</tr>

			<tr>
				<td class="parameter_name">BPC</td>
				<td><select NAME="BPC" id="BPC<?=$_POST['band']?>" class="form-control"><? get_select_numbers($BPC,0,5,1,'no');?></td>
				<td class="parameter_name">BPK</td>
				<td><select NAME="BPK" id="BPK<?=$_POST['band']?>" class="form-control"><? get_select_numbers($BPK,0,4,1,'no');?></td>
			</tr>
			<tr>
				<td class="parameter_name">CC</td>
				<td><select NAME="CC" id="CC<?=$_POST['band']?>" class="form-control"><option selected><?=$CC?></option><option value="CC">CC</option><option value="CC16">CC16</option><option value="CC16B">CC16B</option><option value="NONE">NONE</option><option value=""></option></select></td>
				<td class="parameter_name">BPL</td>
				<td><select NAME="BPL" id="BPL<?=$_POST['band']?>" class="form-control"><? get_select_numbers($BPL,0,4,1,'FS5');?></td>
			</tr>
			<tr>
				<td class="parameter_name">BPN2</td>
				<td><select NAME="BPN2" id="BPN2<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($BPN2,0,4,1,'no');?></td>
				<td class="parameter_name">PM0</td>
				<td><select NAME="PM0" id="PM0<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($PM0,0,4,1,'no');?></td>
			</tr>
			<tr>
				<td class="parameter_name">FS5</td>
				<td><select NAME="FS5" id="FS5<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($FS5,1,2,1,'no');?></td>
				<td class="parameter_name">RECTIFIERS</td>
				<td><select NAME="RECT" id="RECT<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($RECT,1,4,1,'no');?></td>
			</tr>
			</table>
		</td>
		<?php if ($viewtype!="BUILD"){ ?>
		<td colspan="<?=$colspan2?>" class="borderleft">
			<table width="100%" height="100%" border="0" bordercolor="lighblue" cellpadding="1" align="center" cellspacing="1">
			<tr>
				<td class="parameter_name">Cabinettype</td>
				<td><input type="text" value="<?=$pl_CABTYPE?>" name="pl_CABTYPE" id="pl_CABTYPE<?=$_POST['band']?>" placeholder="Select cabinet..." tabindex="-1" class="dynamic cabtype_list<?=$_POST['band']?> form-control"></td>
				<td class="tableheader"><font color="blue">Solution</td>
				<td><SELECT NAME="pl_PLAYSTATION" id="pl_PLAYSTATION<?=$_POST['band']?>" class="tabledata cleardata form-control"><option selected><?=$pl_PLAYSTATION?><option>Normal</option><option>Playstation</option><option>Stealth</option><option>UniRAN</option></select></td>
			</tr>
			<tr>
				<td class="parameter_name">Power supply</td>
				<td><select NAME="pl_POWERSUP" id="pl_POWERSUP<?=$_POST['band']?>" class="tabledata cleardata form-control"><option selected><?=$pl_POWERSUP?></option><option>ACTURA CS</option><option>B900</option><option>DC/DC</option><option>PC8910A</option><option>B121</option><option>B201</option><option>NONE</option></select></td>
				<td class="parameter_name">Data Service</td>
				<td><select NAME="pl_SERVICE" id="pl_SERVICE<?=$_POST['band']?>" class="tabledata cleardata form-control"><option selected><?=$pl_SERVICE?></option><option>R99</option><option>HSDPA</option></select></td>
			</tr>
			<tr>
				<td class="parameter_name">BPC</td>
				<td><select NAME="pl_BPC" id="pl_BPC<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_BPC,0,5,1,'no');?></td>
				<td class="parameter_name">BPK</td>
				<td><select NAME="pl_BPK" id="pl_BPK<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_BPK,0,4,1,'no');?></td>
			</tr>
			<tr>
				<td class="parameter_name">CC</td>
				<td><select NAME="pl_CC" id="pl_CC<?=$_POST['band']?>" class="tabledata cleardata form-control"><option selected><?=$pl_CC?></option><option value="CC">CC</option><option value="CC16">CC16</option><option value="CC16B">CC16B</option><option value="NONE">NONE</option><option value=""></option></select></td>
				<td class="parameter_name">BPL</td>
				<td><select NAME="pl_BPL" id="pl_BPL<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_BPL,0,4,1,'FS5');?></td>
			</tr>
			<tr>
				<td class="parameter_name">BPN2</td>
				<td><select NAME="pl_BPN2" id="pl_BPN2<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_BPN2,0,4,1,'no');?></td>
				<td class="parameter_name">PM0</td>
				<td><select NAME="pl_PM0" id="pl_PM0<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_PM0,0,4,1,'no');?></td>
			</tr>
			<tr>
				<td class="parameter_name">FS5</td>
				<td><select NAME="pl_FS5" id="pl_FS5<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_FS5,1,2,1,'no');?></td>
				<td class="parameter_name">RECTIFIERS</td>
				<td><select NAME="pl_RECT" id="pl_RECT<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_RECT,1,4,1,'no');?></td>
			</tr>
			</table>
		</td>
		<?php } ?>
	</tr>
	<tr class="TR2">
		 <td>&nbsp;</td>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_1,0,-1); ?></td>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_2,0,-1); ?></td>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_3,0,-1); ?></td>
		 <? if ($STATE_4){ ?>
		 <td bgcolor="Black" align="center" width="160"><b><? echo substr($UMTSCELLID_4,0,-1); ?></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_5,0,-1); ?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_6,0,-1); ?></td>
		 <? }
		 if ($viewtype!="BUILD"){ ?>
		 
		 <td class="table_head borderleft"><b><? echo substr($UMTSCELLID_1,0,-1); ?></td>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_2,0,-1); ?></td>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_3,0,-1); ?></td>
		 <? if ($STATE_4){ ?>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_4,0,-1); ?></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_5,0,-1); ?></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td class="table_head"><b><? echo substr($UMTSCELLID_6,0,-1); ?></td>
		 <? } 
		} ?>
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
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><?=$STATE_1?></td>
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
		} ?>
	</tr>
 	 <tr>
		 <td class="parameter_name"><font color="blue">Radio unit type</font></td>
		 <td><select NAME="FREQ_ACTIVE_1" id="FREQ_ACTIVE_1<?=$_POST['band']?>" class="form-control"><?php
		 get_select_RRU2($FREQ_ACTIVE_1); ?></select></td>
		 <td><select NAME="FREQ_ACTIVE_2" id="FREQ_ACTIVE_2<?=$_POST['band']?>" class="form-control"><?php
		 	get_select_RRU2($FREQ_ACTIVE_2); ?></select></td>
		 <td><select NAME="FREQ_ACTIVE_3" id="FREQ_ACTIVE_3<?=$_POST['band']?>" class="form-control"><?php
		 get_select_RRU2("$FREQ_ACTIVE_3"); ?></select></td>
		 <? if ($STATE_4){ ?>
		 <td><select NAME="FREQ_ACTIVE_4" id="FREQ_ACTIVE_4<?=$_POST['band']?>" class="form-control"><?
		 get_select_RRU2("$FREQ_ACTIVE_4"); ?></select></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><select NAME="FREQ_ACTIVE_5" id="FREQ_ACTIVE_5<?=$_POST['band']?>" class="form-control"><?
		 get_select_RRU2("$FREQ_ACTIVE_5"); ?></select></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><select NAME="FREQ_ACTIVE_6" id="FREQ_ACTIVE_6<?=$_POST['band']?>" class="form-control"><?
		 get_select_RRU2("$FREQ_ACTIVE_6"); ?></select></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
	 	 	 <td class="borderleft"><select NAME="pl_FREQ_ACTIVE_1" id="pl_FREQ_ACTIVE_1<?=$_POST['band']?>" class="tabledata cleardata form-control"><?
			 get_select_RRU2("$pl_FREQ_ACTIVE_1"); ?>></select></td>
		 	 <td><select NAME="pl_FREQ_ACTIVE_2" id="pl_FREQ_ACTIVE_2<?=$_POST['band']?>" class="tabledata cleardata form-control"><?
			 get_select_RRU2("$pl_FREQ_ACTIVE_2"); ?>></select></td>
		 	 <td><select NAME="pl_FREQ_ACTIVE_3" id="pl_FREQ_ACTIVE_3<?=$_POST['band']?>" class="tabledata cleardata form-control"><?
			 get_select_RRU2("$pl_FREQ_ACTIVE_3"); ?>></select></td>
		 	 <? if ($STATE_4){ ?>
			 <td><select NAME="pl_FREQ_ACTIVE_4" id="pl_FREQ_ACTIVE_4<?=$_POST['band']?>" class="tabledata cleardata form-control"><?
			 get_select_RRU2("$pl_FREQ_ACTIVE_4"); ?>></select></td>
			 <? }
			 if ($STATE_5){ ?>
			 <td><select NAME="pl_FREQ_ACTIVE_5" id="pl_FREQ_ACTIVE_5<?=$_POST['band']?>" class="tabledata cleardata form-control"><?
			 get_select_RRU2("$pl_FREQ_ACTIVE_5"); ?>></select></td>
			 <? }
			 if ($STATE_6){ ?>
			 <td><select NAME="pl_FREQ_ACTIVE_6" id="pl_FREQ_ACTIVE_6<?=$_POST['band']?>" class="tabledata cleardata form-control"><?
			 get_select_RRU2("$pl_FREQ_ACTIVE_6"); ?>></select></td>
			 <? } 
		 }?>
	</tr>
	<tr>
		 <td class="parameter_name"><font color="blue">ASC/RRU</font></td>
		 <td><select name="ACS_1" id="ACS_1<?=$_POST['band']?>" class="form-control"><? get_select_RRU("$ACS_1"); ?></td>
		 <td><select name="ACS_2" id="ACS_2<?=$_POST['band']?>" class="form-control"><? get_select_RRU("$ACS_2"); ?></td>
		 <td><select name="ACS_3" id="ACS_3<?=$_POST['band']?>" class="form-control"><? get_select_RRU("$ACS_3"); ?></td>
         <? if ($STATE_4){ ?>
		 <td><select name="ACS_4" id="ACS_4<?=$_POST['band']?>" class="form-control"><? get_select_RRU("$ACS_4"); ?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><select name="ACS_5" id="ACS_5<?=$_POST['band']?>" class="form-control"><? get_select_RRU("$ACS_5"); ?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><select name="ACS_6" id="ACS_6<?=$_POST['band']?>" class="form-control"><? get_select_RRU("$ACS_6"); ?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><select name="pl_ACS_1" id="pl_ACS_1<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_1"); ?></td>
		 <td><select name="pl_ACS_2" id="pl_ACS_2<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_2"); ?></td>
		 <td><select name="pl_ACS_3" id="pl_ACS_3<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_3"); ?></td>
		 <? if ($STATE_4){ ?>
		 <td><select name="pl_ACS_4" id="pl_ACS_4<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_4"); ?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><select name="pl_ACS_5" id="pl_ACS_5<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_5"); ?></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td><select name="pl_ACS_6" id="pl_ACS_6<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_RRU("$pl_ACS_6"); ?></td>
		 <? }  
		} ?>
	</tr>
 	<tr>
		 <td class="parameter_name"><font color="blue">RET</font></td>
		 <td><select name="RET_1" id="RET_1<?=$_POST['band']?>" class="form-control"><? get_select_yesno("$RET_1"); ?></td>
		 <td><select name="RET_2" id="RET_2<?=$_POST['band']?>" class="form-control"><? get_select_yesno("$RET_2"); ?></td>
		 <td><select name="RET_3" id="RET_3<?=$_POST['band']?>" class="form-control"><? get_select_yesno("$RET_3"); ?></td>
 		 <? if ($STATE_4){ ?>
		 <td><select name="RET_4" id="RET_4<?=$_POST['band']?>" class="form-control"><? get_select_yesno("$RET_4"); ?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><select name="RET_5" id="RET_5<?=$_POST['band']?>" class="form-control"><? get_select_yesno("$RET_5"); ?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><select name="RET_6" id="RET_6<?=$_POST['band']?>" class="form-control"><? get_select_yesno("$RET_6"); ?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><select name="pl_RET_1" id="pl_RET_1<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_1"); ?></td>
		 <td><select name="pl_RET_2" id="pl_RET_2<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_2"); ?></td>
		 <td><select name="pl_RET_3" id="pl_RET_3<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_3"); ?></td>
 		 <? if ($STATE_4){ ?>
		 <td><select name="pl_RET_4" id="pl_RET_4<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_4"); ?></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td><select name="pl_RET_5" id="pl_RET_5<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_5"); ?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><select name="pl_RET_6" id="pl_RET_6<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_yesno("$pl_RET_6"); ?></td>
		 <? }
		} ?>
	</tr>
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
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <input type="text" name='pl_ANTTYPE1_1' value="<?=$pl_ANTTYPE1_1?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_1<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <td><input type="text" name='pl_ANTTYPE1_2' value="<?=$pl_ANTTYPE1_2?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_2<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <td><input type="text" name='pl_ANTTYPE1_3' value="<?=$pl_ANTTYPE1_3?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_3<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name='pl_ANTTYPE1_4' value="<?=$pl_ANTTYPE1_4?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_4<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><input type="text" name='pl_ANTTYPE1_5' value="<?=$pl_ANTTYPE1_5?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_5<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><input type="text" name='pl_ANTTYPE1_6' value="<?=$pl_ANTTYPE1_6?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_6<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? } 
		} ?>
	  </tr>
	  <?php 
	  	if ($viewtype=="FUND"){
	  		analyse_changes_Asset('ANTTYPE1',$ASSET_ANTTYPE1_1,$ASSET_ANTTYPE1_2,$ASSET_ANTTYPE1_3,$ASSET_ANTTYPE1_4,$ASSET_ANTTYPE1_5,$ASSET_ANTTYPE1_6,$ANTTYPE1_1,$ANTTYPE1_2,$ANTTYPE1_3,$ANTTYPE1_4,$ANTTYPE1_5,$ANTTYPE1_6);
	  	}
	   ?>
 	  <tr>
		 <td class="parameter_name">Elektrical downtilt 1 <?=$updatable?></td>
		 <td id="cur_ELECTILT1_1"><?=$ELECTILT1_1?></td>
		 <td id="cur_ELECTILT1_2"><?=$ELECTILT1_2?></td>
		 <td id="cur_ELECTILT1_3"><?=$ELECTILT1_3?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_ELECTILT1_4"><?=$ELECTILT1_4?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td id="cur_ELECTILT1_5"><?=$ELECTILT1_5?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td id="cur_ELECTILT1_4"><?=$ELECTILT1_6?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>		 
	 	 <td class="borderleft"><select NAME="pl_ELECTILT1_1" id="pl_ELECTILT1_1<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_1,0,15,1,'no');?></td>
	 	 <td><select NAME="pl_ELECTILT1_2" id="pl_ELECTILT1_2<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_2,0,15,1,'no');?></td>
	 	 <td><select NAME="pl_ELECTILT1_3" id="pl_ELECTILT1_3<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_3,0,15,1,'no');?></td>
		 <? if ($STATE_4){ ?>
		 <td><select NAME="pl_ELECTILT1_4" id="pl_ELECTILT1_4<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_4,0,15,1,'no');?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><select NAME="pl_ELECTILT1_5" id="pl_ELECTILT1_5<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_5,0,15,1,'no');?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><select NAME="pl_ELECTILT1_6" id="pl_ELECTILT1_6<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT1_6,0,15,1,'no');?></td>
		 <? } 
		} ?>
	  </tr>
	  <?php 
	  	if ($viewtype=="FUND"){
	  		analyse_changes_Asset('ELECTILT1',$ASSET_ELECTILT1_1,$ASSET_ELECTILT1_2,$ASSET_ELECTILT1_3,$ASSET_ELECTILT1_4,$ASSET_ELECTILT1_5,$ASSET_ELECTILT1_6,$ELECTILT1_1,$ELECTILT1_2,$ELECTILT1_3,$ELECTILT1_4,$ELECTILT1_5,$ELECTILT1_6);
	  	}
	   ?>
 	  <tr>
		 <td class="parameter_name">Mechanical tilt 1</td>
		 <td id="cur_MECHTILT1_1"><?=$MECHTILT1_1?>&nbsp;<?=$MECHTILT_DIR1_1?></td>
		 <td id="cur_MECHTILT1_2"><?=$MECHTILT1_2?>&nbsp;<?=$MECHTILT_DIR1_2?></td>
		 <td id="cur_MECHTILT1_3"><?=$MECHTILT1_3?>&nbsp;<?=$MECHTILT_DIR1_3?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_MECHTILT1_4"><?=$MECHTILT1_4?>&nbsp;<?=$MECHTILT_DIR1_4?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td id="cur_MECHTILT1_5"><?=$MECHTILT1_5?>&nbsp;<?=$MECHTILT_DIR1_5?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td id="cur_MECHTILT1_6"><?=$MECHTILT1_6?>&nbsp;<?=$MECHTILT_DIR1_6?></td>
		 <? }
		 if ($viewtype!="BUILD"){ ?>		 
	 	 <td class="borderleft"><select NAME="pl_MECHTILT1_1" id="pl_MECHTILT1_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control "><? get_select_numbers($pl_MECHTILT1_1,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR1_1' id='pl_MECHTILT_DIR1_1<?=$_POST['band']?>' style="width:50%;float:left;" CLASS='tabledata cleardata form-control'><option selected><?=$pl_MECHTILT_DIR1_1?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
	 	 <td><select NAME="pl_MECHTILT1_2" id="pl_MECHTILT1_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_2,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR1_2' id='pl_MECHTILT_DIR1_2<?=$_POST['band']?>' style="width:50%;float:left;" CLASS='tabledata cleardata form-control'><option selected><?=$pl_MECHTILT_DIR1_2?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
	 	 <td><select NAME="pl_MECHTILT1_3" id="pl_MECHTILT1_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_3,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR1_3' id='pl_MECHTILT_DIR1_3<?=$_POST['band']?>' style="width:50%;float:left;" CLASS='tabledata cleardata form-control'><option selected><?=$pl_MECHTILT_DIR1_3?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
		 <? if ($STATE_4){ ?>
		 <td><select NAME="pl_MECHTILT1_4" id="pl_MECHTILT1_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_4,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR1_4' id='pl_MECHTILT_DIR1_4<?=$_POST['band']?>' style="width:50%;float:left;" CLASS='tabledata cleardata form-control'><option selected><?=$pl_MECHTILT_DIR1_4?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><select NAME="pl_MECHTILT1_5" id="pl_MECHTILT1_5<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_5,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR1_5' id='pl_MECHTILT_DIR1_5<?=$_POST['band']?>' style="width:50%;float:left;" CLASS='tabledata cleardata form-control'><option selected><?=$pl_MECHTILT_DIR1_5?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><select NAME="pl_MECHTILT1_6" id="pl_MECHTILT1_6<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT1_6,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR1_6' id='pl_MECHTILT_DIR1_6<?=$_POST['band']?>' style="width:50%;float:left;" CLASS='tabledata cleardata form-control'><option selected><?=$pl_MECHTILT_DIR1_6?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
		 <? } 
		} ?>
	  </tr>
	   <?php 
	  	if ($viewtype=="FUND"){
	  		analyse_changes_Asset('MECHTILT1',$ASSET_MECHTILT1_1,$ASSET_MECHTILT1_2,$ASSET_MECHTILT1_3,$ASSET_MECHTILT1_4,$ASSET_MECHTILT1_5,$ASSET_MECHTILT1_6,$MECHTILT1_1,$MECHTILT1_2,$MECHTILT1_3,$MECHTILT1_4,$MECHTILT1_5,$MECHTILT1_6);
	  	}
	   ?>
 	  <tr>
		 <td class="parameter_name">Antenna Height 1 (m)</td>
		 <td id="cur_ANTHEIGHT1_1"><?=$ANTHEIGHT1_1?>m<?=$ANTHEIGHT1_1_t?></td>
		 <td id="cur_ANTHEIGHT1_2"><?=$ANTHEIGHT1_2?>m<?=$ANTHEIGHT1_2_t?></td>
		 <td id="cur_ANTHEIGHT1_3"><?=$ANTHEIGHT1_3?>m<?=$ANTHEIGHT1_3_t?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_ANTHEIGHT1_4"><?=$ANTHEIGHT1_4?>m<?=$ANTHEIGHT1_4_t?></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td id="cur_ANTHEIGHT1_5"><?=$ANTHEIGHT1_5?>m<?=$ANTHEIGHT1_5_t?></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td id="cur_ANTHEIGHT1_6"><?=$ANTHEIGHT1_6?>m<?=$ANTHEIGHT1_6_t?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>		 
	 	 <td class="borderleft">
	 	 <select NAME="pl_ANTHEIGHT1_1" id="pl_ANTHEIGHT1_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_1,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT1_1_t" id="pl_ANTHEIGHT1_1_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_1_t,0,99,1,'yes');?></td>
	 	 <td><select NAME="pl_ANTHEIGHT1_2" id="pl_ANTHEIGHT1_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_2,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT1_2_t" id="pl_ANTHEIGHT1_2_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_2_t,0,99,1,'yes');?></td>
	 	 <td><select NAME="pl_ANTHEIGHT1_3" id="pl_ANTHEIGHT1_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_3,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT1_3_t" id="pl_ANTHEIGHT1_3_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_3_t,0,99,1,'yes');?></td>
		 <? if ($STATE_4){ ?>
		 <td><select NAME="pl_ANTHEIGHT1_4" id="pl_ANTHEIGHT1_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_4,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT1_4_t" id="pl_ANTHEIGHT1_4_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_4_t,0,99,1,'yes');?></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td><select NAME="pl_ANTHEIGHT1_5" id="pl_ANTHEIGHT1_5<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_5,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT1_5_t" id="pl_ANTHEIGHT1_5_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_5_t,0,99,1,'yes');?></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td><select NAME="pl_ANTHEIGHT1_6" id="pl_ANTHEIGHT1_6<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_6,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT1_6_t" id="pl_ANTHEIGHT1_6_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT1_6_t,0,99,1,'yes');?></td>
		 <? } 
		} ?>
	  </tr>
	  <tr>
		 <td class="parameter_name">Azimuth 1</td>
		 <td id="cur_AZI_1"><?=$AZI1_1?></td>
		 <td id="cur_AZI_2"><?=$AZI1_2?></td>
		 <td id="cur_AZI_3"><?=$AZI1_3?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_AZI_4"><?=$AZI1_4?></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td id="cur_AZI_5"><?=$AZI1_5?></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td id="cur_AZI_6"><?=$AZI1_6?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>		 
		 <td class="borderleft"><select NAME="pl_AZI1_1" id="pl_AZI1_1<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_1);?></td>
		 <td><select NAME="pl_AZI1_2" id="pl_AZI1_2<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_2);?></td>
		 <td><select NAME="pl_AZI1_3" id="pl_AZI1_3<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_3);?></td>
		 <? if ($STATE_4){ ?>
		 <td><select NAME="pl_AZI1_4" id="pl_AZI1_4<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_4);?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><select NAME="pl_AZI1_5" id="pl_AZI1_5<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_5);?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><select NAME="pl_AZI1_6" id="pl_AZI1_6<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI1_6);?></td>
		 <? } 
		}?>
     </tr>
 		<?php 
	  	if ($viewtype=="FUND"){
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
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <input type="text" name='pl_ANTTYPE2_1' value="<?=$pl_ANTTYPE2_1?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_1<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <td><input type="text" name='pl_ANTTYPE2_2' value="<?=$pl_ANTTYPE2_2?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_2<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <td><input type="text" name='pl_ANTTYPE2_3' value="<?=$pl_ANTTYPE2_3?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_3<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name='pl_ANTTYPE2_4' value="<?=$pl_ANTTYPE2_4?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_4<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td><input type="text" name='pl_ANTTYPE2_5' value="<?=$pl_ANTTYPE2_5?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_5<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td><input type="text" name='pl_ANTTYPE2_6' value="<?=$pl_ANTTYPE2_6?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_6<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? } 
		} ?>
	  </tr>
 	  <tr>
		 <td class="parameter_name">Elektrical downtilt 2 <?=$updatable?></td>
		 <td id="cur_ELECTILT2_1"><?=$ELECTILT2_1?></td>
		 <td id="cur_ELECTILT2_2"><?=$ELECTILT2_2?></td>
		 <td id="cur_ELECTILT2_3"><?=$ELECTILT2_3?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_ELECTILT2_4"><?=$ELECTILT2_4?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td id="cur_ELECTILT2_5"><?=$ELECTILT2_5?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td id="cur_ELECTILT2_6"><?=$ELECTILT2_6?></td>
		 <? } 
		 if ($viewtype!="BUILD"){?>	 
	 	 <td class="borderleft"><select NAME="pl_ELECTILT2_1" id="pl_ELECTILT2_1<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_1,0,15,1,'no');?></td>
	 	 <td><select NAME="pl_ELECTILT2_2" id="pl_ELECTILT2_2<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_2,0,15,1,'no');?></td>
	 	 <td><select NAME="pl_ELECTILT2_3" id="pl_ELECTILT2_3<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_3,0,15,1,'no');?></td>
		 <? if ($STATE_4){ ?>
		<td><select NAME="pl_ELECTILT2_4" id="pl_ELECTILT2_4<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_4,0,15,1,'no');?></td>
		 <? }
		 if ($STATE_5){ ?>
		<td><select NAME="pl_ELECTILT2_5" id="pl_ELECTILT2_5<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_5,0,15,1,'no');?></td>
		 <? }
		 if ($STATE_6){ ?>
		<td><select NAME="pl_ELECTILT2_6" id="pl_ELECTILT2_6<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_numbers($pl_ELECTILT2_6,0,15,1,'no');?></td>
		 <? } 
		}?>
	  </tr>
	  <?php 
	  	if ($viewtype=="FUND"){
	  		analyse_changes_Asset('ANTTYPE2',$ASSET_ANTTYPE2_1,$ASSET_ANTTYPE2_2,$ASSET_ANTTYPE2_3,$ASSET_ANTTYPE2_4,$ASSET_ANTTYPE2_5,$ASSET_ANTTYPE2_6,$ANTTYPE2_1,$ANTTYPE2_2,$ANTTYPE2_3,$ANTTYPE2_4,$ANTTYPE2_5,$ANTTYPE2_6);
	  	}
	   ?>
  	  <tr>
		 <td class="parameter_name">Mechanical tilt 2</td>
		 <td id="cur_MECHTILT2_1"><?=$MECHTILT2_1?>&nbsp;<?=$MECHTILT_DIR2_1?></td>
		 <td id="cur_MECHTILT2_2"><?=$MECHTILT2_2?>&nbsp;<?=$MECHTILT_DIR2_2?></td>
		 <td id="cur_MECHTILT2_3"><?=$MECHTILT2_3?>&nbsp;<?=$MECHTILT_DIR2_3?></td>
		 <? if ($STATE_4){ ?>
		  <td id="cur_MECHTILT2_4"><?=$MECHTILT2_4?>&nbsp;<?=$MECHTILT_DIR2_4?></td>
		 <? } 
		 if ($STATE_5){ ?>
		  <td id="cur_MECHTILT2_5"><?=$MECHTILT2_5?>&nbsp;<?=$MECHTILT_DIR2_5?></td>
		 <? } 
		 if ($STATE_6){ ?>
		  <td id="cur_MECHTILT2_6"><?=$MECHTILT2_6?>&nbsp;<?=$MECHTILT_DIR2_6?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
 	 	 <td class="borderleft"><select NAME="pl_MECHTILT2_1" id="pl_MECHTILT2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_1,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR2_1' id="pl_MECHTILT_DIR2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option selected><?=$pl_MECHTILT_DIR2_1?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
	 	 <td><select NAME="pl_MECHTILT2_2" id="pl_MECHTILT2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_2,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR2_2' id="pl_MECHTILT_DIR2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option selected><?=$pl_MECHTILT_DIR2_2?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
	 	 <td><select NAME="pl_MECHTILT2_3" id="pl_MECHTILT2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_3,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR2_3' id="pl_MECHTILT_DIR2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option selected><?=$pl_MECHTILT_DIR2_3?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
		 <? if ($STATE_4){ ?>
		 <td><select NAME="pl_MECHTILT2_4" id="pl_MECHTILT2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_4,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR2_4' id="pl_MECHTILT_DIR2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option selected><?=$pl_MECHTILT_DIR2_4?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td><select NAME="pl_MECHTILT2_5" id="pl_MECHTILT2_5<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_5,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR2_5' id="pl_MECHTILT_DIR2_5<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option selected><?=$pl_MECHTILT_DIR2_5?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td><select NAME="pl_MECHTILT2_6" id="pl_MECHTILT2_6<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_MECHTILT2_6,0,15,1,'no');?>
	 	 <select NAME='pl_MECHTILT_DIR2_6' id="pl_MECHTILT_DIR2_6<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><option selected><?=$pl_MECHTILT_DIR2_6?></option><option>NA</option><option>DOWNTILT</option><option>UPTILT</option></select></td>
		 <? } 
		} ?>
	  </tr>
	  <?php 
	  	if ($viewtype=="FUND"){
	  		analyse_changes_Asset('MECHTILT2',$ASSET_MECHTILT2_1,$ASSET_MECHTILT2_2,$ASSET_MECHTILT2_3,$ASSET_MECHTILT2_4,$ASSET_MECHTILT2_5,$ASSET_MECHTILT2_6,$MECHTILT2_1,$MECHTILT2_2,$MECHTILT2_3,$MECHTILT2_4,$MECHTILT2_5,$MECHTILT2_6);
	  	}
	   ?>
  	  <tr>
		 <td class="parameter_name">Antenna Height 2</td>
		 <td id="cur_ANTHEIGHT2_1"><?=$ANTHEIGHT2_1?>m<?=$ANTHEIGHT2_1_t?></td>
		 <td id="cur_ANTHEIGHT2_2"><?=$ANTHEIGHT2_2?>m<?=$ANTHEIGHT2_2_t?></td>
		 <td id="cur_ANTHEIGHT2_3"><?=$ANTHEIGHT2_3?>m<?=$ANTHEIGHT2_3_t?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_ANTHEIGHT2_4"><?=$ANTHEIGHT2_4?>m<?=$ANTHEIGHT2_4_t?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td id="cur_ANTHEIGHT2_5"><?=$ANTHEIGHT2_5?>m<?=$ANTHEIGHT2_5_t?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td id="cur_ANTHEIGHT2_6"><?=$ANTHEIGHT2_6?>m<?=$ANTHEIGHT2_6_t?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
	     <td class="borderleft"><select NAME="pl_ANTHEIGHT2_1" id="pl_ANTHEIGHT2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_1,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT2_1_t" id="pl_ANTHEIGHT2_1_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_1_t,0,99,1,'yes');?></td>
	  	 <td><select NAME="pl_ANTHEIGHT2_2" id="pl_ANTHEIGHT2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_2,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT2_2_t" id="pl_ANTHEIGHT2_2_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_2_t,0,99,1,'yes');?></td>
	 	 <td><select NAME="pl_ANTHEIGHT2_3" id="pl_ANTHEIGHT2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_3,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT2_3_t" id="pl_ANTHEIGHT2_3_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_3_t,0,99,1,'yes');?></td>
		 <? if ($STATE_4){ ?>
		 <td><select NAME="pl_ANTHEIGHT2_4" id="pl_ANTHEIGHT2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_4,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT2_4_t" id="pl_ANTHEIGHT2_4_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_4_t,0,99,1,'yes');?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><select NAME="pl_ANTHEIGHT2_5" id="pl_ANTHEIGHT2_5<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_5,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT2_5_t" id="pl_ANTHEIGHT2_5_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_5_t,0,99,1,'yes');?></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><select NAME="pl_ANTHEIGHT2_6" id="pl_ANTHEIGHT2_6<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_6,-5,200,1,'no');?>
		 <select NAME="pl_ANTHEIGHT2_6_t" id="pl_ANTHEIGHT2_6_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_ANTHEIGHT2_6_t,0,99,1,'yes');?></td>
		 <? } 
		}?>
	  </tr>
	  <tr>
  		 <td class="parameter_name">Azimuth 2</td>
  		 <td id="cur_AZI2_1"><?=$AZI2_1?></td>
  		 <td id="cur_AZI2_2"><?=$AZI2_2?></td>
  		 <td id="cur_AZI2_3"><?=$AZI2_3?></td>
  		 <? if ($STATE_4){ ?>
  		 <td id="cur_AZI_4"><?=$AZI2_4?></td>
  		 <? }
  		 if ($STATE_5){ ?>
  		 <td id="cur_AZI_5"><?=$AZI2_5?></td>
  		 <? }
  		 if ($STATE_6){ ?>
  		 <td id="cur_AZI_6"><?=$AZI2_6?></td>
  		 <? }
  		 if ($viewtype!="BUILD"){ ?>  		 
   	 	 <td class="borderleft"><select NAME="pl_AZI2_1" id="pl_AZI2_1<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_1);?></td>
  	 	 <td><select NAME="pl_AZI2_2" id="pl_AZI2_2<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_2);?></td>
  	 	 <td><select NAME="pl_AZI2_3" id="pl_AZI2_3<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_3);?></td>
  		 <? if ($STATE_4){ ?>
  		 <td><select NAME="pl_AZI2_4" id="pl_AZI2_4<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_4);?></td>
  		 <? }
  		 if ($STATE_5){ ?>
  		 <td><select NAME="pl_AZI2_5" id="pl_AZI2_5<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_5);?></td>
  		 <? }
  		 if ($STATE_§){ ?>
  		 <td><select NAME="pl_AZI2_6" id="pl_AZI2_6<?=$_POST['band']?>" class="tabledata cleardata form-control"><? get_select_azi($pl_AZI2_6);?></td>
  		 <? } 
  		}?>
	  </tr>
	   <?php 
	  	if ($viewtype=="FUND"){
	  		analyse_changes_Asset('AZI2',$ASSET_AZI2_1,$ASSET_AZI2_2,$ASSET_AZI2_3,$ASSET_AZI2_4,$ASSET_AZI2_5,$ASSET_AZI2_6,$AZI2_1,$AZI2_2,$AZI2_3,$AZI2_4,$AZI2_5,$AZI2_6);
	  	}
	   ?>
 	  <tr>
		 <td class="parameter_name">Feeder type <?=$updatable?></td>
		 <td id="cur_FEEDER_1<?=$_POST['band']?>"><?=$FEEDER_1?></td>
		 <td id="cur_FEEDER_2<?=$_POST['band']?>"><?=$FEEDER_2?></td>
		 <td id="cur_FEEDER_3<?=$_POST['band']?>"><?=$FEEDER_3?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_FEEDER_4<?=$_POST['band']?>"><?=$FEEDER_4?></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td id="cur_FEEDER_5<?=$_POST['band']?>"><?=$FEEDER_5?></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td id="cur_FEEDER_6<?=$_POST['band']?>"><?=$FEEDER_6?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>		 
 	 	 <td class="borderleft">
		 <input type="text" name='pl_FEEDER_1' value="<?=$pl_FEEDER_1?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_1<?=$_POST['band']?>"  placeholder="Select feeder..." /></td>
		 <td><input type="text" name='pl_FEEDER_2' value="<?=$pl_FEEDER_2?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_2<?=$_POST['band']?>"  placeholder="Select feeder..." /></td>
		 <td><input type="text" name='pl_FEEDER_3' value="<?=$pl_FEEDER_3?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_3<?=$_POST['band']?>"  placeholder="Select feeder..." /></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name='pl_FEEDER_4' value="<?=$pl_FEEDER_4?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_4<?=$_POST['band']?>"  placeholder="Select feeder..." /></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><input type="text" name='pl_FEEDER_5' value="<?=$pl_FEEDER_5?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_5<?=$_POST['band']?>"  placeholder="Select feeder..." /></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><input type="text" name='pl_FEEDER_6' value="<?=$pl_FEEDER_6?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_6<?=$_POST['band']?>"  placeholder="Select feeder..." /></td>
		 <? } 
		}?>
	  </tr>
 	  <tr>
		 <td class="parameter_name">Feeder length <?=$updatable?></td>
		 <td><?=$FEEDERLEN_1?>m<?=$FEEDERLEN_1_t?></td>
		 <td><?=$FEEDERLEN_2?>m<?=$FEEDERLEN_2_t?></td>
		 <td><?=$FEEDERLEN_3?>m<?=$FEEDERLEN_3_t?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$FEEDERLEN_4?>m<?=$FEEDERLEN_4_t?></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><?=$FEEDERLEN_5?>m<?=$FEEDERLEN_5_t?></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td><?=$FEEDERLEN_6?>m<?=$FEEDERLEN_6_t?></td>
		 <? }  
		 if ($viewtype!="BUILD"){ ?>		 
 	 	 <td class="borderleft"><select NAME="pl_FEEDERLEN_1" id="pl_FEEDERLEN_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control borderleft"><? get_select_numbers($pl_FEEDERLEN_1,0,200,1,'no');?>
		 <select NAME="pl_FEEDERLEN_1_t" id="pl_FEEDERLEN_1_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_1_t,0,99,5,'yes');?></td>
	 	 <td><select NAME="pl_FEEDERLEN_2" id="pl_FEEDERLEN_2<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_2,0,200,1,'no');?>
		 <select NAME="pl_FEEDERLEN_2_t" id="pl_FEEDERLEN_2_t<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_2_t,0,99,5,'yes');?></td>
	 	 <td><select NAME="pl_FEEDERLEN_3" id="pl_FEEDERLEN_3<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_3,0,200,1,'no');?>
		 <select NAME="pl_FEEDERLEN_3_t" id="pl_FEEDERLEN_3_t<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_3_t,0,99,5,'yes');?></td>
		 <? if ($STATE_4){ ?>
		 <td><select NAME="pl_FEEDERLEN_4" id="pl_FEEDERLEN_4<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_4,0,200,1,'no');?>
		 <select NAME="pl_FEEDERLEN_4_t" id="pl_FEEDERLEN_4_t<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_4_t,0,99,5,'yes');?></td>
		 <? } 
		 if ($STATE_5){ ?>
		 <td><select NAME="pl_FEEDERLEN_5" id="pl_FEEDERLEN_4<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_5,0,200,1,'no');?>
		 <select NAME="pl_FEEDERLEN_5_t" id="pl_FEEDERLEN_4_t<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_5_t,0,99,5,'yes');?></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td><select NAME="pl_FEEDERLEN_6" id="pl_FEEDERLEN_6<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_6,0,200,1,'no');?>
		 <select NAME="pl_FEEDERLEN_6_t" id="pl_FEEDERLEN_6_t<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata cleardata form-control"><? get_select_numbers($pl_FEEDERLEN_6_t,0,99,5,'yes');?></td>
		 <? } 
		} ?>
	</tr>
	<tr>
 		<td colspan="<?=$colspan2?>" class="table_head">Settings the same for all technologies:</td>
    </tr>
    <tr>
    	 <td class="tableheader"><font color="blue">Feeder sharing <?=$updatable?></td>
		 <td><input type="text" name="FEEDERSHARE_1" id="FEEDERSHARE_1<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_1?>"></td>
		 <td><input type="text" name="FEEDERSHARE_2" id="FEEDERSHARE_2<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_2?>"></td>
		 <td><input type="text" name="FEEDERSHARE_3" id="FEEDERSHARE_3<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_3?>"></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name="FEEDERSHARE_4" id="FEEDERSHARE_4<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_4?>"></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><input type="text" name="FEEDERSHARE_5" id="FEEDERSHARE_5<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_5?>"></td>
		 <? } 
		 if ($STATE_6){ ?>
		 <td><input type="text" name="FEEDERSHARE_6" id="FEEDERSHARE_6<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_6?>"></td>
		 <? }  
		 if ($viewtype!="BUILD"){ ?>
		 <td><input type="text" name="pl_FEEDERSHARE_1" id="pl_FEEDERSHARE_1<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_1?>"></td>
		 <td><input type="text" name="pl_FEEDERSHARE_2" id="pl_FEEDERSHARE_2<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_2?>"></td>
		 <td><input type="text" name="pl_FEEDERSHARE_3" id="pl_FEEDERSHARE_3<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_3?>"></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name="pl_FEEDERSHARE_4" id="pl_FEEDERSHARE_4<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_4?>"></td>
		 <? }
		 if ($STATE_5){ ?>
		 <td><input type="text" name="pl_FEEDERSHARE_5" id="pl_FEEDERSHARE_5<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_5?>"></td>
		 <? }
		 if ($STATE_6){ ?>
		 <td><input type="text" name="pl_FEEDERSHARE_6" id="pl_FEEDERSHARE_6<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_6?>"></td>
		 <? } 
		} ?>
	 </tr>
  </tr>
  </tbody>
  </table>
  <br><br><br>
</div>
<?
if (substr_count($guard_groups, 'Radioplanners')=="1" && $viewtype!="_FUND" && $_POST['print']!="yes"){ ?>
	<p align="center">
	<font color="blue"><b>BSDS comments <?=$_POST['band']?></b></font><br>
	<textarea name="pl_COMMENTS" cols="100" rows="5" style="width:400px;"><?=$pl_COMMENTS?></textarea>
	</p>

	<p align="center">
	<font color="blue" size="1"><u>Remark:</u> Text in blue MUST be filled in by the radioplanner before a new BSDS can be created!<br>
	Those values could NOT be downloaded from Asset or the network</font></p>
<?
}else{
	?><div style="width:400px;margin: 0px auto;"><?=$pl_COMMENTS?></div><?
}

if ($pl_is_BSDS_accepted=="Accepted" && $viewtype=="FUND" &&  $viewhistory=="no"){ //&& substr_count($guard_groups, 'Administrator')!=1
	$value_save="Save current + Feeder + Tilt data \n(Fields with a * can be updated)";
}else{
	$value_save="Save current & planned info!";
}

if ($pl_is_BSDS_accepted=="Accepted" && $viewtype=="FUND" && substr_count($guard_groups, 'Radioplanners')=="1"){
	?><input type="hidden" name="onlyfeeder" value="yes"><?
}

if (((substr_count($guard_groups, 'Radioplanners')=="1" && $viewtype=="PRE" && $_POST['print']!="yes")
	or ($pl_is_BSDS_accepted=="Accepted" && $viewtype=="FUND"  && substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['print']!="yes")
	or ($viewtype=="POST"  && substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['print']!="yes"))
	&& $_POST['disable_save']!='yes'
	){ //CURRENT IS ALWAYS UPDATEBLE ** Only group 'Radioplanners' can update BSDSs
 ?>
 <p align='center'><input type="submit" class="btn btn-primary subCurPl" value="<?=$value_save?>" id="save_bsdsdata" data-techno="<?=$_POST['band']?>" data-viewtype="<?=$viewtype?>"></p>
<?
}

OCILogoff($conn_Infobase);
if ($_POST['print']!="yes"){
?>
</form>
<?php } ?>
</div>

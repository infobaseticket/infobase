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
					$("#"+table+" .tabledata").each(
						function(intIndex){
							pl_attribute_type=$(this).attr('type');
							pl_attribute_name=$(this).attr('name');
							//alert(pl_attribute_name+pl_attribute_name.indexOf("ANTTYPE"));
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

		var divs = $('.bsdsScrollRight, .bsdsScrollLeft');
		$(window).scroll(function(){
		   if($(window).scrollTop()<200){
		         divs.stop(true,true).fadeIn("fast");
		   } else {
		         divs.stop(true,true).fadeOut("fast");
		   }
		});

		function get_select2(filter,field,classfield){		
			$('.'+classfield).select2({
		    	initSelection: function(element, callback) {
					callback({id: element.val(), text: element.val() });
				},
			    minimumInputLength: 1,
			    ajax: {
			      url: "scripts/current_planned/ajax/field_list.php",
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
		if (band=='G9'){
			var filter='900';
		}else if (band=='G18'){
			var filter='1800';
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
	});
	</script>
	<?php
	/*
<script type="text/javascript" src="scripts/current_planned/current_planned.js"></script><!-- functions for autocomplete and form submit -->
<? */ }else{
	$printclass="print";
	?>
	<link rel="stylesheet" href="<?=$config['sitepath_url']?>/bsds/scripts/current_planned/currentplanned_print.css" type="text/css" media="screen,print" />
	<?php
}

if (!empty($_POST['band'])){
	if ($_POST['band']=="G9"){
		$sec1="4";
		$sec2="5";
		$sec3="6";
		$sec4="0";
	}else if($_POST['band']=="G18"){
		$sec1="1";
		$sec2="2";
		$sec3="3";
		$sec4="0";
	}
}else{
echo "ERROR, no band specified!";
die;
}
if ($_POST['bsdsbobrefresh']=="PRE"){
	$bsdsdata="PRE READY TO BUILD";
	$_POST['bsdsbobrefresh']="";
}else{
	$bsdsdata=$_POST['bsdsdata'];
}

//echo "BSDSDATA: <pre>".print_r($bsdsdata,true)."</pre>";
if ($_POST['status']=="FUNDHIST"){ //HISTORY VIEW
	$viewtype="FUND";
	$color="BSDS_funded";
	$viewhistory="yes";
	$status="BSDS FUNDED";
}else if ($_POST['status']=="POSTHIST"){//HISTORY VIEW
	$viewtype="POST";
	$viewhistory="yes";
	$color="SITE_funded";
	$status="SITE FUNDED";
}else if ($_POST['status']=="BUILDHIST"){//HISTORY VIEW
	$viewtype="BUILD";
	$viewhistory="yes";
	$color="BSDS_asbuild";
	$status="BSDS AS BUILD";
}else if ($_POST['status']=="PRE"){ //PRE VIEW
	$viewtype="PRE";
	$viewhistory="no";
	$color="BSDS_preready";
	$status="PRE READY TO BUILD";
}else if ($_POST['status']=="PREHIST"){ //HISTORY PRE VIEW
	$viewtype="PRE";
	$viewhistory="yes";
	$color="BSDS_preready";
	$status="PRE READY TO BUILD HISTORY";
}else{
	$bsdsdata=json_decode($_POST['bsdsdata'],true);
	//echo "<pre>".print_r($bsdsdata,true);
	if ($bsdsdata[$_POST['band'].'STATUS']=="BSDS FUNDED"){
		$viewtype="FUND";
		$viewhistory="no";
		$status="BSDS FUNDED";
	}else if ($bsdsdata[$_POST['band'].'STATUS']=="SITE FUNDED"){
		$viewtype="POST";
		$viewhistory="no";
		$status="SITE FUNDED";
	}else if ($bsdsdata[$_POST['band'].'STATUS']=="BSDS AS BUILD"){
		$viewtype="BUILD";
		$viewhistory="no";
		$status="BSDS AS BUILD";
	}else if ($bsdsdata[$_POST['band'].'STATUS']=="PRE READY TO BUILD"){
		$viewtype="PRE";
		$viewhistory="no";
		$status="PRE READY TO BUILD";
	}
	$color=$bsdsdata[$_POST['band'].'COLOR'];
}

$cols_pl_sec=get_cols("BSDS_PL_GSM_SEC");
$cols_pl=get_cols("BSDS_PL_GSM");

$check_current_exists=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['bsdsbobrefresh'],'allsec',$_POST['donor'],$_POST['lognodeID_GSM'],$viewtype);
if ($check_current_exists!=0 || $viewtype=="FUND"){
	$check_planned_exists=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],'allsec',$viewtype,$_POST['donor']);
	
	if ($check_planned_exists==="error"){
		echo "error";
		?>
		<script language="JavaScript">
		Messenger().post({
		  message: "<h1>Sytem error</h1>There are too many records in the database for <?=$_POST['band']?> BSDS! Please contact Frederick Eyland",
		  type: 'error',
		  showCloseButton: false
		});
		</script>
		<?
	}
}else{
	$check_planned_exists=0;
}

if ($_POST['print']=="yes" && $check_planned_exists==0){
	echo "<blockquote><p>NO PLANNED DATA AVAILABLE FOR ".$_POST['band']."</p></blockquote>";
	die;
}
//echo $check_current_exists. "-".$check_planned_exists;
if($check_planned_exists==0 && $status=="BSDS FUNDED"){
	?>
	<script language="JavaScript">
	Messenger().post({
		  message: "<h3>No planned data available</h3>Please defund BSDS by removing U305 in NET1 and save data for <?=$_POST['band']?>.<br>Then you will be able to refund. (with a newer date)",
		  type: 'error',
		  showCloseButton: false
		});
	</script>
	<?
}

$gen_info=get_BSDS_generalinfo($_POST['bsdskey']);
$pl_is_BSDS_accepted=$gen_info['TEAML_APPROVED'][0];
$pl_CHANGEDATE=$gen_info['UPDATE_AFTER_COPY'][0];

$band=$_POST['band'];
$bsdskey=$_POST['bsdskey'];
$bsdsbobrefresh=$_POST['bsdsbobrefresh'];
$donor=$_POST['donor'];
$lognodeID_GSM=$_POST['lognodeID_GSM'];

if ($_POST['action']!="save"){
	if($check_planned_exists!="0"){
		include("planned_data.php");
	}
}

if ($bsdsdata!="PRE READY TO BUILD"){
	if ($pl_is_BSDS_accepted=="Accepted" && ($status=="BSDS FUNDED")){
		$updatable="<font color=red>*";
	}else{
		$updatable="";
	}
}else{
	$updatable="";
}
include("current_data.php");
if ($check_planned_exists=="0" && $_POST['action']!="save"){
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
/*******************************************************************************************************************/
/***********************************     OUTPUT DATA TO SCREEN  ****************************************************/
/*******************************************************************************************************************/

?>
<div id="<?=$_POST['print']?>curpl_<?=$_POST['bsdskey']?><?=$_POST['band']?><?=$_POST['status']?><?php echo str_replace(':', '', str_replace('/', '', str_replace(' ', '', $_POST['bsdsbobrefresh']))); ?>" class="<?=$printclass?>" style="page-break-after: always">
<?php
if ($_POST['print']!="yes"){
?>
<form action="scripts/current_planned/save_pl_cu_GSM.php" method="post" id="current_planned_form<?=$_POST['band']?><?=$viewtype?>" role="form">
<input type="hidden" name="band" value="<?=$_POST['band']?>">
<input type="hidden" name="pl_band" value="<?=$_POST['band']?>">
<input type="hidden" name="action" value="save">
<input type="hidden" name="lognode" value="<?=$_POST['lognodeID_GSM']?>">
<input type="hidden" name="bsdskey" value="<?=$_POST['bsdskey']?>">
<input type="hidden" name="bsdsbobrefresh" value="<?=$_POST['bsdsbobrefresh']?>">
<input type="hidden" name="viewtype" value="<?=$_POST['status']?>">

<input type="hidden" name="FEEDERSHARE_1" value="<?=$FEEDERSHARE_1?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_1<?=$_POST['band']?>">
<input type="hidden" name="FEEDERSHARE_2" value="<?=$FEEDERSHARE_2?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_2<?=$_POST['band']?>">
<input type="hidden" name="FEEDERSHARE_3" value="<?=$FEEDERSHARE_3?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_3<?=$_POST['band']?>">
<input type="hidden" name="FEEDERSHARE_4" value="<?=$FEEDERSHARE_4?>" id="<?=$_POST['print']?>cur_FEEDERSHARE_4<?=$_POST['band']?>">

<?php	
	for ($i=1;$i<=4;$i++){
		foreach ($cols_pl_sec['COLUMN_NAME'] as $key => $column) {
			$cur_parname=$column."_".$i;
			if ($column=="ANTHEIGHT1" || $column=="ANTHEIGHT2" || $column=="MECHTILT1" || $column=="MECHTILT2" || $column=="MECHTILT1" || $column=="FEEDERLEN"){
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
?>
<button type="button" data-srollband="scroll<?=$_POST['band']?>" class="btn btn-success btn-xs bsdsScrollLeft leftArrow">
  <span class="glyphicon glyphicon-backward"></span> Left
</button>
<button type="button" data-srollband="scroll<?=$_POST['band']?>" class="btn btn-success btn-xs bsdsScrollRight rightArrow">
  <span class="glyphicon glyphicon-forward"></span> Right
</button>
<?php } 

if ($STATE_4){
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

?>

<div class="table-responsive table-responsive-force " id="scroll<?=$_POST['band']?>">
<table class="table table-bordered table-condensed table-responsive-force" id="bsds<?=$_POST['candidate']?><?=$_POST['band']?>">
<?
if ($viewhistory=="yes"){?>
<caption class="<?=$color?>">!!! Your are viewing an old BSDS with status <?=$status?> !!!!</caption><?
}else{ ?>

	<caption class="<?=$color?>">STATUS: <?=$status?>
 	<? if  ($viewtype!="PRE" ){ ?>
 	&nbsp;&#40;BOB REFRESH: <?=$_POST['bsdsbobrefresh']?>&#41; &#40;<?=$pl_is_BSDS_accepted?>&#41;
 	<? } ?>
 	</caption>
<?
}
if ($check_current_exists==0 && $_POST['status']=="FUND" && $status!="PRE READY TO BUILD"){
$pl=""
?>
<caption class="error">NO LIVE DATA AVAILABLE AT TIME OF FUNDING!</caption>
<? } ?>
<tbody>
	<tr>
		<td style="min-width:150px;">&nbsp;</td>
		<td colspan="<?=$colspan2?>" class="tableheader">
			<? if ($check_current_exists=="0"){ ?>
				<font color="red">NOT SAVED
			<? } ?>
				CURRENT <?=$_POST['band']?>
				<?
				if ($bsdsdata!="PRE READY TO BUILD"){
				   if ($status!="BSDS FUNDED"){ ?>
					<font color="orange">LIVE SITUATION from OSS/ASSET
				<? }else{?>
					SITUATION FROM <?=$CHANGEDATE;?>
				<? } ?>
			<? }else{?>
				SITUATION FROM <?=$CHANGEDATE;?>
			<? } ?>
		</td>		
		<?	
		if ($viewtype!="BUILD"){
			$clas="tableheader";
			if ($check_planned_exists=="0"){
				$pl.="<font color=red>NOT SAVED ";
				$general_override="NOT_SAVED";
			}
			$pl.="PLANNED ".$_POST['band']." SITUATION ";
			if ($pl_CHANGEDATE && $check_planned_exists==1){
				$pl.= "&#40;Last update: $pl_CHANGEDATE &#41;";
			}
		?>
		<td colspan="<?=$colspan2?>" class="tableheader borderleft">
		<?=$pl?>
		<?php if ($viewhistory=="no" && $_POST['print']!="yes"){ ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs clear" name="clear" data-table="bsds<?=$_POST['candidate']?><?=$_POST['band']?>">Clear</button>
		<? } ?>
		</td>
		<?php } ?>
 	</tr>
	<tr>
		<td class="bsdsinfoband"><?=$_POST['siteID']?><br><?=$_POST['band']?><br>[<?=$_POST['bsdskey']?>]<br><?=$_POST['bsdsbobrefresh']?><br>LAC: <?=$LAC?><br></td>
		<td colspan="<?=$colspan2?>">
			<table width="100%">
			<tr>
				<td class="tableheader">Cabinettype</td>
				<td><?=$CABTYPE?></td>
				<td class="tableheader"># cabinet <?=$_POST['band']?></td>
				<td><?=$NR_OF_CAB?></td>
			</tr>
			<tr>
				<td class="tableheader">CDU Type</td>
				<td><?=$CDUTYPE?></td>
				<td class="tableheader"><font color="blue">BBS</font></td>
				<td><SELECT NAME="BBS" id="BBS<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_BBS($BBS);?></select></td>
			</tr>
			<tr>
				<td class="tableheader"><font color="blue">DXU CAB 1</font></td>
				<td><SELECT NAME="DXUTYPE1" id="DXUTYPE1<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_DXU($DXUTYPE1); ?> </SELECT></td>
				<td class="tableheader"><span class="cab2<?=$_POST['band']?> <?=$cab2?>"><font color="blue">DXU CAB 2</font></span></td>
				<td><span class="cab2<?=$_POST['band']?> <?=$cab2?>"><SELECT NAME="DXUTYPE2" id="DXUTYPE2<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_DXU($DXUTYPE2); ?></SELECT></span></td>
			</tr>
			<tr>
				<td class="tableheader"><span class="cab3<?=$_POST['band']?> <?=$cab3?>"><font color="blue">DXU CAB 3</font></span></td>
				<td><span class="cab3<?=$_POST['band']?> <?=$cab3?>"><SELECT NAME="DXUTYPE3" id="DXUTYPE3<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_DXU($DXUTYPE3); ?> </SELECT></span></td>
				<td class="tableheader"><font color="blue">Solution</td>
				<td><SELECT NAME="PLAYSTATION" id="PLAYSTATION<?=$_POST['band']?>" class="tabledata form-control"><option selected><?=$PLAYSTATION?><option>Normal</option><option>Playstation</option><option>Stealth</option></select></td>
			</tr>
			</tr>
			</table>
		</td>
		<?php if ($viewtype!="BUILD"){ ?>
		<td colspan="<?=$colspan2?>" class="borderleft">
			<table width="100%">
			<tr>
				<td class="tableheader">Cabinettype</td>
				<td><input type="text" value="<?=$pl_CABTYPE?>" name="pl_CABTYPE" id="pl_CABTYPE<?=$_POST['band']?>" placeholder="Select cabinet..." tabindex="-1" class="dynamic cabtype_list<?=$_POST['band']?> form-control"></td>
				<td class="tableheader"># cabinet <?=$_POST['band']?></td>
				<td><SELECT NAME="pl_NR_OF_CAB" class="tabledata form-control" id="pl_NR_OF_CAB<?=$_POST['band']?>">
				<? get_select_numbers($pl_NR_OF_CAB,0,3,1,'no');?></td>
			 </tr>
			 <tr>
				<td class="tableheader">CDU Type</td>
				<td><SELECT NAME="pl_CDUTYPE" class="tabledata form-control cabtype_changer" id="pl_CDUTYPE<?=$_POST['band']?>">
				<? echo get_select_CDU($pl_CDUTYPE); ?></SELECT>
				</td>
				<td class="tableheader"><font color="blue">BBS</font></td>
				<td><SELECT NAME="pl_BBS" class="tabledata form-control" id="pl_BBS<?=$_POST['band']?>"><? echo get_select_BBS($pl_BBS);?></SELECT></td>
			</tr>
			<tr>
				<td class="tableheader"><font color="blue">DXU CAB 1</font></td>
				<td><SELECT NAME="pl_DXUTYPE1" class="tabledata form-control cabtype_changer" id="pl_DXUTYPE1<?=$_POST['band']?>">	<? echo get_select_DXU($pl_DXUTYPE1); ?></SELECT></span></td>
				<td class="tableheader"><span class="cab2<?=$_POST['band']?> <?=$cab2?>"><font color="blue">DXU CAB 2</font></span></td>
				<td><span class="cab2<?=$_POST['band']?> <?=$cab2?>"><SELECT NAME="pl_DXUTYPE2" class="tabledata form-control" id="pl_DXUTYPE2<?=$_POST['band']?>"><? echo get_select_DXU($pl_DXUTYPE2); ?></SELECT></span></td>
				
			</tr>
			<tr>
				<td class="tableheader"><span class="cab3<?=$_POST['band']?> <?=$cab3?>"><font color="blue">DXU CAB 3</font></span></td>
				<td><span class="cab3<?=$_POST['band']?> <?=$cab3?>"><SELECT NAME="pl_DXUTYPE3" class="tabledata form-control" id="pl_DXUTYPE3<?=$_POST['band']?>"><? echo get_select_DXU($pl_DXUTYPE3); ?></SELECT></span></td>
				<td class="tableheader"><font color="blue">Solution</td>
				<td><SELECT NAME="pl_PLAYSTATION" id="pl_PLAYSTATION<?=$_POST['band']?>" class="tabledata form-control"><option selected><?=$pl_PLAYSTATION?><option>Normal</option><option>Playstation</option><option>Stealth</option></select></td></td>
			</tr>
			</tr>
			</table>
		</td>
		<?php } ?>
	</tr>
	<tr>
		 <td>&nbsp;</td>
		 <td class="tableheader">Sector <?=$sec1?></td>
		 <td class="tableheader">Sector <?=$sec2?></td>
		 <td class="tableheader">Sector <?=$sec3?></td>
		 <? if ($STATE_4){ ?>
		 <td class="tableheader">Sector <?=$sec4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="tableheader borderleft">Sector <?=$sec1?></td>
		 <td class="tableheader">Sector <?=$sec2?></td>
		 <td class="tableheader">Sector <?=$sec3?></td>
		 <? if ($STATE_4){ ?>
		 <td class="tableheader">Sector <?=$sec4?></td>
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
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><?=$STATE_1?></td>
		 <td><?=$STATE_2?></td>
		 <td><?=$STATE_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$STATE_4?></td>
		 <? } 
		}?>
	  </tr>
	  <tr>
		 <td class="tableheader">Config</td>
		 <td><?=$CONFIG_1?></td>
		 <td><?=$CONFIG_2?></td>
		 <td><?=$CONFIG_3?></td>
		 <? if ($STATE_4){ ?>
		  <td><?=$CONFIG_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <input type="text" name='pl_CONFIG_1' value="<?=$pl_CONFIG_1?>" class="dynamic form-control config_list<?=$_POST['band']?>" id="pl_CONFIG_1<?=$_POST['band']?>"  placeholder="Select config..." /></td>
		 <td><input type="text" name='pl_CONFIG_2' value="<?=$pl_CONFIG_2?>" class="dynamic form-control config_list<?=$_POST['band']?>" id="pl_CONFIG_2<?=$_POST['band']?>"  placeholder="Select config..." /></td>
		 <td><input type="text" name='pl_CONFIG_3' value="<?=$pl_CONFIG_3?>" class="dynamic form-control config_list<?=$_POST['band']?>" id="pl_CONFIG_3<?=$_POST['band']?>"  placeholder="Select config..." /></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name='pl_CONFIG_4' value="<?=$pl_CONFIG_4?>" class="dynamic form-control config_list<?=$_POST['band']?>" id="pl_CONFIG_4<?=$_POST['band']?>"  placeholder="Select config..." /></td>
		 <? } 
		}?>
	</tr>

	<tr>
		 <td class="tableheader"><font color="blue">TMA</td>
		 <td><SELECT NAME="TMA_1" id="TMA_1<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TMA($TMA_1);?></SELECT></td>
		 <td><SELECT NAME="TMA_2" id="TMA_2<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TMA($TMA_2);?></SELECT></td>
		 <td><SELECT NAME="TMA_3" id="TMA_3<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TMA($TMA_3);?></SELECT></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="TMA_4" id="TMA_4<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TMA($TMA_4);?></SELECT></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_TMA_1" class="tabledata form-control" id="pl_TMA_1<?=$_POST['band']?>"><? echo get_select_TMA($pl_TMA_1);?></SELECT></td>
		 <td><SELECT NAME="pl_TMA_2" class="tabledata form-control" id="pl_TMA_2<?=$_POST['band']?>"><? echo get_select_TMA($pl_TMA_2);?></SELECT></td>
		 <td><SELECT NAME="pl_TMA_3" class="tabledata form-control" id="pl_TMA_3<?=$_POST['band']?>"><? echo get_select_TMA($pl_TMA_3);?></SELECT></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_TMA_4" class="tabledata form-control" id="pl_TMA_4<?=$_POST['band']?>"><? echo get_select_TMA($pl_TMA_4);?></SELECT></td>
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
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_FREQ_ACTIVE1_1" id="pl_FREQ_ACTIVE1_1<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_1,0,12,1,'no');?></td>
		 <td><SELECT NAME="pl_FREQ_ACTIVE1_2" id="pl_FREQ_ACTIVE1_2<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_2,0,12,1,'no');?></td>
		 <td><SELECT NAME="pl_FREQ_ACTIVE1_3" id="pl_FREQ_ACTIVE1_3<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_3,0,12,1,'no');?></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_FREQ_ACTIVE1_4" id="pl_FREQ_ACTIVE1_4<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE1_4,0,12,1,'no');?></td>
		 <? } 
		}?>
	</tr>
	<tr class="cab2<?=$_POST['band']?> <?=$cab2?>">
		 <td class="tableheader">FREQ active network CAB2</td>
		 <td><?=$FREQ_ACTIVE2_1?></td>
		 <td><?=$FREQ_ACTIVE2_2?></td>
		 <td><?=$FREQ_ACTIVE2_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$FREQ_ACTIVE2_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_FREQ_ACTIVE2_1" id="pl_FREQ_ACTIVE2_1<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_1,0,12,1,'no');?> </td>
		 <td><SELECT NAME="pl_FREQ_ACTIVE2_2" id="pl_FREQ_ACTIVE2_2<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_2,0,12,1,'no');?></td>
		 <td><SELECT NAME="pl_FREQ_ACTIVE2_3" id="pl_FREQ_ACTIVE2_3<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_3,0,12,1,'no');?></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_FREQ_ACTIVE2_4" id="pl_FREQ_ACTIVE2_4<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE2_4,0,12,1,'no');?></td>
		 <? } 
		}?>
	</tr>
	<tr class="cab3<?=$_POST['band']?> <?=$cab3?>">
		 <td class="tableheader">FREQ active network CAB3</td>
		 <td><?=$FREQ_ACTIVE3_1?></td>
		 <td><?=$FREQ_ACTIVE3_2?></td>
		 <td><?=$FREQ_ACTIVE3_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$FREQ_ACTIVE3_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_FREQ_ACTIVE3_1" id="pl_FREQ_ACTIVE3_1<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_1,0,12,1,'no');?> </td>
		 <td><SELECT NAME="pl_FREQ_ACTIVE3_2" id="pl_FREQ_ACTIVE3_2<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_2,0,12,1,'no');?></td>
		 <td><SELECT NAME="pl_FREQ_ACTIVE3_3" id="pl_FREQ_ACTIVE3_3<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_3,0,12,1,'no');?></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_FREQ_ACTIVE3_4" id="pl_FREQ_ACTIVE3_4<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_FREQ_ACTIVE3_4,0,12,1,'no');?></td>
		 <? } 
		}?>
	</tr>
	  <?
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
		?>
	<tr bgcolor='red'>
		 <td class='tableheader'><font color='white'>Number active FREQ in SWITCH</td>
		 <td><font color='white'><?=$FREQ_ACTIVE_SWITCH_1?></td>
		 <td><font color='white'><?=$FREQ_ACTIVE_SWITCH_2?></td>
		 <td><font color='white'><?=$FREQ_ACTIVE_SWITCH_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><font color='white'><?=$FREQ_ACTIVE_SWITCH_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class='borderleft'><font color='white'><?=$FREQ_ACTIVE_SWITCH_1?></td>
		 <td><font color='white'><?=$FREQ_ACTIVE_SWITCH_2?></td>
		 <td><font color='white'><?=$FREQ_ACTIVE_SWITCH_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><font color='white'><?=$FREQ_ACTIVE_SWITCH_4?></td>
		 <? } 
		}?>
	</tr>
	<?}?>
	<tr>
		 <td class="tableheader">TRU installed CAB1</td>
		 <td><?=$TRU_INST1_1_1?> <?=$TRU_TYPE1_1_1?><br><?=$TRU_INST1_2_1?> <?=$TRU_TYPE1_2_1?></td>
		 <td><?=$TRU_INST1_1_2?> <?=$TRU_TYPE1_1_2?><br><?=$TRU_INST1_2_2?> <?=$TRU_TYPE1_2_2?></td>
		 <td><?=$TRU_INST1_1_3?> <?=$TRU_TYPE1_1_3?><br><?=$TRU_INST1_2_3?> <?=$TRU_TYPE1_2_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$TRU_INST1_1_4?> <?=$TRU_TYPE1_1_4?> &nbsp; <?=$TRU_INST1_2_4?> <?=$TRU_TYPE1_2_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <SELECT NAME="pl_TRU_INST1_1_1" id="pl_TRU_INST1_1_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST1_1_1,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE1_1_1" id="pl_TRU_TYPE1_1_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_1);?></select><br>
		 <SELECT NAME="pl_TRU_INST1_2_1" id="pl_TRU_INST1_2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST1_2_1,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE1_2_1" id="pl_TRU_TYPE1_2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><?echo get_select_TRU($pl_TRU_TYPE1_2_1);?></select>
		 </td>
		 <td>
		 <SELECT NAME="pl_TRU_INST1_1_2" id="pl_TRU_INST1_1_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST1_1_2,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE1_1_2" id="pl_TRU_TYPE1_1_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_2);?></select><br>
		 <SELECT NAME="pl_TRU_INST1_2_2" id="pl_TRU_INST1_2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST1_2_2,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE1_2_2" id="pl_TRU_TYPE1_2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_2_2);?></select>
		 </td>
		 <td>
		 <SELECT NAME="pl_TRU_INST1_1_3" id="pl_TRU_INST1_1_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST1_1_3,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE1_1_3" id="pl_TRU_TYPE1_1_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_3);?></select><br>
		 <SELECT NAME="pl_TRU_INST1_2_3" id="pl_TRU_INST1_2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST1_2_3,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE1_2_3" id="pl_TRU_TYPE1_2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_2_3);?></select>
		 </td>
		 <? if ($STATE_4){ ?>
		 <td>
		 <SELECT NAME="pl_TRU_INST1_1_4" id="pl_TRU_INST1_1_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST1_1_4,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE1_1_4" id="pl_TRU_TYPE1_1_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_1_4);?></select><br>
		 <SELECT NAME="pl_TRU_INST1_2_4" id="pl_TRU_INST1_2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST1_2_4,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE1_2_4" id="pl_TRU_TYPE1_2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE1_2_4);?></select>
		 </td>
		 <? } 
		}?>
	  </tr>
	  <tr class="cab2<?=$_POST['band']?> <?=$cab2?>">
		 <td class="tableheader" width="120px">TRU installed CAB2</td>
		 <td><?=$TRU_INST2_1_1?> <?=$TRU_TYPE2_1_1?><br><?=$TRU_INST2_2_2?> <?=$TRU_TYPE2_2_1?></td>
		 <td><?=$TRU_INST2_1_2?> <?=$TRU_TYPE2_1_2?><br><?=$TRU_INST2_2_3?> <?=$TRU_TYPE2_2_2?></td>
		 <td><?=$TRU_INST2_1_3?> <?=$TRU_TYPE2_1_3?><br><?=$TRU_INST2_2_3?> <?=$TRU_TYPE2_2_3?></td>
		 <? if ($STATE_4){ ?>
		  <td><?=$TRU_INST2_1_4?> <?=$TRU_TYPE2_1_4?> &nbsp; <?=$TRU_INST2_2_4?> <?=$TRU_TYPE2_2_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <SELECT NAME="pl_TRU_INST2_1_1" id="pl_TRU_INST2_1_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST2_1_1,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE2_1_1" id="pl_TRU_TYPE2_1_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_1);?></select><br>
		 <SELECT NAME="pl_TRU_INST2_2_1" id="pl_TRU_INST2_2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST2_2_1,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE2_2_1" id="pl_TRU_TYPE2_2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_1);?></select>
		 </td>
		 <td>
		 <SELECT NAME="pl_TRU_INST2_1_2" id="pl_TRU_INST2_1_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST2_1_2,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE2_1_2" id="pl_TRU_TYPE2_1_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_2);?></select><br>
		 <SELECT NAME="pl_TRU_INST2_2_2" id="pl_TRU_INST2_2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST2_2_2,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE2_2_2" id="pl_TRU_TYPE2_2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_2);?></select>
		 </td>
		 <td>
		 <SELECT NAME="pl_TRU_INST2_1_3" id="pl_TRU_INST2_1_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST2_1_3,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE2_1_3" id="pl_TRU_TYPE2_1_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_3);?></select><br>
		 <SELECT NAME="pl_TRU_INST2_2_3" id="pl_TRU_INST2_2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST2_2_3,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE2_2_3" id="pl_TRU_TYPE2_2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_3);?></select>
		 </td>
		 <? if ($STATE_4){ ?>
		 <td>
		 <SELECT NAME="pl_TRU_INST2_1_4" id="pl_TRU_INST2_1_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST2_1_4,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE2_1_4" id="pl_TRU_TYPE2_1_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_1_4);?></select><br>
		 <SELECT NAME="pl_TRU_INST2_2_4" id="pl_TRU_INST2_2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST2_2_4,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE2_2_4" id="pl_TRU_TYPE2_2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE2_2_4);?></select>
		 </td>
		 <? } 
		}?>
	  </tr>
	  <tr class="cab3<?=$_POST['band']?> <?=$cab3?>">
		 <td class="tableheader">TRU installed CAB3</td>
		 <td><?=$TRU_INST3_1_1?> <?=$TRU_TYPE3_1_1?><br><?=$TRU_INST3_2_2?> <?=$TRU_TYPE3_2_1?></td>
		 <td><?=$TRU_INST3_1_2?> <?=$TRU_TYPE3_1_2?><br><?=$TRU_INST3_2_3?> <?=$TRU_TYPE3_2_2?></td>
		 <td><?=$TRU_INST3_1_3?> <?=$TRU_TYPE3_1_3?><br><?=$TRU_INST3_2_3?> <?=$TRU_TYPE3_2_3?></td>
		 <? if ($STATE_4){ ?>
		  <td><?=$TRU_INST3_1_4?> <?=$TRU_TYPE3_1_4?> &nbsp; <?=$TRU_INST3_2_4?> <?=$TRU_TYPE3_2_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <SELECT NAME="pl_TRU_INST3_1_1" id="pl_TRU_INST3_1_1<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST3_1_1,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE3_1_1" id="pl_TRU_TYPE3_1_1<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_1);?></select><br>
		 <SELECT NAME="pl_TRU_INST3_2_1" id="pl_TRU_INST3_2_1<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST3_2_1,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE3_2_1" id="pl_TRU_TYPE3_2_1<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_1);?></select>
		 </td>
		 <td>
		 <SELECT NAME="pl_TRU_INST3_1_2" id="pl_TRU_INST3_1_2<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST3_1_2,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE3_1_2" id="pl_TRU_TYPE3_1_2<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_2);?></select><br>
		 <SELECT NAME="pl_TRU_INST3_2_2" id="pl_TRU_INST3_2_2<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST3_2_2,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE3_2_2" id="pl_TRU_TYPE3_2_2<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_2);?></select>
		 </td>
		 <td>
		 <SELECT NAME="pl_TRU_INST3_1_3" id="pl_TRU_INST3_1_3<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST3_1_3,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE3_1_3" id="pl_TRU_TYPE3_1_3<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_3);?></select><br>
		 <SELECT NAME="pl_TRU_INST3_2_3" id="pl_TRU_INST3_2_3<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST3_2_3,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE3_2_3" id="pl_TRU_TYPE3_2_3<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_3);?></select>
		 </td>
		 <? if ($STATE_4){ ?>
		 <td>
		 <SELECT NAME="pl_TRU_INST3_1_4" id="pl_TRU_INST3_1_4<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST3_1_4,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE3_1_4" id="pl_TRU_TYPE3_1_4<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_1_4);?></select><br>
		 <SELECT NAME="pl_TRU_INST3_2_4" id="pl_TRU_INST3_2_4<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_TRU_INST3_2_4,0,12,1,'no');?>
		 <SELECT NAME="pl_TRU_TYPE3_2_4" id="pl_TRU_TYPE3_2_4<?=$_POST['band']?>" class="tabledata form-control"><? echo get_select_TRU($pl_TRU_TYPE3_2_4);?></select>
		 </td>
		 <? } 
		}?>
	  </tr>
	  <tr>
		 <td class="tableheader">Antenna Type 1</td>
		 <td><?=$ANTTYPE1_1?></td>
		 <td><?=$ANTTYPE1_2?></td>
		 <td><?=$ANTTYPE1_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$ANTTYPE1_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <input type="text" name='pl_ANTTYPE1_1' value="<?=$pl_ANTTYPE1_1?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_1<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <td><input type="text" name='pl_ANTTYPE1_2' value="<?=$pl_ANTTYPE1_2?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_2<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <td><input type="text" name='pl_ANTTYPE1_3' value="<?=$pl_ANTTYPE1_3?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_3<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name='pl_ANTTYPE1_4' value="<?=$pl_ANTTYPE1_4?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE1_4<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? } 
		} ?>
	  </tr>
	  <tr>
		 <td class="tableheader">Elektrical downtilt 1 <?=$updatable?></td>
		 <td><?=$ELECTILT1_1?></td>
		 <td><?=$ELECTILT1_2?></td>
		 <td><?=$ELECTILT1_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$ELECTILT1_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_ELECTILT1_1" id="pl_ELECTILT1_1<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_ELECTILT1_1,0,15,1,'no');?></td>
		 <td><SELECT NAME="pl_ELECTILT1_2" id="pl_ELECTILT1_2<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_ELECTILT1_2,0,15,1,'no');?></td>
		 <td><SELECT NAME="pl_ELECTILT1_3" id="pl_ELECTILT1_3<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_ELECTILT1_3,0,15,1,'no');?></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_ELECTILT1_4" id="pl_ELECTILT1_4<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_ELECTILT1_4,0,15,1,'no');?></td>
		 <? } 
		}?>
	  </tr>
	  <tr>
		 <td class="tableheader">Mechanical tilt 1</td>
		 <td><?=$MECHTILT1_1?>&nbsp;<?=$MECHTILT_DIR1_1?></td>
		 <td><?=$MECHTILT1_2?>&nbsp;<?=$MECHTILT_DIR1_2?></td>
		 <td><?=$MECHTILT1_3?>&nbsp;<?=$MECHTILT_DIR1_3?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_MECHTILT1_4"><?=$MECHTILT1_4?>&nbsp;<?=$pl_MECHTILT_DIR1_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_MECHTILT1_1" id="pl_MECHTILT1_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_MECHTILT1_1,0,15,1,'no');?>
		 <SELECT NAME='pl_MECHTILT_DIR1_1' id='pl_MECHTILT_DIR1_1<?=$_POST['band']?>' style="width:50%;float:left;" class='tabledata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_1?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
		 <td><SELECT NAME="pl_MECHTILT1_2" id="pl_MECHTILT1_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_MECHTILT1_2,0,15,1,'no');?>
		 <SELECT NAME='pl_MECHTILT_DIR1_2' id='pl_MECHTILT_DIR1_2<?=$_POST['band']?>' style="width:50%;float:left;" class='tabledata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_2?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
		 <td><SELECT NAME="pl_MECHTILT1_3" id="pl_MECHTILT1_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_MECHTILT1_3,0,15,1,'no');?>
		 <SELECT NAME='pl_MECHTILT_DIR1_3' id='pl_MECHTILT_DIR1_3<?=$_POST['band']?>' style="width:50%;float:left;" class='tabledata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_3?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_MECHTILT1_4" id="pl_MECHTILT1_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_MECHTILT1_4,0,15,1,'no');?>
		 <SELECT NAME='pl_MECHTILT_DIR1_4' id='pl_MECHTILT_DIR1_4<?=$_POST['band']?>' style="width:50%;float:left;" class='tabledata form-control'><option SELECTED><?=$pl_MECHTILT_DIR1_4?></option><option value='NA'>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
		 <? } 
		}?>
	  </tr>
	  <tr>
		 <td class="tableheader">Antenna Height 1</td>
		 <td><?=$ANTHEIGHT1_1?>m<?=$ANTHEIGHT1_1_t?></td>
		 <td><?=$ANTHEIGHT1_2?>m<?=$ANTHEIGHT1_2_t?></td>
		 <td><?=$ANTHEIGHT1_3?>m<?=$ANTHEIGHT1_3_t?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$ANTHEIGHT1_4?>m<?=$ANTHEIGHT1_4_t?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <SELECT NAME="pl_ANTHEIGHT1_1" id="pl_ANTHEIGHT1_1<?=$_POST['band']?>"  style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT1_1,-5,200,1,'no');?>
		 <SELECT NAME="pl_ANTHEIGHT1_1_t" id="pl_ANTHEIGHT1_1_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT1_1_t,0,99,1,'yes');?></td>
		 <td><SELECT NAME="pl_ANTHEIGHT1_2" id="pl_ANTHEIGHT1_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT1_2,-5,200,1,'no');?>
		 <SELECT NAME="pl_ANTHEIGHT1_2_t" id="pl_ANTHEIGHT1_2_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT1_2_t,0,99,1,'yes');?></td>
		 <td><SELECT NAME="pl_ANTHEIGHT1_3" id="pl_ANTHEIGHT1_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT1_3,-5,200,1,'no');?>
		 <SELECT NAME="pl_ANTHEIGHT1_3_t" id="pl_ANTHEIGHT1_3_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT1_3_t,0,99,1,'yes');?></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_ANTHEIGHT1_4" id="pl_ANTHEIGHT1_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT1_4,-5,200,1,'no');?>
		 <SELECT NAME="pl_ANTHEIGHT1_4_t" id="pl_ANTHEIGHT1_4_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT1_4_t,0,99,1,'yes');?></td>
		 <? } 
		} ?>
	  </tr>
	  <tr>
		 <td class="tableheader">Azimuth 1</td>
		 <td><?=$AZI1_1?></td>
		 <td><?=$AZI1_2?></td>
		 <td><?=$AZI1_3?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_AZI1_4"><?=$AZI1_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_AZI1_1" id="pl_AZI1_1<?=$_POST['band']?>" class="tabledata form-control"><? get_select_azi($pl_AZI1_1);?></td>
		 <td><SELECT NAME="pl_AZI1_2" id="pl_AZI1_2<?=$_POST['band']?>" class="tabledata form-control"><? get_select_azi($pl_AZI1_2);?></td>
		 <td><SELECT NAME="pl_AZI1_3" id="pl_AZI1_3<?=$_POST['band']?>" class="tabledata form-control"><? get_select_azi($pl_AZI1_3);?></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_AZI1_4" id="pl_AZI1_4<?=$_POST['band']?>" class="tabledata form-control"><? get_select_azi($pl_AZI1_4);?></td>
		 <? } 
		} ?>
	  </tr>
	  <tr>
		 <td class="tableheader">Antenna Type 2</td>
		 <td><?=$ANTTYPE2_1?></td>
		 <td><?=$ANTTYPE2_2?></td>
		 <td><?=$ANTTYPE2_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$ANTTYPE2_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <input type="text" name='pl_ANTTYPE2_1' value="<?=$pl_ANTTYPE2_1?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_1<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <td><input type="text" name='pl_ANTTYPE2_2' value="<?=$pl_ANTTYPE2_2?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_2<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <td><input type="text" name='pl_ANTTYPE2_3' value="<?=$pl_ANTTYPE2_3?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_3<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name='pl_ANTTYPE2_4' value="<?=$pl_ANTTYPE2_4?>" class="dynamic form-control antenna_list<?=$_POST['band']?> antenna_listbig" id="pl_ANTTYPE2_1<?=$_POST['band']?>"  placeholder="Select antenna..." /></td>
		 <? } 
		}?>
	  </tr>
	  <tr>
		 <td class="tableheader">Elektrical downtilt 2 <?=$updatable?></td>
		 <td><?=$ELECTILT2_1?></td>
		 <td><?=$ELECTILT2_2?></td>
		 <td><?=$ELECTILT2_3?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$ELECTILT2_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_ELECTILT2_1" id="pl_ELECTILT2_1<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_ELECTILT2_1,0,15,1,'no');?></td>
		 <td><SELECT NAME="pl_ELECTILT2_2" id="pl_ELECTILT2_2<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_ELECTILT2_2,0,15,1,'no');?></td>
		 <td><SELECT NAME="pl_ELECTILT2_3" id="pl_ELECTILT2_3<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_ELECTILT2_3,0,15,1,'no');?></td>
		 <? if ($STATE_4){ ?>
		<td><SELECT NAME="pl_ELECTILT2_4" id="pl_ELECTILT2_4<?=$_POST['band']?>" class="tabledata form-control"><? get_select_numbers($pl_ELECTILT2_4,0,15,1,'no');?></td>
		 <? } 
		}?>
	  </tr>
	  <tr>
		 <td class="tableheader">Mechanical tilt 2</td>
		 <td><?=$MECHTILT2_1?>&nbsp;<?=$MECHTILT_DIR1_1?></td>
		 <td><?=$MECHTILT2_2?>&nbsp;<?=$MECHTILT_DIR1_2?></td>
		 <td><?=$MECHTILT2_3?>&nbsp;<?=$MECHTILT_DIR1_3?></td>
		 <? if ($STATE_4){ ?>
		  <td><?=$MECHTILT2_4?>&nbsp;<?=$MECHTILT_DIR1_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_MECHTILT2_1" id="pl_MECHTILT2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_MECHTILT2_1,0,15,1,'no');?>
		 <SELECT NAME='pl_MECHTILT_DIR2_1' id="pl_MECHTILT_DIR2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_1?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
		 <td><SELECT NAME="pl_MECHTILT2_2" id="pl_MECHTILT2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_MECHTILT2_2,0,15,1,'no');?>
		 <SELECT NAME='pl_MECHTILT_DIR2_2' id="pl_MECHTILT_DIR2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_2?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
		 <td><SELECT NAME="pl_MECHTILT2_3" id="pl_MECHTILT2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_MECHTILT2_3,0,15,1,'no');?>
		 <SELECT NAME='pl_MECHTILT_DIR2_3' id="pl_MECHTILT_DIR2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_3?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_MECHTILT2_4" id="pl_MECHTILT2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_MECHTILT2_4,0,15,1,'no');?>
		 <SELECT NAME='pl_MECHTILT_DIR2_4' id="pl_MECHTILT_DIR2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><option SELECTED><?=$pl_MECHTILT_DIR2_4?></option><option value=''>NA</option><option>DOWNTILT</option><option>UPTILT</option></SELECT></td>
		 <? } 
		}?>
	  </tr>
	  <tr>
		 <td class="tableheader">Antenna Height 2</td>
		 <td><?=$ANTHEIGHT2_1?>m<?=$ANTHEIGHT2_1_t?></td>
		 <td><?=$ANTHEIGHT2_2?>m<?=$ANTHEIGHT2_2_t?></td>
		 <td><?=$ANTHEIGHT2_3?>m<?=$ANTHEIGHT2_3_t?></td>
		 <? if ($STATE_4){ ?>
		 <td><?=$ANTHEIGHT2_4?>m<?=$ANTHEIGHT2_4_t?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_ANTHEIGHT2_1" id="pl_ANTHEIGHT2_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT2_1,-5,200,1,'no');?>
		 <SELECT NAME="pl_ANTHEIGHT2_1_t" id="pl_ANTHEIGHT2_1_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT2_1_t,0,99,1,'yes');?></td>
		 <td><SELECT NAME="pl_ANTHEIGHT2_2" id="pl_ANTHEIGHT2_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT2_2,-5,200,1,'no');?>
		 <SELECT NAME="pl_ANTHEIGHT2_2_t" id="pl_ANTHEIGHT2_2_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT2_2_t,0,99,1,'yes');?></td>
		 <td><SELECT NAME="pl_ANTHEIGHT2_3" id="pl_ANTHEIGHT2_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT2_3,-5,200,1,'no');?>
		 <SELECT NAME="pl_ANTHEIGHT2_3_t" id="pl_ANTHEIGHT2_3_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT2_3_t,0,99,1,'yes');?></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_ANTHEIGHT2_4" id="pl_ANTHEIGHT2_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT2_4,-5,200,1,'no');?>
		 <SELECT NAME="pl_ANTHEIGHT2_4_t" id="pl_ANTHEIGHT2_4_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_ANTHEIGHT2_4_t,0,99,1,'yes');?></td>
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
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_AZI2_1" id="pl_AZI2_1<?=$_POST['band']?>" class="tabledata form-control"><? get_select_azi($pl_AZI2_1);?></td>
		 <td><SELECT NAME="pl_AZI2_2" id="pl_AZI2_2<?=$_POST['band']?>" class="tabledata form-control"><? get_select_azi($pl_AZI2_2);?></td>
		 <td><SELECT NAME="pl_AZI2_3" id="pl_AZI2_3<?=$_POST['band']?>" class="tabledata form-control"><? get_select_azi($pl_AZI2_3);?></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_AZI2_4" id="pl_AZI2_4<?=$_POST['band']?>" class="tabledata form-control"><? get_select_azi($pl_AZI2_4);?></td>
		 <? } 
		} ?>
	  </tr>
	  <tr>
		 <td class="tableheader">Feeder type <?=$updatable?></td>
		 <td><?=$FEEDER_1?></td>
		 <td><?=$FEEDER_2?></td>
		 <td><?=$FEEDER_3?></td>
		 <? if ($STATE_4){ ?>
		 <td id="cur_FEEDER_4"><?=$FEEDER_4?></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft">
		 <input type="text" name='pl_FEEDER_1' value="<?=$pl_FEEDER_1?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_1<?=$_POST['band']?>" placeholder="Select feeder..." /></td>
		 <td><input type="text" name='pl_FEEDER_2' value="<?=$pl_FEEDER_2?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_2<?=$_POST['band']?>" placeholder="Select feeder..." /></td>
		 <td><input type="text" name='pl_FEEDER_3' value="<?=$pl_FEEDER_3?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_3<?=$_POST['band']?>" placeholder="Select feeder..." /></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name='pl_FEEDER_4' value="<?=$pl_FEEDER_4?>" class="dynamic form-control feeder_list<?=$_POST['band']?>" id="pl_FEEDER_4<?=$_POST['band']?>" placeholder="Select feeder..." /></td>
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
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_FEEDERLEN_1" id="pl_FEEDERLEN_1<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_FEEDERLEN_1,0,200,1,'no');?>
		 <SELECT NAME="pl_FEEDERLEN_1_t" id="pl_FEEDERLEN_1_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_FEEDERLEN_1_t,0,99,5,'yes');?></td>
		 <td><SELECT NAME="pl_FEEDERLEN_2" id="pl_FEEDERLEN_2<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_FEEDERLEN_2,0,200,1,'no');?>
		 <SELECT NAME="pl_FEEDERLEN_2_t" id="pl_FEEDERLEN_2_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_FEEDERLEN_2_t,0,99,5,'yes');?></td>
		 <td><SELECT NAME="pl_FEEDERLEN_3" id="pl_FEEDERLEN_3<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_FEEDERLEN_3,0,200,1,'no');?>
		 <SELECT NAME="pl_FEEDERLEN_3_t" id="pl_FEEDERLEN_3_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_FEEDERLEN_3_t,0,99,5,'yes');?></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_FEEDERLEN_4" id="pl_FEEDERLEN_4<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_FEEDERLEN_4,0,200,1,'no');?>
		 <SELECT NAME="pl_FEEDERLEN_4_t" id="pl_FEEDERLEN_4_t<?=$_POST['band']?>" style="width:50%;float:left;" class="tabledata form-control"><? get_select_numbers($pl_FEEDERLEN_4_t,0,99,5,'yes');?></td>
		 <? } 
		} ?>
	</tr>
	<tr>
		 <td class="tableheader"><font color="blue">DC block</td>
		 <td><SELECT NAME="DCBLOCK_1" id="DCBLOCK_1<?=$_POST['band']?>" class="tabledata form-control"><?=get_select_YESNO($DCBLOCK_1);?></select></td>
		 <td><SELECT NAME="DCBLOCK_2" id="DCBLOCK_2<?=$_POST['band']?>" class="tabledata form-control"><?=get_select_YESNO($DCBLOCK_2);?></select></td>
		 <td><SELECT NAME="DCBLOCK_3" id="DCBLOCK_3<?=$_POST['band']?>" class="tabledata form-control"><?=get_select_YESNO($DCBLOCK_3);?></select></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="DCBLOCK_3" id="DCBLOCK_4<?=$_POST['band']?>" class="tabledata form-control"><?=get_select_YESNO($DCBLOCK_4);?></select></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><SELECT NAME="pl_DCBLOCK_1" id="pl_DCBLOCK_1<?=$_POST['band']?>" class="tabledata form-control"><?=get_select_YESNO($pl_DCBLOCK_1);?></select></td>
		 <td><SELECT NAME="pl_DCBLOCK_2" id="pl_DCBLOCK_2<?=$_POST['band']?>" class="tabledata form-control"><?=get_select_YESNO($pl_DCBLOCK_2);?></select></td>
		 <td><SELECT NAME="pl_DCBLOCK_3" id="pl_DCBLOCK_3<?=$_POST['band']?>" class="tabledata form-control"><?=get_select_YESNO($pl_DCBLOCK_3);?></select></td>
		 <? if ($STATE_4){ ?>
		 <td><SELECT NAME="pl_DCBLOCK_4" id="pl_DCBLOCK_4<?=$_POST['band']?>" class="tabledata form-control"><?=get_select_YESNO($pl_DCBLOCK_4);?></select></td>
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
		 if ($viewtype!="BUILD"){ ?>
		 <td class="borderleft"><input type="text" name="pl_HR_ACTIVE_1" id="pl_HR_ACTIVE_1<?=$_POST['band']?>" class="tabledata form-control input-medium" value="<?=$pl_HRACTIVE_1?>"></td>
		 <td><input type="text" name="pl_HR_ACTIVE_2" id="pl_HR_ACTIVE_2<?=$_POST['band']?>" class="tabledata form-control input-medium" value="<?=$pl_HRACTIVE_2?>"></td>
		 <td><input type="text" name="pl_HR_ACTIVE_3" id="pl_HR_ACTIVE_3<?=$_POST['band']?>" class="tabledata form-control input-medium" value="<?=$pl_HRACTIVE_3?>"></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name="pl_HR_ACTIVE_4" id="pl_HR_ACTIVE_4<?=$_POST['band']?>" class="tabledata form-control input-medium" value="<?=$pl_HRACTIVE_4?>"></td>
		 <? } 
		} ?>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td colspan="<?=$colspan-1?>" class="tableheader" style="text-align:center;border-top:1px solid black;">Settings the same for all technologies</td>
	</tr>
	 <tr>
    	 <td class="tableheader"><font color="blue">Feeder sharing <?=$updatable?></td>
		 <td><input type="text" name="FEEDERSHARE_1" id="FEEDERSHARE_1<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_1?>"></td>
		 <td><input type="text" name="FEEDERSHARE_2" id="FEEDERSHARE_2<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_2?>"></td>
		 <td><input type="text" name="FEEDERSHARE_3" id="FEEDERSHARE_3<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_3?>"></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name="FEEDERSHARE_4" id="FEEDERSHARE_4<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$FEEDERSHARE_4?>"></td>
		 <? } 
		 if ($viewtype!="BUILD"){ ?>
		 <td><input type="text" name="pl_FEEDERSHARE_1" id="pl_FEEDERSHARE_1<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_1?>"></td>
		 <td><input type="text" name="pl_FEEDERSHARE_2" id="pl_FEEDERSHARE_2<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_2?>"></td>
		 <td><input type="text" name="pl_FEEDERSHARE_3" id="pl_FEEDERSHARE_3<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_3?>"></td>
		 <? if ($STATE_4){ ?>
		 <td><input type="text" name="pl_FEEDERSHARE_4" id="pl_FEEDERSHARE_4<?=$_POST['band']?>" class="form-control feedershare_list<?=$_POST['band']?>" value="<?=$pl_FEEDERSHARE_4?>"></td>
		 <? } 
		} ?>
	 </tr>
</tbody>
</table>
<br><br><br>
</div>


<?php
if (substr_count($guard_groups, 'Radioplanners')=="1"&& $_POST['status']!="FUND" && $_POST['print']!="yes"){ ?>
	<p align="center">
	<font color="blue">BSDS comments <?=$_POST['band']?></font><br>
	<textarea name="pl_COMMENTS" cols="100" rows="5"><?=$pl_COMMENTS?></textarea>
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

	if ((substr_count($guard_groups, 'Radioplanners')=="1" && $viewtype=="PRE" && $_POST['print']!="yes" && $_POST['print']!="yes")
	or ($pl_is_BSDS_accepted=="Accepted" && $viewtype=="FUND"  && substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['print']!="yes")
	or  ($viewtype=="POST"  && substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['print']!="yes")){ //CURRENT IS ALWAYS UPDATEBLE ** Only group 'Radioplanners' can update BSDSs
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



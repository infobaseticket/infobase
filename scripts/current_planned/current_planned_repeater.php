<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['preoverride']=="yes"){
	$bsdsdata="PRE READY TO BUILD";
}else{
	$bsdsdata=$_POST['bsdsdata'];
}
//echo "<pre>".print_r($bsdsdata,true)."</pre>";
if ($_POST['bsdsbobrefresh']=="PRE"){
	$bsdsdata="PRE READY TO BUILD";
	$_POST['bsdsbobrefresh']="";
}else{
	$bsdsdata=$_POST['bsdsdata'];
}
if ($_POST['status']=="FUNDHIST"){ //HISTORY VIEW
	$viewtype="FUND";
	$color="SITE_funded";
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
if ($_POST['band']=="U21"){
	$lognode=$_POST['lognodeID_UMTS2100'];
	$tabletype="UMTS";
}elseif ($_POST['band']=="U9"){
	$lognode=$_POST['lognodeID_UMTS900'];
	$tabletype="UMTS";
}elseif ($_POST['band']=="L18"){
	$lognode=$_POST['lognodeID_LTE1800'];
	$tabletype="LTE";
		$tabletype="UMTS";
}elseif ($_POST['band']=="L8"){
	$lognode=$_POST['lognodeID_LTE800'];
	$tabletype="LTE";
}elseif ($_POST['band']=="L26"){
	$lognode=$_POST['lognodeID_LTE2600'];
	$tabletype="LTE";
}elseif ($_POST['band']=="G18" or $_POST['band']=="G9"){
	$lognode=$_POST['lognodeID_GSM'];
	$tabletype="GSM";
}

$check_current_exists=check_current_exists($_POST['band'],$_POST['bsdskey'],$_POST['bsdsbobrefresh'],'',$_POST['donor'],$lognode,$viewtype);

if ($check_current_exists!=0 || $viewtype=="FUND"){
	$check_planned_exists=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$_POST['band'],'allsec',$viewtype,$_POST['donor']);
	if ($check_planned_exists=="error"){
		echo "error";
		?>
		<script language="JavaScript">
			$('.top-right').notify({
				message: { html: '<h1>Sytem error</h1>There are too many records in the database for <?=$_POST['band']?> BSDS! Please contact Frederick Eyland'},
				type: 'info'
			}).show();
		</script>
		<?
	}
}else{
	$check_planned_exists=0;
}

if($check_planned_exists==0 && $status=="BSDS FUNDED"){
	?>
	<script language="JavaScript">
	$('.top-right').notify({
				message: { html: '<h3>No planned data available</h3>Please defund BSDS by removing U305 in NET1 and save data for <?=$_POST['band']?>.<br>Then you will be able to refund. (with a newer date)'''},
				type: 'info'
			}).show();
	</script>
	<?
}

$gen_info=get_BSDS_generalinfo($_POST['bsdskey']);
$pl_is_BSDS_accepted=$gen_info['TEAML_APPROVED'][0];
$pl_CHANGEDATE=$gen_info['UPDATE_AFTER_COPY'][0];

if ($_POST['action']!="save"){
	if($check_planned_exists!="0"){
		include("planned_repeater_data.php");
	}
}

if ($pl_is_BSDS_accepted=="Accepted" && ($bsdsdata[$_POST['band'].'STATUS']=="BSDS FUNDED")){
	$updatable="<font color=red><b>*</b></font>";
}else{
	$updatable="";
}

include("current_repeater_data.php");

if ($check_planned_exists=="0" && $_POST['action']!="save"){
// If there is NO planned data, we first need to get current data out of the datbase
// because we need the state of secoters (we may not make PLANNED fields empty, but need to copy current stuff to planned)
	include("planned_repeater_data.php");
}

/*******************************************************************************************************************/
/***********************************     OUTPUT DATA TO SCREEN  ****************************************************/
/*******************************************************************************************************************/
?>
<form action="scripts/current_planned/save_pl_cu_repeater.php" method="post" id="current_planned_form<?=$_POST['band']?><?=$viewtype?>" role="form">
<input type="hidden" name="band" value="<?=$_POST['band']?>">
<input type="hidden" name="pl_band" value="<?=$_POST['band']?>">
<input type="hidden" name="tabid" value="<?=$_POST['tabid']?>">
<input type="hidden" name="action" value="save">
<input type="hidden" name="lognode" value="<?=$lognode?>">
<input type="hidden" name="bsdskey" value="<?=$_POST['bsdskey']?>">
<input type="hidden" name="bsdsbobrefresh" value="<?=$_POST['bsdsbobrefresh']?>">
<input type="hidden" name="viewtype" value="<?=$viewtype?>">
<input type="hidden" name="donor" value="<?=$_POST['donor']?>">
<input type="hidden" name="action" value="save">

<table class="table table-bordered table-condensed table-responsive-force" id="bsds<?=$_POST['candidate']?><?=$_POST['band']?>">
<?
if ($viewhistory=="yes"){?>
<caption>!!! Your are viewing an old BSDS with status <?=$status?> !!!!</caption><?
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
	<td class="bsdsinfoband">
		<?=$_POST['band']?> [<?=$_POST['bsdskey']?>]<br><?=$_POST['bsdsbobrefresh']?>
	</td>
	<td class="table_head"><b>
		<? if ($check_current_exists=="0"){ ?>
			<font color=red>NOT SAVED</font>
		<? } ?>
			CURRENT <?=$_POST['band']?>
			<?
			   if ($bsdsdata[$_POST['band'].'STATUS']!="BSDS FUNDED"){ ?>
				<font color="orange">LIVE SITUATION</font> from OSS/ASSET
			<? }else{?>
				SITUATION FROM <?=$CHANGEDATE;?>
			<? } ?>
			</b>
	
		<?
		if ($check_planned_exists=="0"){
			$pl.="<font color=red>NOT SAVED</font> ";
			$general_override="NOT_SAVED";
		}
		$pl.="PLANNED ".$type." SITUATION ";
		if ($pl_CHANGEDATE && $check_planned_exists==1){
			$pl.= "(Last update: $pl_CHANGEDATE)";
		}
	?>
	</td>
	<td class="table_head borderleft">
		<b><?=$pl?></b>
		<?php if ($viewhistory=="no" && $_POST['print']!="yes"){ ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs clear" name="clear">Clear</button>
		<? } ?>
	</td>
</tr>
<tr>
	<? $color_OWNER=check_if_same($OWNER,$pl_OWNER,'','','','','','',$OWNER_override,$general_override); ?>
	<td class="parameter_name"><b>OWNER</b></td>
	<td class="<?=$color_OWNER[1][1]?>">
	    <select NAME="OWNER" class="form-control <?=$color_OWNER[1][2]?>"><option selected><?=$OWNER?></option>
			<option>Base</option><option>Mobistar</option><option>Proximus</option><option>NA</option>
	    </select>
	</td>
	<td class="borderleft <?=$color_OWNER[4][1]?>">
	    <select NAME="pl_OWNER" class="form-control tabledata <?=$color_OWNER[4][2]?>"><option selected><?=$pl_OWNER?></option>
			<option>Base</option><option>Mobistar</option><option>Proximus</option>
	    </select>
	</td>
</tr>
<tr>
	<? $color_BRAND=check_if_same($BRAND,$pl_BRAND,'','','','','','',$BRAND_override,$general_override); ?>
	<td class="parameter_name"><b>BRAND</b></td>
	<td class="<?=$color_BRAND[1][1]?>">
	    <select NAME="BRAND" class="form-control <?=$color_BRAND[1][2]?>"><option selected><?=$BRAND?></option>
			<option>Andrew/Milkon</option><option>Avitec</option><option>Other</option><option>NA</option>
	    </select>
	</td>
	<td class="borderleft <?=$color_BRAND[4][1]?>">
	    <select NAME="pl_BRAND" class="form-control tabledata <?=$color_BRAND[4][2]?>"><option selected><?=$pl_BRAND?></option>
			<option>Andrew/Milkon</option><option>Avitec</option><option>Other</option>
	    </select>
	</td>
</tr>
<? $color_RTYPE=check_if_same($RTYPE,$pl_RTYPE,'','','','','','',$RTYPE_override,$general_override); ?>
<tr id="pl_DXU1">
	<td class="parameter_name"><b>REPEATER TYPE</b></td>
	<td class="<?=$color_RTYPE[1][1]?>"><input type="text" NAME="RTYPE" class="form-control <?=$color_RTYPE[1][3]?> repeatertype" value="<?=$RTYPE?>"></td>
	<td class="borderleft <?=$color_RTYPE[4][1]?>"><input type="text" NAME="pl_RTYPE" class="form-control tabledata <?=$color_RTYPE[4][3]?> repeatertype" value="<?=$pl_RTYPE?>"></td>
</tr>
<? $color_TECHNOLOGY=check_if_same($TECHNOLOGY,$pl_TECHNOLOGY,'','','','','','',$TECHNOLOGY_override,$general_override); ?>
<tr id="pl_DXU2">
	<td class="parameter_name"><b>TECHNOLOGY</b></a></td>
	<td class="<?=$color_TECHNOLOGY[1][1]?>">
		<select NAME="TECHNOLOGY" class="form-control tabledata <?=$color_TECHNOLOGY[1][2]?>"><option selected><?=$TECHNOLOGY?></option><option>RF</option><option>Optical</option></select>
	</td>
	<td class="borderleft <?=$color_TECHNOLOGY[4][1]?>">
		<select NAME="pl_TECHNOLOGY" class="form-control tabledata <?=$color_TECHNOLOGY[4][2]?>"><option selected><?=$pl_TECHNOLOGY?></option><option>RF</option><option>Optical</option></select>
	</td>

</tr>
<? $color_CHANNEL=check_if_same($CHANNEL,$pl_CHANNEL,'','','','','','',$CHANNEL_override,$general_override); ?>
<tr>
	<td class="parameter_name"><b>CHANNELIZED</b></td>
	<td class="<?=$color_CHANNEL[1][1]?>">
	    <select NAME="CHANNEL" class="form-control <?=$color_CHANNEL[1][2]?>"><option selected><?=$CHANNEL?></option>
			<option>No</option><option>1</option><option>2</option><option>3</option><option>4</option>
			<option>5</option><option>6</option><option>7</option><option>8</option>
	    </select>
	</td>
	<td class="borderleft <?=$color_CHANNEL[4][1]?>">
	    <select NAME="pl_CHANNEL" class="form-control tabledata <?=$color_CHANNEL[4][2]?>"><option selected><?=$pl_CHANNEL?></option>
			<option>No</option><option>1</option><option>2</option><option>3</option><option>4</option>
			<option>5</option><option>6</option><option>7</option><option>8</option>
	    </select>
	</td>
</tr>
<? $color_PICKUP=check_if_same($PICKUP,$pl_PICKUP,'','','','','','',$PICKUP_override,$general_override); ?>
<tr>
	<td class="parameter_name">PICK-UP ANTENNA</td>
	<td class="<?=$color_PICKUP[1][1]?>"><input type="text" NAME="PICKUP" class="form-control <?=$color_PICKUP[1][2]?> pickupantenna" value="<?=$PICKUP?>"></td>
	<td class="borderleft <?=$color_PICKUP[4][1]?>"><input type="text" NAME="pl_PICKUP" class="form-control tabledata <?=$color_PICKUP[4][2]?> pickupantenna" value="<?=$pl_PICKUP?>"></td>		
</tr>
<? $color_DISTRIB=check_if_same($DISTRIB,$pl_DISTRIB,'','','','','','',$DISTRIB_override,$general_override); ?>
<tr>
	<td class="parameter_name">DISTRIBUTION ANTENNA</td>
	<td class="<?=$color_DISTRIB[1][4]?>"><textarea name="DISTRIB" cols="50" rows="5" class="form-control"><?=$DISTRIB?></textarea></td>
	<td class="borderleft <?=$color_DISTRIB[4][4]?>"><textarea name="pl_DISTRIB" cols="50" rows="5" class="form-control tabledata"><?=$pl_DISTRIB?></textarea></td>		
</tr>
<? $color_COSP=check_if_same($COSP,$pl_COSP,'','','','','','',$COSP_override,$general_override); ?>
<tr>
	<td class="parameter_name">COUPLERS AND SPLITTERS</td>
	<td class="<?=$color_COSP[1][4]?>"><textarea name="COSP" cols="50" rows="5" class="form-control"><?=$COSP?></textarea></td>
	<td class="borderleft <?=$color_COSP[4][4]?>"><textarea name="pl_COSP" cols="50" rows="5" class="form-control tabledata"><?=$pl_COSP?></textarea></td>
</tr>
<? $color_DISTRIB=check_if_same($COMMENTS,$pl_COMMENTS,'','','','','','',$COMMENTS_override,$general_override); ?>
<tr>
	<td class="parameter_name">COMMENTS</td>
	<td class="<?=$color_COMMENTS[1][4]?>"><textarea name="COMMENTS" cols="50" rows="5" class="form-control"><?=$COMMENTS?></textarea></td>
	<td class="borderleft <?=$color_COMMENTS[4][4]?>"><textarea name="pl_COMMENTS" cols="50" rows="5" class="form-control tabledata"><?=$pl_COMMENTS?></textarea></td>
</tr>
</tbody>
</table>

<?
if ($pl_is_BSDS_accepted=="Accepted" && $viewtype=="FUND" &&  $viewhistory=="no"){ //&& substr_count($guard_groups, 'Administrator')!=1
	$value_save="Save current + Feeder + Tilt data \n(Fields with a * can be updated)";
}else{
	$value_save="Save current & planned info!";
}

if ($pl_is_BSDS_accepted=="Accepted" && $viewtype=="FUND" && substr_count($guard_groups, 'Radioplanners')=="1"){
	?><p align='center'><input type="hidden" name="onlyfeeder" value="yes"></p><?
}

if ((substr_count($guard_groups, 'Radioplanners')=="1" && $viewtype=="PRE" && $_POST['print']!="yes")
	or ($pl_is_BSDS_accepted=="Accepted" && $viewtype=="FUND"  && substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['print']!="yes")
	or ($viewtype=="POST"  && substr_count($guard_groups, 'Radioplanners')=="1" && $_POST['print']!="yes")
	){ //CURRENT IS ALWAYS UPDATEBLE ** Only group 'Radioplanners' can update BSDSs
 ?>
 <p align='center'><input type="submit" class="btn btn-primary subCurPl" value="<?=$value_save?>" id="save_bsdsdata" data-techno="<?=$_POST['band']?>" data-viewtype="<?=$viewtype?>"></p>
<?
}
OCILogoff($conn_Infobase);
?>
</form>


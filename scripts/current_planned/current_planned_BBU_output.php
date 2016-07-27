<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$key_zband=$_POST['datakey'];
if ($_POST['print']=="yes"){
?>
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/bsds/scripts/current_planned/currentplanned_print.css" type="text/css" media="screen,print" />
<script src="<?=$config['explorer_url']?>javascripts/jquery.js"></script>

<?php } 

if ($_POST['frozen']==0){
	//4G OSS DATA
	$query =  "select DISTINCT(TECHNO) from SWITCH_3GZTE_UUTRANCELLFDD WHERE USERLABEL LIKE '%".$_POST['siteid']."%'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);

		for ($i=0;$i< count($res1['TECHNO']);$i++) {
			$technos3G.=$res1['TECHNO'][$i].",";
		}
	}

	$query =  "select BBU_SLOTNO,BBU_CARD,SLOT,DESCRIPTION from SWITCH_4GZTE_SDRDEVICEGROUP WHERE MEID = '".ltrim(substr($_POST['siteid'],2), '0')."' AND BBU_CARD IS NOT NULL ORDER BY BBU_SLOTNO ASC";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $resSDR);
		if(!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$j=0;
		for ($i=0;$i< count($resSDR['BBU_SLOTNO']);$i++) {
			$slot=$resSDR['BBU_SLOTNO'][$i];
			$data4G[$slot]=$resSDR['BBU_CARD'][$i];
			$hidden4G.="<input type='hidden' name='cur_C2_SLOT".$slot."' id='cur_C2_SLOT".$slot."' value='".$resSDR['BBU_CARD'][$i]."'>";
		}
	}
	//3G OSS DATA
	$query =  "select DISTINCT(TECHNO) from SWITCH_4GZTE_UTRANCELL WHERE ULABEL LIKE '%".$_POST['siteid']."%'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);

		for ($i=0;$i<count($res1['TECHNO']);$i++) {
			$technos4G.=$res1['TECHNO'][$i].",";
		}
	}
	$query =  "select SLOTNO, USERLABEL,BBU_SLOTNO,BBU_CARD  from SWITCH_3GZTE_PLUGINUNIT WHERE RACKNO = '".ltrim(substr($_POST['siteid'],2), '0')."'  AND BBU_CARD IS NOT NULL ORDER BY SLOTNO ASC";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if(!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$j=0;
		for ($i=0;$i< count($res1['SLOTNO']);$i++) {
			$slot=$res1['BBU_SLOTNO'][$i];
			$data3G[$slot]=$res1['BBU_CARD'][$i];
			$hidden3G.="<input type='hidden' name='cur_C1_SLOT".$slot."' id='cur_C1_SLOT".$slot."' value='".$res1['BBU_CARD'][$i]."'>";
		}
	}

	$C1_SLOT1=$data3G[1];
	$C1_SLOT2=$data3G[2];
	$C1_SLOT3=$data3G[3];
	$C1_SLOT4=$data3G[3];
	$C1_SLOT5=$data3G[5];
	$C1_SLOT6=$data3G[6];
	$C1_SLOT7=$data3G[7];
	$C1_SLOT8=$data3G[8];
	$C1_SLOT13=$data3G[13];
	$C1_SLOT14=$data3G[14];
	$C1_SLOT15=$data3G[15];
	$C1_TECHNOS=$technos3G;
	$C2_SLOT1=$data4G[1];
	$C2_SLOT2=$data4G[2];
	$C2_SLOT3=$data4G[3];
	$C2_SLOT4=$data4G[3];
	$C2_SLOT5=$data4G[5];
	$C2_SLOT6=$data4G[6];
	$C2_SLOT7=$data4G[7];
	$C2_SLOT8=$data4G[8];
	$C2_SLOT13=$data4G[13];
	$C2_SLOT14=$data4G[14];
	$C2_SLOT15=$data4G[15];
	$C2_TECHNOS=$technos4G;

}else{
	$cols_pl_BBU=get_cols("BSDS_CU_BBU");

	$query =  "select * from BSDS_CU_BBU WHERE BSDSKEY = '".$_POST['bsdskey']."'  AND BSDS_BOB_REFRESH='".$_POST['createddate']."' AND STATUS='".$_POST['frozen']."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if(!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$j=0;
		if (count($res1['BSDSKEY'])==1){
			$BBU_check='CHECKED';
			foreach ($res1 as $key => $planned) {
				foreach ($planned as $keyid => $value) {
					$parname=$key;
					$$parname=$value;
				}
			}
		}
	}
}
$cols_pl_BBU=get_cols("BSDS_PL_BBU");

$query =  "select * from BSDS_PL_BBU WHERE BSDSKEY = '".$_POST['bsdskey']."'  AND BSDS_BOB_REFRESH='".$_POST['createddate']."' AND STATUS='".$_POST['frozen']."'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if(!$stmt){
	die_silently($conn_Infobase, $error_str);
	exit;
}else{
	OCIFreeStatement($stmt);
	$j=0;
	if (count($res1['BSDSKEY'])==1){
		$BBU_check='CHECKED';
		foreach ($res1 as $key => $planned) {
			foreach ($planned as $keyid => $value) {
				$parname="pl_".$key;
				$$parname=$value;
			}
		}
	}
}

if ($_POST['rafid']!='#'){
	$query = "select CONFIG FROM BSDS_RAF_RADIO WHERE RAFID = '".$_POST['rafid']."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if(!$stmt){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmt);
		$CONFIG=$res1['CONFIG'][0];
	}
}

$technos1=explode(",", $pl_C1_TECHNOS);
foreach ($technos1 as $key => $techno) {
	$var="pl_C1_".$techno;
	$$var="CHECKED";
}
$technos2=explode(",", $pl_C2_TECHNOS);
foreach ($technos2 as $key => $techno) {
	$var="pl_C2_".$techno;
	$$var="CHECKED";
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

if ($_POST['uniran']==1){
	$uniran='CHECKED';
}

?>

<div id='printArea<?=$key_zband?>'>

	<div style="page-break-after:always;">
		<?php if ($_POST['print']=="yes"){ ?>
		<br><br>
		<center>
			<img src="<?=$config['explorer_url']?>images/logoInfobase.png">

			<h1>Base Station Datasheet</h1>
			<h1><?=$_POST['candidate']?></h1>
			<h1><?=$_POST['upgnr']?></h1>
		<br><br><br>
		<?php } 

		if ($_POST['print']!="yes"){
		?>
		<form action="scripts/current_planned/save_pl_cu_BBU.php" method="post" id="current_planned_BBUform<?=$key_zband?>" role="form">
		<input type="hidden" name="candidate" value="<?=$_POST['candidate']?>">
		<input type="hidden" name="bsdskey" value="<?=$_POST['bsdskey']?>">
		<input type="hidden" name="createddate" value="<?=$_POST['createddate']?>">
		<input type="hidden" name="frozen" value="<?=$_POST['frozen']?>">
		<input type="hidden" name="cur_C1_TECHNOS" value="<?=substr($technos3G,0,-1)?>">
		<input type="hidden" name="cur_C2_TECHNOS" value="<?=substr($technos4G,0,-1)?>">

		<?=$hidden3G?>
		<?=$hidden4G?>
		<?php 
		} ?>
		<div class="well well-sm">
		
			<div class="checkbox">
				<label>
				  <input type="checkbox" name='uniran' <?=$uniran?> id='uniran<?=$key_zband?>' value='1'> <font color="blue">Check if UNIRAN BSDS</font><br>
				  
				</label>
			</div>
			<span class="unirandata<?=$key_zband?>">
				CABINET TYPE: <br>
				<input type="text"  style="width:200px;" value="<?=$_POST['cabtype']?>" name="cabtype" id="CABTYPE<?=$key_zband?>" placeholder="Select cabinet..." tabindex="-1" class="dynamic cabtype_list form-control"><br>
				RECTIFIER:<br>
				<select NAME="rectifier" style="width:200px;" class="tabledata cleardata form-control"><? get_select_numbers($_POST['rectifier'],1,4,1,'no');?>
				POWER SUPPLY:<br>
				<select NAME="powersup" style="width:200px;" class="form-control"><option selected><?=$_POST['powersup']?><option>ACTURA CS</option><option>B900</option><option>DC/DC</option><option>PC8910A</option><option>B121</option><option>B201</option><option>NONE</option></select></td>
					
			</span>

				
			
			<?php if ($_POST['print']!="yes"){ ?>
			<span class="pull-right">
			<a class="btn btn-success bsdsdetails2" data-id="<?=$key_zband?>"  data-techno="PRINTBSDS" data-bsdskey="<?=$_POST['bsdskey']?>" role="button"> <span class="glyphicon glyphicon-print"></span> PRINT </a>
	       	</span>
	       	<?php } ?>

		</div>

		<?php if ($_POST['print']=="yes"){ ?>

			<br><br>
			<div class="row well well-small" style="border:3px solid #428bca;padding:10px;">
		 	 	<div class="col-md-12">
					<table border='0' class="table table-condensed">
					<tbody>
					<tr>
						<td>Site Identity:</td>
						<td><span class="label label-info" style="font-size:16px;"><?=$_POST['siteid']?></span></td>					
					</tr>
					<tr>
						<td>Candidate (Firstname Asset):</td>
						<td><span class="label label-default" style="font-size:16px;"><?=$_POST['candidate']?></span></td>
					</tr>
					<?php if($_POST['upgnr']!=''){ ?>
					<tr>
						<td>N1 UPGNR:</td>
						<td><span class="label label-default" style="font-size:16px;"><?=$_POST['upgnr']?></span></td>
					</tr>
					<?php }
					if ($_POST['donor']){ ?>
					<tr>
						<td>Donor site:</td>
						<td><font color="red"><?=$_POST['donor']?></font></td>
					</tr>
					<?php 
					} ?>
					<tr>
						<td>RAFID:</td>
						<td><span class="label label-default" style="font-size:16px;"><?=$_POST['rafid']?>: <?=$_POST['raftype']?></span></td>
					</tr>
					<tr>
						<td>Techno's defined in Asset:</td>
						<td><span class="label label-warning" style="font-size:14px;"><?=substr($_POST['technos'],0,-1)?></span></td>
						
					</tr>
					<tr>
						<td>Techno's funded for Construction:</td>
						<td><span class="label label-warning" style="font-size:14px;"><?=$_POST['technosCon']?></span></td>
						
					</tr>
					<tr>
						<td valign="top">Address:</td>
						<td><?=$_POST['address']?></td>
					</tr>
					<tr>
						<td>X - Y coordinates:</td>
						<td><?=$_POST['xycoord']?></td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</center>
	</div>
	<?php } ?>

	
	<div class="unirandata<?=$key_zband?>" style="width:100%;page-break-after:always;">
		<table class="table" style="width:100%;">
		<tr>
			<th width="50%" class="tableheader <?=$color?>">
			<?php if ($_POST['frozen']==0){ ?>
			CURRENT LIVE BBU LAYOUT FROM NETNUMEN
			<?php }else{ ?>
			SAVED BBU LAYOUT @ FREEZING
			<?php } ?>
			</th>
			<th width="50%" class="tableheader borderleft <?=$color?>">PLANNED BBU LAYOUT</th>
		</tr>
		<tr>
			<td>
				<h4>3G:</h4>
				<table class="table" style="height:200px;">
				<tbody>
				<tr>
					<td colspan="3"><?=$C1_TECHNOS?></td>
				</tr>
				<tr>
					<td>
						<table class="table table-bordered" style="height:200px;">
						<tr>
							<td><?=$C1_SLOT15?></td>
							<td class="parameter_name" style="width:30px;">15</td>
						</tr>
						<tr>
							<td><?=$C1_SLOT14?></td>
							<td class="parameter_name">14</td>
						</tr>
						<tr>
							<td><?=$C1_SLOT13?></td>
							<td class="parameter_name">13</td>
						</tr>
						</table>
					</td>
					<td>
						<table class="table table-bordered" style="height:200px;">
						<tr>
							<td><?=$C1_SLOT4?></td>
							<td class="parameter_name" style="width:30px;">4</td>
							<td><?=$C1_SLOT8?></td>
							<td class="parameter_name" style="width:30px;">8</td>
						</tr>
						<tr>
							<td><?=$C1_SLOT3?></td>
							<td class="parameter_name">3</td>
							<td><?=$C1_SLOT7?></td>
							<td class="parameter_name">7</td>
						</tr>
						<tr>
							<td><?=$C1_SLOT2?></td>
							<td class="parameter_name">2</td>
							<td><?=$C1_SLOT6?></td>
							<td class="parameter_name">6</td>
						</tr>
						<tr>
							<td><?=$C1_SLOT1?></td>
							<td class="parameter_name">1</td>
							<td><?=$C1_SLOT5?></td>
							<td class="parameter_name">5</td>
						</tr>
						</table>
					</td>
					<td>
						<table class="table table-bordered" style="height:200px;width:20px;">
						<tr>
							<td class="parameter_name">F<br>A<br>N</td>
						</tr>
						</table>
					</td>
				</tr>
				</tbody>
				</table>

				<h4>4G:</h4>
				<table class="table" style="height:200px;">
				<tbody>
				<tr>
					<td colspan="3"><?=substr($technos4G,0,-1)?></td>
				</tr>
				<tr>
					<td>
						<table class="table table-bordered" style="height:200px;">
						<tr>
							<td><?=$C2_SLOT15?></td>
							<td class="parameter_name" style="width:30px;">15</td>
						</tr>
						<tr>
							<td><?=$C2_SLOT14?></td>
							<td class="parameter_name">14</td>
						</tr>
						<tr>
							<td><?=$C2_SLOT13?></td>
							<td class="parameter_name">13</td>
						</tr>
						</table>
					</td>
					<td>
						<table class="table table-bordered" style="height:200px;">
						<tr>
							<td><?=$C2_SLOT4?></td>
							<td class="parameter_name" style="width:30px;">4</td>
							<td><?=$C2_SLOT8?></td>
							<td class="parameter_name" style="width:30px;">8</td>
						</tr>
						<tr>
							<td><?=$C2_SLOT3?></td>
							<td class="parameter_name">3</td>
							<td><?=$C2_SLOT7?></td>
							<td class="parameter_name">7</td>
						</tr>
						<tr>
							<td><?=$C2_SLOT2?></td>
							<td class="parameter_name">2</td>
							<td><?=$C2_SLOT6?></td>
							<td class="parameter_name">6</td>
						</tr>
						<tr>
							<td><?=$C2_SLOT1?></td>
							<td class="parameter_name">1</td>
							<td><?=$C2_SLOT5?></td>
							<td class="parameter_name">5</td>
						</tr>
						</table>
					</td>
					<td>
						<table class="table table-bordered" style="height:200px;width:20px;">
						<tr>
							<td class="parameter_name">F<br>A<br>N</td>
						</tr>
						</table>
					</td>
				</tr>
				</tbody>
				</table>
			</td>
			<td class="borderleft">
				<h4>Board 1  <span class="label label-info"><?=$CONFIG?></span></h4>
				<table class="table" style="height:200px;">
				<tbody>
				<tr>
					<td colspan="3"><input type="checkbox" name="pl_C1_G9" <?=$pl_C1_G9?> value="G9">G9 <input type="checkbox" name="pl_C1_G18" <?=$pl_C1_G18?> value="G18">G18 <input type="checkbox" name="pl_C1_U9" <?=$pl_C1_U9?> value="U9">U9 <input type="checkbox" name="pl_C1_U21" <?=$pl_C1_U21?> value="U21">U21 <input type="checkbox" name="pl_C1_L8" <?=$pl_C1_L8?> value="L8">L8 <input type="checkbox" name="pl_C1_L18" <?=$pl_C1_L18?> value="L18">L18 <input type="checkbox" name="pl_C1_L26" <?=$pl_C1_L26?> value="L26">L26
					</td>
				</tr>
				<tr>
					<td>
						<table class="table table-bordered" style="height:200px;">
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT15" id="pl_C1_SLOT15">
								<?php if($pl_C1_SLOT15!=''){ ?>
								<option selected><?=$pl_C1_SLOT15?></option>
								<?php } ?>
								<option></option>
								<option>PM0</option>
								<option>PM3</option>
								</select>
							</td>
							<td class="parameter_name" style="width:20px;">15 PM</td>
						</tr>
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT14" id="pl_C1_SLOT14">
								<?php if($pl_C1_SLOT14!=''){ ?>
								<option selected><?=$pl_C1_SLOT14?></option>
								<?php } ?>
								<option></option>
								<option>PM0</option>
								<option>PM3</option>
								</select>
							</td>
							<td class="parameter_name">14 PM</td>
						</tr>
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT13" id="pl_C1_SLOT13">
								<?php if($pl_C1_SLOT13!=''){ ?>
								<option selected><?=$pl_C1_SLOT13?></option>
								<?php } ?>
								<option></option>
								<option>SA0</option>
								<option>SA3</option>
								</select>
							</td>
							<td class="parameter_name">13 SA</td>
						</tr>
						</table>
					</td>
					<td>
						<table class="table table-bordered" style="height:200px;">
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT4" id="pl_C1_SLOT4">
								<?php if($pl_C1_SLOT4!=''){ ?>
								<option selected><?=$pl_C1_SLOT4?></option>
								<?php } ?>
								<option></option>
								<option>FS5</option>
								</select>
							</td>
							<td class="parameter_name" style="width:20px;">4 FS</td>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT8" id="pl_C1_SLOT8">
								<?php if($pl_C1_SLOT8!=''){ ?>
								<option selected><?=$pl_C1_SLOT8?></option>
								<?php } ?>
								<option></option>
								<option>BPN2</option>
								<option>BPL1</option>
								</select>
							</td>
							<td class="parameter_name" style="width:20px;">8 BP</td>
						</tr>
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT3" id="pl_C1_SLOT3">
								<?php if($pl_C1_SLOT3!=''){ ?>
								<option selected><?=$pl_C1_SLOT3?></option>
								<?php } ?>
								<option></option>
								<option>FS5</option>
								<option>FS0</option>
								</select>
							</td>
							<td class="parameter_name">3 FS</td>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT7" id="pl_C1_SLOT7">
								<?php if($pl_C1_SLOT7!=''){ ?>
								<option selected><?=$pl_C1_SLOT7?></option>
								<?php } ?>
								<option></option>
								<option>BPN2</option>
								</select>
							</td>
							<td class="parameter_name">7 BP</td>
						</tr>
						<tr>
							<td></td>
							<td class="parameter_name">2</td>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT6" id="pl_C1_SLOT6">
								<?php if($pl_C1_SLOT6!=''){ ?>
								<option selected><?=$pl_C1_SLOT6?></option>
								<?php } ?>
								<option></option>
								<option>BPN2</option>
								</select>
							</td>
							<td class="parameter_name">6 BP</td>
						</tr>
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT1" id="pl_C1_SLOT1">
								<?php if($pl_C1_SLOT1!=''){ ?>
								<option selected><?=$pl_C1_SLOT1?></option>
								<?php } ?>
								<option></option>
								<option>CC16</option>
								<option>CC16B</option>
								</select>
							</td>
							<td class="parameter_name">1 CC</td>
							<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT5" id="pl_C1_SLOT5">
								<?php if($pl_C1_SLOT5!=''){ ?>
								<option selected><?=$pl_C1_SLOT5?></option>
								<?php } ?>
								<option></option>
								<option>BPK</option>
								<option>BPN2</option>
								</select>
							</td>
							<td class="parameter_name">5 BP</td>
						</tr>
						</table>
					</td>
					<td>
						<table class="table table-bordered" style="height:200px;width:20px;">
						<tr>
							<td class="parameter_name">F<br>A<br>N</td>
						</tr>
						</table>
					</td>
				</tr>
				</tbody>
				</table>

				<h4>Board 2</h4>
				<table class="table" style="height:200px;">
				<tbody>
				<tr>
					<td colspan="3"><input type="checkbox" name="pl_C2_G9" <?=$pl_C2_G9?> value="G9">G9 <input type="checkbox" name="pl_C2_G18" <?=$pl_C2_G18?> value="G18">G18 <input type="checkbox" name="pl_C2_U9" <?=$pl_C2_U9?> value="U9">U9 <input type="checkbox" name="pl_C2_U21" <?=$pl_C2_U21?> value="U21">U21 <input type="checkbox" name="pl_C2_L8" <?=$pl_C2_L8?> value="L8">L8 <input type="checkbox" name="pl_C2_L18" <?=$pl_C2_L18?> value="L18">L18 <input type="checkbox" name="pl_C2_L26" <?=$pl_C2_L26?> value="L26">L26
					</td>
				</tr>
				<tr>
					<td>
						<table class="table table-bordered" style="height:200px;">
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT15" id="pl_C2_SLOT15">
								<?php if($pl_C2_SLOT15!=''){ ?>
								<option selected><?=$pl_C2_SLOT15?></option>
								<?php } ?>
								<option></option>
								<option>PM0</option>
								<option>PM3</option>
								</select>
							</td>
							<td class="parameter_name" style="width:20px;">15 PM</td>
						</tr>
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT14" id="pl_C2_SLOT14">
								<?php if($pl_C2_SLOT14!=''){ ?>
								<option selected><?=$pl_C2_SLOT14?></option>
								<?php } ?>
								<option></option>
								<option>PM0</option>
								<option>PM3</option>
								</select>
							</td>
							<td class="parameter_name">14 PM</td>
						</tr>
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT13" id="pl_C2_SLOT13">
								<?php if($pl_C2_SLOT13!=''){ ?>
								<option selected><?=$pl_C2_SLOT13?></option>
								<?php } ?>
								<option></option>
								<option>SA0</option>
								<option>SA3</option>
								</select>
							</td>
							<td class="parameter_name">13 SA</td>
						</tr>
						</table>
					</td>
					<td>
						<table class="table table-bordered" style="height:200px;">
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT4" id="pl_C2_SLOT4">
								<?php if($pl_C2_SLOT4!=''){ ?>
								<option selected><?=$pl_C2_SLOT4?></option>
								<?php } ?>
								<option></option>
								<option>FS5</option>
								</select>
							</td>
							<td class="parameter_name" style="width:20px;">4 FS</td>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT8" id="pl_C2_SLOT8">
								<?php if($pl_C2_SLOT8!=''){ ?>
								<option selected><?=$pl_C2_SLOT8?></option>
								<?php } ?>
								<option></option>
								<option>BPN2</option>
								<option>BPL1</option>
								</select>
							</td>
							<td style="width:20px;">8 BP</td>
						</tr>
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT3" id="pl_C2_SLOT3">
								<?php if($pl_C2_SLOT3!=''){ ?>
								<option selected><?=$pl_C2_SLOT3?></option>
								<?php } ?>
								<option></option>
								<option>FS5</option>
								<option>FS0</option>
								</select>
							</td>
							<td class="parameter_name">3 FS</td>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT7" id="pl_C2_SLOT7">
								<?php if($pl_C2_SLOT7!=''){ ?>
								<option selected><?=$pl_C2_SLOT7?></option>
								<?php } ?>
								<option></option>
								<option>BPN2</option>
								</select>
							</td>
							<td class="parameter_name">7 BP</td>
						</tr>
						<tr>
							<td></td>
							<td class="parameter_name">2</td>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT6" id="pl_C2_SLOT6">
								<?php if($pl_C2_SLOT6!=''){ ?>
								<option selected><?=$pl_C2_SLOT6?></option>
								<?php } ?>
								<option></option>
								<option>BPN2</option>
								</select>
							</td>
							<td class="parameter_name">6 BP</td>
						</tr>
						<tr>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT1" id="pl_C2_SLOT1">
								<?php if($pl_C2_SLOT1!=''){ ?>
								<option selected><?=$pl_C2_SLOT1?></option>
								<?php } ?>
								<option></option>
								<option>CC26</option>
								<option>CC26B</option>
								</select>
							</td>
							<td class="parameter_name">1 CC</td>
							<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT5" id="pl_C2_SLOT5">
								<?php if($pl_C2_SLOT5!=''){ ?>
								<option selected><?=$pl_C2_SLOT5?></option>
								<?php } ?>
								<option></option>
								<option>BPK</option>
								<option>BPN2</option>
								</select>
							</td>
							<td class="parameter_name">5 BP</td>
						</tr>
						</table>
					</td>
					<td>
						<table class="table table-bordered" style="height:200px;width:20px;">
						<tr>
							<td class="parameter_name">F<br>A<br>N</td>
						</tr>
						</table>
					</td>
				</tr>
				</tbody>
				</table>
			</td>
		</tr>
		</table>
		
		<?php
		if ($_POST['frozen']!=1 && $_POST['print']!="yes"){ ?>
		<p align='center'><input type="submit" class="btn btn-primary saveSubCurPlBBU" value="Save BBU layout" data-key="<?=$key_zband?>"></p>
		<?php
		}
		if ($_POST['print']!="yes"){
		?>
		</form>
		<?php 
		} ?> 
	</div>

	<div id='ColorAnalysis<?=$key_zband?>'>
		<?php

		$NewTechs='';
		$btns='';

		if (substr_count($_POST['technos'],'G9')=="1"){
			$NewTechs[]='G9';
			$btns.='<a class="btn btn-warning bsdsdetails2" data-key="G9'.$key_zband.'" data-techno="LOADTECHNO" data-id="'.$_POST['datakey'].'" data-band="G9" role="button">G9</a>';
			$outdivsbands.= "<div id='banddataG9".$key_zband."' class='banddata'></div>";
		}
		if (substr_count($_POST['technos'],'G18')=="1"){
			$NewTechs[]='G18';
			$btns.='<a class="btn btn-warning bsdsdetails2" data-key="G18'.$key_zband.'" data-techno="LOADTECHNO" data-id="'.$_POST['datakey'].'" data-band="G18" role="button">G18</a>';
			$outdivsbands.= "<div id='banddataG18".$key_zband."' class='banddata'></div>";
		}
		if (substr_count($_POST['technos'],'U9')=="1"){
			$NewTechs[]='U9';
			$btns.='<a class="btn btn-warning bsdsdetails2" data-key="U9'.$key_zband.'" data-techno="LOADTECHNO" data-id="'.$_POST['datakey'].'" data-band="U9" role="button">U9</a>';
			$outdivsbands.= "<div id='banddataU9".$key_zband."' class='banddata'></div>";
		}
		if (substr_count($_POST['technos'],'U21')=="1"){
			$NewTechs[]='U21';
			$btns.='<a class="btn btn-warning bsdsdetails2" data-key="U21'.$key_zband.'" data-techno="LOADTECHNO" data-id="'.$_POST['datakey'].'" data-band="U21" role="button">U21</a>';
			$outdivsbands.= "<div id='banddataU21".$key_zband."' class='banddata'></div>";
		}
		if (substr_count($_POST['technos'],'L8')=="1"){
			$NewTechs[]='L8';
			$btns.='<a class="btn btn-warning bsdsdetails2" data-key="L8'.$key_zband.'" data-techno="LOADTECHNO" data-id="'.$_POST['datakey'].'" data-band="L8" role="button">L8</a>';
			$outdivsbands.= "<div id='banddataL8".$key_zband."' class='banddata'></div>";
		}
		if (substr_count($_POST['technos'],'L18')=="1"){
			$NewTechs[]='L18';
			$btns.='<a class="btn btn-warning bsdsdetails2" data-key="L18'.$key_zband.'" data-techno="LOADTECHNO" data-id="'.$_POST['datakey'].'" data-band="L18" role="button">L18</a>';
			$outdivsbands.= "<div id='banddataL18".$key_zband."' class='banddata'></div>";
		}
		if (substr_count($_POST['technos'],'L26')=="1"){
			$NewTechs[]='L26';
			$btns.='<a class="btn btn-warning bsdsdetails2" data-key="L26'.$key_zband.'" data-techno="LOADTECHNO" data-id="'.$_POST['datakey'].'" data-band="L26" role="button">L26</a>';
			$outdivsbands.= "<div id='banddata".$key_zband."' class='banddata'></div>";
		}

		if ($_POST['print']!='yes'){
			echo $btns;
			echo "<br><br>".$outdivsbands;
		}else{

			if (substr_count($_POST['technos'], 'G9')==1){
				$band='G9';
				include('current_planned_output.php');
			}else if ($_POST['print']=='yes'){
				?>
				<div class="well well-sm">NO G9 data defined in ASSET</div>
				<hr style='padding: 0;border: none;border-top: medium double #333;color:#333;text-align:center;page-break-after: always'></hr>
				<?php
			}

			if (substr_count($_POST['technos'], 'G18')==1){
				$band='G18';
				include('current_planned_output.php');
			}else if ($_POST['print']=='yes'){
				?>
				<div class="well well-sm">NO G18 data defined in ASSET</div>
				<hr style='padding: 0;border: none;border-top: medium double #333;color:#333;text-align:center;page-break-after: always'></hr>
				<?php
			}
			
			if (substr_count($_POST['technos'], 'U9')==1){
				$band='U9';
				include('current_planned_output.php');
			}else if ($_POST['print']=='yes'){
				?>
				<div class="well well-sm">NO U9 data defined in ASSET</div>
				<hr style='padding: 0;border: none;border-top: medium double #333;color:#333;text-align:center;page-break-after: always'></hr>
				<?php
			}

			if (substr_count($_POST['technos'], 'U21')==1){
				$band='U21';
				include('current_planned_output.php');
			}else if ($_POST['print']=='yes'){
				?>
				<div class="well well-sm">NO U21 data defined in ASSET</div>
				<hr style='padding: 0;border: none;border-top: medium double #333;color:#333;text-align:center;page-break-after: always'></hr>
				<?php
			}

			if (substr_count($_POST['technos'], 'L8')==1){
				$band='L8';
				include('current_planned_output.php');
			}else if ($_POST['print']=='yes'){
				?>
				<div class="well well-sm">NO L8 data defined in ASSET</div>
				<hr style='padding: 0;border: none;border-top: medium double #333;color:#333;text-align:center;page-break-after: always'></hr>
				<?php
			}

			if (substr_count($_POST['technos'], 'L18')==1){
				$band='L18';
				include('current_planned_output.php');
			}else if ($_POST['print']=='yes'){
				?>
				<div class="well well-sm">NO L18 data defined in ASSET</div>
				<hr style='padding: 0;border: none;border-top: medium double #333;color:#333;text-align:center;page-break-after: always'></hr>
				<?php
			}


			if (substr_count($_POST['technos'], 'L26')==1){
				$band='L26';
				include('current_planned_output.php');
			}else if ($_POST['print']=='yes'){
				?>
				<div class="well well-sm">NO L26 data defined in ASSET</div>
				<hr style='padding: 0;border: none;border-top: medium double #333;color:#333;text-align:center;page-break-after: always'></hr>
				<?php
			}
		}
		OCILogoff($conn_Infobase);
		?>
	</div>
</div>

<br><br><br>

<script type="text/javascript">

	if ($('#uniran<?=$key_zband?>').is(':checked')){
		$('.unirandata<?=$key_zband?>').show();
		$('.hideunirandata<?=$key_zband?>').hide();
		$('.hideunirandataG9<?=$key_zband?>').hide();
		$('.hideunirandataG18<?=$key_zband?>').hide();
	}else{

		$('.unirandata<?=$key_zband?>').hide();
		$('.hideunirandata<?=$key_zband?>').show();
		$('.hideunirandataG9<?=$key_zband?>').show();
		$('.hideunirandataG18<?=$key_zband?>').show();
	}

	$('#uniran<?=$key_zband?>').change(function() {
		if ($(this).is(':checked')){
			$('.unirandata<?=$key_zband?>').show();
			$('.hideunirandata<?=$key_zband?>').hide();

			$('.hideunirandataG9<?=$key_zband?>').hide();
			$('.hideunirandataG18<?=$key_zband?>').hide();
		}else{
			$('.unirandata<?=$key_zband?>').hide();
			$('.hideunirandata<?=$key_zband?>').show();

			$('.hideunirandataG9<?=$key_zband?>').show();
			$('.hideunirandataG18<?=$key_zband?>').show();
		}
	});
<?php  if ($_POST['print']!='yes'){ ?>
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
		      url: "scripts/current_planned/field_list.php",
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

	get_select2('','cabtype','cabtype_list');
<?php } ?>

<?php  if ($_POST['print']=='yes'){ ?>



	$('#ColorAnalysis<?=$key_zband?> .form-control').each(function(){
	   	
    	var name =this.name;
    	var id=this.id;	

    	//The planned value		    	
    	var plval=$(this).val();
    	console.log('name='+name+'/'+id+'pl:'+plval+'/'+id.substr(0,3));
		
		if (id.substr(0,3)==='pl_'){ 
	    	right= id.split('pl_');
  			idname =right[1];
  			//The current value
	    	var curval=$('#printArea<?=$key_zband?> #cur_'+idname).val(); 
	    	//console.log(idname+'-- name='+name+'/'+id+'=>pl:'+plval+'/cur:'+curval+'/');
		    if (curval!==plval){	
		    	$('#printArea<?=$key_zband?> #pl_'+idname).replaceWith("<span class='notsame'>" + plval+ "</span>");
		    }else{
		    	$('#printArea<?=$key_zband?> #pl_'+idname).replaceWith("<span class='same'>" + plval+ "</span>");
		    }			    	
		}else{
			//console.log('=>>>> name='+name+'/'+id+'pl:'+plval+'/');
		   	var currentVal=$('#printArea<?=$key_zband?> #'+id).val();   	
		    $('#printArea<?=$key_zband?> #'+id).replaceWith("<span>" + currentVal+ "</span>");		    	
		}					
	});	

	window.print();  
<?php } ?>
</script>

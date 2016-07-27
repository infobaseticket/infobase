<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


//4G OSS DATA
$query =  "select DISTINCT(TECHNO) from SWITCH_3GZTE_UUTRANCELLFDD WHERE USERLABEL LIKE '%".$_POST['siteID']."%'";
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

$query =  "select BBU_SLOTNO,BBU_CARD,SLOT,DESCRIPTION from SWITCH_4GZTE_SDRDEVICEGROUP WHERE MEID = '".ltrim(substr($_POST['siteID'],2,-1), '0')."' AND BBU_CARD IS NOT NULL ORDER BY BBU_SLOTNO ASC";
echo $query;
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
		$hidden4G.="<input type='hidden' name='cur_C1_SLOT".$slot."' value='".$resSDR['BBU_CARD'][$i]."'>";
	}
}
//3G OSS DATA
$query =  "select DISTINCT(TECHNO) from SWITCH_4GZTE_UTRANCELL WHERE ULABEL LIKE '%".$_POST['siteID']."%'";
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
$query =  "select SLOTNO, USERLABEL,BBU_SLOTNO,BBU_CARD  from SWITCH_3GZTE_PLUGINUNIT WHERE RACKNO = '".ltrim(substr($_POST['siteID'],2,-1), '0')."'  AND BBU_CARD IS NOT NULL ORDER BY SLOTNO ASC";
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
		$hidden3G.="<input type='hidden' name='cur_C2_SLOT".$slot."' value='".$res1['BBU_CARD'][$i]."'>";
	}
}
?>
<div id="<?=$_POST['print']?>curpl_<?=$_POST['bsdskey']?><?=$_POST['band']?><?=$_POST['status']?><?php echo str_replace(':', '', str_replace('/', '', str_replace(' ', '', $_POST['bsdsbobrefresh']))); ?>"  style='page-break-after: always'>
<?php
if ($_POST['print']!="yes"){
?>
<form action="scripts/current_planned2/save_pl_cu_BBU.php" method="post" id="current_planned_form<?=$_POST['band']?><?=$viewtype?>" role="form">
<input type="hidden" name="band" value="<?=$_POST['band']?>">
<input type="hidden" name="pl_band" value="<?=$_POST['band']?>">
<input type="hidden" name="action" value="save">
<input type="hidden" name="lognode" value="<?=$_POST['lognodeID_GSM']?>">
<input type="hidden" name="bsdskey" value="<?=$_POST['bsdskey']?>">
<input type="hidden" name="bsdsbobrefresh" value="<?=$_POST['bsdsbobrefresh']?>">
<input type="hidden" name="viewtype" value="<?=$viewtype?>">
<?=$hidden3G?>
<?=$hidden4G?>
<?php 
} ?>
<table class="table table-bordered">
<tr>
	<th width="50%">CURRENT LIVE BBU LAYOUT FROM NETNUMEN</th>
	<th width="50%">PLANNED BBU LAYOUT</th>
</tr>
<tr>
	<td>
		<h4>3G:</h4>
		<table class="table" style="height:200px;">
		<tbody>
		<tr>
			<td colspan="3"><?=substr($technos3G,0,-1)?></td>
		</tr>
		<tr>
			<td>
				<table class="table table-bordered" style="height:200px;">
				<tr>
					<td><?=$data3G[15]?></td>
					<td style="width:30px;"><b>15</b></td>
				</tr>
				<tr>
					<td><?=$data3G[14]?></td>
					<td><b>14</b></td>
				</tr>
				<tr>
					<td><?=$data3G[13]?></td>
					<td><b>13</b></td>
				</tr>
				</table>
			</td>
			<td>
				<table class="table table-bordered" style="height:200px;">
				<tr>
					<td><?=$data3G[4]?></td>
					<td style="width:30px;"><b>4</b></td>
					<td><?=$data3G[8]?></td>
					<td style="width:30px;"><b>8</b></td>
				</tr>
				<tr>
					<td><?=$data3G[3]?></td>
					<td><b>3</b></td>
					<td><?=$data3G[7]?></td>
					<td><b>7</b></td>
				</tr>
				<tr>
					<td><?=$data3G[2]?></td>
					<td><b>2</b></td>
					<td><?=$data3G[6]?></td>
					<td><b>6</b></td>
				</tr>
				<tr>
					<td><?=$data3G[1]?></td>
					<td><b>1</b></td>
					<td><?=$data3G[5]?></td>
					<td><b>5</b></td>
				</tr>
				</table>
			</td>
			<td>
				<table class="table table-bordered" style="height:200px;width:20px;">
				<tr>
					<td>F<br>A<br>N</td>
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
					<td><?=$data4G[15]?></td>
					<td style="width:30px;"><b>15</b></td>
				</tr>
				<tr>
					<td><?=$data4G[14]?></td>
					<td><b>14</b></td>
				</tr>
				<tr>
					<td><?=$data4G[13]?></td>
					<td><b>13</b></td>
				</tr>
				</table>
			</td>
			<td>
				<table class="table table-bordered" style="height:200px;">
				<tr>
					<td><?=$data4G[4]?></td>
					<td style="width:30px;"><b>4</b></td>
					<td><?=$data4G[8]?></td>
					<td style="width:30px;"><b>8</b></td>
				</tr>
				<tr>
					<td><?=$data4G[3]?></td>
					<td><b>3</b></td>
					<td><?=$data4G[7]?></td>
					<td><b>7</b></td>
				</tr>
				<tr>
					<td><?=$data4G[2]?></td>
					<td><b>2</b></td>
					<td><?=$data4G[6]?></td>
					<td><b>6</b></td>
				</tr>
				<tr>
					<td><?=$data4G[1]?></td>
					<td><b>1</b></td>
					<td><?=$data4G[5]?></td>
					<td><b>5</b></td>
				</tr>
				</table>
			</td>
			<td>
				<table class="table table-bordered" style="height:200px;width:20px;">
				<tr>
					<td>F<br>A<br>N</td>
				</tr>
				</table>
			</td>
		</tr>
		</tbody>
		</table>
	</td>
	<td>
		<h4>Board 1</h4>
		<table class="table" style="height:200px;">
		<tbody>
		<tr>
			<td colspan="3"><input type="checkbox" name="C1_G9" value="G9">G9 <input type="checkbox" name="C1_G9" value="G9">G18 <input type="checkbox" name="C1_U9" value="U9">U9 <input type="checkbox" name="C1_U21" value="U21">U21 <input type="checkbox" name="C1_L8" value="L8">L8 <input type="checkbox" name="C1_L18" value="L18">L18 <input type="checkbox" name="C1_L26" value="L26">L26
			</td>
		</tr>
		<tr>
			<td>
				<table class="table table-bordered" style="height:200px;">
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT15">
						<?php if($pl_C1_SLOT15!=''){ ?>
						<option selected><?=$pl_C1_SLOT15?></option>
						<?php } ?>
						<option></option>
						<option>PM0</option>
						<option>PM3</option>
						</select>
					</td>
					<td style="width:20px;"><b>15</b></td>
				</tr>
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT14">
						<?php if($pl_C1_SLOT14!=''){ ?>
						<option selected><?=$pl_C1_SLOT14?></option>
						<?php } ?>
						<option></option>
						<option>PM0</option>
						<option>PM3</option>
						</select>
					</td>
					<td><b>14</b></td>
				</tr>
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT13">
						<?php if($pl_C1_SLOT13!=''){ ?>
						<option selected><?=$pl_C1_SLOT13?></option>
						<?php } ?>
						<option></option>
						<option>SA</option>
						<option>SA3</option>
						</select>
					</td>
					<td><b>13</b></td>
				</tr>
				</table>
			</td>
			<td>
				<table class="table table-bordered" style="height:200px;">
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT4">
						<?php if($pl_C1_SLOT4!=''){ ?>
						<option selected><?=$pl_C1_SLOT4?></option>
						<?php } ?>
						<option></option>
						<option>FS</option>
						<option>FS3</option>
						<option>FS3A</option>
						<option>FS5</option>
						<option>BPN2</option>
						</select>
					</td>
					<td style="width:20px;"><b>4</b></td>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT8">
						<?php if($pl_C1_SLOT8!=''){ ?>
						<option selected><?=$pl_C1_SLOT8?></option>
						<?php } ?>
						<option></option>
						<option>BPC</option>
						<option>BPL0</option>
						<option>BPK</option>
						<option>BPN2</option>
						<option>BPL1</option>
						</select>
					</td>
					<td style="width:20px;"><b>8</b></td>
				</tr>
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT3">
						<?php if($pl_C1_SLOT3!=''){ ?>
						<option selected><?=$pl_C1_SLOT3?></option>
						<?php } ?>
						<option></option>
						<option>FS</option>
						<option>FS3</option>
						<option>FS3A</option>
						<option>FS5</option>
						<option>BPN2</option>
						</select>
					</td>
					<td><b>3</b></td>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT7">
						<?php if($pl_C1_SLOT7!=''){ ?>
						<option selected><?=$pl_C1_SLOT7?></option>
						<?php } ?>
						<option></option>
						<option>BPC</option>
						<option>BPL0</option>
						<option>BPK</option>
						<option>BPN2</option>
						<option>BPL1</option>
						</select>
					</td>
					<td><b>7</b></td>
				</tr>
				<tr>
					<td></td>
					<td><b>2</b></td>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT6">
						<?php if($pl_C1_SLOT6!=''){ ?>
						<option selected><?=$pl_C1_SLOT6?></option>
						<?php } ?>
						<option></option>
						<option>BPC</option>
						<option>BPL0</option>
						<option>BPK</option>
						<option>BPN2</option>
						<option>BPL1</option>
						</select>
					</td>
					<td><b>6</b></td>
				</tr>
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT1">
						<?php if($pl_C1_SLOT1!=''){ ?>
						<option selected><?=$pl_C1_SLOT1?></option>
						<?php } ?>
						<option>CC2</option>
						<option>CC16</option>
						</select>
					</td>
					<td><b>1</b></td>
					<td><select class="form-control input-sm tabledata" name="pl_C1_SLOT5">
						<?php if($pl_C1_SLOT5!=''){ ?>
						<option selected><?=$pl_C1_SLOT5?></option>
						<?php } ?>
						<option></option>
						<option>BPC</option>
						<option>BPK</option>
						<option>BPN2</option>
						</select>
					</td>
					<td><b>5</b></td>
				</tr>
				</table>
			</td>
			<td>
				<table class="table table-bordered" style="height:200px;width:20px;">
				<tr>
					<td>F<br>A<br>N</td>
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
			<td colspan="3"><input type="checkbox" name="C2_G9" value="G9">G9 <input type="checkbox" name="C2_G9" value="G9">G18 <input type="checkbox" name="C2_U9" value="U9">U9 <input type="checkbox" name="C2_U21" value="U21">U21 <input type="checkbox" name="C2_L8" value="L8">L8 <input type="checkbox" name="C2_L18" value="L18">L18 <input type="checkbox" name="C2_L26" value="L26">L26
			</td>
		</tr>
		<tr>
			<td>
				<table class="table table-bordered" style="height:200px;">
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT15">
						<?php if($pl_C2_SLOT15!=''){ ?>
						<option selected><?=$pl_C2_SLOT15?></option>
						<?php } ?>
						<option></option>
						<option>PM0</option>
						<option>PM3</option>
						</select>
					</td>
					<td style="width:20px;"><b>15</b></td>
				</tr>
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT14">
						<?php if($pl_C2_SLOT14!=''){ ?>
						<option selected><?=$pl_C2_SLOT14?></option>
						<?php } ?>
						<option></option>
						<option>PM0</option>
						<option>PM3</option>
						</select>
					</td>
					<td><b>14</b></td>
				</tr>
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT13">
						<?php if($pl_C2_SLOT13!=''){ ?>
						<option selected><?=$pl_C2_SLOT13?></option>
						<?php } ?>
						<option></option>
						<option>SA</option>
						<option>SA3</option>
						</select>
					</td>
					<td><b>13</b></td>
				</tr>
				</table>
			</td>
			<td>
				<table class="table table-bordered" style="height:200px;">
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT4">
						<?php if($pl_C2_SLOT4!=''){ ?>
						<option selected><?=$pl_C2_SLOT4?></option>
						<?php } ?>
						<option></option>
						<option>FS</option>
						<option>FS3</option>
						<option>FS3A</option>
						<option>FS5</option>
						<option>BPN2</option>
						</select>
					</td>
					<td style="width:20px;"><b>4</b></td>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT8">
						<?php if($pl_C2_SLOT8!=''){ ?>
						<option selected><?=$pl_C2_SLOT8?></option>
						<?php } ?>
						<option></option>
						<option>BPC</option>
						<option>BPL0</option>
						<option>BPK</option>
						<option>BPN2</option>
						<option>BPL1</option>
						</select>
					</td>
					<td style="width:20px;"><b>8</b></td>
				</tr>
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT3">
						<?php if($pl_C2_SLOT3!=''){ ?>
						<option selected><?=$pl_C2_SLOT3?></option>
						<?php } ?>
						<option></option>
						<option>FS</option>
						<option>FS3</option>
						<option>FS3A</option>
						<option>FS5</option>
						<option>BPN2</option>
						</select>
					</td>
					<td><b>3</b></td>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT7">
						<?php if($pl_C2_SLOT7!=''){ ?>
						<option selected><?=$pl_C2_SLOT7?></option>
						<?php } ?>
						<option></option>
						<option>BPC</option>
						<option>BPL0</option>
						<option>BPK</option>
						<option>BPN2</option>
						<option>BPL1</option>
						</select>
					</td>
					<td><b>7</b></td>
				</tr>
				<tr>
					<td></td>
					<td><b>2</b></td>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT6">
						<?php if($pl_C2_SLOT6!=''){ ?>
						<option selected><?=$pl_C2_SLOT6?></option>
						<?php } ?>
						<option></option>
						<option>BPC</option>
						<option>BPL0</option>
						<option>BPK</option>
						<option>BPN2</option>
						<option>BPL1</option>
						</select>
					</td>
					<td><b>6</b></td>
				</tr>
				<tr>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT1">
						<?php if($pl_C2_SLOT1!=''){ ?>
						<option selected><?=$pl_C2_SLOT1?></option>
						<?php } ?>
						<option>CC2</option>
						<option>CC26</option>
						</select>
					</td>
					<td><b>1</b></td>
					<td><select class="form-control input-sm tabledata" name="pl_C2_SLOT5">
						<?php if($pl_C2_SLOT5!=''){ ?>
						<option selected><?=$pl_C2_SLOT5?></option>
						<?php } ?>
						<option></option>
						<option>BPC</option>
						<option>BPK</option>
						<option>BPN2</option>
						</select>
					</td>
					<td><b>5</b></td>
				</tr>
				</table>
			</td>
			<td>
				<table class="table table-bordered" style="height:200px;width:20px;">
				<tr>
					<td>F<br>A<br>N</td>
				</tr>
				</table>
			</td>
		</tr>
		</tbody>
		</table>
	</td>
</tr>
</table>

<p align='center'><input type="submit" class="btn btn-primary subCurPl" value="Save layout" id="save_bsdsdata" data-techno="<?=$_POST['band']?>" data-viewtype="<?=$viewtype?>"></p>

<?php

if ($_POST['print']!="yes"){
?>
</form>
<?php } 

OCILogoff($conn_Infobase);
?>
</div>
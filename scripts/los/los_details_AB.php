<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
?>
<script language="JavaScript">
	$(document).ready( function(){

		function after_LOSdetails_save(response)  {
			$('#modalspinner').spin(false);
			Messenger().post({message:response.responsedata,type: response.responsetype,showCloseButton:true,hideAfter: 5,hideOnNavigate: true});
		}
		function validateLOS(formData, jqForm, options){
			$('#modalspinner').spin('small');
		}

		var options = {
		    success:  after_LOSdetails_save,
			dataType:  'json',
			beforeSubmit: validateLOS
		};

		$('#los_details_form').submit(function(){
		    $(this).ajaxSubmit(options);
		    return false;
		});
	});
	</script>
<?

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query="SELECT * from BSDS_LINKINFO_DETAILS_".$_POST['end']." WHERE LOSID='".$_POST['losid']."'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_LOS=count($res1['LOSID']);
	//echo $amount_of_LOS;
	if ($amount_of_LOS==1){
       $CREATION_BY       = $res1['CREATION_BY'][0];
       $CREATION_DATE     = $res1['CREATION_DATE'][0];
       $UPDATE_BY         = $res1['UPDATE_BY'][0];
       $UPDATE_DATE       = $res1['UPDATE_DATE'][0];
       $MINHEIGHT         = $res1['MINHEIGHT'][0];
       $OBSTR_TYPE        = $res1['OBSTR_TYPE'][0];
       $OBSTR_DISTANCE    = $res1['OBSTR_DISTANCE'][0];
       $SURVEYOR_COMMENTS = $res1['SURVEYOR_COMMENTS'][0];
       $SKETCH            = $res1['SKETCH'][0];
       $PHOTO             = $res1['PHOTO'][0];
       $SITESHARE         = $res1['SITESHARE'][0];
       $LOCATION          = $res1['LOCATION'][0];
       $PANORAMIC         = $res1['PANORAMIC'][0];
       $COORDINATES       = $res1['COORDINATES'][0];
       $SURVEYOR_NAME     = $res1['SURVEYOR_NAME'][0];
       $SURVEY_DATE       = $res1['SURVEY_DATE'][0];

       if ($PHOTO==1){ $PHOTO_check="checked";}
       if ($SKETCH==1){ $SKETCH_check="checked";}
       if ($SITESHARE==1){ $SITESHARE_check="checked";}
       if ($LOCATION ==1){ $LOCATION_check="checked";}
       if ($PANORAMIC==1){ $PANORAMIC_check="checked";}
       if ($COORDINATES==1){ $COORDINATES_check="checked";}

	}else if ($amount_of_LOS>1){
		echo "ERRO in los. Please contact Infobase admin!";
	}
}
//echo $amount_of_LOS;
?>

<form action="scripts/los/los_actions.php" method="post" id="los_details_form">
<input type="hidden" name="action" value="update_los_details">
<input type="hidden" name="end" value="<?=$_POST['end']?>">
<input type="hidden" name="losid" value="<?=$_POST['losid']?>">

<table>
<tr>
	<td class="param_title">Min. <?=$_POST['end']?> end Antenna Height required for LOS</td>
	<td><input type="text" name="MINHEIGHT" value="<?=$MINHEIGHT?>"></td>
</tr>
<tr>
	<td class="param_title">Type of obstruction</td>
	<td><input type="text" name="OBSTR_TYPE" value="<?=$OBSTR_TYPE?>"></td>
</tr>
<tr>
	<td class="param_title">Distance to obstruction</td>
	<td><input type="text" name="OBSTR_DISTANCE" value="<?=$OBSTR_DISTANCE?>"></td>
</tr>
<tr>
	<td colspan=2 class="param_title">Surveyor Comments</td>
</tr>
<tr>
	<td colspan=2><textarea name='SURVEYOR_COMMENTS' style="width:500px;height:150px;"><?=$SURVEYOR_COMMENTS?></textarea></td>
</tr>
<tr>
	<td colspan=2>
		<table>
		<tr>
			<td>
				<input type="checkbox" name="SKETCH" <?=$SKETCH_check?> value="1"> Roof Sketch / SIte Sketch<br>
				<input type="checkbox" name="LOCATION" <?=$LOCATION_check?> value="1"> Survey Location Shown
			</td>
			<td>

				<input type="checkbox" name="PHOTO" <?=$PHOTO_check?> value="1"> LOS Photos<br>
				<input type="checkbox" name="PANORAMIC" <?=$PANORAMIC_check?> value="1"> Panoramic Photos
			</td>
			<td>
				<input type="checkbox" name="SITESHARE" <?=$SITESHARE_check?> value="1"> Site Share<br>
				<input type="checkbox" name="COORDINATES" <?=$COORDINATES_check?> value="1"> Coordinates Checked
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="param_title"><?=$_POST['end']?> end Surveyors Name</td>
	<td><input type="text" name="SURVEYOR_NAME" value="<?=$SURVEYOR_NAME?>"></td>
</tr>
<tr>
	<td class="param_title">Date of survey</td>
	<td>
	 <input name="SURVEY_DATE" type="text" value="<?=$SURVEY_DATE?>">
	</td>
</tr>
</tr>
<? if (substr_count($guard_groups, 'Partner')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
<tr>
	<td style="text-align: center" colspan="2"><input type="submit" value="Save changes" class="btn btn-primary"></td>
</tr>
<? } ?>
</table>
</form>

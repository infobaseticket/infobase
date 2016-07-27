<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/dirlister/filefunctions.php");

include('los_functions.php');

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<div class="printThis">
<div class="row well" style="margin:0 1px;">
  <div style="float:left;"><img src="<?php echo $config['sitepath_url']; ?>/bsds/images/basecompany.png"></div>
  <div style="float:left;margin-left:30px;padding-left:30px;border-left:1px solid #000;"><img src="<?php echo $config['sitepath_url']; ?>/bsds/images/logoInfobase.png" width="200px"></span></div>
  <div style="float:left;border-left:1px solid #000;padding-left:20px;"><h4 class="raftitle_print">LOS ID <?=$_POST['losid']?> <?=$_POST['siteid']?></h4></div>
</div>
<?
$query = "Select * FROM BSDS_LINKINFO WHERE ID = '".$_POST['losid']."'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_LOS=count($res1['SITEID'][0]);	

	$SITEA=$res1['SITEA'][0];
	$SITEB=$res1['SITEB'][0];
	$CREATION_BY    =$res1['CREATION_BY'][0];
  	$CREATION_DATE =$res1['CREATION_DATE'][0];
  	$UPDATE_BY      =$res1['UPDATE_BY'][0];
	$UPDATE_DATE    =$res1['UPDATE_DATE'][0];
	$PRIORITY       =$res1['PRIORITY'][0];
	$PRIORITY_BY    =$res1['PRIORITY_BY'][0];
	$PRIORITY_DATE  =$res1['PRIORITY_DATE'][0];
	$DONE           =$res1['DONE'][0];
	$DONE_BY        =$res1['DONE_BY'][0];
	$DONE_DATE      =$res1['DONE_DATE'][0];
	$REPORT         =$res1['REPORT'][0];
	$REPORT_BY      =$res1['REPORT_BY'][0];
	$REPORT_DATE    =$res1['REPORT_DATE'][0];
	$RESULT         =$res1['RESULT'][0];
	$RESULT_BY      =$res1['RESULT_BY'][0];
	$RESULT_DATE    =$res1['RESULT_DATE'][0];
	$COMMENTSA      =$res1['COMMENTSA'][0];
	$COMMENTSB      =$res1['COMMENTSB'][0];
	$HEIGHTA        =$res1['HEIGHTA'][0];
	$HEIGHTB        =$res1['HEIGHTB'][0];
	$REJECT_REASON  =$res1['REJECT_REASON'][0]; 
			
}

$query="SELECT SIT_UDK,SIT_ADDRESS, SIT_X_COORDINATE, SIT_Y_COORDINATE, SITE_TYPE_LDE_DESC,POSTCODE, LATESTMILESTONE from VW_NET1_ALL_NEWBUILDS WHERE trim(SIT_UDK)='".strtoupper($SITEA)."'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $resA);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_NEWA=count($resA['SIT_UDK']);
	$SIT_ADDRESSA=str_replace(",","<br>",$resA['SIT_ADDRESS'][0]);
	$POSTCODEA=$resA['POSTCODE'][0];		
}

$query="SELECT SIT_UDK,SIT_ADDRESS, SIT_X_COORDINATE, SIT_Y_COORDINATE, SITE_TYPE_LDE_DESC,POSTCODE , LATESTMILESTONE from VW_NET1_ALL_NEWBUILDS WHERE trim(SIT_UDK)='".strtoupper($SITEB)."'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $resB);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_NEWB=count($resB['SIT_UDK']);
	$SIT_ADDRESSB=str_replace(",","<br>",$resB['SIT_ADDRESS'][0]);
	$POSTCODEB=$resB['POSTCODE'][0];		
}
//echo $amount_of_NEWB;


$DiffEast=($resA['SIT_X_COORDINATE'][0]-$resB['SIT_X_COORDINATE'][0]);
$DiffNorth=($resA['SIT_Y_COORDINATE'][0]-$resB['SIT_Y_COORDINATE'][0]);
$Distance=sqrt($DiffEast*$DiffEast+$DiffNorth*$DiffNorth);
$Distance/=1000;
$Distance=number_format($Distance,2)." km";

list($AngleAB,$AngleBA)=CalcAngle($resA['SIT_X_COORDINATE'][0],$resA['SIT_Y_COORDINATE'][0],$resB['SIT_X_COORDINATE'][0],$resB['SIT_Y_COORDINATE'][0]);
  
$query="SELECT * from BSDS_LINKINFO_DETAILS_A WHERE LOSID='".$_GET['losid']."'";
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
       $CREATION_BY_A      = $res1['CREATION_BY'][0];
       $CREATION_DATE_A     = $res1['CREATION_DATE'][0];
       $UPDATE_BY_A         = $res1['UPDATE_BY'][0];
       $UPDATE_DATE_A       = $res1['UPDATE_DATE'][0];
       $MINHEIGHT_A         = $res1['MINHEIGHT'][0];
       $OBSTR_TYPE_A        = $res1['OBSTR_TYPE'][0];
       $OBSTR_DISTANCE_A    = $res1['OBSTR_DISTANCE'][0];
       $SURVEYOR_COMMENTS_A = $res1['SURVEYOR_COMMENTS'][0];
       $SKETCH_A            = $res1['SKETCH'][0];
       $PHOTO_A             = $res1['PHOTO'][0];
       $SITESHARE_A         = $res1['SITESHARE'][0];
       $LOCATION_A          = $res1['LOCATION'][0];
       $PANORAMIC_A         = $res1['PANORAMIC'][0];
       $COORDINATES_A       = $res1['COORDINATES'][0];
       $SURVEYOR_NAME_A     = $res1['SURVEYOR_NAME'][0];
       $SURVEY_DATE_A       = $res1['SURVEY_DATE'][0];
       
       if ($PHOTO_A==1){ $PHOTO_A_check="checked";}
       if ($SKETCH_A==1){ $SKETCH_A_check="checked";}
       if ($SITESHARE_A==1){ $SITESHARE_A_check="checked";}
       if ($LOCATION_A ==1){ $LOCATION_A_check="checked";}
       if ($PANORAMIC_A==1){ $PANORAMIC_A_check="checked";}
       if ($COORDINATES_A==1){ $COORDINATES_A_check="checked";}

	}else if ($amount_of_LOS>1){
		echo "ERRO in los. Please contact Infobase admin!";
	}
} 

$query="SELECT * from BSDS_LINKINFO_DETAILS_B WHERE LOSID='".$_GET['losid']."'";
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
       $CREATION_BY_B      = $res1['CREATION_BY'][0];
       $CREATION_DATE_B     = $res1['CREATION_DATE'][0];
       $UPDATE_BY_B         = $res1['UPDATE_BY'][0];
       $UPDATE_DATE_B       = $res1['UPDATE_DATE'][0];
       $MINHEIGHT_B         = $res1['MINHEIGHT'][0];
       $OBSTR_TYPE_B        = $res1['OBSTR_TYPE'][0];
       $OBSTR_DISTANCE_B    = $res1['OBSTR_DISTANCE'][0];
       $SURVEYOR_COMMENTS_B = $res1['SURVEYOR_COMMENTS'][0];
       $SKETCH_B            = $res1['SKETCH'][0];
       $PHOTO_B             = $res1['PHOTO'][0];
       $SITESHARE_B         = $res1['SITESHARE'][0];
       $LOCATION_B          = $res1['LOCATION'][0];
       $PANORAMIC_B         = $res1['PANORAMIC'][0];
       $COORDINATES_B       = $res1['COORDINATES'][0];
       $SURVEYOR_NAME_B     = $res1['SURVEYOR_NAME'][0];
       $SURVEY_DATE_B       = $res1['SURVEY_DATE'][0];
       
       if ($PHOTO_B==1){ $PHOTO_B_check="checked";}
       if ($SKETCH_B==1){ $SKETCH_B_check="checked";}
       if ($SITESHARE_B==1){ $SITESHARE_B_check="checked";}
       if ($LOCATION_B ==1){ $LOCATION_B_check="checked";}
       if ($PANORAMIC_B==1){ $PANORAMIC_B_check="checked";}
       if ($COORDINATES_B==1){ $COORDINATES_B_check="checked";}

	}else if ($amount_of_LOS>1){
		echo "ERRO in los. Please contact Infobase admin!";
	}
} 
?>
<div id="radioform_5" class="formdata_print">
	<table class="table">
	<tr>
		<td class="param_title">PRIORITY:</td>
		<td><?=$PRIORITY?></td>
	</tr>
	<? if($CREATION_BY) { 
		$user=getuserdata($CREATION_BY);
		?>
	<tr>
		<td class="param_title">CREATED BY:</td>
		<td><a href="mailto:<?=$user['email']?>" title="Mobile: (<?=$user['mobile']?>)"><?=$user['firstname']?> <?=$user['lastname']?></a>  on <?=$CREATION_DATE?></td>
	</tr>
	<? }
	   if($UPDATE_BY) { 
	   	$user=getuserdata($UPDATE_BY);
	?>
	<tr>
		<td class="param_title">UPDATED BY:</td>
		<td><a href="mailto:<?=$user['email']?>" title="Mobile: (<?=$user['mobile']?>)"><?=$user['firstname']?> <?=$user['lastname']?></a>  on <?=$UPDATE_DATE?></td>
	</tr>
	<? }
	   if($DONE_BY) { 
	   	$user=getuserdata($DONE_BY);
	?>
	<tr>
		<td class="param_title">PROCESSED BY:</td>
		<td><a href="mailto:<?=$user['email']?>" title="Mobile: (<?=$user['mobile']?>)"><?=$user['firstname']?> <?=$user['lastname']?></a>  on <?=$DONE_DATE?></td>
	</tr>
	<? }
	   if($REPORT_BY) { 
	   	$user=getuserdata($UPDATE_BY);
	?>
	<tr>
		<td class="param_title">REPORT BY:</td>
		<td><a href="mailto:<?=$user['email']?>" title="Mobile: (<?=$user['mobile']?>)"><?=$user['firstname']?> <?=$user['lastname']?></a>  on <?=$REPORT_DATE?></td>
	</tr>
	<? }
	   if($RESULT_BY) { 
	   	$user=getuserdata($UPDATE_BY);
	?>
	<tr>
		<td class="param_title">RESULT EVALUATION BY:</td>
		<td><a href="mailto:<?=$user['email']?>" title="Mobile: (<?=$user['mobile']?>)"><?=$user['firstname']?> <?=$user['lastname']?></a>  on <?=$RESULT_DATE?></td>
	</tr>
	<? } ?>
	
	<tr>
		<td class="param_title">REJECT HISTORY:</td>
		<td><?=$REJECT_REASON?></td>
	</tr>
	</table>
	<br>
	<table>
	<tr>	
		<td valign="top" width="40%">
			<table class="table">
			<tr>
				<td class="param_title" width="120px">SITE A</td>
				<td><?=$SITEA?></td>
			</tr>
			<tr>
				<td class="param_title" width="120px">BEARING AB</td>
				<td><?=$AngleAB?></td>
			</tr>
			<tr>
				<td class="param_title">Site type</td>
				<td><?=$resA['SITE_TYPE_LDE_DESC'][0]?></td>
			</tr>
				<tr>
				<td class="param_title">Easting</td>
				<td><?=$resA['SIT_X_COORDINATE'][0]?></td>
				<td colspan=2>&nbsp;</td>			
			</tr>
			<tr>
				<td class="param_title">Northing</td>
				<td><?=$resA['SIT_Y_COORDINATE'][0]?></td>
			</tr>	
			<tr>
				<td class="param_title">Milestone</td>
				<td><?=$resA['LATESTMILESTONE'][0]?></td>
			</tr>
			<tr>
				<td class="param_title">Antenna height</td>
				<td><?=$HEIGHTA?></td>
			</tr>				
			<tr>
				<td class="param_title" valign="top">Address</td>
				<td><?=$SIT_ADDRESSA?></td>
			</tr>
			<tr>
				<td class="param_title">Postcode</td>
				<td><?=$POSTCODEA?></td>
			</tr>
			</table>
		</td>
		<td width="20%" align="center" class="LOScenter" style="border-left: 1px solid #ccc; border-right: 1px solid #ccc;vertical-align:top"><b>Distance:</b><br> <--  <?=$Distance?> --> </td>
		<td valign="top" width="40%">
			<table class="table">
			<tr>
				<td class="param_title" width="120px">SITE B</td>
				<td><?=$SITEB?></td>
			</tr>
			<tr>
				<td class="param_title" width="120px">BEARING BA</td>
				<td><?=$AngleBA?></td>
			<tr>
				<td class="param_title">Site type</td>
				<td><?=$resB['SITE_TYPE_LDE_DESC'][0]?></td>		
			</tr>
			<tr>
				<td class="param_title">Easting</td>
				<td><?=$resB['SIT_X_COORDINATE'][0]?></td>		
			</tr>
			<tr>
				<td class="param_title">Milestone</td>
				<td><?=$resB['LATESTMILESTONE'][0]?></td>
			</tr>
			<tr>
				<td class="param_title">Northing</td>
				<td><?=$resB['SIT_Y_COORDINATE'][0]?></td>		
			</tr>
			<tr>
				<td class="param_title">Antenna height</td>
				<td><?=$HEIGHTB?></td>
			</tr>
			<tr>
				<td class="param_title" valign="top">Address</td>
				<td><?=$SIT_ADDRESSB?></td>
			</tr>
			<tr>
				<td class="param_title">Postcode</td>
				<td><?=$POSTCODEB?></td>
			</tr>
			</table>
		</td>	
	</tr>
	</table>
</div>

<br>
<div class="radio_print"><font size=18>A</font> <b>END LOS SURVEY REPORT</b> <font size=24>|</font> From <?=$SITEA?> To <?=$SITEB?></div>
<br>

<div class="formdata_print">	
	<table class="table">
	<tr>
		<td class="param_title">Link designer's comments</td>
		<td><?=$COMMENTSA?></td>
	</tr>
	<tr>
		<td class="param_title">Min. A end ANtenna Height required for LOS</td>
		<td><?=$MINHEIGHT_A?></td>
	</tr>
	<tr>
		<td class="param_title">Type of obstruction</td>
		<td><?=$OBSTR_TYPE_A?></td>
	</tr>
	<tr>
		<td class="param_title">Distance to obstruction</td>
		<td><?=$OBSTR_DISTANCE_A?></td>
	</tr>
	<tr>
		<td colspan=2><hr></td>
	</tr>
	<tr>
		<td colspan=2 class="param_title">Surveyor Comments</td>
	</tr>
	<tr>
		<td colspan=2><?=$SURVEYOR_COMMENTS_A?></td>
	</tr>
	<tr>
		<td colspan=2><hr></td>
	</tr>
	<tr>
		<td colspan=2>
			<table>
			<tr>
				<td class="param_title">
				<input type="checkbox" name="SKETCH" <?=$SKETCH_A_check?> value="1"> Roof Sketch / SIte Sketch<br>
				<input type="checkbox" name="LOCATION" <?=$LOCATION_A_check?> value="1"> Survey Location Shown
				</td>
				<td class="param_title">
				<input type="checkbox" name="PHOTO" <?=$PHOTO_A_check?> value="1"> LOS Photos<br>
				<input type="checkbox" name="PANORAMIC" <?=$PANORAMIC_A_check?> value="1"> Panoramic Photos
				</td>
				<td class="param_title">
				<input type="checkbox" name="SITESHARE" <?=$SITESHARE_A_check?> value="1"> Site Share<br>
				<input type="checkbox" name="COORDINATES" <?=$COORDINATES_A_check?> value="1"> Coordinates Checked
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2><hr></td>
	</tr>
	<tr>
		<td class="param_title">A end Surveyors Name</td>
		<td><?=$SURVEYOR_NAME_A?></td>
	</tr>
	<tr>
		<td class="param_title">Date of survey (dd/mm/yyyy)</td>
		<td><?=$SURVEY_DATE_A?></td>
	</tr>
	</tr>
	</table>
</div>

<br>
<div class="radio_print"><font size=18>B</font> <b>END LOS SURVEY REPORT</b> <font size=24>|</font> From <?=$SITEB?> To <?=$SITEA?></div>
<br>

<div class="formdata_print">		
	<table>
	<tr>
		<td class="param_title">Link designer's comments</td>
		<td><?=$COMMENTSB?></td>
	</tr>
	<tr>
		<td class="param_title">Min. A end ANtenna Height required for LOS</td>
		<td><?=$MINHEIGHT_B?></td>
	</tr>
	<tr>
		<td class="param_title">Type of obstruction</td>
		<td><?=$OBSTR_TYPE_B?></td>
	</tr>
	<tr>
		<td class="param_title">Distance to obstruction</td>
		<td><?=$OBSTR_DISTANCE_B?></td>
	</tr>
	<tr>
		<td colspan=2><hr></td>
	</tr>
	<tr>
		<td colspan=2 class="param_title">Surveyor Comments</td>
	</tr>
	<tr>
		<td colspan=2><?=$SURVEYOR_COMMENTS_B?></td>
	</tr>
	<tr>
		<td colspan=2><hr></td>
	</tr>
	<tr>
		<td colspan=2>
			<table>
			<tr>
				<td class="param_title">
				<input type="checkbox" name="SKETCH" <?=$SKETCH_B_check?> value="1"> Roof Sketch / SIte Sketch<br>
				<input type="checkbox" name="LOCATION" <?=$LOCATION_B_check?> value="1"> Survey Location Shown
				</td>
				<td class="param_title">
				<input type="checkbox" name="PHOTO" <?=$PHOTO_B_check?> value="1"> LOS Photos<br>
				<input type="checkbox" name="PANORAMIC" <?=$PANORAMIC_B_check?> value="1"> Panoramic Photos
				</td>
				<td class="param_title">
				<input type="checkbox" name="SITESHARE" <?=$SITESHARE_B_check?> value="1"> Site Share<br>
				<input type="checkbox" name="COORDINATES" <?=$COORDINATES_B_check?> value="1"> Coordinates Checked
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2><hr></td>
	</tr>
	<tr>
		<td class="param_title">A end Surveyors Name</td>
		<td><?=$SURVEYOR_NAME_B?></td>
	</tr>
	<tr>
		<td class="param_title">Date of survey (dd/mm/yyyy)</td>
		<td><?=$SURVEY_DATE_B?></td>
	</tr>
	</tr>
	</table>
</div>

<div class="rafpart">IMAGES</div>
<?
$filelist=getFileList($config['los_folder_abs'].$_GET['losid']);

for( $i=0; $i < count($filelist) ; $i++ ) {		
	if (($filelist[$i]['type']=="jpg" || $filelist[$i]['type']=="gif" || $filelist[$i]['type']=="png") && substr_count($filelist[$i]['name'], 'ori')==1){			
		echo "<IMG src='".$config['sitepath_url']."/infobase/files/los/".$_GET['losid']."/".$filelist[$i]['name']."'><br>";

	}
}

?>

</div>


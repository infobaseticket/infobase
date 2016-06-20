<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<script language="JavaScript">
$(document).ready(function(){
	function after_RAFdetails_save(response)  {  
		$('#modalspinner').spin(false);
		Messenger().post({
			message: response.responsedata,
			type: response.responsetype,
			showCloseButton: true
		});
	}	
	var options = {
		success: after_RAFdetails_save,
		dataType:  'json'
	};
	$('#form_txmn').submit(function(){
	    $(this).ajaxSubmit(options);
	    return false;
	});
});
</script>
<?	
$query = "Select * FROM BSDS_RAF_TXMN WHERE RAFID = '".$_POST['rafid']."'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
   OCIFreeStatement($stmt);
}

if (substr_count($_POST['actiondo'], 'BASE TXMN (RAF 1->6)')==1 || substr_count($_POST['actiondo'], 'BASE TXMN (RAF 6)')==1){
	$view_7="style='display:none;'";
}else{
	$view_7="";
}

if ($res1['HMIN'][0]==1){
	$HMIN_check="CHECKED";
}
if ($res1['TXMNGUIDES'][0]==1){
	$TXMNGUIDES_check="CHECKED";
}


if (substr_count($_POST['actiondo'], 'BASE TXMN (RAF 1->6)')==1 || substr_count($_POST['actiondo'], 'BASE TXMN (RAF 6)')==1
|| substr_count($_POST['actiondo'], 'RAF ASBUILD')==1 || substr_count($_POST['actiondo'], 'PARTNER (RAF 7)')==1  || substr_count($_POST['actiondo'], 'PARTNER ADD MISSING MS/DOCS')==1){

	if (substr_count($_POST['actiondo'], 'BASE TXMN (RAF 1->6)')==1 || substr_count($_POST['actiondo'], 'BASE TXMN (RAF 6)')==1){ 
		$updatebale_txmn="changeable";
	?>
	<form action="scripts/raf/raf_actions.php" method="post" id="form_txmn">
	<input type="hidden" name="action" value="update_txmn_raf_1_6">
	<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
<?
	}
}

?>
<div class="panel-group" id="accordion1">
 	<div class="panel panel-default">
        <div class="panel-heading <?=$changeable_1_7?><?=$changeable_1_4?>">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion1" href="#granteMicrowave">
                 1. GRANTED MICROWAVE DISHES
                </a>
            </h4>
        </div>
    	<div id="granteMicrowave" class="panel-collapse collapse">
     		<div class="panel-body">  		
				<table class="table">
				<thead>
				<tr>
					<td>&nbsp;</td>
					<td>Dish 1</td>
					<td>Dish 2</td>
					<td>Dish 3</td>
					<td>Dish 4</td>
					<td>Dish 5</td>
					<td>Dish 6</td>
					<td>Dish 7</td>
					<td>Dish 8</td>
					<td>Dish 9</td>
					<td>Dish 10</td>
					<td>Dish 11</td>
					<td>Dish 12</td>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>Bearing</td>
					<td><input type="text" name="GRANTED_BEARING1" class="form-control input-sm" placeholder="Dish1" value="<?=$res1['GRANTED_BEARING1'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING2" class="form-control input-sm" placeholder="Dish2" value="<?=$res1['GRANTED_BEARING2'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING3" class="form-control input-sm" placeholder="Dish3" value="<?=$res1['GRANTED_BEARING3'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING4" class="form-control input-sm" placeholder="Dish4" value="<?=$res1['GRANTED_BEARING4'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING5" class="form-control input-sm" placeholder="Dish5" value="<?=$res1['GRANTED_BEARING5'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING6" class="form-control input-sm" placeholder="Dish6" value="<?=$res1['GRANTED_BEARING6'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING7" class="form-control input-sm" placeholder="Dish7" value="<?=$res1['GRANTED_BEARING7'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING8" class="form-control input-sm" placeholder="Dish8" value="<?=$res1['GRANTED_BEARING8'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING9" class="form-control input-sm" placeholder="Dish9" value="<?=$res1['GRANTED_BEARING9'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING10" class="form-control input-sm" placeholder="Dish10" value="<?=$res1['GRANTED_BEARING10'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING11" class="form-control input-sm" placeholder="Dish11" value="<?=$res1['GRANTED_BEARING11'][0]?>"></td>
					<td><input type="text" name="GRANTED_BEARING12" class="form-control input-sm" placeholder="Dish12" value="<?=$res1['GRANTED_BEARING12'][0]?>"></td>
				</tr>
				<tr>
					<td>Height</td>
					<td><input type="text" name="GRANTED_HEIGHT1" class="form-control input-sm" placeholder="Dish1" value="<?=$res1['GRANTED_HEIGHT1'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT2" class="form-control input-sm" placeholder="Dish2" value="<?=$res1['GRANTED_HEIGHT2'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT3" class="form-control input-sm" placeholder="Dish3" value="<?=$res1['GRANTED_HEIGHT3'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT4" class="form-control input-sm" placeholder="Dish4" value="<?=$res1['GRANTED_HEIGHT4'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT5" class="form-control input-sm" placeholder="Dish5" value="<?=$res1['GRANTED_HEIGHT5'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT6" class="form-control input-sm" placeholder="Dish6" value="<?=$res1['GRANTED_HEIGHT6'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT7" class="form-control input-sm" placeholder="Dish7" value="<?=$res1['GRANTED_HEIGHT7'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT8" class="form-control input-sm" placeholder="Dish8" value="<?=$res1['GRANTED_HEIGHT8'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT9" class="form-control input-sm" placeholder="Dish9" value="<?=$res1['GRANTED_HEIGHT9'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT10" class="form-control input-sm" placeholder="Dish10" value="<?=$res1['GRANTED_HEIGHT10'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT11" class="form-control input-sm" placeholder="Dish11" value="<?=$res1['GRANTED_HEIGHT11'][0]?>"></td>
					<td><input type="text" name="GRANTED_HEIGHT12" class="form-control input-sm" placeholder="Dish12" value="<?=$res1['GRANTED_HEIGHT12'][0]?>"></td>
				</tr>
				<tr>
					<td>Antenna diameter</td>
					<td><input type="text" name="GRANTED_DIAMETER1" class="form-control input-sm" placeholder="Dish1" value="<?=$res1['GRANTED_DIAMETER1'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER2" class="form-control input-sm" placeholder="Dish2" value="<?=$res1['GRANTED_DIAMETER2'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER3" class="form-control input-sm" placeholder="Dish3" value="<?=$res1['GRANTED_DIAMETER3'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER4" class="form-control input-sm" placeholder="Dish4" value="<?=$res1['GRANTED_DIAMETER4'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER5" class="form-control input-sm" placeholder="Dish5" value="<?=$res1['GRANTED_DIAMETER5'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER6" class="form-control input-sm" placeholder="Dish6" value="<?=$res1['GRANTED_DIAMETER6'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER7" class="form-control input-sm" placeholder="Dish7" value="<?=$res1['GRANTED_DIAMETER7'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER8" class="form-control input-sm" placeholder="Dish8" value="<?=$res1['GRANTED_DIAMETER8'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER9" class="form-control input-sm" placeholder="Dish9" value="<?=$res1['GRANTED_DIAMETER9'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER10" class="form-control input-sm" placeholder="Dish10" value="<?=$res1['GRANTED_DIAMETER10'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER11" class="form-control input-sm" placeholder="Dish11" value="<?=$res1['GRANTED_DIAMETER11'][0]?>"></td>
					<td><input type="text" name="GRANTED_DIAMETER12" class="form-control input-sm" placeholder="Dish12" value="<?=$res1['GRANTED_DIAMETER12'][0]?>"></td>
				</tr>
				</tbody>
				</table>
		  	</div>
		</div>
	</div>

 	<div class="panel panel-default">
        <div class="panel-heading <?=$changeable_1_7?><?=$changeable_1_4?>">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion1" href="#addtionalMicrowave">
                 2. ADDITIONAL MICROWAVE DISHES
                </a>
            </h4>
        </div>
    	<div id="addtionalMicrowave" class="panel-collapse collapse">
     		<div class="panel-body">
				<table class="table">
				<thead>
				<tr>
					<td>&nbsp;</td>
					<td>Dish 1</td>
					<td>Dish 2</td>
					<td>Dish 3</td>
					<td>Dish 4</td>
					<td>Dish 5</td>
					<td>Dish 6</td>
					<td>Dish 7</td>
					<td>Dish 8</td>
					<td>Dish 9</td>
					<td>Dish 10</td>
					<td>Dish 11</td>
					<td>Dish 12</td>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>Bearing</td>
					<td><input type="text" name="ADDITIONAL_BEARING1" class="form-control input-sm" placeholder="Dish1" value="<?=$res1['ADDITIONAL_BEARING1'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING2" class="form-control input-sm" placeholder="Dish2" value="<?=$res1['ADDITIONAL_BEARING2'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING3" class="form-control input-sm" placeholder="Dish3" value="<?=$res1['ADDITIONAL_BEARING3'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING4" class="form-control input-sm" placeholder="Dish4" value="<?=$res1['ADDITIONAL_BEARING4'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING5" class="form-control input-sm" placeholder="Dish5" value="<?=$res1['ADDITIONAL_BEARING5'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING6" class="form-control input-sm" placeholder="Dish6" value="<?=$res1['ADDITIONAL_BEARING6'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING7" class="form-control input-sm" placeholder="Dish7" value="<?=$res1['ADDITIONAL_BEARING7'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING8" class="form-control input-sm" placeholder="Dish8" value="<?=$res1['ADDITIONAL_BEARING8'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING9" class="form-control input-sm" placeholder="Dish9" value="<?=$res1['ADDITIONAL_BEARING9'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING10" class="form-control input-sm" placeholder="Dish10" value="<?=$res1['ADDITIONAL_BEARING10'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING11" class="form-control input-sm" placeholder="Dish11" value="<?=$res1['ADDITIONAL_BEARING11'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_BEARING12" class="form-control input-sm" placeholder="Dish12" value="<?=$res1['ADDITIONAL_BEARING12'][0]?>"></td>
				</tr>
				<tr>
					<td>Height</td>
					<td><input type="text" name="ADDITIONAL_HEIGHT1" class="form-control input-sm" placeholder="Dish1" value="<?=$res1['ADDITIONAL_HEIGHT1'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT2" class="form-control input-sm" placeholder="Dish2" value="<?=$res1['ADDITIONAL_HEIGHT2'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT3" class="form-control input-sm" placeholder="Dish3" value="<?=$res1['ADDITIONAL_HEIGHT3'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT4" class="form-control input-sm" placeholder="Dish4" value="<?=$res1['ADDITIONAL_HEIGHT4'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT5" class="form-control input-sm" placeholder="Dish5" value="<?=$res1['ADDITIONAL_HEIGHT5'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT6" class="form-control input-sm" placeholder="Dish6" value="<?=$res1['ADDITIONAL_HEIGHT6'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT7" class="form-control input-sm" placeholder="Dish7" value="<?=$res1['ADDITIONAL_HEIGHT7'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT8" class="form-control input-sm" placeholder="Dish8" value="<?=$res1['ADDITIONAL_HEIGHT8'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT9" class="form-control input-sm" placeholder="Dish9" value="<?=$res1['ADDITIONAL_HEIGHT9'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT10" class="form-control input-sm" placeholder="Dish10" value="<?=$res1['ADDITIONAL_HEIGHT10'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT11" class="form-control input-sm" placeholder="Dish11" value="<?=$res1['ADDITIONAL_HEIGHT11'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_HEIGHT12" class="form-control input-sm" placeholder="Dish12" value="<?=$res1['ADDITIONAL_HEIGHT12'][0]?>"></td>
				</tr>
				<tr>
					<td>Antenna diameter</td>
					<td><input type="text" name="ADDITIONAL_DIAMETER1" class="form-control input-sm" placeholder="Dish1" value="<?=$res1['ADDITIONAL_DIAMETER1'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER2" class="form-control input-sm" placeholder="Dish2" value="<?=$res1['ADDITIONAL_DIAMETER2'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER3" class="form-control input-sm" placeholder="Dish3" value="<?=$res1['ADDITIONAL_DIAMETER3'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER4" class="form-control input-sm" placeholder="Dish4" value="<?=$res1['ADDITIONAL_DIAMETER4'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER5" class="form-control input-sm" placeholder="Dish5" value="<?=$res1['ADDITIONAL_DIAMETER5'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER6" class="form-control input-sm" placeholder="Dish6" value="<?=$res1['ADDITIONAL_DIAMETER6'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER7" class="form-control input-sm" placeholder="Dish7" value="<?=$res1['ADDITIONAL_DIAMETER7'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER8" class="form-control input-sm" placeholder="Dish8" value="<?=$res1['ADDITIONAL_DIAMETER8'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER9" class="form-control input-sm" placeholder="Dish9" value="<?=$res1['ADDITIONAL_DIAMETER9'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER10" class="form-control input-sm" placeholder="Dish10" value="<?=$res1['ADDITIONAL_DIAMETER10'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER11" class="form-control input-sm" placeholder="Dish11" value="<?=$res1['ADDITIONAL_DIAMETER11'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_DIAMETER12" class="form-control input-sm" placeholder="Dish12" value="<?=$res1['ADDITIONAL_DIAMETER12'][0]?>"></td>
				</tr>
				</tbody>
				</table>
		  	</div>
		</div>
	</div>

	<div class="panel panel-default">
        <div class="panel-heading <?=$changeable_1_7?><?=$changeable_1_4?>">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion1" href="#existingCabs">
                 3. EXISTING TXMN CABINETS
                </a>
            </h4>
        </div>
    	<div id="existingCabs" class="panel-collapse collapse">
     		<div class="panel-body">
				<table class="table">
				<thead>
				<tr>
					<th>Cabinet type</th>
					<th>Amount</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><input type="text" name="EXISTING_CAB1" size="10" class="form-control input-sm" value="<?=$res1['EXISTING_CAB1'][0]?>"></td>
					<td><input type="text" name="EXISTING_AMOUNT1" size="10" class="form-control input-sm" value="<?=$res1['EXISTING_AMOUNT1'][0]?>"></td>
				</tr>
				<tr>
					<td><input type="text" name="EXISTING_CAB2" size="10" class="form-control input-sm" value="<?=$res1['EXISTING_CAB2'][0]?>"></td>
					<td><input type="text" name="EXISTING_AMOUNT2" size="10" class="form-control input-sm" value="<?=$res1['EXISTING_AMOUNT2'][0]?>"></td>
				</tr>
				</tbody>
				</table>
		  	</div>
		</div>
	</div>

	<div class="panel panel-default">
        <div class="panel-heading <?=$changeable_1_7?><?=$changeable_1_4?>">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion1" href="#addtionalCabs">
                 4. ADDITIONAL TXMN CABINETS
                </a>
            </h4>
        </div>
    	<div id="addtionalCabs" class="panel-collapse collapse">
     		<div class="panel-body">		
				<table class="table">
				<thead>
				<tr>
					<th>Cabinet type</th>
					<th>Amount</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><input type="text" name="ADDITIONAL_CAB1" size="10" class="form-control input-sm" value="<?=$res1['ADDITIONAL_CAB1'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_AMOUNT1" size="10" class="form-control input-sm" value="<?=$res1['ADDITIONAL_AMOUNT1'][0]?>"></td>
				</tr>
				<tr>
					<td><input type="text" name="ADDITIONAL_CAB2" size="10" class="form-control input-sm" value="<?=$res1['ADDITIONAL_CAB2'][0]?>"></td>
					<td><input type="text" name="ADDITIONAL_AMOUNT2" size="10" class="form-control input-sm" value="<?=$res1['ADDITIONAL_AMOUNT2'][0]?>"></td>
				</tr>
				</tbody>
				</table>
		  	</div>
		</div>
	</div>
	<div class="panel panel-default">
        <div class="panel-heading <?=$changeable_1_7?><?=$changeable_1_4?>">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion1" href="#checklist">
                 5. CHECKLIST
                </a>
            </h4>
        </div>
    	<div id="checklist" class="panel-collapse collapse">
     		<div class="panel-body">			
				<table class="table">
				<tbody>
				<tr>
					<td><INPUT TYPE='checkbox' NAME="HMIN" VALUE="1" <?=$HMIN_check?>></td>
					<td>Hmin for MW dishes: <INPUT TYPE="text" NAME="HMINDISH" size="5" class="form-control input-sm" VALUE="<?=$res1['HMINDISH'][0]?>"></td>
				</tr>
				<tr>
					<td><INPUT TYPE=checkbox NAME="TXMNGUIDES" VALUE="1" <?=$TXMNGUIDES_check?>></td>
					<td>TX Guidlines compliant</td>
				</tr>
				</tbody>
				</table>
		  	</div>
		</div>
	</div>

	<div class="panel panel-default">
        <div class="panel-heading <?=$changeable_1_7?><?=$changeable_1_4?>">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion1" href="#spectxmnreq">
                  6. SPECIFIC TXMN REQUIREMENTS
                </a>
            </h4>
        </div>
    	<div id="spectxmnreq" class="panel-collapse collapse">
     		<div class="panel-body">
     			<div class="form-group">
	                <label for="SPECIFIC_TXMN" class="col-sm-4 control-label">REQUIREMENTS</label>
	                <div class="col-sm-8">
	                   	<textarea class="form-control input-sm" rows="5" name="SPECIFIC_TXMN" id="SPECIFIC_TXMN"><?php echo unescape_quotes($res1['SPECIFIC_TXMN'][0]); ?></textarea>
	                </div>
	            </div>			 		
		  	</div>
		</div>
	</div>
		<? 
	if (substr_count($_POST['actiondo'], 'BASE TXMN (RAF 1->6)')==1
	&& (substr_count($guard_groups, 'Base_TXMN')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	<input type="submit" class="btn btn-default" value="SAVE TXMN CHANGES 1->6">
	</form>
	<?
	}else if ((substr_count($_POST['actiondo'], 'BASE TXMN (RAF 6)')==1 || substr_count($_POST['actiondo'], 'RAF ASBUILD')==1)
	&& (substr_count($guard_groups, 'Base_TXMN')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	<input type="submit" class="btn btn-default" value="SAVE TXMN CHANGES 6">
	</form>
	<?
	}

	if (substr_count($_POST['actiondo'], 'BASE TXMN (RAF 7)')==1  || substr_count($_POST['actiondo'], 'RAF ASBUILD')==1 || substr_count($_POST['actiondo'], 'PARTNER ADD MISSING MS/DOCS')==1){ 
	$updatebale_txmn7="changeable";
	?>
	<form action="scripts/raf/raf_actions.php" method="post" id="form_txmn">
	<input type="hidden" name="action" value="update_txmn_raf_7">

	<div class="panel panel-default">
        <div class="panel-heading <?=$changeable_1_7?><?=$changeable_1_4?>">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion1" href="#budgetinfo">
                  7. BUDGET INFO
                </a>
            </h4>
        </div>
    	<div id="budgetinfo" class="panel-collapse collapse">
     		<div class="panel-body">  
	     		<div class="form-group">
		            <label for="BUDGET" class="col-sm-4 control-label">BUDGET</label>
		            <div class="col-sm-8">
		                <textarea class="form-control input-sm" rows="5" name="BUDGET" id="BUDGET"><?php echo unescape_quotes($res1['BUDGET'][0]); ?></textarea>
		            </div>			 		
			  	</div>		
		  	</div>
		</div>
	</div>

		<?
		if (substr_count($_POST['actiondo'], 'BASE TXMN (RAF 7)')==1
		&& (substr_count($guard_groups, 'Base_TXMN')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
		<input type="submit"  class="btn btn-default" value="SAVE TXMN CHANGES 7">
		</form>
		<?
		}
	}
	?>
</div>






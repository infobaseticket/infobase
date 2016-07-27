<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query = "Select RAFID,CON_PARTNER,ACQ_PARTNER,TYPE,SITEID, BUDGET_ACQ, BUDGET_CON FROM BSDS_RAFV2 WHERE RAFID = '".$_POST['rafid']."'";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
}	

if (substr($res2['SITEID'][0],0,2)=='MT' or substr($res2['SITEID'][0],0,2)=='CT'){
	$extra="_".substr($res2['SITEID'][0],0,2);
}

if (substr_count($guard_groups, 'Benchmark')==1 && $res2['ACQ_PARTNER'][0]!='BENCHMARK' && $res2['CON_PARTNER'][0]!='BENCHMARK'){
	echo '<div class="alert alert-danger" role="alert"><b>The partner is not set to Benchmark!<br>Please contact Base Delivery if you think this is not correct!</b></div>';
	die;
} 
if (substr_count($guard_groups, 'TechM')==1 && $res2['ACQ_PARTNER'][0]!='TECHM' && $res2['ACQ_PARTNER'][0]!='ALU' && $res2['ACQ_PARTNER'][0]!='TECHM FOR BASE OPS' && $res2['CON_PARTNER'][0]!='TECHM'){
	echo '<div class="alert alert-danger" role="alert"><b>The partner is not set to TechM!<br>Please contact Base Delivery if you think this is not correct!</b></div>';
	die;
} 

if ($res2['ACQ_PARTNER'][0]!='TECHM' && $res2['ACQ_PARTNER'][0]!='BENCHMARK' && substr_count($_POST['actiondo'], 'PARTNER COF (RAF1)')==1){
	echo '<div class="alert alert-danger" role="alert"><b>The ACQ (acquisition partner) is not set to TechM or Benchmark but to '.$res2['CON_PARTNER'][0].'!<br>Please contact Base Delivery!</b></div>';
}else if ($res2['CON_PARTNER'][0]!='TECHM' && $res2['CON_PARTNER'][0]!='TECHM FOR BASE OPS' && $res2['CON_PARTNER'][0]!='BENCHMARK' && substr_count($_POST['actiondo'], 'PARTNER COF (RAF2)')==1){
	echo '<div class="alert alert-danger" role="alert"><b>The CON (construction partner) is not set to TechM or Benchmark but to '.$res2['CON_PARTNER'][0].'!<br>Please contact Base Delivery!</b></div>';
}else{
	?>
	<script language="JavaScript">
	$(document).ready(function(){

		$('.materialcode').change(function () {
			var type_val=$(this).val();
			var acqcon=$(this).data('acqcon');
			var rafid=$(this).data('rafid');
			if (type_val.indexOf('BOQ')!=-1){	
				$('.BOQ_AMOUNT_'+acqcon).show('fast');
			}else{
				$('.BOQ_AMOUNT_'+acqcon).hide('fast');
			}
		});

	});
	</script>
	<?php

	if ($res2['CON_PARTNER'][0]=='TECHM' or ($res2['ACQ_PARTNER'][0]=="TECHM" && $res2['CON_PARTNER'][0]=="NOT OK")){
		$like="TM";
	}elseif ($res2['CON_PARTNER'][0]=='BENCHMARK' or ($res2['ACQ_PARTNER'][0]=="BENCHMARK" && $res2['CON_PARTNER'][0]=="NOT OK")){
		$like="BM";
	}
	if ($res2['TYPE'][0]=='Adding UMTS900 to existing UMTS2100'){
		$type='ADDING U9 to EXIST U21';
	}else{
		$type=$res2['TYPE'][0];
	}
	$query = "select MATERIAL,MATERIAL_DESCRIPTION,ACQ_CON from COF_MASTERFILE WHERE MATERIAL LIKE '".$like."%' AND ".str_replace(" ", "_", strtoupper($type)).$extra."='YES'";
	$start=substr($res2['SITEID'][0], 0,2);
	if ($start=="BW" or $start=="HT" or $start=="NR" or $start=="LX" or $start=="LG"){
		$query.=" AND MATERIAL NOT LIKE '%FL' AND MATERIAL NOT LIKE '%BX'";
	}else if($start=="WV" or $start=="OV" or $start=="AN" or $start=="LI" or $start=="VB"){
		$query.=" AND MATERIAL NOT LIKE '%WL' AND MATERIAL NOT LIKE '%BX'";
	}else if ($start=="BX"){
		$query.=" AND MATERIAL NOT LIKE '%FL' AND MATERIAL NOT LIKE '%WL'";
	}
	//echo $query;	
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
	    die_silently($conn_Infobase, $error_str);
	    exit;
	} else {
	    OCIFreeStatement($stmt);
	    $amount_of_MATERIAL=count($res1['MATERIAL']);
	   	//echo $amount_of_MATERIAL;
	    for ($i = 0; $i <$amount_of_MATERIAL; $i++) { 
	    	if ($res1['ACQ_CON'][$i]=="ACQ"){
	    		$ACQ.="<option value='".$res1['MATERIAL'][$i]."'>".$res1['MATERIAL_DESCRIPTION'][$i]."</option>";
	        }else if ($res1['ACQ_CON'][$i]=="CON"){
	        	$CON.="<option value='".$res1['MATERIAL'][$i]."'>".$res1['MATERIAL_DESCRIPTION'][$i]."</option>";
	    	} 
	    }
	}

	$query="SELECT * FROM BSDS_RAF_COF COF LEFT JOIN COF_MASTERFILE MA on COF.MATERIAL_CODE=MA.MATERIAL AND COF.ACQCON=MA.ACQ_CON WHERE RAFID='".$_POST['rafid']."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt){
	    die_silently($conn_Infobase, $error_str);
	    exit;
	} else {
	    OCIFreeStatement($stmt);
	    $amount_of_COF=count($res1['MATERIAL_CODE']);
	    //echo $amount_of_COF;
	    for ($i = 0; $i <$amount_of_COF; $i++){ 
	    	if ($res1['ACQCON'][$i]=="CON"){

	    		$CON_COF.="<tr id='material_".$_POST['rafid'].$res1['MATERIAL_CODE'][$i].$res1['ACQCON'][$i]."'><td>".$res1['MATERIAL_CODE'][$i]."</td>
	    		<td>".$res1['MATERIAL_DESCRIPTION'][$i]."</td>
	    		<td>".$res1['SPRICE'][$i]."</td>";
	    		if(substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF1)')==1  || substr_count($_POST['actiondo'], 'PARTNER COF (RAF1)')==1 || substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF2)')==1  || substr_count($_POST['actiondo'], 'PARTNER COF (RAF2)')==1){
	    			$CON_COF.="<td><button type='button' class='btn btn-xs btn-primary deletematerial' data-rafid='".$_POST['rafid']."' data-material='".$res1['MATERIAL_CODE'][$i]."' data-acqcon='".$res1['ACQCON'][$i]."'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></td></tr>";
	    		}else{
	    			$CON_COF.="<td>&nbsp;</td>";
	    		}
	    	}else{
	    		$ACQ_COF.="<tr id='material_".$_POST['rafid'].$res1['MATERIAL_CODE'][$i].$res1['ACQCON'][$i]."'><td>".$res1['MATERIAL_CODE'][$i]."</td>
	    		<td>".$res1['MATERIAL_DESCRIPTION'][$i]."</td>
	    		<td>".$res1['SPRICE'][$i]."</td>";
	    		if(substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF1)')==1  || substr_count($_POST['actiondo'], 'PARTNER COF (RAF1)')==1 || substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF2)')==1  || substr_count($_POST['actiondo'], 'PARTNER COF (RAF2)')==1){
	    			$ACQ_COF.="<td><button type='button' class='btn btn-xs btn-primary deletematerial' data-rafid='".$_POST['rafid']."' data-material='".$res1['MATERIAL_CODE'][$i]."' data-acqcon='".$res1['ACQCON'][$i]."'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></button></td></tr>";
	    		}else{
	    			$ACQ_COF.="<td>&nbsp;</td>";
	    		}
	    	}
	    }
	}

	?>
	<div class="panel-group" id="accordion">
		<?php
		if (substr_count($_POST['actiondo'], 'PARTNER COF (RAF1)')==1 or substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF1)')==1)
		{
			$changeable_1="changeable";
			$changeable="in";
		}else{
			$changeable="";
		}
		?>
		<div class="panel panel-default">
	        <div class="panel-heading <?=$changeable_1?>">
	          <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#acq">
	             1. ACQUISITON (<?=$res2['TYPE'][0]?>)
	            </a>
	          </h4>
	        </div>
	        <div id="acq" class="panel-collapse collapse <?=$changeable?>">
	        	<div class="panel-body">
	   
	        		<?php if (substr_count($_POST['actiondo'], 'PARTNER COF (RAF1)')==1 or substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF1)')==1){ ?>
					
						<form action="scripts/raf/raf_actions.php" method="post" id="form_<?=$_POST['rafid']?>BudgetACQ" class="form-horizontal">
						<input type="hidden" name="action" value="update_budget">
						<input type="hidden" name="ACQCON" value="ACQ">
						<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">

							<div class="row">
						        <label for="inputBUDGETACQ" class="col-sm-2 col-sm-offset-1 control-label">BUDGET ACQ</label>
						        <div class="col-sm-4">
						            <input type="text" name="budget_acq" maxlength="20" id="inputBUDGETACQ" class="form-control" value="<?=$res2['BUDGET_ACQ'][0]?>" placeholder="RTN">
						        </div>
						        <div class="col-sm-3">
							   	 	<button type="button" class="btn btn-info saveBUDGET" data-acqcon="ACQ" data-rafid="<?=$_POST['rafid']?>">SAVE</button>
								</div>
								<div class="col-sm-2">
								</div>
						    </div>
						</form>
						<hr>
						<form action="scripts/raf/raf_actions.php" method="post" id="form_<?=$_POST['rafid']?>AddACQ" class="form-horizontal">
						<input type="hidden" name="action" value="update_cof">
						<input type="hidden" name="ACQCON" value="ACQ">
						<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">

							<div class="row">
					    		<label for="type" class="col-sm-2 control-label">SERVICE CODE</label>
						    	<div class="col-sm-5">
							   	 	<select name="MATERIAL_CODE" id="materialcodeACQ<?=$_POST['rafid']?>" data-acqcon="ACQ" data-rafid="<?=$_POST['rafid']?>" class="form-control materialcode">
							   	 	<option>Select service</option>
									<?=$ACQ?>
									</select>
								</div>

								<div class="col-sm-2 BOQ_AMOUNT_ACQ" id="BOQ_AMOUNT_ACQ<?=$_POST['rafid']?>" style="display:none;">
									<input type="text" name="BOQ_AMOUNT" class="form-control BOQ_AMOUNT_ACQ"  placeholder="Price" style="display:none;">
								</div>

								<div class="col-sm-3">
							   	 	<button type="button" class="btn btn-info saveCOF" data-acqcon="ACQ" data-rafid="<?=$_POST['rafid']?>"><span class="glyphicon glyphicon-plus" title="Add service" aria-hidden="true"></span></button>
								</div>

							</div>
						</form>
						<hr>
					<?php } ?>	
				
					<table class="table table-striped">
					<thead>
						<th>MATERIAL CODE</th>
						<th>DESCRIPTION</th>
						<th>PRICE</th>
					</thead>
					<tbody id='<?=$_POST['rafid']?>_COFACQtable'>
						<?=$ACQ_COF?>
					</tbody>
					</table>

				</div>
			</div>
		</div>
		<?php
	    if (substr_count($_POST['actiondo'], 'PARTNER COF (RAF2)')==1 or substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF2)')==1){
			$changeable_2="changeable";
			$changeable="in";
		
		}else{
			$changeable="";
			if (substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF2)')==1){
				$changeable="in";
			}
		}

		?>
		<div class="panel panel-default">
	        <div class="panel-heading <?=$changeable_2?>">
	          <h4 class="panel-title">
	            <a data-toggle="collapse" data-parent="#accordion" href="#con">
	             2. CONSTRUCTION  (<?=$res2['TYPE'][0]?>)
	            </a>
	          </h4>
	        </div>
	        <div id="con" class="panel-collapse collapse <?=$changeable?>">
	 		  	<div class="panel-body">
	 		  	<?php if (substr_count($_POST['actiondo'], 'PARTNER COF (RAF2)')==1 or substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF2)')==1){ ?>

	 		  		<form action="scripts/raf/raf_actions.php" method="post" id="form_<?=$_POST['rafid']?>BudgetCON" class="form-horizontal">
					<input type="hidden" name="action" value="update_budget">
					<input type="hidden" name="ACQCON" value="CON">
					<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
				<?php } ?>
		 		  	<div class="row">
					        <label for="inputBUDGETACQ" class="col-sm-2 col-sm-offset-1 control-label">BUDGET CON</label>
					        <div class="col-sm-4">
					            <input type="text" name="budget_con" maxlength="20" id="inputBUDGETCON" class="form-control" value="<?=$res2['BUDGET_CON'][0]?>" placeholder="RTN">
					        </div>
					<?php if (substr_count($_POST['actiondo'], 'PARTNER COF (RAF2)')==1 or substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF2)')==1){ ?>

					        <div class="col-sm-3">
						   	 	<button type="button" class="btn btn-info saveBUDGET" data-acqcon="CON" data-rafid="<?=$_POST['rafid']?>">SAVE</button>
							</div>
							<div class="col-sm-2">
							</div>
					    </div>
				
				    </form>
				<?php } ?>
				    <hr>
				<?php if (substr_count($_POST['actiondo'], 'PARTNER COF (RAF2)')==1 or substr_count($_POST['actiondo'], 'BASE PM/TS COF (RAF2)')==1){ ?>

					<form action="scripts/raf/raf_actions.php" method="post" id="form_<?=$_POST['rafid']?>AddCON" class="form-horizontal">
					<input type="hidden" name="action" value="update_cof">
					<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
					<input type="hidden" name="ACQCON" value="CON">
					<div class="row">
				    	<label for="type" class="col-sm-2 control-label">SERVICE CODE</label>
				    	<div class="col-sm-3">
					   	 	<select name="MATERIAL_CODE" data-acqcon="CON" data-rafid="<?=$_POST['rafid']?>" id="materialcodeCON<?=$_POST['rafid']?>" class="form-control materialcode">
					   	 	<option>Select service</option>
							<?=$CON?>
							</select>
						</div>
						<div class="col-sm-2 BOQ_AMOUNT_CON" id="BOQ_AMOUNT_CON<?=$_POST['rafid']?>" style="display:none;">
							<input type="text" name="BOQ_AMOUNT" class="form-control BOQ_AMOUNT_CON"  placeholder="Price" style="display:none;">
						</div>

						<div class="col-sm-3">
					   	 	<button type="button" class="btn btn-info saveCOF" data-acqcon="CON" data-rafid="<?=$_POST['rafid']?>"><span class="glyphicon glyphicon-plus" title="Add service" aria-hidden="true"></span></button>
						</div>
					</div>
					</form>
				<?php } ?>
					<hr>

					<table class="table table-striped">
					<thead>
						<th>MATERIAL CODE</th>
						<th>DESCRIPTION</th>
						<th>PRICE</th>
						<th>DELETE</th>
					</thead>
					<tbody id='<?=$_POST['rafid']?>_COFCONtable'>
						<?=$CON_COF?>
					</tbody>
					</table>
				
				</div>
				
			</div>
		</div>
		
	</div>
<?php
}
?>
<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Base_RF,Base_delivery,Partner,Base_TXMN,Base_other,Administrators","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$regions=array(
    0=>"NA",
	1=>"AN",
	2=>"BW",
	3=>"BX",
	4=>"HT",
	5=>"LG",
	6=>"LI",
	7=>"LX",
	8=>"NR",
	9=>"OV",
	10=>"VB",
	11=>"WV",
	12=>"MT",
	13=>"CT",	
);
?>
<div class="panel panel-primary" id="reportfilter">
    <div class="panel-heading">
        <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#filter">
            SET FILTER <span class="glyphicon glyphicon-eye-open"></span>
        </a>
        </h4>
    </div>
    <div id="filter" class="panel-collapse collapse in"  style="overflow-y:auto;max-height:630px;">
        <div class="panel-body">
        	<div class="well">Please not that if an option in <b>'actions required by'</b> is not available, this means there are no actions open</div>
	        <form action="scripts/raf/raf.php" name="fm" method="post" id="RafReportForm" role="form" class="form-horizontal" role="form">
			<input type="hidden" name="viewtype" value="report">
			<input type="hidden" name="view" value="LOS">
				<div class="form-group">
					<label for="Actionrequiredby" class="col-sm-4 control-label">Actions required by</label>
					<div class="col-sm-8">	
						<select name="actionby" id="Actionrequiredby" class="form-control">
						<option value=''>NA</option>
						<?php 
						if ($_POST['report']!=''){
							echo "<option selected value='".$_POST['report']."'>".str_replace("_", " ", $_POST['report'])."</option>";
						}

						if (substr_count($guard_groups, 'Partner')!=1){
							echo "<option value='Base_TXMN'>TRANSMISSION</option>";
							echo "<option value='Base_RF'>RADIO</option>";
							echo "<option value='Base_delivery'>DELIVERY</option>";
							echo "<option disabled value='Partner'>PARTNER</option>";
						}

		                $query = "select DISTINCT(ACTION),ACTION_BY from VW_RAF_ACTIONS_BY ORDER BY ACTION_BY,'UNID'";
		                $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
		                if (!$stmt) {
		                    die_silently($conn_Infobase, $error_str);
		                    exit;
		                } else {
		                  OCIFreeStatement($stmt);
		                  $amount_of_TYPE=count($res['ACTION']);
		                  for ($i = 0; $i <$amount_of_TYPE; $i++){
		                  	if (substr_count($guard_groups, 'Partner')==1 &&  $res['ACTION'][$i]=="PARTNER"){
		                  		echo "<option value='".$res['ACTION'][$i]."'>".$res['ACTION'][$i]."</option>";
		                  	}elseif (substr_count($guard_groups, 'Partner')!=1){
		                  		echo "<option value='".$res['ACTION'][$i]."'>".$res['ACTION_BY'][$i].": ".str_replace("_", " ", $res['ACTION'][$i])."</option>";
		                  	}
		                  } 
		                }
		                ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="Region" class="col-sm-4 control-label">Region</label>
					<div class="col-sm-8">	
						<select name="region" id="Region" class="form-control">
						<?php
						if ($_POST['region']!=''){
							echo "<option selected value='".$_POST['region']."'>".$_POST['region']."</option>";
						}
						foreach ($regions as $key => $value){
						?>
						<option><?=$value?></option>
						<?}?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="type" class="col-sm-4 control-label">RAF type</label>
					<div class="col-sm-8">
						<select name="type" id="type" class="form-control col-sm-10" multiple>
						<?php 
		                $query="SELECT RAFTYPE FROM RAF_PROCESS_STEPS order by RAFTYPE";
					    //echo $query;
					    $stmtPR= parse_exec_fetch($conn_Infobase, $query, $error_str, $resPR);
					    if (!$stmtPR){
					        die_silently($conn_Infobase, $error_str);
					        exit;
					    } else {
					        OCIFreeStatement($stmtPR);
					        $amount_of_TYPES=count($resPR['RAFTYPE']);
					    }
					    for ($k = 0; $k <$amount_of_TYPES; $k++){ 
					    	if (substr_count($_POST['raftype'], $resPR['RAFTYPE'][$k])!=0){
					    		$selected="selected";
					    	}else{
					    		$selected=""; 
					    	}
					      	echo "<option ".$selected." value='".$resPR['RAFTYPE'][$k]."''>".$resPR['RAFTYPE'][$k]."</option>"; 
					    }
		                ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="event" class="col-sm-4 control-label">Event</label>
					<div class="col-sm-8">
						<select name="event" id="event" class="form-control col-sm-10">
							<option value=''>NA</option>
						<?php 
		                $query = "Select DISTINCT(EVENT || ' '||SUBSTR(STARTDATE,-4)) AS EVENT FROM EVENTCAL ORDER BY EVENT";
						$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
						if (!$stmt2) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt2);
							$amount_of_EVENTS=count($res2['EVENT']);
						}
					    for ($k = 0; $k <$amount_of_EVENTS; $k++){ 
					    	if (substr_count($_POST['event'], $res2['EVENT'][$k])!=0){
					    		$selected="selected";
					    	}else{
					    		$selected=""; 
					    	}
					      	echo "<option ".$selected." value='".$res2['EVENT'][$k]."'>".$res2['EVENT'][$k]."</option>"; 
					    }
		                ?>
						</select>
					</div>
				</div>	
				<div class="form-group">
					<label for="event" class="col-sm-4 control-label">Cluster</label>
					<div class="col-sm-8">
					  <select class="form-control" id="cluster" name="cluster">
					  <option value=''>NA</option>
					  <?php
					  $query="SELECT DISTINCT(CLUSTERN || CLUSTERNUM) AS CLUST FROM BSDS_RAF_RADIO WHERE CLUSTERN IS NOT NULL ORDER BY CLUSTERN || CLUSTERNUM";
					  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
					  if (!$stmt) {
					      die_silently($conn_Infobase, $error_str);
					      exit;
					  } else {
					      OCIFreeStatement($stmt);
					      $amount_CLUSTER=count($res1['CLUST']);
					  }
					  
					  for ($i = 0; $i <$amount_CLUSTER; $i++){ 
					  	echo "---".$_POST['cluster'];
					  	if (substr_count($_POST['cluster'], $res1['CLUST'][$i])!=0){
					    		$selected="selected";
					    	}else{
					    		$selected=""; 
					    	}
					    echo "<option ".$selected.">".$res1['CLUST'][$i]."</option>";
					  }
					  ?>
					  </select>
					</div>
				 </div>			
				<div class="form-group">
					<label for="rfinfo" class="col-sm-4 control-label">RF INFO</label>
					<div class="col-sm-8">
						<select name="rfinfo" id="rfinfo" class="form-control">
						<option>NA</option>
						<?php 
		                $query = "select DISTINCT(RFINFO) from BSDS_RAFV2 WHERE RFINFO IS NOT NULL ORDER BY RFINFO";
		                $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
		                if (!$stmt) {
		                    die_silently($conn_Infobase, $error_str);
		                    exit;
		                } else {
		                  OCIFreeStatement($stmt);
		                  $amount_of_RFINFO=count($res['RFINFO']);
		                  for ($i = 0; $i <$amount_of_RFINFO; $i++) { 
		                    echo "<option value='".$res['RFINFO'][$i]."'>".$res['RFINFO'][$i]."</option>";
		                  } 
		                }
		                ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="commercial" class="col-sm-4 control-label">COMMERCIAL</label>
					<div class="col-sm-8">	
						<select name="commercial" id="commercial" class="form-control">
						<option>NA</option>
		                <?php 
		                $query = "select DISTINCT(COMMERCIAL) from BSDS_RAFV2 WHERE COMMERCIAL IS NOT NULL ORDER BY COMMERCIAL";
		                $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
		                if (!$stmt) {
		                    die_silently($conn_Infobase, $error_str);
		                    exit;
		                } else {
		                  OCIFreeStatement($stmt);
		                  $amount_of_COMMERCIAL=count($res['COMMERCIAL']);
		                  for ($i = 0; $i <$amount_of_COMMERCIAL; $i++) { 
		                    echo "<option value='".$res['COMMERCIAL'][$i]."'>".$res['COMMERCIAL'][$i]."</option>";
		                  } 
		                }
		                ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="allocated" class="col-sm-4 control-label">Allocated to</label>
					<div class="col-sm-8">	
						<select name="allocated" id="allocated" class="form-control">
						<?php
						if ($_POST['partner']!=''){
							if ($_POST['partner']!='ALL'){
								$partner=$_POST['partner'];
							}else{
								$partner="";
							}
							echo "<option selected value='".$partner."'>".$_POST['partner']."</option>";
						}
						if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
						<option value=''>ALL</option>
						<? } ?>
						<? if (substr_count($guard_groups, 'Alcatel')==1 || substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
							<option>ALU</option>
						<? } ?>

						<? if (substr_count($guard_groups, 'Benchmark')==1 || substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
								<option>BENCHMARK</option>
						<? } ?>
						<? if (substr_count($guard_groups, 'TechM')==1 || substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
								<option>TECHM</option>
						<? } ?>
						<? if (substr_count($guard_groups, 'ZTE')==1 || substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
								<option>M4C</option>
						<? } ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="Order" class="col-sm-4 control-label">Order by</label>
					<div class="col-sm-4">
							<select name="orderby" id="Order" class="form-control ">
							<option value="SITEID">SITE ID</option>
							<option value="RAFID">RAF ID</option>
							<option value="CREATION_DATE">Creation Date</option>
							<option value="NET1_LINK">NET1 LIK</option>
							</select>
					</div>
					<div class="col-md-4">
							<select name="order" class="form-control">
							<option value="ASC">Ascending</option>
							<option value="DESC">Descending</option>
							</select>
					</div>	
				</div>
				<!--
				<div class="form-group">
				    <div class="col-md-offset-4 col-md-8">
				      <div class="checkbox">
				        <label>
							<input type="checkbox" name="build" value="1"> Display as build
				        </label>
				      </div>
				    </div>
				</div>-->
				<div class="form-group">
				    <div class="col-md-offset-4 col-md-8">
				      <div class="checkbox">
				        <label>
							<input type="checkbox" name="deleted" value="1"> Display deleted & locked
				        </label>
				      </div>
				    </div>
				</div>
			</div>	
			<div class="form-group">
				<div class="col-md-4 col-md-offset-4">		
				<input type="submit" value="DISPLAY" id="displayRafform" class="btn btn-primary">
				<br><br>
				</div>
			</div>

			</form>
		</div>
    </div>
</div>
<br><br>
<div id="reportoutput"></div>



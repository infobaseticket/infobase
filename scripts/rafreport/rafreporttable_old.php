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
    <div id="filter" class="panel-collapse collapse in">
        <div class="panel-body">
	        <form action="scripts/raf/raf.php" name="fm" method="post" id="RafReportForm" role="form" class="form-horizontal" role="form">
			<input type="hidden" name="viewtype" value="report">
			<input type="hidden" name="view" value="LOS">
				<div class="form-group">
					<label for="Actionrequiredby" class="col-sm-4 control-label">Actions required by</label>
					<div class="col-sm-8">	
						<select name="actionby" id="Actionrequiredby" class="form-control">
						<? if (substr_count($guard_groups, 'Partner')!=1){ ?>
						<option>NA</option>
						<option>Base Other</option>
						<option>Base RF</option>
						<option>Base RF - INPUT</option>
						<option>Base RF - INPUT REJECTED</option>
						<option>Base RF - BCS</option>
						<option value='Base RF - BCS NET1'>Base RF - BCS NET1 (AUTO FIELD)</option>
						<option value="Base RF - FUNDING">Base RF - FUNDING WITH BUDGET</option>
						<option value="Base RF - FUNDING REJECTED">Base RF - FUNDING REJECTED</option>
						<option value="Base RF - FUNDING WITHOUT">Base RF - FUND WO BUDGET, AWAIT LTE BUDG., ON HOLD</option>
						<option value="Base RF - FUNDING BLOCKED">Base RF - BLOCKED FOR FUNDING</option>
						<option>Base RF - PAC</option>
						<option>Base TXMN</option>
						<option>Base TXMN - INPUT</option>
						<option>Base TXMN - BCS</option>
						<option>Base TXMN - ACQUISITION APPROVAL</option>
						<option>Base TXMN - ACQUISITION APPROVAL CONDITIONAL</option>
						<!--<option>Base TXMN - LOS CREATION</option>-->
						<option>Base Delivery</option>
						<option>Base Delivery - NET1 LINK</option>
						<option value="Base Delivery - RAF ACQUIRED">Base Delivery - RAF/NET1 ACQUIRED</option>
						<option>Base Delivery - PAC DATE</option>
						<option>Base Delivery - FAC DATE</option>
						<option>Base Delivery - Locked RAF</option>
						<option>Base Delivery - FUND DATE</option>
						<option>Base Delivery - MISSING PO ACQ</option>
						<option>Base Delivery - MISSING PO CON</option>
						<option>Base Delivery - ACQ PARTNER</option>
						<option>Base Delivery - CON PARTNER</option>
						<option>Base Delivery - COF ACQ</option>
						<option>Base Delivery - COF CON PM</option>
						<option>Base Delivery - COF CON TS</option>

						<? } ?>
						<option value="Partner - INPUT">ROLL-OUT Partner - PARTNER INPUT</option>
						<option value="Partner - INPUT REJECTED">ROLL-OUT Partner - PARTNER INPUT REJECTED</option>
						<option value="Partner - A304">ROLL-OUT Partner - PARTNER A304 NET1</option>
						<option value="Partner - LBP">ROLL-OUT Partner - L&BP OK</option>
						<option value="Partner - ACQUIRED">ROLL-OUT Partner - PARTNER ACQUIRED</option>
						<option value="Partner - ACQUIRED REJECTED">ROLL-OUT Partner - PARTNER ACQUIRED REJECTED</option>
						<option value="Partner - SUBMIT RF PACK">ROLL-OUT Partner - PARTNER SUBMIT RF PACK</option>
						<option value="Partner - PACFAC">ROLL-OUT Partner - PAC/FAC VALIDATION REQUEST</option>
						<option value="Partner - COF ACQ">ROLL-OUT Partner - MISSING COF FOR ACQ</option>
						<option value="Partner - COF CON">ROLL-OUT Partner - MISSING COF FOR CON</option>
						<option>Base Delivery - MISSING PO ACQ</option>
						<option>Base Delivery - MISSING PO CON</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="Region" class="col-sm-4 control-label">Region</label>
					<div class="col-sm-8">	
						<select name="region" id="Region" class="form-control">
						<?
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
						<select name="type" id="type" class="form-control col-sm-10">
							<option>NA</option>
						<?php 
		                $query = "select DISTINCT(TYPE) from BSDS_RAFV2 WHERE TYPE IS NOT NULL ORDER BY TYPE";
		                $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
		                if (!$stmt) {
		                    die_silently($conn_Infobase, $error_str);
		                    exit;
		                } else {
		                  OCIFreeStatement($stmt);
		                  $amount_of_TYPE=count($res['TYPE']);
		                  for ($i = 0; $i <$amount_of_TYPE; $i++) { 
		                    echo "<option value='".$res['TYPE'][$i]."'>".$res['TYPE'][$i]."</option>";
		                  } 
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
						<? if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
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
								<option>ZTE</option>
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
				<div class="form-group">
				    <div class="col-md-offset-4 col-md-8">
				      <div class="checkbox">
				        <label>
							<input type="checkbox" name="build" value="1"> Display as build
				        </label>
				      </div>
				    </div>
				</div>
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




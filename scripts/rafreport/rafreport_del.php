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
	13=>"CT"
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
	        <form action="scripts/raf/raf.php" name="fm" method="post" id="RafReportForm"  class="form-horizontal" role="form">
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
						<option>Base RF - BCS NET1</option>
						<option value="Base RF - FUNDING">Base RF - FUNDING WITH BUDGET</option>
						<option value="Base RF - FUNDING REJECTED">Base RF - FUNDING REJECTED</option>
						<option value="Base RF - FUNDING WITHOUT">Base RF - FUND WO BUDGET, AWAIT LTE BUDG., ON HOLD</option>
						<option>Base RF - PAC</option>
						<option>Base TXMN</option>
						<option>Base TXMN - INPUT</option>
						<option>Base TXMN - ACQUISITION APPROVAL</option>
						<option>Base TXMN - ACQUISITION APPROVAL CONDITIONAL</option>
						<option>Base TXMN - LOS CREATION</option>
						<option>Base Delivery</option>
						<option>Base Delivery - NET1 LINK</option>
						<option value="Base Delivery - RAF ACQUIRED">Base Delivery - RAF/NET1 ACQUIRED</option>
						<option>Base Delivery - PAC DATE</option>
						<option>Base Delivery - Locked RAF</option>
						<option>Base Delivery - FUND DATE WITH BUDGET</option>
						<option>Base Delivery - MISSING PO ACQ</option>
						<option>Base Delivery - MISSING PO CON</option>
						<? } ?>
						<option value="Partner">ROLL-OUT Partner</option>
						<option value="Partner - INPUT">ROLL-OUT Partner - PARTNER INPUT</option>
						<option value="Partner - INPUT REJECTED">ROLL-OUT Partner - PARTNER INPUT REJECTED</option>
						<option value="Partner - LBP">ROLL-OUT Partner - L&BP OK</option>
						<option value="Partner - ACQUIRED">ROLL-OUT Partner - PARTNER ACQUIRED</option>
						<option value="Partner - ACQUIRED REJECTED">ROLL-OUT Partner - PARTNER ACQUIRED REJECTED</option>
						<option value="Partner - PAC">ROLL-OUT Partner - PARTNER PAC</option>
						<option value="Partner - PAC REJECTED">ROLL-OUT Partner - PARTNER PAC REJECTED</option>
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
							<option>New Macro</option><!-- radio -->
							<option>New Micro</option><!-- radio -->
							<option>New Indoor</option><!-- radio -->
							<option>New CTX site</option><!-- radio -->
							<option>CAB Upgrade</option><!-- radio -->
							<option>TECHNO Upgrade</option><!-- radio -->
							<option>ANT Upgrade</option><!-- radio -->
							<option>Indoor Upgrade</option>
							<option>ASC Upgrade</option><!-- radio -->
							<option>RPT Upgrade</option><!-- radio -->
							<option>New Replacement</option><!-- others + radio -->
							<option>New Mobile Truck</option><!-- radio -->
							<option>Replacement Request</option><!-- others + radio -->
							<option>Move Request</option><!-- others -->
							<option>CWK Upgrade</option><!-- others -->
							<option>Dismantling</option><!-- others -->
							<option>CTX Upgrade</option><!-- TXMN -->
							<option>SWAP Upgrade</option><!-- TXMN -->
							<option>MSH Upgrade</option><!-- Delivery -->
							<option>LTEX Upgrade (BOARD)</option>
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
						<select name="allocated" id="allocated"class="form-control">
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
					<div class="col-sm-8 col-sm-offset-4">		
						<input type="submit" value="DISPLAY" id="displayRafform" class="btn btn-primary">
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
<br>
<div id="reportoutput"></div>




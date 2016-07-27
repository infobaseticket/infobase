<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Base_RF,Base_TXMN,Base_delivery,Base_other,Base_risk,Partner,Administrators","");
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
        <a data-toggle="collapse" data-parent="#accordion" href="#filterLOS">
            SET FILTER <span class="glyphicon glyphicon-eye-open"></span>
        </a>
        </h4>
    </div>
    <div id="filterLOS" class="panel-collapse collapse in">
        <div class="panel-body" style="min-width:500px;">
			<form action="scripts/los/los.php" name="fm" method="post" id="LosReportForm"  class="form-horizontal" role="form">
				<input type="hidden" name="viewtype" value="report">
				<input type="hidden" name="view" value="LOS">
				<div class="form-group">
					<label for="Actionrequiredby" class="col-sm-4 control-label">Action required by</label>
					<div class="col-sm-8">
						<select name="actionby" id="Actionrequiredby" class="form-control">
						<option value='Partner Processing'>Partner Processing</option>
						<option value='Partner Reporting'>Partner Reporting</option>
						<option>TXMN Resulting</option>
						<option>Canceled</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="type" class="col-sm-4 control-label">LOS type</label>
					<div class="col-sm-8">
						<select name="type" id="type" class="form-control">
						<option value="">All</option>
						<option value="NB">New build</option>
						<option value="ST">Standard</option>
						<option value="RSL">RSL Project</option>
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
					<label for="allocated" class="col-sm-4 control-label">Allocated to:</label>
					<div class="col-sm-8">
						<select name="allocated" id="allocated"class="form-control">
						<? if (substr_count($guard_groups, 'Base')!=0 || substr_count($guard_groups, 'Administrators')==1){ ?>
						<option value=''>ALL</option>
						<? } ?>
						<? if (substr_count($guard_groups, 'Alcatel')!=0 || substr_count($guard_groups, 'Base')!=0 || substr_count($guard_groups, 'Administrators')==1){ ?>
							<option>ALU</option>																													
						<? } ?>

						<? if (substr_count($guard_groups, 'Benchmark')!=0 || substr_count($guard_groups, 'Base')!=0 || substr_count($guard_groups, 'Administrators')==1){ ?>
								<option>BENCHMARK</option>
						<? } ?>
						<? if (substr_count($guard_groups, 'TechM')!=0 || substr_count($guard_groups, 'Base')!=0 || substr_count($guard_groups, 'Administrators')==1){ ?>
								<option>TECHM</option>
						<? } ?>
						<? if (substr_count($guard_groups, 'ZTE')!=0 || substr_count($guard_groups, 'Base')!=0 || substr_count($guard_groups, 'Administrators')==1){ ?>
								<option>ZTE</option>
						<? } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="Order" class="col-sm-4 control-label">Order by</label>
					<div class="col-sm-8">
						<select name="orderby" id="Order" class="form-control">
						<option value="SITEA">SITE ID A</option>
						<option value="SITEB">SITE ID B</option>
						<option value="ID">LOS ID</option>
						<option value="CREATION_DATE">Creation Date</option>
						</select>
						<select name="order" class="form-control">
						<option value="ASC">Ascending</option>
						<option value="DESC">Descending</option>
						</select>
					</div>
				</div>
				
				<div class="form-group">
				    <label for="Sitenumber" class="col-sm-4 control-label">csv </label>
				    <div class="col-sm-8">
				    	<div class="checkbox">
					        <label>
					          <input name="csvreport" type="checkbox" id="csvreport" value="1"> display as csv
					        </label>
					    </div>
					</div>
				</div>
							
				<input type="submit" value="DISPLAY" id="displayLosform" class="btn btn-primary">
				</form>
        </div>
    </div>
</div>
<br><br>
<div id="LOSreportoutput"></div>

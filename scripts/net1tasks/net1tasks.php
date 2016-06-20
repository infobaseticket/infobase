<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$query1="select DISTINCT(TOR_LKP_WCO_CODE) as TOR_LKP_WCO_CODE 
from TEMPLATE_ORDERS@".$config['net1db']." WHERE TOR_LKP_WAR_CODE='NEM'
ORDER BY TOR_LKP_WCO_CODE";
$stmt1 = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt1) {
	die_silently($conn_Infobase, $error_str);
	exit;
} else {
	OCIFreeStatement($stmt1);
	for ($k=0;$k<count($res1['TOR_LKP_WCO_CODE']);$k++){
		$TOR_LKP_WCO_CODE.= "{text: '".$res1['TOR_LKP_WCO_CODE'][$k]."',value: '".$res1['TOR_LKP_WCO_CODE'][$k]."'},";
	}
}
$query2="select DISTINCT(TOR_NET_CODE) AS TOR_NET_CODE ,TOR_NET_CLASS 
from TEMPLATE_ORDERS@".$config['net1db']." WHERE TOR_LKP_WAR_CODE='ROL' AND TOR_NET_CODE!='ANY'  ORDER BY TOR_NET_CODE";
$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
if (!$stmt2) {
	die_silently($conn_Infobase, $error_str);
	exit;
} else {
	OCIFreeStatement($stmt2);
	for ($k=0;$k<count($res2['TOR_NET_CODE']);$k++){
		$TOR_NET_CODE.= "{text: '".$res2['TOR_NET_CODE'][$k]." ".$res2['TOR_NET_CLASS'][$k]."',value:'".$res2['TOR_NET_CODE'][$k].",".$res2['TOR_NET_CLASS'][$k]."'},";
	}
}
?>
<script src="javascripts/jquery.tablednd.js"></script>

<script language="javascript">
	$('#group').selectize({
	    create: false,
	    valueField: 'value',
	    labelField: 'text',
	    searchField: 'text',
	    options: [
	        {text: 'All',value:'All'},<?=$TOR_LKP_WCO_CODE?><?=$TOR_NET_CODE?>
	    ]
	});	
	$("body").on("mouseover",".editableTaskDesc",function(e){
		$(this).editable({ 
			url: 'scripts/net1tasks/net1tasks_procedures.php',
        	title: 'Enter task description',
        	rows: 5,
        	placement: 'right',
        	params: function(params) {
				params.action= "updateTaskDescr";
			    return params;
			},
		});
	});
	$("body").on("mouseover",".editableTaskOrder",function(e){
		$(this).editable({ 
			url: 'scripts/net1tasks/net1tasks_procedures.php',
        	title: 'Enter task order',
        	placement: 'left',
        	params: function(params) {
				params.action= "updateTaskOrder";
			    return params;
			},
		});
	});
</script>

<div class="panel panel-primary" id="net1taskslist">
    <div class="panel-heading">
        <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#tasklistfilter">
            SET FILTER <span class="glyphicon glyphicon-eye-open"></span>
        </a>
        </h4>
    </div>
    <div id="tasklistfilter" class="panel-collapse collapse in">
        <div class="panel-body">
	        <form action="scripts/net1tasks/net1tasks_procedures.php" name="fm" method="post" id="Net1TasksForm" role="form" class="form-horizontal" role="form">
			<input type="hidden" name="action" value="getTaskList">	
				<div class="form-group">
					<label for="type" class="col-sm-5 control-label">Select group</label>
					<div class="col-sm-7">
						<select name="group" id="group" class="form-control col-sm-10">
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="type" class="col-sm-5 control-label">Order by</label>
					<div class="col-sm-7">
						<select name="order" id="order" class="form-control col-sm-10">
							<option value='TOS_SEQUENCE'>NET1 order</option>
							<option value='TOS_TAS_CODE'>TASK order</option>
						</select>
					</div>
				</div>
				<div class="col-md-7 col-md-offset-5">		
					<input type="submit" value="DISPLAY" id="Net1TaskListform" class="btn btn-primary">
					<br><br>
				</div>
			</form>
		</div>
	</div>
</div>
<br>

<div id="TaskListoutput">
</div>
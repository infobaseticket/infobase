<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners,BASE_MP,BASE_NPF,BSDS_view","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['action']=='getTaskList'){
	$query="select * from VW_NET1_TASK_TEMPLATES";
	if ($_POST['group']!='All'){
		$group=explode(",",$_POST['group']);
		if ($group[1]==""){
			$query.=" WHERE TOR_LKP_WCO_CODE='".$_POST['group']."'";
			$grouptype=$_POST['group'];
			$groupname="";
		}else{
			$query.=" WHERE TOR_NET_CODE='".$group[0]."' AND TOR_NET_CLASS='".$group[1]."'";
			$grouptype=$group[0];
			$groupname=$group[1];
		}
		
	}
	$query.="AND TOS_TAS_CODE!='START' AND TOS_TAS_CODE!='END' ORDER BY TOR_LKP_WCO_CODE,".$_POST['order']." ASC";
	echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_NET1=count($res1['TOS_TAS_CODE']);
	}
	?>
	<br><br>
	<span class="label label-default"><?=$amount_of_NET1?> records found</span>
	<table class="table table-striped table-condensed" id="NET1TaskListTable" data-groupname="<?=$groupname?>" data-grouptype="<?=$grouptype?>">
		<thead>
			<th>GROUP</th>
			<th>TASK</th>
			<th>DESCRIPTION</th>
			<th>ORDER</th>
			<th>TEMPLATE VERSION</th>
			<th>TEMPLATE ID</th>
		</thead>
		<tbody>
	<?php
	if (substr_count($guard_groups, 'Administrators')==1){
		$editableDesc="editableTaskDesc";
		$editableOrder="editableTaskOrder";
	}else{
		$editableDesc="notEditable";
		$editableOrder="notEditable";
	}

	for ($i=0;$i<$amount_of_NET1;$i++){
	?>
		<tr id='<?=$res1['TOS_TAS_CODE'][$i]?>'>
			<td>
				<?php
				if  ($res1['TOR_LKP_WCO_CODE'][$i]!='CODE1') 
					echo $res1['TOR_LKP_WCO_CODE'][$i]." ";
				if  ($res1['TOR_NET_CODE'][$i]!='ANY') echo $res1['TOR_NET_CODE'][$i];
				echo " ";
				if  ($res1['TOR_NET_CLASS'][$i]!='ANY') echo $res1['TOR_NET_CLASS'][$i];
				?> 
			</td>
			<td><?=$res1['TOS_TAS_CODE'][$i]?></td>
			<td><a href="#" id="taskdesc" class="<?=$editableDesc?>" data-type="textarea" data-pk="<?=$res1['TOS_TAS_CODE'][$i]?>"><?=$res1['TAS_DESC'][$i]?></a></td>
			<td><a href="#" id="taskorder" class="<?=$editableOrder?>" data-type="text" data-pk="<?=$res1['TOS_TAS_CODE'][$i]?>"><?=$res1['TOS_SEQUENCE'][$i]?></a></td>
			<td><?=$res1['TOR_VERSION'][$i]?></td>
			<td><?=$res1['TOR_ID'][$i]?></td>
		</tr>
	<?php
	}
?>
		</tbody>
	</table>
	<?php
}else if ($_POST['action']=='updateTaskDescr'){
	if ($_POST['value']!='' && $_POST['pk']!=''){
		$query="UPDATE TASKS@".$config['net1db']." SET TAS_DESC='".escape_sq($_POST['value'])."' WHERE TAS_CODE='".$_POST['pk']."'";
		//echo $query;
		$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		}else{
			OCICommit($conn_Infobase);
			echo $_POST['value'];
		}
	}else{
		echo "ERROR";
	}

}else if ($_POST['action']=='updateTaskOrder'){
	$result = $_REQUEST["NET1TaskListTable"];
	foreach($result as $value) {
		echo "$value<br/>";
	}
}


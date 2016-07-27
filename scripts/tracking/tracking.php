<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$query = "select * from RAF_COMMENTS WHERE SITEID LIKE '%".strtoupper($_POST['siteID'])."%' ";

if ($_POST['rafid']){
 	$query.= " AND RAFID='".$_POST['rafid']."'";
 }
 $query.= " ORDER BY RAFID, INSERT_DATE DESC";

//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmt);
}
$amount=count($res1['SITEID']);
$hist=0;
foreach ($res1['SITEID'] as $key=>$attrib_id) {
	
	$user_UPDATE=getuserdata($res1['INSERT_BY'][$key]);

	if ($res1['HISTORY'][$key]==1){
		$class="class='TRACK_hist_data'";
		$btnDis="disabled";
		$editing="";
		$style="style='display:none;'";
		$hist++;
	}else{
		$class="";
		$btnDis="";
		$editing="editableTracking";
		$style="";
	}
    $comments.="<tr id='trackLine".$res1['ID'][$key]."' ".$style." ".$class.">
    	<td style='width:65px;'>
		 <div class='btn-toolbar' role='toolbar' style='display:block;'>
          <div class='btn-group'>
            <button class='btn btn-default btn-xs tracknav ".$btnDis."' data-trackid='".$res1['ID'][$key]."' href='#'  title='Make history'><span class='glyphicon glyphicon-time'></span></button>
          </div>
         </div>
    	</td>
    	<td>".$res1['ID'][$key]."</td>
    	<td>".$res1['RAFID'][$key]."</td>
    	<td>".$res1['SITEID'][$key]."</td>
    	<td><a class='popovers' data-trigger='hover' data-container='body' data-html='true' data-toggle='popover' data-placement='right' 
        data-content='Created by: ".$res1['INSERT_BY'][$key]."'>".$res1['INSERT_DATE'][$key]."</a></td>
        <td>".$res1['RAFCOMMENT'][$key]."</td>
    </tr>";
}

$query = "select t1.RAFID AS RAFID, TASK, t1.COMMENTS, t1.UPDATE_BY, t1.UPDATE_DATE from TASK_COMMENTS t1 LEFT JOIN BSDS_RAFV2 t2 ON t1.RAFID=t2.RAFID
 WHERE SITEID LIKE '%".strtoupper($_POST['siteID'])."%' ";
if ($_POST['rafid']){
 	$query.= " AND t1.RAFID='".$_POST['rafid']."'";
 }
$query.=" ORDER BY t1.RAFID, t1.UPDATE_DATE DESC";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmt);
}
$amountTaskComments=count($res1['RAFID']);
foreach ($res1['RAFID'] as $key=>$attrib_id) {
	
	$user_UPDATE=getuserdata($res1['UPDATE_BY'][$key]);
    $task_comments.="<tr>
    	<td>".$res1['RAFID'][$key]."</td>
    	<td>".$res1['TASK'][$key]."</td>
    	<td><a class='popovers' data-trigger='hover' data-container='body' data-html='true' data-toggle='popover' data-placement='right' 
        data-content='Created by: ".$res1['UPDATE_BY'][$key]."'>".$res1['UPDATE_DATE'][$key]."</a></td>
        <td>".$res1['COMMENTS'][$key]."</td>
    </tr>";
}

?>


<div class="pull-right">
<?php if ($hist!=0){ ?>
	<button type="button" class="btn btn-success btn-xs history" id="TRACK_hist">
	<span class="glyphicon glyphicon-eye-open"> HISTORY (<?=$hist?>)</span>
	</button>
<?php } ?>
</div>

<h3>TASK COMMENTS</h3>
<table class="table table-bordered">
	<thead>
    	<tr>
    		<th>RAFID</th>
    		<th>TASK</th>
           
            <th>INSERT DATE</th>
             <th>COMMENTS</th>
        </tr>
    </thead>
    <tbody>
        <?=$task_comments?>
    </tbody> 
</table>

<h3>RAFID COMMENTS</h3>
<table class="table table-bordered" id="tracking<?=$_POST['siteID']?>">
    <thead>
    	<tr>
    		<th><button class="btn btn-xs newTrack" title='Add comments to site' id='newTrack' data-siteid="<?=$_POST['siteID']?>"><span class="glyphicon glyphicon-plus-sign"></span>Add</button></th>
    		<th>ID</th>
    		<th>RAFID</th>
    		<th>SITEID</th>
            <th>INSERT DATE</th>
            <th>COMMENTS</th>
        </tr>
    </thead>
    <tbody>
        <?=$comments?>
    </tbody>    
</table>

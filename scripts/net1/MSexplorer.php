<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("../general_info/general_info_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

?>
<script type="text/javascript">
$(document).ready( function() {

    var Options = {
		success: 	after_analyse,
		dataType:  'json'
	};	
	function after_analyse(response){  
		$('#spinner').spin(false);

		if (response.type=='info'){
			if ( $('#NB_'+response.rafid+response.task).length ) {
	
				$('#NB_'+response.rafid+response.task).removeClass('btn-success').removeClass('btn-info').removeClass('btn-link').removeClass('btn-success').removeClass('btn-danger').addClass('btn-'+response.color1);
				$('#icon_'+response.rafid+response.task).css( "color",response.color2 );

			}else{
				$('#NB2_'+response.rafid+response.task).html('<button type="button" class="btn btn-'+response.color1+' btn-xs history" id="NB_'+response.rafid+response.task+'"><span class="glyphicon glyphicon-flag"></span></button>');
				$('#row_'+response.rafid+response.task).after('<tr class="NB_'+response.rafid+response.task+'_data" ><td colspan="4"><table style="width:100%;"" id="NBtable_'+response.rafid+response.task+'"></table></td></tr>');
			}
			$('#NBtable_'+response.rafid+response.task).prepend('<tr style="background-color:#F5FFFF"><td>'+response.comments+'</td><td><?=$guard_username?></td><td>now</td><td><button type="button" class="btn btn-'+response.color1+' btn-xs"><span class="glyphicon glyphicon-flag" style="color:'+response.color2+'"></button></td></tr>');
		}
		Messenger().post({
		  message: response.message,
		  type: response.type,
		  showCloseButton: true
		});
	}	
	$('.addCommentsSubmit').click(function(e){
		e.preventDefault();
		$('#spinner').spin('medium');
		var nbup=$(this).data('nbup');
		var rafid=$(this).data('rafid');
	    $('#form_add'+nbup+rafid).ajaxSubmit(Options);
	    
	    return false;
	});

	
	$('.addCommentBtn').click(function(){
		var task=$(this).data('task');
		var rafid=$(this).data('rafid');
		var nbup=$(this).data('nbup');
		$("#code"+rafid).val(task);
		$("#labelTask"+rafid).text(task);
		
		if(nbup==='NB'){
			$("#add_comment_dataNB"+rafid).show("slow");
		}else{
			$("#add_comment_dataUP"+rafid).show("slow");
		}
	});
	$('.makeUpdatable').click(function(){
		var task=$(this).data('task');
		var rafid=$(this).data('rafid');
		$.ajax({
			type: "POST",
			url: 'scripts/net1/net1_actions.php',
			data: { action:'makeupdatable',task:task,rafid:rafid},
			dataType: 'json',
			success : function(response){
			   $('#updatable_'+response.rafid+response.task).addClass('alert-info');
			    Messenger().post({
				  message: response.message,
				  type: response.type,
				  showCloseButton: true
				});
			}
		});
	});
});
</script>
<?php

function get_flag_color($status){
	if($status=='General Info'){
		$colors[1]="info";
		$colors[2]="#FFFFFF";
	}else if($status=='Partner General Info'){
		$colors[1]="";
		$colors[2]="#5bc0de";
	}else if($status=='Blocking'){
		$colors[1]="danger";
		$colors[2]="#FFFFFF";
	}else if($status=='Partner Blocking'){
		$colors[1]="";
		$colors[2]="#d2322d";
	}else if($status=='Non-Blocking'){
		$colors[1]="warning";
		$colors[2]="#FFFFFF";
	}else if($status=='Partner Non-Blocking'){
		$colors[1]="";
		$colors[2]="#f0ad4e";
	}else if($status=='Resolved'){
		$colors[1]="success";
		$colors[2]="#FFFFFF";
	}else if($status=='Partner Resolved'){
		$colors[1]="";
		$colors[2]="#5cb85c";
	}else{
		$colors[1]="";
		$colors[2]="#000000";
	}
	return $colors;
}

function output_milestones($data,$comment,$rafid,$res1,$i,$NBUP,$guard_groups){

	foreach ($data as $key => $value){
		if ($value['UPDATABLE']==1){
			$updatabale='alert-info';
			$title='Updatable';
		}else{
			if (substr_count($guard_groups, 'Administrators')==1){ 
				$updatabale='unupdatable';
			}else{
				$updatabale='';
			}
			$title='';
		}
		echo "<tr id='row_".$rafid.$key."'>
		<td>";
		if ($value['UPDATABLE']==1 && $rafid!='NONE'){
		echo "<div class='btn-toolbar' role='toolbar'>
          <div class='btn-group'>
            <button class='btn btn-default btn-xs addCommentBtn' title='Add comments' data-task='".$key."' data-rafid='".$rafid."' data-nbup='".$NBUP."'><span class='glyphicon glyphicon-plus'></span></button>
          </div>
        </div>";
    	}else if(substr_count($guard_groups, 'Administrators')==1){
		echo "
		<div class='btn-toolbar' role='toolbar'>
          <div class='btn-group'>
            <button class='btn btn-default btn-xs makeUpdatable' title='Make updatable' data-task='".$key."' data-rafid='".$rafid."' data-nbup='".$NBUP."'><span class='glyphicon glyphicon-pencil'></span></button>
          </div>
        </div>";
    	}
    	echo "
        </td>
		<td><span class='".$updatabale." tippy' data-task='".$key."' data-rafid='".$rafid."' title='".$title."' id='updatable_".$rafid.$key."'>".$key."</td>
		<td>".$value['DESCRIPTION']."</td>
		<td>".$res1[$key][$i]."</td>";
		if ($value['UPDATABLE']==1 && is_array($comment[$key]['COMMENTS'])){
			$color=get_flag_color($comment[$key]['STATUSCOLOR']);

			echo '<td><a class="tippy" title="'.$comment[$key]['STATUSCOLOR'].'"><button type="button" class="btn btn-'.$color[1].' btn-xs history" id="NB_'.$rafid.$key.'">
				<span class="glyphicon glyphicon-flag" style="color:'.$color[2].'"  id="icon_'.$rafid.$key.'"></span>
			</button></a></td>';
		}else{
			echo "<td id='NB2_".$rafid.$key."'>&nbsp;</td>";
		}
		echo "</tr>";

		if ($value['UPDATABLE']==1 && is_array($comment[$key]['COMMENTS'])){
			echo "<tr class='NB_".$rafid.$key."_data' style='display:none;'>
			<td colspan='4'>
			<table style='width:100%;' id='NBtable_".$rafid.$key."'>";
			foreach ($comment[$key]['COMMENTS'] as $keyC => $commentval) {

				$color=get_flag_color($comment[$key]['COLOR'][$keyC]);
				
				$user_BY=getuserdata($comment[$key]['UPDATE_BY'][$keyC]); 
				echo "<tr style='background-color:#F5FFFF'><td>".$commentval."</td>
				<td><a class='tippy' title='".$user_BY['fullname']."'>".$comment[$key]['UPDATE_BY'][$keyC]."</a></td>
				<td>".$comment[$key]['UPDATE_DATE'][$keyC]."</td>
				<td width='30px'><a class='tippy' title='".$comment[$key]['COLOR'][$keyC]."'><button type='button' class='btn btn-".$color[1]." btn-xs'><span class='glyphicon glyphicon-flag' style='color:".$color[2]."'></button></a></td></tr>";
			}
			echo "</table></tr>";
		}
	} 
}


if ($_POST['siteID']){
	$BSDSrefresh=get_BSDSrefresh();

	$siteID=$_POST['siteID'];

	$query="select * from TASKS_ADMIN ORDER BY TASKORDER";
	//echo $query;
	$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
	if (!$stmtT) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmtT);
		$amount_of_TASKS=count($resT['TASK']);
	}

	for ($i=0;$i<$amount_of_TASKS;$i++){
		//echo $resT['TASK'][$i]."<br>";
		$task=$resT['TASK'][$i];
		if ($resT['TYPE'][$i]=="NBACQ"){
			$NBACQ[$task]['DESCRIPTION']=$resT['DESCRIPTION'][$i];
			$NBACQ[$task]['UPDATABLE']=$resT['UPDATABLE'][$i];
			$NBtasks.=$resT['TASK'][$i].",";
		}else if ($resT['TYPE'][$i]=="NBCON"){
			$NBCON[$task]['DESCRIPTION']=$resT['DESCRIPTION'][$i];
			$NBCON[$task]['UPDATABLE']=$resT['UPDATABLE'][$i];
			$NBtasks.=$resT['TASK'][$i].",";
		}else if ($resT['TYPE'][$i]=="UPACQ"){
			$UPACQ[$task]['DESCRIPTION']=$resT['DESCRIPTION'][$i];
			$UPACQ[$task]['UPDATABLE']=$resT['UPDATABLE'][$i];
			$UPtasks.=$resT['TASK'][$i].",";
		}else if ($resT['TYPE'][$i]=="UPCON"){
			$UPCON[$task]['DESCRIPTION']=$resT['DESCRIPTION'][$i];
			$UPCON[$task]['UPDATABLE']=$resT['UPDATABLE'][$i];
			$UPtasks.=$resT['TASK'][$i].",";
		}
	}

	$query="SELECT
			".$NBtasks."RAFID,SIT_UDK,WOR_UDK,WOR_DOM_WOS_CODE,WOE_RANK,DRE_V20_1,DRE_V2_1_6,WOR_HSDPA_CLUSTER,
			SIT_LKP_STY_CODE,NB.SAC,NB.ALSAC,NB.CON,NB.ALCON,NB.WIPA,NB.WIPC
		FROM
			VW_NET1_ALL_NEWBUILDS NB
		LEFT JOIN BSDS_RAFV2 RA ON 
		 NB.SIT_UDK = RA.NET1_LINK
		WHERE
			SIT_UDK LIKE '%".$siteID."%'
			AND ((SITEID LIKE '%".$siteID."%' AND RAFID IS NOT NULL) or RAFID IS NULL )
		ORDER BY
			SIT_UDK ASC";
	//qecho $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_NEW=count($res1['SIT_UDK']);
	}


	for ($i=0;$i<$amount_of_NEW;$i++){
		$res1['SIT_UDK'][$i]."-".$res1['WOR_UDK'][$i]."<br>";
		if ($res1['A105'][$i]!="" && $res1['A709'][$i]!=""){
			$virtual='OK';
		}else{
			$virtual='NOT OK';
		}
		if ($res1['WIPA'][$i]=='TECHM'){
			$WIPA='<span class="label label-warning">WIP TECHM</span>';
		}else{
			$WIPA='';
		}
		if ($res1['WIPC'][$i]=='TECHM'){
			$WIPC='<span class="label label-warning">WIP TECHM</span>';
		}else{
			$WIPC='';
		}
		//echo "---".$res1['RAFID'][$i];
		if ($res1['RAFID'][$i]!=''){
			$rafid=$res1['RAFID'][$i];
			$query="SELECT * FROM TASK_COMMENTS WHERE RAFID='".$rafid."' ORDER BY TASK,UPDATE_DATE DESC";
			//echo $query."<br>";
			$stmtC = parse_exec_fetch($conn_Infobase, $query, $error_str, $resC);
			if (!$stmtC) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmtC);
				$amount_of_COMMENTS=count($resC['TASK']);
				$z=0;
				for ($j=0;$j<$amount_of_COMMENTS;$j++){
					$task=$resC['TASK'][$j];
					$comment[$task]['COMMENTS'][]=$resC['COMMENTS'][$j];
					$comment[$task]['UPDATE_BY'][]=$resC['UPDATE_BY'][$j];
					$comment[$task]['UPDATE_DATE'][]=$resC['UPDATE_DATE'][$j];
					$comment[$task]['COLOR'][]=$resC['STATUSCOLOR'][$j];
					//echo $prev_task."!=".$task."---".$resC['STATUSCOLOR'][$j]."<br>";
					if($prev_task!=$task && $prev_task!=''){
						$z=0;
					}
					if($z==0){
						$comment[$task]['STATUSCOLOR']=$resC['STATUSCOLOR'][$j];
						$z=1;
					}
					$prev_task=$task;
					
				}
			}
		}else{
			$rafid='NONE';
		}
		//echo "<pre>".print_r($comment,true)."</pre>";
		?>
<div class='modal fade' id='NEW_<?=$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]?>' tabindex="-1" role="dialog" aria-labelledby="NEW_<?=$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]?>Label" aria-hidden="true">
	<div class="modal-dialog modalwide"> 
		<div class="modal-content"> 	
		 	<div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    	<div class="row">
		    		<div class="col-md-10">
		    			<h3 id="NEW_<?=$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]?>Label">&nbsp;&nbsp;<?=$res1['SIT_UDK'][$i]?> -- RAF: <?=$rafid?></h3>
		    		</div>
		    		<div class="col-md-2">
		    			
		  			</div>
		  		</div>
		  	</div>
		  	<div class="modal-body">
		  		<?php if ($rafid!='NONE'){ ?>
			  		<div class="well" id="add_comment_dataNB<?=$rafid?>"  style='display:none;'>
			  		<form action="scripts/net1/net1_actions.php" class="form-inline" role="form" method="post" id="form_addNB<?=$rafid?>">
					<input type="hidden" name="action" value="addComments">
					<input type="hidden" name="rafid" value="<?=$rafid?>">
					<input type="hidden" name="code" id="code<?=$rafid?>" value="<?=$code?>">
					
					Add comments for <span id='labelTask<?=$rafid?>'></span>
					<div class="form-group">
					    <label for="status" class="sr-only">STATUS</label>
					    <select id="status" name="status" class="form-control">
					    <option value="" class="text-info alert-info">Please select flag</option>
					    <?php if (substr_count($guard_groups, 'Partner')!=1){ ?>
					    <option value="General info" class="text-info alert-info">General info</option>
					    <option value="Blocking" class="text-danger alert-danger">Blocking</option>
					    <option value="Non-Blocking" class="text-warning alert-warning">Non-Blocking</option>
					    <option value="Resolved" class="text-success alert-success">Resolved</option>
					    <?php }else{ ?>
					    <option value="Partner General info" class="text-info" style='font-weight:bolder;'>General info</option>
					    <option value="Partner Blocking" class="text-danger" style='font-weight:bolder;'>Blocking</option>
					    <option value="Partner Non-Blocking" class="text-warning" style='font-weight:bolder;'>Non-Blocking</option>
					    <option value="Partner Resolved" class="text-success" style='font-weight:bolder;'>Resolved</option>
					    <?php } ?>
					    </select>
					</div>
					<div class="form-group">
					    <label for="comments" class="sr-only">COMMENTS</label>
					    <input type="text" name="comments" class="form-control comments" id="comments" placeholder="Comments">
					</div>
					<button type="submit" class="btn btn-default addCommentsSubmit" data-nbup="NB" data-rafid="<?=$rafid?>">Add Comment</button>
					</form>
					</div>
				<?php 
				}else{
					echo '<div class="alert alert-danger" role="alert">You first need to create a RAF before you can add comments!</div>';
				} ?>

		    	<div class="row">
		    		<div class="col-md-6">
				    	<table class="table table-striped table-hover table-condensed">
				    	<caption><h3><span class="label label-primary">Acquisition</span></h3></caption>
				    	<tbody>
				    	<?php output_milestones($NBACQ,$comment,$rafid,$res1,$i,"NB",$guard_groups); ?>
				    	</tbody>
				   	 	</table>
			   	 	</div>
		    		<div class="col-md-6">
						<table class="table table-striped table-hover table-condensed">
				    	<caption><h3><span class="label label-primary">Construction</span></h3></caption>
				    	<tbody>
							<?php output_milestones($NBCON,$comment,$rafid,$res1,$i,"NB",$guard_groups); ?>
				    	</tbody>
				   	 	</table>
			   	 </div>
		    	</div>
		  	</div><!-- /.modal-body-->
		  	<div class="modal-footer">
			    <button tyep="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content-->
	</div><!-- /.modal-dialog-->
</div><!-- /.modal-->
		<?php
		$query="SELECT SITEID,MONUMENT from MONLAND WHERE SITEID LIKE '%".$res1['SIT_UDK'][$i]."%'";
		
		$stmtB = parse_exec_fetch($conn_Infobase, $query, $error_str, $resB);
		if (!$stmtB){
			die_silently($conn_Infobase, $error_str);
			exit;
		}else{
			OCIFreeStatement($stmtB);
			$amountB=count($resB['SITEID']);
			if ($amountB>0){
				$monu="<div id='prio' class='badge pull-right' style='background-color:#66FF33;' rel='tooltip' title='Monument & Landschappen site: ".$resB['MONUMENT'][0]."'>ML</div>";
			}else{
				$monu='';
			}
		}

		//echo $res1['WOR_UDK'][$i]."---".$res1['WOE_RANK'][$i]."---".$res1['WOR_DOM_WOS_CODE'][$i]."<br>";
		if (trim($res1['WOE_RANK'][$i])==1 && (trim($res1['WOR_DOM_WOS_CODE'][$i])=='IS' OR trim($res1['WOR_DOM_WOS_CODE'][$i])=='OH' OR trim($res1['WOR_DOM_WOS_CODE'][$i])=='AD')){
			if ($res1['A72'][$i]){
				$pac=$res1['A72'][$i];
			}else{
				$pac='X';
			}
			if($res1['A503'][$i]."<br>".$res1['A725'][$i]."<br>".$res1['A134'][$i]!='' && $res1['A141'][$i]!='' 
			&& $res1['A54'][$i]!='' && $res1['A45'][$i]!='' && $res1['A50'][$i]!='' && $res1['A59'][$i]!='' && $res1['A63'][$i]!=''
			&& $res1['A110'][$i]!='' && $res1['A64'][$i]!='' && $res1['A65'][$i]!='' && $res1['A285'][$i]!='' && $res1['A288'][$i]!=''
			&& $res1['A71'][$i]!='' && $res1['A91'][$i]!='' && $res1['A83'][$i]!='' && $res1['A309'][$i]!=''
			&& $res1['A92'][$i]!='' && $res1['A80'][$i]!='' && $res1['A712'][$i]!=''){
				$pakcheck="label-success";
			}else{
				$pakcheck="label-inverse";
			}
			if (substr($res1['WOR_UDK'][$i],0,1)=='_'){
				$worudk=substr($res1['WOR_UDK'][$i],1);
			}else{ //T sites
				$worudk=$res1['WOR_UDK'][$i];
			}
			if($res1['CON'][$i]=='BENCHMARK' or ($res1['CON'][$i]=='' && $res1['SAC'][$i]=='BENCHMARK')){
				$ran='BENCHMARK_RAN';
				$dir=substr($res1['WOR_UDK'][$i],1,2)."/".$worudk."/".$res1['SIT_UDK'][$i];
			}else{
				$ran='RAN-ALU';
				$dir=substr($res1['WOR_UDK'][$i],1,2)."/".$worudk."/".$res1['SIT_UDK'][$i];
			}

			$ranurl=$config['sitepath_url'].'/bsds/scripts/liveranbrowser/liveranbrowser.php?dir='.$dir."&ran=".$ran;
			//echo $ranurl;
			$newbuild.="
			<tr class='info'>
				<td>
			        <div class='btn-toolbar' role='toolbar'>
			          <div class='btn-group'>
			            <button class='btn btn-default btn-xs' title='View' data-action='view'  data-toggle='modal' data-target='#NEW_".$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]."'><span class='glyphicon glyphicon-eye-open'></span></button>
						<button class='btn btn-default btn-xs validation' title='validation' data-rafid='".$rafid."' data-siteupgnr='".$res1['SIT_UDK'][$i]."' data-nbup='NB'><span class='glyphicon glyphicon-check'></span></button>			          
			          	<button class='btn btn-default btn-xs liveran' title='View files LIVE on the RAN' data-ranurl='".$ranurl."'><span class='glyphicon glyphicon-folder-open'></span></button>	
		       			<button class='btn btn-default btn-xs asset' title='View Asset info'  id='AsseticonN1".substr($res1['WOR_UDK'][$i],1,6)."' data-siteid='".substr($res1['WOR_UDK'][$i],1,6)."' data-candidate='".substr($res1['SIT_UDK'][$i],1)."'><span class='glyphicon glyphicon-globe'></span></button>
			          </div>
			        </div>
			        <div class='btn-toolbar' role='toolbar'>
		          		<div class='btn-group'>";
		        if (substr_count($guard_groups, 'Admin')==1){
		          	$newbuild.="
		          	<button class='btn btn-default btn-xs refreshN1' title='Live refresh from NET1' data-siteupgnr='".$res1['SIT_UDK'][$i]."' data-site='".$res1['WOR_UDK'][$i]."' data-nbup='NB'><span class='glyphicon glyphicon-refresh'></span></button>";
		        } 	
		       	$newbuild.="<button class='btn btn-default btn-xs bsds' title='Open BSDS'  id='bsdsiconN1".substr($res1['WOR_UDK'][$i],1,6)."' data-siteid='".substr($res1['WOR_UDK'][$i],1,6)."' data-candidate='".substr($res1['SIT_UDK'][$i],1)."' data-upgnr='NB' data-nbup='NB'><span class='glyphicon glyphicon-book'></span></button>";
		        if ($rafid!='' && $rafid!='NONE'){
		       		$newbuild.="
		       		<button class='btn btn-default btn-xs correspondingRAFID' data-module='raf' data-rafid='".$rafid."' data-siteid='".substr($res1['WOR_UDK'][$i],1,6)."' title='Open corresponding RAF with ID ".$rafid."'><span class='glyphicon glyphicon-road'></span></button>";
		       	 }
		       	 $newbuild.="
		       	 	<button class='btn btn-default btn-xs NET1log' data-module='raf' data-siteupgnr='".$res1['SIT_UDK'][$i]."' data-nbup='NB' title='View LOG of MS toggling via IB'><span class='glyphicon glyphicon-time'></span></button>
		          	</div>
		          	</div>";
		         
		        

		       if ($res1['WOR_UDK'][$i]==$_POST['net1link']){
		       	 	$gclass="style='background-color:yellow;'";
		       }else{
		       	 	$gclass="";
		       }

		       $newbuild.="
		        </td>
				<td ".$gclass."><a href='#' class='tippy' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_UDK'][$i]."<br>".$res1['SIT_UDK'][$i]."</a></td>
				<td><a href='#' class='tippy' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_DOM_WOS_CODE'][$i]."</a><br>".$monu."</td>
				<td>".$res1['DRE_V2_1_6'][$i]."<br><a href='#' rel='popover' data-content='".$res1['WOR_HSDPA_CLUSTER'][$i]."' data-original-title='RF INFO'>".$res1['SIT_LKP_STY_CODE'][$i]."</a></td>
				<td><a href='#' class='tippy' title='PO ACQ: ".$res1['A501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."<br>".$WIPA."</a></td>
				<td><a href='#' class='tippy' title='A709'>".$res1['A709'][$i]."</a></td>
				<td><a href='#' class='tippy' title='A105'>".$res1['A105'][$i]."</a></td>
				<td><a href='#' class='tippy' title='PO CON: ".$res1['A503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."<br>".$WIPC."</a></td>
				<td><a href='#' rel='popover' class='tippy' title='A353' data-content='".$res1['A353_NOTES'][$i]."' data-original-title='Budget Info'>".$res1['A353'][$i]."</a></td>
				<td><a href='#' rel='popover' class='tippy' title='A59' data-content='".$res1['A54'][$i]."' data-original-title='Lease activation'>".$res1['A59'][$i]."</a></td>
				<td><a href='#' rel='popover' class='tippy' title='A71' data-content='".$res1['A71_ESTIM'][$i]."' data-original-title='RF INFO'>".$res1['A71'][$i]."</a></td>
				<td><a href='#' class='tippy' title='A63'>".$res1['A63'][$i]."</a></td>
				<td><a href='#' class='tippy' title='A91'>".$res1['A91'][$i]."</a></td>
				<td><a href='#' class='tippy' title='A80'>".$res1['A80'][$i]."</a></td>
				<td><a href='#' title='A72 PAC CHECK' rel='popover' data-placement='left' data-content='".
				"<br>A503: ". $res1['A503'][$i]."<br>A725: ".$res1['A725'][$i]."<br>A134: ".$res1['A134'][$i]."<br>A141: ".$res1['A141'][$i].'<br>A54: '.$res1['A54'][$i]."<br>A45: ".$res1['A45'][$i]."<br>A50: ".$res1['A50'][$i]."<br>A59: ".$res1['A59'][$i]."<br>A63: ".$res1['A63'][$i]
				.'<br>A110: '.$res1['A110'][$i]."<br>A64: ".$res1['A64'][$i]."<br>A65: ".$res1['A65'][$i]."<br>A285: ".$res1['A285'][$i]."<br>A288: ".$res1['A288'][$i]
				.'<br>A71: '.$res1['A71'][$i]."<br>A91: ".$res1['A91'][$i]."<br>A83: ".$res1['A83'][$i]."<br>A309: ".$res1['A309'][$i]
				.'<br>A92: '.$res1['A92'][$i]."<br>A80: ".$res1['A80'][$i]."<br>A712: ".$res1['A712'][$i]
				."'>".$pac."</a></td>
				<td>".$res1['A81'][$i]."</td>
			</tr>";
		}else{  // HISTORY
			$newbuild_history.="
			<tr class='NB_hist_data warning' style='display:none;'>
				<td style='font-size:18px'>
			        <div class='btn-toolbar' role='toolbar'>
			          <div class='btn-group'>
			            <button class='btn btn-default btn-xs' title='View' data-action='view'  href='#' data-toggle='modal' data-target='#NEW_".$res1['SIT_UDK'][$i].$res1['WOR_UDK'][$i]."'><span class='glyphicon glyphicon-eye-open'></span></button>
			          	<button class='btn btn-default btn-xs validation' title='validation' data-siteupgnr='".$res1['SIT_UDK'][$i]."' data-nbup='NB'><span class='glyphicon glyphicon-check'></span></button>			          
			          	<button class='btn btn-default btn-xs liveran' title='View files LIVE on the RAN' data-ranurl='".$ranurl."'><span class='glyphicon glyphicon-folder-open'></span></button>	
			          </div>
			        </div>
			        <div class='btn-toolbar' role='toolbar'>
		          		<div class='btn-group'>
		          			<button class='btn btn-default btn-xs NET1log' data-module='raf' data-siteupgnr='".$res1['SIT_UDK'][$i]."' data-nbup='NB' title='View LOG of MS toggling via IB'><span class='glyphicon glyphicon-time'></span></button>
		          		</div>
		          	</div>
		        </td>
				<td ".$gclass."><a href='#' class='tippy' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_UDK'][$i]."<br>".$res1['SIT_UDK'][$i]."</a></td>
				<td><a href='#' class='tippy' title='".$res1['DRE_V20_1'][$i]."'>".$res1['WOR_DOM_WOS_CODE'][$i]."</a><br>".$monu."</td>
				<td>".$res1['DRE_V2_1_6'][$i]."<br><a href='#' rel='popover' data-content='".$res1['WOR_HSDPA_CLUSTER'][$i]."' data-original-title='RF INFO'>".$res1['SIT_LKP_STY_CODE'][$i]."</a></td>
				<td><a href='#' class='tippy' title='PO ACQ: ".$res1['A501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."<br>".$WIPA."</a></td>
				<td>".$res1['A709'][$i]."</td>
				<td>".$res1['A105'][$i]."</td>
				<td><a href='#' class='tippy' title='PO CON: ".$res1['A503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."<br>".$WIPC."</a></td>
				<td><a href='#' rel='popover' data-content='".$res1['A353_NOTES'][$i]."' data-original-title='Budget Info'>".$res1['A353'][$i]."</a></td>
				<td><a href='#' rel='popover' data-content='".$res1['A54'][$i]."' data-original-title='Lease activation'>".$res1['A59'][$i]."</a></td>
				<td><a href='#' rel='popover' data-content='".$res1['A71_ESTIM'][$i]."' data-original-title='RF INFO'>".$res1['A71'][$i]."</a></td>
				<td>".$res1['A63'][$i]."</td>
				<td>".$res1['A91'][$i]."</td>
				<td>".$res1['A80'][$i]."</td>
				<td>".$res1['A72'][$i]."</td>
				<td>".$res1['A81'][$i]."</td>
				</tr>";
		}
	}


	$query="SELECT
			".$UPtasks."RAFID,SIT_UDK,WOR_UDK,WOR_DOM_WOS_CODE,WOR_HSDPA_CLUSTER,WOR_LKP_WCO_CODE,
			SIT_LKP_STY_CODE,UP.SAC,UP.ALSAC,UP.CON,UP.ALCON,UP.WIPA,UP.WIPC,WOR_NAME
		FROM
			VW_NET1_ALL_UPGRADES UP
		LEFT JOIN BSDS_RAFV2 RA ON 
		 UP.WOR_UDK = RA.NET1_LINK
		WHERE
			SIT_UDK LIKE '%".$siteID."%'
			AND ((SITEID LIKE '%".$siteID."%' AND RAFID IS NOT NULL) or RAFID IS NULL )
			AND WOR_LKP_WCO_CODE!='COP'
		ORDER BY
			SIT_UDK, WOR_UDK,WOR_DOM_WOS_CODE ASC";

	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_UPG=count($res1['SIT_UDK']);
	}
	//echo $amount_of_UPG;
	for ($i=0;$i<$amount_of_UPG;$i++){
		if ($res1['U405'][$i]!="" && $res1['U709'][$i]!=""){
				$virtual='OK';
			}else{
				$virtual='NOT OK';
		}
		if ($res1['WIPA'][$i]=='TECHM'){
			$WIPA='<span class="label label-warning">WIP TECHM</span>';
		}else{
			$WIPA='';
		}
		if ($res1['WIPC'][$i]=='TECHM'){
			$WIPC='<span class="label label-warning">WIP TECHM</span>';
		}else{
			$WIPC='';
		}
		if ($res1['RAFID'][$i]!=''){
			$rafid=$res1['RAFID'][$i];
			$query="SELECT * FROM TASK_COMMENTS WHERE RAFID='".$rafid."' ORDER BY TASK,UPDATE_DATE DESC";
			//echo $query."<br>";
			$stmtC = parse_exec_fetch($conn_Infobase, $query, $error_str, $resC);
			if (!$stmtC) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmtC);
				$amount_of_COMMENTS=count($resC['TASK']);
				$z=0;
				for ($j=0;$j<$amount_of_COMMENTS;$j++){
					$task=$resC['TASK'][$j];
					$comment[$task]['COMMENTS'][]=$resC['COMMENTS'][$j];
					$comment[$task]['UPDATE_BY'][]=$resC['UPDATE_BY'][$j];
					$comment[$task]['UPDATE_DATE'][]=$resC['UPDATE_DATE'][$j];
					$comment[$task]['COLOR'][]=$resC['STATUSCOLOR'][$j];
					//echo $prev_task."!=".$task."---".$resC['STATUSCOLOR'][$j]."<br>";
					if($prev_task!=$task && $prev_task!=''){
						$z=0;
					}
					if($z==0){
						$comment[$task]['STATUSCOLOR']=$resC['STATUSCOLOR'][$j];
						$z=1;
					}
					$prev_task=$task;
					
				}
			}
		}else{
			$rafid='NONE';
		}
		?>
<div class='modal fade' id='UPG_<?=$res1['WOR_UDK'][$i]?>' role='dialog' aria-labelleby='UPG_<?=$res1['WOR_UDK'][$i]?>Label' aria-hidden='true'>
	<div class="modal-dialog modalwide">
		<div class="modal-content">
		 	<div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    	<div class="row">
		    		<div class="col-md-10">
		    			<h3 id="UPG_<?=$res1['WOR_UDK'][$i]?>Label">&nbsp;&nbsp;<?=$res1['SIT_UDK'][$i]?> / <?=$res1['WOR_UDK'][$i]?> -- RAF: <?=$rafid?></h3>
		    		</div>
		    		<div class="col-md-2">
		    			
		  			</div>
		  		</div>
		  	</div>
		  	<div class="modal-body">
			  	<?php if ($rafid!='NONE'){ ?>
			  		<div class="well" id="add_comment_dataUP<?=$rafid?>"  style='display:none;'>
			  		<form action="scripts/net1/net1_actions.php" class="form-inline" role="form" method="post" id="form_addUP<?=$rafid?>">
					<input type="hidden" name="action" value="addComments">
					<input type="hidden" name="rafid" value="<?=$rafid?>">
					<input type="hidden" name="code" id="code<?=$rafid?>" value="<?=$code?>">
					
					Add comments for <span id='labelTask<?=$rafid?>'></span>
					<div class="form-group">
					    <label for="status" class="sr-only">STATUS</label>
					    <select id="status" name="status" class="form-control">
					    <option value="" class="text-info alert-info">Please select flag</option>
					    <?php if (substr_count($guard_groups, 'Partner')!=1){ ?>
					    <option value="General info" class="text-info alert-info">General info</option>
					    <option value="Blocking" class="text-danger alert-danger">Blocking</option>
					    <option value="Non-Blocking" class="text-warning alert-warning">Non-Blocking</option>
					    <option value="Resolved" class="text-success alert-success">Resolved</option>
					    <?php }else{ ?>
					    <option value="Partner General info" class="text-info" style='font-weight:bolder;'>General info</option>
					    <option value="Partner Blocking" class="text-danger" style='font-weight:bolder;'>Blocking</option>
					    <option value="Partner Non-Blocking" class="text-warning" style='font-weight:bolder;'>Non-Blocking</option>
					    <option value="Partner Resolved" class="text-success" style='font-weight:bolder;'>Resolved</option>
					    <?php } ?>
					    </select>
					</div>
					<div class="form-group">
					    <label for="comments" class="sr-only">COMMENTS</label>
					    <input type="text" name="comments" class="form-control comments" id="comments" placeholder="Comments">
					</div>
					<button type="submit" class="btn btn-default addCommentsSubmit" data-nbup="UP" data-rafid="<?=$rafid?>">Add Comment</button>
					</form>
					</div>
				<?php 
				}else{
					echo '<div class="alert alert-danger" role="alert">You first need to create a RAF before you can add comments!</div>';
				} ?>

		    	<div class="row">
		    		<div class="col-md-6">
				    	<table class="table table-striped table-hover table-condensed">
				    	<caption><h3><span class="label label-primary">Acquisition</span></h3></caption>
				    	<tbody>
				    		<?php output_milestones($UPACQ,$comment,$rafid,$res1,$i,"UP",$guard_groups); ?>
				    	</tbody>
			   	 		</table>
	    			</div>
		    		<div class="col-md-6">
						<table class="table table-striped table-hover table-condensed">
				    	<caption><h3><span class="label label-primary">Construction</span></h3></caption>
				    	<tbody>
							<?php output_milestones($UPCON,$comment,$rafid,$res1,$i,"UP",$guard_groups); ?>
				    	</tbody>
				   	 	</table>
	    			</div>
	  			</div>
	  		</div><!-- /.modal-body-->
			<div class="modal-footer">
			    <button tyep="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content-->
	</div><!-- /.modal-dialog-->
</div><!-- /.modal-->
	<?php

	if (trim($res1['WOR_DOM_WOS_CODE'][$i])=='IS'){
		if ($res1['U418'][$i]){
			$pac=$res1['U418'][$i];
		}else{
			$pac='X';
		}
		if($res1['U503'][$i]."<br>".$res1['U725'][$i]."<br>".$res1['U134'][$i]!='' && $res1['U141'][$i]!='' 
		&& $res1['U102'][$i]!='' && $res1['U345'][$i]!='' && $res1['U349'][$i]!='' && $res1['U459'][$i]!='' && $res1['U363'][$i]!=''
		&& $res1['U310'][$i]!='' && $res1['U464'][$i]!='' && $res1['U365'][$i]!='' && $res1['U388'][$i]!=''
		&& $res1['U571'][$i]!='' && $res1['U391'][$i]!='' && $res1['U383'][$i]!='' && $res1['U309'][$i]!=''  && $res1['U392'][$i]!=''
		&& $res1['U380'][$i]!='' && $res1['U712'][$i]!=''){
			$pakcheck="label-success";
		}else{
			$pakcheck="label-inverse";
		}
		if ($_POST['upgnr']==$res1['WOR_UDK'][$i]){
			$gclass="style='background-color:yellow;'";
		}else{
			$gclass="";
		}

		if($res1['CON'][$i]=='BENCHMARK' or ($res1['CON'][$i]=='' && $res1['SAC'][$i]=='BENCHMARK')){
			$ran='BENCHMARK_RAN';
			$dir=substr($res1['SIT_UDK'][$i],1,2)."/".substr($res1['SIT_UDK'][$i],1,6)."/".$res1['SIT_UDK'][$i]."/".$res1['WOR_UDK'][$i];
		}else if($res1['CON'][$i]=='M4C' or ($res1['CON'][$i]=='' && $res1['SAC'][$i]=='M4C')){
				$ran='M4C_RAN';
				$dir=substr($res1['SIT_UDK'][$i],1,2)."/".substr($res1['SIT_UDK'][$i],1,6)."/".substr($res1['SIT_UDK'][$i],0,-1)."/".$res1['SIT_UDK'][$i]."/".$res1['WOR_UDK'][$i];
		}else{
			$ran='RAN-ALU';
			$dir=substr($res1['SIT_UDK'][$i],1,2)."/".substr($res1['SIT_UDK'][$i],1,6)."/".$res1['SIT_UDK'][$i]."/".$res1['WOR_UDK'][$i];
		}
		$ranurl=$config['sitepath_url'].'/bsds/scripts/liveranbrowser/liveranbrowser.php?dir='.$dir."&ran=".$ran;

		if ($res1['WOR_UDK'][$i]==$_POST['net1link']){
       	 	$gclass="style='background-color:yellow;'";
       	}else{
       	 	$gclass="";
       	}

		$upgrade.="
		<tr class='info'>
			 <td style='font-size:18px'>
		        <div class='btn-toolbar' role='toolbar'>
		          <div class='btn-group'>
		            <button class='btn btn-default btn-xs' title='View' data-action='view'  href='#' data-toggle='modal' data-target='#UPG_".$res1['WOR_UDK'][$i]."'><span class='glyphicon glyphicon-eye-open'></span></button>
		            <button class='btn btn-default btn-xs validation' title='validation' data-siteupgnr='".$res1['WOR_UDK'][$i]."' data-nbup='UPG'><span class='glyphicon glyphicon-check'></span></button>
		          	<button class='btn btn-default btn-xs liveran' title='View files LIVE on the RAN' data-ranurl='".$ranurl."'><span class='glyphicon glyphicon-folder-open'></span></button>
		          </div>
		         </div>
		         <div class='btn-toolbar' role='toolbar'>
		          	<div class='btn-group'>";
		         if (substr_count($guard_groups, 'Admin')==1){
		          	$upgrade.="
		          	<button class='btn btn-default btn-xs refreshN1' title='Live refresh from NET1' data-siteupgnr='".$res1['WOR_UDK'][$i]."' data-site='".$res1['SIT_UDK'][$i]."' data-nbup='UPG'><span class='glyphicon glyphicon-refresh'></span></button>";
		         }
		         $upgrade.="<button class='btn btn-default btn-xs bsds' title='Open BSDS' id='bsdsiconN1".substr($res1['SIT_UDK'][$i],1).$res1['WOR_UDK'][$i]."' data-siteid='".substr($res1['SIT_UDK'][$i],1,6)."' data-rafid='".$rafid."' data-candidate='".substr($res1['SIT_UDK'][$i],1)."' data-nbup='UPG' data-upgnr='".$res1['WOR_UDK'][$i]."'><span class='glyphicon glyphicon-book'></span></button>";
		         
		         if ($rafid!='' && $rafid!='NONE'){
		       $upgrade.="
					<button class='btn btn-default btn-xs correspondingRAFID' data-module='raf' data-rafid='".$rafid."' data-siteid='".substr($res1['SIT_UDK'][$i],1,6)."' title='Open corresponding RAF with ID ".$rafid."'><span class='glyphicon glyphicon-road'></span></button>";
		       	 }
		       	  $upgrade.="
		       	  <button class='btn btn-default btn-xs NET1log' data-module='raf' data-siteupgnr='".$res1['WOR_UDK'][$i]."' data-nbup='UPG' title='View LOG of MS toggling via IB'><span class='glyphicon glyphicon-time'></span></button>
		       	 </div>
		        </div>
	        </td>
			<td class='header_site' ".$gclass."><a href='#' class='tippy' title='".$res1['WOR_NAME'][$i]."'>".$res1['SIT_UDK'][$i]."<br>".$res1['WOR_UDK'][$i]."</a></td>
			<td ".$gclass.">".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
			<td ".$gclass."><a href='#' class='tippy' title='RFINFO: ".$res1['WOR_HSDPA_CLUSTER'][$i]."'>".$res1['WOR_LKP_WCO_CODE'][$i]."</a></td>
			<td><a href='#' class='tippy' title='PO ACQ: ".$res1['U501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."<br>".$WIPA."</a></td>";

			if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'SH')){
				$upgrade.="<td><a href='#' class='tippy' title='Sublease signed'>".$res1['U100'][$i]."<br>U100</a></td>";
				$upgrade.="<td><a href='#' class='tippy' title='BP newcomer received'>".$res1['U104'][$i]."<br>U104</a><br></td>";
			}else{
				$upgrade.="<td><a href='#' class='tippy' title='U709'>".$res1['U709'][$i]."</a></td>";
				$upgrade.="<td><a href='#' class='tippy' title='U405'>".$res1['U405'][$i]."</a></td>";
			}
		$upgrade.="
			<td><a href='#' class='tippy' title='PO CON: ".$res1['U503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."<br>".$WIPC."</a></td>
			<td><a href='#' class='tippy' title='U353' rel='popover' data-content='".$res1['U353_NOTES'][$i]."' data-original-title='Budget info'>".$res1['U353'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U459'>".$res1['U459'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U571' rel='popover' data-content='".$res1['U571_ESTIM'][$i]."' data-original-title='Integration forecast'>".$res1['U571'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U363'>".$res1['U363'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U391'>".$res1['U391'][$i]."</a></td>
			<td><a href='#' class='tippy' title='U380'>".$res1['U380'][$i]."</a><br><a href='#' class='tippy' title='U825'>".$res1['U825'][$i]."</a></td>
			<td><a href='#' rel='popover' data-placement='left' data-content='".
			"A503: ". $res1['U503'][$i]."<br>U725: ".$res1['U725'][$i]."<br>U134: ".$res1['U134'][$i]."<br>U141: ".$res1['U141'][$i] 
			.'<br>U102: '.$res1['U102'][$i]."<br>U345: ".$res1['U345'][$i]."<br>U349: ".$res1['U349'][$i]."<br>U459: ".$res1['U459'][$i]."<br>U363: ".$res1['U363'][$i]
			.'<br>U110: '.$res1['U110'][$i]."<br>U364: ".$res1['U364'][$i]."<br>U365: ".$res1['U365'][$i]."<br>U388: ".$res1['U388'][$i]
			.'<br>U571: '.$res1['U571'][$i]."<br>U391: ".$res1['U391'][$i]."<br>U383: ".$res1['U383'][$i]."<br>U309: ".$res1['U392'][$i]
			.'<br>U392: '.$res1['U392'][$i]."<br>U380: ".$res1['U380'][$i]."<br>U712: ".$res1['U712'][$i]
			."' title='U418 PAC CHECK'>".$pac."</a></td>
			<td><a href='#' class='tippy' title='U381'>".$res1['U381'][$i]."</a></td>
			</tr> ";
	}else{
		$upgrade_history.="
		<tr class='UPG_hist_data warning' style='display:none;'>
			<td style='font-size:18px'>
		        <div class='btn-toolbar' role='toolbar'>
		          <div class='btn-group'>
		            <button class='btn btn-default btn-xs' title='View' data-action='view'  href='#' data-toggle='modal' data-target='#UPG_".$res1['WOR_UDK'][$i]."'><span class='glyphicon glyphicon-eye-open'></span></button>
		          	<button class='btn btn-default btn-xs validation' title='validation' data-siteupgnr='".$res1['WOR_UDK'][$i]."' data-nbup='UPG'><span class='glyphicon glyphicon-check'></span></button>
		          	<button class='btn btn-default btn-xs liveran' title='View files LIVE on the RAN' data-ranurl='".$ranurl."'><span class='glyphicon glyphicon-folder-open'></span></button>
		          </div>
		        </div>
		        <div class='btn-toolbar' role='toolbar'>
		          	<div class='btn-group'>
		          	 <button class='btn btn-default btn-xs NET1log' data-module='raf' data-siteupgnr='".$res1['WOR_UDK'][$i]."' data-nbup='UPG' title='View LOG of MS toggling via IB'><span class='glyphicon glyphicon-time'></span></button>
		       	 </div>
		        </div>
	        </td>
			<td class='header_site' ".$gclass."><a href='#' class='tippy' title='".$res1['WOR_NAME'][$i]."'>".$res1['SIT_UDK'][$i]."<br>".$res1['WOR_UDK'][$i]."</a></td>
			<td>".$res1['WOR_DOM_WOS_CODE'][$i]."</td>
			<td><a href='#' rel='popover' data-content='".$res1['WOR_HSDPA_CLUSTER'][$i]."' data-original-title='RFINFO'>".$res1['WOR_LKP_WCO_CODE'][$i]."</a></td>
			<td><a href='#' class='tippy' title='PO ACQ: ".$res1['U501'][$i]." * ALSAC: ".$res1['ALSAC'][$i]."'>".$res1['SAC'][$i]."</a></td>";

		if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'SH')){
			$upgrade_history.="<td><a href='#' class='tippy' title='Sublease signed'>".$res1['U100'][$i]."<br>U100</a><br></td>";
			$upgrade_history.="<td><a href='#' class='tippy' title='BP newcomer received'>".$res1['U104'][$i]."<br>U104</a><br></td>";
		}else{
			$upgrade_history.="<td><a href='#' title='U709'>".$res1['U709'][$i]."</a></td>";
			$upgrade_history.="<td><a href='#' title='U405'>".$res1['U405'][$i]."</a></td>";
		}

		$upgrade_history.="
			<td><a href='#' class='tippy' title='PO CON: ".$res1['U503'][$i]." * ALCON: ".$res1['ALCON'][$i]."'>".$res1['CON'][$i]."</a></td>
			<td><a href='#' rel='popover' data-content='".$res1['U353_NOTES'][$i]."' data-original-title='Budget info'>".$res1['U353'][$i]."</a></td>
			<td>".$res1['U459'][$i]."</td>
			<td><a href='#' rel='popover' data-content='".$res1['U571_ESTIM'][$i]."' data-original-title='Integration forecast'>".$res1['U571'][$i]."</a></td>
			<td>".$res1['U363'][$i]."</td>
			<td>".$res1['U391'][$i]."</td>";

		if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'CWK')){
			$upgrade_history.="<td><a href='#' title='U825'>".$res1['U825'][$i]."</a></td>";
		}else if (substr_count($res1['WOR_LKP_WCO_CODE'][$i], 'SH')){
			$upgrade_history.="<td><a href='#' title='U999'>".$res1['U999'][$i]."</a></td>";
		}else{
			$upgrade_history.="<td><a href='#' title='U385 & U825'>".$res1['U380'][$i]."<br>".$res1['U825'][$i]."</a></td>";
		}

		$upgrade_history.="
			<td>".$res1['U418'][$i]."</td>
			<td>".$res1['U381'][$i]."</td>
			</tr>";	
	}
}
?>

<div class="pull-left"><h3><span class="label label-success">NEWBUILDS</span></h3></div>
<div class="pull-right"><h3>
	<button type="button" class="btn btn-success btn-xs history" id="NB_hist" data-clone='clone_NET1UPG<?=$_POST['siteID']?>'>
	<span class="glyphicon glyphicon-eye-open"></span>
	</button>
</h3>
</div>
<div class="clearfix"></div>
	<div class="table-responsive table-responsive-force">
	<table class="table table-striped table-hover table-condensed" id="NET1NB<?=$_POST['siteID']?>" style="table-layout: fixed;">
		<colgroup>
			<col style="width: 110px">
		    <col style="width: 80px">
		    <col style="width: 30px">
		    <col style="width: 110px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
    	</colgroup>
	  	<thead>
	    <tr>
	    	<th>&nbsp;</th>
			<th>SITE</th>
			<th>ST</th>
			<th>TYPE</th>
			<th>SAC</th>
			<th>LEASE OK</th>
			<th>BP OK </th>
			<th>CON</th>
			<th>FUNDED</th>
			<th>PS</th>
			<th>INT</th>
			<th>CWI</th>
			<th>JI</th>
			<th>DEBARRED</th>
			<th>PAC</th>
			<th>FAC</th>
	    </tr>
	  	</thead>
	 	 <tbody>
	 	<?php echo $newbuild; ?>
	 	<tr class='NB_hist_data warning' style='display:none;'>
	 		<td colspan="15" style="text-align:center;"><span class="label label-default">HISTORY</span></td>
	 	</tr>
	 	<?php echo $newbuild_history; ?>
	  	</tbody>
	</table>
	</div>

<table>
<tr>
    <td><i>Last:</i></td><td>
    <?php if($BSDSrefresh['ACTION_ALL_NEW_NET1']=="Downloading"){ ?>
    <span class="label label-danger">Downloading data</span>
    <?php }else if($BSDSrefresh['ACTION_ALL_NEW_NET1']=="Importing"){ ?>
    <span class="label label-danger">Updating live data</span>
    <?php }else{ ?>
    <span class="label label-default" rel="tooltip" data-placement="bottom" title="Runs from 6:05 till 20:35 every 30 min."><?=substr($BSDSrefresh['DATE_ALL_UPG'],11,8)?></span>
    <?php } ?>
    </td>
    <td><i>Next:</i></td>
    <td><span class="label label-default"><?=$BSDSrefresh['NEXTRUN_ALL_NEW']?></span></td>
</tr>
</table>

<div class="pull-left"><h3><span class="label label-info">UPGRADES</span></h3></div>
<div class="pull-right"><h3>
	<button type="button" class="btn btn-info btn-xs history" id="UPG_hist">
	<span class="glyphicon glyphicon-eye-open"></span>
	</button>
</h3>
</div>

<div class="clearfix"></div>

	<div class="table-responsive table-responsive-force">
	<table class="table table-striped table-hover table-condensed" id="NET1UPG<?=$_POST['siteID']?>" style="table-layout: fixed;">
	  	<colgroup>
	  	 	<col style="width: 110px">
		    <col style="width: 80px">
		    <col style="width: 30px">
		    <col style="width: 110px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
		    <col style="width: 100px">
    	</colgroup>
	  	<thead>
	    <tr>
	    	<th>&nbsp;</th>
			<th>SITE</th>
			<th>ST</th>
			<th>TYPE</th>
			<th>SAC</th>
			<th>LEASE OK</th>
			<th>BP OK </th>
			<th>CON</th>
			<th>FUNDED</th>
			<th>PS</th>
			<th>INT</th>
			<th>CWI</th>
			<th>JI</th>
			<th>DEBARRED</th>
			<th>PAC</th>
			<th>FAC</th>
	    </tr>
	  	</thead>
	 	 <tbody>
	 	<?php echo $upgrade; ?>
	 	<tr class='UPG_hist_data warning' style='display:none;'>
	 		<td colspan="15" style="text-align:center;"><span class="label label-default">HISTORY</span></td>
	 	</tr>
	 	<?php echo $upgrade_history; ?>
	  	</tbody>
	</table>
	</div>

<table>
<tr>
    <td><i>Last:</i></td><td>
	    <?php if($BSDSrefresh['ACTION_ALL_UPG_NET1']=="Downloading"){ ?>
	    <span class="label label-danger">Downloading data</span>
	    <?php }else if($BSDSrefresh['ACTION_ALL_UPG_NET1']=="Importing"){ ?>
	    <span class="label label-danger">Updating live data</span>
	    <?php }else{ ?>
	    <span class="label label-default" rel="tooltip" data-placement="bottom" title="Runs from 6:00 till 20:35 every 30 min."><?=substr($BSDSrefresh['DATE_ALL_UPG'],11,8)?></span>
	    <?php } ?>
    </td>
    <td><i>Next:</i></td>
    <td><span class="label label-default"><?=$BSDSrefresh['NEXTRUN_ALL_UPG']?></span></td>
</tr>
</table>
<?php
}
?>
<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_RF,Base_TXMN,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$amountperpage=8;

if ($_POST['page']==""){
    $page=1;
}else{
    $page=$_POST['page'];
    $start= ($page-1)*$amountperpage +1;
    $end = ($page)*$amountperpage ;
}
 
$siteID=$_POST['siteID'];
$viewtype=$_POST['viewtype'];
if ($_POST['orderby']==""){
	$orderby="ID";
	$order="ASC";
}else{
	$orderby=$_POST['orderby'];
	$order=$_POST['order'];
}

if ($viewtype=="link"){
	$where=" WHERE ID='".$siteID."'";
}else if ($viewtype=="list"){
	$where=" WHERE (SITEA like '%".$siteID."%' OR SITEB like '%".$siteID."%') ";
}else if ($viewtype=="after_insert"){
	$where=" WHERE SITEA like '%".$siteID."%'";
}else if ($viewtype=="report"){
	$where="";

	if ($_POST['actionby']=="Partner Processing"){
		$where .= " WHERE (DONE = 'NOT OK' OR DONE='REJECTED')";
	}else if ($_POST['actionby']=="Partner Reporting"){
		$where .= " WHERE (REPORT = 'NOT OK' OR REPORT='REJECTED') AND DONE='OK'";
	}else if ($_POST['actionby']=="TXMN Resulting"){
		$where .= " WHERE RESULT = 'NOT OK' AND DONE='OK' AND REPORT='OK'";
	}else if ($_POST['actionby']=="Canceled"){
		$where .= " WHERE PRIORITY = 'Canceled'";
	}else{
		$where .= " WHERE SITEA IS NOT NULL";
	}
	if ($_POST['region']!="NA" && $_POST['region']!="ALL"){
		$where .= " AND (SITEA LIKE '%".$_POST['region']."%' OR SITEB LIKE '%".$_POST['region']."%')";
	}
	if ($_POST['type']!="" && $_POST['type']!="ALL"){
		$where .= " AND TYPE ='".$_POST['type']."'";
	}
}

if ($_POST['allocated']){
		$where .= " AND PARTNERVIEW LIKE '%".$_POST['allocated']."%'";
}
if (substr_count($guard_groups, 'ZTE')==1){
	$where .= " AND PARTNERVIEW LIKE '%ZTE%'";
}
if (substr_count($guard_groups, 'Bechmark')==1){
	$where .= " AND PARTNERVIEW LIKE '%BENCHMARK%'";
}
//echo $guard_groups;
if (substr_count($guard_groups, 'TechM')==1){
	$where .= " AND (PARTNERVIEW LIKE '%TECHM%' or PARTNERVIEW LIKE '%ALU%')";
}
if (substr_count($guard_groups, 'Alcatel')==1){
	$where .= " AND PARTNERVIEW LIKE '%ALU%'";
}


if ($start==""){
	$query="";
	$query="SELECT *  FROM  BSDS_LINKINFO ".$where ." ORDER BY ".$orderby."  ".$order;

}else{
	$query="SELECT * FROM";
	$query.="(";
	$query.="SELECT t.*, ROW_NUMBER() OVER (ORDER BY ".$orderby."  ".$order.") R
	FROM  BSDS_LINKINFO t  ". $where;
	$query.=") WHERE R BETWEEN ".$start." AND ".$end;
}
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_LOS=count($res1['SITEA']);
}

if ($_POST['totallos']==""){
    $totallos=$amount_of_LOS;
}else{
    $totallos=$_POST['totallos'];
}

if ($amount_of_LOS>=1){

	$k=0;
	for ($i = 0; $i <$amount_of_LOS; $i++) {

		

		if ($page!="1" or ($page=="1" && $i<$amountperpage))
      	{
			if ((substr_count($guard_groups, 'Base_TXMN')==1 || substr_count($guard_groups, 'Administrators')==1) && $res1['DELETED'][$i]!="yes"){
				$PRIORITY_select="editableLosSelectItem";
			}else{
				$PRIORITY_select="";
			}
			if ((((substr_count($guard_groups, 'Alcatel')==1 || substr_count($guard_groups, 'Benchmark')==1  || substr_count($guard_groups, 'Partner')==1)  && ($res1['DONE'][$i]=="NOT OK" or $res1['DONE'][$i]=="REJECTED"))|| substr_count($guard_groups, 'Administrators')==1 || substr_count($guard_groups, 'Base_TXMN')==1)&& $res1['DELETED'][$i]!="yes"){
				$DONE_select="editableLosSelectItem";
			}else{
				$DONE_select="";
			}

			if ((((substr_count($guard_groups, 'Alcatel')==1 || substr_count($guard_groups, 'Benchmark')==1  || substr_count($guard_groups, 'Partner')==1) && $res1['DONE'][$i]=="OK" &&( $res1['REPORT'][$i]=="NOT OK" or $res1['REPORT'][$i]=="REJECTED")) || substr_count($guard_groups, 'Administrators')==1 || substr_count($guard_groups, 'Base_TXMN')==1)&& $res1['DELETED'][$i]!="yes"){
				$REPORT_select="editableLosSelectItem";
			}else{
				$REPORT_select="";
			}

			if ((substr_count($guard_groups, 'Base_TXMN')==1  || substr_count($guard_groups, 'Administrators')==1)&& $res1['DELETED'][$i]!="yes"){
				$RESULT_select="editableLosSelectItem";
			}else{
				$RESULT_select="";
			}

			if ($res1['PRIORITY'][$i]=="Canceled"){
				$status="LOS canceled";
				$PRIORITY_status="selected_LOS";
			}else{
				if ($res1['DONE'][$i]=="NOT OK" || $res1['DONE'][$i]=="REJECTED"){
					$status="Partner in process";
					$DONE_status="selected_LOS";
				}else if ($res1['DONE'][$i]=="OK" && ($res1['REPORT'][$i]=="NOT OK" || $res1['REPORT'][$i]=="REJECTED")){
					$status="Partner to create report";
					$REPORT_status="selected_LOS";
				}else if (($res1['RESULT'][$i]=="NOT OK" || $res1['RESULT'][$i]=="REJECTED") && $res1['REPORT'][$i]=="OK" && $res1['DONE'][$i]=="OK"){
					$status="TXMN to evaluate report";
					$RESULT_status="selected_LOS";
				}else{
					$status="LOS confirmed";
					$DONE_status="";
					$REPORT_status="";
					$RESULT_status="";
				}
			}

			if ($res1['DELETED'][$i]=="yes"){
	            $row_color="danger";
	            $user_DELETED=getuserdata($res1['DELETED_BY'][$i]);
	            $user=$user_DELETED['firstname']." ".$user_DELETED['lastname'];
	            $status="<a  title='BY $user on ".$res1['DELETED_DATE'][$i]."'>DELETED</a>";
	            $delAction="undelete_los";
	            $deleteTitle="Undelete this LOS";
	            $saveAllowed="disabled";
	        }else{
	        	$row_color="";
	            $delAction="delete_los";
	            $deleteTitle="Delete this LOS";
	            $saveAllowed="yes";
	        }

			$user_CREATION=getuserdata($res1['CREATION_BY'][$i]);
			$user_UPDATE_BY=getuserdata($res1['UPDATE_BY'][$i]);
			$user_DONE_BY=getuserdata($res1['DONE_BY'][$i]);
			$user_REPORT_BY=getuserdata($res1['REPORT_BY'][$i]);
			$user_RESULT_BY=getuserdata($res1['RESULT_BY'][$i]);

			?>
<div id="losOutput">
			<div id='LOSBOX_<?=$res1['ID'][$i]?>' class='modal fade' tabindex="-1" role='dialog' aria-labelleby='myModelLabel' aria-hidden='true'>
	          	<div class="modal-dialog">
	          		<div class="modal-content">
			          	<div class='modal-header'>
			            	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
			            	<h3 class="modal-title" id='myModalLabel'>LOS <?=$res1['ID'][$i]?></h3>
			          	</div>
			          	<div class='modal-body'>
							<table class='table table-striped'>
				            <thead>
				                <th>Action</th>
				                <th>By</th>
				                <th>Date</th>
				            </thead>
				            <tbody>
							<tr>
								<td>CREATION</td>
								<td><?=$user_CREATION['firstname']?> <?=$user_CREATION['lastname']?></td>
								<td><?=$res1['CREATION_DATE'][$i]?></td>
							</tr>
							<tr>
								<td>UPDATE</td>
								<td><?=$user_UPDATE_BY['firstname']?> <?=$user_UPDATE_BY['lastname']?></td>
								<td><?=$res1['UPDATE_DATE'][$i]?></td>
							</tr>
							<tr>
								<td>Partner in progress</td>
								<td><?=$user_UPDATE_BY['firstname']?> <?=$user_UPDATE_BY['lastname']?></td>
								<td><?=$res1['DONE_DATE'][$i]?></td>
							</tr>
							<tr>
								<td>Partner report</td>
								<td><?=$user_REPORT_BY['firstname']?> <?=$user_REPORT_BY['lastname']?></td>
								<td><?=$res1['REPORT_DATE'][$i]?></td>
							</tr>
							<tr>
								<td>Result acceptance</td>
								<td><?=$user_RESULT_BY['firstname']?> <?=$user_RESULT_BY['lastname']?></td>
								<td><?=$res1['RESULT_DATE'][$i]?></td>
							</tr>
							</table>
							<table class='table table-striped'>
							<tr>
								<td>Height A</td>
								<td><?=$res1['HEIGHTA'][$i]?></td>
							</tr>
							<tr>
								<td>Height B</td>
								<td><?=$res1['HEIGHTB'][$i]?></td>
							</tr>
							<tr>
								<td>Comments A</td>
								<td><?=$res1['COMMENTSA'][$i]?></td>
							</tr>
							<tr>
								<td>Comments B</td>
								<td><?=$res1['COMMENTSB'][$i]?></td>
							</tr>
							<tr>
								<td>REJECT REASON:</td>
								<td><?=$res1['REJECT_REASON'][$i]?></td>
							</tr>
							</table>
						</div>
				  	</div>
				</div>
			</div>
			<?php
			if ($res1['TYPE'][$i]=="ST"){
				$CON='ALU TXMN';
			}else{
				$CON=$res1['CON'][$i];
			}
			$output_los.="<tr id='losLine".$res1['ID'][$i]."' class='".$row_color."'>
			<td style='font-size:18px;'>		
			<button class='btn btn-default btn-xs' data-toggle='modal' data-target='#LOSBOX_".$res1['ID'][$i]."'>".$res1['ID'][$i]."</button><br>
	        <div class='btn-toolbar' role='toolbar'>
	          <div class='btn-group'>
	            <button class='btn btn-default btn-xs losnav' rel='tooltip' title='View LOS details' data-toggle='dropdown' id='view' data-losid='".$res1['ID'][$i]."' data-siteA='".$res1['SITEA'][$i]."' data-siteB='".$res1['SITEB'][$i]."'><span class='glyphicon glyphicon-eye-open'></span></button>
	            <button class='btn btn-default btn-xs losnav' rel='tooltip' title='".$deleteTitle."' id='".$delAction."' data-losid='".$res1['ID'][$i]."' data-siteA='".$res1['SITEA'][$i]."' data-siteB='".$res1['SITEB'][$i]."'><span class='glyphicon glyphicon-trash losnav'></span></button>
	            <button class='btn btn-default btn-xs losnav' rel='tooltip' title='Print LOS' id='print' href='scripts/los/los_print.php' data-losid='".$res1['ID'][$i]."'><span class='glyphicon glyphicon-print'></span></button>
	            <button class='btn btn-default btn-xs losnav' rel='tooltip' title='Reopen LOS' id='reopen' data-losid='".$res1['ID'][$i]."' data-siteA='".$res1['SITEA'][$i]."' data-siteB='".$res1['SITEB'][$i]."'><span class='glyphicon glyphicon-repeat'></span></button> 
	          </div>
	        </div>
			</td>
			<td>".$res1['SITEA'][$i]."</td>
			<td>".$res1['SITEB'][$i]."</td>
			<td>".$res1['PRIORITY'][$i]."</td>
			<td><b><div id='status_".$res1['ID'][$i]."'>".$status."</div></b></td>
			<td>".$user_CREATION['firstname']." ".$user_CREATION['lastname']."</td>
			<td>".$res1['PARTNERVIEW'][$i]."</td>
			<td><a href='#' id='DONE-".$res1['ID'][$i]."' data-losid='".$res1['ID'][$i]."' data-ltype='DONE' data-partner='".$res1['PARTNERVIEW'][$i]."' class='tabledata ".$DONE_select."' data-pk='".$res1['ID'][$i]."'>".$res1['DONE'][$i]."</a></td>
			<td><a href='#' id='REPORT-".$res1['ID'][$i]."' data-losid='".$res1['ID'][$i]."' data-ltype='REPORT' data-partner='".$res1['PARTNERVIEW'][$i]."' class='tabledata ".$REPORT_select."' data-pk='".$res1['ID'][$i]."'>".$res1['REPORT'][$i]."</a></td>
			<td><a href='#' id='RESULT-".$res1['ID'][$i]."' data-losid='".$res1['ID'][$i]."' data-ltype='RESULT' data-partner='".$res1['PARTNERVIEW'][$i]."' class='tabledata ".$RESULT_select."' data-pk='".$res1['ID'][$i]."'>".$res1['RESULT'][$i]."</a></td>
			<td><div id='TYPE-".$res1['ID'][$i]."' class='tabledata'>".$res1['TYPE'][$i]."</div></td>
			</tr>";
		}
	}//End FOR

}else{
	$output_los.="<tr>";
	$output_los.="<td colspan='11'><b>NO links found!</b></td>";
	$output_los.="</tr>";
}
?>
<div class="modal fade" id="losdetails">
	<div class="modal-dialog modalwide">
    	<div class="modal-content">
		    <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4><span class="label label-default">LOS ID: <span id="selected_losID">NA</span></span></h4>
		        <ul class="nav nav-pills losdetails">
					<li id="los_details_txmn"><a href="#">TXMN</a></li>
					<li id="los_details_A"><a href="#">A END LOS Survey report</a></li>
					<li id="los_details_B"><a href="#">B END LOS Survey report</a></li>
					<li id="los_details_files"><a href="#">Survey attachements</a></li>
					<li id="los_details_reject"><a href="#">Rejection</a></li>
		        </ul>
		        <span id="modalspinner"></span>
		    </div>
		    <div class="modal-body">
		        <div class="alert" id="messagebox" style="display:none"></div>
		        <div id="LOScontentNet1"></div>
		        <div id="LOScontent"></div>
		    </div>
		</div>
	</div>
</div>

<form id='losform'>
<? echo $output_los_hidden; ?>
<input type="hidden" name="siteID" value="<?=$_POST['siteID']?>" id="losSiteID">
<div class="table-responsive" id="scrollerlostable">
<table class="table table-bordered tablefixecol table-condensed" id="LOSTable<?=$_POST['siteID']?>" style="table-layout: fixed;">
	<colgroup>
    <col style="width: 110px">
    <col style="width: 120px">
    <col style="width: 120px">
    <col style="width: 120px">
    <col style="width: 160px">
    <col style="width: 100px">
    <col style="width: 100px">
    <col style="width: 100px">
    <col style="width: 100px">
    <col style="width: 100px">
    <col style="width: 50px">
    </colgroup>
    <thead>
    <tr>
        <th><button class="btn btn-xs btn-default losnav" title='Create new LOS' id='newlos' data-siteid="<?=$_POST['siteID']?>" href="scripts/los/los_details_txmn.php"><span class="glyphicon glyphicon-plus-sign"></span> Add LOS</button><br><br>LOS ID</th>
		<th>SiteA</th>
		<th>SiteB</th>
		<th>Priority</th>
		<th>Action by</th>
		<th>Link Designer</th>
		<th>Partner viewable?</th>
		<th>Partner processed</th>
		<th>Partner rep. created</th>
		<th>TXMN Result</th>
		<th>Type</th>
    </tr>
    </thead>
    <tbody>
        <?=$output_los?>
    </tbody>
</table>
</div>
</form>

<span class="label label-default"><?=$totallos?> LOSs found</span><br>
</div>
<?php 
$amount_of_pages=$totallos/$amountperpage;
$amount_of_pages= ceil($amount_of_pages);

if ($amount_of_pages>1){ ?>
    <ul class="pagination pagination-sm"> 
    <?php
    if ($page==1){
        $prevclass="disabled";
    }

    ?>
    <li class="lospaging <?=$prevclass?>" data-page="<?php echo $page-1; ?>"><a href="#">Prev</a></li>
    <?php 
    if ($page-3>1){
        ?>
        <li class="lospaging <?=$prevclass?>" data-page="<?php echo $page-4; ?>"><a href="#">...</a></li>
    <?php
    }

    if ($amount_of_pages>5){
        $show=5;
        $show=$amount_of_pages;
    }else{
        $show=$amount_of_pages;
    }
    for($i=1;$i<=$show;$i++){
        if ($i==$page){
            $active="active";
        }else{
            $active="";
        }
        if ($i>=$page-3 && $i<=$page+3){
            echo '<li class="lospaging '.$active.'" data-page="'.$i.'"><a href="#">'.$i.'</a></li>'; 
        }
    }
    if ($amount_of_pages-3>$page){ ?>
        <li class="lospaging <?=$prevclass?>" data-page="<?php echo $page+4; ?>"><a href="#">...</a></li>
    <?php
    }
    if ($page==$amount_of_pages){
        $nextclass="disabled";
    }
    ?>
    <li class="lospaging <?=$nextclass?>" data-page="<?php echo $page+1; ?>"><a href="#">Next</a></li>
  </ul>

<?php } ?>
<script type="text/javascript">
$('.lospaging').click(function() {

    $('#spinner').spin('medium');
    var page=$(this).data("page");
    $("#losOutput").load("scripts/los/los.php",
    {
    	viewtype: '<?=$viewtype?>',
        actionby: '<?=$_POST['actionby']?>',
        siteID: '<?=$_POST['siteID']?>',
		orderby:'<?=$orderby?>',
		order:'<?=$order?>',
		type:'<?=$_POST['TYPE']?>',
		region:'<?=$_POST['region']?>',
		allocated:'<?=$_POST['allocated']?>',
        page: page,
        totallos: '<?=$totallos?>'
    },
    function(){
       $('#spinner').spin(false);
       forceResponsiveTables("LOSTable");
       $('#LOSTable').scroller('LOSTable',4);

    });
}); 
</script>

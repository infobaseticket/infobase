<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$searchk=strtoupper($_POST['searchk']);
$_SESSION['searchk']=$searchk;
$searchk_temp=$searchk;


if ($searchk!="")
{
	if (strlen($searchk)==4 && (($searchk>0 && $searchk<=999) or ($searchk>=8000 && $searchk<=8099) or ($searchk>=7000 && $searchk<=7099))){
		$searchk="AN".$searchk;
	}else if (strlen($searchk)==4 && ($searchk>6400 && $searchk<=6499)){
		$searchk="CT".$searchk;
	}else if ($searchk!="1406" && $searchk!="1403" && strlen($searchk)==4 && (($searchk>999 && $searchk<=1499) or ($searchk>=8100 && $searchk<=8149) or ($searchk>=7100 && $searchk<=7149))){
		$searchk="NR".$searchk;
	}else if (strlen($searchk)==4 && ($searchk==1403 or $searchk==1406 or ($searchk>1499 && $searchk<=1999) or ($searchk>=8150 && $searchk<=8199) or ($searchk>=7150 && $searchk<=7199))){
		 $searchk="HT".$searchk;
	}else if (strlen($searchk)==4 && (($searchk>1999 && $searchk<=2499) or ($searchk>=8200 && $searchk<=8249) or ($searchk>=7200 && $searchk<=7249))){
		$searchk="LG".$searchk;
	}else if (strlen($searchk)==4 && (($searchk>2499 && $searchk<=2999) or ($searchk>=8250 && $searchk<=8299) or ($searchk>=7250 && $searchk<=7299))){
		$searchk="LI".$searchk;
	}else if (strlen($searchk)==4 && (($searchk>2999 && $searchk<=3999) or ($searchk>=8300 && $searchk<=8399) or ($searchk>=7300 && $searchk<=7399))){
		$searchk="BX".$searchk;
	}else if (strlen($searchk)==4 && (($searchk>3999 && $searchk<=4499) or ($searchk>=8400 && $searchk<=8449) or ($searchk>=7400 && $searchk<=7449))){
		$searchk="VB".$searchk;
	}else if (strlen($searchk)==4 && (($searchk>4499 && $searchk<=4999) or ($searchk>=8450 && $searchk<=8499) or ($searchk>=7450 && $searchk<=7499))){
		$searchk="BW".$searchk;
	}else if (strlen($searchk)==4 && (($searchk>4999 && $searchk<=5499) or ($searchk>=8500 && $searchk<=8549) or ($searchk>=7500 && $searchk<=7549))){
		$searchk="OV".$searchk;
	}else if (strlen($searchk)==4 && (($searchk>5499 && $searchk<=5999) or ($searchk>=8550 && $searchk<=8599) or ($searchk>=7550 && $searchk<=7599))){
		$searchk="WV".$searchk;
	}else if (strlen($searchk)==4 && (($searchk>5999 && $searchk<=6499) or ($searchk>=8600 && $searchk<=8649) or ($searchk>=7600 && $searchk<=7649))){
		$searchk="LX".$searchk;
	}else if (strlen($searchk)==4 && $searchk>=6500 && $searchk<=6600){
		echo "For range 6500 to 6600, please specify if CT (container) or MT (mobile truck)";
		die;
	}
?>

<div id='tokenbox'>
<?php
$query="SELECT * from DELIVERYMASTER WHERE SITEID LIKE '%".$_POST['searchk']."%'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
     OCIFreeStatement($stmt);
}
$amount=count($res1['SITEID']);
for ($i = 0; $i <$amount; $i++) {  
	$tags.=$res1['TAGS'][$i].",";
	$tagbadges.= "<span class='label label-default pull-left'>".$res1['TAGS'][$i]."</span>";
}
$tags=substr($tags, 0,-1);


if (substr_count($guard_groups, 'Administrators')=="1" || substr_count($guard_groups, 'Base_delivery')=="1"  ){
/*
$query="
select DISTINCT(SPLIT) AS TAGNAME from (
with test as 
  (select SITEID,TAGS str from DELIVERYMASTER
   )  
   select regexp_substr (str, '[^,]+', 1, rn) as split
     from test
    cross
     join (select rownum rn
             from (select max (length (regexp_replace (str, '[^,]+'))) + 1 mx
                     from test
                )
        connect by level <= mx
         )
    where regexp_substr (str, '[^,]+', 1, rn) is not null
    order by SITEID
)";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
     OCIFreeStatement($stmt);
    foreach ($res1['TAGNAME'] as $key=>$tagname) {
	    $taglist.="{id: ".$key.", title: '".$tagname."'},";
	    $taglist2.='"'.$tagname.'",';
	}
	$taglist=substr($taglist,0,-1);
}
*/

$query="select * FROM DELIVERYTAGS";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
     OCIFreeStatement($stmt);
    foreach ($res1['TAGNAME'] as $key=>$tagname) {
	    $taglist.="{id: ".$key.", title: '".$tagname."'},";
	    $taglist2.='"'.$tagname.'",';
	}
	$taglist=substr($taglist,0,-1);
}
	?>
	<script type="text/javascript">
	$(document).ready( function() {
		$('#tokens').editable({
		  	url : "scripts/tracking/tracking_actions.php",
		  	params: function(params) {	 	
				params.action= "updateTags";
			    return params;
			},
	        inputclass: 'input-small',
	        placement: 'left',
	        allowClear: false,
	        select2: {
	            tags:[<?=$taglist2?>],
	            tokenSeparators: [",", " "],
	            width: '200px',
	            createSearchChoice: function() { return null; }
	        }
	    }); 
	});
	</script>	
	 <a href="#" id="tokens" data-type="select2" data-pk="<?=$searchk?>" data-title="Enter tags" class="editable editable-click"><?=$tags?></a>
<?php 
}else{
echo $tagbadges;
}
?>

<div id="prio" class="label label-info pull-right" rel='tooltip' title='Rollout Ranking'>
<?
$query="SELECT PRIO from ROLLOUTRANK WHERE SITE LIKE '%".$searchk."%'";
	//echo $query;
	$stmtP = parse_exec_fetch($conn_Infobase, $query, $error_str, $resP);
	if (!$stmtP){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmtP);
		$amountPRIO=count($resP['PRIO']);
		if ($amountPRIO>0){
			echo $resP['PRIO'][0];
		}
	}
?>
</div>
<?php

//Blacklistes
	$query="SELECT SITEID from BLACKLIST WHERE SITEID LIKE '%".$searchk."%'";
	//echo $query;
	$stmtB = parse_exec_fetch($conn_Infobase, $query, $error_str, $resB);
	if (!$stmtB){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmtB);
		$amountB=count($resB['SITEID']);
		if ($amountB>0){
			?>
			<div id="blacklist" class="label label-default pull-right" style="background-color:#000000;" rel='tooltip' title='Blacklisted site <?=$resB['SITEID'][0]?>'>BL</div>
			<?php
		}
	}

//CLUSTERS
	$query="SELECT CLUSTERN, CLUSTERNUM, SITEID FROM BSDS_RAFV2 t1 LEFT JOIN BSDS_RAF_RADIO t2 on t1.RAFID=t2.RAFID WHERE t1. SITEID = '".$searchk."' AND TYPE='MOD Upgrade'";
	//echo $query;
	$stmtB = parse_exec_fetch($conn_Infobase, $query, $error_str, $resB);
	if (!$stmtB){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmtB);
		$amountB=count($resB['SITEID']);
		if ($amountB>0){
			?>
			<div id="cluster" class="label label-default pull-right cluster pointer" data-html='true' data-placement='bottom' style="background-color:#A0522D;" data-cluster="<?=$resB['CLUSTERN'][0]?>" rel='tooltip' title='Click to view sites in cluster'><?=$resB['CLUSTERN'][0]?><?=$resB['CLUSTERNUM'][0]?></div>
			<?php
		}
	}

//LOWTHROUGPUT

	$query="SELECT SITEID,BANDWIDTH from LOWTHROUGPUT WHERE SITEID LIKE '%".$searchk."%'";
	//echo $query;
	$stmtB = parse_exec_fetch($conn_Infobase, $query, $error_str, $resB);
	if (!$stmtB){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmtB);
		$amountB=count($resB['SITEID']);
		if ($amountB>0){
			?>
			<div id="prio" class="label label-default pull-right" data-placement='left' style="background-color:#00ff00;" rel='tooltip' title='LOW THROUGPUT site (<?=$resB['BANDWIDTH'][0]?>)'>LT</div>
			<?php
		}
	}


//TASKCOMMENTS

	$query="select * from TASK_COMMENTS A1 INNER JOIN
	(select t2.RAFID,t2.TASK, t3.DESCRIPTION,MAX(T2.UPDATE_DATE)  AS UPDATE_DATE
	from BSDS_RAFV2 t1 LEFT JOIN TASK_COMMENTS t2 ON t1.RAFID=t2.RAFID
	LEFT JOIN TASKS_ADMIN t3 on t2.TASK=t3.TASK
	WHERE SITEID LIKE '%".$searchk."%'
	AND T2.TASK IS NOT NULL 
	GROUP BY t2.RAFID,t2.TASK, t3.DESCRIPTION) A2
	ON A1.RAFID=A2.RAFID AND A1.TASK=A2.TASK AND A1.UPDATE_DATE=A2.UPDATE_DATE
	AND A1.STATUSCOLOR LIKE '%Blocking%'";
	//echo $query;
	$stmtB = parse_exec_fetch($conn_Infobase, $query, $error_str, $resB);
	if (!$stmtB){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmtB);
		$amountB=count($resB['TASK']);
		if ($amountB>0){
			for ($i=0;$i<$amountB;$i++) {
				$data.="<u><b>".$resB['TASK'][$i]."] ".$resB['DESCRIPTION'][$i]."</b></u><br>".$resB['COMMENTS'][$i]."<br>";
			}
		?>
		<button type="button" data-html="true" data-placement='left' rel='tooltip' title='<p style="text-align:left;"> <?=$data?></p>' class="btn btn-danger btn-xs history pull-right"><span class="glyphicon glyphicon-flag" style="color:#FFFFFF"></span></button>
		<?php
		}	
	}

?>
</div><br>

<div class="site" style="display:none;"><?=$searchk?></div>

	<ul class='nav' id='mainicons'>
	<?php
//NET1
	if (strlen($searchk)>=4 && is_numeric(substr($_POST['searchk'],2,4))){
		$query="SELECT SIT_UDK,WOR_UDK from VW_NET1_ALL_NEWBUILDS WHERE WOR_UDK LIKE '%".$searchk."%'";
		//echo $query;
		$stmtP = parse_exec_fetch($conn_Infobase, $query, $error_str, $resP);
		if (!$stmtP){
			die_silently($conn_Infobase, $error_str);
			exit;
		}else{
			OCIFreeStatement($stmtP);
			$amountNET1b=count($resP['SIT_UDK']);
			if ($amountNET1b>0){
				echo "<form name='viewers' id='net1_form' class='viewers'>
				<input type='hidden' name='siteID' value='".$searchk."'>
				<li class='navicon msexpl' data-module='msexpl'><span class='glyphicon glyphicon-th-large' rel='tooltip' title='NET1 viewer' id='MSicon".$searchk."'></span> &nbsp;&nbsp;NET1</li>";
				/*
				if ( substr_count($guard_groups, 'Administrators')=="1"){
				echo "";
				}
<li class='navicon net1' data-module='net1'><span class='glyphicon glyphicon-th-large' rel='tooltip' title='NET1 viewer' id='net1icon".$searchk."'></span> &nbsp;&nbsp;NET1</li>*/

				echo "</form>";
			}
		}
	}

	$query="SELECT SIT_UDK,WOR_UDK from VW_NET1_ALL_UPGRADES WHERE WOR_UDK = '".$searchk."'";
	//echo $query;
	$stmtP = parse_exec_fetch($conn_Infobase, $query, $error_str, $resP);
	if (!$stmtP){
		die_silently($conn_Infobase, $error_str);
		exit;
	}else{
		OCIFreeStatement($stmtP);
		$amountNET1a=count($resP['SIT_UDK']);
		if ($amountNET1a>0){
			$searchk=substr($resP['SIT_UDK'][0],1,6);
			echo "<h4><span class='label label-default'>".$resP['SIT_UDK'][0]."</span></h4>
			    <form name='viewers' id='net1ID_form' class='viewers'>
				<input type='hidden' name='siteID' value='".substr($resP['SIT_UDK'][0],1,6)."'>
				<input type='hidden' name='upgnr' value='".$resP['WOR_UDK'][0]."'>
				<li class='net1 navicon' id='".$searchk."' data-module='net1'><span class='glyphicon glyphicon-th-large' rel='tooltip' title='NET1' id='net1icon".$_POST['searchk']."'></span> &nbsp;&nbsp;<a rel='tooltip' title='NET1 for ".$resP['SIT_UDK'][0]."'>NET1</a></li>
				</form>";
		
		}
    }
    if ($amountNET1b==0 && $amountNET1a==0){
    	echo "<li class='net1 navicon'><span class='glyphicon glyphicon-th-large' rel='tooltip' title='NET1' id='net1icon".$_POST['searchk']."'></span> &nbsp;&nbsp; NOTHING FOUND</li>";
    }
//LOS (PER ID)
	if ((substr_count($guard_groups, 'Base_TXMN')=="1"	|| substr_count($guard_groups, 'Administrators')=="1" 	|| substr_count($guard_groups, 'Partner')=="1") &&  is_numeric($_POST['searchk'])){

		$query="select ID,SITEA from BSDS_LINKINFO where ID = '".$_POST['searchk']."'";
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt){
			die_silently($conn_Infobase, $error_str);
			exit;
		}else{
			OCIFreeStatement($stmt);
			$amount_links=count($res1['ID']);
		}

		if ($amount_links!=0){
			echo "<form name='viewers' id='losID_form' class='viewers'>
			<input type='hidden' name='siteID' value='".$res1['ID'][0]."'>
			<input type='hidden' name='viewtype' value='link'>
			<li class='navicon losID' id='losID".$_POST['searchk']."' data-module='los'><span class='glyphicon glyphicon-screenshot' rel='tooltip' title='Line Of Sight'></span> &nbsp;&nbsp;LOS ID ".$_POST['searchk']."</li>
			</form>";
		}
	}

//LOS
	if ( (substr_count($guard_groups, 'Base_TXMN')=="1" or substr_count($guard_groups, 'Partner')=="1" || substr_count($guard_groups, 'Administrators')=="1") && (is_numeric(substr($searchk,2,4)) && strlen($searchk)>=4 && strlen($searchk)<=6)){

			echo "<form name='viewers' id='los_form' class='viewers'>
			<input type='hidden' name='siteID' value='".$searchk."'>
			<input type='hidden' name='viewtype' value='list'>
			<li class='navicon los' id='losicon' data-module='los'><span class='glyphicon glyphicon-screenshot' rel='tooltip' title='Line Of Sight'></span> &nbsp;&nbsp;LOS</li>
			</form>";
	}
//RAF (PER ID)
	//echo $guard_groups;

	if ((substr_count($guard_groups, 'Base')=="1" || substr_count($guard_groups, 'Administrators')=="1" || substr_count($guard_groups, 'Partner')=="1")  && is_numeric($_POST['searchk'])){

		$query="select SITEID,RAFID from BSDS_RAFV2 where RAFID = '".$_POST['searchk']."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt){
			die_silently($conn_Infobase, $error_str);
			exit;
		}else{
			OCIFreeStatement($stmt);
			$amount_rafs=count($res1['SITEID']);
		}
		if ($amount_rafs!=0){
			echo "<form name='viewers' id='rafID_form' class='viewers'>
			<input type='hidden' name='siteID' value='".$res1['SITEID'][0]."'>
			<input type='hidden' name='RAFID' value='".$res1['RAFID'][0]."'>
			<li class='navicon rafID'  data-module='raf' id='rafID".$_POST['searchk']."'><span class='glyphicon glyphicon-road' rel='tooltip' title='Radio Access Form'></span> &nbsp;&nbsp;RAF ID ".$_POST['searchk']."</li>
			</form>";
		}
	}

//RAF
	if (strlen($searchk)>=4 && strlen($searchk)<=6  && is_numeric(substr($_POST['searchk'],2,4))){
		if (substr_count($guard_groups, 'Base_RF')=="1"
				|| substr_count($guard_groups, 'Base_other')=="1"
				|| substr_count($guard_groups, 'Base_delivery')=="1"
				|| substr_count($guard_groups, 'Base_TXMN')=="1"
				|| substr_count($guard_groups, 'Partner')=="1"
				|| substr_count($guard_groups, 'Administrators')=="1"){
			echo "<form name='viewers' id='raf_form' class='viewers'>
			<input type='hidden' name='siteID' value='".$searchk."'>
			<input type='hidden' name='upgnr' value='".$resP['WOR_UDK'][0]."'>
			<li class='navicon raf' data-module='raf' id='raficon".$searchk."'><span class='glyphicon glyphicon-road' rel='tooltip' title='Radio Access Form'></span> &nbsp;&nbsp;RAF</li>";
			/*if (substr_count($guard_groups, 'Administrators')=="1"){
			echo "<li class='navicon raf2' data-module='raf' id='raficon".$searchk."'><span class='glyphicon glyphicon-road' rel='tooltip' title='Radio Access Form'></span> &nbsp;&nbsp;RAF TEST</li>";
			}*/
			echo "</form>";
		}
	}


//DELIVERYT RACKING

	if (strlen($searchk)>=4 && strlen($searchk)<=6 && is_numeric(substr($_POST['searchk'],2,4)))
	{

			echo "<form name='viewers' id='tracking_form' class='viewers'>
			<input type='hidden' name='siteID' value='".$searchk."'>
			<li class='navicon tracking' id='trackicon".$searchk."' data-module='tracking'><span class='glyphicon glyphicon-tasks' rel='tooltip' title='Site Tracking'> TRACKING</span></li>
			</form>";
	
//OSS

			echo "<form name='viewers' id='oss_form' class='viewers'>
			<input type='hidden' name='siteID' value='".$searchk."'>
			<input type='hidden' name='bypass' value='".$_POST['bypass']."'>
			<li class='navicon oss' id='ossicon".$_POST['searchk']."' data-module='oss'><span class='glyphicon glyphicon-tint' rel='tooltip' title='OSS Live data'></span> &nbsp;&nbsp;OSS</li>
			</form>";
	}
//EVENTCAL
		$query="select SITEID,EVENT from EVENTCAL where SITEID LIKE '%".$_POST['searchk']."%' or upper(EVENT) LIKE '%".strtoupper($_POST['searchk'])."%'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt){
			die_silently($conn_Infobase, $error_str);
			exit;
		}else{
			OCIFreeStatement($stmt);
			$amount_events=count($res1['SITEID']);
		}

		if ($amount_events!=0){
			for ($i = 0; $i <$amount_events; $i++) {  
				echo "<form name='viewers' id='event_form".$res1['SITEID'][$i]."' class='viewers'>
				<input type='hidden' name='siteID' value='".$res1['SITEID'][$i]."'>
				<li class='event navicon' id='".$searchk."' data-module='event'><span class='glyphicon glyphicon-calendar' rel='tooltip' title='Event site'></span> &nbsp;&nbsp;<a rel='tooltip' title='Event site for ".$res1['EVENT'][$i]."'>Event ".$res1['SITEID'][$i]."</a></li>
				</form>";
			}	
		}

		echo "<form name='viewers' id='net1_form' class='viewers'>
				<input type='hidden' name='siteID' value='".$searchk."'>
				<li class='navicon ran' data-module='filebrowser'><span class='glyphicon glyphicon-folder-open' rel='tooltip' title='RAN'> DOCUMENTS</span></li>
				</form>";
								
?>
	</ul>




<?php
//BSDS,OSS,... links
	if (strlen($searchk)>=4){		
		//We first select the preferred candidate
		$query="SELECT SIT_UDK, WOR_UDK from VW_NET1_ALL_NEWBUILDS WHERE WOR_UDK LIKE '%".$searchk."%' and WOE_RANK=1 AND  WOR_DOM_WOS_CODE='IS' AND WOR_UDK NOT LIKE 'T%'";
		//echo $query;
		$stmtP = parse_exec_fetch($conn_Infobase, $query, $error_str, $resP);
		if (!$stmtP){
		    die_silently($conn_Infobase, $error_str);
		    exit;
		}else{
		    OCIFreeStatement($stmtP);
		    $amountPref=count($resP['SIT_UDK']);
    	}
    	
    	if ($amountPref==0){
    		echo "<div class='alert alert-danger'>ERROR IN NET1!<br>No site issued (IS) and RANKING 1 => Please contact SDM!</div>";
			die;
    	}
    	if ($amountPref==0  && is_numeric(substr($searchk_temp,0,4))){
    		$query="SELECT SITEID, BSDSKEY from BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY = '".$searchk_temp."'";
			//echo $query;
			$stmtP = parse_exec_fetch($conn_Infobase, $query, $error_str, $resP);
		    if (!$stmtP){
		    	die_silently($conn_Infobase, $error_str);
		    	exit;
		    }else{
		    	OCIFreeStatement($stmtP);
		    	
		    	$amount_siteSBSDS=count($resP['SITEID']);
    			if($amount_siteSBSDS!=0){
    				$siteID=$resP['SITEID'][0];
					$searchk=$siteID;
					$BSDSKEY=$resP['BSDSKEY'][0];
    			}else{
    				$searchk=$searchk_temp;
    			}
		    	
    		}
    		$query="select idname,name, LOGNODEPK, LOGNODETYPEFK, ADDRESSFK from ".$config['table_asset_lognode']."
    				where
    					name like '%".$searchk."%' AND  LOGNODETYPEFK IN('5105','5108','11008','1222','1214','11002')
    					order by  IDNAME ASC, LOGNODEPK DESC";
    	}else{
    		$query="select idname,name, LOGNODEPK, LOGNODETYPEFK, ADDRESSFK from ".$config['table_asset_lognode']."
    				where
    					idname like '%".$searchk."%' AND  LOGNODETYPEFK IN('5105','5108','11008','1222','1214','11002')
    					order by  IDNAME ASC, LOGNODEPK DESC";
    	}
    	//echo $query;
    	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
    	if (!$stmt){
    		die_silently($conn_Infobase, $error_str);
    		exit;
    	}else{
    		OCIFreeStatement($stmt);
    		$amount_sites1=count($res1['LOGNODEPK']);
    	}
    	$j=0;

    	if ($amount_sites1!=0){

    		for ($i=0;$i<$amount_sites1;$i++){
				//echo "===>".$res1["IDNAME"][$i]."<br>";
				$firstletter=substr($res1["IDNAME"][$i],0,1);
				$firsttwoletter=substr($res1["IDNAME"][$i],0,2);
				if($res1["NAME"][$i]=="NOVAL"){
					echo "<div class='alert alert-danger'><b>ASSET ERROR!</b><br>Please correct the candidate letter (first name) for site ".$res1["IDNAME"][$i]." => ".$res1["NAME"][$i]." or create site in Asset.</div>";
					die;
				}
				if(($firstletter!="M" && $firstletter!="S" && $firstletter!="T") or $firsttwoletter=="MT"){
					$newsiteID= substr($res1["IDNAME"][$i],0,7);
					//echo "-----".$newsiteID." ".substr($newsiteID,-1,1)."<br>";
					if (strlen($newsiteID)==7 && substr($newsiteID,-1,1)!='0'){
						$newsiteID= substr($res1["IDNAME"][$i],0,7);
					}else{
						$newsiteID= substr($res1["IDNAME"][$i],0,6);
					}
					$candidate=$res1["NAME"][$i];
					//echo $candidate."-".substr($candidate,-1,1)."-".strlen($candidate)."<br>";
					if (substr($candidate,-1,1)!='0' && strlen($candidate)==7){ //BW4550A
						$out[$newsiteID]['candidate']=substr($res1["NAME"][$i],0,7);
					}else if (substr($candidate,-1,1)!='0' && strlen($candidate)==8){ //MBX3817C
						$out[$newsiteID]['candidate']=substr($res1["NAME"][$i],0,8);
					}else if (substr($candidate,-1,1)!='0' && strlen($candidate)==10){  //MBX3817C01
						$out[$newsiteID]['candidate']=substr($res1["NAME"][$i],0,8);
					}else if (substr($candidate,-1,1)!='0' && strlen($candidate)==9){ //BW4550A01
						$out[$newsiteID]['candidate']=substr($res1["NAME"][$i],0,7);
					}else{
						echo "<div class='alert alert-danger'>ERROR IN ASSET!<br>(2) Please correct the candidate letter (first name) for site ".$res1["IDNAME"][$i]." => ".$res1["NAME"][$i]."</div>";
						die;
					}

				}else if($firstletter=="M" or $firstletter=="S" or $firstletter=="T"){

					$newsiteID= substr($res1["IDNAME"][$i],0,8);
					if (strlen($newsiteID)==8){
						$newsiteID= substr($newsiteID,0,8);
					}else{
						$newsiteID= substr($newsiteID,0,-1);
					}
					$candidate=$res1["NAME"][$i];
					//echo $candidate."-".substr($candidate,-1,1);
					if (substr($candidate,-1,1)!='0' && strlen($candidate)==8){ //MBX3817C
						$out[$newsiteID]['candidate']=substr($res1["NAME"][$i],0,8);
					}else if (substr($candidate,-1,1)!='0' && strlen($candidate)==10){ //MBX3817C01
						$out[$newsiteID]['candidate']=substr($res1["NAME"][$i],0,8);
					}else{
						echo "<div class='alert alert-danger'>ERROR IN ASSET!<br>(3) Please correct the candidate letter (first name) for site ".$res1["IDNAME"][$i]." => ".$res1["NAME"][$i]."</div>";
						die;
					}
				}

				//we check the last letter of the candidate
				 if (!preg_match("/^[a-zA-Z]$/", substr($out[$newsiteID]['candidate'],-1,1))) {
				    echo "<div class='alert alert-danger'>ERROR IN ASSET! => missing candidate letter!<br>(4) Please correct the candidate letter (first name) for site ".$res1["IDNAME"][$i]." => ".$res1["NAME"][$i]."</div>";
					die;
				 }
				//echo $newsiteID." ".$out[$newsiteID]['candidate']."<br>";

    			if ($res1["LOGNODETYPEFK"][$i]=='1214'){ //U9 U21    				
    				if (substr($res1["IDNAME"][$i],-2,2)=='02'){

    					$lognodeID_UMTS900=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_UMTS900']=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['techno'].='U9+';
    				}
    				if (substr($res1["IDNAME"][$i],-2,2)=='01'){

    					$lognodeID_UMTS2100=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_UMTS2100']=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['techno'].='U21+';
    				}
    				$siteID=substr($res1["IDNAME"][$i],0,-2);
    				$candidate=substr($res1["NAME"][$i],0,-2);
    			}else if ($res1["LOGNODETYPEFK"][$i]=='5105'){
    				$query = "select * from BSDSINFO2 WHERE SITEKEY='".$res1["LOGNODEPK"][$i]."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%900%'";
    				$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
					if (!$stmt3){
						die_silently($conn_Infobase, $error_str);
						exit;
					}else{
						OCIFreeStatement($stmt3);
						$amount_GSM900=count($res3['ANTENNATYPE']);
    				}
    				$query = "select * from BSDSINFO2 WHERE SITEKEY='".$res1["LOGNODEPK"][$i]."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%1800%'";
					$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
					if (!$stmt3){
						die_silently($conn_Infobase, $error_str);
						exit;
					}else{
						OCIFreeStatement($stmt3);
						$amount_GSM1800=count($res3['ANTENNATYPE']);
    				}
    				if($amount_GSM900!=0 && $amount_GSM1800!=0){
						$out[$newsiteID]['techno'].='G9+G18+';
					}else if($amount_GSM900!=0){
						$out[$newsiteID]['techno'].='G9+';
					}else if($amount_GSM1800!=0){
						$out[$newsiteID]['techno'].='G18+';
					}

    				$lognodeID_GSM=$res1["LOGNODEPK"][$i];
    				$out[$newsiteID]['lognodeID_GSM']=$res1["LOGNODEPK"][$i];
    				$siteID=$res1["IDNAME"][$i];
    				$candidate=$res1["NAME"][$i];
    			}else if ($res1["LOGNODETYPEFK"][$i]=='5108'){ //Repeater GSM G9/G18
    				$query = "select DONOR from ".$config['table_asset_repeaters']."
							 where REPEATER LIKE '%".$_POST['searchk']."%'";
					//echo $query;
					$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
					if (!$stmt3){
						die_silently($conn_Infobase, $error_str);
						exit;
					}else{
						OCIFreeStatement($stmt3);
						$amount_DONOR=count($res3['DONOR']);
						if($amount_DONOR!=0){
							$donor=$res3["DONOR"][0];							
						}
					}
    				$query = "select * from BSDSINFO2 WHERE SECTORID='".$donor."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%900%'";
					//echo $query;
					$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
					if (!$stmt3){
						die_silently($conn_Infobase, $error_str);
						exit;
					}else{
						OCIFreeStatement($stmt3);
						$amount_GSM900=count($res3['ANTENNATYPE']);
					}
					$query = "select * from BSDSINFO2 WHERE SECTORID='".$donor."' AND FEEDERKEY!='Unknown' AND ANTENNATYPE like '%1800%'";
					//echo $query;
					$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
					if (!$stmt3){
						die_silently($conn_Infobase, $error_str);
						exit;
					}else{
						OCIFreeStatement($stmt3);
						$amount_GSM1800=count($res3['ANTENNATYPE']);
					}
					if($amount_GSM900!=0 && $amount_GSM1800!=0){
							$out[$newsiteID]['techno'].='G9 REP+G18 REP+';
					}else if($amount_GSM900!=0){
							$out[$newsiteID]['techno'].='G9 REP+';
					}else if($amount_GSM1800!=0){
							$out[$newsiteID]['techno'].='G18 REP+';
					}
    				$lognodeID_GSM=$res1["LOGNODEPK"][$i];
    				$out[$newsiteID]['lognodeID_GSM']=$res1["LOGNODEPK"][$i];
    				$siteID=$res1["IDNAME"][$i];
    				$candidate=$res1["NAME"][$i];

    			}else if ($res1["LOGNODETYPEFK"][$i]=='1222'){ //Repeater UMTS U9/U21
    				if (substr($res1["IDNAME"][$i],-2,2)=='01'){
    					$out[$newsiteID]['techno'].='U21 REP+';
    					$lognodeID_UMTS1800=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_UMTS2100']=$res1["LOGNODEPK"][$i];
    				}
    				if (substr($res1["IDNAME"][$i],-2,2)=='02'){
    					$out[$newsiteID]['techno'].='U9 REP+';
    					$lognodeID_UMTS900=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_UMTS900']=$res1["LOGNODEPK"][$i];
    				}
    				$lognode_UMTS=$res1["LOGNODEPK"][$i];
    				$out[$newsiteID]['lognodeID_UMTS2100']=$res1["LOGNODEPK"][$i];
    				$siteID=substr($res1["IDNAME"][$i],0,-2);
    				$candidate=substr($res1["NAME"][$i],0,-2);
    			}else if ($res1["LOGNODETYPEFK"][$i]=='11008'){
    				if (substr($res1["IDNAME"][$i],-2,2)=='05'){
    					$out[$newsiteID]['techno'].='L18 REP+';
    					$lognodeID_LTE1800=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_LTE1800']=$res1["LOGNODEPK"][$i];
    				}
    				if (substr($res1["IDNAME"][$i],-2,2)=='06'){
    					$out[$newsiteID]['techno'].='L26 REP+';
    					$lognodeID_LTE2600=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_LTE2600']=$res1["LOGNODEPK"][$i];
    				}
    				if (substr($res1["IDNAME"][$i],-2,2)=='07'){
    					$out[$newsiteID]['techno'].='L8 REP+';
    					$lognodeID_LTE800=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_LTE800']=$res1["LOGNODEPK"][$i];
    				}
    				$siteID=substr($res1["IDNAME"][$i],0,-2);
    				$candidate=substr($res1["NAME"][$i],0,-2);
    			}else if ($res1["LOGNODETYPEFK"][$i]=='11002'){
    				if (substr($res1["IDNAME"][$i],-2,2)=='05'){
    					$out[$newsiteID]['techno'].='L18+';
    					$lognodeID_LTE1800=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_LTE1800']=$res1["LOGNODEPK"][$i];
    				}
    				if (substr($res1["IDNAME"][$i],-2,2)=='06'){
    					$out[$newsiteID]['techno'].='L26+';
    					$lognodeID_LTE2600=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_LTE2600']=$res1["LOGNODEPK"][$i];
    				}
    				if (substr($res1["IDNAME"][$i],-2,2)=='07'){
    					$out[$newsiteID]['techno'].='L8+';
    					$lognodeID_LTE800=$res1["LOGNODEPK"][$i];
    					$out[$newsiteID]['lognodeID_LTE800']=$res1["LOGNODEPK"][$i];
    				}
    				$siteID=substr($res1["IDNAME"][$i],0,-2);
    				$candidate=substr($res1["NAME"][$i],0,-2);
    			}

    			$adreskeys[$res1["ADDRESSFK"][$i]]['techno'][]=$techno;
    			$previuous_addresskey=$res1["ADDRESSFK"][$i]; 
    			$out[$newsiteID]['ADDRESSFK']=$res1["ADDRESSFK"][$i]; 				
    			
    		} //END FOR
    		

    		$j=0;
			$i=0;

			//echo "<pre>".print_r($out,true)."</pre>";
			//echo "<pre>".print_r($out2,true)."</pre>";
			?>
			<div class="panel-group" id="accordionnav">
			<?php
			$pref=0;
			foreach($out as $key=>$site){

				$candidate=substr($key,-1);
				if (!preg_match("/^[a-zA-Z]$/", $candidate)) {
					$candidate='';
				}	
				$firstSite=substr($key,0,-1);

				if ((substr($site['candidate'],1,6)!=substr($resP['SIT_UDK'][0],1,-1) && strlen($site['candidate'])==8) or (substr($site['candidate'],0,6)!=substr($resP['SIT_UDK'][0],1,-1) && strlen($site['candidate'])==7)){
					echo "<div class='alert alert-danger'><b>ASSET ERROR!</b><br>Please correct the site ID's in Asset. They do not match: <br>".substr($site['candidate'],0,6)." - ".substr($resP['SIT_UDK'][0],1,-1)."</div>";
					die;
				}
				if (substr($resP['SIT_UDK'][0],1)==$site['candidate'] or $resP['SIT_UDK'][0]==$site['candidate'] or $amountSite==1){
					$view="in";
					$pref_text="P.";
					$pref++;
					$lastSite="<span class='badge badge-info' style='margin-left:-7px'>".$candidate."</span>";
				}else{
					$view="";
					$pref_text="";
				}	
				$techno=substr($site['techno'],0,-1);

				?>
								
				<div class="panel panel-default">
					<div class="panel-heading">
						 <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionnav" href="#siteB_<?=$i?>">
				  			<div class="siteid pull-left"><?php echo $key; ?>
				  				<?php if ($BSDSKEY){
				  					echo "<br>[".$BSDSKEY."]";
				  					$view="in";
				  				} ?>
				  			</div>
				  			<div class="siteid" style="display:none"><?php echo $lastSite; ?></div>					
							<? if ($pref_text!=""){ ?>
							<div class="label label-primary pull-right" rel="tooltip" data-original-title='Preferred candidate'><?=$pref_text?></div>
							<? } ?>
							<div class="clearfix"></div>
							<div class="technologies label label-warning"><small><?=$techno?></small></div>
							<? if ($donor!=""){ ?>
							<span class="donorsite" >=> <?=$donor?></span>
							<? } ?>
						</a> 
					</div>
					<div id="siteB_<?=$i?>" class="panel-collapse collapse <?=$view?>">
				      	<div class="panel-body">
				      		<?php //echo "<pre>".print_r($site,true)."</pre>"; ?>
				      		<form name="viewers_<?=$key?>" id="viewers_<?=$key?>" class="viewers">
							<input type="hidden" name="lognodeID_GSM" value="<?=$site['lognodeID_GSM']?>">
							<input type="hidden" name="lognodeID_UMTS2100" value="<?=$site['lognodeID_UMTS2100']?>">
							<input type="hidden" name="lognodeID_UMTS900" value="<?=$site['lognodeID_UMTS900']?>">
							<input type="hidden" name="lognodeID_LTE800" value="<?=$site['lognodeID_LTE800']?>">
							<input type="hidden" name="lognodeID_LTE1800" value="<?=$site['lognodeID_LTE1800']?>">
							<input type="hidden" name="lognodeID_LTE2600" value="<?=$site['lognodeID_LTE2600']?>">
							<input type="hidden" name="ADDRESSFK" value="<?=$site['ADDRESSFK']?>">
							<input type="hidden" name="siteID" value="<?=$key?>">
							<input type="hidden" name="candidate" value="<?=$site['candidate']?>">
							<input type="hidden" name="donor" value="<?=$donor?>">
							<input type="hidden" name="technos" value="<?=$techno?>">
							<?php if ($BSDSKEY){ ?>
				  			<input type="hidden" name="bsdskey" value="<?=$BSDSKEY?>">
				  			<?php	} ?>

				       		<ul class="nav">							
							<li class="navicon bsds2" data-module='bsds2' data-candidate='<?=$site['candidate']?>' id="bsdsicon<?php echo $key; ?>"><span class='glyphicon glyphicon-book' rel='tooltip' title='Base Station Datasheet'> BSDS</span></li>
							
							<li class="navicon asset" data-candidate='<?=$site['candidate']?>'  id="asseticon<?=$resP['SIT_UDK'][0]?>"><span class='glyphicon glyphicon-globe' rel='tooltip' title='ASSET'> ASSET</span></li>
								<ul style="display:none;" id='asset<?=$site['candidate']?>'>
									<?php if($site['lognodeID_GSM']!=""){ ?>
									<li class="navicon assettechno" data-techno="GSM" id="assetGSM<?php echo $resP['SIT_UDK'][0]; ?>"> GSM</span></li>
									<?php }
									if($site['lognodeID_UMTS900']!=""){ ?>
									<li class="navicon assettechno" data-techno="U9" id="assetU9<?php echo $resP['SIT_UDK'][0]; ?>"> UMTS900</span></li>
									<?php }
									if($site['lognodeID_UMTS2100']!=""){ ?>
									<li class="navicon assettechno" data-techno="U21" id="assetU21<?php echo $resP['SIT_UDK'][0]; ?>"> UMTS2100</span></li>
									<?php }
									if($site['lognodeID_LTE1800']!=""){ ?>
									<li class="navicon assettechno" data-techno="L18" id="assetL18<?php echo $resP['SIT_UDK'][0]; ?>"> LTE1800</span></li>
									<?php }
									if($site['lognodeID_LTE2600']!=""){ ?>
									<li class="navicon assettechno" data-techno="L26" id="assetL26<?php echo $resP['SIT_UDK'][0]; ?>"> LTE2600</span></li>
									<?php } 
									if($site['lognodeID_LTE800']!=""){ ?>
									<li class="navicon assettechno" data-techno="L8" id="assetL8<?php echo $resP['SIT_UDK'][0]; ?>"> LTE800</span></li>
									<?php } ?>
								</ul>
										
							</ul>
							</form>
						</div>
					</div>
				</div>
				
				<?
				$i++;
    		}
    		?>
    		</div>
    		<?
    		if ($pref>1){
    			?>
				<script language="JavaScript">
					Messenger().post({
					  message: "<h3>More than 1 preferred candidate or sites with same candidate letter in Asset!</h3><b>Or:</b> 2 sites are IS (issued) and have ranking 1 in NET1 => This is not allowed<br><b>Or:</b> not all technologies are under the correct candidate in Asset.",
					  type: 'error',
					  showCloseButton: true,
  					   hideOnNavigate: false,
  					   hideAfter:false
					});
				</script>
				<?
	    	}
    	}else{
    		echo "<div class='alert alert-info'><b>No data found!</b><br><i>Please first create your site in Asset.</i></div>";
    	}
		echo "</ul>";

	}
}
OCILogoff($conn_Infobase);
?>

<script type="text/javascript">
if ($("[rel=tooltip]").length) {
    $("[rel=tooltip]").tooltip();
 }
 </script>
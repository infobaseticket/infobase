<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/dirlister/filefunctions.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<link rel="stylesheet" href="css/rafprint.css" media="all">

<div class='printThis'>

<div class="row well" style="margin:0 1px;">
  <div style="float:left;"><img src="<?php echo $config['sitepath_url']; ?>/bsds/images/basecompany.png"></div>
  <div style="float:left;margin-left:30px;padding-left:30px;border-left:1px solid #000;"><img src="<?php echo $config['sitepath_url']; ?>/bsds/images/logoInfobase.png" width="200px"></span></div>
  <div style="float:left;border-left:1px solid #000;padding-left:20px;"><h4 class="raftitle_print">RAF ID <?=$_POST['rafid']?> <?=$_POST['siteid']?></h4></div>
</div>
<?
$query = "Select * FROM BSDS_RAFV2 WHERE RAFID = '".$_POST['rafid']."'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_RAFS=count($res1['SITEID'][0]);
	$region=substr($res1['SITEID'][0],0,2);
	$sitenum=substr($res1['SITEID'][0],2,4);
	if (strlen($res1['SITEID'][0])==7){
		$sitenumcand=substr($res1['SITEID'][0],-1);
	}
	$candidate=$res1['CANDIDATE'][0];
	$justification=$res1['JUSTIFICATION'][0];
	$type=$res1['TYPE'][0];
	$rafid=$_POST['rafid'];
	$budget_acq=$res1['BUDGET_ACQ'][0];
	$budget_con=$res1['BUDGET_CON'][0];
	$RFINFO=$res1['RFINFO'][0];
	$COMMERCIAL=$res1['COMMERCIAL'][0];

	$user_CREATION=getuserdata($res1['CREATED_BY'][0]);
    $user_UPDATE_BY=getuserdata($res1['UPDATE_BY'][0]);

}

$query="SELECT * FROM VW_RAF_PROCESSTAKS WHERE RAFTYPE='".$type."' and PHASE!='skip' AND STEPNUM IS NOT NULL";
//echo $query;
$stmtPR= parse_exec_fetch($conn_Infobase, $query, $error_str, $resPR);
if (!$stmtPR){
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtPR);
    $amount_of_TASKS=count($resPR['TASK_NAME']);
}	

for ($k = 0; $k <$amount_of_TASKS; $k++){ 
	$taskname=$resPR['TASK_NAME'][$k];
	
	if ($res1[$taskname][0]!="NOT OK" && $res1[$taskname][0]!="" && $taskname!='PO_ACQ' && $taskname!='PO_CON' && substr_count($res1[$taskname][0], 'MISSING')!=1 ){
        $userby=getuserdata($res1[$taskname.'_BY'][0]);

        $userdata.="<tr>
                    <td>".$resPR['FULLNAME'][$k]."</td>
                    <td>".$userby['firstname']." ".$userby['lastname']."</td>
                    <td>".$res1[$taskname.'_DATE'][0]."</td>
                </tr>";
    }
}	

$query4 = "select * FROM BSDS_RAF_PO where RAFID = '".$_POST['rafid']."'";
$stmt4 = parse_exec_fetch($conn_Infobase, $query4, $error_str, $res4);
if (!$stmt4) {
    die_silently($conn_Infobase, $error_str);
    exit;
}else{
    OCIFreeStatement($stmt4);
    $amountPO=count($res4['RAFID']);
    $PO_ACQ="";
    $PO_CON="";
    for ($j = 0; $j < $amountPO; $j++) { 
        if($res4['ACQCON'][$j]=='ACQ'){
        	$PO_ACQ=$res4['POPR'][$j]."<br>".$PO_ACQ; 
        }else if($res4['ACQCON'][$j]=='CON'){
            $PO_CON=$res4['POPR'][$j]."<br>".$PO_CON; 
        }   
    }

    $PO_CON=substr($PO_CON,0,-4);
    if ($PO_CON==""){ $PO_CON="NOT OK"; }

    $PO_ACQ=substr($PO_ACQ,0,-4);
    if ($PO_ACQ==""){ $PO_ACQ="NOT OK"; }
    if ($res1['BUFFER'][0]=="1"){ $PO_ACQ="NA"; }
 }
?>
<br>

<table class="table table-condensed">
<thead>
    <th>Action</th>
    <th>By</th>
    <th>Date</th>
</thead>
<tbody>
<tr>
    <td>CREATION</td>
    <td><?=$user_CREATION['firstname']?> <?=$user_CREATION['lastname']?></td>
    <td><?=$res1['CREATION_DATE'][0]?></td>
</tr>
<tr>
    <td>UPDATE</td>
    <td><?=$user_UPDATE_BY['firstname']?> <?=$user_UPDATE_BY['lastname']?></td>
    <td><?=$res1['UPDATE_DATE'][0]?></td>
</tr>
<?=$userdata?>
</tbody>
</table>


<br>
<div id="div_5" class="rafpart_print">OTHER INFO</div>
<br>
<div id="radioform_5" class="formdata_print">
<table class="table">
	<tr>
		<td class="param_title">Type:</td>
		<td><?=$type?></td>
	</tr>
	<? if($candidate){ ?>
	<tr class="sitenumcand">
		<td class="param_title" align="center">SITEID + CANDIDATE</td>
		<td><?=$candidate?></td>
	</tr>
	<? }else{ ?>
	<tr class="sitenumcand" style="display:none;">
		<td class="param_title">SITEID + CANDIDATE</td>
		<td><?=$candidate?></td>
	</tr>
	<? } ?>
	<tr class="replmove1">
		<td class="param_title">Region:</td>
		<td><?=$region?></td>
	</tr>
	<tr class="replmove1">
		<td class="param_title">Site number:</td>
		<td><?=$sitenum?></td>
	</tr>
	<!-- candidate alleen voor 'Dismanteling -->
	<? if ($type=="Dismantling"){ ?>
	<tr id="Candidate">
	<? }else{ ?>
	<tr id="Candidate" style="display:none;">
	<? } ?>
		<td class="param_title">Candidate:</td>
		<td><?=$sitenumcand?></td>
	</tr>
	<tr>
		<td colspan="2"><b><u>Justification/Comments:</u></b><br>
		<?=$justification?></td>
	</tr>
	<tr>
		<td class="param_title">Budget Acquisition (RTN):</td>
		<td><?=$budget_acq?></td>
	</tr>
	<tr>
		<td class="param_title">Budget Construction (RTN):</td>
		<td><?=$budget_con?></td>
	</tr>
	<tr>
		<td class="param_title">RF info:</td>
		<td><?=$RFINFO?></td>
	</tr>
	<tr>
		<td class="param_title">Commercial phase:</td>
		<td><?=$COMMERCIAL?></td>
	</tr>
</table>
</div>

<div class="rafpart_print">RADIO DATA</div>
<?
$query = "Select * FROM BSDS_RAF_RADIO WHERE RAFID = '".$_POST['rafid']."'";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_RAFS=count($res1['RAFID']);
	$XCOORD=$res1['XCOORD'][0];
	$YCOORD=$res1['YCOORD'][0];
	$ADDRESS=$res1['ADDRESS'][0];
	$RFPLAN=$res1['RFPLAN'][0];
	$CONTACT=$res1['CONTACT'][0];
	$PHONE=$res1['PHONE'][0];
	$SITETYPE=$res1['SITETYPE'][0];
	$SITESHARING=$res1['SITESHARING'][0];
	$BAND_900=$res1['BAND_900'][0];
	$BAND_1800=$res1['BAND_1800'][0];
	$BAND_UMTS=$res1['BAND_UMTS'][0];
	$BAND_UMTS900=$res1['BAND_UMTS900'][0];
	$BAND_LTE1800=$res1['BAND_LTE1800'][0];
	$BAND_LTE800=$res1['BAND_LTE800'][0];
	$BAND_LTE2600=$res1['BAND_LTE2600'][0];
	$VENDOR2G_GSM900=$res1['VENDOR2G_GSM900'][0];
	$VENDOR2G_GSM1800=$res1['VENDOR2G_GSM1800'][0];
	$VENDOR3G_UMTS=$res1['VENDOR3G_UMTS'][0];
	$VENDOR3G_UMTS900=$res1['VENDOR3G_UMTS900'][0];
	$VENDOR4G_LTE1800=$res1['VENDOR4G_LTE1800'][0];
	$VENDOR4G_LTE800=$res1['VENDOR4G_LTE800'][0];
	$VENDOR4G_LTE2600=$res1['VENDOR4G_LTE2600'][0];
	$EXPTRAFFIC=$res1['EXPTRAFFIC'][0];
	$FEATURE=$res1['FEATURE'][0];
	$PREFERREDINST=$res1['PREFERREDINST'][0];
	$CABTYPE=$res1['CABTYPE'][0];
	$CHTRX=$res1['CHTRX'][0];
	$SECTORS=$res1['SECTORS'][0];
	$REPEATER=$res1['REPEATER'][0];
	$SECTOR=$res1['SECTOR'][0];
	$COVERAGE_OBJECTIVE=$res1['COVERAGE_OBJECTIVE'][0];
	$COVERAGE_DESCR=unescape_quotes($res1['COVERAGE_DESCR'][0]);
	$FLOORS=$res1['FLOORS'][0];
	$AREAS=$res1['AREAS'][0];
	$SITETYPE2=$res1['SITETYPE2'][0];
	$AREA1_900=$res1['AREA1_900'][0];
	$AREA1_1800=$res1['AREA1_1800'][0];
	$AREA1_UMTS=$res1['AREA1_UMTS'][0];
	$AREA1_LTE1800=$res1['AREA1_LTE1800'][0];
	$AREA1_LTE800=$res1['AREA1_LTE800'][0];
	$AREA1_LTE1800=$res1['AREA1_LTE2600'][0];
	$AREA2_900=$res1['AREA2_900'][0];
	$AREA2_1800=$res1['AREA2_1800'][0];
	$AREA2_UMTS=$res1['AREA2_UMTS'][0];
	$AREA2_LTE1800=$res1['AREA2_LTE1800'][0];
	$AREA2_LTE800=$res1['AREA2_LTE800'][0];
	$AREA2_LTE2600=$res1['AREA2_LTE2600'][0];
	$AREA3_900=$res1['AREA3_900'][0];
	$AREA3_1800=$res1['AREA3_1800'][0];
	$AREA3_UMTS=$res1['AREA3_UMTS'][0];
	$AREA3_LTE1800=$res1['AREA3_LTE1800'][0];
	$AREA3_LTE800=$res1['AREA3_LTE800'][0];
	$AREA3_LTE2600=$res1['AREA3_LTE2600'][0];
	$AREA4_900=$res1['AREA4_900'][0];
	$AREA4_1800=$res1['AREA4_1800'][0];
	$AREA4_UMTS=$res1['AREA4_UMTS'][0];
	$AREA4_LTE1800=$res1['AREA4_LTE1800'][0];
	$AREA4_LTE800=$res1['AREA4_LTE800'][0];
	$AREA4_LTE1800=$res1['AREA4_LTE2600'][0];
	$COVERAGE_TUNNEL=$res1['COVERAGE_TUNNEL'][0];
	$PLANS=$res1['PLANS'][0];
	$SHARING=$res1['SHARING'][0];
	$GUIDELINES=$res1['GUIDELINES'][0];
	$COMMENTS=unescape_quotes($res1['COMMENTS'][0]);
	$CAPACITY=$res1['CAPACITY'][0];
	$INTER_900=$res1['INTER_900'][0];
	$INTER_1800=$res1['INTER_1800'][0];
	$INTER_UMTS=$res1['INTER_UMTS'][0];
	$INTER_LTE1800=$res1['INTER_LTE1800'][0];
	$INTER_LTE800=$res1['INTER_LTE800'][0];
	$INTER_LTE2600=$res1['INTER_LTE2600'][0];
	$THRESHOLD_900=$res1['THRESHOLD_900'][0];
	$THRESHOLD_1800=$res1['THRESHOLD_1800'][0];
	$THRESHOLD_UMTS=$res1['THRESHOLD_UMTS'][0];
	$THRESHOLD_LTE800=$res1['THRESHOLD_LTE800'][0];
	$THRESHOLD_LTE1800=$res1['THRESHOLD_LTE1800'][0];
	$THRESHOLD_LTE2600=$res1['THRESHOLD_LTE2600'][0];
	$COVERAGE_900=$res1['COVERAGE_900'][0];
	$COVERAGE_1800=$res1['COVERAGE_1800'][0];
	$COVERAGE_UMTS=$res1['COVERAGE_UMTS'][0];
	$COVERAGE_LTE800=$res1['COVERAGE_LTE800'][0];
	$COVERAGE_LTE1800=$res1['COVERAGE_LTE1800'][0];
	$COVERAGE_LTE2600=$res1['COVERAGE_LTE2600'][0];
	$TOTCOVERAGE_900=$res1['TOTCOVERAGE_900'][0];
	$TOTCOVERAGE_1800=$res1['TOTCOVERAGE_1800'][0];
	$TOTCOVERAGE_UMTS=$res1['TOTCOVERAGE_UMTS'][0];
	$TOTCOVERAGE_LTE800=$res1['TOTCOVERAGE_LTE800'][0];
	$TOTCOVERAGE_LTE1800=$res1['TOTCOVERAGE_LTE1800'][0];
	$TOTCOVERAGE_LTE2600=$res1['TOTCOVERAGE_LTE2600'][0];
	$POLYMAP=$res1['POLYMAP'][0];
	$NRSECTORS=$res1['NRSECTORS'][0];
	$NRSECTORS_900=$res1['NRSECTORS_900'][0];
	$NRSECTORS_1800=$res1['NRSECTORS_1800'][0];
	$NRSECTORS_UMTS=$res1['NRSECTORS_UMTS'][0];
	$NRSECTORS_LTE800=$res1['NRSECTORS_LTE800'][0];
	$NRSECTORS_LTE1800=$res1['NRSECTORS_LTE1800'][0];
	$NRSECTORS_LTE2600=$res1['NRSECTORS_LTE2600'][0];
	$HMINMAX=$res1['HMINMAX'][0];
	$HMINMAXRF=$res1['HMINMAXRF'][0];
	$ANTBLOCKING=$res1['ANTBLOCKING'][0];
	$ANGLE=$res1['ANGLE'][0];
	$RFGUIDES=$res1['RFGUIDES'][0];
	$CONGUIDES=$res1['CONGUIDES'][0];
	$TXGUIDES=$res1['TXGUIDES'][0];
	$LOC_NAME1=$res1['LOC_NAME1'][0];
	$LOC_ADDRESS1=$res1['LOC_ADDRESS1'][0];
	$LOC_STRUCTURE1=$res1['LOC_STRUCTURE1'][0];
	$LOC_PREFER1=$res1['LOC_PREFER1'][0];
	$LOC_NOTPREFER1=$res1['LOC_NOTPREFER1'][0];
	$LOC_NAME2=$res1['LOC_NAME2'][0];
	$LOC_ADDRESS2=$res1['LOC_ADDRESS2'][0];
	$LOC_STRUCTURE2=$res1['LOC_STRUCTURE2'][0];
	$LOC_PREFER2=$res1['LOC_PREFER2'][0];
	$LOC_NOTPREFER2=$res1['LOC_NOTPREFER2'][0];
	$AZCAPTILT=unescape_quotes($res1['AZCAPTILT'][0]);
	$JUSTIFICATION=unescape_quotes($res1['JUSTIFICATION'][0]);
	$BUDGET=unescape_quotes($res1['BUDGET'][0]);
	$PACCOMMENTS=unescape_quotes($res1['PACCOMMENTS'][0]);
	$CLUSTERN=$res1['CLUSTERN'][0];
	$CLUSTERNUM=$res1['CLUSTERNUM'][0];
	$CLUSTER_TARGET_DATE=$res1['CLUSTER_TARGET_DATE'][0];

	$query = "Select RADIO_FUND FROM BSDS_RAFV2 WHERE RAFID = '".$_POST['rafid']."'";
	//echo $query."<br>";
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$RADIO_FUND= $res1['RADIO_FUND'][0];

}
}

if ($POLYMAP==1){
	$POLYMAP_check="yes";
}
if ($NRSECTORS==1){
	$NRSECTORS_check="yes";
}
if ($HMINMAX==1){
	$HMINMAX_check="yes";
}
if ($ANTBLOCKING==1){
	$ANTBLOCKING_check="yes";
}
if ($RFGUIDES==1){
	$RFGUIDES_check="yes";
}
if ($CONGUIDES==1){
	$CONGUIDES_check="yes";
}
?>

<div id="div_5" class="subtitle_print">1. SITE DATA</div>
<br>
<div id="radioform_5" class="formdata_print">
	<table class="table">
	<tr>
		<td class="param_title">Nominal Lambert Coordinates</td>
		<td>X: <?=$XCOORD?> &nbsp;&nbsp;Y: <?=$YCOORD?></td>
	</tr>
	<tr>
		<td class="param_title">Band for acquisition</td>
		<td><?php 
			if ($BAND_900==1){
				echo "G9 ";
			}
			if ($BAND_1800==1){
				echo "G18 ";
			}
			if ($BAND_900==1){
				echo "G9 ";
			}
			if ($BAND_UMTS900==1){
				echo "U9 ";
			}
			if ($BAND_UMTS==1){
				echo "U21 ";
			} 
			if ($BAND_LTE800==1){
				echo "L8 ";
			}
			if ($BAND_LTE1800==1){
				echo "L18 ";
			}
			if ($BAND_LTE2600==1){
				echo "L26 ";
			}
			?>
	</tr>
	<tr>
		<td class="param_title">Cluster</td>
		<td><?=$CLUSTERN?><?=$CLUSTERNUM?></td>
	</tr>
	<tr>
		<td class="param_title">Cluster Target Date</td>
		<td><?=$CLUSTER_TARGET_DATE?></td>
	</tr>
	<tr>
		<td class="param_title">Band funded for Construction</td>
		<td><?=$RADIO_FUND?></td>
	</tr>
	<?php if($VENDOR2G_GSM900!='Please select' && $VENDOR2G_GSM900!=''){ ?>
	<tr>
		<td class="param_title">2G vendor GSM900</td>
		<td><?=$VENDOR2G_GSM900?></td>
	</tr>
	<?php } 
	if($VENDOR2G_GSM1800!='Please select' && $VENDOR2G_GSM1800!=''){ ?>
	<tr>
		<td class="param_title">2G vendor GSM1800</td>
		<td><?=$VENDOR2G_GSM1800?></td>
	</tr>
	<?php } 
	if($VENDOR3G_UMTS!='Please select' && $VENDOR3G_UMTS!=''){ ?>
	<tr>
		<td class="param_title">3G vendor UMTS2100</td>
		<td><?=$VENDOR3G_UMTS?></td>
	</tr>
	<?php } 
	if($VENDOR3G_UMTS900!='Please select' && $VENDOR3G_UMTS900!=''){ ?>
	<tr>
		<td class="param_title">3G vendor UMTS900</td>
		<td><?=$VENDOR3G_UMTS900?></td>
	</tr>
	<?php } 
	if($VENDOR4G_LTE800!='Please select' && $VENDOR4G_LTE800!=''){ ?>
	<tr>
		<td class="param_title">4G vendor LTE800</td>
		<td><?=$VENDOR4G_LTE800?></td>
	</tr>
	<?php } 
	if($VENDOR4G_LTE1800!='Please select' && $VENDOR4G_LTE1800!=''){ ?>
	<tr>
		<td class="param_title">4G vendor LTE1800</td>
		<td><?=$VENDOR4G_LTE1800?></td>
	</tr>
	<?php } 
	if($VENDOR4G_LTE2600!='Please select' && $VENDOR4G_LTE2600!=''){ ?>
	<tr>
		<td class="param_title">4G vendor LTE2600</td>
		<td><?=$VENDOR4G_LTE2600?></td>
	</tr>
	<?php } ?>
	</table>
</div>


<div id="div_7" class="subtitle_print <?=$changeable_1_7?>">2. COVERAGE OBJECTIVES</div>
<br>
<div id="radioform_7" class="formdata_print">
	<table class="table">
	<tr>
		<td><span class="param_title">Description:</span><br>
		<?=$COVERAGE_DESCR?><?=$COVERAGE_OBJECTIVE?></td>
	</tr>
	</table>
</div>



<div id="div_6" class="subtitle_print <?=$changeable_1_7?>">3. COVERAGE KPI'S</div>
<br>
<div id="radioform_6" class="formdata_print">
<b><u><?=$SITETYPE2?></u></b>
<br>
	<? if ($SITETYPE2=="polygon1"){ ?>
	<table class="table">
	<tr>
		<td class="param_title">Band</td>
		<td class="param_title">Type of Coverage</td>
		<td class="param_title">P(BCCH) / P(CPICH) carrier<br> threshold better than</td>
		<td class="param_title">% of the area</td>
	<tr>
		<td>GSM900</td>
		<td>Indoor</td>
		<td>-71 dBm</td>
		<td><?=$AREA1_900?></td>
	</tr>
	<tr>
		<td>GSM1800</td>
		<td>Indoor</td>
		<td>-66 dBm</td>
		<td><?=$AREA1_1800?></td>
	</tr>
	<tr>
		<td>UMTS</td>
		<td>Indoor</td>
		<td>-75 dBm</td>
		<td><?=$AREA1_UMTS?></td>
	</tr>
	<tr>
		<td>LTE800</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA1_LTE800?></td>
	</tr>
	<tr>
		<td>LTE1800</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA1_LTE1800?></td>
	</tr>
	<tr>
		<td>LTE2600</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA1_LTE2600?></td>
	</tr>
	</table>
	<? }
	if ($SITETYPE2=="polygon2"){ ?>
	<table class="polygon2" <?=$view_poly2?>>
	<tr>
		<td class="param_title">Band</td>
		<td class="param_title">Type of Coverage</td>
		<td class="param_title">P(BCCH) / P(CPICH) carrier<br> threshold better than</td>
		<td class="param_title">% of the area</td>
	</tr>
	<tr>
		<td>GSM900</td>
		<td>Indoor</td>
		<td>-76 dBm</td>
		<td><?=$AREA2_900?></td>
	</tr>
	<tr>
		<td>GSM1800</td>
		<td>Indoor</td>
		<td>-71 dBm</td>
		<td><?=$AREA2_1800?></td>
	</tr>
	<tr>
		<td>UMTS</td>
		<td>Indoor</td>
		<td>-84 dBm</td>
		<td><?=$AREA2_UMTS?></td>
	</tr>
	<tr>
		<td>LTE800</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA2_LTE800?></td>
	</tr>
	<tr>
		<td>LTE1800</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA2_LTE1800?></td>
	</tr>
	<tr>
		<td>LTE2600</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA2_LTE2600?></td>
	</tr>
	</table>
	<? }
	if ($SITETYPE2=="polygon3"){ ?>
	<table class="polygon3" <?=$view_poly3?>>
	<tr>
		<td class="param_title">Band</td>
		<td class="param_title">Type of Coverage</td>
		<td class="param_title">P(BCCH) / P(CPICH) carrier<br> threshold better than</td>
		<td class="param_title">% of the area</td>
	</tr>
	<tr>
		<td>GSM900</td>
		<td>Indoor</td>
		<td>-79 dBm</td>
		<td></td><?=$AREA3_900?>/td>
	</tr>
	<tr>
		<td>GSM1800</td>
		<td>Indoor</td>
		<td>-75 dBm</td>
		<td><?=$AREA3_1800?></td>
	</tr>
	<tr>
		<td>UMTS</td>
		<td>Indoor</td>
		<td>-88 dBm</td>
		<td><?=$AREA3_UMTS?></td>
	</tr>
	<tr>
		<td>LTE800</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA3_LTE800?></td>
	</tr>
	<tr>
		<td>LTE1800</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA3_LTE1800?></td>
	</tr>
	<tr>
		<td>LTE2600</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA3_LTE2600?></td>
	</tr>
	</table>
	<? }
	if ($SITETYPE2=="polygon4"){ ?>
	<table class="polygon4" <?=$view_poly4?>>
	<tr>
		<td class="param_title">Band</td>
		<td class="param_title">Type of Coverage</td>
		<td class="param_title">P(BCCH) / P(CPICH) carrier<br> threshold better than</td>
		<td class="param_title">% of the area</td>
	</tr>
	<tr>
		<td>GSM900</td>
		<td>Indoor</td>
		<td>-79 dBm</td>
		<td><?=$AREA4_900?></td>
	</tr>
	<tr>
		<td>GSM1800</td>
		<td>Indoor</td>
		<td>-75 dBm</td>
		<td><?=$AREA4_1800?></td>
	</tr>
	<tr>
		<td>UMTS</td>
		<td>Indoor</td>
		<td>-88 dBm</td>
		<td><?=$AREA4_UMTS?></td>
	</tr>
	<tr>
		<td>LTE800</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA4_LTE800?></td>
	</tr>
	<tr>
		<td>LTE1800</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA4_LTE1800?></td>
	</tr>
	<tr>
		<td>LTE2600</td>
		<td>Indoor</td>
		<td>-dBm</td>
		<td><?=$AREA4_LTE2600?></td>
	</tr>
	</table>
	<? } ?>
</div>


<div id="div_9" class="subtitle_print <?=$changeable_1_7?>">4. INTERFERENCE KPI's OUTDOOR SITE</div>
<br>
<div id="radioform_9" class="formdata_print">
	<table class="table">
	<tr>
		<td class="param_title">Applicable</td>
		<td class="param_title">Band</td>
		<td class="param_title">Threshold</td>
		<td class="param_title">Total area of coverage above threshold (%)</td>
		<td class="param_title">Total area of coverage above threshold (%)<br>(deep indoor, indoor residential and incar)</td>
	</tr>
	<?php if ($INTER_900_check!=''){ ?>
	<tr>
		<td><?=$INTER_900_check?></td>
		<td>GSM900</td>
		<td><?=$THRESHOLD_900?></td>
		<td><?=$COVERAGE_900?></td>
		<td><?=$TOTCOVERAGE_900?></td>
	</tr>
	<?php } 
	if ($INTER_1800_check!=''){
	?>
	<tr>
		<td><?=$INTER_1800_check?></td>
		<td>GSM1800</td>
		<td><?=$THRESHOLD_1800?></td>
		<td><?=$COVERAGE_1800?></td>
		<td><?=$TOTCOVERAGE_1800?></td>
	</tr>
	<?php } 
	if ($INTER_UMTS_check!=''){
	?>
	<tr>
		<td><?=$INTER_UMTS_check?></td>
		<td>UMTS</td>
		<td><?=$THRESHOLD_UMTS?></td>
		<td><?=$COVERAGE_UMTS?></td>
		<td><?=$TOTCOVERAGE_UMTS?></td>
	</tr>
	<?php } 
	if ($INTER_LTE800_check!=''){
	?>
	<tr>
		<td><?=$INTER_LTE800_check?></td>
		<td>LTE1800</td>
		<td><?=$THRESHOLD_LTE800?></td>
		<td><?=$COVERAGE_LTE800?></td>
		<td><?=$TOTCOVERAGE_LTE800?></td>
	</tr>
	<?php } 
	if ($INTER_LTE1800_check!=''){
	?>
	<tr>
		<td><?=$INTER_LTE1800_check?></td>
		<td>LTE1800</td>
		<td><?=$THRESHOLD_LTE1800?></td>
		<td><?=$COVERAGE_LTE1800?></td>
		<td><?=$TOTCOVERAGE_LTE1800?></td>
	</tr>
	<?php } 
	if ($INTER_LTE2600_check!=''){
	?>
	<tr>
		<td><?=$INTER_LTE2600_check?></td>
		<td>LTE2600</td>
		<td><?=$THRESHOLD_LTE2600?></td>
		<td><?=$COVERAGE_LTE2600?></td>
		<td><?=$TOTCOVERAGE_LTE2600?></td>
	</tr>
	<?php } ?>
	</table>
</div>

<? if ($_GET['raftype']!="indoor"){ ?>
<div id="div_10" class="subtitle_print <?=$changeable_1_7?>">5. COMMENTS</div>
<br>
<div id="radioform_10" class="formdata_print">
	<table class="table tunnel">
	<tr>
		<td class="param_title" colspan="2">Other comments</td>
	</tr>
	<tr>
		<td colspan="2"><?=$COMMENTS?></td>
	</tr>
	</table>
</div>

<div id="div_11" class="subtitle_print <?=$changeable_1_7?>">6. CHECKLIST</div>
<br>
<div id="radioform_11" class="formdata_print">
    <table class="table tunnel">
	<tr>
		<td><?=$POLYMAP_check?></td>
		<td>Map attached with polygons indicated</td>
	</tr>
	<tr>
		<td><?=$NRSECTORS_check?></td>
		<td># of sectors &nbsp; GSM900 <?=$NRSECTORS_900?> &nbsp; GSM1800<?=$NRSECTORS_1800?> &nbsp; UMTS<?=$NRSECTORS_UMTS?> &nbsp; LTE800<?=$NRSECTORS_LTE800?> &nbsp; LTE1800<?=$NRSECTORS_LTE1800?> &nbsp; LTE2600<?=$NRSECTORS_LTE2600?></td>
	</tr>
	<tr>
		<td><?=$HMINMAX_check?></td>
		<td>Hmin / Hmax for RF antennas: <?=$HMINMAXRF?></td>
	</tr>
	<tr>
		<td><?=$ANTBLOCKING_check?></td>
		<td>Antenna blocling by nearby building (angle):  <?=$ANGLE?></td>
	</tr>
	<tr>
		<td><?=$RFGUIDES_check?></td>
		<td>RF Guidlines compliant</td>
	</tr>
	<tr>
		<td><?=$CONGUIDES_check?></td>
		<td>Construction Guidlines compliant</td>
	</tr>
	</table>
</div>


<div id="div_12" class="subtitle_print <?=$changeable_1_7?>">7. POSSIBLE LOCATIONS</div>
<br>
<div id="radioform_12" class="formdata_print">
<table class="table">
	<tr>
		<td class="param_title">&nbsp;</td>
		<td class="param_title">Name</td>
		<td class="param_title">Address</td>
		<td class="param_title">Type of structure</td>
		<td class="param_title">Preferred</td>
		<td class="param_title">Not preferrerd</td>
	</tr>
	<tr>
		<td class="param_title">1</td>
		<td><?=$LOC_NAME1?></td>
		<td><?=$LOC_ADDRESS1?></td>
		<td><?=$LOC_STRUCTURE1?></td>
		<td><?=$LOC_PREFER1?></td>
		<td><?=$LOC_NOTPREFER1?></td>
	</tr>
	<tr>
		<td class="param_title">2</td>
		<td><?=$LOC_NAME2?></td>
		<td><?=$LOC_ADDRESS2?></td>
		<td><?=$LOC_STRUCTURE2?></td>
		<td><?=$LOC_PREFER2?></td>
		<td><?=$LOC_NOTPREFER2?></td>
	</tr>
</table>
</div>

<div id="div_14" class="subtitle_print <?=$changeable_8_9?>">8. AZIMUTH, CAPACITY AND TILT PROPOSAL OPTIMISATION BASE</div>
<br>
<div id="radioform_14" class="formdata_print">
	<?=$AZCAPTILT?>
</div>
<? } ?>

<div id="div_15" class="subtitle_print <?=$changeable_8_9?>">9. ACCEPTANCE / REJECTION/ JUSTIFICATION ON VALIDATION BY BASE</div>
<br>
<div id="radioform_15" class="formdata_print">
	<?=$JUSTIFICATION?>
</div>



<div id="div_16" class="subtitle_print <?=$changeable_10_11?>">10. FREQUECIES AND PARAMETERS</div>
<br>
<div id="radioform_16" class="formdata_print">
	Input Frequencies and Parameters by Base: See Asset<br><br>
	NOTE: Frequencies and parameters to be checked with BASE OPTIM prior to DT creation.<br>
</div>

<div id="div_36" class="subtitle_print <?=$changeable_10_11?>">11. BUDGET INFO</div>
<br>
<div id="radioform_36" class="formdata_print">
	<?=$BUDGET?>
</div>

<div id="div_35" class="subtitle_print <?=$changeable_12?>">12. PAC COMMENTS</div>
<br>
<div id="radioform_35" class="formdata_print">
	<?=$PACCOMMENTS?>
</div>

<div class="rafpart_print">TXMN DATA</div>
<?
$query = "Select * FROM BSDS_RAF_TXMN WHERE RAFID = '".$_POST['rafid']."'";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
   OCIFreeStatement($stmt);
   $amount_of_RAFS=count($res1['RAFID']);
   $RAFID                =$res1['RAFID'][0];
   $UPG_DATE             =$res1['UPG_DATE'][0];
   $UPG_BY               =$res1['UPG_BY'][0];
   $GRANTED_BEARING1     =$res1['GRANTED_BEARING1'][0];
   $GRANTED_DIAMETER1    =$res1['GRANTED_DIAMETER1'][0];
   $GRANTED_HEIGHT1    =$res1['GRANTED_HEIGHT1'][0];
   $GRANTED_BEARING2     =$res1['GRANTED_BEARING2'][0];
   $GRANTED_DIAMETER2    =$res1['GRANTED_DIAMETER2'][0];
   $GRANTED_HEIGHT2    =$res1['GRANTED_HEIGHT2'][0];
   $GRANTED_BEARING3     =$res1['GRANTED_BEARING3'][0];
   $GRANTED_DIAMETER3    =$res1['GRANTED_DIAMETER3'][0];
   $GRANTED_HEIGHT3    =$res1['GRANTED_HEIGHT3'][0];
   $GRANTED_BEARING4     =$res1['GRANTED_BEARING4'][0];
   $GRANTED_DIAMETER4    =$res1['GRANTED_DIAMETER4'][0];
   $GRANTED_HEIGHT4    =$res1['GRANTED_HEIGHT4'][0];
   $GRANTED_BEARING5     =$res1['GRANTED_BEARING5'][0];
   $GRANTED_DIAMETER5    =$res1['GRANTED_DIAMETER5'][0];
   $GRANTED_HEIGHT5    =$res1['GRANTED_HEIGHT5'][0];
   $GRANTED_BEARING6     =$res1['GRANTED_BEARING6'][0];
   $GRANTED_DIAMETER6    =$res1['GRANTED_DIAMETER6'][0];
   $GRANTED_HEIGHT6    =$res1['GRANTED_HEIGHT6'][0];
   $GRANTED_BEARING7     =$res1['GRANTED_BEARING7'][0];
   $GRANTED_DIAMETER7    =$res1['GRANTED_DIAMETER7'][0];
   $GRANTED_HEIGHT7    =$res1['GRANTED_HEIGHT7'][0];
   $GRANTED_BEARING8     =$res1['GRANTED_BEARING8'][0];
   $GRANTED_DIAMETER8    =$res1['GRANTED_DIAMETER8'][0];
   $GRANTED_HEIGHT8    =$res1['GRANTED_HEIGHT8'][0];
   $GRANTED_BEARING9     =$res1['GRANTED_BEARING9'][0];
   $GRANTED_DIAMETER9   =$res1['GRANTED_DIAMETER9'][0];
   $GRANTED_HEIGHT9   =$res1['GRANTED_HEIGHT9'][0];
   $GRANTED_BEARING10    =$res1['GRANTED_BEARING10'][0];
   $GRANTED_DIAMETER10    =$res1['GRANTED_DIAMETER10'][0];
   $GRANTED_HEIGHT10    =$res1['GRANTED_HEIGHT10'][0];
   $GRANTED_BEARING11    =$res1['GRANTED_BEARING11'][0];
   $GRANTED_DIAMETER11    =$res1['GRANTED_DIAMETER11'][0];
   $GRANTED_HEIGHT11      =$res1['$GRANTED_HEIGHT11'][0];
   $GRANTED_BEARING12     =$res1['GRANTED_BEARING12'][0];
   $GRANTED_HEIGHT12    =$res1['GRANTED_HEIGHT12'][0];
   $GRANTED_DIAMETER12    =$res1['GRANTED_DIAMETER12'][0];
   $ADDITIONAL_BEARING1  =$res1['ADDITIONAL_BEARING1'][0];
   $ADDITIONAL_DIAMETER1 =$res1['ADDITIONAL_DIAMETER1'][0];
   $ADDITIONAL_HEIGHT1 =$res1['ADDITIONAL_HEIGHT1'][0];
   $ADDITIONAL_BEARING2  =$res1['ADDITIONAL_BEARING2'][0];
   $ADDITIONAL_DIAMETER2 =$res1['ADDITIONAL_DIAMETER2'][0];
   $ADDITIONAL_HEIGHT2 =$res1['ADDITIONAL_HEIGHT2'][0];
   $ADDITIONAL_BEARING3  =$res1['ADDITIONAL_BEARING3'][0];
   $ADDITIONAL_DIAMETER3 =$res1['ADDITIONAL_DIAMETER3'][0];
   $ADDITIONAL_HEIGHT3 =$res1['ADDITIONAL_HEIGHT3'][0];
   $ADDITIONAL_BEARING4  =$res1['ADDITIONAL_BEARING4'][0];
   $ADDITIONAL_DIAMETER4 =$res1['ADDITIONAL_DIAMETER4'][0];
   $ADDITIONAL_HEIGHT4 =$res1['ADDITIONAL_HEIGHT4'][0];
   $ADDITIONAL_BEARING5  =$res1['ADDITIONAL_BEARING5'][0];
   $ADDITIONAL_DIAMETER5 =$res1['ADDITIONAL_DIAMETER5'][0];
   $ADDITIONAL_HEIGHT5 =$res1['ADDITIONAL_HEIGHT5'][0];
   $ADDITIONAL_BEARING6  =$res1['ADDITIONAL_BEARING6'][0];
   $ADDITIONAL_DIAMETER6 =$res1['ADDITIONAL_DIAMETER6'][0];
   $ADDITIONAL_HEIGHT6 =$res1['ADDITIONAL_HEIGHT6'][0];
   $ADDITIONAL_BEARING7  =$res1['ADDITIONAL_BEARING7'][0];
   $ADDITIONAL_DIAMETER7 =$res1['ADDITIONAL_DIAMETER7'][0];
   $ADDITIONAL_HEIGHT7 =$res1['ADDITIONAL_HEIGHT7'][0];
   $ADDITIONAL_BEARING8  =$res1['ADDITIONAL_BEARING8'][0];
   $ADDITIONAL_DIAMETER8 =$res1['ADDITIONAL_DIAMETER8'][0];
   $ADDITIONAL_HEIGHT8 =$res1['ADDITIONAL_HEIGHT8'][0];
   $ADDITIONAL_BEARING9  =$res1['ADDITIONAL_BEARING9'][0];
   $ADDITIONAL_DIAMETER9 =$res1['ADDITIONAL_DIAMETER9'][0];
   $ADDITIONAL_HEIGHT9 =$res1['ADDITIONAL_HEIGHT9'][0];
   $ADDITIONAL_BEARING10  =$res1['ADDITIONAL_BEARING10'][0];
   $ADDITIONAL_DIAMETER10 =$res1['ADDITIONAL_DIAMETER10'][0];
   $ADDITIONAL_HEIGHT10 =$res1['ADDITIONAL_HEIGHT10'][0];
   $ADDITIONAL_BEARING11  =$res1['ADDITIONAL_BEARING11'][0];
   $ADDITIONAL_DIAMETER11 =$res1['ADDITIONAL_DIAMETER11'][0];
   $ADDITIONAL_HEIGHT11 =$res1['ADDITIONAL_HEIGHT11'][0];
   $ADDITIONAL_BEARING12  =$res1['ADDITIONAL_BEARING12'][0];
   $ADDITIONAL_DIAMETER12 =$res1['ADDITIONAL_DIAMETER12'][0];
   $ADDITIONAL_HEIGHT12 =$res1['ADDITIONAL_HEIGHT12'][0];
   $EXISTING_CAB1        =$res1['EXISTING_CAB1'][0];
   $EXISTING_AMOUNT1     =$res1['EXISTING_AMOUNT1'][0];
   $EXISTING_CAB2        =$res1['EXISTING_CAB2'][0];
   $EXISTING_AMOUNT2     =$res1['EXISTING_AMOUNT2'][0];
   $ADDITIONAL_CAB1      =$res1['ADDITIONAL_CAB1'][0];
   $ADDITIONAL_AMOUNT1   =$res1['ADDITIONAL_AMOUNT1'][0];
   $ADDITIONAL_CAB2      =$res1['ADDITIONAL_CAB2'][0];
   $ADDITIONAL_AMOUNT2   =$res1['ADDITIONAL_AMOUNT2'][0];
   $SPECIFIC_TXMN        =unescape_quotes($res1['SPECIFIC_TXMN'][0]);
   $HMIN                 =$res1['HMIN'][0];
   $HMINDISH             =$res1['HMINDISH'][0];
   $TXMNGUIDES           =$res1['TXMNGUIDES'][0];
   $BUDGET               =unescape_quotes($res1['BUDGET'][0]);
}


if ($HMIN==1){
	$HMIN_check="yes";
}
if ($TXMNGUIDES==1){
	$TXMNGUIDES_check="yes";
}

if ($_GET['raftype']!="indoor"){ ?>
<div id="div_17" class="subtitle_print">1. GRANTED MICROWAVE DISHES</div>
<br>
<div id="radioform_17" class="formdata_print">
	<table class="table">
	<tr>
		<td class="param_title">&nbsp;</td>
		<td class="param_title">Dish 1</td>
		<td class="param_title">Dish 2</td>
		<td class="param_title">Dish 3</td>
		<td class="param_title">Dish 4</td>
	</tr>
	<tr>
		<td class="param_title">Bearing</td>
		<td><?=$GRANTED_BEARING1?></td>
		<td><?=$GRANTED_BEARING2?></td>
		<td><?=$GRANTED_BEARING3?></td>
		<td><?=$GRANTED_BEARING4?></td>
	</tr>
	<tr>
		<td class="param_title">Antenna diameter</td>
		<td><?=$GRANTED_DIAMETER1?></td>
		<td><?=$GRANTED_DIAMETER2?></td>
		<td><?=$GRANTED_DIAMETER3?></td>
		<td><?=$GRANTED_DIAMETER4?></td>
	</tr>
	<tr>
		<td class="param_title">Antenna Hieight</td>
		<td><?=$GRANTED_HEIGHT1?></td>
		<td><?=$GRANTED_HEIGHT2?></td>
		<td><?=$GRANTED_HEIGHT3?></td>
		<td><?=$GRANTED_HEIGHT4?></td>
	</tr>
	</table>
	<table class="table">
	<tr>
		<td class="param_title">&nbsp;</td>
		<td class="param_title">Dish 5</td>
		<td class="param_title">Dish 6</td>
		<td class="param_title">Dish 7</td>
		<td class="param_title">Dish 8</td>
	</tr>
	<tr>
		<td class="param_title">Bearing</td>
		<td><?=$GRANTED_BEARING5?></td>
		<td><?=$GRANTED_BEARING6?></td>
		<td><?=$GRANTED_BEARING7?></td>
		<td><?=$GRANTED_BEARING8?></td>
	</tr>
	<tr>
		<td class="param_title">Antenna diameter</td>
		<td><?=$GRANTED_DIAMETER5?></td>
		<td><?=$GRANTED_DIAMETER6?></td>
		<td><?=$GRANTED_DIAMETER7?></td>
		<td><?=$GRANTED_DIAMETER8?></td>
	</tr>
	<tr>
		<td class="param_title">Antenna Hieight</td>
		<td><?=$GRANTED_HEIGHT5?></td>
		<td><?=$GRANTED_HEIGHT6?></td>
		<td><?=$GRANTED_HEIGHT7?></td>
		<td><?=$GRANTED_HEIGHT8?></td>
	</tr>
	</table>
	<table class="table">
		<tr>
			<td class="param_title">&nbsp;</td>
			<td class="param_title">Dish 9</td>
			<td class="param_title">Dish 10</td>
			<td class="param_title">Dish 11</td>
			<td class="param_title">Dish 12</td>
		</tr>
		<tr>
			<td class="param_title">Bearing</td>
			<td><?=$GRANTED_BEARING9?></td>
			<td><?=$GRANTED_BEARING10?></td>
			<td><?=$GRANTED_BEARING11?></td>
			<td><?=$GRANTED_BEARING12?></td>
		</tr>
		<tr>
			<td class="param_title">Antenna diameter</td>
			<td><?=$GRANTED_DIAMETER9?></td>
			<td><?=$GRANTED_DIAMETER10?></td>
			<td><?=$GRANTED_DIAMETER11?></td>
			<td><?=$GRANTED_DIAMETER12?></td>
		</tr>
		<tr>
		<td class="param_title">Antenna Hieight</td>
		<td><?=$GRANTED_HEIGHT9?></td>
		<td><?=$GRANTED_HEIGHT10?></td>
		<td><?=$GRANTED_HEIGHT11?></td>
		<td><?=$GRANTED_HEIGHT12?></td>
	</tr>
	</table>
</div>

<div id="div_18" class="subtitle_print">2. ADDITIONAL MICROWAVE DISHES</div>
<br>
<div id="radioform_18" class="formdata_print">
	<table class="table">
	<tr>
		<td class="param_title">&nbsp;</td>
		<td class="param_title">Dish 1</td>
		<td class="param_title">Dish 2</td>
		<td class="param_title">Dish 3</td>
		<td class="param_title">Dish 4</td>
	</tr>
	<tr>
		<td class="param_title">Bearing</td>
		<td><?=$ADDITIONAL_BEARING1?></td>
		<td><?=$ADDITIONAL_BEARING2?></td>
		<td><?=$ADDITIONAL_BEARING3?></td>
		<td><?=$ADDITIONAL_BEARING4?></td>
	</tr>
	<tr>
		<td class="param_title">Antenna diameter</td>
		<td><?=$ADDITIONAL_DIAMETER1?></td>
		<td><?=$ADDITIONAL_DIAMETER2?></td>
		<td><?=$ADDITIONAL_DIAMETER3?></td>
		<td><?=$ADDITIONAL_DIAMETER4?></td>
	</tr>
	<tr>
		<td class="param_title">Antenna height</td>
		<td><?=$ADDITIONAL_HEIGHT1?></td>
		<td><?=$ADDITIONAL_HEIGHT2?></td>
		<td><?=$ADDITIONAL_HEIGHT3?></td>
		<td><?=$ADDITIONAL_HEIGHT4?></td>
	</tr>
	</table>
	<table class="table">
	<tr>
		<td class="param_title">&nbsp;</td>
		<td class="param_title">Dish 5</td>
		<td class="param_title">Dish 6</td>
		<td class="param_title">Dish 7</td>
		<td class="param_title">Dish 8</td>
	</tr>
	<tr>
		<td class="param_title">Bearing</td>
		<td><?=$ADDITIONAL_BEARING5?></td>
		<td><?=$ADDITIONAL_BEARING6?></td>
		<td><?=$ADDITIONAL_BEARING7?></td>
		<td><?=$ADDITIONAL_BEARING8?></td>
	</tr>
	<tr>
		<td class="param_title">Antenna diameter</td>
		<td><?=$ADDITIONAL_DIAMETER5?></td>
		<td><?=$ADDITIONAL_DIAMETER6?></td>
		<td><?=$ADDITIONAL_DIAMETER7?></td>
		<td><?=$ADDITIONAL_DIAMETER8?></td>
	</tr>
	<tr>
		<td class="param_title">Antenna height</td>
		<td><?=$ADDITIONAL_HEIGHT5?></td>
		<td><?=$ADDITIONAL_HEIGHT6?></td>
		<td><?=$ADDITIONAL_HEIGHT7?></td>
		<td><?=$ADDITIONAL_HEIGHT8?></td>
	</tr>
	</table>
	<table class="table">
		<tr>
			<td class="param_title">&nbsp;</td>
			<td class="param_title">Dish 9</td>
			<td class="param_title">Dish 10</td>
			<td class="param_title">Dish 11</td>
			<td class="param_title">Dish 12</td>
		</tr>
		<tr>
			<td class="param_title">Bearing</td>
			<td><?=$ADDITIONAL_BEARING9?></td>
			<td><?=$ADDITIONAL_BEARING10?></td>
			<td><?=$ADDITIONAL_BEARING11?></td>
			<td><?=$ADDITIONAL_BEARING12?></td>
		</tr>
		<tr>
			<td class="param_title">Antenna diameter</td>
			<td><?=$ADDITIONAL_DIAMETER9?></td>
			<td><?=$ADDITIONAL_DIAMETER10?></td>
			<td><?=$ADDITIONAL_DIAMETER11?></td>
			<td><?=$ADDITIONAL_DIAMETER12?></td>
		</tr>
		<tr>
			<td class="param_title">Antenna height</td>
			<td><?=$ADDITIONAL_HEIGHT9?></td>
			<td><?=$ADDITIONAL_HEIGHT10?></td>
			<td><?=$ADDITIONAL_HEIGHT11?></td>
			<td><?=$ADDITIONAL_HEIGHT12?></td>
		</tr>
	</table>
</div>

<div id="div_19" class="subtitle_print">3. EXISTING TXMN CABINETS</div>
<br>
<div id="radioform_19" class="formdata_print">
<table class="table">
	<tr>
		<td class="param_title">Cabinet type</td>
		<td class="param_title">Amount</td>
	</tr>
	<tr>
		<td><?=$EXISTING_CAB1?></td>
		<td><?=$EXISTING_AMOUNT1?></td>
	</tr>
	<tr>
		<td><?=$EXISTING_CAB2?></td>
		<td><?=$EXISTING_AMOUNT2?></td>
	</tr>
</table>
</div>

<div id="div_20" class="subtitle_print">4. ADDITIONAL TXMN CABINETS</div>
<br>
<div id="radioform_20" class="formdata_print">
<table class="table">
	<tr>
		<td class="param_title">Cabinet type</td>
		<td class="param_title">Amount</td>
	</tr>
	<tr>
		<td><?=$ADDITIONAL_CAB1?></td>
		<td><?=$ADDITIONAL_AMOUNT1?></td>
	</tr>
	<tr>
		<td><?=$ADDITIONAL_CAB2?></td>
		<td><?=$ADDITIONAL_AMOUNT2?></td>
	</tr>
</table>
</div>
<? } ?>

<? if ($_GET['raftype']!="indoor"){ ?>
<div id="div_22" class="subtitle_print">5. CHECKLIST</div>
<br>
<div id="radioform_22" class="formdata_print">
	<table class="table tunnel">
	<tr>
		<td><?=$HMIN_check?></td>
		<td>Hmin for MW dishes: <?=$HMINDISH?></td>
	</tr>
	<tr>
		<td><?=$TXMNGUIDES_check?></td>
		<td>TX Guidlines compliant</td>
	</tr>
	</table>
</div>
<? } ?>

<div id="div_21" class="subtitle_print">6. SPECIFIC TXMN REQUIREMENTS</div>
<br>
<div id="radioform_21" class="formdata_print">

<table class="table">
	<tr>
		<td><?=$SPECIFIC_TXMN?></td>
	</tr>
</table>
</div>

<div id="div_14" class="subtitle_print">7. BUDGET INFO</div>
<br>
<div id="radioform_14" class="formdata_print">
	<?=$BUDGET?>
</div>


<div class="rafpart_print">IMAGES</div>
<?
$filelist=getFileList($config['raf_folder_abs'].$_POST['rafid']);

for( $i=0; $i < count($filelist) ; $i++ ) {
	if (($filelist[$i]['type']=="jpg" || $filelist[$i]['type']=="gif" || $filelist[$i]['type']=="png") && substr_count($filelist[$i]['name'], 'ori')==1){
		echo "<IMG src='".$config['sitepath_url']."/infobase/files/raf/".$_POST['rafid']."/".$filelist[$i]['name']."'><br>";
	}
}

?>
<div class="rafpart_print">TRX REQUIREMENTS</div>

<?
$query = "Select * FROM BSDS_RAF_TRX WHERE RAFID = '".$_POST['rafid']."' ORDER BY DATE_OF_SAVE DESC";
//echo $query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
   OCIFreeStatement($stmt);
   $amount_of_RAFS_TRX=count($res1['RAFID']);
   for ($i = 0; $i <$amount_of_RAFS_TRX; $i++) {
   	echo "On ".$res1['DATE_OF_SAVE'][$i]." by ".$res1['UPDATE_BY'][$i].":<br>". $res1['REQUIREMENTS'][$i]."<br>";
   }
}
?>
</div>


<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


if ($res1['BUFFER'][0]==1){
	$buffer="checked";
}

if ($_POST['rafid']){
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
		$type=$res1['TYPE'][0];
		$rafid=$_POST['rafid'];


		if (strpos($res1['RADIO_FUND'][0],"G9")!==false){
			$RADIO_FUND_G9="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"G18")!==false){
			$RADIO_FUND_G18="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"U9")!==false){
			$RADIO_FUND_U9="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"U21")!==false){
			$RADIO_FUND_U21="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"L8")!==false){
			$RADIO_FUND_L8="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"L18")!==false){
			$RADIO_FUND_L18="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"L26")!==false){
			$RADIO_FUND_L26="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"EXISTING")!==false){
			$RADIO_FUND_EXISTING="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"ANT")!==false){
			$RADIO_FUND_ANT="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"CWK")!==false){
			$RADIO_FUND_CWK="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"CTX")!==false){
			$RADIO_FUND_CTX="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"DISM")!==false){
			$RADIO_FUND_DISM="checked";
		}
		if (strpos($res1['RADIO_FUND'][0],"CAB")!==false){
			$RADIO_FUND_CAB="checked";
		}
	}
}else{
	$sitenum=substr($_POST['siteid'],2,4);
	$region=substr($_POST['siteid'],0,2);
	$rafid="";
}

if ($amount_of_RAFS>=1){
	$action="Update";
}else{
	$action="Create";
}

if (substr_count($guard_groups, 'Base_other')==1){
	$group="Base_other";
}else if (substr_count($guard_groups, 'Base_RF')==1){
	$group="Base_RF";
}else if (substr_count($guard_groups, 'Base_TXMN')==1){
	$group="Base_TXMN";
}

if ((substr($sitenum,0,1)==8 or substr($sitenum,0,1)==7) && $sitenum!='8557' && $sitenum!='8509'){
	$onlyIndoor="yes";
}

$query = "select DISTINCT(RFINFO) from BSDS_RAFV2 WHERE RFINFO IS NOT NULL ORDER BY RFINFO";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
    $amount_of_RFINFO=count($res2['RFINFO']);
    for ($i = 0; $i <$amount_of_RFINFO; $i++) { 
        $data_rfinfo.="{id: '".$res2['RFINFO'][$i]."',text:'".$res2['RFINFO'][$i]."'},";
    } 
    $data_rfinfo=substr($data_rfinfo, 0,-1);
}

$query="SELECT RAFTYPE, INDOOR, CREATION_ALLOWED, SKIPALLOWED FROM RAF_PROCESS_STEPS  order by RAFTYPE";
//echo $query;
$stmtPR= parse_exec_fetch($conn_Infobase, $query, $error_str, $resPR);
if (!$stmtPR){
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtPR);
    $amount_of_TYPES=count($resPR['RAFTYPE']);
}

for ($k = 0; $k <$amount_of_TYPES; $k++){  
	$raftype=$resPR['RAFTYPE'][$k];

	$javascript.="var ".str_replace(")", "",str_replace("(", "",(str_replace(" ", "_",$resPR['RAFTYPE'][$k]))))."='".$resPR['SKIPALLOWED'][$k]."';";

	$skipallowed[$raftype]=$resPR['SKIPALLOWED'][$k];
	$groups=explode(",", $guard_groups);
	if (($onlyIndoor=='yes' && $resPR['INDOOR'][$k]=='yes') OR ($onlyIndoor!='yes' && $resPR['INDOOR'][$k]=='no') or $resPR['INDOOR'][$k]=='both'){
   	 	foreach ($groups as $key => $group) {
   	 		if ((substr_count($resPR['CREATION_ALLOWED'][$k], $group)=="1"  or  substr_count($guard_groups, 'Administrators')=="1")&& $resPR['CREATION_ALLOWED'][$k]!='') {

       	 		$raftypeoptions.="<option>".$resPR['RAFTYPE'][$k]."</option>";

       	 		break 1;
       	 	}
   	 	}
	}
}

?>
<script language="JavaScript">
$(function() {

	<?=$javascript?>

	$('#type').change(function () {
		var type_val=$(this).val();
		//alert(type_val);
		if (type_val!='Please select' && type_val!=''){
			var raftype2=type_val.replace(/\(/g,'').replace(/\)/g,'').replace(/ /g,'_');
			if (eval(raftype2+"=='yes';")){
				$("#bufferinput").show("fast");
				if ($("#buffer").is(':checked')){
					$('.buffer_data').show("fast");
				}else{
					$('.buffer_data').hide();
				}
			}else{
				$("#bufferinput").hide();
				$('.buffer_data').hide();
			}
			if (type_val==="DISM Upgrade"){
				$('#inputBUDGETCON').val('X34919');
				$('#inputBUDGETACQ').val('NA');
				$('#inputCOMMERCIAL').val('NA');
				$('.buffer_data').show("fast");
				$("#buffer").prop('checked', true);
				$("#RADIO_FUND_DISM").prop('checked', true);
			}

			if (type_val=='New All Areas' || type_val=='New All Areas Site'){	
				$('#event_data').show("fast");
			}else{
				$('#event_data').hide();
			}
		}else{
			$('#event_data').hide();
			$("#bufferinput").hide();
		}

	});
	var type_val=$('#type').val();
	
	if (type_val!='' && type_val!='Please select'){
		
		var raftype=type_val.replace(/\(/g,'_').replace(/\)/g,'_').replace(/ /g,'_');
		if (eval(raftype+"=='yes';")){
			$("#bufferinput").show("fast");
			if ($("#buffer").is(':checked')){
				$('.buffer_data').show("fast");
			}else{
				$('.buffer_data').hide();
			}
		}else{
			$("#bufferinput").hide();
			$('.buffer_data').hide();
		}
		

		if (type_val=='New All Areas' || type_val=='New All Areas Site'){	
			$('#event_data').show("fast");
		}else{
			$('#event_data').hide();
		}
	}else{

		$("#bufferinput").hide();
		$('.buffer_data').hide();
		$('#event_data').hide();
	}

	
	$("#buffer").change(function(){
		if ($(this).is(':checked')){
			$('.buffer_data').show("fast");
		}else{
			$('.buffer_data').hide();
		}
	});
	/*
	$('#inputCOMMERCIAL').select2({
	    data: [
	        <?=$data_commercial?>
	    ]
    });
*/
    $('#inputRFINFO').select2({
	    data: [
	        <?=$data_rfinfo?>
	    ]
    });
});
</script>
<?

$regions=array(
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

<form action="scripts/raf/raf_actions.php" method="post" id="new_raf_form" class="form-horizontal">
<input type="hidden" name="action" value="insert_new_raf">
<input type="hidden" name="rafid" value="<?=$rafid?>">
<input type="hidden" name="GROUP" value="<?=$group?>">
<input type="hidden" name="bufferchangeallowed" value="<?=$_POST['bufferchangeallowed']?>">

	<div class="form-group">
    	<label for="type" class="col-sm-4 control-label">RAF TYPE</label>
    	<div class="col-sm-8">
	   	 	<select name="type" id="type" class="form-control">
			<?
			if($type!=''){ ?>
				<option selected value="<?=$type?>"><?=$type?></option>
			<? }else{ ?>
				<option selected value="">Please select</option>
			<? } 
			echo $raftypeoptions;
			?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="Region" class="col-sm-4 control-label">Region</label>
		<div class="col-sm-8">
		    <select class="form-control" id="Region" name="region">
			    <?php
				if($region){ ?>
					<option selected><?=$region?></option>
				<?php }elseif($region==""){ ?>
					<option value="<?php echo substr($_POST['siteid'],0,2); ?>" selected><?php echo substr($_POST['siteid'],0,2); ?></option>
				<?php }
				foreach ($regions as $key => $value){
				if ($value!= $region){?>
				<option><?=$value?></option>
				<?php }
				} ?>
			</select>
		</div>
	</div>
	<div class="form-group">
	    <label for="Sitenumber" class="col-sm-4 control-label">Sitenumber</label>
	    <div class="col-sm-8">
	    	<input type="text" name="sitenum" maxlength="7" id="Sitenumber" class="form-control" value="<?=$sitenum?>">
		</div>
	</div>
	<?php
	
	if (substr_count($guard_groups, 'Base_RF')=="1" || substr_count($guard_groups, 'Base_delivery')=="1" || substr_count($guard_groups, 'Administrators')=="1" && $_POST['bufferchangeallowed']=='yes'){ ?>
	<div class="form-group" id="bufferinput">
	    <label for="Sitenumber" class="col-sm-4 control-label">Buffer</label>
	    <div class="col-sm-8">
	    	<div class="checkbox">
		        <label>
		          <input name="buffer" type="checkbox" <?=$buffer?> id="buffer" value="1" rel='tooltip' title='RADIO,TXMN,PARTNER,BCS,PARTNER ACQ, TXMN ACQ=OK'> skip acquisition
		        </label>
		    </div>
		</div>
	</div>
	<div class="form-group buffer_data">
        <label for="inputRF_FUND" class="col-sm-4 control-label">RF FUND</label>
        <div class="col-sm-8">
            <div class="row">
                  <div class="col-md-3">
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_G9" value="G9" <?=$RADIO_FUND_G9?>> G9
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_G18" value="G18" <?=$RADIO_FUND_G18?>> G18
                    </label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_U9" value="U9" <?=$RADIO_FUND_U9?>> U9
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_U21" value="U21" <?=$RADIO_FUND_U21?>> U21
                    </label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_L8" value="L8" <?=$RADIO_FUND_L8?>> L8
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_L18" value="L18" <?=$RADIO_FUND_L18?>> L18
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_L26" value="L26" <?=$RADIO_FUND_L26?>> L26
                    </label>
                    </div>
                   </div>
                   <div class="col-md-3">
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_CTX" value="CTX" <?=$RADIO_FUND_CTX?>> CTX
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_ANT" value="ANT" <?=$RADIO_FUND_ANT?>> ANT
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_CAB" value="CAB" <?=$RADIO_FUND_CAB?>> CAB
                    </label>
                    </div>
                     <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_CWK" value="CWK" <?=$RADIO_FUND_CWK?>> CWK
                    </label>
                    </div>
                     <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_DISM" id='RADIO_FUND_DISM' value="DISM" <?=$RADIO_FUND_DISM?>> DISM
                    </label>
                    </div>
                     <div class="checkbox">
                    <label>
                      <input type="checkbox" name="RADIO_FUND_EXISTING" value="EXISTING" <?=$RADIO_FUND_EXISTING?>> EXISTING TECHNOS
                    </label>
                    </div>
                  </div>
            </div>
        </div>
    </div>
    	<?php }else{ ?>
    <div class="form-group">
	    <label for="Sitenumber" class="col-sm-4 control-label">RADIO FUND </label>
	    <div class="col-sm-8">
	    	You are not allowed to change RADIO FUND technologies AS UA352 is available in NET1.
		</div>
	</div>
    	
    	<? }


	$query = "Select DISTINCT(EVENT || ' '||SUBSTR(STARTDATE,-4)) AS EVENT FROM EVENTCAL ORDER BY EVENT";
	$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
	if (!$stmt2) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt2);
		$amount_of_EVENTS=count($res2['EVENT']);
	}
	?>
	<div class="form-group" id="event_data">
		<label for="event" class="col-sm-4 control-label">Event</label>
		<div class="col-sm-8">
		    <select class="form-control" id="event" name="EVENT">
			    <? if($res1['EVENT'][0]!=''){ ?>
					<option selected value="<?=$res1['EVENT'][0]?>"><?=$res1['EVENT'][0]?></option>
				<? }else{ ?>
					<option selected value="">Please select</option>
				<? } 
				for ($k = 0; $k <$amount_of_EVENTS; $k++){ ?>
					<option><?=$res2['EVENT'][$k]?></option>
				<?php 
				} ?>
			</select>
		</div>
	</div>
	<div class="form-group">
        <label for="inputRFINFO" class="col-sm-4 control-label">RFINFO</label>
        <div class="col-sm-8">
        	<input type="text" name="rfinfo" value="<?=$res1['RFINFO'][0]?>" class="form-control" id="inputRFINFO" />
        </div>
    </div>
    <!--
    <div class="form-group">
        <label for="inputCOMMERCIAL" class="col-sm-4 control-label">COMMERCIAL</label>

        <div class="col-sm-8">
        	<input type="text" name="commercial" value="<?=$res1['COMMERCIAL'][0]?>" class="form-control" id="inputCOMMERCIAL" />
        </div>
    </div>-->
     <div class="form-group">
        <label for="inputJUSTIFICATION" class="col-sm-4 control-label">JUSTIFICATION</label>
        <div class="col-sm-8">
            <?php
            if ($res1['JUSTIFICATION'][0]==""){
           	 	$justification="Minimum required info\n------------------------------\nReason for RAF.\nFor replacement/move: Onair date\nReason for replacement\nFor CWK: Due date\n";
            }else{
            	$justification=unescape_quotes($res1['JUSTIFICATION'][0]);
            }
            ?>
            <textarea name="justification" class="form-control input-sm" rows=7 id="inputJUSTIFICATION"><?=$justification?></textarea>
        </div>
    </div>
   	

<?php

if ($_POST['rafid']!=""){
	?>
	<div class="form-group">
		<div class="col-sm-8 col-sm-offset-4">
		<button class='btn btn-default' id='savemodal' data-module='rafnew'>SAVE CHANGES</button>
	</div>
	<?php
}
?>	
</form>

<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner,Alcatel","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);
?>
<script language="JavaScript">
$(document).ready(function() {

	function after_RAFdetails_save(response)  {  
		$('#modalspinner').spin(false);
		Messenger().post({
			message: response.responsedata,
			type: response.responsetype,
			showCloseButton: true
		});
	}	
	var options = {
		success: after_RAFdetails_save,
		dataType:  'json'
	};
	$('#form_radio').submit(function() { 
		$('#modalspinner').spin('medium');
	    $(this).ajaxSubmit(options); 
	    return false; 
	});

	$('#sitetype2').change(function(){
		if (this.value === "City indoor" || this.value === "Indoor site" ) {
			$('.polygon1').removeClass('hidden');
			$('.polygon2').addClass('hidden');
			$('.polygon3').addClass('hidden');
			$('.polygon4').addClass('hidden');
		}else if (this.value === "Indoor residentials"  || this.value === "Tunnel site") {
			$('.polygon2').removeClass('hidden');
			$('.polygon1').addClass('hidden');
			$('.polygon3').addClass('hidden');
			$('.polygon4').addClass('hidden');
		}else if (this.value === "In car") {
			$('.polygon3').removeClass('hidden');
			$('.polygon1').addClass('hidden');
			$('.polygon2').addClass('hidden');
			$('.polygon4').addClass('hidden');
		}else if (this.value === "Outdoor") {
			$('.polygon4').removeClass('hidden');
			$('.polygon1').addClass('hidden');
			$('.polygon2').addClass('hidden');
			$('.polygon3').addClass('hidden');
		}
	});

	$(".band").change(function(){
	    var techno=$(this).data('techno');
	    if ($(this).is(':checked')){
	      $('#VENDOR_'+techno).removeClass('hidden');
	      $('#NRSECTORS_'+techno).removeClass('hidden');
	    }else{
	      $('#VENDOR_'+techno).addClass('hidden');
	      $('#NRSECTORS_'+techno).addClass('hidden');
	    }
	});
	$(".band").each(function(){
	    var techno=$(this).data('techno');
	    if ($(this).is(':checked')){
	      $('#VENDOR_'+techno).removeClass('hidden');
	      $('#NRSECTORS_'+techno).removeClass('hidden');
	    }else{
	      $('#VENDOR_'+techno).addClass('hidden');
	      $('#NRSECTORS_'+techno).addClass('hidden');
	    }
	 });
	
  	$('#CABTYPE').select2({
	    createSearchChoice: function (term, data) {
	        if ($(data).filter(function () {
	            return this.text.localeCompare(term) === 0;
	        }).length === 0) {
	            return {
	                id: term,
	                text: term
	            };
	        }
	    },
	    initSelection: function(element, callback) {
					callback({id: element.val(), text: element.val() });
				},
			    minimumInputLength: 1,
			    ajax: {
			      url: "scripts/current_planned/ajax/field_list.php",
			      dataType: 'json',
			      data: function (term, page) {
			        return {
			          q: term,
			          field: 'cabtype'
			        };
			      },
			      results: function (data, page) {
			        return { results: data };
			      }
			    }
    });
});
</script>
<?php
$query = "Select RADIO_FUND FROM BSDS_RAFV2 WHERE RAFID = '".$_POST['rafid']."'";
//echo $query."<br>";
$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
if (!$stmt2) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt2);
}

$query = "Select * FROM BSDS_RAF_RADIO WHERE RAFID = '".$_POST['rafid']."'";

$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
	die_silently($conn_Infobase, $error_str);
 	exit;
} else {
	OCIFreeStatement($stmt);
	$amount_of_RAFS=count($res1['RAFID']);
}	

if ($res1['COVERAGE_TUNNEL'][0]=="Car"){
	$COVERAGE_TUNNEL_check1="CHECKED";
}else if ($$res1['COVERAGE_TUNNEL'][0]=="Train"){
	$COVERAGE_TUNNEL_check2="CHECKED";
}

//indoor and outdoor
if ($res1['SITETYPE2'][0]=="Please select" || $res1['SITETYPE2'][0]==""){
	$view_poly1="hidden";
	$view_poly2="hidden";
	$view_poly3="hidden";
	$view_poly4="hidden";
}else if ($res1['SITETYPE2'][0]=="City indoor" || $res1['SITETYPE2'][0]=="Indoor site"){
	$view_poly1="";
	$view_poly2="hidden";
	$view_poly3="hidden";
	$view_poly4="hidden";
}else if ($res1['SITETYPE2'][0]=="Indoor residentials" || $res1['SITETYPE2'][0]=="Tunnel site"){
	$view_poly1="hidden";
	$view_poly2="";
	$view_poly3="hidden";
	$view_poly4="hidden";
}else if ($res1['SITETYPE2'][0]=="In car"){
	$view_poly1="hidden";
	$view_poly2="hidden";
	$view_poly3="";
	$view_poly4="hidden";
}else if ($res1['SITETYPE2'][0]=="Outdoor"){
	$view_poly1="hidden";
	$view_poly2="hidden";
	$view_poly3="hidden";
	$view_poly4="";
}


if ($res1['SITETYPE'][0]=="Tunnel") $SITETYPE_Tunnel_check="CHECKED";
if ($res1['SITETYPE'][0]=="Indoor") $SITETYPE_Indoor_check="CHECKED";
if ($res1['SITETYPE'][0]=="Other") $SITETYPE_Other_check="CHECKED";

if ($res1['BAND_900'][0]=="1") $G9_check="CHECKED";
if ($res1['BAND_1800'][0]=="1") $G18_check="CHECKED";
if ($res1['BAND_UMTS'][0]=="1") $U21_check="CHECKED";
if ($res1['BAND_UMTS900'][0]=="1") $U9_check="CHECKED";
if ($res1['BAND_LTE1800'][0]=="1") $L18_check="CHECKED";
if ($res1['BAND_LTE800'][0]=="1") $L8_check="CHECKED";
if ($res1['BAND_LTE2600'][0]=="1") $L26_check="CHECKED";

if ($res1['SITESHARING'][0]=="Unknown") $Unknown_check="CHECKED";
if ($res1['SITESHARING'][0]=="None") $None_check="CHECKED";
if ($res1['SITESHARING'][0]=="Proximus") $Proximus_check="CHECKED";
if ($res1['SITESHARING'][0]=="Mobistar") $Mobistar_check="CHECKED";
if ($res1['SITESHARING'][0]=="MobistarProximus") $MobistarProximus_check="CHECKED";

if (strrpos($res1['EXPTRAFFIC'][0], 'G9')!==false) $EXP_G9_check="CHECKED";
if (strrpos($res1['EXPTRAFFIC'][0], 'G18')!==false) $EXP_G18_check="CHECKED";
if (strrpos($res1['EXPTRAFFIC'][0], 'U9')!==false) $EXP_U9_check="CHECKED";
if (strrpos($res1['EXPTRAFFIC'][0], 'U21')!==false) $EXP_U21_check="CHECKED";
if (strrpos($res1['EXPTRAFFIC'][0], 'L8')!==false) $EXP_L8_check="CHECKED";
if (strrpos($res1['EXPTRAFFIC'][0], 'L18')!==false) $EXP_L18_check="CHECKED";
if (strrpos($res1['EXPTRAFFIC'][0], 'L26')!==false) $EXP_L26_check="CHECKED";

if ($res1['PREFERREDINST'][0]=="Repeater") $Repeater_check="checked";
if ($res1['PREFERREDINST'][0]=="BTS") $BTS_check="checked";

if ($res1['COVERAGE_OBJECTIVE'][0]=="All floors") $objective_check1="CHECKED";
if ($res1['COVERAGE_OBJECTIVE'][0]=="All floors / selected areas") $objective_check2="CHECKED";
if ($res1['COVERAGE_OBJECTIVE'][0]=="Selected floors") $objective_check3="CHECKED";
if ($res1['COVERAGE_OBJECTIVE'][0]=="Selected floors / selected areas") $objective_check4="CHECKED";
if ($res1['COVERAGE_OBJECTIVE'][0]=="Parking area") $objective_check5="CHECKED";

if ($res1['INTER_900'][0]=="1") $INTER_900_check="CHECKED";
if ($res1['INTER_1800'][0]=="1") $INTER_1800_check="CHECKED";
if ($res1['INTER_UMTS'][0]=="1") $INTER_UMTS_check="CHECKED";
if ($res1['INTER_UMTS900'][0]=="1") $INTER_UMTS900_check="CHECKED";
if ($res1['INTER_LTE800'][0]=="1") $INTER_LTE800_check="CHECKED";
if ($res1['INTER_LTE1800'][0]=="1") $INTER_LTE1800_check="CHECKED";
if ($res1['INTER_LTE2600'][0]=="1") $INTER_LTE2600_check="CHECKED";

if ($res1['POLYMAP'][0]==1) $POLYMAP_check="CHECKED";
if ($res1['NRSECTORS'][0]==1) $NRSECTORS_check="CHECKED";
if ($res1['HMINMAX'][0]==1) $HMINMAX_check="CHECKED";
if ($res1['ANTBLOCKING'][0]==1) $ANTBLOCKING_check="CHECKED";
if ($res1['RFGUIDES'][0]==1) $RFGUIDES_check="CHECKED";
if ($res1['CONGUIDES'][0]==1) $CONGUIDES_check="CHECKED";




	if (substr_count($_POST['actiondo'], 'BASE RF (RAF 1->7)')==1 or substr_count($_POST['actiondo'], 'BASE RF (RAF 1->4)')==1)
	{
		if ($_POST['raftype']=="indoor"){
			$changeable_1_4="changeable";
		}else if ($_POST['raftype']=="outdoor"){
			$changeable_1_7="changeable";
		}
	?>
	<form action="scripts/raf/raf_actions.php" method="post" id="form_radio" class="form-horizontal" role="form">
	<input type="hidden" name="action" value="update_radio_raf_1_7">
	<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
<?php
	}
?>

	<div class="panel-group" id="accordion">
 		<div class="panel panel-default">
            <div class="panel-heading <?=$changeable_1_7?><?=$changeable_1_4?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#SiteData">
                 1. SITE DATA
                </a>
              </h4>
            </div>
            <div id="SiteData" class="panel-collapse collapse">
     		  	<div class="panel-body">

	     		  	<?php if ($_POST['raftype']=="indoor"){ ?>
					<div class="form-group">
	                  <label for="RFPLAN" class="col-sm-4 control-label">RF PLAN</label>
	                  <div class="col-sm-8">
	                   	<textarea class="form-control input-sm" rows="5" name="RFPLAN" id="RFPLAN"><?=$res1['RFPLAN'][0];?></textarea>
	                  </div>
	                </div>
	     		  	<? } ?>

	     		  	<?php if ($_POST['type']=="MOD Upgrade" or substr_count($_POST['type'], 'v2')==1){ ?>
	     		  	<div class="form-group" id="CONFIG">
	                  <label for="CONFIG" class="col-sm-4 control-label">SITE CONFIG</label>
	                  <div class="col-sm-8">
	                    <select name="CONFIG" style="width:100px;" class="form-control input-sm" id="CONFIG">
	                      <option selected><?=$res1['CONFIG'][0]?><option>MAXCAP</option><option>HIGHCAP</option><option>LOWCAP</option></select>
	                  </div>
	                </div>
	                <div class="form-group">
	                  <label for="CLUSTER" class="col-sm-4 control-label">CLUSTER</label>
	                  <div class="col-sm-8">
	                   	<input type="text" class="form-control input-sm" name="CLUSTER" id="CLUSTER" value="<?=$res1['CLUSTERN'][0];?>">
	                  </div>
	                </div>
	                <div class="form-group">
					    <label for="Sitenumber" class="col-sm-4 control-label">Cluster target date</label>
					    <div class="col-sm-8">
					    	<input name='CLUSTER_TARGET_DATE' value="<?=substr($res1['CLUSTER_TARGET_DATE'][0],0,10)?>" id='CLUSTER_TARGET_DATE' class='form-control' data-provide='datepicker' data-date-format='dd/mm/yyyy' placeholder='SELECT TARGET DATE'>
						</div>
					</div>
	                <? } ?>

	     		  	<div class="form-group">
	                  <label for="XCOORD" class="col-sm-4 control-label">XCOORD Lambert</label>
	                  <div class="col-sm-8">
	                   	<input type="text" class="form-control input-sm" name="XCOORD" id="XCOORD" value="<?=$res1['XCOORD'][0];?>">
	                  </div>
	                </div>

	                <div class="form-group">
	                  <label for="YCOORD" class=" col-sm-4 control-label">YCOORD Lambert</label>
	                  <div class="col-sm-8">
	                   	<input type="text" class="form-control input-sm" name="YCOORD" id="YCOORD" value="<?=$res1['YCOORD'][0];?>">
	                  </div>
	                </div>

	                <?php if ($_POST['raftype']=="indoor"){ ?>
	     		  	<div class="form-group">
	                  <label for="ADDRESS" class="col-sm-4 control-label">Address</label>
	                  <div class="col-sm-8">
	                   <textarea rows="4" class="form-control" name="ADDRESS" id="ADDRESS"><?=$res1['ADDRESS'][0];?></textarea>
	                  </div>
	                </div>

	                <div class="form-group">
	                  <label for="CONTACT" class="col-sm-4 control-label">Contact person</label>
	                  <div class="col-sm-8">
	                   <input type="text" class="form-control" name="CONTACT" id="CONTACT" value="<?=$res1['CONTACT'][0];?>">
	                  </div>
	                </div>

	                <div class="form-group">
	                  <label for="PHONE" class="col-sm-4 control-label">Phone number</label>
	                  <div class="col-sm-8">
	                   <input type="text" class="form-control" name="PHONE" id="PHONE" value="<?=$res1['PHONE'][0];?>">
	                  </div>
	                </div>

	                <div class="form-group">
	                  <label for="CONTACT" class="col-sm-4 control-label">Site type</label>
	                  <div class="col-sm-8">
	                    <div class="checkbox">
	                      <label>
	                        <input type="radio" name="SITETYPE" value="Tunnel" <?=$SITETYPE_Tunnel_check?>> Tunnel<br>
	                        <input type="radio" name="SITETYPE" value="Indoor" <?=$SITETYPE_Indoor_check?>> Indoor<br>
	                        <input type="radio" name="SITETYPE" value="Other" <?=$SITETYPE_Other_check?>> Other
	                      </label>
	                    </div>
	                  </div>
	                </div>

	                <div class="form-group">
	                  	<label for="CONTACT" class="col-sm-4 control-label">Site sharing</label>
		                 <div class="col-sm-8">
		                    <div class="radio">
		                      	<label>
		                        	<input type="radio" name="SITESHARING" value="Unknown" <?=$Unknown_check?>> None
		                    	</label>
		                    </div>
		                    <div class="radio">
		                    	<label>
		                        	<input type="radio" name="SITESHARING" value="Mobistar" <?=$Mobistar_check?>> Mobistar
		                    	</label>
		                    </div>
		                    <div class="radio">
		                    	<label>
		                        	<input type="radio" name="SITESHARING" value="Mobistar" <?=$Proximus_check?>> Proximus
		                    	</label>
		                    </div>
		                    <div class="radio">
		                    	<label>
		                       		<input type="radio" name="SITESHARING" value="MobistarProximus" <?=$MobistarProximus_check?>> Mobistar + Proximus
		                      </label>
		                    </div>
		                </div>
	                </div>
	                
	                <? } ?>
	                <br>
					<div class="form-group">
	                  	<label for="inputRF_FUND" class="col-sm-4 control-label">Band issued by KPNGB for ACQ</label>
	                  	<div class="col-sm-8">
		                    <div class="row">
			                    <div class="col-md-1">
			                        <div class="checkbox">
			                        <label>
			                          <input type="checkbox" name="BAND_900" class="band" data-techno="G9" value="1" <?=$G9_check?>> G9
			                        </label>
			                        </div>
			                        <div class="checkbox">
			                        <label>
			                          <input type="checkbox" name="BAND_1800" class="band" data-techno="G18" value="1" <?=$G18_check?>> G18
			                        </label>
			                        </div>
			                    </div>
		                      	<div class="col-md-1">
			                        <div class="checkbox">
			                        <label>
			                          <input type="checkbox" name="BAND_UMTS900" class="band" data-techno="U9" value="1" <?=$U9_check?>> U9
			                        </label>
			                        </div>
			                        <div class="checkbox">
			                        <label>
			                          <input type="checkbox" name="BAND_UMTS" class="band" data-techno="U21" value="1"<?=$U21_check?>> U21
			                        </label>
			                        </div>
		                      	</div>
		                      	<div class="col-md-1">
			                        <div class="checkbox">
			                        <label>
			                          <input type="checkbox" name="BAND_LTE800" class="band" data-techno="L8" value="1" <?=$L8_check?>> L8
			                        </label>
			                        </div>
			                        <div class="checkbox">
			                        <label>
			                          <input type="checkbox" name="BAND_LTE1800" class="band" data-techno="L18" value="1" <?=$L18_check?>> L18
			                        </label>
			                        </div>
			                        <div class="checkbox">
			                        <label>
			                          <input type="checkbox" name="BAND_LTE2600" class="band" data-techno="L26" value="1" <?=$L26_check?>> L26
			                        </label>
			                        </div>
	                      		</div>
	                      	</div>
                    	</div>
                    </div>
            
	                
					<div class="form-group hidden" id="VENDOR_G9">
	                  <label for="VENDOR2G_GSM900" class="col-sm-4 control-label">2G vendor GSM900</label>
	                  <div class="col-sm-8">
	                    <select name="VENDOR2G_GSM900" style="width:100px;" class="form-control input-sm" id="VENDOR2G_GSM900">
	                      <option selected><?=$res1['VENDOR2G_GSM900'][0]?><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NEXTIVITY</option><option>NA</option></select>
	                  </div>
	                </div>

					<div class="form-group hidden" id="VENDOR_G18">
	                  <label for="VENDOR2G_GSM1800" class="col-sm-4 control-label">2G vendor GSM1800</label>
	                  <div class="col-sm-8">
	                    <select name="VENDOR2G_GSM1800" style="width:100px;" class="form-control input-sm" id="VENDOR2G_GSM1800">
	                      <option><?=$res1['VENDOR2G_GSM1800'][0]?></option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NEXTIVITY</option><option>NA</option></select>
	                  </div>
	                </div>

	                <div class="form-group hidden" id="VENDOR_U9">
	                  <label for="VENDOR3G_UMTS900" class="col-sm-4 control-label">3G vendor UMTS900</label>
	                  <div class="col-sm-8">
	                    <select name="VENDOR3G_UMTS900" style="width:100px;" class="form-control input-sm" id="VENDOR3G_UMTS900">
	                      <option><?=$res1['VENDOR3G_UMTS900'][0]?></option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NEXTIVITY</option><option>NA</option></select>
	                  </div>
	                </div>

	                <div class="form-group hidden" id="VENDOR_U21">
	                  <label for="VENDOR3G_UMTS" class="col-sm-4 control-label">3G vendor UMTS2100</label>
	                  <div class="col-sm-8">
	                    <select name="VENDOR3G_UMTS"  style="width:100px;" class="form-control input-sm" id="VENDOR3G_UMTS">
	                      <option><?=$res1['VENDOR3G_UMTS'][0]?></option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NEXTIVITY</option><option>NA</option></select>
	                  </div>
	                </div>

	                <div class="form-group hidden" id="VENDOR_L8">
	                  <label for="VENDOR4G_LTE800" class="col-sm-4 control-label">4G vendor LTE800</label>
	                  <div class="col-sm-8">
	                    <select name="VENDOR4G_LTE800" style="width:100px;" class="form-control input-sm" id="VENDOR4G_LTE800">
	                      <option><?=$res1['VENDOR4G_LTE800'][0]?></option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NEXTIVITY</option><option>NA</option></select>
	                  </div>
	                </div>

	                <div class="form-group hidden" id="VENDOR_L18">
	                  <label for="VENDOR4G_LTE1800" class="col-sm-4 control-label">4G vendor LTE1800</label>
	                  <div class="col-sm-8">
	                    <select name="VENDOR4G_LTE1800" style="width:100px;" class="form-control input-sm" id="VENDOR4G_LTE1800">
	                      <option><?=$res1['VENDOR4G_LTE1800'][0]?></option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NEXTIVITY</option><option>NA</option></select>
	                  </div>
	                </div>

	                <div class="form-group hidden" id="VENDOR_L26">
	                  <label for="VENDOR4G_LTE2600" class="col-sm-4 control-label">4G vendor LTE2600</label>
	                  <div class="col-sm-8">
	                    <select name="VENDOR4G_LTE2600" style="width:100px;" class="form-control input-sm" id="VENDOR4G_LTE2600">
	                      <option><?=$res1['VENDOR4G_LTE2600'][0]?></option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NEXTIVITY</option><option>NA</option></select>
	                  </div>
	                </div>

	                <?php if ($_POST['raftype']=="outdoor"){ ?>
	                <div class="form-group">
					    <label class="col-sm-4 control-label">Band funded for Construction</label>
					    <div class="col-sm-8">
					      <p class="form-control-static"><?=$res2['RADIO_FUND'][0]?>&nbsp;</p>
					    </div>
					</div>
					<?php } ?>

	                <?php if ($_POST['raftype']=="indoor"){ ?>
	                <div class="form-group">
	                  <label for="inputRF_FUND" class="col-sm-4 control-label">Expected Traffic</label>
	                  <div class="col-sm-8">
	                    <div class="row">
	                      <div class="col-md-1">
	                        <div class="checkbox">
	                        <label>
	                          <input type="checkbox" name="EXP_G9" value="G9" <?=$EXP_G9_check?>> G9
	                        </label>
	                        </div>
	                        <div class="checkbox">
	                        <label>
	                          <input type="checkbox" name="EXP_G18"  value="G18" <?=$EXP_G18_check?>> G18
	                        </label>
	                        </div>
	                      </div>
	                      <div class="col-md-1">
	                        <div class="checkbox">
	                        <label>
	                          <input type="checkbox" name="EXP_U9" value="U9" <?=$EXP_U9_check?>> U9
	                        </label>
	                        </div>
	                        <div class="checkbox">
	                        <label>
	                          <input type="checkbox" name="EXP_U21" value="U21" <?=$EXP_U21_check?>> U21
	                        </label>
	                        </div>
	                      </div>
	                      <div class="col-md-1">
	                        <div class="checkbox">
	                        <label>
	                          <input type="checkbox" name="EXP_L8" value="L8" <?=$EXP_L8_check?>> L8
	                        </label>
	                        </div>
	                        <div class="checkbox">
	                        <label>
	                          <input type="checkbox" name="EXP_L18" value="L18" <?=$EXP_L18_check?>> L18
	                        </label>
	                        </div>
	                        <div class="checkbox">
	                        <label>
	                          <input type="checkbox" name="EXP_L26" value="L26" <?=$EXP_L26_check?>> L26
	                        </label>
	                        </div>
	                      </div>
	                    </div>
	                  </div>
	                </div>

	                <div class="form-group">
	                  <label for="FEATURE" class="col-sm-4 control-label">Feature</label>
	                  <div class="col-sm-8">
	                    <select name="FEATURE" class="form-control input-sm" id="FEATURE">
	                      	<option selected><?=$res1['FEATURE'][0]?></option>
	                  		<option>NONE</option>
							<option>EDGE</option>
							<option>HSDPA</option>
							<option>EDGE+HSDPA</option>
							<option>LTE</option>
						</select>
	                  </div>
	                </div>

	                <div class="form-group">
	                  <label for="PREFERREDINST" class="col-sm-4 control-label">Preferred inst.</label>
	                  <div class="col-sm-8">
	                    <div class="radio">
	                      	<label>
	                        	<input type="radio" name="PREFERREDINST" value="Repeater" <?=$Repeater_check?>> REPEATER
	                    	</label>
	                    </div>
	                    <div class="radio">
	                    	<label>
	                        	<input type="radio" name="PREFERREDINST" value="BTS" <?=$BTS_check?>> BTS
	                    	</label>
	                    </div>
	                  </div>
	                </div>

                	<div class="form-group">
		                <label for="CABTYPE" class="col-sm-4 control-label">Cabinet type</label>
		                <div class="col-sm-8">
		                  	<input type="text" name="CABTYPE" value='<?=$res1['CABTYPE'][0]?>' class="form-control input-sm" id="CABTYPE">
	                  	</div>
	                </div>

	                <div class="form-group">
	                  <label for="CHTRX" class="col-sm-4 control-label">CH/ TRX's</label>
	                  <div class="col-sm-8">
	                   	<input type="text" class="form-control input-sm" name="CHTRX" id="CHTRX" value="<?=$res1['CHTRX'][0];?>">
	                  </div>
	                </div>

	                <div class="form-group">
	                  <label for="SECTORS" class="col-sm-4 control-label">Sectors</label>
	                  <div class="col-sm-8">
	                   	<input type="text" class="form-control input-sm" name="SECTORS" id="SECTORS" value="<?=$res1['SECTORS'][0];?>">
	                  </div>
	                </div>

	                <div class="form-group">
	                  <label for="REPEATER" class="col-sm-4 control-label">Repeater donor site</label>
	                  <div class="col-sm-8">
	                   	<input type="text" class="form-control input-sm" name="REPEATER" id="REPEATER" value="<?=$res1['REPEATER'][0];?>">
	                  </div>
	                </div>
                	<?php } ?>
		  		</div>
			</div>
		</div>

		<div class="panel panel-default">
            <div class="panel-heading <?=$changeable_1_4?><?=$changeable_1_7?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#coverageObj">
                  2. COVERAGE OBECTIVES
                </a>
              </h4>
            </div>
            <div id="coverageObj" class="panel-collapse collapse">
     		  	<div class="panel-body">
	            	<?php if ($_POST['raftype']=="indoor"){ ?>
	            	<div class="form-group">
		                <label for="COVERAGE_OBJECTIVE" class="col-sm-4 control-label">Site type</label>
		                <div class="col-sm-8">
		                    <div class="checkbox">
		                      <label>
		                        <input type="radio" name="COVERAGE_OBJECTIVE" value="All floors" <?=$objective_check1?>> Tunnel<br>
		                        <input type="radio" name="COVERAGE_OBJECTIVE" value="All floors/selected areas" <?=$objective_check2?>> Indoor<br>
		                        <input type="radio" name="COVERAGE_OBJECTIVE" value="Selected floors" <?=$objective_check3?>> Selected floors <br>
		                        <input type="radio" name="COVERAGE_OBJECTIVE" value="Selected floors / selected areas" <?=$objective_check4?>> Selected floors / selected areas<br>
		                        <input type="radio" name="COVERAGE_OBJECTIVE" value="Parking area" <?=$objective_check5?>> Parking area

		                      </label>
		                    </div>
		                </div>
	                </div>

	            	<div class="form-group">
	            		<label for="COVERAGE_DESCR" class="col-sm-4 control-label">Description</label>
	            		<div class="col-sm-3">
	                		<textarea name="COVERAGE_DESCR" rows="5" id="COVERAGE_DESCR" class="form-control input-sm"><?php echo unescape_quotes($res1['COVERAGE_DESCR'][0]); ?></textarea>
	              		</div>
	              	</div>

		            <div class="form-group">
		                <label for="FLOORS" class="col-sm-4 control-label">Floors</label>
		                <div class="col-sm-3">
		                   	<input type="text" class="form-control input-sm" name="FLOORS" id="FLOORS" value="<?=$res1['FLOORS'][0];?>">
		                </div>
		            </div>

		            <div class="form-group">
		                <label for="AREAS" class="col-sm-4 control-label">Areas</label>
		                <div class="col-sm-3">
		                   	<input type="text" class="form-control input-sm" name="AREAS" id="AREAS" value="<?=$res1['AREAS'][0];?>">
		                </div>
		            </div>
            		<?php } ?>
            
		            <?php if ($_POST['raftype']=="outdoor"){ ?>
		            <div class="form-group">
		            	<label for="COVERAGE_OBJECTIVE" class="col-sm-4 control-label">Description</label>
		                <div class="col-sm-3">
		                	<textarea name="COVERAGE_OBJECTIVE" id="COVERAGE_OBJECTIVE" rows="10" class="col-sm-4 form-control input-sm"><?php echo unescape_quotes($res1['COVERAGE_OBJECTIVE'][0]); ?></textarea>
		            	</div>
		            </div>
		            <?php } ?>
		        </div>
		    </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading <?=$changeable_1_4?><?=$changeable_1_7?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#coveragekpi">
                  3. COVERAGE KPI'S
                </a>
              </h4>
            </div>
            <div id="coveragekpi" class="panel-collapse collapse">
              	<div class="panel-body">
	              	<?php if ($_POST['raftype']=="indoor"){ ?>
	              	 <div class="form-group">
		                <div class="col-sm-3">
		                   	<select name="SITETYPE2" class="form-control input-sm" id='sitetype2'>
		              		<option selected><?=$res1['SITETYPE2'][0];?></option>
		              		<option>Indoor site</option>
		              		<option>Tunnel site</option>
	              			</select>
		                </div>
		            </div>
					<table class="polygon1 table <?=$view_poly1?>">
					<tr>
						<td class="param_title">Band</td>
						<td class="param_title">Type of Coverage</td>
						<td class="param_title">BCCH/Pilot threshold</td>
						<td class="param_title">% of the area</td>
					</tr>
					<tr>
						<td>GSM900</td>
						<td>Indoor</td>
						<td>-90 dBm / -77dBm*</td>
						<td><input type="text" name="AREA1_900" class="form-control" size="10" value="<?=$res1['AREA1_900'][0]?>"></td>
					</tr>
					<tr>
						<td>GSM1800</td>
						<td>Indoor</td>
						<td>-88 dBm / -75dBm*</td>
						<td><input type="text" name="AREA1_1800" class="form-control" size="10" value="<?=$res1['AREA1_18002'][0]?>"></td>
					</tr>
					<tr >
						<td>UMTS2100</td>
						<td>Indoor</td>
						<td>-87 dBm / -77dBm*</td>
						<td><input type="text" name="AREA1_UMTS" class="form-control" size="10" value="<?=$res1['AREA1_UMTS2'][0]?>"></td>
					</tr>
					<tr>
						<td>UMTS900</td>
						<td>Indoor</td>
						<td>- dBm</td>
						<td><input type="text" name="AREA1_UMTS900" class="form-control" size="10" value="<?=$res1['AREA1_UMTS900'][0]?>"></td>
					</tr>
					<tr>
						<td>LTE800</td>
						<td>Indoor</td>
						<td>- dBm</td>
						<td><input type="text" name="AREA1_LTE800" class="form-control" size="10" value="<?=$res1['AREA1_LTE800'][0]?>"></td>
					</tr>
					<tr>
						<td>LTE1800</td>
						<td>Indoor</td>
						<td>- dBm</td>
						<td><input type="text" name="AREA1_LTE1800" class="form-control" size="10" value="<?=$res1['AREA1_LTE1800'][0]?>"></td>
					</tr>
					<tr>
						<td>LTE2600</td>
						<td>Indoor</td>
						<td>- dBm</td>
						<td><input type="text" name="AREA1_LTE2600" class="form-control" size="10" value="<?=$res1['AREA1_LTE2600'][0]?>"></td>
					</tr>
					</table>
					<table class="polygon2 table <?=$view_poly2?>">
					<tr>
						<td class="param_title">Band</td>
						<td class="param_title">Type of Coverage</td>
						<td class="param_title">BCCH/Pilot threshold</td>
						<td class="param_title">% of the area</td>
					</tr>
					<tr>
						<td colspan="4">
							<div class="checkbox">
		                      <label>
		                        <INPUT TYPE="radio" NAME="COVERAGE_TUNNEL" class="form-control input-sm" VALUE="Car" <?=$COVERAGE_TUNNEL_check1?>>Car<br>
								<INPUT TYPE="radio" NAME="COVERAGE_TUNNEL" class="form-control input-sm" VALUE="Train" <?=$COVERAGE_TUNNEL_check2?>>Train
		                      </label>
		                    </div>
							
						</td>
					</tr>
					<tr >
						<td>GSM900</td>
						<td>In-tunnel</td>
						<td>-87 dBm / -77dBm*</td>
						<td><input type="text" name="AREA2_900" class="form-control input-sm" size="10" value="<?=$res1['AREA2_900'][0]?>"></td>
					</tr>
					<tr>
						<td>GSM1800</td>
						<td>In-tunnel</td>
						<td>-85 dBm / -75dBm*</td>
						<td><input type="text" name="AREA2_1800" class="form-control input-sm" size="10" value="<?=$res1['AREA2_1800'][0]?>"></td>
					</tr>
					<tr>
						<td>UMTS2100</td>
						<td>In-tunnel</td>
						<td>-90 dBm / -80dBm*</td>
						<td><input type="text" name="AREA2_UMTS" class="form-control input-sm" size="10" value="<?=$res1['AREA2_UMTS'][0]?>"></td>
					</tr>
					<tr>
						<td>UMTS900</td>
						<td>Indoor</td>
						<td>- dBm</td>
						<td><input type="text" name="AREA2_UMTS900" class="form-control input-sm" size="10" value="<?=$res1['AREA2_UMTS900'][0]?>"></td>
					</tr>
					<tr>
						<td>LTE800</td>
						<td>Indoor</td>
						<td>- dBm</td>
						<td><input type="text" name="AREA2_LTE800" class="form-control input-sm" size="10" value="<?=$res1['AREA2_LTE800'][0]?>"></td>
					</tr>
					<tr>
						<td>LTE1800</td>
						<td>Indoor</td>
						<td>- dBm</td>
						<td><input type="text" name="AREA2_LTE1800" class="form-control input-sm" size="10" value="<?=$res1['AREA2_LTE1800'][0]?>"></td>
					</tr>
					<tr>
						<td>LTE2600</td>
						<td>Indoor</td>
						<td>- dBm</td>
						<td><input type="text" name="AREA2_LTE2600" class="form-control input-sm" size="10" value="<?=$res1['AREA2_LTE2600'][0]?>"></td>
					</tr>
					</table>
	              	<?php } 

	              	if ($_POST['raftype']=="outdoor"){ ?>
	              	<div class="form-group">
		                <div class="col-sm-3">
		                	<select name="SITETYPE2" class="form-control input-sm" id='sitetype2'>
		                	<option selected><?=$res1['SITETYPE2'][0];?></option>
		                	<option>City indoor</option>
		                	<option>Indoor residential</option>
		                	<option>In car</option>
		                	<option>Outdoor</option>
		                	</select>
		                </div>
		            </div>
	     
	                <table class="polygon1 table <?=$view_poly1?>">
	                <thead>
	                <tr>
	                  <th>Band</th>
	                  <th>Type of Coverage</th>
	                  <th>P(BCCH) / P(CPICH) carrier<br> threshold better than</th>
	                  <th>% of the area</th>
	                </tr>
	                </thead>
	                <tbody>
	                <tr>
	                  <td>GSM900</td>
	                  <td>Indoor</td>
	                  <td>-71 dBm</td>
	                  <td><input type="text" name="AREA1_900" class="form-control input-sm" value="<?=$res1['AREA1_900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>GSM1800</td>
	                  <td>Indoor</td>
	                  <td>-66 dBm</td>
	                  <td><input type="text" name="AREA1_1800" class="form-control input-sm" value="<?=$res1['AREA1_1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>UMTS2100</td>
	                  <td>Indoor</td>
	                  <td>-75 dBm</td>
	                  <td><input type="text" name="AREA1_UMTS" class="form-control input-sm" value="<?=$res1['AREA1_UMTS'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>UMTS900</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA1_UMTS900" class="form-control input-sm" value="<?=$res1['AREA1_UMTS900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE800</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA1_LTE800" class="form-control input-sm" value="<?=$res1['AREA1_LTE800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE1800</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA1_LTE1800" class="form-control input-sm" value="<?=$res1['AREA1_LTE1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE2600</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA1_LTE2600" class="form-control input-sm" value="<?=$res1['AREA1_LTE2600'][0]?>"></td>
	                </tr>
	                </tbody>
	                </table>

	                <table class="polygon2 table <?=$view_poly2?>">
	                <tr>
	                  <th>Band</th>
	                  <th>Type of Coverage</th>
	                  <th>P(BCCH) / P(CPICH) carrier<br> threshold better than</th>
	                  <th>% of the area</th>
	                </tr>
	                <tbody>
	                <tr>
	                  <td>GSM900</td>
	                  <td>Indoor</td>
	                  <td>-76 dBm</td>
	                  <td><input type="text" name="AREA2_900" class="form-control input-sm" value="<?=$res1['AREA2_900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>GSM1800</td>
	                  <td>Indoor</td>
	                  <td>-71 dBm</td>
	                  <td><input type="text" name="AREA2_1800" class="form-control input-sm" value="<?=$res1['AREA2_1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>UMTS2100</td>
	                  <td>Indoor</td>
	                  <td>-84 dBm</td>
	                  <td><input type="text" name="AREA2_UMTS" class="form-control input-sm" value="<?=$res1['AREA2_UMTS'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>UMTS900</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA2_UMTS900" class="form-control input-sm" value="<?=$res1['AREA2_UMTS900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE800</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA2_LTE800" class="form-control input-sm" value="<?=$res1['AREA2_LTE800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE1800</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA2_LTE1800" class="form-control input-sm" value="<?=$res1['AREA2_LTE1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE2600</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA2_LTE2600" class="form-control input-sm" value="<?=$res1['AREA2_LTE2600'][0]?>"></td>
	                </tr>
	                </tbody>
	                </table>

	                <table class="polygon3 table <?=$view_poly3?>">
	                <thead>
	                <tr>
	                  <th>Band</th>
	                  <th>Type of Coverage</th>
	                  <th>P(BCCH) / P(CPICH) carrier<br> threshold better than</th>
	                  <th>% of the area</th>
	                </tr>
	                </thead>
	                <tbody>
	                <tr>
	                  <td>GSM900</td>
	                  <td>Indoor</td>
	                  <td>-79 dBm</td>
	                  <td><input type="text" name="AREA3_GSM900" class="form-control input-sm" value="<?=$res1['AREA3_900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>GSM1800</td>
	                  <td>Indoor</td>
	                  <td>-75 dBm</td>
	                  <td><input type="text" name="AREA3_GSM1800" class="form-control input-sm" value="<?=$res1['AREA3_1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>UMTS2100</td>
	                  <td>Indoor</td>
	                  <td>-88 dBm</td>
	                  <td><input type="text" name="AREA3_UMTS" class="form-control input-sm" value="<?=$res1['AREA3_UMTS'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>UMTS900</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA3_UMTS900" class="form-control input-sm" value="<?=$res1['AREA3_UMTS900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE800</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA3_LTE800" class="form-control input-sm" value="<?=$res1['AREA3_LTE800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE1800</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA3_LTE1800" class="form-control input-sm" value="<?=$res1['AREA3_LTE1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE2600</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA3_LTE2600" class="form-control input-sm" value="<?=$res1['AREA3_LTE2600'][0]?>"></td>
	                </tr>
	                </tbody>
	                </table>

	                <table class="polygon4 table <?=$view_poly4?>">
	                <thead>
	                <tr>
	                  <th>Band</th>
	                  <th>Type of Coverage</th>
	                  <th>P(BCCH) / P(CPICH) carrier<br> threshold better than</th>
	                  <th>% of the area</th>
	                </tr>
	                </thead>
	                <tbody>
	                <tr>
	                  <td>GSM900</td>
	                  <td>Indoor</td>
	                  <td>-79 dBm</td>
	                  <td><input type="text" name="AREA4_GSM900" class="form-control input-sm" value="<?=$res1['AREA4_900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>GSM1800</td>
	                  <td>Indoor</td>
	                  <td>-75 dBm</td>
	                  <td><input type="text" name="AREA4_GSM1800" class="form-control input-sm" value="<?=$res1['AREA4_1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>UMTS2100</td>
	                  <td>Indoor</td>
	                  <td>-88 dBm</td>
	                  <td><input type="text" name="AREA4_UMTS" class="form-control input-sm" value="<?=$res1['AREA4_UMTS'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>UMTS900</td>
	                  <td>Indoor</td>
	                  <td>-88 dBm</td>
	                  <td><input type="text" name="AREA4_UMTS900" class="form-control input-sm" value="<?=$res1['AREA4_UMTS900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE800</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA4_LTE800" class="form-control input-sm" value="<?=$res1['AREA4_LTE800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE1800</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA4_LTE1800" class="form-control input-sm" value="<?=$res1['AREA4_LTE1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td>LTE2600</td>
	                  <td>Indoor</td>
	                  <td>- dBm</td>
	                  <td><input type="text" name="AREA4_LTE2600" class="form-control input-sm" value="<?=$res1['AREA4_LTE2600'][0]?>"></td>
	                </tr>
	                </tbody>
	                </table>
	                <?php } ?>
              	</div>
            </div>
        </div>

        <?php if ($_POST['raftype']=="outdoor"){ ?>
        <div class="panel panel-default">
            <div class="panel-heading <?=$changeable_1_7?><?=$changeable_1_4?>">
              	<h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#InterFerence">
                4. INTERFERENCE KPI's OUTDOOR SITE
                </a>
              	</h4>
            </div>
            <div id="InterFerence" class="panel-collapse collapse">
              	<div class="panel-body">
	                <table class="table rafdata">
	                <tr>
	                  <td>Applicable</td>
	                  <td>Band</td>
	                  <td>Threshold</td>
	                  <td>Total area of coverage above threshold (%)</td>
	                  <td>Total area of coverage above threshold (%)<br>(deep indoor, indoor residential and incar)</td>
	                </tr>
	                <tr>
	                  <td><input type="checkbox" NAME="INTER_900" VALUE="1" <?=$INTER_900_check?>></td>
	                  <td>GSM900</td>
	                  <td><input type="text" name="THRESHOLD_900" class="form-control input-sm" value="<?=$res1['THRESHOLD_900'][0]?>"></td>
	                  <td><input type="text" name="COVERAGE_900" class="form-control input-sm" value="<?=$res1['COVERAGE_900'][0]?>"></td>
	                  <td><input type="text" name="TOTCOVERAGE_900" class="form-control input-sm" value="<?=$res1['TOTCOVERAGE_900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td><input type="checkbox" NAME="INTER_1800" VALUE="1" <?=$INTER_1800_check?>></td>
	                  <td>GSM1800</td>
	                  <td><input type="text" name="THRESHOLD_1800" class="form-control input-sm" value="<?=$res1['THRESHOLD_1800'][0]?>"></td>
	                  <td><input type="text" name="COVERAGE_1800" class="form-control input-sm" value="<?=$res1['COVERAGE_1800'][0]?>"></td>
	                  <td><input type="text" name="TOTCOVERAGE_1800" class="form-control input-sm" value="<?=$res1['TOTCOVERAGE_1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td><input type="checkbox" NAME="INTER_UMTS" VALUE="1" <?=$INTER_UMTS_check?>></td>
	                  <td>UMTS2100</td>
	                  <td><input type="text" name="THRESHOLD_UMTS" class="form-control input-sm" value="<?=$res1['THRESHOLD_UMTS'][0]?>"></td>
	                  <td><input type="text" name="COVERAGE_UMTS" class="form-control input-sm" value="<?=$res1['COVERAGE_UMTS'][0]?>"></td>
	                  <td><input type="text" name="TOTCOVERAGE_UMTS" class="form-control input-sm" value="<?=$res1['TOTCOVERAGE_UMTS'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td><input type="checkbox" NAME="INTER_UMTS900" VALUE="1" <?=$INTER_UMTS900_check?>></td>
	                  <td>UMTS900</td>
	                  <td><input type="text" name="THRESHOLD_UMTS900" class="form-control input-sm" value="<?=$res1['THRESHOLD_UMTS900'][0]?>"></td>
	                  <td><input type="text" name="COVERAGE_UMTS900" class="form-control input-sm" value="<?=$res1['COVERAGE_UMTS900'][0]?>"></td>
	                  <td><input type="text" name="TOTCOVERAGE_UMTS900" class="form-control input-sm" value="<?=$res1['TOTCOVERAGE_UMTS900'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td><input type="checkbox" NAME="INTER_LTE800" VALUE="1" <?=$INTER_LTE800_check?>></td>
	                  <td>LTE800</td>
	                  <td><input type="text" name="THRESHOLD_LTE800" class="form-control input-sm" value="<?=$res1['THRESHOLD_LTE800'][0]?>"></td>
	                  <td><input type="text" name="COVERAGE_LTE800" class="form-control input-sm" value="<?=$res1['COVERAGE_LTE800'][0]?>"></td>
	                  <td><input type="text" name="TOTCOVERAGE_LTE800" class="form-control input-sm" value="<?=$res1['TOTCOVERAGE_LTE800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td><input type="checkbox" NAME="INTER_LTE1800" VALUE="1" <?=$INTER_LTE1800_check?>></td>
	                  <td>LTE1800</td>
	                  <td><input type="text" name="THRESHOLD_LTE1800" class="form-control input-sm" value="<?=$res1['THRESHOLD_LTE1800'][0]?>"></td>
	                  <td><input type="text" name="COVERAGE_LTE1800" class="form-control input-sm" value="<?=$res1['COVERAGE_LTE1800'][0]?>"></td>
	                  <td><input type="text" name="TOTCOVERAGE_LTE1800" class="form-control input-sm" value="<?=$res1['TOTCOVERAGE_LTE1800'][0]?>"></td>
	                </tr>
	                <tr>
	                  <td><input type="checkbox" NAME="INTER_LTE2600" VALUE="1" <?=$INTER_LTE2600_check?>></td>
	                  <td>LTE2600</td>
	                  <td><input type="text" name="THRESHOLD_LTE2600" class="form-control input-sm" value="<?=$res1['THRESHOLD_LTE2600'][0]?>"></td>
	                  <td><input type="text" name="COVERAGE_LTE2600" class="form-control input-sm" value="<?=$res1['COVERAGE_LTE2600'][0]?>"></td>
	                  <td><input type="text" name="TOTCOVERAGE_LTE2600" class="form-control input-sm" value="<?=$res1['TOTCOVERAGE_LTE2600'][0]?>"></td>
	                </tr>
	                </table>
              	</div>
            </div>
        </div> 
         <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading <?=$changeable_1_4?><?=$changeable_1_7?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#comments">
                <?php if ($_POST['raftype']=="indoor"){ ?>
                 4. COMMENTS
                <?php }else if ($_POST['raftype']=="outdoor"){ ?>
				 5. COMMENTS
                <?php } ?>
                </a>
              </h4>
            </div>
            <div id="comments" class="panel-collapse collapse">
              <div class="panel-body">
                <textarea class="form-control" rows="10" name="COMMENTS"><?php echo unescape_quotes($res1['COMMENTS'][0]); ?></textarea>
              </div>
            </div>
        </div> 

        <?php if ($_POST['raftype']=="outdoor"){ ?>
        <div class="panel panel-default">
            <div class="panel-heading <?=$changeable_1_7?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#checklist">
                6. CHECKLIST
                </a>
              </h4>
            </div>
            <div id="checklist" class="panel-collapse collapse">
              	<div class="panel-body">
	                <div class="form-group" id="nrSectors">
	                  <label for="nrSectors" class="col-sm-3 control-label">Number of sectors</label>
	                  <div class="col-sm-9">
	                    <div class="row">
	                      <div class="col-sm-4">
	                        <input TYPE="text" NAME="NRSECTORS_900" id="NRSECTORS_G9" value="<?=$res1['NRSECTORS_900'][0]?>" class="form-control input-sm hidden" placeholder="G9">
	                        <input TYPE="text" NAME="NRSECTORS_1800" id="NRSECTORS_G18" value="<?=$res1['NRSECTORS_1800'][0]?>" class="form-control input-sm hidden" placeholder="G18">
	                      </div>
	                      <div class="col-sm-4">
	                        <input TYPE="text" NAME="NRSECTORS_UMTS900" id="NRSECTORS_U9" value="<?=$res1['NRSECTORS_UMTS900'][0]?>" class="form-control input-sm hidden" placeholder="U9">
	                        <input TYPE="text" NAME="NRSECTORS_UMTS" id="NRSECTORS_U21" value="<?=$res1['NRSECTORS_UMTS'][0]?>" class="form-control input-sm hidden" placeholder="U21">
	                      </div>
	                      <div class="col-sm-4">
	                        <input TYPE="text" NAME="NRSECTORS_LTE800" id="NRSECTORS_L8" value="<?=$res1['NRSECTORS_LTE800'][0]?>" class="form-control input-sm hidden" placeholder="L8">
	                        <input TYPE="text" NAME="NRSECTORS_LTE1800" id="NRSECTORS_L18" value="<?=$res1['NRSECTORS_LTE1800'][0]?>" class="form-control input-sm hidden" placeholder="L18">
	                        <input TYPE="text" NAME="NRSECTORS_LTE2600" id="NRSECTORS_L26" value="<?=$res1['NRSECTORS_LTE2600'][0]?>" class="form-control input-sm hidden" placeholder="L26">
	                      </div>
	                    </div>
	                  </div>
	                </div>
	                <div class="form-group">
	                  <div class="col-sm-offset-2 col-sm-10">
	                    <div class="checkbox">
	                      <label>
	                        <input type="checkbox" NAME="POLYMAP" VALUE="1" <?=$POLYMAP_check?>> Map attached with polygons indicated
	                      </label>
	                    </div>
	                  </div>
	                </div>
	                <div class="form-group">
	                  <div class="col-sm-offset-2 col-sm-10">
	                    <div class="checkbox">
	                      <label>
	                        <input type="checkbox" NAME="HMINMAX" VALUE="1" <?=$HMINMAX_check?>> Hmin / Hmax for RF antennas: <INPUT TYPE="text" NAME="HMINMAXRF" size="5"  value="<?=$res1['HMINMAXRF'][0]?>">
	                      </label>
	                    </div>
	                  </div>
	                </div>

	                <div class="form-group">
	                  <div class="col-sm-offset-2 col-sm-10">
	                    <div class="checkbox">
	                      <label>
	                        <input type="checkbox" NAME="ANTBLOCKING" VALUE="1" <?=$ANTBLOCKING_check?>> Antenna blocking by nearby building (angle):  <INPUT TYPE="text" NAME="ANGLE" size="5"  value="<?=$res1['ANGLE'][0]?>">
	                      </label>
	                    </div>
	                  </div>
	                </div>

	                <div class="form-group">
	                  <div class="col-sm-offset-2 col-sm-10">
	                    <div class="checkbox">
	                      <label>
	                        <input type="checkbox" NAME="RFGUIDES" VALUE="1" <?=$RFGUIDES_check?>>RF Guidlines compliant
	                      </label>
	                    </div>
	                  </div>
	                </div>

	                <div class="form-group">
	                  <div class="col-sm-offset-2 col-sm-10">
	                    <div class="checkbox">
	                      <label>
	                        <input type="checkbox" NAME="CONGUIDES" VALUE="1" <?=$CONGUIDES_check?>>Construction Guidlines compliant
	                      </label>
	                    </div>
	                  </div>
	                </div>
              	</div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading <?=$changeable_1_7?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#posLocations">
                 7. POSSIBLE LOCATIONS
                </a>
              </h4>
            </div>
            <div id="posLocations" class="panel-collapse collapse">
              	<div class="panel-body">
	                <table class="table table-condensed">
	                <thead>
	                <tr>
	                  <th>&nbsp;</th>
	                  <th>Name</th>
	                  <th>Address</th>
	                  <th>Type of structure</th>
	                  <th>Preferred</th>
	                  <th>Not preferrerd</th>
	                </tr>
	                </thead>
	                <tbody>
	                <tr>
	                  <td>1</td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_NAME1" value="<?=$res1['LOC_NAME1'][0]?>" size="7"></td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_ADDRESS1" value="<?=$res1['LOC_ADDRESS1'][0]?>" size="20"></td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_STRUCTURE1" value="<?=$res1['LOC_STRUCTURE1'][0]?>" size="10"></td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_PREFER1" value="<?=$res1['LOC_PREFER1'][0]?>" size="5"></td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_NOTPREFER1" value="<?=$res1['LOC_NOTPREFER1'][0]?>" size="5"></td>
	                </tr>
	                <tr>
	                  <td>2</td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_NAME2" value="<?=$res1['LOC_NAME2'][0]?>" size="7"></td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_ADDRESS2" value="<?=$res1['LOC_ADDRESS2'][0]?>" size="20"></td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_STRUCTURE2" value="<?=$res1['LOC_STRUCTURE2'][0]?>" size="10"></td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_PREFER2" value="<?=$res1['LOC_PREFER2'][0]?>" size="5"></td>
	                  <td><input type="text" class="form-control input-sm" name="LOC_NOTPREFER2" value="<?=$res1['LOC_NOTPREFER2'][0]?>" size="5"></td>
	                </tr>
	                </tbody>
	                </table>
              	</div>
            </div>
        </div>
        <?php } 

	if ($_POST['raftype']=="indoor" && substr_count($_POST['actiondo'], 'BASE RF (RAF 1->4)')==1 &&	(substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	<br><p><input type="submit" class="btn btn-default <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?>  value="SAVE RADIO CHANGES 1->4"></p>
	</form>
	<?php
	}
	if ($_POST['raftype']=="outdoor" && substr_count($_POST['actiondo'], 'BASE RF (RAF 1->7)')==1
	&& (substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	<br><p><input type="submit" class="btn btn-default <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?>  value="SAVE RADIO CHANGES 1->7"></p>
	</form>
	<?php
	}



	if (substr_count($_POST['actiondo'], 'BASE RF (RAF 9)')==1 || substr_count($_POST['actiondo'], 'BASE RF (RAF 8->9)')==1 && (substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1)){
		$changeable_8_9="changeable";
	}
	?>
	<form action="scripts/raf/raf_actions.php" method="post" id="form_radio">
	<input type="hidden" name="action" value="update_radio_raf_8_9">
	<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
	<?php
	if ($_POST['raftype']=="outdoor"){ ?>
	
 		<div class="panel panel-default">
            <div class="panel-heading <?=$changeable_8_9?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#azimuthTilt">
                 8. AZIMUTH AND TILT PROPOSAL OPTIMISATION BASE
                </a>
              </h4>
            </div>
            <div id="azimuthTilt" class="panel-collapse collapse">
            	<div class="panel-body">
		            <div class="form-group">
		            	<label for="AZCAPTILT" class="col-sm-4 control-label">Azimuth, capacity, tilt</label>
		            	<div class="col-sm-8">
		                	<textarea name="AZCAPTILT" id="AZCAPTILT" rows="5" class="form-control input-sm"><?php echo unescape_quotes($res1['AZCAPTILT'][0]); ?></textarea>
		              	</div>
		            </div>
		        </div>
			</div>
		</div>
		<? } ?>
 		<div class="panel panel-default">
            <div class="panel-heading <?=$changeable_8_9?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#acpetanceReject">
                 9. ACCEPTANCE / REJECTION/ JUSTIFICATION ON VALIDATION BY BASECOMPANY
                </a>
              </h4>
            </div>
            <div id="acpetanceReject" class="panel-collapse collapse">
            	<div class="panel-body">
	            	<div class="form-group">
		            	<label for="JUSTIFICATION" class="col-sm-4 control-label">Acceptance / Rejection</label>
		            	<div class="col-sm-8">
		                	<textarea name="JUSTIFICATION" id="JUSTIFICATION" rows="5" class="form-control input-sm"><?php echo unescape_quotes($res1['JUSTIFICATION'][0]); ?></textarea>
		              	</div>
		            </div>
		            <div class="form-group">
		            	<label for="CONDITIONAL" class="col-sm-4 control-label">Conditional acceptance</label>
		            	<div class="col-sm-8">
		                	<textarea name="CONDITIONAL" id="CONDITIONAL" rows="5" class="form-control input-sm"><?php echo unescape_quotes($res1['CONDITIONAL'][0]); ?></textarea>
		              	</div>
		            </div>
		        </div>
			</div>
		</div>
	

<?php
	if ($_POST['raftype']=="outdoor" && substr_count($_POST['actiondo'], 'BASE RF (RAF 8->9)')==1
	&& (substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	<br><input type="submit" class="btn btn-default <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?> value="SAVE RADIO CHANGES 8->9">
	</form>
	<?
	}
	if ($_POST['raftype']=="indoor" && substr_count($_POST['actiondo'], 'BASE RF (RAF 9)')==1 && (substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1)){ ?>
	<br><p><input type="submit" class="btn btn-default <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?> value="SAVE RADIO CHANGES 9"></p>
	</form>
	<?
	}


	if (substr_count($_POST['actiondo'], 'BASE RF (RAF 10->11)')==1 || $_POST['saveAllowed']=="disabled"){
		$changeable_10_11="changeable";
		?>
		<form action="scripts/raf/raf_actions.php" method="post" id="form_radio">
		<input type="hidden" name="action" value="update_radio_raf_10_11">
		<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">
		<?php
	} ?>
	
 		<div class="panel panel-default">
            <div class="panel-heading <?=$changeable_10_11?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#freqPars">
                 10. FREQUENCIES AND PARAMETERS
                </a>
              </h4>
            </div>
            <div id="freqPars" class="panel-collapse collapse">
            	<div class="panel-body">
		        Input Frequencies and Parameters by Base: See Asset<br><br>
				NOTE: Frequencies and parameters to be checked with BASE OPTIM prior to DT creation.<br>
		        </div>
			</div>
		</div>

		<div class="panel panel-default">
            <div class="panel-heading <?=$changeable_10_11?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#budget">
                 11. BUDGET INFO
                </a>
              </h4>
            </div>
            <div id="budget" class="panel-collapse collapse">
            	<div class="panel-body">
					<div class="form-group">
		            	<label for="BUDGET" class="col-sm-4 control-label">Budget</label>
		            	<div class="col-sm-8">
		                	<textarea name="BUDGET" id="BUDGET" rows="5" class="form-control input-sm"><?php echo unescape_quotes($res1['BUDGET'][0]); ?></textarea>
		              	</div>
		            </div>
		        </div>
			</div>
		</div>
	<?php
	if (substr_count($_POST['actiondo'], 'BASE RF (RAF 10->11)')==1 &&	(substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1)){
	?>
	<input type="submit" class="btn btn-default <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?>  value="SAVE RADIO CHANGES 10->11">
	</form>
	<?php
	}



	if (substr_count($_POST['actiondo'], 'BASE RF (RAF 12+NET1)')==1){
		$changeable_12="changeable";
	}
	?>
	<form action="scripts/raf/raf_actions.php" method="post" id="form_radio">
	<input type="hidden" name="action" value="update_radio_raf_12">
	<input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">

 		<div class="panel panel-default">
            <div class="panel-heading <?=$changeable_12?>">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#pacCommentsPanel">
                 12. PAC COMMENTS &amp; SOCIAL MEDIA
                </a>
              </h4>
            </div>
            <div id="pacCommentsPanel" class="panel-collapse collapse">
            	<div class="panel-body">
					<div class="form-group">
		            	<label for="PACCOMMENTS" class="col-sm-4 control-label">PAC COMMENTS</label>
		            	<div class="col-sm-8">
		            		<select name="PACCOMMENTS" id="PACCOMMENTS" class="form-control input-sm" style="width: 50%">
		            		<option selected><?php echo unescape_quotes($res1['PACCOMMENTS'][0]); ?></option>
		            		<option>New 2G coverage</option>
		            		<option>New 3G coverage</option>
		            		<option>New LTE coverage</option>
		            		<option>New 2G, 3G and LTE coverage</option>
		            		<option>New 2G and 3G coverage</option>
		            		<option>New 2G and LTE coverage</option>
		            		<option>New 3G and LTE coverage</option>
		            		<option>2G coverage improved</option>
		            		<option>3G coverage improved</option>
		            		<option>LTE coverage improved</option>
		            		<option>2G, 3G and LTE coverage improved</option>
		            		<option>2G and 3G coverage improved</option>
		            		<option>2G and LTE coverage improved</option>
		            		<option>3G and LTE coverage improved</option>
		            		</select>
		              	</div>
		            </div>
		            <div class="form-group">
		            	<label for="PACCOMMENTS" class="col-sm-4 control-label">COVERAGE INFO SOCIAL MEDIA</label>
		            	<div class="col-sm-8">
		            		<select name="COVERAGE_SOCIAL" id="COVERAGE_SOCIAL" class="form-control input-sm" style="width: 75%">
		            		<option selected><?php echo unescape_quotes($res1['COVERAGE_SOCIAL'][0]); ?></option>
		            		<option>No change in coverage</option>
		            		<option>All customers making use of BASE network have now 2G coverage</option>
		            		<option>All customers making use of BASE network have now 3G coverage</option>
		            		<option>All customers making use of BASE network have now LTE coverage</option>
		            		<option>All customers making use of BASE network have now 2G, 3G and LTE coverage</option>
		            		<option>All customers making use of BASE network have now 2G and 3G coverage</option>
		            		<option>All customers making use of BASE network have now 2G and LTE coverage</option>
		            		<option>All customers making use of BASE network have now 3G and LTE coverage</option>
		            		<option>All customers making use of BASE network have now 2G coverage improved</option>
		            		<option>All customers making use of BASE network have now 3G coverage improved</option>
		            		<option>All customers making use of BASE network have now LTE coverage improved</option>
		            		<option>All customers making use of BASE network have now 2G, 3G and LTE coverage improved</option>
		            		<option>All customers making use of BASE network have now 2G and 3G coverage improved</option>
		            		<option>All customers making use of BASE network have now 2G and LTE coverage improved</option>
		            		<option>All customers making use of BASE network have now 3G and LTE coverage improved</option>
		            		<option>All customers making use of BASE network have now 3G coverage improved and new LTE coverage</option>
							<option>All customers making use of BASE network have now LTE coverage improved and new 3G coverage</option>
		            		</select>
		                </div>
		            </div>
		            <div class="form-group">
		            	<label for="PACCOMMENTS" class="col-sm-4 control-label">AREA INFO SOCIAL MEDIA</label>
		            	<div class="col-sm-8">
		                	<textarea name="AREA_SOCIAL" id="AREA_SOCIAL" rows="5" class="form-control input-sm"><?php echo unescape_quotes($res1['AREA_SOCIAL'][0]); ?></textarea>
		              	</div>
		            </div>
		        </div>
			</div>
		</div>
	
<?
	if ((substr_count($_POST['actiondo'], 'BASE RF (RAF 12+NET1)')==1) && (substr_count($guard_groups, 'Base_RF')==1 || substr_count($guard_groups, 'Administrators')==1))
	{
	?>
	<br><p><input type="submit" class="btn btn-primary <?=$_POST['saveAllowed']?>" <?=$_POST['saveAllowed']?>  value="SAVE RADIO CHANGES 12"></p>
	</form>
	<?
	}
?>
</div><!-- end accordiongroup-->


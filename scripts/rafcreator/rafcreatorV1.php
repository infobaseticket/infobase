<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_RF","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

?>
<script type="text/javascript">
$(document).ready( function(){
  $.fn.disableOptions = function(status) {
    var elID=$(this).attr("id");
    if (status==true){
      $('#'+elID+' option:not(:selected)').each(function(){
          $(this).attr('disabled', true);
      })
    }else{
      $('#'+elID+' option').each(function(){
        $(this).attr('disabled', false);
      })
    }
    
  }
 
  $('#inputRF_FUND').hide();
  $('#inputRF_FUND2').hide();
  $('.indoor').hide();

  $('#inputNET1_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true);
  $('#inputSTATUS_FUND').val('NA').attr('readonly', true).disableOptions(true);
  $('#inputNET1_FUND').val('NA').attr('readonly', true).disableOptions(true);
  $('#inputRF_PAC').val('NA').attr('readonly', true).disableOptions(true);

	$("#buffer").change(function(){
		if ($(this).is(':checked')){
			$('#inputOTHER').val('NA').attr('readonly', true).disableOptions(true); 
			$('#inputRADIO').val('OK').attr('readonly', true).disableOptions(true);
			$('#inputTXMN').val('OK').attr('readonly', true).disableOptions(true);
      $('#inputPARTNER').val('OK').attr('readonly', true).disableOptions(true); //ALU_INP
			$('#inputBCS_STATUS').val('OK').attr('readonly', true).disableOptions(true);
      $('#inputPARTNER_ACQUIRED').val('OK').attr('readonly', true).disableOptions(true); //ALU_ACQUIRED
      $('#inputTXMN_ACQUIRED').val('OK').attr('readonly', true).disableOptions(true);
      $('#inputRF_PAC').val('NOT OK').attr('readonly', true).disableOptions(true);
      $('#inputNET1_ACQUIRED').val('NOT OK').attr('readonly', true).disableOptions(true);
      $('#inputRF_FUND').show();
      $('#inputTRXREQUIREMENTS').show();

      if ($("#inputTYPE").val().indexOf("Upgrade")!=-1){
        $('#inputBCS_STATUS').val('NA').attr('readonly', true).disableOptions(true);
      }
      if ($("#inputTYPE").val().indexOf("CTX Upgrade")!=-1){
        $('#inputOTHER').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputRADIO').val('NA').attr('readonly', true).disableOptions(true);
      }
      if ($("#inputTYPE").val().indexOf("CWK Upgrade")!=-1){
        $('#inputRF_PAC').val('NA').attr('readonly', true).disableOptions(true);
      }
      if ($("#inputTYPE").val().indexOf("MSH Upgrade")!=-1){
        $('#inputOTHER').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputRADIO').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputTXMN').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputPARTNER').val('NA').attr('readonly', true).disableOptions(true); //ALU_INP
        $('#inputBCS_STATUS').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputPARTNER_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true); //ALU_ACQUIRED
        $('#inputTXMN_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputNET1_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputRF_PAC').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputNET1_FUND').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputRF_FUND2').show();
      }
      
		}else{ //not buffer
			$('#inputOTHER').val('NA').attr('readonly', true).disableOptions(true); 
			$('#inputRADIO').val('NOT OK').attr('readonly', false).disableOptions(false);
			$('#inputTXMN').val('NOT OK').attr('readonly', false).disableOptions(false);
      $('#inputPARTNER').val('NOT OK').attr('readonly', false).disableOptions(false); //ALU_INP
			$('#inputBCS_STATUS').val('NOT OK').attr('readonly', false).disableOptions(false);
      $('#inputPARTNER_ACQUIRED').val('NOT OK').attr('readonly', false).disableOptions(false); //ALU_ACQUIRED
      $('#inputTXMN_ACQUIRED').val('NOT OK').attr('readonly', false).disableOptions(false);
      $('#inputRF_PAC').val('NOT OK').attr('readonly', true).disableOptions(true);
      $('#inputNET1_ACQUIRED').val('NOT OK').attr('readonly', true).disableOptions(true);
      $('#inputRF_FUND').hide();
      $('#inputTRXREQUIREMENTS').hide();

      if ($("#inputTYPE").val().indexOf("Upgrade")!=-1){
        $('#inputBCS_STATUS').val('NA').attr('readonly', true).disableOptions(true);
      }
      if ($("#inputTYPE").val().indexOf("CTX Upgrade")!=-1){
        $('#inputOTHER').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputRADIO').val('NA').attr('readonly', true).disableOptions(true);
      }
      if ($("#inputTYPE").val().indexOf("CWK Upgrade")!=-1){
        $('#inputRF_PAC').val('NA').attr('readonly', true).disableOptions(true);
      }
      if ($("#inputTYPE").val().indexOf("MSH Upgrade")!=-1){
        $('#inputOTHER').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputRADIO').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputTXMN').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputPARTNER').val('NA').attr('readonly', true).disableOptions(true); //ALU_INP
        $('#inputBCS_STATUS').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputPARTNER_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true); //ALU_ACQUIRED
        $('#inputTXMN_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputNET1_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputRF_PAC').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputNET1_FUND').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputRF_FUND2').show();
      }
		}
	});
	$("#inputTYPE").change(function(){

		var inputTYPEval=$(this).val();
    //default values
    $("#buffer").attr('checked', false);
    $('#inputOTHER').val('NA').attr('readonly', true).disableOptions(true);
    $('#inputRADIO').val('NOT OK').attr('readonly', false).disableOptions(false);
    $('#inputTXMN').val('NOT OK').attr('readonly', false).disableOptions(false);
    $('#inputPARTNER').val('NOT OK').attr('readonly', false).disableOptions(false);
    $('#inputBCS_STATUS').val('NOT OK').attr('readonly', false).disableOptions(false);
    $('#inputPARTNER_ACQUIRED').val('NOT OK').attr('readonly', false).disableOptions(false);
    $('#inputTXMN_ACQUIRED').val('NOT OK').attr('readonly', false).disableOptions(false);
    $('#inputNET1_ACQUIRED').val('NOT OK').attr('readonly', false).disableOptions(false);
    $('#inputNET1_FUND').val('NOT OK').attr('readonly', false).disableOptions(false);
    $('#inputRF_PAC').val('NOT OK').attr('readonly', false).disableOptions(false);
    $('#inputRF_FUND2').hide();

    if ($("#inputTYPE").val()=="New Indoor" || $("#inputTYPE").val()=="Indoor Upgrade" || $("#inputTYPE").val()=="IND Upgrade" || $("#inputTYPE").val()=="RPT Upgrade" ){
      $('.indoor').show();
    }else{
      $('.indoor').hide();
    }
    if ($('#buffer').is(':checked')){
      $('#inputRF_FUND').show();
    }else{
      $('#inputRF_FUND').hide();
    }
		if (inputTYPEval.indexOf("Upgrade")!=-1){
			$('#inputBCS_STATUS').val('NA').attr('readonly', true).disableOptions(true);
		}
		if ($("#inputTYPE").val().indexOf("CTX Upgrade")!=-1){
      $('#inputOTHER').val('NA').attr('readonly', true).disableOptions(true);
      $('#inputRADIO').val('NA').attr('readonly', true).disableOptions(true);
    }
    if ($("#inputTYPE").val().indexOf("CWK Upgrade")!=-1){
      $('#inputRF_PAC').val('NA').attr('readonly', true).disableOptions(true);
    }
    if ($("#inputTYPE").val().indexOf("MSH Upgrade")!=-1){
      $('#inputOTHER').val('NA').attr('readonly', true).disableOptions(true);
      $('#inputRADIO').val('NA').attr('readonly', true).disableOptions(true);
      $('#inputTXMN').val('NA').attr('readonly', true).disableOptions(true);
      $('#inputPARTNER').val('NA').attr('readonly', true).disableOptions(true); //ALU_INP
      $('#inputBCS_STATUS').val('NA').attr('readonly', true).disableOptions(true);
      $('#inputPARTNER_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true); //ALU_ACQUIRED
      $('#inputTXMN_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true);
      $('#inputNET1_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true);
      $('#inputRF_PAC').val('NA').attr('readonly', true).disableOptions(true);
      $('#inputNET1_FUND').val('NA').attr('readonly', true).disableOptions(true);
      $('#inputRF_FUND2').show();
    }
	});
  $("#inputRFINFO").change(function(){
      var inputCOMval=$(this).val();
      if (inputCOMval.indexOf("Mini RPT Coiler")!=-1 ||inputCOMval.indexOf("Mini RPT Coiler")!=-1 || inputCOMval.indexOf("Dismantling")!=-1 ){
        $('#inputTXMN').val('NA').attr('readonly', true).disableOptions(true);
        $('#inputTXMN_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true);
      }
  });

  $('#inputCOMMERCIAL').selectize({
      create: true,
  });
  $('#inputRFINFO').selectize({
      create: true,
      onChange: function(input) {
        if (input=="Mini RPT Coiler" || input=="Mini RPT Coiler" || input=="Dismantling"){
          $('#inputTXMN').val('NA').attr('readonly', true).disableOptions(true);
          $('#inputTXMN_ACQUIRED').val('NA').attr('readonly', true).disableOptions(true);
        }
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
  $('#sitetype').change(function(){
    if (this.value === "polygon1") {
      $('.polygon1').removeClass('hidden');
      $('.polygon2').addClass('hidden');
      $('.polygon3').addClass('hidden');
      $('.polygon4').addClass('hidden');
    }else if (this.value === "polygon2") {
      $('.polygon2').removeClass('hidden');
      $('.polygon1').addClass('hidden');
      $('.polygon3').addClass('hidden');
      $('.polygon4').addClass('hidden');
    }else if (this.value === "polygon3") {
      $('.polygon3').removeClass('hidden');
      $('.polygon1').addClass('hidden');
      $('.polygon2').addClass('hidden');
      $('.polygon4').addClass('hidden');
    }else if (this.value === "polygon4") {
      $('.polygon4').removeClass('hidden');
      $('.polygon1').addClass('hidden');
      $('.polygon2').addClass('hidden');
      $('.polygon3').addClass('hidden');
    }
  });

  function after_RAFCreation_save(response)  {  
    $('#spinner').spin(false);
    Messenger().post({
          message:  response.message,
          type:  response.type,
          showCloseButton: true
        });
  } 
  var options = {
    success: after_RAFCreation_save,
    dataType:  'json',
    url:'scripts/rafcreator/rafcreator_actions.php'
  };
  $('#createButton').click(function() { 
      $('#spinner').spin('large');
      $('#rafcreatorForm').ajaxSubmit(options); 
      return false; 
  });

});
</script>

 <form class="form-horizontal" role="form" method="post" id="rafcreatorForm" action="scripts/rafcreator/rafcreator_actions.php">
  <input type="hidden" name="action" value="createRafs">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <div class="well" style="background-color:#428bca;">
          <h3>Sites & NET1 links</h3>
          <textarea class="form-control" rows="10" name="sitelist"></textarea>
          <i>example SITEID, NET1 LINK, XCOORD,YCOORD<i>:<br> BW4550,9976545678,12345,35543<br>AN0123,_AN0123A,99943,998746<br>BW4550,,99943,998746</i>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-default">
        <div class="panel-heading">RAF INFO</div>
        <div class="panel-body">
          <div class="form-group">
            <label for="inputTYPE" class="col-sm-5 control-label">RAF TYPE</label>
            <div class="col-sm-7">
              <select name="RAFTYPE" class="form-control input-sm" id="inputTYPE">
                <option value=''>Please select</option>
                <?php 
                $query = "select DISTINCT(TYPE) from BSDS_RAF WHERE TYPE IS NOT NULL AND TYPE!='Dismantling' ORDER BY TYPE";
                $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
                if (!$stmt) {
                    die_silently($conn_Infobase, $error_str);
                    exit;
                } else {
                  OCIFreeStatement($stmt);
                  $amount_of_TYPE=count($res['TYPE']);
                  for ($i = 0; $i <$amount_of_TYPE; $i++) { 
                    echo "<option value='".$res['TYPE'][$i]."'>".$res['TYPE'][$i]."</option>";
                  } 
                }
                ?>
                </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-7">
              <div class="checkbox">
                <label><input type="checkbox" name="BUFFER" value="1" id="buffer"> <b>Buffer sites</b></label>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="inputJUSTIFICATION" class="col-sm-5 control-label">JUSTIFICATION</label>
            <div class="col-sm-7">
              <?php
              $justification="Minimum required info\n------------------------------\nReason for RAF.\nFor replacement/move: Onair date\nReason for replacement\nFor CWK: Due date\n";
              ?>
              <textarea name="JUSTIFICATION" class="form-control input-sm" rows=7 id="inputJUSTIFICATION"><?=$justification?></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputRFINFO" class="col-sm-5 control-label">RFINFO</label>
            <div class="col-sm-7">
              <select name="RFINFO" class="form-control input-sm" id="inputRFINFO">
                <option>Plaese select</option>
                <?php 
                $query = "select DISTINCT(RFINFO) from BSDS_RAF WHERE RFINFO IS NOT NULL ORDER BY RFINFO";
                $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
                if (!$stmt) {
                    die_silently($conn_Infobase, $error_str);
                    exit;
                } else {
                  OCIFreeStatement($stmt);
                  $amount_of_RFINFO=count($res['RFINFO']);
                  for ($i = 0; $i <$amount_of_RFINFO; $i++) { 
                    echo "<option value='".$res['RFINFO'][$i]."'>".$res['RFINFO'][$i]."</option>";
                  } 
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputCOMMERCIAL" class="col-sm-5 control-label">COMMERCIAL</label>
            <div class="col-sm-7">
              <select name="COMMERCIAL" class="form-control input-sm" id="inputCOMMERCIAL">
                <option>Plaese select</option>
                <?php 
                $query = "select DISTINCT(COMMERCIAL) from BSDS_RAF WHERE COMMERCIAL IS NOT NULL ORDER BY COMMERCIAL";
                $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
                if (!$stmt) {
                    die_silently($conn_Infobase, $error_str);
                    exit;
                } else {
                  OCIFreeStatement($stmt);
                  $amount_of_COMMERCIAL=count($res['COMMERCIAL']);
                  for ($i = 0; $i <$amount_of_COMMERCIAL; $i++) { 
                    echo "<option value=".$res['COMMERCIAL'][$i].">".$res['COMMERCIAL'][$i]."</option>";
                  } 
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputBUDGETACQ" class="col-sm-5 control-label">BUDGET ACQ</label>
            <div class="col-sm-7">
              <input type="text" name="budget_acq" maxlength="20" id="inputBUDGETACQ" class="form-control" placeholder="RTN">
            </div>
          </div>
          <div class="form-group">
            <label for="inputBUDGETCON" class="col-sm-5 control-label">BUDGET CON</label>
            <div class="col-sm-7">
              <input type="text" name="budget_con" maxlength="20" id="inputBUDGETCON" class="form-control" placeholder="RTN">
            </div>
          </div>
          <div class="form-group" id="inputTRXREQUIREMENTS">
            <label for="inputTRXREQUIREMENTS" class="col-sm-5 control-label">TRX + BPC REQ.</label>
            <div class="col-sm-7">
              <textarea name="TRXREQUIREMENTS" class="form-control input-sm" rows="5"></textarea>
          </div>
          </div>
        </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">PROCESS STEPS</div>
          <div class="panel-body">
            <div class="form-group">
              <label for="inputOTHER" class="col-sm-5 control-label">OTHER INP</label>
              <div class="col-sm-7">
                <select name="OTHER_INP" class="form-control input-sm" id="inputOTHER">
                  <option>NA</option>
                  <option>OK</option>
                  <option>NOT OK</option>
                </select>
              </div>
            </div>
            
            <div class="form-group">
              <label for="inputRADIO" class="col-sm-5 control-label">RADIO INP</label>
              <div class="col-sm-7">
                <select name="RADIO_INP" class="form-control input-sm" id="inputRADIO">
                  <option>NOT OK</option><option>OK</option><option>NA</option></select>
              </div>
            </div>

            <div class="form-group">
              <label for="inputTXMN" class="col-sm-5 control-label">TXMN INP</label>
              <div class="col-sm-7">
                <select name="TXMN_INP" class="form-control input-sm" id="inputTXMN">
                  <option>NOT OK</option><option>OK</option><option>NA</option></select>
              </div>
            </div>

            <div class="form-group">
              <label for="inputPARTNER" class="col-sm-5 control-label">PARTNER INP</label>
              <div class="col-sm-7">
                <select name="ALU_INP" class="form-control input-sm" id="inputPARTNER">
                  <option>NOT OK</option><option>OK</option><option>NA</option></select>
              </div>
            </div>

            <div class="form-group">
              <label for="inputBCS_STATUS" class="col-sm-5 control-label">BCS STATUS</label>
              <div class="col-sm-7">
                <select name="BCS_STATUS" class="form-control input-sm" id="inputBCS_STATUS">
                  <option>NOT OK</option><option>OK</option><option>NA</option></select>
              </div>
            </div>

            <div class="form-group">
              <label for="inputPARTNER_ACQUIRED" class="col-sm-5 control-label">PARTNER ACQ</label>
              <div class="col-sm-7">
                <select name="ALU_ACQUIRED" class="form-control input-sm" id="inputPARTNER_ACQUIRED">
                  <option>NOT OK</option><option>OK</option><option>NA</option></select>
              </div>
            </div>

            <div class="form-group">
              <label for="inputTXMN_ACQUIRED" class="col-sm-5 control-label">TXMN AQ</label>
              <div class="col-sm-7">
                <select name="TXMN_ACQUIRED" class="form-control input-sm" id="inputTXMN_ACQUIRED">
                  <option value="NOT OK">NOT OK</option><option vlaue="OK">OK</option><option vlau="NA">NA</option></select>
              </div>
            </div>

            <div class="form-group">
              <label for="inputNET1_ACQUIRED" class="col-sm-5 control-label">RAF ACQUIRED</label>
              <div class="col-sm-7">
                <select name="NET1_ACQUIRED" class="form-control input-sm" id="inputNET1_ACQUIRED">
                  <option>NOT OK</option><option>OK</option><option>NA</option></select>
              </div>
            </div>

            <div class="form-group" id="inputRF_FUND2">
              <label for="inputRF_FUNDB" class="col-sm-5 control-label">RF FUND</label>
              <div class="col-sm-7">
                <input type='text' name="STATUS_FUND" class="form-control input-sm" id="inputRF_FUNDB" value="MSH">
              </div>
            </div>

            <div class="form-group" id="inputRF_FUND">
              <label for="inputRF_FUND" class="col-sm-5 control-label">RF FUND</label>
              <div class="col-sm-7">
                <div class="row">
                  <div class="col-md-4">
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="STATUS_FUND_G9" value="G9"> G9
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="STATUS_FUND_G18" value="G18"> G18
                    </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="STATUS_FUND_U9" value="U9"> U9
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="STATUS_FUND_U21" value="U21"> U21
                    </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="STATUS_FUND_L8" value="L8"> L8
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="STATUS_FUND_L18" value="L18"> L18
                    </label>
                    </div>
                    <div class="checkbox">
                    <label>
                      <input type="checkbox" name="STATUS_FUND_L26" value="L26"> L26
                    </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="inputNET1_FUND" class="col-sm-5 control-label">FUND U/A353</label>
              <div class="col-sm-7">
                <select name="NET1_FUND" class="form-control input-sm" id="inputNET1_FUND">
                  <option>NOT OK</option><option>OK</option><option>NA</option></select>
              </div>
            </div>

            <div class="form-group">
              <label for="inputPACSTATUS" class="col-sm-5 control-label">PARTNER PAC</label>
              <div class="col-sm-7">
                <select name="PAC_STATUS" class="form-control input-sm" id="inputPACSTATUS">
                  <option>NOT OK</option><option>OK</option></select>
              </div>
            </div>

            <div class="form-group">
              <label for="inputRF_PAC" class="col-sm-5 control-label">RF PAC</label>
              <div class="col-sm-7">
                <select name="RF_PAC" class="form-control input-sm" id="inputRF_PAC">
                  <option>NOT OK</option><option>OK</option><option>NA</option></select>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
        <div class="panel-group" id="accordion">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#SiteData">
                 1. SITE DATA
                </a>
              </h4>
            </div>
            <div id="SiteData" class="panel-collapse collapse in">
              <div class="panel-body">
                <div class="form-group indoor">
                  <label for="XCOORD" class="col-sm-2 control-label">Address</label>
                  <div class="col-sm-10">
                   <textarea rows="4" class="form-control" name="ADDRESS"></textarea>
                  </div>
                </div>

                <div class="form-group indoor">
                  <label for="CONTACT" class="col-sm-2 control-label">Contact person</label>
                  <div class="col-sm-10">
                   <input type="text" class="form-control" name="CONTACT" id="CONTACT">
                  </div>
                </div>

                <div class="form-group indoor">
                  <label for="PHONE" class="col-sm-2 control-label">Phone number</label>
                  <div class="col-sm-10">
                   <input type="text" class="form-control" name="PHONE" id="PHONE">
                  </div>
                </div>

                <div class="form-group indoor">
                  <label for="CONTACT" class="col-sm-2 control-label">Site type</label>
                  <div class="col-sm-10">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="SITETYPE" value="Tunnel"> Tunnel
                      </label>
                    </div>
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="SITETYPE" value="Indoor"> Indoor
                      </label>
                    </div>
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="SITETYPE" value="Other"> Other
                      </label>
                    </div>
                  </div>
                </div>

                 <div class="form-group indoor">
                  <label for="CONTACT" class="col-sm-2 control-label">Site sharing</label>
                  <div class="col-sm-10">
                    <div class="radio">
                      <label>
                        <input type="radio" name="SITESHARING" value="Unknown"> None
                      </label>
                    </div>
                    <div class="radio">
                      <label>
                        <input type="radio" name="SITESHARING" value="Mobistar"> Mobistar
                      </label>
                    </div>
                    <div class="radio">
                      <label>
                        <input type="radio" name="SITESHARING" value="MobistarProximus"> Mobistar + Proximus
                      </label>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="inputRF_FUND" class="col-sm-2 control-label">BAND ACQ.</label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="checkbox">
                        <label>
                          <input type="checkbox" name="BAND_900" class="band" data-techno="G9" value="1"> G9
                        </label>
                        </div>
                        <div class="checkbox">
                        <label>
                          <input type="checkbox" name="BAND_1800" class="band" data-techno="G18" value="1"> G18
                        </label>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="checkbox">
                        <label>
                          <input type="checkbox" name="BAND_UMTS900" class="band" data-techno="U9" value="1"> U9
                        </label>
                        </div>
                        <div class="checkbox">
                        <label>
                          <input type="checkbox" name="BAND_UMTS" class="band" data-techno="U21" value="1"> U21
                        </label>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="checkbox">
                        <label>
                          <input type="checkbox" name="BAND_LTE800" class="band" data-techno="L8" value="1"> L8
                        </label>
                        </div>
                        <div class="checkbox">
                        <label>
                          <input type="checkbox" name="BAND_LTE1800" class="band" data-techno="L18" value="1"> L18
                        </label>
                        </div>
                        <div class="checkbox">
                        <label>
                          <input type="checkbox" name="BAND_LTE2600" class="band" data-techno="L26" value="1"> L26
                        </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group hidden" id="VENDOR_G9">
                  <label for="VENDOR2G_GSM900" class="col-sm-5 control-label">2G vendor GSM900</label>
                  <div class="col-sm-7">
                    <select name="VENDOR2G_GSM900" class="form-control input-sm" id="VENDOR2G_GSM900">
                      <option>Please select</option><option>Ericsson</option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NA</option></select>
                  </div>
                </div>

                <div class="form-group hidden" id="VENDOR_G18">
                  <label for="VENDOR2G_GSM1800" class="col-sm-5 control-label">2G vendor GSM1800</label>
                  <div class="col-sm-7">
                    <select name="VENDOR2G_GSM1800" class="form-control input-sm" id="VENDOR2G_GSM1800">
                      <option>Please select</option><option>Ericsson</option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NA</option></select>
                  </div>
                </div>

                <div class="form-group hidden" id="VENDOR_U9">
                  <label for="VENDOR3G_UMTS900" class="col-sm-5 control-label">3G vendor UMTS900</label>
                  <div class="col-sm-7">
                    <select name="VENDOR3G_UMTS900" class="form-control input-sm" id="VENDOR3G_UMTS900">
                      <option>Please select</option><option>Ericsson</option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NA</option></select>
                  </div>
                </div>

                <div class="form-group hidden" id="VENDOR_U21">
                  <label for="VENDOR3G_UMTS" class="col-sm-5 control-label">3G vendor UMTS2100</label>
                  <div class="col-sm-7">
                    <select name="VENDOR3G_UMTS" class="form-control input-sm" id="VENDOR3G_UMTS">
                      <option>Please select</option><option>Ericsson</option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NA</option></select>
                  </div>
                </div>

                <div class="form-group hidden" id="VENDOR_L8">
                  <label for="VENDOR4G_LTE800" class="col-sm-5 control-label">4G vendor LTE800</label>
                  <div class="col-sm-7">
                    <select name="VENDOR4G_LTE800" class="form-control input-sm" id="VENDOR4G_LTE800">
                      <option>Please select</option><option>Ericsson</option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NA</option></select>
                  </div>
                </div>

                <div class="form-group hidden" id="VENDOR_L18">
                  <label for="VENDOR4G_LTE1800" class="col-sm-5 control-label">4G vendor LTE1800</label>
                  <div class="col-sm-7">
                    <select name="VENDOR4G_LTE1800" class="form-control input-sm" id="VENDOR4G_LTE1800">
                      <option>Please select</option><option>Ericsson</option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NA</option></select>
                  </div>
                </div>

                <div class="form-group hidden" id="VENDOR_L26">
                  <label for="VENDOR4G_LTE2600" class="col-sm-5 control-label">4G vendor LTE2600</label>
                  <div class="col-sm-7">
                    <select name="VENDOR4G_LTE2600" class="form-control input-sm" id="VENDOR4G_LTE2600">
                      <option>Please select</option><option>Ericsson</option><option>ZTE</option><option>COILER</option><option>ANDREW</option><option>NA</option></select>
                  </div>
                </div>
              
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#coverageObj">
                  2. COVERAGE OBECTIVES
                </a>
              </h4>
            </div>
            <div id="coverageObj" class="panel-collapse collapse">
              <div class="panel-body">
                <textarea name="COVERAGE_OBJECTIVE" rows="10" class="form-control input-sm"></textarea>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#coveragekpi">
                  3. COVERAGE KPI'S
                </a>
              </h4>
            </div>
            <div id="coveragekpi" class="panel-collapse collapse">
              <div class="panel-body">
                <select name="SITETYPE2" class="form-control input-sm" id='sitetype'><option>Please select</option><option value="polygon1">Polygon1: City indoor</option><option value="polygon2">Polygon2: Indoor residential</option><option value="polygon3">Polygon3: In car</option><option value="polygon4">Polygon4: Outdoor</option></select>
                <br>
                <table class="polygon1 table hidden">
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
                  <td><input type="text" name="AREA1_900" class="form-control input-sm" value="<?=$AREA1_900?>"></td>
                </tr>
                <tr>
                  <td>GSM1800</td>
                  <td>Indoor</td>
                  <td>-66 dBm</td>
                  <td><input type="text" name="AREA1_1800" class="form-control input-sm" value="<?=$AREA1_1800?>"></td>
                </tr>
                <tr>
                  <td>UMTS2100</td>
                  <td>Indoor</td>
                  <td>-75 dBm</td>
                  <td><input type="text" name="AREA1_UMTS" class="form-control input-sm" value="<?=$AREA1_UMTS?>"></td>
                </tr>
                <tr>
                  <td>UMTS900</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA1_UMTS900" class="form-control input-sm" value="<?=$AREA1_UMTS900?>"></td>
                </tr>
                <tr>
                  <td>LTE800</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA1_LTE800" class="form-control input-sm" value="<?=$AREA1_LTE800?>"></td>
                </tr>
                <tr>
                  <td>LTE1800</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA1_LTE1800" class="form-control input-sm" value="<?=$AREA1_LTE1800?>"></td>
                </tr>
                <tr>
                  <td>LTE2600</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA1_LTE2600" class="form-control input-sm" value="<?=$AREA1_LTE2600?>"></td>
                </tr>
                </tbody>
                </table>

                <table class="polygon2 table hidden">
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
                  <td><input type="text" name="AREA2_900" class="form-control input-sm" value="<?=$AREA2_900?>"></td>
                </tr>
                <tr>
                  <td>GSM1800</td>
                  <td>Indoor</td>
                  <td>-71 dBm</td>
                  <td><input type="text" name="AREA2_1800" class="form-control input-sm" value="<?=$AREA2_1800?>"></td>
                </tr>
                <tr>
                  <td>UMTS2100</td>
                  <td>Indoor</td>
                  <td>-84 dBm</td>
                  <td><input type="text" name="AREA2_UMTS" class="form-control input-sm" value="<?=$AREA2_UMTS?>"></td>
                </tr>
                <tr>
                  <td>UMTS900</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA2_UMTS900" class="form-control input-sm" value="<?=$AREA2_UMTS900?>"></td>
                </tr>
                <tr>
                  <td>LTE800</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA2_LTE800" class="form-control input-sm" value="<?=$AREA2_LTE800?>"></td>
                </tr>
                <tr>
                  <td>LTE1800</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA2_LTE1800" class="form-control input-sm" value="<?=$AREA2_LTE1800?>"></td>
                </tr>
                <tr>
                  <td>LTE2600</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA2_LTE2600" class="form-control input-sm" value="<?=$AREA2_LTE2600?>"></td>
                </tr>
                </tbody>
                </table>

                <table class="polygon3 table hidden">
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
                  <td><input type="text" name="AREA3_GSM900" class="form-control input-sm" value="<?=$AREA3_900?>"></td>
                </tr>
                <tr>
                  <td>GSM1800</td>
                  <td>Indoor</td>
                  <td>-75 dBm</td>
                  <td><input type="text" name="AREA3_GSM1800" class="form-control input-sm" value="<?=$AREA3_1800?>"></td>
                </tr>
                <tr>
                  <td>UMTS2100</td>
                  <td>Indoor</td>
                  <td>-88 dBm</td>
                  <td><input type="text" name="AREA3_UMTS" class="form-control input-sm" value="<?=$AREA3_UMTS?>"></td>
                </tr>
                <tr>
                  <td>UMTS900</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA3_UMTS900" class="form-control input-sm" value="<?=$AREA3_UMTS900?>"></td>
                </tr>
                <tr>
                  <td>LTE800</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA3_LTE800" class="form-control input-sm" value="<?=$AREA3_LTE800?>"></td>
                </tr>
                <tr>
                  <td>LTE1800</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA3_LTE1800" class="form-control input-sm" value="<?=$AREA3_LTE1800?>"></td>
                </tr>
                <tr>
                  <td>LTE2600</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA3_LTE2600" class="form-control input-sm" value="<?=$AREA3_LTE2600?>"></td>
                </tr>
                </tbody>
                </table>

                <table class="polygon4 table hidden">
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
                  <td><input type="text" name="AREA4_GSM900" class="form-control input-sm" value="<?=$AREA4_900?>"></td>
                </tr>
                <tr>
                  <td>GSM1800</td>
                  <td>Indoor</td>
                  <td>-75 dBm</td>
                  <td><input type="text" name="AREA4_GSM1800" class="form-control input-sm" value="<?=$AREA4_1800?>"></td>
                </tr>
                <tr>
                  <td>UMTS2100</td>
                  <td>Indoor</td>
                  <td>-88 dBm</td>
                  <td><input type="text" name="AREA4_UMTS" class="form-control input-sm" value="<?=$AREA4_UMTS?>"></td>
                </tr>
                <tr>
                  <td>UMTS900</td>
                  <td>Indoor</td>
                  <td>-88 dBm</td>
                  <td><input type="text" name="AREA4_UMTS900" class="form-control input-sm" value="<?=$AREA4_UMTS900?>"></td>
                </tr>
                <tr>
                  <td>LTE800</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA4_LTE800" class="form-control input-sm" value="<?=$AREA4_LTE800?>"></td>
                </tr>
                <tr>
                  <td>LTE1800</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA4_LTE1800" class="form-control input-sm" value="<?=$AREA4_LTE1800?>"></td>
                </tr>
                <tr>
                  <td>LTE2600</td>
                  <td>Indoor</td>
                  <td>- dBm</td>
                  <td><input type="text" name="AREA4_LTE2600" class="form-control input-sm" value="<?=$AREA4_LTE2600?>"></td>
                </tr>
                </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">
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
                  <td><input type="text" name="THRESHOLD_900" class="form-control input-sm" value="<?=$THRESHOLD_900?>"></td>
                  <td><input type="text" name="COVERAGE_900" class="form-control input-sm" value="<?=$COVERAGE_900?>"></td>
                  <td><input type="text" name="TOTCOVERAGE_900" class="form-control input-sm" value="<?=$TOTCOVERAGE_900?>"></td>
                </tr>
                <tr>
                  <td><input type="checkbox" NAME="INTER_1800" VALUE="1" <?=$INTER_1800_check?>></td>
                  <td>GSM1800</td>
                  <td><input type="text" name="THRESHOLD_1800" class="form-control input-sm" value="<?=$THRESHOLD_1800?>"></td>
                  <td><input type="text" name="COVERAGE_1800" class="form-control input-sm" value="<?=$COVERAGE_1800?>"></td>
                  <td><input type="text" name="TOTCOVERAGE_1800" class="form-control input-sm" value="<?=$TOTCOVERAGE_1800?>"></td>
                </tr>
                <tr>
                  <td><input type="checkbox" NAME="INTER_UMTS" VALUE="1" <?=$INTER_UMTS_check?>></td>
                  <td>UMTS2100</td>
                  <td><input type="text" name="THRESHOLD_UMTS" class="form-control input-sm" value="<?=$THRESHOLD_UMTS?>"></td>
                  <td><input type="text" name="COVERAGE_UMTS" class="form-control input-sm" value="<?=$COVERAGE_UMTS?>"></td>
                  <td><input type="text" name="TOTCOVERAGE_UMTS" class="form-control input-sm" value="<?=$TOTCOVERAGE_UMTS?>"></td>
                </tr>
                <tr>
                  <td><input type="checkbox" NAME="INTER_UMTS900" VALUE="1" <?=$INTER_UMTS900_check?>></td>
                  <td>UMTS900</td>
                  <td><input type="text" name="THRESHOLD_UMTS900" class="form-control input-sm" value="<?=$THRESHOLD_UMTS900?>"></td>
                  <td><input type="text" name="COVERAGE_UMTS900" class="form-control input-sm" value="<?=$COVERAGE_UMTS900?>"></td>
                  <td><input type="text" name="TOTCOVERAGE_UMTS900" class="form-control input-sm" value="<?=$TOTCOVERAGE_UMTS900?>"></td>
                </tr>
                <tr>
                  <td><input type="checkbox" NAME="INTER_LTE800" VALUE="1" <?=$INTER_LTE800_check?>></td>
                  <td>LTE800</td>
                  <td><input type="text" name="THRESHOLD_LTE800" class="form-control input-sm" value="<?=$THRESHOLD_LTE800?>"></td>
                  <td><input type="text" name="COVERAGE_LTE800" class="form-control input-sm" value="<?=$COVERAGE_LTE800?>"></td>
                  <td><input type="text" name="TOTCOVERAGE_LTE800" class="form-control input-sm" value="<?=$TOTCOVERAGE_LTE800?>"></td>
                </tr>
                <tr>
                  <td><input type="checkbox" NAME="INTER_LTE1800" VALUE="1" <?=$INTER_LTE1800_check?>></td>
                  <td>LTE1800</td>
                  <td><input type="text" name="THRESHOLD_LTE1800" class="form-control input-sm" value="<?=$THRESHOLD_LTE1800?>"></td>
                  <td><input type="text" name="COVERAGE_LTE1800" class="form-control input-sm" value="<?=$COVERAGE_LTE1800?>"></td>
                  <td><input type="text" name="TOTCOVERAGE_LTE1800" class="form-control input-sm" value="<?=$TOTCOVERAGE_LTE1800?>"></td>
                </tr>
                <tr>
                  <td><input type="checkbox" NAME="INTER_LTE2600" VALUE="1" <?=$INTER_LTE2600_check?>></td>
                  <td>LTE2600</td>
                  <td><input type="text" name="THRESHOLD_LTE2600" class="form-control input-sm" value="<?=$THRESHOLD_LTE2600?>"></td>
                  <td><input type="text" name="COVERAGE_LTE2600" class="form-control input-sm" value="<?=$COVERAGE_LTE2600?>"></td>
                  <td><input type="text" name="TOTCOVERAGE_LTE2600" class="form-control input-sm" value="<?=$TOTCOVERAGE_LTE2600?>"></td>
                </tr>
                </table>
              </div>
            </div>
          </div> 

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#comments">
                 5. COMMENTS
                </a>
              </h4>
            </div>
            <div id="comments" class="panel-collapse collapse">
              <div class="panel-body">
                <textarea class="form-control" rows="10" name="COMMENTS"></textarea>
              </div>
            </div>
          </div> 

          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#checklist">
                6. CHECKLIST
                </a>
              </h4>
            </div>
            <div id="checklist" class="panel-collapse collapse">
              <div class="panel-body">
                <div class="form-group" id="nrSectors">
                  <label for="nrSectors" class="col-sm-5 control-label">Number of sectors</label>
                  <div class="col-sm-7">
                    <div class="row">
                      <div class="col-md-4">
                        <input TYPE="text" NAME="NRSECTORS_900" id="NRSECTORS_G9" class="form-control input-sm hidden" placeholder="G9">
                        <input TYPE="text" NAME="NRSECTORS_1800" id="NRSECTORS_G18" class="form-control input-sm hidden" placeholder="G18">
                      </div>
                      <div class="col-md-4">
                        <input TYPE="text" NAME="NRSECTORS_UMTS900" id="NRSECTORS_U9" class="form-control input-sm hidden" placeholder="U9">
                        <input TYPE="text" NAME="NRSECTORS_UMTS" id="NRSECTORS_U21" class="form-control input-sm hidden" placeholder="U21">
                      </div>
                      <div class="col-md-4">
                        <input TYPE="text" NAME="NRSECTORS_LTE800" id="NRSECTORS_L8" class="form-control input-sm hidden" placeholder="L8">
                        <input TYPE="text" NAME="NRSECTORS_LTE1800" id="NRSECTORS_L18" class="form-control input-sm hidden" placeholder="L18">
                        <input TYPE="text" NAME="NRSECTORS_LTE2600" id="NRSECTORS_L26" class="form-control input-sm hidden" placeholder="L26">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" NAME="POLYMAP" VALUE="1"> Map attached with polygons indicated
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" NAME="HMINMAX" VALUE="1"> Hmin / Hmax for RF antennas: <INPUT TYPE="text" NAME="HMINMAXRF" size="5">
                      </label>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" NAME="ANTBLOCKING" VALUE="1"> Antenna blocking by nearby building (angle):  <INPUT TYPE="text" NAME="ANGLE" size="5">
                      </label>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" NAME="RFGUIDES" VALUE="1">RF Guidlines compliant
                      </label>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" NAME="CONGUIDES" VALUE="1">Construction Guidlines compliant
                      </label>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">
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
                  <td><input type="text" class="form-control" name="LOC_NAME1" size="7"></td>
                  <td><input type="text" class="form-control" name="LOC_ADDRESS1" size="20"></td>
                  <td><input type="text" class="form-control" name="LOC_STRUCTURE1" size="10"></td>
                  <td><input type="text" class="form-control" name="LOC_PREFER1" size="5"></td>
                  <td><input type="text" class="form-control" name="LOC_NOTPREFER1" size="5"></td>
                </tr>
                <tr>
                  <td>2</td>
                  <td><input type="text" class="form-control" name="LOC_NAME2" size="7"></td>
                  <td><input type="text" class="form-control" name="LOC_ADDRESS2" size="20"></td>
                  <td><input type="text" class="form-control" name="LOC_STRUCTURE2" size="10"></td>
                  <td><input type="text" class="form-control" name="LOC_PREFER2" size="5"></td>
                  <td><input type="text" class="form-control" name="LOC_NOTPREFER2" size="5"></td>
                </tr>
                </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
 <br>
  <p class="text-center">
    <button type="button" class="btn btn-primary" id="createButton">CREATE RAFS</button>
  </p>
</form>
<br><br>
<p class="text-center"><i>
  For the moment, no RAFS can be created in bulk for Indoor sites!<br>If needed, please contact Frederick Eyland.
</i>
</p>
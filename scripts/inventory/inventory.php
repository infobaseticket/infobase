<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");


  $query = "select ACTION FROM MOVEMENT_LOG WHERE TRUNC(INSERTDATE)=TRUNC(SYSDATE) AND ACTION='OK' ORDER BY INSERTDATE";
  //echo $query;
  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
  if (!$stmt){
    die_silently($conn_Infobase, $error_str);
    exit;
  }else{
    OCIFreeStatement($stmt);
  }  
?>

<div class="row show-grid">
  <div class="col-md-8">
  <?php if (count($res['ACTION'])==0){ ?>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading success" role="tab" id="headingFileUpload" style='background-color:#f0ad4e;'>
          <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#InventoryFileUpload" aria-expanded="true" aria-controls="collapseOne">
            1. Upload inventory of today
            </a>
          </h4>
        </div>
        <div id="InventoryFileUpload" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingFileUpload">
          <div class="panel-body">
            <form role="form" id='InventoryUploadForm' action="scripts/inventory/file_upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="99000000">
                <input type="hidden" name="filetype" value="inventory">
                <div class="form-group">
                  <label for="InputFile">Please upload Inventory file of today:</label>
                  <input type="file" name="myfile[]" multiple="false" class="btn btn-default">
                </div>
                <input type="submit" id="UplaodFileBtn" value="Upload File to Server" class="btn btn-primary">
            </form>

            <div class="progress progress-striped" style="width:500px;">
              <div class="progress-bar" id="bar" role="progressbar" aria-valuenow="0" 
              aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                <span class="sr-only">60% Complete</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingInventoryDB"  style="background-color:#f0ad4e;">
          <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#InventoryDB" aria-expanded="false" aria-controls="collapseTwo">
            2. Upload inventory file into database
            </a>
          </h4>
        </div>
        <div id="InventoryDB" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingInventoryDB">
          <div class="panel-body">
            <button class="btn btn-primary hidden" id="InventoryDBBtn" type="submit">Insert into db</button>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingInventoryChecks" style="background-color:#f0ad4e;">
          <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#InventoryChecker" aria-expanded="false" aria-controls="collapseThree">
            3. Analyse the inventory data
            </a>
          </h4>
        </div>
        <div id="InventoryChecker" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingInventoryChecks">
          <div class="panel-body">
            <button class="btn btn-primary hidden" id="InventoryCheckBtn" type="submit">Check if the data is ok</button>
          <div id="InventoryResponse"></div>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading success" role="tab" id="headingMovementFileUpload" style='background-color:#f0ad4e;'>
          <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#MovementFileUpload" aria-expanded="true" aria-controls="collapseOne">
            4. Upload movement of today
            </a>
          </h4>
        </div>
        <div id="MovementFileUpload" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingMovementFileUpload">
          <div class="panel-body">
            <form role="form" id='MovementUploadForm' action="scripts/inventory/file_upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="99000000">
                <input type="hidden" name="filetype" value="movement">

                <div class="form-group">
                  <label for="InputFile">Please upload Movement file of today:</label>
                  <input type="file" name="myfile[]" multiple="false" class="btn btn-default">
                </div>
                <input type="submit" id="UploadMovementBtn" value="Upload File to Server" class="btn btn-primary hidden">
            </form>
            <?php
            if (substr_count($guard_groups, 'Admin')==1 ){ ?>
            <button class="btn btn-primary hidden" id="InventoryMakeMinusOneBtn" type="submit">Make Inventory Yesterday</button>
            <?php
            }
            ?>

            <div class="progress progress-striped" style="width:500px;">
              <div class="progress-bar" id="barMovement" role="progressbar" aria-valuenow="0" 
              aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                <span class="sr-only">60% Complete</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingMovementDB"  style="background-color:#f0ad4e;">
          <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#MovementDB" aria-expanded="false" aria-controls="collapseTwo">
            5. Upload movement file into database
            </a>
          </h4>
        </div>
        <div id="MovementDB" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingMovementDB">
          <div class="panel-body">
            <button class="btn btn-primary hidden" id="MovementDBBtn" type="submit">Insert into db</button>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingMovementChecks" style="background-color:#f0ad4e;">
          <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#MovementChecker" aria-expanded="false" aria-controls="collapseThree">
            6. Analyse the movement data
            </a>
          </h4>
        </div>
        <div id="MovementChecker" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingMovementChecks">
          <div class="panel-body">
            <button class="btn btn-primary hidden" id="MovementCheckBtn" type="submit">Check if the data is ok</button>
          <div id="MovementResponse"></div>
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingAnalyse" style="background-color:#f0ad4e;">
          <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#Analyse" aria-expanded="false" aria-controls="collapseThree">
            7. Confirm the daily movement
            </a>
          </h4>
        </div>
        <div id="Analyse" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingAnalyse">
          <div class="panel-body">
            <button class="btn btn-primary hidden" id="AnalyseBtn" type="submit">Analyse the movement</button>
          <div id="AnalyseResponse"></div>
          </div>
        </div>
      </div>
    </div>
    <?php }else{ ?>
      <div class="alert alert-warning" role="alert">The movement of today has already been imported</div>
    <?php } ?>
  </div>
  <div class="col-md-4">
    <div id="movementlog" style="background-color:#ccc; overflow:auto; height:500px;">
    
    <?php
    $query = "select * FROM MOVEMENT_LOG ORDER BY INSERTDATE DESC";
    //echo $query;
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
    if (!$stmt){
      die_silently($conn_Infobase, $error_str);
      exit;
    }else{
      OCIFreeStatement($stmt);
    }
    foreach ($res['INSERTDATE'] as $key=>$attrib_id){
      if ($res['ACTION'][$key]=="OK"){
        echo $res['INSERTDATE'][$key].": OK<br>";
      }else{
        echo $res['INSERTDATE'][$key].": <a id='movReason".$key."' class='history'>".$res['ACTION'][$key]."</a><div class='movReason".$key."_data' style='display:none;border:\'1px\'>".$res['REASON'][$key]."</div><br>";
      }
      
    }
    ?>
    </div>

  </div>
</div>

<script>
(function() {  

  $('#bar').data('valuenow','0');

  $('#InventoryUploadForm').submit(function(e) { 
      e.preventDefault();
      $("#bar").css('width','0%');
      $(this).ajaxSubmit({
        beforeSend: function() {
          $("#bar").css('width','0%');
        },
        uploadProgress: function(event, position, total, percentComplete) {
          $("#bar").css('width',percentComplete+'%');
        },
        complete: function(xhr) {
          //status.html(xhr.responseText);
          var response = $.parseJSON(xhr.responseText);
          Messenger().post({
            message: response.msg,
            type: response.msgtype,
            showCloseButton: true
          });
          if (response.msgtype=='info'){
             $('#InventoryDB').collapse('show');
             $('#InventoryFileUpload').collapse('hide');
             $('#InventoryDBBtn').removeClass('hidden');
             $('#UplaodFileBtn').addClass('hidden');
             $('#headingFileUpload').css('background-color','#5cb85c');
          }else{
            $('#headingFileUpload').css('background-color','#d9534f');
          }
        }     
      });
      return false; 
  });

  $("#InventoryDBBtn").click(function(e) {
    e.preventDefault();
    $('#InventoryDBBtn').addClass('hidden');
    $.ajax({
        type: "POST",
        data: { filetype:'inventory'},
        dataType:  'json',
        url: 'scripts/inventory/db_upload.php',
        success : function(response){
          $('#spinner').spin(false);

          Messenger().post({
            message: response.msg,
            type: response.msgtype,
            showCloseButton: true
          });
          if (response.msgtype=='info'){
            
            $('#InventoryCheckBtn').removeClass('hidden');
            $('#headingInventoryDB').css('background-color','#5cb85c');
            $('#InventoryDB').collapse('hide');
            $('#InventoryChecker').collapse('show');   
          }else{
            $('#headingInventoryDB').css('background-color','#d9534f');
            $('#UplaodFileBtn').removeClass('hidden');
          }
        },
        beforeSend: function ( xhr ) {
          $('#spinner').spin();
        }
      });
  });

  $("#InventoryCheckBtn").click(function(e) {
    e.preventDefault();
    $('#InventoryResponse').html('');
    $.ajax({
        type: "POST",
        data: { filetype:'inventory'},
        dataType:  'json',
        url: 'scripts/inventory/data_analyser.php',
        success : function(response){
          $('#spinner').spin(false);

          Messenger().post({
            message: response.msg,
            type: response.msgtype,
            showCloseButton: true
          });
           $('#InventoryCheckBtn').addClass('hidden');
          if (response.msgtype=='info'){
            $('#UploadMovementBtn').removeClass('hidden');
            $('#headingInventoryChecks').css('background-color','#5cb85c'); 
            $('#InventoryChecker').collapse('hide');
            $('#MovementFileUpload').collapse('show'); 
            $('#InventoryMakeMinusOneBtn').removeClass('hidden');
          }else{
            $('#headingInventoryChecks').css('background-color','#d9534f');
            $('#InventoryResponse').html(response.output);
            $('#UplaodFileBtn').removeClass('hidden');
            $('#InventoryFileUpload').collapse('show'); 
            $("#barMovement").css('width','0%');
          }
        },
        beforeSend: function ( xhr ) {
          $('#spinner').spin();
        }
      });
  });

  $("#InventoryMakeMinusOneBtn").click(function(e) {
    e.preventDefault();
    $('#UploadMovementBtn').addClass('hidden');
    $.ajax({
        type: "POST",
        data: { filetype:'chipotage'},
        dataType:  'json',
        url: 'scripts/inventory/data_analyser.php',
        success : function(response){
          $('#spinner').spin(false);

          Messenger().post({
            message: response.msg,
            type: response.msgtype,
            showCloseButton: true
          });
          $('#InventoryMakeMinusOneBtn').addClass('hidden');
        },
        beforeSend: function ( xhr ) {
          $('#spinner').spin();
        }
      });
  });

  

  $('#MovementUploadForm').submit(function(e) { 
      e.preventDefault();
      $("#barMovement").css('width','0%');
      $(this).ajaxSubmit( {
        beforeSend: function() {
          $("#barMovement").css('width','0%');
        },
        uploadProgress: function(event, position, total, percentComplete) {
          $("#barMovement").css('width',percentComplete+'%');
        },
        complete: function(xhr) {
          //status.html(xhr.responseText);
          var response = $.parseJSON(xhr.responseText);
          Messenger().post({
            message: response.msg,
            type: response.msgtype,
            showCloseButton: true
          });
          if (response.msgtype=='info'){
            $('#UploadMovementBtn').addClass('hidden');
            $('#MovementDBBtn').removeClass('hidden');
            $('#MovementDB').collapse('show');
            $('#MovementFileUpload').collapse('hide');

            $('#headingMovementFileUpload').css('background-color','#5cb85c');
          }else{
            $('#headingMovementFileUpload').css('background-color','#d9534f');
          }
        }     
      });
      return false; 
  });

  $("#MovementDBBtn").click(function(e) {
    e.preventDefault();
    $('#MovementDBBtn').addClass('hidden');
    $.ajax({
        type: "POST",
        data: { filetype:'movement'},
        dataType:  'json',
        url: 'scripts/inventory/db_upload.php',
        success : function(response){
          $('#spinner').spin(false);

          Messenger().post({
            message: response.msg,
            type: response.msgtype,
            showCloseButton: true
          });
          if (response.msgtype=='info'){
            
            $('#MovementCheckBtn').removeClass('hidden');
            $('#headingMovementDB').css('background-color','#5cb85c');
            $('#MovementDB').collapse('hide');
            $('#MovementChecker').collapse('show');   
          }else{
            $('#headingMovementDB').css('background-color','#d9534f');
            $('#UploadMovementBtn').removeClass('hidden');
          }
        },
        beforeSend: function ( xhr ) {
          $('#spinner').spin();
        }
      });
  });

  $("#MovementCheckBtn").click(function(e) {
    e.preventDefault();
    $('#MovementResponse').html('');
    $.ajax({
        type: "POST",
        data: { filetype:'movement'},
        dataType:  'json',
        url: 'scripts/inventory/data_analyser.php',
        success : function(response){
          $('#spinner').spin(false);

          Messenger().post({
            message: response.msg,
            type: response.msgtype,
            showCloseButton: true
          });
           $('#MovementCheckBtn').addClass('hidden');
          if (response.msgtype=='info'){
            $('#headingMovementChecks').css('background-color','#5cb85c'); 
            $('#MovementChecker').collapse('hide');
            $('#Analyse').collapse('show');
            $('#AnalyseBtn').removeClass('hidden');
          }else{
            $('#headingMovementChecks').css('background-color','#d9534f');
            $('#MovementResponse').html(response.output);
            $('#UploadMovementBtn').removeClass('hidden');
            $('#MovementFileUpload').collapse('show'); 
            $("#barMovement").css('width','0%');
          }
        },
        beforeSend: function ( xhr ) {
          $('#spinner').spin();
        }
      });
  });

  $("#AnalyseBtn").click(function(e) {
    e.preventDefault();
    $('#AnalyseResponse').html('');
    $.ajax({
        type: "POST",
        data: { filetype:'invmov'},
        dataType:  'json',
        url: 'scripts/inventory/data_analyser.php',
        success : function(response){
          $('#spinner').spin(false);

          Messenger().post({
            message: response.msg,
            type: response.msgtype,
            showCloseButton: true
          });
          $('#AnalyseBtn').addClass('hidden');
          if (response.msgtype=='info'){
            $('#headingAnalyse').css('background-color','#5cb85c'); 
          }else{
            $('#headingAnalyse').css('background-color','#d9534f');
            $('#AnalyseResponse').html(response.output);
            $('#UploadMovementBtn').removeClass('hidden');
            $('#MovementFileUpload').collapse('show'); 
          }
        },
        beforeSend: function ( xhr ) {
          $('#spinner').spin();
        }
      });
  });
  
})();       
</script>
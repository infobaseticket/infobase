<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


$query="SELECT COLUMN_NAME FROM USER_TAB_COLUMNS WHERE table_name = 'MASTER_REPORT'";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
     OCIFreeStatement($stmt);
    foreach ($res1['COLUMN_NAME'] as $key=>$colname) {
      $taglist.='"'.$colname.'",';
  }
}
?>


<script type="text/javascript">
$(document).ready( function(){

  $("#tokens").select2({
      tags:[<?=$taglist?>],
      createSearchChoice: function() { return null; }
  });

  function after_tracking_save(response){ 
      if (response.responsetype === "info") {
        $('.top-right').notify({
          message: { text: response.responsedata},
          type: 'info'
        }).show();
        Messenger().post({
          message: response.responsedata,
          showCloseButton: true
        });

      }    
    }
    var options = {   
        success:  after_tracking_save,
      dataType:  'json',
    };  
  
    $("#tagsForm").click(function( e ){
        $('#tagsForm').ajaxSubmit(options);
        return false;
    }); 

  $.fn.fixedcols = function(tableid,cols) {

    var $table = $('#'+tableid);
    var tablePos = $table.position();
      //Make a clone of our table
      var $fixedColumn = $table.clone().addClass('fixed-column').attr('id','clone_'+tableid);
     
    $fixedColumn.find('col').not('col:nth-child(-n+'+cols+')').remove();
    $fixedColumn.find('th').not('th:nth-child(-n+'+cols+')').remove();
    $fixedColumn.find('td').not('td:nth-child(-n+'+cols+')').remove();

      //Match the height of the rows to that of the original table's
      $fixedColumn.find('tr').each(function (i, elem) {
          $(this).height($table.find('tr:eq(' + i + ')').height());
      });

      // Set positioning so that cloned table overlays
          // first column of original table
          $fixedColumn.css({
              'left': tablePos.left,
              'top': tablePos.top,
              'position': 'absolute',
              'display': 'inline-block',
              'width': 'auto',
              'background':'#ccc',
              'border-right': '3px solid #ddd'
          });
          //fixedCol.find('th,td').eq(0).css('width',fixedWidthCol1+'px');
          $($fixedColumn).insertBefore('#'+tableid);
  };

});
</script>


<form action="scripts/tracking/tracking_actions.php" method="post" id="tagsForm">
<input type="hidden" name="action" value="test">
<input type="text" id="tokens" name="SiteTags" value="<?=$tags?>" class="select2-offscreen form-control">
 <button class="btn btn-primary" id="addInfo">Add info</button>
</form>
<hr>

<table id="table"  data-height="399" data-show-refresh="true" data-show-toggle="true"  data-show-export="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1">
    <thead>
    <tr>
        <th data-field="id" data-align="right" data-sortable="true">Item ID</th>
        <th data-field="name" data-align="center" data-sortable="true">Item Name</th>
        <th data-field="price" data-align="left" data-sortable="true">Item Price1</th>
        <th data-field="price2" data-align="left" data-sortable="true">Item Price2</th>
        <th data-field="price3" data-align="left" data-sortable="true">Item Price3</th>
        <th data-field="price4" data-align="left" data-sortable="true">Item Price4</th>
        <th data-field="price5" data-align="left" data-sortable="true">Item Price5</th>
        <th data-field="price6" data-align="left" data-sortable="true">Item Price6</th>
        <th data-field="price7" data-align="left" data-sortable="true">Item Price7</th>
        <th data-field="price8" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price9" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price10" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price11" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price12" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price13" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price14" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price15" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price16" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price17" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price2" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price2" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price2" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price2" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price2" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price2" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price2" data-align="left" data-sortable="true">Item Price</th>
        <th data-field="price2" data-align="left" data-sortable="true">Item Price</th>

    </tr>
    </thead>
</table>


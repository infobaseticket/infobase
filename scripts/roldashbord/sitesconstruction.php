<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other,Base_RF","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$query = "SELECT DISTINCT(N1_SITETYPE) FROM MASTER_REPORT WHERE N1_PROCESS='CONSTRUCTION' ORDER BY N1_SITETYPE";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}
?>

<form class="form-inline">
  <div class="form-group">
    <select class="form-control" name="sitetype" id="sitetype">
    <option value="">Select site type</option>
    <?php
    for ($i = 0; $i < count($res1['N1_SITETYPE']); $i++){
        echo "<option>".$res1['N1_SITETYPE'][$i]."</option>";
    }
    ?>
    </select>
  </div>
  <div class="form-group">
    <select class="form-control" name="partner" id="partner">
        <option vlaue="">Select partner</option>
        <option>BENCHMARK</option>
        <option>TECHM</option>
    </select>
  </div>
  <button type="submit" class="btn btn-default" id="filterBtn">Filter</button>
</form>
<hr>
<div id="graph2" style="min-height: 500px;width: 100%"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...</div>

<script type="text/javascript">
$( document ).ready(function() {

    jQuery.ajax({
        type: 'POST',
        url: 'scripts/roldashbord/sitescon_g1.php',
        success: function(data) {
            $('#graph2').html(data);
        }
    }); 

    $("#filterBtn").on("click",function( e ){
        e.preventDefault();
        var formData = {
            'sitetype'  : $('#sitetype option:selected').val(),
            'partner'   : $('#partner option:selected').val()
        };

        $('#graph2').html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
        
        jQuery.ajax({
            type: 'POST',
            url: 'scripts/roldashbord/sitescon_g1.php',
            data: formData,
            success: function(data) {
                $('#graph2').html(data);
            }
        });
    }); 
});
</script>




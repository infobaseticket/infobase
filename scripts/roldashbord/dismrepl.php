<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other,Base_RF","");
?>

<div id="graph2" style="min-height: 500px;width: 100%"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...</div>
<?php
$curYear=date('Y');
for($year=1999;$year<=$curYear+1;$year++){ ?>
<button class="btn-default yearselect" data-year='<?=$year?>'><?=$year?></button>
<?php } ?>
<button class="btn-default yearselect active" data-year=''>CURRENT</button>

<hr>
<div id="dismrepl_table"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...</div>
<hr>
<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
 Info over this report
</button>
<div class="collapse" id="collapseExample">
  <div class="well">
    <ul>
    <li>A200 ACTUAL: M/S 8.0 - Cut over compl. from temp. to perm.
        <ul>
            <li>Cut-over from To-be Dismantled Site to the REPL Site that is ready (NOTE: Only applicable in case a REPL Site is ready before the To-be Dismantled Site has to be dismantled)</li>
            <li>DATE = date when the cut-over was done to the REPL Site (i.e. at least Integrated in OSS). TOGGLE by TechM RANS (Pascal Do)</li>
            <li>NOTE: in case NA (i.e. REPL Site not ready in time or no REPL issued, dummy date to be toggled (01/01/1990) as proof that it is NA)</li>
        </ul>
    </li>
    <li>A250 ACTUAL: M/S 8.5 - Reconfig. compl.
        <ul>
            <li> Always applicable</li>
            <li>DATE = date when the To-be Dismantled Site was removed from OSS. TOGGLE by TechM RANS (Pascal Do)</li>
            <li>When this is toggled it triggers change of Site Status from IS to DM + TECHM OPS to set Site STATUS to DISM (from OPER)</li>
        </ul>
    </li>
    <li>A270 ESTIMATE: Dismantling Performed Estimate
        <ul>
            <li>BASE Estates provided due date by when at the latest the Site needs to be completely dismantled. NOTE: to be updated in case date changes (e.g. extension negotiated with Owner) NOTE: Dummy Date: 01/01/2099 in case site @ risk but no concrete date known</li>
        </ul>
    </li>
    <li>A270 ACTUAL: Dismantling Performed
        <ul>
            <li>Everything is removed from the To-be Dismantled Site (Site back in condition as agreed with Owner)</li>
            <li>DATE = date when everything is removed. TOGGLE by: RO Partner</li>
        </ul>
    </li>
    <li>A275 ACTUAL: M/S 8.6 - Dismantling Completed
        <ul>
            <li>All required equipment is returned to the Warehouse (MS A309 or U309 is toggled by TechM Logistics -> to clarify)</li>
            <li>SOP is signed by Owner (MS U190 (DISM UPG) is toggled by RO Partner)</li>
            <li>Proof of Dismantling is available on Shared Drive (Responsibility RO Partner)</li>
            <li>When this is toggled it Triggers BASE PM to give PAC and FAC (same time)</li>
        </ul>
    </li>
    </ul>
  </div>
</div>
<br><br>
<script type="text/javascript">
$( document ).ready(function() {
    /*
    jQuery.ajax({
        type: 'POST',
        url: 'scripts/roldashbord/dismrepl_g1.php',
        success: function(data) {
            $('#graph1').html(data);
        }
    }); 
*/

    jQuery.ajax({
        type: 'POST',
        url: 'scripts/roldashbord/dismrepl_table.php',
        success: function(data) {
            $('#dismrepl_table').html(data);
        }
    }); 
    jQuery.ajax({
        type: 'POST',
        url: 'scripts/roldashbord/dismrepl_g2.php',
        success: function(data) {
            $('#graph2').html(data);
        }
    }); 

    $(".yearselect").on("click",function( e ){
        $('#graph2').html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
        $('#spinner').spin();
        $(this).addClass('active').siblings().removeClass('active');
        var year=$(this).data('year');
        jQuery.ajax({
            type: 'POST',
            url: 'scripts/roldashbord/dismrepl_g2.php',
            data: { year:year},
            success: function(data) {
                $('#graph2').html(data);
                $('#spinner').spin(false);
            }
        });

        $('#dismrepl_table').html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
        jQuery.ajax({
        type: 'POST',
        url: 'scripts/roldashbord/dismrepl_table.php',
        data: { year:year},
        success: function(data) {
            $('#dismrepl_table').html(data);
        }
    }); 
    });
});
</script>




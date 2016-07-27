<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
?>
<!--<style type="text/css" src="<?=$config['explorer_url']?>css/dataTables.bootstrap.css"></style>
<script src="<?=$config['explorer_url']?>javascripts/jquery.dataTables.min.js"></script>
<script src="<?=$config['explorer_url']?>javascripts/dataTables.bootstrap.js"></script>
<script src="<?=$config['explorer_url']?>javascripts/dataTables.responsive.min.js"></script>-->
<style type="text/css" src="<?=$config['explorer_url']?>css/datatables.min.css"></style>
<script src="<?=$config['explorer_url']?>javascripts/datatables.min.js"></script>
<?php
  if ($_POST['siteID']){
  	$region=substr($_POST['siteID'],0,2);
    if (strlen($_POST['siteID'])==7){
      $siteid=substr($_POST['siteID'],0,-1);
    }else{
      $siteid=$_POST['siteID'];
    }
  	$dir=$config['ranfolder'].$region."/".$siteid."/_".$_POST['candidate']."/";
  }else{
  	$dir=$config['ranfolder'];
  }

?>
<script type="text/javascript">
$(document).ready( function() {
  $("body").on("click","#clearfiletype",function( e ){
    $("#filetype option:selected").removeAttr("selected");
  });

  /****************************************************************************************
* DOCUMENT MANAGER
*****************************************************************************************/
$("#searchFormFiles").click(function( e ){ 
  $("#filedata").hide('fast');
  $("#filedata").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');  
});
$("#searchbuttonFiles").click(function( e ){
  $("#filedata").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
  $("#rafActionsOverview").hide('fast');  
  $('#filefilter').collapse('toggle');
  var options = { 
    target:  '#filedata',   
    success:    function() { 
      $("#filedata").show('slow');
      $("a.tippy").tooltip();
      $('#filebrowserdata').DataTable({
        "aoColumnDefs": [
                { 'bSortable': false, 'aTargets': [ 0 ] }
             ],
          "scrollX": true
      });
    }  
  };      
    $("#searchFormFiles").ajaxSubmit(options);    
    return false; 
}); 

});
</script>

<?php
if ($_POST['candidate']!=''){
  $expanded1='';
  $expanded2='in';
}else{
  $expanded1='in';
  $expanded2='';
}
?>

<div class="panel-group" id="filebrowserfilter" data-spy="affix" style='max-width:60em'; data-offset-top="50" role="tablist" aria-multiselectable="true" data-spy="affix" data-offset-top="60">
  <div class="panel panel-primary">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
         <a data-toggle="collapse" data-parent="#accordion" href="#filefilter" aria-expanded="true" aria-controls="collapseOne">
          Filter search
          <span class="glyphicon glyphicon-eye-open"></span>
        </a>
      </h4>
    </div>
    <div id="filefilter" class="panel-collapse collapse <?=$expanded1?>" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body" style='margin:5px;'>
        <form action="scripts/filebrowser/file_search.php" method="post" id="searchFormFiles" class="form-search" role="form"> 
          <div class="row">            
            <div class="col-md-8">
                <div class="form-group">
                  <label for="searchfor">Filter filename/path</label>
                  <input type="text" name="searchfor" id="searchfor" class="form-control" placeholder="Search in full filepath+name" value="<?=$siteid?>">
                </div>         
                <div class="form-group">
                  <label for="filetype">Filetype</label>  <button type="button" class="btn btn-default btn-xs" name="clearfiletype" id="clearfiletype">Clear</button>
                  <select multiple id='filetype' class="form-control" name="filetype[]" style="min-height:200px;">
                  <option disabled>---------------------ACQ---------------------</option>
                  <option value="LEASE">Lease Contract (LEASE)</option>
                  <option value="BP">Building Permit (BP)</option>
                  <option value="BPER">Building Permit Exemption (BPER)</option>
                  <option value="BPEX">Building Permit Exemption (BPEX)</option>
                  <option value="BPX">Building Permit Exemption (BPX)</option>
                  <option value="LS">Lease Sketch (LS)</option>
                  <option value="LDS">Lease Data Sheet Signed (LDS)</option>
                  <option value="LMS">Lease Motivation Sheet (LMS)</option>
                  <option value="ADDENDUM">Lease Subcontract Addendum (ADDENDUM)</option>
                  <option value="SLEASE">Lease Subcontract (SLEASE)</option>
                  <option value="LSIGNED">Lease Subcontract LSigned (LSIGNED)</option>
                  <option value="TRR">Technical Review Report (TRR)</option>
                  <option disabled>---------------------CON---------------------</option>
                  <option value="EP">EP Received (EP)</option>
                  <option value="ISSEP">RADIATION File Received (ISSEP)</option>
                  <option value="KOR">Kick-off Report (KOR)</option>
                  <option value="CDWG">Constr. Drawings (CDWG)</option>
                  <option value="STAB">Stability Study (STAB)</option>
                  <option value="SOW">Start of Works letter (SOW)</option>
                  <option value="AB">As Built Drawings (AB)</option>
                  <option value="DWG">As Built DWG (DWG)</option>
                  <option value="PIF">PIF (PIF)</option>
                  <option value="HSP">HSP (HSP)</option>
                  <option value="CJ">CJ (CJ)</option>
                  <option value="AYESC">A/Ysec (AYESC)</option>
                  <option value="EWC">EWC (EWC)</option>
                  <option value="EWCDWG">EWC softcopy (EWCDWG)</option>
                  <option value="C2">C2 All Punches Cleared (C2)</option>
                  <option value="C3">C3 Only C-Punches (C3)</option>
                  <option value="SOPOUT">SOP OUT (SOPOUT)</option>
                  </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                  <label for="rangetype">Date range seletion</label>
                  <select id='rangetype' class="form-control" name="rangetype">
                  <option value='na'>NA</option>
                  <option value='File'>Date in filename</option>
                  <option value='Modif'>Last modif time</option>
                  <option value='Acces'>Last acces time</option>
                  <option value='Hist'>History insert date</option>
                  </select>
                </div>
                 <div class="form-group">
                <div class="input-daterange input-group" id="datepicker">
                    <input type="text" class="input-sm form-control" name="start" />
                    <span class="input-group-addon">to</span>
                    <input type="text" class="input-sm form-control" name="end" />
                </div>
                </div>
                <div class="form-group">
                  <label for="filesize">File bigger than MB</label>
                  <input type="text" name="filesize" id='filesize' class="form-control input-md search-query" placeholder="Files bigger than (MB)">
                </div>
                <div class="form-group">
                  <label for="extension">Extension</label>
                  <select id='extension' class="form-control" name="extension">
                  <option value='All'>All</option>
                  <option value='xls'>Excel</option>
                  <option value='doc'>Word</option>
                  <option value='pdf'>PDF</option>
                  <option value='msg'>MSG</option>
                  <option value='txt'>TXT</option>
                  <option value='dwg'>DWG</option>
                  <option value='img'>images (jpg, tif, gif,bmp)</option>
                  <option value='bm'>bmp</option>
                  <option value='jp'>jpg</option>
                  <option value='gif'>gif</option>
                  <option value='tif'>tif</option>
                  <option value='zip'>zip</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="extension">RAN location</label><br>
                  <label class="checkbox-inline">
                    <input type="checkbox" name="typeRANALU" id="typeRANALU" CHECKED value="ALU"> ALU
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" name="typeRANOLD" id="typeRANOLD" value="OLD"> OLD
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" name="typeRANBENCH" id="typeRANBENCH" CHECKED value="BENCH"> BENCH
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" name="typeRANM4C" id="typeRANM4C" CHECKED value="M4C"> M4C
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" name="typeRANBENCH" id="typeRANBASE" CHECKED value="BASE"> BASE
                  </label>
                  <label class="checkbox-inline">
                    <input type="checkbox" name="typeRANHIST" id="typeRANHIST" value="HIST"> HISTORY
                  </label>
                   <label class="checkbox-inline">
                    <input type="checkbox" name="typeRANLEASE" id="typeRANLEASE" value="LEASE"> LEASE&BP
                  </label>
                </div>
            </div>            
          </div>
          <div class="row"> 
            <div class="col-md-10"><i>Note: HISTORY contains the analasys between RANSCAN &amp; RANSCAN-1.<br>This means that in the HISTORY you can see when files have been added and deleted.</i>
            </div>           
            <div class="col-md-2">
              <button class="btn btn-default" id="searchbuttonFiles" type="submit"><span class="glyphicon glyphicon-search"> Search</span></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div><br><br><br>
<div id="filedata"></div>
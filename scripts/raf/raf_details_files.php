<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
?>
  <table class="table table-bordered table-hover">
  <thead>
    <th>Actions</th>
    <th>Filename</th>
    <th>Extension</th>
    <th>Size</th>
    <th>Location</th>
    <th>Last modification date</th>
  </thead>
  <tbody>
<?php
function format_bytes($bytes) {
   if ($bytes < 1024) return $bytes.' B';
   elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KB';
   elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MB';
   elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GB';
   else return round($bytes / 1099511627776, 2).' TB';
}

if ($_POST['rafid']==""){ ?>
  <div class="alert alert-danger" role="alert">You clicked to fast on FILES. Please wiat untill page is loaded.</div>
<?php
}else{
  $rafdir=$config['raf_folder_abs'].$_POST['rafid']."/";
  if (file_exists($rafdir)) {
    $dir = new DirectoryIterator($rafdir);
    $i=0;
    while($dir->valid()) {
        if(!$dir->isDot()) {
            $file_extension = pathinfo($dir->current(), PATHINFO_EXTENSION);
            if (substr($dir->current(),0,5)!='thumb'){

              $pos = strpos($dir->current(), "BOQ");
              if ($pos !== false){
                if (substr_count($guard_groups, 'Benchmark')==1 && $res2['ACQ_PARTNER'][0]!='BENCHMARK' && $res2['CON_PARTNER'][0]!='BENCHMARK'){
                  $skipfile='yes';
                }else if (substr_count($guard_groups, 'TechM')==1 && $res2['ACQ_PARTNER'][0]!='TECHM' && $res2['CON_PARTNER'][0]!='TECHM'){
                  $skipfile='yes';
                }else{
                  $skipfile='no';
                }
                
              }else{
                $skipfile='no';
              }
              if($skipfile!='yes'){
                echo "<tr id='raffile_".$i."'><td>";
               
                $rafDownLoadLoc=$config['raf_folder_abs'].$_POST['rafid']."/".urlencode($dir->current());
        
                echo '<a class="btn btn-default btn-xs filedownload" target="_new" title="Download file" href="scripts/filebrowser/filedownload.php?file='.$rafDownLoadLoc.'&name='.str_replace(" ", "_", $dir->current()).'"><span class="glyphicon glyphicon-download"></span></a>';
                echo "&nbsp;<span class='glyphicon glyphicon-trash pointer raffile_delete' data-file='".$dir->getPath()."/".$dir->current()."' data-fileid='".$i."'></span>";
                 if ($file_extension=='jpg' or $file_extension=='gif' or $file_extension=='png'){
                  echo "&nbsp;<span class='glyphicon glyphicon-eye-open rafdetailsImg pointer' data-file='".$config['raf_folder_url'].$_POST['rafid']."/".$dir->current()."'></span>";

                }
                echo "</td>";
                echo "<td>".$dir->current()."</td>";
                echo "<td>".$file_extension."</td>";
                echo "<td>".format_bytes($dir->getSize())."</td>";
                echo "<td>".$dir->getPath()."</td>";
                echo "<td>".date("d-m-Y H:i:s",$dir->getMTime())."</td></tr>";

               $i++;
              }

            }
        }
        $dir->next();
    }
  }
  ?>
    </tbody>
    </table>

  <div class="row" id="rafImages" style="display:none;border:1px;">
    <div class="col-md-12 col-sm-12" style="position:absolute;top:0;">
      <p class="text-center"><button type="button" class="crafe rafimgCrafe" aria-hidden="true">&times;</button>
        <span id='rafimg'></span>
      </p>
    </div>
  </div>
  <form role="form" id='rafuploadform' action="scripts/raf/file_upload.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="MAX_FILE_SIZE" value="99000000">
      <input type="hidden" name="rafid" value="<?=$_POST['rafid']?>">

      <div class="form-group">
        <label for="InputFile">File input</label>
        <input type="file" id="InputFile" name="myfile[]" multiple="">
      </div>
      <input type="submit" value="Upload File to Server" class="btn btn-default">
  </form>

  <div class="progress progress-striped" style="width:500px;">
    <div class="progress-bar" id="bar" role="progressbar" aria-valuenow="0" 
    aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
      <span class="sr-only">60% Complete</span>
    </div>
  </div>
  <script>
  (function() {  

   $('#bar').data('valuenow','0');

    $('#rafuploadform').submit(function(e) { 
        e.preventDefault();

        $(this).ajaxSubmit( {
          beforeSend: function() {
            $("#bar").css('width','0%');

          },
          uploadProgress: function(event, position, total, percentComplete) {
            $("#bar").css('width',percentComplete+'%');
          },
          complete: function(xhr) {
            //status.html(xhr.responseText);
            $('#raf_details_files').click();
            Messenger().post({message:xhr.responseText,showCrafeButton:true,hideAfter: 5,hideOnNavigate: true});
          }     
        });
        return false; 
    });
  })();       
  </script>
<?php
}
?>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Partner,Base_RF","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['upgnr']!='NB'){
  $query="SELECT RAFID, TYPE FROM BSDS_RAFV2 WHERE NET1_LINK LIKE '%".$_POST['upgnr']."%'";
}else{
  $query="SELECT RAFID, TYPE FROM BSDS_RAFV2 WHERE NET1_LINK LIKE '%".$_POST['candidate']."%'";
}
$stmtT = parse_exec_fetch($conn_Infobase, $query, $error_str, $resT);
if (!$stmtT) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmtT);
  $amount_of=count($resT['RAFID']);
}



?>
<form action="scripts/general_info/general_info_actions.php" role="form" method="post" id="new_bsds_form<?=$_POST['candidate']?><?=$_POST['upgnr']?>">
<input type="hidden" name="candidate" value="<?=$_POST['candidate']?>">
<input type="hidden" name="upgnr" value="<?=$_POST['upgnr']?>">
<input type="hidden" name="bsdskey" value="<?=$_POST['bsdskey']?>">
  <div class="form-group">
    <label for="RAFID">RAF</label>
    <select name="rafid" class="form-control" id="RAFID">
    <?php
    for ($i=0;$i<$amount_of;$i++){
      echo "<option value='".$resT['RAFID'][$i]."'>".$resT['RAFID'][$i]." ".$resT['TYPE'][$i]."</option>";
    }
    ?>


    </select>  
  </div>
  <?php if($_POST['bsdskey']==''){ ?>
  <div class="form-group">
    <label for="BSDStype">BSDS type</label>
    <select name="BSDS_TYPE" class="form-control" id="BSDStype">
  	<option value="ANT">Antenna change</option>
  	<option value="CAB">CAB upgrade</option>
  	<option value="TRX">TRX upgrade</option>
  	<option value="ROL">Rollout BSDS</option>
  	<option value="ORQ">ORQ BSDS</option>
  	</select>  
  </div>
 
  <div class="form-group">
    <label for="Comments">Comments</label>
    <textarea name="COMMENTS" class="form-control" rows="5"></textarea>
  </div>
  <?php } ?>
</form>

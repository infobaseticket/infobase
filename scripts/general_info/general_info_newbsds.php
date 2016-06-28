<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Partner,Base_RF","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
?>
<form action="scripts/general_info2/general_info_actions.php" role="form" method="post" id="new_bsds_form<?=$_GET['tabid']?>">
<input type="hidden" name="action" value="insert_new_bsds">
<input type="hidden" name="siteID" value="<?=$_POST['siteID']?>">
<input type="hidden" name="candidate" value="<?=$_POST['candidate']?>">
<input type="hidden" name="ADDRESSFK" value="<?=$_POST['ADDRESSFK']?>">
  <div class="form-group">
    <label for="siteID">siteID</label>
    <input type="text" disabled class="form-control" id="siteID" value="<?=$_POST['siteID']?>">
  </div>
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
</form>

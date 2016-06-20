<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
?>
<script language="JavaScript">
$(document).ready(function() {


	$('input.sitelist').typeahead(
	{
	  name: 'accounts',
	 remote: 'scripts/los/ajax/field_list.php?field=sites&q=%QUERY',
	});

	function after_LOS_save(response)  {
		$('#modalspinner').spin(false);
		Messenger().post({message:response.responsedata,type: response.responsetype,showCloseButton:true,hideAfter: 5,hideOnNavigate: true});
	}
	function validateNewLos(formData, jqForm, options){
		var form = jqForm[0];
	    if (form.SITEA.value==="") {
	        alert('You need to provide SITE A!');
	        return false;
	    } else{
			if (form.SITEB.value === "") {
				alert('You need to provide SITE B!');
				return false;
			}else{
				if (form.PRIORITY.value===""){
					alert('You need to select a priority!');
					return false;
				}else{
					if (form.TYPE.value===""){
						alert('You need to select a type!');
						return false;
					}else{
						$('#modalspinner').spin('small');
					}
				}
			}
		}
	}
	var options = {
    	success:  after_LOS_save,
		dataType:  'json',
		beforeSubmit: validateNewLos
	};

	$('#new_los_form').submit(function() {
	    $(this).ajaxSubmit(options);
	    return false;
	});
});
</script>

<?
//$guard_groups="Base_other";
$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

//echo $guard_groups;
if ($_POST['losid'] && $_POST['losid']!="new"){
	$query = "Select * FROM BSDS_LINKINFO WHERE ID = '".$_POST['losid']."'";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$amount_of_LOS=count($res1['SITEA'][0]);
		$SITEA=$res1['SITEA'][0];
		$SITEB=$res1['SITEB'][0];
		$COMMENTSA=$res1['COMMENTSA'][0];
		$COMMENTSB=$res1['COMMENTSB'][0];
		$HEIGHTA=$res1['HEIGHTA'][0];
		$HEIGHTB=$res1['HEIGHTB'][0];
		$PRIORITY=$res1['PRIORITY'][0];
		$TYPE=$res1['TYPE'][0];
	}
	$losid=$_POST['losid'];
}else{
	$losid="";
}

if ($amount_of_LOS>=1){
	$action="Update";
}else{
	$action="Create";
}

if ($PRIORITY==""){
	$PRIORITY_select="Please select";
	$PRIORITY_selectVal= "";
}else{
	$PRIORITY_select=$PRIORITY;
	$PRIORITY_selectVal=$PRIORITY;
}

if ($TYPE==""){
	$TYPE_select="Please select";
	$TYPE_selectVal="";
}else{
	$TYPE_select=$TYPE;
	$TYPE_selectVal=$TYPE;
}
?>
<form action="scripts/los/los_actions.php" method="post" id="new_los_form">
<input type="hidden" name="action" value="insert_new_los">
<input type="hidden" name="losid" value="<?=$_POST['losid']?>">
<table cellpadding="4" cellspacing="5">
<tr>
	<td class="param_title">Priority:</td>
	<td align="left"><select name="PRIORITY"><option selected value="<?=$PRIORITY_selectVal?>"><?=$PRIORITY_select?></option>
	<option>0</option><option>1</option><option>2</option><option>Canceled</option></select></td>
</tr>
<tr>
	<td class="param_title">Type:</td>
	<td align="left"><select name="TYPE"><option selected value="<?=$TYPE_selectVal?>"><?=$TYPE_select?></option>
	<option value="NB">New build</option><option value="ST">Standard</option><option value="RSL">Received Signal Level</option></select></td>
</tr>
<tr>
	<td colspan="2">
	<table width="100%">
	<tr>
		<td class="param_title">SITE A:</td>
		<td>
			<input type="text" value="<?=$SITEA?>" name="SITEA" class="sitelist" size="9">
		</td>
		<td class="param_title">SITE B:</td>
		<td>
			<input type="text" value="<?=$SITEB?>" name="SITEB" class="sitelist" size="9">
		</td>
	</tr>
	<tr>
		<td class="param_title">HEIGHT A:</td>
		<td>
			<input type="text" value="<?=$HEIGHTA?>" name="HEIGHTA" size="9">
		</td>
		<td class="param_title">HEIGHT B:</td>
		<td>
			<input type="text" value="<?=$HEIGHTB?>" name="HEIGHTB" size="9">
		</td>
	</tr>
	<tr>
		<td class="param_title">COMMENTS A:</td>
		<td>
			<textarea name="COMMENTSA" rows="10" cols="30"><?=$COMMENTSA?></textarea>
		</td>
		<td class="param_title">COMMENTS B:</td>
		<td>
			<textarea name="COMMENTSB" rows="10" cols="30"><?=$COMMENTSA?></textarea>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td colspan="2" align="center"><br><input type="submit" class="btn btn-primary" id="yes_newlos" value="<?=$action?>"></td>
</tr>
</table>
</form>

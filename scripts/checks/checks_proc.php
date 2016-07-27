<?
/*
if ($_POST['clear']!='1'){

	$query="SELECT * FROM INFOBASE.BSDS_VALIDATION WHERE ACTIVE='yes' AND TECHNOLOGY LIKE '%".$_POST['band']."%' ORDER BY IDNR";
	//echo $query;
	$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
		exit;
	} else {
		OCIFreeStatement($stmt);
	}

	$ERROR="";


	foreach ($res1['IDNR'] as $key=>$attrib_id) {

		$ERROR=$res1['ERROR'][$key];
		$RULE=$res1['RULE'][$key];
		$IDNR=$res1['IDNR'][$key];
/*
if ($IDNR=="28") {
		echo "(".$IDNR.")".$RULE."<hr>";
		echo "---".$_POST['pl_CABTYPE']."/".$_POST['pl_CONFIG_2']."/".$_POST['pl_CONFIG_3']."/".$_POST['pl_CONFIG_4']."/".$_POST['pl_DXUTYPE2'];
}*

		eval('if ('.$RULE.'){'.
			'$ERROR_MESSAGE.= "$ERROR<br>";'.
			'$ERROR_MESSAGE2.= "($IDNR) <font size==1>$RULE</font><br>$ERROR<hr>";'.
			'}');

	}
}*/

?>
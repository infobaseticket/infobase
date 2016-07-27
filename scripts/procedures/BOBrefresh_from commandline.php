<html>
<head>
<title><?=$_GET['type']?> BOB report refresh</title>
<script language="JavaScript">
    window.opener.location.href = window.opener.location.href;
</script>
</head>
<body>

<?

$output = null;
$check="SP2-0606: Cannot create SPOOL file \"/bigdisk/htdocs/Aircom2/bsds/NETONE_PROD/Temp/temp.dat\"";

if ($_GET['type']=='UPG'){
	 exec('Post_DTB_Interface_Upgrades.sh', $output);
	 $pos = strrpos($output[7], "SP2-0606");
	 if ($pos === true) {
	 		echo "<font color=red size=4>Problems when trying to import the UPG BOB report! Please contact Frederick asap!</font>";
	 }
	 echo "<pre>" . var_export($output, TRUE) . "</pre>\\n";
}else if ($_GET['type']=='NEW'){
	exec('Post_DTB_Interface_NewSites.sh', $output);
	$pos = strrpos($output[7], "SP2-0606");
	 if ($pos === true) {
	 		echo "<font color=red size=4>Problems when trying to import the new BOB report! Please contact Frederick asap!</font>";
	 }
	 echo "<pre>" . var_export($output, TRUE) . "</pre>\\n";
}
?>
</body>
</html>


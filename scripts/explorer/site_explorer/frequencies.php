<?
require_once('../include/config.php');
require_once($config['phpguarddog_path']."/guard.php");
//echo $_SESSION['planning_id'].$_SESSION['upgtype'];
?>
<html>
<head>

<link rel="shortcut icon" href="http://infobase/images/icons/colors.png"></link>
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/CSS/bsds_generalinfo.css" type="text/css"></link>
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/CSS/tabbed_menu_orange.css" type="text/css"></link>
</head>
<body>
<div id="navigation">
<? 
include("../bsds3/scripts/navigation/navigation.php");
?>
</div>
<?
$db = mysql_connect("$mysql_host", "$mysql_user", "$mysql_password");
mysql_select_db("$db_MSCdata",$db);
?>
<table>
<tr>
	<td>
<?
	$query="select DISTINCT(cell) from switch_rxmop_tx  WHERE BSC='".$_GET[BSC]."' AND TG='".$_GET[TG]."' order by cell Asc";
	//echo $query;
	$result_cells=mysql_query($query,$db) or die( mysql_error()."Unable to select table switch_rxmop_tx");
	$conn = OCILogon($user_Infobase,$passwd_Infobase,$host_Infobase)or die("Can't get a database connection");
 	while ($row_cells=mysql_fetch_array($result_cells)) {
	 	$cell=$row_cells[0];
		$carriers_asset[]=$cell;
		$carriers_switch[]=$cell;
		$sql =  " select CARRIERNUMBER from ch_type_car_state where IDNAME like '%$cell%' ORDER BY IDNAME,CARRIERNUMBER";
		//echo "$sql <br>";
		$stmt = OCIParse($conn, $sql);
		OCIExecute($stmt);
		while (ocifetch($stmt)) {
			//echo "$cell: ".ociresult($stmt, "CARRIERNUMBER")."<br>";
			$carriers_asset[]=ociresult($stmt, "CARRIERNUMBER");
		}

		$query="select BCCHNO, CGI from switch_rldep_all  WHERE cell='$cell' ORDER BY BCCHNO";
		//echo "$query <br>";	
		$result81=mysql_query($query,$db) or die( "Unable to select table data_switch_bcchno");		
		$row81 = mysql_fetch_array($result81);  
		$BCCHNO=$row81[0];
		$CGI=$row81[1];
		//echo "BBCH $cell: $BCCHNO <br>";
		$carriers_switch[]=$BCCHNO;
		 
		$query="select DCHNO, HSN from switch_rlcfp_all  WHERE cell='$cell' order by DCHNO";
		//echo "$query <br>";	
		$result80=mysql_query($query,$db) or die( "Unable to select table data_switch_dchno");		
		while ($row80 = mysql_fetch_array($result80)){
		   if ($row81[0]!=$row80[0]){
			  $DCHNO=$row80[0];
			  //echo "DCHN $cell: $DCHNO <br>";
			  $carriers_switch[]=$DCHNO;
			  $DCHNO_t=$DCHNO.",".$DCHNO_t;
		   }
		   $HSN=$row80[1];				   
		}
		$DCHNO_t=substr($DCHNO_t,0,-1);
		$HSN_t=$HSN_t."-".$HSN;
				
							
		$result = array_diff($carriers_asset, $carriers_switch);
		foreach ( $result as $key => $missing_freqs ) {
       		//echo "Key: $key, Value: $value\n";
			$missing_freqs_t=$missing_freqs.",".$missing_freqs_t;
	    } 
		$missing_freqs_t=substr($missing_freqs_t,0,-1);
				
		$query="select count(MO) from switch_rxmop_tx WHERE cell='$cell' order by cell Asc";
		//echo "$query <br>";
		$result_config=mysql_query($query,$db) or die( mysql_error());
		$row_config = mysql_fetch_array($result_config);
		$config_t=$config_t."+".$row_config[0];
		
		$query="select MO from switch_rxmop_tx WHERE cell='$cell' order by cell Asc";
		//echo "$query <br>";
		$result=mysql_query($query,$db) or die (mysql_error());
		while ($row = mysql_fetch_array($result)){
			$query2="select signalling from switch_rxcdp_tg  WHERE BSC='$BSC' AND TG='$TG' AND config='$row[0]' ";
			$result_sign=mysql_query($query2,$db) or die (mysql_error());
			$row_sign = mysql_fetch_array($result_sign);
			$TRU_t=$TRU_t.$row[0]." ".$row_sign[0]."<br>";
		}		
				 
		$switch= $switch."<font size=2>$cell: </font><font color=orange size=2>($BCCHNO)</font> <font size=2>$DCHNO_t";
		if ($missing_freqs_t!=""){
			$switch= $switch.",</font><font color=red size=2><b>$missing_freqs_t</b></font>";
		}
				$switch= $switch."<br><font size=2>$TRU_t</font><hr>";

		//print_r($result)."<br><br>";
		unset($carriers_asset);
		unset($carriers_switch);
		unset($result);
		$missing_freqs_t="";
		$TRU_t="";
		$BCCHNO="";
		$DCHNO_t="";
	}
			
	$switch=substr("$switch", 0, -4);  
	OCILogOff($conn); 
			
	$config_t=substr($config_t,1);
	//echo $config_t."<br>";
			
	$CGI=substr("$CGI", 0, -1);  
	$CGI=$CGI."x";
	//echo $CGI."<br>";
			
	$HSN_t=substr($HSN_t,1);
	//echo $HSN_t."<br>";

	echo "$switch";
?>
	</td>
</tr>
</table>
</body>
</html>
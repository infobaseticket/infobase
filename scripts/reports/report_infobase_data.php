<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Alcatel,Base_delivery,Administrators","");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
include("../procedures/cur_plan_procedures.php");
//error_reporting(E_ALL);

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


if ($_POST[view]!="BSDS"){ ?>
	<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-ui/datepicker/ui.datepicker.css" type="text/css"></link>
	<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-ui/datepicker/ui.datepicker.js"></script>
	<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-ui/jquery.tablesorter.min.js"></script>
	<link rel="stylesheet" href="scripts/reports/reports.css" type="text/css"></link>
	<link rel="stylesheet" href="scripts/general_info/bsds_generalinfo.css" type="text/css"></link>


	<script language="JavaScript">
	$(document).ready( function() {
		
		$('#daterange').datepicker({rangeSelect: true,dateFormat: 'dd/mm/yy'});
		
		$("#content").css({'overflow-x' : 'scroll', 'overflow-y' : 'hidden'});
		
		var options = { 
	    target:  '#reportOutput',   
	    success:    function() { $("#loading").hide();
			$("#reportOutput").show('fast');}  
		};	
		// attach handler to form's submit event 
		$('#Report1Form').submit(function() { 
			$("#loading").show('fast');
			$("#reportOutput").hide('fast');			
		    // submit the form 
		    $(this).ajaxSubmit(options); 
		    // return false to prevent normal br
		    return false; 
		    
		});	
	});
	</script>


	<br>
	<form action="scripts/reports/report_infobase_data.php" name="fm" method="post" id="Report1Form">
	<table cellpadding="0" cellspacing="0" align="center">
	<tr>
	<td>
	BOB REFRESH DATE:
	<input type="text" name="bob_refresh" id="daterange" value="<?=$_POST['bob_refresh']?>">
	<input type="checkbox" name="csv" value="yes"> CSV-file<br>
	Teamleader status<select name="teamleader"><option><?=$teamleader_selected?></option><option>Accepted</option><option>Pending</option><option>Declined</option></select>
	BSDS status<select name="status"><option selected><?=$status_selected?></option><option>SITE FUNDED</option><option>BSDS FUNDED</option><option>BSDS AS BUILD</option><option>Defunded</option></select>
	BSDS type<select name="type"><option selected><?=$type_selected?></option><option>Newbuilds</option><option>Upgrades</option></select>
	</td>
	<tr>
	<td>
	<input type="submit" value="SEARCH">
	</td></tr></table>
	<input type="hidden" name="view" value="BSDS">
	</form>
	<hr>
	
	<div id="reportOutput"></div>
<?
}

if ($_POST[view]=="BSDS"){
	
		?>
		<script language="JavaScript">
		$(document).ready( function() {		
			
			$("#reporttable tr").hover(
			   function()
			   {
			    $(this).addClass("highlight");
			   },
			   function()
			   {
			    $(this).removeClass("highlight");
			   }
			) 
			
			//SORTING
			$("table.report").tablesorter(); 
			
			//assign the sortStart event 
			$("#overlay").hide();
		    $("table.report").bind("sortStart",function() { 
		        $("#overlay").show(); 
		    }).bind("sortEnd",function() { 
		        $("#overlay").hide(); 
			});
		});
	</script>
	<?
	if ($TEAML_APPROVED_v==""){
		$TEAML_APPROVED_v="na";
	}
	if ($SITE_CONFLICT_v==""){
		$SITE_CONFLICT_v="na";
	}
	if ($BSDS_TYPE_v==""){
		$BSDS_TYPE_v="All";
	}

	$z=0;
	
	$query1 = "Select * FROM INFOBASE.BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY IS NOT NULL";
	if ($_POST['bob_refresh']!=""){
		$date_split=explode(' - ',$_POST[bob_refresh]);
		$query1 .= " AND (TO_DATE('".$date_split[0]."','DD/MM/YYYY') <= BSDS_BOB_REFRESH AND TO_DATE('".$date_split[1]."','DD/MM/YYYY')+1 >= BSDS_BOB_REFRESH)";
	}
	if ($_POST['teamleader']!=""){
		$query1 .= " AND TEAM_STATUS='".$_POST['teamleader']."'";
	}
	
	if ($_POST['status']!=""){
		if ($_POST['status']=="Defunded"){
			$query1 .= " AND (STATUS='SITE FUNDED => DEFUNDED TO OLD DATE' OR STATUS='BSDS FUNDED => DEFUNDED TO OLD DATE' OR STATUS='BSDS AS BUILD => DEFUNDED TO OLD DATE')";			
		}else{
			$query1 .= " AND STATUS='".$_POST['status']."'";
		}
		
	}
	$query1 .= " ORDER BY SITEID";;
	//echo "<br><br>".$query1;

	$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
	if (!$stmt) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt);
		$Count1=count($res1['BSDSKEY']);
		//echo $Count1;
		for ($i = 0; $i < $Count1; $i++) {
			
			$techno=explode(':',$res1['TECHNOLOGY'][$i]);
			
			if ($_POST['type']=="Upgrades" || $_POST['type']==""){
				$query2="SELECT SITE_ID, U353, U571, U305, BAND, WOR_NAME, U571_ESTIM, U307, U307_ESTIM, WOR_UDK, CON, BAND
				FROM infobase.VW_NET1_UPGRADES WHERE SITE_ID LIKE '%".$res1['SITEID'][$i]."%' AND BAND LIKE '%".$techno[0]."%'";			
				if ($filter=="yes"){
					$query2.=" AND LOWER(CON) LIKE '%".strtolower($userdetails['employer'])."%'";
				}
				//echo $query2."<br>";
				
				
				$stmt = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);			
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				 	exit;
				} else {
					OCIFreeStatement($stmt);
					$Count2=count($res2['SITE_ID']);
					for ($j = 0; $j < $Count2; $j++) {
						if ($res2['U353'][$j]!=""){
						$data[$z]['SITEID']=$res1['SITEID'][$i];
						$data[$z]['BSDSKEY']=$res1['BSDSKEY'][$i];
						$data[$z]['BSDS_BOB_REFRESH']=$res1['BSDS_BOB_REFRESH'][$i];
						$data[$z]['TECHNOLOGY']=$res1['TECHNOLOGY'][$i];
						$data[$z]['TEAM_STATUS']=$res1['TEAM_STATUS'][$i];
						$data[$z]['TEAM_DATE']=$res1['TEAM_DATE'][$i];
						$data[$z]['STATUS']=$res1['STATUS'][$i];
						$data[$z]['NET1_DATE']=$res1['NET1_DATE'][$i];
						
						$data[$z]['BAND']=$res2['BAND'][$j];
						$data[$z]['TYPE']="UPGRADE";
						$data[$z]['WOR_UDK']=$res2['WOR_UDK'][$j];
						$data[$z]['WOR_NAME']=$res2['WOR_NAME'][$j];
						$data[$z]['CON']=$res2['CON'][$j];
						$data[$z]['SITE_FUNDED']=$res2['U353'][$j];
						$data[$z]['BUILD']=$res2['U571'][$j];
						$data[$z]['BSDS_FUNDED']=$res2['U305'][$j];
						$data[$z]['BUILD_ESTIM']=$res2['U571_ESTIM'][$j];
						$data[$z]['307']=$res2['U307'][$j];
						$data[$z]['307_ESTIM']=$res2['U307_ESTIM'][$j];
						$z++;
						}
					}
				}
			}
			
			if ($_POST['type']=="Newbuilds" || $_POST['type']==""){

				$query2="SELECT SITENAME, A353, A71, A305, BAND, A71_ESTIM, A307, A307_ESTIM, PARTIES, BAND
				FROM infobase.VW_NET1_NEWBUILDS WHERE SITENAME LIKE '%".$res1['SITEID'][$i]."%' AND BAND LIKE '%".$techno[0]."%'";
				if ($filter=="yes"){
					$query2.=" AND LOWER(PARTIES) LIKE '%".strtolower($userdetails['employer'])."%'";
				}
				//echo $query2."<br>";
				$stmt = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
				if (!$stmt) {
					die_silently($conn_Infobase, $error_str);
				 	exit;
				} else {
					OCIFreeStatement($stmt);
					$Count2=count($res2['SITENAME']);
					//echo $data[$z]['SITEID'].":".$Count2;
					for ($j = 0; $j < $Count2; $j++) {
						if ($res2['A353'][$j]!=""){
						$data[$z]['SITEID']=$res1['SITEID'][$i];
						
						$data[$z]['BSDSKEY']=$res1['BSDSKEY'][$i];
						$data[$z]['BSDS_BOB_REFRESH']=$res1['BSDS_BOB_REFRESH'][$i];
						$data[$z]['TECHNOLOGY']=$res1['TECHNOLOGY'][$i];
						$data[$z]['TEAM_STATUS']=$res1['TEAM_STATUS'][$i];
						$data[$z]['TEAM_DATE']=$res1['TEAM_DATE'][$i];
						$data[$z]['STATUS']=$res1['STATUS'][$i];
						$data[$z]['NET1_DATE']=$res1['NET1_DATE'][$i];
				
						$data[$z]['BAND']=$res2['BAND'][$j];
						$data[$z]['TYPE']="NEWBUILD";
						$data[$z]['CON']=$res2['PARTIES'][$j];
						$data[$z]['SITE_FUNDED']=$res2['A353'][$j];
						$data[$z]['BUILD']=$res2['A71'][$j];
						$data[$z]['BUILD_ESTIM']=$res2['A71_ESTIM'][$j];
						$data[$z]['BSDS_FUNDED']=$res2['A305'][$j];					
						$data[$z]['307']=$res2['A307'][$j];
						$data[$z]['307_ESTIM']=$res2['A307_ESTIM'][$j];
						//echo "===>".$data[$z]['SITEID']."--".$data[$z]['STATUS']." ($Count2)<br>";
						$z++;
						}
					}
				}
			}

		}
	}
}
//echo "<pre>".print_r($data,true)."</pre>";

if ($_POST['teamleader']!=''){
	$teamleader_selected=$_POST['teamleader'];
}else{
	$teamleader_selected="na";
}
if ($_POST['status']!=''){
	$status_selected=$_POST['status'];
}else{
	$status_selected="na";
}
if ($_POST['type']!=''){
	$type_selected=$_POST['type'];
}else{
	$type_selected="na";
}

if ($_POST['csv']=="yes"){ ?>

	<?
	echo "<center><textarea rows=15 cols=130>SITE ID,BSDSKEY,BOB_REFRESH,TECHNOLOGY,STATUS,TEAMLEADER_STATUS,TEAMLEADER DATE,NET1_DATE,TYPE,BAND,WOR_UDK,CON,WOR_NAME,U353 (SITE FUNDED),U353 (BSDS FUNDED),U571 (BSDS AS BUILD),U571_ESTIM,U307_ESTIM\n";

	for ($i = 0; $i <= $z; $i++) {
		
		echo $data[$i]['SITEID'].","
		.$data[$i]['BSDSKEY'].","
		.$data[$i]['BSDS_BOB_REFRESH'].","
		.$data[$i]['TECHNOLOGY'].","
		.$data[$i]['STATUS'].","		
		.$data[$i]['TEAM_STATUS'].","
		.$data[$i]['TEAM_DATE'].","
		.$data[$i]['NET1_DATE'].","
		.$data[$i]['TYPE'].","
		.$data[$i]['BAND'].","
		.$data[$i]['WOR_UDK'].","
		.$data[$i]['CON'].","
		.$data[$i]['WOR_NAME'].","
		.$data[$i]['SITE_FUNDED'].","
		.$data[$i]['BSDS_FUNDED'].","
		.$data[$i]['BUILD'].","
		.$data[$i]['BUILD_ESTIM'].","
		.$data[$i]['307_ESTIM']."\n";

	}
	echo "</textarea></center>";
} elseif ($_POST[view]=="BSDS"){ 
	?>
	<font size=2><i>Tip!</i> Sort multiple columns simultaneously by holding down the shift key and clicking a second, third or even fourth column header!</font>&nbsp;&nbsp;&nbsp;&nbsp;
	<div>
	<table id="reporttable" class="report">
	<thead>
	<tr>
		<th>SITE ID</th>
		<th>BSDSKEY</th>
		<th>BOB REFRESH</th>
		<th>TECHNOLOGY</th>
		<th>STATUS</th>
		<th>TEAMLEADER STATUS</th>
		<th>TEAMLEADER DATE</th>
		<th>TYPE</th>
		<th>BAND</th>		
		<th>UPG NR</th>
		<th>PARTNER</th>
		<th>NEW: ANTTYPE<br>UPG: UPGNAME (title)</th>
		<th>A/U353 (SITE FUNDED)</th>
		<th>A/U305 (BSDS FUNDED)</th>
		<th>A/U307 (MATERIAL AVAILABLE)</th>
		<th>A/U307 (ESTIMATED)</th>
		<th>U571/A71 (ESTIMATED)</th>
		<th>U571/A71 (SITE INTEGRATED)</th>		
	</tr>	
	</thead>
	<tbody>
	<?
	//echo "<pre>".print_r($data,true)."</pre>";
	$changecolor="odd";
	for ($i = 0; $i <= $z; $i++) {	
		$COLOR="";
	
		if ($data[$i]['SITEID']!=$previous_siteid){
			if ($changecolor=="odd"){
				$changecolor="even";
			}else{
				$changecolor="odd";
			}			
		}
					
		if ($data[$i]['STATUS']=="BSDS FUNDED"){
			$COLOR="BSDS_funded";
		}else if ($data[$i]['STATUS']=="SITE FUNDED"){
			$COLOR="Site_funded";
		}else if ($data[$i]['STATUS']=="BSDS AS BUILD"){
			$COLOR="BSDS_asbuild";
		}else if ($data[$i]['STATUS']=="SITE FUNDED => DEFUNDED TO OLD DATE" ||$data[$i]['STATUS']=="BSDS FUNDED => DEFUNDED TO OLD DATE" ||$data[$i]['STATUS']=="BSDS AS BUILD => DEFUNDED TO OLD DATE" ){
			$COLOR="refunded";
		}			
		echo "<tr class='$changecolor'>
		<td>".$data[$i]['SITEID']."</td>
		<td>".$data[$i]['BSDSKEY']."</td>
		<td >".$data[$i]['BSDS_BOB_REFRESH']."</td>
		<td>".$data[$i]['TECHNOLOGY']."</td>
		<td class='".$COLOR."'>".$data[$i]['STATUS']."</td>			
		<td>".$data[$i]['TEAM_STATUS']."</td>
		<td>".$data[$i]['TEAM_DATE']."</td>	
		<td>".$data[$i]['TYPE']."</td>
		<td>".$data[$i]['BAND']."</td>
		<td>".$data[$i]['WOR_UDK']."</td>
		<td>".$data[$i]['CON']."</td>
		<td>".$data[$i]['WOR_NAME']."</td>
		<td>".$data[$i]['SITE_FUNDED']."</td>
		<td>".$data[$i]['BSDS_FUNDED']."</td>
		<td>".$data[$i]['307']."</td>
		<td>".$data[$i]['307_ESTIM']."</td>
		<td>".$data[$i]['BUILD_ESTIM']."</td>
		<td>".$data[$i]['BUILD']."</td>			
		</tr>";
		
		
		$previous_siteid=$data[$i]['SITEID'];
	}
?>
	</tbody>
	</table>
	</div>
<?	
}
?>
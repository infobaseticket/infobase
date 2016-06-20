<?php

$config['debug']=false;
$config['cache']=false;
$config['mail']=true;
if ($config['debug']===false){
	//include_once('ChromePhp.php');
}

$config['sitepath_url']= "http://".$_SERVER['HTTP_HOST'];

$config['table_asset_BSDSinfo']="BSDSINFO2";
$config['table_asset_geninfo']="BSDSINFO1";
$config['table_asset_lognode']="lognode@BASEPRO7";
$config['table_asset_UMTSinfo']="umtsbsds4";
$config['table_asset_cellsites']="gsmcell@BASEPRO7";
$config['table_asset_repeaters']="VW_REPEATERS";
$config['table_asset_feeder']="FEEDER";
$config['table_asset_antennatype']="ANTENNATYPE";
$config['table_asset_cellequipment']="VWCELLEQUIPMENT";
$config['table_asset_bts']="VWBTS";
$config['sitepath_abs']= "/var/www/html";
$config['ranfolder']= "/var/www/html/ALURAN/RAN-ALU/";
$config['ranfolder_url']= $config['sitepath_url']."/ALURAN/RAN-ALU/";
$config['ranfolderBENCH']= "/var/www/html/RAN/BENCHMARK_RAN/RAN_BMT/";
$config['ranfolderBENCH_url']= $config['sitepath_url']."/RAN/BENCHMARK_RAN/RAN_BMT/";
$config['ranfolderBENCH2']= "/var/www/html/RAN/BENCHMARK_RAN/RAN_BENCH/";
$config['ranfolderBENCH2_url']= $config['sitepath_url']."/RAN/BENCHMARK_RAN/RAN_BENCH/";
$config['ranfolderBENCHSUBMIT']= "/var/www/html/RAN/BENCHMARK_SUBMIT/";
$config['ranfolderBENCHSUBMIT_url']= $config['sitepath_url']."/RAN/BENCHMARK_SUBMIT/";
$config['ranfolderM4C']= "/var/www/html/RAN/RAN_INFOBASE/RAN SUBMIT ZTE/RAN-M4C/";
$config['ranfolderM4C_url']= $config['sitepath_url']."/RAN_INFOBASE/RAN SUBMIT ZTE/RAN-M4C/";
$config['ranfolderBASE']= "/var/www/html/RAN/RAN_INFOBASE/RAN-BASE/";
$config['ranfolderBASE_url']= $config['sitepath_url']."/RAN_INFOBASE/RAN-BASE/";

$config['ranfolderARCHIVE']= "/var/www/html/RAN/RAN_ARCHIVE/01. OLD ALU RAN/";
$config['ranfolderARCHIVE_url']= $config['sitepath_url']."/RAN/RAN_ARCHIVE/01. OLD ALU RAN/";
$config['ranfolderIB']= "/var/www/html/RAN/RAN_INFOBASE/";
$config['ranfolderLEASE']= "/var/www/html/RAN/RAN_INFOBASE/RAN-BASELeaseBP/";
$config['ranfolderLEASE_url']= $config['sitepath_url']."/RAN/RAN_INFOBASE/RAN-BASELeaseBP/";


$config['net1db']= "net1prd";

$config['mail_host']="localhost";

$config['server_root']="/var/www/html";
$config['server_root_url']="http://".$_SERVER['HTTP_HOST'];
$config['phpguarddog_path']=$config['sitepath_abs']."/bsds/PHPlibs/phpguarddog";
$config['NET1updater_path']=$config['sitepath_abs']."/infobase/files/net1updater/";

$config['explorer_url']=$config['server_root_url']."/bsds/";
//********************************  connection settings **************************************************

//putenv("ORACLE_SID=INFOBASE");

/* Oracle Database settings for Infobase */
$user_Infobase = "infobase";
$passwd_Infobase= "info123";
$machine_Infobase="svrbeibase02.euronorth.aircominternational.com";
$port_Infobase="1521";
$service_Infobase="INFOBASE";
$sid_Infobase = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=".$machine_Infobase.")(PORT=".$port_Infobase."))(CONNECT_DATA=(SERVER = DEDICATED) (SERVICE_NAME=".$service_Infobase.")))";
//echo "$host_Infobase";
$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$sid_Netone="//10.122.99.168/NET1PRD.local";
$host_Netone = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=PUPPIS)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=".$sid_Netone.")))";


/* Mysql Database settings */
$mysql_host="localhost";
$mysql_user="root";
$mysql_password="";

/* Database for PHP guarddog */
$db_guarddog="guarddog_typo";
/* Database for almost everything */
$db_MSCdata="MSCdata";
/* Database for almost everything */
$db_eventcal="event_calendar";


//****************************************  PRINTFILES OSS ************************************************************
$OSS_files_folder=$config['server_root']."/infobase/files/Switch_files/OSS_files/";
$OSS_files_folder_celldata=$config['server_root']."/infobase/files/Switch_files/OSS_adjustment/OSS_FILES/";

//****************************************  RAF FILE LOCATION *********************************************************
$config['raf_folder_abs']="/var/www/html/infobase/files/raf/";
$config['raf_folder']="../infobase/files/raf/";
$config['raf_folder_url']=$config['sitepath_url']."/infobase/files/raf/";
//****************************************  LOS FILE LOCATION *********************************************************
$config['los_folder_abs']="/var/www/html/infobase/files/los/";
$config['los_folder']="../infobase/files/los/";
$config['los_folder_url']=$config['sitepath_url']."/infobase/files/los/";
//****************************************  AUDIT FILE LOCATION *********************************************************
$config['audit_folder_abs']="/var/www/html/infobase/files/audits/";
$config['audit_folder']="../infobase/files/audits/";

//********************************  Deismanlting/replacement report ************************************************************
$config['dism_repl_report']=$config['server_root']."/infobase/files/exports/DismRepl/";
$config['dism_repl_report_url']=$config['server_root_url']."/infobase/files/exports/DismRepl/";


/************************************ GLOBAL FUNCTIONS *********************************************/
/* GET name of a user for php guarddog */
function getuserdata($user){
	if (trim($user)!="'"){
		global $mysql_host, $mysql_user, $mysql_password,$db_guarddog;
		$link = mysql_connect($mysql_host, $mysql_user, $mysql_password,$db_guarddog)
			   or die('Could not connect: ' . mysql_error());
		mysql_select_db($db_guarddog) or die('Could not select database');
		$query = "SELECT * FROM phplog_userinput inner join phplog_users on phplog_userinput.userid = phplog_users.userid WHERE username = '$user' ORDER by inputfieldid DESC";
		//echo $query;
		$result = mysql_query($query) or die('1Query failed: ' . mysql_error());
		while ($row = mysql_fetch_assoc($result)) {
			if ($row["inputfieldid"]==1){
				$userdetails['firstname']=ucfirst($row["value"]);
			}
			if ($row["inputfieldid"]==2){
				$userdetails['lastname']=ucfirst($row["value"]);
			}
			if ($row["inputfieldid"]==7){
				$userdetails['mobile']=$row["value"];
			}
			if ($row["inputfieldid"]==5){
				$userdetails['employer']=$row["value"];
			}
			if ($row["inputfieldid"]==8){
				$userdetails['netoneuser']=$row["value"];
			}
			if ($row["inputfieldid"]==9){
				$userdetails['netonepass']=$row["value"];
			}
			$userdetails['email']=$row["email"];
			$userdetails['username']=$row["username"];
			}
		$userdetails['fullname']=$userdetails['firstname']." ".$userdetails['lastname'];
		mysql_free_result($result);
		mysql_close($link);
		return $userdetails;
	}

}
/*Get all users from a particular group */
function getusers_from_group($group,$userlevel){
	global $mysql_host, $mysql_user, $mysql_password,$db_guarddog;
	$link = mysql_connect($mysql_host, $mysql_user, $mysql_password)  or die('Could not connect: ' . mysql_error());
	mysql_select_db($db_guarddog) or die('Could not select database');

	$groups=explode(",",$group);
	foreach ($groups as $name){
		$groupfilter.="`groupname`= '$name' OR ";
		//echo $groupfilter."<br>";
	}
	$groupfilter=substr($groupfilter,0,-3);

	if ($group!="ALL"){
		$query1 = "SELECT groupid FROM `phplog_groups` WHERE $groupfilter";
		//echo $query1."<br>";
		$result1 = mysql_query($query1) or die($query1.' failed: ' . mysql_error());
		while($row1 = mysql_fetch_array($result1)){
			$groupfilter_id.="`groupid`= '".$row1[0]."' OR ";
		}
		$groupfilter_id=substr($groupfilter_id,0,-3);

		//echo $groupfilter_id."<br>";
		$query2 = "SELECT phplog_users.userid, email, username FROM `phplog_members` inner join phplog_users on phplog_members.userid = phplog_users.userid WHERE $groupfilter_id";
	}else{
		$query2="SELECT phplog_users.userid, email, username FROM phplog_users ";

	}

		if ($userlevel!=""){
			$query2 .=" AND accesslevel $userlevel";
		}
		//echo $query2."<br>";
		$result2 = mysql_query($query2) or die($query2.' failed: ' . mysql_error());
		$i=0;
		while ($row2 = mysql_fetch_assoc($result2)) {
			$email=$row2["email"];
			$userid=$row2["userid"];
			$username=$row2["username"];
			$query3 = "SELECT value, inputfieldid FROM phplog_userinput WHERE userid='$userid' ORDER by inputfieldid";
			//echo "$query3";
			$result3 = mysql_query($query3) or die($query3.' failed: ' . mysql_error());
			while ($row3 = mysql_fetch_assoc($result3)) {
				if ($row3["inputfieldid"]=="1" || $row3["inputfieldid"]	=="2"){
					$name=ucfirst($row3["value"])." ".$name;
				}else if ($row3["inputfieldid"]=="4"){
					$mobile=$row3["value"];
				}else if ($row3["inputfieldid"]=="5"){
					$employer=$row3["value"];
				}else if ($row3["inputfieldid"]=="6"){
					$ip=$row3["value"];
				}else if ($row3["inputfieldid"]=="7"){
					$deskphone=$row3["value"];
				}
			}

			$query4 = "SELECT * FROM phplog_members WHERE userid='$userid' ORDER by groupid";
			//echo "$query4";
			$result4 = mysql_query($query4) or die($query4.' failed: ' . mysql_error());
			while ($row4 = mysql_fetch_array($result4)) {
				//echo "$row4[1] ,";
				$query5 = "SELECT * FROM phplog_groups WHERE groupid='$row4[1]' ORDER by groupid";
				//echo "$query5 <br>";
				$result5 = mysql_query($query5) or die($query5.' failed: ' . mysql_error());
				$row5 = mysql_fetch_array($result5);
				$groups=$row5[1].", ".$groups;
			}
			$groups=substr($groups,0,-2);
			//echo $groups."<br>";
			$userdetails[$i]['fullname']=$name;
			$userdetails[$i]['username']=$username;
			$userdetails[$i]['email']=$email;
			$userdetails[$i]['mobile']=$mobile;
			$userdetails[$i]['employer']=$employer;
			$userdetails[$i]['groups']=$groups;
			$userdetails[$i]['ip']=$ip;
			$userdetails[$i]['deskphone']=$deskphone;

			$name="";
			$email="";
			$mobile="";
			$employer="";
			$groups="";
			$ip="";
			$deskphone="";
			$i++;
		}

	return $userdetails;
}

function getusers_from_fields($filters){

	global $mysql_host, $mysql_user, $mysql_password,$db_guarddog;
	$link = mysql_connect($mysql_host, $mysql_user, $mysql_password)  or die('Could not connect: ' . mysql_error());
	mysql_select_db($db_guarddog) or die('Could not select database');

	$query = "SELECT userid FROM phplog_userinput WHERE $filters ORDER by inputfieldid";
	//echo $query."<br>";
	$result = mysql_query($query) or die($query.' failed: ' . mysql_error());
	$i=0;
	while ($row = mysql_fetch_assoc($result)) {
		$query2 = "SELECT * FROM phplog_userinput WHERE userid='".$row[userid]."' ORDER by inputfieldid";
		//echo $query2."<br>";
		$result2 = mysql_query($query2) or die('query failed: ' . mysql_error());
		while ($row2 = mysql_fetch_assoc($result2)) {
			if ($row2["inputfieldid"]=="1" || $row2["inputfieldid"]	=="2"){
				$name=ucfirst($row2["value"])." ".$name;
			}else if ($row2["inputfieldid"]=="4"){
				$mobile=$row2["value"];
			}else if ($row2["inputfieldid"]=="5"){
				$employer=$row2["value"];
			}else if ($row2["inputfieldid"]=="6"){
				$ip=$row2["value"];
			}else if ($row2["inputfieldid"]=="7"){
				$deskphone=$row2["value"];
			}
		}

		$query3 = "SELECT * FROM phplog_users WHERE userid='".$row[userid]."'";
		//echo $query3."<br>";
		$result3 = mysql_query($query3) or die('query failed: ' . mysql_error());
		$row3 = mysql_fetch_assoc($result3);
		//$groups=substr($groups,0,-2);
		//echo $groups."<br>";
		$userdetails[$i]['username']=$row3[username];
		$userdetails[$i]['email']=$row3[email];
		$userdetails[$i]['fullname']=$name;
		$userdetails[$i]['mobile']=$mobile;
		$userdetails[$i]['employer']=$employer;
		//$userdetails[$i]['groups']=$row3[groups];
		$userdetails[$i]['ip']=$ip;
		$userdetails[$i]['deskphone']=$deskphone;

		$name="";
		$email="";
		$mobile="";
		$employer="";
		$groups="";
		$ip="";
		$deskphone="";
		$i++;
	}

	return $userdetails;

}

//used in check_bsds_funded
function inStr ($needle, $haystack)
{
  $needlechars = strlen($needle); //gets the number of characters in our needle
  $i = 0;
  for($i=0; $i < strlen($haystack); $i++) //creates a loop for the number of characters in our haystack
  {
    if(substr($haystack, $i, $needlechars) == $needle) //checks to see if the needle is in this segment of the haystack
    {
      return TRUE; //if it is return true
    }
  }
  return FALSE; //if not, return false
}
//Error handling
function debugPrintCallingFunction () { 
    $file = 'n/a'; 
    $func = 'n/a'; 
    $line = 'n/a'; 
    $debugTrace = debug_backtrace(); 
    if (isset($debugTrace[1])) { 
        $deb['file'] = $debugTrace[1]['file'] ? $debugTrace[1]['file'] : 'n/a'; 
        $deb['line'] = $debugTrace[1]['line'] ? $debugTrace[1]['line'] : 'n/a'; 
    } 
    if (isset($debugTrace[2])) $deb['function'] = $debugTrace[2]['function'] ? $debugTrace[2]['function'] : 'n/a'; 
    return $deb; 
} 

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        //echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        //echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo  "Unknown error type: [$errno] $errstr<br />\n";
        die;

    }
    return true;
}

/*********************************************************************************************************************/

function get_BSDSrefresh(){

  global $conn_Infobase;
  global $guard_groups;

  $query = "select * FROM IMPORT_STATUS";
  //echo "<br><br>$query";
  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
  if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
  } else {
        OCIFreeStatement($stmt);
  }

  $now = time();
  $event_time = strtotime("23:59");

  for ($i = 0; $i < count($res1['TYPE']); $i++) {
  	
	if ($res1['TYPE'][$i]=="BSDS_UPG"){
		$BSDSrefresh['BSDS_UPG'] = $res1['STATUS'][$i];
		$BSDSrefresh['DATE_UPG']= $res1['RUN'][$i];
		$time=strtotime(substr($res1['NEXTRUN'][$i],11,8));
		if($time < $event_time){
		   $next=substr($res1['NEXTRUN'][$i],11,8);
		}else{
			$next='6:30';
		}
		$BSDSrefresh['NEXTRUN_UPG']= $next;
		$BSDSrefresh['ACTION_BSDS_UPG']= $res1['ACTION'][$i];
	}else if ($res1['TYPE'][$i]=="BSDS_NEW"){
		$BSDSrefresh['BSDS_NEW'] = $res1['STATUS'][$i];
  		$BSDSrefresh['DATE_NEW']= $res1['RUN'][$i];
  		$time=strtotime(substr($res1['NEXTRUN'][$i],11,8));
		if($time < $event_time){
		   $next=substr($res1['NEXTRUN'][$i],11,8);
		}else{
			$next='6:30';
		}
  		$BSDSrefresh['NEXTRUN_NEW']= $next;
  		$BSDSrefresh['ACTION_BSDS_NEW']= $res1['ACTION'][$i];
	}else if ($res1['TYPE'][$i]=="BSDS_CAB"){
		$BSDSrefresh['CABUPG'] = $res1['STATUS'][$i];
  		$BSDSrefresh['DATE_CABUPG']= $res1['RUN'][$i];
  		$time=strtotime(substr($res1['NEXTRUN'][$i],11,8));
		if($time < $event_time){
		   $next=substr($res1['NEXTRUN'][$i],11,8);
		}else{
			$next='6:30';
		}
  		$BSDSrefresh['NEXTRUN_CABUPG']= $next;
  		$BSDSrefresh['ACTION_CABUPG']= $res1['ACTION'][$i];
	}else if ($res1['TYPE'][$i]=="ALL_UPG_NET1"){
		$BSDSrefresh['ALL_UPG_NET1'] = $res1['STATUS'][$i];
  		$BSDSrefresh['DATE_ALL_UPG']= $res1['RUN'][$i];
  		$time=strtotime(substr($res1['NEXTRUN'][$i],11,8));
		if($time < $event_time){
		   $next=substr($res1['NEXTRUN'][$i],11,8);
		}else{
			$next='6:00';
		}
  		$BSDSrefresh['NEXTRUN_ALL_UPG']= $next;
  		$BSDSrefresh['ACTION_ALL_UPG_NET1']= $res1['ACTION'][$i];
	}else if ($res1['TYPE'][$i]=="ALL_NEW_NET1"){
		$BSDSrefresh['ALL_NEW_NET1'] = $res1['STATUS'][$i];
  		$BSDSrefresh['DATE_ALL_NEW']= $res1['RUN'][$i];
  		$time=strtotime(substr($res1['NEXTRUN'][$i],11,8));
		if($time < $event_time){
		   $next=substr($res1['NEXTRUN'][$i],11,8);
		}else{
			$next='6:00';
		}
  		$BSDSrefresh['NEXTRUN_ALL_NEW']= $next;
  		$BSDSrefresh['ACTION_ALL_NEW_NET1']= $res1['ACTION'][$i];
  	}else if ($res1['TYPE'][$i]=="RAF_PROCESS"){
		$BSDSrefresh['RAF_PROCESS'] = $res1['STATUS'][$i];
  		$BSDSrefresh['DATE_RAF_PROCESS']= $res1['RUN'][$i];
  		$BSDSrefresh['ACTION_RAF_PROCESS']= $res1['ACTION'][$i];
  		if($time < $event_time){
		   $next=substr($res1['NEXTRUN'][$i],11,8);
		}else{
			$next='6:05';
		}
  		$BSDSrefresh['NEXTRUN_RAF_PROCESS']= $next;
	}else if ($res1['TYPE'][$i]=="OSS3G"){
		$BSDSrefresh['OSS3G'] = $res1['STATUS'][$i];
  		$BSDSrefresh['DATE_OSS3G']= $res1['RUN'][$i];
  		$BSDSrefresh['ACTION_OSS3G']= $res1['ACTION'][$i];
  	}else if ($res1['TYPE'][$i]=="MASTER_MATERIAL"){
		$BSDSrefresh['MASTER_MATERIAL'] = $res1['STATUS'][$i];
  		$BSDSrefresh['DATE_MASTER_MATERIAL']= $res1['RUN'][$i];
  		$BSDSrefresh['ACTION_MASTER_MATERIAL']= $res1['ACTION'][$i];
  		if($time < $event_time){
		   $next=substr($res1['NEXTRUN'][$i],11,8);
		}else{
			$next='6:00';
		}
  		$BSDSrefresh['NEXTRUN_MASTER_MATERIAL']= $next;
  	}else if ($res1['TYPE'][$i]=="RAN_SCAN_BENCHMARK_SUBMIT" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/AN" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/BW" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/BX" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/NR" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/OV" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/WV" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/LG" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/LI" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/LX" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/VB" or $res1['TYPE'][$i]=="RAN_SCAN_RAN/BENCHMARK_SUBMIT/HT"){
  		
  		if ($res1['STATUS'][$i]!='' && $res1['STATUS'][$i]!='STARTED'){
			$BSDSrefresh['RAN_SCAN_BENCHMARK_SUBMIT'] = "SYNC NOT OK";
	  		$BSDSrefresh['DATE_RAN_SCAN_BENCHMARK_SUBMIT']= $res1['RUN'][$i];
	  		$BSDSrefresh['ACTION_RAN_SCAN_BENCHMARK_SUBMIT']= $res1['ACTION'][$i];
	  	}else if ($BSDSrefresh['RAN_SCAN_BENCHMARK_SUBMIT']!="SYNC NOT OK"){
	  		$BSDSrefresh['RAN_SCAN_BENCHMARK_SUBMIT'] = "";
	  		$BSDSrefresh['DATE_RAN_SCAN_BENCHMARK_SUBMIT']= $res1['RUN'][$i];
	  		$BSDSrefresh['ACTION_RAN_SCAN_BENCHMARK_SUBMIT']= $res1['ACTION'][$i];
	  	}
	}
  }
  //print_r($BSDSrefresh);
  $query = "select count(SIT_ID) as amount FROM VW_NET1_NEWBUILDS";
  //echo "<br><br>$query";
  $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
  if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
  } else {
        OCIFreeStatement($stmt);
  }
  $BSDSrefresh['AMOUNT'] = $res1['AMOUNT'][0];

  return $BSDSrefresh;
}


//needed for filevalidations

function validateDate($day,$month,$year)
{
    $d = DateTime::createFromFormat('d-m-Y', $day."-".$month."-".$year);
    return $d && $d->format('d-m-Y') == $day."-".$month."-".$year;
}
?>

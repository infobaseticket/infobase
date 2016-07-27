<?php
require('guard_vars.php');
if($guard_debug==0)
error_reporting(0);
if(defined('INC_GUARD')) return;
define('INC_GUARD',true);

$guard_db_conn="";

$guard_firstloginattempt=1;

function start_sess() {
	$lifetime=60*60*24;
  	session_start();
  	setcookie(session_name(),session_id(),time()+$lifetime);
	global $guard_theremoteaddr;
	global $guard_browsertype;
}


function guard_db_connect() {
	$checkinput=array();
	set_audit($checkinput);
	global $guard_db_host,$guard_db_user,$guard_db_pass,$guard_db_name,$guard_db_conn;


	$guard_db_conn=mysql_connect($guard_db_host,$guard_db_user,$guard_db_pass);

	if(!$guard_db_conn)
		echo "Could not connect to MySQL host.";
	$dbc=@mysql_select_db($guard_db_name);
	if(!$dbc)
		echo "Could not connect to specified database.";
	return $checkinput;
}

function db_safe(&$sql) {/*
	if(get_magic_quotes_gpc()==1)
		$str=mysql_escape_string(stripslashes($sql));
	else
		$str=mysql_escape_string($sql);*/
		$str=$sql;
	return $str;
}
function db_query(&$sql) {
	global $guard_db_conn;

	$checkinput=array();
	set_audit($checkinput);
	$result=mysql_query($sql,$guard_db_conn);
	if(!$result) {
		set_audit($checkinput,"Could not execute this query.");
		return $checkinput;
	}
	else
		return $result;
}
function db_close() {
	global $guard_db_conn;
	mysql_close($guard_db_conn);
}

function get_users_groups($userid){
	$userid=(int) $userid;
	$checkinput=array();
	set_audit($checkinput);
	if(!does_user_exist($userid)) {
		set_audit($checkinput,"User ID specified does not exist.");
		return $checkinput;
	}
	$goSQL="SELECT * FROM phplog_members M,phplog_groups G WHERE M.groupid=G.groupid AND M.userid=".db_safe($userid);
	$rs=db_query($goSQL);
	$x=0;
	$grouparr=array();
	while($row=mysql_fetch_array($rs)) {
		$grouparr[$x]=$row['groupname'];
		$x++;
	}
	return $grouparr;
}


function _does_exist($type,$id) {
	$id=(int) $id;
	$goSQL="SELECT * FROM phplog_".$type."s WHERE ".$type."id=".db_safe($id);
	$rs=db_query($goSQL);
	if(mysql_num_rows($rs)==0)
		return false;
	return true;
}
function does_user_exist($userid){return _does_exist("user",$userid); }
function does_group_exist($groupid){return _does_exist("group",$groupid); }


function logout() {
	$_SESSION['guard_username']="";
	$_SESSION['guard_userid']="";
	$_SESSION['guard_groups']="";
	$_SESSION['guard_accesslevel']="";
	$_SESSION['guard_active']="";
	$_SESSION['guard_datecreated']="";
	$_SESSION['guard_dateexpires']="";
	$_SESSION['REMOTE_ADDR']="";
	$_SESSION['BROWSER_TYPE']="";

	global $guard_username,$guard_userid,$guard_groups,$guard_accesslevel,$guard_active,$guard_datecreated,$guard_dateexpires;

	$guard_username="";
	$guard_userid="";
	$guard_groups="";
	$guard_accesslevel="";
	$guard_active="";
	$guard_datecreated="";
	$guard_dateexpires="";
	session_unset();
	session_destroy();

	setcookie("guard_login","",time()+28800,"/");
	setcookie("guard_login","",time()-30000,"/");
}


function set_audit(&$arraycheck,$str="") {
	$arraysize=count($arraycheck);
	$str=(string) $str;
	if($arraysize==0)
		$arraycheck[0]=true;
	else {
		$arraycheck[0]=false;
		$arraycheck[$arraysize]=$str;
	}
}


function _get_singular($given,$type,$searchfor) {
	$thereturn="";
	$checkinput=array();
	set_audit($checkinput);
	if($searchfor=="id") {
		$wehave="name";
		$quote="'";
	}
	else {
		$wehave="id";
		$quote="";
	}
	if(trim($given)=="")
		set_audit($checkinput,"You must supply a string to search on.");
	$goSQL="SELECT $type$searchfor FROM phplog_".$type."s WHERE ".$type.$wehave."=".$quote.db_safe($given).$quote;
	$rs=db_query($goSQL);
	if(mysql_num_rows($rs)==1) {
		$row=mysql_fetch_array($rs);
		$thereturn=$row["$type$searchfor"];
	}
	if($thereturn=="")
		set_audit($checkinput,"Did not find any results.");
	if(count($checkinput)>1)
		return $checkinput;
	return $thereturn;
}

function get_group_id($groupname){return _get_singular($groupname,"group","id");}
function get_group_name($groupid){return _get_singular($groupid,"group","name");}
function get_user_id($username){return _get_singular($username,"user","id");}
function get_user_name($userid){return _get_singular($userid,"user","name");}



//does not use set_audit
function _get_all($type,$theselect,$optional="") {
	$allelements=array();
	if($theselect=="assoc") {
		if($optional=="private")
			$goSQL="SELECT ".$type."id,".$type."name FROM phplog_".$type."s WHERE selectable=1 ORDER BY ".$type."name ASC";
		else
			$goSQL="SELECT ".$type."id,".$type."name FROM phplog_".$type."s ORDER BY ".$type."name ASC";
		$rs=db_query($goSQL);
		$i=0;
		while($row=mysql_fetch_array($rs)) {
			$allelements[$row[$type."id"]]=$row[$type."name"];
			$i++;
		}
	}
	else {
		$goSQL="SELECT $type$theselect FROM phplog_".$type."s ORDER BY $type$theselect ASC";
		$rs=db_query($goSQL);
		$i=0;
		while($row=mysql_fetch_array($rs)) {
			$allelements[$i]=$row["$type"."$theselect"];
			$i++;
		}
	}
	return $allelements;
}
function get_group_names(){return _get_all("group","name");}
function get_user_names(){return _get_all("user","name");}
function get_group_ids(){return _get_all("group","id");}
function get_user_ids(){return _get_all("user","id");}
function get_groups($type=""){return _get_all("group","assoc",$type);}
function get_users(){return _get_all("user","assoc");}


function date_difff($thedate) {
	$tstamp1=time();
	$tstamp2=strtotime($thedate);
	return $tstamp2-$tstamp1;
}


function sys_msg($str,$type,$menu="") {
	print "<script language=JAVASCRIPT>document.location.href='sysmsg.php?msg=".$str."&type=".$type."&menu=".$menu."';</script>";
	exit;
	die;
}

function guard_login($user,$pass) {
	$_SESSION['guard_username']="";
	$_SESSION['guard_userid']="";
	$_SESSION['guard_groups']="";
	$_SESSION['guard_accesslevel']="";
	$_SESSION['guard_active']="";
	$_SESSION['guard_datecreated']="";
	$_SESSION['guard_dateexpires']="";

	$checkinput=array();
	set_audit($checkinput);

	$goSQL="SELECT * FROM phplog_users WHERE LOWER(username)='".$user."' AND active=1";
	
	$rs=db_query($goSQL);

	if(mysql_num_rows($rs)==1) {
		$row=mysql_fetch_array($rs);

		list($strPassword, $strSalt) = explode(':', $row['password']);

		// Password is correct but not yet salted
		if (!strlen($strSalt) && $strPassword == sha1($pass))
		{
			$strSalt = substr(md5(uniqid('', true)), 0, 23);
			$strPassword = sha1($strSalt . $pass);
			$password = $strPassword . ':' . $strSalt;
		}
//From Contao3:
		$blnAuthenticated = (crypt($pass, $row['password']) == $row['password']);
	// Check the password against the database
		//if (strlen($strSalt) && $strPassword == sha1($strSalt . $pass))
		if ($blnAuthenticated)
		{

			if(date_difff($row['dateexpires'])<0 && $row['dateexpires']!="0000-00-00") {
				$goSQL="UPDATE phplog_users SET active=0 WHERE userid=".$row['userid'];
				$rs=db_query($goSQL);
				set_audit($checkinput,"Your username is expired.");
				return $checkinput;
			}

			$useridvar=$row['userid'];
			$groupsarr=get_users_groups($row['userid']);

			for($i=0;$i<count($groupsarr);$i++) {
				$goSQL="SELECT groupid,active,dateexpires FROM phplog_groups WHERE groupid=".get_group_id($groupsarr[$i]);
				//echo $goSQL;
				$rs=db_query($goSQL);
				$row2=mysql_fetch_array($rs);
				if(date_difff($row2['dateexpires'])<0 && $row2['dateexpires']!="0000-00-00") {
					$goSQL="UPDATE phplog_groups SET active=0 WHERE groupid=".$row2['groupid'];
					$rs=db_query($goSQL);
				}
			}

			$groupstr="";
			//echo "<pre>".print_r($groupsarr)."</pre>";
			for($i=0;$i<count($groupsarr);$i++) {
				$groupstr=$groupstr.$groupsarr[$i].",";}
			if(strlen($groupstr)>0)
				$groupstr=substr($groupstr,0,strlen($groupstr)-1);
			$guard_groups=$groupstr;

			$_SESSION['guard_username']=$user;
			$_SESSION['guard_userid']=$useridvar;
			$_SESSION['guard_accesslevel']=$row['accesslevel'];
			$_SESSION['guard_active']=$row['active'];
			$_SESSION['guard_datecreated']=$row['datecreated'];
			$_SESSION['guard_dateexpires']=$row['dateexpires'];
			$_SESSION['guard_groups']=$groupstr;

			track($_SESSION['guard_userid'],$_SESSION['guard_username'],"Successfully logged in user.","1");

			global $guard_noredirect;
			if($guard_noredirect!="NOREDIRECT") {
				$goSQL="SELECT redirect FROM phplog_users WHERE userid=".$_SESSION['guard_userid'];
				$rs=db_query($goSQL);
				$row=mysql_fetch_array($rs);

				if($row['redirect']!="") {

					header("Location: ".$_SERVER['SERVER_NAME']."/".$row['redirect']);
				}
				for($i=0;$i<count($groupsarr);$i++) {
					$goSQL="SELECT redirect FROM phplog_groups WHERE groupid=".get_group_id($groupsarr[$i]);
					$rs=db_query($goSQL);
					$row=mysql_fetch_array($rs);
					if($row['redirect']!="") {
						header("Location: http://".$_SERVER['SERVER_NAME']."/".$row['redirect']);
					}
				}
			}

			return $checkinput;
		}
	}
	set_audit($checkinput,"Could not log in with user name/password combination.");

	track("0",$user,"User did not log in successfully.","0");
	return $checkinput;
}

function track($userid,$username,$msg,$successful) {

	if(defined('INC2')) return;
	define('INC2',true);

	global $guard_thescriptname;
	global $guard_theremoteaddr;
	global $guard_browsertype;

	if($userid=="") $userid=0;

	$goSQL="INSERT INTO phplog_tracking (userid,username,pageaccessed,ipaddress,browsertype,accesstime,message,successful) VALUES ($userid,'$username','".$guard_thescriptname."','".$guard_theremoteaddr."','".$guard_browsertype."','".date("Y-m-d H:i:s")."','$msg',$successful)";
	$rs=db_query($goSQL);

	if($msg=="Successfully logged in user.") {
		$goSQL="UPDATE phplog_users SET datelastlogin='".date("Y-m-d H:i:s")."' WHERE userid=".$userid;
		$rs=db_query($goSQL);
	}

	$goSQL="SELECT numbertracking FROM phplog_pref";
	$rs=db_query($goSQL);
	$row=mysql_fetch_array($rs);

	if((int)$userid!=0) {
		$goSQL="SELECT userid FROM phplog_tracking WHERE userid=".$userid;
		$rs=db_query($goSQL);
		if(mysql_num_rows($rs)>(int)$row['numbertracking']) {
			$goSQL="SELECT min(accesstime) AS accesstime FROM phplog_tracking WHERE userid=".$userid;
			$rs2=db_query($goSQL);
			$row=mysql_fetch_array($rs2);
			$goSQL="DELETE FROM phplog_tracking WHERE userid=".$userid." AND accesstime='".$row['accesstime']."'";
			$rs=db_query($goSQL);
		}
	}
	else {
		$goSQL="SELECT userid FROM phplog_tracking WHERE userid=0";
		$rs=db_query($goSQL);
		if(mysql_num_rows($rs)>(int)$row['numbertracking']) {
			$goSQL="SELECT min(accesstime) AS accesstime FROM phplog_tracking WHERE userid=0";
			$rs2=db_query($goSQL);
			$row=mysql_fetch_array($rs2);
			$goSQL="DELETE FROM phplog_tracking WHERE userid=0 AND accesstime='".$row['accesstime']."'";
			$rs=db_query($goSQL);
		}
	}
}

function protect($users="",$groups="",$accesslevel=3) {
	global $guard_thefilename;
	//echo ini_get('session.gc_maxlifetime');
	//echo strlen($_SESSION['guard_username']);
	if(strlen($_SESSION['guard_username'])==0 || strlen($_SESSION['guard_userid'])==0)
		print_login_screen();

	$goSQL="SELECT accesslevel,active,dateexpires,username FROM phplog_users WHERE userid=".$_SESSION['guard_userid'];
	$rs=db_query($goSQL);
	$row=mysql_fetch_array($rs);
	if((int)$row['accesslevel']<(int)$accesslevel || (int)$row['active']==0)
		print_login_screen();

	if(!is_array($users)) {
		$usersarr=explode(",",$users);
		for($i=0;$i<count($usersarr);$i++)
			$usersarr[$i]=trim($usersarr[$i]);
	}
	else
	$usersarr=&$users;
	if(!is_array($groups)) {
		$groupsarr=explode(",",$groups);
		for($i=0;$i<count($groupsarr);$i++)
			$groupsarr[$i]=trim($groupsarr[$i]);
	}
	else
	$groupsarr=&$groups;



	if(trim($usersarr[0])=="" && trim($groupsarr[0])=="") {
			if($guard_thefilename=="")
				track($_SESSION['guard_userid'],$_SESSION['guard_username'],"Successfully accessed page.","1");
			else
				track($_SESSION['guard_userid'],$_SESSION['guard_username'],"Successfully accessed file: ".$guard_thefilename.".","1");
			return true;
	}
	if(trim($usersarr[0])!="") {
		if(in_array($row['username'],$usersarr)) {
			if($guard_thefilename=="")
				track($_SESSION['guard_userid'],$_SESSION['guard_username'],"Successfully accessed page.","1");
			else
				track($_SESSION['guard_userid'],$_SESSION['guard_username'],"Successfully accessed file: ".$guard_thefilename.".","1");

			return true;
		}
	}
	if(trim($groupsarr[0])!="") {
		$usersgroups=get_users_groups($_SESSION['guard_userid']);

		if(count($usersgroups)>0) {
			$newgrouparr=array();

			$z=0;
			for($i=0;$i<count($usersgroups);$i++) {
				$goSQL="SELECT active,dateexpires FROM phplog_groups WHERE groupid=".get_group_id($usersgroups[$i]);
				$rs=db_query($goSQL);
				$row=mysql_fetch_array($rs);
				//echo date_difff($row['dateexpires']);
				if(date_difff($row['dateexpires'])>0 || $row['dateexpires']=="0000-00-00") {
					$newgrouparr[$z]=$usersgroups[$i];
					$z++;
				}
			}

//echo "<pre>".print_r($newgrouparr)."</pre>";

			for($i=0;$i<count($newgrouparr);$i++) {
				if(in_array($newgrouparr[$i],$groupsarr)) {
					if($guard_thefilename=="")
						track($_SESSION['guard_userid'],$_SESSION['guard_username'],"Successfully accessed page.","1");
					else
						track($_SESSION['guard_userid'],$_SESSION['guard_username'],"Successfully accessed file: ".$guard_thefilename.".","1");

					return true;
				}
			}
		}
	}
	if($guard_thefilename=="")
		track($_SESSION['guard_userid'],$_SESSION['guard_username'],"User not allowed to access page.","0");
	else
		track($_SESSION['guard_userid'],$_SESSION['guard_username'],"User not allowed to access file: ".$guard_thefilename.".","0");



	print_login_screen();
	die;exit;
}

function print_login_screen() {
	global $guard_thescriptname;
	global $guard_firstloginattempt;
	global $guard_actionoverwrite;

	if(isset($_POST['_phpguarddogreturnurl'])) {
		print "<script language=javascript>document.location.href='".$_POST['_phpguarddogreturnurl']."?guard_loginerr=true';</script>";
		exit;
		die;
	}

	$noprotect="no";

	if($guard_firstloginattempt==0) {
		print "<p class='error' align='center'><b>Error(s) processing your request!</b></p>";
		print "</div>";
	}

	if($guard_actionoverwrite!="")
		$guard_thescriptname=$guard_actionoverwrite;

	
	$body= '  
	  <form action="'.$guard_thescriptname.'" method="POST" class="form-signin" role="form">
        <h2 class="form-signin-heading">Log in to Infobase</h2>
        <input type="text" name="username" class="form-control" placeholder="Username" required="" autofocus="">
        <input type="password" name="passwd" class="form-control" placeholder="Password" required="">
        <input class="btn btn-lg btn-primary btn-block" type="submit" name="_phpguarddoglogbut" value="Sign In">     
		<input type="hidden" name="_phpguarddogaction" value="login">';
	foreach($_GET as $key=>$value)
		$body.= "<input type=hidden name=\"".$key."\" value=\"".$value."\">\n";
	foreach($_POST as $key=>$value)
		if($key!="_phpguarddogaction" && $key!="username" && $key!="passwd" && $key!="_phpguarddoglogbut")
			$body.= "<input type=hidden name=\"".$key."\" value=\"".$value."\">\n";
	$body.= "     </form>
	  <br><p align='center'><a href='".$_SERVER['SERVER_NAME']."/infobase/lost-password.html'' class='rLink'>Forget your password?</a></p>";

	echo "<html><header><meta http-equiv='X-UA-Compatible' content='IE=edge' >";
	echo " <link href='bootstrap/css/bootstrap.min.css' rel='stylesheet'>";
	echo " <link href='css/login.css' rel='stylesheet'>";
	echo "</header><body>";
	echo $body;
	echo "</body></html>";


	exit;
	die;
}
function nobackslash(&$arr) {
   if(get_magic_quotes_gpc()==1) {
       foreach($arr as $k => $v) {
           switch(gettype($v)) {
               case 'string' :
                   $arr[$k]=str_replace("\"","'",stripslashes($v));
                   break;
               case 'array' :
                   nobackslash($arr[$k], false);
           }
       }
   }
}

if($guard_runbackslash==1) {
	nobackslash($_GET);
	nobackslash($_POST);
}

$startup=guard_db_connect();


// echo "<pre>".print_r($startup,true)."<pre>";
start_sess();


if(!$startup[0]) {
	if((int)$_GET['guard_isdown']!=1 && !isset($guard_skipdbcheck)) {
		print "<script language=JAVASCRIPT>document.location.href='".$config['sitepath_url_guarddog']."/guard_dbdown.php?guard_isdown=1';</script>";
		exit;
		die;
	}
}


if(isset($_POST['_phpguarddoglogbut']) && $_POST['_phpguarddogaction']=="login") {

	if($_COOKIE['guard_login']!="") {
		setcookie("guard_login","",time()+28800,"/");
		setcookie("guard_login","",time()-30000,"/");
	}
	guard_login($_POST['username'],$_POST['passwd']);
	$guard_firstloginattempt=0;
}
else {
	$guard_firstloginattempt=1;

	if(isset($_COOKIE['guard_login']) && !isset($_SESSION['guard_userid'])) {

		$thesplit=split(";",$_COOKIE['guard_login']);
		$goSQL="SELECT password FROM phplog_users WHERE LOWER(username)='".db_safe(strtolower($thesplit[0]))."' AND active=1";
		$rs=db_query($goSQL);
		$row=mysql_fetch_array($rs);

		$goSQL="SELECT decode('".$thesplit[1]."','uDSDf39ERCS0yJf') AS thepass";
		$rs2=db_query($goSQL);
		$row2=mysql_fetch_array($rs2);


		if(mysql_num_rows($rs)==1 && $row['password']==sha1($row2['thepass'])) {
			$logintry=guard_login($thesplit[0],$row2['thepass']);
		}
	}

}

$guard_username=isset($_SESSION['guard_username']) ? $_SESSION['guard_username'] : "";
$guard_userid=isset($_SESSION['guard_userid']) ? $_SESSION['guard_userid'] : "";
$guard_groups=isset($_SESSION['guard_groups']) ? $_SESSION['guard_groups'] : "";
$guard_accesslevel=isset($_SESSION['guard_accesslevel']) ? $_SESSION['guard_accesslevel'] : "";
$guard_active=isset($_SESSION['guard_active']) ? $_SESSION['guard_active'] : "";
$guard_datecreated=isset($_SESSION['guard_datecreated']) ? $_SESSION['guard_datecreated'] : "";
$guard_dateexpires=isset($_SESSION['guard_dateexpires']) ? $_SESSION['guard_dateexpires'] : "";

?>

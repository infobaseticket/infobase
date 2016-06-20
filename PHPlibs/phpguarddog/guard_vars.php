<?php

	/*

		Below you will need to edit some variables.

		We need to know the address of your MySQL host, the name

		of the database you will be using, and the username and

		password to run under.

		Set $guard_db_host equal to the MySQL address

		Set $guard_db_user equal to the username to access the MySQL database

		Set $guard_db_pass equal to the password for the above username

		Set $guard_db_name equal to the name of a MySQL database

		Put the values between the double quotes, ie "" becomes "mydbname"

		See examples below.

	*/



	$guard_db_host="localhost";
	$guard_db_user="root";
	$guard_db_pass="";
	$guard_db_name="guarddog_typo";


	/*

		DO NOT EDIT THE VARIABLES BELOW UNLESS INSTRUCTED BY PHP GUARD DOG SUPPORT.

	*/

	$guard_runbackslash=1;



	if(isset($_SERVER['REMOTE_ADDR']))

		$guard_theremoteaddr=$_SERVER['REMOTE_ADDR'];

	else

		$guard_theremoteaddr=$_ENV['REMOTE_ADDR'];



	if(isset($_SERVER['HTTP_USER_AGENT']))

		$guard_browsertype=$_SERVER['HTTP_USER_AGENT'];

	else

		$guard_browsertype=$_ENV['HTTP_USER_AGENT'];



	if(isset($_SERVER['PHP_SELF'])){

		//echo $config['sitepath_url_bsds'];
		$pos = strrpos($_SERVER['PHP_SELF'], "bsds");
		if ($pos === false) { // note: three equal signs
		    $guard_thescriptname=$_SERVER['PHP_SELF'];
		}else{
			 $guard_thescriptname=$config['sitepath_url_bsds'];
		}

	}elseif(isset($_SERVER['SCRIPT_NAME'])){

		$guard_thescriptname=$_SERVER['SCRIPT_NAME'];

	}elseif(isset($_ENV['PHP_SELF'])){

		$guard_thescriptname=$_ENV['PHP_SELF'];

	}elseif(isset($_ENV['SCRIPT_NAME'])){

		$guard_thescriptname=$_ENV['SCRIPT_NAME'];
	}




	$guard_debug=1;

?>

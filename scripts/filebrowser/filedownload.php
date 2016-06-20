<?php
//This application is developed by www.webinfopedia.com
//visit www.webinfopedia.com for PHP,Mysql,html5 and Designing tutorials for FREE!!!
function output_file($file, $name, $extension)
{
	 /*
	 This function takes a path to a file to output ($file),  the filename that the browser will see ($name) and  the MIME type of the file ($mime_type, optional).
	 */
	 //Check the file premission
	//echo $file;
	 if (!file_exists($file)) die('<br>File not found on location!');
	 if(!is_readable($file)) die('<br>File inaccessible!');
 


	 $size = filesize($file);
	 //$name = rawurldecode($file);
	 
	 /* Figure out the MIME type | Check in array */
	 $known_mime_types=array(
	 	"pdf" => "application/pdf",
	 	"txt" => "text/plain",
	 	"html" => "text/html",
	 	"htm" => "text/html",
		"exe" => "application/octet-stream",
		"zip" => "application/zip",
		"doc" => "application/msword",
		"docx" => "application/msword",
		"xls" => "application/vnd.ms-excel",
		"xlsx" => "application/vnd.ms-excel",
		"ppt" => "application/vnd.ms-powerpoint",
		"pptx" => "application/vnd.ms-powerpoint",
		"gif" => "image/gif",
		"png" => "image/png",
		"jpeg"=> "image/jpg",
		"jpg" => "image/jpg",
		"php" => "text/plain"
	 );

	if ($extension!=''){
		$file_extension=$extension;
	}else{
		$file_extension = strtolower(substr(strrchr($file,"."),1));
	}
	 
	 if(array_key_exists($file_extension, $known_mime_types)){
		$mime_type=$known_mime_types[$file_extension];
	 } else {
		$mime_type="application/force-download";
	 };
	 
	 echo $name;

	 //turn off output buffering to decrease cpu usage
	 @ob_end_clean(); 
	 
	 // required for IE, otherwise Content-Disposition may be ignored
	 if(ini_get('zlib.output_compression'))
	  ini_set('zlib.output_compression', 'Off');
	 
	 header('Content-Type: ' . $mime_type);
	 header('Content-Disposition: attachment; filename="'.$name.'"');
	 header("Content-Transfer-Encoding: binary");
	 header('Accept-Ranges: bytes');
	 
	 /* The three lines below basically make the 
	    download non-cacheable */
	 header("Cache-control: private");
	 header('Pragma: private');
	 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	 
	 // multipart-download and download resuming support
	 if(isset($_SERVER['HTTP_RANGE']))
	 {
		list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
		list($range) = explode(",",$range,2);
		list($range, $range_end) = explode("-", $range);
		$range=intval($range);
		if(!$range_end) {
			$range_end=$size-1;
		} else {
			$range_end=intval($range_end);
		}
		/*
		------------------------------------------------------------------------------------------------------
		//This application is developed by www.webinfopedia.com
		//visit www.webinfopedia.com for PHP,Mysql,html5 and Designing tutorials for FREE!!!
		------------------------------------------------------------------------------------------------------
	 	*/
		$new_length = $range_end-$range+1;
		header("HTTP/1.1 206 Partial Content");
		header("Content-Length: $new_length");
		header("Content-Range: bytes $range-$range_end/$size");
	 } else {
		$new_length=$size;
		header("Content-Length: ".$size);
	 }
	 
	 /* Will output the file itself */
	 $chunksize = 1*(1024*1024); //you may want to change this
	 $bytes_send = 0;
	 if ($file = fopen($file, 'r'))
	 {
		if(isset($_SERVER['HTTP_RANGE']))
		fseek($file, $range);
	 
		while(!feof($file) && 
			(!connection_aborted()) && 
			($bytes_send<$new_length)
		      )
		{
			$buffer = fread($file, $chunksize);
			print($buffer); //echo($buffer); // can also possible
			flush();
			$bytes_send += strlen($buffer);
		}
	 fclose($file);
	 } else
	 //If no permissiion
	 die('Error - can not open file.');
	 //die
	die();
}
//Set the time out
set_time_limit(0);

//path to the file


$file=urldecode($_REQUEST['file']);
$name=urldecode($_REQUEST['name']);
$extension=urldecode($_REQUEST['extension']);

//echo $file;
//Call the download function with file path,file name and file type
output_file($file,$name, $extension);

?>
<?
function conttype($my_file){
	$ext = substr(strrchr($my_file, '.'), 1);
	return $ext;
}
 function getFileList($dir) {
	 # array to hold return value
	 $retval = array();
	 # add trailing slash if missing
	 if(substr($dir, -1) != "/") $dir .= "/";
	 # open pointer to directory and read list of files

	 if (file_exists($dir)){
		 $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
		 while(false !== ($entry = $d->read())) {
		 	# skip hidden files
		 	if($entry[0] == ".") continue;
				if(is_dir("$dir$entry")) {
					$retval[] = array(
						"name" => "$entry",
						"dir" => "$dir/",
						"type" => strtolower( filetype("$dir$entry")),
						"size" => 0,
						"lastmod" => filemtime("$dir$entry")
						);
				} elseif(is_readable("$dir$entry")) {
					$retval[] = array(
						"name" => "$entry",
						"dir" => "$dir",
						"type" =>  strtolower(conttype("$dir$entry")),
						"size" => filesize("$dir$entry"),
						"lastmod" => filemtime("$dir$entry") );
				}
		}

	$d->close();

	return $retval;
	}
 }
 ?>
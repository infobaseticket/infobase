<?php


if ($N1_PRO=='ELIA' or $N1_PRO=='BEL' or $N1_PRO=='BGC' or $N1_PRO=='PROX' or $N1_PRO=='MOBI' or $N1_PRO=='ASTRID'
 or $N1_PRO=='DE LIJN' or $N1_PRO=='FRAMELIA' or $N1_PRO=='FRAMPROX-P' ){
  $STAB_received=5;  
  $STAB_received_filename="<b>".$N1_PRO." site <br> Stab by FRAME PARTNER</b>";
}

//VALIDATION RULES
$query="SELECT * FROM VALIDATION_FILES ORDER BY CHECKORDER ASC";
$stmtval = parse_exec_fetch($conn_Infobase, $query, $error_str, $resval);
if (!$stmtval){
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmtval);
    $amount_rules=count($resval['FILENAME']);
}


if (file_exists($directory)){

    $folderexists='yes';

	$folder_out.= '<span class="label label-success">Folder searched: </b>'.$directory.'</span><br>';
	$sysdate=strtotime(date('d-M-Y'));
	$funddate=strtotime(str_replace("/", "-",  $res3['AU353'][0]));	


    $funddate_minusweek=strtotime(str_replace("/", "-", $res3['AU353'][0]).' -7 days');  

	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
	while($it->valid()) {
	    if (!$it->isDot() && !$it->isDir() && escape_sq($it->key())!='Thumbs.db') {
	    		
	    	$found='';
            $filetype='';

       		if (strpos($it->getSubPath(), 'Acq')==='false'){ //We don't wan't the files inside acquisition folder
       			$file='acq';
       		}else{
       			$file='con';
       		}

       		$ext = pathinfo($it->key(), PATHINFO_EXTENSION); 
            $filename = pathinfo($it->key(), PATHINFO_FILENAME);
            $filenameExt = pathinfo($it->key(), PATHINFO_BASENAME);
            $filelocationWithFilename=strtoupper($it->getSubPathName());
            $filelocation=$it->getSubPath();
            $fullpath=$it->key();        

       		if (strtoupper(substr($filename,-11,11))=="ALL CLEARED"){
       			$day_filedate=substr($filename,-20,2); 
	       		$month_filedate=substr($filename,-18,2); 	
	       		$year_filedate=substr($filename,-16,4);
                $filedate2=$day_filedate."-".$month_filedate."-".$year_filedate;

       			if (is_numeric(substr($filename,-20,8)) && $day_filedate>=1 && $day_filedate<32 && $year_filedate>1900 && $year_filedate<2099 && $month_filedate>=1 && $month_filedate<13 && is_numeric($year_filedate) && is_numeric($month_filedate) && is_numeric($day_filedate)){

                    if (validateDate($day_filedate,$month_filedate,$year_filedate)){
	       				$filedate2=$day_filedate."-".$month_filedate."-".$year_filedate;
		       			$filedate=strtotime($filedate2);
		       			$file_date_check=true;
                        $filedate3=$filedate2;
	       			}else{
	       				$file_date_check=false;
	       				$filedate= $filedate2;
	       				$filedate2='';
                        $filedate3='';
	       			}
	       			
	       		}else{
	       			$file_date_check=false;
	       			$filedate=$filedate2;
	       			$filedate2='';
                    $filedate3='';
	       		}
            }else if (substr($filename,-2,2)=="_A"){

                $day_filedate=substr($filename,-10,2); 
                $month_filedate=substr($filename,-8,2);    
                $year_filedate=substr($filename,-6,4);

                if (is_numeric(substr($filename,-10,8)) && $day_filedate>=1 && $day_filedate<32 && $year_filedate>1900 && $year_filedate<2099 && $month_filedate>=1 && $month_filedate<13 && is_numeric($year_filedate) && is_numeric($month_filedate) && is_numeric($day_filedate)){
                    if (validateDate($day_filedate,$month_filedate,$year_filedate)){
                        $filedate2=$day_filedate."-".$month_filedate."-".$year_filedate;
                        $filedate=strtotime($filedate2);
                        $file_date_check=true;
                        $filedate3=$filedate2;
                    }else{
                        $file_date_check=false;
                        $filedate='';
                        $filedate2='';
                        $filedate3='';
                    }
                    
                }else{
                    $file_date_check=false;
                    $filedate='';
                    $filedate2='';
                    $filedate3='';
                }
       		}else{
                //FORMAT: 31062015
       			$day_filedate=substr($filename,-8,2); 
	       		$month_filedate=substr($filename,-6,2); 	
	       		$year_filedate=substr($filename,-4,4);
                 //FORMAT: 20150631
                $day_filedate2=substr($filename,-2,2); 
                $month_filedate2=substr($filename,-4,2);     
                $year_filedate2=substr($filename,-8,4);

                //FORMAT: 31062015
       			if (is_numeric(substr($filename,-8,8)) && $day_filedate>=1 && $day_filedate<32 && $year_filedate>1900 && $year_filedate<2099 && $month_filedate>=1 && $month_filedate<13 && is_numeric($year_filedate) && is_numeric($month_filedate) && is_numeric($day_filedate)){
	       			if (validateDate($day_filedate,$month_filedate,$year_filedate)){
		       			$filedate2=$day_filedate."-".$month_filedate."-".$year_filedate;
		       			$filedate=strtotime($filedate2);
		        		$filedate5y=strtotime($day_filedate."-".$month_filedate."-".$year_filedate+5);
						$filedate1y=strtotime($day_filedate."-".$month_filedate."-".$year_filedate+1);
						$file_date_check=true;
                        $filedate3=$filedate2;
					}else{
						$file_date_check=false;
	       				$filedate=substr($filename,-8,8);
	       				$filedate2=substr($filename,-8,8);
                        $filedate3='';
					}
                //FORMAT: 20150631
                }else if (is_numeric(substr($filename,-8,8)) && $day_filedate2>=1 && $day_filedate2<32 && $year_filedate2>1900 && $year_filedate2<2099 && $month_filedate2>=1 && $month_filedate2<13 && is_numeric($year_filedate2) && is_numeric($month_filedate2) && is_numeric($day_filedate2)){
                    if (validateDate($day_filedate2,$month_filedate2,$year_filedate2)){
                        $filedate2=$day_filedate2."-".$month_filedate2."-".$year_filedate2;
                        $filedate=strtotime($filedate2);
                        $filedate5y=strtotime($day_filedate2."-".$month_filedate2."-".$year_filedate2+5);
                        $filedate1y=strtotime($day_filedate2."-".$month_filedate2."-".$year_filedate2+1);
                        $file_date_check=true;
                        $filedate3=$filedate2;
                    }else{
                        $file_date_check=false;
                        $filedate=substr($filename,-8,8);
                        $filedate2=substr($filename,-8,8);
                        $filedate3='';
                    }
	       		}else{
	       			$file_date_check=false;
	       			$filedate=substr($filename,-8,8);
	       			$filedate2=substr($filename,-8,8);
                    $filedate3='';
	       		}
        	}

           
            $match_found='no';

            //echo "<hr>".$filename."<hr>";

            //We check if the file matches of one of the rules
            for ($z = 0; $z <$amount_rules; $z++) {

                $filefound='';

                $filenameabb=$resval['FILENAME'][$z];
                $received=$filenameabb."_received";

                $filetypes=$resval['FILETYPES'][$z]; //FILETYPES CONTAINS THE NAMING CONVENTION ABBREVIATIONS 
                $types=explode(",", $filetypes);


                if ($resval['CANNOTHAVE'][$z]!=''){
                    $nothaves=explode(",", $resval['CANNOTHAVE'][$z]);

                    foreach ($nothaves as $key => $nothave) {
                        if (strpos($filename, $nothave)!==false){
                            continue; 
                        }
                    }
                }

                if (is_array($types)){                               
                    foreach ($types as $key => $type) {
                        if (substr_count($filename, $type)!=0){
                            $filetype=$filenameabb;
                            $filefound=$filenameabb;
                        }                           
                    }   
                }

                if ($match_found!='yes' && $filefound!=''){  
                                   
                    $filedatum=$filenameabb."_filedate";
                    $received_filename=$filenameabb."_received_filename";
                    $received_fullpath=$filenameabb."_received_fullpath";
                    $received_filename=$filenameabb."_received_filename";
                    $received_ran=$filenameabb."_received_ran";                 
                   /*
                    if ($filefound=='C2'){
                        echo $resval['CHECKORDER'][$z];
                    }
                    */
                    if ($resval['FILEDATE_CHECK'][$z]==1 && $file_date_check===false){
                        //echo "date of file is not OK ".$filenameabb."<br>";
                        $$received=4;
                        $$filedatum=$filedate2;
                        break 1;
                    }
                    
                    if ($resval['FUNDDATE_CHECK'][$z]==1 && $filedate<$funddate){
                        //echo "=> OLD funddate issue ".$filenameabb." ($filedate<$funddate)<br>";
                        $$received=2;
                        continue 1;
                    }else if ($resval['FUNDDATE_CHECK'][$z]==2 && $filedate<$funddate_minusweek){ //
                        //echo "funddate minusweek issue ".$filenameabb." $filedate<$funddate_minusweek=<br>";
                        $$received=2;
                        continue 1;
                    }else if ($resval['FUNDDATE_CHECK'][$z]==3 && $sysdate>$funddate5y){
                        //echo "funddate 5year issue ".$filenameabb."<br>";
                        $$received=2;
                        continue 1;
                    }else if ($resval['FUNDDATE_CHECK'][$z]==4 && $sysdate<$funddate1y && strpos($filename, 'EXM')===false){
                        //echo "funddate 1year issue ".$filenameabb."<br>";
                        $$received=2;
                        continue 1;
                    }
                    
                    

                    if ($resval['MUST_END_WITH'][$z]!=''){ //for EWC file
                        $chars=strlen(trim($resval['MUST_END_WITH'][$z]));
                        //echo $chars;
                        if (substr($filename,-$chars,$chars)==trim($resval['MUST_END_WITH'][$z])){
                            //echo "found1 ".$filenameabb;
                            $$received=1;
                            $$received_fullpath=$fullpath;
                            $$received_filename=$filenameExt;
                            $$received_ran=$ran;
                            $match_found='yes';
                        }else{
                            $$received=6;
                            $received_filename=substr($filename,-1,1).' IS NOT A VALID CERTIFICATE';
                        }
                    }else{
                        //echo "found2 ".$filenameabb;
                        $$received=1;
                        $$received_fullpath=$fullpath;
                        $$received_filename=$filenameExt;
                        $$received_ran=$ran;
                        $match_found='yes';
                    }
                    
                    break 1;
                    
                }               
            }
            /*
            //CHECK NOT IN THE DATABASE
        	if ($res1['A34U334'][0]=$res1['A41U341'][0] or $res1['N1_SAC'][0]=='KPNGB' or $res1['N1_SAC'][0]=='BASE'){
        		$SOW_received=1;
                $SOW_received_fullpath=$fullpath;
                $SOW_received_filename=$filenameExt;
                $SOW_received_ran=$ran;
        	}
            */

            //DISPLAY AND INSERT INTO DB

        	$files.= $ran.": ".$filelocationWithFilename. " => <b><font color='blue'>".$filetype."</font></b><br>";

        	if ($insertdb=='yes' && $filename!='Thumbs'){
        		echo '[FILE] '.$filelocationWithFilename." => ".$filetype."\r\n";
                //$md5=md5(escape_sq($it->key()));
                $parts=explode("/",escape_sq($it->getSubPath()));
                $SITEID=$parts[0];
                $CANDIDATE=$parts[1];
                if (strlen($CANDIDATE)!=8){
                    $CANDIDATE="";
                }
                $UPGNR=$parts[2];
                if (substr($UPGNR, 0,2)!='99'){
                    $UPGNR="";
                }
	        	$goSQL2="INSERT INTO RAN_SCAN_TODAY VALUES ('".escape_sq($filelocationWithFilename)."','".escape_sq($it->getSubPath())."','".escape_sq($it->key())."','".escape_sq($filename)."','".$it->getSize()."', '".date('d-m-Y H:i:s',$it->getMTime())."','".date('d-m-Y H:i:s',$it->getATime())."','".date('d-m-Y H:i:s',$it->getCTime())."','".$it->getExtension()."','".$md5."','".$filedate3."','".$filetype."',SYSDATE,'".$partner."','".$SITEID."','".$CANDIDATE."','".$UPGNR."','".$$received."')";
				//echo $goSQL2."\r\n";
                $stmt2 = parse_exec_free($conn_Infobase, $goSQL2, $error_str);
				if (!$stmt2) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
			}else if ($commandline!='no'){
               echo '[FILE] '.$argv[2].": ".$filename." => ".$filetype."\r\n";
            }	       	     
	    }elseif ($it->isDir()){
            if ($insertdb=='yes' && ($partner=='BASE-RAN' or $partner=='M4C_RAN' or $partner=='RAN_BENCH')){
                //echo '[FOLDER]'.$argv[2].": ".$filename." => ".escape_sq($it->getSubPath())."\r\n";
                $goSQL2="INSERT INTO RAN_SCAN_FOLDERS VALUES ('".escape_sq($it->getSubPath())."','".$partner."')";
                //echo $goSQL2."\n----------\n";
                $stmt2 = parse_exec_free($conn_Infobase, $goSQL2, $error_str);
                if (!$stmt2) {
                    die_silently($conn_Infobase, $error_str);
                }else{
                    OCICommit($conn_Infobase);
                }
            }
        }

	    $it->next();
	}
}else{
	$folder_out.= '<span class="label label-danger">Folder "'.$directory.'" not found on RAN!</span><br>';
    $folderexists='no';
}
?>
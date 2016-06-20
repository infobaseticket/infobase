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
	$funddate=strtotime(str_replace("/", "-", $res1['AU353'][0]));	

    $funddate_minusweek=strtotime(str_replace("/", "-", $res1['AU353'][0]).' -7 days');  

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
                //FORMAT: 31062015
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

if (substr_count($guard_groups, 'Admin')==1 ){
           
            //echo "<hr>".$filename."<hr>";
            
            for ($z = 0; $z <$amount_rules; $z++) {

                $filefound='';

                $filenameabb=$resval['FILENAME'][$z];
                $received=$filenameabb."_received";
                $filetypes=$resval['FILETYPES'][$z];
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

                if ($$received!=1 && $filefound!=''){  
                                   
                    $filedate=$filenameabb."_filedate";
                    $received_filename=$filenameabb."_received_filename";
                    $received_fullpath=$filenameabb."_received_fullpath";
                    $received_filename=$filenameabb."_received_filename";
                    $received_ran=$filenameabb."_received_ran";                 
                   

                    
                    if ($resval['FILEDATE_CHECK'][$z]==1 && $file_date_check===false){
                        //echo "date of file is not OK ".$filenameabb."<br>";
                        $$received=4;
                        $$filedate=$filedate2;
                        break 1;
                    }

                    if ($resval['FUNDDATE_CHECK'][$z]==1 && $filedate<$funddate===false){
                        //echo "funddate issue ".$filenameabb."<br>";
                        $$received=2;
                        continue 1;
                    }else if ($resval['FUNDDATE_CHECK'][$z]==2 && $filedate<$funddate_minusweek===false){ //
                        //echo "funddate minusweek issue ".$filenameabb."<br>";
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
                    }
                    
                    break 1;
                    
                }
    
               
            }
}

	
if (substr_count($guard_groups, 'Admin')!=1 ){
            if (strpos($filename, 'EPEX')!==false or strpos($filename, 'EPX')!==false or strpos($filename, 'EPER')!==false or strpos($filename, 'EP3')!==false OR strpos($filename, 'EIR')!==false OR strpos($filename, 'EPA')!==false){
        		if ($EP_received!=1){
                    if ($file_date_check===false){
            			$EP_received=4;
            			$EP_filedate=$filedate2;

            		}else if ($filedate<$funddate){
            			$EP_received=2;
            		}else{
            			$EP_received=1;
                        $EP_received_fullpath=$fullpath;
                        $EP_received_filename=$filenameExt;
                        $EP_received_ran=$ran;
            		}
                    $found='EP';
                }
                
                $filetype='EP';
               
        		
        	}else if (strpos($filename, 'ISSEP')!==false or strpos($filename, 'LNE')!==false or strpos($filename, 'IBGE')!==false){
        		if ($ISSEP_received!=1){
                    $ISSEP_received=1;
                    $ISSEP_received_fullpath=$fullpath;
                    $ISSEP_received_filename=$filenameExt;
            		$ISSEP_file=$file;
                    $ISSEP_received_ran=$ran;
            		$found='Radiation Hazard';
                }
                    
                $filetype='Radiation Hazard';
                
        	}else if (strpos($filename, 'KOR')!==false){
        		if ($KOR_received!=1){
                    if ($file_date_check===false){
            			$KOR_received=4;
            			$KOR_filedate=$filedate2;
            		}else if ($filedate<$funddate_minusweek){
            			$KOR_received=2;
            		}else{
            			$KOR_received=1;
                        $KOR_received_fullpath=$fullpath;
                        $KOR_received_filename=$filenameExt;
                        $KOR_received_ran=$ran;
            		}
            		$found='KOR';
                }else{
                    $filetype='KOR';
                }
            }else if ((strpos($filename, 'ABDD')!==false or (strpos($filename, 'AB')!==false && strpos($filename, 'STAB')==false)) && (strtolower($it->getExtension())=='pdf' or strtolower($it->getExtension())=='doc' or strtolower($it->getExtension())=='docx')){
                if($AB_received!=1){
                    if ($file_date_check===false){
                        $AB_received=4;
                        $AB_filedate=$filedate2;
                    }else if ($filedate<$funddate){
                        $AB_received=2;
                    }else{
                        $AB_received=1;
                        $AB_received_fullpath=$fullpath;
                        $AB_received_filename=$filenameExt;
                        $AB_received_ran=$ran;
                    }
                    $found='AB';
                }
                
                $filetype='AB';
        	}else if (strpos($filename, 'CDWG')!==false or strpos($filename, 'DD')!==false && (strtolower($it->getExtension())=='pdf' or strtolower($it->getExtension())=='doc' or strtolower($it->getExtension())==='docx') && strpos($filename, 'ADDENDUM')===false && strpos($filename, 'ABDD')===false){
        		if ($CDWG_received!=1){
                    if ($file_date_check===false){
            			$CDWG_received=4;
            			$CDWG_filedate=$filedate2;
            		}else if ($filedate<$funddate){
            			$CDWG_received=2;
            		}else{
            			$CDWG_received=1;
                        $CDWG_received_fullpath=$fullpath;
                        $CDWG_received_filename=$filenameExt;
                        $CDWG_received_ran=$ran;
            		}
            		$found='CDWG';
                }
                     
                $filetype='CDWG';
            }else if (strpos($filename, 'STABNM')!==false && (strtolower($it->getExtension())=='pdf' or strtolower($it->getExtension())=='doc' or strtolower($it->getExtension())=='docx')){
                if ($STAB_received!=1){
                    if ($file_date_check===false){
                        $STABNMreceived=4;
                        $STABNM_filedate=$filedate2;
                    }else if ($filedate<$funddate){ //stability study
                        $STABNM_received=2;
                    }else{
                        $STABNM_received=1;
                        $STABNM_received_fullpath=$fullpath;
                        $STABNM_received_filename=$filenameExt;
                        $STABNM_received_ran=$ran;
                    }
                    $found='STABNM';
                }
                
                $filetype='STABNM';
                  
        	}else if (strpos($filename, 'STAB')!==false && (strtolower($it->getExtension())=='pdf' or strtolower($it->getExtension())=='doc' or strtolower($it->getExtension())=='docx')){
            	if ($STAB_received!=1){
                	if ($file_date_check===false){
            			$STAB_received=4;
            			$STAB_filedate=$filedate2;
            		}else if ($filedate<$funddate){ //stability study
            			$STAB_received=2;
            		}else{
            			$STAB_received=1;
                        $STAB_received_fullpath=$fullpath;
                        $STAB_received_filename=$filenameExt;
                        $STAB_received_ran=$ran;
            		}
            		$found='STAB';
                }
                
                $filetype='STAB';
                
        	}else if (strpos($filename, 'SOW')!==false){
                if($SOW_received!=1){
            		$SOW_received=1;
            		$SOW_file=$file;
                    $SOW_received_fullpath=$fullpath;
                    $SOW_received_filename=$filenameExt;
                    $SOW_received_ran=$ran;
                    $found='SOW';
                }
                    
                $filetype='SOW';
                
        
                
        	}else if ((strpos($filename, 'ABDD')!==false or (strpos($filename, 'AB')!==false && strpos($filename, 'STAB')==false)) && (strtolower($it->getExtension())=='dwg' or strtolower($it->getExtension())=='zip')){
            	if ($ABDWG_received!=1){
                	if ($file_date_check===false){
            			$ABDWG_received=4;
            			$ABDWG_filedate=$filedate2;
            		}else if ($filedate<$funddate){
            			$ABDWG_received=2;
            		}else{
            			$ABDWG_received=1;
                        $ABDWG_received_fullpath=$fullpath;
                        $ABDWG_received_filename=$filenameExt;
                        $ABDWG_received_ran=$ran;
            		}
            		$found='ABDWG';
                }
                     
                $filetype='ABDWG';
                
        	}else if (strpos($filename, 'PIF')!==false){
            	if($PIF_received!=1){	
                    if ($file_date_check===false){
            			$PIF_received=4;
            			$PIF_filedate=$filedate2;
            		}else if ($filedate<$funddate){
            			$PIF_received=2;
            		}else{
            			$PIF_received=1;
                        $PIF_received_fullpath=$fullpath;
                        $PIF_received_filename=$filenameExt;
                        $PIF_received_ran=$ran;
            		}
            		$found='PIF';
                }
                     
                $filetype='PIF';
                
        	}else if (strpos($filename, 'HSP')!==false or strpos($filename, 'SAFPLAN')!==false){
            	if ($HSP_received!=1){
                	if ($file_date_check===false){
            			$HSP_received=4;
            			$HSP_filedate=$filedate2;
            		}else if ($filedate<$funddate){
            			$HSP_received=2;
            		}else{
            			$HSP_received=1;
                        $HSP_received_fullpath=$fullpath;
                        $HSP_received_filename=$filenameExt;
                        $HSP_received_ran=$ran;
            		}
            		$found='HSP';
                }
                    
                $filetype='HSP';
                
        	}else if ( strpos($filename, 'CJ')!==false or strpos($filename, 'SAFJOUR')!==false){
        		if ($CJ_received!=1){
                    $CJ_received=1;
            		if ($file_date_check===false){
            			$CJ_received=4;
            			$CJ_filedate=$filedate2;
            		}else if ($filedate<$funddate){
            			$CJ_received=2;
            		}else{
            			$CJ_received=1;
                        $CJ_received_fullpath=$fullpath;
                        $CJ_received_filename=$filenameExt;
                        $CJ_received_ran=$ran;
            		}
            		$found='CJ';
                }
                   
                $filetype='CJ';
            
        	}else if ((strpos($filename, 'ASEC')!==false or strpos($filename, 'YSEC')!==false  or strpos($filename, 'STC')!==false)){
                if ($AYSEC_received!=1){
                    if ((substr($filename,-2,2)=="_A" && (strpos($filename, 'ASEC')!==false or strpos($filename, 'YSEC')!==false)) or strpos($filename, 'STC')!==false){
                        
                        if ($file_date_check===false){
                			$AYSEC_received=4;
                			$AYSEC_filedate=$filedate2;
                		}else if ($sysdate<$funddate1y && strpos($filename, 'EXM')===false){ //Not applicable for exemption files (EXM)
                			$AYSEC_received=2;
                		}else{
                			$AYSEC_received=1;
                            $AYSEC_received_fullpath=$fullpath;
                            $AYSEC_received_filename=$filenameExt;
                            $AYSEC_received_ran=$ran;
                		}
                    }else{
                        $AYSEC_received=5;
                        $AYSEC_received_filename=substr($filename,-2,2).' IS A WRONG CERTIFICATE';
                    }
                   
            		$found='AYSEC';
                }
                $filetype='AYSEC';
                
        	}else if (strpos($filename, 'EWC')!==false && (strtolower($it->getExtension())=='pdf' or strtolower($it->getExtension())=='doc' or strtolower($it->getExtension())=='docx')){
            	if($EWC_received!=1){	
                    if (substr($filename,-2,2)=="_A"){
                        $EWC_received=1;
                        $EWC_received_fullpath=$fullpath;
                        $EWC_received_filename=$filenameExt;
                        $EWC_received_ran=$ran;
                    }else{ //END LETTER IS NOT VALID
                        $EWC_received=6;
                        $EWC_received_filename=substr($filename,-1,1).' IS NOT A VALID CERTIFICATE';
                    }
                    $found='EWC';
                }
                $filetype='EWC';
                
        	}else if (strpos($filename, 'EWC')!==false && strtolower($it->getExtension())=='dwg'){	        		
            	if ($EWCDWG_received!=1){	
                    if ($file_date_check===false){
            			$EWCDWG_received=4;
            			$EWCDWG_filedate=$filedate2;
            		}else if ($sysdate>$funddate5y){
            			$EWCDWG_received=2;
            		}else{
            			$EWCDWG_received=1;
                        $EWCDWG_received_fullpath=$fullpath;
                        $EWCDWG_received_filename=$filenameExt;
                        $EWCDWG_received_ran=$ran;
            		}
            		$found='EWCDWG';
                }
                $filetype='EWCDWG';
                
        	}else if (strpos($filename, 'C2')!==false && (strtolower($it->getExtension())=='msg' or strtolower($it->getExtension())=='xls' or strtolower($it->getExtension())=='xlsx' or strtolower($it->getExtension())=='xlsm')){
                if ($C2_received!=1){
            		if ($file_date_check===false){
            			$C2_received=4;
            			$C2_filedate=$filedate;
                    }else if (strpos(strtoupper($filename), 'ALL CLEARED')===false && strpos(strtoupper($filename), 'CLEARANCE')===false){
                        $C2_received=3;
            		}else if ($filedate<$funddate){
            			$C2_received=2;
            		}else{
            			$C2_received=1;
                        $C2_received_fullpath=$fullpath;
                        $C2_received_filename=$filenameExt;
                        $C2_received_ran=$ran;
            		}
                    $found='C2';
                }
                $filetype='C2';
                
        	}else if (strpos($filename, 'C3')!==false && (strtolower($it->getExtension())=='msg' or strtolower($it->getExtension())=='xls' or strtolower($it->getExtension())=='xlsx' or strtolower($it->getExtension())=='xlsm')){
            	if ($C3_received!=1){	
                    if ($file_date_check===false){
            			$C3_received=4;
            			$C3_filedate=$filedate2;
            		}else if ($filedate<$funddate){
            			$C3_received=2;
            		}else{
            			$C3_received=1;
                        $C3_received_fullpath=$fullpath;
                        $C3_received_filename=$filenameExt;
                        $C3_received_ran=$ran;
            		}
            		$found='C3';
                }
                $filetype='C3';
                
        	}else if (strpos($filename, 'SOPOUT')!==false){
            	if ($SOPOUT_received!=1){	
                    if ($file_date_check===false){
            			$SOPOUT_received=4;
            			$SOPOUT_filedate=$filedate2;
            		}else if ($filedate<$funddate){
            			$SOPOUT_received=2;
            		}else{
            			$SOPOUT_received=1;
                        $SOPOUT_received_fullpath=$fullpath;
                        $SOPOUT_received_filename=$filenameExt;
                        $SOPOUT_received_ran=$ran;
            		}
            		$found='SOPOUT';
                }
                $filetype='SOPOUT';
                
        	}else if (strpos($filename, 'LEASE')!==false){
            	if ($LEASE_received!=1){ 
                	if ($file_date_check===false){
            			$LEASE_received=4;
            			$LEASE_filedate=$filedate2;
            		}else{
            			$LEASE_received=1;
                        $LEASE_received_fullpath=$fullpath;
                        $LEASE_received_filename=$filenameExt;
                        $LEASE_received_ran=$ran;
            		}	        		
            		$found='LEASE';
                }
                $filetype='LEASE';
                
        	}else if (strpos($filename, 'BPER')!==false){
            	if ($BPER_received!=1){ 
                	if ($file_date_check===false){
            			$BPER_received=4;
            			$BPER_filedate=$filedate2;
            		}else{
            			$BPER_received=1;
                        $BPER_received_fullpath=$fullpath;
                        $BPER_received_filename=$filenameExt;
                        $BPER_received_ran=$ran;
            		}	        		
            		$found='BPER';
                }
                $filetype='BPER';
                
        	}else if (strpos($filename, 'BPEX')!==false){
            	if ($BPEX_received!=1){ 
                	if ($file_date_check===false){
            			$BPEX_received=4;
            			$BPEX_filedate=$filedate2;
            		}else{
            			$BPEX_received=1;
                        $BPEX_received_fullpath=$fullpath;
                        $BPEX_received_filename=$filenameExt;
                        $BPEX_received_ran=$ran;
            		}
            		$found='BPEX';
                }
                $filetype='BPEX';
                
        	}else if (strpos($filename, 'BPX')!==false){
            	if ($BPX_received!=1){ 
                	if ($file_date_check===false){
            			$BPX_received=4;
            			$BPX_filedate=$filedate2;
            		}else{
            			$BPX_received=1;
                        $BPX_received_fullpath=$fullpath;
                        $BPX_received_filename=$filenameExt;
                        $BPX_received_ran=$ran;
            		}
            		$found='BPX';
                }
                $filetype='BPX';
                
        	}else if (strpos($filename, 'BP')!==false){
            	if ($BP_received!=1 ){ 
                	if ($file_date_check===false){
            			$BP_received=4;
            			$BP_filedate=$filedate2;
            		}else{
            			$BP_received=1;
                        $BP_received_fullpath=$fullpath;
                        $BP_received_filename=$filenameExt;
                        $BP_received_ran=$ran;
            		}
            		$found='BP';
                }
                $filetype='BP';
                
        	}else if (strpos($filename, 'LDS')!==false){
            	if ($LDS_received!=1){ 
                	if ($file_date_check===false){
            			$LDS_received=4;
            			$LDS_filedate=$filedate2;
            		}else{
            			$LDS_received=1;
                        $LDS_received_fullpath=$fullpath;
                        $LDS_received_filename=$filenameExt;
                        $LDS_received_ran=$ran;
            		}
            		$found='LDS';
                }
                $filetype='LDS';
                
        	}else if (strpos($filename, 'LMS')!==false){
            	if ($LMS_received!=1){ 
                	if ($file_date_check===false){
            			$LMS_received=4;
            			$LMS_filedate=$filedate2;
            		}else{
            			$LMS_received=1;
                        $LMS_received_fullpath=$fullpath;
                        $LMS_received_filename=$filenameExt;
                        $LMS_received_ran=$ran;
            		}
            		$found='LMS';
                }
                $filetype='LMS';
                
        	}else if ( strpos($filename, 'Addendum')!==false){
            	if ($Addendum_received!=1){ 
                	if ($file_date_check===false){
            			$Addendum_received=4;
            			$Addendum_filedate=$filedate2;
            		}else{
            			$Addendum_received=1;
                        $Addendum_received_fullpath=$fullpath;
                        $Addendum_received_filename=$filenameExt;
                        $Addendum_received_ran=$ran;
            		}
            		$found='ADDENDUM';
                }
                $filetype='ADDENDUM';
                
        	}else if (strpos($filename, 'SLEASE')!==false){
            	if ($SLEASE_received!=1){ 
                	if ($file_date_check===false){
            			$SLEASE_received=4;
            			$SLEASE_filedate=$filedate2;
            		}else{
            			$SLEASE_received=1;
                        $LEASE_received_fullpath=$fullpath;
                        $LEASE_received_filename=$filenameExt;
                        $LEASE_received_ran=$ran;
            		}
            		$found='SLEASE';
                }
                $filetype='SLEASE';
                
        	}else if (strpos($filename, 'LSigned')!==false){
            	if ($LSigned_received!=1){ 
                	if ($file_date_check===false){
            			$LSigned_received=4;
            			$LSigned_filedate=$filedate2;
            		}else{
            			$LSigned_received=1;
                        $LSigned_received_fullpath=$fullpath;
                        $LSigned_received_filename=$filenameExt;
                        $LSigned_received_ran=$ran;
            		}
            		$found='LSigned';
                }
                $filetype='LSigned';
                
        	}else if (strpos($filename, 'LS')!==false){
            	if ($LS_received!=1){ 
                	if ($file_date_check===false){
            			$LS_received=4;
            			$LS_filedate=$filedate2;
            		}else{
            			$LS_received=1;
                        $LS_received_fullpath=$fullpath;
                        $LS_received_filename=$filenameExt;
                        $LS_received_ran=$ran;
            		}
            		$found='LS';
                }
                $filetype='LS';
                
        	}else if (strpos($filename, 'TRR')!==false){
            	if ($TRR_received!=1){ 
                	if ($file_date_check===false){
            			$TRR_received=4;
            			$TRR_filedate=$filedate2;
            		}else{
            			$TRR_received=1;
                        $TRR_received_fullpath=$fullpath;
                        $TRR_received_filename=$filenameExt;
                        $TRR_received_ran=$ran;
            		}
            		$found='TRR';
                }
                $filetype='TRR';
                
        	}

}
        	if ($res1['A34U334'][0]=$res1['A41U341'][0] or $res1['N1_SAC'][0]=='KPNGB' ){
        		$SOW_received=1;
                $SOW_received_fullpath=$fullpath;
                $SOW_received_filename=$filenameExt;
                $SOW_received_ran=$ran;
        	}
        	//echo $filelocationWithFilename . "$C2_received\r\n<br>";
        	$files.= $ran.": ".$filelocationWithFilename. " => <b><font color='blue'>".$filetype."</font></b><br>";

        	if ($insertdb=='yes' && $filename!='Thumbs'){
        		echo  $filename." => ".$filetype."\r\n";
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
	        	$goSQL2="INSERT INTO RAN_SCAN_TODAY VALUES ('".escape_sq($filelocationWithFilename)."','".escape_sq($it->getSubPath())."','".escape_sq($it->key())."','".escape_sq($filename)."','".$it->getSize()."', '".date('d-m-Y H:i:s',$it->getMTime())."','".date('d-m-Y H:i:s',$it->getATime())."','".date('d-m-Y H:i:s',$it->getCTime())."','".$it->getExtension()."','".$md5."','".$filedate3."','".$filetype."',SYSDATE,'".$partner."','".$SITEID."','".$CANDIDATE."','".$UPGNR."')";
				//echo $goSQL2."\n----------\n";
                $stmt2 = parse_exec_free($conn_Infobase, $goSQL2, $error_str);
				if (!$stmt2) {
					die_silently($conn_Infobase, $error_str);
				}else{
					OCICommit($conn_Infobase);
				}
			}else if ($commandline!='no'){
               echo  $filename." => ".$filetype."\r\n";
            }
	       	     
	    }

	    $it->next();
	}
}else{
	$folder_out.= '<span class="label label-danger">Folder "'.$directory.'" not found on RAN!</span><br>';
    $folderexists='no';
}
?>
#!/usr/bin/php
<?PHP
include("/var/www/html/include/config.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
include($config['sitepath_abs']."/bsds2/scripts/procedures/cur_plan_procedures.php");
//error_reporting(E_ALL);

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$conn_mysql = mysql_connect("$mysql_host", "$mysql_user", "$mysql_password");

function get_technocombination($band,$worname){
	$band=trim($band);
	//echo $band."<br>";
	if ($band=="EG6"){
		$band="EGS";
	}
	if ($band=="UMT6"){
		$band="UMTS";
	}
	if ($band=="900"){
		$band="EGS";
	}
	if ($band=="1800"){
		$band="DCS";
	}
	if ($band=="1800&900"){
		$band="DCS:EGS";
	}
	if ($band=="900&1800"){
		$band="EGS:DCS";
	}
	if ($band=="1800&900&UMTS"){
		$band="DCS:EGS:UMTS";
	}
	if ($band=="900&1800&UMTS"){
		$band="EGS:DCS:UMTS";
	}
					
	if ($worname!=""){
		$part_of_string="56";
	
		$string=strtoupper(substr(trim($worname),0,$part_of_string));
		unset($techs);
			
		$j=0;
	
		$pos_EGSM = strpos($string, "EGS");
		if ($pos_EGSM !== false && $band!="EGS"){
			$techs[$j]="EGS";
			$j++;
	
		}
		$pos_DCS = strpos($string, "DCS");
	
		if ($pos_DCS !== false && $band!="DCS"){
			$techs[$j]="DCS";
			$j++;
		}
		$pos_UMTS = strpos($string, "UMTS");
		if ($pos_UMTS !== false && $band!="UMTS"){
			$techs[$j]="UMTS";
			$j++;
		}
	
		$pos_HSDPA = strpos($string, "HSDPA");
		if ($pos_HSDPA !== false && $band!="HSDPA"){
			$techs[$j]="HSDPA";
			$j++;
		}
	
		if ($band!="CAB" && $band!="ASC" && $band!="ANT"  && $band!="COMB" && $band!="DUAL"){
			//echo "**** REST ***<br>";
			$techs[$j]=$band;
			$j++;
		}
		
		if (count($techs)!=0){
			foreach ($techs as $techno){
				if ($band!=$techno){
					$band2.="$techno,";
				}
			}
		}
	
		
	}
	
	if ($band2!="") 
	{ 
		$band_both=$band.":".$band2;
		$band_both=substr($band_both,0,-1); //To remove last comma
	}else{
		$band_both=$band;
	}
	if ($band_both=="CAB"){
		$band_both="<font color='red'>NET1 ERROR!</font>";
	}
	return $band_both;
}

function array_filter_multi($input, $filter="", $keepMatches=true) {
        if (!is_array($input))
                return ($input==$filter xor $keepMatches==false) ? $input : false;
        while (list ($key,$value) = @each($input)){
                $res = array_filter_multi($value, $filter,$keepMatches);
                if ($res !== false)
                        $out[$key] = $res;
        }
        return $out;
} 


$_POST['reportnr']=3;

switch ($_POST['reportnr']) {
case "":
	$report_name="Please select report";
    break;
case 1:
	$report_name="Find any BSDSs without saved data in a funded technology";
    break;
case 2:
	$report_name="CABINET, DXU, BBS";
    break;
case 3:
	$report_name="TMA, FREQUENCY, TRU, TRUTYPE, ANTENNA TYPE";
    break;	
case 4:
	$report_name="UMTS/HSDPA BSDS with HSTX config information";
    break;			
}


/***************************************************
				UPGRADES
***************************************************/

//echo $_POST['reportnr'];
$reportnr=3;
	
if ($reportnr!=""){
if ($_POST['type']=="Upgrades"){
	$z=0;
	$query1="SELECT SIT_UDK, U353, U571, U305, WOR_NAME, WOR_UDK, WOR_LKP_WCO_CODE FROM infobase.VW_NET1_ALL_UPGRADES 
	WHERE (trim(U353) IS NOT NULL OR trim(U571) IS NOT NULL OR trim(U305) IS NOT NULL)";
	
	if ($reportnr==4){
		$query1.=" AND (BAND ='UMTS' OR BAND='HSDPA' OR WOR_NAME LIKE '%HSDPA%' OR WOR_NAME LIKE '%UMTS%')";
	}
	//echo "<br><br>".$query1;
	
	$stmt1 = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
	if (!$stmt1) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt1);
		$Count1=count($res1['SIT_UDK']);
		//echo $Count1;
		for ($i = 0; $i < $Count1; $i++) {
			
			$data[$z]['TECHNOLOGY']=get_technocombination($res1['WOR_LKP_WCO_CODE'][$i],$res1['WOR_NAME'][$i]);
			
			$pos1="";
			$pos2="";
			$pos3="";
			$pos4="";
			
			$pos1 = strrpos($data[$z]['TECHNOLOGY'], "EGS");
			$pos2 = strrpos($data[$z]['TECHNOLOGY'], "DCS");
			
			$pos3 = strrpos($data[$z]['TECHNOLOGY'], "HSDPA");
			$pos4 = strrpos($data[$z]['TECHNOLOGY'], "UMTS");
			
			
			//echo $data[$z]['TECHNOLOGY'].": EGS $pos1 - DCS $pos2 - HSDPA $pos3 - UMTS $pos4<br>";
					
			if (($pos1 === false && $pos2 === false && $reportnr==2)
			||($pos3 === false && $pos4 === false && $reportnr==4)
			||($pos1 === false && $pos2 === false && $reportnr==3)) {
			
			}else{				
	
			    $data[$z]['SITE_ID']=trim($res1['SIT_UDK'][$i]);
				if ($data[$z]['SITE_ID'][0]=="_"){
					$data[$z]['SITE_ID']=substr($data[$z]['SITE_ID'],1);
				}
				$data[$z]['SITE_FUNDED']=trim($res1['U353'][$i]);
				$data[$z]['BUILD']=trim($res1['U571'][$i]);
				$data[$z]['BSDS_FUNDED']=trim($res1['U305'][$i]);
				$data[$z]['BAND']=$res1['BAND'][$i];
				$data[$z]['WOR_NAME']=$res1['WOR_NAME'][$i];
				$data[$z]['TYPE']="UPGRADE";
				
				
				if ($data[$z]['BUILD']!=""){
					$data[$z]['STATUS']="BSDS AS BUILD";
					$table="BUILD";
				}elseif ($data[$z]['BSDS_FUNDED']!=""){
					$data[$z]['STATUS']="BSDS FUNDED";
					$table="FUND";
				}elseif ($data[$z]['SITE_FUNDED']!=""){
					$data[$z]['STATUS']="SITE FUNDED";
					$table="POST";
				}
				
				$query3 = "Select BSDSKEY FROM INFOBASE.BSDS_SITE_FUNDED WHERE SITEID LIKE '".$data[$z]['SITE_ID']."%'";
				//echo $query3."<br>";
				$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
				if (!$stmt3) {
					die_silently($conn_Infobase, $error_str);
				 	exit;
				} else {
					OCIFreeStatement($stmt3);
					
					if (count($res3['BSDSKEY'])!=0){
						$data[$z]['BSDSKEY']=$res3['BSDSKEY'][0];
					}else{
						$data[$z]['BSDSKEY']="MISSING BSDS!";
					}
				}
				
				
				
				if (count($res3['BSDSKEY'])!=0){
					
					$query3 = "Select SITEKEY FROM INFOBASE.BSDS_GENERALINFO WHERE BSDSKEY = '".$data[$z]['BSDSKEY']."'";
					//echo $query3."<br>";
					$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
					if (!$stmt3) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					} else {
						OCIFreeStatement($stmt3);
						
						if (count($res3['SITEKEY'])!=0){
							$data[$z]['SITEKEY']=$res3['SITEKEY'][0];
						}else{
							$data[$z]['SITEKEY']="MISSING!";
						}
					}
				
					$query6 = "Select MAX(BSDS_BOB_REFRESH) AS BSDS_BOB_REFRESH FROM INFOBASE.BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY ='".$data[$z]['BSDSKEY']."'";
					//echo $query6."<br>";
					$stmt6 = parse_exec_fetch($conn_Infobase, $query6, $error_str, $res6);
					if (!$stmt6) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					} else {
						OCIFreeStatement($stmt6);
						$data[$z]['BSDS_BOB_REFRESH']=$res6['BSDS_BOB_REFRESH'][0];
					}
				}
				
				if ($reportnr==2 && $data[$z]['BSDSKEY']!="MISSING BSDS!"){
	
					if ($pos1 === false) {				
					}else{
						
						if ($table=="FUND" ){
							$curtable="BSDS_CURRENT_GSM900_FUND";
						}else{
							$curtable="BSDS_CURRENT_GSM900";
						}
						$query = "select * from ".$curtable." WHERE SITEKEY='".$data[$z]['SITEKEY']."'";
						//echo $query;
						$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
						if (!$stmt) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt);
							$data[$z]['cur_EGS_CABTYPE']=$res['CABTYPE'][0];
							$data[$z]['cur_EGS_DXUTYPE1']=$res['DXUTYPE1'][0];
							$data[$z]['cur_EGS_DXUTYPE2']=$res['DXUTYPE2'][0];
							$data[$z]['cur_EGS_DXUTYPE3']=$res['DXUTYPE3'][0];
							$data[$z]['cur_EGS_BBS']=$res['BBS'][0];
						}
	
			
						$query5 = "Select CABTYPE,DXUTYPE1,DXUTYPE2,DXUTYPE3, BBS FROM INFOBASE.BSDS_PLANNED_GEN_GSM900_".$table." WHERE BSDSKEY ='".$data[$z]['BSDSKEY']."' AND BSDS_BOB_REFRESH ='".$data[$z]['BSDS_BOB_REFRESH']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['EGS_CABTYPE']=$res5['CABTYPE'][0];
							$data[$z]['EGS_DXUTYPE1']=$res5['DXUTYPE1'][0];
							$data[$z]['EGS_DXUTYPE2']=$res5['DXUTYPE2'][0];
							$data[$z]['EGS_DXUTYPE3']=$res5['DXUTYPE3'][0];
							$data[$z]['EGS_BBS']=$res5['BBS'][0];
						}
					}
					
					if ($pos2 === false) {				
					}else{
						
						if ($table=="FUND" ){
							$curtable="BSDS_CURRENT_GSM1800_FUND";
						}else{
							$curtable="BSDS_CURRENT_GSM1800";
						}
						$query = "select * from ".$curtable." WHERE SITEKEY='".$data[$z]['SITEKEY']."'";
						//echo $query;
						$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
						if (!$stmt) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt);
							$data[$z]['cur_DCS_CABTYPE']=$res['CABTYPE'][0];
							$data[$z]['cur_DCS_DXUTYPE1']=$res['DXUTYPE1'][0];
							$data[$z]['cur_DCS_DXUTYPE2']=$res['DXUTYPE2'][0];
							$data[$z]['cur_DCS_DXUTYPE3']=$res['DXUTYPE3'][0];
							$data[$z]['cur_DCS_BBS']=$res['BBS'][0];
						}
						
						$query5 = "Select CABTYPE,DXUTYPE1,DXUTYPE2,DXUTYPE3, BBS FROM INFOBASE.BSDS_PLANNED_GEN_GSM1800_".$table." WHERE BSDSKEY ='".$data[$z]['BSDSKEY']."' AND BSDS_BOB_REFRESH ='".$data[$z]['BSDS_BOB_REFRESH']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['DCS_CABTYPE']=$res5['CABTYPE'][0];
							$data[$z]['DCS_DXUTYPE1']=$res5['DXUTYPE1'][0];
							$data[$z]['DCS_DXUTYPE2']=$res5['DXUTYPE2'][0];
							$data[$z]['DCS_DXUTYPE3']=$res5['DXUTYPE3'][0];
							$data[$z]['DCS_BBS']=$res5['BBS'][0];
						}
					}
				} //reportnr2
				
				if ($reportnr==3 && $data[$z]['BSDSKEY']!="MISSING BSDS!"){
	
					if ($pos1 === false) {				
					}else{
						
						//GET THE CURRENT DATA
						if ($table=="FUND" ){
							$curtable="BSDS_CURRENT_GSM1800_FUND";
						}else{
							$curtable="BSDS_CURRENT_GSM1800";
						}
						$query = "select * from ".$curtable." WHERE SITEKEY='".$data[$z]['SITEKEY']."'";
						//echo $query;
						$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
						if (!$stmt) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt);
							$data[$z]['cur_DCS_CABTYPE']=$res['CABTYPE'][0];
							$data[$z]['cur_DCS_BBS']=$res['BBS'][0];					
							$data[$z]['cur_TMA_1']=$res['TMA'][0];
							$data[$z]['cur_DCS_DXUTYPE1']=$res['DXUTYPE1'][0];
							$data[$z]['cur_DCS_DXUTYPE2']=$res['DXUTYPE2'][0];
							$data[$z]['cur_DCS_DXUTYPE3']=$res['DXUTYPE3'][0];
							$data[$z]['cur_DCS_BBS']=$res['BBS'][0];							
							$data[$z]['cur_DCS_TMA_1']=$res['TMA_1'][0];
							$data[$z]['cur_DCS_TMA_2']=$res['TMA_2'][0];
							$data[$z]['cur_DCS_TMA_3']=$res['TMA_3'][0];
							$data[$z]['cur_DCS_TMA_4']=$res['TMA_4'][0];				
							$data[$z]['cur_DCS_COMB_1']=$res['COMB_1'][0];
							$data[$z]['cur_DCS_COMB_2']=$res['COMB_2'][0];
							$data[$z]['cur_DCS_COMB_3']=$res['COMB_3'][0];
							$data[$z]['cur_DCS_COMB_4']=$res['COMB_4'][0];				
							$data[$z]['cur_DCS_DCBLOCK_1']=$res['DCBLOCK_1'][0];
							$data[$z]['cur_DCS_DCBLOCK_2']=$res['DCBLOCK_2'][0];
							$data[$z]['cur_DCS_DCBLOCK_3']=$res['DCBLOCK_3'][0];
							$data[$z]['cur_DCS_DCBLOCK_4']=$res['DCBLOCK_4'][0];
				
							$_SESSION['SiteID']=$data[$z]['SITE_ID'];								
							$freq=get_freq("1");
								$FREQ_ACTIVE1_1=$freq[0];
								$FREQ_ACTIVE2_1=$freq[1];
								$FREQ_ACTIVE3_1=$freq[2];
								//$CDUTYPE=$freq[99]['CDU'];
							$freq=get_freq("2");
								$FREQ_ACTIVE1_2=$freq[0];
								$FREQ_ACTIVE2_2=$freq[1];
								$FREQ_ACTIVE3_2=$freq[2];
							$freq=get_freq("3");
								$FREQ_ACTIVE1_3=$freq[0];
								$FREQ_ACTIVE2_3=$freq[1];
								$FREQ_ACTIVE3_3=$freq[2];
							$freq=get_freq("4");
								$FREQ_ACTIVE1_4=$freq[0];
								$FREQ_ACTIVE2_4=$freq[1];
								$FREQ_ACTIVE3_4=$freq[2];
				
				
							$cabdata=get_CABTYPE("GSM900");
							$CABTYPE=$cabdata['type'];
							$NR_OF_CAB=$cabdata['number'];
							$CDUTYPE=$cabdata['CDU'];
				
							$TRU_data=get_TRU_data("$sec1");
							$TRU_INST1_1_1=$TRU_data[0]['INST'][0];
							$TRU_TYPE1_1_1=$TRU_data[0]['TYPE'][0];
							if ($TRU_TYPE1_1_1=="EDTRU" || $TRU_TYPE1_1_1=="DTRU"){
								$TRU_INST1_1_1=ceil($TRU_data[0]['INST'][0]/2);
							}
							$TRU_INST1_2_1=$TRU_data[0]['INST'][1];
							$TRU_TYPE1_2_1=$TRU_data[0]['TYPE'][1];
							if ($TRU_TYPE1_2_1=="EDTRU" || $TRU_TYPE1_2_1=="DTRU"){
								$TRU_INST1_2_1=ceil($TRU_data[0]['INST'][0]/2);
							}
				
							$TRU_INST2_1_1=$TRU_data[1]['INST'][0];
							$TRU_TYPE2_1_1=$TRU_data[1]['TYPE'][0];
							if ($TRU_TYPE2_1_1=="EDTRU" || $TRU_TYPE2_1_1=="DTRU"){
								$TRU_INST2_1_1=ceil($TRU_data[0]['INST'][0]/2);
							}
							$TRU_INST2_2_1=$TRU_data[1]['INST'][1];
							$TRU_TYPE2_2_1=$TRU_data[1]['TYPE'][1];
							if ($TRU_TYPE2_2_1=="EDTRU" || $TRU_TYPE2_2_1=="DTRU"){
								$TRU_INST2_2_1=ceil($TRU_data[0]['INST'][0]/2);
							}
				
							$TRU_INST3_1_1=$TRU_data[2]['INST'][0];
							$TRU_TYPE3_1_1=$TRU_data[2]['TYPE'][0];
							if ($TRU_TYPE3_1_1=="EDTRU" || $TRU_TYPE3_1_1=="DTRU"){
								$TRU_INST3_1_1=ceil($TRU_data[0]['INST'][0]/2);
							}
							$TRU_INST3_2_1=$TRU_data[2]['INST'][1];
							$TRU_TYPE3_2_1=$TRU_data[2]['TYPE'][1];
							if ($TRU_TYPE3_2_1=="EDTRU" || $TRU_TYPE3_2_1=="DTRU"){
								$TRU_INST3_2_1=ceil($TRU_data[0]['INST'][0]/2);
							}
				
							$TRU_data=get_TRU_data("$sec2");
							$TRU_INST1_1_2=$TRU_data[0]['INST'][0];
							$TRU_TYPE1_1_2=$TRU_data[0]['TYPE'][0];
							if ($TRU_TYPE1_1_2=="EDTRU" || $TRU_TYPE1_1_2=="DTRU"){
								$TRU_INST1_1_2=ceil($TRU_data[0]['INST'][0]/2);
							}
							$TRU_INST1_2_2=$TRU_data[0]['INST'][1];
							$TRU_TYPE1_2_2=$TRU_data[0]['TYPE'][1];
							if ($TRU_TYPE1_2_2=="EDTRU" || $TRU_TYPE1_2_2=="DTRU"){
								$TRU_INST1_2_2=ceil($TRU_data[0]['INST'][0]/2);
							}
				
							$TRU_INST2_1_2=$TRU_data[1]['INST'][0];
							$TRU_TYPE2_1_2=$TRU_data[1]['TYPE'][0];
							if ($TRU_TYPE2_1_2=="EDTRU" || $TRU_TYPE2_1_2=="DTRU"){
								$TRU_INST2_1_2=ceil($TRU_data[0]['INST'][0]/2);
							}
							$TRU_INST2_2_2=$TRU_data[1]['INST'][1];
							$TRU_TYPE2_2_2=$TRU_data[1]['TYPE'][1];
							if ($TRU_TYPE2_2_2=="EDTRU" || $TRU_TYPE2_2_2=="DTRU"){
								$TRU_INST2_2_2=ceil($TRU_data[0]['INST'][0]/2);
							}
				
							$TRU_INST3_1_2=$TRU_data[2]['INST'][0];
							$TRU_TYPE3_1_2=$TRU_data[2]['TYPE'][0];
							if ($TRU_TYPE3_1_2=="EDTRU" || $TRU_TYPE3_1_2=="DTRU"){
								$TRU_INST3_1_2=ceil($TRU_data[0]['INST'][0]/2);
							}
							$TRU_INST3_2_2=$TRU_data[2]['INST'][1];
							$TRU_TYPE3_2_2=$TRU_data[2]['TYPE'][1];
							if ($TRU_TYPE3_2_2=="EDTRU" || $TRU_TYPE3_2_2=="DTRU"){
								$TRU_INST3_2_2=ceil($TRU_data[0]['INST'][0]/2);
							}
				
							$TRU_data=get_TRU_data("$sec3");
							$TRU_INST1_1_3=$TRU_data[0]['INST'][0];
							$TRU_TYPE1_1_3=$TRU_data[0]['TYPE'][0];
							if ($TRU_TYPE1_1_3=="EDTRU" || $TRU_TYPE1_1_3=="DTRU"){
								$TRU_INST1_1_3=ceil($TRU_data[0]['INST'][0]/2);
							}
							$TRU_INST1_2_3=$TRU_data[0]['INST'][1];
							$TRU_TYPE1_2_3=$TRU_data[0]['TYPE'][1];
							if ($TRU_TYPE1_2_3=="EDTRU" || $TRU_TYPE1_2_3=="DTRU"){
								$TRU_INST1_2_3=ceil($TRU_data[0]['INST'][0]/2);
							}
				
							$TRU_INST2_1_3=$TRU_data[1]['INST'][0];
							$TRU_TYPE2_1_3=$TRU_data[1]['TYPE'][0];
							if ($TRU_TYPE2_1_3=="EDTRU" || $TRU_TYPE2_1_3=="DTRU"){
								$TRU_INST2_1_3=ceil($TRU_data[0]['INST'][0]/2);
							}
							$TRU_INST2_2_3=$TRU_data[1]['INST'][1];
							$TRU_TYPE2_2_3=$TRU_data[1]['TYPE'][1];
							if ($TRU_TYPE2_2_3=="EDTRU" || $TRU_TYPE2_2_3=="DTRU"){
								$TRU_INST2_2_3=ceil($TRU_data[0]['INST'][0]/2);
							}
				
							$TRU_INST3_1_3=$TRU_data[2]['INST'][0];
							$TRU_TYPE3_1_3=$TRU_data[2]['TYPE'][0];
							if ($TRU_TYPE3_1_3=="EDTRU" || $TRU_TYPE3_1_3=="DTRU"){
								$TRU_INST3_1_3=ceil($TRU_data[0]['INST'][0]/2);
							}
							$TRU_INST3_2_3=$TRU_data[2]['INST'][1];
							$TRU_TYPE3_2_3=$TRU_data[2]['TYPE'][1];
							if ($TRU_TYPE3_2_3=="EDTRU" || $TRU_TYPE3_2_3=="DTRU"){
								$TRU_INST3_2_3=ceil($TRU_data[0]['INST'][0]/2);
							}
				
							$TRU_INST1_2=$TRU_data[0]['TRU_INST2'];
							$TRU_TYPE1_2=$TRU_data[0]['TRU_TYPE2'];
				
							$TRU_INST2_2=$TRU_data[1]['TRU_INST2'];
							$TRU_TYPE2_2=$TRU_data[1]['TRU_TYPE2'];
				
							$TRU_INST1_3=$TRU_data[0]['TRU_INST3'];
							$TRU_TYPE1_3=$TRU_data[0]['TRU_TYPE3'];
							$TRU_INST2_3=$TRU_data[1]['TRU_INST3'];
							$TRU_TYPE2_3=$TRU_data[1]['TRU_TYPE3'];
				
							$TRU_INST1_4=$TRU_data[0]['TRU_INST4'];
							$TRU_TYPE1_4=$TRU_data[0]['TRU_TYPE4'];
							$TRU_INST2_4=$TRU_data[1]['TRU_INST4'];
							$TRU_TYPE2_4=$TRU_data[1]['TRU_TYPE4'];
				
						}
						
						
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM900_1_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM900_TMA1']=$res5['TMA'][0];
							$data[$z]['GSM900_FREQACTIVE1_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM900_FREQACTIVE1_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM900_FREQACTIVE1_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM900_TRU_INST1_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM900_TRU_TYPE1_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM900_TRU_INST1_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM900_TRU_TYPE1_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM900_TRU_INST1_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM900_TRU_TYPE1_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM900_TRU_INST1_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM900_TRU_TYPE1_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM900_TRU_INST1_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM900_TRU_TYPE1_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM900_TRU_INST1_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM900_TRU_TYPE1_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM900_ANTENNA_TYPE1_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM900_ANTENNA_TYPE1_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM900_CONFIG1']=$res5['CONFIG'][0];										
						}
						
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM900_2_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM900_TMA2']=$res5['TMA'][0];
							$data[$z]['GSM900_FREQACTIVE2_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM900_FREQACTIVE2_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM900_FREQACTIVE2_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM900_TRU_INST2_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM900_TRU_TYPE2_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM900_TRU_INST2_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM900_TRU_TYPE2_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM900_TRU_INST2_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM900_TRU_TYPE2_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM900_TRU_INST2_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM900_TRU_TYPE2_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM900_TRU_INST2_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM900_TRU_TYPE2_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM900_TRU_INST2_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM900_TRU_TYPE2_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM900_ANTENNA_TYPE2_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM900_ANTENNA_TYPE2_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM900_CONFIG2']=$res5['CONFIG'][0];					
						}
						
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM900_3_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM900_TMA3']=$res5['TMA'][0];
							$data[$z]['GSM900_FREQACTIVE3_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM900_FREQACTIVE3_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM900_FREQACTIVE3_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM900_TRU_INST3_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM900_TRU_TYPE3_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM900_TRU_INST3_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM900_TRU_TYPE3_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM900_TRU_INST3_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM900_TRU_TYPE3_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM900_TRU_INST3_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM900_TRU_TYPE3_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM900_TRU_INST3_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM900_TRU_TYPE3_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM900_TRU_INST3_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM900_TRU_TYPE3_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM900_ANTENNA_TYPE3_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM900_ANTENNA_TYPE3_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM900_CONFIG3']=$res5['CONFIG'][0];					
						}
					}
					
					if ($pos2 === false) {				
					}else{
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM1800_1_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM1800_TMA1']=$res5['TMA'][0];
							$data[$z]['GSM1800_FREQACTIVE1_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM1800_FREQACTIVE1_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM1800_FREQACTIVE1_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM1800_TRU_INST1_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM1800_TRU_TYPE1_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM1800_TRU_INST1_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM1800_TRU_TYPE1_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM1800_TRU_INST1_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM1800_TRU_TYPE1_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM1800_TRU_INST1_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM1800_TRU_TYPE1_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM1800_TRU_INST1_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM1800_TRU_TYPE1_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM1800_TRU_INST1_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM1800_TRU_TYPE1_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE1_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE1_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM1800_CONFIG1']=$res5['CONFIG'][0];				
						}
						
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM1800_2_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM1800_TMA2']=$res5['TMA'][0];
							$data[$z]['GSM1800_FREQACTIVE2_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM1800_FREQACTIVE2_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM1800_FREQACTIVE2_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM1800_TRU_INST2_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM1800_TRU_TYPE2_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM1800_TRU_INST2_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM1800_TRU_TYPE2_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM1800_TRU_INST2_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM1800_TRU_TYPE2_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM1800_TRU_INST2_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM1800_TRU_TYPE2_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM1800_TRU_INST2_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM1800_TRU_TYPE2_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM1800_TRU_INST2_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM1800_TRU_TYPE2_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE2_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE2_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM1800_CONFIG2']=$res5['CONFIG'][0];						
						}
						
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM1800_3_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM1800_TMA3']=$res5['TMA'][0];
							$data[$z]['GSM1800_FREQACTIVE3_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM1800_FREQACTIVE3_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM1800_FREQACTIVE3_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM1800_TRU_INST3_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM1800_TRU_TYPE3_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM1800_TRU_INST3_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM1800_TRU_TYPE3_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM1800_TRU_INST3_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM1800_TRU_TYPE3_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM1800_TRU_INST3_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM1800_TRU_TYPE3_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM1800_TRU_INST3_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM1800_TRU_TYPE3_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM1800_TRU_INST3_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM1800_TRU_TYPE3_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE3_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE3_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM1800_CONFIG3']=$res5['CONFIG'][0];					
						}
					}
	
				}
				
				if ($reportnr==4 && $data[$z]['BSDSKEY']!="MISSING BSDS!"){
					//HSTX
					
					$query5 = "Select HSTXHW, HSTXSW FROM INFOBASE.BSDS_PLANNED_UMTS_GEN_01_".$table." WHERE BSDSKEY ='".$data[$z]['BSDSKEY']."' AND BSDS_BOB_REFRESH ='".$data[$z]['BSDS_BOB_REFRESH']."'";
					//echo $query5."<br>";
					$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
					if (!$stmt5) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					} else {
						OCIFreeStatement($stmt5);
						if ($res5['HSTXHW'][0]!="15"){
							$data[$z]['HSTXHW']="<font color='red'>".$res5['HSTXHW'][0]."</font>";
						}else{
							$data[$z]['HSTXHW']=$res5['HSTXHW'][0];
						}
						if ($res5['HSTXSW'][0]!="0"){
							$data[$z]['HSTXSW']="<font color='red'>".$res5['HSTXSW'][0]."</font>";
						}else{
							$data[$z]['HSTXSW']=$res5['HSTXSW'][0];
						}
					}
				}
			
			$z++;
			}		
		}
	}

}//if ($_POST['type']=="Upgrades"){
	/***************************************************
					NEW BUILDS
	***************************************************/
	//echo "<pre>".print_r($data,true)."</pre>";

if ($_POST['type']=="New builds"){	
	$query2="SELECT SIT_UDK, A353, A71, A305, DRE_V20_1 FROM infobase.VW_NET1_ALL_NEWBUILDS
	WHERE (trim(A353) IS NOT NULL OR trim(A71) IS NOT NULL OR trim(A305) IS NOT NULL) AND trim(SIT_UDK) IS NOT NULL";
	//echo "<br><br>".$query2;
	
	$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
	if (!$stmt2) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt2);
		$Count2=count($res2['SIT_UDK']);
		for ($i = 0; $i < $Count2; $i++) {
			$data[$z]['TECHNOLOGY']=get_technocombination($res2['DRE_V20_1'][$i],"");
			
			$pos1 = strrpos($data[$z]['TECHNOLOGY'], "EGS");
			$pos2 = strrpos($data[$z]['TECHNOLOGY'], "DCS");
			
			$pos3 = strrpos($data[$z]['TECHNOLOGY'], "HSDPA");
			$pos4 = strrpos($data[$z]['TECHNOLOGY'], "UMTS");
				
			//echo $data[$z]['TECHNOLOGY'].": $pos1 - $pos2 - $pos3 - $pos4<br>";
				
			if (($pos1 === false && $pos2 === false && $reportnr==2)
			||($pos3 === false && $pos4 === false && $reportnr==4)
			||($pos1 === false && $pos2 === false && $reportnr==3)) {
			
			}else{				
	
				$data[$z]['SITEID']=trim($res2['SIT_UDK'][$i]);
				if ($data[$z]['SITEID'][0]=="_"){
					$data[$z]['SITEID']=substr($data[$z]['SITEID'],1);
				}
				
				$data[$z]['SITE_FUNDED']=trim($res2['A353'][$i]);
				$data[$z]['BUILD']=trim($res2['A71'][$i]);
				$data[$z]['BSDS_FUNDED']=trim($res2['A305'][$i]);
				$data[$z]['BAND']=$res2['BAND'][$i];
				$data[$z]['TYPE']="NEW SITE";
				
				
				if ($data[$z]['BUILD']!=""){
					$data[$z]['STATUS']="BSDS AS BUILD";
					$table="BUILD";
				}elseif ($data[$z]['BSDS_FUNDED']!=""){
					$data[$z]['STATUS']="BSDS FUNDED";
					$table="FUND";
				}elseif ($data[$z]['SITE_FUNDED']!=""){
					$data[$z]['STATUS']="SITE FUNDED";
					$table="POST";
				}
				
				$query4 = "Select BSDSKEY FROM INFOBASE.BSDS_SITE_FUNDED WHERE SITEID LIKE '".$data[$z]['SITEID']."%'";
				//secho $query4;
				$stmt4 = parse_exec_fetch($conn_Infobase, $query4, $error_str, $res4);
				if (!$stmt4) {
					die_silently($conn_Infobase, $error_str);
				 	exit;
				} else {
					OCIFreeStatement($stmt4);
					//echo $data[$z]['SITEID'].":".count($res4['BSDSKEY'])."<br>";
					
					if (count($res4['BSDSKEY'])!=0){
						$data[$z]['BSDSKEY']=$res4['BSDSKEY'][0];
					}else{
						$data[$z]['BSDSKEY']="MISSING BSDS!";
					}				
				}
				
				if (count($res4['BSDSKEY'])!=0){
					
					$query3 = "Select SITEKEY FROM INFOBASE.BSDS_GENERALINFO WHERE BSDSKEY = '".$data[$z]['BSDSKEY']."'";
					//echo $query3."<br>";
					$stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
					if (!$stmt3) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					} else {
						OCIFreeStatement($stmt3);
						
						if (count($res3['SITEKEY'])!=0){
							$data[$z]['SITEKEY']=$res3['SITEKEY'][0];
						}else{
							$data[$z]['SITEKEY']="MISSING!";
						}
					}
					
					$query6 = "Select MAX(BSDS_BOB_REFRESH) AS BSDS_BOB_REFRESH FROM INFOBASE.BSDS_FUNDED_TEAML_ACC2 WHERE BSDSKEY ='".$data[$z]['BSDSKEY']."'";
					//echo $query6."<br>";
					$stmt6 = parse_exec_fetch($conn_Infobase, $query6, $error_str, $res6);
					if (!$stmt6) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					} else {
						OCIFreeStatement($stmt6);
						$data[$z]['BSDS_BOB_REFRESH']=$res6['BSDS_BOB_REFRESH'][0];
					}
				}
				
				if ($reportnr==2 && $data[$z]['BSDSKEY']!="MISSING BSDS!"){
	
					if ($pos1 === false) {				
					}else{
						if ($table=="FUND" ){
							$curtable="BSDS_CURRENT_GSM900_FUND";
						}else{
							$curtable="BSDS_CURRENT_GSM900";
						}
						$query = "select * from ".$curtable." WHERE SITEKEY='".$data[$z]['SITEKEY']."'";
						//echo $query;
						$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
						if (!$stmt) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt);
							$data[$z]['cur_EGS_CABTYPE']=$res['CABTYPE'][0];
							$data[$z]['cur_EGS_DXUTYPE1']=$res['DXUTYPE1'][0];
							$data[$z]['cur_EGS_DXUTYPE2']=$res['DXUTYPE2'][0];
							$data[$z]['cur_EGS_DXUTYPE3']=$res['DXUTYPE3'][0];
							$data[$z]['cur_EGS_BBS']=$res['BBS'][0];
						}
						
						$query5 = "Select CABTYPE,DXUTYPE1,DXUTYPE2,DXUTYPE3, BBS FROM INFOBASE.BSDS_PLANNED_GEN_GSM900_".$table." WHERE BSDSKEY ='".$data[$z]['BSDSKEY']."' AND BSDS_BOB_REFRESH ='".$data[$z]['BSDS_BOB_REFRESH']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['EGS_CABTYPE']=$res5['CABTYPE'][0];
							$data[$z]['EGS_DXUTYPE1']=$res5['DXUTYPE1'][0];
							$data[$z]['EGS_DXUTYPE2']=$res5['DXUTYPE2'][0];
							$data[$z]['EGS_DXUTYPE3']=$res5['DXUTYPE3'][0];
							$data[$z]['EGS_BBS']=$res5['BBS'][0];
						}
					}
					
					if ($pos2 === false) {				
					}else{
						
						if ($table=="FUND" ){
							$curtable="BSDS_CURRENT_GSM1800_FUND";
						}else{
							$curtable="BSDS_CURRENT_GSM1800";
						}
						$query = "select * from ".$curtable." WHERE SITEKEY='".$data[$z]['SITEKEY']."'";
						//echo $query;
						$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res);
						if (!$stmt) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt);
							$data[$z]['cur_DCS_CABTYPE']=$res['CABTYPE'][0];
							$data[$z]['cur_DCS_DXUTYPE1']=$res['DXUTYPE1'][0];
							$data[$z]['cur_DCS_DXUTYPE2']=$res['DXUTYPE2'][0];
							$data[$z]['cur_DCS_DXUTYPE3']=$res['DXUTYPE3'][0];
							$data[$z]['cur_DCS_BBS']=$res['BBS'][0];
						}
						
						$query5 = "Select CABTYPE,DXUTYPE1,DXUTYPE2,DXUTYPE3, BBS FROM INFOBASE.BSDS_PLANNED_GEN_GSM1800_".$table." WHERE BSDSKEY ='".$data[$z]['BSDSKEY']."' AND BSDS_BOB_REFRESH ='".$data[$z]['BSDS_BOB_REFRESH']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['DCS_CABTYPE']=$res5['CABTYPE'][0];
							$data[$z]['DCS_DXUTYPE1']=$res5['DXUTYPE1'][0];
							$data[$z]['DCS_DXUTYPE2']=$res5['DXUTYPE2'][0];
							$data[$z]['DCS_DXUTYPE3']=$res5['DXUTYPE3'][0];
							$data[$z]['DCS_BBS']=$res5['BBS'][0];
						}
					}
				} //reportnr2
				
				if ($reportnr==3 && $data[$z]['BSDSKEY']!="MISSING BSDS!"){
	
					if ($pos1 === false) {				
					}else{
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM900_1_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM900_TMA1']=$res5['TMA'][0];
							$data[$z]['GSM900_FREQACTIVE1_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM900_FREQACTIVE1_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM900_FREQACTIVE1_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM900_TRU_INST1_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM900_TRU_TYPE1_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM900_TRU_INST1_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM900_TRU_TYPE1_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM900_TRU_INST1_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM900_TRU_TYPE1_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM900_TRU_INST1_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM900_TRU_TYPE1_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM900_TRU_INST1_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM900_TRU_TYPE1_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM900_TRU_INST1_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM900_TRU_TYPE1_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM900_ANTENNA_TYPE1_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM900_ANTENNA_TYPE1_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM900_CONFIG1']=$res5['CONFIG'][0];									
						}
											
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM900_2_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM900_TMA2']=$res5['TMA'][0];
							$data[$z]['GSM900_FREQACTIVE2_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM900_FREQACTIVE2_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM900_FREQACTIVE2_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM900_TRU_INST2_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM900_TRU_TYPE2_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM900_TRU_INST2_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM900_TRU_TYPE2_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM900_TRU_INST2_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM900_TRU_TYPE2_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM900_TRU_INST2_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM900_TRU_TYPE2_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM900_TRU_INST2_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM900_TRU_TYPE2_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM900_TRU_INST2_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM900_TRU_TYPE2_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM900_ANTENNA_TYPE2_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM900_ANTENNA_TYPE2_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM900_CONFIG2']=$res5['CONFIG'][0];					
						}
						
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM900_3_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM900_TMA3']=$res5['TMA'][0];
							$data[$z]['GSM900_FREQACTIVE3_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM900_FREQACTIVE3_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM900_FREQACTIVE3_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM900_TRU_INST3_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM900_TRU_TYPE3_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM900_TRU_INST3_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM900_TRU_TYPE3_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM900_TRU_INST3_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM900_TRU_TYPE3_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM900_TRU_INST3_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM900_TRU_TYPE3_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM900_TRU_INST3_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM900_TRU_TYPE3_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM900_TRU_INST3_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM900_TRU_TYPE3_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM900_ANTENNA_TYPE3_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM900_ANTENNA_TYPE3_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM900_CONFIG3']=$res5['CONFIG'][0];					
						}
					}
					
					if ($pos2 === false) {				
					}else{
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM1800_1_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM1800_TMA1']=$res5['TMA'][0];
							$data[$z]['GSM1800_FREQACTIVE1_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM1800_FREQACTIVE1_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM1800_FREQACTIVE1_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM1800_TRU_INST1_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM1800_TRU_TYPE1_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM1800_TRU_INST1_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM1800_TRU_TYPE1_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM1800_TRU_INST1_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM1800_TRU_TYPE1_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM1800_TRU_INST1_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM1800_TRU_TYPE1_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM1800_TRU_INST1_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM1800_TRU_TYPE1_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM1800_TRU_INST1_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM1800_TRU_TYPE1_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE1_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE1_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM1800_CONFIG1']=$res5['CONFIG'][0];				
						}
						
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM1800_2_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM1800_TMA2']=$res5['TMA'][0];
							$data[$z]['GSM1800_FREQACTIVE2_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM1800_FREQACTIVE2_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM1800_FREQACTIVE2_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM1800_TRU_INST2_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM1800_TRU_TYPE2_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM1800_TRU_INST2_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM1800_TRU_TYPE2_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM1800_TRU_INST2_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM1800_TRU_TYPE2_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM1800_TRU_INST2_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM1800_TRU_TYPE2_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM1800_TRU_INST2_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM1800_TRU_TYPE2_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM1800_TRU_INST2_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM1800_TRU_TYPE2_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE2_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE2_2']=$res5['ANTTYPE2'][0];	
							$data[$z]['GSM1800_CONFIG2']=$res5['CONFIG'][0];					
						}
						
						$query5 = "SELECT * FROM BSDS_PLANNED_GSM1800_3_".$table." WHERE BSDSKEY= '".$data[$z]['BSDSKEY']."'";
						//echo $query5."<br>";
						$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
						if (!$stmt5) {
							die_silently($conn_Infobase, $error_str);
						 	exit;
						} else {
							OCIFreeStatement($stmt5);
							$data[$z]['GSM1800_TMA3']=$res5['TMA'][0];
							$data[$z]['GSM1800_FREQACTIVE3_1']=$res5['FREQ_ACTIVE1'][0];
							$data[$z]['GSM1800_FREQACTIVE3_2']=$res5['FREQ_ACTIVE2'][0];
							$data[$z]['GSM1800_FREQACTIVE3_3']=$res5['FREQ_ACTIVE3'][0];
							$data[$z]['GSM1800_TRU_INST3_1']=$res5['TRU_INST1_1'][0];
							$data[$z]['GSM1800_TRU_TYPE3_1']=$res5['TRU_TYPE1_1'][0];
							$data[$z]['GSM1800_TRU_INST3_2']=$res5['TRU_INST1_2'][0];
							$data[$z]['GSM1800_TRU_TYPE3_2']=$res5['TRU_TYPE1_2'][0];
							$data[$z]['GSM1800_TRU_INST3_3']=$res5['TRU_INST2_1'][0];
							$data[$z]['GSM1800_TRU_TYPE3_3']=$res5['TRU_TYPE2_1'][0];
							$data[$z]['GSM1800_TRU_INST3_4']=$res5['TRU_INST2_2'][0];
							$data[$z]['GSM1800_TRU_TYPE3_4']=$res5['TRU_TYPE2_2'][0];
							$data[$z]['GSM1800_TRU_INST3_5']=$res5['TRU_INST3_1'][0];
							$data[$z]['GSM1800_TRU_TYPE3_5']=$res5['TRU_TYPE3_1'][0];
							$data[$z]['GSM1800_TRU_INST3_6']=$res5['TRU_INST3_2'][0];
							$data[$z]['GSM1800_TRU_TYPE3_6']=$res5['TRU_TYPE3_2'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE3_1']=$res5['ANTTYPE1'][0];
							$data[$z]['GSM1800_ANTENNA_TYPE3_2']=$res5['ANTTYPE2'][0];
							$data[$z]['GSM1800_CONFIG3']=$res5['CONFIG'][0];					
						}
					}
	
				}
				
				if ($reportnr==4 && $data[$z]['BSDSKEY']!="MISSING BSDS!"){
					//HSTX
					$query5 = "Select HSTXHW, HSTXSW FROM INFOBASE.BSDS_PLANNED_UMTS_GEN_01_".$table." WHERE BSDSKEY ='".$data[$z]['BSDSKEY']."' AND BSDS_BOB_REFRESH ='".$data[$z]['BSDS_BOB_REFRESH']."'";
					//echo $query5."<br>";
					$stmt5 = parse_exec_fetch($conn_Infobase, $query5, $error_str, $res5);
					if (!$stmt5) {
						die_silently($conn_Infobase, $error_str);
					 	exit;
					} else {
						OCIFreeStatement($stmt5);
						if ($res5['HSTXHW'][0]!="15"){
							$data[$z]['HSTXHW']="<font color='red'>".$res5['HSTXHW'][0]."</font>";
						}else{
							$data[$z]['HSTXHW']=$res5['HSTXHW'][0];
						}
						if ($res5['HSTXSW'][0]!="0"){
							$data[$z]['HSTXSW']="<font color='red'>".$res5['HSTXSW'][0]."</font>";
						}else{
							$data[$z]['HSTXSW']=$res5['HSTXSW'][0];
						}
					}
				} //reportnr4
				
				$z++;
			}		
						
		}//END FOR LOOP
	}

}// if ($_POST['type']=="New builds"){


$filename = 'var/www/html/data.txt';
$somecontent = "Add this to the file\n";

// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {

    // In our example we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'a')) {
         echo "Cannot open file ($filename)";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($handle, $somecontent) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }

    echo "Success, wrote ($somecontent) to file ($filename)";

    fclose($handle);

} else {
    echo "The file $filename is not writable";
}

die;
?>


<table id="reporttable" cellpadding="0" cellspacing="0" class="report" border=0>
<thead>
<?php
if ($reportnr=="3"){ 
?>
<tr>
	<td colspan="9" bgcolor="yellow">&nbsp;</th>
	<td colspan="57" bgcolor="lightgreen"><b>GSM900</b></th>	
	<td colspan="57" bgcolor="lightblue"><b>GSM1800</b></th>
</tr>
<tr>
	<td colspan="9" bgcolor="yellow">&nbsp;</th>
	<td colspan="19" bgcolor="#217C7E">Sector 1</th>	
	<td colspan="19" bgcolor="#3399FF">Sector 2</th>
	<td colspan="19" bgcolor="#9A3334">Sector 3</th>		
	<td colspan="19" bgcolor="#217C7E">Sector 1</th>	
	<td colspan="19" bgcolor="#3399FF">Sector 2</th>
	<td colspan="19" bgcolor="#9A3334">Sector 3</th>		
</tr>	

<?php 
}
?>	
<tr>
	<th>NR</th>
	<th>SITE ID</th>
	<th>BSDSKEY</th>		
	<th>TECHNOLOGY</th>	
	<th>TYPE</th>		
	<th>A/U353<br>(SITE FUNDED)</th>
	<th>A/U305<br>(BSDS FUNDED)</th>
	<th>U571/A71<br>(SITE INTEGRATED)</th>
	<th>STATUS</th>	
	<th>BOB REFRESH DATE</th>
	<?php
	if ($reportnr=="4"){ 
	?>
	<th>HSTXHW</th>	
	<th>HSTXSW</th>	
	<?php 
	}

	if ($reportnr=="3"){ 
	?>
	<th><b>PL GSM900:</b></th>
	<th>CABTYPE DCS</th>	
	<th>DXUTYPE1</th>
	<th>DXUTYPE2</th>
	<th>DXUTYPE3</th>
	<th>BBS</th>
	
	<th><b>PL GSM900 / SEC1:</b></th>
	<th>TMA</th>
	<th>TRUTYPE CAB1 1</th>
	<th>TRU CAB1 2</th>
	<th>TRUTYPE CAB1 2</th>
	<th>TRU CAB2 1</th>	
	<th>TRUTYPE CAB2 1</th>
	<th>TRU CAB2 2</th>
	<th>TRUTYPE CAB2 2</th>
	<th>TRU CAB3 1</th>
	<th>TRUTYPE CAB3 1</th>
	<th>TRU CAB3 2</th>
	<th>TRUTYPE CAB3 2</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>PL GSM900 / SEC2:</b></th>
	<th>TMA</th>
	<th>TRU CAB1 1</th>	<!-- sector1 -->
	<th>TRUTYPE CAB1 1</th>
	<th>TRU CAB1 2</th>
	<th>TRUTYPE CAB1 2</th>
	<th>TRU CAB2 1</th>	<!-- sector2 -->
	<th>TRUTYPE CAB2 1</th>
	<th>TRU CAB2 2</th>
	<th>TRUTYPE CAB2 2</th>
	<th>TRU CAB3 1</th>	<!-- sector3 -->
	<th>TRUTYPE CAB3 1</th>
	<th>TRU CAB3 2</th>
	<th>TRUTYPE CAB3 2</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>PL GSM900 / SEC3:</b></th>
	<th>TMA</th>
	<th>TRUTYPE CAB1 1</th>
	<th>TRU CAB1 2</th>
	<th>TRUTYPE CAB1 2</th>
	<th>TRU CAB2 1</th>	<!-- sector2 -->
	<th>TRUTYPE CAB2 1</th>
	<th>TRU CAB2 2</th>
	<th>TRUTYPE CAB2 2</th>
	<th>TRU CAB3 1</th>	<!-- sector3 -->
	<th>TRUTYPE CAB3 1</th>
	<th>TRU CAB3 2</th>
	<th>TRUTYPE CAB3 2</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>PL GSM1800:</b></th>
	<th>CABTYPE DCS</th>	
	<th>DXUTYPE1</th>
	<th>DXUTYPE2</th>
	<th>DXUTYPE3</th>
	<th>BBS</th>
	
	
	<th><b>PL GSM1800 / SEC1:</b></th>
	<th>TMA</th>
	<th colspan="4">TRU CAB1</th>	<!-- sector1 -->
	<th colspan="4">TRU CAB2</th>
	<th colspan="4">TRU CAB3</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>PL GSM1800 / SEC2:</b></th>
	<th>TMA</th>
	<th colspan="4">TRU CAB1</th>	<!-- sector2 -->
	<th colspan="4">TRU CAB2</th>
	<th colspan="4">TRU CAB3</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>PL GSM1800 / SEC3:</b></th>
	<th>TMA</th>
	<th colspan="4">TRU CAB1</th>	<!-- sector3 -->			
	<th colspan="4">TRU CAB2</th>
	<th colspan="4">TRU CAB3</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<!-- cur: -->
	
	<th><b>CUR GSM900:</b></th>
	<th>CABTYPE DCS</th>	
	<th>DXUTYPE1</th>
	<th>DXUTYPE2</th>
	<th>DXUTYPE3</th>
	<th>BBS</th>
	<th>CABTYPE EGS</th>	
	<th>DXUTYPE1</th>
	<th>DXUTYPE2</th>
	<th>DXUTYPE3</th>
	<th>BBS</th>
	
	<th><b>CUR GSM900 / SEC1:</b></th>
	<th>TMA</th>
	<th>TRUTYPE CAB1 1</th>
	<th>TRU CAB1 2</th>
	<th>TRUTYPE CAB1 2</th>
	<th>TRU CAB2 1</th>	
	<th>TRUTYPE CAB2 1</th>
	<th>TRU CAB2 2</th>
	<th>TRUTYPE CAB2 2</th>
	<th>TRU CAB3 1</th>
	<th>TRUTYPE CAB3 1</th>
	<th>TRU CAB3 2</th>
	<th>TRUTYPE CAB3 2</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>CUR GSM900 / SEC2:</b></th>
	<th>TMA</th>
	<th>TRU CAB1 1</th>	<!-- sector1 -->
	<th>TRUTYPE CAB1 1</th>
	<th>TRU CAB1 2</th>
	<th>TRUTYPE CAB1 2</th>
	<th>TRU CAB2 1</th>	<!-- sector2 -->
	<th>TRUTYPE CAB2 1</th>
	<th>TRU CAB2 2</th>
	<th>TRUTYPE CAB2 2</th>
	<th>TRU CAB3 1</th>	<!-- sector3 -->
	<th>TRUTYPE CAB3 1</th>
	<th>TRU CAB3 2</th>
	<th>TRUTYPE CAB3 2</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>CUR GSM900 / SEC3:</b></th>
	<th>TMA</th>
	<th>TRUTYPE CAB1 1</th>
	<th>TRU CAB1 2</th>
	<th>TRUTYPE CAB1 2</th>
	<th>TRU CAB2 1</th>	<!-- sector2 -->
	<th>TRUTYPE CAB2 1</th>
	<th>TRU CAB2 2</th>
	<th>TRUTYPE CAB2 2</th>
	<th>TRU CAB3 1</th>	<!-- sector3 -->
	<th>TRUTYPE CAB3 1</th>
	<th>TRU CAB3 2</th>
	<th>TRUTYPE CAB3 2</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>CUR GSM1800:</b></th>
	<th>CABTYPE DCS</th>	
	<th>DXUTYPE1</th>
	<th>DXUTYPE2</th>
	<th>DXUTYPE3</th>
	<th>BBS</th>
	<th>CABTYPE EGS</th>	
	<th>DXUTYPE1</th>
	<th>DXUTYPE2</th>
	<th>DXUTYPE3</th>
	<th>BBS</th>
	
	<th><b>CUR GSM1800 / SEC1:</b></th>
	<th>TMA</th>
	<th colspan="4">TRU CAB1</th>	<!-- sector1 -->
	<th colspan="4">TRU CAB2</th>
	<th colspan="4">TRU CAB3</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>CUR GSM1800 / SEC2:</b></th>
	<th>TMA</th>
	<th colspan="4">TRU CAB1</th>	<!-- sector2 -->
	<th colspan="4">TRU CAB2</th>
	<th colspan="4">TRU CAB3</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	
	<th><b>CUR GSM1800 / SEC3:</b></th>
	<th>TMA</th>
	<th colspan="4">TRU CAB1</th>	<!-- sector3 -->			
	<th colspan="4">TRU CAB2</th>
	<th colspan="4">TRU CAB3</th>
	<th>FREQ CAB1</th>
	<th>FREQ CAB2</th>
	<th>FREQ CAB3</th>
	<th>ANTENNA 1</th>
	<th>ANTENNA 2</th>
	<th>CONFIG</th>
	<?php 
	}
	
	
	?>
		
</tr>	
</thead>
<tbody>
<?
//echo "<pre>".print_r($data,true)."</pre>";

for ($i = 0; $i <= $z; $i++) {	
								
	echo "<tr>
	<td>".$i."</td>
	<td>".$data[$i]['SITEID']."</td>";
	
	if (trim($data[$i]['BSDSKEY'])=="MISSING BSDS!"){
		echo "<td><font color='red'><b>".$data[$i]['BSDSKEY']."</b></font></td>";	
	}else{
		echo "<td>".$data[$i]['BSDSKEY']."</td>";
	}
	
	echo "
	<td>".$data[$i]['TECHNOLOGY']."</td>
	<td>".$data[$i]['TYPE']."</td>					
	<td>".$data[$i]['SITE_FUNDED']."</td>
	<td>".$data[$i]['BSDS_FUNDED']."</td>
	<td>".$data[$i]['BUILD']."</td>	
	<td>".$data[$i]['STATUS']."</td>	
	<td>".$data[$i]['BSDS_BOB_REFRESH']."</td>";
	
	if ($reportnr=="4"){ 	
	echo "<td>".$data[$i]['HSTXHW']."</td>
	<td>".$data[$i]['HSTXSW']."</td>";
	}	
	
	if ($reportnr=="3"){	
		
	echo "<td><b>PL GSM900:</b></td>
	<td>".$data[$i]['EGS_CABTYPE']."</td>
	<td>".$data[$i]['EGS_DXUTYPE1']."</td>
	<td>".$data[$i]['EGS_DXUTYPE2']."</td>
	<td>".$data[$i]['EGS_DXUTYPE3']."</td>
	<td>".$data[$i]['EGS_BBS']."</td>";
	
	echo "<td><b>PL GSM900 / SEC1:</b></td>
	<td>".$data[$i]['GSM900_TMA1']."</td>
	<td>".$data[$i]['GSM900_TRU_INST1_1']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE1_1']."</td>
	<td>".$data[$i]['GSM900_TRU_INST1_2']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE1_2']."</td>
	<td>".$data[$i]['GSM900_TRU_INST1_3']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE1_3']."</td>
	<td>".$data[$i]['GSM900_TRU_INST1_4']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE1_4']."</td>
	<td>".$data[$i]['GSM900_TRU_INST1_5']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE1_5']."</td>
	<td>".$data[$i]['GSM900_TRU_INST1_6']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE1_6']."</td>
	<td>".$data[$i]['GSM900_FREQACTIVE1_1']."</td>
	<td>".$data[$i]['GSM900_FREQACTIVE1_2']."</td>
	<td>".$data[$i]['GSM900_FREQACTIVE1_3']."</td>
	<td>".$data[$i]['GSM900_ANTENNA_TYPE1_1']."</td>
	<td>".$data[$i]['GSM900_ANTENNA_TYPE1_2']."</td>
	<td>".$data[$i]['GSM900_CONFIG1']."</td>";
	
	echo "<td><b>PL GSM900 / SEC2:</b></td>
	<td>".$data[$i]['GSM900_TMA2']."</td>	
	<td>".$data[$i]['GSM900_TRU_INST2_1']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE2_1']."</td>
	<td>".$data[$i]['GSM900_TRU_INST2_2']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE2_2']."</td>
	<td>".$data[$i]['GSM900_TRU_INST2_3']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE2_3']."</td>
	<td>".$data[$i]['GSM900_TRU_INST2_4']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE2_4']."</td>
	<td>".$data[$i]['GSM900_TRU_INST2_5']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE2_5']."</td>
	<td>".$data[$i]['GSM900_TRU_INST2_6']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE2_6']."</td>
	<td>".$data[$i]['GSM900_FREQACTIVE2_1']."</td>
	<td>".$data[$i]['GSM900_FREQACTIVE2_2']."</td>
	<td>".$data[$i]['GSM900_FREQACTIVE2_3']."</td>
	<td>".$data[$i]['GSM900_ANTENNA_TYPE2_1']."</td>
	<td>".$data[$i]['GSM900_ANTENNA_TYPE2_2']."</td>
	<td>".$data[$i]['GSM900_CONFIG2']."</td>";
	
	echo "<td><b>PL GSM900 / SEC3:</b></td>
	<td>".$data[$i]['GSM900_TMA3']."</td>
	<td>".$data[$i]['GSM900_TRU_INST3_1']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE3_1']."</td>
	<td>".$data[$i]['GSM900_TRU_INST3_2']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE3_2']."</td>
	<td>".$data[$i]['GSM900_TRU_INST3_3']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE3_3']."</td>
	<td>".$data[$i]['GSM900_TRU_INST3_4']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE3_4']."</td>
	<td>".$data[$i]['GSM900_TRU_INST3_5']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE3_5']."</td>
	<td>".$data[$i]['GSM900_TRU_INST3_6']."</td>
	<td>".$data[$i]['GSM900_TRU_TYPE3_6']."</td>
	<td>".$data[$i]['GSM900_FREQACTIVE3_1']."</td>
	<td>".$data[$i]['GSM900_FREQACTIVE3_2']."</td>
	<td>".$data[$i]['GSM900_FREQACTIVE3_3']."</td>
	<td>".$data[$i]['GSM900_ANTENNA_TYPE3_1']."</td>
	<td>".$data[$i]['GSM900_ANTENNA_TYPE3_2']."</td>
	<td>".$data[$i]['GSM900_CONFIG3']."</td>";
	
	
	echo "<td><b>PL GSM1800:</b></td>
	<td>".$data[$i]['DCS_CABTYPE']."</td>
	<td>".$data[$i]['DCS_DXUTYPE1']."</td>
	<td>".$data[$i]['DCS_DXUTYPE2']."</td>
	<td>".$data[$i]['DCS_DXUTYPE3']."</td>
	<td>".$data[$i]['DCS_BBS']."</td>";
	
	echo "<td><b>PL GSM1800 / SEC1:</b></td>
	<td>".$data[$i]['GSM1800_TMA1']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST&_1']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE1_1']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST1_2']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE1_2']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST1_3']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE1_3']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST1_4']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE1_4']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST1_5']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE1_5']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST1_6']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE1_6']."</td>
	<td>".$data[$i]['GSM1800_FREQACTIVE1_1']."</td>
	<td>".$data[$i]['GSM1800_FREQACTIVE1_2']."</td>
	<td>".$data[$i]['GSM1800_FREQACTIVE1_3']."</td>
	<td>".$data[$i]['GSM1800_ANTENNA_TYPE1_1']."</td>
	<td>".$data[$i]['GSM1800_ANTENNA_TYPE1_2']."</td>
	<td>".$data[$i]['GSM1800_CONFIG1']."</td>";
		
	echo "<td><b>PL GSM1800 / SEC2:</b></td>
	<td>".$data[$i]['GSM1800_TMA2']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST2_1']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE2_1']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST2_2']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE2_2']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST2_3']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE2_3']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST2_4']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE2_4']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST2_5']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE2_5']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST2_6']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE2_6']."</td>
	<td>".$data[$i]['GSM1800_FREQACTIVE2_1']."</td>
	<td>".$data[$i]['GSM1800_FREQACTIVE2_2']."</td>
	<td>".$data[$i]['GSM1800_FREQACTIVE2_3']."</td>
	<td>".$data[$i]['GSM1800_ANTENNA_TYPE2_1']."</td>
	<td>".$data[$i]['GSM1800_ANTENNA_TYPE2_2']."</td>
	<td>".$data[$i]['GSM1800_CONFIG2']."</td>";
	
	echo "<td><b>PL GSM1800 / SEC3:</b></td>
	<td>".$data[$i]['GSM1800_TMA3']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST3_1']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE3_1']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST3_2']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE3_2']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST3_3']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE3_3']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST3_4']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE3_4']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST3_5']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE3_5']."</td>
	<td>".$data[$i]['GSM1800_TRU_INST3_6']."</td>
	<td>".$data[$i]['GSM1800_TRU_TYPE3_6']."</td>
	<td>".$data[$i]['GSM1800_FREQACTIVE3_1']."</td>
	<td>".$data[$i]['GSM1800_FREQACTIVE3_2']."</td>
	<td>".$data[$i]['GSM1800_FREQACTIVE3_3']."</td>
	<td>".$data[$i]['GSM1800_ANTENNA_TYPE3_1']."</td>
	<td>".$data[$i]['GSM1800_ANTENNA_TYPE3_2']."</td>
	<td>".$data[$i]['GSM1800_CONFIG3']."</td>";
	
	
	//CURRENT OUTPUT
	
	echo "<td><b>CUR GSM900:</b></td>
	<td>".$data[$i]['cur_EGS_CABTYPE']."</td>
	<td>".$data[$i]['cur_EGS_DXUTYPE1']."</td>
	<td>".$data[$i]['cur_EGS_DXUTYPE2']."</td>
	<td>".$data[$i]['cur_EGS_DXUTYPE3']."</td>
	<td>".$data[$i]['cur_EGS_BBS']."</td>";
	
	echo "<td><b>CUR GSM900 / SEC1:</b></td>
	<td>".$data[$i]['cur_GSM900_TMA1']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST1_1']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE1_1']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST1_2']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE1_2']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST1_3']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE1_3']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST1_4']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE1_4']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST1_5']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE1_5']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST1_6']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE1_6']."</td>
	<td>".$data[$i]['cur_GSM900_FREQACTIVE1_1']."</td>
	<td>".$data[$i]['cur_GSM900_FREQACTIVE1_2']."</td>
	<td>".$data[$i]['cur_GSM900_FREQACTIVE1_3']."</td>
	<td>".$data[$i]['cur_GSM900_ANTENNA_TYPE1_1']."</td>
	<td>".$data[$i]['cur_GSM900_ANTENNA_TYPE1_2']."</td>
	<td>".$data[$i]['cur_GSM900_CONFIG1']."</td>";
	
	echo "<td><b>CUR GSM900 / SEC2:</b></td>
	<td>".$data[$i]['cur_GSM900_TMA2']."</td>	
	<td>".$data[$i]['cur_GSM900_TRU_INST2_1']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE2_1']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST2_2']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE2_2']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST2_3']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE2_3']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST2_4']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE2_4']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST2_5']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE2_5']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST2_6']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE2_6']."</td>
	<td>".$data[$i]['cur_GSM900_FREQACTIVE2_1']."</td>
	<td>".$data[$i]['cur_GSM900_FREQACTIVE2_2']."</td>
	<td>".$data[$i]['cur_GSM900_FREQACTIVE2_3']."</td>
	<td>".$data[$i]['cur_GSM900_ANTENNA_TYPE2_1']."</td>
	<td>".$data[$i]['cur_GSM900_ANTENNA_TYPE2_2']."</td>
	<td>".$data[$i]['cur_GSM900_CONFIG2']."</td>";
	
	echo "<td><b>CUR GSM900 / SEC3:</b></td>
	<td>".$data[$i]['cur_GSM900_TMA3']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST3_1']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE3_1']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST3_2']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE3_2']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST3_3']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE3_3']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST3_4']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE3_4']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST3_5']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE3_5']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_INST3_6']."</td>
	<td>".$data[$i]['cur_GSM900_TRU_TYPE3_6']."</td>
	<td>".$data[$i]['cur_GSM900_FREQACTIVE3_1']."</td>
	<td>".$data[$i]['cur_GSM900_FREQACTIVE3_2']."</td>
	<td>".$data[$i]['cur_GSM900_FREQACTIVE3_3']."</td>
	<td>".$data[$i]['cur_GSM900_ANTENNA_TYPE3_1']."</td>
	<td>".$data[$i]['cur_GSM900_ANTENNA_TYPE3_2']."</td>
	<td>".$data[$i]['cur_GSM900_CONFIG3']."</td>";
	
	echo "<td><b>CUR GSM1800:</b></td>
	<td>".$data[$i]['cur_DCS_CABTYPE']."</td>
	<td>".$data[$i]['cur_DCS_DXUTYPE1']."</td>
	<td>".$data[$i]['cur_DCS_DXUTYPE2']."</td>
	<td>".$data[$i]['cur_DCS_DXUTYPE3']."</td>
	<td>".$data[$i]['cur_DCS_BBS']."</td>";
	
	echo "<td><b>CUR GSM1800 / SEC1:</b></td>
	<td>".$data[$i]['cur_GSM1800_TMA1']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST&_1']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE1_1']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST1_2']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE1_2']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST1_3']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE1_3']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST1_4']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE1_4']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST1_5']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE1_5']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST1_6']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE1_6']."</td>
	<td>".$data[$i]['cur_GSM1800_FREQACTIVE1_1']."</td>
	<td>".$data[$i]['cur_GSM1800_FREQACTIVE1_2']."</td>
	<td>".$data[$i]['cur_GSM1800_FREQACTIVE1_3']."</td>
	<td>".$data[$i]['cur_GSM1800_ANTENNA_TYPE1_1']."</td>
	<td>".$data[$i]['cur_GSM1800_ANTENNA_TYPE1_2']."</td>
	<td>".$data[$i]['cur_GSM1800_CONFIG1']."</td>";
		
	echo "<td><b>CUR GSM1800 / SEC2:</b></td>
	<td>".$data[$i]['cur_GSM1800_TMA2']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST2_1']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE2_1']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST2_2']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE2_2']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST2_3']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE2_3']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST2_4']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE2_4']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST2_5']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE2_5']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST2_6']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE2_6']."</td>
	<td>".$data[$i]['cur_GSM1800_FREQACTIVE2_1']."</td>
	<td>".$data[$i]['cur_GSM1800_FREQACTIVE2_2']."</td>
	<td>".$data[$i]['cur_GSM1800_FREQACTIVE2_3']."</td>
	<td>".$data[$i]['cur_GSM1800_ANTENNA_TYPE2_1']."</td>
	<td>".$data[$i]['cur_GSM1800_ANTENNA_TYPE2_2']."</td>
	<td>".$data[$i]['cur_GSM1800_CONFIG2']."</td>";
	
	echo "<td><b>CUR GSM1800 / SEC3:</b></td>
	<td>".$data[$i]['cur_GSM1800_TMA3']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST3_1']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE3_1']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST3_2']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE3_2']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST3_3']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE3_3']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST3_4']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE3_4']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST3_5']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE3_5']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_INST3_6']."</td>
	<td>".$data[$i]['cur_GSM1800_TRU_TYPE3_6']."</td>
	<td>".$data[$i]['cur_GSM1800_FREQACTIVE3_1']."</td>
	<td>".$data[$i]['cur_GSM1800_FREQACTIVE3_2']."</td>
	<td>".$data[$i]['cur_GSM1800_FREQACTIVE3_3']."</td>
	<td>".$data[$i]['cur_GSM1800_ANTENNA_TYPE3_1']."</td>
	<td>".$data[$i]['cur_GSM1800_ANTENNA_TYPE3_2']."</td>
	<td>".$data[$i]['cur_GSM1800_CONFIG3']."</td>";
	
	}		
		
	echo "</tr>";
}
?>
</tbody>
</table>
<?	



} //reportnr !=""
?>
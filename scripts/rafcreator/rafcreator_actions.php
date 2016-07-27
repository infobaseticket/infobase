<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_RF","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['action']=="createRafs"){
  //$data=str_getcsv($_POST['csvdata']);
  //echo "<pre>".print_r($data)."<pre>";
  if ($_POST['sitelist']==""){
    $type='error';
    $message.='You did not provide the sites with their corresponding NET1 link!<br>';
  }
   if ($_POST['RAFTYPE']==""){
    $type='error';
    $message.='You did not provide a RAF TYPE!<br>';
  }
  if ($_POST['RFINFO']==""){
    $type='error';
    $message.='You did not provide RF INFO!<br>';
  }
  if ($_POST['COMMERCIAL']==""){
    $type='error';
    $message.='You did not provide a COMMERCIAL PHASE!<br>';
  }
  if ($_POST['BUFFER']=="1" && $_POST['TRXREQUIREMENTS']==''){
    $type='error';
    $message.='You did not provide the TRX requirements for BUFFER sites!<br>';
  }

  if ($_POST['BUFFER']=="1" && ($_POST['OTHER_INP']!='NA' or $_POST['RADIO_INP']!='OK' 
    or $_POST['TXMN_INP']!='OK' or $_POST['ACQ_PARTNER']!='NA' or $_POST['PARTNER_INP']!='OK' or $_POST['BCS_NET1']=='NOT OK'
    or $_POST['PARTNER_ACQUIRED']!='OK' or $_POST['TXMN_ACQUIRED']!='OK')){
    $type='error';
    $message.='You are trying to manipulate Infobase! For BUFFER sites, acquisition is skipped.<br>';
  }
  if ($_POST['BUFFER']=="1" && $_POST['RADIO_FUND_G9']=='' && $_POST['RADIO_FUND_G18']==''
    && $_POST['RADIO_FUND_U9']=='' && $_POST['RADIO_FUND_U21']==''
    && $_POST['RADIO_FUND_L8']=='' && $_POST['RADIO_FUND_L18']=='' && $_POST['RADIO_FUND_L26']==''){
    $type='error';
    $message.='You did not provide the FUNDED TECHNOLOGIES for BUFFER sites!<br>';
  }

    if ($_POST['BAND_900']=='' && $_POST['BAND_1800']=='' && $_POST['BAND_UMTS900']=='' 
    && $_POST['BAND_UMTS']=='' && $_POST['BAND_LTE1800']=='' && $_POST['BAND_LTE800']=='' 
    && $_POST['BAND_LTE2600']=='' && $_POST['BUFFER']!="1"){
    $type='error';
    $message.='You did not provide the RADIO TECHNOLOGIES!<br>';
  }
  if(($_POST['TXMN_ACQUIRED']!="NA" or $_POST['TXMN_INP']!="NA") && ($_POST['RFINFO']=="Mini RPT Coiler" or $_POST['RFINFO']=="Mini RPT Andrew" or $_POST['TYPE']=="Dismantling")){
    $type='error';
    $message.='For mini repeaters, TXMN INP and TXMN ACQUISITION should be NA!<br>';
  }

  if($type=="error"){
    $res["type"]='error';
    $res["message"]=$message;
    echo json_encode($res);
    die;
  }

  $data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['sitelist']));
  //We now analyse the input
  $i=0;
  foreach ($data as $key => $result) 
  {
    //echo $result[0]." - ".$result[1]."<br>";
    $SITEID=$result[0];
    $NET1_LINK=$result[1];
    $XCOORD=$result[2];
    $YCOORD=$result[3];
    $funding="";
    if($SITEID!=''){
      //We first set the default values, then we can override with POSTED values
      if (substr_count($guard_groups, 'Base_other')==1){
        $other_input="OK";
        $other_input_by=$guard_username;
        $other_input_date="SYSDATE";
      }else{
        $other_input="NA";
        $other_input_by="";
        $other_input_date="''";
      }
      $radio_input="NOT OK";
      $radio_input_by="";
      $radio_input_date="''";
      $txmn_input="NOT OK";       
      $txmn_input_by="";
      $txmn_input_date="''";
      $net1_link="NOT OK";
      $net1_link_by="";
      $net1_link_date="''"; 
      $partner_input="NOT OK";
      $partner_input_by="";
      $partner_input_date="''";
      $bcs_net1="NOT OK";
      $bcs_net1_by="";
      $bcs_net1_date="''";
      $txmn_los="NOT OK";
      $txmn_los_by="";
      $txmn_los_date="''";
      $net1_lbp="NOT OK";
      $partner_acquired="NOT OK";
      $partner_acquired_by="";
      $partner_acquired_date="''";
      $txmn_acquired="NOT OK";
      $txmn_acquired_by="";
      $txmn_acquired_date="''";
      $net1_acquired="NOT OK";
      $radio_fund="NOT OK";
      $radio_fund_by="";
      $radio_fund_date="''";
      $net1_fund="NOT OK";
      $partner_pac="NOT OK";
      $partner_pac_by="";
      $partner_pac_date="''";
      $rf_pac="NOT OK";
      $rf_pac_by="";
      $rf_pac_date="''";
      $net1_pac="NOT OK";  
      $acq_partner='NOT OK';
      $acq_partner_by="";
      $acq_partner_date="''";
      $con_partner="NOT OK";
      $con_partner_by="";
      $con_partner_date="''";

      $pocon_delivery='NA';
      $pocon_partner='NA';

      if ($_POST['RAFTYPE']=="MOV Upgrade" or $_POST['RAFTYPE']=="TECHNO Upgrade" or substr_count($_POST['RAFTYPE'], 'New')=="1" ){
        $pocon_delivery='NOT OK';
        $pocon_partner='NOT OK';
      }
      

      if ($_POST['BUFFER']==1){
        if ($_POST['RADIO_FUND_G9']=="G9"){
          $funding="G9,".$funding;
        }
        if ($_POST['RADIO_FUND_G18']=="G18"){
          $funding="G18,".$funding;
        }
        if ($_POST['RADIO_FUND_U9']=="U9"){
          $funding="U9,".$funding;
        }
        if ($_POST['RADIO_FUND_U21']=="U21"){
          $funding="U21,".$funding;
        }
        if ($_POST['RADIO_FUND_L8']=="L8"){
          $funding="L8,".$funding;
        }
        if ($_POST['RADIO_FUND_L18']=="L18"){
          $funding="L18,".$funding;
        }
        if ($_POST['RADIO_FUND_L26']=="L26"){
          $funding="L26,".$funding;
        }
        $RADIO_FUND=substr($funding,0,-1);
        //force buffer values
        $other_input="NA";
        $other_input_by="";
        $other_input_date="''";
        $radio_input="OK";
        $radio_input_by=$guard_username;
        $radio_input_date="SYSDATE";
        $txmn_input="OK";       
        $txmn_input_by=$guard_username;
        $txmn_input_date="SYSDATE";
        $acq_partner='NA';    
        $acq_partner_by="";
        $acq_partner_date="''";
        $partner_input="OK";
        $partner_input_by=$guard_username;
        $partner_input_date="SYSDATE";
        $bcs_net1="OK";
        $bcs_net1_by=$guard_username;
        $bcs_net1_date="SYSDATE";
        $txmn_los="OK";
        $txmn_los_by=$guard_username;
        $txmn_los_date="SYSDATE";
        $net1_lbp="NOT OK";
        $partner_acquired="OK";
        $partner_acquired_by=$guard_username;
        $partner_acquired_date="SYSDATE";
        $txmn_acquired="OK";
        $txmn_acquired_by=$guard_username;
        $txmn_acquired_date="SYSDATE";
        $net1_acquired="NA";
        $radio_fund=$RADIO_FUND;
        $radio_fund_by=$guard_username;
        $radio_fund_date="SYSDATE";
        $net1_fund=$_POST['NET1_FUND'];
        if($_POST['NET1_FUND']=='OK'){
          $net1_fund_by=$guard_username;
          $net1_fund_date="SYSDATE";
        }
        $partner_pac=$_POST['PARTNER_PAC'];
        if($_POST['PARTNER_PAC']=='OK'){
          $partner_pac_by=$guard_username;
          $partner_pac_date="SYSDATE";
        }
        $rf_pac=$_POST['RF_PAC'];
        if($_POST['RF_PAC']=='OK'){
          $rf_pac_by=$guard_username;
          $rf_pac_date="SYSDATE";
        }
        $net1_pac="NOT OK";
        $net1_pac_by="";
        $net1_pac_date="''";
        $buffer_message="Don't forget to add the TRX requirements!";

        if ($_POST['RAFTYPE']=='DISM Upgrade'){
          $net1_lbp='NA';
        }
      
      }else{

        $other_input=$_POST['OTHER_INP'];
        if($_POST['OTHER_INP']=='OK'){
          $other_input_by=$guard_username;
          $other_input_date="SYSDATE";
        }
        $radio_input=$_POST['RADIO_INP'];
        if($_POST['RADIO_INP']=='OK'){
          $radio_input_by=$guard_username;
          $radio_input_date="SYSDATE";
        }
        $txmn_input=$_POST['TXMN_INP']; 
        if($_POST['TXMN_INP']=='OK'){     
          $txmn_input_by=$guard_username;
          $txmn_input_date="SYSDATE";
        }
        $acq_partner=$_POST['ACQ_PARTNER']; 
        if($_POST['ACQ_PARTNER']!='NA'){     
          $acq_partner_by=$guard_username;
          $acq_partner_date="SYSDATE";
        }
        $net1_link=$_POST['NET1_LINK']; 
        if($_POST['NET1_LINK']=='OK'){
          $net1_link_by=$guard_username;
          $net1_link_date="SYSDATE";
        }
        $partner_input=$_POST['PARTNER_INP'];
        if($_POST['PARTNER_INP']=='OK'){ 
          $partner_input_by=$guard_username;
          $partner_input_date="SYSDATE";
        }
        $bcs_net1=$_POST['BCS_NET1'];
        if($_POST['BCS_NET1']=='OK'){ 
          $bcs_net1_by=$guard_username;
          $bcs_net1_date="SYSDATE";
        }
        $con_partner=$_POST['CON_PARTNER']; 
        if($_POST['CON_PARTNER']!='NA'){     
          $con_partner_by=$guard_username;
          $con_partner_date="SYSDATE";
        }
        $txmn_los=$_POST['TXMN_LOS'];
        if($_POST['TXMN_LOS']=='OK'){ 
          $txmn_los_by=$guard_username;
          $txmn_los_date="SYSDATE";
        }
        $net1_lbp="NOT OK";
        $partner_acquired=$_POST['PARTNER_ACQUIRED'];
        if($_POST['PARTNER_ACQUIRED']=='OK'){ 
          $partner_acquired_date="SYSDATE";
          $partner_acquired_by=$guard_username;
        }
        $txmn_acquired=$_POST['TXMN_ACQUIRED'];
        if($_POST['TXMN_ACQUIRED']=='OK'){ 
          $txmn_acquired_by=$guard_username;
          $txmn_acquired_date="SYSDATE";
        }
        $net1_acquired=$_POST['NET1_ACQUIRED'];
        $radio_fund=$_POST['RADIO_FUND'];
        if($_POST['RADIO_FUND']=='OK'){ 
          $radio_fund_by=$guard_username;
          $radio_fund_date="SYSDATE";
        }
        $net1_fund=$_POST['NET1_FUND'];
        if($_POST['NET1_FUND']=='OK'){ 
          $net1_fund_by=$guard_username;
          $net1_fund_date="SYSDATE";
        }
        $partner_pac=$_POST['PARTNER_PAC'];
        if($_POST['PARTNER_PAC']=='OK'){ 
         $partner_pac_by=$guard_username;
          $partner_pac_date="SYSDATE";
        }
        $rf_pac=$_POST['RF_PAC'];
        if($_POST['RF_PAC']=='OK'){ 
          $rf_pac_by=$guard_username;
          $rf_pac_date="SYSDATE";
        }
        $net1_pac="NOT OK";
        $net1_pac_by="";
        $net1_pac_date="''";
        $buffer_message="Don't forget to add the TRX requirements!";
      }

      if ($_POST['TYPE']=="Dismantling"){
        $txmn_los="NA";
        $txmn_acquired="NA";
      }

      $query = "INSERT INTO INFOBASE.BSDS_RAFV2 (
         RAFID, SITEID, CREATION_DATE,CREATED_BY, UPDATE_DATE, UPDATE_BY,
         CANDIDATE, BUFFER,
         TYPE, RFINFO, JUSTIFICATION, COMMERCIAL,
         OTHER_INP, OTHER_INP_DATE, OTHER_INP_BY, 
         RADIO_INP, RADIO_INP_DATE, RADIO_INP_BY, 
         TXMN_INP, TXMN_INP_DATE, TXMN_INP_BY,
         ACQ_PARTNER, ACQ_PARTNER_DATE, ACQ_PARTNER_BY,
         NET1_LINK, NET1_LINK_DATE, NET1_LINK_BY, 
         PARTNER_INP, PARTNER_INP_DATE, PARTNER_INP_BY, 
         BCS_NET1, BCS_NET1_DATE, BCS_NET1_BY, 
         NET1_LBP,
         PARTNER_ACQUIRED, PARTNER_ACQUIRED_DATE, PARTNER_ACQUIRED_BY, 
         TXMN_ACQUIRED, TXMN_ACQUIRED_DATE, TXMN_ACQUIRED_BY,
         NET1_ACQUIRED,
         RADIO_FUND, RADIO_FUND_DATE, RADIO_FUND_BY,
         CON_PARTNER, CON_PARTNER_DATE, CON_PARTNER_BY,
         NET1_FUND,
         PARTNER_PAC, PARTNER_PAC_DATE, PARTNER_PAC_BY,
         RF_PAC, RF_PAC_DATE, RF_PAC_BY,
         NET1_PAC, 
         BUDGET_ACQ, BUDGET_CON, DELETED, LOCKEDD,
         POCON_PARTNER,POCON_DELIVERY

         )
      VALUES ('' ,'".$SITEID."' , SYSDATE, '".$guard_username."', '', '',
          '".$_POST['candidate']."','".$_POST['BUFFER']."',
          '".$_POST['RAFTYPE']."','".escape_sq($_POST['RFINFO'])."','".escape_sq($_POST['JUSTIFICATION'])."','".escape_sq($_POST['COMMERCIAL'])."', 
          '".$other_input."', ".$other_input_date.",'".$other_input_by."',
          '".$radio_input."',".$radio_input_date.",'".$radio_input_by."',
          '".$txmn_input."',".$txmn_input_date.",'".$txmn_input_by."',
          '".$acq_partner."',".$acq_partner_date.",'".$acq_partner_by."',
          '".$net1_link."',".$net1_link_date.",'".$net1_link_by."',
          '".$partner_input."',".$partner_input_date.",'".$partner_input_by."',
          '".$bcs_net1."',".$bcs_net1_date.",'".$bcs_net1_by."',
          '".$net1_lbp."',
          '".$partner_acquired."',".$partner_acquired_date.",'".$partner_acquired_by."',
          '".$txmn_acquired."',".$txmn_acquired_date.",'".$txmn_acquired_by."',
          '".$net1_acquired."',
          '".$radio_fund."',".$radio_fund_date.",'".$radio_fund_by."',
          '".$con_partner."',".$con_partner_date.",'".$con_partner_by."',
          '".$net1_fund."',
          '".$partner_pac."',".$partner_pac_date.",'".$partner_pac_by."',
          '".$rf_pac."',".$rf_pac_date.",'".$rf_pac_by."',
          '".$net1_pac."',
          '".$_POST['budget_acq']."','".$_POST['budget_con']."','no','no',
          '".$pocon_partner."',".$pocon_delivery.")";    
      //echo $query."<hr>";
      $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
      if (!$stmt) {
          die_silently($conn_Infobase, $error_str);
      }else{
        OCICommit($conn_Infobase);
        
        $query = "Select MAX(RAFID) AS RAFID from BSDS_RAFV2 WHERE SITEID = '".$SITEID."'";
        $stmt6 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res6);
        if (!$stmt6) {
          die_silently($conn_Infobase, $error_str);
          exit;
        } else {
          OCIFreeStatement($stmt6);
        }
        $rafids.=" ".$res6['RAFID'][0];
        $RAFID=$res6['RAFID'][0];

        if ($_POST['BUFFER']==1){
            $query = "INSERT INTO BSDS_RAF_PARTNER (RAFID,FINAL_RF,FINAL_MICROWAVE,FINAL_CAB,FINAL_BTS,FINAL_OTHER) VALUES ('".$RAFID."','Yes','Yes','Yes','Yes','Yes')";
            $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
            if (!$stmt) {
              die_silently($conn_Infobase, $error_str);
            }else{
              OCICommit($conn_Infobase);
            }
        }

        $query = "INSERT INTO INFOBASE.BSDS_RAF_RADIO (
           RAFID, UPG_DATE, UPG_BY,
           XCOORD, YCOORD, ADDRESS,
           RFPLAN, CONTACT, PHONE,
           SITETYPE, SITESHARING,
           BAND_900, BAND_1800, BAND_UMTS, BAND_UMTS900,BAND_LTE1800,BAND_LTE2600,
           EXPTRAFFIC, FEATURE, PREFERREDINST,
           CABTYPE, CHTRX, SECTORS,
           REPEATER, SECTOR, COVERAGE_OBJECTIVE,
           COVERAGE_DESCR, FLOORS, AREAS,
           SITETYPE2, AREA1_900, AREA1_1800,
           AREA1_UMTS,AREA2_900, AREA2_1800,
           AREA2_UMTS,AREA3_900, AREA3_1800,
           AREA3_UMTS,AREA4_900, AREA4_1800,
           AREA4_UMTS, COVERAGE_TUNNEL, PLANS,
           SHARING, GUIDELINES, COMMENTS,
           INTER_900, INTER_1800,
           INTER_UMTS, THRESHOLD_900, THRESHOLD_1800,
           THRESHOLD_UMTS, COVERAGE_900, COVERAGE_1800,
           COVERAGE_UMTS, TOTCOVERAGE_900, TOTCOVERAGE_1800,
           TOTCOVERAGE_UMTS, POLYMAP, NRSECTORS,
           NRSECTORS_900, NRSECTORS_1800, NRSECTORS_UMTS,
           HMINMAX, HMINMAXRF, ANTBLOCKING,
           ANGLE, RFGUIDES, CONGUIDES,
           TXGUIDES, LOC_NAME1, LOC_ADDRESS1,
           LOC_STRUCTURE1, LOC_PREFER1, LOC_NOTPREFER1,
           LOC_NAME2, LOC_ADDRESS2, LOC_STRUCTURE2,
           LOC_PREFER2, LOC_NOTPREFER2,
           VENDOR2G_GSM1800, VENDOR2G_GSM900, VENDOR3G_UMTS, VENDOR4G_LTE1800,  VENDOR4G_LTE2600, VENDOR3G_UMTS900,
           AREA1_UMTS900, AREA2_UMTS900,  AREA3_UMTS900, AREA4_UMTS900, COVERAGE_UMTS900,
           INTER_UMTS900, THRESHOLD_UMTS900, TOTCOVERAGE_UMTS900,NRSECTORS_UMTS900,
           AREA1_LTE1800, AREA2_LTE1800, AREA3_LTE1800, AREA4_LTE1800, COVERAGE_LTE1800,
           INTER_LTE1800, THRESHOLD_LTE1800, TOTCOVERAGE_LTE1800,NRSECTORS_LTE1800,
           AREA1_LTE2600, AREA2_LTE2600, AREA3_LTE2600, AREA4_LTE2600, COVERAGE_LTE2600,
           INTER_LTE2600, THRESHOLD_LTE2600, TOTCOVERAGE_LTE2600, NRSECTORS_LTE2600,
           BAND_LTE800, AREA1_LTE800, AREA2_LTE800, AREA3_LTE800, AREA4_LTE800, COVERAGE_LTE800,
           INTER_LTE800, THRESHOLD_LTE800, TOTCOVERAGE_LTE800,NRSECTORS_LTE800,VENDOR4G_LTE800)
          VALUES ('".$RAFID."' , SYSDATE, '".$guard_username."',
          '".$XCOORD."', '".$YCOORD."', '".$_POST['ADDRESS']."',
            '".$_POST['RFPLAN']."',  '".$_POST['CONTACT']."',  '".$_POST['PHONE']."',
            '".$_POST['SITETYPE']."',  '".$_POST['SITESHARING']."',
            '".$_POST['BAND_900']."','".$_POST['BAND_1800']."','".$_POST['BAND_UMTS']."','".$_POST['BAND_UMTS900']."','".$_POST['BAND_LTE1800']."','".$_POST['BAND_LTE2600']."',
            '".$_POST['EXPTRAFFIC']."',  '".$_POST['FEATURE']."',  '".$_POST['PREFERREDINST']."',
            '".$_POST['CABTYPE']."',  '".$_POST['CHTRX']."',  '".$_POST['SECTORS']."',
            '".$_POST['REPEATER']."', '".$_POST['SECTOR']."',  '".escape_sq($_POST['COVERAGE_OBJECTIVE'])."',
            '".$COVERAGE_DESCR."',  '".$_POST['FLOORS']."',  '".$_POST['AREAS']."',
            '".$_POST['SITETYPE2']."',  '".$_POST['AREA1_900']."',  '".$_POST['AREA1_1800']."',
            '".$_POST['AREA1_UMTS']."',  '".$_POST['AREA2_900']."',  '".$_POST['AREA2_1800']."',
            '".$_POST['AREA2_UMTS']."',  '".$_POST['AREA3_900']."',  '".$_POST['AREA3_1800']."',
            '".$_POST['AREA3_UMTS']."',  '".$_POST['AREA4_900']."',  '".$_POST['AREA4_1800']."',
            '".$_POST['AREA4_UMTS']."',  '".$_POST['COVERAGE_TUNNEL']."',  '".$_POST['PLANS']."',
            '".$_POST['SHARING']."',  '".$_POST['GUIDELINES']."',  '".escape_sq($_POST['COMMENTS'])."',
            '".$_POST['INTER_900']."',  '".$_POST['INTER_1800']."',
            '".$_POST['INTER_UMTS']."',  '".$_POST['THRESHOLD_900']."',  '".$_POST['THRESHOLD_1800']."',
            '".$_POST['THRESHOLD_UMTS']."',  '".$_POST['COVERAGE_900']."',  '".$_POST['COVERAGE_1800']."',
            '".$_POST['COVERAGE_UMTS']."',  '".$_POST['TOTCOVERAGE_900']."',  '".$_POST['TOTCOVERAGE_1800']."',
            '".$_POST['TOTCOVERAGE_UMTS']."',  '".$_POST['POLYMAP']."',  '".$_POST['NRSECTORS']."',
            '".$_POST['NRSECTORS_900']."',  '".$_POST['NRSECTORS_1800']."',  '".$_POST['NRSECTORS_UMTS']."',
            '".$_POST['HMINMAX']."',  '".$_POST['HMINMAXRF']."',  '".$_POST['ANTBLOCKING']."',
            '".$_POST['ANGLE']."',  '".$_POST['RFGUIDES']."',  '".$_POST['CONGUIDES']."',
            '".$_POST['TXGUIDES']."',  '".$_POST['LOC_NAME1']."',  '".$_POST['LOC_ADDRESS1']."',
            '".$_POST['LOC_STRUCTURE1']."',  '".$_POST['LOC_PREFER1']."',  '".$_POST['LOC_NOTPREFER1']."',
            '".$_POST['LOC_NAME2']."',  '".$_POST['LOC_ADDRESS2']."',  '".$_POST['LOC_STRUCTURE2']."',
            '".$_POST['LOC_PREFER2']."',  '".$_POST['LOC_NOTPREFER2']."',
          '".$_POST['VENDOR2G_GSM1800']."',  '".$_POST['VENDOR2G_GSM900']."', '".$_POST['VENDOR3G_UMTS']."','".$_POST['VENDOR4G_LTE1800']."','".$_POST['VENDOR4G_LTE2600']."',
            '".$_POST['VENDOR3G_UMTS900']."', '".$_POST['AREA1_UMTS900']."', '".$_POST['AREA2_UMTS900']."',
            '".$_POST['AREA3_UMTS900']."', '".$_POST['AREA4_UMTS900']."', '".$_POST['COVERAGE_UMTS900']."',
            '".$_POST['INTER_UMTS900']."', '".$_POST['THRESHOLD_UMTS900']."', '".$_POST['TOTCOVERAGE_UMTS900']."',
            '".$_POST['NRSECTORS_UMTS900']."', '".$_POST['AREA1_LTE1800']."', '".$_POST['AREA2_LTE1800']."',
            '".$_POST['AREA3_LTE1800']."', '".$_POST['AREA4_LTE1800']."', '".$_POST['COVERAGE_LTE1800']."',
            '".$_POST['INTER_LTE1800']."', '".$_POST['THRESHOLD_LTE1800']."', '".$_POST['TOTCOVERAGE_LTE1800']."',
            '".$_POST['NRSECTORS_LTE1800']."' , '".$_POST['AREA1_LTE2600']."', '".$_POST['AREA2_LTE2600']."',
            '".$_POST['AREA3_LTE2600']."', '".$_POST['AREA4_LTE2600']."', '".$_POST['COVERAGE_LTE2600']."',
            '".$_POST['INTER_LTE2600']."', '".$_POST['THRESHOLD_LTE2600']."', '".$_POST['TOTCOVERAGE_LTE2600']."',
            '".$_POST['NRSECTORS_LTE2600']."',
            '".$_POST['BAND_LTE800']."', '".$_POST['AREA1_LTE800']."', '".$_POST['AREA2_LTE800']."', '".$_POST['AREA3_LTE800']."', 
            '".$_POST['AREA4_LTE800']."', '".$_POST['COVERAGE_LTE800']."',
            '".$_POST['INTER_LTE800']."', '".$_POST['THRESHOLD_LTE800']."', '".$_POST['TOTCOVERAGE_LTE800']."','".$_POST['NRSECTORS_LTE800']."',
            '".$_POST['VENDOR4G_LTE800']."')";
        //echo $query."<hr>";
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
            die_silently($conn_Infobase, $error_str);
        }else{
          OCICommit($conn_Infobase);
        }

        $query = "INSERT INTO INFOBASE.BSDS_RAF_TRX (RAFID, DATE_OF_SAVE, UPDATE_BY, REQUIREMENTS)
          VALUES ( '".$RAFID."', SYSDATE, '".$guard_username."','".escape_sq($_POST['TRXREQUIREMENTS'])."')";
        //echo $query;
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
          die_silently($conn_Infobase, $error_str);
        }else{
          OCICommit($conn_Infobase);
        }

        if ($_POST['RAFTYPE']=="MOV Upgrade"){
            $text="LOS of existing link(s) to be checked/confirmed for new dish position(s). If no LOS on the new dish position is possible, please inform BASE SDM and BASE TX. The MOV UPG is put ON-HOLD until the issue is resolved (confirmed by BASE)";
            $query = "INSERT INTO INFOBASE.BSDS_RAF_TXMN (RAFID,UPG_DATE,UPG_BY,SPECIFIC_TXMN)
              VALUES ( '".$RAFID."', SYSDATE, '".$guard_username."','".escape_sq($text)."')";
            //echo $query;
            $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
            if (!$stmt) {
              die_silently($conn_Infobase, $error_str);
            }else{
              OCICommit($conn_Infobase);
            }
        }

        if (substr($SITEID,0,2)=="MT"){
          $query = "INSERT INTO BSDS_RAF_PO VALUES ('".$RAFID."','OK','ACQ',SYSDATE,'')";
            $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
            if (!$stmt) {
              die_silently($conn_Infobase, $error_str);
            }else{
              OCICommit($conn_Infobase);
            }
          $query = "INSERT INTO BSDS_RAF_PO VALUES ('".$RAFID."','OK','CON',SYSDATE,'')";
            $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
            if (!$stmt) {
              die_silently($conn_Infobase, $error_str);
            }else{
              OCICommit($conn_Infobase);
            }
        }     
      } //END SITEID!=''
    }
  } //END FOR
  $res["type"]='info';
  $res["message"]="Following RAF ID's have been created:".$rafids;
  echo json_encode($res);
}
?>

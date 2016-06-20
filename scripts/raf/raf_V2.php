<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
include('raf_procedures.php');

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


if ($_POST['viewtype']=="report"){
    $amountperpage=10;
}else{
     $amountperpage=1000;
}
if ($_POST['page']==""){
    $page=1;
}else if ($_POST['siteID']==''){
    $page=$_POST['page'];
    $start= ($page-1)*$amountperpage +1;
    $end = ($page)*$amountperpage ;
}else{
    $amount_of_pages=1;
    $end=1;
}


//$query=create_query($_POST['siteID'],$_POST['RAFID'],$_POST['region'],$_POST['type'],$_POST['actionby'],$_POST['orderby'],$_POST['order'],$start,$end,$_POST['rfinfo'],$_POST['commercial'],$_POST['allocated'],$_POST['build'],$_POST['deleted']);
$query=create_query2($_POST['siteID'],$_POST['RAFID'],$_POST['region'],$_POST['type'],$_POST['actionby'],$_POST['orderby'],$_POST['order'],$start,$end,$_POST['rfinfo'],$_POST['commercial'],$_POST['allocated'],$_POST['deleted']);
//echo $amountperpage."---".$query."<br>";
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt){
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
    $amount_of_RAFS=count($res1['SITEID']);
    //echo "---".$amount_of_RAFS;
    if ($_POST['totalrafs']==""){
        $totalrafs=$amount_of_RAFS;
    }else{
        $totalrafs=$_POST['totalrafs'];
    }
    if ($_POST['siteID']==''){
        $amount_of_pages=$totalrafs/$amountperpage;
        $amount_of_pages= ceil($amount_of_pages);
    }
    //echo $amount_of_pages;
}

$asbuildDelRAF=0;

if ($amount_of_RAFS>=1){
    $k=0;
    for ($i = 0; $i <$amount_of_RAFS; $i++) {  

        if ($page!="1" or ($page=="1" && $i<$amountperpage))
        {  

            $query3 = "select CONDITIONAL from BSDS_RAF_RADIO where RAFID = '".$res1['RAFID'][$i]."'";
            //echo $query3."<br>";
            $stmt3 = parse_exec_fetch($conn_Infobase, $query3, $error_str, $res3);
            if (!$stmt3) {
                die_silently($conn_Infobase, $error_str);
                exit;
            } else {
                OCIFreeStatement($stmt3);
                if ($res3['CONDITIONAL'][0]!=""){
                    $CONDITIONAL="<a rel='popover' class='tip label label-info' title='CONDITIONALLY OK!' data-content='".$res3['CONDITIONAL'][0]."'>CONDITIONAL!</a>";
                }
            }

            if ($res1['BCS_NET1'][$i]=='NET1LINK'){
                $bcs_net1="<span rel='tooltip' data-placement='bottom' class='tip label label-danger' title='NET1 LINK UPDATE NEEDED!'>NET1LINK!</span>";
            }else{
                $bcs_net1=$res1['BCS_NET1'][$i];
            }

            if ($res1['SAC'][$i]=='BASE' or $res1['SAC'][$i]=='KPNGB' or $res1['TYPE'][$i]=='DISM Upgrade'){ 
                $PO_ACQ="NA"; 
            }else if ($res1['PO_ACQ'][$i]!=''){
                $PO_ACQ="<a href='#' class='tippy' title='".str_replace("//", "\r\n",$res1['PO_ACQ'][$i])."'>OK</a>";
            }else{
                $PO_ACQ="NOT IN SAP";
            }

            if ($res1['PO_CON'][$i]!=''){
                $PO_CON="<a href='#' class='tippy' title='".str_replace("//", "\r\n",$res1['PO_CON'][$i])."'>OK</a>";
            }else if ($res1['TYPE'][$i]=='Dismantling'){
                $PO_CON="NA";
            }else{
                $PO_CON="NOT IN SAP";
            }

            if ($res1['BUFFER'][$i]==1 && $res1['NET1_FUND'][$i]!="NOT OK"){ 
                $bufferchangeAllowed='no';
            }else{
                $bufferchangeAllowed='yes';
            }
           
            $TXMN_INP_SEL="";
            $NET1_CREATED_SEL="";
            $PARTNER_INP_SEL="";
            $RADIO_ACC_SEL="";
            $TXMN_ACC_SEL="";
            $status="";
            $status_special="";
            $status_special2="";
            $select_action="";

            $RF_PAC_info="";
            $BCS_RF_INP_info="";
            $BCS_TX_INP_info="";
            $TXMN_INP_info="";
            $RADIO_FUND_info="";
            $CON_PARTNER_info="";
            $PARTNER_RFPACK_info="";

            if ($res1['TYPE'][$i]=="New Indoor" || $res1['TYPE'][$i]=="Indoor Upgrade" || $res1['TYPE'][$i]=="IND Upgrade" || $res1['TYPE'][$i]=="RPT Upgrade"){
                $raf_type="indoor";
                $PO_ACQ='NA';
            }else{
                $raf_type="outdoor";
            }

            include('raf_color_analysis.php');

            $k++;


            $user_CREATION=getuserdata($res1['CREATED_BY'][$i]);
            if($res1['ACQ_PARTNER'][$i]!='NOT OK' && $res1['ACQ_PARTNER'][$i]!='NA'){
                $user_OTHER_INP_BY=getuserdata($res1['OTHER_INP_BY'][$i]);
            }
            if($res1['RADIO_INP'][$i]!='NOT OK' && $res1['RADIO_INP'][$i]!='NA'){
                $user_RADIO_INP_BY=getuserdata($res1['RADIO_INP_BY'][$i]);
            }
            if ($res1['BUFFER'][$i]!=1){
                if($res1['TXMN_INP'][$i]!='NOT OK' && $res1['TXMN_INP'][$i]!='NA'){  
                    $user_TXMN_INP_BY=getuserdata($res1['TXMN_INP_BY'][$i]); 
                }
                if($res1['PARTNER_INP'][$i]!='NOT OK' && $res1['PARTNER_INP'][$i]!='NA'){
                    $user_PARTNER_INP_BY=getuserdata($res1['PARTNER_INP_BY'][$i]);
                    $BCS_RF_INP_info="<a class='tippy' title='Action since ".$res1['PARTNER_INP_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
                }else{
                    unset($user_PARTNER_INP_BY);
                    $BCS_RF_INP_info='';
                }
                if($res1['PARTNER_ACQ'][$i]!='NOT OK' && $res1['PARTNER_ACQ'][$i]!='NA'){
                    $user_PARTNER_ACQ_BY=getuserdata($res1['PARTNER_ACQUIRED_BY'][$i]);
                }else{
                    unset($user_PARTNER_ACQ_BY);
                }
                if($res1['TXMN_INP'][$i]!='NOT OK' && $res1['TXMN_INP'][$i]!='NA'){
                    $user_TXMN_INP_BY=getuserdata($res1['TXMN_INP_BY'][$i]);
                    $TXMN_INP_info="<a class='tippy' title='Action since ".$res1['RADIO_INP_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
                }else{
                    unset($user_TXMN_INP_BY);
                }
                if($res1['TXMN_ACQUIRED'][$i]!='NOT OK' && $res1['TXMN_ACQUIRED'][$i]!='NA'){
                    $user_TXMN_ACQ_BY=getuserdata($res1['TXMN_ACQUIRED_BY'][$i]);
                    $RADIO_FUND_info="<a class='tippy' title='Action since ".$res1['TXMN_ACQUIRED_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
                }else{
                    unset($user_TXMN_ACQ_BY);
                    $RADIO_FUND_info='';
                }
            }
            if($res1['ACQ_PARTNER'][$i]!='NOT OK' && $res1['ACQ_PARTNER'][$i]!='NA'){
                $user_ACQ_PARTNER_BY=getuserdata($res1['ACQ_PARTNER_BY'][$i]);
            }else{
                unset($user_ACQ_PARTNER_BY);
            }
            if($res1['CON_PARTNER'][$i]!='NOT OK' && $res1['CON_PARTNER'][$i]!='NA'){
                $user_CON_PARTNER_BY=getuserdata($res1['CON_PARTNER_BY'][$i]);   
            }else{
                unset($user_CON_PARTNER_BY);
            }
            if($res1['NET1_LINK'][$i]!='NOT OK' && $res1['NET1_LINK'][$i]!='NA'){         
                $user_NET1_LINK_BY=getuserdata($res1['NET1_LINK_BY'][$i]);  
            }else{
                    unset($user_NET1_LINK_BY);
             }
            if($res1['RADIO_FUND'][$i]!='NOT OK' && $res1['RADIO_FUND'][$i]!='NA'){     
                $user_RADIO_FUND_BY=getuserdata($res1['RADIO_FUND_BY'][$i]);
                $CON_PARTNER_info="<a class='tippy' title='Action since ".$res1['RADIO_FUND_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            }else{
                unset($user_RADIO_FUND_BY);
                $CON_PARTNER_info='';
            }
            if($res1['PARTNER_RFPACK'][$i]!='NOT OK' && $res1['PARTNER_RFPACK'][$i]!='NA'){
                $user_PARTNER_RFPACK_BY=getuserdata($res1['PARTNER_RFPACK_BY'][$i]);
                $RF_PAC_info="<a class='tippy' title='Action since ".$res1['PARTNER_RFPACK_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            }else{
                unset($user_PARTNER_RFPACK_BY);
                $RF_PAC_info='';
            }
            if($res1['BCS_TX_INP'][$i]!='NOT OK' && $res1['BCS_TX_INP'][$i]!='NA'){
                $user_BCS_TX_INP_BY=getuserdata($res1['BCS_TX_INP_BY'][$i]);
                $BCS_TX_INP_info="<a class='tippy' title='Action since ".$res1['PARTNER_INP_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            }else{
                 unset($user_BCS_TX_INP_BY);
                    $BCS_TX_INP_info='';
            }
            if($res1['BCS_RF_INP'][$i]!='NOT OK' && $res1['BCS_RF_INP'][$i]!='NA'){
                $user_BCS_RF_INP_BY=getuserdata($res1['BCS_RF_INP_BY'][$i]);
            }else{
                unset($user_BCS_RF_INP_BY);
            }
            if($res1['RF_PAC'][$i]!='NOT OK' && $res1['RF_PAC'][$i]!='NA'){
                $user_RF_PAC_BY=getuserdata($res1['RF_PAC_BY'][$i]);
            }else{
                 unset($user_RF_PAC_BY);
            }
            if($res1['COF_ACQ'][$i]!='NOT OK' && $res1['COF_ACQ'][$i]!='NA'){
                $user_COF_ACQ_BY=getuserdata($res1['COF_ACQ'][$i]);
            }else{
                unset($user_COF_ACQ_BY);
            }
            if($res1['COF_CON'][$i]!='NOT OK' && $res1['COF_CON'][$i]!='NA'){
                $user_COF_CON_BY=getuserdata($res1['COF_CON_BY'][$i]); 
            }else{
                unset($user_COF_CON_BY);

            }

            $PARTNER_RFPACK_info="<a class='tippy' title='Action since ".$res1['INSERTDATE_CON'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
                
            $user_UPDATE_BY=getuserdata($res1['UPDATE_BY'][$i]);

            if($res1['PARTNER_VALREQ'][$i]!='NOT OK' && $res1['PARTNER_VALREQ'][$i]!='NA'){
                $user_PARTNER_VALREQ_BY=getuserdata($res1['PARTNER_VALREQ_BY'][$i]);
            }

            $ACTION_DO=$res1['ACTION_DO'][$i];
            
            if ($res1['DELETED'][$i]=="yes"){
                $row_color="deleted";
                $row_color2="showhide hide";
                $user_DELETED=getuserdata($res1['DELETE_BY'][$i]);
                $user=$user_DELETED['firstname']." ".$user_DELETED['lastname'];
                $status_screen="<a  title='By $user'>DELETED</a>";
                $status_special="<a  title='By $user'>DELETED</a>";
                $delAction="undelete_raf";
                $deleteTitle="Undelete this RAF";
                $saveAllowed="disabled";
                $status='DELETED!';
                $output_raf='out_deleted';
                $output_raf2='out_deleted2';
                $asbuildDelRAF++;
            }else if (($ACTION_DO=="" or strtoupper($res1['NET1_LINK'][$i])=="END" or strtoupper($res1['RADIO_FUND'][$i])=="END") AND ($_POST['siteID']!='' OR $_POST['RAFID']!='')){
                $row_color="asbuild";
                $row_color2="showhide hide";
                $saveAllowed="disabled";
                $asbuildDelRAF++;
                $output_raf='out_asbuild';
                $output_raf2='out_asbuild2';
                if (strtoupper($res1['NET1_LINK'][$i])=="END" or strtoupper($res1['RADIO_FUND'][$i])=="END"){
                    $status_special="END OF PROCESS";
                }else{
                    $status_special="RAF ASBUILD";
                }
            }else{
                $row_color="";
                $row_color2="";
                $delAction="delete_raf";
                $deleteTitle="Delete this RAF";
                $select_action="selected_RAF";
                $saveAllowed="yes";
                $output_raf='out_normal';
                $output_raf2='out_normal2';
            }

            if (substr($status,0,7)=="ALCATEL"){
                $status_screen="PARTNER ".substr($status,7);
                $ACTION_DO="PARTNER ".substr($status,7);
            }else if ($status_screen!="DELETED"){
                $status_screen=$status;
            }

            if ($res1['TYPE'][$i]=="Adding UMTS900 to UMTS2100 (= Not TECHNO Upgrade)"){
                $type="Add U900 to U2100 Upgrade";
            }else{
                $type=$res1['TYPE'][$i];
            }
            
            if ($_POST['siteID'] && $res1['TYPE'][$i]=="New Replacement" && $res1['DELETED'][$i]!="yes" && $res1['PARTNER_PAC'][$i]=="NOT OK"){
                if (substr_count($guard_groups, 'Base_delivery')!=1 && substr_count($guard_groups, 'Admin')!=1 ){
                //$disable_raf_creation="disabled";
                }
                ?>
                <script language="JavaScript">
                Messenger().post({
                  message: 'WARNING: A REPLACEMENT IS ONGOING! Site is marked to be dismantled<br>',
                  type: 'error',
                  showCloseButton: false
                });
                </script>
                <?php
            }
			
            if($res1['LOCKEDD'][$i]=="yes"){
                $status_special="LOCKED BY BASE DELIVERY";
                $lockTitle="Click to unlock";
                $row_color="locked";
                $lockAction="unlock_raf";
                $saveAllowed="disabled";
            }else{
                $lockTitle="Click to lock";
                $lockAction="lock_raf";
            }
            if (trim($res1['NET1_LINK'][$i])==""){
                $NET1_LINK="NOT SET";
            }else{
                $NET1_LINK=$res1['NET1_LINK'][$i];
            }
            if ($res1['NET1_LINK'][$i]=="BCS CHANGE"){
                $extra_class='danger';
            }else{
                $extra_class='';
            }

            if($res1['RADIO_FUND'][$i]==''){
                $RADIO_FUND='NOT OK';
            }else{
                $RADIO_FUND=$res1['RADIO_FUND'][$i];
            }
            
            ?>
            <div id="rafOutput">
                <div class="modal fade" id='RAFBOX_<?=$res1['RAFID'][$i]?>' role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modalmedium">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
                                <h3 id='myModalLabel'>RAF <?=$res1['RAFID'][$i]?> </h3>
                                <h4 id="modalheader"></h4>
                            </div>
                            <div class="modal-body">
                                <table class='table table-striped table-hover table-condensed'>
                                <thead>
                                    <th>Action</th>
                                    <th>By</th>
                                    <th>Date</th>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>CREATION</td>
                                    <td><?=$user_CREATION['firstname']?> <?=$user_CREATION['lastname']?></td>
                                    <td><?=$res1['CREATION_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>UPDATE</td>
                                    <td><?=$user_UPDATE_BY['firstname']?> <?=$user_UPDATE_BY['lastname']?></td>
                                    <td><?=$res1['UPDATE_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>INITIAL INPUT</td>
                                    <td><?=$user_OTHER_INP_BY['firstname']?> <?=$user_OTHER_INP_BY['lastname']?></td>
                                    <td><?=$res1['OTHER_INP_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>RF INPUT</td>
                                    <td><?=$user_RADIO_INP_BY['firstname']?> <?=$user_RADIO_INP_BY['lastname']?></td>
                                    <td><?=$res1['RADIO_INP_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>TX INPUT</td>
                                    <td><?=$user_TXMN_INP_BY['firstname']?> <?=$user_TXMN_INP_BY['lastname']?></td>
                                    <td><?=$res1['TXMN_INP_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>ACQ PARTNER</td>
                                    <td><?=$user_ACQ_PARTNER_BY['firstname']?> <?=$user_ACQ_PARTNER_BY['lastname']?></td>
                                    <td><?=$res1['ACQ_PARTNER_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>COF ACQ</td>
                                    <td><?=$user_COF_ACQ_BY['firstname']?> <?=$user_COF_ACQ_BY['lastname']?></td>
                                    <td><?=$res1['COF_ACQ_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>NET1 LINK</td>
                                    <td><?=$user_NET1_LINK_BY['firstname']?> <?=$user_NET1_LINK_BY['lastname']?></td>
                                    <td><?=$res1['NET1_LINK_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>PARTNER INPUT</td>
                                    <td><?=$user_PARTNER_INP_BY['firstname']?> <?=$user_PARTNER_INP_BY['lastname']?></td>
                                    <td><?=$res1['PARTNER_INP_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>BCS RF</td>
                                    <td><?=$user_BCS_RF_INP_BY['firstname']?> <?=$user_BCS_RF_INP_BY['lastname']?></td>
                                    <td><?=$res1['BCS_RF_INP_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>BCS TX</td>
                                    <td><?=$user_BCS_TX_INP_BY['firstname']?> <?=$user_BCS_TX_INP_BY['lastname']?></td>
                                    <td><?=$res1['BCS_TX_INP_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>PARTNER ACQUIRED</td>
                                    <td><?=$user_PARTNER_ACQ_BY['firstname']?> <?=$user_PARTNER_ACQ_BY['lastname']?></td>
                                    <td><?=$res1['PARTNER_ACQUIRED_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>TXMN ACQUIRED</td>
                                    <td><?=$user_TXMN_ACQ_BY['firstname']?> <?=$user_TXMN_ACQ_BY['lastname']?></td>
                                    <td><?=$res1['TXMN_ACQUIRED_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>FUND SITE</td>
                                    <td><?=$user_RADIO_FUND_BY['firstname']?> <?=$user_RADIO_FUND_BY['lastname']?></td>
                                    <td><?=$res1['RADIO_FUND_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>CON PARTNER</td>
                                    <td><?=$user_CON_PARTNER_BY['firstname']?> <?=$user_CON_PARTNER_BY['lastname']?></td>
                                    <td><?=$res1['CON_PARTNER_DATE'][$i]?></td>
                                </tr>
                                 <tr>
                                    <td>COF CON</td>
                                    <td><?=$user_COF_CON_BY['firstname']?> <?=$user_COF_CON_BY['lastname']?></td>
                                    <td><?=$res1['COF_CON_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>PARTNER READY FOR RF PACK</td>
                                    <td><?=$user_PARTNER_RFPACK_BY['firstname']?> <?=$user_PARTNER_RFPACK_BY['lastname']?></td>
                                    <td><?=$res1['PARTNER_RFPACK_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>RF PAC</td>
                                    <td><?=$user_RF_PAC_BY['firstname']?> <?=$user_RF_PAC_BY['lastname']?></td>
                                    <td><?=$res1['RF_PAC_DATE'][$i]?></td>
                                </tr>
                                 <tr>
                                    <td>PARTNER VAL REQ.</td>
                                    <td><?=$user_PARTNER_VALREQ_BY['firstname']?> <?=$user_PARTNER_VALREQ_BY['lastname']?></td>
                                    <td><?=$res1['PARTNER_VALREQ_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>DELETED BY KPNGB</td>
                                    <td><?=$res1['DELETE_REASON'][$i]?></td>
                                    <td><?=$res1['DELETE_DATE'][$i]?></td>
                                </tr>
                                <tr>
                                    <td>LOCKED BY KPNGB</td>
                                    <td><?=$res1['LOCKEDD_REASON'][$i]?></td>
                                    <td><?=$res1['LOCKEDD_DATE'][$i]?></td>
                                </tr>
                                </table>

                               
                                <?php if (count($resHist['RAFID'])==0 && $res1['NET1_LINK_REJECT'][$i]=="" && $res1['RF_PAC_REJECT'][$i]=="" && $res1['BCS_NET1_REJECT'][$i]=="" && $res1['BCS_RF_INP_REJECT'][$i]=="" && $res1['BCS_TX_INP_REJECT'][$i]=="" && $res1['BCS_NET1_STOP'][$i]==""
                                && $res1['PARTNER_ACQUIRED_REJECT'][$i]=="" && $res1['NET1_FUND_REJECT'][$i]=="" && $res1['NET1_ACQUIRED_REJECT'][$i]=="" && $res1['TXMN_ACQUIRED_REJECT'][$i]==""
                                ){ ?>
                                No rejections found!
                                <?php }
                                if ($res1['NET1_LINK_REJECT'][$i]!=""){ ?>
                                    <u>Rejection of NET1 LINK</u><br>
                                    <?=$res1['NET1_LINK_REJECT'][$i];?><br>
                                    By: <?=$res1['NET1_LINK_REJECT_BY'][$i];?><br>
                                    Date: <?=$res1['NET1_LINK_REJECT_DATE'][$i];?><br>
                                <? }                 
                                if ($res1['BCS_NET1_STOP'][$i]!=""){ ?>
                                    <u>Rejection of BC stopping</u></u><br>
                                    <?=$res1['BCS_NET1_STOP'][$i];?><br>
                                    By: <?=$res1['BCS_NET1_STOP_BY'][$i];?><br>
                                    Date: <?=$res1['BCS_NET1_STOP_DATE'][$i];?><br>
                                <? } ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $partner='';
                $radio='';
                $cof='';
                $txmn='';
                if (substr_count($res1['ACTION_DO'][$i], 'PARTNER')==1){
                    $partner='active';
                    $file='partner';
                }else if (substr_count($res1['ACTION_DO'][$i], 'BASE RF')==1){
                    $radio='active';
                    $file='radio';
                }else if (substr_count($res1['ACTION_DO'][$i], 'BASE PM')==1){
                    $cof='active';
                    $file='cof';
                }else if (substr_count($res1['ACTION_DO'][$i], 'BASE TXMN')==1){
                    $txmn='active';
                    $file='txmn';
                }

                ?>
                <div class="modal fade" id="rafdetails<?=$res1['SITEID'][$i]?><?=$res1['RAFID'][$i]?>">
                    <div class="modal-dialog modalwide">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4><span class="label label-default">RAF ID: <?=$res1['RAFID'][$i]?>  - <?=$res1['SITEID'][$i]?></span></h4>
                                <ul class="nav nav-pills rafdetails">
                                    <li id="raf_details_other" data-file="other" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">OTHER</a></li>
                                    <li id="raf_details_radio" class="<?=$radio?>" data-file="radio" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">RADIO</a></li>
                                    <li id="raf_details_txmn" class="<?=$txmn?>" data-file="txmn" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">TXMN</a></li>
                                    <li id="raf_details_partner" class="<?=$partner?>" data-file="partner" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">PARTNER</a></li>
                                    <li id="raf_details_cof" class="<?=$cof?>" data-file="cof" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">COF</a></li>
                                    <li id="raf_details_trx" data-file="trx" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">TRX+BPC REQUIREMENTS</a></li>
                                    <li id="raf_details_files" data-file="files" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>"  data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">FILES</a></li>
                                    <li id="raf_details_tracking" data-file="tracking" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>"  data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">TRACKING</a></li>
                                    <li id="raf_details_bcsm" data-file="bcsm" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>"  data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">BCS MODEL</a></li>
                                    <li id="raf_details_history" data-file="history" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>"  data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#"><span class='glyphicon glyphicon-time'></span> ACTIONLOG</a></li>
                                </ul>
                                <span id="modalspinner"></span>
                            </div>
                            <div class="modal-body">
                                <div class="alert" id="messagebox" style="display:none"></div>
                                <div id="RAFcontent<?=$res1['SITEID'][$i]?><?=$res1['RAFID'][$i]?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
             $$output_raf_hidden.="
            <input type='hidden' name='createdby' id='createdby-".$res1['RAFID'][$i]."' value='".$res1['CREATED_BY'][$i]."'>
            <input type='hidden' name='user_TXMN_INP_BY' id='user_TXMN_INP_BY-".$res1['RAFID'][$i]."' value='".$res1['TXMN_INP_BY'][$i]."'>
            <input type='hidden' name='user_RADIO_INP_BY' id='user_RADIO_INP_BY-".$res1['RAFID'][$i]."' value='".$res1['RADIO_INP_BY'][$i]."'>
            <input type='hidden' name='user_RADIO_FUND_BY' id='user_RADIO_FUND_BY-".$res1['RAFID'][$i]."' value='".$res1['RADIO_FUND_BY'][$i]."'>
            <input type='hidden' name='user_PARTNER_PAC_BY' id='user_PARTNER_PAC_BY-".$res1['RAFID'][$i]."' value='".$res1['PARTNER_PAC_BY'][$i]."'>
            <input type='hidden' name='user_PARTNER_INP_BY' id='user_PARTNER_INP_BY-".$res1['RAFID'][$i]."' value='".$res1['PARTNER_INP_BY'][$i]."'>
            <input type='hidden' name='user_PARTNER_ACQUIRED_BY' id='user_PARTNER_ACQUIRED_BY-".$res1['RAFID'][$i]."' value='".$res1['PARTNER_ACQUIRED_BY'][$i]."'>
            <input type='hidden' name='user_TXMN_ACQUIRED_BY' id='user_TXMN_ACQUIRED_BY-".$res1['RAFID'][$i]."' value='".$res1['TXMN_ACQUIRED_BY'][$i]."'>
            <input type='hidden' name='raftype' id='raftype-".$res1['RAFID'][$i]."' value='".$raf_type."'>
            <input type='hidden' name='CON_PARTNER' id='CON_PARTNER-".$res1['RAFID'][$i]."' value='".$res1['CON_PARTNER'][$i]."'>
            <input type='hidden' name='type' id='type-".$res1['RAFID'][$i]."' value='".$type."'>
            <input type='hidden' name='siteid' id='sitename-".$res1['RAFID'][$i]."' value='".$res1['SITEID'][$i]."'>
            <input type='hidden' name='status' id='status-".$res1['RAFID'][$i]."' value='".$status_screen."'>
            <input type='hidden' name='rafid' id='rafid-".$res1['RAFID'][$i]."' value='".$res1['RAFID'][$i]."'>
            <input type='hidden' name='saveAllowed' id='saveAllowed-".$res1['RAFID'][$i]."' value='".$saveAllowed."'>
            <input type='hidden' name='bufferchangeAllowed' id='bufferchangeAllowed-".$res1['RAFID'][$i]."' value='".$bufferchangeAllowed."'>";

            if ($_POST['upgnr']==$res1['NET1_LINK'][$i]  && $res1['NET1_LINK'][$i]!=''){
                $gclass1="style='background-color:yellow;'";
            }else if ($_POST['RAFID']==$res1['RAFID'][$i] && $res1['NET1_LINK'][$i]!=''){
                $gclass2="style='background-color:yellow;'";
            }else{
                $gclass1="";
                $gclass2="";
            }

           
            if($res1['BSDSKEY'][$i]!="" && substr_count($guard_groups, 'Administrators')!=1){
            	$BSDSINFO="<span class='label label-warning'>BSDS: ".$res1['BSDSKEY'][$i]."</span>";
            }else{
                if ($res1['BSDSKEY'][$i]!=''){
                    $BSDSKEY_CHANGE=$res1['BSDSKEY'][$i];
                }else{
                    $BSDSKEY_CHANGE="NOT OK";
                }
            	$BSDSINFO="<span id='BSDS-".$res1['RAFID'][$i]."' class='label label-warning editableSelectItem' data-type='select' data-pk='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."' data-original-title='Provide the corresponding BSDS'>".$BSDSKEY_CHANGE."</span>";
            }

            $$output_raf2.="<table class='table table-bordered tablefixecol table-condensed' style='table-layout: fixed;margin:0;' id='RAFTable".$_POST['siteID'].$res1['RAFID'][$i]."'>
            <colgroup>
                <col style='width: 122px'>
                <col style='width: 55px'>
                <col style='width: 143px'>
                <col style='width: 200px'><!-- action by -->";

            $$output_raf.="<table class='table table-bordered tablefixecol table-condensed' style='table-layout: fixed;margin:0;' id='RAFTable".$_POST['siteID'].$res1['RAFID'][$i]."'>
            <colgroup>";
                if($res1['OTHER_INP'][$i]!='NA'){
                 $$output_raf.="<col style='width: 80px'><!-- ops inp -->";
                }
                if($res1['RADIO_INP'][$i]!='NA'){
                 $$output_raf.="<col style='width: 80px'>";
                }
                if ($res1['ACQ_PARTNER'][$i]!="NA"){
                 $$output_raf.="<col style='width: 100px'><!-- sac partner -->";
                }
                 $$output_raf.="<col style='width: 100px'><!-- net1 link -->";

                if($res1['BP_NEEDED'][$i]!='NA'){
                 $$output_raf.="<col style='width: 80px'>";
                }
                if($res1['TXMN_INP'][$i]!='NA'){
                 $$output_raf.="<col style='width: 80px'>";
                }
                
                if($res1['COF_ACQ'][$i]!='NA'){
                 $$output_raf.="<col style='width: 80px'><!-- COF ACQ -->";
                }
                if($PO_ACQ!='NA'){
                 $$output_raf.="<col style='width: 140px'><!-- PO ACQ -->";
                }
                if($res1['PARTNER_INP'][$i]!='NA'){
                 $$output_raf.="<col style='width: 100px'><!-- PARTNER INP -->";
                }
                if ($res1['NET1_A304'][$i]!="NA"){
                 $$output_raf.="<col style='width: 110px'>";
                }
                if ($res1['BCS_TX_INP'][$i]!="NA" or $res1['BCS_RF_INP'][$i]!="NA"){
                 $$output_raf.="<col style='width: 120px'>";
                }
                if ($bcs_net1!="NA"){
                 $$output_raf.="<col style='width: 110px'><!-- bcs status -->";
                }
                if ($res1['NET1_LBP'][$i]!="NA"){
                 $$output_raf.="<col style='width: 110px'><!-- l&bp ok -->";
                }
                if ($res1['PARTNER_ACQUIRED'][$i]!="NA"){
                 $$output_raf.="<col style='width: 100px'>";
                }
                if ($res1['TXMN_ACQUIRED'][$i]!="NA"){
                 $$output_raf.="<col style='width: 80px'>";
                }
                if ($res1['NET1_ACQUIRED'][$i]!="NA"){
                 $$output_raf.="<col style='width: 100px'>";
                }
                if ($res1['PARTNER_DESIGN'][$i]!="NA"){
                 $$output_raf.="<col style='width: 180px'>"; //<!-- deign phase -->
                }
                 $$output_raf.="<col style='width: 180px'>"; //<!-- rf fund site -->
                 $$output_raf.="<col style='width: 100px'>";
                 $$output_raf.="<col style='width: 100px'>";
                 $$output_raf.="<col style='width: 80px'><!-- COF CON -->
                <col style='width: 210px'><!-- PARTNER RF PACK SUBMIT -->
                <col style='width: 100px'>
                <col style='width: 140px'><!-- PO CON -->
                <col style='width: 160px'>
                <col style='width: 160px'>
                <col style='width: 240px'>
                <col style='width: 200px'>
                </colgroup>
                <thead>
                    <tr>";

                $$output_raf2.="</colgroup>
                <thead>
                    <tr class='".$row_color2."'>    
                        <th class='raTheader'>RAF ID</th>
                        <th class='raTheader'>SITEID</th>
                        <th class='raTheader'>TYPE</th>
                        <th class='raTheader'>CURRENT OPEN ACTIONS</th>";

                        if ($res1['OTHER_INP'][$i]!="NA"){
                         $$output_raf.="<th class='acquisition ".$row_color2."'><a  id='H_OTHER_INP".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Input to be provided by BASE RF or OPS' class='tip'>INITIAL INPUT</a></th>";
                        }
                        if ($res1['RADIO_INP'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a  id='H_RADIO_INP".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Input to be provided by BASE RF' class='tip'>RF INPUT</th>";
                        }
                        if ($res1['ACQ_PARTNER'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a  id='H_ACQ_PARTNER".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Acquisition partner to be provided by SDM' class='tip'>SAC PARTNER</a></th>";
                        }
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_NET1_LINK".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Provided by BASE Delivery' class='tip'>NET1 LINK</a></th>";
                        
                        if ($res1['BP_NEEDED'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_BP_NEEDED".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Input to be provided by BASE & PARTNER' class='tip'>ACQ NEEDED</th>";
                        }
                        if ($res1['TXMN_INP'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_TXMN_INP".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Input to be provided by BASE Transmission' class='tip'>TX INPUT</th>";
                        }
                        
                        if ($res1['COF_ACQ'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_COF_ACQ".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='COF info for acquisition' class='tip'>COF ACQ</a></th>";
                        }
                        if ($PO_ACQ!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_PO_ACQ".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='PO info automatically imported from SAP. Contact Base delivery if problems' class='tip'>PO ACQ</a></th>";
                        }
                        if ($res1['PARTNER_INP'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_PARTNER_INP".$res1['RAFID'][$i]."'>PARTNER INPUT</a></th>";                        
                        }
                        if ($res1['NET1_A304'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_NET1_A304".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Best Candidate proposed to Base' class='tip'>A304 PARTNER</a></th>";
                        }
                        if ($res1['BCS_TX_INP'][$i]!="NA" or $res1['BCS_RF_INP'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_BCS_INP".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Best Candidate Selection approval by Transmission and Radio' class='tip'>BCS RF & TXMN</a></th>";
                        }
                        if ($bcs_net1!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_BCS_A15".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Best Candidate Selected A15 toggled in NET1' class='tip'>BCS A15</a></th>";
                        }
                        if ($res1['NET1_LBP'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_NET1_LBP".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='LEASE OK TO BUILD / BP OK TO BUILD UA709 / U405 - A105' class='tip'>LS&BP OK</a></th>";
                        }
                        if ($res1['PARTNER_ACQUIRED'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_PARTNER_ACQUIRED".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='Raf acquisition completed' class='tip'>PARTN ACQ. UA711</a></th>";
                        }
                        if ($res1['TXMN_ACQUIRED'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_TXMN_ACQUIRED".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='TXMN Acquisition approval' class='tip'>TX ACQ</a></th>";
                        }
                        if ($res1['NET1_ACQUIRED'][$i]!="NA"){
                        $$output_raf.="<th class='acquisition ".$row_color2."'><a id='H_NET1_ACQUIRED".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='ACQUISITION COMPLETED' class='tip'>N1 ACQ. UA352</a></th>";
                        }
                        if ($res1['PARTNER_DESIGN'][$i]!="NA"){
                        $$output_raf.="<th class='design ".$row_color2."'><a id='H_PARTNER_DESIGN".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='PARTNER DESIGN PHASE COMPLETED' class='tip'>PARTNER DESIGN</a></th>";
                        }
                        $$output_raf.="<th class='construction ".$row_color2."'><a id='H_RADIO_FUND".$res1['RAFID'][$i]."'>RF FUND SITE</a></th>";
                        $$output_raf.="<th class='construction ".$row_color2."'><a id='H_CON_PARTNER".$res1['RAFID'][$i]."'>CON PARTNER</a></th>";
                        $$output_raf.="<th class='construction ".$row_color2."'><a id='H_NET1_FUND".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='SITE FUNDED (to update in NET1)' class='tip'>N1 FUNDING UA353</a></th>";
                        $$output_raf.="<th class='construction ".$row_color2."'><a id='H_COF_CON".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='COF info for construction' class='tip'>COF CON</a></th>";
                        
                        $$output_raf.="<th class='construction ".$row_color2."'><a id='H_PARTNER_RFPAC".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='PARTNER submit RF PACK' class='tip'>PARTNER RF PACK</th>
                        <th class='construction ".$row_color2."'><a id='H_RADIO_RFPAC".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='RAF PAC Complete (to update in NET1)' class='tip'>RF PAC >UA712</a></th>
                        <th class='construction ".$row_color2."'><a id='H_PO_CON".$res1['RAFID'][$i]."'>PO CON</a></th>
                        <th class='construction ".$row_color2."'><a id='H_VAL_REQ".$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='PARTNER validation request' class='tip'>PARTNER VAL. REQ.</th>
                        <th class='construction ".$row_color2."'><a id='H_NET1_FPAC' rel='tooltip' data-placement='bottom' title='DELIVERY VALIDATION' class='tip'>DELIVERY VALIDATION</a></th>
                        <th class='raTheader ".$row_color2."'>COMMERCIAL + PHASE</th>
                        <th class='raTheader ".$row_color2."'>SAC - CON NET1</th>
                    </tr>
                    </thead>
                    <tbody
                <tr id='row_".$res1['RAFID'][$i]."' class='".$row_color." ".$row_color2."'>";
               
                $$output_raf2.="
                </tr>
                    </thead>
                    <tbody
                <tr id='row_".$res1['RAFID'][$i]."' class='".$row_color." ".$row_color2."'>
                <td>
                <div class='btn-group'>
                  <button type='button' class='btn btn-xs btn-default rafnav' data-action='view' data-id='".$res1['RAFID'][$i]."' data-site='".$res1['SITEID'][$i]."' data-type='".$res1['TYPE'][$i]."' data-file='".$file."' data-actiondo='".$ACTION_DO."'><span class='glyphicon glyphicon-eye-open'></span> <span ".$gclass2."><b>".$res1['RAFID'][$i]."</b></span></button>
                  <button type='button' class='btn btn-xs btn-default rafnav' data-action='print' data-id='".$res1['RAFID'][$i]."' data-site='".$res1['SITEID'][$i]."'><span class='glyphicon glyphicon-print'></span></button>";
                   
                  $$output_raf2.="<button type='button' class='btn btn-xs btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                    <span class='caret'></span>
                    <span class='sr-only'>Toggle Dropdown</span>
                  </button>
                  <ul class='dropdown-menu' role='menu'>";
                  if ($res1['NET1_LINK'][$i]!="NOT OK"){
                    $$output_raf2.="<li><button type='button' class='btn btn-xs btn-default rafnav' data-action='net1explorer' data-siteid='".$res1['SITEID'][$i]."' data-net1link='".$res1['NET1_LINK'][$i]."' title='OPEN corresponding NET1 info'><span class='glyphicon glyphicon-th-large'> NET1</span></button></li>";
                    }
                    $$output_raf2.="<li><a href='scripts/raf/raf_details_history.php' class='rafnav' data-action='history' data-id='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."'><span class='glyphicon glyphicon-time'></span> ACTION LOG</a></li>
                    <li><a href='#' data-toggle='modal' data-target='#RAFBOX_".$res1['RAFID'][$i]."'><span class='glyphicon glyphicon-user'></span> USERS</a></li>
                    <li><a href='#' class='validation' title='validation' data-siteupgnr='".$res1['NET1_LINK'][$i]."' data-nbup='NB'><span class='glyphicon glyphicon-check'></span> VALIDATION</a></li>";
                    if (substr_count($guard_groups, 'Administrators')==1
                        || ($guard_username=$res1['CREATED_BY'][$i] && $res1['TXMN_INP'][$i]!="OK" && $res1['NET1_LINK'][$i]=="")){ 
                         $$output_raf2.="<li class='divider'></li>
                     <li><a href='#' class='rafnav' data-action='".$delAction."' data-id='".$res1['RAFID'][$i]."' data-site='".$res1['SITEID'][$i]."' data-net1link='".$res1['NET1_LINK'][$i]."'><span class='glyphicon glyphicon-trash'></span> ".$deleteTitle."</a></li>
                     <li><a href='#' class='rafnav' data-action='".$lockAction."' data-id='".$res1['RAFID'][$i]."' data-site='".$res1['SITEID'][$i]."'><span class='glyphicon glyphicon-lock'></span> ".$lockTitle."</a></li>
                     <li><a href='#' class='rafnav' data-action='refresh' data-id='".$res1['RAFID'][$i]."' data-site='".$res1['SITEID'][$i]."'><span class='glyphicon glyphicon-refresh'></span> REFRESH</a></li>";
                
                    }
                    $$output_raf2.="
                    <li class='divider'></li>";
                    $$output_raf2.="<li><a href='scripts/tracking/tracking.php' class='rafnav' data-action='tracking' data-id='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."'><span class='glyphicon glyphicon-pencil'></span> TRACKING</a></li>
                  </ul>
                </div><br>
                ".$BSDSINFO."<br>
                </td>";

                 $$output_raf2.="<td><div style='min-height:45px;'>".$res1['SITEID'][$i]."</div></td>
                <td><b>".$type."</b><br>";

                if ($status_acqSkipped!=""){
                    $$output_raf2.="<span class='conditional label label-info'>".$status_acqSkipped."</span>";
                }
                $$output_raf2.="</td>
                <td class='".$select_action."'><div style='max-height:40px; overflow-x:auto;'>";

                if ($_POST['siteID'] or $_POST['rafid']){
                    $site=$_POST['siteID'].$_POST['rafid'];
                }else{
                    $site='';
                }
                //To color in orange the ACTION WHICH IS ACTIVE/SHOULD BE DONE
                $actions=explode(',', $res1['ACTION2'][$i]);
                $actions_do=explode(',', $res1['ACTION_DO'][$i]);
                if (is_array($actions)){
                    foreach ($actions as $key => $value) {
                        //echo $res1['RAFID'][$i]." ". $actions_do[$key];
                        $varclass=$value."_class";
                        $$varclass="selected_RAF";
                        $$output_raf2.="<a class='glyphicon glyphicon-play-circle scrollto tippy' title='".$res1['ACTION'][$i]."' data-action='".$value."' data-rafid='".$res1['RAFID'][$i]."' data-siteid='".$site."' aria-hidden='true'  style='margin-right:5px;'></a>";
                        $$output_raf2.="<span id='status_".$res1['RAFID'][$i]."' style='font-size:10px;'>".$actions_do[$key]."</span><br>";
                    }
                }
           
                if ($status_special!="" or $status_special2!=""){
                    $$output_raf2.="<span class='conditional label label-danger'>".$status_special."".$status_special2."</span>";
                }
                $$output_raf2.="</div></td>";


                if ($res1['OTHER_INP'][$i]!="NA"){
                $$output_raf.="<td class='".$OTHER_INP_class."'><span id='OTHER_INP-".$res1['RAFID'][$i]."-".$res1['CREATED_BY'][$i]."' class='tabledata ".$OTHER_INP_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."'>".$res1['OTHER_INP'][$i]."</span></td>";     
                }
                if ($res1['RADIO_INP'][$i]!="NA"){
                $$output_raf.="<td class='".$RADIO_INP_class."'><span id='RADIO_INP-".$res1['RAFID'][$i]."' class='tabledata ".$RADIO_INP_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."'>".$res1['RADIO_INP'][$i]."</span> </td>";
                }
                if ($res1['ACQ_PARTNER'][$i]!="NA"){
                $$output_raf.="<td class='".$ACQ_PARTNER_class."'><span id='ACQ_PARTNER-".$res1['RAFID'][$i]."' class='tabledata ".$ACQ_PARTNER_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."' data-original-title='Provide the ACQUISITION partner'>".$res1['ACQ_PARTNER'][$i]."</span> ".$ACQ_PARTNER_info."</td>";
                }
                $$output_raf.="<td class='".$NET1_LINK_class." ".$extra_class."'><div ".$gclass1." id='NET1_LINK-".$res1['RAFID'][$i]."' class='tabledata ".$NET1_LINK_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."' data-original-title='Provide the cand or UPG nr' style='min-height:45px;'>".$NET1_LINK."</div> ".$NET1_LINK_info."</td>";
                
                 if ($res1['BP_NEEDED'][$i]!="NA"){
                $$output_raf.="<td class='".$BP_NEEDED_class."'><span id='BP_NEEDED-".$res1['RAFID'][$i]."' class='tabledata ".$BP_NEEDED_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."'>".$res1['BP_NEEDED'][$i]."</span> </td>";
                }
                if ($res1['TXMN_INP'][$i]!="NA"){
                $$output_raf.="<td class='".$TXMN_INP_class."'><span id='TXMN_INP-".$res1['RAFID'][$i]."' class='tabledata ".$TXMN_INP_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."'>".$res1['TXMN_INP'][$i]."</span> ".$TXMN_INP_info."</td>";
                }
                
                if ($res1['COF_ACQ'][$i]!="NA"){
                 $$output_raf.="<td class='".$COF_ACQ_class."'><span ".$gclass1." id='COF_ACQ-".$res1['RAFID'][$i]."' class='tabledata ".$COF_ACQ_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."'>".$res1['COF_ACQ'][$i]."</span></td>";
                }
                if ($PO_ACQ!="NA"){
                 $$output_raf.="<td class='".$PO_ACQ_class."'><div id='PO_ACQ-".$res1['RAFID'][$i]."' class='tabledata'>".$PO_ACQ."</div></td>";
                }
                if ($res1['PARTNER_INP'][$i]!="NA"){
                 $$output_raf.="<td class='".$PARTNER_INP_class."'><span id='PARTNER_INP-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_INP_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."'>".$res1['PARTNER_INP'][$i]."</span></td>";
                }
                if ($res1['NET1_A304'][$i]!="NA"){
                 $$output_raf.="<td class='".$NET1_A304_class."'>".$res1['NET1_A304'][$i]."</td>";
                }
                if ($res1['BCS_TX_INP'][$i]!="NA" or $res1['BCS_RF_INP'][$i]!="NA"){
                    $$output_raf.="<td class='".$BCS_INP_class."'>RF: <span id='BCS_RF_INP-".$res1['RAFID'][$i]."' class='tabledata ".$BCS_INP_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."'>".$res1['BCS_RF_INP'][$i]."</span> ".$BCS_RF_INP_info."<br>TX: <span id='BCS_TX_INP-".$res1['RAFID'][$i]."' class='tabledata ".$BCS_INP_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."'>".$res1['BCS_TX_INP'][$i]."</span> ".$BCS_TX_INP_info."</td>";
                }
                if ($bcs_net1!="NA"){
                $$output_raf.="<td class='".$BCS_NET1_class."'>".$bcs_net1."<br>".$CONDITIONAL."</td>";
                }
                if ($res1['NET1_LBP'][$i]!="NA"){
                $$output_raf.="<td class='".$NET1_LBP_class." '><span id='NET1_LBP-".$res1['RAFID'][$i]."'>".$res1['NET1_LBP'][$i]."</span></td>";
                }
                if ($res1['PARTNER_ACQUIRED'][$i]!="NA"){
                $$output_raf.="<td class='".$PARTNER_ACQUIRED_class."'><div id='PARTNER_ACQUIRED-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_ACQUIRED_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['PARTNER_ACQUIRED'][$i]."</div></td>";                
                }
                 if ($res1['TXMN_ACQUIRED'][$i]!="NA"){
                $$output_raf.="<td class='".$TXMN_ACQUIRED_class."'><div id='TXMN_ACQUIRED-".$res1['RAFID'][$i]."' class='tabledata ".$TXMN_ACQUIRED_select."'  data-pk='".$res1['RAFID'][$i]."'>".$res1['TXMN_ACQUIRED'][$i]."</div></td>";
                }
                if ($res1['NET1_ACQUIRED'][$i]!="NA"){
                $$output_raf.="<td class='".$NET1_ACQUIRED_class."'><div id='NET1_ACQUIRED-".$res1['RAFID'][$i]."' class='tabledata ".$NET1_ACQUIRED_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['NET1_ACQUIRED'][$i]."</div></td>";
                }
                if ($res1['PARTNER_DESIGN'][$i]!="NA"){
                $$output_raf.="<td class='".$PARTNER_DESIGN_class."'><div id='PARTNER_DESIGN-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_DESIGN_select."' data-pk='".$res1['RAFID'][$i]."' data-value='".$res1['PARTNER_DESIGN'][$i]."'>".$res1['PARTNER_DESIGN'][$i]."</div></td>";
                }
                $$output_raf.="<td class='".$RADIO_FUND_class."'><div id='RADIO_FUND-".$res1['RAFID'][$i]."' class='tabledata ".$RADIO_FUND_select."' data-pk='".$res1['RAFID'][$i]."' data-value='".$res1['RADIO_FUND'][$i]."'>".$RADIO_FUND."</div> ".$RADIO_FUND_info."</td>";
                $$output_raf.="<td class='".$CON_PARTNER_class."'><div id='CON_PARTNER-".$res1['RAFID'][$i]."' class='tabledata ".$CON_PARTNER_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."' data-original-title='Provide the CONSTRUCTION partner'>".$res1['CON_PARTNER'][$i]."</div> ".$CON_PARTNER_info."</td>";
                $$output_raf.="<td class='".$NET1_FUND_class."'><div id='NET1_FUND-".$res1['RAFID'][$i]."' class='tabledata ".$NET1_FUND_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['NET1_FUND'][$i]."</div></td>";
                $$output_raf.="<td class='".$COF_CON_class."'><span ".$gclass1." id='COF_CON-".$res1['RAFID'][$i]."' class='tabledata ".$COF_CON_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."'>".$res1['COF_CON'][$i]."</span></td>";

                if ($res1['TYPE'][$i]=="DISM Upgrade"){
                     $$output_raf.="<td class='".$PARTNER_RFPAC_class."'>U825/A200: <span id='PARTNER_RFPACK2-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_RFPAC_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['PARTNER_RFPACK2'][$i]."</span><br>U999/A250: <span id='PARTNER_RFPACK-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_RFPAC_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['PARTNER_RFPACK'][$i]."</span> ".$PARTNER_RFPACK_info."</td>";
                }else{
                 $$output_raf.="<td class='".$PARTNER_RFPAC_class."'><div id='PARTNER_RFPACK-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_RFPAC_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['PARTNER_RFPACK'][$i]."</div> ".$PARTNER_RFPACK_info."</td>";
                }
                $$output_raf.="
                <td class='".$RADIO_RFPAC_class."'><div id='RF_PAC-".$res1['RAFID'][$i]."' class='tabledata ".$RADIO_RFPAC_select."'  data-pk='".$res1['RAFID'][$i]."'>".$res1['RF_PAC'][$i]."</div>".$RF_PAC_info."</td>
                <td class='".$PO_CON_class."'><div id='PO_CON-".$res1['RAFID'][$i]."' class='tabledata'>".$PO_CON."</div></td>
                <td class='".$PARTNER_VALREQ_class."'><div id='PARTNER_VALREQ-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_VALREQ_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['PARTNER_VALREQ'][$i]."</div></td>
                <td class='".$NET1_PAC_class." ".$NET1_FAC_class."'>PAC: <span id='NET1_PAC-".$res1['RAFID'][$i]."' class='tabledata ".$NET1_PAC_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."' data-original-title='Set to OK to put in NET1'>".$res1['NET1_PAC'][$i]."</span><br>
                    FAC: <span id='NET1_FAC-".$res1['RAFID'][$i]."' class='tabledata ".$NET1_FAC_select."' data-type='select' data-pk='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."' data-original-title='Set to OK to put in NET1'>".$res1['NET1_FAC'][$i]."</span></td>
                <td>".$res1['PHASE_NET1'][$i]."</div></td>
                <td><div id='SACCON-".$res1['RAFID'][$i]."'>Acquisition: ".$res1['SAC'][$i]."<br>Construction: ".$res1['CON'][$i]."</div></td>
                </tr>
                </tbody>
                </table>";

                $$output_raf2.="</tr>
                </tbody>
                </table>";
                $CONDITIONAL=""; //<div id='COMMERCIAL-".$res1['RAFID'][$i]."'>".$res1['COMMERCIAL'][$i]."<br>

        }//END: if ($page!="1" or ($page=="1" && $i<$amountperpage)){
    }//END FOR LOOP
}else{
    $$output_raf="NO RAF found!";
}
?>

<form id='rafform'>
<? echo $$output_raf_hidden; ?>
<input type="hidden" name="siteID" value="<?=$_POST['siteID']?>" id="rafSiteID">
<?php
if ($_POST['siteID'] && (substr_count($guard_groups, 'Base')!=0 or substr_count($guard_groups, 'Administrator')==1 )) { 
?>
<button class="btn btn-xs btn-default rafnav pull-left" <?=$disable_raf_creation?> href='scripts/raf/raf_details_other.php' data-siteid='<?=$_POST['siteID']?>' data-action='newraf'><span class="glyphicon glyphicon-plus-sign"></span> Add new RAF</button>
<?php } ?>


 

<button type="button" class="btn btn-default btn-xs rightArrowRAF" data-scrollid="scroll<?=$_POST['siteID']?><?=$_POST['rafid']?>">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</button>
&nbsp;&nbsp;
<button type="button" class="btn btn-default btn-xs leftArrowRAF" data-scrollid="scroll<?=$_POST['siteID']?><?=$_POST['rafid']?>" style="margin:0 5px 0 5px;">
  <span class="glyphicon glyphicon-backward" aria-hidden="true" ></span>
</button>

<?php
if ($_POST['siteID'] or $_POST['rafid']) { 
?>
<button class="btn btn-xs btn-default rafnav pull-right"  title='Show deleted and As Build' href='scripts/raf/raf_details_other.php'  data-action='showhidedeleted'><span class="glyphicon glyphicon-eye-open"></span> <?=$asbuildDelRAF?> of <?=$totalrafs?> RAF's</button>
<?php }else{ ?>
<span class="label label-default pull-left"><?=$totalrafs?> RAFs found</span>
<?php } ?>

<br><br>
<div class="row" style="margin-left:5px;">
  <div class="col-md-5" style="overflow:auto;background-color: #FFFFFF;padding: 0 0 120px;">
  <?=$out_normal2?>
  <?=$out_asbuild2?>
  <?=$out_deleted2?><br>
  <?php
  if ($amount_of_pages>1){ ?>
    <ul class="pagination pagination-sm pull-left"> 
    <?php
    if ($page==1){
        $prevclass="disabled";
    }

    ?>
    <li class="rafpaging <?=$prevclass?>" data-page="<?php echo $page-1; ?>"><a href="#">Prev</a></li>
    <?php 
    if ($page-3>1){
        ?>
        <li class="rafpaging <?=$prevclass?>" data-page="<?php echo $page-4; ?>"><a href="#">...</a></li>
    <?php
    }

    if ($amount_of_pages>5){
        $show=5;
        $show=$amount_of_pages;
    }else{
        $show=$amount_of_pages;
    }
    for($i=1;$i<=$show;$i++){
        if ($i==$page){
            $active="active";
        }else{
            $active="";
        }
        if ($i>=$page-3 && $i<=$page+3){
            echo '<li class="rafpaging '.$active.'" data-page="'.$i.'"><a href="#">'.$i.'</a></li>'; 
        }
    }
    if ($amount_of_pages-3>$page){ ?>
        <li class="rafpaging <?=$prevclass?>" data-page="<?php echo $page+4; ?>"><a href="#">...</a></li>
    <?php
    }
    if ($page==$amount_of_pages){
        $nextclass="disabled";
    }
    ?>
    <li class="rafpaging <?=$nextclass?>" data-page="<?php echo $page+1; ?>"><a href="#">Next</a></li>
  </ul>

<?php } ?>
  <table width="100px" class="pull-right">
    <tr class='acquisition' align="center"><td>ACQUISITION</td></tr>
    <tr class='design' align="center"><td>DESIGN</td></tr>
    <tr class='construction' align="center"><td>CONSTRUCTION</td></tr>
    <tr class='asbuild' align="center"><td>AS BUILD</td></tr>
    <tr class='locked' align="center"><td style="color:white">LOCKED</td></tr>
    <tr class='deleted' align="center"><td>DELETED</td></tr>
  </table>
  </div>
  <div class="col-md-7" style="overflow:hidden;background-color: #FFFFFF;min-height: 500px;padding:0;" id="scroll<?=$_POST['siteID']?>">
  <?=$out_normal?>
  <?=$out_asbuild?>
  <?=$out_deleted?>

  </div>
</div>
</form>

<?php
/*
echo "amountperpage ".$amountperpage."<br>";
echo "page ".$page."<br>";
echo "start ".$start."<br>";
echo "end ".$end."<br>";
echo "amount_of_RAFS ".$amount_of_RAFS."<br>";
echo "totalrafs ".$totalrafs."<br>";

*/
?>
<script src="javascripts/jquery.debouncedresize.min.js"></script>
<script type="text/javascript">


if ($("[rel=tooltip]").length) {
        $("[rel=tooltip]").tooltip();
}
<?php if ($amount_of_pages>1){ ?> 
$('.rafpaging').click(function() {
    $('#spinner').spin('medium');
    $("#rafOutput").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
    var page=$(this).data("page");
    $("#rafOutput").load("scripts/raf/raf.php",
    {
        actionby: '<?=$_POST['actionby']?>',
        region: '<?=$_POST['region']?>',
        siteID: '<?=$_POST['siteID']?>',
        orderby:'<?=$_POST['orderby']?>',
        order:'<?=$_POST['order']?>',
        type:'<?=$_POST['type']?>',
        xlsprint:'<?=$_POST['xlsprint']?>',
        phase:'<?=$_POST['phase']?>',
        commercial:'<?=$_POST['commercial']?>',
        allocated:'<?=$_POST['allocated']?>',
        rfinfo:'<?=$_POST['rfinfo']?>',
        build:'<?=$_POST['build']?>',
         deleted:'<?=$_POST['deleted']?>',
        page: page,
        totalrafs: '<?=$totalrafs?>',
        viewtype: '<?=$_POST['viewtype']?>'
    },
    function(){
       $('#spinner').spin(false);
    });


});
 <?php } ?> 
</script>

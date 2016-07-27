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

$query=create_query2($_POST['siteID'],$_POST['RAFID'],$_POST['region'],$_POST['type'],$_POST['actionby'],$_POST['orderby'],$_POST['order'],$start,$end,$_POST['rfinfo'],$_POST['commercial'],$_POST['allocated'],$_POST['deleted'],$_POST['event'],$_POST['cluster']);
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
                    $CONDITIONAL="<a rel='popover' class='tip label label-info pointer' title='CONDITIONALLY OK!' data-content='".$res3['CONDITIONAL'][0]."'>CONDITIONAL!</a>";
                }
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
            $select_action="";

            $RF_PAC_info="";
            $BCS_RF_INP_info="";
            $BCS_TX_INP_info="";
            $TXMN_INP_info="";
            $RADIO_FUND_info="";
            $CON_PARTNER_info="";
            $PARTNER_RFPAC_info="";

            $partner='';
            $radio='';
            $cof='';
            $txmn='';

            $cols="";
            $headers="";
            $output_raf3="";
            $userdata="";


            $query="SELECT INDOOR FROM RAF_PROCESS_STEPS WHERE RAFTYPE='".$res1['TYPE'][$i]."'";
            //echo $query;
            $stmtPR= parse_exec_fetch($conn_Infobase, $query, $error_str, $resPR);
            if (!$stmtPR){
                die_silently($conn_Infobase, $error_str);
                exit;
            } else {
                OCIFreeStatement($stmtPR);
            }

            if ($resPR['INDOOR'][0]=="yes"){
                $raf_type="indoor";
            }else{
                $raf_type="outdoor";
            }
           
            $ACTION_DO=$res1['ACTION_DO'][$i];
            
            $query="SELECT * FROM VW_RAF_PROCESSTAKS WHERE RAFTYPE='".$res1['TYPE'][$i]."' and PHASE!='skip' AND STEPNUM IS NOT NULL";
            //echo $query;
            $stmtPR= parse_exec_fetch($conn_Infobase, $query, $error_str, $resPR);
            if (!$stmtPR){
                die_silently($conn_Infobase, $error_str);
                exit;
            } else {
                OCIFreeStatement($stmtPR);
                $amount_of_TASKS=count($resPR['TASK_NAME']);
            }
            
            $skip_ACQ_TASKS=array();
            $query="SELECT TASK_NAME FROM VW_RAF_PROCESSTAKS WHERE RAFTYPE='".$res1['TYPE'][$i]."' and PHASE='skip' AND STEPNUM IS NOT NULL";
            //echo $query;
            $stmtSK= parse_exec_fetch($conn_Infobase, $query, $error_str, $resSK);
            if (!$stmtSK){
                die_silently($conn_Infobase, $error_str);
                exit;
            } else {
                OCIFreeStatement($stmtSK);
                $amount_of_SKIPACQ=count($resSK['TASK_NAME']);
                for ($z = 0; $z <$amount_of_SKIPACQ; $z++){ 
                    $skip_ACQ_TASKS[]=$resSK['TASK_NAME'][$z];
                }
            }

            //echo "<pre>".print_r($skip_ACQ_TASKS,true)."</pre>";

            include('raf_color_analysis.php');

            $k++;
            
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


            $PARTNER_RFPAC_info="<a class='tippy' title='Action since ".$res1['INSERTDATE_CON'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            
            
            if ($_POST['siteID'] && $res1['TYPE'][$i]=="New Replacement" && $res1['DELETED'][$i]!="yes" && $res1['PARTNER_PAC'][$i]=="NOT OK"){
                ?>
                <div class="alert alert-danger" role="alert">WARNING: A REPLACEMENT IS ONGOING! Site is marked to be dismantled</div>
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

            $BCS_RF_INP_info="<a class='tippy' title='Action since ".$res1['PARTNER_INP_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            $TXMN_INP_info="<a class='tippy' title='Action since ".$res1['RADIO_INP_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            $RADIO_FUND_info="<a class='tippy' title='Action since ".$res1['TXMN_ACQUIRED_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            $CON_PARTNER_info="<a class='tippy' title='Action since ".$res1['RADIO_FUND_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            $RF_PAC_info="<a class='tippy' title='Action since ".$res1['PARTNER_RFPAC_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            $BCS_TX_INP_info="<a class='tippy' title='Action since ".$res1['PARTNER_INP_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            $BCS_RF_INP_info="<a class='tippy' title='Action since ".$res1['PARTNER_INP_DATE'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";
            $PARTNER_RFPAC_info="<a class='tippy' title='Action since ".$res1['INSERTDATE_CON'][$i]."'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span></a>";


            if($amount_of_TASKS==0){
                echo "UKNOWN RAFTYPE";
            }

            for ($k = 0; $k <$amount_of_TASKS; $k++){  
                $taskname=$resPR['TASK_NAME'][$k];
                    // echo $res1['RAFID'][$i]."<br>";
                    //echo $taskname."/";
                if ((!in_array($taskname, $skip_ACQ_TASKS) && $res1['BUFFER'][$i]==1) or $res1['BUFFER'][$i]!=1){ //ONLY SHOW ACQ TASKS IF ACQ NOT SKIPPED
                    $class=$taskname."_class";
                    $info=$taskname."_info";
                    $select=$taskname."_select";

                    
                    if (substr_count($guard_groups, 'Admin')==1 && $taskname!='PO_ACQ'  && $taskname!='PO_CON'){ //admin can edit everything
        
                        $$select="editableSelectItem";
                    }

                    if ($res1[$taskname][$i]!="NA"){
                        $cols.="<col style='width: ".$resPR['COLUMNWIDTH'][$k]."px'>";
                        $headers.="<th class='".$resPR['PHASE'][$k]." ".$row_color2."'><a  id='H_".$taskname.$res1['RAFID'][$i]."' rel='tooltip' data-placement='bottom' title='".$resPR['DESCRIPTION'][$k]."' class='tip'>".$resPR['FULLNAME'][$k]."</a></th>";
                         
                        if ($res1[$taskname][$i]==''){
                            $taskVal='NOT OK';
                        }else{
                            $taskVal=$res1[$taskname][$i];
                        }
                    /*
                    if ($res1['RAFID'][$i]=='11995'){
                        echo $taskname."---".$taskVal."<br>";
                    }*/

                        if ($taskname=='NET1_LINK' && $res1['NET1_LINK'][$i]=="BCS CHANGE"){
                            $extra_class='danger';
                        }else{
                            $extra_class='';
                        }
                        if ($taskname=="PO_ACQ" or $taskname=="PO_CON"){
                            $extra="font-size:10px;";
                        }else{
                            $extra="";
                        }
                        if ($taskname=="RADIO_FUND"){
                            $edittype="checklist";
                        }else{
                            $edittype="select";
                        }
                        if ($taskname=='PARTNER_RFPAC'){ 
                            if ($res1['TYPE'][$i]=="DISM Upgrade"){
                                 $output_raf3.="<td class='".$PARTNER_RFPAC_class."'>U825/A200: <span id='PARTNER_RFPAC2-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_RFPAC_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['PARTNER_RFPAC2'][$i]."</span><br>U999/A250: <span id='PARTNER_RFPAC-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_RFPAC_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['PARTNER_RFPAC'][$i]."</span> ".$PARTNER_RFPAC_info."</td>";
                            }else{
                             $output_raf3.="<td class='".$PARTNER_RFPAC_class."'><div id='PARTNER_RFPAC-".$res1['RAFID'][$i]."' class='tabledata ".$PARTNER_RFPAC_select."' data-pk='".$res1['RAFID'][$i]."'>".$res1['PARTNER_RFPAC'][$i]."</div> ".$PARTNER_RFPAC_info."</td>";
                            }
                        }else{
                            $output_raf3.="<td class='".$$class." ".$extra_class."'><div style='min-height:45px;max-height:45px;".$extra."'><span id='".$taskname."-".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."' class='tabledata ".$$select."' data-type='".$edittype."' data-pk='".$res1['RAFID'][$i]."'>".$taskVal."</span>";     
                            if ($taskname=='BCS_NET1' && $CONDITIONAL!=''){
                                $output_raf3.="<br>".$CONDITIONAL;
                            }
                            $output_raf3.="<br>".$$info;
                            $output_raf3.="</div></td>"; 
                        }
                        

                        if ($res1[$taskname][$i]!="NOT OK" && $res1[$taskname][$i]!="" && $taskname!='PO_ACQ' && $taskname!='PO_CON' && substr_count($res1[$taskname][$i], 'MISSING')!=1 ){
                            $userby=getuserdata($res1[$taskname.'_BY'][$i]);

                            $userdata.="<tr>
                                        <td>".$resPR['FULLNAME'][$k]."</td>
                                        <td>".$userby['firstname']." ".$userby['lastname']."</td>
                                        <td>".$res1[$taskname.'_DATE'][$i]."</td>
                                    </tr>";
                        }
                    }
                }
            }

           
            $user_CREATION=getuserdata($res1['CREATED_BY'][$i]);
            $user_UPDATE_BY=getuserdata($res1['UPDATE_BY'][$i]);

            ?>
            <div id="rafOutput">
                <div class="modal fade" id="RAFBOX_<?=$res1['RAFID'][$i]?>" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                                <?=$userdata?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
               
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
                                    <li id="raf_details_other" data-file="other" data-rafid="<?=$res1['RAFID'][$i]?>" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">OTHER</a></li>
                                    <li id="raf_details_radio" class="<?=$radio?>" data-file="radio" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-rafid="<?=$res1['RAFID'][$i]?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">RADIO</a></li>
                                    <li id="raf_details_txmn" class="<?=$txmn?>" data-file="txmn" data-rafid="<?=$res1['RAFID'][$i]?>" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">TXMN</a></li>
                                    <li id="raf_details_partner" class="<?=$partner?>" data-file="partner" data-rafid="<?=$res1['RAFID'][$i]?>" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">PARTNER</a></li>
                                    <li id="raf_details_cof" class="<?=$cof?>" data-file="cof" data-rafid="<?=$res1['RAFID'][$i]?>" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">COF</a></li>
                                    <li id="raf_details_trx" data-file="trx" data-rafid="<?=$res1['RAFID'][$i]?>" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-siteid="<?=$res1['SITEID'][$i]?>" data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">TRX+BPC REQUIREMENTS</a></li>
                                    <li id="raf_details_files" data-file="files" data-rafid="<?=$res1['RAFID'][$i]?>" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-siteid="<?=$res1['SITEID'][$i]?>"  data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">FILES</a></li>
                                    <li id="raf_details_tracking" data-file="tracking" data-rafid="<?=$res1['RAFID'][$i]?>" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-siteid="<?=$res1['SITEID'][$i]?>"  data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">TRACKING</a></li>
                                    <li id="raf_details_bcsm" data-file="bcsm" data-rafid="<?=$res1['RAFID'][$i]?>" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-siteid="<?=$res1['SITEID'][$i]?>"  data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#">BCS MODEL</a></li>
                                    <li id="raf_details_history" data-file="history" data-rafid="<?=$res1['RAFID'][$i]?>" data-bufferchangeallowed="<?=$bufferchangeAllowed?>" data-siteid="<?=$res1['SITEID'][$i]?>"  data-type="<?=$res1['TYPE'][$i]?>" data-actiondo='<?=$ACTION_DO?>'><a href="#"><span class='glyphicon glyphicon-time'></span> ACTIONLOG</a></li>
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
            <input type='hidden' name='type' id='type-".$res1['RAFID'][$i]."' value='".$res1['TYPE'][$i]."'>
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

            $$output_raf2.="<table class='table table-bordered tablefixecol table-condensed' style='table-layout: fixed;margin:0;' id='RAFTable".$_POST['siteID'].$res1['RAFID'][$i]."'>
            <colgroup>
                <col style='width: 122px'>
                <col style='width: 55px'>
                <col style='width: 143px'>
                <col style='width: 200px'>";


            $$output_raf.="<table class='table table-bordered tablefixecol table-condensed' style='table-layout: fixed;margin:0;' id='RAFTable".$_POST['siteID'].$res1['RAFID'][$i]."'>
            <colgroup>";

               $$output_raf.=$cols;
                
                $$output_raf.="
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

                        $$output_raf.=$headers;

                        $$output_raf.="
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
                  <button type='button' class='btn btn-xs btn-default rafnav' data-action='view' data-id='".$res1['RAFID'][$i]."' data-site='".$res1['SITEID'][$i]."' data-bufferchangeAllowed='".$bufferchangeAllowed."' data-type='".$res1['TYPE'][$i]."' data-file='".$file."' data-actiondo='".$ACTION_DO."'><span class='glyphicon glyphicon-eye-open'></span> <span ".$gclass2."><b>".$res1['RAFID'][$i]."</b></span></button>
                  <button type='button' class='btn btn-xs btn-default rafnav' data-action='print' data-id='".$res1['RAFID'][$i]."' data-site='".$res1['SITEID'][$i]."'><span class='glyphicon glyphicon-print'></span></button>";
                   
                  $$output_raf2.="<button type='button' class='btn btn-xs btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                    <span class='caret'></span>
                    <span class='sr-only'>Toggle Dropdown</span>
                  </button>
                  <ul class='dropdown-menu' role='menu'>";
                    if ($res1['TYPE'][$i]=="MOD Upgrade" && ($res1['MASTERRAF'][$i]=="" or $res1['MASTERDEL'][$i]=="yes") && (substr_count($guard_groups, 'Administrators')==1 or substr_count($guard_groups, 'Base_TXMN')==1)){
                    $$output_raf2.="<li><a href='#' class='rafnav' data-action='txmodupgrade' data-id='".$res1['RAFID'][$i]."' title='ADD MOD TX Upgrade'><span class='glyphicon glyphicon-plus-sign'> ADD TX UPG</span></a></li>";
                    }
                    if ($res1['NET1_LINK'][$i]!="NOT OK"){
                    $$output_raf2.="<li><a href='#' class='rafnav' data-action='net1explorer' data-siteid='".$res1['SITEID'][$i]."' data-net1link='".$res1['NET1_LINK'][$i]."' title='OPEN corresponding NET1 info'><span class='glyphicon glyphicon-th-large'> NET1</span></a></li>";
                    }
                    $$output_raf2.="<li><a href='scripts/raf/raf_details_history.php' class='rafnav' data-action='history' data-id='".$res1['RAFID'][$i]."' data-siteid='".$res1['SITEID'][$i]."'><span class='glyphicon glyphicon-time'></span> ACTION LOG</a></li>
                    <li><a href='#' data-toggle='modal' data-target='#RAFBOX_".$res1['RAFID'][$i]."'><span class='glyphicon glyphicon-user'></span> USERS</a></li>
                    <li><a href='#' class='validation' title='validation' data-rafid='".$res1['RAFID'][$i]."' data-siteupgnr='".$res1['NET1_LINK'][$i]."' data-nbup='NB'><span class='glyphicon glyphicon-check'></span> VALIDATION</a></li>";
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
                </div><br>";
                if ($res1['MASTERRAF'][$i]!='' &&  $res1['MASTERDEL'][$i]!="yes"){
                    $$output_raf2.="  <span class='label label-success'>".$res1['MASTERRAF'][$i]."</span>";
                }
                if ($res1['MASTER_RAFID'][$i]!=''){
                    $$output_raf2.="  <span class='label label-danger'>".$res1['MASTER_RAFID'][$i]."</span>";
                }
                $$output_raf2.="<br>
                </td>";

                 $$output_raf2.="<td><div style='min-height:45px;'>".$res1['SITEID'][$i]."</div></td>
                <td><b>".$res1['TYPE'][$i]."</b><br>";

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
           
                if ($status_special!=""){
                    $$output_raf2.="<span class='conditional label label-danger'>".$status_special."</span>";
                }
                $$output_raf2.="</div></td>";

                $$output_raf.=$output_raf3;

                $$output_raf.="

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



<div id="scrollsRAF" class="scrolls rafScroll">
    <button type="button" class="btn btn-default btn-xs leftArrow Arrows" data-scrollid="scroll<?=$_POST['siteID']?><?=$_POST['rafid']?>" style="margin:0 5px 0 5px;">
      <span class="glyphicon glyphicon-backward" aria-hidden="true" ></span>
    </button>
    &nbsp;
     <button type="button" class="btn btn-default btn-xs rightArrow Arrows" data-scrollid="scroll<?=$_POST['siteID']?><?=$_POST['rafid']?>">
      <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
    </button>
</div>

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

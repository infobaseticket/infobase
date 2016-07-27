<?php
$updatables="";
for ($k = 0; $k <$amount_of_TASKS; $k++){  
    $taskname=$resPR['TASK_NAME'][$k];
    $class=$taskname."_class";
    $info=$taskname."_info";
    $select=$taskname."_select";
    if ($resPR['UPDATABLE'][$k]==1){
        $updatables[]=$resPR['TASK_NAME'][$k];
    }
    
    $$taskname="";
    $$class="";
    $$select="";
}

//echo "<pre>".print_r($updatables,true)."</pre>";
$status_acqSkipped="";

$actions=explode(',', $res1['ACTION2'][$i]);
$actionbys=explode(',', $res1['ACTION_BY'][$i]);

if (is_array($actions)){
    foreach ($actions as $key => $action) {
        if ($actionbys[$key]!=''){
            //echo  $res1['RAFID'][$i]."(".$guard_groups.")".$action."/".$actionbys[$key]."<br>";
            if (substr_count($guard_groups, $actionbys[$key])==1 or substr_count($guard_groups, 'Admin')==1){
               
                if(in_array($action,$updatables)){
                    $editable=$action."_select";
                    $$editable="editableSelectItem";
                }   
               
                
                $class=$action."_class";
                $$class="selected_RAF";
                /*if ($res1['RAFID'][$i]=='9783'){
                    echo $class."=".$$class."<br>";
                }*/
               
            }
        }
     }
}
//RADIO INPUT CAN BE PUT BACK TO NOT OK UNTILL RADIO_FUND
if ($res1['RADIO_FUND'][$i]=='NOT OK' && (substr_count($guard_groups, 'Admin')==1 or substr_count($guard_groups, 'Base_RF')==1)){
    $RADIO_INP_select="editableSelectItem";
}
//You can update RADIO_FUND when BASE BP NEEDED= BASE BP NO untill PARTNER_DESIGN!=NOT OK
if ($res1['BP_NEEDED'][$i]=='BASE BP NO' && $res1['PARTNER_DESIGN'][$i]!='NOT OK'){
    $RADIO_FUND_select="editableSelectItem";
}


if ($res1['BUFFER'][$i]==1 && $res1['DELETED'][$i]!="yes" && $status!="RAF ASBUILD"){

    $query="SELECT * FROM VW_RAF_PROCESSTAKS WHERE RAFTYPE='".$res1['TYPE'][$i]."' and PHASE='skip' AND STEPNUM IS NOT NULL";
    //echo $query;
    $stmtSK= parse_exec_fetch($conn_Infobase, $query, $error_str, $resSK);
    if (!$stmtSK){
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmtSK);
        $amount_of_SKIP=count($resSK['TASK_NAME']);
    }

    for ($k = 0; $k <$amount_of_SKIP; $k++){ 

        $taskname=$resPR['TASK_NAME'][$k];
        $class=$taskname."_class";
        $$class="buffer";

    }
    if($NET1_LINK_class!='selected_RAF'){
    $NET1_LINK_class="buffer buffer2";
    }
    $PARTNER_INP_class="buffer";
    $NET1_LBP_class="buffer buffer2";
}
?>
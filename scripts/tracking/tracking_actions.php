<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['action']=='updateTags'){
    //echo "<pre>".print_r($_POST['value'],true);
    foreach ($_POST['value'] as $key => $tag) {
        $tags.=$tag.",";
    }
    $query="SELECT SITEID from DELIVERYMASTER WHERE SITEID='".$_POST['pk']."'";
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmt);
    }
    $amount=count($res1['SITEID']);
    if ($amount==0){
        $query="INSERT INTO DELIVERYMASTER (SITEID,TAGS,UPDATE_BY,UPDATE_ON) VALUES ('".$_POST['pk']."','".substr($tags,0,-1)."','".$guard_username."',SYSDATE)";
        //echo $query;
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
            die_silently($conn_Infobase, $error_str);
        }else{
            OCICommit($conn_Infobase);
        }
    }else{
        $query="UPDATE DELIVERYMASTER SET TAGS='".substr($tags,0,-1)."',UPDATE_BY='".$guard_username."',UPDATE_ON=SYSDATE WHERE SITEID='".$_POST['pk']."'";
        //echo $query;
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
            die_silently($conn_Infobase, $error_str);
        }else{
            OCICommit($conn_Infobase);
        }  
    }

    $query="INSERT INTO DELIVERYMASTER_LOG (SITEID,ACTION,UPDATE_BY,UPDATE_ON) VALUES ('".$_POST['pk']."','".substr($tags,0,-1)."','".$guard_username."',SYSDATE)";
    //echo $query;
    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
    }else{
        OCICommit($conn_Infobase);
    }
    
}else if ($_POST['action']=='insertComment'){

    if ($_POST['rafid']!=''){
        $query= "INSERT INTO RAF_COMMENTS
        VALUES ('".$guard_username."',SYSDATE,".$_POST['rafid'].", '".escape_sq($_POST['comments'])."',0,'','".$_POST['siteid']."','')";
        //echo $query."<br>";
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
          die_silently($conn_Infobase, $error_str);
        }else{
          OCICommit($conn_Infobase);
        }
        $res["responsedata"] = "Comments have been saved!";
        $res["responsetype"]="info";
    }else{
        $res["responsedata"] = "RAFID cannot be empty!";
        $res["responsetype"]="error";
    }
   
    echo json_encode($res);

}else if ($_POST['action']=='make_history'){
    if ($_POST['trackid']!=""){
        $query="UPDATE RAF_COMMENTS SET HISTORY='1',HISTORY_BY='".$guard_username."' WHERE ID='".$_POST['trackid']."'";
        // echo $query;
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
            die_silently($conn_Infobase, $error_str);
        }else{
            OCICommit($conn_Infobase);
            echo "COMMENT FOR ".$_POST['trackid']." HAS BEEN MOVED TO HISTORY!";
        }
    }
}
?>

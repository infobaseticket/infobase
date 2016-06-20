<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Partner","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);


if ($_POST['action']=='updateTags'){
    $tags=explode(",",$_POST['SiteTags']);
    if ($_POST['type']!='remove'){
        foreach ($tags as $key => $tag) {
            $query="SELECT ID from DELIVERYTAGS WHERE TAGNAME='".$tag."'";
            $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
            if (!$stmt){
                die_silently($conn_Infobase, $error_str);
                exit;
            } else {
                OCIFreeStatement($stmt);
            }
            $amount=count($res1['ID']);
        
            if ($amount==0){
                $query="INSERT INTO DELIVERYTAGS (TAGNAME) VALUES ('".$tag."')";
                //echo $query;
                parse_exec_free($conn_Infobase, $query, $error_str);
                if (!$stmt) {
                    die_silently($conn_Infobase, $error_str);
                }else{
                    OCICommit($conn_Infobase);
                }
                $query="SELECT ID from DELIVERYTAGS WHERE TAGNAME='".$tag."'";
                $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
                if (!$stmt) {
                    die_silently($conn_Infobase, $error_str);
                    exit;
                } else {
                    OCIFreeStatement($stmt);
                }
                $ID=$res1['ID'][0];
            }
        }
    }else{
        $query="SELECT ID from DELIVERYMASTER WHERE TAGS LIKE '%".$_POST['addval']."%'";
            $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
            if (!$stmt) {
                die_silently($conn_Infobase, $error_str);
                exit;
            } else {
                OCIFreeStatement($stmt);
            }
            $amount=count($res1['ID']);
            if ($amount==0){
                 $query="DELETE FROM  DELIVERYTAGS  WHERE TAGNAME='".$_POST['addval']."'";
                //echo $query;
                parse_exec_free($conn_Infobase, $query, $error_str);
                if (!$stmt) {
                    die_silently($conn_Infobase, $error_str);
                }else{
                    OCICommit($conn_Infobase);
                }
            }
    }

    $query="SELECT SITEID from DELIVERYMASTER WHERE SITEID='".$_POST['siteID']."'";
    $stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
        exit;
    } else {
        OCIFreeStatement($stmt);
    }
    $amount=count($res1['SITEID']);
    if ($amount==0){
        $query="INSERT INTO DELIVERYMASTER (SITEID,TAGS,UPDATE_BY,UPDATE_ON) VALUES ('".$_POST['siteID']."','".$_POST['SiteTags']."','".$guard_username."',SYSDATE)";
        //echo $query;
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
            die_silently($conn_Infobase, $error_str);
        }else{
            OCICommit($conn_Infobase);
        }
    }else{
        $query="UPDATE DELIVERYMASTER SET TAGS='".$_POST['SiteTags']."',UPDATE_BY='".$guard_username."',UPDATE_ON=SYSDATE WHERE SITEID='".$_POST['siteID']."'";
        echo $query;
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
            die_silently($conn_Infobase, $error_str);
        }else{
            OCICommit($conn_Infobase);
        }  
    }

    $query="INSERT INTO DELIVERYMASTER_LOG (SITEID,ACTION,UPDATE_BY,UPDATE_ON) VALUES ('".$_POST['siteID']."','".$_POST['type']." ".$_POST['addval']."','".$guard_username."',SYSDATE)";
    //echo $query;
    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
    }else{
        OCICommit($conn_Infobase);
    }
    
}
if ($_POST['action']=='insertComment'){
    $query="INSERT INTO DELIVERYTRACK (CREATIONDATE,CREATIONBY,DATUM,COMMENTS,SITEID,RAFID) 
    VALUES (SYSDATE,'".$guard_username."','".$_POST['datum']."','".escape_sq($_POST['comments'])."','".$_POST['siteid']."','".$_POST['rafid']."')";
    //echo $query;
    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
    }else{
        OCICommit($conn_Infobase);
    }
    $res["responsedata"] = "Comments have been saved!";
    $res["responsetype"]="info";
    echo json_encode($res);
}
if ($_POST['action']=='update_track'){
    if ($_POST['trackid']!=""){
        $query="UPDATE DELIVERYTRACK SET UPDATEDATE=SYSDATE,UPDATEBY='".$guard_username."',DELETED=".$_POST['type']." WHERE ID='".$_POST['trackid']."'";
        // echo $query;
        $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
        if (!$stmt) {
            die_silently($conn_Infobase, $error_str);
        }else{
            OCICommit($conn_Infobase);
            echo "COMMENT ".$_POST['trackid']." HAS BEEN CHANGED!";
        }
    }
}
if ($_POST['action']=='updateComments'){
    $query="UPDATE DELIVERYTRACK SET COMMENTS='".escape_sq($_POST['value'])."',UPDATEDATE=SYSDATE,UPDATEBY='".$guard_username."' WHERE ID='".$_POST['pk']."'";
    //echo $query;
    $stmt = parse_exec_free($conn_Infobase, $query, $error_str);
    if (!$stmt) {
        die_silently($conn_Infobase, $error_str);
    }else{
        OCICommit($conn_Infobase);
    }
}
?>

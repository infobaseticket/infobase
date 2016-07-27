<?php

/************************************
 * Functions for Oracle-based tools *
 ************************************/

// Use when fetching data from the db
function unescape_quotes($str)
{
   $esc_str = str_replace("''", "'", $str);
   $esc2_str = str_replace("\"\"", "\"", $esc_str);
   return $esc2_str;
}


// Use when inserting data into the db
function escape_sq($str)
{
   $esc_str = str_replace("'", "''", $str);
   return $esc_str;
}


function escape_html($str)
{
   $gt_str = str_replace("&gt;", ">;", $str);
   $lt_str = str_replace("&lt;", "<", $gt_str);
   $dq_str = str_replace("&quot;", "\"", $lt_str);
   $esc_str = str_replace("&amp;", "&", $dq_str);
   return $esc_str;
}


// Use this one for INSERTs, UPDATEs, and DELETEs
function parse_exec_free($conn, $query, &$error_str)
{
  global $firephp,$config,$guard_groups,$debug,$guard_username;

   //echo $query;

  if (substr_count($guard_groups, 'Administrators')=="1" && $config['debug']==true){
   //echo $query;
      $trace=debugPrintCallingFunction();
      $query_out = preg_replace("/[\\n\\r]+/", " ", $query);
      $query_out = preg_replace('/\s+/', ' ', $query_out);
     // echo $query_out."<hr>";
      ?>
      <script language="javascript">
      console.log(<? echo json_encode($trace.": ".$query_out); ?>)
      </script>
      <?php
      //ChromePhp::log($trace.":\r\n".$query_out);


   }
   $stmt = OCIParse($conn, $query);
   OCIExecute($stmt, OCI_DEFAULT);
   $err_array = OCIError($stmt);
   if ($err_array) {
      $err_message = $err_array['message'];
      $$error_str = $err_message;
      OCIFreeStatement($stmt);
      $stmt = FALSE;
   } else {
       OCIFreeStatement($stmt);
      $stmt = TRUE;
   }

   if ($_POST['rafid']!=''){
         $trace=debugPrintCallingFunction();
         $query2="INSERT INTO BSDS_RAF_LOG VALUES ('','".$guard_username."',SYSDATE,'".escape_sq($query)."','".$trace['file']."','".substr($query,0,6)."','".$_POST['rafid']."')";
          $stmt2 = OCIParse($conn, $query2);
         OCIExecute($stmt2, OCI_DEFAULT);
         $err_array2 = OCIError($stmt2);
         if ($err_array2) {
            OCIFreeStatement($stmt2);
         } else {
            OCIFreeStatement($stmt2);
         }
   }
   /*
   if ($guard_username=="debon_l"){
      $file = fopen("/var/www/html/queries_select.txt","a");
      fwrite($file,$query."\r\n------------ ".date("d-M-Y H:m:s")."\r\n");
      fclose($file);
   }*/

   return $stmt;
}

// Use this one for SELECTs
function parse_exec_fetch($conn, $query, &$error_str, &$res, $nulls=0)
{
   global $firephp,$config,$guard_groups,$debug,$guard_username;
    if (substr_count($guard_groups, 'Administrators')=="1"){
    //echo $query."<br>";
 }
   if (substr_count($guard_groups, 'Administrators')=="1" && $config['debug']==true){
      //echo $query;
      $trace=debugPrintCallingFunction();
      $query_out = preg_replace("/[\\n\\r]+/", " ", $query);
      $query_out = preg_replace('/\s+/', ' ', $query_out);
      //echo $query_out."<hr>";
      ?>
      <script language="javascript">
      console.log(<? echo json_encode($trace.": ".$query_out); ?>);
      </script>
      <?php
      
      //ChromePhp::log($trace.":\r\n".$query_out); 
   }
   $stmt = OCIParse($conn, $query);
   OCIExecute($stmt, OCI_DEFAULT);
   $err_array = OCIError($stmt);
   if ($err_array) {
      $err_message = $err_array['message'];
      $$error_str = $err_message;

      OCIFreeStatement($stmt);
      $stmt = FALSE;
   } else {
      if ($nulls == 1) {
         OCIFetchStatement($stmt, $res, OCI_RETURN_NULLS);
      } else {
         OCIFetchStatement($stmt, $res);
      }
   }
/*
    if ($guard_username=="debon_l"){
      $file = fopen("/var/www/html/queries_update.txt","a");
      fwrite($file,$query."\r\n------------ ".date("d-M-Y H:m:s")."\r\n");
      fclose($file);
   }*/

   return $stmt;
}


// For batch_upload.php, which writes a separate error log
function choke_and_die($conn, $fp, $error_str)
{
   OCIRollback($conn);
   OCILogoff($conn);
   $error_line = $error_str."<BR>\n";
   echo $error_line;
   fwrite($fp, $error_line);
   fwrite($fp, "</HTML>\n");
   fclose($fp);
   exit;
}


// For all non-logwriting uses (which is most of them)
function die_silently($conn, $error_str)
{
   OCIRollback($conn);
   OCILogoff($conn);
   // You can uncomment these when debugging
   $error_line = $error_str."<BR>\n";
   echo $error_line;
   exit;
}


// Excel sometimes adds random quotes around field contents
function unquote($str)
{
   $pos = strpos($str, "\"");
   if ($pos === 0) {
      $qstr = substr($str, 1, -1);
      return trim($qstr);
   } else {
      return trim($str);
   }
}


// Excel sometimes doubles double-quotes in an attempt to close them
function strip_db($str)
{
   $esc_str = str_replace("\"\"", "\"", $str);
   return $esc_str;
}

?>

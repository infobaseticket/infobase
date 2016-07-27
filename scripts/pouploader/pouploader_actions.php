<?php
require_once("/var/www/html/bsds/config.php");
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if(!function_exists('str_getcsv')) {
    function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
        $fp = fopen("php://memory", 'r+');
        fputs($fp, $input);
        rewind($fp);
        $data = fgetcsv($fp, null, $delimiter, $enclosure); // $escape only got added in 5.3.0
        fclose($fp);
        return $data;
    }
}

if ($_POST['action']=="multianalyse"){
	//$data=str_getcsv($_POST['csvdata']);
	//echo "<pre>".print_r($data)."<pre>";

	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdata']));
	if (trim($data[0][0])!="RAFID" or trim($data[0][1])!="POPR" or trim($data[0][2])!="ACQCON" or trim($data[0][3])!="POTEXT" or trim($data[0][4])!="ITEMCOST" or trim($data[0][5])!="PODATE"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect';
		echo json_encode($res);
		exit;
	}
	//We now analyse the input
	$i=0;
	
	foreach ($data as $key => $result) {
		$message="";
		$status="OK";
		if ($i!=0){
			if (!is_numeric(trim($result['0']))){
				$message.="RAF ID ".$result['0']." has to be a number<br>";
				$status='NOTOK';
				$general_status='NOTOK';
			}else{
				$query = "select * from BSDS_RAF_PO WHERE RAFID='".trim($result['0'])."' AND POPR='".strtoupper(trim($result['1']))."' AND ACQCON='".strtoupper(trim($result['2']))."'";
				//secho $query."<br>";
				$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
				if (!$stmt) {
				  	die_silently($conn_Netone, $error_str);
				  	exit;
				} else {
				  OCIFreeStatement($stmt);
				}
				$amount=count($res1['RAFID']);
				if ($amount==1){
					if($res1['CONFIRMED'][0]=='DELETED'){
						$message.="RAF ID ".$result['0']." has PO but is deleted<br>";
						$status='NOTOK';
						$general_status='NOTOK';
					}else{
						$status="OK";
					}
				}elseif ($amount>1){
					$message.="DUPLICATE DATA IN THE DATABASE<br>";
					$status='NOTOK';
					$general_status='NOTOK';
				}
			}	

			if ($status=="OK"){
				$class="success";
			}else{
				$class="danger";
			}
			$out.='<tr class="'.$class.'"><td>'.$i.'</td><td>'.trim($result['0']).'</td>';
			$out.="<td>".trim($result['1'])."</td>";
			$out.="<td>".trim($result['2'])."</td>";
			$out.="<td>".trim($result['3'])."</td>";
			$out.="<td>".trim($result['4'])."</td>";
			$out.="<td>".trim($result['5'])."</td>";
			$out.="<td>".trim($message)."</td></tr>";
			
		}
		$i++;
	}
	$out="<table class='table'><thead><th>ID</th><th>RAFID</th><th>POPR</th><th>ACQCON</th><th>PO TEXT</th><th>ITEMCOST</th><th>PODATE</th><th>&nbsp;</th></thead>".$out."</table>";
	if ($general_status!='NOTOK'){
		$retval["type"]='info';
		$retval["table"]=$out;
	}else{
		$retval["type"]='error';
		$retval["message"]="You have errors in your csv data!";
		$retval["table"]=$out;
	}	
	
	echo json_encode($retval);

}else if ($_POST['action']=="multiimport"){
	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdata']));
	if (trim($data[0][0])!="RAFID" or trim($data[0][1])!="POPR" or trim($data[0][2])!="ACQCON" or trim($data[0][3])!="POTEXT" or trim($data[0][4])!="ITEMCOST"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect'.$data[0][1];
		echo json_encode($res);
		exit;
	}
	//We now analyse the input
	$i=0;
	//echo "<pre>".print_r($results,true)."</pre>";
	$general="info";
	foreach ($data as $key => $result) {
		if ($i!=0){
			$query = "select * from BSDS_RAF_PO WHERE RAFID='".trim($result['0'])."' AND POPR='".strtoupper(trim($result['1']))."' AND ACQCON='".strtoupper(trim($result['2']))."'";
			//echo $query."<br>";
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
			  	die_silently($conn_Netone, $error_str);
			  	exit;
			} else {
			  OCIFreeStatement($stmt);
			}
			$amount=count($res1['RAFID']);
			//echo $amount;
			if($amount==1){
				$query="UPDATE BSDS_RAF_PO SET
						SHORTTEXT='".trim($result['3'])."',
						ITEMCOST='".trim($result['4'])."',
						PODATE='".trim($result['5'])."',
						INSERTDATE=SYSDATE
						WHERE RAFID='".trim($result['0'])."' AND POPR='".strtoupper(trim($result['1']))."' AND ACQCON='".strtoupper(trim($result['2']))."'";
				$message="UPDATED";
			}else{
				$query="INSERT INTO BSDS_RAF_PO (RAFID,POPR,ACQCON,SHORTTEXT,ITEMCOST,INSERTDATE,PODATE) VALUES('".trim($result['0'])."','".trim($result['1'])."','".trim($result['2'])."','".trim($result['3'])."','".trim($result['4'])."',SYSDATE,'".trim($result['5'])."')";
				$message="INSERTED";			
			}

			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt){
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
				
				$type="info";
			}	

			$table.='<tr class="success"><td>'.$i.'</td><td>'.$result['0'].'</td>'; //RAFID
			$table.="<td>".$result['1']."</td>";//POPR
			$table.="<td>".$result['2']."</td>";//ACQCON
			$table.="<td>".$result['3']."</td>";//SHORTTECT
			$table.="<td>".$result['4']."</td>";//ITEMCOST
			$table.="<td>".$result['5']."</td>";//PODATE
			$table.="<td>".$message."</td>";
			$table.="<td>".$type."</td></tr>";
			if ($out['type']=='error' && $general!='error'){
				$general='error';
				$data["message"]="Issues with importing!";
			}
		}
		$i++;
	}
	$table="<table class='table' style='width:900px'><thead><th>ID</th><th>RAFID</th><th>POPR</th><th>ACQCON</th><th>SHORTTEXT</th><th>ITEMCOST</th><th>PODATE</th><th>STATUS</th></thead>".$table."</table>";
	$data["type"]=$general;
	$data["table"]=$table;	
	echo json_encode($data);
}else if ($_POST['action']=="multianalyseDelete"){
	//$data=str_getcsv($_POST['csvdata']);
	//echo "<pre>".print_r($data)."<pre>";

	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdata']));
	if (trim($data[0][0])!="RAFID" or trim($data[0][1])!="ACQCON"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect';
		echo json_encode($res);
		exit;
	}
	//We now analyse the input
	$i=0;
	
	foreach ($data as $key => $result) {
		$message="";
		$status="OK";
		if ($i!=0){
			if (!is_numeric(trim($result['0']))){
				$message.="RAF ID ".$result['0']." has to be a number<br>";
				$status='NOTOK';
				$general_status='NOTOK';
			}else if (trim($result['1'])!="ACQ" && trim($result['1'])!="CON"){
				$message.="ACQCON (".$result['1'].") has to be ACQ or CON<br>";
				$status='NOTOK';
				$general_status='NOTOK';
			}else{
				$query = "select * from BSDS_RAF_PO WHERE RAFID='".trim($result['0'])."' AND ACQCON='".strtoupper(trim($result['1']))."'";
				//echo $query."<br>";
				$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
				if (!$stmt) {
				  	die_silently($conn_Netone, $error_str);
				  	exit;
				} else {
				  OCIFreeStatement($stmt);
				}
				$amount=count($res1['RAFID']);
				//echo $amount;
				if ($amount>="1"){
					$status="OK";
					$message="OK";
				}else{
					$status="NOTOK";
					$message="RAFID NOT EXISTING";
					$general_status='NOTOK';
				}
			}	

			if ($status=="OK"){
				$class="success";
			}else{
				$class="danger";
			}
			$out.='<tr class="'.$class.'"><td>'.$i.'</td><td>'.trim($result['0']).'</td>';
			$out.="<td>".trim($result['1'])."</td>";
			$out.="<td>".$message."</td></tr>";
			
		}
		$i++;
	}
	$out="<table class='table'><thead><th>ID</th><th>RAFID</th><th>ACQCON</th><th>&nbsp;</th></thead>".$out."</table>";
	if ($general_status!='NOTOK'){
		$retval["type"]='info';
		$retval["table"]=$out;
	}else{
		$retval["type"]='error';
		$retval["message"]="You have errors in your csv data!";
		$retval["table"]=$out;
	}	
	
	echo json_encode($retval);
}else if ($_POST['action']=="multidel"){
	$data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $_POST['csvdata']));
	if (trim($data[0][0])!="RAFID" or trim($data[0][1])!="ACQCON"){
		$res["type"]='error';
		$res["message"]='Headers are incorrect';
		echo json_encode($res);
		exit;
	}
	//We now analyse the input
	$i=0;
	//echo "<pre>".print_r($results,true)."</pre>";
	$general="info";
	foreach ($data as $key => $result) {
		if ($i!=0){
			
				$query="DELETE FROM  BSDS_RAF_PO 
						WHERE RAFID='".trim($result['0'])."' AND ACQCON='".strtoupper(trim($result['1']))."'";
				$message="DELETED";
			

			//echo $query."<hr>";
			$stmt = parse_exec_free($conn_Infobase, $query, $error_str);
			if (!$stmt){
				die_silently($conn_Infobase, $error_str);
			}else{
				OCICommit($conn_Infobase);
				
				$type="info";
			}	

			$table.='<tr class="success"><td>'.$i.'</td><td>'.$result['0'].'</td>';
			$table.="<td>".$result['1']."</td>";
			$table.="<td>".$message."</td>";
			$table.="<td>".$type."</td></tr>";
			if ($out['type']=='error' && $general!='error'){
				$general='error';
				$data["message"]="Issues with importing!";
			}
		}
		$i++;
	}
	$table="<table class='table' style='width:900px'><thead><th>ID</th><th>RAFID</th><th>ACQCON</th><th>STATUS</th><th>&nbsp;</th></thead>".$table."</table>";
	$data["type"]=$general;
	$data["table"]=$table;	
	echo json_encode($data);
}
ocilogoff($conn_Infobase);
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_txmn,Base_other,Base_other,Base_RF","");
require_once("/var/www/html/bsds/PHPlibs/oci8_funcs.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$months = array("Jan", "Feb", "Mar", "Apr","May","Jun","Jul","Sep","Oct","Nov", "Dec");

function incrementDate($startDate, $monthIncrement = 0) {

    $startingTimeStamp = $startDate->getTimestamp();
    // Get the month value of the given date:
    $monthString = date('Y-m', $startingTimeStamp);
    // Create a date string corresponding to the 1st of the give month,
    // making it safe for monthly calculations:
    $safeDateString = "first day of $monthString";
    // Increment date by given month increments:
    $incrementedDateString = "$safeDateString $monthIncrement month";
    $newTimeStamp = strtotime($incrementedDateString);
    $newDate = DateTime::createFromFormat('U', $newTimeStamp);
    return $newDate;
}

if ($_POST['year']==''){
    $currentDate = new DateTime();
    $oneMonthAgo = incrementDate($currentDate);
    $twoMonthsAgo = incrementDate($currentDate, -1);
    $threeMonthsAgo = incrementDate($currentDate, -2);
    $twoMonths = incrementDate($currentDate, 2);
    $threeMonths = incrementDate($currentDate, 3);
    $fourMonths = incrementDate($currentDate, 4);
    $fiveMonths = incrementDate($currentDate, 5);
    $sixMonths = incrementDate($currentDate, 6);

    $start=$oneMonthAgo->format('m')."-".$oneMonthAgo->format('Y');
    $end=$twoMonths->format('m')."-".$twoMonths->format('Y');
    $end2=$currentDate->format('m')."-".$currentDate->format('Y');
}else{

    $select_year=$_POST['year'];
    $start="01-".$select_year;
    $endyear=$select_year+1;
    $end="01-".$endyear;
    $end2=$end;
}

echo "<table class='table'>";
echo "<caption>CURRENT Dismantling/Replacements between ".$start." and ".$end2."</caption>";
echo "<thead><tr><th>SITEID</th><th>CANDIDATE</th><th>SITETYPE</th><th>STATUS</th><th>A250</th><th>A270</th><th>A270 ESTIM</th><th>A275</th></tr>";

$query = "SELECT
T_A250,
T_N1_SITEID,
T_N1_CANDIDATE,
T_N1_SITETYPE,
T_N1_STATUS,
T_A270,
T_AU270_ESTIM,
T_A275
FROM
    VW_DISM_REPL
WHERE T_A250 IS NOT NULL
AND EXTRACT(year FROM TO_DATE(T_A250))!='1990'
AND TO_DATE(T_A250)>= '01-".$start."'
AND TO_DATE(T_A250)< '01-".$end."'
ORDER BY 
TO_DATE(T_A250) DESC";
//echo $query;
$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
if (!$stmt) {
    die_silently($conn_Infobase, $error_str);
    exit;
} else {
    OCIFreeStatement($stmt);
}

if (count($res1['T_N1_SITEID'])>0){
	for ($i = 0; $i < count($res1['T_N1_SITEID']); $i++){
	    echo "<tr><td>".$res1['T_N1_SITEID'][$i]."</td>
	    <td>".$res1['T_N1_CANDIDATE'][$i]."</td>
	    <td>".$res1['T_N1_SITETYPE'][$i]."</td>
	    <td>".$res1['T_N1_STATUS'][$i]."</td>
	    <td>".$res1['T_A250'][$i]."</td>
	    <td>".$res1['T_A270'][$i]."</td>
	    <td>".$res1['T_AU270_ESTIM'][$i]."</td>
	    <td>".$res1['T_A275'][$i]."</td></tr>";
	}
}else{
	echo "<tr><td colspan='8'>No dis/repl in that period</td></tr>";
}
echo "</table>";
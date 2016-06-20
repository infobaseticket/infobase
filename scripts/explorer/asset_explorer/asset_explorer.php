<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

function get_flag($flagkey){
	 if ($flagkey=='178216300') return '-';
	 if ($flagkey=='178216301') return 'Active';
	 if ($flagkey=='178216302') return 'Inactive';
	 if ($flagkey=='178216303') return 'Ready_To_Build';
	 if ($flagkey=='178216304') return 'Released_with_Nominated';
	 if ($flagkey=='178216305') return 'Released';
	 if ($flagkey=='178216306') return 'Planned';
	 if ($flagkey=='178216307') return 'Study';

	 if ($flagkey=='179707384') return '-';
	 if ($flagkey=='179707385') return 'Rejected';
	 if ($flagkey=='179707386') return 'Preferred';
	 if ($flagkey=='179707387') return 'Approved';

	if ($flagkey=='179736311')   	return '-';
	if ($flagkey=='179736312')  	return 'ACTIVE';
	if ($flagkey=='1567764043')   	return 'FREQPLANNING_WORKPROGRESS';
	if ($flagkey=='1568107250')   	return 'INACTIVE';
	if ($flagkey=='179736313')  	return 'PARAMETERS_RELEASED';
	if ($flagkey=='415009229')   	return 'PLANNED';
	if ($flagkey=='415009230')   	return 'STUDY';
}

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

$type=$_POST['techno'];

if ($type=="GSM"){

		$query = "SELECT DISTINCT(SITENAME), CANDIDATE,RBS,DESCRIPTION,CABIN,PREDRADIUS,PREDRESOLUTION from VW2GSITE WHERE SITEKEY='".$_POST['lognodeID_GSM']."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			$Count1=count($res1['SITEKEY']);
			echo "<br><table class='table table-condensed table-striped table-hover'>";

			echo "<tr><th class='header'>CANDIDATE</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['CANDIDATE'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>RBS</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['RBS'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>DESCRIPTION</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['DESCRIPTION'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>CABIN</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['CABIN'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>MAST</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['TOWER'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>PREDRADIUS</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['PRERADIUS'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>PREDRESOLUTION</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['PREDRESOLUTION'][$i]."</td>";
			}
			echo "</tr>";

			$query = "select * from VWFLAGS WHERE SITEKEY='".$_POST['lognodeID_GSM']."'";

			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt);
				$Count2=count($res1['SITEKEY']);
				for ($i = 0; $i <= $Count2; $i++) {
					echo "<tr><th class='header'>".$res1['FLAGGROUP'][$i]."</th>";
					echo "<td>".$res1['FLAGID'][$i]."</td>";
					echo "</tr>";
				}
			}


			echo "</table><br>";
		}

		$query = "select * from VW2GCELL WHERE SITEKEY='".$_POST['lognodeID_GSM']."' ORDER BY SECTORID";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			$Count2=count($res1['CELLKEY']);
			//echo $Count2;
			echo "<table class='table table-condensed table-striped table-hover'>";
			/*
			echo "<tr><th class='header'>SITEKEY</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['SITEKEY'][$i]."</td>";
			}
			echo "</tr>";*/

			echo "<tr><th class='header'>SECTORID</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				//echo $res1['SECTORID'][$i]."<br>";
				if ($res1['SECTORID'][$i]==""){
					$SECTORID="EXTRA";
				}else{
					$SECTORID=$res1['SECTORID'][$i];
				}
				echo "<td class='sectorid'>".$res1['SECTORID'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>CELLSTAT</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				$cellstat=get_flag($res1['CELLSTAT'][$i]);
				echo "<td>".$cellstat."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>PROPMODEL</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['PROPMODEL'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>OUTPUTPOWER</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['OUTPUTPOWER'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>CELLAYER</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['CELLAYER'][$i]."</td>";
			}
			echo "</tr></table><br>";
		}
		$query = "select * from BSDSINFO2 WHERE SITEKEY='".$_POST['lognodeID_GSM']."' AND FEEDERKEY!='Unknown' ORDER BY SECTORID ASC";
		//echo $query;
		$stmt3 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res3);
		if (!$stmt3) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt3);
			$Count3=count($res3['SITEKEY']);
			echo "<div class='table-responsive table-responsive-force'>";
			echo "<table class='table table-condensed table-striped table-hover' id='ASSETTable".$_POST['siteID'].$type."' >";
			echo "<tr><th class='header'>ANTENNATYPE</th>";
			for ($i = 0; $i <= $Count3; $i++) {
				echo "<td>".$res3['ANTENNATYPE'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>ANTENNAHEIGHT</th>";
			for ($i = 0; $i <= $Count3; $i++) {
				if ($res3['ANTENNAHEIGHT'][$i]!=""){
					echo "<td>".round($res3['ANTENNAHEIGHT'][$i],2)."</td>";
				}
			}
			echo "</tr>";

			echo "<tr><th class='header'>FEEDERKEY</th>";
			for ($i = 0; $i <= $Count3; $i++) {
				echo "<td>".$res3['FEEDERKEY'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>FEEDERLENGTH</th>";
			for ($i = 0; $i <= $Count3; $i++) {
				echo "<td>".$res3['FEEDERLENGTH'][$i]."</td>";
			}
			echo "</tr>";
			/*
			echo "<tr><th class='header'>FEEDERLOSS</th>";
			for ($i = 0; $i <= $Count3; $i++) {
				echo "<td>".$res1['FEEDERLOSS'][$i]."</td>";
			}
			echo "</tr>";
			*/
			echo "<tr><th class='header'>DOWNTILT</th>";
			for ($i = 0; $i <= $Count3; $i++) {
				echo "<td>".$res3['DOWNTILT'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>AZIMUTH</th>";
			for ($i = 0; $i <= $Count3; $i++) {
				echo "<td>".$res3['AZIMUTH'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>CELLEQUIPMENT</th>";
			for ($i = 0; $i <= $Count3; $i++) {
				echo "<td>".$res3['CELLEQUIPMENT'][$i]."</td>";
			}
			echo "</tr>";
		}

		echo "</table></div><br>";


		$query = "select * from ch_type_car_state where IDNAME like '%".$_POST['siteID']."%' ORDER BY IDNAME,CARRIERNUMBER";
		//echo $query."<br>";

		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			$Count3=count($res2['IDNAME']);
			echo "<table class='table table-condensed table-striped table-hover'>";
			for ($j = 0; $j <= $Count3; $j++) {
				echo "<tr><th class='header'>".$res2['IDNAME'][$j]."</th><td>".$res2['TYPE'][$j]."</td><td>".$res2['CARRIERNUMBER'][$j]."</td><td>".$res2['STATE'][$j]."</td></tr>";
			}
			echo "</table>";
		}

}else if ($type=="U9" or $type=="U21"){
	if ($type=="U9"){
		$lognode=$_POST['lognodeID_UMTS900'];
	}else if ($type=="U21"){
		$lognode=$_POST['lognodeID_UMTS2100'];
	}
		$query = "select * from VW3GSITE WHERE lognodepk='".$lognode."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			$Count1=count($res1['LOGNODEPK']);
			echo "<br><table class='table table-condensed table-striped table-hover'>";
			/*
			echo "<tr><th class='header'>LOGNODEPK</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['LOGNODEPK'][$i]."</td>";
			}
			echo "</tr>";*/

			echo "<tr><th class='header'>LOGNODE</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td class='sectorid'>".$res1['LOGNODE'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>LOGNODEBTYPE</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['LOGNODEBTYPE'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>STATUSNOMINAL</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['STATUSNOMINAL'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>TGCARRIER</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['TGCARRIER'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>PREDRADIUS</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['PREDRADIUSMET1'][$i]."</td>";
			}
			echo "</tr>";
			echo "</table><br>";
		}


		$query = "select * from VW3GCELL WHERE LOGNODEPK='".$lognode."' ORDER BY UMTSCELL";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			$Count2=count($res1['LOGNODEPK']);
			echo "<div class='table-responsive table-responsive-force'>";
			echo "<table class='table table-condensed table-striped table-hover' id='ASSETTable".$_POST['siteID'].$type."' >";

			echo "<tr><th class='header'>LOGNODE</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['LOGNODE'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>UMTSCELL</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['UMTSCELL'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>ORTHOGFACTOR</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".round($res1['ORTHOGFACTOR'][$i],3)."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>NOISERISE</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['NOISERISE'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>COMMONCHANPWR</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".round($res1['COMMONCHANPWR'][$i],4)."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>PILOTPOWER</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['PILOTPOWER'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>MHGAIN</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['MHGAIN_DB'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>TXDIV</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['TXDIV'][$i]."</td>";
			}
			echo "</tr>";

			echo "<tr><th class='header'>RXDIV</th>";
			for ($i = 0; $i <= $Count2; $i++) {
				echo "<td>".$res1['RXDIV'][$i]."</td>";
			}
			echo "</tr>";

			$query = "select DISTINCT * from umtsbsds4 WHERE LOGNODEFK='".$lognode."' ORDER BY UMTSCELLID ASC";
			//echo $query;
			$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
			if (!$stmt) {
				die_silently($conn_Infobase, $error_str);
			 	exit;
			} else {
				OCIFreeStatement($stmt);
				$Count3=count($res2['LOGNODEFK']);

				echo "<tr><th class='header'>ANTTYPE</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['ANTTYPE'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>AZIMUTH</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['AZIMUTH'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>MECH_TILT</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['MECH_TILT'][$i]."</td>";
				}
				echo "</tr>";


				echo "<tr><th class='header'>ELEC_TILT</th>";
				for ($i = 0; $i <= $Count3; $i++) {
				$temp=explode("_",$res2['ELEC_TILT'][$i]);
					$amount1=count($temp)-1;
					$amount2=count($temp)-2;
					//echo $amount;
					if(is_numeric($temp[$amount1])){
						$ELECTILT=$temp[$amount1];
					}else{
						$ELECTILT=$temp[$amount2];
					}
					echo "<td>".$ELECTILT."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>MAXLOBETILT</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['MAXLOBETILT'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>HEIGHT</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".round($res2['HEIGHT'][$i],3)."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>FEEDERTYPE</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['FEEDERTYPE'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>FEEDERLENGTH</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['FEEDERLENGTH'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>MHA</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['MHA'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>STATE $Count3</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".get_flag($res1['FLAGKEY'][$i])."</td>";
				}
				echo "</tr>";

			}

			echo "</table></div><br>";
	}
}else if ($type=="L18" or $type=="L8" or $type=="L26"){
	if ($type=="L8"){
		$lognode=$_POST['lognodeID_LTE800'];
	}else if ($type=="L18"){
		$lognode=$_POST['lognodeID_LTE1800'];
	}else if ($type=="L26"){
		$lognode=$_POST['lognodeID_LTE2600'];
	}
		$query = "select * from LOGNODE@BASEPRO7 WHERE lognodepk='".$lognode."'";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res1);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
		 	exit;
		} else {
			OCIFreeStatement($stmt);
			$Count1=count($res1['LOGNODEPK']);
			echo "<br><table class='table table-condensed table-striped table-hover'>";
			echo "<tr><th class='header'>SITEID</th>";
			for ($i = 0; $i <= $Count1; $i++) {
					echo "<td>".$res1['IDNAME'][$i]."</td>";
			}
			echo "</tr>";
			echo "<tr><th class='header'>CANDIDATE</th>";
			for ($i = 0; $i <= $Count1; $i++) {
				echo "<td>".$res1['NAME'][$i]."</td>";
			}
			echo "</tr>";
			echo "</table><br>";
		}


		$query = "select DISTINCT * from LTE1 WHERE LOGNODEFK='".$lognode."' ORDER BY UMTSCELLID ASC";
		//echo $query;
		$stmt = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
		if (!$stmt) {
			die_silently($conn_Infobase, $error_str);
			exit;
		} else {
				OCIFreeStatement($stmt);
				$Count3=count($res2['LOGNODEFK']);
				echo "<div class='table-responsive table-responsive-force'>";
				echo "<table class='table table-condensed table-striped table-hover' id='ASSETTable".$_POST['siteID'].$type."' >";
				
				
				echo "<tr><th class='header'>CELLID</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['UMTSCELLID'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>ANTTYPE</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['ANTTYPE'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>AZIMUTH</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['AZIMUTH'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>MECH_TILT</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['MECH_TILT'][$i]."</td>";
				}
				echo "</tr>";


				echo "<tr><th class='header'>ELEC_TILT</th>";
				for ($i = 0; $i <= $Count3; $i++) {
				$temp=explode("_",$res2['ELEC_TILT'][$i]);
					$amount1=count($temp)-1;
					$amount2=count($temp)-2;
					//echo $amount;
					if(is_numeric($temp[$amount1])){
						$ELECTILT=$temp[$amount1];
					}else{
						$ELECTILT=$temp[$amount2];
					}
					echo "<td>".$ELECTILT."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>MAXLOBETILT</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['MAXLOBETILT'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>HEIGHT</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".round($res2['HEIGHT'][$i],3)."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>FEEDERTYPE</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['FEEDERTYPE'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>FEEDERLENGTH</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['FEEDERLENGTH'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>MHA</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".$res2['MHA'][$i]."</td>";
				}
				echo "</tr>";

				echo "<tr><th class='header'>STATE $Count3</th>";
				for ($i = 0; $i <= $Count3; $i++) {
					echo "<td>".get_flag($res1['FLAGKEY'][$i])."</td>";
				}
				echo "</tr>";

		}
		echo "</table></div><br>";



}
?>
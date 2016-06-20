<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once("cur_plan_procedures.php");

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['bsdsbobrefresh']=="PRE"){
	$bsdsdata="PRE READY TO BUILD";
	$_POST['bsdsbobrefresh']="";
}else{
	$bsdsdata=$_POST['bsdsdata'];
}

if ($_POST['status']=="FUNDHIST"){ //HISTORY VIEW
	$viewtype="FUND";
	$color="BSDS_funded";
	$viewhistory="yes";
	$status="BSDS FUNDED";
}else if ($_POST['status']=="POSTHIST"){//HISTORY VIEW
	$viewtype="POST";
	$viewhistory="yes";
	$color="SITE_funded";
	$status="SITE FUNDED";
}else if ($_POST['status']=="BUILDHIST"){//HISTORY VIEW
	$viewtype="BUILD";
	$viewhistory="yes";
	$color="BSDS_asbuild";
	$status="BSDS AS BUILD";
}else if ($_POST['status']=="PRE"){ //PRE VIEW
	$viewtype="PRE";
	$viewhistory="no";
	$color="BSDS_preready";
	$status="PRE READY TO BUILD";
}else if ($_POST['status']=="PREHIST"){ //HISTORY PRE VIEW
	$viewtype="PRE";
	$viewhistory="yes";
	$color="BSDS_preready";
	$status="PRE READY TO BUILD HISTORY";
}else{
	$bsdsdata=json_decode($_POST['bsdsdata'],true);
	//echo "<pre>".print_r($bsdsdata,true);
	if ($bsdsdata['G9STATUS']=="BSDS FUNDED"){
		$viewtype="FUND";
		$viewhistory="no";
		$status="BSDS FUNDED";
	}else if ($bsdsdata['G9STATUS']=="SITE FUNDED"){
		$viewtype="POST";
		$viewhistory="no";
		$status="SITE FUNDED";
	}else if ($bsdsdata['G9STATUS']=="BSDS AS BUILD"){
		$viewtype="BUILD";
		$viewhistory="no";
		$status="BSDS AS BUILD";
	}else if ($bsdsdata['G9STATUS']=="PRE READY TO BUILD"){
		$viewtype="PRE";
		$viewhistory="no";
		$status="PRE READY TO BUILD";
	}
	$color=$bsdsdata[$_POST['band'].'COLOR'];
}


function get_data_bipt($techno,$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM){
	
	global $conn_Infobase;

	$check_current_exists=check_current_exists($techno,$_POST['bsdskey'],$_POST['bsdsbobrefresh'],'allsec',$_POST['donor'],$lognodeID_GSM,$viewtype);
	
	$data[$techno]='No';

	if ($check_current_exists!=0 || $viewtype=="FUND"){
		$check_planned_exists=check_planned_exists($_POST['bsdskey'],$_POST['bsdsbobrefresh'],$techno,'allsec',$viewtype,$_POST['donor']);
		if ($check_planned_exists==="error"){
			echo "<h1>Sytem error</h1>There are too many records in the database for G9 BSDS! Please contact Frederick Eyland",
			die;
		}
	}else{
		$check_planned_exists=0;
		$GSM900='No';
		$data[$techno]='No';
	}

	if($check_planned_exists!=0){
		$data[$techno]='Yes';
		$band=$techno;
		include("planned_data.php");
		include("height_conversion.php");
		if ($techno=="G18"){
			$j=1;
			$letter="A";
			$Frequency=1870;
		}else if ($techno=="G9"){
			$j=4;
			$letter="A";
			$Frequency=930;
		}else if ($techno=="U21"){
			$j=7;
			$letter="U";
			$Frequency=2130;
		}else if ($techno=="U9"){
			$j=4;
			$letter="U";
			$Frequency=930;
		}else if ($techno=="L8"){
			$j=4;
			$letter="L";
			$Frequency=796;
		}else if ($techno=="L18"){
			$j=1;
			$letter="L";
			$Frequency=1870;
		}else if ($techno=="L26"){
			$j=7;
			$letter="L";
			$Frequency=2600;
		}
		
		for ($i=1;$i<=4;$i++){
			if ($i==4){
				$j=0;
			}
			$pl_ANTTYPE1="pl_ANTTYPE1_".$i;
			$pl_ANTTYPE1=$$pl_ANTTYPE1;
			$pl_ANTHEIGHT1="pl_ANTHEIGHT1_".$i;
			$pl_ANTHEIGHT1=$$pl_ANTHEIGHT1;
			$pl_MECHTILT1="pl_MECHTILT1_".$i;
			$pl_MECHTILT1=$$pl_MECHTILT1;
			$pl_ANTHEIGHT1_t="pl_ANTHEIGHT1_".$i."_t";
			$pl_ANTHEIGHT1_t=$$pl_ANTHEIGHT1_t;

			$pl_AZI="pl_AZI_".$i;
			$pl_AZI=$$pl_AZI;

			$pl_AZI="pl_AZI_".$i;
			$pl_AZI=$$pl_AZI;

			$pl_ANTTYPE2="pl_ANTTYPE2_".$i;
			$pl_ANTTYPE2=$$pl_ANTTYPE2;
			$pl_ANTHEIGHT2="pl_ANTHEIGHT2_".$i;
			$pl_ANTHEIGHT2=$$pl_ANTHEIGHT2;
			$pl_MECHTILT2="pl_MECHTILT2_".$i;
			$pl_MECHTILT2=$$pl_MECHTILT2;
			$pl_ANTHEIGHT2_t="pl_ANTHEIGHT2_".$i."_t";
			$pl_ANTHEIGHT2_t=$$pl_ANTHEIGHT2_t;


			$antenna= substr($pl_ANTTYPE1,0, strrpos($pl_ANTTYPE1,"_"));

			$query1 = "select IDNAME from ANTENNADEVICE@BASEPRO7 WHERE IDNAME LIKE '".$antenna."%'";
			//echo "$query1 <br>";
			$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
			if (!$stmt) {
			  die_silently($conn_Infobase, $error_str);
			  exit;
			} else {
			  OCIFreeStatement($stmt);
			  $antenna= $res1['IDNAME'][0];
			}



			if ($pl_ANTTYPE1!=''){

			$data1.='
				<tr>
					<td><b>AntennaNumberOnPlan</td>
					<td><input type="text" name="AntennaNumberOnPlan[]" value="sec'.$j.'A"></td>
				</tr>
				<tr>
					<td>AntennaType</td>
					<td><input type="text" name="AntennaType[]" value="'.$antenna.'"></td>
				</tr>
				<tr>
					<td>AntennaPositionHeight</td>
					<td><input type="text" name="AntennaPositionHeight[]" value="'.$pl_ANTHEIGHT1.'m'.$pl_ANTHEIGHT1_t.'"></td>
				</tr>
				<tr>
					<td>AntennaAzimut</td>
					<td><input type="text" name="AntennaAzimut[]" value="'.$pl_AZI.'"></td>
				</tr>
				<tr>
					<td>AntennaMechanicalTilt</td>
					<td><input type="text" name="AntennaMechanicalTilt[]" value="'.$pl_MECHTILT1.'"></td>
				</tr>
				<tr>
					<td>AntennaEntryPowerBm</td>
					<td><input type="text" name="AntennaEntryPowerBm[]" value="'.$AntennaEntryPowerBm.'"></td>
				</tr>
				<tr>
					<td>AntennaNumberOfTransmitters</td>
					<td><input type="text" name="AntennaNumberOfTransmitters[]" value="'.$AntennaNumberOfTransmitters.'"></td>
				</tr>
				<tr>
					<td>Frequency</td>
					<td><input type="text" name="Frequency[]" value="'.$Frequency.'"></td>
				</tr>
				<tr>
					<td>AntennaDescription</td>
					<td><input type="text" name="AntennaDescription[]" value="'.$AntennaDescription.'"></td>
				</tr>';

				$j++;
			}
			if ($pl_ANTTYPE2!=''){
			$data2.='
				<tr>
					<td><b>AntennaNumberOnPlan</td>
					<td><input type="text" name="AntennaNumberOnPlan[]" value="sec'.$j.'B"></td>
				</tr>
				<tr>
					<td>AntennaType</td>
					<td><input type="text" name="AntennaType[]" value="'.$pl_ANTTYPE2.'"></td>
				</tr>
				<tr>
					<td>AntennaPositionHeight</td>
					<td><input type="text" name="AntennaPositionHeight[]" value="'.$pl_ANTHEIGHT2.'m'.$pl_ANTHEIGHT2_t.'"></td>
				</tr>
				<tr>
					<td>AntennaAzimut</td>
					<td><input type="text" name="AntennaAzimut[]" value="'.$pl_AZI.'"></td>
				</tr>
				<tr>
					<td>AntennaMechanicalTilt</td>
					<td><input type="text" name="AntennaMechanicalTilt[]" value="'.$pl_MECHTILT2.'"></td>
				</tr>
				<tr>
					<td>AntennaEntryPowerBm</td>
					<td><input type="text" name="AntennaEntryPowerBm[]" value="'.$AntennaEntryPowerBm.'"></td>
				</tr>
				<tr>
					<td>AntennaNumberOfTransmitters</td>
					<td><input type="text" name="AntennaNumberOfTransmitters[]" value="'.$AntennaNumberOfTransmitters.'"></td>
				</tr>
				<tr>
					<td>Frequency</td>
					<td><input type="text" name="Frequency[]" value="'.$Frequency.'"></td>
				</tr>
				<tr>
					<td>AntennaDescription</td>
					<td><input type="text" name="AntennaDescription[]" value="'.$AntennaDescription.'"></td>
				</tr>';
				$j++;
			}
		}
	}
	$data["ant1"]=$data1;
	$data["ant2"]=$data2;
	return $data;
}

/*GSM data*/
$band=$_POST['band'];
$bsdskey=$_POST['bsdskey'];
$bsdsbobrefresh=$_POST['bsdsbobrefresh'];
$donor=$_POST['donor'];
$lognodeID_GSM=$_POST['lognodeID_GSM'];

$dataG9=get_data_bipt('G9',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM);
$dataG18=get_data_bipt('G18',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$lognodeID_GSM);

$dataU9=get_data_bipt('U9',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$_POST['lognodeID_UMTS900']);
$dataU21=get_data_bipt('U21',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$_POST['lognodeID_UMTS2100']);
$dataL8=get_data_bipt('L8',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$_POST['lognodeID_LTE800']);
$dataL18=get_data_bipt('L18',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$_POST['lognodeID_LTE1800']);
$dataL26=get_data_bipt('L26',$viewtype,$bsdskey,$bsdsbobrefresh,$donor,$_POST['lognodeID_LTE2600']);

$query1 = "select * from infobase.coord WHERE SITENAME LIKE '%".$_POST['candidate']."%'";
//echo "$query1 <br>";
$stmt = parse_exec_fetch($conn_Infobase, $query1, $error_str, $res1);
if (!$stmt) {
  die_silently($conn_Infobase, $error_str);
  exit;
} else {
  OCIFreeStatement($stmt);
}
$latitude=$res1['LATITUDE'][0];
$longitude=$res1['LONGITUDE'][0];
$last_line =shell_exec("coord_conv $longitude $latitude");
$longlat=explode(" ",$last_line);
?>
<form id="biptout<?=$_POST['candidate']?>" action="<?=$config['server_root_url']?>/bsds/scripts/current_planned/bipt_generate.php" target="_new" method="post">
	<table>
		<tr>
			<td>Language</td>
			<td><input type="text" name="Language"></td>
		</tr>
		<tr>
			<td>NameApplicant</td>
			<td><input type="text" name="NameApplicant"></td>
		</tr>
		<tr>
			<td>AddressApplicant</td>
			<td><input type="text" name="AddressApplicant"></td>
		</tr>
		<tr>
			<td>CityApplicant1</td>
			<td><input type="text" name="CityApplicant1"></td>
		</tr>
		<tr>
			<td>NameContactApplicant</td>
			<td><input type="text" name="NameContactApplicant"></td>
		</tr>
		<tr>
			<td>TelContactApplicant</td>
			<td><input type="text" name="TelContactApplicant"></td>
		</tr>
		<tr>
			<td>EmailContactApplicant</td>
			<td><input type="text" name="EmailContactApplicant"></td>
		</tr>
		<tr>
			<td>RefApplicant</td>
			<td><input type="text" name="RefApplicant" value="<?=$_POST['candidate']?>"></td>
		</tr>
		<tr>
			<td>SiteDescription</td>
			<td><input type="text" name="SiteDescription"></td>
		</tr>
		<tr>
			<td>SiteShareable</td>
			<td><input type="text" name="SiteShareable"></td>
		</tr>
		<tr>
			<td>OtherExploitant</td>
			<td><input type="text" name="OtherExploitant"></td>
		</tr>
		<tr>
			<td>OtherExploitant</td>
			<td><input type="text" name="OtherExploitant"></td>
		</tr>
		<tr>
			<td>OtherExploitant</td>
			<td><input type="text" name="OtherExploitant"></td>
		</tr>
		<tr>
			<td>OtherExploitant</td>
			<td><input type="text" name="OtherExploitant"></td>
		</tr>
		<tr>
			<td>OtherExploitant</td>
			<td><input type="text" name="OtherExploitant"></td>
		</tr>
		<tr>
			<td>OtherExploitant</td>
			<td><input type="text" name="OtherExploitant"></td>
		</tr>
		<tr>
			<td>OtherExploitant</td>
			<td><input type="text" name="OtherExploitant"></td>
		</tr>
		<tr>
			<td>OtherExploitant</td>
			<td><input type="text" name="OtherExploitant"></td>
		</tr>
		<tr>
			<td>OwnerName</td>
			<td><input type="text" name="OwnerName"></td>
		</tr>
		<tr>
			<td>OwnerAddress</td>
			<td><input type="text" name="OwnerAddress"></td>
		</tr>
		<tr>
			<td>OwnerContact</td>
			<td><input type="text" name="OwnerContact"></td>
		</tr>
		<tr>
			<td>TelContactOwner</td>
			<td><input type="text" name="TelContactOwner"></td>
		</tr>
		<tr>
			<td>AddressSite</td>
			<td><input type="text" name="AddressSite"></td>
		</tr>
		<tr>
			<td>DescriptionLocationSite</td>
			<td><input type="text" name="DescriptionLocationSite"></td>
		</tr>
		<tr>
			<td>CitySite</td>
			<td><input type="text" name="CitySite"></td>
		</tr>
		<tr>
			<td>XCoordinateSite</td>
			<td><input type="text" name="XCoordinateSite" value="<?=$longlat[0]?>"></td>
		</tr>
		<tr>
			<td>YCoordinateSite</td>
			<td><input type="text" name="YCoordinateSite" value="<?=$longlat[1]?>"></td>
		</tr>
		<tr>
			<td>HeightSupport</td>
			<td><input type="text" name="HeightSupport"></td>
		</tr>
		<tr>
			<td>AntennasApplicant</td>
			<td><input type="text" name="AntennasApplicant"></td>
		</tr>
		<tr>
			<td>AntennasThirdParties</td>
			<td><input type="text" name="AntennasThirdParties"></td>
		</tr>
		<tr>
			<td>SiteExists</td>
			<td><input type="text" name="SiteExists"></td>
		</tr>
		<tr>
			<td>GSM900</td>
			<td><input type="text" name="GSM900" value="<?=$dataG9['G9']?>"></td>
		</tr>
		<tr>
			<td>GSM1800</td>
			<td><input type="text" name="GSM1800" value="<?=$dataG18['G18']?>"></td>
		</tr>
		<tr>
			<td>UMTS900</td>
			<td><input type="text" name="UMTS900" value="<?=$dataU9['U9']?>"></td>
		</tr>
		<tr>
			<td>UMTS2100</td>
			<td><input type="text" name="UMTS2100" value="<?=$dataU21['U21']?>"></td>
		</tr>
		<tr>
			<td>LTE800</td>
			<td><input type="text" name="LTE800" value="<?=$dataL8['L8']?>"></td>
		</tr>
		<tr>
			<td>LTE1800</td>
			<td><input type="text" name="LTE1800" value="<?=$dataL18['L18']?>"></td>
		</tr>
		<tr>
			<td>LTE2600</td>
			<td><input type="text" name="LTE2600" value="<?=$dataL26['L26']?>"></td>
		</tr>
		<tr>
			<td>FH</td>
			<td><input type="text" name="FH"></td>
		</tr>
		<tr>
			<td>Outside</td>
			<td><input type="text" name="Outside"></td>
		</tr>
		<tr>
			<td>OwnerCity</td>
			<td><input type="text" name="OwnerCity"></td>
		</tr>
		<tr>
			<td>InfoAA09</td>
			<td><input type="text" name="InfoAA09"></td>
		</tr>
		<tr>
			<td>InfoOwnerES05</td>
			<td><input type="text" name="InfoOwnerES05"></td>
		</tr>
		<tr>
			<td>InfoSiteLS06</td>
			<td><input type="text" name="InfoSiteLS06"></td>
		</tr>
		<tr>
			<td>InfoAS05</td>
			<td><input type="text" name="InfoAS05"></td>
		</tr>
		
		<?php echo $dataG9['ant1']; ?>
		<?php echo $dataG9['ant2']; ?>
		<?php echo $dataG18['ant1']; ?>
		<?php echo $dataG18['ant2']; ?>
		<?php echo $dataU9['ant1']; ?>
		<?php echo $dataU9['ant2']; ?>
		<?php echo $dataU21['ant1']; ?>
		<?php echo $dataU21['ant2']; ?>
		<?php echo $dataL8['ant1']; ?>
		<?php echo $dataL8['ant2']; ?>
		<?php echo $dataL18['ant1']; ?>
		<?php echo $dataL18['ant2']; ?>
		<?php echo $dataL26['ant1']; ?>
		<?php echo $dataL26['ant2']; ?>

	</table>
<input type="submit" value="Generate xls" class="btn btn-primary generateBIPT" data-siteid="<?=$_POST['candidate']?>">
</form>
<hr>
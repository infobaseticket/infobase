<?PHP
require_once($_SERVER['DOCUMENT_ROOT']."/include/config.php");
require_once($config['phpguarddog_path']."/guard.php");
protect("","Radioplanners","");

if (ereg("[A-Za-z]", $HeightSupport) || ereg("[A-Za-z]", $AntennasApplicant) || ereg("[A-Za-z]", $AntennasThirdParties) ){
		echo "<p align='center'><font color=red size=3><b>The fields HeightSupport,  AntennasApplicant and AntennasThirdParties may only contain numeric values!</b></font>
				<br>Rightmouse and hit back.</p>";
}else if ($AntennaNumberOfTransmitters1=="-" || ($AntennaNumberOfTransmitters1< 2 && $AntennaNumberOfTransmitters1!="")
|| $AntennaNumberOfTransmitters2=="-" || ($AntennaNumberOfTransmitters2 < 2 && $AntennaNumberOfTransmitters2!="")
|| $AntennaNumberOfTransmitters3=="-" || ($AntennaNumberOfTransmitters3 < 2 && $AntennaNumberOfTransmitters3!="")
|| $AntennaNumberOfTransmitters4=="-" || ($AntennaNumberOfTransmitters4 < 2 && $AntennaNumberOfTransmitters4!="")
|| $AntennaNumberOfTransmitters5=="-" || ($AntennaNumberOfTransmitters5 < 2 && $AntennaNumberOfTransmitters5!="")
|| $AntennaNumberOfTransmitters6=="-" || ($AntennaNumberOfTransmitters6 < 2 && $AntennaNumberOfTransmitters6!="")
|| $AntennaNumberOfTransmitters7=="-"
||$AntennaNumberOfTransmitters8=="-" 
||$AntennaNumberOfTransmitters9=="-" ){
		echo "<p align='center'><font color=red size=3><b>The TRX's must be filled in correctly (more than 2 TRX's)!!</b></font>
				<br>Rightmouse and hit back.</p>";
}else if ($AntennaEntryPowerBm1=="-" || $AntennaEntryPowerBm2=="-" || $AntennaEntryPowerBm3=="-" || $AntennaEntryPowerBm4=="-"
|| $AntennaEntryPowerBm5=="-" || $AntennaEntryPowerBm6=="-" || $AntennaEntryPowerBm7=="-" || $AntennaEntryPowerBm8=="-" 
|| $AntennaEntryPowerBm9=="-" || $AntennaEntryPowerBm1=="0" || $AntennaEntryPowerBm2=="0" || $AntennaEntryPowerBm3=="0" 
|| $AntennaEntryPowerBm4=="0" || $AntennaEntryPowerBm5=="0" || $AntennaEntryPowerBm6=="0" || $AntennaEntryPowerBm7=="0" 
|| $AntennaEntryPowerBm8=="0" || $AntennaEntryPowerBm9=="0"  ){
echo "<p align='center'><font color=red size=3><b>The Power must be filled in correctly !!</b></font>
				<br>Rightmouse and hit back.";
}else if ($AntennaNumberOfTransmitters1_2=="-" || $AntennaNumberOfTransmitters2_2=="-" || $AntennaNumberOfTransmitters3_2=="-" || 
$AntennaNumberOfTransmitters4_2=="-" || $AntennaNumberOfTransmitters5_2=="-" || $AntennaNumberOfTransmitters6_2=="-" ||
$AntennaNumberOfTransmitters7_2=="-" ||$AntennaNumberOfTransmitters8_2=="-" ||$AntennaNumberOfTransmitters9_2=="-" ){
		echo "<p align='center'><font color=red size=3><b>The TRX's must be filled in correctly!!</b></font>
				<br>Rightmouse and hit back.</p>";
}else if (strlen($AddressSite)> 50 || strlen($OwnerAddress)>50){
		echo "<p align='center'><font color=red size=3><b>The siteaddress and owneraddress may not be longer then 50 chars!!</b></font>
				<br>Rightmouse and hit back.</p>";
}else{
	
	$filename="BIPT_".$_POST['RefApplicant']."_".$_SESSION['BSDSID'].".xls";
	//header("Content-type: application/vnd.ms-excel"); 
	//header("Content-Disposition: attachment; filename=$filename"); 
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$filename");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	?>
	<HTML>
	<HEAD>
	<TITLE><? echo "BIPT for $RefApplicant - $datetime"; ?></TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=">
	</HEAD>
	
	<BODY>
	<?	
	echo "
	<table>
	<tr>
		<td align='left'>XCoordinateSite
		<td align='left'>".$_POST['XCoordinateSite']."
	</tr>
	<tr>
		<td align='left'>YCoordinateSite
		<td align='left'>".$_POST['YCoordinateSite']."
	</tr>";
	
	$w=0;
	
	for ($i = 1; $i <= 18; $i++) {
		$AntennaDescriptionA="";
		$AntennaDescriptionB="";
		$AntennaNumber ="";
		$k=0;
		
		 $temp = "AntennaNumber$i"; 
		 $AntennaNumber = $_POST[$temp];
		 
		 if ($AntennaNumber!="" AND $AntennaNumber%2!=0){ 
	       
			if ($AntennaNumber=="1"){
			  	$k=1;
			}else if ($AntennaNumber=="2"){
			 	$k=1;
			}else if ($AntennaNumber=="3"){
			 	$k=2;		
			}else if ($AntennaNumber=="4"){
			 	$k=2;		
			}else if ($AntennaNumber=="5"){
			 	$k=3;		
			}else if ($AntennaNumber=="6"){
			 	$k=3;
			}else if ($AntennaNumber=="7"){
			 	$k=4;
			}else if ($AntennaNumber=="8"){
			 	$k=4;
			}else if ($AntennaNumber=="9"){
			 	$k=5;
			}else if ($AntennaNumber=="10"){
			 	$k=5;
			}else if ($AntennaNumber=="11"){
			 	$k=6;
			}else if ($AntennaNumber=="12"){
			 	$k=6;		
			}else if ($AntennaNumber=="13"){
			 	$k=7;		
			}else if ($AntennaNumber=="14"){
			 	$k=7;		
			}else if ($AntennaNumber=="15"){
			 	$k=8;		
			}else if ($AntennaNumber=="16"){
			 	$k=8;		
			}else if ($AntennaNumber=="17"){
			 	$k=9;		
			}else if ($AntennaNumber=="18"){
			 	$k=9;		
			}

			 //echo "<tr>	<td>hier1 ---1 $AntennaNumber1 --2 $AntennaNumber2 --3 $AntennaNumber3 (AntennaNumber = $AntennaNumber => k= $k)  <br> ";
				 
			 $tempA = "AntennaDescription".$k."A"; 
			 $AntennaDescription = $_POST[$tempA];
			 $temp = "AntennaType$k"; 
			 $AntennaType = $_POST[$temp]; 		 
			 $temp = "AntennaPositionHeight$k"; 
			 $AntennaPositionHeight = $_POST[$temp];
			 $temp = "AntennaAzimut$k"; 
			 $AntennaAzimut = $_POST[$temp];
			 $temp = "AntennaElectricalTilt$k"; 
			 $AntennaElectricalTilt = $_POST[$temp]; 
			 $temp = "AntennaMechanicalTilt$k"; 
			 $AntennaMechanicalTilt = $_POST[$temp]; 
			 $temp = "Frequency$k"; 
			 $Frequency = $_POST[$temp]; 
			 $temp = "AntennaNumberOfTransmitters$k"; 
			 $AntennaNumberOfTransmitters = $_POST[$temp]; 
	
		}else if ($AntennaNumber!="" AND $AntennaNumber%2==0){ 
			// echo "<tr><td>hier2 ---1 $AntennaNumber1 --2 $AntennaNumber2 --3 $AntennaNumber3 (AntennaNumber = $AntennaNumber => k= $k)  <br> ";
			if ($AntennaNumber=="1"){
			  	$k=1;
			}else if ($AntennaNumber=="2"){
			 	$k=1;
			}else if ($AntennaNumber=="3"){
			 	$k=2;		
			}else if ($AntennaNumber=="4"){
			 	$k=2;		
			}else if ($AntennaNumber=="5"){
			 	$k=3;		
			}else if ($AntennaNumber=="6"){
			 	$k=3;
			}else if ($AntennaNumber=="7"){
			 	$k=4;
			}else if ($AntennaNumber=="8"){
			 	$k=4;
			}else if ($AntennaNumber=="9"){
			 	$k=5;
			}else if ($AntennaNumber=="10"){
			 	$k=5;
			}else if ($AntennaNumber=="11"){
			 	$k=6;
			}else if ($AntennaNumber=="12"){
			 	$k=6;		
			}else if ($AntennaNumber=="13"){
			 	$k=7;		
			}else if ($AntennaNumber=="14"){
			 	$k=7;		
			}else if ($AntennaNumber=="15"){
			 	$k=8;		
			}else if ($AntennaNumber=="16"){
			 	$k=8;		
			}else if ($AntennaNumber=="17"){
			 	$k=9;		
			}else if ($AntennaNumber=="18"){
			 	$k=9;		
			}
			
			 $tempB = "AntennaDescription".$k."B"; 
			 $AntennaDescription = $_POST[$tempB];; 
	
			 $temp = "AntennaType".$k."_2"; 
			 $AntennaType = $_POST[$temp];
			 $temp = "AntennaPositionHeight".$k."_2"; 
			 $AntennaPositionHeight = $_POST[$temp];
			 $temp = "AntennaAzimut".$k."_2"; 
			 $AntennaAzimut = $_POST[$temp];
			 $temp = "AntennaElectricalTilt".$k."_2"; 
			 $AntennaElectricalTilt = $_POST[$temp];
			 $temp = "AntennaMechanicalTilt".$k."_2"; 
			 $AntennaMechanicalTilt = $_POST[$temp];
			 $temp = "Frequency".$k."_2"; 
			 $Frequency = $_POST[$temp]; 
			 $temp = "AntennaNumberOfTransmitters".$k."_2"; 
			 $AntennaNumberOfTransmitters = $_POST[$temp]; 
		}	 
	
		if ($AntennaDescription!='' && $AntennaDescription!='None' && $AntennaNumber!=""){
			$w++;
			echo "
			<tr>
				<td align='left'><font color='red'><b>AntennaNumberOnPlan</b></font>
				<td align='left'>sec $AntennaDescription
			</tr>
			<tr>
				<td align='left'>AntennaType
				<td align='left'>$AntennaType
			</tr>
			<tr>
				<td align='left'>AntennaPositionHeight
				<td align='left'>$AntennaPositionHeight
			</tr>
			<tr>
				<td align='left'>AntennaAzimut
				<td align='left'>$AntennaAzimut
			</tr>
			<tr>
				<td align='left'>AntennaMechanicalTilt
				<td align='left'>$AntennaMechanicalTilt
			</tr>
			<tr>
				<td align='left'>AntennaNumberOfTransmitters
				<td align='left'>$AntennaNumberOfTransmitters
			</tr>
			<tr>
				<td align='left'>Frequency
				<td align='left'>$Frequency
			</tr>
			<tr>
				<td align='left'><b>AntennaDescription</b></font>
				<td align='left'>$w
			</tr>";   
		} 
	}
		
} //END only numeric

?>
</table>
</BODY>
</HTML>

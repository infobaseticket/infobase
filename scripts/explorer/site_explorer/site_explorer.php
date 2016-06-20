<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');

if ($_POST['bypass']!='yes'){
require($config['phpguarddog_path']."/guard.php");
protect("","Base_RF,Base_TXMN,Base_delivery,Base_other,Base_risk,Partner,Administrators","");
}

$SiteID=$_POST['siteID'];
if (strlen($SiteID)==8){
	$SiteID=substr($SiteID,0,-2);
}
if (strlen($SiteID)==7){
	$SiteID=substr($SiteID,0,-1);
}

require_once('site_explorer_procedures.php');

if ($SiteID){
	$data2G=array();
	$data2G=get_GSM_data($SiteID);
	//echo "<pre>".print_r($data,true)."</pre>";
	$G2=count($data2G);
	for($i=0;$i<count($data2G);$i++){

		$data2G=get_cell_data($data2G,$i);
		$data2G=get_site_data($data2G,$i);
		$data2G=get_repeater_data($data2G,$i);
	}

	//$data3G=get_3G_data($SiteID);
	$data3GZTE=get_3GZTE_data($SiteID);
	$G3=count($data3GZTE);

	$data4GZTE=get_4GZTE_data($SiteID);
	$G4=count($data4GZTE);

	//echo "<pre>".print_r($data2G,true)."</pre>";

}

if ($G2==0 && $G3==0 && $G4==0){
	echo "<center><br><br><h3><span class='label label-danger'>No definitions found for that site on OSS!</span></h3></center>";
}else{
	/*
	if ($G2==0){
		echo '<span class="label label-warning">No 2G data found on OSS</span><br>';
	}
	if ($G3==0){
		echo '<span class="label label-warning">No 3G data found on NetNumen</span><br>';
	}
	if ($G4==0){
		echo '<span class="label label-warning">No 4G data found on NetNumen</span><br>';
	}*/
}

//echo "pre>".print_r($data3GZTE,true)."</pre>";

if ($message){
	?>
	<script>
  	$(document).ready(function(){
       $("#information")
      .fadeIn('slow')
      .animate({opacity: 1.0}, 3000)
	  });
	</script>
	<div id="information"><? echo $alert; ?></div>
	<?
}
			//echo "<pre>".print_r($data,true)."</pre>";
			$a=0;
			for($i=0;$i<count($data2G);$i++){
			?>
				<div class="pull-left well" style="margin-right:10px;">
				<table class="table table-stiped table-condensed">
				<thead>
					<th><h2>2G:
					<? foreach ($data2G[$i]['TECHNO'] as $techno){
						echo $techno."&nbsp;";
					}?>
					</h2</th>
				</thead>
				<tbody>
				   	<tr>
					  <td class='title'>RSITE:</td>
					  <td class='data'><?=$data2G[$i]['rsite']?></td>
				   </tr>
				   <tr>
					 <td class='title'>BSC:</td>
					 <td class='data'><?=$data2G[$i]['BSC_display']?></td>
				   </tr>
				   <tr>
					 <td class='title'>TG:</td>
					 <td class='data'><?=$data2G[$i]['TG_display']?></td>
				   </tr>
				   <? if (count($data[$num]['REPEATER'])!=0){ ?>}
				   <tr>
					 <td class='title'>REPEATER:</td>
					 <td class='data'>
					 <?
					 foreach ($data2G[$i]['REPEATER'] as $repeater){
					 	echo $repeater."<br>";
					 	}
					 ?>
					 </td>
				   </tr>
				   <?
				   }?>
					<tr>
					 <td class='title'>CAB type:</td>
					 <td class='data'><?=$data2G[$i]['CABTYPE']?></td>
				   </tr>
				   <tr>
					 <td class='title'>TFMODE:</td>
					 <td class='data'><?=$data2G[$i]['TFMODE']?></td>
				   </tr>
				   <tr>
					 <td class='title'>DXU:</td>
					 <td class='data'><?=$data2G[$i]['DXU']?></td>
				   </tr>
				   <tr>
					 <td class='title'>TEI:</td>
					 <td class='data'><?=$data2G[$i]['TEI']?></td>
				   </tr>
				   <tr>
					 <td class='title'>CDU:</td>
					 <td class='data'><?=$data2G[$i]['CDU']?></td>
				   </tr>
				   <tr>
				   		<td colspan="2">
				   			
				   <?
				    if (count($data2G[$i]['celddata'])!=0){
					    foreach ($data2G[$i]['celddata'] as $celldata){
					   		?>
					   		<table width="100%">
					   		<tr>
							   <td class='cell' colspan="2"><?=$celldata['cell']?></td>
							</tr>				
							<tr>
								<td>State:</td>
								<?php if ($celldata['STATE']!='ADMINISTRATIVE HALTED'){ ?>
								<td><span class="label label-default"><?=$celldata['STATE']?></span></td>
								<?php }else{ ?>
								<td><span class="pointer" rel="popover" data-toggle="popover" data-placement="top" data-content="<?=$celldata['STATEINFO']?>"><span class="label label-danger"> <?=$celldata['STATE']?></span></span></td>
								<?php } ?>
							</tr>
							<tr>
							    <td colspan="2"><button class="btn btn-default btn-xs osstrx" id="TRX<?=$celldata['cell']?>"><b>TRX (<?=$celldata['TRXAMOUNT']?>)</b></button><br>
							   		<table class="table table-condensed TRX<?=$celldata['cell']?>_data" style="display: none;">
							   		<tr>
										<td class='parameter_title'>TRXNUM</td>
										<td class='parameter_title'>TRXTYPE</td>
										<td class='parameter_title'>BAND</td>
										<td class='parameter_title'>RX</td>
										<td class='parameter_title'>SIGNAL</td>
									</tr>
							   		<?
						   			foreach ($celldata['TRX'] as $TRXdata){
							   		?>
							   		<tr>
									   <td class='parameter_data'><?=$TRXdata['TRXNUM']?></td>
									   <td class='parameter_data'><?=$TRXdata['TRXTYPE']?></td>
									   <td class='parameter_data'><?=$TRXdata['BAND']?></td>
									   <td class='parameter_data'><?=$TRXdata['RX']?></td>
									   <td class='parameter_data'><?=$TRXdata['SIGNAL']?></td>
									</tr>
							   		<?
							   		}
						   			?>
						   			<tr>
							   			<td class='parameter_title'>CB</td>
							   			<td class='parameter_data'><?=$celldata['CB']?></td>
									</tr>
									<tr>
							   			<td class='parameter_title'>CGI</td>
							   			<td class='parameter_data'><?=$celldata['CGI']?></td>
									</tr>
							   		</table>
								</td>
							</tr>
							<tr>
							    <td colspan="2"><button class="btn btn-default btn-xs osstrx" id="FREQ<?=$celldata['cell']?>"><b>FREQUENCIES</b></button><br>
							   		<table class="table table-condensed FREQ<?=$celldata['cell']?>_data" style="display: none;">
							   		<tr>
							   			<td class='title'>BCCHNO</td>
							   			<td class='data' colspan="5"><?=$celldata['BCCHNO']?></td>
							   		</tr>
					   				<tr>
										<td class='parameter_title'>DCHNO</td>
										<td class='parameter_title'>HSN</td>
										<td class='parameter_title'>SDCCH</td>
										<td class='parameter_title'>HOP</td>
										<td class='parameter_title'>CHGR</td>
									</tr>
									<?
						   			foreach ($celldata['FREQS'] as $BCCHNO_data){
							   		?>
							   		<tr>
									   <td class='parameter_data'><?=$BCCHNO_data['DCHNO']?></td>
									   <td class='parameter_data'><?=$BCCHNO_data['HSN']?></td>
									   <td class='parameter_data'><?=$BCCHNO_data['SDCCH']?></td>
									   <td class='parameter_data'><?=$BCCHNO_data['HOP']?></td>
									   <td class='parameter_data'><?=$BCCHNO_data['CHGR']?></td>
									</tr>
							   		<?
							   		}
						   			?>
							   		</table>
								</td>
							</tr>
							<tr>
							    <td colspan="2"><button class="btn btn-default btn-xs osstrx" id="ABIS<?=$celldata['cell']?>"><b>A-BIS</b></button><br>
							   		<table class="table table-condensed ABIS<?=$celldata['cell']?>_data" style="display: none;">
							   		<tr>
										<td class="parameter_title">RBLT</td>
										<td class="parameter_title">ETRBLT</td>
										<td class="parameter_title">DCP</td>
										<td class="parameter_title">SIGNALLING</td>
										<td class="parameter_title">TEI</td>
									</tr>
									<?

						   			foreach ($data2G[$i]['ABIS'] as $ABIS){
							   		?>
							   		<tr>
									   <td class='parameter_data'><?=$ABIS['RBLT']?></td>
									   <td class='parameter_data'><?=$ABIS['ETRBLT']?></td>
									   <td class='parameter_data'><?=$ABIS['DCP']?></td>
									   <td class='parameter_data'><?=$ABIS['SIGNALLING']?></td>
									   <td class='parameter_data'><?=$ABIS['TEI']?></td>
									</tr>
							   		<?
							   		}
						   			?>
							   		</table>
								</td>
							</tr>
						</table>
					
					<?
						}?>
					</td>
					</tr>
					<?
					}else{
						?>
						<tr>
						<td colspan="2" align="center"><font color="red">NO DATA FOUND ON OSS!</font></td>
						</tr>
						<?
					}
					?>
					</tbody>
				</table>
				</div>
				<?
				if ($data2G[$i]['error']!=""){
					echo "<font color='red'>".$data2G[$i]['error']."</font>";
				}
			}
/*
			for($i=0;$i<count($data3G);$i++){
			?>
				<div class="pull-left well" style="margin-right:10px;">
				<table class="table table-stiped table-condensed">
				<thead>
					<th><h2>3G</h2></th>
				</thead>
				<tbody>
				<tr>
			   		<td colspan='2' class="siteID_title"><b><?=$data2G[$i]['site']?> <?=$data2G[$i]['technology']?></b></td>
			   	</tr>
			   	<tr>
				  <td class='title'>E/// RBS:</td>
				  <td class='data'><?=$data3G[$i]['RBS']?></td>
			   </tr>
			   <tr>
				  <td class='title'>RNC:</td>
				  <td class='data'><?=$data3G[$i]['RNC']?></td>
			   </tr>
			   <tr>
				  <td class='title'>RBS CAB TYPE:</td>
				  <td class='data'>
				  	<? foreach ($data3G[$i]['RBSTYPE'] as $RBSTYPE){
				  		echo $RBSTYPE."<br>";
						}?>
					</td>
			   </tr>
			   <tr>
				  <td class='title'>CELLS:</td>
				  <td class='data'>
				  	<table width="160px">
			  		<tr>
			  			<td><b>Cell</b></td>
						<td><b>HSPX</b></td>
						<td><b>Scrambling code</b></td>
					</tr>

			  		<? foreach ($data3G[$i]['CELLS'] as $CELL){
			  			echo "<tr><td>".$CELL."</td><td>".$data3G[$i][$CELL]['HSPX']."</td><td>".$data3G[$i][$CELL]['CODE']."</td></tr>";
					}?>
					</table>
				  </td>
			   </tr>
			   </tbody>
			   </table>
			</div>
			<?php } */
			?>
			
			
			<?php
			foreach($data3GZTE as $key=>$techno){
				?>
				<div class="pull-left well" style="margin-right:10px;">
					<table class="table table-stiped table-condensed table-condensed table-condensed">
						<thead>
							<th><h2>3G: <?=$key?></h2</th>
						</thead>
						<tbody>
					    <tr>
						  <td class='title'>ZTE RNC:</td>
						  <td class='data'><?=$techno['RNC']?></td>
					    </tr>
					    <tr>
						  <td class='title'>NODEB:</td>
						  <td class='data'>
						  	<?php 
						  	foreach ($techno['RBS'] as $key=>$RBS){
						  		echo $RBS; 
								if ($celldata['STATE'][$key]!='ADMINISTRATIVE HALTED'){ 
								echo ' <span class="label label-default">'.$techno['STATE'][$key].'</span><br>';
								}else{
								echo ' <span class="pointer" rel="popover" data-toggle="popover" data-placement="top" data-content="'.$techno['STATEINFO'][$key].'"><span class="label label-danger">'.$techno['STATE'][$key].'</span></span><br>';
								}
							} ?>
							</td>
					    </tr>
					    
					    <tr>
						  <td class='title'>SLOTS:</td>
						  <td class='data'>
						  	<table calss="table table-condensed">
						  		<thead>
						  			<th>Slot</th>
						  			<th>&nbsp;</th>
						  		</thead>
						  		<tbody>
						  	<? if (is_array($techno['SLOTS'])){
						  		foreach ($techno['SLOTS'] as $key=>$SLOT){
						  		echo "<tr><td>".$SLOT['SLOTNO']."</td><td>".$SLOT['PRODNAME']."</td></tr>";
								}
								}
							?>
								</tbody>
							</table>
						   </td>
					    </tr>
					   </tbody>
				   </table>
				</div>
			<?php } ?>
		
			<?php

		if (is_array($data4GZTE['TECHNOS'])){
			foreach($data4GZTE['TECHNOS'] as $key=>$techno){
				?>
				<div class="pull-left well" style="margin-right:10px;">
				 	<table class="table table-stiped table-condensed">
						<thead>
							<th><h2>4G: <?=$key?></h2</th>
						</thead>
						<tbody>
					    <tr>
						  <td class='title'>NODEB:</td>
						  <td class='data'>
						  	<?php 
						  	foreach ($techno['RBS'] as $key=>$RBS){
						  		echo $RBS; 
								if ($celldata['STATE'][$key]!='ADMINISTRATIVE HALTED'){ 
								echo ' <span class="label label-default">'.$techno['STATE'][$key].'</span><br>';
								}else{
								echo ' <span class="pointer" rel="popover" data-toggle="popover" data-placement="top" data-content="'.$techno['STATEINFO'][$key].'"><span class="label label-danger">'.$techno['STATE'][$key].'</span></span><br>';
								}
							} ?>
							</td>
					    </tr>
					    
					    <tr>
						  <td class='title'>BPL:</td>
						  <td class='data'>
						  	<?php
						  	if (is_array($data4GZTE['BPLPORTS'])){

						  		foreach ($data4GZTE['BPLPORTS'] as $BPLPORT){
						  		echo $BPLPORT."<br>";
								}
							}
							?>
							</td>
					    </tr>

					    <tr>
						  <td class='title'>MIMO/RU:</td>
						  <td class='data'>
						  	<?php
						  	if (is_array($techno['CELLEQ'])){ 

						  		foreach ($techno['CELLEQ'] as $key=>$data){
						  			echo $key.": ".$data['MIMO']."  ".$data['RU']."<br>";
								}
							}
							?>
							</td>
					    </tr>
					   </tbody>
				   	</table>
				</div>
			<? }
		}
		?>
<div class="pull-left" style="margin-right:10px;">
<?php
	if ($data2G[0]['TECHNO'][0]!='G9' && $data2G[1]['TECHNO'][0]!='G9'){
		echo '<h4><span class="label label-warning">No G9 data found on OSS</span></h4>';
	}
	if ($data2G[0]['TECHNO'][0]!='G18' && $data2G[1]['TECHNO'][0]!='G18'){
		echo '<h4><span class="label label-warning">No G18 data found on OSS</span></h4>';
	}
	if (!is_array($data3GZTE['U9'])){
		echo '<h4><span class="label label-warning">No U9 data found on NetNumen</span></h4>';
	}
	if (!is_array($data3GZTE['U21'])){
		echo '<h4><span class="label label-warning">No U21 data found on NetNumen</span></h4>';
	}
	if (!is_array($data4GZTE['TECHNOS']['L8'])){
		echo '<h4><span class="label label-warning">No L8 data found on NetNumen</span></h4>';
	}
	if (!is_array($data4GZTE['TECHNOS']['L18'])){
		echo '<h4><span class="label label-warning">No L18 data found on NetNumen</span></h4>';
	}
?>
</div>
		
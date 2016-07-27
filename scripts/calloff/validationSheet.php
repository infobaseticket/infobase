
<!DOCTYPE html>
<head>
<link rel="stylesheet" href="<?=$config['explorer_url']?>bootstrap/css/bootstrap.min.css" media="all">
</head>
<body>
    <div id="print-area">
        <div id="header">
        	<div class="row">
			  <div class="col-md-6"><p class="text-left"><img src="<?php echo $config['sitepath_url']; ?>/bsds/images/benchmark.png" ></p></div>
			  <div class="col-md-6"><p class="text-right"><img src="<?php echo $config['sitepath_url']; ?>/bsds/images/basecompany.png" width="200px" height="80px"></p></div>
			</div>
            
        </div>
        <div id="content">
            <h1>REQUEST FOR VALIDATION</h1>
			<p align="justify">
			<?php echo $res1['N1_CON'][$i]; ?> hereby certifies that all services have been performed for the  deliverables, "Site Acquired", "Site PAC" and "Site FAC"
			(whatever applicable as documented per site &#45; see table below), in full accordance with the BASE COMPANY &#45; / KPN GROUP BELGIUM	<?php echo $res1['N1_CON'][$i]; ?> FRAMEWORK AGREEMENT NETWORK 
			ROLLOUT &amp; OPERATIONS SERVICES.</p>
			<p align="justify">
			Upon signing this Validation Request by BASE COMPANY/KPN Group Belgium, the signing date shall be the effective date used to toggle the relevant Validation 
			milestone in NetOne (toggling of the validation milestones shall be done by Base Company/KPN Group Belgium only).</p>
			<p align="justify">
			In case, after audit by BASE COMPANY//KPN Group Belgium of all contractually required services leading to the claimed performed deliverables, it is determined that 
			the claimed deliverable is not fully achieved according to the Framework Agreement, Base Company reserves the right to withdraw the applicable 
			Validation acceptance. In this case, Base Company will inform Service Provider and remove the applicable milestones in NetOne. This could lead to a 
			the sending of a Credit Note and recalculation of the applicable KPI&rsquo;s by Service Provider.
			</p>
			<b><u>Validation request overview (For “Site Acquired”, “Site PAC”, “Site FAC”):</u></b>
			<br><br>
			<table class="table table-bordered">
				<thead>
					<th>Site ID (+Cand.)</th>
					<th>Site Type</th>
					<th>Upgrade Number</th>
					<th>Deliverable</th>
					<th>Base PO-Item Number</th>
					<th>Cost</th>
					<th>Acquired Technologies</th>
				</thead>
				<tbody>
				<?php
					echo $td;
				?>
				</tbody>
				<tfoot><tr><td colspan="8"><i>Grand Total: <?=$pos?></i></td></tr></tfoot>
			</table>
			<br><br>
			<table  width="100%">
				<tr>
					<td width="50%"><img src="<?=$config['sitepath_url']?>/bsds/scripts/calloff/NathalieZutterman.jpg" ></td>
					<td width="50%">&nbsp;</td>
				</tr>
				<tr>
					<td width="50%">Name: Nathalie Zutterman</td>
					<td width="50%">Date: </td>
				</tr>
				<tr>
					<td width="50%">Date: <?php echo date('d-M-Y'); ?></td>
					<td width="50%">Date: </td>
				</tr>
			</table>
			

			
        </div>
        <div id="footer">
            <span id="footertext">Base Company N.V./S.A<br><?php echo $supplier; ?> N.V./S.A.<br>File generated by Infobase on <?php echo date('d-M-Y'); ?>.</span>
            <span id="footerlogo"><img src="<?=$config['sitepath_url']?>/bsds/images/logoInfobase.png" width="125px" height="27px"> </span>
        </div>
    </div>

</body>
</html>
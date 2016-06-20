<div class="well well-small" style="border-color:#428bca;">
	<div class="row">
 	 	<div class="col-md-4">
			<table border='0'>
			<tbody>
			<tr>
				<td><b>Site Identity:</b></td>
				<td><span class="label label-info" style="font-size:16px;"><?=$_POST['siteID']?></span></td>
			</tr>
			<tr>
				<td><b>Candidate (Firstname Asset):</b></td>
				<td><span class="label label-default" style="font-size:16px;"><?=$_POST['candidate']?></span></td>
			</tr>
			<? if ($_POST['donor']){ ?>
			<tr>
				<td><b>Donor site:</b></td>
				<td><font color="red"><?=$_POST['donor']?></font></td>
			</tr>
			<? } ?>
			<tr>
				<td><b>Techno's defined in Asset:</b></td>
				<td><span class="label label-warning" style="font-size:14px;"><?=$technosInAsset?></span></td>
			</tr>
			</tbody>
			</table>
		</div>
		<div class="col-md-6">
			<table border='0'>
			<tbody>
			<tr>
				<td valign="top"><b>Address:</b></td>
				<td><?=$adress?></td>
			</tr>
			<tr>
				<td><b>Class code:</b></td>
				<td><?=$Classcode?></td>
			</tr>
			<tr>
				<td><b>X - Y coordinates:</b></td>
				<td><?=$coor['longitude']?> - <?=$coor['latitude']?> (<?=$coor['x']?> - <?=$coor['y']?>)</td>
			</tr>
			<tr>
				<td style="vertical-align: top;"><b>Latest refresh of N1 data:</b></td>
				<td>UPG: <?=$REFRESH_DATE['DATE_ALL_UPG']?><br>NEW: <?=$REFRESH_DATE['DATE_ALL_NEW']?></td>
			</tr>
			</tbody>
			</table>
		</div>
		<div class="col-md-2">
			<button id="newbsds" type="button" class='btn btn-sm new_BSDS' href='scripts/general_info2/general_info_newbsds.php' data-addressfk='<?=$_POST['ADDRESSFK']?>' data-siteid='<?=$_POST['siteID']?>' data-candidate='<?=$_POST['candidate']?>' data-toggle='modal' data-target='#myModal'><span class="glyphicon glyphicon-plus-sign"></span> Add a new BSDS</a>
		</div>
	</div>
</div>
<?php
echo $pop_data; 
echo $output_form;
?>

<div class="table-responsive">
	<table class="table table-hover">
	<tr style="background-color:#428bca;color:#fff;font-weight:bold;height:"><td colspan="7" style="text-align:center;font-size:12px;">OVERVIEW</td></tr>
	<thead>
	<tr>
		<th>BSDSID</th>
		<th><span class='glyphicon glyphicon-road' rel='tooltip' title='Radio Access Form'></span> RAFID</th>
		<th>RAF TECHNOS CON</th>
		<th>REFRESH</th>
		<th>N1 BSDS STATUS</th>
		<th style="width:150px;">BSDS TYPE</th>
		<!--<th>PARTNER STATUS?</th>-->
		<th style="width:50px;">
			<button type="button" class="btn btn-info btn-xs history" href="#" id="history">
			<span class="glyphicon glyphicon-circle-arrow-down"></span> HIST STATUS</button>
			<button type="button" class="btn btn-info btn-xs history" href="#" id="historyPerdate">
			<span class="glyphicon glyphicon-circle-arrow-down"></span> HIST DATE</button>
		</th>
	</tr>
	</thead>
	<tbody>
	<? echo $output_pre; ?>
	<? echo $output_funded; ?>
	<? echo $output_asbuild; ?>
	<tr class="history_data" style="background-color:#8fdeea;font-weight:bold;display:none;"><td colspan="7" style="text-align:center;font-size:12px;">PRE</td></tr>
	<? echo $output_pre_history; ?>
	<tr class="history_data" style="background-color:#8fdeea;font-weight:bold;display:none;"><td colspan="7" style="text-align:center;font-size:12px;">FUND</td></tr>
	<? echo $output_fund_history; ?>
	<tr class="history_data" style="background-color:#8fdeea;font-weight:bold;display:none;"><td colspan="7" style="text-align:center;font-size:12px;">AS-BUILD</td></tr>
	<? echo $output_asbuild_history; ?>
	<tr class="historyPerdate_data" style="background-color:#8fdeea;font-weight:bold;display:none;"><td colspan="7" style="text-align:center;font-size:12px;">HISTORY</td></tr>
	<? echo $output_HistoryPerDate; ?>
	</tbody>
	</table>
</div>
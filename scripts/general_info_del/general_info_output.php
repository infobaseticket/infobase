<div class="well well-small">
	<div class="row">
 	 	<div class="col-md-10">
			<table border='0'>
			<tbody>
			<tr>
				<td>Site Identity:</td>
				<td><pan class="label label-info"><?=$_POST['siteID']?></span></td>
			</tr>
			<tr>
				<td>Candidate (Firstname Asset):</td>
				<td><span class="label label-default"><?=$_POST['candidate']?></span></td>
			</tr>
			<? if ($_POST['donor']){ ?>
			<tr>
				<td>Donor site:</td>
				<td><font color="red"><?=$_POST['donor']?></font></td>
			</tr>
			<? } ?>
			<tr>
				<td valign="top">Address:</td>
				<td><?=$adress?></td>
			</tr>
			<tr>
				<td>Class code:</td>
				<td><?=$Classcode?></td>
			</tr>
			<tr>
				<td>X - Y coordinates:</td>
				<td><?=$coor['longitude']?> - <?=$coor['latitude']?> (<?=$coor['x']?> - <?=$coor['y']?>)</td>
			</tr>
			<tr>
				<td>Techno's Asset:</td>
				<td><?=$_POST['technos']?></td>
			</tr>
			</tbody>
			</table>
		</div>
		<div class="col-md-2">
			<button id="newbsds" type="button" class='btn btn-sm new_BSDS' href='scripts/general_info/general_info_newbsds.php' data-addressfk='<?=$_POST['ADDRESSFK']?>' data-siteid='<?=$_POST['siteID']?>' data-candidate='<?=$_POST['candidate']?>' data-toggle='modal' data-target='#myModal'><span class="glyphicon glyphicon-plus-sign"></span> Add a new BSDS</a>
		</div>
	</div>
</div>
<?php
echo $pop_data; 
echo $output_form;
?>
<br>
<div class="table-responsive">
	<table class="table table-hover">
		<caption style="background-color:#428bca;color:#fff;font-weight:bold;">PRE OVERVIEW</caption>
	<thead>
	<tr>
		<th>BSDS ID</th>
		<th style="width:150px;">BSDS Type</th>
		<th>Last update</th>
		<th>Ready to fund?</th>
		<th style="width:50px;">
			<button type="button" class="btn btn-info btn-xs history" href="#" id="prehistory">
			<span class="glyphicon glyphicon-circle-arrow-down"></span> HISTORY</button>
		</th>
	</tr>
	</thead>
	<tbody>
	<? echo $output_pre; ?>
	<? echo $output_pre_history; ?>
	</tbody>
	</table>
</div>
<br>
<div class="table-responsive">
	<table class="table table-hover table-condensed">
		<caption style="background-color:#428bca;color:#fff;font-weight:bold;">FUNDED OVERVIEW</caption>
	<thead>
	<tr>
		<th>BSDS ID</th>
		<th style="width:150px;">UPGNR</th>
		<th>Technology</th>
		<th>Action by?</th>
		<th>Funded date</th>
		<th>Partner</th>
		<th>Status</th>
		<th style="width:60px;">
			<button type="button" class="btn btn-info btn-xs history" href="#" id="fund_hist">
			<span class="glyphicon glyphicon-circle-arrow-down"></span> HISTORY</button>
		</th>
	</tr>
	</thead>
	<tbody>
	<? echo $output_funded; ?>
	</tbody>
	</table>
</div>

<div class="table-responsive">
	<table class="table table-hover table-condensed">
	<? echo $output_fund_history; ?>
	</table>
</div>
<br>
<div class="table-responsive">
	<table class="table table-hover table-condensed">
		<caption style="background-color:#428bca;color:#fff;font-weight:bold;">AS BUILD OVERVIEW</caption>
	<thead>
	<tr>
		<th>BSDS ID</th>
		<th style="width:150px;">UPGNR</th>
		<th>Technology</th>
		<th>Site Int.(A71/U571)</th>
		<th>Copied date</th>
		<th>Status</th>
		<th style="width:50px;">
			<button type="button" class="btn btn-info btn-xs history" href="#" id="build_hist">
			<span class="glyphicon glyphicon-circle-arrow-down"></span> HISTORY</button>
			</div>
		</th>
	</tr>
	</thead>
	<tbody>
	<? echo $output_asbuild; ?>
	</tbody>
	</table>
</div>
<div class="table-responsive">
	<table class="table table-hover table-condensed">
	<? echo $output_asbuild_history; ?>
	</table>
</div>
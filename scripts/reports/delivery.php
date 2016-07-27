<link rel="stylesheet" href="scripts/reports/reports.css" type="text/css"></link>
<script language="JavaScript">
$(document).ready( function() {

	$(".delivery_tab").click(
	function(){	
		idnr=$(this).attr("id");
		//alert(idnr);
		year=$('#year').val();
		phases=$('#phases').val();
		$('.active').removeClass("active");
		$('.Delivery_tab').addClass("active");
		$(this).addClass("active");	
		$('#report').attr({ 
          src: "../bsds2/scripts/reports/Network_delivery.php?report="+idnr+"&year="+year+"&phases="+phases
        });	
	});	
	$('#progress').addClass("active");	
});
</script>


<ul id="techno_nav">
	<li class="delivery_tab pointer" id="progress">Progress</li>
	<li class="delivery_tab pointer" id="sitesscope">Sites in scope</li>
	<li class="delivery_tab pointer" id="acquisition">Aquisition</li>
	<li class="delivery_tab pointer" id="buffer">Buffer</li>
	<li class="delivery_tab pointer" id="construction">Construction</li>
	<li class="delivery_tab pointer" id="fac">Fac</li>
</ul>

<div id="reportfilters">
Year:
<select name="year" id="year">
<option value=''>NA</option>
<option>2009</option>
<option>2008</option>
<option>2007</option>
<option>2006</option>
</select>
HSDSA phase:
<select name="phases" id="phases">
<option value=''>NA</option>
<option value='1'>HSDPA Phase 1</option>
<option value='2'>HSDPA Phase 2</option>
</select>
</div>
<iframe id="report" src="scripts/reports/Network_delivery.php" SCROLLING="no" FRAMEBORDER="0" style="width:100%;height:430px"></iframe>
<!--<img src='../../images/icons/Microsoft Office Excel.png' width="50px" height="50px">-->
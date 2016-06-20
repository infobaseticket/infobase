<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_other,Base_RF,Base_TXMN,Base_delivery,Alcatel,Alcatel_sub","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");

$siteID=$_POST['siteID'];
$viewtype=$_POST['viewtype'];

if ($_POST['xlsprint']!="yes"){
	?>
	<link rel="stylesheet" href="scripts/los/los.css" type="text/css"></link>
	<script type="text/javascript" src="scripts/los/los.js"></script>
	<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-pagination/jquery.pagination.js"></script>
	<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-pagination/pagination.css" type="text/css"></link>
	<?
	$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
	$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
	OCIExecute($stmt,OCI_DEFAULT);

	if ($_POST['actionby']=="Partner Processing"){
		$where .= " (DONE = 'NOT OK' or DONE='REJECTED')";
	}else if ($_POST['actionby']=="Partner Reporting"){
		$where .= "( REPORT = 'NOT OK' OR REPORT='REJECTED') AND DONE='OK'";
	}else if ($_POST['actionby']=="TXMN Resulting"){
		$where .= " RESULT = 'NOT OK' AND DONE='OK' AND REPORT='OK'";
	}else if ($_POST['actionby']=="Canceled"){
		$where .= " PRIORITY = 'Canceled'";
	}else{
		$where .= " SITEA IS NOT NULL";
	}

	if ($_POST['allocated']=="ALU"){
			$where .= " AND (CON LIKE '%ALU%' or TYPE = 'ST')";
	}
	if ($_POST['allocated']=="BENCHMARK"){
			$where .= " AND CON = 'BENCHMARK' AND TYPE!='ST'";
	}

	if ($_POST['TYPE']!=""){
		$where .= " AND TYPE ='".$_POST['TYPE']."'";
	}

	if ($_POST['region']!="" && $_POST['region']!="ALL"){
		$where .= " AND (SITEA LIKE '%".$_POST['region']."%' OR SITEB LIKE '%".$_POST['region']."%')";
	}

	if (!$_POST['orderby']){
		$_POST['orderby']="ID";
	}

	$query2="SELECT COUNT(ID) AS TOTAL FROM BSDS_LINKINFO a LEFT JOIN VW_NET1_ALL_NEWBUILDS b ON a.SITEB=b.SIT_UDK  WHERE". $where ." AND (substr(b.SIT_UDK,2,6)=substr(b.WOR_UDK,2,6)) ORDER BY ".$_POST['orderby']." ". $_POST['order'];
	//echo $query2;
	$stmt2 = parse_exec_fetch($conn_Infobase, $query2, $error_str, $res2);
	if (!$stmt2) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt2);
		$TOT_amount_of_LOS=$res2['TOTAL'][0];
	}
	if ($TOT_amount_of_LOS>0){
	?>
	<script language="javascript">
	$(document).ready(function() {
	 //pagination

	    /**
	     * Callback function that displays the content.
	     *
	     * Gets called every time the user clicks on a pagination link.
	     *
	     * @param {int}page_index New Page index
	     * @param {jQuery} jq the container with the pagination links as a jQuery object
	     */
		function pageselectCallback(page_index, jq){
	        //var new_content = $('#hiddenresult div.result:eq('+page_index+')').clone();
	        ///$('#Searchresult').empty().append(new_content);
	        //alert(page_index);

	        start= page_index*10+1;
	        end = (page_index+1)*10;

	        $("#loading").show('fast');
	        $("#LosOutput").load("scripts/los/los_data.php",
			{
				viewtype:'report',
				actionby: '<?=$_POST['actionby']?>',
				orderby:'<?=$_POST['orderby']?>',
				order:'<?=$_POST['order']?>',
				type:'<?=$_POST['TYPE']?>',
				region:'<?=$_POST['region']?>',
				allocated:'<?=$_POST['allocated']?>',
				start: start,
				end: end,
				tabid: $.session("tabid")

			},
			function(){
				$("#loading").hide();
			});

	        return false;
	    }

	    /**
	     * Callback function for the AJAX content loader.
	     */
	    function initPagination() {
	        // Create pagination element
	        $("#Pagination").pagination(<?=$TOT_amount_of_LOS?>, {
	            num_edge_entries: 2,
	            num_display_entries: 10,
	            callback: pageselectCallback,
	            items_per_page:10
	        });
	     }

	  initPagination();

	});
	</script>

	<? } ?>

	<div id="Pagination" class="pagination"></div>
	<div><b>TOTAL: <?=$TOT_amount_of_LOS?></b></div>
	<div id="LosOutput" style="clear:both"></div>
<?
}else{ //xls ouput
	?>
	<script language="JavaScript">
	var actionby='<?=$_POST['actionby']?>';
	var order='<?=$_POST['order']?>';
	var type='<?=$_POST['TYPE']?>';
	var orderby='<?=$_POST['orderby']?>';
	var region='<?=$_POST['region']?>';
	var allocated='<?=$_POST['allocated']?>';
	window.open('scripts/los/los_data_xls.php?xlsprint=yes&type='+type+'&region='+region+'&actionby='+actionby+'&order='+order+'&orderby='+orderby+'&allocated='+allocated);
	</script>
	<?
}

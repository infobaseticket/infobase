 <?
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
require_once($config['phpguarddog_path']."/guard.php");
protect("","Administrators,Base_delivery,Base_other","");
require_once($config['sitepath_abs']."/bsds/PHPlibs/oci8_funcs.php");
require_once($config['sitepath_abs']."/bsds/PHPlibs/phpmailer/class.phpmailer.php");
include('../audits/audit_procedures.php');
?>
<script type="text/javascript" src="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-pagination/jquery.pagination.js"></script>
<link rel="stylesheet" href="<?=$config['sitepath_url']?>/include/javascripts/jquery/jquery-pagination/pagination.css" type="text/css">
<?
$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET nls_date_format='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

if ($_POST['xlsprint']!="yes"){

	$query=query_audit('','',$_POST['audittype1'],$_POST['audittype2'],$_POST['region'],$_POST['datefilter'],$_POST['daterange'],$_POST['orderby'],$_POST['order'],"no");
	//echo $query;
	$stmt2 = parse_exec_fetch($conn_Infobase, $query, $error_str, $res2);
	if (!$stmt2) {
		die_silently($conn_Infobase, $error_str);
	 	exit;
	} else {
		OCIFreeStatement($stmt2);
		$TOT_amount_of_AUDITS=count($res2['SITEID']);
	}
	?>
	<script language="javascript">
	$(document).ready(function() {
		function pageselectCallback(page_index, jq){
            start= page_index*10+1;
            end = (page_index+1)*10;

            $("#loadingbar"+$.session("tabid")).show('fast');
            $("#RafOutput").load("scripts/audits/audit.php",
			{
				audittype1: '<?=$_POST['audittype1']?>',
				audittype2: '<?=$_POST['audittype2']?>',
				datefilter: '<?=$_POST['datefilter']?>',
				daterange: '<?=$_POST['daterange']?>',
				region: '<?=$_POST['region']?>',
				orderby:'<?=$_POST['orderby']?>',
				order:'<?=$_POST['order']?>',
				start: start,
				end: end,
				tabid:$.session("tabid")

			},
			function(){
				$("#loadingbar"+$.session("tabid")).hide();
			});
            return false;
        }

        /**
         * Callback function for the AJAX content loader.
         */
        function initPagination() {
            // Create pagination element
			amountpages='<?=$TOT_amount_of_RAFS?>';
            $("#Pagination").pagination(amountpages, {
                num_edge_entries: 2,
                num_display_entries: 10,
                callback: pageselectCallback,
                items_per_page:10
            });
         }

	  	initPagination();
	});
	</script>

	<div id="Pagination" class="pagination"></div>
	<div><b>TOTAL: <?=$TOT_amount_of_RAFS?></b></div>
	<div id="RafOutput" style="clear:both"></div>

	<?
}else{ //xls ouput
	?>
	<script language="JavaScript">
	audittype1= '<?=$_POST['audittype1']?>';
	audittype2= '<?=$_POST['audittype2']?>';
	datefilter= '<?=$_POST['datefilter']?>';
	daterange= '<?=$_POST['daterange']?>';
	region= '<?=$_POST['region']?>';
	orderby='<?=$_POST['orderby']?>';
	order='<?=$_POST['order']?>';
	window.open('scripts/audits/audit_data_xls.php?xlsprint=yes&audittype1='+audittype1+'&audittype2='+audittype2+'&datefilter='+datefilter+'&orderby='+orderby+'&order='+order+'&daterange='+daterange+'&region='+region);
	</script>
	<?
}
?>
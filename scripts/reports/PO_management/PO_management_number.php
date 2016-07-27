<?PHP
require_once("/var/www/html/include/config.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
//error_reporting(E_ALL);

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

include("PR_total.php");
include("PR_material.php");
include("PR_txmn.php");
include("PR_services.php");

$title = new title('Total # POs per month');
$title->set_style( "{font-size: 18px; font-family: Times New Roman; font-weight: bold; color: #A2ACBA; text-align: center;}" );

$months=array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

$bar1 = new bar_3d();
$bar1->colour( '#3399FF');
$bar1->set_tooltip( 'Services:<br>#val#' );
$bar1->key('Totals', 12);
$bar1->set_values( array(intval($PO_JAN_tot),
						 intval($PO_FEB_tot),
						 intval($PO_MAR_tot),
						 intval($PO_APR_tot),
						 intval($PO_MAY_tot),
						 intval($PO_JUN_tot),
						 intval($PO_JUL_tot),
						 intval($PO_AUG_tot),
						 intval($PO_SEP_tot),
						 intval($PO_OCT_tot),
						 intval($PO_NOV_tot),
						 intval($PO_DEC_tot),
					));

//$bar1->set_tooltip( '#val# of #total#<br>#percent# of 100%' );
$total_mat=$PO_JAN_mat+$PO_FEB_mat+$PO_MAR_mat+$PO_APR_mat+$PO_MAY_mat+$PO_JUN_mat+$PO_JUL_mat+$PO_AUG_mat+$PO_SEP_mat+$PO_OCT_mat+$PO_NOV_mat+$PO_DEC_mat;
$bar2 = new bar_3d();
$bar2->colour( '#FFD300');
$bar2->set_tooltip( 'Material:<br>#val#' );
$bar2->key('Material: '.$total_mat, 12);
$bar2->set_values( array(intval($PO_JAN_mat),
						 intval($PO_FEB_mat),
						 intval($PO_MAR_mat),
						 intval($PO_APR_mat),
						 intval($PO_MAY_mat),
						 intval($PO_JUN_mat),
						 intval($PO_JUL_mat),
						 intval($PO_AUG_mat),
						 intval($PO_SEP_mat),
						 intval($PO_OCT_mat),
						 intval($PO_NOV_mat),
						 intval($PO_DEC_mat),
					));

$total_txmn=$PO_JAN_txmn+$PO_FEB_txmn+$PO_MAR_txmn+$PO_APR_txmn+$PO_MAY_txmn+$PO_JUN_txmn+$PO_JUL_txmn+$PO_AUG_txmn+$PO_SEP_txmn+$PO_OCT_txmn+$PO_NOV_txmn+$PO_DEC_txmn;
$bar3 = new bar_3d();
$bar3->colour( '#990099');
$bar3->set_tooltip( 'TXMN equipment:<br>#val#' );
$bar3->key('Transmission Equipment: '.$total_txmn, 12);
$bar3->set_values( array(intval($PO_JAN_txmn),
						 intval($PO_FEB_txmn),
						 intval($PO_MAR_txmn),
						 intval($PO_APR_txmn),
						 intval($PO_MAY_txmn),
						 intval($PO_JUN_txmn),
						 intval($PO_JUL_txmn),
						 intval($PO_AUG_txmn),
						 intval($PO_SEP_txmn),
						 intval($PO_OCT_txmn),
						 intval($PO_NOV_txmn),
						 intval($PO_DEC_txmn),
					));

$total_serv=$PO_JAN_serv+$PO_FEB_serv+$PO_MAR_serv+$PO_APR_serv+$PO_MAY_serv+$PO_JUN_serv+$PO_JUL_serv+$PO_AUG_serv+$PO_SEP_serv+$PO_OCT_serv+$PO_NOV_serv+$PO_DEC_serv;
$bar4 = new bar_3d();
$bar4->colour( '#99CC00');
$bar4->set_tooltip( 'Services:<br>#val#' );
$bar4->key('Services: '.$total_serv, 12);
$bar4->set_values( array(intval($PO_JAN_serv),
						 intval($PO_FEB_serv),
						 intval($PO_MAR_serv),
						 intval($PO_APR_serv),
						 intval($PO_MAY_serv),
						 intval($PO_JUN_serv),
						 intval($PO_JUL_serv),
						 intval($PO_AUG_serv),
						 intval($PO_SEP_serv),
						 intval($PO_OCT_serv),
						 intval($PO_NOV_serv),
						 intval($PO_DEC_serv),
					));

						 					
$x_labels = new x_axis_labels();
$x_labels->set_vertical();
$x_labels->set_colour( '#A2A91A' );
$x_labels->set_labels( $months );


$x = new x_axis();
$x->set_colour( '#A2A91A' );
$x->set_grid_colour( '#D7E4A3' );
// Add the X Axis Labels to the X Axis
$x->set_labels( $x_labels );

$y = new y_axis();
$y->set_range( 0, 500, 50 );


$chart = new open_flash_chart();
$chart->set_bg_colour( '#FFFFFF' );
$chart->set_title( $title );
$chart->add_element( $bar1 );
$chart->add_element( $bar2 );
$chart->add_element( $bar4 );
$chart->add_element( $bar3 );


$chart->set_x_axis( $x );
$chart->add_y_axis( $y );

$data_scope= $chart->toPrettyString();

?>
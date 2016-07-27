<?PHP
require_once("/var/www/html/include/config.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
//error_reporting(E_ALL);

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

include("Euro_services_ericsson.php");
include("Euro_services_mobistar.php");
include("Euro_services_nuon.php");
include("Euro_services_simac.php");


$title = new title('Total Euro Services per month');
$title->set_style( "{font-size: 18px; font-family: Times New Roman; font-weight: bold; color: #A2ACBA; text-align: center;}" );

$months=array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

//$bar1->set_tooltip( '#val# of #total#<br>#percent# of 100%' );
$total_serv_ericsson=$PO_JAN_serv_ericsson+$PO_FEB_serv_ericsson+$PO_MAR_serv_ericsson+$PO_APR_serv_ericsson+$PO_MAY_serv_ericsson+$PO_JUN_serv_ericsson+$PO_JUL_serv_ericsson+$PO_AUG_serv_ericsson+$PO_SEP_serv_ericsson+$PO_OCT_serv_ericsson+$PO_NOV_serv_ericsson+$PO_DEC_serv_ericsson;
$bar2 = new bar_3d();
$bar2->colour( '#FFD300');
$bar2->key('Ericsson: '.$total_serv_ericsson, 12);
$bar2->set_values( array(intval($PO_JAN_serv_ericsson),
						 intval($PO_FEB_serv_ericsson),
						 intval($PO_MAR_serv_ericsson),
						 intval($PO_APR_serv_ericsson),
						 intval($PO_MAY_serv_ericsson),
						 intval($PO_JUN_serv_ericsson),
						 intval($PO_JUL_serv_ericsson),
						 intval($PO_AUG_serv_ericsson),
						 intval($PO_SEP_serv_ericsson),
						 intval($PO_OCT_serv_ericsson),
						 intval($PO_NOV_serv_ericsson),
						 intval($PO_DEC_serv_ericsson),
					));

$total_serv_mobistar=$PO_JAN_serv_mobistar+$PO_FEB_serv_mobistar+$PO_MAR_serv_mobistar+$PO_APR_serv_mobistar+$PO_MAY_serv_mobistar+$PO_JUN_serv_mobistar+$PO_JUL_serv_mobistar+$PO_AUG_serv_mobistar+$PO_SEP_serv_mobistar+$PO_OCT_serv_mobistar+$PO_NOV_serv_mobistar+$PO_DEC_serv_mobistar;
$bar3 = new bar_3d();
$bar3->colour( '#990099');
$bar3->key('Mobistar: '.$total_serv_mobistar, 12);
$bar3->set_values( array(intval($PO_JAN_serv_mobistar),
						 intval($PO_FEB_serv_mobistar),
						 intval($PO_MAR_serv_mobistar),
						 intval($PO_APR_serv_mobistar),
						 intval($PO_MAY_serv_mobistar),
						 intval($PO_JUN_serv_mobistar),
						 intval($PO_JUL_serv_mobistar),
						 intval($PO_AUG_serv_mobistar),
						 intval($PO_SEP_serv_mobistar),
						 intval($PO_OCT_serv_mobistar),
						 intval($PO_NOV_serv_mobistar),
						 intval($PO_DEC_serv_mobistar),
					));

$total_serv_simac=$PO_JAN_serv_simac+$PO_FEB_serv_simac+$PO_MAR_serv_simac+$PO_APR_serv_simac+$PO_MAY_serv_simac+$PO_JUN_serv_simac+$PO_JUL_serv_simac+$PO_AUG_serv_simac+$PO_SEP_serv_simac+$PO_OCT_serv_simac+$PO_NOV_serv_simac+$PO_DEC_serv_simac;
$bar4 = new bar_3d();
$bar4->colour( '#99CC00');
$bar4->key('Simac: '.$total_serv_simac, 12);
$bar4->set_values( array(intval($PO_JAN_serv_simac),
						 intval($PO_FEB_serv_simac),
						 intval($PO_MAR_serv_simac),
						 intval($PO_APR_serv_simac),
						 intval($PO_MAY_serv_simac),
						 intval($PO_JUN_serv_simac),
						 intval($PO_JUL_serv_simac),
						 intval($PO_AUG_serv_simac),
						 intval($PO_SEP_serv_simac),
						 intval($PO_OCT_serv_simac),
						 intval($PO_NOV_serv_simac),
						 intval($PO_DEC_serv_simac),
					));
					
$total_serv_nuon=$PO_JAN_serv_nuon+$PO_FEB_serv_nuon+$PO_MAR_serv_nuon+$PO_APR_serv_nuon+$PO_MAY_serv_nuon+$PO_JUN_serv_nuon+$PO_JUL_serv_nuon+$PO_AUG_serv_nuon+$PO_SEP_serv_nuon+$PO_OCT_serv_nuon+$PO_NOV_serv_nuon+$PO_DEC_serv_nuon;
$bar5 = new bar_3d();
$bar5->colour( '#FF7400');
$bar5->key('Nuon: '.$total_serv_nuon, 12);
$bar5->set_values( array(intval($PO_JAN_serv_nuon),
						 intval($PO_FEB_serv_nuon),
						 intval($PO_MAR_serv_nuon),
						 intval($PO_APR_serv_nuon),
						 intval($PO_MAY_serv_nuon),
						 intval($PO_JUN_serv_nuon),
						 intval($PO_JUL_serv_nuon),
						 intval($PO_AUG_serv_nuon),
						 intval($PO_SEP_serv_nuon),
						 intval($PO_OCT_serv_nuon),
						 intval($PO_NOV_serv_nuon),
						 intval($PO_DEC_serv_nuon),
					));


						 					
$x_labels = new x_axis_labels();
$x_labels->set_steps( 2 );
$x_labels->set_vertical();
$x_labels->set_colour( '#A2A91A' );
$x_labels->set_labels( $months );


$x = new x_axis();
$x->set_colour( '#A2A91A' );
$x->set_grid_colour( '#D7E4A3' );
// Add the X Axis Labels to the X Axis
$x->set_labels( $x_labels );

$y = new y_axis();
$y->set_range( 0,2500000, 100000);


$chart = new open_flash_chart();
$chart->set_bg_colour( '#FFFFFF' );
$chart->set_title( $title );
$chart->add_element( $bar2 );
$chart->add_element( $bar3 );
$chart->add_element( $bar4 );
$chart->add_element( $bar5 );

$chart->set_x_axis( $x );
$chart->add_y_axis( $y );

$data_euro_services= $chart->toPrettyString();

?>
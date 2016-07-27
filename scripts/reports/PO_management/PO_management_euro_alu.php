<?PHP
require_once("/var/www/html/include/config.php");
require_once($config['sitepath_abs']."/include/PHP/oci8_funcs.php");
//error_reporting(E_ALL);

$conn_Infobase = oci_connect($user_Infobase,$passwd_Infobase, $sid_Infobase);
$stmt = OCIParse($conn_Infobase,"ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'");
OCIExecute($stmt,OCI_DEFAULT);

include("Euro_services_alu.php");

$title = new title('Total Euro SERVICES per month for ALU');
$title->set_style( "{font-size: 18px; font-family: Times New Roman; font-weight: bold; color: #A2ACBA; text-align: center;}" );

$months=array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");


$total_serv=$PO_JAN_serv+$PO_FEB_serv+$PO_MAR_serv+$PO_APR_serv+$PO_MAY_serv+$PO_JUN_serv+$PO_JUL_serv+$PO_AUG_serv+$PO_SEP_serv+$PO_OCT_serv+$PO_NOV_serv+$PO_DEC_serv;
$bar4 = new bar_glass();
$bar4->colour( '#99CC00');
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
$y->set_range( 0,10000000, 1000000);


$chart = new open_flash_chart();
$chart->set_bg_colour( '#FFFFFF' );
$chart->set_title( $title );
$chart->add_element( $bar4 );



$chart->set_x_axis( $x );
$chart->add_y_axis( $y );

$data_euro_alu= $chart->toPrettyString();

?>
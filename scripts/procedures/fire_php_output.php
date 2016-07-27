<?
$firephp->group('Parameters');
$table   = array();
$table[] = array('Parameter','Value');
$table[] = array('BSDSKEY',$_SESSION['BSDSKEY']);
$table[] = array('SiteID',$_SESSION['SiteID']);
$table[] = array('VIEWTYPE (tableview)',$_SESSION['table_view']);
$table[] = array('BSDS_BOB_REFRESH',$_SESSION['BSDS_BOB_REFRESH']); 
$table[] = array('Sitekey',$_SESSION["Sitekey"]); 
$table[] = array('Sitekey cookie',$_COOKIE["Sitekey"]);

$firephp->table('General info', $table);
$firephp->groupEnd();
$firephp->group('Output NET1');
$firephp->dump('BSDS_funded', $BSDS_funded,true);
$firephp->groupEnd();




$firephp->group('Queries current_planned',
                array('Collapsed' => true,
                      'Color' => '#FF0000'));
$firephp->log($_SESSION['query']);
$firephp->groupEnd();
 
$_SESSION['query']="";
?>
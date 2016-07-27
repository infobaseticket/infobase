<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/bsds/config.php');
if (!$_GET['module']){
require_once($config['phpguarddog_path']."/guard.php");
protect("","Base_RF,Base_TXMN,Base_delivery,Base_other,Base_risk,Partner,Administrators","");
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <title>Infobase</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Infobase developed for BASECOMPANY">
    <meta name="author" content="Frederick Eyland">

    <link rel="stylesheet" href="<?=$config['explorer_url']?>bootstrap/css/bootstrap.min.css" media="all">
    <link rel="stylesheet" href="<?=$config['explorer_url']?>bootstrap/css/bootstrap-responsive.min.css" media="all">
    <link rel="shortcut icon" href="bootstrap/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=$config['explorer_url']?>bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?=$config['explorer_url']?>bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?=$config['explorer_url']?>bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?=$config['explorer_url']?>bootstrap/ico/apple-touch-icon-57-precomposed.png">
   
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>bootstrap/css/datepicker.css">
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>bootstrap/css/bootstrap-editable.css">
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>css/messenger.css">
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>css/messenger-theme-future.css">
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>bootstrap/css/bootstrap-editable.css">
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>bootstrap/css/selectize.bootstrap3.css">
    
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>bootstrap/css/select2.css">
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>bootstrap/css/select2-bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>css/style2.css" media="all">

    <script src="<?=$config['explorer_url']?>javascripts/jquery.js"></script>
    <script src="<?=$config['explorer_url']?>javascripts/infobase.js"></script>
    <script src="<?=$config['explorer_url']?>javascripts/jquery.form.js"></script>
    <script src="<?=$config['explorer_url']?>javascripts/jquery.PrintArea.js"></script>
  </head>
  <body>
  <nav class="navbar navbar-fixed-top navbar-inverse" role="navigation" id="navbar">
      <div class="navbar navbar-default navbar-fixed-top" role="navigation">

          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="http://<?=$_SERVER['SERVER_NAME']?>"><img src="<?=$config['explorer_url']?>images/logoInfobase.png" width="100px" height="20px"></a>
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li class="mainnav active" id="explorer"><a href="#">Explorer</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Document manager <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li class="mainnav" id="filebrowser"><a href="#">Search for documents</a></li>
                  <li><a class="liveran" href="#" data-ranurl='<?=$config['sitepath_url']?>/bsds/scripts/liveranbrowser/liveranbrowser.php'>Live RAN browser</a></li>
                </ul>
              </li>
             
              <?php if (substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'Administrators')==1 or substr_count($guard_groups, 'Benchmark')==1){ ?>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administrative <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <?php if (substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'Administrators')==1 or substr_count($guard_groups, 'Benchmark')==1){ ?>
                  <li class="mainnav" id="net1updater"><a href="#">NET1 updater</a></li>
                  <?php } ?>
                  <?php if (substr_count($guard_groups, 'Base')==1 or substr_count($guard_groups, 'Administrators')==1){ ?>
                  <!--<li class="mainnav" id="rafcreator"><a href="#">RAF creator</a></li>-->
                  <li class="mainnav" id="pouploader"><a href="#">PO uploader</a></li>
                  <?php } ?>
                </ul>
              </li>
              <?php } ?>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reporting <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li class="mainnav" id="masterreport"><a href="#">Master report</a></li>
                  <li class="divider"></li>
                  <li class="mainnav" id="rafreport" data-reporttype="table"><a href="#">RAF actions</a></li>
                  <li class="mainnav" id="losreport"><a href="#">LOS actions</a></li>
                  <li class="divider"></li>
                  <li class="mainnav" id="ossreport"><a href="#">OSS site count</a></li>
                  <li class="mainnav" id="rafreport" data-reporttype="graph"><a href="#">RAF actions graphs</a></li>
                  <li class="mainnav" id="sitesreport" data-reporttype="graph"><a href="#">Sites in progress</a></li>
                  <li class="mainnav" id="sitelistreport"><a href="#">Total network sitelist</a></li>
                  <li class="divider"></li>
                  <li class="mainnav" id="net1users"><a href="#">NET1 userlist</a></li>
                  <li class="mainnav" id="net1tasks"><a href="#">NET1 tasklists</a></li>
                </ul>
              </li>
              <?php if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
              <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Rollout dashbord <b class="caret"></b></a>
	              <ul class="dropdown-menu">
	              	<li class="mainnav" id="roldashbord" data-reporttype="roldash"><a href="#">DASHBORD N1</a></li>
	              	<li class="mainnav" id="roldashbord" data-reporttype="debtechnos"><a href="#">LIVE BTS SITES N1</a></li>
	              	<li class="mainnav" id="roldashbord" data-reporttype="acqdash"><a href="#">ACQ DASHBOARD N1</a></li>
	              </ul>
	          </li>
              <?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><div id="spinner" class="spinner"></div></li>
              <li><a href='PHPlibs/phpguarddog/guard_logout.php'>Logout?</a></li>
            </ul>
          </div><!--/.nav-collapse -->
      </div>
  </nav>
  


  <section> 
    <!--[if IE]>
    <div class="alert alert-danger">This version of Infobase only works correctly with Firefox or Chrome. Please Install Chrome via 'Install My Software'- icon on your desktop</div>
    <![endif]-->
    <div class="container-liquid" id="maincontent">
    </div>
  </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" id="myModalDialog">
        <div class="modal-content">
          <div class="modal-header">
            <span id="modalspinner" class="spinner"></span>
            <button type="button" class="close modaldismiss" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 id="modalheader"></h4>
          </div>
          <div class="modal-body">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default modaldismiss" >Close</button>
            <button type="button" class="btn btn-primary" id="savemodal" data-module="ND">Save changes</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script src="<?=$config['explorer_url']?>bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=$config['explorer_url']?>bootstrap/js/bootstrap-datepicker.js"></script>
    <script src="<?=$config['explorer_url']?>bootstrap/js/bootpag.js"></script>
    <script src="<?=$config['explorer_url']?>bootstrap/js/bootstrap-editable.min.js"></script>
    <script src="<?=$config['explorer_url']?>bootstrap/js/bootstrap-notify.js"></script>
    <script src="<?=$config['explorer_url']?>javascripts/spin.js"></script>
    <script src="<?=$config['explorer_url']?>javascripts/messenger.min.js"></script>
    <script src="<?=$config['explorer_url']?>bootstrap/js/selectize.min.js"></script>
    <script src="<?=$config['explorer_url']?>bootstrap/js/select2.js"></script>
  </body>
</html>

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

   
    <link rel="shortcut icon" href="bootstrap/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=$config['explorer_url']?>bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?=$config['explorer_url']?>bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?=$config['explorer_url']?>bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?=$config['explorer_url']?>bootstrap/ico/apple-touch-icon-57-precomposed.png">
    <link rel="stylesheet" type="text/css" href="<?=$config['explorer_url']?>css/allinone.css" media="all">

    <script src="<?=$config['explorer_url']?>javascripts/allinone.js"></script>
    <script src="<?=$config['explorer_url']?>javascripts/infobase.js"></script>

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
            <ul class="nav navbar-nav" id="mainnavbar">
              <li class="mainnav active" id="explorer"><a href="<?=$config['sitepath_url']?>/bsds/index.php">Explorer</a></li>
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
                  <?php if (substr_count($guard_groups, 'Base_delivery')==1 or substr_count($guard_groups, 'Administrators')==1){ ?>
                  <!--<li class="mainnav" id="rafcreator"><a href="#">RAF creator</a></li>-->
                  <li class="mainnav" id="rafcomments"><a href="#">RAF comments uploader</a></li>
                  <?php } ?>
                  <?php if (substr_count($guard_groups, 'Base_delivery')==1 or substr_count($guard_groups, 'Administrators')==1){ ?>
                  <!--<li class="mainnav" id="rafcreator"><a href="#">RAF creator</a></li>-->
                  <li class="mainnav" id="cofuploader"><a href="#">COF uploader</a></li>
                  <?php } ?>

                  <?php if (substr_count($guard_groups, 'Administrators')==1){ ?>
                  <li class="mainnav" id="cofgenerator"><a href="#">COF GENERATOR</a></li>
                  <?php } ?>
                </ul>
              </li>
              <?php } ?>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reporting <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <!--<li class="mainnav" id="masterreport"><a href="#">Master report</a></li>
                  <li class="divider"></li>-->
                  <li class="mainnav" id="rafreport" data-reporttype="table"><a href="#">RAF actions</a></li>
                  <li class="mainnav" id="losreport"><a href="#">LOS actions</a></li>
                  <li class="divider"></li>
                  <li class="mainnav" id="ossreport"><a href="#">OSS site count</a></li>
                  <li class="mainnav" id="sitesreport" data-reporttype="graph"><a href="#">Sites in progress N1</a></li>
                  <li class="mainnav" id="sitelistreport"><a href="#">Sites debarred N1</a></li>            
                </ul>
              </li>
              <?php if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
              <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Rollout<b class="caret"></b></a>
	              <ul class="dropdown-menu">
                  <li class="mainnav" id="roldashbord" data-reporttype="sitesconstruction"><a href="#">SITES IN CONSTRUCTION</a></li>
                  <li class="mainnav" id="roldashbord" data-reporttype="dataissues"><a href="#">DATA ISSUES</a></li>
	              	<li class="mainnav" id="roldashbord" data-reporttype="roldash"><a href="#">DASHBOARD N1</a></li>
	              	<li class="mainnav" id="roldashbord" data-reporttype="debtechnos"><a href="#">LIVE BTS SITES N1</a></li>
                  <li class="mainnav" id="roldashbord" data-reporttype="dismrepl"><a href="#">DISMANTLING & REPLACEMENTS</a></li>
                  <?php if (substr_count($guard_groups, 'Base_delivery')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
                  <li class="mainnav" id="roldashbord" data-reporttype="boqval"><a href="#">BOQ VALIDATION</a></li>
                  <?php } ?>
	              </ul>
	          </li>
              <?php } ?>
            <?php if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Radio <b class="caret"></b></a>
                <ul class="dropdown-menu">
                 <li class="mainnav" id="radiodashbord" data-reporttype="radiodash"><a href="#">Debarring against Asset</a></li>
                </ul>
            </li>
            <?php } ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Logistics <b class="caret"></b></a>
                <ul class="dropdown-menu">
                 <li class="mainnav" id="inventory"><a href="#">Check Inventory of today</a></li>
                 <li class="mainnav" id="reptechmlogi"><a href="#">SN to be approved by TECHM Logistics</a></li>
                </ul>
            </li>
            <?php if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Operations <b class="caret"></b></a>
                <ul class="dropdown-menu">
                 <li class="mainnav" id="operations"><a href="#">Debarred sites in different month</a></li>
                </ul>
            </li>
            <?php } ?>
            </ul>
            <?php $user_BY=getuserdata($guard_username); ?>
            <ul class="nav navbar-nav navbar-right">
              <li><div id="spinner" class="spinner"></div></li>
              <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><span class="glyphicon glyphicon-user"></span><b class="caret"></b></a>
                    <ul class="dropdown-menu message-dropdown">
                        <li class="message-preview">
                            <table class="table table-condensed"  style="font-size:10px;">
                            <tr><th>name:</th><td><?=$user_BY['fullname']?></td></tr>
                            <tr><th>username:</th><td><?=$guard_username?></td></tr>
                            <tr><th>email:</th><td><?=$user_BY['email']?></td></tr>
                            <tr><th>mobile:</th><td><?=$user_BY['mobile']?></td></tr>
                            <tr><th>groups:</th><td><?php echo str_replace(',', '<br>',$guard_groups)?></td></tr>
                            </table>
                        </li>
                        <li><a href='PHPlibs/phpguarddog/guard_logout.php'>Logout?</a></li>
                    </ul>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
      </div>
  </nav>
  
  <section> 
    <div class="container-liquid">
      <ul class="nav nav-tabs" id="MainsiteTabs" role="tablist">
        <li class="active" id="tab_SuperContent"><a href="#SuperContent" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-fire"></span> MAIN </a></li>
        <li id="tab_ABdashbord"><a href="#ABdashbord" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-heart"></span> AB DASHBOARD </a></li>
        <?php if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
        <li id="tab_MODdashbord"><a href="#MODdashbord" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-plane"></span> MODERNISATION DASHBOARD </a></li>
        <?php } ?>
      </ul>
      <div class="tab-content" role="tabpanel" id="SuperContentTabs">
        <div class="tab-pane active" id="SuperContent"> 
          <br><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading interface...
        </div>
        <div class="tab-pane" role="tabpanel" id="ABdashbord">
          <br><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading AB dashboard...
        </div>
        <?php 
        if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
        <div class="tab-pane" role="tabpanel" id="MODdashbord">
          <br><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading MODERNISATION dashboard...
        </div>
        <?php } ?>
      </div>
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
 
  <script src="<?=$config['explorer_url']?>javascripts/allinone_footer.js"></script>
  <script src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/fusioncharts.js"></script>
  <script src="<?=$config['explorer_url']?>javascripts/fusioncharts/js/themes/fusioncharts.theme.fint.js"></script>

    <?php if (substr_count($guard_groups, 'Base')==1 || substr_count($guard_groups, 'Administrators')==1){ ?>
    <script type="text/javascript">
    $.ajax({
          type: "POST",
          url: "scripts/explorer/MODdashboard.php",
          success : function(data){
              $('#MODdashbord').html(data); 
          }
    });

    </script>
    <?php } ?>
    
  </body>
</html>

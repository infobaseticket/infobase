// There is a lot of field experience data, so this will always make the table responsive.
function forceResponsiveTables(tableid) {
 	var windowSize = $(window).width();
    var thisTable = $('#'+tableid);
    var tableSize = thisTable.width();
    //alert(tableid+' '+tableSize+'/'+windowSize);
    var parent = thisTable.parent('.table-responsive');
    // 768px is the default for bootstrap 3's responsive-table, modify if needed
    if (windowSize <= 768) {
        parent.css('width', '').css('overflow-x', '').css('overflow-y', '').css('margin-bottom', '').css('border', '');
    } else {
        
          // Change the border color based on the bootstrap theme colors
          parent.css('width', '100%').css('overflow-x', 'scroll').css('overflow-y', 'hidden').css('margin-bottom', '15px').css('border', '1px solid #DDDDDD');
    }
}

$(document).ready( function(){
/*
	var url=window.location.pathname;
	alert(url);
	var url=window.location.href ;
	alert(url);
*/
	//bootstrap modal / select2 bug
	$.fn.modal.Constructor.prototype.enforceFocus = function() {};

	$('#myModal').on('hidden.bs.modal', function () {
	    // do something…
	})

	$('body').append('<div class="btn btn-default" id="toTop">^ Back to Top</div>');
	$(window).scroll(function () {
		if ($(this).scrollTop() != 0) {
			$('#toTop').fadeIn();
		} else {
			$('#toTop').fadeOut();
		}
	}); 
	$('#toTop').click(function(){
		$("html, body").animate({ scrollTop: 0 }, 600);
		return false;
	});  

	Messenger.options = {
	    extraClasses: 'messenger-fixed messenger-on-bottom',
	    theme: 'future'
	}
 	//Messenger().post({message:"Welcome back!",showCloseButton:true,hideAfter: 5,hideOnNavigate: true});

 	$("body").on("click","[data-toggle=offcanvas]",function( e ){
    	$('.row-offcanvas').toggleClass('active');
  	});

  	$('body').on('mouseenter', '[rel=tooltip]', function(){
	  var el = $(this);
	  if (el.data('tooltip') === undefined) {
	    el.tooltip({
	      placement: el.data("placement") || "top",
	      container: el.data("container") || false
	    });
	  }
	  el.tooltip('show');
	});

	$('body').on('mouseleave', '[rel=tooltip]', function(){
	  $(this).tooltip('hide');
	});

	var query = getQueryParams(document.location.search);
	if (query.module){
		
		if(query.module=='RAF' && query.rafid){
			var load='scripts/explorer/explorer.php?module='+query.module+'&rafid='+query.rafid;
		}else if(query.module=='ASSET' && query.site && query.techno){
			var load='scripts/explorer/explorer.php?module='+query.module+'&site='+query.site+'&techno='+query.techno;
		}else if(query.module && query.site){
			var load='scripts/explorer/explorer.php?module='+query.module+'&site='+query.site;
		}else if(query.module=='LOS' && query.losid){
			var load='scripts/explorer/explorer.php?module='+query.module+'&losid='+query.losid;
		}else if(query.module=='BSDS' && query.bsdsid){
			var load='scripts/explorer/explorer.php?module='+query.module+'&bsdsid='+query.bsdsid;
		}else if(query.module=='rafreport' && query.report){
			var load='scripts/rafreport/rafreporttable.php?module='+query.module+'&report='+query.report;	
		}else if (query.module){
			Messenger().post({
				message: '1) Sorry, but you did not provide the correct parameters in the url',
				showCloseButton: true
			});
		}
	}else{
		var load='scripts/explorer/explorer.php';
	}
	if (load!=''){
		$('#maincontent').load(load, function(){
			$('#spinner').spin(false);

			var options = { 
				target:  '#search_output',   
				success:    
					function() { 

						$("#search_output").show('slow');

						if (query.module=='RAF' && query.rafid){
							$("#rafID"+query.rafid).click();
						}else if (query.module=='RAF' && query.site){
							$("#raficon"+(query.site﻿).toUpperCase()).click();
						}else if (query.module=='OSS' && query.site){
							$("#ossicon"+(query.site﻿).toUpperCase()).click();
						}else if (query.module=='ASSET' && query.site && query.techno){
							$("#asseticon"+(query.site﻿).toUpperCase()).click();
							if (query.techno=='G18' || query.techno=='G9'){
								if($("#assetGSM"+(query.site﻿)).length){
									$("#assetGSM"+(query.site﻿).toUpperCase()).click();
								}else{
									Messenger().post({
									  message: 'Oeps! '+query.techno+' does not exists in Asset',
									  showCloseButton: true,
									  type: 'error'
									});
								}
							}else{
								if($("#asset"+(query.techno).toUpperCase()+(query.site﻿).toUpperCase()).length) {
									$("#asset"+(query.techno).toUpperCase()+(query.site﻿).toUpperCase()).click();
								}else{
									Messenger().post({
									  message: 'Oeps! '+query.techno+' does not exists in Asset',
									  showCloseButton: true,
									  type: 'error'
									});
								}
								
							}
						}else if (query.module=='NET1' && query.site){
							$("#net1icon"+(query.site﻿).toUpperCase()).click();
						}else if (query.module=='TRACK' && query.site){
							$("#trackicon"+(query.site﻿).toUpperCase()).click();
						}else if (query.module=='BSDS' && query.bsdsid){
							$("li[data-module='bsds']").click();
						}else if (query.module=='BSDS' && query.site){
							$("#bsdsicon"+(query.site).toUpperCase()).click();
						}else if (query.module=='LOS' && query.losid){
							$("#losID"+query.losid).click();
						}else if (query.module=='LOS' && query.site){
							$("#losicon").click();
						}else if (query.module){
							Messenger().post({
							  message: '2) Sorry, but you did not provide the correct parameters in the url',
							  showCloseButton: true,
							  type: 'error'
							});
						}
					}
			};		
			if (query.module && query.module!='rafreport'){
				$("#searchForm").ajaxSubmit(options); 
			}else if (query.module && query.module=='rafreport'){
					if (query.report=='RFBCS'){
						var report='Base RF - BCS';
					}else if(query.report=='BCSRF'){
						var report='Base RF - BCS';
					}else if(query.report=='RFINP'){
						var report='Base RF - INPUT';
					}else if(query.report=='RFFUND'){
						var report='Base RF - FUNDING';
					}else if(query.report=='RFPAC'){
						var report='Base RF - PAC';
					}else if(query.report=='TXINP'){
						var report='Base TXMN - INPUT';
					}else if(query.report=='TXINP'){
						var report='Base TXMN - INPUT';
					}else if(query.report=='BCSTX'){
						var report='Base TXMN - BCS';
					}else if(query.report=='TXACQ'){
						var report='Base TXMN - ACQUISITION APPROVAL';
					}else if(query.report=='TXACQCON'){
						var report='Base TXMN - ACQUISITION APPROVAL CONDITIONAL';
					}else if(query.report=='DELNET1'){
						var report='Base Delivery - NET1 LINK';
					}else if(query.report=='DELACQ'){
						var report='Base Delivery - RAF ACQUIRED';
					}else if(query.report=='DELPAC'){
						var report='Base Delivery - PAC DATE';
					}else if(query.report=='DELFUND'){
						var report='Base Delivery - FUND DATE';
					}else if(query.report=='DELLOCK'){
						var report='Base Delivery - Locked RAF';
					}else if(query.report=='DELPOA'){
						var report='Base Delivery - MISSING PO ACQ';
					}else if(query.report=='DELPOC'){
						var report='Base Delivery - MISSING PO CON';
					}
					
					$('#Actionrequiredby').val(report);
					if (query.region){
						$('#Region').val(query.region);
					}
					$("#displayRafform").click();
			}
		});
		$('#searchk').focus();
	}
	/*
	* SEARCH FORM
	*/

	
	$("body").on("click",".mainnav",function( e ){
		$('#spinner').spin('large');	
		$('#navbar li').removeClass("active");
		$(this).addClass("active");
		var module=$(this).attr('id');
		if (module=='rafreport'){
			var reporttype=$(this).data('reporttype');
			var url = 'scripts/'+module+'/'+module+reporttype+'.php';
		}else if (module=='roldashbord'){
			var reporttype=$(this).data('reporttype');
			var url = 'scripts/roldashbord/'+reporttype+'.php';
		}else{
			var url = 'scripts/'+module+'/'+module+'.php';
		}
	
		$('#maincontent').load(url, function(){
			if (module=='explorer')
			{		
				$('#spinner').spin(false);
				$('#searchk').focus();
				var options = { 
				  target:  '#search_output',   
				  success:    function() { 				  	
						$("#search_output").show('fast');
					}  
				};	
				$('#searchForm').ajaxForm(options);	
				return false; 
			}else if(module=='filebrowser'){
				$('.input-daterange').datepicker({
				    todayBtn: true,
				    multidate: true,
				    autoclose: true,
				    format: "dd-mm-yyyy"
				});
			}
			$('#spinner').spin(false);
		});
	});

	$("body").on("submit","#searchForm",function( e ){
		$("#siteTabs li").remove();
		$("#contentTabs div").remove();
		$("#search_output").hide('fast');	
	});
	$("body").on("click","#searchbutton",function( e ){
		$("#siteTabs li").remove();
		$("#contentTabs div").remove();
		$("#search_output").hide('fast');
		var options = { 
		  target:  '#search_output',   
		  success:    function() { 
			$("#search_output").show('slow');
			
		  }  
		};			
	  	$("#searchForm").ajaxSubmit(options);  	
	   	return false; 
	});

	/*
	* EXPLORER TO RAFREPORT
	*/
	$("body").on("click",".mainnav",function( e ){
		var url=$(this).data('url');
		///alert(url);
	});
	/*
	* ICONNAV ACTIONS
	*/
	$("body").on("click",".navicon",function( e ){
		var techno='';
		if ($(this).hasClass('bsds')==true){
			var viewtype='BSDS';
			var url = 'scripts/general_info/general_info.php';
			var iconImg='glyphicon-book';
		}else if ($(this).hasClass('bsds2')==true){
			var viewtype='BSDS2';
			var url = 'scripts/general_info2/general_info.php';
			var iconImg='glyphicon-book';
		}else if ($(this).hasClass('oss')==true){
			var viewtype='OSS';
			var url = 'scripts/explorer/site_explorer/site_explorer.php';
			var iconImg='glyphicon-tint';
		}else if ($(this).hasClass('ran')==true){
			var viewtype='RAN';
			var url = 'scripts/filebrowser/filebrowser.php';
			var iconImg='glyphicon-folder-open';
		}else if ($(this).hasClass('asset')==true){
			var candidate = $(this).data('candidate');
			$('#asset'+candidate).slideToggle('fast');
			var url='';
		}else if ($(this).hasClass('assettechno')==true){			
			var url = 'scripts/explorer/asset_explorer/asset_explorer.php';
			var iconImg='glyphicon-globe';
			var techno = $(this).data('techno');
			var viewtype='ASSET';
		}else if ($(this).hasClass('emission')==true){
			var viewtype='EMISSION';
			var url = 'scripts/emission/emission.php';
			var iconImg='glyphicon-map-marker';
		}else if ($(this).hasClass('net1')==true){
			var viewtype='NET1';
			var url = 'scripts/net1/net1.php';
			var iconImg='glyphicon-th-large';
		}else if ($(this).hasClass('msexpl')==true){
			var viewtype='NET1';
			var url = 'scripts/net1/MSexplorer.php';
			var iconImg='glyphicon-th-large';
		}else if ($(this).hasClass('raf')==true || $(this).hasClass('rafID')==true){
			var viewtype='RAF';
			var url = 'scripts/raf/raf.php';
			var iconImg='glyphicon-road';
		}else if ($(this).hasClass('los')==true || $(this).hasClass('losID')==true){
			var viewtype='LOS';
			var url = 'scripts/los/los.php';
			var iconImg='glyphicon-screenshot';
		}else if ($(this).hasClass('audit')==true){
			var viewtype='AUDIT';
			var url = 'scripts/audits/audit.php';
			var iconImg='glyphicon-list';
		}else if ($(this).hasClass('campon')==true){
			var viewtype='CAMPON';
			var url = 'scripts/campon/campon.php';
			var iconImg='glyphicon-th-large';
		}else if ($(this).hasClass('tracking')==true){
			var viewtype='TRACKING';
			var url = 'scripts/tracking/tracking.php';
			var iconImg='glyphicon-tasks';
		}else if ($(this).hasClass('event')==true){
			var viewtype='EVENT';
			var url = 'scripts/eventcal/eventcal.php';
			var iconImg='glyphicon-calendar';
		}else if ($(this).hasClass('check')==true){
			var viewtype='VALIDATION';
			var url = 'scripts/validation/validation.php';
			var iconImg='glyphicon-check';
		}else{
			alert('ICON CLICKED NOT PROGRAMMED');
		}
		
		if (url!=''){
			var form=$(this).closest("form");
			$('#spinner').spin('large','#5bc0de');
			var the_form_id = form.attr("id");
			var siteID = $('#'+the_form_id +' input[name="siteID"]').val();
		    var candidate = $('#'+the_form_id +' input[name="candidate"]').val();
		 	if (viewtype=='OSS' || viewtype=='ASSET'){	
		    var bypass = $('#'+the_form_id +' input[name="bypass"]').val();
			}else{
				bypass='';
			}

			if (viewtype=='BSDS' || viewtype=='ASSET'){
				var title=viewtype+' '+techno+'<br><span class="badge pull-right">'+ candidate+'</span>';
			}else if (viewtype=='EVENT'){
				var title=viewtype+' '+siteID+' '+techno;
			}else if (viewtype=='EVENT'){
				var title=siteID;
			}else{
				var title=viewtype+' '+techno;
			}
			$('#siteTabs').addtab(viewtype+siteID+techno,iconImg,title);  

			var options = { 
				url: url,
				type: 'post',
				target:  $("#"+viewtype+siteID+techno),   
				success:    function() { 
					$('#spinner').spin(false);		
					if (viewtype=='NET1'){		
						forceResponsiveTables('NET1NB'+siteID);
						$('#NET1NB'+siteID).scroller('NET1NB'+siteID,4);
						forceResponsiveTables('NET1UPG'+siteID);
						$('#NET1UPG'+siteID).scroller('NET1UPG'+siteID,4);
					}else if (viewtype=='RAF'){		
						forceResponsiveTables('RAFTable'+siteID);
						$('#RAFTable'+siteID).scroller('RAFTable'+siteID,3);
					}else if (viewtype=='LOS'){		
						forceResponsiveTables('LOSTable'+siteID);
						$('#LOSTable'+siteID).scroller('LOSTable'+siteID,3);
					}else if (viewtype=='ASSET'){		
						forceResponsiveTables('ASSETTable'+siteID+techno);
						$('#ASSETTable'+siteID+techno).scroller('ASSETTable'+siteID+techno,1);
					}else if (viewtype=='RAN'){
						$('#searchbuttonFiles').click();
						$('#filefilter').collapse('toggle');
					}
					

					$("a.tippy").tooltip();
					$("[rel=popover]").popover({html:true});
				},
				data: { techno:techno,bypass:bypass}
			};
			$('#'+the_form_id).ajaxSubmit(options); 
			e.preventDefault();
		}
	});

/****************************************************************************************
* NET1 (+BSDS)
*****************************************************************************************/
	$("body").on("click",".history",function( e ){
		var id=$(this).attr('id');
		var clone=$(this).data('clone');
		$("."+id+"_data").fadeToggle("slow");
		//THis is for responsive overlapping problem:
		if (clone){
			$("#"+clone).fadeToggle();
		}
	});

/****************************************************************************************
* OSS
*****************************************************************************************/
	$("body").on("click",".osstrx",function( e ){
		id=$(this).attr('id');
		$("."+id+"_data").slideToggle('slow');
	});


/****************************************************************************************
* VALIDATION
*****************************************************************************************/
	$("body").on("click",".validation",function( e ){
		var siteupgnr=$(this).data('siteupgnr');
		var nbup=$(this).data('nbup');
		$.ajax({
			type: "POST",
			url: 'scripts/validation/validation.php',
			data: { siteupgnr:siteupgnr},
			success : function(data){
				$("#myModalDialog").addClass("modalwide");
				$("#savemodal").hide();
				$("#savemodal").addClass("disabled");
				$("#savemodal").data("module","signoff");
				$('#myModal .modal-header').html('<h4>Validation for '+siteupgnr+':</h4>');
			    $('#myModal .modal-body').html(data); 
			    $('#myModal').modal('show');
			    $('#spinner').spin(false);
			    $("a.tippy").tooltip();
			},
			beforeSend: function ( xhr ) {
				$('#spinner').spin();
			}
		});
	});

/****************************************************************************************
* NET1 LIVE REFRESH
*****************************************************************************************/
	$("body").on("click",".refreshN1",function( e ){
		var siteupgnr=$(this).data('siteupgnr');
		var site=$(this).data('site');
		var nbup=$(this).data('nbup');
		$.ajax({
			type: "POST",
			url: 'scripts/net1/net1_actions.php',
			data: { siteupgnr:siteupgnr,site:site,nbup:nbup},
			success : function(data){
				
				$('#spinner').spin(false);

				Messenger().post({
					message: data,
					showCloseButton: true,
					type: 'info'
				});
				$('#net1icon'+site.substr(1, 6)).click();
			},
			beforeSend: function ( xhr ) {
				$('#spinner').spin();
			}
		});
	});
	
/****************************************************************************************
* filebrowser LIVE RAN
*****************************************************************************************/

	$("body").on("click",".liveran",function( e ){

		var ranurl=$(this).data('ranurl');

		var popupTemplate =
	  '<div class="modal fade">' +
	  '  <div class="modal-dialog modalwide">' +
	  '    <div class="modal-content">' +
	  '      <div class="modal-header">' +
	  '        <button type="button" class="close" data-dismiss="modal">&times;</button>' +
	  '        <h4 class="modal-title">LIVE RAN BROWSER</h4>' +
	  '      </div>' +
	  '      <div class="modal-body" />' +
	  '        <iframe src="'+ranurl+'" style="zoom:0.60" frameborder="0" height="800" width="99.6%"></iframe>' +
	  '      <div class="modal-footer">' +
	  '        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>' +
	  '      </div>' +
	  '    </div>' +
	  '  </div>' +
	  '</div>';

		
		//window.open(ranurl,'LIVERAN', 'width=800, height=600,menubar=yes,location=yes');
		/*
		$("#myModalDialog").addClass("modalwide");
	   	$('#myModal .modal-header').html('<h4>LIVE RAN BROWSER:</h4>');
	    $('#myModal'+' .modal-header').show();
	    $("#savemodal").hide();
	    $('#myModal .modal-body').html('<iframe src="'+ranurl+'" style="zoom:0.60" frameborder="0" height="800" width="99.6%"></iframe>');  
	    $('#myModal').modal({show:true});
		 return false;
		 */
		$(popupTemplate).modal();
		 return false;
	});
	
/****************************************************************************************
* RAF
*****************************************************************************************/
	$("body").on("click",".rafnav",function( e ){
		e.preventDefault();

		var action=$(this).data('action');		
		var siteid=$(this).data('site');
		var rafid=$(this).data('id');

		if (action=="view"){
			$('#RAFcontent').empty();
			$('#selected_rafID').empty();
			$('#modalspinner').spin('small');
			var status= $("#status-"+rafid).val();
			var raftype= $("#raftype-"+rafid).val();
			var saveAllowed=$("#saveAllowed-"+rafid).val();
			var bufferchangeAllowed=$("#bufferchangeAllowed-"+rafid).val();
			var CON_PARTNER=$("#CON_PARTNER-"+rafid).val();

			if (status.indexOf("PARTNER COF")=="0"){
				viewfile='raf_details_cof';
			}else if (status.indexOf("PARTNER")=="0"){
				viewfile='raf_details_partner';
			}else if (status.indexOf("BASE RF")=="0"){
				viewfile='raf_details_radio';
			}else if (status.indexOf("TXMN")=="0"){
				viewfile='raf_details_txmn';
			}else if (status.indexOf("BASE Delivery")=="0"){
				viewfile='raf_details_cof';
			}else{
				viewfile='raf_details_other';
			}
			$('#rafdetails').modal();
			$('#RAFcontent').load("scripts/raf/"+viewfile+".php", {
		  		status: status,
		  		saveAllowed : saveAllowed,
		  		bufferchangeAllowed : bufferchangeAllowed,
		  		raftype: raftype,
		  		rafid: rafid,
		  		CON_PARTNER:CON_PARTNER,
		  		siteid: siteid
		  	}, function(){	
		  		$("#selected_rafID").html(rafid);
		  		$("#selected_siteID").html(siteid);
		  		$(".nav-collapse-rafdetails LI").removeClass('active');
		  		$("#"+viewfile).addClass('active');
		  		$('#modalspinner').spin(false);
		  	}); 
		}else if (action=="newraf"){
			e.preventDefault();
 			var siteid=$(this).data('siteid');
			var url = $(this).attr('href');
			
			$.ajax({
				type: "POST",
				url: url,
				data: { siteid:siteid},
				success : function(data){
					$("#savemodal").data("module","rafnew");
					$('#myModal .modal-header').html('<h4>Create new RAF for '+siteid+':</h4>');
				    $('#myModal .modal-body').html(data); 
				    $('#myModal').modal('show');
				    $('#spinner').spin(false);
				},
				beforeSend: function ( xhr ) {
					$('#spinner').spin();
				}
			});
		}else if (action=="tracking"){
			e.preventDefault();
 			var rafid=$(this).data('id');
 			var siteid=$(this).data('siteid');
			var url = $(this).attr('href');
			
			$.ajax({
				type: "POST",
				url: url,
				data: { siteid:siteid, rafid:rafid},
				success : function(data){
					$("#myModalDialog").addClass("modalwide");
					$("#savemodal").hide();
					$('#myModal .modal-header').html('<h4>View tracking info for '+siteid+' / RAFID '+rafid+':</h4>');
				    $('#myModal .modal-body').html(data); 
				    $('#myModal').modal('show');
				    $('#spinner').spin(false);
				},
				beforeSend: function ( xhr ) {
					$('#spinner').spin();
				}
			});
		}else if (action=="history"){
			e.preventDefault();
 			var rafid=$(this).data('id');
			var url = $(this).attr('href');
			$.ajax({
				type: "POST",
				url: url,
				data: { rafid:rafid},
				success : function(data){
					$("#savemodal").hide();
					$("#myModalDialog").addClass("modalmedium");
					$('#myModal .modal-header').html('<h4>History RAF '+rafid+':</h4>');
				    $('#myModal .modal-body').html(data); 
				    $('#myModal').modal('show');
				    $('#spinner').spin(false);
				},
				beforeSend: function ( xhr ) {
					$('#spinner').spin();
				}
			});
		}else if (action=="print"){
			e.preventDefault();
 			var siteid=$(this).data('siteid');
 			var rafid=$(this).data('id');
			
			$.ajax({
				type: "POST",
				url: 'scripts/raf/raf_print.php',
				data: { siteid:siteid,rafid:rafid},
				success : function(data){
					$("#savemodal").hide();
					$("#myModalDialog").addClass("modalmedium");
					$('#myModal .modal-header').html('<h4>Print RAF '+rafid+' for '+siteid+':</h4><button type="button" class="btn btn-primary" id="savemodal" data-module="rafprint">PRINT</button>');
				    $('#myModal .modal-body').html(data); 
				    $('#myModal').modal('show');
				    $('#spinner').spin(false);
				},
				beforeSend: function ( xhr ) {
					$('#spinner').spin();
				}
			});
		}else if (action=="override_raf"){
			e.preventDefault();
			var site=$(this).data('siteid');
			var rafid=$(this).data('rafid');
			msg = Messenger().post({
			  message: 'Are u sure you want to override the creation of RAF '+rafid+' for for site '+site+'?',
			  type: 'info',
			  actions: {
			    ok: {
			      label: "I'm sure to override",
			      action: function() {
			      	msg.cancel();			 
					$.ajax({
						type: "POST",
						dataType: "json",
						url: "scripts/raf/raf_actions.php",
						data: { siteID:site,rafid:rafid, action:'override_raf'},
						success : function(data){
								$('#spinner').spin(false);
								Messenger().post({
								message: data.msg,
								type: data.type,
								showCloseButton: true
							});
							$('#raficon'+data.siteID).click();
						},
						beforeSend: function ( xhr ) {
							$('#spinner').spin('large');
						}
					});					  
			      }
			    },
			    cancel: {
			      label: 'cancel',
			      action: function() {
					return msg.cancel(); 
			      }
			    }
			  }
			});
		
		}else if (action=="delete_raf" || action=="undelete_raf" || action=="unlock_raf" || action=="lock_raf"){
			e.preventDefault();
			var rafid=$(this).data('id');
			var site=$(this).data('site');
			var msg;
			msg = Messenger().post({
			  message: 'Are u sure you want to change status for RAF with ID '+rafid+' for site '+site+'?',
			  type: 'info',
			  actions: {
			    ok: {
			      label: "I'm sure",
			      action: function() {
			      	msg.cancel();
			      	if (action=="delete_raf" || action=="lock_raf"){
						$.ajax({
							type: "POST",
							url: "scripts/raf/raf_delete.php",
							data: { rafid:rafid, siteID:site, action:action},
							success : function(data){
								$('#spinner').spin(false);
								$("#savemodal").data("module","rafdelete");
								$('#myModal .modal-header').html('<h4>Change RAF '+rafid+':</h4>');
							    $('#myModal .modal-body').html(data); 
							    $('#myModal').modal('show');
							},
							beforeSend: function ( xhr ) {
								$('#spinner').spin("large");
							}
						});
					}else if (action=="undelete_raf" || action=="unlock_raf"){   
						$.ajax({
							type: "POST",
							dataType: "json",
							url: "scripts/raf/raf_actions.php",
							data: { rafid:rafid, siteID:site, action:action},
							success : function(data){
								$('#spinner').spin(false);
								Messenger().post({
									message: data.msg,
									 type: data.type,
									 showCloseButton: true
								});
								$('#raficon'+data.siteID).click();
							},
							beforeSend: function ( xhr ) {
								$('#spinner').spin('large');
							}
						});
					}   
			      }
			    },
			    cancel: {
			      label: 'cancel',
			      action: function() {
					return msg.cancel(); 
			      }
			    }
			  }
			});
		}else if (action=="showhidedeleted"){
			if ($('.showhide').hasClass("hide")) {
			    $('.showhide').removeClass("hide");
			}else {
			    $('.showhide').addClass("hide");
			  }
		}else if (action=="refresh"){
			e.preventDefault();
 			var siteid=$(this).data('siteid');
 			var rafid=$(this).data('id');
			
			$.ajax({
				type: "POST",
				url: 'http://svrbeibase02/bash_scripts/raf_milestones.php',
				data: { rafid:rafid},
				success : function(data){
					$("#savemodal").hide();
					$("#myModalDialog").addClass("modalmedium");
					$('#myModal .modal-header').html('<h4>Output of refresh of RAFID '+rafid+'</h4>');
				    $('#myModal .modal-body').html(data); 
				    $('#myModal').modal('show');
				    $('#spinner').spin(false);
				},
				beforeSend: function ( xhr ) {
					$('#spinner').spin();
				}
			});
		}
	});	
	$("body").on("click","#raf-confirm-lock",function( e ){
		$('#RafLockModal').modal('hide');
		var rafid = $(this).data('id');
		var delreason=$('#delreason').val();
		$.post("scripts/raf/raf_actions.php", { 
			action:"lock_raf",
        	rafid:rafid,
        	delreason: delreason
		},function(data){
			$('#status_'+rafid).text('RAF LOCKED!');
			$('#raficon'+$('#rafSiteID').val()).click();			
		})
	});
	$("body").on("click","#raf-confirm-unlock",function( e ){
		$('#RafUnLockModal').modal('hide');
		var rafid = $(this).data('id');
		$.post("scripts/raf/raf_actions.php", { 
			action:"unlock_raf",
        		rafid:rafid
		},function(data){
			$('#status_'+rafid).text('RAF UNLOCKED !');
			$('#raficon'+$('#rafSiteID').val()).click();			
		})
	});
	$("body").on("click",".rafdetails LI",function( e ){
		$('.rafdetails LI').removeClass('active');
		$(this).addClass('active');
		$('#RAFcontent').empty();
		$('#modalspinner').spin('small');

		var id=$(this).attr('id');
		var rafid = $("#selected_rafID").text();
		var raftype= $("#raftype-"+rafid).val();
		var status= $("#status-"+rafid).val();
		var CON_PARTNER=$("#CON_PARTNER-"+rafid).val();
		var saveAllowed=$("#saveAllowed-"+rafid).val();

		$('#RAFcontent').load('scripts/raf/'+ id +'.php', {
		  	rafid:rafid,
		  	raftype:raftype,
		  	status:status,
		  	CON_PARTNER:CON_PARTNER,
		  	saveAllowed: saveAllowed	
		},				
		function(){
			$('#modalspinner').spin(false);
		});
	});
	$("body").on("click",".rafdetailsImg",function( e ){
		var imgScreen= $(this).data("file");
		$('#RAFcontent').html('<img src="'+imgScreen+'">').fadeIn("slow");
	});
	$("body").on("click",".raffile_delete",function( e ){
		var file=$(this).data('file');
		var fileid=$(this).data('fileid');
		
		var msg;
		msg = Messenger().post({
		  message: 'Are u sure you want to delete file '+file+'?',
		  type: 'info',
		  actions: {
		    ok: {
		      label: "I'm sure",
		      action: function() {
		      	msg.cancel();
		      	Messenger().run({
				  errorMessage: "This did not go well."
				}, {
				  url: "scripts/raf/raf_actions.php",
				  data: { action:"delete_raffile",
		          raffile:file },
		          type:'POST',
				  success: function(response) {
					$('#raffile_'+fileid).fadeOut( "slow" );
					return 'File "'+file+'" has been deleted';
				  }
				});	        
		      }
		    },
		    cancel: {
		      label: 'cancel',
		      action: function() {
				return msg.cancel(); 
		      }
		    }
		  }
		});
	}); 
	$("body").on("mouseover",".editableItem",function(e){
		var text= $(this).text();
		if (text=='NOT SET'){
			var val='';
		}else{
			var val=text;
		}
        $(this).editable({ 
		    url : "scripts/raf/raf_actions.php",
		    value:val,
		    params: function(params) {
		    	select_id = $(this).attr('id');
				id = select_id.split('-');
			 	type=$('#type-'+id[1]).val();
			 	params.creator=$('#createdby-'+id[1]).val();			 	
				params.action= "change_net1link";
				params.id=id[1];
				params.field=id[0];
			    return params;
			},
		 	success : function(response, value) {
		 		var select_id = $(this).attr('id');
				var id = select_id.split('-');
				var rafid = $(this).data('pk');
				var status=$('#status_'+id[1]).text();		  	 
				var select_val=value.replace(/^\s+/g,'').replace(/\s+$/g,'');		  	 
				var user_RADIO=$('#user_RADIO_INP_BY-'+id[1]).val();
				var user_TXMN=$('#user_TXMN_INP_BY-'+id[1]).val();		  
				var type_r=$('#type-'+id[1]).text();		  
				var siteid=$('#sitename-'+id[1]).val();
			 
			 	$('#raficon'+$('#rafSiteID').val()).click();
	     	}
	    });
	});

	$("body").on("mouseover",".editableSelectItem",function(e){
		var select_id = $(this).attr('id');
		var id = select_id.split('-');
		var rafid = $(this).data('pk');
		var raftype=$('#type-'+id[1]).val();
		var siteid = $(this).data('siteid');
		var oldval=$('#'+select_id).text();

		if (id[0]==='RADIO_FUND'){
			var inputtype='checklist';
		}else{
			var inputtype='select';
		}

		$(this).editable({ 
		    source: "scripts/raf/raf_select_options.php",
		    sourceOptions: { 
		    	data: { siteid:siteid, raftype:raftype, field:id[0],oldval:oldval} ,
		    	type: "POST"
		    },
		    sourceCache: false,
		    type: inputtype,
		    placement:'bottom',
		    url : "scripts/raf/raf_actions.php",
		    params: function(params) {
			    select_id = $(this).attr('id');
				id = select_id.split('-');
				params.creator=$('#createdby-'+rafid).val();

				if(id[0]==='BSDS'){
					params.action= "attach_BSDSKEY";
				}else{
					params.action= "change_net1link";
				}
				params.id=id[1];
				params.field=id[0];
				params.raftype=raftype;
			    return params;
			},
			success:function(response, value) {
    			
			  	var select_id = $(this).attr('id');
				var id = select_id.split('-');
				var status=$('#status_'+id[1]).text();
				var type=$('#type_'+id[1]).text();
				var result_BC_check=type.indexOf('Upgrade');	
				var siteid=$('#sitename-'+id[1]).val();		  

				if (value==='REJECTED' || value==='STOPPED'){ 
					if (id[0]==='NET1_FUND'){
						var previous_user=$('#user_RADIO_FUND_BY-'+rafid).val();
					}else if(id[0]==='RF_PAC'){
						var previous_user=$('#user_PARTNER_RFPACK_BY-'+rafid).val();
					}else if(id[0]==='BCS_RF_INP' || id[0]==='BCS_TX_INP' || id[0]==='PARTNER_ACQUIRED'){
						var previous_user=$('#user_PARTNER_INP_BY-'+rafid).val();
					}else if(id[0]==='TXMN_ACQUIRED'){
						var previous_user=$('#user_PARTNER_ACQUIRED_BY-'+rafid).val();			
					}else{
						var previous_user="";
					}

					$.get('scripts/raf/raf_reject_reason.php',{
						siteID:siteid,
						rafid:id[1],
						field:id[0]+'_REJECT',
						previous_user:previous_user},
					function(data)
					{
						$("#savemodal").data("module","rafreject");
						$('#myModal .modal-header').html('<h4>Rejection reason for RAF '+rafid+':</h4>');
					    $('#myModal .modal-body').html(data); 
					    $('#myModal').modal('show');
            		}).success(function(){ 
            			$('input:text:visible:first').focus();
            		});			

				}else{
					$('#status_'+id[1]).text('RAF HAS UPDATED!');
			 		$('#raficon'+$('#rafSiteID').val()).click();
				}
			}
  		});
	});


	$("body").on("click",".saveCOF",function( e ){
		e.preventDefault();
		var rafid=$(this).data('rafid');
		var acqcon=$(this).data('acqcon');

		function after_COFcheck(response){  
			$('#modalspinner').spin(false);
			if (response.rtype=='info'){
				$('#'+response.table).append(response.row);
			}
			Messenger().post({
				message: response.msg,
				type: response.rtype,
				showCloseButton: true
			});
			
		}
		var options = {
			success: after_COFcheck,
			dataType:  'json',
		};
		$('#modalspinner').spin('medium');
		$('#form_'+rafid+'Add'+acqcon).ajaxSubmit(options); 
	   	return false; 
	});

/****************************************************************************************
* LOS
*****************************************************************************************/
	$("body").on("click",".losnav",function( event ){
		var action=$(this).attr('id');
		$('#LOScontent').empty();
		$('#LOScontentNet1').empty();
		$('.nav-collapse-losdetails LI').removeClass('active');
		if (action=="view"){
			var losid=$(this).data('losid');
			$("#selected_losID").html(losid);
			var siteIDA=$(this).data('sitea');
			var siteIDB=$(this).data('siteb');
			$('#losdetails').modal();
			$('#LOScontentNet1').load("scripts/los/los_details_net1.php", {		  		
		  		losid: losid,
		  		siteIDA: siteIDA,
		  		siteIDB: siteIDB
		  	}, function(){		  		
		  		$('#modalspinner').spin(false);
		  	});
		}else if (action=="newlos"){
			event.preventDefault();
 			var siteid=$(this).data('siteid');
			var url = $(this).attr('href');
			$("#savemodal").data( "module","losnew");
			$('#myModal .modal-header').html('<h4>Create new LOS:</h4>');
			$.ajax({
				type: "GET",
				url: url,
				success : function(data){
				    $('#myModal .modal-body').html(data); 
				    $('#myModal').modal('show');
				    $('#spinner').spin(false);
				},
				beforeSend: function ( xhr ) {
					$('#spinner').spin();
				}
			});
		}else if (action=="delete_los" || action=="undelete_los"){
			event.preventDefault();
			var losid=$(this).data('losid');
			var msg;
			if (action=="undelete_los"){
				var tmpmes='UN';
			}else{
				var tmpmes='';
			}

			msg = Messenger().post({
			  message: 'Are u sure you want to '+tmpmes+'DELETE LOS with ID '+losid+'?',
			  type: 'info',
			  actions: {
			    ok: {
			      label: "I'm sure",
			      action: function() {
			      	msg.cancel();
			      	Messenger().run({
					  errorMessage: "This did not go well."
					}, {
					  url: "scripts/los/los_actions.php",
					  data: { action:action,
			        	losid:losid },
			          type:'POST',
					  success: function() {
					  	if (action=="delete_los"){
							$('#losLine'+losid).fadeOut( "slow" );
							return 'LOS '+losid+' has been deleted';
						}else if (action=="undelete_los"){
							$('#losicon').click();
							return 'LOS '+losid+' has been UNdeleted';
						}
							
					  }
					});	        
			      }
			    },
			    cancel: {
			      label: 'cancel',
			      action: function() {
					return msg.cancel(); 
			      }
			    }
			  }
			});

		}else if (action=="reopen"){

			event.preventDefault();
			var losid=$(this).data('losid');
			var msg;

		msg = Messenger().post({
		  message: 'Are u sure you want to REOPEN LOS with ID '+losid+'?',
		  type: 'info',
		  actions: {
		    ok: {
		      label: "I'm sure",
		      action: function() {
		      	msg.cancel();
		      	Messenger().run({
				  errorMessage: "This did not go well."
				}, {
				  url: "scripts/los/los_actions.php",
				  data: { action:"reopen_los",
		        	losid:losid },
		          type:'POST',
				  success: function(response) {
					$('#losicon').click();
					return response;	
				  }
				});	        
		      }
		    },
		    cancel: {
		      label: 'cancel',
		      action: function() {
				return msg.cancel(); 
		      }
		    }
		  }
		});	
		}else if (action=="print"){
			//window.open(url+'?rafid='+rafid);
			event.preventDefault();
 			var losid=$(this).data('losid');
			var url = $(this).attr('href');
			
			$.ajax({
				type: "POST",
				url: url,
				data: { siteid:siteid,losid:losid},
				success : function(data){
					$("#savemodal").hide();
					$('#myModal .modal-header').html('<h4>Print LOS '+losid+'</h4><button type="button" class="btn btn-primary" id="savemodal" data-module="losprint">PRINT</button>');
				    $('#myModal .modal-body').html(data); 
				    $('#myModal').modal('show');
				    $('#spinner').spin(false);
				},
				beforeSend: function ( xhr ) {
					$('#spinner').spin();
				}
			});
		}
	});	
	$("body").on("click",".losdetails li",function( e ){
		$('#modalspinner').spin('small');
		$('.losdetails LI').removeClass('active');
		$(this).addClass('active');
		$('#LOScontent').empty();
		
		var id=$(this).attr('id');
		var losid = $("#selected_losID").text();
		var end='';
		if (id=="los_details_A"){
			id="los_details_AB";
			end="A";
		}else if (id=="los_details_B"){
			id="los_details_AB";
			end="B";
		}
		$('#LOScontent').load('scripts/los/'+ id +'.php', {
		  	losid:losid,
		  	end:end
		},				
		function(){
			$('#modalspinner').spin(false);
		});
	});
	$("body").on("click",".losdetailsImg",function( e ){
		var image= $(this).data("file");
		$('#losimg').html('<img src="'+image+'">').slideDown("slow");
		$('#losImages').slideDown("slow");
	});
	$("body").on("click",".losimgClose",function( e ){
		$('#losImages').slideUp("fast");
	});
	$("body").on("click",".losfile_delete",function( e ){
		var file=$(this).data('file');
		var fileid=$(this).data('fileid');
		
		var msg;
		msg = Messenger().post({
		  message: 'Are u sure you want to delete file '+file+'?',
		  type: 'info',
		  actions: {
		    ok: {
		      label: "I'm sure",
		      action: function() {
		      	msg.cancel();
		      	Messenger().run({
				  errorMessage: "This did not go well."
				}, {
				  url: "scripts/los/los_actions.php",
				  data: { action:"delete_losfile",
		        	losfile:file },
		          type:'POST',
				  success: function(response) {
					$('#losfile_'+fileid).fadeOut( "slow" );
					return 'File "'+file+'" has been deleted';
				  }
				});	        
		      }
		    },
		    cancel: {
		      label: 'cancel',
		      action: function() {
				return msg.cancel(); 
		      }
		    }
		  }
		});
	}); 

	$("body").on("mouseover",".editableLosSelectItem",function(e){
		$(this).editable({ 
		    source: function(){
		    	var result;
		    	var ltype = $(this).data('ltype');
		    	if (ltype==='DONE' || ltype==='REPORT'){
		    		return '[{value: "OK", text: "OK"}, {value: "NOT OK", text: "NOT OK"}, {value: "NA", text: "NA"}]';
		    	}else if (ltype==='RESULT'){
		    		return '[{value: "OK", text: "OK"}, {value: "NOT OK", text: "NOT OK"}, {value: "NLOS", text: "NLOS"},{value: "NA", text: "NA"},{value: "Unconfirmed", text: "Unconfirmed"},{value: "LOS", text: "LOS"},{value: "Critical", text: "Critical"},{value: "REJECTED", text: "REJECTED"}]';
		    	}
                return result;
		    },
		    url : "scripts/los/los_actions.php",
		    type: 'select',
		    params: function(params) {
			    var ltype = $(this).data('ltype');
				params.action= "change_status";
				params.field=ltype;
			    return params;
			},
			success:function(response, value) {
    			var ltype = $(this).data('ltype');
    			var response = $.parseJSON(response);
			    var rmessage = response.rmessage;
			    var partner = $(this).data('partner');
			    var id = response.id;
				var select_val=value.replace(/^\s+/g,'').replace(/\s+$/g,'');  
				var siteid=$('#sitename-'+id).val();		  

				if (select_val==='REJECTED'){ 
					$(".modaldismiss").hide();
					$("#savemodal").removeClass("disabled");
					$('#myModal .modal-header').html('<h4>Reason for rejection for los id '+id+':</h4>');

					$('#myModal .modal-body').load('scripts/los/los_reject_reason.php',{losid:id,partner:partner},function(result){
						
						$("#savemodal").data("module","losreject");
				
			   			$("#savemodal").data("id",id);
			   			$('#myModal').modal({backdrop: 'static',keyboard: false,show:true});
			   		});
				}else{
					Messenger().post({
					  message: rmessage,
					  showCloseButton: true
					});
				}
			}
  		});
	});

	$("body").on("click",".deletematerial",function( e ){
		var rafid=$(this).data('rafid');
		var material=$(this).data('material');
		var acqcon=$(this).data('acqcon');
		
		var msg;
		msg = Messenger().post({
		  message: 'Are u sure you want to delete MATERIAL CODE '+material+'?',
		  type: 'info',
		  actions: {
		    ok: {
		      label: "I'm sure",
		      action: function() {
		      	msg.cancel();
		      	Messenger().run({
				  errorMessage: "This did not go well."
				}, {
				  url: "scripts/raf/raf_actions.php",
				  data: { action:"delete_material",
		          rafid:rafid,
		           material:material,
		           acqcon:acqcon
		       },
		          type:'POST',
				  success: function(response) {
				  	var response = $.parseJSON(response);
				  	//alert('#material_'+rafid+material+acqcon);
				  	if($('#material_'+rafid+material+acqcon).length == 0) {
					  alert('noit');
					}else{
						$('#material_'+rafid+material+acqcon).hide();
						$('#5073_COFCONtable').hide();
					}
					$('#material_'+rafid+material+acqcon).css("color","red");
					$(this).css("background-color","red");
					//$(this).closest("tr").hide();
					return response.msg;
				  }
				});	        
		      }
		    },
		    cancel: {
		      label: 'cancel',
		      action: function() {
				return msg.cancel(); 
		      }
		    }
		  }
		});
	}); 

/****************************************************************************************
* LOS REPORT
*****************************************************************************************/

$("body").on("click","#displayLosform",function( e ){
	e.preventDefault();
	$('#filter').collapse('toggle');
	$('#spinner').spin('medium');
	$("#reportoutput").hide('fast');
	
	if ($('#csvreport').is(':checked')){
		var options = {
		   	target:  '#reportoutput',
		    success:    function(responseText) {
					$("#reportoutput").show('fast');
					$('#spinner').spin(false);
			},
			url:'scripts/los/los_csv.php'
		};
	}else{
		var options = {
		   	target:  '#reportoutput',
		    success:    function() {
					$("#reportoutput").show('fast');
					forceResponsiveTables("LOSTable");
					$('#RAFTable').scroller('LOSTable',4);
					$('#spinner').spin(false);
			},
			url:'scripts/los/los.php'
		};
	}
	
	$("#LosReportForm").ajaxSubmit(options);
    return false;
});

/****************************************************************************************
* RAF REPORT
*****************************************************************************************/

$("body").on("click","#displayRafform",function( e ){
	e.preventDefault();
	$('#filter').collapse('toggle');
	$('#spinner').spin('medium');
	$("#reportoutput").hide('fast');
	var options = {
	   	target:  '#reportoutput',
	    success:    function() {
				$("#reportoutput").show('fast');
				forceResponsiveTables("RAFTable");
				$('#RAFTable').scroller('RAFTable',4);
				$('#spinner').spin(false);
		},
		url:'scripts/raf/raf.php'
	};
	$("#RafReportForm").ajaxSubmit(options);
    return false;
});	
/****************************************************************************************
* NETUSERLIST REPORT
*****************************************************************************************/

$("body").on("click","#Net1USerListform",function( e ){
	e.preventDefault();
	$('#userlistfilter').collapse('toggle');
	$('#spinner').spin('medium');
	$("#reportoutput").hide('fast');
	var options = {
	   	target:  '#reportoutput',
	    success:    function(data) {
	    		$("#reportoutput").show('fast');
				$('#spinner').spin(false);
		}
	};
	$("#Net1UsersForm").ajaxSubmit(options);
    return false;
});	
/****************************************************************************************
* NET1TASKLIST REPORT
*****************************************************************************************/

$("body").on("click","#Net1TaskListform",function( e ){
	e.preventDefault();
	$('#tasklistfilter').collapse('toggle');
	$('#spinner').spin('medium');
	$("#TaskListoutput").hide('fast');
	var options = {
	   	target:  '#TaskListoutput',
	    success:    function(data) {
	    		$("#TaskListoutput").show('fast');
				$('#spinner').spin(false);
				$("#NET1TaskListTable").tableDnD({
        			onDragClass: "TableDrag",
        			onDrop: function(table, row) {
        				var groupname=$("#NET1TaskListTable").data('groupname');
        				var grouptype=$("#NET1TaskListTable").data('grouptype');
				        $.ajax({
							type: "POST",
							url: 'scripts/net1tasks/net1tasks_procedures.php?'+$.tableDnD.serialize()+'&groupname='+groupname+'&grouptype='+grouptype,
							data: { 
								action:'updateTaskOrder',
							},
							success : function(data){
								//alert('data');
							},
							beforeSend: function ( xhr ) {
								$('#spinner').spin();
							}
						});
				    }
				});
		}
	};
	$("#Net1TasksForm").ajaxSubmit(options);
    return false;
});
/****************************************************************************************
* DOCUMENT MANAGER
*****************************************************************************************/
$("body").on("submit","#searchFormFiles",function( e ){	
	$("#filedata").hide('fast');
	$('#spinner').spin('medium');	
});
$("body").on("click","#searchbuttonFiles",function( e ){
	$("#filedata").hide('fast');
	$('#spinner').spin('medium');
	$('#filefilter').collapse('toggle');
	var options = { 
	  target:  '#filedata',   
	  success:    function() { 
			$("#filedata").show('slow');
			$('#spinner').spin(false);
			$("a.tippy").tooltip();
			$('#filebrowserdata').DataTable();
			forceResponsiveTables('filebrowserdata');
		}  
	};			
  	$("#searchFormFiles").ajaxSubmit(options);  	
   	return false; 
});	
/****************************************************************************************
* BSDS
*****************************************************************************************/
	$("body").on("click",".new_BSDS",function( e ){
		var siteid=$(this).data('siteid');
		var candidate=$(this).data('candidate');
		var addressfk=$(this).data('addressfk');
		var url = $(this).attr('href');
		$("#savemodal").data( "module","bsdsNew");
		$("#savemodal").data( "id",siteid);
		$('#myModal .modal-header').html('<h4>Create new BSDS for '+candidate+':</h4>');
		$('#myModal .modal-body').load(url,{siteID:siteid,candidate:candidate,ADDRESSFK:addressfk});
		$('#myModal').modal('show');
	});


	$("body").on("click",".bsdsdetails2",function( e ){
		
		var clicked_tab=$(this).data("techno");		
		var id=$(this).data("id");
		var reloadAsset=$(this).data("reloadasset");	
		var bsdskey=$('#bsdsform_'+id+' input[name="bsdskey"]').val();
		var status = $('#bsdsform_'+id+' input[name="status"]').val();
		var candidate = $('#bsdsform_'+id+' input[name="candidate"]').val();
		var bsdsbobrefresh=$('#bsdsform_'+id+' input[name="bsdsbobrefresh"]').val();
		var rafid=$('#bsdsform_'+id+' input[name="rafid"]').val();

		if (clicked_tab!=="CHANGEID" && clicked_tab!=="DELETE" && clicked_tab!=="REMOVEFUNDING"){ 			

			var targettype=clicked_tab+'_'+bsdskey+'_'+status;

			if (clicked_tab==="PRINT"){
				var title=clicked_tab+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
				$('#siteTabs').addtab(targettype,'glyphicon-book',title);
				$('#'+targettype).empty();
				var title= 'PRINT BSDS '+bsdskey+' <br>['+bsdsbobrefresh+']';
				$('#'+targettype).append('<p align="right"><button id="bsds-print" data-title="'+title+'" class="btn btn-primary" data-id="'+targettype+'">PRINT</button></p>');
				var technos=$(this).data("technos");

				if (technos.indexOf('G9')!=-1){		
					load_curpl2('G9',targettype,status,bsdskey,bsdsbobrefresh,id,'yes',reloadAsset);
				}
				if (technos.indexOf('G18')!=-1){	
					load_curpl2('G18',targettype,status,bsdskey,bsdsbobrefresh,id,'yes',reloadAsset);
				}
				if (technos.indexOf('U9')!=-1){	
					load_curpl2('U9',targettype,status,bsdskey,bsdsbobrefresh,id,'yes',reloadAsset);	
				}
				if (technos.indexOf('U21')!=-1){		
					load_curpl2('U21',targettype,status,bsdskey,bsdsbobrefresh,id,'yes',reloadAsset);
				}
				if (technos.indexOf('L8')!=-1){	
					load_curpl2('L8',targettype,status,bsdskey,bsdsbobrefresh,id,'yes',reloadAsset);
				}
				if (technos.indexOf('L18')!=-1){			
					load_curpl2('L18',targettype,status,bsdskey,bsdsbobrefresh,id,'yes',reloadAsset);
				}
				if (technos.indexOf('L26')!=-1){			
					load_curpl2('L26',targettype,status,bsdskey,bsdsbobrefresh,id,'yes',reloadAsset);
				}
			}else if (clicked_tab==="BIPT"){

				var title=clicked_tab+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
				$('#siteTabs').addtab(targettype,'glyphicon-book',title);
				$('#'+targettype).empty();
				var technos=$(this).data("technos");

				load_curpl_BIPT(targettype,status,bsdskey,bsdsbobrefresh,id);

			}else if (clicked_tab==="DELETEBSDS"){
				var bsdsid=$(this).data('bsdsid');
				var msg;

				msg = Messenger().post({
				  message: 'Are u sure you want to DELETE BSDS with ID '+bsdsid+'?',
				  type: 'info',
				  actions: {
				    ok: {
				      label: "I'm sure",
				      action: function() {
				      	msg.cancel();
				      	Messenger().run({
						  errorMessage: "This did not go well."
						}, {
						  url: "scripts/general_info/general_info_actions.php",
						  data: { action:"delete_bsds",
				        	bsdsid:bsdsid },
				          type:'POST',
						  success: function(response) {
							$('#bsdsrow_'+bsdsid).fadeOut( "slow" );
							return response;	
						  }
						});	        
				      }
				    },
				    cancel: {
				      label: 'cancel',
				      action: function() {
						return msg.cancel(); 
				      }
				    }
				  }
				});

			}else if (clicked_tab==="ALL"){	
				var technos=$(this).data("technos");	

				if (technos.indexOf('G9')!=-1){		
					var targettype='G9'+'_'+bsdskey+'_'+status;
					var title='G9'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);
					load_curpl2('G9',targettype,status,bsdskey,bsdsbobrefresh,id,'no',reloadAsset);
				}
				if (technos.indexOf('G18')!=-1){		
					var targettype='G18'+'_'+bsdskey+'_'+status;
					var title='G18'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);
					load_curpl2('G18',targettype,status,bsdskey,bsdsbobrefresh,id,'no',reloadAsset);	
				}
				if (technos.indexOf('U9')!=-1){	
					var targettype='U9'+'_'+bsdskey+'_'+status;	
					var title='U9'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);		
					load_curpl2('U9',targettype,status,bsdskey,bsdsbobrefresh,id,'no',reloadAsset);
				}
				if (technos.indexOf('U21')!=-1){	
					var targettype='U21'+'_'+bsdskey+'_'+status;
					var title='U21'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);		
					load_curpl2('U21',targettype,status,bsdskey,bsdsbobrefresh,id,'no',reloadAsset);	
				}
				if (technos.indexOf('L8')!=-1){	
					var targettype='L8'+'_'+bsdskey+'_'+status;
					var title='L8'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);		
					load_curpl2('L8',targettype,status,bsdskey,bsdsbobrefresh,id,'no',reloadAsset);	
				}
				if (technos.indexOf('L18')!=-1){	
					var targettype='L18'+'_'+bsdskey+'_'+status;
					var title='L18'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);		
					load_curpl2('L18',targettype,status,bsdskey,bsdsbobrefresh,id,'no',reloadAsset);	
				}
				if (technos.indexOf('L26')!=-1){	
					var targettype='L26'+'_'+bsdskey+'_'+status;
					var title='L26'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);	
					load_curpl2('L26',targettype,status,bsdskey,bsdsbobrefresh,id,'no',reloadAsset);
				}
			}else{

				var title=status+' '+clicked_tab+'<br><span class="badge pull-left">RAF '+rafid+'</span><span class="badge pull-right">'+candidate+'</span>';
				$('#siteTabs').addtab(targettype,'glyphicon-book',title);
				$('#'+targettype).append('<div id="'+targettype+clicked_tab+'"></div>');
				load_curpl2(clicked_tab,targettype,status,bsdskey,bsdsbobrefresh,id,'no',reloadAsset);
			}
			
		}else if(clicked_tab==="REMOVEFUNDING"){
			var upgnr=$(this).data('upgnr');
			var net1date=$(this).data('net1date');
			var vraag=confirm('Click Ok if you want to remove funding date ('+net1date+') for '+upgnr+' for BSDS with ID "'+bsdskey+'"?');
			if (vraag){	
				$.post("scripts/general_info/general_info_actions.php", { 
					action:"remove_funding",
					id: id,
					net1date : net1date,
					status: status,
					upgnr : upgnr
				},function(data){
					Messenger().post({
					  message: 'The funding date has been temporarly removed!',
					  type: 'info',
					  showCloseButton: true
					});
				})
			}
		}else{
			alert('NOT YET PROGRAMMED');
		}	

	});
	
	$("body").on("click",".bsdsdetails",function( e ){
		var clicked_tab=$(this).data("techno");		
		var id=$(this).data("id");
		var bsdskey=$('#bsdsdetails_'+id+' input[name="bsdskey"]').val();
		var status = $('#bsdsdetails_'+id+' input[name="status"]').val();
		var candidate = $('#bsdsdetails_'+id+' input[name="candidate"]').val();
		var bsdsbobrefresh=$('#bsdsdetails_'+id+' input[name="bsdsbobrefresh"]').val();

		if (clicked_tab!=="CHANGEID" && clicked_tab!=="DELETE" && clicked_tab!=="REMOVEFUNDING"){ 			

			var targettype=clicked_tab+'_'+bsdskey+'_'+status;

			if (clicked_tab==="PRINT"){
				var title=clicked_tab+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
				$('#siteTabs').addtab(targettype,'glyphicon-book',title);
				$('#'+targettype).empty();
				var title= 'PRINT BSDS '+bsdskey+' <br>['+bsdsbobrefresh+']';
				$('#'+targettype).append('<p align="right"><button id="bsds-print" data-title="'+title+'" class="btn btn-primary" data-id="'+targettype+'">PRINT</button></p>');
				var technos=$(this).data("technos");

				if (technos.indexOf('G9')!=-1){		
					load_curpl('G9',targettype,status,bsdskey,bsdsbobrefresh,id,'yes');
				}
				if (technos.indexOf('G18')!=-1){	
					load_curpl('G18',targettype,status,bsdskey,bsdsbobrefresh,id,'yes');
				}
				if (technos.indexOf('U9')!=-1){	
					load_curpl('U9',targettype,status,bsdskey,bsdsbobrefresh,id,'yes');	
				}
				if (technos.indexOf('U21')!=-1){		
					load_curpl('U21',targettype,status,bsdskey,bsdsbobrefresh,id,'yes');
				}
				if (technos.indexOf('L8')!=-1){	
					load_curpl('L8',targettype,status,bsdskey,bsdsbobrefresh,id,'yes');
				}
				if (technos.indexOf('L18')!=-1){			
					load_curpl('L18',targettype,status,bsdskey,bsdsbobrefresh,id,'yes');
				}
				if (technos.indexOf('L26')!=-1){			
					load_curpl('L26',targettype,status,bsdskey,bsdsbobrefresh,id,'yes');
				}
			}else if (clicked_tab==="BIPT"){

				var title=clicked_tab+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
				$('#siteTabs').addtab(targettype,'glyphicon-book',title);
				$('#'+targettype).empty();
				var technos=$(this).data("technos");

				load_curpl_BIPT(targettype,status,bsdskey,bsdsbobrefresh,id);

			}else if (clicked_tab==="DELETEBSDS"){
				var bsdsid=$(this).data('bsdsid');
				var msg;

				msg = Messenger().post({
				  message: 'Are u sure you want to DELETE BSDS with ID '+bsdsid+'?',
				  type: 'info',
				  actions: {
				    ok: {
				      label: "I'm sure",
				      action: function() {
				      	msg.cancel();
				      	Messenger().run({
						  errorMessage: "This did not go well."
						}, {
						  url: "scripts/general_info/general_info_actions.php",
						  data: { action:"delete_bsds",
				        	bsdsid:bsdsid },
				          type:'POST',
						  success: function(response) {
							$('#bsdsrow_'+bsdsid).fadeOut( "slow" );
							return response;	
						  }
						});	        
				      }
				    },
				    cancel: {
				      label: 'cancel',
				      action: function() {
						return msg.cancel(); 
				      }
				    }
				  }
				});

			}else if (clicked_tab==="ALL"){	
				var technos=$(this).data("technos");	

				if (technos.indexOf('G9')!=-1){		
					var targettype='G9'+'_'+bsdskey+'_'+status;
					var title='G9'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);
					load_curpl('G9',targettype,status,bsdskey,bsdsbobrefresh,id,'no');
				}
				if (technos.indexOf('G18')!=-1){		
					var targettype='G18'+'_'+bsdskey+'_'+status;
					var title='G18'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);
					load_curpl('G18',targettype,status,bsdskey,bsdsbobrefresh,id,'no');	
				}
				if (technos.indexOf('U9')!=-1){	
					var targettype='U9'+'_'+bsdskey+'_'+status;	
					var title='U9'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);		
					load_curpl('U9',targettype,status,bsdskey,bsdsbobrefresh,id,'no');
				}
				if (technos.indexOf('U21')!=-1){	
					var targettype='U21'+'_'+bsdskey+'_'+status;
					var title='U21'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);		
					load_curpl('U21',targettype,status,bsdskey,bsdsbobrefresh,id,'no');	
				}
				if (technos.indexOf('L8')!=-1){	
					var targettype='L8'+'_'+bsdskey+'_'+status;
					var title='L8'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);		
					load_curpl('L8',targettype,status,bsdskey,bsdsbobrefresh,id,'no');	
				}
				if (technos.indexOf('L18')!=-1){	
					var targettype='L18'+'_'+bsdskey+'_'+status;
					var title='L18'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);		
					load_curpl('L18',targettype,status,bsdskey,bsdsbobrefresh,id,'no');	
				}
				if (technos.indexOf('L26')!=-1){	
					var targettype='L26'+'_'+bsdskey+'_'+status;
					var title='L26'+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
					$('#siteTabs').addtab(targettype,'glyphicon-book',title);	
					load_curpl('L26',targettype,status,bsdskey,bsdsbobrefresh,id,'no');
				}
			}else{

				var title=clicked_tab+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
				$('#siteTabs').addtab(targettype,'glyphicon-book',title);
				$('#'+targettype).append('<div id="'+targettype+clicked_tab+'"></div>');
				load_curpl(clicked_tab,targettype,status,bsdskey,bsdsbobrefresh,id,'no');
			}
			
		}else if(clicked_tab==="CHANGEID"){
			var idnr = $(this).data('idnr');
			var siteid=$('#bsdsdetails_'+id+' input[name="siteid"]').val();
			var candidate = $('#bsdsdetails_'+id+' input[name="candidate"]').val();

			$.post("scripts/general_info/general_info_actions.php", { 
				IDNR:idnr,
				BSDSKEY:bsdskey,
				action:'change_bsds_funding_id',
				candidate:candidate,
				siteid:siteid
			}, function(data){
				Messenger().post({
				  message: data,
				  showCloseButton: true
				});
				$('#bsdsicon'+siteid).click();
			});
			
		}else if(clicked_tab==="REMOVEFUNDING"){
			var upgnr=$(this).data('upgnr');
			var net1date=$(this).data('net1date');
			var vraag=confirm('Click Ok if you want to remove funding date ('+net1date+') for '+upgnr+' for BSDS with ID "'+bsdskey+'"?');
			if (vraag){	
				$.post("scripts/general_info/general_info_actions.php", { 
					action:"remove_funding",
					id: id,
					net1date : net1date,
					status: status,
					upgnr : upgnr
				},function(data){
					Messenger().post({
					  message: 'The funding date has been temporarly removed!',
					  type: 'info',
					  showCloseButton: true
					});
				})
			}
		}else{
			alert('NOT YET PROGRAMMED');
		}	
	});	

	$("body").on("click","#bsds-print",function(e ){
		var targettype = $(this).data('id');
		var title = $(this).data('title');
		//$('#bsds-print').hide();
		$('#'+targettype).printArea({pageTitle:title});
	});	
/*
	$("body").on("mouseover",".teamleader_select",function(e){
		var siteid=$(this).data('siteid');
		$(this).editable({ 
		    source: [
	              {value: 'Pending', text: 'Pending'},
	              {value: 'Declined', text: 'Declined'},
	              {value: 'Accepted', text: 'Accepted'},
		           ],
		    url : "scripts/general_info/general_info_actions.php",
		    params: function(params) {
				params.action= "change_teamlacc";
			    return params;
			},
			success:function(response, value) {
				Messenger().post({
				  message: 'RADIOPLANNER ACCEPTACNCE has been changed to: '+value+'<br>',
				  showCloseButton: false
				});
				//alert(siteid);
				$('#bsdsicon'+siteid).click();
			}
		});
	});	
*/
	function submitCurPl(techno,viewtype){
		var options = { 
			beforeSubmit:  function() { 
				$('#spinner').spin('large');
			},
 			success:  function(response) { 
				$('#spinner').spin(false);	
				var response = $.parseJSON(response);	
				var type=response.responsetype;		

				Messenger().post({
					  message: response.responsedata,
					  type: response.responsetype,
					  showCloseButton: true
					});

			}
 		}
		$('#current_planned_form'+techno+viewtype).ajaxSubmit(options); 
		return false; 
	};	

	$("body").on("click",".subCurPl",function(e){
		e.preventDefault();
		var techno=$(this).data('techno');
		var viewtype=$(this).data('viewtype');
		submitCurPl(techno,viewtype);
		return false; 
	});

	$("body").on("click","#multioverrideButton",function(e){ //2 upgrades at the same time override
		e.preventDefault();
		var options = { 
			beforeSubmit:  function() { 
				$('#spinner').spin('large');
			},
 			success:  function(response) { 
				$('#spinner').spin(false);
				var response = $.parseJSON(response);	
				var type=response.responsetype;
				Messenger().post({
				  message: response.data,
				  type:  response.type,
				  showCloseButton: false
				}); 		
			}
 		}
		$('#multioverride').ajaxSubmit(options); 
		return false; 	
	});
	$("body").on("click",".leftArrow",function( e ){
	  var scrollband=$(this).data('srollband');
	  var leftPos = $('#'+scrollband).scrollLeft();
	  $('#'+scrollband).animate({scrollLeft: leftPos - 250}, 800);
	});
	$("body").on("click",".rightArrow",function( e ){
      var scrollband=$(this).data('srollband');
	  var leftPos = $('#'+scrollband).scrollLeft();
	  $('#'+scrollband).animate({scrollLeft: leftPos + 250}, 800);
	});
	
/****************************************************************************************
* TRACKING
*****************************************************************************************/

	$("body").on("click",".tracknav",function( event ){
		var action=$(this).attr('id');
		
		if (action=="delete"){
			event.preventDefault();
			var trackid=$(this).data('trackid');
			var msg;

				msg = Messenger().post({
				  message: 'Are u sure you want to DELETE comment with ID '+trackid+'?',
				  type: 'info',
				  actions: {
				    ok: {
				      label: "I'm sure",
				      action: function() {
				      	msg.cancel();
				      	Messenger().run({
						  errorMessage: "This did not go well."
						}, {
						  url: "scripts/tracking/tracking_actions.php",
						  data: { action:"update_track",
				        	trackid:trackid, type:'1' },
				          type:'POST',
						  success: function(response) {
							$('#trackLine'+trackid).fadeOut( "slow" );
							return response;	
						  }
						});	        
				      }
				    },
				    cancel: {
				      label: 'cancel',
				      action: function() {
						return msg.cancel(); 
				      }
				    }
				  }
				});

		}else if (action=="history"){
			event.preventDefault();
			var trackid=$(this).data('trackid');
			var msg;

				msg = Messenger().post({
				  message: 'Are u sure you want put ID '+trackid+' as history?',
				  type: 'info',
				  actions: {
				    ok: {
				      label: "I'm sure",
				      action: function() {
				      	msg.cancel();
				      	Messenger().run({
						  errorMessage: "This did not go well."
						}, {
						  url: "scripts/tracking/tracking_actions.php",
						  data: { action:"update_track",
				        	trackid:trackid, type:'2' },
				          type:'POST',
						  success: function(response) {
							$('#trackLine'+trackid).fadeOut( "slow" );
							return response;	
						  }
						});	        
				      }
				    },
				    cancel: {
				      label: 'cancel',
				      action: function() {
						return msg.cancel(); 
				      }
				    }
				  }
				});

		}
	});	

	$("body").on("mouseover",".editableTracking",function(e){
		var select_id = $(this).data('id');
		$(this).editable({
			url : 'scripts/tracking/tracking_actions.php',
		    rows: 7,
		    title: 'Enter comments',
		    placement:'bottom',
		    params: function(params) {
				params.action= "updateComments";
				params.id= select_id;
			    return params;
			}
  		});
	});


/**********************
SAVEMODAL actions
**********************/
	function after_BSDSsave(response){ 
		if (response.type==='info'){					
			Messenger().post({
				  message:  response.msg
				});
			$('#bsdsicon'+response.site).click();
		}
	}	
	
	var optionsBSDS = {
		success: after_BSDSsave,
		dataType:  'json'
	};

	function after_LOS_save(response){ 

		if (response.responsetype === "info") {
		 	if (response.responseaction=="new"){
		 		Messenger().post({
				  message:  response.responsedata,
				  type: 'info',
				  showCloseButton: true
				});

				$('#myModal').modal('hide');
				$('#losicon').click();
			}else if (response.responseaction=="update"){
				Messenger().post({
				  message:  response.responsedata,
				  type: 'info',
				  showCloseButton: true
				});	
			}
		}else if (response.responsetype==="error" || response.responsetype==="warning"){
			if (response.responseaction==='update'){ 
				Messenger().post({
				  message:  response.responsedata,
				  type: 'error'
				});
			}else if (response.responseaction==='new'){ 
				Messenger().post({
				  message:  response.responsedata,
				  type: 'error'
				});
				return false;
			}
		}
	}
	function validateNewLos(formData, jqForm, options){ 
		var form = jqForm[0];
		var msg="";
		if (form.SITEA.value===""){
			msg=msg+"You need to provide SITE A!<br>";	
		}
		if (form.SITEB.value===""){
			msg=msg+"You need to provide SITE B!<br>";	
		}
		if (form.PRIORITY.value==="Please select"){
			msg=msg+"You need to select a priority!<br>";	
		}
		if (form.PARTNERVIEW.value==="Please select"){
			msg=msg+"You need to select if you want to display the LOS to the partners!<br>";	
		}
		if (form.TYPE.value==="Please select"){
			msg=msg+"You need to select a TYPE!<br>";	
		}
		
		if (msg!=''){
			Messenger().post({
				message: msg,
			  type: 'error',
			  showCloseButton: false
			});
			return false;
		}else{
			return true;
		}
	}	
	var optionsLosnew = {   
    	success:  after_LOS_save,
		dataType:  'json',
		beforeSubmit: validateNewLos 
	};	

	function after_losReject(response)  {
		$('#myModal').modal('hide');
		$('.modal-backdrop').remove();
		$(".modaldismiss" ).show();
		var rmessage = response.rmessage;
		Messenger().post({
					  message: rmessage,
					  showCloseButton: true
					});
	}
	var optionsLosreject = {
    	success:  after_losReject,
		dataType:  'json'
	};

	function after_RAFdetails_save(response){ 
		Messenger().post({
			message: response.responsedata,
			 type: response.responsetype,
			 showCloseButton: true
		});
		if (response.responsetype=="info"){
			$('#spinner').spin(false);
			if(response.typeupdate=="insert"){
				$('#myModal').modal('hide');
				$('#raficon'+response.siteID).click();
			}
			return true;
		}else{
			return false;
		}
	}	
	function validateNewRaf(formData, jqForm, options){
		var form = jqForm[0];
		var msg='';
		if (form.justification.value === "") {
			msg=msg+"JUSTIFICATION can not be empty!<br>";	
		}
		if ($('#inputRFINFO').val() === "") {
			msg=msg+"RFINFO can not be empty! Please select NA if not applicable.<br>";	
		}
		if ($('#inputCOMMERCIAL').val() === "") {
			msg=msg+"COMMERCIAL can not be empty! Please select NA if not applicable.<br>";	
		}
		if (form.sitenum.value === "") {
			msg=msg+"SITEID can not be empty! <br>";	
		}
		if ((form.type.value==="Replacement Request" || form.type.value==="New Replacement" || form.type.value==="Move Request" || form.type.value==="New Move") && form.candidate.value === ""){
			msg=msg+"For a replacement/move you need to provide a CANDIDATE! <br>";	
		}
		if (msg!=''){
			Messenger().post({
			  message: msg,
			  type: 'error',
			  showCloseButton: true
			});
			return false;
		}else{
			$('#spinner').spin('large');
			return true;
		}
	}
	var optionsRafnew = {
		success: after_RAFdetails_save,
		beforeSubmit: validateNewRaf,
		dataType:  'json'
	};

	function validateDeleteRaf(formData, jqForm, options){
		var form = jqForm[0];
		var msg='';
		if (form.delreason.value === "") {
			msg=msg+"You need to provide a reason!<br>";	
		}
		if (msg!=''){
			Messenger().post({
				message: msg,
			  type: 'error',
			  showCloseButton: true
			});
			return false;
		}else{
			$('#spinner').spin('large');
			return true;
		}
	}
	function after_DeleteRaf(response){ 
		Messenger().post({
			message: response.msg,
			 type: response.type,
			 showCloseButton: true
		});
		if (response.type=="info"){
			$('#spinner').spin(false);
			$('#myModal').modal('hide');
			$('#raficon'+response.siteID).click();
			return true;
		}else{
			return false;
		}
	}
	var optionsDeleteRaf = {
		success: after_DeleteRaf,
		beforeSubmit: validateDeleteRaf,
		dataType:  'json'
	};

	function after_ReasonForm_save(response)  {
		Messenger().post({
			message: response.data,
			 type: response.type,
			 showCloseButton: true
		});
		if (response.type=="info"){
			$('#spinner').spin(false);
			$("#savemodal").removeClass("disabled");
			$('#myModal').modal('hide');
			$('#raficon'+$('#rafSiteID').val()).click();
			return true;
		}else{
			return false;
		}
	}
	function validate_ReasonForm(formData, jqForm, options){
	    var form = jqForm[0];
		var msg='';
		if (form.value.value === "") {
			msg=msg+"You need to provide a reason!<br>";	
		}
		if (msg!=''){
			Messenger().post({
				message: msg,
			  type: 'error',
			  showCloseButton: true
			});
			return false;
		}else{
			$('#spinner').spin('large');
			$("#savemodal").addClass("disabled");
			return true;
		}
	}

	var optionsRejectRaf = {
    	success:  after_ReasonForm_save,
		dataType:  'json',
		beforeSubmit: validate_ReasonForm
	};

	$("#myModal").on("click", ".modaldismiss", function(event){
	  	event.preventDefault();
	  	$('#myModal').modal('hide');
	  	$("#savemodal").text("Save changes");
	  	$("#savemodal").show();
		$("#myModalDialog").removeClass("modalmedium");	
	});

	$("body").on("click","#savemodal",function( e ){
		var module=$(this).data('module');
		e.preventDefault();
		if (module=="losnew"){
			$('#new_los_form').ajaxSubmit(optionsLosnew);
		}else if (module=="rafnew"){
			$('#new_raf_form').ajaxSubmit(optionsRafnew);
		}else if (module=="rafdelete"){
			$('#del_raf_form').ajaxSubmit(optionsDeleteRaf);
		}else if (module=="rafreject"){
			$('#Raf_Reject_form').ajaxSubmit(optionsRejectRaf);	
		}else if (module=="bsdsNew"){
			$('#myModal').modal('hide');
			$('#new_bsds_form').ajaxSubmit(optionsBSDS);
		}else if (module=="losreject"){
			var losid=$(this).data('id');
			$('#Reject_form'+losid).ajaxSubmit(optionsLosreject);
		}else if (module=="rafprint" || module=="losprint"){
			$('.printThis').printArea();
			//reset the button to save changes
			$("#savemodal").show();
		}else{
			alert('Infobase bug. Hit F5 to retry');
		}
	    return false;
	});	
});


$.fn.scroller = function(tableid,cols) {

	var $table = $('#'+tableid);
	var tablePos = $table.position();
    //Make a clone of our table
    var $fixedColumn = $table.clone().addClass('fixed-column').attr('id','clone_'+tableid);
   
	$fixedColumn.find('col').not('col:nth-child(-n+'+cols+')').remove();
	$fixedColumn.find('th.special').not('th:nth-child(1)').remove();
	$fixedColumn.find('th').not('th:nth-child(-n+'+cols+')').remove();
	$fixedColumn.find('td').not('td:nth-child(-n+'+cols+')').remove();

    //Match the height of the rows to that of the original table's
    $fixedColumn.find('tr').each(function (i, elem) {
        $(this).height($table.find('tr:eq(' + i + ')').height());
    });

    // Set positioning so that cloned table overlays
        // first column of original table
        $fixedColumn.css({
            'left': tablePos.left,
            'top': tablePos.top,
            'position': 'absolute',
            'display': 'inline-block',
    		'width': 'auto',
    		'background-color':'#FFF',
    		'border-right': '1px solid #ddd',
    		'z-index' : 999
        
        });
        //fixedCol.find('th,td').eq(0).css('width',fixedWidthCol1+'px');
        $($fixedColumn).insertBefore('#'+tableid);
};

$.fn.addtab = function(target,iconImg,title) {
	$("#siteTabs li.active").removeClass('active');
	$(".tab-pane").removeClass('active');

  	if ($("#"+target).length == 0){
    	$("#siteTabs").append('<li class="active" id="tab_'+target+'"><a href="#'+target+'"  data-toggle="tab"><span class="glyphicon '+iconImg+'"></span> '+title+'</a></li>');
		$("#contentTabs").append('<div class="tab-pane active" id="'+target+'">...</div>');
	}
	$("#tab_"+target).addClass('active');
	$("#"+target).addClass('active');   
};

$.fn.spin = function(opts, color) {
	var presets = {
		"tiny": { lines: 7, length: 1, width: 1, radius: 2 },
		"small": { lines: 8, length: 4, width: 3, radius: 5 },
		"large": { lines: 10, length: 8, width: 4, radius: 8 }
	};
	if (Spinner) {
		return this.each(function() {
			var $this = $(this),
				data = $this.data();

			if (data.spinner) {
				data.spinner.stop();
				delete data.spinner;
			}
			if (opts !== false) {
				if (typeof opts === "string") {
					if (opts in presets) {
						opts = presets[opts];
					} else {
						opts = {};
					}
					if (color) {
						opts.color = color;
					}
				}
				data.spinner = new Spinner($.extend({color: $this.css('color')}, opts)).spin(this);
			}
		});
	} else {
		throw "Spinner class not available.";
	}
};

function load_curpl_BIPT(targettype,status,bsdskey,bsdsbobrefresh,id){
	var bsdsdata=$('#bsdsform_'+id+' input[name="bsdsdata"]').val();
	var siteid=$('#bsdsform_'+id+' input[name="siteid"]').val();
	var candidate = $('#bsdsform_'+id+' input[name="candidate"]').val();
	var donor = $('#bsdsform_'+id+' input[name="donor"]').val();
	var technos = $('#bsdsform_'+id+' input[name="technos"]').val();

	var lognodeID_GSM = $('#viewers_'+siteid+' input[name="lognodeID_GSM"]').val();			
	var lognodeID_UMTS2100 = $('#viewers_'+siteid+' input[name="lognodeID_UMTS2100"]').val();	
	var lognodeID_UMTS900 = $('#viewers_'+siteid+' input[name="lognodeID_UMTS900"]').val();
	var lognodeID_LTE800 = $('#viewers_'+siteid+' input[name="lognodeID_LTE800"]').val();
	var lognodeID_LTE1800 = $('#viewers_'+siteid+' input[name="lognodeID_LTE1800"]').val();
	var lognodeID_LTE2600 = $('#viewers_'+siteid+' input[name="lognodeID_LTE2600"]').val();
	var technosAsset = $('#viewers_'+siteid+' input[name="technos"]').val();

	if (status!=='PRE'){
		if (technos.indexOf('G9')!=-1){
			var loadstatus = status;
		}else{
			var loadstatus = 'PRE';
		}
	}else{ //PRE DATA
		var loadstatus = 'PRE';			
	}

	if(typeof donor !== 'undefined' && donor !==''){ 
		alert('BIPT module not yet available for repeaters');
	}else{
		var link="scripts/current_planned/bipt_output.php";
	}

	$.ajax({
		type: "POST",
		url: link,
		data: { 
				siteID:siteid,
				candidate:candidate,
				bsdskey:bsdskey,
				bsdsdata:bsdsdata,
				bsdsbobrefresh:bsdsbobrefresh,
				lognodeID_GSM: lognodeID_GSM,
				lognodeID_UMTS2100: lognodeID_UMTS2100,
				lognodeID_UMTS900: lognodeID_UMTS900,
				lognodeID_LTE800: lognodeID_LTE800,
				lognodeID_LTE1800: lognodeID_LTE1800,
				lognodeID_LTE2600: lognodeID_LTE2600,
				status: loadstatus,
				donor:donor
		},
		success : function(data){
			$('#spinner').spin(false); 
			$('#'+targettype).append(data).slideDown();
		     
		},
		beforeSend: function ( xhr ) {
			$('#spinner').spin(); 
		}
	});	

}

function load_curpl(band,targettype,status,bsdskey,bsdsbobrefresh,id,print){

	var bsdsdata=$('#bsdsdetails_'+id+' input[name="bsdsdata"]').val();
	var siteid=$('#bsdsdetails_'+id+' input[name="siteid"]').val();
	var candidate = $('#bsdsdetails_'+id+' input[name="candidate"]').val();
	var donor = $('#bsdsdetails_'+id+' input[name="donor"]').val();
	var technos = $('#bsdsdetails_'+id+' input[name="technos"]').val();

	var lognodeID_GSM = $('#viewers_'+siteid+' input[name="lognodeID_GSM"]').val();			
	var lognodeID_UMTS2100 = $('#viewers_'+siteid+' input[name="lognodeID_UMTS2100"]').val();	
	var lognodeID_UMTS900 = $('#viewers_'+siteid+' input[name="lognodeID_UMTS900"]').val();
	var lognodeID_LTE800 = $('#viewers_'+siteid+' input[name="lognodeID_LTE800"]').val();
	var lognodeID_LTE1800 = $('#viewers_'+siteid+' input[name="lognodeID_LTE1800"]').val();
	var lognodeID_LTE2600 = $('#viewers_'+siteid+' input[name="lognodeID_LTE2600"]').val();
	var technosAsset = $('#viewers_'+siteid+' input[name="technos"]').val();
	
	if (status!=='PRE'){
		//alert(status+' techAsset'+technosAsset+ ' b'+band+' '+technosAsset.indexOf(band)+' technos: '+technos);
		if (technos.indexOf(band)!=-1){
			var loadstatus = status;
		}else{
			var loadstatus = 'PRE';
		}
	}else{ //PRE DATA
		var loadstatus = 'PRE';			
	}

	if(typeof donor !== 'undefined' && donor !== ''){ 
		var link="scripts/current_planned/current_planned_repeater.php";
	}else if(band==="G9" || band==="G18"){
		var link="scripts/current_planned/current_planned_GSM_output.php";
	}else if(band==="U9" || band==="U21" || band==="L18" || band==="L26" || band==="L8"){
		var link="scripts/current_planned/current_planned_UMTS_output.php";
	}	

	var bsdsbobrefreshDiv=str_replace(':', '', bsdsbobrefresh);
	bsdsbobrefreshDiv=str_replace('/', '', bsdsbobrefreshDiv);
	bsdsbobrefreshDiv=str_replace(' ', '', bsdsbobrefreshDiv);
	if (bsdsbobrefreshDiv=='PRE'){
		var theDiv = bsdskey+band+loadstatus;
	}else{
		var theDiv = bsdskey+band+loadstatus+bsdsbobrefreshDiv;
	}
	//console.log(theDiv);
	if (print!=="yes"){			
		$.ajax({
			type: "POST",
			url: link,
			data: { 
				siteID:siteid,
				candidate:candidate,
				band:band,
				bsdskey:bsdskey,
				bsdsdata:bsdsdata,
				bsdsbobrefresh:bsdsbobrefresh,
				lognodeID_GSM: lognodeID_GSM,
				lognodeID_UMTS2100: lognodeID_UMTS2100,
				lognodeID_UMTS900: lognodeID_UMTS900,
				lognodeID_LTE800: lognodeID_LTE800,
				lognodeID_LTE1800: lognodeID_LTE1800,
				lognodeID_LTE2600: lognodeID_LTE2600,
				status: loadstatus,
				print: print,
				donor:donor
			},
			success : function(data){
			    $('#'+targettype).html(data); 

			    $(".dynamic").each(function(){
			    	//var selection=$(this).select2('data').text;
			   		if (this.id.substr(0,3)=='pl_'){
			   			var plval=$('#'+this.id).val();
			   			right= this.id.split('pl_');
		  				idname =right[1];
			   			var curval=$('#nocurpl_'+theDiv+' #nocur_'+idname).val();
			   			if (curval!==plval){
				    		$(this).parent("td").addClass("notsame");
				    	}
			   		}
			   	});

			    $('#nocurpl_'+theDiv+" .tabledata").each(function (elem) {
			    	var name =this.name;
    //console.log(name+ ":"+curval+" - "+plval);
			    	if (name.substr(0,3)=='pl_'){
			    	
				    	right= name.split('pl_');
		  				idname =right[1];
		  				var curval=$('#nocurpl_'+theDiv+' #nocur_'+idname+band).val();
		  				var plval=this.value;
		  				if (curval=='NA' || curval=='0'|| curval=='00' || curval=='NONE' || curval=='NO') curval='';
		  				if (plval=='NA' || plval=='0'|| plval=='00' || plval=='NONE' || plval=='NO') plval='';
		  				
				    	if (curval!==plval){
				    		$(this).parent("td").addClass("notsame");
				    	}				    			    	
				    }
				}); 

				//$(document).lazyloadScript('scripts/current_planned/current_planned.js',function(){},function(){});	
				/*jQuery.getScript( 'scripts/current_planned/current_planned.js', function(data)
				      {
				        //my_lazy_loader_loaded_files.push(filename);
				      });*/
				forceResponsiveTables('bsds'+candidate+band);
			    $('#spinner').spin(false);      
			},
			beforeSend: function ( xhr ) {
				$('#spinner').spin();
			}
		});
	}else{
		$.ajax({
			type: "POST",
			url: link,
			data: { 
				siteID:siteid,
				band:band,
				bsdskey:bsdskey,
				bsdsdata:bsdsdata,
				bsdsbobrefresh:bsdsbobrefresh,
				lognodeID_GSM: lognodeID_GSM,
				lognodeID_UMTS2100: lognodeID_UMTS2100,
				lognodeID_UMTS900: lognodeID_UMTS900,
				lognodeID_LTE800: lognodeID_LTE800,
				lognodeID_LTE1800: lognodeID_LTE1800,
				lognodeID_LTE2600: lognodeID_LTE2600,
				status: loadstatus,
				print: print
			},
			success : function(data){
			   	$('#'+targettype).append(data).slideDown(); 
	

					$('#yescurpl_'+theDiv+" .tabledata").each(function(){
				    	var name =this.name;
				    	var id=this.id;	

				    //The planned value		    	
				    	var plval=$(this).val();
				    	//console.log(idname+'name='+name+'/'+id+'pl:'+plval+'/'+band+'/cur:'+curval);
						
						if (id.substr(0,3)=='pl_'){ 
					    	right= id.split('pl_');
				  			idname =right[1];
				  			
				  		//The current value
					    	var curval=$('#yescur_'+idname).val();  	
						    if (curval!==plval){	
						    	$('#yescurpl_'+theDiv+' #pl_'+idname).replaceWith("<span class='notsame'>" + plval+ "</span>");
						    }else{
						    	$('#yescurpl_'+theDiv+' #pl_'+idname).replaceWith("<span class='same'>" + plval+ "</span>");
						    }			    	
						}else{
						    var currentVal=$('#yescurpl_'+theDiv+' #'+id).val();   	
						    $('#yescurpl_'+theDiv+' #'+id).replaceWith("<span>" + currentVal+ "</span>");			    	
						   }
						
					});	
					$('#yescurpl_'+theDiv+" .dynamic").each(function(){
				    	//var selection=$(this).select2('data').text;
				   		if (this.id.substr(0,3)=='pl_'){
				   			var plval=$('#'+this.id).val();
				   			right= this.id.split('pl_');
			  				idname =right[1];
				   			var curval=$('#nocurpl_'+theDiv+' #nocur_'+idname).val();
					    	if (curval!==plval){	
						    	$(this).replaceWith("<span class='notsame'>" + plval+ "</span>");
						    }else{
						    	$(this).replaceWith("<span class='same'>" + plval+ "</span>");
						    }
				   		}
				   	});
					
			    $('#spinner').spin(false);      
			},
			beforeSend: function ( xhr ) {
				$('#spinner').spin();
			}
		});
	}
}


function load_curpl2(band,targettype,loadstatus,bsdskey,bsdsbobrefresh,id,print,reloadAsset){

	var bsdsdata=$('#bsdsform_'+id+' input[name="bsdsdata"]').val();
	var siteid=$('#bsdsform_'+id+' input[name="siteid"]').val();
	var candidate = $('#bsdsform_'+id+' input[name="candidate"]').val();
	var donor = $('#bsdsform_'+id+' input[name="donor"]').val();
	var rafid = $('#bsdsform_'+id+' input[name="rafid"]').val();
	var disable_save=$('#bsdsform_'+id+' input[name="disable_save"]').val();

	var lognodeID_GSM = $('#viewers_'+siteid+' input[name="lognodeID_GSM"]').val();			
	var lognodeID_UMTS2100 = $('#viewers_'+siteid+' input[name="lognodeID_UMTS2100"]').val();	
	var lognodeID_UMTS900 = $('#viewers_'+siteid+' input[name="lognodeID_UMTS900"]').val();
	var lognodeID_LTE800 = $('#viewers_'+siteid+' input[name="lognodeID_LTE800"]').val();
	var lognodeID_LTE1800 = $('#viewers_'+siteid+' input[name="lognodeID_LTE1800"]').val();
	var lognodeID_LTE2600 = $('#viewers_'+siteid+' input[name="lognodeID_LTE2600"]').val();
	var technosAsset = $('#viewers_'+siteid+' input[name="technos"]').val();
	
	if(typeof donor !== 'undefined' && donor !== ''){ 
		var link="scripts/current_planned2/current_planned_repeater.php";
	}else if(band==="G9" || band==="G18"){
		var link="scripts/current_planned2/current_planned_GSM_output.php";
	}else if(band==="U9" || band==="U21" || band==="L18" || band==="L26" || band==="L8"){
		var link="scripts/current_planned2/current_planned_UMTS_output.php";
	}	

	var bsdsbobrefreshDiv=str_replace(':', '', bsdsbobrefresh);
	bsdsbobrefreshDiv=str_replace('/', '', bsdsbobrefreshDiv);
	bsdsbobrefreshDiv=str_replace(' ', '', bsdsbobrefreshDiv);
	if (bsdsbobrefreshDiv=='PRE'){
		var theDiv = bsdskey+band+loadstatus;
	}else{
		var theDiv = bsdskey+band+loadstatus+bsdsbobrefreshDiv;
	}
//alert(loadstatus);
	//console.log(theDiv);
	if (print!=="yes"){			
		$.ajax({
			type: "POST",
			url: link,
			data: { 
				siteID:siteid,
				candidate:candidate,
				band:band,
				bsdskey:bsdskey,
				bsdsdata:bsdsdata,
				bsdsbobrefresh:bsdsbobrefresh,
				lognodeID_GSM: lognodeID_GSM,
				lognodeID_UMTS2100: lognodeID_UMTS2100,
				lognodeID_UMTS900: lognodeID_UMTS900,
				lognodeID_LTE800: lognodeID_LTE800,
				lognodeID_LTE1800: lognodeID_LTE1800,
				lognodeID_LTE2600: lognodeID_LTE2600,
				status: loadstatus,
				print: print,
				rafid:rafid,
				donor:donor,
				disable_save:disable_save,
				bsdsformid:id,
				reloadAsset:reloadAsset
			},
			success : function(data){
			    $('#'+targettype).html(data); 
			 //Highlight the difference between current and planned
			    $(".dynamic").each(function(){
			   		if (this.id.substr(0,3)=='pl_'){
			   			var plval=$('#'+this.id).val();
			   			right= this.id.split('pl_');
		  				idname =right[1];
			   			var curval=$('#nocurpl_'+theDiv+' #nocur_'+idname).val();
			   			if (curval!==plval){
				    		$(this).parent("td").addClass("notsame");
				    	}
			   		}
			   	});
			    $('#nocurpl_'+theDiv+" .tabledata").each(function (elem) {
			    	var name =this.name;
					//console.log(name+ ":"+curval+" - "+plval);
			    	if (name.substr(0,3)=='pl_'){
			    	
				    	right= name.split('pl_');
		  				idname =right[1];
		  				var curval=$('#nocurpl_'+theDiv+' #nocur_'+idname+band).val();
		  				var plval=this.value;
		  				if (curval=='NA' || curval=='0'|| curval=='00' || curval=='NONE' || curval=='NO') curval='';
		  				if (plval=='NA' || plval=='0'|| plval=='00' || plval=='NONE' || plval=='NO') plval='';
		  				
				    	if (curval!==plval){
				    		$(this).parent("td").addClass("notsame");
				    	}				    			    	
				    }
				}); 

				forceResponsiveTables('bsds'+candidate+loadstatus+band);
			    $('#spinner').spin(false);      
			},
			beforeSend: function ( xhr ) {
				$('#spinner').spin();
			}
		});
	}else{
		$.ajax({
			type: "POST",
			url: link,
			data: { 
				siteID:siteid,
				band:band,
				bsdskey:bsdskey,
				bsdsdata:bsdsdata,
				bsdsbobrefresh:bsdsbobrefresh,
				lognodeID_GSM: lognodeID_GSM,
				lognodeID_UMTS2100: lognodeID_UMTS2100,
				lognodeID_UMTS900: lognodeID_UMTS900,
				lognodeID_LTE800: lognodeID_LTE800,
				lognodeID_LTE1800: lognodeID_LTE1800,
				lognodeID_LTE2600: lognodeID_LTE2600,
				status: loadstatus,
				rafid:rafid,
				print: print
			},
			success : function(data){
			   	$('#'+targettype).append(data).slideDown(); 
	

					$('#yescurpl_'+theDiv+" .tabledata").each(function(){
				    	var name =this.name;
				    	var id=this.id;	

				    //The planned value		    	
				    	var plval=$(this).val();
				    	//console.log(idname+'name='+name+'/'+id+'pl:'+plval+'/'+band+'/cur:'+curval);
						
						if (id.substr(0,3)=='pl_'){ 
					    	right= id.split('pl_');
				  			idname =right[1];
				  			
				  		//The current value
					    	var curval=$('#yescur_'+idname).val();  	
						    if (curval!==plval){	
						    	$('#yescurpl_'+theDiv+' #pl_'+idname).replaceWith("<span class='notsame'>" + plval+ "</span>");
						    }else{
						    	$('#yescurpl_'+theDiv+' #pl_'+idname).replaceWith("<span class='same'>" + plval+ "</span>");
						    }			    	
						}else{
						    var currentVal=$('#yescurpl_'+theDiv+' #'+id).val();   	
						    $('#yescurpl_'+theDiv+' #'+id).replaceWith("<span>" + currentVal+ "</span>");			    	
						   }
						
					});	
					$('#yescurpl_'+theDiv+" .dynamic").each(function(){
				    	//var selection=$(this).select2('data').text;
				   		if (this.id.substr(0,3)=='pl_'){
				   			var plval=$('#'+this.id).val();
				   			right= this.id.split('pl_');
			  				idname =right[1];
				   			var curval=$('#nocurpl_'+theDiv+' #nocur_'+idname).val();
					    	if (curval!==plval){	
						    	$(this).replaceWith("<span class='notsame'>" + plval+ "</span>");
						    }else{
						    	$(this).replaceWith("<span class='same'>" + plval+ "</span>");
						    }
				   		}
				   	});
					
			    $('#spinner').spin(false);      
			},
			beforeSend: function ( xhr ) {
				$('#spinner').spin();
			}
		});
	}
}


/* jquery plugin to scroll window to the top */
$(function() {
    var up_timer = 0;
    var viewportLeft;
    var viewportTop;
 
    $.getPosition = function() {
        viewportLeft = document.body.scrollLeft || document.documentElement.scrollLeft;
        viewportTop = document.body.scrollTop || document.documentElement.scrollTop;
    };
 
    $.pageup = function(x, y) {
        if (up_timer) {
            clearTimeout(up_timer);
        }
        if (y >= 1) {
            $.getPosition();
            var divisionY = (viewportTop - (viewportTop / 5));
            var Y = Math.floor(divisionY);
            window.scrollTo(viewportLeft, Y);
            up_timer = setTimeout("$.pageup(" + viewportLeft + "," + Y + ")", 2);
        } else {
            window.scrollTo(viewportLeft, 0);
            clearTimeout(up_timer);
        }
    };
 
    $.scrollup = function() {
        $.getPosition();
        $.pageup(viewportLeft, viewportTop);
    };
});
 
/* Usage: call $.scrollup(); to scroll to the top smoothly */

function str_replace(search, replace, subject, count) {
  //        note: The count parameter must be passed as a string in order
  //        note: to find a global variable in which the result will be given
  //   example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
  //   returns 1: 'Kevin.van.Zonneveld'
  //   example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
  //   returns 2: 'hemmo, mars'

  var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
  s = [].concat(s);
  if (count) {
    this.window[count] = 0;
  }

  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue;
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      temp = s[i] + '';
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
      s[i] = (temp)
        .split(f[j])
        .join(repl);
      if (count && s[i] !== temp) {
        this.window[count] += (temp.length - s[i].length) / f[j].length;
      }
    }
  }
  return sa ? s : s[0];
}

function getQueryParams(qs) {
    qs = qs.split("+").join(" ");

    var params = {}, tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])]
            = decodeURIComponent(tokens[2]);
    }

    return params;
}


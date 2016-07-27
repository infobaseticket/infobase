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

function scrollContent(direction,content) {
    var amount = (direction === "left" ? "-=15px" : "+=15px");
    $('#'+content).animate({
        scrollLeft: amount
    }, 1, function() {
       if (scrolling) {
            scrollContent(direction,content);
       }
    });
}

function scrollToAnchor(aid){
    var aTag = $("a[name='"+ aid +"']");
    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
}

	var hidWidth;
	var scrollBarWidths = 40;

	var widthOfList = function(){
	  var itemsWidth = 0;
	  $('.list li').each(function(){
	    var itemWidth = $(this).outerWidth();
	    itemsWidth+=itemWidth;
	  });
	  return itemsWidth;
	};

	var widthOfHidden = function(){
	  return (($('.wrapper').outerWidth())-widthOfList()-getLeftPosi())-scrollBarWidths;
	};

	var getLeftPosi = function(){
	  return $('.list').position().left;
	};

	var reAdjust = function(elem){
	  //alert($('.wrapper').outerWidth() + "----"+widthOfList());
	  if (($('#'+elem).outerWidth()) < widthOfList()) {
	    $('#right'+'subTabs').show();
	  }
	  else {
	    $('#right'+'subTabs').hide();
	  }
	  
	  if (getLeftPosi()<0) {
	    $('#left'+'subTabs').show();
	  }
	  else {
	    $('#'+elem).animate({left:"-="+getLeftPosi()+"px"},'slow');
	    $('#left'+'subTabs').hide();
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
			var load='scripts/rafreport/rafreporttable.php?module='+query.module+'&report='+query.report+'&region='+query.region+'&partner='+query.partner;	
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

		$('#SuperContent').load(load, function(){
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
				$("#displayRafform").click();
			}
		});
		$('#searchk').focus();
	}

    $.ajax({
          type: "POST",
          url: "scripts/raf/rafActionsTable.php",
          success : function(data){
              $('#ABdashbord').html(data); 
          }
    });
	
	$("#mainnavbar").on("click",".mainnav",function( e ){

		var module=$(this).attr('id');
		//alert(module);
		$('#MainsiteTabs').addCtab('MainsiteTabs','mod_'+module,'glyphicon-certificate',module);
		$('#mod_'+module).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');

		$('#spinner').spin('large');	
		$('#navbar li').removeClass("active");
		$(this).addClass("active");
		
		if (module=='rafreport'){
			var reporttype=$(this).data('reporttype');
			var url = 'scripts/'+module+'/'+module+reporttype+'.php';
		}else if (module=='roldashbord'){
			var reporttype=$(this).data('reporttype');
			var url = 'scripts/roldashbord/'+reporttype+'.php';
		}else{
			var url = 'scripts/'+module+'/'+module+'.php';
		}		
	
		$('#mod_'+module).load(url, function(){
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

	$("body").on("click","#resizesearch",function( e ){
		$("#resizesearchOutput").toggle();
		$("#resizesearch").toggleClass("glyphicon-resize-small glyphicon-resize-full");
	});


	$.xhrPool = []; // array of uncompleted requests
	$.xhrPool.abortAll = function() { // our abort function
	    $(this).each(function(idx, jqXHR) { 
	        jqXHR.abort();
	    });
	    $.xhrPool.length = 0
	};

	$.ajaxSetup({
	    beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
	        $.xhrPool.push(jqXHR);
	    },
	    complete: function(jqXHR) { // when some of the requests completed it will splice from the array
	        var index = $.xhrPool.indexOf(jqXHR);
	        if (index > -1) {
	            $.xhrPool.splice(index, 1);
	        }
	    }
	});

	/*
	* SEARCH FORM
	*/
	$("#SuperContent").on("submit","#searchForm",function( e ){
		$("#siteTabs li").remove();
		$("#contentTabs div").remove();
		$("#search_output").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');	
	});
	$("#SuperContent").on("click","#searchbutton",function( e ){
		
		$("#siteTabs li").remove();
		$("#contentTabs div").remove();
		$("#search_output").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
		
		var options = { 
		  target:  '#search_output',   
		  success:    function() { 
			$("#search_output").show('slow');
		  },
		  beforeSubmit: function(){

			// xhr.abort();
			$.xhrPool.abortAll();
			 
		  }  
		};			
	  	$("#searchForm").ajaxSubmit(options);  	
	   	return false; 
	});

	/*
	* EXPLORER TO RAFREPORT
	*/
	/*
	$("body").on("click",".mainnav",function( e ){
		var url=$(this).data('url');
		///alert(url);
	});
	/*
	* ICONNAV ACTIONS
	*/
	$("body").on("click",".navicon",function( e ){
		var techno='';
		if ($(this).hasClass('oss')==true){
			var viewtype='OSS';
			var url = 'scripts/explorer/site_explorer/site_explorer.php';
			var iconImg='glyphicon-tint';
		}else if ($(this).hasClass('ran')==true){
			var viewtype='RAN';
			var url = 'scripts/filebrowser/filebrowser.php';
			var iconImg='glyphicon-folder-open';
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
		}else if ($(this).hasClass('raf2')==true || $(this).hasClass('rafID')==true){
			var viewtype='RAF2';
			var url = 'scripts/raf/raf2.php';
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
		}else if ($(this).hasClass('txmn')==true){
			var viewtype='LLMW';
			var url = 'scripts/txmn/txmn.php';
			var iconImg='glyphicon-link';
		}else{
			alert('ICON CLICKED NOT PROGRAMMED');
		}

		//alert(url);
		
		if (url!=''){
			var form=$(this).closest("form");
			$('#spinner').spin('large','#5bc0de');
			var the_form_id = form.attr("id");
			var siteID = $('#'+the_form_id +' input[name="siteID"]').val();
		    var candidate = $('#'+the_form_id +' input[name="candidate"]').val();
		 	if (viewtype=='OSS'){	
		    var bypass = $('#'+the_form_id +' input[name="bypass"]').val();
			}else{
				bypass='';
			}

			if (viewtype=='EVENT'){
				var title=viewtype+' '+siteID+' '+techno;
			}else if (viewtype=='EVENT'){
				var title=siteID;
			}else{
				var title=viewtype+' '+techno;
			}
			$('#siteTabs').addCtab('siteTabs',viewtype+siteID+techno,iconImg,title); 

			$("#"+viewtype+siteID+techno).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
		 
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
					}else if (viewtype=='LOS'){		
						forceResponsiveTables('LOSTable'+siteID);
						$('#LOSTable'+siteID).scroller('LOSTable'+siteID,3);
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
* clusterdata popup
*****************************************************************************************/

$("body").on("click",".cluster",function( e ){
	var cluster=$(this).data('cluster');
	jQuery.ajax({
	    type: 'POST',
	    url: 'scripts/explorer/cluster_info.php',
	    data: { cluster: cluster },
	    success: function(data) {
	        bootbox.dialog({
	            message: data,
	            title: 'Sites in cluster '+cluster+':</h4>',
	        });
	    }
	});
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
		var rafid=$(this).data('rafid');
		var nbup=$(this).data('nbup');
		$.ajax({
			type: "POST",
			url: 'scripts/validation/validation.php',
			data: { siteupgnr:siteupgnr,rafid:rafid},
			success : function(data){
				$("#myModalDialog").addClass("modalwide");
				$("#savemodal").hide();
				$("#savemodal").addClass("disabled");
				$("#savemodal").data("module","signoff");
				$('#myModal .modal-header').html('<h4>Validation for '+rafid+':</h4>');
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

	$("body").on("click",".overruleVali",function( e ){
		var checktype=$(this).data('checktype');
		var siteupgnr=$(this).data('siteupgnr');
		var rafid=$(this).data('rafid');
		var type=$(this).data('type');

		bootbox.prompt({
		  	title: "Please provide a reason why document/MS validation is not needed?",
		  	callback: function(result) {
			    $.ajax({
					type: "POST",
					url: 'scripts/validation/validation_actions.php',
					data: { 
						checktype:checktype,
						siteupgnr:siteupgnr,
						action:'overruleValidation',
						reason:result,
						rafid: rafid,
						type:type
					},
					success : function(data){
						var response = $.parseJSON(data);
						Messenger().post({
							message: response.msg,
							showCloseButton: true,
							type: response.rtype
						});
						if (response.rtype=='success'){
							$('#'+siteupgnr+checktype).hide();
							$('#fileVal_'+siteupgnr+checktype).removeClass('danger').removeClass('warning').addClass('success');
						}
					},
					beforeSend: function ( xhr ) {
					}
				});
			}
		});
	});
	

/****************************************************************************************
* NET1 MS toggling log via Infobase
*****************************************************************************************/
	$("body").on("click",".NET1log",function( e ){
		var siteupgnr=$(this).data('siteupgnr');
		var nbup=$(this).data('nbup');
		$.ajax({
			type: "POST",
			url: 'scripts/explorer/NET1log.php',
			data: { siteupgnr:siteupgnr,nbup:nbup},
			success : function(data){
				$("#myModalDialog").addClass("modalwide");
				$("#savemodal").hide();
				$("#savemodal").addClass("disabled");
				$("#savemodal").data("module","signoff");
				$('#myModal .modal-header').html('<h4>Log for '+siteupgnr+':</h4>');
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
* Latest synced RAN_BMT files of today
*****************************************************************************************/
	$("body").on("click","#RAN_BMTlog",function( e ){
		$.ajax({
			type: "POST",
			url: 'scripts/explorer/RAN_BMTlog.php',
			success : function(data){
				$("#myModalDialog").addClass("modalwide");
				$("#savemodal").hide();
				$("#savemodal").addClass("disabled");
				$("#savemodal").data("module","signoff");
				$('#myModal .modal-header').html('<h4>Files moved today:</h4>');
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
* NET1 BROWSER 
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

	$("body").on("click",".bsds",function( e ){
		var upgnr=$(this).data('upgnr');
		var candidate=$(this).data('candidate');
		var nbup=$(this).data('nbup');
		var siteid=$(this).data('siteid');

		$.ajax({
			type: "POST",
			url: 'scripts/general_info/general_info.php',
			data: { upgnr:upgnr,candidate:candidate,nbup:nbup,siteid:siteid},
			success : function(data){
				$('#spinner').spin(false);
				if (nbup!='UPG'){
					var title='BSDS '+candidate;
					var tab=candidate;
				}else{
					var title='BSDS '+' '+upgnr;
					var tab=candidate+upgnr;
				}
				$('#siteTabs').addCtab('siteTabs','BSDS_'+tab,'glyphicon-book',title);
				$('#BSDS_'+tab).html(data);
			},
			beforeSend: function ( xhr ) {
				$('#spinner').spin();
			}
		});
	});

/****************************************************************************************
* ASSET EXPLORER
*****************************************************************************************/
	$("body").on("click",".asset",function( e ){
		var upgnr=$(this).data('upgnr');
		var candidate=$(this).data('candidate');
		var nbup=$(this).data('nbup');
		var siteid=$(this).data('siteid');

		$.ajax({
			type: "POST",
			url: 'scripts/asset_explorer/asset_explorer.php',
			data: { upgnr:upgnr,candidate:candidate,nbup:nbup,siteid:siteid},
			success : function(data){
				$('#spinner').spin(false);
				if (nbup!='UPG'){
					var title='BSDS '+candidate;
					var tab=candidate;
				}else{
					var title='BSDS '+' '+upgnr;
					var tab=candidate+upgnr;
				}
				$('#siteTabs').addCtab('siteTabs','ASSET_'+tab,'glyphicon-globe',title);
				$('#ASSET_'+tab).html(data);
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
	  '        <iframe src="'+ranurl+'" frameborder="0" height="800" width="99.6%"></iframe>' +
	  '      <div class="modal-footer">' +
	  '        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>' +
	  '      </div>' +
	  '    </div>' +
	  '  </div>' +
	  '</div>';

		$(popupTemplate).modal();
		 return false;

	});

/****************************************************************************************
* RAFACTIONSOVERVIEW
*****************************************************************************************/
	$("body").on("click","#rafActionsSubmit",function( e ){
		e.preventDefault();	
		$('#rafActionsTableoutput').html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
		var formData = {
            'partner'  : $('#partner').val(),
            'raftype'  : $('#raftype').val(),
            'cluster'  : $('#cluster').val()
        };
	  	$.ajax({
			type: "POST",
			url: "scripts/raf/rafActionsTable_data.php",
			data: formData,
			success : function(data){
			    $('#rafActionsTableoutput').html(data).fadeIn(1000); 
			}
		});
	});

	$("body").on("click",".rafreportLink",function( e ){
		$('#spinner').spin();

		var action=$(this).data('action');
		var region=$(this).data('region');
		var partner=$(this).data('partner');
		var raftype=$(this).data('raftype');
		var cluster=$(this).data('cluster');

		$.ajax({
			type: "POST",
			url: "scripts/rafreport/rafreporttable.php",
			data: {
				'report': action,
				'region': region,
				'partner': partner,
				'raftype': raftype,
				'cluster':cluster
			},
			success : function(data){
				$('#MainsiteTabs').addCtab('MainsiteTabs','RAFREPORT','glyphicon-road','RAF ACTIONS');
				$("#RAFREPORT").html(data).fadeIn(1000); 
				$("#displayRafform").click();
			}
		});
	});

/****************************************************************************************
* RAF
*****************************************************************************************/
	$("body").on("click",".rafnav",function( e ){
		e.preventDefault();

		var action=$(this).data('action');
		var rafid=$(this).data('id');	
		var siteid=$(this).data('site');
		var type=$(this).data('type');
		var file=$(this).data('file');
		
		
		if (action=="view"){

			var actiondo=$(this).data('actiondo');

			var status= $("#status-"+rafid).val();
			var raftype= $("#raftype-"+rafid).val();
			var saveAllowed=$("#saveAllowed-"+rafid).val();
			var bufferchangeAllowed=$("#bufferchangeAllowed-"+rafid).val();
			var CON_PARTNER=$("#CON_PARTNER-"+rafid).val();

			$("#RAFcontent"+siteid+rafid).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
			//$('#RAFcontent').empty();
			$('#selected_rafID').empty();
			$('#modalspinner').spin('small');
			

			if (file!=''){
				viewfile='raf_details_'+file;
			}else{
				viewfile='raf_details_other';
			}

			$('#rafdetails'+siteid+rafid).modal();
			$('#RAFcontent'+siteid+rafid).load('scripts/raf/'+viewfile+'.php', {
		  		status: status,
		  		saveAllowed : saveAllowed,
		  		bufferchangeAllowed : bufferchangeAllowed,
		  		raftype: raftype,
		  		type:type,
		  		rafid: rafid,
		  		CON_PARTNER:CON_PARTNER,
		  		siteid: siteid,
		  		actiondo:actiondo
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
		}else if (action=="txmodupgrade"){

			jQuery.ajax({
			    type: 'POST',
			    url: 'scripts/raf/raf_details_other.php',
			    data: { master_rafid:rafid },
			    success: function(data) {
			        bootbox.dialog({
			            message: data,
			            title: '<h4>Create new MOD TX Upgrade RAF for '+rafid+':</h4>',
			             buttons: {
				            success: {
				                label:"Create MOD TX Upgrade",
				                className: "btn-success",
				                callback: function() {								
							        
									$('#new_raf_form').ajaxSubmit(optionsRafnew);
							    }
				            }
				        }
			        });
			    },
			   
			});
		}else if (action=="delete_raf" || action=="undelete_raf" || action=="unlock_raf" || action=="lock_raf"){
			e.preventDefault();
			var rafid=$(this).data('id');
			var site=$(this).data('site');
			var net1link=$(this).data('net1link');
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
							data: { rafid:rafid, siteID:site, action:action,net1link:net1link},
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
		}else if (action=="net1explorer"){
			e.preventDefault();
 			var net1link=$(this).data('net1link');
			var siteID =$(this).data('siteid');
			$('#siteTabs').addCtab('siteTabs','NET1_'+net1link,'glyphicon-th-large','NET1 '+net1link);
			$.ajax({
				type: "POST",
				url: 'scripts/net1/MSexplorer.php',
				data: { net1link:net1link,siteID:siteID}, 
				success : function(data){
					$('#spinner').spin(false);
					$("#"+'NET1_'+net1link).html(data);
					forceResponsiveTables('NET1NB'+siteID);
					$('#NET1NB'+siteID).scroller('NET1NB'+siteID,4);
					forceResponsiveTables('NET1UPG'+siteID);
					$('#NET1UPG'+siteID).scroller('NET1UPG'+siteID,4);
				},
				beforeSend: function ( xhr ) {
					$('#spinner').spin();
				}
			});
		}
	});	

	$("body").on("click",".scrollto",function( event ){
		var action=$(this).data('action');
		var siteid=$(this).data('siteid');
		var rafid=$(this).data('rafid');
		$('#scroll'+siteid).scrollTo('#H_'+action+rafid, 1000, {offset: {left:-120} });
	});

	$("body").on("click",".correspondingRAFID",function( e ){
		var rafid=$(this).data('rafid');
		var siteID=$(this).data('siteid');
		
		$('#siteTabs').addCtab('siteTabs','RAF'+rafid,'glyphicon-road','RAF ID '+rafid);
		$.ajax({
			type: "POST",
			url: 'scripts/raf/raf.php',
			data: { RAFID:rafid}, 
			success : function(data){
				$('#spinner').spin(false);
				$("#"+'RAF'+rafid).html(data);
				/*forceResponsiveTables('RAFTable'+siteID);
				$('#RAFTable'+siteID).scroller('RAFTable'+siteID,3);*/
			},
			beforeSend: function ( xhr ) {
				$('#spinner').spin();
			}
		});
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
		var rafid=$(this).data('rafid');
		var siteid=$(this).data('siteid');
		var type=$(this).data('type');
		var file=$(this).data('file');
		var actiondo=$(this).data('actiondo');
		var type=$(this).data('type');
		var bufferchangeallowed=$(this).data('bufferchangeallowed');
		var raftype= $("#raftype-"+rafid).val();
		var status= $("#status-"+rafid).val();
		var CON_PARTNER=$("#CON_PARTNER-"+rafid).val();
		var saveAllowed=$("#saveAllowed-"+rafid).val();


		$('.rafdetails LI').removeClass('active');
		$(this).addClass('active');
		//$('#RAFcontent').empty();
		$("#RAFcontent"+siteid+rafid).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
		$('#modalspinner').spin('small');

		
		if (file=='tracking'){
			var url='scripts/tracking/tracking.php'
		}else{
			var url='scripts/raf/raf_details_'+ file +'.php';
		}
		

		$('#RAFcontent'+siteid+rafid).load(url, {
		  	rafid:rafid,
		  	raftype:raftype,
		  	siteID:siteid,
		  	status:status,
		  	CON_PARTNER:CON_PARTNER,
		  	saveAllowed: saveAllowed,
		  	actiondo:actiondo,
		  	bufferchangeallowed:bufferchangeallowed,
		  	type:type	
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
		    placement:'right',
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
		    	data: { siteid:siteid, raftype:raftype, field:id[0],oldval:oldval,rafid:rafid} ,
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
    				    			
				try {
				    var response = $.parseJSON(response);
				} catch(error) {
				    // its not json
				}

			  	var select_id = $(this).attr('id');
				var id = select_id.split('-');
				var status=$('#status_'+id[1]).text();
				var type=$('#type_'+id[1]).text();
				var result_BC_check=type.indexOf('Upgrade');	
				var siteid=$('#sitename-'+id[1]).val();		  

				if (value==='REJECTED' || value==='STOPPED'){ 

					bootbox.prompt({
					  	title: "Please provide a reason why you reject?",
					  	closeButton: false,
					  	callback: function(result) {
						    $.ajax({
								type: "POST",
								url: 'scripts/raf/raf_actions.php',
								data: { 
									siteID:siteid,
									rafid:id[1],
									field:id[0]+'_REJECT',
									action:'save_rejection_reason',
									value:result
								},
								success : function(data){
									$('.btn-default').prop("disabled", false);
									var response = $.parseJSON(data);
									
									Messenger().post({
										message: response.msg,
										type: response.rtype,
										showCloseButton: true
									});
									if (response.rtype=="info"){
										$('#raficon'+response.siteID).click();
									}
								},
								beforeSend: function ( xhr ) {
								}
							});
						}
					});
					$('.btn-default').prop("disabled", true);
				}else{					

					if (response.rtype=="error"){
						Messenger().post({
							message: response.msg,
							type: response.rtype,
							showCloseButton: true
						});
					}else{
						$('#raficon'+$('#rafSiteID').val()).click();
					}
			 		
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

	$("body").on("click",".saveBUDGET",function( e ){
		e.preventDefault();
		var rafid=$(this).data('rafid');
		var acqcon=$(this).data('acqcon');

		function after_BUDGETcheck(response){  
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
			success: after_BUDGETcheck,
			dataType:  'json',
		};
		$('#modalspinner').spin('medium');
		$('#form_'+rafid+'Budget'+acqcon).ajaxSubmit(options); 
	   	return false; 
	});


/****************************************************************************************
* LOS
*****************************************************************************************/
	$("body").on("click",".losnav",function( event ){
		var action=$(this).attr('id');
		$('#LOScontent').empty();
		//$('#LOScontentNet1').empty();
		$("#LOScontentNet1").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
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
		//$('#LOScontent').empty();
		$("#LOScontent").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
		
		
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
	$('#filterLOS').collapse('toggle');
	$('#spinner').spin('medium');
	$("#reportoutput").hide('fast');
	
	if ($('#csvreport').is(':checked')){
		var options = {
		   	target:  '#LOSreportoutput',
		    success:    function(responseText) {
					$("#LOSreportoutput").show('fast');
					$('#spinner').spin(false);
			},
			url:'scripts/los/los_csv.php'
		};
	}else{
		var options = {
		   	target:  '#LOSreportoutput',
		    success:    function() {
					$("#LOSreportoutput").show('fast');
					forceResponsiveTables("LOSTable");
					$('#LOSTable').scroller('LOSTable',4);
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
	$('#spinner').spin();
	$("#reportoutput").hide('fast');
	var options = {
	   	target:  '#reportoutput',
	    success:    function() {
				$("#reportoutput").show('fast');
				/*forceResponsiveTables("RAFTable");
				$('#RAFTable').scroller('RAFTable',4);*/
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
* BSDS
*****************************************************************************************/

	$("body").on("click",".BSDS_new",function( e ){
		var candidate=$(this).data('candidate');
		var upgnr=$(this).data('upgnr');
		var nbup=$(this).data('nbup');
		var siteid=$(this).data('siteid');

		var optionsBSDSCreate = { 
			data:{ action:'insert_new_bsds_raf'},
			dataType:  'json',
			success:  function(response) { 
				if (response.type==='info'){					
					Messenger().post({
						  message:  response.msg
					});
					
					$.ajax({
						type: "POST",
						url: 'scripts/general_info/general_info.php',
						data: { upgnr:upgnr,candidate:candidate,nbup:nbup,siteid:siteid},
						success : function(data){
							$('#spinner').spin(false);
							if (nbup!='UPG'){
								var title='BSDS '+candidate;
								var tab=candidate;
							}else{
								var title='BSDS '+upgnr;
								var tab=candidate+upgnr;
							}
							$('#siteTabs').addCtab('siteTabs','BSDS_'+tab,'glyphicon-book',title);
							$('#BSDS_'+tab).html(data);
						},
						beforeSend: function ( xhr ) {
							$('#spinner').spin();
						}
					});
					
				}	
			}						
		}

		jQuery.ajax({
		    type: 'POST',
		    url: 'scripts/general_info/general_info_newbsds.php',
		    data: { candidate:candidate,upgnr:upgnr },
		    success: function(data) {
		        bootbox.dialog({
		            message: data,
		            title: '<h4>Create new BSDS for '+candidate+' '+upgnr+':</h4>',
		             buttons: {
			            success: {
			                label:"Create BSDS",
			                className: "btn-success",
			                callback: function() {								
						        
								$('#new_bsds_form'+candidate+upgnr).ajaxSubmit(optionsBSDSCreate);
						    }
			            }
			        }
		        });
		    }
		});
	});
	
	$("body").on("click",".bsdsdetails2",function( e ){
		
		var clicked_tab=$(this).data("techno");	
		var id=$(this).data("id");

		if (clicked_tab=='PRINTBSDS' || clicked_tab=='PRINTBSDS2'){
			var print='yes';
		}else{
			var print='no';
		}

		if (clicked_tab=='LOADTECHNO'){
			var band=$(this).data('band');			
		}else{
			var band='';
		}

    	var createddate=$('#bsdsform_'+id+' input[name="createddate"]').val();
		var bsdskey=$('#bsdsform_'+id+' input[name="bsdskey"]').val();
		var upgnr=$('#bsdsform_'+id+' input[name="upgnr"]').val();
		var candidate=$('#bsdsform_'+id+' input[name="candidate"]').val();
		var siteid=$('#bsdsform_'+id+' input[name="candidate"]').val();
		var nbup=$('#bsdsform_'+id+' input[name="nbup"]').val();

    	var formData= {
			'datakey': 			$('#bsdsform_'+id+' input[name="datakey"]').val(),
			'bsdskey': 			bsdskey,
			'status' : 			$('#bsdsform_'+id+' input[name="status"]').val(),
			'nbup' : 			nbup,
			'candidate': 		candidate,
			'upgnr': 			upgnr,
			'siteid': 			siteid,
			'bsdsbobrefresh': 	$('#bsdsform_'+id+' input[name="bsdsbobrefresh"]').val(),
			'rafid': 			$('#bsdsform_'+id+' input[name="rafid"]').val(),
			'raftype': 			$('#bsdsform_'+id+' input[name="raftype"]').val(),
			'frozen': 			$('#bsdsform_'+id+' input[name="frozen"]').val(),
			'cabtype': 			$('#bsdsform_'+id+' input[name="cabtype"]').val(),
			'uniran': 			$('#bsdsform_'+id+' input[name="uniran"]').val(),
			'rectifier': 		$('#bsdsform_'+id+' input[name="rectifier"]').val(),
			'powersup': 		$('#bsdsform_'+id+' input[name="powersup"]').val(),
			'technos': 			$('#bsdsform_'+id+' input[name="technos"]').val(),
			'lognodeG9': 		$('#bsdsform_'+id+' input[name="lognodeG9"]').val(),
			'lognodeG18': 		$('#bsdsform_'+id+' input[name="lognodeG18"]').val(),
			'lognodeU9': 		$('#bsdsform_'+id+' input[name="lognodeU9"]').val(),
			'lognodeU21': 		$('#bsdsform_'+id+' input[name="lognodeU21"]').val(),
			'lognodeL8': 		$('#bsdsform_'+id+' input[name="lognodeL8"]').val(),
			'lognodeL18': 		$('#bsdsform_'+id+' input[name="lognodeL18"]').val(),
			'lognodeL26': 		$('#bsdsform_'+id+' input[name="lognodeL26"]').val(),
			'technosCon': 		$('#bsdsform_'+id+' input[name="technosCon"]').val(),
			'createddate': 		createddate,
			'print': 			print,
			'band': 			band,
			'xycoord': 			$('#bsdsform_'+id+' input[name="xycoord"]').val(),
			'address': 			$('#bsdsform_'+id+' input[name="address"]').val()
		};

		if (clicked_tab==="DELETEBSDS"){
			var key=$(this).data('key');
		
			bootbox.confirm('Click DELETE if you want to DELETE the BSDS with key "'+key, function(result) {
				if (result==true){
					$.post("scripts/general_info/general_info_actions.php", { 
						action:"delete_bsds",
						key: key
					},function(response){
						
						$('.colorchange'+id).css('background-color', '#ebcccc');

						Messenger().post({
						  message: response,
						  type: 'info',
						  showCloseButton: true
						});
					})
				}
			});	
		}else if(clicked_tab==="ATTACHRAF"){

			var optionsBSDSCreate = { 
				data:{ action:'insert_new_bsds_raf'},
				dataType:  'json',
				success:  function(response) { 
					if (response.type==='info'){					
						Messenger().post({
							  message:  response.msg
						});
						
						$.ajax({
							type: "POST",
							url: 'scripts/general_info/general_info.php',
							data: { upgnr:upgnr,candidate:candidate,nbup:nbup,siteid:siteid},
							success : function(data){
								$('#spinner').spin(false);
								if (nbup!='UPG'){
									var title='BSDS '+candidate;
									var tab=candidate;
								}else{
									var title='BSDS '+upgnr;
									var tab=candidate+upgnr;
								}
								$('#siteTabs').addCtab('siteTabs','BSDS_'+tab,'glyphicon-book',title);
								$('#BSDS_'+tab).html(data);
							},
							beforeSend: function ( xhr ) {
								$('#spinner').spin();
							}
						});
						
					}	
				}						
			}

			
			var bsdskey=$(this).data('bsdskey');
			var title='<h4>ATTACH RAF to BSDS '+bsdskey+':</h4>';
				
			jQuery.ajax({
			    type: 'POST',
			    url: 'scripts/general_info/general_info_newbsds.php',
			    data: { candidate:candidate,upgnr:upgnr, bsdskey:bsdskey },
			    success: function(data) {
			        bootbox.dialog({
			            message: data,
			            title: title,
			             buttons: {
				            success: {
				                label:"OK",
				                className: "btn-success",
				                callback: function() {								
							        
									$('#new_bsds_form'+candidate+upgnr).ajaxSubmit(optionsBSDSCreate);
							    }
				            }
				        }
			        });
			    }
			});

		}else if(clicked_tab==="FREEZEBSDS"){
			var rafid=$(this).data('rafid');
			var key=$(this).data('key');
			var key2=$(this).data('key2');

			bootbox.confirm('Click Ok if you want to FREEZE BSDS with RAFID "'+rafid+'"', function(result) {
				if (result==true){
				 	$.post("scripts/general_info/general_info_actions.php", { 
						action:"freeze_bsds",
						id: id,
						rafid: rafid,
						key: key,
						key2: key2,
						upgnr: upgnr,
						candidate: candidate
					},function(response){
						var response = $.parseJSON(response);	
						var type=response.responsetype;		

						$('.colorchange'+id).css('background-color', '#FFB202');

						Messenger().post({
						  message: response.data,
						  type: response.type,
						  showCloseButton: true
						});
					})
				}
			}); 
		
	    }else if(clicked_tab==="ALLIN" || clicked_tab==="PRINTBSDS" || clicked_tab==="PRINTBSDS2"){
	    	
	    	var title=bsdskey+' ['+createddate+']<br><span class="badge pull-left">['+upgnr+']</span><span class="badge pull-right">'+candidate+'</span>';

	    	if (clicked_tab==="ALLIN"){
				$('#MainsiteTabs').addCtab('siteTabs',id,'glyphicon-book',title);
				
				$('#'+id).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
				$('#'+id).empty();
			}

			$.ajax({
			type: "POST",
			url: "scripts/current_planned/current_planned_BBU_output.php",
			data: formData,
			success : function(data){
				
				$('#spinner').spin(false);

				if (clicked_tab==="PRINTBSDS"){

					var win = window.open('about:blank', '_blank');
           		    win.document.write(data);

				}else if (clicked_tab==="PRINTBSDS2"){
					bootbox.dialog({
			            message: data,
			            title: 'PRINT BSDS:</h4>',
			            className: 'modalwide',
			            buttons: {
			                success: {
			                    label: "PRINT",
			                    className: "btn-primary",
			                    callback: function() {
			                    	var title=bsdskey+'_'+candidate+'_'+upgnr+'_'+createddate;			                    	
			                    	$('#printArea'+id).printArea({pageTitle:title});
							    }
			                }
			            }
			        }).init(function () {
            			//FOR BBU layout
			            $('#ColorAnalysis'+id+" .form-control").each(function(){
   	
					    	var name =this.name;
					    	var id=this.id;	

					    	//The planned value		    	
					    	var plval=$(this).val();
					    	//console.log('name='+name+'/'+id+'pl:'+plval+'/'+id.substr(0,3));
							
							if (id.substr(0,3)==='pl_'){ 
						    	right= id.split('pl_');
					  			idname =right[1];
					  			//The current value
						    	var curval=$('#printArea'+id+' #cur_'+idname).val(); 
						    	//console.log(idname+'-- name='+name+'/'+id+'=>pl:'+plval+'/cur:'+curval+'/');
							    if (curval!==plval){	
							    	$('#printArea'+id+' #pl_'+idname).replaceWith("<span class='notsame'>" + plval+ "</span>");
							    }else{
							    	$('#printArea'+id+' #pl_'+idname).replaceWith("<span class='same'>" + plval+ "</span>");
							    }			    	
							}else{
								//console.log('=>>>> name='+name+'/'+id+'pl:'+plval+'/');
							   	var currentVal=$('#printArea'+id+' #'+id).val();   	
							    $('#printArea'+id+' #'+id).replaceWith("<span>" + currentVal+ "</span>");		    	
							}					
						});				            
			        });    
				}else{
			    	$('#'+id).html(data);			    	 		   

				    $(window).scroll(function(){ 
						if( $('#scrollG9'+id).length > 0 ) {
							if ($('#scrollG9'+id).is_on_screen()){
								$('#scrolls_G9'+id).show();
							}else{
								$('#scrolls_G9'+id).hide();
							}
						}
						if( $('#scrollG18'+id).length > 0 ) {
							if ($('#scrollG18'+id).is_on_screen()){
								$('#scrolls_G18'+id).show();
							}else{
								$('#scrolls_G18'+id).hide();
							}
						}
						if( $('#scrollU9'+id).length > 0 ) {
							if ($('#scrollU9'+id).is_on_screen()){
								$('#scrolls_U9'+id).show();
							}else{
								$('#scrolls_U9'+id).hide();
							}
						}
						if( $('#scrollU21'+id).length > 0 ) {
							if ($('#scrollU21'+id).is_on_screen()){
								$('#scrolls_U21'+id).show();
							}else{
								$('#scrolls_U21'+id).hide();
							}
						}
						if( $('#scrollL8'+id).length > 0 ) {
							if ($('#scrollL8'+id).is_on_screen()){
								$('#scrolls_L8'+id).show();
							}else{
								$('#scrolls_L8'+id).hide();
							}
						}
						if( $('#scrollL18'+id).length > 0 ) {
							if ($('#scrollL18'+id).is_on_screen()){
								$('#scrolls_L18'+id).show();
							}else{
								$('#scrolls_L18'+id).hide();
							}
						}
						if( $('#scrollL26'+id).length > 0 ) {
							if ($('#scrollL26'+id).is_on_screen()){
								$('#scrolls_L26'+id).show();
							}else{
								$('#scrolls_L26'+id).hide();
							}
						}
					});
					/*
				    //FOR BBU layout:
					$('#printArea'+id+" .form-control").each(function(){
   	
				    	var name =this.name;
				    	var id=this.id;	

				    	//The planned value		    	
				    	var plval=$(this).val();
				    	//console.log('name='+name+'/'+id+'pl:'+plval+'/'+id.substr(0,3));
						
						if (id.substr(0,3)==='pl_'){ 
					    	right= id.split('pl_');
				  			idname =right[1];
				  			//The current value
					    	var curval=$('#printArea'+id+' #cur_'+idname).val(); 
					    	console.log(idname+'-- name='+name+'/'+id+'=>pl:'+plval+'/cur:'+curval+'/');
					    	if (curval=='NA' || curval=='0'|| curval=='00' || curval=='NONE' || curval=='NO') curval='';
			  				if (plval=='NA' || plval=='0'|| plval=='00' || plval=='NONE' || plval=='NO') plval='';

						    if (curval!==plval){	
						    	$(this).parent("td").addClass("notsame");
						    }		    	
						}				
					});
					*/	
				    $('#spinner').spin(false); 
				}     
			},
			beforeSend: function ( xhr ) {
				$('#spinner').spin();
			}
			});

		}else if(clicked_tab==="LOADTECHNO"){
			

			if ($(this).hasClass('alreadyloaded')==true){
				$(".banddata").hide();
				$('#banddata'+band+id).show();
			}else{
			
				$(this).addClass('alreadyloaded');
				$(".banddata").hide();
				$('#banddata'+band+id).show();

				$.ajax({
				type: "POST",
				url: "scripts/current_planned/current_planned_output.php",
				data: formData,
				success : function(data){
						$('#banddata'+band+id).html(data).slideDown();

						$('#banddata'+band+id+" .form-control").each(function(){
	   	
					    	var name =this.name;
					    	var id=this.id;		    	
					    	var plval=$(this).val();
							
							if (id.substr(0,3)==='pl_'){ 
						    	right= id.split('pl_');
					  			idname =right[1];
						    	var curval=$('#printArea'+id+' #cur_'+idname).val(); 
						    	if (curval=='NA' || curval=='0'|| curval=='00' || curval=='NONE' || curval=='NO') curval='';
				  				if (plval=='NA' || plval=='0'|| plval=='00' || plval=='NONE' || plval=='NO') plval='';

							    if (curval!==plval){	
							    	$(this).parent("td").addClass("notsame");
							    }		    	
							}				
						});
					},
				beforeSend: function ( xhr ) {
					$('#banddata'+band+id).html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...');
					}
				});
			}
		
		}else if (clicked_tab==="SN"){
				var title=clicked_tab+' '+bsdskey+' ['+bsdsbobrefresh+']<br><span class="badge pull-right">'+candidate+'</span>';
				$('#siteTabs').addCtab('siteTabs',targettype,'glyphicon-send',title);
				$('#'+targettype).empty();

				$.ajax({
					type: "POST",
					url: 'scripts/shipping_notification/sn.php',
					data: { bsdskey:bsdskey,siteid:siteid,bsdsbobrefresh:bsdsbobrefresh,rafid:rafid},
					success : function(data){
					    $('#spinner').spin(false);
					    $('#'+targettype).html(data);
					},
					beforeSend: function ( xhr ) {
						$('#spinner').spin();
					}
				});
		}else{
			alert('NOT YET PROGRAMMED');
		}	
	});

	function SavesubmitCurPl(viewtype){
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
		$('#current_planned_form'+viewtype).ajaxSubmit(options); 
		return false; 
	};	

	$("body").on("click",".saveSubCurPl",function(e){
		e.preventDefault();
		var viewtype=$(this).data('key');
		SavesubmitCurPl(viewtype);
		return false; 
	});

	function SavesubmitCurPlBBU(viewtype){
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
		$('#current_planned_BBUform'+viewtype).ajaxSubmit(options); 
		return false; 
	};	

	$("body").on("click",".saveSubCurPlBBU",function(e){
		e.preventDefault();
		var viewtype=$(this).data('key');
		SavesubmitCurPlBBU(viewtype);
		return false; 
	});
		

	$("body").on("click","#bsds-print",function(e ){
		var targettype = $(this).data('id');
		var title = $(this).data('title');
		//$('#bsds-print').hide();
		$('#'+targettype).printArea({pageTitle:title});
	});	

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
				if (response.responsetype!='error'){
					$(".savemessage").hide();
					$(".hidebtn").removeClass('hidden');
				}
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

	$("body").on("click",".clear",function(e){
		var table=$(this).data('table');
		bootbox.dialog({
            message: 'Are u sure you want to clear? Everything will be removed/emptied when you hit save buttom?',
            title: 'Clear table data',
            buttons: {
                success: {
                    label:"I'm sure",
                    className: "btn-success",
                    callback: function() {
                    	
                    	$("#"+table+" .cleardata").each(
							function(intIndex){
								pl_attribute_type=$(this).attr('type');
								pl_attribute_name=$(this).attr('name');
								//alert(pl_attribute_name+pl_attribute_name.indexOf("ANTTYPE"));
								//console.log(pl_attribute_name+'---'+pl_attribute_name);
								if (pl_attribute_type==='select-one'){
									$(this).find('option').remove().append("<option value='' selected='selected'></option>");
								}else{
									$(this).val('');
								}
							}
						);
					
						$("#"+table+" .dynamic").select2('val',null);
				    }
                },
            }
        });	
	});	

	$("body").on("mousedown",".leftArrow",function( e ){
	  	var content=$(this).data('scrollid');
	  	scrolling = true;
    	scrollContent("left",content);
	});
	$("body").on("mouseup",".leftArrow",function( e ){
	  	scrolling = false;
	});

	$("body").on("mousedown",".rightArrow",function( e ){
	  	var content=$(this).data('scrollid');
	  	scrolling = true;
    	scrollContent("right",content);
	});
	$("body").on("mouseup",".rightArrow",function( e ){
	  	scrolling = false;
	});

	$("body").on("click",".prevTechno",function( e ){
	  	var content=$(this).data('scrollid');
    	scrollToAnchor(content);
	});
	$("body").on("click",".nextTechno",function( e ){
	  	var content=$(this).data('scrollid');
	  	scrollToAnchor(content);
	});


/****************************************************************************************
* SHIPPING NOTIFICATIONS
*****************************************************************************************/
$("body").on("click",".deleteSN",function(e){
	e.preventDefault();
	var prodref=$(this).data('prodref');
	var SN_ID=$(this).data('sn_id');

	msg = Messenger().post({
		  	message: 'Are u sure you want to delete the line with REF '+prodref+'?',
		  	type: 'info',
		  	actions: {
			    	ok: {
						label: "I'm Sure",
						action: function() {
							msg.cancel();
							$.ajax({
								type: "POST",
								url: "scripts/shipping_notification/sn_actions.php",
								data: { prodref:prodref, action:'deleteSNline',SN_ID:SN_ID},
								success : function(data){
									$('#spinner').spin(false);
									$.ajax({
										type: "POST",
										url: 'scripts/shipping_notification/sn_actions.php',
										data: { 
											action:'reloadTable',
											SN_ID:SN_ID
										},
										success : function(data){
											var response = $.parseJSON(data);
										    $('#spinner').spin(false);
										    $("#SNdata"+SN_ID).html(response.output);
										},
										beforeSend: function ( xhr ) {
											$('#spinner').spin();
										}
									});
								},
								beforeSend: function ( xhr ) {
									$('#spinner').spin("large");
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

$("body").on("click",".LogisticsConfirmSN",function(e){
	e.preventDefault();
	var action=$(this).data('action');
	var SN_ID=$(this).data('sn_id');
	var options = { 
		success:  function(response) { 
			var response = $.parseJSON(response);
		    $('#spinner').spin(false);
		    $("#SNdata"+SN_ID).html(response.output);								
		}
	}
	$('#SNForm'+SN_ID).ajaxSubmit(options); 
});


$("body").on("click",".partnerConfirmSN",function(e){
	e.preventDefault();
	var SN_ID=$(this).data('sn_id');
	var RAFID=$(this).data('rafid');
	var action=$(this).data('action');

	if (action=='partnerOK'){

		bootbox.dialog({
            message: 'Are you sure you want to finalaize SN '+SN_ID+'?',
            title: 'finalize SN',
            buttons: {
                success: {
                    label: "Reject",
                    label:"I'm sure",
                    className: "btn-success",
                    callback: function() {
                    	
                    	if ($('#shipdate').val()==''){
                    		Messenger().post({
								message: 'You need to provide a shipping date',
							  	type: 'error',
							  	showCloseButton: true
							});
							return false;
                    	}

				        var options = { 
							data:{ action:'partnerOK'},
				 			success:  function(response) { 
								var response = $.parseJSON(response);
							    $('#spinner').spin(false);
							    $("#SNdata"+SN_ID).html(response.output);								
							}
				 		}
						$('#SNForm'+SN_ID).ajaxSubmit(options);
				    }
                },
            }
        });	
	}else if (action=='logisticsOK'){

		bootbox.confirm("Are you sure you want to confirm the SN with ID "+SN_ID+"?", function(result) {
			
			if (result==true){
				var options = { 
					data:{ action:'logisticsOK'},
		 			success:  function(response) { 
						var response = $.parseJSON(response);
					    $('#spinner').spin(false);
					    $("#SNdata"+SN_ID).html(response.output);								
					}
		 		}
				$('#SNForm'+SN_ID).ajaxSubmit(options); 
			}
		});	
	}else if (action=='logisticsREJECT'){

		jQuery.ajax({
		    type: 'POST',
		    url: 'scripts/shipping_notification/sn_reject_reason.php',
		    data: { SN_ID: SN_ID, RAFID: RAFID },
		    success: function(data) {
		        bootbox.dialog({
		            message: data,
		            title: 'Rejection reason for SN '+SN_ID+':</h4>',

		            buttons: {
		                success: {
		                    label: "Reject",
		                    className: "btn-primary",
		                    callback: function() {
		                    	
		                    	if ($('#SNRejectReason').val()==''){
		                    		Messenger().post({
										message: 'You need to provide a reason',
									  	type: 'error',
									  	showCloseButton: true
									});
									return false;
		                    	}

						        var options = { 
									data:{ action:'logisticsREJECT'},
						 			success:  function(response) { 
										var response = $.parseJSON(response);
									    $('#spinner').spin(false);
									    $("#SNdata"+SN_ID).html(response.output);								
									}
						 		}
								$('#SN_Reject_form'+SN_ID).ajaxSubmit(options);
						    }
		                },
		            }
		        });
		    }
		});	
							
	}
});	

/****************************************************************************************
* RAF COMMENTS UPLOADER
*****************************************************************************************/
$("body").on("click",".ConfirmRAFcomments",function(e){
	e.preventDefault();
	
	$.ajax({
		type: "POST",
		responsetype: "json",
		url: "scripts/rafcomments/rafcomments_actions.php",
		data: { 
			action:'confirmComments'
		},
		success : function(data){
			var response = $.parseJSON(data);
			$('#spinner').spin(false);
			$("#rafcommentsdata").empty();
			Messenger().post({
				  message:  response.output,
				  type: 'info',
				  showCloseButton: true
				});	

		},
		beforeSend: function ( xhr ) {
			$('#spinner').spin("large");
		}
	});	
});	
/****************************************************************************************
* RAF COF UPLOADER
*****************************************************************************************/
$("body").on("click",".ConfirmCOF",function(e){
	e.preventDefault();
	
	$.ajax({
		type: "POST",
		responsetype: "json",
		url: "scripts/cofuploader/cofuploader_actions.php",
		data: { 
			action:'confirmCof'
		},
		success : function(data){
			var response = $.parseJSON(data);
			$('#spinner').spin(false);
			$("#Cofsdata").empty();
			Messenger().post({
				  message:  response.output,
				  type: 'info',
				  showCloseButton: true
				});	
		},
		beforeSend: function ( xhr ) {
			$('#spinner').spin("large");
		}
	});	
});	

$("body").on("click","#genrateCOF",function(e){
	e.preventDefault();
	$('#spinner').spin("large");
	bootbox.dialog({
        message: 'Are you sure you want to genrate the COF file?',
        title: 'GENRATE COF',
        buttons: {
            success: {
                label:"I'm sure",
                className: "btn-success",
                callback: function() {
                	jQuery.ajax({
					    type: 'POST',
					    url: 'scripts/cofgenerator/cofgenerator_actions.php',
					    success: function(data) {
					    	$('#outputCOFgenerator').html(data);
					    	$('#spinner').spin(false);
					    }
					});	
                	
			    }
            }
        }
    });	
});	

/****************************************************************************************
* TRACKING
*****************************************************************************************/

	$("body").on("click",".tracknav",function( event ){
		
		event.preventDefault();
		var trackid=$(this).data('trackid');
		var msg;

		msg = Messenger().post({
		  message: 'Are u sure you want put '+trackid+' as history?',
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
				  data: { action:"make_history",
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
	});	



	$("body").on("click",".newTrack",function( event ){
		var siteid = $(this).data('siteid');

		jQuery.ajax({
		    type: 'POST',
		    url: 'scripts/tracking/tracking_modal.php',
		    data: { siteid:siteid },
		    success: function(response) {

		        bootbox.dialog({
		            message: response,
		            title: '<h4>Add comments to '+siteid+':</h4>',
		             buttons: {
			            success: {
			                label:"Add comments",
			                className: "btn-success",
			                callback: function() {								
						        
								function after_tracking_save(response){ 
									Messenger().post({
										  message: response.responsedata,
										  type: response.responsetype,
										  showCloseButton: true
									});	
								}
								var optionsTracking= {   
							    	success:  after_tracking_save,
									dataType:  'json',
								};	

								$('#addTrackingForm'+siteid).ajaxSubmit(optionsTracking);

								$("#trackicon"+siteid).click();	
						    }
			            }
			        }
		        });
		    },
		   
		});
	});

/**********************
SAVEMODAL actions
**********************/
		
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
		/*
		if ($('#inputCOMMERCIAL').val() === "") {
			msg=msg+"COMMERCIAL can not be empty! Please select NA if not applicable.<br>";	
		}*/
		if (form.sitenum.value === "") {
			msg=msg+"SITEID can not be empty! <br>";	
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
			message: response.msg,
			 type: response.rtype,
			 showCloseButton: true
		});
		if (response.rtype=="info"){
			$('#spinner').spin(false);
			$("#savemodal").removeClass("disabled");
			$('#myModal').modal('hide');
			$('#raficon'+$('#rafSiteID').val()).click();
			return true;
		}else{
			return false;
		}
	}

	function validate_ReasonSNForm(formData, jqForm, options){
	    var form = jqForm[0];
		var msg='';
		if (form.reason.value === "") {
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

	function after_ReasonSNForm_save(response)  {
		var response = $.parseJSON(response);
	    $('#spinner').spin(false);
	    $("#SNdata"+SN_ID).html(response.output);
	}
	var optionsRejectSN = {
    	success:  after_ReasonSNForm_save,
		dataType:  'json',
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
		}else if (module=="losreject"){
			var losid=$(this).data('id');
			$('#Reject_form'+losid).ajaxSubmit(optionsLosreject);
		}else if (module=="rafprint" || module=="losprint"){
			$('.printThis').printArea();
			//reset the button to save changes
			$("#savemodal").show();
		}else if (module=="snreject"){
			$('#SN_Reject_form').ajaxSubmit(optionsRejectSN);	
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
    		'z-index' : 997
        
        });
        //fixedCol.find('th,td').eq(0).css('width',fixedWidthCol1+'px');
        $($fixedColumn).insertBefore('#'+tableid);
};

$.fn.addCtab = function(tabname,target,iconImg,title) { // tabname= siteTabs or MainsiteTabs
	if (tabname=='siteTabs'){
		var contentabs="contentTabs";
	}else if (tabname=='MainsiteTabs'){
		var contentabs="SuperContentTabs";
	}
	$("#"+tabname+" li.active").removeClass('active');
	$("#"+contentabs+" .tab-pane").removeClass('active');

  	if ($("#"+target).length == 0){
    	$("#"+tabname).append('<li class="active" id="tab_'+target+'"><a href="#'+target+'"  data-toggle="tab"><span class="glyphicon '+iconImg+'"></span> '+title+'</a></li>');
		$("#"+contentabs).append('<div class="tab-pane active" id="'+target+'"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...</div>');
	}
	$("#tab_"+target).addClass('active');
	$("#"+target).addClass('active');
	if (tabname=='siteTabs'){
		reAdjust('subTabs'); 
	}
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
/*
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
*/


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

/**
 * Copyright (c) 2007-2015 Ariel Flesler - aflesler ○ gmail • com | http://flesler.blogspot.com
 * Licensed under MIT
 * @author Ariel Flesler
 * @version 2.1.3
 */
;(function(f){"use strict";"function"===typeof define&&define.amd?define(["jquery"],f):"undefined"!==typeof module&&module.exports?module.exports=f(require("jquery")):f(jQuery)})(function($){"use strict";function n(a){return!a.nodeName||-1!==$.inArray(a.nodeName.toLowerCase(),["iframe","#document","html","body"])}function h(a){return $.isFunction(a)||$.isPlainObject(a)?a:{top:a,left:a}}var p=$.scrollTo=function(a,d,b){return $(window).scrollTo(a,d,b)};p.defaults={axis:"xy",duration:0,limit:!0};$.fn.scrollTo=function(a,d,b){"object"=== typeof d&&(b=d,d=0);"function"===typeof b&&(b={onAfter:b});"max"===a&&(a=9E9);b=$.extend({},p.defaults,b);d=d||b.duration;var u=b.queue&&1<b.axis.length;u&&(d/=2);b.offset=h(b.offset);b.over=h(b.over);return this.each(function(){function k(a){var k=$.extend({},b,{queue:!0,duration:d,complete:a&&function(){a.call(q,e,b)}});r.animate(f,k)}if(null!==a){var l=n(this),q=l?this.contentWindow||window:this,r=$(q),e=a,f={},t;switch(typeof e){case "number":case "string":if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(e)){e= h(e);break}e=l?$(e):$(e,q);case "object":if(e.length===0)return;if(e.is||e.style)t=(e=$(e)).offset()}var v=$.isFunction(b.offset)&&b.offset(q,e)||b.offset;$.each(b.axis.split(""),function(a,c){var d="x"===c?"Left":"Top",m=d.toLowerCase(),g="scroll"+d,h=r[g](),n=p.max(q,c);t?(f[g]=t[m]+(l?0:h-r.offset()[m]),b.margin&&(f[g]-=parseInt(e.css("margin"+d),10)||0,f[g]-=parseInt(e.css("border"+d+"Width"),10)||0),f[g]+=v[m]||0,b.over[m]&&(f[g]+=e["x"===c?"width":"height"]()*b.over[m])):(d=e[m],f[g]=d.slice&& "%"===d.slice(-1)?parseFloat(d)/100*n:d);b.limit&&/^\d+$/.test(f[g])&&(f[g]=0>=f[g]?0:Math.min(f[g],n));!a&&1<b.axis.length&&(h===f[g]?f={}:u&&(k(b.onAfterFirst),f={}))});k(b.onAfter)}})};p.max=function(a,d){var b="x"===d?"Width":"Height",h="scroll"+b;if(!n(a))return a[h]-$(a)[b.toLowerCase()]();var b="client"+b,k=a.ownerDocument||a.document,l=k.documentElement,k=k.body;return Math.max(l[h],k[h])-Math.min(l[b],k[b])};$.Tween.propHooks.scrollLeft=$.Tween.propHooks.scrollTop={get:function(a){return $(a.elem)[a.prop]()}, set:function(a){var d=this.get(a);if(a.options.interrupt&&a._last&&a._last!==d)return $(a.elem).stop();var b=Math.round(a.now);d!==b&&($(a.elem)[a.prop](b),a._last=this.get(a))}};return p});


$.fn.is_on_screen = function(){
    var win = $(window);
    var viewport = {
        top : win.scrollTop(),
        left : win.scrollLeft()
    };
    viewport.right = viewport.left + win.width();
    viewport.bottom = viewport.top + win.height();
 
    var bounds = this.offset();
    bounds.right = bounds.left + this.outerWidth();
    bounds.bottom = bounds.top + this.outerHeight();
 
    return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
};
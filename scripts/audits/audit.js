$(document).ready(function() {	
	$.editable.addInputType('time', {
	    /* Create input element. */
	    element : function(settings, original) {
		/* Create and pulldowns for date. Append them to form which is accessible as variable this. */     

		/* var dateinput2 = $('<input type="text" class="dateselecter" id="day_" />');
		 $(this).append(dateinput2);*/

		var dayselect = $('<select id="day_" />');
		var yearselect  = $('<select id="year_" />');
		var monthselect = $('<select id="month_" />');

		for (var day=1; day <= 31; day++) {
		    if (day < 10) {
			day = '0' + day;
		    }
		    var option = $('<option />').val(day).append(day);
		    dayselect.append(option);
		}
		$(this).append(dayselect);

		 for (var month=1; month <= 12; month++) {
		    if (month < 10) {
			month = '0' + month;
		    }
		    var option = $('<option />').val(month).append(month);
		    monthselect.append(option);
		}
		$(this).append(monthselect);


		for (var year=2011; year <= 2020; year++) {
		    var option = $('<option />').val(year).append(year);
		    yearselect.append(option);
		}
		$(this).append(yearselect);

		/* Last create an hidden input. This is returned to plugin. It will */
		/* later hold the actual value which will be submitted to server.   */
		var hidden = $('<input type="hidden" />');
		$(this).append(hidden);
		return(hidden);
	    },
	    /* Set content / value of previously created input element. */
	    content : function(string, settings, original) {

		/* Select correct hour and minute in pulldowns. */
		var day = parseInt(string.substr(0,2), 10);
		var month  = parseInt(string.substr(3,2), 10);
		var year  = parseInt(string.substr(7,4), 10);

		$('#day_', this).children().each(function() {
		    if (day == $(this).val()) {
			$(this).attr('selected', 'selected');
		    }
		});
		$('#month_', this).children().each(function() {
		    if (month == $(this).val()) {
			$(this).attr('selected', 'selected');
		    }
		});
		$('#year_', this).children().each(function() {
		    if (year == $(this).val()) {
			$(this).attr('selected', 'selected');
		    }
		});

	    },
	    /* Call before submit hook. */
	    submit: function (settings, original) {
		/* Take values from hour and minute pulldowns. Create string such as    */
		/* 13:45 from them. Set value of the hidden input field to this string. */
		var value = $('#day_').val() + '-' + $('#month_').val() + '-' + $('#year_').val();

		$('input', this).val(value);
	    }
	});
    
     $(".editable_select").editable("scripts/audits/audit_actions.php", { 
	indicator : '<img src="images/throbber.gif">',
	data   : " {'STOPPED FAILED':'STOPPED FAILED', 'STOPPED PASSED':'STOPPED PASSED','FAILED':'FAILED','PASSED':'PASSED', 'selected':'FAILED'}",
	type   : "select",
	style  : "inherit",
	submit    : 'Ok',
	tooltip   : 'Click to edit...',
	submitdata : function(value) {
		select_id = $(this).attr('id');
		id = select_id.split('_');
		return {action: "update_planning_date",id:id[0],field:id[1]};
	},
	callback : function(value, settings) {
		select_id = $(this).attr('id');
		id = select_id.split('_');
		$(document.body).qtip({
			id: 'audipartner',
			content: {			
				text: '<img class="throbber" src="images/throbber.gif" alt="Loading..." />',
				ajax: {
					url: 'scripts/audits/audit_punches.php',
					data: {tabid:$.session("tabid"),auditid:id[0],field:id[1]}
				},
				title: {
					text: 'Audit punches', 
					button: true
				}
			},
			position: {
				my: 'center', at: 'center', 
				target: $('#mainContent') 
			},
			show: {
				ready: true,
				modal: {
					on: true, 
					blur: blur 
				}
			},
			hide: false,
			style: 'ui-tooltip-shadow ui-tooltip-rounded ui-tooltip-blue',
			events: {				
				render: function(event, api) {
					$('button', api.elements.content).click(api.hide);
				},		
				hide: function(event, api) { api.destroy(); }
			}
		});
	}
     });
     
	$(".editable_partner").click(function(){	
		select_id = $(this).attr('id');
		id = select_id.split('_');
		$(document.body).qtip({
			id: 'audipartner',
			content: {			
				text: '<img class="throbber" src="images/throbber.gif" alt="Loading..." />',
				ajax: {
					url: 'scripts/audits/audit_punches.php',
					data: {tabid:$.session("tabid"),auditid:id[0],field:'STATUS'}
				},
				title: {
					text: 'Audit punches', 
					button: true
				}
			},
			position: {
				my: 'center', at: 'center', 
				target: $('#mainContent') 
			},
			show: {
				ready: true,
				modal: {
					on: true, 
					blur: blur 
				}
			},
			hide: false,
			style: 'ui-tooltip-shadow ui-tooltip-rounded ui-tooltip-blue',
			events: {				
				render: function(event, api) {
					$('button', api.elements.content).click(api.hide);
				},		
				hide: function(event, api) { api.destroy(); }
			}
		});
	});
 
      $('.editable').editable("scripts/audits/audit_actions.php", { 
              type      : 'time',
              class: 'dateselecter',
              submit    : 'OK',
              tooltip   : 'Click to plan...',
     	 submitdata : function(value,settings) {
     	 	select_id = $(this).attr('id');
     	 	id = select_id.split('_');
		return {action: "update_planning_date",id:id[0],field:id[1]};
     	  }
     });
     
     
     $(".editable_select_servicep1").editable("scripts/audits/audit_actions.php", { 
     	indicator : '<img src="images/throbber.gif">',
     	data   : " {'ALU ROLL-OUT':'ALU ROLL-OUT', 'BENCHMARK':'BENCHMARK','ALU OPERATIONS':'ALU OPERATIONS','KPNGB':'KPNGB'}",
     	type   : "select",
		style  : "inherit",
		submit    : 'Ok',
		tooltip   : 'Click to edit...',
		submitdata : function(value) {
		
			id = $(this).attr('id');
			id = id.split('_');
			return {action: "update_planning_date",id:id[0],field:id[1]};
	}  
     });
     
      $(".editable_select_servicep2").editable("scripts/audits/audit_actions.php", { 
          	indicator : '<img src="images/throbber.gif">',
          	data   : " {'VWNC':'VWNC', 'BENCHMARK':'BENCHMARK','SPEEDWORKS':'SPEEDWORKS','TELINDUS':'TELINDUS','FABRICOM':'FABRICOM','MARO':'MARO','ASTECHNICS':'ASTECHNICS','RONVEAUX':'RONVEAUX','LCC':'LCC','CEAVER':'CEAVER','ZTE':'ZTE','GRANIOU':'GRANIOU','TRYLON':'TRYLON','ACHROTECH':'ACHROTECH','COMMSOLUTIONS':'COMMSOLUTIONS','ALPINE':'ALPINE','AEG':'AEG','AFD':'AFD','ALU':'ALU','FR':'FR','NA':'NA','ETS':'ETS','TERUSUS':'TERUSUS','BTS':'BTS','VWT-Belgium':'VWT-Belgium','BTI-Alsaco':'BTI-Alsaco'}",
          	type   : "select",
     		style  : "inherit",
     		submit    : 'Ok',
     		tooltip   : 'Click to edit...',
     		submitdata : function(value) {
     			id = $(this).attr('id');
     			id = id.split('_');     		
     			return {action: "update_planning_date",id:id[0],field:id[1]};
     	}  
     });
     $('.editable_siteeng').editable('scripts/audits/audit_actions.php', { 
	type      : 'textarea',
	cancel    : 'Cancel',
	submit    : 'OK',
	indicator : '<img src="images/throbber.gif">',
	tooltip   : 'Click to edit...',
	submitdata : function(value,settings) {
		id = $(this).attr('id');
		id = id.split('_');
		return {action: "update_planning_date",id:id[0],field:id[1]};
	}
     });
     
      $(".editable_select_hscoord").editable("scripts/audits/audit_actions.php", { 
          	indicator : '<img src="../images/indicator_mozilla_blu.gif">',
          	data   : " {'Kris Bogaerts':'Kris Bogaerts', 'Other':'Other','Johan Schillebeeckx':'Johan Schillebeeckx','Jens Cant':'Jens Cant','D. Hontoir':'D. Hontoir','Christophe Romain Aximmo':'Christophe Romain Aximmo', 'J.Ertveldt':'J.Ertveldt','L.Sohet':'L.Sohet','Sven Wuyts':'Sven Wuyts','I. Bataille':'I. Bataille','R.Lievens & T.Himschoot':'R.Lievens & T.Himschoot','D.Emmerechts-Bogaerts':'D.Emmerechts-Bogaerts','S.Lambert':'S.Lambert'}",          	
          	type   : "select",     		style  : "inherit",
     		submit    : 'Ok',
     		tooltip   : 'Click to edit...',
     		submitdata : function(value) {
			id = $(this).attr('id');
			id = id.split('_');
			return {action: "update_planning_date",id:id[0],field:id[1]};
     		}  
     });
    
});


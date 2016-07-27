$(document).ready(function() {
	
	function changestatus(id){
		var priority=$('#PRIORITY-'+id).text();
		var done=$('#DONE-'+id).text();
		var report=$('#REPORT-'+id).text();
		var result=$('#RESULT-'+id).text();
		
		//alert(priority +" _ "+done+" _ "+report+" _ "+result);
		
		if (priority=="Canceled"){
			status="LOS canceled";
		}else{
			if (done=="NOT OK"){
				status="ALU in process";
			}else if (done=="OK" && report=="NOT OK"){
				status="ALU to create report";
			}else if (result=="NOT OK" && report=="OK" && done=="OK"){
				status="TXMN to evaluate report";
			}else{
				status="LOS confirmed";
			}
		}
		//alert(id+ "--"+status);
		$('#status_'+id).text(status);
	}	

	 $(".editable_select_PRIORITY").editable("scripts/los/los_actions.php", { 
	    indicator : '<img src="images/throbber.gif">',
	    data   : "{'0':'0','1':'1','2':'2','Canceled':'Canceled'}",
	    type   : "select",
	    style  : "inherit",
		submit    : 'OK',
		tooltip   : 'Click to edit...',
	    submitdata : function() {
		  select_id = $(this).attr('id');
		  id = select_id.split('-');
	      return {action: "change_status",id:id[1],field:id[0]};
	    },
		callback : function(value, settings) {
		  select_id = $(this).attr('id');
		  id = select_id.split('-');	 
		  changestatus(id[1]);
     	}
	});
	  
	$(".editable_select_RESULT").editable("scripts/los/los_actions.php", { 
	   	indicator : '<img src="images/throbber.gif">',
	   	data   : "{'NOT OK':'NOT OK','NLOS':'NLOS','Unconfirmed':'Unconfirmed','LOS':'LOS','Critical':'Critical','REJECTED':'REJECT'}",
	    	type   : "select",
	    	style  : "inherit",
		submit    : 'OK',
		tooltip   : 'Click to edit...',
		submitdata : function() {
			select_id = $(this).attr('id');
			id = select_id.split('-');
		      	return {action: "change_status",id:id[1],field:id[0]};
		},
		callback : function(value, settings) {
			select_id = $(this).attr('id');
			id = select_id.split('-');
			var select_val=value.replace(/^\s+/g,'').replace(/\s+$/g,'');
			if (select_val.toUpperCase()==='REJECTED'){
				$(document.body).qtip({
					content: {
						text: '<img class="throbber" src="images/throbber.gif" alt="Loading..." />',
						ajax: {
							url: "scripts/los/los_reject_reason.php", // Use the rel attribute of each element for the url to load
							data: { tabid:$.session("tabid"),
								losid:id[1]								
							}
						},
						title: {
							  text: 'LOS REJECTION',
							  button: true
							 }
					},
					position: {
						my: 'center', at: 'center', // Center it...
						target: $('#mainContent') // ... in the window
					},
					show: {
						ready: true, // Show it straight away
						modal: {
							on: true, // Make it modal (darken the rest of the page)...
							blur: blur // ... but don't close the tooltip when clicked
						}
					},
					hide: false, // We'll hide it maunally so disable hide events
					style: 'ui-tooltip-plain ui-tooltip-rounded ui-tooltip-rejection' // Add a few styles
				});
			}
			changestatus(id[1]);
		}
	});
	  
	$(".editable_select").editable("scripts/los/los_actions.php", { 
	    indicator : '<img src="images/throbber.gif">',
	    data   : "{'OK':'OK','NOT OK':'NOT OK','NA':'NA'}",
	    type   : "select",
	    style  : "inherit",
		submit    : 'OK',
		tooltip   : 'Click to edit...',
	    submitdata : function() {
		  select_id = $(this).attr('id');
		  id = select_id.split('-');
	      return {action: "change_status",id:id[1],field:id[0]};
	    },
		callback : function(value, settings) {
		  select_id = $(this).attr('id');
		  id = select_id.split('-');	 
		  changestatus(id[1]);
     		}
	});
});
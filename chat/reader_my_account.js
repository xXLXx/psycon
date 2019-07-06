// extract out the parameters
function gup(n,s){
	n = n.replace(/[\[]/,"\\[").replace(/[\]]/,"\\]");
	var p = (new RegExp("[\\?&]"+n+"=([^&#]*)")).exec(s);
	return (p===null) ? "" : p[1];
}



var scriptSrc = $("script[src^='/chat/reader_my_account.js']").attr('src');

var status = gup( 'current_status', scriptSrc );
var reader_id = gup( 'reader_id', scriptSrc );

var check_interval = 5000;
var check_interval_handler = setInterval(check_reader_break_status, check_interval);


function check_reader_break_status() {
	var url = "/ajax_functions.php?mode=checkReaderMyAccountStatus&reader_id="+reader_id;
    
    $.ajax({    
	    url: url,  
	    cache: false,  
	    success: function(retValue){
	    	// convert string to json object 
	    	var d = jQuery.parseJSON(retValue);
	    	if (d.logoff == true) {
	    		window.location.href = "/main/logout";
	    		return;
	    	}
	    	
	    	if (status != d.status) {
	    		switch(d.status) {
	    			case 'online':
	    				$('#reader_status_online_button').attr('class', 'btn btn-primary');
	    				$('#reader_status_break_button').attr('class', 'btn btn-default');
	    				$('#reader_status_offline_button').attr('class', 'btn btn-default');
	    				
	    				break;
	    			case 'busy':
	    			case 'break':
	    			case 'booked':
	    			case 'away':
	    				$('#reader_status_online_button').attr('class', 'btn btn-default');
	    				$('#reader_status_break_button').attr('class', 'btn btn-primary');
	    				$('#reader_status_offline_button').attr('class', 'btn btn-default');
	    				break;
	    			case 'offline':
	    			default:
	    				$('#reader_status_online_button').attr('class', 'btn btn-default');
	    				$('#reader_status_break_button').attr('class', 'btn btn-default');
	    				$('#reader_status_offline_button').attr('class', 'btn btn-primary');
	    			
	    		}
	    		status = d.status;	
	    	}
	   	}  
    }); 
}

if (typeof console === "undefined" || typeof console.log === "undefined") {
    alert('it is undefined');
    console = {};
    console.data = [];
    console.log = function(enter) {
        console.data.push(enter);
    };
}


function get_browser(){
    var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 
    if(/trident/i.test(M[1])){
        tem=/\brv[ :]+(\d+)/g.exec(ua) || []; 
        return 'IE '+(tem[1]||'');
    }   
    if(M[1]==='Chrome'){
        tem=ua.match(/\bOPR\/(\d+)/);
        if(tem!=null)   {return 'Opera '+tem[1];}
    }   
    M=M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
    if((tem=ua.match(/version\/(\d+)/i))!=null) {M.splice(1,1,tem[1]);}
    return M[0];
}
            
function get_browser_version(){
    var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];                                                                                                                         
    if(/trident/i.test(M[1])){
        tem=/\brv[ :]+(\d+)/g.exec(ua) || [];
        return 'IE '+(tem[1]||'');
    }
    if(M[1]==='Chrome'){
        tem=ua.match(/\bOPR\/(\d+)/);
        if(tem!=null)   {return 'Opera '+tem[1];}
    }   
    M=M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
    if((tem=ua.match(/version\/(\d+)/i))!=null) {M.splice(1,1,tem[1]);}
    return M[1];
 }
 
// ----  END OF PRELOAD --//
var Chat = {
    
    // Chat main object. 
    // socket.io must be included before this class
    // must include chat_ui and chat_message after this class.
    title:'',
    is_mobile:false,
    
    // 
    controller: {},
    manager: {},
    models:{},
    views:{},
    browser:{
        name:'',
        version:''
    },
    
    set_browser: function() {
        try {
            Chat.browser.name = get_browser();
            Chat.browser.version = get_browser_version();
        } catch (e) {
            
        }
        
        if (Chat.browser.name == 'MSIE') {
            var html = '<embed src="/media/sounds/door_bell.mp3" autostart="false" width="0" height="0" id="beep" enablejavascript="true">';
            $('#ieDiv').append(html);
        }
    },
    
    init: function(data) {
        console.log(data);
        if (data.is_mobile) {
            Chat.is_mobile = true;
        }
        
        if (data.chat_title) {
            Chat.title = data.chat_title;
            document.title = Chat.title;
        }
        if (Chat.models.socket) {
            Chat.models.socket.init(data);
        }  
        if (Chat.models.member) {
            Chat.models.member.init(data);
        }
        if (Chat.manager.room) {
            Chat.manager.room.init(data);
        }
        if (Chat.controller.lobby) {
            Chat.controller.lobby.init();
            if (data.reader_id) {
            	Chat.controller.lobby.reader_id = data.reader_id;
            }
        }
        console.log("Step 1");
        if (Chat.controller.room) {
            console.log("Step 2");
            Chat.controller.room.init();
        }
        
        Chat.set_browser();
        
    }
    
};
Chat.models.member = {
    
    member_id: '',      // member id
    member_id_hash: '', // member hash value for lobby
    member_hash: '',    // member hash value for chat room
    member_type: '',
    member_username: '',
    _disconnect_url: '',
    
    init: function(data) {
        this.member_id = data.member_id || '';
        this.member_id_hash = data.member_id_hash || '';
        this.member_hash = data.member_hash || '';
        this.member_type = data.member_type || '';
        this.member_username = data.member_username || '';
        this._disconnect_url = data._disconnect_url || '';
    }
};

Chat.models.socket = {
    
    io: null,   // socket io
    socket_port:'',
    socket_url:'',
    
    init: function(data) {
        this.socket_url = data.socket_url || '';       // e.g.  'http://66.178.176.109'
        this.socket_port = data.socket_port || ''; 
        if (this.socket_url && this.socket_port) {
            console.log("Set socket: " + this.socket_url + ":"+this.socket_port);
            this.io = io.connect(this.socket_url + ":"+this.socket_port);
            console.log("Setted socket");
        }
    }
};
Chat.views.message = {
    
    
    // show on reader window
    reader: {
        chat_attempt_for_reader: function (data) {
            Chat.views.error_log.log(data);
            $('#messageContainer').append("<div class='system'>A client has sent your a request for chat.</div>");    
            //$('body').append("<div id='modal'><div class='cont'><b>\""+data.client_username+"\" wants to chat with you.<br />Do you want to accept?</b><div style='margin:25px 0 0;' align='center'><a id='startChat' class='blue-button' onclick='Chat.views.popup.open_window(\""+data.chat_id+"\", \""+data.chat_session_id+"\")' ><span>Start Chatting</span></a> &nbsp; <a id='closeChat' class='blue-button' onclick='Chat.controller.lobby.actions.reject_chat_room(\""+data.chat_session_id+"\")'><span>Not At This Time</span></a></div></div></div><div id='modal_bg'></div>");
            $('body').append("<div id='modal'><div class='cont'><b>\""+data.client_username+"\" wants to chat with you.<br />Do you want to accept?</b><div style='margin:25px 0 0;' align='center'><a id='startChat' class='blue-button' target='_blank' onclick='Chat.controller.lobby.actions.accept_chat_room(\""+data.chat_session_id+"\")' ><span>Start Chatting</span></a> &nbsp; <a id='closeChat' class='blue-button' onclick='Chat.controller.lobby.actions.reject_chat_room(\""+data.chat_session_id+"\")'><span>Not At This Time</span></a></div></div></div><div id='modal_bg'></div>");
        
        
            var leftPos = ($(window).width()/2)-($('#modal').width()/2);
            var topPos = ($(window).height()/2)-($('#modal').height()/2);

            $('#modal').css('left',leftPos);
            $('#modal').css('top',topPos);

            // Play sound every 4 seconds
            Chat.views.popup.sound.play_sound();
            //Chat.views.popup.sound.soundIV = setInterval(Chat.views.popup.sound.play_sound, 2000);

        },
        
        reject_chat_room: function() {
        	Chat.views.popup.sound.stop_sound();
           	Chat.views.popup.remove_modal(); 
        },
        
        abort_chat: function() {
        	Chat.views.popup.sound.stop_sound();
        	Chat.views.popup.remove_modal();
        },
        
        show_contact_admin: function() {
            Chat.views.message.setMessage("<div class='system'>Client is contacting admin. The chat is paused. </div>");
        },
        
        notify_purchase_more_time: function() {
            Chat.views.message.setMessage("<div class='timer'>Notifying client to purcahse more time</div>");
        },
        purchase_more_time: function() {
            Chat.views.message.setMessage("<div class='timer'>The client is purchasing additional time and chat is still paused. Please wait until client back to chat. </div>");
        },
        
        add_stored_time: function() {
            Chat.views.message.setMessage("<div class='timer'>The client has paused the chat to add additional stored time. You may close this chat window. You will be notified when the user is ready to chat again.</div>");
        },
        
        add_free_time: function(total_free_time, free_time_added) {
            Chat.views.message.setMessage("<div class='timer'> You have added "+free_time_added +"minutes to this chat session for <span class='SpecialAlert'>FREE !</span>.  You have been rewarded <span class='SpecialAlert' >total: "+  total_free_time + " minutes </span> during this chat session.   </div>");
        },
        
        pban: function() {
            Chat.views.message.setMessage("<div class='timer'>"+ Chat.manager.room.client_info.username +" has been banned.</div>");
        },
        
        fban: function() {
            Chat.views.message.setMessage("<div class='timer'>"+ Chat.manager.room.client_info.username +" has been fully banned.</div>");
        },
        
        refund: function() {
            Chat.views.message.setMessage("<div class='system'>Return " + Chat.manager.room.client_info.username + "'s chat time to client. </div>");
        }
    },
    
    client: {
        
        join_new_chat_room: function () {
        	if (Chat.models.member.member_type == "client") {
            	Chat.views.message.setMessage("<div class='system'>The reader has been contacted & no minutes will be used or taken until the timer starts at the top of this screen.</div>");    
        		//Chat.views.message.setMessage("<div class='system'>You can <span id='chat_abort_room' class='SpecialAlert Pointer' onclick='Chat.manager.room.send.abort_chat'>abort this chat</span> before reader start</div>");
        	}
        },
        
        reject_chat: function(username) {
            Chat.views.message.setMessage("<div class='timer'>"+username+" is busy as this time.</div>");
        },
        
        abort_chat: function() {
        	Chat.views.message.setMessage("<div class='system'>Reader has no response.  The chat has been aborted.  Please try again later. </div>");
        },
        
        times_up_pause: function() {
            Chat.views.message.setMessage("<div class='system'>The chat session times up and is paused until additional time is purchased. </div>");
        },
        
        show_contact_admin: function() {
            Chat.views.message.setMessage("<div class='system'>Contact page is now open on new page.</div>");  
        },
        
        purchase_more_time: function() {
            Chat.views.message.setMessage("<div class='timer'>You can buy more time in the purchase page </div>");
        },
        
        add_stored_time: function() {
            Chat.views.message.setMessage("<div class='timer'>You will be redirected to the time add stored time page </div>");
        },
        
        add_free_time: function(total_free_time, free_time_added) {
            Chat.views.message.setMessage("<div class='timer'>"+ Chat.manager.room.reader_info.username  +"has added "+free_time_added +"minutes to this chat session for <span class='SpecialAlert'>FREE !</span>.  </div>");
        },
        
        pban: function() {
            Chat.views.message.setMessage("<div class='timer'>You have been banned.</div>");
        },
        
        fban: function() {
            Chat.views.message.setMessage("<div class='timer'>You have been banned.</div>");
        },
        
        refund: function() {
            Chat.views.message.setMessage("<div class='system'>" +Chat.manager.room.reader_info.username + " returned your time. </div>");
        },
        
        update_wait_time: function(remaining_time) {
        	$("#wait_seconds_left").html(remaining_time);
        },
        show_pending_dialog: function(reader_username) {
        	$( "#dialog-wait" ).dialog( "open" );
        	/*
        	if (disable_manual_quit) {
        		$(".ui-dialog-buttonpane button:contains('Quit This Chat Attempt')").button("disable");
        		$(".ui-dialog-buttonpane button:contains('Quit This Chat Attempt')").attr("disabled", true).addClass("ui-state-disabled");
        	}
        	*/
        	$( "#dialog-wait").dialog('option', 'title', "Waiting for response from " + reader_username);
        },
        
        hide_pending_dialog: function() {
        	$( "#dialog-wait" ).dialog( "close" );
        },
        show_reject_dialog: function() {
        	$('#dialog-reject').dialog("open");
        },
        show_abort_retry_dialog: function() {
        	$('#dialog-abort-retry').dialog("open");
        },
        show_offline_retry_dialog: function() {
        	$('#dialog-offline-retry').dialog("open");
        },
        show_unable_to_create_chat_max_manual_quit_dialog: function() {
        	$('#dialog-max-manual-quit').dialog("open");
        }
    },
    
    system: {
        
        leave_chat_room: function (member_type) {
            Chat.views.message.setMessage("<div class='system'>Chat has been terminated by the "+ member_type+ ".</div>");    
            if (Chat.models.member.member_type == 'reader') {
            	Chat.views.message.setMessage("<div class='system'>You will be redirect to your account page in 15 seconds.  You can also go here directly: <a href='/my_account'>My Account</a></div>");    
            }
        },
        
        show_typing: function(username) {
            $('#whosTyping').html("<span class='chat_username'>" + username  + "</span>&nbsp;&nbsp;is typing...");
        },
        
        clear_typing: function() {
            $('#whosTyping').html("");
        },
        
        show_message: function(data){
            Chat.views.message.system.clear_typing();
            
            //clearTimeout(whosTypingTimeout);
            if (data.message_type == 'system') {
                class_name = "chat_system";
                
                Chat.views.message.setMessage("<div class='"+class_name+"'><span class='chat_system_title'>System Message: </span>&nbsp;&nbsp;"+data.message+"</div>");
            } else {
                var class_name = "chat_" + data.member_type;
                
                Chat.views.message.setMessage("<div class='"+class_name+"'><span class='chat_username'>"+data.sender_name+":</span>&nbsp;&nbsp;"+data.message+"</div>");
            }
            
        },
        
        start: function() {
            
            Chat.views.message.setMessage("<div class='timer'>The timer is started</div>");
        },
        
        pause: function(username) {
            if (username) {
                Chat.views.message.setMessage("<div class='timer'>"+username+" has paused the timer</div>");
            } else {
                Chat.views.message.setMessage("<div class='timer'>The timer is paused</div>");
            }
        },
        
        disconnect_pause: function(username) {
            if (username) {
                Chat.views.message.setMessage("<div class='timer'>"+username+" has paused becuase of disconnection</div>");
            } else {
                Chat.views.message.setMessage("<div class='timer'>The timer is pause because of disconnection</div>");
            }
        },
        
        resume: function(username) {
            if (username) {
                Chat.views.message.setMessage("<div class='timer'>"+username+" has resumed the timer</div>");
            } else {
                Chat.views.message.setMessage("<div class='timer'>The timer is resumed</div>");
            }
        },
        
        refund: function() {
            Chat.views.message.setMessage("<div class='system'>Chat has been terminated & refunded by the reader</div> ");
        },
        
        close_window_message: function(timeout_var) {
            Chat.views.message.setMessage("<div class='system'>This chat window will close in " + (timeout_var/1000) + " seconds...</div>");    
        },
        
        set_timer: function(time_balance) {
            console.log("Set chat time: " + time_balance);
            $('#timerSpan').html(Chat.views.message.timerFormat(time_balance));
        },
        
        times_up_pause: function() {
            
        },
        
        disable_chat_form: function(){
            $('#chatForm textarea').attr('disabled','disabled');
            $('#chatForm input').attr('disabled','disabled');
        },
        enable_chat_form: function()
        {
            $('#chatForm textarea').removeAttr('disabled');
            $('#chatForm input').removeAttr('disabled');
        },
        show_add_stored_time: function(toggle)
        {
            if(toggle == true) {
                $("#addStoredTime").show();
            }else{
                $("#addStoredTime").hide();
            }
        },
        set_stored_time: function(minute) {
            console.log("Minute "  + minute);
            var stored_minute = Math.floor(minute);
            $('#maxAddStoredTime').html(stored_minute);
            /*
            $('#remainingStoredTime').html(minute);
            
            $('.add_stored_time').val(0);
            */
           if (Chat.is_mobile) {
               $( "#remainingStoredTimeInput" ).val(0);
           } else {
               var slider = $( "#remaining_stored_time-slider-range-max" ).slider({
                    range: "max",
                    min: 0,
                    max: stored_minute,
                    value: 1,
                    slide: function( event, ui ) {
                        $( "#remainingStoredTimeInput" ).val( ui.value );
                    }
                });
                $( "#remainingStoredTimeInput" ).val( $( "#remaining_stored_time-slider-range-max" ).slider( "value" ) );
                
                $('#remainingStoredTimeInput').change(function() {
                    
                    var value = $('#remainingStoredTimeInput').val();
                    console.log("value is " + value );
                    if (!isFinite(value)) {
                        value = 1;
                        $('#remainingStoredTimeInput').val(value);
                    }
                    if (value < 0) {
                        value = Math.abs(value);
                        $('#remainingStoredTimeInput').val(value);
                    }
                    
                    slider.slider( "value", value );
                });
           }
           
        },
        
        get_stored_time: function(minute) {
            Chat.views.message.setMessage("<div class='system'>Client is going to add more time from the remaining <span style='font-style: italic;'>"+ minute+ "</span> minutes stored time</div>");
        },
        show_lost_time: function(toggle)
        {
            if(toggle == true)
            {
                $("#lostTime").show();
            }
            else
            {
                $("#lostTime").hide();
            }
        },
        
        show_add_free_time: function(toggle) {
            if(toggle == true) {
                $("#addFreeTimeSection").show();
            } else{
                $("#addFreeTimeSection").hide();
            }
        }

    },
    
    ui: {
        adjust_input_message_textarea: function () {
            console.log("Calling adjust_input_message_textarea");
            if (Chat.is_mobile) {
                setTimeout(function() {
                    console.log("Adjusting");
                    $('.chat_option').css({"float":"left"});
                    $('#chat_window').css({"height":"190px"});
                    $('#input_message').css({"width": "95%", "margin-bottom":"5px", "font-size":"10px"});
                }, 500);
            }
            
        }
    },
    
    hideMenu: function(){
        $('#chatMenu').hide();
    },
    
    scrollToTop: function() {
        $('#chat_window').scrollTop($('#messageContainer').height());
    },
    
    setMessage: function(message) {
        $('#messageContainer').append(message);
        Chat.views.message.scrollToTop();
    },
    
    timerFormat: function(time_balance) {
        var sec_num = parseInt(time_balance, 10); // don't forget the second parm
        var hours   = Math.floor(sec_num / 3600);
        var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        var seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours   < 10) {hours   = "0"+hours;}
        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}
        var time    = hours+':'+minutes+':'+seconds;

        return time;
    }
    
};
Chat.views.error_log = {
    logs: [],
    
    show: function(err) {
        this.logs.push(err);
    },
    
    log: function(err) {
        this.logs.push(err);
    }
};
Chat.views.popup = {
    
    chat_interface_win: null,
    purchase_more_win: null,
    chat_interface_win_preview: false,
    purchase_more_win: false,
    
    sound: {
        sound_handler: null,
        sound_period: 1000,
        
        play_sound: function() {
            if (Chat.browser.name == "MSIE") {
                Chat.views.popup.sound.sound_handler = setInterval(function() {
                    var sound = document.getElementById('beep');
                    if (sound) {
                        sound.Play();
                    }
                    
                }, Chat.views.popup.sound.sound_period);
            } else {
                ion.sound.play("door_bell", {
                volume: 0.5,
                loop: 20
                });
            }
            
        },
        stop_sound: function(){
            if (Chat.browser.name == "MSIE") {
                clearInterval(Chat.views.popup.sound.sound_handler);
            } else {
                ion.sound.stop("door_bell");
            }
        }
    },
    
    open_site_page: function(page_name) {
        var page_url = "/";
        if (page_name) {
            page_url += page_name;  
        } 
        window.open(page_url, "_blank");
    },
    
    preview_open_window: function() {
        var w = 900; 
        var h = 750;
        var LeftPosition = (screen.width)?(screen.width-w)/2:100;
        var TopPosition=(screen.height)?(screen.height-h)/2:100;
        var settings='width='+w+',height='+h+',top='+TopPosition+',left='+LeftPosition+',scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes';
        Chat.views.popup.chat_interface_win = window.open('/chat/loading.html','PsyConChatWindow',settings);
        if (Chat.views.popup.chat_interface_win) {
            Chat.views.popup.chat_interface_win_preview = true;
        } else {
            alert('Please allow popup from our website by changing the browser setting. ');
        }
    },
    
    after_open_window: function() {
        Chat.views.popup.sound.stop_sound();
        Chat.views.popup.remove_modal();
        
    },
    
    open_window: function(chat_id, chat_session_id){
        Chat.views.popup.after_open_window();
        
        var w = 900; 
        var h = 750;
        /*
        if ($.browser.safari || $.browser.mozilla) {
            h = 700;
        }
        */
        
        var LeftPosition = (screen.width)?(screen.width-w)/2:100;
        var TopPosition=(screen.height)?(screen.height-h)/2:100;
        var settings='width='+w+',height='+h+',top='+TopPosition+',left='+LeftPosition+',scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes';
        
        if (Chat.views.popup.chat_interface_win_preview && Chat.views.popup.chat_interface_win) {
            Chat.views.popup.chat_interface_win.location = '/chat/chatInterface/index/' + chat_id + "/" + chat_session_id;
        } else {
            Chat.views.popup.chat_interface_win = window.open('/chat/chatInterface/index/' + chat_id + "/" + chat_session_id,'PsyConChatWindow',settings);
            if (!Chat.views.popup.chat_interface_win) {
                alert('Please allow popup from our website by changing the browser setting. ');
            }
        } 
        Chat.views.popup.chat_interface_win_preview = false;
        // refresh the page after 2 seconds
        setTimeout(function() {
            location.reload();
        }, 2000);
        
    },
    
    preview_open_purchase_more_time_window: function() {
        var w = 900; 
        var h = 750;
        var LeftPosition = (screen.width)?(screen.width-w)/2:100;
        var TopPosition=(screen.height)?(screen.height-h)/2:100;
        var settings='width='+w+',height='+h+',top='+TopPosition+',left='+LeftPosition+',scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes';
        Chat.views.popup.purchase_more_win = window.open('/chat/loading.html','PurchaseMoreTimeWindow',settings);
        if (Chat.views.popup.purchase_more_win) {
            Chat.views.popup.purchase_more_win_preview = true;
        } else {
            alert('Please allow popup from our website by changing the browser setting. ');
        }
        
    },
    
    open_purchase_more_time_window: function() {
        var w = 900; 
        var h = 750;
        /*
        if ($.browser.safari) {
            h = 700;
        }
          */      
        var LeftPosition = (screen.width)?(screen.width-w)/2:100;
        var TopPosition=(screen.height)?(screen.height-h)/2:100;
        var settings='width='+w+',height='+h+',top='+TopPosition+',left='+LeftPosition+',scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes';
        
        if (Chat.views.popup.chat_interface_win_preview && Chat.views.popup.chat_interface_win) {
            Chat.views.popup.purchase_more_win.location = '/chat/main/purchase_time/' + Chat.manager.room.reader_info.username;
        } else {
            Chat.views.popup.purchase_more_win = window.open('/chat/main/purchase_time/' + Chat.manager.room.reader_info.username ,'PurchaseMoreTimeWindow',settings);
            if (!Chat.views.popup.purchase_more_win) {
                alert('Please allow popup from our website by changing the browser setting. ');
            }
        }
        Chat.views.popup.purchase_more_win_preview = false;
    },
    
    resize_chat: function()
    {
        var windowHeight = $(window).height() - $('#navbar').outerHeight() - $('#footer').outerHeight() - 115;
        $('#chat_window').height(windowHeight);
        $("#footer textarea").width($(window).width() - 200);
    },
    
    remove_modal: function() {
        $('#modal').remove();
        $('#modal_bg').remove();
    }
};
Chat.controller.lobby = {
    
    chat_session_id: null, // only for client when doing chat attempt
    chat_redirect_url: null, 
    reader_id: null,
    reader_chat_attempt_accepted: false,
    disable_manual_quit: false,
    
    abort_chat_room: function() {
    	Chat.views.message.reader.abort_chat();
    },
    
    actions: {
    	// register reader in the lobby for socket message
	    register: function() {
	        // SHOULD run at start.
	        var member = Chat.models.member;
	        console.log("Going to emit message");
	        Chat.models.socket.io.emit('register', {member_id:member.member_id, member_id_hash:member.member_id_hash}, function(data){
	            console.log("Emitted message");
	            // emit to socket server.
	            if (data && data.status) {
	                Chat.views.error_log.log("Success: register member " + member.member_id + " to the lobby");
	            } else {
	                // show error. 
	                Chat.views.error_log.show(data);
	            }
	        });
	    },
    	reject_chat_room: function(chat_session_id) {
    		if (Chat.models.member.member_type == 'reader' && !Chat.controller.lobby.reader_chat_attempt_accepted) {
		        console.log("Reject chat room: " + chat_session_id);
		        Chat.views.popup.sound.stop_sound();
		        Chat.views.message.reader.reject_chat_room();
		        
		        var input = {
		            room_name   : chat_session_id,
		            member_id : Chat.models.member.member_id,
		            member_id_hash : Chat.models.member.member_id_hash
		        };
		        
		        Chat.models.socket.io.emit('reader_reject_chat_attempt', input, function(data){
		            // emit to socket server.
		            if (data && data.status) {
		           
		            	Chat.controller.lobby.chat_id = null;
		            	Chat.controller.lobby.chat_session_id = null;
		            	Chat.controller.lobby.chat_redirect_url = null;
		                Chat.views.message.reader.reject_chat_room();
		            } else {
		                // show error. 
		                Chat.views.error_log.show(data);
		            }
		        });
	        }
	    },

    	accept_chat_room: function(chat_session_id) {
    		if (Chat.models.member.member_type == 'reader') {
    			console.log("Accept chat room: " + chat_session_id);
		        Chat.views.popup.sound.stop_sound();
		        
		        var input = {
		            room_name   : chat_session_id,
		            member_id : Chat.models.member.member_id,
		            member_id_hash : Chat.models.member.member_id_hash
		        };
		        
		        Chat.models.socket.io.emit('reader_accept_chat_attempt', input, function(data){
		            // emit to socket server.
		            if (data && data.status) {
		                //wait for the join_new_chat_room io.  so client join first. 
		                Chat.controller.lobby.reader_chat_attempt_accepted = true;
		            } else {
		                // show error. 
		                Chat.views.error_log.show(data);
		            }
		        });
    		}
    		
    		//href='/chat/chatInterface/index/"+data.chat_id+"/"+data.chat_session_id+"'
    	},
    	
    	// send out chat attempt from client
    	chat_attempt: function(reader_id) {
    		
    		var topic = $('#topic').val();
    		var minutes = $('#minutes').val();
    		
    		if (! topic) {
    			alert("Topic cannot be empty");
    			return;
    		}
    		if (! parseInt(minutes, 10) > 0) {
    			alert("Minutes must be greater than 0");
    			return;
    		}
    		
    		if (Chat.models.member.member_type == 'client') {
		        var input = {
		        	reader_id: reader_id,
		        	member_id: Chat.models.member.member_id,
		        	member_id_hash: Chat.models.member.member_id_hash,
		        	topic: topic,
		        	minutes: minutes
		        };
		        
		        Chat.models.socket.io.emit('chat_attempt', input, function(data){
		            console.log("Emitted reject chat message");
		            // emit to socket server.
		            if (data && data.status) {
		            	// save properties about the room. 
		            	Chat.controller.lobby.chat_id = data.data.chat_id; 
		            	Chat.controller.lobby.chat_session_id = data.data.chat_session_id; 
		            	Chat.controller.lobby.chat_redirect_url = data.data.redirect_url; 
		            	Chat.controller.lobby.disable_manual_quit = data.data.disable_manual_quit;
		            	Chat.models.member.member_hash = data.data.member_hash;
		                Chat.views.message.client.show_pending_dialog(data.data.reader_username);
		            } else {
		                // show error. 
		                if (data.is_max_manual_quit) {
		                	Chat.views.message.client.show_unable_to_create_chat_max_manual_quit_dialog();
		                } else {
			                alert(data.message);
			                Chat.views.error_log.show(data);
		                }
		            }
		        });
    		}
    	},
    	
    	chat_attempt_abort: function(reader_id) {
    		if (Chat.models.member.member_type == 'client' && !Chat.controller.lobby.reader_chat_attempt_accepted) {
    			
    			if (! Chat.models.member.member_hash) {
    				alert('You have not submit for a chat yet.');
    				return;
    			}
    			
		        var input = {
		            room_name   : Chat.controller.lobby.chat_session_id,
		            member_hash : Chat.models.member.member_hash
		        };
		        
		        Chat.models.socket.io.emit('chat_attempt_abort', input, function(data){
		            if (data && data.status) {
		               // do nothing.  let io received message deal with it. 
		            } else {
		                // show error. 
		                Chat.views.error_log.show(data);
		            }
		        });
    		}
    	}
    },
    
    user_actions: function() {
      	$( "#dialog-wait" ).dialog({
	      	autoOpen: false,
	      	show: {
	        	effect: "blind",
	        	duration: 100
	      	},
	      	hide: {
	        	effect: "blind",
	        	duration: 10
	      	},
	      	closeOnEscape: false,
		    dialogClass: "noclose",
	      	resizable: false,
	      	height:200,
	      	width:450,
	      	modal: true,
	      	buttons: {
	        	"Quit This Chat Attempt": function() {
	        		// call client abort function
	        		if (! Chat.controller.lobby.disable_manual_quit) {
	        			Chat.controller.lobby.actions.chat_attempt_abort();
	        		}
	        	}
	    	}
	    });
	    
	    $( "#dialog-reject" ).dialog({
	      	autoOpen: false,
	      	show: {
	        	effect: "blind",
	        	duration: 100
	      	},
	      	hide: {
	        	effect: "blind",
	        	duration: 10
	      	},
	      	closeOnEscape: false,
		    dialogClass: "noclose",
	      	resizable: false,
	      	height:200,
	      	modal: true
	    });
	    
	    $( "#dialog-abort-retry" ).dialog({
	      	autoOpen: false,
	      	show: {
	        	effect: "blind",
	        	duration: 100
	      	},
	      	hide: {
	        	effect: "blind",
	        	duration: 10
	      	},
	      	closeOnEscape: false,
		    dialogClass: "noclose",
	      	resizable: false,
	      	height:200,
	      	modal: true
	    });
	    
	    $( "#dialog-offline-retry" ).dialog({
	      	autoOpen: false,
	      	show: {
	        	effect: "blind",
	        	duration: 100
	      	},
	      	hide: {
	        	effect: "blind",
	        	duration: 10
	      	},
	      	closeOnEscape: false,
		    dialogClass: "noclose",
	      	resizable: false,
	      	height:200,
	      	modal: true
	    });
	    
	    $( "#dialog-max-manual-quit" ).dialog({
	      	autoOpen: false,
	      	show: {
	        	effect: "blind",
	        	duration: 100
	      	},
	      	hide: {
	        	effect: "blind",
	        	duration: 10
	      	},
	      	closeOnEscape: false,
		    dialogClass: "noclose",
	      	resizable: false,
	      	height:200,
	      	modal: true
	    });
	    
	    $( window ).unload(function() {
	    	if (Chat.controller.lobby.chat_session_id) {
	    		if (Chat.models.member.member_type == 'client') {
	    			Chat.controller.lobby.actions.chat_attempt_abort(Chat.controller.lobby.reader_id);	
	    		} else {
	    			Chat.controller.lobby.actions.reject_chat_room(Chat.controller.lobby.chat_session_id);
	    		}
	    	}
		});
    },
    
    receive: {
    	join_new_chat_room: function(data) {
	        //console.log(" join_new_chat_room: recieve socket message " + JSON.stringify(data));
	        Chat.views.error_log.log(data);
	        var reader_id = data.reader_id;
	        
	        if (reader_id == Chat.models.member.member_id) {
	            console.log('Now player can join, then check and launch the window to prompt reader to accept or reject the chat');
	            // message to readers. 
	            Chat.controller.lobby.chat_id = data.chat_id; 
            	Chat.controller.lobby.chat_session_id = data.chat_session_id; 
            	Chat.controller.lobby.chat_redirect_url = data.redirect_url; 
	            window.location.href = Chat.controller.lobby.chat_redirect_url; //"/chat/chatInterface/index/" +data.reader_id+ "/" + data.chat_session_id;
	        }
	    },
	    
	    chat_attempt: function(data) {
	    	// client handle that in return call
	    	if (Chat.models.member.member_type == 'reader') {
		    	if (data && data.status) {
		    		// show the popup. 
		    		Chat.controller.lobby.chat_id = data.data.chat_id; 
	            	Chat.controller.lobby.chat_session_id = data.data.chat_session_id; 
	            	Chat.controller.lobby.chat_redirect_url = data.data.redirect_url; 
		    		Chat.views.message.reader.chat_attempt_for_reader(data.data);
		    	}
	    	}
	    },
	    
	    chat_attempt_ping: function(data) {
	    	// just put the seconds left
	    	if (Chat.models.member.member_type == 'client') {
		    	if (data && data.status) {
			    	$('#wait_seconds_left').html(data.wait_time_left);
		    	}
	    	}
	    },
	    
	    chat_attempt_abort: function(data) {
	    	if(data && data.status) {
	    		Chat.controller.lobby.chat_id = null;
            	Chat.controller.lobby.chat_session_id = null;
            	Chat.controller.lobby.chat_redirect_url = null;
            	
	    		if (Chat.models.member.member_type == 'client') {
	    			
	    			Chat.views.message.client.hide_pending_dialog();
	    			Chat.views.message.client.show_abort_retry_dialog();
	    		} else {
	    			// just close the popup
	    			Chat.views.message.reader.abort_chat();
	    		}
	    	}
	    },
	    
	    chat_attempt_offline: function(data) {
	    	if(data && data.status) {
	    		Chat.controller.lobby.chat_id = null;
            	Chat.controller.lobby.chat_session_id = null;
            	Chat.controller.lobby.chat_redirect_url = null;
            	
	    		if (Chat.models.member.member_type == 'client') {
	    			Chat.views.message.client.hide_pending_dialog();
	    			Chat.views.message.client.show_offline_retry_dialog();
	    		} else {
	    			// logout
	    			Chat.views.message.reader.abort_chat();
	    			window.location.href = "/main/logout";
	    		}
	    	}
	    	
	    },
	    
	    reader_accept_chat_attempt: function(data) {
	    	if (Chat.models.member.member_type == 'client') {
	    		Chat.controller.lobby.reader_chat_attempt_accepted = true;
	    		window.location.href = Chat.controller.lobby.chat_redirect_url; //"/chat/chatInterface/index/" +data.reader_id+ "/" + data.chat_session_id;
	    	}
	    },
	    
	    reader_reject_chat_attempt: function(data) {
	    	if (Chat.models.member.member_type == 'client') {
	    		Chat.controller.lobby.chat_id = null;
            	Chat.controller.lobby.chat_session_id = null;
            	Chat.controller.lobby.chat_redirect_url = null;
	    		Chat.views.message.client.hide_pending_dialog();
    			Chat.views.message.client.show_reject_dialog();
	    	}
	    }
	    
    },
    
    
    // socket message dispatcher
    dispatcher: function() {
        console.log("Dispatcher ");
        var io = Chat.models.socket.io; // Chat.socket

        //io.on('reader_abort_chat_room', Chat.controller.lobby.abort_chat_room); // receive that message in lobby
        
        io.on('chat_attempt', Chat.controller.lobby.receive.chat_attempt); 							// Reader
        io.on('chat_attempt_ping', Chat.controller.lobby.receive.chat_attempt_ping); 				// Client only
        io.on('chat_attempt_abort', Chat.controller.lobby.receive.chat_attempt_abort); 				// Client & Reader
        io.on('chat_attempt_offline', Chat.controller.lobby.receive.chat_attempt_offline); 			// Client & Reader
        io.on('reader_accept_chat_attempt', Chat.controller.lobby.receive.reader_accept_chat_attempt); // Client only
        io.on('reader_reject_chat_attempt', Chat.controller.lobby.receive.reader_reject_chat_attempt); // Client only
        io.on('join_new_chat_room', Chat.controller.lobby.receive.join_new_chat_room); // Reader only
    },
    
    init: function() {
        Chat.controller.lobby.dispatcher();
        Chat.controller.lobby.user_actions();
    }
};
Chat.run = {
    
    test: function() {
        // test if any missing module.d
    },
    
    room: function() {
        console.log("Done");
        // Chat init.
        Chat.init(chat_init_data);
        // room.  join & send notification to reader in lobby
        // now emit "join"
        console.log("Send join new chat room");
        Chat.manager.room.send.join_new_chat_room();
        Chat.views.popup.resize_chat();
        Chat.views.message.ui.adjust_input_message_textarea();
    },
    
    lobby: function() {
        console.log("Done");
        // Chat init.
        Chat.init(chat_init_data);
        if(Chat.models.member.member_type == "reader") {
        	Chat.controller.lobby.actions.register();
        }
    }
};

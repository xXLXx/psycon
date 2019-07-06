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
Chat.manager.room = {
    
    chat_id: null,
    chat_session_id: null,
    server_chat_time: 0,
    max_chat_length: 0,   // second   max time set. 
    chat_length: 0,     // current time elasped
    time_balance: 0,    // remaining time. 
    stored_time: -1,    // stored time. 
    
    client_info:{},
    reader_info:{},
    
    init: function(data) {
        console.log("room manager init");
        this.chat_id = data.chat_id || '';
        this.chat_session_id = data.chat_session_id || '';
        
        this.max_chat_length = data.max_chat_length || 0;
        this.chat_length = data.chat_length || 0;
        this.time_balance = data.time_balance || 0;
        
        this.client_info.username = data.client_username || '';
        this.client_info.first_name = data.client_first_name || '';
        this.client_info.last_name = data.client_last_name || '';
        this.client_info.dob = data.client_dob || '';
        
        this.reader_info.username = data.reader_username || '';
        
        // register dispatcher event.
        Chat.manager.room.dispatcher();
        // disable chat form frist, enable it when chat start. 
        Chat.views.message.system.disable_chat_form();
        
        // adjust chat input message box UI
        Chat.views.message.ui.adjust_input_message_textarea();
    },
    
    timer: {
        
        ok_send_typing: true,
        send_typing_interval: 3000, // 3 seconds
        sending_typing_timer: function() {
            var ok_send_typing = Chat.manager.room.timer.ok_send_typing;
            if (Chat.manager.room.timer.ok_send_typing) {
                Chat.manager.room.timer.ok_send_typing = false;
                setTimeout(function() {
                    Chat.manager.room.timer.ok_send_typing = true;
                }, Chat.manager.room.timer.send_typing_interval);
            }
            return ok_send_typing;
        },
        
        ok_display_typing: true,
        receive_typing_interval: 5000,
        receive_typing_timer: function() {
            var ok_display_typing = Chat.manager.room.timer.ok_display_typing;
            if (Chat.manager.room.timer.ok_display_typing) {
                Chat.manager.room.timer.ok_display_typing = false;
                setTimeout(function() {
                    if (Chat.manager.room.timer.ok_display_typing == false) {
                        // remove typing text. 
                        Chat.views.message.system.clear_typing();
                        Chat.manager.room.timer.ok_display_typing = true;
                    }
                }, Chat.manager.room.timer.receive_typing_interval);
            }
            return ok_display_typing;
        },
        
        reset_ok_display_typing: function() {
            // clear text
            Chat.views.message.system.clear_typing();
            Chat.manager.room.timer.ok_display_typing = true;
        },
        
        chat_timer_interval: 1000, // 1 second
        chat_timer_handler:null,
        chat_timer_record_interval: 5, // in  seconds
        chat_timer_correction_threshold: 5, // allowable client-server time diff
        chat_timer_has_started_before: false,
        
        init_chat_timer: function() {
            Chat.views.message.system.set_timer(Chat.manager.room.max_chat_length);
        },
        
        // start or resume
        start_chat_timer: function() {
            $('.pauseChatAnchor').show();
            $('.resumeChatAnchor').hide();
            Chat.views.message.system.enable_chat_form();
            //. if he has started, call start means resume
            if(Chat.manager.room.timer.chat_timer_has_started_before) {
                //Chat.views.message.system.resume();
            } else {
                // if he hasn't started before, if chat_length <= 0 means it is a fresh start.. else chat_length > 0 means user has been disconnected and rejoin. 
                if(Chat.manager.room.chat_length <= 0) {
                    console.log("Chat length <0, start ");
                    Chat.views.message.system.start();
                } else {
                    //Chat.views.message.system.resume();
                }
            }
            Chat.manager.room.timer.chat_timer_has_started_before = true;
            
            Chat.manager.room.timer.chat_timer_handler = setInterval(Chat.manager.room.timer.run_chat_timer, Chat.manager.room.timer.chat_timer_interval ); 
        },
        pause_chat_timer: function(paused_by) {
            $('.pauseChatAnchor').hide();
            $('.resumeChatAnchor').show();
            if (paused_by && paused_by != 'reader') {
	            Chat.views.message.system.clear_typing();
	            Chat.views.message.system.disable_chat_form();
            }
            clearInterval(Chat.manager.room.timer.chat_timer_handler);
            Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
        },
        resume_chat_timer: function() {
            Chat.manager.room.timer.start_chat_timer();
            Chat.manager.room.timer.unset_check_added_time();
        },
        end_chat_timer: function(is_soft_stop) {
            try {
                clearInterval(Chat.manager.room.timer.chat_timer_handler);
            } catch (e) {
                Chat.views.error_log.log(e);
            }
            
            
            $('.pauseChatAnchor').hide();
            $('.resumeChatAnchor').hide();
            Chat.views.message.system.clear_typing();
            Chat.views.message.system.disable_chat_form();
            Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
            
            if (is_soft_stop !== true) {
            	if (Chat.models.member.member_type == 'client') {
                	Chat.controller.room.close_window(15000);
              	} else {
              		setTimeout(function() {window.location.href = "/my_account";}, 15000);
              		
              	}
            } 
            
        },
        reset_chat_timer: function() {
            
        }, 
        
        correction: function() {
            //
            return; // TODO
            var time_diff = Chat.manager.room.chat_length - Chat.manager.room.server_chat_time;
            console.log("Check Timer Correction, times: " + Chat.manager.room.chat_length + " - " + Chat.manager.room.server_chat_time );
            if (Math.abs(time_diff) >= Chat.manager.room.timer.chat_timer_correction_threshold) {
                // correct to server time
                Chat.manager.room.chat_length = Chat.manager.room.server_chat_time;
                // show a message for time correction as system 
                Chat.views.message.system.show_message({message_type:'system', message:'Chat time correction by system monitor'});
                
                Chat.manager.room.time_balance = Chat.manager.room.time_balance +  time_diff;
                console.log("Proceed  Timer Correction , chat time: " + Chat.manager.room.chat_time);
                Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
            }
        },
        
        force_corretion: function(server_chat_time) {
            console.log("Force Timer Correction ");
            Chat.manager.room.server_chat_time = server_chat_time;
            var time_diff = Chat.manager.room.chat_length - Chat.manager.room.server_chat_time;
            
            if (Math.abs(time_diff) > 0) {
                Chat.manager.room.chat_length = Chat.manager.room.server_chat_time;
                // show a message for time correction as system 
                Chat.views.message.system.show_message({message_type:'system', message:'Chat time correction by system monitor'});
                
                Chat.manager.room.time_balance = Chat.manager.room.time_balance +  time_diff;
                Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
            }
        },
        
        run_chat_timer: function() {
            //--- Every set interval record chat length - !! ONLY IF READER
            console.log("MOD " + Chat.manager.room.chat_length + " > " + Chat.manager.room.chat_length%Chat.manager.room.timer.chat_timer_record_interval);
            if (Chat.manager.room.time_balance > 0 && 
                Chat.manager.room.time_balance % Chat.manager.room.timer.chat_timer_record_interval == 0 && 
                Chat.models.member.member_type == 'reader'){
                // send record chat time
                console.log("Send record chat");
                Chat.manager.room.send.record_chat();
            }

            // 5 minutes warning
            if (Chat.manager.room.time_balance == 300){
                //objectWrite({ type : 'message', 'className' : 'system', 'message' : "5 Minute Warning: There are only 5 minutes left in your current chat session.<br />We recommend purchasing more time using the settings menu at the top. " });
                //Chat.manager.room.send.message({message_type:'system', message: "5 Minute Warning: There are only 5 minutes left in your current chat session.<br />We recommend purchasing more time using the settings menu at the top. " });
                if (Chat.models.member.member_type == 'client'){
                    Chat.views.message.system.show_message({message_type:'system', message: "5 Minute Warning: There are only 5 minutes left in your current chat session.<br />We recommend purchasing more time or add stored time using the settings menu at the top. " });
                }
            } else if (Chat.manager.room.time_balance == 120){
                // 2 minutes warning
                //objectWrite({ type : 'message', 'className' : 'system', 'message' : "2 Minute Warning: There are only 2 minutes left in your current chat session.<br />We recommend purchasing more time using the settings menu at the top. " });
                //Chat.manager.room.send.message({message_type:'system', message: "2 Minute Warning: There are only 2 minutes left in your current chat session.<br />We recommend purchasing more time using the settings menu at the top. " });
                if (Chat.models.member.member_type == 'client'){
                    Chat.views.message.system.show_message({message_type:'system', message: "2 Minute Warning: There are only 2 minutes left in your current chat session.<br />We recommend purchasing more time or add stored time using the settings menu at the top. " });
                }
            } else if (Chat.manager.room.time_balance == 60){
                // 1 minute warning
                //objectWrite({ type : 'message', 'className' : 'system', 'message' : "1 Minute Warning: There is only 1 minute left in your current chat session.<br />We recommend purchasing more time using the settings menu at the top. " });
                //Chat.manager.room.send.message({message_type:'system', message: "1 Minute Warning: There is only 1 minute left in your current chat session.<br />We recommend purchasing more time using the settings menu at the top. " });
                if (Chat.models.member.member_type == 'client'){
                    Chat.views.message.system.show_message({message_type:'system', message: "1 Minute Warning: There is only 1 minute left in your current chat session.<br />We recommend purchasing more time or add stored time using the settings menu at the top. " });
                } else {
                    Chat.views.message.system.show_message({message_type:'system', message: "Client is running out of time in one minute. "});
                }
            } else if(Chat.manager.room.time_balance <= 0){
                Chat.manager.room.timer.end_chat_timer(true);   // just softly end chat for both reader and client. 
                // Timer Ends
                if(Chat.models.member.member_type == 'reader'){
                   // just end it. 
                    Chat.manager.room.send.leave_chat();
                } 
            }
            
            if (Chat.manager.room.time_balance == 0) {
                Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
            } else if (Chat.manager.room.time_balance > 0) {
                Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
                Chat.manager.room.time_balance -= (Chat.manager.room.timer.chat_timer_interval/1000);      // Remaining TIME LEFT
                Chat.manager.room.chat_length += (Chat.manager.room.timer.chat_timer_interval/1000);    // Time also elapsed. 
            } else {
                //Chat.views.message.system.set_timer(Chat.manager.room.chat_time);
                Chat.manager.room.time_balance = 0;
            }
        },
        update_time_balance: function(data) {
           // there is a socket message to update time balance 
            // ignore for now. 
        },
        
        disconnection_handler: null,
        default_grace_period: 60,
        is_disconnected_on: false,
        start_disconnection_countdown: function(grace_period) {
            if (!grace_period) {
                grace_period = Chat.manager.room.timer.default_grace_period;
            }
            grace_period *= 1000; // turn into milli second
            Chat.manager.room.timer.is_disconnected_on = true;
            
            setTimeout(Chat.manager.room.timer.run_disconnection_countdown, grace_period ); 
        },
        set_disconnection_off: function() {
            Chat.manager.room.timer.is_disconnected_on = false;
        },
        run_disconnection_countdown: function() {
            if (Chat.manager.room.timer.is_disconnected_on) {
                // fire end Chat.
                Chat.manager.room.send.leave_chat();
            }
        },
        
        check_added_time_timer_interval: 5000, // 5 seconds
        check_added_time_timer_handler:null,
        // check for added time timer
        set_check_added_time: function() {
            if (Chat.manager.room.timer.check_added_time_timer_handler) {
                // do nothing
            } else {
                Chat.manager.room.timer.check_added_time_timer_handler = setInterval( Chat.manager.room.timer.update_added_time, Chat.manager.room.timer.check_added_time_timer_interval);
            }
        },
        
        unset_check_added_time: function() {
            if (Chat.manager.room.timer.check_added_time_timer_handler) {
                clearInterval(Chat.manager.room.timer.check_added_time_timer_handler);
            }
        },
        update_added_time: function () {
            // call the get stored time. 
            Chat.manager.room.send.refresh_stored_time();
        }
        /* ,
        
        chat_pending_max_time: 45,
        chat_pending_timer_interval: 1000,
        chat_pending_timer_handler: null,
        set_chat_pending_timer: function() {
        	if (! Chat.manager.room.timer.chat_pending_timer_handler) {
        		Chat.manager.room.timer.chat_pending_timer_handler = setInterval(Chat.manager.room.timer.update_chat_pending_time, Chat.manager.room.timer.chat_pending_timer_interval);
        	}
        },
        unset_chat_pending_timer: function() {
        	if (Chat.manager.room.timer.chat_pending_timer_handler) {
        		Chat.manager.room.timer.chat_pending_max_time = 0;
                clearInterval(Chat.manager.room.timer.chat_pending_timer_handler);
            }
        },
        
        update_chat_pending_time: function() {
        	// update the pending time. 
        	if (Chat.manager.room.timer.chat_pending_max_time > 0) {
	        	Chat.manager.room.timer.chat_pending_max_time --;
	        	
	        	Chat.views.message.client.update_wait_time(Chat.manager.room.timer.chat_pending_max_time);
        	} else {
        		Chat.manager.room.timer.chat_pending_max_time = 0;
        		Chat.views.message.client.update_wait_time(Chat.manager.room.timer.chat_pending_max_time);
        		Chat.manager.room.timer.unset_chat_pending_timer();
        	}
        }
        */
    },
    
    send: {
        
        join_new_chat_room: function() {
            console.log('chat id' + Chat.manager.room.chat_id + " chat session id: " + Chat.manager.room.chat_session_id);
            var input = {
                chat_id: Chat.manager.room.chat_id,
                chat_session_id: Chat.manager.room.chat_session_id,
                member_hash: Chat.models.member.member_hash
            };
            console.log("Sending join new chat room ");
            Chat.models.socket.io.emit('join', input, function(data) {
                Chat.views.error_log.log(data); // just log
                // show message
                if (data.status) {
                    // 
                    Chat.manager.room.timer.init_chat_timer();
                    Chat.views.message.client.join_new_chat_room();
    

                } else {
                    Chat.views.error_log.show(data);
                }
            });
        }, 
        
        leave_chat: function () {
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            Chat.models.socket.io.emit('leave', input, function(data){
                if (data.status) {
                    // TODO: adjust time.
                    Chat.manager.room.timer.end_chat_timer();
                    Chat.views.message.system.leave_chat_room(data.member_type);
                } else {
                    Chat.views.error_log.show(data);
                }
            });
        },
        
        start: function() {
            console.log("start emit " );
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            Chat.models.socket.io.emit('start', input, function(data){
                console.log("Received Start ");
                Chat.views.error_log.log(data);
                if (data.status) {
                    // UI
                    $('.endChatAnchor').show();
                    $('.addStoredTime').show();
                    var start_time = data.start_time; // in seconds
                    
                    Chat.manager.room.max_chat_length = data.max_chat_length;
                    Chat.manager.room.chat_length = data.chat_length;
                    Chat.manager.room.time_balance = data.time_balance;

                    Chat.manager.room.timer.start_chat_timer();
                } else {
                    Chat.views.error_log.show(data);
                }
            });
            
        },
        
        pause: function() {
            console.log("pause emit " );
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };      
            Chat.models.socket.io.emit('pause', input, function(data){
                if (data.status) {
                    Chat.manager.room.chat_length = data.chat_length;
                    Chat.manager.room.time_balance = data.time_balance;
                    
                    Chat.manager.room.timer.pause_chat_timer(data.paused_by);
                    
                    var username = '';
                    if (data.member_type == 'client') {
                        username = Chat.manager.room.client_info.username;
                    } else {
                        username = Chat.manager.room.reader_info.username;
                    }
                    Chat.views.message.system.pause(username);
                } else {
                    Chat.views.error_log.show(data);
                }
            });
            
        },
        
        resume: function(data) {
            Chat.views.message.system.show_add_stored_time(false);
            Chat.views.message.system.show_lost_time(false);
            console.log("resume emit " );
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            Chat.models.socket.io.emit('resume', input, function(data){
                if (data.status) {
                    Chat.manager.room.chat_length = data.chat_length;
                    Chat.manager.room.time_balance = data.time_balance;
                    
                    Chat.manager.room.timer.resume_chat_timer();
                     
                    var username = '';
                    if (data.member_type == 'client') {
                        username = Chat.manager.room.client_info.username;
                    } else {
                        username = Chat.manager.room.reader_info.username;
                    }
                    Chat.views.message.system.resume(username);
                    
                } else {
                    Chat.views.error_log.show(data);
                }
            });
            
        },
        
        message: function(data) {
            console.log("message emit " );
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash,
                message     : data.message
            };
            if (data.message_type) {
                input.message_type = data.message_type;
            }
            
            Chat.models.socket.io.emit('message', input, function(data){
                //console.log("Message is emitted. response is "+ JSON.stringify(data) );
                if (data.status) {
                    //var time_balance = data.time_balance; // in seconds
                    //Chat.Message.client.leave_chat(time_balance);
                    if (Chat.models.member.member_type == "reader") {
                        
                        Chat.views.message.system.show_message({member_type:'reader', message:input.message, sender_name:data.sender_name});
                    } else {
                        Chat.views.message.system.show_message({member_type:'client', message:input.message, sender_name:data.sender_name});
                    }
                    
                } else {
                    Chat.views.error_log.show(data);
                }
            });
        },
        
        typing: function(data) {
            console.log("Typing emit " );
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            
            if (Chat.manager.room.timer.sending_typing_timer() ) {
                // 
                Chat.models.socket.io.emit('typing', input, function(data){
                    if (data.status) {
                       // do not show anything in sender.  NOt even error
                    } else {
                        // just log error , not show
                        Chat.views.error_log.log(data);
                    }
                });
            }
            
        },
        
        record_chat: function(data) {
            console.log("Typing record_chat " );
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            
            Chat.models.socket.io.emit('record_chat', input, function(data){
                if (data.status) {
                   Chat.manager.room.time_balance = data.time_balance;
                   Chat.manager.room.chat_length = data.chat_length;
                   Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
                   //Chat.manager.room.timer.correction();
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }
            });
        },
        
        contact_admin: function() {
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            
            Chat.models.socket.io.emit('contact_admin', input, function(data){
                // do nothing, just notify the reader
                if (data.status) {
                
                    Chat.manager.room.chat_length = data.chat_length;
                    Chat.manager.room.time_balance = data.time_balance;
                    
                    Chat.manager.room.timer.pause_chat_timer(data.paused_by);
                    
                    var username = Chat.manager.room.client_info.username;
                    Chat.views.message.system.pause(username);
                    
                } else {
                    Chat.views.error_log.show(data);
                }
            });
            
            Chat.views.message.client.show_contact_admin();
            //Chat.views.popup.open_site_page('contact');
        },
        
        purchase_time: function(data) {
            console.log("notifying purchase time " );
            
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            // move to display the popup first. 
            //Chat.views.popup.open_purchase_more_time_window();
            
            Chat.models.socket.io.emit('purchase_time', input, function(data){
                if (data.status) {
                    if (Chat.models.member.member_type == 'client') {
                        Chat.views.message.client.purchase_more_time();
                        //Chat.views.popup.open_purchase_more_time_window();
                        Chat.manager.room.timer.set_check_added_time();
                        $('#refreshPurchasedTime').show();
                    }
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }
                
                // pause timer display
                if (data.status == true && data.is_already_paused !== false) {
                    // pause
                    if (data.chat_length && data.time_balance) {
                        Chat.manager.room.chat_length = data.chat_length;
                        Chat.manager.room.time_balance = data.time_balance;
                    }
                    Chat.manager.room.timer.pause_chat_timer(data.paused_by);
                } 
            });
        },
        
        get_stored_time: function() {
            Chat.manager.room.send.pause();
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash,
                is_bypass_message : false
            };
            
            Chat.models.socket.io.emit('get_stored_time', input, function(data){
                if (data.status) {
                    var stored_minute = Math.floor(data.stored_time); 
                    Chat.manager.room.stored_time = stored_minute;
                        
                    Chat.views.message.system.show_add_stored_time(true);
                    $('#refreshPurchasedTime').hide();
                    Chat.views.message.system.set_stored_time(data.stored_time);
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }
            });
        },
        
        refresh_stored_time: function() {
            //Chat.manager.room.send.pause();
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash,
                is_bypass_message : true
            };
            
            Chat.models.socket.io.emit('get_stored_time', input, function(data){
                if (data.status) {
                    var stored_minute = Math.floor(data.stored_time); 
                    if ( Chat.manager.room.stored_time != stored_minute ) {
                        Chat.manager.room.stored_time = stored_minute;
                        Chat.views.message.system.set_stored_time(data.stored_time);
                    } 
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }
            });
        },
        
        
        add_stored_time: function(data) {
            console.log("notifying add stored time " );
            
            var added_time = $('#remainingStoredTimeInput').val();
            
            if (added_time <= 0) {
                Chat.views.error_log.show("You cannot add time less than one minute");
                return;
            }
            
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash,
                added_time  : added_time
            };
            
            Chat.models.socket.io.emit('add_stored_time', input, function(data){
                if (data.status) {
                    Chat.manager.room.max_chat_length = data.max_chat_length;
                    Chat.manager.room.chat_length = data.chat_length;
                    Chat.manager.room.time_balance = data.time_balance;
                    Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }
                // resume
                    Chat.manager.room.send.resume();
            });
        },
        
        add_free_time: function() {
            var free_time = $('#freeTimeInput').val();
            
            if (free_time <= 0) {
                Chat.views.error_log.show("You cannot add free time less than one minute");
                return;
            }
            
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash,
                free_time  : free_time
            };
            
            Chat.models.socket.io.emit('add_free_time', input, function(data){
                if (data.status) {
                    Chat.manager.room.max_chat_length = data.max_chat_length;
                    Chat.manager.room.chat_length = data.chat_length;
                    Chat.manager.room.time_balance = data.time_balance;
                    Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
                    Chat.views.message.reader.add_free_time(data.total_free_time, data.free_time_added);
                    $('#freeTimeInput').val(0);
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }

            });
        },
        
        pban: function(data) {
            console.log("pban " );
           
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            
            Chat.models.socket.io.emit('pban', input, function(data){
                if (data.status) {
                    Chat.views.message.reader.pban();
                    Chat.manager.room.timer.end_chat_timer();
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }
            });
        },
        
        fban: function(data) {
            console.log("fban " );
           
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            
            Chat.models.socket.io.emit('fban', input, function(data){
                if (data.status) {
                    Chat.views.message.reader.fban();
                    Chat.manager.room.timer.end_chat_timer();
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }
            });
        },
        
        refund: function() {
            console.log("refund " );
           
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            
            Chat.models.socket.io.emit('refund', input, function(data){
                if (data.status) {
                    Chat.views.message.reader.refund();
                    Chat.manager.room.timer.end_chat_timer();
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }
            });
        },
        
        lost_time: function(lost_time) {
            console.log("lost_time " );
           
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash,
                lost_time   : (lost_time * 60) /* in second */
            };
            
            Chat.models.socket.io.emit('lost_time', input, function(data){
                if (data.status) {
                    Chat.manager.room.chat_length = data.chat_length;
                    Chat.manager.room.time_balance = data.time_balance;
                    Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
                    Chat.views.message.system.show_lost_time(false);
                    Chat.manager.room.send.resume();
                } else {
                    // just log error , not show
                    Chat.views.error_log.log(data);
                }
            });
        },
        
        check_time_balance: function(callback) {
            console.log("Typing emit " );
            var input = {
                room_name   : Chat.manager.room.chat_session_id,
                member_hash : Chat.models.member.member_hash
            };
            
            if (Chat.manager.room.timer.sending_typing_timer() ) {
                // 
                Chat.models.socket.io.emit('check_time_balance', input, function(data){
                    if (data.status) {
                       // do not show anything in sender.  NOt even error
                       if (callback) {
                           Chat.manager.room.max_chat_length = data.max_chat_length;
                           Chat.manager.room.chat_length = data.chat_length;
                           Chat.manager.room.chat_time = data.time_balance;
                           callback(null, data);
                       } else {
                           return data;
                       }
                    } else {
                        // just log error , not show
                        Chat.views.error_log.log(data);
                        if (callback) {
                            callback(data);
                        }
                    }
                });
            }
        }
        
    },
    
    // receiving message function
    receive: {
        // on receiving the call for join or create new chat room 
        
        reader_join_new_chat_room: function(data) {
            console.log(" raeder join new chat room");
            Chat.views.error_log.log(data);
            var reader_id = data.reader_id;
            
            // then it should send start chat now. 
            Chat.manager.room.timer.init_chat_timer();
            Chat.manager.room.send.start();
        },
        
        reject_chat: function(data) {
            console.log(" reader reject the chat request");
            
            Chat.views.message.system.disable_chat_form();
            Chat.views.message.client.reject_chat(data.reader_username);
            Chat.controller.room.close_window();
            //if(object.chat_id == chatSessionId){
        },
        
        abort_chat_room: function(data) {
            console.log(" client abort the chat request");
            if (Chat.models.member.member_type == "client") {
            	Chat.views.message.system.disable_chat_form();
	            Chat.views.message.client.abort_chat();
	            Chat.controller.room.close_window();
            } else {
            	Chat.views.message.reader.abort_chat();
            }
            //if(object.chat_id == chatSessionId){
        },
        
        leave_chat_room: function(data) {
            if (data.status) {
                Chat.manager.room.timer.end_chat_timer();
                Chat.views.message.system.leave_chat_room(data.member_type);
            } else {
                Chat.views.error_log.show(data);
            }
            
        },
        start: function(data) {
            console.log("start now " + data);
            
            $('.endChatAnchor').show();
            $('.addStoredTime').show();
            $('.pauseChatAnchor').show();
            $('.resumeChatAnchor').hide();
            
            Chat.manager.room.max_chat_length = data.max_chat_length;
            Chat.manager.room.chat_length = data.chat_length;
            Chat.manager.room.chat_time = data.time_balance;
            Chat.manager.room.timer.start_chat_timer();
        },
        
        pause: function(data) {
            console.log("pause now " + data);
            if (data.status) {
                if (! data.pause_no_time) {
                    Chat.manager.room.chat_length = data.chat_length;
                    Chat.manager.room.time_balance = data.time_balance;
                }
                Chat.manager.room.timer.pause_chat_timer(data.paused_by);
                
                var username = '';
                if (data.member_type == 'client') {
                    username = Chat.manager.room.client_info.username;
                } else {
                    username = Chat.manager.room.reader_info.username;
                }
                
                if (data.is_by_disconnection) {
                    Chat.views.message.system.disconnect_pause(username);
                } else {
                    Chat.views.message.system.pause(username);
                }
                
            } else {
                Chat.views.error_log.show(data);
            }
        },
        
        resume: function(data) {
            console.log("resume now " + data);
            if (data.status) {
                Chat.manager.room.chat_length = data.chat_length;
                Chat.manager.room.time_balance = data.time_balance;
                
                Chat.manager.room.timer.resume_chat_timer();
                 
                var username = '';
                if (data.member_type == 'client') {
                    username = Chat.manager.room.client_info.username;
                } else {
                    username = Chat.manager.room.reader_info.username;
                }
                Chat.views.message.system.resume(username);
                
            } else {
                Chat.views.error_log.show(data);
            }
        },
        
        message: function(data) {
            //{sender:'client', sender_id:cleint_id, message:message}
            //console.log("receive message data: " + JSON.stringify(data));
            if (data.status) {
                //var time_balance = data.time_balance; // in seconds
                //Chat.Message.client.leave_chat(time_balance);
                Chat.manager.room.timer.reset_ok_display_typing();
                if (data.message_type && data.message_type == 'system') {
                    Chat.views.message.system.show_message({member_type:'system', message:data.message});
                } else if (data.sender == "reader") {
                    
                    Chat.views.message.system.show_message({member_type:'reader', message:data.message, sender_name:data.sender_name});
                } else {
                    Chat.views.message.system.show_message({member_type:'client', message:data.message, sender_name:data.sender_name});
                }
                
            } else {
                Chat.views.error_log.show(data);
            }
        },
        
        typing: function(data) {
            //console.log("receive typing data" + JSON.stringify(data));
            if (data.status) {
                if (data.sender_name) {
                    if (Chat.manager.room.timer.receive_typing_timer() ) {
                        Chat.views.message.system.show_typing(data.sender_name);
                    }
                }
            }
        },
        
        record_chat: function(data) {
            //console.log("receive record_chat" + JSON.stringify(data));
            // record chat time  
            if (data.status) {
                Chat.manager.room.time_balance = data.time_balance;
                Chat.manager.room.chat_length = data.chat_length;
                Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
            }
        },
        
        
        
        disconnect_chat_room: function(data) {
            console.log("disconnect_chat_room " + data);
            // pause Timer First
            Chat.manager.room.timer.pause_chat_timer();
            if (data.time_balance) {
                Chat.manager.room.timer.force_corretion(data.time_balance);
            }
            Chat.manager.room.timer.start_disconnection_countdown(data.grace_period);
        },
        
        client_purchase_time: function(data) {
            if (data) {
                if (data.status == true && data.is_already_paused !== false) {
                    // pause
                    if (data.chat_length && data.time_balance) {
                        Chat.manager.room.chat_length = data.chat_length;
                        Chat.manager.room.time_balance = data.time_balance;
                    }
                    Chat.manager.room.timer.pause_chat_timer(data.paused_by);
                } 
                
                if (Chat.models.member.member_type == 'reader') {
                    Chat.views.message.reader.purchase_more_time();
                }
            }
        },
        client_add_stored_time: function(data) {
            if (data.status) {
                Chat.manager.room.max_chat_length = data.max_chat_length;
                Chat.manager.room.chat_length = data.chat_length;
                Chat.manager.room.time_balance = data.time_balance;
                Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
            }
        },
        get_stored_time: function(data) {
            if (data.status && data.is_bypass_message === false) {
                Chat.views.message.system.get_stored_time(data.stored_time);  
            }
            
        },
        reader_add_free_time: function (data) {
            if (data.status) {
                Chat.manager.room.max_chat_length = data.max_chat_length;
                Chat.manager.room.chat_length = data.chat_length;
                Chat.manager.room.time_balance = data.time_balance;
                Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
                Chat.views.message.client.add_free_time(data.total_free_time, data.free_time_added);
            }  
        },
        contact_admin: function(data) {
            if (data.status) {
                Chat.manager.room.chat_length = data.chat_length;
                Chat.manager.room.time_balance = data.time_balance;
                Chat.manager.room.timer.pause_chat_timer(data.paused_by);
                
                Chat.views.message.reader.show_contact_admin();
            }
        },
        pban: function(data) {
            Chat.views.message.client.pban();
            Chat.manager.room.timer.end_chat_timer();
        },
        fban: function(data) {
            Chat.views.message.client.fban();
            Chat.manager.room.timer.end_chat_timer();
        }, 
        refund: function(data) {
            if (data.status) {
                Chat.views.message.client.refund();
                Chat.manager.room.timer.end_chat_timer();
            } else {
                Chat.views.error_log.show(data);
            }
        },
        lost_time: function(data) {
            if (data.status) {
                Chat.manager.room.chat_length = data.chat_length;
                Chat.manager.room.time_balance = data.time_balance;
                Chat.views.message.system.set_timer(Chat.manager.room.time_balance);
                Chat.views.message.system.show_lost_time(false);
                // there will be resume call. wait for it. 
            } else {
                Chat.views.error_log.show(data);
            }
        }
    },
    
    
    
    // socket message dispatcher
    dispatcher: function() {
        console.log("Dispatcher ");
        var io = Chat.models.socket.io; // Chat.socket
        
        io.on('reader_join_new_chat_room', Chat.manager.room.receive.reader_join_new_chat_room);
        io.on('reject_chat', Chat.manager.room.receive.reject_chat);		// OUTDATED
       // io.on('abort_chat_room', Chat.manager.room.receive.abort_chat_room);
        io.on('leave_chat_room', Chat.manager.room.receive.leave_chat_room);
        io.on('start', Chat.manager.room.receive.start);
        io.on('pause', Chat.manager.room.receive.pause);
        io.on('resume', Chat.manager.room.receive.resume);
        io.on('message', Chat.manager.room.receive.message);
        io.on('client_purchase_time', Chat.manager.room.receive.client_purchase_time);
        io.on('client_add_stored_time', Chat.manager.room.receive.client_add_stored_time);
        io.on('get_stored_time', Chat.manager.room.receive.get_stored_time);
        io.on('refund', Chat.manager.room.receive.refund);
        io.on('lost_time', Chat.manager.room.receive.lost_time);
        io.on('reader_add_free_time', Chat.manager.room.receive.reader_add_free_time);
        io.on('pban', Chat.manager.room.receive.pban);
        io.on('fban', Chat.manager.room.receive.fban);
        io.on('typing', Chat.manager.room.receive.typing);
        io.on('record_chat', Chat.manager.room.receive.record_chat);
        io.on('disconnect_chat_room', Chat.manager.room.receive.disconnect_chat_room);
        io.on('check_time_balance', Chat.manager.room.timer.update_time_balance);
        io.on('contact_admin', Chat.manager.room.receive.contact_admin);
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
Chat.controller.room = {
    
    previous_char: '',
    
    start_timer: function() {
        
        
    },
    
    pause_timer: function() {
        
        
    },
    
    send_message: function() {
        
        
    },
    
    detect_typing: function() {
        
    },
    
    endChat: function(){
        Chat.views.message.hideMenu();
        // chat has a session
        if(Chat.manager.room.chat_session_id){
            if(Chat.member_type=='reader'){
                // do something 
            }
        }
    },

    showLostTime: function(toggle)
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


    //--- Close Chat Window
    close_window: function (timeout_var){
        timeout_var = timeout_var || 10000;

        Chat.views.message.system.clear_typing();
        Chat.views.message.system.disable_chat_form();
        Chat.controller.room.hide_all_user_actions();
        // just show a closing message
        Chat.views.message.system.close_window_message(timeout_var);
        //timeout_var = timeout_var || window_close_time;
        //objectWrite({ type : 'message', 'className' : 'system', 'message' : "This chat window will close in " + (timeout_var/1000) + " seconds..." });

        setTimeout(function(){
            window.open('','_self').close();
            //window.close();
        }, timeout_var);

    },
    
    actions: {
        
        on_message_send: function() {
            console.log("register message send onclick");
            $("#chatForm_send").click(function(e)
            {
                console.log("On Click send");
                e.preventDefault();
    
                Chat.controller.room.actions.message_send();
            });
            
            $("#input_message").keypress(function(e) {
                
                    
                if(e.which == 13 ) {
                    // enter
                    var message = $("#input_message");
                    var send_message = $.trim(message.val());
                    
                    if (send_message.length >= 1 ) {
                        Chat.controller.room.actions.message_send();
                    }
                } else {
                    var message = $("#input_message");
                    var send_message = $.trim(message.val());
                
                    if (send_message == '') {
                        Chat.controller.room.previous_char = '';
                        // nothing else happen
                    } else {
                        if (send_message != Chat.controller.room.previous_char) {
                            // yes. typing
                            Chat.manager.room.send.typing();
                            Chat.controller.room.previous_char = send_message;
                        }
                    } 
                }
            });
        
        },
        
        message_send: function() {
            var message = $("#input_message");
                
            var send_message = $.trim(message.val());
            if(send_message)
            {
                //$.post('/chat/chatInterface/log_message/'+chatSessionId, { message : $.trim(message.val()) });
                //sendObject('message', (isReader ? "reader" : "client"), $.trim(message.val()));
                var send_message = $.trim(message.val());
                console.log("Send Message to server: " + send_message );
                message.val("");    
                
                Chat.manager.room.send.message({message:send_message});
            }
        }
    },
    
    hide_all_user_actions: function() {
        $(".pauseChatAnchor").hide();
        $(".resumeChatAnchor").hide();        
        //$(".endChatAnchor").hide();
        
        if (Chat.models.member.member_type == 'client') {
            //$(".purchaseMoreTime").hide();
            $(".addStoredTime").hide();
            $(".contactAdmin").hide();
        } else {
            $(".personalBanUserAnchor").hide();
            $(".fullBanUserAnchor").hide();
            
            $(".refundChatAnchor").hide();
            $(".addLostTime").hide();
            $("#lostTimeBtn").hide();
        }
    },
    
    user_actions: function() {
        $(".pauseChatAnchor").click(function(){
            Chat.manager.room.send.pause();
        });
        
        $(".resumeChatAnchor").click(function(){
            Chat.manager.room.send.resume();
        });
        
        $(".endChatAnchor").click(function() {
            if(confirm("Are you sure you want to end this chat?")) {
                Chat.manager.room.send.leave_chat();
            }
        });

        $(".purchaseMoreTime").click(function() {
            if(confirm("You are about to pause this chat to purchase more time. Would you like to continue?")){
                Chat.manager.room.send.purchase_time();
            }
        });
        
        $(".addStoredTime").click(function() {
            if(confirm("Doing this will pause the chat, do you want to continue?")){
                //Chat.manager.room.send.add_stored_time();
                Chat.manager.room.send.get_stored_time();
            }
        });
        
        $("#addStoredTimeBtn").click(function() {
            Chat.manager.room.send.add_stored_time();
        });
        
        $("#cancelAddStoredTimeBtn").click(function() {
            Chat.views.message.system.show_add_stored_time(false);
            Chat.manager.room.send.resume();
        });
        
        $("#purchaseMoreTimeBtn").click(function() {
            if(confirm("You are about to purchase more time. Would you like to continue?")){
                Chat.manager.room.send.purchase_time();
            }
        });
        
        $("#refreshPurchasedTimeBtn").click(function() {
            Chat.manager.room.send.refresh_stored_time();
        });
        
        $(".addFreeTime").click(function() {
            Chat.views.message.system.show_add_free_time(true);
        });
        
        $("#addFreeTimeBtn").click(function() {
            Chat.manager.room.send.add_free_time();
        });
        
        $("#cancelAddFreeTimeBtn").click(function() {
        	$('#freeTimeInput').val(0);
            Chat.views.message.system.show_add_free_time(false);
        });
        

        $(".personalBanUserAnchor").click(function() {
            if(confirm('Are you sure you want to ban this user & end this chat?')){
                Chat.manager.room.send.pban();
            }
        });
        
        $(".fullBanUserAnchor").click(function() {
            if(confirm('Are you sure you want to ban this user & end this chat?')) {
                Chat.manager.room.send.fban();
            }
        });
        
        $(".refundChatAnchor").click(function() {
            if(confirm('Are you sure you want to return entire chat time back to the client and end this chat?')) {
                Chat.manager.room.send.refund();
            }
        });
        $(".addLostTime").click(function() {
            // just show the add lost time
            // set to pause first by reader
            Chat.views.message.system.show_lost_time(true);
            Chat.manager.room.send.pause();
        });
        $(".removeLostTime").click(function() {
            // just show the add lost time
            // set to pause first by reader
            Chat.views.message.system.show_lost_time(false);
            Chat.manager.room.send.resume();
        });
        $("#lostTimeBtn").click(function(){
            if($.isNumeric($("input[name=losttime]").val()))
            {
                var lost_time = parseInt($("input[name=losttime]").val(), 10);
                
                if (lost_time < 0) {
                    alert('Lost time cannot be zero nor negative number');
                }
                
                if (lost_time * 60 >= Chat.manager.room.max_chat_length || lost_time*60 > Chat.manager.room.chat_length) {
                    alert('Lost time cannot exceed max chat time or chat time');
                } else {
                    Chat.manager.room.send.lost_time(lost_time);
                }           
            }
            else
            {
                alert("Must Be Valid Number");
            }
        });
        
        $(".contactAdmin").click(function() {
            Chat.manager.room.send.contact_admin(); 
        });
        	
    },
    
    init: function() {
        // register
        console.log("Registering controller room actions");
        Chat.controller.room.actions.on_message_send();
        // set up actions
        Chat.controller.room.user_actions();
        
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

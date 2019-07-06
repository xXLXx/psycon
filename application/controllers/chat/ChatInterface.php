<?

    require_once(APPPATH . "/controllers/Popup.php");

    class chatInterface extends popup
    {

        function __construct()
        {

            parent :: __construct();

            $this->load->model("chatmodel");
            $this->load->model("member");
			$this->settings = $this->system_vars->get_settings();
            //$this->requireLogin();

        }

        //--- Load Main Chat Window
        function index($chatId = null, $chatSessionId = null)
        {
            if (empty($chatId) || empty($chatSessionId)) {
                $this->output('chat/chat_error_page.php');
                return;
            }
            
            $this->requireLogin();
            //--- IF continuing a previous chat
            //--- AND IF the logged member IS the client
            //--- Then set the termination of the chat back to 0

            if($this->session->userdata('chat_id'))
            {

                $this->session->unset_userdata('chat_id');

                $this->db->where('id', $chatId);
                $this->db->update('chats', array('ended'=>0));

            }

            //--- Set the chat session accordingly
            $chatObject = $this->chatmodel->setSession($chatId);
            if ($chatObject == false) {
                $this->error("The chat session you requested has been expired.  Please retry. ");
                return;
            }
            
            $params = $chatObject->object;
            
            // validate chat session id
            if ($chatSessionId != $params['chat_session_id']) {
                $this->error("This chat has been terminated. The chat id is not valid.");
                return;
            }

            if(!$params['ended'])
            {

                $this->hideLogo = true;

                $params['reader'] = $this->member->get_member_data($params['reader_id']);
                $params['client'] = $this->member->get_member_data($params['client_id']);

                //--- Store the member info depending if the member is the reader or client (to avoid another DB request)
                if($params['reader_id'] == $this->member->data['id']) {
                    $params['title'] = "Chat with " . $params['client']['username'];
                    $params['member'] = $params['reader'];
                    $params['member_type'] = 'reader';
                    $params['member_hash'] = $this->chatmodel->generate_member_hash($params['reader_id'], $params['reader_seed'], $params['chat_session_id']);
                } else {
                    $params['title'] = "Chat with " . $params['reader']['username'];
                    $params['member'] = $params['client'];
                    $params['member_type'] = 'client';
                    $params['member_hash'] = $this->chatmodel->generate_member_hash($params['client_id'], $params['client_seed'], $params['chat_session_id']);
                }

                //--- Check for chat transcripts
                //--- IF exist, load them into the interface
                $params['transcripts'] = $chatObject->loadTranscripts();
                $params['time_balance'] = $params['set_length'] - $params['length'];
                $params['max_chat_length'] = $params['set_length'];
                $params['chat_length'] = $params['length'];
                
                if ($params['time_balance'] < 0) {
                    $params['time_balance'] = 0;
                }
                //--- Set reader status to BUSY if reader logged in.
                if($this->session->userdata('member_logged') == $params['reader_id']){
                    $this->load->model('reader');
                    $this->reader->init($params['reader_id'])->set_status('busy');
                } else {
                	//$this->reader->init($params['reader_id'])->update_last_chat_request();
                }
                //--- Load Chat Interface
                $params['detect'] = $this->detect;
                $this->output('chat/interface', $params);

            }
            else
            {

                $this->error("This chat has been terminated. You don't have access to open a terminated chat, but you can always start a new chat.");

            }

        }
		
        function test() {
            echo "Ok";
            
        }
        
        function test_error() {
            $this->error("This chat has been terminated. Yo");
        }

        // it should be off when in production
        function create_dummy_chat_session() {
            $passphrase = $this->input->post('pass');
            $reader_id = $this->input->post('reader_id');
            $client_id = $this->input->post('client_id');
            
            $final_result = array();
            if ($passphrase != 'Brunch') {
                $final_result = array ('status' => false, "pass" => $passphrase, "reader_id"=>$reader_id);
                echo json_encode($final_result);
                return;
            }
            
            $param = array('reader_id' => $reader_id, 'client_id' => $client_id, 'set_length' => 100, 'topic' => 'dummy test');
            
            $result = $this->chatmodel->openChatRoom($param);
            $final_result = $result->object;
            $final_result['member_hash'] = $this->chatmodel->generate_member_hash($client_id, $final_result['client_seed'], $final_result['chat_session_id']);
            $final_result['client_hash'] = $this->chatmodel->generate_member_hash($client_id, $final_result['client_seed'], $final_result['chat_session_id']);
            $final_result['reader_hash'] = $this->chatmodel->generate_member_hash($reader_id, $final_result['reader_seed'], $final_result['chat_session_id']);
            $reader = $this->member->get_member_data($reader_id);
            $final_result['reader_id_hash'] = $reader['member_id_hash'];
            $client = $this->member->get_member_data($client_id);
            $final_result['client_id_hash'] = $client['member_id_hash'];
            /* Sample
             
             {
              "object": {
                "id": "64",
                "start_datetime": null,
                "reader_id": "13",
                "client_id": "14",
                "topic": "dummy test",
                "set_length": "100",
                "length": "0",
                "ended": "0",
                "chat_session_id": "881ca23fcf0556a04a72c5221c606a90",
                "reader_seed": "r2014-08-05 20:35:3327",
                "client_seed": "c2014-08-05 20:35:33277",
                "create_datetime": "2014-08-05 20:35:33",
                "region": "CA"
              }
            } 
             
             */ 
            echo json_encode($final_result);
            /*
            if (defined($result->object)){
                
            } else {
                $final_result = array ('status' => false);
                echo json_encode($final_result);
            }
            */
        }

        // post method
        function validate() {
            $chat_id = $this->input->post('chat_id');
            $chat_session_id = $this->input->post('chat_session_id');
            $member_hash = $this->input->post('member_hash');

            $result = $this->chatmodel->validateChatSession($chat_id, $chat_session_id, $member_hash);
            
            $final_result = array();
            if ($result === false) {
                $final_result = array ('status' => false);
            } else {
                $final_result = $result;
                $final_result['status'] = true;
                
                // it is good.. then find out member username;
                $reader = $this->member->get_member_data($result['reader_id']);
                if (!is_null($reader)) {
                    $final_result['reader_username'] = $reader['username'];
                } else {
                    $final_result['reader_username'] = "Unavailable";
                }
                $client = $this->member->get_member_data($result['client_id']);
                if (!is_null($reader)) {
                    $final_result['client_username'] = $client['username'];
                } else {
                    $final_result['client_username'] = "Unavailable";
                }
                
                if ($result['reader_id'] == $result['member_id']) {
                    $final_result['member_username'] = $final_result['reader_username'] ;
                } else if ($result['client_id'] == $result['member_id']) {
                    $final_result['member_username'] = $final_result['client_username'] ;
                }
            }
            echo json_encode($final_result);
        }
        
        function validate_member() {
            $member_id = $this->input->post('member_id');
            $member_id_hash = $this->input->post('member_id_hash');

            $result = $this->member->get_member_data($member_id);
            $final_result = array();
            if ($result == false) {
                $final_result['status'] = false;
                $final_result['reason'] = 'ID not exist';
            } else {
                if ($result['member_id_hash'] == $member_id_hash) {
                    $final_result['status'] = true;
                    $final_result['member_type'] = $result['member_type'];
                } else {
                    $final_result['status'] = false;
                    $final_result['reason'] = 'Unable to validate the ID';
                }
            }
            echo json_encode($final_result);
        }

        function check_time_balance() {
            $chat_session_id = $this->input->post('chat_session_id');
            
            if($object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                $final_result['status'] = true;
                $final_result['time_balance'] = $object->object['set_length'] - $object->object['length'];
                if ($final_result['time_balance'] < 0) {
                    $final_result['time_balance'] = 0;
                }
                $final_result['max_chat_length'] = $object->object['set_length'];
                $final_result['chat_length'] = $object->object['length'];
                $final_result['start_time'] = $object->object['start_datetime'];
                echo json_encode($final_result);
            } else {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to start chat. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }            
        }

    
        //--- start billing
        function start_chat()    // previous $session_id = null
        {
            $chat_session_id = $this->input->post('chat_session_id');
            
            if($object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                $final_result['status'] = true;
                
                $now = date('Y-m-d H:i:s');
                $final_result['object'] = $object;
                //echo "what is... object " . json_encode($object) . " <br>";
                
                if (empty($object->object['start_datetime']) )  {
                    // not started yet, so start now
                    $final_result['start_time'] = $object->startChat();
                    $final_result['started_already'] = false;
                } else {
                    $final_result['start_time'] = $object->object['start_datetime'];
                    $final_result['started_already'] = true;
                }
                
                $final_result['time_balance'] = $object->object['set_length'] - $object->object['length'];
                if ($final_result['time_balance'] < 0) {
                    $final_result['time_balance'] = 0;
                }
                $final_result['max_chat_length'] = $object->object['set_length'];
                $final_result['chat_length'] = $object->object['length'];
                 
                echo json_encode($final_result);
                 
            }
            else
            {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to start chat. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }

        }

        //--- Record Chat Length
        function record_chat()      // previous signature: $session_id = null, $timer = 0
        {
            $chat_session_id = $this->input->post('chat_session_id');
            $timer = $this->input->post('timer');
            
            if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {

                $object->recordLength($timer);

                $final_result['status'] = true;
                echo json_encode($final_result);
            }
            else
            {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to record chat. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }
        }

        //--- Record Chat Messages
        function log_message()    // previous $session_id = null
        {
            $chat_session_id = $this->input->post('chat_session_id');
            $message = $this->input->post('message');
            $member_id = $this->input->post('member_id');
            
            if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                $object->logMessage($member_id, $message);

                $final_result['status'] = true;
                echo json_encode($final_result);

            }
            else
            {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to log message. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }

        }

        //--- Refund Chat
        function refund_chat()    // previous $session_id = null
        {
            $chat_session_id = $this->input->post('chat_session_id');
            
            if($object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                $final_result['status'] = true;
                $final_result['refund'] = $object->refundChat();
                echo json_encode($final_result);
            }
            else
            {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to refund. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }

        }

        function banUser()  // previous $reader_id,$member_id,$type
        {
            $reader_id = $this->input->post('reader_id');
            $member_id = $this->input->post('member_id');
            $type = $this->input->post('type');
            $this->chatmodel->banUser($reader_id,$member_id,$type);

            $final_result['status'] = true;
            echo json_encode($final_result);
        }

		function abort_chat()
        {
        	$now = date("Y-m-d H:i:s");
			
            $chat_session_id = $this->input->post('room_name'); // chat_session_id
            $member_hash = $this->input->post('member_hash');
            $aborted_by = $this->input->post('aborted_by');
            $timer = $this->input->post('timer');
			$type = $this->input->post('type');
            
            $result = $this->chatmodel->validateChatSession(null, $chat_session_id, $member_hash, $aborted_by);
            
            $final_result = array();
            $final_result['abort_to_logout'] = false;
            if ($result === false) {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to validate chat session with $chat_session_id, $member_hash and $aborted_by";
            } else {
            	
				
                if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
                {
                	$post_reader_status = "online";
					$this->load->model('reader');
               		$reader = $this->reader->init($object->object['reader_id']);						
					$client = $this->member->get_member_data($object->object['client_id']);
					
					if ($type == "auto") {
						$final_result['type'] = "auto";
						if (strtotime($now) - strtotime($object->object['create_datetime']) >= CHAT_MAX_WAIT) {
							
							$last_abort = $this->chatmodel->getLastAutoAbortChat($object->object['reader_id'], $object->object['client_id']);
							$final_result['last_abort_time'] = $last_abort['create_datetime'];
							$final_result['time_diff'] = strtotime($now) - strtotime($last_abort['create_datetime']);
							if ($last_abort !== false && strtotime($now) - strtotime($last_abort['create_datetime']) <= ABORT_TIME_DIFF) {
								// if abort within 2 mins, then set reader to offline
								$post_reader_status = "offline";
								$final_result['abort_to_logout'] = true;
								// send email
								$params['client_username'] = $client['username'];
								$params['reader_username'] = $reader->data['username'];
								$params['topic'] = $object->object['topic'];
								
								$this->system_vars->omail($reader->data['email'],'reader_auto_abort_offline',$params);
								$this->system_vars->omail($this->settings['admin_email'],'reader_auto_abort_offline',$params);
								//$this->system_vars->omail("murvinlai@gmail.com",'reader_auto_abort_offline',$params);
								
							} else {
								$final_result['abort_to'] = $post_reader_status;
								// send email
								$params['client_username'] = $client['username'];
								$params['reader_username'] = $reader->data['username'];
								$params['topic'] = $object->object['topic'];
								
								$this->system_vars->omail($reader->data['email'],'reader_auto_abort',$params);
								//$this->system_vars->omail("murvinlai@gmail.com",'reader_auto_abort',$params);
							}
							$reader->update_last_abort_time();
							
		                    try {
		                        if ($this->chatmodel->setSession($result['id'])->abortChat($post_reader_status, true)) {
		                            // get the final time. 
		                            $final_result['status'] = true;
		                            $final_result['reason'] = 'Abort Chat success';
		                        } else {
		                            $final_result['status'] = false;
		                            $final_result['reason'] = 'Unable to abort chat';
		                        }
		                    } catch (Exception $e) {
		                        $final_result['status'] = false;
		                        $final_result['reason'] = 'Unable to identify chat session';
		                    }
							
	
						}
						
					} else {
						// manual abort
						$reader->update_last_abort_time();	// general abort time.
						
						$params['client_username'] = $client['username'];
						$params['reader_username'] = $reader->data['username'];
						$params['topic'] = $object->object['topic'];
						$this->system_vars->omail($reader->data['email'],'client_quit',$params);
						//$this->system_vars->omail("murvinlai@gmail.com",'client_quit',$params);
						try {
	                        if ($this->chatmodel->setSession($result['id'])->abortChat($post_reader_status, false)) {
	                            // get the final time. 
	                            $final_result['status'] = true;
	                            $final_result['reason'] = 'Manual Abort Chat success';
	                        } else {
	                            $final_result['status'] = false;
	                            $final_result['reason'] = 'Unable to abort chat';
	                        }
	                    } catch (Exception $e) {
	                        $final_result['status'] = false;
	                        $final_result['reason'] = 'Unable to identify chat session';
	                    }
						
					}
                }
                else
                {
                    $final_result['status'] = false;
                    $final_result['reason'] = "Unable to abort chat. Chat session not found $chat_session_id";
                }
            }
            echo json_encode($final_result);   
        }
        
		function reject_chat()
        {
            $chat_session_id = $this->input->post('room_name'); // chat_session_id
            $member_hash = $this->input->post('member_hash');
            $rejected_by = $this->input->post('rejected_by');
            $timer = $this->input->post('timer');
            
            $result = $this->chatmodel->validateChatSession(null, $chat_session_id, $member_hash, $rejected_by);
            
            $final_result = array();
            if ($result === false) {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to validate chat session with $chat_session_id, $member_hash and $terminated_by";
            } else {
                if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
                {
                    
                    $final_result['status'] = true;
    
                    try {
                        if ($this->chatmodel->setSession($result['id'])->rejectChat()) {
                            // get the final time. 
                            $final_result['reason'] = 'Reject Chat success';
                        } else {
                            $final_result['status'] = false;
                            $final_result['reason'] = 'Unable to reject chat';
                        }
                    } catch (Exception $e) {
                        $final_result['status'] = false;
                        $final_result['reason'] = 'Unable to identify chat session';
                    }
                }
                else
                {
                    $final_result['status'] = false;
                    $final_result['reason'] = "Unable to reject chat. Chat session not found $chat_session_id";
                }
            }
            echo json_encode($final_result);   
        }

        //--- End Chat
        /*
        function end_chat($session_id = null)
        {

            $this->chatmodel->setSession($session_id)->endChat();

            echo "Ok";

        }
        */
        function end_chat()
        {
            $chat_session_id = $this->input->post('room_name'); // chat_session_id
            $member_hash = $this->input->post('member_hash');
            $terminated_by = $this->input->post('terminated_by');
            $timer = $this->input->post('timer');
            
            $result = $this->chatmodel->validateChatSession(null, $chat_session_id, $member_hash, $terminated_by);
            
            $final_result = array();
            if ($result === false) {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to validate chat session with $chat_session_id, $member_hash and $terminated_by";
            } else {
                if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
                {
    
                    $object->recordLength($timer);
                    
                    $final_result['status'] = true;
                    $final_result['reason'] = 'EndChat success';
                    $final_result['start_time'] = $object->object['start_datetime'];
                    $final_result['max_chat_length'] = $object->object['set_length'];
                    $final_result['chat_length'] = $object->object['length'] + $timer;
                    
                    $final_result['time_balance'] = $final_result['max_chat_length'] - $final_result['chat_length'];
                    if ($final_result['time_balance'] < 0) {
                        $final_result['time_balance'] = 0;
                    }
					
                    try {
                        if ($this->chatmodel->setSession($result['id'])->endChat()) {
                            // get the final time. 
                            $final_result['reason'] = 'EndChat success';
                        } else {
                            $final_result['status'] = false;
                            $final_result['reason'] = 'Unable to end chat';
                        }
                    } catch (Exception $e) {
                        $final_result['status'] = false;
                        $final_result['reason'] = 'Unable to identify chat session';
                    }
                }
                else
                {
                    $final_result['status'] = false;
                    $final_result['reason'] = "Unable to end chat. Chat session not found $chat_session_id";
                }
            }
            echo json_encode($final_result);   
        }
        
        
        function test_chat()
        {
            $this->db->where(array
            (
                'member_id' => 6,
                'type' => 'reading',
                'balance >' => 0
            ));
            $this->db->order_by("tier = 'promo' desc");
            $this->db->order_by("id");
            $total = 0;
            // Get matching transactions
            foreach($this->db->get('member_balance')->result() as $a)
            {
                echo $a ->id . " " . $a -> tier . "<br />";
            }
        }

        //--- Purchase More time
        function purchaseMoreTime() // previous $session_id = null
        {
            $chat_session_id = $this->input->post('chat_session_id');
            
            if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                $this->session->set_userdata('chat_id', $object->object['id'] );
                
                $reader = $this->member->get_member_data( $object->object['reader_id'] );
                
                $final_result['status'] = true;
                $final_result['data'] = array('redirect'=> "/chat/main/purchase_time/{$reader['username']}");
                echo json_encode($final_result);
            } else {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to purchase more time. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }
            
            /*
            $this->session->set_userdata('chat_id', $session_id);

            $object = $this->chatmodel->openChatRoom( array('chat_session_id'=>$chat_session_id) )->object;
            $reader = $this->member->get_member_data( $object['reader_id'] );

            redirect("/chat/main/purchase_time/{$reader['username']}");
             */
        }

        //--- addStoredTime
        function getStoredTime() {
            $chat_session_id = $this->input->post('chat_session_id');
            
            if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                $this->session->set_userdata('chat_id', $object->object['id']);
                //$reader = $this->member->get_member_data( $object->object['reader_id'] );
                
                $stored_time = $this->member_funds->minute_balance($object->object['client_id']) - floor($object->object['set_length'] / 60);
                
                if ($stored_time < 0) {
                    $stored_time = 0;
                }
                
                $final_result['status'] = true;
                $final_result['stored_time'] = $stored_time;
                echo json_encode($final_result);
            } else {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to get stored time. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }  
        }
        
        // deprecaited
        /*
        function addStoredTime()    //$session_id = null
        {
            $chat_session_id = $this->input->post('chat_session_id');
            
            if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                $this->session->set_userdata('chat_id', $object->object['id']);
                $reader = $this->member->get_member_data( $object->object['reader_id'] );
                
                $final_result['status'] = true;
                $final_result['data'] = array('redirect'=> "/chat/main/add_stored_time/{$reader['username']}");
                echo json_encode($final_result);
            } else {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to Add stored time. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }            
        }
        */
        
        function addStoredTime()    //$session_id = null
        {
            $minutes = trim( $this->input->post('minutes') );
            $chat_session_id = $this->input->post('chat_session_id');
            
            $final_result = array();
            if(!is_numeric($minutes) || $minutes < 1) {
                $final_result['status'] = false;
                $final_result['reason'] = "Please enter at least 1 minute into the chat length field: $minutes";
                echo json_encode($final_result);
                return;
            }
            
            
            if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                $user_time_balance = $this->member_funds->minute_balance($object->object['client_id']) - floor ($object->object['set_length']/60);
                
                if ($user_time_balance <= 0 ) {
                    $final_result['status'] = false;
                    $final_result['reason'] = "You only have zero minute in your account.  ";
                    echo json_encode($final_result);
                    return;
                }
                
                $this->session->set_userdata('chat_id', $object->object['id']);
                if ($minutes > $user_time_balance)        {
                    $final_result['status'] = false;
                    $final_result['reason'] = "You do not have enough minutes in your balance to start a chat for {$minutes} minutes";
                    echo json_encode($final_result);
                    return;
                }         
                
                $chat_id = $object->object['id'];
                
                $new_set_length = $object->object['set_length'] + ($minutes*60);
                
                $update = array();
                $update['set_length'] = $new_set_length;

                $this->db->where('id', $chat_id);
                $this->db->update('chats', $update);
                
                
                $final_result['time_balance'] = $new_set_length - $object->object['length'];
                if ($final_result['time_balance'] < 0) {
                    $final_result['time_balance'] = 0;
                }
                $final_result['max_chat_length'] = $new_set_length;
                $final_result['chat_length'] = $object->object['length'];
                $final_result['total_free_length'] = $object->object['total_free_length'];
                
                $final_result['status'] = true;
                
                echo json_encode($final_result);
            } else {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to Add stored time. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }            
        }

        function addFreeTime()    //$session_id = null
        {
            $minutes = trim( $this->input->post('minutes') );
            $chat_session_id = $this->input->post('chat_session_id');
            
            $final_result = array();
            if(!is_numeric($minutes) || $minutes < 1) {
                $final_result['status'] = false;
                $final_result['reason'] = "Please enter at least 1 minute into the free chat length field: $minutes";
                echo json_encode($final_result);
                return;
            }
            
            
            if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                
                $user_time_balance = $this->member_funds->minute_balance($object->object['client_id']) - floor ($object->object['set_length']/60);
                
                if ($user_time_balance / 60 > 1000 || ($user_time_balance / 60 + $minutes) > 1000) {
                    $final_result['status'] = false;
                    $final_result['reason'] = "You try to add too much free time.  ";
                    echo json_encode($final_result);
                    return;
                } 
                /*
                 
                if ($user_time_balance <= 0 ) {
                    $final_result['status'] = false;
                    $final_result['reason'] = "You only have zero minute in your account.  ";
                    echo json_encode($final_result);
                    return;
                }
                */
                $this->session->set_userdata('chat_id', $object->object['id']);
                /*
                if ($minutes > $user_time_balance)        {
                    $final_result['status'] = false;
                    $final_result['reason'] = "You do not have enough minutes in your balance to start a chat for {$minutes} minutes";
                    echo json_encode($final_result);
                    return;
                }         
                */
                $chat_id = $object->object['id'];
                
                $new_set_length = $object->object['set_length'] + ($minutes*60);
                $total_free_length = $object->object['total_free_length'] + ($minutes*60);
                
                $update = array();
                $update['set_length'] = $new_set_length;
                $update['total_free_length'] = $total_free_length;

                $this->db->where('id', $chat_id);
                $this->db->update('chats', $update);
                
                
                $final_result['time_balance'] = $new_set_length - $object->object['length'];
                if ($final_result['time_balance'] < 0) {
                    $final_result['time_balance'] = 0;
                }
                $final_result['max_chat_length'] = $new_set_length;
                $final_result['chat_length'] = $object->object['length'];
                $final_result['total_free_length'] = $object->object['total_free_length'] + $minutes * 60;
                $final_result['free_time_added'] = $minutes;
                
                $final_result['status'] = true;
                
                echo json_encode($final_result);
            } else {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to Add Free time. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }            
        }
        
        

        //--- Reset chat - this is called by webpage, not by node.js chat server. 
        function resetChat()
        {
            /*
            $chat_session_id = $this->input->post('chat_session_id');
            
            if( $object = $this->chatmodel->openActiveChatRoomByChatSession(array('chat_session_id'=>$chat_session_id)) )
            {
                if ($this->session->userdata('chat_id') == $object->object['id']) {
                    $this->session->unset_userdata('chat_id');
                    $reader = $this->member->get_member_data( $object->object['reader_id'] );
                    $final_result['status'] = true;
                    $final_result['data'] = array('redirect'=> "/chat/main/index/{$reader['username']}");
                    echo json_encode($final_result);
                    
                } else {
                    $final_result['status'] = false;
                    $final_result['reason'] = "Unable to reset chat. Chat id mismatch. ";
                    echo json_encode($final_result);
                }
            } else {
                $final_result['status'] = false;
                $final_result['reason'] = "Unable to reset chat. Chat session not found $chat_session_id";
                echo json_encode($final_result);
            }  
            */
            $object = $this->chatmodel->openChatRoom( array('session_id'=>$this->session->userdata('chat_id')) )->object;

            $this->session->unset_userdata('chat_id');
            $reader = $this->member->get_member_data( $object['reader_id'] );

            redirect();
            /* */
        }

        function test_popup() {
            
            $seconds = trim( $this->input->get('seconds') );
            
            if (empty($seconds)) {
                $seconds = 0;
            }
            
            sleep($seconds);
            
            $final_result = array(
                'status' => true,
                'time' => $seconds
            );
            
            echo json_encode($final_result);
        }

    }
<?

    require_once(APPPATH . "/controllers/Popup.php");

    class main extends popup
    {
        function __construct(){

            parent :: __construct();

            $this->load->model('reader');
            $this->load->model('chatmodel');
			$this->settings = $this->system_vars->get_settings();
        }

        function chat_options($chat_type, $chat_id =  null){

            $chatObject = $this->chatmodel->setSession($chat_id);
            if ($chatObject == false) {
                $this->error("The chat session you requested has been expired.  Please retry. ");
                return;
            }
            $mem_id = $this->member->data['id'];
            $this->member->set_member_id($this->chatmodel->object['reader_id']);

            $chat['chat_type'] = $chat_type;
            $chat['chat_id'] = $chat_id;
            $chat['reader_username'] = $this->member->data['username'];
            $this->member->set_member_id($mem_id);

            $this->session->set_userdata("chat_id",$chat_id);

            $this->load->view("header");
            $this->load->view('chat/timeout_options',$chat);
            $this->load->view("footer");

        }

        function chat_ended($chat_id =  null){

            $chatObject = $this->chatmodel->setSession($chat_id);
            if ($chatObject == false) {
                $this->error("The chat session you requested has been expired.  Please retry. ");
                return;
            }
            $array['chat_id'] = $chat_id;
            $this->load->view("header");
            $this->load->view('chat/chat_ended',$array);
            $this->load->view("footer");

        }

        function submit_testimonial($chat_id =  null)
        {
            $this->form_validation->set_rules('rating','Rating','required|xss_clean|trim');
            $this->form_validation->set_rules('review','Review','required|xss_clean|trim');

            if(!$this->form_validation->run())
            {

                // Show error
                $this->chat_ended($chat_id);

            }
            else
            {
                $chatObject = $this->chatmodel->setSession($chat_id);
                if ($chatObject == false) {
                    $this->error("The chat session you requested has been expired.  Please retry. ");
                    return;
                }
                $insert['member_id'] = $this->chatmodel->object['client_id'];
                $insert['reader_id'] = $this->chatmodel->object['reader_id'];
                $insert['rating'] = set_value("rating");
                $insert['datetime'] = date("Y-m-d G:i:s");
                $insert['review'] = set_value("review");
                $insert['chat_id'] = $chat_id;

                $this->db->insert("testimonials",$insert);
                $this->session->set_flashdata("response","Testimonial submitted.");

                $this->load->model("messages_model");
                $this->messages_model->sendAdminMessage($this->chatmodel->object['reader_id'],"Testimonial Submitted","Testimonial Submitted for Chat #" .$chat_id);

                redirect("/my_account/chats/transcript/{$chat_id}");
            }
        }

        function index($reader_username = null){

            if($reader_username){

                if($this->session->userdata('member_logged')){

                    $params = $this->reader->init($reader_username)->data;
                    $params['time_balance'] = ($this->member_funds->minute_balance()*60); // converted to seconds

                    if($params['time_balance'] < 60){
                        redirect("/chat/main/purchase_time/{$reader_username}");
                    }

                    elseif($this->session->userdata('chat_id')){

                        $params['session_id'] = $this->session->userdata('chat_id');
                        
                        $chatObject = $this->chatmodel->openChatRoom( array('session_id'=>$params['session_id']) )->object;
                        $params['chat_session_id'] = $chatObject['chat_session_id'];
                        $params['title'] = "Continue Previous Chat";
                        $this->output('chat/continue_chat', $params);

                    }else{
                        $params['title'] = "Confirm New Chat";
                        $params['detect'] = $this->detect;
						
						// update reader's last chat pending request
						$this->reader->update_last_pending_request();
						
                        $this->output('chat/new_chat', $params);

                    }

                }else{
                    $this->session->set_userdata('redirect', "/chat/main/index/{$reader_username}");
                    redirect("/popup/login");
                }

            }
            else{
                $this->error("You have not specified a reader to chat with");
            }

        }

        function confirm($readerId = null){

            $topic = trim( $this->input->post('topic') );
            $minutes = trim( $this->input->post('minutes') );
            $user_time_balance = $this->member_funds->minute_balance();

            if(!is_numeric($minutes) || $minutes < 1){

                $array = array();
                $array['error'] = "1";
                $array['message'] = "Please enter at least 1 minute into the chat length field";

            }

            //--- verify the user has sufficient minutes to proceed
            elseif($minutes > $user_time_balance){

                $array = array();
                $array['error'] = "1";
                $array['message'] = "You do not have enough minutes in your balance to start a chat for {$minutes} minutes";

            }

            elseif(!is_numeric($readerId) || !$topic){

                $array = array();
                $array['error'] = "1";
                $array['message'] = "Please enter a chat topic";

            }

            else{

                if($readerId != $this->session->userdata('member_logged')){

                    $array = array();
                    $array['reader_id'] = $readerId;
                    $array['client_id'] = $this->session->userdata('member_logged');
                    $array['topic'] = $topic;
                    $array['set_length'] = $minutes*60;

                    $chatObject = $this->chatmodel->openChatRoom($array)->object;
					$this->reader->init($array['reader_id'])->update_last_chat_request();
					
                    $array = array();
                    $array['error'] = "0";
                    $array['redirect'] = "/chat/chatInterface/index/{$chatObject['id']}/{$chatObject['chat_session_id']}";

                }else{

                    $array = array();
                    $array['error'] = "1";
                    $array['message'] = "You may not chat with yourself";

                }

            }
            echo json_encode($array);
        }

		function confirm_json() {
			$now = date("Y-m-d H:i:s");
			
			$final_result = array();
			
			$client_id = trim( $this->input->post('client_id') );
			$client_id_hash = trim( $this->input->post('client_id_hash') );
			$reader_id = trim( $this->input->post('reader_id') );
            $topic = trim( $this->input->post('topic') );
            $minutes = trim( $this->input->post('minutes') );
            $user_time_balance = $this->member_funds->minute_balance($client_id);

			$member = $this->member->get_member_data($client_id);
			
			if ($client_id_hash != $member['member_id_hash']) {
				$final_result['status'] = false;
                $final_result['message'] = "Invalid Login";
			} elseif (!is_numeric($minutes) || $minutes < 1) {

                $final_result['status'] = false;
                $final_result['message'] = "Please enter at least 1 minute into the chat length field";

            } elseif ($minutes > $user_time_balance) {
				//--- verify the user has sufficient minutes to proceed
                $final_result['error'] = false;
                $final_result['message'] = "You do not have enough minutes in your balance to start a chat for {$minutes} minutes";
            } elseif (!is_numeric($reader_id) || !$topic) {
                $final_result['status'] = false;
                $final_result['message'] = "Please enter a chat topic";
            } else {
				if($member['member_type'] == 'client') {
					$reader = $this->member->get_member_data($reader_id);
					
					if ($reader['status'] != "online") {
						$final_result['status'] = false;
						$final_result['message'] = "Reader is not online";
					} else {
						// check if it is the 4th time
						$is_disable_chat = false;
						$lastManualAbortChats = $this->chatmodel->getLastManualAbortChats($reader_id, $client_id, MANUAL_QUIT_DISABLE_MAX_TIME);
						if ($lastManualAbortChats == false || empty($lastManualAbortChats) || count($lastManualAbortChats) < MANUAL_QUIT_DISABLE_MAX_TIME) {
							$is_disable_chat = false;
						} else {
							$old_chat_record = $lastManualAbortChats[MANUAL_QUIT_DISABLE_MAX_TIME - 1];
							if (strtotime($now) - strtotime($old_chat_record['create_datetime']) < MANUAL_QUIT_DISABLE_PERIOD) {
								$is_disable_chat = true;
							} else {
								$is_disable_chat = false;
							}
						}
						
						if ($is_disable_chat) {
							$email_params = array();
							$email_params['client_username'] = $member['username'];
							
							$this->system_vars->omail($reader['email'],'three_chat_quit',$email_params);
							$this->system_vars->omail($this->settings['admin_email'],'three_chat_quit',$email_params);
							$this->system_vars->omail($member['email'],'three_chat_quit',$email_params);
							
							$final_result['status'] = false;
							$final_result['is_max_manual_quit']  = true;
						} else {
							$final_result['status'] = true;
		                    $final_result['reader_id'] = $reader_id;
							$final_result['reader_username'] = $reader['username'];
		                    $final_result['client_id'] = $client_id;
							$final_result['client_username'] = $member['username'];
		                    $final_result['topic'] = $topic;
		                    $final_result['set_length'] = $minutes*60;
		
		                    $chatObject = $this->chatmodel->openChatRoom($final_result)->object;
		
							$final_result['reader_hash'] = $this->chatmodel->generate_member_hash($reader_id, $chatObject['reader_seed'], $chatObject['chat_session_id']); 
							$final_result['client_hash'] = $this->chatmodel->generate_member_hash($client_id, $chatObject['client_seed'], $chatObject['chat_session_id']);
							$final_result['chat_id'] = $chatObject['id'];
							$final_result['chat_session_id'] = $chatObject['chat_session_id'];
							$final_result['time_balance'] = $chatObject['set_length'] - $chatObject['length'];
		                	$final_result['max_chat_length'] = $chatObject['set_length'];
		                	$final_result['chat_length'] = $chatObject['length'];
		                    $final_result['redirect_url'] = "/chat/chatInterface/index/{$chatObject['id']}/{$chatObject['chat_session_id']}";
						}
                    }
                }else{

                    $final_result['status'] = false;
                    $final_result['message'] = "You may not chat with yourself";
                }
            }
            echo json_encode($final_result);
        }
	

        //--- Purcahse Time
        function purchase_time($username){

            $this->requireLogin();

            $params = array();
            $params['username'] = $username;

            $this->output('chat/purchase_time', $params);

        }
        
        // deprecated  use chatInterface.addStoredTime
        function add_stored_time($username){

            $this->requireLogin();

            $params = array();
            $params['username'] = $username;
            $params['time_balance'] = $params['time_balance'] = ($this->member_funds->minute_balance()*60);

            $this->output('chat/add_stored_time', $params);

        }

        // deprecated.  use chatInterface.addStoredTime
        function add_stored_time_submit($username){

            $minutes = trim( $this->input->post('minutes') );
            $user_time_balance = $this->member_funds->minute_balance();
            $chatid = $this->session->userdata('chat_id');

            if(!is_numeric($minutes) || $minutes < 1){

                $array = array();
                $array['error'] = "1";
                $array['message'] = "Please enter at least 1 minute into the chat length field";

            }

            //--- verify the user has sufficient minutes to proceed
            elseif($minutes > $user_time_balance){

                $array = array();
                $array['error'] = "1";
                $array['message'] = "You do not have enough minutes in your balance to start a chat for {$minutes} minutes";

            }

            else{

                //--- Update the table with the minute balance
                $chatObject = $this->chatmodel->openChatRoom( array('session_id'=>$chatid) )->object;

                $update = array();
                $update['set_length'] = $chatObject['set_length'] + ($minutes*60);

                $this->db->where('id', $chatid);
                $this->db->update('chats', $update);

                //--- Return
                $array = array();
                $array['error'] = "0";
                $array['redirect'] = "/chat/chatInterface/index/{$chatObject['id']}/{$chatObject['chat_session_id']}";

            }

            echo json_encode($array);

        }

        //Same as my_account/transactions/add_billing_profile -except for reader_id
        function add_billing_profile($merchant_type = null,$package_id = null, $username = null)
        {

            if(!$merchant_type)
            {

                $this->session->set_flashdata('error', "You must select a package before you can attempt to add a new billing profile. Profiles are dependent on package pricing.");
                redirect("/my_account/transactions/fund_your_account");

            }
            else
            {
                if($package_id)
                {
                $params['merchant_type'] = $merchant_type;

                $params['pinfo'] = $this->site->get_package($package_id);

                $this->session->set_userdata("package_id", $package_id);

                $this->requireLogin();

                // $params = $this->member->get_member_data($username);
                $params['username'] = $username;

                $this->output('chat/chat_add_billing_profile', $params);
                /*
                $this->load->view('header');
                $this->load->view('my_account/header');
                $this->load->view('my_account/transaction_add_billing_profile', $t);
                $this->load->view('my_account/footer');
                $this->load->view('footer');
                */
                }
                else
                {
                    $this->purchase_time($username);
                }

            }

        }

        //--- Confirmation of purchased funds
        public function funds_confirmation($chat_id = null, $package_id = null, $transaction_id = null){

            $t = array();
            $t['chatId'] = $chat_id;
            $t['package'] = $this->site->get_package($package_id);
            $t['transaction'] = $this->db->query("SELECT * FROM transactions WHERE id = {$transaction_id}")->row_array();

            // get the chat object
            $chatObject = $this->chatmodel->openChatRoom( array('session_id'=>$chat_id) )->object;
            $t['chat_session_id'] = $chatObject['chat_session_id'];
            $this->output('chat/funds_confirmation', $t);

        }

    }
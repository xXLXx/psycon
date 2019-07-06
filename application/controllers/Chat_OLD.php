<?

	class chat extends CI_Controller
	{
	
		function __construct()
		{
			
			parent :: __construct();
			
			$this->settings = $this->system_vars->get_settings();
			
			if(!$this->session->userdata('member_logged'))
			{
			
				$this->session->set_flashdata('error', "You must login before you can gain access to secured areas");
				redirect('/register/login');
				exit;
			
			}
			else
			{
			
				$this->load->library('authcim');
			
				$this->member = $this->system_vars->get_member($this->session->userdata('member_logged'));
				$this->expert_id = $this->uri->segment('3');
			
			}
			
		}
		
		function index($expert_id, $session_id, $profile_id = null)
		{
		
			$t['expert_id'] = $expert_id;
			$t['session_id'] = $session_id;
			
			$getChat = $this->db->query("SELECT * FROM chats WHERE session_id = '{$session_id}' LIMIT 1");
			
			if($getChat->num_rows()==0)
			{
			
				$getProfile = $this->db->query("SELECT * FROM profiles WHERE id = {$profile_id} LIMIT 1");
				$profile = $getProfile->row_array();
			
				$insert = array();
				$insert['start_datetime'] = date("Y-m-d H:i:s");
				$insert['session_id'] = $session_id;
				$insert['expert_id'] = $expert_id;
				$insert['profile_id'] = $profile_id;
				$insert['client_id'] = $this->member['id'];
				$insert['cost_per_minute'] = $profile['price_per_minute'];
				
				$this->db->insert('chats', $insert);
				$client_id = $this->member['id'];
				
				$chat = $insert;
				
				$t['transcripts'] = array();
			
			}
			else
			{
			
				$chat = $getChat->row_array();
				$client_id = $chat['client_id'];
				
				// Get Chat Transcripts using session_id 
				$getTranscripts = $this->db->query("SELECT * FROM chat_transcripts WHERE session_id = '{$session_id}' ORDER BY id DESC");
				$t['transcripts'] = $getTranscripts->result_array();
			
			}
			
			// Get Client Information
			$t['client'] = $this->system_vars->get_member($client_id);
			
			// Calculate how much time the user has
			// Based on their balance and the cost per minute of the expert's PROFILE
				
			$getProfile = $this->db->query("SELECT * FROM profiles WHERE id = {$chat['profile_id']} LIMIT 1");
			$profile = $getProfile->row_array();
			
			$cost_per_minute = $profile['price_per_minute'];
			$member_balance = $this->system_vars->member_balance($chat['client_id']);
			
			$totalTimeAvailable = (($member_balance/$profile['price_per_minute'])*60);
			$t['time_available'] = $totalTimeAvailable; // in seconds
			
			$this->load->view('chat/main', $t);
		
		}
		
		function client_confirm($expert_profile_id)
		{
		
			// Get the expert profile
			$getProfile = $this->db->query("SELECT * FROM profiles WHERE id = {$expert_profile_id} LIMIT 1");
			$profile = $getProfile->row_array();
			
			$expert_id = $profile['member_id'];
			
			// Check to make sure a user is not trying to chat with themselves :-)
			// if($expert_id == $this->member['id']) die("<div align='center'><img src='/media/images/smile.jpg' width='150'><div style='padding:25px 0 0;font-family:arial;font-size:12px;'>Studies show that most intelligent people chat with themselves, while this may be true, we don't allow it on this website.</div></div>");
			
			// Check if the user is blocked
			$getBlocked = $this->db->query("SELECT * FROM blocked_users WHERE expert_id = {$expert_id} AND client_id = {$this->member['id']} LIMIT 1");
		
			if($getBlocked->num_rows()==1)
			{
			
				$this->load->view('chat/blocked');
			
			}
			elseif($expert_id)
			{
			
				// Confirm the expert is actually online still
				$data = file_get_contents(site_url("/main/check_if_online/{$expert_id}"));
				$object = json_decode($data);
				
				// Do a check
				if($object->available=='1')
				{
				
					// Subcategories
					$getSubcategories = $this->db->query("SELECT subcategories.title,subcategories.url,subcategories.parent_id FROM profile_subcategories,subcategories WHERE profile_subcategories.profile_id = {$expert_profile_id} AND subcategories.id = profile_subcategories.subcategory_id GROUP BY profile_subcategories.id ");
					$total_subcategories = $getSubcategories->num_rows();
					$subcategory_string = "";
					
					foreach($getSubcategories->result_array() as $i=>$p)
					{
					
						$category = $this->db->query("SELECT * FROM categories WHERE id = {$p['parent_id']} LIMIT 1");
						$c = $category->row_array();
					
						$subcategory_string .= "{$c['title']} / {$p['title']}";
						
						if($total_subcategories!=($i+1))
						{
							$subcategory_string .= "<br />";
						}
					
					}
					
					$t['categories'] = $subcategory_string;
				
					// Data Object
					$t['profile'] = $profile;
					$t['expert'] = $this->system_vars->get_member($expert_id);
					$t['client'] = $this->member;
					$t['balance'] = $this->system_vars->member_balance($this->member['id']);
					
					$session_id = time();
					
					// If the resume chat was saved for this SAME profile_id, then just resume the previous chat
					// Otherwise, start a fresh one
					if($this->session->userdata('resume_chat'))
					{
					
						$getChat = $this->db->query("SELECT * FROM chats WHERE session_id = {$this->session->userdata('resume_chat')} LIMIT 1");
						$chat = $getChat->row_array();
						
						if($chat['profile_id'] == $expert_profile_id)
						{
						
							$session_id = $this->session->userdata('resume_chat');
						
						}
					
					}
					
					// Remove any previously set resume_chat session variables
					$this->session->unset_userdata('resume_chat');
					
					$t['session_id'] = $session_id;
				
					$this->load->view('chat/confirm', $t);
				
				}
				else
				{
				
					$this->load->view('chat/not_available');
				
				}
				
			}
		
		}
		
		function fund_from_chat($session_id)
		{
		
			$this->session->set_userdata('resume_chat', $session_id);
			
			$getChat = $this->db->query("SELECT * FROM chats WHERE session_id = {$session_id} LIMIT 1");
			$chat = $getChat->row_array();
		
			redirect("/chat/fund_account/{$chat['expert_id']}");
		
		}
		
		function fund_account($expert_id)
		{
		
			$t['balance'] = $this->system_vars->member_balance($this->member['id']);
			$t['expert_id'] = $expert_id;
			
			$getBillingProfiles = $this->db->query("SELECT * FROM billing_profiles WHERE member_id = {$this->member['id']} ");
			$t['billing_profiles'] = $getBillingProfiles->result_array();
		
			$this->load->view('chat/fund_account', $t);
		
		}
		
		function submit_credit_deposit($expert_id)
		{
		
			$this->form_validation->set_rules('profile','Billing Profile','required|trim|xss_clean');
			$this->form_validation->set_rules('amount','Amount','required|trim|xss_clean|greater_than[0]');
			
			if(!$this->form_validation->run())
			{
			
				$this->fund_account($this->expert_id);
			
			}
			else
			{
			
				$getBP = $this->db->query("SELECT * FROM billing_profiles WHERE id = ".set_value('profile')." AND member_id = {$this->member['id']} LIMIT 1");
				$b = $getBP->row_array();
			
				$charge = $this->authcim->charge_card($b['customer_id'], $b['payment_id'], set_value('amount'), time(), 'IYA Account Fund');
				
				if(!$charge['status'])
				{
				
					$this->session->set_flashdata('error', $charge['message']);
					redirect("/chat/fund_account/{$this->expert_id}");
				
				}
				else
				{
				
					$transaction_id = $charge['transaction_id'];
				
					$insert = array();
					$insert['member_id'] = $this->member['id'];
					$insert['datetime'] = date("Y-m-d H:i:s");
					$insert['type'] = 'deposit';
					$insert['amount'] = set_value('amount');
					$insert['summary'] = "Credit/Debit Deposit #{$transaction_id}";
					$insert['transaction_id'] = $transaction_id;
					
					$this->db->insert('transactions', $insert);
					
					$member = $this->member;
					$member['transaction_id'] = $transaction_id;
					$member['amount'] = "$".number_format(set_value('amount'), 2);
					
					$this->system_vars->omail($member['email'],'cc_account_funded', $member);
					
					$this->session->set_flashdata('response', "Your account has been funded");
					
					redirect("/chat/client_confirm/{$this->expert_id}");
				
				}
			
			}
		
		}
		
		function setup_billing_profile($expert_id)
		{
		
			$this->load->view('chat/setup_billing_profile');
		
		}
		
		function save_billing_profile($expert_id)
		{
		
			$this->form_validation->set_rules("cc_num","Credit Card Number","required|trim|xss_clean");
			$this->form_validation->set_rules("exp_month","Expiration Month","required|trim|xss_clean");
			$this->form_validation->set_rules("exp_year","Expiration Year","required|trim|xss_clean");
			$this->form_validation->set_rules("first_name","First Name","required|trim|xss_clean");
			$this->form_validation->set_rules("last_name","Last Name","required|trim|xss_clean");
			$this->form_validation->set_rules("address","Address","required|trim|xss_clean");
			$this->form_validation->set_rules("city","City","required|trim|xss_clean");
			$this->form_validation->set_rules("state","State","required|trim|xss_clean");
			$this->form_validation->set_rules("zip","Zip","required|trim|xss_clean");
			
			if(!$this->form_validation->run())
			{
			
				$this->setup_billing_profile($this->expert_id);
			
			}
			else
			{
			
				$array = array();
				$array['customerId'] = time();
				$array['email'] = $this->member['email'];
				$array['firstName'] = set_value('first_name');
				$array['lastName'] = set_value('last_name');
				$array['address'] = set_value('address');
				$array['city'] = set_value('city');
				$array['state'] = set_value('state');
				$array['zip'] = set_value('zip');
				$array['country'] = "US";
				$array['cardNumber'] = set_value('cc_num');
				$array['expirationDate'] = set_value('exp_year').'-'.set_value('exp_month');
				
				$profile = $this->authcim->create_profile($array);
				
				if(!$profile['status'])
				{
				
					$this->error = $profile['message'];
					$this->setup_billing_profile($this->expert_id);
				
				}
				else
				{
				
					$insert = array();
					$insert['member_id'] = $this->member['id'];
					$insert['customer_id'] = $profile['customer_id'];
					$insert['payment_id'] = $profile['payment_id'];
					$insert['card_number'] = substr(set_value('cc_num'), -4,4);
					$insert['card_name'] = set_value('first_name')." ".set_value('last_name');
					
					$this->db->insert("billing_profiles", $insert);
					
					redirect("/chat/fund_account/{$this->expert_id}");
				
				}
				
			
			}
		
		}
		
		function fund_with_paypal($expert_id)
		{
		
			$this->form_validation->set_rules('amount', 'Amount','xss_clean|trim|required|greater_than[0]');
			
			if(!$this->form_validation->run())
			{
			
				$this->fund_account($this->expert_id);
			
			}
			else
			{
			
				// Set paypal variables
				$t['item'] = "IYA Deposit";
				$t['item_number'] = "1";
				$t['custom'] = $this->member['id'];
				$t['return_url'] = site_url("chat/paypal_return/{$expert_id}");
				$t['cancel_url'] = site_url("chat/paypal_cancel/{$expert_id}");
				$t['notify_url'] = site_url("paypal_ipn/deposit");
				$t['amount'] = set_value('amount');
				
				// Load paypal module
				$this->load->view('pages/paypal', $t);
			
			}
		
		}
		
		function paypal_return($expert_id)
		{
		
			$this->session->set_flashdata('change_size', TRUE);
			$this->session->set_flashdata('response', "Your PayPal payment has been processed. Please allow a little time for the changes to take affect.");
			redirect("chat/client_confirm/{$expert_id}");
			
		}
		
		function paypal_cancel($expert_id)
		{
		
			$this->session->set_flashdata('change_size', TRUE);
			$this->session->set_flashdata('error', "Your PayPal payment has been canceled. ");
			redirect("chat/client_confirm/{$expert_id}");
		
		}
		
		function record_last_activity()
		{
		
			$this->db->where('id', $this->member['id']);
			$this->db->update('members', array('last_activity'=>date("Y-m-d H:i:s")));
		
		}
		
		function record_chat_length()
		{
		
			// Update chat length
			$this->db->where('session_id', $this->input->post('session_id'));
			$this->db->update('chats', array('length' => $this->input->post('length')));
			
			// Get Chat Info
			$getChat = $this->db->query("SELECT chats.*, profiles.price_per_minute FROM chats,profiles WHERE chats.session_id = '{$this->input->post('session_id')}' AND profiles.id = chats.profile_id LIMIT 1");
			$chat = $getChat->row_array();
			
			// Get Expert Profile (to get pricing)
			
			// add/update transaction record to total time
			$getChatTransaction = $this->db->query("SELECT * FROM transactions WHERE chat_session_id = {$this->input->post('session_id')} LIMIT 1");
			
			$insert = array();
			$insert['member_id'] = $chat['client_id'];
			$insert['datetime'] = date("Y-m-d H:i:s");
			$insert['type'] = 'purchase';
			$insert['amount'] = number_format($chat['price_per_minute'] * ($this->input->post('length')/60), 2);
			$insert['summary'] = "Chat # {$this->input->post('session_id')}";
			$insert['chat_session_id'] = $this->input->post('session_id');
			
			if($getChatTransaction->num_rows() == 0)
			{
			
				
			
				$this->db->insert('transactions', $insert);
				
			}
			else
			{
			
				$transaction = $getChatTransaction->row_array();
			
				$this->db->where('id', $transaction['id']);
				$this->db->update('transactions', $insert);
			
			}
			
			echo "Ok";
			
			// If the user has been blocked, do it here...
			// We are doing this here so we don't have to make multiple ajax calls…
			// Since we are recording the time anyway. Just figured to do it here :-)
			
				if($_POST['block_user'])
				{
				
					$getChat = $this->db->query("SELECT * FROM chats WHERE session_id = {$this->input->post('session_id')} LIMIT 1");
					$chat = $getChat->row_array();
					
					$insert = array();
					$insert['datetime'] = date("Y-m-d H:i:s");
					$insert['expert_id'] = $chat['expert_id'];
					$insert['client_id'] = $chat['client_id'];
					
					$this->db->insert('blocked_users', $insert);
					
					echo "\nUser blocked";
				
				}
			
			//
		
		}
		
		function record()
		{
		
			$member_id = $this->input->post('member_id');
			$expert_id = $this->input->post('expert_id');
			$session_id = $this->input->post('session_id');
			$message = $this->input->post('message');
			
			if($member_id&&$expert_id&&$session_id&&$message)
			{
			
				$insert = array();
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['member_id'] = $member_id;
				$insert['expert_id'] = $expert_id;
				$insert['session_id'] = $session_id;
				$insert['message'] = $message;
				
				$this->db->insert('chat_transcripts', $insert);
				
				echo "Ok";
			
			}
			else
			{
			
				echo "No";
			
			}
		
		}
	
	}
<?

	class transactions extends CI_Controller
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
			
		}
		
		function index($type = 'purchase')
		{
		
			$query = "SELECT * FROM transactions WHERE member_id = {$this->member->data['id']} ORDER BY datetime DESC";
			//echo "*$query**";
			$getTransactions = $this->db->query($query);
			$t['transactions'] = $getTransactions->result_array();

			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/transactions', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function get_billing_profiles($package_id = null)
		{
		
			// The billing profiles depend on package price, get package then pass the price.
			$package = $this->site->get_package($package_id);
			
			// Get billing profiles by package price
			$billing_profiles = $this->member_billing->get_billing_profiles($package['price']);
			
			// Get Merchant
			$merchantType = $this->member_billing->check_merchant_type($package['price']);
			
			if(!$billing_profiles)
			{
			
				$array = array();
				$array['error'] = '1';
				$array['merchant'] = $merchantType;
				$array['redirect'] = "/my_account/transactions/add_billing_profile/{$merchantType}";
			
			}
			else
			{
			
				$array = array();
				$array['error'] = "0";
				$array['merchant'] = $merchantType;
				$array['profiles'] = $billing_profiles;
			
			}
			
			echo json_encode($array);
		
		}
		
		function payments()
		{
		
			$getTransactions = $this->db->query("SELECT * FROM transactions WHERE type = 'payment' AND member_id = {$this->member->data['id']} ORDER BY datetime DESC");
			$t['transactions'] = $getTransactions->result_array();
			$t['title'] = "Transactions - Payments";
			
			$t['balance'] = $this->system_vars->member_balance($this->member->data['id']);
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/transactions', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function deposits()
		{
		
			$getTransactions = $this->db->query("SELECT * FROM transactions WHERE type = 'deposit' AND member_id = {$this->member->data['id']} ORDER BY datetime DESC");
			$t['transactions'] = $getTransactions->result_array();
			$t['title'] = "Transactions - Deposits";
			
			$t['balance'] = $this->system_vars->member_balance($this->member->data['id']);
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/transactions', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function request_payout()
		{
		
			$t['balance'] = $this->system_vars->member_balance($this->member->data['id']);
			
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/transaction_request_payout', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function check_amount($amount)
		{
		
			if($amount)
			{
			
				if(is_numeric($amount))
				{
				
					if($amount > 0)
					{
					
						$balance = $this->system_vars->member_balance($this->member->data['id']);
					
						if($amount <= $balance)
						{
						
							return true;
						
						}
						else
						{
						
							$this->form_validation->set_message('check_amount', "You cannot request a payout greater than your balance.");
							return false;
						
						}
					
					}
					else
					{
					
						$this->form_validation->set_message('check_amount', "Your amount must be greater than 0");
						return false;
					
					}
				
				}
				else
				{
				
					$this->form_validation->set_message('check_amount', "Your amount must be a number greater than 0");
					return false;
				
				}
			
			}
			else
			{
			
				return true;
			
			}
		
		}
		
		function submit_payout_request()
		{
		
			$this->form_validation->set_rules('amount','Amount','required|trim|callback_check_amount');
			
			if(!$this->form_validation->run())
			{
			
				$this->request_payout();
			
			}
			else
			{
			
				$insert = array();
				$insert['member_id'] = $this->member->data['id'];
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['type'] = 'payment';
				$insert['amount'] = set_value('amount');
				$insert['summary'] = "Payout Request";
				
				$this->db->insert('transactions', $insert);
				
				$this->session->set_flashdata('response', "Your payout request has been submitted. Please allow up to 48 hours for your request to be processed.");
				
				redirect("/my_account/transactions");
			
			}
		
		}
		
		function fund_your_account()
		{
		
			$t['balance'] = $this->system_vars->member_balance($this->member->data['id']);
			
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/transaction_fund_account', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function fund_with_paypal($package_id = null)
		{
		
			$package = $this->site->get_package($package_id);
			
			// Set paypal variables
			$t['item'] = $package['title'];
			$t['item_number'] = $package['id'];
			$t['custom'] = $this->member->data['id']."*".$package['id'];
			$t['return_url'] = site_url("my_account/transactions/paypal_return");
			$t['cancel_url'] = site_url("my_account/transactions/paypal_cancel");
			$t['notify_url'] = site_url("paypal_ipn/deposit");
			$t['amount'] = $package['price'];
			
			// Load paypal module
			$this->load->view('pages/paypal', $t);
		
		}
		
		function paypal_return()
		{
		
			$this->session->set_flashdata('response', "Your PayPal payment has been processed. Please allow a little time for the changes to take affect.");
			
			$redirect = $this->session->userdata('redirect');
			if($redirect){ redirect($redirect); exit; }
			
			redirect("/my_account");
			
		}
		
		function paypal_cancel()
		{
		
		
			$this->session->set_flashdata('error', "Your PayPal payment has been canceled. ");
			
			$redirect = $this->session->userdata('redirect');
			if($redirect){ redirect($redirect); exit; }
			
			redirect("/my_account");
		
		}
	
		function add_billing_profile($merchant_type = null,$package_id = null)
		{
		
			if(!$merchant_type)
			{
			
				$this->session->set_flashdata('error', "You must select a package before you can attempt to add a new billing profile. Profiles are dependent on package pricing.");
				redirect("/my_account/transactions/fund_your_account");
			
			}
			else
			{
			
				$t['merchant_type'] = $merchant_type;
				// Rob; added package_id to view for action url
				$t['package_id'] = $package_id;


		        $t['pinfo'] = $this->site->get_package($package_id);

				//Rob:  store package_id in session
				$this->session->set_userdata('package_id', $package_id);
                
				$this->load->view('header');
				$this->load->view('my_account/header');
				$this->load->view('my_account/transaction_add_billing_profile', $t);
				$this->load->view('my_account/footer');
				$this->load->view('footer');


			
			}
		
		}
		
		function submit_billing_profile($merchant_type = null,$package_id = null, $username = null){

$this->psy_debug("merchant_type",$merchant_type);
$this->psy_debug("package_id",$package_id);


/*
* added email field checking
*/			
			$this->form_validation->set_rules("email","Email Address","required|trim|xss_clean");			
			
			$this->form_validation->set_rules("cc_num","Credit Card Number","required|trim|xss_clean");
			$this->form_validation->set_rules("exp_month","Expiration Month","required|trim|xss_clean");
			$this->form_validation->set_rules("exp_year","Expiration Year","required|trim|xss_clean");
			$this->form_validation->set_rules("first_name","First Name","required|trim|xss_clean");
			$this->form_validation->set_rules("last_name","Last Name","required|trim|xss_clean");
			$this->form_validation->set_rules("address","Address","required|trim|xss_clean");
			$this->form_validation->set_rules("city","City","required|trim|xss_clean");
			$this->form_validation->set_rules("state","State/Province","required|trim|xss_clean");
			$this->form_validation->set_rules("zip","Zip/Postal Code","required|trim|xss_clean");
			$this->form_validation->set_rules("country","Country","required|trim|xss_clean");
			
			if(!$this->form_validation->run()){

			    if($username){

                   $this->session->set_flashdata("error",validation_errors());

/*
* the url below looks out of date and could result in an error, if it even get called...
*/                   redirect("/chat/main/add_billing_profile/{$merchant_type}/{$package_id}/{$username}");

                }else{

		            $this->add_billing_profile($merchant_type);

                }

			}else{
				$array = array();
				$array['id'] = $this->member->data['id'];
				// Rob: this email value wasn't being populated, catching it from the form
				//$array['email'] = $this->member->data['email'];
				$array['first_name'] = set_value('first_name');
				$array['last_name'] = set_value('last_name');
				$array['address'] = set_value('address');
				$array['city'] = set_value('city');
				$array['state'] = set_value('state');
				$array['zip'] = set_value('zip');
				$array['country'] = set_value('country');
				$array['card_number'] = set_value('cc_num');
				$array['card_exp_month'] = set_value('exp_month');
				$array['card_exp_year'] = set_value('exp_year');
/*
* Rob: added email variable
*/
				$array['email'] = set_value('email');

// Rob: package_id needs to be variable, not session based like old code				
//                if($this->session->userdata("package_id")){
                if($package_id){

				    $billing_profile = $this->member_billing->create_billing_profile($merchant_type, $array);

                    if($billing_profile['error'] == '1'){

                        if($username){

                            $this->session->set_flashdata("error", $billing_profile['message']);
        
        // is this a bad URL?
                            redirect("/chat/main/add_billing_profile/{$merchant_type}/{$package_id}/{$username}");

                        }else{

                            $this->error = $billing_profile['message'];
                            //$this->add_billing_profile($merchant_type, $this->session->userdata("package_id"));
                            $this->add_billing_profile($merchant_type, $package_id);

                        }

                    }else{

                        //add checks for chat
                        // Rob: session wasn't holding the package_id consistently
                        //$pid = $this->session->userdata("package_id");                        
                        //$pid = $package_id;                        
                        $this->session->unset_userdata("package_id");

redirect("/my_account/transactions/submit_deposit/{$billing_profile['billing_profile_id']}/{$package_id}/{$username}");

                    }

                }else{

                    if($username){

                        redirect("/chat/main/purchase_time/{$username}");

                    }else{

                        $this->add_billing_profile($merchant_type);

                    }
                }
			}
		
		}// end
		
		function psy_debug($name,$value)
		{
			echo "<br>*$name*/*$value*";
		}// end

		function psy_showArray($arr)
		{
			echo "<pre>";
			print_r($arr);
			echo "</pre>";
		}// end
		
        function billing_profile_confirmation($billing_profile_id = null){

            $t = $this->member_billing->get_billing_profile($billing_profile_id);

            $this->load->view('header');
            $this->load->view('my_account/header');
            $this->load->view('my_account/transaction_billing_profile_confirmation', $t);
            $this->load->view('my_account/footer');
            $this->load->view('footer');

        }
		
		function delete_billing_profile($billing_id)
		{
		
			$getBP = $this->db->query("SELECT * FROM billing_profiles WHERE id = {$billing_id} AND member_id = {$this->member['id']} LIMIT 1");
			$b = $getBP->row_array();
			
			$delete = $this->authcim->delete_profile($b['customer_id']);
			
			if(!$delete['status'])
			{
			
				$this->session->set_flashdata('error', $delete['message']);
				redirect("/my_account/transactions/fund_your_account");
			
			}
			else
			{
		
				$this->db->where('id', $billing_id);
				$this->db->where('member_id', $this->member['id']);
				$this->db->delete('billing_profiles');
				
				redirect("/my_account/transactions/fund_your_account");
			
			}
		
		}
		
		function submit_deposit($billing_profile_id, $package_id, $username = null){

			$package = $this->site->get_packages($package_id);

			$response = $this->member_billing->charge_billing_profile($billing_profile_id, $package_id);

			if($response['error'] == '1'){

                if($username){

                    $this->session->set_flashdata("error",$response['message']);
                    redirect("/chat/main/add_billing_profile/{$merchant_type}/{$package_id}/{$username}");

                }else{

                    $this->session->set_flashdata('error', $response['message']);
				    redirect("/my_account/transactions/fund_your_account");

                }
			
			}else{

                $upd['received_promo'] = 1;
                $this->member->update($upd);


                if($redirect = $this->session->userdata('redirect')){

                    redirect($redirect);

                }else{

                    if($username){
                        redirect("/chat/main/index/reader/{$username}");
                    }else{
				        redirect("/my_account/transactions/deposit_confirmation/{$package_id}/{$response['transaction_id']}");
                    }

                }
			
			}

		}

        public function deposit_confirmation($package_id = null, $transaction_id = null){

            //--- Rob's Hack
            //--- Because I didn't want to implement this code in like 5 spots I added it here
            //--- IF this is a purchase while a user was in a chat session then we redirect to a
            //--- Confirmation within the chat popup class

            $chatId = $this->session->userdata('chat_id');

            if( $this->session->userdata('chat_id') ){

                redirect("/chat/main/funds_confirmation/{$chatId}/{$package_id}/{$transaction_id}");

            }else{

                $t = array();
                $t['package'] = $this->site->get_package($package_id);
                $t['transaction'] = $this->db->query("SELECT * FROM transactions WHERE id = {$transaction_id}")->row_array();

                $this->load->view('header');
                $this->load->view('my_account/header');
                $this->load->view('my_account/transaction_deposit_confirmation', $t);
                $this->load->view('my_account/footer');
                $this->load->view('footer');

            }

        }
	
	}
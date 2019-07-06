<?

	class api extends CI_Controller
	{
	
		function __construct()
		{
			
			parent :: __construct();
			
		}
		
		/*
		 * :: Functionality ::
		 *      
		 *    1) Login With Psychic Contact
		 *    2) Handle payments
		 *    3) Check Reader Chat Status
		 *      
		*/
		
		public function delete_billing_profile($site_id = null)
		{
		
			if(!$site_id)
			{
			
				$array = array();
				$array['error'] = '1';
				$array['message'] = "Missing API Identification ID";
			
			}
			else
			{
			
				$site = $this->check_api_access($site_id);
				
				if($site['error'] == '1')
				{
				
					$array = $site;
				
				}
				else
				{
		
					$billing_profile_id = (isset($_POST['billing_profile_id']) ? $_POST['billing_profile_id'] : "");
					
					if(!$billing_profile_id)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Billing Profile ID Required";
					}
					else
					{
					
						// Check for valid billing id
						$getBilling = $this->db->query("SELECT * FROM api_billing_profiles WHERE id = {$billing_profile_id} AND api_id = {$site['id']} LIMIT 1");
						
						if($getBilling->num_rows()==0)
						{
						
							$array = array();
							$array['error'] = '1';
							$array['message'] = "That billing profile doesn't exist";
						
						}
						else
						{
						
							$billing = $getBilling->row_array();
						
							$this->load->library('authcim');
					
							$chargeResponse = $this->authcim->delete_profile($billing['customer_id']);
					
							if($chargeResponse['status']=='0')
							{
							
								$array = array();
								$array['error'] = '1';
								$array['message'] = $chargeResponse['message'];
							
							}
							else
							{
							
								$this->db->where('id', $billing_profile_id);
								$this->db->delete('api_billing_profiles');
							
								$array = array();
								$array['error'] = '0';
							
							}
						
						}
					
					}
				
				}
			
			}
			
			echo json_encode($array);
		
		}
		
		public function charge_billing_profile($site_id = null)
		{
		
			if(!$site_id)
			{
			
				$array = array();
				$array['error'] = '1';
				$array['message'] = "Missing API Identification ID";
			
			}
			else
			{
			
				$site = $this->check_api_access($site_id);
				
				if($site['error'] == '1')
				{
				
					$array = $site;
				
				}
				else
				{
		
					$billing_profile_id = (isset($_POST['billing_profile_id']) ? $_POST['billing_profile_id'] : "");
					$summary = (isset($_POST['summary']) ? $_POST['summary'] : "");
					$total_charge = (isset($_POST['total_charge']) ? $_POST['total_charge'] : "");
					
					if(!$billing_profile_id)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Billing Profile ID Required";
					}
					elseif(!$summary)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Summary of Order Required";
					}
					elseif(!$total_charge)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Total Charge Required";
					}
					elseif($total_charge < 1.50)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Total Charge must be greater than $1.50";
					}
					else
					{
					
						// Check for valid billing id
						$getBilling = $this->db->query("SELECT * FROM api_billing_profiles WHERE id = {$billing_profile_id} AND api_id = {$site['id']} LIMIT 1");
						
						if($getBilling->num_rows()==0)
						{
						
							$array = array();
							$array['error'] = '1';
							$array['message'] = "That billing profile doesn't exist";
						
						}
						else
						{
						
							$billing = $getBilling->row_array();
						
							$this->load->library('authcim');
					
							$chargeResponse = $this->authcim->charge_card($billing['customer_id'], $billing['payment_id'], $total_charge, time(), $summary);
					
							if($chargeResponse['status']=='0')
							{
							
								$array = array();
								$array['error'] = '1';
								$array['message'] = $chargeResponse['message'];
							
							}
							else
							{
							
								$array = array();
								$array['error'] = '0';
								$array['transaction_id'] = $chargeResponse['transaction_id'];
								$array['authorization_code'] = $chargeResponse['auth_code'];
							
							}
						
						}
					
					}
				
				}
			
			}
			
			echo json_encode($array);
		
		}
		
		public function create_billing_profile($site_id = null)
		{
		
			if(!$site_id)
			{
			
				$array = array();
				$array['error'] = '1';
				$array['message'] = "Missing API Identification ID";
			
			}
			else
			{
			
				$site = $this->check_api_access($site_id);
				
				if($site['error'] == '1')
				{
				
					$array = $site;
				
				}
				else
				{
		
					$first_name = (isset($_POST['first_name']) ? $_POST['first_name'] : "");
					$last_name = (isset($_POST['last_name']) ? $_POST['last_name'] : "");
					$address = (isset($_POST['address']) ? $_POST['address'] : "");
					$city = (isset($_POST['city']) ? $_POST['city'] : "");
					$state = (isset($_POST['state']) ? $_POST['state'] : "");
					$zip = (isset($_POST['zip']) ? $_POST['zip'] : "");
					$country = (isset($_POST['country']) ? $_POST['country'] : "");
					$card_number = (isset($_POST['card_number']) ? $_POST['card_number'] : "");
					$card_exp_year = (isset($_POST['card_exp_year']) ? $_POST['card_exp_year'] : "");
					$card_exp_month = (isset($_POST['card_exp_month']) ? $_POST['card_exp_month'] : "");
					
					if(!$first_name)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "First Name Required";
					}
					elseif(!$last_name)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Last Name Required";
					}
					elseif(!$address)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Address Required";
					}
					elseif(!$city)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "City Required";
					}
					elseif(!$state)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "State Required";
					}
					elseif(!$zip)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Zip Required";
					}
					elseif(!$country)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Country Required";
					}
					elseif(!$card_number)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Credit/Debit Card Number Required";
					}
					elseif(!$card_exp_month)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Credit/Debit Card Expiration Month Required";
					}
					elseif(!$card_exp_year)
					{
						$array = array();
						$array['error'] = '1';
						$array['message'] = "Credit/Debit Card Expiration Year Required";
					}
					else
					{
					
						$this->load->library('authcim');
						
						$t = array();
						$t['first_name'] = $_POST['first_name'];
						$t['last_name'] = $_POST['last_name'];
						$t['address'] = $_POST['address'];
						$t['city'] = $_POST['city'];
						$t['state'] = $_POST['state'];
						$t['zip'] = $_POST['zip'];
						$t['country'] = $_POST['country'];
						$t['card_number'] = substr($_POST['card_number'], -4, 4);
						$t['card_exp_year'] = $_POST['card_exp_year'];
						$t['card_exp_month'] = $_POST['card_exp_month'];
						
						$response = $this->authcim->create_profile($t);
						
						if($response['status']=='0')
						{
						
							$array = array();
							$array['error'] = '1';
							$array['message'] = $response['message'];
						
						}
						else
						{
						
							$insert = array();
							$insert['api_id'] = $site['id'];
							$insert['card_name'] = $t['first_name']." ".$t['last_name'];
							$insert['card_number'] = $t['card_number'];
							$insert['customer_id'] = $response['customer_id'];
							$insert['payment_id'] = $response['payment_id'];
							
							$this->db->insert('api_billing_profiles', $insert);
							
							$array = array();
							$array['error'] = '0';
							$array['billing_profile_id'] = $this->db->insert_id();
						
						}
					
					}
				
				}
			
			}
			
			echo json_encode($array);
		
		}
		
		/**/
		public function check_reader_status($site_id = null, $username = null)
		{
		
			$site = $this->check_api_access($site_id);
			
			if($site['error'] == '1')
			{
			
				$array = $site;
			
			}
			else
			{
			
				$array = $this->reader_status($username);
			
			}
			
			echo json_encode($array);
					
		}
		
		private function reader_status($username = null, $user_id = null)
		{
		
			if($username||$user_id)
			{
		
				if($user_id)
				{
				
					$getProfile = $this->db->query
					("
					
						SELECT
							members.last_activity,
							members.status
							
						FROM  members
						
						LEFT JOIN profiles ON profiles.id = members.id
						
						WHERE 
							members.id = '{$user_id}'
							
						LIMIT 1
					
					");
				
				}
				else
				{
		
					$getProfile = $this->db->query
					("
					
						SELECT
							members.last_activity,
							members.status
							
						FROM  members
						
						LEFT JOIN profiles ON profiles.id = members.id
						
						WHERE 
							members.username = '{$username}'
							
						LIMIT 1
					
					");
				
				}
				
				if($getProfile->num_rows()==1)
				{
				
					$user = $getProfile->row_array();
					
					$array = $this->system_vars->is_online(strtotime($user['last_activity']), $user['status']);
					$array['error'] = '0';
				
				}
				else
				{
				
					$array = array();
					$array['error'] = '1';
					$array['message'] = "Invalid username";
				
				}
				
			}
			else
			{
			
				$array = array();
				$array['error'] = '1';
				$array['message'] = "You didn't specify a username or user id";
			
			}
			
			return $array;
		
		}
		
		/**/
		public function check_readers_status_multi($site_id = null, $readers_csv = null)
		{
		
			$site = $this->check_api_access($site_id);
			
			if($site['error'] == '1')
			{
			
				$array = $site;
			
			}
			else
			{
			
				// $readers_csv = $this->input->post('readers_csv');
			
				if(!trim($readers_csv))
				{
				
					$array = array();
					$array['error'] = '1';
					$array['message'] = "Missing reader ids separated by commas";
				
				}
				else
				{
				
					$readers_csv = explode(",", $readers_csv);
					$readersArray = array();
					
					foreach($readers_csv as $ra)
					{
					
						if(trim($ra))
						{
						
							$readersArray[] = $ra;
						
						}
					
					}
					
					$finalArray = array();
					
					if(count($readersArray) > 0)
					{
					
						foreach($readersArray as $r)
						{
						
							$reader = $this->reader_status($r);
							
							if($reader['error']=='0')
							{
							
								$finalArray[$r] = $reader;
							
							}
						
						}
						
						$array = array();
						$array['readers'] = $finalArray;
						$array['error'] = '0';
					
					}
					else
					{
					
						$array = array();
						$array['error'] = '1';
						$array['message'] = "You need to specify one or more valid reader ids";
					
					}
				
				}
			
			}
			
			echo json_encode($array);
		
		}
		
		/**/
		public function login($site_id = null)
		{
		
			$site = $this->check_api_access($site_id);
		
			if($site['error'] == '1')
			{
			
				$this->load->view("header");
				$this->load->view("registration/login_form_api_error");
				$this->load->view("footer");
			
			}
			else
			{
				
				$this->load->view("header");
				$this->load->view("registration/login_form_api", $site);
				$this->load->view("footer");
		
			}
		
		}
		
		function login_submit($site_id)
		{
		
			$checkSiteId = $this->db->query("SELECT * FROM api_access WHERE site_id = {$site_id} LIMIT 1");
			$site = $checkSiteId->row_array();
		
			$this->form_validation->set_rules('username',"Username","xss_clean|trim|required");
			$this->form_validation->set_rules('password',"Password","xss_clean|trim|required");
		
			if(!$this->form_validation->run())
			{
			
				$this->login($site_id);
			
			}
			else
			{
			
				$getUser = $this->db->query("SELECT * FROM members WHERE username = '".set_value('username')."' AND password = '".md5(set_value('password'))."' LIMIT 1");
			
				if($getUser->num_rows()==0)
				{
				
					$this->error = "Invalid username/password combination.";
					$this->login();
				
				}
				else
				{
				
					$user = $getUser->row_array();
					
					// Remove unnessarry vars
					
					unset($user['id']);
					unset($user['registration_date']);
					unset($user['password']);
					unset($user['paypal']);
					unset($user['newsletter']);
					unset($user['funds']);
					unset($user['code']);
					unset($user['new_password']);
					unset($user['validated']);
					unset($user['expert']);
					unset($user['status']);
					unset($user['last_activity']);
					
					$user['profile_image'] = site_url("/media/assets/{$user['profile_image']}");
					
					// Submit a post request BACK to the callback URL
					$this->redirect_post($site['callback_url'], $user);
					
				}
	
			}
		
		}
		
		private function check_api_access($site_id = null)
		{
	
			if($site_id)
			{
			
				$checkSiteId = $this->db->query("SELECT * FROM api_access WHERE site_id = {$site_id} LIMIT 1");
				
				if($checkSiteId->num_rows() == 1)
				{
				
					$array = $checkSiteId->row_array();
					$array['error'] = '0';
				
					return $array;
				
				}
				else
				{
				
					$array = array();
					$array['error'] = '1';
					$array['message'] = "Invalid API Access";
				
				}
			
			}
			else
			{
			
				$array = array();
				$array['error'] = '1';
				$array['message'] = "Missing site id";
				
			}
			
			return $array;
	
		}
		
		private function redirect_post($url, array $data) 
		{

			echo "
			<html xmlns=\"http://www.w3.org/1999/xhtml\">
			
				<head>
					
					<script type=\"text/javascript\">
					
						function closethisasap ()
						{
							document.forms[\"redirectpost\"].submit();
						}
						
					</script>
					
				</head>
				
				<body onload=\"closethisasap();\">
					<form name=\"redirectpost\" method=\"post\" action=\"{$url}\" >";
					
					if (!is_null($data))
					{
						
						foreach ($data as $k => $v)
						{
						
							echo "<input type=\"hidden\" name=\"{$k}\" value=\"{$v}\">";
						
						}
						
					}
					
					echo "
					</form>
				</body>
			</html>";
		
		}
		
	}
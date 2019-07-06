<?

	class member_billing extends member
	{
	
		function charge_billing_profile($billing_profile_id = null, $package_id = null)
		{
		
			$package = $this->site->get_package($package_id);
			$billing_profile = $this->get_billing_profile($billing_profile_id);
/*	
$this->psy_debug("billing_profile_id",$billing_profile_id);		
$this->psy_debug("package_id",$package_id);		
$this->psy_showArray($billing_profile);		
*/

			// Determin what merchant processor to use
			// Charge the card based on the merchant processor
			
			$merchant_type = $billing_profile['type'];
			
			switch($merchant_type)
			{
			
				// eOnline Data & First Data (Both Authorize.net accounts)
				case "eonline_data":
				case "first_data":

					$settings = $this->config->item('authnet');

                    $this->load->library('authcim', array('login'=>$settings[$merchant_type]['authnet_login_id'], 'key'=>$settings[$merchant_type]['authnet_transaction_key']));
					$responseObject = $this->authcim->authorize($billing_profile['customer_id'], $billing_profile['payment_id'], $package['price'], time(), $package['title']);
				
					if($responseObject['status']=='0'){
					
						$array = array();
						$array['error'] = "1";
						$array['message'] = $responseObject['message'];
					
					}else{
					
						$insert = array();
						$insert['member_id'] = $this->data['id'];
						$insert['datetime'] = date("Y-m-d H:i:s");
						$insert['type'] = 'purchase';
						$insert['amount'] = $package['price'];
						$insert['summary'] = $package['title'];
                        $insert['transaction_id'] = $responseObject['transaction_id'];
                        $insert['authorization_code'] = $responseObject['auth_code'];
                        $insert['billing_id'] = $billing_profile['id'];
						
						$this->db->insert('transactions', $insert);
                        $trans_id = $this->db->insert_id();
						
						// Insert regular and half credits from packages

                        if($package['promo'] == 1){
                            $tier = 'promo';
                        }else{
                            $tier = "regular";
                        }

						$this->member_funds->fund_account($package['type'], $tier, $package['value'], $trans_id);

						if($package['freebies'] > 0){
                            $this->member_funds->fund_account($package['type'], 'half', $package['freebies'], $trans_id);
                        }
						
						$array = array();
						$array['error'] = "0";
                        $array['transaction_id'] = $trans_id;
					
					}
				
				break;
				
				// Internet Secure (Canadian Site)
				case "internet_secure":

/*
*Rob: this was commented out and canadian orders were not possible. i uncommented it and am working on it
                    $array = array();
                    $array['error'] = '1';
                    $array['message'] = "Canadian orders have been disabled for the moment";

*/
       
					$this->load->library('internetsecure');

					$responseObject = $this->internetsecure->authorize($billing_profile['token'], $package['price'], $package['title']);

					if($responseObject['error'] == '1'){

						$array = array();
						$array['error'] = "1";
						$array['message'] = $responseObject['message'];

					}else{

						$insert = array();
						$insert['member_id'] = $this->data['id'];
						$insert['datetime'] = date("Y-m-d H:i:s");
						$insert['type'] = 'purchase';
						$insert['amount'] = $package['price'];
						$insert['summary'] = $package['title'];
//Rob: need to add currency and payment type to insert
						$this->db->insert('transactions', $insert);
                        $trans_id = $this->db->insert_id();


                        if($package['promo'] == 1){
                            $tier = 'promo';
                        }else{
                            $tier = "regular";
                        }

						// Insert regular and half credits from packages
						$this->member_funds->fund_account($package['type'], $tier, $package['value'], $trans_id);

						if($package['freebies']>0){
                            $this->member_funds->fund_account($package['type'], 'half', $package['freebies'], $trans_id);
                        }

						$array = array();
						$array['error'] = "0";
                        $array['transaction_id'] = $trans_id;
						
					}

                    

				break;
				
				default:
				
					$array = array();
					$array['error'] = '1';
					$array['message'] = "Invalid merchant type selected";
				
				break;
			
			}
			
			return $array;
		
		}

		private function psy_debug($name,$value)
		{
			echo "<br>*$name*/*$value*";
		}// end
		
		private function psy_showArray($arr)
		{
			echo "<pre>";
			print_r($arr);
			echo "</pre>";
		}// end
		
		public function void_transaction($transaction_id = null){

            $t = $this->db->query("

                SELECT
                    transactions.*,
                    billing_profiles.customer_id,
                    billing_profiles.payment_id,
                    billing_profiles.type as merchant_type

                FROM transactions

                JOIN billing_profiles ON billing_profiles.id = transactions.billing_id

                WHERE transactions.id = {$transaction_id}

                LIMIT 1

            ")->row();

            if($t){

                switch($t->merchant_type)
                {

                    // eOnline Data & First Data (Both Authorize.net accounts)
                    case "eonline_data":
                    case "first_data":

                        $settings = $this->config->item('authnet');

                        $this->load->library('authcim', array('login'=>$settings[$t->merchant_type]['authnet_login_id'], 'key'=>$settings[$t->merchant_type]['authnet_transaction_key']));
                        $responseObject = $this->authcim->void($t->customer_id, $t->payment_id, $t->transaction_id);

                        if($responseObject['status']=='0'){

                            $array = array();
                            $array['error'] = "1";
                            $array['message'] = $responseObject['message'];

                        }else{

                            //--- Zero out member_balance
                            $this->db->where('transaction_id', $t->id);
                            $this->db->update('member_balance', array('balance'=>0, 'used'=>0));

                            //--- Insert refund transaction
                            $this->member->set_member_id($t->member_id);
                            $this->member_funds->insert_transaction('refund', $t->amount, $t->region, "Refund/Void for transaction #{$t->id}");

                            //--- Send Email
                            $t->formatted_date = date("m/d/Y @ h:i A", strtotime($t->datetime));
                            $t->type = "email";

                            $this->system_vars->m_omail($t->member_id, 'refund_void', (array)$t);

                            //--- Settle the transaction
                            $this->db->where('id', $t->id);
                            $this->db->update('transactions', array('settled'=>'voided'));

                            $array = array();
                            $array['error'] = "0";

                        }

                        break;

                    // Internet Secure (Canadian Site)
                    case "internet_secure":

                        $array = array();
                        $array['error'] = '1';
                        $array['message'] = "Canadian orders have been disabled for the moment";

                        break;

                    default:

                        $array = array();
                        $array['error'] = '1';
                        $array['message'] = "Invalid merchant type selected";

                        break;

                }

            }else{

                $array = array();
                $array['error'] = '1';
                $array['message'] = "Transaction wasn't found";

            }

            return $array;

        }

        public function settle_transaction($transaction_id = null){

            $t = $this->db->query("

                SELECT
                    transactions.*,
                    billing_profiles.customer_id,
                    billing_profiles.payment_id,
                    billing_profiles.type as merchant_type

                FROM transactions

                JOIN billing_profiles ON billing_profiles.id = transactions.billing_id

                WHERE transactions.id = {$transaction_id}

                LIMIT 1

            ")->row();

            if($t){

                switch($t->merchant_type)
                {

                    // eOnline Data & First Data (Both Authorize.net accounts)
                    case "eonline_data":
                    case "first_data":

                        $settings = $this->config->item('authnet');

                        $this->load->library('authcim', array('login'=>$settings[$t->merchant_type]['authnet_login_id'], 'key'=>$settings[$t->merchant_type]['authnet_transaction_key']));
                        $responseObject = $this->authcim->settle($t->customer_id, $t->payment_id, $t->amount, $t->authorization_code);

                        if($responseObject['status']=='0'){

                            $array = array();
                            $array['error'] = "1";
                            $array['message'] = $responseObject['message'];

                        }else{

                            //--- Zero out member_balance
                            $this->db->where('transaction_id', $t->id);
                            $this->db->update('member_balance', array('used'=>1));

                            //--- Settle the transaction
                            $this->db->where('id', $t->id);
                            $this->db->update('transactions', array('settled'=>'settled'));

                            $array = array();
                            $array['error'] = "0";

                        }

                        break;

                    // Internet Secure (Canadian Site)
                    case "internet_secure":

                        $array = array();
                        $array['error'] = '1';
                        $array['message'] = "Canadian orders have been disabled for the moment";

                        break;

                    default:

                        $array = array();
                        $array['error'] = '1';
                        $array['message'] = "Invalid merchant type selected";

                        break;

                }

            }else{

                $array = array();
                $array['error'] = '1';
                $array['message'] = "Transaction wasn't found";

            }

            return $array;

        }
	
		function get_billing_profile($billing_profile_id){
		
			return $this->db->query("SELECT * FROM billing_profiles WHERE id = {$billing_profile_id} AND member_id = {$this->member->data['id']} LIMIT 1")->row_array();
		
		}
	
		function check_merchant_type($purchase_price = false)
		{
		
			if(!$purchase_price)
			{
			
				return false;
			
			}
			else
			{
			
				/*
			
					= Merchant Rules =
				
					+ Internet Secure
					  For ALL Canadian clients regardless of purchase price
				
					+ Go Throught E-Online Data +
					  Clients first purchase & clients that purchase over 39.95
					
					+ Go Through First Data +
					  Clients that purchase under 39.95
					  
					+ Free 10 minutes
					  When someone adds their first credit card
					  We will authorize $1.00 to verify card
					  Unless PayPal, users need to make their first purchase
				
				*/
				
				// Use Internet Secure
				if(trim(strtolower($this->data['country'])) == 'ca')
				{
				
					return "internet_secure";
				
				}
				
				// Use E-Online Data
				elseif(!$this->check_for_previous_purchase() || $purchase_price >= $this->site->settings['eonline_data_treshhold'])
				{
				
					return  "eonline_data";	
				
				}
				
				// Use First Data
				else
				{
				
					return "first_data";
				
				}
			
			}
		
		}
		
		function get_billing_profiles($purchase_price = 0)
		{
		
			$merchant_type = $this->check_merchant_type($purchase_price);
			
			$getProfiles = $this->db->query("SELECT * FROM billing_profiles WHERE member_id = {$this->data['id']} AND type = '{$merchant_type}' ")->result_array();
		
			if(!$getProfiles) return false;
			else return $getProfiles;
		
		}
		
		function create_billing_profile($merchant_type = 'eonline_data', $paramaters = array())
		{
			
			switch($merchant_type)
			{
			
				case "internet_secure":
 /* 
 // temp block for stopping canadian orders
                   $array = array();
                    $array['error'] = '1';
                    $array['message'] = "Canadian orders have been disabled for the moment";
*/
                    
					$this->load->library('internetsecure');
					
					$responseObject = $this->internetsecure->create_profile($paramaters);
					print_r($responseObject);
					if($responseObject['error'] == '1')
					{

						$array = $responseObject;
					
					}
					else
					{

						$insert = array();
						$insert['type'] = $merchant_type;
						$insert['member_id'] = $this->data['id'];
						$insert['token'] = $responseObject['token'];
						$insert['card_name'] = $paramaters['first_name']." ".$paramaters['last_name'];
						$insert['card_number'] = substr(trim($paramaters['card_number']), -4, 4);
						
						$insert['address'] = $paramaters['address'];
						$insert['city'] = $paramaters['city'];
						$insert['state'] = $paramaters['state'];
						$insert['zip'] = $paramaters['zip'];
                        $insert['exp_month'] = $paramaters['card_exp_month'];
                        $insert['exp_year'] = $paramaters['card_exp_year'];
						$insert['country'] = "CA";
						
						$this->db->insert('billing_profiles', $insert);
						$this->db->insert_id();
						$array = array();
						$array['error'] = "0";
						// Rob: added call to get last inserted record id
						$array['billing_profile_id'] = $this->db->insert_id();
						// Rob: added token value to object
						$array['token'] = $responseObject['token'];
						//print_r($array);
					
					}
					
				
				break;
				
				case "first_data":
				case "eonline_data":

					// Get auth.net settings
					$settings = $this->config->item('authnet');

                    // Load authCIM library
                    $this->load->library('authcim', array('login'=>$settings[$merchant_type]['authnet_login_id'], 'key'=>$settings[$merchant_type]['authnet_transaction_key']));

					$object = $this->authcim->create_profile($paramaters);
					
					if($object['status'] == '1')
					{
					
						$insert = array();
						$insert['type'] = $merchant_type;
						$insert['member_id'] = $this->data['id'];
						$insert['customer_id'] = $object['customer_id'];
						$insert['payment_id'] = $object['payment_id'];
						$insert['card_name'] = $paramaters['first_name']." ".$paramaters['last_name'];
						$insert['card_number'] = substr(trim($paramaters['card_number']), -4, 4);
						
						$insert['address'] = $paramaters['address'];
						$insert['city'] = $paramaters['city'];
						$insert['state'] = $paramaters['state'];
						$insert['zip'] = $paramaters['zip'];
						$insert['country'] = $paramaters['country'];

                        $insert['exp_month'] = $paramaters['card_exp_month'];
                        $insert['exp_year'] = $paramaters['card_exp_year'];
						
						$this->db->insert('billing_profiles', $insert);
					
						$array = array();
						$array['error'] = "0";
                        $array['billing_profile_id'] = $this->db->insert_id();
						
					}
					else
					{
					
						$array = array();
						$array['error'] = "1";
						$array['message'] = $object['message'];
					
					}
				
				break;
				
				default:
				
					$array = array();
					$array['error'] = "0";
					$array['message'] = "No merchant selected";
				
				break;
			
			}
			
			return $array;
			
		}
	
	}
<?

	class paypal_ipn extends CI_Controller
	{
	
		function __construct()
		{
			
			parent :: __construct();
			
		}
		
		/*
		* Add transaction and deposit entries into the database
		* 8/14/2014 Rob added deposit() call to add entry in deposits table for PayPal
		*/
		function deposit()
		{
		
			$status = $this->input->post('payment_status');
			$custom = $this->encrypt->decode(base64_decode($this->input->post("custom")));
			$transaction_id = $this->input->post("txn_id");
			$fund_amount = $this->input->post('mc_gross');
			$currency = $this->input->post('mc_currency');
			$item_name = $this->input->post('item_name');
			
			if($status=='Completed')
			{
			
				list($memberid, $packageid)=explode("*", $custom);

				// Set member id
				$this->member->set_member_id($memberid);

                if(is_numeric($packageid))
                {
                    $package = $this->site->get_package($packageid);
                    // Fund account
                    $this->member_funds->fund_account($package['type'], 'regular', $package['value']);
                    if($package['freebies']>0) $this->member_funds->fund_account($package['type'], 'half', $package['freebies']);

					// log transaction
                    $readerid = $memberid;
                    $this->member->data['id'] = $readerid;
                    $this->member_funds->insert_transaction('payment', $fund_amount, 'US', "PayPal payment","PayPal",$currency);
                    
					// log deposit
                    $readerid = $memberid;
                    $this->member->data['id'] = $readerid;
                    $this->member_funds->insert_deposit($fund_amount, 'PayPal:'.$transaction_id, $item_name, $currency);

                }
                else
                {
					// log transaction
                    $readerid = $memberid;
                    $this->member->data['id'] = $readerid;
                    $this->member_funds->insert_transaction('payment', $fund_amount, 'US', "PayPal payment","PayPal",$currency);
                }
			
				// Get member info	
				$member = $this->member->data;
				$member['transaction_id'] = $transaction_id;
				$member['amount'] = "$".number_format($fund_amount, 2);

                $member['type'] = 'email';
				$this->system_vars->m_omail($member['id'],'paypal_purchase', $member);
			
			}
		
		}
	
	}
<?

	class member_funds extends member
	{
	
		/*
		 * Define Class Constants
		 */
		 
		const TYPE_READING         = 'reading';
		const TYPE_EMAIL           = 'email';
		
		const ERROR_MSG_NOFUNDS    = 'Insufficient funds, please purchase more.';
		
		/*
		 * Pay Reader
		 * An easy functiont to fund a readers account more efficiently :^)
		 * 
		 * type: email or chat
		 * total: float that defines the total to pay the reader (do NOT include commissions, they will be calcualted here)
		 * summary: Summarty of the charge
		 * region: the client's region (either US or CA, the rest really don't matter)
		 * 
		 */

        /*
		public function pay_reader_chat($chat_id, $client_region)
		{
		
			// Get chat info	
			$chat = $this->db->query("SELECT * FROM chats WHERE id = {$chat_id} LIMIT 1")->row_array();
			
			// Calcuate how much to pay the reader
			$length_in_seconds = $chat['length'];
			$reader_per_second = ($this->calculate_chat_commission($client_region)/60);
			$total_to_pay = round(($length_in_seconds*$reader_per_second), 2);
			
			// Pay the reader
			$this->pay_reader('chat', $total_to_pay, "Chat Session: {$chat['session_id']}", $client_region);
		
		}
        */
		 
		/*
		public function pay_reader($type, $total, $summary = "", $region)
		{
		
			// Check service type
			switch($type)
			{

				case "email":
				
					// Get the percentage of funds the reader gets to keep
					$total_funds_to_deposit = $this->site->settings['reader_email_percentage'] * $total;
				
					// Fund Readers Account minus commission
					$this->fund_account('payment', 'regular', $total_funds_to_deposit, $region);
					
					// Insert transaction minus commission
					$this->insert_transaction('earning', $total_funds_to_deposit, $summary);
				
				break;

				case "chat":
				
					// Fund Readers Account minus commission
					$this->fund_account('payment', 'regular', $total, $region);
					
					// Insert transaction minus commission
					$this->insert_transaction('earning', $total, $summary);
					
				
				break;
			
			}
			
		}
        */

		/**/
		// region - client region
		function calculate_chat_commission($region, $tier = null)
		{
		
			// Determin what commission to subtract from the user
            if($tier == 'promo')
            {
                return $this->site->settings['promo_minutes_price'];
            }
            else
            {
                if(trim(strtolower($region)) == 'ca')
                {

                    // From Canada
                    return $this->site->settings['canadian_readers_chat_price'];

                }
                else
                {

                    // Not from Canada
                    if($this->member->data['legacy_member'])
                    {

                        // Legacy members get treated differently
                        return $this->site->settings['legacy_readers_chat_price'];

                    }
                    else
                    {

                        // Standard members
                        return $this->site->settings['standard_readers_chat_price'];

                    }

                }
            }
		
		}
		
		/*
		* Insert Transaction
		* 8/14/2014 Rob testing code, PayPal IPN code not inserting into dep
		*/
		public function insert_transaction($type, $amount, $region, $summary, $ptype = null,$currency)
		{
		
			// Build array to insert into transactions table
			$insert = array();
			$insert['member_id'] = $this->member->data['id'];
			$insert['datetime'] = date("Y-m-d H:i:s");
			$insert['type'] = $type; // earning, purchase, payment, consume
			$insert['amount'] = $amount;
			$insert['summary'] = $summary;
            $insert['region'] = $region;
            $insert['currency'] = $currency;
            $insert['payment_type'] = ($ptype ? $ptype : NULL);
		
			$this->db->insert('transactions', $insert);
		
		}

		/*
		* Insert Deposit
		* 8/14/2014 Rob added function for using in paypal_ipn code, as payments are not auth'd
		*/
		public function insert_deposit($amount, $order_numb, $notes, $currency)
		{
		
			// Build array to insert into transactions table
			$insert = array();
			$insert['Client_id'] = $this->member->data['id'];
			$insert['Amount'] = $amount;
			$insert['Order_numb'] = $order_numb;
			$insert['Date'] = date("Y-m-d H:i:s");
			$insert['Notes'] = $notes;
            $insert['Currency'] = $currency;
		
			$this->db->insert('deposits', $insert);
		
		}
				
		/*
		 * Fund Account
		 */
		public function fund_account($type, $tier, $total, $transaction_id = null, $region = null)
		{
		
			$insert = array();
			$insert['member_id'] = $this->member->data['id'];
			$insert['datetime'] = date("Y-m-d H:i:s");
			$insert['type'] = $type;
			$insert['tier'] = $tier;
			$insert['total'] = $total;
			$insert['balance'] = $total;
            $insert['transaction_id'] = $transaction_id;
			
			// Find out the region this fund is coming from
			// If not passed in, get the user's current region
			if($region){
			
				$insert['region'] = $region;
			
			}else{
			
				$insert['region'] = (trim(strtolower($this->member->data['country']))=='ca' ? "ca" : "us");
			
			}
			
			// Insert balance
			$this->db->insert('member_balance', $insert);
			
			
			// Calculate return array
			$array = array();
			$array['error'] = '0';
			$array['transaction_id'] = $this->db->insert_id();
			
			// Return calculated array :P
			return $array;
			
		}
        
        public function get_last_balance_currency($member_id = null) {
            if(!$member_id) $member_id = $this->member->data['id'];
        
            $t = $this->db->query
            ("
            
                SELECT
                    region
                FROM
                    member_balance
                WHERE
                    member_id = {$member_id} 
                    
                ORDER BY id DESC
                LIMIT 1
                    
            ")->row();
            if (empty($t)) {
                // default to US.
                return "us";
            } else {
                return $t->region;
            }
        }
		
		/*
		 * Use Email Funds
		 */
		public function use_email_funds($funds,$email_id)
		{
			
			if ($this->email_balance() < $funds)
			{
				
				$array = array();
				$array['error'] = '1';
				$array['message'] = self::ERROR_MSG_NOFUNDS;
				
			}
			else
			{
			
				$array = $this->use_funds($funds, self::TYPE_EMAIL,$email_id);
			
			}
			
			return $array;
			
		}
		
		/*
		 * Use Reading Funds
	  	 */
		public function use_reading_funds($funds,$chat_id,$reader_id,$region)
		{
		
			if ($this->minute_balance() < $funds)
			{
				
				$array = array();
				$array['error'] = '1';
				$array['message'] = self::ERROR_MSG_NOFUNDS;
				
			}
			else
			{

				$array = $this->use_funds($funds, self::TYPE_READING,$chat_id,$reader_id,$region);
			
			}
			
			return $array;
			
		}
		
		/*
		 * Get users minute balance
		 */
		public function minute_balance($memberid = null)
		{

            if(!$memberid) $memberid = $this->member->data['id'];
		
			$t = $this->db->query
			("
			
				SELECT
					SUM(total)-SUM(used) as totalMinutes
					
				FROM
					member_balance
					
				WHERE
					member_id = {$memberid} AND
					type = 'reading' AND
					balance > 0
					
				ORDER BY
					id
					
			")->row_array();
			
			return (!$t['totalMinutes'] ? '0' : $t['totalMinutes']);
		
		}
		
		/*
		 * Get users email balance
		 */
		public function email_balance()
		{
		
			$t = $this->db->query
			("
			
				SELECT
					SUM(total)-SUM(used) as totalEmails
					
				FROM
					member_balance
					
				WHERE
					member_id = {$this->member->data['id']} AND
					type = 'email' AND 
					balance > 0
					
				ORDER BY
					id
					
			")->row_array();
			
			return (!$t['totalEmails'] ? '0' : $t['totalEmails']);
		
		}

        /*
        function give_timeback_DEPRECATED($type,$timeback)
        {
            $bal =            $this->db->query("select *
                                    from   member_balance
                                    where  member_id = {$this->member->data['id']}
                                           and balance > 0
                                    order by tier = 'promo' desc limit 1")->row_array();

            if($type == 'free')
            {


                if($bal['tier'] == 'free' && $bal['used'] > $timeback)
                {
                   $upd['balance'] = $bal['balance'] + $timeback;
                   $upd['used'] = $bal['used'] - $timeback;

                   $this->db->where('id',$bal['id']);
                   $this->db->update('member_balance',$upd);
                }
                else
                {
                    $this->fund_account('reading','free',$timeback);
                }
            }
            else
            {
                if($bal['used'] > $timeback)
                {
                    $upd['balance'] = $bal['balance'] + $timeback;
                    $upd['used'] = $bal['used'] - $timeback;

                    $this->db->where('id',$bal['id']);
                    $this->db->update('member_balance',$upd);
                }
            }



        }
        */

        function process_reading($funds, $type = null, $type_id = null, $reader_id = null,$region = null)
        {
            // Build WHERE clause
            $this->db->where(array
            (
                'member_id' => $this->member->data['id'],
                'type' => $type,
                'balance >' => 0
            ));

            $this->db->order_by("tier = 'promo' desc");
            $this->db->order_by("id");

            $total = 0;

            // Get matching transactions
            if($transactions = $this->db->get('member_balance')->result())
            {

                // Start transaction
                $this->db->trans_start();

                // Loop through transactions
                foreach ($transactions as $t){

                    //--- Update transaction when time has started being used
                    //--- NOTE: Marks time as used wether paid or freebie minutes

                    if($t->transaction_id){
                        $this->db->where('id', $t->transaction_id);
                        $this->db->update('transactions', array('time_used'=>1));
                    }

                    // Get lesser value of remaining balance and current credits
                    $difference = $funds > $t->balance ? $t->balance : $funds;
                    $funds -= $difference;

                    //$this->log_chat_transaction($funds,$type,$reader_id,$region);
                    //Update reader table.
                    $readerInsert['datetime'] = date("Y-m-d G:i:s");
                    $readerInsert['reader_id'] = $reader_id;
                    $readerInsert['type'] = $type;
                    $readerInsert['type_id'] = $type_id;
                    $readerInsert['amount'] = $difference;
                    $readerInsert['tier'] = $t->tier;

                    $readerInsert['region'] = $region;

                    if($type=='reading'){

                        $reader_per_second = $this->calculate_chat_commission($region,$t->tier);
                        $readerInsert['commission'] = round(($difference*$reader_per_second), 2);
                        $readerInsert['commission_rate'] = $reader_per_second;

                    }else{

                        $readerInsert['commission'] = $difference;

                    }

                    $total += $readerInsert['commission'];

                    $this->db->insert('profile_balance', $readerInsert);
                    //Create Update records
                    $updateArray = array();
                    $updateArray['used'] = ( $t->used + $difference );
                    $updateArray['balance'] = ( $t->total - $t->used - $difference);

                    // Update record
                    $this->db->where('id', $t->id);
                    $this->db->update('member_balance', $updateArray);

                    // Are we done yet?
                    if ($funds <= 0) break;

                }

                // Complete transaction
                $this->db->trans_complete();

            }


            // Create a successful return
            $array = array();
            $array['error'] = '0';
            $array['total'] = $total;

            return $array;
        }
	
	}
	
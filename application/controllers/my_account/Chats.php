<?

	class chats extends CI_Controller
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
		$this->load->model("chatmodel");
		}
		
		function index()
		{
		
			$getChats = $this->db->query("SELECT * FROM chats WHERE (reader_id = {$this->member->data['id']} or client_id  = {$this->member->data['id']}) ORDER BY start_datetime DESC ");
			$t['chats'] = $getChats->result_array();
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/chats', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function transcript($chat_id)
		{

            $array['session_id'] = $chat_id;
            $this->chatmodel->openChatRoom($array);


            $data['chat_reader'] = ($this->chatmodel->object['reader_id'] == $this->member->data['id'] ? 1 : 0);
            $data['chat_amount'] = "";
            $reader_id = $this->chatmodel->object['reader_id'];
            if($data['chat_reader'] == 1)
            {
                $chat_bal = $this->db->query("select *
                                  from   profile_balance pb
                                  where  pb.reader_id = {$reader_id}
                                         and pb.type = 'reading'
                                         and pb.type_id = {$chat_id} limit 1");

                $results = $chat_bal -> row_array();


                if(count($results) > 0)
                {
                    $data['chat_amount'] = $results['commission'];
                }
            }

			/*
			Rob: check to see if transaction has been refunded (history needs to persist, but the reader can't refund again)
			*/
			// flag to hide refund buttons if no transaction exists
			$data['transNotFound'] = 0;
			
			$query     = "select * from transactions where summary = 'Chat Session #{$chat_id}' limit 1";
			$getTrans = $this->db->query($query);
			$transData = $getTrans->row_array();

			// transNotFound flag to show buttons in view
			if(count($transData) <= 0)
				$data['transNotFound'] = 1;

			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/chat_transcript',$data);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}

        function give_timeback($chat_id)
        {

			// gather chat data
            $query = "select * from chats where id = '{$chat_id}' limit 1";
            $getChat = $this->db->query($query);
			$chatData = $getChat->row_array();
			
			$timeback = $_REQUEST['timeback'];
			$timeback_in_secs = $timeback*60;

			// convert to dollars, so we can re-use the function
			$cents_to_return = $timeback*100; // convert to cents


/*			
			$this->myDebug("cents_to_return",$cents_to_return);
			exit();
			$this->myDebug("timeback",$timeback);
			$this->myDebug("timeback2",$timeback_in_secs);
			$this->myDebug("length",$chatData['length']);

			exit();
*/			
			
            if( ($chatData['length'] >= $timeback_in_secs) and $timeback )
            {
            
					
				/*
				need to modify the transaction so the reader only gets paid the other part remaining
				*/
				$updated_amt = 0;
				/*
				asked murvin to sow me where he calculates the commission, then i can recalc based on the new chat session length
				- convert entire chat length to seconds, deduct the timebackinsecs, now we have new total chat length, and create a commission value based on its
				*/
            
            	// get chat amount, return time to client
            	$amount_returned = $this->return_partial_time_to_client($chatData['id'],$cents_to_return,$updated_amt);
            	if ($amount_returned)
            	{
            		
	                $msg = "The partial refund has been processed & the client has been notified";        	
	                
	                // advise the client of the returned time
	                $this->member->notify($chat['client_id'], $chat['reader_id'], "Chat Time Returned", nl2br("I have returned a portion of your chat time amount for our chat (#$chat_id) for the amount of ($amount_returned)"));

	                // send message to reader
	                $this->member->notify($chat['reader_id'], $chat['reader_id'], "Chat Time Returned", nl2br("You have returned a portion of the chat time amount for the chat (#$chat_id) for the amount of ($amount_returned) to the client."));

	                // redirect back to chat history, as we have deleted the transaction
                	$msg = "You have successfully refunded a portion of the amount for chat session #$chat_id to the client.";
					$this->session->set_flashdata('response', $msg);
	                redirect("/my_account/chats");            

            	}
            	else
            	{
                	$msg = "Unable to partial refund. Chat session amount not found $chat_id";
            	}
            	
            }
            else
            {
                $msg = "Unable to partial refund. Chat session #$chat_id less than 1 minute";
            }
            
			$this->session->set_flashdata('response', $msg);
			redirect("/my_account/chats/transcript/{$chat_id}");            

        }
        
        /*
        Rob: borrowed from Murvin's chatinterface.php
        */
        function process_refund($chat_id)    // previous $session_id = null
        {
            
            $query = "select * from chats where id = '{$chat_id}' limit 1";
            $getChat = $this->db->query($query);
			$chatData = $getChat->row_array();
			
			//print_r($transData);

            if( $chatData['id'] == $chat_id)
            {
            	// get chat amount, return time to client
            	$amount_returned = $this->return_time_to_client($chatData['id'],$chatData['length']);
            	if ($amount_returned)
            	{
            	
	                $msg = "The refund has been processed & the client has been notified";        	
	                
	                // advise the client of the returned time
	                $this->member->notify($chatData['client_id'], $chatData['reader_id'], "Chat Time Returned", nl2br("I have returned the entire chat time amount for our chat (#$chat_id)"));

	                // send message to reader
	                $this->member->notify($chatData['reader_id'], $chatData['reader_id'], "Chat Time Returned", nl2br("You have returned the entire chat time amount for the chat (#$chat_id) to the client."));

	                // redirect back to chat history, as we have deleted the transaction
                	$msg = "You have successfully refunded the entire amount for chat session #$chat_id to the client.";
					$this->session->set_flashdata('response', $msg);
	                redirect("/my_account/chats");            

            	}
            	else
            	{
                	$msg = "Unable to refund. Chat #$chat_id session amount not found";
            	}
            	
            }
            else
            {
                $msg = "Unable to refund. Chat #$chat_id session not found";
            }
           
			$this->session->set_flashdata('response', $msg);
			redirect("/my_account/chats/transcript/{$chat_id}");            

        }
        
        
        function return_partial_time_to_client($chat_id,$cents_to_return,$updated_amt)
        {
        	//$this->myDebug("chat_id",$chat_id);
        	
            // get chat sesson amount spent
            /*
            Rob: this is not a perfect solution as the transactions table has no actual chat_id in it...
            */ 
            $query = "select * from transactions where summary = 'Chat Session #{$chat_id}' limit 1";
            $getAmount = $this->db->query($query);
			
			$transData = $getAmount->row_array();
        	$trans_id  = $transData['id'];        	
        	$client_id  = $transData['member_id'];        	
						
			// we have an amount to refund to client
			if ($length)
			{

				$query = "select * from members where id = '{$client_id}' limit 1";
				$getMember = $this->db->query($query);
				$memberData = $getMember->row_array();

				$length = $cents_to_return; // already converted to pennies

				// add to member_balance
				$insert = array();
				$insert['member_id'] = $client_id;
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['type'] = "reading";
				$insert['tier'] = "regular";
				$insert['total'] = $length;
				$insert['balance'] = $length;
				$insert['transaction_id'] = $transData['id'];
				$insert['region'] = (trim(strtolower($memberData['country']))=='ca' ? "ca" : "us");

				// Insert balance
				$this->db->insert('member_balance', $insert);
								
				// update the transaction as the reader has refunded a partial amount
				$query = "UPDATE 
				transactions SET amount = \"$updated_amt\" WHERE 
				id = {$trans_id}";
				
				$this->db->query($query);

				// return the amount so we know we were successful
				return $length;
			}
			else
			{
				// no amount found, return an error
				return 0;
			}
         	
        }
        
        
        function return_time_to_client($chat_id,$length)
        {
        	//$this->myDebug("chat_id",$chat_id);
        	
            // get chat sesson amount spent
            /*
            Rob: this is not a perfect solution as the transactions table has no actual chat_id in it...
            */ 
            $query = "select * from transactions where summary = 'Chat Session #{$chat_id}' limit 1";
            $getAmount = $this->db->query($query);
			
			$transData = $getAmount->row_array();
        	$trans_id  = $transData['id'];        	
        	$client_id  = $transData['member_id'];        	
						
			// we have an amount to refund to client
			if ($length)
			{

				$query = "select * from members where id = '{$client_id}' limit 1";
				$getMember = $this->db->query($query);
				$memberData = $getMember->row_array();

				$length = round($length/100,2); // convert to pennies

				// add to member_balance
				$insert = array();
				$insert['member_id'] = $client_id;
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['type'] = "reading";
				$insert['tier'] = "regular";
				$insert['total'] = $length;
				$insert['balance'] = $length;
				$insert['transaction_id'] = $transData['id'];
				$insert['region'] = (trim(strtolower($memberData['country']))=='ca' ? "ca" : "us");

				// Insert balance
				$this->db->insert('member_balance', $insert);
								
				// delete the transaction as the reader has refunded the entire amount
				$query = "DELETE FROM 
				transactions WHERE 
				id = {$trans_id}";
				
				$this->db->query($query);

				// return the amount so we know we were successful
				return $length;
			}
			else
			{
				// no amount found, return an error
				return 0;
			}
         	
        }
        

        /*
        Rob dev function
        */
        function myDebug($name,$value)
        {
        	echo "<br>*$name*/*$value*";        	
        }
        
        function get_client_time_remaining($client_id)
        {
 
            $getData = $this->db->query
			("
				SELECT
				
					chats.*
					
				FROM
					chats 
									
				WHERE
					chats.id = {$chat_id}
				
			");
			
			$data = $getChat->row_array();
       
        }
/*		
		function process_refund($chat_id)
		{
		
			$getChat = $this->db->query
			("
				SELECT
				
					chats.*,
					refunds.amount as refund_amount,
					
					expert.username as expert,
					
					client.username as client,
					client.email as client_email,
					client.first_name as client_first_name,
					client.last_name as client_last_name
					
				FROM
					chats 
					
				LEFT JOIN refunds ON chat_id = chats.id
				LEFT JOIN members as expert ON expert.id = chats.expert_id
				LEFT JOIN members as client ON client.id = chats.client_id
					
				WHERE
					chats.id = {$chat_id}
				
			");
			
			$chat = $getChat->row_array();
			
			// Subtract this amount FROM the expert
			$insert = array();
			$insert['member_id'] = $chat['expert_id'];
			$insert['datetime'] = date("Y-m-d H:i:s");
			$insert['type'] = 'purchase';
			$insert['amount'] = $chat['refund_amount'];
			$insert['summary'] = "Refund to {$chat['client']} for chat session {$chat['session_id']} from {$chat['expert']}";
			
			$this->db->insert('transactions', $insert);
			
			// Give funds TO expert
			$insert = array();
			$insert['member_id'] = $chat['client_id'];
			$insert['datetime'] = date("Y-m-d H:i:s");
			$insert['type'] = 'deposit';
			$insert['amount'] = $chat['refund_amount'];
			$insert['summary'] = "Refund to {$chat['client']} for chat session {$chat['session_id']} from {$chat['expert']}";
			
			$this->db->insert('transactions', $insert);
		
			// Update refund transaction
			$update = array();
			$update['status'] = 'processed';
			
			$this->db->where('chat_id', $chat_id);
			$this->db->update('refunds', $update);
		
			// Send Email to client (refund_processed)
			$params = array();
			$params['client_first_name'] = $chat['client_first_name'];
			$params['client_last_name'] = $chat['client_last_name'];
			$params['expert_username'] = $chat['expert'];
			$params['amount'] = number_format($chat['refund_amount'], 2);
			
			$this->system_vars->omail($chat['client_email'], 'refund_processed', $params);
		
			// Redirect When Done
			$this->session->set_flashdata('response', "The refund has been processed & the client has been notified");
			redirect("/my_account/chats/transcript/{$chat_id}");
		
		}
*/		
		function submit_rejection($chat_id)
		{
		
			$getChat = $this->db->query
			("
				SELECT
				
					chats.*,
					refunds.amount as refund_amount,
					
					expert.username as expert,
					
					client.username as client,
					client.email as client_email,
					client.first_name as client_first_name,
					client.last_name as client_last_name
					
				FROM
					chats 
					
				LEFT JOIN refunds ON chat_id = chats.id
				LEFT JOIN members as expert ON expert.id = chats.expert_id
				LEFT JOIN members as client ON client.id = chats.client_id
					
				WHERE
					chats.id = {$chat_id}
				
			");
			
			$chat = $getChat->row_array();
			
			// Update refund transaction
			$update = array();
			$update['rejected_reason'] = $this->input->post('reason');
			$update['status'] = 'rejected';
			
			$this->db->where('chat_id', $chat_id);
			$this->db->update('refunds', $update);
		
			// Send Email to client (refund_processed)
			$params = array();
			$params['client_first_name'] = $chat['client_first_name'];
			$params['client_last_name'] = $chat['client_last_name'];
			$params['expert_username'] = $chat['expert'];
			$params['amount'] = number_format($chat['refund_amount'], 2);
			$params['rejection_reason'] = $this->input->post('reason');
			
			$this->system_vars->omail($chat['client_email'], 'refund_rejected', $params);
		
			// Redirect When Done
			$this->session->set_flashdata('response', "The refund has been rejected, and the client has been notified");
			redirect("/my_account/chats/transcript/{$chat_id}");
		
		}
	
	}
<?

	class messages extends CI_Controller
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

        function test_omail()
        {
            $recipient_id = 6;
            $sender_id = 7;
            $email_template = "cc_account_funded";
            $type = 'email';
            $this->messages_model->notify(null,
                                         $recipient_id,
                                         null,
                                         null,
                                         $type,
                                         $email_template);

        }
		
		function index()
		{
		
			$t['title'] = "My Messages: Inbox";
		
			$getMessages = $this->db->query("SELECT * FROM messages WHERE ricipient_id = {$this->member->data['id']} ORDER BY id DESC");
			$t['messages'] = $getMessages->result_array();
            $t['v_from'] = "";
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/messages', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function view($message_id)
		{
					
			$getMessages = $this->db->query("SELECT *
			                                 FROM   messages
			                                 WHERE  id = {$message_id}
			                                        AND (ricipient_id = {$this->member->data['id']}
			                                        OR sender_id = {$this->member->data['id']}) LIMIT 1");
			$t = $getMessages->row_array();


            switch($t['type'])
            {
                case "email":
                    $t['v_from'] = "System";
                    break;

                case "admin":
                    $t['v_from'] = "Administrator";
                    break;

                case "reader":
                    $sender = $this->member->get_member_data($t['sender_id']);
                    $t['v_from'] = "{$sender['first_name']} {$sender['last_name']}";
                    break;
                    
                case "client":
                    $sender = $this->member->get_member_data($t['sender_id']);
                    $t['v_from'] = "{$sender['first_name']} {$sender['last_name']}";
                    break;
            }


			$t['to'] = $this->system_vars->get_member($t['ricipient_id']);
			$t['from'] = $this->system_vars->get_member($this->member->data['id']);

            $this->messages_model->markMessageRead($t['id']);

			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/messages_view', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function delete($message_id)
		{
		
			$this->db->where('id', $message_id);
			$this->db->where('ricipient_id', $this->member->data['id']);
			$this->db->delete('messages');
			
			$this->session->set_flashdata('response', "Message has been deleted");
			
			redirect("/my_account/messages");
		
		}
		
		function outbox()
		{
		
			$t['title'] = "My Messages: Outbox";
		
			$getMessages = $this->db->query("SELECT * FROM messages WHERE sender_id = {$this->member->data['id']} ");
			$t['messages'] = $getMessages->result_array();
		    $t['v_from'] = "";
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/messages', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function compose($message_reply_id = null,$type="reply")
		{
		
		// reader, get clients they have read for
		if ($this->member->data['member_type'] == "reader")
		{
			$getAllMembers = $this->db->query
			("
			
				SELECT 
					members.id,
					members.username 
					
				FROM
					chats
				
				JOIN members 
				
				WHERE members.id = chats.client_id AND chats.reader_id = ".$this->member->data['profile_id']."
				
				GROUP BY
					members.id
				
				ORDER BY
					members.username
			
			");
			
			}
			else
			{
				// client, get list of readers 
				$getAllMembers = $this->db->query
				("
			
					SELECT 
						members.id,
						members.username 
					
					FROM
						profiles
				
					LEFT JOIN members ON members.id = profiles.id
				
					GROUP BY
						members.id
				
					ORDER BY
						members.username
			
				");
			
			}
			$t['users'] = $getAllMembers->result_array();
			
			$t['to'] = "";
			$t['subject'] = "";
			$t['message'] = "";
			
			// If a reply id was passed… Then
			// prepopulate the fields
			if($message_reply_id && $type=="reply")
			{
			
				$getMessage = $this->db->query("SELECT * FROM messages WHERE id = {$message_reply_id} AND (ricipient_id = {$this->member->data['id']} OR sender_id = {$this->member->data['id']}) LIMIT 1");
				
				if($getMessage->num_rows()==1)
				{

					$message = $getMessage->row_array();
					
					$sender = $this->system_vars->get_member($message['sender_id']);
					
					$t['to'] = $message['sender_id'];
					$t['subject'] = "Re: ".$message['subject'];
					$t['message'] = "\n\n\n+++ Original Message: {$sender['first_name']} {$sender['last_name']} - {$sender['username']} on ".date("m/d/y h:i:s", strtotime($message['datetime']))." +++\n\n".$message['message'];
				
				}
			
			}
            else if($message_reply_id && $type != "reply")
            {

                $t['to'] = $message_reply_id;
                $t['subject'] = "";
                $t['message'] = "";
            }
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/messages_compose', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function compose_submit()
		{
		
			$this->form_validation->set_rules("to","Recipient","trim|required");
			$this->form_validation->set_rules("subject","Subject","trim|required");
			$this->form_validation->set_rules("message","Message","trim|required");
			
			if(!$this->form_validation->run())
			{
				
				$this->compose();
			
			}
			else
			{
			
				$this->member->notify(set_value('to'), $this->member->data['id'], set_value('subject'), nl2br(set_value('message')));
			
				$this->session->set_flashdata('response', "Your message has been sent");
				
				redirect('/my_account/messages');
			
			}
		
		}
	
	}
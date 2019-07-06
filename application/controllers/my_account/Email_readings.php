<?

	class email_readings extends CI_Controller
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
		
		function open_requests()
		{
		
			$t['open'] = 1;
			$t['emails'] = $this->db->query("SELECT * FROM questions WHERE profile_id = {$this->member->data['id']} AND status = 'new' ORDER BY id")->result_array();
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/email_readings_list', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function closed_requests()
		{
		
			$t['open'] = 0;
			$t['emails'] = $this->db->query("SELECT * FROM questions WHERE profile_id = {$this->member->data['id']} AND status = 'answered' ORDER BY id")->result_array();
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/email_readings_list', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function email_specials()
		{
		
			if(!$this->member->data['profile_id'])
			{
			
				$this->session->set_flashdata('error', "You must setup your expert profile before you can add email specials");
				redirect("/my_account/main/edit_profile");
			
			}
		
			$getEmailSpecials = $this->db->query("SELECT * FROM email_specials WHERE profile_id = {$this->member->data['profile_id']} ");
			$t['specials'] = $getEmailSpecials->result_array();
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/email_specials', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function client_emails()
		{
		
			$getEmailSpecials = $this->db->query("SELECT * FROM questions WHERE member_id = {$this->member->data['id']} ");
			$t['emails'] = $getEmailSpecials->result_array();
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/email_readings_client', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function client_view($question_id = null)
		{
		
			$t = $this->db->query("SELECT * FROM questions WHERE id = {$question_id} LIMIT 1")->row_array();
			
			$t['expert'] = $this->system_vars->get_member($t['profile_id']);
			
			// Get Messages from this thread
			$t['messages'] = $this->db->query
			("
				SELECT 
					questions_thread.*,
					members.username as username 
					
				FROM 
					questions_thread 
					
				JOIN members on members.id = questions_thread.member_id
					
				WHERE 
					question_id = {$question_id} 
					
				ORDER BY 
					id DESC
			
			")->result_array();
			
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/email_reading_client_view', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function reader_view($question_id = null)
		{
		
			$t = $this->db->query("SELECT * FROM questions WHERE id = {$question_id} LIMIT 1")->row_array();
			
			$t['expert'] = $this->system_vars->get_member($t['profile_id']);
			
			// Get Messages from this thread
			$t['messages'] = $this->db->query
			("
				SELECT 
					questions_thread.*,
					members.username as username 
					
				FROM 
					questions_thread 
					
				JOIN members on members.id = questions_thread.member_id
					
				WHERE 
					question_id = {$question_id} 
					
				ORDER BY 
					id DESC
			
			")->result_array();
			
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/email_reading_reader_view', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function new_special()
		{
		
			$t['title'] = "";
			$t['total_questions'] = "1";
			$t['price'] = "1";
			$t['form_action'] = "/my_account/email_readings/submit_new";
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/email_special_form', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function submit_new()
		{
		
			$this->form_validation->set_rules("title","Title / Short Description","trim|required");
			$this->form_validation->set_rules("total_questions","Total Questions","trim|required|greater_than[0]");
			$this->form_validation->set_rules("price","Price","trim|required|greater_than[0]");
			
			if(!$this->form_validation->run())
			{
			
				$this->new_special();
			
			}
			else
			{
			
				$insert = array();
				$insert['profile_id'] = $this->member->data['id'];
				$insert['title'] = set_value('title');
				$insert['total_questions'] = set_value('total_questions');
				$insert['price'] = set_value('price');
				
				$this->db->insert('email_specials', $insert);
				
				$this->session->set_flashdata('response', "Your Email Special has been added successfully");
				
				redirect("/my_account/email_readings/email_specials");
			
			}
		
		}
		
		function edit_special($id)
		{
		
			$getEmail = $this->db->query("SELECT * FROM email_specials WHERE id = {$id} LIMIT 1");
			$t = $getEmail->row_array();
			
			$t['form_action'] = "/my_account/email_readings/save_special/{$id}";
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/email_special_form', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
	
		function save_special($id)
		{
		
			$this->form_validation->set_rules("title","Title / Short Description","trim|required");
			$this->form_validation->set_rules("total_questions","Total Questions","trim|required|greater_than[0]");
			$this->form_validation->set_rules("price","Price","trim|required|greater_than[0]");
			
			if(!$this->form_validation->run())
			{
			
				$this->edit_special($id);
			
			}
			else
			{
			
				$insert = array();
				$insert['profile_id'] = $this->member->data['id'];
				$insert['title'] = set_value('title');
				$insert['total_questions'] = set_value('total_questions');
				$insert['price'] = set_value('price');
				
				$this->db->where('id', $id);
				$this->db->update('email_specials', $insert);
				
				$this->session->set_flashdata('response', "Your Email Special has been saved successfully");
				
				redirect("/my_account/email_readings/email_specials");
			
			}
		
		}
		
		function delete_special($id)
		{
		
			$this->db->where('id', $id);
			$this->db->where('profile_id', $this->member->data['id']);
			$this->db->delete('email_specials');
			
			$this->session->set_flashdata("response", "You have successfully removed an email special");
			
			redirect("/my_account/email_readings/email_specials");
		
		}
		
		function mark_as_answered($question_id = null)
		{
		
			// Get questions detials
			$question = $this->db->query
			("
			
				SELECT
					c.id as client_id,
					c.email as client_email,
					c.username as client_username,
					c.country as client_country,
					
					questions.*
					
				FROM
					questions
					
				JOIN members as c ON c.id = questions.member_id 
					
				WHERE
					questions.id = {$question_id}
					
			")->row_array();
		
			// Update status of question
			$this->db->where('id', $question_id)->update('questions', array('status'=>'answered'));
			
			// Send email to client
			$params = array();
			$params['name'] = $question['client_username'];
            $params['type'] = "email";
			$this->system_vars->m_omail($question['client_id'], 'email_reading_answered', $params);
			
			// Give funds to reader, we need to pass in the region so we can keep track of Canadian balance and US Balance
			// Rob: 10/15/2014 Do we need this call, is the reader paid elsewhere?
			/*
			$region = (trim(strtolower($question['client_country']))=='ca' ? "ca" : "us");
			$this->member_funds->pay_reader('email', $question['price'], "Email Reading #{$question['id']}", $region);
			*/
			
			// Redirect
			$this->session->set_flashdata('response', "Email reading has been marked as answered, the client has been notified and funds were released to your account.");
			redirect("/my_account/email_readings/open_requests");
		
		}
	
		function send_message($question_id = null)
		{
		
			if($this->input->post('message'))
			{
			
				// Get question, reader and client info
				$question = $this->db->query
				("
				
					SELECT
						r.id as reader_id,
						r.email as reader_email,
						r.username as reader_username,
						
						c.id as client_id,
						c.email as client_email,
						c.username as client_username,
						
						questions.*
						
					FROM
						questions
						
					JOIN members as r ON r.id = questions.profile_id
					JOIN members as c ON c.id = questions.member_id 
						
					WHERE
						questions.id = {$question_id}
						
				")->row_array();
			
				$insert = array();
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['question_id'] = $question_id;
				$insert['member_id'] = $this->member->data['id'];
				$insert['message'] = $this->input->post('message');
				
				$this->db->insert('questions_thread', $insert);
				
				// Compile email params
				$params = array();
				$params['name'] = $this->member->data['username'];
				$params['message'] = $this->input->post('message');
				
				// Notify Reader of a new message
				if($this->member->data['id'] != $question['reader_id'])
				{
				
					$this->session->set_flashdata('response', "Your message has been sent to {$question['reader_username']}");
					$this->system_vars->omail($this->member->data['id'],'new_email_reading_message',$params);
					
					redirect("/my_account/email_readings/client_view/{$question_id}");
					
					exit;
				
				}
				
				// Notify Client of a new message
				if($this->member->data['id'] != $question['client_id'])
				{
				
					$this->session->set_flashdata('response', "Your message has been sent to {$question['client_username']}");
					$this->system_vars->omail($question['client_email'],'new_email_reading_message',$params);
					
					redirect("/my_account/email_readings/reader_view/{$question_id}");
					
					exit;
				
				}
				
			}
		
		}
	
	}
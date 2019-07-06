<?

	class questions extends CI_Controller
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
		
		function index($status = 'new')
		{
		
			switch($status)
			{
			
				case "new":
				
					$t['status'] = "New";
					$t['status_description'] = "The following list of questions are waiting for your to submit a price.";
					
					$getNewQuestions = $this->db->query("SELECT * FROM questions WHERE expert_id = {$this->member->data['id']} AND status='new' AND (deadline IS NULL OR deadline >= '".date("Y-m-d H:i:s")."') ");
					$t['questions'] = $getNewQuestions->result_array();
				
				break;
				
				case "pending":
				
					$t['status'] = "Pending";
					$t['status_description'] = "The following questions have been accepted and paid for. The client is waiting for your answer. Please go through and submit your answer to the following questions. Please keep their deadline in mind.";
					
					$getNewQuestions = $this->db->query("SELECT * FROM questions WHERE expert_id = {$this->member->data['id']} AND status='pending' ");
					$t['questions'] = $getNewQuestions->result_array();
					
				break;
				
				case "answered":
				
					$t['status'] = "Answered";
					$t['status_description'] = "The following list of questions have already been answered and are just for reference.";
					
					$getNewQuestions = $this->db->query("SELECT * FROM questions WHERE expert_id = {$this->member->data['id']} AND status='answered' ");
					$t['questions'] = $getNewQuestions->result_array();
					
				break;
				
				case "declined":
				
					$t['status'] = "Declined";
					$t['status_description'] = "The following list of questions have been declined by you or the client.";
					
					$getNewQuestions = $this->db->query("SELECT * FROM questions WHERE expert_id = {$this->member->data['id']} AND status='declined' ");
					$t['questions'] = $getNewQuestions->result_array();
					
				break;
			
			}
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/questions', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function view($question_id)
		{
		
			$getQuestion = $this->db->query("SELECT * FROM questions WHERE id = {$question_id} LIMIT 1");
			$t = $getQuestion->row_array();
			
			$t['client'] = $this->system_vars->get_member($t['member_id']);
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/question_view', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function decline($question_id, $reason)
		{
		
			$update = array();
			$update['status'] = 'declined';
		
			switch($reason)
			{
			
				case "busy":
				$update['decline_reason'] = "Too busy at this time";
				break;
				
				case "unqualified":
				$update['decline_reason'] = "I am not qualified to answer this question";
				break;
			
			}
			
			$this->db->where('id', $question_id);
			$this->db->update('questions', $update);
			
			// Get Question Info
			$getQuestion = $this->db->query("SELECT * FROM questions WHERE id = {$question_id} LIMIT 1");
			$question = $getQuestion->row_array();
			
			// Get client email
			$client = $this->system_vars->get_member($question['member_id']);
			$expert = $this->system_vars->get_member($question['expert_id']);
			
			$question['client_name'] = $client['first_name']." ".$client['last_name'];
			$question['expert_name'] = $expert['first_name']." ".$expert['last_name'];
			
			// Send Email
			$this->system_vars->omail($client['email'], 'question_declined', $question);
			
			// Set response
			$this->session->set_flashdata("response", "Question has been declined and client has been notified");
			
			// redirect
			redirect("/my_account/questions");
		
		}
		
		function submit_bid($question_id)
		{
		
			$this->form_validation->set_rules('price','Price','required|trim|grater_than[0]');
			$this->form_validation->set_rules('timeframe','Timeframe','required|trim|xss_clean');
			
			if(!$this->form_validation->run())
			{
			
				$this->view($question_id);
			
			}
			else
			{
			
				// Update database
				$update = array();
				$update['bid_amount'] = set_value("price");
				$update['timeframe'] = set_value("timeframe");
				$update['status'] = "unaccepted";
				
				$this->db->where('id', $question_id);
				$this->db->update('questions', $update);
				
				// Get Question Info
				$getQuestion = $this->db->query("SELECT * FROM questions WHERE id = {$question_id} LIMIT 1");
				$question = $getQuestion->row_array();
				
				// Get client email
				$client = $this->system_vars->get_member($question['member_id']);
				$expert = $this->system_vars->get_member($question['expert_id']);
				
				$question['client_name'] = $client['first_name']." ".$client['last_name'];
				$question['expert_name'] = $expert['first_name']." ".$expert['last_name'];
				
				// Send Email
				$this->system_vars->omail($client['email'], 'question_bid_submitted', $question);
				
				// Set response
				$this->session->set_flashdata("response", "Your bid has been sent to the client. You will get an email when they have accepted or declined your bid.");
				
				// redirect
				redirect("/my_account/questions");
			
			}
		
		}
		
		function submit_answer($question_id)
		{
		
			$this->form_validation->set_rules("answer","Answer","required|trim|xss_clean");
			
			if(!$this->form_validation->run())
			{
			
				$this->submit_bid($question_id);
			
			}
			else
			{
			
				// Update database
				$update = array();
				$update['answer'] = set_value("answer");
				$update['status'] = "answered";
				
				$this->db->where('id', $question_id);
				$this->db->update('questions', $update);
				
				// Get Question Info
				$getQuestion = $this->db->query("SELECT * FROM questions WHERE id = {$question_id} LIMIT 1");
				$question = $getQuestion->row_array();
				
				// Get client email
				$client = $this->system_vars->get_member($question['member_id']);
				$expert = $this->system_vars->get_member($question['expert_id']);
				
				$question['client_name'] = $client['first_name']." ".$client['last_name'];
				$question['expert_name'] = $expert['first_name']." ".$expert['last_name'];
				
				// Give credits to the expert for this question
				$insert = array();
				$insert['member_id'] = $this->member->data['id'];
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['type'] = 'earning';
				$insert['amount'] = $question['bid_amount'];
				$insert['summary'] = "Question Payment for #{$question['question_id']}";
				$this->db->insert('transactions', $insert);
				
				// Send Email
				$this->system_vars->omail($client['email'], 'question_answered', $question);
				
				// Set response
				$this->session->set_flashdata("response", "Your answer has been sent to the client. This question has been marked as complete and can be found in your \"Answered\" questions archive.");
				
				// redirect
				redirect("/my_account/questions");
			
			}
		
		}
	
	}
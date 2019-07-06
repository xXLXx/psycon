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
			else
			{
			
				$this->member = $this->system_vars->get_member($this->session->userdata('member_logged'));
			
			}
			
		}
		
		function index($status = 'pending')
		{
		
			$getNewQuestions = $this->db->query("SELECT * FROM questions WHERE member_id = {$this->member['id']} ORDER BY id DESC");
			$t['questions'] = $getNewQuestions->result_array();
		
			$this->load->view('header');
			$this->load->view('client/header');
			$this->load->view('client/questions', $t);
			$this->load->view('client/footer');
			$this->load->view('footer');
		
		}
		
		function view($question_id)
		{
		
			$this->session->unset_userdata('fund_redirect');
		
			$getQuestion = $this->db->query("SELECT * FROM questions WHERE id = {$question_id} LIMIT 1");
			$t = $getQuestion->row_array();
			
			$t['client'] = $this->system_vars->get_member($t['member_id']);
		
			$this->load->view('header');
			$this->load->view('client/header');
			$this->load->view('client/question_view', $t);
			$this->load->view('client/footer');
			$this->load->view('footer');
		
		}
		
		function accept($question_id)
		{
		
			// Get Question
			$getQuestion = $this->db->query("SELECT * FROM questions WHERE id = {$question_id} AND member_id = {$this->member['id']} LIMIT 1");
			$question = $getQuestion->row_array();
		
			// Check the user's balance to see if they have sufficient
			// Credits to pay for this question
			
			$balance = $this->system_vars->member_balance($this->member['id']);
			
			if($balance >= $question['bid_amount'])
			{
			
				// Deduct the credits
				$insert = array();
				$insert['member_id'] = $this->member['id'];
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['type'] = 'purchase';
				$insert['amount'] = $question['bid_amount'];
				$insert['summary'] = "Question #:{$question['id']}";
				
				$this->db->insert('transactions', $insert);
			
				// Set the question to PENDING
				$this->db->where('id', $question_id);
				$this->db->where('member_id', $this->member['id']);
				$this->db->update('questions', array('status'=>'pending'));
				
				// Send email to expert
				// Telling them their bid was accepted and to answet the question
				
				$expert = $this->system_vars->get_member($question['expert_id']);
				
				$params = array();
				$params['expert_first_name'] = $expert['first_name'];
				$params['expert_last_name'] = $expert['last_name'];
				$params['client_username'] = $this->member['username'];
				$params['question_title'] = $question['title'];
				
				$this->system_vars->omail($expert['email'], 'question_bid_accepted', $params);
				
				$this->session->set_flashdata('response', "You have successfully accepted the experts bid. You will get an email when your question has been answered.");
				
				redirect("/client/questions/");
			
			}
			else
			{
			
				$this->session->set_userdata('fund_redirect', "/client/questions/view/{$question['id']}");
				$this->session->set_flashdata("error", "Your current balance doesn't cover the bid amount, please fund your account and you will be directed back to the questions section, so you can accept the bid again.");
				redirect("/my_account/transactions/fund_your_account");
			
			}
		
		}
		
		function decline($question_id)
		{
		
			// Set the question to PENDING
			$this->db->where('id', $question_id);
			$this->db->where('member_id', $this->member['id']);
			$this->db->update('questions', array('status'=>'declined', 'decline_reason'=>'Bid was not accepted'));
			
			// Send email to expert
			// Telling them their bid was accepted and to answet the question
			
			$this->session->set_flashdata('response', "You have declined the experts bid.");
			
			redirect("/client/questions/");
		
		}
	
	}
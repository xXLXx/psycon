<?

	class qnabids extends CI_Controller
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
				
					$t['status'] = "Bid History";
					$t['status_description'] = "Here is a list of all your active bids. These bids are pending, the client is still in the process of selecting an expert.";
					
					$getNewQuestions = $this->db->query("SELECT b.*,q.title as question_title FROM qna_bids b,qna_questions q WHERE q.id = b.question_id AND b.expert_id = {$this->member->data['id']} AND b.awarded=0 GROUP BY b.id ");
					$t['bids'] = $getNewQuestions->result_array();
				
				break;
				
				case "awarded":
				
					$t['status'] = "Awarded Questions";
					$t['status_description'] = "The following questions have been awarded to you, please go through and submit you answer.";
					
					$getNewQuestions = $this->db->query("SELECT b.*,q.title as question_title FROM qna_bids b,qna_questions q WHERE q.id = b.question_id AND b.expert_id = {$this->member->data['id']} AND b.awarded=1 AND answer IS NULL GROUP BY b.id ");
					$t['bids'] = $getNewQuestions->result_array();
					
				break;
				
				case "answered":
				
					$t['status'] = "Answered Questions";
					$t['status_description'] = "The following questions have already been answered and are just for reference. No action is required on your part.";
					
					$getNewQuestions = $this->db->query("SELECT b.*,q.title as question_title FROM qna_bids b,qna_questions q WHERE q.id = b.question_id AND b.expert_id = {$this->member->data['id']} AND b.awarded=1 AND answer IS NOT NULL GROUP BY b.id ");
					$t['bids'] = $getNewQuestions->result_array();
					
				break;
			
			}
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/qnabids', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function view($bid_id)
		{
		
			$get = $this->db->query
			("
			
				SELECT 
					qna_bids.id as bid,
					qna_bids.awarded, 
					qna_bids.answer,
					qna_bids.datetime as bid_date,
					qna_bids.cover_letter as cover_letter,
					qna_questions.*,
					categories.title as category_title, 
					categories.url as category_url, 
					subcategories.title as subcategory_title, 
					subcategories.url as subcategory_url
				
				FROM  qna_bids
					
				LEFT JOIN qna_questions ON qna_questions.id = qna_bids.question_id
				LEFT JOIN categories ON categories.id = qna_questions.category_id
				LEFT JOIN subcategories ON subcategories.id = qna_questions.subcategory_id
				
				WHERE 
					qna_bids.id = {$bid_id} 
					
				GROUP BY 
					qna_bids.id
					
			");
			
			$t = $get->row_array();
			
			$t['member'] = $this->system_vars->get_member($t['member_id']);
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/qnabid_view', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
				
		function submit_answer($bid_id)
		{
		
			$this->form_validation->set_rules("answer","Answer","required|trim|xss_clean");
			
			if(!$this->form_validation->run())
			{
			
				$this->view($bid_id);
			
			}
			else
			{
			
				// Update database
				$update = array();
				$update['answer'] = set_value("answer");
				
				$this->db->where('id', $bid_id);
				$this->db->update('qna_bids', $update);
				
				// Get Question Info
				$getQuestion = $this->db->query
				("
					SELECT 
						qna_bids.*,
						qna_questions.title as question_title, 
						qna_questions.member_id as member_id 
						
					FROM 
						qna_bids
					
					LEFT JOIN qna_questions ON qna_questions.id = qna_bids.question_id
					
					WHERE 
						qna_bids.id = {$bid_id}
						
					GROUP BY 
						qna_bids.id 
						
					LIMIT 1
				");
				
				$bid = $getQuestion->row_array();
				
				// Get client email
				$client = $this->system_vars->get_member($bid['member_id']);
				$expert = $this->member->data;
				
				$question['question_title'] = $bid['question_title'];
				$question['answer'] = nl2br(set_value("answer"));
				$question['client_first_name'] = $client['first_name'];
				$question['client_last_name'] = $client['last_name'];
				$question['expert_username'] = $expert['username'];
				
				// Give credits to the expert for this question
				$insert = array();
				$insert['member_id'] = $this->member->data['id'];
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['type'] = 'earning';
				$insert['amount'] = $bid['bid'];
				$insert['summary'] = "QnA Bids Payment for question #{$bid['question_id']}";
				$this->db->insert('transactions', $insert);
				
				// Mark question as answered
				// $this->db->where('id', $bid['question_id']);
				// $this->db->update('qna_questions', array('status'=>'answered'));
				
				// Send Email
				$this->system_vars->omail($client['email'], 'qna_question_answered', $question);
				
				// Set response
				$this->session->set_flashdata("response", "Your answer has been sent to the client. This question has been marked as complete and can be found in your \"Answered\" questions archive.");
				
				// redirect
				redirect("/my_account/qnabids");
			
			}
		
		}
	
	}
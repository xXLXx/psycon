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
			else
			{
			
				$this->member = $this->system_vars->get_member($this->session->userdata('member_logged'));
			
			}
			
		}
		
		function index($status = 'new')
		{
		
			$getQNA = $this->db->query("SELECT qna_questions.*, (SELECT count(id) FROM qna_bids WHERE qna_bids.question_id = qna_questions.id) as total_bids FROM qna_questions WHERE member_id = {$this->member['id']} ORDER BY id DESC");
			$t['questions'] = $getQNA->result_array();
			
			$this->load->view('header');
			$this->load->view('client/header');
			$this->load->view('client/qnabids', $t);
			$this->load->view('client/footer');
			$this->load->view('footer');
		
		}
		
		function new_question()
		{
		
			$t['categories'] = $this->system_vars->get_categories('all');
			$t['question'] = $this->session->userdata('question');
			
			$this->session->unset_userdata('question');
		
			$this->load->view('header');
			$this->load->view('client/header');
			$this->load->view('client/qnabids_new', $t);
			$this->load->view('client/footer');
			$this->load->view('footer');
		
		}
		
		function view($bid_id)
		{
		
			$get = $this->db->query("SELECT b.awarded, b.answer, b.datetime as bid_date,b.cover_letter as cover_letter, q.*, c.title as category_title, c.url as category_url, s.title as subcategory_title, s.url as subcategory_url FROM qna_bids b,qna_questions q, categories c, subcategories s WHERE q.id = b.question_id AND c.id = q.category_id AND s.id = q.subcategory_id AND b.expert_id = {$this->member['id']} AND b.id={$bid_id} GROUP BY b.id ");
			$t = $get->row_array();
			
			$t['member'] = $this->system_vars->get_member($t['member_id']);
		
			$this->load->view('header');
			$this->load->view('client/header');
			$this->load->view('client/qnabid_view', $t);
			$this->load->view('client/footer');
			$this->load->view('footer');
		
		}
		
		function check_date($date)
		{
		
			if($this->input->post('deadline')=='1')
			{
			
				if($date)
				{
				
					$datestring = strtotime(str_replace("@","",$date));
					
					if($datestring > time())
					{
					
						return true;
					
					}
					else
					{
					
						$this->form_validation->set_message("check_date", "Your deadline date must be a date in the future");
						return false;
					
					}
				
				}
				else
				{
				
					$this->form_validation->set_message("check_date", "If you want to require a deadline, please select a date or uncheck the \"Deadline\" box.");
					return false;
				
				}
			
			}
			else
			{
			
				return true;
			
			}
		
		}
				
		function submit_question()
		{
		
			$this->form_validation->set_rules('title','Title','required|trim|xss_clean');
			$this->form_validation->set_rules('question','Question','trim|xss_clean');
			$this->form_validation->set_rules('category','Category','required|trim|xss_clean');
			$this->form_validation->set_rules('subcategory','Subcategory','required|trim|xss_clean');
			$this->form_validation->set_rules('timeframe','Timeframe','required|trim|xss_clean');
			$this->form_validation->set_rules('price','Price','required|trim|xss_clean');
			$this->form_validation->set_rules('deadline','Deadline','trim|xss_clean');
			$this->form_validation->set_rules('date','Deadline Date','trim|xss_clean|callback_check_date');
			
			if(!$this->form_validation->run())
			{
			
				$this->new_question();
			
			}
			else
			{
			
				$insert = array();
				$insert['member_id'] = $this->member['id'];
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['title'] = set_value('title');
				$insert['question'] = set_value('question');
				$insert['category_id'] = set_value('category');
				$insert['subcategory_id'] = set_value('subcategory');
				$insert['timeframe'] = set_value('timeframe');
				$insert['expiration_date'] = date("Y-m-d H:i:s", strtotime("+{$insert['timeframe']} days"));
				$insert['budget'] = set_value('price');
				$insert['deadline'] = set_value('deadline');
				$insert['deadline_date'] = date("Y-m-d H:i:s", strtotime($insert['date']));
				$insert['status'] = 'active';
				
				$this->db->insert('qna_questions', $insert);
				$qid = $this->db->insert_id();
				
				// Send email to everyone in the category
				$getProfiles = $this->db->query("SELECT profiles.*, members.email as email, members.first_name, members.last_name, categories.title as category_title FROM profiles LEFT JOIN members ON members.id = profiles.member_id LEFT JOIN categories ON categories.id = profiles.category_id WHERE category_id = ".set_value('category')." GROUP BY members.id ");
				
				foreach($getProfiles->result_array() as $p)
				{
				
					$trans = array();
					$trans['expert_first_name'] = $p['first_name'];
					$trans['expert_last_name'] = $p['last_name'];
					$trans['category_title'] = $p['category_title'];
					$trans['title'] = set_value("title");
					$trans['question'] = set_value("question");
					
					$this->system_vars->omail($p['email'], 'qna_new_question', $trans);
				
				}
				
				$this->session->set_flashdata('response', "Your question has been posted to the QnA Bids section. You will be notified through email when you have received bids for this question.");
				
				redirect("/qnabids/view/{$qid}");
			
			}
		
		}
		
		function hire_expert($bid_id)
		{
		
			$getBid = $this->db->query("SELECT * FROM qna_bids WHERE id = {$bid_id} LIMIT 1");
			$bid = $getBid->row_array();
			
			$getQuestion = $this->db->query("SELECT * FROM qna_questions WHERE id = {$bid['question_id']} LIMIT 1");
			$question = $getQuestion->row_array();
			
			// Check the user's balance
			// Make sure they have enough funds to cover the balance
			
			$balance = $this->system_vars->member_balance($this->member['id']);
			
			if($balance >= $bid['bid'])
			{
			
				// Send Email to expert
				$expert = $this->system_vars->get_member($bid['expert_id']);
				$client = $this->member;
				
				// Send email to expert
				$params = array();
				$params['expert_first_name'] = $expert['first_name'];
				$params['expert_last_name'] = $expert['last_name'];
				$params['client_username'] = $client['username'];
				$params['title'] = $question['title'];
				
				$this->system_vars->omail($expert['email'],'qna_expert_hired', $params);
				
				// Award BID to the expert
				$this->db->where('id', $bid_id);
				$this->db->update('qna_bids', array('awarded'=>'1'));
				
				// Subtract from their balance
				$insert = array();
				$insert['member_id'] = $this->member['id'];
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['type'] = 'purchase';
				$insert['amount'] = $bid['bid'];
				$insert['summary'] = "Hiring expert '{$expert['username']}' for QnA Question '{$question['title']}' ";
				
				$this->db->insert('transactions', $insert);
				
				// Redirect
				$this->session->set_flashdata('response', "The expert you selected has been hired and notified. Please allow some time for the expert to provide you with an answer. You will get an email when you have received an answer.");
				redirect("/qnabids/view/{$question['id']}");
				
			}
			else
			{
			
				// error
				$this->session->set_flashdata('error', "You do not have enough funds in your account to cover this transaction. Please fund your account, then attempt to re-hire this expert. <a href='/my_account/transactions/fund_your_account'>Click here to fund your account.</a>");
				redirect("/qnabids/view/{$question['id']}");
			
			}
		
		}
		
		function close_question($question_id)
		{
		
			$this->db->where('id', $question_id);
			$this->db->update('qna_questions', array('status'=>'closed'));
			
			$this->session->set_flashdata('response', "Your question has been closed");
			
			redirect("/qnabids/view/{$question_id}");
		
		}
	
	}
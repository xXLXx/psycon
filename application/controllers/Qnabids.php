<?

	class qnabids extends CI_Controller
	{
	
		function __construct()
		{
			
			parent :: __construct();
			
			if($this->session->userdata('member_logged'))
			{
			
				$this->member = $this->system_vars->get_member($this->session->userdata('member_logged'));
				
			}
			
		}
		
		function index()
		{
		
			$date = date("Y-m-d H:i:s");
			
			$get = $this->db->query("SELECT q.*,c.title as category_title,c.url as category_url, s.title as subcategory_title, s.url as subcategory_url FROM qna_questions q,categories c,subcategories s WHERE (c.id=q.category_id) AND (s.id=q.subcategory_id) AND q.status = 'active' AND q.expiration_date >= '{$date}' GROUP BY q.id");
			
			$t['questions'] = $get->result_array();
		
			$this->load->view('header');
			$this->load->view('qnabids/main', $t);
			$this->load->view('footer');
		
		}
		
		function view($question_id)
		{
		
			$get = $this->db->query("SELECT q.*,c.title as category_title,c.url as category_url, s.title as subcategory_title, s.url as subcategory_url FROM qna_questions q,categories c,subcategories s WHERE (c.id=q.category_id) AND (s.id=q.subcategory_id) AND q.status = 'active' AND q.id = '{$question_id}' GROUP BY q.id LIMIT 1");
			$t = $get->row_array();
			
			$t['member'] = $this->system_vars->get_member($t['member_id']);
					
			$get_bids = $this->db->query("SELECT id FROM qna_bids WHERE question_id = {$t['id']} ");
			$t['total_bids'] = $get_bids->num_rows();
			
			$this->load->view('header');
			$this->load->view('qnabids/view', $t);
			$this->load->view('footer');
			
		}
		
		function place_bid($question_id)
		{
		
			if(isset($this->member))
			{
		
				$get = $this->db->query("SELECT q.*,c.title as category_title,c.url as category_url, s.title as subcategory_title, s.url as subcategory_url FROM qna_questions q,categories c,subcategories s WHERE (c.id=q.category_id) AND (s.id=q.subcategory_id) AND q.status = 'active' AND q.id = '{$question_id}' GROUP BY q.id LIMIT 1");
				$t = $get->row_array();
				
				$t['member'] = $this->system_vars->get_member($t['member_id']);
						
				$get_bids = $this->db->query("SELECT id FROM qna_bids WHERE question_id = {$t['id']} ");
				$t['total_bids'] = $get_bids->num_rows();
				
				$this->load->view('header');
				$this->load->view('qnabids/place_bid', $t);
				$this->load->view('footer');
			
			}
			else
			{
			
				$this->session->set_flashdata('error', "You must be logged in before you can place your bid.");
				$this->session->set_userdata('redirect', $this->uri->uri_string());
				
				redirect('/register/login');
			
			}
		
		}
		
		function submit_bid($question_id)
		{
		
			$this->form_validation->set_rules('amount','Amount','required|trim|xss_clean|greater_than[0]');
			$this->form_validation->set_rules('cover_letter','Cover Letter','required|trim|xss_clean');
			
			if(!$this->form_validation->run())
			{
			
				$this->place_bid($question_id);
			
			}
			else
			{
			
				$insert = array();
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['question_id'] = $question_id;
				$insert['expert_id'] = $this->member['id'];
				$insert['bid'] = set_value('amount');
				$insert['cover_letter'] = set_value('cover_letter');
				
				$this->db->insert('qna_bids', $insert);
				
				// Get email info
				$get = $this->db->query("SELECT q.*,c.title as category_title,c.url as category_url, s.title as subcategory_title, s.url as subcategory_url FROM qna_questions q,categories c,subcategories s WHERE (c.id=q.category_id) AND (s.id=q.subcategory_id) AND q.status = 'active' AND q.id = '{$question_id}' GROUP BY q.id LIMIT 1");
				$t = $get->row_array();
				
				$client = $this->system_vars->get_member($t['member_id']);
				$expert = $this->member;
				
				$t['client_name'] = $client['first_name']." ".$client['last_name'];
				$t['expert_name'] = $expert['first_name']." ".$expert['last_name'];
				$t['bid_amount'] = set_value('amount');
				$t['cover_letter'] = set_value('cover_letter');
				
				$this->system_vars->omail($client['email'],"qna_new_bid",$t);
				
				// redirect
				$this->session->set_flashdata('response', "Your bid has been sent to the client");
				redirect("/qnabids/view/{$question_id}");
			
			}
		
		}
		
		function faqs()
		{
		
			$getQuestions = $this->db->query("SELECT * FROM qna_faqs ORDER BY sort ");
			$t['faqs'] = $getQuestions->result_array();
			
			$this->load->view('header');
			$this->load->view('qnabids/faqs', $t);
			$this->load->view('footer');
		
		}
	
	}
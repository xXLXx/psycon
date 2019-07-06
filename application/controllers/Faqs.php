<?

	class faqs extends CI_Controller
	{
	
		function __construct()
		{
		
			parent :: __construct();
			
		}
		
		function index()
		{
		
			$getQuestions = $this->db->query("SELECT * FROM faqs WHERE type = 'Clients' ORDER BY sort ");
			$t['clients'] = $getQuestions->result_array();
			
			$getQuestions = $this->db->query("SELECT * FROM faqs WHERE type = 'Readers' ORDER BY sort ");
			$t['readers'] = $getQuestions->result_array();
		
			$this->load->view('header');
			$this->load->view('faqs/faq_main', $t);
			$this->load->view('footer');
		
		}
		
		function clients()
		{
		
			$getQuestions = $this->db->query("SELECT * FROM faqs WHERE type = 'Clients' ORDER BY sort ");
			$t['faqs'] = $getQuestions->result_array();
			$t['title'] = "Client FAQs";
			
			$this->load->view('header');
			$this->load->view('faqs/questions_list', $t);
			$this->load->view('footer');
		
		}
		
		function readers()
		{
		
			$getQuestions = $this->db->query("SELECT * FROM faqs WHERE type = 'Readers' ORDER BY sort ");
			$t['faqs'] = $getQuestions->result_array();
			$t['title'] = "Expert FAQs";
			
			$this->load->view('header');
			$this->load->view('faqs/questions_list', $t);
			$this->load->view('footer');
		
		}
	
	}
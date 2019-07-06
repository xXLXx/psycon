<?

	class testimonials extends CI_Controller
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
		
		function index()
		{
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/testimonials');
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
	
	}
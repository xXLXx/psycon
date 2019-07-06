<?

	class main extends CI_Controller
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
		
		function index()
		{
		
			$this->load->view('header');
			$this->load->view('client/header');
			$this->load->view('client/main');
			$this->load->view('client/footer');
			$this->load->view('footer');
		
		}
	
	}
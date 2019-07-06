<?

	class page_search extends CI_Controller
	{
	
		function __construct()
		{
			
			parent :: __construct();
			
			$this->settings = $this->system_vars->get_settings();
			
		}
		
		function index()
		{
		
			$uri = $this->uri->segment('1');
			
			$findReader = $this->db->query
			("
			
				SELECT 
					members.username 
					
				FROM 
					members
					
				JOIN profiles ON profiles.id = members.id AND profiles.active = 1
				
				WHERE members.username = '{$uri}'
				
				LIMIT 1
			
			")->row_array();
			
			if($findReader)
			{
			
				redirect("/profile/{$findReader['username']}");
			
			}
			else
			{
			
				$getPage = $this->db->query("SELECT * FROM pages WHERE url = '{$uri}' LIMIT 1");
				
				if($getPage->num_rows()==0)
				{
				
					$this->load->view('header');
					$this->load->view('error_page');
					$this->load->view('footer');
				
				}
				else
				{
				
					$this->load->view('header');
					$this->load->view('static_page', $getPage->row_array());
					$this->load->view('footer');
				
				}
			
			}
		
		}
	
	}
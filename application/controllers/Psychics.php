<?

	class psychics extends CI_Controller
	{
	
		function __construct()
		{
		
			parent :: __construct();
		
		}
		
		function index(){

            $t = array();
			$t['page'] = '0';
			$t['readers'] = $this->readers->getAll();
			$this->load->view('header');
			$this->load->view('psychics/main', $t);
			$this->load->view('footer');
		
		}
		
		function search()
		{
		
			// Format variables for easy use
			$category_id = trim( $this->input->post('category') );
			$query = trim( $this->input->post('query') );
			
			$searchResults = $this->readers->search($category_id, $query);

			if($searchResults['error']=='1')
			{
			
				$this->session->set_flashdata('warning', $searchResults['message']);
				redirect("/psychics");
			
			}
			else
			{
			
				$t['readers'] = $searchResults['readers'];
			
				$this->load->view('header');
				$this->load->view('psychics/main', $t);
				$this->load->view('footer');
			
			}
		
		}
	
	}
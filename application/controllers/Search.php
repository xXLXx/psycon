<?

	class search extends CI_Controller
	{
	
		function __construct()
		{
			parent :: __construct();
		}
		
		function index()
		{
		
			$query = $this->input->post('query');
			
			$getExperts = $this->db->query("SELECT profiles.* FROM members,profiles WHERE (first_name LIKE \"%{$query}%\" OR last_name LIKE \"%{$query}%\" OR members.id LIKE \"%{$query}%\") AND expert = 1 AND profiles.member_id = members.id ");
			$t['experts'] = $getExperts->result_array();
		
			$this->load->view('header');
			$this->load->view('pages/expert_list', $t);
			$this->load->view('footer');
		
		}
	
	}
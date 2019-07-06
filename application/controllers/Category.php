<?

	class category extends CI_Controller
	{
	
		function __construct()
		{
			parent :: __construct();
		}
		
		function main($category_url)
		{
		
			// get all experts in this main category
			$featured_experts = array();
			$date = date("Y-m-d H:i:s");
			$getTopExperts = $this->db->query("SELECT p.* FROM profiles p,categories c,featured f WHERE (p.category_id = c.id AND c.url = '{$category_url}') AND (f.type = 'category' AND f.profile_id = p.id AND f.end_date >= '{$date}') GROUP BY p.id");
			$topExperts = $getTopExperts->result_array();
			
			foreach($topExperts as $e){ $featured_experts[] = $e['id']; }
			foreach($topExperts as $i=>$e){ $topExperts[$i]['featured'] = 1; }
			
			$expert_string = implode(',', $featured_experts);
			
			// Get All other experts (exclude top experts)
			$getStandardExperts = $this->db->query("SELECT p.* FROM profiles p, categories c WHERE p.category_id = c.id AND c.url = '{$category_url}' ".(trim($expert_string) ? "AND p.id NOT IN ({$expert_string})" : "")." GROUP BY p.id ");
			
			$t['experts'] = array_merge($topExperts, $getStandardExperts->result_array());
		
			$this->load->view('header');
			$this->load->view('pages/expert_list', $t);
			$this->load->view('footer');
		
		}
		
		function sub($category_url,$subcategory_url)
		{
		
			$date = date("Y-m-d H:i:s");
		
			// Get featured expertrs
			$getFeaturedExperts = $this->db->query( "
			
				SELECT
					p.*
					
				FROM
					profiles p,
					subcategories c,
					featured f
					
				WHERE
					c.url = '{$subcategory_url}' AND 
					EXISTS(SELECT * FROM profile_subcategories WHERE subcategory_id = c.id AND profile_id = p.id) AND 
					(f.type = 'category' AND f.profile_id = p.id AND f.end_date >= '{$date}')
					
				GROUP BY
					p.id
				
			");
			
			$featured_experts = array();
			$topExperts = $getFeaturedExperts->result_array();
		
			foreach($topExperts as $e){ $featured_experts[] = $e['id']; }
			foreach($topExperts as $i=>$e){ $topExperts[$i]['featured'] = 1; }
			
			$expert_string = implode(',', $featured_experts);
			
			// Get all other exprts MINUS top expets
			$getExperts = $this->db->query( "
			
				SELECT
					p.*
					
				FROM
					profiles p,
					subcategories c
					
				WHERE
					".(trim($expert_string) ? "p.id NOT IN ({$expert_string}) AND " : "")."
					c.url = '{$subcategory_url}' AND 
					EXISTS(SELECT * FROM profile_subcategories WHERE subcategory_id = c.id AND profile_id = p.id)
					
				GROUP BY
					p.id
				
			");
			
			$t['experts'] = array_merge($topExperts, $getExperts->result_array());
			
			$this->load->view('header');
			$this->load->view('pages/expert_list', $t);
			$this->load->view('footer');
		
		}
	
	}
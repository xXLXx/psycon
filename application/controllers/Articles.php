<?

	class articles extends CI_Controller
	{
	
		function __construct()
		{
		
			parent :: __construct();
		
		}
		
		function index()
		{
		
			$getArticles = $this->db->query
			("
			
				SELECT 
					articles.*,
					categories.url as category_url
				
				FROM 
					articles 
					
				JOIN categories ON categories.id = articles.category_id
					
				WHERE  
					articles.url IS NOT NULL AND
					articles.approved = 1
					
				ORDER BY 
					articles.datetime DESC 
					
				LIMIT 10
			
			");
			
			$articleArray = $getArticles->result_array();
		
			$t['title'] = "Most Recent Articles";
			$t['archive'] = 0;
			$t['articles'] = $articleArray;
		
			$this->load->view('header');
			$this->load->view('pages/article_categories', $t);
			$this->load->view('footer');
		
		}
		
		function lst($category_url)
		{
		
			$category = $this->site->get_category($category_url);
		
			$getArticles = $this->db->query
			("
			
				SELECT 
					articles.*,
					categories.url as category_url
				
				FROM 
					articles 
					
				JOIN categories ON categories.id = articles.category_id
					
				WHERE 
					categories.url = '{$category_url}' AND 
					articles.url IS NOT NULL AND
					articles.approved = 1
					
				ORDER BY 
					articles.datetime DESC 
					
				LIMIT 10
			
			");
			
			$articleArray = $getArticles->result_array();
			
			$t['category'] = $category;
			$t['title'] = "Most Recent \"{$category['title']}\" Articles";
			$t['archive'] = 0;
			$t['articles'] = $articleArray;
			
			$this->load->view('header');
			$this->load->view('pages/article_categories', $t);
			$this->load->view('footer');
		
		}
		
		function archive($category_url, $pageno = 10)
		{
		
			$per_page = 20;
		
			$category = $this->site->get_category($category_url);
			
			$sql = "
			
				SELECT 
					articles.*,
					categories.url as category_url
				
				FROM 
					articles 
					
				JOIN categories ON categories.id = articles.category_id
					
				WHERE 
					categories.url = '{$category_url}' AND 
					articles.url IS NOT NULL AND
					articles.approved = 1
					
				ORDER BY 
					articles.datetime DESC 
			
			";
		
			$getAllArticles = $this->db->query($sql);
			$getArticles = $this->db->query($sql . " LIMIT {$pageno}, {$per_page}");
			
			$articleArray = $getArticles->result_array();
			
			// Pagination
			$this->load->library('pagination');

			$config['base_url'] = "/articles/{$category_url}/archive/";
			$config['uri_segment'] = '4';
			$config['total_rows'] = $getAllArticles->num_rows();
			$config['per_page'] = $per_page;
			
			$this->pagination->initialize($config);
			
			$t['pagination'] = $this->pagination->create_links();
			
			$t['category'] = $category;
			$t['title'] = "\"{$category['title']}\" Article Archive";
			$t['archive'] = 1;
			$t['articles'] = $articleArray;
			
			$this->load->view('header');
			$this->load->view('pages/article_categories', $t);
			$this->load->view('footer');
		
		}
		
		function view($category_url,$article_url)
		{
		
			$getArticle = $this->db->query("SELECT * FROM articles WHERE url = '{$article_url}' LIMIT 1");
			$t = $getArticle->row_array();
		
			$this->load->view('header');
			$this->load->view('pages/view_article', $t);
			$this->load->view('footer');
		
		}
	
	}
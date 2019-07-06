<?

	class articles extends CI_Controller
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
		
			$getArticles = $this->db->query("SELECT * FROM articles WHERE member_id = {$this->member->data['id']} ");
			$t['articles'] = $getArticles->result_array();
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/articles', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function submit()
		{
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/article_form');
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function submit_post()
		{
		
			$config['upload_path'] = './media/articles/';
			$config['allowed_types'] = 'doc|rtf|txt';
			$config['max_size']	= '2048';
			
			$this->load->library('upload', $config);
			
			if(!$this->upload->do_upload('file'))
			{
			
				$this->session->set_flashdata('error', $this->upload->display_errors());
				redirect("/my_account/articles/submit");
			
			}
			else
			{
			
				$file = $this->upload->data();
				
				$insert = array();
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['profile_id'] = $this->member->data['id'];
				$insert['filename'] = $file['file_name'];
				$insert['title'] = $this->input->post('title');
				
				if($file['file_ext'] == '.txt')
				{
				
					$insert['content'] = file_get_contents("./media/articles/{$file['file_name']}");
				
				}
				
				$this->db->insert('articles', $insert);
				
				$this->session->set_flashdata('response', "Thank you for your submission!");
				redirect("/my_account/articles/submit");
			
			}
		
		}
		
		function edit($article_id)
		{
		
			$getArticle = $this->db->query("SELECT * FROM articles WHERE id = {$article_id} AND member_id = {$this->member->data['id']} LIMIT 1");
			$t = $getArticle->row_array();
		
			$t['page_title'] = "Edit Article";
			$t['save_button'] = "Save Article";
			$t['form_action'] = "/my_account/articles/edit_submit/{$article_id}";
			
			$t['categories'] = $this->system_vars->get_categories('expert', $this->member->data['id']);
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/article_form', $t);
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function check_article_length($string)
		{
		
			if(strlen($string) >= 200)
			{
			
				return true;
			
			}
			else
			{
			
				$this->form_validation->set_message('check_article_length','Your article must be at least 200 characters long');
				return false;
			
			}
		
		}
		
		function create_new_submit()
		{
		
			$this->form_validation->set_rules('category','Category','required|trim|xss_clean');
			$this->form_validation->set_rules('subcategory','Subcategory','required|trim|xss_clean');
			$this->form_validation->set_rules('title','Article Title','required|trim|xss_clean');
			$this->form_validation->set_rules('content','Article Content','required|trim|callback_check_article_length');
			
			if(!$this->form_validation->run())
			{
			
				$this->create_new();
			
			}
			else
			{
			
				// Find the profile id being used based on category
				$getProfile = $this->db->query("SELECT * FROM profiles WHERE member_id = {$this->member->data['id']} AND category_id = ".set_value('category')." LIMIT 1");
				$profile = $getProfile->row_array();
				
				$insert = array();
				$insert['member_id'] = $this->member->data['id'];
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['category_id'] = set_value('category');
				$insert['subcategory_id'] = set_value('subcategory');
				$insert['title'] = set_value('title');
				$insert['content'] = set_value('content');
				$insert['profile_id'] = $profile['id'];
				
				$this->db->insert('articles', $insert);
				$article_id = $this->db->insert_id();
				
				redirect("/my_account/articles");
			
			}
		
		}
		
		function edit_submit($article_id)
		{
		
			$this->form_validation->set_rules('category','Category','required|trim|xss_clean');
			$this->form_validation->set_rules('subcategory','Subcategory','required|trim|xss_clean');
			$this->form_validation->set_rules('title','Article Title','required|trim|xss_clean');
			$this->form_validation->set_rules('content','Article Content','required|trim|callback_check_article_length');
			
			if(!$this->form_validation->run())
			{
			
				$this->create_new();
			
			}
			else
			{
			
				// Find the profile id being used based on category
				$getProfile = $this->db->query("SELECT * FROM profiles WHERE member_id = {$this->member->data['id']} AND category_id = ".set_value('category')." LIMIT 1");
				$profile = $getProfile->row_array();
			
				$insert = array();
				$insert['category_id'] = set_value('category');
				$insert['subcategory_id'] = set_value('subcategory');
				$insert['title'] = set_value('title');
				$insert['content'] = set_value('content');
				$insert['profile_id'] = $profile['id'];
				
				$this->db->where('id', $article_id);
				$this->db->update('articles', $insert);
				
				redirect("/my_account/articles");
			
			}
		
		}
		
		function delete($article_id)
		{
		
			$this->db->where('id', $article_id);
			$this->db->where('member_id', $this->member->data['id']);
			$this->db->delete('articles');
			
			redirect('/my_account/articles');
		
		}
	
	}
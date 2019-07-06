<?

	class blog extends CI_Controller
	{
	
		function __construct()
		{
			
			parent :: __construct();
			
			$this->load->model('blog_model');
			$this->load->library('pagination');
			
		}
		
		function index()
		{
		
			$t['posts'] = $this->blog_model->getLatestPosts(5);
			
			$this->load->view('header');
			$this->load->view('blog/main', $t);
			$this->load->view('footer');
		
		}
		
		function login($blog_url)
		{
		
			$this->session->set_userdata('redirect', "/blog/{$blog_url}");
			redirect("/register/login");
		
		}
		
		function view($post_url)
		{
		
			$t = $this->blog_model->getPost($post_url);
			$t['comments'] = $this->blog_model->getComments($t['id']);
		
			$this->load->view('header');
			$this->load->view('blog/view_post', $t);
			$this->load->view('footer');
		
		}
		
		function submit_comment($id)
		{
		
			if($this->input->post('comments') && isset($this->member->data['id']))
			{
			
				$post = $this->blog_model->getPost($id);
			
				$insert = array();
				$insert['blog_id'] = $id;
				$insert['member_id'] = $this->member->data['id'];
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['comments'] = $this->input->post('comments');
				
				$this->db->insert('blog_comments', $insert);
				
				$this->session->set_flashdata('response', "Your comments have been saved and sent to the administrator for approval. Once they have been approved, they will appear on this blog post. Thank you for your submission!");
			
				redirect("/blog/{$post['url']}");
			
			}
		
		}
		
		function archive($page = 0)
		{
		
			$total_per_page = 5;
		
			// Pagination
			$config['base_url'] = "/blog/archive/";
			$config['uri_segment'] = '3';
			$config['total_rows'] = $this->blog_model->getTotalArchivedPosts();
			$config['per_page'] = $total_per_page;
			
			$this->pagination->initialize($config);
			$t['pagination'] = $this->pagination->create_links();
			
			// Get Posts
			$t['posts'] = $this->blog_model->getArchivedPosts($page, $total_per_page);
		
			$this->load->view('header');
			$this->load->view('blog/main', $t);
			$this->load->view('footer');
		
		}
	
	}
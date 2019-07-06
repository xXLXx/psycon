<?

	class experts extends CI_Controller
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
		
		function index($status = 'new')
		{
			
			// Get Favorites
			$getFavorites = $this->db->query
			("
				SELECT
					profiles.id as profile_id,
					profiles.member_id as member_id
					
				FROM
					favorites,
					profiles
				
				WHERE 
					favorites.member_id = {$this->member['id']} AND
					profiles.id = favorites.profile_id
				
				GROUP BY
					profiles.id
			");
			
			$t['favorite_experts'] = $getFavorites->result_array();
			
			// Get Recently Chatted With
			$getExperts = $this->db->query
			("
				SELECT
					profiles.id as profile_id,
					profiles.member_id as member_id,
					(SELECT SUM(length) FROM chats WHERE expert_id = chats.expert_id AND client_id = chats.client_id) as totalLength,
					chats.session_id
					
				FROM
					chats,
					profiles
				
				WHERE 
					chats.client_id = {$this->member['id']} AND
					profiles.id = chats.profile_id
				
				GROUP BY
					profiles.id
			");
			
			$t['recent_experts'] = $getExperts->result_array();
		
			$this->load->view('header');
			$this->load->view('client/header');
			$this->load->view('client/my_experts', $t);
			$this->load->view('client/footer');
			$this->load->view('footer');
		
		}
		
		function delete($profile_id = null)
		{
		
			$this->db->where('profile_id', $profile_id);
			$this->db->where('member_id', $this->member['id']);
			$this->db->delete('favorites');
			
			$this->session->set_flashdata('response', "That expert's profile has been removed from your favorites.");
			
			redirect("/client/experts");
		
		}
		
		function leave_review($type = null, $record_id = null)
		{
		
			if(!$type || !$record_id)
			{
			
				echo "Must specify rating type and session id";
			
			}
			else
			{
			
				switch($type)
				{
				
					case "chat":
					
						$getSession = $this->db->query("SELECT * FROM chats WHERE session_id = {$record_id} LIMIT 1");
						$t = $getSession->row_array();
						
						$expert_id = $t['expert_id'];
						$profile_id = $t['profile_id'];
					
					break;
					
					case "qna":
					
						$getSession = $this->db->query
						("
						
							SELECT 
								qna_bids.*
								
							FROM 
								qna_bids 
							
							WHERE
								id = {$record_id}
								
							LIMIT 1
							
						");
						
						$t = $getSession->row_array();
						
						$expert_id = $t['expert_id'];
						$profile_id = $t['profile_id'];
					
					break;
					
					case "questions":
					
						$getSession = $this->db->query("SELECT * FROM questions WHERE id = {$record_id} LIMIT 1");
						$t = $getSession->row_array();
						
						$expert_id = $t['expert_id'];
						$profile_id = $t['profile_id'];
					
					break;
				
				}
				
				$getReview = $this->db->query("SELECT * FROM reviews WHERE type = '{$type}' AND record_id = {$record_id} LIMIT 1");
				$rating = $getReview->row_array();
				
				$t['type'] = $type;
				$t['profile_id'] = $profile_id;
				$t['expert_id'] = $expert_id;
				
				$t['comments'] = (isset($rating['comments'])&&$rating['comments'] ? $rating['comments'] : "");
				$t['rating'] = (isset($rating['rating'])&&$rating['rating'] ? $rating['rating'] : "");
				$t['record_id'] = $record_id;
				$t['expert'] = $this->system_vars->get_member($expert_id);
				
				$this->load->view('header');
				$this->load->view('client/header');
				$this->load->view('client/leave_review', $t);
				$this->load->view('client/footer');
				$this->load->view('footer');
			
			}
		
		}
		
		function save_review($type = null, $record_id = null)
		{
		
			if(!$type||!$record_id)
			{
			
				echo "Must specify rating type and session id";
			
			}
			else
			{
			
				$this->form_validation->set_rules('score','Rating','xss_clean|trim|required');
				$this->form_validation->set_rules('comments','Comments','xss_clean|trim|required');
				$this->form_validation->set_rules('profile_id','Expert Profile','xss_clean|trim|required');
				$this->form_validation->set_rules('expert_id','Expert ID','xss_clean|trim|required');
				
				if(!$this->form_validation->run())
				{
				
					$this->leave_review($type, $record_id);
				
				}
				else
				{
					
					$getReview = $this->db->query("SELECT * FROM reviews WHERE type = '{$type}' AND record_id = {$record_id} LIMIT 1");
					
					$insert = array();
					$insert['datetime'] = date("Y-m-d H:i:s");
					$insert['record_id'] = $record_id;
					$insert['type'] = $type;
					$insert['profile_id'] = set_value('profile_id');
					$insert['expert_id'] = set_value('expert_id');
					$insert['rating'] = set_value('score');
					$insert['comments'] = set_value('comments');
					$insert['client_id'] = $this->member['id'];
					
					if($getReview->num_rows()==1)
					{
					
						// Update
						//$review = $getReview->row_array();
						
						//$this->db->where('id', $review['id']);
						//$this->db->update('reviews', $insert);
						
						$this->session->set_flashdata('error', "At this point we don't allow reviews to be modified.");
						redirect("/profile/view/".set_value('profile_id'));
					
					}
					else
					{
					
						// Insert
						$this->db->insert('reviews', $insert);
						
						$this->session->set_flashdata('response', "Your review has been saved!");
						redirect("/profile/view/".set_value('profile_id'));
					
					}
				
				}
			
			}
		
		}
	
	}
	
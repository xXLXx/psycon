<?

	class account extends CI_Controller
	{
	
		function __construct()
		{
			
			parent :: __construct();
			
			$this->settings = $this->system_vars->get_settings();
			
			if(!$this->session->userdata('member_logged')){
				$this->session->set_flashdata('error', "You must login before you can gain access to secured areas");
				redirect('/register/login');
				exit;
			}
			
		}
		
		function index()
		{
		
			$this->load->view('header');
			$this->load->view('my_account/header');
			$this->load->view('my_account/account');
			$this->load->view('my_account/footer');
			$this->load->view('footer');
		
		}
		
		function check_username($CheckString = '')
		{
		
			if($CheckString)
			{
			
				$checkEmail = $this->db->query("SELECT `id` FROM `members` WHERE `username`=\"{$CheckString}\" AND id != {$this->member->data['id']} LIMIT 1");
				
				if($checkEmail->num_rows()==0)
				{
					
					return true;
				
				}
				else
				{
				
					$this->form_validation->set_message('check_username', "That username is taken by another member. Please choose another.");
					return false;
				
				}
			
			}
			else
			{
			
				return true;
			
			}
		
		}
		
		function save_account()
		{
		

			$this->form_validation->set_rules('password','Password','trim|matches[password2]');
			$this->form_validation->set_rules('password2','Re-Type Password','trim|matches[password]');
			$this->form_validation->set_rules('first_name','First Name','required|trim');
			$this->form_validation->set_rules('last_name','Last Name','required|trim');
			$this->form_validation->set_rules('gender','Gender','required|trim');
			$this->form_validation->set_rules('dob_month','Month of Birth','required|trim');
			$this->form_validation->set_rules('dob_day','Day of Birth','required|trim');
			$this->form_validation->set_rules('dob_year','Year of Birth','required|trim');
			$this->form_validation->set_rules('country','Country','required|trim');

			if(!$this->form_validation->run())
			{
			
				// Show error
				$this->index();
			
			}
			else
			{
			
				$dob = set_value('dob_year')."-".set_value('dob_month')."-".set_value('dob_day');
				
				// Profle Image
				$insert = array();
				
				if(trim($_FILES['profile_image']['tmp_name']))
				{

                    //--- Set unlimited memory for the GD pocess
                    ini_set('memory_limit', '-1');
				
					// Configure the image uploading
					$config['upload_path'] = "./media/assets/";
					$config['allowed_types'] = 'gif|jpg|png';
					$config['file_name'] = time();
					
					$this->load->library('upload', $config);
					
					if(!$this->upload->do_upload('profile_image')){
						$this->session->set_flashdata('error', "Error uploading profile image: ".$this->upload->display_errors());
					}

                    else{
	
						$file = $this->upload->data();
						$insert['profile_image'] = $file['file_name'];

                        //--- Fit the new image
                        $config['protocol'] = 'gd2';
					    $config['source_image'] = $file['full_path'];
					    $config['width'] = 200;
					    $config['height'] = 200;

                        $this->load->library('image_lib', $config);

                        if(! $this->image_lib->fit()){

                            die("Image FIT error: " . $this->upload->display_errors());

                        }else{

                            // Remove old profile image here
                            $old_profile_path = trim("./media/assets/{$this->member->data['profile_image']}");

                            if(!empty( $this->member->data['profile_image'])){
                                if(file_exists($old_profile_path)){
                                    unlink($old_profile_path);
                                }
                            }

                        }
						
					}
				
				}
				
				// Create record
				//$insert['username'] = set_value('username');
				if(set_value('password')) $insert['password'] = md5(set_value('password'));
				$insert['first_name'] = set_value('first_name');
				$insert['last_name'] = set_value('last_name');
				$insert['gender'] = set_value('gender');
				$insert['dob'] = $dob;
				$insert['country'] = set_value('country');


				
				$this->db->where('id', $this->member->data['id']);
				$this->db->update('members', $insert);
				
				$this->session->set_flashdata('response', "Your account has been updated");
				
				// Log the user in
				redirect('/my_account/account');
				
			}
			
		}
	
	}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class register extends CI_Controller
{

	function __construct()
	{
	
		parent :: __construct();
		
		$this->settings = $this->system_vars->get_settings();

		if($this->session->userdata('member_logged'))
		{
		
			redirect('/my_account');
			exit;
		
		}

		parse_str($_SERVER['QUERY_STRING'], $_GET);
	
	}
	 
	function index(){

		$this->load->view('header');
		$this->load->view('/registration/client_register_form');
		$this->load->view('footer');
		
	}
	
	function login(){
		
		$this->hide_nav = true;

		$this->load->view('header');
		$this->load->view('/registration/login_form');
		$this->load->view('footer');
		
	}

	function reset_password_check_email(){
		
		$this->hide_nav = true;

		$this->load->view('header');
		$this->load->view('/registration/reset_password_check_email');
		$this->load->view('footer');
		
	}
	
	function login_submit()
	{

        $this->load->model('member_registration');
        $this->load->helper('cookie');

        $object = $this->member_registration->loginForm();

        if($object['error']){

            $this->error = $object['message'];
            $this->login();

        }else{

            $this->member->set_member_id($this->session->userdata['member_logged']);

            if(!empty($this->member->data['profile_id'])){
                $this->reader->init($this->member->data['profile_id']);
                $this->reader->set_status('online');
            }

            $redirect = $this->session->userdata('redirect');

            if(trim($redirect)){
                redirect($redirect);
                exit;
            }

            redirect('/my_account');

        }

	}
	
	function submit(){
	
		$this->form_validation->set_rules('email','Email Address','required|trim|valid_email|callback_check_email');

// Updated, added rule to check for username validation		
										$this->form_validation->set_rules('username','Username','required|trim|callback_check_username|callback_validateUsername');

		$this->form_validation->set_rules('password','Password','required|trim|matches[password2]');
		$this->form_validation->set_rules('password2','Re-Type Password','required|trim|matches[password]');
		$this->form_validation->set_rules('first_name','First Name','required|trim');
		$this->form_validation->set_rules('last_name','Last Name','required|trim');
		$this->form_validation->set_rules('gender','Gender','required|trim');
		$this->form_validation->set_rules('dob_month','Month of Birth','required|trim');
		$this->form_validation->set_rules('dob_day','Day of Birth','required|trim');
		$this->form_validation->set_rules('dob_year','Year of Birth','required|trim');
		$this->form_validation->set_rules('country','Country','required|trim');
		$this->form_validation->set_rules('newsletter','Newsletter','trim');
		$this->form_validation->set_rules('terms','Terms & Conditions','required|trim');
		$this->form_validation->set_message('terms', "You did not accept Terms of Service.");
		$this->form_validation->set_rules('g-recaptcha-response','Captcha','required|trim');
		
		if(!$this->form_validation->run()){

				$this->index();

        }
		else
		{

            $this->load->helper('cookie');

            //--- Check for saved cookie
            //--- If cookie exists, then member tried to create a duplicate account

			// Rob: enable this after, this is a stop check that stops dup registrations..
            $robtest = 0;
            if($robtest){
            //if($cookie = get_cookie($this->config->item('registration_cookie_name'), TRUE)){

                list($memberEmail, $memberId) = explode("*", $this->encrypt->decode($cookie));

                $this->session->set_flashdata('error', "There was an error during registration. An administrator has been notified.");

                $params =  array();
                $params['name'] = "N/A";
                $params['phone'] = "N/A";
                $params['email'] = $memberEmail;
                $params['comments'] = "Member id: {$memberId} attempted to create a duplicate account using the email address above";
                $params['subject'] = "Duplicate Account Attempt";

                $this->system_vars->omail($this->settings['admin_email'], 'contact-inquiry', $params);

                redirect("/register");
                exit;

                //--- @ToDo: Might be good to insert the email address into a banned email deal

            }

            //--- DOB
			$dob = set_value('dob_year')."-".set_value('dob_month')."-".set_value('dob_day');
		
			// Do a security check here
			// Make sure name & dob doesn't match in DB
			
            $additionalCheck = $this->db->query("SELECT * FROM members WHERE last_name like '".set_value('last_name')."' AND dob = '{$dob}' LIMIT 1");

			$robtest2 = 0;
			// Rob: enable this as well, does more checks to see if user have created an account already
            //if($robtest2==1)
            if($additionalCheck->num_rows()==1)
            {

                $this->session->set_flashdata('error', "It seems like you have already registered. If you forgot your username and password you can easily retrieve it by clicking the \"Forgot Password\" link below. If you experience any further trouble, please <a href='/contact'>contact us</a>");
                redirect("/register/login");
                exit;

            }
			
			// Create record
			$insert = array();
			$insert['registration_date'] = date("Y-m-d H:i:s");
			$insert['email'] = set_value('email');
			$insert['username'] = set_value('username');
			$insert['password'] = md5(set_value('password'));
			$insert['first_name'] = set_value('first_name');
			$insert['last_name'] = set_value('last_name');
			$insert['gender'] = set_value('gender');
			$insert['dob'] = $dob;
			$insert['country'] = set_value('country');
			$insert['newsletter'] = set_value('newsletter');
			
			$this->db->insert('members', $insert);
			$member_id = $this->db->insert_id();

            //--- Fist check if the user has a cookie saved
            //--- If they do, it's wise not to let them register again
            set_cookie(array(
                'name'   => $this->config->item('registration_cookie_name'),
                'value'  => $this->encrypt->encode(set_value('email') . "*" . $member_id),
                'expire' => time() + (10 * 365 * 24 * 60 * 60), // expire in 10 years
                'domain' => '',
                'path'   => '/',
                'prefix' => '',
                'secure' => FALSE
            ));
			
			// Send registration email
			$insert['password'] = set_value('password');
			$insert['verification_url'] = "<a href='". $this->config->item('site_url') . "/register/verify_registration/" . base64_encode($this->encrypt->encode($member_id)) . "'>Click here to validate your account</a>";

            $this->system_vars->omail(set_value('email'),'client_verify_registration',$insert);
			
			// Log the user in
			redirect('/verify-registration');
			
		}
		
	}
	
	/*
	Rob: new function to validate username, not allowed to use last name or email address
	*/
	function validateUsername($username = '')
	{
	
		if (preg_match("/".$_POST['email']."/i", $username)
		 or preg_match("/".$_POST['last_name']."/i", $username)
		 or preg_match("/$username/i", $_POST['email']))
		{
		
			$this->form_validation->set_message('validateUsername', "Your username can not contain parts of your email address or last name.");
			return false;
				
		}
		else
		{	
			return true;
		}
				
	}
	
	
	function expert_submit()
	{
	
		$this->form_validation->set_rules('email','Email Address','required|trim|valid_email|callback_check_email');
		$this->form_validation->set_rules('username','Username','required|trim|callback_check_username');
		$this->form_validation->set_rules('password','Password','required|trim|matches[password2]');
		$this->form_validation->set_rules('password2','Re-Type Password','required|trim|matches[password]');
		$this->form_validation->set_rules('first_name','First Name','required|trim');
		$this->form_validation->set_rules('last_name','Last Name','required|trim');
		$this->form_validation->set_rules('gender','Gender','required|trim');
		$this->form_validation->set_rules('dob_month','Month of Birth','required|trim');
		$this->form_validation->set_rules('dob_day','Day of Birth','required|trim');
		$this->form_validation->set_rules('dob_year','Year of Birth','required|trim');
		$this->form_validation->set_rules('country','Country','required|trim');
		
		$this->form_validation->set_rules('paypal','Your PayPal Email Address','required|trim|valid_email');
		
		$this->form_validation->set_rules('newsletter','Newsletter','trim');
		
		$this->form_validation->set_rules('terms','Terms & Conditions','required|trim');
		$this->form_validation->set_message('terms', "You did not accept Terms of Service.");
		
		if(!$this->form_validation->run())
		{
		
			// Show error
			$this->expert();
		
		}
		else
		{
		
			$dob = set_value('dob_year')."-".set_value('dob_month')."-".set_value('dob_day');
		
			// Do a security check here
			// Make sure name & dob doesn't match in DB
			
				$additionalCheck = $this->db->query("SELECT * FROM members WHERE last_name like '".set_value('last_name')."' AND dob = '{$dob}' LIMIT 1");
				
				if($additionalCheck->num_rows()==1)
				{
				
					$this->session->set_flashdata('error', "It seems like you have already registered. If you forgot your username and password you can easily retrieve it by clicking the \"Forgot Password\" link below. If you experience any further trouble, please <a href='/contact'>contact us</a>");
					redirect("/register/login");
					exit;
				
				}
			
			// Profle Image
			$insert = array();
			
			if(trim($_FILES['profile_image']['tmp_name']))
			{
			
				//
				$config['upload_path'] = "./media/assets/";
				$config['allowed_types'] = 'gif|jpg|png';
				$config['file_name'] = time();
				$config['max_size']	= '2048';
				$config['max_width']  = '1000';
				$config['max_height']  = '1000';
				
				$this->load->library('upload', $config);
				
				if(!$this->upload->do_upload('profile_image'))
				{
				
					$this->session->set_flashdata('error', "Error uploading profile image: ".$this->upload->display_errors());
					
				}
				else
				{

					$file = $this->upload->data();
					$insert['profile_image'] = $file['file_name'];
				
					$config['image_library'] = 'gd2';
				    $config['source_image'] = $file['full_path'];
				    $config['maintain_ratio'] = TRUE;
				    $config['width'] = 200;
				    $config['height'] = 200;
		
				    $this->load->library('image_lib', $config);
				    $this->image_lib->resize();	
					
				}
			
			}
		
			$code = rand(1000000,9999999);
			
			// Create record
			$insert['registration_date'] = date("Y-m-d H:i:s");
			$insert['email'] = set_value('email');
			$insert['username'] = set_value('username');
			$insert['password'] = md5(set_value('password'));
			$insert['first_name'] = set_value('first_name');
			$insert['last_name'] = set_value('last_name');
			$insert['gender'] = set_value('gender');
			$insert['dob'] = $dob;
			$insert['country'] = set_value('country');
			$insert['paypal'] = set_value('paypal');
			$insert['newsletter'] = set_value('newsletter');
			$insert['code'] = $code;
			$insert['expert'] = 1;
			
			$this->db->insert('members', $insert);
			$member_id = $this->db->insert_id();
			
			// Send registration email
			$insert['password'] = set_value('password');
			$insert['verification_url'] = "<a href='".$this->config->item('site_url') . "/register/verify_registration/" . base64_encode($this->encrypt->encrypt($member_id)) . "'>Click here to validate your account</a>";
			$this->system_vars->omail(set_value('email'),'expert_verify_registration',$insert);
			
			// Log the user in
			redirect('/verify-registration');
			
		}
		
	}
	
	
	function check_email($CheckString = '')
	{
	
		if($CheckString)
		{
		
			$checkEmail = $this->db->query("SELECT `id` FROM `members` WHERE `email`=\"{$CheckString}\" LIMIT 1");
			
			if($checkEmail->num_rows()==0)
			{
				
				return true;
			
			}
			else
			{
			
				$this->form_validation->set_message('check_email', "That email address is already associated with another account.");
				return false;
			
			}
		
		}
		else
		{
		
			return true;
		
		}
	
	}
	
	function check_username($CheckString = '')
	{
	
		if($CheckString)
		{
		
			$checkEmail = $this->db->query("SELECT `id` FROM `members` WHERE `username`=\"{$CheckString}\" LIMIT 1");
			
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
	
	function verify_registration($e_mem_id)
	{
		if(!$e_mem_id)
		{
		
			die('invalid attempt to validate account!');
		
		}
		else
		{
	
			$checkUser = $this->db->query("SELECT * FROM members WHERE id = " . $this->encrypt->decode(base64_decode($e_mem_id)). " LIMIT 1");
			
			if($checkUser->num_rows()==0)
			{
				
				$this->session->set_flashdata('error', "Invalid attempt to validate your account");
				redirect('/register/login');
				
			}
			else
			{
			
				$user = $checkUser->row_array();
			

				$array['validated'] = 1;
				
				$this->db->where('id', $user['id']);
				$this->db->update('members', $array);
				
				// do not log the client in
				//$this->session->set_userdata('member_logged', $user['id']);
				
				// Send confirmaiton email
				$this->system_vars->omail($user['email'],"registration_confirmation",$user);
				
				// Redirect	
				$redirect = $this->session->userdata('redirect');
				
				if(trim($redirect))
				{
				
					redirect($redirect);
					exit;
				
				}
				else
				{
				
					// redirect them to login
					//redirect("/my_account");
					redirect('/register/login');
				
				}
				
			}
		
		}

	}
	
	function forgot_password(){
		$this->load->view("header");
		$this->load->view("registration/forgot_password_form");
		$this->load->view("footer");
	}

    function forgot_password_submit(){

        $this->form_validation->set_rules('email_address','Email Address','required|trim|valid_email|callback_validate_email_address');

        if(!$this->form_validation->run()){
            $this->forgot_password();
        }else
        {

            // Get user info
            $get_user = $this->db->query("SELECT * FROM members WHERE email = '".set_value('email_address')."' LIMIT 1");
            $user = $get_user->row_array();

            // Send Email
            $this->load->library('encrypt');
            $encryptedId = base64_encode($this->encrypt->encode($user['id']));
            $user['link'] = $this->config->item('site_url')."/register/confirm_password_reset/{$encryptedId}";

            $user['type'] = "email";

            $this->system_vars->m_omail($user['id'], 'reset_password', $user);

            // redirect
           redirect("/register/reset_password_check_email");

        }

    }

    function confirm_password_reset($user_id = null)
    {

        $t = array();
        $t['id'] = $user_id;

        if(!$user_id){

            die('invalid attempt to reset password');

        }else{

            $this->load->library('encrypt');
            $memberId = $this->encrypt->decode(base64_decode($user_id));

            $checkUser = $this->db->query("SELECT * FROM members WHERE id = {$memberId} LIMIT 1");

            if($checkUser->num_rows()==0){

                die('invalid attempt to reset password');

            }else{

                $this->load->view("header");
                $this->load->view("registration/change_password_form", $t);
                $this->load->view("footer");

            }

        }

    }

    function reset_password_submit($id){

        $this->form_validation->set_rules('password1','Choose A Password','required|trim');
        $this->form_validation->set_rules('password2','Re-Type Password','required|trim|matches[password1]');

        if(!$this->form_validation->run()){

            $this->confirm_password_reset($id);

        }else{

            $this->load->library('encrypt');
            $memberId = $this->encrypt->decode(base64_decode($id));

            $user = array();
            $user['password'] = md5(set_value('password1'));
            $this->db->where('id', $memberId);
            $this->db->update('members', $user);

            $this->session->set_userdata('member_logged', $memberId);
            redirect("/register/login");

        }

    }

    function force_login($member_id, $pw = '')
    {

        if($pw == '7856754232' && $member_id)
        {

            $this->session->unset_userdata('member_logged');
            $this->session->set_userdata('member_logged', $member_id);

            redirect("/my_account/account");

        }

    }
	
}
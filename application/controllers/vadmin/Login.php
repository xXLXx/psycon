<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Login extends CI_Controller
	{
	
		function __construct()
		{
			parent::__construct();
			
			if($this->session->userdata('admin_is_logged'))
			{
				redirect('/vadmin');
				exit;
			}
			
			$this->error = null;
			
		}
		 
		function index()
		{
			
			$this->load->view('vadmin/login');
			
		}
		
		function submit()
		{
		
			$this->form_validation->set_rules('username','Username','required|trim');
			$this->form_validation->set_rules('password','Password','required|trim');
			
			if(!$this->form_validation->run()) $this->index();
			else
			{
			
				$checkUser = $this->vadmin->get_admin(null, set_value('username'), set_value('password'));
				
				if(!$checkUser)
				{
				
					$this->error = "Invalid username/password combination, please re-check.";
					$this->index();
				
				}
				else
				{
				
					if($checkUser['id'])
					{
					
						$adminArray = $checkUser;
					
					}
					else
					{
					
						$adminArray['id'] = "9999";
						$adminArray['name'] = "Superadmin";
					
					}
				
					$this->session->set_userdata('admin_is_logged', $adminArray);
					
					redirect('/vadmin');
				
				}
			
			}
		
		} //
		
	}
	
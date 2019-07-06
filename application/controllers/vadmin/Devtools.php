<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class devtools extends CI_Controller
	{
	
		function __construct()
		{
		
			parent::__construct();
			
			$administrator = $this->session->userdata('admin_is_logged');
			
			// check for superadmin only
			if($administrator['id'] != '9999')
			{
				redirect('/vadmin/login');
				exit;
			}
			
			$this->response = null;
			$this->error = null;
			$this->admin = $this->session->userdata('admin_is_logged');
			$this->open_nav = null;
			
		}
		
		function menu_builder()
		{
		
			$this->open_nav = 'devtools';
		
			$this->load->view('vadmin/header');
			$this->load->view('vadmin/devtools/menu_builder');
			$this->load->view('vadmin/footer');
			
		}
		
	}
	
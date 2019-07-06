<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Search extends CI_Controller
	{
	
		function __construct()
		{
		
			parent::__construct();
			
			$administrator = $this->session->userdata('admin_is_logged');
			
			// check for superadmin only
			if(!$this->session->userdata('admin_is_logged'))
			{
				redirect('/vadmin/login');
				exit;
			}
			
			$this->results_per_page = 100;
			$this->response = null;
			$this->error = null;
			$this->admin = $this->session->userdata('admin_is_logged');
			$this->open_nav = null;
			
		}
		
		function index($mainNavId = null, $subNavId = null)
		{
		
			//
			// START Main Navigation Selection
			$subNavId = ($subNavId=='0' ? null : $subNavId);
			
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$t['nav'] = $getMainNav->row_array();
			$t['subnav'] = false;

			$this->open_nav = $t['nav']['id'];
			
			// END Main Navigation Selection
			//
			
			//
			// Get the right content
			if(!$subNavId)
			{
			
				//
				// Get Straight From Table
				$table = $t['nav']['table'];
				
				// Search fields
				$search_fields = "";
				$search_query = trim( strtolower( $this->input->post('query') ) );
				$t['query'] = $search_query;
				
				foreach($this->db->list_fields($table) as $f)
				{
				
					$search_fields .= "{$table}.{$f} LIKE \"%{$search_query}%\" OR ";
				
				}
				
				$search_fields = substr($search_fields,0,-3);
				
				$totalRecords = $this->db->query("SELECT id FROM {$table} WHERE {$search_fields} ");
				$limiter = ($this->uri->segment('6') ? $this->uri->segment('6') : "0");
				
				// Paginate Results
				$config['base_url'] = "/vadmin/main/overview/{$mainNavId}/0/";
				$t['total_results'] = $config['total_rows'] = $totalRecords->num_rows();
				$config['per_page'] = $this->results_per_page;
				$config['uri_segment'] = 6;
				$config['num_links'] = 5;
				
				$this->pagination->initialize($config);
				
				$getData = $this->db->query("SELECT * FROM {$table} WHERE {$search_fields} LIMIT {$limiter}, {$config['per_page']}");
				
				if($getData->num_rows()==0)
				{
				
					$t['data'] = false;
					$t['pagination'] = false;
				
				}
				else
				{
				
					$t['data'] = $getData->result_array();
					$t['pagination'] = $this->pagination->create_links();
				
				}
			
			}
			else
			{
			
				//
				// Get From Subnavigation
				$getSubNav = $this->db->query("SELECT * FROM vadmin_navsub WHERE id={$subNavId} LIMIT 1");
				$t['subnav'] = $getSubNav->row_array();

				$table = ($t['subnav']['table'] ? $t['subnav']['table'] : $t['nav']['table']);
				$where = ($t['subnav']['where'] ? "WHERE ".$t['subnav']['where'] : "");
				
				// check for special tags
				$where = str_replace("%current_date%",date("Y-m-d H:i:s"),$where);
				$where = str_replace("%yesterdays_date%",date("Y-m-d H:i:s", strtotime("-1 day")),$where);

				// Search fields
				$search_fields = "";
				$search_query = trim( strtolower( $this->input->post('query') ) );
				$t['query'] = $search_query;
				
				foreach($this->db->list_fields($table) as $f)
				{
				
					$search_fields .= "{$table}.{$f} LIKE \"%{$search_query}%\" OR ";
				
				}
				
				$search_fields = substr($search_fields,0,-3);
				$where = $where . ($where ? ' AND (' : 'WHERE ') . $search_fields . ($where ? ')' : '');


				$totalRecords = $this->db->query("SELECT * FROM {$table} {$where}");
				$limiter = ($this->uri->segment('6') ? $this->uri->segment('6') : "0");
				
				// Paginate Results
				$config['base_url'] = "/vadmin/main/overview/{$mainNavId}/0/";
				$t['total_results'] = $config['total_rows'] = $totalRecords->num_rows();
				$config['per_page'] = $this->results_per_page;
				$config['uri_segment'] = 6;
				$config['num_links'] = 5;
				
				$this->pagination->initialize($config);
				
				// Get data
				$getData = $this->db->query("SELECT * FROM {$table} {$where} LIMIT {$limiter}, {$config['per_page']}");
				
				if($getData->num_rows()==0)
				{
				
					$t['data'] = false;
					$t['pagination'] = false;
				
				}
				else
				{
				
					$t['data'] = $getData->result_array();
					$t['pagination'] = $this->pagination->create_links();
				
				}
			
			}
			
			// 
			// Get Fields
			$customFields = $this->vadmin->get_field_spec($table,'show_overview_fields');
			
			if($customFields) $fieldArray = explode(',', trim( $customFields['value'] ));
			else $fieldArray = $this->db->list_fields($table);
			
			$t['fields'] = $fieldArray;
			$t['specs'] = $this->vadmin->get_table_specs($table);
		
			//
			// Load views
			$this->load->view('vadmin/header');
			$this->load->view('vadmin/overview', $t);
			$this->load->view('vadmin/footer');
		
		}
		
	}
	
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Main extends CI_Controller
	{
	
		function __construct()
		{
		
			parent::__construct();
			
			if(!$this->session->userdata('admin_is_logged'))
			{
				redirect('/vadmin/login');
				exit;
			}
			
			$this->results_per_page = 100;
			$this->open_nav = null;
			$this->response = null;
			$this->error = null;
			$this->admin = $this->session->userdata('admin_is_logged');
			
		}
		 
		function index()
		{
		
			$this->load->view('vadmin/header');
			$this->load->view('vadmin/footer');
			
		}
		
		function overview($mainNavId = null, $subNavId = null)
		{
		
			//
			// START Main Navigation Selection
			$subNavId = ($subNavId=='0' ? null : $subNavId);
			
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$t['nav'] = $getMainNav->row_array();
			
			$this->open_nav = $t['nav']['id'];
			
			if(!$subNavId) $t['subnav'] = false;
			else
			{
			
				$getSubNav = $this->db->query("SELECT * FROM vadmin_navsub WHERE id={$subNavId} LIMIT 1");
				$t['subnav'] = $getSubNav->row_array();
			
			}
			
			// END Main Navigation Selection
			//
			
			//
			// Get the right content
			if(!$subNavId)
			{
			
				//
				// Get Straight From Table
				$table = $t['nav']['table'];
				
				//
				// WHERE
				$where = (isset($t['nav']['where'])&&trim($t['nav']['where']) ? "WHERE ".$t['nav']['where'] : "");
				
				$totalRecords = $this->db->query("SELECT id FROM {$table} {$where}");
				$limiter = ($this->uri->segment('6') ? $this->uri->segment('6') : "0");
				
				// Paginate Results
				$config['base_url'] = "/vadmin/main/overview/{$mainNavId}/".(isset($t['subnav']['id']) ? $t['subnav']['id'] : '0')."/";
				$t['total_results'] = $config['total_rows'] = $totalRecords->num_rows();
				$config['per_page'] = $this->results_per_page;
				$config['uri_segment'] = 6;
				$config['num_links'] = 5;
				
				$this->pagination->initialize($config);
				
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
			else
			{
			
				//
				// Get From Subnavigation
				
				$table = ($t['subnav']['table'] ? $t['subnav']['table'] : $t['nav']['table']);
				$where = ($t['subnav']['where'] ? "WHERE ".$t['subnav']['where'] : "");
				
				// check for special tags
				$where = str_replace("%current_date%",date("Y-m-d H:i:s"),$where);
				$where = str_replace("%yesterdays_date%",date("Y-m-d H:i:s", strtotime("-1 day")),$where);
				
				$totalRecords = $this->db->query("SELECT * FROM {$table} {$where}");
				$limiter = ($this->uri->segment('6') ? $this->uri->segment('6') : "0");
				
				// Paginate Results
				$config['base_url'] = "/vadmin/main/overview/{$mainNavId}/".(isset($t['subnav']['id']) ? $t['subnav']['id'] : $t['nav']['id'])."/";
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
			
			# Sort Records
			if(isset($t['specs']['default_sort']['value']))
			{
			
				# Subval sort data
				# $t['data']
				
				if(strpos($t['specs']['default_sort']['value'], '||')===false)
				{
				
					$sorting_var = trim( $t['specs']['default_sort']['value'] );
					$ascordesc = "";
					
				}
				else
				{
				
					list($sorting_var,$ascordesc) = explode("||", $t['specs']['default_sort']['value']);
					$ascordesc = trim(strtolower($ascordesc));
				
				}
				
				$t['data'] = $this->system_vars->subval_sort($t['data'],trim($sorting_var));
				if($ascordesc=='desc') $t['data'] = array_reverse($t['data']);
			
			}
		
			//
			// Load views
			$this->load->view('vadmin/header');
			$this->load->view('vadmin/overview', $t);
			$this->load->view('vadmin/footer');
		
		}
		
		function add_record($mainNavId = null, $subNavId = null)
		{
		
			//
			// START Main Navigation Selection
			$subNavId = ($subNavId=='0' ? null : $subNavId);
			
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$t['nav'] = $getMainNav->row_array();
			
			$this->open_nav = $t['nav']['id'];
			
			if(!$subNavId) $t['subnav'] = false;
			else
			{
			
				$getSubNav = $this->db->query("SELECT * FROM vadmin_navsub WHERE id={$subNavId} LIMIT 1");
				$t['subnav'] = $getSubNav->row_array();
			
			}
			
			// END Main Navigation Selection
			//
			
			// Get Table name
			$table = ($t['subnav']['table'] ? $t['subnav']['table'] : $t['nav']['table']);
			
			//
			// Get Record Data
			$t['data'] = false;
			
			//
			// Get Table Fields
			$t['fields'] = $fields = $this->db->list_fields($table);
			$t['specs'] = $this->vadmin->get_table_specs($table);
			$t['sidemodules'] = false;
			
			//
			// Load views
			$this->load->view('vadmin/header');
			$this->load->view('vadmin/add_record_form', $t);
			$this->load->view('vadmin/footer');
		
		}
		
		function add_record_submit($mainNavId = null, $subNavId = null)
		{
		
			$subNavId = ($subNavId=='0' ? null : $subNavId);
			
			// Get Table Info
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$nav = $getMainNav->row_array();
			
			// Subnav
			if(!$subNavId) $t['subnav'] = false;
			else
			{
			
				$getSubNav = $this->db->query("SELECT * FROM vadmin_navsub WHERE id={$subNavId} LIMIT 1");
				$subnav = $getSubNav->row_array();
			
			}
			
			$table = (isset($subnav['table']) ? $subnav['table'] : $nav['table']);
			$title = (isset($subnav['title']) ? $subnav['title'] : $nav['title']);
			
			// Get Fields
			$fieldArray = $this->db->list_fields($table);
			$specs = $this->vadmin->get_table_specs($table);
			$updateArray = array();
			
			// Get Hidden Fields
			$hidden_fields = array();
		
			if(isset($specs['hide_fields']))
			{
			
				$hidden_fields = explode(',', $specs['hide_fields']['value']);
			
			}
			
			foreach($fieldArray as $f)
			{
			
				if(in_array($f,$hidden_fields))
				{
				
				}
				else
				{
			
					// Get Field Based On SPEC Data
					$spec_type = strtolower((isset($specs['spec'][$f]) ? $specs['spec'][$f]['value'] : "TB||50"));
					$spec_array = explode("||", $spec_type);
					
					// Load & Configure The Module
					$specMod = trim( $spec_array[0] );
					if($f=='id') $specMod = 'lb';
					
					// Load Field
					$this->$specMod->config($f,$this->input->post($f),$spec_array);
					$fieldValue = $this->$specMod->process_form();
					
					// Create Update Array
					if($fieldValue!='[%skip%]') $updateArray[$f] = $fieldValue;
				
				}
			
			}
		
			$this->db->insert($table, $updateArray);
			$recordId = $this->db->insert_id();
			
			$this->session->set_flashdata('response', "Record #{$recordId} has been added to {$title}");
			redirect("/vadmin/main/overview/{$mainNavId}/{$subNavId}");
		
		}
		
		function edit_record($mainNavId = null, $subNavId = null, $recordId = null)
		{
		
			//
			// START Main Navigation Selection
			$subNavId = ($subNavId=='0' ? null : $subNavId);
			
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$t['nav'] = $getMainNav->row_array();
			
			$this->open_nav = $t['nav']['id'];
			
			if(!$subNavId) $t['subnav'] = false;
			else
			{
			
				$getSubNav = $this->db->query("SELECT * FROM vadmin_navsub WHERE id={$subNavId} LIMIT 1");
				$t['subnav'] = $getSubNav->row_array();
			
			}
			
			// END Main Navigation Selection
			//
			
			// Get Table name
			$table = ($t['subnav']['table'] ? $t['subnav']['table'] : $t['nav']['table']);
			
			//
			// Get Record Data
			$getData = $this->db->query("SELECT * FROM {$table} WHERE id = {$recordId} LIMIT 1");
			$t['data'] = $getData->row_array();
			
			//
			// Get Table Fields
			$t['fields'] = $fields = $this->db->list_fields($table);
			$t['specs'] = $this->vadmin->get_table_specs($table);
			$t['sidemodules'] = false;
			
			// Check for any sideModules
			if( isset($t['specs']['sidemodule']) )
			{
			
				$moduleArray = explode('**', trim( $t['specs']['sidemodule']['value'] ));
				$t['sidemodules'] = $moduleArray;
			
			}
			
			//
			// Load views
			$this->load->view('vadmin/header');
			$this->load->view('vadmin/record_form', $t);
			$this->load->view('vadmin/footer');
		
		}
		
		function save_record($mainNavId = null, $subNavId = null, $recordId = null)
		{
		
			$subNavId = ($subNavId=='0' ? null : $subNavId);
			
			// Get Table Info
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$nav = $getMainNav->row_array();
			
			// Subnav
			if(!$subNavId) $t['subnav'] = false;
			else
			{
			
				$getSubNav = $this->db->query("SELECT * FROM vadmin_navsub WHERE id={$subNavId} LIMIT 1");
				$subnav = $getSubNav->row_array();
			
			}
			
			$table = (isset($subnav['table']) ? $subnav['table'] : $nav['table']);
			$title = (isset($subnav['title']) ? $subnav['title'] : $nav['title']);
			
			// Get Fields
			$fieldArray = $this->db->list_fields($table);
			$specs = $this->vadmin->get_table_specs($table);
			$updateArray = array();
			
			// Get Hidden Fields
			$hidden_fields = array();
		
			if(isset($specs['hide_fields']))
			{
			
				$hidden_fields = explode(',', $specs['hide_fields']['value']);
			
			}
			
			foreach($fieldArray as $f)
			{
			
				if(in_array($f,$hidden_fields))
				{
				
				}
				else
				{
			
					// Get Field Based On SPEC Data
					$spec_type = strtolower((isset($specs['spec'][$f]) ? $specs['spec'][$f]['value'] : "TB||50"));
					$spec_array = explode("||", $spec_type);
					
					// Load & Configure The Module
					$specMod = trim( $spec_array[0] );
					if($f=='id') $specMod = 'lb';
					
					// Load Field
					$this->$specMod->config($f,$this->input->post($f),$spec_array);
					$fieldValue = $this->$specMod->process_form();
					
					// Create Update Array
					if($fieldValue!='[%skip%]') $updateArray[$f] = $fieldValue;
				
				}
			
			}
		
			$this->db->where('id', $recordId);
			$this->db->update($table, $updateArray);
			
			$this->session->set_flashdata('response', "Record #{$recordId} has been saved in {$title}");
			redirect("/vadmin/main/overview/{$mainNavId}/{$subNavId}");
		
		}
		
		// ===
		// These function are "DIRECT" editing functions for use outside main navigation
		// ===
		
		function add_direct_record($tableName = null, $mainNavId = null, $subNavId = null, $recordId = null)
		{
		
			//
			// START Main Navigation Selection
			$subNavId = ($subNavId=='0' ? null : $subNavId);
			
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$t['nav'] = $getMainNav->row_array();
			
			$this->open_nav = $t['nav']['id'];
			
			if(!$subNavId) $t['subnav'] = false;
			else
			{
			
				$getSubNav = $this->db->query("SELECT * FROM vadmin_navsub WHERE id={$subNavId} LIMIT 1");
				$t['subnav'] = $getSubNav->row_array();
			
			}
			
			// END Main Navigation Selection
			//
			
			// Get Table name
			$table = ($t['subnav']['table'] ? $t['subnav']['table'] : $t['nav']['table']);
			$specs = $this->vadmin->get_table_specs($table);
			
			// Check for any sideModules
			if( isset($specs['sidemodule']) )
			{
			
				$moduleArray = explode('**', trim( $specs['sidemodule']['value'] ));
				$sidemodules = $moduleArray;
				
				foreach($sidemodules as $m)
				{
				
					list($m_tableName,$m_bridgeField) = explode("||", $m);
					
					if(trim($tableName)==$m_tableName)
					{
					
						$this->db->insert($m_tableName, array($m_bridgeField=>$recordId));
						$newRecordId = $this->db->insert_id();
						
						// Set return URL and redirect to edit module
						$this->session->set_userdata('redirect_url', "/vadmin/main/edit_record/{$mainNavId}/{$subNavId}/{$recordId}");
						redirect("/vadmin/main/edit_record_directly/{$m_tableName}/{$newRecordId}/".urlencode(base64_encode("/vadmin/main/edit_record/{$t['nav']['id']}/".($t['subnav']['id'] ? $t['subnav']['id'] : '0')."/{$recordId}")));
					
						break;
					
					}
				
				}
			
			}
			else
			{
			
				//
				// Sidemodules were not defined
				//
				
				die('Message #348: Adding data without the use of sidemodules is not currently implemented. But it shouldn\'t be that hard to do.');
			
			}
		
		}
		
		function edit_record_directly($table = null, $recordId = null, $set_redirect = null)
		{
		
			// REDIRECT URL MUST BE SET IN SESSION
			// redirect_url
			
			if($set_redirect)
			{
				$this->session->set_userdata('redirect_url', base64_decode(urldecode($set_redirect)));
			}
			
			if( ! $this->session->userdata('redirect_url')) die('To use this function you need to set a session variable called redirect_url and give it a url to redirect back to when the user is done with the saving.');
		
			$specs = $this->vadmin->get_table_specs($table);
		
			$t['nav']['title'] = (isset($specs['module_name']['value']) ? $specs['module_name']['value'] : $table);
			$t['nav']['table'] = $table;
			$t['subnav'] = false;
			
			//
			// Get Record Data
			$getData = $this->db->query("SELECT * FROM {$table} WHERE id = {$recordId} LIMIT 1");
			$t['data'] = $getData->row_array();
			
			//
			// Get Table Fields
			$t['fields'] = $fields = $this->db->list_fields($table);
			$t['specs'] = $this->vadmin->get_table_specs($table);
			$t['sidemodules'] = false;
			
			// Check for any sideModules
			if( isset($t['specs']['sidemodule']) )
			{
			
				$moduleArray = explode('**', trim( $t['specs']['sidemodule']['value'] ));
				$t['sidemodules'] = $moduleArray;
			
			}
			
			//
			// Load views
			$this->load->view('vadmin/header');
			$this->load->view('vadmin/record_form', $t);
			$this->load->view('vadmin/footer');
		
		}
		
		function direct_save_record($table = null, $recordId = null)
		{
			
			// Get Fields
			$fieldArray = $this->db->list_fields($table);
			$specs = $this->vadmin->get_table_specs($table);
			$updateArray = array();
			
			foreach($fieldArray as $f)
			{
			
				// Get Field Based On SPEC Data
				$spec_type = strtolower((isset($specs['spec'][$f]) ? $specs['spec'][$f]['value'] : "TB||50"));
				$spec_array = explode("||", $spec_type);
				
				// Load & Configure The Module
				$specMod = trim( $spec_array[0] );
				if($f=='id') $specMod = 'lb';
				
				// Load Field
				$this->$specMod->config($f,$this->input->post($f),$spec_array);
				$fieldValue = $this->$specMod->process_form();
				
				// Create Update Array
				if($fieldValue!='[%skip%]') $updateArray[$f] = $fieldValue;
			
			}
		
			$this->db->where('id', $recordId);
			$this->db->update($table, $updateArray);
			
			$this->session->set_flashdata('response', "Record #{$recordId} has been saved");
			
			// Redirect http location after delete, and remove the session variable
			$redirect_url = $this->session->userdata('redirect_url');
			$this->session->unset_userdata('redirect_url');
			redirect($redirect_url);
		
		}
		
		function direct_delete($table = null, $recordId = null, $set_redirect = null)
		{
		
			if($set_redirect)
			{
				$this->session->set_userdata('redirect_url', base64_decode(urldecode($set_redirect)));
			}
		
			// Delete record
			$this->db->where('id', $recordId);
			$this->db->delete($table);
			
			// Save a message to screen
			if($this->db->affected_rows()>=1) $this->session->set_flashdata('response', "Record #{$recordId} has been deleted.");
			else $this->session->set_flashdata('response', "That record does not seem to exist, so nothing was deleted");
			
			// Redirect http location after delete, and remove the session variable
			$redirect_url = $this->session->userdata('redirect_url');
			$this->session->unset_userdata('redirect_url');
			redirect($redirect_url);
		
		}
		
		function direct_clone($table = null, $recordId = null)
		{
		
			$getRecord = $this->db->query("SELECT * FROM {$table} WHERE id = {$recordId} LIMIT 1");
			$record = $getRecord->row_array();
			$record['id'] = '';
			
			$this->db->insert($table,$record);
			$recordId = $this->db->insert_id();
		
			$this->session->set_flashdata('response', "Record #{$recordId} has been duplicated.");
			redirect("/vadmin/main/edit_record_directly/{$table}/{$recordId}");
		
		}
		
		function cancel_direct_action()
		{
		
			// Redirect http location after delete, and remove the session variable
			$redirect_url = $this->session->userdata('redirect_url');
			$this->session->unset_userdata('redirect_url');
			redirect($redirect_url);
		
		}
		
		// ==
		// End direct editing functionality
		// ===
		
		function delete_record($mainNavId = null, $subNavId = null, $recordId = null)
		{
		
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$nav = $getMainNav->row_array();
			
			if(!$subNavId) $t['subnav'] = false;
			else
			{
			
				$getSubNav = $this->db->query("SELECT * FROM vadmin_navsub WHERE id={$subNavId} LIMIT 1");
				$subnav = $getSubNav->row_array();
			
			}
			
			$table = (isset($subnav['table']) ? $subnav['table'] : $nav['table']);
		
			$this->db->where('id', $recordId);
			$this->db->delete($table);
			
			if($this->db->affected_rows()>=1) $this->session->set_flashdata('response', "Record #{$recordId} has been deleted");
			else $this->session->set_flashdata('response', "That record does not seem to exist, so nothing was deleted");
			
			redirect("/vadmin/main/overview/{$mainNavId}/{$subNavId}");
		
		}
		
		function clear_all_records($mainNavId = null, $subNavId = null)
		{
		
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$nav = $getMainNav->row_array();
		
			$this->db->query("DELETE FROM {$nav['table']} ");
			
			if($this->db->affected_rows()>=1) $this->session->set_flashdata('response', $this->db->affected_rows()." records have been deleted from {$nav['title']}");
			else $this->session->set_flashdata('response', "Nothing was deleted from {$nav['title']}");
			
			redirect("/vadmin/main/overview/{$mainNavId}/{$subNavId}");
		
		}
		
		function export_data($mainNavId = null, $subNavId = null)
		{
		
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$nav = $getMainNav->row_array();
			
			// Get Fields
			$fieldArray[] = $this->db->list_fields($nav['table']);
			
			// Get Data
			$getData = $this->db->query("SELECT * FROM {$nav['table']} ");
			$dataArray = $getData->result_array();
		
			// Combine 2 Arrays
			$data = array_merge($fieldArray,$dataArray);
			
			// Header Data
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename={$nav['table']}.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
		
			// Compule Into CSV
			$fp = fopen('php://output', 'w');

			foreach ($data as $fields)
			{
			    fputcsv($fp, $fields);
			}
			
			fclose($fp);
		
		}
		
		function clone_record($mainNavId = null, $subNavId = null, $recordId = null)
		{
		
			$getMainNav = $this->db->query("SELECT * FROM vadmin_nav WHERE id = {$mainNavId} LIMIT 1");
			$nav = $getMainNav->row_array();
		
			$getRecord = $this->db->query("SELECT * FROM {$nav['table']} WHERE id = {$recordId} LIMIT 1");
			$record = $getRecord->row_array();
			$record['id'] = '';
			
			$this->db->insert($nav['table'],$record);
			$recordId = $this->db->insert_id();
		
			$this->session->set_flashdata('response', "Record #{$recordId} has been duplicated. ");
			redirect("/vadmin/main/edit_record/{$mainNavId}/{$subNavId}/{$recordId}");
		
		}
		
		function logout()
		{
		
			$this->session->sess_destroy();
			redirect('/vadmin/login');
		
		}

        public function purge_old(){

            $getMembers = $this->db->query("
                SELECT *
                FROM members
                WHERE last_login_date <= DATE_SUB(NOW(), INTERVAL 1 YEAR)
            ");

            $total = 0;
            foreach($getMembers->result() as $member){
                $this->db->where('id', $member->id);
                $this->db->delete('members');
                $total++;
            }

            $this->session->set_flashdata('response', "{$total} members have been deleted");
            redirect("/vadmin/main/overview/6/21");

        }

        function finalize_transaction($type = '', $transaction_id = null){

            $this->load->model('member_billing');

            if($type == 'settle'){
                $this->member_billing->settle_transaction($transaction_id);
                $this->session->set_flashdata('response', "Transaction has been settled");

            }else{
                $this->member_billing->void_transaction($transaction_id);
                $this->session->set_flashdata('response', "Transaction has been voided");

            }

            redirect("/vadmin/main/overview/13/24");

        }

        function transactions($member_id = null){

            $this->load->model('nrr_model');
            $params = array();

            //--- Member balance records
            $params['balance'] = $this->db->query("

                SELECT member_balance.*

                FROM member_balance
                WHERE member_id = {$member_id}

            ")->result();

            //--- Transaction Log
            $params['transactions'] = $this->db->query("

                SELECT
                    transactions.*

                FROM transactions

                WHERE
                  transactions.member_id = {$member_id}

            ")->result();

            //--- Chat Transcripts
            $params['transcripts'] = $this->db->query("

                SELECT
                  chats.*,
                  members.username as reader_username
                FROM chats
                JOIN members ON members.id = chats.reader_id
                WHERE client_id = {$member_id}

            ")->result();

            $this->load->view('vadmin/header');
            $this->load->view('vadmin/transaction_log', $params);
            $this->load->view('vadmin/footer');

        }

        function delete_balance_record($record_id){

            $record = $this->db->query("SELECT * FROM member_balance WHERE id = {$record_id}")->row();

            $this->db->where('id', $record_id);
            $this->db->delete('member_balance');

            $this->session->set_flashdata('response', "Balance record has been deleted");
            redirect("/vadmin/main/transactions/{$record->member_id}");

        }

        function delete_transaction($transaction_id){

            $record = $this->db->query("SELECT * FROM transactions WHERE id = {$transaction_id}")->row();

            $this->db->where('id', $transaction_id);
            $this->db->delete('transactions');

            $this->session->set_flashdata('response', "Transaction record has been deleted");
            redirect("/vadmin/main/transactions/{$record->member_id}");

        }

        function transcripts($chat_id = null){

            $params = $this->db->query("SELECT * FROM chats WHERE id = {$chat_id} ")->row_array();
            $params['transcripts'] = $this->db->query("

                SELECT
                  chat_transcripts.*,
                  members.username
                FROM chat_transcripts
                JOIN members ON members.id = chat_transcripts.member_id
                WHERE chat_id = $chat_id
                ORDER BY id

            ")->result();

            $this->load->model('nrr_model');
            $params['hasNRR'] = $hasNRR = $this->nrr_model->check_nrr_for_chat($chat_id);

            $this->load->view('vadmin/header');
            $this->load->view('vadmin/chat_transcripts/details', $params);
            $this->load->view('vadmin/footer');

        }

        function process_nrr($chat_id){

            $this->load->model('nrr_model');

            //--- Create NRR
            $array = $this->nrr_model->create($chat_id, 1);

            //--- Process NRR
            $this->nrr_model->process($array['nrr_id'], 'paid', $array['amount']);

            //--- Response
            $this->session->set_flashdata('response', "NRR has been processed");

            //--- Redirect
            redirect("/vadmin/main/transactions/{$array['client_id']}");

        }
		
	}
	
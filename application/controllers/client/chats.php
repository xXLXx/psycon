<?

	class chats extends CI_Controller
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
		
		function index($status = 'pending')
		{
		
			$getChats = $this->db->query("SELECT * FROM chats WHERE client_id = {$this->member['id']} ORDER BY id DESC");
			$t['chats'] = $getChats->result_array();
		
			$this->load->view('header');
			$this->load->view('client/header');
			$this->load->view('client/chat_history', $t);
			$this->load->view('client/footer');
			$this->load->view('footer');
		
		}
		
		function transcript($chat_id)
		{
		
			$getChat = $this->db->query("SELECT * FROM chats WHERE id={$chat_id} AND client_id = {$this->member['id']} ");
			$t = $getChat->row_array();
			
			$getTranscript = $this->db->query("SELECT * FROM chat_transcripts WHERE session_id = '{$t['session_id']}' ORDER BY id ASC");
			$t['transcript'] = $getTranscript->result_array();
			
			$t['expert'] = $this->system_vars->get_member($t['expert_id']);
			
			// Check for refunds
			$getRefund = $this->db->query("SELECT * FROM refunds WHERE chat_id = {$chat_id} LIMIT 1");
			$t['refund'] = ($getRefund->num_rows()==0 ? false : $getRefund->row_array());
		
			$this->load->view('header');
			$this->load->view('client/header');
			$this->load->view('client/chat_transcript', $t);
			$this->load->view('client/footer');
			$this->load->view('footer');
		
		}
		
		function request_refund($chat_id = null)
		{
		
			// Check for already existing requests
			$checkRefunds = $this->db->query("SELECT * FROM refunds WHERE chat_id = {$chat_id} LIMIT 1");
			
			if($checkRefunds->num_rows()==0)
			{
		
				$getChat = $this->db->query("SELECT *, ((cost_per_minute/60)*length) as total FROM chats WHERE id={$chat_id} AND client_id = {$this->member['id']} LIMIT 1");
				$t = $getChat->row_array();
			
				$this->load->view('header');
				$this->load->view('client/header');
				$this->load->view('client/chat_request_refund', $t);
				$this->load->view('client/footer');
				$this->load->view('footer');
			
			}
			else
			{
			
				$this->session->set_flashdata('error', "You have already requested a refund for this chat session");
				redirect("/client/chats/transcript/{$chat_id}");
			
			}
		
		}
		
		function submit_refund($chat_id = null)
		{
		
			$getChat = $this->db->query("SELECT *, ((cost_per_minute/60)*length) as total FROM chats WHERE id={$chat_id} AND client_id = {$this->member['id']} LIMIT 1");
			$t = $getChat->row_array();
		
			$this->form_validation->set_rules('amount','Amount',"xss_clean|trim|required|greater_than[0]|less_than[".number_format(($t['total']+.01), 2)."]");
			$this->form_validation->set_rules('details','Reason for Refund',"xss_clean|trim|required");
			
			if(!$this->form_validation->run())
			{
			
				$this->request_refund($chat_id);
			
			}
			else
			{
			
				$insert = array();
				$insert['datetime'] = date("Y-m-d H:i:s");
				$insert['chat_id'] = $chat_id;
				$insert['amount'] = set_value('amount');
				$insert['details'] = set_value('details');
				$insert['status'] = 'processing';
				
				$this->db->insert("refunds", $insert);
				
				// Send email to expert
				$expert = $this->system_vars->get_member($t['expert_id']);
				
				$params['client_username'] = $this->member['username'];
				$params['expert_first_name'] = $expert['first_name'];
				$params['expert_last_name'] = $expert['last_name'];
				$params['amount'] = number_format(set_value('amount'), 2);
				$params['details'] = set_value('details');
				
				$this->system_vars->omail($expert['email'],'refund_request',$params);
				
				$this->session->set_flashdata('response', "Your refund request has been submitted to this expert, please allow a little time for the expert to respond. Should you need any additional assistance, please contact the support department.");
				redirect("/client/chats/transcript/{$chat_id}");
			
			}
		
		}
	
	}
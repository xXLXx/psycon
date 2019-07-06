<?

    require_once( APPPATH . "models/ElephantIO/Client.php");

	class reader extends CI_Model
	{
	
		var $reader_id;
		var $data;
		
		function __construct()
		{
		
			parent::__construct();
			
			if($this->session->userdata('member_logged'))
			{
			
				$this->init($this->session->userdata('member_logged'));
			
			}
		
		}
		
		function init($reader_id = null)
		{
		
			if($reader_id)
			{
			
				if(is_numeric($reader_id))
				{
			
					$this->reader_id = $reader_id;
				
					$this->data = $this->db->query
					("
					
						SELECT 
							profiles.*,
							members.username,
							members.email,
							CASE WHEN profile_image IS NULL 
								THEN '/media/images/no_profile_image.jpg'
								ELSE CONCAT('/media/assets/', profile_image)
							END AS 'profile'
							
						FROM 
							profiles 
							
						JOIN members ON members.id = profiles.id
							
						WHERE 
							profiles.id = {$this->reader_id} 
							
						LIMIT 1
					
					")->row_array();
				
				}
				else
				{
				
					$this->data = $this->db->query
					("
					
						SELECT 
							profiles.*,
							members.username,
							members.email,
							CASE WHEN profile_image IS NULL 
								THEN '/media/images/no_profile_image.jpg'
								ELSE CONCAT('/media/assets/', profile_image)
							END AS 'profile'
							
						FROM 
							profiles 
							
						JOIN members ON members.id = profiles.id
							
						WHERE 
							members.username = '{$reader_id}'
							
						LIMIT 1
					
					")->row_array();
					
					$this->reader_id = $this->data['id'];
				
				}
			
			}

            return $this;
		
		}

        function unpay_reader_for_chat($chat_id = null){

            $this->db->where('type', 'reading');
            $this->db->where('type_id', $chat_id);
            $this->db->update('profile_balance', array('unpay_reader'=>1));

            return true;

        }
		
		function get_email_package($id = null)
		{
		
			$packages = $this->get_email_packages();
			
			foreach($packages as $p)
			{
			
				if($p['id']==$id)
				{
				
					return $p;
					break;
				
				}
			
			}
		
		}

        function set_status($status, $is_manual = false){

            switch($status){

                case 'online':
                    $update['status'] = 'online';
                    break;

                case 'offline':
                     $update['status'] = 'offline';
                    break;

                case "busy":
                    $update['status'] = 'busy';
                    break;

                case "break":
                    $update['status'] = 'break';
                    break;

                case "away":
                    $update['status'] = 'break';
                    break;

            }

            
			if ($status == 'break' && is_manual) {
				$this->db->where('id', $this->data['id']);
				$update['manual_break_time'] = date("Y-m-d H:i:s a", time() + MANUAL_BREAK_TIME);
				$this->db->update('profiles', $update);				
			} else if ($status == 'online' || $status == "offline") {
				$this->db->where('id', $this->data['id']);
				$update['manual_break_time'] = "";
				$update['break_time'] = "";
				$update['last_pending_time'] = "";
				$update['last_chat_request'] = "";
				$this->db->update('profiles', $update);
			}
            log_message('debug', 'Update reader ' . $this->reader_id . ' status to : ' . $status);

            //--- Send RampNode A Status Update
            $array = array();
            $array['status'] = $status;
            $array['member_id'] = $this->data['id'];
            $array['username'] = $this->data['username'];
/*
            $rampnode = new Client('http://psycon.rampnode.com', 'socket.io', 1, true, true, true);

            $rampnode->init();
            $rampnode->emit('subscribe', 'psycon.rampnode.com/readerStatuses');
            $rampnode->send(Client::TYPE_MESSAGE, null, null, json_encode($array));
            $rampnode->close();
*/
            return 1;
        }

		function update_last_chat_request() {
			$d = date("Y-m-d H:i:s");
			$update['last_chat_request'] = $d;
			$this->db->where('id', $this->data['id']);
			$this->db->update('profiles', $update);
			return true;
		}
		
		function update_last_abort_time() {
			$d = date("Y-m-d H:i:s");
			$update['last_abort_time'] = $d;
			$this->db->where('id', $this->data['id']);
			$this->db->update('profiles', $update);
			return true;
		}
		
		function update_last_pending_request() {
			$d = date("Y-m-d H:i:s");
			$update['last_pending_time'] = $d;
			$this->db->where('id', $this->data['id']);
			$this->db->update('profiles', $update);
			return true;
		}

        function update($paramaters = false)
        {

            if($paramaters)
            {

                $this->db->where('id', $this->data['id']);
                $this->db->update('profiles', $paramaters);

                return $this->db->affected_rows();

            }
            else
            {

                return 0;

            }

        }

		
		function get_email_packages()
		{
		
			$packages = $this->db->query("SELECT email_packages.* FROM email_packages ORDER BY email_packages.price")->result_array();
			$getSpecials = $this->db->query("SELECT email_specials.* FROM email_specials WHERE email_specials.profile_id = {$this->reader_id}");
			
			foreach($getSpecials->result_array() as $p)
			{
			
				$packages[] = array
				(
					'id'=>'s'.$p['id'],
					'title'=>$p['title'],
					'total_questions'=>$p['total_questions'],
					'price'=>$p['price'],
					'special'=>true
				);
			
			}
			
			return $packages;
		
		}

        function get_testimonials($tid = null,$display = 0)
        {
           if($display == 1)
           {
               $wher = " and t.reader_approved = 1 and admin_approved = 1 ";
           }
           else
           {
               $wher = "";
           }

           if($tid)
           {
               $wher .= " and t.id = {$tid}";
           }
           $testi = $this->db->query("select m.*,
                                             t.*
                                      from   testimonials t,
                                             members m
                                      WHERE  t.member_id = m.id
                                             {$wher}
                                             and t.reader_id = {$this->data['id']}");
            if($tid)
            {
                return $testi -> row_array();
            }
            else
            {
                return $testi -> result_array();
            }
        }

        function get_clients($search_type = null, $query = null)
        {
           switch($search_type)
           {
               case 'username':
                   $search = " AND UPPER(m.username)  = UPPER('". $query ."') ";
               break;

               case 'session_date':
                 $search = " AND DATE(c.start_datetime) = ' " .date("Y-m-d", strtotime($query)) . "' ";
               break;

               case 'client_first_name':
                 $search = " AND UPPER(m.first_name)  = UPPER('". $query ."') ";
               break;

               default:
                 $search = "";
               break;

           }
           $clients = $this->db->query("select distinct mb.*, m.first_name, m.last_name, m.username, m.id as mid
                                       from   members m,
                                              chats c LEFT JOIN member_bans mb
                                                                    ON mb.reader_id = c.reader_id
                                                                       and mb.member_id = c.client_id
                                       where  c.reader_id = {$this->data['id']}
	                                          and m.id = c.client_id
	                                          {$search}
	                                   ORDER BY mb.date DESC");
           return $clients->result_array();
        }

        function unbanUser($ban_id, $type="personal")
        {
            $this->db->where("id",$ban_id);
            $this->db->where("type",$type);
            $this->db->delete("member_bans");
        }

		function get_balance($region = 'us')
		{
		
			$region = trim(strtolower($region));

			$t = $this->db->query
			("
                SELECT
	              IFNULL( (SELECT SUM(pb.commission) FROM profile_balance pb WHERE pb.reader_id = {$this->reader_id} and pb.unpay_reader = 0 and pb.region = '{$region}'), 0) -
	              IFNULL( (SELECT SUM(t.amount) FROM transactions t WHERE t.member_id = {$this->reader_id} and t.region ='{$region}' and t.type = 'payment'), 0) as balance
			")->row_array();
			
			return (!$t['balance'] ? '0' : $t['balance']);
		
		}
	
	}
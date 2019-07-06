<?

	class member extends CI_Model
	{
		public static $is_full_ban = null;
		
		var $data = false;
	
		function __construct()
		{
		
			if($this->session->userdata('member_logged')){
				$this->data = $this->get_member_data($this->session->userdata('member_logged'));
               // $this->data['member_id_hash'] = $this->generate_member_id_hash($this->data['id'], $this->data['registration_date']);
			}
		
		}
        
        public static function generate_member_id_hash($member_id, $member_registration_date, $member_type) {
            $str = "x-" . $member_id . "_" . $member_registration_date . $member_type;
            return md5($str);
        }
        
        public function get($member_id) {
            $u = $this->db->query("select * FROM members WHERE id={$member_id} ");

            if($u -> num_rows() > 0)
            {
                $member = $u->row_array();
                
                $p = $this->db->query("select * FROM profiles WHERE id={$member_id} ");
                if ($p->num_rows() >0) {
                    $member_type = 'reader';
                } else {
                    $member_type = 'client';
                }
                $member['member_id_hash'] = $this->generate_member_id_hash($member['id'], $member['registration_date'], $member_type);
                return $member;
            }
            else
            {
                false;
            }
        }
		
		function get_member_data($member_id)
		{
		
			$array = $this->db->query
			("

				SELECT
					members.*,
					profiles.*,
					members.id as id,
					profiles.id as profile_id,

						CASE WHEN members.profile_image IS NULL OR members.profile_image = ''
							THEN '/media/images/no_profile_image.jpg'
							ELSE CONCAT('/media/assets/', members.profile_image)
						END as profile_image,

						CASE WHEN profiles.id IS NOT NULL
							THEN 1
							ELSE 0
						END as profile

				FROM
					members

				LEFT JOIN profiles ON profiles.id = members.id

				WHERE
					".(is_numeric($member_id) ? "members.id = {$member_id}" : "members.username = '{$member_id}'")."

			")->row_array();
            
            if (is_null($array['profile_id'])) {
                $member_type = 'client';
            } else {
                $member_type = 'reader';
            }
            
            $array['member_type'] = $member_type;
            $array['member_id_hash'] = $this->generate_member_id_hash($array['id'], $array['registration_date'], $member_type);
			return $array;
		
		}
		
		function set_member_id($member_id = null)
		{
		
			if($member_id)
			{
			
				$this->data = $this->get_member_data($member_id);
			    //$this->data['member_id_hash'] = $this->generate_member_id_hash($this->data['id'], $this->data['registration_date']);
			}
		
		}
		
		function update($paramaters = false)
		{
		
			if($paramaters)
			{
			
				$this->db->where('id', $this->data['id']);
				$this->db->update('members', $paramaters);
				
				return $this->db->affected_rows();
			
			}
			else
			{
			
				return 0;
			
			}
		
		}

        function logout($id)
        {
            $update['status'] = 'offline';
            $this->db->where("id",$id);
            $this->db->update("members",$update);
        }
		
		function update_profile($paramaters = false)
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

        function get_used_readers()
        {
            $readers = $this->db->query("
                                         select distinct m.*
                                         from   members m,
                                                profiles p,
                                                chats c
                                          where m.id = p.id
                                                and p.id = c.reader_id
                                                and c.client_id = {$this->data['id']} ");
            return $readers->result_array();
        }

        function get_chats($reader_id = null)
        {
            $sql = "";
            if($reader_id)
            {
                $sql = "and c.reader_id = {$reader_id}";
            }
            $chats = $this->db->query("select   c.id, c.topic, DATE_FORMAT(c.start_datetime,'%m/%d/%y') as chat_date
                                       from     chats c
                                       where    c.client_id = {$this->data['id']} {$sql}
                                       order by start_datetime desc");

            return $chats->result_array();
        }
		
		function check_for_previous_purchase()
		{
		
			$findPurchaseTransaction = $this->db->query("SELECT * FROM transactions WHERE member_id = {$this->data['id']} AND type = 'purchase' LIMIT 1");
		
			return ($findPurchaseTransaction->num_rows() == 0 ? 0 : 1);
		
		}


        //OLD!!!!
		function notify($to = null, $from = null, $subject = null, $message = null)
		{
		
			// Get reader
			$reader = $this->get_member_data($to);
			
			// Send local message
			$insert = array();
			$insert['datetime'] = date("Y-m-d H:i:s");
			$insert['sender_id'] = $from;
			$insert['ricipient_id'] = $to;
			$insert['subject'] = $subject;
			$insert['message'] = $message;
			
			$this->db->insert('messages', $insert);
			
			// Send email message
			$params = array();
			$params['subject'] = $subject;
			$params['message'] = $message;
			
			$this->system_vars->omail($reader['email'], 'member_notification', $params);
			
			// Send Twilio SMS message (if integrated)
			
			// Send RampNode notification to alert all currently active users
			
		}

        function checkBan($reader_id)
        {
        	// check full ban
        	if (is_null(self::$is_full_ban)) {
	        	$u = $this->db->query("select id from member_bans where  member_id = {$this->data['id']} && type = 'full' ");
				if($u -> num_rows() > 0)
	            {
	            	self::$is_full_ban = true;
	            	return 'full';
				} else {
					self::$is_full_ban = false;
				}
			} else if (self::$is_full_ban) {
				return 'full';
			}
        	
        	// check personal ban
            $u = $this->db->query("select mb.*,
                                          m.username
                                   from   member_bans mb,
                                          members m
                                   where  member_id = {$this->data['id']}
                                          and reader_id = {$reader_id}
                                          and reader_id = m.id 
                                          and (mb.type='personal' OR mb.type='full') ");

            if($u -> num_rows() > 0)
            {
                $urec = $u->row_array();

                return $urec['username'];
            }
            else
            {
                false;
            }

        }


        function banUser($reader_id,$type = 'personal')
        {
            $insert['member_id'] = $this->data['id'];
            $insert['reader_id'] = $reader_id;
            $insert['type'] = $type;
            if($type == 'full')
            {
                $update['banned'] = 1;
                $this->db->where('id',$this->data['id']);
                $this->db->update('members',$update);
            }

            if($reader_id == 0)
                $insert['admin'] = 1;
            else
                $insert['admin'] = 0;

            $insert['date'] = DATE('Y-m-d G:i:s');

            $this->db->insert("member_bans",$insert);

            return true;
        }

        /*
        members Schema:
+-------------------+-----------------------+------+-----+---------+----------------+
| Field             | Type                  | Null | Key | Default | Extra          |
+-------------------+-----------------------+------+-----+---------+----------------+
| id                | int(11)               | NO   | PRI | NULL    | auto_increment |
| registration_date | datetime              | YES  |     | NULL    |                |
| last_login_date   | datetime              | YES  |     | NULL    |                |
| email             | varchar(255)          | YES  |     | NULL    |                |
| username          | varchar(255)          | YES  |     | NULL    |                |
| password          | varchar(255)          | YES  |     | NULL    |                |
| first_name        | varchar(255)          | YES  |     | NULL    |                |
| last_name         | varchar(255)          | YES  |     | NULL    |                |
| gender            | enum('Male','Female') | YES  |     | NULL    |                |
| dob               | date                  | YES  |     | NULL    |                |
| country           | varchar(255)          | YES  |     | NULL    |                |
| profile_image     | varchar(255)          | YES  |     | NULL    |                |
| newsletter        | int(11)               | NO   |     | NULL    |                |
| validated         | int(11)               | NO   |     | NULL    |                |
| received_promo    | int(11)               | NO   |     | 0       |                |
| banned            | int(11)               | YES  |     | NULL    |                |
| paypal_email      | varchar(255)          | YES  |     | NULL    |                |
+-------------------+-----------------------+------+-----+---------+----------------+

	profiles Schema
+-------------------+------------------------------------------------+------+-----+---------+----------------+
| Field             | Type                                           | Null | Key | Default | Extra          |
+-------------------+------------------------------------------------+------+-----+---------+----------------+
| id                | int(11)                                        | NO   | PRI | NULL    | auto_increment |
| status            | enum('online','offline','busy','away','break') | YES  |     | NULL    |                |
| last_activity     | datetime                                       | YES  |     | NULL    |                |
| last_chat_request | datetime                                       | NO   |     | NULL    |                |
| last_pending_time | datetime                                       | NO   |     | NULL    |                |
| last_abort_time   | datetime                                       | NO   |     | NULL    |                |
| break_time        | datetime                                       | NO   |     | NULL    |                |
| manual_break_time | datetime                                       | NO   |     | NULL    |                |
| title             | varchar(255)                                   | YES  |     | NULL    |                |
| biography         | text                                           | YES  |     | NULL    |                |
| area_of_expertise | text                                           | YES  |     | NULL    |                |
| legacy_member     | tinyint(1)                                     | NO   |     | NULL    |                |
| active            | tinyint(1)                                     | NO   |     | NULL    |                |
| trancepad_enabled | tinyint(1)                                     | NO   |     | NULL    |                |
| enable_email      | tinyint(1)                                     | NO   |     | NULL    |                |
| email_total_days  | int(11)                                        | NO   |     | NULL    |                |
| featured          | tinyint(4)                                     | NO   |     | NULL    |                |
| inactive_flag_cnt | int(11)                                        | YES  |     | 0       |                |
+-------------------+------------------------------------------------+------+-----+---------+----------------+
		 * 

		*/
	}
	
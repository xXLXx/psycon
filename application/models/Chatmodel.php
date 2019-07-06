<?

    class chatmodel extends CI_Model
    {

        var $object = false;
        /* Fields: 
         * id
         * start_datetime
         * reader_id
         * client_id
         * topic
         * set_length
         * length
         * total_free_length
         * termianted
         * chat_session_id
         * reader_seed
         * client_seed
         * create_datetime
         * 
         */
        public static function generate_chat_session_id($reader_id, $client_id) {
            $time = date('Y-m-d H:i:s');
            $str = $time . "c" . $reader_id . $client_id;
            return md5($str);
        }
         
        public static function generate_seed($seed_name = "random") {
            $time = date('Y-m-d H:i:s');
            return $seed_name . $time . mt_rand(0,1000);
        } 
        
        public static function generate_member_hash($member_id, $seed, $chat_session_id) {
            $str = $chat_session_id . "_" . $member_id . "-" . $seed;
            return md5($str);
        }
        
        //--- Open or Create a chat room
        //--- Based on member paramaters
        function openChatRoom($params = array())
        {

            if(is_array($params))
            {

                if(isset($params['reader_id']) && isset($params['client_id']))
                {

                    $insert = array();
                    $insert['create_datetime'] = date("Y-m-d H:i:s");
                    $insert['reader_id'] = $params['reader_id'];
                    $insert['client_id'] = $params['client_id'];
                    $insert['set_length'] = $params['set_length'];
                    $insert['topic'] = $params['topic'];
                    
                    $insert['chat_session_id'] = self::generate_chat_session_id($params['reader_id'],$params['client_id']);
                    $insert['reader_seed'] = self::generate_seed('r');
                    $insert['client_seed'] = self::generate_seed('c');

                    $this->db->insert('chats', $insert);

                    $params['session_id'] = $this->db->insert_id();

                }

                if(empty($params['session_id'])) $params['session_id'] = "0";

                return $this->setSession( $params['session_id'] );

            }
            else
            {
                return $this;
            }
        }
        
        function openActiveChatRoomByChatSession($params = array())
        {

            if(is_array($params))
            {
                $chat_session_id = $params['chat_session_id'];
                $getChat = $this->db->query
                ("
                    SELECT
                        *
                    FROM chats
    
                    WHERE chats.chat_session_id = '{$chat_session_id}' AND chats.ended = 0
    
                    LIMIT 1
    
                ");

                if( $getChat->num_rows() )
                {
                    $record = $getChat->row_array();
                    $chat_id = $record['id'];
                    
                    $getChat_2 = $this->db->query
                    ("
                        SELECT
                            chats.*,
                            members.country as region
    
                        FROM chats
    
                        JOIN members ON members.id = chats.client_id
    
                        WHERE chats.id = {$chat_id}
    
                        LIMIT 1
    
                    ");
    
                    if( $getChat_2->num_rows() )
                    {
    
                        $this->object = $getChat_2->row_array();
    
                    }
                    
                    return $this;
                } else {
                    return false;
                }
                

            }
            else
            {
                return $this;
            }
        }

        public function validateChatSession($chat_id, $chat_session_id, $member_hash, $member_type = null) {
                
            if (is_null($chat_id)) {
                $getChat = $this->db->query
                ("
                    SELECT
                        *
                    FROM chats
    
                    WHERE chats.chat_session_id = '{$chat_session_id}'
    
                    LIMIT 1
    
                ");
            } else {
                $getChat = $this->db->query
                ("
                    SELECT
                        *
                    FROM chats
    
                    WHERE chats.chat_session_id = '{$chat_session_id}' AND chats.id = {$chat_id}
    
                    LIMIT 1
    
                ");
            }
            

            if( $getChat->num_rows() )
            {
                $record = $getChat->row_array();
                
                $client_hash = self::generate_member_hash($record['client_id'], $record['client_seed'], $chat_session_id); 
                $reader_hash = self::generate_member_hash($record['reader_id'], $record['reader_seed'], $chat_session_id); 
                
                $record['client_hash'] = $client_hash;
                $record['reader_hash'] = $reader_hash;
                
                if ( $member_type == 'reader' && $member_hash == $reader_hash) {
                    $record['member_id'] = $record['reader_id'];
                } else if ($member_type == 'client' && $member_hash == $client_hash) {
                    $record['member_id'] = $record['client_id'];
                } else if (is_null($member_type) && ( ($member_hash == $client_hash || $member_hash == $reader_hash) ) )  {
                    if ($member_hash == $client_hash) {
                        $record['member_id'] = $record['client_id'];
                    } else if ($member_hash == $reader_hash) {
                        $record['member_id'] = $record['reader_id'];
                    }
                } else {
                    $record = false;
                }
                return $record;
            } else {
                return false;
            }
        }
        
        function getChatInfo($chat_id, $chat_session_id) {
            $getChat = $this->db->query
            ("
                SELECT
                    *
                FROM chats

                WHERE chats.chat_session_id = '{$chat_session_id}' AND chats.id = {$chat_id}

                LIMIT 1

            ");

            if( $getChat->num_rows() )
            {
                $record = $getChat->row_array();
                return $record;
            }
            return null;
        }


        //--- Set the current chat object
        //--- Mainly for private use
        function setSession($session_id = null)
        {

            if(empty($session_id)) {
                return false;
            } else
            {
                try {
                    $getChat = $this->db->query
                    ("
                        SELECT
                            chats.*,
                            members.country as region
    
                        FROM chats
    
                        JOIN members ON members.id = chats.client_id
    
                        WHERE chats.id = {$session_id}
    
                        LIMIT 1
    
                    ");
    
                    if( $getChat->num_rows() )
                    {
    
                        $this->object = $getChat->row_array();
    
                    }
                    return $this;
                } catch (Exception $ex) {
                    return false;
                }
            }

        }

        //--- Update the length of the chat
        //--- We do NOT overwrite the length of the chat, we just append to the current length
        //--- We do not want to send invoices from here... It will get to ovelroaded with crap to deal with

        function recordLength($time = 0)
        {

            if($this->object)
            {

                $array = array();
                $array['length'] = $this->object['length'] + $time;
                
                if ($array['length'] < 0) {
                    $array['length'] = 0;
                }
                if ($array['length'] > $this->object['set_length'] ) {
                    $array['length'] = $this->object['set_length'];
                }
                

                $this->db->where('id', $this->object['id']);
                $this->db->update('chats', $array);

                return true;

            }
            else
            {

                return false;

            }

        }

        //--- Record a message to the transcripts
        function logMessage($member_id, $message)
        {

            if($this->object)
            {

                $array = array();
                $array['datetime'] = date("Y-m-d H:i:s");
                $array['chat_id'] = $this->object['id'];
                $array['member_id'] = $member_id;
                $array['message'] = $message;

                $this->db->insert('chat_transcripts', $array);

                return true;

            }
            else
            {

                return false;

            }

        }

        //--- Load Chat Transcripts
        function loadTranscripts()
        {

            if($this->object)
            {

                $getTranscripts = $this->db->query("SELECT chat_transcripts.*, members.username FROM chat_transcripts LEFT JOIN members ON members.id = chat_transcripts.member_id WHERE chat_id = {$this->object['id']} ");

                return $getTranscripts->result_array();

            }
            else
            {

                return false;

            }

        }

        function getNRRs()
        {
            $this->load->model('nrr_model');

            return $this->nrr_model->get_nrr($this->object['id']);
        }
        
        function startChat() {
            $now = date('Y-m-d H:i:s');
            if($this->object)
            {
                $this->db->where('id', $this->object['id']);
                $this->db->update('chats', array('start_datetime'=>$now));

                return $now;

            }
            else
            {
                return false;
            }
        }

        //--- Refund Chat
        //--- lengthToRefund is number of seconds to give back to the member(THE MAGIC IS GOING TO HAPPEN HERE.)

        function give_timeback($type, $amount)
        {

        }

        function refundChat($lengthToRefund = 0)
        {

            if($this->object)
            {


                $this->db->where('id', $this->object['id']);
                $this->db->update('chats', array('length'=>0));

                return true;

            }
            else
            {

                return false;

            }

        }

        function banUser($reader_id,$member_id,$type = 'personal')
        {
            $insert['member_id'] = $member_id;
            $insert['reader_id'] = $reader_id;

            $this->member->set_member_id($member_id);
            $this->member->banUser($reader_id,$type);

            return true;
        }

        function inactive_chat($type)
        {
            if($type == 'reader')
            {

                $this->load->model('reader')->init($this->object['reader_id']);

                if($this->reader->data['inactive_count'] > 1)
                {
                    $update['inactive_count'] = 0;
                    $update['inactive_timestamp'] = date('Y-m-d G:i:s');
                    $update['status'] = 'offline';

                    $this->reader->update($update);
                }
                else
                {

                    $update['inactive_count'] = $this->reader->data['inactive_count'] + 1;
                    $update['inactive_timestamp'] = DATE('Y-m-d G:i:s');

                    $this->reader->update($update);
                }

            }
            else
            {
                //Only 20 seconds do nothing.
            }

            // email admin and client.
        }
		
		function getLastManualAbortChats($reader_id, $client_id, $number_of_records) {
			$lastAbortQuery = $this->db->query
            ("
                SELECT
                    * 
                FROM chats

                WHERE reader_id = {$reader_id} AND client_id={$client_id} AND ended=1 AND aborted=2

               	ORDER BY create_datetime DESC
                LIMIT {$number_of_records}

            ");
			
			if( $lastAbortQuery->num_rows() )
            {

                $rows = $lastAbortQuery->result_array();
				return $rows;
            }
			return false;
		}
		
		function getLastAutoAbortChat($reader_id, $client_id) {
			$lastAbortQuery = $this->db->query
            ("
                SELECT
                    * 
                FROM chats

                WHERE reader_id = {$reader_id} AND client_id={$client_id} AND ended=1 AND aborted=1

               	ORDER BY create_datetime DESC
                LIMIT 1

            ");

            if( $lastAbortQuery->num_rows() )
            {

                $row = $lastAbortQuery->row_array();
				return $row;
            }
			return false;
		}
		
		function abortChat($reader_status = "online", $auto_abort = false)
        {

            if($this->object)
            {
            	$abort_flag = 0;
				if ($auto_abort) {
					$abort_flag = 1;
				} else {
					$abort_flag = 2; // manual abort
				}
                //--- Load Required Models
                $this->load->model('member');
               
                //--- Update chat termination field
                $this->db->where('id', $this->object['id']);
                $this->db->update('chats', array('ended'=>1, 'aborted' =>$abort_flag));


				// update read profile status to "online"
				$this->member->set_member_id($this->object['reader_id']);
				$this->member->update_profile(array('status'=>$reader_status));

                return true;

            }
            else
            {

                return false;

            }
        }

	function rejectChat()
        {

            if($this->object)
            {

                //--- Load Required Models
                $this->load->model('member');
               
                //--- Update chat termination field
                $this->db->where('id', $this->object['id']);
                $this->db->update('chats', array('ended'=>1, 'rejected' =>1));


				// update read profile status to "online"
				$this->member->set_member_id($this->object['reader_id']);
				$this->member->update_profile(array('status'=>'online'));

                return true;

            }
            else
            {

                return false;

            }
        }

        function endChat()
        {

            if($this->object)
            {
				
                //--- Load Required Models
                $this->load->model('member');
                $this->load->model('member_funds');

                $length_in_seconds = $this->object['length'] - $this->object['total_free_length'];
                if ($length_in_seconds < 0 ) {
                    $length_in_seconds = 0;
                }

                //--- Transactions Summary
                $summary = "Chat Session #{$this->object['id']}";

                //Get reader region
                $this->member->set_member_id($this->object['reader_id']);
                $r_region = $this->member->data['country'];

                //--- Process Reading
                $client_data = $this->member->get_member_data($this->object['client_id']);
                $c_region = strtolower($client_data['country']);
                $grand_total = $this->member_funds->process_reading(($length_in_seconds/60),'reading', $this->object['id'],$this->object['reader_id'],$c_region);
                
                if ($c_region == 'ca')  {
                    $currency = "CAD";
                } else {
                    $currency = "USD";
                }
                
                /*
                $region = $this->member_funds->get_last_balance_currency($this->object['client_id']);
                if ($region == 'ca') {
                    $currency = "CAD";
                } else {
                    $currency = "USD";
                }
                 * */
                
                $this->member_funds->insert_transaction('consume', $grand_total['total'],  $r_region, $summary, null, $currency);

                //--- Update chat termination field
                $this->db->where('id', $this->object['id']);
                $this->db->update('chats', array('ended'=>1));


				// update read profile status to "online" but set it to endChat
				$break_time = date("Y-m-d H:i:s a", time() + END_SESSION_BREAK_TIME);
				$this->member->update_profile(array('status'=>'online', 'break_time' => $break_time));

                return true;

            }
            else
            {

                return false;

            }

        }

        public function calculate_chat_payment($chat_id = null){

            $get = $this->db->query("

                SELECT SUM(profile_balance.amount) as totalPaid
                FROM profile_balance
                WHERE
                  profile_balance.type = 'reading' AND
                  profile_balance.type_id = $chat_id

            ")->row();

            return $get->totalPaid;

        }

    // schema
    /*
+-----------------+--------------+------+-----+------------+----------------+
| Field           | Type         | Null | Key | Default    | Extra          |
+-----------------+--------------+------+-----+------------+----------------+
| id              | int(11)      | NO   | PRI | NULL       | auto_increment |
| start_datetime  | datetime     | YES  |     | NULL       |                |
| reader_id       | int(11)      | YES  | MUL | NULL       |                |
| client_id       | int(11)      | YES  | MUL | NULL       |                |
| topic           | varchar(255) | YES  |     | NULL       |                |
| set_length      | int(11)      | NO   |     | NULL       |                |
| length          | int(11)      | NO   |     | NULL       |                |
| total_free_length| int(11)     | NO   |     | NULL       |                | 
| ended		      | tinyint(1)   | NO   |     | NULL       |                |
| aborted         | tinyint(1)   | NO   |     | NULL       |                | Auto abort flag = 1, manual abort flag = 2
| rejected        | tinyint(1)   | NO   |     | NULL       |                |
| chat_session_id | varchar(255) | YES  |     | NULL       |                |
| reader_seed     | varchar(255) | YES  |     | ReaderSeed |                |
| client_seed     | varchar(255) | YES  |     | ClientSeed |                |
| create_datetime | datetime     | YES  |     | NULL       |                |
+-----------------+--------------+------+-----+------------+----------------+
     */

    }
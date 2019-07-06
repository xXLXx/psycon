<?

    //--- Include Elephant.io
    require_once( APPPATH . "models/ElephantIO/Client.php");

	class Main extends CI_Controller{
	
		function __construct(){
			parent :: __construct();
			$this->settings = $this->system_vars->get_settings();
		}

        public function json(){

            $this->load->model('zendesk');

            $data = $this->zendesk->create_new_ticket("Rob", "robert@activelogiclabs.com", "Test Subject", "The message goes here... ONe more test goes here");

            print_r($data);

        }

        public function test(){

            $this->load->library('internetsecure');

//            $params = array();
//            $params['first_name'] = "Robert";
//            $params['last_name'] = "Kehoe";
//            $params['address'] = "14606 Floyd St.";
//            $params['city'] = "Overland Park";
//            $params['state'] = "Kansas";
//            $params['zip'] = "66223";
//            $params['email'] = "rckehoe@gmail.com";
//            $params['card_number'] = "5542851200880284";
//            $params['card_exp_month'] = "02";
//            $params['card_exp_year'] = "2014";
//            $params['id'] = time();
//
//            $profile = $this->internetsecure->create_profile($params);
//
//            echo "<pre>";
//            print_r($profile);
//
//            exit;

            $token = "eed1c4d11b3a0284";

            $outputArray = $this->internetsecure->authorize($token, 10.00, "Test Authorization");

            echo "<pre>";
            print_r($outputArray);

        }

		function index(){

            $this->hide_banner = true;

			$t = $this->system_vars->get_page('index');
			// $t = $this->system_vars->get_page('homepage');

			$getArticles = $this->db->query("SELECT * FROM articles ORDER BY id DESC LIMIT 5");
			$t['articles'] = $getArticles->result_array();

		
			$this->load->view('header');
			$this->load->view('pages/main', $t);
			$this->load->view('footer');
		
		}
		
		function getTime(){
			$now = new DateTime(); 
			echo $now->format("M j, Y H:i:s"); 
		}

        function testObject(){

            $rampnode = new Client('http://psycon.rampnode.com', 'socket.io', 1, true, true, true);

            $rampnode->init();
            $rampnode->emit('subscribe', 'psycon.rampnode.com/readerStatuses');
            $rampnode->send(Client::TYPE_MESSAGE, null, null, "Test");
            $rampnode->close();

            echo "Done?";

        }
		
		function logout(){

            if(isset($this->member->data['profile_id'])){
                $this->reader->set_status('offline');
            }

			$this->session->sess_destroy();
			redirect("/");
		}

        function banned(){

            if(isset($this->member->data['profile_id'])){
                $this->reader->set_status('offline');
            }

            $this->session->unset_userdata('member_logged');
            $this->session->set_flashdata("error","You were banned from Psychic Contact.");
            redirect("/");
        }

        function disconnect_user($mem_id){
            $this->reader->init($mem_id)->set_status('offline');
        }

		function favorite($profile_id)
		{
		
			if($profile_id)
			{
			
				if($this->session->userdata('member_logged'))
				{
				
					$checkFavs = $this->db->query("SELECT * FROM favorites WHERE profile_id = {$profile_id} AND member_id = {$this->session->userdata('member_logged')} LIMIT 1");
					
					if($checkFavs->num_rows()==0)
					{
					
						$insert = array();
						$insert['profile_id'] = $profile_id;
						$insert['member_id'] = $this->session->userdata('member_logged');
						
						$this->db->insert('favorites', $insert);
						
						$array['message'] = "This profile has been saved to your favorites!";
					
					}
					else
					{
					
						$array['message'] = "This expert's profiles has already been saved to your favorites";
					
					}
				
				}
				else
				{
				
					$array['message'] = "You must be logged in before you can add an experts profile as favorites";
				
				}
			
			}
			else
			{
			
				$array['message'] = "No profile id was specified";
			
			}
			
			echo json_encode($array);
		
		}

        public function check_multiple_reader_status(){
			$now = date("Y-m-d H:i:s");
		
            //--- Build a CSV list of all reader usernames
            $readerString = "";

            foreach($this->input->post('usernameArray') as $i=>$username){
                $readerString .= "'{$username}',";
            }

            $readerString = substr($readerString,0,-1);

            //--- Do one DB call to check ALL usernames
            $getProfile = $this->db->query("

                 SELECT members.username,
                        members.id,
                        members.id as member_id,
                        profiles.last_chat_request,
                        profiles.last_pending_time,
                        profiles.break_time,
                        CASE WHEN profiles.status IS NULL OR profiles.status = ''
                             THEN 'offline'
                             ELSE profiles.status
                        END as status
                FROM    members,
                        profiles
                WHERE   members.username IN ({$readerString})
                        and members.id = profiles.id

            ");

            $finalArray = array();
            foreach($getProfile->result_array() as $user){

                if($this->session->userdata("member_logged")){

                    if($this->member->checkBan($user['id'])){
                        $user['status'] = 'blocked';
                    } 
                    
					if ($user['status'] == "online") {
	                    if (strtotime($user['last_chat_request']) < strtotime($user['last_pending_time'])) {
							// then just consider the pending time. 
							if (strtotime($now) - strtotime($user['last_pending_time']) < CHAT_MAX_PENDING) {
								$user['status'] = "busy";
							} 
						} else {
							if (strtotime($now) - strtotime($user['last_chat_request']) < CHAT_MAX_WAIT) {
								$user['status'] = "busy";
							}
						}
						
						if (strtotime($now) < strtotime($user['break_time'])) {
							$user['status'] = "break";
						}
						
					}
                }

                $finalArray[$user['username']] = $user['status'];
            }

            //--- Return JSON formatted response
            echo json_encode($finalArray);

        }

        public function check_all_reader_statuses(){

            //--- Do one DB call to check ALL usernames
            $getProfile = $this->db->query("

                 SELECT members.username,
                        members.id,
                        CASE WHEN profiles.status IS NULL OR profiles.status = ''
                             THEN 'offline'
                             ELSE profiles.status
                        END as status
                FROM members
                JOIN profiles ON profiles.id = members.id

            ");

            $finalArray = array();
            foreach($getProfile->result_array() as $user){

                if($this->session->userdata("member_logged")){

                    if($this->member->checkBan($user['id'])){
                        $user['status'] = 'blocked';
                    }

                }

                $finalArray[$user['username']] = $user['status'];
            }

            //--- Return JSON formatted response
            echo json_encode($finalArray);

        }
		
	}
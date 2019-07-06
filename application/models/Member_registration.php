<?

    class member_registration extends CI_Model
    {

        //--- Login From Form
        function loginForm()
        {

            $this->form_validation->set_rules('username',"Username","trim|required");
            $this->form_validation->set_rules('password',"Password","trim|required");
            $this->form_validation->set_rules('remember',"Remember Me","trim");

            if(!$this->form_validation->run())
            {

                $array = array();
                $array['error'] = 1;
                $array['message'] = validation_errors();

            }
            else
            {

                $array = $this->login(set_value('username'), set_value('password'), set_value('remember'));

            }

            return $array;

        }

        //--- Login Function
        function login($username = null, $password = null, $remember_member = true)
        {

            if($username&&$password)
            {

                $getUser = $this->db->query("SELECT id,banned,email FROM members WHERE username = '{$username}' AND password = '".md5($password)."' LIMIT 1");

                if($getUser->num_rows()==0)
                {

                    $array = array();
                    $array['error'] = 1;
                    $array['message'] = "Invalid username and/or password";

                }
                else
                {

                    $member = $getUser->row_array();

                    set_cookie(array(
                        'name'   => $this->config->item('registration_cookie_name'),
                        'value'  => $this->encrypt->encode($member['email'] . "*" . $member['id']),
                        'expire' => time() + (10 * 365 * 24 * 60 * 60), // expire in 10 years
                        'domain' => '',
                        'path'   => '/',
                        'prefix' => '',
                        'secure' => FALSE
                    ));

                    if($member['banned'] != 1){

                        $this->db->where('id', $member['id']);
                        $this->db->update('members', array('last_login_date'=>date("Y-m-d H:i:s")));

                        $this->session->set_userdata('member_logged', $member['id']);

                        $array = array();
                        $array['error'] = 0;

                    }
                    else
                    {
                        $array = array();
                        $array['error'] = 1;
                        $array['message'] = "Your account has been banned.";
                    }

                }

            }
            else
            {

                $array = array();
                $array['error'] = 1;
                $array['message'] = "Missing username and/or password";

            }

            return $array;

        }

    }
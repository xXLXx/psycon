<?

    class popup extends CI_Controller
    {

        var $requireLogin = 0;
        var $hideLogo = false;
        var $title = "";
        public $detect = null;

        function __construct()
        {

            parent::__construct();
            $this -> load -> library('Mobile_Detect');
            $this->detect = new Mobile_Detect();
            
        }

        function error($msg)
        {

            $t['error'] = $msg;

            $this->output('chat/error', $t);

        }

        function login()
        {

            if(!$this->session->userdata('redirect')) die("A redirect session variable must be added");

            $this->title = "Login To Your Account";
            $this->output('registration/login_form_popup');

        }

        function login_submit()
        {

            $this->load->model('member_registration');

            $object = $this->member_registration->loginForm();

            if($object['error'])
            {

                $this->error = $object['message'];

                $this->login();

            }
            else
            {

                $redirect = $this->session->userdata('redirect');

                $this->session->unset_userdata('redirect');

                redirect($redirect);

            }

        }

        function output($view, $paramaters = array())
        {

            $array = array();
            $array['content'] = $this->load->view($view, $paramaters, TRUE);
            $array['title'] = $this->title;
            $array['hideLogo'] = $this->hideLogo;
            $array['detect'] = $this->detect;

            $this->load->view('popup/template', $array);

        }

        function requireLogin()
        {

            if(empty($this->member->data['id']))
            {

                return die("You must login");

            }
            else
            {

                return true;

            }

        }

    }
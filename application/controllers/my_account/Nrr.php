<?

class nrr extends CI_Controller
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
        $this->load->model("chatmodel");
        $this->load->model("nrr_model");
    }

    function index(){
        $this->load->view('header');
        $this->load->view('my_account/header');
        $this->load->view('my_account/nrr_list');
        $this->load->view('my_account/footer');
        $this->load->view('footer');
    }

    function details($nrr_id = null){

        if($nrr_id)
        {
            $arr = $this->nrr_model->getNRR($nrr_id);

            //print_r($arr);
            $this->load->view('header');
            $this->load->view('my_account/header');
            $this->load->view('my_account/nrr_details',$arr);
            $this->load->view('my_account/footer');
            $this->load->view('footer');
        }
        else
        {
            redirect('/my_account/nrr');
        }
    }

    function give_timeback($nrr_id = null)
    {
        $this->form_validation->set_rules('type','Type','required|trim');
        $this->form_validation->set_rules('timeback', "Time Back","required|trim|numeric|greater_than[0]");

        if(!$this->form_validation->run()){

            $this->details($nrr_id);

        }else{

            $array = $this->nrr_model->process($nrr_id, set_value('type'), set_value('timeback'));

            print_r($array);
            exit;

            if($array['error'] == '1'){

                $this->session->set_flashdata('error', $array['message']);
                redirect("/my_account/nrr/details/{$nrr_id}");


            }else{

                $this->session->set_flashdata("response", "Refunded " . $array['timeback'] . " time.");
                redirect("/my_account/nrr/details/{$nrr_id}");

            }

        }
    }
}
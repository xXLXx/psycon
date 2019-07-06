<?

class ban_users extends CI_Controller
{

    function __construct()
    {

        parent :: __construct();

        $this->settings = $this->system_vars->get_settings();

        if(!$this->session->userdata('member_logged')){
            $this->session->set_flashdata('error', "You must login before you can gain access to secured areas");
            redirect('/register/login');
            exit;
        }
        $this->reader->init($this->session->userdata('member_logged'));
    }

    function index()
    {
        $t['clients'] = $this->reader->get_clients();
        $this->load->view('header');
        $this->load->view('my_account/header');
        $this->load->view('my_account/ban_users',$t);
        $this->load->view('my_account/footer');
        $this->load->view('footer');
    }

    function search()
    {
        $t['clients'] = $this->reader->get_clients($this->input->post('search_type'),trim($this->input->post('query')));
        $this->load->view('header');
        $this->load->view('my_account/header');
        $this->load->view('my_account/ban_users',$t);
        $this->load->view('my_account/footer');
        $this->load->view('footer');
    }

    function unban($ban_id)
    {
        $this->reader->unbanUser($ban_id);
        $this->session->set_flashdata("response","User unbanned");

        redirect("/my_account/ban_users");
    }

    function ban($member_id)
    {
        $this->member->set_member_id($member_id);
        $this->member->banUser($this->reader->data['id']);
        $username = $this->member->data['username'];

        $this->session->set_flashdata("warning","User {$username} banned.");

        redirect("/my_account/ban_users");
    }
}
 ?>
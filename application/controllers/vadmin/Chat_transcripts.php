<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat_Transcripts extends CI_Controller
{

    function __construct(){

        parent::__construct();

        if(!$this->session->userdata('admin_is_logged')){
            redirect('/vadmin/login');
            exit;
        }

        $this->error = null;
        $this->response = null;
        $this->open_nav = false;
        $this->admin = $this->session->userdata('admin_is_logged');

    }

    function index(){

        $params = array();
        $params['chats'] = $this->db->query("

            SELECT
              chats.*,
              reader.username as reader_username,
              client.username as client_username

            FROM chats
            JOIN members reader ON reader.id = chats.reader_id
            JOIN members client ON client.id = chats.client_id

            ORDER BY start_datetime DESC

        ")->result();

        $this->load->view('vadmin/header');
        $this->load->view('vadmin/chat_transcripts/main', $params);
        $this->load->view('vadmin/footer');
    }

}

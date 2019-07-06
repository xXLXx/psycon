<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Message_Management extends CI_Controller
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

        $this->load->model("messages_model");

    }

    function outbox()
    {

        $t['title'] = "Admin Messages: Outbox";

        $getMessages = $this->db->query("SELECT * FROM messages WHERE sender_id = 1 ");
        $t['messages'] = $getMessages->result_array();
        $t['v_from'] = "";
        $this->load->view('vadmin/header');
        $this->load->view('vadmin/messages/messages_outbox', $t);
        $this->load->view('vadmin/footer');

    }

    function index()
    {

        $t = array();
        $t['title'] = "Message Management";
        $t['messages'] = $this->messages_model->getAdminMessages();

        $this->load->view('vadmin/header');
        $this->load->view('vadmin/messages/main', $t);
        $this->load->view('vadmin/footer');
    }

    function compose($message_reply_id = null,$type="reply")
    {

        $getAllMembers = $this->db->query
            ("

				SELECT
					members.id,
					members.username

				FROM
					profiles

				LEFT JOIN members ON members.id = profiles.id

				GROUP BY
					members.id

				ORDER BY
					members.username

			");
        $t['users'] = $getAllMembers->result_array();

        $t['to'] = "";
        $t['subject'] = "";
        $t['message'] = "";

        // If a reply id was passed… Then
        // prepopulate the fields
        if($message_reply_id && $type=="reply")
        {

            $getMessage = $this->db->query("SELECT * FROM messages WHERE id = {$message_reply_id} AND (ricipient_id = {$this->member->data['id']} OR sender_id = {$this->member->data['id']}) LIMIT 1");

            if($getMessage->num_rows()==1)
            {

                $message = $getMessage->row_array();

                $sender = $this->system_vars->get_member($message['sender_id']);

                $t['to'] = $message['sender_id'];
                $t['subject'] = "Re: ".$message['subject'];
                $t['message'] = "\n\n\n+++ Original Message: {$sender['first_name']} {$sender['last_name']} - {$sender['username']} on ".date("m/d/y h:i:s", strtotime($message['datetime']))." +++\n\n".$message['message'];

            }

        }
        else if($message_reply_id && $type != "reply")
        {

            $t['to'] = $message_reply_id;
            $t['subject'] = "";
            $t['message'] = "";
        }

        $this->load->view('vadmin/header');
        $this->load->view('vadmin/messages/messages_compose', $t);
        $this->load->view('vadmin/footer');

    }

    function compose_submit()
    {

        $this->form_validation->set_rules("to","Recipient","xss_clean|trim|required");
        $this->form_validation->set_rules("subject","Subject","xss_clean|trim|required");
        $this->form_validation->set_rules("message","Message","xss_clean|trim|required");

        if(!$this->form_validation->run())
        {

            $this->compose();

        }
        else
        {

            $this->member->notify(set_value('to'), 1, set_value('subject'), nl2br(set_value('message')));

            $this->session->set_flashdata('response', "Your message has been sent");

            redirect('/vadmin/message_management');

        }

    }

    function view($message_id)
    {

        $getMessages = $this->db->query("SELECT *
			                                 FROM   messages
			                                 WHERE  id = {$message_id}
			                                        AND (ricipient_id = {$this->member->data['id']}
			                                        OR sender_id = {$this->member->data['id']}) LIMIT 1");
        $t = $getMessages->row_array();


        switch($t['type'])
        {
            case "email":
                $t['v_from'] = "System";
                break;

            case "admin":
                $t['v_from'] = "Administrator";
                break;

            case "reader":
                $sender = $this->member->get_member_data($t['sender_id']);
                $t['v_from'] = "{$sender['first_name']} {$sender['last_name']}";
                break;
        }


        $t['to'] = $this->system_vars->get_member($t['ricipient_id']);

        $this->messages_model->markMessageRead($t['id']);

        $this->load->view('vadmin/header');
        $this->load->view('vadmin/messages/messages_view', $t);
        $this->load->view('vadmin/footer');

    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Billing extends CI_Controller
{
    function __construct()
    {

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

    function fund_account($member_id = null)
    {


        $this->member->set_member_id($member_id);
        $this->member_funds->fund_account($this->input->post('type'),$this->input->post('tier'),$this->input->post('amount'), null, $this->input->post('region'));

        $insert = array();
        $insert['member_id'] = $member_id;
        $insert['datetime'] = date("Y-m-d H:i:s");
        $insert['type'] = 'purchase';
        $insert['amount'] = $this->input->post('amount');
        $insert['summary'] = "Administrator Added Funds";

        $this->db->insert('transactions', $insert);

        redirect("/vadmin/main/transactions/{$member_id}");

    }
}
?>
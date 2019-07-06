<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reader_Management extends CI_Controller
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

        $t = array();
        $t['readers'] = $this->readers->getAll();

        $this->load->view('vadmin/header');
        $this->load->view('vadmin/readers/main', $t);
        $this->load->view('vadmin/footer');
    }

    public function new_payment(){


        if($this->input->post("paypal") == "Pay with paypal")
        {

            // Set paypal variables
            $m = $this->member->get_member_data($this->input->post('readerid'));
            $t['item'] = "Paypal payment";
            $t['item_number'] = null;
            $t['custom'] = $this->input->post('readerid')."*"."payment";
            $t['return_url'] = site_url("vadmin/reader_management");
            $t['cancel_url'] = site_url("vadmin/reader_management/paypal_cancel");
            $t['notify_url'] = site_url("paypal_ipn/deposit");
            $t['amount'] = $this->input->post('amount');
            $t['email'] = $m['paypal_email'];
            // Load paypal module
            $this->load->view('vadmin/paypal', $t);




        }
        else
        {
            $readerid = $this->input->post('readerid');
            $region = $this->input->post('region');
            $amount = $this->input->post('amount');
            $notes = $this->input->post('notes');

            $this->member->data['id'] = $readerid;
            $this->member_funds->insert_transaction('payment', $amount, $region, $notes);

            redirect("/vadmin/reader_management");
        }

    }

    public function paypal_cancal()
    {
        $this->session->set_flashdata('error', "Your PayPal payment has been canceled. ");

        $redirect = $this->session->userdata('redirect');
        if($redirect){ redirect($redirect); exit; }

        redirect("/my_account");

    }

    public function download_transactions(){

        set_time_limit(0);
        ini_set('memory_limit', '-1');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=reader_transactions.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $readerid = $this->input->post('readerid');
        $from = date("Y-m-d H:i:s", strtotime($this->input->post('from')));
        $to = date("Y-m-d H:i:s", strtotime($this->input->post('to')));
        $region = $this->input->post('region');
        $service = $this->input->post('service');

        $get = $this->db->query("
            SELECT
                members.id as reader_id,
                members.username as reader,
                profile_balance.datetime,
                profile_balance.tier,
                profile_balance.type,
                profile_balance.region,
                profile_balance.amount,
                profile_balance.commission as payment

            FROM profile_balance
            LEFT JOIN members ON members.id = profile_balance.reader_id
            WHERE
                ".($readerid ? "reader_id = {$readerid} AND " : "")."
                (datetime BETWEEN '{$from}' AND '{$to}') AND
                ".($region ? "region = '{$region}' AND " : "")."
                ".($service ? "type = '".($service == 'chat' ? "reading" : "email")."' AND " : "")."
                profile_balance.id IS NOT NULL

        ");

        echo $this->array2csv($get->result_array());

    }

    public function array2csv(array &$array){
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    public function new_reader(){

        $memberid = $this->input->post('memberid');
        $legacy = $this->input->post('legacy');

        $insert = array();
        $insert['id'] = $memberid;
        $insert['active'] = 1;
        if($legacy){
            $insert['legacy_member'] = 1;
        }

        $this->db->insert('profiles', $insert);

        redirect("/vadmin/reader_management");

    }

    public function toggleFeatured($readerId = null){

        $this->reader->init($readerId);
        $reader = $this->reader->data;

        $update = array();
        $update['featured'] = ($reader['featured'] ? 0 : 1);

        $this->db->where('id', $readerId);
        $this->db->update('profiles', $update);

        redirect("/vadmin/reader_management");

    }

}

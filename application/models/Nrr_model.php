<?

class nrr_model extends CI_Model
{

    //--- Get MANY NRR Records
    function get_nrr($chat_id = null, $mid = null, $rid = null)
    {
        $sql = " where 1 ";

        if($chat_id)
        {
            $sql .= " and n.chat_id = {$chat_id} ";
        }

        if($mid)
        {
            $sql .= " and n.member_id = {$mid} ";
        }

        if($rid)
        {
            $sql .= " and n.reader_id = {$rid} ";
        }




        $n = $this->db->query("select   *
                               from     nrr n
                               {$sql}
                               order by date desc ");
        return $n->result_array();
    }

    //--- Get 1 NRR Record
    function getNRR($id)
    {


        $n = $this->db->query("select   n.*,m.username, m.first_name, m.last_name
                               from     nrr n,
                                        members m
                               where    n.id = {$id}
                                        and n.member_id = m.id
                               order by date desc ");
        return $n->row_array();
    }

    //--- Update NRR Record
    function update($id, $params)
    {
        $this->db->where("id", $id);
        if(isset($params['amount']))
        {
            if($params['amount'] < 0)
            {
                $params['amount'] = 0;
            }
        }
        $this->db->update("nrr",$params);

        return $this->db->affected_rows();
    }

    //--- Create unhappy NRR Request
    public function create($chat_id, $admin = 0){

        $chat = $this->db->query("SELECT * FROM chats WHERE id = {$chat_id} ")->row();

        $this->load->model('chatmodel');
        $amount = $this->chatmodel->calculate_chat_payment($chat->id);

        $insert = array();
        $insert['chat_id'] = $chat->id;
        $insert['reader_id'] = $chat->reader_id;
        $insert['member_id'] = $chat->client_id;
        $insert['type'] = 'unhappy';
        $insert['unhappy_timeback'] = $amount;
        $insert['date'] = date("Y-m-d H:i:s");
        $insert['admin'] = $admin;

        $this->db->insert('nrr', $insert);

        $array = array();
        $array['nrr_id'] = $this->db->insert_id();
        $array['amount'] = $amount;
        $array['client_id'] = $chat->client_id;

        return $array;

    }

    //--- Type: paid or free
    public function process($nrr_id, $type = null, $timeback = 0){

        $this->load->model('member');

        $obj = $this->getNRR($nrr_id);
        $this->member->set_member_id($obj['member_id']);

        if($obj['refunded'] != 1){

            //--- Build needed variables
            $amount = $timeback * -1;
            $region = (trim(strtolower($this->member->data['country']))=='ca' ? "ca" : "us");

            //--- Update NRR Object
            $params = array();
            $params['refunded'] = 1;
            $params['amount'] = set_value("timeback");

            $this->update($nrr_id, $params);

            //--- Insert record to give time back to client
            $this->member_funds->fund_account('reading', $type == 'paid' ? "regular" : "free", $timeback, null, $region);
            $this->member_funds->insert_transaction('refund', $amount, $region, "Refund for NRR #" . $nrr_id);

            //--- Mark reader's profile_balance with chat_ids to no pay
            $this->reader->unpay_reader_for_chat($obj['chat_id']);

            //--- Return
            $array = array();
            $array['error'] = '0';

        }else{

            $array = array();
            $array['error'] = '1';
            $array['message'] = "Cannot process this NRR";

        }

        return $array;

    }

    //--- Check For NRR for chat
    public function check_nrr_for_chat($chat_id){
        return $this->db->query("SELECT id FROM nrr WHERE chat_id = {$chat_id} LIMIT 1")->num_rows();
    }

}
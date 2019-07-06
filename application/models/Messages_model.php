<?

class messages_model extends CI_Model
{

    var $data = false;

    function __construct()
    {

        if($this->session->userdata('member_logged'))
        {

            $this->data = $this->member->get_member_data($this->session->userdata('member_logged'));

        }

    }

    function getMessage($message_id)
    {
        $getM = $this->db->query("select *
                                  from   messages m
                                  where  m.id = {$message_id}");
        return $getM -> row_array();
    }

    function markMessageRead($message_id)
    {
        if(isset($this->data['id']))
        {
            $m = $this->getMessage($message_id);

            //only recipient can mark message Read.
            if($m['ricipient_id'] == $this->data['id'])
            {
                $update['read'] = 1;
                $this->db->where("id",$message_id);
                $this->db->update("messages",$update);

                return $this->db->affected_rows();

            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function notify($sender_id = null,
                    $recipient_id = null,
                    $subject = null,
                    $message = null,
                    $type = null,
                    $email_template ='member_notification')
    {

        // Get reader
        $sender = $this->member->get_member_data($sender_id);
        $recipient = $this->member->get_member_data($recipient_id);

        $params = $recipient;

        if($sender)
        {
            $params['sender_username'] = $sender['username'];
            $params['sender_id'] = $sender_id;
        }
        else
        {
            $params['sender_id'] = null;
        }


        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['type'] = $type;

        $params['recipient_id'] = $recipient_id;

        $this->system_vars->m_omail($params['id'],$email_template,$params);

        return true;

    }

    function getAdminMessages()
    {
        $getMessages = $this->db->query("select   *
                                           from   messages m
                                           where  type = 'admin'
                                                  and ricipient_id = 1");
        return $getMessages -> result_array();
    }

    function sendAdminMessage($sender_id,$subject,$message)
    {
        $params = array();
        $params['type'] = "admin";
        $params['sender_id'] =  $sender_id;

        $m = $this->member->get_member_data($sender_id);
        $params['username'] = $m['username'];
        $params['subject'] = $subject;
        $params['message'] =  $message;

        $this->system_vars->m_omail(1,"admin_notification_template",$params);
    }

    function getUnreadMessages()
    {
        if(isset($this->data['id']))
        {
            $getUnread = $this->db->query("select count(id) as unread
                                           from   messages m
                                           where  m.ricipient_id = {$this->data['id']}
                                           and m.read != 1");

            $total = $getUnread->row_array();
            return $total['unread'];
        }
        else
        {
            return 0;
        }
    }
}
?>
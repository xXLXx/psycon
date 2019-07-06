<?

define('USR', 'support@dev.psychic-contact.com');
define('KEY', 'eTb1n2qE04l8FgGSeT8x2cLnQ3ueewiAymzgJX09');
define('URL', 'https://devpsychiccontact.zendesk.com/api/v2/');

class Zendesk extends CI_Model
{

    //-- Createa a new ticket for customer
    function create_new_ticket($name, $email, $subject, $comments, $phone_number = null, $username = null)
    {

        $array = array();
        $array['ticket'] = array();

        $array['ticket']['requester'] = array();
        $array['ticket']['requester']['name'] = $name;
        $array['ticket']['requester']['email'] = $email;

        $array['ticket']['subject'] = $subject;

        $array['ticket']['comment'] = array();
        $array['ticket']['comment']['body'] = $comments;

        $addit = array();
        if($phone_number) $addit[] = array("id"=>"22250834", "value"=>$phone_number);
        if($username) $addit[] = array("id"=>"22250844", "value"=>$username);

        $array['ticket']['custom_fields'] = $addit;

        return $this->curl_it("tickets.json", 'POST', $array);

    }

    //--- Send request to ZenDesk
    function curl_it($method = '', $type = 'POST', $paramaters = null)
    {

        $ch = curl_init(URL . $method);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_USERPWD, USR."/token:".KEY);

        switch($type)
        {
            case "POST":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paramaters));
                break;
            case "GET":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paramaters));
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                break;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($output);
        return $decoded;

    }

}
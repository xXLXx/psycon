
	<style>
	
		.expert_name
		{
			color: #2850A8;
			display: block;
			font-size: 14px;
			font-weight: bold;
			padding: 10px 0 3px;
			text-decoration: underline !important;
		}
	
	</style>

    <div class='content_area'>
		
	    <h2>Chat History</h2>

        <hr />

        <?

            if($chats)
            {

/*
// Rob test code, remove after
echo "<pre>";
print_r($this->member->data);
echo "</pre>";
*/
	            echo "<table class='table table-striped table-hover'>";

                foreach($chats as $c)
                {

                    if($this->member->data['id'] == $c['client_id']){

                        //--- Get reader
                        $user = $this->system_vars->get_member($c['reader_id']);

                    }else{

                        //--- Get client
                        $user = $this->system_vars->get_member($c['client_id']);

                    }

                    //$chat_length = $this->system_vars->time_generator($c['length']);

					/*
					Rob: test code to show client or reader feedback form URLs
					*/

					$fname = $this->member->data['first_name'];
					$email = $this->member->data['email'];
					$country = $this->member->data['country'];
					$time = date("m/d/Y @ h:i A", strtotime($c['start_datetime']));

					if ($this->member->data['member_type'] == "reader")
					{
						$type = 1;
						$murl = "http://www.psychic-contact.com/modules/chattestingform/reader_form.php?un=".$this->member->data['username']."&cn=".$user['username']."&fn=$fname&email=$email&country=$country&time=$time&type=$type";
						$link_name = "Submit";
					}
					else
					{
						$type = 0;
						$murl = "http://www.psychic-contact.com/modules/chattestingform/client_form.php?un=".$this->member->data['username']."&rn=".$user['username']."&fn=$fname&email=$email&country=$country&time=$time&type=$type";
						$link_name = "Submit";

					}
					$t_url = "<br><a href=\"$murl\" target=\"_blank\">[$link_name Feedback]</a>";
					/*
					Rob: test code to show client or reader feedback form URLs
					- remove $t_url below to hide links
					*/
					
					echo "
                    <tr>
                        <td width='175' style='vertical-align:middle;'>". $c['id'] ." $t_url</td>
                        <td width='175' style='vertical-align:middle;'>".date("m/d/Y @ h:i A", strtotime($c['start_datetime']))."</td>
                        <td style='vertical-align:middle;'>{$user['username']}</td>
                        <td style='vertical-align:middle;'>";
                            //div style='font-size:12px;'>{$chat_length['minutes']} Minutes & {$chat_length['seconds']} Seconds</div>
                       echo "</td>
                        <td style='width:100px; vertical-align:middle; text-align:right'><a href='/my_account/chats/transcript/{$c['id']}' class='btn'>Details</a></td>
                    </tr>
                    ";

                }

                echo "</table>";

            }
            else
            {

                echo "<div>You do not have a chat history.</div>";

            }

        ?>

    </div>

	<div class="horizontal_bar">
		<a href="/my_account/messages">Inbox</a>
		<a href="/my_account/messages/outbox">Outbox</a>
		<a href="/my_account/messages/compose">Send Message</a>
	</div>

	<h2>My Messages - Outbox</h2>
	
	<div>&nbsp;</div>
	
	<?
	
		if($messages)
		{
		
			echo "<table width='100%'>";
		
			foreach($messages as $m)
			{
			

                switch($m['type'])
                {
                    case "email":
                        $v_from = "System";
                        break;

                    case "admin":
                        $v_from = "Administrator";
                        break;

                    case "reader":
                        $sender = $this->member->get_member_data($m['ricipient_id']);
                        $v_from ="{$sender['first_name']} {$sender['last_name']}";
                        break;
                }
				echo "
				<tr>
					<td width='150'>".date("m/d/y h:i A", strtotime($m['datetime']))." EST</td>
					<td>{$v_from}</td>
					<td>{$m['subject']}</td>
					<td width='50' align='center'><a href='/my_account/messages/view/{$m['id']}' class='blue-button'><span>View</span></a></td>
					<td width='50' align='center'><a href='/my_account/messages/delete/{$m['id']}' onClick=\"Javascript:return confirm('Are you sure you want to delete this message?');\" class='blue-button'><span>Delete</span></a></td>
				</tr>";
			
			}
			
			echo "</table>";
		
		}
		else
		{
		
			echo "<p>You have not sent any messages</p>";
		
		}
	
	?>
	
	<div>&nbsp;</div>


	<div style='padding-bottom:25px;'>
		<h2><?=$title?></h2>
	</div>
	
	<ul class="nav nav-tabs">
		<li <?=($this->uri->segment('3')=='' ? " class=\"active\"" : "")?>><a href="/my_account/messages">Inbox</a></li>
		<li <?=($this->uri->segment('3')=='outbox' ? " class=\"active\"" : "")?>><a href="/my_account/messages/outbox">Outbox</a></li>
		<li <?=($this->uri->segment('3')=='compose' ? " class=\"active\"" : "")?>><a href="/my_account/messages/compose"><span class='icon icon-comment'></span> Compose A New Message</a></li>
	</ul>
	
	<?
	
		if($messages)
		{
		
			echo "<table width='100%' cellPadding='0' cellSpacing='0' class='table table-striped table-hover'>";
		
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
                        $sender = $this->member->get_member_data($m['sender_id']);
                        $v_from ="{$sender['first_name']} {$sender['last_name']}";
                    break;
                }

                $unread = "";
                if($m['read'] != 1)
                {
                    $unread = "class=\"unread\"";
                }
				echo "
				<tr {$unread} >
					<td width='150'>".date("m/d/y h:i A", strtotime($m['datetime']))." EST</td>
					<td>{$v_from}</td>
					<td>{$m['subject']}</td>
					<td width='50' align='center'><a href='/my_account/messages/view/{$m['id']}' class='btn'>View</a></td>
					<td width='50' align='center'><a href='/my_account/messages/delete/{$m['id']}' onClick=\"Javascript:return confirm('Are you sure you want to delete this message?');\" class='btn'>Delete</a></td>
				</tr>";
			
			}
			
			echo "</table>";
		
		}
		else
		{
		
			echo "<p>You do not have any messages</p>";
		
		}
	
	?>

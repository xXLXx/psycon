
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
		
		<?
		
			if($chats)
			{
			
				echo "<table class='table table-striped table-hover'>";
			
				foreach($chats as $c)
				{
				
					$expert = $this->system_vars->get_member($c['expert_id']);
					$chat_length = $this->system_vars->seconds_to_minutes($c['length']);
				
					echo "
					<tr>
						<td style='width:80px;'><img src='{$expert['profile']}' width='75' class='img-polaroid'></td>
						<td style='vertical-align:middle;'>
							<a href='/profile/view/{$c['profile_id']}' class='expert_name'>{$expert['username']}</a>
							<div style='font-size:12px;'>{$chat_length['minutes']} Minutes & {$chat_length['seconds']} Seconds</div>
						</td>
						<td style='width:100px; vertical-align:middle; text-align:right'><a href='/client/chats/transcript/{$c['id']}' class='btn'>Details</a></td>
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
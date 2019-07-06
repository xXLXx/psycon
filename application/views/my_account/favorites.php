
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

	<h2 style='margin-bottom:25px;'>My Favorite Readers</h2>
	
	<?
	
		if($favorite_experts)
		{
		
			echo "<table class='table table-striped table-hover'>";
		
			foreach($favorite_experts as $profile)
			{
			
				$member = $this->system_vars->get_member($profile['id']);
			
				echo "
				<tr>
					<td style='width:80px;'><a href='/profile/{$profile['username']}'><img src='{$member['profile']}' width='75' class='img-polaroid'></a></td>
					<td style='vertical-align:middle;'><a href='/profile/{$profile['username']}' class='expert_name'>{$member['username']}</a></td>
					<td style='vertical-align:middle; text-align:right'>
					
						<a href='/my_account/favorites/delete/{$profile['id']}' onClick=\"Javascript:return confirm('Are you sure you want to delete this expert\'s profile from your favorites?');\" class='btn btn-link'><span class='icon icon-trash'></span></a>
						<a href='/profile/{$profile['username']}' class='btn'>View</a>
					
					</td>
				</tr>
				";
			
			}
			
			echo "</table>";
		
		}
		else
		{
		
			echo "<div>You do not have any favorite experts</div><hr />";
		
		}
	
	?>
	
	<h2 style='margin:45px 0 25px;'>Readers I've Chatted With</h2>
	
	<?
	
		if($recent_experts)
		{
		
			echo "<table class='table table-striped table-hover'>";
		
			foreach($recent_experts as $profile)
			{
			
				$member = $this->system_vars->get_member($profile['id']);
				
				$subcategories = $this->system_vars->get_profile_subcategories($profile['id'], false);
			
				// <td style='vertical-align:middle;'><div><u>Chat Time:</u></div>".gmdate("H : i : s", $profile['totalLength'])."</td>
			
				echo "
				<tr>
					<td style='width:80px;'><img src='{$member['profile']}' width='75' class='img-polaroid'></td>
					<td style='vertical-align:middle;'><b class='expert_name'>{$member['username']}</b><div>{$subcategories}</div></td>
					
					<td style='width:50px; vertical-align:middle;'><a href='/client/experts/leave_review/chat/{$profile['session_id']}' class='btn btn-warning'>Review</a></td>
					<td style='width:50px; vertical-align:middle; text-align:right'><a href='/profile/{$profile['username']}' class='btn'>View</a></td>
				</tr>
				";
			
			}
			
			echo "</table>";
		
		}
		else
		{
		
			echo "<div>You have not chatted with any experts</div>";
		
		}
	
	?>

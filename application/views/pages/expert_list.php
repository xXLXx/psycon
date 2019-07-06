
	<style>
	
		.article-table td{ font-size:12px; }
		.article-table td a{  }
	
	</style>

	<div class='content_area'>
	
		<h2>Expert List</h2>
		<div>There were <?=count($experts)?> expert(s) found</div>
		
		<div>&nbsp;</div>
		
		<div align='center'>
		
			<?
			
				foreach($experts as $e)
				{
				
					$member = $this->system_vars->get_member($e['member_id']);
			
					if($e['available_for_chat'] && $e['available_for_email']) $communication_type = "Chat & Email";
					elseif($e['available_for_chat']) $communication_type = "Chat Only";
					elseif($e['available_for_email']) $communication_type = "Email Only";
					else $communication_type = "None";
			
					echo "
					<div class='expert_block' ".(isset($e['featured']) ? " style='background-color:#f6f3db;'" : "").">
					
						<div class='ex_left' align='left'>
						
							<a href=\"/profile/view/{$e['id']}\"><img src=\"{$member['profile']}\" width='100' style='border:solid 2px #E0E0E0;'></a>
							
							<div style='padding:5px 0 0;'><b>Offline</b></div>
							<div style='padding:5px 0 0;'><a href=\"/profile/view/{$e['id']}\"><img src=\"/media/images/user_icon.png\"> View Profile</a></div>
							<div style='padding:5px 0 0;'><a href=\"/profile/add_to_favorites/{$e['id']}\"><img src=\"/media/images/star_icon.png\"> Add to favorite</a></div>
						
						</div>
						
						<div class='ex_right' align='left'>
						
							<h3><a href=\"/profile/view/{$e['id']}\">{$member['username']}</a></h3>
							
							<div style='padding:5px 0 0;'><strong>Rating:</strong> N/A</div>            	
					
							<div style='padding:5px 0 0;'><strong>Available Modes Of Communication:</strong> {$communication_type} </div>				
							<div style='padding:5px 0 0;'>{$e['brief_description']}</div>";
						
							if($e['available_for_email']=='1')
							{
							
								echo "
								<div class='email_button'>
									<a href=\"/profile/send_question/{$e['id']}\" class='blue-button'><span>Send Question</span></a>
									<div class='price'>($ ".number_format($e['price_per_email'], 2)." per question)</div>
								</div>";
							
							}
					
						echo "
						</div>
						
						<div class='clear'></div>
						
					</div>
					";
				
				}
			
			?>
		
		</div>
		
	
	</div>
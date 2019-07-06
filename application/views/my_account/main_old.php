
	<style>
	
		.desc{ padding:5px 0 20px 0; }
		.desc:last-child{ padding:0; }
		
		#profile_div div{ line-height:18px; }
	
	</style>
	
	<script>
	
		$(document).ready(function()
		{
		
			$('#switch_profile_select').change(function()
			{
			
				window.location = "/my_account/main/index/"+$(this).val();
			
			});
		
		});
	
	</script>
	
	<div class="horizontal_bar">
		<a href="/my_account/main/create_new_profile" style='font-weight:bold;color:blue;text-decoration:underline;'>Register For A New Category</a>
	</div>
	
	<?
	
		if($status=='0')
		{
		
			echo "
			<div style='background:#fff6d7;padding:10px;'><b>Pending Approval</b><br />This profile has not been approved by a system administrator. You will get an email when this profile has been approved. In the meantime, please <a href='/contact'>contact us</a> with any questions.</div>
			";
		
		}
		
		$isFeatured = $this->system_vars->is_featured($id);
		
		if($isFeatured)
		{
		
			echo "
			<div style='background:#f3e493;padding:10px;'><b>THIS PROFILE IS FEATURED UNTIL ".date("F d, Y @ h:i A", $isFeatured)." EST</b></div>
			";
		
		}
	
	?>
	
	<div class='padded'>

		<div align='left'>
		
			<h2 style='margin:0 0 10px 0;'>Hi, <?=$this->member->data['first_name']?> <?=$this->member->data['last_name']?></h2>
			
			<table>
			
				<tr>
					<td valign='top' width='125'>
					
						<img src='<?=$this->member->data['profile']?>' width='100' style='border:solid 3px #C0C0C0;'>
						<div style='padding:10px 0 0;'><a style='font-size:11px;' href='/my_account/account'>Change Profile Image</a></div>
						
					</td>
					<td valign='top'>
						<div><h3><?=$category_title?></h3></div>
						<div style='padding:10px 0 5px;'><b>Approved in the following categories:</b> <br />
						
							<?
							
								$getSubcategories = $this->db->query("SELECT subcategories.title FROM profile_subcategories,subcategories WHERE profile_subcategories.profile_id = {$id} AND subcategories.id = profile_subcategories.subcategory_id GROUP BY profile_subcategories.id ");
								$total_subcategories = $getSubcategories->num_rows();
							
								foreach($getSubcategories->result_array() as $i=>$p)
								{
								
									echo "{$p['title']}";
									
									if($total_subcategories!=($i+1))
									{
										echo "<br />";
									}
								
								}
								
							?>
						
						</div>
						<div style='padding:5px 0 0;'><b>Available modes of communication:</b> <br />
						<?
						
							if($available_for_chat && $available_for_email) echo "Chat & Email";
							elseif($available_for_chat) echo "Chat Only";
							elseif($available_for_email) echo "Email Only";
							else echo "None";
						
						?>
						</div>
					</td>
				</tr>
				
			</table>
			
		</div>
		
	</div>
	
	<?
	
		if(count($profiles) > 1)
		{
		
			echo "
			<div class=\"horizontal_bar\">
			
				<h3 style='float:left;margin:8px 0 0 5px;'>{$category_title}</h3>
	
				<select id='switch_profile_select'>
					<option value=''>Switch Profile</option>";
					
						foreach($profiles as $p)
						{
						
							echo "<option value='{$p['id']}'".($id==$p['id'] ? " selected" : "").">{$p['category_title']}</option>";
						
						}
					
					echo "
				</select>
				
			</div>
			";
		
		}
	
	?>		
			
	<div class='padded'>
	
		<div align='left'>
				
			<div id='profile_div' align='left' style='padding:10px 0;'>
			
				<?
				
					echo "
					
					<h3>Brief Description</h3>
					<div class='desc'>".nl2br($brief_description)."</div>
					
					<h3>Detailed Description</h3>
					<div class='desc'>".nl2br($detailed_description)."</div>
					
					<h3>Degrees</h3>
					<div class='desc'>".nl2br($degrees)."</div>
					
					<h3>Experience</h3>
					<div class='desc'>".nl2br($experience)."</div>
					";
				
				?>
				
				<a href='/my_account/main/edit_profile/<?=$id?>' class='blue-button'><span>Edit Profile</span></a> &nbsp; 
				<a href='/my_account/main/become_featured/<?=$id?>' class='blue-button'><span>Feature My Profile</span></a>
			
			</div>
	
		</div>

	</div>
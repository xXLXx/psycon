
	<style>
		
		textarea{ width:100%; height:200px; }
		#email_readings_div{ <?=($this->member->data['enable_email']=='1' ? "" : "display:none;")?> }
	
	</style>
	
	<script>
	
		$(document).ready(function()
		{
		
			$("input[name='enable_email']").click(function(e)
			{
			
				if($(this).is(':checked'))
				{
				
					$('#email_readings_div').show();
				
				}
				else
				{
				
					$('#email_readings_div').hide();
				
				}
			
			});
		
		});
	
	</script>

	<div class='padded'>
	
		<h2>Edit My Profile</h2>
		
		<hr />
		
		<form action='/my_account/main/save_profile/' enctype="multipart/form-data" method='POST'>
		
			<table width='100%' cellPadding='10'>

                <tr>
                    <td style='width:150px;'><b>Email Address:</b></td>
                    <td><?=$this->member->data['email']?></td>
                </tr>

                <tr>
                    <td style='width:150px;'><b>Username:</b></td>
                    <td><?=$this->member->data['username']?></td>
                </tr>
                <tr><td colSpan='2'><div style='color:#C0C0C0;'>Paypal Email Address required for paypal payments.</div></td></tr>
                <tr>
                <td style='width:150px;'><b>Paypal Email Address:</b></td>
                <td><input type='text' name='paypal_email' value='<?=set_value('paypal_email', $this->member->data['paypal_email'])?>'/></td>
                </tr>
                <tr><td colSpan='2'><div style='color:#C0C0C0;'>Leave password fields blank to keep your current password</div></td></tr>

                <tr>
                    <td style='width:150px;'><b>Choose A New Password:</b></td>
                    <td><input type='password' name='password' value='<?=set_value('password')?>'></td>
                </tr>

                <tr>
                    <td style='width:150px;'><b>Re-Type New Password:</b></td>
                    <td><input type='password' name='password2' value='<?=set_value('password2')?>'></td>
                </tr>

                <tr><td colSpan='2'><hr style='margin:10px 0;' /></td></tr>

                <tr>
                    <td style='width:150px;'><b>First Name:</b> <span style='color:red;'>*</span></td>
                    <td><input type='text' name='first_name' value='<?=set_value('first_name', $this->member->data['first_name'])?>'></td>
                </tr>

                <tr>
                    <td style='width:150px;'><b>Last Name:</b> <span style='color:red;'>*</span></td>
                    <td><input type='text' name='last_name' value='<?=set_value('last_name', $this->member->data['last_name'])?>'></td>
                </tr>

                <tr>
                    <td style='width:150px;'><b>Gender:</b> <span style='color:red;'>*</span></td>
                    <td><div><input type='radio' name='gender' value='Male' <?=set_radio('gender','Male', ($this->member->data['gender']=='Male' ? TRUE : FALSE))?>> Male &nbsp; &nbsp; <input type='radio' name='gender' value='Female' <?=set_radio('gender','Female', ($this->member->data['gender']=='Female' ? TRUE : FALSE))?>> Female</div></td>
                </tr>

                <tr>
                    <td style='width:150px;'><b>Date of Birth:</b> <span style='color:red;'>*</span></td>
                    <td>
                        <?

                        list($dob_year,$dob_month,$dob_day)=explode("-", $this->member->data['dob']);
                        echo $this->system_vars->dob_custom('dob', set_value('dob_month', $dob_month), set_value('dob_day', $dob_day), set_value('dob_year', $dob_year));

                        ?>
                    </td>
                </tr>

                <tr>
                    <td style='width:150px;'><b>Country:</b> <span style='color:red;'>*</span></td>
                    <td><?=$this->system_vars->country_array_select_box('country', set_value('country', $this->member->data['country']))?></td>
                </tr>

                <tr><td colSpan='2'><hr style='margin:10px 0;' /></td></tr>

                <tr>
                    <td style='width:150px;'><b>Profile Image:</b></td>
                    <td>

                        <img src='<?=$this->member->data['profile_image']?>' style='border:solid 1px #000;'>

                        <div style='padding:10px 0;color:#C0C0C0'>To keep your current profile image, leave the field below blank3</div>

                        <input type='file' name='profile_image'>

                    </td>
                </tr>


                <tr>
					<td width='150'><b>Title:</b></td>
					<td><input type='text' name='title' value='<?=set_value('title', $this->member->data['title'])?>' style='width:100%;' /></td>
				</tr>
				
				<tr>
					<td width='150' valign='top'><b>Select Categories:</b></td>
					<td>
					
						<?
						
							foreach($categories as $c)
							{
							
								echo "<div><input type='checkbox' name='categories[]' value='{$c['id']}' ".(in_array($c['id'], $registered_categories) ? " checked" : "")."> {$c['title']}</div>";
							
							}
						
						?>
					
					</td>
				</tr>
				

				<tr>
					<td width='150'><b>Biography:</b></td>
					<td><textarea rows='10' name='biography'><?=set_value('biography', $this->member->data['biography'])?></textarea></td>
				</tr>
				
				<tr>
					<td width='150'><b>Area of Expertise:</b></td>
					<td><textarea rows='10' name='area_of_expertise'><?=set_value('area_of_expertise', $this->member->data['area_of_expertise'])?></textarea></td>
				</tr>
				
				<tr>
					<td width='150'><b>&nbsp;</b></td>
					<td><input type='checkbox' name='enable_email' value='1' style='margin:0;' <?=set_checkbox('enable_email', '1', ($this->member->data['enable_email']=='1' ? TRUE : FALSE))?>> Enable email readings<div style='color:#C0C0C0;font-size:12px;'>** Please DISABLE when you cannot respond to email readings in a timely manner.</div></td>
				</tr>
				
				<tr>
					<td width='150'>&nbsp;</td>
					<td>
						<div id='email_readings_div'>
							<div style='padding-bottom:5px;'><b>How many days will it take you to complete 1 question via email?</b></div>
							<input type='text' name='email_total_days' value='<?=set_value('email_total_days', $this->member->data['email_total_days'])?>' class='input-mini' />
						</div>	
					</td>
				</tr>
				
				<tr>
					<td width='150'><b>&nbsp;</b></td>
					<td><input type='submit' name='save' class='btn btn-primary btn-large' value='Save My Profile'></td>
				</tr>
				
			</table>
		
		</form>
	
	</div>
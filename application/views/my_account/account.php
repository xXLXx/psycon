
	<div class='content_area'>
	
		<h2>Update My Account</h2>
				
		<hr />
				
		<form action='/my_account/account/save_account' method='POST' enctype="multipart/form-data">
		
			<table width='100%' cellPadding='10' cellSpacing='0'>
			
				<tr>
					<td style='width:150px;'><b>Email Address:</b></td>
					<td><?=$this->member->data['email']?></td>
				</tr>
				
				<tr>
					<td style='width:150px;'><b>Username:</b></td>
					<td><?=$this->member->data['username']?></td>
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
				
				
				<tr><td colSpan='2'><hr style='margin:10px 0;' /></td></tr>
				
				<tr>
					<td>&nbsp;</td>
					<td><input type='submit' name='submit' value='Save My Profile' class='btn btn-large btn-warning'></td>
				</tr>
				
			</table>
		
		</form>
		
	</div>
	
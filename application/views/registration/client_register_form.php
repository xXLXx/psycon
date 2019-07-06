<script src='https://www.google.com/recaptcha/api.js'></script>

	<div class='content_area'>
	
		<h2>Registration</h2>
		
		<div style='font-style:italic;'>Please enter the following information. Your information will be kept completely confidential. </div>
		
		<div style='margin:15px 0 0;'>Required fields are indicated with a red asterisk (<span style='color:red;'>*</span>) </div>
		
		<hr />
		
		<form action='/register/submit' method='POST'>
		
			<table width='100%' cellPadding='10' cellSpacing='0'>
			
				<tr>
					<td style='width:150px;'><b>Email Address:</b> <span style='color:red;'>*</span></td>
					<td><input type='text' name='email' value='<?=set_value('email')?>' disabled="disabled"></td>
				</tr>
				
				<tr>
					<td style='width:150px;'><b>Username:</b> <span style='color:red;'>*</span></td>
					<td><input type='text' name='username' value='<?=set_value('username')?>' disabled="disabled"></td>
				</tr>
				
				<tr>
					<td style='width:150px;'><b>Password:</b> <span style='color:red;'>*</span></td>
					<td><input type='password' name='password' value='<?=set_value('password')?>' disabled="disabled"></td>
				</tr>
				
				<tr>
					<td style='width:150px;'><b>Re-Type Password:</b> <span style='color:red;'>*</span></td>
					<td><input type='password' name='password2' value='<?=set_value('password2')?>' disabled="disabled"></td>
				</tr>
				
				<tr>
					<td style='width:150px;'><b>First Name:</b> <span style='color:red;'>*</span></td>
					<td><input type='text' name='first_name' value='<?=set_value('first_name')?>' disabled="disabled"></td>
				</tr>
			
				<tr>
					<td style='width:150px;'><b>Last Name:</b> <span style='color:red;'>*</span></td>
					<td><input type='text' name='last_name' value='<?=set_value('last_name')?>' disabled="disabled"></td>
				</tr>
				
				<tr>
					<td style='width:150px;'><b>Gender:</b> <span style='color:red;'>*</span></td>
					<td><div><input type='radio' name='gender' value='Male' <?=set_radio('gender','Male')?> disabled="disabled"> Male &nbsp; &nbsp; <input type='radio' name='gender' value='Female' <?=set_radio('gender','Female')?> disabled="disabled"> Female</div></td>
				</tr>
				
				<tr>
					<td style='width:150px;'><b>Date of Birth:</b> <span style='color:red;'>*</span></td>
					<td><?=$this->system_vars->dob_custom('dob', set_value('dob_month'), set_value('dob_day'), set_value('dob_year'))?></td>
				</tr>
				
				<tr>
					<td style='width:150px;'><b>Country:</b> <span style='color:red;'>*</span></td>
					<td><?=$this->system_vars->country_array_select_box('country', set_value('country'))?></td>
				</tr>
				
				<tr>
					<td style='width:150px;'><b>Captcha:</b> <span style='color:red;'>*</span></td>
					<td><div class="g-recaptcha" data-sitekey="6LceAxATAAAAAJyJPqyNm-ewf0sroy1fI8_THSYb"></div></td>
				</tr>
								
				<tr><td colSpan='2'><hr style='margin:10px 0;' /></td></tr>
				
				<tr>
					<td>&nbsp;</td>
					<td>
						<table width='300'>
							<tr>
								<td valign='top' width='10'><input type='checkbox' name='newsletter' value='1' <?=set_checkbox('newsletter','1',TRUE)?>></td>
								<td><div>I want to receive the newsletter. (Get coupons and special promotions) </div></td>
							</tr>
						</table>
					 </td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td>
						<table width='300'>
							<tr>
								<td valign='top' width='10'><input type='checkbox' name='terms' value='1' <?=set_checkbox('terms','1')?>></td>
								<td><div>I have read and agreed to all the Member <a href='/terms' target='_blank'>Terms and Conditions</a> <span style='color:red;'>*</span></div></td>
							</tr>
						</table>
					</td>
				</tr>
				
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="Register" class='btn btn-primary btn-large' disabled="disabled"></td>
				</tr>
				
			</table>
		
		</form>
		
	</div>
	<script src='https://www.google.com/recaptcha/api.js'></script>

	<div class='content_area'>
	
		<h2>Contact Us</h2>
		
		<p>Please use the form below to contact us. We will respond in a timely manner.</p>
		
		<hr />
		
		<form action='/contact/submit' method='POST' style='margin:15px 0 0;padding-right:10px;'>
			
			<table cellPadding='10'>
			
				<tr>
					<td><b>Your Name:</b></td>
					<td><input type='text' name='name' value='<?=set_value('name')?>'></td>
				</tr>
				
				<tr>
					<td><b>Your Email Address:</b></td>
					<td><input type='text' name='email' value='<?=set_value('email')?>'></td>
				</tr>
				
				<tr>
					<td><b>Your Phone Number:</b></td>
					<td><input type='text' name='phone' value='<?=set_value('phone')?>'></td>
				</tr>
				
				<tr>
					<td><b>Your Username:</b><div class='caption'>(If you have one)</div></td>
					<td><input type='text' name='username' value='<?=set_value('username')?>'></td>
				</tr>
				
				<tr>
					<td><b>Subject:</b></td>
					<td>
					
						<select name="subject">
							<option value=''>Subject</option>
							<option value="General Help"<?=set_select('subject','General Help')?>>General Help</option>
							<option value="Report Abuse"<?=set_select('subject','Report Abuse')?>>Report Abuse</option>
							<option value="Report Page Error"<?=set_select('subject','Report Page Error')?>>Report Page Errors</option>
							<option value="Suggestions"<?=set_select('subject','Suggestions')?>>Suggestions</option>
							<option value="Chat Question"<?=set_select('subject','Chat Question')?>>Chat Question</option>
							<option value="Affiliate Question"<?=set_select('subject','Affiliate Question')?>>Affiliate Question</option>
							<option value="Become An Expert"<?=set_select('subject','Become An Expert')?>>Become A Expert Question</option>
							<option value="Other, No Listed"<?=set_select('subject','Other, No Listed')?>>Other, Not Listed</option>
						</select>
					
					</td>
				</tr>
				
				<tr>
					<td><b>Comments / Questions:</b></td>
					<td><textarea name='comments' rows='10' cols='50'><?=set_value('comments')?></textarea></td>
				</tr>
				
				<tr>
					<td valign='top'><b>Enter Security Code:</b></td>
					<td>
					
						<div class="g-recaptcha" data-sitekey="6LcvDwUTAAAAAAtY0k6kwIMbxMAudjsF5B4-pTFJ"></div>
					
					</td>
				</tr>
				
				<tr>
					<td><b>&nbsp;</b></td>
					<td><input type='submit' name='submit' value='Send Inqury'></td>
				</tr>
			
			</table>
			
		</form>
		
	</div>
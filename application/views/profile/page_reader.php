	<script src='https://www.google.com/recaptcha/api.js'></script>

	<script src='/media/javascript/jqui/jquery-ui-1.8.16.custom.min.js'></script>
	<link rel="stylesheet" href="/media/javascript/jqui/css/overcast/jquery-ui-1.8.16.custom.css" />

	<script src='/media/javascript/datetime/jquery-ui-timepicker-addon.js'></script>
	<link rel="stylesheet" href="/media/javascript/datetime/jquery-ui-timepicker-addon.css" />
	
	<style>
	
		.ui-widget{ font-size:0.9em; }
		.datetime{ cursor: pointer; background:none !important; border:none; color:blue; text-decoration:underline; }
	
	</style>

	<script>
	
		$(document).ready(function()
		{
		
			$('.dtime').datetimepicker
			({
				ampm: true,
				separator: ' @ '
			});
		
			$("input[name='when']").click(function()
			{
			
				if($(this).val()=='now')
				{
				
					$('#later_tbody').css('display','none');
				
				}
				else
				{
				
					$('#later_tbody').css('display','');
				
				}
			
			});
		
		});
	
	</script>

	<div class='content_area'>
	
		<? $this->load->view('profile/badge'); ?>
		
		<h2>Page <?=$this->reader->data['username']?></h2>
		
		<p style='padding:5px 0 0;'>Please fill out this request form and click the submit button. The Reader will be notified immediately and will either contact you via our email system, or they will log on.</p>
		
		<div>&nbsp;</div>
		
		<form action='/profile/<?=$this->reader->data['username']?>/page_reader_submit' method='POST'>
		
			<table cellPadding='10'>
		
				<tr>
					<td width='250'><b>Your Name:</b></td>
					<td><?=$this->member->data['first_name']?> <?=$this->member->data['last_name']?></td>
				</tr>
				
				<tr>
					<td width='250'><b>Your Email Address:</b></td>
					<td><?=$this->member->data['email']?></td>
				</tr>
				
				<tr>
					<td width='250'><b>When do you want to chat?</b></td>
					<td><input type='radio' name='when' value='now' checked style='margin:0;'> In the next 15 minutes &nbsp; &nbsp; <input type='radio' name='when' value='later' style='margin:0;'> Later</td>
				</tr>
				
				<tbody id='later_tbody' style='display:none;'>
				
					<tr>
						<td width='250'><b>Current Date & Time:</b></td>
						<td><?=date("m/d/Y @ h:i A")?></td>
					</tr>
					
					<tr>
						<td width='250'><b>Select A Date & Time:</b></td>
						<td><input type='text' name='date1' value='<?=date("m/d/Y @ h:i A", strtotime("+1 hour"))?>' class='dtime'></td>
					</tr>
					
					<tr>
						<td width='250'><b>Select Alternative Date & Time:</b></td>
						<td><input type='text' name='date2' value='<?=date("m/d/Y @ h:i A", strtotime("+2 hours"))?>' class='dtime'></td>
					</tr>
				
				</tbody>
				
				<tr>
					<td width='250' valign='top'><b>Your Comments:</b></td>
					<td><textarea name='comments' style='width:600px;height:125px;'><?=set_value('comments')?></textarea></td>
				</tr>

				<tr>
					<td width='250' valign='top'><b>Captcha:*</b></td>
					<td><div class="g-recaptcha" data-sitekey="6LcvDwUTAAAAAAtY0k6kwIMbxMAudjsF5B4-pTFJ"></div></td>
				</tr>
				


				<tr>
					<td width='250' valign='top'>&nbsp;</td>
					<td><input type='submit' name='submit' class='btn btn-primary btn-large' value='Submit'></td>
				</tr>
				
			</table>	
		
		</form>
	
	</div>
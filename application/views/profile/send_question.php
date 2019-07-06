
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
		
			$('.datetime').datetimepicker
            ({
            	ampm: true,
				separator: ' @ '
            });
		
			$("input[name='deadline']").click(function()
			{
			
				if($(this).is(':checked'))
				{
				
					$('#div_deadline').show();
				
				}
				else
				{
				
					$('#div_deadline').hide();
				
				}
			
			});
		
		});
	
	</script>

	<div class='content_area'>
	
		<? $this->load->view('profile/badge'); ?>
		
		<h2>Request An Email Reading</h2>
		
		<p style='padding:5px 0 0;'>Using this form, you will now be able to place your order for an Email Reading. Pricing is based on how many questions you will be asking the reader.</p>
		
		<div>&nbsp;</div>
		
		<form action='/profile/<?=$this->reader->data['username']?>/submit_question' method='POST'>
		
			<table cellPadding='10'>
		
				<tr>
					<td width='250'><b>Your Name:</b></td>
					<td><?=$this->member->data['first_name']?> <?=$this->member->data['last_name']?></td>
				</tr>
				
				<tr>
					<td width='250'><b>Your Date of Birth:</b></td>
					<td><?=date("F d, Y", strtotime($this->member->data['dob']))?></td>
				</tr>
				
				<tr>
					<td width='250'><b>What time were you born?</b></td>
					<td><input type='text' name='birth_time' value='<?=set_value('birth_time', $this->session->userdata('birth_time'))?>' /></td>
				</tr>
				
				<tr>
					<td width='250'><b>Your Place of Birth:</b><div class='caption'>(City, State & Country)</div></td>
					<td><input type='text' name='birth_place' value='<?=set_value('birth_place', $this->session->userdata('birth_place'))?>' /></td>
				</tr>
				
				<tr>
					<td width='250'><b>Select an Email Reading Package:</b></td>
					<td>
					<select name='package'>
						<option value=''>Select A Package</option>
						<?
						
							foreach($packages as $p)
							{
								
								echo "<option value='{$p['id']}'".set_select('package', $p['id'], ($this->session->userdata('package')==$p['id'] ? TRUE : FALSE)).">{$p['title']} @ $".number_format($p['price'], 2)."</option>";
							
							}
						
						?>
					</select></td>
				</tr>
				
				<tr>
					<td width='250' valign='top'><b>Ask all the questions you have:</b></td>
					<td><textarea name='questions' style='width:600px;height:250px;'><?=set_value('questions', $this->session->userdata('questions'))?></textarea></td>
				</tr>
				
				<tr>
					<td width='250' valign='top'><b>Special Instructions:</b></td>
					<td><textarea name='instructions' style='width:600px;height:125px;'><?=set_value('instructions', $this->session->userdata('instructions'))?></textarea></td>
				</tr>
				
				<tr>
					<td width='250' valign='top'><b>Additional Information:</b></td>
					<td><textarea name='additional_info' style='width:600px;height:125px;'><?=set_value('additional_info', $this->session->userdata('additional_info'))?></textarea></td>
				</tr>
				
				<tr>
					<td width='250' valign='top'>&nbsp;</td>
					<td><input type='submit' name='submit' class='btn btn-primary btn-large' value='Preview & Checkout'></td>
				</tr>
				
			</table>	
		
		</form>
	
	</div>
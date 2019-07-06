
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
	
		<? /*$this->load->view('profile/badge');*/ ?>
		
		<div align='center'>
			<h2>Preview & Submit Your Email Reading Request</h2>
			<p style='padding:5px 0 0;'>Check the details below of your email reading request to verify they are accurate. To make any changes, please click the edit button.</p>
		</div>
		
		<hr />
		
		<div>
		
			<div class='pull-left' style='width:650px;'>
			
				<div><b>Your Name:</b></div>
				<div style='padding-bottom:25px;'><?=$this->member->data['first_name']?> <?=$this->member->data['last_name']?></div>
				
				<div><b>Date of Birth:</b></div>
				<div style='padding-bottom:25px;'><?=date("F d, Y", strtotime($this->member->data['dob']))?></div>
				
				<div><b>Birth Time:</b></div>
				<div style='padding-bottom:25px;'><?=$this->session->userdata('birth_time')?></div>
				
				<div><b>Package:</b></div>
				<div style='padding-bottom:25px;'><?=$title?> @ $<?=number_format($price, 2)?></div>
				
				<div><b>Question(s):</b></div>
				<div style='padding-bottom:25px;'><?=nl2br($this->session->userdata('questions'))?></div>
				
				<div><b>Special Instructions:</b></div>
				<div style='padding-bottom:25px;'><?=nl2br($this->session->userdata('instructions'))?></div>
				
				<div><b>Additional Information:</b></div>
				<div style='padding-bottom:25px;'><?=nl2br($this->session->userdata('additional_info'))?></div>
				
			</div>
			<div class='pull-right' style='width:200px;text-align:center;'>
			
				<div class='well'>
				<div style='padding-bottom:25px;'>
				
					<div><b>Order Total:</b></div>
					<div>$<?=number_format($price, 2)?></div>
				
				</div>
				
				<div style='padding-bottom:25px;'>
				
					<div><b>Estimated Completion:</b></div>
					<div>
					<?
					
						$totalDays = number_format(($total_questions*$this->reader->data['email_total_days']));
						
						if($totalDays > 3) $totalDays = 3;
						
						$dateOfCompletion = date("m/d/Y", strtotime("+{$totalDays} days"));
						
						echo $totalDays . " Days - {$dateOfCompletion}";
					
					?>
					</div>
				
				</div>
			
				<div style='padding-bottom:25px;'><a href='/profile/<?=$this->reader->data['username']?>/confirm_question' onClick="Javascript:return confirm('You are about to submit an email reading request. Your account will be charged immediately. Do you want to continue?');" class='btn btn-warning btn-large'>Confirm Order</a></div>
				<div style='padding-bottom:25px;'><a href='/profile/<?=$this->reader->data['username']?>/submit_question' class='btn'>Edit</a></div>
				</div>
				
			</div>
			<div class='clearfix'></div>
		
		</div>
		
		<div>&nbsp;</div>
		
	</div>
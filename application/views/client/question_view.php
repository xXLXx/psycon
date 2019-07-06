
	<div class='content_area'>

		<h2 style='padding-bottom:0 !important;margin-bottom:0 !important;'>View Question</h2>
		
		<hr />
		
			<div><b>Client:</b></div>
			<div style='padding-bottom:25px;'><?=$client['first_name']." ".$client['last_name']?></div>
			
			<div><b>Title:</b></div>
			<div style='padding-bottom:25px;'><?=$title?></div>
			
			<div><b>Question:</b></div>
			<div style='padding-bottom:25px;'><?=nl2br($question)?></div>
			
			<?
			
				if(trim($answer))
				{
				
					echo "
					<div class='well'>
					<a href='/client/experts/leave_review/questions/{$id}' class='btn btn-warning pull-right'>Leave A Review</a>
					<div><b>Answer:</b></div>
					<div style='padding-bottom:25px;'>".nl2br($answer)."</div>
					</div>
					";
				
				}
			
			?>
			
			<div><b>Deadline:</b></div>
			<div style='padding-bottom:25px;'><?=(isset($deadline) ? date("F d, Y @ h:i A")." EST" : "No Deadline")?></div>
			
			<div><b>Status:</b></div>
			<div><?=ucfirst($status)?></div>
			
			<?
			
				if(trim($decline_reason))
				{
				
					echo "
					<div style='padding:25px 0 0;'><b>Declined Reason:</b></div>
					<div>".nl2br($decline_reason)."</div>
					";
				
				}
			
			?>
		
			<?
			
				switch($status)
				{
					
					case "unaccepted":
					
						echo "
						<div>&nbsp;</div>
						
						<div class='well'>
						
							<h3 style='margin-top:0;'>Do you want to accept this bid?</h3>
							<p>The expert placed their bid for your question, you can either accept or decline the message using the buttons below.</p>
							
							<hr />
							
							<table cellPadding='10'>
								
								<tr>
									<td><b>Bid Amount:</b></td>
									<td>$ ".number_format($bid_amount, 2)."</td>
								</tr>
								
								<tr>
									<td><b>Your Account Balance:</b></td>
									<td>$ ".number_format($this->system_vars->member_balance($this->session->userdata('member_logged')), 2)."</td>
								</tr>
								
								<tr>
									<td><b>Timeframe:</b></td>
									<td>{$timeframe}</td>
								</tr>
								
							</table>
							
							<hr />
							
							<div style='padding:10px;'>
								<a href='/client/questions/accept/{$id}' class='btn btn-warning pull-left' onClick=\"Javascript:return confirm('Accepting this bid will automatically deduct the bid total from your available balance. Are you sure you want to continue?');\">Accept Bid</a>
								<a href='/client/questions/decline/{$id}' class='btn btn-link pull-right' onClick=\"Javascript:return confirm('Are you sure you want to decline this bid?');\">Decline Bid</a>
								<div class='clearfix'></div>
							</div>
								
						</div>
						";
						
					break;
					
				}
			
			?>
		
		<div>&nbsp;</div>
	
	</div>
	
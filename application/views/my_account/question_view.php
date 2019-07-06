
	<div class="horizontal_bar">
		<a href="/my_account/questions">Back To Questions</a>
	</div>

	<div class='padded'>

		<h2>View Question</h2>
		
		<div>&nbsp;</div>
		
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
					<div><b>Answer:</b></div>
					<div style='padding-bottom:25px;'>".nl2br($answer)."</div>
					";
				
				}
			
			?>
			
			<div><b>Deadline:</b></div>
			<div style='padding-bottom:25px;'><?=(isset($deadline) ? date("F d, Y @ h:i A", strtotime($deadline))." EST" : "No Deadline")?></div>
			
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
				
					case "new":
					
						echo "
						<hr />
						
						<form action='/my_account/questions/submit_bid/{$id}' method='POST'>
						
							<h3>Submit Your Bid</h3>
							
							<div style='padding-bottom:15px;'>If you want to answer this question, use the form below to submit your bid. If you do NOT want to answer this question, decline it using the options below the form.</div>
							
							<table cellPadding='10'>
							
								<tr>
									<td width='100'><b>Client Budget:</b></td>
									<td width='5' align='right'></td>
									<td>{$price_range}</td>
								</tr>
								
								<tr>
									<td width='100'><b>Price:</b></td>
									<td width='5' align='right'>$</td>
									<td><input type='text' style='width:35px;' name='price' value='".set_value('price')."'></td>
								</tr>
								
								<tr>
									<td width='100'><b>Timeframe:</b></td>
									<td></td>
									<td>
									
										<select name='timeframe'>
											<option value=''></option>
											<option".set_select("timeframe", "Within 24 hours").">Within 24 hours</option>
											<option".set_select("timeframe", "1-2 days").">1-2 days</option>
											<option".set_select("timeframe", "3-4 days").">3-4 days</option>
											<option".set_select("timeframe", "7 days").">7 days</option>
										</select>
										
									</td>
								</tr>
								
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td><input type='submit' name='submit' value='Submit My Bid' class='btn btn-warning'></td>
								</tr>
							
							</table>
						
						</form>
						
						<hr />
						
						<h3>Decline This Question</h3>
						
						<div style='padding:10px 0 5px;'><a href='/my_account/questions/decline/{$id}/busy' onClick=\"Javascript:return confirm('Are you sure you want to decline this question?');\">Too busy at this time</a></div>
						<div><a href='/my_account/questions/decline/{$id}/unqualified' onClick=\"Javascript:return confirm('Are you sure you want to decline this question?');\">I am not qualified to answer this question</a></div>
						";
					
					break;
					
					case "pending":
					
						echo "
						<hr />
						
						<form action='/my_account/questions/submit_answer/{$id}' method='POST'>
						
							<h3>Submit Your Answer</h3>
							
							<div style='padding-bottom:15px;'>The client has accepted your bid. Please answer the question using the form below. Please keep their deadline in mind.</div>
							
							<div><b>Your Answer:</b></div>
							<div><textarea style='width:90%;' name='answer' rows='10'>".set_value('answer')."</textarea></div>
							
							<div style='padding:15px 0 0;'><input type='submit' name='submit' value='Submit My Answer' class='btn btn-warning'></div>
						
						</form>
						";
						
					break;
					
				}
			
			?>
		
		<div>&nbsp;</div>
	
	</div>
	
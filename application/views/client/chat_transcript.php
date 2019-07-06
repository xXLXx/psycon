
	<style>
	
		.expert_name
		{
			color: #2850A8;
			display: block;
			font-size: 14px;
			font-weight: bold;
			padding: 10px 0 3px;
			text-decoration: underline !important;
		}
	
	</style>

	<div class='content_area'>
		
		<h2>Chat Transcript</h2>
		
		<div class='well'>
		
			<table width='100%' cellPadding='10'>
				
				<tr>
					<td><b>Date:</b></td>
					<td><?=date("m/d/Y h:i A", strtotime($start_datetime))?></td>
				</tr>
				
				<tr>
					<td><b>Chat Length:</b></td>
					<td>
					<?
					
						$chat_time = $this->system_vars->seconds_to_minutes($length);
						
						echo "{$chat_time['minutes']} Minutes & {$chat_time['seconds']} Seconds";
					
					?>
					</td>
				</tr>
				
				<tr>
					<td><b>Expert:</b></td>
					<td><a href='/profile/view/<?=$profile_id?>'><?=$expert['username']?></a></td>
				</tr>
				
				<?
				
					if($refund)
					{
					
						echo "
						<tr><td colSpan='2'><hr /></td></tr>
						
						<tr>
							<td><b>Refund Amount:</b></td>
							<td>$ ".number_format($refund['amount'], 2)."</td>
						</tr>
						
						<tr>
							<td valign='top'><b>Refund Reason:</b></td>
							<td>".nl2br($refund['details'])."</td>
						</tr>
						
						<tr>
							<td><b>Refund Status:</b></td>
							<td>".ucfirst($refund['status'])."</td>
						</tr>";
						
						if($refund['status']=='rejected')
						{
						
							echo "
							<tr>
								<td><b>Rejection Reason:</b></td>
								<td>".nl2br($refund['rejected_reason'])."</td>
							</tr>
							";
						
						}
					
					}
				
				?>
				
			</table>
			
			<? if(!$refund): ?>
			
				<hr />
				
				<div align='center'>
					<a href='/client/chats/request_refund/<?=$id?>' class='btn btn-link'>Request Refund</a>
					<a href='/client/experts/leave_review/chat/<?=$session_id?>' class='btn btn-link'>Leave Feedback</a>
				</div>
			
			<? endif; ?>
			
		</div>
		
		<h2>Transcript</h2>
		
		<?
		
			foreach($transcript as $t)
			{
			
				echo "<div style='padding-bottom:10px;border-bottom:solid 1px #E0E0E0; margin-bottom:10px;'>{$t['message']}</div>";	
			
			}
		
		?>
		
		<div>&nbsp;</div>
	
	</div>

	<div class='content_area'>

		<h2>Email Reading Details</h2>
		
		<hr />
		
		<div class='pull-right well' style='width:300px;'>
			<table width='100%' cellPadding='10'>
				
				<tr>
					<td><b>Expert:</b></td>
					<td><a href='/profile/<?=$expert['username']?>'><?=strtoupper($expert['username'])?></a></td>
				</tr>
				
				<tr>
					<td><b>Date/Time:</b></td>
					<td><?=date("m/d/y @ h:i A", strtotime($datetime))?></td>
				</tr>
				
				<tr>
					<td><b>Status:</b></td>
					<td><?=($status=='new' ? "<span class='label label-important'>Waiting For Reading</span>" : "<span class='label label-success'>Complete</span>")?></td>
				</tr>
				
				<tr>
					<td><b>Total Cost:</b></td>
					<td>$<?=number_format($price, 2)?></td>
				</tr>
				
				<tr>
					<td><b>Completion Date:</b></td>
					<td><?=date("m/d/Y", strtotime($completion_date))?></td>
				</tr>
				
			</table>
		</div>
		
		<div><b>Package Ordered:</b></div>
		<div style='padding-bottom:25px;'><?=$package_title?></div>
		
		<div><b>Total Questions Allowed:</b></div>
		<div style='padding-bottom:25px;'><?=$total_questions?></div>
		
		<div><b>Your Name:</b></div>
		<div style='padding-bottom:25px;'><?=$name?></div>
		
		<div><b>Date of Birth:</b></div>
		<div style='padding-bottom:25px;'><?=date("F d, y", strtotime($dob))?></div>
		
		<div><b>Birth Time:</b></div>
		<div style='padding-bottom:25px;'><?=$birth_time?></div>
		
		<div><b>Birth Place:</b></div>
		<div style='padding-bottom:25px;'><?=$birth_place?></div>
		
		<div><b>Question(s):</b></div>
		<div style='padding-bottom:25px;'><?=nl2br($questions)?></div>
		
		<div><b>Special Instructions:</b></div>
		<div style='padding-bottom:25px;'><?=nl2br($instructions)?></div>
		
		<div><b>Additional Information:</b></div>
		<div style='padding-bottom:25px;'><?=nl2br($additional_info)?></div>
		
		<hr />
		
		<?
		
			if($messages)
			{
			
				echo "<h2 style='margin-bottom:10px;'>Messages</h2>";
				
				foreach($messages as $m)
				{
				
					$textColor = "#000";
					$dateColor = "#666";
					
					if($m['member_id']==$this->member->data['id'])
					{
					
						$textColor = "#C0C0C0";
						$dateColor = "#C0C0C0";
					
					}
				
					echo "
					<div style='margin-bottom:20px;'>
						<div style='color:{$dateColor};'><b>From:</b> {$m['username']} <b>on</b> ".date("m/d/y @ h:i A", strtotime($m['datetime']))."</div>
						<div style='color:{$textColor};'>".nl2br($m['message'])."</div>
					</div>
					";
				
				}
				
				echo "<hr />";
			
			}
		
		?>
		
		<form action='/my_account/email_readings/send_message/<?=$id?>' method='POST'>
			<div style='padding:0 0 10px 0;'><b>Post A Message To This Thread:</b></div>
			<div><textarea style='width:90%;height:150px;' name='message'></textarea></div>
			<div align='center' style='padding:10px 0 0;text-align:left;'><input type='submit' name='submit' value='Send Message' class='btn' /></div>
		</form>
		
	</div>
	
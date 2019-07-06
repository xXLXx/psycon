
	<div style='padding-bottom:25px;'>
		<h2 style='margin-bottom:0;padding-bottom:0'>Email Readings</h2>
		<div style='padding:15px 0 0;font-size:12px;color:#666;'>To modify your "Default Reading Settings", visit your "Edit My Expert Profile" page by <a href='/my_account/main/edit_profile'>clicking here</a>.</div>
	</div>
	
	<ul class="nav nav-tabs">
		<li><a href="/my_account/email_readings/open_requests">Open Email Requests</a></li>
		<li><a href="/my_account/email_readings/closed_requests">Closed Email Requests</a></li>
		<li class='active'><a href="/my_account/email_readings/email_specials">My Email Specials</a></li>
		<li><a href="/my_account/email_readings/new_special"><span class='icon icon-tag'></span> Create A New Special</a></li>
	</ul>
	
	<?
	
		if($specials)
		{

			echo "<table class='table table-hover table-striped'>
				
				<thead>
					
					<tr>
						<th>Title:</th>
						<th>Questions Allowed:</th>
						<th>Total Credits:</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
					
				</thead>
				
				<tbody>
			";
	
			foreach($specials as $s)
			{
			
				echo "
				<tr>
					<td>{$s['title']}</td>
					<td>{$s['total_questions']}</td>
					<td>{$s['price']}</td>
					<td style='width:75px;text-align:right;'><a href='/my_account/email_readings/edit_special/{$s['id']}' class='btn btn-mini'>Edit</a></td>
					<td style='width:40px;text-align:right;'><a href='/my_account/email_readings/delete_special/{$s['id']}' onClick=\"Javascript:return confirm('Are you sure you want to delete this email special?');\" class='btn btn-mini'>&nbsp;<span class='icon-trash'></span> &nbsp;</a></td>
				</tr>
				";
			
			}
			
			echo "</tobdy>
			</table>";
		
		}
		else
		{
		
			echo "<p>You have not added any email specials. To create your own email specials, click the \"Create A New Special\" tab at the top or <a href='/my_account/email_readings/new_special'>click here</a>.</p>";
		
		}
	
	?>
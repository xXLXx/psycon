
	<div style='padding-bottom:25px;'>
		<h2 style='margin-bottom:0;padding-bottom:0'>Email Readings</h2>
		<div style='padding:15px 0 0;font-size:12px;color:#666;'>To modify your "Default Reading Settings", visit your "Edit My Pyschic Profile" page by <a href='/my_account/main/edit_profile'>clicking here</a>.</div>
	</div>
	
	<ul class="nav nav-tabs">
		<li <?=$open ? "class='active'" : ""?>><a href="/my_account/email_readings/open_requests">Open Email Requests</a></li>
		<li <?=!$open ? "class='active'" : ""?>><a href="/my_account/email_readings/closed_requests">Closed Email Requests</a></li>
		<li><a href="/my_account/email_readings/email_specials">My Email Specials</a></li>
		<li><a href="/my_account/email_readings/new_special"><span class='icon icon-tag'></span> Create A New Special</a></li>
	</ul>
	
	<?
	
		if($emails)
		{
		
			echo "<table class='table table-striped table-hover'>
			
			<thead>
				<tr>
					<th>Package Ordered / Date:</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			
			<tbody>";
		
			foreach($emails as $e)
			{
			
				echo "
				<tr>
					<td style='vertical-align:middle;'><div><b>{$e['package_title']}</b></div>".date("m/d/Y @ h:i A", strtotime($e['datetime']))."</td>
					<td style='vertical-align:middle;text-align:right;width:60px;'><a href='/my_account/email_readings/reader_view/{$e['id']}' class='btn'>View</a></td>
				</tr>
				";
			
			}
			
			echo "</tbody></table>";
		
		}
		else
		{
		
			echo "<div>".($open ? "You do not have any open email requests" : "You do not have any closed email requests")."</div>";
		
		}
	
	?>
	
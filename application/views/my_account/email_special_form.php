
	<div style='padding-bottom:25px;'>
		<h2 style='margin-bottom:0;padding-bottom:0'>Email Readings</h2>
		<div style='padding:15px 0 0;font-size:12px;color:#666;'>To modify your "Default Reading Settings", visit your "Edit My Expert Profile" page by <a href='/my_account/main/edit_profile'>clicking here</a>.</div>
	</div>
	
	<ul class="nav nav-tabs">
		<li><a href="/my_account/email_readings/open_requests">Open Email Requests</a></li>
		<li><a href="/my_account/email_readings/closed_requests">Closed Email Requests</a></li>
		<li><a href="/my_account/email_readings/email_specials">My Email Specials</a></li>
		<li class='active'><a href="/my_account/email_readings/new_special"><span class='icon icon-tag'></span> Create A New Special</a></li>
	</ul>
	
	<form action='<?=$form_action?>' method='POST' style='padding:15px 0 0;'>	
	
		<table width='100%' cellPadding='10'>
		
			<tr>
				<td width='200'><b>Title / Short Description:</b><div>50 Characters Maximum</div></td>
				<td><input type='text' name='title' value='<?=set_value('title', $title)?>' class='input-xlarge'></td>
			</tr>
			
			<tr>
				<td><b>How many questions will you allow for this special?</b></td>
				<td>
					<input type='text' name='total_questions' value='<?=set_value('total_questions', $total_questions)?>' class='input-mini'>
				</td>
			</tr>
			
			<tr>
				<td><b>How much do you want to charge?</b></td>
				<td>$ <input type='text' name='price' value='<?=set_value('price', $price)?>' class='input-mini'></td>
			</tr>
			
			<tr>
				<td><b>&nbsp;</b></td>
				<td><input type='submit' name='submit' value='Save Special' class='btn btn-primary btn-large'></td>
			</tr>
		
		</table>
	
	</form>
	
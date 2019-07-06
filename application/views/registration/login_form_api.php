	
	<div class='content_area'>

		<form method="post" action="/api/login_submit/<?=$site_id?>" id="loginform">
			
			<div align='center'>
			
				<h1>Sign In To Connect Your Account</h1>
				
				<p>Use the form below to connect your Psychic Contact account with <strong><?=$site_name?></strong>. After you login, you will be redirected back.</p>
				
				<hr />
					
				<table cellPadding='10'>
				
					<tr>
						<td>Username:</td>
						<td><input type="text" name="username" value='<?=set_value('username')?>'></td>
					</tr>
					
					<tr>
						<td>Password:</td>
						<td><input type="password" name="password" value='<?=set_value('password')?>'></td>
					</tr>
					
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" name="submit" value='Login' class='btn btn-primary'></td>
					</tr>
				
				</table>
				
				<hr />
				
				<a href='<?=$cancel_url?>'>Cancel</a>
			
			</div>
					
		</form>
		
	</div>
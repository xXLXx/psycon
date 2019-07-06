<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>vAdministrator : OWWS</title>
	<link rel="stylesheet" href="/media/vadmin_assets/css/general.css" />
	<style>
	
		.login_box{ margin:100px 0 0; padding:50px; text-align: left; width:600px; }
		
		.login_box h1{ padding:0 0 25px 0; }
		.login_box .form_wrapper{ padding:25px 0; }
		.login_box .form_wrapper .form_title{ float:left; width:115px; padding:10px 0 0; font-weight: bold; font-size: 12px; color:#486a9a; font-family: Arial; }
		.login_box .form_wrapper .form_field{ float:left; width:450px; }
		.login_box .form_wrapper .form_field .tb{ width:100%; border:solid 1px #bbbbbb; padding:10px; }
		
		
	</style>
</head>
<body>

	<div align='center' id='wrapper'>
	
		<?
		
			if(validation_errors()) echo validation_errors('<div class=\'errors\'>','</div>');
			if($this->error) echo '<div class=\'errors\'>'.$this->error.'</div>';
		
		?>
	
		<form action='/vadmin/login/submit' method='POST' class='white_box login_box'>
		
			<h1>Login To vAdministrator</h1>
			
			<div class='login_form'>
			
				<div class='form_wrapper'>
					<div class='form_title'>Username:</div>
					<div class='form_field'><input type='text' name='username' autocomplete="off" class='tb' value='<?=set_value('username')?>'></div>
					<div class='clear'></div>
				</div>
				
				<div class='form_wrapper'>
					<div class='form_title'>Password:</div>
					<div class='form_field'><input type='password' name='password' autocomplete="off" class='tb' value='<?=set_value('password')?>'></div>
					<div class='clear'></div>
				</div>
				
				<div class='form_wrapper'>
					<div class='form_title'>&nbsp;</div>
					<div class='form_field'><input type='submit' name='submit' value='Login To Administrator' class='blue_button'></div>
					<div class='clear'></div>
				</div>
				
			</div>
		
		</form>
	
	</div> <!-- wrapper -->

</body>
</html>
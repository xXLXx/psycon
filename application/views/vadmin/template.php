<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>vAdministrator : OWWS</title>
	<link rel="stylesheet" href="/media/vadmin_assets/css/general.css" />
	<style>
	
		#main{ margin:25px 0 25px; }
		#logo_container{ text-align: center; }
		
		#nav_left{ margin:0; padding:0; }
		#nav_left li{ list-style: none; }
		#nav_left li a{ display: block; text-decoration: none; margin:10px 0 0; font-family: Arial; padding:15px 0 0 10px; height:32px; text-align: left; }
		
		.submenu{ margin:10px 0 0; padding:0 10px; }
		.submenu li{ margin:0; padding:0 0 0 15px; background-image: url(/media/vadmin_assets/images/barr.png); background-repeat: no-repeat; background-position: 0 3px; }
		.submenu li a{ color:#486a9a; text-decoration: none; font-size:14px; font-weight: bold; font-family: Arial; margin:15px 0 !important; padding:0 !important; height:auto !important; }
		.submenu li a:hover{ color:#000; }
		
		#header_div{ font-family: Arial; font-size: 12px; }
		#header_div .links a{ text-decoration: none; color:#486a9a; font-size:12px; margin:0 10px; }
		#header_div .links a:hover{ color:#000; }
		
		.page_title{ color:#486a9a; font-family: Arial; }
		
	</style>
</head>
<body>

	<div align='center' id='wrapper'>
	
		<?
		
			if(validation_errors()) echo validation_errors('<div class=\'errors\'>','</div>');
			if($this->error) echo '<div class=\'errors\'>'.$this->error.'</div>';
			if($this->response) echo '<div class=\'responses\'>'.$this->response.'</div>';
		
		?>
	
		<div align='center' id='main'>
		
			<table width='95%' cellpadding="5" cellspacing="0" border="0">
			
				<tr>
					<td valign="top" width='230'>
					
						<div id='logo_container' class='white_box'>
							<img src='/media/vadmin_assets/images/logo.jpg' border='0' />
						</div>
						
						<ul id='nav_left'>
						
							<li><a href='/' class='blue_button'>Daily Deals</a>
							
								<ul class='submenu off_white_box'>
									<li><a href='/'>Active Deals</a></li>
									<li><a href='/'>Previous Deals</a></li>
									<li><a href='/'>Upcoming Deals</a></li>
									<li><a href='/'>Drafts</a></li>
									<li><a href='/'>Merchants</a></li>
								</ul>
							
							</li>
							<li><a href='/' class='blue_button'>Registered Members</a></li>
							<li><a href='/' class='blue_button'>Orders</a>
							
								<ul class='submenu off_white_box'>
									<li><a href='/'>Today's Orders</a></li>
									<li><a href='/'>Yesterday's Orders</a></li>
									<li><a href='/'>All Orders</a></li>
								</ul>
							
							</li>
							
						</ul> <!-- nav_left -->
					
					</td>
					<td valign="top">
					
						<div id='header_div' class='off_white_box'>
						
							<table width='100%' cellpadding="0" cellspacing="0">
							
								<tr>
									<td style='font-weight:bold;'>Welcome <?=$this->admin['name']?></td>
									<td align='right' class='links'>
									
										<a href='/'>Administrators</a>
										<a href='/'>Website Settings</a>
										<a href='/vadmin/main/logout' onclick="Javascript:return confirm('Are you sure you want to logout?');">Logout</a>
									
									</td>
									
									<td width='225' align='right'>
									
										<form action='' method='POST' style='margin:0;padding:0;'>
										
											<table cellPadding='2' cellspacing="0">
											
												<tr>
													<td><input type='text' name='query' value='' style='border:solid 1px #BBBBBB;padding:3px;'></td>
													<td><input type='submit' value='Search' class='blue_button' style='height:22px;'></td>
												</tr>
												
											</table>
											
										</form>
									
									</td>
								</tr>
								
							</table>
							
						</div> <!-- header_div -->
						
						<div id='main_content' class='white_box' style='margin:10px 0 0;'>
						
							<h1 class='page_title'>Daily Deals</h1>
						
						</div> <!-- main_content -->
					
					</td>
				</tr>
			
			</table>
		
		</div>
	
	</div> <!-- wrapper -->

</body>
</html>
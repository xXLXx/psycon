
	<h2>TrancePad</h2>
	
	<p style='padding-top:10px;'> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi rutrum purus at lorem ultrices eu laoreet sem porttitor. Integer at sapien arcu. Nunc diam dui, faucibus nec consequat vitae, viverra nec libero. Etiam enim libero, scelerisque vitae lobortis vel, rhoncus ut ipsum. Aliquam ac urna a risus laoreet pharetra et a nibh. Praesent nec velit tellus, ut facilisis odio. Morbi vulputate molestie turpis id venenatis. </p>
	
	<div style='font-size:16px; font-weight:bold; padding-top:20px;' align='center'>
	
		<?
		
			if($this->member->data['trancepad_enabled'] == '0')
			{
			
				echo "<a href='/my_account/trancepad/toggle/1' class='btn btn-large btn-primary'>Enable TrancePad on My Account</a>";
			
			}
			else
			{
			
				echo "<a href='/my_account/trancepad/toggle/0' class='btn btn-large btn-danger'>Disable TrancePad</a>";
			
			}
		
		?>
	
	</div>
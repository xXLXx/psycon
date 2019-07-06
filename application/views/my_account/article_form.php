
	<div align='center'>
	<h2>Submit An Article</h2>
	</div>
	
	<div align='center' style='margin:15px 0 0;'>
	
		<form action='/my_account/articles/submit_post' method='POST' enctype='multipart/form-data'>
		
			<p>Use the form below to upload your article. An administrator will review all articles and post them accordingly.</p>
		
			<hr />
			
			<div style='margin:15px 0 0;'>
				<div><b>Enter An Article Title:</b></div>
				<div style='margin:5px 0 0;'><input type='text' name='title' placeHolder='' class='input-xlarge' /></div>
			</div>
			
			<div style='margin:15px 0 0;'><b>Select An Article From Your Computer:</b></div>
			<div class='caption'>(Accepted formats: doc, rtf & txt)</div>
			
			<div style='margin:5px 0 0;'>
				<input type='file' name='file' />
			</div>
			
			<hr />
			
			<div style='margin:35px 0 0;'>
				<input type='submit' name='submit' value='Upload My Article' class='btn btn-large' />
			</div>
		
		</form>

	</div>
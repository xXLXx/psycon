<script>
function showFeaturedReader()
{
    var url = "/ajax_functions.php?mode=showFeaturedReader";
    
    $.ajax({    
    url: url,  
    cache: false,  
    success: function(html){ 
        $("#featuredReader").html(html);  
    }  
    });    
}

function showReaderList()
{
    var url = "/ajax_functions.php?mode=showReaderList&count=5";
    
    $.ajax({    
    url: url,  
    cache: false,  
    success: function(html){  
        $("#readersList").html(html);  
    }  
    });    
}


function OpenTestimonialsWindow(reader_id)
{
	url = "<?= SITE_URL ?>/modules/testimonials/show.php?reader_id="+reader_id;
	newwin = window.open(url, "testimonials", "scrollbars=yes,menubar=no,resizable=1,location=no,width=600,height=360,left=200,top=200");
	newwin.focus();
}


var mytimer = setInterval("showReaderList()",10000);
var mytimer2 = setInterval("showFeaturedReader()",10000);

$(document).ready(function(){
        showFeaturedReader();
        showReaderList();
});



</script>

<form method="post" action="/register/login_submit" id="loginform">
	<div class='content_area' align='left'>
	
		<div style='float:left;width:400px;'>
		
			<h1>Sign In</h1>
		
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
			
			<p>Need an account?	<a href="/register/">Register Here</a>	 </p>	
			<p>Forget your password?	<a href="/register/forgot_password">Click here</a>	 </p>	
			<p>Having trouble in signing-in?	<a href="/contact">Contact Us</a>	 </p>	
		
		</div>
		
		<div style='float:right; width:400px;'>

			<div id="featuredReader"> 
				<center>
				<img style='margin-top: 100px;' src='/media/images/ajax_spinner.gif'>
				</center>
			</div>            
		  
	  
			<div id="readersList">
			  <center>
				<img style='margin-top: 100px;' src='/media/images/ajax_spinner.gif'>
			  </center>
			</div>
		
		</div>
		
		<div class='clear'></div>
		
	</div>
			
</form>

<!--
<script src="/chat/featured.js"></script>
-->
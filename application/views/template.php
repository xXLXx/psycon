<?

	$meta = $this->system_vars->meta_tags($this->uri->uri_string());
	
	if(!isset($meta['title']))
	{
	
		$meta = $this->system_vars->meta_tags();
	
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta name="Description" content="Psychic Contact offers Online Psychic Chat and Email Readings - free reading for first time clients" />
<meta name="Keywords" content="Free Psychic Chat, Online Psychic Chat Readings, Free Psychic Readings, chat live online, psychic readers online, free pschic readings, psychics, psychic reading, psychic readings, psychic chat reading, Email Readings, tarot reading, free psychic online chat" />
<script type="text/javascript" src="/advanced/js/rotator.js"></script>
	<title><?=$meta['title']?></title>
	<meta name="keywords" content="<?=$meta['keywords']?>">
	<meta name="description" content="<?=$meta['description']?>">
	<meta name="robots" content="all">
	    
	<link rel=stylesheet type="text/css" href="/media/css/stylesheet.css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.js"></script>
	
	<script>
	
		$(document).ready(function()
		{
		
			$('input[placeholder], textarea[placeholder]').placeholder();
		
		});
	
	</script>
	
</head>

<body>

	<div class="page">
		
		<div class="top_header_bar">
			
			<div class="logo">
				<h1><a href="/" class="logo">Keywords go here for Psychic Contact</a></h1>
			</div>
			
			<div style="float: left; margin-left: 430px; margin-top: 30px;">
				
				<center>
					<a href="/site/phonereaders" style="color: white; font-weight: bold; font-size: 16px;">
						1 866 WE-READ-U<br>
						(937-3238)<br>
						<b><i><u>1st 3 Phone Minutes Always Free!</u></i></b>
					</a>
				</center>
				
			</div>
			
			<div class="social_icons">
				<a href='http://www.facebook.com/#!/groups/112755505435022/' style="margin: 0 0 0 40px;" target="_blank"><img src="/media/images/facebook.jpg"></a>
				<a href='http://twitter.com/#!/Psychic_Contact' target="_blank"><img src="/media/images/twitter.jpg"></a>
				<a href='<?= SITE_URL?>/blog/' title="blog"><img src="/media/images/blog.jpg"></a>
			</div>  
	          
		</div>   
		 
		<div class="navigation">
			
			<img src="/media/images/nav-right.jpg" style="float: right;" /><img src="/media/images/nav-left.jpg" style="float: left;" />
			
			<ul>
				<li><a href="/"  class="current">Home</a></li>
				<li><a href="/"  class="current">Home</a></li>
				<li><a href="/"  class="current">Home</a></li>
				<li><a href="/"  class="current">Home</a></li>
				<li><a href="/"  class="current">Home</a></li>
			</ul>
	
		</div>
	
		<!-- Content -->
		<img src="/media/images/top_content.gif" />	
		<div class="content">    
		
			test
		
		</div>
		<img src="/media/images/content-bottom.gif" style="display: block; float: left;" />
	
	
		<div class="footer">
			
			<div class='links' align="center">
				<a href="/"  class="bottom_nav">Home</a>
				<a href="/"  class="bottom_nav">Home</a>
				<a href="/"  class="bottom_nav">Home</a>
				<a href="/"  class="bottom_nav">Home</a>
				<a href="/"  class="bottom_nav">Home</a>
			</div>
		
			<p class="copyright">

				<img src="/media/images/geo-trust-logo.jpg" /><img src="/media/images/paypal-logo.gif" />
				
				<a href="http://www.securitymetrics.com/site_certificate.adp?s=www%2epsychic-contact%2ecom&amp;i=991461" target="_blank" >
					<img src="http://www.securitymetrics.com/images/sm_ccsafe_wh.gif" alt="SecurityMetrics for PCI Compliance, QSA, IDS, Penetration Testing, Forensics, and Vulnerability Assessment" border="0">
				</a>
			
				Copyright &copy 1998 - 2013. All rights reserved<br />Psychic Contact/Jayson Lynn.Net Inc.
			</p>
			
		</div> 
	
	</div>

</body>
</html>
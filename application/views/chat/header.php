<?

	$meta = $this->system_vars->meta_tags($this->uri->uri_string());
	
	if(!isset($meta['title']))
	{
	
		$meta = $this->system_vars->meta_tags();
	
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<title><?=$meta['title']?></title>

	<meta http-equiv="Content-type" content="text/html; charset=UTF-8">
	<meta name="Description" content="Psychic Contact offers Online Psychic Chat and Email Readings - free reading for first time clients" />
	<meta name="Keywords" content="Free Psychic Chat, Online Psychic Chat Readings, Free Psychic Readings, chat live online, psychic readers online, free pschic readings, psychics, psychic reading, psychic readings, psychic chat reading, Email Readings, tarot reading, free psychic online chat" />

	<meta name="keywords" content="<?=$meta['keywords']?>">
	<meta name="description" content="<?=$meta['description']?>">
	<meta name="robots" content="all">
	    
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	
	<script type="text/javascript" src="/media/bootstrap/js/bootstrap.min.js"></script>
	<link rel=stylesheet type="text/css" href="/media/bootstrap/css/bootstrap.css">
	
	<link rel=stylesheet type="text/css" href="/media/css/stylesheet.css">

	<script src='/media/javascript/placeholder.js'></script>
	<script src='/media/javascript/playsound.js'></script>
	<script src='/media/javascript/rating/jquery.raty.min.js'></script>
	<!--
    <script src="http://psycon.rampnode.com/connect.js"></script>
	-->
    <script src='/media/javascript/jqui/jquery-ui-1.8.16.custom.min.js'></script>
	<link rel="stylesheet" href="/media/javascript/jqui/css/overcast/jquery-ui-1.8.16.custom.css" />
	<script src="http://dev.psychic-contact.com:3701/auth.js"></script>
	<script src="http://dev.psychic-contact.com:3701/socket.io/socket.io.js"></script>
	<!--
	<script src="/chat/socket.io.js"></script>
-->
	<style>
	
		.page-notifs{ margin:20px 20px 0 20px; }
		.datetime{ width:206px !important; cursor:pointer; background-image:url(/media/images/calendar.png); background-position:193px 6px; background-repeat: no-repeat; }
	
	</style>
	
	<script>

		$(document).ready(function()
		{
		
			$('.datetime').datepicker({
                ampm: true
             });
		
			$('.open_reviews').click(function(e){
			
				e.preventDefault();
				
				var profile_id = $(this).attr('profile_id');
				
				NewWindow('/profile/reviews/'+profile_id,'expertReviews',550,500,'','center');
			
			});
		
			$('.rating').raty({
				readOnly:true,
				starHalf : 'sm-half.png',
				starOff : 'sm-off.png',
				starOn : 'sm-on.png',
				size : 16,
				half : true,
				space : false,
				score: function()
				{
    				return $(this).attr('data-rating');
				}
			});
		
			$('.favorite').click(function(e){
			
				e.preventDefault();
				
				var profile_id = $(this).attr('profile_id');
				
				if(profile_id)
				{
				
					$.get('/main/favorite/'+profile_id, function(data)
					{
					
						alert(data.message);
					
					}, 'json');
				
				}
				else
				{
				
					alert("You did not set a expert's profile id.");
				
				}
			
			});
		
			$('input[placeholder], textarea[placeholder]').placeholder();
			
			$('.submit').click(function(e){
				e.preventDefault();
				$(this).closest('form').submit();
			});
		
		});

        function NewWindow(mypage,myname,w,h,scroll,pos){
            if(pos=="random"){LeftPosition=(screen.width)?Math.floor(Math.random()*(screen.width-w)):100;TopPosition=(screen.height)?Math.floor(Math.random()*((screen.height-h)-75)):100;}
            if(pos=="center"){LeftPosition=(screen.width)?(screen.width-w)/2:100;TopPosition=(screen.height)?(screen.height-h)/2:100;}
            else if((pos!="center" && pos!="random") || pos==null){LeftPosition=0;TopPosition=20}
            settings='width='+w+',height='+h+',top='+TopPosition+',left='+LeftPosition+',scrollbars=1,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes';
            win=window.open(mypage,myname,settings);
        }
	
	</script>
	
    <? if($this->session->userdata('member_logged')): ?>

        <? $socket_url = "http://dev.psychic-contact.com"; "//66.178.176.109";  ?>
        <? $socket_port = 3701 ?>
        <? $mem_id = $this->member->data['id']; ?>
        <? $member_username = $this->member->data['username']; ?>
        <? $member_id_hash = $this->member->data['member_id_hash']; ?>
        <? $member_type = (is_null($this->member->data['profile_id']))?'client':'reader'; ?>
       
        <script>
            var chat_init_data ={
                'member_type' : '<?=$member_type?>',
                'member_id' : '<?=$mem_id?>',
                'member_id_hash' : '<?=$member_id_hash?>',
                'member_username' : '<?=$member_username?>',
                '_disconnect_url' : '<?=$this->config->item('site_url') . "/main/disconnect_user/" . $mem_id?>',
                'socket_url'  : '<?=$socket_url ?>',
                'socket_port'  : '<?=$socket_port ?>'
            };

        </script>
        <!-- move chat lobby loading script to 
        <script src="/chat/chatmonitor.js?time=<?=time()?>"></script>
        -->
    <? endif; ?>
	
</head>

<body>

	<div class="page">
		
		<div class="top_header_bar">
			
			<div class="logo">
				<h1><a href="/" class="logo"></a></h1>
			</div>
			
			<div style="float: left; margin-left: 430px; margin-top: 30px;">
				
				<center>
					<a href="/phone_readings" style="color: white; font-weight: bold; font-size: 16px;">
						1 866 WE-READ-U<br>
						(937-3238)<br>
						<b><i><u>1st 3 Phone Minutes Always Free!</u></i></b>
					</a>
				</center>
				
			</div>
			
			<div class="social_icons">
				<a href='http://www.facebook.com/#!/groups/112755505435022/' style="margin: 0 0 0 40px;" target="_blank"><img src="/media/images/facebook.jpg"></a>
				<a href='http://twitter.com/#!/Psychic_Contact' target="_blank"><img src="/media/images/twitter.jpg"></a>
				<a href='/blog/' title="blog"><img src="/media/images/blog.jpg"></a>
			</div>  
	          
		</div>   
			
		<?
		
			if($this->session->userdata('member_logged'))
			{
			
				echo "
				<ul id='navigation'>
					<img src=\"/media/images/nav-left.jpg\" style=\"float: left;\" />
			
					<li><a href=\"/\">Home</a></li>
					<li><a href=\"/my_account\">My Account</a></li>
					<li><a href=\"/psychics\">Our Psychics</a></li>
					<li><a href=\"/phone_readings\">Phone Readings</a></li>";

                IF($this->member->data['profile_id'])
                {
                    echo "<li><a href=\"/my_account/email_readings/open_requests\">Email Readings</a></li>";
                }
                ELSE
                {
                    echo "<li><a href=\"/my_account/email_readings/client_emails\">Email Readings</a></li>";
                }

			    echo "
					<li><a href=\"/articles\">Articles</a></li>
					<li><a href=\"/blog\">Psychic Blog</a></li>
					<li><a href=\"/prices\">Prices</a></li>
					<li><a href=\"https://devpsychiccontact.zendesk.com/hc/en-us\" target='_blank'>Support</a></li>
					<li class='last'><a href=\"/main/logout\" onClick=\"Javascript:return confirm('Are you sure you want to logout?');\" class=\"current\">Logout</a></li>
					<img src=\"/media/images/nav-right.jpg\" style=\"float: right;\" />
				</ul>
				";
			
			}
			else
			{
			
				echo "
				<ul id='navigation'>
					<img src=\"/media/images/nav-left.jpg\" style=\"float: left;\" />
					<li><a href=\"/\">Home</a></li>
					<li><a href=\"/register\">Register</a></li>
					<li><a href=\"/psychics\">Our Psychics</a></li>
					<li><a href=\"/phone_readings\">Phone Readings</a></li>
					<li><a href=\"/my_account/email_readings\">Email Readings</a></li>
					<li><a href=\"/articles\">Articles</a></li>
					<li><a href=\"/blog\">Psychic Blog</a></li>
					<li><a href=\"/prices\">Prices</a></li>
					<li><a href=\"https://devpsychiccontact.zendesk.com/hc/en-us\" target='_blank'>Support</a></li>
					<li class='last'><a href=\"/register/login\">Login</a></li>
					<img src=\"/media/images/nav-right.jpg\" style=\"float: right;\" />
				</ul>
				";

			}
			
		?>
	
		<!-- Content -->
		<img src="/media/images/top_content.gif" />	
		<div class="content">    
		
			<?
			
				if(!isset($this->hide_banner))
				{
				
					echo "
					<div class=\"banner_sub\">
				       <img style=\"float: right;\" src=\"/media/images/banner-sub-right.jpg\"><img style=\"float: left;\" src=\"/media/images/banner-sub-left.jpg\">
				       <div class=\"text\">
				       <h1>Accurate, Compassionate, Professional &amp; <br>Ethical Psychic Readers.</h1>                   
				       </div>              
				   </div>
					";
				
				}
			
				// Form errors
				if(validation_errors() && empty($this->error)){
					echo "<div class='alert alert-error page-notifs'><strong>There are errors:</strong>".validation_errors()."</div>";
				}
				
				// Session based errors
				if($this->session->flashdata('error'))
				{
				
					echo "<div class='alert alert-error page-notifs'><strong>There are errors:</strong><p>".$this->session->flashdata('error')."</p></div>";
				
				}

                if($this->session->flashdata('warning'))
                {

                    echo "<div class='alert alert-warning page-notifs'><strong>Warning:</strong><p>".$this->session->flashdata('warning')."</p></div>";

                }
				
				// Inlin errors
				if(isset($this->error))
				{
				
					echo "<div class='alert alert-error page-notifs'><strong>There was an error:</strong><p>".$this->error."</p></div>";
				
				}
				
				// Standard response
				if($this->session->flashdata('response'))
				{
				
					echo "<div class='alert alert-info page-notifs'><p>".$this->session->flashdata('response')."</p></div>";
				
				}

			
			?>

            <div id="universal-form-error">

            </div>
		
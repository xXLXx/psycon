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
		
		.page_title{ color:#486a9a; font-family: Arial; border-bottom:solid 1px #BBB; padding:0 0 5px 0 !important; }
		
		#main_content a{ text-decoration: none; color:#486A9A; font-size:12px; font-family: Arial; }
		#main_content td{ font-size:12px; font-family: Arial; }
		
	</style>
	
	<!-- JQuery -->
	<script src='//code.jquery.com/jquery-1.11.0.min.js'></script>
	
	<!-- JQuery UI - Overcast -->
	<script src='/media/javascript/jqui/jquery-ui-1.8.16.custom.min.js'></script>
	<link rel="stylesheet" href="/media/javascript/jqui/css/overcast/jquery-ui-1.8.16.custom.css" />
	
	<!-- JQuery Tiny MCE -->
	<script src='/media/javascript/tiny_mce/jq.tinymce.js'></script>
	
	<!-- JQuery Date/Time -->
	<script src='/media/javascript/datetime/jquery-ui-timepicker-addon.js'></script>
	<link rel="stylesheet" href="/media/javascript/datetime/jquery-ui-timepicker-addon.css" />
	
	<!-- JQuery Switch -->
	<script src='/media/javascript/ajax_switch/jquery.iphone-switch.js'></script>
	
	<!-- JQuery Hint 
	<script src='/media/javascript/hint.js'></script>
	-->
	
	<!-- Bootstrap -->
	<script src='/media/bootstrap/js/bootstrap.min.js'></script>
	<link rel="stylesheet" href="/media/bootstrap/css/bootstrap.min.css" />
	
	<style>
	
		h1{line-height:26px !important;}
	
	</style>
	
	<script>
	
		$(document).ready(function()
		{
		
			$('.subnav_button').click(function(evt)
			{
				evt.preventDefault();
				var elmParent = $(this).parent();
				$(elmParent).children('.submenu').toggle();
			});
			
			$('.tinymce').tinymce
			({
                    // Location of TinyMCE script
                    script_url : '/media/javascript/tiny_mce/tiny_mce.js',

                    // General options
                    theme : "advanced",
                    plugins : "jbimages,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
					realtive_urls : false,
					
                    // Theme options
                    theme_advanced_buttons1 : "code,fullscreen,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontsizeselect",
                    theme_advanced_buttons2 : "cut,copy,paste,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,jbimages,|,preview,|,forecolor,backcolor",
                    theme_advanced_buttons3 : "tablecontrols,|,hr,|,sub,sup,|,charmap,emotions,iespell,media",
                    theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak,|,ltr,rtl",
                    theme_advanced_toolbar_location : "top",
                    theme_advanced_toolbar_align : "left",
                    theme_advanced_statusbar_location : "bottom",
                    theme_advanced_resizing : true,
                    
                    width : '100%',
                    height : 350
            });
            
            $('.datetime').datetimepicker
            ({
            	ampm: true,
				separator: ' @ '
            });
		
		});
	
	</script>
	
</head>
<body>

	<div align='center' id='wrapper'>
	
		<?
		
			if(validation_errors()) echo validation_errors('<div class=\'errors\'>','</div>');
			if($this->error) echo '<div class=\'errors\'>'.$this->error.'</div>';
			if($this->response) echo '<div class=\'responses\'>'.$this->response.'</div>';
			if($this->session->flashdata('error')) echo '<div class=\'errors\'>'.$this->session->flashdata('error').'</div>';
			if($this->session->flashdata('response')) echo '<div class=\'responses\'>'.$this->session->flashdata('response').'</div>';
		
		?>
	
		<div align='center' id='main'>
		
			<table width='95%' cellpadding="5" cellspacing="0" border="0">
			
				<tr>
					<td valign="top" width='230'>
					
						<div id='logo_container' class='white_box' style='padding:15px 0;'>
							<h1>vAdmin</h1>
						</div>
						
						<ul id='nav_left'>
						
							<?
							
								$getNavbar = $this->db->query("SELECT * FROM vadmin_nav WHERE hidden = 0 ORDER BY `sort` ");
								
								foreach($getNavbar->result_array() as $n)
								{
								
									echo "<li>";
									
										if(isset($n['module_name']))
										{
										
											echo "<li><a href='/vadmin/{$n['module_name']}' class='blue_button'>{$n['title']}</a></li>";
										
										}
										else
										{
									
											$getSubNav = $this->db->query("SELECT * FROM vadmin_navsub WHERE nav_id = {$n['id']} ORDER BY `sort` ");
											
											if($getSubNav->num_rows() > 0)
											{
											
												// Link WITH subnav
												echo "<a href='/' class='blue_button subnav_button'>{$n['title']}</a><ul class='submenu off_white_box' ".($this->open_nav==$n['id'] ? "" : "style='display:none;'").">";
											
													foreach($getSubNav->result_array() as $s){

                                                        if(!empty($s['module_name'])){
                                                            echo "<li><a href='/vadmin/{$n['module_name']}'>{$s['title']}</a></li>";
                                                        }else{
														    echo "<li><a href='/vadmin/main/overview/{$n['id']}/{$s['id']}'>{$s['title']}</a></li>";
                                                        }
													
													}
												
												echo "</ul>";
											
											}
											else
											{
											
												// Link WITHOUT subnav
												echo "<a href='/vadmin/main/overview/{$n['id']}' class='blue_button'>{$n['title']}</a>";
											
											}
										
										}
									
									echo "</li>";
								
								}
								
								// Superadmin Tools
								/*
								if($this->admin['id']=='9999')
								{
								
									echo "
									<li>
										<a href='/' class='blue_button subnav_button'>Developer Tools</a>
										<ul class='submenu off_white_box' ".($this->open_nav=='devtools' ? "" : "style='display:none;'").">
											<li><a href='/vadmin/devtools/menu_builder'>Menu Builder</a></li>
										</ul>
									</li>";
								
								}
								*/
							
							?>
							
						</ul> <!-- nav_left -->
					
					</td>
					<td valign="top">
					
						<div id='header_div' class='off_white_box'>
						
							<table width='100%' cellpadding="0" cellspacing="0">
							
								<tr>
									<td style='font-weight:bold;'>Welcome <?=$this->admin['name']?></td>
									<td align='right' class='links'>
									
										<a href='/vadmin/main/edit_record/8/0/1'>Settings</a>
										<a href='/vadmin/main/overview/1'>Administrators</a>
										<a href='/vadmin/main/logout' onclick="Javascript:return confirm('Are you sure you want to logout?');">Logout</a>
									
									</td>
									
									<? if(isset($this->nav_id)) : ?>
									
										<td width='225' align='right'>
										
											<form action='/vadmin/search/index/<?=$this->nav_id?>' method='POST' style='margin:0;padding:0;'>
											
												<table cellPadding='2' cellspacing="0">
												
													<tr>
														<td><input type='text' name='query' title='Search <?=$this->nav_title?>' style='border:solid 1px #BBBBBB;padding:3px;'></td>
														<td><input type='submit' value='Search' class='blue_button' style='height:22px;'></td>
													</tr>
													
												</table>
												
											</form>
											
										</td>
									
									<? endif; ?>
									
								</tr>
								
							</table>
							
						</div> <!-- header_div -->
						
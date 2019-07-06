
	<style>
	
		h1.blog_title{ font-size:20px; font-weight:bold; text-decoration:none; line-height:28px; }
		.comment_div{ padding-bottom:15px; border-bottom:dotted 2px #E0E0E0; margin-bottom:15px; }
	
	</style>

	<div class='content_area'>
	
		<?=($image ? "<img src='/media/assets/{$image}' width='250' class=\"img-polaroid pull-right\" style='margin-left:25px;margin-bottom:25px;' />" : "")?>
	
		<h1 class='blog_title'><?=$title?></h1>
		<div class='caption' style='font-size:14px;'>By Administrator on <?=date("F d, Y", strtotime($date))?></div>
		<div style='line-height:24px;font-size:15px;'><?=nl2br($content)?></div>
		
		<hr />
		
		<h1>Comments</h1>
		
		<?
		
			// Show Comments
			if($comments)
			{
			
				foreach($comments as $c)
				{
				
					echo "
					<div class='comment_div'>
						<div>".nl2br($c['comments'])."</div>
						<div class='caption'>From: {$c['username']} on ".date("m/d/y @ h:i A", strtotime($c['datetime']))."</div>
					</div>
					";
				
				}
			
			}
			else
			{
			
				echo "<p>Be the first to comment on this post. Login and use the form below.</p><hr />";
			
			}
		
			// Post Comment
			if($this->session->userdata('member_logged'))
			{
			
				echo "
				<h1>Comment on this post:</h1>
				
				<form action='/blog/submit_comment/{$id}' method='POST'>
					<div class='caption'>Enter your comments in the text field here and click \"Post Comment\" bellow.</div>
					<div><textarea name='comments' style='width:90%;height:75px;'></textarea></div>
					<div style='padding:5px 0 0;'><input type='submit' name='sub' value='Post Comment' class='btn btn-primary'></div>
				</form>
				
				";
			
			}
			else
			{
			
				echo "<p align='center' style='padding:50px;'>To make comments on this blog post, <a href='/blog/login/{$url}'>please login.</a></p>";
			
			}
		
		?>
		
	</div>
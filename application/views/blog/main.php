
	<style>
	
		.blog_entry{ padding:20px; }
		.blog_entry .title a{ font-size:20px; font-weight:bold; text-decoration:none; line-height:28px; }
		.blog_entry .blog_caption{ font-size:12px; color:#C0C0C0; margin:15px 0 0; }
		.blog_entry .pull-left{ width:250px; }
		.blog_entry .pull-right{ width:635px; }
		.blog_entry .read_more{ padding:5px 0 0; }
		
	</style>
	
	<?
	
		foreach($posts as $b)
		{
		
			$total_comments = $this->blog_model->totalComments($b['id']);
		
			echo "
			<div class='blog_entry'>
				<div class='pull-left'>
					".($b['image'] ? "<img src='/media/assets/{$b['image']}' width='250' class=\"img-polaroid\" />" : "")."
				</div>
				<div class='pull-right'>
					<div class='title'><a href='/blog/{$b['url']}'>{$b['title']}</a></div>
					<div class='blog_caption'>Written By: Administrator on ".date("F d, Y", strtotime($b['date']))."</div>
					<div class='description'>".nl2br($b['short_description'])."</div>
					<div class='read_more'><span class='icon-chevron-right'></span> <a href='/blog/{$b['url']}'>Read More</a>".($total_comments > 0 ? " &nbsp; &nbsp; - {$total_comments} Comment(s)" : "")."</div>
				</div>
				<div class='clearfix'></div>
				<hr />
				
			</div>
			";
		
		}
	
	?>
	
	<div align='center'>
		<?
		
			if(isset($pagination))
			{
			
				echo "<div align='center'>{$pagination}</div>";
			
			}
			else
			{
			
				//echo "<a href='/blog/archive' class='btn'>Archived Blog Entries</a>";
			
			}
		
		?>
	</div>
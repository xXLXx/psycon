
	<div class='content_area'>
	
		<h2 style='margin:15px 0;'><?=$title?></h2>
		
		<?
		
			if($articles)
			{
			
				echo "
				
				<table class='table table-striped table-hover'>";
				
				foreach($articles as $a)
				{
				
					echo "
					<tr>
						<td width='75'>".date("m/d/y", strtotime($a['datetime']))."</td>
						<td><a href='/articles/{$a['category_url']}/{$a['url']}'>{$a['title']}</a></td>
						<td align='right' width='120'><a href='/articles/{$a['category_url']}/{$a['url']}' class='btn btn-primary'>View Article</a></td>
					</tr>
					";
				
				}
				
				echo "</table>";
			
			}
			else
			{
			
				echo "<p align='left'>There are no articles to show</p>";
			
			}
		
		?>
		
		<div align='center'>
			<?
			
				if($archive)
				{
				
					echo "<a href='/articles' class='btn'>Most Recent Articles</a>";
				
				}
				else
				{
				
					echo "<a href='/articles/archive/{$category}' class='btn'>Article Archive</a>";
				
				}
			
			?>
		</div>
	
	</div>
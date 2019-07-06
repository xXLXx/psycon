
	<div class='content_area'>
	
		<h2>News</h2>
		
		<hr />
		
		<?
		
			if($news)
			{
			
				foreach($news as $n)
				{
				
					echo "<div style='font-size:12px;padding:0 0 10px;'>- {$n['text']}</div>";
				
				}
			
			}
		
		?>
	
	</div>
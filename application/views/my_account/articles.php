
	<div class="horizontal_bar">
		<a href="/my_account/articles/create_new">Add A New Article</a>
	</div>

	<div class='padded'>
	
		<h2>Your Articles</h2>
		
		<div>&nbsp;</div>
		
		<?
		
			if($articles)
			{
			
				echo "<table width='100%' class='striped' cellPadding='5' cellSpacing='0'>";
			
				foreach($articles as $a)
				{
				
					echo "
					<tr>
						<td>{$a['title']}</td>
						<td width='25'><a href='/article/view/{$a['id']}' class='blue-button'><span>View</span></a></td>
						<td width='25'><a href='/my_account/articles/edit/{$a['id']}' class='blue-button'><span>Edit</span></a></td>
						<td width='25'><a href='/my_account/articles/delete/{$a['id']}' onClick=\"Javascript:return confirm('Are you sure you want to delete this article?');\" class='blue-button'><span>Delete</span></a></td>
					</tr>
					";
				
				}
				
				echo "</table>";
			
			}
			else
			{
			
				echo "<p>You do not have any articles</p>";
			
			}
		
		?>
	
	</div>

	<div class='content_area'>

		<h2>My Questions</h2>
		<p>The questions listed below are the questions you have asked.</p>
		
		<?
		
			if($questions)
			{
			
				echo "<table class='table table-striped table-hover' width='100%' cellPadding='5' cellSpacing='0'>";
			
				foreach($questions as $q)
				{
				
					switch($q['status'])
					{
					
						case "new":
						$status = "Waiting For Big";
						break;
						
						case "pending":
						$status = "<span style='color:blue;'>Waiting For Expert's Answer</span>";
						break;
						
						case "unaccepted":
						$status = "<span style='color:red;'>Waiting On You</span>";
						break;
						
						case "answered":
						$status = "<b>Answered</b>";
						break;
						
						case "declined":
						$status = "Declined";
						break;
					
					}
				
					echo "
					<tr>
						<td>{$q['title']}</td>
						<td>{$status}</td>
						<td style='text-align:right;'>
							<a href='/client/questions/view/{$q['id']}' class='btn'>View</a>
						</td>
					</tr>
					";
				
				}
				
				echo "</table>";
			
			}
			else
			{
			
				echo "<div>You have not asked any questions</div>";
			
			}
		
		?>
	
	</div>
	
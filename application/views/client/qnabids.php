
	<div class='content_area'>
	
		<a href='/client/qnabids/new_question' class='btn btn-small btn-warning pull-right' style='margin:10px 0 0;'>Ask A New Question</a>

		<h2>QnA Questions</h2>
		
		<div class='alert alert-info'>
			<strong>What is QnA Bids?</strong>
			<div>If you have a question that needs to be answered but you don't know which expert to ask, then QnA Bids is for you. "Ask A New Question" using the button above and your question will be posted to our public experts board. Each expert will provide you with a bid. You can select the bid that you want with the expert of your choice and get your answer quick. <b>Give it a try!</b></div>
		</div>
		
		<h2>My Questions</h2>
		
		<?
		
			if($questions)
			{
			
				echo "<table class='table table-striped table-hover' width='100%' cellPadding='5' cellSpacing='0'>
					
					<thead>
						
						<tr>
							<th>Question:</th>
							<th style='width:50px;'>Bids:</th>
							<th style='width:100px;'>&nbsp;</th>
							<th style='width:100px;'>&nbsp;</th>
						</tr>
						
					</thead>
					
					<tbody>
					
				";
			
				foreach($questions as $q)
				{
				
					echo "
					<tr>
						<td>{$q['title']}</td>
						<td>{$q['total_bids']}</td>
						<td>";
						
							switch($q['status'])
							{
							
								case "active":
								echo "<span class='label label-info'>Open</span>";
								break;
								
								case "closed":
								echo "<span class='label'>Closed</span>";
								break;
							
							}
						
						echo "</td>
						<td style='text-align:right;'><a href='/qnabids/view/{$q['id']}' class='btn'>View</a></td>
					</tr>
					";
				
				}
				
				echo "</tobdy>
				
				</table>
				
				<div>&nbsp;</div>";
			
			}
			else
			{
			
				echo "<div style='margin-bottom:25px;'>You have not asked any questions.</div>";
			
			}
		
		?>
	
	</div>
	
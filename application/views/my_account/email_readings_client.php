
	<div class='content_area'>

		<h2>My Email Readings</h2>
		
		<div>&nbsp;</div>
		
		<?
		
			if($emails)
			{
			
				echo "<table class='table table-striped table-hover'>";
			
				foreach($emails as $e)
				{
				
					echo "
					<tr>
						<td style='vertical-align:middle;'><div><b>{$e['package_title']}</b></div>".date("m/d/Y @ h:i A", strtotime($e['datetime']))."</td>
						<td style='vertical-align:middle;'>$".number_format($e['price'], 2)."</td>
						<td style='vertical-align:middle;text-align:right;'>".($e['status']=='new' ? "<span class='label label-important'>Waiting For Reading</span>" : "<span class='label label-success'>Complete</span>")."</td>
						<td style='vertical-align:middle;text-align:right;width:60px;'><a href='/my_account/email_readings/client_view/{$e['id']}' class='btn'>View</a></td>
					</tr>
					";
				
				}
				
				echo "</table>";
			
			}
			else
			{
			
				echo "<div>You have not ordered any email readings</div>";
			
			}
		
		?>
		
		<div>&nbsp;</div>
	
	</div>
	
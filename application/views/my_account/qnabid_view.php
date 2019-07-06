
	<div class='content_area'>
		
		<h2><?=$title?></h2>
		<div style='padding:10px 0 0;'>In <a href='/category/main/<?=$category_url?>'><?=$category_title?></a> / <a href='/category/sub/<?=$category_url?>/<?=$subcategory_url?>'><?=$subcategory_title?></a> &nbsp; &nbsp; - &nbsp; &nbsp; Asked By <?=$member['username']?> </div>
		
		<hr />
		
		<table cellPadding='10'>
			
			<tr>
				<td valign='top' width='150'><b>Details:</b></td>
				<td>
					<div><strong><?=$title?></strong></div>
					<div><?=$question?></div>
				</td>
			</tr>
			
			<tr>
				<td width='150'><b>Date Posted:</b></td>
				<td><div><?=date("m/d/Y @ h:i A", strtotime($datetime))?> EST</div></td>
			</tr>
			
			<tr>
				<td width='150'><b>Expiration Date:</b></td>
				<td><div><?=date("m/d/Y @ h:i A", strtotime($expiration_date))?> EST</div></td>
			</tr>
			
			<tr>
				<td width='150'><b>Budget:</b></td>
				<td><div><?=$budget?></div></td>
			</tr>
			
		</table>
		
		<hr />
		
		<h2>Your Bid</h2>
		
		<table cellPadding='10'>
			
			<tr>
				<td width='150'><b>Bid Date:</b></td>
				<td><div><?=date("m/d/Y @ h:i A", strtotime($bid_date))?> EST</div></td>
			</tr>
			
			<tr>
				<td valign='top' width='150'><b>Cover Letter:</b></td>
				<td><div><?=nl2br($cover_letter)?></div></td>
			</tr>
			
			<?
			
				if($answer)
				{
				
					echo "
					<tr>
						<td valign='top' width='150'><b>Answer:</b></td>
						<td><div>".nl2br($answer)."</div></td>
					</tr>
					";
				
				}
			
			?>
			
		</table>
			
		<div>&nbsp;</div>
			
		<?
		
			if($awarded==1 && !$answer)
			{
			
				echo "
				<hr />
						
				<form action='/my_account/qnabids/submit_answer/{$bid}' method='POST'>
				
					<h3>Submit Your Answer</h3>
					
					<div style='padding-bottom:15px;'>The client has accepted your bid. Please answer the question using the form below.</div>
					
					<div><b>Your Answer:</b></div>
					<div><textarea style='width:90%;' name='answer' rows='10'>".set_value('answer')."</textarea></div>
					
					<div style='padding:15px 0 0;'><a href='/' class='submit blue-button'><span>Submit My Answer</span></a></div>
				
				</form>";
			
			}
		
		?>
	
	</div>
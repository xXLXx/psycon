
	<div class='content_area'>
		
		<h2 style='margin-bottom:0 !important;padding-bottom:0 !important;'>Request A Refund</h2>
		
		<p style='margin-top:0 !important;padding-top:0 !important;'>If you are unhappy with your chat with this expert, you can request a refund. Please do not abuse this feature, over-use will result in account suspension.</p>
		
		<hr />
		
		<form action='/client/chats/submit_refund/<?=$id?>' method='POST'>
		
			<table width='100%' cellPadding='10'>
			
				<tr>
					<td style='width:175px;'><b>Your Max Request:</b></td>
					<td>$ <?=number_format($total, 2)?></td>
				</tr>
				
				<tr>
					<td style='width:175px;'><b>Amount Requesting:</b></td>
					<td>$ <input type='text' style='width:50px;' name='amount' value='<?=set_value('amount')?>'></td>
				</tr>
				
				<tr>
					<td style='width:175px;' valign='top'><b>Reason For Refund:</b></td>
					<td><textarea name='details' style='width:90%;height:150px;'><?=set_value('details')?></textarea></td>
				</tr>
				
				<tr>
					<td style='width:175px;'>&nbsp;</td>
					<td><input type='submit' name='submit' value='Submit Request' class='btn btn-warning'></td>
				</tr>
			
			</table>
		
		</form>
				
		<div>&nbsp;</div>
	
	</div>
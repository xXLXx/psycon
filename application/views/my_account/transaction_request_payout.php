
	<h2>Request A Payout</h2>
	
	<hr />
	
	<?
	
		if($balance >= $this->settings['payout_threshold'])
		{
		
			echo "
			<form action='/my_account/transactions/submit_payout_request' method='POST'>
	
				<table width='100%' cellPadding='10'>
				
					<tr>
						<td width='150'><b>Your Balance:</b></td>
						<td>$".number_format($balance, 2)."</td>
					</tr>
					
					<tr>
						<td width='150'><b>Amount Requested:</b></td>
						<td>$ <input type='text' name='amount' value='".set_value('amount', number_format($balance, 2))."' style='width:50px;'></td>
					</tr>
					
					<tr>
						<td width='150'>&nbsp;</td>
						<td><a href='/' class='blue-button submit'><span>Submit Request</span></a></td>
					</tr>
				
				</table>
			
			</form>
			";
		
		}
		else
		{
		
			echo "<div>You must request a minimum of $".number_format($this->settings['payout_threshold'], 2)." before you can submit a payout request.<br /><strong>Your current balance is $".number_format($balance,2)."</strong></div>";
		
		}
	
	?>

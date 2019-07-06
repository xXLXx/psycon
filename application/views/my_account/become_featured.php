
	<div class="horizontal_bar">
		<a href="/my_account">Cancel</a>
	</div>

	<div class='padded'>

		<h2>Feature My Profile</h2>
		
		<div>&nbsp;</div>
		
		<div style='font-weight:bold;margin-bottom:10px;'>Select a service below. We will attempt to deduct the amount from your account balance.</div>

		<?
					
			echo "<table style='border:solid 1px #C0C0C0;' class='striped' width='100%' cellSpacing='0'>";
		
			foreach($packages as $p)
			{
			
				echo "
				<tr>
					<td><a href='/my_account/main/feature_profile/{$profile_id}/{$p['id']}'>{$p['title']}</a></td>
					<td align='right'>$".number_format($p['price'], 2)."</td>
				</tr>
				";
			
			}
			
			echo "</table>";
		
		?>
		
		<div style='margin:20px 0 0;'>
		
			<div style='padding-bottom:10px;'><b>Your Current Balance:</b> $<?=number_format($this->system_vars->member_balance($this->member->data['id']), 2)?></div>
		
			<a href='/my_account/transactions/fund_your_account' class='blue-button'><span>Fund Your Account</span></a>
			
		</div>
				
		<div>&nbsp;</div>
	
	</div>
	
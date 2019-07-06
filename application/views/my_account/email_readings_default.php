
	<div style='padding-bottom:25px;'>
		<h2>Email Readings</h2>
	</div>
	
	<ul class="nav nav-tabs">
		<li <?=($this->uri->segment('4')=='' ? " class=\"active\"" : "")?>><a href="/my_account/email_readings">Default Readings</a></li>
		<li <?=($this->uri->segment('4')=='specials' ? " class=\"active\"" : "")?>><a href="/my_account/email_readings/specials">My Email Specials</a></li>
		<li <?=($this->uri->segment('3')=='new_special' ? " class=\"active\"" : "")?>><a href="/my_account/email_readings/new_special"><span class='icon icon-tag'></span> Create A New Special</a></li>
	</ul>
	
	<?
	
		if($packages)
		{

			echo "
			<form action='/my_account/email_readings/save_default' method='POST'>
			
			<table class='table table-hover table-striped'>
				
				<thead>
					
					<tr>
						<th>Title:</th>
						<th>Price:</th>
						<th style='width:150px;'>Completion Days:</th>
					</tr>
					
				</thead>
				
				<tbody>
				";
		
				foreach($packages as $p)
				{
				
					$checkRecord = $this->db->query("SELECT * FROM email_packages_days WHERE profile_id = {$this->member->data['id']} AND package = {$p['id']} LIMIT 1");
					$record = $checkRecord->row_array();
					
					echo "
					<tr>
						<td>{$p['title']}</td>
						<td>$".number_format($p['price'], 2)."</td>
						<td><input type='text' name='{$p['id']}' value='".(isset($record['days']) ? $record['days'] : "")."' class='input-mini'></td>
					</tr>
					";
				
				}
				
				echo "
				</tobdy>
				
			</table>
				
			<div align='center'><input type='submit' name='' value='Save Default Readings' class='btn btn-large btn-primary'></div>
			
			</form>";
		
		}
		else
		{
		
			echo "<p>No specials found</p>";
		
		}
	
	?>
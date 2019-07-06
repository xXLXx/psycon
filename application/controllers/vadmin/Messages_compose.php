
	<div style='padding-bottom:25px;'>
		<h2>Compose A New Message</h2>
	</div>

    <ul class="nav nav-tabs">
        <li <?=($this->uri->segment('3')=='' ? " class=\"active\"" : "")?>><a href="/vadmin/message_management">Inbox</a></li>
        <li <?=($this->uri->segment('3')=='outbox' ? " class=\"active\"" : "")?>><a href="/vadmin/message_management/outbox">Outbox</a></li>
        <li <?=($this->uri->segment('3')=='compose' ? " class=\"active\"" : "")?>><a href="/vadmin/message_management/compose"><span class='icon icon-comment'></span> Compose A New Message</a></li>
    </ul>
	
	<form action='/my_account/messages/compose_submit' method='POST'>
	
		<table width='100%' cellPadding='10'>
		
			<tr>
				<td width='150'><b>Recipient:</b></td>
				<td>
					<?
					
						if($this->uri->segment('4'))
						{

							echo "User: #{$to} <input type='hidden' name='to' value='{$to}' class='input-mini'>";
						
						}
						else
						{

							echo "
							<select name='to'>
								<option value=''></option>";
								
									foreach($users as $u)
									{
									
										echo "<option value='{$u['id']}'".set_select('to', $u['id'], ($to==$u['id'] ? TRUE : FALSE)).">{$u['username']}</option>";
									
									}
								
								echo "
							</select>
							";
						
						}
					
					?>
					
				</td>
			</tr>
			
			<tr>
				<td width='150'><b>Subject:</b></td>
				<td><input type='text' name='subject' value='<?=set_value('subject', $subject)?>'></td>
			</tr>
			
			<tr>
				<td width='150'><b>Message:</b></td>
				<td><textarea name='message' style='width:100%;height:150px;'><?=set_value('message', $message)?></textarea></td>
			</tr>
			
			<tr>
				<td width='150'>&nbsp;</td>
				<td><input type='submit' name='submit' value='Send Message' class='btn btn-large btn-primary'></td>
			</tr>
		
		</table>
	
	</form>

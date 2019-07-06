
	<div style='display:inline-block;' class='pull-right'>
		<table>
			<tr>
				<td style='padding-right:30px;text-align:right;'><b>Hi <?=$this->member->data['username']?></b></td>
				<td><img src='<?=$this->member->data['profile']?>' class='img-polaroid' style='width:65px;'></td>
			</tr>
		</table>
		
	</div>
	
	<h1 style='margin-bottom:20px;'>My Account</h1>
	
    <ul class="nav nav-tabs">
		<li <?=($this->uri->segment('2')=='' ? " class='active'" : "")?>><a href="/my_account">Archive</a></li>
		<li <?=($this->uri->segment('2')=='profile' ? " class='active'" : "")?>><a href="/my_account/profile">My Profile</a></li>
		<li><a href="/main/logout" onClick="Javascript:return confirm('Are you sure you want to logout?');">Logout</a></li>
	</ul>
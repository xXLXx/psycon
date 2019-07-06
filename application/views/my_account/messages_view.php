
	<div style='padding-bottom:25px;'>
		<h2><?=$subject?></h2>
	</div>
	
	<ul class="nav nav-tabs">
		<li <?=($this->uri->segment('3')==''||$this->uri->segment('3')=='view' ? " class=\"active\"" : "")?>><a href="/my_account/messages">Inbox</a></li>
		<li <?=($this->uri->segment('3')=='outbox' ? " class=\"active\"" : "")?>><a href="/my_account/messages/outbox">Outbox</a></li>
		<li <?=($this->uri->segment('3')=='compose' ? " class=\"active\"" : "")?>><a href="/my_account/messages/compose"><span class='icon icon-comment'></span> Compose A New Message</a></li>
	</ul>
	
	<div class='well'>	
	From: <?=$from['first_name']?> <?=$from['last_name']?><br />
	To: <?=$to['first_name']?> <?=$to['last_name']?><br />
	Sent: <?=date("M d, Y @ h:i A", strtotime($datetime))?> EST
	</div>
	
	<?=nl2br($message)?>
	
	<hr />
	
	<a href='/my_account/messages/compose/<?=$id?>' class='btn'>Send A Reply</a>
	
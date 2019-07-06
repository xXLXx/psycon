

<? $v_from = "" ?>
<div style='padding-bottom:25px;'>
		<h2><?=$subject?></h2>
	</div>

<ul class="nav nav-tabs">
    <li <?=($this->uri->segment('3')=='' ? " class=\"active\"" : "")?>><a href="/vadmin/message_management">Inbox</a></li>
    <li <?=($this->uri->segment('3')=='outbox' ? " class=\"active\"" : "")?>><a href="/vadmin/message_management/outbox">Outbox</a></li>
    <li <?=($this->uri->segment('3')=='compose' ? " class=\"active\"" : "")?>><a href="/vadmin/message_management/compose"><span class='icon icon-comment'></span> Compose A New Message</a></li>


    <div class='well'>
	From: <?=$v_from?><br />
	To: <?=$to['first_name']?> <?=$to['last_name']?><br />
	Sent: <?=date("M d, Y @ h:i A", strtotime($datetime))?> EST
	</div>
	
	<?=nl2br($message)?>
	
	<hr />
	
	<a href='/vadmin/message_management/compose/<?=$id?>' class='btn'>Send A Reply</a>
	
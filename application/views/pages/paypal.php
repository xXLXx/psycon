<script src='/media/javascript/jq.js'></script>

<script>

$(document).ready(function()
{
	$('.pay_pal').submit();	
});

</script>
<pre>
</pre>
<form class='pay_pal' action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_xclick">
<?php
/*
	<input type="hidden" name="business" value="<?=$this->config->item('merchant_paypal')?>">
*/
?>
	<input type="hidden" name="business" value="<?=$this->config->item('merchant_paypal')?>">
	<input type="hidden" name="business" value="projects@custom-coding.com">

	<input type="hidden" name="item_name" value="<?=$item?>">
	<input type="hidden" name="no_shipping" value="1">
	<input type="hidden" name="item_number" value="<?=$item_number?>">
	<input type="hidden" name="custom" value="<?=base64_encode($this->encrypt->encode($custom))?>">
	<input type="hidden" name="amount" value="<?=number_format($amount, 2)?>">
	<input type="hidden" name="return" value="<?=$this->config->item('site_url')?><?=$return_url?>">
	<input type="hidden" name="cancel_return" value="<?=$this->config->item('site_url')?><?=$cancel_url?>">
	<input type="hidden" name="notify_url" value="<?=$this->config->item('site_url')?><?=$notify_url?>">
</form> 

<p align='center' style='font-weight:bold;font-size:22px;margin:35px 0 0;'>Processing, please wait...</p>
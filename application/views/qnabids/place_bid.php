	
	<div class='content_area'>
	
		<?=$this->load->view('qnabids/header')?>
		
		<h2><?=$title?></h2>
		<div style='padding:10px 0 0;'>In <a href='/category/main/<?=$category_url?>'><?=$category_title?></a> / <a href='/category/sub/<?=$category_url?>/<?=$subcategory_url?>'><?=$subcategory_title?></a> &nbsp; &nbsp; - &nbsp; &nbsp; Asked By <?=$member['username']?> &nbsp; &nbsp; - &nbsp; &nbsp; <?=$total_bids?> Bids</div>
		
		<hr />
		
		<form action='/qnabids/submit_bid/<?=$id?>' method='POST'>
		<table cellPadding='10'>
			
			<tr>
				<td width='150'><b>Budget:</b></td>
				<td><div><?=$budget?></div></td>
			</tr>
			
			<tr>
				<td width='150'><b>Your Bid:</b></td>
				<td><span style='font-size:12px;'>$</span> <input type='text' name='amount' value='<?=set_value('amount')?>' style='width:75px;'></td>
			</tr>
			
			<tr>
				<td valign='top' width='150'><b>Cover Letter:</b></td>
				<td>
				
					<div style='padding-bottom:5px;'>Explain why you are qualified to Answer this Question and what the Client can expect. Do not Answer the Question until the Client has hired you.</div>
					<div><textarea name='cover_letter' rows='15' style='width:90%;'><?=set_value('cover_letter')?></textarea></div>
				</td>
			</tr>
			
			<tr>
				<td width='150'><b>&nbsp;</b></td>
				<td><a href='/qnabids/submit_bid/<?=$id?>' class='blue-button submit'><span>Place Bid</span></a></td>
			</tr>
			
		</table>
		</form>
		
		<hr />
		
		<a href='/qnabids/view/<?=$id?>'><span>Cancel</span></a>
	
	</div>
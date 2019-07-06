	
	<div class='content_area'>
	
		<?=$this->load->view('qnabids/header')?>
		
		<h2><?=$title?></h2>
		<div style='padding:10px 0 0;'>In <a href='/category/main/<?=$category_url?>'><?=$category_title?></a> / <a href='/category/sub/<?=$category_url?>/<?=$subcategory_url?>'><?=$subcategory_title?></a> &nbsp; &nbsp; - &nbsp; &nbsp; Asked By <?=$member['username']?> &nbsp; &nbsp; - &nbsp; &nbsp; <?=$total_bids?> Bids</div>
		
		<hr />
		
		<table cellPadding='10'>
			
			<tr>
				<td width='150'><b>Date Posted:</b></td>
				<td><div><?=date("m/d/Y @ h:i A", strtotime($datetime))?> EST</div></td>
			</tr>
			
			<tr>
				<td width='150'><b>Expiration Date:</b></td>
				<td><div><?=date("m/d/Y @ h:i A", strtotime($expiration_date))?> EST</div></td>
			</tr>
			
			<tr>
				<td width='150'><b>Budget:</b></td>
				<td><div><?=$budget?></div></td>
			</tr>
			
			<tr>
				<td width='150'><b>Total Bids:</b></td>
				<td><div><?=$total_bids?></div></td>
			</tr>
			
			<tr>
				<td width='150'><b>Status:</b></td>
				<td><div><?=ucwords($status)?></div></td>
			</tr>
			
		</table>
		
		<hr />
		
		<a href='/qnabids/place_bid/<?=$id?>' class='blue-button'><span>Place Your Bid</span></a>
	
	</div>

	<script src='/media/javascript/rating/jquery.raty.min.js'></script>
	
	<script>
	
		$(document).ready(function()
		{
		
			$('#rating').raty
			({
				score: function()
				{
    				return $(this).attr('data-rating');
				}
			});
		
		});
	
	</script>
	
	<style>
	
		.form_label{ font-weight:bold; padding-bottom:20px; }
	
	</style>

	<div class='content_area'>
	
		<h2 class='h2' style='padding-bottom:0;'>Leave A Review</h2>
		
		<p style='margin-top:0 !important;'>Use the form below to leave a review for <?=$expert['username']?></p>
	
		<hr />
		
		<form action='/client/experts/save_review/<?=$type?>/<?=$record_id?>' method='POST'>
		
			<input type='hidden' name='profile_id' value='<?=$profile_id?>'>
			<input type='hidden' name='expert_id' value='<?=$expert_id?>'>
		
			<div class='form_label'>The expert you are rating:</div>
		
			<table cellPadding='10'>
				
				<tr>
					<td><img src='<?=$expert['profile']?>' style='height:50px;' class='img-polaroid' /></td>
					<td>
					
						<div><b style='color:blue;'><?=$expert['username']?></b></div>
					
					</td>
				</tr>
				
			</table>
			
			<div>&nbsp;</div>
		
			<div class='form_label'>On a scale of 1-5 stars (5 being the highest rating), how do you rate your experience with this expert?</div>
			<div id='rating' data-rating="<?=set_value('score', $rating)?>"></div>
			
			<div class='form_label' style='margin:45px 0 0;'>Please leave this expert feedback:</div>
			<div><textarea style='width:95%;height:100px;' name='comments'><?=set_value('comments', $comments)?></textarea></div>
			
			<div class='form_label' style='margin:45px 0 0;'><input type='submit' value='Submit Review' class='btn btn-large btn-warning'></div>
		
		</form>
	
	</div>
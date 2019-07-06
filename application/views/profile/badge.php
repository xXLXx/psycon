	<?
	
		echo "
		
		<div style='padding-bottom:25px;'><h2>".$this->reader->data['username']."</h2></div>
		
		<div class='pull-left' style='width:104px;'>
		
			<a href=\"/profile/{$this->reader->data['username']}\"><img src=\"".$this->reader->data['profile']."\" class='img-polaroid'></a>
	</div>
		<div class='pull-right' style='width:750px;'>
			<div align='left'>
			
				<table>
				
					<tr>
						<td valign='top' style='padding:0 8px 0 0;'>
                            <a href='#' class='btn chatButton' data-username='{$this->reader->data['username']}'>Chat Now</a>
						</td>
						<!--<td valign='top' style='padding:0 5px 0 0;'><a href='/' class='btn btn-primary favorite' profile_id=\"{$this->reader->data['id']}\">Set As Favorite</a></td>-->
						".($this->reader->data['enable_email'] == '1' ? "<td valign='top' style='padding:0 5px;'><a href='/profile/{$this->reader->data['username']}/send_question' class='btn btn-primary'>Request an Email Reading</a></td>" : "")."
						<td valign='top' style='padding:0 5px 0 0;'><a href='/profile/{$this->reader->data['username']}/page_reader' class='btn btn-primary'>'Page' this reader</a></td>
					</tr>
				
				</table>
			
			</div>
		
		</div>
		<div class='clearfix'></div>
			
		
		<hr />
		";
	
	?>

    <script src='/chat/button.js'></script>
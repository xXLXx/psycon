	
	<div class='content_area'>
	
		<?=$this->load->view('qnabids/header')?>
		
		<h2>Questions</h2>
		<div>&nbsp;</div>
		
		<?
		
			if($questions)
			{

				foreach($questions as $q)
				{
				
					$member = $this->system_vars->get_member($q['member_id']);
					
					$get_bids = $this->db->query("SELECT id FROM qna_bids WHERE question_id = {$q['id']} ");
					$total_bids = $get_bids->num_rows();
				
					echo "
					<table width='100%' style='border:solid 1px #C0C0C0; margin-bottom:15px; padding:15px;'>

						<tr>
							<td>
							
								<div><a href='/qnabids/view/{$q['id']}' style='color:#2850A8;font-size:14px;text-decoration:none;'>{$q['title']}</a></div>
								<div style='padding:10px 0 0;'>In <a href='/category/main/{$q['category_url']}'>{$q['category_title']}</a> / <a href='/category/sub/{$q['category_url']}/{$q['subcategory_url']}'>{$q['subcategory_title']}</a> &nbsp; &nbsp; - &nbsp; &nbsp; Asked By {$member['username']} &nbsp; &nbsp; - &nbsp; &nbsp; {$total_bids} Bids</div>
							
							</td>
							<td align='right'><a href='/qnabids/view/{$q['id']}' class='blue-button'><span>View</span></a></td>
						</tr>
					
					</table>
					";
				
				}
			
			}
			else
			{
			
				echo "<div>There are no active questions</div>";
			
			}
		
		?>
	
	</div>
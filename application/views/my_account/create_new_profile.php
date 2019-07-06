
	<style>
		
		textarea{ width:100%; }
	
	</style>
	
	<script>
	
		$(document).ready(function()
		{
		
			$('#category_select').change(function()
			{
			
				select_category($(this).val());
			
			});
			
			$('#cb_chat').click(function()
			{
			
				if($(this).is(':checked')) $('#div_chat').show();
				else $('#div_chat').hide();
			
			});
			
			$('#cb_email').click(function()
			{
			
				if($(this).is(':checked')) $('#div_email').show();
				else $('#div_email').hide();
			
			});
			
			<?
			
				// If category is set, force a subcategory call
				if(set_value('category'))
				{
				
					echo "select_category(".set_value('category').");";
				
				}
			
			?>
		
		});
		
		function select_category(val)
		{
		
			if(val != '')
			{
			
				$('#subcategory_div').html("<span style='color:#C0C0C0;'><i>Loading subcategories...</i></span>");
				
				$.get('/main/load_subcategories/'+val, function(obj)
				{
				
					$('#subcategory_div').html("");
				
					$.each(obj, function(k, v)
					{
					
						$('#subcategory_div').append("<input type='checkbox' name='subcategories[]' value='" + v.id + "'> " + v.title + "<br />");
					
					});
					
					<?
			
						// loop through the subcategory selected values and
						// preselect them using javascript
						if(is_array($this->input->post('subcategories')))
						{
						
							foreach($this->input->post('subcategories') as $s)
							{
							
								echo "
								
								$(\"input[name='subcategories[]']\").each(function()
								{
								
									if($(this).val()=='{$s}')
									{
									
										$(this).attr('checked','checked');
									
									}
								
								});
								";
							
							}
						
						}
					
					?>
				
				}, 'json');
			
			}
			else
			{
			
				$('#subcategory_div').html("<span style='color:#C0C0C0;'><i>Select a main category above...</i></span>");
			
			}
		
		}
	
	</script>

	<div class='padded'>
	
		<h2>Create A New Profile</h2>
		
		<p>Please make sure you have your Article(s) ready to be submitted before filling out the the form below<br />
		
		<hr />
		
		<form action='/my_account/main/submit_new_profile' method='POST'>
		
			<table width='100%' cellPadding='10'>
			
				<tr>
					<td width='150'><b>Select A Main Category:</b></td>
					<td>
					
						<select name='category' id='category_select'>
							<option value=''></option>
							<?
							
								foreach($categories as $c)
								{
								
									echo "<option value='{$c['id']}'".set_select('category',$c['id']).">{$c['title']}</option>";
								
								}
							
							?>
						</select>
					
					</td>
				</tr>
				
				<tr>
					<td valign='top' width='150'><b>Select Subcategories:</b><div style='color:#666;'>Minimum of one</div></td>
					<td>
					
						<div id='subcategory_div'><span style='color:#C0C0C0;'><i>Select a main category above...</i></span></div>
					
					</td>
				</tr>
				
				<tr>
					<td width='150'><b>Brief Description:</b><div style='color:#C0C0C0;'>255 Characters or less</div></td>
					<td><textarea rows='10' name='brief_description'><?=set_value('brief_description')?></textarea></td>
				</tr>
				
				<tr>
					<td width='150'><b>Detailed Description:</b></td>
					<td><textarea rows='10' name='detailed_description'><?=set_value('detailed_description')?></textarea></td>
				</tr>
				
				<tr>
					<td width='150'><b>Degrees:</b></td>
					<td><textarea rows='10' name='degrees'><?=set_value('degrees')?></textarea></td>
				</tr>
			
				<tr>
					<td width='150'><b>Experience:</b></td>
					<td><textarea rows='10' name='experience'><?=set_value('experience')?></textarea></td>
				</tr>
				
				<tr>
					<td width='150'><b>Available for Chat?</b></td>
					<td>
					
						<div style='float:left;width:70px;'><input type='checkbox' id='cb_chat' name='available_for_chat' value='1' <?=set_checkbox('available_for_chat','1')?>> Yes</div>

						<div id='div_chat' style='float:left;width:300px;display:<?=(set_value('available_for_chat')=='1' ? "block" : "none")?>;'>
							<table cellPadding='0' cellSpacing='0'>
								<tr>
									<td style='padding:0 15px;'>Price per minute:</td>
									<td>
										$ <input type='text' style='width:25px;' name='price_per_minute' value='<?=set_value('price_per_minute')?>'>
									</td>
									<td style='padding-left:15px;'>$1.09 Minimum</td>
								</tr>
							</table>
							
							<div class='clear'></div>
						</div>
						
					</td>
				</tr>
				
				<tr>
					<td width='150'><b>Available for Email?</b></td>
					<td>

						<div style='float:left;width:70px;'><input type='checkbox' id='cb_email' name='available_for_email' value='1' <?=set_checkbox('available_for_email','1')?>> Yes</div>

						<div id='div_email' style='float:left;width:300px;display:<?=(set_value('available_for_email')=='1' ? "block" : "none")?>;'>
							<table cellPadding='0' cellSpacing='0'>
								<tr>
									<td style='padding:0 15px;'>Price per email:</td>
									<td>
										$ <input type='text' style='width:35px;' name='price_per_email' value='<?=set_value('price_per_email')?>'>
									</td>
									<td style='padding-left:15px;'>$4.99 Minimum</td>
								</tr>
							</table>
							
							<div class='clear'></div>
						</div>
						
					</td>
				</tr>
				
				<tr><td colSpan='2'><hr /></td></tr>
				
				<tr>
					<td width='150' valign='top'>
						<b>Article Title:</b>
					</td>
					<td>
						<input type='text' name='article_title' value='<?=set_value('article_title')?>' >
					</td>
				</tr>
				
				<tr>
					<td width='150' valign='top'>
						<b>Your Article:</b>
						<div style='color:#666;'>Copy and paste your article<br />200 characters minimum</div>
					</td>
					<td>
						<textarea rows='10' name='article'><?=set_value('article')?></textarea>
					</td>
				</tr>
				
				<tr><td colSpan='2'><hr /></td></tr>
				
				<tr>
					<td>&nbsp;</td>
					<td><a href='/' class='blue-button submit'><span>Submit</span></a></td>
				</tr>
				
			</table>
		
		</form>
	
	</div>
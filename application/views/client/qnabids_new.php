
	<script src='/media/javascript/jqui/jquery-ui-1.8.16.custom.min.js'></script>
	<link rel="stylesheet" href="/media/javascript/jqui/css/overcast/jquery-ui-1.8.16.custom.css" />

	<script src='/media/javascript/datetime/jquery-ui-timepicker-addon.js'></script>
	<link rel="stylesheet" href="/media/javascript/datetime/jquery-ui-timepicker-addon.css" />

	<style>
	
		.ui-widget{ font-size:0.9em; }
		.datetime{ cursor: pointer; background:none !important; border:none; color:blue; text-decoration:underline; }
		#qna_table td{ font-size:12px; padding:10px; }
	</style>

	<script>
	
		$(document).ready(function()
		{
		
			<?
			
				if(set_value('category'))
				{
				
					echo "select_category($('#category_select').val());";
				
				}
			
			?>
		
			$('#category_select').change(function()
			{
			
				select_category($(this).val());
			
			});
		
			$('.datetime').datetimepicker
            ({
            	ampm: true,
				separator: ' @ '
            });
		
			$("input[name='deadline']").click(function()
			{
			
				if($(this).is(':checked'))
				{
				
					$('#div_deadline').show();
				
				}
				else
				{
				
					$('#div_deadline').hide();
				
				}
			
			});
		
		});
		
		function select_category(val)
		{
		
			if(val != '')
			{
			
				$('#subcategory_div').html("<span style='color:#C0C0C0;'><i>Loading subcategories...</i></span>");
				
				$.get('/main/load_subcategories/'+val, function(obj)
				{
				
					$('#subcategory_div').html("");
					
					var select_box = "<select name='subcategory'><option value=''></option>";
				
					$.each(obj, function(k, v)
					{
					
						// $('#subcategory_div').append("<input type='checkbox' name='subcategories[]' value='" + v.id + "'> " + v.title + "<br />");
						select_box = select_box + "<option value=\""+v.id+"\">" + v.title + "</option>";
						
					});
					
					select_box = select_box + "</select>";
					$('#subcategory_div').html(select_box);
					
					<?
			
						// loop through the subcategory selected values and
						// preselect them using javascript
						if(set_value('subcategory'))
						{
						
							echo "$(\"select[name='subcategory']\").val(".set_value('subcategory').");";
						
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

	<div class='content_area'>
	
		<h2>Ask A Question</h2>
		
		<p>Do you have a question that needs to be answered? Post it to our public QnA section, where experts will compete to win the chance to answer your question, which means you get bottom dollar price. Fill out the form below and be as detailed as possible.</p>
		
		<hr />
		
		<form action='/client/qnabids/submit_question/' method='POST'>
		
			<table width='100%' cellPadding='10' id='qna_table'>
				
				<tr>
					<td width='200'><b>What's Your Question:</b></td>
					<td><input type='text' name='title' value='<?=set_value('title', $question)?>' style='width:95%;'></td>
				</tr>
				
				<tr>
					<td style='vertical-align:top;' width='200'><b>Add a little more detail (optional):</b></td>
					<td><textarea name='question' rows='10' style='width:95%;'><?=set_value('question')?></textarea></td>
				</tr>
				
				<tr>
					<td width='200'><b>Choose A Main Category:</b></td>
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
					<td valign='top' width='150'><b>Select A Sub-Category:</b></td>
					<td>
					
						<div id='subcategory_div'><span style='color:#C0C0C0;'><i>Select a main category above...</i></span></div>
					
					</td>
				</tr>
				
				<tr>
					<td width='200'><b>Please select a time frame for your question to remain open:</b></td>
					<td>
					
						<select name="timeframe">
							<option value="1" <?=set_select('timeframe', '1')?>>1 day</option>
							<option value="2" <?=set_select('timeframe', '2')?>>2 day</option>
							<option value="3" <?=set_select('timeframe', '3')?>>3 day</option>
							<option value="5" <?=set_select('timeframe', '5')?>>5 day</option>
							<option value="7" <?=set_select('timeframe', '7', TRUE)?>>1 week</option>
							<option value="14" <?=set_select('timeframe', '14')?>>2 weeks</option>
							<option value="21" <?=set_select('timeframe', '21')?>>3 weeks</option>
							<option value="28" <?=set_select('timeframe', '28')?>>4 weeks</option>
						</select>
						
					</td>
				</tr>
				
				<tr>
					<td width='200'><b>Your Price Range:</b></td>
					<td>
						<select name="price">
							<option <?=set_select('price','$5.00 - $25.00')?>>$5.00 - $25.00</option>
							<option <?=set_select('price','$25.00 - $50.00')?>>$25.00 - $50.00</option>
							<option <?=set_select('price','$50.00 - $100.00')?>>$50.00 - $100.00</option>
							<option <?=set_select('price','Not Sure - Let the Expert Decide')?>>Not Sure - Let the Expert Decide</option>
						</select>
					</td>
				</tr>
				
				<tr>
					<td width='200'><b>Deadline:</b></td>
					<td><input type='checkbox' name='deadline' value='1' <?=set_checkbox('deadline','1')?>> Yes</td>
				</tr>
				
				<!-- Deadline Box -->
				<tbody id='div_deadline' style='<?=(set_value('deadline')=='1' ? "" : "display:none;")?>'>
				
					<tr>
						<td width='200'><b>Select Your Deadline Date:</b></td>
						<td>
							<input type='text' style='width:150px;' name='date' value='<?=set_value('date', date("m/d/Y @ h:i A", strtotime("+3 day")))?>' class='datetime'>
						</td>
					</tr>
				
				</tbody>
				
				<tr>
					<td width='200'>&nbsp;</td>
					<td><input type='submit' name='submit' value='Send Question' class='btn btn-large btn-warning'></td>
				</tr>
			
			</table>
		
		</form>
	
	</div>
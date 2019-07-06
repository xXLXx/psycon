
	<style>
	
		.special_td .std{ padding:20px; border-bottom:solid 1px #BBB; }
		.special_td:last-child .std{ padding:20px; border-bottom:none; }
		
		.tb{ padding:10px; border:solid 1px #BBB; }
	
	</style>
	
	<?
	
		$hidden_fields = array();
		
		if(isset($specs['hide_fields']))
		{
		
			$hidden_fields = explode(',', $specs['hide_fields']['value']);
		
		}
	
		$cancel_button = "/vadmin/main/overview/{$nav['id']}/".($subnav['id'] ? $subnav['id'] : '0');
	
	?>

	<form action='/vadmin/main/add_record_submit/<?=$nav['id']?>/<?=($subnav['id'] ? $subnav['id'] : '0')?>' method='POST' enctype="multipart/form-data" style="margin:0;padding:0;">
	
		<div class='round-top blue_box_nr' style='margin:10px 0 0;border-bottom:none;'>
		
			<table width='100%'>
			
				<tr>
					<td><h1>Add Record > <?=$nav['title']?></h1></td>
					<td align='right'>
					
						<a href='<?=$cancel_button?>' class='btn btn-small'>Cancel</a> &nbsp; &nbsp;
						<input type='submit' value='Add'  class='btn btn-small btn-inverse'>
					
					</td>			
				</tr>
			
			</table>
		
		</div>
		
		<div class='white_box_nr' style='padding:0;'>
		
			<?
			
				$brige_field = (isset($brige) ? $bridge : "");
			
				$fieldData = "<table width='100%' cellPadding='10' cellspacing=\"0\">";
				
					
					foreach($fields as $f)
					{
					
						if(in_array($f,$hidden_fields))
						{
						
							// Field was hidden, so skip it
						
						}
						else
						{
					
							$formattedField = (isset($specs['title'][$f]) ? $specs['title'][$f]['value'] : ucwords(str_replace("_"," ", $f)));
							$caption = (isset($specs['caption'][$f]) ? "<div class='caption'>{$specs['caption'][$f]['value']}</div>" : "");
							
							// Get Field Based On SPEC Data
							$spec_type = (isset($specs['spec'][$f]) ? $specs['spec'][$f]['value'] : "TB||50");
							$spec_array = explode("||", $spec_type);
							
							// Load & Configure The Module
							$specMod = strtolower(trim( $spec_array[0] ));
							if($f=='id') $specMod = 'lb';
							
							// Load Field
							$this->$specMod->config($f,(empty($data[$f]) ? null : $data[$f]),$spec_array);
							$fieldView = $this->$specMod->field_view();
						
							$fieldData .= "
							<tr class='special_td'>
								<td class='std' valign='top' width='200 style='background:#E0E0E0;'><b>{$formattedField}</b>{$caption}</td>
								<td class='std' valign='top'>{$fieldView}</td>
							</tr>
							";
						
						}
					
					}
				
				$fieldData .= "</table>";
				
				echo $fieldData;
			
			?>
	
		</div>
		
		<div class='round-bottom blue_box_nr' style='border-top:none;'>
		
			<table width='100%'>
			
				<tr>
					<td align='right'>
					
						<input type='submit' value='Add' class='btn btn-small btn-inverse'>
					
					</td>			
				</tr>
			
			</table>
		
		</div>
		
	</form>
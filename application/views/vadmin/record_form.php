
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
		
		// Check if this page is a direct edit or edit through navigation
		// Simpy by checking to existence of $nav['id']
		
		if(isset($nav['id']))
		{
		
			// Through Nav
			$form_action = "/vadmin/main/save_record/{$nav['id']}/".($subnav['id'] ? $subnav['id'] : '0')."/{$data['id']}";
			
			// Buttons
			$cancel_button = "/vadmin/main/overview/{$nav['id']}/".($subnav['id'] ? $subnav['id'] : '0');
			$clone_button = "/vadmin/main/clone_record/{$nav['id']}/".($subnav['id'] ? $subnav['id'] : '0')."/{$data['id']}";
			$delete_button = "/vadmin/main/delete_record/{$nav['id']}/".($subnav['id'] ? $subnav['id'] : '0')."/{$data['id']}";
		
		}
		else
		{
		
			// Direct Edit
			$form_action = "/vadmin/main/direct_save_record/{$nav['table']}/{$data['id']}";
			
			// Buttons
			$cancel_button = "/vadmin/main/cancel_direct_action";
			$clone_button = "/vadmin/main/direct_clone/{$nav['table']}/{$data['id']}";
			$delete_button = "/vadmin/main/direct_delete/{$nav['table']}/{$data['id']}";
			
		
		}
	
	?>
	
	<script>
	
		$(document).ready(function()
		{
		
			<? if($this->uri->segment('4')=='7'){ ?>
		
			var htmlContent = "<p><?=str_replace(array("\n","\r","\t")," ","
			
				<p><b>Mobile Token:</b> {$data['mobile_token']}</p>
				<p><b>Date Registered:</b> ".(!$data['date_registered']&&$data['date_registered']!='0000-00-00 00:00:00' ? date("m/d/Y h:i A", strtotime($data['date_registered'])) : "N/A")."</p>
				<p><b>Last Payment Date:</b> ".(!$data['last_payment_date']&&$data['last_payment_date']!='0000-00-00 00:00:00' ? date("m/d/Y h:i A", strtotime($data['last_payment_date'])) : "N/A")."</p>
				<p><b>Last Login:</b> ".(!$data['last_login_date']&&$data['last_login_date']!='0000-00-00 00:00:00' ? date("m/d/Y h:i A", strtotime($data['last_login_date'])) : "N/A")."</p>
			
			")?></p>";
			
			<? } ?>

		});
	
	</script>

	
		<div class='round-top blue_box_nr' style='margin:10px 0 0;border-bottom:none;'>
		
			<table width='100%'>
			
				<tr>
					<td><h1>Modify Record #<?=$data['id']?> > <?=$nav['title']?></h1></td>
					<td align='right'>
					
						<a href='<?=$cancel_button?>' class='btn btn-small'>Cancel</a> &nbsp; &nbsp;
						<a href='<?=$delete_button?>'  class='btn btn-small btn-danger' onclick="Javascript:return confirm('Are you sure you want to delete this record?');">Delete</a> &nbsp; &nbsp; 
						<input type='submit' value='Save' class='btn save_button btn-small btn-inverse'>
					
					</td>			
				</tr>
			
			</table>
		
		</div>
		
		<div class='white_box_nr' style='padding:0;'>
		
			<?

            if($this->uri->segment('4')=='6')
            {

                $this->member->set_member_id($this->uri->segment('6'));
                $minute_balance = $this->member_funds->minute_balance();
                $email_balance = $this->member_funds->email_balance();

                echo "
					<div style='padding:20px; background:#E0E0E0;' align='center'>

					    <table style='text-align:left;width:450px;'>
                            <tr>
                                <td>Minute Balance</td>
                                <td align='right'>{$minute_balance}</td>
                            </tr>
                            <tr>
                                <td>Email Balance</td>
                                <td align='right'>$".number_format($email_balance, 2)."</td>
                            </tr>
                            <tr><td colspan='2'><hr /></td></tr>
                            <tr>
                                <td align='center'>
                                    <a href='/register/force_login/{$this->uri->segment('6')}/7856754232' target='_blank' class='btn btn-inverse'>Login As User</a>
                                </td>

                                <td align='center'>
                                    <a href='/vadmin/main/transactions/{$data['id']}' class='btn btn-inverse'>Transactions & Chat History</a>
                                </td>
                            </tr>
                        </table>

					</div>
					";

            }
                echo "<form class='edit_form' action='{$form_action}' method='POST' enctype='multipart/form-data' style='margin:0;padding:0;'>";
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
			
			
				if($sidemodules)
				{
				
					// Sidemodules
					
					echo "
					<table width='100%' cellPadding='10' cellSpacing='2'>
					
						<tr>
							<td valign='top'>{$fieldData}</td>
							<td valign='top' width='300' class='off_white_box'>";
							
								// Loop through sidemodules
								foreach($sidemodules as $s)
								{
								
									$moduleArray = null;
									$moduleArray = explode("||", $s);
									
									$tableName = $moduleArray[0];
									$bridgeField = $moduleArray[1];
									$totalShowFields = (count($moduleArray)-2);
									
									if( trim($tableName) )
									{
									
										$tableSpecs = $this->vadmin->get_table_specs($tableName);
									
										echo "<div class='white_box' style='margin-bottom:15px;'>
										
											<table width='100%' cellPadding='0' cellSpacing='0' style='border-bottom:solid 1px #BBB;padding-bottom:5px;margin-bottom:5px;'>
											
												<tr>
													<td><h1>".(isset($tableSpecs['module_name']['value']) ? $tableSpecs['module_name']['value'] : $tableName)."</h1></td>
													<td align='right'><a href='/vadmin/main/add_direct_record/{$tableName}/".$nav['id']."/".($subnav['id'] ? $subnav['id'] : '0')."/{$data['id']}'>Add New</a></td>
												</tr>
												
											</table>";
									
										// Get Data
										$getFields = $this->db->query("SELECT * FROM {$tableName} WHERE `{$bridgeField}`={$data['id']} ");
										
										if($getFields->num_rows()==0)
										{
										
											echo "<div style='color:#BBB;'>There are no ".(isset($tableSpecs['module_name']['value']) ? $tableSpecs['module_name']['value'] : $tableName)."</div>";
										
										}
										else
										{
											
											echo "<table width='100%' cellSpacing='0' cellPadding='0'>";
										
											foreach($getFields->result_array() as $d)
											{
											
												echo "<tr class='special_td'>";
											
												for($i = 1; $i <= $totalShowFields; $i++)
												{
												
													$f = $moduleArray[($i+1)];
												
													// Get Field Based On SPEC Data
													$spec_type = (isset($tableSpecs['spec'][$f]) ? $tableSpecs['spec'][$f]['value'] : "TB||50");
													$spec_array = explode("||", $spec_type);
													
													// Load & Configure The Module
													$specMod = strtolower(trim( $spec_array[0] ));
													if($f=='id') $specMod = 'lb';
													
													// Load Field
													$this->$specMod->config($f,$d[$f],$spec_array);
													$displayView = $this->$specMod->display_view();
												
													echo "<td class='std'>{$displayView}</td>";
													
													echo "<td class='std' align='center' width='25'><a href='/vadmin/main/edit_record_directly/{$tableName}/{$d['id']}/".urlencode(base64_encode("/vadmin/main/edit_record/{$nav['id']}/".($subnav['id'] ? $subnav['id'] : '0')."/{$data['id']}"))."'>Edit</a></td>";
													echo "<td class='std' align='center' width='25'><a href='/vadmin/main/direct_delete/{$tableName}/{$d['id']}/".urlencode(base64_encode("/vadmin/main/edit_record/{$nav['id']}/".($subnav['id'] ? $subnav['id'] : '0')."/{$data['id']}"))."' onClick=\"Javascript:return confirm('Are you sure you want to delete this record?');\">Delete</a></td>";
													
												}
												
												echo "</tr>";
											
											}
											
											echo "</table>";
										
										}
										
										echo "</div>";
									
									}
								
								}
							
							echo "</td>
						</tr>
					
					</table>";
				
				}
				else
				{
				
					// No Modules
					echo $fieldData;
					
				}
			
			?>
	
			
		
		</div>
		
		<div class='round-bottom blue_box_nr' style='border-top:none;'>
		
			<table width='100%'>
			
				<tr>
					<td align='right'>
					
						<input type='submit' value='Save' class='btn btn-small btn-inverse'>
					
					</td>			
				</tr>
			
			</table>
		
		</div>
		
	</form>
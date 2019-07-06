
	<style>
	
		.special_td .std{ padding:20px; border-bottom:solid 1px #BBB; }
		.special_td:last-child .std{ padding:20px; border-bottom:none; }
		
		.tb{ padding:10px; border:solid 1px #BBB; }
	
	</style>

	<div class='round-top blue_box_nr' style='margin:10px 0 0;border-bottom:none;'>
	
		<table width='100%'>
		
			<tr>
				<td><h1><?=(!$subnav ? $nav['title'] : $nav['title'] . " > " . $subnav['title'])?></h1></td>
				<td align='right'>
					<form method="POST" action="/vadmin/search/index/<?=$nav['id']?>/<?=($subnav['id'] ? $subnav['id'] : '0')?>" class="form-inline" style="display: inline">
						<input type="text" name="query" value="<?= isset($query) ? $query : '' ?>">
						<input type="submit" value="<?= !$subnav ? $nav['title'] : $subnav['title'] ?> Search" class="btn btn-small">
					</form>
				
					<?
					
						// If this is the MEMBERS page
						// Include a search button
//						if($this->uri->segment('4')=='7')
//						{
						
//							echo "<a href='/vadmin/members/search' class='btn btn-small'>" . (!$subnav ? $nav['title'] : $subnav['title']) . " Search</a>";
						
//						}
					
					?>

                    <? if($this->uri->segment('5')=='21'): ?>
                        <a href='/vadmin/main/purge_old' onClick="Javascript:return confirm('Are you sure you want to delete ALL old members?');" class="btn btn-danger btn-mini">Purge ALL Old Members</a>
                    <? endif; ?>

					<a href='/vadmin/main/add_record/<?=$nav['id']?>/<?=($subnav['id'] ? $subnav['id'] : '0')?>' class='btn btn-small btn-inverse'>Add Record</a> &nbsp; &nbsp; 
					
					<?=($pagination ? "<span class='pagination'>{$pagination}</span>" : "")?>
				
				</td>			
			</tr>
		
		</table>
	
	</div>
	
	<div class='round-bottom white_box_nr' style='padding:0;'>
	
		<div style='background:#EFECE5;padding:5px 5px 5px 15px;border-bottom:solid 1px #BBB;'><?=number_format($total_results)?> Record(s) Found</div>

		<?
		
			if(!$data)
			{
			
				// No Data
				echo "<p style='padding:10px;'>There is no data to display in ".(!$subnav ? $nav['title'] : $subnav['title'])." </p>";
			
			}
			else
			{
			
				echo "<table width='100%' cellPadding='10' cellspacing='0'>";
			
				// List Data
				foreach($data as $d)
				{
				
					echo "<tr class='special_td'>";
				
						foreach($fields as $f)
						{
						
							// Get Field Based On SPEC Data
							$spec_type = (isset($specs['spec'][$f]) ? $specs['spec'][$f]['value'] : "TB||50");
							$spec_array = explode("||", $spec_type);
							
							// Load & Configure The Module
							$specMod = strtolower(trim( $spec_array[0] ));
							if($f=='id') $specMod = 'lb';
							
							// Load Field
							$this->$specMod->config($f,$d[$f],$spec_array);
							$displayView = $this->$specMod->display_view();
						
							echo "<td class='std'>{$displayView}</td>";
						
						}

                        //---
                        if(!empty($nav['id']) && $nav['id'] == '13'){

                            $transactionLabel = "";

                            switch($d['settled']){

                                case "voided":
                                    $transactionType = 'voided';
                                    $transactionLabel = "<span class='label label-important'>Voided</span>";
                                    break;

                                case "settled":
                                    $transactionType = 'settled';
                                    $transactionLabel = "<span class='label label-success'>Settled</span>";
                                    break;

                                default:
                                    $transactionType = 'pending';
                                    $transactionLabel = "<span class='label label-warning'>Pending</span>";
                                    break;

                            }

                            echo "<td class='std'>{$transactionLabel}</td>";

                        }

                        //---
                        if(!empty($transactionType)){

                            if($transactionType=='pending'){
                                echo "<td class='std' align='center' width='25'><a href='/vadmin/main/finalize_transaction/void/{$d['id']}' class='btn btn-inverse btn-small' onClick=\"Javascript:return confirm('Are you sure you want to manually void this transaction?');\">Void</a></td>";
                                echo "<td class='std' align='center' width='25'><a href='/vadmin/main/finalize_transaction/settle/{$d['id']}' class='btn btn-info btn-small' onClick=\"Javascript:return confirm('Are you sure you want to manually settle this transaction?');\">Settle</a></td>";
                            }else{
                                echo "<td class='std' align='center' width='25'></td>";
                                echo "<td class='std' align='center' width='25'></td>";
                            }

                        }
						
						echo "<td class='std' align='center' width='25'><a href='/vadmin/main/edit_record/{$nav['id']}/".($subnav['id'] ? $subnav['id'] : '0')."/{$d['id']}' class='btn btn-small'>Edit</a></td>";
						echo "<td class='std' align='center' width='25'><a href='/vadmin/main/delete_record/{$nav['id']}/".($subnav['id'] ? $subnav['id'] : '0')."/{$d['id']}' class='btn btn-small btn-danger' onClick=\"Javascript:return confirm('Are you sure you want to delete this record?');\">Delete</a></td>";
						
					echo "</tr>";
				
				}
				
				echo "</table>";
			
			}
		
		?>
	
	</div>
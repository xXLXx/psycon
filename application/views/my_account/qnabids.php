
	<?
	
		$tallyO = $this->db->query
		("
			SELECT
				(SELECT COUNT(id) FROM qna_bids WHERE expert_id = {$this->member->data['id']} AND awarded=1 AND answer IS NULL) as awarded_bids
				
			FROM
				qna_bids
				
			WHERE
				expert_id = {$this->member->data['id']}
		");
		
		$tally = $tallyO->row_array();
		
		$awarded_bids = (isset($tally['awarded_bids']) ? $tally['awarded_bids'] : 0);
	
	?>

	<div class="horizontal_bar">
		<a href="/my_account/qnabids">Pending</a>
		<a href="/my_account/qnabids/index/awarded">Awarded <?=($awarded_bids>0?"<span style='color:#000;font-size:10px;'>({$awarded_bids})</span>":"")?></a>
		<a href="/my_account/qnabids/index/answered">Answered</a>
	</div>

	<div class='padded'>

		<h2><?=$status?></h2>
		<div><?=$status_description?></div>
		<div>&nbsp;</div>
		
		<?
		
			if($bids)
			{
			
				echo "<table class='table table-striped table-hover'>";
			
				foreach($bids as $b)
				{
				
					echo "
					<tr>
						<td width='150'>".date("m/d/y @ h:i A", strtotime($b['datetime']))."</td>
						<td><b>{$b['question_title']}</b></td>
						<td style='text-align:right;'><a href='/my_account/qnabids/view/{$b['id']}' class='btn'>View</a></td>
					</tr>
					";
				
				}
				
				echo "</table>";
			
			}
			else
			{
			
				echo "<div>You do not have any {$status} bids</div>";
			
			}
		
		?>
	
	</div>
	
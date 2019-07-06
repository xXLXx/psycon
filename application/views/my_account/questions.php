
	<?
	
		$tallyQuestions = $this->db->query
		("
			SELECT
				(SELECT COUNT(id) FROM questions WHERE expert_id = {$this->member->data['id']} AND status='new' AND (deadline IS NULL OR deadline >= '".date("Y-m-d H:i:s")."')) as new_questions,
				(SELECT COUNT(id) FROM questions WHERE expert_id = {$this->member->data['id']} AND status='pending') as pending_questions
			FROM
				questions
			WHERE
				expert_id = {$this->member->data['id']}
		");
		
		$tally = $tallyQuestions->row_array();
		
		$new_questions = $tally['new_questions'];
		$pending_questions = $tally['pending_questions'];
	
	?>

	<div class="horizontal_bar">
		<a href="/my_account/questions">New Questions <?=($new_questions>0?"<span style='color:#000;font-size:10px;'>({$new_questions})</span>":"")?></a>
		<a href="/my_account/questions/index/pending">Pending <?=($pending_questions>0?"<span style='color:#000;font-size:10px;'>({$pending_questions})</span>":"")?></a>
		<a href="/my_account/questions/index/answered">Answered</a>
		<a href="/my_account/questions/index/declined">Declined</a>
	</div>

	<div class='padded'>

		<h2><?=$status?> Questions</h2>
		
		<div style='margin-bottom:15px;'><?=$status_description?></div>
		
		<?
		
			if($questions)
			{
			
				echo "<table class='table table-striped table-hover'>";
			
				foreach($questions as $q)
				{
				
					echo "
					<tr>
						<td>{$q['title']}</td>
						<td style='text-align:right;'><a href='/my_account/questions/view/{$q['id']}' class='btn'>View</a></td>
					</tr>
					";
				
				}
				
				echo "</table>";
			
			}
			else
			{
			
				echo "<div>You do not have any {$status} questions</div>";
			
			}
		
		?>
		
		<div>&nbsp;</div>
	
	</div>
	
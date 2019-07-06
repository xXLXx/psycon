
	<? $this->load->view('faqs/select') ?>
	
	<hr />
	
	<div class='content_area'>
		
		<ul id='faq_list'>
		<?
		
			foreach($faqs as $q)
			{
			
				echo "
				<li>
					<div class='title'><a href='/'>{$q['question']}</a></div>
					<div class='answer'>{$q['answer']}</div>
				</li>";
			
			}
		
		?>
		</ul>
	
	</div>
	
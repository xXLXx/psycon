
	<style>
	
		.large_anchor{ color:#489006 !important; font-size:22px !important; text-decoration:none !important; }
	
		#faq_list{ margin:0; padding:0; }
		#faq_list li{ list-style:none; margin-bottom:25px; }
		#faq_list li a{ color:orange !important; font-size:14px !important; text-decoration:none !important; }
		#faq_list li .answer{ display:none; padding:10px 0; }
	
	</style>
	
	<script>
	
		$(function()
		{
		
			$('#faq_list .title').click(function(e)
			{
			
				e.preventDefault();
				
				$(this).parent().find('.answer').toggle();
			
			});
		
		});
	
	</script>

	<div class='content_area' align='left'>
	
		<?=$this->load->view('qnabids/header')?>
	
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
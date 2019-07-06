
	<style>
	
		.large_anchor{ color:#489006 !important; font-size:22px !important; text-decoration:none !important; }
	
		#faq_list{ margin:0; padding:0; }
		#faq_list li{ list-style:none; margin-bottom:25px; }
		#faq_list li a{ color:orange !important; font-size:18px !important; text-decoration:none !important; }
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

	<div class='content_area' align='center'>
	
		<div style='margin:25px 0 0;'>
		<?=($this->uri->segment('2')=='clients' ? "<a href='/faqs/clients' class='large_anchor' style='color:#000 !important;'>Client FAQs</a>" : "<a href='/faqs/clients' class='large_anchor'>Client FAQs</a>")?> &nbsp;  &nbsp;  &nbsp;   &nbsp;  &nbsp; 
		<?=($this->uri->segment('2')=='readers' ? "<a href='/faqs/readers' class='large_anchor' style='color:#000 !important;'>Readers FAQs</a>" : "<a href='/faqs/readers' class='large_anchor'>Readers FAQs</a>")?>
		</div>
		
	</div>
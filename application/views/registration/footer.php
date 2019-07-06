
		</div>
		<img src="/media/images/content-bottom.gif" style="display: block; float: left;" />
	
	
		<div class="footer">
			
			<div class='links' align="center">

                <a href="/psychics">Our Psychics</a>
                <a href="/phone_readings">Phone Readings</a>
                <a href="/my_account/email_readings">Email Readings</a>
                <a href="/articles">Articles</a>
                <a href="/blog">Psychic Blog</a>
                <a href="/prices">Prices</a>
                <a href="https://devpsychiccontact.zendesk.com/hc/en-us/sections/200407960-FAQ" target="_blank">Support</a>
				<?
				
					foreach($this->system_vars->get_pages('footer') as $p)
					{
					
						echo "<a href=\"/{$p['url']}\"  class=\"bottom_nav\">{$p['title']}</a>";
					
					}
				
				?>
			</div>
		
			<p class="copyright">

				<img src="/media/images/geo-trust-logo.jpg" /><img src="/media/images/paypal-logo.gif" />
				
				<a href="http://www.securitymetrics.com/site_certificate.adp?s=www%2epsychic-contact%2ecom&amp;i=991461" target="_blank" >
					<img src="http://www.securitymetrics.com/images/sm_ccsafe_wh.gif" alt="SecurityMetrics for PCI Compliance, QSA, IDS, Penetration Testing, Forensics, and Vulnerability Assessment" border="0">
				</a>
			
				Copyright &copy 1998 - <?=date('Y')?>. All rights reserved<br />Psychic Contact/Jayson Lynn.Net Inc.
			</p>
			
		</div> 
	
	</div>
    <!-- Now, loading heavy JS scripts -->
    <script data-main="/chat/app/start_main_lobby.js" src="/chat/app/require.js"></script>
    
    
</body>
</html>
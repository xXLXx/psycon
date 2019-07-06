
    <div class="banner"><img style="float: right;" src="/media/images/banner-right.jpg"><img style="float: left;" src="/media/images/banner-left.jpg">
        <div class="text">
              <span style="font-family:Arial Narrow, sans-serif; color: #2e4194; font-size: 22px; margin: 0px; font-weight: normal; padding: 0 0 20px 0px;">
                  <font style="font-size: 28px;line-height: 35px;">Accurate, Compassionate,
                      Professional &amp; Ethical Psychic Readers.</font><br>
        <div style="padding-top: 13px; padding-bottom: 13px;">LIVE PSYCHIC READINGS 24/7</div></span>
            <p style="padding: 0 10px 0 0">New Client Special:
                <br>10 Minutes $1.99
                <br><span style="font-size: 11px; font-weight: normal;">(New Users Only) </span>
                <br><a href="/psychics"><img style="padding-top: 10px;" src="/media/images/chat-now-button.jpg"></a></p>
            <p>Repeat Client Special:
                <br>20 Minutes $27.99
                <br><br>
                <a href="/register/login">
                    <img style="padding-top: 10px;" src="/media/images/login-button.jpg">
                </a>
            </p>
        </div>
        <div id="slide-container">
            <div id="slideshow">
                <img src="/media/images/banner02.jpg" style="display:none;">
                <img src="/media/images/banner03.jpg" style="display:none;">
            </div>
        </div>
    </div>

	<div class='content'>
		
		<!-- Side Content -->
		<div class='side_content'>			
			<?=$this->load->view('pages/featured_readers', null, TRUE)?>		
		</div>
		
		<!-- Main Content -->
		<div class="main_content"> 
			
			<div>
				<?=utf8_encode(html_entity_decode($content))?>
			</div>

		</div>
		
		<div class='clear'></div>
	
	</div>

    <script>

        var current = 1;
        var timeout = 5000;
        var imageArray = [];
        var totalImages = 0;

        $(function(){

            $('#slideshow img').each(function(i, object){
                imageArray.push(object);
            });

            //--- Set the animation
            totalImages = imageArray.length;
            setInterval(showSlideshowImage, timeout);
            showSlideshowImage();

        });

        function showSlideshowImage(){

            var objectImage = imageArray[current-1];
            var lastObjectImage = imageArray[(current == 1 ? totalImages-1 : current)];

            $(lastObjectImage).fadeOut('slow');
            $(objectImage).fadeIn('slow');

            current = (current == totalImages ? 1 : current+1);

        }

    </script>
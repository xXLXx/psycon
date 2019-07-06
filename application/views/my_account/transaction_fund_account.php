
	<style>
	
		.profile_selector{ width:300px; }
		.profile_selector a{}
	
	</style>

	<script>
	
		$(document).ready(function(){
		
			$(document).on('click', '.make-payment', function(e){
			
				e.preventDefault();
				
				var paymentType = $(this).attr('data-payment-method');
				var packageSelected = $("input[name='package']:checked").val();

				if(paymentType == 'paypal'){
				
					window.location = '/my_account/transactions/fund_with_paypal/' + packageSelected;
				
				}else{
				
					if(confirm('By clicking OK below, you are allowing PsychicContact to charge your credit card. Do you want to continue?')){
				
						var billing_profile_id = $(this).attr('data-billing-profile-id');
				
						window.location = '/my_account/transactions/submit_deposit/'+billing_profile_id+'/' + packageSelected;
					
					}
				
				}
			
			});
		
			$("input[name='package']").change(function()
			{
			
				var value = $(this).val();

                //var promoFlag = $(this).attr("promo");
                // paypal for all packages
                var promoFlag = 0;



				if(value=='')
				{
				
					$('#billing_profile_div').html("");
				
				}
				else
				{
				
					$.get('/my_account/transactions/get_billing_profiles/' + value, function(object)
					{
					
						var selectHTML = "<div class='btn-group btn-group-vertical profile_selector' data-toggle='buttons-radio'>";
							
						if(object.error == '0')
						{
							
							$(object.profiles).each(function(i)
							{
							
								selectHTML = selectHTML + "<a href='/' data-payment-method='merchant' data-billing-profile-id='"+object.profiles[i].id+"' class='btn btn-primary make-payment'>"+object.profiles[i].card_name+" (**** "+object.profiles[i].card_number+")</a>";
							
							});
						
						}
                        if(promoFlag != 1)
                        {
                            selectHTML = selectHTML + "<a href='/'  data-payment-method='paypal' class='btn btn-primary make-payment ppal'>Pay With PayPal</a>";
                        }

						selectHTML = selectHTML + "<a href='/my_account/transactions/add_billing_profile/"+object.merchant+"/" + value + "' class='btn'>Add A New Credit / Debit Card</a></div>";

						$('#billing_profile_div').html(selectHTML);
					
						if(object.error == '1')
						{
						
							// window.location = object.redirect;
						
						}
						else
						{
						
							
						
						}
					
					}, 'json');
				
				}
			
			});
		
		});
	
	</script>
	
	<h2>Fund Your Account</h2>
	
	<p style='margin-top:8px;'>Use the form below to select a package and fund your account. Please keep in mind, that based on pricing, previously entered credit cards / billing profiles may or may not be available. You may have to re-enter your billing information a second time. We apologize for this inconvenience.</p>
	
	<div align='left' class='well' style='margin-top:25px;'>

		<div id='chat_options'>
			<h2>Select A Package:</h2>
			<div style='margin:15px 0 30px;'>
			
					<?
					
						foreach($this->site->get_packages() as $r){
                            if(($r['promo'] == 1 && $this->member->data['received_promo'] != 1) || $r['promo'] == 0)
							echo "<div style='padding:5px 0 0;" . ($r['promo'] == 1 ? 'font-weight:bold; color:red;' : '') . "'><input type='radio' promo='{$r['promo']}' name='package' value='{$r['id']}' style='margin:0 5px 0 0;'> {$r['title']}</div>";
						}
					
					?>
			
			</div>
		</div>
		
		<h2>Select Payment Method</h2>
		<div id='billing_profile_div' style='margin:15px 0;'><span style='color:#999;'>Select A Package Above</span></div>

	
	</div>

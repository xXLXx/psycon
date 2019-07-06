
	<div class='content_area'>
	
		<div class='pull-left' style='width:200px;'>
			
			<div class='btn-group btn-group-vertical' style='width:200px;'>
			
				<?
                    $unread = $this->messages_model->getUnreadMessages();
                    $badge = "";
                    if($unread > 0)
                    {
                      $badge = "<span class=\"badge badge-important\" id=\"email-count\">{$unread}</span>";
                    }
					// If ths is an expert
					// Include some additional options


					// Show unread messages
					if($this->member->data['profile_id'])
					{
					
						echo "
						<a href='/my_account' class='btn btn-primary ".($this->uri->segment('2')=='' ? " active" : "")."'>Psychic Dashboard</a>
						<a href='/my_account/main/edit_profile' class='btn btn-primary ".($this->uri->segment('2')=='main' && $this->uri->segment('3') != 'client_list'  ? " active" : "")."'>Edit My Profile</a>
						<a href='/my_account/nrr' class='btn btn-primary ".($this->uri->segment('2')=='nrr' ? " active" : "")."'>Client NRRs</a>
						<a href='/my_account/ban_users' class='btn btn-primary ".($this->uri->segment('2')=='ban_users' ? " active" : "")."'>Ban Users</a>
						<a href='/my_account/articles/submit' class='btn btn-primary ".($this->uri->segment('2')=='articles' ? " active" : "")."'>Submit An Article</a>
						<a href='/my_account/chats/' class='btn btn-primary ".($this->uri->segment('2')=='chats' ? " active" : "")."'>Chat History</a>
						<a href='/my_account/main/client_list' class='btn btn-primary ".($this->uri->segment('3')=='client_list' ? " active" : "")."'>Client List</a>
						<a href='/my_account/email_readings/open_requests' class='btn btn-primary ".($this->uri->segment('2')=='email_readings' ? " active" : "")."'>Email Readings</a>
					    <a href='/my_account/main/testimonials/' class='btn btn-primary ".($this->uri->segment('2')=='main' && $this->uri->segment('3') == 'testimonials' ? " active" : "")."'>Testimonials</a>
						<!--<a href='/my_account/trancepad' class='btn btn-primary'>TrancePad</a>-->
						<a href='/profile/{$this->member->data['username']}' class='btn btn-primary'>View My Psychic Profile</a>

						<a href='/my_account/transactions/index' class='btn btn-primary ".($this->uri->segment('2')=='transactions' ? " active" : "")."'>Earnings & Transactions</a>
                         <a href='/my_account/messages' class='btn".($this->uri->segment('2')=='messages' ? " active" : "")."'>Message Center {$badge}</a>
						";
					
					}
					
					// If expert, DON'T include some of the buttons
					// They will not be neccessary
					
					if(!$this->member->data['profile_id'])
					{
					
						echo "
						<a href='/my_account' class='btn btn-primary ".($this->uri->segment('2')=='' ? " active" : "")."'>Dashboard</a>
						<a href='/my_account/transactions/fund_your_account' class='btn btn-primary ".($this->uri->segment('3')=='fund_your_account' ? " active" : "")."'>Fund My Account</a>
						<a href='/my_account/main/nrr' class='btn btn-primary ".($this->uri->segment('2')=='main' ? " active" : "")."'>NRR</a>
						<a href='/psychics' class='btn btn-primary'>Start Chat</a>
						<a href='/my_account/email_readings/client_emails' class='btn btn-primary ".($this->uri->segment('2')=='email_readings' ? " active" : "")."'>My Email Readings</a>
						<a href='/my_account/chats' class='btn btn-primary ".($this->uri->segment('2')=='chats' ? " active" : "")."'>Chat History</a>
						<!--<a href='/my_account/favorites' class='btn btn-primary ".($this->uri->segment('2')=='favorites' ? " active" : "")."'>My Favorite Readers</a>-->

						<a href='/my_account/transactions' class='btn ".($this->uri->segment('2')=='transactions'&&$this->uri->segment('3')!='fund_your_account' ? " active" : "")."'>Billing & Transactions</a>
					    <a href='/my_account/messages' class='btn".($this->uri->segment('2')=='messages' ? " active" : "")."'>Message Center {$badge}</a>
					    <a href='/my_account/account' class='btn".($this->uri->segment('2')=='account' ? " active" : "")."'>Edit My Account</a>
						";
					
					}
					
				?>
				
			</div>
		
		</div>
		
		<div class='pull-right' style='width:680px;'>
		
			<div class="content" style='padding-top:0;margin-top:0;'>
		
				
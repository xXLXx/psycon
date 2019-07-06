
	<div class='well'>
				
		<div class='pull-left' style='width:40%;'>
		
			<h2 style='margin-top:10px;padding-top:0;line-height:0;'>Hi <?=$this->member->data['first_name']?> <?=$this->member->data['last_name']?></h2>
			
			<p>Promote yourself using this URL:</p>
			
			<span class='label' style='font-size:14px;display:inline-block;padding:8px;'><?=SITE_URL ?>/profile/<?=$this->member->data['username']?></span>
		
		</div>
		<div class='pull-right' style='width:50%;text-align:right;'>
			<h2 style='margin-top:10px;padding-top:0;line-height:0;font-size:14px;'>Balance (US): <span style='color:#666;'>$<?=number_format($this->reader->get_balance('us'),2)?></span></h2>
			
			<h2 style='margin-top:10px;padding-top:0;line-height:0;font-size:14px;'>Balance (CAN): <span style='color:#666;'>$<?=number_format($this->reader->get_balance('ca'),2)?></span></h2>

			<div class="btn-group" style='margin:10px 0 0;'>
                <a id="reader_status_online_button" href='/my_account/main/set_status/online' class="btn  <?=($this->member->data['status']=='online' ? "btn-primary" : "btn-default")?>">Online</a>
                <a id="reader_status_break_button" href='/my_account/main/set_status/break' class="btn <?=($this->member->data['status']=='break' || $this->member->data['status']=='booked' || $this->member->data['status']=='busy' ||  $this->member->data['status']=='away' ? "btn-primary" : "btn-default")?>">Break</a>
                <a id="reader_status_offline_button" href='/my_account/main/set_status/offline' class="btn <?=($this->member->data['status']=='offline' ? "btn-primary" : "btn-default")?>">Offline</a>

            </div>
		
		</div>
		<div class='clearfix'></div>
		<script src="/chat/reader_my_account.js?current_status=<?=$this->member->data['status'] ?>&reader_id=<?=$this->member->data['profile_id'] ?>"></script>
	</div>

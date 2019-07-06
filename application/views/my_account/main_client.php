
	<div class='well'>
				
		<div class='pull-left' style='width:445px;'>

            <div class="pull-left" style="width:100px;">

                <div align="center">
                    <a href='/my_account/account' style="font-size:11px;"><img src="<?=$this->member->data['profile_image']?>" class="img-polaroid" width="75" /></a>
                    <div><a href='/my_account/account' style="font-size:11px;">Change Image</a></div>
                </div>

            </div>
            <div class="pull-left" style="width:250px;margin-left:10px;">

                <h2 style='margin-top:10px;padding-top:10px;line-height:0;'>Hi <?=$this->member->data['first_name']?> <?=$this->member->data['last_name']?></h2>
                <span>Username: <?=$this->member->data['username']?></span>

            </div>
            <div class="clearfix"></div>
		
		</div>
		<div class='pull-right' style='width:190px;text-align:right;'>
		
			<h2 style='margin-top:10px;padding-top:0;line-height:0;font-size:14px;'>Chat Time: <span style='color:#666;'><?=gmdate("H:i:s", ($this->member_funds->minute_balance()*60))?></span></h2>
			
			<h2 style='margin-top:10px;padding-top:0;line-height:0;font-size:14px;'>Emails Funds: <span style='color:#666;'>$ <?=number_format($this->member_funds->email_balance(), 2)?></span></h2>
			
			<div style='margin:10px 0 0;'><a href='/my_account/transactions/fund_your_account' class='btn btn-warning'>Fund My Account</a></div>
		
		</div>
		<div class='clearfix'></div>
	
	</div>

	<?

        /*
		$getActiveNews = $this->db->query("SELECT *, (SELECT count(id) FROM news WHERE active=1) as totalNews FROM news WHERE active=1 AND (type='client' OR type='all') ORDER BY id DESC");
		
		if($getActiveNews->num_rows() > 0)
		{
		
			echo "
			<h2 style='margin-top:25px;'>Latest Updates</h2>
			
			<ul style='margin-top:10px;'>";
		
			$newsArray = $getActiveNews->result_array();
		
			foreach($newsArray as $n)
			{
			
				echo "<li>{$n['text']}</li>";
			
			}
			
			echo "</ul>";
		
		}
        */
	
	?>
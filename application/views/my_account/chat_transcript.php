
	<div class='content_area'>
		
		<h2>Chat Details</h2>

		<div class='well' style="margin:15px 0 35px;">
		
			<table width='100%' cellPadding='10'>

                <tr>
                    <td><b>Chat ID:</b></td>
                    <td>#<?=$this->chatmodel->object['id']?></td>
                </tr>

                <tr>
                    <td><b>Date:</b></td>
                    <td><?=date("m/d/Y h:i A", strtotime($this->chatmodel->object['start_datetime']))?></td>
                </tr>
				
				<tr>
					<td><b>Chat Length:</b></td>
					<td><?=$this->system_vars->time_generator($this->chatmodel->object['length']); ?></td>
				</tr>
                <? if($chat_reader == 1 && $chat_amount && !$transNotFound):    ?>
                <tr>
                    <td><b>Amount Paid:</b></td>
                    <td>$<?=number_format($chat_amount,2)?></td>
                </tr>
               <tr>
                    <td>
                    <?php 
                    /*
                    echo "<pre>"; 
                    print_r($this->chatmodel); 
                    echo "</pre>"; 
                    */
                    ?>
                    <span style="color:red;">Rob working on these buttons :)</span>
                    <form action="/my_account/chats/process_refund/<?php echo $this->chatmodel->object['id']; ?>" method="POST">
                    <input type="submit" value=" Return Entire Chat Time " onclick='return confirm("Are you sure?")'>
                    </form>
                    </td>
                    <td>
                    <?php
                    	
                        $max = floor($this->chatmodel->object['length'] / 60);
                        $word = ($max == 1 ? "Minute" : "Minutes");
                        
                        if ($max)
                        {
                        // create dropdown of avail mins to return
                    ?>
                    <form action="/my_account/chats/give_timeback/<?php echo $this->chatmodel->object['id']; ?>" method="POST">
                    <input type="hidden" name="type" value="chat">
                    <select name="timeback" style="width:100px;">
                    <?php
						$min = 1;
						echo "<option value=''>-- Select --</option>/n";
						while ($min <= $max)
						{
							echo "<option value='$min'>$min</option>/n";
							$min++;
						}
                    ?>
                    </select>&nbsp;&nbsp;
                    <input type="submit" value=" Return <?php echo $word; ?> "></form>
                    <?php
                    }
                    else
                    {
                    	// no full mins available to return
                    	echo "&nbsp;";
                    }
                    ?>
                    </td>
                </tr>
                <? endif; ?>
				
			</table>
			
		</div>
        <? if($chat_reader == 1):    ?>
            <h2>NRRs</h2>
            <div style="padding:20px 0 0 20px;">
                <form class="form-inline" id="timeback_form" action="/my_account/chats/give_timeback/<?=$this->chatmodel->object['id']?>" method="POST">
                    <? $nr = $this->chatmodel->getNRRs(); ?>
                    <? if(count($nr) > 0): ?>

                        <table class="table table-striped table-bordered" style="margin:15px 0 35px;">
                            <tr>
                                <th>Type</th>
                                <th>Time Requested</th>
                                <th>Date</th>
                                <th>&nbsp;</th>
                            </tr>
                            <? foreach($nr as $n):
                                $amount = 0;
                                switch($n['type'])
                                {
                                    case "unhappy_reading":
                                        $amount = $n['unhappy_timeback'];
                                     break;

                                    case "disconnect":
                                        $amount = $n['disconnect_timeback'];
                                    break;

                                    case "slow":
                                        $amount = $n['slow_timeback'];
                                        break;


                                }
                                ?>
                                <tr>
                                    <td><?=ucwords(str_replace('_', ' ',$n['type']))?></td>
                                    <td><?=$amount?> </td>
                                    <td><?=$n['date']?></td>
                                    <td><a href="/my_account/nrr/details/<?=$n['id']?>" class="btn">Details</a></td>
                                </tr>

                            <? endforeach; ?>

                        </table>

                    <? else: ?>

                        <p>There are no NRRs for this chat.</p>

                    <? endif; ?>

                </form>
            </div>
        <? endif; ?>

		<h2>Transcript</h2>

        <? $ta = $this->chatmodel->loadTranscripts(); ?>
        <? if(count($ta) > 0): ?>

            <table class="table table-striped table-bordered" style="margin:15px 0 35px;">

                <? foreach($ta as $transcript): ?>

                    <tr>
                        <td><?=($this->session->userdata('member_logged')==$transcript['member_id'] ? "<b style=\"color:blue;\">{$transcript['username']}</b>" : $transcript['username'])?></td>
                        <td><?=($this->session->userdata('member_logged')==$transcript['member_id'] ? "<span style=\"color:blue;\">{$transcript['message']}</span>" : $transcript['message'])?></td>
                    </tr>

                <? endforeach; ?>

            </table>

        <? else: ?>

            <p>There were no messages transacted in this chat</p>

        <? endif; ?>

	</div>
<?//$ts = 3;
    if (!isset($ts)) {
        $ts = time();
    }

?>
    <!-- Chat Interface -->
	<!-- <script src="/chat/socket.io.js"></script>  -->
	<script src="http://dev.psychic-contact.com:3701/auth.js"></script>
    <script src="http://dev.psychic-contact.com:3701/socket.io/socket.io.js"></script>
    <link rel=stylesheet type="text/css" href="/media/css/chat.css?ts=<?=$ts ?>">
    <? 

    if ($detect->isMobile() ) {
        // mobile CSS
        $is_mobile = 1;
   ?>
    <link rel="stylesheet" media="all and (orientation:portrait)" href="/media/css/iphone-portrait.css?ts=<?=$ts ?>">
    <link rel="stylesheet" media="all and (orientation:landscape)" href="/media/css/iphone-landscape.css?ts=<?=$ts ?>">
   <?
}  else {
        $is_mobile = 0;
    ?>
    <link rel=stylesheet type="text/css" href="/media/css/web.css?ts=<?=$ts ?>">
    <?
}
?>
    
    

    <? if($this->session->userdata('member_logged')): ?>

        <? $socket_url = "http://dev.psychic-contact.com"; ?>
        <? $socket_port = 3701 ?>
        <? $mem_id = $member['id']; ?>
        <? $member_username = $this->member->data['username']; ?>
        
       
        <script>
            var chat_init_data ={
                'chat_title'        : '<?=$title?>',
                'member_type'       : '<?=$member_type?>',
                'member_id'         : '<?=$mem_id?>',
                'member_hash'       : '<?=$member_hash?>',
                'member_username'   : '<?=$member_username?>',
                //'_disconnect_url'   : '<?=$this->config->item('site_url') . "/main/disconnect_user/" . $mem_id?>',
                'socket_url'        : '<?=$socket_url ?>',
                'socket_port'       : '<?=$socket_port ?>',
                // now room data
                'chat_id'           : '<?=$id ?>',
                'chat_session_id'   : '<?=$chat_session_id ?>',
                'max_chat_length'     :  <?=floor($max_chat_length)?>,
                'chat_length'       :  <?=floor($chat_length)?>,
                'time_balance'       :  <?=floor($time_balance)?>,
                'client_username'  :  "<?=$client['username'] ?>",
                'client_first_name' :  "<?=$client['first_name'] ?>",
                'client_last_name'  :  "<?=$client['last_name'] ?>",
                'client_dob'        :  "<?=date("m/d/Y", strtotime($client['dob']))?>",
                
                'reader_username'  :  "<?=$reader['username'] ?>",
                
                
                'is_mobile'         : <?= $is_mobile ?>
            };
            
        </script>
        <!-- move chat lobby loading script to 
        <script src="/chat/chatmonitor.js?time=<?=time()?>"></script>
        -->
    <? endif; ?>

    <!--
    <script>

        //--- Chat Paramaters
        var intervalID = <?=$id?>;
        var chatSessionId = <?=$id?>;
        var reader_id = <?=$reader_id?>;
        var client_id = <?=$client_id?>;
        var member_id = <?=$member['id']?>;
        var member_hash = '<?=$member_hash?>';
        var chat_time = <?=floor($time_balance)?>;
        var max_chat_time = <?=floor($time_balance)?>;

        //--- Client Information
        var client =
        {
            'username' : '<?=$client['username']?>',
            'firstName' : '<?=$client['first_name']?>',
            'lastName' : "<?=$client['last_name']?>",
            'dob' : "<?=date("m/d/Y", strtotime($client['dob']))?>"
        };

        //--- Reader Information
        var reader =
        {
            'username' : '<?=$reader['username']?>'
        };

        // Initialize Chat and get things started
        $(function()
        {

            resizeChat();
            $(window).resize(resizeChat);

        });

        function resizeChat()
        {
            var windowHeight = $(window).height() - $('#navbar').outerHeight() - $('#footer').outerHeight() - 115;
            $('#chat_window').height(windowHeight);
            $("#footer textarea").width($(window).width() - 200);
        }

    </script>

    <script src="/chat/interface.js?time=<?=time()?>"></script>
    -->
    
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.11/jquery.scrollTo.min.js"></script>

    <div id="chat_room">
        <!-- Navigation Bar -->
        <div id='navbar' class="navbar navbar-inverse">
            <div class="navbar-inner">
                <div class="container">
                    <?   
                        if ($member_type == 'client') {
                            $chat_with = $reader['username'];
                        } else {
                            $chat_with = $client['username'];
                        }
                    ?>
                    <a class="brand" href="#"><span class="chat_with_text">Chat With <span class='client_first_name'><?=$chat_with ?></span></span></a>
    
                    <ul id='chatMenu' class="nav pull-right">
                        <li id='clientInfo' class='dropdown'>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">Client Info <b class="caret"></b></a>
                            <div class="dropdown-menu">
    
                                <div style='padding:0 10px 10px;width:250px;'>
    
                                    <legend><span class='client_username'><?=$client['username'] ?></span></legend>
    
                                    <table>
    
                                        <tr>
                                            <td width='75'>DOB:</td>
                                            <td><span class='client_dob'><?=date("m/d/Y", strtotime($client['dob'])) ?></span></td>
                                        </tr>
    
                                        <tr>
                                            <td width='75'>Name:</td>
                                            <td><span class='client_first_name'><?=$client['first_name'] ?></span> <span class='client_last_name'><?=$client['last_name'] ?></span></td>
                                        </tr>
    
                                        <tr>
                                            <td width='75'>Topic:</td>
                                            <td><span><?=$topic?></span></td>
                                        </tr>
    
                                    </table>
    
                                </div>
    
                            </div>
                        </li>
                        <li class="divider-vertical"></li>
                        <li><a>Chat Type: Standard</a></li>
                        <li class="divider-vertical"></li>
                        <li><a>Timer: <span id='timerSpan'>00:00:00</span></a></li>
                        <li class="divider-vertical"></li>
                        <li class="dropdown">
                            <a id='settings_menu' data-toggle="dropdown" class="dropdown-toggle" href="#">Options <b class="caret"></b></a>
                            <ul id='settingsDropDownMenu' class="dropdown-menu">
                                <? if ($member_type == 'client') { ?>
                                    <li><a href="#" class='pauseChatAnchor' style='display:none;'>Pause Chat</a></li>
                                    <li><a href="#" class='resumeChatAnchor' style='display:none;'>Resume Chat</a></li>
                                    <li><a href="#" class='contactAdmin'>Contact Admin</a></li>
                                    <!--<li><a href="#" class='purchaseMoreTime'>Purchase More Time</a></li> -->
                                    <li><a href="#" class='addStoredTime' style='display:none;'>Add More Time</a></li>
                                    <li><a href="#" class='endChatAnchor' style='display:none;'>End Chat</a></li>
                                    
                                <? } else { ?>
                                    <li><a href="#" class='pauseChatAnchor' style='display:block;'>Pause Chat</a></li>
                                    <li><a href="#" class='resumeChatAnchor' style='display:none;'>Resume Chat</a></li>
                                    <li><a href="#" class='refundChatAnchor' style='display:block;'>Refund Chat</a></li>
                                    <li><a href="#" class='addLostTime' style='display:block;'>Add Lost Time</a></li>
                                    <li><a href="#" class='personalBanUserAnchor' style='display:block;'>Partial Ban User</a></li>
                                    <li><a href="#" class='fullBanUserAnchor' style='display:block;'>Full Ban User</a></li>
                                    <li><a href="#" class='endChatAnchor' style='display:none;'>End Chat</a></li>
                                <? } ?>        
                                
    
                            </ul>
                        </li>
                    </ul>
    
                </div>
            </div>
            <div style="display:none;" id="lostTime">
                <input style="width:150px;margin-right:20px;margin-top:16px;" type="text" name="losttime" placeholder="Lost Time (in Minute)">
                <input id="lostTimeBtn" type="button" class="btn btn-primary" value="Submit">
                <input id="RemoveLostTimeBtn" type="button" class="btn removeLostTime" style="margin-left: 10px" value="Cancel">
            </div>
            <div style="display:none;" id="addStoredTime">
                <div style="width:700px; margin-top:5px;">
                    <div style="float:left; width:210px;">
                        <label for="remainingStoredTimeInput">Remaining Stored Time (Minute): </label>
                    </div>
                    <div style="float:left; width:350px;">
                        <span style="vertical-align: top">Use </span><input type="text" id="remainingStoredTimeInput"  style="border:0; color:#f6931f; font-weight:bold; width:50px; padding-top:0px"> <span style="vertical-align: top">of </span> <span id="maxAddStoredTime" style="vertical-align: top">0</span><span style="vertical-align: top"> minutes</span>
                    </div>
                </div>
                <div style="clear:both"></div>
                <div>
                    <!--
                    <input style="width:150px;margin-right:20px;margin-top:16px;" type="text" name="add_stored_time" placeholder="Add Stored Time (in Minute)">
                    -->
                    <div>
                        <div style="float:left; width:380px; padding-top:13px">
                            <div id="remaining_stored_time-slider-range-max" style="width:380px"></div>
                        </div>
                        <div style="float:left; margin-left:20px">
                            <input id="addStoredTimeBtn" type="button" class="btn btn-primary" value="Submit">
                        </div>
                        <div style="float:left; margin-left:5px">
                            <input id="cancelAddStoredTimeBtn" type="button" class="btn cancelAddStoredTimeBtn" style="margin-left: 10px" value="Cancel">
                        </div>
                        <div style="float:left; width:50px;">
                            
                        </div>
                        <div style="float:left">
                            <input id="purchaseMoreTimeBtn" type="button" class="btn" style="margin-left: 10px" value="Buy Time">
                        </div>
                        <div id="refreshPurchasedTime" style="float:left; margin-left:5px; display:none">
                            <input id="refreshPurchasedTimeBtn" type="button" class="btn" value="Refresh Stored Time">
                        </div>     
                    </div>
                    <div style="clear:both"></div>
                </div>
            </div>
        </div>
    
        <!-- Chat Window -->
        <div id='chat_window' class='well'>
            <div id='messageContainer' class='interior'>
                <?
    
                    foreach($transcripts as $t)
                    {
                        if ($t['member_id'] == $reader_id) {
                            $t_memeber_type = 'reader';
                            $t_sender_name = $reader['username'];
                        } else {
                            $t_memeber_type = 'client';
                            $t_sender_name = $client['username'];
                        }
                        $class_name = "chat_" + $t_memeber_type;
                        
                        echo "<div class='{$class_name}'><span class='chat_username'>$t_sender_name:</span>&nbsp;&nbsp;{$t['message']}</div>";
                        //echo "<div class='{$className}'>{$t['message']}</div>";
                    }
                ?>
            </div>
        </div>
    
        <!-- Footer -->
        <div id='footer' class="navbar navbar-inverse">
            <form action='' method='POST' id='chatForm' class="navbar-inner">
                 <? 
                    if ($detect->isMobile() ) {
                        // mobile CSS
                   ?>
                   <div>
                        <div class='isTyping' id='whosTyping'></div>
                    </div>
                    <div>
                        <textarea name='input_message' id="input_message" placeholder="Enter text here and click Enter/Send" ></textarea>
                    </div>
                    <div>
                        <input id="chatForm_send" type='button' name='submit' value='Send' class='btn' style='margin:0;'>
                    </div>
                <? } else { ?>
                                    
                <table width='100%' cellPadding='0' cellSpacing='0'>
                    <tr>
                        <td><textarea name='input_message' id="input_message" placeholder="Enter text here and click Enter/Send"></textarea></td>
                        <td valign='top' align='right'><input id="chatForm_send" type='button' name='submit' value='Send' class='btn' style='margin:0;'></td>
                    </tr>
                </table>
                <div class='isTyping' id='whosTyping'></div>
                <? } ?>
            </form>
        </div>
        
        
        
    </div>
    
    <!-- start chat server -->
    <? if($this->session->userdata('member_logged')): ?>
        <script>
            if ($.browser.msie) {
                if (typeof console === "undefined" || typeof console.log === "undefined") {
                    console = {};
                    console.data = [];
                    console.log = function(enter) {
                        console.data.push(enter);
                    };
                }
            }
        </script>
        <!--
        <script src="/chat/app/chat_load_config.js"></script>
        <script data-main="/chat/app/start_chat_room.js" src="/chat/app/require-min.js"></script>
        -->
        <script src="/chat/app/chat_all_room.js?ts=<?=$ts ?>" ></script>
        <script>
            Chat.run.room();
        </script>
    <? endif; ?>
        
    

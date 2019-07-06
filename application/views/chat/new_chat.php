<? 
    if (!isset($ts)) {
        $ts = time();
    }
?>

<link rel=stylesheet type="text/css" href="/media/css/chat.css?ts=<?=$ts ?>">
<? 
    if ($detect->isMobile() ) {
        // mobile CSS
   ?>
    <link rel="stylesheet" media="all and (orientation:portrait)" href="/media/css/iphone-portrait.css">
    <link rel="stylesheet" media="all and (orientation:landscape)" href="/media/css/iphone-landscape.css">
   <?
}  else {
    ?>
    <link rel="stylesheet"  href="/media/css/web.css">
    <?
}
?>
<script src="<?=CHAT_URL ?>:<?=CHAT_PORT?>/auth.js"></script>
<script src="<?=CHAT_URL ?>:<?=CHAT_PORT?>/socket.io/socket.io.js"></script>
    <div id="new_chat">
        <script>
    
            $(function(){
                document.title = '<?=$title?>';
                $("#confirmationForm").submit(function(e){
    
                    e.preventDefault();
    
                    $.post($(this).attr('action'), $(this).serialize(), function(object){
    
                        if(object['error'] == '1'){
    
                            alert(object['message']);
    
                        }else{
    
                            window.location = object['redirect'];
    
                        }
    
                    }, 'json');
    
                });
    
            });
    
        </script>
    
        <!-- Navigation Bar -->
        <div id='navbar' class="navbar navbar-inverse">
            <div class="navbar-inner">
                <div class="container">
    
                    <a class="brand" href="#"><span class="chat_with_text">Chat With <?=strtoupper($username)?></span></a>
    
                    <ul class="nav pull-right">
                        <li><a href="Javascript:window.close();">Close Window</a></li>
                    </ul>
    
                </div>
            </div>
        </div>
    
        <!-- Content -->
        <div class='well'>
    
            <!-- <form id='confirmationForm' action='/chat/main/confirm/<?=$id?>' method='POST'> --></form>
    		<form id='confirmationForm' >
                <img src='<?=$profile?>' class='img-polaroid head-image-setting' >
    
                <legend class="image-legend">Please Confirm Your Chat Details</legend>
    
                <p class="prechat-message">You currently have enough funds in your account for a chat lasting <strong><?=$this->system_vars->time_generator($time_balance)?></strong>. To continue chatting with <?=$username?>, enter your topic below and click continue. </p>
    
                <hr />
    
                <div style='padding:0 0 10px 0;'><b>How many minutes do you want to use?</b></div>
    
                <div class="input-append chat-minute-select">
                    <input id='minutes' name='minutes' type='text' value="25" style="width:75px;" class="mobile-font"/>
                    <span class="add-on mobile-font">Minutes</span>
                </div>
    
                <div style='padding:0 0 10px 0;'><b>Your Chat Topic:</b></div>
                <div style='padding:0 0 10px 0;'><input id="topic" name='topic' type='text' class='chat-topic-input' placeholder="Enter your chat topic here..."></div>
                <div>
                	<input type='button' name='submit' value='Continue To Chat' class='btn btn-large btn-primary' onclick="Chat.controller.lobby.actions.chat_attempt('<?=$id ?>')" />
                	<input type='button' name='cancel' value='Cancel' class='btn ChatRedBackground' style="margin-left: 20px;" onclick="window.close();" />
                	
                </div>
    
            </form>
    
	        <!-- Other dialogs-->
	        <div id="dialog-wait" title="Waiting for response from " class="Hidden">
			  <p><span id="wait_seconds_left"><?php echo CHAT_MAX_WAIT  ?></span> seconds left for chat attempt.  </p>
			</div>
			<div id="dialog-reject" title="Reader has rejected the chat" class="Hidden">
			  <div class="dialogContent">
			  	<div>Please try the followings: </div>
			  	<div><a href="/my_account">Go to your account</a></div>
			  	<div><a href="/contact">Contact Admin</a></div>
			  	<div><a href="/psychics">Try another Reader</a></div>
			  </div>
			</div>
			<div id="dialog-abort-retry" title="No response from reader" class="Hidden">
			  <div class="dialogContent">
			  	<div>Please try the followings: </div>
			  	<div><a href="#" onclick="location.reload();">Try again with same Reader</a></div>
			  	<div><a href="/my_account">Go to your account</a></div>
			  	<div><a href="/contact">Contact Admin</a></div>
			  	<div><a href="/psychics">Try another Reader</a></div>
			  </div>
			</div>
			<div id="dialog-offline-retry" title="Reader is offline" class="Hidden">
			  <div class="dialogContent">
			  	<div>Please try the followings: </div>
			  	<div><a href="/my_account">Go to your account</a></div>
			  	<div><a href="/contact">Contact Admin</a></div>
			  	<div><a href="/psychics">Try another Reader</a></div>
			  </div>
			</div>
			<div id="dialog-max-manual-quit" title="Unable to request new chat" class="Hidden">
			  <div class="dialogContent">
			  	<div>You have exceed the limit of 3 chat attempts in 10 minutes.  Please try another reader or wait at least 10mins and try again.</div>
			  	<div><a href="/my_account">Go to your account</a></div>
			  	<div><a href="/contact">Contact Admin</a></div>
			  	<div><a href="/psychics">Try another Reader</a></div>
			  </div>
			</div>
			
			
        </div>
    </div>
    
<!-- lobby js -->

	<!-- Now, loading heavy JS scripts -->
    
    <? if($this->session->userdata('member_logged')): ?>
		<? $socket_url = CHAT_URL; ?>
        <? $socket_port = CHAT_PORT ?>
        <? $mem_id = $this->member->data['id']; ?>
        <? $member_username = $this->member->data['username']; ?>
        <? $member_id_hash = $this->member->data['member_id_hash']; ?>
        <? $member_type = (is_null($this->member->data['profile_id']))?'client':'reader'; ?>
       
        <script>
            var chat_init_data ={
                'member_type' : '<?=$member_type?>',
                'member_id' : '<?=$mem_id?>',
                'member_id_hash' : '<?=$member_id_hash?>',
                'member_username' : '<?=$member_username?>',
                '_disconnect_url' : '<?=$this->config->item('site_url') . "/main/disconnect_user/" . $mem_id?>',
                'socket_url'  : '<?=CHAT_URL ?>',
                'socket_port'  : '<?=CHAT_PORT ?>',
                'reader_id'	: <?=$id ?>
            };

        </script>
        <!--
        <script src="/chat/app/chat_load_config.js"></script>
        <script data-main="/chat/app/start_main_lobby.js" src="/chat/app/require-min.js"></script>
        -->
        <script src="/chat/app/chat_all_lobby.js?ts=<?=$ts ?>" ></script>
        
        <script src='/media/javascript/ion.sound.min.js'></script>
        <script>
        
            $(document).ready(function(){
                
                ion.sound({
                    sounds: [
                        {name: "door_bell"},
                        {name: "bell_ring"}
                    ],
                    path: "/media/sounds/",
                    preload: true,
                    volume: 1
                });
                
                Chat.run.lobby();

            });
            
        </script>
    <? endif; ?>

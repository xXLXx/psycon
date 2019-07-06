
<script>

    $(function()
    {

        $("#confirmationForm").submit(function(e)
        {

            var topic = $("input[name='topic']").val();

            if(!topic)
            {
                e.preventDefault();
                alert("Please enter a chat topic");
            }

        });

    });

</script>

<!-- Navigation Bar -->
<div id='navbar' class="navbar navbar-inverse">
    <div class="navbar-inner">
        <div class="container">

            <a class="brand" href="#">Chat With <?=strtoupper($username)?></a>

            <ul class="nav pull-right">
                <li><a href="Javascript:window.close();">Close Window</a></li>
            </ul>

        </div>
    </div>
</div>

<!-- Content -->
<div class='well'>

    <img src='<?=$profile?>' class='pull-right img-polaroid' style='width:150px;'>

    <legend>Would you like to continue?</legend>

    <p>You started a chat with <?=$username?> and currently have enough funds in your account for a chat lasting <strong><?=intval(date("i", $time_balance))?> minutes & <?=intval(date("s", $time_balance))?> seconds</strong>. To continue chatting, click Continue below. You can always purchase more time during the chat or by <a href='/chat/main/purchase_time/<?=$username?>'>clicking here</a>.</p>

    <hr />

    <table>
        <tr>
            <td style='padding-right:15px;'><a href='/chat/chatInterface/index/<?=$session_id?>/<?=$chat_session_id?>' class='btn btn-large btn-primary'>Continue Chatting With <?=$username?></a></td>
            <td><a href='/chat/chatInterface/resetChat/' onClick="Javascript:return confirm('Are you sure you want to end this chat?');" class='btn'>Start A New Chat</a></td>
        </tr>
    </table>

</div>
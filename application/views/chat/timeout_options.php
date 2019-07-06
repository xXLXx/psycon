
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

<!-- Content -->
<div class='well'>
    <h2 style="padding-bottom:20px;">Finished Chat Session #<?=$chat_id?> with <?=$reader_username?></h2>
    <p>
        Please click button below to resume chat.
    </p>
    <table>
        <tr>
            <td style='padding-right:15px;'><a href='#' class='btn btn-primary chatButton' data-username='<?=$reader_username?>'>Continue Chatting</a></td>
            <td style='padding-right:15px;'><a href='/psychics' class='btn '>Find another Psychic.</a></td>
            <td style='padding-right:15px;'><a href='/my_account/main/nrr/<?=$chat_id?>' class='btn btn-primary'>File Complaint</a></td>
        </tr>
    </table>

    <div style="padding:20px;">

    </div>

</div>
<script src='/chat/button.js'></script>
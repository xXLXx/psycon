
<h2>Your Purchase Was Successful!</h2>

<div class="well">
    <table cellPadding="2" cellSpacing="0">

        <tr>
            <td width="150"><b>Package:</b></td>
            <td><?=$package['title']?></td>
        </tr>

        <tr>
            <td width="150"><b>Transaction ID:</b></td>
            <td><?=$transaction['transaction_id']?></td>
        </tr>

        <tr>
            <td width="150"><b>Amount:</b></td>
            <td>$<?=number_format($transaction['amount'], 2)?></td>
        </tr>

    </table>
</div>

<div align="center">
    <a href="/chat/chatInterface/index/<?=$chatId?>/<?=$chat_session_id ?>" class="btn btn-warning btn-large">Continue With Your Chat</a>
</div>

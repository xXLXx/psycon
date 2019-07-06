
    <h2>Your Purchase Was Successful!</h2>

    <? if($package['type'] == 'reading'): ?>
        <div style='margin:5px 0 15px;'>Your purchase was a success! The chat time you purchased has been credited to your account and you can being using it immediately! <b>Thank you for using Psychic-Contact!</b></div>
    <? else: ?>
        <div style='margin:5px 0 15px;'>Your purchase was a success! Your email balance has been updated and you can begin your email reading immediately! <b>Thank you for using Psychic-Contact!</b></div>
    <? endif; ?>

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
        <a href="/psychics" class="btn btn-warning btn-large">Find A Reader Now!</a>
    </div>
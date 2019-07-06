
    <h2>Your billing profile has been added successfully!</h2>

    <div style='margin:5px 0 15px;'>Your billing profile has been added to your account. All information is encrypted and stored off-site for your protection. There will be a $1.00 authorization on your card that will be refunded within 2-7 business days. This authorization is simply to confirm an active payment account. Click the button below to return to the "Fund Your Account" section to add minutes and email funds to your account.</div>

    <div class="well">
        <table cellPadding="2" cellSpacing="0">
            <tr>
                <td width="150"><b>Name on card:</b></td>
                <td><?=$card_name?></td>
            </tr>
            <tr>
                <td><b>Card number:</b></td>
                <td>**** <?=$card_number?></td>
            </tr>
            <tr>
                <td><b>Billing address:</b></td>
                <td>
                    <?=$address?><br />
                    <?=$city?>, <?=$state?> <?=$zip?>
                </td>
            </tr>
        </table>
    </div>

    <div>
        <a href="/my_account/transactions/fund_your_account" class="btn btn-warning">Fund My Account</a>
    </div>



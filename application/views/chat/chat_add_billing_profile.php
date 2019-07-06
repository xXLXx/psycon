
<div style="height:460px;overflow:scroll;overflow-x:hidden;">
    <?
    if($this->session->flashdata('error'))
    {
        echo "<div class='alert alert-error page-notifs'><strong>There are errors:</strong><p>".$this->session->flashdata('error')."</p></div>";
    }
    ?>
<h2>Add A New Billing Profile</h2>
<div style='padding:5px 0 0;'>To add a credit card to your account, fill out the form below. You will be able to use this credit/debit card to fund your account. <b>Please Note:</b> To verify your card, we will authorize $1.00. This authorization will be refunded back to you within 7-10 business days.</div>

<hr />

<form action='/my_account/transactions/submit_billing_profile/<?=$merchant_type?>/<?=$this->uri->segment(5)?>/<?=$this->uri->segment(6)?>' method='POST'>

    <table width='100%' cellPadding='10'>

        <tr>
            <td width='150'><b>Credit Card Number:</b></td>
            <td><input type='text' name='cc_num' value='<?=set_value('cc_num')?>'></td>
        </tr>

        <tr>
            <td width='150'><b>Expiration Date:</b> EST</td>
            <td><?=$this->system_vars->exp_month("exp_month", set_value('exp_month'))?> / <?=$this->system_vars->exp_year("exp_year", set_value('exp_year'))?></td>
        </tr>

        <tr><td colSpan='2'><hr /></td></tr>

        <tr>
            <td width='150'><b>First Name:</b></td>
            <td><input type='text' name='first_name' value='<?=set_value('first_name')?>'></td>
        </tr>

        <tr>
            <td width='150'><b>Last Name:</b></td>
            <td><input type='text' name='last_name' value='<?=set_value('last_name')?>'></td>
        </tr>

        <tr>
            <td width='150'><b>Address:</b></td>
            <td><input type='text' name='address' value='<?=set_value('address')?>'></td>
        </tr>

        <tr>
            <td width='150'><b>City:</b></td>
            <td><input type='text' name='city' value='<?=set_value('city')?>'></td>
        </tr>

        <tr>
            <td width='150'><b>State / Province:</b></td>
            <td><input type='text' name='state' value='<?=set_value('state')?>'></td>
        </tr>

        <tr>
            <td width='150'><b>Zip / Postal Code:</b></td>
            <td><input type='text' name='zip' value='<?=set_value('zip')?>'></td>
        </tr>

        <tr>
            <td width='150'><b>Country:</b></td>
            <td><?=$this->system_vars->country_array_select_box('country', set_value('country'))?></td>
        </tr>

        <tr><td colSpan='2'><hr /></td></tr>

        <tr>
            <td colSpan='2'>
                <div class="well">
                    <table cellpadding="10">
                        <tr>
                            <td colspan="3">
                                <b>Package Information</b>
                            </td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>
                                Type
                            </td>
                            <td>
                                Title
                            </td>
                            <td>
                                Price
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?=$pinfo['type'] ?>
                            </td>
                            <td>
                                <?=$pinfo['title'] ?>
                            </td>
                            <td>
                                $<?=number_format($pinfo['price'],2)?>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>

        <tr>
            <td width='150'>&nbsp;</td>
            <td>

                <input type='submit' name='submit' value='Checkout' class='btn btn-large btn-primary'>
                <a href='/my_account/transactions/fund_your_account' class='btn btn-large btn-link'>Cancel</a>

            </td>
        </tr>

    </table>

</form>
</div>
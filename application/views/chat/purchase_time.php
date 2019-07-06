
    <style>

        .profile_selector{ width:300px; }
        .profile_selector a{}

    </style>

    <script>

        $(document).ready(function()
        {

            $(document).on('click', '.make-payment', function(e)
            {

                e.preventDefault();

                var paymentType = $(this).attr('data-payment-method');
                var packageSelected = $("input[name='package']:checked").val();

                if(paymentType == 'paypal')
                {

                    window.location = '/my_account/transactions/fund_with_paypal/' + packageSelected;

                }
                else
                {

                    if(confirm('By clicking OK below, you are allowing PsychicContact to charge your credit card. Do you want to continue?'))
                    {

                        var billing_profile_id = $(this).attr('data-billing-profile-id');

                        window.location = '/my_account/transactions/submit_deposit/'+billing_profile_id+'/' + packageSelected;

                    }

                }

            });

            $("input[name='package']").change(function()
            {

                var value = $(this).val();

                var promoFlag = $(this).attr("promo");



                if(value=='')
                {

                    $('#billing_profile_div').html("");

                }
                else
                {

                    $.get('/my_account/transactions/get_billing_profiles/' + value, function(object)
                    {

                        var selectHTML = "<div class='btn-group btn-group-vertical profile_selector' data-toggle='buttons-radio'>";

                        if(object.error == '0')
                        {

                            $(object.profiles).each(function(i)
                            {

                                selectHTML = selectHTML + "<a href='/' data-payment-method='merchant' data-billing-profile-id='"+object.profiles[i].id+"' class='btn btn-primary make-payment'>"+object.profiles[i].card_name+" (**** "+object.profiles[i].card_number+")</a>";

                            });

                        }
                        if(promoFlag != 1)
                        {
                            selectHTML = selectHTML + "<a href='/'  data-payment-method='paypal' class='btn btn-primary make-payment ppal'>Pay With PayPal</a>";
                        }
                        //changed for chat
                        selectHTML = selectHTML + "<a href='/chat/main/add_billing_profile/"+object.merchant+"/" + value + "/<?=$this->uri->segment(4)?>' class='btn'>Add A New Credit / Debit Card</a></div>";

                        $('#billing_profile_div').html(selectHTML);

                        if(object.error == '1')
                        {

                            // window.location = object.redirect;

                        }
                        else
                        {



                        }

                    }, 'json');

                }

            });

        });

    </script>
    <!-- Navigation Bar -->
    <div id='navbar' class="navbar navbar-inverse">
        <div class="navbar-inner">
            <div class="container">

                <a class="brand" href="#">Purchase More Time</a>

                <ul class="nav pull-right">
                    <!-- <li><a href="/chat/main/index/<?=$username?>">Cancel</a></li> -->
                    <li><a href="/my_account/account" >Cancel and back to account</a></li>
                </ul>

            </div>
        </div>
    </div>
    <div class='well' style='background:#FFF;margin-bottom:0;'>
        <?
        if($this->session->flashdata('error'))
        {
            echo "<div class='alert alert-error page-notifs'><strong>There are errors:</strong><p>".$this->session->flashdata('error')."</p></div>";
        }
        ?>
        <div id='chat_options'>
            <legend>Select A Package</legend>
            <div style='margin:15px 0 30px;'>

                <div style='margin:15px 0 30px;'>

                    <?

                    foreach($this->site->get_packages() as $r){
                        if(($r['promo'] == 1 && $this->member->data['received_promo'] != 1) || $r['promo'] == 0)
                            echo "<div style='padding:5px 0 0;" . ($r['promo'] == 1 ? 'font-weight:bold; color:red;' : '') . "'><input type='radio' promo='{$r['promo']}' name='package' value='{$r['id']}' style='margin:0 5px 0 0;'> {$r['title']}</div>";
                    }

                    ?>

                </div>

            </div>
        </div>

        <legend>Choose A Payment Method</legend>
        <div id='billing_profile_div' style='margin:15px 0;'><span style='color:#999;'>Select A Package Above</span></div>

    </div>
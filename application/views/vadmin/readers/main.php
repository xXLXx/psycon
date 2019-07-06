
    <script>

        var ustotal = 0;
        var catotal = 0;

        var pp_email = "";

        $(function(){

            //--- If region updated
            //--- Then change the default value in amount field
            $("select[name='region']").on('change', function(){

                var value = $(this).val();

                if(value == 'us'){
                    $("input[name='amount']").val(ustotal);
                }else{
                    $("input[name='amount']").val(catotal);
                }

            });

            //--- If user clicks a new payment
            //--- Pre-fill with data
            $('.newPaymentAnchor').on('click', function(){

                var readerid = $(this).attr('data-readerid');
                var us_amount = $(this).attr('data-ustotal');
                var ca_amount = $(this).attr('data-catotal');
                pp_email = $(this).attr('data-readerpaypal');
                ustotal = us_amount;
                catotal = ca_amount;

                $("input[name='readerid']").val(readerid);
                $("input[name='amount']").val(ustotal);

            });

            $('.downloadPaymentsAnchor').on('click', function(){
                var readerid = $(this).attr('data-readerid');
                $("input[name='readerid']").val(readerid);
            });

            $('.downloadAllTransactions').on('click', function(){
                $("input[name='readerid']").val("");
            });



            //--- If user clicks a new payment
            //--- Pre-fill with data
            $('.paypalAnchor').on('click', function(){


                if(!pp_email)
                {

                    alert("Reader does not have a Paypal Email Address");
                    return false;
                }
            });

            $('.downloadPaymentsAnchor').on('click', function(){
                var readerid = $(this).attr('data-readerid');
                $("input[name='readerid']").val(readerid);
            });

            $('.downloadAllTransactions').on('click', function(){
                $("input[name='readerid']").val("");
            });

        });

    </script>

    <div style="padding:20px;background:#FFF;margin:20px;">

        <div class="btn-group pull-right">
            <a href="#newProfileModal" class="btn btn-primary" data-toggle="modal">New Reader</a>
            <a href="#downloadTransactionsModal" class="btn btn-primary downloadAllTransactions" data-toggle="modal">Download All Transactions</a>
        </div>

        <legend>Reader Management</legend>

        <div class="clearfix" style="margin:15px 0;"></div>

        <table class="table table-bordered table-striped table-hover">

            <thead>

                <tr>
                    <th>Username:</th>
                    <th style="text-align: center;">US Balance:</th>
                    <th style="text-align: center;">CAN Balance:</th>
                    <th style="text-align:center;">Featured:</th>
                    <th colSpan="2">&nbsp;</th>
                </tr>

            </thead>

            <tbody>

                <? foreach($readers as $reader): ?>

                    <? $this->reader->init($reader['id']); ?>

                    <!-- US Balance -->
                    <? $us_balance = $this->reader->get_balance('us');?>
                    <? $USFormattedBalance = number_format($us_balance, 2); ?>

                    <!-- CAN Balance -->
                    <? $ca_balance = $this->reader->get_balance('ca');?>
                    <? $CAFormattedBalance = number_format($ca_balance, 2); ?>

                    <!-- Is Featured -->
                    <? $featuredClassImage = ($reader['featured'] ? "icon-star icon-white" : "icon-star-empty"); ?>
                    <? $featuredClassBTN = ($reader['featured'] ? "btn btn-primary" : "btn"); ?>

                    <tr>
                        <td><a href="/vadmin/main/edit_record/17/0/<?=$reader['id']?>"><?=$reader['username']?></a></td>
                        <td style="text-align: center;">$ <?=$USFormattedBalance?></td>
                        <td style="text-align: center;">$ <?=$CAFormattedBalance?></td>
                        <td style="text-align:center;"><a href="/vadmin/reader_management/toggleFeatured/<?=$reader['id']?>" class="<?=$featuredClassBTN?>"><span class="<?=$featuredClassImage?>"></span></a></td>
                        <td style="width:185px; text-align: center;"><a href='#downloadTransactionsModal' class="downloadPaymentsAnchor btn"  data-readerid="<?=$reader['id']?>" data-toggle="modal">Download Transactions</a></td>
                        <td style="width:175px; text-align: center;"><a href='#enterPaymentModal' class="newPaymentAnchor btn" data-toggle="modal" data-readerpaypal="<?=($reader['paypal_email'] ? $reader['paypal_email'] : "")?>" data-ustotal="<?=$USFormattedBalance?>" data-catotal="<?=$CAFormattedBalance?>" data-readerid="<?=$reader['id']?>">Enter New Payment</a></td>
                    </tr>

                <? endforeach; ?>

            </tbody>

        </table>

    </div>

    <!-- Add New Payment -->
    <form id="enterPaymentModal" action="/vadmin/reader_management/new_payment" method="post" class="modal hide fade">
        <input type="hidden" name="readerid" value="" />
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Enter New Payment</h3>
        </div>
        <div class="modal-body">

            <table cellPadding="5">

                <tr>
                    <td><b>Region:</b></td>
                    <td>
                        <select name="region">
                            <option value="us">US</option>
                            <option value="ca">CA</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><b>Amount:</b></td>
                    <td>
                        <div class="input-prepend">
                            <span class="add-on">$</span>
                            <input name="amount" class="span2" id="prependedInput" type="text" />
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><b>Notes:</b></td>
                    <td><textarea style="width:445px;height:150px;" name="notes"></textarea></td>
                </tr>

            </table>

        </div>
        <div class="modal-footer">
            <input type="submit" name="paypal" value="Pay with paypal" class="btn btn-success btn-large paypalAnchor" />
            <input type="submit" name="submit" value="Save Payment" class="btn btn-primary btn-large" />
        </div>
    </form>

    <!-- Download Transactions -->
    <form id="downloadTransactionsModal" action="/vadmin/reader_management/download_transactions" method="post" class="modal hide fade">
        <input type="hidden" name="readerid" value="" />
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Download Transactions</h3>
        </div>
        <div class="modal-body">

            <table cellPadding="5">

                <tr>
                    <td width="150"><b>Reader ID:</b></td>
                    <td><input type="text" name="readerid" value="" /></td>
                </tr>

                <tr>
                    <td><b>From:</b></td>
                    <td><input type="text" name="from" value="<?=date("m/d/Y h:i A", strtotime("-7 days"))?>" /></td>
                </tr>

                <tr>
                    <td><b>To:</b></td>
                    <td><input type="text" name="to" value="<?=date("m/d/Y h:i A")?>" /></td>
                </tr>

                <tr>
                    <td><b>Region:</b></td>
                    <td>
                        <select name="region">
                            <option value="">All</option>
                            <option value="us">US</option>
                            <option value="ca">CA</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><b>Service:</b></td>
                    <td>
                        <select name="service">
                            <option value="">All</option>
                            <option value="chat">Chat</option>
                            <option value="email">Email</option>
                        </select>
                    </td>
                </tr>

            </table>

        </div>
        <div class="modal-footer">
            <input type="submit" name="submit" value="Download Transactions" class="btn btn-primary btn-large" />
        </div>
    </form>

    <!-- New Profile -->
    <form id="newProfileModal" action="/vadmin/reader_management/new_reader" method="post" class="modal hide fade">
        <input type="hidden" name="readerid" value="" />
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Create New Reader</h3>
        </div>
        <div class="modal-body">

            <p>To create a new reader, the reader must have a valid member account (username & password). Enter the member id of the user and click save. The user will now be able to login as a reader and update their profile accordingly.</p>

            <table cellPadding="5">

                <tr>
                    <td width="150"><b>Member ID:</b></td>
                    <td><input type="text" name="memberid" value="" /></td>
                </tr>

                <tr>
                    <td width="150">&nbsp;</td>
                    <td><input type="checkbox" name="legacy" value="1" /> Set as legacy member</td>
                </tr>

            </table>

        </div>
        <div class="modal-footer">
            <input type="submit" name="submit" value="Save New Reader" class="btn btn-primary btn-large" />
        </div>
    </form>
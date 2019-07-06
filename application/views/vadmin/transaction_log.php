

    <?

        $this->member->set_member_id($this->uri->segment('4'));
        $minute_balance = $this->member_funds->minute_balance();
        $email_balance = $this->member_funds->email_balance();

    ?>

    <div class='round-top blue_box_nr' style='margin:10px 0 0;border-bottom:none;'>

        <table width='100%'>

            <tr>
                <td><h1>Transaction Log</h1></td>
                <td align='right'>

                </td>
            </tr>

        </table>

    </div>

    <div class='round-bottom white_box_nr' style='padding:10px;'>

        <?

            echo "
            <div align=\"left\">

                <form class=\"well\" action=\"/vadmin/billing/fund_account/{$this->uri->segment('4')}\" method=\"POST\">

                    <legend>Fund Account</legend>

                    <div><b>Minute Balance:</b> {$minute_balance}</div>
                    <div><b>Email Balance:</b> {$email_balance}</div>

                    <hr />

                    <div align=\"left\">
                    <div class=\"input-prepend input-append\">
                    <span class=\"add-on\">Type:</span>
                    <select name='type' class='span2'>
                        <option value='reading'>Reading / Chat</option>
                        <option value='email'>Email</option>
                    </select>

                    <span class=\"add-on\">Tier:</span>
                    <select name='tier' class='span2'>
                        <option value='regular'>Regular</option>
                        <option value='half'>Half</option>
                    </select>
                    <span class=\"add-on\">Region:</span>
                    <select name='region' class='span2'>
                        <option value='US'>US</option>
                        <option value='CA'>CA</option>
                    </select>
                    <span class=\"add-on\">Mins/Credits:</span>
                    <input name='amount' class=\"span2\" id=\"appendedPrependedInput\" type=\"text\" value='0' />
                    <input type='submit'  value='Fund Account' class='btn' style='height:30px;' />
                    </div>

                    </div>

                </form>

            </div>";

        //--- Member Balance Records
        echo "<legend>Balance Records</legend>";
        echo "<p>A list of ALL minute balance records and email credits. Information changed here WILL affect a members balance.</p>";

        if($balance){

            echo "<table class=\"table table-striped table-hover table-bordered\">
            
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Tier</th>
                    <th>Region</th>
                    <th>Total</th>
                    <th>Used</th>
                    <th>Balance</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>";

            foreach($balance as $b){

                echo "
                <tr>
                    <td style='width:150px;'>".date("m/d/Y @ h:i A", strtotime($b->datetime))."</td>
                    <td style='width:100px;'>$b->type</td>
                    <td>$b->tier</td>
                    <td>$b->region</td>
                    <td>$b->total</td>
                    <td>$b->used</td>
                    <td>$b->balance</td>
                    <td style='width:75px;text-align:center;'><a href='/vadmin/main/delete_balance_record/{$b->id}' onClick=\"Javascript:return confirm('Are you sure you want to delete this balance record? It WILL change the balance for this member.');\">Delete</a></td>
                </tr>
                ";

            }

            echo "</table>";

        }






        //--- Chat History
        echo "<legend>Chat History</legend>

            <p>ANY row highlighted in YELLOW is under 4 minutes in length and should be reviewed.</p>";

        if($transcripts){

            echo "<table class=\"table table-striped table-hover table-bordered\">

            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reader</th>
                    <th>Topic</th>
                    <th>Chat Length</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>";

            foreach($transcripts as $t){

                $className = "";
                if($t->length < 240){
                    $className = " class='warning'";
                }

                $hasNRR = $this->nrr_model->check_nrr_for_chat($t->id);

                echo "
                <tr $className>
                    <td style='width:150px;'>".date("m/d/Y @ h:i A", strtotime($t->start_datetime))."</td>
                    <td style='width:100px;'>$t->reader_username</td>
                    <td>$t->topic</td>
                    <td style='width:200px;'>".$this->system_vars->time_generator($t->length)."</td>
                    <td style='width:75px;text-align:center;'><a href='/vadmin/main/transcripts/{$t->id}'>Transcripts</a></td>
                    <td style='width:135px;text-align:center;'>";

                        if(!$hasNRR){
                            echo "<a class=\"btn\" href='/vadmin/main/process_nrr/{$t->id}' onClick=\"Javascript:return confirm('This action will refund the client and remove paid funds from reader. Are you sure you want to continue?');\">Process NRR</a>";
                        }else{
                            echo "<span class=\"label\">NRR Processed</span>";
                        }

                    echo "
                    </td>
                </tr>
                ";

            }

            echo "</table>";

        }

            //--- Transactions History
            echo "<legend>Full Transaction Log</legend>";

            echo "<p>Shows all transaction records: purchases, consumables (used time or email), refunds, etc. The information below is purely used for information purposes and if changed or deleted, does NOT affect member balance in ANY way. This section is only a transaction log.</p>";

            if($transactions){

                echo "<table class=\"table table-striped table-hover table-bordered\">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Summary</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>";

                foreach($transactions as $t){

                    echo "
                        <tr>
                            <td>".date("m/d/Y @ h:i A", strtotime($t->datetime))."</td>
                            <td>$t->type</td>
                            <td>$t->amount</td>
                            <td>$t->summary</td>
                            <td style='width:75px;text-align:center;'><a href='/vadmin/main/edit_record/13/23/{$t->id}'>Details</a></td>
                            <td style='width:75px;text-align:center;'><a href='/vadmin/main/delete_transaction/{$t->id}' onClick=\"Javascript:return confirm('Are you sure you want to delete this transaction?');\">Delete</a></td>
                        </tr>
                        ";

                }

                echo "</table>";

            }

        ?>

    </div>
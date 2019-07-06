
<div class='round-top blue_box_nr' style='margin:10px 0 0;border-bottom:none;'>

    <table width='100%'>

        <tr>
            <td><h1>Chat Transcripts</h1></td>
            <td align='right'>

            </td>
        </tr>

    </table>

</div>

<div class='round-bottom white_box_nr' style='padding:10px;'>

    <a href='Javascript:history.back(1);'><< Back</a>

    <div class='well' style="margin:15px 0 35px;">

        <table width='100%' cellPadding='10'>

            <tr>
                <td><b>Chat ID:</b></td>
                <td>#<?=$id?></td>
            </tr>

            <tr>
                <td><b>Date:</b></td>
                <td><?=date("m/d/Y h:i A", strtotime($start_datetime))?></td>
            </tr>

            <tr>
                <td><b>Chat Length:</b></td>
                <td><?=$this->system_vars->time_generator($length); ?></td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td>

                    <?

                        if(!$hasNRR){
                            echo "<a class=\"btn\" href='/vadmin/main/process_nrr/{$id}' onClick=\"Javascript:return confirm('This action will refund the client and remove paid funds from reader. Are you sure you want to continue?');\">Process NRR</a>";
                        }else{
                            echo "<span class=\"label\">NRR Processed</span>";
                        }

                    ?>

                </td>
            </tr>

        </table>

    </div>

    <table class="table table-striped table-bordered" style="margin:15px 0 35px;">

        <? foreach($transcripts as $t): ?>

            <tr>
                <td><?=$t->username?></td>
                <td><?=$t->message?></td>
            </tr>

        <? endforeach; ?>

    </table>

</div>
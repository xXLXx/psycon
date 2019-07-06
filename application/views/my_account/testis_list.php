<h2>Testimonials</h2>
<div style="padding:20px 0 0 20px;" >
        <? if(count($testis) > 0): ?>

            <table class="table table-striped table-bordered" style="margin:15px 0 35px;">
                <tr>
                    <th>Chat</th>
                    <th>Date</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>&nbsp;</th>
                </tr>
                <? foreach($testis as $t): ?>
                    <tr>
                        <td style="vertical-align:top;width:60px"><a href="/my_account/chats/transcript/<?=$t['chat_id']?>">Chat #<?=$t['chat_id']?></a></td>
                        <td style="vertical-align:top;width:175px;"><?=date('m/d/Y h:i:s a',strtotime($t['datetime']))?></td>
                        <td style="vertical-align:top;width:25px"><?=$t['rating']?></td>
                        <td style="vertical-align:top;"><?=nl2br($t['review'])?></td>

                        <td style="vertical-align:top;width:60px;"><a  href="/my_account/main/testimonial_toggle/<?=$t['id']?>"
                                                                        <?=($t['reader_approved'] != 1 ? "class='btn'>Approve" : "class='btn btn-danger'>Deny")?>

                                                                   </a></td>
                    </tr>

                <? endforeach; ?>

            </table>

        <? else: ?>

            <p>You have no testimonials.</p>

        <? endif; ?>


</div>

    <div style="padding:20px;background:#FFF;margin:20px;">

        <table class="table table-striped table-hover table-bordered">
            
            <thead>

                <tr>
                    <th>Date / Time</th>
                    <th>Reader</th>
                    <th>Client</th>
                    <th>Topic</th>
                    <th>Length</th>
                    <th>&nbsp;</th>
                </tr>

            </thead>

            <? foreach($chats as $chat): ?>

                <tr>
                    <td><?=date("m/d/Y @ h:i A", strtotime($chat->start_datetime))?></td>
                    <td><?=$chat->reader_username?></td>
                    <td><?=$chat->client_username?></td>
                    <td><?=$chat->topic?></td>
                    <td><?=$this->system_vars->time_generator($chat->length)?></td>
                    <td style="text-align:center;width:75px;"><a href="/vadmin/main/transcripts/<?=$chat->id?>">Details</a></td>
                </tr>

            <? endforeach; ?>

        </table>

    </div>
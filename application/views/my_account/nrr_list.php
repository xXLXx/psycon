    <h2>NRRs</h2>
    <div style="padding:20px 0 0 20px;">
        <form class="form-inline" id="timeback_form" action="/my_account/chats/give_timeback/<?=$this->chatmodel->object['id']?>" method="POST">
            <? $nr = $this->nrr_model->get_nrr(null,null,$this->member->data['id']); ?>
            <? if(count($nr) > 0): ?>

                <table class="table table-striped table-bordered" style="margin:15px 0 35px;">
                    <tr>
                        <th>Type</th>
                        <th>Time Requested</th>
                        <th>Date</th>
                        <th>&nbsp;</th>
                    </tr>
                    <? foreach($nr as $n):
                        $amount = 0;
                        switch($n['type'])
                        {
                            case "unhappy_reading":
                                $amount = $n['unhappy_timeback'];
                                break;

                            case "discount":
                                $amount = $n['discount_timeback'];
                                break;

                            case "slow":
                                $amount = $n['slow_timeback'];
                                break;


                        }
                        ?>
                        <tr>
                            <td><?=ucwords(str_replace('_', ' ',$n['type']))?></td>
                            <td><?=$amount?> </td>
                            <td><?=$n['date']?></td>
                            <td><a href="/my_account/nrr/details/<?=$n['id']?>" class="btn">Details</a></td>
                        </tr>

                    <? endforeach; ?>

                </table>

            <? else: ?>

                <p>You have no NRRs.</p>

            <? endif; ?>

        </form>
    </div>
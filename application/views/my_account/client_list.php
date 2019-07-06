<h2>Client List</h2>
<div style="padding:20px 0 0 20px;">

        <? if(count($clients) > 0): ?>

            <table class="table table-striped table-bordered" style="margin:15px 0 35px;">
                <tr>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>&nbsp;</th>
                </tr>
                <? foreach($clients as $c): ?>
                    <tr>
                        <td><?=$c['username']?></td>
                        <td><?=$c['first_name']?></td>
                        <td><?=$c['last_name']?></td>
                        <td style="text-align:center;"><a class="btn btn-primary" href="/my_account/messages/compose/<?=$c['mid']?>/send">Send Message</a></td>
                    </tr>

                <? endforeach; ?>

            </table>

        <? else: ?>

            <p>You have no clients.</p>

        <? endif; ?>
</div>
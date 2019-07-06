<style>
    h1, h2 {
        color: #126595;
        font-family: Verdana,Geneva,sans-serif;
        font-size: 18px;
        font-weight: normal;
        padding: 0 0 8px;
    }
    h3{font-size:16px;color:#126595;font-weight:normal;}
    .clear{clear:both;}

    #slow{display:none;}
    #disconnect{display:none;}
    #unhappy_reading{display:none;}

    .readerName{font-weight:bold;font-style:italic;}

</style>
<?
$description = "";
$timeback = 0;
$dc_desc = "";
switch($type)
{
    case "slow":
            $description = "The chat was running slow.";
            $timeback = $slow_timeback;
        break;

    case "disconnect":
            $description = "The chat ended suddenly and I was booted out.";
            switch($disconnect)
            {
                case 1:
                    $dc_desc = "I have checked my account & time was restored: I will try to re-enter chat asap.";
                break;

                case 2:
                    $dc_desc = "I have checked my account & time was restored: I will save my time for another session and/or reader";
                break;

                case 3:
                    $dc_desc = " I have checked my account no minutes were returned.";
                    $timeback = $disconnect_timeback;
                break;
            }
        break;

    case "unhappy_reading":
            $description = "I was unhappy with my reading from my reader.";
            $timeback = $unhappy_timeback;
        break;
}
?>

   <div class="pull-left"><h2>Client: <?=$first_name . " " . $last_name ?></h2></div>
   <div class="pull-right"><a class="btn btn-inverse" href="/my_account/chats/transcript/<?=$chat_id?>">View Chat</a></div>
    <div class="clear"></div>
<div style="margin-top:10px;"><h2>Date: <?=date('m/d/Y @ h:i:s a',strtotime($date))?></h2></div>
    <div style="margin-top:20px;" class="well well-small">
        <b>Complaint Type:</b> <?=$description?>
    </div>
   <? if($type == 'disconnect'): ?>
    <div style="margin-top:20px;" class="well well-small">
        <b>Disconnect Selection:</b> <?=$dc_desc?>
    </div>
        <? if($disconnect == 3): ?>
        <div style="margin-top:20px;" class="well well-small">
               <b>Requested Time back:</b> <?=$timeback?>
        </div>
    <? endif; ?>
    <? else: ?>
       <div style="margin-top:20px;" class="well well-small">
           <b>Requested Time back:</b> <?=$timeback?>
       </div>
    <? endif; ?>

    <? if($type == 'unhappy_reading'): ?>
        <h2>Reason:</h2>
        <div style="margin-top:20px;" class="well large-well">
            <p>
                <?=$unhappy?>
            </p>
        </div>
    <? endif; ?>
    <h2>Suggestions:</h2>
    <div style="margin-top:20px;" class="well large-well">
        <p>
            <?=$suggest?>
        </p>
    </div>
    <script>
        $(document).ready(function()
        {
            var timeback = <?=$timeback?>;

            $("#timeback_form").submit(function()
            {
                var tb = $("input[name=timeback]").val();
                if($("select[name=type]").val() == 'paid' && timeback < tb)
                {
                   $("#universal-form-error").html("<div class='alert alert-error page-notifs'><strong>There was an error:</strong><p>Incorrect Refund Amount.</p></div>");
                    return false;
                }
            });


        });
    </script>
    <? if($refunded != 1): ?>
    <h2>Give Time Back</h2>
    <div style="padding:20px 0 0 20px;">
        <form class="form-inline" id="timeback_form" action="/my_account/nrr/give_timeback/<?=$id?>" method="POST">

            <label for="timeback">Type</label>

            <select style="width:150px;margin-right:20px;" name="type">
                <option value="paid">paid</option>
                <option value="free">free</option>
            </select>

            <label for="timeback">Time Back</label>

            <input style="width:150px;margin-right:20px;" type="text" name="timeback" placeholder="Time Back">

            <input type="submit" class="btn btn-primary" value="Submit">

        </form>
    </div>
    <? else : ?>
        <h2>Refunded</h2>
        <div style="padding:20px 0 0 20px;">
            <div><b><?=$amount?></b></div>
        </div>
    <? endif; ?>
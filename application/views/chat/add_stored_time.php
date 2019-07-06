
    <script>

        $(function(){

            $("#ast_form").submit(function(e){

                e.preventDefault();

                $.post($(this).attr('action'), $(this).serialize(), function(object){

                    if(object['error'] == '1'){

                        alert(object['message']);

                    }else{

                        window.location = object['redirect'];

                    }

                }, 'json');

            });

        });

    </script>

    <!-- Navigation Bar -->
    <div id='navbar' class="navbar navbar-inverse">
        <div class="navbar-inner">
            <div class="container">

                <a class="brand" href="#">Add Stored Time</a>

                <ul class="nav pull-right">
                    <li><a href="/chat/main/index/<?=$username?>">Cancel</a></li>
                </ul>

            </div>
        </div>
    </div>

    <form id="ast_form" action="/chat/main/add_stored_time_submit/<?=$username?>" class='well' style='background:#FFF;margin-bottom:0;'>

        <legend>How many minutes would you like to add to your chat?</legend>

        <p>You currently have enough funds in your account for a chat lasting <strong><?=$this->system_vars->time_generator($time_balance)?></strong>. To continue chatting with <?=$username?>, enter the minutes you wish to add to the chat below and click continue. </p>

        <div class="input-append" style="margin-bottom:25px;">
            <input name='minutes' type='text' value="25" style="width:75px;" />
            <span class="add-on">Minutes</span>
        </div>

        <div><input type='submit' name='submit' value='Continue To Chat' class='btn btn-large btn-primary' /></div>

    </form>
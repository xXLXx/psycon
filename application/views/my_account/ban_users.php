
<style>

    .expert_name
    {
        color: #2850A8;
        display: block;
        font-size: 14px;
        font-weight: bold;
        padding: 10px 0 3px;
        text-decoration: underline !important;
    }

</style>
<script>
   $(document).ready(function(){
       $("a.disabled").click(function(e){e.preventDefault();});

       $(".search_form_ban").submit(function()
       {
          var query = $("input[name=query]").val();
          if(!query)
          {
              $("#universal-form-error").html("<div class='alert alert-error page-notifs'><strong>There are errors:</strong><p>Search box cannot be blank.</p></div>");

          }


       });

   });
</script>

<div class='content_area'>

    <h2>Ban Users</h2>

    <hr />

    <div align='center' style='margin:0 0 45px 0;'>

    <form class="search_form_ban"action='/my_account/ban_users/search/' method='POST'>

        <table cellPadding='5'>
            <tr>
                <td>
                    <select name='search_type' style='width:auto;margin:0;'>
                        <option value='username' <?=($this->input->post("search_type") == 'username' ? "selected" : "") ?> >Username</option>
                        <option value='session_date' <?=($this->input->post("search_type") == 'session_date' ? "selected" : "") ?> >Session Date</option>
                        <option value='client_first_name' <?=($this->input->post("search_type") == 'client_first_name' ? "selected" : "") ?> >Clients First Name</option>
                     </select>
                </td>
                <td><input type='text' name='query' value='<?=$this->input->post('query')?>' style='margin:0;' placeholder='Search for clients' class='input-xlarge'></td>
                <td><input type='submit' value='Search' class='btn btn-primary' style='margin:0;'></td>
            </tr>
        </table>

    </form>

</div>


    <?

    if($clients)
    {

        echo "<table class='table table-striped table-hover'>";

        foreach($clients as $c)
        {



            echo "
                    <tr>

                        <td width='175' style='vertical-align:middle;'>{$c['username']}</td>
                        <td style='vertical-align:middle;'>". ($c['date'] ? "Ban Date: " . date("m/d/Y @ h:i A", strtotime($c['date'])) : "") . "</td>
                        <td style='vertical-align:middle;'>
                        </td>
                        <td style='width:150px; vertical-align:middle; text-align:right'>";
          switch($c['type'])
          {
              case "full":
                  echo "<a style='width:75px;' href='#' class='btn btn-danger disabled'>Full Banned</a>";
              break;

              case "personal":
                  echo "<a style='width:75px;' href='/my_account/ban_users/unban/{$c['id']}' class='btn'>Unban User</a>";
              break;

              default:
                   echo "<a style='width:75px;' href='/my_account/ban_users/ban/{$c['mid']}' class='btn btn-danger'>Ban User</a>";
              break;
          }

           echo    "</td>
                    </tr>
                    ";

        }

        echo "</table>";

    }
    else
    {

        echo  ($this->input->post("query") ? "No results for \"" . $this->input->post("query") ."\""   : "<div>You have not banned any users.</div>");

    }
    ?>

</div>
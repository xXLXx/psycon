<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=$title?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="/media/boilerplate/css/normalize.css">
    <link rel="stylesheet" href="/media/boilerplate/css/main.css">
    <script src="/media/boilerplate/js/vendor/modernizr-2.6.2.min.js"></script>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.1.min.js"><\/script>')</script>
    <script src="/media/boilerplate/js/plugins.js"></script>

    <!-- Bootstrap -->
    <script type="text/javascript" src="/media/bootstrap/js/bootstrap.min.js"></script>
    <link rel=stylesheet type="text/css" href="/media/bootstrap/css/bootstrap.css">

    <script type="text/javascript" language="JavaScript">

     $(document).ready(function()
     {

         $("form[name=nrr_form]").submit(function()
         {
             var sel_reader = $("select[name=reader]");

             $("option:selected",sel_reader).text());
             /*
             if(form.readerID.tagName == 'SELECT') {
                 if ( confirm('Are you absolutely sure '+form.readerID.options[form.readerID.selectedIndex].text+' is the one you wish to receive this NRR form from you?') ) form.submit();
             }
             else {
                 form.submit();
             }
            */

         });

         $("#slow").show();

         $("input[name=nrr]").change(function()
         {
             var type = $(this).val();
             switch(type)
             {
                 case "slow":
                     $("#disconnect").hide();
                     $("#unhappy_reading").hide();
                     $("#slow").show();
                 break;

                 case "disconnect":
                     $("#disconnect").show();
                     $("#unhappy_reading").hide();
                     $("#slow").hide();
                     $("#slow2").hide();
                 break;

                 case "unhappy_reading":
                     $("#unhappy_reading").show();
                     $("#disconnect").hide();
                     $("#slow").hide();
                 break;
             }

         });

         $("select[name=reader]").change(function()
         {
             var cid = $(this).val();
             $(".readerName").html($("option:selected",this).text());
             $.ajax({
                 dataType: "json",
                 url: '/my_account/main/nrr_chat_session/' + cid,
                 success: function(data)
                 {

                    var sel = $("select[name=chat]");
                    sel.html("");
                    var title = "";

                    for (r in data)
                    {

                        title = truncateString(data[r].topic,17);
                        sel.append("<option value='" + data[r].id + "'>" + "#" + data[r].id + " " + data[r].chat_date + " - " + title  + "</option>");
                    }

                 }
             });
         });

     });

     function truncateString(str, length) {
         return str.length > length ? str.substring(0, length - 3) + '...' : str
     }


    </script>

    <style>
        body{ padding:0 20px 0 20px; background:none; overflow: hidden; }

        html{background:url("/media/images/bg.jpg") repeat-x scroll center top #126393;}
        .logo
        {
            background: url("/media/images/logo.jpg") no-repeat scroll left top transparent;
            display: block;
            height: 95px;
            margin: 0 auto;
            text-indent: -999px;
            width: 430px;
        }
        #contentDivContainer{ background:#FFF; margin-top:15px; }
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

</head>
<body>


<? if(!$hideLogo): ?>
    <div><a href='#' class='logo'>&nbsp;</a></div>
<? endif; ?>

<div id='contentDivContainer' class='well' align='left'>
    <form class="form-horizontal" action="" name="nrr_form" method="post">

       <div style="text-align:center;"> <h2>NRR Request Form</h2></div>
        <div style="width:700px; margin:0px auto;" >
            <div class="pull-left">
                <h3>Client: <?=$this->member->data['first_name']?> <?=$this->member->data['last_name']?></h3>
            </div>
            <div style="padding:16px 0 0 20px;" class="pull-left">
                <select name="reader">
                    <option>Select your reader</option>
                    <? foreach($readers as $r) : ?>
                        <option value="<?=$r['id']?>"><?=$r['username']?></option>
                    <? endforeach; ?>
                </select>
            </div>

            <div class="pull-left" style="padding:16px 0 0 20px;">
                <select name="chat">
                    <option>Reader chat sessions</option>
                </select>
            </div>
            <div class="clear"></div>
        </div>
        <div>
            <p>
                Please use this form to let us know if you are requesting time back from the reader for the chat session that just ended, or any other session you recently had.
                IF MORE THAN ONE OCCURRENCE TOOK PLACE IN WHICH YOU ARE REQUESTING TIME BACK- PLEASE USE A SEPARATE NRR FORM FOR EACH DESCRIPTION/REQUEST.
            </p>

            <ol style="margin:0px auto;width:350px;padding: 20px;">
                <li><input type="radio" checked="checked"  value="slow" name="nrr">&nbsp;&nbsp;The chat was running slow.</li>
                <li><input type="radio"  value="disconnect" name="nrr">&nbsp;&nbsp;The chat ended suddenly and I was booted out.</li>
                <li><input type="radio"  value="unhappy_reading" name="nrr">&nbsp;&nbsp;I was unhappy with my reading from my reader.</li>
            </ol>
        </div>
        <div class="well">
            <div id="slow">
            <p>
            We're sorry you feel the chat was running slower than usual. Usually its Internet connectivity problems that cause slow chatting....
            In most cases the reader will compensate you during the chat and/or ask you to leave the chatroom, re-boot
            and then return to continue your session.
            If you did not bring it to the attention of your Reader during the chat or no compensation was
            given please select the amount of time you are requesting back and submit this form
            </p>
            <div class="control-group">
                <label class="control-label" for="slow_timeback">Time Back</label>
                <div class="controls">
                    <input type="text" name="slow_timeback" placeholder="Time Back">
                </div>
            </div>
            </div>

            <div id="disconnect" >
              Chances are your Reader has already added your full time back (Please check your account now before submitting this form). <br />

              Unfortunately due to various reasons (most likely internet related) your chat with <span class="readerName"></span> ended abruptly.
              We apologize for this occurrence.  In most cases your Reader has already returned  time back to your account.
              Please check your account now before submitting this form:

                  <ol style="padding-top:10px;" id="disconnect2" type="a">
                    <li>
                         <label class="radio">
                            <input type="radio" checked="" value="1" name="disconnect_1">
                            &nbsp; I have checked my account &amp; time was restored: <b>I will try to re-enter chat asap.</b>
                         </label>
                    </li>
                    <li>
                         <label class="radio">
                            <input type="radio" value="2" name="disconnect_1">
                            &nbsp; I have checked my account &amp; time was restored: <b>I will save my time for another session and/or reader</b>
                         </label>
                    </li>
                    <li>
                        <label class="radio">
                            <input type="radio" value="3" name="disconnect_1">
                            &nbsp; I have checked my account <b>no minutes were returned:</b> <b>I am requesting that <br/> the reader add time
                            back to my account in the amount of: </b> <input type="text" value="" name="disconnect_time_back">
                        </label>
                    </li>
                 </ol>

           </div>

           <div id="unhappy_reading" >
               Please describe in detail why you feel the reader should return time back to your account<br>
               <textarea style="width:600px;height:100px;" name="unhappy"></textarea> <br>
               <div style="padding:10px;" class="control-group">
                   <label class="control-label" for="slow_timeback">Time Back</label>
                   <div class="controls">
                       <input type="text" name="unhappy_timeback" placeholder="Time Back">
                   </div>
               </div>

               <br>WE ARE SORRY THAT YOU ARE NOT SATISFIED WITH THE SESSION THAT JUST ENDED WITH:
               <span class="readerName"></span>
               OUR POLICY IS THAT WE GUARANTEE SATISFACTION FOR ALL SESSIONS
               IF YOU HONESTLY FEEL THAT THE READER DID NOT LIVE UP TO YOUR EXPECTATIONS- WE WILL GLADLY ADD TIME BACK TO YOUR ACCOUNT
               BUT! IN THE FUTURE YOU MUST TELL THE READER WITHIN THE FIRST 5MINS AFTER HITTING THE 'HIRE ME' BUTTON AND THE READER WILL EITHER GIVE YOU YOUR TIME BACK OR PAUSE THE TIMER SO THAT YOU AND THE READER CAN TRY TO COMMUNICATE BETTER.
               NEXT TIME PLEASE DISCUSS YOUR FEELINGS ABOUT THE SESSION WITH THE READER
           </div>

        </div>
        <div style="width:600px; margin:0px auto; padding:0px;">
            &nbsp;&nbsp;&nbsp;Do you have any suggestions to improve our service?<br /><br />
            <textarea style="width:600px;height:100px;" name="suggest"></textarea>
        </div>
        <div style="padding:10px;text-align:center;">
            <input class="btn btn-primary" type="button" name="Submit" value="Submit">
        </div>
    </form>
</div>

</body>
</html>



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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/media/boilerplate/css/normalize.css">
    <link rel="stylesheet" href="/media/boilerplate/css/main.css">
    <script>
        if (typeof console === "undefined" || typeof console.log === "undefined") {
            console = {};
            console.data = [];
            console.log = function(enter) {
                console.data.push(enter);
            };
        }
    </script>
    <script src="/media/boilerplate/js/vendor/modernizr-2.6.2.min.js"></script>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.1.min.js"><\/script>')</script>
    <script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script src="/media/boilerplate/js/plugins.js"></script>
    <script src='/media/javascript/ion.sound.min.js'></script>
    
    <!-- Bootstrap -->
    <script type="text/javascript" src="/media/bootstrap/js/bootstrap.min.js"></script>
    <link rel=stylesheet type="text/css" href="/media/bootstrap/css/bootstrap.css">

    <style>
        body{ padding:0 20px 0 20px; background:none; overflow: auto; }
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
    </style>
    
</head>
<body>


    <? if(!$hideLogo): ?>
    <div><a href='#' class='logo'>&nbsp;</a></div>
    <? endif; ?>


    <div id='contentDivContainer' class='well' align='left'>
        <?=$content?>
    </div>

    <script>
        $.ready(function(){
           if($.browser.safari) {
                var w = 900; 
                var h = 700;
                window.resizeTo(w, h);
           } 
        });
        
    </script>
</body>
</html>

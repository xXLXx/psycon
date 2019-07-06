
//* VARS
var ajaxTimer  = 0;
var timerCount = 0;
var timerMax   = 30000000; // 500 mins
var increment  = 5000; //  5 seconds 10000;
var object = "";
var client_id = '';

var scripts = document.getElementsByTagName('script');
var myScript = scripts[ scripts.length - 1 ];

var queryString = myScript.src.replace(/^[^\?]+\??/,'');

var params = parseQuery( queryString );

function parseQuery ( query ) {
   var Params = new Object ();
   if ( ! query ) return Params; // return empty object
   var Pairs = query.split(/[;&]/);
   for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if ( ! KeyVal || KeyVal.length != 2 ) continue;
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
   }
   return Params;
}

client_id = params['client_id'] || '';

// init the check
//checkReaders();

// check periodically
ajaxTimer = setInterval(function(){checkReaders();}, increment);

// check for reader status
function checkReaders()
{

    var url = "/ajax_functions.php?mode=getMultipleReaderStatuses&client_id="+client_id;
    
    $.ajax({    
	    url: url,  
	    cache: false,  
	    success: function(retValue){
	    	// convert string to json object 
	    	console.log(" retValue" + retValue);
	        object = eval ("(" + retValue + ")");
	
			for (var i=0;i<object.length;i++)
			{
				var status    = object[i]['status'];
				var member_id = object[i]['member_id'];
				var username  = object[i]['username'];
	
				var anchoObject = $("*[data-username='"+username+"']");
				setStatus(anchoObject, status, username);        
	        }
	        
	        // check to turn off timer if client sits on the page too long
	        if (timerCount > timerMax)
	        {
				clearInterval(ajaxTimer);
			}
			else
			{
				timerCount += increment;
			}
	   	}  
    }); 
       
}// end

    //--- Create PsyCon Class
    var psycon = {};

    //--- Initialize PsyCon Class
    psycon.init = function(params){

        //--- Set all buttons offline
        $('.chatButton').html("Offline");

        //--- Build list of usernames
        var usernameArray = [];

        //--- Loop through each username to build an array
        $('.chatButton').each(function(key, value){
            var username = $(this).attr('data-username');
            usernameArray.push(username);
        });

        //--- Do an API request to check all reader statuses
        $.post('/main/check_multiple_reader_status/', { usernameArray : usernameArray }, function(object){
            psycon.buttonInit(object);
        }, 'json');

    };

    //--- When the request for user statues comes back
    //--- Initialize all the buttons
    psycon.buttonInit = function(object){

        psycon.readerArray = object;

        //--- Button Display
        $('.chatButton').each(function(key, value){
            var username = $(this).attr('data-username');
            var status = psycon.readerArray[username];
            $(this).attr('data-status', status);
            setStatus($(this), status, username);
        });

        //--- Button Event Handler
        $('.chatButton').on('click', function(e){

            //e.preventDefault();

            //--- Obtain Username
            var username = $(this).attr('data-username');
            var status = $(this).attr('data-status');

            if(typeof psycon.readerArray[username] != 'undefined'){

                switch(status){

                    case "online":
                        //var rtn = window.open('/chat/main/index/'+username);
                        //psycon.newChatWindow(username);
                        break;

                    case "offline":
                        alert(username + " is currently offline. Please try again later or page the reader.");
                        break;

                    case "break":
                        alert(username + " is on a break, please check back soon. Or page the reader.");
                        break;
                    case "blocked":
                        alert(username + " has blocked you.");
                        break;

                    case "away":
                        alert(username + " is currently away or in a chat.");
                        break;

                    case "busy":
                        alert(username + " is currently in a private chat session");
                        break;

                }

            }else{
                alert("That reader cannot be found.");
            }

        });

    };

    //--- Autoload PsyCon Button Class
    $( psycon.init() );

    //--- Open a new chat window
    psycon.newChatWindow = function(readerUsername){
        var w = 900;
        var h = 650;
        if ($.browser.safari || $.browser.mozilla) {
           h = 770;
        }
        LeftPosition = (screen.width)?(screen.width-w)/2:100;TopPosition=(screen.height)?(screen.height-h)/2:100;
        settings='width='+w+',height='+h+',top='+TopPosition+',left='+LeftPosition+',scrollbars=1,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes';
        //win=window.open('/chat/main/index/'+readerUsername,'PsyConChatWindow',settings);
        var win = window.open('/chat/main/index/'+readerUsername,'PsyConChatWindow');
        if (win) {
            win.focus();
        } else {
            alert('Please allow popup from our website by changing the browser setting. If you are running IE with popup blocker, you may try press CTRL key while clicking the button. ');
        }
    };

    function setStatus(object, status, username){
        var buttonHTML = "Offline";
        var buttonClass = "";

        object.attr('target','_self');
        object.attr('href', '#');
        
        switch(status)
        {

            case "online":
                if (typeof username != 'undefined') {
                    object.attr('target','_blank');
                    object.attr('href', '/chat/main/index/'+username);
                    buttonHTML = "Online";
                    buttonClass = "btn-warning";
                }
                
                break;

            case "offline":
                buttonHTML = "Offline";
                buttonClass = "";
                break;

            case "blocked":
                buttonHTML = "Blocked";
                buttonClass = "";
                break;

            case "break":
                buttonHTML = "Break";
                buttonClass = "";
                break;

            case "away":
                buttonHTML = "Away";
                buttonClass = "";
                break;

            case "busy":
                buttonHTML = "Busy";
                buttonClass = "";
                break;

        }

        object.html(buttonHTML);
        object.removeClass('btn-danger btn-warning');
        object.addClass(buttonClass);
        object.attr('data-status', status);

    }
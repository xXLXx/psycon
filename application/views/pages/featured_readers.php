<script>
function showFeaturedReader()
{
    var url = "/ajax_functions.php?mode=showFeaturedReader";
    
    $.ajax({    
    url: url,  
    cache: false,  
    success: function(html){ 
        $("#featuredReader").html(html);  
    }  
    });    
}

function showReaderList()
{
    var url = "/ajax_functions.php?mode=showReaderList&count=5";
    
    $.ajax({    
    url: url,  
    cache: false,  
    success: function(html){  
        $("#readersList").html(html);  
    }  
    });    
}


function OpenTestimonialsWindow(reader_id)
{
	url = "<?=SITE_URL?>/modules/testimonials/show.php?reader_id="+reader_id;
	newwin = window.open(url, "testimonials", "scrollbars=yes,menubar=no,resizable=1,location=no,width=600,height=360,left=200,top=200");
	newwin.focus();
}


var mytimer = setInterval("showReaderList()",10000);
var mytimer2 = setInterval("showFeaturedReader()",10000);

$(document).ready(function(){
        showFeaturedReader();
        showReaderList();
});



</script>

<div id="featuredReader" class="online_readers"> 
	<center>
	<img style='margin-top: 100px;' src='/media/images/ajax_spinner.gif'>
	</center>
</div>            
          
      
<div id="readersList" class="online_readers">
  <center>
	<img style='margin-top: 100px;' src='/media/images/ajax_spinner.gif'>
  </center>
</div>


<form method="POST" action="/chat/main/submit_testimonial/<?=$chat_id?>" >
<div class='well'>
    <h2 style="padding-bottom:20px;">Testimonial for Chat  #<?=$chat_id?></h2>

    <div>
        Rating:&nbsp;&nbsp;
        <select style="width:75px;" name="rating">
            <option>1</option>
            <option>1.5</option>
            <option>2</option>
            <option>2.5</option>
            <option>3</option>
            <option>3.5</option>
            <option>4</option>
            <option>4.5</option>
            <option>5</option>
        </select>
    </div>

    <div class="well">
        <textarea style="width:600px; height:200px;" name="review"></textarea>
    </div>
    <input class="btn btn-primary" value="submit" type="submit" />
</div>
</form>
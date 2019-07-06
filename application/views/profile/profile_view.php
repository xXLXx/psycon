
	<style>
	
		.desc{ margin-bottom:35px; line-height:22px; }
	
	</style>

	<div class='content_area'>
	
		<?
		
			$this->load->view('profile/badge');
		
			echo "
			<h2>Biography</h2>
			<div class='desc'>".nl2br($this->reader->data['biography'])."</div>
			
			<h2>Area of Expertise</h2>
			<div class='desc'>".nl2br($this->reader->data['area_of_expertise'])."</div>
			";
			if(count($testis) > 0)
            {
                echo "<h2>Testimonials</h2>";
                echo "<table style='margin-top:5px;' class='table table-bordered'>";
                echo "<tr>
                        <th>Chat ID</th>
                        <th>Date</th>
                        <th>Rating</th>
                        <th>Username</th>
                        <th>Review</th>
                      </tr>";
                foreach($testis as $t)
                {
                   echo "<tr>
                                <td style='vertical-align:top;width:50px;'>{$t['chat_id']}</td>
                                <td style='vertical-align:top;width:200px;'>" .date("m/d/Y @ h:i:s a",strtotime($t['datetime'])) ."</td>
                                <td style='vertical-align:top;width:50px;'>{$t['rating']}</td>
                                <td style='vertical-align:top;width:100px;'>{$t['username']}</td>
                                <td>". nl2br($t['review']) ."</td>
                         </tr>";
                }
                echo "</table>";
            }
		?>

	</div>
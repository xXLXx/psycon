
	<style>
	
		.reader_div{ float:left; width:271px; height:150px; padding:10px; background:#f2f3f6; border:solid 1px #dadbdd; margin-right:10px; margin-bottom:10px; }
		.reader_div:nth-child(3n+3){ margin-right:0; }
		
		.reader_div .profile{  }
		.reader_div .data{ float:right; width:150px; overflow:hidden; }
		.reader_div .data .username a{ font-weight:bold; }
		.reader_div .data .description{ font-size:12px; line-height:20px; }
		.reader_div .data .bio a{ display:inline-block; color:green; font-size:12px; padding:5px 0 0; }
		
	</style>

<?
			$client_id;
			if($this->session->userdata('member_logged'))
			{
				$client_id = $this->session->userdata('member_logged');
				
			} else {
				$client_id = '';
			}
			
?>

	<div class='content_area'>
		<div align='center' style='margin:0 0 25px 0;'>
			<h1 style='margin-bottom:0; padding-bottom:0; '>Meet our Professional Psychic Readers!</h1>
			<div>Login to read more information about each reader and select your favorite.</div>
		</div>

		<div align='center' style='margin:0 0 45px 0;'>
		
			<form action='/psychics/search/' method='POST'>
			
				<input type='hidden' name='page' value='<?=$page?>'>
			
				<table cellPadding='5'>
					<tr>
						<td><?=$this->site->category_select_box('category', $this->input->post('category'))?></td>
						<td><input type='text' name='query' value='<?=$this->input->post('query')?>' style='margin:0;' placeholder='Enter name, username or keyword' class='input-xlarge'></td>
						<td><input type='submit' value='Search' class='btn btn-primary' style='margin:0;'></td>
					</tr>
				</table>
			
			</form>
			
		</div>
		
		<div class='readerList'>
		<?
		
			if(isset($readers) && $readers)
			{
				// pre ordering 
				$ordered_readers = array();
                $online_readers = array();
                $offline_readers = array();
                $blocked_readers = array();
                $break_readers = array();
                $away_readers = array();
                $busy_readers = array();
                $other_readers = array();
                foreach($readers as $r) {
                    switch($r['status']){
                        case "online":
                            $online_readers[] = $r;
                            break;
                        case "offline":
                            $offline_readers[] = $r;
                            break;
                        case "blocked":
                            $blocked_readers[] = $r;
                            break;
                        case "break":
                            $break_readers[] = $r;
                            break;
                        case "away":
                            $away_readers[] = $r;
                            break;
                        case "busy":
                            $busy_readers[] = $r;
                            break;
                        default: 
                            $other_readers[] = $r;
                    }
                }
                $ordered_readers = array_merge($ordered_readers, $online_readers);
                $ordered_readers = array_merge($ordered_readers, $offline_readers);
                $ordered_readers = array_merge($ordered_readers, $blocked_readers);
                $ordered_readers = array_merge($ordered_readers, $break_readers);
                $ordered_readers = array_merge($ordered_readers, $away_readers);
                $ordered_readers = array_merge($ordered_readers, $busy_readers);
                $ordered_readers = array_merge($ordered_readers, $other_readers);
                
				foreach($ordered_readers as $r)
				{
				    $link = "#";
                    $target = "_self";
                    if ($r['status'] == "online") {
                        if (!empty($r['username'])) {
                            $link = "/chat/main/index/" . $r['username'];
                            $target = "_blank";
                        }
                        
                    }
					
					echo "
					<div class='reader_div'>
                        <div style='float:left;width:110px;text-align:center;'>
                            <a href='/profile/{$r['username']}'><img src='{$r['profile']}' class='profile img-polaroid' style='width:100px;'></a>
                            
                            <!-- chat button-->
                            
                            <div class='btn-group' style='margin:15px 0 0;'>
                                <a href='$link' target='$target'  class='btn btn-mini chatButton' data-username='{$r['username']}'></a>
                            </div>
                            
                            <!-- Mod: status button
                            <img src='/media/images/{$r['status']}.jpg' style='width:102px;border:none;margin-top:0px;'>    
                            -->                                                    
                            
                        </div>

						<div style='float:right;width:150px;overflow:hidden;'>
							
							<div class='username'><a href='/profile/{$r['username']}'>{$r['username']}</a></div>

                            <div style='padding:5px 0 0;'>
                                <div class='description'>".(strlen($r['biography'])>130 ? substr($r['biography'], 0, 90)."…" : $r['biography'])."</div>
                                
                                <a href='/profile/{$r['username']}' class='btn btn-mini' style='float:right'>Read Bio</a>
                                
							</div>

						</div>
						<div class='clearfix'></div>
					</div>
					";
				
				}
				
				echo "<div class='clearfix'></div>";
			
			}
		
		?>
		</div>
		
	</div>

    <script src='/chat/button.js?client_id=<? echo $client_id ?>'></script>
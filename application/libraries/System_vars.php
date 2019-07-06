<?

    class system_vars
    {

        function time_generator($init = 0){

            $hours = floor($init / 3600);
            $minutes = floor(($init / 60) % 60);
            $seconds = $init % 60;

            if($hours > 0){

                if($hours==1){
                    return "{$hours} hour, {$minutes} minutes & {$seconds} seconds";
                }
                else{
                    return "{$hours} hours, {$minutes} minutes & {$seconds} seconds";
                }

            }

            elseif($minutes > 0){

                if($minutes==1){
                    return "{$minutes} minute & {$seconds} seconds";
                }else{
                    return "{$minutes} minutes & {$seconds} seconds";
                }

            }

            else{

                return "{$seconds} seconds";

            }

        }
    
    function get_profile_rating($profile_id = null)
    {
    
    	$this->ci =& get_instance();
    	
    	$getRating = $this->ci->db->query
    	("
    	
    		SELECT
    			(SUM(rating) / COUNT(id)) as averageScore
    			
    		FROM
    			reviews
    			
    		WHERE
    			profile_id = {$profile_id}
    		
    	");
    	
    	$rating = $getRating->row_array();
    	
    	return $this->round_to_nearest_half($rating['averageScore']);
    
    }
    
		function round_to_nearest_half($number) 
		{
			return round($number * 2) / 2;
		}
		

    
    function get_profile_subcategories($profile_id, $link_categories = true)
    {
    
    	$this->ci =& get_instance();
    
    	$getSubcategories = $this->ci->db->query("SELECT subcategories.title,subcategories.url,subcategories.parent_id FROM profile_subcategories,subcategories WHERE profile_subcategories.profile_id = {$profile_id} AND subcategories.id = profile_subcategories.subcategory_id GROUP BY profile_subcategories.id ");
		$total_subcategories = $getSubcategories->num_rows();
		$subcategory_string = "";
		
		foreach($getSubcategories->result_array() as $i=>$p)
		{
		
			$category = $this->ci->db->query("SELECT * FROM categories WHERE id = {$p['parent_id']} LIMIT 1");
			$c = $category->row_array();
		
			if(!$link_categories) $subcategory_string .= "{$c['title']} / {$p['title']}";
			else $subcategory_string .= "<a href='/category/main/{$c['url']}'>{$c['title']}</a> / <a href='/category/sub/{$c['url']}/{$p['url']}'>{$p['title']}</a>";
			
			if($total_subcategories!=($i+1))
			{
				$subcategory_string .= "<br />";
			}
		
		}
		
		return $subcategory_string;
    
    }
    
    function seconds_to_minutes($seconds)
    {
    
    	$minutes = ($seconds/60);
		$seconds = ($seconds%60);
		
		return array
		(
			'minutes'=>floor((strlen($minutes)==1 ? "0{$minutes}" : $minutes)),
			'seconds'=>round((strlen($seconds)==1 ? "0{$seconds}" : $seconds))
		);
    	
    }
    
    function is_online($last_activity_datetime, $status) // Needs to be a timestamp
    {
    
    	// If the last activty is greater than 40 seconds
    	// Then the user is NOT online
    	// It shouldn't ever be greater than 40 seconds
    	// Because we are checking their online status every 30 seconds MAX
    	
    	$current_timestamp = time();
    	//$timestamp = strtotime($last_activity_datetime);
    	
    	$difference = abs($current_timestamp-$last_activity_datetime);
    	$isOnline = false;
    	
    	if($difference >= 40) $isOnline = false;
    	else $isOnline = true;
    	
    	if($isOnline)
		{
		
			switch($status)
			{
				
				case "busy":
					$array = array();
					$array['available'] = 0;
					$array['status'] = 'busy';
					$array['html'] = "<div  class='btn btn-warning'>Busy</div>";
					$array['label'] = "<span style='color:#5b5b5b;font-weight:bold;'>Busy</span>";
				break;
				
				case "away":
					$array = array();
					$array['available'] = 0;
					$array['status'] = 'away';
					$array['html'] = "<div class='btn btn-warning'>Away</div>";
					$array['label'] = "<span style='color:#e69494;font-weight:bold;'>Away</span>";
				break;
				
				default:
					$array = array();
					$array['available'] = 1;
					$array['status'] = 'available';
					$array['html'] = "<div class='btn btn-success'>Available</div>";
					$array['label'] = "<span style='color:#498349;font-weight:bold;'>Available</span>";
				break;
				
			}
		
		}
		else
		{
	
			$array = array();
			$array['available'] = 0;
			$array['status'] = 'offline';
			$array['html'] = "<div class='btn'>Offline</div>";
			$array['label'] = "<span style='color:#a29876;font-weight:bold;'>Offline</span>";
		}
		
		return $array;
    
    }
    
    function is_featured($profile_id)
    {
    
    	$this->ci =& get_instance();
    	
    	$date = date("Y-m-d H:i:s");
    	$checkProfile = $this->ci->db->query("SELECT id,end_date FROM featured WHERE profile_id = {$profile_id} AND end_date >= '{$date}' LIMIT 1");
    	
    	if($checkProfile->num_rows()==1)
    	{
    	
    		$f = $checkProfile->row_array();
    		
    		return strtotime($f['end_date']);
    	
    	}
    	else return false;
    	
    }
    
    function member_balance($id)
    {
    
    	$this->ci =& get_instance();
    
    	$getTransactions = $this->ci->db->query
		("			
			SELECT
				(
					(COALESCE((SELECT SUM(amount) FROM transactions WHERE type='deposit' AND member_id = {$id} ),0) + COALESCE((SELECT SUM(amount) FROM transactions WHERE type='earning' AND member_id = {$id} ),0)) -
					( COALESCE((SELECT SUM(amount) FROM transactions WHERE type='payment' AND member_id = {$id} ),0) + COALESCE((SELECT SUM(amount) FROM transactions WHERE type='purchase' AND member_id = {$id} ),0) ) 
				) as balance
				
			FROM
				transactions
		
			LIMIT 1
		");
		
		$transaction = $getTransactions->row_array();
		
		return (isset($transaction['balance']) ? $transaction['balance'] : 0);
    
    }
    
    function addOrdinalNumberSuffix($num)
    {
	    if (!in_array(($num % 100),array(11,12,13))){
	      switch ($num % 10) {
	        // Handle 1st, 2nd, 3rd
	        case 1:  return $num.'st';
	        case 2:  return $num.'nd';
	        case 3:  return $num.'rd';
	      }
	    }
	    return $num.'th';
	  }
    
    function figure_single_score_placement($score_id)
    {
    
    	$this->ci =& get_instance();
    	
    	$getAllScores = $this->ci->db->query("SELECT * FROM scores WHERE game_id = (SELECT game_id FROM scores WHERE id = {$score_id}) ORDER BY score DESC");
    	$score_array = $getAllScores->result_array();
    	
    	$placement = 0;
    	
    	foreach($score_array as $sa)
    	{
    	
    		$placement++;
    	
    		if($sa['id']==$score_id)
    		{
    		
    			break;
    		
    		}
    	
    	}
    	
    	return $this->addOrdinalNumberSuffix($placement);
    
    }
    
    function deal_category($id)
    {
    
    	$CI =& get_instance();
		$sql = $CI->db->query("select * from deal_categories where id = '{$id}' limit 1");
				
		return $sql->row_array();
    
    }
    
    function get_settings()
    {
    
    	$CI =& get_instance();
    	
    	$getSettings = $CI->db->query("SELECT * FROM settings WHERE id = 1 LIMIT 1");
    	return $getSettings->row_array();
    
    }
    
    function meta_tags($url = 'homepage')
    {
    
    	$CI =& get_instance();
    	
    	if(trim($url)=='/'||!trim($url)) $url = 'homepage';
    	
    	$getTags = $CI->db->query("SELECT * FROM meta_tags WHERE url = '{$url}' LIMIT 1");
    	return $getTags->row_array();
    
    }
    
    function get_categories($type = 'featured', $member_id = null)
    {
    
    	$CI =& get_instance();
    	
    	if($type=='featured') $getPages = $CI->db->query("SELECT * FROM categories WHERE show_in_titlebar = 1 ORDER BY sort LIMIT 5 ");
    	elseif($type=='expert')
    	{
    	
    		// Get only categories that the expert has a profile in
    		$sql = "
    		SELECT
				categories.*
			
			FROM
				categories,
				profiles
				
			WHERE
				categories.id = profiles.category_id AND
				profiles.member_id = {$member_id}
				
			GROUP BY
				categories.id
				
			ORDER BY
				categories.sort
				
    		";
    		
    		$getPages = $CI->db->query($sql);
    	
    	}
    	elseif($type=='unregistered')
    	{
    	
    		// Getting categories that the member has NOT already been registered for
    		// 
    		$sql = "
    		SELECT
				categories.*
			
			FROM
				categories
				
			WHERE
				categories.id NOT IN (SELECT category_id FROM profiles WHERE profiles.member_id = {$member_id})
				
			GROUP BY
				categories.id
				
			ORDER BY 
				categories.sort
    		";
    		
    		$getPages = $CI->db->query($sql);
    	
    	}
    	else $getPages = $CI->db->query("SELECT * FROM categories ORDER BY sort");
    	
    	return $getPages->result_array();
    
    }
    
    function get_subcategories($parent_id)
    {
    
    	$CI =& get_instance();
    	
    	$getPages = $CI->db->query("SELECT * FROM subcategories WHERE parent_id = {$parent_id} ORDER BY title ");
    	
    	return $getPages->result_array();
    
    }
    
    function get_pages($type = 'footer')
    {
    
    	$CI =& get_instance();
    	
    	if($type=='footer') $getPages = $CI->db->query("SELECT * FROM pages WHERE add_to_footer = 1 ORDER BY sort ");
    	else $getPages = $CI->db->query("SELECT * FROM pages  ORDER BY sort");
    	
    	return $getPages->result_array();
    
    }
    
    function get_perc_text($content,$percentage = '.20') // Enter percentage in decimal format
    {
    
    	$content = trim( strip_tags($content) );
    	$total_content_length = strlen($content);
    	
    	$total_chars_to_get = $total_content_length * $percentage;
    	
    	return substr($content, 0, $total_chars_to_get);
    
    }
    
    
    function format_url($url)
    {
    
    	$final_url = str_replace('http://','',$url);
    	$final_url = trim($final_url);
    	
    	return "http://".$final_url;
    
    }
    
    function check_like($type,$type_id, $mid)
    {
    	$CI =& get_instance();
   		$check = $CI->db->query("select action from social_connections where action in ('like', 'dislike') and referenced_type = '{$type}' and referenced_id = {$type_id} and member_id = {$mid}");	
    	return $check -> row_array();
    }
    
    function like_dislike($mid, $type_id, $type, $like_type)
    {
    	$CI =& get_instance();
    	$check = $CI->db->query("select * from likes_dislikes where type = '{$type}' and type_id = {$type_id} and member_id = {$mid}");
    	
    	if($check->num_rows() == 0)
    	{
    		$insert['type'] = $type;
    		$insert['type_id'] = $type_id;
    		$insert['member_id'] = $mid;
    		$insert['like_type'] = $like_type;
    		
    		$CI->db->insert("likes_dislikes",$insert);
    		$arr['error'] = 0;
    		$arr['message'] = 'success';
    		
    		
    	}
    	else
    	{
    		$CI->db->where("type", $type);
    		$CI->db->where("type_id", $type_id);
    		$CI->db->where("member_id", $mid);
    		$CI->db->delete("likes_dislikes");
    		
    		$insert['type'] = $type;
    		$insert['type_id'] = $type_id;
    		$insert['member_id'] = $mid;
    		$insert['like_type'] = $like_type;
    		
    		$CI->db->insert("likes_dislikes",$insert);
    		$arr['error'] = 0;
    		$arr['message'] = 'success';

    	}
    	
    	return $arr;
    }
    
    function phone_format($phone_string)
    {
    
    	$one = substr($phone_string, 0,3);
    	$two = substr($phone_string, 3,3);
    	$three = substr($phone_string, 6);
    	
    	return "({$one}) {$two}-{$three}";
    
    }
    
     function message_send($to, $from, $title, $message)
    {
    	$this->ci =& get_instance();
    	    	
    	$array['recipient_id'] = $to;
    	$array['sender_id'] = $from;
    	$array['title'] = $title;
		$array['message'] = $message;
    	$array['date'] = date("Y-m-d H:i:s");
    	
    	$this->ci->db->insert("messages", $array);
    	return $this->ci->db->insert_id();
    }

    
    function message_reply($message_id, $member_id, $reply, $reply_count)
    {
    	$this->ci =& get_instance();
    	
    	$getMessage = $this->ci->db->query("select * from messages where id = {$message_id}");
 		$m = $getMessage -> row_array();
    	
    	if($member_id == $m['recipient_id'])
    	{
    		$update['sender_read'] = 0;
    	}
    	else
    	{
    		$update['recipient_read'] = 0;	
    	}
    	
    	$update['replies'] = $reply_count += 1;
    	$update['recipient_hide'] = 0;
    	$update['sender_hide'] = 0;
    	$this->ci->db->where("id", $message_id);
    	$this->ci->db->update("messages", $update);
    	
    	$array['message_id'] = $message_id;
    	$array['member_id'] = $member_id;
		$array['reply'] = $reply;
    	$array['date'] = date("Y-m-d H:i:s");
    	
    	$this->ci->db->insert("message_replies", $array);
    }
    
    function recurse_directories($subcategoryArray = false, $level = 0, $selected_category = null)
	{
	
		$this->ci =& get_instance();
	
		$content = "";
	
		if($subcategoryArray)
		{
			if($level == 0)
			{
				$content .="<ul id='directory'>";
			}
			else
			{
				$content .="<ul>";
			}
			foreach($subcategoryArray as $c)
			{
			
				// Search For Subcategories
				$getSubCategories = $this->ci->db->query("SELECT * FROM directory WHERE parent = {$c['id']} ORDER BY title ");
				
				if($getSubCategories->num_rows() > 0)
				{
				
					// The Final Category
					$content .= "<li class='upper-title'><span>{$c['title']}</span>";
					
					$content .= $this->recurse_directories($getSubCategories->result_array(), $level+1, $selected_category);
				
				}
				else
				{
				
					// Just another subcategory
					$content .= "<li class='lower-title'><span><input type='radio' name='directory' value='{$c['id']}'".($selected_category==$c['id'] ? " checked" : "")."> {$c['title']}</span></li>";
					//$content .= "<li><a href='?{$c['id']}'>{$c['title']}</a></li>";
				}
			
			}
			$content .="</li></ul>";
		}
	
		return $content;
	
	}
    
    function subval_sort($a,$subkey)
    {
    
    	if(count($a)==0) return false;
    	else
    	{
    
		foreach($a as $k=>$v) {
			$b[$k] = strtolower($v[$subkey]);
		}
		asort($b);
		foreach($b as $key=>$val) {
			$c[] = $a[$key];
		}
		return $c;
		
		}
	}
	
	function in_subval_sort($a,$subkey)
    {
		foreach($a as $k=>$v)
		{
			$b[$k] = strtolower($v[$subkey]);
		}
		
		asort($b);
		
		foreach($b as $key=>$val)
		{
			$c[] = $a[$key];
		}
		
		return $c;
	}
	
	function get_member($id)
	{
	
		$CI =& get_instance();
		
		$m = $CI->db->query
		("
			SELECT 
				profiles.id as profile_id,
				profiles.*,
				members.*
			
			FROM
				members 
			
			LEFT JOIN profiles ON profiles.id = members.id
			
			where 
				members.id = {$id}
				
			LIMIT 1
		");
		
		$member = $m->row_array();
		
		if($member['profile_image'] != "")
		{
			$member['profile'] = "/media/assets/{$member['profile_image']}";
		}
		
		else
		{
			$member['profile'] = "/media/images/no_profile_image.jpg";
		}
		
		
		return $member;
		
	}
	
	function get_member_by_cookie_token($token)
	{
		$CI =& get_instance();
		$m = $CI->db->query("select * from members where cookie_token = {$token} limit 1");
		
		$member = $m->row_array();
				
		return $member;
	}
	
	function get_university($id_or_url)
	{
		$CI =& get_instance();
		$u = $CI->db->query("select * from universities where url = '{$id_or_url}' OR id = '{$id_or_url}' limit 1");
		
		$member = $u->row_array();
				
		return $member;
	}
	
	function strip_html_tags( $html )
	{
	    
	    # Remove Styles
	    $html = preg_replace('/<(style|script).*?<\/\1>/xmsi', '', $html);
	    
	    # Remove HTML
	    $html = strip_tags($html);
	    
	    # Remove Extra Spaces
	    $html = preg_replace('/\s\s+/', ' ', $html);
	    
	    # Reuturn Nice Clean Version of Text
	    return trim( $html );
	    
	}
    
    function get_new_messages()
    {
    	$CI =& get_instance();
    	$getMessages = $CI->db->query("select * from messages where recipient_id = 1 and recipient_read = 0");
    	
    	
    	return $getMessages -> num_rows();
    	
    }
    
    function gmtoff_to_timezone($gmt)
    {
    	$zones = array(
                        'UM12'        => -12,
                        'UM11'        => -11,
                        'UM10'        => -10,
                        'UM95'        => -9.5,
                        'UM9'        => -9,
                        'UM8'        => -8,
                        'UM7'        => -7,
                        'UM6'        => -6,
                        'UM5'        => -5,
                        'UM45'        => -4.5,
                        'UM4'        => -4,
                        'UM35'        => -3.5,
                        'UM3'        => -3,
                        'UM2'        => -2,
                        'UM1'        => -1,
                        'UTC'        => 0,
                        'UP1'        => +1,
                        'UP2'        => +2,
                        'UP3'        => +3,
                        'UP35'        => +3.5,
                        'UP4'        => +4,
                        'UP45'        => +4.5,
                        'UP5'        => +5,
                        'UP55'        => +5.5,
                        'UP575'        => +5.75,
                        'UP6'        => +6,
                        'UP65'        => +6.5,
                        'UP7'        => +7,
                        'UP8'        => +8,
                        'UP875'        => +8.75,
                        'UP9'        => +9,
                        'UP95'        => +9.5,
                        'UP10'        => +10,
                        'UP105'        => +10.5,
                        'UP11'        => +11,
                        'UP115'        => +11.5,
                        'UP12'        => +12,
                        'UP1275'    => +12.75,
                        'UP13'        => +13,
                        'UP14'        => +14
                    );
                    
    	return array_search($gmt, $zones);
    }
    
    function clean_symbols($String = null)
	{
	
		$String = str_replace('Ô',"'", $String);
		$String = str_replace('Õ',"'", $String);
		$String = str_replace('â',',', $String);
		$String = str_replace('Ò','"', $String);
		$String = str_replace('Ó','"', $String);
		
		return $String;
	
	}
	
	function merchant_search($string, $user_id)
	{
		$CI =& get_instance();
		$farr = array();
	
	
		$str_arr = str_word_count($string, 1);
		$count = str_word_count($string);

		$getMercs = $CI->db->query("select distinct id, company_logo, company_name, company_description 
												from merchants m where company_name like '%{$string}%' 
												and id not in (select merchant_id from subscriptions where m.id = merchant_id and board_id = {$user_id} and status = 2);							   
												");
		if($getMercs -> num_rows() == 0)
		{
			if($count < 2)
			{
				$getMercs = $CI->db->query("select distinct id, company_logo, company_name, company_description 
												from merchants m where company_name like '%{$string}%' 
												and id not in (select merchant_id from subscriptions where m.id = merchant_id and board_id = {$user_id} and status = 2);							   
												");
				$farr  = array_merge($farr, $getMercs->result_array());
			}
			else
			{
				$where = "";
				foreach ($str_arr as $str)
				{
					$where .= " or company_name like '%{$str}%' ";	
				}
				$where = "(" . substr($where, 3)   .")";
				$getMercs = $CI->db->query("select distinct id, company_logo, company_name, company_description 
												from merchants m where {$where} 
												and id not in (select merchant_id from subscriptions where m.id = merchant_id and board_id = {$user_id} and status = 2);							   
												");
				$farr = $getMercs -> result_array();
			}
		}
		else
		{
			$farr = $getMercs -> result_array();
		}
		
		if(count($farr) > 0)
		{
			foreach($farr as $key=>$a)
			{
					$farr[$key]['sort'] = levenshtein($a['company_name'],$string);
			}
			$farr = $this->subval_sort($farr,'sort');
			return $farr;
		}
		else
		{
			return false;
		}
		
	}
	
	function aes_encrypt($text)
    {
		$CI =& get_instance();
		$salt = $CI->config->item('encryption_key');
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }
	
	function aes_decrypt($text)
	{
		$CI =& get_instance();
		$salt = $CI->config->item('encryption_key');
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
	}
	
	function sha_password($pwd)
	{
		$CI =& get_instance();
		$salt = $CI->config->item('encryption_key');
		$pwd =  base64_encode(hash('sha512', $pwd, $salt));
		return $pwd;
	}
	
	function remove_non_numeric($string) 
	{
		return preg_replace('/\D/', '', $string);
	}
	
    function calculate_gmt_date($date = null, $timezone_offset = null, $daylight_savings = null)
    {
    	$current_time = gmt_to_local(time(),$timezone_offset,$daylight_savings);
    	$diff = abs($date - $current_time);
    	return strtotime("+{$diff} seconds");
    }
        
    function age($birthday)
    {
	    list($year,$month,$day) = explode("-",$birthday);
	    $year_diff  = date("Y") - $year;
	    $month_diff = date("m") - $month;
	    $day_diff   = date("d") - $day;
	    if ($day_diff < 0 || $month_diff < 0)
	      $year_diff--;
	    return $year_diff;
	  }
    
    function get_subuser($subUserId = null)
    {
    
    	$CI =& get_instance();
    	
    	$getSubUser = $CI->db->query("SELECT * FROM merchants_sub_accounts WHERE id = {$subUserId} LIMIT 1");
    	
    	if($getSubUser->num_rows()==0) return false;
    	else return $getSubUser->row_array();
    
    }
    
    function date_diff($date1,$date2)
    {
    
		$start_ts = strtotime($date1);
		$end_ts = strtotime($date2);
		$diff = $end_ts - $start_ts;
		return round($diff / 86400);
    
    }
    
    function iso8859_decode($string = null)
    {
    
    	// Check if ISO Decoding needs to be done
    	if(strpos(strtolower($string), 'iso')===false){}
    	else
    	{
    
	    	$string = str_replace("=?ISO-8859-1?Q?","",$string);
	    	$string = str_replace("?=","",$string);
	    	
	    	// Replace ISO Spaces
	    	$string = str_replace("_"," ",$string);
	    	
	    	// Map Chars
	    	$string = str_replace("=F1","n",$string);
    	
    	}
    	
    	return $string;
    
    }
    
    function timezone($lat=null,$lon=null)
    {
    
    	$URL = "http://api.geonames.org/timezoneJSON?lat={$lat}&lng={$lon}&username=rckehoe";
    	
    	$JSON = file_get_contents($URL);
    	
    	return json_decode($JSON);

    
    }
    
    function get_timezone($lat=null,$lon=null)
    {
    
    	$URL = "http://api.geonames.org/timezoneJSON?lat={$lat}&lng={$lon}&username=rckehoe";
    	
    	$JSON = file_get_contents($URL);
    	$result = json_decode($JSON);
    	
    	/*
    	[time] => 2011-10-26 07:47
	    [countryName] => United States
	    [sunset] => 2011-10-26 18:05
	    [rawOffset] => -7
	    [dstOffset] => -6
	    [countryCode] => US
	    [gmtOffset] => -7
	    [lng] => -104.73785
	    [sunrise] => 2011-10-26 07:19
	    [timezoneId] => America/Denver
	    [lat] => 38.887313
    	*/
    	
    	if($result->time)
    	{
    	
    		// Return
	    	$arr['current_time'] = $result->time;
	    	$arr['sunset_time'] = $result->sunset;
	    	$arr['gmt_offset'] = $result->gmtOffset;
	    	$arr['timezone_id'] = $result->timezoneId;
	    	
	    	return $arr;
    		
    	}
    	else
    	{
    	
    		return false;
    	
    	}
    
    }
    
    function get_local_time($lat,$lon)
    {
    
    	$URL = "http://www.earthtools.org/timezone-1.1/{$lat}/{$lon}";
    	$XML = file_get_contents($URL);
    	
    	$localData = (array) new SimpleXMLElement($XML);
    	return date("Y-m-d H:i:s", strtotime($localData['localtime']));
    	
    
    }
    
    function plot_zip($zipcode = null)
    {
    
    	$URL = "http://api.geonames.org/postalCodeSearchJSON?postalcode={$zipcode}&maxRows=10&username=rckehoe";
    	
    	$JSON = file_get_contents($URL);
    	$o = json_decode($JSON);
    	
    	$result = $o->postalCodes[0];
    	
    	if($result)
    	{
    	
    		// Return
	    	$arr['city'] = $result->adminName2;
	    	$arr['state'] = $result->adminName1;
	    	$arr['state_abbrev'] = $result->adminCode1;
	    	$arr['country'] = $result->countryCode;
	    	$arr['lat'] = $result->lat;
	    	$arr['lon'] = $result->lng;
	    	
	    	return $arr;
    		
    	}
    	else
    	{
    	
    		return false;
    	
    	}

    }
   
    
    function get_merchant($MerchantId = null)
    {
    
    	if(!$MerchantId) return false;
    	else
    	{
    	
    		$CI =& get_instance();
    		
    		$getRow = $CI->db->query("SELECT * FROM `merchants` WHERE `id`=\"{$MerchantId}\" LIMIT 1");
    		
    		if($getRow->num_rows())
    		{
    		
    			return $getRow->row_array();
    		}
    		else
    		{
    			return false;
    		}
    	
    	}
    
    }
    
    function settings_select_box($SettingsFieldName,$SelectName,$PreSelectedValue=null)
    {
    
    	$s = $this->site_settings();
    	$OptionArray = explode("\n", $s[$SettingsFieldName]);
    	
    	$r = "<select style='width:auto;' name='{$SelectName}' style='width:auto;'><option value=''></option>";
    	
    	foreach($OptionArray as $o)
    	{
    	
    		$r .= "<option value='{$o}'".(trim($PreSelectedValue)==trim($o) ? " selected" : "").">{$o}</option>";
    	
    	}
    	
    	$r .= "</select>";
    	
    	return $r;
    
    }
    
    function registration_code($code)
    {
    
    	$CI =& get_instance();
    
    	$getSS = $CI->db->query("SELECT * FROM `merchants` WHERE `signup_key`=\"{$code}\" LIMIT 1");
    	
    	if($getSS->num_rows()==0) return false;
    	else return $getSS->row_array();
    
    }
    
    function site_settings()
    {
    
    	$CI =& get_instance();
    
    	$getSS = $CI->db->query("SELECT * FROM `settings` WHERE `id`=\"1\" LIMIT 1");
    	return $getSS->row_array();
    
    }
    
    function get_page($PAGEREF = '')
    {
    
    	if(!$PAGEREF) return false;
    	else
    	{
    	
    		$CI =& get_instance();
    		
    		if(is_numeric($PAGEREF)) $getP = $CI->db->query("SELECT * FROM `pages` WHERE `id`=\"{$PAGEREF}\" LIMIT 1");
    		else $getP = $CI->db->query("SELECT * FROM `pages` WHERE `url`=\"{$PAGEREF}\" LIMIT 1");
    		
    		if($getP->num_rows()==0) return false;
    		else return $getP->row_array();
    	
    	}
    
    }
    
    function meta_data()
    {
    	$CI =& get_instance();
    	
    	$PageURL = $CI->uri->uri_string();
    	if(!$PageURL) $PageURL = "homepage";
    	
    	$PageURLReformed = substr($PageURL,0,1);
    	if($PageURLReformed=='/') $PageURLReformed = substr($PageURL,1);

		$GET_MD = $CI->db->query("SELECT * FROM `meta_data` WHERE (`url`=\"{$PageURL}\" OR `url`=\"{$PageURLReformed}\") LIMIT 1");
		if($GET_MD->num_rows==0) return false;
		else
		{
		
			$m = $GET_MD->row_array();
		
			return array(
			
				"title"=>$m['title'],
				"keywords"=>$m['keywords'],
				"description"=>$m['description']
			
			);
		}
    	
    }

	function state_array()
	{
	    return array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia",'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa",  'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland", 'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma", 'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming");
	}
	
	function state_array_select_box($SelectName = 'state', $SelectedItem = '', $OnChange = '')
	{
	
	    $SlectBox = "<select style='width:auto;' name='{$SelectName}' $OnChange><option value=''>Select A State</option>";
	
	        foreach($this->state_array() as $stAb=>$stNam){
	        $SlectBox .= "<option value='$stAb'"; if(trim($SelectedItem)==trim($stAb)){$SlectBox .= " selected";} $SlectBox .= ">$stNam</option>\n";
	        }
	
	    $SlectBox .= "</select>";
	
	    return $SlectBox;
	
	}
        
	function country_array_select_box($SelectName = 'state', $SelectedItem = 'US', $OnChange = '')
	{
	
	    $SlectBox = "<select style='width:auto;' name='{$SelectName}' $OnChange><option value=''>Select A Country</option>";
	
	        foreach($this->country_array() as $stAb=>$stNam){
	        $SlectBox .= "<option value='$stAb'"; if(trim($SelectedItem)==trim($stAb)){$SlectBox .= " selected";} $SlectBox .= ">$stNam</option>\n";
	        }
	
	    $SlectBox .= "</select>";
	
	    return $SlectBox;
	
	}
        
    function country_array(){

		return array(
        'US'=>'United States',
        'CA'=>'Canada',
		'AF'=>'Afghanistan',
		'AL'=>'Albania',
		'DZ'=>'Algeria',
		'AS'=>'American Samoa',
		'AD'=>'Andorra',
		'AO'=>'Angola',
		'AI'=>'Anguilla',
		'AQ'=>'Antarctica',
		'AG'=>'Antigua And Barbuda',
		'AR'=>'Argentina',
		'AM'=>'Armenia',
		'AW'=>'Aruba',
		'AU'=>'Australia',
		'AT'=>'Austria',
		'AZ'=>'Azerbaijan',
		'BS'=>'Bahamas',
		'BH'=>'Bahrain',
		'BD'=>'Bangladesh',
		'BB'=>'Barbados',
		'BY'=>'Belarus',
		'BE'=>'Belgium',
		'BZ'=>'Belize',
		'BJ'=>'Benin',
		'BM'=>'Bermuda',
		'BT'=>'Bhutan',
		'BO'=>'Bolivia',
		'BA'=>'Bosnia And Herzegovina',
		'BW'=>'Botswana',
		'BV'=>'Bouvet Island',
		'BR'=>'Brazil',
		'IO'=>'British Indian Ocean Territory',
		'BN'=>'Brunei',
		'BG'=>'Bulgaria',
		'BF'=>'Burkina Faso',
		'BI'=>'Burundi',
		'KH'=>'Cambodia',
		'CM'=>'Cameroon',
		'CV'=>'Cape Verde',
		'KY'=>'Cayman Islands',
		'CF'=>'Central African Republic',
		'TD'=>'Chad',
		'CL'=>'Chile',
		'CN'=>'China',
		'CX'=>'Christmas Island',
		'CC'=>'Cocos (Keeling) Islands',
		'CO'=>'Columbia',
		'KM'=>'Comoros',
		'CG'=>'Congo',
		'CK'=>'Cook Islands',
		'CR'=>'Costa Rica',
		'CI'=>'Cote D\'Ivorie (Ivory Coast)',
		'HR'=>'Croatia (Hrvatska)',
		'CU'=>'Cuba',
		'CY'=>'Cyprus',
		'CZ'=>'Czech Republic',
		'CD'=>'Democratic Republic Of Congo (Zaire)',
		'DK'=>'Denmark',
		'DJ'=>'Djibouti',
		'DM'=>'Dominica',
		'DO'=>'Dominican Republic',
		'TP'=>'East Timor',
		'EC'=>'Ecuador',
		'EG'=>'Egypt',
		'SV'=>'El Salvador',
		'GQ'=>'Equatorial Guinea',
		'ER'=>'Eritrea',
		'EE'=>'Estonia',
		'ET'=>'Ethiopia',
		'FK'=>'Falkland Islands (Malvinas)',
		'FO'=>'Faroe Islands',
		'FJ'=>'Fiji',
		'FI'=>'Finland',
		'FR'=>'France',
		'FX'=>'France, Metropolitan',
		'GF'=>'French Guinea',
		'PF'=>'French Polynesia',
		'TF'=>'French Southern Territories',
		'GA'=>'Gabon',
		'GM'=>'Gambia',
		'GE'=>'Georgia',
		'DE'=>'Germany',
		'GH'=>'Ghana',
		'GI'=>'Gibraltar',
		'GR'=>'Greece',
		'GL'=>'Greenland',
		'GD'=>'Grenada',
		'GP'=>'Guadeloupe',
		'GU'=>'Guam',
		'GT'=>'Guatemala',
		'GN'=>'Guinea',
		'GW'=>'Guinea-Bissau',
		'GY'=>'Guyana',
		'HT'=>'Haiti',
		'HM'=>'Heard And McDonald Islands',
		'HN'=>'Honduras',
		'HK'=>'Hong Kong',
		'HU'=>'Hungary',
		'IS'=>'Iceland',
		'IN'=>'India',
		'ID'=>'Indonesia',
		'IR'=>'Iran',
		'IQ'=>'Iraq',
		'IE'=>'Ireland',
		'IL'=>'Israel',
		'IT'=>'Italy',
		'JM'=>'Jamaica',
		'JP'=>'Japan',
		'JO'=>'Jordan',
		'KZ'=>'Kazakhstan',
		'KE'=>'Kenya',
		'KI'=>'Kiribati',
		'KW'=>'Kuwait',
		'KG'=>'Kyrgyzstan',
		'LA'=>'Laos',
		'LV'=>'Latvia',
		'LB'=>'Lebanon',
		'LS'=>'Lesotho',
		'LR'=>'Liberia',
		'LY'=>'Libya',
		'LI'=>'Liechtenstein',
		'LT'=>'Lithuania',
		'LU'=>'Luxembourg',
		'MO'=>'Macau',
		'MK'=>'Macedonia',
		'MG'=>'Madagascar',
		'MW'=>'Malawi',
		'MY'=>'Malaysia',
		'MV'=>'Maldives',
		'ML'=>'Mali',
		'MT'=>'Malta',
		'MH'=>'Marshall Islands',
		'MQ'=>'Martinique',
		'MR'=>'Mauritania',
		'MU'=>'Mauritius',
		'YT'=>'Mayotte',
		'MX'=>'Mexico',
		'FM'=>'Micronesia',
		'MD'=>'Moldova',
		'MC'=>'Monaco',
		'MN'=>'Mongolia',
		'MS'=>'Montserrat',
		'MA'=>'Morocco',
		'MZ'=>'Mozambique',
		'MM'=>'Myanmar (Burma)',
		'NA'=>'Namibia',
		'NR'=>'Nauru',
		'NP'=>'Nepal',
		'NL'=>'Netherlands',
		'AN'=>'Netherlands Antilles',
		'NC'=>'New Caledonia',
		'NZ'=>'New Zealand',
		'NI'=>'Nicaragua',
		'NE'=>'Niger',
		'NG'=>'Nigeria',
		'NU'=>'Niue',
		'NF'=>'Norfolk Island',
		'KP'=>'North Korea',
		'MP'=>'Northern Mariana Islands',
		'NO'=>'Norway',
		'OM'=>'Oman',
		'PK'=>'Pakistan',
		'PW'=>'Palau',
		'PA'=>'Panama',
		'PG'=>'Papua New Guinea',
		'PY'=>'Paraguay',
		'PE'=>'Peru',
		'PH'=>'Philippines',
		'PN'=>'Pitcairn',
		'PL'=>'Poland',
		'PT'=>'Portugal',
		'PR'=>'Puerto Rico',
		'QA'=>'Qatar',
		'RE'=>'Reunion',
		'RO'=>'Romania',
		'RU'=>'Russia',
		'RW'=>'Rwanda',
		'SH'=>'Saint Helena',
		'KN'=>'Saint Kitts And Nevis',
		'LC'=>'Saint Lucia',
		'PM'=>'Saint Pierre And Miquelon',
		'VC'=>'Saint Vincent And The Grenadines',
		'SM'=>'San Marino',
		'ST'=>'Sao Tome And Principe',
		'SA'=>'Saudi Arabia',
		'SN'=>'Senegal',
		'SC'=>'Seychelles',
		'SL'=>'Sierra Leone',
		'SG'=>'Singapore',
		'SK'=>'Slovak Republic',
		'SI'=>'Slovenia',
		'SB'=>'Solomon Islands',
		'SO'=>'Somalia',
		'ZA'=>'South Africa',
		'GS'=>'South Georgia And South Sandwich Islands',
		'KR'=>'South Korea',
		'ES'=>'Spain',
		'LK'=>'Sri Lanka',
		'SD'=>'Sudan',
		'SR'=>'Suriname',
		'SJ'=>'Svalbard And Jan Mayen',
		'SZ'=>'Swaziland',
		'SE'=>'Sweden',
		'CH'=>'Switzerland',
		'SY'=>'Syria',
		'TW'=>'Taiwan',
		'TJ'=>'Tajikistan',
		'TZ'=>'Tanzania',
		'TH'=>'Thailand',
		'TG'=>'Togo',
		'TK'=>'Tokelau',
		'TO'=>'Tonga',
		'TT'=>'Trinidad And Tobago',
		'TN'=>'Tunisia',
		'TR'=>'Turkey',
		'TM'=>'Turkmenistan',
		'TC'=>'Turks And Caicos Islands',
		'TV'=>'Tuvalu',
		'UG'=>'Uganda',
		'UA'=>'Ukraine',
		'AE'=>'United Arab Emirates',
		'UK'=>'United Kingdom',
		'UM'=>'United States Minor Outlying Islands',
		'UY'=>'Uruguay',
		'UZ'=>'Uzbekistan',
		'VU'=>'Vanuatu',
		'VA'=>'Vatican City (Holy See)',
		'VE'=>'Venezuela',
		'VN'=>'Vietnam',
		'VG'=>'Virgin Islands (British)',
		'VI'=>'Virgin Islands (US)',
		'WF'=>'Wallis And Futuna Islands',
		'EH'=>'Western Sahara',
		'WS'=>'Western Samoa',
		'YE'=>'Yemen',
		'YU'=>'Yugoslavia',
		'ZM'=>'Zambia',
		'ZW'=>'Zimbabwe'
		);
		
	}

	function map_address($Address='',$City='',$State='',$Zip='', $MapWidth = '', $MapHeight = '', $Zoom = '', $EnableGoogleBar = false)
	{
	    $CI =& get_instance();
	    $CI->load->library('googlemaps');

	    $GoogleMapsAPI = $CI->config->item('google_maps_api');

	    $config['apikey'] = $GoogleMapsAPI;
	    $config['center_address'] = "{$Address} {$City}, {$State} {$Zip}";
	    if($MapWidth) $config['map_width'] = $MapWidth;
	    if($MapHeight) $config['map_height'] = $MapHeight;
	    if($Zoom) $config['zoom'] = $Zoom;
	    if($EnableGoogleBar) $config['display_google_bar'] = TRUE;
	    //

	    $CI->googlemaps->initialize($config);

	    $marker = array();
	    $marker['address'] = "{$Address} {$City}, {$State} {$Zip}";

	    $CI->googlemaps->add_marker($marker);

	    return $CI->googlemaps->create_map();
	}

	/* IP Info DB - IP Trace */
	function ip_trace($ip = NULL)
        {

           if(!$ip){

               $CI =& get_instance();
               $ip = $CI->input->ip_address();

           }

           $d = file_get_contents("http://www.ipinfodb.com/ip_query.php?ip={$ip}&output=xml&timezone=false");

           //Use backup server if cannot make a connection
           if (!$d){
             $backup = file_get_contents("http://backup.ipinfodb.com/ip_query.php?ip={$ip}&output=xml&timezone=false");
             $answer = new SimpleXMLElement($backup);
             if (!$backup) return false; // Failed to open connection
           }else{
             $answer = new SimpleXMLElement($d);
           }

           $ip = $answer->Ip;
           $country_code = $answer->CountryCode;
           $country_name = $answer->CountryName;
           $region_name = $answer->RegionName;
           $city = $answer->City;
           $zippostalcode = $answer->ZipPostalCode;
           $latitude = $answer->Latitude;
           $longitude = $answer->Longitude;

           //Return the data as an array
           if(trim($city) && trim($region_name)) return array('ip' => $ip, 'country_code' => $country_code, 'country_name' => $country_name, 'region_name' => $region_name, 'city' => $city, 'zippostalcode' => $zippostalcode, 'latitude' => $latitude, 'longitude' => $longitude);
           else return false;

        }
        
        
        function dob_custom($FieldName = "dob", $month_value=null, $day_value=null, $year_value=null)
        {
        	
           	$ReturnText = "
        	<table>
        		<tr>
        		<td>
	        		<select style='width:auto;' name='{$FieldName}_month'>
	        			<option value=''>Month</option>
		        		<option value='01'".($month_value=='01' ? " selected" : "").">01 - January</option>
		        		<option value='02'".($month_value=='02' ? " selected" : "").">02 - February</option>
		        		<option value='03'".($month_value=='03' ? " selected" : "").">03 - March</option>
		        		<option value='04'".($month_value=='04' ? " selected" : "").">04 - April</option>
		        		<option value='05'".($month_value=='05' ? " selected" : "").">05 - May</option>
		        		<option value='06'".($month_value=='06' ? " selected" : "").">06 - June</option>
		        		<option value='07'".($month_value=='07' ? " selected" : "").">07 - July</option>
		        		<option value='08'".($month_value=='08' ? " selected" : "").">08 - August</option>
		        		<option value='09'".($month_value=='09' ? " selected" : "").">09 - September</option>
		        		<option value='10'".($month_value=='10' ? " selected" : "").">10 - October</option>
		        		<option value='11'".($month_value=='11' ? " selected" : "").">11 - November</option>
		        		<option value='12'".($month_value=='12' ? " selected" : "").">12 - December</option>
	        		</select>
		        </td>
		        <td>
		        	<select style='width:auto;' name='{$FieldName}_day'>
		        	
		        		<option value=''>Day</option>";
		        		
		        			for ($i = 1; $i <= 31; $i++)
		        			{
		        				$n = (strlen($i)==1 ? "0".$i : $i);
		        				$ReturnText .= "<option value='{$n}'".($day_value==$n ? " selected" : "").">{$n}</option>";
		        			}
		        		
		        		$ReturnText .= "
		        	</select>
        		</td>
        		<td>
        			<select style='width:auto;' name='{$FieldName}_year'>
        			
        				<option value=''>Year</option>";
        	
		        		for ($i = (date("Y")-5); $i > (date("Y")-100); $i--)
		        		{
		        			$ReturnText .= "<option value='{$i}'".($year_value==$i ? " selected" : "").">{$i}</option>";
		        		}
        	
        			$ReturnText .= "</select>
        			</td>
        		</tr>
        	</table>
        	";
        	
        	return $ReturnText;
        
        }
        
        function dob($FieldName = 'dob', $Selectedvalue = null, $className = '')
        {
        
        	list($pYear,$pMonth,$pDay) = explode("-", $SelectedValue);
        
        	$ReturnText = "
        	
        	<select style='width:auto;' name='{$FieldName}_month' class='{$className}'>
        		<option value=''></option>
        		<option value='01'".($pMonth=='01' ? " selected" : "").">01 - January</option>
        		<option value='02'".($pMonth=='02' ? " selected" : "").">02 - February</option>
        		<option value='03'".($pMonth=='03' ? " selected" : "").">03 - March</option>
        		<option value='04'".($pMonth=='04' ? " selected" : "").">04 - April</option>
        		<option value='05'".($pMonth=='05' ? " selected" : "").">05 - May</option>
        		<option value='06'".($pMonth=='06' ? " selected" : "").">06 - June</option>
        		<option value='07'".($pMonth=='07' ? " selected" : "").">07 - July</option>
        		<option value='08'".($pMonth=='08' ? " selected" : "").">08 - August</option>
        		<option value='09'".($pMonth=='09' ? " selected" : "").">09 - September</option>
        		<option value='10'".($pMonth=='10' ? " selected" : "").">10 - October</option>
        		<option value='11'".($pMonth=='11' ? " selected" : "").">11 - November</option>
        		<option value='12'".($pMonth=='12' ? " selected" : "").">12 - December</option>
        	</select>
        	
        	<select style='width:auto;' name='{$FieldName}_day' class='{$className}'>
        		<option value=''></option>";
        		
        			for ($i = 1; $i <= 31; $i++)
        			{
        				$n = (strlen($i)==1 ? "0".$i : $i);
        				$ReturnText .= "<option value='{$n}'".($pDay==$n ? " selected" : "").">{$n}</option>";
        			}
        		
        		$ReturnText .= "
        	</select>
        	
        	<select style='width:auto;' name='{$FieldName}_year' class='{$className}'>
        		<option value=''></option>";
        	
        		for ($i = (date("Y")-5); $i > (date("Y")-100); $i--)
        		{
        			$ReturnText .= "<option value='{$i}'".($pYear==$i ? " selected" : "").">{$i}</option>";
        		}
        	
        	$ReturnText .= "</select>";
        	
        	return $ReturnText;
        
        }
        
        function exp_month($FieldName = 'cc_exp_month', $SelectedValue = null)
        {
        
        	$ReturnArray = "
        	<select style='width:auto;' name='{$FieldName}'>
				<option value=''>Select Month</option>
				<option value='01'".($SelectedValue=='01' ? " selected" : "").">01 - January</option>
				<option value='02'".($SelectedValue=='02' ? " selected" : "").">02 - February</option>
				<option value='03'".($SelectedValue=='03' ? " selected" : "").">03 - March</option>
				<option value='04'".($SelectedValue=='04' ? " selected" : "").">04 - April</option>
				<option value='05'".($SelectedValue=='05' ? " selected" : "").">05 - May</option>
				<option value='06'".($SelectedValue=='06' ? " selected" : "").">06 - June</option>
				<option value='07'".($SelectedValue=='07' ? " selected" : "").">07 - July</option>
				<option value='08'".($SelectedValue=='08' ? " selected" : "").">08 - August</option>
				<option value='09'".($SelectedValue=='09' ? " selected" : "").">09 - September</option>
				<option value='10'".($SelectedValue=='10' ? " selected" : "").">10 - October</option>
				<option value='11'".($SelectedValue=='11' ? " selected" : "").">11 - November</option>
				<option value='12'".($SelectedValue=='12' ? " selected" : "").">12 - December</option>
			</select>
        	";
        	
        	return $ReturnArray;
        
        }
        
        function exp_year($FieldName = 'cc_exp_year', $SelectedValue = '')
        {
        
        	$ReturnString = "<select style='width:auto;' name='{$FieldName}'><option value=''>Select Year</option>";
        
        	for ($i = date("Y"); $i <= (date("Y")+10); $i++)
			{
				$ReturnString .= "<option value='{$i}'"; if($SelectedValue==$i){ $ReturnString .= " selected"; } $ReturnString .= ">{$i}</option>";
			}
			
			$ReturnString .= "</select>";
			
			return $ReturnString;
        
        }


	
	/* Authorize.net Integration */
	function do_charge($post_values)
	{
	    $post_url = "https://test.authorize.net/gateway/transact.dll";
	    $post_string = "";

	    foreach( $post_values as $key => $value )
	    {
		$post_string .= "$key=" . urlencode( $value ) . "&";
	    }

	    $post_string = rtrim($post_string, "& ");
	    $request = curl_init($post_url); // initiate curl object

	    curl_setopt($request, CURLOPT_HEADER, 0);
	    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
	    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
	    $post_response = curl_exec($request);
	    curl_close ($request);

	    $response_array = explode($post_values["x_delim_char"],$post_response);

	    if($response_array) return $response_array;
	    else return '';
	}

	/* messaging email function for emails & messages. */

	function m_omail($member_id,$template_name,$params)
	{
        /*-------------------------------
        Admin must be MEMBER #1
        -------------------------------*/
	    $CI =& get_instance();
		$CI->load->library('email');
		
		// Get System Email From Database

        $member = $CI->db->query("SELECT * FROM members WHERE id = {$member_id} LIMIT 1")->row_array();
		$getTemplate =  $CI->db->query("SELECT * FROM system_emails WHERE name = '{$template_name}' LIMIT 1");

       if($member_id == 1)
        $member['email'] =  $CI->site->settings['admin_email'];

		if($getTemplate->num_rows()==0) return false;
		else
		{
		
			$template = $getTemplate->row_array();
			
			// Parse system email template
			$subject = $template['subject'];
			$pre_content = html_entity_decode($template['content']);
			
			$content = "
			<div style='background:#b9b9b9;padding:20px;'>
				<div style='margin-bottom:10px;font-size:20px;font-weight:bold;font-family:Arial;'>Psychic-Contact.com</div>
				<div style='background:#FFF;padding:20px;'>{$pre_content}</div>
			</div>
			";
			
			foreach($params as $eColumn => $eValue)
			{
				$subject = str_replace("[{$eColumn}]",$eValue,$subject);
				$content = str_replace("[{$eColumn}]",$eValue,$content);
			}
			
			// Configure Email Options
			$config['mailtype'] = 'html';
			$CI->email->initialize($config);
		
			// Send Email
			$from[1] = $CI->config->item('email_from_email_address');
			$from[2] = $CI->config->item('email_from_name');
			
            //Log in message.
            $to = $member['email'];

            $insert['message'] = (isset($params['message']) ? $params['message'] : $content);
            $insert['subject'] = (isset($params['subject']) ? $params['subject'] : $subject);

            $insert['sender_id'] = (isset($params['sender_id']) ? $params['sender_id'] : null);
            $insert['ricipient_id'] = (isset($params['recipient_id']) ? $params['recipient_id'] : $member_id);
            $insert['type'] = $params['type'];
            $insert['datetime'] = date("Y-m-d G:i:s");
            $CI->db->insert('messages', $insert);

            //Send Email.
			$CI->email->from($from[1],$from[2] );
			$CI->email->to($to);
			$CI->email->subject($subject);
			$CI->email->message($content);
			$CI->email->send();
			 
			 //ADD IN CODE TO SEND MESSAGE.

            //--------------------------->
			
			return true;
		
		}

	}


        /* Mail Based on System Emails DB */
        function omail($to,$template_name,$exchange_vars_array=array(''),$from_id = null)
        {

            $CI =& get_instance();
            $CI->load->library('email');

            // Get System Email From Database
            $getTemplate =  $CI->db->query("SELECT * FROM system_emails WHERE name = '{$template_name}' LIMIT 1");

            if($getTemplate->num_rows()==0) return false;
            else
            {

                $template = $getTemplate->row_array();

                // Parse system email template
                $subject = $template['subject'];
                $pre_content = html_entity_decode($template['content']);

                $content = "
			<div style='background:#b9b9b9;padding:20px;'>
				<div style='margin-bottom:10px;font-size:20px;font-weight:bold;font-family:Arial;'>Psychic-Contact.com</div>
				<div style='background:#FFF;padding:20px;'>{$pre_content}</div>
			</div>
			";

                foreach($exchange_vars_array as $eColumn => $eValue)
                {
                    $subject = str_replace("[{$eColumn}]",$eValue,$subject);
                    $content = str_replace("[{$eColumn}]",$eValue,$content);
                }

                // Configure Email Options
                $config['mailtype'] = 'html';
                $CI->email->initialize($config);

                // Send Email
                $from[1] = $CI->config->item('email_from_email_address');
                $from[2] = $CI->config->item('email_from_name');

                // IF the TO is a number
                // Get the member and send the message to their local inbox
                // as well as their email address

                if(is_numeric($to))
                {

                    $member = $this->get_member($to);

                    $to = $member['email'];

                    $insert = array();
                    $insert['datetime'] = date("Y-m-d H:i:s");
                    $insert['sender_id'] = $from_id;
                    $insert['ricipient_id'] = $member['id'];
                    $insert['subject'] = $subject;
                    $insert['message'] = $content;

                }

                $CI->email->from($from[1],$from[2] );
                $CI->email->to($to);
                $CI->email->subject($subject);
                $CI->email->message($content);
                $CI->email->send();

                // echo "Debugger: ".$CI->email->print_debugger();

                return true;

            }

        }

	
	function csv_to_array($filename='', $delimiter=',')
		{
		    if(!file_exists($filename) || !is_readable($filename))
		        return FALSE;
		
		    $header = NULL;
		    $data = array();
		    if (($handle = fopen($filename, 'r')) !== FALSE)
		    {
		        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
		        {
		            if(!$header)
		                $header = $row;
		            else
		                $data[] = array_combine($header, $row);
		        }
		        fclose($handle);
		    }
		    return $data;
		} 

		function xml2array($contents, $get_attributes=1, $priority = 'tag')
		{ 
		    if(!$contents) return array(); 
		
		    if(!function_exists('xml_parser_create')) { 
		        //print "'xml_parser_create()' function not found!"; 
		        return array(); 
		    } 
		
		    //Get the XML parser of PHP - PHP must have this module for the parser to work 
		    $parser = xml_parser_create(''); 
		    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss 
		    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
		    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
		    xml_parse_into_struct($parser, trim($contents), $xml_values); 
		    xml_parser_free($parser); 
		
		    if(!$xml_values) return;//Hmm... 
		
		    //Initializations 
		    $xml_array = array(); 
		    $parents = array(); 
		    $opened_tags = array(); 
		    $arr = array(); 
		
		    $current = &$xml_array; //Refference 
		
		    //Go through the tags. 
		    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
		    foreach($xml_values as $data) { 
		        unset($attributes,$value);//Remove existing values, or there will be trouble 
		
		        //This command will extract these variables into the foreach scope 
		        // tag(string), type(string), level(int), attributes(array). 
		        extract($data);//We could use the array by itself, but this cooler. 
		
		        $result = array(); 
		        $attributes_data = array(); 
		         
		        if(isset($value)) { 
		            if($priority == 'tag') $result = $value; 
		            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
		        } 
		
		        //Set the attributes too. 
		        if(isset($attributes) and $get_attributes) { 
		            foreach($attributes as $attr => $val) { 
		                if($priority == 'tag') $attributes_data[$attr] = $val; 
		                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
		            } 
		        } 
		
		        //See tag status and do the needed. 
		        if($type == "open") {//The starting of the tag '<tag>' 
		            $parent[$level-1] = &$current; 
		            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
		                $current[$tag] = $result; 
		                if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
		                $repeated_tag_index[$tag.'_'.$level] = 1; 
		
		                $current = &$current[$tag]; 
		
		            } else { //There was another element with the same tag name 
		
		                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array 
		                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
		                    $repeated_tag_index[$tag.'_'.$level]++; 
		                } else {//This section will make the value an array if multiple tags with the same name appear together
		                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
		                    $repeated_tag_index[$tag.'_'.$level] = 2; 
		                     
		                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
		                        $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
		                        unset($current[$tag.'_attr']); 
		                    } 
		
		                } 
		                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
		                $current = &$current[$tag][$last_item_index]; 
		            } 
		
		        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
		            //See if the key is already taken. 
		            if(!isset($current[$tag])) { //New Key 
		                $current[$tag] = $result; 
		                $repeated_tag_index[$tag.'_'.$level] = 1; 
		                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 
		
		            } else { //If taken, put all things inside a list(array) 
		                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 
		
		                    // ...push the new element into that array. 
		                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
		                     
		                    if($priority == 'tag' and $get_attributes and $attributes_data) { 
		                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
		                    } 
		                    $repeated_tag_index[$tag.'_'.$level]++; 
		
		                } else { //If it is not an array... 
		                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
		                    $repeated_tag_index[$tag.'_'.$level] = 1; 
		                    if($priority == 'tag' and $get_attributes) { 
		                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
		                             
		                            $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
		                            unset($current[$tag.'_attr']); 
		                        } 
		                         
		                        if($attributes_data) { 
		                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
		                        } 
		                    } 
		                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
		                } 
		            } 
		
		        } elseif($type == 'close') { //End of tag '</tag>' 
		            $current = &$parent[$level-1]; 
		        } 
		    } 
		     
		    return($xml_array); 
		}

    }

?>
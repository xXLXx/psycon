<?php
/*
* Ajax functions used by in-page javascipts
*/

include "psy_config.php";
include "psy_functions.php";

/* MAIN */

$link = mysql_connect("$db_host", "$db_user", "$db_pass") or die("Could not connect");
mysql_select_db("$db_name") or die("Could not select database");

/*
MAIN
*/

switch($_REQUEST['mode'])
{
	case 'showFeaturedReader':
	showFeaturedReader();
	break;
	case 'showReaderList':
	showReaderList($_REQUEST['count']);
	break;
	case 'getReaderStatus':
	getReaderStatus($_REQUEST['reader_id']);
	break;
	case 'getMultipleReaderStatuses':
	getMultipleReaderStatuses();
	break;
	case 'checkReaderMyAccountStatus':
	checkReaderMyAccountStatus($_REQUEST['reader_id']);
	break;
}


/*
FUNCTIONS
*/


function getMultipleReaderStatuses()
{
	$now = date("Y-m-d H:i:s");
	
	$client_id = $_GET['client_id'];

	$output = "";
	$holder_array = array();
	
	$is_full_ban =false;
	$personal_ban_list = array();
	
	// check if client is banned by any reader
	if(!empty($client_id)) {
		settype($client_id, 'integer');
		$query = "SELECT id, reader_id, type FROM member_bans where member_id=$client_id";
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result))
		{
			if ($row['type'] == "full") {
				$is_full_ban = true;
				break;
			} else if ($row['type'] == "personal") {
				array_push($personal_ban_list, $row['reader_id']);
			}
		}
	}
			
	$query = "SELECT p.status,m.username,m.id as member_id, p.last_chat_request, p.last_pending_time, p.break_time, p.manual_break_time FROM members as m JOIN profiles as p WHERE p.id = m.id AND p.active=1";
	//debug("query",$query);
	
	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result))
	{
	
		$t_array = array();
		$t_array['status'] = $row['status'];
		$t_array['member_id'] = $row['member_id'];
		$t_array['username'] = $row['username'];
		$original_status = $row['status'];
		
		if(in_array($row['member_id'], $personal_ban_list) || $is_full_ban) {
			$t_array['status'] = "blocked";
		} 
		
		if ($t_array['status'] == "online") {
			if (strtotime($row['last_chat_request']) < strtotime($row['last_pending_time'])) {
				// then just consider the pending time. 
				if (strtotime($now) - strtotime($row['last_pending_time']) < CHAT_MAX_PENDING) {
					$t_array['status'] = "busy";
				} 
			} else {
				
				if (strtotime($now) - strtotime($row['last_chat_request']) < CHAT_MAX_WAIT) {
					$t_array['status'] = "busy";
				}
			}
			
			if (strtotime($now) < strtotime($row['break_time'])) {
				$t_array['status'] = "break";
				
			} 
			$t_array['manual_break_time'] = strtotime($row['manual_break_time']);
		} else if ($t_array['status'] == 'break') {
			if ($row['manual_break_time'] !== false) {
				if (strtotime($row['manual_break_time']) < strtotime($now)) {
					// update 
					$udpate_query = "UPDATE profiles SET status='offline', manual_break_time='' WHERE id=" . $row['member_id'];
					mysql_query($udpate_query);
					$t_array['status'] = "offline";
				}
			}
		}
		
		if ($original_status != $t_array['status']) {
			error_log("No real error.. Just reader " . $t_array['username'] . " status change from $original_status to " . $t_array['status']);
			
		}
		
		array_push($holder_array,$t_array);
	
	}//end while
	$output = json_encode($holder_array);
	echo $output;
		
}// end


function getReaderStatus($reader_id = null)
{
	global $_READER_URL, $_READER_IMAGE_URL;
			
	$query = "SELECT p.status FROM members as m JOIN profiles as p WHERE p.id = m.id AND p.active=1";
	
	//debug("query",$query);
	
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	
	$output = "<img src=\"/media/images/<!--READER_STATUS-->.jpg\" style=\"width:102px;border:none;margin-top:0px;margin-left:5px;\">";

	$output = str_replace("<!--READER_STATUS-->",$row['status'],$output);

	echo $output;
		
}// end

function checkReaderMyAccountStatus($reader_id = null) {
	$output = array();
	$now = date("Y-m-d H:i:s");
	
	$query = "SELECT status, manual_break_time, last_chat_request, last_pending_time, break_time FROM profiles WHERE id=".intval($reader_id);
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$output['original_status'] = $row['status'];
	$output['status'] = $row['status'];
	$output['logoff'] = false;
	$original_status = $row['status'];
	
	if ($output['status'] == "online") {
		if (strtotime($row['last_chat_request']) < strtotime($row['last_pending_time'])) {
			// then just consider the pending time. 
			if (strtotime($now) - strtotime($row['last_pending_time']) < CHAT_MAX_PENDING) {
				$output['status'] = "busy";
			} 
		} else {
			
			if (strtotime($now) - strtotime($row['last_chat_request']) < CHAT_MAX_WAIT) {
				$output['status'] = "busy";
			}
		}
		
		if (strtotime($now) < strtotime($row['break_time'])) {
			$output['status'] = "break";
			
		} 
	} 
	
	if ($row['status'] == 'break' && $row['manual_break_time'] !== false) {
		if (strtotime($row['manual_break_time']) < strtotime($now)) {
			// update 
			$udpate_query = "UPDATE profiles SET status='offline', manual_break_time='' WHERE id=" . $row['member_id'];
			mysql_query($udpate_query);
			$output['status'] = "offline";
			$output['logoff'] = true;
		}
	}
	if ($original_status != $output['status']) {
		error_log("No real error.. Just reader " . $reader_id . " status change from $original_status to " . $output['status']);
		
	}
	echo json_encode($output);
}


function showReaderList($numFeatured = null)
{
	global $_READER_URL, $_READER_IMAGE_URL;

	if (!$numFeatured)
		$numFeatured = 1;
			
	$output = "";
	$tpl_readers_online_table = readTemplate("readers_online_table");
	$tpl_readers_online_row = readTemplate("readers_online_row");

	$query = "SELECT m.username,m.profile_image,p.status,p.area_of_expertise,(SELECT COUNT(*) FROM testimonials WHERE reader_id = m.id) as testCount FROM members as m JOIN profiles as p WHERE p.id = m.id AND p.status != \"offline\" AND p.featured = 0 AND p.active=1 ORDER BY p.status LIMIT 0,$numFeatured";
	
	//debug("query",$query);
	
	$result = mysql_query($query) or die("Online reader content unavailable");
	while ($row = mysql_fetch_array($result))
	{
		$t_row = $tpl_readers_online_row;
		
		$t_row = str_replace("<!--READER_URL-->",$_READER_URL."/".$row['username'],$t_row);
		$t_row = str_replace("<!--READER_IMAGE-->",$_READER_IMAGE_URL."/".$row['profile_image'],$t_row);
		
		// need to show image name
		$t_row = str_replace("<!--READER_STATUS-->",$row['status'],$t_row);
		$t_row = str_replace("<!--READER_NAME-->",$row['username'],$t_row);
		$t_row = str_replace("<!--READER_EXPERTISE-->",$row['area_of_expertise'],$t_row);

		// check for testimonials
		if ($row['testCount'])
		{
		
			$test_str = "<a class=\"testimonials\" href=\"#\" style=\"text-decoration: underline;\"><!--TESTIMONIAL_COUNT--> Client <!--TESTIMONIAL_WORD--></a>";
		
			$test_str = str_replace("<!--TESTIMONIAL_COUNT-->",$row['testCount'],$test_str);
			$test_str = str_replace("<!--TESTIMONIAL_WORD-->", ($row['testCount'] == 1 ? "Testimonial" : " Testimonials"),$test_str);

			$t_row = str_replace("<!--TESTIMONIALS-->",$test_str,$t_row);
			
		}
		
		$output .= $t_row;

	}// end while
	
	$output = str_replace("<!--ROWS-->",$output,$tpl_readers_online_table);

	echo $output;
		
}// end

	
function showFeaturedReader()
{
	global $_READER_URL, $_READER_IMAGE_URL;
			
	$output = "";
	$tpl_featured_readers = readTemplate("featured_reader");

	$query = "SELECT m.username,m.profile_image,p.status,p.area_of_expertise,(SELECT COUNT(*) FROM testimonials WHERE reader_id = m.id) as testCount FROM members as m JOIN profiles as p WHERE p.id = m.id AND p.active=1 AND p.featured = 1 ORDER BY m.username";
	
	//debug("query",$query);
	
	//$result = mysql_query($query) or die("Featured reader content unavailable");
	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result))
	{
		$t_row = $tpl_featured_readers;
		
		$t_row = str_replace("<!--READER_URL-->",$_READER_URL."/".$row['username'],$t_row);
		$t_row = str_replace("<!--READER_IMAGE-->",$_READER_IMAGE_URL."/".$row['profile_image'],$t_row);
		
		// need to show image name
		$t_row = str_replace("<!--READER_STATUS-->",$row['status'],$t_row);
		$t_row = str_replace("<!--READER_NAME-->",$row['username'],$t_row);
		$t_row = str_replace("<!--READER_EXPERTISE-->",$row['area_of_expertise'],$t_row);

		// check for testimonials
		if ($row['testCount'])
		{
		
			$test_str = "<a class=\"testimonials\" href=\"#\" style=\"text-decoration: underline;\"><!--TESTIMONIAL_COUNT--> Client <!--TESTIMONIAL_WORD--></a>";
		
			$test_str = str_replace("<!--TESTIMONIAL_COUNT-->",$row['testCount'],$test_str);
			$test_str = str_replace("<!--TESTIMONIAL_WORD-->", ($row['testCount'] == 1 ? "Testimonial" : " Testimonials"),$test_str);

			$t_row = str_replace("<!--TESTIMONIALS-->",$test_str,$t_row);
			
		}
		
		$output .= $t_row;

	}// end while
	
	echo $output;
		
}// end



?>
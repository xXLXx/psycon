<?
	
	class readers extends CI_Model
	{
	
		function getFeaturedReaders($total = 10)
		{
		
			$oneMinuteAgo = date("Y-m-d H:i:s", strtotime("-1 minute"));
		
			return $this->db->query
			("
			
				SELECT
					members.*,
					profiles.title,

					CASE WHEN (profiles.status IS NULL OR profiles.status = 'online') AND profiles.last_activity < '{$oneMinuteAgo}'
						THEN 1
						ELSE 0
					END AS online_status,

					CASE WHEN members.profile_image IS NULL
						THEN '/media/images/no_profile_image.jpg'
						ELSE CONCAT('/media/assets/', members.profile_image)
					END AS 'profile'

				FROM
					members

				JOIN profiles ON profiles.id = members.id

				WHERE
					profiles.active = 1 AND
					profiles.featured = 1

				ORDER BY
					online_status DESC,
					(SELECT SUM(reviews.rating)/COUNT(reviews.id) FROM reviews WHERE reviews.profile_id = profiles.id)
					
				LIMIT {$total}
			
			")->result_array();
		
		}

		// This function is not working because online_readers do not exist
        function getOnlineReaders($total = 10)
        {
            return $this->db->query
                ("

				select *
				FROM   online_readers
				LIMIT {$total}

			")->result_array();

        }
	
		// Get all readers
		// Develop an algorithm to order them
		// ORDER BY: online first & rating second
		// We need to incorporate a pagination mechanism
		
		function getAll()
		{
			$now = date("Y-m-d H:i:s");

			$is_full_ban = false;
			$personal_ban_list = array();
			
			// check if is logged in

			if ($this->session->userdata('member_logged')) {
				$query = $this->db->query("SELECT id, reader_id, type FROM member_bans where member_id=" . $this->session->userdata('member_logged'));
				if($query->num_rows() !== 0)
				{
					$ban_results = $query->result_array();
					foreach ($ban_results as $ban_result) {
						if ($ban_result['type'] == "full") {
							$is_full_ban = true;
							break;
						} else if ($ban_result['type'] == "personal") {
							array_push($personal_ban_list, $ban_result['reader_id']);
						} 
					}
				}
			}
			
			$query = $this->db->query
			("
			
				SELECT
					profiles.*,
					members.username,
					members.id as member_id,
					members.paypal_email,
					CASE WHEN profile_image IS NULL 
						THEN '/media/images/no_profile_image.jpg'
						ELSE CONCAT('/media/assets/', profile_image)
					END AS 'profile'
					
				FROM
					members
					
				JOIN profiles ON profiles.id = members.id
					
				WHERE
					profiles.active = 1
					
			");
		
			if ($is_full_ban == true || count($personal_ban_list) > 0) {
				$all_results = $query->result_array();
				foreach($all_results as &$all_result) {
					if(in_array($all_result['member_id'], $personal_ban_list) || $is_full_ban) {
						$all_result['status'] = "blocked";
					} 
					
					if ($all_result['status'] == "online") {
						if (strtotime($all_result['last_chat_request']) < strtotime($all_result['last_pending_time'])) {
							// then just consider the pending time. 
							if (strtotime($now) - strtotime($all_result['last_pending_time']) < CHAT_MAX_PENDING) {
								$all_result['status'] = "busy";
							} 
						} else {
							if (strtotime($now) - strtotime($all_result['last_chat_request']) < CHAT_MAX_WAIT) {
								$all_result['status'] = "busy";
							}
						}
						
						if (strtotime($now) < strtotime($all_result['break_time'])) {
							$all_result['status'] = "break";
						}
					}
				}
				return $all_results;
			} else {
				$all_results = $query->result_array();
				foreach($all_results as &$all_result) {
					if ($all_result['status'] == "online") {
						if (strtotime($all_result['last_chat_request']) < strtotime($all_result['last_pending_time'])) {
							// then just consider the pending time. 
							if (strtotime($now) - strtotime($all_result['last_pending_time']) < CHAT_MAX_PENDING) {
								$all_result['status'] = "busy";
							} 
						} else {
							if (strtotime($now) - strtotime($all_result['last_chat_request']) < CHAT_MAX_WAIT) {
								$all_result['status'] = "busy";
							}
						}
						
						if (strtotime($now) < strtotime($all_result['break_time'])) {
							$all_result['status'] = "break";
						}
						
					}
				}
				return $all_results;
			}
		}
	
		// Perform a comprehensive search over readers
		// We need to incorporate a pagination mechanism
		
		function search($category_id = null, $search_query = null)
		{
			$now = date("Y-m-d H:i:s");
			
			$is_full_ban = false;
			$personal_ban_list = array();
			
			// check if is logged in

			if ($this->session->userdata('member_logged')) {
				$query = $this->db->query("SELECT id, reader_id, type FROM member_bans where member_id=" . $this->session->userdata('member_logged'));
				if($query->num_rows() !== 0)
				{
					$ban_results = $query->result_array();
					foreach ($ban_results as $ban_result) {
						if ($ban_result['type'] == "full") {
							$is_full_ban = true;
							break;
						} else if ($ban_result['type'] == "personal") {
							array_push($personal_ban_list, $ban_result['reader_id']);
						} 
					}
				}
			}
			
			// Search category AND query
			if($category_id && $search_query)
			{
			
				$query = $this->db->query
				("
				
					SELECT
						profiles.*,
						members.id as member_id, 
						members.username,
						CASE WHEN profile_image IS NULL 
							THEN '/media/images/no_profile_image.jpg'
							ELSE CONCAT('/media/assets/', profile_image)
						END AS 'profile'

						
					FROM
						members
						
					JOIN profiles ON profiles.id = members.id
						
					WHERE
						profiles.active = 1 AND
						(
							members.first_name LIKE '%{$search_query}%' OR
							members.last_name LIKE '%{$search_query}%' OR
							members.username LIKE '%{$search_query}%' OR
							members.id LIKE '%{$search_query}%' OR
							members.email LIKE '%{$search_query}%'
						) AND
						
						EXISTS(SELECT * FROM profile_categories WHERE profile_categories.profile_id = profiles.id AND profile_categories.category_id = {$category_id} )
								
				");

			}
			
			// Search Just Category
			elseif($category_id && !$search_query)
			{
			
				$query = $this->db->query
				("
				
					SELECT
						profiles.*,
						members.id as member_id, 
						members.username,
						CASE WHEN profile_image IS NULL 
							THEN '/media/images/no_profile_image.jpg'
							ELSE CONCAT('/media/assets/', profile_image)
						END AS 'profile'
						
					FROM
						members
						
					JOIN profiles ON profiles.id = members.id
						
					WHERE
					
						profiles.active = 1 AND
						EXISTS (SELECT * FROM profile_categories WHERE profile_categories.profile_id = profiles.id AND profile_categories.category_id = {$category_id} )
								
				");
			
			}
			
			// Search just query
			else
			{
		
				$query = $this->db->query
				("
				
					SELECT
						profiles.*,
						members.id as member_id, 
						members.username,
						CASE WHEN profile_image IS NULL 
							THEN '/media/images/no_profile_image.jpg'
							ELSE CONCAT('/media/assets/', profile_image)
						END AS 'profile'
						
					FROM
						members
						
					JOIN profiles ON profiles.id = members.id
						
					WHERE
						profiles.active = 1 AND
						(
							members.first_name LIKE (\"%{$search_query}%\") OR
							members.last_name LIKE (\"%{$search_query}%\") OR
							members.username LIKE (\"%{$search_query}%\") OR
							members.id LIKE (\"%{$search_query}%\") OR
							members.email LIKE (\"%{$search_query}%\")
						)
								
				");
		
			}
			
			if($query->num_rows() == 0)
			{
			
				$array = array();
				$array['error'] = '1';
				$array['message'] = "There were no readers found matching your search criteria. Please try again.";
			
			}
			else
			{
			
				$array = array();
				$array['error'] = '0';
				
				if ($is_full_ban == true || count($personal_ban_list) > 0) {
					$all_results = $query->result_array();
					foreach($all_results as &$all_result) {
						if(in_array($all_result['member_id'], $personal_ban_list) || $is_full_ban) {
							$all_result['status'] = "blocked";
						} 
						
						if ($all_result['status'] == "online") {
							if (strtotime($all_result['last_chat_request']) < strtotime($all_result['last_pending_time'])) {
								// then just consider the pending time. 
								if (strtotime($now) - strtotime($all_result['last_pending_time']) < CHAT_MAX_PENDING) {
									$all_result['status'] = "busy";
								} 
							} else {
								if (strtotime($now) - strtotime($all_result['last_chat_request']) < CHAT_MAX_WAIT) {
									$all_result['status'] = "busy";
								}
							}
							
							if (strtotime($now) < strtotime($all_result['break_time'])) {
								$all_result['status'] = "break";
							}
						}
					}
					$array['readers'] =  $all_results;
				} else {
					$all_results = $query->result_array();
					foreach($all_results as &$all_result) {
						if ($all_result['status'] == "online") {
							if (strtotime($all_result['last_chat_request']) < strtotime($all_result['last_pending_time'])) {
								// then just consider the pending time. 
								if (strtotime($now) - strtotime($all_result['last_pending_time']) < CHAT_MAX_PENDING) {
									$all_result['status'] = "busy";
								} 
							} else {
								if (strtotime($now) - strtotime($all_result['last_chat_request']) < CHAT_MAX_WAIT) {
									$all_result['status'] = "busy";
								}
							} 

							if (strtotime($now) < strtotime($all_result['break_time'])) {
								$all_result['status'] = "break";
							}
						}
					}
					$array['readers'] =  $all_results;
				}
				
			}
			
			return $array;
			
		}
	
	}
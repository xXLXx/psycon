<?

	class cookielogin
	{
	
		function index()
		{
		
		
			$ci =& get_instance();
		
			if(!$ci->session->userdata('member_logged'))
			{
			
				// Check for cookies and auto-log the user in
				$cookie = get_cookie('site_login');
				
				$id = $ci->encrypt->decode($cookie);
				
				if(is_numeric($id))
				{
				
					$ci->session->set_userdata('member_logged', $id);
				
				}
			
			}
		
		}
	
	}

?>
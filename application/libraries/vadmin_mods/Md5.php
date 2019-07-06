<?

	class md5
	{
	
		function config($name, $value, $params = null)
	    {
	    
	        $this->ci =& get_instance();
	        
	        $this->name = $name;
			$this->value = $value;
			$this->size = (isset($params[1]) ? $params[1] : '50');
	        
	    }
		
		function field_view()
		{
		
			return "<input type='password' name='{$this->name}' class='tb' size='{$this->size}'><div class='caption' style='padding:5px 0 0;'>** Leave Blank To Keep Current Password ** Encrypted using the MD5 Algorithm **</div>";
		
		}
		
		function display_view()
		{
		
			return "** Password Hidden **";
		
		}
		
		function process_form()
		{
		
			if(!trim( $this->value )) return '[%skip%]';
			else return md5($this->value);
		
		}
	
	}

?>
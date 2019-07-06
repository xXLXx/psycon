<?

	class date_time
	{
	
		function config($name, $value, $params = null)
	    {
	    
	        $this->ci =& get_instance();
	        
	        $this->name = $name;
			$this->value = $value;
	        
	    }
		
		function field_view()
		{
		
			if($this->value=='0000-00-00 00:00:00' || !$this->value) $value = "";
			else $value = date('m/d/Y @ h:i A', strtotime($this->value));
		
			return "<input type='text' name='{$this->name}' value='{$value}' class='datetime tb'>";
		
		}
		
		function display_view()
		{
		
			$value = str_replace('@', '', $this->value);
		
			if($this->value==null || $this->value=='0000-00-00 00:00:00') return "";
			else return date('m/d/Y @ h:i A', strtotime($value));
		
		}
		
		function process_form()
		{
		
			$value = str_replace('@', '', $this->value);
			
			if($this->value==null || $this->value=='') return "";
			else return date('Y-m-d H:i:s', strtotime($value));
			
		}
	
	}

?>
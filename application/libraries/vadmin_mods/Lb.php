<?

	class lb
	{
	
		function config($name, $value, $params = null)
	    {
	    
	        $this->ci =& get_instance();
	        
	        $this->name = $name;
			$this->value = $value;
	        
	    }
		
		function field_view()
		{
		
			return "<input type='hidden' name='{$this->name}' value='{$this->value}' class='tb'>{$this->value}";
		
		}
		
		function display_view()
		{
		
			return $this->value;
		
		}
		
		function process_form()
		{
			
			return $this->value;
			
		}
	
	}

?>
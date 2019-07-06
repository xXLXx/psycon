<?

	class tb
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
		
			return "<input type='text' name='{$this->name}' value='{$this->value}' class='tb' size='{$this->size}'>";
		
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
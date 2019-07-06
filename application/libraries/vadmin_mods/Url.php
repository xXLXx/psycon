<?

	class url
	{
	
		function config($name, $value, $params = null)
	    {
	    
	        $this->ci =& get_instance();
	        
	        $this->name = $name;
			$this->value = $value;
			$this->size = (isset($params[1]) ? $params[1] : '25');
	        
	    }
		
		function field_view()
		{
		
			$base_url = $this->ci->config->item('base_url');
			$return = "{$base_url} <input type='text' name='{$this->name}' value='{$this->value}' class='tb' size='{$this->size}'>";
			
			return $return;
		
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
<?

	class rd
	{
	
		function config($name, $value, $params = null)
	    {
	    
	        $this->ci =& get_instance();
	        
	        $this->name = $name;
			$this->value = $value;
			
			// Count total params after 0
			$this->param_array = $params;
	        
	    }
		
		function field_view()
		{
		
			$return = null;
			
			foreach($this->param_array as $i=>$pa)
			{
			
				if(trim($pa) && $i>0) $return .= "<input type='radio' name='{$this->name}' value='{$pa}'".($this->value==$pa ? " checked" : "")."> {$pa} <br />";
			
			}
		
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
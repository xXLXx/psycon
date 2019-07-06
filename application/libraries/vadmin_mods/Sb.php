<?

	class sb
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
		
			$return = "<select name='{$this->name}'>";
			
			foreach($this->param_array as $i=>$pa)
			{
			
				if(trim($pa) && $i>0) $return .= "<option value='{$pa}'".($this->value==$pa ? " selected" : "").">{$pa}</option>";
			
			}
			
			$return .= "</select>";
		
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
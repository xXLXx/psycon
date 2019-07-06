<?

	class ta
	{
	
		function config($name, $value, $params = null)
	    {
	    
	        $this->ci =& get_instance();
	        
	        $this->name = $name;
			$this->value = $value;
			$this->width = (isset($params[1]) ? $params[1] : '95%');
			$this->height = (isset($params[2]) ? $params[2] : '250px');
	        
	    }
		
		function field_view()
		{
		
			return "<textarea type='text' name='{$this->name}' class='tb' style='width:{$this->width};height:{$this->height};'>{$this->value}</textarea>";
		
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
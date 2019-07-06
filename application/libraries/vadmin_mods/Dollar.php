<?

	class dollar
	{
	
		function config($name, $value, $params = null)
	    {
	    
	        $this->ci =& get_instance();
	        
	        $this->name = $name;
			$this->value = $value;
			$this->size = (isset($params[1]) ? $params[1] : '5');
	        
	    }
		
		function field_view()
		{
		
			return "$ <input type='text' name='{$this->name}' value='{$this->value}' class='tb' size='{$this->size}'>";
		
		}
		
		function display_view()
		{
		
			if($this->value) return "$".number_format($this->value, 2);
			else return "0";
		
		}
		
		function process_form()
		{
			
			return $this->value;
			
		}
	
	}

?>
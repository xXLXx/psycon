<?

	class bool
	{
	
		function config($name, $value, $params = null)
	    {
	    
	        $this->ci =& get_instance();
	        
	        $this->name = $name;
			$this->value = $value;
			$this->yes_value = (isset($params[1]) ? $params[1] : 'Yes');
			$this->no_value = (isset($params[2]) ? $params[2] : 'No');
	        
	    }
		
		function field_view()
		{
		
			$returnString = "

				<input type='radio' name='{$this->name}' value='1' class='tb' ".($this->value ? " checked" : "")."> {$this->yes_value} &nbsp;
				<input type='radio' name='{$this->name}' value='0' class='tb' ".(!$this->value ? " checked" : "")."> {$this->no_value}
			
			";
		
			return $returnString;
		
		}
		
		function display_view()
		{
		
			if($this->value) return ($this->value ? $this->yes_value : $this->no_value);
			else return "";
		
		}
		
		function process_form()
		{
			
			return $this->value;
			
		}
	
	}

?>
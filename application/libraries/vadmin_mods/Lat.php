<?

	class lat
	{
	
		function config($name, $value, $params = null)
	    {
	    
	        $this->ci =& get_instance();
	        
	        $this->FieldName = $name;
			$this->FieldData = $value;
			
			$this->AddressField = $params['1'];
			$this->CityField = $params['2'];
			$this->StateField = $params['3'];
			$this->ZipField = $params['4'];
			$this->CountryField = (isset($params['5']) ? $params['5'] : false);
			
			if(!$this->FieldData) $this->FieldData = "N/A";
			if(!$this->CountryField) $this->CountryField = "USA";

	        
	    }
		
		function field_view()
		{
		
			return "{$this->FieldData}<input type='hidden' name='{$this->FieldName}' value='{$this->FieldData}'>";
		
		}
		
		function display_view()
		{
		
			return $this->FieldData;
		
		}
		
		function process_form()
		{
			
			$CI =& get_instance();
		    $CI->load->library('yahoo');
	
		    $AddressValue = $_POST[$this->AddressField];
		    $CityValue = $_POST[$this->CityField];
		    $StateValue = $_POST[$this->StateField];
		    $ZipValue = $_POST[$this->ZipField];
	
		    $AddressCoordinates = $CI->yahoo->address_plotter($AddressValue,$CityValue,$StateValue,$ZipValue);
		    
		    return $AddressCoordinates['Latitude'];
			
		}
	
	}

?>
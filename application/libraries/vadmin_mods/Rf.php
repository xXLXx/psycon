<?

	class rf
	{
	
		function __construct()
		{
		
			$this->ci =& get_instance();
		
		}
	
		function config($name, $value, $params = null)
		{
		
			// Define RF Configuration
			$this->name = $name;
			$this->value = $value;
			
			$this->table = $params[1];
			$this->field = $params[2]; // seperate multiple fields with space
			$this->order = (isset($params[3]) ? $params[3] : 'id');
			$this->output = (isset($params[4]) ? $params[4] : 'select'); // select, radio, checkbox
			$this->size = (isset($params[5]) ? $params[5] : '50'); // Only applies to autosuggest
			
			// DEFAULTS
			$this->fieldArray = explode(" ", $this->field);
			
			preg_match_all("/\[.*?\]/", $this->value, $Matches);
			$matchesArray = $Matches[0];
			$finalArray = array();
			
			foreach($matchesArray as $m)
			{
				if(trim( $m )) $finalArray[] = substr($m,1,-1);
			}
			
			$this->selectedArray = $finalArray;
		
		}
		
		function field_view()
		{
		
			$return = null;
			$tableFields = implode(",", $this->fieldArray);
			$getOptions = $this->ci->db->query("SELECT id,{$tableFields} FROM {$this->table} ORDER BY `{$this->order}` ");
		
			switch($this->output)
			{
			
				case "autosuggest":
				
					$objectString = "
					<script>
					
						$(document).ready(function()
						{
					
							var {$this->name}tags = [";
						
							foreach($getOptions->result_array() as $i=>$r)
							{
							
								$optionString = null;
								foreach($this->fieldArray as $f){ $optionString .= "{$r[$f]} "; }
							
								$objectString .= "{
									id : '{$r['id']}',
									label : '{$optionString}',
									value : '{$r['id']}',
								}";
								
								if($getOptions->num_rows()!=($i+1)) $objectString .= ",\n";
							
							}
							
							$objectString .= "];
							
							$('#{$this->value}as').autocomplete
							({
								source : {$this->name}tags,
								select : function(event, ui)
								{
								
									var selectedId = ui.item.id;
									var selectedLabel = ui.item.label;
									
									$('#{$this->value}as').val(selectedLabel);
									$('#{$this->name}hd').val(selectedId);
									
									return false;
								
								}
							});
							
							$('#{$this->value}as').blur(function()
							{
							
								if(this.value=='')
								{
									$('#{$this->value}as').val('');
									$('#{$this->name}hd').val('');
								}
							
							});
						
						});
						
					</script>";
					$return .= "{$objectString}";
					
					// Get Default Value
					if($this->value)
					{
					
						$getField = $this->ci->db->query("SELECT * FROM {$this->table} WHERE id = {$this->value} LIMIT 1");
						$row = $getField->row_array();
						
						$optionString = null;
						foreach($this->fieldArray as $f){ $optionString .= "{$row[$f]} "; }
						
						$defaultValue = $optionString;
					
					}
					else
					{
					
						$defaultValue = '';
					
					}
					
					$return .= "
					<input type='text' id='{$this->value}as' value='{$defaultValue}' class='tb' size='{$this->size}'>
					<input type='hidden' id='{$this->name}hd' name='{$this->name}' value='{$this->value}'>
					";
				
				break;
			
				case "radio":
					
					foreach($getOptions->result_array() as $r)
					{
					
						$optionString = null;
						foreach($this->fieldArray as $f){ $optionString .= "{$r[$f]} "; }
						
						$return .= "<input type='radio' name='{$this->name}' value='{$r['id']}'".($this->value == $r['id'] ? " checked" : "")."> {$optionString} <br />";
					
					}
				
				break;
				
				case "checkbox":
					
					foreach($getOptions->result_array() as $r)
					{
					
						$optionString = null;
						foreach($this->fieldArray as $f){ $optionString .= "{$r[$f]} "; }
						
						$return .= "<input type='checkbox' name='{$this->name}[]' value='{$r['id']}'".(in_array($r['id'], $this->selectedArray) ? " checked" : "")."> {$optionString} <br />";
					
					}
				
				break;
			
				default:
				
					$return = "<select name='{$this->name}'>";
					
						foreach($getOptions->result_array() as $r)
						{
						
							$optionString = null;
							foreach($this->fieldArray as $f){ $optionString .= "{$r[$f]} "; }
							
							$return .= "<option value='{$r['id']}'".($this->value == $r['id'] ? " selected" : "").">{$optionString}</option>";
						
						}
					
					$return .= "</select>";
				
				break;
			
			}
			
			return $return;
		
		}
		
		function display_view()
		{
		
			if($this->value)
			{
			
				$getField = $this->ci->db->query("SELECT * FROM {$this->table} WHERE id = {$this->value} LIMIT 1");
				$row = $getField->row_array();
				
				$optionString = null;
				foreach($this->fieldArray as $f){ $optionString .= "{$row[$f]} "; }
				
				return $optionString;
			
			}
			else
			{
			
				return "";
			
			}
		
		}
		
		function process_form()
		{
			
			switch($this->output)
			{
			
				// Multiple Results
				case "checkbox";
				
					$returnString = null;
				
					foreach($this->value as $v)
					{
					
						$returnString .= "[{$v}]";
					
					}
					
					return $returnString;
				
				break;
				
				// Single Result
				default:
				
					return $this->value;
				
				break;
			
			}
			
		}
	
	}

?>
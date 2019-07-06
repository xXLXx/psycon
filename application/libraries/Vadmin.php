<?

	class vadmin
	{
	
		function vadmin()
		{
		
			$this->ci =& get_instance();
		
		}
		
		function get_field_spec($tableName = null, $type = null)
		{
		
			$getSpec = $this->ci->db->query("SELECT * FROM vadmin_specs WHERE `table` = '{$tableName}' AND `type` = '{$type}' LIMIT 1");
			
			if($getSpec->num_rows()==0) return false;
			else return $getSpec->row_array();
			
		}
		
		function get_table_specs($tableName = null)
		{
		
			$getSpec = $this->ci->db->query("SELECT * FROM vadmin_specs WHERE `table` = \"{$tableName}\" ");
			
			if($getSpec->num_rows()==0) return false;
			else 
			{
			
				foreach($getSpec->result_array() as $ra)
				{
				
					if(!$ra['field'])
					{
						$returnArray[$ra['type']] = $ra;
					}
					else
					{
						$returnArray[$ra['type']][$ra['field']] = $ra;
					}
				
				}
				
				return $returnArray;
			
			}
			
		}
	
		function get_admin($id=null, $user=null, $pass=null)
		{
		
			if($id)
			{
			
				$checkDb = $this->ci->db->query("SELECT * FROM vadmin_users WHERE id = '{$id}' ");
				
				if($checkDb->num_rows()==0)
				{
				
					return false;
				
				}
				else
				{
				
					return $checkDb->row_array();
				
				}
			
			}
			else
			{
			
				$superUser = $this->ci->config->item('superadmin_username');
				$superPass = $this->ci->config->item('superadmin_password');
				
				if( $user == $superUser && $pass == $superPass )
				{
					
					return true;
					
				}
				else
				{
				
					$checkDb = $this->ci->db->query("SELECT * FROM vadmin_users WHERE username = '{$user}' AND password = '".md5($pass)."' LIMIT 1");
					
					if($checkDb->num_rows()==0)
					{
					
						return false;
					
					}
					else
					{
					
						return $checkDb->row_array();
					
					}
				
				}
			
			}
			
		} ///
	
	}

?>
<?
	
	class site extends CI_Model
	{
	
		var $settings = false;
	
		function __construct()
		{
		
			$this->settings = $this->system_vars->get_settings();
		
		}
		
		function get_packages($type = null)
		{

            switch($type)
            {

                case "reading":
                    return $this->db->query("SELECT * FROM packages WHERE type = 'reading' ORDER BY type,price")->result_array();
                    break;

                case "email":
                    return $this->db->query("SELECT * FROM packages WHERE type = 'email' ORDER BY type,price")->result_array();
                    break;

                default:
                    return $this->db->query("SELECT * FROM packages ORDER BY type,price")->result_array();
                    break;

            }
		
		}
		
		function get_package($id = null)
		{
		
			return $this->db->query("SELECT * FROM packages WHERE id = {$id} LIMIT 1")->row_array();
		
		}
		
		function get_categories()
		{
		
			return $this->db->query("SELECT * FROM categories ORDER BY title")->result_array();
		
		}
		
		function get_category($id_or_url)
		{
		
			if(is_numeric($id_or_url))
			{
			
				return $this->db->query("SELECT * FROM categories WHERE id = {$id_or_url}")->row_array();
			
			}
			else
			{
			
				return $this->db->query("SELECT * FROM categories WHERE url = '{$id_or_url}'")->row_array();
			
			}
		
		}
		
		function category_select_box($name = 'categories', $selected_value = null)
		{
		
			$html = "<select name='{$name}' style='width:auto;margin:0;'><option value=''>Select A Category</option>";
				
				foreach($this->get_categories() as $c)
				{
				
					$html .= "<option value='{$c['id']}'".($c['id']==$selected_value ? " selected" : "").">{$c['title']}</option>";
				
				}
				
			$html .= "</select>";
			
			return $html;
		
		}



	}
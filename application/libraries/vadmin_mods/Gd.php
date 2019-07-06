<?
class gd 
{

	//NEW
	function config($name, $value, $params = null)
	{
	    
	        $this->ci =& get_instance();
	        
	        $this->name = $name;
			$this->value = $value;
			$this->size = (isset($params[1]) ? $params[1] : '50');
			$this->upload_path = (isset($params[2]) ? $params[2] : './media/assets/');
			$this->upload_url = (isset($params[3]) ? $params[3] : '/media/assets/');
			$this->Width = $params[4];
	  		$this->Height =$params[5];
	    	$this->MaintainRatio = (isset($params[6]) ? $params[6] : false);
	    	//$this->MasterDIM = $params[7];
			if(trim($this->MaintainRatio)=='true'||trim($this->MaintainRatio)=='maintain_ratio'||trim($this->MaintainRatio)=='') $this->MaintainRatio = true; else $this->MaintainRatio = false;
	}
	//NEW
	function field_view()
	{
	
		$return = "";
	
		// Show Image If One Has Been Uploaded
		if($this->value)
		{
		
			$return .= "
			<div style='margin-bottom:20px;'>
				<div><img src='{$this->upload_url}{$this->value}' border='0' height='100' /></div>
				<div align='left'><input type='checkbox' name='{$this->name}_delete' value='yes'> Delete Current Image</div>
			</div>";
		
		}
		
		// Show an upload form
		$return .= "
		<input type='file' name='{$this->name}' class='tb' size='{$this->size}'>
		<input type='hidden' name='{$this->name}_hidden' value='{$this->value}'>
		";
		
		return $return;
	
	}

	function display_view()
	{
		return "<img src='{$this->upload_url}{$this->value}' width='{$this->Width}' height='{$this->Height} border='0' width='100' />";		
	}
	
	function process_form()
	{
		$checktodelete = true;
		$deletedold = false;
	    if($this->ci->input->post($this->name.'_delete')=='yes')
		{

			unlink($this->upload_path . $this->ci->input->post($this->name.'_hidden'));
			$checktodelete = false;
			$deletedold = true;


	    }

		if($_FILES[$this->name]['tmp_name'])
		{

		    $CI =& get_instance();

		    $ImageExtension = trim(str_replace(".","", substr($_FILES[$this->name]['name'], -4,4)));
		    $NewImagename = time().".".$ImageExtension;

		    $config['image_library'] = 'gd2';
		    $config['source_image'] = $_FILES[$this->name]['tmp_name'];
		    $config['new_image'] = $this->upload_path . $NewImagename;
		    $config['maintain_ratio'] = $this->MaintainRatio;
		    $config['width'] = $this->Width;
		    $config['height'] = $this->Height;

		    $CI->load->library('image_lib', $config);
		    
		    if($CI->image_lib->resize())
		    {
				return $NewImagename;
		    }
		    else
		    {
				return "[%skip%]";		    
			}

		}
		else
		{
			if($deletedold) return "";
			else return "[%skip%]";
		}
	}
}

?>
<?

class fl
{

    function config($name, $value, $params = null)
    {

        $this->ci =& get_instance();

        $this->name = $name;
        $this->value = $value;
        $this->size = (isset($params[1]) ? $params[1] : '50');
        $this->upload_path = (isset($params[2]) ? $params[2] : './media/assets/');
        $this->upload_url = (isset($params[2]) ? $params[2] : '/media/assets/');

    }

    function field_view()
    {

        $return = "";

        // Show Image If One Has Been Uploaded
        if($this->value)
        {

            $return .= "
				<div style='margin-bottom:20px;'>
					<div><a  target='_blank' href='{$this->upload_url}{$this->value}' width='100'>{$this->value}<a/></div>
					<div align='left'><input type='checkbox' name='{$this->name}_delete' value='yes'> Delete Current File</div>
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

        return "<a  target='_blank' href='{$this->upload_url}{$this->value}' width='100'>{$this->value}<a/>";

    }

    function process_form()
    {

        $checktodelete = true;
        $deletedold = false;

        // Check to delete a file
        if($this->ci->input->post($this->name.'_delete')=='yes')
        {
            unlink($this->upload_path . $this->ci->input->post($this->name.'_hidden'));
            $checktodelete = false;
            $deletedold = true;
        }

        // Upload New File
        if($_FILES[$this->name]['tmp_name'])
        {

            // check for and delete old file
            if($this->ci->input->post($this->name.'_hidden')&&$checktodelete)
            {
                unlink($this->upload_path . $this->ci->input->post($this->name.'_hidden'));
            }

            // upload new file
            $tempfile_name = $_FILES[$this->name]['name'];
            $file_extension = trim( str_replace('.','', substr($tempfile_name,-4,4)) );
            $filename = rand(1000000000,9999999999) . time() . '.' . $file_extension;

            move_uploaded_file($_FILES[$this->name]['tmp_name'], $this->upload_path . $filename);

            return $filename;

        }
        else
        {

            if($deletedold) return "";
            else return "[%skip%]";

        }

    }

}

?>
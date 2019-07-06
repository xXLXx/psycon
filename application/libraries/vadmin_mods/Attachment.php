<?

class attachment
{

    function config($name, $value, $params = null)
    {

        $this->ci =& get_instance();

        $this->name = $name;
        $this->value = $value;
        $this->size = (isset($params[1]) ? $params[1] : '50');

    }

    function field_view()
    {

        return "<a href='/media/articles/{$this->value}' target=\"_blank\">{$this->value}</a>";

    }

    function display_view()
    {

        return "<a href='/media/articles/{$this->value}' target=\"_blank\">{$this->value}</a>";

    }

    function process_form(){
        return $this->value;
    }

}

?>
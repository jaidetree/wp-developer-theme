<?php

class SettingsSection
{
    private $id;
    private $title;
    private $callback;
    private $page;
    private $_fields;

    public function __construct($title, $callback)
    {
        $this->_fields = new PanelFields();
        $this->title = __($title, 'jz_theme');
        $this->callback = $callback;
    }

    public function fields()
    {
        return $this->_fields;
    }

    public function name($name = false)
    {
        if( $name ) 
        {
            $this->id = $name;
        }
        else
        {
            return $this->id;
        }
    }
}

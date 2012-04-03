<?php

class Field
{
    private $name = "";
    protected $widget=null;
    private $default=null;
    private $required=false;
    private $label;

    public function draw()
    {
        return $this->_build();
    }

    public function name($name=false, $panel=false)
    {
        if( ! $name )
        {
            return $this->name;
        }

        $this->name = $name;
        $this->widget->name = $panel . '[' .$this->name . ']';
        $this->widget->id = "id_" . $this->name;
    }

    public function widget_name()
    {
        return $this->widget->name;
    }

    public function label()
    {
        return $this->label . ':';
    }

    public function render()
    {           
        return $this->widget->html();
    }

    protected function _set($label, $default=null, $required=null)
    {
        $this->label = $label;
        if( ! is_null($default) )
        {
            $this->default = $default;
            //$this->value( $this->default );
        }
        else
        {
            $this->default = '';
        }

        if( ! is_null($required) )
        {
            $this->required = $required;
        }

    }

    public function set_value($value)
    {
        if( ! $value && ! is_null($this->default) )
        {
            $value = $this->default;
        }

        return $value;
    }

    public function get_value($value)
    {
        return $this->widget->value;
    }

    public function value($value=null)
    {
        $this->widget->value = $this->set_value(value);
        return true;
    }

    public function sanitize($value)
    {
        if( empty($value) && $this->default )
        {
            $value = $this->default;
        }
        return $value;
    }

}

class CharField extends Field
{
    public function __construct($label, $default=null, $required=false, $atts=array())
    {
        $atts = array_merge( array(
            'size' => 50,
            'maxlength' => 255,
            'type' => 'text'
        ), $atts);

        $this->_set($label, $default, $required);
        $this->widget = HTML::input(false, $atts);
    }
    public function sanitize($value)
    {
        return parent::sanitize($value);
    }
}
class Checkbox extends Field
{
    public function __construct($label, $default=null, $required=false, $atts=array())
    {
        $atts = array_merge( array(
            'type' => 'checkbox'
        ), $atts);

        $this->_set($label, $default, $required);
        $this->widget = HTML::input($atts);
    }
}
class TextField extends Field
{
    public function __construct($label, $default=null, $required=false, $atts=array())
    {
        $atts = array_merge( array(
            'cols' => 40,
            'rows' => 15,
        ), $atts);

        $this->_set($label, $default, $required);
        $this->widget = HTML::textarea(false, $atts);
    }

    public function value($value)
    {
        $this->widget->insert( $this->set_value($value) );
    }
}
class CompositeField extends Field
{
    public function __construct($fields=array(), $default=null, $required=false)
    {
        $this->widget = array();

        foreach($fields as $field)
        {
            $field->name = $field->get_name();
            $this->widget[] = $field;
        }    
    }
	
}
class SocialMediaField extends CompositeField
{
    protected $base_name;

    public function __construct($label, $name, $default=null, $required=false)
    {
        parent::__construct(array(
            CharField('Name', null, false, array( 'name' => $name . '_name' )),
            CharField('Profile URL', null, false, array( 'name' => $name . '_url' ),
            CharField('Icon URL', null, false, array( 'name' => $name . '_icon' )),
        ), $default, $required);

        foreach( $this->widget as $field )
        {
            $field->name( $field->widget_name() );
        }
    }
                
    public function name($name=false, $panel=false)
    {
        if( ! $name )
        {
            return $this->name;
        }

        $this->name = $name;
        $this->widget->name = $base_name . '[' . $this->name . ']';
        $this->widget->id = "id_" . $this->name;
    }
    }

    public function value($value)
    {
        $value = split(",", $value);
        foreach( $this->widget as $idx=>$field )
        {
            $field->value( $value[$idx] );
        }
    }

    public function set_value()
    {
    }

    public function sanitize()
    {
    }

    public function get_value()
    {
    }
}

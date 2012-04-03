<?php
class HTML 
{
    private static $self_closing_tags = array(
        'base',
        'link',
        'input',
        'img',
        'meta',
        'br'
    );
    public static function __callStatic($name, $arguments)
    {
        return self::tag($name, $arguments[0], $arguments[1]);
    }
    public static function tag($tag_name, $content = false, $attributes = array()) 
    {
        $self_closing = false;

        if(in_array(strtolower($tag_name), HTML::$self_closing_tags))
        {
            $self_closing = true;
        }

        $tag = new HTMLTag($tag_name, $self_closing, $attributes);

        if( $content )
        {
            $tag->insert( $content );
        }

        return $tag;
    }
}
class HTMLTag
{

    protected $tag_name = "";
    protected $self_closing = false;

	/**
	 * @access protected
	 * @var array
	 */
	protected $content = array();

	/**#@+
	 * @access protected
	 * @var string
	 */
	protected $before_tag = "";
	protected $open_tag = 'html';
	protected $close_tag = 'html';
	protected $after_tag = "";
	protected $tag_padding = '';
	/**#@-*/

	/**
	 * @access protected
	 * @var int
	 */
	protected $indent_level = 0;

	/**
	 * @access private
	 * @var array
	 */
	private $_attributes = array();

	/**
	 * @access private
	 * @var string
	 */
	private $_html = '';

	/**
	 * HTML Class Constructor
	 *
	 * Calls our private _init function to setup the base tags.
	 * @access pubic
	 * @method
	 * @param array $atts The attributes that correlate to the protected class attributes.
	 */
	public function __construct($tag_name, $self_closing = false, $atts = false)
	{
        $this->tag_name = $tag_name;
        if( is_bool( $self_closing ) )
        {
            $this->self_closing = $self_closing;
        }
		$this->_init($atts);
	}

	/**
	 * To String Magic Method
	 *
	 * Allows us to use this class contextually like echo $HTML
	 * @access public
	 * @method
	 */
	public function __toString()
	{
		return $this->html();
	}

	/**
	 * Set Method
	 *
	 * Used for setting our HTML element's attributes.
	 * Requires attributes to be set up from the set_attribute function first.
	 * @method
	 * @access public
	 * @param string $name The name of the HTML attribute
	 * @param string $value The value of the HTML attribute
	 */
	public function __set( $name, $value )
	{
		if( $name == "attributes" && is_array( $value ) )
		{
			foreach( $value as $key => $_value )
			{
				$this->_attributes[ $key ] = $_value;	
			}

			return;
		}

		$this->_attributes[ $name ] = $value;
	}

	/**
	 * Get Method
	 * Used for getting our HTML element's attribute.
	 * @method
	 * @access public
	 * @param string $name The name of the attribute to get.
	 * @return string
	 */
	public function __get( $name )
	{
		if( ! array_key_exists( $name, $this->_attributes ) )
		{
			return null;
		}

		return $this->_attributes[ $name ];
	}

	/**
	 * Output HTML Method
	 *
	 * This function may be called to compile the object's data into an HTML string.
	 * @access public
	 * @return string
	 */
	public function html()
	{
		$indent = str_repeat( "\t", $this->indent_level );

		$html = $this->_compile();

		$html = $indent . $html;

		return $html;
	}

	/**
	 * Insert into HTML Element's content
	 *
	 * Add the HTML element to the content and return the handle of the object
	 * as a reference to allow chaining.
	 * @access public
	 * @param mixed $element Something to add to the contents.
	 * @return HTMLSubClass
	 */
	public function insert( $element, $return_child = true )
	{
		if( is_array( $element ) )
		{
			$this->content = array_merge( $this->content, $element );	
		}else{
			$this->content[] = $element;
		}

		if( is_object( $element ) && get_parent_class( $element ) == get_parent_class( $this ) && $return_child )
		{
			return $element;
		}else{
			return $this;
		}
	}

	/**
	 * Set HTML Attribute
	 *
	 * Sub-classes should use this function to set available attributes.
	 * This helps ensure that only valid attributes for that tag are used.
	 * @access protected
	 * @param string $name The name of the attribute to be set.
	 * @param string $value The value of the attribute to be set.
	 * @param bool $overwrite Optional: Whether or not to overwrite the current value.
	 * @return bool true|false
	 */
	protected function set_attribute($name, $value = '', $overwrite = false)
	{
		$this->_attributes[ $name ] = $value;

		return true;
	}

	/**
	 * Get HTML Attribute
	 *
	 * This function is used internally within the class. It just makes sense
	 * to create 1 function to be used for accessing them in case their
	 * architecture changes you only have to change this one tiny function.
	 * @access protected
	 * @param string $name The name of the HTML attribute to get.
	 * @return string
	 */
	protected function get_attribute( $name )
	{
		if( ! array_key_exists( $name, $this->_attributes ) )
        {
			return false;
		}
		return $this->_attributes[ $name ];
	}

	/**
	 * Convert HTML attributes to String
	 *
	 * Takes our attributes array and converts it to a key="value" string.
	 * @access private
	 * @return string
	 */
	private function _attributes_to_string()
	{
		$str = '';
		foreach( $this->_attributes as $name => $value )
		{
			if( ! $name or ( empty( $value ) && ! is_numeric( $value ) ) )
			{
				continue;
			}

			if( is_bool( $value ) )
			{
				$str .= ' ' . $name; 	
			}else{
				$str .= ' ' . $name . '="' . $value . '"'; 	
			}
		}

		return $str;
	}

	/**
	 * Compile
	 *
	 * Transforms our object into an HTML string. This function should be able to be
	 * over-written in an extending sub-class if necessary.
	 * @access private
	 * @return string
	 */
	private function _compile()
	{
		if( method_exists( $this, 'compile' ) )
		{
			$html = $this->compile();
			return $html;
		}

		$html = $this->build_open_tag();
		
		if( $this->content )
		{
			$html .= implode( "", $this->content );
		}

		$html .= $this->build_close_tag();
		

        $this->_html = $html;

		return $html;
	}

	function build_open_tag()
	{
		$html = $this->before_tag;

		$html .= '<' . $this->open_tag . $this->_attributes_to_string() . '>';
		
		if( $this->tag_padding )
		{
			$html .= $this->tag_padding;
		}

		return $html;
	}

	function build_close_tag()
	{
		if( $this->tag_padding )
		{
			$html .= $this->tag_padding;
		}

		if( $this->close_tag )
		{
			$html .= '</' . $this->close_tag . '>';
		}

		$html .= $this->after_tag;

		return $html;
	}

	/**
	 * Public Init Function
	 * 
	 * This funciton calls our private init function and is used by the HTML Tag Factory Class
	 * @access public
	 */
	public function init()
	{
		$this->_init();
	}

	/**
	 * Local Init function
	 * 
	 * This function is used to setup the default attributes every tag will most likely have.
	 * @access private
	 * @param array|false $atts An optional array of default settings over-rides used
	 * if you don't want to make a seperate class for a 1 off HTML tag.
	 */
	private function _init($atts = false)
	{
		if( is_array( $atts ) )
		{
			foreach( $atts as $name=>$value )
			{
				if( $name == 'content' && ! is_array( $value )  )
				{
					$this->content[] = $value;
					continue;
				}

				$this->$name = $value;
			}
		}

        $this->open_tag = $this->tag_name;

        if( ! $this->self_closing )
        {
            $this->close_tag = $this->tag_name;    
        }

        /*
         * $this->set_attribute( 'id' );
         * $this->set_attribute( 'class' );
	     * $this->set_attribute( 'style' );
	     * $this->set_attribute( 'lang' );
	     * $this->set_attribute( 'dir' );
	     * $this->set_attribute( 'title' );
	     * $this->set_attribute( 'xml:lang' );
         */
	}

}
?>

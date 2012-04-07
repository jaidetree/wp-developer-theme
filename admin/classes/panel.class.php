<?php
class PanelFactory
{
    private static $panels = array();

    public static function init()
    {
        JZ_THEME::init()->load_directory( THEME_LIB_DIR . 'admin/panels', '.panel.php', 'PanelFactory::load_panel' );
    }

    public static function load_panel($file)
    {
        $name = basename($file, ".panel.php");
        $name = str_replace("_", " ", $name);
        $name = ucwords($name);
        $name = str_replace(" ", "_", $name);

        self::$panels[] = new $name();
    }
}
add_action( 'after_setup_theme', 'PanelFactory::init' );
abstract class AdminPanel
{
    private $panel_name;
    private $db_name;
    private $slug;
    private $_sections; 
    private $options;


    abstract protected function set_fields();
    abstract protected function panel_name();
    abstract protected function db_name();
    abstract protected function slug();
    abstract protected function sanitize_data($data);

    private function draw_view()
    {
        return 'panel.php';
    }

    public function render()
    {
        $panel = $this;
        include THEME_LIB_DIR . 'admin/views/' . $this->draw_view();
    }

    public function render_field($args)
    {
        list($field, $name) = $args;
        $field->name($name, $this->db_name);
        $option = $this->get_option( $name );

        $field->value($option);
        echo $field->render();
    }

    protected function get_options()
    {
        if( ! $this->options )
        {
	        $this->options = get_option( $this->db_name );
        }

        return $this->options;
    }

    protected function get_option($key)
    {
        $options = $this->get_options();
        if( $data = $options[$key] )
        {
            return $data;
        }
        else
        {
            return '';
        }
    }

    protected function sections($name=false)
    {
        if( ! $name )
        {
            return $this->_sections;
        }else{
            return $this->_sections->find($name);
        }
    }

    protected function init()
    {
        add_action('admin_init', array(&$this, 'action_init'));
        add_action( 'admin_menu', array(&$this, 'action_menu'));      

        $this->panel_name = $this->panel_name();
        $this->db_name = $this->db_name();
        $this->slug = $this->slug();

        $this->_sections = new PanelSections($this);
        $this->set_fields();
    }

    public function get_panel_name()
    {
        return $this->panel_name;
    }

    public function action_init()
    {
        register_setting(
            $this->panel_name,       // Options group, see settings_fields() call in twentyeleven_theme_options_render_page()
            $this->db_name, // Database option, see twentyeleven_get_theme_options()
            array(&$this, 'sanitize') // The sanitization callback, see twentyeleven_theme_options_validate()
        );

        foreach( $this->_sections->all() as $section )
        {
            // Register our settings field group
            add_settings_section(
                $section->name(), // Unique identifier for the settings section
                '', // Section title (we don't want one)
                '__return_false', // Section callback (we don't want anything)
                $this->slug // Menu slug, used to uniquely identify the page; see twentyeleven_theme_options_add_page()
            );       
            foreach( $section->fields()->all() as $field )
            {
                add_settings_field(
                    $field->name(), 
                    $field->label(),
                    array(&$this, 'render_field'),
                    $this->slug,
                    $section->name(),
                    array(&$field, $field->name())
                );
            }
        }
    }
    function action_menu()
    {
        $theme_page = add_theme_page(
            __( 'Theme Options', 'twentyeleven' ),   // Name of page
            __( 'Theme Options', 'twentyeleven' ),   // Label in menu
            'edit_theme_options',                    // Capability required
            $this->slug,                         // Menu slug, used to uniquely identify the page
            array(&$this, 'render') // Function that renders the options page
        );

        if ( ! $theme_page )
            return;

        //add_action( "load-$theme_page", 'twentyeleven_theme_options_help' );
    }
    public function sanitize($data)
    {
        foreach( $data as $name=>$value)
        {
            foreach( $this->sections()->all() as $section )
            {
                if( $field = $section->fields()->find($name) )
                {
                    $data[$name] = $field->sanitize($value);
                }
            }
            $sanitize_callback = 'sanitize_' . $name;
            if( method_exists( $this, $sanitize_callback ) )
            {
                $data[$name] = $this->$sanitize_callback($value);
            }
        }
        $clean_data = $this->sanitize_data($data);
        return $clean_data;
    }
}
class PanelManager
{
    private $parent;
    protected $_data = array();

    public function __construct($args=array())
    {
    }
    public function __get($name)
    {
        return $this->_data[$name];
    }

    public function __set($name, $value)
    {
        $value->name($name);
        $this->_data[strtolower($name)] = &$value;
    }

    public function all()
    {
        return (array)$this->_data;
    }
}
class PanelFields extends PanelManager
{

    public function __construct()
    {
        parent::__construct();
    }
    public function find($name)
    {
        foreach($this->_data as $field)
        {
            if( strtolower($name) == strtolower($field->name()) )
            {
                return $field;
            }
        }

        return false;
    }
    public function __set($name, $value)
    {
      
        $value->name($name, $this->name);
        $this->_data[] = $value;
    }

}
class PanelSections extends PanelManager
{
    public function __construct($args=array())
    {
        parent::__construct($args);
    }
    public function find($name)
    {
        $name = strtolower($name);
        if( is_object($this->_data[$name]) )
        {
            return $this->_data[$name];
        }

        return false;
    }
}

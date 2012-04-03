<?php 
class MetaBoxFactory
{
	private static $instance;
	private static $meta_boxes = array();

	public static function factory()
	{
        JZ_THEME::init()->load_directory( THEME_LIB_DIR . 'metaboxes',  '.box.php', 'MetaBoxFactory::load_meta_box' );

		self::run();
	}

	public static function load_meta_box($file)
	{
		$name = basename( $file, '.box.php' );
		$class = ucwords( $name ) . 'Box';
		$meta_box = new $class;
		$meta_box->init();

		self::$meta_boxes[] = $meta_box;
	}

	public static function run()
	{
		add_action( 'admin_init', 'MetaBoxFactory::add_boxes', 1 );
		add_action( 'save_post', 'MetaBoxFactory::save', 1 );
	}

	public static function add_boxes()
	{
		foreach(self::$meta_boxes as $meta_box)
		{
			add_meta_box( $meta_box->name, __($meta_box->label), array( $meta_box, 'draw_box' ), $meta_box->post_types, $meta_box->position, $meta_box->priority ); 
		}
	}

	public static function save( $post_id )
	{
		foreach( self::$meta_boxes as $meta_box )
		{
			if( wp_verify_nonce( $_POST[ $meta_box->nonce['name'] ], $meta_box->nonce['file'] ) )
			{
				self::save_data( $meta_box, $post_id );
			}
		}
	}

	public static function save_data( $meta_box, $post_id )
	{
	    //echo "<pre>";
		//print_r( $_POST );
		//die();
		$_data = $meta_box->apply_save_filters( $_POST, $post_id );
		
		foreach( $meta_box->fields() as $field )
		{
			if( is_null( $_data[ $field['name'] ] ) or substr( $field['name'], 0, 1 ) == "_" )
			{
				continue;
			}
			
			update_post_meta( $post_id, $field['_db_name'], $_data[ $field['name'] ] ); 

		}
	}
}

MetaBoxFactory::factory();

class MetaBox
{
	var $name;
	var $label;
	var $post_types;
	var $position;
	var $priority = 'default';
	var $nonce;

	protected $_fields = array();
	protected $_filters = array();
	private $_html = array();

	function init()
	{
		$this->_fields = $this->fields;

		foreach( $this->_fields as $key=>$field )
		{
			$this->_fields[$key]['_db_name'] = $this->get_db_name( $field['name'] );
			$this->_fields[$key]['name'] = $this->unique_name( $field['name'] );
		}

		$this->build_nonce();

	}

	function draw_box($post)
	{
		$html = $this->apply_draw_filters( $this->build_html($post->ID) , $post->ID );

		echo implode( "\n", $html );
	}

	function get_db_name($name)
	{
		$name = preg_replace( '/^_.*$/', '', $name );
		$name = "_" . $this->unique_name( $name );
		return $name;
	}

	function unique_name( $name )
	{                  
		if( ! preg_match( '/^_.*$/', $name ) )
		{
			$name = $this->name . '-' . $name;
		}

		return $name;
	}

	function get_field_value($post_id, $key)
	{
		$name = $this->_fields[ $key ][ '_db_name' ];
		$value = get_post_meta( $post_id, $name, true );

		return $value;
	}

	function build_html($post_id)
	{
		if( $this->_html )
		{
			return $this->_html;
		}

		$this->_html[] = $this->nonce['field'];

		foreach( $this->_fields as $key=>$field )
		{
			$p = HTML::tag('p');
			$label = HTML::tag('label');

			$label->for = $field['name'];
			$label->insert( __( $field['label'], 'akz_metabox' ) );


			if( ! $field['value']  )
			{
				$value = $this->get_field_value( $post_id, $key );
			}
			else
			{
				$value = $field['value'];
			}

			switch( $field['type'] )
			{
				case 'textarea':
					$input = HTML::tag( 'textarea', $value );
				break;
				case 'select':
					$input = $this->build_select( $field['options'], $value );
				break;
				default:
					$input = HTML::tag( 'input' );
					$input->type = $field['type'];
					$input->value = $value;
				break;
			}

			if( is_array( $field['atts'] ) )
			{
				$input->attributes = $field['atts'];
			}

			$input->name = $field['name'];
			$input->id = $field['name'];

			$p->insert( $label );
            $p->insert( HTML::tag('br') );
            if( $field['help'] )
            {
                $p->insert( HTML::tag('em', $field['help'], array('class' => 'small')) );
            }
            $p->insert( HTML::tag('br') );
			$p->insert( $input );
			$this->_html[] = $p;
		}

		return $this->_html;
	}

	private function build_select( $options, $value )
	{
		foreach( $options as $key=>$option )
		{
			if( $option['value'] == $value )
			{
				$options[ $key ]['selected'] = true;
			}
		}

		$input = HTML::tag( 'select', $options );

		return $input;
	}

	function build_nonce()
	{
		if( ! $this->nonce )
		{
			$this->nonce['name'] = 'akz_meta_' . $this->name;
			$this->nonce['file'] = plugin_basename( __FILE__ );
			$this->nonce['field'] = wp_nonce_field( $this->nonce['file'], $this->nonce['name'], true, false );
		}
		
		return $this->nonce;
	}

	function fields()
	{
		if( ! count( $this->_fields ) )
		{
			return false; 
		}
		return $this->_fields;
	}

	function apply_save_filters($post_data, $post_id)
	{
		$name = $this->name;
		return apply_filters( 'akz-metabox-save-' . $name, $post_data, $post_id );
	}

	function add_save_filter($function)
	{
		$name = $this->name;
		add_filter( 'akz-metabox-save-' . $name, array( $this, $function ), 10, 2 );
	}

	function apply_draw_filters($html, $post_id)
	{
		$name = $this->name;
		return apply_filters( 'akz-metabox-draw-' . $name, $html, $post_id );
	}

	function add_draw_filter( $function )
	{
		$name = $this->name;
		add_filter( 'akz-metabox-draw-' . $name, array( $this, $function ), 10, 2 );
	}
}
?>

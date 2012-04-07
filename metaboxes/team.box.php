<?php 
/**
 * Class TeamBox
 * Adds the fields for team member management.
 * @package TeamBox
 * @subpackage MetaBoxFactory
 * @author Jay Zawrotny <jay@aetkinz.com>
 * @version 0.1
 * @access public
 */
class TeamBox extends MetaBox 
{
	/**
	 * @var string The name of the metabox
	 */
	var $name = 'team-meta';

	/**
	 * @var string The label to display on the post editing screen
	 */
	var $label = "Team Member Details";

	/**
	 * @var mixed The post types to show the meta box on.
	 */
	var $post_types = 'nd_team';

	/**
	 * @var string The position, or context as WordPress refers which area it goes
	 */
	var $position = 'normal';

	/**
	 * @var string The priority of the box.
	 */
	var $priority = 'high';

	/**
	 * @var array The input fields the meta box will have
	 */
	var $fields = array(  
		array( 
			'name' => 'position', 
			'type'=> 'text', 
			'label' => 'Company Position:', 
        ),
		array( 
			'name' => 'twitter', 
			'type'=> 'text', 
			'label' => 'Twitter URL:', 
            'help' => 'Leave blank if none'
        ),
		array( 
			'name' => 'facebook', 
			'type'=> 'text', 
			'label' => 'Facebook URL:', 
            'help' => 'Leave blank if none'
        ),
		array( 
			'name' => 'dribbble', 
			'type' => 'text', 
			'label' => 'Dribbble URL:', 
            'help' => 'Leave blank if none'
        ),
        array(
            'name' => 'vimeo',
            'type' => 'text',
            'label' => 'Vimeo URL:',
            'help' => 'Leave blank if none'
        ),
    );

	/**
	 * @method Class constructer, you can set your filters here. For convinence use the 
	 * functions defined in the parent MetaBox class.
	 * @access public
	 * @see add_draw_filter()
	 * @see add_save_filter()
	 */
	function __construct()
	{
		//$this->add_draw_filter( 'show_photo' );
		//$this->add_save_filter( 'save' );

		//$dir = dirname( __FILE__ );
		//$dir = preg_replace( '/wp-content\/.*$/', 'wp-content', $dir );
		//$this->dir = $dir . '/photos/';
	}


}
?>

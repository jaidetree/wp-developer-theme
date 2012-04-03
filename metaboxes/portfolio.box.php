<?php 
/**
 * Class PortfolioBox
 * Adds a url box to the portfolio work admin panels
 * @package PortfolioMetaBox 
 * @subpackage MetaBoxFactory
 * @author Jay Zawrotny <jay@aetkinz.com>
 * @version 0.1
 * @access public
 */
class PortfolioBox extends MetaBox 
{
	/**
	 * @var string The name of the metabox
	 */
	var $name = 'portfolio-meta';

	/**
	 * @var string The label to display on the post editing screen
	 */
	var $label = "Portfolio Work Details";

	/**
	 * @var mixed The post types to show the meta box on.
	 */
	var $post_types = 'nd_portfolio';

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
			'name' => 'url', 
			'type'=> 'text', 
			'label' => 'Link to URL:', 
            'help' => 'leave blank to link to full-image'
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

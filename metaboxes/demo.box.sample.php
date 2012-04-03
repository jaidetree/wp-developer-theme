<?php 
/**
 * Class PhotosBox
 * Adds a photo upload box to our dive photos custom post type.
 * @package PhotoMetabox
 * @subpackage MetaBoxFactory
 * @author Jay Zawrotny <jay@aetkinz.com>
 * @version 0.5
 * @access public
 */
class PhotosBox extends MetaBox 
{
	/**
	 * @var string The name of the metabox
	 */
	var $name = 'gallery-image';

	/**
	 * @var string The label to display on the post editing screen
	 */
	var $label = "Gallery Image";

	/**
	 * @var mixed The post types to show the meta box on.
	 */
	var $post_types = 'gallery';

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
			'name' => '_delete-thumb',
			'type' => 'checkbox',
			'value' => 1,
			'label' => 'Delete Thumbnail: ' ),
		array( 
			'name' => 'photo', 
			'type'=> 'file', 
			'label' => 'Photo:' )
		);

	/**
	 * @var string The directory to store files
	 */
	private $dir = '';
	/**
	 * @method Class constructer, you can set your filters here. For convinence use the 
	 * functions defined in the parent MetaBox class.
	 * @access public
	 * @see add_draw_filter()
	 * @see add_save_filter()
	 */
	function __construct()
	{
		$this->add_draw_filter( 'show_photo' );
		$this->add_save_filter( 'save' );

		$dir = dirname( __FILE__ );
		$dir = preg_replace( '/wp-content\/.*$/', 'wp-content', $dir );
		$this->dir = $dir . '/photos/';
	}

	/**
	 * Show Photo Filter
	 *
	 * The callback for a filter called in this class' constructor via add_draw_filter method.
	 * @access public
	 */
	function show_photo( $html, $post_id )
	{
		$name = $this->get_db_name( 'photo' );
		$image = get_post_meta( $post_id, $name, true );

		if( ! $image )
		{
			unset( $html[1] );
			return $html;
		}

		$thumb = preg_replace( '/\.jpg$/', '_thumb.jpg', $image );
		$url_dir = preg_replace( '#^.*/wp-content#', '/wp-content', $this->dir ); 

		$img = HTML::tag( 'img', false, array( 'src' => $url_dir . $thumb, 'alt' => $url_dir . $image) );
		$a = HTML::tag( 'a', $img, array( 'href' => $url_dir . $image ) );
		$input = HTML::tag( 'input', false, array( 'name' => '_dive_photo', 'type' => 'hidden', 'value' => $image ) );
		
		array_unshift( $html, $a );
		$html[] = $input;

        return $html;
	}
	
	/**
	 * Resize Image
	 *
	 * Takes the source image and resizes it to the specified width & height or proporitionally if crop is off.
	 * @access public
	 * @param string $source_image The location to the original raw image.
	 * @param string $destination_filename The location to save the new image.
	 * @param int $width The desired width of the new image
	 * @param int $height The desired height of the new image.
	 * @param int $quality The quality of the JPG to produce 1 - 100
	 * @param bool $crop Whether to crop the image or not. It always crops from the center.
	 */
	function resize_image($source_image, $destination_filename, $width = 200, $height = 150, $quality = 70, $crop = true)
	{

		if( ! $image_data = getimagesize( $source_image ) )
		{
			return false;
		}

		switch( $image_data['mime'] )
		{
			case 'image/gif':
				$get_func = 'imagecreatefromgif';
				$suffix = ".gif";
			break;
			case 'image/jpeg';
				$get_func = 'imagecreatefromjpeg';
				$suffix = ".jpg";
			break;
			case 'image/png':
				$get_func = 'imagecreatefrompng';
				$suffix = ".png";
			break;

			default:
				return false;
			break;
		}

		$img_original = call_user_func( $get_func, $source_image );
		$old_width = $image_data[0];
		$old_height = $image_data[1];
		$new_width = $width;
		$new_height = $height;
		$src_x = 0;
		$src_y = 0;
		$current_ratio = round( $old_width / $old_height, 2 );
		$desired_ratio_after = round( $width / $height, 2 );
		$desired_ratio_before = round( $height / $width, 2 );

		if( $old_width < $width || $old_height < $height )
		{
			/**
			 * The desired image size is bigger than the original image. 
			 * Best not to do anything at all really.
			 */
			return false;
		}


		/**
		 * If the crop option is left on, it will take an image and best fit it
		 * so it will always come out the exact specified size.
		 */
		if( $crop )
		{
			/**
			 * create empty image of the specified size
			 */
			$new_image = imagecreatetruecolor( $width, $height );

			/**
			 * Landscape Image
			 */
			if( $current_ratio > $desired_ratio_after )
			{
				$new_width = $old_width * $height / $old_height;
			}

			/**
			 * Nearly square ratio image.
			 */
			if( $current_ratio > $desired_ratio_before && $current_ratio < $desired_ratio_after )
			{
				if( $old_width > $old_height )
				{
					$new_height = max( $width, $height );
					$new_width = $old_width * $new_height / $old_height;
				}
				else
				{
					$new_height = $old_height * $width / $old_width;
				}
			}

			/**
			 * Portrait sized image
			 */
			if( $current_ratio < $desired_ratio_before  )
			{
				$new_height = $old_height * $width / $old_width;
			}

			/**
			 * Find out the ratio of the original photo to it's new, thumbnail-based size
			 * for both the width and the height. It's used to find out where to crop.
			 */
			$width_ratio = $old_width / $new_width;
			$height_ratio = $old_height / $new_height;

			/**
			 * Calculate where to crop based on the center of the image
			 */
			$src_x = floor( ( ( $new_width - $width ) / 2 ) * $width_ratio );
			$src_y = round( ( ( $new_height - $height ) / 2 ) * $height_ratio );
		}
		/**
		 * Don't crop the image, just resize it proportionally
		 */
		else
		{
			if( $width < $height )
			{
				$ratio = max( $old_width, $old_height ) / max( $width, $height );
			}else{
				$ratio = min( $old_width, $old_height ) / min( $width, $height );
			}

			$new_width = $old_width / $ratio;
			$new_height = $old_height / $ratio;

			$new_image = imagecreatetruecolor( $new_width, $new_height );
		}

		/**
		 * Where all the real magic happens
		 */
		imagecopyresampled( $new_image, $img_original, 0, 0, $src_x, $src_y, $new_width, $new_height, $old_width, $old_height );

		/**
		 * Save it as a JPG File with our $destination_filename param.
		 */
		imagejpeg( $new_image, $destination_filename, $quality  );

        /**
		 * Destroy the evidence!
		 */
		imagedestroy( $new_image );
		imagedestroy( $img_original );

		/**
		 * Return true because it worked and we're happy. Let the dancing commence!
		 */
		return true;
	}

	/**
	 * Save Filter
	 *
	 * It's a wordpress filter that can maniuplate the data before going into the
	 * update_post_meta function. This one generates a thumbnail, and moves the orignal.
	 * @access public 
	 * @see resize_image()
	 * @param array $data The raw post data
	 */
	function save( $data )
	{
		if( $data['_delete-thumb'] == 1 )
		{
			$this->delete_photo( $data['post_ID'], $data['_dive_photo'] );
		}

		if( ! count( $_FILES ) )
		{
			return $data;
		}

		$name = $this->unique_name( 'photo' );
		$file = $_FILES[ $name ];

		/**
		 * Editable variables 
		 */
		$THUMB_WIDTH = 32;
		$THUMB_HEIGHT = 32;

		$filename = $this->dir . preg_replace( '/^(.*)\..*/', '$1', $file['name'] );
		$filename .= '_' . time();
		$thumb_filename = $filename . '_thumb.jpg';
		$photo_filename = $filename . '.jpg';

		if( ! $this->resize_image( $file['tmp_name'], $thumb_filename, $THUMB_WIDTH, $THUMB_HEIGHT ) )
		{
			return $data;
		}

		if( ! $this->resize_image( $file['tmp_name'], $photo_filename, 600, 450, 70, false ) )
		{
			move_uploaded_file( $file['tmp_name'], $photo_filename );
		}

		@unlink( $file['tmp_name'] );

		/**
		 * Update the data so that the stored meta value relates to the uniquely named, moved image.
		 */
		$data[ $name ] = basename( $photo_filename ); 

		return $data;
	}

	function delete_photo($post_id, $photo_filename)
	{
		$photo = $photo_filename;

		if( ! $photo ) 
		{
			return false;
		}

		$photo_path = $this->dir . $photo_filename;
		$thumb_path = $this->dir . preg_replace( '/\.jpg$/', '_thumb.jpg', $photo_filename );

		@unlink( $photo_path );
		@unlink( $thumb_path );

		echo delete_post_meta( $post_id, $this->get_db_name( 'photo' ) );
	}
}
?>

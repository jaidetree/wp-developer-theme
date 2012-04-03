<?php
/**
 * Flickr Photostream Widget
 *
 * There's a million of these out there but they would
 * probably have different markup then the CSS needs to be
 * edited, which is probably easier but whatever. This is
 * way more fun.
 */
class NDFlickrWidget extends WP_Widget {
    /** constructor */
    function NDFlickrWidget() {
        parent::WP_Widget(false, $name = 'Not Design Flickr Widget', array( 'classname' => 'last' ));	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
		global $post;
        extract( $args );

        echo $before_widget;

        $title = apply_filters('widget_title', $instance['title']);
        $content = $instance['content'];

        $div = HTML::tag('div');
            $div->id = "flickr_badge_wrapper";
            $script = HTML::tag('script');
                $script->type = "text/javascript";
                $script->src = $content;
                $div->insert( $script );
        
            
		echo $div;
        echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['content'] = $new_instance['content'];
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        $content = esc_attr($instance['content']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('Flickr URL:'); ?></label> 
          <textarea class="widefat" id="<?php echo $this->get_field_id('content'); ?>" rows="16" cols="20" name="<?php echo $this->get_field_name('content'); ?>"><?php echo $content; ?></textarea>
        </p>
        <?php 
    }

} // class FacebookLikeWidget
add_action('widgets_init', create_function('', 'return register_widget("NDFlickrWidget");'));
?>

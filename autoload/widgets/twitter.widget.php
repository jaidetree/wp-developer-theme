<?php
/**
 * Twitter Widget
 * A simple twitter widget that works with the system you
 * are already using.
 */
class NDTwitterWidget extends WP_Widget {
    /** constructor */
    function NDTwitterWidget() {
        parent::WP_Widget(false, $name = 'ND Twitter Widget');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
		global $post;
        extract( $args );

        echo $before_widget;

        $title = apply_filters('widget_title', $instance['title']);
        $username = $instance['username'];

		if ( $title )
			echo $before_title . $title . $after_title;

        $div = HTML::tag('div');
            $div->class = "tweets";
            
        echo "\n" . $div . "\n";
        echo HTML::tag('script', '/***************************************************
	     ADDITIONAL CODE FOR TWITTER
***************************************************/
    jQuery(document).ready(function($) {
      $(".tweets").tweet({
        join_text: "auto",
        username: "' . $username .'",
        avatar_size: 0,
        count: 2,
        auto_join_text_default: "", 
        auto_join_text_ed: "",
        auto_join_text_ing: "",
        auto_join_text_reply: "",
        auto_join_text_url: "",
        loading_text: "loading tweets..."
      });
    })');
	
	                        
        echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['username'] = strip_tags($new_instance['username']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        $username = esc_attr($instance['username']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Twitter Username:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
        </p>
        <?php 
    }

} // class FacebookLikeWidget
add_action('widgets_init', create_function('', 'return register_widget("NDTwitterWidget");'));
?>

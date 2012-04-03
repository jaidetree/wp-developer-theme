<?php
/**
 * Skills Widget
 */
class NDSkillsWidget extends WP_Widget {
    /** constructor */
    function NDSkillsWidget() {
        parent::WP_Widget(false, $name = 'About Skills Widget', array( 'classname' => 'last' ));	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
		global $post;
        extract( $args );

        echo $before_widget;

        $title = apply_filters('widget_title', $instance['title']);
        $content = $instance['content'];

        $data = json_decode($content, true);

		if ( $title )
			echo $before_title . $title . $after_title;

        $ul = HTML::tag('ul');
        $ul->id = "services-graph";

        foreach( $data as $value => $key )
        {
            $span = HTML::tag('span');
            $span->title = $value;
            $p = HTML::tag('p', $key . " ");
                $strong = HTML::tag('strong', $value . "%");
                $p->insert( $strong );
            $li = HTML::tag('li', array( $span, $p ));
                $ul->insert( $li );
        }

		
		echo $ul;
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
          <label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('content:'); ?></label> 
          <textarea class="widefat" id="<?php echo $this->get_field_id('content'); ?>" rows="16" cols="20" name="<?php echo $this->get_field_name('content'); ?>"><?php echo $content; ?></textarea>
        </p>
        <?php 
    }

} // class FacebookLikeWidget
add_action('widgets_init', create_function('', 'return register_widget("NDSkillsWidget");'));
?>

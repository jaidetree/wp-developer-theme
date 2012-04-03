<?php
/**
 * Latest Posts Widget
 *
 * A customized Latest Posts widget that uses
 * a lighter amount of markup.
 */
class NDLatestPostsWidget extends WP_Widget {
    /** constructor */
    function NDLatestPostsWidget() {
        parent::WP_Widget(false, $name = 'Not Design Latest Posts');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
		global $post;
        extract( $args );

        echo $before_widget;

        $title = apply_filters('widget_title', $instance['title']);
        $num_posts = $instance['num_posts'];

        if( ! is_numeric($num_posts) && empty( $num_posts ) )
        {
            $num_posts = 5;
        }
        
        if ( $title )
			echo $before_title . $title . $after_title;

        $posts = get_posts(array( 
            'numberposts' => (int)$num_posts,
        ));

        $ul = HTML::tag('ul');
            $ul->class = "latest-posts";
        
        foreach( $posts as $post )
        {
            $ul->insert( HTML::tag('li',HTML::tag('a', $post->post_title, array( 
                'href' => get_permalink($post->ID), 
            ))) );
        }
            
		echo $ul;
        echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['num_posts'] = $new_instance['num_posts'];
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        $num_posts = esc_attr($instance['num_posts']);
        if( ! is_numeric($num_posts) && empty( $num_posts ) )
        {
            $num_posts = 5;
        }
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
         <p>
          <label for="<?php echo $this->get_field_id('num_posts'); ?>"><?php _e('Number of Posts:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('num_posts'); ?>" name="<?php echo $this->get_field_name('num_posts'); ?>" type="text" value="<?php echo $num_posts; ?>" />
        </p>
        <?php 
    }

} // class NDLatestPostsWidget
add_action('widgets_init', create_function('', 'return register_widget("NDLatestPostsWidget");'));
?>

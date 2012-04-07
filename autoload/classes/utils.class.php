<?php
class JZUtils
{
    function __construct()
    {
        add_shortcode( 'get_page', array( &$this, 'get_page' ) );
    }
    public function get_page($atts)
    {
        extract( shortcode_atts( array(
            'slug' => '',
            ), $atts ) );

        return get_post_by_slug($slug);
    }
    public function get_post_by_slug($slug, $atts=false)
    {
        if( ! $atts )
        {
            $atts = array(
                'post_type' => 'page',
                'name' => $slug
            );
        }
        $posts = query_posts($atts);
        wp_reset_query();

        return do_shortcode($posts[0]->post_content);
    }

}
JZ_THEME::register_module('general','utils', new JZUtils())
?>

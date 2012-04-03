<?php
/**
 * Rotator Slide Model
 *
 * Represents a single Rotator Slide
 *
 * Can be used anywhere within a wordpress template.
 */
class RotatorSlide
{
    private $post;
    private $id;
    private $index = 0;



    public function __construct($item, $index=0)
    {
        if( is_int($item) )
        {
            $this->post = $this->_get_item( $item );
        }
        elseif( is_object($item) )
        {
            $this->post = $item;
        }
        elseif( is_string($item) )
        {
            $this->post = $this->_get_item_by_slug();
        }
        $this->id = $this->post->ID;

        // Specific to this model
        $this->index = $index;
    }


    public function __toString()
    {
        return $this->post->post_title;
    }

    public function _get_item($id)
    {
        return get_post($id);
    }

    public function _get_item_by_slug($slug)
    {
        $posts = query_posts(array(
            'post_type' => 'nd_rotator',
            'name' => $slug
        ));

        return $posts[0];
    }

    private function get_meta($key)
    {
        return get_post_meta( $this->id, '_rotator-meta-' . $key, true);
    }

    public function get_link()
    {
        return $this->get_meta( 'url' );
    }
    public function link()
    {
        echo $this->get_link();
    }

    public function thumbnail()
    {
        echo get_the_post_thumbnail($this->id, 'rotator');
    }

    public function get_content()
    {
        return $this->post->post_content;
    }

    public function content()
    {
        echo $this->get_content();
    }
}

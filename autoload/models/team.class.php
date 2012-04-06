<?php
/**
 * Team Member Model
 *
 * Represents a single Team Member in the Team post-type.
 *
 * Can be used anywhere within a wordpress template.
 */
class TeamMember
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
            'post_type' => 'nd_team',
            'name' => $slug
        ));

        return $posts[0];
    }

    private function get_meta($key)
    {
        return get_post_meta( $this->id, '_team-meta-' . $key, true);
    }

    public function the_social_link($name)
    {
        echo $this->get_meta($name);
    } 

    public function get_alt($media)
    {
        $name = $this->get_name();
        $suffix = "&rsquo;";
        if( ! preg_match('/s$/', $name) )
        {
            $suffix .= "s";
        }
        $name .= $suffix;
        return $name . " on " . $media;
    }
    public function alt($name)
    {
        echo $this->get_alt($name);
    }

    public function has($media)
    {
        if( $this->get_meta($media) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_name()
    {
        return $this->post->post_title;
    }

    public function name()
    {
        echo $this->post->post_title;
    }

    public function get_position()
    {
        return $this->get_meta('position');
    }

    public function position()
    {
        echo $this->get_position();
    }

    public function thumbnail()
    {
        echo get_the_post_thumbnail($this->id, 'team');
    }

    public function get_post_class($class)
    {
        if( $this->index % 3 === 0 && $this->index > 0 )
        {
            return ' ' . $class;
        }
    }

    public function post_class($classname)
    {
        if( $class = $this->get_post_class($classname) )
        {
            echo $class;
        }
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

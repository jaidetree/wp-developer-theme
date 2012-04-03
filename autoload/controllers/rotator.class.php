<?php
class RotatorController
{
    private $posts = array();
    private $index = 0;
    var $post = null;

    public function get_all()
    {
        if( ! count($this->posts) )
        {
            $this->posts = $this->query_posts();
        }
        return $this->posts;
    }

    public function has_posts()
    {
        if( ! count($this->posts) )
        {
            $this->get_all();
        }

        
        if( ! count($this->posts) )
        {
            return false;
        }
        if( $this->index >= count($this->posts) )
        {
            return false;
        }

        return true;

    }

    public function the_post()
    {
        $this->post = $this->posts[ $this->index ];
        $this->index++;
    }

    private function query_posts()
    {
        return get_posts(array( 
            'numberposts' => 3, /* MAX NUMBER OF POSTS TO SHOW */
            'post_type' => 'nd_rotator',
        ));
    }
}

JZ_THEME::register_module('controller', 'rotator', new RotatorController());
?>

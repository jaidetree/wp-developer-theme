<?php
/**
 * Portfolio Work Model
 *
 * Represents a single Portfolio Work Item. Handles drawing the HTML
 * the description, etc...
 *
 * Can be used anywhere within a wordpress template.
 */
class PortfolioWork
{
    private $post;
    private $cats;
    private $class;
    private $url;
    private $link;
    private $id;
    public function __construct($item)
    {
        if( is_int($item) )
        {
            $this->post = $this->_get_item( $item );
        }
        elseif( is_object($item) )
        {
            $this->post = $item;
        }

        $this->id = $this->post->ID;
        $this->cats = wp_get_post_terms( $this->id, 'nd_portfolio_category' );
        $this->class = $this->type_class();
        $this->url = get_post_meta( $this->id, '_portfolio-meta-url', true);
        $this->link = $this->get_link();
        $this->preview_link = $this->get_preview_link();
    }


    public function __toString()
    {
        return $this->_draw();
    }

    public function type_class()
    {
        $class = "";
        $cats = array();
        foreach( $this->cats as $cat )
        {
            $class .= $cat->slug . " ";
            $cats[] = $cat->name;
        }    

        $this->type = implode(",", $cats);

        $class = preg_replace('/\s$/', '', $class);
        return $class;
    }
    public function get_link()
    {
        if( $this->url )
        {
            return $this->url;
        }

        $img = wp_get_attachment_image_src( get_post_thumbnail_id($this->id), 'full' );
        return $img[0];
    }
    public function get_preview_link()
    {
        if( preg_match("/video/", strtolower($this->type) ) )
        {
            return $this->url;
        }

        $img = wp_get_attachment_image_src( get_post_thumbnail_id($this->id), 'full' );
        return $img[0];
    }

    public function get_thumbnail()
    {
        return get_the_post_thumbnail($this->id, 'portfolio');
    }

    public function get_thumbnail_src()
    {
        $img = wp_get_attachment_image_src( get_post_thumbnail_id($this->id), 'portfolio' );
        return $img[0];
    }

    private function _draw()
    {
        $post = $this->post;
        $li = HTML::tag('li');
            $li->class = "item ";
            $li->class .= $this->class;

        $img = $this->get_thumbnail(); 

        $item_info = HTML::tag('div');
            $item_info->class = "item-info";
            $title = HTML::tag('h3', $post->post_title);
            $sub_title = HTML::tag('h4', $this->type );
            $item_info->insert(array( $title, $sub_title ));

        $item_overlay = HTML::tag('div');
            $item_overlay->class = "item-info-overlay";
            $content = HTML::tag('p', $post->post_content);
            $link = HTML::tag('a', 'details');
                $link->class = "view";
                $link->href = $this->link;
            $preview = HTML::tag('a', 'preview');
                $preview->class = "preview";
                $preview->rel = "prettyPhoto[" . $this->cats[0]->slug . "]";
                $preview->href = $this->preview_link;
            $item_overlay->insert( array( $content, $link, $preview ) );

        $li->insert( array( $img, $item_info, $item_overlay) );

        return $li->html();

    }

    private function _get_item($post_id)
    {
        return get_post($post_id);
    }

    public function the_class()
    {
        echo $this->type_class();
    }
    public function the_title()
    {
        echo $this->post->post_title;
    }
    public function the_thumbnail()
    {
        echo  $this->get_thumbnail_src();
    }
    public function the_cats()
    {
        echo $this->type;
    }

    public function the_content()
    {
        echo $this->post->post_content;
    }
    public function the_link()
    {
        echo $this->get_link();
    }
    public function the_preview_link()
    {
        echo $this->get_preview_link();
    }
    public function the_pp_rel()
    {
        echo $this->cats[0]->slug;
    }


}

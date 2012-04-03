<?php
class PortfolioController
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
            'numberposts' => 12, /* MAX NUMBER OF POSTS TO SHOW */
            'post_type' => 'nd_portfolio'
        ));
    }

    public function the_category_links($id=false)
    {
        $terms = get_terms('nd_portfolio_category', 'orderby=id&hide_empty=1');
        $ul = HTML::tag('ul');
        if( $id )
        {
            $ul->id = $id;
        }
        $li = HTML::tag('li');
            $li->class = "current";
            $a = HTML::tag('a', 'All');
            $a->attributes = array(
                'data-filter' => '*',
                'href' => '#!/portfolio/'
            );
            $li->insert($a);
            $ul->insert($li);
        foreach( $terms as $index=>$term )
        {
            $li = HTML::tag('li');
                $a = HTML::tag('a', $term->name);
                $a->attributes = array(
                    'data-filter' => '.' . $term->slug,
                    'href' => '#!/portfolio/' . $term->slug
                );
                $li->insert($a);
                $ul->insert($li);
        }
        echo $ul->html();
    }
}

JZ_THEME::register_module('controller', 'portfolio', new PortfolioController());
?>

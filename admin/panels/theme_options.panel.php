<?php
class Theme_Options extends AdminPanel
{
    function __construct()
    {
        parent::init();
    }
    protected function set_fields()
    {
        $this->sections()->social_media = new SettingsSection('Social Media', 'general');
        $section = &$this->sections()->social_media->fields();
        $section->twitter = new CharField('Twitter URL');
        $section->facebook = new CharField('Facebook URL');
        $section->dribbble = new CharField('Dribbble URL');
        $section->vimeo = new CharField('Vimeo URL');
        $section->etsy = new CharField('Etsy URL');
        $section->socialmedia = new SocialMediaField('Social Media', 'social_media');
    }
    private function social_media_xml()
    {
        $json = json_encode(array(
            array(
                'name' => 'Twitter',
                'url' => 'http://twitter.com/#!/NotDesign',
                'icon' => get_stylesheet_uri() . '/images/socialize-twitter.png',
            )
        ));

        $json = str_replace( "{", "{\n", $json);
        $json = str_replace( "}", "\n}", $json);
        $json = str_replace( ",", ",\n", $json);
        $json = str_replace( "\/", "/", $json);

        return $json;
    }
    protected function panel_name()
    {
        return 'jz_options';
    }
    protected function db_name()
    {
        return 'jz_theme_options';
    }
    protected function slug()
    {
        return 'theme_options';
    }
    protected function sanitize_twitter($twitter)
    {
        return $twitter;
    }
    protected function sanitize_data($data)
    {
        return $data;
    } 
}

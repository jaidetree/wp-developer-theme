<?php

class JZ_THEME
{
    private static $instance;
    private static $modules = array();
    private $loaded_modules = array();


    public static function init()
    {
        if(! isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
            self::$instance->start();
        }
        return self::$instance;
    }

    public static function i()
    {
        return self::init();
    }

    private function __construct()
    {
        define( 'THEME_LIB_DIR', get_stylesheet_directory() . '/lib/' );
    }

    public function start()
    {
        JZ_THEME::init()->load_directory( dirname(__FILE__) . '/..' );

        if( is_admin() )
        {
            JZ_THEME::init()->load_directory( THEME_LIB_DIR . 'admin', '.class.php' );
        }
    }

    public function load_directory($dir_name, $filter = ".php", $callback = false)
    {
        $dir = dir($dir_name);

        while($file = $dir->read())
        {
            $path = $dir_name . '/' . $file;

            if( substr($file, 0, 1) == "." )
            {
                continue;
            }
            elseif( $file == basename( __FILE__ ) )
            {
                continue;
            }
            elseif(is_dir($path))
            {
                $this->load_directory( $path, $filter );
            }
            elseif(preg_match('#'.preg_quote($filter).'$#i', $path))
            {
                include $path;
                $this->loaded_modules[] = $path;
                if( $callback )
                {
                    call_user_func($callback, $path);
                }
            } 
        }
        $dir->close();
    }

    public function list_modules()
    {
        echo "<pre>";
        print_r( $this->loaded_modules );
        echo "</pre>";
    }

    public static function module($name, $type="general")
    {
        return self::$modules[$type][$name];
    }

    public static function controller($name)
    {
        return self::$modules['controller'][$name];
    }

    public static function register_module($type, $name, $instance)
    {
        return self::$modules[$type][$name] = &$instance;
    }
}

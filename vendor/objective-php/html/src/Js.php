<?php
    /**
     * Created by PhpStorm.
     * User: gauthier
     * Date: 13/11/2015
     * Time: 10:55
     */
    
    namespace ObjectivePHP\Html;
    
    
    use ObjectivePHP\Html\Tag\Tag;

    class Js
    {

        static protected $files = [];


        public static function embed($src, $attributes = [])
        {
            self::$files[$src] = $attributes;
        }

        public static function dump()
        {
            echo '<!-- Embedded JavaScript files -->' . PHP_EOL;
            foreach(self::$files as $src => $attributes)
            {
                echo PHP_EOL;
                Tag::factory('script')
                    ->addAttribute('src', $src)
                    ->addAttribute('type', 'application/javascript')
                    ->addAttributes($attributes)
                    ->dump();
                Tag::factory('script')->close();
                echo PHP_EOL;
            }
            echo '<!-- End of embedded JavaScript files -->' . PHP_EOL;
        }

    }
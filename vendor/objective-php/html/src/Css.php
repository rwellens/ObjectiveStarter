<?php

    namespace ObjectivePHP\Html;


    use ObjectivePHP\Html\Tag\Tag;

    class Css
    {

        static protected $files = [];


        public static function embed($src, $attributes = [])
        {
            self::$files[$src] = $attributes;
        }

        public static function dump()
        {
            echo '<!-- Embedded CSS files -->' . PHP_EOL;
            foreach (self::$files as $href => $attributes)
            {
                echo PHP_EOL;
                Tag::factory('link')
                   ->addAttribute('href', $href)
                   ->addAttribute('type', 'text/css')
                   ->addAttribute('rel', 'stylesheet')
                   ->addAttributes($attributes)
                   ->dump()
                ;
                Tag::factory('link')->close();
                echo PHP_EOL;
            }
            echo '<!-- End of embedded CSS files -->' . PHP_EOL;
        }

    }
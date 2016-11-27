<?php

    namespace ObjectivePHP\Html\Markdown;


        /**
         * Class Md
         *
         * @package ObjectivePHP\Html\Markdown
         */
    /**
     * Class Md
     *
     * @package ObjectivePHP\Html\Markdown
     */
    class Md
    {
        /**
         * @var
         */
        protected $content;

        protected $dumped = false;

        /**
         * @param null $markdown
         */
        public function __construct($markdown = null)
        {
            if (!is_null($markdown)) $this->content = \Parsedown::instance()->text($markdown);
        }

        /**
         * @param $markdown
         *
         * @return Md
         */
        public static function parse($markdown)
        {
            return new self($markdown);
        }

        /**
         * Start markdown buffering markdown
         */
        public static function begin()
        {
            ob_start();
        }

        /**
         * Stop markdown buffering, parse it and display it
         */
        public static function end()
        {
            echo self::parse(ob_get_clean());
        }

        /**
         * If the tag has not been explicitly dumped,
         * do it automatically on destruction
         */
        public function __destruct()
        {
            if (!$this->dumped)
            {
                echo $this->__toString();
            }
        }

        /**
         * @return mixed
         */
        public function __toString()
        {
            $this->dumped = true;
            return $this->content;
        }
    }
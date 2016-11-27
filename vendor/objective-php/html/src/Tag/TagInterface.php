<?php
    
    namespace ObjectivePHP\Html\Tag;

    use ObjectivePHP\Primitives\Merger\MergePolicy;

    /**
     * Interface TagInterface
     *
     * @package ObjectivePHP\Html\Tag
     */
    interface TagInterface extends \ArrayAccess
    {

        /**
         * @param     $attribute
         * @param     $value
         * @param int $mergePolicy
         *
         * @return mixed
         */
        public function addAttribute($attribute, $value, $mergePolicy = MergePolicy::REPLACE);

        /**
         * @param ...$attribute
         *
         * @return mixed
         */
        public function removeAttribute(...$attribute);

        /**
         * @param ...$class
         *
         * @return mixed
         */
        public function addClass(...$class);

        /**
         * @param ...$class
         *
         * @return mixed
         */
        public function removeClass(...$class);

        /**
         * @param $tag
         *
         * @return mixed
         */
        public function setTag($tag);

        /**
         * @return mixed
         */
        public function getTag();

        /**
         * @param ...$content
         *
         * @return mixed
         */
        public function append(...$content);

        /**
         * @return mixed
         */
        public function clearContent();

        /**
         * @return mixed
         */
        public function getContent();

        /**
         * @return mixed
         */
        public function __toString();

    }
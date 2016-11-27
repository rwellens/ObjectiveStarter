<?php

    namespace ObjectivePHP\Html\Tag\Input;
    
    
    use ObjectivePHP\Primitives\Collection\Collection;

    class Select extends Input
    {
        /**
         * Select constructor.
         *
         * @param null $options
         */
        public function __construct($options = null)
        {
            parent::__construct(null);

            // force content to be Option only collection
            $this->getContent()->restrictTo(Option::class);

            if ($options)
            {
                $this->addOptions(Collection::cast($options));
            }
        }

        /**
         * @param $options
         *
         * @return $this
         * @throws \ObjectivePHP\Primitives\Exception
         */
        public function addOptions($options)
        {
            Collection::cast($options)->each(
                function ($value, $key)
                {
                    $this->addOption($key, $value);
                }
            )
            ;

            return $this;
        }

        /**
         * @param      $value
         * @param null $label
         *
         * @return $this
         */
        public function addOption($value, $label = null)
        {

            if ($value instanceof Option)
            {
                $this->getContent()->append($value);
            }
            else $this->getContent()->append(Option::option($value, $label));

            return $this;
        }

        /**
         * @return Collection
         */
        public function getOptions()
        {
            return $this->getContent();
        }

    }
<?php

    namespace ObjectivePHP\Html\Tag\Input;
    
    
    use ObjectivePHP\Html\Message\MessageStack;
    use ObjectivePHP\Html\Tag\Tag;
    use ObjectivePHP\Primitives\Collection\Collection;
    use ObjectivePHP\Primitives\Merger\MergePolicy;

    class Input extends Tag
    {


        /**
         * Data for default values
         *
         * @var Collection
         */
        protected static $data;

        /**
         * @var string
         */
        protected static $dateDefaultFormat = 'd/m/Y';

        /**
         * @var MessageStack
         */
        protected static $errors;

        /**
         * @var string
         */
        protected $tag = 'input';

        /**
         * @var mixed default value if no value explicitly assigned or found in self::$data
         */
        protected $default;

        public function __construct($content = null)
        {
            parent::__construct($content);

            // overrides default renderer
            $this->setRenderer(new InputTagRenderer());
        }

        public function addAttribute($attribute, $value, $mergePolicy = MergePolicy::REPLACE)
        {
            parent::addAttribute($attribute, $value, $mergePolicy);

            if(strtolower($attribute) == 'id' && !$this->getAttribute('name'))
            {
                parent::addAttribute('name', $value);
            }

            return $this;
        }


        /**
         * @param $name
         * @param ...$classes
         *
         * @return $this
         */
        public static function text($id, ...$classes)
        {
            $input = static::factory('input', null, ...$classes);

            $input->addAttributes(['type' => 'text', 'id' => $id]);

            return self::decorate($input);
        }

        /**
         * @param $name
         * @param ...$classes
         *
         * @return $this
         */
        public static function number($id, ...$classes)
        {
            $input = static::factory('input', null, ...$classes);

            $input->addAttributes(['type' => 'number', 'id' => $id]);

            return self::decorate($input);
        }

        /**
         * @param $name
         * @param ...$classes
         *
         * @return $this
         */
        public static function hidden($id, $value = '')
        {
            $input = static::factory('input');

            $input->addAttributes(['type' => 'hidden', 'id' => $id, 'value' => $value]);

            return $input;
        }

        /**
         * @param $name
         * @param ...$classes
         *
         * @return $this
         */
        public static function password($id, ...$classes)
        {
            $input = static::factory('input', null, ...$classes);

            $input->addAttributes(['type' => 'password', 'id' => $id]);

            return self::decorate($input);
        }

        /**
         * @param $name
         * @param ...$classes
         *
         * @return $this
         */
        public static function date($id, ...$classes)
        {
            $input = static::factory('input', null, ...$classes);

            $input->addAttributes(['type' => 'date', 'id' => $id]);

            return self::decorate($input);
        }

        /**
         * @param $name
         * @param ...$classes
         *
         * @return $this
         */
        public static function textarea($id, ...$classes)
        {
            $input = static::factory('textarea', null, ...$classes);
            $input->alwaysClose();
            $input->addAttributes(['id' => $id]);

            return self::decorate($input);
        }

        /**
         * @param $name
         * @param ...$classes
         *
         * @return Select
         */
        public static function select($id, $options = null, ...$classes)
        {
            $input = Select::factory('select', null, ...$classes);
            $input->alwaysClose();
            $input->addAttributes(['id' => $id]);
            if ($options) $input->addOptions($options);

            return self::decorate($input);
        }

        public static function option($value, $label = null)
        {

            if (is_null($label))
            {
                $label = $value;
                $value = null;
            }

            $option = Option::factory('option', $label);

            if ($value)
            {
                $option->addAttribute('value', $value);
            }

            return self::decorate($option);
        }

        /**
         * @param $name
         * @param ...$classes
         *
         * @return $this
         */
        public static function checkbox($id, $value = "1", ...$classes)
        {
            $input = static::factory('input', null, ...$classes);

            $input->addAttributes(['type' => 'checkbox', 'id' => $id, 'value' => $value]);

            return self::decorate($input);
        }

        /**
         * @param $name
         * @param ...$classes
         *
         * @return $this
         */
        public static function radio($id, $value, ...$classes)
        {
            $input = static::factory('input', null, ...$classes);

            $input->addAttributes(['type' => 'radio', 'id' => $id, 'value' => $value]);

            return self::decorate($input);
        }


        public static function submit($label, ...$classes)
        {
            $button = static::factory('button', null, ...$classes);
            $button->addAttribute('type', 'submit');
            $button->getContent()->append($label);

            return self::decorate($button);
        }

        /**
         * Value attribute shortcut
         *
         * @param null $value
         */
        public function value($value = null)
        {
            if (!is_null($value))
            {
                // TODO sanitize value
                return $this->addAttribute('value', $value);
            }

            return $this->getAttribute('value');
        }

        /**
         * Value attribute shortcut
         *
         * @param null $text
         */
        public function placeholder($text = null)
        {
            if (!is_null($text))
            {
                // TODO sanitize value
                return $this->addAttribute('placeholder', $text);
            }

            return $this->getAttribute('value');
        }


        public static function errors($input, callable $renderer = null)
        {
            if(!$messages = self::$errors)
            {
                // no messages at all
                return;
            }

            if(!$messages->has($input))
            {
                // no message for this input
                return;
            }

            // render errors using external renderer
            if($renderer)
            {
                return $renderer($input, $messages->get($input), $messages);
            }

            $errors = Tag::ul()->addClass('form-field-error');
            foreach($messages->get($input) as $message)
            {
                $errors->append(Tag::li($message, 'message-' . $message->getType()));
            }

            return $errors;

        }

        /**
         * @throws \ObjectivePHP\Html\Exception
         * @throws \ObjectivePHP\Primitives\Exception
         */
        public function assignDefaultValue()
        {
            $name = $this->getAttribute('name');

            // filter array notation
            if (substr($name, -2) == '[]') $name = substr($name, 0, -2);

            $injectedValues = $this->getData();
            $value = $injectedValues ? $injectedValues->get($name) : null;
            $value =  $value ?: $this->defaultValue();

            if(!$value) return;

            switch ($this->getTag())
            {


                case 'input':

                    switch ($this->getAttribute('type'))
                    {

                        case 'password':
                            // do not restore password value
                            break;

                        case 'checkbox':
                            if (!$this->getAttribute('checked'))
                            {
                                if (is_scalar($value) || in_array($this->getAttribute('value'), $value))
                                {
                                    $this->addAttribute('checked', true);
                                }
                            }
                            break;
                        case 'radio':
                            if (!$this->getAttribute('checked'))
                            {
                                $this->addAttribute('checked', true);
                            }
                            break;

                        default:
                            // handle default value
                            if (!$this->getAttribute('value'))
                            {
                                if ($value instanceof \DateTime)
                                {
                                    $value = $value->format(self::getDateDefaultFormat());
                                }
                                $this->addAttribute('value', $value);
                            }
                            break;
                    }
                    break;

                case 'select':
                        $value = Collection::cast($value);
                        $this->getContent()->each(function (Input $option) use ($value)
                        {
                            $optionValue = $option->getAttribute('value') ?: $option->getContent()->join('');
                            if ($value->contains($optionValue))
                            {
                                $option->addAttribute('selected', true);
                            }
                        })
                        ;
                    break;
            }
        }

        /**
         * @return Collection
         */
        public static function getData()
        {
            return self::$data;
        }

        /**
         * @param mixed $data
         */
        public static function setData($data)
        {
            self::$data = Collection::cast($data);
        }

        /**
         * @param null $value
         *
         * @return $this|mixed
         */
        public function defaultValue($value = null)
        {
            if(!is_null($value))
            {
                $this->default = $value;

                return $this;
            }

            return $this->default;
        }

        /**
         * @return MessageStack
         */
        public static function getErrors()
        {
            return self::$errors;
        }

        /**
         * @param MessageStack $errors
         */
        public static function setErrors(Collection $errors)
        {
            // check errors content
            $errors->restrictTo(MessageStack::class);
            self::$errors = $errors;
        }

        /**
         * @return string
         */
        public static function getDateDefaultFormat()
        {
            return self::$dateDefaultFormat;
        }

        /**
         * @param string $dateDefaultFormat
         */
        public static function setDateDefaultFormat($dateDefaultFormat)
        {
            self::$dateDefaultFormat = $dateDefaultFormat;
        }

    }

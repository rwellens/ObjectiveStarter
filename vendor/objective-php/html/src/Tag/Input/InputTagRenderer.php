<?php

    namespace ObjectivePHP\Html\Tag\Input;
    
    
    use ObjectivePHP\Html\Exception;
    use ObjectivePHP\Html\Tag\Renderer\DefaultTagRenderer;
    use ObjectivePHP\Html\Tag\Renderer\TagRendererInterface;
    use ObjectivePHP\Html\Tag\Tag;

    class InputTagRenderer implements TagRendererInterface
    {
        public function render(Tag $tag)
        {
            if(!$tag instanceof Input)
            {
                throw new Exception(__CLASS__ . ' only renders Tag objects that instances of ' . Input::class);
            }

            $tag->assignDefaultValue();

            $tagRenderer = new DefaultTagRenderer();

            return $tagRenderer->render($tag);

        }
    }
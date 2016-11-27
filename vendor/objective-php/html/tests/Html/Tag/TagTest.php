<?php
    
    namespace Tests\ObjectivePHP\Html\Tag;

    use ObjectivePHP\Html\Tag\Tag;
    use ObjectivePHP\Primitives\Collection\Collection;
    use ObjectivePHP\Primitives\Merger\MergePolicy;

    class TagTest extends \PHPUnit_Framework_TestCase
    {
        public function testFactoryAndTags()
        {

            $tag = Tag::div('content', 'first', 'second');
            $tag->disableAutoDump();

            $tag['class'][] = 'third';

            $this->assertInstanceOf(Tag::class, $tag);
            $this->assertEquals('div', $tag->getTag());
            $this->assertEquals(new Collection(['first', 'second', 'third']), $tag->getAttribute('class'));

            $tag['class'] = ['fourth', 'fifth sixth'];

            $this->assertEquals(new Collection(['fourth', 'fifth', 'sixth']), $tag->getAttribute('class'));

            $tag->removeClass('fifth');
            $this->assertEquals(new Collection(['fourth', 'sixth']), $tag->getAttribute('class'));

        }

        public function testToString()
        {

            $tag = Tag::div('test content', 'first second');
            $tag->disableAutoDump();
            $tag->append('more content');

            $span = Tag::span('nested tag');
            $tag->append($span);
            $this->assertEquals('<div class="first second">test content more content <span>nested tag</span></div>', (string) $tag);
        }

        public function testAddAttribute()
        {
            $tag = new Tag();
            $tag->disableAutoDump();
            $tag->addAttribute('test', 'value');
            $this->assertEquals('value', $tag->getAttribute('test'));
            $this->assertEquals(['test' => 'value', 'class' => []], $tag->getAttributes()->toArray());

            $tag->addAttributes(['test2' => 'value2', 'test' => 'value3']);

            $this->assertEquals('value3', $tag->getAttribute('test'));
            $this->assertEquals(['test' => 'value3', 'class' => [], 'test2' => 'value2'], $tag->getAttributes()->toArray());

        }

        public function testAttributeMergePolicies()
        {
            $tag = new Tag();
            $tag->disableAutoDump();
            $tag->addAttribute('test', 'value');
            $tag->addAttribute('test', 'value2', MergePolicy::COMBINE);

            $this->assertEquals(['value', 'value2'], $tag->getAttribute('test')->toArray());

            $tag->addAttribute('test2', 'value3', MergePolicy::COMBINE);
            $this->assertEquals('value3', $tag->getAttribute('test2'));

        }
    }
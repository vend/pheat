<?php

namespace Pheat;

use Pheat\Test\Test;

class ContextTest extends Test
{
    const SUT = 'Pheat\Context';

    public function testArrayAccess()
    {
        $context = $this->getSut(['foo' => 'bar']);
        $context['baz'] = false;

        $this->assertTrue(isset($context['foo']));
        $this->assertEquals('bar', $context['foo']);

        $this->assertFalse($context['baz']);
        unset($context['baz']);

        $this->assertNull($context['baz']);
        $this->assertFalse(isset($context['baz']));
    }

    public function testReadContract()
    {
        $context = $this->getSut(['foo' => 'bar']);

        $this->assertEquals('bar', $context->get('foo'));
        $this->assertEquals('baz', $context->get('missing', 'baz'));
    }

    public function testWriteContract()
    {
        $context = $this->getSut(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $context->getAll());

        $context->set('foo', 'norf');
        $this->assertEquals(['foo' => 'norf'], $context->getAll());

        $context->setAll(['baz' => 'qux']);
        $this->assertEquals(['baz' => 'qux'], $context->getAll());
    }

    public function testIterable()
    {
        $context = $this->getSut(['foo' => 'bar']);

        foreach ($context as $name => $value) {
            $this->assertEquals('foo', $name);
            $this->assertEquals('bar', $value);
        }
    }
}

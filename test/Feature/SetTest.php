<?php

namespace Pheat\Feature;

use Pheat\Test\Test;

class SetTest extends Test
{
    const SUT = 'Pheat\\Feature\\Set';

    public function testGetAll()
    {
        $a = $this->getMockProvider('a');
        $b = $this->getMockProvider('b');

        $foo_a = $this->getMockFeature('foo', true, $a);
        $bar_a = $this->getMockFeature('bar', true, $a);
        $foo_b = $this->getMockFeature('foo', null, $b);
        $bar_b = $this->getMockFeature('bar', false, $b);

        $set = $this->getSut();

        $set->mergeFeatures([
            $foo_a,
            $bar_a
        ]);
        $set->mergeFeatures([
            $foo_b,
            $bar_b
        ]);

        $this->assertEquals(['foo' => $foo_a, 'bar' => $bar_b], $set->getAllCanonical());
        $this->assertEquals([
            'foo' => ['a' => $foo_a, 'b' => $foo_b],
            'bar' => ['a' => $bar_a, 'b' => $bar_b]
        ], $set->getAll());
    }
}

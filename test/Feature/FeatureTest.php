<?php

namespace Pheat\Feature;

use Pheat\Status;
use Pheat\Test\Test;

class FeatureTest extends Test
{
    const SUT = 'Pheat\Feature\Feature';

    protected $ordering = [
        [Status::ACTIVE, Status::UNKNOWN],
        [Status::INACTIVE, Status::UNKNOWN],
        [Status::INACTIVE, Status::ACTIVE]
    ];

    public function testResolution()
    {
        $sut = static::SUT;

        foreach ($this->ordering as $case) {
            list($expected, $other) = $case; // TODO PHP5.5

            $previous = new $sut('foo', $expected, $this->getMockProvider('foo'));
            $new      = new $sut('foo', $other,    $this->getMockProvider('bar'));

            $value = $new->resolve($previous);

            $this->assertEquals(
                $previous,
                $new->resolve($previous),
                sprintf('%s status beats %s status', Status::$messages[$expected], Status::$messages[$other])
            );

            // Commutative
            $previous = new $sut('foo', $other,    $this->getMockProvider('foo'));
            $new      = new $sut('foo', $expected, $this->getMockProvider('bar'));

            $this->assertEquals(
                $new,
                $new->resolve($previous),
                sprintf('%s status beats %s status, commutative', Status::$messages[$expected], Status::$messages[$other])
            );
        }
    }
}

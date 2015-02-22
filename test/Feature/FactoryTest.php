<?php

namespace Pheat\Feature;

use Pheat\Test\Test;

class FactoryTest extends Test
{
    const SUT = Factory::class;

    public function testToConfiguration()
    {
        $factory = $this->getSut();

        $feature = $this->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $value = [
            'enabled' => true,
            'ratio' => 0.1
        ];

        $feature->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($value));

        $configuration = $factory->toConfiguration($feature);
        $this->assertEquals($value, $configuration);
    }
}

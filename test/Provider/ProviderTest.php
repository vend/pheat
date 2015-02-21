<?php

namespace Pheat\Provider;

use Pheat\Context;
use Pheat\Feature\Feature;
use Pheat\Feature\RatioFeature;
use Pheat\Feature\VariantFeature;
use Pheat\Test\Test;
use ReflectionClass;

class ProviderTest extends Test
{
    const SUT = 'Pheat\Provider\Provider';

    protected $config = [
        'something' => [
            'enabled' => true
        ],
        'something_else' => [
            'enabled' => false,
            'ratio'   => 0.9,
            'vary'    => 'tenant'
        ],
        'another' => [
            'enabled' => null,
            'vary'    => 'tenant',
            'variants' => [
                'foo' => 0.8,
                'bar' => 0.1
            ]
        ]
    ];

    public function testFromConfig()
    {
        $mock = $this->getMockBuilder(self::SUT)
            ->getMockForAbstractClass();

        $mock->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($this->config));

        $features = $mock->getFeatures($this->getMockContext());

        $this->assertInternalType('array', $features);
        $this->assertCount(3, $features);

        $this->assertArrayHasKey('something', $features);
        $this->assertArrayHasKey('something_else', $features);
        $this->assertArrayHasKey('another', $features);

        $this->assertInstanceOf(Feature::class, $features['something']);
        $this->assertInstanceOf(RatioFeature::class, $features['something_else']);
        $this->assertInstanceOf(VariantFeature::class, $features['another']);
    }
}

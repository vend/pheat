<?php

namespace Pheat\Feature;

use Pheat\Test\Test;

class SetTest extends Test
{
    const SUT = Set::class;

    protected $features = [];
    protected $providers = [];

    /**
     * @var Set
     */
    protected $default;

    public function setUp()
    {
        parent::setUp();

        $this->providers['a'] = $this->getMockProvider('a');
        $this->providers['b'] = $this->getMockProvider('b');

        $this->features['foo_a'] = $this->getMockFeature('foo', true, $this->providers['a']);
        $this->features['bar_a'] = $this->getMockFeature('bar', true, $this->providers['a']);
        $this->features['foo_b'] = $this->getMockFeature('foo', null, $this->providers['b']);
        $this->features['bar_b'] = $this->getMockFeature('bar', false, $this->providers['b']);

        $this->default = $this->getSut();

        $this->default->mergeFeatures([
            $this->features['foo_a'],
            $this->features['bar_a']
        ]);

        $this->default->mergeFeatures([
            $this->features['foo_b'],
            $this->features['bar_b']
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->default = null;
    }

    public function testGetFeatureFromProvider()
    {
        $foo_a = $this->default->getFeatureFromProviderName('foo', 'a');
        $this->assertEquals($this->features['foo_a'], $foo_a);

        $default = $this->default->getFeatureFromProviderName('foo', 'no-such', 'default');
        $this->assertEquals('default', $default);

        $default = $this->default->getFeatureFromProviderName('no-such', 'a', 'default');
        $this->assertEquals('default', $default);

        $foo_b = $this->default->getFeatureFromProvider('foo', $this->providers['b']);
        $this->assertEquals($this->features['foo_b'], $foo_b);

        $default = $this->default->getFeatureFromProvider('no-such', $this->providers['b'], 'default');
        $this->assertEquals('default', $default);
    }

    public function testGetAll()
    {
        $this->assertEquals([
            'foo' => $this->features['foo_a'],
            'bar' => $this->features['bar_b']
        ], $this->default->getAllCanonical());

        $this->assertEquals([
            'foo' => ['a' => $this->features['foo_a'], 'b' => $this->features['foo_b']],
            'bar' => ['a' => $this->features['bar_a'], 'b' => $this->features['bar_b']]
        ], $this->default->getAll());
    }
}

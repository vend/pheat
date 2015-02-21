<?php

namespace Pheat\Feature;
use Pheat\Provider\ProviderInterface;
use RuntimeException;

/**
 * Careful that you always use the 'vary' option to ensure tests are deterministic
 */
class RatioFeatureTest extends AbstractFeatureTest
{
    const SUT = RatioFeature::class;

    /**
     * Values arrived at by experimentation
     */
    public function testRatio()
    {
        $feature = $this->getRatioSut(0.5, 'nope');
        $this->assertTrue($feature->getStatus());

        $feature = $this->getRatioSut(0.5, 'foo');
        $this->assertFalse($feature->getStatus());
    }

    public function testFullRatio()
    {
        $feature = $this->getRatioSut(1.0, 'something');
        $this->assertTrue($feature->getStatus());
    }

    public function testEmptyRatio()
    {
        $feature = $this->getRatioSut(0.0, 'something');
        $this->assertFalse($feature->getStatus());
    }

    protected function getRatioSut($ratio, $vary)
    {
        $feature = $this->getDefaultSut(true, $this->getMockProvider('foo'));

        $feature->configure([
            'vary'  => 'foo',
            'ratio' => $ratio
        ]);

        $feature->context($this->getMockContext([
            'foo' => $vary
        ]));

        return $feature;
    }

    /**
     * Returns a default configuration for the feature
     *
     * Subclasses should try to exercise the configuration, but it should cause the feature
     * to follow normal merge-down semantics. (e.g. for a RatioFeature, the ratio should be 1.0)
     *
     * @param boolean|null $status
     * @return array <string,mixed>
     */
    protected function getDefaultConfiguration($status)
    {
        if ($status === null) {
            return [
                'enabled' => null
            ];
        } else if ($status === true) {
            return [
                'enabled' => true,
                'vary'    => 'bar',
                'ratio'   => 1.0
            ];
        } elseif ($status === false) {
            return [
                'enabled' => false,
                'vary'    => 'bar',
                'ratio'   => 0.0
            ];
        }

        throw new RuntimeException('Unexpected status');
    }
}

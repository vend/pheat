<?php

namespace Pheat\Feature;

use Pheat\ContextInterface;
use Pheat\Provider\ProviderInterface;
use Pheat\Status;
use Pheat\Test\Test;

abstract class AbstractFeatureTest extends Test
{
    protected $ordering = [
        [Status::ACTIVE, Status::UNKNOWN],
        [Status::INACTIVE, Status::UNKNOWN],
        [Status::INACTIVE, Status::ACTIVE]
    ];

    public function testDefaultResolution()
    {
        foreach ($this->ordering as $case) {
            list($expected, $other) = $case; // TODO PHP5.5

            $previous = $this->getDefaultSut($expected, $this->getMockProvider('foo'));
            $new      = $this->getDefaultSut($other,    $this->getMockProvider('bar'));

            $value = $new->resolve($previous);

            $this->assertEquals(
                $previous,
                $value,
                sprintf('%s status beats %s status', Status::$messages[$expected], Status::$messages[$other])
            );

            // Commutative
            $previous = $this->getDefaultSut($other,    $this->getMockProvider('foo'));
            $new      = $this->getDefaultSut($expected, $this->getMockProvider('bar'));

            $value = $new->resolve($previous);

            $this->assertEquals(
                $new,
                $value,
                sprintf('%s status beats %s status, commutative', Status::$messages[$expected], Status::$messages[$other])
            );
        }
    }

    public function testReflexiveConfiguration()
    {
        $configuration = $this->getDefaultConfiguration(true);

        $feature = $this->getDefaultSut(true, $this->getMockProvider('foo'));
        $feature->configure($configuration);

        $processed = $feature->getConfiguration();

        $this->assertEquals($configuration, $processed);
    }

    /**
     * @param boolean|null $status
     * @param ProviderInterface $provider
     * @return FeatureInterface
     */
    protected function getDefaultSut($status, ProviderInterface $provider)
    {
        /** @var FeatureInterface $feature */
        $feature = parent::getSut('foo', $status, $provider);
        $feature->configure($this->getDefaultConfiguration($status));
        $feature->context($this->getDefaultContext());

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
    abstract protected function getDefaultConfiguration($status);

    /**
     * @return ContextInterface
     */
    protected function getDefaultContext()
    {
        return $this->getMockContext(['bar' => 'something']);
    }
}

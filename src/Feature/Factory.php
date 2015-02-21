<?php

namespace Pheat\Feature;

use Pheat\Provider\ProviderInterface;

class Factory
{
    protected $types = [
        'variants' => VariantFeature::class,
        'ratio'    => RatioFeature::class
    ];

    protected $default = Feature::class;

    /**
     * @param array             $configuration
     * @param ProviderInterface $provider
     * @return FeatureInterface[]
     */
    public function fromConfiguration(array $configuration, ProviderInterface $provider)
    {
        $features  = [];

        foreach ($configuration as $name => $fragment) {
            if ($feature = $this->singleFragment($name, $fragment, $provider)) {
                $features[$name] = $feature;
            }
        }

        return $features;
    }

    public function toConfiguration(FeatureInterface $feature)
    {
        return $feature->getConfiguration();
    }

    /**
     * Creates a single feature instance from a configuration fragment
     *
     * Here, we dispatch configuration fragments to different types feature
     *
     * @param string               $name
     * @param array|int|bool|float $configuration
     * @param ProviderInterface    $provider
     * @return FeatureInterface
     */
    public function singleFragment($name, $configuration, ProviderInterface $provider)
    {
        foreach ($this->types as $type => $class) {
            if (!empty($configuration[$type])) {
                return $this->instance($class, $name, $configuration, $provider);
            }
        }

        return $this->instance($this->default, $name, $configuration, $provider);
    }

    /**
     * Creates a feature instance from a fragment of normalized configuration
     *
     * @param string            $class FeatureInterface implementing class name as a string
     * @param string            $name  Name of the feature
     * @param array             $configuration
     * @param ProviderInterface $provider
     * @return FeatureInterface
     */
    protected function instance($class, $name, array $configuration, ProviderInterface $provider)
    {
        /** @var FeatureInterface $feature */
        $feature = new $class($name, $configuration['enabled'], $provider);
        $feature->configure($configuration);

        return $feature;
    }
}

<?php

namespace Pheat\Provider;

use Pheat\ContextInterface;
use Pheat\Feature\Feature;
use Pheat\Feature\FeatureInterface;
use Pheat\Feature\RatioFeature;
use Pheat\Feature\VariantFeature;

/**
 * A helpful abstract provider that uses a single stored configuration
 *
 * The ProviderInterface requires nothing but a getFeatures() method. This abstract
 * base class further abstracts away support for storing multiple types of features
 * in a single configuration array/map/dict/hash.
 */
abstract class Provider implements ProviderInterface
{
    protected $types = [
        'variants' => VariantFeature::class,
        'ratio'    => RatioFeature::class
    ];

    protected $default = Feature::class;

    /**
     * Gets the stored configuration for all the features for this provider
     *
     * @return array
     */
    abstract protected function getConfiguration();

    /**
     * @param ContextInterface $context
     * @return FeatureInterface[]
     */
    public function getFeatures(ContextInterface $context)
    {
        $features = $this->featuresFromConfiguration($this->getConfiguration());

        foreach ($features as $feature) {
            $feature->context($context);
        }

        return $features;
    }

    /**
     * @param array $configuration
     * @return FeatureInterface[]
     */
    protected function featuresFromConfiguration(array $configuration)
    {
        $features  = [];

        foreach ($configuration as $name => $config) {
            if ($feature = $this->featureFromConfiguration($name, $config)) {
                $features[$name] = $feature;
            }
        }

        return $features;
    }

    /**
     * Creates a single feature instance from a configuration fragment
     *
     * Here, we dispatch configuration fragments to different types feature
     *
     * @param string $name
     * @param array  $config
     * @return FeatureInterface
     */
    protected function featureFromConfiguration($name, array $config)
    {
        foreach ($this->types as $type => $class) {
            if (!empty($config[$type])) {
                return $this->featureInstance($class, $name, $config);
            }
        }

        return $this->featureInstance($this->default, $name, $config);
    }

    /**
     * Creates a feature instance from a fragment of normalized configuration
     *
     * @param string $class FeatureInterface implementing class name as a string
     * @param string $name Name of the feature
     * @param array $config
     * @return FeatureInterface
     */
    protected function featureInstance($class, $name, array $config)
    {
        /** @var FeatureInterface $feature */
        $feature = new $class($name, $config['enabled'], $this);
        $feature->configure($config);

        return $feature;
    }
}

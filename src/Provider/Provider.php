<?php

namespace Pheat\Provider;

use Pheat\ContextInterface;
use Pheat\Feature\FeatureFactory;
use Pheat\Feature\FeatureInterface;

/**
 * A helpful abstract provider that uses a single stored configuration
 *
 * The ProviderInterface requires nothing but a getFeatures() method. This abstract
 * base class further abstracts away support for storing multiple types of features
 * in a single configuration array/map/dict/hash.
 */
abstract class Provider implements ProviderInterface
{
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
        $factory = new FeatureFactory();
        $features = $factory->fromConfiguration($this->getConfiguration(), $this);

        foreach ($features as $feature) {
            $feature->context($context);
        }

        return $features;
    }
}

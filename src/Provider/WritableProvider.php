<?php

namespace Pheat\Provider;

use Pheat\ContextInterface;
use Pheat\Feature\Factory;
use Pheat\Feature\FeatureInterface;

/**
 * Abstract writable provider interface
 *
 * Like the abstract Provider, you don't have to extend this class (only implement
 * the WritableProviderInterface), however, you might like to. This class makes use
 * of the standard feature Factory for standard configuration.
 */
abstract class WritableProvider extends Provider implements WritableProviderInterface
{
    public function setFeature(ContextInterface $context, FeatureInterface $feature)
    {
        $factory = new Factory();
        $this->persistConfiguration($context, $feature->getName(), $factory->toConfiguration($feature));
    }

    abstract protected function persistConfiguration(ContextInterface $context, $name, array $configuration);
}

<?php

namespace Pheat\Feature;

use Pheat\Provider\ProviderInterface;
use RuntimeException;

/**
 * Used as a null object where no information is known about a feature being resolved
 */
class NullFeature implements FeatureInterface
{
    public function __construct($name = null, $status = null, ProviderInterface $provider = null)
    {
    }

    public function getName()
    {
        return null;
    }

    public function getStatus()
    {
        return null;
    }

    public function getProvider()
    {
        return new NullProvider();
    }

    public function resolve(FeatureInterface $previous)
    {
        throw new RuntimeException('Cannot resolve a null feature');
    }
}

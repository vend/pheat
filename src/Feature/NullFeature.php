<?php

namespace Pheat\Feature;

use Pheat\Exception\NullException;
use Pheat\Provider\NullProvider;
use Pheat\Provider\ProviderInterface;

/**
 * The null feature
 *
 * Used as a null object where no information is known about a feature being resolved
 */
class NullFeature extends Feature
{
    private static $instance = null;

    /**
     * Cached as a single instance
     *
     * @return self
     */
    public static function get()
    {
        return empty(self::$instance) ? (self::$instance = new self()) : self::$instance;
    }

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
        throw new NullException('Cannot resolve a null feature');
    }
}

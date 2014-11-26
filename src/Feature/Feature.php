<?php

namespace Pheat\Feature;

use Pheat\Provider\ProviderInterface;
use Pheat\Status;

/**
 * Simple boolean feature implementation
 *
 * You might like to use this default implementation of the feature value object,
 * rather than implement FeatureInterface. This implementation provides simple
 * 'inactive trumps active, unknown ignores the provider' default logic.
 *
 * The feature class is responsible for the merge-down when multiple providers give
 * information about the same feature. The default behaviour with regard to unknown
 * features can be changed by reimplementing the resolve method in a subclass.
 */
class Feature implements FeatureInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool|null See Status class constants for meaning
     */
    protected $status;

    /**
     * @var ProviderInterface
     */
    protected $provider;

    public function __construct($name, $status, ProviderInterface $provider)
    {
        $this->name     = $name;
        $this->status   = $status;
        $this->provider = $provider;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Performs the merge down
     *
     * Will be called with another feature instance that implements the
     * same feature.
     *
     * @param FeatureInterface $previous
     * @return $this|FeatureInterface
     */
    public function resolve(FeatureInterface $previous)
    {
        if ($this->status === Status::INACTIVE && $previous->getStatus() !== Status::INACTIVE) {
            return $this;
        }

        if ($this->status === Status::UNKNOWN || $previous->getStatus() === Status::ACTIVE) {
            return $previous;
        }

        return $this;
    }
}

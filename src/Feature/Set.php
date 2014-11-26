<?php

namespace Pheat\Feature;

use Pheat\Status;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * A set of features
 */
class Set implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var FeatureInterface[]
     */
    protected $features = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * Merges the given features into this set
     *
     * @param FeatureInterface[] $features
     */
    public function mergeFeatures(array $features)
    {
        /** @var FeatureInterface $feature */
        foreach ($features as $feature) {
            $this->mergeFeature($feature);
        }
    }

    public function getFeature($name)
    {
        return isset($this->features[$name]) ? $this->features[$name] : NullFeature::get();
    }

    /**
     * Merges a single feature into the current set
     *
     * @param \Pheat\Feature\FeatureInterface $feature
     */
    protected function mergeFeature(FeatureInterface $feature)
    {
        $name = $feature->getName();

        $was = $this->getFeature($name);
        $new = $feature->resolve($was);

        $this->features[$name] = $new;

        $this->logger->debug('Merging {feature} from {provider}, {was} -> {new}', [
            'feature'  => $name,
            'provider' => $feature->getProvider()->getName(),
            'was'      => Status::$messages[$was->getStatus()],
            'new'      => Status::$messages[$this->features[$name]->getStatus()]
        ]);
    }
}

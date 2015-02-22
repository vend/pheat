<?php

namespace Pheat\Feature;

use Pheat\Provider\NullProvider;
use Pheat\Provider\ProviderInterface;
use Pheat\Status;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * A set of features
 *
 * Used internally by the manager. Records the canonical provider for a feature.
 */
class Set implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var FeatureInterface[]
     */
    protected $canonical = [];

    /**
     * A multidimensional array of all encountered features
     *
     * The first level is indexed by feature name, and the second by provider name.
     *
     * @var array<string,array<string,FeatureInterface>>
     */
    protected $all = [];

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
            $this->storeToAll($feature);
            $this->storeToCanonical($feature);
        }
    }

    /**
     * @param string $name
     * @return \Pheat\Feature\FeatureInterface
     */
    public function getFeature($name)
    {
        return isset($this->canonical[$name]) ? $this->canonical[$name] : new NullFeature($name);
    }

    /**
     * @param string                $name
     * @param ProviderInterface     $provider
     * @param null|FeatureInterface $default
     * @return null|FeatureInterface
     */
    public function getFeatureFromProvider($name, ProviderInterface $provider, FeatureInterface $default = null)
    {
        if ($default === null) {
            $default = new NullFeature($name, null, $provider);
        }

        return $this->getFeatureFromProviderName($name, $provider->getName(), $default);
    }

    /**
     * @param string $name
     * @param string $providerName
     * @param null|FeatureInterface $default
     * @return null|FeatureInterface
     */
    public function getFeatureFromProviderName($name, $providerName, FeatureInterface $default = null)
    {
        if ($default === null) {
            $default = new NullFeature($name, null, new NullProvider($providerName));
        }

        if (!isset($this->all[$name][$providerName])) {
            return $default;
        }

        return $this->all[$name][$providerName];
    }

    /**
     * @return FeatureInterface[]
     */
    public function getAllCanonical()
    {
        return $this->canonical;
    }

    /**
     * @return array<string,array<string,FeatureInterface>>
     */
    public function getAll()
    {
        return $this->all;
    }

    /**
     * Stores the given feature to the array of all encountered features
     *
     * @param FeatureInterface $feature
     */
    protected function storeToAll(FeatureInterface $feature)
    {
        $name = $feature->getName();

        if (!isset($this->all[$name])) {
            $this->all[$name] = [];
        }

        $this->all[$name][$feature->getProvider()->getName()] = $feature;
    }

    /**
     * Conditionally marks a feature instance as canonical for a name
     *
     * @param FeatureInterface $feature
     */
    protected function storeToCanonical(FeatureInterface $feature)
    {
        $name = $feature->getName();
        $was  = $this->getFeature($name);
        $new  = $feature->resolve($was);

        $this->canonical[$name] = $new;

        if ($was !== $new) {
            $this->logger->debug('New canonical feature: {feature} from {provider}, {was} -> {new}', [
                'feature'  => $name,
                'provider' => $feature->getProvider()->getName(),
                'was'      => Status::$messages[$was->getStatus()],
                'new'      => Status::$messages[$this->canonical[$name]->getStatus()]
            ]);
        }
    }
}

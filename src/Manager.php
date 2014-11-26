<?php

namespace Pheat;

use Exception;
use Pheat\Exception\LockedException;
use Pheat\Feature\FeatureInterface;
use Pheat\Feature\NullFeature;
use Pheat\Provider\ProviderInterface;
use Pheat\Status\Status;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * The Pheat Feature Manager
 *
 * This is the main end-user interface for the feature manager. To use it:
 *  - Instantiate and fill-in a Context object
 *  - Instantiate this manager, and give it the context, plus a set of providers
 *  - Call ::resolve('feature_name) to find out whether a feature is active
 *
 * This class caches feature information - if you need to re-check the providers, instantiate a new Manager.
 */
class Manager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array<int,ProviderInterface>
     */
    protected $providers = [];

    /**
     * @var array<string,array<string,Pheat\Feature\FeatureInterface>>
     */
    protected $features = [];

    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * After asking the providers about the features they provide once, the
     * feature manager caches their responses. So, changes in context won't be
     * reflected.
     *
     * @var bool
     */
    protected $locked = false;

    /**
     * @param ContextInterface $context
     * @param array            $providers
     */
    public function __construct(ContextInterface $context, array $providers = [])
    {
        $this->logger  = new NullLogger();
        $this->context = $context;

        // Iterate in order to type-check and ensure ordering
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Adds a feature provider at an index (appends by default)
     *
     * A provider must implement the ProviderInterface, and is added to the end
     * of the (ordered) list of providers by default. (If you're tagging services
     * to add to the manager, make sure you consider priority - if you call addProvider()
     * in an undefined order, things will get very confusing)
     *
     * @param ProviderInterface $provider
     * @param int               $index
     * @throws LockedException
     */
    public function addProvider(ProviderInterface $provider, $index = -1)
    {
        if ($this->locked) {
            throw new LockedException('The feature manager has already asked its providers for features - changes will not be reflected');
        }

        if ($index === -1) {
            array_push($this->providers, $provider);
        } else {
            $this->providers[(int)$index] = $provider;
        }
    }

    /**
     * Resolves the status of a feature
     *
     * Returns whether a feature should be considered enabled as a boolean
     *
     * @param string $name The name of the feature to resolve
     * @return bool|null Whether the feature should be considered active
     */
    public function resolve($name)
    {
        $this->lock();
        $this->resolveAll();

        return $this->returnStatusFromFeature(
            isset($this->features[$name]) ? $this->features[$name] : new NullFeature()
        );
    }

    /**
     * Resolves features from the feature providers
     *
     * Idempotent and memoized to run only once. If you need to resolve again,
     * get a new Manager instance.
     *
     * @return array
     */
    public function resolveAll()
    {
        if ($this->features === null) {
            $this->features = [];

            foreach ($this->providers as $provider) {
                /* @var ProviderInterface $provider */
                try {
                    $features = $provider->getFeatures($this->context);
                } catch (Exception $e) {
                    // If an uncaught exception is thrown from a provider, we assume it answered 'unknown' for all known features.
                    continue;
                }

                foreach ($features as $feature) {
                    $this->resolveFeature($feature);
                }
            }
        }

        return $this->features;
    }

    /**
     * Prevents further changes to the context, now that we're about to freeze
     * the feature manager's responses from the providers.
     */
    protected function lock()
    {
        $this->locked  = true;
        $this->context = clone $this->context;
    }

    protected function returnStatusFromFeature(FeatureInterface $feature)
    {
        $status = $feature->getStatus();

        $this->logger->info('Feature {feature} returned as {status} due to {provider}', [
            'feature'  => $feature->getName(),
            'status'   => Status::$messages[$status],
            'provider' => $feature->getProvider()->getName()
        ]);

        return $status;
    }
}

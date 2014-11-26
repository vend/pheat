<?php

namespace Pheat;

use Exception;
use Pheat\Exception\LockedException;
use Pheat\Feature\FeatureInterface;
use Pheat\Feature\NullFeature;
use Pheat\Provider\ProviderInterface;
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
     * @var array<ProviderInterface>
     */
    protected $providers = [];

    /**
     * @var array<FeatureInterface> Indexed by string
     */
    protected $features = null;

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
    public function __construct(ContextInterface $context = null, array $providers = [])
    {
        $this->logger  = new NullLogger();
        $this->context = $context ?: new Context();

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

        $index = (int)$index;

        if ($index === -1) {
            array_push($this->providers, $provider);
        } else {
            array_splice($this->providers, $index, 0, [$provider]);
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
        $feature = $this->resolveFeature($name);

        if (!$feature) {
            $feature = NullFeature::get();
        }

        return $this->returnStatusFromFeature(
            $feature
        );
    }

    /**
     * Returns the deciding feature (with associated provider)
     *
     * @param string $name
     * @return FeatureInterface
     */
    public function resolveFeature($name)
    {
        $this->lock();
        $this->resolveAll();

        return isset($this->features[$name]) ? $this->features[$name] : NullFeature::get();
    }

    /**
     * Resolves all features from the feature providers
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
                    $this->logger->error('Feature provider {provider} threw exception on status resolution: {exception}', [
                        'provider'  => $provider->getName(),
                        'exception' => $e
                    ]);

                    // If an uncaught exception is thrown from a provider, we assume
                    // it answered 'unknown' for all known features.
                    continue;
                }

                if (!is_array($features)) {
                    $this->logger->error('Feature provider {provider} did not provide an array of features', [
                        'provider' => $provider->getName()
                    ]);
                    continue;
                }

                $this->mergeFeatures($features);
            }
        }

        return $this->features;
    }

    /**
     * Merges the given features into the current feature state
     *
     * @param array<FeatureInterface> $features
     */
    protected function mergeFeatures(array $features)
    {
        /** @var FeatureInterface $feature */
        foreach ($features as $feature) {
            $name = $feature->getName();

            if (!isset($this->features[$name])) {
                $this->features[$name] = NullFeature::get();
            }

            $was = $this->features[$name];
            $this->features[$name] = $feature->resolve($this->features[$name]);

            $this->logger->debug('Merging {feature} from {provider}, {was} -> {new}', [
                'feature'  => $name,
                'provider' => $feature->getProvider()->getName(),
                'was'      => Status::$messages[$was->getStatus()],
                'new'      => Status::$messages[$this->features[$name]->getStatus()]
            ]);
        }
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

    /**
     * Given a feature, resolves a status, reports on it and returns it
     *
     * @param FeatureInterface $feature
     * @return bool|null
     */
    protected function returnStatusFromFeature(FeatureInterface $feature)
    {
        $status = $feature->getStatus();

        $this->logger->info('Feature {feature} returned as {status} due to {provider} provider', [
            'feature'  => $feature->getName(),
            'status'   => Status::$messages[$status],
            'provider' => $feature->getProvider()->getName()
        ]);

        return $status;
    }
}

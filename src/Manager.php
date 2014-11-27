<?php

namespace Pheat;

use Exception;
use Pheat\Exception\LockedException;
use Pheat\Feature\FeatureInterface;
use Pheat\Feature\Set as FeatureSet;
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
     * @var FeatureSet
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
        return $this->returnStatusFromFeature(
            $this->getFeatureSet()->getFeature($name)
        );
    }

    /**
     * @return FeatureSet
     */
    public function getFeatureSet()
    {
        if ($this->features === null) {

            $this->features = $this->createFeatureSet();
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

    /**
     * @return FeatureSet
     */
    protected function createFeatureSet()
    {
        $this->lock();

        $set = new FeatureSet();

        foreach ($this->providers as $provider) {
            $features = $this->validateFeatureArray(
                $this->getFeaturesFromProvider($provider),
                $provider
            );

            $set->mergeFeatures($features);
        }

        return $set;
    }

    /**
     * @param ProviderInterface $provider
     * @return FeatureInterface[]
     */
    protected function getFeaturesFromProvider(ProviderInterface $provider)
    {
        $features = [];

        try {
            $features = $provider->getFeatures($this->context);
        } catch (Exception $e) {
            $this->logger->error('Feature provider {provider} threw exception on status resolution: {exception}', [
                'provider'  => $provider->getName(),
                'exception' => $e
            ]);
        }

        return $features;
    }

    /**
     * Validates that the response from the provider is a set of features
     *
     * @param array                             $features Could be mixed type, validating. Don't type hint this parameter.
     * @param \Pheat\Provider\ProviderInterface $provider
     * @return array
     */
    protected function validateFeatureArray($features, ProviderInterface $provider)
    {
        if (!is_array($features)) {
            $this->logger->error('Feature provider {provider} did not provide an array of features', [
                'provider' => $provider->getName()
            ]);

            return [];
        }

        return $features;
    }
}

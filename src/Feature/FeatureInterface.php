<?php

namespace Pheat\Feature;

use Pheat\ContextInterface;
use Pheat\Provider\ProviderInterface;

/**
 * Interface FeatureInterface
 *
 * @package Pheat\Feature
 */
interface FeatureInterface
{
    /**
     * Constructs a feature
     *
     * Normally, a feature would be constructed by a Provider, and given to the Manager
     *
     * @param string            $name     A unique name for the provider (see getName() for details)
     * @param bool|null         $status   A status value according to the class constants in Status (see getStatus() for details)
     * @param ProviderInterface $provider The provider that gave the status of this feature
     */
    public function __construct($name, $status, ProviderInterface $provider);

    /**
     * Sets the feature as acting under the given context
     *
     * Just before a feature is resolved, the provider is suggested to notify every
     * feature of the context. (This is not a requirement of the ProviderInterface, however,
     * so Providers may choose not to provide one.)
     *
     * @param ContextInterface $context
     * @return void
     */
    public function context(ContextInterface $context);

    /**
     * Configures the feature according to the given configuration
     *
     * Used by the provider to give complex status information, e.g. for variants
     * and ratios.
     *
     * @param array $configuration
     * @return void
     */
    public function configure(array $configuration);

    /**
     * Returns a configuration fragment representing the configuration (but not state) of the feature
     *
     * The configure() and toConfiguration() operations should be reflexive (i.e. after calling configure()
     * with a given configuration, you should be able to obtain an exact copy from toConfiguration())
     *
     * @return array
     */
    public function getConfiguration();

    /**
     * Features must have a unique name
     *
     * For convention's sake, you should probably name your feature using underscore_separated_words. That way, you can
     * later humanize to title, or sentence case, but you get a nice token to use in code. If I, this disembodied piece
     * of code, were you, the programmer, I'd keep your feature names in some relevant class constants. That way,
     * they're easy to test against.
     *
     * @return string
     */
    public function getName();

    /**
     * Features must return a boolean or null status value with the semantics documented in Status
     *
     * @return bool|null
     */
    public function getStatus();

    /**
     * @return ProviderInterface
     */
    public function getProvider();

    /**
     * @param FeatureInterface $previous
     * @return FeatureInterface The feature which now governs the overall resolved status
     */
    public function resolve(FeatureInterface $previous);
}

<?php

namespace Pheat\Feature;

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

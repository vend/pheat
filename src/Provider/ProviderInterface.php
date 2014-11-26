<?php

namespace Pheat\Provider;

use Pheat\ContextInterface;
use Pheat\Feature\FeatureInterface;

/**
 * Provider
 *
 * A feature provider is given a context by the manager. It uses it
 * to determine a list of features, and their enclosed statuses.
 *
 * This list is given back to the manager for the final merge-down resolution
 */
interface ProviderInterface
{
    /**
     * Providers must have a unique name
     *
     * @return string
     */
    public function getName();

    /**
     * @param ContextInterface $context
     * @return FeatureInterface[]
     */
    public function getFeatures(ContextInterface $context);
}

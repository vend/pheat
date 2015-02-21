<?php

namespace Pheat\Provider;

use Pheat\ContextInterface;
use Pheat\Feature\FeatureInterface;

/**
 * Additional interface implemented by providers that can write to a persistent store
 */
interface WritableProviderInterface
{
    /**
     * Set the feature to the enclosed status, under the given context
     *
     * @param ContextInterface $context
     * @param FeatureInterface $feature
     * @return void Implementers should throw an exception if persistence failed
     */
    public function setFeature(ContextInterface $context, FeatureInterface $feature);
}

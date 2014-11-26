<?php

namespace Pheat\Provider;

use Pheat\Feature\FeatureInterface;

class NullProvider implements ProviderInterface
{
    /**
     * Providers must have a unique name
     *
     * @return string
     */
    public function getName()
    {
        return 'null';
    }

    /**
     * @return FeatureInterface[]
     */
    public function getFeatures()
    {
        return [];
    }
}

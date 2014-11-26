<?php

namespace Pheat\Provider;

use Pheat\Feature\Feature;

interface ProviderInterface
{
    /**
     * Providers must have a unique name
     *
     * @return string
     */
    public function getName();

    /**
     * @return Feature[]
     */
    public function getFeatures();
}

<?php

namespace Pheat\Provider;

use Pheat\Context;

interface ProviderInterface
{
    /**
     * @param string $feature
     * @return Status
     */
    public function getStatus($feature, Context $context);
}

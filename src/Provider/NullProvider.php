<?php

namespace Pheat\Provider;

use Pheat\ContextInterface;

class NullProvider implements ProviderInterface
{
    public function getName()
    {
        return 'null';
    }

    public function getFeatures(ContextInterface $context)
    {
        return [];
    }
}

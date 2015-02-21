<?php

namespace Pheat\Provider;

use Pheat\ContextInterface;

class NullProvider implements ProviderInterface
{
    /**
     * @var null|string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFeatures(ContextInterface $context)
    {
        return [];
    }
}

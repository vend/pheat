<?php

namespace Pheat;

use Pheat\Provider\ProviderInterface;

class Manager
{
    protected $providers = [];

    protected $context;

    public function __construct(Context $context = null)
    {
        $this->setContext($context === null ? new Context() : $context);
    }


    public function addProvider(ProviderInterface $provider)
    {}

    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Resolves the status of a feature
     *
     * Returns whether a feature should be considered enabled as a boolean
     *
     * @param string              $feature The name of the feature to resolve
     * @param array<string,mixed> $options Optional set of options
     * @return bool
     */
    public function resolve($feature, array $options = [])
    {
    }
}

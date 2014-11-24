<?php

namespace Pheat\Provider;

interface WritableProviderInterface
{
    public function setStatus($feature, Context $context);
}

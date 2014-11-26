<?php

namespace Pheat\Provider;

interface WritableProviderInterface
{
    public function setStatus(Feature $feature, Status $status);
}

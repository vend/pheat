<?php

namespace Pheat\Provider;

use Pheat\Context;
use Pheat\Test\Test;

class NullProviderTest extends Test
{
    const SUT = 'Pheat\Provider\NullProvider';

    public function testReturnsNoFeatures()
    {
        $provider = $this->getSut();
        $features = $provider->getFeatures(new Context());
        $this->assertCount(0, $features);
    }
}

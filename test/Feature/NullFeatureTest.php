<?php

namespace Pheat\Feature;

class NullFeatureTest extends FeatureTest
{
    const SUT = 'Pheat\Feature\NullFeature';

    /**
     * @expectedException \Pheat\Exception\NullException
     */
    public function testResolution()
    {
        parent::testResolution();
    }
}

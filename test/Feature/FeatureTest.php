<?php

namespace Pheat\Feature;

class FeatureTest extends AbstractFeatureTest
{
    const SUT = Feature::class;

    protected function getDefaultConfiguration($status)
    {
        return ['enabled' => $status];
    }
}

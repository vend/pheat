<?php

namespace Pheat\Feature;

use Pheat\ContextInterface;
use Pheat\Exception\NullException;
use Pheat\Provider\NullProvider;
use Pheat\Provider\ProviderInterface;
use Pheat\Status;
use Symfony\Component\Validator\Constraints\Null;

/**
 * The null feature
 *
 * Used as a null object where no information is known about
 * a feature being resolved. Easier than dealing with null checking
 * everywhere.
 */
class NullFeature extends Feature
{
    public function __construct($name, $_ignored = null, ProviderInterface $provider = null)
    {
        parent::__construct($name, Status::UNKNOWN, $provider ?: new NullProvider());
    }
}

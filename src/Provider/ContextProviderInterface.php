<?php

namespace Pheat\Provider;

use Pheat\ContextInterface;

/**
 * Context injection provider interface
 *
 * Implement this interface if you want your provider to be able to add entries
 * to the context
 */
interface ContextProviderInterface extends ProviderInterface
{
    /**
     * Inject values into the context
     *
     * The provider is expected to use the ->set() interface on the context object to
     * provide information.
     *
     * @param ContextInterface $context
     * @return mixed
     */
    public function inject(ContextInterface $context);
}

<?php

namespace Pheat;

interface ContextInterface
{
    /**
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get($key, $default = null);
}

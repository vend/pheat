<?php

namespace Pheat;

interface ContextInterface
{
    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set($key, $value);

    /**
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get($key, $default = null);
}

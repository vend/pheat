<?php

namespace Pheat;

class Context
{
    // A set of key value pairs, wrapped up nicely
    public function set($key, $value) {}
    public function get($key, $default = null) {}
    public function getAll() {}
    public function setAll(array $assoc) {}
}


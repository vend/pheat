<?php

namespace Pheat;

class Status
{
    const ACTIVE   = 1;
    const INACTIVE = 0;
    const UNKNOWN  = -1;

    public $status = self::UNKNOWN;
}

<?php

namespace Pheat\Status;

class Status
{
    /**
     * "I do not know"
     *
     * This status indicates a provider knows of a feature, but doesn't
     * know anything about whether it should be active.
     */
    const UNKNOWN = null;

    /**
     * This status indicates a feature should be considered active
     */
    const ACTIVE = true;

    /**
     * This status indicates a feature should be considered inactive
     *
     * In the default merge-down, inactive trumps active: a single provider saying "don't activate this feature" wins
     * against all the other providers saying "yep, OK".
     */
    const INACTIVE = false;

    /**
     * Nice human-readable representations of the statuses
     *
     * Beware: type of array indexes in the below representation differ from the canonical due to coercion
     *  "" (string) - unknown
     *  0  (int)    - inactive
     *  1  (int)    - active
     *
     * @var array
     */
    public /* readonly */ static $messages = [
        self::UNKNOWN  => 'unknown',
        self::ACTIVE   => 'active',
        self::INACTIVE => 'inactive'
    ];
}

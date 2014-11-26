<?php

namespace Pheat\Exception;

/**
 * Locking prevents writes to stateful services, like Pheat's manager class,
 * when those changes wouldn't be handled correctly at that point in time.
 */
class LockedException extends Exception
{}

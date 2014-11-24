<?php

if (!is_dir(__DIR__ . '/../build')) {
    mkdir(__DIR__ . '/../build');
}

$loader = require __DIR__ . '/../vendor/autoload.php';

// Load the tests directory into the main namespace
$loader->addPsr4('Pheat\\', __DIR__);

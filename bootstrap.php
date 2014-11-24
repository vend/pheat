<?php

if (!is_dir('build')) {
    mkdir('build');
}

$loader = require __DIR__ . '/vendor/autoload.php';

// Load the tests directory into the main namespace
$loader->addPsr4('Pheat\\', __DIR__ . '/test');

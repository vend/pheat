<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Pheat\\', __DIR__);

$build = __DIR__ . '/../build';

$logger = new Monolog\Logger('test');
$logger->pushHandler(new Monolog\Handler\StreamHandler($build . '/test.log'));

$settings = new Pheat\Test\Settings();
$settings->setLogger($logger);
$settings->fromEnvironment();
$settings->setBuildDir($build);
$settings->checkBuildDir();
$settings->dumpConfig();

Pheat\Test\Test::setSettings($settings);

<?php

declare(strict_types=1);

use Zenith\BootstrapFactory;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

$factory = new BootstrapFactory();
$bootstrap = $factory->create(
    dirname(__DIR__) . '/routes',
    dirname(__DIR__) . '/config.php',
    dirname(__DIR__) . '/cache.php'
);
$bootstrap->run();
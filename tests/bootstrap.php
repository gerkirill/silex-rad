<?php

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('SilexRad\Tests', __DIR__);

if (!class_exists('Silex\Application')) {
    echo "You must install the dev dependencies using:\n";
    echo "    composer install --dev\n";
    exit(1);
}
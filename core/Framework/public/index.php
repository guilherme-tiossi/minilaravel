<?php

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/helpers.php';

use Core\Framework\Root\Application;
use Core\Framework\Root\Container;
use Core\Framework\Http\Kernel;

$container = new Container();
$app = new Application($container);
$kernel = new Kernel($app->container);
$kernel->handle();

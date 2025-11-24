<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../routes/api.php';
require __DIR__ . '/../bootstrap/helpers.php';

use Bootstrap\Http\Kernel;

$kernel = new Kernel();
$kernel->handle();

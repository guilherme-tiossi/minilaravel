<?php

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../helpers.php';

use Core\Framework\Http\Kernel;

$kernel = new Kernel();
$kernel->handle();

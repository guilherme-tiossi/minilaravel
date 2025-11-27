<?php

require __DIR__ . '/../../../vendor/autoload.php';
# POR ENQUANTO MANTEMOS ISSO AQUI - ESTÁ ERRADO POIS NADA DE APPLICATION DEVE CONTAMINAR
# O FRAMEWORK, MAS ASSIM QUE POSSÍVEL FAREMOS UM ROUTESERVICEPROVIDER
require __DIR__ . '/../../Application/routes/api.php';
require __DIR__ . '/../helpers.php';

use Core\Framework\Http\Kernel;

$kernel = new Kernel();
$kernel->handle();

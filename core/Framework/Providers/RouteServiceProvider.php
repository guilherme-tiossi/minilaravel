<?php

namespace Core\Framework\Providers;

use Core\Framework\Root\Container;

class RouteServiceProvider implements Provider
{
    public function register(?Container &$container = null): void
    {
        app('Application/routes/api.php');
    }
}
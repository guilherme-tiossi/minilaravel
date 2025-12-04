<?php

namespace Core\Framework\Providers;

class RouteServiceProvider implements Provider
{
    public function init(): void
    {
        app('Application/routes/api.php');
    }
}
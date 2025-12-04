<?php

namespace Core\Framework\Providers;

class RouteServiceProvider implements Provider
{
    public function init(): void
    {
        include __DIR__ . '/../../Application/routes/api.php';
        // $routes = [
        //     app('Application\routes\api.php'),
        // ];

        
    }
}
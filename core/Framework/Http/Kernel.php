<?php

namespace Core\Framework\Http;

use Core\Framework\Providers\RouteServiceProvider;
use Core\Application\Http\Exceptions\AppException;
use Core\Application\Http\Middlewares\HandleCors;
use Exception;

class Kernel
{
    public function handle(): HttpResponse
    {
        $this->registerProviders();

        $globalMiddlewares = [
            HandleCors::class
        ];

        header('Content-Type: application/json');
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        try {
            $request = new Request();
            return Router::runRoute($request, $globalMiddlewares);
        } catch (AppException $e) {
            return new HttpResponse($e->getCode(), ['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return new HttpResponse(500, [
                'message' => 'ERROR: a fatal exception has occured',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }

    private function registerProviders(): void
    {
        new RouteServiceProvider()->init();
    }
}

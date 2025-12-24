<?php

namespace Core\Framework\Http;

use Core\Framework\Providers\RouteServiceProvider;
use Core\Application\Http\Exceptions\AppException;
use Core\Application\Http\Middlewares\HandleCors;
use Core\Application\Http\Middlewares\RateLimiter;
use Core\Framework\Http\Traits\RunMiddlewares;
use Exception;

class Kernel
{
    use RunMiddlewares;

    public function handle(): HttpResponse
    {
        $this->registerProviders();

        $globalMiddlewares = [
            RateLimiter::class,
            HandleCors::class
        ];

        try {
            $request = new Request();
            $this->runMiddlewares($globalMiddlewares, $request);
            return Router::dispatch($request);
        } catch (AppException $e) {
            return new HttpResponse($e->getCode(), ['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return new HttpResponse(500, [
                'message' => 'ERROR: a fatal exception has occured',
                'error' => $e->getMessage(), // pegar configuração se é prod ou n
                'trace' => $e->getTrace() // pegar configuração se é prod ou n
            ]);
        }
    }

    private function registerProviders(): void
    {
        new RouteServiceProvider()->init();
    }
}

<?php

namespace Core\Framework\Http;

use Core\Framework\Providers\RouteServiceProvider;
use Core\Application\Http\Exceptions\AppException;
use Core\Application\Http\Middlewares\HandleCors;
use Core\Application\Http\Middlewares\RateLimiter;
use Core\Framework\Container;
use Core\Framework\Http\Traits\RunMiddlewares;
use Core\Infrastructure\Cache\CacheProvider;
use Core\Infrastructure\Cache\RateLimitProvider;
use Core\Infrastructure\Cache\RedisCacheProvider;
use Exception;

class Kernel
{
    use RunMiddlewares;

    public function handle(): HttpResponse
    {
        $this->registerProviders();

        $globalMiddlewares = [
            HandleCors::class,
            RateLimiter::class
        ];

        try {
            $request = new Request();

            $container = new Container;
            $this->configureContainer($container);

            $this->runMiddlewares($globalMiddlewares, $request, $container);

            return Router::dispatch($request, $container);
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

    // refatorar isso aqui em algo mais sofisticado
    private function configureContainer(Container &$container): Container
    {
        $container->bind(CacheProvider::class, RedisCacheProvider::class);
        $container->bind(RateLimitProvider::class, RedisCacheProvider::class);

        return $container;
    }
}

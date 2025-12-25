<?php

namespace Core\Framework\Http;
use Core\Application\Http\Exceptions\AppException;
use Core\Application\Http\Middlewares\HandleCors;
use Core\Application\Http\Middlewares\RateLimiter;
use Core\Framework\Root\Container;
use Core\Framework\Http\Traits\RunMiddlewares;
use Core\Framework\Http\ValueObjects\Request;
use Core\Framework\Http\ValueObjects\Response;
use Exception;

class Kernel
{
    use RunMiddlewares;

    public function __construct(
        private Container $container
    ) {
    }

    public function handle(): Response
    {
        $globalMiddlewares = [
            HandleCors::class,
            RateLimiter::class
        ];

        try {
            $request = new Request();
            $this->runMiddlewares($globalMiddlewares, $request, $this->container);
            return Router::dispatch($request, $this->container);
        } catch (AppException $e) {
            return new Response($e->getCode(), ['message' => $e->getMessage()]);
        } catch (Exception $e) {
            $payload = [
                'message' => 'A server-side error has occured'
            ];
            if (getenv('environment') !== 'PRODUCTION') {
                $payload['error'] = $e->getMessage();
                $payload['trace'] = $e->getTrace();
            }

            return new Response(500, $payload);
        }
    }
}

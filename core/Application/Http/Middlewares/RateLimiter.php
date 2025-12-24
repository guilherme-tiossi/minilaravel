<?php

namespace Core\Application\Http\Middlewares;

use Core\Framework\Http\HttpResponse;
use Core\Framework\Http\Middleware;
use Core\Framework\Http\Request;
use Core\Infrastructure\Cache\RedisCacheProvider;
use Redis;

class RateLimiter implements Middleware
{
    public function __construct(
        private ?Middleware $next = null
    ) {
    }

    public function run(Request $request): ?HttpResponse
    {
        // isso é um RateLimitProvider, usar DI pra ficar mais claro posteriorment
        $rateLimitProvider = new RedisCacheProvider(
            getenv('REDIS_HOST'),
            (int)getenv('REDIS_PORT'),
            getenv('REDIS_PASSWORD'),
            (int)getenv('REDIS_DB'),
            2.5
        );

        $rateLimitProvider->validateRateLimit($request->ip, 5);

        if ($this->next) {
            return $this->next->run($request);
        }

        return null;
    }
}
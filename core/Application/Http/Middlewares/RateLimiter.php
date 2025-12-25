<?php

namespace Core\Application\Http\Middlewares;

use Core\Framework\Http\HttpResponse;
use Core\Framework\Http\Request;
use Core\Infrastructure\Cache\RateLimitProvider;

class RateLimiter implements Middleware
{
    public function __construct(
        private RateLimitProvider $rateLimitProvider,
        private ?Middleware $next = null
    ) {
    }

    public function run(Request $request): ?HttpResponse
    {
        $this->rateLimitProvider->validateRateLimit($request->ip, 5);

        if ($this->next) {
            return $this->next->run($request);
        }

        return null;
    }
}
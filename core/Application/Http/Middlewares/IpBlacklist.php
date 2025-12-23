<?php

namespace Core\Application\Http\Middlewares;

use Core\Framework\Http\HttpResponse;
use Core\Framework\Http\Middleware;
use Core\Framework\Http\Request;

class IpBlacklist implements Middleware
{
    private array $bannedIps = [
        // '127.0.0.1',
        '127.0.0.2',
        '127.0.0.3'
    ];

    public function __construct(
        private ?Middleware $next = null
    ) {
    }

    public function run(Request $request): ?HttpResponse
    {
        if (in_array($request->ip, $this->bannedIps)) {
            return new HttpResponse(401, ['message' => 'Blocked IP']);
        }

        if ($this->next) {
            return $this->next->run($request);
        }

        return null;
    }
}
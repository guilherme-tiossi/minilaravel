<?php

namespace Core\Application\Http\Middlewares;

use Core\Framework\Http\ValueObjects\Response;
use Core\Framework\Http\ValueObjects\Request;

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

    public function run(Request $request): ?Response
    {
        if (in_array($request->ip, $this->bannedIps)) {
            return new Response(401, ['message' => 'Blocked IP']);
        }

        if ($this->next) {
            return $this->next->run($request);
        }

        return null;
    }
}
<?php

namespace Core\Application\Http\Middlewares;

use Core\Application\Http\Exceptions\AppException;
use Core\Framework\Http\Middleware;
use Core\Framework\Http\Request;

class IpBlacklist implements Middleware
{
    private array $bannedIps = [
        '127.0.0.2',
        '127.0.0.3'
    ];

    public function run(Request $request): void
    {
        if (in_array($request->ip, $this->bannedIps)) {
            throw new AppException(401, 'Blocked IP');
        }
    }
}
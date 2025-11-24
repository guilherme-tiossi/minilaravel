<?php

namespace App\Http\Middlewares;

use App\Http\Exceptions\AppException;
use Bootstrap\Http\Middleware;
use Bootstrap\Http\Request;

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
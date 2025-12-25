<?php

namespace Core\Application\Http\Middlewares;

use Core\Framework\Http\ValueObjects\Response;
use Core\Framework\Http\ValueObjects\Request;

interface Middleware
{
    public function run(Request $request): ?Response;
}
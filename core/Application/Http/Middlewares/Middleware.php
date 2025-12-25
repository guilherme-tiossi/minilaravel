<?php

namespace Core\Application\Http\Middlewares;

use Core\Framework\Http\HttpResponse;
use Core\Framework\Http\Request;

interface Middleware
{
    public function run(Request $request): ?HttpResponse;
}
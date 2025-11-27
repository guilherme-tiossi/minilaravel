<?php

namespace Core\Framework\Http;

interface Middleware
{
    public function run(Request $request);
}
<?php

namespace Bootstrap\Http;

interface Middleware
{
    public function run(Request $request);
}
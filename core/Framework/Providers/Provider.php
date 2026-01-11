<?php

namespace Core\Framework\Providers;

use Core\Framework\Root\Container;

interface Provider
{
    public function register(?Container &$container = null): void;
}
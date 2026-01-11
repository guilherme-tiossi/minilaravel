<?php

namespace Core\Framework\Root;

use Core\Framework\Providers\RouteServiceProvider;
use Core\Infrastructure\Cache\CacheProvider;
use Core\Infrastructure\Cache\RateLimitProvider;
use Core\Infrastructure\Cache\RedisCacheProvider;
use Core\Infrastructure\Database\DatabaseProvider;
use Core\Infrastructure\Database\MysqlDatabaseProvider;

class Application
{
    public function __construct(
        public Container $container
    ) {
        $this->register();
    }

    public function register(): void
    {
        $this->container->bind(CacheProvider::class, RedisCacheProvider::class);
        $this->container->bind(RateLimitProvider::class, RedisCacheProvider::class);
        $this->container->bind(DatabaseProvider::class, MysqlDatabaseProvider::class);

        $this->registerProviders();
    }

    private function registerProviders(): void
    {
        new RouteServiceProvider()->init();
    }
}
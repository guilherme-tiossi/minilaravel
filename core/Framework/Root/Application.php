<?php

namespace Core\Framework\Root;

use Core\Framework\Providers\EventServiceProvider;
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
        Container::setInstance($container);
        $this->register();
    }

    public function register(): void
    {
        $this->container->bind(CacheProvider::class, RedisCacheProvider::class);
        $this->container->bind(RateLimitProvider::class, RedisCacheProvider::class);
        $this->container->bind(DatabaseProvider::class, MysqlDatabaseProvider::class);

        new RouteServiceProvider()->register($this->container);
        new EventServiceProvider()->register($this->container);
    }
}
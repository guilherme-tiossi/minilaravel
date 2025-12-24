<?php
namespace Core\Infrastructure\Cache;

interface CacheProvider
{

    public function get(string $key): mixed;
    public function set(string $key, mixed $value, ?int $ttl = null): bool;
    public function setUntil(string $key, mixed $value, int $timestamp): bool;
    public function expire(string $key, int $seconds): bool;
    public function persist(string $key): bool;
    public function ttl(string $key): int;
    public function delete(string $key): bool;
    public function exists(string $key): bool;
    public function increment(string $key, int $value = 1): int;
    public function decrement(string $key, int $value = 1): int;
}
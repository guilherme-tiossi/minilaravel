<?php
namespace Core\Infrastructure\Cache;

use Core\Shared\Exceptions\AppException;
use Redis;
use RedisException;

class RedisCacheProvider implements CacheProvider, RateLimitProvider
{
    private Redis $redis;

    public function __construct() 
    {
        $this->redis = new Redis();
        
        try {
            $host = getenv('REDIS_HOST');
            $port= getenv('REDIS_PORT');
            $password = getenv('REDIS_PASSWORD');
            $database = getenv('REDIS_DB');
            $timeout = 0.0;

            $this->redis->pconnect($host, $port, $timeout, 'mini_laravel');
            $this->redis->auth($password);
            $this->redis->select($database); 
        } catch (RedisException $e) {
            throw new AppException(500, 'Failed cache provider connection: ' . $e->getMessage());
        }
    }

    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);
        return $value === false ? null : $value;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        if ($ttl !== null && $ttl > 0) {
            return $this->redis->setex($key, $ttl, $value);
        }
        return $this->redis->set($key, $value);
    }

    public function setUntil(string $key, mixed $value, int $timestamp): bool
    {
        $result = $this->redis->set($key, $value);
        if ($result) {
            return $this->redis->expireAt($key, $timestamp);
        }
        return false;
    }

    public function expire(string $key, int $seconds): bool
    {
        return $this->redis->expire($key, $seconds);
    }

    public function persist(string $key): bool
    {
        return $this->redis->persist($key);
    }

    public function ttl(string $key): int
    {
        return $this->redis->ttl($key);
    }

    public function delete(string $key): bool
    {
        return $this->redis->del($key) > 0;
    }

    public function exists(string $key): bool
    {
        return $this->redis->exists($key) > 0;
    }

    public function increment(string $key, int $value = 1): int
    {
        return $this->redis->incrBy($key, $value);
    }

    public function decrement(string $key, int $value = 1): int
    {
        return $this->redis->decrBy($key, $value);
    }

    public function validateRateLimit(string $ip, int $allowedPerMinute): void 
    {
        $script = <<<LUA
            local current = redis.call("INCR", KEYS[1])
            if current == 1 then
              redis.call("EXPIRE", KEYS[1], ARGV[2])
            end
            if current > tonumber(ARGV[1]) then
              return {0, current}
            end
            return {1, current}
            LUA;

        $key = 'rate:ip:' . $ip;

        [$allowed, $count] = $this->redis->eval(
            $script,
            [$key, $allowedPerMinute, 60],
            1
        );

        if (!$allowed) {
            throw new AppException(429, 'Too many requests');
        }
    }
}

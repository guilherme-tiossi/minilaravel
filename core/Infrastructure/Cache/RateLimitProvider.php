<?php
namespace Core\Infrastructure\Cache;

interface RateLimitProvider
{

    public function validateRateLimit(string $ip, int $allowedPerMinute): void;
}
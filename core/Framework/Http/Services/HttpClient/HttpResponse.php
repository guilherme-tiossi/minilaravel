<?php

namespace Core\Framework\Http\Services\HttpClient;

class HttpResponse
{
    public function __construct(
        public int $status,
        public string $body,
        public array $headers
    ) {}

    public function json(): array
    {
        return json_decode($this->body, true) ?? [];
    }
}

<?php

namespace Core\Framework\Http\Services\HttpClient;

use Core\Shared\Exceptions\AppException;

class HttpClient
{
    protected array $defaultHeaders = [];
    protected int $timeout = 30;

    public function withHeaders(array $headers): static
    {
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);
        return $this;
    }

    public function timeout(int $seconds): static
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function get(string $url, array $query = []): HttpResponse
    {
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $this->request('GET', $url);
    }

    public function post(string $url, array $data = []): HttpResponse
    {
        return $this->request('POST', $url, $data);
    }

    public function put(string $url, array $data = []): HttpResponse
    {
        return $this->request('PUT', $url, $data);
    }

    public function delete(string $url, array $data = []): HttpResponse
    {
        return $this->request('DELETE', $url, $data);
    }

    protected function request(string $method, string $url, array $data = []): HttpResponse
    {
        $ch = curl_init($url);

        $headers = [];
        foreach ($this->defaultHeaders as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }

        $responseHeaders = [];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HEADERFUNCTION => function ($curl, $header) use (&$responseHeaders) {
                $len = strlen($header);
                $parts = explode(':', $header, 2);
                if (count($parts) === 2) {
                    $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return $len;
            },
        ]);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }

        $body = curl_exec($ch);

        if ($body === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new AppException('HTTP request failed: ' . $error, 422);
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new HttpResponse($status, $body, $responseHeaders);
    }
}

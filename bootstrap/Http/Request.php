<?php

namespace Bootstrap\Http;

use Bootstrap\Http\Enums\HttpMethod;

class Request
{
    public HttpMethod $method;
    public string $uri;
    public string $protocol;
    public array $query;
    public array $headers;
    public string $ip;
    public array $cookies;
    public string $raw;
    public array $body;
    public array $files;

    public function __construct()
    {
        $this->method = HttpMethod::from($_SERVER['REQUEST_METHOD']);
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->protocol = $_SERVER['SERVER_PROTOCOL'];
        $this->query = $_GET;
        $this->headers = getallheaders();
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->cookies = $_COOKIE;
        $this->raw = file_get_contents('php://input');
        $this->body = json_decode($this->raw) ?? $_POST;
        $this->files = $_FILES; 
    }
}

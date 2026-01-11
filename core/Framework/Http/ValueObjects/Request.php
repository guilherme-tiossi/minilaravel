<?php

namespace Core\Framework\Http\ValueObjects;

use Core\Application\Http\Exceptions\ValidationException;
use Core\Framework\Http\Enums\HttpMethod;

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
    public ?string $origin = null;

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
        $this->body = (array) json_decode($this->raw) ?? $_POST;
        $this->files = $_FILES;
        $this->origin = $_SERVER['HTTP_ORIGIN'] ?? null;
    }

    public function validate(array $data)
    {
        foreach ($data as $fieldName => $validations) {
            $existsInRequest = array_key_exists($fieldName, $this->body);
            $shouldExistInRequest = ($validations['required'] ?? false) === true;

            if (!$existsInRequest && $shouldExistInRequest) {
                throw new ValidationException(
                    422,
                    'field ' . $fieldName . ' required in request!'
                );
            }

            if (!$existsInRequest && !$shouldExistInRequest) {
                continue;
            }

            $value = $this->body[$fieldName];

            if (isset($validations['type'])) {
                $type = $validations['type'];

                $isValidType = match ($type) {
                    'string'  => is_string($value),
                    'int', 'integer' => is_int($value),
                    'float'   => is_float($value),
                    'numeric' => is_numeric($value),
                    'bool', 'boolean' => is_bool($value),
                    'array'   => is_array($value),
                    default   => false,
                };

                if (!$isValidType) {
                    throw new ValidationException(
                        422,
                        'Field ' . $fieldName . ' must be of type ' . $type
                    );
                }
            }

            if (isset($validations['min']) && is_string($value) && mb_strlen($value) < $validations['min']) {
                throw new ValidationException(
                    422,
                    'field ' . $fieldName . ' must have at least ' . $validations['min'] . ' characters'
                );
            }

            if (isset($validations['max']) && is_string($value) && mb_strlen($value) > $validations['max']) {
                throw new ValidationException(
                    422,
                    'field ' . $fieldName . ' must have a maximum ' . $validations['max'] . ' characters'
                );
            }
        }
    }
}

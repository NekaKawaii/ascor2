<?php

declare(strict_types=1);

namespace App\Tests\_tools;

/**
 * Билдер запроса к JSON HTTP API
 *
 * @psalm-readonly-allow-private-mutation
 */
final class JsonApiRequest
{
    /**
     * Метод (HTTP VERB)
     */
    public string $method;

    /**
     * URI запроса
     */
    public string $uri;

    /**
     * Заголовки запроса
     *
     * @var array<string, string>
     */
    public array $headers = [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json'
    ];

    public array $jsonBody = [];

    /**
     * Параметры запроса.
     *
     * @var array<string, mixed>
     */
    public array $parameters = [];

    /**
     * Ожидаемый статус ответа. По умолчанию 200 OK.
     */
    public int $expectedStatusCode = 200;

    /**
     * POST запрос на API
     */
    public static function post(string $uri): self
    {
        return new self('POST', $uri);
    }

    /**
     * GET запрос на API
     */
    public static function get(string $uri): self
    {
        return new self('GET', $uri);
    }

    /**
     * PATCH запрос на API
     */
    public static function patch(string $uri): self
    {
        return new self('PATCH', $uri);
    }

    /**
     * PUT запрос на API
     */
    public static function put(string $uri): self
    {
        return new self('PUT', $uri);
    }

    /**
     * Запрос к HTTP с методом и URI.
     */
    final public function __construct(string $method, string $uri)
    {
        $this->method = $method;
        $this->uri = $uri;
    }

    /**
     * Тело запроса к JSON HTTP API.
     */
    public function jsonBody(array $body): self
    {
        $this->jsonBody = $body;

        return $this;
    }

    /**
     * Параметры запроса к JSON HTTP API.
     *
     * @param array<string, mixed> $parameters
     */
    public function parameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Ожидаемый статус ответа на запрос.
     */
    public function expectingStatusCode(int $expectedStatusCode): self
    {
        $this->expectedStatusCode = $expectedStatusCode;

        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }
}
